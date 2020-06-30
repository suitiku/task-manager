<!--テンプレートタスク表示用コンポーネント-->
<template>
    <div v-if="task" class="container">
        <!--通知-->
        <notice ref="notice" />
        
        <!--テンプレート削除確認モーダル-->
        <modal ref="deleteModal" v-model="deleteModal">
            <b>テンプレート「{{task.name}}」を削除します。</b>
            <b>この処理は取り消しできません。</b>
            <b>よろしいですか？</b>
            <p>　</p>
            <div class="buttons">
                <button class="btn btn-danger d-block" v-on:click="deleteTask()">テンプレートを削除</button>
                <button class="btn btn-secondary d-block" v-on:click="cancelModal()">キャンセル</button>
            </div>
        </modal>
        
        <!--タグ編集用モーダル-->
        <modal ref="editTagModal" v-model="editTagModal">
            <p>タグの付替えを行います</p>
            <tag-list v-model="selectedTagIds" ref="tagList" v-bind:userId="task.user_id" />
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
                <date-picker ref="editTaskStartDate" v-model="editedTask.start_date" />
                <span>締切</span>
                <date-picker ref="editTaskDeadLine" v-model="editedTask.dead_line" />
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
                <button class="btn btn-secondary d-block" v-on:click="cancelModal()">キャンセル</button>
            </div>
        </modal>
        
        <!--メインコンテンツ-->
        <div v-bind:class="wrapper_class" v-bind:style="inactivateTask">
            <!--本体表示部分-->
            <!--ラベル部分-->
            <div class="task-label" v-on:click="openDetail()">
                <div class="headline">
                    <div>
                        <span class="task-headline">{{task.name}}</span>
                    </div>
                </div>
                <div>
                    
                    <!--タグ編集ボタン-->
                    <i class="fas fa-tag task-icon" v-on:click="showEditTagModal()"></i>
                    <!--編集ボタン-->
                    <i class="far fa-edit task-icon" v-on:click="showEditTaskModal()"></i>
                    <!--削除ボタン-->
                    <i class="fas fa-trash task-icon" v-on:click="showDeleteTaskModal()"></i>
                </div>
            </div>
            <!--詳細部分（クリックで開閉）-->
            <div class="detail">
                <!--概要-->
                <p class="overview">{{task.overview}}</p>
                
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
                <!--子アイテム-->
                <div class="items">
                    <p v-for="(item,itemIndex) in task.items" v-bind:class="setItemClass(item.is_checked)" v-bind:style="inactivateItem[item.id]">
                            <span v-show="!editItemMode[itemIndex]">{{item.name}}</span>
                            <!--編集用セクション-->
                            <span v-show="editItemMode[itemIndex]">
                                <input class="editable" type="text" v-model="task.items[itemIndex].name" v-on:keydown="updateItem(itemIndex)">
                            </span> 
                            <!--編集ボタン-->
                            <i class="far fa-edit task-icon" v-on:click="toggleEditMode(itemIndex)"></i>
                            <!--削除ボタン-->
                            <i class="fas fa-trash task-icon" v-on:click="showDeleteItemModal(item.id,item.name)"></i>
                    </p>
                    <!--アイテムの追加処理セクション-->
                    <div class="editable">
                        <text-spliter v-model="items" />
                        <button class="btn btn-outline-primary mx-auto d-block" v-on:click="addItems()">アイテムを追加</button>
                    </div>
                </div>
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
                selectedTagIds:[],
            }  
        },
        props:{
            value: {
                type:[String,Number,Object],
                required:false,
            }
        },
        watch:{
            value:{
                handler:async function() {
                    this.setTask()
                },
                deep:true,
            },
            //編集用モーダルを閉じたときにアップデート
            editModal:async function(newVal,oldVal){
                if(newVal == false){
                    await this.fetchTask()
                }
            },
            selectedTagIds:async function(newVal,oldVal){
                //モーダルを開いている場合だけタグの変更を有効化
                if(!this.editTagModal)return
                
                // 投入用オブジェクト作成
                let tagsObject = {
                    task_id:this.task.id,
                    tag_ids:this.selectedTagIds
                }
                
                // 書き込み
                try{
                    await axios.put('/api/tag_task',tagsObject)
                    this.$refs.notice.showNotice('タグを変更しました')
                }catch(error){
                    this.$refs.notice.showNotice('タグの変更に失敗しました')
                    console.log(error)
                }
            },
        },
        created:async function(){
        },
        mounted:async function(){
            this.setTask()
        },
        computed:{
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
                
                //タグリストのtagIdセット
                this.selectedTagIds = this.task.tags.map(el => el.id)
            },
            openDetail: function(){
                this.fetchTask()
                this.detail = !this.detail
                this.wrapper_class = this.detail ? 'task-wrapper detail-active' : 'task-wrapper'
            },
            setItemClass:function(is_checked){
                return is_checked == true ? 'item-completed' : ''
            },
            setItemDisabled:function(is_checked){
                return is_checked || false
            },
            showDeleteTaskModal:function(){
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
            showEditTaskModal:async function(){
                //datepickerの初期値設定
                this.$refs.editTaskStartDate.init(this.task.start_date)
                this.$refs.editTaskDeadLine.init(this.task.dead_line)
                // モーダル展開
                this.$refs.editModal.openModal()
            },
            showDeleteItemModal:function(id,name){
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
            cancelModal:function(){
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
                        let result = await axios.post('/api/items',postItem)
                        this.task.items.push(result.data)
                        this.$refs.notice.showNotice('タスクにアイテムを追加しました')
                    }catch(error){
                        this.$refs.notice.showNotice('アイテムの追加に失敗しました')
                        console.log(error)
                    }
                }
                this.$emit('input',this.task)
                this.items = []
            },
            showEditTagModal:function(){
                this.$refs.editTagModal.openModal()
                this.$refs.tagList.fetchTags()
                this.isEditedTags = true
            },
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
        cursor:pointer;
        z-index:2;
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
    .tags {
        display:flex;
        justify-content:flex-start;
        flex-wrap:wrap;
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
        
    }
    .detail-active {
        max-height:2000px;
        transition:all 1.0s ease;
    }
    .editable {
        position:relative;
        z-index:3;
    }
    .tool-tip-content{
        display:flex;
        align-items:center;
    }
    .overview {
        white-space:pre-wrap;
        word-wrap:break-word;
    }
</style>
