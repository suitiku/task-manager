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
            <button v-if="typeof newColumn.index === 'number'" v-on:click="editColumn()">列を編集</button>
            <button v-else v-on:click="addColumn()">列を追加</button>
        </modal>
        
        <!--リスト表示部-->
        <div id="list-wrapper">
            <div class="row">
                <span v-for="(columnName,index) in listDefinition" ref="columns" v-bind:style="setColumnWidth(index)" v-on:click="clickColumn(index)" class="column">{{columnName.name}}</span>
            </div>
            <div v-for="(item,index) in listItems" class="row">
                <div v-if="editMode">
                    <i class="fas fa-minus-circle rotate" v-on:click="deleteItem(index)"></i>
                    <input v-for="(column,columnIndex) in item" type="text" v-model="listItems[index][columnIndex].value" v-bind:style="setColumnWidth(columnIndex)" />
                </div>
                <div v-else>
                    <span v-for="(column,columnIndex) in item" v-bind:style="setColumnWidth(columnIndex)">{{column.value}} {{listDefinition[columnIndex].suffix}}</span>
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
                listDefinition:[],
                listItems:[],
                originalSortedListItems:[],
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
            getList:async function(){
                if(this.listId){
                    this.initList()
                    let result = await axios.get('/api/lists/' + this.listId)
                    this.myList = result.data
                    this.listDefinition = JSON.parse(result.data.column_definitions)
                    for(let item of result.data.my_list_items){
                        this.listItems.push(JSON.parse(item.values))
                        this.originalSortedListItems.push(JSON.parse(item.values))
                    }
                    this.getColumnWidths()
                }else{ //list_idがない場合は新規作成と解釈してlistを初期化する
                    this.initList()
                }
            },
            initList:function(){
                this.listDefinition = []
                this.listItems = []
            },
            getColumnWidths:function(){
                // 各カラムの最大幅を求める
                for(let column of this.listDefinition){
                    if(!this.columnWidths[column.index] || this.columnWidths[column.index] < column.name.length){
                        this.columnWidths.splice(column.index,1,column.name.length)
                    }
                }
                for(let item of this.listItems){
                    for(let column of item){
                        if(!this.columnWidths[column.index] || this.columnWidths[column.index] < column.value.length){
                            this.columnWidths.splice(column.index,1,column.value.length)
                        }
                    }
                }
            },
            setColumnWidth:function(index){
                return {width:this.columnWidths[index] + 2 + 'em'}
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
                let addColumn = {
                    index:columnIndex,
                    value:this.newColumn.default
                }
                for(let item of this.listItems){
                    item.push(addColumn)
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
                     maxWidth = Math.max(item[this.newColumn.index].value.length + this.newColumn.suffix.length,maxWidth)
                }
                maxWidth = Math.max(this.newColumn.name.length,maxWidth)
                this.columnWidths.splice(this.newColumn.index,1,maxWidth)
                this.$refs.newColumnModal.closeModal()
            },
            //保存
            saveList:async function(){
                // データを整理
                // リスト本体
                let postData = {
                    list:{
                        user_id:1,
                        name:'test',
                        description:null,
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
                this.listItems = JSON.parse(JSON.stringify(this.originalSortedListItems))
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
        margin:1em;
        padding:0em 1em;
        border:1px solid grey;
        .row {
            border-bottom:1px solid grey;
        }
        span {
            display:inline-block;
            padding:0.5em;
        }
        .column {
            cursor:pointer;
        }
        .selected {
            background-color:rgba(orange,0.5);
        }
        .selected-reverse {
            background-color:rgba(blue,0.5);
        }
    }
</style>
