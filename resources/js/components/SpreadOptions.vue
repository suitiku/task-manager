<!--単一ファイルコンポーネントのテンプレートファイル-->
<template>
    <div ref="optionsWrapper" class="options-wrapper" v-on:click="showOptions()">
        <span>テスト</span>
        <div ref="baseCircle" class="base-circle invisible"></div>
        <div v-for="(option,index) in options" ref="options" class="option invisible">{{option.label}}</div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                baseCircleLeft:0,
                baseCircleTop:0,
            }  
        },
        props: {
            options:{
                type:[String,Array],
                default:[],
                required:false
            }
        },
        watch:{
            
        },
        created:function(){
            
        },
        mounted:function(){
            
        },
        methods: {
            showOptions:function(){
                // baseCircleの位置を算出
                let wrapperRect = this.$refs.optionsWrapper.getBoundingClientRect()
                let baseCircleRect = this.$refs.baseCircle.getBoundingClientRect()
                this.baseCircleLeft = wrapperRect.width / 2 - baseCircleRect.width / 2
                this.baseCircleTop = wrapperRect.height/2 - baseCircleRect.height/2
                this.$refs.baseCircle.style.left = this.baseCircleLeft + 'px'
                this.$refs.baseCircle.style.top = this.baseCircleTop + 'px'
                this.$refs.baseCircle.classList.add('visible')
                this.$refs.baseCircle.classList.remove('invisible')
                this.$refs.baseCircle.classList.add('expand')
                
                this.$refs.baseCircle.addEventListener('animationend',this.expandOptions)
                
            },
            expandOptions:function(){
                let vue = this
                //option数で分割角度を算出
                let baseAngle = 135 //展開を始める角度。0は9時方向で時計回り。
                let splitAngle = 90 / (this.options.length - 1) //展開範囲
                // let splitAngle = 90 / this.options.length //展開範囲
                let circleRect = this.$refs.baseCircle.getBoundingClientRect()
                //基準のoptionの長さを算出
                
                let optionWidthArray = this.$refs.options.map(option => {
                    return option.getBoundingClientRect().width
                })
                let baseAdjustWidth = Math.max.apply(null,optionWidthArray)
                // let baseAdjustWidth = circleRect.width/2 + 20
                
                for(let index in vue.options){
                    //表示位置算出
                    let option = vue.$refs.options[index]
                    let optionRect = option.getBoundingClientRect()
                    let optionBaseTop = Number(this.baseCircleTop + circleRect.height/2 - optionRect.height/2)
                    
                    option.style.left = this.baseCircleLeft + circleRect.width/2 + 'px'
                    option.style.top = optionBaseTop + 'px'
                    
                    option.classList.remove('invisible')
                    option.classList.add('visible')
                    
                    // indexとsplitAngleからY座標の描画位置を調整
                    let deg
                    let optionLeft
                    let optionTop
                    let adjustWidth = circleRect.width/2 + 40//optionRect.width + optionRect.height + 10
                    
                    option.style.width = baseAdjustWidth + 'px'
                    deg = baseAngle + splitAngle * Number(index)
                    //X軸方向
                    if((0 <= deg && deg <= 90) || 270 < deg){ //&& deg < 360)){
                        option.style.transformOrigin = 'center right'
                        optionLeft = -(Math.cos(deg / 180 * Math.PI) * adjustWidth) - (baseAdjustWidth - circleRect.width/2) -20
                        option.style.transform = 'rotate(' + deg + 'deg)'
                    }else{
                        option.style.transformOrigin = 'center left'
                        optionLeft = -this.baseCircleLeft - (Math.cos(deg / 180 * Math.PI) * adjustWidth)
                        option.style.transform = 'rotate(' + (deg - 180) + 'deg)'
                    }
                    // Y軸方向
                    optionTop = optionBaseTop - (Math.sin( deg / 180 * Math.PI) * adjustWidth)
                    // 位置設定
                    option.style.left = optionLeft + 'px'
                    option.style.top = optionTop + 'px'
                }
                this.$refs.baseCircle.removeEventListener('animationend',this.expandOptions)
            }
        }
    }
</script>
<style lang="scss" scoped>
    .options-wrapper {
        display:inline-block;
        position:relative;
        cursor:pointer;
        border:1px solid red;
        width:3em;
        height:2em;
        span {
            font-size:100%;
        }
        
        .base-circle {
            position:absolute;   
            z-index:50;
            width:5em;
            height:5em;
            border-radius:50%;
            background-color:rgba(grey,0.5);
            transform:scale(1);
            &.expand {
                animation:expand 1s ease 1 forwards;
            }
        }
        
        .option {
            position:absolute;
            z-index:55;
            top:0;
            left:0;
            display:inline-block;
            border:1px solid white;
            border-radius:5%;
            white-space: nowrap;
            padding:0 0.3em;
            cursor:pointer;
            background-color:rgba(white,0.8);
            text-align:right;
            /*transform-origin:center right;*/
            transition:all 0.5s ease;
        }
    }
    @keyframes expand {
        0% {
            transform:scale(0);
        }
        100% {
            transform:scale(1);
        }
    }
</style>
