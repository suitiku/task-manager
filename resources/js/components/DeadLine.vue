<!--将来の日付を返すUIコンポーネント-->
<!--選択式と記述式を切替-->
<template>
    <div class="container">
        
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                options:[
                    {label:'1時間後',value:'0000-00-00 01:00:00'},
                    {label:'1日後',value:'0000-00-01 00:00:00'},
                    {label:'1週間後',value:'0000-00-07 01:00:00'},
                ],
                baseDateTime:'',
                resultDateTime:'',
                year:'',
                month:'',
                day:'',
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
            selectOption:function(value){
                this.baseDateTime = new Date() //現在時刻を取得
                
                
                
                
                console.log()
                
                
            }
        }
    }
</script>
<style scoped>
    
</style>
