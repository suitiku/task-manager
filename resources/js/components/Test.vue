<!--テスト用コンポーネント-->
<template>
    <div class="container">
        <!--モーダル-->
        <modal ref="modal" v-model="modal">
            <versatile-form table="tasks" />
        </modal>
        <button class="button" v-on:click="showModal()">modal</button>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                modal:false,
                test:'',
                tag:''
            }  
        },
        mounted() {
        },
        created:async function() {
            let result = await axios.get('/api/tags/1')
            this.tag = result.data
        },
        methods: {
            showModal:function(){
                this.$refs.modal.openModal()
            }
        },
    }
</script>
<style>
    .space {
        width:100%;
        height:50px;
        border:1px solid black;
    }
    .button {
        position:fixed;
        top:0;
        left:0;
    }
</style>
