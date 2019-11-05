<!--テスト用コンポーネント-->
<template>
    <div class="container">
        <modal ref="modal">
            <versatile-form table="tasks" />
        </modal>
        <button v-on:click="openModal">modal</button>
        <!--<div style="width:50%;">-->
        <!--    <versatile-form table="tasks" />-->
        <!--</div>-->
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
            }  
        },
        mounted() {
        },
        created() {
        },
        methods: {
            openModal:function(){
                this.$refs.modal.openModal()
            }
        },
    }
</script>
<style>
</style>
