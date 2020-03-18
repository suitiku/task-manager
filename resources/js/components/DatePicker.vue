<!--日付選択統合コンポーネント-->
<!--今後の改修ポイント-->
<!--１．選択肢の追加-->
<!--３．リセット機能-->
<template>
    <div class="date-picker-wrapper">
        <div class="result-display">{{japaneseDatetime}}</div>
        <div class="select-option-wrapper">
            <tag-cloud v-model="selectedOption" v-bind:options="options" />
        </div>
        <div class="component-wrapper">
            <date-selecter v-model="result" v-show="selectedOption == 'dateSelecter'" />
            <dead-line v-model="result" v-show="selectedOption == 'deadLine'" />
        </div>
        
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                options: [
                    {label:'現在時刻',value:'current'},
                    {label:'シメキリ',value:'deadLine'},
                    {label:'数字入力',value:'dateSelecter'},
                ],
                selectedOption:'current',
                deadLine:false,
                result:'',
                japaneseDatetime:''
            }  
        },
        props: {
            value:{
                type:String,
                required:false
            }
        },
        watch:{
            result:function(){
                if(this.result){
                    let date = new Date(this.result)
                    this.japaneseDatetime = date.getFullYear() + '年' + (date.getMonth() + 1) + '月' + date.getDate() + '日 ' + date.getHours() + '時' + date.getMinutes() + '分'
                    this.$emit('input',this.result)
                }
            },
            selectedOption:function(){
                if(this.selectedOption == 'current'){
                    this.setCurrentDatetime()
                }
            },
        },
        created:function(){
            
        },
        mounted:function(){
            // 初期値設定
            if(this.value){
                this.selectedOption = ''
                let date = new Date(this.value)
                this.japaneseDatetime = date.getFullYear() + '年' + (date.getMonth() + 1) + '月' + date.getDate() + '日 ' + date.getHours() + '時' + date.getMinutes() + '分'
            }else{
                this.setCurrentDatetime()
            }
        },
        methods: {
            setCurrentDatetime:function(){
                let currentDatetime = new Date()
                let year = currentDatetime.getFullYear()
                let month = currentDatetime.getMonth() + 1
                let day = currentDatetime.getDate()
                let hour = currentDatetime.getHours()
                let minute = currentDatetime.getMinutes()
                let result = year + '-' + month + '-' + day +' ' + hour + ':' + minute + ':00'
                this.japaneseDatetime = year + '年' + month + '月' + day + '日 ' + hour + '時' + minute + '分'
                this.$emit('input',result)
            }
        }
    }
</script>
<style scoped>
    .date-picker-wrapper {
        position:relative;
        border:2px solid grey;
        background:whitesmoke;
        border-radius:0.3em;
        width:100%;
        display:flex;
        flex-direction:column;
        justify-content:center;
        align-items:center;
        margin:0.5em;
        padding:0.5em;
    }
    .select-option-wrapper {
        max-width:22em;
    }
    .toggle span {
        margin-right:1em;
    }
    .component-wrapper {
        max-width:35em;
        min-height:7em;
        margin-top:1em;
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
