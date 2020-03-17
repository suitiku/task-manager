<!--汎用フォーム-->
<!--今後の改修ポイント-->
<!--①リセット-->
<template>
    <div class="container">
        <!--通知-->
        <notice ref="notice" />
        
        <div class="forms" ref="slot">
            <slot></slot>
        </div>
        <div class="buttons">
            <button v-if="!value.id" type="button" class="btn btn-outline-primary mx-auto d-block" v-on:click="createRecord">登録</button>
            <button v-else type="button" class="btn btn-outline-primary mx-auto d-block" v-on:click="editRecord">編集</button>
            <!--<button class="btn btn-outline-primary mx-auto d-block" v-on:click="resetForm">項目をリセット</button>-->
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                postObject:{}, //書き込み用オブジェクト
            }  
        },
        props: {
            value: {
                type:[String,Number,Object],
            },
            table: {
                type:String,
                default:'',
                required:true
            },
        },
        mounted:async function(){
        },
        created() {
        },
        watch:{
        },
        methods: {
            createRecord: async function(){
                //Validation
                //配列要素を変換
                //空要素をkeyごと削除
                for(let key in this.value){
                    if(this.value[key] != ''){
                        this.postObject[key] = this.value[key]
                    }
                }
                try{
                    let result = await axios.post('/api/' + this.table,this.postObject)
                    //終了処理
                    this.$refs.notice.showNotice('Success!')
                    this.$emit('input',result.data) //挿入したデータを送出
                }catch(error){
                    this.$refs.notice.showNotice('Failure!')
                    console.log(error)
                }
            },
            editRecord: async function(){
                //Validation
                //配列要素を変換
                //空要素をkeyごと削除
                //idを削除
                for(let key in this.value){
                    if(this.value[key] != '' || key == 'id'){
                        this.postObject[key] = this.value[key]
                    }
                }
                try{
                    let result = await axios.put('/api/' + this.table + '/' + this.value.id, this.postObject)
                    this.$refs.notice.showNotice('Modified Success!')
                    this.$emit('input',result.data)
                }catch(e){
                    console.log(e)
                    this.$refs.notice.showNotice('Modified Failure!')
                }
            },
        },
    }
</script>
<style>
    span {
        font-weight:bold;
    }
    .inline {
        display:flex;
        align-items:center;
    }
    .forms > input,textarea {
        width:100%;
        display:block;
        margin:0.5em;
        padding:0.3em;
        border:1px solid grey;
        border-radius:0.3em;
    }
    .buttons {
        display:flex;
        justify-content:space-around;
        margin:1.5em 0;
    }
</style>
