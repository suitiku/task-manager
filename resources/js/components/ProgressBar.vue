<!--プログレスバーコンポーネント-->
<!--今後の改修ポイント-->
<!--１．色の変更-->
<!--２．パーセンテージによって色を変える-->
<template>
    <div class="container">
        <div class="bar-wrapper">
            <div class="bar" v-bind:style="{width:width}"></div>
            <span v-bind:style="char" class="char">{{width}}</span>
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
            //分母
            denominotor: {
                type:Number,
                default:0,
                required:true,
            },
            //分子
            numerator: {
                type:Number,
                default:0,
                required:true
            } 
        },
        watch:{
            
        },
        created(){
            
        },
        mounted() {
            // console.log(this.numerator / this.denominotor)
        },
        computed:{
            width:function(){
                return Math.floor(((this.numerator / this.denominotor) * 100)) + '%'
            },
            char:function(){
                let style = {}
                if(((this.numerator / this.denominotor) * 100) < 90 ){
                    let width = Math.floor(((this.numerator / this.denominotor) * 100 + 2)) + '%'
                    // console.log(width)
                    style = {
                        left: String(width)
                    }
                }else{
                    style = {
                        left:'80%',
                        color:'white'
                    }
                }
                return style
            }
        },
        methods: {
            
        }
    }
</script>
<style scoped>
    .bar-wrapper {
        position:relative;
        width:100%;
        height:2em;
        border:1px solid mediumspringgreen;
        border-radius:1em;
        overflow:hidden;
    }
    .bar {
        height:2em;
        background:mediumspringgreen;
        transition:all 1s;
    }
    .char {
        position:relative;
        top:-1.7em;
        font-weight:bold;
        transition:all 1s;
    }
</style>
