<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <div class="row align-items-center">
                        <div class="col-2">
                            <img src="../../../@core/assets/images/logo-thb.jpg" width="300%" alt="rs-logo">
                            <br>
                        </div>
                        <div class="col text-center text-bold-28">
                            <!-- <p class="text-bold-14">RS Taman Harapan Baru</p>
                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                            <p>Info: +62 778 4161212 / 0853 7111 1212</p>
                             -->
                        </div>
                        <div class="col-2">
                            <tr>
                                <td><img src="../../../@core/assets/images/lafki-logo.png" width="85%" alt="lafki-logo"
                                        align="right">
                                </td>
                            </tr>
                        </div>
                        <div class="col-2">
                            <tr>
                                <td><img src="../../../@core/assets/images/apexa.png" width="80%" alt="-logo"
                                        align="right">
                                </td>
                            </tr>
                        </div>
                    </div>
                    <div class="text-uppercase text-center mt-5">
                        <div class="text-bold-24 with-line">SURAT KETERANGAN PEMERIKSAAN NAPZA</div>
                        <div class="text-regular-24">Nomor: {{ letterData?.no }}</div>
                    </div>
                    <div class="text-regular-18 mt-32" style="line-height: 140%;">
                        <div>Yang bertandatangan di bawah ini,</div>
                        <table class="mt-2 ml-3">
                            <tr>
                                <td style="width: 200px;" class="align-top pl-0">Nama</td>
                                <td class="px-12 text-center">:</td>
                                <td class="">dr. Fitta Deskawaty, Sp. KJ</td>
                            </tr>
                            <tr>
                                <td class="align-top pl-0">SIP</td>
                                <td class="px-12 text-center">:</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="align-top pl-0">Jabatan</td>
                                <td class="px-12 text-center">:</td>
                                <td>Dokter Spesialis Kesehatan Jiwa</td>
                            </tr>
                            <tr>
                                <td class="align-top pl-0">Instansi</td>
                                <td class="px-12 text-center">:</td>
                                <td>Rs. Taman Harapan Baru Bekasi</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="align-top pl-0">Telah melakukan pemeriksaan psikiatri pada napza terhadap : </td>
                            </tr>
                            <tr>
                                <td class="align-top pl-0">Nama Lengkap</td>
                                <td class="px-12 text-center">:</td>
                                <td>{{ regData.name }}</td>
                            </tr>
                            <tr>
                                <td class="align-top pl-0">Jenis Kelamin</td>
                                <td class="px-12 text-center">:</td>
                                <td>{{ regData.sex_name }}</td>
                            </tr>
                            <tr>
                                <td class="align-top pl-0">Tempat/Tanggal Lahir</td>
                                <td class="px-12 text-center">:</td>
                                <td>{{regData.birth_place}}/{{regData.birth_date}}</td>
                            </tr>
                            <tr>
                                <td class="align-top pl-0">Pendidikan</td>
                                <td class="px-12 text-center">:</td>
                                <td>{{ getlastEdu(regData.last_edu_id) }}</td>
                            </tr>
                            <tr>
                                <td class="align-top pl-0">Status Perkawinan</td>
                                <td class="px-12 text-center">:</td>
                                <td>{{ getMarried(regData.married_id) }}</td>
                            </tr>
                            <tr>
                                <td class="align-top pl-0">Pekerjaan</td>
                                <td class="px-12 text-center">:</td>
                                <td>{{ getProffesion(regData.profession_id) }}</td>
                            </tr>
                            <tr>
                                <td class="align-top pl-0">Alamat</td>
                                <td class="px-12 text-center">:</td>
                                <td>{{ regData.address }}</td>
                            </tr>
                        </table>
                        <div class="mt-4">Dan Berdasarkan pemeriksaan : </div>
                        <table class="ml-3">
                            <tr>
                                <td style="width: 200px;" class="align-top pl-0">Fisik Diagnostik</td>
                                <td class="px-12 text-center">:</td>
                                <td class="px-12 text-center">Pada Tanggal ,,,,,,,,,,,,,,,,,...</td>
                                <td>Jam ...............</td>
                            </tr>
                            <tr>
                                <td class="align-top pl-0">Psikiatri</td>
                                <td class="px-12 text-center">:</td>
                                <td class="px-12 text-center">Pada Tanggal ,,,,,,,,,,,,,,,,,...</td>
                                <td>Jam ...............</td>
                            </tr>
                            <tr>
                                <td class="align-top pl-0">Pemeriksaan Tambahan</td>
                                <td class="px-12 text-center">:</td>
                                <td class="px-12 text-center">Pada Tanggal ,,,,,,,,,,,,,,,,,...</td>
                                <td>Jam ...............</td>
                            </tr>
                        </table>
                        <div class="mt-4">
                            Menunjukkan <strong>Ada/ Tidak Ada</strong> gejala-gejala penggunaan Narkotika/ Zat Psikoaktif
                            Surat keterangan Pemeriksaan Napza ini digunakan untuk keperluan :
                        </div>
                        <div class="mt-4 text-center">
                            <div class="text-bold-24 with-line">
                                "
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                "
                            </div>
                        </div>
                        <div class="mt-4">Demikian surat keterangan ini dibuat agar dapat dipergunakan sebagai mana mestinya.</div>
                        <div class="mt-4">
                            <i>NB : Hasil Pemeriksaan dan Laboratorium Terlampir</i>
                        </div>
                        <div style="margin-top: 700px;">
                            <div>
                                <i>Lampiran :</i>
                            </div>
                            <div class="mt-2 ml-3">
                                <div>a. Pemeriksaan Fisik</div>
                                <table class="mt-2 ml-3">
                                    <tr>
                                        <td style="width: 200px;" class="align-top pl-0">Tanda Vital</td>
                                        <td class="px-12 text-center">:</td>
                                        <td class="">TD: {{ vitalData.systolic }}/{{ vitalData.diastolic }} mmHg</td>
                                        <td class="">N: {{ vitalData.pulse }} x/i</td>
                                    </tr>
                                    <tr>
                                        <td colspan="1"></td>
                                        <td class="px-12 text-center">:</td>
                                        <td class="">RR: {{ vitalData.respiratory_rate }} x/i</td>
                                        <td class="">T: {{ vitalData.temperature }} &deg;C</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top pl-0">Penampilan</td>
                                        <td class="px-12 text-center">:</td>
                                        <td class="">Rapi / Tidak Rapi</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top pl-0">Cara Berjalan</td>
                                        <td class="px-12 text-center">:</td>
                                        <td class="">Biasa / Ada Gangguan..............</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top pl-0">Cara Bicara</td>
                                        <td class="px-12 text-center">:</td>
                                        <td class="">Biasa / Ada Gangguan..............</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top pl-0">Konjungtiva</td>
                                        <td class="px-12 text-center">:</td>
                                        <td class="">Normal / Tidak Normal</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top pl-0">Bekas suntikan/jejas</td>
                                        <td class="px-12 text-center">:</td>
                                        <td class="">Ada / Tidak Ada</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top pl-0">Tremor</td>
                                        <td class="px-12 text-center">:</td>
                                        <td class="">Ada / Tidak Ada</td>
                                    </tr>
                                </table>
                                <div class="mt-4">b. Pemeriksaan Psikiatrik</div>
                                <table class="mt-2 ml-3">
                                    <tr>
                                        <td style="width: 200px;" class="align-top pl-0">Alur pembicaraan</td>
                                        <td class="px-12 text-center">:</td>
                                        <td>Normal / Tidak Normal</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top pl-0">Waham</td>
                                        <td class="px-12 text-center">:</td>
                                        <td>Ada / Tidak Ada</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top pl-0">Halusinasi</td>
                                        <td class="px-12 text-center">:</td>
                                        <td>Ada / Tidak Ada</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top pl-0">Austistik</td>
                                        <td class="px-12 text-center">:</td>
                                        <td>Ada / Tidak Ada</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top pl-0">Visual</td>
                                        <td class="px-12 text-center">:</td>
                                        <td>Ada / Tidak Ada</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top pl-0">Lainnya</td>
                                        <td class="px-12 text-center">:</td>
                                        <td>Ada / Tidak Ada</td>
                                    </tr>
                                </table>

                                <div class="mt-4">c. Pemeriksaan Tambahan</div>
                                <table class="mt-2 ml-3">
                                    <tr>
                                        <td style="width: 200px;" class="align-top pl-0">Morphine (MOP)</td>
                                        <td class="px-12 text-center">:</td>
                                        <td>Negatif / Positif</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top pl-0">Ampethamine (AMP)</td>
                                        <td class="px-12 text-center">:</td>
                                        <td>Negatif / Positif</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top pl-0">THC (Mariyuana)</td>
                                        <td class="px-12 text-center">:</td>
                                        <td>Negatif / Positif</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top pl-0">Methampetamine (MET)</td>
                                        <td class="px-12 text-center">:</td>
                                        <td>Negatif / Positif</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top pl-0">Cocaine (COC)</td>
                                        <td class="px-12 text-center">:</td>
                                        <td>Negatif / Positif</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top pl-0">Benzodiazephine</td>
                                        <td class="px-12 text-center">:</td>
                                        <td>Negatif / Positif</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top pl-0">Soma</td>
                                        <td class="px-12 text-center">:</td>
                                        <td>Negatif / Positif</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end w-100 mt-80">
                                <div>
                                    <div>Bekasi, {{ regData.date_added | convert_dateStr}}</div>
                                    <div class="text-center" :class="regData?.doctor_signature ? '' : 'mb-64'">Dokter Pemeriksa</div>
                                    <!-- <div :class="regData.doctor_signature ? '' : 'mb-64'">Nama Jelas Dan Tanda Tangan
                                    </div> -->
                                    <!-- <template v-if="regData?.doctor_signature">
                                        <b-img class="mx-auto d-block" :src="urlbe + regData?.doctor_signature" style="width: 120px;" />
                                    </template> -->
                                    <div class="text-center"><u>{{ regData?.doctor_name }}</u></div>
                                    <div>21/SIPDSS/DPMPTSP-BTM/01/III/2024</div> <!-- No SIP -->
                                </div>
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
            urlbe: process.env.VUE_APP_IMAGE,
            regData: {},
            soapData: {},
            letterData: {},
            vitalData:{},
            days: 1,
            soap_id: '',
            id: '',
            params: {
                offset: 0,
                limit: 20,
                registration_destination_id: ''
            },
        }
    },
    computed: {
        ...mapState({
            items: state => state.soap.items,
        })
    },
    filters: {
        convert_date(value) {
            return moment(new Date(value)).locale('id').format('LL')
        },
        convert_time(value) {
            return moment(new Date(value)).locale('id').format('LT')
        },
        convert_dateStr(value) {
            if(value) return moment(value).locale('id').format('LL')
        },
        convert_timeStr(value) {
            if(value) return moment(value, 'HH:mm:ss').locale('id').format('HH.mm');
        },
    },
    methods: {
        getSoap(id) {
            this.$store.dispatch('soap/getBySoapId', id).then(data => {
                if(data.length > 0){
                    this.getData(data[0].registration_destination_id)
                    this.soapData = data[0]
                    // console.log(data[0])
                }
            })
        },
        geLetter(id) {
            this.$store.dispatch('letter/getById', id).then(data => {
                this.letterData = data[0]
                // const dateB = moment(data[0].end);
                // const dateC = moment(data[0].start);
                // this.days = dateB.diff(dateC, 'days') + 1
            })
        },
        getVitalList() {
            this.params.registration_destination_id = this.regData.registration_destination_id

            try {
                this.$store.dispatch('vitalsign/getListVitalSign', this.params).then((response) => {
                    if(response?.rows.length > 0){
                        this.vitalData = response.rows[0]
                    }
                    // this.totalRows = response.iTotal
                    // console.log('this.vital_list: ', this.vitalData);
                }, error => {
                    console.error('Error while GET vitalsign/getListVitalSign at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET vitalsign/getListVitalSign at: ', error);
            }
        },
        getData (id) {
            this.$store.dispatch('registration/getById', id).then(data => {
                this.regData = data[0]
                this.getVitalList()
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

        //Helper Method
        parsed_age(input_date) {
            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const currentDate = moment();

            const years = currentDate.diff(birthDate, 'years');
            birthDate.add(years, 'years');
            const months = currentDate.diff(birthDate, 'months');
            birthDate.add(months, 'months');
            const days = currentDate.diff(birthDate, 'days');

            // return `${years} th ${months} bln ${days} hr`;
            return `${years} tahun`;
        },
        getMarried(code) {
            const marrieds = {
                '1': 'Lajang',
                '2': 'Kawin',
                '3': 'Janda',
                '4': 'Duda',
            };
            return marrieds[code] || '-';
        },
        getProffesion(code) {
            const proffesions = {
                '1': 'Pegawai Negeri',
                '2': 'Pegawai Swasta',
                '3': 'Pekerja Lepas',
                '4': 'Pelajar',
                '5': 'Mahasiswa',
                '6': 'Ibu Rumah Tangga',
                '7': 'TNI / POLRI',
                '8': 'Pensiunan',
                '9': 'Tidak Bekerja',
                '10': 'Lain Lain',
                '11': 'BUMN',
                '12': 'Buruh',
            };
            return proffesions[code] || '-';
        },
        getlastEdu(code) {
            const proffesions = {
                '1': 'Sekolah Dasar',
                '2': 'Sekolah Menengah Pertama',
                '3': 'Sekolah Menengah Atas',
                '4': 'Diploma 1',
                '5': 'Diploma 3',
                '6': 'Sarjana',
                '7': 'Magister',
                '8': 'Doktor',
                '9': 'Spesialist',
                '10': 'Sastra 4',
            };
            return proffesions[code] || '-';
        },
    },
    mounted() {
        if (this.$route.params.soap_id) {
            this.getSoap(this.$route.params.soap_id)
        }
        // this.geLetter(this.$route.params.letter_id)
        window.addEventListener('keydown', this.handleKeyPress);
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
        margin: 16mm;
    }

    .spnkp table,
    .spnkp th,
    .spnkp td {
        border: 2px solid black;
        border-collapse: collapse;
    }
}

.spnkp table,
.spnkp th,
.spnkp td {
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
}

#canvas {
    /* width: 210mm; */
    width: 21.2cm !important;
    /* height: 29.7cm !important; */
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    padding: 40px;
}

.with-line {
    text-decoration: underline;
    text-decoration-thickness: 2px;
    text-underline-offset: 8px;
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

.canvas-footer {
    position: absolute;
    bottom: 0;
    /* right: 0; */
}
</style>
