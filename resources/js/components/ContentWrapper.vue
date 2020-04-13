<!--contentをまとめるコンポーネント-->
<!--ユーザーデータ等の集中管理-->
<template>
    <div class="contents-wrapper">
        <div class="contents">
            <home v-if="content == 'home'" v-bind:user="user"/>
            <task-list v-else-if="content == 'task'" v-model="tasks" v-bind:userId="user.id"/>
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
                if(contentName == 'task'){
                    this.fetchTasks()
                }
                this.content = contentName
            },
            fetchTasks: async function(){
                // プロジェクトの取得（ユーザーIDでフィルター）
                if(!this.user){return }
                let result = await axios.get('/api/mytasks',{
                                                params:{user_id:this.user.id,}
                                            })
                this.tasks = result.data
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
</style>
