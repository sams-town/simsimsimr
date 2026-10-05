<template>
    <div>
        <patientMenu selectedMenu="menu13" />
        <div class="label-head my-16 my-lg-24">
            <span class="px-16 text-bold-24 text-neutral-700">Riwayat Diet Pasien</span>
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
                                <img src="@/@core/assets/icons/nurse/user.svg" alt="user-icon">{{this.regData.norm}}
                            </span>
                            <p class="mb-0 mt-12 text-semi-24 text-neutral-800">
                                <span>({{this.regData.alias_name}}) </span>
                                <span>{{this.regData.name}}</span>
                            </p>
                            <div class="row mx-0 mt-16">
                                <b-badge class="rounded text-neutral-500 text-uppercase border border-success-200 px-10 py-1" :variant="this.regData.is_new == 'N' ? 'success-200' : 'info-200'">{{ this.regData.is_new == 'N' ? 'Pasien Lama' : 'pasien Baru' }}</b-badge>
                                <b-badge
                                    class="rounded text-neutral-500 text-uppercase border border-success-500 px-10 py-1 mx-lg-12 "
                                    variant="success-200">{{this.regData.health_facility_name}}</b-badge>
                                <b-badge
                                    class="rounded text-neutral-500 text-uppercase border border-warning-500 px-10 py-1 "
                                    variant="warning-200">{{this.regData.health_facility_item_name}}</b-badge>
                                <div v-if="this.regData.registration_insurance">
                                    <div v-for="list in this.regData.registration_insurance">
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
        <section class="mt-24">
            <div class="d-flex justify-content-between align-items-center">
                <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0">
                    <span class="text-regular-14">Tampilkan</span>
                    <b-form-select v-model="perPage" size="sm" :options="pageOptions" class="mx-1"/>
                    <span class="text-regular-14">Entri</span>
                </b-form-group>
                <div>
                    <b-button @click="openModal" size="sm" pill variant="primary" class="d-block px-3">
                        <feather-icon icon="PlusIcon" class="mr-1" /> Tambah Order
                    </b-button>
                </div>
            </div>
            <b-table fixed bordered head-variant="light" :items="nutrition_list" :fields="nutrition_fields" class="mt-12" style="font-size: 13px;">
                <template #cell(time_type_id)="row">{{ getTimeName(row.value) }}</template>
                <template #cell(action)="row">
                    <div v-if="row.item.status == 0" class="d-flex">
                        <b-button @click="onEditNutrition(row.item)" size="sm" variant="warning-500" class="mr-1">
                            <feather-icon icon="EditIcon" />
                        </b-button>
                        <b-button @click="removeNutrition(row.item)" size="sm" variant="danger-500">
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

        <b-modal v-model="active.modal_main" title="Tambah Order" no-close-on-backdrop>
            <div class="row">
                <div class="col-lg-4">
                    <label class="c-form-label">Tanggal</label>
                    <b-form-input type="date" v-model="form_nutrition.date" />
                </div>
                <div class="col-lg-4">
                    <label class="c-form-label">Diet</label>
                    <v-select v-model="form_nutrition.diet_id" :options="optionsDiet" :reduce="item => item.value" label="title" placeholder="-- pilih --" />                            
                </div>
                <div class="col-lg-4">
                    <label class="c-form-label">Waktu</label>
                    <b-form-checkbox-group v-model="form_nutrition.time_arr" size="sm">
                        <b-form-checkbox value="1">Pagi</b-form-checkbox>
                        <b-form-checkbox value="2">Siang</b-form-checkbox>
                        <b-form-checkbox value="3">Sore</b-form-checkbox>
                    </b-form-checkbox-group>
                </div>
                <div class="col-12 mt-10">
                    <label class="c-form-label">Catatan</label>
                    <b-form-textarea rows="4" v-model="form_nutrition.notes" autocomplete="off" />
                </div>
            </div>

            <template #modal-footer>
                <div class="d-flex justify-content-end w-100">
                    <b-button @click="validationForm" :disabled="active.disabled_main" variant="primary-500">Simpan</b-button>
                </div>
            </template>
        </b-modal>
        <b-modal v-model="active.modal_edit" title="Tambah Order" no-close-on-backdrop>
            <div class="row">
                <div class="col-lg-4">
                    <label class="c-form-label">Tanggal</label>
                    <b-form-input type="date" v-model="form_nutrition.date" />
                </div>
                <div class="col-lg-4">
                    <label class="c-form-label">Waktu</label>
                    <v-select v-model="form_nutrition.time_type_id" :options="optionsTime" :reduce="item => item.value" label="title" placeholder="-- pilih --" />                            
                </div>
                <div class="col-lg-4">
                    <label class="c-form-label">Diet</label>
                    <v-select v-model="form_nutrition.diet_id" :options="optionsDiet" :reduce="item => item.value" label="title" placeholder="-- pilih --" />                            
                </div>
                <div class="col-12 mt-10">
                    <label class="c-form-label">Catatan</label>
                    <b-form-textarea rows="4" v-model="form_nutrition.notes" autocomplete="off" />
                </div>
            </div>

            <template #modal-footer>
                <div class="d-flex justify-content-end w-100">
                    <b-button @click="validationForm" :disabled="active.disabled_main" variant="primary-500">Simpan</b-button>
                </div>
            </template>
        </b-modal>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import patientMenu from '@/@core/components/tab-menu/patientMenu.vue';
