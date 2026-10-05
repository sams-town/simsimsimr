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
                        <p class="text-center text-bold-16">PENERIMAAN LOGISTIK</p>
                        <div class="row mx-0 mt-28">
                            <div class="col-6">
                                <table class="text-regular-14">
                                    <tr>
                                        <td class="first-cell">NO. PENERIMAAN</td>
                                        <td class="px-3">:</td>
                                        <td>R04202111000001</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">GUDANG</td>
                                        <td class="px-3">:</td>
                                        <td>GUDANG UMUM</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <!-- /info-table -->
                        <div class="px-3 mt-32">
                            <b-table bordered responsive small head-variant="light" :fields="LogisticAccTable.fields"
                                :items="LogisticAccTable.items" thead-class="text-bold-12"
                                tbody-tr-class="text-regular-12" class="text-center">
                            </b-table>
                        </div>
                        <div class="mt-64 d-flex justify-content-center">
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
            LogisticAccTable: {
                fields: [
                    { key: 'no', label: 'No' },
                    { key: 'code', label: 'Kode' },
                    { key: 'goods_name', label: 'Nama Barang' },
                    { key: 'batch', label: 'Batch' },
                    { key: 'exp', label: 'Exp' },
                    { key: 'unit', label: 'Unit Satuan    ' },
                    { key: 'unit_price', label: 'Harga Satuan' },
                    { key: 'discount', label: 'Diskon' },
                    { key: 'amount', label: 'Jumlah' },
                    { key: 'item_price', label: 'Harga Item' },
                    { key: 'hp', label: 'HP' },
                    { key: 'hja_non', label: 'HJA non' },
                    { key: 'hja_free', label: 'HJA Bebas' },
                    { key: 'hjs_class3', label: 'HJA Kelas 3' },
                    { key: 'hjs_class2', label: 'HJA Kelas 2' },
                    { key: 'hjs_class3', label: 'HJA Kelas 1' },
                    { key: 'hjs_vip', label: 'HJA VIP' },
                    { key: 'hjs_svip', label: 'HJA SVIP' },
                ],
                items: [
                    {
                        no: 1,
                        code: '-',
                        goods_name: 'Amplop Coklat A4',
                        batch: '454655',
                        exp: '2021-11-01',
                        unit: '5.00',
                        unit_price: '45000.00',
                        discount: '0.00',
                        amount: '225000',
                        item_price: '45000.00',    
                        hp: '49500.00',
                        hja_non: '64845.00',
                        hja_free: '64350.00',
                        hjs_class3: '64350.00',
                        hjs_class2: '66825.00',
                        hjs_class1: '66825.00',
                        hjs_vip: '69300.00',
                        hjs_svip: '71775.00',
                    },
                    {
                        no: 2,
                        code: '-',
                        goods_name: 'Amplop Coklat A4',
                        batch: '454655',
                        exp: '2021-11-01',
                        unit: '5.00',
                        unit_price: '45000.00',
                        discount: '0.00',
                        amount: '225000',
                        item_price: '45000.00',    
                        hp: '49500.00',
                        hja_non: '64845.00',
                        hja_free: '64350.00',
                        hjs_class3: '64350.00',
                        hjs_class2: '66825.00',
                        hjs_class1: '66825.00',
                        hjs_vip: '69300.00',
                        hjs_svip: '71775.00',
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
