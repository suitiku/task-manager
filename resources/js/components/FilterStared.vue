<!--ステータスフィルターを適用するコンポーネント-->
<!--デフォルトは1の実行中-->
<!--今後の改修ポイント-->

<template>
    <div>
        <!--ボタン部分-->
        <div ref="staredFilterButton" v-on:click="toggleStaredFilter()" class="filter-button">
            <i class="fas fa-star"></i>
            <span>スター</span>
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                stared:false,
                filteredArray:[]
            }
        },
        props: {
            originalArray:{
                type:[Array,String,Object]
            },
        },
        watch:{
            originalArray:async function(){
                this.filterStared()
            },
        },
        created:function(){
            
        },
        mounted:async function(){
        },
        computed:{
        },
        methods: {
            toggleStaredFilter:function(){
                this.stared = !this.stared
                this.$refs.staredFilterButton.classList.toggle('filter-button-active')
                this.filterStared()
            },
            filterStared:function(){
                if(!this.stared){
                    this.$emit('input',this.originalArray)
                    return
                }
                
                //配列リセット
                this.filteredArray = []
                
                //フィルター
                this.filteredArray = this.originalArray.filter(el => {
                    return el && el.stared == true
                })
                
                //出力
                this.$emit('input',this.filteredArray)
            },
        }
    }
</script>
<style scoped>
     .filter-button {
        color:white;
        border:1px solid grey;
        background:grey;
        border-radius:0.2em;
        padding:0.1em 0.6em;
        display:inline-flex;
        align-items:center;
        opacity:0.8;
        cursor:pointer;
        transition:all 0.2s ease;
    }
    .filter-button span {
        margin-left:0.2em;
    }
    .filter-button-active {
        border:1px solid orange;
        background:orange;
        opacity:1.0;
    }
    .filter-button:hover {
        border:1px solid orange;
        background:orange;
        opacity:0.9;
    }
</style>
