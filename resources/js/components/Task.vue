<!--タスク単体を表示するデザインコンポーネント-->
<!--編集、状態の変化で上部に送出-->
<!--4 ステッカー（新規作成にNew!、締切間近にDANGER!など-->
<!--5 ログ表示-->
<template>
    <div class="container">
        <!--通知-->
        <notice ref="notice" />
        
        <!--タスク削除確認モーダル-->
        <modal ref="deleteModal" v-model="deleteModal">
            <b>タスク「{{task.name}}」を削除します。</b>
            <b>この処理は取り消しできません。</b>
            <b>よろしいですか？</b>
            <p>　</p>
            <div class="buttons">
                <button class="btn btn-danger d-block" v-on:click="deleteTask()">タスクを削除</button>
                <button class="btn btn-secondary d-block" v-on:click="cancelDialog()">キャンセル</button>
            </div>
        </modal>
        
        <!--編集用モーダル-->
        <modal ref="editModal" v-model="editModal">
            <versatile-form v-model="editedTask" v-bind:foreignKeys="foreignKeys" table="tasks" v-bind:idProp="taskId"/>
        </modal>
        
        <!--タグ編集用モーダル-->
        <modal ref="editTagModal" v-model="editTagModal">
            <p>タグを編集します。</p>
            <tag-cloud v-model="selectedTags" v-bind:options="tags" multiple/>
        </modal>
        
        <!--アイテムリストのアイテム削除確認モーダル-->
        <modal ref="deleteItemModal" v-model="deleteItemModal">
            <b>アイテム「{{targetItemName}}」を削除します。</b>
            <b>この処理は取り消しできません。</b>
            <b>よろしいですか？</b>
            <p>　</p>
            <div class="buttons">
                <button class="btn btn-danger d-block" v-on:click="deleteItem()">アイテムを削除</button>
                <button class="btn btn-secondary d-block" v-on:click="cancelDialog()">キャンセル</button>
            </div>
        </modal>
        
        
        <!--メインコンテンツ-->
        <div v-bind:class="wrapper_class" v-bind:style="inactivateTask">
            <!--マスク部-->
            <div v-bind:class="mask_class" v-on:click="openDetail()"></div>
            <div class="state-icon" v-show="checkbox || not_started">
                <i v-show="checkbox" class="far fa-2x fa-check-circle"></i>
                <i v-show="not_started" class="fas fa-2x fa-exclamation-circle"></i>
            </div>
            <!--本体表示部分-->
            <!--ラベ���部分-->
            <div class="task-label">
                <div class="headline">
                    <div class="headline-icons">
                        <!--炎上マーク（締切24時間以内のタスク）-->
                        <i class="fas fa-fire" v-bind:style="fire"></i>
                    </div>
                    <input ref="checkbox" class="checkbox" type="checkbox" v-on:change="checkTask(task.id)">
                    <div>
                        <div v-if="task.project && task.project.id != 1" class="project-label">{{task.project.name}}</div>
                        <span class="task-headline">{{task.name}}</span>
                    </div>
                </div>
                <div>
                    <!--締切-->
                    <i class="far fa-clock"></i>
                    <span class="dead-line">{{task.dead_line}}</span>
                    
                    <!--タグ編集ボタン-->
                    <i class="fas fa-tag task-icon" v-on:click="showEditTagDialog()"></i>
                    <!--編集ボタン-->
                    <i class="far fa-edit task-icon" v-on:click="showEditTaskDialog()"></i>
                    <!--削除ボタン-->
                    <i class="fas fa-trash task-icon" v-on:click="showDeleteTaskDialog()"></i>
                </div>
            </div>
            <!--詳細部分（クリックで開閉）-->
            <div class="detail">
                <!--各種パラメーター-->
                <span class="label">優先度</span>
                <star-range v-model="task.priority" />
                <span class="label">難易度</span>
                <star-range v-model="task.difficulty" />
                <!--タグ-->
                <div class="tags">
                    <div class="tag" v-for="(tag,index) in task.tags">
                        <i class="fas fa-tag" v-bind:style="{color:tag.color}"></i>
                        <span>{{tag.name}}</span>
                    </div>
                </div>
                <!--概要-->
                <p>{{task.overview}}</p>
                <!--子アイテム-->
                <div class="items">
                    <p v-for="(item,itemIndex) in task.items" v-bind:class="setItemClass(item.is_checked)" v-bind:style="inactivateItem[item.id]">
                            <span v-show="!editItemMode[itemIndex]">
                                <input type="checkbox" class="checkbox" v-on:change="checkItem(item.id)" v-bind:checked="item.is_checked" v-bind:disabled="setItemDisabled(item.is_checked)"> {{item.name}}
                            </span>
                            <!--編集用セクション-->
                            <span v-show="editItemMode[itemIndex]">
                                <input class="editable" type="text" v-model="task.items[itemIndex].name" v-on:keydown="updateItem(itemIndex)">
                            </span> 
                            <!--編集ボタン-->
                            <i class="far fa-edit task-icon" v-on:click="toggleEditMode(itemIndex)"></i>
                            <!--削除ボタン-->
                            <i class="fas fa-trash task-icon" v-on:click="showDeleteItemDialog(item.id,item.name)"></i>
                    </p>
                    <!--アイテムの追加処理セクション-->
                    <div class="editable">
                        <text-spliter v-model="items" />
                        <button class="btn btn-outline-primary mx-auto d-block" v-on:click="addItems">アイテムを追加</button>
                    </div>
                </div>
                <!--ログ-->
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                task:{},
                wrapper_class:'task-wrapper',
                mask_class:'mask',
                mask:false,
                checkbox:false,
                not_started:false,
                detail:false,
                deleteModal:false,
                editModal:false,
                deleteItemModal:false,
                editedTask:'',
                inactivateTask:'',
                inactivateItem:[],
                targetItemId:'',
                targetItemName:'',
                editItemMode:[],
                items:[],
                editTagModal:false,
                isEditedTags:false,
                tags:[],
                selectedTags:[],
                foreignKeys:[
                    {project_id:
                        {
                            table:'projects',
                            columns:['name','dead_line'],
                            comment:'所属するプロジェクトを選択してください。'
                        }
                    },
                    {state_id:
                        {
                            table:'states',
                            columns:['name'],
                            comment:'状態を選択してください'
                        }
                    }
                ]
            }  
        },
        props:{
            taskId: {
                type:[String,Number],
                required:true,
            }
        },
        watch:{
            taskId:async function() {
                await this.fetchTask()
                this.updateData()
            },
            task:{
                handler:async function(){
                    this.updateData()
                },
                deep:true
            },
            editedTask:async function(){
                await this.fetchTask()
                this.updateData()
            },
            selectedTags:async function(){
                if(!this.isEditedTags){return }
                let tagsObject = {
                    task_id:this.task.id,
                    tag_ids:this.selectedTags
                }
                try{
                    await axios.put('/api/tag_task/',tagsObject)
                    this.$refs.notice.showNotice('タグを変更しました')
                }catch(error){
                    this.$refs.notice.showNotice('タグの変更に失敗しました')
                    console.log(error)
                }
            },
            // タグ編集後に閉じたときはフラグをfalseにしてタスクを再取得
            editTagModal:function(newVal,oldVal){
                if(newVal == false){
                    this.isEditedTags = false
                    this.fetchTask()
                }
            }
        },
        created:async function(){
            if(this.taskId){
                await this.fetchTask()
                this.updateData()
                this.fetchTags()
            }
        },
        mounted:async function(){
            if(this.taskId && !this.task){
                await this.fetchTask()
                this.updateData()
                this.fetchTags()
            }
        },
        computed:{
            // 炎上中マーク
            fire:function(){
                let currentDatetime = new Date()
                let deadlineDatetime = new Date(this.task.dead_line)
                let remainingTime = deadlineDatetime - currentDatetime
                if(0 > remainingTime){ //締切を過ぎているか残り時間が12時間以内の場合に表示
                    return {fontSize:'150%',color:'red',opacity:'1.0'}
                }else if(remainingTime < 43200000){
                    return {fontSize:'150%',color:'orange',opacity:'1.0'}
                }else{
                    return {fontSize:'150%',color:'grey',opacity:'0.3'}
                }
            },
        },
        methods: {
            fetchTask:async function(){
                let result = await axios.get('/api/tasks/' + this.taskId)
                this.task = result.data
                for(let index in this.task.items){
                    this.editItemMode.push(false)
                }
                //設定済みのタグの取得
                for(let tag of this.task.tags){
                    this.selectedTags.push(tag.id)
                }
            },
            fetchTags:async function(){
                let result = await axios.get('/api/tags/')
                for(let tag of result.data){
                    this.tags.push({label:tag.name,value:tag.id})
                }
            },
            openDetail: function(){
                this.detail = !this.detail
                this.wrapper_class = this.detail ? 'task-wrapper detail-active' : 'task-wrapper'
            },
            checkTask:async function(taskId){
                let check = event
                if(event.target.checked == true){
                    let modifyData = {
                        state_id:3
                    }
                    let result = await axios.put('/api/tasks/' + taskId, modifyData)
                    if(result.data){
                        //taskを再取得して設定
                        let taskResult = await axios.get('api/tasks/' + taskId)
                        this.task = taskResult.data
                        this.$emit('input',taskResult.data)
                    }
                }
            },
            setItemClass:function(is_checked){
                return is_checked == true ? 'item-completed' : ''
            },
            setItemDisabled:function(is_checked){
                return is_checked || false
            },
            checkItem:async function(itemId){
                let el = event
                let modifyData = {is_checked:true}
                let result = await axios.put('/api/items/' + itemId,modifyData)
                if(result.data){
                    el.target.disabled = true
                    el.target.parentElement.className = 'item-completed'
                }
            },
            updateData:function(){
                // 各種パラメータをリセット
                this.mask_class = 'mask'
                this.checkbox = false
                this.not_started = false
                
                //チェックボックスの要素を取得
                let check = this.$refs.checkbox
                check.checked = false
                check.disabled = false
                
                let current_datetime = new Date()
                let task_datetime = new Date(this.task.start_date)
                if(this.task.state_id == 3){
                    this.mask_class = 'mask mask-active'
                    this.checkbox = true
                    check.checked = 'checked'
                    check.disabled = true
                }else if(current_datetime < task_datetime){
                    this.mask_class = 'mask mask-active'
                    this.not_started = true
                    check.disabled = true
                }
            },
            showDeleteTaskDialog:function(){
                this.$refs.deleteModal.openModal()
            },
            deleteTask:async function(){
                // API経由で削除
                // 返り値はboolean
                let result = await axios.delete('/api/tasks/' + this.task.id)
                if(result.data){
                    // 削除が成功した場合
                    // noticeで通知
                    this.$refs.notice.showNotice('タスクを削除しました')
                    // 通知が終わった後に���らを削除（不可視化）
                    this.inactivateTask = {display:'none'}
                }else{
                    // 削除が失敗した場合
                    // noticeで通知
                    this.$refs.notice.showNotice('タスクの削除に失敗しました')
                }
                this.$refs.deleteModal.closeModal()
            },
            showEditTaskDialog:async function(){
                this.$refs.editModal.openModal()
            },
            showDeleteItemDialog:function(id,name){
                this.targetItemId = id
                this.targetItemName = name
                this.$refs.deleteItemModal.openModal()
            },
            deleteItem:async function(){
                // API経由で削除
                // 返り値はboolean
                let result = await axios.delete('/api/items/' + this.targetItemId)
                if(result.data){
                    // 削除が成功した場合
                    // noticeで通知
                    this.$refs.notice.showNotice('アイテムを削除しました')
                    // 通知が終わった後に自らを削除（不可視化）
                    this.inactivateItem[this.targetItemId] = {display:'none'}
                }else{
                    // ��除が失敗した場合
                    // noticeで通知
                    this.$refs.notice.showNotice('アイテムの削除に失敗しました')
                }
                this.$refs.deleteItemModal.closeModal()
            },
            cancelDialog:function(){
                this.$refs.deleteModal.closeModal()
                this.$refs.editModal.closeModal()
                this.$refs.deleteItemModal.closeModal()
            },
            toggleEditMode:function(itemIndex){
                this.editItemMode.splice(itemIndex,1,!this.editItemMode[itemIndex])
            },
            updateItem:async function(itemIndex){
                if(event.keyCode == 13){ //変換終了時のenterではなく、かつenterキーの場合
                    try{
                        await axios.put('/api/items/' + this.task.items[itemIndex].id,this.task.items[itemIndex])
                        this.$refs.notice.showNotice('アイテムを変更しました')
                        this.fetchTask()
                    }catch(error){
                        this.$refs.notice.showNotice('アイテムの変更に失敗しました')
                        console.log(error)
                    }
                }
            },
            addItems:async function(){
                for(let item of this.items){
                    let postItem = {
                        task_id:this.task.id,
                        name:item,
                        is_checked:false
                    }
                    try{
                        await axios.post('/api/items',postItem)
                        this.items = []
                        this.$refs.notice.showNotice('タスクにアイテムを追加しました')
                        this.fetchTask()
                    }catch(error){
                        this.$refs.notice.showNotice('アイテムの追加に失敗しました')
                        console.log(error)
                    }
                }
            },
            showEditTagDialog:function(){
                this.$refs.editTagModal.openModal()
                this.isEditedTags = true
            }
        }
    }
