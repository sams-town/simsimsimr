<template>
    <div id="app">
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'JM' },
                { text: 'Dokter' }
            ]"
        >
          <div>
              <!-- <topMenu selectedMenu="menu1" /> -->
              <section class="card mt-32">
                  <div class="card-header font-weight-bold bg-primary-200">Filter Data</div>
                  <div class="card-body">
                      <div class="d-flex justify-content-center">
                          <b-form class="col-lg-12">
                              <div class="row">
                                  <div class="col-lg-5 px-0 mt-12">
                                      <div class="d-flex align-items-center">
                                          <div class="col-lg">
                                              <label class="c-form-label">Tanggal Mulai</label>
                                              <b-form-input type="date" v-model="params.date_start"></b-form-input>
                                          </div>
                                          <span>-</span>
                                          <div class="col-lg">
                                              <label class="c-form-label">Tanggal Akhir</label>
                                              <b-form-input type="date" v-model="params.date_end"></b-form-input>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="col-12 col-lg-3 mt-12">
                                      <label class="c-form-label">Dokter</label>
                                      <v-select v-model="params.doctor_in_charge_name" :options="OptionsDoctor" :reduce="OptionsDoctor => OptionsDoctor.value" label="title" placeholder="-- Dokter --"></v-select>
                                  </div>
                                  <!-- <div class="col-12 col-lg-4 mt-12">
                                      <label class="c-form-label">Instalasi</label>
                                      <v-select v-model="params.health_facility_id" :options="OptionsHealth" :reduce="OptionsHealth => OptionsHealth.value" label="title" placeholder="-- Instalasi --"></v-select>
                                  </div> -->
                                  <!-- <div class="col-12 col-lg-4 mt-12">
                                      <label class="c-form-label">Penjamin</label>
                                      <v-select v-model="params.company_id" :options="OptionsCompanyInsurance" :reduce="OptionsCompanyInsurance => OptionsCompanyInsurance.value" label="title" placeholder="-- Penjamin --"></v-select>
                                  </div> -->
                              </div>
                              <div class="d-flex mt-24">
                                  <b-button @click="filterData" variant="primary" :disabled="isDisabledFilter" class="mr-2">
                                      <feather-icon icon="SearchIcon" /> Filter
                                  </b-button>
                                  <!-- <b-button @click="importData" variant="primary" class="mr-2">
                                      <feather-icon icon="DownloadIcon" /> Import BA
                                  </b-button> -->
                                  <!-- <b-button @click="filterDataExcel" variant="primary" :disabled="isDisabledFilter" class="mr-2">
                                      <feather-icon icon="DownloadIcon" /> Export Data
                                  </b-button> -->
                                  <b-button @click="filterReset" variant="outline-primary" class="mr-2">
                                      <feather-icon icon="XIcon" /> Clear Filter
                                  </b-button>
                              </div>
                          </b-form>
                      </div>
                  </div>
              </section>
              <section class="mt-32 mt-lg-40">
                  <!-- <div class="row mx-0">
                      <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0 mr-1">
                          <span class="text-regular-14">Tampilkan</span>
                          <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions"/>
                          <span class="text-regular-14">Entri</span>
                      </b-form-group>
                  </div> -->
                  <div class="table-container">
                    <table class="table custom-table table-bordered table-striped">
                        <thead class="text-center text-bold-14">
                            <tr>
                                <th>
                                    <input 
                                    type="checkbox" 
                                    v-model="selectAll" 
                                    @change="toggleSelectAll"
                                    />
                                </th>
                                <th>NAMA DOKTER</th>
                                <th>TANGGAL</th>
                                <th>NAMA PASIEN</th>
                                <th>JENIS</th>
                                <th>RI/RJ</th>
                                <th>TINDAKAN DOKTER</th>
                                <th>JASA MEDIS DOKTER</th>
                            </tr>
                        </thead>
                        <!-- <template v-for="(item_g, index_g) in group"> -->
                            <tr>
                                <td class="text-right"><b>TOTAL</b></td>
                                <td colspan="7" class="text-right">
                                    {{ total_medical_fee | currency }}
                                </td>
                            </tr>
                            <!-- <tr class="item" v-for="(item, index) in items" v-if="item_g.doctor_in_charge_name === item.doctor_in_charge_name"> -->
                            <tr class="item" v-for="(item, index) in items">
                                <td class="text-center">
                                    <input style="width: 40px;"
                                    type="checkbox" 
                                    :disabled="item.periode_id === null"
                                    :checked="isItemSelected(item.id)"
                                    @change="toggleItemSelection(item)"
                                    />
                                </td>
                                <td>{{ item.doctor_in_charge_name }}</td>
                                <td>{{ item.checkin_date }}</td>
                                <td>{{ item.name }}</td>
                                <td>{{ item.period_name }}</td>
                                <td>{{ item.health_facility_name }}</td>
                                <td>{{ item.desc_inacbg }}</td>
                                <td class="text-right">{{ item.cost_doctor | currency }}</td>
                            </tr>
                            <tr>
                                <td class="text-right"><b>TOTAL</b></td>
                                <td colspan="7" class="text-right">
                                    {{ total_medical_fee | currency }}
                                </td>
                            </tr>
                            <!-- <tr>
                                <td colspan="3"><label><b>{{ item_g.doctor_in_charge_name }}</b></label></td>
                                <td colspan="10" class="text-right"><b>TOTAL</b></td>
                                <td class="text-right">
                                    {{ item_g.total_cost_doctor | currency }}
                                </td>
                            </tr> -->
                        <!-- </template> -->
                    </table>
                </div>
                <div class="col-3 pl-0">
                    <label class="c-form-label">Periode</label>
                    <v-select v-model="period_id" :options="OptionsPeriod" size="sm" :reduce="OptionsPeriod => OptionsPeriod.value" label="title" placeholder="-- Periode --"></v-select>
                </div>
                <b-button @click="saveValidation" variant="primary" class="mr-2 mt-3">
                    <feather-icon icon="SaveIcon" /> Selesai Validasi
                </b-button>
                  
                </section>
          </div>                
        </Layout>
    </div>
  </template>
