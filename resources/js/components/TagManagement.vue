<!--タグ管理用コンポーネント-->
<template>
    <div>
        <!--通知-->
        <notice ref="notice" />
        
        <!--新規／編集用モーダル-->
        <modal ref="modal" v-model="modal">
            <input type="text" v-model="editedTag.name" />
            <textarea v-model="editedTag.overview" />
            <!--<tag-cloud v-model="editedTag.color" v-bind:options="colorOptions" />-->
            <color-picker v-model="editedTag.color" v-bind:colorOptions="colorOptions" />
            <button v-if="editedTag.id == ''" type="button" class="btn btn-outline-primary mx-auto d-block" v-on:click="createTag()">登録</button>
            <button v-else type="button" class="btn btn-outline-primary mx-auto d-block" v-on:click="editTag()">編集</button>
        </modal>
        
        <!--とりあえず一覧表示（使用数付き）-->
        <div class="display-tags">
            <div v-for="(tag,index) in tags" class="tag-wrapper">
                <div v-bind:style="{background:tag.color}" class="tag" v-on:click="showModal(tag)">
                    <span>{{tag.name}}</span>
                </div>
                <span>{{countTasks(tag)}}</span>
            </div>
            <i class="fas fa-plus-circle" v-on:click="showModal()"></i>
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                tags:[],
                modal:false,
                editId:'',
                editedTag:{},
                colorOptions:['#ef857d','#89c997','#fdd35c','#82cddd','#d4d9df','#c7a5cc']
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
            countTasks:function(tag){
                if(!tag.tasks){
                    return 0
                }else{
                    return tag.tasks.length
                }
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
            }
        }
    }
</script>
<style scoped>
    .display-tags {
        display:flex;
        flex-wrap:wrap;
        align-items:center;
    }
    .tag-wrapper {
        display:flex;
        align-items:center;
        margin:1em;
        cursor:pointer;
    }
    .tag-wrapper span {
        font-family: Impact,Charcoal;
    }
    .tag {
        margin-right:0.3em;
        display:inline-block;
        padding:0.5em;
        border:1px solid grey;
        border-radius:0.2em;
        opacity:0.9;
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
    
</style>
