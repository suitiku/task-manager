<!--日付選択統合コンポーネント-->
<!--今後の改修ポイント-->
<!--１．選択肢の追加-->
<!--２．デザイン修正-->
<!--３．リセット機能-->
<template>
    <div class="date-picker-wrapper">
        <div class="toggle">
            <span>入力方式を切り替える</span>
            <toggle-switch v-model="dead_line" />
        </div>
        <date-selecter v-model="result" v-show="!dead_line" />
        <dead-line v-model="result" v-show="dead_line" />
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                dead_line:false,
                result:''
            }  
        },
        props: {
            
        },
        watch:{
            result:function(){
                this.$emit('input',this.result)
            }
        },
        created:function(){
            
        },
        mounted:function(){
            
        },
        methods: {
            
        }
    }
</script>
<style scoped>
    .date-picker-wrapper {
        width:100%;
        display:flex;
        flex-direction:column;
        justify-content:center;
        align-items:center;
        margin:0.5em;
    }
    .toggle {
        display:flex;
        flex-direction:column;
        justify-content:center;
        align-items:center;
        margin:0.5em;
    }
</style>
