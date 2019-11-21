<!--タグ表示用コンポーネント-->
<!--今後の改修ポイント-->
<!--１．削除した際に通知コンポーネントを起動（成功／失敗）-->
<!--２．色設定の追加-->
<template>
    <div class="tag-root-wrapper" v-bind:style="deactive">
        <!--<div v-show="detail" class="tag-detail">{{tag.overview}}</div>-->
        <div v-show="detail" class="tag-detail">ダブルクリックでこのタグを削除します</div>
        <div class="tag-wrapper" v-bind:style="{background:tag.color}" v-on:dblclick="deleteTag" v-on:mouseover="detail = true" v-on:mouseleave="detail = false" >
            <div class="tag-label" v-bind:style="fontColor()">{{tag.name}}</div>
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                deactive:'',
                detail:false,
                //明るい背景ではフォント黒、暗い背景ではフォント白
                brightBackgroundColor:['pink']
            }  
        },
        props: {
            //親コンポーネントのID。削除の際に使用
            id: {
                type:Number,
                default:0,
                required:false,
            },
            tag: {
                type:Object,
                required:true,
            }
        },
        watch:{
            
        },
        created:function(){
            
        },
        mounted:function(){
            
        },
        methods: {
            deleteTag:async function(){
                let deleteObj = {task_id:this.id,tag_id:this.tag.id}
                try{
                    let result = await axios.delete('/api/tag_task', {data:deleteObj})
                    this.$emit('input','deleted')
                    this.deactive = {display:'none'}
                }catch(error){
                    console.log(error)
                    this.$emit('input','failed')
                }
            },
            fontColor:function(){
                if(this.brightBackgroundColor.indexOf(this.tag.color) != -1){
                    return {color:'black'}
                }else{
                    return {color:'white'}
                }
            }
        }
    }
</script>
<style scoped>
    .tag-root-wrapper {
        position:relative;
        height:3em;
        width:3.5em;
    }
    .tag-wrapper {
        position:absolute;
        bottom:0;
        transform-origin:center bottom;
        overflow:hidden;
        width:3.5em;
        height:1em;
        padding:2px;
        text-align:center;
        border-radius:0.1em 0.1em 0 0;
        cursor:pointer;
        transition:all 0.3s;
    }
    .tag-wrapper:hover {
        height:4.0em;
        transition:all 0.1s;
    }
    .tag-label {
        user-select:none;
        font-size:0.15em;
    }
    .tag-detail {
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
