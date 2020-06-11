<!--ウェイティング画面の表示-->
<!--今後の改修ポイント-->
<template>
    <div v-bind:class="waitingRoot">
        <div class="waiting-background"></div>
        <div class="waiting-content">
            <div class="centering">
                <div class="waiting-circle"></div>
                <div class="waiting-message">{{message}}</div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                waitingRoot:'waiting-root',
                message:''
            }  
        },
        props: {
            
        },
        watch:{
            
        },
        created:function(){
            
        },
        mounted:function(){
            
        },
        methods: {
            enableWaiting:function(message){
                this.message = message
                this.waitingRoot = 'waiting-root waiting-root-active'
                this.$emit('input',true)
            },
            disableWaiting:function(){
                this.message = ''
                this.waitingRoot = 'waiting-root'
                this.$emit('input',false)
            }
        }
    }
</script>
<style scoped>
    .waiting-root {
        position:fixed;
        overflow-y:auto;
        z-index:20;
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
    .waiting-root-active {
        opacity:1.0;
        visibility:visible;
        transition:opacity 0.7s,visibility 0.7s;
    }
    .waiting-background {
        position:fixed;
        top:0;
        left:0;
        height:100%;
        width:100%;
        background:grey;
        opacity:0.5;
    }
    .waiting-content {
        position:fixed;
        z-index:21;
        top:40%;
    }
    .centering {
        display:flex;
        flex-direction:column;
        justify-content:center;
        align-items:center;
    }
    .waiting-circle {
        width:7em;
        height:7em;
        position:relative;
        border:1px solid white;
        background:white;
        border-radius:50%;
        animation:waiting-circle 1.5s infinite linear;
    }
    .waiting-circle::before {
        content:'';
        position:absolute;
        width:50%;
        height:50%;
        border-radius:100% 25% 0;
        background:grey;
    }
    .waiting-circle::after {
        content:'';
        position:absolute;
        left:25%;
        top:25%;
        width:50%;
        height:50%;
        border-radius: 50%;
        background:white;
    }
    @keyframes waiting-circle {
        0% {
            transform:rotate(0deg);
        }
        100% {
            transform: rotate(360deg)
        }
    }
    .waiting-message {
        margin-top:1em;
        animation:waiting-message 0.5s infinite alternate;
        color:white;
        text-align:center;
    }
    @keyframes waiting-message {
        0% {
            opacity: 0
        }
        100% {
            opacity: 1.0
        }
    }
</style>
