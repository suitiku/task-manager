<!--複数フィルターを適用するコンポーネント-->

<template>
    <div class="container">
        <!--モーダル-->
        <modal ref="filterModal">
            {{columnName}}
            <!--フィルターするカラム名-->
                <tag-cloud v-model="columnName" v-bind:options="fileterOptions" />
            
            <!--値-->
                <!--１．数字-->
            
                <!--２．5段階評価-->
                <star-range v-if="columnName == 'priority' || columnName == 'difficulty'" v-model="comparisonValue" />
            
                <!--３．日付-->
                <!--４．文字列-->
            
            <!--演算子-->
            <tag-cloud v-model="comparisonOperator" v-bind:options="operatorOptions" />
            
            <button v-on:click="addFilter()">フィルターを追加</button>
        </modal>
        <!--処理部-->
        <div class="filter-container">
            <div v-for="(filter,index) in filters">
                <filter-array v-model="filteredArray[index]" v-bind:key="index" v-bind:originalArray="targetArray" v-bind:columnName="filter.columnName" v-bind:comparisonValue="filter.comparisonValue" v-bind:comparisonOperator="filter.comparisonOperator" />
                <span class="operator">{{filterOperators[index]}}</span>
            </div>
        </div>
        <button v-on:click="showFilterModal()">add filter</button>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                filteredArray:[],
                filters:[],
                filterOperators:[],
                // タグクラウド用option
                fileterOptions:[
                    {label:'優先度',value:'priority'},          
                    {label:'重要度',value:'difficulty'}            
                ],
                operatorOptions:[
                    {label:'<',value:'<'},
                    {label:'<=',value:'<='},
                    {label:'=',value:'='},
                    {label:'>=',value:'>='},
                    {label:'>',value:'>'},
                ],
                columnName:'',
                comparisonValue:'',
                comparisonOperator:''
            }
        },
        props: {
            targetArray:{
                type:[Array,String]
            }
        },
        watch:{
            //個別にフィルターされた配列をand/or演算して出力
            filteredArray:function(){
                if(this.filteredArray.length == 1){
                    this.$emit('input',this.filteredArray[0])
                }else{
                    this.$emit('input',[])
                    this.operate()
                }
            }
            
        },
        created:function(){
            
        },
        mounted:function(){
            
        },
        methods: {
            showFilterModal:function(){
                  this.$refs.filterModal.openModal()
            },
            addFilter:function(){
                let filter = {
                    columnName:this.columnName,
                    comparisonValue:this.comparisonValue,
                    comparisonOperator:this.comparisonOperator
                }
                this.filteredArray.push({})
                this.filters.push(filter)
                this.filterOperators.push('*')
                // console.log(this.filteredArray)
            },
            // 配列をAnd/Or演算して出力
            operate:function(){
                let result = []
                let resultIds = []
                let ids = []
                for(let index in this.filteredArray){
                    if(index == 0){
                        for(let el of this.filteredArray[0]){
                            // resultIds.push(el.id)
                            ids.push(el.id)
                        }
                    }else{
                        resultIds = []
                        if(!this.filteredArray[index].length){continue }
                        if(this.filterOperators[index - 1] == '*'){
                        // 積
                            console.log('かける')
                            // console.log(this.filteredArray[index].length)
                            // console.log(ids)
                            // console.log(index)
                            // console.log(this.filteredArray[index])
                            for(let el of this.filteredArray[index]){
                                if(ids.indexOf(el.id) != -1){
                                    resultIds.push(el.id)
                                }
                            }
                        }else{
                        // 和
                            console.log('たす')
                        }
                        ids = resultIds
                        console.log(ids)
                    }
                }
                // idから配列を復元して出力
                for(let el of this.targetArray){
                    if(resultIds.indexOf(el.id) != -1){
                        result.push(el)
                    }
                }
                this.$emit('input',result)
            },
            //andとorを切り替え
            toggleOperator:function(index){
                
            }
        }
    }
</script>
<style scoped>
    .filter-container {
        display:flex;
        border:1px solid grey;
        border-radius:0.2em;
        padding:0.8em;
    }
    .operator {
        margin:0 0.5em;
        cursor:pointer;
    }
</style>
