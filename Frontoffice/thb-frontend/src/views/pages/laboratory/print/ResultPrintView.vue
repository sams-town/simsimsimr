<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button @click="closeWindow()" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas()" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <!-- <div class="px-3">
                        <img src="@/@core/assets/images/nurse/rshbh.png" style="width: 100%;" alt=""> <br>
                    </div> -->
                    <div class="row align-items-center pb-40" style="border-bottom: 2px solid black;">
                        <div class="col-2 text-center">
                            <img src="../../../../@core/assets/images/logo-thb-small.png" width="60%" alt="rs-logo"> <br>
                        </div>
                        <div class="col text-center">
                            <p class="text-bold-18">RS Taman Harapan Baru</p>
                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                            <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                        </div>
                        <div class="col-2 text-center">
                            <img src="../../../../@core/assets/images/lafki-logo.png" width="60%" alt="lafki-logo"> <br>
                        </div>
                    </div>
                    <div class="mt-20">
                        <p class="text-center text-bold-16">HASIL PEMERIKSAAN LABORATORIUM</p>
                        <div class="row mx-0 mt-28">
                            <div class="col-6">
                                <table class="text-regular-14">
                                    <tr>
                                        <td>No. Reg / RM <br> <i class="text-regular-12">Medical Record Number	</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{regData.code}} / {{regData.norm}}</td>
                                    </tr>
                                    <tr>
                                        <td>NIK <br> <i class="text-regular-12">Resident ID Number</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{regData.idcard}}</td>
                                    </tr>
                                    <tr>
                                        <td>Nama Pasien <br> <i class="text-regular-12">Pattient Name</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{regData.name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Alamat <br> <i class="text-regular-12">Address</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{regData.address}}</td>
                                    </tr>
                                    <tr>
                                        <td>Tgl. Permintaan <br> <i class="text-regular-12">Date Request</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{orderData.date_request}}</td>
                                    </tr>
                                    <tr>
                                        <td>Tgl. Hasil <br> <i class="text-regular-12">Date Reported</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{orderData.date_result}}</td>
                                    </tr>
                                    <!-- <tr>
                                        <td>Tgl. Cetak <br> <i class="text-regular-12">Print Out Date</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{orderData.date_request}}</td>
                                    </tr> -->
                                </table>
                            </div>
                            <div class="col-6">
                                <table class="text-regular-14">
                                    <tr>
                                        <td>No. Lab <br> <i class="text-regular-12">Lab Number</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{orderData.no_request}}</td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal Lahir <br> <i class="text-regular-12">Date of Birth</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{regData.birth_date}}</td>
                                    </tr>
                                    <tr>
                                        <td>Umur <br> <i class="text-regular-12">Age</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{parsed_age(this.regData.birth_date)}}</td>
                                    </tr>
                                    <tr>
                                        <td>Kelamin <br> <i class="text-regular-12">Sex</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{regData.sex_name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Dokter Pengirim <br> <i class="text-regular-12">Reffering Doctor</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{orderData.doctor_in_charge_name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Dokter Lab <br> <i class="text-regular-12">Lab Doctor</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">dr. Andini Triasti Siregar, M.ked, Sp.PK</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <!-- /info-table -->
                        <div class="px-3 mt-32">

                            <b-table bordered responsive head-variant="light" hover small :fields="resultFields"
                                :items="result.result_detail" tbody-tr-class="text-medium-14" class="mt-12 text-center">
                            </b-table>
                            <!-- <b-table-simple responsive bordered>
                                <b-thead class="text-regular-14 text-center">
                                    <b-tr>
                                        <b-th>JENIS PEMERIKSAAN <br> <i class="text-regular-12">Type of examination</i></b-th>
                                        <b-th>HASIL <br> <i class="text-regular-12">Result</i></b-th>
                                        <b-th>NILAI RUJUKAN <br> <i class="text-regular-12">Reference Range</i></b-th>
                                        <b-th>SATUAN <br> <i class="text-regular-12">unit</i></b-th>
                                    </b-tr>
                                </b-thead>
                                <b-tbody class="text-regular-14">
                                    <b-tr>
                                        <b-td colspan="4" class="font-weight-bold">DARAH LENGKAP PEREMPUAN</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td>- HEMOGLOBIN (HB)</b-td>
                                        <b-td>12</b-td>
                                        <b-td>11.7-15.5</b-td>
                                        <b-td>g/dL</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td>- HEMATOKRIT (HT)</b-td>
                                        <b-td>35</b-td>
                                        <b-td>35.0-47.0</b-td>
                                        <b-td>%</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td colspan="4" class="font-weight-bold">HITUNG JENIS</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td>- BASOFIL</b-td>
                                        <b-td>1</b-td>
                                        <b-td>0-1</b-td>
                                        <b-td>%</b-td>
                                    </b-tr>
                                </b-tbody>
                            </b-table-simple> -->
                        </div>
                        <!-- <div class="text-regular-14 px-3">
                            <p>Kesimpulan : Hasil baik tidak ada kelainan</p>
                            <p class="mt-1">Conclusion : Good results, no abnormalities</p>
                            <p class="mt-1">Saran/ Recommended action :</p>
                            <p class="mt-1">Catatan/ Notes : Normal</p>
                        </div>
                        <div class="d-flex mt-32 justify-content-end">
                            <div class="text-center text-regular-12">
                                <img src="./../../../../@core/assets/images/laboratory/barcode.png" style="width: 140px;" alt="footer">
                                <p>Digital Signed Code :</p>
                                <p>914ec36a8c90946b02bcbb9d9f21ee762f55c0ed</p>
                            </div>
                        </div> -->
                    </div>
                    <!-- <div class="canvas-footer mt-32">
                        <div class="d-flex justify-content-center">
                            <img src="./../../../../@core/assets/images/nurse/vector-footer.png" style="width: 100%;" alt="footer">
                        </div>
                    </div> -->
                </div>
            </div>
        </b-container>
    </div>
</template>
<script>
import moment from 'moment';
export default {
    data() {
        return {
            laboratory_action: {
                field: [
                    { key: 'print_all', label: 'PRINT ALL' },
                    { key: 'costing_id', label: 'TGL. PERMINTAAN' },
                    { key: 'costing_name', label: 'TINDAKAN LABORATORIUM' },
                    { key: 'var_1', label: 'HARGA TINDAKAN' },
                    { key: 'action', label: 'TINDAKAN' },
                    { key: 'bill_status', label: 'STATUS BILLING' },
                ],
                // item: [
                //     {
                //         request_date: '24-03-2023 11:04:05',
                //         costing_name: 'Hematologi Lengkap',
                //         var_1: '104.500,00',
                //         bill_status: 'Sudah Melunasi Pembayaran',
                //     },
                // ],
            },
            orderData:{},
            result:{},
            regData:{},
            resultFields: [
                { key: 'Parameter', label: 'Parameter' },
                { key: 'hasil', label: 'Hasil' },
                { key: 'flag', label: 'Flag' },
                { key: 'n_rujukan', label: 'Nilai Rujukan' },
                { key: 'satuan', label: 'Satuan' },
                { key: 'metoda', label: 'Metoda' },
            ]
        }
    },
    filters:{
        currency(value) {
            return new Intl.NumberFormat("en-US", { style: "currency", currency: "IDR" }).format(value)
        }
    },
    methods: {
        parsed_age(input_date) {
            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const currentDate = moment();

            const years = currentDate.diff(birthDate, 'years');
            birthDate.add(years, 'years');
            const months = currentDate.diff(birthDate, 'months');
            birthDate.add(months, 'months');
            const days = currentDate.diff(birthDate, 'days');

            return `${years} th ${months} bln ${days} hr`;
        },
        handleKeyPress(event) {
            if (event.ctrlKey && event.key === 'p') {
                event.preventDefault();
                this.printCanvas();
            }
        },
        printCanvas() {
            const canvas = this.$refs.canvas;
            const originalContents = document.body.innerHTML;
            document.body.innerHTML = canvas.innerHTML;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        },
        closeWindow() {
            window.close()
        },
        // printCanvas() {
        //     const canvas = this.$refs.canvas;
        //     // Save the current contents of the body to a variable
        //     const originalContents = document.body.innerHTML;
        //     // Set the body contents to the contents of the "canvas" div
        //     document.body.innerHTML = canvas.innerHTML;
        //     // Call the print method of the current window
        //     window.print();
        //     // Restore the original contents of the body
        //     document.body.innerHTML = originalContents;
        // },
        refresh () {
            router.go(router.currentRoute)
        },
        getData (id) {
			this.$store.dispatch('laboratory/getByIdOrder', id).then(data => {
                this.orderData = data[0]
                this.regData = data[0].regData
                this.$store.dispatch('laboratory/getResultById', id).then(result => {
                    this.result = result[0]
                    console.log('this.result', this.result);
                })
            })
        }
    },
    mounted () {
        window.addEventListener('keydown', this.handleKeyPress);

        if (this.$route.params.id) {
            // this.show = true
            this.id = this.$route.params.id
            this.getData(this.$route.params.id)
        }
    },
    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },
}
</script>
<style>
@import './../../../../@core/css/main/main.min.css';
@media print {
    @page {
        size: A4;
        margin: 16mm;
    }
}
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

/* #canvas {
    width: 210mm;
    width: 21.2cm !important;
    height: 29.7cm !important;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
} */

/* .canvas-header {
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
} */

.w-40 {
    width: 40%;
}

.canvas-footer {
    position: absolute;
    bottom: 0;
    /* right: 0; */
}
</style>
