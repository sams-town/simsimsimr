<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Master Departemen' }
            ]"
        />
        <section class="content pt-12">
            <div class="d-flex justify-content-between">
                <div class="row w-100 align-items-end">
                    <div class="col-lg-4">
                        <label class="fs-13 mb-0">Departemen</label>
                        <div class="d-flex align-items-center position-relative">
                            <b-form-input v-model="searchQuery" @input="filterDepartment" class="pl-5" size="sm" autocomplete="off" placeholder="masukkan nama departemen" />
                            <b-icon icon="search" class="position-absolute pl-3 text-neutral-400" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between mt-12">
                <div>
                    <b-button size="sm" variant="primary" @click="openDepartmentModal">
                        <feather-icon icon="PlusCircleIcon" class="mr-1" />
                        <span class="fs-12">Tambah Departemen</span>
                    </b-button>
                </div>
                <div class="d-flex align-items-center">
                    <span class="fs-12 mr-1">Tampilkan</span>
                    <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions" />
                </div>
            </div>

            <section class="mt-12">
                <b-table empty-text="Tidak ada Data" bordered fixed small head-variant="light" :items="dept_list" :fields="deptFields" :per-page="0" :current-page="currentPage" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">

                    <template #cell(action)="row">
                        <b-button @click="onEditDepartment(row.item)" variant="warning-500" size="sm" class="mr-1">
                            <feather-icon icon="EditIcon" />
                        </b-button>
                        <b-button @click="removeDepartment(row.item)" variant="danger-500" size="sm" class="mr-1">
                            <feather-icon icon="TrashIcon" />
                        </b-button>
                        <router-link :to="{ path: '/master/department/position/' + row.item.id }">
                            <b-button size="sm" variant="primary" v-b-popover.hover.top="'Buka Jabatan'" class="mr-1">
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
            
            <b-modal v-model="active.modal.department" size="md" >
                <template #modal-header="{ close }">
                    <div class="d-flex justify-content-between w-100">
                        <div class="text-uppercase font-weight-bold fs-13 pr-16">
                            ANDA SEDANG MENGELOLA DEPARTEMEN 
                        </div>
                        <feather-icon icon="XIcon" size="18" @click="close()" class="pointer"/>
                    </div>
                </template>
                <div class="row">
                    <div class="col-lg-4">
                        <label class="fs-13 mb-0">Nama Departemen</label>
                        <b-form-input v-model="form_department.name" autocomplete="off" size="sm"/>                        
                    </div>
                </div>
                <template #modal-footer>
                    <div class="w-100 d-flex justify-content-end m-0">
                        <b-button @click="validationDepartment" :disabled="active.disabled.department" variant="primary" size="sm" class="px-32">SIMPAN</b-button>
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

export default {
    mixins: [optionMixin],
    data() {
        return {
            searchQuery: JSON.parse(localStorage.getItem('masterdepartmentfiltersession')) ? JSON.parse(localStorage.getItem('masterdepartmentfiltersession')).name : "", 
            hfi_inpatient: '40f06534-07ec-4221-ad9d-dea08196079f',
            active: {
                modal: {
                    department: false,
                },
                disabled: {
                    department: false,
                }
            },

            // ================================

            deptFields: [
                { key: 'action', label: 'Aksi', thStyle: { width: '150px' },},
                { key: 'name', label: 'Departemen', sortable: true },
            ],

            params:
            JSON.parse(localStorage.getItem('masterdepartmentfiltersession')) ? JSON.parse(localStorage.getItem('masterdepartmentfiltersession')) : 
            {
                limit: 20,
                offset: 0,
                name:'',
            },
            currentPage: 1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            totalRows: 0,
            pageOptions: [20, 50, 100],

            dept_list:[],
            form_department: {
                id:'',
                name:''
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

                this.onDepartmentList()
            }
        },
        perPage: {
            handler: function (value) {
                this.params.limit = this.perPage
                this.onDepartmentList()
            }
        },
    },

    computed: {
        ...mapState({}),
    },

    methods: {
        filterDepartment() {
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }

            this.searchTimeout = setTimeout(() => {
                this.params.name = ""

                const input = this.searchQuery.trim();
                // console.log(input, 'thisearchquery')

                this.params.name = input

                this.$store.dispatch("department/getDeptList", { params: this.params})
                .then((res) => {
                    this.dept_list = res.rows
                    this.totalRows = res.iTotal
                    // console.log('this.dept_list', res);  
                    // console.log('this.dept_list', this.dept_list);                    

                })
                .catch((err) => {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    console.error('Error catch at: ', err)      
                })
            }, 300)
        },
        async openDepartmentModal() {
            await this.resetDepartment()
            this.active.modal.department = true
        },

        async resetDepartment() {
            this.form_department.id = ''
            this.form_department.name = ''
        },

        validationDepartment() {
            try {
                this.active.disabled.department = true
                this.$store.dispatch('department/postPutDepartment', this.form_department)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal.department = false
                        this.onDepartmentList()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabled.department = false
            }   
        },

        async onEditDepartment(obj) {
            await this.openDepartmentModal()

            this.form_department.id = obj?.id
            this.form_department.name = obj?.name
        },

        removeDepartment(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda yakin ingin menghapus Departemen ${obj.name} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-600', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('department/remove', obj.id)
                    .then(
                        res => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.onDepartmentList()
                        }
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    })
                }
            })
        },
        
        onDepartmentList() {
            this.$store.dispatch("department/getDeptList", { params: this.params})
            .then((res) => {
                this.dept_list = res.rows
                this.totalRows = res.iTotal
                console.log('this.dept_list', res);  
                console.log('this.dept_list', this.dept_list);                    

            })
            .catch((err) => {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', err)      
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
        this.onDepartmentList()
    }
}
</script>