<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <div class="canvas-header px-3">
                        <div class="row align-items-center">
                            <div class="col-6">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="./../../../../@core/assets/images/logo-thb-small.png" width="80px" alt="rshbh-logo"> <br>
                                    <div class="text-semi-12 px-16">
                                        <p>RS Taman Harapan Baru</p>
                                        <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex flex-column text-right h-100 align-items-end justify-content-center">
                                    <p class="text-regular-12">POLI / RUANGAN:</p>
                                    <p class="text-bold-16">{{ this.data.health_facility_item_name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-20">
                        <p class="text-center text-bold-12" >Nama Dokter : {{ this.data.doctor_in_charge_name }}</p>
                        <p class="text-center text-bold-12" >Tanggal : {{ this.data.date_added }}</p>
                        <div class="col mt-16">
                            <table class="text-regular-12">
                                <tr>
                                    <td>No. RM</td>
                                    <td class="px-3">:</td>
                                    <td>{{ this.data.norm }}</td>
                                </tr>
                                <tr>
                                    <td>Nama Pasien</td>
                                    <td class="px-3">:</td>
                                    <td>{{ this.data.patient_name }}</td>
                                </tr>
                                <tr>
                                    <td>Tanggal Lahir</td>
                                    <td class="px-3">:</td>
                                    <td>{{ this.data.patient_birth_date }}</td>
                                </tr>
                                <tr>
                                    <td>Cara Bayar</td>
                                    <td class="px-3">:</td>
                                    <td>{{ this.data.insurance_name }} - {{ this.data.company_name }}</td>
                                </tr>
                                <tr>
                                    <td>Alamat</td>
                                    <td class="px-3">:</td>
                                    <td>-</td>
                                </tr>
                            </table>
                        </div>
                        <!-- /info-table -->
                        <div class="px-3 mt-32">
                            <b-table-simple bordered>
                                <b-thead class="text-uppercase text-bold-10 text-center">
                                    <b-tr>
                                        <b-th>No</b-th>
                                        <b-th>(Kode) nama Obat</b-th>
                                        <b-th>Signa</b-th>
                                        <b-th>Racik</b-th>
                                        <b-th>Jumlah Obat</b-th>
                                        <b-th>Harga</b-th>
                                        <b-th>Total Harga</b-th>
                                    </b-tr>
                                </b-thead>
                                <b-tbody class="text-regular-10 text-center">
                                    <template v-for="(item, index) in this.data.form_prescription">
                                        <b-tr>
                                            <b-td>{{ index + 1 }}</b-td>
                                            <b-td class="text-left">({{ item.pharmacy_catalog_code }}) {{ item.pharmacy_catalog_name }}</b-td>
                                            <b-td>{{ item.used }}</b-td>
                                            <b-td><template v-if="item.prescription_item_type_id !== 1">{{ item.prescription_item_type_id }}</template></b-td>
                                            <b-td>{{ (item.qty-(item.is_retur_full === 1 && item.pharmacy_catalog_id_retur || item.pharmacy_catalog_id_ref ? 0 : item.qty_retur)) }}</b-td>
                                            <b-td class="text-right">{{ item.var_1 | currency }}</b-td>
                                            <b-td class="text-right">{{ item.var_1*(item.qty-(item.is_retur_full === 1 && item.pharmacy_catalog_id_retur || item.pharmacy_catalog_id_ref ? 0 : item.qty_retur)) | currency }}</b-td>
                                        </b-tr>
                                    </template>
                                    <b-tr>
                                        <b-td colspan="4"></b-td>
                                        <b-td colspan="2" class="text-right">Total</b-td>
                                        <b-td class="text-right">{{this.data.var_1 | currency}}</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td colspan="4"></b-td>
                                        <b-td colspan="2" class="text-right">Jumlah Bayar</b-td>
                                        <b-td class="text-right">{{this.data.var_3 | currency}}</b-td>
                                    </b-tr>
                                </b-tbody>
                            </b-table-simple>
                        </div>
                        <div class="row mt-32 text-regular-12 justify-content-between">
                            <div class="col-6">
                                <div class="col-4">
                                    <b-table-simple small bordered>
                                        <b-tbody>
                                            <b-tr>
                                                <b-td>H: </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>E: </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>T: </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>P: </b-td>
                                            </b-tr>
                                        </b-tbody>
                                    </b-table-simple>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="row">
                                    <div class="col-6 ml-auto text-center">
                                        <div :class="soapData.employee_signature ? '' : 'mb-80'">Dokter</div>
                                        <template v-if="soapData.employee_signature">
                                            <b-img :src="urlbe+soapData.employee_signature" style="width: 120px;"/>
                                        </template>
                                        <div>{{ soapData.employee_doctor_name }}</div>
                                    </div>
                                    <div class="col-6">
                                        <p class="text-center">Apoteker</p>
                                        <p class="text-center mt-80">(........................................)</p>
                                    </div>
                                </div>
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
            urlbe:process.env.VUE_APP_IMAGE,
            data:{},
            verification: JSON.parse(localStorage.getItem('verification')),
            soapData: {}
        }
    },
    methods: {
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
            location.reload()
        },
        closeWindow() {
            window.close()
        },
        getData (id) {
			this.$store.dispatch('prescription/getById', this.$route.params.id).then(data => {
                this.data = data[0]
                // console.log('Data: ', this.data);

                this.$store.dispatch('soap/getByNorm', this.data.norm).then(data => {
                    this.soapData = data[0]
                    // console.log('this.soapData', this.soapData)
                })
            })
        }
    },
    filters:{
        currency(value) {
            return new Intl.NumberFormat("en-US", { style: "currency", currency: "IDR" }).format(value)
        }
    },
    mounted () {
        window.addEventListener('keydown', this.handleKeyPress);
        if (this.$route.params.id) {
            this.id = this.$route.params.id
            this.getData()
        }
    },
    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },
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
    width: 21.2cm !important;
    height: 29.7cm !important;
    /* width: 21.2cm !important;
    height: 29.7cm !important; */
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    padding: 20px;
}

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
