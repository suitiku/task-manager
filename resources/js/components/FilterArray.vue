<!--複数フィルターを適用するコンポーネント-->
<!--今後の改修ポイント-->
<!--１．テキスト検索の実装-->
<!--２．数値検索の実装-->
<!--３．デザインの修正-->
<!--４．オプション検索（状態とか）を分離する？-->
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
                <!--数字：number,五段階評価：star,日付：date,文字列：string,候補：options-->
                <component 
                    v-bind:is="setComparisonValueComponent()" 
                    v-model="comparisonValue"
                    v-bind:options="setComparisonValueOptions()" />
            
            <!--演算子-->
            <!--optionsが選択された場合は表示しない（デフォルトの＝になる）-->
            <tag-cloud 
                v-show="showComparisonOperator()" 
                v-model="comparisonOperator" 
                v-bind:options="operatorOptions"
            />
            
            <button v-on:click="addFilter()">フィルターを追加</button>
        </modal>
        <!--メイン表示部-->
        <div class="filter-container">
            <div v-for="(filter,index) in filters" class="filter-wrapper">
                <span class="filter-label" v-on:click="showDeleteFilterToolTip(index)">{{setLabel(filter)}}</span>
                <div v-bind:class="setOperatorClass(index)" v-on:click="toggleOperator(index)"><span>+</span></div>
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
                comparisonOperator:'=',
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
            //個別にフィルター
            filters:function(){
                //リセット
                this.filteredArray = []
                //フィルターがない場合は全部出力
                if(!this.filters || this.filters.length == 0){
                    this.$emit('input',this.targetArray)
                    return 
                }
                
                for(let filter of this.filters){
                    //オペレータによって分岐
                    switch(filter.comparisonOperator){
                        case '<':
                            this.filteredArray.push(this.targetArray.filter(el => {
                                return el[filter.columnName] < filter.comparisonValue
                            }))
                            break
                        case '<=':
                            this.filteredArray.push(this.targetArray.filter(el => {
                                return el[filter.columnName] <= filter.comparisonValue
                            }))
                            break
                        case '=':
                            this.filteredArray.push(this.targetArray.filter(el => {
                                return el[filter.columnName] == filter.comparisonValue
                            }))
                            break
                        case '>=':
                            this.filteredArray.push(this.targetArray.filter(el => {
                                return el[filter.columnName] >= filter.comparisonValue
                            }))
                            break
                        case '>':
                            this.filteredArray.push(this.targetArray.filter(el => {
                                return el[filter.columnName] > filter.comparisonValue
                            }))
                            break
                    }
                }
                this.operate()
            },
            targetArray:function(){
                if(!this.filteres || this.filters.length == 0){ //フィルターが設定されていない場合は全部出力
                    this.$emit('input',this.targetArray)   
                }
            },
            columnName:function(){
                this.comparisonValue = ''
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
                
                //optionsが選択された場合は比較演算子を==にする
                if(selectedOption.type == 'options'){this.comparisonOperator = '='}
                
                let filter = {
                    columnName:this.columnName,
                    columnLabel:selectedOption.label,
                    comparisonValue:this.comparisonValue,
                    comparisonOperator:this.comparisonOperator
                }
                this.filteredArray.push({})
                this.filters.push(filter)
                this.filterOperators.push('+')
            },
            // 配列をAnd/Or演算して出力
            operate:function(){
                // リセット
                this.$emit('input',[])
                let result = []
                let resultIds = []
                let ids = []
                
                //フィルターの数が0/1の場合
                if(!this.filteredArray || this.filteredArray.length == 0){ //フィルターが設定されていない場合は全部出力
                    this.$emit('input',this.targetArray)
                }else if(this.filteredArray.length == 1){
                    this.$emit('input',this.filteredArray[0])
                }
                
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
            setComparisonValueComponent:function(){
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
                    case 'options':
                        return 'tag-cloud'
                }
            },
            setComparisonValueOptions:function(){
                if(!this.columnName){return ''}
                let selectedOption = this.filterOptions.find(el => {
                    return el.value == this.columnName
                })
                return selectedOption.options
            },
            showComparisonOperator:function(){
                if(!this.columnName){return ''}
                let selectedOption = this.filterOptions.find(el => {
                    return el.value == this.columnName
                })
                return selectedOption.type != 'options' || false
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
            setLabel:function(filter){
                let selectedFilterOption = this.filterOptions.find(el => {
                    return el.value == filter.columnName
                })
                if(selectedFilterOption.type == 'options'){
                    return selectedFilterOption.label + ': ' + selectedFilterOption.options.find(el => {
                        return el.value == filter.comparisonValue
                    }).label
                }else{
                    return filter.columnLabel + ' ' + filter.comparisonOperator + ' ' + filter.comparisonValue
                }
            }
        }
    }
</script>
<style scoped>
    .filter-container {
        display:flex;
        align-items:center;
        border:1px solid grey;
        border-radius:0.2em;
        padding:0.8em;
    }
    .filter-wrapper {
        display:flex;
    }
    .filter-label {
        padding:0.3em;
        border:1px solid gray;
        border-radius:0.2em;
        cursor:pointer;
    }
    .operator-or {
        display:flex;
        align-items:center;
        cursor:pointer;
        margin:0 0.5em;
        transition:all 0.5s ease;
    }
    .operator-and {
        display:flex;
        align-items:center;
        cursor:pointer;
        margin:0 0.5em;
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
