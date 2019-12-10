<!--contentをまとめるコンポーネント-->
<!--ユーザーデータ等の集中管理-->
<template>
    <div class="lists-wrapper">
        <div class="contents">
            <home v-show="content == 'home'"/>
            <task-list v-show="content == 'task'" v-bind:user_id="user.id"/>
            <project-list v-show="content == 'project'"/>
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
