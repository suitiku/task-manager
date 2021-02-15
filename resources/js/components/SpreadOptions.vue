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
                let splitAngle = 180 / (this.options.length + 1)
                let circleRect = this.$refs.baseCircle.getBoundingClientRect()
                for(let index in vue.options){
                    //表示位置算出
                    let option = vue.$refs.options[index]
                    let optionRect = option.getBoundingClientRect()
                    let optionBaseTop = Number(this.baseCircleTop + circleRect.height/2 - optionRect.height/2)
                    option.style.left = this.baseCircleLeft + 'px'
                    option.style.top = optionBaseTop + 'px'
                    
                    option.classList.remove('invisible')
                    option.classList.add('visible')
                    
                    // // indexとsplitAngleからY座標の描画位置を調整
                    let deg
                    let optionLeft
                    let optionTop
                    if(Number(index) < vue.options.length/2){
                        deg = (90 - (splitAngle * (Number(index) + 1)))
                        option.style.transform = 'rotate(' + deg + 'deg)'
                        optionLeft = -(Math.cos(deg / 180 * Math.PI) * (circleRect.width/2 + optionRect.width))
                        optionTop = optionBaseTop - Math.sin( deg / 180 * Math.PI) * (circleRect.width/2 + optionRect.width)
                    }else{
                        deg = ((splitAngle * (Number(index) + 1)) -90)
                        option.style.transform = 'rotate(' + -deg + 'deg)'
                        optionLeft = -(Math.cos(deg / 180 * Math.PI) * (circleRect.width/2 + optionRect.width))
                        optionTop = optionBaseTop + Math.sin( deg / 180 * Math.PI) * (circleRect.width/2 + optionRect.width)
                    }
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
        width:5em;
        height:2em;
        /*border-radius:50%;*/
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
            /*display:inline-block;*/
            border:1px solid grey;
            border-radius:2%;
            white-space: nowrap;
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
