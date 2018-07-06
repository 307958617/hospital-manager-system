<template>
    <v-table
            is-horizontal-resize
            column-width-drag
            style="width:100%"
            :columns="columns"
            :table-data="tableData"
            row-hover-color="#eee"
            row-click-color="#edf7ff"
            @on-custom-comp="customCompFunc"
    >
    </v-table>
</template>


<script>

    export default{
        data() {
            return {
                tableData: [],
                columns: [
                    {field: 'id', title: 'ID', width: 80, titleAlign: 'center', columnAlign: 'center',isResize:true},
                    {field: 'name', title: '部门名称', width: 200, titleAlign: 'center', columnAlign: 'center',isResize:true},
                    {field: 'custome-adv', title: '操作',width: 200, titleAlign: 'center',columnAlign:'center',componentName:'table-operation',isResize:true}
                ]
            }
        },
        mounted() {
            this.get_tableData()
        },
        methods: {
            get_tableData() {
                axios.get('/department/org/get').then(res=> {
                    this.tableData = res.data.data
                })
            },
            customCompFunc(params){

                console.log(params);

                if (params.type === 'delete'){ // do delete operation
                    console.log(params.rowData['id']);
//                    this.$delete(this.tableData,params.index);

                }else if (params.type === 'edit'){ // do edit operation

                    alert(`行号：${params.index} 姓名：${params.rowData['id']}`)
                }
            }
        }
    }
    // 自定义列组件
    Vue.component('table-operation',{
        template:`<span>
        <a href="" @click.stop.prevent="update(rowData,index)">编辑</a>&nbsp;
        <a href="" @click.stop.prevent="deleteRow(rowData,index)">删除</a>
        </span>`,
        props:{
            rowData:{
                type:Object
            },
            field:{
                type:String
            },
            index:{
                type:Number
            }
        },
        methods:{
            update(){
                // 参数根据业务场景随意构造
                let params = {type:'edit',index:this.index,rowData:this.rowData};
                this.$emit('on-custom-comp',params);
            },

            deleteRow(){
                // 参数根据业务场景随意构造
                let params = {type:'delete',index:this.index,rowData:this.rowData};
                this.$emit('on-custom-comp',params);
            }
        }
    })
</script>