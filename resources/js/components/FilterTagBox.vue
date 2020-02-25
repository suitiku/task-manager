<!--タグフィルターを適用するコンポーネント-->
<!--今後の改修ポイント-->
<template>
    <div class="container">
        <!--処理部-->
        <div class="filter-container">
            <div v-for="(selectedTagId,index) in selectedTagIds" class="filter-wrapper">
                <span class="filter-label">{{displayTagName(selectedTagId)}}</span>
                <div v-bind:class="setOperatorClass(index)" v-on:click="toggleOperator(index)"><span>+</span></div>
            </div>
        </div>
        <!--タグ一覧表示部分-->
        <div>
            <tag-cloud v-model="selectedTagIds" v-bind:options="tags" multiple />
        </div>
        
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                tags:[],
                filteredArray:[],
                filterOperators:[],
                selectedTagIds:[],
            }
        },
        props: {
            targetArray:{
                type:[Array,String]
            },
        },
        watch:{
            selectedTagIds:async function(newVal,oldVal){
                //配列リセット
                this.filteredArray = []
                
                //個別にフィルター
                for(let selectedTagId of this.selectedTagIds){
                    await this.filterTag(selectedTagId)
                }
                
                //and/or演算
                if(!this.selectedTagIds || this.selectedTagIds.length == 0){ //フィルターが設定されていない場合は全部出力
                    console.log('length 0')
                    this.$emit('input',this.targetArray)   
                }else if(this.selectedTagIds.length == 1){
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
            },
        },
        created:function(){
            
        },
        mounted:async function(){
            let result = await axios.get('/api/tags')
            for(let tag of result.data){
                this.tags.push({label:tag.name,value:tag.id})
            }
        },
        methods: {
            filterTag:async function(selectedTagId){
                //フィルター
                this.filteredArray.push(this.targetArray.filter(el => {
                    return el.tags.some(tag => tag.id == selectedTagId)
                }))
            },
            // 配列をAnd/Or演算して出力
            operate:function(){
                this.$emit('input',[]) //2020-02-18
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
                        // if(!this.filteredArray[index].length){continue }
                        if(this.filterOperators[index - 1] == '*'){
                            // 積
                            resultIds = []
                            if(!this.filteredArray[index].length){break } //空の要素があったら終了
                            for(let el of this.filteredArray[index]){
                                if(ids.indexOf(el.id) != -1){
                                    resultIds.push(el.id)
                                }
                            }
                        }else{
                            // 和
                            if(!this.filteredArray[index].length){continue } //空の要素の場合はループ飛ばす
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
            //タグIDからタグ名を取得
            displayTagName:function(selectedTagId){
                let tag = this.tags.find(el => el.value == selectedTagId)
                return tag.label
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
    .filter-wrapper {
        display:flex;
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
    .filter-label {
        padding:0.3em;
        border:1px solid gray;
        border-radius:0.2em;
    }
    
    
</style>
