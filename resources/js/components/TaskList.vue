<template>
    <div class="container">
        <div class="row justify-content-start align-items-baseline">
            <!--<div class="col-md-8">-->
                <div class="card card-default col-md-3 m-2" v-for="task in tasks">
                    <div class="card-header"><input type="checkbox">　//カテゴリとか//</div>
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
            <!--</div>-->
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                test:'',
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
            }
        }
    }
</script>
<style>
    span {
        font-size:50%;
    }
</style>
