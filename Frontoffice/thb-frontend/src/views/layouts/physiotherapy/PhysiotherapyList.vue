<template>
    <div>
        <TopPhysiotherapyMenu selectedMenu="menu2" />
        <div class="col mt-32">
            <div class="card">
                <div @click="showFilter = !showFilter" class="card-header d-flex justify-content-between bg-primary-200">
                    <span class="text-bold-18">Filter Data</span>
                    <b-icon v-if="showFilter" icon="chevron-up" />
                    <b-icon v-else icon="chevron-down" />
                </div>
                <div v-if="showFilter" class="card-body">
                    <div class="row">
                        <div class="col-lg-6 px-lg-5">
                            <div>
                                <label class="c-form-label">No Pendaftaran</label>
                                <b-input-group>
                                    <b-form-input type="number"></b-form-input>
                                    <b-input-group-append>
                                        <b-button variant="neutral-200" disabled class="border"><b-icon
                                                icon="search" /></b-button>
                                    </b-input-group-append>
                                </b-input-group>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">No RM</label>
                                <b-input-group>
                                    <b-form-input type="number"></b-form-input>
                                    <b-input-group-append>
                                        <b-button variant="neutral-200" disabled class="border"><b-icon
                                                icon="search" /></b-button>
                                    </b-input-group-append>
                                </b-input-group>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Nama Pasien</label>
                                <b-form-select></b-form-select>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Status</label>
                                <b-form-select></b-form-select>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Jenis pasien</label>
                                <b-form-radio-group size="sm">
                                    <b-form-radio value="1">Pasien Lama</b-form-radio>
                                    <b-form-radio value="2">Pasien Baru</b-form-radio>
                                </b-form-radio-group>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Kelamin</label>
                                <b-form-radio-group size="sm">
                                    <b-form-radio value="1">Laki-laki</b-form-radio>
                                    <b-form-radio value="2">Perempuan</b-form-radio>
                                </b-form-radio-group>
                            </div>
                        </div>
                        <div class="col-lg-6 px-lg-5">
                            <div class="mt-12 mt-lg-0">
                                <label class="c-form-label">Instalasi</label>
                                <b-form-select></b-form-select>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Instalasi Sub</label>
                                <b-form-select></b-form-select>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Dokter</label>
                                <b-form-select></b-form-select>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Cara Masuk</label>
                                <b-form-select></b-form-select>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Cara Bayar</label>
                                <b-form-select></b-form-select>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Tanggal Request</label>
                                <div class="row mx-0">
                                    <b-form-input type="date" class="col" v-model="params.date_start"></b-form-input>
                                    <span class="px-3">-</span>
                                    <b-form-input type="date" class="col" v-model="params.date_end"></b-form-input>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-lg-end mt-40">
                        <div class="col-lg-2">
                            <b-button @click="filterData" variant="primary" :disabled="isDisabledFilter" class="mr-2">
                                <feather-icon icon="SearchIcon" /> Filter
                            </b-button>
                            <b-button @click="filterReset" variant="outline-primary" class="mr-2">
                                <feather-icon icon="XIcon" /> Clear Filter
                            </b-button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- here -->
            <section class="mt-32 mt-lg-40">
                <div class="row mx-0">
                    <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0 mr-1">
                        <span class="text-regular-14">Tampilkan</span>
                        <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions"/>
                        <span class="text-regular-14">Entri</span>
                    </b-form-group>
                </div>
                <b-table bordered responsive hover small head-variant="light" :items="items" :fields="fields" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                    <template #thead-top="data">
                        <b-tr>
                            <b-th colspan="5">Data Pendaftaran</b-th>
                            <b-th colspan="6">Data Pasien</b-th>
                            <b-th colspan="4">Data Tujuan</b-th>
                            <b-th></b-th>
                        </b-tr>
                    </template>
                    
                    <template #cell(code)="row">
                        <div class="text-regular-16">
                            <router-link :to="{ path: '/prints/label/'+ row.item.id}">
                                <b-badge size="sm" variant="success-500" class="p-2 font-weight-normal">
                                    <feather-icon icon="TagIcon" /> {{ row.item.code }}
                                </b-badge>
                            </router-link>
                        </div>
                    </template>

                    <template #cell(name)="row">
                        <div>
                            <span class="font-weight-bold">[{{ row.item.alias_name }}]</span> {{ row.item.name }}
                        </div>
                    </template>

                    <template #cell(queue)="row">
                        <div>
                            <template v-if="row.item.health_facility_name == 'Rawat Inap' || row.item.health_facility_name == 'IGD'">
                                <span></span>
                            </template>
                            <template v-else>
                                <span>{{ row.value == 0 ? '' : row.value }}</span>
                            </template>
                        </div>
                    </template>
                    
                    <template #cell(is_new)="data">
                        <div class="text-regular-16">
                            <b-badge size="sm" :variant=" data.value === 'N' ? 'success-600' : 'info-600'" class="p-2 font-weight-normal">
                                <span>
                                    {{ data.value === 'N' ? 'Pasien Lama' : 'Pasien Baru' }}
                                </span>
                            </b-badge>
                        </div>
                    </template>

                    <template #cell(exit_name)="row">
                        <div class="text-regular-16">
                            <div v-if="row.item.exit_id == null">
                                <b-badge size="sm" variant="info-300" class="p-2 font-weight-normal">Sedang Dalam Penanganan</b-badge>
                            </div>
                            <div v-else class="text-regular-16">
                                <b-badge size="sm" variant="success-200" class="p-2 font-weight-normal">{{ row.item.exit_name }}</b-badge>
                            </div>
                        </div>
                    </template>
                                    
                    <template #cell(norm)="row">
                        <div class="text-regular-16">
                            <!-- <router-link :to="{ name: 'PhysiotherapyListProcessView' }"> -->
                            <router-link :to="{ path: '/physiotherapy/list/process/'+ row.item.id}">
                                <b-badge size="sm" variant="info-600" class="p-2 font-weight-normal">
                                    {{ row.value }}
                                </b-badge>
                            </router-link>
                        </div>
                    </template>

                    <template #cell(insurance_name)="row">
                        <div v-for="insurance in row.item.registration_insurance">{{ insurance.insurance_name }} | {{ insurance.company_name }}</div>
                    </template>

                    <template #cell(age)="row">
                        <div>
                            {{ parsed_age(row.item.birth_date) }}
                        </div>
                    </template>

                    <template #cell(register_date)="row">
                        <div>
                            {{ parsed_start_from_date(row.item.register_date) }}
                        </div>
                    </template>

                    <template #cell(action)="row">
                        <div class="d-flex mx-0">
                            <router-link :to="{ path: '/patient/detail/'+ row.item.registration_destination_id}">
                                <b-button size="sm" variant="info-600" class="mr-1">
                                    <feather-icon icon="FileIcon" />
                                </b-button>
                            </router-link>
                            <router-link :to="{ path: '/prints/label/'+ row.item.registration_destination_id}" target="_blank">
                                <b-button size="sm" variant="success-500" class="mr-1">
                                    <feather-icon icon="TagIcon" />
                                </b-button>
                            </router-link>
                            <router-link :to="{ path: '/prints/struk/'+ row.item.registration_destination_id}" target="_blank">
                                <b-button size="sm" variant="primary-500" class="mr-1">
                                    <feather-icon icon="PaperclipIcon" />
                                </b-button>
                            </router-link>
                            <b-button @click="cancelRegistration(row.item)" size="sm" variant="danger">
                                <feather-icon icon="XSquareIcon" />
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
    </div>
