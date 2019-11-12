<!--プロジェクト単体表示コンポーネント-->
<!--今後の改修ポイント-->
<!--１．進捗度メーター-->
<template>
    <div class="container">
        <modal ref="modal" v-model="modal">
            <versatile-form v-model="newTask" ref="form" table="tasks" v-bind:column_override="override" />
        </modal>
        <div class="wrapper">
            <div class="project">
                <h3>{{project.name}}</h3>
                <div class="info">最終締切　{{project.dead_line}}</div>
                <div class="overview">{{project.overview}}</div>
                <div class="tasks">
                    <button class="btn btn-outline-primary mx-auto d-block" v-on:click="addTask">プロジェクトにタスクを追加</button>
                    <task v-for="(task,index) in project.tasks" v-bind:task="task" v-bind:key="index" class="task" />
                    <p v-if="project.tasks == ''" class="task">タスクが登録されていません！</p>
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
                this.project.tasks.push(result.data)
            } 
        },
        created(){
            
        },
        mounted() {
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
        }
    }
</script>
<style scoped>
    .wrapper {
        border:3px solid grey;
        border-radius:0.2em;
        padding:1em;
    }
    .info {
        width:100%;
        margin:0.5em 0em;
        padding:0.1em 2em;
        background:orange;
        color:white;
        font-size:60%;
    }
    .overview {
        margin:0.5em 1em;
        padding:1em;
        border-radius:0.2em;
        background:whitesmoke;
    }
    .tasks {
        width:100%;
    }
    .task {
        margin-top:0.2em;
    }
</style>
