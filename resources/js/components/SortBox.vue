<!--単一ファイルコンポーネントのテンプレートファイル-->
<template>
    <div class="sort-box-wrapper">
        <div v-for="(column,index) in columns" class="sort-wrapper">
            <div class="sort-label" ref="sortButton" v-on:click="enable(column.columnName,index)">{{column.columnLabel}}</div>
        </div>
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
                currentColumnName:'',
                currentButtonIndex:'',
                ascending:true,
                output:[]
            }  
        },
        props: {
            columns:{
                type:[String,Object,Array]
            },
            originalArray:{
                type:[String,Object,Array]
            }
        },
        watch:{
            originalArray:function(){
                if(this.currentColumnName == ''){
                    this.$emit('input',this.originalArray)
                }else{
                    this.sortArray()
                }
            },
            currentColumnName:{
                immediate:true,
                handler:function(){
                    if(this.currentColumnName === ''){
                        this.$emit('input',this.originalArray)
                    }else{
                        this.sortArray()
                    }
                }
            }
        },
        created:function(){
            
        },
        mounted:function(){
            
        },
        methods: {
            enable:function(columnName,buttonIndex){
                if(this.currentButtonIndex === ''){
                    event.target.classList.toggle('enable')
                    this.currentButtonIndex = buttonIndex
                }else if(this.currentButtonIndex === buttonIndex){
                    event.target.classList.toggle('enable')
                    this.currentButtonIndex = ''
                }else{
                    this.$refs.sortButton[this.currentButtonIndex].classList.remove('enable')
                    event.target.classList.toggle('enable')
                    this.currentButtonIndex = buttonIndex
                }
                
                //現在のsortカラムと同じ場合は無効化
                this.currentColumnName = this.currentColumnName == columnName ? '' : columnName
            },
            sortArray:function(){
                //昇順でソート
                let sortedArray =JSON.parse(JSON.stringify(this.originalArray))
                let vue = this
                sortedArray.sort(function(a,b) {
                    if(a[vue.currentColumnName] < b[vue.currentColumnName]) return -1
                    if(a[vue.currentColumnName] > b[vue.currentColumnName]) return 1
                    return 0
                })
                if(!this.ascending){sortedArray.reverse()}
                this.$emit('input',sortedArray)
            },
            // 昇順/降順を切り替える
            toggleAscending:function(){
                if(this.currentColumnName === ''){return }
                this.ascending = !this.ascending
                this.sortArray()
            },
            setArrowStyle:function(flag){
                if((this.ascending && flag == 'desc') || (!this.ascending && flag == 'asc')){
                    return {color:'white'}
                }else{
                    return ''
                }
            },
        }
    }
</script>
<style scoped>
    .sort-box-wrapper {
        display:flex;
    }
    .sort-wrapper {
        display:flex;
        align-items:center;
        padding:0 0.4em;
    }
    .enable {
        background:#ffff7f;
    }
    .sort-label {
        border:1px solid grey;
        padding:0 0.8em;
        margin-right:0.2em;
        border-radius:0.2em;
        cursor:pointer;
        transition:all 0.5s;
    }
    .ascending-descending-selector {
        display:flex;
        flex-direction:column;
        cursor:pointer;
    }
    .ascending-descending-selector i {
        font-size:120%;
        color:grey;
        transition:all 0.3s;
    }
</style>
