<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Master Kamar' }
            ]"
        >
        <section class="">
            <div class="row w-100">
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Bangsal</label>
                    <v-select v-model="params.type_room_id" @input="onRoomList" :options="TypeRoomOptions" :reduce="item => item.value" label="title" placeholder="pilih bangsal" class="select-size-sm" />
                </div>
                <div class="col-lg-3 mt-8 mt-lg-0">
                    <label class="fs-13 mb-0">Ruangan</label>
                    <b-form-input v-model="params.name" @input="onRoomList" size="sm" />
                </div>
            </div>

            <div class="d-flex justify-content-between mt-12">
                <div>
                    <b-button size="sm" variant="primary" @click="openRoomModal">
                        <feather-icon icon="PlusCircleIcon" class="mr-1" />
                        <span class="fs-12">Tambah Ruangan</span>
                    </b-button>
                </div>
                <div class="d-flex align-items-center">
                    <span class="fs-12 mr-1">Tampilkan</span>
                    <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions" />
                </div>
            </div>

            <section class="mt-12">
                <b-table bordered fixed small head-variant="light" :items="room_list" :fields="roomFields" :per-page="0" :current-page="currentPage" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                    <template #cell(beds)="row">{{ row.item.beds.length }}</template>

                    <template #cell(gender_availability)="row">
                        <div>{{ getGenderAvailabilityName(row.value) }}</div>
                    </template>

                    <template #cell(is_surgery)="row">
                        <div>{{ getSurgeryName(row.value) }}</div>
                    </template>

                    <template #cell(is_isolation)="row">
                        <div>{{ getIsolationName(row.value) }}</div>
                    </template>

                    <template #cell(is_internal)="row">
                        <div>{{ getInternalDiseaseName(row.value) }}</div>
                    </template>

                    <template #cell(room_pressure)="row">
                        <div>{{ getRoomPressure(row.value) }}</div>
                    </template>

                    <template #cell(action)="row">
                        <b-button @click="onEditRoom(row.item)" variant="warning-500" size="sm" class="mr-1">
                            <feather-icon icon="EditIcon" />
                        </b-button>
                        <b-button @click="removeRoom(row.item)" variant="danger-500" size="sm" class="mr-1">
                            <feather-icon icon="TrashIcon" />
                        </b-button>
                        <!-- <template v-if="row.item.beds?.length > 0"> -->
                            <b-button size="sm" @click="row.toggleDetails" :variant="row.detailsShowing ? 'w3green' : 'outline-w3green'">
                                <feather-icon :icon="row.detailsShowing ? 'EyeOffIcon' : 'EyeIcon'" /> Bed
                            </b-button>
                        <!-- </template> -->
                    </template>

                    <template #row-details="row" class="bg-danger">
                        <div class="bg-success-100 py-1 font-weight-bold">{{ row.item.name }}</div>
                        <div class="text-left py-1">
                            <b-button 
                                @click="openCreateBed({ room_id: row.item.id, room_name: row.item.name })" 
                                variant="primary-500" size="sm">
                                <feather-icon icon="PlusCircleIcon" class="mr-1"/> 
                                <span class="fs-12">Tambah Bed</span>
                            </b-button>
                        </div>
                        <b-table small responsive :fields="bedFields" :items="row.item.beds" >
                            <template #cell(action)="data">
                                <b-button 
                                    @click="openPutBed({ ...data.item, room_name: row.item.name })" 
                                    variant="warning-500" size="sm" class="mr-1">
                                    <feather-icon icon="EditIcon" />
                                </b-button>
                                <b-button @click="removeBed({ ...data.item, room_name: row.item.name })" variant="danger-500" size="sm" class="mr-1">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </template>
                        </b-table>
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
            
            <b-modal v-model="active.modal.room" size="md" no-close-on-backdrop no-close-on-esc>
                <template #modal-header="{ close }">
                    <div class="d-flex justify-content-between w-100">
                        <div class="text-uppercase font-weight-bold fs-13 pr-16">
                            ANDA SEDANG MENGELOLA RUANGAN 
                        </div>
                        <feather-icon icon="XIcon" size="18" @click="close()" class="pointer"/>
                    </div>
                </template>
                <div class="row">
                    <div class="col-lg-4">
                        <label class="fs-13 mb-0">Bangsal</label>
                        <v-select v-model="form_room.health_facility_item_id" :options="OptionsHealthFacilityItem" :reduce="item => item.value" label="title" placeholder="pilih bangsal" class="select-size-sm" />
                    </div>
                    <div class="col-lg-4 mt-8 mt-lg-0">
                        <label class="fs-13 mb-0">Tipe Ruangan</label>
                        <v-select v-model="form_room.type_room_id" :options="TypeRoomOptions" :reduce="item => item.value" label="title" placeholder="pilih tipe ruangan" class="select-size-sm" />
                    </div>
                    <div class="col-lg-4 mt-8 mt-lg-0">
                        <label class="fs-13 mb-0">Nama Kamar</label>
                        <b-form-input v-model="form_room.name" autocomplete="off" size="sm"/>
                    </div>
                    <div class="col-lg-4 mt-8">
                        <label class="fs-13 mb-0">Kelas Layanan</label>
                        <v-select v-model="form_room.service_class_id" :options="OptionsServiceClass" :reduce="item => item.value" label="title" placeholder="pilih kelas" class="select-size-sm"/>
                    </div>
                    <div class="col-lg-4 mt-8">
                        <label class="fs-13 mb-0">Tarif</label>
                        <v-select v-model="form_room.costing_id" :options="CostingOptions" :reduce="item => item.value" label="title" placeholder="pilih tarif" class="select-size-sm"/>
                    </div>
                    <div class="col-lg-4 mt-8">
                        <label class="fs-13 mb-0">Bedah</label>
                        <v-select v-model="form_room.is_surgery" :options="optSurgery" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm"/>
                    </div>
                    <div class="col-lg-4 mt-8">
                        <label class="fs-13 mb-0">Isolasi</label>
                        <v-select v-model="form_room.is_isolation" :options="optIsolation" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm"/>
                    </div>
                    <div class="col-lg-4 mt-8">
                        <label class="fs-13 mb-0">Tekanan</label>
                        <v-select v-model="form_room.room_pressure" :options="optRoomPressure" :reduce="item => item.value" label="title" placeholder="pilih tekanan" class="select-size-sm"/>
                    </div>
                    <div class="col-lg-4 mt-8">
                        <label class="fs-13 mb-0">Gender</label>
                        <v-select v-model="form_room.gender_availability" :options="optGenderAvailability" :reduce="item => item.value" label="title" placeholder="pilih gender" class="select-size-sm"/>
                    </div>
                    <div class="col-lg-4 mt-8">
                        <label class="fs-13 mb-0">Penyakit dalam</label>
                        <v-select v-model="form_room.is_internal" :options="optInternal" :reduce="item => item.value" label="title" placeholder="pilih penyakit dalam" class="select-size-sm"/>
                    </div>
                </div>
                <template #modal-footer>
                    <div class="w-100 d-flex justify-content-end m-0">
                        <b-button @click="validationRoom" :disabled="active.disabled.room" variant="primary" size="sm" class="px-32">SIMPAN</b-button>
                    </div>
                </template>
            </b-modal>


            <b-modal v-model="active.modal.cBed" size="sm" no-close-on-backdrop no-close-on-esc>
                <template #modal-header="{ close }">
                    <div class="d-flex justify-content-between w-100">
                        <div class="text-uppercase font-weight-bold fs-13 pr-16">
                            ANDA SEDANG MENAMBAHKAN BED PADA RUANGAN 
                            <span class="text-primary">{{ temp_c_room_name }}</span>
                        </div>
                        <feather-icon icon="XIcon" size="18" @click="close()" class="pointer"/>
                    </div>
                </template>

                <div>
                    <label class="fs-13 mb-0">Nama Bed</label>
                    <b-form-input v-model="form_create_bed.name" autocomplete="off" size="sm"/>
                </div>

                <template #modal-footer>
                    <div class="w-100 d-flex justify-content-end m-0">
                        <b-button @click="validationCreateBed" :disabled="active.disabled.cBed" variant="primary" size="sm" class="px-32">SIMPAN</b-button>
                    </div>
                </template>
            </b-modal>

            <b-modal v-model="active.modal.pBed" size="sm" no-close-on-backdrop no-close-on-esc>
                <template #modal-header="{ close }">
                    <div class="d-flex justify-content-between w-100">
                        <div class="text-uppercase font-weight-bold fs-13 pr-16">
                            ANDA SEDANG MENGEDIT BED PADA RUANGAN 
                            <span class="text-primary">{{ temp_p_room_name }}</span>
                        </div>
                        <feather-icon icon="XIcon" size="18" @click="close()" class="pointer"/>
                    </div>
                </template>

                <div>
                    <label class="fs-13 mb-0">Nama Bed</label>
                    <b-form-input v-model="form_put_bed.name" autocomplete="off" size="sm"/>
                </div>
                <div class="mt-8">
                    <label class="fs-13 mb-0">Status Bed</label>
                    <v-select v-model="form_put_bed.type_status_bed_id" :options="TypeStatusBedOptions" :reduce="item => item.value" label="title" placeholder="pilih status bed" />                    
                </div>

                <template #modal-footer>
                    <div class="w-100 d-flex justify-content-end m-0">
                        <b-button @click="validationPutBed" :disabled="active.disabled.pBed" variant="primary" size="sm" class="px-32">SIMPAN</b-button>
                    </div>
                </template>
            </b-modal>

        </section>
        </Layout>
    </div>
