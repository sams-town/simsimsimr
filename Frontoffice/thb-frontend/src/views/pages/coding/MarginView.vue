<template>
    <div id="app">
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Margin' },
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
                                  <div class="col-12 col-lg-4 mt-12">
                                      <label class="c-form-label">Ruangan</label>
                                      <v-select v-model="params.health_facility_item_id" :options="OptionsHealthFacilityItem" :reduce="OptionsHealthFacilityItem => OptionsHealthFacilityItem.value" label="title" placeholder="-- Ruangan --"></v-select>
                                  </div>
                              </div>
                              <div class="d-flex mt-24">
                                  <b-button @click="filterData" variant="primary" :disabled="isDisabledFilter" class="mr-2">
                                      <feather-icon icon="SearchIcon" /> Filter
                                  </b-button>
                                  <!-- <b-button @click="importData" variant="primary" class="mr-2">
                                      <feather-icon icon="DownloadIcon" /> Import BA
                                  </b-button> -->
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
                                <th>RUANGAN</th>
                                <th>NORM</th>
                                <th>PASIEN</th>
                                <th>HPP FARMASI</th>
                                <th>HPP LAB</th>
                                <th>HPP RADIOLOGI</th>
                                <th>OVERHEAD</th>
                                <th>JM</th>
                                <th>INA</th>
                                <th>TOTAL HPP</th>
                                <th>TARIF INA</th>
                                <th>KETERANGAN</th>
                                <th>FOLLOW-UP</th>
                            </tr>
                        </thead>
                        <tr>
                            <td><label><b></b></label></td>
                            <!-- <td colspan="2" class="text-right"><b>TOTAL</b></td>
                            <td class="text-right">
                                {{ total_medical_fee | currency }}
                            </td> -->
                        </tr>
                        <tr class="item" v-for="(item, index) in items">
                            <td>{{ item.health_facility_item_name }}</td>
                            <td>{{ item.norm }}</td>
                            <td>
                                <b-button 
                                    size="sm" 
                                    variant="outline-primary" 
                                    :disabled="isDisabled" 
                                    @click.prevent="viewInvoice(item.registration_id)"
                                    class="mr-1">
                                    <feather-icon icon="ViewIcon" />
                                    {{ item.name_patient }}
                                </b-button>
                                <router-link :to="{ path: '/patient/doctor/' + item.id }">
                                    <b-button size="sm" variant="primary" v-b-popover.hover.right="'Buka Rekam Medis Disini'" class="mr-1">
                                        <feather-icon icon="FileIcon" />
                                    </b-button>
                                </router-link>
                            </td>
                            <td>
                                <b-button 
                                    size="sm" 
                                    variant="outline-primary" 
                                    :disabled="isDisabled" 
                                    @click.prevent="viewPrescriptionHPP(item.registration_id)"
                                    class="mr-1">
                                    <feather-icon icon="ViewIcon" />
                                    {{ item.total_hpp_farmasi | currency}}
                                </b-button>
                            </td>
                            <td>
                                <b-button 
                                    size="sm" 
                                    variant="outline-primary" 
                                    :disabled="isDisabled" 
                                    @click.prevent="viewLaboratoryHPP(item.registration_id)"
                                    class="mr-1">
                                    <feather-icon icon="ViewIcon" />
                                    {{ item.total_hpp_laboratorium | currency}}
                                </b-button>
                            </td>
                            <td>
                                <b-button 
                                    size="sm" 
                                    variant="outline-primary" 
                                    :disabled="isDisabled" 
                                    @click.prevent="viewRadiologyHPP(item.registration_id)"
                                    class="mr-1">
                                    <feather-icon icon="ViewIcon" />
                                    {{ item.total_hpp_radiology | currency}}
                                </b-button>
                            </td>
                            <td>{{ item.overhead | currency}}</td>
                            <td>{{ item.jm | currency}}</td>
                            <td>
                                <b-button 
                                    size="sm" 
                                    variant="outline-primary" 
                                    :disabled="isDisabled" 
                                    @click.prevent="viewDiagnosa(item.registration_id,item)"
                                    class="mr-1">
                                    <feather-icon icon="ViewIcon" />
                                    {{ item.desc_inacbg }}
                                </b-button>
                            </td>
                            <td>{{ item.total_hpp | currency}}</td>
                            <td>{{ item.cost_inacbg | currency}}</td>
                            <td>
                                <span 
                                    :style="{
                                        color: getColor(item.total_hpp, item.cost_inacbg),
                                        fontWeight: 'bold'
                                    }"
                                >
                                    {{ getStatus(item.total_hpp, item.cost_inacbg) }}
                                </span>
                            </td>
                            <td>{{ item.treatment_done }}</td>
                        </tr>
                    </table>
                </div>
                <!-- <b-button @click="saveValidation" variant="primary" class="mr-2 mt-3">
                    <feather-icon icon="SaveIcon" /> Selesai Validasi
                </b-button> -->
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
                <b-modal v-model="active.PrescriptionHPP" scrollable size="lg" title="HPP Farmasi" hide-footer>
                    <div class="table-container">
                        <table class="table custom-table table-bordered table-striped">
                            <thead class="text-center text-bold-14">
                                <tr>
                                    <th>RESEP</th>
                                    <th>TGL</th>
                                    <th>KATALOG</th>
                                    <th>HPP</th>
                                    <th>QTY</th>
                                    <th>TOTAL HPP</th>
                                </tr>
                            </thead>
                            <tr>
                                <td><label><b></b></label></td>
                                <!-- <td colspan="2" class="text-right"><b>TOTAL</b></td>
                                <td class="text-right">
                                    {{ total_medical_fee | currency }}
                                </td> -->
                            </tr>
                            <tr class="item" v-for="(item, index) in prescription_item" v-if="item.qty > 0">
                                <td>{{ item.no_order }} </td>
                                <td>{{ item.date_added }} </td>
                                <td>{{ item.pharmacy_catalog_name }}</td>
                                <td>{{ item.var_3 | currency}}</td>
                                <td>{{ item.qty }}</td>
                                <td>{{ item.total_hpp | currency}}</td>
                            </tr>
                        </table>
                    </div>
                </b-modal>
                <b-modal v-model="active.LaboratoryHPP" scrollable size="lg" title="HPP Lab" hide-footer>
                    <div class="table-container">
                        <table class="table custom-table table-bordered table-striped">
                            <thead class="text-center text-bold-14">
                                <tr>
                                    <th>TGL</th>
                                    <th>PEMERIKSAAN</th>
                                    <th>KATALOG</th>
                                    <th>HPP</th>
                                    <th>QTY</th>
                                    <th>TOTAL HPP</th>
                                </tr>
                            </thead>
                            <tr>
                                <td><label><b></b></label></td>
                                <!-- <td colspan="2" class="text-right"><b>TOTAL</b></td>
                                <td class="text-right">
                                    {{ total_medical_fee | currency }}
                                </td> -->
                            </tr>
                            <tr class="item" v-for="(item, index) in laboratory_item" v-if="item.qty > 0">
                                <td>{{ item.date_request }} </td>
                                <td>{{ item.package_name }} </td>
                                <td>{{ item.pharmacy_catalog_name }}</td>
                                <td>{{ item.hpp | currency}}</td>
                                <td>{{ item.qty }}</td>
                                <td>{{ item.total | currency}}</td>
                            </tr>
                        </table>
                    </div>
                </b-modal>
                <b-modal v-model="active.RadiologyHPP" scrollable size="lg" title="HPP Radiologi" hide-footer>
                    <div class="table-container">
                        <table class="table custom-table table-bordered table-striped">
                            <thead class="text-center text-bold-14">
                                <tr>
                                    <th>TGL</th>
                                    <th>PEMERIKSAAN</th>
                                    <th>KATALOG</th>
                                    <th>HPP</th>
                                    <th>QTY</th>
                                    <th>TOTAL HPP</th>
                                </tr>
                            </thead>
                            <tr>
                                <td><label><b></b></label></td>
                                <!-- <td colspan="2" class="text-right"><b>TOTAL</b></td>
                                <td class="text-right">
                                    {{ total_medical_fee | currency }}
                                </td> -->
                            </tr>
                            <tr class="item" v-for="(item, index) in radiology_item" v-if="item.qty > 0">
                                <td>{{ item.date_request }} </td>
                                <td>{{ item.package_name }} </td>
                                <td>{{ item.pharmacy_catalog_name }}</td>
                                <td>{{ item.hpp | currency}}</td>
                                <td>{{ item.qty }}</td>
                                <td>{{ item.total | currency}}</td>
                            </tr>
                        </table>
                    </div>
                </b-modal>
                <b-modal v-model="active.Diagnosa" scrollable size="lg" title="Coding" hide-footer>
                    <div class="table-container">
                        <b-table-simple responsive bordered small class="mt-12 text-center" style="font-size: 14px;">
                            <b-thead>
                                <b-tr>
                                    <b-th style="width: 60px;">No</b-th>
                                    <b-th>ICD10</b-th>
                                    <b-th style="width: 60px;">Aksi</b-th>
                                </b-tr>
                            </b-thead>
                            <draggable
                                v-model="form_diagnosis.icd10"
                                tag="tbody"
                                handle=".drag-handle"
                                :animation="200"
                            >
                                <b-tr v-for="(row, index) in diagnosa_item.icd10" :key="row.id">
                                    <b-td>
                                        <span class="drag-handle" style="cursor:move;">&#9776;</span> {{ index + 1 }}
                                    </b-td>
                                    <b-td class="text-left">{{ row.code }} - {{ row.name }}</b-td>
                                    <b-td>
                                        <b-button variant="danger-600" size="sm" @click="delete10(index)" class="mr-2">
                                            <feather-icon icon="TrashIcon" />
                                        </b-button>
                                    </b-td>
                                </b-tr>
                            </draggable>
                        </b-table-simple>
                    </div>
                    <div class="table-container">
                        <b-table-simple responsive bordered small class="mt-12 text-center" style="font-size: 14px;">
                            <b-thead>
                                <b-tr>
                                    <b-th style="width: 60px;">No</b-th>
                                    <b-th>ICD9cm</b-th>
                                    <b-th style="width: 60px;">Aksi</b-th>
                                </b-tr>
                            </b-thead>
                            <draggable
                                v-model="form_diagnosis.icd9cm"
                                tag="tbody"
                                handle=".drag-handle"
                                :animation="200"
                            >
                                <b-tr v-for="(row, index) in diagnosa_item.icd9cm" :key="row.id">
                                    <b-td>
                                        <span class="drag-handle" style="cursor:move;">&#9776;</span> {{ index + 1 }}
                                    </b-td>
                                    <b-td class="text-left">{{ row.code }} - {{ row.name }}</b-td>
                                    <b-td>
                                        <b-button variant="danger-600" size="sm" @click="delete9cm(index)" class="mr-2">
                                            <feather-icon icon="TrashIcon" />
                                        </b-button>
                                    </b-td>
                                </b-tr>
                            </draggable>
                        </b-table-simple>
                    </div>
                    <div class="row">
                        <div class="col-12 mt-8">
                            <label class="fs-13 mb-0">FOLLOW-UP</label>
                            <textarea v-model.lazy="form_data.treatment_done" rows="5" class="form-control form-control-sm" autocomplete="off" />
                        </div>
                    </div>
                    <div class="col-3 pl-0 mt-4">
                        <b-button @click="saveCoding()" size="lg" variant="primary">
                            <feather-icon icon="SaveIcon" /> Update
                        </b-button>
                    </div>
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
import draggable from 'vuedraggable';
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
import prescription from '@/store/pharmacy/prescription'

