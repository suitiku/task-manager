<!--配列をソート-->
<template>
    <div class="sort-wrapper">
        <div class="sort-label" v-on:click="clickButton()">{{columnLabel}}</div>
        <div class="ascending-descending-selector" v-on:click="toggleAscending()">
            <i class="fas fa-sort-up" v-bind:style="setArrowStyle('desc')"></i>
            <i class="fas fa-sort-down" v-bind:style="setArrowStyle('asc')"></i>
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                enable:false,
                 ascending:true,
            }  
        },
        props: {
            targetArray:{
                type:[Array,String]
            },
            columnName:{
                type:String,
                default:'',
                required:false
            },
            columnLabel: {
                type:String,
                default:'',
                required:false,
            },
        },
        watch:{
            //初期状態はそのまま出力
            targetArray:function(){
                if(!this.enable){
                    this.$emit('input',this.targetArray)
                }else{
                    this.sortArray()
                }
            }
            
        },
        created:function(){
            
        },
        mounted:function(){
            
        },
        methods: {
            clickButton:function(){
                // カラー変更
                event.target.classList.toggle('enable')
                
                //有効化／無効化トグル
                this.enable = !this.enable
                //無効の場合は元の配列を
                if(!this.enable){
                    this.$emit('input',this.targetArray)
                    return
                }
                
                this.sortArray()
            },
            sortArray:function(){
                //昇順でソート
                let sortedArray =JSON.parse(JSON.stringify(this.targetArray))
                let vue = this
                sortedArray.sort(function(a,b) {
                    if(a[vue.columnName] < b[vue.columnName]) return -1
                    if(a[vue.columnName] > b[vue.columnName]) return 1
                    return 0
                })
                if(!this.ascending){sortedArray.reverse()}
                this.$emit('input',sortedArray)
            },
            // 昇順/降順を切り替える
            toggleAscending:function(){
                if(!this.enable) return
                this.ascending = !this.ascending
                this.sortArray()
            },
            setArrowStyle:function(flag){
                if((this.ascending && flag == 'desc') || (!this.ascending && flag == 'asc')){
                    return {color:'white'}
                }else{
                    return ''
                }
            }
            
        }
    }
</script>
<style scoped>
    .sort-wrapper {
        display:flex;
        align-items:center;
        padding:0 0.4em;
        transition:all 0.5s;
    }
    .enable {
        background:#ffff7f;
    }
    .sort-label {
        border:1px solid grey;
        padding:0.2em;
        margin-right:0.2em;
        border-radius:0.2em;
        cursor:pointer;
    }
    .ascending-descending-selector {
        display:flex;
        flex-direction:column;
        cursor:pointer;
        /*border:1px solid red;*/
    }
    .ascending-descending-selector i {
        margin:-0.3em 0;
        font-size:120%;
        color:grey;
        transition:all 0.3s;
    }
</style>
