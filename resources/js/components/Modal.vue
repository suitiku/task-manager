<!--単一ファイルコンポーネントのテンプレートファイル-->
<template>
    <div v-bind:class="modalClass">
        <div class="modal-wrapper">
            <div v-on:click="closeModal()" class="close-button"><i class="fa-2x far fa-times-circle"></i></div>
            <div class="modal-content"><slot></slot></div>
        </div>
        <div v-on:click="closeModal()" class="modal-background"></div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                modal:true,
                modalClass:'wrapper'
            }  
        },
        watch:{
            
        },
        created(){
            
        },
        mounted() {
        },
        methods: {
            openModal:function(){
                this.modalClass = 'wrapper modal-active'
            },
            closeModal:function(){
                this.modalClass = 'wrapper'
            }
        }
    }
</script>
<style scoped>
    .wrapper {
        transition:opacity 0.5s,visibility 0.5s;
        opacity:0;
        visibility:hidden;
        display:flex;
        justify-content:center;
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
    }
    .close-button {
        position:relative;
        left: calc(50% + 0.5em);
        top:-0.5em;
        width:2em;
        height:2em;
        /*border:3px solid brack;*/
        border-radius:50%;
        background-color:white;
        z-index:15;
    }
    .modal-content {
        position:relative;
        width:50%;
        z-index:10;
        padding:1em;
    }
    .modal-background {
        position:absolute;
        left:-50%;
        top:-50%;
        width:200%;
        height:150%;
        z-index:5;
        background-color:grey;
        opacity:0.5;
    }
</style>
