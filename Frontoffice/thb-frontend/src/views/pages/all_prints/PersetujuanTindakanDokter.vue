<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper mt-16">
                <div id="canvas" ref="canvas" style="position: relative;">
                    <b-table-simple class="w-100 sbpk">
                        <b-tr>
                            <b-td colspan="5" class="p-16">
                                <div class="row align-items-center">
                                    <div class="col-5">
                                        <img src="../../../@core/assets/images/logo-thb.jpg" width="240px"
                                            alt="rs-logo"> <br>
                                        <div class="text-regular-10 mt-12">
                                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                                            <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                                        </div>
                                    </div>
                                    <div class="col text-center text-regular-18 mt-20">
                                        <b>
                                            <p>PERSETUJUAN TINDAKAN KEDOKTERAN</p>
                                            <p>(CONSENT TO MEDICAL PROCEDUR)</p>
                                        </b>
                                        <!--  -->
                                    </div>
                                    <div class="col text-center text-bold-24">
                                        <img src="../../../@core/assets/images/lafki-logo.png" width="120px"
                                            alt="lafki-logo">
                                        <img src="../../../@core/assets/images/apexa.png" width="120px"
                                            alt="-logo">
                                    </div>
                                </div>
                            </b-td>
                        </b-tr>
                        <b-tr>
                            <b-td colspan="3" class="p-16">
                                <p class="p-8">Yang bertanda tangan dibawah ini saya:</p>
                                <ol type="1">
                                    <li>
                                        <div class="row">
                                            <div class="col-5">Nama</div>
                                            <div>:</div>
                                            <div class="col">{{ patient.fullname }}</div>
                                        </div>
                                    </li>
                                    <li class="mt-2">
                                        <div class="row">
                                            <div class="col-5">Umur</div>
                                            <div>:</div>
                                            <div class="col">{{ patient.norm }}</div>
                                        </div>
                                    </li>
                                    <li class="mt-2">
                                        <div class="row">
                                            <div class="col-5">Jenis Kelamin</div>
                                            <div>:</div>
                                            <div class="col">{{ patient.birth_date }}</div>
                                        </div>
                                    </li>
                                    <li class="mt-2">
                                        <div class="row">
                                            <div class="col-5">KTP/Identitas</div>
                                            <div>:</div>
                                            <div class="col">{{ patient.sex_name }}</div>
                                        </div>
                                    </li>
                                    <li class="mt-2">
                                        <div class="row">
                                            <div class="col-5">No Telp</div>
                                            <div>:</div>
                                            <div class="col">{{ patient.checkin_date }}</div>
                                        </div>
                                    </li>
                                    <li class="mt-2">
                                        <div class="row">
                                            <div class="col-5">Alamat</div>
                                            <div>:</div>
                                            <div class="col">{{ patient.checkin_date }}</div>
                                        </div>
                                    </li>
                                </ol>
                            </b-td>
                            <b-td colspan="2" class="p-16">
                                <p>Dengan ini menyatakan persetujuan untuk dilakukannya tindakan:....</p>
                                <p>Terhadap Saya/Suami/Istri/Anak/Orang Tua/..........Saya, dengan identitas:</p>
                                <p class="mt-40">Saya memahami perlunya dan manfaat tindakan tersebut seperti yang telah
                                    dijelaskan
                                    kepada saya,termasuk resiko dan komplikasi yang mungkin timbul.</p>
                                <p class="mt-10">Saya juga menyadari bahwa oleh karena itu ilmu kedokteran bukanlah ilmu
                                    pasti,maka
                                    keberhasilan tindakan kedokteran bukanlah keniscayaan,melainkan sangat bergantung
                                    pada Tuhan Yang Maha Esa</p>
                                <!-- <b-checkbox-group disabled>
                                    <b-checkbox class="col-5">GD</b-checkbox>
                                    <b-checkbox class="col-5">Kunjungan Awal</b-checkbox>
                                    <b-checkbox class="col-5">GTD</b-checkbox>
                                    <b-checkbox class="col-5">Kontrol Lanjutan</b-checkbox>
                                    <b-checkbox class="col-5">DTG</b-checkbox>
                                    <b-checkbox class="col-5">Observasi</b-checkbox>
                                    <b-checkbox class="col-5">TGTD</b-checkbox>
                                    <b-checkbox class="col-5">Post Operasi</b-checkbox>
                                </b-checkbox-group> -->
                            </b-td>
                        </b-tr>
                        <b-tr>
                            <b-td colspan="5"></b-td>
                        </b-tr>
                        <b-tr class="text-center text-bold-14">
                            <b-th colspan="2">Perwakilan Dokter (Doctor's Representative)</b-th>
                            <b-th colspan="2">Saksi Perawat / Management (Witnes (Nurse/Management))</b-th>
                            <b-th colspan="2">Pasien / Keluarga (Patien/Family)</b-th>
                        </b-tr>
                        <template>
                            <b-tr>
                                <b-td colspan="2" align="center">Dokter/Doctor
                                    <p class="mt-80">(Nama Jelas/Fullname)</p>
                                    <p class="mt-80">Konsultasi lain/Other Consultant</p>
                                    <p class="mt-80">(Nama Jelas/Fullname)</p>
                                </b-td>
                                <b-td colspan="2" align="center">Saksi/Witness
                                    <p class="mt-80">(Nama Jelas/Fullname)</p>
                                </b-td>
                                <b-td align="center">Bekasi,....20 Waktu/Time

                                    <p class="mt-80">(Nama Jelas/Fullname)</p>
                                    <p class="mt-80">*atau yang mewakili pasien dalam hal pasien tidak mampu mengambil
                                        keputusan sendiri.(Patient Representative/Guardian)</p>
                                    <p class="mt-80">(Nama Jelas/Fullname)</p>
                                </b-td>
                            </b-tr>
                        </template>
                        <b-tr>
                            <b-td colspan="5"></b-td>
                        </b-tr>
                        <!-- <b-tr>
                            <b-td colspan="4"></b-td>
                            <b-td>KODE ICD10</b-td>
                        </b-tr> -->
                        <b-tr>
                            <b-td colspan="4">
                                <div class="font-weight">Masalah yang mungkin timbul selama pemulihan
                                    <small class="font-italic">(possible problems during recovery)</small>
                                </div>
                            </b-td>
                            <b-td class="font-weight-bold">....</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td colspan="4">
                                <div class="font-weight">Akibat yang mungkin terjadi jika tidak dilakukan
                                    tindakan/perawatan<small class="font-italic">(The possible result of non
                                        treatments)</small> </div>
                            </b-td>
                            <b-td class="font-weight-bold">....</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td colspan="4">
                                <div class="font-weight">Tanggapan pasien/keluarga<small
                                        class="font-italic">(Patien/family
                                        response)</small> </div>
                            </b-td>
                            <b-td class="font-weight-bold">....</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td colspan="4">
                                <div class="font-weight">Jika pasien/keluarga telah memahami informasi yang
                                    disampaikan,untuk kolom tanggapan silahkan menyalin kalimat berikut
                                    <small class="font-italic"><b>"(Saya
                                            telah menerima dan memahami sepenuhnya informasi mengenai kondisi
                                            pasien,diagnosis tindakan yang dilakukan,tujuan/indikasi tindakan,tata cara
                                            tindakan yang dilakukan,manfaat dan kekurangan,alternatif
                                            pengobatan,progonis,masalah yang mungkin timbul selama
                                            penyembuhan dan akibat bila tidak dilakukan tindakan seperti yang telah
                                            disampaikan
                                            diatas.)"</b></small>
                                </div>
                            </b-td>
                            <b-td class="font-weight text-center">
                                Bekasi,....20 Waktu/Time
                                <p class="mt-1">Penerima Informasi</p>
                                <p class="mt-80">(.........)</p>
                            </b-td>
                        </b-tr>
                        <b-tr>
                            <b-td colspan="4">
                                <div class="font-weight">Pemberi informasi
                                    <small class="font-italic"><b>"(Saya telah
                                            menerangkan informasi
                                            diatas secara benar dan jelas,dan saya telah memberikan
                                            kesempatan untuk bertanya dan/atau berdiskusi kepada
                                            pasien/keluarga.)"</b></small>
                                </div>
                            </b-td>
                            <b-td class="font-weight text-center">
                                Bekasi,....20 Waktu/Time
                                <p class="mt-1">Penerima Informasi</p>
                                <p class="mt-80">(.........)</p>
                            </b-td>
                        </b-tr>
                        <b-tr v-if="result.soap_a_anythingelse">
                            <b-td colspan="5">{{ result.soap_a_anythingelse }}</b-td>
                        </b-tr>
                    </b-table-simple>
                </div>
            </div>
        </b-container>
    </div>
