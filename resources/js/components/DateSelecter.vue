<!--日時選択コンポーネント-->
<!--YYYY-MM-DD hh:mm:ssの形式で返却-->
<!--今後の改修ポイント-->
<!--①一部を入力して補完する-->
<template>
    <div class="date-selecter-root-wrapper">
        <div class="date-selecter-wrapper">
            <input type="text" v-model="datetime" placeholder="例）2001-01-01 12:30" class="date-selecter">
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                datetime:''
            }  
        },
        props: {
            
        },
        watch:{
            datetime:{
                handler:function(){
                    this.createDatetime()
                },
                deep:true
            }
        },
        created:function(){
            
        },
        mounted:function(){
            
        },
        methods: {
            createDatetime:function(){
                let currentDatetime = new Date()
                switch(true){
                    case /\d{4}-\d{1,2}-\d{1,2}\s\d{1,2}:\d{1,2}/.test(this.datetime):
                        let result = this.validateDatetime(this.datetime)
                        this.$emit('input',result)
                        break
                    default:
                        this.$emit('input','')
                }
            },
            validateDatetime:function(){
                let result = this.datetime.match(/(\d{4})-(\d{1,2})-(\d{1,2})\s(\d{1,2}):(\d{1,2})/)
                let year = Number(result[1])
                let month = Number(result[2])
                let day = Number(result[3])
                let hour = Number(result[4])
                let minute = Number(result[5])
                let longDayMonths = [1,3,5,7,8,10,12]
                
                //月
                if(month > 13){
                    month = 12
                }
                //日
                if(month == 2){
                    if(year % 4 != 0){
                        if(day > 28){day = 28}
                    }else if(year % 100 != 0){
                        if(day > 29){day = 29}
                    }else if(year % 400 != 0){
                        if(day > 28){day = 28}
                    }else{
                        if(day > 29){day = 29}
                    }
                }else if(longDayMonths.indexOf(month) == -1){
                    if(day > 30){day = 30}
                }else{
                    if(day > 31){day = 31}
                }
                //時
                if(hour > 23){hour = 23}
                //分
                if(minute > 59){minute = 59}
                
                //結果返却
                return year + '-' + month + '-' + day + ' ' + hour + ':' + minute
            }
        }
    }
</script>
<style scoped>
    .date-selecter-root-wrapper {
        width:100%;
        display:flex;
        flex-wrap:wrap;
        justify-content:center;
        flex-direction:column;
        align-items: center;
        margin:1em;
    }
    .date-selecter-wrapper {
        width:100%;
        min-height:3.0em;
        display:flex;
        justify-content:center;
        flex-wrap:wrap;
        align-items:center;
        margin:0.5em;
        padding:0.5em;
    }
    .date-selecter {
        width:12em;
        text-align:center;
        border:1px solid grey;
        border-radius:0.3em;
        margin:0 0.5em;
    }
    .result-display {
        width:14em;
        height:2.0em;
        margin:0.5em 0;
        border:1px solid grey;
        padding:0.3em;
        border-radius:0.3em;
        text-align:center;
    }
    .date-set {
        display:flex;
        justify-content:center;
        align-items:flex-end;
         margin:0 0.6em;
    }
    span {
        font-size:60%;
    }
    .date-selecter-wrapper span:first-child {
        font-size:100%;
    }
    
</style>
