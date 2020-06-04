<!--テスト用コンポーネント-->
<template>
    <div class="container">
        <div v-show="test">
            <headline-2>テスト</headline-2>
        </div>
        
        <!--<waiting v-model="waiting" ref="waiting" />-->
        <!--<notice ref="notice" />-->
        <!--モーダル-->
        {{selectedTagIds}}
        <modal ref="modal" v-model="modal">
            <tag-list v-model="selectedTagIds" userId="1" />
        </modal>
        <!--<button class="button" v-on:click="showCong()">congratulation!</button>-->
        <!--<button class="button" v-on:click="showModal()">modal</button>-->
        <button class="button" v-on:click="showHeadline()">H2</button>
        <!--<button class="button" v-on:click="showNotice()">Notice</button>-->
        <!--<button class="button" v-on:click="toggleWaiting()">notice</button>-->
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                modal:false,
                test:false,
                hoge:'',
                project:{},
                taskIds:[4,8,9],
                tasks:[],
                task:{},
                tags:[],
                selectedTagId:0,
                selectedTagIds:[1,3,5],
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
                ],
                colorOptions:['#ef857d','#89c997','#fdd35c','#82cddd','#d4d9df','#c7a5cc'],
                userId:1,
                waiting:false,
                items:[
                    {label:'りんご',value:'apple'},
                    {label:'バナナ',value:'banana'},
                    {label:'ドラゴンフルーツ',value:'dragon fruit'},
                    {label:'アンデスメロン',value:'andes melon'}
                ]
            }  
        },
        created:async function(){
            //プロジェクト
            let projectResult = await axios.get('/api/projects/1')
            this.project = projectResult.data
            
            //タスク全取得
            let result = await axios.get('/api/tasks')
            this.tasks = result.data
            this.task = this.tasks[1]
            
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
        z-index:30;
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
</style>
