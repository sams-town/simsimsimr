<template>
    <div>
        <patientMenu selectedMenu="menu24" />
        <div class="label-head my-16 my-lg-24">
            <span class="px-16 text-bold-24 text-neutral-700">Hemodialisa</span>
        </div>
        <!-- ////////  -->
        <div class="card-body shadow-sm rounded">
            <div class="row mx-0">
                <div class="col-12 col-lg">
                    <div class="row">
                        <div class="col-12 col-lg-3">
                            <img src="@/@core/assets/images/pendaftaran/user.png" alt="your-photo"
                                class="rounded-circle shadow-sm c-img">
                        </div>
                        <div class="col-12 col-lg">
                            <span class="text-bold-18 text-indigo">
                                <img src="@/@core/assets/icons/nurse/user.svg" alt="user-icon">{{regData.norm}}
                            </span>
                            <p class="mb-0 mt-12 text-semi-24 text-neutral-800">
                                <span>({{regData.alias_name}}) </span>
                                <span>{{regData.name}}</span>
                            </p>
                            <div class="row mx-0 mt-16">
                                <b-badge class="rounded text-neutral-500 text-uppercase border border-success-200 px-10 py-1" :variant="regData.is_new == 'N' ? 'success-200' : 'info-200'">{{ regData.is_new == 'N' ? 'Pasien Lama' : 'pasien Baru' }}</b-badge>
                                <b-badge
                                    class="rounded text-neutral-500 text-uppercase border border-success-500 px-10 py-1 mx-lg-12 "
                                    variant="success-200">{{regData.health_facility_name}}</b-badge>
                                <b-badge
                                    class="rounded text-neutral-500 text-uppercase border border-warning-500 px-10 py-1 "
                                    variant="warning-200">{{regData.health_facility_item_name}}</b-badge>
                                <div v-if="regData.registration_insurance">
                                    <div v-for="list in regData.registration_insurance">
                                        <b-badge v-if="list.is_cob == 1"class="rounded text-neutral-500 text-uppercase border border-warning-500 px-10 py-1" variant="warning-200">COB</b-badge>
                                    </div>
                                </div>
                            </div>
                            <p class="text-neutral-800 mt-8 mt-lg-16">
                                <span class="text-regular-14 mr-2">Status:</span>
                                <span class="text-bold-14">Dalam Penanganan Medis</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 mt-16 mt-lg-0">
                    <div class="d-flex flex-column align-items-lg-end h-100">
                        <div class="row mx-0">
                            <b-button pill variant="outline-indigo" size="sm" class="mr-2">
                                <b-icon icon="arrow-left" /> <span>Kembali</span>
                            </b-button>
                            <administration />
                        </div>
                        <div class="mt-2 mt-lg-auto">
                            <b-button pill variant="outline-indigo" @click="refresh()" size="sm">
                                <b-icon icon="arrow-clockwise" /> <span>Refresh</span>
                            </b-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section class="mt-32">
            <div class="d-flex justify-content-between align-items-center">
                <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0">
                    <span class="text-regular-14">Tampilkan</span>
                    <b-form-select v-model="perPage" size="sm" :options="pageOptions" class="mx-1"/>
                    <span class="text-regular-14">Entri</span>
                </b-form-group>
                <b-button @click="openMainModal" pill variant="primary" class="px-3">
                    <feather-icon icon="PlusIcon" class="mr-1" /> Tambah
                </b-button>
            </div>
            <b-table bordered fixed head-variant="light" :items="hemodialysis_list" :fields="hemodialysis_fields" class="mt-12" style="font-size: 13px;">
                <template #cell(prescription_hd)="row">
                    <div v-html="parsedBody(row.value ? row.value : '-')"></div>
                </template>
                <template #cell(vascular_access)="row">
                    <div v-html="parsedBody(row.value ? row.value : '-')"></div>
                </template>
                <template #cell(action)="row">
                    <div class="d-flex">
                        <b-button @click="onEditMedical(row.item)" size="sm" variant="warning-500" class="mr-1">
                            <feather-icon icon="EditIcon" />
                        </b-button>
                        <b-button @click="removeMedical(row.item)" size="sm" variant="danger-500">
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

        <b-modal v-model="active.modal_main" size="lg" title="Tambah/update hemodialisa pasien" no-close-on-backdrop>
            <div class="col">
                <label class="c-form-label">Diagnosis Utama</label>
                <v-select v-model="form_hemodialysis.main_diagnosis" :options="OptionsDiagnose" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
            </div>
            <div class="col mt-10">
                <label class="c-form-label">Etiologi</label>
                <v-select v-model="form_hemodialysis.etiology" :options="OptionsEtiology" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
            </div>
            <div class="col mt-10">
                <label class="c-form-label">Penyakit Penyerta</label>
                <v-select v-model="form_hemodialysis.comorbidities" :options="OptionsComorbidities" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
            </div>
            <div class="col mt-10">
                <label class="c-form-label">Resep HD</label>
                <b-textarea v-model="form_hemodialysis.prescription_hd" />
            </div>
            <div class="col mt-10">
                <label class="c-form-label">Frekuensi HD</label>
                <b-input-group append="/minggu">
                    <b-form-input v-model="form_hemodialysis.frequency_hd" type="number" />
                </b-input-group>
            </div>
            <div class="col mt-10">
                <label class="c-form-label">Akses Vaskular</label>
                <b-textarea v-model="form_hemodialysis.vascular_access" />
            </div>

            <template #modal-footer>
                <div class="d-flex justify-content-end">
                    <b-button @click="validationHemodialysis" :disabled="active.disabled_main" variant="primary">Simpan</b-button>
                </div>
            </template>
        </b-modal>
    </div>
