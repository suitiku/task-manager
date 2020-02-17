<!--複数フィルターを適用するコンポーネント-->
<!--今後の改修ポイント-->
<!--１．テキスト検索の実装-->
<!--２．数値検索の実装-->
<!--３．デザインの修正-->
<template>
    <div class="container">
        <!--ツールチップ-->
        <tool-tip ref="toolTipDelete">
            <div class="tool-tip-content">
                <span>このフィルターを削除します　</span>
                <button class="btn btn-sm btn-warning d-block" v-on:click="deleteFilter()">OK</button>
            </div>
        </tool-tip>
        <tool-tip ref="toolTip"><span>フィルターを追加します</span></tool-tip>
        <!--モーダル-->
        <modal ref="filterModal">
            <!--フィルターするカラム名-->
                <tag-cloud v-model="columnName" v-bind:options="filterOptions" />
            
            <!--値-->
                <!--数字：number,五段階評価：star,日付：date,文字列：string-->
                <component v-bind:is="showComparisonValueComponent()" v-model="comparisonValue" />
            
            <!--演算子-->
            <tag-cloud v-model="comparisonOperator" v-bind:options="operatorOptions" />
            
            <button v-on:click="addFilter()">フィルターを追加</button>
        </modal>
        <!--処理部-->
        <div class="filter-container">
            <div v-for="(filter,index) in filters" class="filter-wrapper">
                <div v-on:click="showDeleteFilterToolTip(index)" v-on:mouseout="hideDeleteFilterToolTip()">
                    <filter-array 
                        v-model="filteredArray[index]" 
                        v-bind:key="index" 
                        v-bind:originalArray="targetArray" 
                        v-bind:columnName="filter.columnName" 
                        v-bind:columnLabel="filter.columnLabel" 
                        v-bind:comparisonValue="filter.comparisonValue" 
                        v-bind:comparisonOperator="filter.comparisonOperator"
                    />
                </div>
                <span v-bind:class="setOperatorClass(index)" v-on:click="toggleOperator(index)">+</span>
            </div>
            <i class="fas fa-plus-circle fa-lg add-button" 
                v-on:click="showFilterModal()" 
                v-on:mouseover="showToolTip()"
                v-on:mouseout="hideToolTip()">
            </i>
        </div>
        
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                filteredArray:[],
                filters:[],
                filterOperators:[],
                operatorOptions:[
                    {label:'<',value:'<'},
                    {label:'<=',value:'<='},
                    {label:'=',value:'='},
                    {label:'>=',value:'>='},
                    {label:'>',value:'>'},
                ],
                columnName:'',
                comparisonValue:'',
                comparisonOperator:'',
                deleteTargetIndex:''
            }
        },
        props: {
            targetArray:{
                type:[Array,String]
            },
            filterOptions:{
                type:[Array,String]
            }
        },
        watch:{
            //個別にフィルターされた配列をand/or演算して出力
            filteredArray:function(){
                if(!this.filteredArray || this.filteredArray.length == 0){ //フィルターが設定されていない場合は全部出力
                    this.$emit('input',this.targetArray)   
                }else if(this.filteredArray.length == 1){
                    this.$emit('input',this.filteredArray[0])
                }else{
                    this.$emit('input',[])
                    this.operate()
                }
            },
            targetArray:function(){
                if(!this.filteredArray || this.filteredArray.length == 0){ //フィルターが設定されていない場合は全部出力
                    this.$emit('input',this.targetArray)   
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
                let selectedOption = this.filterOptions.find(el => {
                    return el.value == this.columnName
                })
                let filter = {
                    columnName:this.columnName,
                    columnLabel:selectedOption.label,
                    comparisonValue:this.comparisonValue,
                    comparisonOperator:this.comparisonOperator
                }
                this.filteredArray.push({})
                this.filters.push(filter)
                this.filterOperators.push('*')
            },
            // 配列をAnd/Or演算して出力
            operate:function(){
                let result = []
                let resultIds = []
                let ids = []
                for(let index in this.filteredArray){
                    if(index == 0){
                        for(let el of this.filteredArray[0]){
                            resultIds.push(el.id)
                            ids.push(el.id)
                        }
                    }else{
                        if(!this.filteredArray[index].length){continue }
                        if(this.filterOperators[index - 1] == '*'){
                            // 積
                            resultIds = []
                            for(let el of this.filteredArray[index]){
                                if(ids.indexOf(el.id) != -1){
                                    resultIds.push(el.id)
                                }
                            }
                        }else{
                            // 和
                            // 追加用id配列
                            for(let el of this.filteredArray[index]){
                                ids.push(el.id)
                            }
                            // setオブジェクトを生成
                            let setObj = new Set(ids)
                            for(let id of setObj){
                                resultIds.push(id)
                            }
                        }
                        ids = resultIds
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
                if(this.filterOperators[index] == '*'){
                    this.filterOperators.splice(index,1,'+')
                }else{
                    this.filterOperators.splice(index,1,'*')
                }
                this.operate()
            },
            setOperatorClass:function(index){
                return this.filterOperators[index] == '*' ? 'operator-and' : 'operator-or'
            },
            showComparisonValueComponent:function(){
                if(!this.columnName){return ''}
                let selectedOption = this.filterOptions.find(el => {
                    return el.value == this.columnName
                })
                switch(selectedOption.type){
                    case 'star':
                        return 'star-range'
                    case 'number':
                        return ''
                    case 'date':
                        return 'date-picker'
                    case 'string':
                        return ''
                }
            },
            showDeleteFilterToolTip:function(index){
                this.deleteTargetIndex = index
                this.$refs.toolTipDelete.showToolTip()
            },
            hideDeleteFilterToolTip:function(){
                let vue = this
                let timer = window.setTimeout(function(){
                    vue.$refs.toolTipDelete.hideToolTip()
                    this.deleteTargetIndex = ''
                },2000)
            },
            deleteFilter:function(){
                this.filterOperators.splice(this.deleteTargetIndex,1)
                this.filters.splice(this.deleteTargetIndex,1)
                this.filteredArray.splice(this.deleteTargetIndex,1)
                this.$refs.toolTipDelete.hideToolTip() //ツールチップを消す
            },
            showToolTip:function(){
                this.$refs.toolTip.showToolTip()
            },
            hideToolTip:function(){
                this.$refs.toolTip.hideToolTip()
            },
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
    .filter-wrapper {
        display:flex;
    }
    .operator-or {
        display:inline-block;
        margin:0 0.5em;
        cursor:pointer;
        transition:all 0.5s ease;
    }
    .operator-and {
        display:inline-block;
        margin:0 0.5em;
        cursor:pointer;
        transform:rotate(45deg);
        transition:all 0.5s ease;
        color:red;
    }
    .tool-tip-content{
        display:flex;
        align-items:center;
    }
    .add-button {
        cursor:pointer;
    }
    
    
</style>
