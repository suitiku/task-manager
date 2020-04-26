<!--プロジェクト単体表示コンポーネント-->
<!--今後の改修ポイント-->
<!--①更新系の際に親コンポーネント（ProjectList.vue）があるのが前提となっているのでスタンドアローンでも動くように改修する-->
<template>
    <div class="container">
        <!--通知-->
        <notice ref="notice" />
        
        <!--編集用モーダル-->
        <modal ref="editModal" v-model="editModal">
            <versatile-form v-model="editedProject" table="projects">
                <input v-model="editedProject.name" type="text" placeholder="プロジェクト名">
                <textarea v-model="editedProject.overview" placeholder="概要" />
                <span>締切</span>
                <date-picker ref="editProjetcDeadLine" v-model="editedProject.dead_line" />
            </versatile-form>
        </modal>
        
        <!--削除確認モーダル-->
        <modal ref="deleteModal" v-model="deleteModal">
            <b>プロジェクト「{{project.name}}」を削除します。</b>
            <b>この処理は取り消しできません。</b>
            <b>よろしいですか？</b>
            <p>　</p>
            <div class="buttons">
                <button class="btn btn-danger d-block" v-on:click="deleteProject()">プロジェクトを削除</button>
                <button class="btn btn-secondary d-block" v-on:click="closeModal()">キャンセル</button>
            </div>
        </modal>
        
        <!--タスクコピー確認用モーダル-->
        <modal ref="copyModal" v-model="copyModal">
            <!--<div>-->
                <p>タスク「{{project.name}}」をコピー／テンプレートにします。</p>
                <div>
                    <button type="button" class="btn btn-primary" v-on:click="copyProject()">コピーする</button>
                    <!--<button type="button" class="btn btn-primary" v-on:click="templateProject()">テンプレートにする</button>-->
                    <button type="button" class="btn btn-secondary" v-on:click="closeModal()">キャンセル</button>
                </div>
            <!--</div>-->
        </modal>
        
        <!--表示部-->
        <div class="project-wrapper">
            <div class="info">
                <div>
                    <i class="far fa-clock"></i>　{{project.dead_line}}
                </div>
                <div>
                    <!--編集ボタン-->
                    <i class="far fa-edit icon" v-on:click="showEditProjectModal()"></i>
                    <!--削除ボタン-->
                    <i class="fas fa-trash icon" v-on:click="showDeleteProjectModal()"></i>
                    <!--コピーボタン-->
                    <i class="fas fa-copy" v-on:click="showCopyProjectModal()"></i>
                </div>
            </div>
            <div class="project-content-wrapper">
                <h3>{{project.name}}</h3>
                <progress-bar v-bind:denominotor="denominotor" v-bind:numerator="numerator" />
                <button class="btn btn-outline-primary mx-auto d-block" v-on:click="openDetail">
                    <span v-if="detail == 'project-detail-close'">プロジェクト詳細を表示する</span>
                    <span v-else>詳細を隠す</span>
                </button>
                <div v-bind:class="detail">
                    <!--関係者リスト-->
                    
                    <!--概要-->
                    <div class="overview">{{project.overview}}</div>
                    
                    <!--タスクリスト-->
                    <task-list v-model="tasks" v-bind:projectId="project.id" v-bind:userId="project.user_id" />
                    
                    <!--ガントチャート-->
                    
                    <!--ログ-->
                    
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                taskIds:[],
                tasks:[], //変更監視用
                denominotor:0,
                numerator:0,
                detail:'project-detail-close',
                editModal:false,
                editedProject:{},
                deleteModal:false,
                copyModal:false,
                
            }  
        },
        props: {
            project:{
                type:Object,
                default:{},
                required:true,
            }  
        },
        watch:{
            editModal:async function(newVal,oldVal){
                if(newVal == false){
                    await this.$parent.fetchProjects()
                }  
            },
            'project.tasks':{
                handler:function(){
                    this.setTasks()
                },
                deep:true
            },
            tasks:{
                handler:async function(){
                    //下部のTaskListが更新された場合、プロジェクトが変更されたものがあったばあい、ProjectList.vueのfetchProjectsを実行して画面を更新
                    let result = this.tasks.some(task => {
                        return task.project_id != this.project.id && !task.is_template
                    })
                    if(result){
                        await this.$parent.fetchProjects()
                    }
                    // プログレスバーをセット
                    this.setProgress() 
                },
                deep:true
            }
        },
        created(){
            
        },
        mounted() {
            this.setTasks()
            this.setProgress()
        },
        computed:{
            override:function(){
                return [{project_id:this.project.id}]
            }
        },
        methods: {
            setTasks:async function(){
                if(!this.project.tasks)return 
                
                //プロジェクトに付随するタスクをセット
                let allTasks = JSON.parse(JSON.stringify(this.project.tasks))  
                this.tasks = allTasks.filter(task => {
                    return task.is_template == false
                })
                
                // ユーザーの持つテンプレート化されたタスクを取得
                let result = await axios.get('/api/mytasks',{
                                                params:{user_id:this.project.user_id,}
                                            })
                let templateTasks = result.data.filter(task => {
                    return task.is_template == true
                })
                
                this.tasks = Array.from(new Set(this.tasks.concat(templateTasks)))
            },
            openDetail:function(){
                this.detail = this.detail == 'project-detail-close' ? 'project-detail-open' : 'project-detail-close'  
            },
            setProgress:function(){
                let meterTasks = this.tasks.filter(task => {
                    return task != '' && !task.is_template
                })
                if(meterTasks.length == 0){
                    this.denominotor = 0
                    return
                }
                
                this.denominotor = meterTasks.length
                let numerator = 0
                for(let task of meterTasks){
                    // 最新の状態を取得
                    let lastTaskStatusId = task.states[task.states.length -1].id
                    if(lastTaskStatusId == 2){
                        numerator ++
                    }
                }
                this.numerator = numerator
            },
            showEditProjectModal:function(){
                //編集モーダル用のデータ作成
                this.editedProject = {
                    id:this.project.id,
                    user_id:this.project.user_id,
                    name:this.project.name,
                    overview:this.project.overview,
                    dead_line:this.project.dead_line,
                }
                // datepickerの初期値設定
                this.$refs.editProjetcDeadLine.init(this.project.dead_line)
                
                //モーダル展開
                this.$refs.editModal.openModal()
            },
            showDeleteProjectModal:function(){
                this.$refs.deleteModal.openModal()
            },
            closeModal:function(){
                this.$refs.deleteModal.closeModal()
                this.$refs.copyModal.closeModal()
            },
            deleteProject:async function(){
                try{
                    this.$refs.deleteModal.closeModal()
                    await axios.delete('/api/projects/' + this.project.id)
                    this.$refs.notice.showNotice('プロジェクトを削除しました')
                    this.$parent.fetchProjects()
                }catch(error){
                    this.$refs.notice.showNotice('プロジェクトの削除に失敗しました')
                    console.log(error)
                }
            },
            showCopyProjectModal:function(){
                this.$refs.copyModal.openModal()
            },
            copyProject:async function(){
                console.log(this.project)
                // let currentDatetime = new Date()
                // let postProject = {
                //     user_id:this.project.user_id,
                //     name:this.project.name,
                //     overview:this.project.overview,
                //     dead_line:this.project.dead_line
                // }
                // let postTasks = []
                // for(let task of this.project.tasks){
                //     postTasks.push({
                        
                //     })
                // }
                try{
                    
                }catch(error){
                    this.$refs.notice.showNotice('プロジェクトのコピーに失敗しました')
                    console.log(error)
                }
            }
        }
    }
</script>
<style scoped>
    .project-wrapper {
        position:relative;
        width:100%;
        border:3px solid grey;
        border-radius:0.2em;
    }
    .project-content-wrapper {
        position:relative;
        padding:1em;
        overflow:hidden;
    }
    .info {
        width:100%;
        margin-bottom:0.5em;
        padding:0.1em 2em;
        background:orange;
        /*color:white;*/
        /*font-size:60%;*/
        display:flex;
        justify-content:space-between;
    }
    .project-detail-close {
        max-height:0;
        transition:all 0.3s;
    }
    .project-detail-open {
        position:relative;
        max-height:1000px;
        transition:all 0.3s;
    }
    .overview {
        margin:0.5em 1em;
        padding:1em;
        border-radius:0.2em;
        background:gainsboro;
    }
    .tasks {
        position:relative;
        width:100%;
        margin:1em 0;
    }
    button {
        margin:0.5em 0;
    }
    .icon {
        cursor:pointer;
    }
</style>