</template>
<script>
import patientMenu from '@/@core/components/tab-menu/patientMenu.vue';
import administration from '@/views/layouts/patient/doctor/administration.vue';
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import vSelect from 'vue-select'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import moment from 'moment'

export default {
    name: 'PatientHemodialysis',
    components: {
        patientMenu, administration, ModernLabel, vSelect
    },
    data() {
        return {
            reg_des_id: '',
            regData: {},
            active: {
                modal_main: false,
                disabled_main: false,
            },

            pageOptions: [20, 50, 100],
            currentPage:1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            isDisabledFilter: false,
            totalRows: 0,
            params: {
                offset: 0,
                limit: 20,
                registration_destination_id: ''
            },

            OptionsDiagnose: [],
            OptionsEtiology: [],
            OptionsComorbidities: [],

            form_hemodialysis: {
                id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                main_diagnosis: '',
                etiology: '',
                comorbidities: '',
                prescription_hd: '',
                frequency_hd: '',
                vascular_access: ''
            },

            hemodialysis_fields: [
                { key: 'date_updated', label: 'Tanggal Update', thStyle: 'width: 150px' },
                { key: 'main_diagnosis', label: 'Diagnosis Utama' },
                { key: 'etiology', label: 'Etiologi' },
                { key: 'comorbidities', label: 'Penyakit Penyerta' },
                { key: 'prescription_hd', label: 'Resep HD' },
                { key: 'frequency_hd', label: 'Frekuensi HD' },
                { key: 'vascular_access', label: 'Akses Vaskular' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 100px' },
            ],

            hemodialysis_list: [],
        }
    },
    watch: {
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage*(value-1)+1
                this.lastRows = this.perPage*(value)+1
                this.params.offset = this.perPage*(value-1)
                this.params.limit = this.perPage
                this.getHemodialysis()
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.getHemodialysis()
            }
        },
    },
    methods: {
        parsedBody(text){
            return String(text || '').replace(/\n/g, "<br>");
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },

        getData(regDesId) {
			this.$store.dispatch('registration/getById', regDesId).then(data => {
                this.regData = data[0]
            })
        },

        openMainModal() {
            this.active.modal_main = true
            this.clearFormHemodialysis()
        },

        clearFormHemodialysis() {
            this.form_hemodialysis.id = ''
            this.form_hemodialysis.registration_destination_id = ''
            this.form_hemodialysis.doctor_in_charge_id = ''
            this.form_hemodialysis.main_diagnosis = ''
            this.form_hemodialysis.etiology = ''
            this.form_hemodialysis.comorbidities = ''
            this.form_hemodialysis.prescription_hd = ''
            this.form_hemodialysis.frequency_hd = ''
            this.form_hemodialysis.vascular_access = ''
        },

        validationHemodialysis() {
            if (this.form_hemodialysis.id == '') {
                this.form_hemodialysis.registration_destination_id = this.reg_des_id
                this.form_hemodialysis.doctor_in_charge_id = this.regData.doctor_in_charge_id
            }

            try {
                this.active.disabled_main = true
                this.$store.dispatch('hemodialysis/postPutHemodialysis', this.form_hemodialysis)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal_main = false
                        this.active.disabled_main = false
                        this.clearFormHemodialysis()
                        this.getHemodialysis()
                    } 
                )
                .catch(({error, response}) => {
                    this.notification('Failed','CloseIcon','danger',`${error}`)
                    this.active.disabled_main = false
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
                this.active.disabled_main = false
            }
        },

        getHemodialysis() {
            this.params.registration_destination_id = this.reg_des_id
            try {                
                this.$store.dispatch('hemodialysis/getListHemodialysis', this.params).then((response) => {
                    this.hemodialysis_list = response.rows
                    this.totalRows = response.iTotal
                }, error => {
                    console.error('Error while GET hemodialysis/getListHemodialysis at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET hemodialysis/getListHemodialysis at: ', error);
            }
        },

        onEditMedical(obj) {
            this.clearFormHemodialysis()
            this.active.modal_main = true
            
            this.form_hemodialysis.id = obj.id
            this.form_hemodialysis.registration_destination_id = obj.registration_destination_id
            this.form_hemodialysis.doctor_in_charge_id = obj.doctor_in_charge_id
            this.form_hemodialysis.main_diagnosis = obj.main_diagnosis
            this.form_hemodialysis.etiology = obj.etiology
            this.form_hemodialysis.comorbidities = obj.comorbidities
            this.form_hemodialysis.prescription_hd = obj.prescription_hd
            this.form_hemodialysis.frequency_hd = obj.frequency_hd
            this.form_hemodialysis.vascular_access = obj.vascular_access
        },

        removeMedical(obj) {            
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus hemodialisa tanggal ${obj.date_updated} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('hemodialysis/deleteHemodialysis', obj.id)
                    .then(
                        response => {
                            if (response.status === 200 || response.status === 201) {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getHemodialysis()
                            } else {
                                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            }
                        },
                        error => {
                            this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            console.error('Error While Delete Using hemodialysis/deleteHemodialysis at: ', error)
                        }
                    )
                    .catch(({error, response}) => {
                        this.notification('Failed','CloseIcon','danger',`${error}`)
                    })
                }
            })
        },
        getMasterHemodialysis() {
            try {                
                this.$store.dispatch('hemodialysis/getListMaster', {})
                .then(
                    (response) => {
                        let data = response.rows
                        if(data && data.length > 0) {
                            for (let i = 0; i < data.length; i++) {
                                const el = data[i];
                                if (el.code == 'N') {
                                    this.OptionsDiagnose.push({ value: el.name, title: el.name })
                                } else if(el.code == 'E') {
                                    this.OptionsEtiology.push({ value: el.name, title: el.name })
                                } else if(el.code == 'P') {
                                    this.OptionsComorbidities.push({ value: el.name, title: el.name })                                    
                                }
                            }
                        }

                    }, error => {
                        console.error('Error while GET hemodialysis/getListMaster at: ', error);
                    }
                )
            } catch (error) {
                console.error('Error catch while GET hemodialysis/getListMaster at: ', error);
            }
        },
    },
    
    created () {
        this.getMasterHemodialysis()

        if (this.$route.params.id) {
            this.reg_des_id = this.$route.params.id
            this.getData(this.$route.params.id)
            this.getHemodialysis()
        }
    }
}
</script>
<style scoped>
.label-head::before {
    content: '';
    border-left: 6px solid #648DE1;
}
.c-img {
    aspect-ratio: 1/1;
    object-fit: cover;
    max-width: 150px;
    max-height: 150px;
    border: 10px solid white;
}

</style>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>