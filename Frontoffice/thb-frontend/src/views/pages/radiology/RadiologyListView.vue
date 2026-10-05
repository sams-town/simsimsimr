<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Radiologi' }
            ]"
        >
        <TopRadiologyMenu selectedMenu="menu1" />
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
                        <div class="col-3">
                            <label class="c-form-label">Dokter</label>
                            <v-select class="select-size-sm" v-model="params.doctor_in_charge_id" :options="doctor_list" :reduce="item => item.value" label="title" placeholder="pilih dpjp"></v-select>
                        </div>
                        <div class="col-3">
                            <label class="c-form-label">Poli/Ruangan</label>
                            <v-select class="select-size-sm" v-model="params.health_facility_item_id" :options="OptionsHealthFacilityItem" :reduce="item => item.value" label="title" placeholder="Poli/Ruangan"></v-select>
                        </div>
                        <div class="col-3">
                            <label class="c-form-label">Pemeriksaan</label>
                            <v-select class="select-size-sm" v-model="params.costing_id" :options="OptionsCosting" :reduce="item => item.value" label="title" placeholder="Tindakan"></v-select>
                        </div>
                        <div class="col-3">
                            <label class="c-form-label">Status Expertise</label>
                            <select class="form-control" v-model="params.expertise_status">
                                <option :value="2">Terisi</option>
                                <option :value="1">Belum Terisi</option>
                            </select>
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
                        <b-button @click="getRadiology" variant="primary" :disabled="isDisabledFilter" class="mr-2">
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
                <!-- <b-table bordered responsive hover small head-variant="light" :items="items" :fields="fields" :tbody-tr-class="rowClass" thead-class="text-bold-14" class="mt-12 text-center"> -->
                <!-- <b-table bordered responsive hover small head-variant="light" :items="items" :fields="fields" tbody-tr-class="text-regular-12" thead-class="text-bold-14" class="mt-12 text-center">                     -->
                <b-table bordered responsive hover small head-variant="light" :items="radiology_list" :fields="fields" tbody-tr-class="text-regular-12" thead-class="text-bold-14" class="mt-12 text-center">                    
                    <template #cell(status)="row">
                        <div>
                            <span v-if="row.value == 1">Menunggu antrian radiologi</span> 
                            <span v-if="row.value == 2">Diperiksa Radiografer</span> 
                            <span v-if="row.value == 3">Selesai</span> 
                        </div>
                    </template>

                    <template #cell(expertise_status)="data">
                        <div class="text-regular-16">
                            <b-badge size="sm" :variant=" data.value === 1 ? 'w3green' : 'w3green'" class="p-2 font-weight-normal">
                                <span>
                                    {{ data.value == '2' ? 'Sudah Terisi' : 'Belum Terisi' }}
                                </span>
                            </b-badge>
                        </div>
                    </template>

                    <template #cell(action)="row">
                        <div class="d-flex mx-0">
                            <!-- <template v-if="row.item.status_display == 2"> -->
                                <router-link :to="{ path: '/radiology/list/process/'+ row.item.id}">
                                    <b-button size="sm" variant="w3green" class="mr-1" v-b-popover.hover.top="'Periksa'">
                                        <feather-icon icon="SearchIcon" />
                                    </b-button>
                                </router-link>
                            <!-- </template> -->

                            <router-link :to="{ path: '/patient/detail/'+ row.item.registration_destination_id}">
                                <b-button size="sm" variant="info-600" class="mr-1" v-b-popover.hover.top="'Detail Pasien'">
                                    <feather-icon icon="FileIcon" />
                                </b-button>
                            </router-link>
                                        
                            <router-link :to="{ path: '/prints/label/radiology/'+ row.item.id}" target="_blank">
                                <b-button size="sm" variant="success-500" class="mr-1" v-b-popover.hover.top="'Label Pasien'">
                                    <feather-icon icon="TagIcon" />
                                </b-button>
                            </router-link>
                            
                            <b-button v-if="row.item.status == 1" @click="cancelRadiologyRequest(row.item)" size="sm" variant="danger" v-b-popover.hover.top="'Batal'" class="mr-1">
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

                    <template #cell(insurance_name)="row">
                        <div v-for="list in row.item.registration_insurance">
                            <div v-if="list.insurance_id != 2">{{ list.insurance_name }}</div>
                            <div v-else-if="list.insurance_id == 2">{{ list.company_name }}</div>
                        </div>
                    </template>

                    <template #cell(name)="row">
                        <div>
                            <span class="font-weight-bold">[{{ row.item.alias_name }}]</span> {{ row.item.name }} <br> {{ row.item.idcard }}
                        </div>
                    </template>

                    <template #cell(age)="row">
                        <div>
                            {{ row.item.sex_name }} <br>
                            {{ parsed_age(row.item.birth_date) }}
                        </div>
                    </template>

                    <template #cell(no_request)="row">
                        <div>
                            <span class="font-weight-bold">{{ row.item.no_request }}</span> <br> {{ row.item.date_request }} 
                        </div>
                    </template>

                    <template #cell(is_new)="data">
                        <div class="text-regular-16">
                            <b-badge size="sm" :variant=" data.value === 'N' ? 'w3green' : 'info-600'" class="p-2 font-weight-normal">
                                <span>
                                    {{ data.value === 'N' ? 'Pasien Lama' : 'Pasien Baru' }}
                                </span>
                            </b-badge>
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
        </Layout>
    </div>
