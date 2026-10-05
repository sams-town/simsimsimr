<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <div class="px-3">
                        <img src="@/@core/assets/images/nurse/rshbh.png" style="width: 100%;" alt=""> <br>
                    </div>
                    <div class="mt-20">
                        <p class="text-center text-bold-16">HASIL</p>
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
                                        <td style="vertical-align:top;">{{regData.citizen_id}}</td>
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
                                        <td>Tgl. Permintaan <br> <i class="text-regular-12">Date Reported</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{orderData.date_request}}</td>
                                    </tr>
                                    <tr>
                                        <td>Tgl. Hasil <br> <i class="text-regular-12">Date Reported</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{orderData.date_request}}</td>
                                    </tr>
                                    <tr>
                                        <td>Tgl. Cetak <br> <i class="text-regular-12">Print Out Date</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{orderData.date_request}}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-6">
                                <table class="text-regular-14">
                                    <tr>
                                        <td>No. Fisioterapi <br> <i class="text-regular-12">Fisioterapi Number</i></td>
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
                                        <td style="vertical-align:top;"></td>
                                    </tr>
                                    <tr>
                                        <td>Kelamin <br> <i class="text-regular-12">Sex</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{regData.sex_name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Dokter Pengirim <br> <i class="text-regular-12">Reffering Doctor</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{orderData.employee_doctor_name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Dokter Fisioterapi <br> <i class="text-regular-12">Fisioterapi Doctor</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">dr. Cici Julia Sri Dewi, Sp.PK</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <!-- /info-table -->
                        <div class="text-regular-14 px-3">
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
                        </div>
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
            regData:{}
        }
    },
    filters:{
        currency(value) {
            return new Intl.NumberFormat("en-US", { style: "currency", currency: "IDR" }).format(value)
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
        },
        refresh () {
            router.go(router.currentRoute)
        },
        getData (id) {
			this.$store.dispatch('laboratory/getByIdOrder', id).then(data => {
                this.orderData = data[0]
                this.regData = data[0].regData
            })
        }
    },
    mounted () {
        // if (this.$route.params.id) {
        //     // this.show = true
        //     this.id = this.$route.params.id
        //     this.getData(this.$route.params.id)
        // }
    }
}
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
