<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Master Department', link:'/master/department' },
                { text: dept_name },
            ]"
        />
        <section class="content pt-12">
            <div class="d-flex justify-content-between">
                <div class="row w-100 align-items-end">
                    <div class="col-lg-3">
                        <label class="fs-13 mb-0">Instalasi Sub</label>
                        <v-select class="select-size-sm" @input="filterSub" v-model="params.health_facility_item_id" aria-readonly="true" :options="healthFacilityItemSearchList" :reduce="healthFacilityItemSearchList => healthFacilityItemSearchList && healthFacilityItemSearchList.value" label="title" placeholder="-- Instalasi Sub --"></v-select>
                    </div>
                    <!-- <div class="col-lg-3">
                        <label class="fs-13 mb-0">Jabatan</label>
                        <v-select class="select-size-sm" @input="filterSub" v-model="params.position_id" aria-readonly="true" :options="OptPositionList" :reduce="OptPositionList => OptPositionList && OptPositionList.value" label="title" placeholder="-- Jabatan --"></v-select>
                    </div> -->
                </div>
            </div>
            <div class="d-flex justify-content-between mt-12">
                <div>
                    <b-button size="sm" variant="primary" @click="openAddPositionModal">
                        <feather-icon icon="PlusCircleIcon" class="mr-1" />
                        <span class="fs-12">Tambah Jabatan</span>
                    </b-button>
                </div>
                <div class="d-flex align-items-center">
                    <span class="fs-12 mr-1">Tampilkan</span>
                    <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions" />
                </div>
            </div>

            <section class="mt-12">
                <b-table empty-text="Tidak ada Data" bordered fixed small head-variant="light" :items="position_list" :fields="positionFields" :per-page="0" :current-page="currentPage" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                    <template #cell(action)="row">
                        <b-button @click="onEditPosition(row.item)" variant="warning-500" size="sm" class="mr-1">
                            <feather-icon icon="EditIcon" />
                        </b-button>
                        <b-button @click="removePosition(row.item)" variant="danger-500" size="sm" class="mr-1">
                            <feather-icon icon="TrashIcon" />
                        </b-button>
                        <router-link :to="{ path: '/master/department/employee/' + row.item.id + '/' + row.item.department_id }">
                            <b-button size="sm" variant="primary" v-b-popover.hover.top="'Buka Kepegawaian'" class="mr-1">
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
            
            <b-modal v-model="active.modal.position" size="md" no-close-on-backdrop>
                <template #modal-header="{ close }">
                    <div class="d-flex justify-content-between w-100">
                        <div class="text-uppercase font-weight-bold fs-13 pr-16">
                            ANDA SEDANG MENGELOLA JABATAN 
                        </div>
                        <feather-icon icon="XIcon" size="18" @click="close()" class="pointer"/>
                    </div>
                </template>
                <div class="row">
                    <div class="col-lg-6">
                        <div>
                            <label class="c-form-label">Departemen</label>
                            <div>{{ dept_name }}</div>
                            <!-- <v-select v-model="form.health_facility_id" aria-readonly="true" :options="OptionsHealthFacility" :reduce="OptionsHealthFacility => OptionsHealthFacility.value" label="title" placeholder="--Instalasi --"></v-select> -->
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">Instalasi</label>
                            <v-select v-model="form.health_facility_id" aria-readonly="true" :options="OptionsHealthFacility" :reduce="OptionsHealthFacility => OptionsHealthFacility && OptionsHealthFacility.value" label="title" placeholder="--Instalasi --"></v-select>
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">Instalasi Sub</label>
                            <v-select v-model="form.health_facility_item_id" aria-readonly="true" :options="OptionsHealthFacilityItem" :reduce="OptionsHealthFacilityItem => OptionsHealthFacilityItem && OptionsHealthFacilityItem.value" label="title" placeholder="--Instalasi Sub --"></v-select>
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">Jabatan</label>
                            <b-form-input v-model="form.name" autocomplete="off"/>
                        </div>
                    </div>
                </div>
                <template #modal-footer>
                    <div class="w-100 d-flex justify-content-end m-0">
                        <b-button @click="validationPosition" :disabled="active.disabled.room" variant="primary" size="sm" class="px-32">SIMPAN</b-button>
                    </div>
                </template>
            </b-modal>
        </section>
    </div>
</template>

<script>
import { mapState } from 'vuex'
import axios from 'axios'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import optionMixin from '@/mixins/optionMixin'
import vSelect from 'vue-select'

