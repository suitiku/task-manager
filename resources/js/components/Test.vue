<!--テスト用コンポーネント-->
<template>
    <div class="container">
        <!--<tag-list v-bind:tags="task.tags" v-bind:id="task.id" />-->
        <task v-bind:task="task" />
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                task:{}
            }  
        },
        mounted() {
        },
        created:async function() {
            let result = await axios.get('/api/tasks/1')
            this.task = result.data
        },
        methods: {
        },
    }
</script>
<style>
    .space {
        width:100%;
        height:50px;
    }
</style>
