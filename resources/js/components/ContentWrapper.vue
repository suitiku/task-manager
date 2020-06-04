<!--contentをまとめるコンポーネント-->
<!--ユーザーデータ等の集中管理-->
<template>
    <div class="contents-wrapper">
        <div class="contents">
            <home v-show="content == 'home'" v-bind:user="user"/>
            <task-list v-show="content == 'task'" v-model="tasks" v-bind:userId="user.id"/>
            <project-list v-show="content == 'project'" v-bind:user_id="user.id" />
            <setting v-if="content == 'settings'" v-bind:user_id="user.id" />
            <div class="spacer"></div>
        </div>
    </div>
</template>

<script>
    import bus from '../eventBus.js'
    export default {
        data:function(){
            return {
                content:'',
                tasks:[]
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
        mounted:async function() {
            // イベントバスを設定
            bus.$on('change-content',this.changeContent)
        },
        methods: {
            changeContent:function(contentName){
                this.content = contentName
            },
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
    /*下に余白*/
    .spacer {
        height:5em;
    }
    
@media screen and (max-width:480px) { 
    .contents-wrapper {
        width:100%;
    }
}
@media screen and (min-width:480px){
    .contents-wrapper {
        width:80%;
    }
}
</style>
