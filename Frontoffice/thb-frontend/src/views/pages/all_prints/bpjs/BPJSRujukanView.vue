<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <!-- <b-button @click="closeWindow" v-if="print" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" v-if="print" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button> -->
            </div>
            <div class="canvas-wrapper">
                <div id="canvas" ref="canvas" class="mt-36" style="position: relative;">
                    <div class="d-flex justify-content-between w-1oo px-3 mt-16">
                        <div class="d-flex">
                            <div>
                                <img src="@/@core/assets/images/bpjs/bpjs_logo.png" height="40px" alt="rs-logo"> <br>
                            </div>
                            <div class="text-bold-16 ml-24">
                                <div>SURAT RUJUKAN</div>
                                <div>RS Taman Harapan Baru</div>
                            </div>
                        </div>
                        <div>
                            <div>No. {{ regRefer.no_refer }}</div>
                            <div>Tgl. {{ regRefer.tglRencanaKontrol }}</div>
                        </div>
                    </div>
                    <div class="mt-40">
                        <div class="row mx-0">
                            <div class="col-6">
                                <table class="text-regular-14">
                                    <tr>
                                        <td class="first-cell" style="vertical-align: top;">Kepada Yth</td>
                                        <td class="px-3" style="vertical-align: top;">:</td>
                                        <td>
                                            <div>{{ regRefer.tipeRujukan !== 2 ? objects?.rujukan?.poliTujuan?.nama : ''}}</div>
                                            <div>{{objects?.rujukan?.tujuanRujukan?.nama}}</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Mohon Pemeriksasan dan Penanganan Lebih Lanjut</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">No. Kartu</td>
                                        <td class="px-3">:</td>
                                        <td>{{pesertaData?.peserta?.noKartu}}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Nama Peserta</td>
                                        <td class="px-3">:</td>
                                        <td class="text-uppercase">
                                            <template v-if="pesertaData.peserta">
                                                {{pesertaData.peserta.nama}}
                                            </template>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Tgl.Lahir</td>
                                        <td class="px-3">:</td>
                                        <td>
                                            <template v-if="pesertaData.peserta">
                                                {{pesertaData.peserta.tglLahir}} Kelamin {{pesertaData.peserta.kelamin}}
                                            </template>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Diagnosa Awal</td>
                                        <td class="px-3">:</td>
                                        <td>{{objects?.rujukan?.diagnosa?.nama}}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Keterangan</td>
                                        <td class="px-3">:</td>
                                        <td>{{regRefer.catatan}}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-6">
                                <table class="text-regular-14">
                                    <tr>
                                        <td class="first-cell"></td>
                                        <td class="px-3"></td>
                                        <td class="text-uppercase">
                                            {{ regRefer.tipeRujukan === 0 ? '==Rujuk Penuh==' : '' }}
                                            {{ regRefer.tipeRujukan === 1 ? '==Rujuk Partial==' : '' }}
                                            {{ regRefer.tipeRujukan === 2 ? '==Rujuk Balik (Non PRB)==' : '' }}
                                        </td>
                                        </tr>
                                    <tr>
                                        <td class="first-cell"></td>
                                        <td class="px-3"></td>
                                        <td>{{ regRefer.jnsPelayanan == "1" ? 'RAWAT INAP' : 'RAWAT JALAN' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="px-3 mt-16 row">
                            <div class="col-8">
                                <!-- <div>
                                    <small class="text-muted">* Rujukan Berlaku Sampai Dengan</small>
                                </div>
                                <div>
                                    <small class="text-muted">* Tgl.Rencana Berkunjung</small>
                                </div> -->
                                <div class="mt-32">
                                    <!-- <small class="text-muted">Cetakan ke 1 {{regIns.timestampt}}</small> -->
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center text-regular-14">
                                    <p>Mengetahui</p>
                                    <div class="px-32 mt-72">
                                    <!-- <div v-if="pesertaData.peserta" class="px-32 mt-72"> -->
                                        <!-- {{ pesertaData.peserta.nama }} -->
                                        <hr class="w-100" style="border: 1px solid black;">
                                    <!-- </div> -->
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
import moment from 'moment';

export default {
    data() {
        return {
            regData: {},
            regRefer: {},
            pesertaData: {},
            print:true,
            regIns:{},
            objects:{},
            date_now: moment(new Date()).format('YYYY-MM-DD'),
        }
    },
    methods: {
        async getDataBpjs(id) {
            try {
                const response = await this.$store.dispatch('patient/bpjs', {url:'Sep',idx:id})
                this.pesertaData = response.data.response

            } catch (error) {
                this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
            }
        },
        async getData(id) {
            try {
                // this.form.tglSep = response[0].
                const response = await this.$store.dispatch('registration/getById', id)
                this.regData = response[0]
            } catch (error) {
                this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
            }
        },
        async getRegRefer(id) {
            try {
                const response = await this.$store.dispatch('registration/getByReferBySep', {registration_destination_id:this.$route.params.id,sep:this.$route.params.no})
                this.regRefer = response[0]
                this.objects = JSON.parse(response[0].objects)
                console.log(this.objects)
            } catch (error) {
                this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
            }
        },
        printCanvas() {
            this.print = false
            setTimeout(() => {
                const canvas = this.$refs.canvas;
                const originalContents = document.body.innerHTML;
                document.body.innerHTML = canvas.innerHTML;
                window.print();
                document.body.innerHTML = originalContents;
            }, 5000);
            // setTimeout(() => {
            //     location.reload();
            // }, 1500);
        },
        closeWindow() {
            window.close()
        }
    },
    mounted() {
        if (this.$route.params.id) {
            this.printCanvas()
            this.getRegRefer(this.$route.params.no)
            this.getDataBpjs(this.$route.params.no)
            this.getData(this.$route.params.id)
        }
    }
};

</script>
<style>
/* @import './../../../../@core/css/main/main.min.css'; */
@import './../../../../@core/css/main/main.min.css';


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
}

.canvas-wrapper {
    display: flex;
    justify-content: center;
}

#canvas {
    /* width: 210mm; */
    width: 21.2cm !important;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
}

.with-line {
    text-decoration: underline;
    text-decoration-thickness: 2px;
    text-underline-offset: 8px;
}
.underline{
    text-decoration: underline;;
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

.italic{
    font-style: italic;
}

.canvas-footer {
    position: absolute;
    bottom: 0;
    /* right: 0; */
}
</style>
