<!--単一ファイルコンポーネントのテンプレートファイル-->
<template>
    <div class="wrapper">
        <i class="fas fa-star" v-for="indexChecked of stared" v-on:click="updateValue(indexChecked)"></i>
        <i class="far fa-star" v-for="indexUnChecked of (5 - stared)" v-on:click="updateValue(stared + indexUnChecked)"></i>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                stared:0
            }  
        },
        props: {
            value: {
                type:[Number,String],
                default:0,
                required:false
            }
        },
        watch:{
            value:function(){
                if(this.value == ''){
                    this.stared = 1
                    this.$emit('input',1)
                }else{
                    this.stared = this.value
                }
            }
        },
        created(){
            this.init()
        },
        mounted() {
            this.init()
        },
        methods: {
            init:function(){
                if(this.value){
                    this.stared = this.value
                }else{
                    this.stared = 1
                    this.$emit('input',1)
                }
            },
            updateValue:function(index){
                this.stared = index
                this.$emit('input',index)
            }
        }
    }
</script>
<style scoped>
    .wrapper {
        width:6em;
        display:flex;
        justify-content:center;
    }
    
</style>
