<!--リスト表示コンポーネント-->
<template>
    <div>
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
        <div>
            <span v-for="(columnName,index) in listDefinition" v-bind:style="setColumnWidth(index)" v-on:click="clickColumn(index)" class="column">{{columnName.name}}</span>
        </div>
        <div v-for="(item,index) in listItems">
            <span v-for="(column,columnIndex) in item" v-bind:style="setColumnWidth(columnIndex)">{{column.value}} {{listDefinition[columnIndex].suffix}}</span>
        </div>
        {{columnWidths}}
    </div>
</template>

<script>
    import axios from 'axios'
    export default {
        data:function(){
            return {
                listDefinition:[],
                listItems:[],
                columnWidths:[],
                newColumnModal:false,
                newColumn:{
                    name:'',
                    type:'',
                    suffix:'',
                    default:'',
                }
            }  
        },
        props: {
            listId:{
                type:[Number,String],
                default:0,
                required:true,
            },
            editMode:{
                type:[Boolean,Number],
                default:true,
                required:false
            }
        },
        watch:{
            
        },
        created:async function(){
            if(this.listId){
                let result = await axios.get('/api/lists/' + this.listId)
                console.log(result.data)
                this.listDefinition = JSON.parse(result.data.column_definitions)
                for(let item of result.data.my_list_items){
                    this.listItems.push(JSON.parse(item.values))
                }
                this.getColumnWidths()
            }
        },
        mounted:function(){
        },
        computed:{
            
        },
        methods: {
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
            showAddColumnModal:function(){
                this.$refs.newColumnModal.openModal()
            },
            addColumn:function(){
                //newColumnをリセット
                this.newColumn = {
                    name:'',
                    type:'',
                    suffix:'',
                    default:'',
                }
                let columnIndex = this.testDefinition.length
                this.newColumn.index = columnIndex
                this.testDefinition.push(this.newColumn)
                let addColumn = {
                    index:columnIndex,
                    value:this.newColumn.default
                }
                for(let item of this.testItems){
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
                        column_definitions:JSON.stringify(this.testDefinition),
                        type:'nomal',
                        is_stared:false
                    },
                    items:[]
                }
                //アイテム
                for(let index in this.testItems){
                    let postItem = {
                        index:index,
                        values:JSON.stringify(this.testItems[index]),
                        is_checked:false,
                        is_stared:false,
                    }
                    postData.items.push(postItem)
                }
                console.log(postData)
                try{
                    let result = await axios.post('/api/lists',postData)
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
                    
                }
            }
        }
    }
</script>
<style scoped>
    span {
        display:inline-block;
        padding:0.5em;
        border:1px solid grey;
    }
    .column {
        cursor:pointer;
    }
</style>
