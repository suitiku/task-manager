<!--単一ファイルコンポーネントのテンプレートファイル-->
<template>
    <div class="container">
        <div v-bind:class="button_class" v-on:click="toggleButton()">
            <slot></slot>
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                active:false,
                button_class:'button-wrapper'
            }  
        },
        props: {
            value:Boolean
        },
        watch:{
            
        },
        created:function(){
            
        },
        mounted:function(){
            
        },
        methods: {
            toggleButton:function(){
                if(!this.active){
                    this.button_class = 'button-wrapper button-active'
                    this.active = true
                    this.$emit('input',true)
                }else{
                    this.button_class = 'button-wrapper'
                    this.active = false
                    this.$emit('input',false)
                }
                
                
            },
            
        }
    }
</script>
<style scoped>
    .button-wrapper {
        width:2.5em;
        height:2.5em;
        border-radius:50%;
        background:gainsboro;
        color:white;
        font-weight:bold;
        display:flex;
        justify-content:center;
        flex-direction:column;
        align-items:center;
        cursor:pointer;
        transition:all 0.5s ease;
    }
    .button-active {
        color:red;
        background:silver;
    }
</style>
