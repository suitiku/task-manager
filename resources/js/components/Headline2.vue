<!--見出しコンポーネント-->
<template>
    <div class="wrapper">
        <h2 ref="headline"><slot></slot></h2>
    </div>
    <!--<h2 ref="headline" v-bind:style="setPositionLeft()"><slot></slot></h2>-->
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
            let vu = this
            this.setPositionLeft()
            
            window.addEventListener('resize',function(){
                vu.setPositionLeft()
            },false)
        },
        computed:{
        },
        methods: {
            setPositionLeft:function(){
                if(!this.$refs.headline)return
                this.$refs.headline.style.left = 0
                let left = this.$refs.headline.getBoundingClientRect().left + 10
                this.$refs.headline.style.left = -left + 'px'
            }
        }
    }
</script>
<style scoped>
    h2 {
        position:relative;
        height:1.5em;
        overflow:hidden;
        word-wrap:normal;
        padding:0.2em 0 0.2em 1em;
        margin:1em 0;
        border-radius:0.1em;
        background:orange;
        color:white;
        font-weight:900;
        font-style:italic;
        font-family: 'M PLUS 1p', sans-serif;
        animation:setup 0.8s ease-in-out 1;
        animation-fill-mode:forwards;
    }
    @keyframes setup {
        0% {
            width:0%;
        }
        90% {
            width:70%;
        }
        100% {
            width:65%;
        }
    }
</style>
