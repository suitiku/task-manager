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
                <div class="decoration">
                    <p>decoration</p>
                    <i class="fas fa-border-all fa-lg" v-on:click="changeListAppearance('simple-border')"></i>
                    <i class="fas fa-underline fa-lg" v-on:click="changeListAppearance('simple-border-bottom')"></i>
                    <i class="fas fa-highlighter fa-lg" v-on:click="changeListAppearance('highlight-odd')"></i>
                    <i class="fas fa-list-ol fa-lg" v-on:click="changeListAppearance('meta-visible')"></i>
                </div>
            </div>
            
            <!--絞り込み検索窓-->
            <input v-if="!editMode" type="text" v-model="filterWord" v-on:input="filterRowsByText()" placeholder="全体からしぼりこみ">
            
            <!--フィルター部分-->
            <div class="filters">
                <div v-for="(column,index) in listDefinition" class="filter">
                    <div>
                        <span>{{column.name}}</span>
                        <input v-if="column.type=='Text'" v-model="filters[index]" type="text" />
                        <range-number v-else-if="column.type=='Number'" v-model="filters[index]" v-bind:minimumValue="setMinimumValue(index)" v-bind:maximumValue="setMaximumValue(index)" />
                    </div>
                    <i v-model="filterOperators[index]" v-if="index != listDefinition.length -1" class="fas fa-plus operator" v-on:click="toggleFilterOperator(index)"></i>
                </div>
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
                            <!--カラム名表示-->
                            <span v-for="(columnName,columnIndex) in listDefinition" ref="columns" v-bind:style="setColumnWidth(columnIndex)" class="column">
                                <span v-if="editMode" v-on:click="showEditColumnModal(columnIndex)">{{columnName.name}}</span>
                                <span v-else>{{columnName.name}}</span>
                                <i class="fas fa-sort-amount-down-alt" v-on:click="clickSortButton(columnIndex)"></i>
                            </span>
                        </div>
                        <div class="row">
                            <div v-if="editMode">
                                <input v-for="(column,columnIndex) in item.values" type="text" v-model="listItems[index].values[columnIndex].value" v-bind:style="setColumnWidth(columnIndex)" />
                            </div>
                            <div v-else>
                                <span v-for="(column,columnIndex) in item.values" v-bind:style="setColumnWidth(columnIndex)">{{column.value}} {{listDefinition[columnIndex].suffix}}</span>
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
                originalListItems:[], //ベースとなる行リスト
                sortedListItems:[], //ソートされた行リストを保存
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
                filters:[],
                filterOperators:[], //and/or用の演算子配列
                filterWord:'', //フィルター用（Rawテキスト）
                filteredArray:[], //フィルターされた結果itemsの配列
                // filterNumber:{ //フィルター用（数値）
                //     min:null,
                //     max:null,
                // },
                // filterNumberSettings:{
                //     minimum:0,
                //     maximum:100,
                //     validDigits:0,
                // },
                // filterIndex:null,  //フィルターされているカラムのインデックス
                // initialPositionLeft:0, //カラム別フィルターの移動位置記憶用
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
            },
            filters:{
                handler:function(){
                    this.filterRows()
                },
                deep:true
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
                this.sortedListItems = []
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
                    // console.log(this.myList)
                    this.listDefinition = JSON.parse(result.data.column_definitions)
                    //filterOperatorsをセット
                    for(let index in this.listDefinition){
                        if(index >= this.listDefinition.length - 1)break
                        this.filterOperators.push('and')
                    }
                    for(let item of result.data.my_list_items){
                        let pushItem = item
                        pushItem.values = JSON.parse(item.values)
                        this.listItems.push(pushItem)
                        this.sortedListItems.push(pushItem)
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
                    for(let index in item.values){
                        if(item.values[index].value && this.columnWidths[index] < item.values[index].value.length){
                            this.columnWidths.splice(index,1,item.values[index].value.length)
                        }
                    }
                }
            },
            setColumnWidth:function(index){
                return {width:this.columnWidths[index] + 3.0 + 'em'}
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
                this.listItems.push({values:addItem})
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
                    this.listItems[index].values.push({
                        index:columnIndex,
                        value:this.newColumn.default
                    })
                }
                //幅調整
                let width = Math.max(this.newColumn.name.length,this.newColumn.default.length)
                this.columnWidths.push(width)
            },
            showEditColumnModal:function(index){
                this.newColumn = {
                    index:index,
                    name:this.listDefinition[index].name,
                    type:this.listDefinition[index].type,
                    suffix:this.listDefinition[index].suffix,
                    default:this.listDefinition[index].default,
                }
                this.$refs.newColumnModal.openModal()
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
                    let valueLength = item.values[this.newColumn.index].value ? item.values[this.newColumn.index].value.length : 0
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
                    this.listItems[index].values.splice(this.newColumn.index,1)
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
                        values:JSON.stringify(this.listItems[index].values),
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
                    this.$refs.notice.showNotice('リストを保存しました')
                }catch(error){
                    console.log(error)
                    this.$refs.notice.showNotice('リストの保存に失敗しました')
                }
                
            },
            // ソートボタンをクリック
            clickSortButton:function(columnIndex){
                // 他のカラムが選択されていたらリセット
                if(this.sortedIndex != null && this.sortedIndex != columnIndex){
                    this.$refs.columns[this.sortedIndex].children[1].className = 'fas fa-sort-amount-down-alt'
                    this.sortedIndex = null
                }
                
                if(this.sortedIndex == null){
                    event.target.classList.add('selected')
                    this.sortedIndex = columnIndex
                    this.sortListASC(columnIndex)
                }else if(event.target.classList.contains('selected')){
                    event.target.classList.remove('fa-sort-amount-down-alt','selected')
                    event.target.classList.add('fa-sort-amount-down','selected-alt')
                    this.sortListDESC(columnIndex)
                }else if(event.target.classList.contains('selected-alt')){
                    event.target.classList.add('fa-sort-amount-down-alt')
                    event.target.classList.remove('fa-sort-amount-down','selected-alt')
                    this.sortedIndex = null
                    this.sortListIndex()
                }
            },
            // インデックスでソート
            sortListIndex:function(){
                this.sortedListItems = []
                for(let item of this.myList.my_list_items){
                    this.sortedListItems.push(item)
                }
                this.filterRowsByText()
            },
            // ソート
            sortListASC:function(columnIndex){ //昇順
                let vue = this
                this.sortedListItems.sort(function(itemA,itemB){
                    //数値以外
                    if(vue.listDefinition[columnIndex].type != 'Number' && vue.listDefinition[columnIndex].type != 'Date'){
                        return itemA.values[columnIndex].value < itemB.values[columnIndex].value ? 1 : -1
                    }else{ //数値／日付
                        return (Number(itemA.values[columnIndex].value) < Number(itemB.values[columnIndex].value) ? -1 : 1);
                    }
                })
                this.filterRowsByText()
            },
            sortListDESC:function(columnIndex){ //降順
                let vue = this
                this.sortedListItems.sort(function(itemA,itemB){
                    //数値以外
                    if(vue.listDefinition[columnIndex].type != 'Number' && vue.listDefinition[columnIndex].type != 'Date'){
                        return itemA.values[columnIndex].value > itemB.values[columnIndex].value ? 1 : -1
                    }else{ //数値／日付
                        return (Number(itemA.values[columnIndex].value) > Number(itemB.values[columnIndex].value) ? -1 : 1);
                    }
                })
                this.filterRowsByText()
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
            },
            // 全体から絞り込み
            filterRowsByText:function(){
                this.listItems = []
                let filterWords = this.filterWord.split(' ').filter(word => {
                    return word != ''
                })
                if(filterWords.length == 0){
                    this.listItems = this.sortedListItems
                    return
                }
                let result
                result = this.sortedListItems.filter(item => {
                    return item.values.some(column => {
                        return filterWords.some(filterWord => {
                            return String(column.value).replace(' ','').indexOf(filterWord) != -1
                        })
                    })
                })
                this.listItems = result
            },
            // カラム別フィルター
            filterRows:function(){
                this.listItems = []
                this.filteredArray = []
                let result
                for(let index in this.listDefinition){
                    switch(this.listDefinition[index].type) {
                        case 'Text': 
                            if(!this.filters[index]){
                                this.filteredArray.push(this.sortedListItems.map(item => {return item.id}))
                                break
                            }
                            let filterWords = this.filters[index].split(' ').filter(word => {
                                return word != ''
                            })
                            if(filterWords.length == 0){
                                this.filteredArray.push(this.sortedListItems.map(item => {return item.id}))
                                break
                            }
                            result = this.sortedListItems.filter(row => {
                                return filterWords.some(filterWord => {
                                    return String(row.values[index].value).replace(' ','').indexOf(filterWord) != -1
                                })
                            })
                            this.filteredArray.push(result.map(item => {return item.id}))
                            break
                        case 'Number':
                            if(!this.filters[index]){
                                this.filteredArray.push(this.sortedListItems.map(item => {return item.id}))
                                break
                            }
                            result = this.sortedListItems.filter(row => {
                                return Number(row.values[index].value) >= this.filters[index].min && Number(row.values[index].value) <= this.filters[index].max
                            })
                            this.filteredArray.push(result.map(item => {return item.id}))
                            break
                    }                    
                    
                }
                this.applyOperators()
            },
            applyOperators:function(){ //and/or演算子をフィルターに適用、idで比較する
                let result = []
                for(let index in this.filterOperators){
                    if(index == 0)result = this.filteredArray[index]
                    if(this.filterOperators[index] == 'and'){
                        result = result.filter(id => {
                            return this.filteredArray[Number(index) + 1].indexOf(id) != -1
                        })
                    }else{
                        result = new Set(result.concat(this.filteredArray[Number(index) + 1]))
                    }
                }
                //sortedListItemsのidと突合して復元
                this.listItems = this.sortedListItems.filter(item => {
                    return result.indexOf(item.id) != -1
                })
            },
            toggleFilterOperator:function(index){
                event.target.classList.toggle('or')
                this.filterOperators.splice(index,1,this.filterOperators[index] == 'and' ? 'or' : 'and')
            },
            // RangeNumberの初期値を設定
            setMinimumValue:function(index){
                let valueArray = this.sortedListItems.map(item => {
                    return Number(item.values[index].value)
                })
                return Math.min.apply(null,valueArray)
            },
            setMaximumValue:function(index){
                let valueArray = this.sortedListItems.map(item => {
                    return Number(item.values[index].value)
                })
                return Math.max.apply(null,valueArray)
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
                    transition:all 0.2s ease;
                    &:hover {
                        background-color:rgba(orange,0.5);
                    }
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
                    user-select:none;
                    cursor:pointer;
                    padding:0.3em 1.2em 0.3em 0.3em;
                    display:flex;
                    justify-content:space-between;
                    align-items:center;
                    span {
                        border:none;
                    }
                    i {
                        display:inline-block;
                        height:1em;
                        vertical-align: middle;
                        color:grey;
                        opacity:0.6;
                    }
                    i:hover {
                        color:orange;
                    }
                    i.selected {
                        color:orange;
                        opacity:1.0;
                    }
                    i.selected-alt {
                        color:blue;
                        opacity:1.0;
                    }
                }
            }
        }
    }
    
    /*フィルター部分*/
    .filters {
        margin:1em 0;
        padding:1em;
        border-radius:0.5em;
        background-color:rgba(grey,0.3);
        .filter {
            div {
                display:flex;
                &>* {
                    margin:0 1em;
                }
            }
        }
        .operator {
            margin-left:3em;
            cursor:pointer;
            transform:rotate(45deg);
            transition:all 0.2s;
            &.or {
                transform:rotate(90deg);
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
        padding:1em;
        div {
            display:flex;
            flex-direction:column;
            justify-content:center;
            align-items:center;
            i {
                color:grey;
                display:inline-block;
                margin:0.5em;
                cursor:pointer;
                transition:all 0.2s ease;
            }
        }
        .decoration {
            margin-bottom:2em;
            i:hover {
                text-shadow: 0 0 5px orange;
            }
            .selected{
                text-shadow: 0 0 5px orange;
            }
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
    
    /*インターフェース系*/
    .invisible-sort {
        .column {
            i {
                display:none;
            }
        }
    }
    
</style>
