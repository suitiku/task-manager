<!--タグフィルターを適用するコンポーネント-->
<!--今後の改修ポイント-->
<!--１．「タグなし」のフィルタリング-->
<!--２．タグの表示を改善-->
<template>
    <div class="container">
        <!--モーダル-->
        <modal ref="tagModal" v-model="tagModal">
            <p>タグを選択して下さい。</p>
            <tag-list v-model="selectedTagIds" ref="tagList" v-bind:userId="userId" />
        </modal>
        
        <!--処理部-->
        <div class="filter-container">
            <div v-for="(selectedTagId,index) in selectedTagIds" class="filter-wrapper">
                <span class="filter-label" v-bind:style="getTagColor(selectedTagId)">{{getTagName(selectedTagId)}}</span>
                <div v-bind:class="setOperatorClass(index)" v-on:click="toggleOperator(index)"><span>+</span></div>
            </div>
            <button class="btn btn-primary mx-auto d-block" v-on:click="showTagModal()">タグを選択</button>
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
                tagModal:false,
                userId:'',
            }
        },
        props: {
            targetArray:{
                type:[Array,String,Object]
            },
        },
        watch:{
            selectedTagIds:async function(newVal,oldVal){
                this.filterTag()
            },
            targetArray:function(newVal,oldVal){
                if(oldVal.length == 0 && newVal.length != 0){
                    this.userId = this.targetArray[0].user_id
                    this.fetchTags()
                }
                this.filterTag()
            },
        },
        created:function(){
            
        },
        mounted:function(){
        },
        methods: {
            fetchTags:async function(){
                if(!this.userId)return
                let result = await axios.get('/api/mytags',{
                                                    params:{user_id:this.userId,}
                                                })
                
                for(let tagColor in result.data){
                    for(let tag of result.data[tagColor]){
                        this.tags.push(tag)
                    }
                }
            },
            filterTag:async function(){
                //配列リセット
                this.filteredArray = []
                
                //個別にフィルター
                for(let selectedTagId of this.selectedTagIds){
                    this.filteredArray.push(this.targetArray.filter(el => {
                        return el && el.tags.some(tag => tag.id == selectedTagId)
                    }))
                }
                
                //and/or演算
                if(!this.selectedTagIds || this.selectedTagIds.length == 0){ //フィルターが設定されていない場合は全部出力
                    this.$emit('input',this.targetArray)   
                }else if(this.selectedTagIds.length == 1){
                    this.$emit('input',this.filteredArray[0])
                }else{
                    this.$emit('input',[])
                    this.operate()
                }
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
            getTagName:function(selectedTagId){
                let tag = this.tags.find(el => el.id == selectedTagId)
                return tag.name
            },
            getTagColor:function(selectedTagId){
                let tag = this.tags.find(el => el.id == selectedTagId)
                return {background:tag.color}
            },
            showTagModal:function(){
                this.$refs.tagList.init()
                this.$refs.tagModal.openModal()
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
        display:flex;
        align-items:center;
        padding:0.2em;
        border:1px solid gray;
        border-radius:0.2em;
    }
</style>
