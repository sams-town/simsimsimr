<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <!-- <b-button @click="closeWindow" v-if="print" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" v-if="print" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button> -->
            </div>
            <div class="canvas-wrapper">
                <div id="canvas" ref="canvas" class="mt-36" style="position: relative;">
                    <div class="d-flex justify-content-between w-100 px-3 mt-8">
                        <div class="d-flex align-items-center">
                            <img src="@/@core/assets/images/bpjs/bpjs_logo.png" height="40px" alt="rs-logo"> <br>
                            <div class="font-weight-bold ml-24">
                                <div>{{ regDataControl?.jnsPelayanan == "1" ? 'SURAT RENCANA RAWAT INAP' : 'SURAT RENCANA KONTROL' }}</div>
                                <div>RS Taman Harapan Baru</div>
                            </div>
                        </div>
                        <div class="font-weight-bold">No. {{ this.$route.params.no }}</div>
                    </div>
                    <div class="mt-24">
                        <div class="col-12">
                            <table class="text-regular-14">
                                <tr>
                                    <td class="first-cell" style="vertical-align: top;">Kepada Yth</td>
                                    <td class="px-3" style="vertical-align: top;">:</td>
                                    <td>
                                        <div>{{regDataControl?.namaDokter}}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="pt-3">
                                        <div>Mohon Pemeriksaan Lebih Lanjut :</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="first-cell">No. Kartu</td>
                                    <td class="px-3">:</td>
                                    <td>
                                        {{regDataControl?.noKartu}}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="first-cell">Nama Peserta</td>
                                    <td class="px-3">:</td>
                                    <td class="text-uppercase">
                                        {{regData.alias_name}}. {{regData.name}}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="first-cell">Tgl.Lahir</td>
                                    <td class="px-3">:</td>
                                    <td>
                                        {{regData.birth_date}} ({{regData.sex_name}})
                                    </td>
                                </tr>
                                <tr>
                                    <td class="first-cell">Diagnosa Awal</td>
                                    <td class="px-3">:</td>
                                    <td>{{ regData.registration_insurance[0]?.sep ? pesertaSEP?.diagnosa : ''}}</td>
                                </tr>
                                <tr>
                                    <td class="first-cell">{{regDataControl?.jnsPelayanan == "1" ? 'Rencana Inap' : 'Rencana Kontrol'}}</td>
                                    <td class="px-3">:</td>
                                    <td>{{regDataControl?.tglRencanaKontrol}}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="ml-3 mt-16 row text-regular-14">Demikian atas bantuannya, diucapkan banyak terima kasih.</div>
                        <div class="ml-3 mt-16 row text-regular-14"><b>{{regDataControl?.is_post_inpatient === 1 ? 'PASIEN KONTROL POST INAP' : ''}}</b></div>
                        <div class="d-flex justify-content-end px-3 mt-16">
                            <div class="col-4">
                                <div class="text-center text-regular-14">
                                    <div :class="regData.employee_signature ? '' : 'mb-64'" >Mengetahui DPJP,</div>
                                    <template v-if="regData.employee_signature">
                                        <b-img :src="urlbe+regData.employee_signature" style="width: 120px;"/>
                                    </template>
                                    <div>({{ regDataControl?.namaDokter }})</div>
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
            urlbe:process.env.VUE_APP_IMAGE,
            manual: '1',
            regData: {},
            pesertaSEP: {},
            pesertaData: {},
            regDataControl:{},
            print:true,
            regIns:{},
            date_now: moment(new Date()).format('YYYY-MM-DD'),
        }
    },
    methods: {
        async getDataBpjs(id) {
            try {
                const response = await this.$store.dispatch('patient/bpjs', {url:'Control',idx:id})
                this.pesertaData = response.data.response
                // console.log(this.pesertaData)

            } catch (error) {
                // this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
            }
        },
        async getData(id) {
            try {
                const response = await this.$store.dispatch('registration/getById', id)
                this.regData = response[0]
                this.getDataSEP(response[0].registration_insurance[0]?.sep)
                // Pass registration_destination_id instead of number_insurance
                this.getDataControl()
            } catch (error) {
                // this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
            }
        },
        async getDataControl() {
            try {
                // Only use registration_destination_id, no need for number_insurance
                const response = await this.$store.dispatch('registration/getByControlByNoka', {registration_destination_id:this.$route.params.id})
                console.log('Response getDataControl:', response)
                this.regDataControl = response[0]
                console.log('regDataControl:', this.regDataControl)
            } catch (error) {
                console.error('Error getDataControl:', error)
                // this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
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
        async getDataSEP(id) {
            try {
                const response = await this.$store.dispatch('patient/bpjs', {url:'Sep',idx:id})
                this.pesertaSEP = response.data.response
            } catch (error) {
                // this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
            }
        },

        handleKeyPress(event) {
            if (event.ctrlKey && event.key === 'p') {
                event.preventDefault();
                this.printCanvas();
            }
        },

        closeWindow() {
            window.close()
        }
    },
    created() {
        if (this.$route.params.id) {
            this.getDataBpjs(this.$route.params.no)
            this.getData(this.$route.params.id)
            this.printCanvas()
        }
    },

    mounted() {
        window.addEventListener('keydown', this.handleKeyPress);
    },
    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },
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
