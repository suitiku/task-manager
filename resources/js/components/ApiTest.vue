<!--単一ファイルコンポーネントのテンプレートファイル-->
<template>
    <div>
        {{isLogin}}
        <p>ログインフォーム</p>
        <p><input type="email" v-model="email"></p>
        <p><input type="password" v-model="password"></p>
        <button type="button" v-on:click="login">ログイン</button>
        <button type="button" v-on:click="getUserInfo">ユーザー情報を取得</button>
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                isLogin:false,
                email:'',
                password:'',
                userInfo:{}
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
            login:function(){
                axios.get('/sanctum/csrf-cookie')
                    .then(response => {

                        const url = '/api/login';
                        const params = {
                            email: this.email,
                            password: this.password
                        };
                        axios.post(url, params)
                            .then(response => {
                                console.log(response)
                                this.loggedIn = response.data;

                            })
                            .catch(error => {

                                alert('ログインに失敗しました。');

                            });

                    });
            },
            getUserInfo:async function(){
                let result = await axios.get('/api/tasks')
                console.log(result.data)
            }
        }
    }
</script>
<style scoped>
    
</style>
