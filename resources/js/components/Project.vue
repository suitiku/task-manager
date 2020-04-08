<!--プロジェクト単体表示コンポーネント-->
<!--今後の改修ポイント-->
<template>
    <div class="container">
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
                    <task-list v-model="tasks" v-bind:taskIds="taskIds" v-bind:projectId="project.id" v-bind:userId="project.user_id" />
                    
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
                taskIds:[],
                tasks:[], //変更監視用
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
            tasks:function(){
                this.setProgress() 
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
                if(this.tasks.length == 0){
                    this.denominotor = 0
                    return
                }
                
                this.denominotor = this.tasks.length
                let numerator = 0
                for(let task of this.tasks){
                    // 最新の状態を取得
                    let lastTaskStatusId = task.states[task.states.length -1].id
                    if(lastTaskStatusId == 2){
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
