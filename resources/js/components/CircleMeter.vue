<!--円型のメーターコンポーネント-->
<!--今後の改修ポイント-->
<!--１．大きさを可変にする-->
<!--２．色の変更-->
<!--３．アニメーションの実装（入力されたらnumeratorの数字を遅延して増減させる形で実装する）-->
<!--４．真ん中に線が入るやつ修正-->
<template>
    <div ref="circleBackground" class="circle-background">
        <div ref="circleLeft" v-bind:style="circleLeftAngle" class="circle-left"></div>
        <div ref="circleRight" v-bind:style="circleRightAngle" class="circle-right"></div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
            }  
        },
        props: {
            // 分母
            denominator:{
                type:[Number,String],
                default:1,
                required:true
            },
            // 分子
            numerator:{
                type:[Number,String],
                default:0,
                required:true
            }
        },
        watch:{
            
        },
        created:function(){
            
        },
        mounted:function(){
        },
        computed:{
            circleRightAngle:function(){
                let degree = (this.numerator / this.denominator) * 360
                return {transform:'rotate(' + degree + 'deg)'}
            },
            circleLeftAngle:function(){
                let degree = (this.numerator / this.denominator) * 360
                if(degree > 180){
                    return {transform:'rotate(180deg)',background:'orange'}
                }else{
                    return {transform:'rotate(0deg)',background:'whitesmoke'}
                }
            }
        },
        methods: {
        }
    }
</script>
<style scoped>
    .circle-background {
        position:relative;
        z-index:1;
        width:1em;
        height:1em;
        display:flex;
        background:whitesmoke;
        border-radius:50%;
    }
    .circle-right {
        position:absolute;
        display:block;
        content:'';
        z-index:2;
        width:0.5em;
        height:1em;
        border-radius:0.5em 0 0 0.5em;
        background-color:orange;
        transform-origin:right;
    }
    .circle-left {
        position:relative;
        display:block;
        content:'';
        z-index:3;
        width:0.5em;
        height:1em;
        border-radius:0.5em 0 0 0.5em;
        transform-origin:right;
    }
</style>
