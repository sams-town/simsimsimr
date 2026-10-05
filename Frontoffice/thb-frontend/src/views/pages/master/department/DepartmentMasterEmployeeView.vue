<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Master Department', link:'/master/department' },
                { text: position_data.department_name, 
                    link:'/master/department/position/' + position_data.department_id 
                },
                { text: position_data.name},
            ]"
        />
        <section class="content pt-12">
            <div class="d-flex justify-content-between">
                <div class="row w-100 align-items-end">
                    <div class="col-lg-3">
                        <label class="fs-13 mb-0">Fasilitas Kesehatan</label>
                        <v-select class="select-size-sm" @input="filterEmployee" v-model="params.health_facility_id" :options="OptionsHealthFacility" :reduce="OptionsHealthFacility => OptionsHealthFacility.value" label="title" placeholder="-- Fasilitas Kesehatan --"></v-select>
                    </div>
                    <!-- <div class="col-lg-3">
                        <label class="fs-13 mb-0">Jabatan</label>
                        <v-select class="select-size-sm" @input="filterEmployee" v-model="params.position_id" :options="OptPositionList" :reduce="OptPositionList => OptPositionList && OptPositionList.value" label="title" placeholder="-- Jabatan --"></v-select>
                    </div> -->
                    <div class="col-lg-3"> 
                        <label class="fs-13 mb-0">Nama Pegawai</label>
                        <b-form-input class="select-size-sm" @input="filterEmployee" v-model="searchQuery"></b-form-input>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between mt-12">
                <div>
                    <b-button size="sm" variant="primary" @click="openEmployeeModal">
                        <feather-icon icon="PlusCircleIcon" class="mr-1" />
                        <span class="fs-12">Tambah Kepegawaian</span>
                    </b-button>
                </div>
                <div class="d-flex align-items-center">
                    <span class="fs-12 mr-1">Tampilkan</span>
                    <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions" />
                </div>
            </div>

            <section class="mt-12">
                <b-table empty-text="Tidak ada Data" show-empty bordered fixed small head-variant="light" :items="items" :fields="fields" :per-page="0" :current-page="currentPage" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                    <template v-slot:empty="scope">
                        <h6>{{ scope.emptyText }}</h6>
                    </template>
                    <template #cell(is_delete)="data">
                        <b-badge :variant="status[1][data.value]">
                            {{ status[0][data.value] }}
                        </b-badge>
                    </template>
                    <template #cell(action)="row">
                        <b-button @click="onEditEmployee(row.item)" variant="warning-500" size="sm" class="mr-1">
                            <feather-icon icon="EditIcon" />
                        </b-button>
                        <b-button @click="removeEmployee(row.item)" variant="danger-500" size="sm" class="mr-1">
                            <feather-icon icon="TrashIcon" />
                        </b-button>
                        <b-button @click="onEditUser(row.item)" variant="primary" size="sm" class="mr-1">
                            <feather-icon icon="FilePlusIcon" />
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
            

            <b-modal v-model="active.modal.employee" size="md" >
                <template #modal-header="{ close }">
                    <div class="d-flex justify-content-between w-100">
                        <div class="text-uppercase font-weight-bold fs-13 pr-16">
                            ANDA SEDANG MENGELOLA KEPEGAWAIAN 
                            <!-- <span class="text-primary">{{ temp_c_room_name }}</span> -->
                        </div>
                        <feather-icon icon="XIcon" size="18" @click="close()" class="pointer"/>
                    </div>
                </template>

                <div class="row">
                    <div class="col-lg-3">
                        <div class="">
                            <label class="c-form-label">Nama Depan (title)</label>
                            <b-form-input v-model="form.f_title" autocomplete="off" placeholder="misal: Ir / Drs"/>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mt-12 mt-lg-0">
                            <label class="c-form-label">Nama</label>
                            <b-form-input v-model="form.name" autocomplete="off"/>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mt-12 mt-lg-0">
                            <label class="c-form-label">Nama Belakang (title)</label>
                            <b-form-input v-model="form.b_title" autocomplete="off" placeholder="misal: S.kom"/>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mt-12 mt-lg-0">
                            <label class="c-form-label">Nama Lengkap</label>
                            <b-form-input v-model="form.fullname" disabled :value="this.fullname_group" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    
                    <div class="col">
                        <div class="mt-12">
                            <label class="c-form-label">Alamat</label>
                            <b-form-input v-model="form.address" autocomplete="off"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3">
                        <div class="mt-12">
                            <label class="c-form-label">Jenis Kelamin</label>
                            <v-select v-model="form.sex" aria-readonly="true" :options="OptionsSex" :reduce="OptionsSex => OptionsSex.value" label="title" placeholder="-- Jenis Kelamin --"></v-select>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mt-12">
                            <label class="c-form-label">Departemen</label>
                            <b-form-input v-model="position_data.department_name" autocomplete="off" disabled/>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="mt-12">
                            <label class="c-form-label">Jabatan</label>
                            <b-form-input v-model="position_data.name" autocomplete="off" disabled/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mt-12">
                            <label class="c-form-label">Tanggal Lahir</label>
                            <b-form-input v-model="form.birth_date" type="date" autocomplete="off"/>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mt-12">
                            <label class="c-form-label">Masa Berakhir Jabatan</label>
                            <b-form-input v-model="form.date_expired" type="date" autocomplete="off"/>
                        </div>
                    </div>
                </div>

                <template #modal-footer>
                    <div class="w-100 d-flex justify-content-end m-0">
                        <b-button @click="validationEmployee" :disabled="active.disabled.employee" variant="primary" size="sm" class="px-32">SIMPAN</b-button>
                    </div>
                </template>
            </b-modal>

            <b-modal v-model="active.modal.user" size="sm" >
                <template #modal-header="{ close }">
                    <div class="d-flex justify-content-between w-100">
                        <div class="text-uppercase font-weight-bold fs-13 pr-16">
                            ANDA SEDANG MENGELOLA USER
                            <!-- <span class="text-primary">{{ temp_c_room_name }}</span> -->
                        </div>
                        <feather-icon icon="XIcon" size="18" @click="close()" class="pointer"/>
                    </div>
                </template>

                <div class="row">
                    <div class="col-lg-12">
                        <div>
                            <label class="c-form-label">Pegawai</label>
                            <v-select v-model="form_user.employee_id" :options="OptionsEmployee" :reduce="OptionsEmployee => OptionsEmployee.value" label="title" placeholder="-- Pegawai --" disabled></v-select>
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">Auth Group</label>
                            <v-select v-model="form_user.group_id" :options="OptionsGroup" :reduce="OptionsGroup => OptionsGroup.value" label="title" placeholder="-- Auth Group --"></v-select>
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">Nama</label>
                            <b-form-input v-model="form_user.username" autocomplete="off" />
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">Password</label>
                            <b-form-group label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0 mr-1">
                                <b-form-input v-model="form_user.password" autocomplete="off" />
                                <b-button @click="randPass" variant="outline-secondary"> Generate Password </b-button>
                            </b-form-group>
                        </div>
                    </div>
                </div>    

                <template #modal-footer>
                    <div class="w-100 d-flex justify-content-end m-0">
                        <b-button @click="resetUserForm" type="reset" variant="outline-secondary" class="mr-2"> Reset </b-button>
                        <b-button @click="validationUser" :disabled="active.disabled.user" variant="primary" size="sm" class="px-32">SIMPAN</b-button>
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
            searchQuery: JSON.parse(localStorage.getItem('masterdepemployeefiltersession')) ? JSON.parse(localStorage.getItem('masterdepemployeefiltersession')).fullname : "", 
            hfi_inpatient: '40f06534-07ec-4221-ad9d-dea08196079f',
            position_id: null,
            dept_id: null,
            dept_name: null,
            position_data:[],
            user_datum:[],
            active: {
                modal: {
                    employee: false,
                    user:false,
                },
                disabled: {
                    employee: false,
                    user: false,
                }
            },

            // ================================

            params: {
                limit: 20,
                offset: 0,
                position_id: null,
                fullname: '',
                health_facility_id: '', 
            },
            currentPage: 1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,

            employee_list: [],
            form: {
                id:null,
                f_title:'',
                name: '',
                b_title:'',
                fullname:'',
                sex:'',
                address:'',
                position_id:'',
                date_expired:'',
                birth_date:''
            },

            form_user: {
                id: '',
                employee_id: '',
                group_id: '',
                username: '',
                password: ''
            },

            requestCancelToken: null,
            searchTimeout: null,

            // ================================
            status: [{
                0: 'Active', 1: 'Deleted'
            },
            {
                0: 'primary', 1: 'danger'
            }],
            fields: [
                { key: 'fullname', label: 'Nama Lengkap', sortable: true },
                { key: 'health_facility_name', label: 'Fasilitas Kesehatan', sortable: true },
                { key: 'position_name', label: 'Jabatan', sortable: true },
                { key: 'sex_name', label: 'Kelamin', sortable: true },
                { key: 'birth_date', label: 'Tanggal Lahir', sortable: true },
                { key: 'address', label: 'Alamat', sortable: true },
                { key: 'is_delete', label: 'Status', sortable: true },
                { key: 'action', label: 'Aksi'},
            ],
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

                this.filterEmployee()
            }
        },
        perPage: {
            handler: function (value) {
                this.params.limit = this.perPage
                this.filterEmployee()
            }
        },
    },

    computed: {
        ...mapState({ 
            totalRows: state => state.employee.totalRows, 
            items: state => state.employee.items,

            pageOptions: state => state.employee.pageOptions, 
            OptionsSex: state => state.helper.options.sex,

            //user
            OptionsEmployee: state => state.employee.options.employee,
            OptionsGroup: state => state.group.options.group,

            // for filters
            OptionsPosition: state => state.position.options.position,
            OptionsHealthFacility: state => state.healthfacility.options.healthfacility,
        }),
        sortOptions() {
            // Create an options list from our fields
            return this.fields.filter(f => f.sortable).map(f => ({ text: f.label, value: f.key }))
        },
        dateFormat () {
            return create_at => this.$options.filters.formatDate(create_at)
        },
        fullname_group () {
            let groups = []
            if (this.form.f_title) {
                groups.push(this.form.f_title + '.')
            }

            if (this.form.name && this.form.b_title) {
                groups.push(this.form.name + ',')
            } else if (this.form.name) {
                groups.push(this.form.name)
            }

            if (this.form.b_title) {
                groups.push(this.form.b_title)
            }

            let final_groups = groups.join(' ')
            return this.form.fullname = final_groups
        }

    },

    methods: {
        filterEmployee() {
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }

            this.searchTimeout = setTimeout(() => {
                this.params.fullname = ""

                const input = this.searchQuery.trim();

                this.params.fullname = input

                // if (this.requestCancelToken) {
                //     // console.log("Membatalkan request sebelumnya...")
                //     this.requestCancelToken.cancel("Request sebelumnya dibatalkan.")
                // }

                // this.requestCancelToken = axios.CancelToken.source()
                // console.log("Mengirim request baru...", this.params)
                // console.log('this.requestCancelToken: ', this.requestCancelToken)

                
                // console.log(this.params);
                this.$store.dispatch('employee/getAll', this.params)
                .then((res) => {
                    localStorage.setItem("masterdepemployeefiltersession", JSON.stringify(this.params))
                })
                .catch((err) => {
                    this.notification('Failed','CloseIcon','danger','Cannot get Employee/getAll')
                    console.error('Error catch at: ', err)    
                })
            }, 300)
        },
        async openEmployeeModal() {
            await this.resetEmployee()
            this.active.modal.employee = true
        },

        async openUserModal() {
            this.active.modal.user = true
        },

        async resetEmployee() {
            this.form.id = ''
            this.form.f_title = ''
            this.form.name = ''
            this.form.b_title = ''
            this.form.fullname = ''
            this.form.sex = ''
            this.form.address = ''
            // this.form.position_id = ''
            this.form.date_expired = ''
            this.form.birth_date = ''
        },

        validationEmployee() {
            try {
                this.active.disabled.employee = true
                this.$store.dispatch('employee/formPostPut', this.form)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal.employee = false
                        this.filterEmployee()
                        this.$store.dispatch('employee/getParamAll') // 1 select filter
                        this.$store.dispatch('group/getParam') // 1 select filter
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabled.employee = false
            }   
        },

        async onEditEmployee(obj) {
            await this.openEmployeeModal()
            
            this.form.id = obj.id
            this.form.f_title = obj.f_title
            this.form.name = obj.name
            this.form.b_title = obj.b_title
            this.form.fullname = obj.fullname
            this.form.sex = obj.sex
            this.form.address = obj.address
            this.form.position_id = obj.position_id
            this.form.date_expired = obj.position_date_expired
            this.form.birth_date = obj.birth_date
        },

        validationUser(){
            try {
                this.active.disabled.user = true
                const formData = this.form_user
                console.log(formData, 'FormData')
                this.$store.dispatch('user/formPostPut', formData)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal.user = false
                        this.resetUserForm()
                        this.filterEmployee()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabled.user = false
            }   
        },

        async onEditUser(obj) {
            await this.resetUserForm()
            if(obj.user_id){
                await this.$store.dispatch("user/getById",obj.user_id)
                .then((res) => {
                    console.log(res,'user/getbyID')
                    if(Array.isArray(res) && res.length > 0){
                        this.user_datum = res[0]
                        this.form_user.id = this.user_datum.id
                        this.form_user.employee_id = this.user_datum.employee_id
                        this.form_user.group_id = this.user_datum.group_id
                        this.form_user.username = this.user_datum.username
                    }
                
                })
                .catch((err) => {
                    this.notification('Failed','CloseIcon','danger','Cannot get user/getById')
                    console.error('Error catch at: ', err)      
                })
            }else {
                this.form_user.employee_id = obj.id
            }
            this.openUserModal()
        },

        removeEmployee(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda yakin ingin menghapus kepegawaian ${obj.name} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-600', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('employee/remove', obj.id)
                    .then(
                        res => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.filterEmployee()
                        }
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    })
                }
            })
        },
                
        onPositionById() {
            this.$store.dispatch("position/getPositionById", this.position_id)
            .then((res) => {
                if(res.length > 0 && Array.isArray(res)){
                    // console.log('this.position_list', res[0]);  
                    this.position_data = res[0]
                    
                }
            })
            .catch((err) => {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', err)      
            })
        },

        async resetUserForm() {
            this.form_user.password = ''
            this.form_user.group_id = ''
            this.form_user.username = ''
        },
        
        // ===========================================        

        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },

        randPass () {
            let pass = '';
            let str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' +
                'abcdefghijklmnopqrstuvwxyz0123456789@#$';

            for (let i = 1; i <= 8; i++) {
                let char = Math.floor(Math.random()
                    * str.length + 1);

                pass += str.charAt(char)
            }
            this.form_user.password = pass
        },
    },

    async created () {
        this.position_id = this.$route.params.idposition
        this.params.position_id = this.position_id
        this.form.position_id = this.position_id
        this.dept_id = this.$route.params.iddepartment
        this.filterEmployee()
        this.onPositionById()
        
        // get option jabatan
        // const resOptionJabatan = await this.$store.dispatch('position/getOptPosition')
        // this.OptPositionList = resOptionJabatan

        // option buat create user
        this.$store.dispatch('employee/getParamAll') // 1 select filter
        this.$store.dispatch('group/getParam') // 1 select filter
    }
}
</script>