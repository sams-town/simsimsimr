<template>
    <div>
        <Layout
        :layout_breadcrumb_items="[
            { text: 'Home', link: '/' },
            { text: 'Dashboard Balancing' },
        ]"
        >
        <div class="d-flex justify-content-between">
            <div class="row w-100 align-items-end">
                <div class="col-lg-2">
                    <label class="fs-13 mb-0">Cari Tanggal Awal</label>
                    <b-form-input :disabled="isDisabledFilter" v-model="params.date_start" @input="onListCheckout" size="sm" type="date" />
                </div>
                <div class="col-lg-2">
                    <label class="fs-13 mb-0">Cari Tanggal Akhir</label>
                    <b-form-input :disabled="isDisabledFilter" v-model="params.date" @input="onListCheckout" size="sm" type="date" />
                </div>
            </div>
            <!-- <div class="d-flex align-items-end">
                <div class="d-flex align-items-center">
                    <span class="fs-12 mr-1">Tampilkan</span>
                    <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions" />
                </div>
            </div> -->
        </div>
        <div class="table-responsive mt-12">
            <b-table
                bordered
                small
                head-variant="light"
                :items="ListItems"
                :fields="CheckoutTable"
                :per-page="0"
                :current-page="currentPage"
                thead-tr-class="fs-13"
                tbody-tr-class="fs-12"
                class="text-center"
            >
                <template #cell(#)="data">
                {{ data.index + 1 }}
                </template>
                <template #cell(receipt_data)="row">
                    <template v-for="value in row.item.receipt_data">
                        <div>{{ value.pharmacy_catalog_name }}</div>
                    </template>
                </template>
            </b-table>
        </div>
        <!-- <div class="col px-0 mt-20">
            <div class="d-flex">
                <div class="mr-auto">
                    <b-pagination v-model="currentPage" :total-rows="totalRows" :per-page="perPage" align="fill" size="sm" class="my-0"></b-pagination>
                </div>
                <span class="fs-12">Menampilkan {{ firstRows }} hingga 
                    <template v-if="lastRows < totalRows">{{ lastRows }}</template> 
                    <template v-else>{{ totalRows}}</template> 
                    dari {{ totalRows }} entri
                </span>
            </div>
        </div> -->
      </Layout>
    </div>
  </template>
  <script>
  import moment from 'moment'
  import { mapState } from 'vuex'
  
  export default {
    components: {
    },
    data() {
        return {
            params: {
                date_start: moment(new Date()).format('YYYY-MM-DD'),
                date: moment(new Date()).format('YYYY-MM-DD'),
            },
            CheckoutTable: [
                { sortable: true, key: 'no_invoice', label: 'Faktur', thStyle: { minWidth: '100px' } },
                { sortable: true, key: 'vendor_name', label: 'Vendor', thStyle: { minWidth: '100px' }  },
                { sortable: true, key: 'receipt_data', label: 'Item', thStyle: { minWidth: '80px' } },
                { sortable: true, key: 'nfinish', label: 'Tagihan', thStyle: { minWidth: '80px' } },
                { sortable: true, key: 'patient_name', label: 'Nama', thStyle: { minWidth: '300px' } },
                { sortable: true, key: 'norm', label: 'NORM', thStyle: { minWidth: '100px' } },
                { sortable: true, key: 'sep', label: 'SEP', thStyle: { minWidth: '100px' } },
                { sortable: true, key: 'register_date', label: 'Tgl Masuk', thStyle: { minWidth: '100px' } },
                { sortable: true, key: 'checkout_date', label: 'Tgl Pulang', thStyle: { minWidth: '100px' } },
                { sortable: true, key: 'insurance_name', label: 'Jaminan', thStyle: { minWidth: '100px' } },
                { sortable: true, key: 'desc_inacbg', label: 'INACBG', thStyle: { minWidth: '100px' }},
                { sortable: true, key: 'cost_inacbg', label: 'TARIF INA', thStyle: { minWidth: '100px' }},
                { sortable: true, key: 'period_name', label: 'Ket', thStyle: { minWidth: '100px' }},
            ],
            ListItems:[],
            currentPage:1,
            requestCancelToken: null,
            searchTimeout: null,
            isDisabledFilter: false,
        }
    },
    watch: {},
    methods: {
        async getDataCheckoutDashboard(){
            try {
                const checkoutResp = await this.$store.dispatch("dashboard/getDashboardBalancing", this.params)
                this.ListItems = checkoutResp.data
                // console.log('checkoutResponse', checkoutResp)
            } catch (error) {
                console.error("Failed to fetch checkout dashboard data:", error)
            }
        },
        onListCheckout() {
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }

            this.searchTimeout = setTimeout(() => {
                this.isDisabledFilter = true;

                this.$store.dispatch("dashboard/getDashboardBalancing", this.params )
                .then((res) => {
                    this.ListItems = res.data
                    this.isDisabledFilter = false
                })
                .catch((err) => {
                    console.error("Error saat request:", err)
                    this.isDisabledFilter = false
                })
            }, 300)
        },
    },
    computed: {
        ...mapState({
            dashboardData: state => state.dashboard.dashboardData,
        }),
    },
  
    created() {
        this.getDataCheckoutDashboard()
    }
  }
  </script>
  <style scoped>
    /* .table-responsive .b-table tbody > tr > td {
        padding-top: 10px;
        padding-bottom: 10px;
        line-height: 1.4;
    } */
  </style>