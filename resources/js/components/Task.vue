<!--タスク単体を表示するデザインコンポーネント-->
<!--編集、状態の変化で上部に送出-->
<!--2 詳細ボタン-->
<!--3 編集ボタン-->
<!--4 ステッカー（新規作成にNew!、締切間近にDANGER!など-->
<template>
    <div class="container">
        <div v-if="task.project" class="project-label">{{task.project.name}}</div>
        <div v-bind:class="wrapper">
            <div class="completed" v-show="checkbox"></div>
            <div class="completed-mark" v-show="checkbox">completed!</div>
            <div class="task">
                <span>
                    <input type="checkbox" v-on:change="checkTask(task.id)">
                    <span class="headline" v-on:click="openDetail()">{{task.name}}</span>
                </span>
                <span>
                    <span class="label">優先度</span>
                    <i class="fas fa-star" v-for="p in task.priority"></i>
                    <!--<i class="far fa-star" v-for="np in (5 - task.priority)"></i> なんかエラー出るから後で直す-->
                    <span class="label">締切</span>
                    <span>{{task.dead_line}}</span>
                </span>
            </div>
            <div class="detail">
                <p>{{task.overview}}</p>
                <div class="items">
                    <p v-for="item in task.items" v-bind:class="setItemClass(item.is_checked)">
                        <input type="checkbox" v-on:change="checkItem(item.id)" v-bind:checked="item.is_checked" v-bind:disabled="setItemDisabled(item.is_checked)"> {{item.name}} -- <span>{{item.memo}}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                wrapper:'task-wrapper',
                checkbox:false,
                detail:false,
            }  
        },
        props:{
            task: {
                type:Object,
                default:{},
                required:true,
            }
        },
        watch:{
            task:function(){
                this.updateData()
            }
        },
        created:async function(){
            
        },
        mounted() {
            this.updateData()
        },
        computed:{
            
        },
        methods: {
            openDetail: function(){
                this.detail = !this.detail
                this.wrapper = this.detail ? 'task-wrapper detail-active' : 'task-wrapper'
            },
            checkTask:async function(taskId){
                if(event.target.checked == true){
                    let postObject = {
                        task_id:taskId,
                        state_id:4
                    }
                    let result = await axios.post('/api/state_task',postObject)
                    if(result.data){
                        this.checkbox = true
                        //状態が変化したらtasks/idを再取得して送出
                        let taskResult = await axios.get('/api/tasks/' + taskId)
                        this.$emit('input',taskResult.data)
                    }
                }
            },
            setItemClass:function(is_checked){
                return is_checked == true ? 'item-completed' : ''
            },
            setItemDisabled:function(is_checked){
                return is_checked || false
            },
            checkItem:async function(itemId){
                let el = event
                let modifyData = {is_checked:true}
                let result = await axios.put('/api/items/' + itemId,modifyData)
                if(result.data){
                    el.target.disabled = true
                    el.target.parentElement.className = 'item-completed'
                }
            },
            updateData:function(){
                let stateIndex = this.task.states_tasks.length - 1
                let stateId = this.task.states_tasks[stateIndex].id
                this.checkbox = stateId == 4 ? true : false
            }
        }
    }
</script>
<style scoped>
    .container {
        position:relative;
        width:100%;
    }
    .task-wrapper {
        position:relative;
        width:100%;
        max-height:2.5em;
        overflow:hidden;
        border:1px solid black;
        border-radius:0.2em;
        transition:all 1.5s ease;
    }
    .task {
        padding:0.5em;
        display:flex;
        justify-content:space-between;
    }
    .project-label {
        position:absolute;
        z-index:2;
        height:1.5em;
        top:-0.3em;
        left:0.2em;
        padding:0em 0.2em;
        background:burlywood;
        border-radius:0.3em;
        font-size:30%;
        overflow:hidden;
        
    }
    .headline {
        font-weight:bold;
        cursor:pointer;
    }
    .label {
        font-size:50%;
        border-radius:0.2em;
        background-color:gainsboro;
        padding:0.1em;
        margin:0 0.2em;
    }
    .detail {
        width:100%;
        padding:2em;
        background-color:gainsboro;
    }
    .detail-active {
        max-height:500px;
    }
    
    .completed {
        width:120%;
        height:120%;
        position:absolute;
        z-index:2;
        background-color:grey;
        opacity:0.5;
        animation:completed 0.3s ease 0s 1;
    }
    @keyframes completed {
        0% {
            width:0%;
        }
        100% {
            width:100%; 
        }
    }
    .completed-mark {
        padding:0.4em;
        color:red;
        position:absolute;
        /*top:50%;*/
        left:calc(50% - 2em);
        border:solid white 2px;
        background-color:white;
        /*border-radius:0.3em;*/
        z-index:3;
        opacity:1.0;
        /*transform:rotate(10deg);*/
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
    .item-completed {
        text-decoration:line-through;
    }
</style>
