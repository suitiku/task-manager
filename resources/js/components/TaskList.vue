<!--タスク一覧表示用コンポーネント-->
<!--今後の改修ポイント-->
<!--①テンプレートタスクの処理を分離する-->
<template>
    <div class="task-list-wrapper">
        <!--ウェイティング画面-->
        <waiting ref="waiting" />
        
        <!--通知-->
        <notice ref="notice" />
        
        <!--モーダル-->
        <!--新規タスク追加用モーダル-->
        <modal ref="newTaskModal" v-model="newTaskModal">
            <versatile-form ref="newTaskForm" v-model="newTask" table="tasks">
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
                <date-picker v-model="newTask.start_date" ref="newTaskStartDate" />
                <span>締切</span>
                <date-picker v-model="newTask.dead_line" ref="newTaskDeadLine" />
                
                <div v-if="!projectId">
                    <span>プロジェクトを選択してください（選択しない場合は単体のタスクとなります）</span>
                    <tag-cloud v-model="newTask.project_id" v-bind:options="projects" v-bind:defaultValue="defaultProjectId"/>
                    <input class="input-inline" ref="newProject" type="text" placeholder="プロジェクトを新規登録" v-on:keydown="createProject()" />
                </div>
            </versatile-form>
            <div v-show="newTask.id" class="tags-and-items">
                <!--タグ登録-->
                <p>タグを追加します。</p>
                <tag-list v-model="selectedTagIds" ref="tagList" v-bind:userId="userId" />
                
                <!--アイテム登録-->
                <div>
                    <p>改行区切りでアイテムリストを作成します</p>
                    <text-spliter v-model="items" />
                    <button class="btn btn-outline-primary mx-auto d-block" v-on:click="addItems">アイテムリストを追加</button>
                </div>
            </div>
        </modal>
        
        <!--タスクコピー確認用モーダル-->
        <modal ref="copyTaskModal" v-model="copyModal">
            <div class="task-copy-dialog">
                <p>タスク「{{copyTargetTask.name}}」をコピー／テンプレートにします。</p>
                <div>
                    <button type="button" class="btn btn-primary" v-on:click="copyTask()">コピーする</button>
                    <button type="button" class="btn btn-primary" v-on:click="templateTask()">テンプレートにする</button>
                    <button type="button" class="btn btn-secondary" v-on:click="hidecopyTaskModal()">キャンセル</button>
                </div>
            </div>
        </modal>
        
        <!--テンプレート表示用モーダル-->
        <modal ref="templateModal" v-model="templateModal">
            <div class="task-copy-dialog">
                <p>生成元のテンプレートを選択してください</p>
                <div class="template-list">
                    <div v-for="(templateTask,templateIndex) in templateTasks">
                        <input v-if="templateTask" type="radio" v-model="selectedTemplateTask" v-bind:value="templateTask">
                        <span v-if="templateTask">{{templateTask.name}}</span>
                    </div>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" v-on:click="addTemplateTask()">テンプレートからタスクを生成</button>
                    <button type="button" class="btn btn-secondary" v-on:click="hideTemplateModal()">キャンセル</button>
                </div>
            </div>
        </modal>
        
        <!--リスト表示部-->
        <div class="task-list-container">
            <div class="all-task-switch" v-if="!projectId">
                <span>全タスクを表示</span>
                <toggle-switch v-model="allTasks" />
            </div>
            <!--リマインダー表示部-->
            <reminders v-bind:userId="userId" />
            
            <!--フィルター＆ソート-->
            <div class="filter-and-sort">
                <!--フィルター-->
                <div class="filter-area">
                    <filter-array v-model="filteredTasks" v-bind:originalArray="tasks" v-bind:filterOptions="filterOptions" />
                    <filter-tag v-model="tagFilteredTasks" v-bind:originalArray="filteredTasks" />
                    <filter-stared v-model="staredFilteredTasks" v-bind:originalArray="tagFilteredTasks" />
                    <!--ステースタフィルター-->
                    <filter-status v-model="statusFilteredTasks" v-bind:originalArray="staredFilteredTasks" />
                </div>
                <!--ソート-->
                <sort-box v-model="displayedTasks" v-bind:originalArray="statusFilteredTasks" v-bind:columns="sortColumns" />
            </div>
            
            <!--リスト表示-->
            <div v-for="(task,index) in displayedTasks" class="task">
                <task v-if="task" v-model="tasks[getTasksIndex(task.id)]" v-bind:key="index"/>
                <div v-if="task" class="control-buttons">
                    <i class="fas fa-copy" v-on:click="showCopyTaskModal(task)"></i>
                </div>
            </div>
            
            <!--タスク追加エリア-->
            <div v-bind:class="addTaskArea">
                <input v-model="quickTask" type="text" placeholder="簡単登録" v-on:keydown="addQuickTask()">
                <button class="btn btn-primary mx-auto d-block" v-on:click="showNewTaskModal()">詳細登録</button>
                <button class="btn btn-primary mx-auto d-block" v-on:click="showTemplateModal()">テンプレートから作成</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        model:{
            prop:'tasks'
        },
        data:function(){
            return {
                allTasks:false,
                newTaskModal:false,
                filteredTasks:[], //フィルターしたタスク配列
                tagFilteredTasks:[], //タグでフィルターしたタスク配列
                staredFilteredTasks:[], //スターでふぃるたしたタスク配列
                statusFilteredTasks:[], //ステータスフィルターしたタスク配列
                displayedTasks:[], //表示用タスクの配列：ソート後
                newTask:{},
                selectedTagIds:[],
                projects:[],
                defaultProjectId:'', //所属なしのproject_id
                ids:[], //編集確認用のtask.idの配列
                taskFilter:'incomplete',
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
                templateTasks:[],
                templateModal:false,
                selectedTemplateTask:'',
                
            }  
        },
        props:{
            userId:{
                type:[String,Number],
                required:false,
            },
            projectId:{
                type:[String,Number],
                required:false
            },
            tasks:{
                type:[String,Array],
            }
        },
        mounted() {
            if(this.projectId)return 
            
            this.fetchCurrentTasks()
            this.fetchTemplateTasks()
            this.fetchProjects()
        },
        created() {
            
        },
        computed:{
             // タスク追加部分のclass設定
            addTaskArea:function(){
                if(this.projectId){
                    return 'add-task-area'
                }else{
                    return 'add-task-area fixed'
                }
            }  
        },
        watch: {
            //新規タスク登録モーダルを閉じた際にtasksに追加
            newTaskModal:async function(newVal,oldVal){
                if(newVal == false){
                    if(!this.newTask.id){return }
                    let result = await axios.get('/api/tasks/' + this.newTask.id)
                    this.tasks.push(result.data)
                }
            },
            //全タスク取得スイッチ
            allTasks:function(newVal,oldVal){
                if(newVal == true){
                    this.fetchAllTasks()
                }else{
                    this.fetchCurrentTasks()
                }
            },
            selectedTagIds:async function(){
                //新規タスクのidが設定されている場合だけタグの変更を有効化
                if(!this.newTask.id)return
                
                // 投入用オブジェクト作成
                let tagsObject = {
                    task_id:this.newTask.id,
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
        methods: {
            fetchAllTasks:async function(){
                  this.$refs.waiting.enableWaiting('タスクを読み込んでいます')
                  try{
                        let result = await axios.get('/api/mytasks',{
                                                params:{user_id:this.userId,}
                                            })
                        this.$emit('input',result.data)
                        this.$refs.waiting.disableWaiting()
                        this.$refs.notice.showNotice('全タスクを取得しました')
                  }catch(error){
                      this.$refs.waiting.disableWaiting()
                      this.$refs.notice.showNotice('タスクの取得に失敗しました')
                      console.log(error)
                  }
            },
            fetchCurrentTasks:async function(){
                  this.$refs.waiting.enableWaiting('タスクを読み込んでいます')
                  try{
                        let result = await axios.get('/api/mytasks/current',{
                                                params:{user_id:this.userId,}
                                            })
                        this.$emit('input',result.data)
                        this.$refs.waiting.disableWaiting()
                        this.$refs.notice.showNotice('タスクを取得しました')
                  }catch(error){
                      this.$refs.waiting.disableWaiting()
                      this.$refs.notice.showNotice('タスクの取得に失敗しました')
                      console.log(error)
                  }
            },
            fetchTemplateTasks:async function(){
                try{
                    let result = await axios.get('/api/mytemplates',{
                                            params:{user_id:this.userId,}
                                        })
                    this.templateTasks = result.data
              }catch(error){
                  console.log(error)
              }
            },
            fetchProjects:async function(){
                // userIdがない場合は飛ばす
                if(!this.userId)return
                
                // プロジェクトの取得
                let result = await axios.get('/api/myprojects',{
                                                params:{user_id:this.userId,}
                                            })
                                            
                //「所属なし」プロジェクトのidを設定（一番若いやつ？）
                this.defaultProjectId = result.data[0].id
                
                // projectIdが設定されている場合は飛ばす
                if(this.projectId)return
                
                //プロジェクトをセット
                this.projects = result.data
                
                // デフォルトプロジェクトを削除
                this.projects.shift()
            },
            showNewTaskModal:function(){
                //プロジェクトを再取得
                this.fetchProjects()
                
                //フォームのpostObjectを初期化
                this.$refs.newTaskForm.init()
                
                //開始と締め切りを現在時刻に合わせる
                let currentDatetime = new Date()
                
                 // リセット
                this.newTask = {
                    user_id:this.userId,
                    project_id:this.defaultProjectId, //デフォルトプロジェクトIDにしておく
                    name:'',
                    overview:'',
                    priority:1,
                    difficulty:1,
                    is_template:false,
                    start_date:currentDatetime,
                    dead_line:currentDatetime
                }
                
                //DatePickerを初期化
                this.$refs.newTaskStartDate.init()
                this.$refs.newTaskDeadLine.init()
                
                // タグリストをリセット
                this.selectedTagIds = []
                this.$refs.tagList.init()
                
                //子アイテムをリセット
                this.items = []
                //モーダルを展開
                this.$refs.newTaskModal.openModal()
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
                    // プロジェクトの下にある場合はprojectIdを使う。ない場合は「所属なし」
                    let project_id = this.projectId || this.defaultProjectId
                    try{
                        let currentDatetime = new Date()
                        let deadLine = new Date(currentDatetime.getTime() + 43200000) //デフォルトの締切は12時間後
                        let postObject = {
                            user_id:this.userId,
                            project_id:project_id,
                            name:this.quickTask,
                            overview:null,
                            priority:3, //デフォルトは3
                            difficulty:3, //デフォルトは3
                            start_date:currentDatetime.toISOString().slice(0, 19).replace('T', ' '),
                            dead_line:deadLine.toISOString().slice(0, 19).replace('T', ' ')
                        }
                        let result = await axios.post('/api/tasks',postObject)
                        this.tasks.push(result.data)
                        //通知処理
                        this.$refs.notice.showNotice('タスクを追加しました')
                        this.quickTask = ''
                    }catch(error){
                        this.$refs.notice.showNotice('タスクの追加に失敗しました')
                        console.log(error)
                    }
                }
            },
            showCopyTaskModal:function(task){
                this.copyTargetTask = JSON.parse(JSON.stringify(task))
                if(this.projectId){
                    this.copyTargetTask.project_id = this.projectId
                }
                this.$refs.copyTaskModal.openModal()
            },
            hidecopyTaskModal:function(){
                this.$refs.copyTaskModal.closeModal()
            },
            copyTask:async function(){
                this.$refs.waiting.enableWaiting('タスクをコピーしています')
                try{
                    let result = await axios.post('/api/tasks/copy/' + this.copyTargetTask.id)
                    this.tasks.push(result.data)
                    this.$refs.waiting.disableWaiting()
                    this.$refs.copyTaskModal.closeModal()
                    this.$refs.notice.showNotice('タスクをコピーしました')
                }catch(error){
                    this.$refs.waiting.disableWaiting()
                    this.$refs.copyTaskModal.closeModal()
                    this.$refs.notice.showNotice('タスクのコピーに失敗しました')
                    console.log(error)
                }
            },
            //タスクをテンプレート化する
            templateTask:async function(){
                let taskId = this.copyTargetTask.id
                try{
                    //taskのis_templateをtrueに
                    await axios.put('/api/tasks/' + taskId,{project_id:this.defaultProjectId,is_template:true})
                    //終了処理
                    this.$refs.copyTaskModal.closeModal()
                    this.$refs.notice.showNotice('タスクをテンプレートにしました')
                    this.tasks[this.getTasksIndex(taskId)].is_template = true
                    this.templateTasks.push(this.tasks[this.getTasksIndex(taskId)])
                    this.tasks.splice(this.getTasksIndex(taskId),1)
                    this.$emit('input',this.tasks)
                }catch(error){
                    this.$refs.copyTaskModal.closeModal()
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
                this.copyTargetTask = JSON.parse(JSON.stringify(this.selectedTemplateTask))
                this.$refs.waiting.enableWaiting('テンプレートからタスクを作成しています')
                try{
                    let result = await axios.post('/api/tasks/template/' + this.copyTargetTask.id)
                    //Project.vueの下にある場合はproject_idを書き換える
                    if(this.projectId){
                        console.log(result.data.id)
                        await axios.put('/api/tasks/' + result.data.id, {project_id:this.projectId})
                    }
                    this.tasks.push(result.data)
                    this.$refs.waiting.disableWaiting()
                    this.$refs.templateModal.closeModal()
                    this.$refs.notice.showNotice('テンプレートからタスクを作成しました')
                }catch(error){
                    this.$refs.waiting.disableWaiting()
                    this.$refs.templateModal.closeModal()
                    this.$refs.notice.showNotice('テンプレートからのタスクの作成に失敗しました')
                    console.log(error)
                }
            },
            getTasksIndex:function(id){
                return this.tasks.findIndex(task => task.id == id)
            },
            createProject:async function(){
                if(event.keyCode == 13){
                    let currentDatetime = new Date()
                    let deadLine = new Date(currentDatetime.getTime() + 604800000) //デフォルトの締切は一週間後
                    let postObject = {
                        user_id:this.userId,
                        name:event.target.value,
                        dead_line:deadLine.toISOString().slice(0, 19).replace('T', ' ')
                    }
                    try{
                        await axios.post('/api/projects',postObject)
                        this.$refs.notice.showNotice('プロジェクトを追加しました')
                        //プロジェクトを再取得
                        this.fetchProjects()
                    }catch(error){
                        this.$refs.notice.showNotice('プロジェクトの追加に失敗しました')
                        console.log(error)
                    }
                    
                }
            },
        }
    }
</script>
<style scoped>
    .task-list-container {
        display:flex;
        flex-direction:column;
        align-items:center;
        position:relative;
        width: 100%;
        padding-bottom:3em;
    }
    .sortBox {
        margin:1em 0;
        display:flex;
        justify-content:center;
    }
    .filter-box {
        width:100%;
        margin:1em 0;
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
    }
    .add-task-area {
        margin:1em 2em;
        width:100%;
        display:flex;
        background:orange;
        padding:1em;
    }
    .fixed {
        width:50%;
        right:3em;
        bottom:0;
        opacity:0.7;
        position:fixed;
        z-index:5;
    }
    .fixed:hover{
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
    .filter-area {
        display:flex;
        justify-content:flex-start;
    }
    .filter-area div {
        margin:0.5em;
    }
    .task {
        width:100%;
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
    .all-task-switch {
        display:flex;
        align-items:center;
    }
    .all-task-switch span {
        margin-right:1em;
    }
</style>
