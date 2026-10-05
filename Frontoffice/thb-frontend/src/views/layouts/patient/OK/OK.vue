<template>
    <div>
        <patientMenu selectedMenu="menu15" />
        <div class="label-head my-16">
            <span class="px-16 text-bold-24 text-neutral-700">Riwayat OK Pasien</span>
        </div>
        <PatientBanner />
        <div class="mt-24">
            <div class="d-flex justify-content-end">
                <b-button @click="openModalOrder(null)" size="sm" class="px-3" pill variant="primary">
                    <feather-icon icon="PlusIcon" /> Tambah Order Kamar Operasi
                </b-button>
            </div>
            <section class="mt-32">
                <div class="row mx-0">
                    <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0 mr-1">
                        <span class="text-regular-14">Tampilkan</span>
                        <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions"/>
                        <span class="text-regular-14">Entri</span>
                    </b-form-group>
                </div>
                <b-table hover bordered responsive :fields="OKfields" :items="operating_list" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                    <template #cell(name)="row">
                        [{{ row.item.alias_name }}] {{ row.item.patient_name }}
                    </template>

                    <template #cell(operation_type_id)="row">
                        <div class="w-100 text-center">
                            <span v-if="row.value == 1">CITO</span>
                            <span v-else-if="row.value == 2">ELECTIVE</span>
                            <span v-else-if="row.value == 3">One Day Care (ODC)</span>
                        </div>
                    </template>

                    <template #cell(room)="row">
                        <template v-if="row.item.room_name || row.item.bed_name ">{{ row.item.room_name }} {{ row.item.bed_name }}</template>
                        <div v-else>-</div>
                    </template>

                    <template #cell(operation_approved_date)="row">
                        <div>{{ row.value ? row.value : '-'}}</div>
                    </template>

                    <template #cell(approved)="row">
                        <div>{{ row.item.employee_approved_name ? row.item.employee_approved_name : '-' }}</div>
                        <div>{{ row.item.employee_approved_date ? row.item.employee_approved_date : '-' }}</div>
                    </template>

                    <template #cell(action)="row">
                        <div class="d-flex">
                            <b-button @click="onModalInfo(row.item)" v-b-popover.hover.top="'Informasi Operasi'" size="sm" variant="w3green" class="mr-2">
                                <feather-icon icon="SearchIcon" />
                            </b-button>
                            <template v-if="row.item.status == 1">
                                <b-button @click="openModalOrder(row.item)" v-b-popover.hover.top="'Edit Order'" size="sm" variant="warning" class="mr-2">
                                    <feather-icon icon="EditIcon" />
                                </b-button>
                                <b-button @click="cancelOrder(row.item)" size="sm" variant="danger" v-b-popover.hover.top="'Batalkan Operasi'">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </template>
                        </div>
                    </template>
                </b-table>
                <div class="col px-0 mt-20">
                    <div class="d-flex">
                        <span class="text-regular-14">Menampilkan {{ firstRows }} hingga <template v-if="lastRows < totalRows">{{ lastRows }}</template> <template v-else>{{ totalRows }}</template> dari {{ totalRows }} entri</span>
                        <div class="ml-auto">
                            <b-pagination 
                                v-model="currentPage" 
                                :total-rows="totalRows"
                                :per-page="perPage" 
                                align="fill" size="sm" class="my-0"></b-pagination>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <b-modal v-model="active.modal_order" size="lg" title="Order Kamar Operasi" >
            <section class="card border-w3green bg-warning-100">
                <div class="card-body">
                    <table>
                        <tr>
                            <th class="align-top" style="width: 200px;">Diagnosis ICD10</th>
                            <th class="align-top px-3" style="width: 30px;">:</th>
                            <td class="align-top">
                                <template v-if="getICD10.icd10.length > 0">
                                    <li v-for="(item, index) in getICD10.icd10" :key="index">{{ item.code }} {{ item.name }}</li>
                                </template>
                                <div v-else>-</div>
                            </td>
                        </tr>
                        <tr>
                            <th class="align-top">Catatan ICD10</th>
                            <th class="align-top px-3">:</th>
                            <td class="align-top">{{ getICD10.icd10_notes ? getICD10.icd10_notes : '-' }}</td>
                        </tr>
                        <tr>
                            <th class="align-top">Diagnosis Non-ICD10</th>
                            <th class="align-top px-3">:</th>
                            <td class="align-top">
                                <template v-if="getICD10.non_icd10.length > 0">
                                    <li v-for="(item, index) in getICD10.non_icd10" :key="index">{{ item }}</li>
                                </template>
                                <div v-else>-</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </section>
            
            <div class="mt-32">
                <!-- <b-table :fields="ICD9TableFields" :items="form_order.icd9cm" responsive bordered class="mt-8" style="font-size: 13px;">
                    <template #cell(no)="row">
                        <div class="text-center">{{ row.index+1 }}</div>
                    </template>
                    <template #cell(action)="row">
                        <div class="d-flex justify-content-center">
                            <b-button variant="danger-600" size="sm" @click="delete9cm(row.index)">
                                <feather-icon icon="TrashIcon" />
                            </b-button>
                        </div>
                    </template>
                </b-table>
                <v-select v-model="icd9cm_id" @input="onAdd9cm($event)" aria-readonly="true" @search="onSearchIcd9cm" :options="OptionsIcd9cm" :reduce="OptionsIcd9cm => OptionsIcd9cm" label="title" placeholder="-- Pilih ICD9CM --" class="mt-12"></v-select> -->

                <b-textarea rows="2" type="text" v-model="form_order.therapy_plan" autocomplete="off" placeholder="Tindakan" class="mt-12"></b-textarea>
                
            </div>
            <div class="row">
                <div class="col-lg-6 mt-12">
                    <label class="c-form-label">Tanggal Operasi</label>                        
                    <b-form-input v-model="form_order.operation_date" type="datetime-local" class="form-control" />
                </div>
                <div class="col-lg-6 mt-12">
                    <label class="c-form-label">Operator</label>
                    <v-select v-model="form_order.main_operator_id" :options="doctor_list" :reduce="doctor_list => doctor_list.value" label="title" placeholder="-- Operator --"></v-select>
                </div>
                <div class="col-lg-6 mt-12">
                    <label class="c-form-label">Jenis Operasi</label>
                    <v-select v-model="form_order.operation_type_id" :options="OptionsOperationType" :reduce="OptionsOperationType => OptionsOperationType.value" label="title" placeholder="-- Jenis Operasi --"></v-select>
                </div>
                <div class="col-lg-6 mt-12">
                    <label class="c-form-label">Infectious</label>
                    <v-select v-model="form_order.infectious_type_id" :options="OptionsInfectious" :reduce="OptionsInfectious => OptionsInfectious.value" label="title" placeholder="-- Infectious --"></v-select>
                </div>
                <div class="col-12 mt-12">
                    <label class="c-form-label">Permintaan Akomodasi</label>
                    <b-form-checkbox-group v-model="form_order.accomodation_arry">
                        <b-form-checkbox value="1" class="my-1">Check 1</b-form-checkbox>
                        <b-form-checkbox value="2" class="my-1">Check 2</b-form-checkbox>
                        <b-form-checkbox value="3" class="my-1">Check 3</b-form-checkbox>
                        <b-form-checkbox value="4" class="my-1">Check 4</b-form-checkbox>
                    </b-form-checkbox-group>
                </div>
            </div>
            <template #modal-footer>
                <div class="w-100 d-flex justify-content-end">
                    <b-button @click="validationOrder" :disabled="disabled.order" variant="primary" class="px-3">
                        <feather-icon icon="SaveIcon" /> Simpan
                    </b-button>
                </div>
            </template>
        </b-modal>

        <b-modal scrollable v-model="active.modal_info" title="Informasi Operasi" hide-footer>
            <b-table-simple responsive class="w-100" style="font-size: 13px;">
                <b-tr>
                    <b-th class="align-top" style="width: 170px">Nama</b-th>
                    <b-td class="align-top text-center">:</b-td>
                    <b-td class="align-top">{{ information.patient_name ? information.patient_name : '-' }}</b-td>
                </b-tr>
                <b-tr>
                    <b-th class="align-top">No RM</b-th>
                    <b-td class="align-top text-center">:</b-td>
                    <b-td class="align-top">{{ information.norm ? information.norm : '-' }}</b-td>
                </b-tr>
                <b-tr>
                    <b-th class="align-top">Tanggl Lahir</b-th>
                    <b-td class="align-top text-center">:</b-td>
                    <b-td class="align-top">{{ information.birth_date ? information.birth_date : '-' }}</b-td>
                </b-tr>
                <b-tr>
                    <b-th class="align-top">Operator</b-th>
                    <b-td class="align-top text-center">:</b-td>
                    <b-td class="align-top">{{ information.employee_approved_name ? information.employee_approved_name : '-' }}</b-td>
                </b-tr>
                <b-tr>
                    <b-th class="align-top">Diagnosa ICD10</b-th>
                    <b-td class="align-top text-center">:</b-td>
                    <b-td class="align-top">
                        <template v-if="information && information.icd10">
                            <li v-for="(item, index) in information.icd10" :key="index">{{ item.icd10_code }} {{ item.icd10_name }}</li>
                        </template>
                        <div v-else>-</div>
                    </b-td>
                </b-tr>
                <b-tr>
                    <b-th class="align-top">Tindakan ICD9CM</b-th>
                    <b-td class="align-top text-center">:</b-td>
                    <b-td class="align-top">
                        <template v-if="information && information.icd9cm">
                            <li v-for="(item, index) in information.icd9cm" :key="index">{{ item.icd9cm_code }} {{ item.icd9cm_name }}</li>
                        </template>
                        <div v-else>-</div>
                    </b-td>
                </b-tr>
                <b-tr>
                    <b-th class="align-top">Rencana Operasi</b-th>
                    <b-td class="align-top text-center">:</b-td>
                    <b-td class="align-top">{{ information.operation_approved_date ? information.operation_approved_date : '-' }}</b-td>
                </b-tr>
                <b-tr>
                    <b-th class="align-top">Ruang Operasi</b-th>
                    <b-td class="align-top text-center">:</b-td>
                    <b-td class="align-top">{{ information.room_name ? information.room_name : '-' }} {{ information.room_name }}</b-td>
                </b-tr>
            </b-table-simple>
        </b-modal>
        
    </div>
