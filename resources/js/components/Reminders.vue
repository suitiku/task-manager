<template>
    <div id="reminder-wrapper">
        <div v-for="(reminder,index) in reminders">
            <span>{{reminder.task.name}}</span>
            <span>{{reminder.message}}</span>
        </div>
    </div>
</template>
<script>
    import axios from 'axios'
    export default {
        data: function(){
            return {
                reminders: []
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
            this.getReminders()
        },
        watch: {
            userId:function(){
                if(!this.userId)return
                this.getReminders()
            }
        },
        methods: {
            getReminders: async function(){
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
            }
        }
    }
</script>
<style lang="scss" scoped>
    #reminder-wrapper {
        width: 100%;
        padding: 0.8em;
        background-color: rgba(255,165,0,0.4);
        border-radius: 0.2em;
        div {
            span {
                margin:0.3em 1em 0.3em 0em;
                &:first-child {
                    font-weight: bold;
                }
            }
            &:before {
                content: '・';
            }
        }
    }
</style>