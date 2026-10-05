<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Rawat Inap' }
            ]"
        >
            <section class="">
                <div class="d-flex justify-content-between">
                    <div class="row w-100 align-items-end">
                        <div class="col-lg-3">
                            <label class="fs-13 mb-0">Cari Bangsal</label>
                            <v-select class="select-size-sm" v-model="params.health_facility_item_id" @input="onListFast" :options="OptionsHealthFacilityItem" :reduce="item => item.value" label="title" placeholder="pilih bangsal"></v-select>
                        </div>
                        <div class="col-lg-3">
                            <label class="fs-13 mb-0">Cari DPJP</label>
                            <v-select :disabled="is_user_specialist" class="select-size-sm" v-model="params.doctor_in_charge_id" @input="onListFast" :options="doctor_list" :reduce="item => item.value" label="title" placeholder="pilih dpjp"></v-select>
                        </div>
                        <div class="col-lg-3">
                            <label class="fs-13 mb-0">Cari Pasien</label>
                            <div class="d-flex align-items-center position-relative">
                                <b-form-input v-model="searchQuery" @input="onListFast" class="pl-5" size="sm" autocomplete="off" placeholder="masukkan nama atau NRM" />
                                <b-icon icon="search" class="position-absolute pl-3 text-neutral-400" />
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-end">
                        <div class="d-flex align-items-center">
                            <span class="fs-12 mr-1">Tampilkan</span>
                            <b-form-select style="width: 70px;" id="per-page-select" v-model="perPage" size="sm" :options="pageOptions" />
                        </div>
                    </div>
                </div>
                <b-table bordered fixed small head-variant="light" :items="ListItems" :fields="InpatientTable" :per-page="0" :current-page="currentPage" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                    <template #cell(los)="row">
                        <div class="text-center">
                            <div class="font-weight-bold">{{ row.item.los }}</div>
                            <div>{{ row.item.room_abbreviation }} {{ row.item.bed_name ? `BED ${row.item.bed_name}` : '' }}</div>
                        </div>
                    </template>
                    <template #cell(ews_score)="row">
                        <div class="d-flex justify-content-center align-items-center w-100" :class="getEwsStatus(row.value, row.item.birth_date)" style="height: 35px;">{{ getEwsNumber(row.value) }}</div>
                    </template>
                    <template #cell(name)="row">
                        <div class="text-left">
                            <div class="font-weight-bold">{{ row.item.alias }}. {{ row.item.name }}</div>
                            <div>{{ localDate(row.item.birth_date) }} ({{ parsedAge(row.item.birth_date) }}) {{ row.item.norm }}</div>
                        </div>
                    </template>
                    <template #cell(doctor_nickname)="row">
                        <span>{{ row.value }}</span>
                        <div v-if="row.item.consultation_type_id">
                            <b-badge variant="orange-500" class="ml-1">{{ getConsultationTypeName(row.item.consultation_type_id) }}</b-badge>
                        </div>
                    </template>
                    <template #cell(company_name)="row">
                        <div>
                            <template>
                                <div>{{ row.item.insurance_id != 1 ? row.item.company_name : row.item.insurance_name }}</div>
                                <div>Kelas {{  row.item.service_class_name }}</div>
                            </template>
                            <b-badge v-if="row.item.is_cob == 1" class="rounded text-neutral-500 text-uppercase border border-warning-500 px-10 py-1" variant="warning-200">COB</b-badge>
                        </div>
                        <!-- <div>
                            <template>{{ row.item.insurance_id == 2 || row.item.insurance_id == 3 ? row.item.company_name : row.item.insurance_name }}</template>
                            <b-badge v-if="row.item.is_cob == 1" class="rounded text-neutral-500 text-uppercase border border-warning-500 px-10 py-1" variant="warning-200">COB</b-badge>
                        </div> -->
                    </template>
                    <template #cell(status)="row">
                        <b-badge :variant="getStatusVariant(row.item.exit_name)" class="p-2">{{ getPatientStatus(row.item.exit_name) }}</b-badge>
                    </template>
                    <template #cell(encounter)="row">
                        <div class="d-flex justify-content-start">
                            <template v-if="userData.user_group.id == admission_group">
                                <router-link :to="{ path: '/patient/detail/' + row.item.registration_destination_id }">
                                    <b-button size="sm" variant="success-500" class="mr-1" >
                                        <feather-icon icon="FileIcon" />
                                    </b-button>
                                </router-link>
                            </template>
                            <template v-else>
                                <template v-if="row.item.is_transfer == 0">
                                    <b-button
                                    @click="triggerConfirm({
                                        registration_destination_id: row.item.registration_destination_id,
                                        alias_name: row.item.alias,
                                        name: row.item.name,
                                        room_abbreviation: row.item.room_abbreviation,
                                        bed_name: row.item.bed_name,
                                        doctor_in_charge_id: row.item.doctor_in_charge_id,
                                        birth_date: row.item.birth_date,
                                        hide_button: false,
                                    })"
                                    size="sm" variant="outline-primary" v-b-popover.hover.right="'Konfirmasi transfer'" class="mr-1">
                                        <feather-icon icon="UserCheckIcon" />
                                    </b-button>
                                </template>

                                <template v-if="row.item.is_transfer == 1 && row.item.registration_destination_ref_id">
                                    <b-button
                                    @click="triggerConfirm({
                                        registration_destination_id: row.item.registration_destination_id,
                                        alias_name: row.item.alias,
                                        name: row.item.name,
                                        room_abbreviation: row.item.room_abbreviation,
                                        bed_name: row.item.bed_name,
                                        doctor_in_charge_id: row.item.doctor_in_charge_id,
                                        birth_date: row.item.birth_date,
                                        hide_button: true,
                                    })"
                                    size="sm" variant="outline-primary" v-b-popover.hover.left="'Riwayat transfer'" class="mr-1">
                                        <feather-icon icon="InfoIcon" />
                                    </b-button>
                                </template>

                                <template v-if="row.item.is_transfer == 1">
                                    <template v-if="userData.user_group.name === 'Nurse' || userData.employee_id == admin_id">
                                        <b-button
                                            @click="triggerInterTransfer({
                                                registration_destination_id: row.item.registration_destination_id,
                                                health_facility_item_name: row.item.health_facility_item_name
                                            })"
                                            size="sm" variant="w3green"
                                            v-b-popover.hover.left="'Pindahkan Pasien'"
                                            class="mr-1">
                                                <feather-icon icon="SendIcon" />
                                        </b-button>
                                    </template>

                                    <!-- if login dokter -->
                                    <template v-if="userData.user_group.name === 'Doctor'">
                                        <router-link :to="{ path: '/patient/doctor/' + row.item.registration_destination_id }">
                                            <b-button size="sm" variant="primary" v-b-popover.hover.right="'Buka Rekam Medis Disini'" class="mr-1">
                                                <feather-icon icon="FileIcon" />
                                            </b-button>
                                        </router-link>
                                    </template>

                                    <!-- if login JKN atau Admin -->
                                    <template v-else-if="userData.user_group.id == 'a4c04d8d-12d8-4c54-afca-69de6bd5f62d' || userData.employee_id == admin_id">
                                        <router-link :to="{ path: '/patient/doctor/' + row.item.registration_destination_id }">
                                            <b-button size="sm" variant="primary" v-b-popover.hover.top="'Buka RM Dokter'" class="mr-1">
                                                <feather-icon icon="FileIcon" />
                                            </b-button>
                                        </router-link>
                                        <router-link :to="{ path: '/patient/nurse/' + row.item.registration_destination_id }">
                                            <b-button size="sm" variant="success-500" v-b-popover.hover.top="'Buka RM Perawat'" class="mr-1">
                                                <feather-icon icon="FileIcon" />
                                            </b-button>
                                        </router-link>
                                    </template>

                                    <!-- selain kedua kondisi diatas -->
                                    <template v-else>
                                        <router-link :to="{ path: '/patient/nurse/' + row.item.registration_destination_id }">
                                            <b-button size="sm" variant="success-500" v-b-popover.hover.right="'Buka Rekam Medis Disini'" class="mr-1">
                                                <feather-icon icon="FileIcon" />
                                            </b-button>
                                        </router-link>
                                        <b-button size="sm" variant="info-600" class="mr-1"
                                            @click="openCheckout({
                                                registration_destination_id: row.item.registration_destination_id,
                                                name: row.item.name,
                                                alias_name: row.item.alias,
                                                norm: row.item.norm,
                                                room_abbreviation: row.item.room_abbreviation,
                                                bed_name: row.item.bed_name,
                                            })"
                                        >
                                            <feather-icon icon="LogOutIcon" />
                                        </b-button>
                                    </template>
                                </template>
                                    <router-link :to="{ path: '/prints/resume/' + row.item.registration_destination_id }">
                                        <b-button size="sm" variant="primary-400" v-b-popover.hover.top="'Print'" class="mr-1" >
                                            <feather-icon icon="FileIcon" />
                                        </b-button>
                                    </router-link>
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
        </Layout>

        <b-modal v-model="active.modal.checkout" size="sm" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="d-flex justify-content-between w-100">
                    <div class="text-uppercase font-weight-bold fs-13 pr-16">
                        Anda akan melakukan checkout pada pasien
                        <span class="text-primary">{{ temp_change.alias_name }}. {{ temp_change.name }} | {{ temp_change.norm }} | {{ temp_change.room_abbreviation }} BED {{ temp_change.bed_name }}</span>
                    </div>
                    <feather-icon icon="XIcon" @click="close()" class="pointer"/>
                </div>
            </template>
            <div>
                <label class="fs-13 mb-0">Alasan Checkout</label>
                <v-select v-model="form_checkout.exit_id" :options="optExit" :reduce="item => item.value" label="text" class="select-size-sm" placeholder="-- Alasan --"></v-select>
            </div>
            <template #modal-footer>
                <div class="w-100 d-flex justify-content-end m-0">
                    <b-button @click="registrationCheckout" :disabled="active.disabled.checkout" size="sm" variant="primary">CHECKOUT SEKARANG</b-button>
                </div>
            </template>
        </b-modal>

        <InternalTransferView ref="InterTransferRef" @refreshRegdata="onListFast" />
        <ConfirmTransferView ref="confirmTransferRef" :vHideConfirm="isHideConfirmButton" @refreshRegdata="onListFast" />
    </div>
