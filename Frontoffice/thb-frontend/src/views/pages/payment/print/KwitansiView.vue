<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <div class="canvas-body px-40">
                        <section>
                            <div class="mt-120">
                                <p class="text-center text-bold-16" style="text-decoration: underline;">KWITANSI</p>
                                <p class="text-center text-bold-16">KWT/33839/RWJ/RSC/II/2021</p>
                            </div>
                            <div class="mt-28">
                                <table class="text-regular-14">
                                    <tr>
                                        <td style="width: 30%;">NO. RM</td>
                                        <td class="px-3">:</td>
                                        <td>00013596</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 30%;">TELAH TERIMA DARI</td>
                                        <td class="px-3">:</td>
                                        <td>WATI [An] </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 30%;">UANG SEJUMLAH</td>
                                        <td class="px-3">:</td>
                                        <td>Rp.3.475.000,-</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 30%;">TERBILANG</td>
                                        <td class="px-3">:</td>
                                        <td>Tiga Juta Empat Ratus Tujuh Puluh Lima Ribu Rupiah</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 30%; vertical-align: top;">UNTUK PEMBAYARAN</td>
                                        <td style="vertical-align: top;" class="px-3">:</td>
                                        <td>ADM PASIEN LAMA RAWAT JALAN,  Konsultasi Dokter Gigi,  Ekstraksi Gigi Dewasa Sulit ,  Hematologi Lengkap,  CT Scan Orbita Kontras ,  Udara Tekan per 12 jam,</td>
                                    </tr>
                                </table>
                            </div>
                        </section>
                        <section class="d-flex w-100 mt-64">
                            <div class="ml-auto col-4 col-lg-5 text-center">
                                <p class="text-regular-14">DKI JAKARTA, 05-05-2023</p>
                                <p class="text-regular-14">Dibuat Oleh,</p>
                                <!-- <img src="@/@core/assets/images/nurse/vector-sign.png" width="80px" alt="your-sign" class="my-2"> -->
                                <p class="text-bold-14 text-uppercase mt-64">simrs, Adm</p>
                            </div>

                        </section>
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
@import '../../../../@core/css/main/main.min.css';

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
    width: 21.2cm !important;
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
