<template>
    <div>
        <Layout
        :layout_breadcrumb_items="[
            { text: 'Home', link: '/' },
            { text: 'Dashboard Checkout Monitoring' },
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
                <div class="col-lg-2">
                    <label class="fs-13 mb-0">Cara Pulang</label>
                    <v-select class="select-size-sm" v-model="params.exit_id" @input="onListCheckout" :options="OptionsExit" :reduce="item => item.value" label="title" placeholder="pilih cara pulang"></v-select>
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
                <template #cell(statusPulang)="row">
                    <template v-if="!row.item.statusPulang">
                        <router-link :to="{ path: '/bpjs/checkout/' + row.item.registration_destination_id + '/'+ row.item.sep }">
                            <b-button size="sm" variant="success-500" class="mr-1" >
                                <feather-icon icon="FileIcon" /> UpdateTglPulang
                            </b-button>
                        </router-link>
                    </template>
                    <template v-else>
                        {{ row.item.statusPulang }}
                    </template>
                </template>
                <!-- bpjs/checkout -->
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
                exit_id:'',
            },
            
            OptionsExit:[
                { value: '807ca005-5827-4e19-9c97-0bc3d4e924ca', title: 'Atas Persetujuan Dokter' },
                { value: '4a6d28c4-e020-425f-91e1-d39699cf679b', title: 'Meninggal' },
                { value: 'a1631acc-6a94-414d-95de-a9faea0f1c2f', title: 'Dirujuk' },
                { value: 'ae0aabfa-1600-443f-8a60-62f9e76b1608', title: 'Permintaan Sendiri' },
                { value: 'dfe2b9c6-5934-4748-aad9-6d19c957ecfd', title: 'Kabur' },
                { value: '03e783b8-92ef-40c2-a867-d5895ffbfc83', title: 'Poliklinik' },
                { value: 'f359a4b5-4406-49d7-9a81-49bf34b4c21a', title: 'Perbaikan' },
                { value: '2542a5f7-570a-449c-9eee-525569c0ce1e', title: 'Rawat Inap' },
            ],
            CheckoutTable: [
                { key: '#', label: 'No', thStyle: { maxWidth: '60px' } },
                { sortable: true, key: 'fullname', label: 'Nama Pasien', thStyle: { minWidth: '300px' } },
                { sortable: true, key: 'health_facility_item_name', label: 'Bangsal', thStyle: { minWidth: '200px' }  },
                { sortable: true, key: 'rencana_pulang', label: 'Waktu Rencana Pulang', thStyle: { minWidth: '200px' } },
                { sortable: true, key: 'isi_form_pasien_pulang', label: 'Waktu Pengisian Form Pasien Pulang', thStyle: { minWidth: '200px' } },
                { sortable: true, key: 'proses_resep', label: 'Waktu Resep Diproses', thStyle: { minWidth: '100px' } },
                { sortable: true, key: 'billing_selesai', label: 'Waktu Billing Selesai Diproses', thStyle: { minWidth: '100px' } },
                { sortable: true, key: 'checkout', label: 'Waktu Pasien Pulang (Checkout)', thStyle: { minWidth: '100px' }},
                { sortable: true, key: 'exit_reason', label: 'Status', thStyle: { minWidth: '100px' }},
                { sortable: true, key: 'statusPulang', label: 'Status Pulang BPJS', thStyle: { minWidth: '100px' }},
                { sortable: true, key: 'dead', label: '', thStyle: { minWidth: '100px' }},
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
                const checkoutResp = await this.$store.dispatch("dashboard/getDashboardCheckoutMonitoring", this.params)
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

                this.$store.dispatch("dashboard/getDashboardCheckoutMonitoring", this.params )
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