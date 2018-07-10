<template>
    <div id="hot-preview">
        <HotTable :settings="settings"></HotTable>
    </div>
</template>

<script>
    import HotTable from '@handsontable-pro/vue';

    export default {
        components: {
            HotTable
        },
        mounted() {
            console.log('sss');
            this.getData()
        },
        data() {
            return {
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
                    colWidths: [60, 120, 200],
                    columnSorting : true,
                    manualColumnResize: true,
                    sortIndicator: true,
                    stretchH: 'all',
                    dropdownMenu: ['filter_by_condition', 'filter_by_value', 'filter_action_bar'],
                    filters: true,
                    rowHeaders: true
                }
            };
        },
        methods: {
            getData() {
                axios.get('/department/org/get').then(res=> {
                    this.settings.data = res.data.data;
                    console.log(res.data.data)
                })
            }
        }

    }
</script>

<style src="./handsontable.full.css"></style>
