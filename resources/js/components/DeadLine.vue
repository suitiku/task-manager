<!--将来の日付を返すUIコンポーネント-->
<!--選択式と記述式を切替-->
<!--今後の改修ポイント-->
<!--１．設定値の表示-->
<!--２．設定値を切りのいい数字にする関数-->
<!--３．セレクトボックスのデザイン-->
<!--４．記述式を追加-->
<template>
    <div class="dead-line-wrapper">
        <select v-model="spanOption" v-on:change="selectSpanOption">
            <option v-bind:value="0" selected>超短期スパン（1日まで）</option>
            <option v-bind:value="1">短期スパン（1週間まで）</option>
            <option v-bind:value="2">中期スパン（1ヶ月まで）</option>
            <option v-bind:value="3">長期スパン（1年まで）</option>
        </select>
        <tag-cloud v-model="selectedOption" v-bind:options="options" ref="tagCloud" />
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                options:[
                    {label:'1時間後',value:'0000-00-00 01:00:00'},
                    {label:'6時間後',value:'0000-00-00 06:00:00'},
                    {label:'12時間後',value:'0000-00-00 12:00:00'},
                    {label:'24時間後',value:'0000-00-00 24:00:00'},
                ],
                selectedOption:'',
                spanOption:''
            }  
        },
        props: {
            
        },
        watch:{
            selectedOption:function(){
                //リセット
                if(this.selectedOption == ''){
                    this.$emit('input','')
                    return 
                }
                let baseDateTime = new Date()
                let text = this.selectedOption[0]
                let regex = /(\d{4})-(\d{2})-(\d{2})\s{1}(\d{2}):(\d{2}):(\d{2})/
                let regexResult = text.match(regex)
                let year = Number(regexResult[1]) + baseDateTime.getFullYear()
                let month = Number(regexResult[2]) + baseDateTime.getMonth()
                let day = Number(regexResult[3]) + baseDateTime.getDate()
                let hour = Number(regexResult[4]) + baseDateTime.getHours()
                let minute = Number(regexResult[5]) + baseDateTime.getMinutes()
                let second = Number(regexResult[6]) + baseDateTime.getSeconds()
                let resultDateTime = new Date(year,month,day,hour,minute,second)
                let resultText = resultDateTime.getFullYear() + '-' + (resultDateTime.getMonth() + 1) + '-' + resultDateTime.getDate() + ' ' + resultDateTime.getHours() + ':' + resultDateTime.getMinutes() + ':' + resultDateTime.getSeconds()
                this.$emit('input',resultText)
            }
        },
        created:function(){
            
        },
        mounted:function(){
        },
        methods: {
            selectSpanOption:function(){
                this.$refs.tagCloud.resetForm()
                switch(this.spanOption){
                    case 0:
                        this.options= [
                            {label:'1時間後',value:'0000-00-00 01:00:00'},
                            {label:'6時間後',value:'0000-00-00 06:00:00'},
                            {label:'12時間後',value:'0000-00-00 12:00:00'},
                            {label:'24時間後',value:'0000-00-00 24:00:00'},
                        ]
                        break
                    case 1:
                        this.options= [
                            {label:'1日後',value:'0000-00-01 00:00:00'},
                            {label:'3日後',value:'0000-00-03 00:00:00'},
                            {label:'5日後',value:'0000-00-05 00:00:00'},
                            {label:'7日後',value:'0000-00-07 00:00:00'},
                        ]
                        break
                    case 2:
                        this.options= [
                            {label:'1週間後',value:'0000-00-07 00:00:00'},
                            {label:'2週間後',value:'0000-00-14 00:00:00'},
                            {label:'3週間後',value:'0000-00-21 00:00:00'},
                            {label:'4週間後',value:'0000-01-00 00:00:00'},
                        ]
                        break
                    case 3:
                        this.options= [
                            {label:'1ヶ月後',value:'0000-01-00 00:00:00'},
                            {label:'3ヶ月後',value:'0000-03-00 00:00:00'},
                            {label:'6ヶ月後',value:'0000-06-00 00:00:00'},
                            {label:'9ヶ月後',value:'0000-09-00 00:00:00'},
                            {label:'12ヶ月後',value:'0001-00-00 00:00:00'},
                        ]
                        break
                    
                }
            }
        }
    }
</script>
<style scoped>
    .dead-line-wrapper {
        display:flex;
        flex-direction:column;
        justify-content:center;
        align-items:center;
    }
    select {
        text-align:center;
    }
</style>
