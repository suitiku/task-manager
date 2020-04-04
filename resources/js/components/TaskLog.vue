<!--タスクデータからログ画面を構築する-->
<!--今後の改修ポイント-->
<!--①内容を変更したことを記録-->
<!--②動きをなめらかにする-->
<!--③表示ボタンをトグルスイッチに-->
<template>
    <div class="container">
        <button class="btn btn-outline-primary mx-auto d-block" v-on:click="displayLogs()">ログを表示する</button>
        <div ref="logs" class="logs">
            <div v-for="(state,index) in task.states">
                <!--タスク作成時-->
                <div v-if="index == 0">
                    <span>
                        {{state.pivot.created_at}}
                        <i class="fas fa-power-off" v-bind:style="setTaskStatus(state)"></i>
                        <span>タスクが作成されました。</span>
                    </span>
                </div>
                <!--作成時以降-->
                <div v-else >
                    <span>
                        {{state.pivot.created_at}}
                        <i class="fas fa-power-off" v-bind:style="setTaskStatus(state)"></i>
                        {{state.pivot.state_detail}}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {

            }  
        },
        props: {
            task:{
                type:[String,Object],
                required:true
            }
        },
        watch:{
            
        },
        created:function(){
            
        },
        mounted:function(){
            
        },
        methods: {
            setTaskStatus:function(state){
                if(state.id == 1){
                    return {color:'red'}
                }else{
                    return {color:'grey'}
                }
            },
            displayLogs:function(){
                this.$refs.logs.classList.toggle('active')
            }
        }
    }
</script>
<style scoped>
    i {
        margin:0 1em;
        font-size:1.2em;
    }
    .container {
        margin:1em 0;
    }
    .logs {
        /*opacity:0;*/
        display:none;
        margin:1em 0;
        overflow:hidden;
        padding:0 1em;
        border:1px solid grey;
        transition:all 0.3s;
    }
    .active {
        /*opacity:1.0;*/
        display:block;
        transition:all 0.3s;
    }
    .logs div {
        margin:0.5em 0;
    }
</style>
