<template>
    <div>
        <topMenu selectedMenu="menu12" />
        <section class="col mt-32">
            <div class="card">
                <div class="card-header bg-primary fw-bold text-center text-white">Merge Rekam Medis</div>
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-6">
                                    <label class="c-form-label">No. RM Lama</label>
                                    <b-input-group class="mb-3">
                                        <b-form-input v-model="form_norm.old_norm" disabled />
                                        <b-input-group-append>
                                            <b-button @click="openModal" size="sm" variant="w3green">Cari Pasien</b-button>
                                        </b-input-group-append>
                                    </b-input-group>
                                </div>
                                <div class="col-lg-6 mt-10 mt-lg-0">
                                    <label class="c-form-label">No. RM Baru</label>
                                    <b-form-input v-model="form_norm.new_norm" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        <b-button @click="validationNorm" :disabled="active.disabled_norm" variant="primary-500" class="px-16">
                            <feather-icon icon="SaveIcon" /> Simpan
                        </b-button>
                    </div>
                </div>
            </div>
        </section>

        <b-modal v-model="active.main_modal" size="lg" title="Cari Pasien" hide-footer>
            <div class="card">
                <div class="card-header bg-primary text-white">Pencarian Pasien</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <label class="c-form-label">Nama Pasien</label>
                            <b-form-input v-model="params.name" autocomplete="off" />
                        </div>
                        <div class="col-lg-4 mt-10 mt-lg-0">
                            <label class="c-form-label">Tanggal Lahir</label>
                            <b-form-input v-model="params.birth_date" autocomplete="off" type="date" class="col" />
                        </div>
                        <div class="col-lg-4 mt-10 mt-lg-0">
                            <label class="c-form-label">No RM</label>
                            <b-form-input v-model="params.norm" autocomplete="off" />
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        <b-button @click="getRegList" :disabled="active.disabled_filter" variant="primary"> 
                            <feather-icon icon="SearchIcon"/> Filter
                        </b-button>
                    </div>
                </div>
            </div>
            <div class="card mt-16">
                <div class="card-body">
                    <div class="row mx-0">
                        <b-form-group label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0 mr-1">
                            <span class="text-regular-14">Tampilkan</span>
                            <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions"/>
                            <span class="text-regular-14">Entri</span>
                        </b-form-group>
                    </div>
                    <div class="col px-0 mt-3">
                        <b-table small hover responsive head-variant="light" :items="items" :fields="fields" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                            <template #cell(code)="row">
                                <span class="font-weight-bold">{{ row.item.health_facility_id != "40f06534-07ec-4221-ad9d-dea08196079f" ? row.item.register_date : row.item.checkin_date }}</span>
                            </template>
                            <template #cell(action)="row">
                                <b-button size="sm" @click="getThisPatient(row.item)" variant="w3green">Ambil</b-button>
                            </template>
                            <template #cell(instalation)="row">
                                <div>{{ row.item.health_facility_item_name }}</div>
                                <div class="font-weight-bold">{{ row.item.doctor_in_charge_name }}</div>
                            </template>
                            <template #cell(insurance_name)="row">
                                <div>
                                    {{ row.item.how_to_pay }} <span v-if="row.item.insurance_name">| {{ row.item.insurance_name }}</span>
                                    <b-badge v-if="row.item.is_cob == 1"class="rounded text-neutral-500 text-uppercase border border-warning-500 px-10 py-1" variant="warning-200">COB</b-badge>
                                </div>
                            </template>
                        </b-table>
                    </div>
                    <div class="col px-0 mt-20">
                        <div class="d-flex">
                            <span class="text-regular-14">Menampilkan {{ firstRows }} hingga <template v-if="lastRows < totalRows">{{ lastRows }}</template> <template v-else>{{ totalRows }}</template> dari {{ totalRows }} entri</span>
                            <div class="ml-auto">
                                <b-pagination v-model="currentPage" :total-rows="totalRows"
                                    :per-page="perPage" :current-page="currentPage"
                                    align="fill" size="sm" class="my-0"></b-pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </b-modal>

    </div>
</template>

<script>
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import topMenu from '@/@core/components/top-menu/topMenu.vue'
import { mapState } from 'vuex'

export default {
    name: 'app',
    components: {
        topMenu
    },
    data() {
        return {
            fields: [
                { key: 'idcard', label: 'ID/KTP/SIM/Passport' },
                { key: 'name', label: 'Nama Pasien' },
                { key: 'norm', label: 'No RM', sortable: true },
                { key: 'action', label: 'Aksi' },
                { key: 'sex_name', label: 'Jenis Kelamin' },
                { key: 'birth_date', label: 'Tanggal Lahir' },
                { key: 'address', label: 'Alamat' },
            ],
            pageOptions: [10,20, 50, 100],
            currentPage: 1,
            perPage: 10,
            firstRows: 1,
            lastRows: 10,

            params: {
                offset: 0,
                limit: 10,
                name: '',
                birth_date: '',
                norm: '',
            },
            active: {
                disabled_norm: false,
                disabled_filter: false,
                main_modal: false,
            },
            form_norm: {
                new_norm: '',
                old_norm: '',
            }
        }
    },
    computed: {
        ...mapState({ 
            items: state => state.patient.items,
            totalRows: state => state.patient.totalRows, 
        }),
    },
    watch: {
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage*(value-1)+1
                this.lastRows = this.perPage*(value)
                this.params.offset = this.perPage*(value-1)
                this.params.limit = this.perPage
                this.getRegList()
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.getRegList()
            }
        },
    },
    methods: {
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        openModal() {
            this.active.main_modal = true
            this.getRegList()
        },
        getRegList() {
            this.active.disabled_filter = true

            try {
                this.$store.dispatch('patient/getAll', this.params)
                .then(res => {
                    this.active.disabled_filter = false
                },
                err => {
                    console.error('Error here at: ', err)                
                    this.active.disabled_filter = false
                })
            } catch (error) {
                console.error('Error while GET patient/getAll at: ', error)                
            }
        },
        
        getThisPatient(obj) {
            this.form_norm.old_norm = obj.norm ? obj.norm : ''
            this.active.main_modal = false
        },

        validationNorm() {
            let data = {
                norm: { old_norm: this.form_norm.old_norm},
                new_norm: this.form_norm.new_norm
            }

            try {
                this.active.disabled_norm = true
                this.$store.dispatch('patient/putMergeNorm', data ).then((response) => {
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.active.disabled_norm = false

                    data.norm.old_norm = ''
                    data.new_norm = ''
                    this.resetForm()
                    
                }, err => {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    this.active.disabled_norm = false
                    console.error('Error at: ', err)                
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                this.active.disabled_norm = false
                console.error('Error at: ', error)                
            }
        },
        resetForm() {
            this.form_norm.new_norm = ''
            this.form_norm.old_norm = ''
        }
    },
}
</script>