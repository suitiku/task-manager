<!--テスト用コンポーネント-->
<template>
    <div class="container">
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                tasks:[],
                filteredTasks:[],
            }  
        },
        mounted() {
        },
        created:async function() {
            let result = await axios.get('/api/tasks')
            this.tasks = result.data
        },
        methods: {
        },
    }
</script>
<style>
</style>