</template>
<script>
import TopPhysiotherapyMenu from '@/@core/components/top-menu/TopPhysiotherapyMenu.vue';
import moment from 'moment'
import { mapState } from 'vuex'
import vSelect from 'vue-select'
import DatePicker from 'v-calendar'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
export default {
    name: 'PhysiotherapyListView',
    components: {
        TopPhysiotherapyMenu,
    },
    data() {
        return {
            showFilter: true,
            showCancelModal: false,
            inspection_report: {
                field: [
                    { key: 'registration_code', label: 'Kode Pendaftaran' },
                    { key: 'action', label: 'Aksi' },
                    { key: 'entry_method', label: 'Cara Masuk' },
                    { key: 'payment_method', label: 'Cara Bayar' },
                    { key: 'no_rm', label: 'No RM' },
                    { key: 'patient_name', label: 'Nama Pasien [A/n]' },
                    { key: 'phone_number', label: 'No HP' },
                    { key: 'status', label: 'Status' },
                    { key: 'patient_type', label: 'Jenis Pasien' },
                    { key: 'birth_date', label: 'Tanggal lahir' },
                    { key: 'age', label: 'Umur Pasien' },
                    { key: 'gender', label: 'Kelamin Pasien' },
                    { key: 'installation', label: 'Instalasi Asal' },
                    { key: 'poli', label: 'Poli/Ruangan Asal' },
                    { key: 'doctor', label: 'Dokter' },
                    { key: 'request_date', label: 'Tanggal Request' },
                ],
                item: [
                    {
                        registration_code: '202301030100001',
                        entry_method: 'Pribadi',
                        payment_method: 'Langsung',
                        no_rm: '00013603',
                        patient_name: 'Erviana Dewi Kartika [Nn]',
                        phone_number: '082250814877',
                        status: 'Belum Input Hasil Pemeriksaan',
                        patient_type: 'Pasien Lama',
                        birth_date: '15-06-1993',
                        age: '29 Tahun 6 Bulan 25 Hari',
                        gender: 'Perempuan',
                        installation: 'Instalasi IGD',
                        poli: 'instalasi gawat darurat',
                        doctor: 'Giga, adm',
                        request_date: '05-01-2023 11:18:13',
                    },
                    {
                        registration_code: '202301030100001',
                        entry_method: 'Pribadi',
                        payment_method: 'Langsung',
                        no_rm: '00013603',
                        patient_name: 'Erviana Dewi Kartika [Nn]',
                        phone_number: '082250814877',
                        status: 'Belum Input Hasil Pemeriksaan',
                        patient_type: 'Pasien Baru',
                        birth_date: '15-06-1993',
                        age: '29 Tahun 6 Bulan 25 Hari',
                        gender: 'Perempuan',
                        installation: 'Instalasi IGD',
                        poli: 'instalasi gawat darurat',
                        doctor: 'Giga, adm',
                        request_date: '05-01-2023 11:18:13',
                    },

                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },
            params: {
                limit: 20,
                offset: 0,
                code: '',
                norm: '',
                name: '',
                sex: '',
                how_to_enter_id: '',
                is_new: '',
                health_facility_id: '',
                health_facility_item_id: '',
                doctor_in_charge_id: '',
                date_start: moment(new Date()).format('YYYY-MM-DD'),
                date_end: moment(new Date()).format('YYYY-MM-DD'),
            },
            currentPage:1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            isDisabled: false,
            isDisabledFilter: false,
            show: false,
        }
    },
    watch: {
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage*(value-1)+1
                this.lastRows = this.perPage*(value)+1
                this.params.offset = this.perPage*(value-1)
                this.params.limit = this.perPage
                this.$store.dispatch('physiotherapy/getOrder',this.params)
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.$store.dispatch('physiotherapy/getOrder',this.params)
            }
        },
    },
    mounted() {
        this.inspection_report.totalRows = this.inspection_report.item.length
    },
    computed: {
        ...mapState({ 
            totalRows: state => state.physiotherapy.totalRows, 
            fields: state => state.physiotherapy.fields, 
            items: state => state.physiotherapy.items,
            pageOptions: state => state.physiotherapy.pageOptions,
        }),
        firstRow() {
            return (this.inspection_report.currentPage - 1) * this.inspection_report.perPage + 1
        },
        lastRow() {
            return Math.min(this.inspection_report.currentPage * this.inspection_report.perPage, this.inspection_report.totalRows)
        },
    },
    
    mounted() {
        this.$store.state.totalRows = this.items.length
        this.show = false
    },
    created () {
        this.$store.dispatch('physiotherapy/getOrder',this.params)
        this.$store.dispatch('healthfacility/getParam', 'healthfacility')
        this.$store.dispatch('helper/getParam', 'type_how_to_enter')
        this.$store.dispatch('helper/getParam', 'sex')
        this.$store.dispatch('helper/getParam', 'is_new')
        this.show = true
    },
    methods: {
        patientFiltered(patientItems) {
            this.inspection_report.totalRows = patientItems.length
            this.inspection_report.currentPage = 1
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        cancelRegistration (obj) {
            this.$bvModal
                .msgBoxConfirm('Anda yakin ingin membatalkan nomor registrasi ' + obj.code + ' ?', {
                title: 'Cancel Registration', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    // console.log('selected code: ', obj.code + ' | id: ' + obj.id)
                    this.$store.dispatch('registration/updateIsCancelRegistration', { registration_id: obj.registration_id, purpose: 'cancel' })
                    .then(response => {
                        this.$store.dispatch('physiotherapy/getOrder', this.params)
                        this.notification('Success','CheckIcon','success','You have successfully')
                    },
                    error => {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.log(error);
                    }) 
                }
            })
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
        parsed_start_from_date(input_date) {
            return moment(input_date).format('DD-MM-YYYY');
        },
        filterData () {
            this.show = true
            this.isDisabledFilter = true
            this.$store.dispatch('physiotherapy/getOrder', this.params)
            .then(res => {
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
                type_how_to_enter_id: '',
                is_new: '',
                health_facility_id: '',
                health_facility_item_id: '',
                doctor_in_charge_id: '',
                date_start: moment(new Date()).format('YYYY-MM-DD'),
                date_end: moment(new Date()).format('YYYY-MM-DD'),
            }
            
            this.$store.dispatch('physiotherapy/getOrder',this.params)
        },
    }
}
</script>