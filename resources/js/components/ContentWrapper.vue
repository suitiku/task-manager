<!--contentをまとめるコンポーネント-->
<!--ユーザーデータ等の集中管理-->
<template>
    <div class="contents-wrapper">
        <div class="contents">
            <home v-if="content == 'home'" v-bind:user="user"/>
            <task-list v-else-if="content == 'task'" v-bind:user_id="user.id"/>
            <project-list v-else-if="content == 'project'" v-bind:user_id="user.id" />
            <div class="spacer"></div>
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
        mounted:async function() {
            // イベントバスを設定
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
    /*下に余白*/
    .spacer {
        height:5em;
    }
</style>
