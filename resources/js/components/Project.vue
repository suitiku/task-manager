<!--プロジェクト単体表示コンポーネント-->
<!--今後の改修ポイント-->
<template>
    <div class="container">
        <!--新規タスク追加用モーダル-->
        <!--<modal ref="newTaskModal" v-model="newTaskModal">-->
        <!--    <versatile-form v-model="newTask" table="tasks">-->
        <!--        <input v-model="newTask.name" type="text" placeholder="タスク名">-->
        <!--        <textarea v-model="newTask.overview" placeholder="概要" />-->
        <!--        <div class="inline">-->
        <!--            <span>優先度</span>-->
        <!--            <star-range v-model="newTask.priority" />-->
        <!--        </div>-->
        <!--        <div class="inline">-->
        <!--            <span>難易度</span>-->
        <!--            <star-range v-model="newTask.difficulty" />-->
        <!--        </div>-->
        <!--        <span>開始日</span>-->
        <!--        <date-picker v-model="newTask.start_date" />-->
        <!--        <span>締切</span>-->
        <!--        <date-picker v-model="newTask.dead_line" />-->
        <!--        <span>プロジェクトを選択してください</span>-->
        <!--        <list-box v-model="newTask.project_id" ref="projectsListbox" table="projects" />-->
        <!--        <input class="input-inline" ref="newProject" type="text" placeholder="プロジェクトを新規登録" v-on:keydown="createProject()" />-->
        <!--    </versatile-form>-->
        <!--    <div v-show="newTask.id" class="tags-and-items">-->
                <!--タグ登録-->
        <!--        <p>タグを追加します。</p>-->
        <!--        <tag-list v-bind:taskId="newTask.id" />-->
                <!--アイテム登録-->
        <!--        <div>-->
        <!--            <p>改行区切りでアイテムリストを作成します</p>-->
        <!--            <text-spliter v-model="items" />-->
        <!--            <button class="btn btn-outline-primary mx-auto d-block" v-on:click="addItems()">アイテムリストを追加</button>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</modal>-->
        
        <!--表示部-->
        <div class="project-wrapper">
            <div class="info"><i class="far fa-clock"></i>　{{project.dead_line}}</div>
            <div class="project-content-wrapper">
                <h3>{{project.name}}</h3>
                <progress-bar v-bind:denominotor="denominotor" v-bind:numerator="numerator" />
                <button class="btn btn-outline-primary mx-auto d-block" v-on:click="openDetail">
                    <span v-if="detail == 'project-detail-close'">プロジェクト詳細を表示する</span>
                    <span v-else>詳細を隠す</span>
                </button>
                <div v-bind:class="detail">
                    <!--関係者リスト-->
                    
                    <!--概要-->
                    <div class="overview">{{project.overview}}</div>
                    
                    <!--タスクリスト-->
                    <!--<div class="tasks">-->
                        <!--<task v-model="project.tasks[index]" v-for="(task,index) in project.tasks" v-bind:task="task" v-bind:key="index"/>-->
                    <!--    <task v-model="project.tasks[index]" v-for="(task,index) in project.tasks" v-bind:taskId="task.id" v-bind:key="index"/>-->
                    <!--    <p v-if="project.tasks == ''" class="task">タスクが登録されていません！</p>-->
                    <!--    <button class="btn btn-outline-primary mx-auto d-block" v-on:click="addTask">プロジェクトにタスクを追加</button>-->
                    <!--</div>-->
                    <task-list v-bind:taskIds="taskIds" v-bind:projectId="project.id" v-bind:userId="project.user_id" />
                    
                    <!--ガントチャート-->
                    
                    <!--ログ-->
                    
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                // newTaskModal:false,
                // newTask:{},
                // items:[],
                taskIds:[],
                denominotor:0,
                numerator:0,
                detail:'project-detail-close'
            }  
        },
        props: {
            project:{
                type:Object,
                default:{},
                required:true,
            }  
        },
        watch:{
            newTask:async function(newVal,oldVal){
                if(!newVal.id){return } //newValにidがなければ終了
                let result = await axios.get('/api/tasks/' + newVal.id)
                delete result.data.project
                this.project.tasks.push(result.data)
            },
            'project.tasks': {
                handler: function(newVal,oldVal){
                    // プロジェクトラベルの削除
                    for(let value of this.project.tasks){
                        delete value.project
                    }
                    //プログレスバーの設定
                    this.setProgress() 
                },
                deep:true
            }
        },
        created(){
            
        },
        mounted() {
            this.getTaskIds()
            this.setProgress()
        },
        computed:{
            override:function(){
                return [{project_id:this.project.id}]
            }
        },
        methods: {
            // showNewTaskModal:function(){
            //     // リセット
            //     this.newTask = {
            //         user_id:this.user_id,
            //         project_id:'',
            //         name:'',
            //         overview:'',
            //         priority:1,
            //         difficulty:1,
            //         start_date:'',
            //         dead_line:'',
            //         is_template:false,
            //     }
            //     this.items = []
            //     this.$refs.newTaskModal.openModal()
            // },
            getTaskIds:function(){
                this.taskIds = []
                if(this.project.tasks.length == 0)return 
                for(let task of this.project.tasks){
                    this.taskIds.push(task.id)
                }
            },
            openDetail:function(){
                this.detail = this.detail == 'project-detail-close' ? 'project-detail-open' : 'project-detail-close'  
            },
            setProgress:function(){
                //タスクが登録されていない場合は終了
                if(this.project.tasks.length == 0){
                    this.denominotor = 0
                    return
                }
                
                this.denominotor = this.project.tasks.length
                let numerator = 0
                for(let task of this.project.tasks){
                    if(task.state_id == 3){
                        numerator ++
                    }
                }
                this.numerator = numerator
            }
        }
    }
</script>
<style scoped>
    .project-wrapper {
        position:relative;
        width:100%;
        border:3px solid grey;
        border-radius:0.2em;
    }
    .project-content-wrapper {
        position:relative;
        padding:1em;
        overflow:hidden;
    }
    .info {
        width:100%;
        margin-bottom:0.5em;
        padding:0.1em 2em;
        background:orange;
        color:white;
        font-size:60%;
    }
    .project-detail-close {
        max-height:0;
        transition:all 0.3s;
    }
    .project-detail-open {
        position:relative;
        max-height:1000px;
        transition:all 0.3s;
    }
    .overview {
        margin:0.5em 1em;
        padding:1em;
        border-radius:0.2em;
        background:gainsboro;
    }
    .tasks {
        position:relative;
        width:100%;
        margin:1em 0;
    }
    button {
        margin:0.5em 0;
    }
</style>
