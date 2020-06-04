<!--見出しコンポーネント-->
<!--今後の改修ポイント-->
<!--１．v-showの下に来た場合（mount時にdsiplay:none）にgetBoundingClientRectが取れないのでどうにかしたい-->
<template>
    <div class="wrapper" ref="wrapper">
        <h2 class="h2" ref="headline"><slot></slot></h2>
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
    .h2 {
        position:relative;
        height:1.6em;
        overflow:hidden;
        padding:0.2em 0 0.4em 1em;
        margin:1em 0;
        border-radius:0.1em;
        background:orange;
        color:white;
        font-family: 'M PLUS 1p', sans-serif;
        font-weight:900;
        font-style:oblique;
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
    
@media screen and (max-width:480px) { 
    .h2 {
        width:130%;
    }
}
@media screen and (min-width:480px){
    .h2 {
        animation:setup 0.8s ease-in-out 1;
        animation-fill-mode:forwards;
    }
}
</style>
