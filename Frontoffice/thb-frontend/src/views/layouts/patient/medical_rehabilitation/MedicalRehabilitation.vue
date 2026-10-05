<template>
    <div>
        <patientMenu selectedMenu="menu23" />
        <div class="label-head my-16 my-lg-24">
            <span class="px-16 text-bold-24 text-neutral-700">Rehabilitasi Medik</span>
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

        <!-- <section class="row mt-32">
            <b-button @click="openModalPhy">Form kedua</b-button>
        </section> -->

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
            <b-table bordered head-variant="light" :items="medical_list" :fields="medical_fields" class="mt-12" style="font-size: 13px;">
                <template #cell(rehabilitation)="row">
                    <div class="row">
                        <div class="col-lg-6">
                            <div>
                                <div class="font-weight-bold">Anamnesa:</div>
                                <div v-html="parsedBody(row.item.anamnesa ? row.item.anamnesa : '-')"></div>
                            </div>
                            <div class="mt-12">
                                <div class="font-weight-bold">Pemeriksaan & Uji Fungsi :</div>
                                <div v-html="parsedBody(row.item.functional_testing ? row.item.functional_testing : '-')"></div>
                            </div>
                            <div class="mt-12">
                                <div class="font-weight-bold">Diagnosis Medis (ICD-10) :</div>
                                <template v-if="row.item.medical_diagnosis && row.item.medical_diagnosis.length > 0">
                                    <li v-for="(list, index) in row.item.functional_diagnosis">{{ list.icd10_code }} {{ list.icd10_name }}</li>
                                </template>
                            </div>
                            <div class="mt-12">
                                <div class="font-weight-bold">Diagnosis Fungsi (ICD-10) :</div>
                                <template v-if="row.item.functional_diagnosis && row.item.functional_diagnosis.length > 0">
                                    <li v-for="(list, index) in row.item.functional_diagnosis">{{ list.icd10_code }} {{ list.icd10_name }}</li>
                                </template>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mt-12 mt-lg-0">
                                <div class="font-weight-bold">Pemeriksaan Penunjang :</div>
                                <div v-html="parsedBody(row.item.support_examination ? row.item.support_examination : '-')"></div>
                            </div>
                            <div class="mt-12">
                                <div class="font-weight-bold">Tata Laksana KFR (ICD9 CM) :</div>
                                <template v-if="row.item.kfr_management && row.item.kfr_management.length > 0">
                                    <li v-for="(list, index) in row.item.kfr_management">{{ list.icd9cm_code }} {{ list.icd9cm_name }}</li>
                                </template>
                            </div>
                            <div class="mt-12">
                                <div class="font-weight-bold">Anjuran :</div>
                                <div v-html="parsedBody(row.item.advice ? row.item.advice : '-')"></div>
                            </div>
                            <div class="mt-12">
                                <div class="font-weight-bold">Evaluasi :</div>
                                <div v-html="parsedBody(row.item.evaluation ? row.item.evaluation : '-')"></div>
                            </div>
                            <div class="mt-12">
                                <div class="font-weight-bold">Suspek Penyakit Akibat :</div>
                                <div>{{ row.item.suspected_disease_type_id == 0 ? 'Tidak' : 'Ya, ' }} <span v-if="row.item.suspected_disease_type_id == 1">{{ row.item.suspected_disease }}</span></div>
                            </div>
                        </div>
                    </div>
                </template>

                <template #cell(results)="row">
                    <div>
                        <div class="font-weight-bold">Hasil yang didapat:</div>
                        <div v-html="parsedBody(row.item.result ? row.item.result : '-')"></div>
                    </div>
                    <div class="mt-12">
                        <div class="font-weight-bold">Kesimpulan :</div>
                        <div v-html="parsedBody(row.item.conclusion ? row.item.conclusion : '-')"></div>
                    </div>
                    <div class="mt-12">
                        <div class="font-weight-bold">Rekomendasi :</div>
                        <div v-html="parsedBody(row.item.recommendation ? row.item.recommendation : '-')"></div>
                    </div>
                </template>

                <template #cell(action)="row">
                    <div class="d-flex">
                        <b-button @click="onEditMedical(row.item)" size="sm" variant="warning-500" class="mr-1">
                            <feather-icon icon="EditIcon" class="mr-2" />Tindakan
                        </b-button>
                        <b-button @click="onMedicalResult(row.item)" size="sm" variant="primary-500" class="mr-1">
                            <feather-icon icon="EditIcon" class="mr-2" />Hasil
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

        <b-modal v-model="active.modal_main" size="lg" title="Rehabilitasi Medik" no-close-on-backdrop>
            <div class="">
                <div class="col-lg-4">
                    <label class="c-form-label">Tanggal Pelayanan</label>
                    <b-form-input v-model="form_medical.service_date" type="date" />
                </div>
                <div class="col mt-10">
                    <label class="c-form-label">Anamnesa</label>
                    <b-textarea v-model="form_medical.anamnesa" />
                </div>
                <div class="col mt-10">
                    <label class="c-form-label">Pemeriksaan & Uji Fungsi</label>
                    <b-textarea v-model="form_medical.functional_testing" />
                </div>
                <div class="col mt-10">
                    <label class="c-form-label">Diagnosis Medis (ICD-10)</label>
                    <template v-if="form_medical.medical_diagnosis.length > 0">
                        <b-table responsive bordered :fields="icd10Fields" :items="form_medical.medical_diagnosis" class="mb-8 text-center" style="font-size: 14px;" >
                            <template #cell(no)="row">{{ row.index+1 }}</template>
                            <template #cell(title)="row">
                                <div class="text-left">{{ row.value }}</div>
                            </template>
                            <template #cell(action)="row">
                                <b-button variant="danger-600" size="sm" @click="deleteMedicalIcd10(row.index)" class="mr-2">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </template>
                        </b-table>
                    </template>  
                    <v-select v-model="medicalIcd10_id" @input="onAddMedicalIcd10($event)" aria-readonly="true" @search="onSearchMedicalIcd10" :options="OptionsMedicalIcd10" :reduce="OptionsMedicalIcd10 => OptionsMedicalIcd10" label="title" placeholder="-- Diagnosa --"></v-select>
                </div>
                <div class="col mt-10">
                    <label class="c-form-label">Diagnosis Fungsi (ICD-10)</label>
                    <template v-if="form_medical.functional_diagnosis.length > 0">
                        <b-table responsive bordered :fields="icd10Fields" :items="form_medical.functional_diagnosis" class="mb-8 text-center" style="font-size: 14px;" >
                            <template #cell(no)="row">{{ row.index+1 }}</template>
                            <template #cell(title)="row">
                                <div class="text-left">{{ row.value }}</div>
                            </template>
                            <template #cell(action)="row">
                                <b-button variant="danger-600" size="sm" @click="deleteFuncIcd10(row.index)" class="mr-2">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </template>
                        </b-table>
                    </template>  
                    <v-select v-model="funcIcd10_id" @input="onAddFuncIcd10($event)" aria-readonly="true" @search="onSearchFuncIcd10" :options="OptionsFuncIcd10" :reduce="OptionsFuncIcd10 => OptionsFuncIcd10" label="title" placeholder="-- Diagnosa --"></v-select>
                </div>
                <div class="col mt-10">
                    <label class="c-form-label">Pemeriksaan Penunjang</label>
                    <b-textarea v-model="form_medical.support_examination" />
                </div>
                <div class="col mt-10">
                    <label class="c-form-label">Tata Laksana KFR (ICD9 CM)</label>
                    <template v-if="form_medical.kfr_management.length > 0">
                        <b-table responsive bordered :fields="icd9cmFields" :items="form_medical.kfr_management" class="mb-8 text-center" style="font-size: 14px;" >
                            <template #cell(no)="row">{{ row.index+1 }}</template>
                            <template #cell(title)="row">
                                <div class="text-left">{{ row.value }}</div>
                            </template>
                            <template #cell(action)="row">
                                <b-button variant="danger-600" size="sm" @click="delete9cm(row.index)" class="mr-2">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </template>
                        </b-table>
                    </template>
                    <v-select v-model="kfr_icd9cm_id" @input="onAdd9cm($event)" aria-readonly="true" @search="onSearchIcd9cm" :options="OptionsIcd9cm" :reduce="OptionsIcd9cm => OptionsIcd9cm" label="title" placeholder="-- ICD 9 --"></v-select>
                </div>
                <div class="col mt-10">
                    <label class="c-form-label">Anjuran</label>
                    <b-textarea v-model="form_medical.advice" />
                </div>
                <div class="col mt-10">
                    <label class="c-form-label">Evaluasi</label>
                    <b-textarea v-model="form_medical.evaluation" />
                </div>
                <div class="col mt-10">
                    <label class="c-form-label">Suspek Penyakit Akibat</label>
                    <b-form-radio-group v-model="form_medical.suspected_disease_type_id">
                        <b-form-radio value="0">Tidak</b-form-radio>
                        <b-form-radio value="1">Ya</b-form-radio>
                    </b-form-radio-group>
                    <b-form-input v-model="form_medical.suspected_disease" v-if="form_medical.suspected_disease_type_id == '1'" class="mt-10" />
                </div>
            </div>

            <template #modal-footer>
                <div class="d-flex justify-content-end">
                    <b-button @click="validationMedical" :disabled="active.disabled_main" variant="primary">Simpan</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active.modal_result" size="lg" title="Update Hasil" no-close-on-backdrop>
            <div class="col">
                <label class="c-form-label">Hasil Yang Didapat</label>
                <b-textarea v-model="form_result.result" />
            </div>
            <div class="col mt-10">
                <label class="c-form-label">Kesimpulan</label>
                <b-textarea v-model="form_result.conclusion" />
            </div>
            <div class="col mt-10">
                <label class="c-form-label">Rekomendasi</label>
                <b-textarea v-model="form_result.recommendation" />
            </div>

            <template #modal-footer>
                <div class="d-flex justify-content-end">
                    <b-button @click="validationResult" :disabled="active.disabled_result" variant="primary">Simpan</b-button>
                </div>
            </template>
        </b-modal>

        <!-- <b-modal v-model="active.modal_sch" size="lg" title="Jadwal Fisiioterapi" no-close-on-backdrop>
            <div class="">
                <label class="c-form-label">Diagnosa</label>
                <template v-if="form_schedule.icd10.length > 0">
                    <b-table responsive bordered :fields="icd10Fields" :items="form_schedule.icd10" class="mt-12 text-center" style="font-size: 14px;" >
                        <template #cell(no)="row">{{ row.index+1 }}</template>
                        <template #cell(title)="row">
                            <div class="text-left">{{ row.value }}</div>
                        </template>
                        <template #cell(action)="row">
                            <b-button variant="danger-600" size="sm" @click="removeSchIcd10(row.index)" class="mr-2">
                                <feather-icon icon="TrashIcon" />
                            </b-button>
                        </template>
                    </b-table>
                </template>  
                <v-select v-model="icd10_sch_id" @input="addSchIcd10($event)" aria-readonly="true" @search="onSearchSchIcd10" :options="OptionsSchIcd10" :reduce="OptionsSchIcd10 => OptionsSchIcd10" label="title" placeholder="-- Diagnosa --"></v-select>
            </div>

            <ModernLabel label="Program" />
            <div class="row mt-12">
                <div class="col-lg-6">
                    <label class="c-form-label">Program</label>
                    <b-form-input v-model="sch_program" />
                </div>
                <div class="col-lg-6">
                    <label class="c-form-label">Tanggal</label>
                    <b-form-input v-model="sch_date" type="date" />
                </div>
            </div>
            <b-button @click="addPrograms">Tambah</b-button>

            <b-table responsive bordered :fields="scheduleFields" :items="form_schedule.programs" class="mt-12" style="font-size: 14px;" >
                <template #cell(no)="row">{{ row.index+1 }}</template>
                <template #cell(action)="row">
                    <b-button variant="danger-600" size="sm" @click="removeProgram(row.index)" class="mr-2">
                        <feather-icon icon="TrashIcon" />
                    </b-button>
                </template>
            </b-table>

            <template #modal-footer>
                <div class="d-flex justify-content-end">
                    <b-button @click="validationPhySchedule" :disabled="active.disabled_sch" variant="primary">Simpan</b-button>
                </div>
            </template>
        </b-modal> -->
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
    name: 'PatientMedicalRehabilitation',
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
                modal_result: false,
                disabled_result: false,
                // modal_sch: false,
                // disabled_sch: false,
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

            medical_fields: [
                { key: 'service_date', label: 'Tanggal Pelayanan', thStyle: 'width: 150px' },
                { key: 'rehabilitation', label: 'Tindakan Uji Fungsi / Prosedur KFR' },
                { key: 'results', label: 'Hasil Tindakan', thStyle: 'width: 350px' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 250px' },
            ],

            medical_list: [],
            form_medical: {
                id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                service_date: moment(new Date()).format('YYYY-MM-DD'),
                anamnesa: '',
                functional_testing: '',
                support_examination: "",
                advice: '',
                evaluation: '',
                suspected_disease_type_id: 0,
                suspected_disease: "",
                medical_diagnosis: [],
                functional_diagnosis: [],
                kfr_management: []
            },
            medicalIcd10_id:'',
            OptionsMedicalIcd10:[],

            funcIcd10_id:'',
            OptionsFuncIcd10:[],

            kfr_icd9cm_id:'',
            OptionsIcd9cm:[],

            icd10Fields: [
                { key: 'no', label: 'No', thStyle: 'width: 60px' },
                { key: 'title', label: 'ICD10', thClass: 'text-left' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 100px' },
            ],
            icd9cmFields: [
                { key: 'no', label: 'No', thStyle: 'width: 60px' },
                { key: 'title', label: 'ICD9CM', thClass: 'text-left' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 100px' },
            ],

            form_result: {
                medical_id: '',
                result: '',
                conclusion: '',
                recommendation: '',
            },

            // form_schedule: {
            //     icd10: [],
            //     programs: [],
            // },
            // icd10_sch_id:'',
            // OptionsSchIcd10:[],

            // sch_program: '',
            // sch_date: '',

            // scheduleFields: [
            //     { key: 'no', label: 'No', thStyle: 'width: 60px' },
            //     { key: 'program', label: 'Program', thClass: 'text-left' },
            //     { key: 'date', label: 'Tanggal', thClass: 'text-left' },
            //     { key: 'action', label: 'Aksi', thStyle: 'width: 100px' },
            // ],
        }
    },
    watch: {
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage*(value-1)+1
                this.lastRows = this.perPage*(value)+1
                this.params.offset = this.perPage*(value-1)
                this.params.limit = this.perPage
                this.getMedicalList()
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.getMedicalList()
            }
        },
        "form_medical.suspected_disease_type_id"(newValue) {
            if (newValue != "1") {
                this.form_medical.suspected_disease = ""
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

        //  FORM REHABILITASI MEDIK

        onSearchMedicalIcd10(search, loading) {
            if(search.length >= 3) {
                loading(true);
                this.$store.dispatch('icd10/getParam', search).then(data => {
                    this.OptionsMedicalIcd10 = []
                    for (let i = 0; i < data.length; i++) {
                        const option = []
                        for (const key in data[i]) {
                            if (key === 'id') { option['value'] = data[i][key] } else if (key === 'name') { option['title'] = data[i]['code']+' '+data[i][key] } else if (key === 'id') { option['oid'] = data[i][key] }
                        }
                        this.OptionsMedicalIcd10.push(Object.assign({}, option))
                    }
                    loading(false)
                })
            }
        },

        onAddMedicalIcd10 (event) {
            this.medicalIcd10_id = ''

            if (this.form_medical.medical_diagnosis.some(item => item.icd10_id === event.value)) {
                this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list ICD10 yang sama')
            } else {
                this.form_medical.medical_diagnosis.push({ icd10_id:event.value, title:event.title, value:event.value, action: 'action'})
            }
        },

        deleteMedicalIcd10 (index) {
            this.form_medical.medical_diagnosis.splice(index, 1)
        },

        onSearchFuncIcd10(search, loading) {
            if(search.length >= 3) {
                loading(true);
                this.$store.dispatch('icd10/getParam', search).then(data => {
                    this.OptionsFuncIcd10 = []
                    for (let i = 0; i < data.length; i++) {
                        const option = []
                        for (const key in data[i]) {
                            if (key === 'id') { option['value'] = data[i][key] } else if (key === 'name') { option['title'] = data[i]['code']+' '+data[i][key] } else if (key === 'id') { option['oid'] = data[i][key] }
                        }
                        this.OptionsFuncIcd10.push(Object.assign({}, option))
                    }
                    loading(false)
                })
            }
        },

        onAddFuncIcd10 (event) {
            this.funcIcd10_id = ''

            if (this.form_medical.functional_diagnosis.some(item => item.icd10_id === event.value)) {
                this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list ICD10 yang sama')
            } else {
                this.form_medical.functional_diagnosis.push({ icd10_id:event.value, title:event.title, value:event.value, action: 'action'})
            }
        },

        deleteFuncIcd10 (index) {
            this.form_medical.functional_diagnosis.splice(index, 1)
        },

        onSearchIcd9cm(search, loading) {
            if(search.length >= 2) {
                loading(true);
                this.$store.dispatch('icd9cm/getParam', search).then(data => {
                    this.OptionsIcd9cm = []
                    for (let i = 0; i < data.length; i++) {
                        const option = []
                        for (const key in data[i]) {
                            if (key === 'id') { option['value'] = data[i][key] } else if (key === 'name') { option['title'] = data[i]['code']+' '+data[i][key] } else if (key === 'id') { option['oid'] = data[i][key] }
                        }
                        this.OptionsIcd9cm.push(Object.assign({}, option))
                    }
                    loading(false)
                })
            }
        },

        onAdd9cm (event) {
            this.kfr_icd9cm_id = ''

            if (this.form_medical.kfr_management.some(item => item.icd9cm_id === event.value)) {
                this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list ICD9 yang sama')
            } else {
                this.form_medical.kfr_management.push({ icd9cm_id:event.value, title:event.title, value:event.value, action: 'action'})
            }
        },

        delete9cm (index) {
            this.form_medical.kfr_management.splice(index, 1)
        },

        openMainModal() {
            this.active.modal_main = true
            this.clearFormMedical()
        },

        clearFormMedical() {
            this.form_medical.id = ''
            this.form_medical.registration_destination_id = ''
            this.form_medical.doctor_in_charge_id = ''
            this.form_medical.service_date = moment(new Date()).format('YYYY-MM-DD')
            this.form_medical.anamnesa = ''
            this.form_medical.functional_testing = ''
            this.form_medical.support_examination = ""
            this.form_medical.advice = ''
            this.form_medical.evaluation = ''
            this.form_medical.suspected_disease_type_id = 0
            this.form_medical.suspected_disease = ""
            this.form_medical.medical_diagnosis = []
            this.form_medical.functional_diagnosis = []
            this.form_medical.kfr_management = []
        },

        validationMedical() {
            if (this.form_medical.id == '') {
                this.form_medical.registration_destination_id = this.reg_des_id
                this.form_medical.doctor_in_charge_id = this.regData.doctor_in_charge_id
            }

            try {
                this.active.disabled_main = true
                this.$store.dispatch('rehabilitation/postPutMedical', this.form_medical)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal_main = false
                        this.active.disabled_main = false
                        this.clearFormMedical()
                        this.getMedicalList()
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

        getMedicalList() {
            this.params.registration_destination_id = this.reg_des_id
            try {                
                this.$store.dispatch('rehabilitation/getMedicalList', this.params).then((response) => {
                    this.medical_list = response.rows
                    this.totalRows = response.iTotal
                }, error => {
                    console.error('Error while GET rehabilitation/getMedicalList at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET rehabilitation/getMedicalList at: ', error);
            }
        },

        onEditMedical(obj) {
            this.clearFormMedical()
            this.active.modal_main = true
            
            this.form_medical.id = obj.id
            this.form_medical.registration_destination_id = obj.registration_destination_id
            this.form_medical.doctor_in_charge_id = obj.doctor_in_charge_id
            this.form_medical.service_date = obj.service_date 
            this.form_medical.anamnesa = obj.anamnesa
            this.form_medical.functional_testing = obj.functional_testing
            this.form_medical.support_examination = obj.support_examination
            this.form_medical.advice = obj.advice
            this.form_medical.evaluation = obj.evaluation
            this.form_medical.suspected_disease_type_id = obj.suspected_disease_type_id
            this.form_medical.suspected_disease = obj.suspected_disease

            if (obj.medical_diagnosis) {
                const mappedMed = obj.medical_diagnosis.map(item => {
                    return {
                        icd10_id: item.icd10_id,
                        value: item.icd10_id,
                        title: `${item.icd10_code} ${item.icd10_name}`, 
                        action: "action"
                    };
                });                
                this.form_medical.medical_diagnosis = mappedMed;
            }

            if (obj.functional_diagnosis) {
                const mappedFunc = obj.functional_diagnosis.map(item => {
                    return {
                        icd10_id: item.icd10_id,
                        value: item.icd10_id,
                        title: `${item.icd10_code} ${item.icd10_name}`, 
                        action: "action"
                    };
                });                
                this.form_medical.functional_diagnosis = mappedFunc;
            }

            if (obj.kfr_management) {
                const mappedKfr = obj.kfr_management.map(item => {
                    return {
                        icd9cm_id: item.icd9cm_id,
                        value: item.icd9cm_id,
                        title: `${item.icd9cm_code} ${item.icd9cm_name}`, 
                        action: "action"
                    };
                });                
                this.form_medical.kfr_management = mappedKfr;
            }
        },

        removeMedical(obj) {            
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus Rehabilitasi Medik tanggal ${obj.service_date} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('rehabilitation/deleteMedical', obj.id)
                    .then(
                        response => {
                            if (response.status === 200 || response.status === 201) {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getMedicalList()
                            } else {
                                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            }
                        },
                        error => {
                            this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            console.error('Error While Delete Using rehabilitation/deleteMedical at: ', error)
                        }
                    )
                    .catch(({error, response}) => {
                        this.notification('Failed','CloseIcon','danger',`${error}`)
                    })
                }
            })
        },

        onMedicalResult(obj) {
            this.clearFormResult()
            this.active.modal_result = true

            this.form_result.medical_id = obj.id
            this.form_result.result = obj.result
            this.form_result.conclusion = obj.conclusion
            this.form_result.recommendation = obj.recommendation
        },
        
        clearFormResult() {
            this.form_result.medical_id = ''
            this.form_result.result = ''
            this.form_result.conclusion = ''
            this.form_result.recommendation = ''
        },

        validationResult() {
            try {
                this.active.disabled_result = true
                this.$store.dispatch('rehabilitation/putResult', this.form_result)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal_result = false
                        this.active.disabled_result = false
                        this.clearFormResult()
                        this.getMedicalList()
                    } 
                )
                .catch(({error, response}) => {
                    this.notification('Failed','CloseIcon','danger',`${error}`)
                    this.active.disabled_result = false
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
                this.active.disabled_result = false
            }
        },


        // JADWAL FISIOTERASPI
        // openModalPhy() {
        //     this.active.modal_sch = true
        // },

        // onSearchSchIcd10(search, loading) {
        //     if(search.length >= 3) {
        //         loading(true);
        //         this.$store.dispatch('icd10/getParam', search).then(data => {
        //             this.OptionsSchIcd10 = []
        //             for (let i = 0; i < data.length; i++) {
        //                 const option = []
        //                 for (const key in data[i]) {
        //                     if (key === 'id') { option['value'] = data[i][key] } else if (key === 'name') { option['title'] = data[i]['code']+' '+data[i][key] } else if (key === 'id') { option['oid'] = data[i][key] }
        //                 }
        //                 this.OptionsSchIcd10.push(Object.assign({}, option))
        //             }
        //             loading(false)
        //         })
        //     }
        // },

        // addSchIcd10 (event) {
        //     this.icd10_sch_id = ''

        //     if (this.form_schedule.icd10.some(item => item.icd10_id === event.value)) {
        //         this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list ICD10 yang sama')
        //     } else {
        //         this.form_schedule.icd10.push({ icd10_id:event.value, title:event.title, value:event.value, action: 'action'})
        //     }

        // },

        // removeSchIcd10 (index) {
        //     this.form_schedule.icd10.splice(index, 1)
        // },

        // addPrograms() {
        //     let temp_arry = {
        //         program: this.sch_program,
        //         date: this.sch_date,
        //     }
        //     this.form_schedule.programs.push(temp_arry)
        //     this.sch_program = ''
        //     this.sch_date = ''
        // },

        // removeProgram(index) {
        //     this.form_schedule.programs.splice(index, 1)
        // },

        // validationPhySchedule() {
        //     console.log('this.form_schedule: ', this.form_schedule);

        // }
    },
    
    created () {
        if (this.$route.params.id) {
            this.reg_des_id = this.$route.params.id
            this.getData(this.$route.params.id)
            this.getMedicalList()
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