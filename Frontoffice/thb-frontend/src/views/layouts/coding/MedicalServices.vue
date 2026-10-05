<template>
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
            <table class="table">
                <thead class="text-center text-bold-14">
                    <tr>
                        <th>SEP</th>
                        <th>RANAP/RAJAL</th>
                        <th>NAMA</th>
                        <th>INACBG</th>
                        <th>TARIF INACBG</th>
                        <th>TARIF RS</th>
                        <th>PERSEN</th>
                        <!-- <th>TOTAL JASDOK</th> -->
                        <th>JM</th>
                    </tr>
                </thead>
                <template v-for="(item_g, index_g) in group">
                    <tr>
                        <td><label><b>{{ item_g.doctor_in_charge_name }}</b></label></td>
                        <td colspan="6" class="text-right"><b>TOTAL</b></td>
                        <td class="text-right">
                            {{ item_g.total_cost_doctor | currency }}
                            <!-- {{ (((item.cost_inacbg-((item.cost_inacbg*item.percentage)/100))*20)/100) | currency }} -->
                        </td>
                    </tr>
                    <tr class="item" v-for="(item, index) in items" v-if="item_g.doctor_in_charge_name === item.doctor_in_charge_name">
                        <td>{{ item.sep }}</td>
                        <td>{{ item.health_facility_id }}</td>
                        <td>{{ item.name }}</td>
                        <td>{{ item.desc_inacbg }}</td>
                        <td class="text-right">{{ item.cost_inacbg | currency }}</td>
                        <td class="text-right">{{ item.cost_hostpital | currency }}</td>
                        <td class="text-right">{{ item.health_facility_id === 'RAWAT INAP' ? ((item.cost_inacbg*item.percentage)/100) : '' | currency }}</td>
                        <!-- <td class="text-right">{{ item.health_facility_id === 'RAWAT INAP' ? item.cost_inacbg-((item.cost_inacbg*item.percentage)/100) : item.cost_inacbg-((item.cost_inacbg*20)/100) | currency }}</td> -->
                        <td class="text-right">{{ item.health_facility_id === 'RAWAT INAP' ? (((item.cost_inacbg-((item.cost_inacbg*item.percentage)/100))*20)/100) : ((item.cost_inacbg*item.percentage)/100) | currency }}</td>
                    </tr>
                    <tr>
                        <td><label><b>{{ item_g.doctor_in_charge_name }}</b></label></td>
                        <td colspan="6" class="text-right"><b>TOTAL</b></td>
                        <td class="text-right">
                            {{ item_g.total_cost_doctor | currency }}
                            <!-- {{ (((item.cost_inacbg-((item.cost_inacbg*item.percentage)/100))*20)/100) | currency }} -->
                        </td>
                    </tr>
                </template>
            </table>
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
            OptionsHealth:[
                {value:'RAWAT INAP',title:'RAWAT INAP'},
                {value:'RAWAT JALAN',title:'RAWAT JALAN'},
            ],
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
                health_facility_id: '',
                health_facility_item_id: '',
                doctor_in_charge_id: '',
                doctor_in_charge_name: '',
                date_start: moment(new Date()).format('YYYY-MM-DD'),
                date_end: moment(new Date()).format('YYYY-MM-DD'),
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
            totalRows: state => state.costing.ms.totalRows, 
            items: state => state.costing.ms.items,
            group: state => state.costing.ms.group,
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
                    this.$store.dispatch('registration/updateIsCancelRegistration', { registration_id: obj.registration_id, purpose: 'cancel', reason: this.purpose })
                    .then(response => {
                        this.$store.dispatch('costing/getAllFast', this.params)
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
            this.$store.dispatch('costing/getAllFast', this.params)
            .then(res => {
                this.show = false
                this.isDisabledFilter = false
            },
            err => {
                this.show = false
                this.isDisabledFilter = false
            })
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
            return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'IDR' }).format(value)
        }
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>
