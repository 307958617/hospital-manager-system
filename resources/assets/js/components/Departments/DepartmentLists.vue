<template>
    <div>
        <div id="hot-preview">
            <HotTable :settings="settings"></HotTable>
            <!--<pagination :limit="2" :data="laravelData" @pagination-change-page="getResults"></pagination>-->
        </div>




        <div>
            <span class="custom-control-inline">
                <select class="custom-select" style="width:60px;text-align:center" v-model="pagesize" v-on:change="showPage(pageCurrent,$event,true)">
                    <option v-for="item in arrPageSize"  value="item">{{item}}</option>
                </select>
            </span>
            <span class="custom-control-inline">
                <ul class="pagination">
                    <li v-for="item in pageCount+1" v-if="item===1" v-on:click="showPage(1,$event)" class="page-item"><a class="page-link" href="#">首页</a></li>
                    <li v-for="item in pageCount+1" v-if="item===1" v-on:click="showPage(pageCurrent-1,$event)" class="page-item"><a class="page-link" href="#">上一页</a></li>
                    <li v-for="item in pageCount+1" v-if="item===1" v-on:click="showPage(item,$event)" class="page-item"><a class="page-link" href="#">{{item}}</a></li>
                    <li v-for="item in pageCount+1" v-if="item===1 && item<showPagesStart-1" class="page-item disabled"><a class="page-link" href="#">...</a></li>
                    <li v-for="item in pageCount+1" v-if="item>1&&item<=pageCount-1&&item>=showPagesStart&&item<=showPageEnd&&item<=pageCount" v-on:click="showPage(item,$event)" class="page-item"><a class="page-link" href="#">{{item}}</a></li>
                    <li v-for="item in pageCount+1" v-if="item===pageCount&&item>showPageEnd+1" class="page-item disabled"><a class="page-link" href="#">...</a></li>
                    <li v-for="item in pageCount+1" v-if="item===pageCount&&item!==1" v-on:click="showPage(item,$event)" class="page-item"><a class="page-link" href="#">{{item}}</a></li>
                    <li v-for="item in pageCount+1" v-if="item===pageCount" v-on:click="showPage(pageCount+1,$event)" class="page-item"><a class="page-link" href="#">下一页</a></li>
                    <li v-for="item in pageCount+1" v-if="item===pageCount" v-on:click="showPage(pageCount,$event)" class="page-item"><a class="page-link" href="#">尾页</a></li>

                </ul>
            </span>
            <span class="custom-control-inline">
                <input class="form-control" style="width:45px;text-align:center" type="text" v-model="pageCurrent" v-on:keyup.enter="showPage(pageCurrent,$event,true)" />
            </span>
            <span>{{pageCurrent}}/{{pageCount}}</span>
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
                //总项目数
                totalCount: 200,
                //分页大小
                pagesize: 3,
                //分页数
                arrPageSize: [10, 20, 30, 40],
                //当前分页数
                pageCount: 20,
                //当前页面
                pageCurrent: 1,
                //显示分页按钮数
                showPages: 11,
                //开始显示的分页按钮
                showPagesStart: 1,
                //结束显示的分页按钮
                showPageEnd: 100,
                //所有数据
                arrayDataAll: [],
                //分页数据
                arrayData: [],















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
                if(pageIndex > 0) {
                    if (pageIndex > this.pageCount) {
                        pageIndex = this.pageCount;
                    }

                    //判断数据是否需要更新
                    const currentPageCount = Math.ceil(this.totalCount / this.pagesize);
                    if (currentPageCount !== this.pageCount) {
                        pageIndex = 1;
                        this.pageCount = currentPageCount;
                    }else if (this.pageCurrent === pageIndex && currentPageCount === this.pageCount && typeof (forceRefresh) === "undefined") {
                        console.log("not refresh");
                        return;
                    }

                    //从所有数据中取分页数据
                    let newPageInfo = [];
                    for (let i = 0; i < this.pagesize; i++) {
                        let index = i + (pageIndex - 1) * this.pagesize;
                        if (index > this.totalCount - 1)break;
                        newPageInfo[newPageInfo.length] = this.arrayDataAll[index];
                    }
                    this.pageCurrent = pageIndex;
                    this.settings.data = newPageInfo;

                    //计算分页按钮数据
                    if (this.pageCount > this.showPages) {
                        if (pageIndex <= (this.showPages - 1) / 2) {
                            this.showPagesStart = 1;
                            this.showPageEnd = this.showPages - 1;
                            console.log("showPage1")
                        }
                        else if (pageIndex >= this.pageCount - (this.showPages - 3) / 2) {
                            this.showPagesStart = this.pageCount - this.showPages + 2;
                            this.showPageEnd = this.pageCount;
                            console.log("showPage2")
                        }
                        else {
                            console.log("showPage3")
                            this.showPagesStart = pageIndex - (this.showPages - 3) / 2;
                            this.showPageEnd = pageIndex + (this.showPages - 3) / 2;
                        }
                    }
                }
                console.log(pageIndex)
            },








            getResults() {
                axios.get('/department/org/get').then(res=> {
                    this.arrayDataAll = res.data.data;
                    this.totalCount = res.data.data.length;
                    const currentPageCount = Math.ceil(this.totalCount / this.pagesize);
                    if (currentPageCount !== this.pageCount) {
                        this.pageCount = currentPageCount;
                    }

                    let newPageInfo = [];
                    for (let i = 0; i < this.pagesize; i++) {
                        let index = i;
                        if (index > this.totalCount - 1)break;
                        newPageInfo[newPageInfo.length] = this.arrayDataAll[index];
                    }
                    this.pageCurrent = 1;
                    this.settings.data = newPageInfo;
                });
            },
        }

    }
</script>

<style src="./handsontable.full.css"></style>
