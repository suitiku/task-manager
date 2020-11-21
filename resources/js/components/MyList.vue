<!--リスト表示コンポーネント-->
<template>
    <div>
        <!--通知-->
        <notice ref="notice" />
        
        <!--列追加／編集用モーダル-->
        <modal ref="newColumnModal" v-model="newColumnModal">
            <input type="text" v-model="newColumn.name" placeholder="列の名前">
            <select v-model="newColumn.type">
                <option value="Number">数値</option>
                <option value="Text">文字</option>
                <option value="Date">日付</option>
                <option value="Boolean">Yes/No</option>
            </select>
            <input type="text" v-model="newColumn.suffix" placeholder="接尾辞" >
            <input type="text" v-model="newColumn.default" placeholder="既定値" >
            <div v-if="typeof newColumn.index === 'number'">
                <button v-on:click="editColumn()">列を編集</button>
                <button v-on:click="deleteColumn()">列を削除</button>
            </div>
            <div v-else>
                <button v-on:click="addColumn()">列を追加</button>
            </div>
        </modal>
        
        
        <!--リスト表示部-->
        <div id="list-wrapper">
            <!--メタデータ編集部分-->
            <div v-if="editMode">
                <input type="text" v-model="listMetaData.name" />
                <textarea type="text" v-model="listMetaData.description">{{listMetaData.description}}</textarea>
            </div>
            <!--リスト外見変更-->
            <div v-else class="fixed-bar-right">
                <i class="fas fa-border-all fa-lg" v-on:click="changeListAppearance('simple-border')"></i>
                <i class="fas fa-underline fa-lg" v-on:click="changeListAppearance('simple-border-bottom')"></i>
                <i class="fas fa-highlighter fa-lg" v-on:click="changeListAppearance('highlight-odd')"></i>
                <i class="fas fa-list-ol fa-lg" v-on:click="changeListAppearance('meta-visible')"></i>
            </div>
            
            <!--リスト本体-->
            <div id="list" ref="list">
                <div class="row-meta">
                    <div v-for="index in listItems.length + 1" class="row">
                        <div v-if="index == 1"></div>
                        <div v-else>
                            <i v-if="editMode" class="fas fa-minus-circle rotate" v-on:click="deleteItem(index - 2)"></i>
                            <i v-else>{{index - 1}}</i>
                        </div>
                    </div>
                </div>
                <div class="row-data">
                    <div v-for="(item,index) in listItems">
                        <div v-if="index == 0" class="row">
                            <span v-for="(columnName,columnIndex) in listDefinition" ref="columns" v-bind:style="setColumnWidth(columnIndex)" v-on:click="clickColumn(columnIndex)" class="column">
                                {{columnName.name}}
                            </span>
                        </div>
                        <div class="row">
                            <div v-if="editMode">
                                <input v-for="(column,columnIndex) in item" type="text" v-model="listItems[index][columnIndex].value" v-bind:style="setColumnWidth(columnIndex)" />
                            </div>
                            <div v-else>
                                <span v-for="(column,columnIndex) in item" v-bind:style="setColumnWidth(columnIndex)">{{column.value}} {{listDefinition[columnIndex].suffix}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'
    export default {
        data:function(){
            return {
                myList:{}, //取得したリストをそのまま入れておく
                listMetaData:{}, //タイトルや説明など
                listDefinition:[], //列の定義
                listItems:[], //行
                columnWidths:[],
                newColumnModal:false,
                newColumn:{
                    index:'',
                    name:'',
                    type:'',
                    suffix:'',
                    default:'',
                },
                sortedIndex:null, //現在ソートされているカラムのインデックスを格納
            }  
        },
        props: {
            userId:{
                type:[Number,String],
                default:null,
                required:false
            },
            listId:{
                type:[Number,String],
                default:0,
                required:false,
            },
            editMode:{
                type:[Boolean,Number],
                default:false,
                required:false,
            }
        },
        watch:{
            listId:function(){
                this.getList()
            }
        },
        created:async function(){
            this.getList()
        },
        mounted:function(){
        },
        computed:{
            
        },
        methods: {
            initList:function(){
                this.listDefinition = []
                this.listItems = []
            },
            getList:async function(){
                if(this.listId){
                    this.initList()
                    let result = await axios.get('/api/lists/' + this.listId)
                    this.myList = result.data
                    this.listMetaData = {
                        name:result.data.name,
                        description:result.data.description,
                        type:result.data.type
                    }
                    this.listDefinition = JSON.parse(result.data.column_definitions)
                    for(let item of result.data.my_list_items){
                        this.listItems.push(JSON.parse(item.values))
                    }
                    this.getColumnWidths()
                    
                    //閲覧モードのときはメタデータを不可視化
                    if(!this.editMode){
                        this.$refs.list.classList.add('meta-invisible')
                    }
                    
                }else{ //list_idがない場合は新規作成と解釈してlistを初期化する
                    this.initList()
                }
            },
            getColumnWidths:function(){
                // 各カラムの最大幅を求める
                for(let column of this.listDefinition){
                    if(!this.columnWidths[column.index] || this.columnWidths[column.index] < column.name.length){
                        this.columnWidths.splice(column.index,1,column.name.length)
                    }
                }
                for(let item of this.listItems){
                    for(let index in item){
                        if(item[index].value && this.columnWidths[index] < item[index].value.length){
                            this.columnWidths.splice(index,1,item[index].value.length)
                        }
                    }
                }
            },
            setColumnWidth:function(index){
                return {width:this.columnWidths[index] + 2.5 + 'em'}
            },
            changeListAppearance:function(className){
                if(event.target.classList.contains('selected')){
                    this.$refs.list.classList.remove(className)
                }else{
                    this.$refs.list.classList.add(className)
                }
                event.target.classList.toggle('selected')
            },
            addItem:function(){
                let addItem = []
                for(let column of this.listDefinition){
                    let addColumn = {index:column.index,value:column.default}
                    addItem.push(addColumn)
                }
                this.listItems.push(addItem)
            },
            deleteItem:async function(index){
                let confirmResult = confirm('行を削除します。よろしいですか？')
                if(!confirmResult)return
                
                let itemId = this.myList.my_list_items[index].id
                try{
                    let result = await axios.delete('/api/list_items/' + itemId)
                    if(result.data){
                        this.listItems.splice(index,1)
                        this.$refs.notice.showNotice('リストアイテムを削除しました')
                    }else{
                        this.$refs.notice.showNotice('リストアイテムの削除に失敗しました')
                    }
                }catch(error){
                    console.log(error)
                    this.$refs.notice.showNotice('リストアイテムの削除に失敗しました')
                }  
            },
            showAddColumnModal:function(){
                 //newColumnをリセット
                this.newColumn = {
                    index:'',
                    name:'',
                    type:'',
                    suffix:'',
                    default:'',
                }
                this.$refs.newColumnModal.openModal()
            },
            addColumn:function(){
                let columnIndex = this.listDefinition.length
                this.newColumn.index = columnIndex
                this.listDefinition.push(this.newColumn)
                for(let index in this.listItems){
                    this.listItems[index].push({
                        index:columnIndex,
                        value:this.newColumn.default
                    })
                }
                //幅調整
                let width = Math.max(this.newColumn.name.length,this.newColumn.default.length)
                this.columnWidths.push(width)
            },
            editColumn:function(){
                this.listDefinition[this.newColumn.index].name = this.newColumn.name
                this.listDefinition[this.newColumn.index].type = this.newColumn.type
                this.listDefinition[this.newColumn.index].suffix = this.newColumn.suffix
                this.listDefinition[this.newColumn.index].default = this.newColumn.default
                //幅調整
                this.columnWidths[this.newColumn.index] = 0 //リセット
                let maxWidth = 0
                for(let item of this.listItems){
                    let valueLength = item[this.newColumn.index].value ? item[this.newColumn.index].value.length : 0
                    let suffixLength = this.newColumn.suffix ? this.newColumn.suffix.length : 0
                     maxWidth = Math.max( valueLength + suffixLength, maxWidth)
                }
                maxWidth = Math.max(this.newColumn.name.length, maxWidth)
                this.columnWidths.splice(this.newColumn.index,1,maxWidth)
                this.$refs.newColumnModal.closeModal()
            },
            deleteColumn:function(){
                let confirmResult = confirm('この列を削除します。よろしいですか？')
                if(!confirmResult)return
                
                this.listDefinition.splice(this.newColumn.index,1)
                for(let index in this.listItems){
                    this.listItems[index].splice(this.newColumn.index,1)
                }
                this.$refs.newColumnModal.closeModal()
                this.$refs.notice.showNotice('列を削除しました')
            },
            //保存
            saveList:async function(){
                // データを整理
                // リスト本体
                let postData = {
                    list:{
                        user_id:this.userId,
                        name:this.listMetaData.name,
                        description:this.listMetaData.description,
                        column_definitions:JSON.stringify(this.listDefinition),
                        type:'nomal',
                        is_stared:false
                    },
                    items:[]
                }
                //アイテム
                for(let index in this.listItems){
                    let postItem = {
                        index:index,
                        values:JSON.stringify(this.listItems[index]),
                        is_checked:false,
                        is_stared:false,
                    }
                    postData.items.push(postItem)
                }
                console.log(postData)
                try{
                    let result
                    if(this.listId){ //上書き
                        result = await axios.put('/api/lists/' + this.listId, postData)
                    }else{ //新規
                        result = await axios.post('/api/lists',postData)
                        this.$emit('input',result.data)
                    }
                    console.log(result.data)
                }catch(error){
                    console.log(error)
                }
                
            },
            //カラム名をクリック
            clickColumn:function(index){
                if(this.editMode){ //編集モード（編集モーダルを表示）
                    this.newColumn.index = index
                    this.newColumn.name = this.listDefinition[index].name
                    this.newColumn.type = this.listDefinition[index].type
                    this.newColumn.suffix = this.listDefinition[index].suffix
                    this.newColumn.default = this.listDefinition[index].default
                    this.$refs.newColumnModal.openModal()
                }else{ //閲覧モード（並び替え）
                    //カラムを選択（昇順）→降順→選択解除で推移
                    if(this.sortedIndex == null || Number(this.sortedIndex) != index){
                        this.sortList(index)
                        if(this.$refs.columns[this.sortedIndex]){
                            this.$refs.columns[this.sortedIndex].classList = 'column'
                        }
                        event.target.classList.add('selected')
                        this.sortedIndex = index
                    }else if(event.target.classList.contains('selected-reverse')){
                        this.sortListIndex()
                        event.target.classList.remove('selected')
                        event.target.classList.remove('selected-reverse')
                        this.sortedIndex = null
                    }else if(Number(this.sortedIndex) == index){
                        this.sortListReverse(index)
                        event.target.classList.remove('selected')
                        event.target.classList.add('selected-reverse')
                    }
                    
                }
            },
            // インデックスでソート
            sortListIndex:function(){
                this.listItems = []
                for(let item of this.myList.my_list_items){
                    this.listItems.push(JSON.parse(item.values))
                }
            },
            // ソート
            sortList:function(columnIndex){ //昇順
                let vue = this
                this.listItems.sort(function(itemA,itemB){
                    //数値以外
                    if(vue.listDefinition[columnIndex].type != 'Number' && vue.listDefinition[columnIndex].type != 'Date'){
                        return itemA[columnIndex].value < itemB[columnIndex].value ? 1 : -1
                    }else{ //数値／日付
                        return (Number(itemA[columnIndex].value) < Number(itemB[columnIndex].value) ? -1 : 1);
                    }
                })
            },
            sortListReverse:function(columnIndex){ //降順
                let vue = this
                this.listItems.sort(function(itemA,itemB){
                    //数値以外
                    if(vue.listDefinition[columnIndex].type != 'Number' && vue.listDefinition[columnIndex].type != 'Date'){
                        return itemA[columnIndex].value > itemB[columnIndex].value ? 1 : -1
                    }else{ //数値／日付
                        return (Number(itemA[columnIndex].value) > Number(itemB[columnIndex].value) ? -1 : 1);
                    }
                })
            },
            createNewList:function(){
                this.listMetaData = {
                    name:'NO TITLE',
                    description:'Insert Description!'
                }
                this.listDefinition = []
                this.listItems = []
                this.listDefinition = [
                    {name:'no title',type:'Text',suffix:null,default:null}    
                ]
                this.addItem()
            }
        }
    }
</script>
<style lang="scss" scoped>
    .rotate {
        margin-left:0.1em;
        cursor:pointer;
        &:hover {
            animation:rotate 1s linear infinite;
        }
    }
    @keyframes rotate {
        0% {
            transform:rotate(0deg);
        }
        100% {
            transform:rotate(360deg);
        }
    }
    
    #list-wrapper {
        display:flex;
        flex-direction:column;
        #list {
            display:flex;
            margin:1em;
            .row-meta {
                margin-right:1.0em;
                padding-top:0.4em;
                .row {
                    div {
                        height:100%;
                        i {
                            display:inline-block;
                            height:100%;
                            padding:0.2em;
                        }
                    }
                }
            }
            .row-data {
                
            }
            .row {
                margin:0em 0em;
                padding:0em 0em;
                min-height:1.8em;
                
                div {
                    display:flex;
                    span {
                        display:inline-block;
                        height:100%;
                        margin:0em 0em;
                        padding:0.2em;
                        
                    }
                    input {
                        display:inline-block;
                        height:calc(100% - 2px);
                        margin:0em 0em;
                        padding:0em;
                    }
                }
                
                .column {
                    cursor:pointer;
                    padding:0.2em;
                }
                .column.selected::after {
                    content:"　\f162";
                    font-family: FontAwesome;
                    color:orange;
                }
                .column.selected-reverse::after {
                    content:"　\f163";
                    font-family: FontAwesome;
                    color:blue;
                }
            }
        }
    }
    
    /*外見変更用のコントロールパネル（右固定）*/
    .fixed-bar-right {
        display:flex;
        flex-direction:column;
        justify-content:center;
        align-items:center;
        position:fixed;
        right:2em;
        width:5em;
        top:3em;
        z-index:10;
        border:1px solid white;
        background-color:white;
        border-radius:0.2em;
        i {
            color:grey;
            display:inline-block;
            margin:0.5em;
            cursor:pointer;
            transition:all 0.2s ease;
        }
        i:hover {
            text-shadow: 0 0 5px orange;
        }
        .selected{
            text-shadow: 0 0 5px orange;
        }
    }
    
    /*装飾用クラス*/
    /*ボーダー系*/
    .simple-border {
        .row-meta {
            .row {
                border:1px solid transparent;
            }
        }
        span {
            border:1px solid grey;
        }
    }
    .simple-border-bottom {
        .row-meta {
            .row {
                border-bottom:1px solid transparent;
            }
        }
        .row-data{
            .row {
                border-bottom:1px solid grey;
            }
        }
    }
    
    /*ハイライト系*/
    .highlight-odd {
        .row-data > div:nth-child(even){
            background-color:rgba(orange,0.5)
        }
    }
    
    /*行頭系*/
    .meta-invisible {
        .row-meta {
            visibility:hidden;
        }
    }
    .meta-visible {
        .row-meta{
            visibility:visible;
        }
    }
    
</style>
