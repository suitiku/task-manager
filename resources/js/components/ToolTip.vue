<!--単一ファイルコンポーネントのテンプレートファイル-->
<template>
    <div ref="toolTip" v-bind:class="toolTipRoot" v-bind:style="toolTipPosition">
        <div class="tool-tip">
            <slot></slot>
            <span>{{textContent}}</span>
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                textContent:'',
                toolTipRoot:'tool-tip-hide',
                toolTipPosition:{
                    top:0,
                    left:0
                }
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
            showToolTip:function(content){
                this.textContent = content
                this.toolTipPosition.top = (event.clientY - this.$refs.toolTip.clientHeight - 30) + 'px'
                this.toolTipPosition.left = (event.clientX - this.$refs.toolTip.clientWidth / 2) + 'px'
                this.toolTipRoot = 'tool-tip-show'
            },
            hideToolTip:function(){
                this.toolTipRoot = 'tool-tip-hide'
                this.textContent = ''
            },
            toggleToolTipVisible:function(content){
                this.textContent = content
                this.toolTipPosition.top = (event.clientY - this.$refs.toolTip.clientHeight - 30) + 'px'
                this.toolTipPosition.left = (event.clientX - this.$refs.toolTip.clientWidth / 2) + 'px'
                this.toolTipRoot = this.toolTipRoot == 'tool-tip-show' ? 'tool-tip-hide' : 'tool-tip-show'
            }
        }
    }
</script>
<style scoped>
    .tool-tip-hide {
        position:fixed;
        transform-origin:bottom center;
        transform:rotateX(90deg);
        transition:opacity,transform 0.5s ease;
    }
    .tool-tip-show {
        position:fixed;
        transform-origin:bottom center;
        transform:rotateX(0deg);
        transition:opacity,transform 0.5s ease;
    }
    .tool-tip {
        position: relative;
        display: inline-block;
        padding: 1.0em 0.5em;
        max-height:200px;
        min-width: 5em;
        max-width: 100%;
        color: #555;
        font-size: 75%;
        background: #FFF;
        border: solid 3px #555;
        box-sizing: border-box;
        text-align:center;
        border-radius:1.0em;
    }
    .tool-tip:before {
        content: "";
        position: absolute;
        bottom: -24px;
        left: 50%;
        margin-left: -15px;
        border: 12px solid transparent;
        border-top: 12px solid #FFF;
        z-index: 2;
    }
    .tool-tip:after {
        content: "";
        position: absolute;
        bottom: -30px;
        left: 50%;    
        margin-left: -17px;
        border: 14px solid transparent;
        border-top: 14px solid #555;
        z-index: 1;
}
</style>
