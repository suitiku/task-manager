<!--日付選択統合コンポーネント-->
<!--今後の改修ポイント-->
<!--１．選択肢の追加-->
<!--３．リセット機能-->
<template>
    <div class="date-picker-wrapper">
        <div ref="resultDisplay" class="result-display">{{japaneseDatetime}}</div>
        <div class="select-option-wrapper">
            <tag-cloud v-model="selectedOption" v-bind:options="options" />
        </div>
        <div class="component-wrapper">
            <date-input v-model="result" v-show="selectedOption == 'dateInput'" />
            <dead-line v-model="result" v-show="selectedOption == 'deadLine'" />
            <date-slider ref="dateSlider" v-model="result" v-show="selectedOption == 'dateSlider'" />
        </div>
        
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                options: [
                    {label:'スライダー',value:'dateSlider'},
                    {label:'現在時刻',value:'current'},
                    {label:'シメキリ',value:'deadLine'},
                    {label:'数字入力',value:'dateInput'},
                ],
                selectedOption:'dateSlider',
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
                    this.changeNotification()
                }
            },
            selectedOption:function(){
                if(this.selectedOption == 'current'){
                    this.setCurrentDatetime()
                    this.$refs.dateSlider.setSliderPosition()
                }
            },
        },
        created:function(){
        },
        mounted:function(){
            this.init()
        },
        methods: {
            init(datetime){
                //引数を指定して強制的に時間指定する
                if(datetime){
                    this.$emit('input',datetime)
                    this.selectedOption = ''
                    let date = new Date(datetime)
                    this.japaneseDatetime = date.getFullYear() + '年' + (date.getMonth() + 1) + '月' + date.getDate() + '日 ' + date.getHours() + '時' + date.getMinutes() + '分'
                    return
                }
                
                // 初期値設定
                if(this.value){
                    this.selectedOption = ''
                    let date = new Date(this.value)
                    this.japaneseDatetime = date.getFullYear() + '年' + (date.getMonth() + 1) + '月' + date.getDate() + '日 ' + date.getHours() + '時' + date.getMinutes() + '分'
                }else{
                    this.setCurrentDatetime()
                }
            },
            setCurrentDatetime:function(){
                if(this.result){this.changeNotification()}
                let currentDatetime = new Date()
                let year = currentDatetime.getFullYear()
                let month = currentDatetime.getMonth() + 1
                let day = currentDatetime.getDate()
                let hour = currentDatetime.getHours()
                let minute = currentDatetime.getMinutes()
                let result = year + '-' + month + '-' + day +' ' + hour + ':' + minute + ':00'
                this.japaneseDatetime = year + '年' + month + '月' + day + '日 ' + hour + '時' + minute + '分'
                this.$emit('input',result)
            },
            //変更を視覚的に示すメソッド
            changeNotification:function(){
                let resultDisplay = this.$refs.resultDisplay
                resultDisplay.classList.add('change-result')
                resultDisplay.addEventListener('animationend',function(){
                    resultDisplay.classList.remove('change-result')
                })
            }
        }
    }
</script>
<style scoped>
    .date-picker-wrapper {
        position:relative;
        border:1px solid grey;
        /*background:whitesmoke;*/
        border-radius:0.3em;
        width:100%;
        display:flex;
        flex-direction:column;
        justify-content:center;
        align-items:center;
        margin:1.0em;
        padding:1.5em;
    }
    .select-option-wrapper {
    }
    .toggle span {
        margin-right:1em;
    }
    .component-wrapper {
        display:flex;
        max-width:100%;
        min-height:7em;
        margin-top:1em;
    }
    .result-display {
        width:14em;
        min-height:2.0em;
        margin:0.5em 0;
        border:3px solid grey;
        padding:0.3em;
        border-radius:0.3em;
        text-align:center;
    }
    .change-result {
        animation:change 0.5s;
    }
    @keyframes change {
        0% {
            border:3px solid grey;
            background:inherit;
            opacity:1.0;
        }
        25% {
            border:3px solid orange;
            background:orange;
            opacity:0.5;
        }
        100% {
            border:3px solid grey;
            background:inherit;
            opacity:1.0;
        }
    }
</style>
