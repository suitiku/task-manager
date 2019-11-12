<!--プロジェクト一覧ページ-->
<template>
    <div class="container">
       <project v-for="(project,index) in projects" v-bind:project="project" v-bind:key="index" />
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                projects:[],
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
            }
        }
    }
</script>
<style scoped>
    
</style>
