<template>
    <div>
        <section class="card">
            <div class="card-header font-weight-bold bg-primary-200">Filter Data</div>
            <div class="card-body">
                <div class="d-flex justify-content-center">
                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-lg-6">
                                <label class="c-form-label">Nama</label>
                                <b-form-input v-model="params.name" />
                            </div>
                            <div class="col-lg-6 mt-10 mt-lg-0">
                                <label class="c-form-label">Kode</label>
                                <b-form-input v-model="params.code" />
                            </div>
                        </div>
                        <div class="d-flex mt-16">
                            <b-button @click="getDiet" :disabled="isDisabledFilter" variant="primary" class="mr-2" > 
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
                    <b-button @click="openModalMain" variant="primary">
                        <feather-icon icon="PlusIcon" class="mr-1"/> Tambah
                    </b-button>
                </div>
            </div>
            <b-table bordered fixed head-variant="light" small :items="diet_list" :fields="fields" class="mt-12" style="font-size: 13px;">

                <template #cell(action)="row">
                    <div class="d-flex">
                        <b-button @click="onUpdateDiet(row.item)" size="sm" variant="warning-500" class="mr-1">
                            <feather-icon icon="EditIcon" />
                        </b-button>
                        <b-button @click="removeDiet(row.item)" size="sm" variant="danger-500">
                            <feather-icon icon="TrashIcon" />
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

        <b-modal v-model="active.modal_main" title="Tambah/Update Master Hemodialisa" no-close-on-backdrop>
            <div>
                <div>
                    <label class="c-form-label">Nama</label>
                    <b-form-input v-model="form_nutrition.name" />
                </div>
                <div class="mt-10">
                    <label class="c-form-label">Kode</label>
                    <b-form-input v-model="form_nutrition.code" />
                </div>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end">
                    <b-button @click="validationDiet" :disabled="disabled_diet" size="sm" variant="primary">
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
    name: 'HemodialysisMaster',
    components: {
        vSelect, ModernLabel
    },
    data() {
        return {
            fields: [
                { key: 'name', label: 'Nama', sortable: true },
                { key: 'code', label: 'Kode', sortable: true },
                { key: 'action', label: 'Aksi', thStyle: 'width: 150px' },
            ],
            OptionsHemoType: [
                { value: 'N', title: 'Diagnosa' },
                { value: 'E', title: 'Etiologi' },
                { value: 'P', title: 'Penyakit Penyerta' },
            ],
            diet_list: [],
            active: {
                modal_main: false,
            },
            params: {
                offset: 0,
                limit: 20,
                code: '',
                name: '',
            },
            pageOptions: [20, 50, 100],
            currentPage:1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            isDisabledFilter: false,
            totalRows: 0,
            
            disabled_diet: false,
            form_nutrition: {
                id: '',
                code: '',
                name: '',
            },
        }
    },
    watch: {
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage*(value-1)+1
                this.lastRows = this.perPage*(value)+1
                this.params.offset = this.perPage*(value-1)
                this.params.limit = this.perPage
                this.getDiet()
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.getDiet()
            }
        },
    },
    methods: {
        getDiet() {
            try {
                this.isDisabledFilter = true
                
                this.$store.dispatch('nutrition/getListDiet', this.params).then((response) => {
                    this.diet_list = response.rows
                    this.totalRows = response.iTotal
                    this.isDisabledFilter = false
                }, error => {
                    console.error('Error while GET nutrition/getListDiet at: ', error);
                    this.isDisabledFilter = false
                })
            } catch (error) {
                console.error('Error catch while GET nutrition/getListDiet at: ', error);
                this.isDisabledFilter = false
            }
        },
        clearFilter() {
            this.params.name = ''
            this.params.code = ''
            this.getDiet()
        },
        openModalMain() {
            this.resetFormMaster()
            this.active.modal_main = true
        },
        resetFormMaster() {
            this.form_nutrition.id = ''
            this.form_nutrition.name = ''
            this.form_nutrition.code = ''
        },
        validationDiet () {
            this.disabled_diet = true
            try {
                this.$store.dispatch('nutrition/postPutDiet', this.form_nutrition)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal_main = false
                        this.resetFormMaster()
                        this.getDiet()
                    } 
                )
                .catch(({error, response}) => {
                    this.notification('Failed','CloseIcon','danger',`${error}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.disabled_diet = false
            }
        },
        onUpdateDiet(obj) {
            this.openModalMain()
            this.form_nutrition.id = obj?.id || ''
            this.form_nutrition.name = obj?.name || ''
            this.form_nutrition.code = obj?.code || ''
        },

        removeDiet(obj) {            
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus ${obj.name} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('nutrition/deleteDiet', obj.id)
                    .then(
                        response => {
                            if (response.status === 200 || response.status === 201) {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getDiet()
                            } else {
                                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            }
                        },
                        error => {
                            this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            console.error('Error While Delete Using nutrition/deleteDiet at: ', error)
                        }
                    )
                    .catch(({error, response}) => {
                        this.notification('Failed','CloseIcon','danger',`${error}`)
                    })
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
        this.getDiet()
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>