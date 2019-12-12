<!--contentをまとめるコンポーネント-->
<!--ユーザーデータ等の集中管理-->
<template>
    <div class="contents-wrapper">
        <div class="contents">
            <home v-if="content == 'home'" v-bind:user="user"/>
            <task-list v-else-if="content == 'task'" v-bind:user_id="user.id"/>
            <project-list v-else-if="content == 'project'"/>
        </div>
    </div>
</template>

<script>
    import bus from '../eventBus.js'
    export default {
        data:function(){
            return {
                content:''
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
            bus.$on('change-content',this.changeContent)
        },
        methods: {
            changeContent:function(contentName){
                this.content = contentName
            }
        }
    }
</script>
<style scoped>
    .contents-wrapper {
        position:relative;
    }
    .contents {
        position:relative;
        width:100%;
    }
</style>