</template>
<script>
import dateMixin from '@/mixins/dateMixin';
import optionMixin from '@/mixins/optionMixin';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue';
import axios from 'axios';
import io from 'socket.io-client';
import { mapState } from 'vuex';
import ConfirmTransferView from '../../patient/detail/ConfirmTransferView.vue';
import InternalTransferView from '../../patient/detail/InternalTransferView.vue';
// let firstLoad = true;

export default {
    // beforeRouteEnter(to, from, next) {
    //     if (!firstLoad && from.path !== to.path && localStorage.getItem('ranappatientfiltersession')) {
    //         localStorage.removeItem('ranappatientfiltersession');
    //     }
    //     firstLoad = false;
    //     next();
    // },
    mixins: [dateMixin, optionMixin],
    components: { InternalTransferView, ConfirmTransferView },
    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),
            socket: io(process.env.VUE_APP_SOCKET_IO, { transports: ["websocket", "polling", "flashsocket"], withCredentials: true }),
            searchQuery: "",
            params: {
                offset: 0,
                limit: 20,
                norm_like: '',
                name: '',
                birth_date: '',
                health_facility_id: '40f06534-07ec-4221-ad9d-dea08196079f',
                health_facility_item_id: '',
                doctor_in_charge_id: '',
                is_checkout: 0,
            },
            currentPage: 1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            totalRows: 0,
            pageOptions: [20, 50, 100, 250],
            isDisabledFilter: false,
            doctorFilterReady: false,

            InpatientTable: [
                { key: 'encounter', label: 'Aksi', thStyle: { width: '190px' } },
                { sortable: true, key: 'ews_score', label: 'EWS', thStyle: { width: '50px' } },
                { sortable: true, key: 'name', label: 'Identitas Pasien', thStyle: { width: '300px' } },
                { sortable: true, key: 'doctor_nickname', label: 'DPJP' },
                { sortable: true, key: 'los', label: 'LoS/Bangsal' },
                { sortable: true, key: 'company_name', label: 'Cara Bayar' },
                { key: 'status', label: 'Status' },
            ],
            ListItems: [],
            requestCancelToken: null,
            searchTimeout: null,
            doctor_list: [],
            is_user_specialist: false,
            active: {
                modal: {
                    checkout: false,
                },
                disabled: {
                    transfer: false,
                    checkout: false,
                },
            },
            temp_change: {
                name: '',
                alias_name: '',
                norm: '',
                room_abbreviation: '',
                bed_name: '',
                doctors: [],
                // registration_destination_id: '',
                // doctor_in_charge_id: '',
                // is_change_doctor: 'not_accepted',
                // type_room_id: '',
                // room_id: '',
                // bed_id: ''
            },

            form_checkout: {
                id: '',
                exit_id: '',
            },

            isHideConfirmButton: false,
            admission_group: '00a30ce3-2ca3-45f8-8837-4a0babce20e9',
            admin_id: 'd77142c4-4777-47d8-bd0c-fd7dfd134af4',
        }
    },
    computed: {
        ...mapState({
            OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,
            optExit: state => state.frontoptions.general.checkout.exit,
            // TypeRoomOptions: state => state.helper.options.type_room,
            // OptionsRoom: state => state.room.options.room,
            // OptionsBed: state => state.room.options.bed,
        }),
    },
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
        async openCheckout(obj){
            this.temp_change.name = obj?.name
            this.temp_change.alias_name = obj?.alias_name
            this.temp_change.norm = obj?.norm
            this.temp_change.room_abbreviation = obj?.room_abbreviation
            this.temp_change.bed_name = obj?.bed_name

            await this.resetCheckout()
            this.form_checkout.id = obj?.registration_destination_id

            this.active.modal.checkout = true
        },

        async resetCheckout() {
            this.form_checkout.id = ''
            this.form_checkout.exit_id = ''
        },

        async registrationCheckout() {
            try {
                this.active.disabled.checkout = true
                await this.$store.dispatch('registration/checkout', this.form_checkout)
                .then(
                    async res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        await this.resetCheckout()
                        this.active.modal.checkout = false
                        this.onListFast()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                console.error('Error catch at: ', error)
            } finally {
                this.active.disabled.checkout = false
            }
        },

        triggerInterTransfer(obj) {
            try {
                this.$store.dispatch('registration/getById', obj.registration_destination_id).then(data => {
                    const regData = data[0]
                    let message = {
                        regData: regData,
                        pdCopywriting: `Dari ${obj.health_facility_item_name} ke?`
                    }
                    this.$refs.InterTransferRef.parentTrigger(message);
                })
            } catch (error) {
                console.error(`error at: ${error}`)
            }
        },

        triggerConfirm(param) {
            this.isHideConfirmButton = param.hide_button
            const message = param
            this.$refs.confirmTransferRef.openConfirm(message);
        },

        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },

        getPatientStatus(exit_name) {
            if (!exit_name) return 'Rawat Inap';
            return exit_name
        },

        getStatusVariant(exit_name) {
            if (!exit_name) return 'w3green';
            return 'warning-400'
        },

        getEwsStatus(value, birth_date) {
            let is_pews = this.pewsCriteria(birth_date) || false
            let score = value ? Number(value) : null

            if (is_pews === true) {
                // console.log('ada yg masuk', `${value}`, score);
                if (score != null && (score <= 2 || score == 99 || score === 0)) return 'bg-white'
                if (score == 4) return 'bg-warning-200'
                if (score == 5) return 'bg-orange-300'
                if (score >= 6) return 'bg-danger-500 text-white'
                return 'bg-white text-ax-primary-500'
            } else {
                if (score == 99) return 'bg-white'
                if (score >= 1 && score <= 4) return 'bg-info-200'
                if (score >= 5 && score <= 6) return 'bg-warning-200'
                if (score >= 7 && score != 99) return 'bg-danger-500 text-white'
                return 'bg-white text-ax-primary-500'
            }
        },

        getEwsNumber(value) {
            let status = Number(value)
            if (status == 99) return '-'
            return status || '0'
        },

        onListFast() {
            this.doctorFilterReady = false
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
                    // console.log('this.ListItems: ', this.ListItems)
                    this.totalRows = res.iTotal
                    this.isDisabledFilter = false
                    this.doctorFilterReady = true

                    let filterParams = {
                        norm_like: this.params.norm_like,
                        name: this.params.name,
                        birth_date: this.params.birth_date,
                        doctor_in_charge_id: this.params.doctor_in_charge_id,
                        health_facility_item_id: this.params.health_facility_item_id
                    }

                    // localStorage.setItem("ranappatientfiltersession", JSON.stringify(this.params))
                    localStorage.setItem("ranappatientfiltersession", JSON.stringify(filterParams))

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

        getDoctorId() {
            const userLogin = localStorage.getItem('userData')
            const parsed = JSON.parse(userLogin);
            let is_specialist = parsed.user_group.name === 'Doctor' && parsed.position_id !== 'd93bd445-df1e-451a-bf95-5fc10546c133'

            if (userLogin && is_specialist == true) {
                this.is_user_specialist = true
                return parsed.employee_id
            }
            this.is_user_specialist = false
            return ''
        },

        getSpecialistDoctorList() {
            try {
                this.$store.dispatch('employee/getDoctorList', { is_specialist: '1' }).then((response) => {
                    this.doctor_list = response
                }, error => {
                    console.error('Error while GET employee/getDoctorList general at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET employee/getDoctorList general at: ', error);
            }
        },
    },
    // mounted() {
    //     this.params.doctor_in_charge_id = this.getDoctorId()
    //     this.socket.on('GET_WORKORDER', (data) => {
    //         this.onListFast()
    //     });
    // },
    created() {
        const filterSession = JSON.parse(localStorage.getItem('ranappatientfiltersession'));
        if (filterSession) {
            this.searchQuery = this.getSearchQuery(filterSession);
            this.params = {...this.params, ...filterSession};
        } else {
            this.params = {
                offset: 0,
                limit: 20,
                norm_like: '',
                name: '',
                birth_date: '',
                health_facility_id: '40f06534-07ec-4221-ad9d-dea08196079f',
                health_facility_item_id: '',
                doctor_in_charge_id: '',
                is_checkout: 0,
            };
        }

        const doctorId = this.getDoctorId();
        if (!this.params.doctor_in_charge_id && this.is_user_specialist) {
            this.params.doctor_in_charge_id = doctorId;
        }

        this.$store.dispatch('healthfacilityitem/getParam', this.params.health_facility_id)
        this.getSpecialistDoctorList()
        // this.params.doctor_in_charge_id = this.getDoctorId()
        this.onListFast()
    }

}
</script>