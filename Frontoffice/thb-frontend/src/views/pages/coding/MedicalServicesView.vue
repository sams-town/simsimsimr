<template>
    <div id="app">
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'JM' },
                { text: 'BPJS' }
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
                                  <div class="col-12 col-lg-4 mt-12">
                                      <label class="c-form-label">Instalasi</label>
                                      <v-select v-model="params.health_facility_id" :options="OptionsHealth" :reduce="OptionsHealth => OptionsHealth.value" label="title" placeholder="-- Instalasi --"></v-select>
                                  </div>
                                  <div class="col-12 col-lg-4 mt-12">
                                      <label class="c-form-label">Penjamin</label>
                                      <v-select v-model="params.company_id" :options="OptionsCompanyInsurance" :reduce="OptionsCompanyInsurance => OptionsCompanyInsurance.value" label="title" placeholder="-- Penjamin --"></v-select>
                                  </div>
                              </div>
                              <div class="d-flex mt-24">
                                  <b-button @click="filterData" variant="primary" :disabled="isDisabledFilter" class="mr-2">
                                      <feather-icon icon="SearchIcon" /> Filter
                                  </b-button>
                                  <b-button @click="importData" variant="primary" class="mr-2">
                                      <feather-icon icon="DownloadIcon" /> Import BA
                                  </b-button>
                                  <b-button @click="exportToExcel" variant="primary" class="mr-2">
                                      <feather-icon icon="DownloadIcon" /> Export Data
                                  </b-button>
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
                                <th>SEP</th>
                                <th>KET</th>
                                <th>RANAP/RAJAL</th>
                                <th>KELAS</th>
                                <th>NAMA</th>
                                <th>DPJP</th>
                                <th>TGL.MASUK</th>
                                <th>TGL.KELUAR</th>
                                <th>INACBG</th>
                                <th>TARIF INACBG</th>
                                <th>TOTAL TARIF</th>
                                <th>TARIF RS</th>
                                <th>25%</th>
                                <th>TOTAL JASDOK</th>
                                <th>20%</th>
                                <th>TARIF DOKTER</th>
                                <template v-for="(item_d, index_d) in doctor">
                                    <th><div>{{item_d.nickname}}</div></th>
                                </template>
                            </tr>
                        </thead>
                        <tr>
                            <td colspan="3"><label><b></b></label></td>
                            <td colspan="11" class="text-right"><b>TOTAL</b></td>
                            <td class="text-right">
                                {{ total_medical_fee | currency }}
                            </td>
                        </tr>
                        <tr class="item" v-for="(item, index) in items">
                            <td class="text-center">
                                <input style="width: 40px;"
                                type="checkbox" 
                                :disabled="item.periode_id === null"
                                :checked="isItemSelected(item.id)"
                                @change="toggleItemSelection(item)"
                                />
                            </td>
                            <td>{{ item.sep }}</td>
                            <td>{{ item.period_name }}</td>
                            <td>{{ item.health_facility_name }}</td>
                            <td>{{ item.service_class_name }}</td>
                            <td>{{ item.name }}</td>
                            <td>{{ item.dpjp }}</td>
                            <td>{{ item.checkin_date }}</td>
                            <td>{{ item.checkout_date }}</td>
                            <td>{{ item.desc_inacbg }}</td>
                            <td class="text-right">{{ item.cost_inacbg | currency }}</td>
                            <td class="text-right">{{ item.cost_inacbg_tariff | currency }}</td>
                            <td class="text-right">
                                <b-button 
                                    size="sm" 
                                    variant="outline-primary" 
                                    :disabled="isDisabled" 
                                    @click.prevent="viewInvoice(item.registration_id)"
                                    class="mr-1">
                                    <feather-icon icon="ViewIcon" />
                                    {{ item.cost_hostpital | currency }}
                                </b-button>
                            </td>
                            <td class="text-right">{{ item.health_facility_id === '40f06534-07ec-4221-ad9d-dea08196079f' || '3cb626f5-9148-4fb1-8663-3d7f028b01a6' ? item.cost_25 : '' | currency }}</td>
                            <!-- <td class="text-right">{{ item.health_facility_id === '40f06534-07ec-4221-ad9d-dea08196079f' || '3cb626f5-9148-4fb1-8663-3d7f028b01a6' ? item.cost_inacbg-((item.cost_inacbg*item.percentage)/100) : item.cost_inacbg-((item.cost_inacbg*20)/100) | currency }}</td> -->
                            <td class="text-right">{{ item.health_facility_id === '40f06534-07ec-4221-ad9d-dea08196079f' || '3cb626f5-9148-4fb1-8663-3d7f028b01a6' ? item.fee_doctor : ((item.cost_inacbg*item.percentage)/100) | currency }}</td>
                            <td class="text-right">{{ item.health_facility_id === '40f06534-07ec-4221-ad9d-dea08196079f' || '3cb626f5-9148-4fb1-8663-3d7f028b01a6' ? item.cost_20 : ((item.cost_inacbg*item.percentage)/100) | currency }}</td>
                            <td class="text-right">{{ item.health_facility_id === '40f06534-07ec-4221-ad9d-dea08196079f' || '3cb626f5-9148-4fb1-8663-3d7f028b01a6' ? item.medical_fee : ((item.cost_inacbg*item.percentage)/100) | currency }}</td>
                            <template v-for="(item_d, index_d) in doctor">
                                <td class="text-right"> 
                                    {{ item[item_d.id] == '' ? '' : item[item_d.id] | currency  }} 
                                </td>
                            </template>
                        </tr>
                    </table>
                </div>
                <b-button @click="saveValidation" variant="primary" class="mr-2 mt-3">
                    <feather-icon icon="SaveIcon" /> Selesai Validasi
                </b-button>
                  <!-- <b-table bordered responsive hover small head-variant="light" :items="items" :fields="fields" :per-page="0" :current-page="currentPage" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center"></b-table>
                  <div class="col px-0 mt-20">
                      <div class="d-flex">
                          <span class="text-regular-14">Menampilkan {{ firstRows }} hingga <template
                                  v-if="lastRows < totalRows">{{ lastRows }}</template> <template v-else>{{ totalRows
                                  }}</template> dari {{ totalRows }} entri</span>
                          <div class="ml-auto">
                              <b-pagination v-model="currentPage" :total-rows="totalRows" :per-page="perPage"
                                  align="fill" size="sm" class="my-0"></b-pagination>
                          </div>
                      </div>
                  </div> -->
                  
                </section>
                <b-modal v-model="active.invoicePreview" scrollable size="lg" title="Invoice" hide-footer>

                </b-modal>

                <b-modal v-model="active.import" scrollable size="lg" title="Import BA" hide-footer>
                    <div class="row">
                        <div class="col-12 col-lg-12 mt-1">
                            <div class="d-flex" style="padding: 0 -12px;">
                                <div class="col-3 pl-0">
                                    <label class="c-form-label">Periode</label>
                                    <v-select v-model="period_id" :options="OptionsPeriod" size="sm" :reduce="OptionsPeriod => OptionsPeriod.value" label="title" placeholder="-- Periode --"></v-select>
                                </div>
                                <div class="col-4 pl-0">
                                    <label class="c-form-label">File</label>
                                    <b-form-file v-model="input_pdf" @change="uploadExcel($event)" size="lg" autocomplete="off"></b-form-file>
                                </div>
                                <div class="col-3 pl-0 mt-4">
                                    <b-button @click="saveImport()" size="lg" variant="primary">
                                        <feather-icon icon="SaveIcon" /> Simpan
                                    </b-button>
                                </div>
                            </div><br><br><br></br><br><br><br></br><br><br><br></br><br><br><br></br>
                            <b-table fixed small bordered responsive :items="resultBA" head-variant="light" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="text-center"></b-table>
                        </div>
                    </div>
                </b-modal>
                <PaymentInvoiceComponent ref="childInvoiceRef" />
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
import ExcelJS from 'exceljs'
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
                {value:'7073c48a-50bd-4063-9fe1-b1f842a97aca',title:'AGUSTUS 25 - REGULER'},
                {value:'442b4d7d-8a25-46d3-a650-c2d515778b55',title:'AGUSTUS 25 - REVISI 1'},
                {value:'e0c0900f-60ba-4e91-b191-dc386f2f1123',title:'AGUSTUS 25 - REVISI 2'},
                {value:'0ea71bc3-a70f-400a-828e-772523362e54',title:'SEPTEMBER 25 - REGULER'},
                {value:'feda9a8f-1cbe-42ed-ad6e-80c1d777ba8e',title:'SEPTEMBER 25 - REVISI 1'},
                {value:'a47b3762-22b8-4746-af55-24c9995426d0',title:'SEPTEMBER 25 - REVISI 2'},
                {value:'646d9f61-82a1-4ff1-bd1c-108bb32e63c2',title:'OKTOBER 25 - REGULER'},
                {value:'5bc2ee0a-67c9-440a-805f-c6a212537466',title:'OKTOBER 25 - REVISI 1'},
                {value:'922354cd-718a-4e58-81e3-4fe9e31e4c1c',title:'OKTOBER 25 - REVISI 2'},
                {value:'6cc1154a-6d94-4ee8-9c20-1eb905ab63e0',title:'NOVEMBER 25 - REGULER'},
                {value:'f15c7dcd-cbe9-4bd2-9b43-807eb6d34f44',title:'NOVEMBER 25 - REVISI 1'},
                {value:'7064e332-0823-4fb5-a33b-bb3caa3754fe',title:'NOVEMBER 25 - REVISI 2'},
                {value:'775d3f56-0e43-48da-9482-040612f73bfc',title:'DESEMBER 25 - REGULER'},
                {value:'4dbb0011-be73-4143-985f-cdf672eb4039',title:'DESEMBER 25 - REVISI 1'},
                {value:'8b7e1d76-5676-4892-8727-8873c8fb3ae9',title:'DESEMBER 25 - REVISI 2'},
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
                // date_start: '2025-08-01',
                // date_end: '2025-08-31',
                date_start: '2025-10-01',
                date_end: '2025-10-31',
                insurance_id: 2,
                company_id: '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8',
            },
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
                this.$store.dispatch('costing/getAllFast',this.params)
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.$store.dispatch('costing/getAllFast',this.params)
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
        // this.$store.dispatch('costing/getAllFast',this.params)
        this.$store.dispatch('healthfacility/getParam', 'healthfacility')
        this.$store.dispatch('helper/getParam', 'type_how_to_enter')
        this.$store.dispatch('helper/getParam', 'sex')
        this.$store.dispatch('helper/getParam', 'is_new')
        this.show = true
    },
    methods: {
        mappingArray(data) {
            if (data) {
                this.total_medical_fee = 0
                for (let index = 0; index < data.length; index++) {
                    this.total_medical_fee += data[index].medical_fee
                }
            }
        },
        saveValidation () {
            this.$store.dispatch('costing/sendValidation', { data:this.selectedItems})
            .then(data => {
                console.log(data)
            })
        },
        getAnyDpjpValue(item) {
            return this.doctor
            .filter(d => Number(item[d.id]) > 0) // hanya ambil yang > 0
            .map(d => ({
                id: d.id,
                value: Number(item[d.id])
            }));
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
                    medical_fee: item.medical_fee,
                    health_facility_id: item.health_facility_id,
                    doctor_in_charge_id: item.doctor_in_charge_id,
                    any_dpjp: this.getAnyDpjpValue(item),
                });
            }

            // sinkronkan selectAll
            this.syncSelectAll();
        },

        // toggle semua baris
        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedItems = this.items
                .filter(i => i.period_id !== null)
                .map(item => ({
                    id: item.id,
                    medical_fee: item.medical_fee,
                    health_facility_id: item.health_facility_id,
                    doctor_in_charge_id: item.doctor_in_charge_id,
                    any_dpjp: this.getAnyDpjpValue(item),
                }));
            } else {
                this.selectedItems = [];
            }
        },

        // cek apakah semua item sudah terpilih
        syncSelectAll() {
            const selectableItems = this.items.filter(i => i.period_id !== null);
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
        filterData () {
            this.show = true
            this.isDisabledFilter = true
            this.$store.dispatch('costing/getAllFast', this.params)
            .then(res => {
                console.log(res)
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
        async exportToExcel() {
            const workbook = new ExcelJS.Workbook();
            const worksheet = workbook.addWorksheet("Download Excel");
            const date_range =
                this.params.date_start == this.params.date_end
                    ? this.params.date_start
                    : `${this.params.date_start}_${this.params.date_end}`;

            // ===== DEFINE HEADER COLUMNS =====
            const headerTitles = [
                'reg_des_id',
                'SEP',
                'REFER',
                'KET',
                'RANAP/RAJAL',
                'KELAS',
                'NAMA',
                'DPJP',
                'TGL.MASUK',
                'TGL.KELUAR',
                'INACBG',
                'TARIF INACBG',
                'TOTAL TARIF',
                'TARIF RS',
                '25%',
                'TOTAL JASDOK',
                '20%',
                'TARIF DOKTER',
                ...this.doctor.map(d => d.nickname) // kolom dinamis dari doctor
            ];

            const headerRow = worksheet.addRow(headerTitles);

            // ===== STYLE HEADER =====
            headerRow.eachCell((cell) => {
                cell.font = { bold: true };
                cell.alignment = { horizontal: 'center', vertical: 'middle' };
                cell.fill = {
                    type: 'pattern',
                    pattern: 'solid',
                    fgColor: { argb: 'FFFF99' }, // kuning muda
                };
                cell.border = {
                    top: { style: 'thin' },
                    left: { style: 'thin' },
                    bottom: { style: 'thin' },
                    right: { style: 'thin' },
                };
            });

            // ===== HELPER UNTUK BORDER =====
            const applyBorderToRow = (row) => {
                row.eachCell((cell) => {
                    cell.border = {
                        top: { style: 'thin' },
                        left: { style: 'thin' },
                        bottom: { style: 'thin' },
                        right: { style: 'thin' },
                    };
                });
            };

            // ===== ISI DATA =====
            this.items.forEach((item) => {
                const isSpecialHF =
                    item.health_facility_id === '40f06534-07ec-4221-ad9d-dea08196079f' ||
                    item.health_facility_id === '3cb626f5-9148-4fb1-8663-3d7f028b01a6';

                const rowData = [
                    item.registration_destination_id,
                    item.sep,
                    item.no_refer,
                    item.period_name,
                    item.health_facility_name,
                    item.service_class_name,
                    item.name,
                    item.dpjp,
                    item.checkin_date,
                    item.checkout_date,
                    item.desc_inacbg,
                    item.cost_inacbg,
                    item.cost_inacbg_tariff,
                    item.cost_hostpital,
                    isSpecialHF ? item.cost_25 : '',
                    isSpecialHF
                        ? item.fee_doctor
                        : (item.cost_inacbg * item.percentage) / 100,
                    isSpecialHF ? item.cost_20 : (item.cost_inacbg * item.percentage) / 100,
                    isSpecialHF ? item.medical_fee : (item.cost_inacbg * item.percentage) / 100,
                    // kolom dinamis untuk dokter
                    // ...this.doctor.map((d) => (item[d.id] ? item[d.id] : '')),
                    ...this.doctor.map((d) => (d.id === item.doctor_in_charge_id ? '' : (item[d.id] || ''))),
                ];

                const row = worksheet.addRow(rowData);
                row.eachCell((cell) => {
                    if (typeof cell.value === 'number') {
                        cell.numFmt = '#,##0'; // format angka ribuan
                        cell.alignment = { horizontal: 'right' };
                    }
                });
                applyBorderToRow(row);
            });

            // ===== AUTO WIDTH TIAP KOLOM =====
            worksheet.columns.forEach((column) => {
                let maxLength = 0;
                column.eachCell({ includeEmpty: true }, (cell) => {
                    const val = cell.value ? cell.value.toString() : '';
                    maxLength = Math.max(maxLength, val.length);
                });
                column.width = maxLength < 12 ? 12 : maxLength;
            });

            // ===== DOWNLOAD FILE =====
            const buffer = await workbook.xlsx.writeBuffer();
            const blob = new Blob([buffer], {
                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `Report_Jasdok_${date_range}.xlsx`;
            link.click();
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
            
            this.$store.dispatch('costing/getAllFast',this.params)
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
