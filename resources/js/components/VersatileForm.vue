<!--万能フォーム生成コンポーネント-->
<!--テーブル名から入力フォームを生成する-->
<!--新規作成と編集-->

<!--今後の改修-->
<!--1 UIコンポーネントから生成する-->
<!--2 バリデーションの追加-->
<!--3 editRecordの追加-->
<!--4 処理成功ダイアログ追加-->
<!--5 外部キーの値選択処理追加-->

<template>
    <div class="container">
        <div class="forms">
            <div v-for="(column,index) in columns" v-bind:key="index">
                <span v-show="labelColumns.indexOf(column.DATA_TYPE) != -1">{{setPlaceholder(column)}}</span>
                <input v-if="column.COLUMN_KEY == '' && inputColumns.indexOf(column.DATA_TYPE) != -1" v-model="postObject[column.COLUMN_NAME]" v-bind:type="setInputType(column.DATA_TYPE)" v-bind:placeholder="setPlaceholder(column)">
                <star-range v-else-if="column.COLUMN_TYPE == 'tinyint(4)'" v-model="postObject[column.COLUMN_NAME]" />
                <input v-else-if="column.COLUMN_TYPE == 'tinyint(1)'" v-model="postObject[column.COLUMN_NAME]" type="checkbox">
                <textarea v-else-if="textareaColumns.indexOf(column.DATA_TYPE) != -1" v-model="postObject[column.COLUMN_NAME]" v-bind:placeholder="setPlaceholder(column)"></textarea>
            </div>
        </div>
        <div class="buttons">
            <button v-if="id == ''" type="button" class="btn btn-outline-primary mx-auto d-block" v-on:click="createRecord">登録</button>
            <button v-else type="button" class="btn btn-outline-primary mx-auto d-block" v-on:click="editRecord">編集</button>
            <button class="btn btn-outline-primary mx-auto d-block" v-on:click="resetForm">項目をリセット</button>
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                id:this.id_props, //編集用id
                columns:[],
                postObject:{}, //送信用
                
                //除外リスト
                whiteList:['id','created_at','updated_at'],
                
                //form種別判定用配列
                inputColumns:['int','varchar','datetime'],
                textareaColumns:['text'],
                labelColumns:['tinyint','datetime']
            }  
        },
        props: {
            table: {
                type:String,
                default:'',
                required:true
            },
            id_props: { //親コンポーネントから編集対象レコードを指定するためのid
                type:[Number,String],
                default:'',
                required:false
            }
        },
        mounted() {
            this.init()
        },
        created() {
            this.init()
        },
        methods: {
            init:async function(){
                let result = await axios.get('/api/table_info/' + this.table)
                this.columns = result.data
                for(let index of Object.keys(result.data)){
                    if(this.whiteList.indexOf(result.data[index].COLUMN_NAME) == -1){
                        this.$set(this.postObject,result.data[index].COLUMN_NAME,'')
                    }
                }
                
            },
            setInputType:function(dataType){
                switch(dataType){
                    case 'int':
                        return 'number'
                    case 'varchar':
                        return 'text'
                    case 'datetime':
                        return 'date'
                }
            },
            resetForm:function(){
                for(let key of Object.keys(this.postObject)){
                    this.$set(this.postObject,key,'')
                }
            },
            setPlaceholder:function(column){
                return column.COLUMN_COMMENT || column.COLUMN_NAME  
            },
            createRecord: async function(){
                let result = await axios.post('/api/' + this.table,this.postObject)
                if(result.data){
                    console.log(result.data)
                    this.$emit('input',result.data) //挿入したデータを送出
                    this.id = result.data.id //編集用idをセット
                }
            },
            editRecord: async function(){
                let result = await axios.put('/api/' + this.table + '/' + this.id, this.postObject)
                if(result.data){
                    console.log(result.data)
                    this.$emit('input',result.data)
                }
            },
        },
    }
</script>
<style>
    .forms input,textarea {
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
    }
</style>
