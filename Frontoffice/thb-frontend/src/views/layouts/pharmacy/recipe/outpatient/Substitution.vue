<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button size="sm" @click="handleBack" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <div class="d-flex align-items-center justify-content-center pb-24" style="border-bottom: 3px solid black;">
                        <img src="../../../../../@core/assets/images/logo-thb-small.png" width="80px" alt="rs-logo"> <br>
                        <div class="text-regular-14 pl-16">
                            <p class="text-bold-18">RS Taman Harapan Baru</p>
                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                            <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                        </div>
                    </div>
                    <!-- <div class="canvas-header d-flex px-3 mt-16">
                        <img src="@/@core/assets/images/nurse/zeemar-logo.png" alt="rs-logo"> <br>
                        <div class="ml-3">
                            <p class="text-regular-12">Jl.Raya Jakarta 11520</p>
                            <p class="text-regular-12">Telp: (021) 12345678</p>
                            <p class="text-regular-12">Email: info@gmail.com</p>
                            <p class="text-regular-12">Website: www.website.com</p>
                        </div>
                    </div><hr> -->
                    <div class="mt-40">
                        <div class="text-center">
                            <p class="text-bold-16">FORM SUBSTITUSI OBAT</p>
                        </div><br>
                        <div class="row mx-0">
                            <div class="col-8">
                                <table class="text-regular-14">
                                    <tr>
                                        <td class="first-cell">Nama Dokter</td>
                                        <td class="px-3">:</td>
                                        <td>{{ this.data.doctor_in_charge_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Nama Obat Kosong</td>
                                        <td class="px-3">:</td>
                                        <td>{{ this.data.pharmacy_catalog_name_ref }}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Nama Obat Penggati</td>
                                        <td class="px-3">:</td>
                                        <td>{{ this.data.pharmacy_catalog_name_sub }}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Aturan Pakai</td>
                                        <td class="px-3">:</td>
                                        <td>{{ this.data.used }}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Jam Konfirmasi</td>
                                        <td class="px-3">:</td>
                                        <td>{{ this.data.date_confirm }}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Alasan Kekosongan Obat</td>
                                        <td class="px-3">:</td>
                                        <td>{{ this.data.notes }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="px-3 mt-32">
                        <section class="card mt-40">
                            <div class="card-header text-center font-weight-bold">VALIDASI</div>
                            <div class="card-body">
                                <b-table-simple small class="text-regular-14">
                                    <b-tbody>
                                        <b-tr>
                                            <b-td class="border-0" style="text-align: center;" >Nama Petugas Farmasi</b-td>
                                            <b-td class="border-0" style="text-align: center;" >Nama Dokter</b-td>
                                            </b-tr>
                                        <b-tr>
                                            <b-td class="border-0" style="height:60px;" ></b-td>
                                            <b-td class="border-0" style="height:60px;" ></b-td>
                                            </b-tr>
                                        <b-tr>
                                            <b-td class="border-0" style="text-align: center;" >(Administrator)</b-td>
                                            <b-td class="border-0" style="text-align: center;" >({{this.data.doctor_in_charge_name}})</b-td>
                                        </b-tr>
                                    </b-tbody>
                                </b-table-simple>
                            </div>
                        </section>
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
            data:{}
        }
    },
    methods: {
        printCanvas() {
            const canvas = this.$refs.canvas;
            const originalContents = document.body.innerHTML;
            document.body.innerHTML = canvas.innerHTML;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        },
        handleBack() {
            this.$router.go(-1);
        },
        getData (id) {
			this.$store.dispatch('prescription/getItemById', {pharmacy_prescription_id:this.$route.params.id,pharmacy_catalog_id:this.$route.params.pharmacy_catalog_id}).then(data => {
                this.data = data[0]
                console.log(data[0])
                console.log(this.data)
            })
        }
    },
    mounted () {
        if (this.$route.params.id) {
            this.id = this.$route.params.id
            this.getData()
        }
    }
};

</script>
<style>
@import '@core/css/main/main.min.css';

@media print {
    @page {
        size: A4;
        margin: 16mm;
    }
}
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
    /* width: 210mm; */
    width: 21.2cm !important;
    /* height: 29.7cm !important; */
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    padding: 20px 0;
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
