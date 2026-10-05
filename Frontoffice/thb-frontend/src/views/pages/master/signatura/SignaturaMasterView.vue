<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Master Signatura' }
            ]"
        >
            <section>
                <section>
                    <ModernLabel label="Waktu konsumsi/pemakaian" />
                    <div class="row w-100 mt-8">
                        <div class="col-lg-3">
                            <label class="fs-13 mb-0">Waktu</label>
                            <b-form-input v-model="time_params.name" @input="onTimeList" size="sm" placeholder="cari waktu" />
                        </div>
                    </div>
        
                    <div class="d-flex justify-content-between mt-12">
                        <div>
                            <b-button size="sm" variant="primary" @click="openTimeModal">
                                <feather-icon icon="PlusCircleIcon" class="mr-1" />
                                <span class="fs-12">Waktu konsumsi/pemakaian</span>
                            </b-button>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="fs-12 mr-1">Tampilkan</span>
                            <b-form-select id="per-page-select" v-model="time_data.perPage" size="sm" :options="time_data.pageOptions" />
                        </div>
                    </div>

                    <section class="mt-12">
                        <b-table bordered fixed small head-variant="light" :items="time_list" :fields="timeFields" :per-page="0" :current-page="time_data.currentPage" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                            <template #cell(action)="row">
                                <b-button @click="onEditTime(row.item)" variant="warning-500" size="sm" class="mr-1">
                                    <feather-icon icon="EditIcon" />
                                </b-button>
                                <b-button @click="removeTime(row.item)" variant="danger-500" size="sm" class="mr-1">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </template>
                        </b-table>
                        <div class="col px-0 mt-20">
                            <div class="d-flex">
                                <div class="mr-auto">
                                    <b-pagination v-model="time_data.currentPage" :total-rows="time_data.totalRows" :per-page="time_data.perPage" align="fill" size="sm" class="my-0"></b-pagination>
                                </div>
                                <span class="fs-12">Menampilkan {{ time_data.firstRows }} hingga 
                                    <template v-if="time_data.lastRows < time_data.totalRows">{{ time_data.lastRows }}</template> 
                                    <template v-else>{{ time_data.totalRows}}</template> 
                                    dari {{ time_data.totalRows }} entri
                                </span>
                            </div>
                        </div>
                    </section>
                </section>

                <section class="mt-32">
                    <ModernLabel label="Dosis" variant="w3green" />
                    <div class="row w-100 mt-8">
                        <div class="col-lg-3">
                            <label class="fs-13 mb-0">Dosis</label>
                            <b-form-input v-model="dose_params.name" @input="onDoseList" size="sm" placeholder="cari dosis" />
                        </div>
                        <div class="col-lg-3 mt-8 mt-lg-0">
                            <label class="fs-13 mb-0">Kemasan</label>
                            <v-select v-model="dose_params.package_id" @input="onDoseList" :options="PackageOptions" :reduce="item => item.value" label="title" placeholder="kemasan" class="select-size-sm"/>                
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-12">
                        <div>
                            <b-button size="sm" variant="w3green" @click="openDoseModal">
                                <feather-icon icon="PlusCircleIcon" class="mr-1" />
                                <span class="fs-12">Dosis</span>
                            </b-button>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="fs-12 mr-1">Tampilkan</span>
                            <b-form-select id="per-page-select" v-model="dose_data.perPage" size="sm" :options="dose_data.pageOptions" />
                        </div>
                    </div>

                    <section class="mt-12">
                        <b-table bordered fixed small head-variant="light" :items="dose_list" :fields="doseFields" :per-page="0" :current-page="dose_data.currentPage" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                            <template #cell(action)="row">
                                <b-button @click="onEditDose(row.item)" variant="warning-500" size="sm" class="mr-1">
                                    <feather-icon icon="EditIcon" />
                                </b-button>
                                <b-button @click="removeDose(row.item)" variant="danger-500" size="sm" class="mr-1">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </template>
                        </b-table>
                        <div class="col px-0 mt-20">
                            <div class="d-flex">
                                <div class="mr-auto">
                                    <b-pagination v-model="dose_data.currentPage" :total-rows="dose_data.totalRows" :per-page="dose_data.perPage" align="fill" size="sm" class="my-0"></b-pagination>
                                </div>
                                <span class="fs-12">Menampilkan {{ dose_data.firstRows }} hingga 
                                    <template v-if="dose_data.lastRows < dose_data.totalRows">{{ dose_data.lastRows }}</template> 
                                    <template v-else>{{ dose_data.totalRows}}</template> 
                                    dari {{ dose_data.totalRows }} entri
                                </span>
                            </div>
                        </div>
                    </section>
                </section>
                
                <b-modal v-model="active.modal.time" size="sm" no-close-on-backdrop>
                    <template #modal-header="{ close }">
                        <div class="d-flex justify-content-between w-100">
                            <div class="text-uppercase font-weight-bold fs-13 pr-16">
                                ANDA SEDANG MENGELOLA WAKTU
                            </div>
                            <feather-icon icon="XIcon" size="18" @click="close()" class="pointer"/>
                        </div>
                    </template>
                    
                    <div>
                        <label class="fs-13 mb-0">Waktu konsumsi/pemakaian</label>
                        <b-input-group size="sm" prepend="Setiap" append="jam">
                            <b-form-input v-model="form_time.name" type="number" autocomplete="off" />
                        </b-input-group>
                    </div>

                    <template #modal-footer>
                        <div class="w-100 d-flex justify-content-end m-0">
                            <b-button @click="validationTime" :disabled="active.disabled.time" variant="primary" size="sm" class="px-32">SIMPAN</b-button>
                        </div>
                    </template>
                </b-modal>

                <b-modal v-model="active.modal.dose" size="sm" no-close-on-backdrop>
                    <template #modal-header="{ close }">
                        <div class="d-flex justify-content-between w-100">
                            <div class="text-uppercase font-weight-bold fs-13 pr-16">
                                ANDA SEDANG MENGELOLA DOSIS
                            </div>
                            <feather-icon icon="XIcon" size="18" @click="close()" class="pointer"/>
                        </div>
                    </template>
                    
                    <section>
                        <div>
                            <label class="fs-13 mb-0">Kemasan</label>
                            <v-select v-model="form_dose.package_id" :options="PackageOptions" :reduce="item => item.value" label="title" placeholder="kemasan" class="select-size-sm" />                
                        </div>
                        <div class="mt-8">
                            <label class="fs-13 mb-0">Dosis</label>
                            <b-form-input v-model="form_dose.name" size="sm" autocomplete="off" placeholder="misal: 1, 1/2, 15 ml" />
                        </div>
                    </section>

                    <template #modal-footer>
                        <div class="w-100 d-flex justify-content-end m-0">
                            <b-button @click="validationDose" :disabled="active.disabled.dose" variant="primary" size="sm" class="px-32">SIMPAN</b-button>
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
            active: {
                modal: {
                    time: false,
                    dose: false,
                },
                disabled: {
                    time: false,
                    dose: false,
                }
            },

            // ================================

            timeFields: [
                { key: 'action', label: 'Aksi', thStyle: { width: '150px' },},
                { key: 'name', label: 'Waktu (dalam satuan jam)' },
                { key: 'date_updated', label: 'diperbarui' },
            ],

            time_params: {
                limit: 20,
                offset: 0,
                name: '',
            },

            time_data: {
                currentPage: 1,
                perPage: 20,
                firstRows: 1,
                lastRows: 20,
                totalRows: 0,
                pageOptions: [20, 50, 100],
            },

            time_list: [],
            form_time: {
                id: '',
                name: '',
            },

            time_requestCancelToken: null,
            time_searchTimeout: null,

            // ================================

            doseFields: [
                { key: 'action', label: 'Aksi', thStyle: { width: '150px' },},
                { key: 'name', label: 'Dosis' },
                { key: 'package_name', label: 'Kemasan' },
                { key: 'date_updated', label: 'diperbarui' },
            ],

            dose_params: {
                limit: 20,
                offset: 0,
                name: '',
                package_id: '',
            },

            dose_data: {
                currentPage: 1,
                perPage: 20,
                firstRows: 1,
                lastRows: 20,
                totalRows: 0,
                pageOptions: [20, 50, 100],
            },

            dose_list: [],
            form_dose: {
                id: '',
                name: '',
            },

            dose_requestCancelToken: null,
            dose_searchTimeout: null,

            form_dose: {
                id: '',
                package_id: '',
                name: '',
            }
        }
    },

    watch: {
        "time_data.currentPage": {
            handler: function (value) {
                this.time_data.firstRows = this.time_data.perPage * (value - 1) + 1
                this.time_data.lastRows = this.time_data.perPage * (value)
                this.time_params.offset = this.time_data.perPage * (value - 1)
                this.time_params.limit = this.time_data.perPage

                this.onTimeList()
            }
        },
        "time_data.perPage": {
            handler: function (value) {
                this.time_params.limit = this.time_data.perPage
                this.onTimeList()
            }
        },
        "dose_data.currentPage": {
            handler: function (value) {
                this.dose_data.firstRows = this.dose_data.perPage * (value - 1) + 1
                this.dose_data.lastRows = this.dose_data.perPage * (value)
                this.dose_params.offset = this.dose_data.perPage * (value - 1)
                this.dose_params.limit = this.dose_data.perPage

                this.onDoseList()
            }
        },
        "dose_data.perPage": {
            handler: function (value) {
                this.time_params.limit = this.dose_data.perPage
                this.onDoseList()
            }
        },
    },

    computed: {
        ...mapState({ 
            PackageOptions: state => state.packages.options.packages,
        }),
    },

    methods: {
        async openTimeModal() {
            await this.resetRoom()
            this.active.modal.time = true
        },

        async resetRoom() {
            this.form_time.id = ''
            this.form_time.name = ''
        },

        async validationTime() {
            try {
                this.active.disabled.time = true
                await this.$store.dispatch('kardex/postPutSignaturaTime', this.form_time)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal.time = false
                        this.onTimeList()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabled.time = false
            }   
        },

        async onEditTime(obj) {
            await this.openTimeModal()
            
            this.form_time.id = obj?.id
            this.form_time.name = obj?.name
        },

        removeTime(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda yakin ingin menghapus signatura waktu - ${obj.name} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-600', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('kardex/deleteSignaturaTime', obj.id)
                    .then(
                        res => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.onTimeList()
                        }
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    })
                }
            })
        },

        onTimeList() {
            if (this.time_searchTimeout) {
                clearTimeout(this.time_searchTimeout);
            }

            this.time_searchTimeout = setTimeout(() => {
                if (this.time_requestCancelToken) {
                    this.time_requestCancelToken.cancel("Request sebelumnya dibatalkan.")
                }

                this.time_requestCancelToken = axios.CancelToken.source()

                this.$store.dispatch("kardex/getSignaturaTime", { params: this.time_params, cancelToken: this.time_requestCancelToken.token })
                .then((res) => {
                    this.time_list = res.rows
                    this.time_data.totalRows = res.iTotal
                    // console.log('this.time_list', res);                    
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

        async openDoseModal() {
            await this.resetDose()
            this.active.modal.dose = true
        },

        async resetDose() {
            this.form_dose.id = ''
            this.form_dose.name = ''
            this.form_dose.package_id = ''
        },

        async validationDose() {
            try {
                this.active.disabled.dose = true
                await this.$store.dispatch('kardex/postPutSignaturaDose', this.form_dose)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal.dose = false
                        this.onDoseList()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabled.dose = false
            }   
        },

        async onEditDose(obj) {
            await this.openDoseModal()
            
            this.form_dose.id = obj?.id
            this.form_dose.name = obj?.name
            this.form_dose.package_id = obj?.package_id
        },

        removeDose(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda yakin ingin menghapus signatura dosis - ${obj.name} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-600', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('kardex/deleteSignaturaDose', obj.id)
                    .then(
                        res => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.onDoseList()
                        }
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    })
                }
            })
        },

        onDoseList() {
            if (this.dose_searchTimeout) {
                clearTimeout(this.dose_searchTimeout);
            }

            this.dose_searchTimeout = setTimeout(() => {
                if (this.dose_requestCancelToken) {
                    this.dose_requestCancelToken.cancel("Request sebelumnya dibatalkan.")
                }

                this.dose_requestCancelToken = axios.CancelToken.source()

                this.$store.dispatch("kardex/getSignaturaDose", { params: this.dose_params, cancelToken: this.dose_requestCancelToken.token })
                .then((res) => {
                    this.dose_list = res.rows
                    this.dose_data.totalRows = res.iTotal
                    // console.log('this.dose_list', res);                    
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
        
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
    },

    created() {
        this.$store.dispatch('packages/getParam')
        this.onTimeList()
        this.onDoseList()
    }
}
</script>