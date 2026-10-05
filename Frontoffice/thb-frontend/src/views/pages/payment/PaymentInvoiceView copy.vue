<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <div class="canvas-header">
                        <img src="@/@core/assets/images/nurse/zeemar-logo.png" alt=""> <br>
                        <img src="@/@core/assets/images/nurse/vector-header.png" alt="">
                    </div>
                    <div class="canvas-body mt-32 px-40">
                        <section>
                            <p class="text-center text-bold-16">RINCIAN BILLING SEMENTARA</p>
                            <div class="row mx-0 mt-28">
                                <div class="col-6">
                                    <table class="text-regular-14">
                                        <tr>
                                            <td class="first-cell">No. RM</td>
                                            <td class="px-3">:</td>
                                            <td>00013596</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">No. Pendaftaran</td>
                                            <td class="px-3">:</td>
                                            <td>202210261000003</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Nama Pasien</td>
                                            <td class="px-3">:</td>
                                            <td>Wati</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Jenis Kelamin / Umur</td>
                                            <td class="px-3">:</td>
                                            <td>LAKI - LAKI / 17 Tahun</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Jaminan</td>
                                            <td class="px-3">:</td>
                                            <td>Pribadi</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Poli</td>
                                            <td class="px-3">:</td>
                                            <td>Poli Gigi dan Mulut</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-6">
                                    <table class="text-regular-14">
                                        <tr>
                                            <td class="first-cell">No. Invoice</td>
                                            <td class="px-3">:</td>
                                            <td>0000033836</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Tgl. / Jam Masuk</td>
                                            <td class="px-3">:</td>
                                            <td>24-03-2023 10:16:36</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Alamat</td>
                                            <td class="px-3">:</td>
                                            <td>Jl. Melati</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Dokter</td>
                                            <td class="px-3">:</td>
                                            <td>dr. Vicky Vendy</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </section>
                        <section class="mt-24">
                            <b-table-simple class="text-center">
                                <b-thead class="text-bold-14">
                                    <b-tr class="bg-neutral-100">
                                        <b-th>No</b-th>
                                        <b-th>Tindakan</b-th>
                                        <b-th>Harga</b-th>
                                        <b-th>QTY</b-th>
                                        <b-th>Disc/Promo</b-th>
                                        <b-th>Sub Total</b-th>
                                    </b-tr>
                                </b-thead>
                                <b-tbody class="text-regular-14">
                                    <b-tr class="text-left font-weight-bold bg-neutral-100">
                                        <b-td colspan="6">Rincian Dokter</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td>1</b-td>
                                        <b-td>
                                            <p class="text-left">Konsultasi Dokter Gigi 31/03/2023 <br> simrs, Adm</p>
                                        </b-td>
                                        <b-td>Rp.100.000,00	</b-td>
                                        <b-td>1</b-td>
                                        <b-td>0.00</b-td>
                                        <b-td class="text-right">Rp.100.000,00</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td>2</b-td>
                                        <b-td>
                                            <p class="text-left">Ekstraksi Gigi Dewasa Sulit 31/03/2023 <br> simrs, Adm</p>
                                        </b-td>
                                        <b-td>Rp.400.000,00	</b-td>
                                        <b-td>1</b-td>
                                        <b-td>0.00</b-td>
                                        <b-td class="text-right">Rp.400.000,00</b-td>
                                    </b-tr>
                                    <b-tr class="text-left font-weight-bold bg-neutral-100">
                                        <b-td colspan="6">Rincian Laboratorium</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td>1</b-td>
                                        <b-td>
                                            <p class="text-left">Hematologi Lengkap 31/03/2023</p>
                                        </b-td>
                                        <b-td>Rp.104.500,00</b-td>
                                        <b-td>1</b-td>
                                        <b-td></b-td>
                                        <b-td class="text-right">Rp.104.500,00</b-td>
                                    </b-tr>
                                    <b-tr class="text-left font-weight-bold bg-neutral-100">
                                        <b-td colspan="6">Rincian Radiologi</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td>1</b-td>
                                        <b-td>
                                            <p class="text-left">CT Scan Orbita Kontras 31/03/2023</p>
                                        </b-td>
                                        <b-td>Rp.2.445.500,00</b-td>
                                        <b-td>1</b-td>
                                        <b-td></b-td>
                                        <b-td class="text-right">Rp.2.445.500,00</b-td>
                                    </b-tr>
                                    <b-tr class="text-left font-weight-bold bg-neutral-100">
                                        <b-td colspan="6">Pemakaian Alat</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td>1</b-td>
                                        <b-td>
                                            <p class="text-left">Udara Tekan per 12 jam 13/04/2023</p>
                                        </b-td>
                                        <b-td>Rp.410.000,00	</b-td>
                                        <b-td>1</b-td>
                                        <b-td></b-td>
                                        <b-td class="text-right">Rp.410.000,00	</b-td>
                                    </b-tr>
                                    <b-tr class="text-left font-weight-bold bg-neutral-100">
                                        <b-td colspan="6">Rincian Administrasi</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td>1</b-td>
                                        <b-td>
                                            <p class="text-left">ADM PASIEN LAMA RAWAT JALAN 31/03/2023</p>
                                        </b-td>
                                        <b-td>Rp.15.000,00	</b-td>
                                        <b-td>1</b-td>
                                        <b-td></b-td>
                                        <b-td class="text-right">Rp.15.000,00	</b-td>
                                    </b-tr>
                                    <b-tr class="text-right text-regular-14">
                                        <b-td colspan="4">Sub Total</b-td>
                                        <b-td colspan="2">Rp.3.475.000,00</b-td>
                                    </b-tr>
                                    <b-tr class="text-right text-regular-14">
                                        <b-td colspan="4">Diskon</b-td>
                                        <b-td colspan="2">Rp.0,00</b-td>
                                    </b-tr>
                                    <b-tr class="text-right text-regular-14">
                                        <b-td colspan="4">Voucher</b-td>
                                        <b-td colspan="2">Rp.0,00</b-td>
                                    </b-tr>
                                    <b-tr class="text-right text-regular-14">
                                        <b-td colspan="4">Total</b-td>
                                        <b-td colspan="2">Rp.3.475.000,00</b-td>
                                    </b-tr>
                                </b-tbody>
                            </b-table-simple>
                        </section>
                        <section class="d-flex justify-content-between w-100 mt-64">
                            <div class="col-4 col-lg-5 text-center">
                                <p class="text-regular-14">Pasien / Penanggung Jawab</p>
                                <!-- <img src="@/@core/assets/images/nurse/vector-sign.png" width="80px" alt="your-sign" class="my-2"> -->
                                <p class="text-bold-14 text-uppercase mt-80">WATI</p>
                            </div>
                            <div class="ml-auto col-4 col-lg-5 text-center">
                                <p class="text-regular-14">DKI JAKARTA, 05-05-2023</p>
                                <p class="text-regular-14">Dibuat Oleh,</p>
                                <!-- <img src="@/@core/assets/images/nurse/vector-sign.png" width="80px" alt="your-sign" class="my-2"> -->
                                <p class="text-bold-14 text-uppercase mt-64">simrs, Adm</p>
                            </div>
                        </section>
                    </div>
                    <div class="canvas-footer mt-32">
                        <div class="d-flex justify-content-center">
                            <img src="../../../@core/assets/images/nurse/vector-footer.png" style="width: 100%;" alt="footer">
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
            // Save the current contents of the body to a variable
            const originalContents = document.body.innerHTML;
            // Set the body contents to the contents of the "canvas" div
            document.body.innerHTML = canvas.innerHTML;
            // Call the print method of the current window
            window.print();
            // Restore the original contents of the body
            document.body.innerHTML = originalContents;
        }
    }
};

</script>
<style>
@import '../../../@core/css/main/main.min.css';
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
    width: 30cm !important;
    /* width: 21.2cm !important; */
    /* height: 29.7cm !important; */
    /* box-shadow: 0 0 5px rgba(0, 0, 0, 0.1); */
}

.canvas-header {
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
}

.w-40 {
    width: 40%;
}

.canvas-footer {
    position: absolute;
    bottom: 0;
    /* right: 0; */
}
</style>
