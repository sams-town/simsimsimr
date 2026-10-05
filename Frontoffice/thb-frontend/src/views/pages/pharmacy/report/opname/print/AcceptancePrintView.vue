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
                        <p class="text-center text-bold-16">LAPORAN PENERIMAAN FARMASI</p>
                        <div class="col mt-16">
                            <table class="text-regular-12">
                                <tr>
                                    <td>Katalog</td>
                                    <td class="px-3">:</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <td>Gudang</td>
                                    <td class="px-3">:</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <td>Supplier</td>
                                    <td class="px-3">:</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <td>Per Tanggal</td>
                                    <td class="px-3">:</td>
                                    <td>-</td>
                                </tr>
                            </table>
                        </div>
                        <!-- /info-table -->
                        <div class="px-3 mt-32">
                            <b-table-simple small bordered>
                                <b-thead class="text-uppercase text-bold-10 text-center">
                                    <b-tr>
                                        <b-th colspan="7">Perincian Data Obat</b-th>
                                        <b-th colspan="14">Perincian Harga</b-th>
                                    </b-tr>
                                    <b-tr>
                                        <b-th>NO</b-th>
                                        <b-th>PRICIPAL</b-th>
                                        <b-th>KATALOG</b-th>
                                        <b-th>SEDIAAN</b-th>
                                        <b-th>NO BA PENERIMAAN</b-th>
                                        <b-th>GUDANG</b-th>
                                        <b-th>SUPPLIER</b-th>
                                        <b-th>TANGGAL PENERIMAAN</b-th>
                                        <b-th>HP ITEM</b-th>
                                        <b-th>JUMLAH ITEM</b-th>
                                        <b-th>TOTAL HP * JI</b-th>
                                        <b-th>% DISK</b-th>
                                        <b-th>JUMLAH DISK</b-th>
                                        <b-th>HARGA SETELAH DISK</b-th>
                                        <b-th>PPN</b-th>
                                        <b-th>HPA</b-th>
                                        <b-th>HJD</b-th>
                                        <b-th>TIPE PENJUALAN</b-th>
                                        <b-th>MARGIN</b-th>
                                        <b-th>HJA</b-th>
                                        <b-th>HET</b-th>
                                    </b-tr>
                                </b-thead>
                                <b-tbody class="text-regular-10">
                                    <b-tr>
                                        <b-td>-</b-td>
                                        <b-td>-</b-td>
                                        <b-td>-</b-td>
                                        <b-td>-</b-td>
                                        <b-td>-</b-td>
                                        <b-td>-</b-td>
                                        <b-td>-</b-td>
                                        <b-td>-</b-td>
                                        <b-td>-</b-td>
                                        <b-td>-</b-td>
                                        <b-td>-</b-td>
                                        <b-td>-</b-td>
                                        <b-td>-</b-td>
                                        <b-td>-</b-td>
                                        <b-td>-</b-td>
                                        <b-td>-</b-td>
                                        <b-td>-</b-td>
                                        <b-td>-</b-td>
                                        <b-td>-</b-td>
                                        <b-td>-</b-td>
                                        <b-td>-</b-td>
                                    </b-tr>
                                </b-tbody>
                            </b-table-simple>
                        </div>
                        <!-- <div class="col-lg mt-32 text-regular-12">
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
                        </div> -->
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
@import './../../../../../../@core/css/main/main.min.css';

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
    height: 100% !important;
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
