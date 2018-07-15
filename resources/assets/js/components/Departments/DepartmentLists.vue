<template>
    <div>
        <v-table is-horizontal-resize style="width:100%" :columns="columns" :table-data="tableData" :filter-method="filterMethod"></v-table>

        <department-model v-if="showEditDepartment">
            <h3 slot="header">高级过滤</h3>
            <div slot="body">
                <div v-for="single in allFilter">
                    <div class="btn-group">
                        <button type="button" class="btn btn-success">选择过滤条件</button>
                        <select class="btn btn-outline-success" v-model="single[0]">
                            <option v-for="column in columns" v-bind:value="column.field">{{ column.title }}</option>
                        </select>
                        <select class="btn btn-outline-success" v-model="single[1]">
                            <option v-for="option in options" v-bind:value="option.value">{{option.text}}</option>
                        </select>
                        <input type="text" class="btn btn-outline-success" placeholder="输入条件" v-model="single[2]">
                    </div>
                    <button @click="delFilter(single)" v-if="allFilter.length > 1" type="button" class="btn btn-danger"><i class="fa fa-minus" aria-hidden="true"></i></button>
                </div>
            </div>

            <button @click="addFilter" type="button" class="btn btn-sm btn-success" slot="footer"><i class="fa fa-plus" aria-hidden="true"></i></button>
            <button @click="chaxun" class="btn btn-sm btn-success" slot="footer">查询</button>
            <!--实现点击取消按钮，隐藏模态框-->
            <button class="btn btn-sm btn-default" slot="footer" @click="closeModel">退出</button>
        </department-model>



        <div class="btn-group">
            <button class="btn btn-success" @click="openModel">高级筛选</button>
            <button class="btn btn-primary" @click="clearFilter">清除筛选</button>
        </div>
    </div>
</template>


<script>
    import DepartmentModel from './DepartmentModel.vue'
    export default{
        components: {
            'department-model':DepartmentModel,
        },
        data(){
            return {
                showEditDepartment:false,
                allFilter:[],
                singleFilter:[],
                // define default value
                selectedColumn: '',
                selectedSymbol:'',
                selectedCondition:'',

                // define options
                options: [
                    {text: '大于', value: '>'},
                    {text: '大于等于', value: '>='},
                    {text: '等于', value: '=' },
                    {text: '小于', value: '<' },
                    {text: '小于等于', value: '<=' },
                    {text: '不等于', value: '<>'},
                    {text: '包含', value: 'like' },
                    {text: '左配', value: 'left' }
                ],






                dumpData: [],
                tableData: [],
                columns: [
                    {field: 'id', title: 'ID', width: 50, titleAlign: 'center',columnAlign:'center',isResize:true,isFrozen:true},
                    {field: 'name', title: '名称', width: 80, titleAlign: 'center',columnAlign:'center',isResize:true,
                        filterMultiple: true,
                        filters: [{
                            label: '外一科',
                            value: '外一科',
                        }, {
                            label: '外二科',
                            value: '外二科',
                        }, {
                            label: '内一科',
                            value: '内一科',
                        }]
                    },
                    {field: 'created_at', title: '创建时间', width: 150, titleAlign: 'center',columnAlign:'center',isResize:true},
                ]
            }
        },
        methods: {

            // 数据筛选
            filterMethod(filters){

                let tableData = this.dumpData;

                // filter name
                if (Array.isArray(filters.name)){

                    tableData = tableData.filter(item => filters.name.indexOf(item.name) > -1);
                }

                this.tableData = tableData;
            },

            getTableData(){
                axios.post('/department/org/get').then(res => {
                    this.dumpData = res.data.data;
                    this.tableData = res.data.data;
                });
            },

            openModel() {
                this.showEditDepartment = true;
                this.allFilter.push(this.singleFilter)
            },
            closeModel() {
                this.showEditDepartment=false;
                this.singleFilter = [];
                this.allFilter = []
            },
            clearFilter() {
                this.allFilter = [];
                this.getTableData();
            },
            chaxun() {
                this.allFilter.forEach(function (singleFilter) {
                    if(singleFilter[1] === 'like') {
                        singleFilter[2] = '%'+singleFilter[2]+'%'
                    }
                    if(singleFilter[1] === 'left') {
                        singleFilter[1] = 'like';
                        singleFilter[2] = singleFilter[2]+'%'
                    }
                });
                axios.post('/department/org/get',{filters:this.allFilter}).then(res => {
                    this.dumpData = res.data.data;
                    this.tableData = res.data.data;
                    this.showEditDepartment = false;
                    this.singleFilter = [];
                    this.allFilter = []
                });
            },
            addFilter() {
                this.singleFilter = [];
                this.allFilter.push(this.singleFilter);
            },
            delFilter(single) {
                this.allFilter.splice(this.allFilter.indexOf(single),1)
            }
        },

        mounted(){
            this.getTableData();
        }
    }
</script>
