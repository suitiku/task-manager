<template>
    <div>
        <!--モーダル-->
        <modal ref="reminderModal" v-model="reminderModal">
            <p>下記タスクにリマインダーがセットされています。ご確認下さい。</p>
            <div class="flex flex-center flex-vertical child-inline-block child-margin">
                <button type="button" class="btn btn-primary" v-on:click="setReminderLater()">1日後に再通知</button>
                <button type="button" class="btn btn-danger" v-on:click="deleteReminder()">確認したのでこのリマインダーを削除する</button>
            </div>
            <task v-model="task" />
        </modal>
        
        <!--表示部分-->
        <div id="reminder-wrapper" class="flex flex-vertical">
            <div v-for="(reminder,index) in reminders" v-on:click="showTaskDetail(reminder.id,reminder.task)">
                <span>{{reminder.task.name}}</span>
                <span>{{reminder.message}}</span>
            </div>
            <p v-show="reminders.length == 0">現在設定されているリマインダーはありません。</p>
        </div>
    </div>
</template>
<script>
    import axios from 'axios'
    export default {
        data: function(){
            return {
                reminders: [],
                targetReminderId:'',
                reminderModal:false,
                task:{}
            }  
        },
        props: {
            userId: {
                type: [Number,String],
                default: 0,
                required: true
            }  
        },
        mounted: function(){
            if(!this.userId)return
            this.fetchReminders()
        },
        watch: {
            userId:function(){
                if(!this.userId)return
                this.fetchReminders()
            },
            reminderModal:async function(newVal,oldVal){
                if(newVal == false){
                    this.fetchReminders()
                }
            }
        },
        methods: {
            fetchReminders: async function(){
                try{
                    let result = await axios.get('/api/myreminders', {
                        params: {
                            user_id:this.userId,
                        }
                    })
                    this.reminders = result.data
                }catch(error){
                    console.log(error)
                }
            },
            showTaskDetail:function(reminderId,task){
                this.targetReminderId = reminderId
                this.task = {}
                this.task = JSON.parse(JSON.stringify(task))
                this.$refs.reminderModal.openModal()
            },
            setReminderLater:async function(){
                // 削除しないでalert_datetimeの値をアップデートする
                //1日後のdatetimeを設定
                let newDatetime = new Date(Date.now() + 86400000)
                
                // 投入用オブジェクト構築
                let updateObject = {
                    alert_datetime:newDatetime
                }
                
                // 書き込み
                try{
                    let result = await axios.put('/api/reminders/' + this.targetReminderId, updateObject)
                    this.$refs.reminderModal.closeModal()
                }catch(error){
                    console.log(error)
                }
            },
            deleteReminder:async function(){
                if(!this.targetReminderId)return 
                
                try{
                    await axios.delete('/api/reminders/' + this.targetReminderId)
                }catch(error){
                    console.log(error)
                }
            }
        }
    }
</script>
<style lang="scss" scoped>
    .flex {
        display: flex;
    }
    .flex-vertical {
        flex-direction: column;
    }
    .flex-center {
        align-items: center;
    }
    .child-inline-block {
        & * {
            display: inline-block;
        }
    }
    .child-margin {
        & * {
            margin: 0.5em;
        }
    }
    #reminder-wrapper {
        width: 100%;
        padding: 0.8em;
        background-color: rgba(255,165,0,0.4);
        border-radius: 0.2em;
        div {
            display: inline-block;
            padding: 0.3em;
            cursor: pointer;
            transition: all 0.3s ease;
            span {
                margin:0.3em 1em 0.3em 0em;
                &:first-child {
                    font-weight: bold;
                    font-size: 110%;
                }
            }
            &:before {
                content: '\f12a';
                font-family: "Font Awesome 5 Free","Font Awesome 5 Brands";
                font-weight: 900;
                margin-right: 1.5em;
            }
            &:hover {
                background-color: rgba(191,191,191,0.4);
            }
        }
    }
</style>