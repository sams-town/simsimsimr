<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Master Aturan Pakai' }
            ]"
        >
            <section>
                <section>
                    <div class="row w-100">
                        <div class="col-lg-3">
                            <label class="fs-13 mb-0">Aturan pakai</label>
                            <b-form-input v-model="params.name" @input="onInstructionList" size="sm" placeholder="cari aturan pakai" />
                        </div>
                    </div>
        
                    <div class="d-flex justify-content-between mt-12">
                        <div>
                            <b-button size="sm" variant="primary" @click="openModal">
                                <feather-icon icon="PlusCircleIcon" class="mr-1" />
                                <span class="fs-12">Aturan pakai</span>
                            </b-button>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="fs-12 mr-1">Tampilkan</span>
                            <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions" />
                        </div>
                    </div>

                    <section class="mt-12">
                        <b-table bordered fixed small head-variant="light" :items="instruction_list" :fields="instructionFields" :per-page="0" :current-page="currentPage" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                            <template #cell(action)="row">
                                <b-button @click="onEditInstruction(row.item)" variant="warning-500" size="sm" class="mr-1">
                                    <feather-icon icon="EditIcon" />
                                </b-button>
                                <b-button @click="removeInstruction(row.item)" variant="danger-500" size="sm" class="mr-1">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
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
                </section>
                
                <b-modal v-model="active.modal.main" size="sm" no-close-on-backdrop>
                    <template #modal-header="{ close }">
                        <div class="d-flex justify-content-between w-100">
                            <div class="text-uppercase font-weight-bold fs-13 pr-16">
                                ANDA SEDANG MENGELOLA ATURAN PAKAI
                            </div>
                            <feather-icon icon="XIcon" size="18" @click="close()" class="pointer"/>
                        </div>
                    </template>
                    
                    <div>
                        <label class="fs-13 mb-0">Aturan pakai</label>
                        <b-form-input v-model="form_instruction.name" autocomplete="off" />
                    </div>

                    <template #modal-footer>
                        <div class="w-100 d-flex justify-content-end m-0">
                            <b-button @click="validationInstruction" :disabled="active.disabled.main" variant="primary" size="sm" class="px-32">SIMPAN</b-button>
                        </div>
                    </template>
                </b-modal>
            </section>
        </Layout>
    </div>
</template>

<script>
import axios from 'axios'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import optionMixin from '@/mixins/optionMixin'

export default {
    mixins: [optionMixin],
    data() {
        return {
            active: {
                modal: {
                    main: false,
                },
                disabled: {
                    main: false,
                }
            },

            // ================================

            instructionFields: [
                { key: 'action', label: 'Aksi', thStyle: { width: '150px' },},
                { key: 'name', label: 'Aturan Pakai' },
            ],

            params: {
                limit: 20,
                offset: 0,
                name: '',
            },

            currentPage: 1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            totalRows: 0,
            pageOptions: [20, 50, 100],

            instruction_list: [],
            form_instruction: {
                id: '',
                name: '',
            },

            requestCancelToken: null,
            searchTimeout: null,
        }
    },

    watch: {
        currentPage: {
            handler: function (value) {
                this.firstRows = this.perPage * (value - 1) + 1
                this.lastRows = this.perPage * (value)
                this.params.offset = this.perPage * (value - 1)
                this.params.limit = this.perPage

                this.onInstructionList()
            }
        },
        perPage: {
            handler: function (value) {
                this.params.limit = this.perPage
                this.onInstructionList()
            }
        },
    },

    methods: {
        async openModal() {
            await this.resetForm()
            this.active.modal.main = true
        },

        async resetForm() {
            this.form_instruction.id = ''
            this.form_instruction.name = ''
        },

        async validationInstruction() {
            try {
                this.active.disabled.main = true
                await this.$store.dispatch('catalog/postPutCatalogInstruction', this.form_instruction)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal.main = false
                        this.onInstructionList()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabled.main = false
            }   
        },

        async onEditInstruction(obj) {
            await this.openModal()
            
            this.form_instruction.id = obj?.id
            this.form_instruction.name = obj?.name
        },

        removeInstruction(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda yakin ingin menghapus aturan pakai '${obj.name}' ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-600', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('catalog/deleteCatalogInstruction', obj.id)
                    .then(
                        res => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.onInstructionList()
                        }
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    })
                }
            })
        },

        onInstructionList() {
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }

            this.searchTimeout = setTimeout(() => {
                if (this.requestCancelToken) {
                    this.requestCancelToken.cancel("Request sebelumnya dibatalkan.")
                }

                this.requestCancelToken = axios.CancelToken.source()

                this.$store.dispatch("catalog/getCatalogInstruction", { params: this.params, cancelToken: this.requestCancelToken.token })
                .then((res) => {
                    this.instruction_list = res.rows
                    this.totalRows = res.iTotal
                    // console.log('this.instruction_list', this.instruction_list);                    
                    // console.log('this.totalRows', this.totalRows);                    
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
        this.onInstructionList()
    }
}
</script>