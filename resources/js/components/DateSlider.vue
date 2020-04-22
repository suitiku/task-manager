<!--単一ファイルコンポーネントのテンプレートファイル-->
<template>
    <div class="date-slider-wrapper">
        <!--日-->
        <slider v-model="date" v-bind:items="dateOptions" />
        
        <!--時-->
        <slider ref="timeSlider" v-model="date" v-bind:items="timeOptions" />
        
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                date:'',
                time:'',
                dateOptions:[],
                timeOptions:[],
            }  
        },
        props: {
            
        },
        watch:{
            
        },
        created:function(){
            
        },
        mounted:async function(){
            this.setDateOptions()
            await this.setTimeOptions()
            this.setSliderPosition()
        },
        methods: {
            setDateOptions:function(){
                let dateTimes = []
                let currentDateTime = new Date()
                dateTimes.push(currentDateTime)
                for(let i = 0; i<7 ;i++){
                    dateTimes.push(new Date(dateTimes[i].getTime() + 86400000))
                    if(i == 0){
                        let date = dateTimes[0].getFullYear() + '-' + (dateTimes[0].getMonth() + 1) + '-' + dateTimes[0].getDate()
                        this.dateOptions.push({label:'本日',value:date})
                    }else{
                        let label = (dateTimes[i].getMonth() + 1) + '月' + dateTimes[i].getDate() + '日'
                        let date = dateTimes[i].getFullYear() + '-' + (dateTimes[i].getMonth() + 1) + '-' + dateTimes[i].getDate()
                        this.dateOptions.push({label:label,value:date})
                    }
                }
            },
            setTimeOptions:function(){
                let value
                for(let i=0;i < 24;i++){
                    value = i + ':00'
                    this.timeOptions.push({label:value,value:value})
                    
                    value = i + ':30'
                    this.timeOptions.push({label:value,value:value})
                }
                
            },
            setSliderPosition:function(){
                this.$refs.timeSlider.scrollSlider(3)
            }
        }
    }
</script>
<style scoped>
    .date-slider-wrapper {
        width:50%;
    }
</style>
