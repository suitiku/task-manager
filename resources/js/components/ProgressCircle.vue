<!--サークルプログレスバー-->
<template>
    <div class="container">
        <div ref="circle" class="circle" v-bind:style="circle_animation"></div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                circle_animation:{
                    '@keyframes circle-right': {
                        '0%': {
                            transform:'rotate(0deg)',
                            background:'grey'
                        },
                        '50%': {
                            transform:'rotate(180deg)',
                            background:'grey'
                        },
                        '50.01%': {
                            transform:'rotate(360deg)',
                            background:'inherit'
                        },
                        '100%': {
                            transform:'rotate(360deg)',
                            background:'inherit'
                        }
                    }
                }
            }  
        },
        props: {
            
        },
        watch:{
            
        },
        created:function(){
            
        },
        mounted:function(){
            let circle = this.$refs.circle
            let before = window.getComputedStyle(circle,'::before')
            console.log(before)
        },
        computed:{
        },
        methods: {
            
        }
    }
</script>
<style lang="scss" scoped>
    .circle {
        position:relative;
        width:5em;
        height:5em;
        border-radius:50%;
        background:red;
        overflow:hidden;
    }
    .circle:before {
        content:'';
        position:absolute;
        display:block;
        width:50%;
        height:100%;
        background:grey;
        transform-origin:center right;
        animation: circle-left 10s linear forwards;
    }
    .circle:after {
        content:'';
        position:absolute;
        right:0;
        display:block;
        width:50%;
        height:100%;
        background:grey;
        transform-origin:center left;
        animation:circle-right 10s linear forwards;
    }
    /*@keyframes circle-right {*/
    /*    0% {*/
    /*        transform:rotate(0deg);*/
    /*        background:grey;*/
    /*    }*/
    /*    50% {*/
    /*        transform:rotate(180deg);*/
    /*        background:grey;*/
    /*    }*/
    /*    50.01% {*/
    /*        transform:rotate(360deg);*/
    /*        background:inherit;*/
    /*    }*/
    /*    100% {*/
    /*        transform:rotate(360deg);*/
    /*        background:inherit;*/
    /*    }*/
    /*}*/
    @keyframes circle-left {
        0%{transform:rotate(0deg);}
        50%{transform:rotate(0deg);}
        100%{transform:rotate(180deg);}
    }
</style>
