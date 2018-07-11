<template>
    <div id="test" class="form-group">
        <div class="form-group">
            <div class="page-header">
                数据
            </div>
            <table class="table table-bordered table-responsive table-striped">
                <tr>
                    <th>ID</th>
                    <th>姓名</th>
                </tr>
                <tr v-for="dataItem in arrayData">
                    <td >{{dataItem.id}}</td>
                    <td >{{dataItem.name}}</td>
                </tr>
            </table>
            <div class="page-header">分页</div>
            <div class="pager" id="pager">
                    <span class="form-inline">
                        <select class="form-control" v-model="pagesize" v-on:change="showPage(pageCurrent,$event,true)">
                            <option v-for="item in arrPageSize" value="item">{{item}}</option>
                        </select>
                    </span>
                <div v-for="item in pageCount+1">
                        <span v-if="item==1" class="btn btn-default" v-on:click="showPage(1,$event)">
                            首页
                        </span>
                    <span v-if="item==1" class="btn btn-default" v-on:click="showPage(pageCurrent-1,$event)">
                            上一页
                        </span>
                    <span v-if="item==1" class="btn btn-default" v-on:click="showPage(item,$event)">
                            {{item}}
                        </span>
                    <span v-if="item==1&&item<showPagesStart-1" class="btn btn-default disabled">
                            ...
                        </span>
                    <span v-if="item>1&&item<=pageCount-1&&item>=showPagesStart&&item<=showPageEnd&&item<=pageCount" class="btn btn-default" v-on:click="showPage(item,$event)">
                            {{item}}
                        </span>
                    <span v-if="item==pageCount&&item>showPageEnd+1" class="btn btn-default disabled">
                            ...
                        </span>
                    <span v-if="item==pageCount&&item!=1" class="btn btn-default" v-on:click="showPage(item,$event)">
                            {{item}}
                        </span>
                    <span v-if="item==pageCount" class="btn btn-default" v-on:click="showPage(pageCurrent+1,$event)">
                            下一页
                        </span>
                    <span v-if="item==pageCount" class="btn btn-default" v-on:click="showPage(pageCount,$event)">
                            尾页
                        </span>
                </div>
                <span class="form-inline">
                        <input class="pageIndex form-control" style="width:60px;text-align:center" type="text" v-model="pageCurrent" v-on:keyup.enter="showPage(pageCurrent,$event,true)" />
                    </span>
                <span>{{pageCurrent}}/{{pageCount}}</span>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            this.getData()
        },
        data() {
            return {
                //总项目数
                totalCount: 200,
                //分页数
                arrPageSize: [10, 20, 30, 40],
                //当前分页数
                pageCount: 20,
                //当前页面
                pageCurrent: 1,
                //分页大小
                pagesize: 10,
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
                //排序字段
                sortparam: "",
                //排序方式
                sorttype: 1,
            }
        },
        methods: {
            getData() {
                axios.get('/department/org/get').then(res => {
                    this.arrayDataAll = res.data.data;
                })
            },

            //分页方法
            showPage: function (pageIndex, $event, forceRefresh) {

                if (pageIndex > 0) {


                    if (pageIndex > this.pageCount) {
                        pageIndex = this.pageCount;
                    }

                    //判断数据是否需要更新
                    var currentPageCount = Math.ceil(this.totalCount / this.pagesize);
                    if (currentPageCount != this.pageCount) {
                        pageIndex = 1;
                        this.pageCount = currentPageCount;
                    }
                    else if (this.pageCurrent == pageIndex && currentPageCount == this.pageCount && typeof (forceRefresh) == "undefined") {
                        console.log("not refresh");
                        return;
                    }

                    //处理分页点中样式
//                    var buttons = $("#pager").find("span");
//                    for (var i = 0; i < buttons.length; i++) {
//                        if (buttons.eq(i).html() != pageIndex) {
//                            buttons.eq(i).removeClass("active");
//                        }
//                        else {
//                            buttons.eq(i).addClass("active");
//                        }
//                    }

                    //从所有数据中取分页数据
                    var newPageInfo = [];
                    for (var i = 0; i < this.pagesize; i++) {
                        var index = i + (pageIndex - 1) * this.pagesize;
                        if (index > this.totalCount - 1)break;
                        newPageInfo[newPageInfo.length] = this.arrayDataAll[index];
                    }
                    this.pageCurrent = pageIndex;
                    this.arrayData = newPageInfo;

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
            }
        }
    }
</script>
