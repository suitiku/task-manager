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
                <span v-if="index != 0">×</span>
                <filter-array v-model="filteredArray[index]" v-bind:key="index" v-bind:originalArray="targetArray" v-bind:columnName="filter.columnName" v-bind:comparisonValue="filter.comparisonValue" v-bind:comparisonOperator="filter.comparisonOperator" />
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
                console.log(this.filteredArray)
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
    
</style>
