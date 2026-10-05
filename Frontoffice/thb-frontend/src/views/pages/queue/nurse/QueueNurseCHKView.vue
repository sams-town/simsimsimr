<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Checkout' }
            ]"
        >
            <section class="">
                <section class="">
                    <div class="d-flex justify-content-between">
                        <div class="row w-100 align-items-end">
                            <div class="col-lg-2">
                                <label class="fs-13 mb-0">Tanggal Mulai (check-in)</label>
                                <b-form-input v-model="params.date_start" @input="onListFast" size="sm" type="date" />
                            </div>
                            <div class="col-lg-2">
                                <label class="fs-13 mb-0">Tanggal Akhir (check-in)</label>
                                <b-form-input v-model="params.date_end" @input="onListFast" size="sm" type="date" />
                            </div>
                            <div class="col-lg-3">
                                <label class="fs-13 mb-0">Cari DPJP</label>
                                <v-select class="select-size-sm" v-model="params.doctor_in_charge_id" @input="onListFast" :options="doctor_list" :reduce="item => item.value" label="title" placeholder="pilih dpjp"></v-select>
                            </div>
                            <div class="col-lg-3">
                                <label class="fs-13 mb-0">Cari Pasien</label>
                                <div class="d-flex align-items-center position-relative">
                                    <b-form-input v-model="searchQuery" @input="onListFast" class="pl-5" size="sm" autocomplete="off" placeholder="masukkan nama atau NRM" />
                                    <b-icon icon="search" class="position-absolute pl-3 text-neutral-400" />
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <label class="fs-13 mb-0">Cara Pulang</label>
                                <v-select class="select-size-sm" v-model="params.exit_id" @input="onListFast" :options="OptionsExit" :reduce="item => item.value" label="title" placeholder="pilih cara pulang"></v-select>
                            </div>
                            <!-- <div class="col-lg-3">
                                <label class="fs-13 mb-0">Cari Poliklinik</label>
                                <v-select class="select-size-sm" v-model="params.health_facility_item_id" @input="onListFast" :options="OptionsHealthFacilityItem" :reduce="item => item.value" label="title" placeholder="pilih poliklinik"></v-select>
                            </div> -->
                        </div>
                        <div class="d-flex align-items-end">
                            <div class="d-flex align-items-center">
                                <span class="fs-12 mr-1">Tampilkan</span>
                                <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions" />
                            </div>
                        </div>
                    </div>
                    <b-table bordered fixed small head-variant="light" :items="ListItems" :fields="CheckoutTable" :per-page="0" :current-page="currentPage" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                        <template #cell(queue)="row">{{ row.item.code_queue }}-{{ row.item.queue }}</template>

                        <template #cell(name)="row">
                            <div class="text-left">
                                <div class="font-weight-bold">{{ row.item.alias }}. {{ row.item.name }}</div>
                                <div>{{ localDate(row.item.birth_date) }} ({{ parsed_age(row.item.birth_date) }}) {{ row.item.norm }}</div>
                            </div>
                        </template>
                        <template #cell(company_name)="row">
                            <div>
                                <template>{{ row.item.insurance_id == 2 || row.item.insurance_id == 3 ? row.item.company_name : row.item.insurance_name }}</template>
                                <b-badge v-if="row.item.is_cob == 1" class="rounded text-neutral-500 text-uppercase border border-warning-500 px-10 py-1" variant="warning-200">COB</b-badge>
                            </div>
                        </template>
                        <template #cell(encounter)="row">
                            <div class="d-flex justify-content-center">
                                <template v-if="userData.user_group.name === 'Doctor' || userData.user_group.name === 'Kasir' || userData.user_group.name === 'Fisioterapi' || userData.user_group.name === 'RM'  || userData.employee_id == 'd77142c4-4777-47d8-bd0c-fd7dfd134af4'">
                                    <router-link :to="{ path: '/patient/doctor/' + row.item.registration_destination_id }">
                                        <b-button size="sm" variant="primary" class="mr-1">
                                            <feather-icon icon="FileIcon" />
                                        </b-button>
                                    </router-link>
                                </template> 

                                <template v-if="userData.user_group.name === 'Nurse' || userData.user_group.name === 'Kasir' || userData.user_group.name === 'Fisioterapi' || userData.employee_id == 'd77142c4-4777-47d8-bd0c-fd7dfd134af4'">
                                    <router-link :to="{ path: '/patient/nurse/' + row.item.registration_destination_id }">
                                        <b-button size="sm" variant="success-500" class="mr-1" >
                                            <feather-icon icon="FileIcon" />
                                        </b-button>
                                    </router-link>
                                    
                                    <router-link :to="{ path: '/prints/label/'+ row.item.registration_destination_id}">
                                        <b-button size="sm" variant="outline-success-500" class="mr-1">
                                        <feather-icon icon="TagIcon" />
                                        </b-button>
                                    </router-link>
                                </template>

                                <template v-if="row.item.type_visit_id == 2">
                                    <b-button
                                        @click="openControl({
                                            name: row.item.name, 
                                            alias_name: row.item.alias, 
                                            norm: row.item.norm, 
                                            health_facility_item_name: row.item.health_facility_item_name, 
                                            date_control: row.item.date_control, 
                                            registration_destination_id: row.item.registration_destination_id, 
                                        })"
                                        size="sm" 
                                        variant="outline-primary" 
                                        v-b-popover.hover.right="'Ganti tanggal kontrol'">
                                        <feather-icon icon="CalendarIcon" />
                                    </b-button>
                                </template>
                            </div>
                        </template>
                    </b-table>
                    <div class="col px-0 mt-20">
                        <div class="d-flex">
                            <div class="mr-auto">
                                <b-pagination v-model="currentPage" :total-rows="totalRows" :per-page="perPage" align="fill" size="sm" class="my-0"></b-pagination>
                            </div>
                            <span class="fs-12">Menampilkan {{ firstRows }} hingga 
                                <template v-if="lastRows < totalRows">{{ lastRows }}</template> 
                                <template v-else>{{ totalRows}}</template> 
                                dari {{ totalRows }} entri
                            </span>
                        </div>
                    </div>
                </section>

                <b-modal ref="controlModal" size="sm">
                    <template #modal-header="{ close }">
                        <div class="fs-13 font-weight-bold text-uppercase w-100">
                            ANDA AKAN MENGGANTI TANGGAL KONTROL
                            <span class="text-primary">{{ temp_control.alias_name }}. {{ temp_control.name }} | {{ temp_control.norm }} | {{ temp_control.health_facility_item_name }}</span>
                        </div>
                        <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
                    </template>
                    <div class="fs-13">
                        <label class="fs-13 mb-0">
                            <i>Ganti tanggal kontrol dari {{ temp_control.date_control }} ke?</i>
                        </label>
                        <b-form-input v-model="form_control.date_control" size="sm" type="date"/>
                    </div>
                    <template #modal-footer>
                        <div class="d-flex justify-content-end w-100">
                            <b-button :disabled="disabled_control" @click="validationControl()" size="sm" variant="w3green">GANTI SEKARANG</b-button>
                        </div>
                    </template>
                </b-modal>

            </section>
        </Layout>
    </div>
