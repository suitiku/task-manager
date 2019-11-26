<!--トグルスイッチコンポーネント-->
<template>
    <div class="toggle-switch-wrapper">
        <div class="click-area" v-on:click="toggleSwitch()"></div>
        <div v-bind:class="toggle_switch_class">
            <div class="toggle-switch-child"></div>
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                result:false,
                toggle_switch_class:'toggle-switch'
            }  
        },
        props: {
            
        },
        watch:{
            
        },
        created:function(){
            
        },
        mounted:function(){
            
        },
        methods: {
            toggleSwitch:function(){
                console.log(event.target.className)
                if(this.toggle_switch_class == 'toggle-switch'){
                    this.toggle_switch_class = 'toggle-switch toggle-switch-active'
                }else{
                    this.toggle_switch_class = 'toggle-switch'
                }
            }
        }
    }
</script>
<style scoped>
    .click-area {
        position:absolute;
        z-index:2;
        width:2em;
        height:1em;
    }
    .toggle-switch {
        position:relative;
        width:2em;
        height:1em;
        border:1px solid black;
        overflow:hidden;
        transition:all 0.3s;
    }
    .toggle-switch-child {
        position:relative;
        width:1em;
        height:calc(1em - 2px);
        background:black;
        transition:all 0.3s;
    }
    .toggle-switch-active {
        border:1px solid red;
    }
    .toggle-switch-active .toggle-switch-child {
        margin-left:1em;
        width:calc(1em - 2px);
        height:calc(1em - 2px);
        background:red;
    }
    
    
</style>