</template>
<script>
import { mapState } from 'vuex'
import patientMenu from '@/@core/components/tab-menu/patientMenu.vue';
import administration from '@/views/layouts/patient/doctor/administration.vue';
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import vSelect from 'vue-select'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import moment from 'moment'


export default {
    name: 'Patient_OK',
    components: {
        patientMenu, administration, ModernLabel, vSelect,
    },
    data() {
        return {
            active: {
                modal_order: false,
                modal_info: false,
            },
            disabled: {
                order: false,
            },
            pageOptions: [20, 50, 100],
            currentPage:1,
            perPage: 20,
            totalRows: 0,
            firstRows: 1,
            lastRows: 20,
            params: {
                limit: 20,
                offset: 0,
                registration_destination_id: '',
            },
            OKfields: [
                { key: 'no_order', label: 'Kode Pendaftaran', thStyle: { minWidth: '140px' } },
                { key: 'name', label: 'Nama', thStyle: { minWidth: '140px' } },
                { key: 'norm', label: 'No RM', thStyle: { minWidth: '100px' } },
                { key: 'operation_type_id', label: 'Jenis Operasi', thStyle: { minWidth: '140px' } },
                { key: 'operation_date', label: 'Tanggal Request', thStyle: { minWidth: '140px' } },
                { key: 'operation_approved_date', label: 'Rencana Operasi', thStyle: { minWidth: '140px' } },
                { key: 'room', label: 'Ruang Operasi', thStyle: { minWidth: '140px' } },
                { key: 'approved', label: 'Disetujui Oleh', thStyle: { minWidth: '140px' } },
                { key: 'action', label: 'Action', thStyle: { minWidth: '140px' } },
            ],

            regData: {},
            soapData: {},
            doctor_list: [],
            operating_list: [],

            OptionsOperationType: [
                { value: 1, title: 'CITO' },
                { value: 2, title: 'ELECTIVE' },
                { value: 3, title: 'One Day Care (ODC)' },
            ],

            OptionsInfectious: [
                { value: 1, title: 'Infectious' },
                { value: 2, title: 'Non-Infectious' },
            ],

            ICD9TableFields: [
                { key: 'no', label: 'No', thStyle: 'width: 50px; text-align: center' },
                { key: 'title', label: 'ICD9CM' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 100px; text-align: center' },
            ],
            form_order: {
                id: '',
                registration_destination_id: '',
                soap_id: '',
                main_operator_id: '',
                operation_date: '',
                infectious_type_id: 0,
                operation_type_id: 0,
                icd9cm: [],
                therapy_plan: '',
                accomodation_arry: [],
            },
            icd9cm_id: '',
            OptionsIcd9cm: [],

            getICD10: {
                icd10: [],
                icd10_notes: '',
                non_icd10: [],
            },
            information: {},
        }
    },
    computed: {
        ...mapState({ 
            TypeRoomOptions: state => state.helper.options.type_room,
            OptionsRoom: state => state.room.options.room,
            OptionsBed: state => state.room.options.bed,         
        }),
    },
    watch : {
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage*(value-1)+1
                this.lastRows = this.perPage*(value)+1
                this.params.offset = this.perPage*(value-1)
                this.params.limit = this.perPage
                this.getOperatingList()
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.getOperatingList()
            }
        },
    },
    methods: {
        // GENERAL
        refresh () {
            router.go(router.currentRoute)
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        getData (id) {
			this.$store.dispatch('registration/getById', id).then(data => {
                this.regData = data[0]

                this.params.registration_destination_id = this.regData.registration_destination_id
                this.getOperatingList()

            })
            
            this.$store.dispatch('soap/getDetail', { registration_destination_id: id }).then(data => {
                this.soapData = data[0]
                if (this.soapData) {
                    this.getICD10.icd10 = this.soapData.icd10 ? this.soapData.icd10 : []
                    this.getICD10.icd10_notes = this.soapData.icd10_notes
                    this.getICD10.non_icd10 = this.soapData.non_icd10 ? this.soapData.non_icd10 : []

                    this.form_order.registration_destination_id = this.soapData.registration_destination_id
                    this.form_order.soap_id = this.soapData.id
                }
            })

            this.$store.dispatch('employee/getDoctorOptions', '').then((response) => {
                this.doctor_list = response
            })

        },
        
        // ORDER OK
        openModalOrder(obj){
            this.active.modal_order = true
            this.resetOrderForm()

            if (obj != null) {
                this.form_order.id = obj.id
                this.form_order.registration_destination_id = obj.registration_destination_id
                this.form_order.soap_id = obj.soap_id
                this.form_order.main_operator_id = obj.main_operator_id
                this.form_order.operation_date = obj.operation_date
                this.form_order.infectious_type_id = obj.infectious_type_id
                this.form_order.operation_type_id = obj.operation_type_id
                this.form_order.therapy_plan = obj.therapy_plan
                this.form_order.accomodation_arry = obj.accomodation_arry

                if (obj.icd9cm) {
                    const mappedIcd9cm = obj.icd9cm.map(item => {
                        return {
                            icd9cm_id: item.icd9cm_id,
                            value: item.icd9cm_id,
                            title: `${item.icd9cm_code} ${item.icd9cm_name}`, 
                            action: "action"
                        };
                    });                
                    this.form_order.icd9cm = mappedIcd9cm;
                }
            }
        },
        onAdd9cm (event) {
            this.icd9cm_id = ''
            if (this.form_order.icd9cm.some(item => item.icd9cm_id === event.value)) {
                this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list ICD9CM yang sama')
            } else {
                this.form_order.icd9cm.push({ icd9cm_id:event.value, title:event.title, value:event.value, action: 'action'})
            }
        },
        delete9cm (index) {
            this.form_order.icd9cm.splice(index, 1)
        },
        onSearchIcd9cm(search, loading) {
            if(search.length >= 2) {
                loading(true);
                this.$store.dispatch('icd9cm/getParam', search).then(data => {
                    this.OptionsIcd9cm = []
                    for (let i = 0; i < data.length; i++) {
                        const option = []
                        for (const key in data[i]) {
                            if (key === 'id') { option['value'] = data[i][key] } else if (key === 'name') { option['title'] = data[i]['code']+' '+data[i][key] } else if (key === 'id') { option['oid'] = data[i][key] }
                        }
                        this.OptionsIcd9cm.push(Object.assign({}, option))
                    }
                    loading(false)
                })
            }
        },
        validationOrder() {
            // console.log('this.form_order: ', this.form_order);
            this.disabled.order = true

            try {
                this.$store.dispatch('operatie/postPutOrder', this.form_order).then((response) => {
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.active.modal_order = false
                    this.getOperatingList()
                    this.resetOrderForm()
                    this.disabled.order = false
                })
                
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                this.disabled.order = false
                console.log('Error at: ', error);
            }
            
        },
        resetOrderForm() {
            this.form_order.id = ''
            this.form_order.main_operator_id = ''
            this.form_order.operation_date = ''
            this.form_order.infectious_type_id = 0
            this.form_order.operation_type_id = 0
            this.form_order.icd9cm = []
            this.form_order.therapy_plan = ''
            this.form_order.accomodation_arry = []
        },
        getOperatingList() {
            try {
                this.$store.dispatch('operatie/getAllList', this.params).then(response => {
                    this.operating_list = response.rows
                    this.totalRows = response.iTotal
                })
            } catch (error) {
                console.log('Error while GET DATA at: ', error);
            }
        },
        cancelOrder(obj){
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin membatalkan ${obj.no_order} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    try {
                        this.$store.dispatch('operatie/delRemoveOrder', obj.id) .then(response => {
                            if (response.status === 200 || response.status === 201) {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getOperatingList()
                            } else {
                                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            }
                        }) 
                        
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.log('Error at: ', error);
                    }
                }
            })
        },
        onModalInfo(obj) {
            this.information = ''
            this.active.modal_info = true
            this.information = { ...obj }
        }

    },
    created() {
        if (this.$route.params.id) {
            this.getData(this.$route.params.id)
        }
        this.$store.dispatch('helper/getParam', 'type_room')
    },

}
</script>
<style scoped>
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
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>