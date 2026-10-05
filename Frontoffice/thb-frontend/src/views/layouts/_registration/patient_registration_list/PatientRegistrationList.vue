<template>
    <div>
        <topMenu selectedMenu="menu1" />
        <section class="card mt-32">
            <div class="card-header font-weight-bold bg-primary-200">Filter Data</div>
            <div class="card-body">
                <div class="d-flex justify-content-center">
                    <b-form class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-4">
                                <label class="c-form-label">No Pendaftaran</label>
                                <b-form-input v-model="params.code"></b-form-input>
                            </div>
                            <div class="col-lg-4 mt-12 mt-lg-0">
                                <label class="c-form-label">No RM</label>
                                <b-form-input v-model="params.norm"></b-form-input>
                            </div>
                            <div class="col-lg-4 mt-12 mt-lg-0">
                                <label class="c-form-label">Nama Pasien</label>
                                <b-form-input v-model="params.name"></b-form-input>
                            </div>
                        </div>
                        <div class="row">
                            <!-- <div class="col-lg-4 mt-12">
                                <label class="c-form-label">Jenis Pasien</label>
                                <v-select v-model="params.is_new" aria-readonly="true" :options="OptionsIsNew" :reduce="OptionsIsNew => OptionsIsNew.value" label="title" placeholder="-- Jenis pasien --"></v-select>
                            </div> -->
                            <div class="col-lg-4 mt-12">
                                <label class="c-form-label">SEP</label>
                                <b-form-input v-model="params.sep"></b-form-input>
                            </div>
                            <div class="col-lg-4 mt-12">
                                <label class="c-form-label">Cara Masuk</label>
                                <v-select v-model="params.type_how_to_enter_id" aria-readonly="true" :options="OptionsTypeHowToEnter" :reduce="OptionsTypeHowToEnter => OptionsTypeHowToEnter.value" label="title" placeholder="-- Cara Masuk --"></v-select>
                            </div>
                            <div class="col-lg-4 mt-12">
                                <label class="c-form-label">Jenis Kelamin</label>
                                <v-select v-model="params.sex" :options="OptionsSex" :reduce="OptionsSex => OptionsSex.value" required label="title" placeholder="-- Pilih Kelamin --"></v-select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 mt-12">
                                <label class="c-form-label">Instalasi</label>
                                <v-select v-model="params.health_facility_id" aria-readonly="true" :options="OptionsHealthFacility" :reduce="OptionsHealthFacility => OptionsHealthFacility.value" label="title" placeholder="-- Instalasi --"></v-select>
                            </div>
                            <div class="col-lg-4 mt-12">
                                <label class="c-form-label">Poli</label>
                                <v-select v-model="params.health_facility_item_id" aria-readonly="true" :options="OptionsHealthFacilityItem" :reduce="OptionsHealthFacilityItem => OptionsHealthFacilityItem.value" label="title" placeholder="-- Poliklinik --"></v-select>
                            </div>
                            <div class="col-lg-4 mt-12">
                                <label class="c-form-label">Dokter</label>
                                <v-select v-model="params.doctor_in_charge_id" class="col px-0" aria-readonly="true" :options="OptionsDoctor" :reduce="OptionsDoctor => OptionsDoctor.value" label="title" placeholder="-- Dokter --"></v-select>
                            </div>
                        </div>
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
                                <label class="c-form-label">Jenis Penjamin</label>
                                <v-select v-model="params.insurance_id" :options="OptionsInsurance" :reduce="OptionsInsurance => OptionsInsurance.value" label="title" placeholder="-- Jenis Penjamin --"></v-select>
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
                            <b-button @click="filterDataExcel" variant="primary" :disabled="isDisabledFilter" class="mr-2">
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
            <div class="row mx-0">
                <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0 mr-1">
                    <span class="text-regular-14">Tampilkan</span>
                    <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions"/>
                    <span class="text-regular-14">Entri</span>
                </b-form-group>
            </div>
            <b-table bordered responsive hover small head-variant="light" :items="items" :fields="fields" :per-page="0" :current-page="currentPage" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                <template #thead-top="data">
                    <b-tr>
                        <b-th colspan="4">Data Pendaftaran</b-th>
                        <b-th colspan="5">Data Pasien</b-th>
                        <b-th colspan="2">Data Tujuan</b-th>
                        <b-th></b-th>
                    </b-tr>
                </template>
                
                <template #cell(code)="row">
                    <div class="text-regular-16">
                        <router-link :to="{ path: '/prints/label/'+ row.item.id}">
                            <b-badge size="sm" variant="success-500" class="p-2 font-weight-normal">
                                <feather-icon icon="TagIcon" /> {{ row.item.code }}
                            </b-badge>
                        </router-link> <br>
                        <span class="font-weight-bold">{{ row.item.health_facility_id != "40f06534-07ec-4221-ad9d-dea08196079f" ? row.item.register_date : row.item.checkin_date }}</span>
                    </div>
                </template>

                <template #cell(name)="row">
                    <div>
                        <span class="font-weight-bold">[{{ row.item.alias }}]</span> {{ row.item.name }}
                    </div>
                </template>

                <template #cell(queue)="row">
                    <div>
                        <template v-if="row.item.health_facility_name == 'Rawat Inap' || row.item.health_facility_name == 'IGD'">
                            <span></span>
                        </template>
                        <template v-else>
                            <span>{{ row.item.code_queue }}-{{ row.item.queue }}</span>
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
                        <router-link :to="{ path: '/patient/detail/'+ row.item.registration_destination_id}">
                            <b-badge size="sm" variant="info-600" class="p-2 font-weight-normal">
                                {{ row.value }}
                            </b-badge>
                        </router-link>
                    </div>
                </template>

                <template #cell(insurance_name)="row">
                    <div>
                        {{ row.item.how_to_pay }} | {{ row.item.insurance_name }}
                        <b-badge v-if="row.item.is_cob == 1"class="rounded text-neutral-500 text-uppercase border border-warning-500 px-10 py-1" variant="warning-200">COB</b-badge>
                        {{ row.item.sep }}
                    </div>
                </template>

                <template #cell(age)="row">
                    <div>
                        {{ row.item.sex_name }} <br>
                        {{ parsed_age(row.item.birth_date) }}
                    </div>
                </template>

                <template #cell(health_facility_item_name)="row">
                    <div>
                        {{ row.item.health_facility_item_name }} <br>
                        <span class="font-weight-bold">{{ row.item.doctor_in_charge_name }}</span>
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
                    <span class="text-regular-14">Menampilkan {{ firstRows }} hingga <template
                            v-if="lastRows < totalRows">{{ lastRows }}</template> <template v-else>{{ totalRows
                            }}</template> dari {{ totalRows }} entri</span>
                    <div class="ml-auto">
                        <b-pagination v-model="currentPage" :total-rows="totalRows" :per-page="perPage"
                            align="fill" size="sm" class="my-0"></b-pagination>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
