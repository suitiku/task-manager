<!--単一ファイルコンポーネントのテンプレートファイル-->
<template>
    <span class="filter-label">{{columnName}} {{comparisonOperator}} {{comparisonValue}}</span>
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
            comparisonValue:{
                type:[String,Number]
            },
            comparisonOperator:{
                type:String,
                defalut:'=',
            }
        },
        watch:{
            
        },
        created:function(){
            
        },
        mounted:function(){
            
        },
        methods: {
            filterArray:function(){
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
                            return el[this.columnName] = this.comparisonValue
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
