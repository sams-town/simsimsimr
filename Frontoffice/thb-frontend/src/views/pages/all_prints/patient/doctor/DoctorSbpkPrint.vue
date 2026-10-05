<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="generateToImage" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
                <!-- <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button> -->
            </div>
            <div class="canvas-wrapper mt-16">
                <div id="canvas" ref="canvas" style="position: relative;">
                    <b-table-simple class="w-100 sbpk">
                        <b-tr>
                            <b-td colspan="5" class="p-16">
                                <div class="row align-items-center">
                                    <div class="col-5">
                                        <img src="../../../../../@core/assets/images/logo-thb.jpg" width="240px" alt="rs-logo"> <br>
                                        <div class="text-regular-10 mt-12">
                                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                                            <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                                        </div>
                                    </div>
                                    <div class="col text-center text-bold-24">SURAT BUKTI PELAYANAN KESEHATAN (SBPK)</div>
                                </div>
                            </b-td>
                        </b-tr>
                        <b-tr>
                            <b-td colspan="3" class="p-16">
                                <ol type="1">
                                    <li>
                                        <div class="row">
                                            <div class="col-5">Nama Pasien</div>
                                            <div>:</div>
                                            <div class="col">{{ patient.name }}</div>
                                        </div>
                                    </li>
                                    <li class="mt-2">
                                        <div class="row">
                                            <div class="col-5">Nomor RM</div>
                                            <div>:</div>
                                            <div class="col">{{ patient.norm }}</div>
                                        </div>
                                    </li>
                                    <li class="mt-2">
                                        <div class="row">
                                            <div class="col-5">Tanggal Lahir</div>
                                            <div>:</div>
                                            <div class="col">{{ patient.birth_date }}</div>
                                        </div>
                                    </li>
                                    <li class="mt-2">
                                        <div class="row">
                                            <div class="col-5">Jenis Kelamin</div>
                                            <div>:</div>
                                            <div class="col">{{ patient.sex_name }}</div>
                                        </div>
                                    </li>
                                    <li class="mt-2">
                                        <div class="row">
                                            <div class="col-5">Tanggal Masuk</div>
                                            <div>:</div>
                                            <div class="col">{{ patient.checkin_date }}</div>
                                        </div>
                                    </li>
                                </ol>
                            </b-td>
                            <b-td colspan="2" class="p-16">
                                <ol style="list-style-type:square" >
                                    <li>
                                        <div class="row">
                                            <div class="col-5">Kriteria Pasien</div>
                                            <div>:</div>
                                            <div class="col">{{ patient.type_emergency_name }}</div>
                                        </div>
                                    </li>
                                    <li class="mt-2">
                                        <div class="row">
                                            <div class="col-5">Jenis Kunjungan</div>
                                            <div>:</div>
                                            <div class="col">{{ patient.type_visit_name }}</div>
                                        </div>
                                    </li>
                                    <li class="mt-2">
                                        <div class="row">
                                            <div class="col-5">Cara Keluar RS</div>
                                            <div>:</div>
                                            <div class="col">{{ patient.exit_name ? patient.exit_name : '-' }}</div>
                                        </div>
                                    </li>
                                </ol>
                            </b-td>
                        </b-tr>
                        <b-tr>
                            <b-td colspan="5" >
                                <div>
                                    <div class="font-weight-bold" >ANAMNESA :</div>
                                    <div>
                                        <ul type="square" >
                                            <li>
                                                <b>Keluhan Utama: </b>
                                                <div>{{ result.main_complaint }}</div>
                                            </li>
                                            <li class="mt-12">
                                                <b>Riwayat Penyakit Sekarang : </b>
                                                <div v-html="parsedBody(result && result.history_disease_now ? result.history_disease_now : '')"></div>
                                            </li>
                                            <li class="mt-12">
                                                <b>Riwayat Penyakit Dahulu : </b>
                                                <div v-html="parsedBody(result && result.history_disease_past ? result.history_disease_past : '')"></div>
                                            </li>
                                            <li class="mt-12">
                                                <b>Riwayat Obat : </b>
                                                <div v-html="parsedBody(result && result.history_drugs ? result.history_drugs : '')"></div>
                                            </li>
                                            <li class="mt-12">
                                                <b>Systolic: </b>{{ this.result.systolic_blood_pressure ? this.result.systolic_blood_pressure : '-' }} mmHg,
                                                <b>Diastolic: </b>{{ this.result.diastolic_blood_pressure ? this.result.diastolic_blood_pressure : '-' }} mmHg,
                                                <b>Frekuensi Nadi: </b>{{ this.result.pulse_frequency ? this.result.pulse_frequency : '-' }} x/mnt,
                                                <b>Frekuensi Nafas: </b>{{ this.result.breath_frequency ? this.result.breath_frequency : '-' }} x/mnt,
                                                <b>Oksimetri: </b>{{ this.result.oximetry_frequency ? this.result.oximetry_frequency : '-' }} %,
                                                <b>Suhu: </b>{{ this.result.temperature ? this.result.temperature : '-' }} ᵒC,
                                                <b>Oksigen: </b> {{ getOxygenName(result?.oxygen_type_id || '-') }},
                                                <b>Kesadaran: </b> {{ getAwareName(result?.aware) }},
                                                <b>GCS Mata: </b> {{ getGcsEyeName(result?.eye_score || '-') }},
                                                <b>GCS Verbal: </b> {{ getGcsVerbalName(result?.verbal_score || '-') }},
                                                <b>GCS Motorik: </b> {{ getGcsMotoricName(result?.motor_score || '-') }},
                                                <b>GCS Pupil: </b> {{ getGcsPupilName(result?.pupil_score) }},
                                                <!-- <b>Skor Nyeri: </b>{{ this.result.sore_score ? this.result.sore_score : '-' }},
                                                <b>EWS: </b>{{ this.result.ews ? this.result.ews : '-' }},
                                                <b>Gula Darah: </b>{{ this.result.blood_sugar ? this.result.blood_sugar : '-' }} mg/dL, -->
                                            </li>
                                            <li class="mt-12">
                                                <b>Berat Badan: </b>{{ this.result.weight ? this.result.weight : '-' }} kg,
                                                <b>Tinggi Badan: </b>{{ this.result.height ? this.result.height : '-' }} cm,
                                                <b>IMT: </b>{{ this.result.imt ? this.result.imt : '-' }} kg/m²,
                                                <b>Lingkar Kepala: </b>{{ this.result.head_circumference ? this.result.head_circumference : '-' }} cm,
                                                <b>Lingkar Dada: </b>{{ this.result.chest_circumference ? this.result.chest_circumference : '-' }} cm,
                                                <b>Lingkar Lengan Atas: </b>{{ this.result.arm_circumference ? this.result.arm_circumference : '-' }} cm,
                                                <b>Lingkar Perut: </b>{{ this.result.stomach_circumference ? this.result.stomach_circumference : '-' }} cm,
                                                <b>Lingkar Lutut: </b>{{ this.result.knee_circumference ? this.result.knee_circumference : '-' }} cm,
                                                <b>Status Lokalis: </b>{{ this.result.physical_check ? this.result.physical_check : '-' }} cm,
                                                <!-- <b>Lainnya: </b>{{ this.result.soap_o_anythingelse ? this.result.soap_o_anythingelse : '-' }} -->
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </b-td>
                        </b-tr>
                        <b-tr>
                            <b-td colspan="5"></b-td>
                        </b-tr>
                        <b-tr class="text-center text-bold-14">
                            <b-th style="width: 12%;">POLIKLINIK / PENUNJANG</b-th>
                            <b-th>TINDAKAN PEMERIKSAAN PENUNJANG / TETAP</b-th>
                            <b-th>KODE ICD10</b-th>
                            <b-th>HASIL PEMERIKSAAN</b-th>
                            <b-th style="width: 10%;">TDT & NAMA JELAS DOKTER / PETUGAS</b-th>
                        </b-tr>
                        <template v-if="result.laboratory_request_items">
                            <template v-for="(item, index) in this.result.laboratory_request_items">
                                <b-tr>
                                    <b-td>LABORATORIUM</b-td>
                                    <b-td>{{ item.costing_name }}</b-td>
                                    <b-td></b-td>
                                    <b-td></b-td>
                                    <b-td></b-td>
                                </b-tr>
                            </template>
                        </template>
                        <template v-if="result.radiology_request_items">
                            <template v-for="(item, index) in this.result.radiology_request_items">
                                <b-tr>
                                    <b-td>RADIOLOGI</b-td>
                                    <b-td>{{ item.costing_name }}</b-td>
                                    <b-td></b-td>
                                    <b-td></b-td>
                                    <b-td></b-td>
                                </b-tr>
                            </template>
                        </template>
                        <template v-if="result.physiotherapy_request">
                            <template v-for="(item, index) in result.physiotherapy_request">
                                <template v-for="(list, index) in item.physiotherapy_request_items">
                                    <b-tr>
                                        <b-td>FISIOTERAPI</b-td>
                                        <b-td>{{ list.costing_name }}</b-td>
                                        <b-td></b-td>
                                        <b-td></b-td>
                                        <b-td></b-td>
                                    </b-tr>
                                </template>
                            </template>
                        </template>
                        <b-tr>
                            <b-td colspan="5"></b-td>
                        </b-tr>
                        <!-- <b-tr>
                            <b-td colspan="4"></b-td>
                            <b-td>KODE ICD10</b-td>
                        </b-tr> -->
                        <b-tr>
                            <b-td colspan="4" >
                                <div class="font-weight-bold" >DIAGNOSA PRIMER <small>(Diisi hanya boleh 1 diagnosa)</small></div>
                            </b-td>
                            <b-td class="font-weight-bold">KODE ICD10</b-td>
                        </b-tr>
                        <template v-for="(item,index) in result.icd10">
                            <b-tr>
                                <template v-if="index == 0">
                                    <b-td colspan="4">{{ index+1 }}. {{ item.name }}</b-td>
                                    <b-td>{{ item.code }}</b-td>
                                </template>
                                <!-- <template v-else-if="result.icd10.length == 0">
                                    <b-td colspan="4">-</b-td>
                                    <b-td>-</b-td>
                                </template> -->
                            </b-tr>
                        </template>
                        <b-tr>
                            <b-td colspan="4">
                                <div class="font-weight-bold" >DIAGNOSA SEKUNDER <small class="font-italic">(Diisi boleh lebih dari 1 diagnosa)</small> </div>
                            </b-td>
                            <b-td class="font-weight-bold">KODE ICD10</b-td>
                        </b-tr>
                        <template v-for="(item,index) in result.icd10">
                            <b-tr>
                                <template v-if="index != 0">
                                    <b-td colspan="4">{{ index }}. {{ item.name }}</b-td>
                                    <b-td>{{ item.code }}</b-td>
                                </template>
                                <!-- <template v-else>
                                    <b-td colspan="4">-</b-td>
                                    <b-td>-</b-td>
                                </template> -->
                            </b-tr>
                        </template>
                        <b-tr>
                            <b-td colspan="5"></b-td>
                        </b-tr>
                        <!-- here -->
                        <b-tr>
                            <b-td colspan="4">
                                <div class="font-weight-bold">TINDAKAN</div>
                            </b-td>
                            <b-td class="font-weight-bold">KODE ICD9CM</b-td>
                        </b-tr>
                        <template v-for="(item,index) in result.icd9cm">
                            <b-tr>
                                <b-td colspan="4">{{ index+1 }}. {{ item.name }}</b-td>
                                <b-td>{{ item.code }}</b-td>
                            </b-tr>
                        </template>
                        <b-tr>
                            <b-td colspan="5"></b-td>
                        </b-tr>
                        <b-tr>
                            <b-td colspan="5" >
                                <div class="font-weight-bold" >CATATAN ICD9CM</div>
                            </b-td>
                            <!-- <b-td></b-td> -->
                        </b-tr>
                        <template>
                            <b-tr>
                                <b-td colspan="5">{{result.therapy_plan}}</b-td>
                                <!-- <b-td></b-td> -->
                            </b-tr>
                        </template>
                        <b-tr>
                            <b-td colspan="5"></b-td>
                        </b-tr>
                        <b-tr>
                            <b-td colspan="5">
                                <div class="font-weight-bold" >LAINNYA</div>
                            </b-td>
                        </b-tr>
                        <!-- <b-tr v-if="result.soap_a_anythingelse" >
                            <b-td colspan="5">{{ result.soap_a_anythingelse }}</b-td>
                        </b-tr> -->
                        <b-tr>
                            <b-td colspan="5"></b-td>
                        </b-tr>
                        <b-tr>
                            <b-td colspan="5" >
                                <div class="font-weight-bold" >TINDAKAN MEDIS</div>
                            </b-td>
                            <!-- <b-td></b-td> -->
                        </b-tr>
                        <template v-for="(item,index) in result.transaction_doctor">
                            <b-tr >
                                <b-td colspan="5">{{ index+1 }}. {{ item.costing_name }}</b-td>
                                <!-- <b-td></b-td> -->
                            </b-tr>
                        </template>
                        <b-tr>
                            <b-td colspan="5">
                                <div class="d-flex justify-content-between w-100">
                                    <div class="text-center d-flex flex-column align-items-center px-32">
                                        <!-- <div> Bekasi, {{ localDate(patient.checkin_date) }}</div> -->
                                        <div >Pasien / Keluarga Pasien</div>
                                        <template v-if="globalQRText">
                                            <div class="my-2" id="renderQR">
                                                <qrcode-vue id="qrcodeCanvas" :value="globalQRText" :size="100" level="H" ></qrcode-vue>
                                            </div>
                                        </template>
                                        <div>({{ patient.name }})</div>
                                    </div>
                                    <div class="text-center px-32">
                                        <!-- <div> Bekasi, {{ today }}</div> -->
                                        <div> Bekasi, {{ localDate(patient.checkin_date) }}</div>
                                        <div :class="result.employee_signature ? '' : 'mb-64'" >DPJP</div>
                                        <template v-if="result.employee_signature">
                                            <b-img :src="urlbe+result.employee_signature" style="width: 120px;"/>
                                        </template>
                                        <div>({{ result.employee_doctor_name }})</div>
                                    </div>
                                </div>
                                <div class="mt-24 text-uppercase text-semi-12">
                                    <div style="text-decoration: underline;" >catatan:</div>
                                    <ol type="1">
                                        <li>form ini tidak untuk dibawa pulang pasien</li>
                                        <li>jika pasien pulang atas permintaan sendiri maka tidak bisa menggunakan BPJS (pasien bayar sendiri)</li>
                                    </ol>
                                </div>
                            </b-td>
                        </b-tr>
                    </b-table-simple>
                </div>
            </div>
        </b-container>
    </div>
