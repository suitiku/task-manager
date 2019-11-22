<!--単一ファイルコンポーネントのテンプレートファイル-->
<template>
    <div class="container">
        <modal ref="modal" v-model="modal">
            <h3>追加したいタグを選択してください（複数可）</h3>
            <list-box ref="list" v-model="new_tags" table="tags" is_multiple/>
            <button class="btn btn-outline-primary mx-auto d-block" v-on:click="addTags()">選択したタグを追加する</button>
        </modal>
        <div class="tag-list-wrapper">
            <tag v-for="(tag,index) in tags" v-bind:tag="tag" v-bind:id="id" v-bind:key="index" />
            <div class="add-tag-wrapper">
                <div v-show="add_detail" class="add-detail">タグを追加します</div>
                <div class="add-tag" v-on:click="openModal()" v-on:mouseover="add_detail = true" v-on:mouseleave="add_detail = false"><i class="fas fa-plus-circle"></i></div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                tags:[],
                new_tags:[],
                add_detail:false,
                modal:false,
            }  
        },
        props: {
            id: {
                type:Number,
                required:false
            },
        },
        watch:{
            id:function(){
                this.fetchTags()
            }
        },
        created:function(){
            
        },
        mounted:async function(){
            
        },
        methods: {
            fetchTags:async function(){
                let result = await axios.get('/api/tasks/' + this.id)
                this.tags = result.data.tags
            },
            openModal:function(){
                this.$refs.list.resetValue()
                this.$refs.modal.openModal()
            },
            addTags:async function(){
                let postObject = {
                    task_id:this.id,
                    tag_id:this.new_tags
                }
                try{
                    await axios.post('/api/tag_task',postObject)
                    this.fetchTags()
                    this.$refs.modal.closeModal()
                }catch(error){
                    console.log(error)
                }
            }
        }
    }
</script>
<style scoped>
    .tag-list-wrapper {
        position:relative;
        width:50%;
        display:flex;
        border:1px solid black;
    }
    
    
    .add-tag-wrapper {
        position:relative;
        margin:0 0.5em;
    }
    .add-tag{
        position:absolute;
        bottom:0;
        transform-origin:center bottom;
        overflow:hidden;
        width:3.5em;
        height:1.3em;
        padding:2px;
        text-align:center;
        border-radius:0.1em 0.1em 0 0;
        cursor:pointer;
        transition:all 0.3s;
        border-left:1px solid black;
        border-top:1px solid black;
        border-right:1px solid black;
        background:white;
    }
    .add-tag:hover {
        height:4.0em;
        transition:all 0.1s;
    }
    .add-detail {
        position:absolute;
        z-index:2;
        opacity:0.9;
        bottom:6em;
        left:-2.75em;
        display:block;
        width:10em;
        max-height:4em;
        overflow:hidden;
        background:grey;
        color:white;
        text-align:center;
        border-radius:0.3em;
        font-size:0.3em;
        padding:0.3em;
        user-select:none;
        animation:tag 0.4s ;
    }
    @keyframes tag {
        0% {
            transform:scale(0,0);
        }
        100% {
            transform:scale(1.0,1.0);
        }
    }
</style>
