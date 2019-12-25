<!--テスト用コンポーネント-->
<template>
    <div class="container">
        
        <notice ref="notice" />
        <!--モーダル-->
        <modal ref="modal" v-model="modal">
            <versatile-form table="tasks" idProp="4"/>
        </modal>
        <button class="button" v-on:click="showModal()">modal</button>
        <!--<button class="button" v-on:click="showNotice()">notice</button>-->
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                modal:false,
                test:'',
                task:'',
            }  
        },
        created:async function(){
        },
        mounted:async function() {
            let result = await axios.get('/api/tasks/1')
            this.task = result.data
        },
        methods: {
            showModal:function(){
                this.$refs.modal.openModal()
            },
            showNotice:function(){
                this.$refs.notice.showNotice('実行中')
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
