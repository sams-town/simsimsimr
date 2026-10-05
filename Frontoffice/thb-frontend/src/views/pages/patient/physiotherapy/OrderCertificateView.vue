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
                        <p class="text-center text-bold-16">ORDER LABORATORIUM</p>
                        <div class="row mx-0 mt-28">
                            <div class="col-6">
                                <table class="text-regular-14">
                                    <tr>
                                        <td class="first-cell">No. Reg / RM</td>
                                        <td class="px-3">:</td>
                                        <td>202210261000003 / 00013596</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Nama Pasien</td>
                                        <td class="px-3">:</td>
                                        <td>Wati</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Tgl. Permintaan</td>
                                        <td class="px-3">:</td>
                                        <td>26-10-2022 16:38:08</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Tgl. Hasil</td>
                                        <td class="px-3">:</td>
                                        <td>30-01-2023 10:26:45</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Alamat</td>
                                        <td class="px-3">:</td>
                                        <td>Jl. Melati</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-6">
                                <table class="text-regular-14">
                                    <tr>
                                        <td class="first-cell">No. Lab</td>
                                        <td class="px-3">:</td>
                                        <td>202301300002</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Tanggal Lahir</td>
                                        <td class="px-3">:</td>
                                        <td>03-02-2021</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Umur</td>
                                        <td class="px-3">:</td>
                                        <td>2 Tahun 3 Bulan 1 Hari</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Kelamin</td>
                                        <td class="px-3">:</td>
                                        <td>LAKI - LAKI</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell"> Dokter Pengirim</td>
                                        <td class="px-3">:</td>
                                        <td>simrs, Adm</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <!-- /info-table -->
                        <div class="px-3 mt-32">
                            <b-table-simple responsive bordered>
                                <b-thead class="text-uppercase text-center">
                                    <b-tr>
                                        <b-th>Jenis Pemeriksaan</b-th>
                                    </b-tr>
                                </b-thead>
                                <b-tbody>
                                    <b-tr>
                                        <b-td class="py-2">-Seri DHF ( Hb, L,Ht,Tr )</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td class="py-2">-Seri DHF ( Hb, L,Ht,Tr )</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td class="py-2">-Seri DHF ( Hb, L,Ht,Tr )</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td class="py-2">-Seri DHF ( Hb, L,Ht,Tr )</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td class="py-2">-Seri DHF ( Hb, L,Ht,Tr )</b-td>
                                    </b-tr>
                                </b-tbody>
                            </b-table-simple>
                        </div>
                    </div>
                    <div class="canvas-footer mt-32">
                        <div class="d-flex justify-content-center">
                            <img src="./../../../../@core/assets/images/nurse/vector-footer.png" style="width: 100%;"
                                alt="footer">
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
