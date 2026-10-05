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
                        <div class="text-bold-24 with-line">SURAT PERNYATAAN BPJS KESEHATAN</div>
                        <div class="text-regular-24">{{ letterData.no }}</div>
                    </div>
                    <div class="text-regular-18 mt-32" style="line-height: 140%;">
                        <!-- <div>Yang bertanda tangan dibawah ini:.., menerangkan dengan sesungguhnya,bahwa telah lahir
                            seorang bayi dengan jenis kelamin:.. <span>{{
                                regData.doctor_in_charge_name
                                }}</span>
                        </div> -->
                        <div class="mt-5"><b>Saya bertanda tangan dibawah ini</b>:</div>
                        <table class="mt-16">
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Nama</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.responsible_person_name }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Alamat</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.responsible_person_address }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">No HP</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.responsible_person_phone }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Pekerjaan</td>
                                <td class="col-1 text-right">:</td>
                                <!-- field nya tidak ada pada saat pendaftaran pasien -->
                                <td>{{ }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">No. Kartu Identitas</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.responsible_person_idcard }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Hubungan Dengan Pasien</td>
                                <td class="col-1 text-right">:</td>
                                <td>
                            <tr>
                                <td><span>{{ regData.responsible_person_responsible_name }}</span></td>
                            </tr>
                            </td>
                            </tr>
                        </table>
                        <div class="mt-5"><b>Dengan ini menyatakan bahwa</b>:</div>
                        <table class="mt-16">
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Nama Pasien</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.fullname }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Tempat/Tanggal Lahir</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.birth_place}}, {{ convert_date(regData.birth_date) }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">No Rekam Medis</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.norm }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Pekerjaan</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.profession_name }}</td>
                            </tr>
                        </table>
                        <div class="mt-5"><b>Saya menyatakan dengan ini bahwa</b>:</div>
                        <div class="mt-16 row-cols-1">1. Apakah pasien punya kartu BPJS?
                            <span><b>{{ regData.company_id == company_bpjs || regData.company_id == company_bpjstk ?
                                    'Ya' :
                                    'Tidak' }}</b></span>
                        </div>
                        <div>
                            (atas permintaan sendiri(keluarga)) tidak ingin menggunakan kartu BPJS-Kesehatan(JKN-KIS)
                            pasien / keluarga menyatakan sebagai pasien umum / perusahaan / Asuransi dan sudah
                            mendapatkan penjelasan mengenai penjaminan menggunakan JKN-KIS tidak ada perbedaan dengan
                            jaminan umum maupun jaminan lainnya,apabila telah menyatakan sebagai pasien umum /
                            perusahaan / Asuransi selama episode rawatan inti tidak dapat beralih menjadi pasien BPJS -
                            Kesehatan (JKN-KIS)
                        </div>
                        <div class="mt-16 row-cols-1">2. Jika ada kartu BPJS - Kesehatan(JKN-KIS) namun tidak membawa
                            diberi waktu 2x24 jam hari kerja yaitu selambat - lambatnya: Hari {{
                            convert_date(regData.birth_date, 'dddd', 2) }},tgl {{ convert_date(regData.birth_date, 'L',
                            2) }} Untuk
                            menyerahkan identitas sebagai peserta BPJS - Kesehatan (JKN-KIS)
                        </div>
                        <div class="mt-16 row-cols-1">3. Jika ada pasien Kartu BPJS - Kesehatan (JKN-KIS) namun belum
                            aktif diberi waktu 2x24 jam hari kerja (Rawat Inap) & pada tanggal pelayanan yang sama(Rawat
                            Jalan),yaitu selambat-lambatnya : Hari {{ convert_date(regData.birth_date, 'dddd', 2) }},tgl
                            {{ convert_date(regData.birth_date, 'L', 2) }} Untuk menyerahkan identitas
                            sebagai peserta BPJS - Kesehatan (JKN-KIS)
                        </div>
                        <div class="mt-16 row-cols-1">4. Pasien kecelakaan lalu lintas (KKL),wajib mengurus surat JASA
                            RAHARJA dalam waktu 2x24 jam hari kerja,yaitu selambat-lambatnya: Hari {{
                            convert_date(regData.birth_date, 'dddd', 2) }},tgl {{ convert_date(regData.birth_date, 'L',
                            2) }}
                            Jam {{ convert_date(regData.birth_date, 'LT', 2) }}
                        </div>
                        <div class="mt-16 row-cols-1">5. Jika pasien menunggak iuran BPJS - KESEHATAN (JKN-KIS) wajib
                            melampirkan bukti pembayaran iuran dan bukti pembayaran denda pelayanan rawat inap dalam
                            waktu 2x24 jam hari kerja,yaitu selambat-lambatnya: Hari {{
                            convert_date(regData.birth_date, 'dddd', 2) }},tgl {{ convert_date(regData.birth_date, 'L',
                            2) }}
                            Jam {{ convert_date(regData.birth_date, 'LT', 2) }}
                        </div>
                        <div class="mt-16 row-cols-1">6. Pasien BPJS- Kesehatan yang masuk melalui IGD,harus sesuai
                            dengan kriteria Emergensi Rumah Sakit. Setelah penilaian dokter IGD tidak termasuk dalam
                            kriteria Emergensi,maka pasien tidak dapat menggunakan jaminan BPJS - Kesehatan (JKN-KIS)
                            dan berlaku sebagai pasien umum
                        </div>

                        <div class="mt-5">Apabila saya tidak melengkapi persyaratan BPJS Kesehatan,saya bersedia
                            membayar seluruh biaya perawatan dari awal pasien masuk hingga pasien dirawat inap sebagai
                            pasien <b>umum</b> sesuai dengan ketentuan RS Taman Harapan Baru Bekasi. Demikian surat
                            pernyataan ini saya buat untuk dapat dipergunakan sebaik-baiknya.</div>
                        <div class="d-flex justify-content-end w-100 mt-64">
                            <div class="col-10 text-center">
                                <div>Bekasi, {{convert_date(regData.birth_date, 'L') }}
                                    Jam {{ convert_date(regData.birth_date, 'LT') }}</div>
                                <div class="mb-120">Yang Menyatakan</div>
                                {{ ('(.................................................)') }}
                                <div class="mb-120">tanda tangan
                                    <div>dan nama jelas
                                        pasien/keluarga</div>
                                </div>
                                <!-- <template v-if="soapData.employee_signature">
                                    <b-img :src="urlbe + soapData.employee_signature" style="width: 120px;" />
                                </template> -->
                                <!-- <div>{{ soapData.employee_doctor_name }}</div> -->
                            </div>
                            <div class="col-5 text-center">
                                <br>
                                <div class="mb-120">Petugas administrasi</div>
                                <!-- <div> {{ ('Materi 10000') }}</div> -->
                                {{ ('(.................................................)') }}
                                <div class="mb-120">tanda dan nama jelas
                                </div>
                                <!-- <template v-if="soapData.employee_signature">
                                    <b-img :src="urlbe + soapData.employee_signature" style="width: 120px;" />
                                </template> -->
                                <!-- <div>{{ regData.doctor_in_charge_name }}</div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </b-container>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import moment from 'moment'

export default {
    data() {
        return {
            urlbe: process.env.VUE_APP_IMAGE,
            regData: {},
            soapData: {},
            letterData: {},
            days: 1,
            soap_id: '',
            company_bpjs:'1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8',
            company_bpjstk:'bb0d633f-99e1-4990-901b-0417a14e3cb6'
        }
    },
    computed: {
        ...mapState({
            items: state => state.soap.items,
        })
    },
    filters: {
        // convert_date(value) {
        //     return moment(new Date(value)).locale('id').format('LL')
        // }
    },
    methods: {
        getData(id) {
            this.$store.dispatch('registration/getById', id).then(data => {
                this.regData = data[0]
            })
        },
        getSoap(id) {
            this.$store.dispatch('soap/getBySoapId', id).then(data => {
                this.soapData = data[0]
            })
        },
        geLetter(id) {
            this.$store.dispatch('letter/getById', id).then(data => {
                this.letterData = data[0]
                const dateB = moment(data[0].end);
                const dateC = moment(data[0].start);
                this.days = dateB.diff(dateC, 'days') + 1
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
        convert_date(value, format = 'LL', addedDays = 0) {
            if(addedDays !== 0){
                return moment(new Date(value)).locale('id').add(addedDays, 'days').format(format);
            } else {
                return moment(new Date(value)).locale('id').format(format);
            }
        }
    },
    mounted() {
        this.getData(this.$route.params.id)
        this.getSoap(this.$route.params.soap_id)
        this.geLetter(this.$route.params.letter_id)
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
