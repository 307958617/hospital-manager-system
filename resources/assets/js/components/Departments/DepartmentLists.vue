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
                            <input type="text" v-model="search.name" class="form-control" @input="searchAndFilterData">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card text-center">
                    <div class="card-header "><h4>Created_At</h4></div>
                    <div class="card-body">
                        <vue-datepicker-local v-model="search.startTime" @input="searchAndFilterData" clearable format="YYYY-MM-DD HH:mm:ss" />--
                        <vue-datepicker-local v-model="search.endTime" @input="searchAndFilterData" clearable format="YYYY-MM-DD HH:mm:ss" />
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card text-center">
                    <div class="card-header "><h4>Active</h4></div>
                    <div class="card-body">
                        <button class="btn btn-sm btn-outline-success"><i class="fa fa-check" aria-hidden="true"></i></button>
                        <button class="btn btn-sm btn-outline-danger"><i class="fa fa-times" aria-hidden="true"></i></button>
                        <button class="btn btn-sm btn-outline-warning "><i class="fa fa-power-off" aria-hidden="true"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="card dataTable">
            <div class="card-body">
                <div class="text pull-left grey"><h2><i class="fa fa-h-square"></i> 部门管理</h2></div>
                <div class="btn-group pull-right dataTableButtons">
                    <button class="btn btn-danger" @click="delt()">删除</button>
                    <button class="btn btn-primary" @click="selectAll()">全选</button>
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
        },
        methods: {
            initDataTable() {
                let table = $('#dataTable').DataTable({
//                    dom:'Bfrtip',
                    buttons: [
                        {
                            extend: 'collection',
                            text: '更多',
                            buttons: [
                                'copy','excel','print','columnsToggle'
                            ],
                            fade: true
                        },

                    ],
                    language: {
                        "lengthMenu": "每页 _MENU_ 条记录",
                        "zeroRecords": "没有找到记录",
                        "info": "第 _PAGE_ 页 / 总 _PAGES_ 页，共 _TOTAL_ 条数据",
                        "infoEmpty": "无记录",
                        "sSearch": "搜索:",
                        "sInfoFiltered": "(从 _MAX_ 条记录中过滤)",
                    },
                    stateSave:false
                });
                //将自动生成的按钮放到指定的位置
                table.buttons().container().appendTo($('.dataTableButtons'))
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
//                console.log(this.selectedDepartmentId)

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
//            这个方法只适合单独查询某条记录有用，不能多条件查询
//            searchName() {
//                let table = $('#dataTable').DataTable();
//                let name = this.search.name;
//                table.column(1).search(name).draw();
//                let filteredName = table.rows().data().filter(function(value,index) {
//                    if(value[1].indexOf(name) !== -1) {
//                        return true
//                    }
//                });
//                this.getSearchData(filteredName);
//            },

            searchAndFilterData() {
                let table = $('#dataTable').DataTable();
                $.fn.dataTable.ext.search.pop();
//                table.column(2).search(time).draw();
//              下面是查询字段格式化
                let startTime = moment(this.search.startTime).valueOf();
                let endTime = moment(this.search.endTime).valueOf();
                let name = this.search.name;
//              下面时查找方法用于显示到当前表
                $.fn.dataTable.ext.search.push(
                    function (settings,data,dataIndex) {
//                      设置实际需要获得到的字段
                        let needTime = moment(data[2]).valueOf();
                        let needName = data[1];
//                        console.log(data[1] + time);
                        if (
                                ( isNaN( startTime ) && isNaN( endTime ) && (needName.indexOf(name) !== -1) ) ||
                                ( isNaN( startTime ) && needTime <= endTime && (needName.indexOf(name) !== -1) ) ||
                                ( startTime <= needTime   && isNaN( endTime ) && (needName.indexOf(name) !== -1) ) ||
                                ( startTime <= needTime   && needTime <= endTime && (needName.indexOf(name) !== -1) )

                        )
                        {
                            return true;
                        }
                        return false;
                    }
                );
//              用查询后的数据重新绘制表格
                table.draw();
//              用filter获取查询后的结果
                let filteredTime = table.rows().data().filter(function(value,index) {
//                  设置实际需要获得到的字段
                    let needTime = moment(value[2]).valueOf();
                    let needName = value[1];
                    if (
                        ( isNaN( startTime ) && isNaN( endTime ) && (needName.indexOf(name) !== -1) ) ||
                        ( isNaN( startTime ) && needTime <= endTime && (needName.indexOf(name) !== -1) ) ||
                        ( startTime <= needTime   && isNaN( endTime ) && (needName.indexOf(name) !== -1) ) ||
                        ( startTime <= needTime   && needTime <= endTime && (needName.indexOf(name) !== -1) )
                    )
                    {
                        return true;
                    }
                });
                this.getSearchData(filteredTime);
            },
            getSearchData(filteredData) {
                let length = filteredData.length;
                this.searchData = [];
                for (let i=0;i < length;i++) {
                    let id = filteredData[i][0];
                    let name = filteredData[i][1];
                    let created_at = filteredData[i][2];
                    let single = {id:id,name:name,created_at:created_at};
//                    console.log(single);
                    this.searchData.push(single)
                }
//                console.log(filteredData);
                console.log(this.searchData);
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