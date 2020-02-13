<!--単一ファイルコンポーネントのテンプレートファイル-->
<template>
    <span class="filter-label">{{columnLabel}} {{comparisonOperator}} {{comparisonValue}}</span>
</template>

<script>
    export default {
        data:function(){
            return {
                filteredArray:[]
            }  
        },
        props: {
            originalArray:{
                type:[Array,String],
            },
            columnName:{
                type:String,
            },
            columnLabel: {
                type:String,  
            },
            comparisonValue:{
                type:[String,Number]
            },
            comparisonOperator:{
                type:String,
                defalut:'=',
            }
        },
        watch:{
            columnName:{
                immediate:true,
                handler(){
                    this.filterArray()
                }
            },
            comparisonValue:{
                immediate:true,
                handler(){
                    this.filterArray()
                }
            },
            comparisonOperator:{
                immediate:true,
                handler(){
                    this.filterArray()
                }
            }
            
        },
        created:function(){
            
        },
        mounted:function(){
            
        },
        methods: {
            filterArray:function(){
                //3つのパラメータが揃っていない場合は終了
                if(!this.columnName || !this.comparisonValue || !this.comparisonOperator){return }
                //オペレータによって分岐
                switch(this.comparisonOperator){
                    case '<':
                        this.filteredArray = this.originalArray.filter(el => {
                            return el[this.columnName] < this.comparisonValue
                        })
                        break
                    case '<=':
                        this.filteredArray = this.originalArray.filter(el => {
                            return el[this.columnName] <= this.comparisonValue
                        })
                        break
                    case '=':
                        this.filteredArray = this.originalArray.filter(el => {
                            return el[this.columnName] == this.comparisonValue
                        })
                        break
                    case '>=':
                        this.filteredArray = this.originalArray.filter(el => {
                            return el[this.columnName] >= this.comparisonValue
                        })
                        break
                    case '>':
                        this.filteredArray = this.originalArray.filter(el => {
                            return el[this.columnName] > this.comparisonValue
                        })
                        break
                }
                this.$emit('input',this.filteredArray)
            }
        }
    }
</script>
<style scoped>
    .filter-label {
        padding:0.3em;
        border:1px solid gray;
        border-radius:0.2em;
    }
</style>
