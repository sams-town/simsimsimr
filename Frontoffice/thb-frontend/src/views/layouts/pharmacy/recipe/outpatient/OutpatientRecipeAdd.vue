<template>
    <div>
        <TopPharmacyMenu selectedMenu="menu6" />
        <!-- <PharmacyReportMenu selectedMenu="menu1" class="mt-32" /> -->
        <b-overlay
            :show="show"
            spinner-variant="primary"
            spinner-type="grow"
            spinner-small
            rounded="sm"
            >
            <section class="card">
                <div class="card-header bg-primary-200 font-weight-bold">PROSES PEMAKAIAN OBAT & ALKES / RESEP DOKTER</div>
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <div class="col-lg-11">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="row mt-12">
                                        <div class="col-lg-3 text-lg-right">
                                            <label>No. Order</label>
                                        </div>
                                        <div class="col-lg">
                                            <b-form-input v-model="form_transaction.no_order" value="PRC/Med/01/2023/00002" readonly/>
                                        </div>
                                    </div>
                                    <div class="row mt-12">
                                        <div class="col-lg-3 text-lg-right">
                                            <label>Apotik</label>
                                        </div>
                                        <div class="col-lg">
                                            <v-select v-model="form_transaction.pharmacy_depo_id" :options="DepoOptions" :reduce="DepoOptions => DepoOptions.value" label="title" placeholder="-- Pilih Depo --"></v-select>
                                        </div>
                                    </div>
                                    <div class="row mt-12">
                                        <div class="col-lg-3 text-lg-right">
                                            <label>Dokter</label>
                                        </div>
                                        <div class="col-lg">
                                            <b-form-input v-model="form_transaction.doctor_in_charge_name" readonly/>
                                            <b-form-input v-model="form_transaction.health_facility_item_name" readonly/>
                                        </div>
                                    </div>
                                    <div class="row mt-12">
                                        <div class="col-lg-3 text-lg-right">
                                            <label>Jenis Resep</label>
                                        </div>
                                        <div class="col-lg">
                                            <b-input-group>
                                                <v-select style="width:100%" v-model="form_transaction.type_prescription_id" @input="typePres()" :options="PrescriptionTypeOptions" :reduce="PrescriptionTypeOptions => PrescriptionTypeOptions.value" label="title" placeholder="Instalasi"></v-select>
                                            </b-input-group>
                                        </div>
                                    </div>
                                    <div class="row mt-12">
                                        <div class="col-lg-3 text-lg-right">
                                            <label>Tanggal</label>
                                        </div>
                                        <div class="col-lg">
                                            <b-input-group>
                                                <b-form-input type="date" v-model="form_transaction.date_added" autocomplete="off"></b-form-input>
                                            </b-input-group>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <!-- <div class="row">
                                        <div class="col-lg-3 text-lg-right">
                                            <label>Gudang</label>
                                        </div>
                                        <div class="col-lg">
                                            <b-input-group>
                                                <b-input-group-prepend is-text checked>
                                                    <b-form-radio type="radio" v-model="form_transaction.pharmacy_depo_id" value="8da5d967-18da-4f87-86dc-0a9416611c11" :checked="true"/>
                                                </b-input-group-prepend>
                                                <b-form-input value="INSTALASI FARMASI" disabled></b-form-input>
                                            </b-input-group>
                                        </div>
                                    </div> -->
                                    <div class="row mt-12" v-if="form_transaction.type_prescription_id !== 5">
                                        <div class="col-lg-3 text-lg-right">
                                            <label>Pasien</label>
                                        </div>
                                        <div class="col-lg">
                                            <b-input-group>
                                                <v-select style="width:100%" v-model="health_facility_id" readonly :options="OptionsHealthFacility" :reduce="OptionsHealthFacility => OptionsHealthFacility.value" label="title" placeholder="Instalasi"></v-select>
                                                <!-- <b-form-input style="width:50%" v-model="form_transaction.patient_name" readonly autocomplete="off"></b-form-input> -->
                                            </b-input-group>
                                            <b-input-group>
                                                <b-form-input v-model="form_transaction.norm" readonly autocomplete="off"></b-form-input>
                                                <v-select :disabled="is_search" style="width:80%" @input="onAddReg($event)" v-model="form_transaction.registration_destination_id" readonly :options="RegistRanapOptions" :reduce="RegistRanapOptions => RegistRanapOptions.value" label="title" placeholder="Cari Pasien"></v-select>
                                                <!-- <b-form-input style="width:50%" v-model="form_transaction.patient_name" readonly autocomplete="off"></b-form-input> -->
                                            </b-input-group>
                                            <b-input-group>
                                                <b-form-input v-model="form_transaction.patient_birth_place" readonly autocomplete="off"></b-form-input>
                                                <b-form-input type="date" v-model="form_transaction.patient_birth_date" readonly autocomplete="off"></b-form-input>
                                                <b-form-input :value="form_transaction.registration_destination_id ? parsed_age(form_transaction.patient_birth_date) : '-'" readonly autocomplete="off"></b-form-input>
                                            </b-input-group>
                                            <b-input-group>
                                                <b-form-input v-model="form_transaction.company_name" readonly autocomplete="off"></b-form-input>
                                                <b-form-input v-model="form_transaction.company_name_cob" readonly autocomplete="off"></b-form-input>
                                                <b-form-input style="width:30%" v-model="form_transaction.number_insurance" readonly autocomplete="off"></b-form-input>
                                            </b-input-group>
                                            <b-input-group>
                                                <b-form-input :value="'Berat Badan: ' + `${form_transaction.registration_destination_id && patientTtv && patientTtv.weight ? patientTtv.weight : 0}` + ' kg'" readonly autocomplete="off"></b-form-input>
                                                <b-form-input :value="'Tinggi Badan: ' + `${form_transaction.registration_destination_id && patientTtv && patientTtv.height? patientTtv.height ? patientTtv.height : 0 : 0}` + ' cm'" readonly autocomplete="off"></b-form-input>
                                            </b-input-group>
                                        </div>
                                    </div>
                                    <div class="row mt-12" v-if="form_transaction.type_prescription_id === 5">
                                        <div class="col-lg-3 text-lg-right">
                                            <label>Pasien</label>
                                        </div>
                                        <div class="col-lg">
                                            <b-input-group>
                                                <b-form-input v-model="form_transaction.fullname" placeholder="Nama" autocomplete="off"></b-form-input>
                                            </b-input-group>
                                            <b-input-group>
                                                <v-select style="width:100%" v-model="form_transaction.type_free_id" readonly :options="OptionsFree" :reduce="OptionsFree => OptionsFree.value" label="title" placeholder="Pembeli"></v-select>
                                            </b-input-group>
                                            <!-- <b-input-group>
                                                <b-form-input v-model="form_transaction.phone" placeholder="Nomer Telepon" autocomplete="off"></b-form-input>
                                            </b-input-group>
                                            <b-input-group>
                                                <b-form-input v-model="form_transaction.addess" placeholder="Nomer Telepon" autocomplete="off"></b-form-input>
                                            </b-input-group> -->
                                            <!-- <b-input-group>
                                                <b-form-input v-model="form_transaction.patient_birth_place" readonly autocomplete="off"></b-form-input>
                                                <b-form-input type="date" v-model="form_transaction.patient_birth_date" readonly autocomplete="off"></b-form-input>
                                                <b-form-input :value="form_transaction.registration_destination_id ? parsed_age(form_transaction.patient_birth_date) : '-'" readonly autocomplete="off"></b-form-input>
                                            </b-input-group>
                                            <b-input-group>
                                                <b-form-input v-model="form_transaction.company_name" readonly autocomplete="off"></b-form-input>
                                                <b-form-input v-model="form_transaction.number_insurance" readonly autocomplete="off"></b-form-input>
                                            </b-input-group>
                                            <b-input-group>
                                                <b-form-input :value="'Berat Badan: ' + `${form_transaction.registration_destination_id && patientTtv && patientTtv.weight ? patientTtv.weight : 0}` + ' kg'" readonly autocomplete="off"></b-form-input>
                                                <b-form-input :value="'Tinggi Badan: ' + `${form_transaction.registration_destination_id && patientTtv && patientTtv.height? patientTtv.height ? patientTtv.height : 0 : 0}` + ' cm'" readonly autocomplete="off"></b-form-input>
                                            </b-input-group> -->
                                        </div>
                                    </div>
                                    <div class="row mt-12">
                                        <div class="col-lg-3 text-lg-right">
                                            <label>Total</label>
                                        </div>
                                        <div class="col-lg">
                                            <b-input-group prepend="Nilai">
                                                <b-form-input readonly v-model="form_transaction.var_1_show" class="text-right" />
                                            </b-input-group>
                                            <b-input-group prepend="Pembulatan">
                                                <b-form-input readonly v-model="form_transaction.var_2_show" class="text-right" />
                                            </b-input-group>
                                            <b-input-group prepend="Akhir">
                                                <b-form-input readonly v-model="form_transaction.var_3_show" class="text-right" />
                                            </b-input-group>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="card mt-32">
                <div class="card-header bg-primary-200">
                    <div class="d-flex justify-content-lg-between">
                        <!-- <span class="font-weight-bold">Total : Rp. {{this.form_transaction.nfinish}}</span> -->
                        <div class="d-flex">
                            <b-button variant="primary-600" v-if="form_transaction.flag === 0" @click="submit()" :disabled="isDisabled" size="md" class="mr-1">
                                <feather-icon icon="SaveIcon" /> Proses Resep
                            </b-button>
                            <b-button variant="primary-600" v-if="form_transaction.flag === 1" @click="viewEticket()" size="md" class="mr-1">
                                <feather-icon icon="PrinterIcon" /> E-Tiket
                            </b-button>
                            <b-button variant="primary-600" v-if="form_transaction.flag === 1" @click="viewVerification()" size="md" class="mr-1">
                                <feather-icon icon="PrinterIcon" /> Validasi
                            </b-button>
                            <b-button variant="primary-600" v-if="form_transaction.flag === 1" @click="viewRecipe()" size="md" class="mr-1">
                                <feather-icon icon="PrinterIcon" /> Print Resep
                            </b-button>
                            <b-button variant="primary-600" v-if="form_transaction.flag === 1" @click="viewRecipeCheck()" size="md" class="mr-1">
                                <feather-icon icon="PrinterIcon" /> Print Resep 2
                            </b-button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-responsive table-bordered" responsive small bordered>
                        <thead class="text-center text-bold-14">
                            <tr>
                                <th rowspan="2">
                                    <!-- <b-form-checkbox /> -->
                                </th>
                                <th rowspan="2" style="min-width: 300px;">NAMA BARANG</th>
                                <th colspan="3">ESTIMASI ORDER</th>
                                <th colspan="4">ORDER PROSES</th>
                            </tr>
                            <tr>
                                <th style="min-width: 50px;">SEDIAAN OBAT</th>
                                <th style="min-width: 50px;">DOSIS DOKTER</th>
                                <th style="min-width: 50px;">JUMLAH</th>
                                <th style="min-width: 180px;">KEMASAN</th>
                                <th style="min-width: 220px;">HARGA</th>
                                <th style="min-width: 270px;">TOTAL</th>
                                <th style="min-width: 300px;">KETERANGAN&ATURAN PAKAI</th>
                            </tr>
                        </thead>
                        <template v-for="(item_p, index_p) in form_transaction.form_prescription_parent">
                            <tr>
                                <td colspan="2">
                                    <label><b>{{ item_p.prescription }}</b></label>
                                </td>
                                <td colspan="2" v-if="item_p.id !== 1">
                                    <!-- <b-input-group append="Jumlah">
                                        <b-form-input style="width:150px" type="number" v-model="item_p.value" />
                                    </b-input-group>
                                    <b-input-group append="Aturan Pakai">
                                        <b-form-input style="width:150px" type="text" v-model="item_p.used" />
                                    </b-input-group> -->
                                </td>
                            </tr>
                            <!-- {{ item_p.prescription }} {{ item_p.id }} -->
                            <tr class="item" v-for="(item, index) in form_transaction.form_prescription" v-if="item_p.id === item.prescription_type_id" :style="(item.prescription_type_id !== 1) ? 'background-color:#F8C43E' : ''">
                                <td>
                                    <!-- <b-form-checkbox v-model="item.index" /> -->
                                    <b-button variant="danger-600" size="sm" @click="deletePRES(index)" class="mr-2">
                                        <feather-icon icon="TrashIcon" />
                                    </b-button>
                                </td>
                                <td>
                                    {{ item.name }}<hr>
                                    <!-- <b-button @click="changeCatalog(item)" variant="primary-600" size="sm">
                                        <feather-icon icon="RefreshCcwIcon" /> Ganti Katalog
                                    </b-button>&nbsp;
                                    <b-button v-if="item.pharmacy_catalog_id_sub" @click="viewChangeLog(item)" variant="primary-600" size="sm">
                                        <feather-icon icon="InfoIcon" /> 
                                    </b-button> -->
                                    <label><b>STOK : {{ item.stock }}</b></label>
                                </td>
                                <td class="text-right">{{item.sum_dose}}</td>
                                <td class="text-right">
                                    <template v-if="item.prescription_type_id === 1">
                                        {{item.value_doctor}} {{item.package_name}} 
                                    </template>
                                    <template v-if="item.prescription_type_id !== 1">
                                        {{item.value_doctor}} {{item.dose_name}} 
                                    </template>
                                </td>
                                <!-- <td>
                                    <v-select v-model="item.package_id" readonly :options="PackageOptions" :reduce="PackageOptions => PackageOptions.value" label="title" placeholder="Kemasan"></v-select>
                                </td> -->
                                <td>
                                    <b-form-input style="width:100px" type="number" @change="vChanged($event, index)" v-model="item.qty" />
                                </td>
                                <td>
                                    <v-select v-model="item.package_id" readonly :options="PackageOptions" :reduce="PackageOptions => PackageOptions.value" label="title" placeholder="Kemasan"></v-select>
                                </td>
                                <td>
                                    <b-form-input type="text" readonly v-model="item.var_1_show" />
                                </td>
                                <td>
                                    <b-form-input type="text" readonly v-model="item.var_2_show" />
                                </td>
                                <td>
                                    <b-input-group append="Keterangan">
                                        <b-form-input style="width:150px" type="text" v-model="item.desc" />
                                    </b-input-group>
                                    <b-input-group append="Aturan Pakai" v-if="item.prescription_type_id === 1">
                                        <b-form-input style="width:150px" type="text" v-model="item.used" />
                                    </b-input-group>
                                </td>
                                <!-- <td>
                                    <b-button variant="danger-600" size="sm" @click="deleteSC(item.index+1)" class="mr-2">
                                        <feather-icon icon="TrashIcon" />
                                    </b-button>
                                </td> -->
                            </tr>
                        </template>
                        <tr>
                            <td></td>
                            <td colspan="5">
                                <v-select v-model="catalog" @input="onAddCAT($event)" :options="CatalogOptions" :reduce="CatalogOptions => CatalogOptions" label="title" placeholder="-- Pilih Katalog --"></v-select>
                            </td>
                            <div>&nbsp;</div>
                            <div>&nbsp;</div>
                            <div>&nbsp;</div>
                            <div>&nbsp;</div>
                            <div>&nbsp;</div>
                            <div>&nbsp;</div>
                            <div>&nbsp;</div>
                        </tr>
                        <!-- <tr>
                            <td colspan="7">&nbsp;</td>
                            <td><b-form-input type="text" v-model="form_transaction.var_3_show" readonly/></td>
                        </tr> -->
                    </table>
                </div>
            </section>
            <section class="card mt-32">
                <div class="card-header bg-primary-200">
                    <div class="d-flex justify-content-lg-between">
                        <span class="font-weight-bold">VALIDASI RESEP</span>
                        <div class="d-flex">
                            <!-- <b-button variant="primary-600" size="sm" @click.prevent="validationForm" class="mr-1">
                                <feather-icon icon="SaveIcon" />
                            </b-button>
                            <b-button variant="primary-600" size="sm">
                                <feather-icon icon="RefreshCcwIcon" />
                            </b-button> -->
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-responsive table-bordered" responsive small bordered>
                        <thead class="text-center text-bold-14">
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">Jenis Pengecekan</th>
                                <th>Ya</th>
                                <th>Tidak</th>
                                <th rowspan="2">Keterangan/Tindak Lanjut</th>
                            </tr>
                            <tr>
                                <th><b-form-radio v-model="verall" @change="verificationAll($event)" :value="1" /></th>
                                <th><b-form-radio v-model="verall" @change="verificationAll($event)" :value="0" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="item" v-for="(item, index) in form_transaction_verification">
                                <td>
                                    {{ item.value }}
                                </td>
                                <td>
                                    {{ item.title }}
                                </td>
                                <td>
                                    <b-form-radio v-model="item.yn" @click="verification()" :value="1" />
                                </td>
                                <td>
                                    <b-form-radio v-model="item.yn" @click="verification()" :value="0" />
                                </td>
                                <td>
                                    <b-form-input v-model="item.notes"/>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </b-overlay>
        <b-modal v-model="open_modal.change" title="Ganti Katalog" hide-footer>
            <div>
                <div class="mt-12">
                    <label class="c-form-label">Dokter</label>
                    <b-form-input v-model="form_transaction.doctor_in_charge_name" readonly/>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Nama Obat/Katalog Awal</label>
                    <v-select v-model="form_transaction_subs.pharmacy_catalog_id_ref" disabled :options="CatalogOptions" :reduce="CatalogOptions => CatalogOptions.value" label="title" placeholder="-- Pilih Katalog --"></v-select>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Nama Obat/Katalog Pengganti</label>
                    <v-select v-model="form_transaction_subs.pharmacy_catalog_id_sub" :options="CatalogOptions" :reduce="CatalogOptions => CatalogOptions.value" label="title" placeholder="-- Pilih Katalog --"></v-select>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Aturan Pakai</label>
                    <b-form-input v-model="form_transaction_subs.used"/>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Alasan Subtitusi Obat</label>
                    <b-textarea v-model="form_transaction_subs.notes"></b-textarea>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Petugas Farmasi</label>
                    <b-form-input v-model="form_transaction_subs.user" readonly/>
                </div>
                <div class="d-flex justify-content-end mt-24">
                    <b-button variant="indigo-500" @click.prevent="validationChangeCatalog" class="px-3">Ganti</b-button>
                </div>
            </div>
        </b-modal>
    </div>
