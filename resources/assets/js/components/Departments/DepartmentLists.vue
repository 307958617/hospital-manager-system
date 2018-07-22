<template>
    <div class="container">
        <button class="btn btn-danger" @click="delt()">删除</button>
        <button class="btn btn-primary" @click="selectAll()">全选</button>
        <button class="btn btn-outline-dark" @click="unSelect()">不选</button>
        <table id="dataTable" class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Created_At</th>
                </tr>
            </thead>
            <tfoot>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Created_At</th>
            </tr>
            </tfoot>

            <tbody>
                <tr v-for="department in departments" @click="selectDepartment(department,$event)" :class="selectedDepartmentId.indexOf(department.id) !== -1?'selected table-info':''">
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
                departments:[],
                selectedDepartmentId:[]
            }
        },
        mounted() {
            this.$nextTick(function() {
                this.getDepartments().then((response) => {
                    // do what you need to do
                    this.departments = response.data.data
                }).then(() => {
                    // execute the call to render the table, now that you have the data you need
                    this.initDataTable()
                })
            })
        },
        methods: {
            initDataTable() {
                $('#dataTable').DataTable({
                    language: {
                        "lengthMenu": "每页 _MENU_ 条记录",
                        "zeroRecords": "没有找到记录",
                        "info": "第 _PAGE_ 页 ( 总共 _PAGES_ 页 )",
                        "infoEmpty": "无记录",
                        "sSearch": "搜索:",
                    },
                    stateSave:true,
                })
            },
            getDepartments() {
               return axios.get('/department/org/get')
            },
            selectDepartment(department,e) {
                if(e.currentTarget.className.indexOf('selected table-info') !== -1) {
                    this.selectedDepartmentId.splice(this.selectedDepartmentId.indexOf(department.id),1);
                }else {
                    this.selectedDepartmentId.push(department.id) ;
                }
                console.log(this.selectedDepartmentId)

            },
            delt() {
                let table = $('#dataTable').DataTable();
                table.rows('.selected').remove().draw( false );
                this.selectedDepartmentId = []
            },
            selectAll() {
                this.selectedDepartmentId = this.departments.map((department)=>{return department.id});
            },
            unSelect() {
                this.selectedDepartmentId = []
            }
        }
    }
</script>