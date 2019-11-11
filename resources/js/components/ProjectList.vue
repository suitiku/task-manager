<!--プロジェクト一覧ページ-->
<template>
    <div class="container">
        {{projects}}
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
