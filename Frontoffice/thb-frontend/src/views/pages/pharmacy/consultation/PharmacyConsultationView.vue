<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'FARMASI' },
                { text: 'KONSELING OBAT' },
            ]"
        >
        <TopPharmacyMenu selectedMenu="menu7" />
        <section class="mt-24 d-flex">
            <b-button pill @click="toggleMainTab('list')"
                :variant="active.main_tab === 'list' ? 'primary-500' : 'outline-primary-500'" class="mr-2">List
            </b-button>
            <b-button pill @click="toggleMainTab('add')"
                :variant="active.main_tab === 'add' ? 'primary-500' : 'outline-primary-500'">Tambah Data</b-button>
        </section>
        <b-overlay
            :show="show"
            spinner-variant="primary"
            spinner-type="grow"
            spinner-small
            rounded="sm"
            >
            <div v-if="active.main_tab === 'list'">
                <section class="mt-32 mt-24">
                    <div class="card">
                        <div class="card-header bg-primary-200 fw-bold">Filter Data</div>
                        <div class="card-body">
                            <div class="d-flex justify-content-center">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-4 mt-1">
                                            <div>
                                                <label class="c-form-label">No Rawat</label>
                                                <b-form-input v-model="params.registration_destination_id" />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mt-1">
                                            <div>
                                                <label class="c-form-label">Nama Pasien</label>
                                                <b-form-input v-model="params.patient_name" />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mt-1">
                                            <label class="c-form-label">Tanggal Konseling</label>
                                            <b-input-group>
                                                <b-form-input id="example-input" v-model="params.date_start"
                                                    type="text" placeholder="YYYY-MM-DD"
                                                    autocomplete="off"></b-form-input>
                                                <b-input-group-append>
                                                    <b-form-datepicker v-model="params.date_start" button-only
                                                        right locale="en-US"
                                                        aria-controls="example-input"></b-form-datepicker>
                                                </b-input-group-append>
                                                <b-form-input id="example-inputx" v-model="params.date_end"
                                                    type="text" placeholder="YYYY-MM-DD"
                                                    autocomplete="off"></b-form-input>
                                                <b-input-group-append>
                                                    <b-form-datepicker v-model="params.date_end" button-only
                                                        right locale="en-US"
                                                        aria-controls="example-inputx"></b-form-datepicker>
                                                </b-input-group-append>
                                            </b-input-group>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end mt-20">
                                        <b-button pill variant="primary" @click="filterData" :disabled="isDisabledFilter" class="px-3">Filter</b-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="mt-32 mt-24">
                    <div class="row mx-0">
                        <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0 mr-1">
                            <span class="text-regular-14">Tampilkan</span>
                            <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions"/>
                            <span class="text-regular-14">Entri</span>
                        </b-form-group>
                    </div>
                    <b-table bordered responsive hover small head-variant="light" :items="items" :fields="fields" :per-page="0" :current-page="currentPage" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                        <template #cell(previous_counseling)="data">
                            <b-badge :variant="data.value === 1 ? 'primary' : 'secondary'">
                                {{ data.value === 1 ? 'Ya' : 'Tidak' }}
                            </b-badge>
                        </template>
                        <template #cell(action)="row">
                            <div class="row mx-0">
                                <b-button variant="warning-500" size="sm" class="mr-1" @click="editData(row.item)">
                                    <feather-icon icon="EditIcon"  />
                                </b-button>
                                <b-button variant="danger-500" size="sm" @click="rowOnDelete(row.item.id)">
                                    <feather-icon icon="TrashIcon"  />
                                </b-button>
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
            <div v-if="active.main_tab === 'add'">
                <section class="card mt-32">
                    <div class="card-header bg-primary-200 font-weight-bold">
                        <div class="d-flex justify-content-lg-between">
                            <span class="font-weight-bold">DATA KONSELING FARMASI</span>
                            <div class="d-flex">
                                <template v-if="this.button == true">
                                    <b-button variant="primary-600" size="sm" @click.prevent="validationForm" class="mr-1">
                                        <feather-icon icon="SaveIcon" /> Simpan
                                    </b-button>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-center">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="row mt-1">
                                            <div class="col-lg-4">
                                                <v-select :disabled="is_search" @input="onAddReg($event)" v-model="form.registration_destination_id" readonly :options="RegistRanapOptions" :reduce="RegistRanapOptions => RegistRanapOptions.value" label="title" placeholder="Cari Pasien"></v-select>
                                            </div>
                                            <div class="col-lg-3">
                                                <b-form-input v-model="form.norm" placeholder="No RM" readonly />
                                            </div>
                                            <div class="col-lg-5">
                                                <b-form-input v-model="form.patient_name" placeholder="Nama Pasien" readonly />
                                            </div>
                                        </div>
                                        <div class="row mt-1 align-items-center">
                                            <div class="col-lg-2">
                                                <label class="c-form-label mb-0">Tgl. Lahir</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <b-form-input v-model="form.patient_birth_date" readonly />
                                            </div>
                                        </div>
                                        <div class="row mt-1 align-items-center">
                                            <div class="col-lg-2">
                                                <label class="c-form-label mb-0">Tanggal</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <b-input-group>
                                                    <b-form-input id="counseling-date" v-model="form.date" type="text" placeholder="YYYY-MM-DD" autocomplete="off"></b-form-input>
                                                    <b-input-group-append>
                                                        <b-form-datepicker v-model="form.date" button-only right locale="en-US" aria-controls="counseling-date"></b-form-datepicker>
                                                    </b-input-group-append>
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-3">
                                                <b-form-input v-model="form.pharmacist_name" placeholder="apoteker" readonly />
                                            </div>
                                        </div>
                                        <div class="row mt-1 align-items-center">
                                            <div class="col-lg-6">
                                                <label class="c-form-label mb-0">Diagnosa</label>
                                                <b-form-input v-model="form.diagnosis" placeholder="Diagnosa" />
                                            </div>
                                            <div class="col-lg-3">
                                                <label class="c-form-label mb-0">Alergi</label>
                                                <b-form-input v-model="form.allergy" placeholder="Alergi" />
                                            </div>
                                            <div class="col-lg-3">
                                                <label class="c-form-label mb-0">Pernah Konseling Sebelumnya</label>
                                                <v-select v-model="form.previous_counseling" :options="YesNoOptions" :reduce="YesNoOptions => YesNoOptions.value" label="title" placeholder="Pernah Konseling Sebelumnya"></v-select>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-lg-12">
                                                <label class="c-form-label">Nama Obat, Dosis & Cara Pemakaian</label>
                                                <b-textarea v-model="form.drug_dose_usage" rows="3" />
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-lg-12">
                                                <label class="c-form-label">Keluhan</label>
                                                <b-textarea v-model="form.complaint" rows="3" />
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-lg-12">
                                                <label class="c-form-label">Tindak Lanjut</label>
                                                <b-textarea v-model="form.follow_up" rows="3" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </b-overlay>
        </Layout>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import TopPharmacyMenu from '@/@core/components/top-menu/TopPharmacyMenu.vue';
