<!--タスク一覧-->

<!--今後の改修ポイント-->
<!--1 並び替え（優先度、難易度、締切）-->
<template>
    <div class="container">
        <modal ref="modal" v-model="modal">
            <versatile-form v-model="newTask" ref="form" table="tasks" />
        </modal>
        <button class="btn btn-outline-primary mx-auto d-block" v-on:click="addTask">タスクを追加</button>
        <task v-for="(task,index) in tasks" v-bind:task="task" v-bind:key="index" />
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                modal:false,
                tasks:[],
                newTask:{},
                ids:[] //編集確認用のtask.idの配列
            }  
        },
        mounted() {
        },
        created() {
            this.fetchTasks();
        },
        watch: {
            newTask:function(newVal,oldVal){
                if(!newVal.id){return } //newValにidがなければ終了
                if(this.ids.indexOf(newVal.id) == -1){ //新規登録
                    this.tasks.push(newVal)
                    this.wrapperClass.push('task-wrapper')
                    this.ids.push(newVal.id)
                }else{                                 //編集->更新
                    let index = this.tasks.findIndex((task) => {
                        return (task.id == newVal.id)
                    })
                    this.tasks.splice(index,1,newVal)
                }
            }  
        },
        methods: {
            fetchTasks: async function(){
                let result = await axios.get('/api/tasks')
                this.tasks = result.data
            },
            addTask:function(){
                this.$refs.form.resetForm()
                this.$refs.modal.openModal()
            }
        }
    }
</script>
<style scoped>

</style>
