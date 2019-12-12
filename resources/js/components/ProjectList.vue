<!--プロジェクト一覧ページ-->
<template>
    <div class="container">
        <modal ref="modal" v-model="modal">
            <versatile-form v-model="newProject" ref="form" table="projects" />
        </modal>
        <div class="add-project-button">
            <button class="btn btn-outline-primary d-block" v-on:click="addProject">プロジェクトを追加</button>
        </div>
        <div v-for="(project,index) in projects" v-bind:key="index" class="project">
            <project v-if="project.id != 1" v-bind:project="project" />
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                modal:false,
                projects:[],
                newProject:{}
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
                let result = await axios.get('/api/projects')
                this.projects = result.data
            },
            addProject:function(){
                this.$refs.form.resetForm()
                this.$refs.modal.openModal()
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
