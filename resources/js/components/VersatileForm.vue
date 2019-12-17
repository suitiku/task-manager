<!--万能フォーム生成コンポーネント-->
<!--テーブル名から入力フォームを生成する-->
<!--新規作成と編集-->

<!--今後の改修-->
<!--1 UIコンポーネントから生成する-->
<!--2 バリデーションの追加-->
<!--4 処理成功ダイアログ追加-->

<template>
    <div class="container">
        <!--通知-->
        <notice ref="editNotice" />
        
        <div class="forms">
            <div v-for="(column,index) in columns" v-bind:key="index" class="column">
                <!--{{column.COLUMN_NAME}}-->
                <!--{{postObject[column.COLUMN_NAME]}}-->
                <!--インライン表示-->
                <div v-if="column.COLUMN_TYPE == 'tinyint(4)'" class="form-inline">
                    <span v-show="labelColumns.indexOf(column.DATA_TYPE) != -1">{{setPlaceholder(column)}}</span>
                    <star-range v-model="postObject[column.COLUMN_NAME]" />                    
                </div>
                <!--ブロック表示-->
                <div v-else class="form-block">
                    <span v-show="labelColumns.indexOf(column.DATA_TYPE) != -1">{{setPlaceholder(column)}}</span>
                    <date-picker v-if="column.COLUMN_TYPE == 'datetime'" v-model="postObject[column.COLUMN_NAME]" />
                    <input v-else-if="column.COLUMN_KEY == '' && inputColumns.indexOf(column.DATA_TYPE) != -1" v-model="postObject[column.COLUMN_NAME]" v-bind:type="setInputType(column.DATA_TYPE)" v-bind:placeholder="setPlaceholder(column)">
                    <input v-else-if="column.COLUMN_TYPE == 'tinyint(1)'" v-model="postObject[column.COLUMN_NAME]" type="checkbox">
                    <textarea v-else-if="textareaColumns.indexOf(column.DATA_TYPE) != -1" v-model="postObject[column.COLUMN_NAME]" v-bind:placeholder="setPlaceholder(column)"></textarea>
                </div>
            </div>
            <!--外部キー値の設定-->
            <div v-if="foreign_keys" class="foreign">
                <div v-for="(foreign_key,index) in foreign_keys">
                    <p>{{foreign_key[Object.keys(foreign_key)[0]].comment}}</p>
                    <list-box ref="foreigns" v-model="postObject[Object.keys(foreign_key)[0]]" v-bind:table="foreign_key[Object.keys(foreign_key)[0]].table" v-bind:columns="foreign_key[Object.keys(foreign_key)[0]].columns" />
                </div>
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
                id:this.idProp, //編集用id
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
            idProp: { //親コンポーネントから編集対象レコードを指定するためのid
                type:[Number,String],
                default:'',
                required:false
            },
            //key:value配列の形式で上書きする。外部キーの設定などに利用
            column_override: {
                type:Array,
                required:false
            },
            // 外部キーの設定。foreign_key:table形式の配列。この設定を元にlist-boxを生成する
            foreign_keys: {
                type:Array,
                required:false
            },
        },
        mounted:async function(){
            await this.init()
            await this.overwritePostObject()
        },
        created() {
        },
        watch:{
        },
        methods: {
            init:async function(){
                // テーブルからカラムの情報を取得
                let result = await axios.get('/api/table_info/' + this.table)
                this.columns = result.data
                for(let index of Object.keys(result.data)){
                    let columnName = result.data[index].COLUMN_NAME
                    if(this.whiteList.indexOf(columnName) == -1){
                        this.$set(this.postObject,columnName,'')
                    }
                }
                //上書き処理がある場合
                if(this.column_override){
                    for(let value of this.column_override){
                        let key = Object.keys(value)
                        this.$set(this.postObject,key,value[key])
                    }
                }
            },
            overwritePostObject:async function(){
                // idPropsが設定されている場合はそのレコードを取得
                let existingRecord = {}
                if(this.idProp){
                    existingRecord = await axios.get('/api/' + this.table + '/' + this.idProp)
                    for(let key of Object.keys(this.postObject)){
                        this.$set(this.postObject,key,existingRecord.data[key])
                    }
                }
            },
            setInputType:function(dataType){
                switch(dataType){
                    case 'int':
                        return 'number'
                    case 'varchar':
                        return 'text'
                }
            },
            resetForm:function(){
                for(let key of Object.keys(this.postObject)){
                    this.$set(this.postObject,key,'')
                }
                //外部キー要素（list-box）を初期化
                if(this.foreign_keys){
                    this.$refs.foreigns.forEach(foreign => {
                        foreign.resetValue()
                    })
                }
                //上書き処理がある場合
                if(this.column_override){
                    for(let value of this.column_override){
                        let key = Object.keys(value)
                        this.$set(this.postObject,key,value[key])
                    }
                }
            },
            setPlaceholder:function(column){
                return column.COLUMN_COMMENT || column.COLUMN_NAME  
            },
            createRecord: async function(){
                //Validation
                //配列要素を変換
                //空要素をkeyごと削除
                for(let key of Object.keys(this.postObject)){
                    if(this.postObject[key] == ''){delete this.postObject[key]}
                    if(this.postObject[key] && Array.isArray(this.postObject[key])){this.postObject[key] = this.postObject[key][0]}
                }
                let result = await axios.post('/api/' + this.table,this.postObject)
                if(result.data){
                    this.$emit('input',result.data) //挿入したデータを送出
                    this.id = result.data.id //編集用idをセット
                }
            },
            editRecord: async function(){
                
                try{
                    let result = await axios.put('/api/' + this.table + '/' + this.id, this.postObject)
                    this.$refs.editNotice.showNotice('タスクを修正しました','success')
                    this.$emit('input',result.data)
                }catch(e){
                    console.log(e)
                    this.$refs.editNotice.showNotice('タスクの修正に失敗しました','error')
                }
            },
        },
    }
</script>
<style>
    span {
        font-weight:bold;
    }
    .form-inline {
        display:flex;
        justify-content:flex-start;
    }
    .form-inline span {
        margin-right:2em;
    }
    .form-block {
        display:flex;
        justify-content:center;
        flex-direction:column;
        align-items:center;
    }
    .forms input,textarea {
        width:100%;
        display:block;
        margin:0.5em;
        padding:0.3em;
        border:1px solid grey;
        border-radius:0.3em;
    }
    .foreign {
        margin:1em 0;
    }
    .buttons {
        display:flex;
        justify-content:space-around;
    }
    .column {
        margin:0.8em 0;
    }
</style>
