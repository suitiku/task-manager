<!--単一ファイルコンポーネントのテンプレートファイル-->
<template>
    <div class="container">
        <h4>こんにちわ！{{user.name}}さん！</h4>
        
        <!--ニュースフィード-->
        
        <!--今日締切のタスク数-->
        
        <!--今月の消化タスク-->
        
        <!--月ごとの消化タスクグラフ-->
        <p>ここにグラフコンポーネントを追加</p>
        
        <!--今日の予定-->
        
    </div>
</template>

<script>
    export default {
        data:function(){
            return {

            }  
        },
        props: {
            user:{
                type:Object,
                required:false,
            }
        },
        watch:{
            
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
