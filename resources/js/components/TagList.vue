<!--タグ付け替えコンポーネント-->
<!--今後の追加機能-->
<!--①カラーで並べ替え-->
<template>
    <div class="container">
        <!--通知-->
        <notice ref="notice" />
        
        <!--表示部-->
        <div class="tag-list-container">
            <div ref="tag" v-for="(tag,index) in tags" v-bind:class="setClass(index)" v-bind:style="{background:tag.color}" v-on:click="selectTag(tag.id)">
                <span>{{tag.name}}</span>
            </div>
        </div>
        <!--追加部分-->
        <input class="input-inline" ref="newTag" type="text" placeholder="タグを新規登録" v-on:keydown="createTag()" />
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                userId:'',
                tags:[],
                selectedTags:[],
            }  
        },
        props: {
            //タスク単位のタグを管理する
            taskId:{
                type:[String,Number],
                required:false
            }
        },
        watch:{
            // 途中でtaskIdが設定されたら初期化
            taskId:function(){
                this.init()  
            },
            //タグを選択して付け替え
            selectedTags:async function(newVal,oldVal){
                // 初回は飛ばす
                if(oldVal.length == 0){return }
                
                // 投入用オブジェクト作成
                let tagsObject = {
                    task_id:this.taskId,
                    tag_ids:this.selectedTags
                }
                
                // 書き込み
                try{
                    await axios.put('/api/tag_task',tagsObject)
                    this.$refs.notice.showNotice('タグを変更しました')
                }catch(error){
                    this.$refs.notice.showNotice('タグの変更に失敗しました')
                    console.log(error)
                }
            },
        },
        created:function(){
        },
        mounted:function(){
            this.init()
        },
        methods: {
            init:async function(){
                if(!this.taskId){return }
                 // タスクに紐付いたタグの取得
                let result = await axios.get('/api/tasks/' + this.taskId)
                this.selectedTags = result.data.tags.map(el => el.id)
                
                // userIdを設定
                this.userId = result.data.user_id
                
                this.fetchTags()
            },
            fetchTags:async function(){
                // userIdに基づきすべてタグを取得
                let result = await axios.get('/api/mytags',{
                                                params:{user_id:this.userId,}
                                            })
                this.tags = result.data
            },
            setClass:function(index){
                if(this.selectedTags.indexOf(this.tags[index].id) == -1){
                    return 'tag'
                }else{
                    return 'tag selected'
                }
            },
            selectTag:function(id){
                if(this.selectedTags.indexOf(id) == -1){
                    this.selectedTags.push(id)
                }else{
                    this.selectedTags.splice(this.selectedTags.indexOf(id),1)
                }
            },
            createTag:async function(){
                if(event.keyCode == 13){
                    let postObject = {
                        user_id:this.userId,
                        name:event.target.value,
                        color:'#ef857d'
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
            
        }
    }
</script>
<style scoped>
    .tag-list-container {
        display:flex;
        flex-wrap:wrap;
    }
    .tag {
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
    .selected {
        opacity:1.0;
        border:2px solid grey;
        margin:0.2em 0.3em;
        transition:opacity 0.3s;
    }
    .input-inline {
        margin:0.5em 0.3em;
        width:20em;
        display:block;
        padding:0.3em;
        border:1px solid grey;
        border-radius:0.3em;
    }
    
</style>
