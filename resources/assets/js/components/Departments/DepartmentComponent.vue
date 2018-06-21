<template>
    <div class="dd">
        <ol class="dd-list">
            <li v-for="Department in Departments" class="dd-item" :data-name="Department.name" :data-id="Department.id">
                <div class="dd-handle ">{{ Department.name }}</div>
            </li>
        </ol>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Department2.');
            this.getDepartments()
        },
        data() {
            return {
                Departments:[]
            }
        },
        methods: {
            getDepartments() {
                axios.get('/department/get').then(res=> {
                    console.log(res);
                    this.Departments = res.data.data;
                }).catch(error=> {
                    throw error
                })
            }
        }
    }
</script>
