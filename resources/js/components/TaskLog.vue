<!--タスクデータからログ画面を構築する-->
<!--今後の改修ポイント-->
<!--①内容を変更したことを記録-->
<template>
    <div class="container">
        <div class="log-button">
            <span>{{setButtonMessage}}</span>
            <toggle-switch v-model="openLog" />
        </div>
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
                openLog:false
            }  
        },
        props: {
            task:{
                type:[String,Object],
                required:true
            }
        },
        watch:{
            openLog:function(){
                if(this.openLog){
                    let lineNum = this.task.states.length * 2.0 + 2.0
                    this.$refs.logs.style.cssText = 'height:' + lineNum + 'em; opacity:1.0; transition:all 0.3s ease'
                }else{
                    this.$refs.logs.style.cssText = 'height:0em; opacity:0; transition:all 0.3s ease'
                }
            }
        },
        created:function(){
            
        },
        mounted:function(){
            
        },
        computed:{
            setButtonMessage:function(){
                return this.openLog == false ? 'ログを表示' : 'ログを閉じる'
            }
        },
        methods: {
            setTaskStatus:function(state){
                if(state.id == 1){
                    return {color:'red'}
                }else{
                    return {color:'grey'}
                }
            },
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
    .log-button {
        display:flex;
        justify-content:center;
        align-items:center;
    }
    .log-button span {
        margin-right:1em;
    }
    .logs {
        opacity:0;
        height:0;
        margin:1em 0;
        overflow:hidden;
        padding:0 1em;
        border:1px solid grey;
    }
    .logs div {
        margin:0.5em 0;
    }
</style>
