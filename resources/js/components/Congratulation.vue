<!--タスク／プロジェクト完了後に呼ばれるお疲れ様画面-->
<!--今後の改修ポイント-->
<!--１．デザイン（地味すぎる）-->
<!--２．classListでスマートなコードにする-->
<template>
    <div v-bind:class="congClass">
        <div class="cong-background" v-on:click="closeCong()"></div>
        <div v-bind:class="congContentClass">
            <div class="slot-content">{{message}}</div>
            <i class="far fa-thumbs-up"></i>
        </div>
    </div>    
</template>

<script>
    export default {
        data:function(){
            return {
                congClass:'cong-root',
                congContentClass:'cong-content',
                contentTop:0,
                message:''
            }  
        },
        watch:{
            
        },
        created(){
            
        },
        mounted() {
        },
        computed:{
            
        },
        methods: {
            openCong:function(message){
                this.message = message
                this.congContentClass = 'cong-content cong-content-active'
                this.congClass = 'cong-root-active'
            },
            closeCong:function(){
                this.message = ''
                this.contentTop = '0px'
                this.congContentClass = 'cong-content'
                this.congClass = 'cong-root'
            },
        }
    }
</script>
<style scoped>
    .cong-root {
        position:fixed;
        overflow-y:auto;
        
        z-index:9;
        top:0;
        left:0;
        right:0;
        bottom:0;
        padding:3em 0em;
        display:flex;
        justify-content:center;
        width:100%;
        transition:opacity 0.7s,visibility 0.7s;
        opacity:0;
        visibility:hidden;
    }
    .cong-root-active {
        position:fixed;
        overflow-y:auto;
        
        z-index:9;
        top:0;
        left:0;
        right:0;
        bottom:0;
        padding:3em 0em;
        display:flex;
        justify-content:center;
        width:100%;
        opacity:1.0;
        visibility:visible;
        transition:opacity 0.2s,visibility 0.2s;
    }
    .cong-content {
        position:absolute;
        top:20%;
        min-width:20em;
        min-height:5em;
        /*border-radius: 50%;*/
        display: flex;
        align-items:center;
        justify-content:center;
        overflow:hidden;
        z-index:14;
        margin-bottom:3em;
        padding:1em;
        opacity:0;
        visibility:hidden;
        border:1px solid white;
        background:white;
        outline:none;
        transition:opacity 0.7s;
    }
    .cong-content-active {
        overflow:visible;
        opacity:1.0;
        visibility:visible;
    }
    .cong-background {
        position:fixed;
        top:0;
        left:0;
        height:5000px;
        width:100%;
        background:grey;
        opacity:0.5;
        z-index:10;
    }
    .slot-content {
        display: flex;
        justify-content:center;
        align-items:center;
        font-family: 'M PLUS 1p', sans-serif;
        font-size:1.5em;
        margin:1em;
        text-align:center;
    }
    i {
        font-size:300%;
        color:orange;
        transform-origin: center center;
        animation:icon 0.5s infinite;
    }
    @keyframes icon {
        0% {
            transform: scale(1.5,1.5)
        }
        100% {
            transform: scale(1.0,1.0)
        }
    }
   
</style>
