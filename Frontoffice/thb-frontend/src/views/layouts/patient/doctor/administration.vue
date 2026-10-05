<template>
    <div>
        <b-modal v-model="showModal" size="md" hide-footer no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ADMINISTRASI SURAT UNTUK PASIEN
                    <span class="text-primary">{{ temp_header.alias_name }}. {{ temp_header.name }} | {{localDate(temp_header.birth_date)}} ({{ parsedAge(temp_header.birth_date) }}) | {{ temp_header.norm }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div class="card">
                <div class="card-body p-12">
                    <div class="row">
                        <div class="col-lg-4">
                            <label class="fs-13 mb-0">Tanggal *</label>
                            <b-form-input size="sm" v-model="form.date" type="date" />
                        </div>
                        <div class="col-lg-8 mt-8 mt-lg-0">
                            <label class="fs-13 mb-0">Tipe Surat</label>
                            <v-select v-model="form.letter_type_id" aria-readonly="true"
                                :options="OptionsTypeLetter"
                                :reduce="OptionsTypeLetterItem => OptionsTypeLetterItem" label="title"
                                placeholder="-- Tipe Surat --"
                                class="select-size-sm"
                            />
                        </div>
                    </div>
                    <div class="row" v-if="form.letter_type_id && form.letter_type_id.value == '035e0b43-a5a9-41ae-a183-00398b4b3ef2'">
                        <div class="col-lg-6 mt-8" v-if="form.letter_type_id && form.letter_type_id.value == '035e0b43-a5a9-41ae-a183-00398b4b3ef2'">
                            <label class="fs-13 mb-0">Dokter *</label>
                             <v-select
                                v-model="form.selected_doctor_id"
                                :options="doctor_list"
                                :reduce="doctor => doctor.value"
                                label="title"
                                placeholder="-- Pilih Dokter --"
                            ></v-select>
                        </div>
                        <div class="col-lg-6 mt-8">
                            <label class="fs-13 mb-0">Usia Kehamilan *</label>
                            <b-form-input size="sm" v-model="form.age_pregnant" type="text" />
                        </div>
                    </div>
                    <div class="row" v-if="form.letter_type_id && form.letter_type_id.value == 'e9432634-dd6b-41cd-b57d-7c34daba9efc'">
                        <div class="col-lg-6 mt-8" v-if="form.letter_type_id && form.letter_type_id.value == 'e9432634-dd6b-41cd-b57d-7c34daba9efc'">
                            <label class="fs-13 mb-0">Dokter *</label>
                             <v-select
                                v-model="form.selected_doctor_id"
                                :options="doctor_list"
                                :reduce="doctor => doctor.value"
                                label="title"
                                placeholder="-- Pilih Dokter --"
                            ></v-select>
                        </div>
                        <div class="col-lg-6 mt-8">
                            <label class="fs-13 mb-0">Waktu Meninggal *</label>
                            <b-input-group>
                                <b-input type="date" v-model="temp.date" />
                                <b-input type="time" v-model="temp.time" />
                            </b-input-group>
                        </div>
                        <div class="col-lg-6 mt-8">
                            <label class="fs-13 mb-0">Nama Lengkap *</label>
                            <b-form-input size="sm" v-model="form.receipt_name" type="text" />
                        </div>
                        <div class="col-lg-6 mt-8">
                            <label class="fs-13 mb-0">Kartu Identitas *</label>
                            <b-form-input size="sm" v-model="form.receipt_idcard" type="text" />
                        </div>
                        <div class="col-lg-6 mt-8">
                            <label class="fs-13 mb-0">Tanggal Lahir *</label>
                            <b-form-input size="sm" v-model="form.birth_date" type="date" />
                        </div>
                        <div class="col-lg-6 mt-8">
                            <label class="fs-13 mb-0">No HP *</label>
                            <b-form-input size="sm" v-model="form.receipt_phone" type="text" />
                        </div>
                        <div class="col-lg-6 mt-8">
                            <label class="fs-13 mb-0">Hubungan dengan Pasien *</label>
                            <b-form-input size="sm" v-model="form.receipt_relation" type="text" />
                        </div>
                        <div class="col-lg-6 mt-8">
                            <label class="fs-13 mb-0">Alamat *</label>
                            <b-form-input size="sm" v-model="form.receipt_address" type="text" />
                        </div>
                        <div class="col-lg-6 mt-8">
                            <label class="fs-13 mb-0">Alamat Jenazah *</label>
                            <b-form-input size="sm" v-model="form.destination_of_the_corpse" type="text" />
                        </div>
                        <div class="col-lg-6 mt-8">
                            <label class="fs-13 mb-0">Agama *</label>
                            <b-form-input size="sm" v-model="form.receipt_religion" type="text" />
                        </div>
                    </div>
                    <!-- <div class="row" v-if="form.letter_type_id && form.letter_type_id.value == 'e9432634-dd6b-41cd-b57d-7c34daba9efc'">
                        <div class="col-lg-6 mt-8">
                            <label class="fs-13 mb-0">Nama Lengkap *</label>
                            <b-form-input size="sm" v-model="form.end" type="text" />
                        </div>
                        <div class="col-lg-6 mt-8">
                            <label class="fs-13 mb-0">Kartu Identitas *</label>
                            <b-form-input size="sm" v-model="form.end" type="text" />
                        </div>
                        <div class="col-lg-6 mt-8">
                            <label class="fs-13 mb-0">Tanggal Lahir *</label>
                            <b-form-input size="sm" v-model="form.birth_date" type="date" />
                        </div>
                        <div class="col-lg-6 mt-8">
                            <label class="fs-13 mb-0">No HP *</label>
                            <b-form-input size="sm" v-model="form.end" type="text" />
                        </div>
                        <div class="col-lg-6 mt-8">
                            <label class="fs-13 mb-0">Hubungan dengan Pasien *</label>
                            <b-form-input size="sm" v-model="form.end" type="text" />
                        </div>
                        <div class="col-lg-6 mt-8">
                            <label class="fs-13 mb-0">Alamat *</label>
                            <b-form-input size="sm" v-model="form.end" type="text" />
                        </div>
                        <div class="col-lg-6 mt-8">
                            <label class="fs-13 mb-0">Tujuan Jenazah *</label>
                            <b-form-input size="sm" v-model="form.end" type="text" />
                        </div>
                    </div> -->
                    <div class="row" v-if="form.letter_type_id && form.letter_type_id.value == '79b57879-2205-4af9-88e8-d97fb8b14422' || form.letter_type_id.value == '7cbf9ef9-71fa-4d14-9f65-a3021bd411ab'">
                        <div class="col-lg-4 mt-8" v-if="form.letter_type_id && form.letter_type_id.value == '79b57879-2205-4af9-88e8-d97fb8b14422'">
                            <label class="fs-13 mb-0">Dokter *</label>
                             <v-select
                                v-model="form.selected_doctor_id"
                                :options="doctor_list"
                                :reduce="doctor => doctor.value"
                                label="title"
                                placeholder="-- Pilih Dokter --"
                            ></v-select>
                            <!-- <pre>{{ OptionsSoapPlanDoctor }}</pre> -->
                            <!-- <v-select v-model="form.soap_id" :options="OptionsSoapPlanDoctor" :reduce="(OptionsSoapPlanDoctor) => OptionsSoapPlanDoctor.value" label="title" placeholder="-- Dokter --" class="select-size-sm" /> -->

                        </div>
                        <div class="col-lg-4 mt-8" v-if="form.letter_type_id && form.letter_type_id.value == '79b57879-2205-4af9-88e8-d97fb8b14422' || form.letter_type_id.value == '7cbf9ef9-71fa-4d14-9f65-a3021bd411ab'">
                            <label class="fs-13 mb-0">Mulai *</label>
                            <b-form-input size="sm" v-model="form.start" type="date" />
                        </div>
                        <div class="col-lg-4 mt-8" v-if="form.letter_type_id && form.letter_type_id.value == '79b57879-2205-4af9-88e8-d97fb8b14422' || form.letter_type_id.value == '7cbf9ef9-71fa-4d14-9f65-a3021bd411ab'">
                            <label class="fs-13 mb-0">Akhir *</label>
                            <b-form-input size="sm" v-model="form.end" type="date" />
                        </div>
                    </div>
                    <div class="row" v-if="form.letter_type_id && form.letter_type_id.value === 'ed0c34c7-feea-4df1-9ceb-d341877a7306'">
                        <div class="col-lg-6 mt-8">
                            <label class="fs-13 mb-0">Kesimpulan</label>
                            <b-form-textarea size="sm" v-model="form.conclusion" placeholder="Tulis kesimpulan Anda di sini." />
                        </div>
                        <div class="col-lg-6 mt-8">
                            <label class="fs-13 mb-0">Saran </label>
                            <b-form-textarea size="sm" v-model="form.advice" placeholder="Berikan saran bagi pasien." />
                        </div>
                    </div>
                    <div class="row" v-if="form.letter_type_id && form.letter_type_id.is_informed_consent == 1">
                        <div class="col-lg-6 mt-8">
                            <label class="fs-13 mb-0">Peran Penerima Informasi </label>
                            <v-select v-model="form.responsible_id" :options="OptionsResponsible" :reduce="OptionsResponsible => OptionsResponsible.value" required label="title" placeholder="--Pilih Hubungan dengan Pasien --"></v-select>
                        </div>
                        <div class="col-lg-6 mt-8">
                            <label class="fs-13 mb-0">Nama Penerima Informasi</label>
                            <b-form-input v-model="form.responsible_name" :readonly="form.responsible_id === 9" placeholder="Tulis nama penerima informasi di sini."/>
                        </div>
                        <div class="col-lg-6 mt-8">
                            <label class="fs-13 mb-0">Nama Saksi</label>
                            <b-form-input v-model="form.witness_name" placeholder="Tulis nama saksi di sini."/>
                        </div>
                        <div class="col-lg-12 mt-8">
                            <label class="fs-13 mb-0">Dokter Pemberi Informasi</label>
                            <v-select
                                v-model="form.selected_doctor_id"
                                :options="doctor_list"
                                :reduce="doctor => doctor.value"
                                label="title"
                                placeholder="-- Pilih Dokter --"
                            ></v-select>
                        </div>
                    </div>
                </div>
                <div class="card-footer px-12 py-1">
                    <div class="d-flex justify-content-end w-100">
                        <b-button size="sm" @click.prevent="handleSubmit" variant="primary" class="px-24">SIMPAN</b-button>
                    </div>
                </div>
            </div>
            <div class="mt-32">
                <b-table bordered responsive hover small head-variant="light" :fields="fields" :items="items"
                    :per-page="0" :current-page="currentPage" thead-class="text-bold-14"
                    tbody-tr-class="text-regular-12" class="mt-12 text-center">
                    <template #cell(is_delete)="data">
                        <b-badge :variant="status[1][data.value]">
                            {{ status[0][data.value] }}
                        </b-badge>
                    </template>
                    <template #cell(action)="row">
                        <div class="row mx-0">
                            <b-button variant="info" @click="showLetterPrint(row.item.id)" size="sm" class="mr-1" v-if="row.item.is_informed_consent == 1">
                                <feather-icon icon="EyeIcon" />
                            </b-button>
                            <b-button variant="info" @click="showLetterEdit(row.item.id)" size="sm" class="mr-1" v-if="row.item.letter_type_id == 'e9432634-dd6b-41cd-b57d-7c34daba9efc'">
                                <feather-icon icon="EyeIcon" />
                            </b-button>
                            <b-button variant="warning-500" @click="handlePrint(row.item)" size="sm" class="mr-1">
                                <feather-icon icon="PrinterIcon" />
                            </b-button>

                            <b-button variant="danger-500" size="sm">
                                <feather-icon icon="TrashIcon" @click="rowOnDelete(row.item.id)" />
                                <b-link v-b-modal.modal-center>
                                </b-link>
                            </b-button>
                        </div>
                    </template>
                </b-table>
            </div>
        </b-modal>
        <b-modal
            v-model="showLetterPrintModal"
            size="xl"
            hide-footer
            title="Preview Surat"
            body-class="p-0"
            dialog-class="modal-dialog-scrollable"
            content-class="modal-tall"
        >
            <div class="ql-editor">
                <!-- Header logos -->
                <div class="row align-items-center mb-4">
                    <div class="col-2">
                    <img src="../../../../@core/assets/images/logo-thb.jpg" width="200%" alt="rs-logo">
                    </div>
                    <div class="col text-center text-bold-28"></div>
                    <div class="col-2">
                    <!-- <img src="../../../../@core/assets/images/lafki-logo.png" width="85%" alt="lafki-logo" class="float-right"> -->
                    </div>
                    <div class="col-2">
                    <!-- <img src="../../../../@core/assets/images/apexa.png" width="80%" alt="-logo" class="float-right"> -->
                    </div>
                </div>
                <div v-html="letterPrintContent"></div>
                <div class="row">
                    <div class="col-lg-6 mt-8">
                        <label class="font-weight-bold fs-16 mb-0">Informasi Lainnya {{ form.content_plus }}</label>
                         <div v-if="!isEditingAdditionalInfo" @click="isEditingAdditionalInfo = true" class="border p-2 rounded bg-light pointer" style="min-height: 38px;">
                            {{ form.content_plus || 'Klik untuk menambah informasi tambahan...' }}
                        </div>
                        <b-form-textarea v-else v-model="form.content_plus" placeholder="Lengkapi informasi tambahan (opsional)." @keydown.enter.prevent="saveAdditionalInfo"
                        @blur="saveAdditionalInfo" autofocus />
                    </div>
                </div>
                <div class="d-flex flex-row justify-content-center align-items-center mt-16">
                    <div class="col-lg-4">
                        <div class="font-weight-bold mt-2">Tertanda Penerima Informasi</div>
                        <div v-if="!useSavedSignature">
                            <VueSignaturePad id="signature" width="400px" height="200px" ref="signaturePad" style="border: 1px solid black;"
                                :options="{onBegin: () => {$refs.signaturePad.resizeCanvas()}}" />
                            <!-- <div class="d-flex justify-content-center mt-8">
                                <b-button @click="clearSignature" :disabled="disabled_btn" size="sm" variant="outline-primary" class="px-12 mr-2">BERSIHKAN</b-button>
                                <b-button @click="saveSignature" :disabled="disabled_btn" size="sm" variant="primary-500" class="px-24">SIMPAN</b-button>
                            </div>
                            <div class="mt-32 text-center">
                                <div class="fs-14">Gambar Tersimpan : {{ imageVariable ? '' : '-' }}</div>
                                <img v-if="imageVariable" :src="imageVariable" width="400px" height="200px" class="mt-1 p-1" style="border: 1px dashed black;">
                                <div v-if="imageVariable" class="mt-2">
                                    <b-button size="sm" variant="success" @click="useSavedSignature = true">Gunakan TTD</b-button>
                                </div>
                            </div> -->
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="font-weight-bold mt-2">Tertanda Saksi</div>
                        <div v-if="!useSavedSignature">
                            <VueSignaturePad id="signature" width="400px" height="200px" ref="signature2Pad" style="border: 1px solid black;"
                                :options="{onBegin: () => {$refs.signature2Pad.resizeCanvas()}}" />
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="font-weight-bold mt-2">Tertanda Pemberi Informasi</div>
                        <div v-if="!useSavedSignature">
                            <VueSignaturePad id="signature" width="400px" height="200px" ref="signature3Pad" style="border: 1px solid black;"
                                :options="{onBegin: () => {$refs.signature3Pad.resizeCanvas()}}" />
                        </div>
                    </div>
                    <!-- <div v-else class="w-100 text-center"> -->
                    </div>
                    <div class="w-100 text-center">
                        <div class="mt-2 d-flex justify-content-center gap-2">
                            <b-button size="sm" variant="success" @click="saveSignature">
                            Simpan
                            </b-button>
                        </div>
                    </div>
                </div>
        </b-modal>
    </div>
</template>

<script>
import dateMixin from "@/mixins/dateMixin";
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue';
import moment from 'moment';
import { useRouter } from "vue-router";
import { mapState } from 'vuex';

export default {
    mixins: [dateMixin],
    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),
            urlbe:process.env.VUE_APP_IMAGE,
            showModal: false,
            showGenerateNumber: false,
            randomNumber: null,
            active: {
                tab: 'tab-1',
                variant_1: 'warning-500',
                variant_2: 'outline-neutral-500',
                variant_3: 'outline-neutral-500',
            },
            params: {
                registration_destination_id:'',
                offset: 0,
                limit: 20,
                name: '',
                content: ''
            },
            currentPage: 1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            isDisabled: false,
            isDisabledFilter: false,
            show: false,
            dir: false,
            sortBy: '',
            sortDesc: false,
            sortDirection: 'asc',
            filter: null,
            filterOn: [],
            status: [{
                0: 'Active', 1: 'Deleted'
            },
            {
                0: 'primary', 1: 'danger'
            }],
            temp: {
                date: moment(new Date()).format('YYYY-MM-DD'),
                time: moment(new Date()).format('HH:mm'),
            },
            form: {
                id: "",
                registration_destination_id: "",
                letter_type_id: "",
                date: "",
                start: "",
                end: "",
                conclusion: "",
                advice: "",
                responsible_name: "",
                responsible_id: 9,
                witness_name: "",
                selected_doctor_id: "",
                soap_id:"",
                content_plus: "",
                age_pregnant: "",
                deathtime: "",
                receipt_name: "",
                receipt_idcard: "",
                receipt_phone: "",
                receipt_relation: "",
                receipt_address: "",
                destination_of_the_corpse: "",
                receipt_religion: ""
            },
            temp_header: {
                name: '',
                alias_name: '',
                norm: '',
                birth_date: '',
            },
            doctor_list: [],
            showLetterPrintModal: false,
            letterPrintContent: "",
            imageVariable: '',
            disabled_btn: false,
            regData: {},
            reg_des_id: '',
            useSavedSignature: false,
            isEditingAdditionalInfo: false
        }
    },
    computed: {
        ...mapState({
            start: state => state.letter.start,
            totalRows: state => state.letter.totalRows,
            fields: state => state.letter.fields,
            items: state => state.letter.items,
            pageOptions: state => state.letter.pageOptions,
            OptionsSoapPlanDoctor: state => state.soap.options.plandoctorletter,
            OptionsTypeLetter: state => state.lettertype.options.letter,
            OptionsResponsible: state => state.helper.options.responsible
        }),
        sortOptions() {
            // Create an options list from our fields
            return this.fields.filter(f => f.sortable).map(f => ({ text: f.label, value: f.key }))
        },
        dateFormat() {
            return create_at => this.$options.filters.formatDate(create_at)
        },
        defaultDoctorId() {
        const letterType = this.form.letter_type_id;
        // Informed Consent
        if (letterType && letterType.is_informed_consent == 1) {
            const userLogin = localStorage.getItem('userData');
            if (userLogin) {
                const parsed = JSON.parse(userLogin);
                const employeeId = parsed.employee_id;
                const found = this.doctor_list.find(doc => doc.value === employeeId);
                // console.log("found user login", found);
                // console.log("this.form.letter_type_id", this.form.letter_type_id);
                return found ? found.value : '';
                // console.log(this.form.selected_doctor_id);
            }
            // return userLogin ? userLogin.employee_id : '';
        }
        // Surat Keterangan Sakit (Medical Certificate)
        if (letterType && letterType.value === '79b57879-2205-4af9-88e8-d97fb8b14422') {
            // console.log("found regData", this.regData.doctor_in_charge_id);
                // console.log("this.form.letter_type_id", this.form.letter_type_id);
            return this.regData && this.regData.doctor_in_charge_id ? this.regData.doctor_in_charge_id : '';
        }
        return '';
        },
        patientName() {
            return this.temp_header.name || '';
        }
    },
    watch: {
        currentPage: {
            handler: function (value) {
                this.firstRows = this.perPage * (value - 1) + 1
                this.lastRows = this.perPage * (value) + 1
                this.params.offset = this.perPage * (value - 1)
                this.params.limit = this.perPage
                this.params.registration_destination_id = this.$router.history.current.params.id
                this.$store.dispatch('letter/getAll', this.params)
            }
        },
        perPage: {
            handler: function (value) {
                this.params.limit = this.perPage
                this.$store.dispatch('letter/getAll', this.params)
            }
        },
        'form.letter_type_id': {
            handler(newVal, oldVal) {
                if (this.form.letter_type_id && this.form.letter_type_id.is_informed_consent == 1) {
                    this.form.responsible_id = 9;
                    this.form.responsible_name = this.patientName;
                } else {
                    this.form.responsible_id = "";
                }

                if (this.form.id === "") {
                    this.form.start = "";
                    this.form.end = "";
                    this.form.conclusion = "";
                    this.form.advice = "";
                    this.form.soap_id = "";
                    this.form.responsible_name = this.form.responsible_id === 9 ? this.patientName : "";
                    this.form.witness_name = "";

                    this.form.selected_doctor_id = this.defaultDoctorId;

                    this.form.content_plus = ""
                }

            },
            immediate: false
        },
        regData: {
            handler() {
                // When regData changes, also update the default doctor
                this.form.selected_doctor_id = this.defaultDoctorId;
            },
            deep: true
        },
        'form.responsible_id': function(newVal) {
            // console.log('form.responsible_id:', newVal);
            if (newVal == 9) {
                this.form.responsible_name = this.patientName;
            } else {
                this.form.responsible_name = '';
            }
        }
    },

    mounted() {
        this.$store.state.totalRows = this.items.length
        this.$store.dispatch('soap/getSoapDoctorLetterById', this.$router.history.current.params.id)
        this.show = false
        this.params.registration_destination_id = this.$router.history.current.params.id
        this.getData(this.params.registration_destination_id)
        this.getDoctorList()
    },
    created() {
        this.params.registration_destination_id = this.$router.history.current.params.id
        this.$store.dispatch('letter/getAll', this.params)
        this.$store.dispatch('lettertype/getParam', "letter")
        this.show = true
    },
    methods: {
        parentTrigger(message) {
            this.openAdministrationModal(message)
            // console.log('Message:', message);
        },
        openAdministrationModal(obj) {
            this.active.history_modal = true
            this.temp_header.alias_name = obj.alias_name
            this.temp_header.name = obj.name
            this.temp_header.norm = obj.norm
            this.temp_header.birth_date = obj.birth_date
            this.showModal = !this.showModal
        },
        notification(a, b, c, d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d }
            })
        },
        generateNumber() {
            this.showGenerateNumber = true;
            const alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            let result = '';
            // Generate 2 random alphabetic characters
            for (let i = 0; i < 2; i++) {
                result += alphabet.charAt(Math.floor(Math.random() * alphabet.length));
            }
            // Generate 6 random numeric characters
            for (let i = 0; i < 6; i++) {
                result += Math.floor(Math.random() * 10);
            }
            this.randomNumber = result;
        },
        filterData() {
            this.show = true
            this.isDisabledFilter = true
            this.$store.dispatch('letter/getAll', this.params)
                .then(res => {
                    this.show = false
                    this.isDisabledFilter = false
                },
                    err => {
                        this.show = false
                        this.isDisabledFilter = false
                    })
        },
        filterReset() {
            this.params.name = ''
            this.params.offset = 0
            this.params.limit = this.perPage
            this.$store.dispatch('letter/getAll', this.params)
        },
        editData(obj) {
            this.active.main_tab = 'add'
            this.form.id = obj.id
            this.form.name = obj.name
            this.show = false
        },
        async handleSubmit() {
            try {
                this.form.deathtime = `${this.temp.date || moment(new Date()).format('YYYY-MM-DD')} ${this.temp.time || moment(new Date()).format('HH:mm')}`;
                this.form.registration_destination_id = this.$router.history.current.params.id ?? ""
                const formData = {...this.form};
                if (formData.letter_type_id && typeof formData.letter_type_id === 'object') {
                    formData.letter_type_id = formData.letter_type_id.value;
                }
                // console.log('Form data before dispatch:', formData);
                await this.$store.dispatch('letter/formPostPut', formData).then((response) => {
                    console.log('formPostPut response:', response);
                    if (this.form.letter_type_id && this.form.letter_type_id.is_informed_consent == 1) {
                        this.showLetterPrint(response.data.id);
                        return;
                    }
                    this.notification('Success', 'CheckIcon', 'success', 'You have successfully')
                    this.$store.dispatch('letter/getAll', this.params)
                })
            } catch (error) {
                this.notification('Failed', 'CloseIcon', 'danger', 'Something wrong!')
                console.log(error);
            }
        },
        async rowOnDelete(id) {
            try {
                await this.$store.dispatch("letter/remove", id);
                this.notification('Success', 'CheckIcon', 'success', 'You have successfully')
                this.$store.dispatch('letter/getAll', this.params)
            } catch (error) {
                this.notification('Failed', 'CloseIcon', 'danger', 'Something wrong!')
            }
        },
        handlePrint(data) {
            // console.log('handlePrint data:', data);
            // console.log('form.letter_type_id:', this.form.letter_type_id);
            // console.log('letter type id: ', this.form.letter_type_id.value);
            // console.log(data.letter_type_id === '79b57879-2205-4af9-88e8-d97fb8b14422');
            // Surat Keterangan Sakit (Medical Certificate)
            if (data.letter_type_id === '79b57879-2205-4af9-88e8-d97fb8b14422') {
                // console.log('Cetak surat sakit');
                const url = this.$router.resolve({ path: '/prints/medical-certificate/'+ data.registration_destination_id+'/'+ data.id }).href;
                window.open(url, '_blank');
            } else if (data.letter_type_id === '7cbf9ef9-71fa-4d14-9f65-a3021bd411ab') {
                const url = this.$router.resolve({ path: '/prints/skri/'+ data.registration_destination_id+'/'+ data.id }).href;
                window.open(url, '_blank');
            // SPRI
            } else if (data.letter_type_id === '0cf3a866-010a-4937-be09-b3994f9000ac') {
                let url = this.urlbe + data.content
                window.open(url, '_blank')
            } else if (data.letter_type_id === '7fb946dc-3215-4b17-b2da-d0ed0fa50384') {
                const url = this.$router.resolve({ path: '/prints/surat-hak-kewajiban-pasien-keluarga/' + data.registration_destination_id + '/' + data.id }).href;
                window.open(url, '_blank');
            } else if (data.letter_type_id === 'b4d3e86c-a8ae-489c-98c7-94f10aafcb4f') {
                const url = this.$router.resolve({ path: '/prints/surat-pernyataan-bpjs-kesehatan/' + data.registration_destination_id + '/' + data.id }).href;
                window.open(url, '_blank');
            // Surat Kronologi Kejadian
            } else if (data.letter_type_id === 'ecc590cf-5cd8-4521-9218-756affebe4bd') {
                const url = this.$router.resolve({ path: '/prints/surat-kronologi-kejadian/' + data.registration_destination_id + '/' + data.id }).href;
                window.open(url, '_blank');
            } else if (data.letter_type_id === 'c2ed646e-e24a-496a-b333-67ea16e3cab4') {
                const url = this.$router.resolve({ path: '/prints/surat-keterangan-lahir/' + data.nurse_id }).href;
                window.open(url, '_blank');
            // Surat Layak Terbang
            } else if (data.letter_type_id === '035e0b43-a5a9-41ae-a183-00398b4b3ef2') {
                const url = this.$router.resolve({ path: '/prints/flight-certificate/' + data.registration_destination_id + '/' + data.id }).href;
                window.open(url, '_blank');
            // Surat Endoscopy
            } else if (data.letter_type_id === '2290ab0f-50b8-4369-ac00-46eb0d164ba9') {
                const url = this.$router.resolve({ path: '/prints/inform-endoscopy-letter/' + data.registration_destination_id + '/' + data.id }).href;
                window.open(url, '_blank');
            // Surat Kolonoskopi
            } else if (data.letter_type_id === '2d482ae7-132f-4214-94b6-e78c92e1f198') {
                const url = this.$router.resolve({ path: '/prints/inform-kolonoskopi-letter/' + data.registration_destination_id + '/' + data.id }).href;
                window.open(url, '_blank');
            // Surat Ligasi
            } else if (data.letter_type_id === 'd513a0db-3056-4a6a-ad65-a4f65beeb3b8') {
                const url = this.$router.resolve({ path: '/prints/inform-ligasi-letter/' + data.registration_destination_id + '/' + data.id }).href;
                window.open(url, '_blank');
            // Surat Jaminan
            } else if (data.letter_type_id === 'ce83dcf6-dd39-4b93-9429-b5fab3e1d10a') {
                const url = this.$router.resolve({ path: '/prints/guarantee-letter/' + data.registration_destination_id + '/' + data.id }).href;
                window.open(url, '_blank');
            // Surat Kematian & Pengambilan Jenazah
            } else if (data.letter_type_id === 'e9432634-dd6b-41cd-b57d-7c34daba9efc') {
                const url = this.$router.resolve({ path: '/prints/surat-kematian/' + data.registration_destination_id + '/' + data.id }).href;
                window.open(url, '_blank');
            }
            // Surat Keterangan Sehat
            else if (data.letter_type_id === 'ed0c34c7-feea-4df1-9ceb-d341877a7306') {
                const url = this.$router.resolve({ path: '/prints/surat-keterangan-sehat/' + data.registration_destination_id + '/' + data.id }).href;
                window.open(url, '_blank');
            }
            // Surat permintaan naik kelas rawat perawatan
            else if (data.letter_type_id === '') {
                const url = this.$router.resolve({ path: '/prints/surat-permintaan-naik-kelas-rawat-perawatan/' + data.registration_destination_id + '/' + data.id }).href;
                window.open(url, '_blank');
            }
            // Surat Keterangan Lahir
            else if (data.letter_type_id === '') {
                const url = this.$router.resolve({ path: '/prints/surat-keterangan-lahir/' + data.registration_destination_id + '/' + data.id }).href;
                window.open(url, '_blank');
            }
            // Surat Perkiraan Biaya Operasi/Tindakan masih on-going
            else if (data.letter_type_id === '') {
                const url = this.$router.resolve({ path: '/prints/perkiraan-biaya-op-tindakan/' + data.registration_destination_id + '/' + data.id }).href;
                window.open(url, '_blank');
            }
            else {
                // console.log('why am i here');
                // this.$router.push({ name: 'PrintPatientDoctorLetterView', params: { id: data.id } });
                const url = this.$router.resolve({ name: 'PrintPatientDoctorLetterView', params: { id: data.id } }).href;
                window.open(url, '_blank');
            }
        },
        getDoctorList() {
            try {
                this.$store.dispatch('employee/getDoctorList', {}).then((response) => {
                    // console.log('Doctor List Response:', response);
                    this.doctor_list = response;
                }, error => {
                    console.error('Error while GET employee/getDoctorList general at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET employee/getDoctorList general at: ', error);
            }
        },
        // openPreviewLetter(letterId){

        // }
        async showLetterPrint(letterId) {
            try {
                const response = await this.$store.dispatch("letter/getById", letterId);
                // console.log("LetterPrint response", response);
                const data = response[0];
                this.letterPrintContent = data?.content || "<p>No content found.</p>";
                this.showLetterPrintModal = true;


                if (data && data.registration_destination_id) {
                    this.reg_des_id = data.registration_destination_id;
                }

                this.form ={
                    ...this.form,
                    ...data,
                };

                this.form.id = data.id
                this.form.letter_type_id = {value:data.letter_type_id,title:data.letter_type_name,is_informed_consent: 1}

                this.$nextTick(() => {
                    this.$refs.signaturePad.resizeCanvas();
                    this.$refs.signature2Pad.resizeCanvas();
                });
            } catch (error) {
                this.letterPrintContent = "<p>Error loading content.</p>";
                this.showLetterPrintModal = true;
            }
        },
        closeLetterPrintModal() {
            this.showLetterPrintModal = false;
            this.letterPrintContent = "";
        },
        getData(regDesId) {
            this.$store.dispatch('registration/getById', regDesId).then(data => {
                this.regData = data[0]
                // console.log('this.regData:', this.regData);
                const baseUrl = this.regData?.patient_signature ? `${this.urlbe}${this.regData.patient_signature}`: ''

                // nambahin parameter timestamp biar bisa bypass cache nya
                this.imageVariable = baseUrl + (baseUrl ? `?t=${Date.now()}` : '')
                // console.log('imageVariable: ', this.imageVariable);

            })
        },
        clearSignature() {
            this.$refs.signaturePad.clearSignature();
            this.$refs.signature2Pad.clearSignature();
            this.$refs.signature3Pad.clearSignature();
        },

        saveSignature() {
            const { isEmpty: isEmpty1, data: data1 } = this.$refs.signaturePad.saveSignature();
            const { isEmpty: isEmpty2, data: data2 } = this.$refs.signature2Pad.saveSignature();
            const { isEmpty: isEmpty3, data: data3 } = this.$refs.signature3Pad.saveSignature();

            console.log('Pad 1:', isEmpty1, data1);
            console.log('Pad 2:', isEmpty2, data2);
            console.log('Pad 3:', isEmpty3, data3);
            this.disabled_btn = true
            // console.log(isEmpty);
            // console.log(data);

            // this.base64ToImageObject(data)
            // .then(result => {
            //     this.imageVariable = result;
            // })
            // .catch(error => {
            //     console.error('Error:', error);
            //     this.disabled_btn = false
            // });

            this.validationSignature(data1,data2,data3)
            this.$nextTick(() => {
                setTimeout(() => {
                    const modalContent = document.querySelector('.ql-editor');
                    if (modalContent) {
                        modalContent.scrollTop = modalContent.scrollHeight;
                    }
                }, 400);
            });
        },
        validationSignature(data1,data2,data3) {
            let obj = {
                signature_1: data1,
                signature_2: data2,
                signature_3: data3,
                content_plus: this.form.content_plus,
                extension: 'image/png',
                id: this.form.id
            }

            try {
                this.$store.dispatch('letter/putSignature', obj)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.clearSignature()
                        const url = this.$router.resolve({ name: 'PrintPatientDoctorLetterView', params: { id: this.form.id } }).href;
                        window.open(url, '_blank');
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)
            } finally {
                this.disabled_btn = false
            }
        },
        async saveSignatureAndLetter() {
            try {
                this.disabled_btn = true
                const obj = {
                    signature: this.imageVariable,
                    extension: 'image/png',
                    letter_type_id: this.form.letter_type_id.value
                };
                // console.log('obj:', obj);

                const signatureResult = await this.$store.dispatch('letter/putSignature', obj);
                console.log('Signature dispatch result:', signatureResult);

                // this.form.registration_destination_id = this.reg_des_id || this.$router.history.current.params.id || "";
                // const formData = { ...this.form };
                // if (formData.letter_type_id && typeof formData.letter_type_id === 'object') {
                //     formData.letter_type_id = formData.letter_type_id.value;
                // }
                // console.log('formData', formData)
                // const formDataResult = await this.$store.dispatch('letter/formPostPut', formData);
                // console.log('formData dispatch result', formDataResult)

                this.notification('Success', 'CheckIcon', 'success', 'Surat dan tanda tangan berhasil disimpan');
                this.showLetterPrintModal = false;
                this.useSavedSignature = false;
                this.imageVariable = '';
            } catch (error) {
                console.error('Error:', error);
            } finally {
                this.disabled_btn = false
            }
        },
        saveAdditionalInfo(){
            this.isEditingAdditionalInfo = false;
        }


    }
}
</script>

<style>
#signature {
  border: double 3px transparent;
  border-radius: 5px;
  background-image: linear-gradient(white, white),
    radial-gradient(circle at top left, #4bc5e8, #9f6274);
  background-origin: border-box;
  background-clip: content-box, border-box;
}

.modal-tall {
  min-height: 90vh;
  height: 90vh;
  max-height: 95vh;
}

.ql-editor {
  height: 70vh;
  max-height: 75vh;
  overflow-y: auto;
}
</style>