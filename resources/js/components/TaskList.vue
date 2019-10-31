<template>
    <div class="container">
        <div class="row justify-content-start align-items-baseline">
            <div class="card-wrapper card card-default col-md-3 m-2" v-for="(task,index) in tasks">
                <div class="completed" v-show="checkboxes[index]"></div>
                <div class="completed-mark" v-show="checkboxes[index]">completed!</div>
                <div class="card-header"><input type="checkbox" v-on:change="checkTask(task.id,index)">　//カテゴリとか//</div>
                <div class="card-body">
                    <h4 class="card-title">{{task.name}}</h4>
                    <p>{{task.overview}}</p>
                    <div class="items">
                        <p v-for="item in task.items">
                            <input type="checkbox"> {{item.name}} -- <span>{{item.memo}}</span>
                        </p> 
                    </div>
                </div>
                <div class="card-footer">締切：{{task.dead_line}}</div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                checkboxes:[],
                tasks:[],
            }  
        },
        mounted() {
            console.log('Component mounted.')
        },
        created() {
            this.fetchTasks();
        },
        methods: {
            fetchTasks: async function(){
                let result = await axios.get('/api/tasks')
                this.tasks = result.data
                for(let index of Object.keys(this.tasks)){
                    let stateIndex = this.tasks[index].states_tasks.length - 1
                    let stateId = this.tasks[index].states_tasks[stateIndex].id
                    let check = stateId == 3 ? true : false
                    this.checkboxes.push(check)
                }
                console.log(this.tasks)
            },
            checkTask:async function(taskId,index){
                if(event.target.checked == true){
                    let postObject = {
                        task_id:taskId,
                        state_id:3
                    }
                    let result = await axios.post('/api/state_task',postObject)
                    if(result.data){
                        this.checkboxes.splice(index,1,true)
                    }
                }
            }
        }
    }
</script>
<style>
    span {
        font-size:50%;
    }
    .card-wrapper{
        overflow:hidden;
    }
    .completed {
        width:110%;
        height:100%;
        position:absolute;
        left:-10%;
        z-index:2;
        background-color:grey;
        opacity:0.5;
        animation:completed 0.3s ease 0s 1;
    }
    @keyframes completed {
        0% {
            height:0%;
        }
        100% {
           height:100%; 
        }
    }
    .completed-mark {
        padding:0.5em;
        color:red;
        position:absolute;
        top:50%;
        left:25%;
        /*left:5%;*/
        border:solid white 2px;
        background-color:white;
        border-radius:0.3em;
        z-index:3;
        opacity:1.0;
        transform:rotate(10deg);
        font-weight:bold;
        animation:completed-mark-before 1s linear 0s 1,completed-mark 0.5s linear 0.5s 1;
    }
    @keyframes completed-mark-before {
        0% {
            opacity:0;
        }
        100% {
            opacity:0;
        }
    }
    @keyframes completed-mark {
        0% {
            opacity:0;
        }
        100% {
            opacity:1.0;
        }
    }
</style>
