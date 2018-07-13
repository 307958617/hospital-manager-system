<template>
    <div>
        <v-table is-horizontal-resize style="width:100%" :columns="columns" :table-data="tableData" :filter-method="filterMethod"></v-table>

        <div class="btn-group">
            <button type="button" class="btn btn-default">选择过滤条件</button>
            <select class="btn" v-model="selectedColumn">
                <option v-for="column in columns" v-bind:value="column.field">{{ column.title }}</option>
            </select>
            <select class="btn" id="symbol" name="symbol">
                <option>大于</option>
                <option>等于</option>
                <option>小于</option>
                <option>包含</option>
                <option>不包含</option>
            </select>
            <input type="text" class="btn" placeholder="输入条件" name="condition">
            <button type="button" class="btn btn-primary" @click="queren">确认</button>
        </div>
    </div>
</template>


<script>
    // import the component
    import Treeselect from '@riophae/vue-treeselect'
    // import the styles

    export default{
        components: { Treeselect },
        data(){
            return {
                arrFilter:[],
                // define default value
                selectedColumn: '请选择列表名',
                // define options
                options: [
                    {text: '大于', value: '>'},
                    {text: '等于', value: '=' },
                    {text: '小于', value: '<' },
                    {text: '不等于', value: '<>'},
                    {text: '包含', value: 'like' }
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

                axios.get('/department/org/get').then(res => {
                    this.dumpData = res.data.data;
                    this.tableData = res.data.data;
                });
            },

            queren() {
                console.log(this.selectedColumn)
            }
        },

        mounted(){
            this.getTableData();
        }
    }
</script>