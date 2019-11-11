<!--テスト用コンポーネント-->
<template>
    <div class="container">
        <task v-bind:task="task" />
        <!--<project-list />-->
        <!--{{test}}-->
        <!--<check-box v-model="test"/>-->
        <!--<modal ref="modal">-->
        <!--    <versatile-form table="tasks" />-->
        <!--</modal>-->
        <!--<button v-on:click="openModal">modal</button>-->
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                task: {
                     id: 1,
                    project_id: 1,
                    name: "メール処理",
                    overview: " ",
                    priority: 1,
                    difficulty: 1,
                    dead_line: "2022-05-01 19:00:00",
                    created_at: "2019-11-11 13:13:19",
                    updated_at: "2019-11-11 13:13:19"
                }
            }  
        },
        mounted() {
        },
        created() {
        },
        methods: {
            openModal:function(){
                this.$refs.modal.openModal()
            }
        },
    }
</script>
<style>
</style>
