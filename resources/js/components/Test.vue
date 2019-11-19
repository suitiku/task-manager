<!--テスト用コンポーネント-->
<template>
    <div class="container">
        {{test}}
        <tag-cloud v-model="test" v-bind:options="options" />
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                test:'',
                options:[
                    {label:'1時間後',value:'1'},
                    {label:'1日後',value:'2'},
                    {label:'1週間後',value:'3'},
                    {label:'1ヶ月後',value:'3'},
                    {label:'3ヶ月後',value:'3'},
                    {label:'6ヶ月後',value:'3'},
                    {label:'1年後',value:'3'},
                    {label:'2年後',value:'3'},
                    {label:'5年後',value:'3'},
                    {label:'10年後',value:'3'},
                ],
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
