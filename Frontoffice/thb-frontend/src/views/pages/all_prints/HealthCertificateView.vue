<template>
  <div>
    <b-container fluid="xl" class="main-content">
      <div class="d-flex">
        <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
        <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
      </div>
      <div class="canvas-wrapper">
        <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
          <!-- Header logos -->
          <div class="row align-items-center mb-4">
            <div class="col-2">
              <img src="../../../@core/assets/images/logo-thb.jpg" width="200%" alt="rs-logo">
            </div>
            <div class="col text-center text-bold-28"></div>
            <div class="col-2">
              <!-- <img src="../../../@core/assets/images/lafki-logo.png" width="85%" alt="lafki-logo" class="float-right"> -->
            </div>
            <div class="col-2">
              <!-- <img src="../../../@core/assets/images/apexa.png" width="80%" alt="-logo" class="float-right"> -->
            </div>
          </div>
          <!-- Title -->
          <div class="text-uppercase text-center mt-24">
            <div class="text-bold-24 with-line">SURAT KETERANGAN SEHAT</div>
            <div class="text-regular-18">CERTIFICATE OF HEALTH</div>
            <div class="text-regular-18">{{letterData.no}}</div>
          </div>
          <div class="text-regular-18 mt-32" style="line-height: 140%;">
            <div class="underline">Dengan ini menerangkan bahwa:</div>
            <div class="mt-2 italic">This is to certify that:</div>
            <div class="mt-8 underline">Hasil pemeriksaan fisik yang dilakukan tanggal:</div>
            <div class="mt-2 italic">The result of the general physical examination conducted on:</div>
            <!-- Patient Info -->
            <div class="container mt-24">
              <div class="row mb-1 align-items-start">
                <div class="col-4">
                  <span class="underline">Nama</span><br>
                  <span class="italic">Name</span>
                </div>
                <div class="col-auto text-right pr-0" style="width:10px;">:</div>
                <div class="col">{{ regData.fullname }}</div>
              </div>
              <div class="row mb-1 align-items-start">
                <div class="col-4">
                  <span class="underline">Tanggal Lahir</span><br>
                  <span class="italic">Date of Birth</span>
                </div>
                <div class="col-auto text-right pr-0" style="width:10px;">:</div>
                <div class="col">{{ regData.birth_date | convert_date }}</div>
              </div>
              <div class="row mb-1 align-items-start">
                <div class="col-4">
                  <span class="underline">Jenis Kelamin</span><br>
                  <span class="italic">Sex</span>
                </div>
                <div class="col-auto text-right pr-0" style="width:10px;">:</div>
                <div class="col">
                  {{ regData.sex_name }}<br>
                  <i>{{ regData.sex_name === 'Perempuan' ? '(Female)' : regData.sex_name === 'Laki - Laki' ? '(Male)' : regData.sex_name }}</i>
                </div>
              </div>
              <div class="row mb-1 align-items-start">
                <div class="col-4">
                  <span class="underline">Alamat</span><br>
                  <span class="italic">Address</span>
                </div>
                <div class="col-auto text-right pr-0" style="width:10px;">:</div>
                <div class="col">{{ regData.address }}</div>
              </div>
              <div class="row mb-1 align-items-start">
                <div class="col-4">
                  <span class="underline">Pekerjaan</span><br>
                  <span class="italic">Occupation</span>
                </div>
                <div class="col-auto text-right pr-0" style="width:10px;">:</div>
                <div class="col">{{ regData.profession_name }}</div>
              </div>
              <!-- Vital Signs -->
              <div class="row mb-1 align-items-start">
                <div class="col-4">
                  <span class="underline">Tanda-tanda Vital</span><br>
                  <span class="italic">Vital Signs</span>
                </div>
                <div class="col-auto text-right pr-0" style="width:10px;">:</div>
                <div class="col">
                  <div><span class="italic">Blood Pressure</span>/Tekanan Darah: {{ (latestVital.systolic || '-') + '/' + (latestVital.diastolic || '-') + ' mmHg' }}</div>
                  <div><span class="italic">Pulse</span>/Nadi: {{ (latestVital.pulse || '-') + ' x/mnt' }}</div>
                  <div><span class="italic">Respiration Rate</span>/Frekuensi Nafas: {{ (latestVital.respiratory_rate || '-') + ' x/mnt' }}</div>
                  <div><span class="italic">Temperature</span>/Suhu: {{ (latestVital.temperature || '-') + ' ᵒC' }}</div>
                  <div><span class="italic">Height</span>/TB: {{ (latestVital.height || '-') + ' cm' }}</div>
                  <div><span class="italic">Weight</span>/BB: {{ (latestVital.weight || '-') + ' kg' }}</div>
                  <div><span class="italic">Blood Type</span>/Golongan Darah: {{ (regData.blood || '-') + ' ' }}</div>
                </div>
              </div>
            </div>
            <!-- Section: Anamnesa -->
            <div class="mt-2 underline">Menunjukkan keadaan Kesehatan pada saat diperiksa:</div>
            <div class="mt-2 mb-4 italic">Proves the above to be in good physical health</div>
            <div class="row mb-1">
              <div class="col-4 underline">Anamnesa</div>
            </div>
            <div class="row mb-1">
              <div class="col-4 italic">Anamnesis</div>
              <div class="col-auto text-right pr-0" style="width:10px;">:</div>
              <div class="col">{{ form_anamnesa.main_complaint }}</div>
              <div class="col">{{ form_anamnesa.history_disease_now }}</div>
            </div>
            <!-- Section: Diagnosa -->
            <div class="row mt-2 mb-1">
              <div class="col-4 underline">Diagnosa</div>
            </div>
            <div class="row mb-1">
              <div class="col-4 italic">Diagnosis</div>
              <div class="col-auto text-right pr-0" style="width:10px;">:</div>
              <div class="col">{{ form_diagnosis.icd10[0]?.title || '-' }}</div>
            </div>
            <!-- Section: Kesimpulan -->
            <div class="row mt-2 mb-1">
              <div class="col-4 underline">Kesimpulan</div>
            </div>
            <div class="row mb-1">
              <div class="col-4 italic">Conclusion</div>
            </div>
            <div class="row mb-1 ">
              <div class="col ">{{ letterData.conclusion || '-' }}</div>
            </div>
            <!-- Section: Saran -->
            <div class="row mt-2 mb-1">
              <div class="col-4 underline">Saran</div>
            </div>
            <div class="row mb-1">
              <div class="col-4 italic">Advice</div>
            </div>
            <div class="row mb-1">
              <div class="col">{{ letterData.advice || '-' }}</div>
            </div>
            <!-- Signature -->
            <div class="d-flex justify-content-end w-100 mt-64">
              <div class="col-4 text-center">
                <div :class="regData.employee_signature ? '' : 'mb-64'">Bekasi, {{letterData.date | convert_date}}</div>
                <div :class="regData.employee_signature ? '' : 'mb-64'">Dokter Pemeriksa,</div>
                <template v-if="regData.employee_signature">
                  <b-img :src="urlbe+regData.employee_signature" style="width: 120px;"/>
                </template>
                <div>{{ regData.doctor_in_charge_name }}</div>
                <div class="text-left" v-if="regData.sip">SIP : {{ regData.sip }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </b-container>
  </div>
</template>

<script>
import moment from 'moment';
import { mapState } from 'vuex';

export default {
    data() {
        return {
            urlbe:process.env.VUE_APP_IMAGE,
            regData:{},
            soapData:{},
            letterData:{},
            days:1,
            soap_id:'',
            reg_des_id: '',
            vital_list: [],
            form_anamnesa: {
                soap_id: '',
                doctor_in_charge_id: '',
                registration_destination_id: '',
                is_initial_inpatient: 0,
                is_hemodialysis: 0,
                is_medical_rehabilitation: 0,
                anamesa_type_id: '',
                family_relation_id: '',
                family_name: '',
                main_complaint: '',
                history_disease_now: '',
                history_disease_past: '',
                history_drugs: '',
                allergies: []
            },
            form_diagnosis: {
                soap_id: '',
                doctor_in_charge_id: '',
                registration_destination_id: '',
                is_initial_inpatient: 0,
                is_hemodialysis: 0,
                is_medical_rehabilitation: 0,
                icd10: [],
                icd10_notes: '',
                non_icd10: []
            },
            hfi_inpatient: '40f06534-07ec-4221-ad9d-dea08196079f',
            hfi_outpatient: '3cb626f5-9148-4fb1-8663-3d7f028b01a6',
            hfi_emergency: '444b3134-5b75-4b67-af72-a558d46716d7'
        }
    },
    computed: {
        ...mapState({
            items: state => state.soap.items,
        }),
        latestVital(){

            return this.vital_list.length > 0 ? this.vital_list[0] : {};
        }
    },
    filters:{
        convert_date(value) {
            return moment(new Date(value)).locale('id').format('LL')
        },
        convert_date_en(value){
            return moment(new Date(value)).locale('en').format('LL')
        }
    },
    methods: {
        getData (id) {
            this.$store.dispatch('registration/getById', id).then(data => {
                this.regData = data[0]
                this.getAnamnesa(this.regData.registration_destination_id, this.regData.health_facility_id)
                this.getDiagnosis(this.regData.registration_destination_id, this.regData.health_facility_id)

                console.log('regData:', this.regData)
            })
        },
        getVitalList(reg_id) {
            try {
                this.$store.dispatch('vitalsign/getListVitalSign', { registration_destination_id: reg_id }).then((response) => {
                    this.vital_list = response.rows
                    // console.log('vital_list:', this.vital_list)
                    // console.log('latestVital:', this.latestVital)
                }, error => {
                    console.error('Error while GET vitalsign/getListVitalSign at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET vitalsign/getListVitalSign at: ', error);
            }
        },
        geLetter (id) {
			this.$store.dispatch('letter/getById', id).then(data => {
                this.letterData = data[0]
                console.log('Fetched letterData:', this.letterData)
                const dateB = moment(data[0].end);
                const dateC = moment(data[0].start);
                this.days = dateB.diff(dateC, 'days')+1
            })
        },
        handleKeyPress(event) {
            if (event.ctrlKey && event.key === 'p') {
                event.preventDefault();
                this.printCanvas();
            }
        },
        printCanvas() {
            const canvas = this.$refs.canvas;
            const originalContents = document.body.innerHTML;
            document.body.innerHTML = canvas.innerHTML;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        },
        closeWindow() {
            window.close()
        },
        fillFormAnamnesa(obj) {
            if (!obj) return;
            this.form_anamnesa.soap_id = obj.soap_id || '';
            this.form_anamnesa.doctor_in_charge_id = obj.doctor_in_charge_id || '';
            this.form_anamnesa.registration_destination_id = obj.registration_destination_id || '';
            this.form_anamnesa.is_initial_inpatient = obj.is_initial_inpatient || 0;
            this.form_anamnesa.is_hemodialysis = obj.is_hemodialysis || 0;
            this.form_anamnesa.is_medical_rehabilitation = obj.is_medical_rehabilitation || 0;
            this.form_anamnesa.anamesa_type_id = obj.anamesa_type_id || '';
            this.form_anamnesa.family_relation_id = obj.family_relation_id || '';
            this.form_anamnesa.family_name = obj.family_name || '';
            this.form_anamnesa.main_complaint = obj.main_complaint || '';
            this.form_anamnesa.history_disease_now = obj.history_disease_now || '';
            this.form_anamnesa.history_disease_past = obj.history_disease_past || '';
            this.form_anamnesa.history_drugs = obj.history_drugs || '';
            this.form_anamnesa.allergies = Array.isArray(obj.allergies)
                ? obj.allergies.map(a => ({ name: a.name }))
                : [];
        },
        fillFormDiagnosis(obj) {
            this.form_diagnosis.soap_id = obj?.id || ''
            this.form_diagnosis.doctor_in_charge_id = obj?.doctor_in_charge_id || this.regData.doctor_in_charge_id
            this.form_diagnosis.registration_destination_id = obj?.registration_destination_id || this.regData.registration_destination_id
            this.form_diagnosis.is_initial_inpatient = 0
            this.form_diagnosis.is_hemodialysis = this.regData.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e' ? 1 : 0
            this.form_diagnosis.is_medical_rehabilitation = this.regData.health_facility_item_id == '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a' ? 1 : 0
            if (obj.icd10) {
                const mappedIcd10 = obj.icd10.map(item => {
                    return {
                        icd10_id: item.icd10_id,
                        value: item.icd10_id,
                        title: `${item.code} ${item.name}`,
                        action: "action"
                    };
                });
                this.form_diagnosis.icd10 = mappedIcd10;
            } else {
                this.form_diagnosis.icd10 = []
            }
            this.form_diagnosis.icd10_notes = obj?.icd10_notes || ''
            this.form_diagnosis.non_icd10 = obj?.non_icd10 || []
        },
        async resetFormAnamnesa() {
            this.form_anamnesa.doctor_in_charge_id = ''
            this.form_anamnesa.registration_destination_id = ''
            this.form_anamnesa.is_initial_inpatient = 0
            this.form_anamnesa.anamesa_type_id = 1
            this.form_anamnesa.family_relation_id = ''
            this.form_anamnesa.family_name = ''
            this.form_anamnesa.main_complaint = ''
            this.form_anamnesa.history_disease_now = ''
            this.form_anamnesa.history_disease_past = ''
            this.form_anamnesa.history_drugs = ''
            this.form_anamnesa.allergies =  []
        },
        getAnamnesa(reg_des_id, hfi_id) {
            let params = {
                registration_destination_id: reg_des_id
            }
            if (hfi_id === this.hfi_inpatient) {
                // this.reg_des_id = reg_des_id
                params = {
                    registration_destination_id: reg_des_id,
                    // is_booking: hfi_id === this.hfi_outpatient ? 0 : 1,
                    is_initial_inpatient: 1
                }
            } else if (hfi_id === this.hfi_outpatient) {
                // this.reg_des_id = reg_des_id
                params = {
                    registration_destination_id: reg_des_id,
                    is_booking: 0
                    // is_initial_inpatient: 1
                }
            }

            // const outpatient_id = "3cb626f5-9148-4fb1-8663-3d7f028b01a6"
            this.$store.dispatch('soap/getDetail', params).then(async (response) => {
                console.log(response)
                this.fillFormAnamnesa(response[0])
                // if (response.length > 0) {
                //     // await this.fillFormAnamnesa(response[0])
                // }
            })

        },
        getDiagnosis(reg_des_id, hfi_id) {
            let params = {
                registration_destination_id: reg_des_id
            }
            if (hfi_id === this.hfi_inpatient) {
                // this.reg_des_id = reg_des_id
                params = {
                    registration_destination_id: reg_des_id,
                    // is_booking: hfi_id === this.hfi_outpatient ? 0 : 1,
                    is_initial_inpatient: 1
                }
            } else if (hfi_id === this.hfi_outpatient) {
                // this.reg_des_id = reg_des_id
                params = {
                    registration_destination_id: reg_des_id,
                    is_booking: 0
                    // is_initial_inpatient: 1
                }
            }

            // const outpatient_id = "3cb626f5-9148-4fb1-8663-3d7f028b01a6"
            this.$store.dispatch('soap/getDetail', params).then(async (response) => {
                console.log(response)
                this.fillFormDiagnosis(response[0])
                // if (response.length > 0) {
                //     // await this.fillFormAnamnesa(response[0])
                // }
            })

        }

    },
    mounted() {
        this.geLetter(this.$route.params.letter_id)
        this.getVitalList(this.$route.params.id)
        window.addEventListener('keydown', this.handleKeyPress);
    },
    created(){
        this.getData(this.$route.params.id)
        console.log("test")
    },
    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },
};

</script>
<style>
/* @import './../../../../@core/css/main/main.min.css'; */
@import './../../../@core/css/main/main.min.css';


@media print {
    @page {
        size: A4;
        margin: 10mm;
    }
}

td {
    padding: 2px 0;
}

p {
    margin: 0;
    padding: 0;
}

.main-content {
    margin-top: 12px;
    padding-bottom: 32px;
}

.canvas-wrapper {
    display: flex;
    justify-content: center;
}

#canvas {
    /* width: 210mm; */
    width: 21.2cm !important;
    height: 29.7cm !important;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    padding: 40px;
}

.with-line {
    text-decoration: underline;
    text-decoration-thickness: 2px;
    text-underline-offset: 8px;
}
.underline{
    text-decoration: underline;;
}

/* .canvas-header {
    width: 100%;
}

.canvas-header img:first-child {
    width: 80px;
}

.canvas-header img:last-child {
    width: 100%;
    height: 100%;
    display: block;
    margin-top: -30px;
} */

.w-40 {
    width: 40%;
}

.italic{
    font-style: italic;
}

.canvas-footer {
    position: absolute;
    bottom: 0;
    /* right: 0; */
}
table{
    width: 100%;
    table-layout: fixed;
}
.bordered {
  border: 1px solid black;
  border-collapse: collapse;
}
.bordered td{
    border: 1px solid black;
    padding: 4px;
}
/* table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
} */
</style>
