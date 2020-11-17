<!--リスト一覧-->
<template>
    <div>
        <!--通知-->
        <notice ref="notice" />
        
        <!--詳細表示用モーダル-->
        <modal ref="detailModal" v-model="detailModal">
            <my-list v-bind:userId="user_id" v-bind:listId="listId" v-bind:editMode="false" />
        </modal>
        
        <!--編集用モーダル-->
        <modal ref="editModal" v-model="editModal">
            <!--編集用コントロールパネル-->
            <div class="fixed-bar">
                <i class="fas fa-arrow-circle-right fa-lg" v-on:click="addColumn()"></i>
                <i class="fas fa-arrow-circle-down fa-lg" v-on:click="addItem()"></i>
                <i class="far fa-save fa-lg" v-on:click="saveList()"></i>
                <i class="fas fa-trash fa-lg" v-on:click="deleteList()"></i>
            </div>
            <my-list v-model="editedList" v-bind:userId="user_id" v-bind:listId="listId" v-bind:editMode="true" ref="editingList" />
        </modal>
        
        <!--リスト情報表示用モーダル-->
        
        <h2>リスト一覧</h2>
        <div id="list-wrapper">
            <div v-for="(myList,index) in myLists" class="list">
                <h3>{{myList.name}}</h3>
                <p>{{myList.description}}</p>
                <div class="icons">
                    <i class="far fa-list-alt fa-lg" v-on:click="showListDetail(myList.id)"></i>
                    <i class="far fa-edit fa-lg" v-on:click="showEditModal(myList.id,index)"></i>
                    <i class="fas fa-info-circle fa-lg"></i>
                </div>
            </div>
            <div class="list">
                <i class="fas fa-plus-circle fa-lg" v-on:click="showNewListModal()"></i>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'
    export default {
        data:function(){
            return {
                myLists:[],
                detailModal:false,
                editModal:false,
                listId:null,
                targetIndex:null,
                editedList:{} //編集／新規のList（v-model）
            }  
        },
        props: {
            user_id:{
                type:[Number,String],
                default:null,
                required:false
            }
            
        },
        watch:{
            detailModal:function(newVal,oldVal){
                if(newVal == false && oldVal == true){
                    this.listId = null
                    this.targetIndex = null
                }
            },
            editModal:function(newVal,oldVal){
                if(newVal == false && oldVal == true){
                    this.listId = null
                    this.targetIndex = null
                }
            },
            editedList:function(newVal,oldVal){
                if(this.editedList.id){
                    console.log('ついか')
                    this.myLists.unshift(this.editedList)
                }
            }
        },
        created:function(){
            this.getLists()
        },
        mounted:function(){
            
        },
        methods: {
            //ユーザーIDを基準に全リストを取得
            getLists:async function(){
                let result = await axios.get('/api/mylists', {params:{user_id:this.user_id}})
                this.myLists = result.data
            },
            //リストIDを基準に取得
            getList:async function(id){
                let result = await axios.get('/api/mylists/' + id)
            },
            updateLists:function(id,index){
                let list =  this.getList(id)
            },
            showListDetail:function(id){
                this.listId = id
                this.$refs.detailModal.openModal()
            },
            showEditModal:function(id,index){
                this.listId = id
                this.targetIndex = index
                this.$refs.editModal.openModal()
            },
            addItem:function(){
                this.$refs.editingList.addItem()
            },
            addColumn:function(){
                this.$refs.editingList.showAddColumnModal()
            },
            saveList:function(){
                this.$refs.editingList.saveList()
            },
            showNewListModal:function(){
                this.listId = null
                this.editedList = {}
                this.$refs.editingList.createNewList()
                this.$refs.editModal.openModal()
            },
            deleteList:async function(){
                let confirmResult = confirm('このリストを削除します。よろしいですか？')
                if(!confirmResult)return
                    
                try{
                    let result = await axios.delete('/api/lists/' + this.listId)
                    if(result.data){
                        this.$refs.notice.showNotice('リストを削除しました')
                        this.myLists.splice(this.targetIndex,1)
                        this.listId = null
                        this.targetIndex = null
                        this.$refs.editModal.closeModal()
                    }
                }catch(error){
                    this.$refs.notice.showNotice('リストの削除に失敗しました')
                    console.log(error)
                }
                
            }
        }
    }
</script>
<style lang="scss" scoped>
    .fixed-bar {
        position:fixed;
        left:calc(50% - 1.7em);
        top:3em;
        z-index:10;
        border:3px solid grey;
        padding:0.5em;
        border-radius:0.2em;
        background-color:rgba(white,0.9);
        i {
            margin:0.2em;
            cursor:pointer;
        }
    }
    #list-wrapper {
        display:flex;
        flex-wrap:wrap;
        .list {
            display:flex;
            flex-direction:column;
            justify-content:center;
            width:10em;
            border:3px solid grey;
            border-radius:0.5em;
            margin:1em;
            padding:1em;
            h3 {
                font-family: 'M PLUS 1p', sans-serif;
                font-weight:900;
            }
            .icons {
                i {
                    margin:0.2em;
                    cursor:pointer;
                }
            }
        }
    }
</style>