</template>

<script>
import moment from 'moment'
import TopPharmacyMenu from '@/@core/components/top-menu/TopPharmacyMenu.vue';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import vSelect from 'vue-select'
// import PharmacyReportMenu from '@/@core/components/tab-menu/PharmacyReportMenu.vue';
import { mapState } from 'vuex'
import router from '@/router/index.js'

export default {
    name: 'OutpatientRecipeDetail',
    components: {
        TopPharmacyMenu, vSelect, //PharmacyReportMenu, 
    },
    data() {
        return {
            // health_facility_id:'40f06534-07ec-4221-ad9d-dea08196079f',
            health_facility_id:'',
            isDisabled: false,
            prescData:{},
            verall:0,
            open_modal: {
                change: false,
            },
            active: {
                tab: 'tab1',
                modal_filter: false,
                report_format: '1',
                recipe_modal: false,
            },
            FilterForm: {
                installation: null,
                datePick: {
                    start: null,
                    end: null
                },
            },
            ReportForm: {
                datePick: {
                    start: null,
                    end: null
                },
            },
            params: {
                limit: 20,
                offset: 0,
                patient_name: '',
                norm: '',
                registration_destination_id:'',
                pharmacy_depo_id: '',
                date_start: moment(new Date()).format('YYYY-MM-DD'),
                date_end: '',
                name: '',
                health_facility_item_id: '',
                doctor_in_charge_id: '',
            },
            currentPage:1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            isDisabled: false,
            isDisabledFilter: false,
            show: false,
            is_search:false,
            OptionsFree:[
                { value: 1, title: 'Pasien' },
                { value: 2, title: 'Karyawan' },
            ],
            PrescriptionTypeOptions:[
                { value: 1, title: 'Resep' },
                { value: 2, title: 'Resep Pulang' },
                { value: 3, title: 'BHP' },
                { value: 4, title: 'DO' },
                { value: 5, title: 'Pembelian Bebas' }
            ],
            PrescriptionOptions:[
                { value: 1, title: 'OBAT SATUAN' },
                // { value: 2, title: 'RACIK 1' },
                // { value: 3, title: 'RACIK 2' },
                // { value: 4, title: 'RACIK 3' },
                // { value: 5, title: 'RACIK 4' }
            ],
            form_transaction_verification: [
                { value: 1, title: 'Identitas Pasien', yn:0, notes:'' },
                { value: 2, title: 'Nama Dokter Penulis Resep', yn:0, notes:'' },
                { value: 3, title: 'Kesesuaian Obat', yn:0, notes:'' },
                { value: 4, title: 'Jumlah Obat yang diberikan / yang ada', yn:0, notes:'' },
                { value: 5, title: 'Benar Dosis', yn:0, notes:'' },
                { value: 6, title: 'Benar rute', yn:0, notes:'' },
                { value: 7, title: 'Benar waktu pemberian', yn:0, notes:'' },
                { value: 8, title: 'Ketepatan aturan pemakaian', yn:0, notes:'' },
            ],
            form_transaction: {
                id:'',
                view:'add',
                soap_id:'',
                pharmacy_depo_id:'',
                type_prescription_id:1,
                date_added: moment(new Date()).format('YYYY-MM-DD'),
                norm:'',
                fullname:'',
                type_free_id:1,
                flag:0,
                health_facility_item_id:'',
                form_prescription:[],
                form_prescription_parent:[{
                    id:1,
                    prescription:'Obat Satuan',
                    used:'',
                    value:0
                }]
            },
            form_transaction_subs: {
                id:'',
                pharmacy_catalog_id_ref:'',
                pharmacy_catalog_id_sub:'',
                doctor_in_charge_id:'',
                user   :'d77142c4-4777-47d8-bd0c-fd7dfd134af4',
                notes   :'',
                used   :''
            },
            catalog:'',
            prescription:1,
            prescription_name:'OBAT SATUAN',
            patientTtv: {},
        }
    },
    filters:{
        currency(value) {
            return new Intl.NumberFormat("en-US", { style: "currency", currency: "IDR" }).format(value)
        },
        numberToDecimal(value) {
            let val = (value/1).toFixed(0).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },
        numberToDecimalCurr(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },
        decimalToNumber(value) {
            const val = value
                .replace(/(kr|\$|£|€)/g, '') // getting rid of currency
                .trim()
                .replace(/(.+)[.,](\d+)$/g, "$1x$2") // stripping number into $1: integer and $2: decimal part and putting it together with x as decimal point
                .replace(/[.,]/g, '') // getting rid of . AND ,
                .replace('x', '.'); // replacing x with .
            return parseFloat(val);
        }
    },
    computed: {
        ...mapState({ 
            OptionsPharmacyDepo: state => state.depo.options.depo,
            CatalogOptions: state => state.catalog.options.catalog,
            OptionsHealthFacility: state => state.healthfacility.options.healthfacility,
            RegistRanapOptions:  state => state.registration.options.ranap,
            PackageOptions: state => state.packages.options.packages,
            DepoOptions: state => state.depo.options.depo, 
            items : state => state.soap.items,
        }),
        // outpatient - tab1, tab2, tab3
        firstRow() {
            return (this.OutpatientTable.currentPage - 1) * this.OutpatientTable.perPage + 1
        },
        lastRow() {
            return Math.min(this.OutpatientTable.currentPage * this.OutpatientTable.perPage, this.OutpatientTable.totalRows)
        }
    },
  watch: {
    'items': function(val){
        if (val) {
            this.patientTtv = val[0]
            // console.log('ttv', this.patientTtv);
        }
    },
    health_facility_id (val) {
        this.is_search = true
        this.form_transaction.registration_destination_id = ''
        if(val){
            return this.$store.dispatch('registration/getRanapAll',{health_facility_id:val,date_start:moment(this.form_transaction.date_added).format('YYYY-MM-DD'),date_end:moment(this.form_transaction.date_added).format('YYYY-MM-DD')}).then(res => {this.is_search = false})
        }
    },
    'form_transaction.date_added'(val) {
      if (val) {
        return this.$store.dispatch('registration/getRanapAll',{health_facility_id:this.health_facility_id,date_start:moment(val).format('YYYY-MM-DD'),date_end:moment(val).format('YYYY-MM-DD')})
      }
    },
  },
    methods: {
        typePres() {
            if (form_transaction.type_prescription_id === 5) {

            }
        },
        parsed_age(input_date) {
            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const currentDate = moment();

            const years = currentDate.diff(birthDate, 'years');
            birthDate.add(years, 'years');
            const months = currentDate.diff(birthDate, 'months');
            birthDate.add(months, 'months');
            const days = currentDate.diff(birthDate, 'days');

            return `${years} th ${months} bln ${days} hr`;
        },
        deletePRES (index) {
            this.form_transaction.form_prescription.splice(index, 1)
        },
        onAddCAT (event) {
            this.catalog = ''
            this.collation = ''
            let prescription_type = {value:1,title:'OBAT SATUAN'}
            this.prescription = prescription_type
            this.prescription_type = prescription_type
            if (this.prescription === '') {
                alert("Pilih Racikan!");
            } else {
                this.$store.dispatch('prescription/geCatalogItem', {id:event.data_catalog.id,pharmacy_depo_id:this.form_transaction.pharmacy_depo_id}).then(res => {
                    console.log(res)
                    this.form_transaction.form_prescription.push(
                        { 
                            code:event.data_catalog.code,
                            name:event.data_catalog.name,
                            pharmacy_catalog_id:event.data_catalog.id, 
                            package_id:event.data_catalog.package_id,
                            dose:event.data_catalog.dose,
                            sum_dose:event.data_catalog.dose+' '+event.data_catalog.dose_name, 
                            package_name:event.data_catalog.package_name,
                            prescription_type_id:this.prescription_type.value,
                            prescription_type_name:this.prescription_type.title,
                            hja:res.hja,
                            hp:res.hp,
                            hna:res.hna,
                            stock:res.stock,
                            var_1:res.hja,
                            var_3:res.hpp,
                            var_2:res.hja*0,
                            var_1_show:this.currency(res.hja),
                            var_2_show:this.currency(res.hja*0),
                            sort:this.form_transaction.form_prescription.length
                        }
                    )
                })
            }
        },
        currency(value) {
            return new Intl.NumberFormat("en-US", { style: "currency", currency: "IDR" }).format(value)
        },
        verification () {
            console.log('test')
            localStorage.setItem('verification', JSON.stringify(this.form_transaction_verification))
            // transferdata: JSON.parse(localStorage.getItem('transferdata')),
        },
        verificationAll (value) {
            this.form_transaction_verification = []
            if (value === 1) {
                this.form_transaction_verification = [
                    { value: 1, title: 'Identitas Pasien', yn:1, notes:'' },
                    { value: 2, title: 'Nama Dokter Penulis Resep', yn:1, notes:'' },
                    { value: 3, title: 'Kesesuaian Obat', yn:1, notes:'' },
                    { value: 4, title: 'Jumlah Obat yang diberikan / yang ada', yn:1, notes:'' },
                    { value: 5, title: 'Benar Dosis', yn:1, notes:'' },
                    { value: 6, title: 'Benar rute', yn:1, notes:'' },
                    { value: 7, title: 'Benar waktu pemberian', yn:1, notes:'' },
                    { value: 8, title: 'Ketepatan aturan pemakaian', yn:1, notes:'' }
                ]
            } else {
                this.form_transaction_verification = [
                    { value: 1, title: 'Identitas Pasien', yn:0, notes:'' },
                    { value: 2, title: 'Nama Dokter Penulis Resep', yn:0, notes:'' },
                    { value: 3, title: 'Kesesuaian Obat', yn:0, notes:'' },
                    { value: 4, title: 'Jumlah Obat yang diberikan / yang ada', yn:0, notes:'' },
                    { value: 5, title: 'Benar Dosis', yn:0, notes:'' },
                    { value: 6, title: 'Benar rute', yn:0, notes:'' },
                    { value: 7, title: 'Benar waktu pemberian', yn:0, notes:'' },
                    { value: 8, title: 'Ketepatan aturan pemakaian', yn:0, notes:'' }
                ]
            }
            localStorage.setItem('verification', JSON.stringify(this.form_transaction_verification))
            // transferdata: JSON.parse(localStorage.getItem('transferdata')),
        },
        viewVerification (data) {
            localStorage.setItem('verification', JSON.stringify(this.form_transaction_verification))
            router.push({ name: 'PharmacyRecipeVerificationView', params: { id: this.form_transaction.id }  })
        },
        viewEticket (data) {
            localStorage.setItem('verification', JSON.stringify(this.form_transaction_verification));
            const url = this.$router.resolve({ name: 'PharmacyRecipeEticketView', params: { id: this.form_transaction.id } }).href;
            window.open(url, '_blank');
            // localStorage.setItem('verification', JSON.stringify(this.form_transaction_verification))
            // router.push({ name: 'PharmacyRecipeEticketView', params: { id: this.form_transaction.id }  })
        },
        viewRecipe (data) {
            localStorage.setItem('verification', JSON.stringify(this.form_transaction_verification));
            const url = this.$router.resolve({ name: 'PharmacyRecipePrintView', params: { id: this.form_transaction.id } }).href;
            window.open(url, '_blank');
            // localStorage.setItem('verification', JSON.stringify(this.form_transaction_verification))
            // router.push({ name: 'PharmacyRecipePrintView', params: { id: this.form_transaction.id }  })
        },
        viewRecipeCheck (data) {
            localStorage.setItem('verification', JSON.stringify(this.form_transaction_verification));
            const url = this.$router.resolve({ name: 'PharmacyRecipeCheckPrintView', params: { id: this.form_transaction.id } }).href;
            window.open(url, '_blank');
        },
        viewChangeLog (data) {
            router.push({ name: 'PharmacyRecipeSubstitutionView', params: { id: this.form_transaction.id, pharmacy_catalog_id: data.pharmacy_catalog_id }  })
        },
        changeCatalog (data) {
            this.open_modal.change = !this.open_modal.change
            this.form_transaction_subs.id = this.form_transaction.id
            this.form_transaction_subs.doctor_in_charge_id = this.form_transaction.doctor_in_charge_id
            this.form_transaction_subs.pharmacy_catalog_id_ref = data.pharmacy_catalog_id
            this.form_transaction_subs.used = data.used
        },
        onAddReg () {
			this.$store.dispatch('registration/getById', this.form_transaction.registration_destination_id).then(data => {
                let regData = data[0]
                this.form_transaction.patient_name = regData.name
                this.form_transaction.norm = regData.norm
                // this.form_transaction.pharmacy_depo_id = '297c44f0-12ef-40e6-b9a7-c831065b3372'
                // this.form_transaction.date_added = moment(new Date()).format('YYYY-MM-DD')
                this.form_transaction.health_facility_item_id = regData.health_facility_item_id
                this.form_transaction.doctor_in_charge_id = regData.doctor_in_charge_id
                this.form_transaction.depo_name = 'Instalasi Farmasi'
                this.form_transaction.patient_birth_place = regData.birth_place
                this.form_transaction.patient_birth_date = regData.birth_date
                this.form_transaction.doctor_in_charge_name = regData.doctor_in_charge_name
                this.form_transaction.health_facility_item_name = regData.health_facility_item_name
                this.form_transaction.company_name = regData.registration_insurance[0].company_name
                this.form_transaction.number_insurance = regData.registration_insurance[0].number_insurance
                this.form_transaction.insurance_name = regData.registration_insurance[0].insurance_name
                // this.form_nurse.employee_nurse_id = this.userData.employee_id
                // this.form_nurse.registration_destination_id = id
                // this.show = false

                // console.log('regData.norm', regData.norm);
                this.$store.dispatch('soap/getByRM', regData.norm)

            })
        },
        getData (id) {
			this.$store.dispatch('prescription/getById', id).then(data => {
                this.prescData = data[0]
                this.openPrescription(this.prescData)
                // this.form_nurse.employee_nurse_id = this.userData.employee_id
                // this.form_nurse.registration_destination_id = id
                // this.show = false
            })
        },
        openPrescription (data) {
            this.form_transaction.form_prescription_parent = []
            this.form_transaction.form_prescription = []
            this.form_transaction.id = data.id
            this.form_transaction.norm = data.norm
            this.form_transaction.date_added = moment(data.date_added).format('YYYY-MM-DD')
            this.form_transaction.no_order = data.no_order
            this.form_transaction.flag = data.flag
            this.form_transaction.var_1_show = this.currency(data.var_1)
            this.form_transaction.var_2_show = this.currency(data.var_2)
            this.form_transaction.var_3_show = this.currency(data.var_3)
            this.form_transaction.doctor_in_charge_id = data.doctor_in_charge_id
            this.form_transaction.doctor_in_charge_name = data.doctor_in_charge_name
            this.form_transaction.health_facility_item_name = data.health_facility_item_name
            this.form_transaction.patient_name = data.patient_name
            this.form_transaction.patient_birth_date = data.patient_birth_date
            this.form_transaction.patient_birth_place = data.patient_birth_place
            this.form_transaction.company_name = data.company_name
            this.form_transaction.insurance_name = data.insurance_name
            let prescription_type = {value:1,title:'OBAT SATUAN'}
            this.prescription = prescription_type
            this.prescription_type = prescription_type
            this.$store.dispatch('prescription/getPrescriptionItem', data.id).then(res => {
                for (let index = 0; index < res.length; index++) {
                    this.form_transaction.form_prescription.push(
                        { 
                            code:res[index].pharmacy_catalog_code,
                            name:res[index].pharmacy_catalog_name,
                            pharmacy_catalog_id:res[index].pharmacy_catalog_id, 
                            pharmacy_catalog_id_sub:res[index].pharmacy_catalog_id_sub, 
                            package_id:res[index].package_id,
                            dose:res[index].dose,
                            sum_dose:res[index].dose+' '+res[index].dose_name, 
                            dose_name:res[index].dose_name,
                            package_name:res[index].package_name,
                            prescription_type_id:res[index].prescription_item_type_id,
                            prescription_type_name:this.prescription_type.title,
                            hja:res[index].hja,
                            hp:res[index].hp,
                            hna:res[index].hna,
                            stock:res[index].stock,
                            var_1:res[index].hja,
                            var_3:res[index].hpp,
                            var_2:res[index].hja*res[index].qty,
                            var_1_show:this.currency(res[index].hja),
                            var_2_show:this.currency(res[index].hja*res[index].qty),
                            value_doctor:res[index].qty_doctor,
                            qty:res[index].qty,
                            used:res[index].used,
                            desc:res[index].desc,
                            sort:res[index].sort, 
                        }
                    )
                }
                this.form_transaction.form_prescription_parent.push({
                    id:1,
                    prescription:'Obat Satuan',
                    used:'',
                    value:0
                },{
                    id:2,
                    prescription:'Racik 1',
                    used:'',
                    value:0
                },{
                    id:3,
                    prescription:'Racik 2',
                    used:'',
                    value:0
                },{
                    id:4,
                    prescription:'Racik 3',
                    used:'',
                    value:0
                },{
                    id:5,
                    prescription:'Racik 4',
                    used:'',
                    value:0
                }) 
                this.sumAll()
            })
            this.form_transaction.health_facility_item_id = data.health_facility_item_id
            this.form_transaction.pharmacy_depo_id = data.pharmacy_depo_id
            this.form_transaction.soap_id = data.soap_id
            this.form_transaction.view = 'edit'
        },
        vChanged($event, index){
            let qty = $event
            let var_1 = this.form_transaction.form_prescription[index].var_1
            this.form_transaction.form_prescription[index].var_2 = qty * var_1
            this.form_transaction.form_prescription[index].var_2_show = this.currency(qty * var_1)
            this.sumAll()
        },
        sumAll() {
            let ntotal = 0
            let ndiscount = 0
            let nfinish = 0
            this.form_transaction.form_prescription.forEach(element => {
                ntotal += element.var_2
                // ndiscount += element.discount_price
                nfinish += element.var_2
            })
            this.form_transaction.var_1 = ntotal
            this.form_transaction.var_2 = ndiscount
            this.form_transaction.var_3 = nfinish
            this.form_transaction.var_1_show = this.$options.filters.currency(ntotal)
            this.form_transaction.var_2_show = this.$options.filters.currency(ndiscount)
            this.form_transaction.var_3_show = this.$options.filters.currency(nfinish)
        },
        validationFormTransaction (id) {
            // this.$refs.simpleRules.validate()
            // .then(success => {
            //     if (success) {
                const formData = this.form_transaction
                this.$store.dispatch('soap/formPostPut', formData)
                .then(res => {
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.$store.dispatch('soap/getSoapDoctorById', this.$route.params.id)
                    this.$store.dispatch('soap/getSoapPrescriptionById', this.$route.params.id)
                }, 
                err => {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                })
                // } else {
                // this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                // }
            // })
        },
        submit () {
            // this.$refs.simpleRules.validate()
            // .then(success => {
                //     if (success) {
                this.isDisabled = true
                const formData = this.form_transaction
                this.$store.dispatch('prescription/formPostPutAdd', formData)
                .then(res => {
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.getData(this.$route.params.id)
                    this.$store.dispatch('prescription/getOrder',this.params)
                    this.isDisabled = false
                }, 
                err => {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    this.isDisabled = false
                })
                // } else {
                // this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                // }
            // })
        },
        validationChangeCatalog () {
            // this.$refs.simpleRules.validate()
            // .then(success => {
            //     if (success) {
                const formData = this.form_transaction_subs
                this.$store.dispatch('prescription/formPostPutSubs', formData)
                .then(res => {
                    this.open_modal.change = !this.open_modal.change
                    this.$store.dispatch('prescription/getById', this.form_transaction.id).then(data => {
                        this.openPrescription(this.form_transaction)
                    })
                    this.notification('Success','CheckIcon','success','You have successfully')
                }, 
                err => {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                })
                // } else {
                // this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                // }
            // })
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        }
    },
    mounted () {
        // if (this.$route.params.id) {
        //     this.form_transaction.id = this.$route.params.id
        //     this.getData(this.$route.params.id)
        // }
    },
    created () {
        this.$store.dispatch('healthfacility/getParam', 'healthfacility')
        // this.$store.dispatch('registration/getRanapAll',{health_facility_id:this.health_facility_id,date_start:moment(this.form_transaction.date_added).format('YYYY-MM-DD'),date_end:moment(this.form_transaction.date_added).format('YYYY-MM-DD')})
        this.$store.dispatch('catalog/getParam')
        this.$store.dispatch('depo/getParam','noncentral')
        this.$store.dispatch('packages/getParam')
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>