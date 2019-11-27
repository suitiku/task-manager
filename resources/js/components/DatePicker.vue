<!--日付選択統合コンポーネント-->
<!--今後の改修ポイント-->
<!--１．選択肢の追加-->
<!--２．デザイン修正-->
<!--３．リセット機能-->
<!--４．現在時刻セット機能-->
<!--５．デザインが崩れないようにある程度の高さを確保しておく-->
<template>
    <div class="date-picker-wrapper">
        <div class="toggle">
            <span>入力方式を切り替える</span>
            <toggle-switch v-model="dead_line" />
        </div>
        <div class="component-wrapper">
            <date-selecter v-model="result" v-show="!dead_line" />
            <dead-line v-model="result" v-show="dead_line" />
        </div>
        <div class="result-display">{{japaneseDatetime}}</div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                dead_line:false,
                result:'',
                japaneseDatetime:''
            }  
        },
        props: {
            
        },
        watch:{
            result:function(){
                if(this.result){
                    let date = new Date(this.result)
                    this.japaneseDatetime = date.getFullYear() + '年' + (date.getMonth() + 1) + '月' + date.getDate() + '日 ' + date.getHours() + '時' + date.getMinutes() + '分'
                    this.$emit('input',this.result)
                }
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
    .component-wrapper {
        width:100%;
        min-height:7em;
    }
    .result-display {
        width:14em;
        min-height:2.0em;
        margin:0.5em 0;
        border:1px solid grey;
        padding:0.3em;
        border-radius:0.3em;
        text-align:center;
    }
</style>
