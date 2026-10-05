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
                    </div>
                    <section class="mt-20">
                        <p class="text-center text-bold-16">KARTU KETERSEDIAAN</p>
                        <p class="text-center text-regular-12">TANGGAL 01 January 1970</p>
                        <!-- /info-table -->
                        <div class="px-3 mt-32">
                            <b-table bordered :fields="AvailableTable.fields" :items="AvailableTable.items" thead-class="text-bold-14" tbody-tr-class="text-regular-14"></b-table>
                        </div>
                    </section>
                </div>
            </div>
        </b-container>
    </div>
</template>
<script>
export default {
    data() {
        return {
            AvailableTable: {
                fields: [
                    {key: 'no', label: 'No'},
                    {key: 'code', label: 'KODE'},
                    {key: 'drug_name', label: 'NAMA OBAT/ALKES'},
                    {key: 'unit', label: 'SATUAN'},
                    {key: 'qty', label: 'JUMLAH FISIK'},
                    {key: 'cost', label: 'HARGA PEROLEHAN'},
                    {key: 'total', label: 'TOTAL'},
                ],
                items: [
                    {
                        no: '1',
                        code: '-',
                        drug_name: 'ANADEX TABLET',
                        unit: 'Tablet',
                        qty: '135',
                        cost: '2.887,50',
                        total: '389.812,50',
                    },
                    {
                        no: '2',
                        code: '-',
                        drug_name: 'K-FILE 10 25 MM',
                        unit: 'Pcs',
                        qty: '0',
                        cost: '66.000,00',
                        total: '0,00',
                    },
                ]
            }
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
