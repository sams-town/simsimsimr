<template>
    <div>
        <b-container fluid="xl" class="mt-32 pb-32">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="d-flex justify-content-center mt-32">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <div class="letter-header">
                        <img src="../../../@core/assets/images/logo-thb.jpg" width="300px" alt="rs-logo">
                        <div class="d-flex">
                            <!-- <img src="../../../@core/assets/images/lafki-logo.png" width="70px" alt="lafki-logo"> -->
                            <!-- <img src="../../../@core/assets/images/apexa.png" width="100px" alt="apex-logo" class="ml-8"> -->
                        </div>
                    </div>
                    <div style="padding: 0 16mm;">
                        <div class="fw-700 fs-18 text-center mt-32">
                            <div>SURAT JAMINAN PASIEN RUJUKAN</div>
                            <div>{{letterData.no}}</div>
                        </div>
                        <div class="mt-32" style="line-height: 140%;">
                            <div>
                                <div class="fw-600">
                                    <div>Kepada Yth.</div>
                                    <div class="mt-8">Direktur RS Graha Hermin</div>
                                </div>
                                <div class="mt-8">Dengan ini menyatakan dan menjamin bahwa:</div>
                                <table class="mt-24 w-100 custom-table">
                                    <tr>
                                        <td style="width: 200px;">Nama Lengkap Pasien</td>
                                        <td class="px-1 text-center">:</td>
                                        <td>{{ regData.name }}</td>
                                    </tr>
                                    <tr>
                                        <td>Nomor Rekam Medis</td>
                                        <td class="px-1 text-center">:</td>
                                        <td>{{ regData.norm }}</td>
                                    </tr>
                                    <tr>
                                        <td>NIK</td>
                                        <td class="px-1 text-center">:</td>
                                        <td>{{ regData.idcard }}</td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal Lahir</td>
                                        <td class="px-1 text-center">:</td>
                                        <td>{{ regData.birth_date }}</td>
                                    </tr>
                                    <tr>
                                        <td>Alamat</td>
                                        <td class="px-1 text-center">:</td>
                                        <td>{{ regData.address }}</td>
                                    </tr>
                                    <tr>
                                        <td>Nomor Telepon</td>
                                        <td class="px-1 text-center">:</td>
                                        <td>{{ regData.phone }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="mt-32">Dengan ini kami menyatakan dan menjamin bahwa pasien yang disebutkan di atas adalah <span class="fw-600">Benar dan Sah</span> merupakan pasien yang saat ini berada dalam perawatan resmi di <span class="fw-600">RS Taman Harapan Baru Bekasi</span>. Berdasarkan hasil pemeriksaan medis, evaluasi klinis, serta rekam medis yang tersimpan pada sistem informasi rumah sakit, pasien tersebut dinyatakan memerlukan tindakan dan/atau pelayanan medis lanjutan untuk menjaga keberlangsungan terapi, pemulihan kondisi, serta penanganan sesuai standar prosedur operasional dan ketentuan medis yang berlaku.</div>
                            <div class="mt-10">Pernyataan ini dibuat dengan sebenar-benarnya untuk keperluan administrasi, koordinasi layanan kesehatan, maupun kebutuhan resmi lainnya, dan dapat digunakan sebagaimana mestinya.</div>

                            <div class="mt-64">
                                <div>Manager Keuangan</div>
                                <b-img :src="ttdsalah" class="my-12" style="width: 200px;"/>
                                <div>Ferry Rahmansyah</div>
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

export default {
    data() {
        return {
            urlbe: process.env.VUE_APP_IMAGE,
            regData: {},
            letterData: {},
            days: 1,
            ttdsalah: 'http://localhost/be-ci/assets/document/ttd/ttd-dr-Bintang.png'
        }
    },

    methods: {
        async getData(reg_des_id) {
            try {
                const response = await this.$store.dispatch('registration/getById', reg_des_id)
                this.regData = response[0]
            } catch (error) {
                console.error('error getData at: ', error)
            }
        },

        geLetter (id) {
			this.$store.dispatch('letter/getById', id).then(data => {
                this.letterData = data[0]
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
    },

    mounted() {
        window.addEventListener('keydown', this.handleKeyPress);
    },

    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },

    created() {
        this.getData(this.$route.params.reg_des_id)
        // this.geLetter(this.$route.params.letter_id)
    }
}
</script>

<style scoped>
.letter-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 40px 16mm;
    border-bottom: 2px solid;
    border-image: linear-gradient(to right, #3fe0d0 50%, #e73d58 50%) 1;
}

.custom-table td{
    vertical-align: top;
}

@media print {
    @page {
        size: A4;
        margin: 0mm;
    }
}

td {
    padding: 2px 0;
}
</style>
