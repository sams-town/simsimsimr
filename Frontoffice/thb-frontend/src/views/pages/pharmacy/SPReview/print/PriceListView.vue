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
                        <p class="text-center text-bold-16">PENERIMAAN FARMASI</p>
                        <p class="text-center text-bold-14 mt-10">LIST HARGA PENERIMAAN / HARGA JUAL FARMASI</p>
                        <p class="text-regular-12 text-center">Tanggal 30-05-2023 s.d 30-05-2023</p>
                        <!-- /info-table -->
                        <div class="px-3 mt-32">
                            <b-table-simple responsive bordered>
                                <b-thead class="text-uppercase text-bold-12 text-center">
                                    <b-tr>
                                        <b-th rowspan="2">Tanggal</b-th>
                                        <b-th rowspan="2">Nama Obat / Alkes</b-th>
                                        <b-th rowspan="2">Nomor Penerimaan</b-th>
                                        <b-th rowspan="2">Gudang</b-th>
                                        <b-th rowspan="2">HNA</b-th>
                                        <b-th rowspan="2">Disc</b-th>
                                        <b-th rowspan="2">HP</b-th>
                                        <b-th colspan="7">HARGA JUAL APOTIK</b-th>
                                        <b-th rowspan="2">TGL BERLAKU</b-th>
                                        <b-th rowspan="2">TGL BERAKHIR</b-th>
                                    </b-tr>
                                    <b-tr>
                                        <b-th>NON</b-th>
                                        <b-th>BEBAS</b-th>
                                        <b-th>KELAS 3</b-th>
                                        <b-th>KELAS 2</b-th>
                                        <b-th>KELAS 1</b-th>
                                        <b-th>VIP</b-th>
                                        <b-th>SVIP</b-th>
                                    </b-tr>
                                </b-thead>
                                <b-tbody class="text-regular-12">
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
    width: 40cm !important;
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
