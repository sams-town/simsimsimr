<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <div class="row align-items-center">
                        <div class="col-2">
                            <img src="../../../@core/assets/images/logo-thb.jpg" width="300%" alt="rs-logo">
                            <br>
                        </div>
                        <div class="col text-center text-bold-28">
                            <!-- <p class="text-bold-14">RS Taman Harapan Baru</p>
                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                            <p>Info: +62 778 4161212 / 0853 7111 1212</p>
                             -->
                        </div>
                        <div class="col-2">
                            <tr>
                                <td><img src="../../../@core/assets/images/lafki-logo.png" width="85%" alt="lafki-logo"
                                        align="right">
                                </td>
                            </tr>
                        </div>
                        <div class="col-2">
                            <tr>
                                <td><img src="../../../@core/assets/images/apexa.png" width="80%" alt="-logo"
                                        align="right">
                                </td>
                            </tr>
                        </div>
                    </div>
                    <div class="text-uppercase text-center mt-5">
                        <div class="text-bold-24 with-line">SURAT KETERANGAN KELAHIRAN</div>
                        <div class="text-bold-24">( Birth Certificate )</div>
                        <div class="text-regular-24">Nomor: {{ letterData?.no }}</div>
                    </div>
                    <div class="text-regular-18 mt-32" style="line-height: 140%;">
                        <div>Yang bertanda tangan dibawah ini: <span>{{
                                getNewbornData?.doctor_name
                                }}</span>, menerangkan dengan sesungguhnya, bahwa telah lahir
                            seorang Bayi dengan Jenis Kelamin {{ regData.sex_name || '.....' }} :
                        </div>
                        <table class="mt-16 ml-3">
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">No. Rekam Medik</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.norm  }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Nama Bayi</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ getNewbornData.baby_name }}</td>
                            </tr>
                            <!-- <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Jenis Kelamin</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.birth_date }}</td>
                            </tr> -->
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Anak dari Ibu</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ letterData?.name_mother }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Nama Ayah</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ letterData?.name_father }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">No KTP Ibu</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ letterData?.idcard_mother }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">No KTP Ayah</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ letterData?.idcard_father }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Pekerjaan</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ letterData?.profession_name }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Pada Tanggal</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData?.birth_date  | convert_dateStr }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Pukul</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ getNewbornData?.birth_hour | convert_timeStr }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Alamat</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData?.address }}</td>
                            </tr>
                        </table>
                        <div class="row mt-5">
                            <div class="col-6">
                                <div><b>Keterangan Medis</b>:</div>
                                <table class="mt-16">
                                    <tr>
                                        <td class="col-6 pl-0" style="vertical-align: top;">Proses Kelahiran</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>{{ getJenisKelahiran(getNewbornData) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="col-3 pl-0" style="vertical-align: top;">Anak Ke -</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>{{ getNewbornData?.birth_order }}</td>
                                    </tr>
                                    <tr>
                                        <td class="col-3 pl-0" style="vertical-align: top;">Diagnosa</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>{{ getDiagnosa(getNewbornData) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="col-3 pl-0" style="vertical-align: top;">Ketuban</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>{{ getNewbornData?.water_breaks_color }}</td>
                                    </tr>
                                    <tr>
                                        <td class="col-3 pl-0" style="vertical-align: top;">Penyulit</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>{{ getNewbornData?.complication }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-6">
                                <div><b>Keterangan Fisik</b>:</div>
                                <table class="mt-16">
                                    <tr>
                                        <td class="col-6 pl-0" style="vertical-align: top;">Berat Badan</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>{{ getNewbornData && getNewbornData.weight ? getNewbornData.weight : '-' }} g</td>
                                    </tr>
                                    <tr>
                                        <td class="col-3 pl-0" style="vertical-align: top;">Panjang Badan</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>{{ getNewbornData && getNewbornData.height ? getNewbornData.height : '-' }} cm</td>
                                    </tr>
                                    <tr>
                                        <td class="col-3 pl-0" style="vertical-align: top;">Lingkar Kepala</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>{{ getNewbornData && getNewbornData.hfb_circumference_fronto_occipitalis ? getNewbornData.hfb_circumference_fronto_occipitalis : '-' }} cm</td>
                                    </tr>
                                    <tr>
                                        <td class="col-3 pl-0" style="vertical-align: top;">Lingkar Dada</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>{{ getNewbornData && getNewbornData.hfb_chest_circumference ? getNewbornData.hfb_chest_circumference : '-' }} cm</td>
                                    </tr>
                                    <tr>
                                        <td class="col-7 pl-0" style="vertical-align: top;">Lingkar Lengan Atas</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>{{ getNewbornData && getNewbornData.hfb_upper_arm_circumference ? getNewbornData.hfb_upper_arm_circumference : '-' }} cm</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="d-flex justify-content-start w-100 mt-64">
                            <div>
                                <div>Bekasi {{ nurseData.date_added | convert_dateStr}}</div>
                                <div :class="getNewbornData?.doctor_signature ? '' : 'mb-64'">Dokter yang menangani,</div>
                                <!-- <div :class="soapData.doctor_signature ? '' : 'mb-64'">Nama Jelas Dan Tanda Tangan
                                </div> -->
                                <template v-if="getNewbornData?.doctor_signature">
                                    <b-img :src="urlbe + getNewbornData?.doctor_signature" style="width: 120px;" />
                                </template>
                                <div>{{ getNewbornData?.doctor_name }}</div>
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
import { mapState } from 'vuex';

export default {
    data() {
        return {
            urlbe: process.env.VUE_APP_IMAGE,
            regData: {},
            soapData: {},
            letterData: {},
            days: 1,
            soap_id: '',
            id: '',
            nurseData:[],
            patientMotherData:[],
            getNewbornData:[],
            hfb_delivery: [
                { value: 1, title: 'SC' },
                { value: 2, title: 'Spontan' },
                { value: 3, title: 'VE' },
                { value: 4, title: 'Lainnya' },
            ],
            hfb_newborn_baby: [
                { value: 1, title: 'Aterm' },
                { value: 2, title: 'Prematur' },
                { value: 3, title: 'Immatur' },
                { value: 4, title: 'Dysmatur' },
                { value: 5, title: 'Serotinus' },
            ],
        }
    },
    computed: {
        ...mapState({
            items: state => state.soap.items,
        })
    },
    filters: {
        convert_date(value) {
            return moment(new Date(value)).locale('id').format('LL')
        },
        convert_time(value) {
            return moment(new Date(value)).locale('id').format('LT')
        },
        convert_dateStr(value) {
            if(value) return moment(value).locale('id').format('LL')
        },
        convert_timeStr(value) {
            if(value) return moment(value, 'HH:mm:ss').locale('id').format('HH.mm');
        },
    },
    methods: {
        getDiagnosa(newBornData){
            if(newBornData?.hfb_newborn_baby_type_id){
                const result = this.hfb_newborn_baby.filter(item => item.value == newBornData.hfb_newborn_baby_type_id)
                if(result.length > 0) {
                    return result[0].title
                }
            }
        },
        getJenisKelahiran(newBornData){
            if(newBornData?.hfb_delivery_type_id){
                if(newBornData?.hfb_delivery_type_id == 4){
                    return newBornData.hfb_delivery_other
                } else {
                    const result = this.hfb_delivery.filter(item => item.value == newBornData.hfb_delivery_type_id)
                    if(result.length > 0) {
                        return result[0].title
                    }
                }
            }
        },
        getData (nurse_id) {
			this.$store.dispatch('nurse/getByNurseId', nurse_id).then(data => {
                this.nurseData = data[0]

                this.$store.dispatch('registration/getById', this.nurseData.registration_destination_id).then(data => {
                    this.regData = data[0]

                    this.$store.dispatch('nurse/getNewbornInpatientDetail', { nurse_id: nurse_id }).then((response) => {
                        this.getNewbornData = response[0]
                        if(this.getNewbornData?.letter.length > 0) {
                            this.letterData = this.getNewbornData?.letter[0]
                        }
                    })

                })
            })
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
    },
    mounted() {
        if (this.$route.params.nurse_id) {
            this.id = this.$route.params.nurse_id
            this.getData(this.$route.params.nurse_id)
        }
        window.addEventListener('keydown', this.handleKeyPress);
    },
    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },
};

</script>
<style>
/* @import './../../../../@core/css/main/main.min.css'; */
@import './../../../@core/css/main/main.min.css';


@media print {
    @page {
        size: A4;
        margin: 16mm;
    }

    .spnkp table,
    .spnkp th,
    .spnkp td {
        border: 2px solid black;
        border-collapse: collapse;
    }
}

.spnkp table,
.spnkp th,
.spnkp td {
    border: 2px solid black;
    border-collapse: collapse;
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
    height: 29.7cm !important;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    padding: 40px;
}

.with-line {
    text-decoration: underline;
    text-decoration-thickness: 2px;
    text-underline-offset: 8px;
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
