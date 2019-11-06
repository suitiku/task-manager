<!--タスク一覧-->

<!--今後の改修ポイント-->
<!--1 並び替え（優先度、難易度、締切）-->
<!--2 詳細ボタン-->
<!--3 編集ボタン-->
<!--4 ステッカー（新規作成にNew!、締切間近にDANGER!など-->
<!--5 アニメーション周りを見直し-->
<template>
    <div class="container">
        <modal ref="modal" v-model="modal">
            <versatile-form v-model="newTask" ref="form" table="tasks" />
        </modal>
        <button class="btn btn-outline-primary mx-auto d-block" v-on:click="addTask">タスクを追加</button>
        <div v-for="(task,index) in tasks" v-bind:class="wrapperClass[index]">
            <div class="completed" v-show="checkboxes[index]"></div>
            <div class="completed-mark" v-show="checkboxes[index]">completed!</div>
            <div class="task">
                <span>
                    <input type="checkbox" v-on:change="checkTask(task.id,index)">
                    <span class="headline" v-on:click="openDetail(index)">{{task.name}}</span>
                </span>
                <span>
                    <span class="label">優先度</span>
                    <i class="fas fa-star" v-for="p in task.priority"></i>
                    <i class="far fa-star" v-for="np in (5 - task.priority)"></i>
                    <span class="label">締切</span>
                    <span>{{task.dead_line}}</span>
                </span>
            </div>
            <div v-show="details[index]" class="detail">
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
                modal:false,
                checkboxes:[],
                tasks:[],
                details:[],
                wrapperClass:[],
                newTask:{},
            }  
        },
        mounted() {
        },
        created() {
            this.fetchTasks();
        },
        watch: {
            newTask:function(newVal,oldVal){
                if(newVal.id){
                    this.tasks.push(newVal)
                    this.wrapperClass.push('task-wrapper')
                }
            }  
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
                    this.details.push(false)
                    this.wrapperClass.push('task-wrapper')
                }
                console.log(this.tasks)
            },
            openDetail:function(index){
                this.wrapperClass[index] = this.details[index] ? 'task-wrapper' : 'task-wrapper detail-active'
                this.details.splice(index,1,!this.details[index])
                
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
            setItemDisabled:function(is_checked){
                return is_checked || false
            },
            setItemClass:function(is_checked){
                return is_checked == true ? 'item-completed' : ''
            },
            addTask:function(){
                this.$refs.form.resetForm()
                this.$refs.modal.openModal()
            }
        }
    }
</script>
<style>
    .task-wrapper {
        position:relative;
        width:100%;
        max-height:2.5em;
        margin:0.5em;
        overflow:hidden;
        border:1px solid black;
        border-radius:0.2em;
        transition:max-height 2s ease;
    }
    .task {
        padding:0.5em;
        display:flex;
        justify-content:space-between;
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
        max-height:1000px;
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
