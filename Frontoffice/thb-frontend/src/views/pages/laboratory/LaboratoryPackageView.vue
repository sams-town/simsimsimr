<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Laboratorium' }
            ]"
        >
            <TopLaboratoryMenu selectedMenu="menu4" />
            <br>
            <section class="">
                <div class="row w-100">
                    <div class="col-lg-3 mt-8 mt-lg-0">
                        <label class="fs-13 mb-0">Pemeriksaan</label>
                        <b-form-input v-model="params.name" @input="onList" size="sm" />
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-12">
                    <div>
                        <b-button size="sm" variant="primary" @click="openModal">
                            <feather-icon icon="PlusCircleIcon" class="mr-1" />
                            <span class="fs-12">Tambah Pemeriksaan</span>
                        </b-button>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="fs-12 mr-1">Tampilkan</span>
                        <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions" />
                    </div>
                </div>

                <section class="mt-12">
                    <b-table bordered fixed small head-variant="light" :items="room_list" :fields="packageFields" :per-page="0" :current-page="currentPage" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                        <template #cell(package)="row">{{ row.item.package.length }}</template>

                        <template #cell(action)="row">
                            <b-button @click="onEditRoom(row.item)" variant="warning-500" size="sm" class="mr-1">
                                <feather-icon icon="EditIcon" />
                            </b-button>
                            <b-button @click="removeRoom(row.item)" variant="danger-500" size="sm" class="mr-1">
                                <feather-icon icon="TrashIcon" />
                            </b-button>
                            <!-- <template v-if="row.item.package?.length > 0"> -->
                                <b-button size="sm" @click="row.toggleDetails" class="mr-1" :variant="row.detailsShowing ? 'w3green' : 'outline-w3green'">
                                    <feather-icon :icon="row.detailsShowing ? 'EyeOffIcon' : 'EyeIcon'" /> Detail
                                </b-button>
                            <!-- </template> -->
                        </template>

                        <template #row-details="row" class="bg-danger">
                            <div class="bg-success-100 py-1 font-weight-bold">{{ row.item.name }}</div>
                            <div class="text-left py-1">
                                <b-button class="mr-1"
                                    @click="openCreateHPP({ laboratory_package_id: row.item.id, name: row.item.name })" 
                                    variant="primary-500" size="sm">
                                    <feather-icon icon="PlusCircleIcon" class="mr-1"/> 
                                    <span class="fs-12">Tambah BHP</span>
                                </b-button>
                                <b-button class="mr-1" 
                                    @click="openCreateParameter({ laboratory_package_id: row.item.id, title: row.item.name, parameter: row.item })" 
                                    variant="primary-500" size="sm">
                                    <feather-icon icon="EyeIcon" class="mr-1"/> 
                                    <span class="fs-12">Edit Parameter</span>
                                </b-button>
                            </div>
                            <b-table small responsive :fields="parameterFields" no-sort-reset :items="row.item.collation">
                                <template #cell(collation_name)="row">
                                    <p class="text-regular-14 mt-4"><b>{{ row.item.collation_name }}</b></p>
                                    <b-table small responsive :fields="iparameterFields" no-sort-reset :items="row.item.parameter">
                                    </b-table>
                                </template>
                            </b-table>
                            <b-table small responsive :fields="hppFields" :items="row.item.hpp" >
                                <template #cell(action)="data">
                                    <b-button 
                                        @click="openPutHPP({ ...data.item, name: row.item.name })" 
                                        variant="warning-500" size="sm" class="mr-1">
                                        <feather-icon icon="EditIcon" />
                                    </b-button>
                                    <b-button @click="removeHPP({ ...data.item, name: row.item.name })" variant="danger-500" size="sm" class="mr-1">
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
                                ANDA SEDANG MENGELOLA PEMERIKSAAN LABORATORIUM 
                            </div>
                            <feather-icon icon="XIcon" size="18" @click="close()" class="pointer"/>
                        </div>
                    </template>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="col-lg-4 mt-8 mt-lg-0">
                                <label class="fs-13 mb-0">Nama</label>
                                <b-form-input v-model="form_package.name" autocomplete="off" size="sm"/>
                            </div>
                        </div>
                    </div>
                    <template #modal-footer>
                        <div class="w-100 d-flex justify-content-end m-0">
                            <b-button @click="validationRoom" :disabled="active.disabled.room" variant="primary" size="sm" class="px-32">SIMPAN</b-button>
                        </div>
                    </template>
                </b-modal>

                <b-modal v-model="active.modal.cHPP" size="sm" no-close-on-backdrop no-close-on-esc>
                    <template #modal-header="{ close }">
                        <div class="d-flex justify-content-between w-100">
                            <div class="text-uppercase font-weight-bold fs-13 pr-16">
                                ANDA SEDANG MENAMBAHKAN HPP PADA PEMERIKSAAN
                                <span class="text-primary">{{ temp_c_name }}</span>
                            </div>
                            <feather-icon icon="XIcon" size="18" @click="close()" class="pointer"/>
                        </div>
                    </template>

                    <div>
                        <label class="fs-13 mb-0">BMHP</label>
                        <v-select v-model="form_create_hpp.pharmacy_catalog_id" :options="CatalogOptions" :reduce="CatalogOptions => CatalogOptions.value" label="title" placeholder="-- Pilih Katalog --"></v-select>
                    </div>

                    <template #modal-footer>
                        <div class="w-100 d-flex justify-content-end m-0">
                            <b-button @click="validationCreateHPP" :disabled="active.disabled.cHPP" variant="primary" size="sm" class="px-32">SIMPAN</b-button>
                        </div>
                    </template>
                </b-modal>

                <b-modal v-model="active.modal.cParameter" size="sm" no-close-on-backdrop no-close-on-esc>
                    <template #modal-header="{ close }">
                        <div class="d-flex justify-content-between w-100">
                            <div class="text-uppercase font-weight-bold fs-13 pr-16">
                                PARAMETER TINDAKAN
                                <span class="text-primary">{{   }}</span>
                            </div>
                            <feather-icon icon="XIcon" size="18" @click="close()" class="pointer"/>
                        </div>
                    </template>

                    <div>
                        <label class="fs-13 mb-0">Pemeriksaan</label>
                        <v-select v-model="form_create_parameter.laboratory_collation_id" @input="onAddCollation($event)" :options="CollationOptions" :reduce="CollationOptions => CollationOptions" label="title" placeholder="-- Pemeriksaan --"></v-select>
                    </div>

                    <b-table bordered fixed small head-variant="light" :items="collation_parameter_dummy" :fields="collationParameterFields" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                        <template #row-details="row"></template>
                        <template #cell(parameter)="row">
                            <div v-if="row.item.type == 'parent'" class="d-flex justify-content-between align-items-center bg-primary text-white px-3 py-2 rounded">
                                <strong>{{ row.item.group_name }}</strong>    
                                <b-button size="sm" variant="danger" @click="removeGroup(row.item.group_name)" > <feather-icon icon="TrashIcon" size="11"/> </b-button>
                            </div>
                            <div v-else class="ps-3"> {{ row.item.name }} </div>
                        </template>
                        <template #cell(value)="row">
                            <div v-if="row.item.type == 'child'">{{ row.item.value }}</div>
                        </template>
                        <template #cell(unit)="row">
                            <div v-if="row.item.type == 'child'">{{ row.item.unit }}</div>
                        </template>
                    </b-table>

                    <template #modal-footer>
                        <div class="w-100 d-flex justify-content-end m-0">
                            <b-button @click="validationPutCollation" :disabled="active.disabled.pParameter" variant="primary" size="sm" class="px-32">SIMPAN</b-button>
                        </div>
                    </template>
                </b-modal>

                <b-modal v-model="active.modal.pHPP" size="sm" no-close-on-backdrop no-close-on-esc>
                    <template #modal-header="{ close }">
                        <div class="d-flex justify-content-between w-100">
                            <div class="text-uppercase font-weight-bold fs-13 pr-16">
                                ANDA SEDANG MENGEDIT HPP PADA PEMERIKSAAN
                                <span class="text-primary">{{ temp_p_name }}</span>
                            </div>
                            <feather-icon icon="XIcon" size="18" @click="close()" class="pointer"/>
                        </div>
                    </template>

                    <div>
                        <label class="fs-13 mb-0">BHP</label>
                        <v-select v-model="form_put_hpp.pharmacy_catalog_id" :options="CatalogOptions" :reduce="CatalogOptions => CatalogOptions.value" label="title" placeholder="-- Pilih Katalog --"></v-select>
                    </div>

                    <template #modal-footer>
                        <div class="w-100 d-flex justify-content-end m-0">
                            <b-button @click="validationCreateHPP" :disabled="active.disabled.cHPP" variant="primary" size="sm" class="px-32">SIMPAN</b-button>
                        </div>
                    </template>
                </b-modal>

            </section>
        </Layout>
    </div>
