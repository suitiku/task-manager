<!--選択ボックス-->
<!--props:options => {label:hoge,value:moge}の配列-->
<!--label,valueがない場合はnameとidを使用-->
<!--今後の改修ポイント-->
<!--１．デザイン？-->
<template>
    <div class="container">
        <div class="option-wrapper">
            <div v-for="(option,index) in options" v-bind:class="setClass(option)" v-on:click="selectOption(option)">{{getLabel(option)}}</div>
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                labelArray:['label','name'],
                valueArray:['value','id'],
                defaultMode:false,
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
            },
            defaultValue: {
                type:[Number,String,Array],
                required:false
            }
        },
        watch:{
            value:function(newVal,oldVal){
                if(newVal == '' && this.defaultValue){
                    this.$emit('input',this.defaultValue)
                    this.defaultMode = true
                }
            },
            defaultValue:function(){
                if(this.defaultValue && !this.value){
                    this.$emit('input',this.defaultValue)
                    this.defaultMode = true
                }
            }
        },
        created:function(){
            
        },
        mounted:function(){
            
        },
        methods: {
            getLabel:function(option){
                if(!option)return
                
                for(let key of this.labelArray){
                    if(option[key]){
                        return option[key]
                    }
                }
            },
            selectOption:function(option){
                if(!option)return
                
                let resultValue
                for(let key of this.valueArray){
                    if(option[key]){
                        resultValue = option[key]
                    }
                }
                
                if(this.multiple){
                    if(!this.value || this.defaultMode){
                        this.result = []
                        this.defaultMode = false
                    }else{
                        this.result = this.value
                    }
                    if(event.target.className == 'option selected'){
                        let index = this.result.indexOf(resultValue)
                        this.result.splice(index,1)
                        event.target.className = 'option'
                        return
                    }
                }else{
                    if(event.target.className == 'option selected'){
                        event.target.className = 'option'
                        this.$emit('input','')
                        return
                    }
                    this.$emit('input',resultValue)
                    return
                }
                
                this.result.push(resultValue)
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
                if(!option)return
                
                let result
                for(let key of this.valueArray){
                    if(option[key]){
                        result = option[key]
                    }
                }
                
                if(this.multiple){
                    if(this.value.indexOf(result) != -1){
                        return 'option selected'
                    }else{
                        return 'option'
                    }
                }else{
                    if(this.value == result){
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