import vSelect from 'vue-select'
import moment from 'moment'
export default {
    name: 'MedicationCounseling',
    components: {
        TopPharmacyMenu, vSelect
    },
    data() {
        return {
            is_search:false,
            button: true,
            health_facility_id:'',
            urlbe: process.env.VUE_APP_IMAGE,
            userData: JSON.parse(localStorage.getItem('userData')),
            active: {
                main_tab: 'list',
            },
            params: {
                registration_destination_id: '',
                patient_name: '',
                date_start: '',
                date_end: '',
                offset: 0,
                limit: 20,
            },
            currentPage: 1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            isDisabled: false,
            isDisabledFilter: false,
            show: false,
            attachmentPreview: null,
            attachmentFile: null,
            form: {
                id: '',
                registration_destination_id: '',
                norm: '',
                patient_id: '',
                patient_name: '',
                patient_birth_date: '',
                date: moment().format('YYYY-MM-DD'),
                time: moment().format('HH:mm:ss'),
                pharmacist_id: '',
                pharmacist_name: JSON.parse(localStorage.getItem('userData')).employee_name,
                diagnosis: '',
                allergy: '',
                previous_counseling: 2,
                drug_dose_usage: '',
                complaint: '',
                follow_up: '',
                attachment: '',
            },
            YesNoOptions: [
                { value: 1, title: 'Ya' },
                { value: 2, title: 'Tidak' },
            ],
        }
    },
    watch: {
        currentPage: {
            handler: function (value) {
                this.firstRows = this.perPage * (value - 1) + 1
                this.lastRows = this.perPage * (value) + 1
                this.params.offset = this.perPage * (value - 1)
                this.params.limit = this.perPage
                this.$store.dispatch('counseling/getAll', this.params)
            }
        },
        'form.date'(val) {
            if (val) {
                return this.$store.dispatch('registration/getRanapAll',{health_facility_id:'3cb626f5-9148-4fb1-8663-3d7f028b01a6',date_start:moment(val).format('YYYY-MM-DD'),date_end:moment(val).format('YYYY-MM-DD')})
            }
        },
        perPage: {
            handler: function (value) {
                this.params.limit = this.perPage
                this.$store.dispatch('counseling/getAll', this.params)
            }
        }
    },
    computed: {
        ...mapState({
            RegistRanapOptions:  state => state.registration.options.ranap,
            totalRows: state => state.counseling.totalRows,
            fields: state => state.counseling.fields,
            items: state => state.counseling.items,
            pageOptions: state => state.counseling.pageOptions,
            PharmacistOptions: state => state.employee.options.pharmacist,
        }),
    },
    filters: {
        currency(value) {
            return new Intl.NumberFormat().format(value)
        },
    },
    methods: {
        onAddReg () {
			this.$store.dispatch('registration/getById', this.form.registration_destination_id).then(data => {
                let regData = data[0]
                this.form.patient_name = regData.name
                this.form.norm = regData.norm
                this.form.patient_birth_date = regData.birth_date
                this.$store.dispatch('soap/getByRM', regData.norm)

            })
        },
        onAttachmentChange(event) {
            const file = event.target.files[0]
            if (!file) return
            this.attachmentFile = file
            const reader = new FileReader()
            reader.onload = e => {
                this.attachmentPreview = e.target.result
            }
            reader.readAsDataURL(file)
        },
        validationForm() {
            this.show = true
            this.$store.dispatch('counseling/formPostPut', this.form)
            .then(res => {
                this.notification('Success', 'CheckIcon', 'success', 'You have successfully')
                this.$store.dispatch('counseling/getAll', this.params)
                this.toggleMainTab('list')
                this.show = false
            },
            err => {
                this.show = false
                this.notification('Failed', 'CloseIcon', 'danger', 'You have unsuccessful')
            })
        },
        filterData() {
            this.show = true
            this.isDisabledFilter = true
            this.$store.dispatch('counseling/getAll', this.params)
            .then(res => {
                this.show = false
                this.isDisabledFilter = false
            },
            err => {
                this.show = false
                this.isDisabledFilter = false
            })
        },
        rowOnDelete(id) {
            this.$bvModal
                .msgBoxConfirm('Please confirm that you want to delete everything.', {
                title: 'Please Confirm', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true) {
                    this.$store.dispatch('counseling/remove', id)
                    .then(response => {
                        if (response.status === 200) {
                            this.$store.dispatch('counseling/getAll', this.params)
                            this.notification('Success', 'CheckIcon', 'success', 'You have successfully')
                        } else {
                            this.notification('Failed', 'CloseIcon', 'danger', 'You have unsuccessful')
                        }
                    },
                    error => {
                        this.notification('Failed', 'CloseIcon', 'danger', 'You have unsuccessful')
                    })
                }
            })
        },
        notification(a, b, c, d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d }
            })
        },
        toggleMainTab(tab) {
            this.active.main_tab = tab
            this.button = true
            this.attachmentPreview = null
            this.attachmentFile = null
            this.form = {
                id: '',
                registration_destination_id: '',
                norm: '',
                patient_id: '',
                patient_name: '',
                patient_birth_date: '',
                date: moment().format('YYYY-MM-DD'),
                time: moment().format('HH:mm:ss'),
                is_face_to_face: true,
                pharmacist_id: '',
                pharmacist_name: JSON.parse(localStorage.getItem('userData')).employee_name,
                diagnosis: '',
                allergy: '',
                previous_counseling: 2,
                drug_dose_usage: '',
                complaint: '',
                follow_up: '',
                attachment: '',
            }
        },
        editData(obj) {
            this.button = true
            this.active.main_tab = 'add'
            this.attachmentPreview = null
            this.attachmentFile = null
            this.form = {
                id: obj.id,
                registration_destination_id: obj.registration_destination_id,
                norm: obj.norm,
                patient_id: obj.patient_id,
                patient_name: obj.patient_name,
                patient_birth_date: obj.patient_birth_date,
                date: obj.date,
                time: obj.time,
                is_face_to_face: obj.is_face_to_face === 1,
                pharmacist_id: obj.pharmacist_id,
                pharmacist_name: obj.pharmacist_name,
                diagnosis: obj.diagnosis,
                allergy: obj.allergy,
                previous_counseling: obj.previous_counseling,
                drug_dose_usage: obj.drug_dose_usage,
                complaint: obj.complaint,
                follow_up: obj.follow_up,
                attachment: obj.attachment,
            }
            this.show = false
        },
    },
    created() {
        this.$store.dispatch('counseling/getAll', this.params)
        this.$store.dispatch('employee/getParam', 'pharmacist')
        this.$store.dispatch('registration/getRanapAll',{health_facility_id:'3cb626f5-9148-4fb1-8663-3d7f028b01a6',date_start:moment(this.form.date).format('YYYY-MM-DD'),date_end:moment(this.form.date).format('YYYY-MM-DD')}).then(res => {this.is_search = false})
    }
};
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>