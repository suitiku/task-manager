<!--テスト用コンポーネント-->
<template>
    <div class="container">
        <project-list />
        <!--<versatile-form ref="form" table="tasks" v-bind:column_override="override" />-->
        <!--<project v-bind:project="project" />-->
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                project: {},
                override:[{project_id:3}]
            }  
        },
        mounted() {
        },
        created:async function() {
            let result = await axios.get('/api/projects/3')
            this.project = result.data
        },
        methods: {
        },
    }
</script>
<style>
</style>
