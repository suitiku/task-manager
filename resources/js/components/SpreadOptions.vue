<!--単一ファイルコンポーネントのテンプレートファイル-->
<template>
    <div ref="optionsWrapper" class="options-wrapper" v-on:click="showOptions()">
        <span>テスト</span>
        <div ref="baseCircle" class="base-circle invisible"></div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {

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
            showOptions:function(){
                // baseCircleの位置を算出
                let wrapperRect = this.$refs.optionsWrapper.getBoundingClientRect()
                let baseCircleRect = this.$refs.baseCircle.getBoundingClientRect()
                let baseCircleLeft = wrapperRect.width / 2 - baseCircleRect.width / 2
                let baseCircleTop = wrapperRect.height/2 - baseCircleRect.height/2
                this.$refs.baseCircle.style.left = baseCircleLeft + 'px'
                this.$refs.baseCircle.style.top = baseCircleTop + 'px'
                this.$refs.baseCircle.classList.add('visible')
                this.$refs.baseCircle.classList.remove('invisible')
                this.$refs.baseCircle.classList.add('expand')
                
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
        width:6em;
        height:2em;
        
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
