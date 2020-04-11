<!--プロジェクト一覧ページ-->
<template>
    <div class="container">
        <modal ref="newProjectModal" v-model="newProjectModal">
            <versatile-form v-model="newProject" table="projects">
                <input v-model="newProject.name" type="text" placeholder="プロジェクト名">
                <textarea v-model="newProject.overview" placeholder="概要" />
                <span>プロジェクトの最終締切</span>
                <date-picker v-model="newProject.dead_line" />
            </versatile-form>
        </modal>
        <div class="add-project-button">
            <button class="btn btn-outline-primary d-block" v-on:click="showNewProjectModal()">プロジェクトを追加</button>
        </div>
        <div v-for="(project,index) in projects" v-bind:key="index" class="project">
            <project v-bind:project="project" />
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                newProjectModal:false,
                projects:[],
                newProject:{}
            }  
        },
        props:{
            user_id:{
                type:[String,Number],
                required:false,
            }  
        },
        watch:{
            newProject:async function(newVal,oldVal){
                if(newVal.id){
                    let result = await axios.get('/api/projects/' + newVal.id)
                    let index = this.projects.findIndex((project) => {
                        return (project.id == newVal.id)
                    })
                    if(index != -1){
                        this.projects.splice(index,1,result.data)
                    }else{
                        this.projects.push(result.data)
                    }
                }
            }
        },
        created(){
            
        },
        mounted() {
            this.fetchProjects()
        },
        methods: {
            fetchProjects: async function(){
                // プロジェクトの取得（ユーザーIDでフィルター）
                if(!this.user_id){return }
                let result = await axios.get('/api/myprojects',{
                                                params:{user_id:this.user_id,}
                                            })
                result.data.splice(0,1)
                this.projects = result.data
            },
            showNewProjectModal:function(){
                // リセット
                this.newProject = {
                    user_id:this.user_id,
                    name:'',
                    overview:'',
                    dead_line:'',
                }
                //モーダルを開く
                this.$refs.newProjectModal.openModal()
            }
        }
    }
</script>
<style scoped>
    .project {
        margin-top:0.5em;
    }
    .add-project-button {
        position:fixed;
        z-index:5;
        right:3em;
        bottom:0;
        background:white;
        padding:1em;
    }
</style>
