<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <section id="canvas" ref="canvas" class="mt-16 p-40" style="position: relative;">
                    <section class="d-flex flex-column justify-content-center text-regular-14 text-center mt-40">
                        <p>RUMAH SAKIT CENDANA</p>
                        <p>* BUKTI REGISTRASI *</p>
                        <p>202301300100004 / 00013607</p>
                    </section>
                    <section class="d-flex justify-content-center">
                        <div>
                            <hr style="border: 1px dashed gray; stroke-dashoffset: 50px;" class="w-100">
                            <table class="text-regular-14">
                                <tr>
                                    <td class="first-cell">NAMA</td>
                                    <td class="px-3">:</td>
                                    <td class="text-capitalize font-weight-bold">wAHYU EKO YULIANTO [Tn] [L]</td>
                                </tr>
                                <tr>
                                    <td class="first-cell">TGL LAHIR</td>
                                    <td class="px-3">:</td>
                                    <td>04-07-1992</td>
                                </tr>
                                <tr>
                                    <td class="first-cell">SEX</td>
                                    <td class="px-3">:</td>
                                    <td>LAKI - LAKI</td>
                                </tr>
                                <tr>
                                    <td class="first-cell">JAMINAN</td>
                                    <td class="px-3">:</td>
                                    <td>Pribadi</td>
                                </tr>
                                <tr>
                                    <td class="first-cell">STATUS</td>
                                    <td class="px-3">:</td>
                                    <td>Pasien Lama</td>
                                </tr>
                                <tr>
                                    <td class="first-cell">DOKTER</td>
                                    <td class="px-3">:</td>
                                    <td>dr. Vicky Vendy</td>
                                </tr>
                                <tr>
                                    <td class="first-cell">LAYANAN</td>
                                    <td class="px-3">:</td>
                                    <td>ALAMANDA 317</td>
                                </tr>
                            </table>
                            <hr style="border: 1px dashed gray; stroke-dashoffset: 50px;" class="w-100">
                        </div>
                    </section>
                    <p class="text-regular-14 text-center">simrs, Adm (30-01-2023/02:26:13)</p>
                </section>
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
    width: 21.2cm !important;
    /* height: 29.7cm !important; */
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
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
