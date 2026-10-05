<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <div class="canvas-header px-3 mt-16">
                        <img src="@/@core/assets/images/bpjs/bpjs_logo.png" height="40px" alt="rs-logo"> <br>
                    </div>
                    <div class="mt-40">
                        <div class="row mx-0">
                            <div class="col-6">
                                <table class="text-regular-14">
                                    <tr>
                                        <td class="first-cell">No. SEP</td>
                                        <td class="px-3">:</td>
                                        <td>0301R01690444513</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Tgl. SEP	</td>
                                        <td class="px-3">:</td>
                                        <td>27 July 2023</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">No. Kartu</td>
                                        <td class="px-3">:</td>
                                        <td>0003502569857 <span class="ml-16">MR. 00610992</span></td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Nama Peserta</td>
                                        <td class="px-3">:</td>
                                        <td class="text-uppercase">BINTANG</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Tgl.Lahir</td>
                                        <td class="px-3">:</td>
                                        <td>15 November 1989 Kelamin</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">No.Telepon</td>
                                        <td class="px-3">:</td>
                                        <td>08123923213</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Sub/Spesialis</td>
                                        <td class="px-3">:</td>
                                        <td>Poli Penyakit Dalam</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Dokter</td>
                                        <td class="px-3">:</td>
                                        <td>Fdr. Rifal Rinaldi, Sp.PD</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Faskes Perujuk</td>
                                        <td class="px-3">:</td>
                                        <td class="text-uppercase">bOJONG</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Diagnosa Awal</td>
                                        <td class="px-3">:</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Catatan</td>
                                        <td class="px-3">:</td>
                                        <td></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-6">
                                <table class="text-regular-14">
                                    <tr>
                                        <td class="first-cell">Peserta</td>
                                        <td class="px-3">:</td>
                                        <td class="text-uppercase">PEKERJA MANDIRI</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">COB</td>
                                        <td class="px-3">:</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell" style="vertical-align:top;">Jns.Kunjungan</td>
                                        <td class="px-3" style="vertical-align:top;">:</td>
                                        <td style="vertical-align:top;">
                                            <li>Konsultasi Dokter (Pertama)</li>
                                            <li>Prosedur Tidak Berkelanjutan</li>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Jns.Rawat</td>
                                        <td class="px-3">:</td>
                                        <td>Rawat Jalan</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Kls.Hak</td>
                                        <td class="px-3">:</td>
                                        <td>3</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Kls.Rawat</td>
                                        <td class="px-3">:</td>
                                        <td>3</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Penjamin</td>
                                        <td class="px-3">:</td>
                                        <td></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="px-3 mt-16 row">
                            <div class="col-8">
                                <div>
                                    <small class="text-muted">*Saya Menyetujui BPJS Kesehatan menggunakan informasi Medis Pasien jika diperlukan.</small>
                                </div>
                                <div>
                                    <small class="text-muted">*SEP bukan sebagai bukti penjaminan peserta</small>
                                </div>
                                <div class="mt-32">
                                    <small class="text-muted">Cetakan ke 5 27-07-2023 18:52:20</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center text-regular-14">
                                    <p>Pasien/ <br> Keluarga Pasien</p>
                                    <div class="px-32 mt-72">
                                        <hr class="w-100" style="border: 1px solid black;">
                                    </div>
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
export default {
    methods: {
        printCanvas() {
            const canvas = this.$refs.canvas;
            const originalContents = document.body.innerHTML;
            document.body.innerHTML = canvas.innerHTML;
            window.print();
            document.body.innerHTML = originalContents;
        }
    }
};

</script>
<style>
@import './../../../../@core/css/main/main.min.css';

/* table,
th,
td {
    border: 1px solid black;
} */
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
    padding: 20px 0;
}

.canvas-header {
    width: 100%;
}

/* .canvas-header img:first-child {
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
