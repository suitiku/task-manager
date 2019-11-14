<!--モーダル表示コンポーネント-->
<!--今後の改修ポイント-->
<template>
    <div v-bind:class="modalClass">
        <div class="modal-wrapper">
            <div class="content-wrapper">
                <div v-on:click="closeModal()" class="close-button"><i class="fa-2x far fa-times-circle"></i></div>
                <div class="modal-content"><slot></slot></div>
            </div>
        </div>
        <div v-on:click="closeModal()" class="modal-background"></div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                modal:true,
                modalClass:'modal-root',
                width:0
            }  
        },
        watch:{
            
        },
        created(){
            
        },
        mounted() {
        },
        computed:{
        },
        methods: {
            openModal:function(){
                this.modalClass = 'modal-root modal-active'
                this.$emit('input',true)
            },
            closeModal:function(){
                this.modalClass = 'modal-root'
                this.$emit('input',false)
            }
        }
    }
</script>
<style scoped>
    .modal-root {
        position:fixed;
        top:0;
        left:0;
        z-index:5;
        height:100%;
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
        width:50%;
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
        z-index:14;
        padding:1em;
    }
    .modal-background {
        position:absolute;
        height:100%;
        width:100%;
        z-index:13;
        background-color:grey;
        opacity:0.5;
    }
   
</style>
