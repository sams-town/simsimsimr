<template>
    <div>
        <section class="card">
            <div class="card-header font-weight-bold bg-primary-200">Filter Data</div>
            <div class="card-body">
                <div class="d-flex justify-content-center">
                    <div class="col-lg-4">
                        <div class="">
                            <label class="c-form-label">Nama Paket</label>
                            <b-form-input v-model="params.name" />
                        </div>
                        <div class="d-flex mt-16">
                            <b-button @click="getBundle" :disabled="isDisabledFilter" variant="primary" class="mr-2" > 
                                <feather-icon icon="SearchIcon"/> Filter
                            </b-button>
                            <b-button @click="clearFilter" :disabled="isDisabledFilter" variant="outline-primary" > 
                                <feather-icon icon="XIcon"/> Reset
                            </b-button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="mt-32">
            <div class="d-flex justify-content-between align-items-center">
                <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0">
                    <span class="text-regular-14">Tampilkan</span>
                    <b-form-select v-model="perPage" size="sm" :options="pageOptions" class="mx-1"/>
                    <span class="text-regular-14">Entri</span>
                </b-form-group>
                <div>
                    <b-button @click="openBundleModal" size="sm" variant="w3green">
                        <feather-icon icon="PlusIcon" class="mr-1"/>
                        Tambah Paket
                    </b-button>
                </div>
            </div>
            <b-table bordered head-variant="light" :items="bundle_list" :fields="fields" class="mt-12" style="font-size: 13px;">
                <template #cell(name)="row">
                    <div class="font-weight-bold">{{ row.item.name }}</div>
                    <div class="mt-8">{{ row.item.description }}</div>
                </template>
                <template #cell(items)="row">
                    <div class="row">
                        <li v-for="(list, index) in row.value" :key="index" class="col-lg-4" :class="index > 2 ? 'mt-10' : ''" style="list-style-type: none;">
                            <b-button @click="removeItems(list)" size="sm" variant="danger-600" class="mr-1">
                                <feather-icon icon="TrashIcon" />
                            </b-button>
                            <span class="font-weight-bold text-uppercase">[{{ list.item_type_id == 1 ? 'Laboratorium' : 'Radiologi' }}]</span>
                            {{ list.package_name }}
                        </li>
                    </div>
                </template>
                <template #cell(action)="row">
                    <div class="d-flex">
                        <b-button @click="onUpdateBundle(row.item)" size="sm" variant="warning-500" class="mr-1">
                            <feather-icon icon="EditIcon" />
                        </b-button>
                        <b-button @click="openItemsModal({ name: row.item.name, id: row.item.id})" size="sm" variant="w3green">
                            <feather-icon icon="PlusIcon" /> ITEMS
                        </b-button>
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
                            align="fill" size="sm" class="my-0">
                        </b-pagination>
                    </div>
                </div>
            </div>
        </section>

        <b-modal v-model="active.modal_bundle" title="Tambah / Update Paket" no-close-on-backdrop>
            <div>
                <div>
                    <label class="c-form-label">Nama Paket</label>
                    <b-form-input v-model="form_bundle.name" />
                </div>
                <div class="mt-10">
                    <label class="c-form-label">Deskripsi</label>
                    <b-textarea v-model="form_bundle.description" />
                </div>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end">
                    <b-button @click="validationBundle" size="sm" variant="primary">
                        <feather-icon icon="SaveIcon" /> Simpan
                    </b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active.modal_items" :title="`Tambah / Update Items ${form_items.medical_support_bundle_name}`" no-close-on-backdrop>
            <div>
                <div>
                    <label class="c-form-label">Tipe Paket</label>
                    <v-select v-model="form_items.item_type_id" :options="OptionsType" :reduce="OptionsType => OptionsType.value" label="title" placeholder="-- Tipe Paket --" />
                </div>
                <div class="mt-10">
                    <label class="c-form-label">Items</label>
                    <template v-if="form_items.item_type_id == 2">
                        <v-select v-model="form_items.package_rad_id" :options="rad_package_list" :reduce="item => item.value" label="title" placeholder="-- Items --" :disabled="!form_items.item_type_id" />
                    </template>
                    <template v-else>
                        <v-select v-model="form_items.package_lab_id" :options="lab_package_list" :reduce="item => item.value" label="title" placeholder="-- Items --" :disabled="!form_items.item_type_id" />
                    </template>
                </div>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end">
                    <b-button @click="validationItems" :disabled="getDisabledItem" size="sm" variant="primary">
                        <feather-icon icon="SaveIcon" /> Simpan
                    </b-button>
                </div>
            </template>
        </b-modal>
    </div>
