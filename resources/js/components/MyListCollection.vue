<!--リスト一覧-->
<template>
    <div>
        <!--詳細表示用モーダル-->
        <modal ref="detailModal" v-model="detailModal">
            <my-list v-bind:listId="listId" v-bind:editMode="editMode" />
        </modal>
        
        <h2>リスト一覧</h2>
        <div id="list-wrapper">
            <div v-for="(myList,index) in myLists" class="list" v-on:click="showListDetail(myList.id)">
                <h3>{{myList.name}}</h3>
                <p>{{myList.description}}</p>
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
                listId:null,
                editMode:false
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
            }
        }
    }
</script>
<style lang="scss" scoped>
    #list-wrapper {
        display:flex;
        .list {
            width:10em;
            border:1px solid grey;
            margin:1em;
            padding:1em;
            cursor:pointer;
        }
    }
</style>
