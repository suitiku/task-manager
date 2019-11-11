<!--かっこいいチェックボックス。後で作る-->
<!--１．長押しでチェック-->
<template>
    <div class="container">
        <div>
            <!--<div v-bind:class="boxClass" v-on:mousedown="mouseDown()" v-on:mouseup="mouseUp()"></div>-->
            <div class="progress"></div>
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                checked:false,
                boxClass:'checkbox',
                checkCount:0,
            }  
        },
        watch:{
            checkCount:function(){
                if(this.checkCount == 4){
                    this.checked = !this.checked
                    this.$emit('input',this.checked)
                    clearInterval(this.timer)
                    this.checkCount = 0
                }
            }
        },
        created(){
            
        },
        mounted() {
        },
        methods: {
            mouseDown:function(){
                let vm = this
                this.timer = setInterval(function(){
                    vm.countMinute()
                },1000)
                this.boxClass = 'checkbox progress checked'
            },
            mouseUp:function(){
                this.checkCount = 0 //離すとリセット
                clearInterval(this.timer)
                this.boxClass = this.checked ? 'checkbox checked' : 'checkbox'
            },
            countMinute:function(){
                this.checkCount += 1
            }
        }
    }
</script>
<style scoped>
    .checkbox {
        position:relative;
        z-index:5;
        visibility:hidden;
        background-color:white;
        width:1.2em;
        height:1.2em;
        border:1px solid black;
        border-radius:0.2em;
        transition:all 4s;
    }
    .checked {
        background-color:grey;
        border-radius:50%;
    }
    .progress {
        position:relative;
        width:2.0em;
        height:2.0em;
        /*top:-1.8em;*/
        /*left:-3px;*/
        /*border:solid 0.5em black;*/
        background-color:black;
        border-radius:50%;
        /*background-color:rgba(0,0,0,0);*/
        overflow:hidden;
        z-index:1;
    }
    .progress::before {
        content:'';
        display:block;
        position:absolute;
        top:0;
        left:-1em;
        width:2.0em;
        height:2.0em;
        background-color:#999;
        transform-origin:right 1em;
        z-index:2;
        animation: rotate-circle-left 2s linear forwards;
    }
    @keyframes rotate-circle-left {
        0%   { transform: rotate(0deg); }
        50%  { transform: rotate(0deg); }
        100% { transform: rotate(180deg); }
    }
    /*.progress {*/
    /*    animation:progress 4s linear 1;*/
    /*}*/
    /*@keyframes progress {*/
    /*    0% {*/
    /*        width:1.5em;*/
    /*        height:1.5em;*/
    /*        border-radius:50%;*/
    /*        border:none;*/
    /*    }*/
    /*    25% {*/
    /*        border-bottom:1px solid black;*/
    /*    }*/
    /*    50% {*/
    /*        border-bottom:1px solid black;*/
    /*        border-left:1px solid black;*/
    /*    }*/
    /*    75% {*/
    /*        border-bottom:1px solid black;*/
    /*        border-left:1px solid black;*/
    /*        border-top:1px solid black;*/
    /*    }*/
    /*    100% {*/
    /*        border-bottom:1px solid black;*/
    /*        border-left:1px solid black;*/
    /*        border-top:1px solid black;*/
    /*        border-right:1px solid black;*/
    /*        border:1px solid black;*/
    /*        width:1em;*/
    /*        height:1em;*/
    /*        border-radius:0.2em;*/
        /*}*/
    /*}*/
</style>
