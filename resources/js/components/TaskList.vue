<!--タスク一覧表示用コンポーネント-->
<!--今後の改修ポイント-->
<template>
    <div class="container">
        <!--通知-->
        <notice ref="notice" />
        
        <!--モーダル-->
        <!--新規タスク追加用モーダル-->
        <modal ref="modal" v-model="modal">
            <versatile-form 
                v-model="newTask" 
                ref="newTask" 
                table="tasks" 
                v-bind:foreignKeys="foreignKeys" 
                v-bind:columnOverride="columnOverride"
            />
            <div v-show="newTask.id" class="tags-and-items">
                <!--タグ登録-->
                <p>タグを追加します。</p>
                <tag-cloud v-model="selectedTags" v-bind:options="tags" multiple/>
                <!--アイテム登録-->
                <div>
                    <text-spliter v-model="items" />
                    <button class="btn btn-outline-primary mx-auto d-block" v-on:click="addItems">アイテムを追加</button>
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
            <sort-box v-model="displayedTasks" v-bind:originalArray="filteredTasks" v-bind:columns="sortColumns" />
        </div>
        
        <!--リスト表示-->
        <!--<task v-for="(task,index) in tasks" v-bind:taskId="task.id" v-bind:key="index"/>-->
        <!--<task v-for="(task,index) in displayedTasks" v-bind:taskId="task.id" v-bind:key="index"/>-->
        <div v-for="(task,index) in displayedTasks" class="task">
            <task v-bind:taskId="task.id" v-bind:key="index"/>
            <div class="control-buttons">
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
                filteredTasks:[], //一般フィルターでフィルターしたタスク配列
                displayedTasks:[], //表示用タスクの配列：タグフィルター後
                newTask:{},
                ids:[], //編集確認用のtask.idの配列
                columnOverride: [
                    {user_id:this.user_id},
                    {state_id:1} //新規タスクは「実行中」で登録
                ],
                foreignKeys:[
                    {project_id:
                        {
                            table:'projects',
                            columns:['name','dead_line'],
                            comment:'所属するプロジェクトを選択してください。'
                        }
                    }
                ],
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
            this.addFilters() //追加でフィルターオプションを追加（状態、）
        },
        watch: {
            // newTask:async function(newVal,oldVal){
            //     let result
            //     if(!newVal.id){return } //newValにidがなければ終了
            //     if(this.ids.indexOf(newVal.id) == -1){ //新規登録
            //         this.ids.push(newVal.id)
            //         result = await axios.get('/api/tasks/' + newVal.id)
            //         this.tasks.push(result.data)
            //     }else{                                 //編集->更新
            //         let index = this.tasks.findIndex((task) => {
            //             return (task.id == newVal.id)
            //         })
            //         this.tasks.splice(index,1,newVal)
            //     }
            // },
            // 新規登録モーダルを閉じた際に更新
            modal:function(){
                if(this.modal == false){
                    this.fetchTasks()
                }
            },
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
            fetchTags: async function(){
                 // タグの取得
                let result = await axios.get('/api/tags')
                let tagsResult = result.data
                
                //tag-cloudに投入できる形に整形
                for(let index of Object.keys(tagsResult)){
                    this.tags.push({label:tagsResult[index].name,value:tagsResult[index].id})
                }
            },
            addFilters: async function(){
                // statesフィルターオプションを追加
                let statesResult = await axios.get('api/states')
                let states = []
                for(let state of statesResult.data){
                    states.push({label:state.name,value:state.id})
                }
                this.filterOptions.push({label:'状態',value:'state_id',type:'options',options:states})
            },
            addTask:function(){
                // リセット
                this.newTask = {}
                this.selectedTags = []
                this.$refs.newTask.resetForm()
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
                            project_id:1, //デフォルトのプロジェクトは無し
                            state_id:1, //デフォルトの状態は「実行中」
                            name:this.quickTask,
                            overview:null,
                            priority:3, //デフォルトは3
                            difficulty:3, //デフォルトは3
                            start_date:currentDatetime.toISOString().slice(0, 19).replace('T', ' '),
                            dead_line:deadLine.toISOString().slice(0, 19).replace('T', ' ')
                        }
                        console.log(postObject)
                        await axios.post('/api/tasks/',postObject)
                        this.$refs.notice.showNotice('タスクを追加しました')
                        this.fetchTasks()
                        this.quickTask = ''
                    }catch(error){
                        this.$refs.notice.showNotice('アイテムの変更に失敗しました')
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
                    state_id:this.copyTargetTask.state_id,
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
        border:1px solid red;
    }
</style>
