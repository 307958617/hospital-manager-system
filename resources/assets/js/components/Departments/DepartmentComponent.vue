<template>
    <div class="row">
        <!--显示科室列表-->
        <div class="col-sm-7">
            <div class="card">
                <div class="card-header">
                    科室列表
                    <div class="btn-group btn-group-sm pull-right">
                    <!--增加相应的按钮及方法-->
                        <button type="button" class="btn btn-success" @click="expandAll">展开所有</button>
                        <button type="button" class="btn btn-success" @click="collapseAll">折叠所有</button>&nbsp;
                        <button type="button" class="btn btn-primary" @click="saveChange">保存修改</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="dd" id="nestable">
                        <ol class="dd-list">
                            <department-tree v-for="Department in Departments" :key="Department.id" :Department="Department" :data-name="Department.name" :data-id="Department.id"></department-tree>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!--增加科室表单-->
        <div class="col-sm-5">
            <form @submit.prevent="addDepartment">
                <div class="card">
                    <div class="card-header">新增科室</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>选择上级科室:</label>
                            <treeselect v-model="pid" placeholder="选择上级科室,不选默认为顶级科室" :normalizer="normalizer" :options="Departments"></treeselect>
                        </div>
                        <div class="form-group">
                            <label>设置科室名称:</label>
                            <input v-model="departmentName" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">增加科室</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    import DepartmentTree from './DepartmentTree.vue'
    //引入vue-treeselect
    import Treeselect from '@riophae/vue-treeselect'
    //引入vue-treeselect的样式
    import '@riophae/vue-treeselect/dist/vue-treeselect.css'
    //同样需要引入nestable
    import nestable from './nestable'
    export default {
        components: {
            'department-tree': DepartmentTree,
            //申明引用组件
            'treeselect':Treeselect
        },
        mounted() {
            this.getDepartments();
            console.log('ss');
        },
        data() {
            return {
                Departments:[],
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
        methods: {
            getDepartments() {
                axios.get('/department/get').then(res=> {
                    console.log(res.data.data);
                    this.Departments = res.data.data;
                }).catch(error=> {
                    throw error
                })
            },
            //增加展开的方法
            expandAll() {
                $('.dd').nestable('expandAll');
            },
            //增加收缩的方法
            collapseAll() {
                $('.dd').nestable('collapseAll');
            },
            //保存修改后的树结构到数据库
            saveChange() {
                const r = $('.dd').nestable('serialize');
                axios.post('/department/change',{'tree':r}).then((res)=>{
                    console.log('调整科室布局成功')
                })
            },
            //保存添加的科室到数据库
            addDepartment() {
                axios.post('/department/add',{pid:this.pid,name:this.departmentName}).then((res)=>{
                    console.log(res.data.data);
                    this.getDepartments();
                })
            }
        }
    }
</script>
