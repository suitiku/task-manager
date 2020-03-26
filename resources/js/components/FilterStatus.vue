<!--ステータスフィルターを適用するコンポーネント-->
<!--デフォルトは1の実行中-->
<!--今後の改修ポイント-->

<template>
    <div class="filter-container">
        <tag-cloud v-bind:options="statuses" v-model="selectedStatusIds" multiple/>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                statuses:[],
                filteredArray:[],
                selectedStatusIds:[], //デフォルトは1の実行中
            }
        },
        props: {
            originalArray:{
                type:[Array,String,Object]
            },
        },
        watch:{
            selectedStatusIds:async function(){
                this.filterStatus()
            },
            originalArray:async function(){
                this.filterStatus()
            },
        },
        created:function(){
            
        },
        mounted:async function(){
            // ステータス一覧を取得
            let result = await axios.get('/api/states')
            for(let state of result.data){
                this.statuses.push({label:state.name,value:state.id})
            }
        },
        methods: {
            filterStatus:async function(){
                //配列リセット
                this.filteredArray = []
                
                //個別にフィルター
                for(let selectedStatusId of this.selectedStatusIds){
                    this.filteredArray.push(this.originalArray.filter(el => {
                        return el.states[el.states.length - 1].id == selectedStatusId
                    }))
                    // await this.filterStatus(selectedStatusId)
                }
                //出力
                if(!this.selectedStatusIds || this.selectedStatusIds.length == 0){ //フィルターが設定されていない場合は全部出力
                    this.$emit('input',this.originalArray)   
                }else if(this.selectedStatusIds.length == 1){
                    this.$emit('input',this.filteredArray[0])
                }else{
                    this.$emit('input',[])
                    this.operate()
                }
            },
            // filterStatus:async function(selectedStatusId){
            //     //フィルター
            //     this.filteredArray.push(this.originalArray.filter(el => {
            //         return el.states[el.states.length - 1].id == selectedStatusId
            //     }))
            // },
            // 配列をAnd演算して出力
            operate:function(){
                this.$emit('input',[])
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
                // idから配列を復元して出力
                for(let el of this.originalArray){
                    if(resultIds.indexOf(el.id) != -1){
                        result.push(el)
                    }
                }
                this.$emit('input',result)
            },
        }
    }
</script>
<style scoped>
    .filter-container {
        display:flex;
        border:1px solid grey;
        border-radius:0.2em;
        padding:0.2em 0.8em;
    }
</style>
