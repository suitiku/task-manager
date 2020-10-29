<!--リスト表示コンポーネント-->
<template>
    <div>
        <!--列追加用モーダル-->
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
            <button v-on:click="addColumn()">列を追加</button>
        </modal>
        
        <!--リスト表示部-->
        <div>
            <span v-for="(columnName,index) in listDefinition" v-bind:style="setColumnWidth(index)">{{columnName.name}}</span>
        </div>
        <div v-for="(item,index) in listItems">
            <span v-for="(column,columnIndex) in item" v-bind:style="setColumnWidth(columnIndex)">{{column.value}} {{listDefinition[columnIndex].suffix}}</span>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'
    export default {
        data:function(){
            return {
                // testDefinition:[
                //     {index:0,name:'品目',type:'Text',suffix:'',default:'noitem'},
                //     {index:1,name:'価格',type:'Number',suffix:'円',default:0},
                //     {index:2,name:'在庫数',type:'Number',suffix:'個',default:0},
                // ],
                // testItems: [
                //     [{index:0,value:'りんご'},{index:1,value:'100'},{index:2,value:'10'}],
                //     [{index:0,value:'パイナップル'},{index:1,value:'200'},{index:2,value:'5'}],
                //     [{index:0,value:'バナナ'},{index:1,value:'40'},{index:2,value:'20'}],
                // ],
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
                required:false, //とりあえずfalse
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
                for(let column of this.testDefinition){
                    let addColumn = {index:column.index,value:column.default}
                    addItem.push(addColumn)
                }
                this.testItems.push(addItem)
            },
            showAddColumnModal:function(){
                this.$refs.newColumnModal.openModal()
            },
            addColumn:function(){
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
                this.columnLength.push(width)
            },
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
</style>