</template>
<script>
import moment from 'moment';
import QrcodeVue from 'qrcode.vue';
export default {
    components: {
        QrcodeVue
    },
    data() {
        return {
            urlbe:process.env.VUE_APP_IMAGE,
            result: {},
            patient: {},
            globalQRText: '',
            form: {
                exit_type_id: null
            },
            today: moment(new Date()).format('DD-MM-YYYY'),

            OptionsOxygen: [
                { value: 2, title: 'Tidak' },
                { value: 1, title: 'Ya' },
            ],
            OptionsAware: [
                { value: 3, title: 'Tidak' },
                { value: 0, title: 'Ya' },
            ],

            OptionGcsEye: [
                { value: 1, title: 'Mata tidak terbuka sama sekali' },
                { value: 2, title: 'Mata hanya terbuka sebagai respons terhadap tekanan' },
                { value: 3, title: 'Hanya membuka mata saat diminta, dan tetap tertutup jika tidak' },
                { value: 4, title: 'Dapat membuka mata secara mandiri' },
            ],
            OptionGcsVerbal: [
                { value: 1, title: 'Tidak bisa berbicara atau membuat suara sama sekali' },
                { value: 2, title: 'Hanya bisa membuat suara, tetapi tidak berbicara' },
                { value: 3, title: 'Bisa berbicara, tetapi respons tidak masuk akal' },
                { value: 4, title: 'Mengalami kebingungan, namun masih bisa menjawab meski kurang paham situasi' },
                { value: 5, title: 'Punya orientasi baik karena mampu menjawab siapa, di mana, serta tanggal atau tahun' },
            ],
            OptionGcsMotoric: [
                { value: 1, title: 'Tidak merespons atau bergerak sama sekali' },
                { value: 2, title: 'Otot meregang (mengulur ke luar) sebagai respons terhadap tekanan' },
                { value: 3, title: 'Otot menegang (menarik ke dalam) saat merasakan tekanan' },
                { value: 4, title: 'Bergerak sebagai refleks terhadap tekanan' },
                { value: 5, title: 'Bergerak menjauh dari tekanan yang dirasakan secara sengaja' },
                { value: 6, title: 'Mampu mengikuti instruksi kapan dan bagaimana harus bergerak' },
            ],
            OptionGcsPupil: [
                { value: 0, title: 'Kedua pupil bereaksi terhadap cahaya' },
                { value: 1, title: 'Satu pupil tidak bereaksi terhadap cahaya' },
                { value: 2, title: 'Kedua pupil tidak bereaksi terhadap cahaya' },
            ],
        }
    },
    mounted() {
        this.getData(this.$route.params.soap_id)
        window.addEventListener('keydown', this.handleKeyPress);
    },
    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },
    methods: {
        getOxygenName(value) {
            const result = this.OptionsOxygen.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        getAwareName(value) {
            const result = this.OptionsAware.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        getGcsEyeName(value) {
            const result = this.OptionGcsEye.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        getGcsVerbalName(value) {
            const result = this.OptionGcsVerbal.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        getGcsMotoricName(value) {
            const result = this.OptionGcsMotoric.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        getGcsPupilName(value) {
            const result = this.OptionGcsPupil.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        saveChangedNurse($event){
            localStorage.setItem("nursestudies", JSON.stringify(this.form_nurse))
        },
        getCode(data) {
            console.log('running');

            let obj = data
            let number_insurance = obj.norm
            if (!obj.registration_insurance) { return obj.norm }

            for (let index = 0; index < obj.registration_insurance.length; index++) {
                const element = obj.registration_insurance[index];

                if (element.is_primary == 0) { continue }
                if (element.insurance_id != 2 && element.company_id != "1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8") { continue }
                // console.log(element);
                number_insurance = element.number_insurance

            }
            console.log(number_insurance);

            this.globalQRText = number_insurance
            this.generateToImage()

        },
        generateToImage() {
            const canvas = document.querySelector("#qrcodeCanvas canvas");
            if (canvas) {
                const dataURL = canvas.toDataURL();
                console.log(dataURL);

                const renderQRDiv = document.getElementById("renderQR");

                const img = document.createElement("img");
                img.src = dataURL;
                img.alt = "Generated QR Code";
                // img.style.marginTop = "-120px";
                canvas.style.display = "none"

                renderQRDiv.appendChild(img);
                setTimeout(() => {
                    this.printCanvas()
                }, 500);
            } else {
                console.error("Canvas not found!");
            }
        },

        parsedBody(text){
            return String(text || '').replace(/\n/g, "<br>");
        },
        localDate(input_date) {
            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },
        getExitType(){
            const r_jalan = '3cb626f5-9148-4fb1-8663-3d7f028b01a6'
            this.form.exit_type_id = 0
            if (this.patient.health_facility_id == r_jalan) {
                this.form.exit_type_id = 1
            } else {
                if (this.patient.exit_id == '4a6d28c4-e020-425f-91e1-d39699cf679b') {
                    this.form.exit_type_id = 6
                } else if (this.patient.exit_id == 'a1631acc-6a94-414d-95de-a9faea0f1c2f'){
                    this.form.exit_type_id = 4
                } else{
                    this.form.exit_type_id = 0 // sementara, krn exit_id nya null semua
                }
                // exit:[
                //     { value: '807ca005-5827-4e19-9c97-0bc3d4e924ca', text: 'Atas Persetujuan Dokter' },
                //     { value: '4a6d28c4-e020-425f-91e1-d39699cf679b', text: 'Meninggal' },
                //     { value: 'a1631acc-6a94-414d-95de-a9faea0f1c2f', text: 'Dirujuk' },
                //     { value: 'ae0aabfa-1600-443f-8a60-62f9e76b1608', text: 'Permintaan Sendiri' }
                // ],
            }
        },
        handleKeyPress(event) {
            if (event.ctrlKey && event.key === 'p') {
                event.preventDefault();
                this.printCanvas();
            }
        },
        closeWindow() {
            window.close()
        },
        printCanvas() {
            const canvas = this.$refs.canvas;
            const originalContents = document.body.innerHTML;
            document.body.innerHTML = canvas.innerHTML;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload()
        },
        getData (soap_id) {
            this.$store.dispatch('soap/getBySoapId', soap_id).then(data => {
                this.result = data[0]
                // console.log('soap:', this.result)

                this.$store.dispatch('registration/getById', this.result.registration_destination_id).then(data => {
                    this.patient = data[0]
                    this.getCode(this.patient)
                    // console.log(result.toDataURL("image/png"));
                })
            })
        },
         // getFamilyRelation(id) {
        //     let data = ''
        //     if (id == 1) {
        //         return data = 'Ayah'

        //     } else if (id == 2) {
        //         return data = 'Ibu'

        //     } else if (id == 3) {
        //         return data = 'Kakak'

        //     } else if (id == 4) {
        //         return data = 'Adik'

        //     } else {
        //         return data = '-'

        //     }
        // },
    },

};

</script>
<style>
@import './../../../../../@core/css/main/main.min.css';

@media print {
    @page {
        size: A4;
        margin: 16mm;
    }

    .sbpk table, .sbpk th, .sbpk td {
        border: 2px solid black;
        border-collapse: collapse;
    }
}
.sbpk table, .sbpk th, .sbpk td {
    /* border: 0; */
  border: 2px solid black;
  border-collapse: collapse;
}
td {
    padding: 2px 0;
}

p {
    margin: 0;
    padding: 0;
}

.main-content {
    margin-top: 32px;
    padding-bottom: 32px;
}

.canvas-wrapper {
    display: flex;
    justify-content: center;
    padding-bottom: 40px;
}
/* #canvas {
    width: 21.2cm !important;
    height: 100% !important;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    padding: 40px;
} */

.with-line {
    text-decoration: underline;
    text-decoration-thickness: 2px;
    text-underline-offset: 8px;
}

.w-40 {
    width: 40%;
}

.canvas-footer {
    position: absolute;
    bottom: 0;
}
</style>
