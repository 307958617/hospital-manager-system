<template>
    <div>
        <!--注意，这里添加一个dd-nodrag从能让下面添加的按钮生效-->
        <li class="dd-item dd-nodrag">
            <!--需要取消原来这个位置添加的两个按钮-->
            <div class="dd-handle">

                {{ Department.name }}
            <!--增加编辑和删除按钮，引入font-awesome-->
                <span class="pull-right">
                    <i @click="showEditDepartment=true" class="fa fa-pencil-square-o" aria-hidden="true"></i>
                    <i @click="delDepartment" class="fa fa-trash-o" aria-hidden="true"></i>
                </span>
            </div>
            <!--必须给这里的ol标签添加判断，不然多了这个ol标签，折叠按钮会出现显示不正常的情况-->
            <ol class="dd-list" v-if="Department.children.length > 0">
                <!--这里需要添加:Departments="Departments"进来从能让vue-treeselect起作用-->
                <department-tree v-for="Department in Department.children" @getDepartments="getDepartments" :key="Department.id" :Departments="Departments" :Department="Department" :data-name="Department.name" :data-id="Department.id"></department-tree>
            </ol>
        </li>

        <department-model v-if="showEditDepartment">
            <h3 slot="header">编辑科室</h3>
            <div slot="body">
                <div class="form-group">
                    <label>选择上级科室:</label>
                    <treeselect v-model="pid" placeholder="不选默认为顶级科室" :normalizer="normalizer" :options="Departments"></treeselect>
                </div>
                <div class="form-group">
                    <label>设置科室名称:</label>
                    <input v-model="departmentName" type="text" class="form-control">
                </div>
            </div>
            <button @click="editDepartment" class="btn btn-sm btn-success" slot="footer">保存</button>
            <!--实现点击取消按钮，隐藏模态框-->
            <button class="btn btn-sm btn-default" slot="footer" @click="showEditDepartment=false">取消</button>
        </department-model>
    </div>
</template>

<script>
    //引入nestable.js
    import nestable from './nestable'
    //引入DepartmentModel
    import DepartmentModel from './DepartmentModel.vue'
    //引入vue-treeselect
    import Treeselect from '@riophae/vue-treeselect'
    //引入vue-treeselect的样式
    import '@riophae/vue-treeselect/dist/vue-treeselect.css'
    export default {
        props: ['Department','Departments'],
        components:{
            'department-model':DepartmentModel,  //引入DepartmentModel
            //申明引用组件
            'treeselect':Treeselect
        },
        data() {
            return {
                //判断编辑科室的模态框是否显示，默认不显示
                showEditDepartment:false,
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
            console.log('Component mounted.');
            //将初始化前移到此处
            $('.dd').nestable();
            //注意，这里需要申明一开始列表的状态时全部展开状态。
            this.expandAll();
            this.pid = this.Department.parent_id;
            this.departmentName = this.Department.name;
        },
        methods: {
            //增加一个扩展所有列表的方法
            expandAll() {
                $('.dd').nestable('expandAll');
            },
            //增加编辑部门的方法
            editDepartment() {
                axios.post('/department/edit',{pid:this.pid,name:this.departmentName,id:this.Department.id}).then((res)=>{
                    console.log('修改成功');
                    //调用父组件的方法，实现添加新分类后马上显示出来，但是不要忘记到父组件里面添加这个方法@getDepartments="getDepartments"
                    //<department-tree v-for="Department in Department.children" @getDepartments="getDepartments" :key="Department.id" :Departments="Departments" :Department="Department" :data-name="Department.name" :data-id="Department.id"></department-tree>
                    this.getDepartments()
                })
            },
            //增加删除部门的方法
            delDepartment() {
                axios.post('/department/delete',{id:this.Department.id}).then((res)=>{
                    console.log('删除成功');
                    this.getDepartments()
                })
            },
            //必须增加这个方法与父组件的名称一样这很重要，本组件递归调用才不会报错
            getDepartments() {
                this.$emit('getDepartments')
            }
        }
    }
</script>
