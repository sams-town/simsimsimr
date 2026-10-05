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
                        <div class="text-bold-24 with-line">HAK DAN KEWAJIBAN PASIEN DAN KELUARGA</div>
                        <div class="text-regular-24">{{ letterData.no }}</div>
                    </div>
                    <div class="text-regular-15 mt-32" style="line-height: 140%;">
                        <div><b>Setiap Pasien Mempunyai Hak:</b></div>
                        <!-- <table class="mt-16">
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Nama</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.fullname }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Tanggal Lahir</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.birth_date }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Jenis Kelamin</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.sex_name }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Alamat</td>
                                <td class="col-1 text-right" style="vertical-align: top;">:</td>
                                <td>{{ regData.address }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Diagnosa</td>
                                <td class="col-1 text-right" style="vertical-align: top;">:</td>
                                <td>
                                    <p v-for="(list, index) in soapData.icd10">
                                        {{ list.code }} - {{ list.name }}
                                    </p>
                                </td>
                            </tr>
                        </table> -->
                        <!-- <div class="mt-16">Perlu istirahat selama {{ days }} hari, mulai dari tanggal {{
                            letterData.start |
                            convert_date }} s/d {{ letterData.end | convert_date }}, karena sakit. Atas perhatian dan
                            kerjasama Bapak/Ibu, kami mengucapkan terima kasih.</div> -->
                        <div class="mt-16">1. Memperoleh informasi mengenai tata tertib dan peraturan yang berlaku di RS
                            Taman Harapan Baru.</div>
                        <div class="mt-16">2. Memperoleh informasi tentang hak dan kewajiban pasien.</div>
                        <div class="mt-16">3. Memperoleh layanan yang manusiawi,adil,jujur,dan tanpa diskriminasi.</div>
                        <div class="mt-16">4. Memperoleh layanan kesehatan yang bermutu sesuai dengan standar profesi
                            dan standar prosedur operasional.</div>
                        <div class="mt-16">5. Memperoleh layanan yang efektif dan efisien sehingga pasien terhindar
                            dari kerugian fisik dan materi.</div>
                        <div class="mt-16">6. Mengajukan pengaduan atas kualitas pelayanan yang didapatkan.</div>
                        <div class="mt-16">7. Memilih dokter dan kelas perawatan sesuai dengan keinginannya dan
                            per aturan yang berlaku di RS Taman Harapan Baru.</div>
                        <div class="mt-16">8. Mendapatkan privasi dan kerahasiaan penyakit yang dideritanya kepada
                            dokter lain yang mempunyai Surat Izin Praktik (SIP) baik dalam maupun di luar RS Taman Harapan Baru.</div>
                        <div class="mt-16">9. Mendapatkan privasi dan kerahasiaan penyakit yang diderita termasuk
                            data-data medisnya.</div>
                        <div class="mt-16">10. Mendapatkan informasi yang meliputi diagnosis dan tatacara Tindakan medis
                            ,tujuan Tindakan medis,alternatif Tindakan,resiko dan komplikasi yang mungkin terjadi,dan
                            progonis Tindakan yang dilakukan serta perki raaan biaya pengobatan.</div>
                        <div class="mt-16">11. Memberikan persetujuan dan menolak atas tindakan yang dilakukan oleh
                            tenaga kesehatan terhadap penyakit yang dideritanya.</div>
                        <div class="mt-16">12. Didampingi keluarganya dalam keadaan kritis.</div>
                        <div class="mt-16">13. Menjalankan Ibadah sesuai agama atau kepercayaan yang dianut nya selama
                            hal itu tidak mengganggu pasien lainnya.</div>
                        <div class="mt-16">14. Memperoleh keamanan dan keselamatan dirinya selama dalam perawatan di RS
                            Taman Harapan Baru.</div>
                        <div class="mt-16">15. Mengajukan usul,perbaikan atas perlakuan RS Taman Harapan Baru terhadap
                            dirinya.</div>
                        <div class="mt-16">16. Menolak pelayanan bimbingan rohani yang tidak sesuai dengan agama dan
                            kepercayaan yang dianutnya.</div>
                        <div class="mt-16">17. Menggugat dan/atau menuntut RS Taman Harapan Baru apabila RS Taman Harapan Baru
                            diduga memberikan pelayanan yang tidak sesuai dengan standar baik secara perdata
                            ataupun pidana.</div>
                        <div class="mt-16">18. Mengeluhkan pelayanan RS Taman Harapan Baru yang tidak sesuai dengan
                            standar pelayanan melalui media cetak dan elektronik sesuai dengan ketentuan peraturan
                            perundang-undangan.</div>

                        <div class="mt-32"><b>Setiap Pasien Dan Keluarga Mempunyai Kewajiban:</b></div>

                        <div class="mt-16">1. Pasien dan keluarga berkewajiban mematuhi peraturan yang berlaku di RS
                            Taman Harapan Baru.</div>
                        <div class="mt-16">2. Pasien dan keluarga berkewajiban menggunakan fasilitas rumah sakit secara
                            bertanggung jawab.</div>
                        <div class="mt-16">3. Pasien dan keluarga berkewajiban menghormati hak-hak pasien lain,dan hak
                            tenaga kesehatan serta petugas lainnya yang bekerja di RS Taman Harapan Baru.</div>
                        <div class="mt-16">4. Pasien dan keluarga berkewajiban memberikan informasi yang jujur,lengkap
                            dan akurasi sesuai kemampuan dan pengetahuannya tentang masalah kesehatan pasien.</div>
                        <div class="mt-16">5. Pasien berkewajiban memberikan informasi yang jujur mengenai
                            kemampuan finansial dan jaminan kesehatan yang dimilikinya.</div>
                        <div class="mt-16">6. Pasien berkewajiban mematuhi rencana terapi yang direkomendasikan oleh
                            tenaga kesehatan di RS Taman Harapan Baru dan disetujui oleh pasien yang bersangkutan
                            setelah mendapatkan penjelasan sesuai dengan ketentuan peraturan perundang-undangan.</div>
                        <div class="mt-16">7. Pasien berkewajiban menerima segala konsekuensi dan keputusan pribadinya
                            untuk menolak rencana terapi yang direkomendasikan oleh tenaga kesehatan atau tidak mematuhi
                            petunjuk yang diberikan tenaga kesehatan dalam rangka penyembuhan penyakit atau masalah
                            kesehatan.</div>
                        <div class="mt-16">8. Pasien dan keluarga memberikan imbalan jasa atas pelayanan yang diterima.
                        </div>


                        <!-- <div class="d-flex justify-content-end w-100 mt-64">
                            <div class="col-4 text-center">
                                <div :class="soapData.employee_signature ? '' : 'mb-64'">Bekasi, {{ letterData.date |
                                    convert_date }}</div>
                                <template v-if="soapData.employee_signature">
                                    <b-img :src="urlbe + soapData.employee_signature" style="width: 120px;" />
                                </template>
                                <div>{{ soapData.employee_doctor_name }}</div>
                            </div>
                        </div> -->
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
        convert_date(value) {
            return moment(new Date(value)).locale('id').format('LL')
        }
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
