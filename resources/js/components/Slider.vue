<!--スライダーコンポーネント-->
<template>
    <div class="wrapper">
        <div class="slide-button" v-on:click="slideContentRight()"><i class="fas fa-chevron-left"></i></div>
        <div class="slider-wrapper" ref="sliderWrapper">
            <div class="slider" ref="slider">
                <div ref="slideEl" v-for="(item,index) in items" class="slider-el" v-bind:style="itemWidth(item)" v-on:click="selectOption(item.value)">{{item.label}}</div>
            </div>
        </div>
        <div class="slide-button" v-on:click="slideContentLeft()"><i class="fas fa-chevron-right"></i></div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {

            }  
        },
        props: {
            items:{
                type:[String,Array],
            }
        },
        watch:{
            
        },
        created:function(){
            
        },
        mounted:function(){
            
        },
        computed:{
        },
        methods: {
            slideContentRight:function(){
                if(this.$refs.slider.offsetLeft >= 0)return 
                let currentLeft = this.$refs.slider.offsetLeft
                this.$refs.slider.style.left = String(currentLeft + 105) + 'px'
            },
            slideContentLeft:function(){
                if(this.$refs.slider.scrollWidth + this.$refs.slider.offsetLeft - this.$refs.sliderWrapper.offsetWidth <= 0)return
                let currentLeft = this.$refs.slider.offsetLeft
                this.$refs.slider.style.left = String(currentLeft - 105) + 'px'
            },
            itemWidth:function(item){
                let width = item.label.length + 2
                if(width < 5){width = 5}
                return {minWidth:width + 'em'}
            },
            selectOption:function(value){
                if(event.target.classList.contains('selected')){
                    this.$emit('input','')
                    event.target.classList.remove('selected')
                }else{
                    this.$emit('input',value)
                    for(let el of this.$refs.slideEl){
                        el.classList.remove('selected')
                    }
                    event.target.classList.add('selected')
                }
            }
        }
    }
</script>
<style scoped>
    .wrapper {
        display:flex;
        align-items:center;
        justify-content:center;
        width:100%;
    }
    .slider-wrapper {
        position:relative;
        border:1px solid grey;
        overflow:hidden;
    }
    .slider {
        position:relative;
        display:flex;
        padding:0.3em 1em;
        transition:all 0.5s ease;
    }
    .slider-el {
        display:flex;
        justify-content:center;
        align-items:center;
        height:1.8em;
        padding:0.2em;
        text-align:center;
        border:2px solid grey;
        border-radius:0.3em;
        margin:0.5em 1em;
        cursor:pointer;
        transition:all 0.3s;
    }
    .selected {
        color:white;
        background:grey;
        transition:all 0.3s;
    }
    .slide-button {
        margin:0 0.5em;
        cursor:pointer;
    }
</style>
