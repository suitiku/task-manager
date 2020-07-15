<!--タスク単体を表示するデザインコンポーネント-->
<!--編集、状態の変化で上部に送出-->
<!--4 ステッカー（新規作成にNew!、締切間近にDANGER!など-->
<!--5　dialogをmodalに統一-->
<template>
    <div v-if="task" class="container">
        <!--通知-->
        <notice ref="notice" />
        
        <!--おつかれ画面-->
        <congratulation ref="cong" />
        
         <!--ツールチップ-->
        <tool-tip ref="toolTip">
            <div class="tool-tip-content">
                {{toolTipContent}}
            </div>
        </tool-tip>
        
        <!--タスク削除確認モーダル-->
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
        
        <!--状態編集用モーダル-->
        <modal ref="editStatusModal" v-model="editStatusModal">
            <div class="edit-status-modal">
                <!--Reminder-->
                <p>リマインダーを追加する</p>
                <toggle-switch v-model="addReminder" />
                <versatile-form ref="newReminderForm" v-model="newReminder" table="reminders" v-show="addReminder">
                    <input v-model="newReminder.message" type="text" placeholder="コメント">
                    <div>
                        <span>画面上でお知らせする</span>
                        <input v-model="newReminder.is_screen" type="checkbox" id="screen">
                    </div>
                    <div>
                        <span>メールでお知らせする</span>
                        <input v-model="newReminder.is_mail" type="checkbox">
                    </div>
                    <span>お知らせする日時</span>
                    <date-picker v-model="newReminder.alert_datetime" />
                </versatile-form>
                
                <!--state-->
                <p>状態を変更します</p>
                <list-box v-model="selectedStatus.id" table="states" v-bind:columns="['name']" />
                <input v-model="selectedStatus.state_detail" type="text" placeholder="コメント（null可）、CTRL+エンターで状態変更" v-on:keydown="changeStatus()">
                <button class="btn btn-primary d-block mx-auto" v-on:click="changeStatus()">状態を変更</button>
            </div>
        </modal>
        
        <!--タグ編集用モーダル-->
        <modal ref="editTagModal" v-model="editTagModal">
            <p>タグの付替えを行います</p>
            <tag-list v-model="selectedTagIds" ref="tagList" v-bind:userId="task.user_id" />
        </modal>
        
        <!--編集用モーダル-->
        <modal ref="editModal" v-model="editModal">
            <versatile-form v-model="editedTask" table="tasks">
                <input v-model="editedTask.name" type="text" placeholder="タスク名">
                <textarea v-model="editedTask.overview" placeholder="概要" />
                <div class="inline">
                    <span>優先度</span>
                    <star-range v-model="editedTask.priority" />
                </div>
                <div class="inline">
                    <span>難易度</span>
                    <star-range v-model="editedTask.difficulty" />
                </div>
                <span>開始日</span>
                <date-picker ref="editTaskStartDate" v-model="editedTask.start_date" />
                <span>締切</span>
                <date-picker ref="editTaskDeadLine" v-model="editedTask.dead_line" />
                <div>    
                    <span>プロジェクトを選択してください（選択しない場合は単体のタスクとなります）</span>
                    <tag-cloud v-model="editedTask.project_id" v-bind:options="projects" v-bind:defaultValue="defaultProjectId" />
                    <input class="input-inline" ref="newProject" type="text" placeholder="プロジェクトを新規登録" v-on:keydown="createProject()" />
                </div>
            </versatile-form>
        </modal>
        
        <!--アイテムリストのアイテム削除確認モーダル-->
        <modal ref="deleteItemModal" v-model="deleteItemModal">
            <b>アイテム「{{targetItemName}}」を削除します。</b>
            <b>この処理は取り消しできません。</b>
            <b>よろしいですか？</b>
            <p>　</p>
            <div class="buttons">
                <button class="btn btn-danger d-block" v-on:click="deleteItem()">アイテムを削除</button>
                <button class="btn btn-secondary d-block" v-on:click="cancelDialog()">キャンセル</button>
            </div>
        </modal>
        
        <!--リマインダー編集モーダル-->
        <modal ref="addReminderModal" v-model="addReminderModal">
            <p>リマインダーを設定します。</p>
            <versatile-form ref="newReminderForm" v-model="newReminder" table="reminders">
                <input v-model="newReminder.message" type="text" placeholder="コメント">
                <div>
                    <span>画面上でお知らせする</span>
                    <input v-model="newReminder.is_screen" type="checkbox" id="screen">
                </div>
                <div>
                    <span>メールでお知らせする</span>
                    <input v-model="newReminder.is_mail" type="checkbox">
                </div>
                <span>お知らせする日時</span>
                <date-picker v-model="newReminder.alert_datetime" />
            </versatile-form>
        </modal>
        
        <!--メインコンテンツ-->
        <div v-bind:class="wrapper_class" v-bind:style="inactivateTask">
            <!--マスク部-->
            <div v-bind:class="maskClass"></div>
            <div class="state-icon" v-show="checked || notActive">
                <div>
                    <!--完了マーク-->
                    <i v-show="checked" class="far fa-2x fa-check-circle"></i>
                    <!--エクスクラメーション-->
                    <i v-show="notActive" class="fas fa-2x fa-exclamation-circle"></i>
                </div>
                <!--状態詳細-->
                <span>{{stateDetail}}</span>
            </div>
            <!--本体表示部分-->
            <!--ラベル部分-->
            <div class="task-label">
                <div class="headline">
                    <div class="headline-icons">
                        <!--スター-->
                        <i class="fas fa-star orange pointer clickable-layer" v-if="task.stared" v-on:click="toggleStared()"></i>
                        <i class="far fa-star translucent pointer clickable-layer" v-else v-on:click="toggleStared()"></i>
                        <!--炎上マーク（締切24時間以内のタスク）-->
                        <i class="fas fa-fire" v-bind:style="fire"></i>
                        <!--進捗メーター-->
                        <circle-meter v-bind:denominator="denominator" v-bind:numerator="numerator" />
                        <!--チェックボックス-->
                        <check-box v-model="checked" class="checkbox" v-bind:disabled="checkDisabled" />
                    </div>
                    <div class="task-headline">
                        <div v-if="task.project && task.project.name != '単体タスク'" class="project-label">{{task.project.name}}</div>
                        <span class="task-title">{{task.name}}</span>
                    </div>
                </div>
                <div class="task-buttons">
                    <!--開閉ボタン-->
                    <i v-if="!detail" class="fas fa-plus-square task-icon" v-on:click="openDetail()"></i>
                    <i v-else class="fas fa-minus-square task-icon" v-on:click="closeDetail()"></i>
                    
                    <!--編集ボタン-->
                    <i class="far fa-edit task-icon" v-on:click="showEditTaskDialog()"></i>
                    <!--タグ編集ボタン-->
                    <i class="fas fa-tag task-icon" v-on:click="showEditTagDialog()"></i>
                    <!--状態編集ボタン-->
                    <i class="far fa-check-square task-icon" v-on:click="showEditStatusDialog()"></i>
                    <!--リマインダーセットボタン-->
                    <i class="fas fa-bell task-icon" v-on:click="showEditRemindersDialog()"></i>
                    <!--削除ボタン-->
                    <i class="fas fa-trash task-icon" v-on:click="showDeleteTaskDialog()"></i>
                </div>
            </div>
            <!--詳細部分-->
            <div class="detail">
                <!--概要-->
                <p class="overview">{{task.overview}}</p>
                <!--各種パラメーター-->
                <span class="label">開始日</span>
                <span class="block">{{task.start_date}}</span>
                <span class="label">締切</span>
                <span class="block">{{task.dead_line}}</span>
                <span class="label">優先度</span>
                <star-range v-model="task.priority" />
                <span class="label">難易度</span>
                <star-range v-model="task.difficulty" />
                <!--タグ-->
                <span class="label">タグ</span>
                <div class="tags">
                    <div class="tag" v-for="(tag,index) in task.tags">
                        <i class="fas fa-tag" v-bind:style="{color:tag.color}"></i>
                        <span>{{tag.name}}</span>
                    </div>
                </div>
                <!--リマインダー-->
                <div>
                    <span class="label">リマインダー</span>
                    <p v-for="(reminder,index) in task.reminders">{{reminder.alert_datetime}}　　{{reminder.message}}</p>
                </div>
                <!--サブタスク-->
                <div class="items">
                    <span class="label">サブタスク</span>
                    <p>{{numerator}}／{{denominator}}</p>
                    <p v-for="(item,itemIndex) in task.items" v-bind:class="setItemClass(item.is_checked)" v-bind:style="inactivateItem[item.id]">
                            <!--通常表示-->
                            <span v-show="!editItemMode[itemIndex]" class="item-label">
                                <input type="checkbox" class="checkbox" v-on:change="checkItem(item)" v-bind:checked="item.is_checked" v-bind:disabled="setItemDisabled(item.is_checked)">
                                <span>{{item.name}}</span>
                            </span>
                            <!--編集用表示-->
                            <span v-show="editItemMode[itemIndex]">
                                <input class="editable" type="text" v-model="task.items[itemIndex].name" v-on:keydown="updateItem(itemIndex)">
                            </span>
                            <!--編集ボタン-->
                            <i class="far fa-edit task-icon" v-on:click="toggleEditMode(itemIndex)"></i>
                            <!--削除ボタン-->
                            <i class="fas fa-trash task-icon" v-on:click="showDeleteItemDialog(item.id,item.name)"></i>
                            <!--チェックの取り消し-->
                            <i class="fas fa-redo task-icon" v-on:click="uncheckItem(item)"></i>
                    </p>
                    <!--アイテムの追加処理セクション-->
                    <div class="editable">
                        <text-spliter v-model="items" />
                        <button class="btn btn-outline-primary mx-auto d-block" v-on:click="addItems()">アイテムを追加</button>
                    </div>
                </div>
                <!--ログ-->
                <task-log v-bind:task="task" class="editable" />
                
                <!--開閉ボタン-->
                <i class="fas fa-minus-square task-icon" v-on:click="closeDetail()"></i>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'
    export default {
        data:function(){
            return {
                task:{},
                projects:[],
                defaultProjectId:'',
                wrapper_class:'task-wrapper',
                maskClass:'mask',
                mask:false,
                checked:false,
                checkDisabled:false,
                notActive:false, //実行状態ではない場合のフラグ
                stateDetail:'', //状態詳細テキスト（実行可能状態ではない場合に表示）
                toolTipContent:'',
                detail:false,
                deleteModal:false,
                editModal:false,
                deleteItemModal:false,
                editedTask:{},
                inactivateTask:'',
                inactivateItem:[],
                targetItemId:'',
                targetItemName:'',
                editItemMode:[],
                items:[],
                editTagModal:false,
                selectedTagIds:[],
                editStatusModal:false,
                selectedStatus:{
                    id:'',
                    state_detail:''
                },
                addReminderModal:false,
                newReminder:{},
                addReminder:false,
            }  
        },
        props:{
            value: {
                type:[String,Number,Object],
                required:false,
            }
        },
        watch:{
            value:{
                handler:async function() {
                    this.setTask()
                    this.updateData()
                },
                deep:true,
            },
            //編集用モーダルを閉じたときにアップデート
            editModal:async function(newVal,oldVal){
                if(newVal == false){
                    await this.fetchTask()
                    this.updateData()
                }
            },
            // タグ編集後に閉じたときにタスクを再取得
            editTagModal:async function(newVal,oldVal){
                if(newVal == false){
                    await this.fetchTask()
                }
            },
            selectedTagIds:async function(newVal,oldVal){
                //モーダルを開いている場合だけタグの変更を有効化
                if(!this.editTagModal)return
                
                // 投入用オブジェクト作成
                let tagsObject = {
                    task_id:this.task.id,
                    tag_ids:this.selectedTagIds
                }
                
                // 書き込み
                try{
                    await axios.put('/api/tag_task',tagsObject)
                    this.$refs.notice.showNotice('タグを変更しました')
                }catch(error){
                    this.$refs.notice.showNotice('タグの変更に失敗しました')
                    console.log(error)
                }
            },
            
        },
        created:async function(){
        },
        mounted:async function(){
            // valueが設定されていない場合は終了
            if(!this.value.name)return
            
            this.setTask()
            this.updateData()
            let vue = this
            this.$watch('checked',async function(newVal,oldVal){
                if(oldVal == false && newVal == true && this.task.states[this.task.states.length -1].id != 2){
                    //サブタスクが全部終わっていない場合の警告文
                    if(this.task.items.find(el => el.is_checked == false)){
                        let result = window.confirm('サブタスクが全てチェックされていませんが完了してよろしいですか？')
                        if(!result){
                            this.checked = false
                            return
                        }
                    }
                    let postObject = {
                        task_id:vue.task.id,
                        state_id:2,
                    }
                    try{
                        await axios.post('/api/state_task',postObject)
                        vue.$refs.cong.openCong('おつかれさまです！')
                        await vue.fetchTask()
                        vue.updateData()
                        vue.$emit('input',this.task)
                    }catch(error){
                        vue.$refs.notice.showNotice('タスクの状態更新に失敗しました')
                        this.checked = false
                        console.log(error)
                    }
                }
            })
        },
        computed:{
            // 炎上中マーク
            fire:function(){
                let currentDatetime = new Date()
                let deadlineDatetime = new Date(this.task.dead_line)
                let remainingTime = deadlineDatetime - currentDatetime
                if(0 > remainingTime){ //締切を過ぎているか残り時間が12時間以内の場合に表示
                    return {fontSize:'150%',color:'red',opacity:'1.0'}
                }else if(remainingTime < 43200000){
                    return {fontSize:'150%',color:'orange',opacity:'1.0'}
                }else{
                    return {fontSize:'150%',color:'grey',opacity:'0.3'}
                }
            },
            // 進捗メーターの分母
            denominator:function(){
                if(!this.task || !this.task.items){
                    return 0
                }else{
                    return this.task.items.length
                }
            },
            // 進捗メーターの分子
            numerator:function(){
                if(!this.task || !this.task.items){
                    return 0
                }else{
                    return this.task.items.filter(el => el.is_checked).length
                }
            }
        },
        methods: {
            fetchTask:async function(){
                let result = await axios.get('/api/tasks/' + this.task.id)
                this.task = result.data
                this.$emit('input',this.task)
            },
            fetchProjects:async function(){
                // プロジェクトの取得
                let result = await axios.get('/api/myprojects',{
                                                params:{user_id:this.task.user_id,}
                                            })
                                            
                //「所属なし」プロジェクトのidを設定（一番若いやつ？）
                this.defaultProjectId = result.data[0].id
                
                //プロジェクトをセット
                this.projects = result.data
                
                // デフォルトプロジェクトを削除
                this.projects.shift()
            },
            setTask:async function(){
                //表示用データ作成
                this.task = JSON.parse(JSON.stringify(this.value))
                //編集モーダル用のデータ作成
                this.editedTask = {
                    id:this.task.id,
                    user_id:this.task.user_id,
                    project_id:this.task.project_id,
                    name:this.task.name,
                    overview:this.task.overview,
                    priority:this.task.priority,
                    difficulty:this.task.difficulty,
                    start_date:this.task.start_date,
                    dead_line:this.task.dead_line,
                    is_template:this.task.is_template,
                }
                
                //子アイテムの編集モード管理配列
                this.editItemMode = []
                for(let index in this.task.items){
                    this.editItemMode.push(false)
                }
                
                //タグリストのtagIdセット
                this.selectedTagIds = this.task.tags.map(el => el.id)
            },
            openDetail: function(){
                this.fetchTask()
                this.detail = true
                this.wrapper_class = 'task-wrapper detail-active'
            },
            closeDetail: function(){
                this.detail = false
                this.wrapper_class = 'task-wrapper'
            },
            setItemClass:function(is_checked){
                return is_checked == true ? 'item item-completed' : 'item'
            },
            setItemDisabled:function(is_checked){
                return is_checked || false
            },
            checkItem:async function(item){
                let el = event
                let modifyData = {is_checked:true}
                let postLogData = {
                    task_id:this.task.id,
                    state_id:this.task.states[this.task.states.length -1].id,
                    state_detail:'「' + item.name + '」を完了しました'
                }
                try{
                    await axios.put('/api/items/' + item.id,modifyData)
                    await axios.post('/api/state_task',postLogData)
                    el.target.disabled = true
                    el.target.parentElement.parentElement.classList.add('item-completed')
                    this.fetchTask()
                }catch(error){
                    console.log(error)
                }
            },
            uncheckItem:async function(item){
                let el = event
                let modifyData = {is_checked:false}
                let postLogData = {
                    task_id:this.task.id,
                    state_id:this.task.states[this.task.states.length -1].id,
                    state_detail:'「' + item.name + '」を差し戻しました。'
                }
                try{
                    //アイテムチェックを取り消し
                    await axios.put('/api/items/' + item.id,modifyData)
                    //ログに書き込み
                    await axios.post('/api/state_task',postLogData)
                    //タスクの更新
                    this.fetchTask()
                }catch(error){
                    console.log(error)
                }
            },
            
            updateData:function(){
                if(!this.task.id){return }
                // 各種パラメータをリセット
                this.maskClass = 'mask'
                this.notActive = false
                this.stateDetail = ''
                this.checkDisabled = false
                
                let current_datetime = new Date()
                let task_datetime = new Date(this.task.start_date)
                
                //statesの最後の状態を取得
                // let states = this.sortStatus()
                let states = this.task.states
                
                //マスク
                if(states[states.length -1].id == 2){ //完了タスク
                    this.maskClass = 'mask mask-active'
                    this.checked = true
                }else if(current_datetime < task_datetime){ //開始前タスク
                    this.maskClass = 'mask mask-active'
                    this.notActive = true
                    this.checkDisabled = true
                    this.stateDetail = '開始前タスクです'
                    this.checked = false
                }else if(states[states.length -1].id != 1){ //実行状態でも完了でもないタスク
                    this.maskClass = 'mask mask-active'
                    this.notActive = true
                    this.checkDisabled = true
                    this.stateDetail = states[states.length -1].pivot.state_detail
                    this.checked = false
                }
                
                //実行状態タスクの場合はチェックを外す
                if(states[states.length -1].id == 1){ //実行状態タスク
                    this.checked = false
                }
            },
            showDeleteTaskDialog:function(){
                this.$refs.deleteModal.openModal()
            },
            deleteTask:async function(){
                try{
                    // API経由で削除
                    // 返り値はboolean
                    let result = await axios.delete('/api/tasks/' + this.task.id)
                    // 削除が成功した場合
                    // noticeで通知
                    this.$refs.notice.showNotice('タスクを削除しました')
                    // 通知が終わった後に自らを削除（不可視化）
                    this.$emit('input','')
                    this.task = {}
                }catch(error){
                    // 削除が失敗した場合
                    // noticeで通知
                    this.$refs.notice.showNotice('タスクの削除に失敗しました')
                    console.log(error)
                }
                this.$refs.deleteModal.closeModal()
            },
            showEditTaskDialog:async function(){
                //プロジェクトを取得
                this.fetchProjects()
                
                //datepickerの初期値設定
                this.$refs.editTaskStartDate.init(this.task.start_date)
                this.$refs.editTaskDeadLine.init(this.task.dead_line)
                
                // モーダル展開
                this.$refs.editModal.openModal()
            },
            showDeleteItemDialog:function(id,name){
                this.targetItemId = id
                this.targetItemName = name
                this.$refs.deleteItemModal.openModal()
            },
            deleteItem:async function(){
                try{
                    // API経由で削除
                    // 返り値はboolean
                    let result = await axios.delete('/api/items/' + this.targetItemId)
                    // 削除が成功した場合
                    // noticeで通知
                    this.$refs.notice.showNotice('アイテムを削除しました')
                    // 通知が終わった後に自らを削除（不可視化）
                    this.inactivateItem[this.targetItemId] = {display:'none'}
                }catch(error){
                    // 削除失敗した場合
                    // noticeで通知
                    this.$refs.notice.showNotice('アイテムの削除に失敗しました')
                    console.log(error)
                }
                this.$refs.deleteItemModal.closeModal()
            },
            cancelDialog:function(){
                this.$refs.deleteModal.closeModal()
                this.$refs.editModal.closeModal()
                this.$refs.deleteItemModal.closeModal()
            },
            toggleEditMode:function(itemIndex){
                this.editItemMode.splice(itemIndex,1,!this.editItemMode[itemIndex])
            },
            updateItem:async function(itemIndex){
                if(event.keyCode == 13){ //変換終了時のenterではなく、かつenterキーの場合
                    try{
                        await axios.put('/api/items/' + this.task.items[itemIndex].id,this.task.items[itemIndex])
                        this.$refs.notice.showNotice('アイテムを変更しました')
                        this.fetchTask()
                    }catch(error){
                        this.$refs.notice.showNotice('アイテムの変更に失敗しました')
                        console.log(error)
                    }
                }
            },
            addItems:async function(){
                for(let item of this.items){
                    let postItem = {
                        task_id:this.task.id,
                        name:item,
                        is_checked:false
                    }
                    try{
                        let result = await axios.post('/api/items',postItem)
                        this.task.items.push(result.data)
                        this.$refs.notice.showNotice('タスクにアイテムを追加しました')
                    }catch(error){
                        this.$refs.notice.showNotice('アイテムの追加に失敗しました')
                        console.log(error)
                    }
                }
                this.$emit('input',this.task)
                this.items = []
            },
            showEditTagDialog:function(){
                this.$refs.editTagModal.openModal()
                this.$refs.tagList.init()
            },
            showEditStatusDialog:function(){
                // フォームをリセット
                this.$refs.newReminderForm.init()
                // Reminderのv-modelリセット
                this.newReminder = {
                    user_id:this.task.user_id,
                    task_id:this.task.id,
                    message:'',
                    is_screen:false,
                    is_mail:false,
                    alert_datetime:new Date(),
                }
                this.$refs.editStatusModal.openModal()
            },
            changeStatus:async function(){
                if(event.type != 'click' && (event.keyCode != 13 || event.ctrlKey != true))return
                
                // Statusが選択されていない
                if(!this.selectedStatus.id){
                    return 
                }else{
                    let postObject = {
                        task_id:this.task.id,
                        state_id:this.selectedStatus.id,
                        state_detail:this.selectedStatus.state_detail
                    }
                    try{
                        await axios.post('/api/state_task',postObject)
                        this.$refs.notice.showNotice('タスクステータスを更新しました')
                        await this.fetchTask()
                        this.updateData()
                        this.selectedStatus = {id:'',state_detail:''} //リセット
                        this.$emit('input',this.task)
                    }catch(error){
                        this.$refs.notice.showNotice('タスクステータスの更新に失敗しました')
                        console.log(error)
                    }
                }
            },
            createProject:async function(){
                if(event.keyCode == 13){
                    let currentDatetime = new Date()
                    let deadLine = new Date(currentDatetime.getTime() + 604800000) //デフォルトの締切は一週間後
                    let postObject = {
                        user_id:this.task.user_id,
                        name:event.target.value,
                        dead_line:deadLine.toISOString().slice(0, 19).replace('T', ' ')
                    }
                    try{
                        await axios.post('/api/projects',postObject)
                        this.$refs.notice.showNotice('プロジェクトを追加しました')
                        //プロジェクトを再取得
                        this.fetchProjects()
                        // インプットをリセット
                        this.$refs.newProject.value = ''
                    }catch(error){
                        this.$refs.notice.showNotice('プロジェクトの追加に失敗しました')
                        console.log(error)
                    }
                    
                }
            },
            showEditRemindersDialog:function(){
                // フォームをリセット
                this.$refs.newReminderForm.init()
                // v-modelリセット
                this.newReminder = {
                    user_id:this.task.user_id,
                    task_id:this.task.id,
                    message:'',
                    is_screen:false,
                    is_mail:false,
                    alert_datetime:new Date(),
                }
                this.$refs.addReminderModal.openModal()
            },
            // スターの付替
            toggleStared:async function(){
                try{
                    let result = await axios.put('/api/star/' + this.task.id)
                    let message = result.data.stared ? 'タスクにスターを付けました' : 'タスクからスターを外しました'
                    this.$refs.notice.showNotice(message)
                    this.task.stared = result.data.stared
                }catch(error){
                    this.$refs.notice.showNotice('スターの付替えに失敗しました')
                    console.log(error)
                }
            }
        }
    }
