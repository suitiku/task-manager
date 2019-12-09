<!--単一ファイルコンポーネントのテンプレートファイル-->
<template>
    <div class="navi-bar-wrapper">
        <div class="button-wrapper">
            <custom-button v-model="options.home"><i class="fas fa-home"></i></custom-button>
        </div>
        <div class="button-wrapper">
            <custom-button v-model="options.task"><i class="fas fa-tasks"></i></custom-button>
        </div>
        <div class="button-wrapper">
            <custom-button v-model="options.project"><i class="fas fa-project-diagram"></i></custom-button>
        </div>
    </div>
</template>

<script>
    import bus from '../eventBus.js'
    export default {
        data:function(){
            return {
                options:{
                    home:false,
                    task:false,
                    project:false
                }
            }  
        },
        props: {
            
        },
        watch:{
            options:{
                handler:function(){
                    // console.log(bus)
                    bus.$emit('bus-event',this.options)
                    // console.log(this.options)
                },
                deep:true,
            }
        },
        created:function(){
            
        },
        mounted:function(){
            
        },
        methods: {
            
        }
    }
</script>
<style scoped>
    .navi-bar-wrapper {
        width:100%;
        display:flex;
        justify-content:flex-start;
    }
    .button-wrapper {
        margin:0 0.3em;
    }
</style>
