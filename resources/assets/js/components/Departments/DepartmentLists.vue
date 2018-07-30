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
                <div class="pull-right btn-group dataTableButtons">
                    <!--<button class="btn btn-sm btn-danger" @click="delt()">删除</button>&nbsp;-->
                    <button class="btn btn-sm btn-success" @click="addDepartment()">新增科室 <i class="fa fa-plus" aria-hidden="true"></i></button>&nbsp;
                </div>
                <br><br>
                <table id="dataTable" class="table table-bordered">
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
                    <tr v-for="department in departments" @click="selectDepartment(department,$event)">
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
                searchedData:[],
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
                    this.searchedData = this.departments
                })
            });
        },
        methods: {
            initDataTable() {
                let table = $('#dataTable').DataTable({
                    language: {
                        "sLengthMenu": "_MENU_",
                        "zeroRecords": "没有找到记录",
                        "info": "第 _PAGE_ 页 / 总 _PAGES_ 页，共 _TOTAL_ 条数据",
                        "infoEmpty": "无记录",
                        "sSearch": "搜索:",
                        "sInfoFiltered": "(从 _MAX_ 条记录中过滤)",
                        "select": {
                            rows:" , %d 条记录被选中"
                        },
                    },
                    fixedHeader:true,
//                    stateSave:true,
                    rowReorder: true,
                    columnDefs: [
                        { targets: [1], visible: false},
                        { targets: '_all', visible: true },
                        { targets: [0], className:'reorder'}
                    ]
                });
                //隐藏第二列
//                table.columns( [1] ).visible( false );
                this.initDataTableButtons(table);
                this.initDataTableSelect(table);
            },
            initDataTableButtons(table) {
//              第一个按钮组
                new $.fn.dataTable.Buttons( table, {
                    name:'first',
                    buttons: [
                        {
                            extend:'colvis',
                            className:'btn-outline-primary btn-sm',
                            text:'<i class="fa fa-eye"></i>'
                        },
                        {
                            extend:'collection',
                            text:'选择',
                            className:'btn-outline-primary btn-sm',
                            buttons: [
                                {
                                    text:'选择当前页',
                                    action: function ( e, dt, node, config ){
                                        dt.rows({page:'current'}).select()
                                    }

                                },
                                {
                                    extend:'selectAll',
                                    text:'全 选'
                                },
                                {
                                    extend:'selectNone',
                                    text:'全不选'
                                },
                            ]
                        },
                        {
                            extend:'copy',
                            text:'<i class="fa fa-files-o" aria-hidden="true"></i>',
                            titleAttr:'复制全部或选中数据',
                            className:'btn-outline-primary btn-sm'
                        },
                        {
                            extend:'print',
                            text:'<i class="fa fa-print"></i>',
                            className:'btn-outline-primary btn-sm',
                            attr:{
                                title:'打印全部或选中数据',
                            },
                            key:{
                                key:'p',
                                ctrlKey:true
                            },
                            //控制不打印隐藏列，即只打印显示列
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                    ]
                });
//              第二个按钮组
                new $.fn.dataTable.Buttons( table, {
                    name:'second',
                    buttons:[
                        {
                            extend:'excel',
                            text:'<i class="fa fa-file-excel-o"></i>',
                            titleAttr:'导出EXCEL',
                            className:'btn-outline-primary btn-sm',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend:'pdf',
                            text:'<i class="fa fa-file-pdf-o"></i>',
                            titleAttr:'导出PDF',
                            className:'btn-outline-primary btn-sm',
                            download: 'open',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                    ]
                });
                //将自动生成的按钮放到指定的位置
                table.buttons( ['first'], null ).containers().appendTo( '.dataTables_length>label' );
                table.buttons( ['second'], null ).containers().appendTo( '.dataTableButtons' );

//              重新设置按钮的显示格式
                $('.dataTables_length>label>select').removeClass().addClass('btn btn-sm btn-primary');
                $('.dataTables_length>label>div').removeClass('btn-group');
            },
            initDataTableSelect(table) {
                table.select.style('os');
                table.select.items('row');
            },
            getDepartments() {
               return axios.get('/department/org/get')
            },
            selectDepartment(department,ee) {
                let table = $('#dataTable').DataTable();
//                console.log(ee.currentTarget);
                $(ee.currentTarget).toggleClass('selected');

                table.on( 'select', function ( e, dt, type, ix ) {  //监听选择事件
                    let selected = dt.rows({selected:true});
//                    if ( selected.count() > 5 ) {      //限制选择的个数
//                        dt.rows(ix).deselect();
//                    }
                });
            },
            delt() {
                let table = $('#dataTable').DataTable();
                table.rows({selected:true}).remove().draw( false );

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
                let filteredData = table.rows().data().filter(function(value,index) {
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
                this.getSearchedData(filteredData);
            },
            getSearchedData(filteredData) {
                let length = filteredData.length;
                this.searchedData = [];
                for (let i=0;i < length;i++) {
                    let id = filteredData[i][0];
                    let name = filteredData[i][1];
                    let created_at = filteredData[i][2];
                    let single = {id:id,name:name,created_at:created_at};
//                    console.log(single);
                    this.searchedData.push(single)
                }
//                console.log(filteredData);
                console.log(this.searchedData);
            }
        }
    }
</script>

<style>
    .card-body{padding: 5px;}
    .card-header{padding: 6px}
    .row .card-body .btn{width: 30%;}
    .card-body>.datepicker>input {height: 27px;width:157px;font-size: 12px;padding-left: 2px}
    tr.selected{background-color: #B0BED9}
    .dt-buttons{display: inline}
</style>