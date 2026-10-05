<template>
    <div>
        <TopLaboratoryMenu selectedMenu="menu1" />
        <div class="col mt-32">
            <section class="card">
                <div class="card-header text-bold-18 bg-primary-200">Filter Data</div>
                <div class="card-body">
                    <div class="row" :class="showFilter ? '' : 'mb-8'">
                        <div class="col-6">
                            <label class="c-form-label">Tanggal Request</label>
                            <div class="row justify-content-center align-items-center position-relative">
                                <div class="col-6">
                                    <b-form-input type="date" v-model="params.date_start"></b-form-input>
                                </div>
                                <span class="position-absolute text-center">-</span>
                                <div class="col-6">
                                    <b-form-input type="date" v-model="params.date_end"></b-form-input>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <label class="c-form-label">Nama Pasien</label>
                            <b-form-input v-model="params.name"/>
                        </div>
                        <div class="col-3">
                            <label class="c-form-label">No RM</label>
                            <b-form-input v-model="params.norm"/>
                        </div>
                    </div>
                    <b-collapse id="collapse-filter" v-model="showFilter" class="mt-12" :class="showFilter ? 'mb-8' : ''">
                        <div class="row">
                            <div class="col-3">
                                <label class="c-form-label">No Order</label>
                                <b-form-input v-model="params.no_request"/>
                            </div>
                        </div>
                    </b-collapse>
                    <small @click="showFilter = !showFilter" class="font-weight-bold text-primary pointer" :class="showFilter ? null : 'collapsed'" :aria-expanded="showFilter ? 'true' : 'false'" aria-controls="collapse-filter">
                        Advance Filter 
                        <b-icon :icon="showFilter == true ? 'chevron-up' : 'chevron-down'" />
                    </small>
                    <div class="d-flex mt-40">
                        <b-button @click="getLaboratory" variant="primary" :disabled="isDisabledFilter" class="mr-2">
                            <feather-icon icon="SearchIcon" /> Filter
                        </b-button>
                        <b-button @click="filterReset" variant="outline-primary" class="mr-2">
                            <feather-icon icon="XIcon" /> Clear Filter
                        </b-button>
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
                <b-table bordered responsive small head-variant="light" :items="laboratory_list" :fields="fields" :tbody-tr-class="rowClass" thead-class="text-bold-16" class="mt-12 text-center">
                    <template #thead-top="data">
                        <b-tr>
                            <b-th colspan="4">Data Pendaftaran</b-th>
                            <b-th colspan="5">Data Pasien</b-th>
                            <b-th colspan="2">Data Tujuan</b-th>
                            <b-th></b-th>
                        </b-tr>
                    </template>

                    <template #cell(no_request)="row">
                        <div>
                            <span class="font-weight-bold">{{ row.item.no_request }}</span> <br> {{ row.item.date_request }} 
                        </div>
                    </template>

                    <template #cell(insurance_name)="row">
                        <div v-for="insurance in row.item.registration_insurance">{{ insurance.insurance_name }} 
                            <template v-if="insurance.company_name">| {{ insurance.company_name }}</template>
                        </div>
                    </template>

                    <template #cell(exit_name)="row">
                        <div class="text-regular-16">
                            <b-badge size="sm" :variant="row.item.exit_id == null ? 'orange-300' : 'success-200'" class="p-2 font-weight-normal">{{row.item.exit_id == null ? 'Sedang Dalam Penanganan' : row.item.exit_name }}</b-badge>
                        </div>
                    </template>

                    <template #cell(norm)="row">
                        <div class="text-regular-16">
                            <router-link :to="{ path: '/laboratory/list/process/'+ row.item.id}">
                                <b-badge size="sm" variant="info-600" class="p-2 font-weight-normal">
                                    {{ row.value }}
                                </b-badge>
                            </router-link>
                        </div>
                    </template>
                    
                    <template #cell(name)="row">
                        <span class="font-weight-bold">[{{ row.item.alias_name }}]</span> {{ row.item.name }} <br> {{ row.item.idcard }}
                    </template>

                    <template #cell(is_new)="data">
                        <div class="text-regular-16">
                            <b-badge size="sm" :variant=" data.value === 'N' ? 'success-600' : 'info-600'" class="p-2 font-weight-normal">{{ data.value === 'N' ? 'Pasien Lama' : 'Pasien Baru' }}</b-badge>
                        </div>
                    </template>

                    <template #cell(age)="row">
                        <div>{{ row.item.sex_name }}</div>
                        <div>{{ parsed_age(row.item.birth_date) }}</div>
                    </template>

                    <template #cell(action)="row">
                        <div class="d-flex">
                            <!-- <template v-if="row.item.status_display == 2"> -->
                                <router-link :to="{ path: '/laboratory/list/process/'+ row.item.id}">
                                    <b-button size="sm" variant="info-600" class="mr-1">
                                        <feather-icon icon="FileIcon" />
                                    </b-button>
                                </router-link>
                            <!-- </template> -->
                            <b-button @click="cancelLaboratoryRequest(row.item)" size="sm" variant="danger" class="mr-1">
                                <feather-icon icon="XSquareIcon" />
                            </b-button>
                            <!-- <template v-if="row.item.status_display == 1">
                                <b-button @click="sendQueue(row.item.queue, 'CALL')" :disabled="isCallDisabled" size="sm" variant="outline-primary" class="mr-1">
                                    <feather-icon icon="Volume2Icon" />
                                </b-button>
    
                                <b-button @click="sendNextStep(row.item.date_request, row.item.queue)" :disabled="isCallDisabled" size="sm" variant="outline-primary" class="mr-1">
                                    <feather-icon icon="CheckIcon" />
                                </b-button>
                            </template> -->
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
import TopLaboratoryMenu from '@/@core/components/top-menu/TopLaboratoryMenu.vue';
import moment from 'moment'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
// import io from 'socket.io-client';

