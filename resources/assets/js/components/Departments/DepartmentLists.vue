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
                            <input type="text" v-model="search.name" class="form-control" @input="searchName">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card text-center">
                    <div class="card-header "><h4>Created_At</h4></div>
                    <div class="card-body">
                        <vue-datepicker-local v-model="search.startTime" @input="searchCreatedTime" clearable format="YYYY-MM-DD HH:mm:ss" />--
                        <vue-datepicker-local v-model="search.endTime" @input="searchCreatedTime" clearable format="YYYY-MM-DD HH:mm:ss" />
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
    import moment from 'moment'
    export default {
        components:{
            VueDatepickerLocal
        },
        name:'dataTable',
        data() {
            return {
                departments:[],
                selectedDepartmentId:[],
                searchData:[],
                search:{
                    name:'',
                    startTime: '',
                    endTime:''
                },

              }
        },
        mounted() {
            this.$nextTick(function() {
                this.getDepartments().then((response) => {
                    // do what you need to do
                    this.departments = response.data.data
                }).then(() => {
                    // execute the call to render the table, now that you have the data you need
                    this.initDataTable();
                    this.searchData = this.departments
                })
            });

            $.fn.dataTable.ext.search.push(
                function (settings,data,dataIndex) {
                    let startTime = parseInt(moment(this.search.startTime).valueOf(),10);
                    let endTime = parseInt(moment(this.search.endTime).valueOf(),10);
                    let time = parseFloat(moment(data[2]).valueOf()) || 0;
                    console.log(data[1] + time);
                    if ( ( isNaN( startTime ) && isNaN( endTime ) ) ||
                        ( isNaN( startTime ) && time <= endTime ) ||
                        ( startTime <= time   && isNaN( endTime ) ) ||
                        ( startTime <= time   && time <= endTime ) )
                    {
                        return true;
                    }
                    return false;
                }
            );
        },
        methods: {
            initDataTable() {
                $('#dataTable').DataTable({
                    language: {
                        "lengthMenu": "每页 _MENU_ 条记录",
                        "zeroRecords": "没有找到记录",
                        "info": "第 _PAGE_ 页 / 总 _PAGES_ 页，共 _TOTAL_ 条数据",
                        "infoEmpty": "无记录",
                        "sSearch": "搜索:",
                    },
                    stateSave:false,
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
                this.selectedDepartmentId = this.searchData.map((department)=>{return parseInt(department.id)});
                console.log(this.selectedDepartmentId)
            },
            unSelect() {
                this.selectedDepartmentId = []
            },
            searchName() {
                this.searchData = [];
                let table = $('#dataTable').DataTable();
                let name = this.search.name;
                table.column(1).search(name).draw();
                let filteredData = table.rows().data().filter(function(value,index) {
                    if(value[1].indexOf(name) !== -1) {
                        return true
                    }
                });
                this.getSearchData(filteredData)
            },

            searchCreatedTime() {
                let table = $('#dataTable').DataTable();
                this.searchData = [];
//                table.column(2).search(time).draw();

                table.draw();

                let filteredData = table.rows().data().filter(function(value,index) {
                    if(moment(value[2]).valueOf() >= startTime) {
                        return true
                    }
                });
                this.getSearchData(filteredData)
            },
            getSearchData(filteredData) {
                let table = $('#dataTable').DataTable();
                let length = filteredData.length;
                for (let i=0;i < length;i++) {
                    let id = table.rows().data()[i][0];
                    let name = table.rows().data()[i][1];
                    let created_at = table.rows().data()[i][2];
                    let single = {id:id,name:name,created_at:created_at};
                    this.searchData.push(single)
                }
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