<!--テンプレートタスクの管理用-->
<template>
    <div class="container">
        <div v-for="(task,index) in templateTasks" class="task">
            <template-task v-model="templateTasks[getTasksIndex(task.id)]" v-bind:key="index"/>
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                templateTasks:[],
            }  
        },
        props: {
            userId:{
                type:[Number,String],
                required:true
            }
        },
        watch:{
        },
        created:function(){
            
        },
        mounted:async function(){
            let result = await axios.get('/api/mytasks/',{params:{user_id:this.userId}})
            this.templateTasks = result.data.filter(task => {
                return task.is_template == true
            })
        },
        methods: {
            getTasksIndex:function(id){
                return this.templateTasks.findIndex(task => task.id == id)
            },
        }
    }
</script>
<style scoped>
    .task {
        width:100%;
        display:flex;
        align-items:center;
    }
</style>