</template>
<script>
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import TopRadiologyMenu from '@/@core/components/top-menu/TopRadiologyMenu.vue';
import moment from 'moment'
import { mapState } from 'vuex'
// import io from 'socket.io-client';

export default {
    name: 'RadiologyListView',
    components: { TopRadiologyMenu },
    data() {
        return {
            // socket : io(process.env.VUE_APP_SOCKET_IO, { transports: ["websocket", "polling", "flashsocket"],withCredentials: true }),
            showFilter: false,
            params: {
                limit: 20,
                offset: 0,
                no_request: '',
                expertise_status: 1,
                norm: '',doctor_in_charge_id:'',health_facility_item_id:'',
                name: '',
                date_start: moment(new Date()).format('YYYY-MM-DD'),
                date_end: moment(new Date()).format('YYYY-MM-DD'),
            },
            totalRows: 0,
            currentPage:1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            pageOptions: [20, 50, 100],
            isDisabledFilter: false,
            isCallDisabled: false,
            fields: [
                { sortable: true, key: 'status', label: 'Status' },
                { sortable: true, key: 'expertise_status', label: 'Status Expertise' },
                { sortable: true, key: 'action', label: 'Aksi' },
                { sortable: true, key: 'insurance_name', label: 'Pembiayaan', thStyle: { minWidth: '150px' } },
                { sortable: true, key: 'name', label: 'Nama', thStyle: { minWidth: '140px' } },
                { sortable: true, key: 'norm', label: 'No RM' },
                { sortable: true, key: 'birth_date', label: 'Tanggal Lahir', thStyle: { minWidth: '120px' } },
                { sortable: true, key: 'age', label: 'Kelamin&Umur', thStyle: { minWidth: '140px' } },
                { sortable: true, key: 'doctor_in_charge_name', label: 'Dokter', thStyle: { minWidth: '140px' } },
                { sortable: true, key: 'health_facility_item_name', label: 'Unit', thStyle: { minWidth: '140px' } },
                { sortable: true, key: 'no_request', label: 'No Order', thStyle: { minWidth: '140px' } },
                { sortable: true, key: 'type_how_to_enter_name', label: 'Cara Masuk', thStyle: { minWidth: '140px' } },
                { sortable: true, key: 'is_new', label: 'Jenis Pasien', thStyle: { minWidth: '120px' } },
            ],
            radiology_list: [],
            doctor_list: []
        }
    },
    watch: {
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage*(value-1)+1
                this.lastRows = this.perPage*(value)+1
                this.params.offset = this.perPage*(value-1)
                this.params.limit = this.perPage
                this.getRadiology()
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.getRadiology()
            }
        },
    },
    computed: {
        ...mapState({ 
            OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,
            OptionsCosting: state => state.costing.options.newCosting,
        }),
    },
    methods: {
        // sendQueue(queue, purpose) {
        //     this.isCallDisabled = true
        //     let params = {
        //         queue: queue,
        //         purpose: purpose,
        //     }
        //     this.socket.emit('SEND_RAD', params);
        //     setTimeout(() => { this.isCallDisabled = false }, 3000);
        // },

        // sendNextStep(date_request, queue) {
        //     let params = {
        //         date: moment(date_request).format('YYYY-MM-DD'),
        //         queue: queue
        //     }

        //     try {
        //         this.$store.dispatch('queue/postPutQueueNextRad', params)
        //         .then(
        //             res => {
        //                 this.notification('Success','CheckIcon','success','You have successfully')
        //                 this.socket.emit('SEND_RAD', params);
        //                 this.getRadiology()
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

        getRadiology() {
            try {
                this.isDisabledFilter = true
                console.log(this.params)
                this.$store.dispatch('radiology/getRadiologyListFast', this.params).then((response) => {
                    this.radiology_list = response.rows
                    this.totalRows = response.iTotal
                    this.isDisabledFilter = false
                }, error => {
                    console.error('Error while GET radiology/getRadiologyListFast at: ', error);
                    this.isDisabledFilter = false
                })
            } catch (error) {
                console.error('Error catch while GET radiology/getRadiologyListFast at: ', error);
                this.isDisabledFilter = false
            }
        },

        cancelRadiologyRequest(obj){
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin membatalkan ${obj.no_request} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(
                value => {
                    if (value === true){
                        this.$store.dispatch('radiology/removeRequest', obj.id)
                        .then(
                            response => {
                                if (response.status === 200 || response.status === 201) {
                                    this.notification('Success','CheckIcon','success','You have successfully')
                                    this.getRadiology()
                                } else {
                                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                                }
                            },
                            error => {
                                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            }
                        ) 
                    }
                }
            )
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

        filterReset () {
            this.params.offset = 0
            this.params.limit = this.perPage
            this.params.no_request = ''
            this.params.norm = ''
            this.params.name = ''
            this.params.expertise_status = ''
            this.params.date_start = moment(new Date()).format('YYYY-MM-DD')
            this.params.date_end = moment(new Date()).format('YYYY-MM-DD')
            
            this.getRadiology()
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

        // rowClass(item, role) {
        //     if (!item || role !== 'row') return 'text-regular-12'
        //     if (item.status == 1) return 'text-regular-12'
        //     if (item.status == 2) return 'text-regular-12 table-warning-400'
        //     if (item.status == 3) return 'text-regular-12 table-success-300'
        // },

    },
    created () {
        this.$store.dispatch('healthfacilityitem/getParamAlls')
        this.$store.dispatch('costing/getNewParamCosting',{type_costing_id:'5'})
        this.getRadiology()
        this.getAllDoctorList()
    },
}
</script>