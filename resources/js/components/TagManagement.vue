<!--タグ管理用コンポーネント-->
<template>
    <div>
        <!--通知-->
        <notice ref="notice" />
        
        <!--新規／編集用モーダル-->
        <modal ref="modal" v-model="modal">
            <input type="text" v-model="editedTag.name" />
            <textarea v-model="editedTag.overview" />
            <color-picker v-model="editedTag.color" v-bind:colorOptions="colorOptions" />
            <div class="buttons">
                <button v-if="editedTag.id == ''" type="button" class="btn btn-outline-primary mx-auto d-block" v-on:click="createTag()">登録</button>
                <button v-else type="button" class="btn btn-outline-primary mx-auto d-block" v-on:click="editTag()">編集</button>
                <button v-if="editedTag.id != ''" type="button" class="btn btn-outline-danger mx-auto d-block" v-on:click="deleteTag()">削除</button>
            </div>
        </modal>
        
        <!--一覧表示（使用数付き）-->
            <div v-for="(tagColor,colorIndex) in tags" class="display-tags">
                <div v-for="(tag,index) in tagColor" class="tag-wrapper">
                    <div v-bind:style="{background:tag.color}" class="tag" v-on:click="showModal(tag)">
                        <span>{{tag.name}}</span>
                    </div>
                    <span>{{tag.tasks_count}}</span>
                </div>
            </div>
        <button type="button" class="btn btn-outline-primary mx-auto d-block" v-on:click="showModal()">タグを追加する</button>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                tags:[],
                displayedTags:{},
                modal:false,
                editId:'',
                editedTag:{},
                colorOptions:['#ef857d','#89c997','#82cddd','#d4d9df','#fdd35c','#c7a5cc'],
            }  
        },
        props: {
            user_id:{
                type:[String,Number],
                default:0,
                required:true
            }
        },
        watch:{
            
        },
        created:function(){
            
        },
        mounted:function(){
            // タグを取得
            this.fetchTags()
            this.init()
        },
        computed:{
            
        },
        methods: {
            init:function(){
                this.editedTag = {
                    id:'',
                    user_id:this.user_id,
                    name:'',
                    overview:'',
                    color:''
                }
            },
            fetchTags: async function(){
                 // タグの取得
                let result = await axios.get('/api/mytags',{
                                                params:{user_id:this.user_id,}
                                            })
                this.tags = result.data
            },
            showModal:function(tag = null){
                if(tag && tag.id != ''){
                    this.editedTag = JSON.parse(JSON.stringify(tag))
                }else{
                    this.init()
                }
                this.$refs.modal.openModal()
            },
            createTag:async function(){
                delete this.editedTag.id //id削除
                try{
                    await axios.post('/api/tags',this.editedTag)
                    
                    //editedTagを初期化
                    this.init()
                    
                    //終了処理
                    this.$refs.notice.showNotice('タグを追加しました')
                    this.fetchTags()
                }catch(error){
                    //editedTagを初期化
                    this.init()
                    
                    this.$refs.notice.showNotice('タグの追加に失敗しました')
                    console.log(error)
                }
            },
            editTag:async function(){
                let tagId = this.editedTag.id
                let postObject = {
                    user_id:this.editedTag.user_id,
                    name:this.editedTag.name,
                    overview:this.editedTag.overview,
                    color:this.editedTag.color,
                }
                try{
                    await axios.put('/api/tags/' + tagId,postObject)
                    
                    //editedTagを初期化
                    this.init()
                    
                    //終了処理
                    this.$refs.notice.showNotice('タグを編集しました')
                    this.fetchTags()
                    
                }catch(error){
                    //editedTagを初期化
                    this.init()
                    this.$refs.notice.showNotice('タグの編集に失敗しました')
                    console.log(error)
                }
            },
            deleteTag:async function(){
                let result = window.confirm('このタグを削除します。\nこの処理は取り消しできません。\nよろしいですか？')
                if(result){
                    try{
                        await axios.delete('/api/tags/' + this.editedTag.id)
                        
                        //editedTagを初期化
                        this.init()
                        this.$refs.modal.closeModal()
                        
                        //終了処理
                        this.$refs.notice.showNotice('タグを削除しました')
                        this.fetchTags()
                    }catch(error){
                        //editedTagを初期化
                        this.init()
                        
                        this.$refs.notice.showNotice('タグの削除に失敗しました')
                        console.log(error)
                }
                }
            }
        }
    }
</script>
<style scoped>
    .display-tags {
        display:flex;
        flex-wrap:wrap;
        align-items:center;
        margin-bottom:1em;
    }
    .tag-wrapper {
        display:flex;
        align-items:center;
        margin:0.3em 1em;
        cursor:pointer;
    }
    .tag-wrapper span {
        font-family: Impact,Charcoal;
    }
    .tag {
        margin-right:0.3em;
        display:inline-block;
        min-width:5em;
        padding:0.3em 0.5em;
        border:1px solid grey;
        border-radius:0.2em;
        opacity:0.9;
    }
    .tag span{
        display:block;
        width:100%;
        text-align-last: justify;
    }
    input,textarea {
        width:100%;
        display:block;
        margin:0.5em;
        padding:0.3em;
        border:1px solid grey;
        border-radius:0.3em;
    }
    
    .tag span {
        font-weight:bold;
        letter-spacing:0.1em;
    }
    .buttons {
        display:flex;
    }
    
</style>
