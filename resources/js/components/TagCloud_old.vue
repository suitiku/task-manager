<!--選択ボックス-->
<!--props:options => {label:hoge,value:moge}の配列-->
<!--label,valueがない場合はnameとidを使用-->
<!--今後の改修ポイント-->
<!--１．デザイン？-->
<template>
    <div class="container">
        <div class="option-wrapper">
            <div v-for="(option,index) in options" v-bind:class="setClass(option)" v-on:click="selectOption(option.value)">{{option.label}}</div>
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                result:this.value
            }  
        },
        props: {
            value: {
                type:[Array,String,Number],
                required:false,
            },
            options: {
                type:Array,
                required:true
            },
            multiple: {
                type:Boolean,
                default:false,
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
            selectOption:function(value){
                if(this.multiple){
                    this.result = this.value
                    if(event.target.className == 'option selected'){
                        let index = this.result.indexOf(value)
                        this.result.splice(index,1)
                        event.target.className = 'option'
                        this.$emit('input',this.result)
                        return
                    }
                    
                }else{
                    if(event.target.className == 'option selected'){
                        event.target.className = 'option'
                        this.$emit('input','')
                        return
                    }
                    this.$emit('input',value)
                    return
                }
                
                this.result.push(value)
                this.$emit('input',this.result)
                event.target.className = 'option selected'
            },
            resetForm:function(){
                this.result = []
                this.$emit('input','')
                let els = document.getElementsByClassName('selected')
                if(els[0]){
                    els[0].className = 'option'
                }
            },
            setClass:function(option){
                if(this.multiple){
                    if(this.value.indexOf(option.value) != -1){
                        return 'option selected'
                    }else{
                        return 'option'
                    }
                }else{
                    if(this.value == option.value){
                        return 'option selected'
                    }else{
                        return 'option'
                    }
                }
            }
        }
    }
</script>
<style scoped>
    .option-wrapper {
        margin:0.5em;
        width:100%;
        display:flex;
        justify-content:space-around;
        flex-wrap:wrap;
    }
    .option {
        margin:0.2em 0.2em;
        padding:0.5em 1em;
        border:1px solid grey;
        color:grey;
        border-radius:0.5em;
        cursor:pointer;
        transition:all 0.3s;
    }
    .selected {
        transition:all 0.3s;
        background:grey;
        color:white;
    }
</style>
