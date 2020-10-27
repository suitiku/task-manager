<!--リスト表示コンポーネント-->
<template>
    <div>
        <div>
            <span v-for="(columnName,index) in testDefinition" v-bind:style="setColumnLength(index)">{{columnName.name}}</span>
        </div>
        <div v-for="(item,index) in testItems">
            <span v-for="(column,columnIndex) in item" v-bind:style="setColumnLength(columnIndex)">{{column.value}} {{testDefinition[columnIndex].suffix}}</span>
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                testDefinition:[
                    {index:0,name:'品目',type:'Text',suffix:''},
                    {index:1,name:'価格',type:'Number',suffix:'円'},
                    {index:2,name:'在庫数',type:'Number',suffix:'個'},
                ],
                testItems: [
                    [{index:0,value:'りんご'},{index:1,value:'100'},{index:2,value:'10'}],
                    [{index:0,value:'パイナップル'},{index:1,value:'200'},{index:2,value:'5'}],
                    [{index:0,value:'バナナ'},{index:1,value:'40'},{index:2,value:'20'}],
                ],
                columnLength:[]
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
        created:function(){
            
        },
        mounted:function(){
            // 各カラムの最大幅を求める
            for(let column of this.testDefinition){
                if(!this.columnLength[column.index] || this.columnLength[column.index] < column.name.length){
                    this.columnLength.splice(column.index,1,column.name.length)
                }
            }
            for(let item of this.testItems){
                for(let column of item){
                    if(!this.columnLength[column.index] || this.columnLength[column.index] < column.value.length){
                        this.columnLength.splice(column.index,1,column.value.length)
                    }
                }
            }
        },
        computed:{
            
        },
        methods: {
            setColumnLength:function(index){
                return {width:this.columnLength[index] + 2 + 'em'}
            }
        }
    }
</script>
<style scoped>
    div {
        /*margin:0.5em;*/
    }
    span {
        display:inline-block;
        padding:0.5em;
        border:1px solid grey;
    }
</style>
