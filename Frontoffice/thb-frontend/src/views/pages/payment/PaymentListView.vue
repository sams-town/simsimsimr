<!-- <template>
    <div id="app">
      <component :is="layout">
        <router-view />
      </component>
    </div>
  </template>
  
<script>
const Layout = () => import('@/views/layouts/Main.vue')
export default {
    components: {
        // Layouts
        Layout
    },
    computed: {
        layout() {
            return `layout`
        }
    }
}
</script>
<template>
    <div>
        <Layout selected_menu="menu3"/>
        <section class="content px-0">
            <div class="row mx-0">
                <div class="card col px-0 border-0">
                    <div class="body-header">
                        <ul class="breadcrumb float-md-right rounded-pill">
                            <li class="breadcrumb-item"><a href="index.html"><b-icon icon="house-fill"></b-icon></a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Sample Pages</a></li>
                            <li class="breadcrumb-item active">Stater Page</li>
                        </ul>
                        <h4>List Pembayaran</h4>
                    </div>
                    <div class="container-fluid">
                        <div class="row clearfix">
                            <div class="col-lg-12 py-lg-16">
                                <PatientPayment />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
<script>
import PatientPayment from '@/views/layouts/payment/PatientPayment.vue';
  
  export default {
    name: 'PatientDetailView',
    components: {
      Layout, PatientPayment
    },
    
  }
</script> -->
<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Kasir' },
                { text: 'List Pasien' }
            ]"
        >
            <section class="">
                <TopPaymentMenu selectedMenu="menu1" class="mt-2" />
                <div class="mt-32">
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
                                        <b-button @click="exportData" variant="primary" :disabled="isDisabledFilter" class="mr-2">
                                            <feather-icon icon="SearchIcon" /> Export
                                        </b-button>
                                        <b-button @click="filterReset" variant="outline-primary" class="mr-2">
                                            <feather-icon icon="XIcon" /> Clear Filter
                                        </b-button>
                                    </div>
                                </b-form>
                            </div>
                        </div>
                    </section>
                    <div class="mt-32 mt-lg-40">
                        <div class="row mx-0">
                            <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0 mr-1">
                                <span class="text-regular-14">Tampilkan</span>
                                <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions"/>
                                <span class="text-regular-14">Entri</span>
                            </b-form-group>
                        </div>
                            <b-table bordered responsive small head-variant="light" :items="items" :fields="fields" :tbody-tr-class="rowClass" :per-page="0" :current-page="currentPage"  thead-class="text-bold-16" class="mt-12 text-center">
                            <template #thead-top="data">
                            <b-tr>
                                <b-th colspan="3">Data Pendaftaran</b-th>
                                <b-th colspan="3">Data Pasien</b-th>
                                <b-th>Data Tujuan</b-th>
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
                                <span class="font-weight-bold">{{ row.item.checkin_date }}</span>
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
                                <router-link :to="{ path: '/payment/detail/'+ row.item.registration_id}">
                                    <b-badge size="sm" variant="info-600" class="p-2 font-weight-normal">
                                        {{ row.value }}
                                    </b-badge>
                                </router-link>
                            </div>
                        </template>

                        <template #cell(insurance_name)="row">
                            <div v-for="insurance in row.item.registration_insurance">
                                {{ insurance.insurance_name }} | {{ insurance.company_name }}
                                <b-badge v-if="insurance.is_cob == 1"class="rounded text-neutral-500 text-uppercase border border-warning-500 px-10 py-1" variant="warning-200">COB</b-badge>
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

                        <template #cell(register_date)="row">
                            <div>
                                {{ parsed_start_from_date(row.item.register_date) }}
                            </div>
                        </template>
                            <template #cell(action)="row">
                                <div style="width: 200px;">
                                    <router-link :to="{ path: '/payment/detail/'+ row.item.registration_id}">
                                        <b-button size="sm" variant="info-600"><b-icon icon="file-earmark" /> Billing</b-button>
                                    </router-link>
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
                    </div>
                </div>
                <!-- here -->
            </section>
        </Layout>
    </div>
</template>

<script>
import TopPaymentMenu from '@/@core/components/top-menu/TopPaymentMenu.vue';
import moment from 'moment'
import { mapState } from 'vuex'
import vSelect from 'vue-select'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
export default {
    name: 'PatientPaymentView',
    components: {
        TopPaymentMenu, vSelect, ToastificationContent
    },
    data() {
        return {
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
        }
    },
    watch: {
        // 'items': function( val) {
        //     console.log(val)
        // },
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage * (value - 1) + 1
                this.lastRows = this.perPage * (value)
                this.params.offset = this.perPage * (value - 1)
                this.params.limit = this.perPage
                this.$store.dispatch('payment/getAll',this.params)
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.$store.dispatch('payment/getAll',this.params)
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
    mounted() {
        this.$store.state.totalRows = this.items.length
        this.show = false
    },
    methods: {
        rowClass(item, role) {
            if (!item || role !== 'row') return
            if (item.inv === null) return 'table-warning-400'
            if (item.inv) return 'table-w3green'
        },

        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
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
            this.$store.dispatch('payment/getAll', this.params)
            .then(res => {
                this.show = false
                this.isDisabledFilter = false
            },
            err => {
                this.show = false
                this.isDisabledFilter = false
            })
        },
        exportData () {
            window.open("http://localhost/be-ci-zeemarr/v1/tr/transaction_invoice/excel?id=2b0c7b0e-5e58-43c0-abf3-3cf815ca74a3")
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
                insurance_id: '',
                company_id: '',
            }
            
            this.$store.dispatch('payment/getAll',this.params)
        },
    },
    computed: {
        ...mapState({ 
            totalRows: state => state.payment.totalRows, 
            fields: state => state.payment.fields, 
            items: state => state.payment.items, 
            pageOptions: state => state.payment.pageOptions, 

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
    
    created () {
        this.$store.dispatch('payment/getAll', this.params)
        this.$store.dispatch('healthfacility/getParam', 'healthfacility')
        this.$store.dispatch('helper/getParam', 'type_how_to_enter')
        this.$store.dispatch('helper/getParam', 'sex')
        this.$store.dispatch('helper/getParam', 'is_new')
        this.show = true
    },
}
</script>