<script>
import moment from 'moment'
import topMenu from '@/@core/components/top-menu/topMenu.vue'
import { mapState } from 'vuex'
import vSelect from 'vue-select'
import DatePicker from 'v-calendar'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import PaymentInvoiceComponent from '@/views/pages/payment/PaymentInvoiceComponent.vue';
import Layout from '@/@core/light/Layout.vue';
import * as pdfjsLib from 'pdfjs-dist/build/pdf'
import workerSrc from 'pdfjs-dist/build/pdf.worker.min.js'

export default {
    components: { Layout, topMenu, vSelect, DatePicker, ToastificationContent, PaymentInvoiceComponent },
    data() {
        return {
            selectAll: false,
            selectedItems: [], // array berisi registration_id yang dicentang
            urlRoot: process.env.VUE_APP_ROOT_API,
            purpose:'',
            OptionsPeriod:[
                {value:'4bfa53c2-14a0-4881-9ebb-62cdbada1790',title:'10 OKTOBER 2025'},
                {value:'b07d2ff7-c853-4e92-ae9d-451816af3941',title:'20 OKTOBER 2025'},
                {value:'95e4e2b7-f28f-40d7-978a-8548d9603e92',title:'10 NOVEMBER 2025'},
                {value:'87335483-45b5-4896-9623-fe2062eed420',title:'20 NOVEMBER 2025'},
                {value:'f65eade9-b845-4b80-84db-22a839c01984',title:'10 DESEMBER 2025'},
                {value:'817b1131-1cee-440b-b253-39fdf5d09dca',title:'20 DESEMBER 2025'},
            ],
            OptionsHealth:[
                {value:'40f06534-07ec-4221-ad9d-dea08196079f',title:'RAWAT INAP'},
                {value:'3cb626f5-9148-4fb1-8663-3d7f028b01a6',title:'RAWAT JALAN'},
            ],
            active:{
                invoicePreview:false,
                import:false
            },
            total_medical_fee: 0,
            totalRows:0,
            items:[],
            group:[],
            doctor:[],
            pdfAsDataUri:'',
            resultBA: null,
            input_pdf: null,
            thumbnail:null,
            period_id:null,
            file_name:null,
            file_type:null,
            params: {
                offset: 0,
                limit: 200,
                code: '',
                norm: '',
                name: '',
                sex: '',
                sep: '',
                how_to_enter_id: '',
                is_new: '',
                health_facility_id: '40f06534-07ec-4221-ad9d-dea08196079f',
                // health_facility_id: '3cb626f5-9148-4fb1-8663-3d7f028b01a6',
                health_facility_item_id: '',
                doctor_in_charge_id: '',
                doctor_in_charge_name: '',
                // date_start: moment(new Date()).format('YYYY-MM-DD'),
                // date_end: moment(new Date()).format('YYYY-MM-DD'),
                date_start: '2025-08-01',
                date_end: '2025-08-31',
                // date_start: '2025-09-01',
                // date_end: '2025-09-30',
                insurance_id: 2,
                company_id: '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8',
            },
            total_medical_fee: 0,
            currentPage:1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            isDisabled: false,
            isDisabledFilter: false,
            show: false,
            result:{file:''},
            fields: [
                { sortable: true, key: 'sep', label: 'SEP', thStyle: { minWidth: '140px' } },
                { sortable: true, key: 'name', label: 'Nama Pasien', thStyle: { minWidth: '140px' } },
                { sortable: true, key: 'doctor_in_charge_name', label: 'DPJP', thStyle: { minWidth: '150px' } },
                { sortable: true, key: 'desc_inacbg', label: 'INACBG', thStyle: { minWidth: '140px' } },
                { sortable: true, key: 'cost_inacbg', label: 'Tarif INACBG', thStyle: { minWidth: '140px' } },
                { sortable: true, key: 'cost_hostpital', label: 'Tarif RS', thStyle: { minWidth: '120px' } },
                { sortable: true, key: 'name', label: 'Aksi' },
            ],
        }
    },
    watch: {
        items() {
            this.syncSelectAll();
        },
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage * (value - 1) + 1
                this.lastRows = this.perPage * (value)
                this.params.offset = this.perPage * (value - 1)
                this.params.limit = this.perPage
                this.$store.dispatch('costing/getAllFastResume',this.params)
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.$store.dispatch('costing/getAllFastResume',this.params)
            }
        },
        'params.health_facility_id' (value) {
            if(value){
                return this.$store.dispatch('healthfacilityitem/getParam', value)
            }
        },
        'params.health_facility_item_id' (value) {
            if(value){
                return this.$store.dispatch('employee/getParam', value)
            }
        },
        'params.insurance_id' (value) {
            if(value){
                return this.$store.dispatch('company/getParamcompany', value)
            }
        },
    },
    computed: {
        ...mapState({ 
            start: state => state.costing.ms.start, 
            // totalRows: state => state.costing.ms.totalRows, 
            // items: state => state.costing.ms.items,
            // group: state => state.costing.ms.group,
            // doctor: state => state.costing.ms.doctor,
            pageOptions: state => state.costing.pageOptions,

            OptionsHealthFacility: state => state.healthfacility.options.healthfacility,
            OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,
            OptionsDoctor: state => state.employee.options.specialist,
            OptionsTypeHowToEnter: state => state.helper.options.howtoenter,
            OptionsSex: state => state.helper.options.sex,
            OptionsIsNew: state => state.helper.options.isNew,
            OptionsInsurance: state => state.helper.options.insurance,
            OptionsCompanyInsurance: state => state.company.options.company,
        }),
    },
    mounted() {
        this.$store.state.totalRows = this.items.length
        this.show = false
    },
    created () {
        this.$store.dispatch('employee/getParamAllSpecialist')
        this.$store.dispatch('company/getParamcompany', 2)
        // this.$store.dispatch('costing/getAllFastResume',this.params)
        this.$store.dispatch('healthfacility/getParam', 'healthfacility')
        this.$store.dispatch('helper/getParam', 'type_how_to_enter')
        this.$store.dispatch('helper/getParam', 'sex')
        this.$store.dispatch('helper/getParam', 'is_new')
        this.show = true
    },
    methods: {
        saveValidation () {
            this.$store.dispatch('costing/sendValidationPayroll', { data:this.selectedItems})
            .then(data => {
                console.log(data)
            })
        },

        // cek apakah item sudah dipilih
        isItemSelected(id) {
            return this.selectedItems.some(sel => sel.id === id);
        },

        // toggle 1 baris
        toggleItemSelection(item) {
            const exists = this.selectedItems.find(sel => sel.id === item.id);
            if (exists) {
                // jika sudah ada → hapus
                this.selectedItems = this.selectedItems.filter(sel => sel.id !== item.id);
            } else {
                // jika belum ada → tambah
                this.selectedItems.push({
                    id: item.id,
                    sep: item.sep,
                    medical_fee: item.medical_fee,
                    period_id: this.period_id,
                });
            }

            // sinkronkan selectAll
            this.syncSelectAll();
        },

        // toggle semua baris
        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedItems = this.items
                .filter(i => i.id !== null)
                .map(item => ({
                    id: item.id,
                    sep: item.sep,
                    period_id: this.period_id,
                    medical_fee: item.medical_fee,
                    doctor_in_charge_id: item.doctor_in_charge_id,
                }));
            } else {
                this.selectedItems = [];
            }
        },

        // cek apakah semua item sudah terpilih
        syncSelectAll() {
            const selectableItems = this.items.filter(i => i.id !== null);
            this.selectAll = this.selectedItems.length === selectableItems.length;
        },
        viewInvoice(e) {
            // this.active.invoicePreview = true
            this.$refs.childInvoiceRef.parentTrigger(e);
            alert(e)
        },
        
        // parentTrigger(message) {
        // },
        uploadExcel(e) {
            for (let i = 0; i < e.target.files.length; i++) {
                const file = e.target.files[i]
                const reader = new FileReader()
                reader.onload = (filez) => {
                    this.file_name = file
                    this.file_type = file.type
                    this.result.file = filez.target.result.replace("data:"+ file.type +";base64,", '');
                }
                reader.readAsDataURL(file)
            }
        },
        saveImport() {
            this.$store.dispatch('costing/sendBA', { id:1,period_id:this.period_id,result: this.result.file, name: this.file_name, extension: this.file_type })
            .then(data => {
                console.log(data)
                this.resultBA  = []
                this.resultBA  = data.data
            })
        },
        importData() {
            this.active.import = true
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        parsed_start_from_date(input_date) {
            return moment(input_date).format('DD-MM-YYYY');
        },
        mappingArray(data) {
            if (data) {
                this.total_medical_fee = 0
                for (let index = 0; index < data.length; index++) {
                    this.total_medical_fee += data[index].cost_doctor
                }
            }
        },
        filterData () {
            this.show = true
            this.isDisabledFilter = true
            this.$store.dispatch('costing/getAllFastResume', this.params)
            .then(res => {
                this.totalRows  = res.data.totalRows
                this.items      = res.data.rows
                this.group      = res.data.group
                this.doctor     = res.data.doctor
                this.mappingArray(this.items)

                this.show = false
                this.isDisabledFilter = false
            },
            err => {
                this.show = false
                this.isDisabledFilter = false
            })
        },
        filterReset () {
            this.params = {
                offset: 0,
                limit: this.perPage,
                code: '',
                norm: '',
                name: '',
                sex: '',
                sep: '',
                type_how_to_enter_id: '',
                is_new: '',
                health_facility_id: '',
                health_facility_item_id: '',
                doctor_in_charge_id: '',
                doctor_in_charge_name: '',
                date_start: moment(new Date()).format('YYYY-MM-DD'),
                date_end: moment(new Date()).format('YYYY-MM-DD'),
                insurance_id: '',
                company_id: '',
            }
            
            this.$store.dispatch('costing/getAllFastResume',this.params)
        },
    },
    filters:{
        currency(value) {
            return value == '' ? '' : new Intl.NumberFormat('en-US', { style: 'currency', currency: 'IDR' }).format(value)
        }
    }
}
</script>
<style scoped>
.table-container {
  max-height: 800px; /* tinggi viewport tabel */
  overflow: auto; /* supaya bisa scroll vertikal & horizontal */
  position: relative;
}

