<!--タスク一覧表示用コンポーネント-->
<!--今後の改修ポイント-->
<template>
    <div class="container">
        <modal ref="modal" v-model="modal">
            <versatile-form v-model="newTask" ref="form" table="tasks" v-bind:foreign_keys="foreign_keys"/>
        </modal>
        <button class="btn btn-outline-primary mx-auto d-block" v-on:click="addTask">タスクを追加</button>
        <!--フィルター-->
        <div class="filter-box">
            <div class="filter">
                <span>状態　</span>
                <label><input type="checkbox" v-model="filtered_states" value="1">実行中</label>
                <label><input type="checkbox" v-model="filtered_states" value="2">対応待ち</label>
                <label><input type="checkbox" v-model="filtered_states" value="3">完了</label>
                <label><input type="checkbox" v-model="filtered_states" value="4">タスク移動</label>
                <label><input type="checkbox" v-model="filtered_states" value="5">未完了</label>
            </div>
            <div class="fileter">
                <span>時間　</span>
                <label><input type="radio" v-model="filtered_date_time" value="1">開始前</label>
                <label><input type="radio" v-model="filtered_date_time" value="2">締切1日前</label>
                <label><input type="radio" v-model="filtered_date_time" value="3">締切12時間前</label>
                
            </div>
            <div class="filter">
                <span>優先度</span>
                <star-range v-model="filtered_priority" />
            </div>
            <div class="filter">
                <span>難易度</span>
                <star-range v-model="filtered_difficulty" />
            </div>
            <div class="filter">
                <span>タグでフィルター</span>
                <tag-cloud v-model="filtered_tags" v-bind:options="tags" multiple />
            </div>
        </div>
        <!--ソート-->
        <div class="sortBox">
            <button class="btn btn-outline-primary mx-auto d-block" v-on:click="sortTask('dead_line')">締切</button>
            <button class="btn btn-outline-primary mx-auto d-block" v-on:click="sortTask('priority')">優先度</button>
            <button class="btn btn-outline-primary mx-auto d-block" v-on:click="sortTask('difficulty')">難易度</button>
        </div>
        <task v-for="(task,index) in tasks" v-bind:task="task" v-bind:key="index"/>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                modal:false,
                // allTasks:[], //データベースから取得したリスト全体
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
                taskFilter:'incomplete',
                tags:[],
                filtered_tags:[],
                filtered_states:[],
                filtered_date_time:[],
                filtered_priority:0,
                filtered_difficulty:0,
            }  
        },
        props:{
            user_id:{
                type:[String,Number],
                required:false,
            }  
        },
        mounted() {
        },
        created() {
            this.fetchTasks()
            this.fetchTags()
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
            filtered_tags:async function(){
                await this.fetchTasks()
                if(this.filtered_tags.length == 0){return } //選択されていない場合は全表示
                let result = this.tasks.filter(task => {
                    for(let index of Object.keys(task.tags)){
                        if(this.filtered_tags.indexOf(task.tags[index].id) != -1){
                            return task
                        }
                    }
                })
                this.tasks = result
            },
            filtered_states:async function(){
                await this.fetchTasks()
                if(this.filtered_states.length == 0){return }
                let result = this.tasks.filter(task => {
                    return (this.filtered_states.indexOf(String(task.state_id)) != -1)
                })
                this.tasks = result
            },
            filtered_date_time:async function(){
                await this.fetchTasks()
                let current_date_time = new Date()
                if(this.filtered_date_time.length == 0){return }
                let result = this.tasks.filter(task => {
                    let to_dead_line = new Date(task.dead_line) - current_date_time.getTime()
                    let from_start_date = new Date(task.start_date) - current_date_time.getTime()
                    switch(this.filtered_date_time){
                        case '1':
                            if(from_start_date > 0){
                                return task
                            }
                            break
                        case '2':
                            if(to_dead_line < 86400000){ //24時間前
                                return task
                            }
                            break
                        case '3':
                            if(to_dead_line < 43200000){ //12時間前
                                return task
                            }
                            break
                    }
                })
                this.tasks = result
            },
            filtered_priority:async function(){
                await this.fetchTasks()
                if(this.filtered_priority == 0){return }
                let result = this.tasks.filter(task => {
                    return task.priority >= this.filtered_priority
                })
                this.tasks = result
            },
            filtered_difficulty:async function(){
                await this.fetchTasks()
                // this.tasks = this.allTasks
                if(this.filtered_difficulty == 0){return }
                let result = this.tasks.filter(task => {
                    return task.difficulty >= this.filtered_difficulty
                })
                this.tasks = result
            },
        },
        methods: {
            fetchTasks: async function(){
                // タスクの取得（ユーザーIDでフィルター）
                if(!this.user_id){return }
                let result = await axios.get('/api/mytasks',{
                                                params:{user_id:this.user_id,}
                                            })
                this.tasks = result.data
            },
            fetchTags: async function(){
                 // タグの取得
                let result = await axios.get('/api/tags')
                let tagsResult = result.data
                for(let index of Object.keys(tagsResult)){
                    this.tags.push({label:tagsResult[index].name,value:tagsResult[index].id})
                }
            },
            addTask:function(){
                this.$refs.form.resetForm()
                this.$refs.modal.openModal()
            },
            sortTask:async function(key){
                await this.fetchTasks()
                this.tasks.sort((a,b) => {
                    return (a[key] < b[key]) ? -1 : 1
                })
            },
            filterTasksByTags:function(){
                
            }
        }
    }
</script>
<style scoped>
    .container {
        position:relative;
        width: 100%;
    }
    .sortBox {
        margin:1em;
        display:flex;
        justify-content:center;
    }
    .filter-box {
        width:100%;
        margin:1em;
        padding:1em;
        border:2px solid grey;
    }
    .filter {
        display:flex;
    }
    span {
        margin-right:1em;
    }
    input {
        margin:0 0.3em;
    }
</style>
