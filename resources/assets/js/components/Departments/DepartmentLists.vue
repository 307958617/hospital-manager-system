<template>
    <div class="ui container">
        <input type="text" v-model="page"  @keyup.enter="gotoPage(page)">
        <vuetable ref="vuetable"
                  :api-mode="false"
                  :fields="fields"
                  :multi-sort="true"
                  :per-page="perPage"
                  :page="page"
                  :data-manager="dataManager"
                  pagination-path="pagination"
                  @vuetable:pagination-data="onPaginationData"

        ></vuetable>
        <div class="vuetable-pagination ui basic segment grid">
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
    export default {
        components: {
            Vuetable,
            VuetablePagination
        },
        data() {
            return {
                perPage:5,
                page:'1',
                data: [],
                fields: [
                    {
                        name: '__checkbox',   // <----
                        titleClass: 'center aligned',
                        dataClass: 'center aligned'
                    },
                    {name:'id',sortField:'id'},
                    {name:'name'},
                    {name:'created_at'},
                ],
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
        },
        methods: {
            onPaginationData (paginationData) {
                this.$refs.pagination.setPaginationData(paginationData);
            },
            onChangePage (page) {
                this.$refs.vuetable.changePage(page);
            },
            gotoPage(page) {
                this.$refs.vuetable.gotoPage(page);
                let a = $("a.item");
                a.addClass("active");

                console.log(a)
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
                    this.perPage
                );
//                console.log('pagination:', pagination);
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