/* Table base */
.custom-table {
  border-collapse: collapse;
  width: 100%;
}

.custom-table th,
.custom-table td {
  white-space: nowrap;
  background: #fff;
}

/* Freeze header */
.custom-table thead th {
  position: sticky;
  top: 0;
  z-index: 5;
  background-color: #fff !important;
  border-bottom: 2px solid #ddd;
}

/* Freeze kolom 1 sampai 6 */
.custom-table th:nth-child(1),
.custom-table td:nth-child(1) {
  position: sticky;
  left: 0;
  z-index: 7;
  background: #fff;
}

.custom-table th:nth-child(2),
.custom-table td:nth-child(2) {
  position: sticky;
  left: 30px; /* lebar kolom 1 */
  z-index: 7;
  background: #fff;
}

.custom-table th:nth-child(3),
.custom-table td:nth-child(3) {
  position: sticky;
  left: 150px; /* lebar kolom 1 */
  z-index: 7;
  background: #fff;
}

.custom-table th:nth-child(4),
.custom-table td:nth-child(4) {
  position: sticky;
  left: 300px; /* kolom 1 + kolom 2 */
  z-index: 7;
  background: #fff;
}

.custom-table th:nth-child(5),
.custom-table td:nth-child(5) {
  position: sticky;
  left: 480px; /* kolom 1+2+3 */
  z-index: 7;
  background: #fff;
}

.custom-table th:nth-child(6),
.custom-table td:nth-child(6) {
  position: sticky;
  left: 550px; /* kolom 1+2+3+4 */
  z-index: 7;
  background: #fff;
}

.custom-table th:nth-child(7),
.custom-table td:nth-child(7) {
  position: sticky;
  left: 680px; /* kolom 1+2+3+4+5 */
  z-index: 7;
  background: #fff;
}

/* Tambahan: biar garis tabel tetap rapih saat sticky */
.custom-table th,
.custom-table td {
  border: 1px solid #dee2e6;
}
.disabled-row {
  background-color: #f3f3f3;
  opacity: 0.6;
  pointer-events: none; /* Tidak bisa klik apa pun */
}
</style>
