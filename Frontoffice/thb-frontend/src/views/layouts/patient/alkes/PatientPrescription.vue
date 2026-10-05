<template>
    <div>
        <patientMenu selectedMenu="menu25" />
        <div class="label-head my-16 my-lg-24">
            <span class="px-16 text-bold-24 text-neutral-700">List Pemakaian Obat</span>
        </div>
        <!-- ////////  -->
        <div class="card-body shadow-sm rounded">
            <div class="row mx-0">
                <div class="col-12 col-lg">
                    <div class="row">
                        <div class="col-12 col-lg-3">
                            <img src="@/@core/assets/images/pendaftaran/user.png" alt="your-photo"
                                class="rounded-circle shadow-sm c-img">
                        </div>
                        <div class="col-12 col-lg">
                            <span class="text-bold-18 text-indigo">
                                <img src="@/@core/assets/icons/nurse/user.svg" alt="user-icon">{{this.regData.norm}}
                            </span>
                            <p class="mb-0 mt-12 text-semi-24 text-neutral-800">
                                <span>({{this.regData.alias_name}}) </span>
                                <span>{{this.regData.name}}</span>
                            </p>
                            <div class="row mx-0 mt-16">
                                <b-badge class="rounded text-neutral-500 text-uppercase border border-success-200 px-10 py-1" :variant="this.regData.is_new == 'N' ? 'success-200' : 'info-200'">{{ this.regData.is_new == 'N' ? 'Pasien Lama' : 'pasien Baru' }}</b-badge>
                                <b-badge
                                    class="rounded text-neutral-500 text-uppercase border border-success-500 px-10 py-1 mx-lg-12 "
                                    variant="success-200">{{this.regData.health_facility_name}}</b-badge>
                                <b-badge
                                    class="rounded text-neutral-500 text-uppercase border border-warning-500 px-10 py-1 "
                                    variant="warning-200">{{this.regData.health_facility_item_name}}</b-badge>
                                <div v-if="this.regData.registration_insurance">
                                    <div v-for="list in this.regData.registration_insurance">
                                        <b-badge v-if="list.is_cob == 1"class="rounded text-neutral-500 text-uppercase border border-warning-500 px-10 py-1" variant="warning-200">COB</b-badge>
                                    </div>
                                </div>
                            </div>
                            <p class="text-neutral-800 mt-8 mt-lg-16">
                                <span class="text-regular-14 mr-2">Status:</span>
                                <span class="text-bold-14">Dalam Penanganan Medis</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 mt-16 mt-lg-0">
                    <div class="d-flex flex-column align-items-lg-end h-100">
                        <div class="row mx-0">
                            <b-button pill variant="outline-indigo" size="sm" class="mr-2">
                                <b-icon icon="arrow-left" /> <span>Kembali</span>
                            </b-button>
                            <administration />
                        </div>
                        <div class="mt-2 mt-lg-auto">
                            <b-button pill variant="outline-indigo" @click="refresh()" size="sm">
                                <b-icon icon="arrow-clockwise" /> <span>Refresh</span>
                            </b-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="mt-24">
            <b-modal v-model="open_modal.procedure" title="Tambah Rincian" hide-footer>
                <div>
                    <div class="mt-12">
                        <label class="c-form-label">Tindakan Procedure</label>
                        <v-select v-model="form_transaction.costing_id" aria-readonly="true" :options="OptionsCosting" :reduce="OptionsCosting => OptionsCosting" label="title" placeholder="-- Tindakan Procedure --"></v-select>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Keterangan</label>
                        <b-textarea v-model="form_transaction.notes" rows="3"></b-textarea>
                    </div>
                    <div class="d-flex justify-content-end mt-24">
                        <b-button variant="indigo-500" @click.prevent="validationFormTransaction" class="px-3">Save</b-button>
                    </div>
                </div>
            </b-modal>
        </div> -->
        <section class="mt-24" style="display:none">
            <div class="card">
                <div class="card-header bg-primary-200">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-bold-16">DPO CHART</span>
                    </div>
                </div>
                <div class="card-body">   
                    <section class="col-lg-12">
                        <div class="row">
                            <table class="table table-responsive table-bordered" responsive small bordered>
                                <thead class="text-center text-bold-14">
                                    <tr>
                                        <th rowspan="2">No</th>
                                        <th rowspan="2">Katalog/Obat</th>
                                        <th rowspan="2">Dosis</th>
                                        <th rowspan="2">Rute Pemberian</th>
                                        <th rowspan="2">Nama Dokter</th>
                                        <th colspan="2">High Alert*</th>
                                        <th rowspan="2">Pemberian & Paraf</th>
                                        <th colspan="12">Waktu Pemberian Obat</th>
                                    </tr>
                                    <tr>
                                        <th>Ya</th>
                                        <th>Tidak</th>
                                        <th colspan="6">Tanggal</th>
                                        <th colspan="6">Tanggal</th>
                                    </tr>
                                </thead>
                                <!-- <tbody>
                                    <tr class="item" v-for="(item, index) in DPO">
                                        <td rowspan="6">{{ item.index }}</td>
                                        <td rowspan="6">{{ item.pharmacy_catalog_name }}</td>
                                        <td rowspan="6">{{ item.used_value }}x{{ item.dose }}{{ item.dose_name }}</td>
                                        <td rowspan="6">{{ item.prescription_type_name }}</td>
                                        <td rowspan="6">{{ item.doctor_in_charge_name }}</td>
                                        <td rowspan="6">
                                            <feather-icon :icon="item.high_yn === 1 ? 'CheckIcon' : ''" size="18" class="mb-1" />
                                        </td>
                                        <td rowspan="6">
                                            <feather-icon :icon="item.high_yn === 0 ? 'CheckIcon' : ''" size="18" class="mb-1" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Jam Pemberian</td>
                                        <td>6</td>
                                        <td>10</td>
                                        <td>14</td>
                                        <td>18</td>
                                        <td>22</td>
                                        <td>2</td>
                                        <td>8</td>
                                        <td>16</td>
                                        <td>24</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Perawat 1</td>
                                    </tr>
                                    <tr>
                                        <td>Perawat 2</td>
                                    </tr>
                                    <tr>
                                        <td>Apoteker</td>
                                    </tr>
                                    <tr>
                                        <td>Pasien/Keluarga</td>
                                    </tr>
                                </tbody> -->
                            </table>
                        </div>
                    </section>
                </div>
            </div>
        </section>
        <section class="mt-24">
            <div class="card">
                <div class="card-header bg-primary-200">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-bold-16">INPUT PEMAKAIAN OBAT & ALKES / RESEP DOKTER</span>
                        <b-button variant="indigo-500" :disabled="is_submit" @click.prevent="validationFormTransaction"><feather-icon icon="SaveIcon" /> Simpan</b-button>
                    </div>
                </div>
                <div class="card-body">   
                    <section class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-3" style="display: none;">
                                <label>Lokasi Order Obat (APOTIK)</label>
                            </div>
                            <div class="col-lg-9" style="display: none;">
                                <v-select v-model="form_transaction.pharmacy_depo_id" :options="OptionsPharmacyDepo" :reduce="(OptionsPharmacyDepo) => OptionsPharmacyDepo.value" label="title" placeholder="-- Lokasi Order Obat (APOTIK) --"></v-select>
                            </div>
                            <div class="col-lg-3" style="display:none;">
                                <label>Poliklinik/Ruangan</label>
                            </div>
                            <div class="col-lg-9" style="display:none;">
                                <v-select v-model="form_transaction.health_facility_item_id" :options="OptionsHealthFacilityItem" :reduce="(OptionsHealthFacilityItem) => OptionsHealthFacilityItem.value" label="title" placeholder="-- Poliklinik/Ruangan --"></v-select>
                            </div>
                            <div class="col-lg-3" style="display:none;">
                                <label>Dokter</label>
                            </div>
                            <div class="col-lg-9" style="display:none;">
                                <v-select v-model="form_transaction.soap_p_id" :options="OptionsSoapPlanDoctor" :reduce="(OptionsSoapPlanDoctor) => OptionsSoapPlanDoctor.value" label="title" placeholder="-- Dokter --"></v-select>
                            </div>
                            <div class="col-lg-3">
                                <label>Jenis Transaksi</label>
                            </div>
                            <div class="col-lg-9">
                                <v-select v-model="form_transaction.type_prescription_id" :options="PrescriptionTypeOptions" :reduce="PrescriptionTypeOptions => PrescriptionTypeOptions.value" label="title" placeholder="Instalasi"></v-select>
                            </div>
                            <!-- <div class="col-lg-3">
                                <label>Tanggal</label>
                            </div>
                            <div class="col-lg-9">
                                <b-form-input type="date" v-model="form_transaction.date_added"></b-form-input>
                            </div> -->
                        </div>
                        <div class="row mt-16">
                            <table class="table">
                                <thead class="text-center text-bold-14">
                                    <tr>
                                        <th rowspan="2">
                                            <!-- <b-form-checkbox /> -->
                                        </th>
                                        <th rowspan="2" style="min-width: 300px;">NAMA BARANG</th>
                                        <th colspan="5">ESTIMASI ORDER</th>
                                    </tr>
                                    <tr>
                                        <!-- <th style="min-width: 50px;">SEDIAAN OBAT</th> -->
                                        <!-- <th style="min-width: 50px;">DOSIS DOKTER</th> -->
                                        <th style="min-width: 50px;">JUMLAH</th>
                                        <th style="min-width: 140px;">KEMASAN</th>
                                        <th style="min-width: 50px;">KETERANGAN&ATURAN PAKAI</th>
                                    </tr>
                                </thead>
                                <template v-for="(item_p, index_p) in form_transaction.form_prescription_parent">
                                    <tr>
                                        <td colspan="2">
                                            <label><b>{{ item_p.prescription }}</b></label>
                                        </td>
                                        <td colspan="2" v-if="item_p.id !== 1">
                                            <b-input-group append="Jumlah">
                                                <b-form-input style="width:150px" type="number" v-model="item_p.value" />
                                            </b-input-group>
                                            <b-input-group append="Aturan Pakai">
                                                <b-form-input style="width:150px" type="text" v-model="item_p.used" />
                                            </b-input-group>
                                        </td>
                                    </tr>
                                    <!-- {{ item_p.prescription }} {{ item_p.id }} -->
                                    <tr class="item" v-for="(item, index) in form_transaction.form_prescription" v-if="item_p.id === item.prescription_type_id">
                                        <td>
                                            <b-button variant="danger-600" size="sm" @click="deletePRES(index)" class="mr-2">
                                                <feather-icon icon="TrashIcon" />
                                            </b-button>
                                        </td>
                                        <td>
                                            {{ item.name }}
                                        </td>
                                        <!-- <td>
                                        </td>
                                        <td>
                                            <b-form-input style="width:100px" type="text" v-model="item.sum_dose" />
                                        </td> -->
                                        <!-- <td>
                                            <template v-if="item.prescription_type_id === 1">
                                                <b-form-input readonly type="number" v-model="item.value_doctor" @change="vdChanged($event, index)"/>
                                            </template>
                                            <template v-if="item.prescription_type_id !== 1">
                                                <b-form-input type="number" v-model="item.value_doctor" @change="vdChanged($event, index)"/>
                                            </template>
                                        </td> -->
                                        <td>
                                            <b-form-input type="number" v-model="item.value_package" />
                                        </td>
                                        <td>
                                            <v-select v-model="item.package_id" :options="PackageOptions" :reduce="PackageOptions => PackageOptions.value" label="title" placeholder="Kemasan"></v-select>
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
                                    <td>
                                        <v-select v-model="prescription" @input="onAddPRES($event)" :options="PrescriptionOptions" :reduce="PrescriptionOptions => PrescriptionOptions" label="title" placeholder="-- Racikan --"></v-select>
                                        <v-select v-model="catalog" @input="onAddCAT($event)" :options="CatalogOptions" :reduce="CatalogOptions => CatalogOptions" label="title" placeholder="-- Pilih Katalog --"></v-select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </section>
                </div>
            </div>
        </section>
        <div class="mt-32">
            <div class="card-body mt-32 border rounded-lg">
                <div class="mt-16 mt-lg-24">
                    <b-table-simple bordered hover resposive>
                        <b-thead>
                            <b-tr>
                                <b-th>Tanggal & Jam Input</b-th>
                                <b-th>No Resep/Order</b-th>
                                <b-th>Lokasi Order Obat (APOTIK)</b-th>
                                <b-th>Dokter</b-th>
                                <b-th>Status</b-th>
                                <b-th>Aksi</b-th>
                            </b-tr>
                        </b-thead>
                        <b-tbody>
                            <template v-for="(itemsResult,i) in prescriptionitems" >
                                <b-tr>
                                    <b-td>{{ itemsResult.date_added }}</b-td>
                                    <b-td>{{ itemsResult.no_order }}
                                        <template v-if="itemsResult.form_prescription.length > 0">
                                            <li v-for="iip in itemsResult.form_prescription">
                                                {{ iip.pharmacy_catalog_name }}, {{ iip.qty }} {{ iip.package_name }}, {{ iip.used }}, {{ iip.desc }}
                                            </li>
                                        </template>
                                    </b-td>
                                    <b-td>{{ itemsResult.depo_name }}</b-td>
                                    <b-td>{{ itemsResult.doctor_in_charge_name }}</b-td>
                                    <b-td>{{ itemsResult.status === 'Layani Resep' ? 'Menunggu Di Proses' : itemsResult.status }}</b-td>
                                    <b-td>
                                        <b-button v-if="itemsResult.status === 'Layani Resep'" @click="openPrescription(itemsResult)" size="sm" variant="warning-500" class="mr-2">
                                            <feather-icon icon="PencilIcon" /> Edit
                                        </b-button>
                                        <!-- <b-button @click="rowOnDelete(result.id)" size="sm" variant="danger-700">
                                            <feather-icon icon="TrashIcon" />
                                        </b-button> -->
                                    </b-td>
                                </b-tr>
                            </template>
                        </b-tbody>
                    </b-table-simple>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import moment from 'moment'
