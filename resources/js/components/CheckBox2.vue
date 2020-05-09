<!--長押しでチェックするチェックボックスコンポーネント-->
<!--今後の改修ポイント-->
<!--①デザイン-->
<template>
    <div class="container">
        <div class="checkarea" v-on:mousedown="startCheck()" v-on:mouseup="stopCheck()" v-on:mouseleave="stopCheck()">
            
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
            
        },
        watch:{
            
        },
        created:function(){
            
        },
        mounted:function(){
            
        },
        methods: {
            startCheck:async function(){
                let vue = this
                let el = event.target
                event.target.classList.add('process')
                setTimeout(function(){
                    if(el.classList.contains('process')){
                        vue.$emit('input',true)
                    }
                },4500)
            },
            stopCheck:function(){
                event.target.classList.remove('process')
                this.$emit('input',false)
            },
            checkComplete:function(){
                this.$emit('input',true)
            }
        }
    }
</script>
<style scoped>
    .checkarea {
        position:relative;
        width:1.5em;
        height:1.5em;
        border-radius:0.1em;
        border:2px solid grey;
        transform-origin:center center;
        transition:transform 5s,background 5s,border-radius 0.2s;
    }
    .process {
        border-radius:50%;
        background:grey;
        transform:scale(0.3,0.3);
    }
</style>
