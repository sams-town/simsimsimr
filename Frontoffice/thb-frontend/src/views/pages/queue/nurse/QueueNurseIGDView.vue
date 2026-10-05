<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'IGD' }
            ]"
        >
            <section class="">
                <div class="d-flex justify-content-between">
                    <div class="row w-100 align-items-end">
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
                            <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions" />
                        </div>
                    </div>
                </div>
                <b-table bordered fixed small head-variant="light" :items="ListItems" :fields="EmergencyTable" :per-page="0" :current-page="currentPage" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                    <template #cell(triage_result)="row">
                        <div class="d-flex justify-content-center align-items-center w-100" :class="getTriageStatus(row.value)" style="height: 35px;">{{ getTriageNumber(row.value) }}</div>
                    </template>
                    <template #cell(doctor_in_charge_name_2)="row">
                        <div>{{ row.value || '-' }}</div>
                    </template>
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
                    <template #cell(status)="row">
                        <b-badge :variant="getStatusVariant(row.item.exit_name)" class="p-2">{{ getPatientStatus(row.item.exit_name) }}</b-badge>
                    </template>
                    <template #cell(encounter)="row">
                        <template v-if="userData.user_group.id == admission_group">
                            <router-link :to="{ path: '/patient/detail/' + row.item.registration_destination_id }">
                                <b-button size="sm" variant="success-500" class="mr-1" >
                                    <feather-icon icon="FileIcon" />
                                </b-button>
                            </router-link>
                        </template>

                        <template v-if="row.item.is_transfer == 0">
                            <template v-if="userData.user_group.id == admission_group || userData.employee_id == admin_id">
                                <b-button
                                    @click="triggerInterTransfer({
                                        registration_destination_id: row.item.registration_destination_id,
                                        triage_result: row.item.triage_result
                                    })"
                                    size="sm" variant="w3green"
                                    v-b-popover.hover.right="'Transfer Internal'"
                                    class="mr-1">
                                    <feather-icon icon="SendIcon" />
                                </b-button>
                            </template>

                            <!-- if login dokter -->
                            <template v-if="userData.user_group.name == 'Doctor'">
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
                                    <b-button size="sm" variant="success-500" v-b-popover.hover.right="'Buka RM Perawat'" class="mr-1">
                                        <feather-icon icon="FileIcon" />
                                    </b-button>
                                </router-link>
                            </template>

                            <!-- if login nurse -->
                            <template v-if="userData.user_group.name == 'Nurse'">
                                <router-link :to="{ path: '/patient/nurse/' + row.item.registration_destination_id }">
                                    <b-button size="sm" variant="success-500" v-b-popover.hover.right="'Buka Rekam Medis Disini'" class="mr-1">
                                        <feather-icon icon="FileIcon" />
                                    </b-button>
                                </router-link>
                            </template>
                        </template>
                        <router-link :to="{ path: '/prints/resume/' + row.item.registration_destination_id }">
                            <b-button size="sm" variant="primary-400" v-b-popover.hover.top="'Print'" class="mr-1" >
                                <feather-icon icon="FileIcon" />
                            </b-button>
                        </router-link>
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
            <InternalTransferView ref="InterTransferRef" @refreshRegdata="onListFast" />
        </Layout>
    </div>
</template>
<script>
import moment from 'moment'
import { mapState } from 'vuex'
import io from 'socket.io-client';
import axios from 'axios'
import InternalTransferView from '../../patient/detail/InternalTransferView.vue';
import dateMixin from '@/mixins/dateMixin';
// let firstLoad = true;

