<!--タグリスト表示編集用コンポーネント-->
<!--今後の改修ポイント-->
<!--１．削除した際のgetDisplayTagNumber()の起動-->
<!--２．画面サイズを変更した際のgetDisplayTagNumber()の起動-->
<template>
    <div class="container">
        <!--モーダル-->
        <modal ref="modal" v-model="modal">
            <h3>追加したいタグを選択してください（複数可）</h3>
            <list-box ref="list" v-model="new_tags" table="tags" is_multiple/>
            <button class="btn btn-outline-primary mx-auto d-block" v-on:click="addTags()">選択したタグを追加する</button>
        </modal>
        <!--タグ表示部-->
        <div class="tag-list-wrapper">
            <tag v-for="index in display_tag_number" v-bind:tag="tags[index - 1]" v-bind:id="id" v-bind:key="index" />
            <div class="add-tag-wrapper">
                <div v-show="add_detail" class="add-detail">タグを追加します</div>
                <div class="add-tag" v-on:click="openModal()" v-on:mouseover="add_detail = true" v-on:mouseleave="add_detail = false"><i class="fas fa-plus-circle"></i></div>
            </div>
            <div class="display-all-tags-button-wrapper" v-show="display_all_tags_button">
                <div v-show="info_detail" class="add-detail">省略されたタグを表示します</div>
                <div class="display-all-tags-button" v-on:click="displayAllTags()" v-on:mouseover="info_detail = true" v-on:mouseleave="info_detail = false">i</div>
            </div>
        </div>
        <div v-show="all_tags" class="all-tags">
            <div v-for="(tag,index) in tags" class="all-tag" v-bind:style="{background:tag.color}">{{tag.name}}</div>
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                display_tag_number:0,
                tags:[],
                new_tags:[],
                add_detail:false,
                info_detail:false,
                display_all_tags_button:false,
                all_tags:false,
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
            id:async function(){
                await this.fetchTags()
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
                this.getDisplayTagNumber()
            },
            openModal:function(){
                this.$refs.list.resetValue()
                this.$refs.modal.openModal()
            },
            addTags:async function(){
                // バリデーション（重複するとエラーになるので）
                await this.fetchTags()
                for(let tag of this.tags){
                    let index = this.new_tags.indexOf(tag.id)
                    if(index != -1){
                        this.new_tags.splice(index,1)
                    }
                }
                
                if(Object.keys(this.new_tags).length == 0){
                    this.$refs.modal.closeModal()
                    return
                }
                
                let postObject = {
                    task_id:this.id,
                    tag_id:this.new_tags
                }
                try{
                    await axios.post('/api/tag_task',postObject)
                    this.$refs.modal.closeModal()
                    this.display_tag_number = 0
                    this.tags = []
                    await this.fetchTags()
                }catch(error){
                    console.log(error)
                }
            },
            displayAllTags:function(){
                if(event.target.className == 'display-all-tags-button'){
                    event.target.className = 'display-all-tags-button button-active'
                    this.all_tags = true
                }else{
                    event.target.className = 'display-all-tags-button'
                    this.all_tags = false
                }  
            },
            getDisplayTagNumber:function(){
                let el = document.getElementsByClassName('tag-list-wrapper')
                let wrapper_width = el[0].clientWidth
                let tag_width = this.getPixel(4.5)
                this.display_tag_number = Math.floor(wrapper_width / tag_width)
                if(this.tags.length < this.display_tag_number){
                    this.display_tag_number = this.tags.length
                }else{
                    this.display_tag_number -- //省略タグ表示ボタン用
                    this.display_all_tags_button = true
                }
            },
            getPixel:function(em) {
                if (em === undefined) { em = 1; }
                let div = $('<div style="width:' + em + 'em;"></div>').appendTo('body');
                let pixel = div.width();
                div.remove();
                return pixel;
            }
        }
    }
</script>
<style scoped>
    .tag-list-wrapper {
        position:relative;
        width:100%;
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
    .display-all-tags-button-wrapper {
        position:absolute;
        bottom:0;
        right:0;
        border-left:1px solid black;
        border-top:1px solid black;
        border-right:1px solid black;
    }
    .display-all-tags-button {
        text-align:center;
        font-size:60%;
        font-weight:bold;
        width:1em;
        height:1.3em;
        overflow:hidden;
        background:whitesmoke;
        user-select:none;
        transition:all 0.2s;
    }
    .button-active {
        height:0.7em;
        background:orange;
        transition:all 0.2s;
    }
    .all-tags {
        display:flex;
        flex-wrap:wrap;
        position:absolute;
        z-index:2;
        width:33%;
        margin-top:1em;
        background:gainsboro;
        opacity:0.9;
        overflow:hidden;
        animation:alltags 0.4s;
    }
    @keyframes alltags {
        0% {
            max-height:0;
        }
        100% {
            max-height:500px;
        }
    }
    .all-tag {
        margin:0.3em;
        padding:0.3em;
        border-radius:0.3em;
    }
</style>