export default {
    mixins: [optionMixin],
    components: { vSelect },
    data() {
        return {
            hfi_inpatient: '40f06534-07ec-4221-ad9d-dea08196079f',
            dept_id: null,
            dept_name: '',
            active: {
                modal: {
                    position: false,
                },
                disabled: {
                    position: false,
                }
            },

            // ================================

            positionFields: [
                { key: 'action', label: 'Aksi', thStyle: { width: '150px' },},
                { key: 'health_facility_item_name', label: 'Unit Kerja Sub', sortable: true },
                { key: 'name', label: 'Jabatan', sortable: true },
            ],

            params: {
                limit: 20,
                offset: 0,
                department_id: null,
                health_facility_id: null,
                // position_id: null,
            },
            currentPage: 1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            totalRows: 0,
            pageOptions: [20, 50, 100],

            position_list: [],

            requestCancelToken: null,
            searchTimeout: null,

            form: {
                id:null,
                health_facility_id:'',
                health_facility_item_id:'',
                name:'',
                department_id:null
            },

            healthFacilityItemSearchList:[],
            OptPositionList:[],
        }
    },

    watch: {
        currentPage: {
            handler: function (value) {
                this.firstRows = this.perPage * (value - 1) + 1
                this.lastRows = this.perPage * (value)
                this.params.offset = this.perPage * (value - 1)
                this.params.limit = this.perPage

                this.filterSub()
            }
        },
        perPage: {
            handler: function (value) {
                this.params.limit = this.perPage
                this.filterSub()
            }
        },
        'form.health_facility_id' (obj) {
            return this.$store.dispatch('healthfacilityitem/getParam', obj)
        }
    },
    computed: {
        ...mapState({
            OptionsHealthFacility: state => state.healthfacility.options.healthfacility,
            OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem 
        }),
    },

    methods: {
        async openAddPositionModal(){
            await this.resetPosition()
            await this.openPositionModal()
        },

        async openPositionModal() {
            this.active.modal.position = true
        },
    
        async resetPosition() {
            this.form.id = ''
            this.form.health_facility_id = ''
            this.form.health_facility_item_id = ''
            this.form.name = ''
        },

        validationPosition() {
            try {
                this.active.disabled.position = true
                this.$store.dispatch('position/formPostPut', this.form)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal.position = false
                        this.filterSub()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabled.position = false
            }   
        },

        async onEditPosition(obj) {
            await this.resetPosition()
            this.form.id = obj.id
            this.form.health_facility_id = obj.health_facility_id
            this.form.health_facility_item_id = obj.health_facility_item_id
            this.form.name = obj.name

            await this.openPositionModal()
        },

        removePosition(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda yakin ingin menghapus Jabatan ${obj.name} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-600', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('position/remove', obj.id)
                    .then(
                        res => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.filterSub()
                        }
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    })
                }
            })
        },
        
        filterSub () {
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }

            this.searchTimeout = setTimeout(() => {
                // console.log('filtersub', this.params)

                this.$store.dispatch("position/getPositionList", { params: this.params})
                .then((res) => {
                    if(res?.rows){
                        if(res?.rows.length > 0){
                            this.position_list = res.rows
                            this.dept_name = this.position_list[0].department_name
                            this.totalRows = res.iTotal
                            // console.log('this.position_list', res);  
                            // console.log('this.position_list', this.position_list);  
                        } else {
                            this.position_list = []
                            this.totalRows = 0
                        }
                    } 
                })
                .catch((err) => {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    console.error('Error catch at: ', err)      
                })
            }, 300)
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

    async created () {
        this.dept_id = this.$route.params?.id
        this.params.department_id = this.dept_id
        this.form.department_id = this.dept_id
        this.filterSub()

        // get option instalasi sub 
        const res = await this.$store.dispatch('healthfacilityitem/getParamAlls')
        const option = []
        for (let i = 0; i < res.length; i++) {
            for (const key in res[i]) {
                if (key === 'id') { option['value'] = res[i][key] } 
                else if (key === 'name') { option['title'] = res[i][key] } 
                else if (key === 'id') { option['oid'] = res[i][key] }
            }
            this.healthFacilityItemSearchList.push(Object.assign({}, option))
        } 

        // get option jabatan
        const resOptionJabatan = await this.$store.dispatch('position/getOptPosition')
        this.OptPositionList = resOptionJabatan
        console.log(resOptionJabatan, 'resOptionJabatan')
    }
}
</script>