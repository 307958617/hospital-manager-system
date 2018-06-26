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
            <form>
                <div class="card">
                    <div class="card-header">新增科室</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="email">选择上级科室:</label>
                            <input type="email" class="form-control" id="email">
                        </div>
                        <div class="form-group">
                            <label for="pwd">设置科室名称:</label>
                            <input type="password" class="form-control" id="pwd">
                        </div>
                        <div class="form-check">
                            <label class="form-check-label">
                                <input class="form-check-input" type="checkbox"> Remember me
                            </label>
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
    //同样需要引入nestable
    import nestable from './nestable'
    export default {
        components: {
            'department-tree': DepartmentTree
        },
        mounted() {
            console.log('Department2.');
            this.getDepartments();
        },
        data() {
            return {
                Departments:[]
            }
        },
        methods: {
            getDepartments() {
                axios.get('/department/get').then(res=> {
                    console.log(res);
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
                axios.post('/department/change',{'tree':r}).then(function (res) {
                    console.log('ok')
                })
            }
        }
    }
</script>
