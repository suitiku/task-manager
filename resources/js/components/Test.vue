<!--テスト用コンポーネント-->
<template>
    <div class="container">
        {{test}}
        <check-box v-model="test"/>
        <!--<modal ref="modal">-->
        <!--    <versatile-form table="tasks" />-->
        <!--</modal>-->
        <!--<button v-on:click="openModal">modal</button>-->
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                test:false
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
