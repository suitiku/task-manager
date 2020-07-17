<!--モーダル表示コンポーネント-->
<!--今後の改修ポイント-->
<template>
    <div ref="modal" id="modal">
        <div class="modal-background" v-on:click="closeModal()"></div>
        <div class="modal-content">
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
            }  
        },
        methods: {
            openModal:function(){
                this.$refs.modal.classList.add('active')
                this.$emit('input',true)
            },
            closeModal:function(){
                this.$refs.modal.classList.remove('active')
                this.$emit('input',false)
            },
        }
    }
</script>
<style lang="scss" scoped>
    #modal {
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
        transition:opacity 0.5s,visibility 0.5s;
        opacity:0;
        visibility:hidden;
        &.active {
            opacity:1.0;
            visibility:visible;
            transition:opacity 0.5s,visibility 0.5s;
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
        .modal-content {
            position:absolute;
            top:20%;
            width:65%;
            height:auto;
            z-index:11;
            margin-bottom:3em;
            padding:1em;
            border:2px solid gray;
            .close-button {
                position:absolute;
                left: calc(100% - 1em);
                top: -1em;
                width:2em;
                height:2em;
                border-radius:50%;
                background-color:white;
                z-index:12;
            }
        }
    }
</style>
