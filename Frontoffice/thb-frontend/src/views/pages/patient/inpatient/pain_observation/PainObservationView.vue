<template>
    <div>
        <patientMenu selectedMenu="menu22" />
        <section class="card mt-32">
            <div class="card-header bg-indigo-200">
                <span class="text-bold-18">Data Dasar</span>
            </div>
            <div class="card-body">
                <div class="row" style="font-size: 14px">
                    <div class="col-lg-4">
                        <div class="font-weight-bold">Nama</div>
                        <div>{{ regData.name }} ({{ parsed_age(regData.birth_date) }})</div>
                        <div>{{ regData.room_name }} ({{ regData.room_name }})</div>
                    </div>
                    <div class="col-lg-4">
                        <div class="font-weight-bold">Tanggal Lahir</div>
                        <div>{{ localDate(regData.birth_date) }}</div>
                    </div>
                    <div class="col-lg-4">
                        <div class="font-weight-bold">No RM</div>
                        <div>{{ regData.norm }}</div>
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
                <b-button @click="openModalPain" pill variant="primary" size="sm" class="px-3">
                    <feather-icon icon="PlusIcon" class="mr-1" /> Tambah
                </b-button>
            </div>

            <b-table fixed bordered head-variant="light" :fields="painFields" :items="pain_list" class="mt-12" style="font-size: 13px;">
                <template #cell(observation)="row">
                    <table class="custom-border w-100" style="font-size: 11px;">
                        <tr>
                            <td style="width: 105px;">Tanggal</td>
                            <td style="width: 10px;">:</td>
                            <td>{{ row.item?.observation_date || '-' }}</td>
                        </tr>
                        <tr>
                            <td>Frekuensi Nadi</td>
                            <td>:</td>
                            <td>{{ row.item?.pulse || '-' }} x/mnt</td>
                        </tr>
                        <tr>
                            <td>Lokasi</td>
                            <td>:</td>
                            <td>{{ row.item?.location || '-' }}</td>
                        </tr>
                        <tr>
                            <td>Karakteristik</td>
                            <td>:</td>
                            <td>{{ row.item?.characteristics || '-' }}</td>
                        </tr>
                        <tr>
                            <td>Skor Nyeri</td>
                            <td>:</td>
                            <td>{{ row.item?.sore_score || '-' }}</td>
                        </tr>
                    </table>
                </template>

                <template #cell(prescription)="row">
                    <table class="custom-border w-100" style="font-size: 11px;">
                        <tr>
                            <td style="width: 120px;">Rutin Analgesik</td>
                            <td style="width: 10px;">:</td>
                            <td>{{ row.item?.analgesic_routine || '-' }}</td>
                        </tr>
                        <tr>
                            <td>Rutin Non-Analgesik</td>
                            <td>:</td>
                            <td>{{ row.item?.non_analgesic_routine || '-' }} x/mnt</td>
                        </tr>
                        <tr>
                            <td>Ekstra Analgesik</td>
                            <td>:</td>
                            <td>{{ row.item?.analgesic_extra || '-' }}</td>
                        </tr>
                        <tr>
                            <td>Ekstra Non-Analgesik</td>
                            <td>:</td>
                            <td>{{ row.item?.non_analgesic_extra || '-' }}</td>
                        </tr>
                        <tr>
                            <td>Skor Dosis</td>
                            <td>:</td>
                            <td>{{ row.item?.dose || '-' }}</td>
                        </tr>
                        <tr>
                            <td>Rute</td>
                            <td>:</td>
                            <td>{{ row.item?.route || '-' }}</td>
                        </tr>
                        <tr>
                            <td>Skor Nyeri (Ulang)</td>
                            <td>:</td>
                            <td>{{ row.item?.sore_score_after || '-' }}</td>
                        </tr>
                    </table>
                </template>

                <template #cell(employee)="row">
                    <div style="font-size: 11px;">
                        <div>
                            <div class="font-weight-bold">Perawat 1:</div>
                            <div>{{ row.item?.employee_nurse_name || '-' }}</div>
                        </div>
                        <div class="mt-1">
                            <div class="font-weight-bold">Perawat 2:</div>
                            <div>{{ row.item?.employee_nurse_2_name || '-' }}</div>
                        </div>
                    </div>
                </template>

                <template #cell(action)="row">
                    <div class="d-flex">
                        <b-button @click="onEditPain(row.item)" size="sm" variant="warning-500" class="mr-1">
                            <feather-icon icon="EditIcon" />
                        </b-button>
                        <b-button @click="removePain(row.item)" size="sm" variant="danger-500">
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

        <b-modal v-model="active.modal_pain" title="Observasi Tanda Vital" size="lg" no-close-on-backdrop>
            <section>
                <ModernLabel label="Observasi"/>
                <div class="row">
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Tanggal</label>
                        <b-form-input v-model="form_pain.observation_date" type="datetime-local" />
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Frekuensi Nadi</label>
                        <b-input-group append="x/mnt">
                            <b-form-input type="number" autocomplete="off" v-model="form_pain.pulse" />
                        </b-input-group>
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Lokasi</label>
                        <b-form-input autocomplete="off" v-model="form_pain.location" />
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Karakteristik</label>
                        <b-form-input autocomplete="off" v-model="form_pain.characteristics" />
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Skor Nyeri</label>
                        <b-form-input type="number" autocomplete="off" v-model="form_pain.sore_score" />
                    </div>                
                </div>
            </section>
            <section class="mt-20">
                <ModernLabel label="Pemberian Obat"/>
                <div class="row">
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Obat Rutin Analgesik</label>
                        <b-form-input v-model="form_pain.analgesic_routine" />
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Obat Rutin Non-Analgesik</label>
                        <b-form-input v-model="form_pain.non_analgesic_routine" />
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Obat Ekstra Analgesik</label>
                        <b-form-input v-model="form_pain.analgesic_extra" />
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Obat Ekstra Non-Analgesik</label>
                        <b-form-input v-model="form_pain.non_analgesic_extra" />
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Dosis</label>
                        <b-form-input v-model="form_pain.dose" type="number" />
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Rute</label>
                        <b-form-input v-model="form_pain.route" />
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Skor Nyeri (Observasi Ulang)</label>
                        <b-form-input v-model="form_pain.sore_score_after" type="number" />
                    </div>
                </div>
            </section>
            <section class="mt-20">
                <ModernLabel label="Perawat"/>
                <div class="row">
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Perawat 1</label>
                        <v-select v-model="form_pain.employee_nurse_id" :options="doctor_list" :reduce="item => item.value" label="title" placeholder="-- Perawat --"></v-select>
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Perawat 2</label>
                        <v-select v-model="form_pain.employee_nurse_2_id" :options="doctor_list" :reduce="item => item.value" label="title" placeholder="-- Perawat --"></v-select>
                    </div>
                </div>
            </section>
            <template #modal-footer>
                <div class="d-flex justify-content-end">
                    <b-button @click="validationPain" :disabled="active.disabledPain" variant="primary" class="px-3">Simpan</b-button>
                </div>
            </template>
        </b-modal>
    </div>
