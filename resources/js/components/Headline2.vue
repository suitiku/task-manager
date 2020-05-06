<!--見出しコンポーネント-->
<template>
    <!--<div class="container">-->
    <h2><slot></slot></h2>
    <!--</div>-->
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
            
        }
    }
</script>
<style scoped>
    h2 {
        position:relative;
        left:-30%;
        padding:0.2em 0 0.2em calc(10% + 2em);
        margin:1em 0;
        border-radius:0.2em;
        background:orange;
        color:white;
        font-weight:bold;
        font-style:oblique;
        font-family: "游ゴシック体", YuGothic, "游ゴシック Medium", "Yu Gothic Medium";
    }
</style>
