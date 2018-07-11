<template>
    <div>
        <div id="hot-preview">
            <HotTable :settings="settings"></HotTable>
            <pagination :limit="2" :data="laravelData" @pagination-change-page="getResults"></pagination>
        </div>




        <div>
            <span class="custom-control-inline">
                <select class="custom-select" v-model="pagesize" v-on:change="showPage(pageCurrent,$event,true)">
                    <option v-for="item in arrPageSize" value="item">{{item}}</option>
                </select>
            </span>
            <span class="custom-control-inline">
                <ul class="pagination">
                    <li v-for="item in pageCount+1" v-if="item===1" v-on:click="showPage(1,$event)" class="page-item"><a class="page-link" href="#">首页</a></li>
                    <li v-for="item in pageCount+1" v-if="item===1" v-on:click="showPage(pageCurrent-1,$event)" class="page-item"><a class="page-link" href="#">上一页</a></li>
                    <li v-for="item in pageCount+1" v-if="item===1" v-on:click="showPage(item,$event)" class="page-item"><a class="page-link" href="#">{{item}}</a></li>
                    <li v-for="item in pageCount+1" v-if="item===1&&item<showPagesStart-1" class="page-item disabled"><a class="page-link" href="#">...</a></li>
                </ul>
            </span>

        </div>
    </div>
</template>

<script>
    import HotTable from '@handsontable-pro/vue';
    import 'handsontable-pro/languages/zh-CN';

    export default {
        components: {
            HotTable
        },
        mounted() {
            this.getResults();
        },
        data() {
            return {

                //分页大小
                pagesize: 10,
                //分页数
                arrPageSize: [10, 20, 30, 40],
                //当前分页数
                pageCount: 20,
                //当前页面
                pageCurrent: 1,
                //开始显示的分页按钮
                showPagesStart: 1,















                // Our data object that holds the Laravel paginator data
                laravelData: {},
                settings: {
                    data: [],
                    colHeaders: ["ID","Name","Created_At"],
                    columns: [
                        {data:'id',type:'numeric'},
                        {data:'name',type:'text'},
                        {
                            data:'created_at',
                            type: 'date',
                            dateFormat: 'YYYY-MM-DD HH:mm:ss',
                            correctFormat: true
                        }
                    ],
                    datePickerConfig: {
                        i18n: {
                            previousMonth: 'Previous Month',
                            nextMonth: 'Next Month',
                            months: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
                            weekdays: ['日', '一', '二', '三', '四', '五', '六'],
                            weekdaysShort: ['日', '一', '二', '三', '四', '五', '六']
                        }
                    },
                    colWidths: [60, 120, 200],
                    columnSorting : true,
                    manualColumnResize: true,
                    sortIndicator: true,
                    stretchH: 'all',
                    dropdownMenu: ['filter_by_condition', 'filter_by_value', 'filter_action_bar'],
                    filters: true,
                    rowHeaders: true,
                    language:'zh-CN'
                }
            };
        },
        methods: {
            showPage(pageIndex, $event, forceRefresh) {
                console.log(pageIndex)
            },








            getResults(page = 1) {
                axios.get('/department/org/get?page='+ page).then(res=> {
                    this.laravelData = res.data.data;
                    this.settings.data = res.data.data.data;
                });
            },
        }

    }
</script>

<style src="./handsontable.full.css"></style>