export default {
    components: { Layout, topMenu, vSelect, DatePicker, ToastificationContent, PaymentInvoiceComponent,draggable },
    data() {
        return {
            form_diagnosis: {icd10: {}},
            selectAll: false,
            prescription_item:[],
            laboratory_item:[],
            radiology_item:[],
            diagnosa_item:[],
            selectedItems: [], // array berisi registration_id yang dicentang
            urlRoot: process.env.VUE_APP_ROOT_API,
            purpose:'',
            form_data : {
                treatment_done:'',
            },
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
                PrescriptionHPP:false,
                LaboratoryHPP:false,
                RadiologyHPP:false,
                Diagnosa:false,
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
                date_start: moment(new Date()).format('YYYY-MM-DD'),
                date_end: moment(new Date()).format('YYYY-MM-DD'),
                // date_start: '2025-08-01',
                // date_end: '2025-08-31',
                // date_start: '2025-11-10',
                // date_end: '2025-11-10',
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
                this.$store.dispatch('dashboard/getMargin',this.params)
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.$store.dispatch('dashboard/getMargin',this.params)
            }
        },
        'params.health_facility_id' (value) {
            if(value){
                return this.$store.dispatch('healthfacilityitem/getParam', value)
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
        this.$store.dispatch('healthfacilityitem/getParam', '40f06534-07ec-4221-ad9d-dea08196079f')
        // this.$store.dispatch('dashboard/getMargin',this.params)
        this.$store.dispatch('healthfacility/getParam', 'healthfacility')
        this.$store.dispatch('helper/getParam', 'type_how_to_enter')
        this.$store.dispatch('helper/getParam', 'sex')
        this.$store.dispatch('helper/getParam', 'is_new')
        this.show = true
    },
    methods: {
        getRatio(hpp, ina) {
            if (!ina || ina == 0) return 0;
            return hpp / ina;
        },

        getStatus(hpp, ina) {
            const ratio = this.getRatio(hpp, ina);

            if (ratio < 0.40) return "(<40%)";
            if (ratio < 0.70) return "(<70%)";
            if (ratio < 0.80) return "(<80%)";
            if (ratio < 1) return "(<100%)";

            return "(≥100%)";  // default
        },

        getColor(hpp, ina) {
            const ratio = this.getRatio(hpp, ina);

            if (ratio < 0.40) return "green";
            if (ratio < 0.70) return "orange";
            if (ratio < 0.80) return "red";
            if (ratio < 1) return "darkred";

            return "black"; // ratio >= 80%
        },

        mappingArray(data) {
            if (data) {
                this.total_medical_fee = 0
                // for (let index = 0; index < data.length; index++) {
                //     this.total_medical_fee += data[index].medical_fee
                // }
            }
        },
        saveCoding () {
            this.$store.dispatch('dashboard/savediagnosa', this.form_data )
            .then(data => {
                this.form_data.treatment_done = ''
                this.filterData()
                this.notification('Success','CheckIcon','success','Berhasil menyimpan')
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
        viewPrescriptionHPP(e) {
            this.prescription_item = []
            this.active.PrescriptionHPP = true
            this.$store.dispatch('dashboard/getMarginPrescription', e)
            .then(res => {
                this.prescription_item = res.data
                console.log(res)
            },
            err => {
            })
        },
        viewLaboratoryHPP(e) {
            this.laboratory_item = []
            this.active.LaboratoryHPP = true
            this.$store.dispatch('dashboard/getMarginLaboratory', e)
            .then(res => {
                this.laboratory_item = res.data
                console.log(res)
            },
            err => {
            })
        },
        viewRadiologyHPP(e) {
            this.radiology_item = []
            this.active.RadiologyHPP = true
            this.$store.dispatch('dashboard/getMarginRadiology', e)
            .then(res => {
                this.radiology_item = res.data
                console.log(res)
            },
            err => {
            })
        },
        viewDiagnosa(e,item) {
            this.radiology_item = []
            this.form_data.treatment_done = ''
            this.form_data.registration_destination_id = ''
            this.form_data.registration_destination_id = item.id
            this.form_data.treatment_done = item.treatment_done
            this.active.Diagnosa = true
            this.$store.dispatch('dashboard/getDiagnosa', e)
            .then(res => {
                this.diagnosa_item = res.data
                console.log(res)
            },
            err => {
            })
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
            this.$store.dispatch('dashboard/getMargin', this.params)
            .then(res => {
                console.log(res)
                this.totalRows  = res.data.totalRows
                this.items      = res.data
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
                'SEP',
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
                    item.sep,
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
            
            this.$store.dispatch('dashboard/getMargin',this.params)
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
