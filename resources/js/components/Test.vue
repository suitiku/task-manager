<!--テスト用コンポーネント-->
<template>
    <div class="container">
        <versatile-form table="tasks" />
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                test:''
            }  
        },
        mounted() {
        },
        created:async function() {
            let result = await axios.get('/api/projects/2')
            this.project = result.data
        },
        methods: {
        },
    }
</script>
<style>
</style>
