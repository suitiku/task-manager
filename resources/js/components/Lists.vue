<!--単一ファイルコンポーネントのテンプレートファイル-->
<template>
    <div class="container">
        <button class="btn btn-outline-primary mx-auto d-block" v-on:click="showTasks = !showTasks">
            <span v-show="showTasks">プロジェクトリストを表示</span>
            <span v-show="!showTasks">タスクリストを表示</span>
        </button>
        <task-list v-show="showTasks" />
        <project-list v-show="!showTasks"/>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                showTasks:false,
            }  
        },
        watch:{
            
        },
        created(){
            
        },
        mounted() {
            console.log('Component mounted.')
        },
        methods: {
            
        }
    }
</script>
<style scoped>
    button {
        margin-bottom:2em;
    }
</style>
