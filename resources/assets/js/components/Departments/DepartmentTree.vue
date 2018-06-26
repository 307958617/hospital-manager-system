<template>
    <li class="dd-item">
        <!--需要取消原来这个位置添加的两个按钮-->
        <div class="dd-handle ">{{ Department.name }}</div>
        <!--必须给这里的ol标签添加判断，不然多了这个ol标签，折叠按钮会出现显示不正常的情况-->
        <ol class="dd-list" v-if="Department.children.length > 0">
            <department-tree v-for="Department in Department.children" :key="Department.id" :Department="Department" :data-name="Department.name" :data-id="Department.id"></department-tree>
        </ol>
    </li>
</template>

<script>
    //引入nestable.js
    import nestable from './nestable'
    export default {
        props: ['Department'],
        data() {
            return {

            }
        },
        mounted() {
            console.log('Component mounted.');
            //将初始化前移到此处
            $('.dd').nestable();
            //注意，这里需要申明一开始列表的状态时全部展开状态。
            this.expandAll();
        },
        methods: {
            //增加一个扩展所有列表的方法
            expandAll() {
                $('.dd').nestable('expandAll');
            },
        }
    }
</script>
