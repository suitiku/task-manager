<!--Homeコンポーネント-->
<!--ユーザーデータ等の集中管理-->
<template>
    <div class="lists-wrapper">
        <button class="btn btn-outline-primary mx-auto d-block" v-on:click="toggleContents">
            <span>タスク／プロジェクトを切替</span>
        </button>
        <div class="contents">
            <task-list v-bind:class="task_list" v-bind:user_id="user.id"/>
            <project-list v-bind:class="project_list" />
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                task_list:'center',
                project_list:'left',
            }  
        },
        props: {
            user:{
                type:Object,
                required:false
            }  
        },
        watch:{
            
        },
        created(){
            
        },
        mounted() {
        },
        methods: {
            toggleContents:function(){
                this.task_list = this.task_list == 'center' ? 'left' : 'center'
                this.project_list = this.project_list == 'center' ? 'left' : 'center'
            }
        }
    }
</script>
<style scoped>
    button {
        margin-bottom:2em;
    }
    .lists-wrapper {
        position:relative;
    }
    .contents {
        position:relative;
        width:100%;
    }
    .left {
        position:absolute;
        left:-150%;
        top:0;
        transition:all 0.3s;
    }
    .center {
        position:absolute;
        left:0;
        top:0;
        transition:all 0.3s;
    }
</style>