<script>
import moment from 'moment'
import topMenu from '@/@core/components/top-menu/topMenu.vue'
import { mapState } from 'vuex'
import vSelect from 'vue-select'
import DatePicker from 'v-calendar'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default {
    components: { topMenu, vSelect, DatePicker, ToastificationContent },
    data() {
        return {
            urlRoot: process.env.VUE_APP_ROOT_API,
            purpose:'',
            params: {
                offset: 0,
                limit: 20,
                code: '',
                norm: '',
                name: '',
                sex: '',
                sep: '',
                how_to_enter_id: '',
                is_new: '',
                health_facility_id: '',
                health_facility_item_id: '',
                doctor_in_charge_id: '',
                date_start: moment(new Date()).format('YYYY-MM-DD'),
                date_end: moment(new Date()).format('YYYY-MM-DD'),
                insurance_id: '',
                company_id: '',
            },
            currentPage:1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            isDisabled: false,
            isDisabledFilter: false,
            show: false,
            fields: [
                { sortable: true, key: 'code', label: 'No Pendaftaran', thStyle: { minWidth: '140px' } },
                { sortable: true, key: 'insurance_name', label: 'Cara Bayar', thStyle: { minWidth: '150px' } },
                { sortable: true, key: 'how_to_enter_name', label: 'Cara Masuk', thStyle: { minWidth: '140px' } },
                { sortable: true, key: 'exit_name', label: 'Status Pasien', thStyle: { minWidth: '140px' } },
                { sortable: true, key: 'norm', label: 'No RM' },
                { sortable: true, key: 'name', label: 'Nama', thStyle: { minWidth: '140px' } },
                { sortable: true, key: 'is_new', label: 'Jenis Pasien', thStyle: { minWidth: '120px' } },
                { sortable: true, key: 'birth_date', label: 'Tanggal Lahir', thStyle: { minWidth: '120px' } },
                { sortable: true, key: 'age', label: 'Kelamin&Umur', thStyle: { minWidth: '140px' } },
                { sortable: true, key: 'health_facility_item_name', label: 'Poli/Ruangan&DPJP', thStyle: { minWidth: '140px' } },
                { sortable: true, key: 'queue', label: 'No Urut', thStyle: { minWidth: '80px' } },
                { sortable: true, key: 'action', label: 'Aksi' },
            ],
        }
    },
    watch: {
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage * (value - 1) + 1
                this.lastRows = this.perPage * (value)
                this.params.offset = this.perPage * (value - 1)
                this.params.limit = this.perPage
                this.$store.dispatch('registration/getListFastReg',this.params)
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.$store.dispatch('registration/getListFastReg',this.params)
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
            start: state => state.registration.fast.start, 
            totalRows: state => state.registration.fast.totalRows, 
            items: state => state.registration.fast.items,
            pageOptions: state => state.registration.pageOptions,

            OptionsHealthFacility: state => state.healthfacility.options.healthfacility,
            OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,
            OptionsDoctor: state => state.employee.options.employee,
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
        // this.$store.dispatch('registration/getListFastReg',this.params)
        this.$store.dispatch('healthfacility/getParam', 'healthfacility')
        this.$store.dispatch('helper/getParam', 'type_how_to_enter')
        this.$store.dispatch('helper/getParam', 'sex')
        this.$store.dispatch('helper/getParam', 'is_new')
        this.show = true
    },
    methods: {
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        cancelRegistration (obj) {
            this.$bvModal
                .msgBoxConfirm(<b-form-input vModel={this.purpose} />, {
                title: 'Cancel Registration', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    // console.log('selected code: ', obj.code + ' | id: ' + obj.id)
                    this.$store.dispatch('registration/updateIsCancelRegistration', { registration_id: obj.id, purpose: 'cancel', reason: this.purpose })
                    .then(response => {
                        this.$store.dispatch('registration/getListFastReg', this.params)
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

            try {
                this.isDisabledFilter = true
                this.$store.dispatch('registration/getListFastReg', this.params)
                .then(res => {
                    this.show = false
                    this.isDisabledFilter = false
                    if (res.status != 200) {
                        this.isDisabledFilter = false
                    }
                })
                
            } catch (error) {
                this.show = false
                this.isDisabledFilter = false
            }
        },
        filterDataExcel () {
            let queryParamsq = [];
            
            if (this.params.offset) {
                queryParamsq.push(`offset=${this.params.offset}`)
            } else if (this.params.offset === 0) {
                queryParamsq.push(`offset=${this.params.offset}`)
            }
            
            if (this.params.limit) {
                queryParamsq.push(`limit=${this.params.limit}`)
            }

            if (this.params.code) {
                queryParamsq.push(`code=${this.params.code}`)
            }

            if (this.params.norm) {
                queryParamsq.push(`norm=${this.params.norm}`)
            }

            if (this.params.name) {
                queryParamsq.push(`name=${this.params.name}`)
            }

            if (this.params.sep) {
                queryParamsq.push(`sep=${this.params.sep}`)
            }

            if (this.params.is_new) {
                if (this.params.is_new == 1) {
                queryParamsq.push(`is_new=N`)
                } else if (this.params.is_new == 2) {
                queryParamsq.push(`is_new=Y`)
                }
            }

            if (this.params.type_how_to_enter_id) {
                queryParamsq.push(`type_how_to_enter_id=${this.params.type_how_to_enter_id}`)
            }

            if (this.params.sex === 1 || this.params.sex === 0) {
                queryParamsq.push(`sex=${this.params.sex}`)
                // queryParamsq.push(`sex=${this.params.sex}`)
            }

            if (this.params.health_facility_id) {
                queryParamsq.push(`health_facility_id=${this.params.health_facility_id}`)
            }

            if (this.params.health_facility_item_id) {
                queryParamsq.push(`health_facility_item_id=${this.params.health_facility_item_id}`)
            }

            if (this.params.doctor_in_charge_id) {
                queryParamsq.push(`doctor_in_charge_id=${this.params.doctor_in_charge_id}`)
            }

            if (this.params.insurance_id) {
                queryParamsq.push(`insurance_id=${this.params.insurance_id}`)
            }

            if (this.params.company_id) {
                queryParamsq.push(`company_id=${this.params.company_id}`)
            }

            if (this.params.doctor_in_charge_id) {
                queryParamsq.push(`doctor_in_charge_id=${this.params.doctor_in_charge_id}`)
            }
            
            queryParamsq.push(`is_checkout=${this.params.is_checkout}`)

            if (this.params.date_start && this.params.date_end) {
                queryParamsq.push(`date_start=${this.params.date_start}`)
                queryParamsq.push(`date_end=${this.params.date_end}`)
            }

            let paramsall = queryParamsq.join('&')
            window.open(this.urlRoot+`mr/registration/report?${paramsall}`)
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
                date_start: moment(new Date()).format('YYYY-MM-DD'),
                date_end: moment(new Date()).format('YYYY-MM-DD'),
                insurance_id: '',
                company_id: '',
            }
            
            this.$store.dispatch('registration/getListFastReg',this.params)
        },
    },
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>
