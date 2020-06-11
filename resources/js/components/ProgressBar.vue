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
                // 母数が0の場合は終了
                if(this.denominotor == 0){
                    return '0%'
                }
                return Math.floor(((this.numerator / this.denominotor) * 100)) + '%'
            },
            char:function(){
                let style = {}
                if(((this.numerator / this.denominotor) * 100) < 90 ){
                    let left = Math.floor(((this.numerator / this.denominotor) * 100 + 2)) + '%'
                    style = {
                        left: String(left)
                    }
                }else if(((this.numerator / this.denominotor) * 100) < 100){
                    style = {
                        left:'80%',
                        color:'white'
                    }
                }else{
                    style = {
                        left:'50%',
                        color:'white'
                    }
                }
                if(this.denominotor == 0){ style = {left:'2%'}}
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
        border:1px solid gainsboro;
        border-radius:1em;
        overflow:hidden;
    }
    .bar {
        height:2em;
        background:orange;
        opacity:0.8;
        transition:all 1s;
    }
    .char {
        position:relative;
        top:-1.8em;
        font-style:italic;
        font-family: 'M PLUS 1p', sans-serif;
        transition:all 1s;
    }
</style>
