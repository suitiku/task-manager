<!--数値範囲を指定するUIコンポーネント-->
<template>
    <div>
        <div>{{minValue}}/{{Math.round(minPercentage)}}</div>
        <div id="range-area">
            <div ref="line" class="line" v-on:mousemove="dragBar()" v-on:mousedown="changeValue()">
                <div ref="lineActive" class="line-active" v-bind:style="setPosition"></div>
            </div>
        </div>
        <div>{{maxValue}}/{{Math.round(maxPercentage)}}</div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                minPercentage:0,
                maxPercentage:50,
                isMouseLeftButton:false, //マウスの左ボタンの状態
            }  
        },
        props: {
            minimumValue:{
                type:Number,
                default:20,
                required:false
            },
            maximumValue:{
                type:Number,
                default:300,
                required:false
            },
            validDigits: {
                type:Number,
                defalut:0,
                required:false,
            }
        },
        watch:{
            
        },
        created:function(){
            
        },
        mounted:function(){
            let vue = this
            window.onmousedown = function(){
                vue.isMouseLeftButton = true
                // console.log(vue.isMouseLeftButton)
            }
            window.onmouseup = function(){
                vue.isMouseLeftButton = false
                // console.log(vue.isMouseLeftButton)
            }
        },
        computed: {
            setPosition:function(){
                let left = this.minPercentage / this.maximumValue //パーセンテージで算出
                // let width = (this.maxPercentage - this.minPercentage) / (this.maximumValue - this.minimumValue)
                let width = (this.maxPercentage - this.minPercentage) / 100
                return {
                    // left:left * 100 + '%',
                    left:this.minPercentage + '%',
                    // width:width * 100 + '%'
                    width:(this.maxPercentage - this.minPercentage) + '%'
                }
            },
            minValue:function(){
                return Math.round((this.maximumValue - this.minimumValue) / 100 * this.minPercentage) + this.minimumValue
            },
            maxValue:function(){
                return Math.round((this.maximumValue - this.minimumValue) / 100 * this.maxPercentage) + this.minimumValue
            }
        },
        methods: {
            changeMouseState:function(){
                this.isMouseLeftButton != this.isMouseLeftButton
            },
            changeValue:function(){
                let line = this.$refs.line
                let lineRect = line.getBoundingClientRect()
                let lineActive = this.$refs.lineActive
                let lineActiveRect = lineActive.getBoundingClientRect()
                
                console.log(event)
                //クリックした位置の値を取得（パーセンテージ）
                // let value = (event.pageX - (lineRect.left + window.pageXOffset)) / lineRect.width * this.maximumValue
                let value = (event.pageX - (lineRect.left + window.pageXOffset)) / lineRect.width * 100
                // console.log(value)
                // 最小値と最大値との距離を測る
                if(Math.abs(this.maxPercentage - value) >= Math.abs(this.minPercentage - value)){
                    this.minPercentage = value
                }else{
                    this.maxPercentage = value
                }
            },
            dragBar:function(){
                if(this.isMouseLeftButton){
                    this.changeValue()
                }
            }
        }
    }
</script>
<style lang="scss" scoped>
    #range-area {
        width:9em;
        height:2em;
        /*border:1px solid red;*/
        display:flex;
        align-items:center;
        cursor:pointer;
        .line {
            position:relative;
            z-index:2;
            width:100%;
            height:0.5em;
            border:1px solid grey;
            border-radius:0.5em;
            .line-active {
                position:relative;
                z-index:1;
                height:100%;
                background-color:orange;
                transition:all 0.1s linear;
            }
        }
    }
    
</style>
