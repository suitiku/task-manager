<!--タスク一覧表示用コンポーネント-->
<!--今後の改修ポイント-->
<template>
    <div class="container">
        <!--通知-->
        <notice ref="notice" />
        
        <!--モーダル-->
        <!--新規タスク追加用モーダル-->
        <modal ref="modal" v-model="modal">
            <versatile-form v-model="newTask" table="tasks">
                <input v-model="newTask.name" type="text" placeholder="タスク名">
                <textarea v-model="newTask.overview" placeholder="概要" />
                <div class="inline">
                    <span>優先度</span>
                    <star-range v-model="newTask.priority" />
                </div>
                <div class="inline">
                    <span>難易度</span>
                    <star-range v-model="newTask.difficulty" />
                </div>
                <span>開始日</span>
                <date-picker v-model="newTask.start_date" />
                <span>締切</span>
                <date-picker v-model="newTask.dead_line" />
                <span>プロジェクトを選択してください</span>
                <list-box v-model="newTask.project_id" table="projects" />
            </versatile-form>
            <div v-show="newTask.id" class="tags-and-items">
                <!--タグ登録-->
                <p>タグを追加します。</p>
                <tag-cloud v-model="selectedTags" v-bind:options="tags" multiple/>
                <input class="input-inline" ref="newTag" type="text" placeholder="タグを新規登録" v-on:keydown="createTag()" />
                <!--アイテム登録-->
                <div>
                    <p>改行区切りでアイテムリストを作成します</p>
                    <text-spliter v-model="items" />
                    <button class="btn btn-outline-primary mx-auto d-block" v-on:click="addItems">アイテムリストを追加</button>
                </div>
            </div>
        </modal>
        
        <!--タスクコピー確認用モーダル-->
        <modal ref="copyTaskDialog" v-model="copyModal">
            <div class="task-copy-dialog">
                <p>タスク「{{copyTargetTask.name}}」をコピー／テンプレートにします。</p>
                <div>
                    <button type="button" class="btn btn-primary" v-on:click="copyTask()">コピーする</button>
                    <button type="button" class="btn btn-primary" v-on:click="templateTask()">テンプレートにする</button>
                    <button type="button" class="btn btn-secondary" v-on:click="hideCopyTaskDialog()">キャンセル</button>
                </div>
            </div>
        </modal>
        
        <!--テンプレート表示用モーダル-->
        <modal ref="templateModal" v-model="templateModal">
            <div class="task-copy-dialog">
                <p>生成元のテンプレートを選択してください</p>
                <div class="template-list">
                    <div v-for="(templateTask,templateIndex) in templateTasks">
                        <input type="radio" v-model="selectedTemplateTask" v-bind:value="templateTask">
                        <span>{{templateTask.name}}</span>
                    </div>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" v-on:click="addTemplateTask()">テンプレートからタスクを生成</button>
                    <button type="button" class="btn btn-secondary" v-on:click="hideTemplateModal()">キャンセル</button>
                </div>
            </div>
        </modal>
        
        <!--タスク追加エリア（固定）-->
        <div class="add-task-area">
            <input v-model="quickTask" type="text" placeholder="簡単登録" v-on:keydown="addQuickTask()">
            <button class="btn btn-primary mx-auto d-block" v-on:click="addTask()">詳細登録</button>
            <button class="btn btn-primary mx-auto d-block" v-on:click="showTemplateModal()">テンプレートから作成</button>
        </div>
        
        <div class="filter-and-sort">
            <!--フィルター-->
            <filter-box v-model="filteredTasks" v-bind:originalArray="tasks" v-bind:filterOptions="filterOptions" />
            <!--ソート-->
            <sort-box v-model="sortedTasks" v-bind:originalArray="filteredTasks" v-bind:columns="sortColumns" />
            <!--ステースタフィルター-->
            <filter-status v-model="displayedTasks" v-bind:originalArray="sortedTasks" />
        </div>
        
        <!--リスト表示-->
        <div v-for="(task,index) in displayedTasks" class="task">
            <task v-if="task" v-model="tasks[getTasksIndex(task.id)]" v-bind:taskId="task.id" v-bind:key="index"/>
            <div v-if="task" class="control-buttons">
                <i class="fas fa-copy" v-on:click="showCopyTaskDialog(task)"></i>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                modal:false,
                tasks:[], //tasksから取得したオリジナルの配列
                filteredTasks:[], //フィルターしたタスク配列
                sortedTasks:[], //ソートしたタスク配列
                displayedTasks:[], //表示用タスクの配列：ステータスフィルター後
                newTask:{},
                projects:[],
                defaultProjectId:'', //所属なしのproject_id
                ids:[], //編集確認用のtask.idの配列
                taskFilter:'incomplete',
                tags:[],
                selectedTags:[],
                items:[],
                quickTask:'',
                filterOptions:[
                    {label:'優先度',value:'priority',type:'star'},          
                    {label:'難易度',value:'difficulty',type:'star'},            
                    {label:'作成日',value:'start_date',type:'date'},
                    {label:'締切',value:'dead_line',type:'date'},
                ],
                sortColumns:[
                    {columnName:'name',columnLabel:'件名'},
                    {columnName:'priority',columnLabel:'優先度'},
                    {columnName:'difficulty',columnLabel:'難易度'},
                    {columnName:'start_date',columnLabel:'作成日'},
                    {columnName:'dead_line',columnLabel:'締切'},
                ],
                //コピー対象のタスクオブジェクト
                copyTargetTask:{},
                copyModal:false,
                
                //テンプレート関連
                templateTasks:[], //テンプレートタスク一覧
                templateModal:false,
                selectedTemplateTask:''
            }  
        },
        props:{
            user_id:{
                type:[String,Number],
                required:false,
            }  
        },
        mounted() {
        },
        created() {
            this.fetchTasks()
            this.fetchTags()
            this.fetchProjects()
        },
        watch: {
            //新規登録画面のタグ登録用
            selectedTags:async function(){
                if(!this.newTask.id){return }
                let tagsObject = {
                    task_id:this.newTask.id,
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
            //新規タスクが登録されたら状態を1:実行中で登録
            newTask:async function(){
                if(!this.newTask.id){return }
                try{
                    await axios.post('/api/state_task',{task_id:this.newTask.id,state_id:1})
                    let resultTask = await axios.get('/api/tasks/' + this.newTask.id)
                    this.tasks.push(resultTask.data)
                }catch(error){
                    console.log(error)
                }
            },
            tasks:async function(){
                // タスクが削除された際にインデックスを詰める
                for(let index in this.tasks){
                    if(this.tasks[index] == ''){
                        this.tasks.splice(index,1)
                    }
                }
            }
        },
        methods: {
            fetchTasks: async function(){
                // タスクの取得（ユーザーIDでフィルター）
                if(!this.user_id){return }
                let result = await axios.get('/api/mytasks',{
                                                params:{user_id:this.user_id,}
                                            })
                //テンプレートファイル以外を表示
                this.tasks = result.data.filter(task => {
                    return task.is_template == false
                })
                
                //テンプレートタスクリスト
                this.templateTasks = result.data.filter(task => {
                    return task.is_template == true
                })
            },
            //フィルター＆ソート
            updateTasks:async function(){
                
            },
            fetchTags: async function(){
                 // タグの取得
                let result = await axios.get('/api/mytags',{
                                                params:{user_id:this.user_id,}
                                            })
                let tagsResult = result.data
                
                //tag-cloudに投入できる形に整形
                for(let index of Object.keys(tagsResult)){
                    this.tags.push({label:tagsResult[index].name,value:tagsResult[index].id})
                }
            },
            fetchProjects:async function(){
                // プロジェクトの取得
                let result = await axios.get('/api/myprojects',{
                                                params:{user_id:this.user_id,}
                                            })
                this.projects = result.data
                
                //「所属なし」プロジェクトのidを設定（一番若いやつ？）
                this.defaultProjectId = result.data[0].id
            },
            addTask:function(){
                // リセット
                this.newTask = {
                    user_id:this.user_id,
                    project_id:'',
                    name:'',
                    overview:'',
                    priority:1,
                    difficulty:1,
                    start_date:'',
                    dead_line:'',
                    is_template:false,
                }
                this.selectedTags = []
                this.$refs.modal.openModal()
            },
            addItems:async function(){
                for(let item of this.items){
                    let postItem = {
                        task_id:this.newTask.id,
                        name:item,
                        is_checked:false
                    }
                    try{
                        await axios.post('/api/items',postItem)
                        this.$refs.notice.showNotice('タスクにアイテムを追加しました')
                    }catch(error){
                        this.$refs.notice.showNotice('アイテムの追加に失敗しました')
                        console.log(error)
                    }
                }
            },
            addQuickTask:async function(){
                if(event.keyCode == 13){ //変換終了時のenterではなく、かつenterキーの場合
                    try{
                        let currentDatetime = new Date()
                        let deadLine = new Date(currentDatetime.getTime() + 43200000) //デフォルトの締切は12時間後
                        let postObject = {
                            user_id:this.user_id,
                            project_id:this.defaultProjectId, //デフォルトのプロジェクトは無し
                            name:this.quickTask,
                            overview:null,
                            priority:3, //デフォルトは3
                            difficulty:3, //デフォルトは3
                            start_date:currentDatetime.toISOString().slice(0, 19).replace('T', ' '),
                            dead_line:deadLine.toISOString().slice(0, 19).replace('T', ' ')
                        }
                        let result = await axios.post('/api/tasks',postObject)
                        //1:実行中でstate_taskテーブルに登録
                        await axios.post('/api/state_task',{task_id:result.data.id,state_id:1})
                        //通知処理
                        this.$refs.notice.showNotice('タスクを追加しました')
                        this.fetchTasks()
                        this.quickTask = ''
                    }catch(error){
                        this.$refs.notice.showNotice('タスクの追加に失敗しました')
                        console.log(error)
                    }
                }
            },
            showCopyTaskDialog:function(task){
                this.copyTargetTask = task
                this.$refs.copyTaskDialog.openModal()
            },
            hideCopyTaskDialog:function(){
                this.$refs.copyTaskDialog.closeModal()
            },
            copyTask:async function(){
                let copiedTask
                this.$refs.copyTaskDialog.closeModal()
                let postObject = {
                    user_id:this.copyTargetTask.user_id,
                    project_id:this.copyTargetTask.project_id,
                    name:this.copyTargetTask.name + '（コピー）',
                    priority:this.copyTargetTask.priority,
                    difficulty:this.copyTargetTask.difficulty,
                    start_date:this.copyTargetTask.start_date,
                    dead_line:this.copyTargetTask.dead_line,
                    is_template:this.copyTargetTask.is_template,
                }
                try{
                    //タスク本体を登録
                    let result = await axios.post('/api/tasks/',postObject)
                    copiedTask = result.data
                    
                    //タグを登録
                    let tags = []
                    for(let tag of this.copyTargetTask.tags){
                        tags.push(tag.id)
                    }
                    let tagsPostObject = {task_id:copiedTask.id,tag_ids:tags}
                    await axios.put('/api/tag_task/',tagsPostObject)
                    
                    //アイテムを登録
                    for(let item of this.copyTargetTask.items){
                        let itemPostObject = {
                            task_id:copiedTask.id,
                            name:item.name,
                            is_checked:item.is_checked
                        }
                        await axios.post('/api/items/',itemPostObject)
                    }
                    
                    //状態を1:実行中で登録
                    await axios.post('/api/state_task',{task_id:copiedTask.id,state_id:1})
                    
                    //終了処理
                    this.$refs.notice.showNotice('タスクをコピーしました')
                    this.fetchTasks()
                }catch(error){
                    this.$refs.notice.showNotice('タスクのコピーに失敗しました')
                    console.log(error)
                }
            },
            templateTask:async function(){
                let taskId = this.copyTargetTask.id
                try{
                    //tasksのis_templateをtrueに
                    await axios.put('/api/tasks/' + taskId,{is_template:true})
                    //終了処理
                    this.$refs.notice.showNotice('タスクをテンプレートにしました')
                    this.fetchTasks()
                }catch(error){
                    this.$refs.notice.showNotice('タスクのテンプレート化に失敗しました')
                    console.log(error)
                }
            },
            showTemplateModal:function(){
                this.$refs.templateModal.openModal()
            },
            hideTemplateModal:function(){
                this.$refs.templateModal.closeModal()
            },
            addTemplateTask:async function(){
                this.copyTargetTask = this.selectedTemplateTask
                this.copyTargetTask.is_template = false
                this.copyTask()
                this.$refs.templateModal.closeModal()
            },
            getTasksIndex:function(id){
                return this.tasks.findIndex(task => task.id == id)
            },
            createTag:async function(){
                if(event.keyCode == 13){
                    let postObject = {
                        user_id:this.user_id,
                        name:event.target.value,
                        color:'#ef857d'
                    }
                    try{
                        let result = await axios.post('/api/tags',postObject)
                        this.$refs.notice.showNotice('タグを追加しました')
                        //tagsに追加
                        this.tags.push({label:result.data.name,value:result.data.id})
                        // インプットをリセット
                        this.$refs.newTag.value = ''
                    }catch(error){
                        this.$refs.notice.showNotice('タグの追加に失敗しました')
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
        width: 100%;
    }
    .sortBox {
        margin:1em;
        display:flex;
        justify-content:center;
    }
    .filter-box {
        width:100%;
        margin:1em;
        padding:1em;
        border:2px solid grey;
    }
    .filter {
        display:flex;
    }
    span {
        margin-right:1em;
    }
    input {
        margin:0 0.3em;
        /*width:100%;*/
        /*display:block;*/
        /*padding:0.3em;*/
        /*border:1px solid grey;*/
        /*border-radius:0.3em;*/
    }
    .add-task-area {
        position:fixed;
        display:flex;
        z-index:5;
        right:3em;
        bottom:0;
        background:orange;
        opacity:0.7;
        padding:1em;
    }
    
    .add-task-area:hover{
        opacity:1.0;
    }
    .add-task-area input {
        margin:0 0.5em;
        border: 1px solid #ccc;
        border-radius:0.3em;
    }
    .filter-and-sort {
        margin:1em 2em;
    }
    .task {
        display:flex;
        align-items:center;
    }
    .control-buttons i {
        cursor:pointer;
    }
    .control-buttons i:hover{
        color:salmon;
    }
    .task-copy-dialog {
        text-align:center;
    }
    .template-list {
        text-align:left;
    }
    .tags-and-items {
        margin:1.5em 0;
    }
    .tool-tip-content{
        display:flex;
        align-items:center;
    }
    .input-inline {
        margin:0.5em 0.3em;
        width:100%;
        display:block;
        padding:0.3em;
        border:1px solid grey;
        border-radius:0.3em;
    }
</style>
