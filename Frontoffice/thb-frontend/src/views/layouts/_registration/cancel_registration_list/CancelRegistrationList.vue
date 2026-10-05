<template>
    <div>
        <topMenu selectedMenu="menu3" />
        <section class="card mt-32">
            <div class="card-header font-weight-bold bg-primary-200">Filter Data</div>
            <div class="card-body">
                <div class="d-flex justify-content-center">
                    <b-form class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-4 mt-12 mt-lg-0">
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
                            <div class="col-lg-4 mt-12">
                                <label class="c-form-label">Jenis Pasien</label>
                                <v-select v-model="params.is_new" aria-readonly="true" :options="OptionsIsNew" :reduce="OptionsIsNew => OptionsIsNew.value" label="title" placeholder="-- Jenis pasien --"></v-select>
                            </div>
                            <div class="col-lg-4 mt-12">
                                <label class="c-form-label">Cara Masuk</label>
                                <v-select v-model="params.type_how_to_enter_id" aria-readonly="true" :options="OptionsTypeHowToEnter" :reduce="OptionsTypeHowToEnter => OptionsTypeHowToEnter.value" label="title" placeholder="-- Cara Masuk --"></v-select>
                            </div>
                            <div class="col-lg-4 mt-12">
                                <label class="c-form-label">Jenis Kelamin</label>
                                <v-select v-model="params.sex" :options="OptionsSex" :reduce="OptionsSex => OptionsSex.value" required label="title" placeholder="-- Pilih Kelamin --"></v-select>
                            </div>
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
                            <div class="col-lg-8 px-0 mt-12">
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
                        </div>
                        <div class="d-flex mt-24">
                            <b-button @click="filterData" :disabled="isDisabledFilter" variant="primary" class="mr-2">
                                <feather-icon icon="SearchIcon" /> Filter
                            </b-button>
                            <b-button @click="filterReset" :disabled="isDisabledFilter" variant="outline-primary" class="mr-2">
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
            <b-table bordered responsive hover small head-variant="light" :items="items" :fields="fields" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                <template #thead-top="data">
                    <b-tr>
                        <b-th colspan="6">Data Pendaftaran</b-th>
                        <b-th colspan="6">Data Pasien</b-th>
                        <!-- <b-th colspan="4">Data Tujuan</b-th> -->
                        <!-- <b-th></b-th> -->
                    </b-tr>
                </template>
                
                <template #cell(code)="row">
                    <div class="text-regular-16">
                        <b-badge size="sm" variant="success-500" class="p-2 font-weight-normal">
                            <feather-icon icon="TagIcon" /> {{ row.item.code }}
                        </b-badge>
                    </div>
                </template>

                <template #cell(name)="row">
                    <div>
                        <span class="font-weight-bold">[{{ row.item.alias_name }}]</span> {{ row.item.name }}
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
                        <b-badge size="sm" variant="info-600" class="p-2 font-weight-normal">
                            {{ row.value }}
                        </b-badge>
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
                        {{ parsed_age(row.item.birth_date) }}
                    </div>
                </template>

                <template #cell(register_date)="row">
                    <div>
                        {{ parsed_start_from_date(row.item.register_date) }}
                    </div>
                </template>

                <!-- <template #cell(action)="row">
                    <div class="d-flex mx-0">
                        <b-button @click="restoreRegistration(row.item)" size="sm" variant="info-600">
                            <feather-icon icon="RefreshCcwIcon" /> Restore
                        </b-button>
                    </div>
                </template> -->
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

            fields: [
                { sortable: true, key: 'code', label: 'No Pendaftaran', thStyle: { minWidth: '140px' } },
                { sortable: true, key: 'insurance_name', label: 'Cara Bayar', thStyle: { minWidth: '150px' } },
                { sortable: true, key: 'type_how_to_enter_name', label: 'Cara Masuk', thStyle: { minWidth: '140px' } },
                { sortable: true, key: 'exit_name', label: 'Status Pasien', thStyle: { minWidth: '140px' } },
                { sortable: true, key: 'norm', label: 'No RM' },
                { sortable: true, key: 'cancel_reason', label: 'Alasan Batal' },
                { sortable: true, key: 'name', label: 'Nama', thStyle: { minWidth: '140px' } },
                { sortable: true, key: 'is_new', label: 'Jenis Pasien', thStyle: { minWidth: '120px' } },
                { sortable: true, key: 'birth_date', label: 'Tanggal Lahir', thStyle: { minWidth: '120px' } },
                { sortable: true, key: 'age', label: 'Kelamin&Umur', thStyle: { minWidth: '140px' } },
                { sortable: true, key: 'health_facility_item_name', label: 'Poli/Ruangan&DPJP', thStyle: { minWidth: '140px' } },
                { sortable: true, key: 'queue', label: 'No Urut', thStyle: { minWidth: '80px' } },
                // { sortable: true, key: 'action', label: 'Aksi' },
            ],

        }
    },
    watch: {
        // 'items': function(val){
        //     if (val) {
        //         console.log('val:', val);
        //     }  else {
        //         console.log('nothong items');
        //     }
        // },
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage*(value-1)+1
                this.lastRows = this.perPage*(value)+1
                this.params.offset = this.perPage*(value-1)
                this.params.limit = this.perPage
                this.$store.dispatch('registration/getCancelledAll',this.params)
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.$store.dispatch('registration/getCancelledAll',this.params)
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
    },
    computed: {
        ...mapState({ 
            totalRows: state => state.registration.totalRows, 
            items: state => state.registration.items,
            pageOptions: state => state.registration.pageOptions,

            OptionsHealthFacility: state => state.healthfacility.options.healthfacility,
            OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,
            OptionsDoctor: state => state.employee.options.employee,
            OptionsTypeHowToEnter: state => state.helper.options.howtoenter,
            OptionsSex: state => state.helper.options.sex,
            OptionsIsNew: state => state.helper.options.isNew,
        }),
    },
    mounted() {
        this.$store.state.totalRows = this.items.length
        this.show = false
    },
    created () {
        this.$store.dispatch('registration/getCancelledAll',this.params)
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
        restoreRegistration (obj) {
            this.$bvModal
                .msgBoxConfirm('Anda yakin ingin melakukan restore nomor registrasi ' + obj.code + ' ?', {
                title: 'Restore Registration', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('registration/updateIsCancelRegistration', { registration_id: obj.registration_id, purpose: 'restore' })
                    .then(response => {
                        this.$store.dispatch('registration/getCancelledAll', this.params)
                        this.notification('Success','CheckIcon','success','You have successfully')
                    },
                    error => {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
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
            this.$store.dispatch('registration/getCancelledAll', this.params)
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
            
            this.$store.dispatch('registration/getCancelledAll',this.params)
        },
    },
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>
