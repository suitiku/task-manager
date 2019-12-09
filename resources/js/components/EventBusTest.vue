<!--単一ファイルコンポーネントのテンプレートファイル-->
<template>
    <div class="container">
        {{hoge}}
    </div>
</template>

<script>
    import bus from '../eventBus.js'
    export default {
        data:function(){
            return {
                hoge:''
            }  
        },
        props: {
            
        },
        watch:{
            
        },
        created:function(){
            
        },
        mounted:function(){
            bus.$on('bus-event',this.busEvent)
        },
        methods: {
            busEvent:function(hoge){
                this.hoge = hoge
            }
        }
    }
</script>
<style scoped>
    
</style>
