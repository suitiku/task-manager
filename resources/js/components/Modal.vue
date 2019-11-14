<!--モーダル表示コンポーネント-->
<!--今後の改修ポイント-->
<!--１．スクロール追従-->
<!--２．データ追加等でdocumentのheightが変更した場合を検知する-->
<template>
    <div v-bind:class="modalClass" v-bind:style="documentHeight">
        <div class="modal-wrapper">
            <div class="content-wrapper">
                <div v-on:click="closeModal()" class="close-button"><i class="fa-2x far fa-times-circle"></i></div>
                <div class="modal-content"><slot></slot></div>
            </div>
        </div>
        <div v-on:click="closeModal()" class="modal-background" v-bind:style="documentHeight"></div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                modal:true,
                modalClass:'wrapper',
                height:0
            }  
        },
        watch:{
            
        },
        created(){
            
        },
        mounted() {
        },
        computed:{
            documentHeight:function(){
                let height = this.height + 'px'
                return {height:height}
            }  
        },
        methods: {
            openModal:function(){
                this.height = document.documentElement.scrollHeight
                this.modalClass = 'wrapper modal-active'
                this.$emit('input',true)
            },
            closeModal:function(){
                this.modalClass = 'wrapper'
                this.$emit('input',false)
            }
        }
    }
</script>
<style scoped>
    .wrapper {
        position:absolute;
        top:0;
        left:0;
        width:100%;
        transition:opacity 0.3s,visibility 0.3s;
        opacity:0;
        visibility:hidden;
    }
    .modal-active {
        opacity:1.0;
        visibility:visible;
    }
    .modal-wrapper {
        position:absolute;
        width:100%;
        display:flex;
        justify-content:center;
        flex-direction:column;
        align-items:center;
    }
    .content-wrapper {
        
    }
    .close-button {
        position:relative;
        left: calc(100% - 1em);
        top:1em;
        width:2em;
        height:2em;
        border-radius:50%;
        background-color:white;
        z-index:15;
    }
    .modal-content {
        position:relative;
        width:100%;
        z-index:10;
        padding:1em;
    }
    .modal-background {
        position:absolute;
        width:100%;
        z-index:5;
        background-color:grey;
        opacity:0.5;
    }
   
</style>