</template>
<script>
import moment from 'moment'
// import { mapState } from 'vuex'
// import io from 'socket.io-client';
import axios from 'axios'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import dateMixin from '@/mixins/dateMixin';

export default {
    mixins: [dateMixin],

    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),
            // socket: io(process.env.VUE_APP_SOCKET_IO, { transports: ["websocket", "polling", "flashsocket"], withCredentials: true }),
            searchQuery: "", 
            params: {
                offset: 0,
                limit: 20,
                norm_like: '',
                name: '',
                birth_date: '',
                // health_facility_id: '3cb626f5-9148-4fb1-8663-3d7f028b01a6',
                health_facility_item_id: '',
                doctor_in_charge_id: '',
                exit_id:'',
                is_checkout: 1,
                date_start: moment(new Date()).format('YYYY-MM-DD'),
                date_end: moment(new Date()).format('YYYY-MM-DD'),
            },
            OptionsDummy: [
                { value: 1, title: 'satu' },
                { value: 1, title: 'dua' },
            ],
            currentPage: 1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            totalRows: 0,
            pageOptions: [20, 50, 100],
            isDisabledFilter: false,
            
            OptionsExit:[
                { value: '807ca005-5827-4e19-9c97-0bc3d4e924ca', title: 'Atas Persetujuan Dokter' },
                { value: '4a6d28c4-e020-425f-91e1-d39699cf679b', title: 'Meninggal' },
                { value: 'a1631acc-6a94-414d-95de-a9faea0f1c2f', title: 'Dirujuk' },
                { value: 'ae0aabfa-1600-443f-8a60-62f9e76b1608', title: 'Permintaan Sendiri' },
                { value: 'dfe2b9c6-5934-4748-aad9-6d19c957ecfd', title: 'Kabur' },
                { value: '03e783b8-92ef-40c2-a867-d5895ffbfc83', title: 'Poliklinik' },
                { value: 'f359a4b5-4406-49d7-9a81-49bf34b4c21a', title: 'Perbaikan' },
                { value: '2542a5f7-570a-449c-9eee-525569c0ce1e', title: 'Rawat Inap' },
            ],

            CheckoutTable: [
                { key: 'encounter', label: 'Aksi', thStyle: { width: '150px' }, },
                { sortable: true, key: 'register_date', label: 'Tanggal Masuk (register)' },
                { sortable: true, key: 'name', label: 'Identitas Pasien', thStyle: { width: '300px' } },
                { sortable: true, key: 'doctor_nickname', label: 'DPJP' },
                { sortable: true, key: 'health_facility_item_name', label: 'Unit' },
                { sortable: true, key: 'company_name', label: 'Cara Bayar' },
                { sortable: true, key: 'exit_name', label: 'Cara Pulang' },
                // { sortable: true, key: 'queue', label: 'Antrian', thStyle: { width: '75px' } },
                // { sortable: true, key: 'sts', label: 'Status' },
            ],
            ListItems: [],
            requestCancelToken: null,
            searchTimeout: null,
            doctor_list: [],
            
            form_control: {
                registration_destination_id: '',
                date_control: null
            },
            temp_control: {
                name: '',
                alias_name: '',
                norm: '',
                health_facility_item_name: '',
                date_control: '',
            },
            disabled_control: false,
        }
    },
    // computed: {
    //     ...mapState({ 
    //         OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,
    //     }),
    // },
    watch: {
        currentPage: {
            handler: function (value) {
                this.firstRows = this.perPage * (value - 1) + 1
                this.lastRows = this.perPage * (value)
                this.params.offset = this.perPage * (value - 1)
                this.params.limit = this.perPage

                this.onListFast()
            }
        },
        perPage: {
            handler: function (value) {
                this.params.limit = this.perPage
                this.onListFast()
            }
        },
    },
    methods: {
        openControl(obj) {
            // console.log('obj:', obj);
            this.resetControlForm()

            this.temp_control.date_control = obj?.date_control 
            this.temp_control.name = obj?.name 
            this.temp_control.alias_name = obj?.alias_name 
            this.temp_control.norm = obj?.norm 
            this.temp_control.health_facility_item_name = obj?.health_facility_item_name 

            this.form_control.registration_destination_id = obj?.registration_destination_id 
            this.form_control.date_control = obj?.date_control 
            
            this.$refs.controlModal.show()
        },

        resetControlForm() {
            this.temp_control.date_control = ''
            this.temp_control.name = ''
            this.temp_control.alias_name = ''
            this.temp_control.norm = ''
            this.temp_control.health_facility_item_name = ''

            this.form_control.registration_destination_id = '' 
            this.form_control.date_control = null 
        },

        validationControl() {
            // console.log(this.form_control);
            try {
                this.disabled_control = true
                this.$store.dispatch('registration/putChangeDateControl', this.form_control)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.resetControlForm()
                        this.$refs.controlModal.hide()                        
                        this.onListFast()
                    } 
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.disabled_control = false
            }
        },

        onListFast() {
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }

            this.searchTimeout = setTimeout(() => {
                this.isDisabledFilter = true;

                this.params.norm_like = ""
                this.params.name = ""
                this.params.birth_date = ""

                const input = this.searchQuery.trim();

                if (/^\d{4}-\d{2}-\d{2}$/.test(input)) {
                    this.params.birth_date = input
                } else {
                    if (/^\d+$/.test(input)) {
                        this.params.norm_like = input
                    }
                    else {
                        this.params.name = input
                    }
                }

                if (this.requestCancelToken) {
                    // console.log("Membatalkan request sebelumnya...")
                    this.requestCancelToken.cancel("Request sebelumnya dibatalkan.")
                }

                this.requestCancelToken = axios.CancelToken.source()
                // console.log("Mengirim request baru...", this.params)
                // console.log('this.requestCancelToken: ', this.requestCancelToken)

                this.$store.dispatch("registration/getListFast", { params: this.params, cancelToken: this.requestCancelToken.token })
                .then((res) => {
                    this.ListItems = res.rows
                    this.totalRows = res.iTotal
                    this.isDisabledFilter = false

                    let filterParams = {
                        norm_like: this.params.norm_like,
                        name: this.params.name,
                        birth_date: this.params.birth_date,
                        health_facility_item_id: this.params.health_facility_item_id,
                        doctor_in_charge_id: this.params.doctor_in_charge_id,
                        exit_id:this.params.exit_id,
                        date_start: this.params.date_start,
                        date_end: this.params.date_end
                    }

                    localStorage.setItem("checkoutpatientfiltersession", JSON.stringify(filterParams))
                })
                .catch((err) => {
                    if (axios.isCancel(err)) {
                        console.log("Request dibatalkan:", err.message)
                    } else {
                        console.error("Error saat request:", err)
                    }
                    this.isDisabledFilter = false
                })
            }, 300)
        },

        getSearchQuery(value) {
            if (value.norm_like) {
                return value.norm_like
            }
            else if (value.name) {
                return value.name
            }
            else if (value.birth_date) {
                return value.birth_date
            } else {
                return ''
            }
        },

        parsed_age(input_date) {
            if (!input_date || input_date === "0000-00-00") {
                return "-";
            }

            const parsed = moment(input_date);
            if (!parsed.isValid()) {
                return "-";
            }

            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const currentDate = moment();

            const years = currentDate.diff(birthDate, 'years');
            birthDate.add(years, 'years');
            const months = currentDate.diff(birthDate, 'months');
            birthDate.add(months, 'months');
            const days = currentDate.diff(birthDate, 'days');

            return `${years} th ${months} bln ${days} hr`;
        },
        
        getAllDoctorList() {
            try {                
                this.$store.dispatch('employee/getDoctorList', { is_specialist: 'all' }).then((response) => {
                    this.doctor_list = response
                }, error => {
                    console.error('Error while GET employee/getDoctorList general at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET employee/getDoctorList general at: ', error);
            }
        },

        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
    },
    created() {
        const filterSession = JSON.parse(localStorage.getItem('checkoutpatientfiltersession'))
        if (filterSession) {
            this.searchQuery = this.getSearchQuery(filterSession)
            this.params = {...this.params, ...filterSession};
        } else {
            this.params = {
                offset: 0,
                limit: 20,
                norm_like: '',
                name: '',
                birth_date: '',
                // health_facility_id: '3cb626f5-9148-4fb1-8663-3d7f028b01a6',
                health_facility_item_id: '',
                doctor_in_charge_id: '',
                exit_id: '',
                is_checkout: 1,
                date_start: moment(new Date()).format('YYYY-MM-DD'),
                date_end: moment(new Date()).format('YYYY-MM-DD'),
            }
        }
        // this.$store.dispatch('healthfacilityitem/getParam', this.params.health_facility_id)
        this.getAllDoctorList()
        this.onListFast()
    }

}
</script>