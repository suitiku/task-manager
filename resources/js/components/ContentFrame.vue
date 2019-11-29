<!--単一ファイルコンポーネントのテンプレートファイル-->
<template>
    <div class="frame-wrapper">
        <div class="frame-label" v-bind:style="{backgourndColor:label_background}">{{frame_label}}</div>
        <slot></slot>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                label_background:'red'
            }  
        },
        props: {
            frame_label: {
                type:String,
                default:'',
                required:false,
            }
        },
        watch:{
            
        },
        created:function(){
            
        },
        mounted:function(){
            // let component = this.$parent
            // console.log(component.$el)
            // while(component.$el.syle.backgroundColor == ''){
            //     component = component.$parent
            //     console.log(component)
            // }
            // this.label_background = component.syle.backgroundColor
        },
        methods: {
            
        }
    }
</script>
<style scoped>
    .frame-wrapper {
        position:relative;
        width:100%;
        border:2px solid grey;
        border-radius:0.5em;
        padding:1em;
    }
    .frame-label {
        position:absolute;
        z-index:2;
        top:-1em;
        left:3%;
        padding:0.5em;
        /*background-color:inherit;*/
    }
</style>
