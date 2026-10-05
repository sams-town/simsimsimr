<template>
    <div>
        <TopInpatientMenu selectedMenu="menu3" />
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
                                <label class="c-form-label">Ruangan</label>
                                <v-select v-model="params.health_facility_item_id" aria-readonly="true" :options="OptionsHealthFacilityItem" :reduce="OptionsHealthFacilityItem => OptionsHealthFacilityItem.value" label="title" placeholder="-- Ruangan --"></v-select>
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
                        <div class="d-flex mt-24">
                            <b-button :disabled="isDisabledFilter" @click="filterData" variant="primary" class="mr-2">
                                <feather-icon icon="SearchIcon" /> Filter
                            </b-button>
                            <b-button :disabled="isDisabledFilter" @click="filterReset" variant="outline-primary" class="mr-2">
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
            <b-table bordered responsive hover small head-variant="light" :items="regList" :fields="fields" :per-page="0" :current-page="currentPage" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                <template #thead-top="data">
                    <b-tr>
                        <b-th colspan="4">Data Pendaftaran</b-th>
                        <b-th colspan="3">Data Pasien</b-th>
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
                    </div>
                </template>

                <template #cell(name)="row">
                    <div>
                        <span class="font-weight-bold">[{{ row.item.alias_name }}]</span> {{ row.item.name }}
                        <br><br>
                        {{ row.item.sex_name }} 
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
                        <router-link :to="{ path: '/patient/detail/'+ row.item.id}">
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
                        {{ row.item.birth_date }}
                        <br>
                        {{ parsed_age(row.item.birth_date) }}
                    </div>
                </template>

                <template #cell(health_facility_item_name)="row">
                    <div>
                        {{ row.item.room_name }}, BED - {{ row.item.bed_name }} <br>
                        <span class="font-weight-bold">{{ row.item.doctor_in_charge_name }}</span>
                    </div>
                </template>

                <template #cell(register_date)="row">
                    <div>
                        {{ parsed_start_from_date(row.item.register_date) }}
                    </div>
                </template>

                <template #cell(action)="row">
                    <div class="d-flex mx-0">
                        <b-button size="sm" variant="info-600" @click="openModal(row.item.id, row.item.id)" class="mr-1">
                            <feather-icon icon="ExternalLinkIcon" /> Checkoutoooo
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
        <b-modal v-model="confirmModal" title="Checkout Pasien" >
            <div>
                <label class="c-form-label">Alasan Checkout</label>
                <v-select v-model="form.purpose_type_id" :options="exit" :reduce="exit => exit.value" label="text" placeholder="-- Alasan --"></v-select>
            </div>
            <div class="mt-12">
                <label class="c-form-label">Tanggal Checkout</label>
                <b-input-group>
                    <b-input type="date" v-model="temp.date" />
                    <b-input type="time" v-model="temp.time" />
                </b-input-group>
            </div>

            <template #modal-footer>
                <div class="w-100 d-flex justify-content-end">
                    <b-button variant="outline-primary" class="mr-2" @click="closeModal">Cancel</b-button>
                    <b-button variant="primary" @click="cancelRegistration">Konfirmasi Checkout</b-button>
                </div>
            </template>
        </b-modal>
    </div>
