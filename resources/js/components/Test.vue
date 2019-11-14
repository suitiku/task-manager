<!--テスト用コンポーネント-->
<template>
    <div class="container">
        <versatile-form table="tasks" v-bind:foreign_keys="foreign_keys" />
    </div>
</template>

<script>
    export default {
        data:function(){
            return {
                range:'',
                project: {},
                override:[{project_id:3}],
                columns: ['name','dead_line'],
                foreign_keys:[
                    {project_id:
                        {
                            table:'projects',
                            columns:['name','dead_line'],
                            comment:'所属するプロジェクトを選択してください。'
                        }
                    }
                ]
            }  
        },
        mounted() {
        },
        created:async function() {
            let result = await axios.get('/api/projects/3')
            this.project = result.data
        },
        methods: {
        },
    }
</script>
<style>
</style>
