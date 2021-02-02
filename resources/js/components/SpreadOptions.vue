<!--円状に広がる選択肢コンポーネント-->
<template>
    <div>
        <div class="circle invisible" ref="baseCircle"></div>
        <div v-for="(option,index) in options" class="option invisible" ref="options">{{option.label}}</div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                options:[
                    {label:'test1',value:0},
                    {label:'test2',value:1},
                    {label:'test3',value:2},
                    {label:'test4',value:3},
                ]
            }  
        },
        props: {
            
        },
        watch:{
            
        },
        created:function(){
            
        },
        mounted:function(){
            
        },
        methods: {
            expand:function(event){
                //option数で分割角度を算出
                let splitAngle = 180 / (this.options.length + 1)
                //表示中心位置を算出
                let eventTarget = event.target.getBoundingClientRect() //送信元のボタン等の要素
                let circle = this.$refs.baseCircle.getBoundingClientRect()
                let x = eventTarget.x + eventTarget.width/2 - circle.width/2
                let y = eventTarget.y + eventTarget.height/2 - circle.height/2
                this.$refs.baseCircle.style.left = x + 'px'
                this.$refs.baseCircle.style.top = y + 'px'
                this.$refs.baseCircle.classList.toggle('visible')
                this.$refs.baseCircle.classList.toggle('expand')
                this.$refs.baseCircle.classList.toggle('invisible')
                
                // aimation終了後に選択肢を展開
                let vue = this
                this.$refs.baseCircle.addEventListener('animationend',function(){
                    // 選択肢の表示
                    for(let index in vue.options){
                        //表示位置算出
                        let option = vue.$refs.options[index]
                        let optionRect = option.getBoundingClientRect()
                        
                        // 基準のY座標算出
                        let baseY = eventTarget.top + eventTarget.height/2 - optionRect.height/2
                        
                        // indexとsplitAngleからY座標の描画位置を調整
                        let deg
                        let positionX
                        let positionY
                        if(Number(index) < vue.options.length/2){
                            deg = (90 - (splitAngle * (Number(index) + 1)))
                            option.style.transform = 'rotate(' + deg + 'deg)'
                            positionX = x - Math.cos(deg / 180 * Math.PI) * circle.width
                            positionY = baseY - Math.sin( deg / 180 * Math.PI) * circle.width
                        }else{
                            deg = ((splitAngle * (Number(index) + 1)) -90)
                            option.style.transform = 'rotate(' + -deg + 'deg)'
                            positionX = x - Math.cos(deg / 180 * Math.PI) * circle.width
                            positionY = baseY + Math.sin( deg / 180 * Math.PI) * circle.width
                        }
                        
                        option.style.left = positionX + 'px'
                        option.style.top = positionY + 'px'
                        option.classList.toggle('invisible')
                        option.classList.toggle('visible')
                    }
                    
                })
                
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
        &.expand {
            animation:hoge 0.3s ease 1;
        }
    }
    @keyframes hoge {
        0% {
            transform:scale(0);
        }
        100% {
            transform:scale(1);
        }
    }
    
    .option {
        display:inline-block;
        position:absolute;
        border:1px solid grey;
        border-radius:0.1em;
        text-align:center;
        padding:0.2em 1em;
    }
    
</style>
