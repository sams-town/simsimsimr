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
                            <img src="@/@core/assets/images/logo-thb.jpg" width="300%" alt="rs-logo">
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
                                <td>
                                    <!-- <img src="@/@core/assets/images/lafki-logo.png" width="85%" alt="lafki-logo"
                                        align="right"> -->
                                </td>
                            </tr>
                        </div>
                        <div class="col-2">
                            <tr>
                                <td>
                                    <!-- <img src="@/@core/assets/images/apexa.png" width="80%" alt="-logo"
                                        align="right"> -->
                                </td>
                            </tr>
                        </div>
                    </div>
                    <div class="text-uppercase text-center mt-5">
                        <div class="text-bold-24 with-line">PERSETUJUAN UMUM / GENERAL CONSENT</div>
                        <!-- <div class="text-regular-24">{{ letterData.no }}</div> -->
                    </div>
                    <div class="text-regular-15 mt-32" style="line-height: 140%;">
                        <div class="mt-16"><b>1. HAK DAN KEWAJIBAN SEBAGAI PASIEN.</b> Saya mengakui bahwa dalam proses pendaftaran untuk mendapatkan perawatan di RS Taman Harapan Baru dan penandatanganan dokumen ini, saya telah mendapat informasi tentang hak-hak dan kewajiban saya sebagai pasien.</div>

                        <div class="mt-16"><b>2. PERSETUJUAN PELAYANAN KESEHATAN.</b> Saya menyetujui dan memberikan persetujuan untuk mendapat pelayanan Kesehatan di RS Taman Harapan Baru dan dengan ini saya meminta dan memberikan kuasa kepada pihak rumah sakit, dokter dan perawat, dan tenaga Kesehatan lainnya untuk memberikan asuhan keperawatan, pemeriksaan fisik yang dilakukan oleh dokter dan perawat, melakukan prosedur diagnostic, radiologi, dan atau terapi dan tatalkasusa sesuai pertimbangan dokter yang diperlukan atau disarankan pada perawatan saya. Hal ini mencakup seluruh pemeriksaan dan prosedur diagnostic rutin, termasuk x-ray, pemberian dan atau Tindakan medis serta penyuntikan (intramuskular, intravena dan prosedur Invasif lainnya) produk farmasi dan oba-obatan, pemasangan alat Kesehatan (kecuali yang membutuhkan persetujuan khusus/tertulis), dan pengambilan darah untuk pemeriksaan laboratorium atau pemeriksaan patologi yang dibutuhkan untuk pengobatan dan Tindakan yang aman.</div>

                        <div class="mt-16"><b>3. PRIVASI.</b> Saya memberi kuasa kepada RS Taman Harapan Baru menjamin privasi dan kerahasiaan penyakit saya selama dalam perawatan.</div>

                        <div class="mt-16"><b>4. RAHASIA DOKTER.</b> Saya setuju RS Taman Harapan Baru menjamin rahasia kedokteran saya baik untuk kepentingan perawatan atau pengobatan, Pendidikan maupun penelitian kecuali saya mengungkapkan sendiri atau orang lain yang saya beri kuasa sebagai Penjamin.</div>

                        <div class="mt-16"><b>5. MEMBUKA RAHASIA KEDOKTERAN.</b> Saya setuju untuk membuka rahasia kedokteran terkait tentang kondisi Kesehatan, asuhan dan pengobatan yang saya terima kepada:
                            <ul>
                            <li>Dokter dan tenaga kesehatan lain yang memberikan asuhan keperawatan kepada saya</li>
                            <li>Asuransi Kesehatan atau perusahaan penjamin biaya atau pihak lain yang menjamin pembiayaan saya</li>
                            <li>Dan pihak yang berwenang</li>
                            </ul>
                        </div>

                        <div class="mt-16"><b>6. BARANG PRIBADI.</b> Saya setuju untuk tidak membawa barang-barang berharga yang tidak diperlukan (seperti: perhiasan, elektronik, dll) selama dalam perawatan di RS Taman Harapan Baru. Saya memahami dan menyetujui RS Taman Harapan Baru tidak bertanggung jawab terhadap kehilangan, kerusakan atau pencurian barang berharga.</div>

                        <div class="mt-16"><b>7. PENGAJUAN KELUHAN.</b> Saya menyatakan bahwa saya telah menerima informasi tentang adanya tatacara mengajukan dan mengatasi keluhan terkait pelayanan medik yang diberikan terhadap diri saya. Saya setuju untuk mengikuti tatacara mengajukan keluhan tersebut sesuai prosedur yang ada.</div>

                        <div class="mt-16"><b>8. KEWAJIBAN PEMBAYARAN.</b> Saya menyatakan setuju, baik sebagai wali atau sebagai pasien, bahwa sesuai pertimbangan pelayanan yang diberikan kepada pasien, maka saya wajib untuk membayar total / menyelesaikan biaya pelayanan berdasarkan acuan biaya dan ketentuan di RS Taman Harapan Baru. Saya juga menyadari dan memahami bahwa:
                            <ul>
                            <li>Apabila kelas rawat yang ditempati tidak sesuai dengan hak kelas perawatan yang ditanggung oleh penjamin, maka saya bersedia untuk melunasi selisih biaya perawatan di RS Taman Harapan Baru.</li>
                            <li>Apabila ada biaya pemeriksaan/Tindakan/perawatan yang tidak ditanggung/Sebagian ditanggung oleh penjamin, maka saya bersedia melunasi biayanya.</li>
                            <li>Apabila rumah sakit membutuhkan proses hukum untuk menagih biaya pelayanan rumah sakit dari saya, saya memahami bahwa saya bertanggung jawab untuk membayar semua biaya yang disebabkan dari proses hukum tersebut.</li>
                            </ul>
                        </div>

                        <div class="mt-16"><b>9. PERMINTAAN PULANG PAKSA.</b> Apabila saya/keluarga ingin mengakhiri pengobatan atau pelayanan medis walaupun dokter belum mengijinkan, maka saya bersedia untuk dinyatakan sebagai pulang paksa dan bila terjadi hal-hal pada saya/keluarga akibat pulang paksa tersebut maka saya tidak akan menuntut pihak rumah sakit. Melalui dokumen ini, saya menegaskan bahwa saya mempercayakan kepada semua tenaga Kesehatan rumah sakit untuk memberikan perawatan, diagnostic dan terapi kepada saya sebagai pasien rawat inap atau rawat jalan atau Instalasi Gawat Darurat (IGD), termasuk semua pemeriksaan penunjang, yang dibutuhkan untuk pengobatan dan Tindakan yang aman.</div>

                        <div class="mt-32">
                            <b>Saya telah membaca dan sepenuhnya setuju</b> dengan setiap pernyataan yang terdapat pada formulir ini dan bersedia menandatanganinya tanpa paksaan dan dengan kesadaran penuh.
                        </div>

                        <div class="text-regular-18 mt-32" style="line-height: 140%;">
                            <div class="d-flex justify-content-end w-100 mt-64">
                                <div class="col-10 text-center">
                                    <div>Bekasi, {{ convert_date(regData.birth_date, 'L') }}</div>
                                    <div class="mb-120">Pasien / Keluarga / Penanggung Jawab</div>
                                    {{ ('(.................................................)') }}
                                    <div class="mb-120">Tanda Tangan dan Nama Lengkap</div>
                                    <!-- <template v-if="soapData.employee_signature">
                                        <b-img :src="urlbe + soapData.employee_signature" style="width: 120px;" />
                                    </template> -->
                                    <!-- <div>{{ soapData.employee_doctor_name }}</div> -->
                                </div>
                                <div class="col-5 text-center">
                                    <br>
                                    <div class="mb-120">Petugas Pendaftaran</div>
                                    <!-- <div
                                        style="display: inline-block; border: 1px solid black; padding: 5px 10px;">
                                        Materai 10000
                                    </div> -->
                                    <!-- <div> {{ ('Materai 10000') }}</div> -->
                                    {{ ('(.................................................)') }}
                                    <div class="mb-120">Tanda Tangan dan Nama Lengkap</div>
                                    <!-- <template v-if="soapData.employee_signature">
                                        <b-img :src="urlbe + soapData.employee_signature" style="width: 120px;" />
                                    </template> -->
                                    <!-- <div>{{ soapData.employee_doctor_name }}</div> -->
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
            days: 1,
            soap_id: ''
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
            if (addedDays !== 0) {
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
@import '@/@core/css/main/main.min.css';


@media print {
    @page {
        size: A4;
        margin: 16mm;
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
    /* height: 50.7cm !important; */
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
