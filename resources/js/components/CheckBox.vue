<!--長押しでチェックするチェックボックスコンポーネント-->
<!--今後の改修ポイント-->
<!--①デザイン-->
<!--②ソースコードの整理-->
<template>
    <div class="container">
        <div ref="cb" class="checkarea unchecked" v-on:mousedown="startCheck()" v-on:mouseup="stopCheck()" v-on:mouseleave="stopCheck()">
            <i class="fas fa-check"></i>
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
            startCheck:function(){
                if(event.target.classList.contains('checked') || event.target.localName == 'i')return
                let vue = this
                this.$refs.cb.classList.add('process')
                setTimeout(function(){
                    if(vue.$refs.cb.classList.contains('process')){
                        vue.$refs.cb.classList.remove('process')
                        vue.$refs.cb.classList.remove('unchecked')
                        vue.$refs.cb.classList.add('checked')
                        vue.$emit('input',true)
                    }
                },2500)
            },
            stopCheck:function(){
                if(!this.$refs.cb.classList.contains('checked')){
                    this.$refs.cb.classList.remove('process')
                    this.$emit('input',false)
                }
            },
        }
    }
</script>
<style scoped>
    .checkarea {
        position:relative;
        width:1.5em;
        height:1.5em;
        border-radius:0.2em;
        border:2px solid grey;
    }
    .unchecked {
        transform-origin:center center;
        transition:transform 5s,background 5s,border-radius 0.2s;
    }
    .unchecked i {
        display:none;
    }
    .process {
        border-radius:50%;
        background:grey;
        transform:scale(0.5,0.5);
    }
    .checked {
        background:grey;
        text-align:center;
        animation:pop-effect 0.2s 1;
    }
    @keyframes pop-effect {
        0%{
            transform:scale(1.2,1.2);
        }
        100% {
            transform:scale(1.0,1,0);
        }
    }
    .checked i {
        color:white;
    }
</style>
