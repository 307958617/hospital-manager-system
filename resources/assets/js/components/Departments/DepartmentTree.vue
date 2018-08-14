<template>
    <!--注意，这里添加一个dd-nodrag从能让下面添加的按钮生效-->
    <li class="dd-item dd-nodrag">
        <!--需要取消原来这个位置添加的两个按钮-->
        <div class="dd-handle">

            {{ Department.name }}
        <!--增加编辑和删除按钮，引入font-awesome-->
            <span class="pull-right">
                <i @click="showAddUserToDepartment=true" class="fa fa-user-plus" aria-hidden="true"></i>&nbsp;
                <i @click="showEditDepartment=true" class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;
                <i @click="delDepartment" class="fa fa-trash-o" aria-hidden="true"></i>
            </span>
        </div>
        <!--必须给这里的ol标签添加判断，不然多了这个ol标签，折叠按钮会出现显示不正常的情况-->
        <ol class="dd-list" v-if="Department.children.length > 0">
            <!--这里需要添加:Departments="Departments"进来从能让vue-treeselect起作用-->
            <department-tree v-for="Department in Department.children" @getDepartments="getDepartments" :key="Department.id" :Department="Department" :data-name="Department.name" :data-id="Department.id" :data-pid="Department.parent_id"></department-tree>
        </ol>


        <department-model v-if="showEditDepartment">
            <h3 slot="header">编辑科室</h3>
            <div slot="body">
                <div class="form-group">
                    <label>设置科室名称:</label>
                    <input v-model="departmentName" type="text" class="form-control">
                </div>
            </div>
            <button @click="editDepartment" class="btn btn-sm btn-success" slot="footer">保存</button>
            <!--实现点击取消按钮，隐藏模态框-->
            <button class="btn btn-sm btn-default" slot="footer" @click="showEditDepartment=false">取消</button>
        </department-model>

        <department-model v-if="showAddUserToDepartment">
            <h3 slot="header">添加人员到{{ departmentName }}</h3>
            <div slot="body">
                <div class="form-group">
                    <label>设置科室名称:</label>
                    <input v-model="departmentName" type="text" class="form-control">
                </div>
            </div>
            <button @click="editDepartment" class="btn btn-sm btn-success" slot="footer">保存</button>
            <!--实现点击取消按钮，隐藏模态框-->
            <button class="btn btn-sm btn-default" slot="footer" @click="showAddUserToDepartment=false">取消</button>
        </department-model>
    </li>
</template>

<script>
    //引入nestable.js
    import nestable from './nestable'
    //引入DepartmentModel
    import DepartmentModel from '../Model.vue'
    export default {
        props: ['Department'],
        components:{
            'department-model':DepartmentModel,  //引入DepartmentModel
        },
        data() {
            return {
                pid:null,
                //判断编辑科室的模态框是否显示，默认不显示
                showEditDepartment:false,
                showAddUserToDepartment:false,
                //增加新增科室名称属性
                departmentName:'',
                //注意，这里必须要用自定义，不然显示不出来的
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
                axios.post('/dep_user/departments/edit',{pid:this.pid,name:this.departmentName,id:this.Department.id}).then((res)=>{
                    console.log('修改成功');
                    this.showEditDepartment = false;
                    //调用父组件的方法，实现添加新分类后马上显示出来，但是不要忘记到父组件里面添加这个方法@getDepartments="getDepartments"
                    //<department-tree v-for="Department in Department.children" @getDepartments="getDepartments" :key="Department.id" :Departments="Departments" :Department="Department" :data-name="Department.name" :data-id="Department.id"></department-tree>
                    this.getDepartments()
                })
            },
            //增加删除部门的方法
            delDepartment() {
                axios.post('/dep_user/departments/delete',{id:this.Department.id}).then((res)=>{
                    console.log('删除成功');
                    const li = $('li[data-id='+this.pid+']');
                    this.delClass(li);
                    this.getDepartments();
                })
            },
            //必须增加这个方法与父组件的名称一样这很重要，本组件递归调用才不会报错
            getDepartments() {
                this.$emit('getDepartments')
            },
            delClass: function(li)
            {
                if(li.children('ol').children('li').length === 1) {
                    li.children('[data-action="collapse"]').remove();
                }
            }
        }
    }
</script>
