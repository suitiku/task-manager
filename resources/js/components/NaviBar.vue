<!--ページ上部のナビゲーションバー-->
<!--home.vueコンポーネントと通信して描写内容を変更する-->
<template>
    <div class="navi-bar-wrapper">
        <!--ホーム-->
        <input v-model="content" type="radio" name="content" value="home" id="navi-home" checked>
        <label for="navi-home"><i class="fas fa-home"></i></label>
        
        <!--タスク-->
        <input v-model="content" type="radio" name="content" value="task" id="navi-task">
        <label for="navi-task"><i class="far fa-check-square"></i></label>
        
        <!--プロジェクト-->
        <input v-model="content" type="radio" name="content" value="project" id="navi-project">
        <label for="navi-project"><i class="fas fa-project-diagram"></i></label>
        
        <!--リスト-->
        <input v-model="content" type="radio" name="content" value="list" id="navi-list">
        <label for="navi-list"><i class="fas fa-list"></i></label>
        
        <!--設定-->
        <input v-model="content" type="radio" name="content" value="settings" id="navi-settings">
        <label for="navi-settings"><i class="fas fa-cog"></i></label>
    </div>
</template>

<script>
    import bus from '../eventBus.js'
    export default {
        data:function(){
            return {
                content:'',
            }  
        },
        props: {
            
        },
        watch:{
            content:function(){
                bus.$emit('change-content',this.content)
            }
        },
        created:function(){
            
        },
        mounted:function(){
            this.content = 'home'
        },
        methods: {
        }
    }
</script>
<style scoped>
    .navi-bar-wrapper {
        width:100%;
        display:flex;
        justify-content:flex-start;
    }
    input {
        display:none;
    }
    label {
        width:2.5em;
        height:2.5em;
        margin:0 1em;
        border-radius:50%;
        background:gainsboro;
        color:white;
        font-weight:bold;
        display:flex;
        justify-content:center;
        flex-direction:column;
        align-items:center;
        cursor:pointer;
        transition:all 0.5s ease;
    }
    input:checked + label {
        color:orange;
        background:gainsboro;
    }
    
</style>
