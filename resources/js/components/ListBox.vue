<!--リストボックスコンポーネント-->
<!--propsでテーブル名（String）とカラム名（Array）を指定して表示-->
<!--返り値：選択されたもののid配列-->
<!--今後の改修ポイント-->
<!--１．検索／絞り込み機能-->
<!--２．アニメーション付きのチェックボックス-->
<!--３．複数選択時に選択したものを別枠に表示-->
<template>
    <div class="container">
        <div class="wrapper">
            <div v-for="(item,index) in items" class="item-wrapper">
                <div v-if="value == item.id" class="item selected" v-on:click="selectItem(item.id)"></div>
                <div v-else class="item" v-on:click="selectItem(item.id)"></div>
                <div class="column-wrapper">
                    <div v-for="(column,columnIndex) in columns">
                        <span v-if="columnIndex == 0">{{item[column]}}</span>
                        <span v-else><span class="column-label">{{column}}</span> {{item[column]}}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                items:[],
                ids:[]
            }  
        },
        props:{
              table:{
                  type:String,
                  default:'',
                  required:true
              },
              columns: {
                    type:Array,
                    default:() => (['name']),
                    required:false
              },
              value: {
                    type:[String,Number],
                    required:false
              },
              is_multiple:{
                  type:Boolean,
                  default:false,
                  required:false
              }
        },
        watch:{
        },
        created(){
            
        },
        mounted() {
            this.init()
        },
        methods: {
            init:async function(){
                let result = await axios.get('/api/' + this.table)
                this.items = result.data
            },
            selectItem: function(id){
                if(this.is_multiple){
                    //複数選択
                    if(event.target.className == 'item selected'){
                        let index = this.ids.indexOf(id)
                        this.ids.splice(index,1)
                        event.target.className = 'item'
                    }else{
                        event.target.className = 'item selected'
                        this.ids.push(id)
                    }
                }else{
                    this.ids = []
                    if(event.target.className == 'item selected'){
                        event.target.className = 'item'
                    }else{
                        let el = document.getElementsByClassName('selected')
                        if(el[0]){el[0].className = 'item'}
                        event.target.className = 'item selected'
                        this.ids.push(id)
                    }
                }
                this.$emit('input',this.ids)
            },
            resetValue:function(){
                this.ids = []
                this.$emit('input',this.ids)
                let els = document.getElementsByClassName('item selected')
                if(els.length > 0){
                    for(let index in Object.keys(els)){
                        els[index].className = 'item'
                    }
                }
            }
        }
    }
</script>
<style scoped>
    .wrapper {
        border:2px solid grey;
        padding:1em;
        max-height:200px;
        overflow:auto;
    }
    .item-wrapper {
        position:relative;
        width:100%;
        /*height:2em;*/
        overflow:hidden;
        border-top:1px solid black;
        border-left:1px solid black;
        border-right:1px solid black;
    }
    .item-wrapper:last-of-type {
        border-bottom:1px solid black;
    }
    .item {
        width:100%;
        height:100%;
        position:absolute;
        z-index:2;
        cursor:pointer;
        transition:all 0.3s;
    }
    .column-wrapper {
        padding:0.3em;
        display:flex;
        justify-content:space-between;
    }
    .selected {
        background:grey;
        opacity:0.5;
    }
    .column-label {
        padding:0em 0.2em;
        background:burlywood;
        border-radius:0.3em;
        font-size:30%;
    }
</style>