</template>
<script>
import vSelect from 'vue-select'
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default {
    name: 'MedicalBundleMaster',
    components: {
        vSelect, ModernLabel
    },
    data() {
        return {
            fields: [
                { key: 'name', label: 'Nama Paket', thStyle: 'width: 400px', sortable: true },
                { key: 'items', label: 'Items', sortable: true },
                { key: 'action', label: 'Aksi', thStyle: 'width: 150px' },
            ],
            bundle_list: [],
            active: {
                modal_bundle: false,
                modal_items: false,
            },
            params: {
                offset: 0,
                limit: 20,
                name: '',
            },
            pageOptions: [20, 50, 100],
            currentPage:1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            isDisabledFilter: false,
            totalRows: 0,

            form_bundle: {
                id: '',
                name: '',
                description: '',
            },

            form_items: {
                medical_support_bundle_id: '',
                medical_support_bundle_name: '',
                package_lab_id: '',
                package_rad_id: '',
                item_type_id: 1,
            },

            OptionsType: [
                { value: 1, title: 'Laboratorium' },
                { value: 2, title: 'Radiologi' },
            ],
            lab_package_list: [],
            rad_package_list: [],
        }
    },
    computed: {
        getDisabledItem() {
            if (this.form_items.item_type_id && (this.form_items.package_lab_id || this.form_items.package_rad_id)) {
                return false
            }
            return true
        }
    },
    watch: {
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage*(value-1)+1
                this.lastRows = this.perPage*(value)+1
                this.params.offset = this.perPage*(value-1)
                this.params.limit = this.perPage
                this.getBundle()
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.getBundle()
            }
        },
        "form_items.item_type_id": function (val) {
            if (val == 2) {
                this.form_items.package_lab_id = ''
            } else if (val == 1) {
                this.form_items.package_rad_id = ''
            } else {
                this.form_items.package_lab_id = ''
                this.form_items.package_rad_id = ''
            }
        },
    },
    methods: {
        // ============================ BUNDLE ============================

        getBundle() {
            try {
                this.isDisabledFilter = true
                
                this.$store.dispatch('healthfacility/getListBundle', this.params).then((response) => {
                    this.bundle_list = response.rows
                    this.totalRows = response.iTotal
                    this.isDisabledFilter = false
                }, error => {
                    console.error('Error while GET healthfacility/getListBundle at: ', error);
                    this.isDisabledFilter = false
                })
            } catch (error) {
                console.error('Error catch while GET healthfacility/getListBundle at: ', error);
                this.isDisabledFilter = false
            }
        },
        clearFilter() {
            this.params.name = ''
            this.getBundle()
        },
        openBundleModal() {
            this.resetFormBundle()
            this.active.modal_bundle = true
        },
        resetFormBundle() {
            this.form_bundle.id = ''
            this.form_bundle.name = ''
            this.form_bundle.description = ''
        },
        validationBundle () {
            try {
                this.$store.dispatch('healthfacility/postPutBundle', this.form_bundle)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal_bundle = false
                        this.resetFormBundle()
                        this.getBundle()
                    } 
                    // err => {
                    //     this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    //     console.error('Error at: ', err)         
                    // }
                )
                .catch(({error, response}) => {
                    this.notification('Failed','CloseIcon','danger',`${error}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            }
        },
        onUpdateBundle(obj) {
            this.openBundleModal()
            this.form_bundle.id = obj.id ? obj.id : ''
            this.form_bundle.name = obj.name ? obj.name : ''
            this.form_bundle.description = obj.description ? obj.description : ''
        },


        // ============================ ITEMS ============================

        openItemsModal(bundle) {
            this.resetFormItems()
            this.active.modal_items = true
            this.form_items.medical_support_bundle_id = bundle.id
            this.form_items.medical_support_bundle_name = bundle.name
        },

        resetFormItems() {
            this.form_items.medical_support_bundle_id = ''
            this.form_items.package_id = ''
        },

        validationItems() {
            let temp_data = {
                medical_support_bundle_id: this.form_items.medical_support_bundle_id,
                items: {
                    item_type_id: this.form_items.item_type_id,
                    package_id: this.form_items.item_type_id == 2 ? this.form_items.package_rad_id : this.form_items.package_lab_id 
                },
            }

            try {
                this.$store.dispatch('healthfacility/putItemsBundle', temp_data)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal_items = false
                        this.getBundle()
                        this.form_items.package_lab_id = ''
                        this.form_items.package_rad_id = ''
                        this.form_items.item_type_id = ''
                    } 
                )
                .catch(({error, response}) => {
                    this.notification('Failed','CloseIcon','danger',`${error}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            }
        },

        removeItems(obj) {
            // console.log(obj);
            let temp_data = {
                medical_support_bundle_id: obj.medical_support_bundle_id,
                package_id: obj.package_id
            }

            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus ${obj.package_name} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('healthfacility/removeItemsBundle', temp_data)
                    .then(
                        response => {
                            if (response.status === 200 || response.status === 201) {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getBundle()
                            } else {
                                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            }
                        },
                        error => {
                            this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            console.error('Error While Delete Using healthfacility/removeItemsBundle at: ', error)
                        }
                    )
                    .catch(({error, response}) => {
                        this.notification('Failed','CloseIcon','danger',`${error}`)
                    })
                }
            })
        },

        getLabPackageListV2() {
            this.$store.dispatch('laboratory/getLaboratoryPackageV2').then((response) => {
                let res = response
                if (res && res.length > 0) {
                    this.lab_package_list = []

                    for (let i = 0; i < res.length; i++) {
                        const el = res[i];
                        let list = el.category_items

                        for (let j = 0; j < list.length; j++) {
                            const item = list[j];
                            let temp_selected = []
                            temp_selected = {
                                value: item.id,
                                title: item.costing_name
                            }

                            this.lab_package_list.push(temp_selected)
                        }
                    }
                }
            })
        },
        getRadPackageListV2(){
            this.$store.dispatch('radiology/getRadiologyPackageV2').then((response) => {
                let res = response
                if (res && res.length > 0) {
                    this.rad_package_list = []

                    for (let i = 0; i < res.length; i++) {
                        const el = res[i];
                        let list = el.category_items

                        for (let j = 0; j < list.length; j++) {
                            const item = list[j];
                            let temp_selected = []
                            temp_selected = {
                                value: item.id,
                                title: item.costing_name
                            }

                            this.rad_package_list.push(temp_selected)
                        }
                    }
                }
            })
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
    },
    created () {
        this.getBundle()
        this.getLabPackageListV2()
        this.getRadPackageListV2()
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>