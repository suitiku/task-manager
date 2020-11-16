<!--リスト一覧-->
<template>
    <div>
        <!--詳細表示用モーダル-->
        <modal ref="detailModal" v-model="detailModal">
            <my-list v-bind:listId="listId" v-bind:editMode="false" />
        </modal>
        
        <!--編集用モーダル-->
        <modal ref="editModal" v-model="editModal">
            <!--編集用コントロールパネル-->
            <div class="fixed-bar">
                <i class="fas fa-arrow-circle-right fa-lg" v-on:click="addColumn()"></i>
                <i class="fas fa-arrow-circle-down fa-lg" v-on:click="addItem()"></i>
                <i class="far fa-save fa-lg" v-on:click="saveList()"></i>
            </div>
            <my-list v-bind:listId="listId" v-bind:editMode="true" ref="editingList" />
        </modal>
        
        <!--リスト情報表示用モーダル-->
        
        <h2>リスト一覧</h2>
        <div id="list-wrapper">
            <div v-for="(myList,index) in myLists" class="list">
                <h3>{{myList.name}}</h3>
                <p>{{myList.description}}</p>
                <div class="icons">
                    <i class="far fa-list-alt fa-lg" v-on:click="showListDetail(myList.id)"></i>
                    <i class="far fa-edit fa-lg" v-on:click="showEditModal(myList.id)"></i>
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
            }  
        },
        props: {
            user_id:{
                type:[Number,String],
                default:null,
                required:true
            }
            
        },
        watch:{
            detailModal:function(newVal,oldVal){
                if(newVal == false && oldVal == true){
                    this.listId = null
                }
            },
            editModal:function(newVal,oldVal){
                if(newVal == false && oldVal == true){
                    this.listId = null
                }
            }
        },
        created:async function(){
            console.log(this.user_id)
            let result = await axios.get('/api/mylists', {params:{user_id:this.user_id}})
            this.myLists = result.data
        },
        mounted:function(){
            
        },
        methods: {
            showListDetail:function(id){
                this.listId = id
                this.$refs.detailModal.openModal()
            },
            showEditModal:function(id){
                this.listId = id
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
                this.$refs.editingList.createNewList()
                this.$refs.editModal.openModal()
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
