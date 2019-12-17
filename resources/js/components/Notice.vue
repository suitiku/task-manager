<!--通知コンポーネント-->
<!--2秒で消える-->
<!--今後の改修ポイント-->
<!--１．デザイン修正-->
<template>
    <div class="notice-wrapper" ref="noticeWrapper">
        <div class="notice-content">
            <div>
                <i v-if="icon == 'success'" class="far fa-check-circle success"></i>
                <i v-else-if="icon =='error'" class="fas fa-exclamation-circle error"></i>
                <span>{{message}}</span>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                message:'',
                icon:''
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
            showNotice:async function(message,icon){
                this.message = message || ''
                this.icon = icon || ''
                let notice = this.$refs.noticeWrapper
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
    span {
        margin-left:0.5em;
    }
    i {
        font-size:150%;
        color:grey;
    }
    .notice-wrapper {
        display:none;
        position:fixed;
        z-index:50;
        right:5%;
        top:5%;
        padding:0.5em;
        /*width:10em;*/
        height:3em;
        overflow:hidden;
        /*border-radius:0.5em;*/
        background:orange;
    }
    .notice-content {
        width:100%;
        height:100%;
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
