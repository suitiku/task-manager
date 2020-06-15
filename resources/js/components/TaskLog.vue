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
            <div v-for="(state,index) in task.states" class="log">
                <div class="state-name">
                    {{state.pivot.created_at}}
                    <i class="fas fa-power-off" v-bind:style="setTaskStatus(state)"></i>
                    （{{state.name}}）
                </div>
                <div>{{state.pivot.state_detail}}</div>
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
                this.$refs.logs.classList.toggle('logs-active')
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
        max-height:0em;
        margin:1em 0;
        overflow:hidden;
        padding:0 1em;
        border:1px solid grey;
        transition:all 0.3s ease;
    }
    .logs-active {
        max-height:500em;
        opacity:1.0;
        transition:all 0.3s ease;
    }
    .log {
        margin:0.6em 0;
        display:flex;
        align-items:center;
        border-bottom:1px dotted grey;
    }
    .state-name {
        white-space: nowrap;
        margin-left:1em;
    }
</style>
