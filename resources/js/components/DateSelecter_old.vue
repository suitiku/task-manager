<!--日時選択コンポーネント-->
<!--YYYY-MM-DD hh:mm:ssの形式で返却-->
<template>
    <div class="date-selecter-root-wrapper">
        <div class="date-selecter-wrapper">
            <div class="date-set">
                <span>20</span>
                <input v-model="datetime.year" type="text" placeholder="年" class="date-selecter" >
                <span>年</span>
            </div>
            <div class="date-set">
                <input v-model="datetime.month" type="text" placeholder="月" class="date-selecter" >
                <span>月</span>
            </div>
            <div class="date-set">
                <input v-model="datetime.day" type="text" placeholder="日" class="date-selecter" >
                <span>日</span>
            </div>
            <div class="date-set">
                <input v-model="datetime.hour" type="text" placeholder="時" class="date-selecter" >
                <span>時</span>
            </div>
            <div class="date-set">
                <input v-model="datetime.minute" type="text" placeholder="分" class="date-selecter" >
                <span>分</span>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                datetime:{
                    year:'',
                    month:'',
                    day:'',
                    hour:'',
                    minute:''
                },
                result:{
                    year:'',
                    month:'',
                    day:'',
                    hour:'',
                    minute:''
                },
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
                for(let key of Object.keys(this.datetime)){
                    switch(key){
                        case 'year':
                            this.result[key] = this.datetime[key] == '' ? currentDatetime.getFullYear() : '20' + this.datetime[key]
                            break
                        case 'month':
                            this.result[key] = this.datetime[key] == '' ? currentDatetime.getMonth() : this.datetime[key] - 1
                            break
                        case 'day':
                            this.result[key] = this.datetime[key] == '' ? currentDatetime.getDate() : this.datetime[key]
                            break
                        case 'hour':
                            this.result[key] = this.datetime[key] == '' ? '1' : this.datetime[key]
                            break
                        case 'minute':
                            this.result[key] = this.datetime[key] == '' ? '1' : this.datetime[key]
                    }
                }
                let convertedDateTime = new Date(this.result.year,this.result.month,this.result.day,this.result.hour,this.result.minute,'00')
                let year = convertedDateTime.getFullYear()
                let month = convertedDateTime.getMonth() + 1
                let day = convertedDateTime.getDate()
                let hour = convertedDateTime.getHours()
                let minute = convertedDateTime.getMinutes()
                let second = convertedDateTime.getSeconds()
                let resultDatetime = year + '-' + month + '-' + day + ' ' + hour +':' + minute + ':' + second
                this.$emit('input',resultDatetime)
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
        width:3em;
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
