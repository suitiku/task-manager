<!--テスト用コンポーネント-->
<template>
    <div class="container">
        <div v-for="tes in test">
            <p>id: {{tes.id}}</p>
            <p>priority: {{tes.priority}}</p>
            <p>difficulty: {{tes.difficulty}}</p>
        </div>
        <filter-box v-model="test" v-bind:targetArray="tasks" />
        
        
        <notice ref="notice" />
        <!--モーダル-->
        <modal ref="modal" v-model="modal">
            <versatile-form table="tasks" idProp="4"/>
        </modal>
        <button class="button" v-on:click="showModal()">modal</button>
        <button class="button" v-on:click="filterArray()">filter</button>
        <!--<button class="button" v-on:click="showNotice()">notice</button>-->
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                modal:false,
                test:'',
                hoge:'',
                tasks:'',
                testOptions: [
                    {label:'test',value:'hoge'},    
                    {label:'aaaa',value:'bbbb'},   
                    {label:'moge',value:'moge2'},   
                    {label:'yamada',value:'yamada'},   
                    {label:'yamamoto',value:'yamada'},   
                ],
                operatorOptions:[
                    {label:'<',value:'<'},
                    {label:'<=',value:'<='},
                    {label:'=',value:'='},
                    {label:'>=',value:'>='},
                    {label:'>',value:'>'},
                ],
            }  
        },
        created:async function(){
            let result = await axios.get('/api/tasks')
            this.tasks = result.data
        },
        mounted:async function() {
            
        },
        methods: {
            filterArray:function(){
                this.$refs.filter.filterArray()
            },
            showModal:function(){
                this.$refs.modal.openModal()
            },
            showNotice:function(){
                this.$refs.notice.showNotice('実行中')
            },
        },
    }
</script>
<style>
    .space {
        width:100%;
        height:50px;
        border:1px solid black;
    }
    .button {
        position:fixed;
        top:0;
        left:0;
    }
</style>
