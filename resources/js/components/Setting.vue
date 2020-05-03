<!--単一ファイルコンポーネントのテンプレートファイル-->
<template>
    <div class="container">
        <h2>テンプレートの編集</h2>
        <template-task-management v-bind:userId="user_id" />
        <h2>タグの編集</h2>
        <tag-management v-bind:user_id="user_id" />
    </div>
</template>

<script>
    export default {
        data:function(){
            return {

            }  
        },
        props: {
            user_id:{
                type:[String,Number],
                required:true
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
