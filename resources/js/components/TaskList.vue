<!--タスク一覧表示用コンポーネント-->
<!--今後の改修ポイント-->
<template>
    <div class="container">
        <modal ref="modal" v-model="modal">
            <versatile-form v-model="newTask" ref="form" table="tasks" v-bind:foreign_keys="foreign_keys"/>
        </modal>
        <button class="btn btn-outline-primary mx-auto d-block" v-on:click="addTask">タスクを追加</button>
        <div class="sortBox">
            <button class="btn btn-outline-primary mx-auto d-block" v-on:click="sortTask('dead_line')">締切</button>
            <button class="btn btn-outline-primary mx-auto d-block" v-on:click="sortTask('priority')">優先度</button>
            <button class="btn btn-outline-primary mx-auto d-block" v-on:click="sortTask('difficulty')">難易度</button>
        </div>
        <task class="task" v-for="(task,index) in tasks" v-bind:task="task" v-bind:key="index" />
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                modal:false,
                allTasks:[], //データベースから取得したリスト全体
                tasks:[], //表示用タスクリスト
                newTask:{},
                ids:[], //編集確認用のtask.idの配列
                foreign_keys:[
                    {project_id:
                        {
                            table:'projects',
                            columns:['name','dead_line'],
                            comment:'所属するプロジェクトを選択してください。'
                        }
                    }
                ],
                taskFilter:'incomplete'
            }  
        },
        mounted() {
        },
        created() {
            this.fetchTasks();
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
            taskFilter:function(){
                this.filterTasks(this.taskFilter)
            }
        },
        methods: {
            fetchTasks: async function(){
                let result = await axios.get('/api/tasks')
                this.allTasks = result.data
                this.tasks = result.data
                for(let index of Object.keys(this.allTasks)){
                    this.ids.push(this.allTasks[index].id)
                }
            },
            addTask:function(){
                this.$refs.form.resetForm()
                this.$refs.modal.openModal()
            },
            sortTask:function(key){
                this.tasks.sort((a,b) => {
                    return (a[key] < b[key]) ? -1 : 1
                })
            },
        }
    }
</script>
<style scoped>
    .sortBox {
        margin:1em;
        display:flex;
        justify-content:center;
    }
    .task {
        margin-bottom:0.2em;
    }
</style>
