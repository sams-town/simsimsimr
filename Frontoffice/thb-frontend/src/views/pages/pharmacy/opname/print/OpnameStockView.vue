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
                        <div class="d-flex align-items-center">
                            <img src="@/@core/assets/images/nurse/zeemar-logo.png" alt=""> <br>
                            <div class="text-semi-12">
                                <p>RS</p>
                                <p>xxxxxxxxx</p>
                                <p>Telp. (0000) 000000</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-20">
                        <p class="text-center text-bold-16">PERINCIAN LEMBAR STOCK OPNAME</p>
                        <div class="col mt-16">
                            <table class="text-regular-12">
                                <tr>
                                    <td>Unit Penyimpanan</td>
                                    <td class="px-3">:</td>
                                    <td>Alamanda</td>
                                </tr>
                                <tr>
                                    <td>Keterangan</td>
                                    <td class="px-3">:</td>
                                    <td>SO Bulan Oktober 2021 Unit: Alamanda</td>
                                </tr>
                                <tr>
                                    <td>Tanggal Aktifasi</td>
                                    <td class="px-3">:</td>
                                    <td>2021-11-01 11:56:42</td>
                                </tr>
                                <tr>
                                    <td>Tanggal Stok Opname</td>
                                    <td class="px-3">:</td>
                                    <td>2021-10-30 13:37:35 s/d 2021-11-02 13:37:35</td>
                                </tr>
                            </table>
                        </div>
                        <!-- /info-table -->
                        <div class="px-3 mt-32">
                            <b-table-simple small bordered>
                                <b-thead class="text-uppercase text-bold-10 text-center">
                                    <b-tr>
                                        <b-th rowspan="2">NO</b-th>
                                        <b-th rowspan="2" style="min-width: 170px;">NAMA BARANG</b-th>
                                        <b-th rowspan="2">SAT</b-th>
                                        <b-th colspan="11">PERHITUNGAN STOK OPNAME</b-th>
                                        <b-th rowspan="2">NO</b-th>
                                        <b-th rowspan="2">OPT</b-th>
                                        <b-th rowspan="2">UPDT</b-th>
                                    </b-tr>
                                    <b-tr>
                                        <b-th>BATCH</b-th>
                                        <b-th>EXP</b-th>
                                        <b-th>SALDO AWAL</b-th>
                                        <b-th>PENERIMAAN</b-th>
                                        <b-th>PEMAKAIAN</b-th>
                                        <b-th>JML ADM</b-th>
                                        <b-th>JML FISIK</b-th>
                                        <b-th>SELISIH</b-th>
                                        <b-th>HARGA</b-th>
                                        <b-th>NILAI</b-th>
                                    </b-tr>
                                </b-thead>
                                <b-tbody class="text-regular-10">
                                    <b-tr>
                                        <b-td>1</b-td>
                                        <b-td>ALCOHOL SWAB</b-td>
                                        <b-td>Pcs</b-td>
                                        <b-td>072104</b-td>
                                        <b-td>31-07-2026</b-td>
                                        <b-td>0</b-td>
                                        <b-td>0</b-td>
                                        <b-td>0</b-td>
                                        <b-td>20</b-td>
                                        <b-td>0</b-td>
                                        <b-td>0</b-td>
                                        <b-td>20</b-td>
                                        <b-td>172,00</b-td>
                                        <b-td>0,00</b-td>
                                        <b-td>1</b-td>
                                        <b-td>Hani Ramadani</b-td>
                                        <b-td>01/11/2021 13:09:38</b-td>
                                    </b-tr>
                                </b-tbody>
                            </b-table-simple>
                        </div>
                        <div class="col-lg mt-32 text-regular-12">
                            <div class="d-flex">
                                <div class="col-3 ml-auto">
                                    <p class="text-center">DI TERIMA OLEH:</p>
                                    <p class="text-center mt-1">PARAF:</p>
                                    <p class="text-center mt-72">(........................................)</p>
                                </div>
                                <div class="col-3">
                                    <p class="text-center">DI BERIKAN OLEH:</p>
                                    <p class="text-center mt-1">PARAF:</p>
                                    <p class="text-center mt-72">(........................................)</p>
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
    width: 100% !important;
    height: 21cm !important;
    /* width: 21.2cm !important;
    height: 29.7cm !important; */
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
