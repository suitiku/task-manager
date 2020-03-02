<!--フィルター統合コンポーネント-->
<!--今後の改修ポイント-->
<!--１．フィルターのoffでフィルタークリア-->
<template>
    <div>
        <div class="filter-select">
            <i class="fas fa-filter" v-on:click="selectFilter('normal')"></i>
            <i class="fas fa-tags" v-on:click="selectFilter('tag')"></i>
            <!--<i class="fas fa-user" v-on:click="selectFilter('person')"></i>　ユーザーフィルターはあとで実装--> 
        </div>
        <div v-bind:class="filterClass">
            <filter-array v-show="selectedFilters.indexOf('normal') != -1" v-model="nomalFilteredArray" v-bind:targetArray="originalArray" v-bind:filterOptions="filterOptions" />
            <filter-tag v-show="selectedFilters.indexOf('tag') != -1" v-model="tagFilteredArray" v-bind:targetArray="nomalFilteredArray" />
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                selectedFilters:[],
                nomalFilteredArray:[],
                tagFilteredArray:[],
                filterClass:'disable'
            }  
        },
        props: {
            originalArray:{
                type:[String,Array,Object],
            },
            filterOptions:{
                type:[String,Array,Object]
            }
        },
        watch:{
            tagFilteredArray:function(){
                this.$emit('input',this.tagFilteredArray)
            },
            selectedFilters:function(){
                this.filterClass = this.selectedFilters.length != 0 ? 'visible' : 'disable'
            }
        },
        created:function(){
            
        },
        mounted:function(){
            
        },
        methods: {
            selectFilter:function(filterName){
                let index = this.selectedFilters.indexOf(filterName)
                if(index != -1){
                    this.selectedFilters.splice(index,1)
                }else{
                    this.selectedFilters.push(filterName)
                }
                event.target.classList.toggle('selected')
            }
        }
    }
</script>
<style scoped>
    .filter-select {
        display:flex;
        padding:1em;
    }
    .filter-select i {
        font-size:120%;
        margin-right:0.5em;
        cursor:pointer;
    }
    .selected {
        color:orange;
    }
    .disable {
        display:none;
    }
    .visible {
        display:block;
    }
    .visible div {
        margin:0.5em 0;
    }
</style>
