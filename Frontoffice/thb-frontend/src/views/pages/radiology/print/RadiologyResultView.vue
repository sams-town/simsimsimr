<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <section class="row mx-0 mt-28">
                        <div class="col-6">
                            <table class="text-regular-14">
                                <tr>
                                    <td class="first-cell">No. Reg / RM</td>
                                    <td class="px-3">:</td>
                                    <td>202210261000003 / 00013596</td>
                                </tr>
                                <tr>
                                    <td class="first-cell">Nama Pasien</td>
                                    <td class="px-3">:</td>
                                    <td>Wati</td>
                                </tr>
                                <tr>
                                    <td class="first-cell">Tgl. Permintaan</td>
                                    <td class="px-3">:</td>
                                    <td>26-10-2022 16:38:08</td>
                                </tr>
                                <tr>
                                    <td class="first-cell">Tgl. Hasil</td>
                                    <td class="px-3">:</td>
                                    <td>30-01-2023 10:26:45</td>
                                </tr>
                                <tr>
                                    <td class="first-cell">Alamat</td>
                                    <td class="px-3">:</td>
                                    <td>Jl. Melati</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-6">
                            <table class="text-regular-14">
                                <tr>
                                    <td class="first-cell">No. Radiologi</td>
                                    <td class="px-3">:</td>
                                    <td>202301300002</td>
                                </tr>
                                <tr>
                                    <td class="first-cell">Tanggal Lahir</td>
                                    <td class="px-3">:</td>
                                    <td>03-02-2021</td>
                                </tr>
                                <tr>
                                    <td class="first-cell">Umur</td>
                                    <td class="px-3">:</td>
                                    <td>2 Tahun 3 Bulan 1 Hari</td>
                                </tr>
                                <tr>
                                    <td class="first-cell">Kelamin</td>
                                    <td class="px-3">:</td>
                                    <td>LAKI - LAKI</td>
                                </tr>
                                <tr>
                                    <td class="first-cell"> Dokter Pengirim</td>
                                    <td class="px-3">:</td>
                                    <td>simrs, Adm</td>
                                </tr>
                            </table>
                        </div>
                    </section>
                    <hr class="w-100 my-32" style="border: 2px solid rgb(82, 82, 82)">
                    <section class="px-3 text-regular-14">
                        <p>Yth simrs, Adm</p>
                        <p class="mt-2">Vert. Thoracal AP/Lat/Obl : Normal</p>
                        <p class="mt-2">Kesan : Baik</p>
                    </section>
                    <section class="d-flex justify-content-end px-3 mt-64">
                        <div class="text-regular-14">
                            <p>Btk</p>
                            <p>Salam Sejawat</p>
                            <p class="mt-64">dr. Anis,Sp.Rad</p>
                        </div>
                    </section>
                    <div class="canvas-footer mt-32">
                        <div class="d-flex justify-content-center">
                            <img src="./../../../../@core/assets/images/nurse/vector-footer.png" style="width: 100%;"
                                alt="footer">
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
    width: 210mm;
    width: 21.2cm !important;
    height: 29.7cm !important;
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
