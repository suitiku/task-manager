<!--タスク単体を表示するデザインコンポーネント-->
<!--編集、状態の変化で上部に送出-->
<!--4 ステッカー（新規作成にNew!、締切間近にDANGER!など-->
<template>
    <div v-if="task" class="container">
        <!--通知-->
        <notice ref="notice" />
        
         <!--ツールチップ-->
        <tool-tip ref="toolTip">
            <div class="tool-tip-content">
                {{toolTipContent}}
            </div>
        </tool-tip>
        
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
        
        <!--状態編集用モーダル-->
        <modal ref="editStatusModal" v-model="editStatusModal">
            <div class="edit-status-modal">
                <p>状態を変更します</p>
                <list-box v-model="selectedStatus.id" table="states" v-bind:columns="['name']" />
                <input v-model="selectedStatus.state_detail" type="text" placeholder="コメント（null可）">
                <button class="btn btn-primary d-block mx-auto" v-on:click="changeStatus()">状態を変更</button>
            </div>
        </modal>
        
        <!--タグ編集用モーダル-->
        <modal ref="editTagModal" v-model="editTagModal">
            <p>タグの付替えを行います</p>
            <tag-list v-bind:taskId="task.id" />
        </modal>
        
        <!--編集用モーダル-->
        <modal ref="editModal" v-model="editModal">
            <versatile-form v-model="editedTask" table="tasks">
                <input v-model="editedTask.name" type="text" placeholder="タスク名">
                <textarea v-model="editedTask.overview" placeholder="概要" />
                <div class="inline">
                    <span>優先度</span>
                    <star-range v-model="editedTask.priority" />
                </div>
                <div class="inline">
                    <span>難易度</span>
                    <star-range v-model="editedTask.difficulty" />
                </div>
                <span>開始日</span>
                <date-picker v-model="editedTask.start_date" />
                <span>締切</span>
                <date-picker v-model="editedTask.dead_line" />
                <span>プロジェクトを選択してください</span>
                <list-box v-model="editedTask.project_id" table="projects" />
            </versatile-form>
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
            <div v-bind:class="maskClass" v-on:click="openDetail()" v-on:mouseover="showToolTip()" v-on:mouseout="hideToolTip()"></div>
            <!--<div class="state-icon" v-show="checkbox || notActive">-->
            <div class="state-icon" v-show="checkbox || notActive">
                <div>
                    <!--完了マーク-->
                    <i v-show="checkbox" class="far fa-2x fa-check-circle"></i>
                    <!--エクスクラメーション-->
                    <i v-show="notActive" class="fas fa-2x fa-exclamation-circle"></i>
                </div>
                <!--状態詳細-->
                <span>{{stateDetail}}</span>
            </div>
            <!--本体表示部分-->
            <!--ラベル部分-->
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
                    
                    <!--状態編集ボタン-->
                    <i class="far fa-check-square task-icon" v-on:click="showEditStatusDialog()"></i>
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
                        <button class="btn btn-outline-primary mx-auto d-block" v-on:click="addItems()">アイテムを追加</button>
                    </div>
                </div>
                <!--ログ-->
                <task-log v-bind:task="task" class="editable" />
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
                maskClass:'mask',
                mask:false,
                checkbox:false,
                notActive:false, //実行状態ではない場合のフラグ
                stateDetail:'', //状態詳細テキスト（実行可能状態ではない場合に表示）
                toolTipContent:'',
                detail:false,
                deleteModal:false,
                editModal:false,
                deleteItemModal:false,
                editedTask:{},
                inactivateTask:'',
                inactivateItem:[],
                targetItemId:'',
                targetItemName:'',
                editItemMode:[],
                items:[],
                editTagModal:false,
                editStatusModal:false,
                selectedStatus:{
                    id:'',
                    state_detail:''
                },
            }  
        },
        props:{
            value: {
                type:[String,Number,Object],
                required:true,
            }
        },
        watch:{
            value:{
                handler:async function() {
                    this.setTask()
                    this.updateData()
                },
                deep:true,
            },
            //編集用モーダルを閉じたときにアップデート
            editModal:async function(newVal,oldVal){
                if(newVal == false){
                    await this.fetchTask()
                    this.updateData()
                }
            },
            // タグ編集後に閉じたときはフラグをfalseにしてタスクを再取得
            editTagModal:async function(newVal,oldVal){
                if(newVal == false){
                    this.isEditedTags = false
                    await this.fetchTask()
                }
            }
        },
        created:async function(){
        },
        mounted:async function(){
            this.setTask()
            this.updateData()
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
                let result = await axios.get('/api/tasks/' + this.task.id)
                this.task = result.data
                this.$emit('input',this.task)
            },
            setTask:async function(){
                //表示用データ作成
                this.task = JSON.parse(JSON.stringify(this.value))
                //編集モーダル用のデータ作成
                this.editedTask = {
                    id:this.task.id,
                    user_id:this.task.user_id,
                    project_id:this.task.project_id,
                    name:this.task.name,
                    overview:this.task.overview,
                    priority:this.task.priority,
                    difficulty:this.task.difficulty,
                    start_date:this.task.start_date,
                    dead_line:this.task.dead_line,
                    is_template:this.task.is_template,
                }
                //子アイテムの編集モード管理配列
                this.editItemMode = []
                for(let index in this.task.items){
                    this.editItemMode.push(false)
                }
            },
            openDetail: function(){
                this.detail = !this.detail
                this.wrapper_class = this.detail ? 'task-wrapper detail-active' : 'task-wrapper'
            },
            checkTask:async function(){
                let check = event
                if(event.target.checked == true){
                    let postObject = {
                        task_id:this.task.id,
                        state_id:2,
                    }
                    
                    try{
                        await axios.post('/api/state_task',postObject)
                        await this.fetchTask()
                        this.updateData()
                        this.$emit('input',this.task)
                    }catch(error){
                        this.$refs.notice.showNotice('タスクの状態更新に失敗しました')
                        console.log(error)
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
                if(!this.task.id){return }
                // 各種パラメータをリセット
                this.maskClass = 'mask'
                this.checkbox = false
                this.notActive = false
                this.stateDetail = ''
                
                //チェックボックスの要素を取得
                let check = this.$refs.checkbox
                check.checked = false
                check.disabled = false
                
                let current_datetime = new Date()
                let task_datetime = new Date(this.task.start_date)
                
                //statesの最後の状態を取得
                let states = this.sortStatus()
                
                if(states[states.length -1].id == 2){ //完了タスク
                    this.maskClass = 'mask mask-active'
                    this.checkbox = true
                    check.checked = 'checked'
                    check.disabled = true
                }else if(current_datetime < task_datetime){ //開始前タスク
                    this.maskClass = 'mask mask-active'
                    this.notActive = true
                    check.disabled = true
                    this.stateDetail = '開始前タスクです'
                }else if(states[states.length -1].id != 1){ //実行状態でも完了でもないタスク
                    this.maskClass = 'mask mask-active'
                    this.notActive = true
                    check.disabled = true
                    this.stateDetail = states[states.length -1].pivot.state_detail
                }
            },
            showDeleteTaskDialog:function(){
                this.$refs.deleteModal.openModal()
            },
            deleteTask:async function(){
                try{
                    // API経由で削除
                    // 返り値はboolean
                    let result = await axios.delete('/api/tasks/' + this.task.id)
                    // 削除が成功した場合
                    // noticeで通知
                    this.$refs.notice.showNotice('タスクを削除しました')
                    // 通知が終わった後に自らを削除（不可視化）
                    this.task = {}
                    this.$emit('input','')
                }catch(error){
                    // 削除が失敗した場合
                    // noticeで通知
                    this.$refs.notice.showNotice('タスクの削除に失敗しました')
                    console.log(error)
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
                try{
                    // API経由で削除
                    // 返り値はboolean
                    let result = await axios.delete('/api/items/' + this.targetItemId)
                    // 削除が成功した場合
                    // noticeで通知
                    this.$refs.notice.showNotice('アイテムを削除しました')
                    // 通知が終わった後に自らを削除（不可視化）
                    this.inactivateItem[this.targetItemId] = {display:'none'}
                }catch(error){
                    // 削除失敗した場合
                    // noticeで通知
                    this.$refs.notice.showNotice('アイテムの削除に失敗しました')
                    console.log(error)
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
            },
            showToolTip:function(){
                if(!this.toolTipContent){return }
                this.$refs.toolTip.showToolTip()
            },
            hideToolTip:function(){
                let vue = this
                let timer = window.setTimeout(function(){
                    vue.$refs.toolTip.hideToolTip()
                },500)
            },
            showEditStatusDialog:function(){
                this.$refs.editStatusModal.openModal()
            },
            sortStatus:function(){
                if(!this.task)return
                return this.task.states.sort((a,b) => {
                    if(a.pivot.created_at < b.pivot.created_at)return -1
                    if(a.pivot.created_at > b.pivot.created_at)return 1
                    return 0
                })
            },
            changeStatus:async function(){
                // Statusが選択されていない
                if(!this.selectedStatus.id){
                    return 
                }else{
                    let postObject = {
                        task_id:this.task.id,
                        state_id:this.selectedStatus.id,
                        state_detail:this.selectedStatus.state_detail
                    }
                    try{
                        await axios.post('/api/state_task',postObject)
                        this.$refs.notice.showNotice('タスクステータスを更新しました')
                        await this.fetchTask()
                        this.updateData()
                        this.selectedStatus = {id:'',state_detail:''} //リセット
                        this.$emit('input',this.task)
                    }catch(error){
                        this.$refs.notice.showNotice('タスクステータスの更新に失敗しました')
                        console.log(error)
                    }
                }
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
        border:1px solid grey;
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
        max-height:2000px;
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
        display:flex;
        align-items:center;
        color:white;
        position:absolute;
        top:0.5em;
        left:calc(50% - 2em);
        z-index:3;
        opacity:1.0;
        /*transform-origin:center;*/
        /*animation:icon-before 1s linear 0s 1,icon 0.5s linear 0.5s 1;*/
    }
    .state-icon div {
        transform-origin:center;
        animation:icon-before 1s linear 0s 1,icon 0.5s linear 0.5s 1;
    }
    .state-icon span {
        margin-left:1em;
        font-size:75%;
        animation:none;
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
    .tool-tip-content{
        display:flex;
        align-items:center;
    }
    .edit-status-modal * {
        margin:1em 0;
    }
    .edit-status-modal input {
        text-align:center;
        width:100%;
        display:block;
        margin:0.5em;
        padding:0.3em;
        border:1px solid grey;
        border-radius:0.3em;
    }
</style>
