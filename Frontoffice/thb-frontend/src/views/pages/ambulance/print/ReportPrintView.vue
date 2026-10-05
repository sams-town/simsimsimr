<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <div class="canvas-header d-flex px-3">
                        <img src="@/@core/assets/images/nurse/zeemar-logo.png" alt=""> <br>
                        <div class="mt-16 ml-20">
                            <p class="text-regular-12">RS</p>
                            <p class="text-regular-12">Jl.Raya Kedoya No.2, Kebon Jeruk, Jakarta 11520</p>
                            <p class="text-regular-12">Telp: (021) 29863838</p>
                            <p class="text-regular-12">Email: info@rscendana.com</p>
                        </div>
                        <!-- <img src="@/@core/assets/images/nurse/vector-header.png" alt=""> -->
                    </div>
                    <div class="mt-40">
                        <p class="text-center text-bold-16">LAPORAN AMBULAN</p>
                        <div class="row mx-0 mt-28">
                            <div class="col-6">
                                <table class="text-regular-14">
                                    <tr>
                                        <td class="first-cell">Tanggal</td>
                                        <td class="px-3">:</td>
                                        <td>11 May 2023 - 11 May 2023</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <!-- /info-table -->
                        <div class="px-3 mt-24">
                            <b-table-simple small responsive bordered>
                                <b-thead class="text-uppercase text-center text-bold-14">
                                    <b-tr>
                                        <b-th rowspan="2">No</b-th>
                                        <b-th rowspan="2">Tanggal Mulai</b-th>
                                        <b-th rowspan="2">Tanggal Selesai</b-th>
                                        <b-th rowspan="2">Nama Pasien</b-th>
                                        <b-th rowspan="2">Tujuan</b-th>
                                        <b-th rowspan="2">Kendaraan</b-th>
                                        <b-th rowspan="2">No Polisi</b-th>
                                        <b-th rowspan="2">Status</b-th>
                                        <b-th colspan="2">Petugas</b-th>
                                        <b-th rowspan="2">Total</b-th>
                                    </b-tr>
                                    <b-tr>
                                        <b-th>Perawat</b-th>
                                        <b-th>Driver</b-th>
                                    </b-tr>
                                </b-thead>
                                <b-tbody class="text-regular-14">
                                    <b-tr>
                                        <b-td colspan="10" class="text-right">Jumlah</b-td>
                                        <b-td>Rp 0.00</b-td>
                                    </b-tr>
                                </b-tbody>
                            </b-table-simple>
                        </div>
                    </div>
                    <div class="col-lg-7 mt-24 text-regular-14">
                        <div class="row">
                            <div class="col-4">
                                <p class="text-center">Mengetahui</p>
                                <p class="text-center mt-72">(....................)</p>
                                <p class="text-center mt-8">Kabid. Keuangan</p>
                            </div>
                            <div class="col-4">
                                <p class="text-center">Diperiksa</p>
                                <p class="text-center mt-72">(....................)</p>
                                <p class="text-center mt-8">Keuangan</p>
                            </div>
                            <div class="col-4">
                                <p class="text-center">Petugas Kasir</p>
                                <p class="text-center mt-72">(....................)</p>
                                <p class="text-center mt-8">Nama Kasir</p>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="canvas-footer mt-32">
                        <div class="d-flex justify-content-center">
                            <img src="./../../../../@core/assets/images/nurse/vector-footer.png" style="width: 100%;"
                                alt="footer">
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
    width: 30cm !important;
    /* width: 21.2cm !important; */
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
