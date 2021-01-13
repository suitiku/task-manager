<!--数値範囲を指定するUIコンポーネント-->
<template>
    <div id="range-number-wrapper">
        <div class="value-display">{{minValue}}</div>
        <div id="range-area">
            <div ref="line" class="line" v-on:mousemove="dragBar()" v-on:mousedown="changeValue()">
                <div ref="lineActive" class="line-active" v-bind:style="setPosition"></div>
            </div>
        </div>
        <div class="value-display">{{maxValue}}</div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                minPercentage:0,
                maxPercentage:50,
                isMouseLeftButton:false, //マウスの左ボタンの状態
                emitValue: {
                    min:null,
                    max:null
                }
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
            validDigits: { //有効桁数
                type:Number,
                default:1,
                required:false,
            }
        },
        watch:{
            emitValue: {
                handler:function(){
                    this.$emit('input',this.emitValue)
                },
                deep:true
            }
        },
        created:function(){
            
        },
        mounted:function(){
            let vue = this
            window.onmousedown = function(){
                vue.isMouseLeftButton = true
            }
            window.onmouseup = function(){
                vue.isMouseLeftButton = false
            }
        },
        computed: {
            setPosition:function(){
                let left = this.minPercentage / this.maximumValue //パーセンテージで算出
                let width = (this.maxPercentage - this.minPercentage) / 100
                return {
                    left:this.minPercentage + '%',
                    width:(this.maxPercentage - this.minPercentage) + '%'
                }
            },
            minValue:function(){
                if(this.minPercentage < 0)this.minPercentage = 0
                let value = Math.round(((this.maximumValue - this.minimumValue) / 100 * this.minPercentage) * 10 ** this.validDigits) / 10 ** this.validDigits + this.minimumValue
                value = this.checkDigits(value) //桁数チェック
                this.emitValue.min = value
                return this.emitValue.min
            },
            maxValue:function(){
                let value = Math.round(((this.maximumValue - this.minimumValue) / 100 * this.maxPercentage) * 10 ** this.validDigits) / 10 ** this.validDigits + this.minimumValue
                value = this.checkDigits(value)
                this.emitValue.max = value
                return this.emitValue.max
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
                
                // console.log(event)
                //クリックした位置の値を取得（パーセンテージ）
                let value = (event.pageX - (lineRect.left + window.pageXOffset)) / lineRect.width * 100
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
            },
            checkDigits:function(value){
                let splitedValue = String(value).split('.')
                if(splitedValue[1] && splitedValue[1].length > this.validDigits){
                    let decimal = splitedValue[1].slice(0,this.validDigits)
                    value = Number(splitedValue[0] + '.' + decimal)
                }
                return value
            }
        }
    }
</script>
<style lang="scss" scoped>
    #range-number-wrapper {
        display:flex;
        justify-content:space-between;
        align-items:center;
        /*border:1px solid blue;*/
        max-width:12em;
        #range-area {
            width:9em;
            height:2em;
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
        .value-display {
            /*border:1px solid red;*/
            width:3em;
            text-align:center;
        }
    }
    
    
</style>
