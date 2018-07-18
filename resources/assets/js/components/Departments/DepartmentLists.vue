<template>
    <div class="ui container">
        <div class="vuetable-pagination ui basic segment grid">
            <vuetable-pagination-info ref="paginationInfoTop" :infoTemplate="infoTemplate">

            </vuetable-pagination-info>
            <vuetable-pagination ref="paginationTop"
                                 @vuetable-pagination:change-page="onChangePage"
            >
            </vuetable-pagination>
        </div>
        <vuetable ref="vuetable"
                  api-url="https://vuetable.ratiw.net/api/users"
                  :fields="fields"
                  :multi-sort="true"
                  pagination-path=""
                  :per-page="20"
                  @vuetable:pagination-data="onPaginationData"
        ></vuetable>
        <div class="vuetable-pagination ui basic segment grid">
            <vuetable-pagination-info ref="paginationInfo" :infoTemplate="infoTemplate">

            </vuetable-pagination-info>
            <vuetable-pagination ref="pagination"
                                 @vuetable-pagination:change-page="onChangePage"
            >
            </vuetable-pagination>
        </div>
    </div>
</template>

<script>
    import Vuetable from 'vuetable-2/src/components/Vuetable.vue'
    import VuetablePagination from 'vuetable-2/src/components/VuetablePagination.vue'
    import VuetablePaginationInfo from 'vuetable-2/src/components/VuetablePaginationInfo.vue'
    import accounting from 'accounting'
    import moment from 'moment'
    export default {
        components: {
            Vuetable,
            VuetablePagination,
            VuetablePaginationInfo
        },
        data() {
            return {
                fields: [
                    {
                        name: '__checkbox',   // <----
                        titleClass: 'center aligned',
                        dataClass: 'center aligned'
                    },
                    {
                        name: '__handle',   // <----
                        dataClass: 'center aligned'
                    },
                    {name: '__sequence',title: '',titleClass: 'center aligned',dataClass: 'right aligned'},
                    {name:'name',sortField:'name',titleClass: 'text-center', dataClass: 'text-left'},
                    {name:'nickname',sortField:'nickname',callback:'upperCase'},
                    'email',
                    'birthdate',
                    {name:'address.line1',title:'<i class="grey mail outline icon"></i>Address1'},
                    {name:'address.line2',title:'Address2'},
                    'address.zipcode',
                    {name:'gender',callback:'genderLabel'},
                    {name:'salary',callback: 'formatNumber'},
                    {name:'birthdate',callback: 'formatDate|YYYY-MM-DD'}
                ],
                infoTemplate:"从 {from} 到 {to} 共 {total} 条",
                selectedTo:[]
            }
        },
        methods: {
            upperCase(value) {
                return value.toUpperCase()
            },
            genderLabel(value) {
                return value === 'M'
                ? '<span class="ui teal label" style="width: 62px"><i class="large man icon"></i>男</span>'
                : '<span class="ui pink label" style="width: 62px"><i class="large woman icon"></i>女</span>'
            },
            formatNumber(value) {
                return accounting.format(value,2,',')
            },
            formatDate(value,fmt = 'DD-MM-YYYY') {
                return value === null
                ? ''
                : moment(value,'YYYY-MM-DD').format(fmt)
            },
            onPaginationData (paginationData) {
                this.$refs.paginationTop.setPaginationData(paginationData);
                this.$refs.paginationInfoTop.setPaginationData(paginationData);

                this.$refs.pagination.setPaginationData(paginationData);
                this.$refs.paginationInfo.setPaginationData(paginationData);
            },
            onChangePage (page) {
                this.$refs.vuetable.changePage(page)
            }
        }
    }
</script>