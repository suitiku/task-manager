<!--テキストを分割するテキストエリアコンポーネント-->
<!--デフォルト分割は改行-->
<template>
    <div class="container">
        <textarea v-model="text"></textarea>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                text:'',
                result:[]
            }  
        },
        props: {
            value:{
                type:[String,Array],
                required:false
            }
        },
        watch:{
            text:function(){
                // 改行で分割
                this.result = this.text.split(/\r\n|\n/)
                // 空要素を排除
                for(let index in this.result){
                    if(this.result[index] == ''){this.result.splice(index,1)}
                }
                // 結果を送出
                this.$emit('input',this.result)
            }
        },
        created:function(){
            
        },
        mounted:function(){
            if(Array.isArray(this.value)){
                for(let element of this.value){
                    this.text += element + '\n'
                }
            }else{
                this.text = this.value
            }
        },
        methods: {
        }
    }
</script>
<style scoped>
    
</style>
