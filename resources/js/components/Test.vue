<!--テスト用コンポーネント-->
<template>
    <div class="container">
        <date-picker />
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                test:'',
                tag:''
            }  
        },
        mounted() {
        },
        created:async function() {
            let result = await axios.get('/api/tags/1')
            this.tag = result.data
        },
        methods: {
            showNotice:function(){
                this.$refs.notice.showNotice()
            }
        },
    }
</script>
<style>
    .space {
        width:100%;
        height:50px;
    }
</style>
