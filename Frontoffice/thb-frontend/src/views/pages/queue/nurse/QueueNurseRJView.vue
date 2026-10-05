<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Poliklinik' }
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
                        <div class="col-lg-3">
                            <label class="fs-13 mb-0">Cari Poliklinik</label>
                            <v-select class="select-size-sm" v-model="params.health_facility_item_id" @input="onListFast" :options="OptionsHealthFacilityItem" :reduce="item => item.value" label="title" placeholder="pilih poliklinik"></v-select>
                        </div>
                        <div class="col-lg-3">
                            <label class="fs-13 mb-0">Cari DPJP</label>
                            <v-select :disabled="is_user_specialist" class="select-size-sm" v-model="params.doctor_in_charge_id" @input="onListFast" :options="doctor_list" :reduce="item => item.value" label="title" placeholder="pilih dpjp"></v-select>
                        </div>
                    </div>
                    <div class="d-flex align-items-end">
                        <div class="d-flex align-items-center">
                            <span class="fs-12 mr-1">Tampilkan</span>
                            <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions" />
                        </div>
                    </div>
                </div>
                <b-table bordered fixed small head-variant="light" :items="ListItems" :fields="OutpatientTable" sort-by="queue" :per-page="0" :current-page="currentPage" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                    <template #cell(queue)="row">{{ row.item.code_queue }}-{{ row.item.queue }}</template>

                    <template #cell(name)="row">
                        <div class="text-left">
                            <div class="font-weight-bold">{{ row.item.alias }}. {{ row.item.name }}</div>
                            <div>({{ row.item.sex_name === 'Perempuan' ? 'P' : 'L' }}) {{ localDate(row.item.birth_date) }} ({{ parsed_age(row.item.birth_date) }}) {{ row.item.norm }}</div>
                        </div>
                    </template>
                    <template #cell(company_name)="row">
                        <div>
                            <template>{{ row.item.insurance_id == 2 || row.item.insurance_id == 3 ? row.item.company_name : row.item.insurance_name }}</template>
                            <b-badge v-if="row.item.is_cob == 1" class="rounded text-neutral-500 text-uppercase border border-warning-500 px-10 py-1" variant="warning-200">COB</b-badge>
                        </div>
                    </template>
                    <template #cell(sts)="row">
                        <b-badge
                            :variant="getStatusVariant(row.item?.sts, row.item?.call_nurse_stamp)"
                            :id="`popover-target-${row.index}`" class="p-2" style="cursor: pointer;">
                            {{ getPatientStatus(row.item?.sts, row.item?.call_nurse_stamp) }}
                        </b-badge>
                        <b-popover :target="`popover-target-${row.index}`" triggers="hover" placement="left">
                            <template #title>
                                <div class="fs-10 text-uppercase font-weight-bold">{{ row.item.alias }}. {{ row.item.name }}</div>
                            </template>
                            <div class="fs-10">
                                <template v-if="row.item?.statuses">
                                    <template v-if="row.item?.statuses?.laboratory.length > 0">
                                        <div class="mb-1">
                                            <b>LABORATORIUM</b>
                                            <div v-for="(list, i) in row.item?.statuses?.laboratory" :key="i">
                                                {{ list.no_request }} ({{ getSuppStatusName(list?.status || '') }})
                                            </div>
                                        </div>
                                    </template>
                                    <template v-if="row.item?.statuses?.radiology.length > 0">
                                        <div class="mb-1">
                                            <b>RADIOLOGI</b>
                                            <div v-for="(list, i) in row.item?.statuses?.radiology" :key="i">
                                                {{ list.no_request }} ({{ getSuppStatusName(list?.status || '') }})
                                            </div>
                                        </div>
                                    </template>
                                    <template v-if="row.item?.statuses?.prescription.length > 0">
                                        <div class="mb-1">
                                            <b>RESEP</b>
                                            <div v-for="(list, i) in row.item?.statuses?.prescription" :key="i">
                                                {{ list.no_order }} ({{ getPrescStatusName(list?.flag || '') }})
                                            </div>
                                        </div>
                                    </template>
                                    <template v-if="row.item?.statuses?.laboratory.length == 0 && row.item?.statuses?.radiology.length == 0 && row.item?.statuses?.prescription.length == 0">
                                        <i class="d-block">belum ada order yang ditambahkan</i>
                                    </template>
                                    <!-- <div>
                                        <b>LABORATORIUM</b>
                                        <template v-if="row.item?.statuses?.laboratory.length > 0">
                                            <div v-for="(list, i) in row.item?.statuses?.laboratory" :key="i">
                                                {{ list.no_request }} ({{ getSuppStatusName(list?.status || 0) }})
                                            </div>
                                        </template>
                                        <template v-else>
                                            <i class="d-block">tidak ada order yang ditambahkan</i>
                                        </template>
                                    </div> -->

                                </template>
                                <template v-else>-</template>
                            </div>
                        </b-popover>
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

                            <template v-if="row.item.is_transfer == 0">
                                <template v-if="userData.user_group.name === 'Doctor' || userData.user_group.name === 'Fisioterapi' || userData.employee_id == admin_id">
                                    <b-button
                                        size="sm"
                                        variant="outline-primary"
                                        :disabled="isDisabled"
                                        @click.prevent="callThisQueue(row.item, 'call')"
                                        class="mr-1"
                                        v-b-popover.hover.left="'Panggil Antrian'">
                                        <feather-icon icon="Volume2Icon" />
                                    </b-button>
                                </template>

                                <template v-if="conditionalMr(row.item.sts)">
                                    <template v-if="userData.user_group.id == admission_group || userData.employee_id == admin_id">
                                        <b-button
                                            @click="triggerInterTransfer({
                                                registration_destination_id: row.item.registration_destination_id,
                                                health_facility_item_name: row.item.health_facility_item_name
                                            })"
                                            size="sm" variant="w3green"
                                            v-b-popover.hover.top="'Transfer Internal'"
                                            class="mr-1">
                                            <feather-icon icon="SendIcon" />
                                        </b-button>
                                    </template>
                                    <!-- if login dokter -->
                                    <template v-if="userData.user_group.name === 'Doctor' || userData.user_group.name === 'Fisioterapi' || userData.employee_id == admin_id">
                                        <router-link :to="{ path: '/patient/doctor/' + row.item.registration_destination_id }">
                                            <b-button size="sm" variant="primary" v-b-popover.hover.top="'Buka RM Dokter'" class="mr-1">
                                                <feather-icon icon="FileIcon" />
                                            </b-button>
                                        </router-link>
                                    </template>
                                </template>

                                <template v-if="userData.user_group.name === 'Nurse' || userData.user_group.name === 'Fisioterapi' || userData.employee_id == admin_id">
                                    <router-link :to="{ path: '/patient/nurse/' + row.item.registration_destination_id }">
                                        <b-button size="sm" variant="success-500" v-b-popover.hover.right="'Buka RM Perawat'" class="mr-1" >
                                            <feather-icon icon="FileIcon" />
                                        </b-button>
                                    </router-link>
                                    <!-- <router-link :to="{ path: '/prints/label/'+ row.item.registration_destination_id}">
                                        <b-button size="sm" variant="success-500" class="mr-1">
                                        <feather-icon icon="TagIcon" />
                                        </b-button>
                                    </router-link> -->
                                    <b-dropdown size="sm" toggle-class="customDropdown" variant="outline-primary" id="dropdown-1" text="" class="mr-1">
                                        <b-dropdown-item :to="{ path: '/prints/label/'+ row.item.registration_destination_id}">Print Label</b-dropdown-item>
                                        <b-dropdown-item :to="{ path: '/patient/insurance/'+ row.item.registration_destination_id + '/' + row.item.number_insurance}">SEP</b-dropdown-item>
                                        <b-dropdown-item :to="{ path: '/bpjs/letter/'+ row.item.registration_destination_id + '/' + row.item.number_insurance}">Surat Kontrol</b-dropdown-item>
                                        <b-dropdown-item :to="{ path: '/bpjs/refer/'+ row.item.registration_destination_id + '/' + row.item.sep}">Rujuk Pasien</b-dropdown-item>
                                        <!-- <b-dropdown-item :to="{ path: '/bpjs/refer-special'}">Rujukan Khusus</b-dropdown-item> -->
                                        <!-- <b-dropdown-item :to="{ path: '/bpjs/refer-prb/'+ row.item.registration_destination_id + '/' + row.item.sep}">PRB</b-dropdown-item> -->
                                        <!-- <b-dropdown-item :to="{ path: '/bpjs/backdate/'+ row.item.registration_destination_id + '/' + row.item.number_insurance}">Backdate</b-dropdown-item> -->
                                        <!-- <b-dropdown-item :to="{ path: '/prints/bpjs-member/'+ row.item.registration_destination_id + '/' + row.item.sep}">Cetak SEP</b-dropdown-item> -->
                                        <!-- <b-dropdown-item :to="{ path: '/patient/insurance/'+ row.item.registration_destination_id + '/' + row.item.number_insurance}">Lihat SEP</b-dropdown-item> -->
                                        <!-- <b-dropdown-divider></b-dropdown-divider>
                                        <b-dropdown-item active>Active action</b-dropdown-item>
                                        <b-dropdown-item disabled>Disabled action</b-dropdown-item> -->
                                    </b-dropdown>
                                </template>
                            </template>
                            <router-link :to="{ path: '/prints/resume/' + row.item.registration_destination_id }">
                                <b-button size="sm" variant="primary-400" v-b-popover.hover.top="'Print'" class="mr-1" >
                                    <feather-icon icon="FileIcon" />
                                </b-button>
                            </router-link>
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
    //     if (!firstLoad && from.path !== to.path && localStorage.getItem('polipatientfiltersession')) {
    //         localStorage.removeItem('polipatientfiltersession');
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
                health_facility_id: '3cb626f5-9148-4fb1-8663-3d7f028b01a6',
                health_facility_item_id: '',
                doctor_in_charge_id: '',
                is_checkout: 0,

                date_start: moment(new Date()).format('YYYY-MM-DD'),
                date_end: moment(new Date()).format('YYYY-MM-DD'),
            },
            currentPage: 1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            totalRows: 0,
            pageOptions: [20, 50, 100],
            isDisabledFilter: false,

            OutpatientTable: [
                { key: 'encounter', label: 'Aksi', thStyle: { width: '220px' }, },
                { sortable: true, key: 'queue', label: 'Antrian', thStyle: { width: '75px' } },
                { sortable: true, key: 'name', label: 'Identitas Pasien', thStyle: { width: '300px' } },
                { sortable: true, key: 'doctor_nickname', label: 'DPJP' },
                { sortable: true, key: 'health_facility_item_name', label: 'Poliklinik' },
                { sortable: true, key: 'company_name', label: 'Cara Bayar' },
                { sortable: true, key: 'sts', label: 'Status' },
            ],
            ListItems: [],
            requestCancelToken: null,
            searchTimeout: null,
            doctor_list: [],
            is_user_specialist: false,

            isDisabled: false,
            admission_group: '00a30ce3-2ca3-45f8-8837-4a0babce20e9',
            admin_id: 'd77142c4-4777-47d8-bd0c-fd7dfd134af4',
        }
    },
    computed: {
        ...mapState({
            OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,
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
        getPrescStatusName(status) {
            if (status == 0) return 'dalam antrian'
            if (status == 1) return 'diproses'
            return '--'
        },

        getSuppStatusName(status) {
            if (status == 1) return 'dalam antrian'
            if (status == 2) return 'diproses'
            return '--'
        },

        conditionalMr(status) {
            if (status >= 4) return true;
            return false
        },
        callThisQueue(data,action) {
            const formData = {
                id: data.registration_destination_id,
                action: action,
                code_booking: data.code_booking
            }
            this.$store.dispatch('queue/formPostPutActionPoli', formData).then(res => {
                // console.log('response: ', res);
                this.$store.dispatch('queue/getAll', this.params) // buat refresh loket
                this.onListFast()
                // this.$store.dispatch('registration/getListFast',this.params)
                this.socket.emit('SEND_WORKORDER', res);
            },
            err => {
                this.isDisabled = false
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
            })
        },

        getPatientStatus(status, call_stamp) {
            if (status == 3 && !call_stamp) return 'Menunggu TTV';
            if (status == 5) return 'Menunggu Farmasi';
            if (status == 6) return 'Obat Diproses';
            if (status == 7 || status == 8) return 'Pasien Pulang';
            if (status == 4 || call_stamp) return 'Menunggu Dokter';
            return status
        },

        getStatusVariant(status, call_stamp) {
            if (status == 3 && !call_stamp) return 'info-400';
            if (status == 5) return 'orange-500';
            if (status == 6) return 'warning-500';
            if (status == 7 || status == 8) return 'success-500';
            if (status == 4 || call_stamp) return 'w3green';
            return 'neutral-300'
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
                        doctor_in_charge_id: this.params.doctor_in_charge_id,
                        health_facility_item_id: this.params.health_facility_item_id
                    }

                    localStorage.setItem("polipatientfiltersession", JSON.stringify(filterParams))
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

        // TRANSFER INTERNAL
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
    },
    mounted() {
        // this.params.doctor_in_charge_id = this.getDoctorId()
        this.socket.on('GET_WORKORDER', (data) => {
            this.onListFast()
        });
    },
    created() {
        const filterSession = JSON.parse(localStorage.getItem('polipatientfiltersession'));
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
                    health_facility_id: '3cb626f5-9148-4fb1-8663-3d7f028b01a6',
                    health_facility_item_id: '',
                    doctor_in_charge_id: '',
                    is_checkout: 1,

                    date_start: moment(new Date()).format('YYYY-MM-DD'),
                    date_end: moment(new Date()).format('YYYY-MM-DD'),
                };
            }
            const doctorId = this.getDoctorId();
            if (!this.params.doctor_in_charge_id && this.is_user_specialist) {
                this.params.doctor_in_charge_id = doctorId;
            }

            this.$store.dispatch('healthfacilityitem/getParam', this.params.health_facility_id)
            this.getSpecialistDoctorList()
            // this.$store.dispatch('employee/getDoctorOptions', '').then((response) => {
            //     this.doctor_list = response
            // })
            // this.params.doctor_in_charge_id = this.getDoctorId()
            this.onListFast()
    }

}
</script>