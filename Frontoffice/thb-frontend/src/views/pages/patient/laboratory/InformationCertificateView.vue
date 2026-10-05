<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <div class="canvas-header">
                        <img src="@/@core/assets/images/nurse/zeemar-logo.png" alt=""> <br>
                        <img src="@/@core/assets/images/nurse/vector-header.png" alt="">
                    </div>
                    <div class="canvas-body mt-32 px-40">
                        <section class="mt-28">
                            <p class="text-center text-bold-18 text-underline" style="text-decoration: underline; text-underline-offset: 3px;">SURAT KETERANGAN</p>
                            <p class="text-center text-semi-18 font-italic">LETTER OF STATEMENT</p>
                            <p class="text-center text-bold-16 mt-24">No : 00003 / Poli Gigi dan Mulut / 26 / V / 2023</p>
                        </section>
                        <section class="mt-28">
                            <p class="text-regular-14">Yang bertanda tangan di bawah ini menerangkan bahwa:</p>
                            <p class="text-regular-14 font-italic">The undersigned below explains that:</p>
                        </section>
                        <section class="mt-12 px-16">
                            <table class="col">
                                <tr>
                                    <td class="w-40 text-regular-14">1.	Nama / <span class="font-italic">Name</span></td>
                                    <td class="text-semi-14 text-uppercase">: WATI</td>
                                </tr>
                                <tr>
                                    <td class="w-40 text-regular-14">2.	NIK / Paspor / <span class="font-italic">Identification Number</span></td>
                                    <td class="text-semi-14 text-uppercase">: 081235454565</td>
                                </tr>
                                <tr>
                                    <td class="w-40 text-regular-14">3.	Tempat, tgl lahir / <span class="font-italic">Place, Date of Birth</span></td>
                                    <td class="text-semi-14">: <span class="text-uppercase">JAKARTA,</span> 03 Februari 2021</td>
                                </tr>
                                <tr>
                                    <td class="w-40 text-regular-14">4.	Jenis Kelamin / <span class="font-italic">Sex</span></td>
                                    <td class="text-semi-14 text-uppercase">: 	LAKI - LAKI .MALE</td>
                                </tr>
                                <tr>
                                    <td class="w-40 text-regular-14">5.	Pekerjaan / <span class="font-italic">Occupation</span></td>
                                    <td class="text-semi-14 text-uppercase">: 	LAKI - LAKI .MALE</td>
                                </tr>
                                <tr>
                                    <td class="w-40 text-regular-14">6.	Alamat / <span class="font-italic">Address</span></td>
                                    <td class="text-semi-14 text-uppercase">: 	Jl. Melati</td>
                                </tr>
                            </table>
                            <div class="mt-24">
                                <p class="text-regular-14">(selanjutnya disebut "Pasien")</p>
                                <p class="text-regular-14 font-italic">(here in after reffered to as "Patient")</p>
                            </div>
                        </section>
                        <section class="mt-24">
                            <p class="text-regular-14">Pasien ini telah menjalani pemeriksaan yang dilakukan oleh tim Rumah Sakit Cendana meliputi:</p>
                            <p class="text-regular-14 font-italic">Patient underwent the following examination(s)at Cendana Hospital</p>
                        </section>
                        <section class="mt-24">
                            <b-table-simple class="text-center">
                                <b-thead>
                                    <b-tr>
                                        <b-th class="py-1">
                                            <p class="text-regular-14">Tanggal Pemeriksaan</p>
                                            <p class="text-regular-14 font-italic">Date of Examination</p>
                                        </b-th>
                                        <b-th class="py-1">
                                            <p class="text-regular-14">Nomor LAB</p>
                                            <p class="text-regular-14 font-italic">LAB Number</p>
                                        </b-th>
                                        <b-th class="py-1">
                                            <p class="text-regular-14">Jenis Pemeriksaan</p>
                                            <p class="text-regular-14 font-italic">Type of Examination</p>
                                        </b-th>
                                        <b-th class="py-1">
                                            <p class="text-regular-14">Hasil Pemeriksaan</p>
                                            <p class="text-regular-14 font-italic">Result</p>
                                        </b-th>
                                    </b-tr>
                                </b-thead>
                                <b-tbody class="text-regular-14">
                                    <b-tr>
                                        <b-td>30 Januari 2023</b-td>
                                        <b-td>202301300002</b-td>
                                        <b-td class="text-uppercase">dARAH RUTIN LAKI-LAKI</b-td>
                                        <b-td>12</b-td>
                                    </b-tr>
                                </b-tbody>
                            </b-table-simple>
                        </section>
                        <section class="mt-24">
                            <p class="text-regular-14">Demikian Surat Keterangan ini dibuat untuk dipergunakan sebagaimana mestinya.</p>
                            <p class="text-regular-14 font-italic">This statement Letter is a verification of examination and results.</p>
                        </section>
                        <section class="d-flex w-100 mt-64">
                            <div class="ml-auto col-4 col-lg-5 text-center">
                                <p class="text-regular-14">Jakarta, 04 Mei 2023</p>
                                <img src="@/@core/assets/images/nurse/vector-sign.png" width="80px" alt="your-sign" class="my-2">
                                <p class="text-bold-14 text-uppercase">drg. Julia Putri Dewi S, MM</p>
                            </div>

                        </section>
                    </div>
                    <div class="canvas-footer mt-32">
                        <div class="d-flex justify-content-center">
                            <img src="./../../../../@core/assets/images/nurse/vector-footer.png" style="width: 100%;" alt="footer">
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
td{
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

.canvas-footer{
    position: absolute;
    bottom: 0;
    /* right: 0; */
}

</style>
