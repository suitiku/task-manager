<!--タグ表示用コンポーネント-->
<template>
    <div class="container">
        <div class="tag">{{tag.label}}</div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {

            }  
        },
        props: {
            tag: {
                type:Object,
                required:true,
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
