<!--ColorPickerコンポーネント-->
<!--候補から選択された色の16進数カラーコードを返す-->
<!--今後の改修ポイント-->

<template>
    <div class="color-wrapper">
        <div 
            ref="buttons"
            v-for="(color,index) in colorOptions" 
            v-on:click="selectColor(index)" 
            class="color-button" 
            v-bind:style="{background:color}">
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {

            }  
        },
        props: {
            colorOptions:{
                type:[Array,String],
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
            selectColor:function(index){
                for(let buttonIndex in  this.$refs.buttons){
                    if(buttonIndex == index){
                        event.target.classList.toggle('selected')
                        if(event.target.classList.contains('selected')){
                            this.$emit('input',this.colorOptions[index])
                        }else{
                            this.$emit('input','')
                        }
                    }else{
                        this.$refs.buttons[buttonIndex].classList.remove('selected')
                    }
                }
            }
        }
    }
</script>
<style scoped>
    .color-wrapper {
        display:flex;
    }
    .color-button {
        width:2em;
        height:2em;
        border-radius:50%;
        margin:0.2em;
        cursor:pointer;
        opacity:0.5;
        transition:all 0.4s;
    }
    .color-button:hover {
        opacity:1.0;
        border:2px solid darkgray;
        transition:all 0.4s;
    }
    .selected {
        opacity:1.0;
        border:2px solid darkgray;
    }
</style>
