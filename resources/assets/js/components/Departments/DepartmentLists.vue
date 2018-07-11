<template>
    <div id="hot-preview">
        <HotTable :settings="settings"></HotTable>
        <pagination :limit="2" :data="laravelData" @pagination-change-page="getResults"></pagination>
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