</script>
<style scoped>
    .container {
        position:relative;
        width:100%;
    }
    .task-wrapper {
        position:relative;
        width:100%;
        max-height:3.0em;
        overflow:hidden;
        border:1px solid black;
        /*border-radius:0.2em;*/
        transition:all 1.0s ease;
    }
    .task-wrapper:hover {
        background:gainsboro;
    }
    .headline {
        display:flex;
        justify-content:flex-start;
        align-items:center;
    }
    .headline input {
        margin:0 1em;
    }
    .task-label {
        height:3.0em;
        padding:0.5em;
        display:flex;
        align-items:center;
        justify-content:space-between;
    }
    .project-label {
        font-size:40%;
    }
    .tags {
        display:flex;
        justify-content:flex-start;
        margin:0.5em 0;
    }
    .tag {
        border:1px solid grey;
        border-radius:0.3em;
        padding:0.3em;
        margin:0 0.3em;
        background:white;
        font-size:75%;
    }
    .checkbox {
        position:relative;
        z-index:3;
    }
    .task-headline {
        font-weight:bold;
    }
    .dead-line {
        margin-right:1em;
        color:grey;
        font-size:75%;
    }
    .task-icon {
        cursor:pointer;
        margin-left:0.5em;
        position:relative;
        z-index:3;
        transition:all 0.3s;
    }
    .task-icon:hover {
        color:salmon;
    }
    .label {
        font-size:50%;
        border-radius:0.2em;
        background-color:gainsboro;
        padding:0.1em;
        margin-left:1em;
    }
    .detail {
        width:100%;
        padding:2em;
        /*background-color:gainsboro;*/
        
    }
    .detail-active {
        max-height:500px;
        transition:all 1.0s ease;
    }
    .editable {
        position:relative;
        z-index:3;
    }
    
    .mask {
        width:120%;
        height:120%;
        position:absolute;
        z-index:2;
        cursor:pointer;
        background-color:grey;
        opacity:0;
    }
    .mask-active {
        opacity:0.5;
        animation:mask 0.3s ease 0s 1;
    }
    @keyframes mask {
        0% {
            width:0%;
        }
        100% {
            width:100%; 
        }
    }
    .state-icon {
        color:white;
        position:absolute;
        top:0.5em;
        left:calc(50% - 2em);
        z-index:3;
        opacity:1.0;
        transform-origin:center;
        animation:icon-before 1s linear 0s 1,icon 0.5s linear 0.5s 1;
    }
    @keyframes icon-before {
        0% {
            opacity:0;
        }
        100% {
            opacity:0;
        }
    }
    @keyframes icon {
        0% {
            transform:rotate(0deg);
            opacity:0;
        }
        100% {
            transform:rotate(360deg);
            opacity:1.0;
        }
    }
    .item-completed {
        text-decoration:line-through;
    }
</style>
