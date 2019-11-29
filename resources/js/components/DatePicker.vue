<!--日付選択統合コンポーネント-->
<!--今後の改修ポイント-->
<!--１．選択肢の追加-->
<!--３．リセット機能-->
<template>
    <div class="date-picker-wrapper">
        <div class="select-option-wrapper">
            <tag-cloud v-model="selected_option" v-bind:options="select_options" />
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
                select_options: [
                    {label:'現在時刻',value:'current'},
                    {label:'シメキリ',value:'dead_line'},
                    {label:'数字入力',value:'date_selecter'},
                ],
                selected_option:'date_selecter',
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
            },
            selected_option:function(){
                switch(this.selected_option[0]){
                    case 'current':
                        this.setCurrentDatetime()
                        break
                    case 'dead_line':
                        this.dead_line = true
                        break
                    case 'date_selecter':
                        this.dead_line = false
                        break
                }
            }
        },
        created:function(){
            
        },
        mounted:function(){
            
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
        border:3px solid grey;
        background:whitesmoke;
        border-radius:0.5em;
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
