<!--テスト用コンポーネント-->
<template>
    <div class="container">
        <project-list />
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                project:{}
            }  
        },
        mounted() {
        },
        created:async function() {
            let result = await axios.get('/api/projects/1')
            this.project = result.data
        },
        methods: {
        },
    }
</script>
<style>
    .space {
        width:100%;
        height:50px;
    }
</style>
