<!--タスク単体を表示するデザインコンポーネント-->
<!--編集、状態の変化で上部に送出-->
<!--1 削除処理-->
<!--3 編集処理-->
<!--4 ステッカー（新規作成にNew!、締切間近にDANGER!など-->
<!--5 ログ表示-->
<template>
    <div class="container">
        <!--通知-->
        <notice ref="notice"></notice>
        
        <!--削除確認モーダル-->
        <modal ref="deleteModal" v-model="deleteModal">
            <b>タスク「{{task.name}}」を削除します。</b>
            <b>この処理は取り消しできません。</b>
            <b>よろしいですか？</b>
            <p>　</p>
            <div class="buttons">
                <button class="btn btn-danger d-block" v-on:click="deleteTask()">タスクを削除</button>
                <button class="btn btn-secondary d-block" v-on:click="cancelDialog()">キャンセル</button>
            </div>
        </modal>
        
        <!--編集用モーダル-->
        <modal ref="editModal" v-model="editModal">
            
        </modal>
        
        <div v-bind:class="wrapper_class" v-bind:style="inactivateTask">
            <div v-bind:class="mask_class" v-on:click="openDetail()"></div>
            <div class="state-icon" v-show="checkbox || not_started">
                <i v-show="checkbox" class="far fa-2x fa-check-circle"></i>
                <i v-show="not_started" class="fas fa-2x fa-exclamation-circle"></i>
            </div>
            <div class="task-label">
                <div class="headline">
                    <input ref="checkbox" class="checkbox" type="checkbox" v-on:change="checkTask(task.id)">
                    <div>
                        <div v-if="task.project && task.project.id != 1" class="project-label">{{task.project.name}}</div>
                        <span class="task-headline">{{task.name}}</span>
                    </div>
                </div>
                <div>
                    <!--締切-->
                    <i class="far fa-clock"></i>
                    <span class="dead-line">{{task.dead_line}}</span>
                    
                    <!--タグがある場合はタグアイコン-->
                    <i class="fas fa-tag" v-bind:style="setTagIcon(task)"></i>
                    <!--編集ボタン-->
                    <i class="far fa-edit task-icon"></i>
                    <!--削除ボタン-->
                    <i class="fas fa-trash task-icon" v-on:click="showDeleteTaskDialog()"></i>
                </div>
            </div>
            <div class="detail">
                <!--各種パラメーター-->
                <span class="label">優先度</span>
                <i class="fas fa-star" v-for="(p,pIndex) in task.priority"></i>
                <i class="far fa-star" v-for="(np,npIndex) in (5 - task.priority)"></i>
                <span class="label">難易度</span>
                <i class="fas fa-star" v-for="(p,pIndex) in task.difficulty"></i>
                <i class="far fa-star" v-for="(np,npIndex) in (5 - task.difficulty)"></i>
                <!--タグ-->
                <div class="tags">
                    <div class="tag" v-for="(tag,index) in task.tags">
                        <i class="fas fa-tag" v-bind:style="{color:tag.color}"></i>
                        <span>{{tag.name}}</span>
                    </div>
                </div>
                <!--概要-->
                <p>{{task.overview}}</p>
                <!--子アイテム-->
                <div class="items">
                    <p v-for="item in task.items" v-bind:class="setItemClass(item.is_checked)">
                        <input type="checkbox" v-on:change="checkItem(item.id)" v-bind:checked="item.is_checked" v-bind:disabled="setItemDisabled(item.is_checked)"> {{item.name}} -- <span>{{item.memo}}</span>
                    </p>
                </div>
                <!--ログ-->
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
                deleteModal:false,
                editModal:false,
                inactivateTask:'',
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
            task:{
                handler:function(){
                    this.updateData()
                },
                deep:true
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
            setTagIcon:function(task){
                return task.tags != '' ? {visibility:'visible'} : {visibility:'hidden'}
            },
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
                        //状態だけ完了のものに変更。親コンポーネント（TaskList.vue）のtasksの値は変更されていないので注意。sort or filterでfetchする。
                        // チェックボックスを無効化
                        check.target.disabled = true
                        //タスク全体にマスクをかける
                        this.mask_class = 'mask mask-active'
                        this.checkbox = true
                        
                        //Project.vueとかでv-modelが設定されているので上部に送出
                        let taskResult = await axios.get('api/tasks/' + taskId)
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
                check.checked = false
                check.disabled = false
                
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
            },
            showDeleteTaskDialog:function(){
                this.$refs.deleteModal.openModal()
            },
            deleteTask:async function(){
                // API経由で削除
                // 返り値はboolean
                let result = await axios.delete('/api/tasks/' + this.task.id)
                if(result.data){
                    // 削除が成功した場合
                    // noticeで通知
                    this.$refs.notice.showNotice('タスクを削除しました')
                    // 通知が終わった後に自らを削除（不可視化）
                    this.inactivateTask = {display:'none'}
                }else{
                    // 削除が失敗した場合
                    // noticeで通知
                    this.$refs.notice.showNotice('タスクの削除に失敗しました')
                }
                this.$refs.deleteModal.closeModal()
            },
            cancelDialog:function(){
                this.$refs.deleteModal.closeModal()
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
        /*border-radius:0.2em;*/
        transition:all 1.0s ease;
    }
    .headline {
        display:flex;
        justify-content:flex-start;
        align-items:center;
    }
    .headline input {
        margin-right:1em;
    }
    .task-label {
        height:3.0em;
        padding:0.5em;
        display:flex;
        align-items:center;
        justify-content:space-between;
    }
    .project-label {
        font-size:40%;
    }
    .tags {
        display:flex;
        justify-content:flex-start;
        margin:0.5em 0;
    }
    .tag {
        border:1px solid grey;
        border-radius:0.3em;
        padding:0.3em;
        margin:0 0.3em;
        background:white;
        font-size:75%;
    }
    .checkbox {
        position:relative;
        z-index:3;
    }
    .task-headline {
        font-weight:bold;
    }
    .dead-line {
        margin-right:1em;
        color:grey;
        font-size:75%;
    }
    .task-icon {
        cursor:pointer;
        margin-left:0.5em;
        position:relative;
        z-index:3;
        transition:all 0.3s;
    }
    .task-icon:hover {
        color:salmon;
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
        /*background-color:gainsboro;*/
        
    }
    .detail-active {
        max-height:500px;
        transition:all 1.0s ease;
    }
    
    .mask {
        width:120%;
        height:120%;
        position:absolute;
        z-index:2;
        cursor:pointer;
        background-color:grey;
        opacity:0;
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
