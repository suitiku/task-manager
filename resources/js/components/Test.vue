<!--テスト用コンポーネント-->
<template>
    <div class="container">
        <spread-options ref="spreadOptions" />
        <!--<modal ref="modal" v-model="modal">-->
        <!--    <div class="wall">test</div>-->
        <!--</modal>-->
        <button class="button center" v-on:click="spreadOptions()">spread</button>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                modal:false,
                modal2:false,
                test:'',
                hoge:0,
                projects:[],
                taskIds:[4,8,9],
                tasks:[],
                task:{},
                tags:[],
                selectedTagId:0,
                selectedTagIds:[],
                filteredTasks:[],
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
                    {label:'締切',value:'dead_line',type:'date'},
                ],
                colorOptions:['#ef857d','#89c997','#fdd35c','#82cddd','#d4d9df','#c7a5cc'],
                userId:1,
                waiting:false,
                items:[
                    {label:'りんご',value:'apple'},
                    {label:'バナナ',value:'banana'},
                    {label:'ドラゴンフルーツ',value:'dragon fruit'},
                    {label:'アンデスメロン',value:'andes melon'}
                ],
                numerator:80
            }  
        },
        created:async function(){
            //プロジェクト
            // let projectResult = await axios.get('/api/projects')
            // this.projects = projectResult.data
            
            //タスク全取得
            // let result = await axios.get('/api/tasks')
            // this.tasks = result.data
            
            //タグ全取得
            // let tagsResult = await axios.get('/api/tags')
            // for(let tag of tagsResult.data){
            //     this.tags.push({label:tag.name,value:tag.id})
            // }
            
            // this.addFilters() //状態を追加
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
            showModal2:function(){
                this.$refs.modal2.openModal()
            },
            showCong:function(){
                this.$refs.cong.openCong('お疲れさまです！')
            },
            showNotice:function(){
                this.$refs.notice.showNotice('だめです！','error')
            },
            showToolTip:function(){
                this.$refs.tooltip.toggleToolTipVisible()
            },
            sortArray:function(){
                console.log('hoge')
                this.$refs.sortArrayComponent.sortArray()
            },
            changeTask:function(){
                this.task = this.tasks[2]
            },
            toggleWaiting:function(){
                if(!this.waiting){
                    this.$refs.waiting.enableWaiting('現在処理中です')
                }else{
                    this.$refs.waiting.disableWaiting()
                }
            },
            showHeadline:function(){
                this.test = !this.test
            },
            getTags:function(){
                this.$refs.tagList.init()
            },
            addItem:function(){
                this.$refs.mylist.addItem()
            },
            addColumn:function(){
                this.$refs.mylist.showAddColumnModal()
            },
            saveList:function(){
                this.$refs.mylist.saveList()
            },
            sortList:function(){
                this.$refs.mylist.sortList(2)
            },
            spreadOptions:function(){
                this.$refs.spreadOptions.expand(event)
            }
        },
    }
</script>
<style>
    .button {
        position:relative;
        cursor:pointer;
        padding:0.5em;
        border:1px solid grey;
        border-radius:0.2em;
        /*z-index:30;*/
    }
    .hoge {
        display:flex;
    }
    .space {
        width:100%;
        height:50px;
        border:1px solid black;
    }
    .tool-tip-area {
        width:50%;
        height:200px;
        background:grey;
    }
    .wall {
        width:100%;
        height:5000px;
        background:orange;
    }
    .slider-area {
        width:50%;
    }
    .congratulation-content {
        border:2px solid red;
    }
    .center {
        position:relative;
        left:20em;
        top:20em;
    }
</style>