export default {
    name: 'LaboratoryListView',
    components: { TopLaboratoryMenu },
    data() {
        return {
            // socket : io(process.env.VUE_APP_SOCKET_IO, { transports: ["websocket", "polling", "flashsocket"],withCredentials: true }),

            fields: [
                { sortable: true, key: 'no_request', label: 'No Order', thStyle: { minWidth: '140px' } },
                { sortable: true, key: 'insurance_name', label: 'Cara Bayar', thStyle: { minWidth: '150px' } },
                { sortable: true, key: 'type_how_to_enter_name', label: 'Cara Masuk', thStyle: { minWidth: '140px' } },
                { sortable: true, key: 'exit_name', label: 'Status Pasien', thStyle: { minWidth: '140px' } },
                { sortable: true, key: 'norm', label: 'No RM' },
                { sortable: true, key: 'name', label: 'Nama', thStyle: { minWidth: '140px' } },
                { sortable: true, key: 'is_new', label: 'Jenis Pasien', thStyle: { minWidth: '120px' } },
                { sortable: true, key: 'birth_date', label: 'Tanggal Lahir', thStyle: { minWidth: '120px' } },
                { sortable: true, key: 'age', label: 'Kelamin&Umur', thStyle: { minWidth: '140px' } },
                { sortable: true, key: 'health_facility_item_name', label: 'Poli/Ruangan', thStyle: { minWidth: '140px' } },
                { sortable: true, key: 'doctor_in_charge_name', label: 'Dokter', thStyle: { minWidth: '140px' } },
                { sortable: true, key: 'action', label: 'Aksi' },
            ],
            params: {
                limit: 20,
                offset: 0,
                no_request: '',
                norm: '',
                name: '',
                date_start: moment(new Date()).format('YYYY-MM-DD'),
                date_end: moment(new Date()).format('YYYY-MM-DD'),
            },
            showFilter: false,
            pageOptions: [20, 50, 100],
            currentPage:1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            totalRows: 0,
            isDisabledFilter: false,

            laboratory_list: [],
            isCallDisabled: false
        }
    },
    watch: {
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage*(value-1)+1
                this.lastRows = this.perPage*(value)+1
                this.params.offset = this.perPage*(value-1)
                this.params.limit = this.perPage
                this.getLaboratory()
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.getLaboratory()
            }
        },
    },
    
    methods: {
        // sendQueue(queue, purpose) {
        //     this.isCallDisabled = true
        //     let params = {
        //         queue: queue,
        //         purpose: purpose,
        //     }
        //     this.socket.emit('SEND_LAB', params);
        //     setTimeout(() => { this.isCallDisabled = false }, 3000);
        // },

        // sendNextStep(date_request, queue) {
        //     let params = {
        //         date: moment(date_request).format('YYYY-MM-DD'),
        //         queue: queue
        //     }

        //     try {
        //         this.$store.dispatch('queue/postPutQueueNextLab', params)
        //         .then(
        //             res => {
        //                 this.notification('Success','CheckIcon','success','You have successfully')
        //                 this.socket.emit('SEND_LAB', params);
        //                 this.getLaboratory()
        //             } 
        //         )
        //         .catch(({error, response}) => {
        //             this.notification('Failed','CloseIcon','danger',`${error}`)
        //         })
        //     } catch (error) {
        //         this.notification('Failed','CloseIcon','danger','You have unsuccessful')
        //         console.error('Error catch at: ', error)                
        //     }
        // },

        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },

        getLaboratory() {
            try {
                this.isDisabledFilter = true
                
                this.$store.dispatch('laboratory/getLaboratoryListFast', this.params).then((response) => {
                    this.laboratory_list = response.rows
                    this.totalRows = response.iTotal
                    this.isDisabledFilter = false
                }, error => {
                    console.error('Error while GET laboratory/getLaboratoryListFast at: ', error);
                    this.isDisabledFilter = false
                })
            } catch (error) {
                console.error('Error catch while GET laboratory/getLaboratoryListFast at: ', error);
                this.isDisabledFilter = false
            }
        },

        cancelLaboratoryRequest(obj){
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus ${obj.no_request} ?`, {
                title: 'Please Confirm', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true) {
                    this.$store.dispatch('laboratory/removeRequest', obj.id)
                    .then(
                        response => {
                            if (response.status === 200 || response.status === 201) {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getLaboratory()

                            } else {
                                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            }
                        },
                        error => {
                            this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        }
                    ) 
                }
            })
        },

        filterReset () {
            this.params.offset = 0
            this.params.limit = this.perPage
            this.params.no_request = ''
            this.params.norm = ''
            this.params.name = ''
            this.params.date_start = moment(new Date()).format('YYYY-MM-DD')
            this.params.date_end = moment(new Date()).format('YYYY-MM-DD')
            
            this.getLaboratory()
        },

        parsed_age(input_date) {
            if (!input_date || input_date === "0000-00-00") {
                return "-";
            }

            const parsed = moment(input_date);
            if (!parsed.isValid()) {
                return "";
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


        rowClass(item, role) {
            if (!item || role !== 'row') return
            if (item.status == 2) return 'table-warning-400'
            if (item.status == 3) return 'table-w3green'
        },
    },
    created () {
        this.getLaboratory()
    },
}
</script>