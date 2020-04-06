<!--トグルスイッチコンポーネント-->
<!--今後の改修ポイント-->
<!--１．デザイン修正-->
<template>
    <div class="toggle-switch-wrapper">
        <div class="click-area" v-on:click="toggleSwitch()"></div>
        <div v-bind:class="toggleSwitchClass">
            <div class="toggle-switch-child"></div>
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                toggleSwitchClass:'toggle-switch'
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
                if(this.toggleSwitchClass == 'toggle-switch'){
                    this.$emit('input',true)
                    this.toggleSwitchClass = 'toggle-switch toggle-switch-active'
                }else{
                    this.$emit('input',false)
                    this.toggleSwitchClass = 'toggle-switch'
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
        border:1px solid grey;
        overflow:hidden;
        transition:all 0.3s;
    }
    .toggle-switch-child {
        position:relative;
        width:1em;
        height:calc(1em - 2px);
        background:grey;
        transition:all 0.3s;
    }
    .toggle-switch-active {
        border:1px solid orange;
    }
    .toggle-switch-active .toggle-switch-child {
        margin-left:1em;
        width:calc(1em - 2px);
        height:calc(1em - 2px);
        background:orange;
    }
    
    
</style>
