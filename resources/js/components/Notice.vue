<!--通知コンポーネント-->
<!--2秒で消える-->
<!--今後の改修ポイント-->
<!--１．デザイン修正-->
<template>
    <div class="notice-wrapper">
        <div class="notice-content">
            <slot></slot>
            <p>{{message}}</p>
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                message:''
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
            showNotice:async function(message){
                this.message = await message
                let notice = document.querySelector('.notice-wrapper')
                notice.className = 'notice-wrapper notice-active'
                notice.addEventListener('animationend',function(){
                    notice.classList.remove('notice-active')
                    this.message = ''
                })
            }
        }
    }
</script>
<style scoped>
    .notice-wrapper {
        display:none;
        position:fixed;
        z-index:50;
        right:10%;
        top:10%;
        padding:0.5em;
        width:8em;
        max-height:4em;
        overflow:hidden;
        border-radius:0.5em;
        background:grey;
    }
    .notice-content {
        width:100%;
        display:flex;
        flex-direction:column;
        align-items:center;
        justify-content:center;
        font-size:75%;
        color:white;
    }
    .notice-active {
        display:block;
        animation:notice 3s;
    }
    @keyframes notice {
        0% {
            opacity:0;
        }
        25% {
            opacity:1.0;
        }
        100% {
            opacity:0;
        }
    }
    
</style>