</template>
<script>
import moment from 'moment';

export default {
    data() {
        return {
            urlbe: process.env.VUE_APP_IMAGE,
            result: {},
            patient: {},
            form: {
                exit_type_id: null
            },
            today: moment(new Date()).format('DD-MM-YYYY')
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
        getExitType() {
            const r_jalan = '3cb626f5-9148-4fb1-8663-3d7f028b01a6'
            this.form.exit_type_id = 0
            if (this.patient.health_facility_id == r_jalan) {
                this.form.exit_type_id = 1
            } else {
                if (this.patient.exit_id == '4a6d28c4-e020-425f-91e1-d39699cf679b') {
                    this.form.exit_type_id = 6
                } else if (this.patient.exit_id == 'a1631acc-6a94-414d-95de-a9faea0f1c2f') {
                    this.form.exit_type_id = 4
                } else {
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
        getData(soap_id) {
            this.$store.dispatch('soap/getBySoapId', soap_id).then(data => {
                this.result = data[0]
                console.log('soap:', this.result)

                this.$store.dispatch('registration/getById', this.result.registration_destination_id).then(data => {
                    this.patient = data[0]
                    // console.log('patient', this.patient);
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
@import './../../../@core/css/main/main.min.css';

@media print {
    @page {
        size: A4;
        margin: 16mm;
    }

    .sbpk table,
    .sbpk th,
    .sbpk td {
        border: 2px solid black;
        border-collapse: collapse;
    }
}

.sbpk table,
.sbpk th,
.sbpk td {
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