</script>
<style scoped>
    .orange {
        color: orange;
    }
    .translucent {
        opacity: 0.5;
    }
    .pointer {
        cursor:pointer;
    }
    .clickable-layer {
        z-index: 3;
    }
    .block {
        display: block;
    }
    i {
        margin:0 0.5em;
    }
    .container {
        position:relative;
        width:100%;
    }
    .task-wrapper {
        position:relative;
        width:100%;
        max-height:3.0em;
        overflow:hidden;
        border:1px solid grey;
        transition:all 1.0s ease;
    }
    .task-wrapper:hover {
        background:gainsboro;
    }
    .headline {
        display:flex;
        justify-content:flex-start;
    }
    .headline input {
        margin:0 1em;
    }
    .task-label {
        height:3.0em;
        display:flex;
        justify-content:space-between;
    }
    .project-label {
        font-size:40%;
    }
    .tags {
        display:flex;
        justify-content:flex-start;
        flex-wrap:wrap;
        margin:0.5em 0;
    }
    .tag {
        min-width:5em;
        text-align:center;
        border:1px solid grey;
        border-radius:0.3em;
        padding:0.3em;
        margin:0 0.3em;
        background:white;
        font-size:75%;
    }
    .headline-icons {
        display:flex;
        align-items:center;
        margin-left:0.2em;
    }
    .checkbox {
        position:relative;
        z-index:3;
        margin:0 0.5em;
    }
    .task-headline {
        display:flex;
        flex-direction:column;
        align-items:flex-start;
    }
    .task-title {
        font-family: "Hiragino Kaku Gothic ProN","メイリオ", sans-serif;
        font-weight:bold;
        display:flex;
        align-items:center;
        height:3em;
    }
    .task-buttons {
        margin-right:0.5em;
        display:flex;
        align-items:center;
        justify-content:flex-end;
        min-width:18em;
    }
    .dead-line {
        margin:0 0.5em;
        color:grey;
        font-size:75%;
    }
    .task-icon {
        cursor:pointer;
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
    }
    .overview {
        white-space:pre-wrap;
        word-wrap:break-word;
    }
    .detail-active {
        max-height:2000px;
        transition:all 1.0s ease;
    }
    .editable {
        position:relative;
        z-index:3;
    }
    .mask {
        width:120%;
        height:3.0em;
        position:absolute;
        z-index:2;
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
        display:flex;
        align-items:center;
        color:white;
        position:absolute;
        top:0.5em;
        left:calc(50% - 2em);
        z-index:3;
        opacity:1.0;
    }
    .state-icon div {
        transform-origin:center;
        animation:icon-before 1s linear 0s 1,icon 0.5s linear 0.5s 1;
    }
    .state-icon span {
        margin-left:1em;
        font-size:75%;
        animation:none;
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
    .item {
        display:flex;
        align-items:center;
    }
    .item-completed {
        text-decoration:line-through;
    }
    .item-label {
        display:flex;
        align-items:center;
    }
    .tool-tip-content{
        display:flex;
        align-items:center;
    }
    .edit-status-modal * {
        margin:1em 0;
    }
    .edit-status-modal input {
        text-align:center;
        width:100%;
        display:block;
        margin:0.5em;
        padding:0.3em;
        border:1px solid grey;
        border-radius:0.3em;
    }
</style>
