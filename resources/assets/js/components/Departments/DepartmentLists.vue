<template>
    <div class="container">
        <table id="dataTable" class="table table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Created_At</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="department in departments">
                <td>{{ department.id }}</td>
                <td>{{ department.name }}</td>
                <td>{{ department.created_at }}</td>
            </tr>
            </tbody>
        </table>
    </div>

</template>

<script>
    export default {
        name:'dataTable',
        data() {
            return {
                departments:[]
            }
        },
        mounted() {
            this.$nextTick(function() {
                this.getDepartments().then((response) => {
                    // do what you need to do
                    this.departments = response.data.data
                }).then(() => {
                    // execute the call to render the table, now that you have the data you need
                    $('#dataTable').DataTable({
                        "language": {
                            "lengthMenu": "每页 _MENU_ 条记录",
                            "zeroRecords": "没有找到记录",
                            "info": "第 _PAGE_ 页 ( 总共 _PAGES_ 页 )",
                            "infoEmpty": "无记录",
                            "infoFiltered": "(从 _MAX_ 条记录过滤)",
                            "sSearch": "搜索:",
                        }
                    })
                })
            })
        },
        methods: {
            getDepartments() {
               return axios.get('/department/org/get')
            }
        }
    }
</script>