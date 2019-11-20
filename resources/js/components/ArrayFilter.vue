<!--フィルターコンポーネント-->
<!--v-modelに設定されたオブジェクト配列から指定のものを送出する-->
<!--汎用性は捨ててタスクとプロジェクトのフィルターを行う-->
<template>
    <div class="container">
        {{state}}
        <tag-cloud v-model="state" v-bind:options="state_options" multiple/>
        <input v-model="priority" type="range" min="0" max="5" >
        <input v-model="difficulty" type="range" min="0" max="5" >
        {{unfilteredObject}}
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                state:[1,2,3,4,5,6,7],
                priority:0,
                difficulty:0,
                state_options: [
                    {label:'作成',value:1},    
                    {label:'実行中',value:2},    
                    {label:'対応待ち',value:3},  
                    {label:'完了',value:4},    
                    {label:'タスク移動',value:5},    
                    {label:'未完了',value:6},    
                    {label:'編集',value:7},
                ]
            }  
        },
        props: {
            unfilteredObject: {
                type:Array,
                required:true
            },
        },
        watch:{
            state:function(){
                this.filter('state')  
            },
            priority:function(){
                this.filter('priority')
            },
            difficulty:function(){
                this.filter('difficulty')
            }
        },
        created:function(){
            
        },
        mounted:function(){
            
        },
        methods: {
            filter:function(key){
                let result
                switch(key){
                    case 'state':
                        result = this.unfilteredObject.filter((obj) => {
                            let index = obj.states_tasks.length - 1
                            return (this.state.indexOf(obj.states_tasks[index].id) != -1)
                        })
                        this.$emit('input',result)
                        break
                    case 'priority':
                    case 'difficulty':
                        result = this.unfilteredObject.filter((obj) => {
                                return (obj[key] <= this[key])
                            })
                        this.$emit('input',result)
                        break
                }
            }
        }
    }
</script>
<style scoped>
    
</style>
