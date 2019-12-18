<!--プロジェクト単体表示コンポーネント-->
<!--今後の改修ポイント-->
<template>
    <div class="container">
        <modal ref="modal" v-model="modal">
            <versatile-form v-model="newTask" ref="form" table="tasks" v-bind:column_override="override" />
        </modal>
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
                    <div class="tasks">
                        <!--<task v-model="project.tasks[index]" v-for="(task,index) in project.tasks" v-bind:task="task" v-bind:key="index"/>-->
                        <task v-model="project.tasks[index]" v-for="(task,index) in project.tasks" v-bind:taskId="task.id" v-bind:key="index"/>
                        <p v-if="project.tasks == ''" class="task">タスクが登録されていません！</p>
                        <button class="btn btn-outline-primary mx-auto d-block" v-on:click="addTask">プロジェクトにタスクを追加</button>
                    </div>
                    
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
                modal:false,
                newTask:{},
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
            this.setProgress()
        },
        computed:{
            override:function(){
                return [{project_id:this.project.id}]
            }
        },
        methods: {
            addTask:function(){
                this.$refs.form.resetForm()
                this.$refs.modal.openModal()
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
        max-height:500px;
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
