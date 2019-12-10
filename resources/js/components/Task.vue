<!--タスク単体を表示するデザインコンポーネント-->
<!--編集、状態の変化で上部に送出-->
<!--2 詳細ボタン-->
<!--3 編集ボタン-->
<!--4 ステッカー（新規作成にNew!、締切間近にDANGER!など-->
<!--5 ログ表示-->
<template>
    <div class="container">
        <div class="tag-list-component-wrapper">
            <!--<div class="tag-list-component">-->
            <!--    <tag-list v-bind:id="task.id" />-->
            <!--</div>-->
        </div>
        <div v-if="task.project" class="project-label">{{task.project.name}}</div>
        <div v-bind:class="wrapper_class">
            <div v-bind:class="mask_class" v-on:click="openDetail()"></div>
            <div class="state-icon" v-show="checkbox || not_started">
                <i v-show="checkbox" class="far fa-2x fa-check-circle"></i>
                <i v-show="not_started" class="fas fa-2x fa-exclamation-circle"></i>
            </div>
            <div class="task">
                <div>
                    <input ref="checkbox" class="checkbox" type="checkbox" v-on:change="checkTask(task.id)">
                    <span class="headline">{{task.name}}</span>
                </div>
                <div>
                    <span class="label">優先度</span>
                    <i class="fas fa-star" v-for="(p,pIndex) in task.priority"></i>
                    <i class="far fa-star" v-for="(np,npIndex) in (5 - task.priority)"></i>
                    <span class="label">締切</span>
                    <span>{{task.dead_line}}</span>
                </div>
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
                wrapper_class:'task-wrapper',
                mask_class:'mask',
                mask:false,
                checkbox:false,
                not_started:false,
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
                this.wrapper_class = this.detail ? 'task-wrapper detail-active' : 'task-wrapper'
            },
            checkTask:async function(taskId){
                let check = event
                if(event.target.checked == true){
                    let modifyData = {
                        state_id:3
                    }
                    let result = await axios.put('/api/tasks/' + taskId, modifyData)
                    if(result.data){
                        // チェックボックスを無効化
                        check.target.disabled = true
                        //タスク全体にマスクをかける
                        this.mask_class = 'mask mask-active'
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
                // 各種パラメータをリセット
                this.mask_class = 'mask'
                this.checkbox = false
                this.not_started = false
                
                //チェックボックスの要素を取得
                let check = this.$refs.checkbox
                
                let current_datetime = new Date()
                let task_datetime = new Date(this.task.start_date)
                if(this.task.state_id == 3){
                    this.mask_class = 'mask mask-active'
                    this.checkbox = true
                    check.checked = 'checked'
                    check.disabled = true
                }else if(current_datetime < task_datetime){
                    this.mask_class = 'mask mask-active'
                    this.not_started = true
                    check.disabled = true
                }
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
        max-height:3.0em;
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
        top:3.2em;
        left:0.2em;
        padding:0em 0.2em;
        background:burlywood;
        border-radius:0.3em;
        font-size:30%;
        overflow:hidden;
        
    }
    .tag-list-component-wrapper {
        width:100%;
        display:flex;
        justify-content:flex-end;
        flex-direction:row;
    }
    .tag-list-component {
        width:40%;
    }
    .checkbox {
        position:relative;
        z-index:3;
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
        margin-left:1em;
    }
    .detail {
        width:100%;
        padding:2em;
        background-color:gainsboro;
    }
    .detail-active {
        max-height:500px;
    }
    
    .mask {
        width:120%;
        height:120%;
        position:absolute;
        z-index:2;
        cursor:pointer;
        background-color:grey;
        /*opacity:0.5;*/
        opacity:0;
        /*animation:mask 0.3s ease 0s 1;*/
    }
    .mask-active {
        opacity:0.5;
        animation:mask 0.3s ease 0s 1;
    }
    @keyframes mask {
        0% {
            width:0%;
        }
        100% {
            width:100%; 
        }
    }
    .state-icon {
        color:white;
        position:absolute;
        top:0.5em;
        left:calc(50% - 2em);
        z-index:3;
        opacity:1.0;
        transform-origin:center;
        animation:icon-before 1s linear 0s 1,icon 0.5s linear 0.5s 1;
    }
    @keyframes icon-before {
        0% {
            opacity:0;
        }
        100% {
            opacity:0;
        }
    }
    @keyframes icon {
        0% {
            transform:rotate(0deg);
            opacity:0;
        }
        100% {
            transform:rotate(360deg);
            opacity:1.0;
        }
    }
    .item-completed {
        text-decoration:line-through;
    }
</style>
