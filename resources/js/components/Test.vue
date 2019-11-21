<!--テスト用コンポーネント-->
<template>
    <div class="container">
        {{test}}
        <div class="space"></div>
        <div class="wrapper">
            <tag v-model="test" v-bind:tag="tag" v-bind:id="id" />
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                test:'',
                tag:{},
                id:3,
            }  
        },
        mounted() {
        },
        created:async function() {
            let result = await axios.get('/api/tags/6')
            this.tag = result.data
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
    .wrapper {
        width:50%;
        display:flex;
        justify-content:flex-start;
    }
</style>
