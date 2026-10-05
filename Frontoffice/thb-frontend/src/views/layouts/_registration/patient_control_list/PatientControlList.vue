<template>
    <div>
        <section class="col mt-32">
            <div class="card">
                <div class="card-header bg-primary fw-bold text-center text-white">Pencarian Rencana Kontrol Pasien</div>
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="d-flex align-items-center">
                                        <div class="col-lg">
                                            <label class="c-form-label">Tanggal Mulai</label>
                                            <b-form-input type="date" v-model="params.date_start"></b-form-input>
                                        </div>
                                        <span>-</span>
                                        <div class="col-lg">
                                            <label class="c-form-label">Tanggal Akhir</label>
                                            <b-form-input type="date" v-model="params.date_end"></b-form-input>
                                        </div>
                                        <div class="col-lg">
                                            <label class="c-form-label">Tipe Data *</label>
                                            <v-select v-model="params.type" aria-readonly="true" :options="OptionsType" :reduce="OptionsType => OptionsType.value" label="title" placeholder="-- Kontrol Dokter / Pununjang --"></v-select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        <b-button @click="validationSearch" :disabled="active.disabled_filter" variant="primary-500" class="px-16">
                            <feather-icon icon="DownloadIcon" /> Export Data
                        </b-button>
                    </div>
                </div>
            </div>
        </section>

    </div>
</template>

<script>
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import topMenu from '@/@core/components/top-menu/topMenu.vue'
import { mapState } from 'vuex'
import moment from 'moment'
import vSelect from 'vue-select'

export default {
    name: 'app',
    components: {
        topMenu, vSelect
    },
    data() {
        return {
            urlRoot: process.env.VUE_APP_ROOT_API,
            OptionsType:[
                { value: 1, title: 'Poliklinik' },
                { value: 3, title: 'Pasien Umum' },
                { value: 2, title: 'Penunjang' }
            ],
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
                date_start: moment(new Date()).format('YYYY-MM-DD'),
                date_end: moment(new Date()).format('YYYY-MM-DD'),
                type: '',
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
        },
        validationSearch() {
            try {
                if (this.params.date_start && this.params.date_end) {
                    window.open(this.urlRoot+`mr/report/control/export?date_start=${ this.params.date_start }&date_end=${ this.params.date_end }&type=${ this.params.type }`)
                } else {
                    this.notification('Warning','CloseIcon','warning','Tanggal mulai dan tanggal akhir wajib diisi')
                }
            } catch (error) {
                console.error('Error while GET patient/getAll at: ', error)                
            }
        },
        
        getThisPatient(obj) {
            this.form_norm.old_norm = obj.norm ? obj.norm : ''
            this.active.main_modal = false
        },

        resetForm() {
            this.form_norm.new_norm = ''
            this.form_norm.old_norm = ''
        }
    },
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>