import administration from '@/views/layouts/patient/doctor/administration.vue';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import moment from 'moment'
import vSelect from 'vue-select'

export default {
    name: 'DoctorStudies',
    components: {
        patientMenu, administration, vSelect
    },
    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),
            active: {
                modal_main: false,
                modal_edit: false,
                disabled_main: false,
            },
            reg_des_id: '',
            regData: {},
            nutrition_list: [],
            form_nutrition: {
                id: '',
                registration_destination_id: '',
                health_facility_item_id: '',
                employee_nurse_id: '',
                room_id: '',
                diet_id: '',
                date: moment(new Date()).format('YYYY-MM-DD'),
                time_arr: [],
                time_type_id: 0,
                notes: '',
                status: 0,
            },
            optionsTime: [
                { value : 1, title: 'Pagi' },
                { value : 2, title: 'Siang' },
                { value : 3, title: 'Sore' },
            ],

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

            nutrition_fields: [
                { key: 'date', label: 'Tanggal Order' },
                { key: 'time_type_id', label: 'Waktu' },
                { key: 'diet_name', label: 'Diet' },
                { key: 'notes', label: 'Catatan' },
                { key: 'action', label: 'Aksi', thStyle: {width: '150px'} },
            ],
        }
    },
    computed: {
        ...mapState({ 
            optionsDiet: state => state.nutrition.optionsDiet, 
        })
    },

    watch: {
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage*(value-1)+1
                this.lastRows = this.perPage*(value)+1
                this.params.offset = this.perPage*(value-1)
                this.params.limit = this.perPage
                this.getNutritionList()
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.getNutritionList()
            }
        },
    },

    methods: {
        refresh () {
            router.go(router.currentRoute)
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        getData (id) {
			this.$store.dispatch('registration/getById', id).then(data => {
                this.regData = data[0]

                this.getNutritionList()
            })
        },
        openModal() {
            this.active.modal_main = true
            this.resetForm()
        },
        validationForm() {
            if (this.form_nutrition.id === '') {
                this.form_nutrition.registration_destination_id = this.regData.registration_destination_id
                this.form_nutrition.health_facility_item_id = this.regData.health_facility_item_id
                this.form_nutrition.employee_nurse_id = this.userData.employee_id
                this.form_nutrition.room_id = this.regData.room_id
            }
            // console.log(this.form_nutrition);

            try {
                this.active.disabled_main = true
                this.$store.dispatch('nutrition/postPutNutrition', this.form_nutrition)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal_main = false
                        this.active.modal_edit = false
                        this.resetForm()
                        this.getNutritionList()
                    } 
                )
                .catch(({error, response}) => {
                    this.notification('Failed','CloseIcon','danger',`${error}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally{
                this.active.disabled_main = false
            }
        },

        resetForm() {
            this.form_nutrition.id = ''
            this.form_nutrition.registration_destination_id = ''
            this.form_nutrition.health_facility_item_id = ''
            this.form_nutrition.employee_nurse_id = ''
            this.form_nutrition.room_id = ''
            this.form_nutrition.diet_id = ''
            this.form_nutrition.date = moment(new Date()).format('YYYY-MM-DD')
            this.form_nutrition.time_type_id = ''
            this.form_nutrition.time_arr = []
            this.form_nutrition.notes = ''
            this.form_nutrition.status = 0
        },

        getNutritionList() {
            this.params.registration_destination_id = this.reg_des_id
            try {                
                this.$store.dispatch('nutrition/getListNutrition', this.params).then((response) => {
                    this.nutrition_list = response.rows
                    this.totalRows = response.iTotal
                }, error => {
                    console.error('Error while GET nutrition/getListNutrition at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET nutrition/getListNutrition at: ', error);
            }
        },

        onEditNutrition(obj) {
            this.resetForm()
            this.active.modal_edit = true
            
            this.form_nutrition.id = obj.id
            this.form_nutrition.registration_destination_id = obj.registration_destination_id
            this.form_nutrition.health_facility_item_id = obj.health_facility_item_id
            this.form_nutrition.employee_nurse_id = obj.employee_nurse_id
            this.form_nutrition.room_id = obj.room_id
            this.form_nutrition.diet_id = obj.diet_id
            this.form_nutrition.date = obj.date
            this.form_nutrition.time_type_id = obj.time_type_id
            this.form_nutrition.notes = obj.notes
            this.form_nutrition.status = 0
        },

        removeNutrition(obj) {            
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus order gizi tanggal ${obj.date} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('nutrition/deleteNutrition', obj.id)
                    .then(
                        response => {
                            if (response.status === 200 || response.status === 201) {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getNutritionList()
                            } else {
                                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            }
                        },
                        error => {
                            this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            console.error('Error While Delete Using nutrition/deleteNutritionl at: ', error)
                        }
                    )
                    .catch(({error, response}) => {
                        this.notification('Failed','CloseIcon','danger',`${error}`)
                    })
                }
            })
        },
        getTimeName(time_id) {
            if (time_id == 1) {
                return 'Pagi'
            } else if (time_id == 2) {
                return 'Siang'
            } else if (time_id == 3) {
                return 'Sore'
            } else {
                return '-'
            }
        },
    },
    created() {
        this.$store.dispatch('nutrition/getParamDiet')
        if (this.$route.params.id) {
            this.reg_des_id = this.$route.params.id
            this.getData(this.$route.params.id)
        }
    },
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>
<style scoped>
@import '../../../../@core/css/utility.css';

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