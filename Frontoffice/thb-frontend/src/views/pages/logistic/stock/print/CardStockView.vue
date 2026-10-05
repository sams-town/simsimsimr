<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <div id="canvas" ref="canvas" class="mt-16 py-3" style="position: relative;">
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
                        <div class="text-center">
                            <p class="text-bold-16">KARTU KETERSEDIAAN</p>
                            <p class="text-regular-12">Tanggal 28-09-2021 s.d 14-07-2023</p>
                        </div>
                        <div class="row mx-0 mt-28">
                            <div class="col-6">
                                <table class="text-regular-14">
                                    <tr>
                                        <td class="first-cell">Kode Barang</td>
                                        <td class="px-3">:</td>
                                        <td>-</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Nama Barang</td>
                                        <td class="px-3">:</td>
                                        <td>-</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Satuan</td>
                                        <td class="px-3">:</td>
                                        <td>-</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Pabrik</td>
                                        <td class="px-3">:</td>
                                        <td>-</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <!-- /info-table -->
                        <div class="px-3 mt-32">
                            <b-table bordered responsive small head-variant="light" :fields="CardStockTable.fields"
                                :items="CardStockTable.items" thead-class="text-center text-bold-12"
                                tbody-tr-class="text-regular-12">
                            </b-table>
                        </div>
                        <!-- <div class="mt-64 d-flex justify-content-center">
                            <div class="col-lg-9">
                                <div class="row">
                                    <div class="col-6 text-center text-regular-14">
                                        <p>Yang Mengajukan</p>
                                        <p>Ka. Instalasi Farmasi</p>
                                        <p class="mt-72">(apt. Dra. Rita Hayati. Mars)</p>
                                    </div>
                                    <div class="col-6 text-center text-regular-14">
                                        <p>Mengetahui,</p>
                                        <p>Manajer Penunjang Medis</p>
                                        <p class="mt-72">(dr. Eko Rusdiyantoso Y.M,Kes)</p>
                                    </div>
                                </div>
                                <div class="mt-40 text-center text-regular-14">
                                    <p>Menyetujui,</p>
                                    <p>Wakil Direktur Pelayanan Medis</p>
                                    <p class="mt-72">(dr. Meizi Fachrizal A. M.Si)</p>
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
    data() {
        return {
            CardStockTable: {
                fields: [
                    { key: 'date', label: 'Tanggal' },
                    { key: 'no', label: 'No' },
                    { key: 'desc', label: 'Deskripsi' },
                    { key: 'batch', label: 'No Batch' },
                    { key: 'exp', label: 'Tanggal Kadaluarsa' },
                    { key: 'catalog_name', label: 'Nama Katalog' },
                    { key: 'unit_price', label: 'Harga Satuan' },
                    { key: 'receive', label: 'Terima' },
                    { key: 'out', label: 'keluar' },
                    { key: 'remain', label: 'Sisa' },
                    { key: 'flag', label: 'Flag Active Catalog' },
                ],
                items: [
                    {
                        date: '2021-11-01 10:32:56',
                        no: 'PNM/Med/11/2021/00001',
                        desc: 'Penerimaan Barang dari Gudang Farmasi di GUDANG UMUM, pada tanggal 2021-11-01 00:00:00, dengan nomor BA: PNM/Med/11/2021/00001',
                        batch: '2435',
                        exp: '2022-07-14',
                        catalog_name: 'Amplop Besar Coklat',
                        unit_price: '51700.00',
                        receive: '3.000.00',
                        out: '0.00',    
                        remain: '3.00',
                        flag: 'Active',
                    },
                    {
                        date: '2021-11-01 10:32:56',
                        no: 'PNM/Med/11/2021/00001',
                        desc: 'Penerimaan Barang dari Gudang Farmasi di GUDANG UMUM, pada tanggal 2021-11-01 00:00:00, dengan nomor BA: PNM/Med/11/2021/00001',
                        batch: '2435',
                        exp: '2022-07-14',
                        catalog_name: 'Amplop Besar Coklat',
                        unit_price: '51700.00',
                        receive: '3.000.00',
                        out: '0.00',    
                        remain: '3.00',
                        flag: 'Active',
                    },
                    
                ]
            },
        }
    },
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
