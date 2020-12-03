<!--数値範囲を指定するUIコンポーネント-->
<template>
    <div id="range-area">
        <div ref="line" class="line" v-on:mousedown="changeValue()">
            <div ref="lineActive" class="line-active" v-bind:style="setPosition"></div>
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                minValue:0,
                maxValue:50,
            }  
        },
        props: {
            min:{
                type:[Number,String],
                default:0,
                required:false
            },
            max:{
                type:[Number,String],
                default:100,
                required:false
            }
        },
        watch:{
            
        },
        created:function(){
            
        },
        mounted:function(){
            
        },
        computed: {
            setPosition:function(){
                let left = this.minValue / this.max //パーセンテージで算出
                let width = (this.maxValue - this.minValue) / this.max
                return {
                    left:left * 100 + '%',
                    width:width * 100 + '%'
                }
            }  
        },
        methods: {
            changeValue:function(){
                let line = this.$refs.line
                let lineRect = line.getBoundingClientRect()
                let lineActive = this.$refs.lineActive
                let lineActiveRect = lineActive.getBoundingClientRect()
                
                console.log(event)
                //クリックした位置の値を取得
                let value = (event.pageX - (lineRect.left + window.pageXOffset)) / lineRect.width * this.max
                console.log(value)
                this.maxValue = value
                if(event.target.className == 'line'){
                    
                }else{ //event.target.className == 'line-active'
                    
                }
            }
        }
    }
</script>
<style lang="scss" scoped>
    #range-area {
        width:9em;
        height:2em;
        border:1px solid red;
        display:flex;
        align-items:center;
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
                transition:all 0.2s linear;
            }
        }
    }
    
</style>
