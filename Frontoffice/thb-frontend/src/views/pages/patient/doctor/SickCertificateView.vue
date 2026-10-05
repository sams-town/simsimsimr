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
                        <section class="mt-28">
                            <p class="text-center text-bold-36">SURAT KETERANGAN SAKIT</p>
                            <p class="text-center text-regular-24">000002.0223456789</p>
                        </section>
                        <section class="row mt-28">
                            <div class="col">
                                <div class="d-flex flex-nowrap">
                                    <div class="col-3 col-lg-4 text-regular-18">NO RM </div>
                                    <div class="col text-semi-18">: 033312345789</div>
                                </div>
                                <div class="d-flex flex-nowrap">
                                    <div class="col-3 col-lg-4 text-regular-18">Pasien</div>
                                    <div class="col text-semi-18">: Abimanyu</div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="d-flex flex-nowrap">
                                    <div class="col-4 col-lg-5 text-regular-18">Umur </div>
                                    <div class="col text-semi-18">: 12 th 4bl 21 hr</div>
                                </div>
                                <div class="d-flex flex-nowrap">
                                    <div class="col-4 col-lg-5 text-regular-18">Tanggal Lahir</div>
                                    <div class="col text-semi-18">: 06 / 02 / 2010</div>
                                </div>
                                <div class="d-flex flex-nowrap">
                                    <div class="col-4 col-lg-5 text-regular-18">Jenis Kelamin</div>
                                    <div class="col text-semi-18">: Laki - Laki</div>
                                </div>
                            </div>
                        </section>
                        <section class="mt-32">
                            <table class="col">
                                <tr>
                                    <td colspan="2" class="text-regular-18 py-3">Yang bertandatangan dibawah ini, Dokter ZeemaRR menerangkan dengan sesungguhnya bahwa:</td>
                                </tr>
                                <tr>
                                    <td class="w-40 text-regular-18">Nama</td>
                                    <td class="text-semi-18">: Abimanyu</td>
                                </tr>
                                <tr>
                                    <td class="text-regular-18">Tempat/ Tgl Lahir</td>
                                    <td class="text-semi-18">: 06 /02 /2010</td>
                                </tr>
                                <tr>
                                    <td class="text-regular-18">Umur</td>
                                    <td class="text-semi-18">: 12 th</td>
                                </tr>
                                <tr>
                                    <td class="text-regular-18">Jenis Kelamin</td>
                                    <td class="text-semi-18">: Laki-Laki</td>
                                </tr>
                                <tr>
                                    <td class="text-regular-18">Alamat</td>
                                    <td class="text-semi-18">: Jl. Jakarta selatan</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-regular-18 py-32">
                                        <p>Berdasarkan Kondisi Kesehatannya, yang bersangkutan dianjurkan :</p>
                                        <p><b-icon icon="check-square-fill" class="text-indigo" /> Istirahat dirumah selama 3 hari, tanggal 20 Feb 2023 s/d 23 Feb 2023</p>
                                        <p><b-icon icon="check-square-fill" class="text-indigo" /> Dilarang Olahraga selama 0 Hari, kecuali kegiatan ringan,</p>
                                    </td>
                                </tr>
                            </table>
                        </section>
                        <section class="d-flex w-100 mb-64">
                            <div class="ml-auto col-4 col-lg-5 text-center">
                                <p class="text-regular-18">06 Februari 2023</p>
                                <p class="text-semi-18">DOKTER PENANGGUNG JAWAB </p>
                                <img src="@/@core/assets/images/nurse/vector-sign.png" width="100px" alt="your-sign" class="mt-1">
                                <p class="text-bold-18 mt-1 text-uppercase">Wahyu Novianto</p>
                                <hr class="w-100 my-1" style="background-color: black; height: 3px;">
                            </div>

                        </section>
                    </div>
                    <div class="canvas-footer mt-32">
                        <div class="d-flex justify-content-center">
                            <img src="./../../../../@core/assets/images/nurse/vector-footer.png" style="width: 100%;" alt="footer">
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
@import './../../../../@core/css/main/main.min.css';

/* table,
th,
td {
    border: 1px solid black;
} */
td{
    padding: 4px 0;
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
    width: 210mm;
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

.canvas-footer{
    position: absolute;
    bottom: 0;
    /* right: 0; */
}

</style>
