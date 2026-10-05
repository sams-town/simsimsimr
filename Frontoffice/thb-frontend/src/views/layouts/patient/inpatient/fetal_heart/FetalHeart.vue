<template>
    <div>
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
                <b-button @click="openModalFetal" pill variant="primary" class="px-3">
                    <feather-icon icon="PlusIcon" class="mr-1" /> Tambah
                </b-button>
            </div>
            <b-table bordered head-variant="light" :items="fetal_list" :fields="fields" class="mt-12" style="font-size: 13px;">
                <template #cell(user)="row">
                    <div class="font-weight-bold">{{ row.item.date_updated }}</div>
                    <div class="mt-8">{{ row.item.employee_nurse_name }}</div>
                </template>

                <template #cell(monitoring)="row">
                    <div class="row">
                        <div class="col-lg-6">
                            <table class="custom-border w-100">
                                <tr>
                                    <td style="width: 150px;">Systolic</td>
                                    <td style="width: 20px;">:</td>
                                    <td>{{ row.item.systolic }} mmHg</td>
                                </tr>
                                <tr>
                                    <td>Diastolic</td>
                                    <td>:</td>
                                    <td>{{ row.item.diastolic }} mmHg</td>
                                </tr>
                                <tr>
                                    <td>Ton. Uteri</td>
                                    <td>:</td>
                                    <td>{{ row.item.uterine_tone }}</td>
                                </tr>
                                <tr>
                                    <td>Kekuatan HIS</td>
                                    <td>:</td>
                                    <td>{{ row.item.uterine_contractions }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-lg-6">
                            <table class="custom-border w-100">
                                <tr>
                                    <td style="width: 150px;">Lama HIS</td>
                                    <td style="width: 20px;">:</td>
                                    <td>{{ row.item.uterine_contractions_duration }}</td>
                                </tr>
                                <tr>
                                    <td>Interval</td>
                                    <td>:</td>
                                    <td>{{ row.item.interval }}</td>
                                </tr>
                                <tr>
                                    <td>Detak Janjung Janin</td>
                                    <td>:</td>
                                    <td>{{ row.item.fetal_heart_rate }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </template>

                <template #cell(action_plan)="row">
                    <div v-html="parsedBody(row.item.action_plan)"></div>
                </template>

                <template #cell(action)="row">
                    <div class="d-flex">
                        <b-button @click="onEditFetal(row.item)" size="sm" variant="warning-500" class="mr-1">
                            <feather-icon icon="EditIcon" />
                        </b-button>
                        <b-button @click="removeFetal(row.item)" size="sm" variant="danger-500">
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

        <b-modal v-model="active.modal_fetal" title="Observasi Detak Jantung Janin" size="lg" no-close-on-backdrop>
            <div class="row">
                <div class="col-lg-4">
                    <label class="c-form-label">Tanggal</label>
                    <b-form-input v-model="form_fetal.observation_date" type="datetime-local" />
                </div>
                <div class="col-lg-4 mt-10 mt-lg-0">
                    <label class="c-form-label">Systolic</label>
                    <b-input-group append="mmHg">
                        <b-form-input type="number" autocomplete="off" v-model="form_fetal.systolic" />
                    </b-input-group>
                </div>
                <div class="col-lg-4 mt-10 mt-lg-0">
                    <label class="c-form-label">Diastolic</label>
                    <b-input-group append="mmHg">
                        <b-form-input type="number" autocomplete="off" v-model="form_fetal.diastolic" />
                    </b-input-group>
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Ton. Uteri</label>
                    <b-form-input autocomplete="off" v-model="form_fetal.uterine_tone" />
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Kekuatan HIS</label>
                    <b-form-input autocomplete="off" v-model="form_fetal.uterine_contractions" />
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Lama HIS</label>
                    <b-form-input autocomplete="off" v-model="form_fetal.uterine_contractions_duration" />
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Interval</label>
                    <b-form-input autocomplete="off" v-model="form_fetal.interval" />
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Detak Janjung Janin</label>
                    <b-form-input autocomplete="off" v-model="form_fetal.fetal_heart_rate" />
                </div>
                <div class="col-12 mt-10">
                    <label class="c-form-label">Rencana Tindakan</label>
                    <b-textarea v-model="form_fetal.action_plan" />
                </div>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end">
                    <b-button @click="validationFetal" :disabled="active.disabledFetal" variant="primary" class="px-3">Simpan</b-button>
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
    name: 'FetalHeart',
    components: {
        patientMenu, ModernLabel, vSelect
    },
    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),
            reg_des_id: '',
            regData: {},
            fetal_list: [],
            active: {
                modal_fetal: false,
                disabledFetal: false,
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
            form_fetal: {
                id: '',
                registration_destination_id: '',
                employee_nurse_id: '',
                observation_date: moment(new Date()).format('YYYY-MM-DD HH:MM'),
                systolic: '',
                diastolic: '',
                uterine_tone: '',
                uterine_contractions: '',
                uterine_contractions_duration: '',
                interval: '',
                fetal_heart_rate: '',
                action_plan: '',
            },
            fields: [
                { key: 'user', label: 'Petugas', thStyle: 'width: 250px' },
                { key: 'observation_date', label: 'Tanggal Observasi', thStyle: 'width: 150px' },
                { key: 'monitoring', label: 'Monitoring' },
                { key: 'action_plan', label: 'Rencana tindakan', thStyle: 'width: 300px' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 100px' },
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
                this.getFetalList()
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.getFetalList()
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
        },

        openModalFetal() {
            this.resetFormFetal()
            this.active.modal_fetal = true
        },

        resetFormFetal() {
            this.form_fetal.id = ''
            this.form_fetal.registration_destination_id = ''
            this.form_fetal.employee_nurse_id = ''
            this.form_fetal.observation_date = moment(new Date()).format('YYYY-MM-DD HH:MM')
            this.form_fetal.systolic = ''
            this.form_fetal.diastolic = ''
            this.form_fetal.uterine_tone = ''
            this.form_fetal.uterine_contractions = ''
            this.form_fetal.uterine_contractions_duration = ''
            this.form_fetal.interval = ''
            this.form_fetal.fetal_heart_rate = ''
            this.form_fetal.action_plan = ''
        },

        validationFetal() {
            this.form_fetal.registration_destination_id = this.reg_des_id
            this.form_fetal.employee_nurse_id = this.userData.employee_id

            try {
                this.active.disabledFetal = true
                this.$store.dispatch('fetalheart/postPutFetalHeart', this.form_fetal)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal_fetal = false
                        this.active.disabledFetal = false
                        this.resetFormFetal()
                        this.getFetalList()
                    } 
                )
                .catch(({error, response}) => {
                    this.notification('Failed','CloseIcon','danger',`${error}`)
                    this.active.disabledFetal = false
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
                this.active.disabledFetal = false
            }
        },

        getFetalList(regDesId) {
            this.params.registration_destination_id = this.reg_des_id

            try {                
                this.$store.dispatch('fetalheart/getListFetalHeart', this.params).then((response) => {
                    this.fetal_list = response.rows
                    this.totalRows = response.iTotal
            }, error => {
                    console.error('Error while GET fetalheart/getListFetalHeart at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET fetalheart/getListFetalHeart at: ', error);
            }
        },

        onEditFetal(obj) {
            this.openModalFetal()

            this.form_fetal.id = obj.id ? obj.id : ''
            this.form_fetal.registration_destination_id = obj.registration_destination_id ? obj.registration_destination_id : ''
            this.form_fetal.employee_nurse_id = obj.employee_nurse_id ? obj.employee_nurse_id : ''
            this.form_fetal.observation_date = obj.observation_date ? obj.observation_date : ''
            this.form_fetal.systolic = obj.systolic ? obj.systolic : ''
            this.form_fetal.diastolic = obj.diastolic ? obj.diastolic : ''
            this.form_fetal.uterine_tone = obj.uterine_tone ? obj.uterine_tone : ''
            this.form_fetal.uterine_contractions = obj.uterine_contractions ? obj.uterine_contractions : ''
            this.form_fetal.uterine_contractions_duration = obj.uterine_contractions_duration ? obj.uterine_contractions_duration : ''
            this.form_fetal.interval = obj.interval ? obj.interval : ''
            this.form_fetal.fetal_heart_rate = obj.fetal_heart_rate ? obj.fetal_heart_rate : ''
            this.form_fetal.action_plan = obj.action_plan ? obj.action_plan : ''
        },


        removeFetal(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus observasi tanggal ${obj.observation_date} dari ${obj.employee_nurse_name} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('fetalheart/deleteFetalHeart', obj.id)
                    .then(
                        response => {
                            if (response.status === 200 || response.status === 201) {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getFetalList()
                            } else {
                                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            }
                        },
                        error => {
                            this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            console.error('Error While Delete Using fetalheart/deleteFetalHeart at: ', error)
                        }
                    )
                    .catch(({error, response}) => {
                        this.notification('Failed','CloseIcon','danger',`${error}`)
                    })
                }
            })
        },
    },
   
    created () {
        if (this.$route.params.id) {
            this.reg_des_id = this.$route.params.id
            this.getData(this.$route.params.id)
            this.getFetalList()
        }
    }
}
</script>
<style scoped>
    table.custom-border tr td {
        border: 0px;
        padding: 4px 12px;
    }
</style>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>