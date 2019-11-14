<!--プロジェクト一覧ページ-->
<template>
    <div class="container">
        <modal ref="modal" v-model="modal">
            <versatile-form v-model="newProject" ref="form" table="projects" />
        </modal>
        <button class="btn btn-outline-primary mx-auto d-block" v-on:click="addProject">プロジェクトを追加</button>
        <project v-for="(project,index) in projects" v-bind:project="project" v-bind:key="index" />
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
            
        },
        created(){
            this.fetchProjects()
        },
        mounted() {
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
    
</style>
