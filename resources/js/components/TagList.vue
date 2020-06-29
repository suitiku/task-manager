<!--タグ付け替えコンポーネント-->
<!--選択されたタグIDを送出-->
<!--今後の追加機能-->
<template>
    <div class="container">
        <!--通知-->
        <notice ref="notice" />
        
        <!--表示部-->
        <!--検索-->
        <input v-model="searchKeyword" class="input-inline" ref="searchTag" type="text" placeholder="タグを検索" />
        
        <!--一覧表示-->
        <div class="tag-list-container" v-for="(tagColor,colorIndex) in tags">
            <div ref="tag" v-for="(tag,index) in tagColor" v-bind:class="setClass(colorIndex,index)" v-bind:style="{background:tag.color}" v-on:click="selectTag(tag.id)">
                <span>{{tag.name}}</span>
            </div>
        </div>
        <!--追加部分-->
        <div class="creation-tag-area">
            <input class="input-inline" ref="newTag" type="text" placeholder="タグを新規登録" v-on:keydown="createTag()" />
            <color-picker v-model="newTagColor" v-bind:colorOptions="colorOptions" />
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                tags:[],
                selectedTags:this.value,
                colorOptions:['#ef857d','#89c997','#82cddd','#d4d9df','#fdd35c','#c7a5cc'],
                newTagColor:'#ef857d',
                searchKeyword:'',
            }
        },
        props: {
            userId:{
                type:[String,Number],
                required:false
            },
            value:{
                type:[String,Number,Array],
                required:false
            }
        },
        watch:{
            searchKeyword:function(newVal,oldVal){
                if(newVal == ''){
                    this.fetchTags()
                }else{
                    this.searchTags()
                }
            }
        },
        created:function(){
        },
        mounted:function(){
            
        },
        methods: {
            init:async function(){
                 // ユーザーに紐付いたタグの取得
                this.fetchTags()
            },
            fetchTags:async function(){
                if(!this.userId)return
                
                // userIdに基づきすべてタグを取得
                let result = await axios.get('/api/mytags',{
                                                params:{user_id:this.userId,}
                                            })
                this.tags = result.data
            },
            setClass:function(colorIndex,index){
                if(this.value.indexOf(this.tags[colorIndex][index].id) == -1){
                    return 'tag'
                }else{
                    return 'tag selected'
                }
            },
            selectTag:function(id){
                if(this.value.indexOf(id) == -1){
                    this.value.push(id)
                }else{
                    this.value.splice(this.value.indexOf(id),1)
                }
            },
            createTag:async function(){
                if(event.keyCode == 13){
                    let postObject = {
                        user_id:this.userId,
                        name:event.target.value,
                        color:this.newTagColor
                    }
                    try{
                        let result = await axios.post('/api/tags',postObject)
                        this.$refs.notice.showNotice('タグを追加しました')
                        // インプットをリセット
                        this.$refs.newTag.value = ''
                        // タグ一覧を更新
                        this.fetchTags()
                    }catch(error){
                        this.$refs.notice.showNotice('タグの追加に失敗しました')
                        console.log(error)
                    }
                    
                }
            },
            searchTags:async function(){
                try{
                    let result = await axios.get('/api/tags/search',{
                        params:{
                            user_id:this.userId,
                            keyword:this.searchKeyword
                        }
                    })
                    this.tags = result.data
                }catch(error){
                    console.log(error)
                }
            }
            
        }
    }
</script>
<style scoped>
    .tag-list-container {
        display:flex;
        flex-wrap:wrap;
    }
    .tag {
        min-width:5em;
        color:grey;
        border:1px solid grey;
        border-radius:0.3em;
        padding:0.4em;
        margin:calc(0.2em + 1px) calc(0.3em + 1px);
        background:white;
        font-size:75%;
        opacity:0.5;
        cursor:pointer;
        transition:opacity 0.3s;
    }
    .tag:hover {
        opacity:0.8;
    }
    .tag span {
        display:block;
        width:100%;
        text-align-last: justify;
    }
    .selected {
        opacity:1.0;
        border:2px solid grey;
        margin:0.2em 0.3em;
        transition:opacity 0.3s;
    }
    .creation-tag-area {
        display:flex;
        align-items:center;
    }
    .input-inline {
        margin:0.5em 0.3em;
        width:15em;
        display:block;
        padding:0.3em;
        border:1px solid grey;
        border-radius:0.3em;
    }
</style>
