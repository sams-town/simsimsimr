<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex" ref="print_button">
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
                        <section>
                            <p class="text-center text-bold-16">RINCIAN BILLING SEMENTARA</p>
                            <div class="row mx-0 mt-28">
                                <div class="col-6">
                                    <table class="text-regular-14">
                                        <tr>
                                            <td class="first-cell">No. RM</td>
                                            <td class="px-3">:</td>
                                            <td>00013596</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">No. Pendaftaran</td>
                                            <td class="px-3">:</td>
                                            <td>202210261000003</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Nama Pasien</td>
                                            <td class="px-3">:</td>
                                            <td>Wati</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Jenis Kelamin / Umur</td>
                                            <td class="px-3">:</td>
                                            <td>LAKI - LAKI / 17 Tahun</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Jaminan</td>
                                            <td class="px-3">:</td>
                                            <td>Pribadi</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Poli</td>
                                            <td class="px-3">:</td>
                                            <td>Poli Gigi dan Mulut</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-6">
                                    <table class="text-regular-14">
                                        <tr>
                                            <td class="first-cell">No. Invoice</td>
                                            <td class="px-3">:</td>
                                            <td>0000033836</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Tgl. / Jam Masuk</td>
                                            <td class="px-3">:</td>
                                            <td>24-03-2023 10:16:36</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Alamat</td>
                                            <td class="px-3">:</td>
                                            <td>Jl. Melati</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Dokter</td>
                                            <td class="px-3">:</td>
                                            <td>dr. Vicky Vendy</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </section>
                        <section class="mt-24">
                            <b-table-simple class="text-center">
                                <b-thead class="text-bold-14">
                                    <b-tr class="bg-neutral-100">
                                        <b-th>No</b-th>
                                        <b-th>Tanggal</b-th>
                                        <b-th>Tindakan</b-th>
                                        <b-th>Dokter</b-th>
                                        <b-th>Harga</b-th>
                                        <b-th>QTY</b-th>
                                        <b-th>Sub Total</b-th>
                                    </b-tr>
                                </b-thead>
                                <b-tbody class="text-regular-14">
                                    <b-tr class="text-left font-weight-bold bg-neutral-100">
                                        <b-td colspan="7">Rincian Dokter</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td>{{ EklaimTable.row_doctor[0].no }}</b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_doctor[0].date"></b-form-input></b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_doctor[0].action"></b-form-input></b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_doctor[0].doctor"></b-form-input></b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_doctor[0].price"></b-form-input></b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_doctor[0].qty"></b-form-input></b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_doctor[0].subTotal"></b-form-input></b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td>{{ EklaimTable.row_doctor[1].no }}</b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_doctor[1].date"></b-form-input></b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_doctor[1].action"></b-form-input></b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_doctor[1].doctor"></b-form-input></b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_doctor[1].price"></b-form-input></b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_doctor[1].qty"></b-form-input></b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_doctor[1].subTotal"></b-form-input></b-td>
                                    </b-tr>
                                    <b-tr class="text-left font-weight-bold bg-neutral-100">
                                        <b-td colspan="7">Rincian Laboratorium</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td>{{ EklaimTable.row_laboratory[0].no }}</b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_laboratory[0].date"></b-form-input></b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_laboratory[0].action"></b-form-input></b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_laboratory[0].doctor"></b-form-input></b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_laboratory[0].price"></b-form-input></b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_laboratory[0].qty"></b-form-input></b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_laboratory[0].subTotal"></b-form-input></b-td>
                                    </b-tr>
                                    <b-tr class="text-left font-weight-bold bg-neutral-100">
                                        <b-td colspan="7">Rincian Radiologi</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td>{{ EklaimTable.row_radiology[0].no }}</b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_radiology[0].date"></b-form-input></b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_radiology[0].action"></b-form-input></b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_radiology[0].doctor"></b-form-input></b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_radiology[0].price"></b-form-input></b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_radiology[0].qty"></b-form-input></b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_radiology[0].subTotal"></b-form-input></b-td>
                                    </b-tr>
                                    <b-tr class="text-left font-weight-bold bg-neutral-100">
                                        <b-td colspan="7">Pemakaian Alat</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td>{{ EklaimTable.row_tool[0].no }}</b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_tool[0].date"></b-form-input></b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_tool[0].action"></b-form-input></b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_tool[0].doctor"></b-form-input></b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_tool[0].price"></b-form-input></b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_tool[0].qty"></b-form-input></b-td>
                                        <b-td><b-form-input size="sm" v-model="EklaimTable.row_tool[0].subTotal"></b-form-input></b-td>
                                    </b-tr>
                                    <b-tr class="text-left font-weight-bold bg-neutral-100">
                                        <b-td colspan="7">Rincian Administrasi</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td>1</b-td>
                                        <b-td>ADM PASIEN LAMA RAWAT JALAN 31/03/2023</b-td>
                                        <b-td>Rp.15.000,00</b-td>
                                        <b-td>1</b-td>
                                        <b-td>0.00</b-td>
                                        <b-td colspan="2" class="text-right">Rp.15.000,00</b-td>
                                    </b-tr>
                                    <b-tr class="text-right text-regular-14">
                                        <b-td colspan="6">Sub Total</b-td>
                                        <b-td colspan="1">Rp.3.475.000,00</b-td>
                                    </b-tr>
                                    <b-tr class="text-right text-regular-14">
                                        <b-td colspan="6">Diskon</b-td>
                                        <b-td colspan="1">Rp.0,00</b-td>
                                    </b-tr>
                                    <b-tr class="text-right text-regular-14">
                                        <b-td colspan="6">Voucher</b-td>
                                        <b-td colspan="1">Rp.0,00</b-td>
                                    </b-tr>
                                    <b-tr class="text-right text-regular-14">
                                        <b-td colspan="6">Total</b-td>
                                        <b-td colspan="1">Rp.3.475.000,00</b-td>
                                    </b-tr>
                                </b-tbody>
                            </b-table-simple>
                        </section>
                        <section class="d-flex w-100 mt-64">
                            <div class="ml-auto col-4 col-lg-5 text-center">
                                <p class="text-regular-14">DKI JAKARTA, 05-05-2023</p>
                                <p class="text-regular-14">Dibuat Oleh,</p>
                                <!-- <img src="@/@core/assets/images/nurse/vector-sign.png" width="80px" alt="your-sign" class="my-2"> -->
                                <p class="text-bold-14 text-uppercase mt-64">simrs, Adm</p>
                            </div>

                        </section>
                    </div>
                    <div class="canvas-footer mt-32">
                        <div class="d-flex justify-content-center">
                            <img src="../../../../@core/assets/images/nurse/vector-footer.png" style="width: 100%;" alt="footer">
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
            EklaimTable: {
                row_doctor: [
                    {
                        no: '1',
                        date: '31-03-2023' ,
                        action: 'Konsultasi Dokter Gigi',
                        doctor: 'simrs, Adm',
                        price: '100.000,00',
                        qty: '1',
                        subTotal: '100.000,00',
                    },
                    {
                        no: '2',
                        date: '31-03-2023' ,
                        action: 'Ekstraksi Gigi Dewasa Sulit',
                        doctor: 'simrs, Adm',
                        price: '400.000,00',
                        qty: '1',
                        subTotal: '400.000,00',
                    },
                ],
                row_laboratory: [
                    {
                        no: '1',
                        date: '31-03-2023' ,
                        action: 'Hematologi Lengkap',
                        doctor: '',
                        price: '104500.00',
                        qty: '1',
                        subTotal: '104500.00',
                    },
                ],
                row_radiology: [
                    {
                        no: '1',
                        date: '31-03-2023' ,
                        action: 'CT Scan Orbita Kontras ',
                        doctor: '',
                        price: '2445500.00',
                        qty: '1',
                        subTotal: '2445500.00',
                    },
                ],
                row_tool: [
                    {
                        no: '1',
                        date: '31-03-2023' ,
                        action: 'Udara Tekan per 12 jam ',
                        doctor: '',
                        price: '410000.00',
                        qty: '1',
                        subTotal: '410000.00',
                    },
                ],

            }
        }
    },
    methods: {
        printCanvas() {
            const canvas = this.$refs.canvas;
            this.$refs.print_button.classList.remove('d-flex');
            this.$refs.print_button.classList.add('d-none');
            window.print();
            this.$refs.print_button.classList.add('d-flex');
            this.$refs.print_button.classList.remove('d-none');
        }
    }
};

</script>
<style>
@import '../../../../@core/css/main/main.min.css';
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
    width: 33cm !important;
    /* width: 21.2cm !important; */
    /* height: 29.7cm !important; */
    /* box-shadow: 0 0 5px rgba(0, 0, 0, 0.1); */
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
