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
                    <button class="btn btn-sm btn-danger" @click="delSelected()">删除所选</button>&nbsp;
                    <button class="btn btn-sm btn-success" @click="showAddModel()">新增科室 <i class="fa fa-plus" aria-hidden="true"></i></button>&nbsp;
                </div>
                <br><br>
                <table id="dataTable" class="table table-bordered">
                    <thead>
                    <tr>
                        <th></th>
                        <th class="exportable">ID</th>
                        <th class="exportable">Name</th>
                        <th class="exportable">Created_At</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th></th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Created_At</th>
                        <th>操作</th>
                    </tr>
                    </tfoot>
                    <!--这里需要特别注意！！！，@click事件必须放到tbody上面，如果放到tr上，那么新增加的行的点击事件将不会被触发-->
                    <tbody @click="selectDepartment($event)">
                    <tr v-for="department in departments">
                        <td></td>
                        <td>{{ department.id }}</td>
                        <td>{{ department.name }}</td>
                        <td>{{ department.created_at }}</td>
                        <td><button class="del btn btn-sm btn-danger">删除</button> <button class="edit btn btn-sm btn-success">修改</button></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>


        <department-model v-if="showAddDepartment">
            <h3 slot="header">增加科室</h3>
            <div slot="body">
                <div class="form-group">
                    <label>选择上级科室:</label>
                    <treeselect @open="reloadOptions" v-model="pid" placeholder="选择上级科室,不选默认为顶级科室" :normalizer="normalizer" :options="treeselectLists"></treeselect>
                </div>
                <div class="form-group">
                    <label>设置科室名称:</label>
                    <input v-model="departmentName" type="text" class="form-control">
                </div>
            </div>
            <button @click="saveDepartment" class="btn btn-sm btn-success" slot="footer">保存</button>
            <!--实现点击取消按钮，隐藏模态框-->
            <button class="btn btn-sm btn-default" slot="footer" @click="showAddDepartment=false">取消</button>
        </department-model>

        <department-model v-if="showEditDepartment">
            <h3 slot="header">修改科室信息</h3>
            <div slot="body">
                <div class="form-group">
                    <label>设置科室名称:</label>
                    <input id="editName" v-model="departmentName" type="text" class="form-control">
                </div>
            </div>
            <button @click="saveEdit" class="saveEdit btn btn-sm btn-success" slot="footer">保存</button>
            <!--实现点击取消按钮，隐藏模态框-->
            <button class="btn btn-sm btn-default" slot="footer" @click="showEditDepartment=false">取消</button>
        </department-model>

        <vue-snotify></vue-snotify>
    </div>
</template>

