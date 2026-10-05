<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <div class="canvas-header px-3">
                        <img src="@/@core/assets/images/nurse/zeemar-logo.png" alt=""> <br>
                        <div class="mt-16">
                            <p class="text-regular-12">Jl.Raya Kedoya No.2, Kebon Jeruk, Jakarta 11520</p>
                            <p class="text-regular-12">Telp: (021) 29863838</p>
                            <p class="text-regular-12">Email: info@rscendana.com</p>
                            <p class="text-regular-12">Website: www.rscendana.com</p>
                        </div>
                        <!-- <img src="@/@core/assets/images/nurse/vector-header.png" alt=""> -->
                    </div>
                    <div class="mt-20">
                        <p class="text-center text-bold-16">PERMINTAAN PENGADAAN BARANG</p>
                        <div class="row mx-0 mt-28">
                            <div class="col-6">
                                <table class="text-regular-14">
                                    <tr>
                                        <td class="first-cell">NO DOKUMEN</td>
                                        <td class="px-3">:</td>
                                        <td><i class="font-weight-bold">PM/LGU/11/2021/00002</i></td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">TANGGAL</td>
                                        <td class="px-3">:</td>
                                        <td>09 November 2021</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <!-- /info-table -->
                        <div class="px-3 mt-32">
                            <b-table-simple responsive bordered>
                                <b-thead class="text-uppercase text-bold-14">
                                    <b-tr>
                                        <b-th>NO</b-th>
                                        <b-th>NAMA BARANG</b-th>
                                        <b-th>KUANTITAS</b-th>
                                        <b-th>HARGA</b-th>
                                        <b-th>DISKON</b-th>
                                        <b-th>JUMLAH</b-th>
                                    </b-tr>
                                </b-thead>
                                <b-tbody class="text-regular-14">
                                    <b-tr>
                                        <b-td>1</b-td>
                                        <b-td>Stiker asturo warna biru</b-td>
                                        <b-td>15 Lembar</b-td>
                                        <b-td>0,00</b-td>
                                        <b-td>0,00</b-td>
                                        <b-td>0,00</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td>2</b-td>
                                        <b-td>Stiker asturo warna biru</b-td>
                                        <b-td>15 Lembar</b-td>
                                        <b-td>0,00</b-td>
                                        <b-td>0,00</b-td>
                                        <b-td>0,00</b-td>
                                    </b-tr>
                                    <b-tr class="font-weight-bold">
                                        <b-td colspan="5">Total: rupiah</b-td>
                                        <b-td>0,00</b-td>
                                    </b-tr>
                                </b-tbody>
                            </b-table-simple>
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
@import './../../../../../@core/css/main/main.min.css';

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
