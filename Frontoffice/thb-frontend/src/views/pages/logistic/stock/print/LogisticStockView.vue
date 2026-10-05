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
                    </div>
                    <div class="mt-20">
                        <div class="text-center">
                            <p class="text-bold-16">PERSEDIAAN LOGISTIK UMUM</p>
                            <p class="text-regular-12">Tanggal 28-09-2021 s.d 14-07-2023</p>
                        </div>
                        <div class="px-3 mt-32">
                            <b-table-simple small bordered head-variant="light">
                                <b-thead class="text-bold-12">
                                    <b-tr>
                                        <b-th>No</b-th>
                                        <b-th>Kode</b-th>
                                        <b-th>Nama Obat/Alkes</b-th>
                                        <b-th>Satuan</b-th>
                                        <b-th>Jumlah Fisik</b-th>
                                        <b-th>Harga Perolehan</b-th>
                                        <b-th>Total</b-th>
                                    </b-tr>
                                </b-thead>
                                <b-tbody class="text-regular-12">
                                    <!-- loop this row line -->
                                    <b-tr>
                                        <b-td>1</b-td>
                                        <b-td>-</b-td>
                                        <b-td>Air Freshener Stella Semprot</b-td>
                                        <b-td>Botol</b-td>
                                        <b-td>40</b-td>
                                        <b-td>26.400,00</b-td>
                                        <b-td>1.056.000,00</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td colspan="7" class="text-right font-weight-bold">3.370.400,00</b-td>
                                    </b-tr>
                                </b-tbody>
                            </b-table-simple>
                            <!-- <b-table bordered responsive small head-variant="light" :fields="LogStockTable.fields"
                                :items="LogStockTable.items" thead-class="text-center text-bold-12"
                                tbody-tr-class="text-regular-12">
                            </b-table> -->
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
            // LogStockTable: {
            //     fields: [
            //         { key: 'no', label: 'No' },
            //         { key: 'code', label: 'Kode' },
            //         { key: 'alkes_name', label: 'Nama Obat / Alkes' },
            //         { key: 'unit', label: 'Satuan' },
            //         { key: 'real_amount', label: 'jumlah Fisik' },
            //         { key: 'price_in', label: 'Harga Perolehan' },
            //         { key: 'total', label: 'Total' },
            //     ],
            //     items: [
            //         {
            //             code: '2021-11-01 10:32:56',
            //             no: 'PNM/Med/11/2021/00001',
            //             alkes_name: 'Penerimaan Barang dari Gudang Farmasi di GUDANG UMUM, pada tanggal 2021-11-01 00:00:00, dengan nomor BA: PNM/Med/11/2021/00001',
            //             unit: '2435',
            //             real_amount: '2022-07-14',
            //             price_in: 'Amplop Besar Coklat',
            //             total: '51700.00',
            //         },                    
            //     ]
            // },
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
    width: 21cm !important;
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