</template>
<script>
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import patientMenu from '@/@core/components/tab-menu/patientMenu.vue';
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import vSelect from 'vue-select'
import moment from 'moment'

export default {
    name: 'PatientInpatient',
    components: {
        patientMenu, ModernLabel, vSelect
    },
    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),
            reg_des_id: '',
            regData: {},
            pain_list: [],
            doctor_list: [],
            active: {
                modal_pain: false,
                disabledPain: false,
            },
            params: {
                offset: 0,
                limit: 20,
                registration_destination_id: ''
            },
            pageOptions: [20, 50, 100],
            currentPage:1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            isDisabledFilter: false,
            totalRows: 0,
            form_pain: {
                id: '',
                registration_destination_id: '',
                observation_date: moment(new Date()).format('YYYY-MM-DD HH:MM'),
                location: '',
                pulse: '',
                characteristics: '',
                sore_score: '',
                analgesic_routine: '',
                non_analgesic_routine: '',
                analgesic_extra: '',
                non_analgesic_extra: '',
                dose: '',
                route: '',
                sore_score_after: '',
                employee_nurse_id: '',
                employee_nurse_2_id: '',
            },
            painFields: [
                { key: 'observation', label: 'Observasi' },
                { key: 'prescription', label: 'Pemberian obat' },
                { key: 'employee', label: 'Perawat', thStyle: 'width: 230px' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 150px' },
            ],
        }
    },
    watch: {
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage*(value-1)+1
                this.lastRows = this.perPage*(value)+1
                this.params.offset = this.perPage*(value-1)
                this.params.limit = this.perPage
                this.getPainList()
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.getPainList()
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
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },

        localDate(input_date) {
            if (!input_date || input_date === "0000-00-00") {
                return "";
            }

            const parsed = moment(input_date);
            if (!parsed.isValid()) {
                return "";
            }

            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },

        parsed_age(input_date) {
            if (!input_date || input_date === "0000-00-00") {
                return "";
            }

            const parsed = moment(input_date);
            if (!parsed.isValid()) {
                return "";
            }

            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const currentDate = moment();

            const years = currentDate.diff(birthDate, 'years');
            birthDate.add(years, 'years');
            const months = currentDate.diff(birthDate, 'months');
            birthDate.add(months, 'months');
            const days = currentDate.diff(birthDate, 'days');

            return `${years} th ${months} bln ${days} hr`;
        },

        getData(regDesId) {
            this.$store.dispatch('registration/getById', regDesId).then(data => {
                this.regData = data[0]
            })

            this.$store.dispatch('employee/getDoctorOptions', '').then((response) => {
                this.doctor_list = response
            })
        },

        openModalPain() {
            this.resetFormPain()
            this.active.modal_pain = true
        },

        resetFormPain() {
            this.form_pain.id = ''
            this.form_pain.registration_destination_id = ''
            this.form_pain.observation_date = moment(new Date()).format('YYYY-MM-DD HH:MM'),
            this.form_pain.location = ''
            this.form_pain.pulse = ''
            this.form_pain.characteristics = ''
            this.form_pain.sore_score = ''
            this.form_pain.analgesic_routine = ''
            this.form_pain.non_analgesic_routine = ''
            this.form_pain.analgesic_extra = ''
            this.form_pain.non_analgesic_extra = ''
            this.form_pain.dose = ''
            this.form_pain.route = ''
            this.form_pain.sore_score_after = ''
            this.form_pain.employee_nurse_id = ''
            this.form_pain.employee_nurse_2_id = ''
        },

        validationPain() {
            if (this.form_pain.id == '') {
                this.form_pain.registration_destination_id = this.reg_des_id
            }

            try {
                this.active.disabledPain = true
                this.$store.dispatch('painobservation/postPutPain', this.form_pain)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal_pain = false
                        this.resetFormPain()
                        this.getPainList()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabledPain = false
            }
        },

        getPainList() {
            this.params.registration_destination_id = this.reg_des_id

            try {                
                this.$store.dispatch('painobservation/getListPain', this.params).then((response) => {
                    this.pain_list = response.rows
                    this.totalRows = response.iTotal
                    // console.log('this.pain_list: ', this.pain_list);
                }, error => {
                    console.error('Error while GET painobservation/getListPain at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET painobservation/getListPain at: ', error);
            }
        },

        onEditPain(obj) {
            this.openModalPain()

            this.form_pain.id = obj?.id || ''
            this.form_pain.registration_destination_id = obj?.registration_destination_id || ''
            this.form_pain.observation_date = obj?.observation_date || moment(new Date()).format('YYYY-MM-DD HH:MM'),
            this.form_pain.location = obj?.location || ''
            this.form_pain.pulse = obj?.pulse || ''
            this.form_pain.characteristics = obj?.characteristics || ''
            this.form_pain.sore_score = obj?.sore_score || ''
            this.form_pain.analgesic_routine = obj?.analgesic_routine || ''
            this.form_pain.non_analgesic_routine = obj?.non_analgesic_routine || ''
            this.form_pain.analgesic_extra = obj?.analgesic_extra || ''
            this.form_pain.non_analgesic_extra = obj?.non_analgesic_extra || ''
            this.form_pain.dose = obj?.dose || ''
            this.form_pain.route = obj?.route || ''
            this.form_pain.sore_score_after = obj?.sore_score_after || ''
            this.form_pain.employee_nurse_id = obj?.employee_nurse_id || ''
            this.form_pain.employee_nurse_2_id = obj?.employee_nurse_2_id || ''
        },


        removePain(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus observasi tanggal ${obj.observation_date}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('painobservation/deletePain', obj.id)
                    .then(
                        res => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.getPainList()
                        }
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    })
                }
            })
        },
    },

    created () {
        if (this.$route.params.id) {
            this.reg_des_id = this.$route.params.id
            this.getData(this.$route.params.id)
            this.getPainList()
        }
    }
}
</script>
<style scoped>
    table.custom-border tr td {
        border: 0px;
        padding: 2px 4px;
    }
</style>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>