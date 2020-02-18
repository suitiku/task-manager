<!--テスト用コンポーネント-->
<template>
    <div class="container">
        <div v-for="tes in test">
            <span>id: {{tes.id}} , name: {{tes.name}}</span>
        </div>
        <filter-box v-model="test" v-bind:targetArray="tasks" v-bind:filterOptions="filterOptions" />
        
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
                filterOptions:[
                    {label:'優先度',value:'priority',type:'star'},          
                    {label:'難易度',value:'difficulty',type:'star'},            
                    {label:'作成日',value:'start_date',type:'date'},
                    {label:'状態',value:'state_id',type:'options',options:[]}
                ],
            }  
        },
        created:async function(){
            let result = await axios.get('/api/tasks')
            this.tasks = result.data
            
            let stateOptions = []
            let states = await axios.get('/api/states')
            for(let state of states.data){
                stateOptions.push({label:state.name,value:state.id})
            }
            this.$set(this.filterOptions[3],'options',stateOptions)
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
            showToolTip:function(){
                this.$refs.tooltip.toggleToolTipVisible()
            }
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
    .tool-tip-area {
        width:50%;
        height:200px;
        background:grey;
    }
</style>
