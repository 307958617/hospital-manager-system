<template>
    <div class="dd" id="nestable">
        <ol class="dd-list">
            <department-tree v-for="Department in Departments" :key="Department.id" :Department="Department" :data-name="Department.name" :data-id="Department.id"></department-tree>
        </ol>
    </div>
</template>

<script>
    import DepartmentTree from './DepartmentTree.vue'
    export default {
        components: {
            'department-tree': DepartmentTree
        },
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
