<!--配列をソート-->
<template>
    <div class="container">
        
    </div>
</template>

<script>
    export default {
        data:function(){
            return {

            }  
        },
        props: {
            targetArray:{
                type:[Array,String]
            },
            columnName:{
                type:String,
                default:'',
                required:false
            },
            ascending: {
                type:Boolean,
                defalut:true,
                required:false
            }
        },
        watch:{
            
        },
        created:function(){
            
        },
        mounted:function(){
            
        },
        methods: {
            sortArray:function(){
                let sortedArray =JSON.parse(JSON.stringify(this.targetArray))
                let vue = this
                sortedArray.sort(function(a,b) {
                    if(a[vue.columnName] < b[vue.columnName]) return -1
                    if(a[vue.columnName] > b[vue.columnName]) return 1
                    return 0
                })
                if(!this.ascending){sortedArray.reverse()}
                this.$emit('input',sortedArray)
            }
        }
    }
</script>
<style scoped>
    
</style>