</template>
<script>
import moment from 'moment'
import TopInpatientMenu from '@/@core/components/top-menu/TopInpatientMenu.vue';
import { mapState } from 'vuex'
import vSelect from 'vue-select'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default {
    components: { TopInpatientMenu, vSelect, ToastificationContent },
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
                health_facility_id: '40f06534-07ec-4221-ad9d-dea08196079f',
                health_facility_item_id: '',
                doctor_in_charge_id: '',
            },
            regList: [],
            exit:[
                { value: '807ca005-5827-4e19-9c97-0bc3d4e924ca', text: 'Atas Persetujuan Dokter' },
                { value: '4a6d28c4-e020-425f-91e1-d39699cf679b', text: 'Meninggal' },
                { value: 'a1631acc-6a94-414d-95de-a9faea0f1c2f', text: 'Dirujuk' },
                { value: 'ae0aabfa-1600-443f-8a60-62f9e76b1608', text: 'Permintaan Sendiri' }
            ],
            form: {
                id: '',
                registration_id: '',
                purpose_type_id: '',
                exit_date: null
            },
            temp: {
                date: moment(new Date()).format('YYYY-MM-DD'),
                time: moment(new Date()).format('HH:mm'),
            },
            // purpose:'',
            currentPage:1,
            perPage: 20,
            totalRows: 0,
            firstRows: 1,
            lastRows: 20,
            isDisabled: false,
            isDisabledFilter: false,
            show: false,
            confirmModal: false,
        }
    },
    watch: {
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage*(value-1)+1
                this.lastRows = this.perPage*(value)+1
                this.params.offset = this.perPage*(value-1)
                this.params.limit = this.perPage
                this.getAllList()
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.getAllList()
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
            fields: state => state.registration.fieldsInpatient, 
            pageOptions: state => state.registration.pageOptions,
            // totalRows: state => state.registration.totalRows, 
            // items: state => state.registration.items,

            OptionsHealthFacility: state => state.healthfacility.options.healthfacility,
            OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,
            OptionsDoctor: state => state.employee.options.employee,
            OptionsTypeHowToEnter: state => state.helper.options.howtoenter,
            OptionsSex: state => state.helper.options.sex,
            OptionsIsNew: state => state.helper.options.isNew,
        }),
    },
    
    methods: {
        getAllList() {
            this.$store.dispatch('registration/getListInpatient',this.params).then((response) => {
                this.regList = response.rows
                this.totalRows = response.iTotal

                this.show = false
                this.isDisabledFilter = false
            })

        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        openModal(id, registration_id){
            this.confirmModal = true
            if(this.confirmModal == true){
                this.form.id = id
                this.form.registration_id = registration_id
            }
        },
        closeModal(){
            this.confirmModal = false
            
            if(this.confirmModal == false){
                this.form.id = ''
                this.form.exit_date = null
                this.temp.date = moment(new Date()).format('YYYY-MM-DD')
                this.temp.time = moment(new Date()).format('HH:mm')
                this.form.purpose_type_id = '' 
                this.form.registration_id = ''
            }
            // console.log('form', this.form);
        },
        cancelRegistration () {
            this.form.exit_date = `${this.temp.date || moment(new Date()).format('YYYY-MM-DD')} ${this.temp.time || moment(new Date()).format('HH:mm')}`;
            // console.log('form:', this.form);
            this.$store.dispatch('registration/checkout', this.form)
            .then(response => {
                this.notification('Success','CheckIcon','success','You have successfully')
                this.getAllList()

                this.form.id = ''
                this.form.registration_id = ''
                this.form.purpose_type_id = ''
                this.form.exit_date = null
                this.temp.date = moment(new Date()).format('YYYY-MM-DD')
                this.temp.time = moment(new Date()).format('HH:mm')

                this.confirmModal = false
            },
            error => {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.log(error);
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
                this.getAllList()
            } catch (error) {
                console.error('Error at: ', error);
                this.isDisabledFilter = false
                
            }
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
                health_facility_id: '40f06534-07ec-4221-ad9d-dea08196079f',
                health_facility_item_id: '',
                doctor_in_charge_id: '',
                date_start: moment(new Date()).format('YYYY-MM-DD'),
                date_end: moment(new Date()).format('YYYY-MM-DD'),
            }
            
            this.getAllList()
        },
    },

    mounted() {
        this.show = false
        // this.$store.state.totalRows = this.items.length
        // console.log('this.items.length: ', this.items.length);
    },
    
    created () {
        this.getAllList()
        
        this.$store.dispatch('healthfacility/getParam', 'healthfacility')
        this.$store.dispatch('helper/getParam', 'type_how_to_enter')
        this.$store.dispatch('helper/getParam', 'sex')
        this.$store.dispatch('helper/getParam', 'is_new')
        this.$store.dispatch('healthfacilityitem/getParam', this.params.health_facility_id)
        this.show = true
    },
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>
