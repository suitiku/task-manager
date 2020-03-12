<!--テスト用コンポーネント-->
<template>
    <div class="container">
        <!--<tag-management user_id="1" />-->
        {{selectedColor}}
        <color-picker v-model="selectedColor" v-bind:colorOptions="colorOptions" />
        
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
                tags:[],
                selectedTagId:0,
                selectedColor:'',
                sortColumns:[
                    {columnName:'priority',columnLabel:'優先度'},
                    {columnName:'difficulty',columnLabel:'難易度'},
                ],
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
                ],
                colorOptions:['#ef857d','#89c997','#fdd35c','#82cddd','#d4d9df','#c7a5cc']
            }  
        },
        created:async function(){
            //タスク全取得
            let result = await axios.get('/api/tasks')
            this.tasks = result.data
            
            //タグ全取得
            let tagsResult = await axios.get('/api/tags')
            for(let tag of tagsResult.data){
                this.tags.push({label:tag.name,value:tag.id})
            }
            
            this.addFilters() //状態を追加
        },
        mounted:async function() {
            
        },
        methods: {
            addFilters: async function(){
                // statesフィルターオプションを追加
                let statesResult = await axios.get('api/states')
                let states = []
                for(let state of statesResult.data){
                    states.push({label:state.name,value:state.id})
                }
                this.filterOptions.push({label:'状態',value:'state_id',type:'options',options:states})
            },
            
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
            },
            sortArray:function(){
                console.log('hoge')
                this.$refs.sortArrayComponent.sortArray()
            }
        },
    }
</script>
<style>
    .hoge {
        display:flex;
    }
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
