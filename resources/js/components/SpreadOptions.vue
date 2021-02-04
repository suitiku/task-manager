<!--円状に広がる選択肢コンポーネント-->
<template>
    <div>
        <div class="circle invisible" ref="baseCircle"></div>
        <div v-for="(option,index) in options" class="option invisible" ref="options" v-on:click="selectOption(index)">{{option.label}}</div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                baseX:0,
                baseY:0,
                eventTarget:{},
                selectedIndex:'',
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
            selectedIndex:function(){
                if(this.options[this.selectedIndex]){
                    this.$emit('input',this.options[this.selectedIndex].value)
                }else{
                    this.$emit('input','')
                }
            }
        },
        created:function(){
            
        },
        mounted:function(){
            
        },
        methods: {
            expand:function(event){
                this.$refs.baseCircle.classList.remove('shrink')
                //option数で分割角度を算出
                let splitAngle = 180 / (this.options.length + 1)
                //表示中心位置を算出
                let eventTarget = event.target.getBoundingClientRect() //送信元のボタン等の要素
                this.eventTarget = event.target.getBoundingClientRect() //送信元のボタン等の要素
                let circle = this.$refs.baseCircle.getBoundingClientRect()
                let x = eventTarget.x + eventTarget.width/2 - circle.width/2
                this.baseX = x
                let y = eventTarget.y + eventTarget.height/2 - circle.height/2
                this.baseY = y
                this.$refs.baseCircle.style.left = x + 'px'
                this.$refs.baseCircle.style.top = y + 'px'
                this.$refs.baseCircle.classList.add('visible')
                this.$refs.baseCircle.classList.remove('invisible')
                this.$refs.baseCircle.classList.add('expand')
                
                // aimation終了後に選択肢を展開
                let vue = this
                
                this.$refs.baseCircle.addEventListener('animationend',this.expandOptions)
                
            },
            expandOptions:function(){ // 選択肢の表示
                let vue = this
                //option数で分割角度を算出
                let splitAngle = 180 / (this.options.length + 1)
                let circle = this.$refs.baseCircle.getBoundingClientRect()
                for(let index in vue.options){
                    //表示位置算出
                    let option = vue.$refs.options[index]
                    option.style.left = this.baseX + 'px'
                    option.style.top = this.baseY + 'px'
                    
                    option.classList.remove('invisible')
                    option.classList.add('visible')
                    
                    let optionRect = option.getBoundingClientRect()
                    
                    // 基準のY座標算出
                    let baseY = this.eventTarget.top + this.eventTarget.height/2 - optionRect.height/2
                    
                    // indexとsplitAngleからY座標の描画位置を調整
                    let deg
                    let positionX
                    let positionY
                    if(Number(index) < vue.options.length/2){
                        deg = (90 - (splitAngle * (Number(index) + 1)))
                        option.style.transform = 'rotate(' + deg + 'deg)'
                        positionX = this.baseX - Math.cos(deg / 180 * Math.PI) * (circle.width/2 + optionRect.width)
                        positionY = baseY - Math.sin( deg / 180 * Math.PI) * (circle.width/2 + optionRect.width)
                    }else{
                        deg = ((splitAngle * (Number(index) + 1)) -90)
                        option.style.transform = 'rotate(' + -deg + 'deg)'
                        positionX = this.baseX - Math.cos(deg / 180 * Math.PI) * (circle.width/2 + optionRect.width)
                        positionY = baseY + Math.sin( deg / 180 * Math.PI) * (circle.width/2 + optionRect.width)
                    }
                    option.style.left = positionX + 'px'
                    option.style.top = positionY + 'px'
                }
                this.$refs.baseCircle.removeEventListener('animationend',this.expandOptions)
            },
            selectOption:function(index){
                if(this.selectedIndex === ''){
                    this.selectedIndex = index
                    this.$refs.options[index].classList.add('selected')
                    this.$refs.options[index].addEventListener('transitionend',this.shrinkOptions)
                }else if(index == this.selectedIndex){
                    this.selectedIndex = ''
                    this.$refs.options[index].classList.remove('selected')
                }else{
                    this.$refs.options[this.selectedIndex].classList.remove('selected')
                    this.selectedIndex = index
                    this.$refs.options[index].classList.add('selected')
                    this.$refs.options[index].addEventListener('transitionend',this.shrinkOptions)
                }
            },
            shrinkOptions:function(){
                for(let optionIndex in this.options){
                    let option = this.$refs.options[optionIndex]
                    let optionRect = option.getBoundingClientRect()
                    option.style.transform = 'rotate(0deg)'
                    option.style.left = this.baseX + 'px'
                    option.style.top = Number(this.baseY + optionRect.height/2) + 'px'
                    option.classList.add('invisible')
                    option.classList.remove('visible')
                    option.removeEventListener('transitionend',this.shrinkOptions)
                }
                this.$refs.baseCircle.classList.remove('expand')
                this.$refs.baseCircle.classList.add('shrink')
                this.$refs.baseCircle.classList.remove('visible')
                this.$refs.baseCircle.classList.add('invisible')
            }
        }
    }
</script>
<style lang="scss" scoped>
    /*visible,invisibleは定義されていないが多分bootstrapに含まれているクラス*/
    .circle {
        position:absolute;
        z-index:5;
        transform:scale(1);
        width:7em;
        height:7em;
        border-radius:50%;
        background-color:rgba(grey,0.3);
        transition:all 0.3s ease 0.3s;
        &.expand {
            animation:expand 1.0s ease 1;
        }
        &.shrink {
            animation:shrink 1.0s ease 1;
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
    @keyframes shrink {
        0% {
            transform:scale(1);
        }
        100% {
            transform:scale(0);
        }
    }
    
    .option {
        cursor:pointer;
        display:inline-block;
        position:absolute;
        border:1px solid grey;
        border-radius:0.1em;
        text-align:center;
        padding:0.2em 1em;
        background-color:white;
        transition:all 0.5s ease 0.2s;
        &.selected {
            background-color:orange;
            color:white;
        }
    }
</style>
