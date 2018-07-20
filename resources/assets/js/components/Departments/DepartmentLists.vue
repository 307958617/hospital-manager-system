<template>
    <div class="ui container">
        <filter-bar></filter-bar>
        <vuetable ref="vuetable"
                  :api-mode="false"
                  :fields="fields"
                  :multi-sort="true"
                  :data-manager="dataManager"
                  pagination-path="pagination"
                  @vuetable:pagination-data="onPaginationData"

        ></vuetable>
        <div class="vuetable-pagination ui basic segment grid">
            <vuetable-pagination-info ref="paginationInfo" :infoTemplate="infoTemplate"></vuetable-pagination-info>
            <vuetable-pagination ref="pagination"
                                 @vuetable-pagination:change-page="onChangePage"
            >
            </vuetable-pagination>
            <span v-if="this.perPage < this.total" class="ui right floated pagination menu one wide column">跳&nbsp;&nbsp;&nbsp;&nbsp;转</span>
            <input v-if="this.perPage < this.total" type="text" class="ui right floated pagination menu one wide column" v-model="current_page"  @keyup.enter="jumpToPage(current_page)">
        </div>
    </div>
</template>

<script>
    import Vuetable from 'vuetable-2/src/components/Vuetable.vue'
    import VuetablePagination from 'vuetable-2/src/components/VuetablePagination.vue'
    import VuetablePaginationInfo from 'vuetable-2/src/components/VuetablePaginationInfo'
    export default {
        components: {
            Vuetable,
            VuetablePagination,
            VuetablePaginationInfo
        },
        data() {
            return {
                perPage:15,
                current_page:1,
                data: [],
                total:'',
                moreParams: {},
                fields: [
                    {
                        name: '__checkbox',   // <----
                        titleClass: 'center aligned',
                        dataClass: 'center aligned'
                    },
                    {name:'id',sortField:'id'},
                    {name:'name',sortField:'name'},
                    {name:'created_at'},
                ],
                infoTemplate:"从 {from} 到 {to} 共 {total} 条记录"
            }
        },
        watch: {
            data(newVal, oldVal) {
                this.$refs.vuetable.refresh();
            }
        },
        mounted() {
            axios.get('/department/org/get').then(res=> {
                this.data = res.data.data
            });
            this.$events.$on('filter-set', eventData => this.onFilterSet(eventData));
            this.$events.$on('filter-reset', e => this.onFilterReset())
        },
        methods: {
            onPaginationData (paginationData) {
                this.$refs.pagination.setPaginationData(paginationData);
                this.$refs.paginationInfo.setPaginationData(paginationData)
            },
            onChangePage (page) {
                this.$refs.vuetable.changePage(page);
            },
            jumpToPage(current_page) {
                this.$refs.vuetable.changePage(parseInt(current_page));
            },
            onFilterSet (filterText) {
                this.moreParams = {
                    'filter': filterText
                };
                Vue.nextTick( () => this.$refs.vuetable.refresh())
            },
            onFilterReset () {
                this.moreParams = {};
                Vue.nextTick( () => this.$refs.vuetable.refresh())
            },
            dataManager(sortOrder, pagination) {
                if (this.data.length < 1) return;

                let local = this.data;
                console.log(sortOrder);
                // sortOrder can be empty, so we have to check for that as well
                if (sortOrder.length > 0) {
                    console.log("orderBy:", sortOrder[0].sortField, sortOrder[0].direction);
                    local = _.orderBy(
                        local,
                        sortOrder[0].sortField,
                        sortOrder[0].direction
                    );
                }

                pagination = this.$refs.vuetable.makePagination(
                    local.length,
                    this.perPage,
                );
                this.current_page = pagination.current_page;
                this.total = pagination.total;

                console.log('pagination:', pagination);
                let from = pagination.from - 1;
                let to = from + this.perPage;

                return {
                    pagination: pagination,
                    data: _.slice(local, from, to)
                };
            },
        }
    }
</script>