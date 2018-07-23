<template>
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="card text-center">
                    <div class="card-header "><h4>Active</h4></div>
                    <div class="card-body">
                        <button class="btn btn-sm btn-outline-success"><i class="fa fa-check" aria-hidden="true"></i></button>&nbsp;
                        <button class="btn btn-sm btn-outline-danger"><i class="fa fa-times" aria-hidden="true"></i></button>&nbsp;
                        <button class="btn btn-sm btn-outline-warning "><i class="fa fa-power-off" aria-hidden="true"></i></button>&nbsp;
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="card text-center">
                    <div class="card-header "><h4>Name</h4></div>
                    <div class="card-body">
                        <div class="input-group-sm">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card text-center">
                    <div class="card-header "><h4>Created_At</h4></div>
                    <div class="card-body">
                        <vue-datepicker-local v-model="startTime" format="YYYY-MM-DD HH:mm:ss" />--
                        <vue-datepicker-local v-model="endTime" format="YYYY-MM-DD HH:mm:ss" />
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card text-center">
                    <div class="card-header "><h4>Active</h4></div>
                    <div class="card-body">
                        <button class="btn btn-sm btn-outline-success"><i class="fa fa-check" aria-hidden="true"></i></button>&nbsp;
                        <button class="btn btn-sm btn-outline-danger"><i class="fa fa-times" aria-hidden="true"></i></button>&nbsp;
                        <button class="btn btn-sm btn-outline-warning "><i class="fa fa-power-off" aria-hidden="true"></i></button>&nbsp;
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="card dataTable">
            <div class="card-body">
                <div class="text pull-left grey"><h2><i class="fa fa-h-square"></i> 部门管理</h2></div>
                <div class="btn-group pull-right">
                    <button class="btn btn-danger" @click="delt()">删除</button>&nbsp;
                    <button class="btn btn-primary" @click="selectAll()">全选</button>&nbsp;
                    <button class="btn btn-outline-dark" @click="unSelect()">不选</button>
                </div>
                <br><br>
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
        </div>
    </div>
</template>

<script>
    import VueDatepickerLocal from 'vue-datepicker-local'
    export default {
        components:{
            VueDatepickerLocal
        },
        name:'dataTable',
        data() {
            return {
                departments:[],
                selectedDepartmentId:[],
                startTime: new Date(),
                endTime:new Date()
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

<style>
    .card-body{padding: 5px;}
    .card-header{padding: 6px}
    .row .card-body .btn{width: 30%;}
    .card-body>.datepicker>input {height: 27px;width:157px;font-size: 12px;padding-left: 2px}
</style>