import { mapState } from 'vuex'
import patientMenu from '@/@core/components/tab-menu/patientMenu.vue';
import administration from '@/views/layouts/patient/doctor/administration.vue';
// import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import router from '@/router/index.js'
import vSelect from 'vue-select'

export default {
    name: 'AlkesStudies',
    components: {
        patientMenu, administration, vSelect
    },
    data() {
        return {
            is_submit:false,
            open_modal: {
                procedure: false,
                add_list: false,
                edit_list: false,
                remove_list: false,
            },
            PrescriptionTypeOptions:[
                { value: 1, title: 'Resep' },
                { value: 2, title: 'Resep Pulang' },
                { value: 4, title: 'DO' }
                // { value: 3, title: 'BHP' },
            ],
            userData: JSON.parse(localStorage.getItem('userData')),
            regData: {},
            DPO: [
                { index: 1, pharmacy_catalog_name: 'Paracetamol', dose:'500', dose_name:'mg', used:'3x1',used_value:'3',prescription_type_name:'PO', doctor_in_charge_name:'dr. Aditya Zhafir Dhiaulhaq, S.BA', high_yn:1,
                gift_paraf:[
                    {gift:'Jam Pemberian'},
                    {gift:'Perawat 1'},
                    {gift:'Perawat 2'},
                    {gift:'Apoteker'},
                    {gift:'Pasien/Keluarga'},
                ]
                },
            ],
            PrescriptionOptions:[
                { value: 1, title: 'OBAT SATUAN' },
                { value: 2, title: 'RACIK 1' },
                { value: 3, title: 'RACIK 2' },
                { value: 4, title: 'RACIK 3' },
                { value: 5, title: 'RACIK 4' }
            ],
            form_transaction: {
                id:'',
                view:'add',
                type_prescription_id:1,
                soap_p_id:'',
                registration_destination_id:'',
                pharmacy_depo_id:'297c44f0-12ef-40e6-b9a7-c831065b3372',
                date_added: moment(new Date()).format('YYYY-MM-DD'),
                norm:'',
                health_facility_item_id:'',
                form_prescription_parent:[],
                form_prescription:[]
            },
            catalog:'',
            prescription:'',
            prescription_name:'OBAT SATUAN'
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
            items: state => state.soap.items, 
            prescriptionitems: state => state.soap.prescription.items, 
            fieldsTransaction: state => state.soap.fieldstransaction,
            OptionsPharmacyDepo: state => state.depo.options.depo,
            OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,
            OptionsSoapPlanDoctor: state => state.soap.options.plandoctor,
            CatalogOptions: state => state.catalog.options.catalog,
            PackageOptions: state => state.packages.options.packages
        }),
        firstRow() {
            return (this.Planningtable.currentPage - 1) * this.Planningtable.perPage + 1
        },
        lastRow() {
            return Math.min(this.Planningtable.currentPage * this.Planningtable.perPage, this.Planningtable.totalRows)
        }
    },
    methods: {
        refresh () {
            router.go(router.currentRoute)
        },
        deletePRES (index) {
            this.form_transaction.form_prescription.splice(index, 1)
        },
        onAddPRES (event) {
            // this.prescription = event.value
            this.prescription_type = event
            let index = this.form_transaction.form_prescription_parent.findIndex((item) => item.id === event.value);
            // Check if the object with the specified property value exists in the array
            if (index === -1) {
                // If not found, push a new object with the desired properties
                this.form_transaction.form_prescription_parent.push({
                    id:event.value,
                    prescription:event.title,
                    used:'',
                    value:0
                }) 
            } else {
                // If found, log a message indicating that the object already exists
                console.log("Object already exists");
            }
        },
        onAddCAT (event) {
            this.catalog = ''
            this.collation = ''
            if (this.prescription === '') {
                alert("Pilih Racikan!");
            } else {
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
                        value_doctor:0,
                        value_package:0,
                        used:'',
                        desc:'',
                        sort:this.form_transaction.form_prescription.length, 
                    }
                )
            }
        },
        vdChanged($event, index){
            let value_doctor = $event
            let value_prescription = 0
            for (let ipp = 0; ipp < this.form_transaction.form_prescription_parent.length; ipp++) {
                if (this.form_transaction.form_prescription[index].prescription_type_id === this.form_transaction.form_prescription_parent[ipp].id) {
                    value_prescription = this.$options.filters.decimalToNumber(this.form_transaction.form_prescription_parent[ipp].value)
                }
            }
            let dose = this.form_transaction.form_prescription[index].dose
            this.form_transaction.form_prescription[index].value_package = this.$options.filters.numberToDecimal(value_doctor*value_prescription/dose)
        },
        getData (id) {
			this.$store.dispatch('registration/getById', id).then(data => {
                this.regData = data[0]
                this.$store.dispatch('healthfacilityitem/getParamAlls')
                this.form_transaction.norm = data[0].norm
                this.form_transaction.registration_destination_id = id
                this.form_transaction.doctor_in_charge_id = data[0].doctor_in_charge_id
                this.form_transaction.health_facility_item_id = data[0].health_facility_item_id
                // this.form_nurse.employee_nurse_id = this.userData.employee_id
                // this.form_nurse.registration_destination_id = id
                // this.show = false
            })
        },
        validationFormTransaction (id) {
            // this.$refs.simpleRules.validate()
            // .then(success => {
            //     if (success) {
                this.is_submit=true
                const formData = this.form_transaction
                this.$store.dispatch('soap/formPostPutPrescriptionModule', formData)
                .then(res => {
                    this.is_submit = false
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.$store.dispatch('soap/getSoapDoctorById', this.$route.params.id)
                    this.$store.dispatch('soap/getSoapPrescriptionById', this.$route.params.id)
                    this.form_transaction.id = ''
                    this.form_transaction.view = 'add'
                    // this.form_transaction.type_prescription_id = 3
                    // this.form_transaction.soap_p_id = ''
                    // this.form_transaction.registration_destination_id = ''
                    this.form_transaction.pharmacy_depo_id = '297c44f0-12ef-40e6-b9a7-c831065b3372'
                    this.form_transaction.date_added =  moment(new Date()).format('YYYY-MM-DD')
                    // this.form_transaction.norm = ''
                    // this.form_transaction.health_facility_item_id = ''
                    this.form_transaction.form_prescription_parent = []
                    this.form_transaction.form_prescription = []
                }, 
                err => {
                    this.is_submit = false
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                })
                // } else {
                // this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                // }
            // })
        },
        openPrescription (data) {
            // this.is_submit=true
            console.log(data)
            this.form_transaction.id = data.id
            this.form_transaction.norm = data.norm
            let prescription_type = {value:1,title:'OBAT SATUAN'}
            this.prescription = prescription_type
            this.prescription_type = prescription_type
            this.form_transaction.form_prescription_parent.push({
                id:1,
                prescription:'Obat Satuan',
                used:'',
                value:0
            }) 
            for (let index = 0; index < data.form_prescription.length; index++) {
                this.form_transaction.form_prescription.push(
                    { 
                        code:data.form_prescription[index].pharmacy_catalog_code,
                        name:data.form_prescription[index].pharmacy_catalog_name,
                        pharmacy_catalog_id:data.form_prescription[index].pharmacy_catalog_id, 
                        pharmacy_prescription_id:data.form_prescription[index].pharmacy_prescription_id, 
                        package_id:data.form_prescription[index].package_id,
                        dose:data.form_prescription[index].dose,
                        sum_dose:data.form_prescription[index].dose+' '+data.form_prescription[index].dose_name, 
                        package_name:data.form_prescription[index].package_name,
                        prescription_type_id:this.prescription_type.value,
                        prescription_type_name:this.prescription_type.title,
                        value_doctor:data.form_prescription[index].qty_doctor,
                        value_package:data.form_prescription[index].qty,
                        used:data.form_prescription[index].used,
                        desc:data.form_prescription[index].desc,
                        sort:data.form_prescription[index].sort, 
                    }
                )
            }
            this.form_transaction.health_facility_item_id = data.health_facility_item_id
            this.form_transaction.pharmacy_depo_id = data.pharmacy_depo_id
            this.form_transaction.soap_p_id = data.soap_p_id
            this.form_transaction.view = 'edit'
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
    },
    mounted () {
        this.$store.dispatch('soap/getSoapPrescriptionById', this.$route.params.id)
        this.$store.dispatch('catalog/getParam')
        this.$store.dispatch('depo/getParam','noncentral')
        this.$store.dispatch('packages/getParam')
        this.$store.dispatch('soap/getSoapDoctorById', this.$route.params.id)
        if (this.$route.params.id) {
            // this.show = true
            this.id = this.$route.params.id
            this.getData(this.$route.params.id)
        }
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>
<style scoped>
@import '../../../../@core/css/utility.css';

.label-head::before {
    content: '';
    border-left: 6px solid #648DE1;
}

.c-img {
    aspect-ratio: 1/1;
    object-fit: cover;
    max-width: 150px;
    max-height: 150px;
    border: 10px solid white;
}
</style>