export default {
    // beforeRouteEnter(to, from, next) {
    //     if (!firstLoad && from.path !== to.path && localStorage.getItem('igdpatientfiltersession')) {
    //         localStorage.removeItem('igdpatientfiltersession');
    //     }
    //     firstLoad = false;
    //     next();
    // },
    mixins: [dateMixin],
    components: {InternalTransferView},

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
                health_facility_id: '444b3134-5b75-4b67-af72-a558d46716d7',
                doctor_in_charge_id: '',
                is_checkout: 0,
            },
            currentPage: 1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            totalRows: 0,
            pageOptions: [20, 50, 100],
            isDisabledFilter: false,

            EmergencyTable: [
                { key: 'encounter', label: 'Rekam Medis', thStyle: { width: '160px' } },
                { sortable: true, key: 'triage_result', label: 'Triase', thStyle: { width: '60px' } },
                { sortable: true, key: 'name', label: 'Identitas Pasien', thStyle: { width: '300px' } },
                { sortable: true, key: 'doctor_nickname', label: 'DPJP' },
                { sortable: true, key: 'company_name', label: 'Cara Bayar' },
                { key: 'status', label: 'Status' },
            ],
            ListItems: [],
            requestCancelToken: null,
            searchTimeout: null,
            admission_group: '00a30ce3-2ca3-45f8-8837-4a0babce20e9',
            admin_id: 'd77142c4-4777-47d8-bd0c-fd7dfd134af4',
        }
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
        getPatientStatus(exit_name) {
            if (!exit_name) return 'Dalam Penanganan';
            return exit_name
        },

        getStatusVariant(exit_name) {
            if (!exit_name) return 'w3green';
            return 'warning-400'
        },

        getTriageColorName(value) {
            if (value == 'LEVEL 1') return 'Hitam'
            if (value == 'LEVEL 2') return 'Merah'
            if (value == 'LEVEL 3') return 'Kuning'
            if (value == 'LEVEL 4') return 'Hijau'
            if (value == 'LEVEL 5') return 'Putih'
            return '-'
        },

        getTriageNumber(value) {
            if (value == 'LEVEL 1') return '1'
            if (value == 'LEVEL 2') return '2'
            if (value == 'LEVEL 3') return '3'
            if (value == 'LEVEL 4') return '4'
            if (value == 'LEVEL 5') return '5'
            return '-'
        },

        getTriageStatus(value) {
            if (value == 'LEVEL 1') return 'bg-neutral-800 text-neutral-100'
            if (value == 'LEVEL 2') return 'bg-danger-300 text-danger-700'
            if (value == 'LEVEL 3') return 'bg-warning-300 text-warning-700'
            if (value == 'LEVEL 4') return 'bg-success-300 text-success-700'
            if (value == 'LEVEL 5') return 'bg-neutral-200 text-neutral-700'
            return 'bg-white'
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
                    }
                    localStorage.setItem("igdpatientfiltersession", JSON.stringify(filterParams))
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
            if (userLogin) {
                const parsed = JSON.parse(userLogin);
                return (parsed.user_group.name === 'Doctor' && parsed.position_id !== 'd93bd445-df1e-451a-bf95-5fc10546c133') ? parsed.employee_id : '';
            }
            return ''
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


        // TRANSFER INTERNAL
        triggerInterTransfer(obj) {
            try {
                this.$store.dispatch('registration/getById', obj.registration_destination_id).then(data => {
                    const regData = data[0]
                    let message = {
                        regData: regData,
                        pdCopywriting: `Dari bed IGD ${this.getTriageColorName(obj.triage_result)} ke?`
                    }
                    this.$refs.InterTransferRef.parentTrigger(message);
                })
            } catch (error) {
                console.error(`error at: ${error}`)
            }
        },
    },
    mounted() {
        this.params.doctor_in_charge_id = this.getDoctorId()
        this.socket.on('GET_WORKORDER', (data) => {
            this.onListFast()
        });
    },
    created() {
        const filterSession = JSON.parse(localStorage.getItem('igdpatientfiltersession'));
        if (filterSession) {
            this.searchQuery = this.getSearchQuery(filterSession);
            this.params = {...this.params,...filterSession};
        }
        this.params.doctor_in_charge_id = this.getDoctorId()
        this.onListFast()
    },
}
</script>

<!-- <style scoped>
.table td {
    height: 100%;
    vertical-align: middle; /* Pastikan isi sel sejajar secara vertikal */
}

</style> -->