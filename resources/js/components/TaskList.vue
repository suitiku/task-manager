<!--タスク一覧表示用コンポーネント-->
<!--今後の改修ポイント-->
<template>
    <div class="container">
        <!--通知-->
        <notice ref="notice" />
        
        <!--モーダル-->
        <modal ref="modal" v-model="modal">
            <versatile-form v-model="newTask" ref="newTask" table="tasks" v-bind:foreignKeys="foreignKeys"/>
        </modal>
        
        <!--タスク追加エリア（固定）-->
        <div class="add-task-area">
            <input v-model="quickTask" type="text" placeholder="簡単タスク登録" v-on:keydown="addQuickTask()">
            <button class="btn btn-outline-primary mx-auto d-block" v-on:click="addTask">詳細登録ダイアログを表示</button>
        </div>
        
        <!--フィルター-->
        <filter-box v-model="displayedTasks" v-bind:targetArray="tasks" v-bind:filterOptions="filterOptions" />
        
        <!--ソート-->
        <!--<div class="sortBox">-->
        <!--    <button class="btn btn-outline-primary mx-auto d-block" v-on:click="sortTask('dead_line')">締切</button>-->
        <!--    <button class="btn btn-outline-primary mx-auto d-block" v-on:click="sortTask('priority')">優先度</button>-->
        <!--    <button class="btn btn-outline-primary mx-auto d-block" v-on:click="sortTask('difficulty')">難易度</button>-->
        <!--</div>-->
        
        <!--リスト表示-->
        <!--<task v-for="(task,index) in tasks" v-bind:taskId="task.id" v-bind:key="index"/>-->
        <task v-for="(task,index) in displayedTasks" v-bind:taskId="task.id" v-bind:key="index"/>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                modal:false,
                tasks:[], //tasksから取得したオリジナルの配列
                displayedTasks:[], //フィルター、ソートされたtaskの配列
                newTask:{},
                ids:[], //編集確認用のtask.idの配列
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
                quickTask:'',
                filterOptions:[
                    {label:'優先度',value:'priority',type:'star'},          
                    {label:'難易度',value:'difficulty',type:'star'},            
                    {label:'作成日',value:'start_date',type:'date'},
                    {label:'締切',value:'dead_line',type:'date'},
                ],
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
            newTask:async function(newVal,oldVal){
                let result
                if(!newVal.id){return } //newValにidがなければ終了
                if(this.ids.indexOf(newVal.id) == -1){ //新規登録
                    this.ids.push(newVal.id)
                    result = await axios.get('/api/tasks/' + newVal.id)
                    this.tasks.push(result.data)
                }else{                                 //編集->更新
                    let index = this.tasks.findIndex((task) => {
                        return (task.id == newVal.id)
                    })
                    this.tasks.splice(index,1,newVal)
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
                this.tasks = result.data
            },
            fetchTags: async function(){
                 // タグの取得
                let result = await axios.get('/api/tags')
                let tagsResult = result.data
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
                this.$refs.newTask.resetForm()
                this.$refs.modal.openModal()
            },
            sortTask:async function(key){
                await this.fetchTasks()
                this.tasks.sort((a,b) => {
                    return (a[key] < b[key]) ? -1 : 1
                })
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
    .add-task-area input {
        margin:0 0.5em;
        border: 1px solid #ccc;
        border-radius:0.3em;
    }
</style>
