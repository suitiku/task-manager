<!--モーダル表示コンポーネント-->
<!--今後の改修ポイント-->
<template>
    <div v-bind:class="modal_class">
        <div class="modal-background" v-on:click="closeModal()"></div>
        <div v-bind:class="modal_content_class">
            <div v-on:click="closeModal()" class="close-button" ><i class="fa-2x far fa-times-circle"></i></div>
            <slot></slot>
        </div>
    </div>    
</template>

<script>
    export default {
        data:function(){
            return {
                modal:true,
                modal_class:'modal-root',
                modal_content_class:'modal-content',
                content_top:0,
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
                this.modal_content_class = 'modal-content modal-content-active'
                this.modal_class = 'modal-root-active'
                this.$emit('input',true)
            },
            closeModal:function(){
                this.content_top = '0px'
                this.modal_content_class = 'modal-content'
                this.modal_class = 'modal-root'
                this.$emit('input',false)
            },
        }
    }
</script>
<style scoped>
    .modal-root {
        position:fixed;
        overflow-y:auto;
        
        z-index:9;
        top:0;
        left:0;
        right:0;
        bottom:0;
        padding:3em 0em;
        display:flex;
        justify-content:center;
        width:100%;
        transition:opacity 0.7s,visibility 0.7s;
        opacity:0;
        visibility:hidden;
    }
    .modal-root-active {
        position:fixed;
        overflow-y:auto;
        
        z-index:9;
        top:0;
        left:0;
        right:0;
        bottom:0;
        padding:3em 0em;
        display:flex;
        justify-content:center;
        width:100%;
        opacity:1.0;
        visibility:visible;
        transition:opacity 0.7s,visibility 0.7s;
    }
    .close-button {
        position:absolute;
        left: calc(100% - 1em);
        top: -1em;
        width:2em;
        height:2em;
        border-radius:50%;
        background-color:white;
        z-index:15;
    }
    .modal-content {
        position:absolute;
        top:20%;
        width:65%;
        height:0px;
        overflow:hidden;
        z-index:14;
        margin-bottom:3em;
        padding:1em;
        opacity:0;
        visibility:hidden;
        transition:opacity 0.7s;
    }
    .modal-content-active {
        overflow:visible;
        height:auto;
        opacity:1.0;
        visibility:visible;
    }
    .modal-background {
        position:fixed;
        top:0;
        left:0;
        height:5000px;
        width:100%;
        background:grey;
        opacity:0.5;
        z-index:10;
    }
   
</style>
