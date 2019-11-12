<!--テスト用コンポーネント-->
<template>
    <div class="container">
        <progress-bar v-bind:denominotor=100 v-bind:numerator="Number(range)" />
        <input type="range" min=0 max=100 v-model="range">
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                range:0,
                project: {},
                override:[{project_id:3}]
            }  
        },
        mounted() {
        },
        created:async function() {
            let result = await axios.get('/api/projects/3')
            this.project = result.data
        },
        methods: {
        },
    }
</script>
<style>
</style>
