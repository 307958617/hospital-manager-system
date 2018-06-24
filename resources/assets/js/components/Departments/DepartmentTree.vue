<template>
    <li class="dd-item">
        <button v-if="Department.children.length > 0" data-action="collapse" type="button">Collapse</button>
        <button data-action="expand" type="button" style="display: none;">Expand</button>
        <div class="dd-handle ">{{ Department.name }}</div>
        <!--必须给这里的ol标签添加判断，不然多了这个ol标签，折叠按钮会出现显示不正常的情况-->
        <ol class="dd-list" v-if="Department.children.length > 0">
            <department-tree v-for="Department in Department.children" :key="Department.id" :Department="Department" :data-name="Department.name" :data-id="Department.id"></department-tree>
        </ol>
    </li>
</template>

<script>
    export default {
        props: ['Department'],
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
