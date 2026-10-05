<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <div class="row align-items-center w-100">
                        <div class="col-2">
                            <img src="../../../../@core/assets/images/logo-thb-small.png" width="60%" alt="rs-logo"> <br>
                        </div>
                        <div class="col text-center text-bold-28">
                            <p class="text-bold-14">RS Taman Harapan Baru</p>
                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                            <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                        </div>
                        <div class="col-2">
                            <img src="../../../../@core/assets/images/lafki-logo.png" width="60%" alt="lafki-logo"> <br>
                        </div>
                    </div>
                    <div class="canvas-body mt-32 px-40">
                        <section>
                            <div class="row mx-0 mt-28">
                                <div class="col-6">
                                    <table class="text-regular-14">
                                        <tr>
                                            <td class="first-cell">No. Nota</td>
                                            <td class="px-3">:</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">No. RM</td>
                                            <td class="px-3">:</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Nama Pasien</td>
                                            <td class="px-3">:</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Dokter</td>
                                            <td class="px-3">:</td>
                                            <td></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-6">
                                    <table class="text-regular-14">
                                        <tr>
                                            <td class="first-cell">Ruangan/Poli</td>
                                            <td class="px-3">:</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Kelas</td>
                                            <td class="px-3">:</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Jenis Bayar</td>
                                            <td class="px-3">:</td>
                                            <td></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </section>
                        <section class="mt-24">
                            <b-table-simple bordered small class="text-center">
                                <b-thead class="text-bold-14">
                                    <b-tr class="bg-neutral-100">
                                        <b-th>JENIS TINDAKAN</b-th>
                                        <b-th>LOKASI</b-th>
                                        <b-th>QTY</b-th>
                                        <b-th>HARGA</b-th>
                                        <b-th>TOTAL</b-th>
                                    </b-tr>
                                </b-thead>
                                <b-tbody class="text-regular-14">
                                    <b-tr>
                                        <b-td class="text-left">Konsultasi Dokter Umum</b-td>
                                        <b-td>IGD</b-td>
                                        <b-td>1</b-td>
                                        <b-td class="text-right">Rp1.000.000,00</b-td>
                                        <b-td class="text-right">Rp1.000.000,00</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td colspan="5" class="text-right font-weight-bold">Rp1.000.000,00</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td colspan="5" class="font-weight-bold text-left">FARMASI</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td class="text-left">Sarung Tangan Steril UK. 7.5 (AYN)</b-td>
                                        <b-td>Depo IGD</b-td>
                                        <b-td>1</b-td>
                                        <b-td class="text-right">Rp8.600,00</b-td>
                                        <b-td class="text-right">Rp8.600,00</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td colspan="5" class="text-right font-weight-bold">Rp1.000.000,00</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td class="text-left">Administrasi</b-td>
                                        <b-td>IGD</b-td>
                                        <b-td>1</b-td>
                                        <b-td class="text-right"></b-td>
                                        <b-td class="text-right">Rp30.000,00</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td colspan="5" class="text-right font-weight-bold">Rp30.000,00</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td colspan="4" class="text-right">Biaya</b-td>
                                        <b-td class="text-right">Rp</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td colspan="4" class="text-right">Diskon</b-td>
                                        <b-td class="text-right">Rp</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td colspan="4" class="text-right">Grand Total</b-td>
                                        <b-td class="text-right">Rp</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td colspan="4" class="text-right">Cara Bayar</b-td>
                                        <b-td class="text-right"></b-td>
                                    </b-tr>
                                </b-tbody>
                            </b-table-simple>
                        </section>
                        <section class="row mt-64">
                            <div class="col-6 text-center">
                                <p class="text-regular-14">Pasien / Keluarga Pasien</p>
                                <p class="text-bold-14 text-uppercase mt-64">(..............................................)</p>
                            </div>
                            <div class="col-6 text-center">
                                <p class="text-regular-14">Bekasi, 26 Agustus 2024</p>
                                <p>( Kasir )</p>
                                <p class="text-bold-14 text-uppercase mt-64">(..............................................)</p>
                            </div>

                        </section>
                    </div>
                    <!-- <div class="mt-32">
                        <div class="d-flex justify-content-center">
                            <img src="../../../../@core/assets/images/nurse/vector-footer.png" style="width: 100%;" alt="footer">
                        </div>
                    </div> -->
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
            location.reload()
        },
        closeWindow(){
            window.close()
        },
    }
};

</script>
<style>
@import '../../../../@core/css/main/main.min.css';
/* @import url('https://fonts.cdnfonts.com/css/dot-matrix'); */

@media print {
    @page {
        size: A4;
    }
}
/* body {
    font-family: 'Dot Matrix', sans-serif;
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


/* #canvas {
    width: 30cm !important;
    width: 21.2cm !important;
    height: 29.7cm !important;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
} */

.w-40 {
    width: 40%;
}

</style>