</template>

<script>
import { mapState } from 'vuex'
import axios from 'axios'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import optionMixin from '@/mixins/optionMixin'

export default {
    mixins: [optionMixin],
    data() {
        return {
            hfi_inpatient: '40f06534-07ec-4221-ad9d-dea08196079f',
            active: {
                modal: {
                    room: false,
                    cBed: false,
                    pBed: false,
                },
                disabled: {
                    room: false,
                    cBed: false,
                    pBed: false,
                }
            },

            // ================================

            roomFields: [
                { key: 'action', label: 'Aksi', thStyle: { width: '150px' },},
                { key: 'type_room_name', label: 'Bangsal', sortable: true },
                { key: 'name', label: 'Ruangan', sortable: true },
                { key: 'beds', label: 'Jumlah Bed', thStyle: { width: '100px' }, },
                { key: 'service_class_name', label: 'Kelas' },
                { key: 'gender_availability', label: 'Gender' },
                { key: 'is_isolation', label: 'Isolasi' },
                { key: 'is_surgery', label: 'Operasi' },
                { key: 'room_pressure', label: 'Tekanan' },
                { key: 'is_internal', label: 'Penyakit Dalam' },
            ],

            params: {
                limit: 20,
                offset: 0,
                name: '',
                type_room_id: '',
            },
            currentPage: 1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            totalRows: 0,
            pageOptions: [20, 50, 100],

            room_list: [],
            form_room: {
                id: '',
                health_facility_id: '40f06534-07ec-4221-ad9d-dea08196079f',
                health_facility_item_id: '',
                service_class_id: '',
                type_room_id: '',
                costing_id: '',
                name: '',
                is_surgery: '',
                is_isolation: '',
                room_pressure: '',
                gender_availability: '',
                is_internal: ''
            },

            requestCancelToken: null,
            searchTimeout: null,

            // ================================

            bedFields: [
                { key: 'action', label: 'Aksi', thStyle: { width: '150px' },},
                { key: 'name', label: 'Bed', sortable: true },
                { key: 'type_status_bed_name', label: 'Status', sortable: true },
                { key: 'date_added', label: 'Date Added' },
                { key: 'last_updated', label: 'Last Updated' },
            ],

            temp_c_room_name: '',
            form_create_bed: {
                id: '',
                room_id: '',
                name: '',
            },
            
            temp_p_room_name: '',
            form_put_bed: {
                id: '',
                name: '',
                type_status_bed_id: '',
            },
        }
    },

    watch: {
        currentPage: {
            handler: function (value) {
                this.firstRows = this.perPage * (value - 1) + 1
                this.lastRows = this.perPage * (value)
                this.params.offset = this.perPage * (value - 1)
                this.params.limit = this.perPage

                this.onRoomList()
            }
        },
        perPage: {
            handler: function (value) {
                this.params.limit = this.perPage
                this.onRoomList()
            }
        },
    },

    computed: {
        ...mapState({ 
            OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,
            TypeRoomOptions: state => state.helper.options.type_room,
            OptionsServiceClass: state => state.helper.options.service_class,
            CostingOptions: state => state.costing.options.serviceData,
            optSurgery: state => state.frontoptions.master.room.surgery,
            optIsolation: state => state.frontoptions.master.room.isolation,
            optRoomPressure: state => state.frontoptions.master.room.room_pressure,
            optGenderAvailability: state => state.frontoptions.master.room.gender_availability,
            optInternal: state => state.frontoptions.master.room.internal_disease,

            TypeStatusBedOptions: state => state.helper.options.type_status_bed,
        }),
    },

    methods: {
        async openRoomModal() {
            await this.resetRoom()
            this.active.modal.room = true
        },

        async resetRoom() {
            this.form_room.id = ''
            this.form_room.health_facility_id = this.hfi_inpatient
            this.form_room.health_facility_item_id = ''
            this.form_room.service_class_id = ''
            this.form_room.type_room_id = ''
            this.form_room.costing_id = ''
            this.form_room.name = ''
            this.form_room.is_surgery = ''
            this.form_room.is_isolation = ''
            this.form_room.room_pressure = ''
            this.form_room.gender_availability = ''
            this.form_room.is_internal = ''
        },

        async validationRoom() {
            try {
                this.active.disabled.room = true
                await this.$store.dispatch('room/postPutRoom', this.form_room)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal.room = false
                        this.onRoomList()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabled.room = false
            }   
        },

        async onEditRoom(obj) {
            await this.openRoomModal()
            
            this.form_room.id = obj?.id
            this.form_room.health_facility_id = obj?.health_facility_id
            this.form_room.health_facility_item_id = obj?.health_facility_item_id
            this.form_room.service_class_id = obj?.service_class_id
            this.form_room.type_room_id = obj?.type_room_id
            this.form_room.costing_id = obj?.costing_id
            this.form_room.name = obj?.name
            this.form_room.is_surgery = obj?.is_surgery
            this.form_room.is_isolation = obj?.is_isolation
            this.form_room.room_pressure = obj?.room_pressure
            this.form_room.gender_availability = obj?.gender_availability
            this.form_room.is_internal = obj?.is_internal
        },

        removeRoom(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda yakin ingin menghapus ruangan ${obj.name} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-600', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('room/remove', obj.id)
                    .then(
                        res => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.onRoomList()
                        }
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    })
                }
            })
        },

        onRoomList() {
            this.doctorFilterReady = false
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }

            this.searchTimeout = setTimeout(() => {
                if (this.requestCancelToken) {
                    this.requestCancelToken.cancel("Request sebelumnya dibatalkan.")
                }

                this.requestCancelToken = axios.CancelToken.source()

                this.$store.dispatch("room/getAllList", { params: this.params, cancelToken: this.requestCancelToken.token })
                .then((res) => {
                    this.room_list = res.rows
                    this.totalRows = res.iTotal
                    // console.log('this.room_list', res);                    
                })
                .catch((err) => {
                    if (axios.isCancel(err)) {
                        console.log("Request dibatalkan:", err.message)
                    } else {
                        console.error("Error saat request:", err)
                    }
                })
            }, 300)
        },

        // ===========================================  
        
        async openCreateBed(obj) {
            await this.resetCreateBedForm()
            this.form_create_bed.room_id = obj?.room_id

            this.temp_c_room_name = obj?.room_name
            this.active.modal.cBed = true
        },

        async resetCreateBedForm() {
            this.form_create_bed.id = ''
            this.form_create_bed.room_id = ''
            this.form_create_bed.name = ''
        },

        async validationCreateBed() {
            try {
                this.active.disabled.cBed = true
                await this.$store.dispatch('bed/postPutBed', this.form_create_bed)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal.cBed = false
                        this.onRoomList()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabled.cBed = false
            }   
        },

        async openPutBed(obj) {
            await this.resetPutBedForm()

            this.form_put_bed.id = obj?.id
            this.form_put_bed.name = obj?.name
            this.form_put_bed.type_status_bed_id = obj?.type_status_bed_id

            this.temp_p_room_name = obj?.room_name
            this.active.modal.pBed = true
        },

        async resetPutBedForm() {
            this.form_put_bed.id = ''
            this.form_put_bed.name = ''
            this.form_put_bed.type_status_bed_id = ''
        },

        async validationPutBed() {
            try {
                this.active.disabled.pBed = true
                await this.$store.dispatch('bed/postPutBed', this.form_put_bed)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal.pBed = false
                        this.onRoomList()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabled.pBed = false
            }
        },

        removeBed(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda yakin ingin menghapus bed ${obj.name} dari ruangan ${obj?.room_name} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-600', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('bed/deleteBed', obj.id)
                    .then(
                        res => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.onRoomList()
                        }
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    })
                }
            })
        },
        
        // ===========================================        

        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
    },

    created () {
        this.onRoomList()
        this.$store.dispatch('healthfacilityitem/getParam', this.hfi_inpatient)
        this.$store.dispatch('costing/getParamByTypeCostingId', 7)
        this.$store.dispatch('helper/getParam', 'type_status_bed')
        this.$store.dispatch('helper/getParam', 'type_room')
    }
}
</script>