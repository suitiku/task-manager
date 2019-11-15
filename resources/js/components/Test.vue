<!--テスト用コンポーネント-->
<template>
    <div class="container">
        <project v-bind:project="project" />
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
            let result = await axios.get('/api/projects/2')
            this.project = result.data
        },
        methods: {
        },
    }
</script>
<style>
</style>