</template>

<script>
import { mapState } from 'vuex'
import TopLaboratoryMenu from '@/@core/components/top-menu/TopLaboratoryMenu.vue';
import axios from 'axios'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import optionMixin from '@/mixins/optionMixin'

export default {
    mixins: [optionMixin],
    components: { TopLaboratoryMenu },
    data() {
        return {
            hfi_inpatient: '40f06534-07ec-4221-ad9d-dea08196079f',
            active: {
                modal: {
                    room: false,
                    cHPP: false,
                    pHPP: false,
                    pParameter: false,
                    cParameter: false,
                },
                disabled: {
                    room: false,
                    cHPP: false,
                    cParameter: false,
                    pParameter: false,
                }
            },

            // ================================

            packageFields: [
                { key: 'action', label: 'Aksi', thStyle: { width: '190px' },},
                { key: 'name', label: 'Pemeriksaan', sortable: true },
                { key: 'category', label: 'Kategori', sortable: true },
            ],

            params: {
                limit: 20,
                offset: 0,
                name: ''
            },
            currentPage: 1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            totalRows: 0,
            pageOptions: [20, 50, 100],

            room_list: [],
            form_package: {
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

            hppFields: [
                { key: 'action', label: 'Aksi', thStyle: { width: '150px' },},
                { key: 'pharmacy_catalog_name', label: 'BHP', sortable: true },
                { key: 'qty', label: 'qty', sortable: true },
                { key: 'package_name', label: 'Satuan', sortable: true },
            ],

            parameterFields: [
                { key: 'collation_name', label: '' },
            ],

            iparameterFields: [
                { key: 'name', label: 'Parameter' },
                { key: 'value', label: 'Nilai Normal' },
                { key: 'unit', label: 'Satuan' },
            ],

            collationParameterFields: [
                {key: 'parameter',label: 'Parameter', thStyle: { width: '190px' }},
                {key: 'value',label: 'Nilai Normal'},
                {key: 'unit',label: 'Satuan'},
            ],

            laboratory_package_id_dummy:'',
            collation_parameter_dummy:[],

            temp_c_name: '',
            form_create_hpp: {
                id: '',
                laboratory_package_id: '',
                pharmacy_catalog_id: '',
            },
            form_create_parameter: {
                id: '',
                laboratory_package_id: '',
                laboratory_collation_id: '',
            },
            
            temp_p_name: '',
            form_put_hpp: {
                id: '',
                laboratory_package_id: '',
                pharmacy_catalog_id: '',
            },
            form_put_parameter: {
                id: '',
                laboratory_package_id: '',
                laboratory_collation_id: '',
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

                this.onList()
            }
        },
        perPage: {
            handler: function (value) {
                this.params.limit = this.perPage
                this.onList()
            }
        },
    },

    computed: {
        ...mapState({ 
            OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,
            TypeRoomOptions: state => state.helper.options.type_room,
            OptionsServiceClass: state => state.helper.options.service_class,
            CatalogOptions: state => state.catalog.options.catalog, 
            CollationOptions: state => state.laboratory.options.collation, 
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
        onAddCollation(event) {
            console.log(event)
            this.collation_parameter_dummy.push({
                type: 'parent',
                group_id: event.value,
                group_name: event.title
            })
            for (let i = 0; i < event.parameter.length; i++) {
                this.collation_parameter_dummy.push({
                    type: 'child',
                    parent_id: event.value,
                    parent_name: event.title,
                    name: event.parameter[i].name,
                    value: event.parameter[i].value,
                    unit: event.parameter[i].unit
                })
            }
        },

        removeGroup(groupName) {
            this.collation_parameter_dummy =
                this.collation_parameter_dummy.filter(item => {
                return item.group_name !== groupName &&
                    item.parent_name !== groupName
            })
        },

        async openModal() {
            await this.resetRoom()
            this.active.modal.room = true
        },

        async resetRoom() {
            this.form_package.id = ''
            this.form_package.health_facility_id = this.hfi_inpatient
            this.form_package.health_facility_item_id = ''
            this.form_package.service_class_id = ''
            this.form_package.type_room_id = ''
            this.form_package.costing_id = ''
            this.form_package.name = ''
            this.form_package.is_surgery = ''
            this.form_package.is_isolation = ''
            this.form_package.room_pressure = ''
            this.form_package.gender_availability = ''
            this.form_package.is_internal = ''
        },

        async validationRoom() {
            try {
                this.active.disabled.room = true
                await this.$store.dispatch('room/postPutRoom', this.form_package)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal.room = false
                        this.onList()
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
            await this.openModal()
            
            this.form_package.id = obj?.id
            this.form_package.health_facility_id = obj?.health_facility_id
            this.form_package.health_facility_item_id = obj?.health_facility_item_id
            this.form_package.service_class_id = obj?.service_class_id
            this.form_package.type_room_id = obj?.type_room_id
            this.form_package.costing_id = obj?.costing_id
            this.form_package.name = obj?.name
            this.form_package.is_surgery = obj?.is_surgery
            this.form_package.is_isolation = obj?.is_isolation
            this.form_package.room_pressure = obj?.room_pressure
            this.form_package.gender_availability = obj?.gender_availability
            this.form_package.is_internal = obj?.is_internal
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
                            this.onList()
                        }
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    })
                }
            })
        },

        onList() {
            this.doctorFilterReady = false
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }

            this.searchTimeout = setTimeout(() => {
                if (this.requestCancelToken) {
                    this.requestCancelToken.cancel("Request sebelumnya dibatalkan.")
                }

                this.requestCancelToken = axios.CancelToken.source()

                this.$store.dispatch("laboratory/getPackage", { params: this.params, cancelToken: this.requestCancelToken.token })
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
        
        async openCreateHPP(obj) {
            await this.resetCreateHPPForm()
            this.form_create_hpp.laboratory_package_id = obj?.laboratory_package_id

            this.temp_c_name = obj?.name
            this.active.modal.cHPP = true
        },

        async resetCreateHPPForm() {
            this.form_create_hpp.id = ''
            this.form_create_hpp.pharmacy_catalog_id = ''
            this.form_create_hpp.laboratory_package_id = ''
        },

        async validationCreateHPP() {
            try {
                this.active.disabled.cHPP = true
                await this.$store.dispatch('laboratory/postPutHPP', this.form_create_hpp)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal.cHPP = false
                        this.onList()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabled.cHPP = false
            }   
        },
        
        async openCreateParameter(obj) {
            this.laboratory_package_id_dummy = ''
            this.collation_parameter_dummy = []
            this.laboratory_package_id_dummy = obj.laboratory_package_id
            console.log(obj)
            obj.parameter.collation.forEach(collation => {
                this.collation_parameter_dummy.push({
                    type: 'parent',
                    cp_id: collation.id,
                    group_id: collation.collation_id,
                    group_name: collation.collation_name
                })
                collation.parameter.forEach(param => {
                    this.collation_parameter_dummy.push({
                        type: 'child',
                        parent_name: collation.collation_name,
                        name: param.name,
                        value: param.value,
                        unit: param.unit
                    })
                })
            })
            console.log(this.collation_parameter_dummy)
            this.active.modal.cParameter = true
        },

        async openPutHPP(obj) {
            await this.resetPutBedForm()

            this.form_put_hpp.id = obj?.id
            this.form_put_hpp.pharmacy_catalog_id = obj?.pharmacy_catalog_id
            this.form_put_hpp.laboratory_package_id = obj?.laboratory_package_id

            this.temp_p_name = obj?.name
            this.active.modal.pHPP = true
        },

        async resetPutBedForm() {
            this.form_put_hpp.id = ''
            this.form_put_hpp.pharmacy_catalog_id = ''
            this.form_put_hpp.laboratory_package_id = ''
        },

        async validationPutHPP() {
            try {
                this.active.disabled.pHPP = true
                await this.$store.dispatch('laboratory/postPutHPP', this.form_put_hpp)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal.pHPP = false
                        this.onList()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabled.pHPP = false
            }
        },

        async validationPutCollation() {
            try {
                this.active.disabled.pHPP = true
                await this.$store.dispatch('laboratory/postPutCollation', {data: this.collation_parameter_dummy, id:this.laboratory_package_id_dummy})
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal.pParameter = false
                        this.onList()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabled.pParameter = false
            }
        },

        removeHPP(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda yakin ingin menghapus bed ${obj.name} dari ruangan ${obj?.name} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-600', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('laboratory/deleteBed', obj.id)
                    .then(
                        res => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.onList()
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
        this.onList()
        this.$store.dispatch('healthfacilityitem/getParam', this.hfi_inpatient)
        this.$store.dispatch('costing/getParamByTypeCostingId', 7)
        this.$store.dispatch('helper/getParam', 'type_status_bed')
        this.$store.dispatch('catalog/getParam')
        this.$store.dispatch('laboratory/getCollationAll')
        this.$store.dispatch('helper/getParam', 'type_room')
    }
}
</script>
<style>
.table td {
    vertical-align: middle;
}

.bg-primary {
    background: #5b7cfa !important;
}
</style>