<script>
    import VueDatepickerLocal from 'vue-datepicker-local'
    import DepartmentModel from './DepartmentModel.vue'
    //引入vue-treeselect
    import Treeselect from '@riophae/vue-treeselect'
    //引入vue-treeselect的样式
    import '@riophae/vue-treeselect/dist/vue-treeselect.css'
    import moment from 'moment'
    export default {
        components:{
            VueDatepickerLocal,
            'department-model':DepartmentModel,  //引入DepartmentModel
            'treeselect':Treeselect
        },
        name:'dataTable',
        data() {
            return {
                showAddDepartment:false,
                showEditDepartment:false,
                departments:[],
                searchedData:[],
                search:{
                    name:'',
                    startTime: '',
                    endTime:''
                },
                selectedRow:{},


                treeselectLists:[],
                //增加上级科室id属性
                pid:null,
                //增加新增科室名称属性
                departmentName:'',
                //注意，这里必须要用自定义，不然显示不出来的
                normalizer(node) {
                    return {
                        id: node.id,//指定id是什么字段
                        label: node.name,//指定label是用的什么字段，即显示什么字段出来
                    }
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
                    this.searchedData = this.departments;
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
                    rowReorder: false,
                    columnDefs: [
                        { targets: [0], className: 'details-control',orderable:false},
                        { targets: -1, orderable:false},
                    ],
                    order: [[1, 'asc']]
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
                                columns: ':visible.exportable'
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
                                columns: ':visible.exportable'
                            }
                        },
                        {
                            extend:'pdf',
                            text:'<i class="fa fa-file-pdf-o"></i>',
                            titleAttr:'导出PDF',
                            className:'btn-outline-primary btn-sm',
                            download: 'open',
                            exportOptions: {
                                columns: ':visible.exportable'
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
            selectDepartment(ee) {
                let table = $('#dataTable').DataTable();
                let buttonClass = $(ee.target).get(0).className;
                let tr = $(ee.target.closest('tr'));
                let row = table.row($(ee.target.closest('tr')).get(0));
                let data = row.data();
                this.selectedRow = row;
                if(buttonClass.indexOf('del') !== -1) {
                    console.log('点击了删除按钮');
                    this.$snotify.error('你真的要删除 '+data[2]+' 吗？', '删除确认', {
                        position:'centerCenter',
                        buttons: [
                            {text: 'Yes', action: (toast) => {
                                axios.post('/department/delete',{id:data[1]}).then(res=> {
                                    this.$snotify.success('删除成功');
                                    table.row(row).remove().draw( false )
                                });
                                this.$snotify.remove(toast.id);
                            }, bold: false},
                            {text: 'No', action: (toast) => {this.$snotify.remove(toast.id);}},
                        ]
                    });
                }
                if(buttonClass.indexOf('edit') !== -1) {
                    console.log('点击了编辑按钮');
//                  显示编辑窗口，并获取当前点击行的数据
                    this.departmentName = data[2];
                    this.showEditDepartment = true;
                }

                if(buttonClass.indexOf('details-control') !== -1) {
                    console.log('展开子项目');
                    if ( row.child.isShown() ) {
                        // This row is already open - close it
                        row.child.hide();
                        tr.removeClass('shown');
                    }
                    else {
                        // Open this row
                        row.child( this.format(data) ).show();
                        tr.addClass('shown');
                    }
                }

                $(ee.currentTarget).toggleClass('selected');
//                table.on( 'select', function ( e, dt, type, ix ) {  //监听选择事件
//                    let selected = dt.rows({selected:true});
//                    if ( selected.count() > 5 ) {      //限制选择的个数
//                        dt.rows(ix).deselect();
//                    }
//                });
            },

            format ( d ) {
            // `d` is the original data object for the row
            return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
                        '<tr>'+
                        '<td>姓名:</td>'+
                        '<td>'+d[2]+'</td>'+
                        '</tr>'+
                        '<tr>'+
                        '<td>年龄:</td>'+
                        '<td>'+d[1]+'</td>'+
                        '</tr>'+
                        '<tr>'+
                        '<td>额外信息:</td>'+
                        '<td>And any further details here (images etc)...</td>'+
                        '</tr>'+
                    '</table>';
            },

            saveEdit() {
                let table = $('#dataTable').DataTable();
                let newName = $('input#editName')[0].value;
                let data = this.selectedRow.data();
                data[2] = newName;
                axios.post('/department/edit',{name:data[2],id:data[1]}).then((res)=>{
                    console.log('修改成功');
                    this.showEditDepartment = false;
                    this.$snotify.success('修改成功！');
                    table.row(this.selectedRow).data(data).draw();
                });
            },

            delSelected() {
                let table = $('#dataTable').DataTable();
                let data = table.rows({selected:true}).data();
                let ids = [];
                data.map(d=> {
                    ids.push(parseInt(d[1]))
                });
                axios.post('/department/deleteSelected',{ids:ids}).then(res=> {
                    console.log('删除成功');
                    table.rows({selected:true}).remove().draw( false );
                })
            },

            showAddModel() {
                this.pid = null;
                this.departmentName = '';
                this.showAddDepartment = true
            },
            saveDepartment() {
                axios.post('/department/add',{pid:this.pid,name:this.departmentName}).then((res)=>{
                    console.log(res.data);
                    this.pid = null;
                    this.departmentName ='';
                    let table = $('#dataTable').DataTable();
                    table.row.add(['',res.data.id,res.data.name,res.data.created_at,'<button class="del btn btn-sm btn-danger">删除</button> <button class="edit btn btn-sm btn-success">修改</button>']).draw(true)
                })
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
                        let needTime = moment(data[3]).valueOf();
                        let needName = data[2];
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
                    let needTime = moment(value[3]).valueOf();
                    let needName = value[2];
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
                    let id = filteredData[i][1];
                    let name = filteredData[i][2];
                    let created_at = filteredData[i][3];
                    let single = {id:id,name:name,created_at:created_at};
//                    console.log(single);
                    this.searchedData.push(single)
                }
//                console.log(filteredData);
                console.log(this.searchedData);
            },
            reloadOptions() {
                axios.get('/department/get').then(res=> {
                    console.log(res.data.data);
                    this.treeselectLists = res.data.data;
                }).catch(error=> {
                    throw error
                });
            },
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
    td.details-control {
        background: url('details_open.png') no-repeat center center;
        cursor: pointer;
    }
    tr.shown td.details-control {
        background: url('details_close.png') no-repeat center center;
    }
</style>