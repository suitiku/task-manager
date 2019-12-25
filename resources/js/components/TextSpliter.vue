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
                result:[],
                isInit:false,
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
            },
            value:function(){
                //初回のみ初期値をセット
                if(!this.isInit){
                    this.text = this.value.join('\n')
                    this.isInit = true
                }
            }
        },
        created:function(){
            
        },
        mounted:function(){
        },
        methods: {
        }
    }
</script>
<style scoped>
    
</style>
