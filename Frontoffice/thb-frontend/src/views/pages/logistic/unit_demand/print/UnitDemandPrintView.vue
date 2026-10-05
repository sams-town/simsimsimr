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
                        <p class="text-center text-bold-16">PERMINTAAN BARANG UNIT</p>
                        <div class="row mx-0 mt-28">
                            <div class="col-6">
                                <table class="text-regular-14">
                                    <tr>
                                        <td class="first-cell">No Transaksi</td>
                                        <td class="px-3">:</td>
                                        <td>M04202111080001</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Tanggal Mutasi</td>
                                        <td class="px-3">:</td>
                                        <td>08-11-2021</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Gudang/ Satelit Tujuan</td>
                                        <td class="px-3">:</td>
                                        <td>marketing</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-6">
                                <table class="text-regular-14">
                                    <tr>
                                        <td class="first-cell">No Dokumen</td>
                                        <td class="px-3">:</td>
                                        <td>PMT/LGU/11/2021/00027</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Gudang Pengirim</td>
                                        <td class="px-3">:</td>
                                        <td>Gudang Umum</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="px-3 mt-32">
                            <b-table bordered responsive small head-variant="light" :fields="UDemandTable.fields"
                                :items="UDemandTable.items" thead-class="text-center text-bold-12"
                                tbody-tr-class="text-regular-12">
                            </b-table>
                        </div>
                        <div class="mt-64 d-flex justify-content-center">
                            <div class="col-lg-9">
                                <div class="row">
                                    <div class="col-6 text-center text-regular-14">
                                        <p>Yang Menyerahkan,</p>
                                        <!-- <p>Ka. Instalasi Farmasi</p> -->
                                        <p class="mt-72">(apt. Dra. Rita Hayati. Mars)</p>
                                    </div>
                                    <div class="col-6 text-center text-regular-14">
                                        <p>Yang Menerima,</p>
                                        <!-- <p>Manajer Penunjang Medis</p> -->
                                        <p class="mt-72">(dr. Eko Rusdiyantoso Y.M,Kes)</p>
                                    </div>
                                </div>
                                <!-- <div class="mt-40 text-center text-regular-14">
                                    <p>Menyetujui,</p>
                                    <p>Wakil Direktur Pelayanan Medis</p>
                                    <p class="mt-72">(dr. Meizi Fachrizal A. M.Si)</p>
                                </div> -->
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
    data() {
        return {
            UDemandTable: {
                fields: [
                    { key: 'no', label: 'No' },
                    { key: 'code', label: 'Kode' },
                    { key: 'goods_name', label: 'Nama Barang' },
                    { key: 'stock', label: 'Stok' },
                    { key: 'demand', label: 'Permintaan' },
                ],
                items: [
                    {
                        no: '1',
                        code: 'PCS0000000051',
                        goods_name: 'Lem kertas stick kenko',
                        stock: '10 Pcs',
                        demand: '1 Pcs',
                    },
                    {
                        no: '2',
                        code: 'PCS0000000051',
                        goods_name: 'Lem kertas stick kenko',
                        stock: '10 Pcs',
                        demand: '1 Pcs',
                    },
                ]
            },

        }
    },
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
