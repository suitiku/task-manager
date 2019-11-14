<!--テスト用コンポーネント-->
<template>
    <div class="container">
        <list-box v-model="range" table="projects" v-bind:columns="columns" is_multiple />
        {{range}}
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                range:'',
                project: {},
                override:[{project_id:3}],
                columns: ['name','dead_line']
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
