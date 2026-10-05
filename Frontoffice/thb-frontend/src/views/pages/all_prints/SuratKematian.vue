<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper mt-32">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <div class="row align-items-center pb-40" style="border-bottom: 2px solid black;">
                        <div class="col-2 text-center">
                            <img src="../../../@core/assets/images/logo-thb-small.png" width="60%" alt="rs-logo"> <br>
                        </div>
                        <div class="col text-center">
                            <p class="text-bold-18">RS Taman Harapan Baru</p>
                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                            <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                        </div>
                        <div class="col-2 text-center">
                            <img src="../../../@core/assets/images/lafki-logo.png" width="60%" alt="lafki-logo"> <br>
                        </div>
                    </div>
                    <div class="text-center mt-32">
                        <div class="text-uppercase text-bold-24 with-line">SURAT KETERANGAN KEMATIAN</div>
                        <div class="mt-1 text-bold-16">Nomor: {{ letterData.no }}</div>
                    </div>
                    <div class="text-regular-18 mt-32" style="line-height: 140%;">
                        <div>
                            <div>Yang Bertanda tangan dibawah ini {{ letterData.doctor_in_charge_name }} menerangkan sesungguhnya bahwa :</div>
                            <table class="mt-16 w-100">
                                <tr>
                                    <td class="col-3">Nama</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ regData.alias_name }}. {{ regData.fullname }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3">No. RM</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ regData.norm }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3">Tempat / Tanggal Lahir</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ localDate(regData.birth_date) }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3">Jenis Kelamin</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ regData.sex_name }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3">Warga Negara/Bangsa</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ regData.citizenship_id == '1' ? 'WNI' : 'WNA' }}</td>
                                </tr>
                                <!-- <tr>
                                    <td class="col-3 align-top">Agama</td>
                                    <td class="col-1 text-right align-top">:</td>
                                    <td>{{ getReligion(regData.religion_id) }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3 align-top">Status Perkawinan</td>
                                    <td class="col-1 text-right align-top">:</td>
                                    <td>{{ getMarried(regData.married_id) }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3 align-top">Pekerjaan</td>
                                    <td class="col-1 text-right align-top">:</td>
                                    <td>{{ getProffesion(regData.profession_id) }}</td>
                                </tr> -->
                                <tr>
                                    <td class="col-3 align-top">Alamat</td>
                                    <td class="col-1 text-right align-top">:</td>
                                    <td>{{ regData.address }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="mt-16">
                            <div>Telah meninggal dunia di RS Taman Harapan Baru Bekasi Pada :</div>
                            <table class="mt-16 w-100">
                                <tr>
                                    <td class="col-3">Hari</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ localDateDay(letterData.deathtime) }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3">Pukul</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ localDateHour(letterData.deathtime) }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3">Tanggal</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ localDate(letterData.deathtime) }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3">Bulan</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ localDateMonth(letterData.deathtime) }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3">Tahun</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ localDateYear(letterData.deathtime) }}</td>
                                </tr>
                                <!-- <tr>
                                    <td class="col-3">Tempat Kematian</td>
                                    <td class="col-1 text-right">:</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="col-3">Sebab Kematian</td>
                                    <td class="col-1 text-right">:</td>
                                    <td></td>
                                </tr> -->
                            </table>
                        </div>
                        <div class="mt-24">Demikian surat keterangan ini kami buat dengan sebenarnya.</div>

                        <div class="d-flex justify-content-end w-100 mt-64">
                            <div class="col-4 text-center">
                                <div :class="letterData.ttd_doctor ? '' : 'mb-64'">Bekasi, {{ localDate(letterData.deathtime) }}<br>Dokter yang menyatakan,</br></div>

                                <template v-if="letterData.ttd_doctor">
                                    <b-img :src="urlbe+letterData.ttd_doctor" style="width: 120px;"/>
                                </template>
                                <div>{{ letterData.doctor_in_charge_name }}</div>
                            </div>
                        </div>

                        <div>
                            <div class="text-bold-15 with-line">Penerima,</div>
                            <table class="mt-16 w-100">
                                <tr>
                                    <td class="col-3">Nama Lengkap dan Tanda Tangan</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ letterDataDetail.receipt_name }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3">No. Kartu Identitas</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ letterDataDetail.receipt_idcard }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3">No. HP</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ letterDataDetail.receipt_phone }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3">Hubungan Dengan Pasien</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ letterDataDetail.receipt_relation }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3 align-top">Alamat</td>
                                    <td class="col-1 text-right align-top">:</td>
                                    <td>{{ letterDataDetail.receipt_address }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row align-items-center" style="margin-top: 400px;">
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
                        <div class="text-bold-24 with-line">BUKTI PENGAMBILAN JENAZAH</div>
                        <div class="text-regular-24"></div>
                    </div>
                    <div class="text-regular-18 mt-32" style="line-height: 140%;">
                        <!-- <div>Yang bertanda tangan dibawah ini:.., menerangkan dengan sesungguhnya,bahwa telah lahir
                            seorang bayi dengan jenis kelamin:.. <span>{{
                                regData.doctor_in_charge_name
                                }}</span>
                        </div> -->
                        <div class="mt-5"><b>I. IDENTITAS JENAZAH</b>:</div>
                        <table class="mt-16">
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">NAMA LENGKAP</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.alias_name }}. {{ regData.fullname }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">UMUR/ JENIS KELAMIN</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.birth_date }} {{ regData.sex_name }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">NO. IDENTITAS</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.idcard }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">ALAMAT</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.address }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">AGAMA</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.religion_name }}</td>
                            </tr>
                        </table>
                        <div class="mt-5"><b>II.IDENTITAS PENGAMBIL JENAZAH</b>:</div>
                        <table class="mt-16">
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">NAMA LENGKAP</td>
                                <td class="col-1 text-right">:</td>
                                    <td>{{ letterDataDetail.receipt_name }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">ALAMAT</td>
                                <td class="col-1 text-right">:</td>
                                    <td>{{ letterDataDetail.receipt_address }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">NO. IDENTITAS</td>
                                <td class="col-1 text-right">:</td>
                                    <td>{{ letterDataDetail.receipt_idcard }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">AGAMA</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ letterDataDetail.receipt_religion }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">TUJUAN JENAZAH</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ letterDataDetail.destination_of_the_corpse }}</td>
                            </tr>
                        </table>
                        <div class="d-flex justify-content-end w-100 mt-64">
                            <div class="col-10 text-center">
                                <div>Bekasi, {{ localDate(letterData.deathtime) }}</div>
                                <div :class="letterData.ttd_doctor ? '' : 'mb-80'">Yang Mengambil</div>
                                <div class="mt-80">
                                ({{  letterDataDetail.receipt_name }})
                            </div>
                                <!-- <template v-if="letterData.ttd_doctor">
                                    <b-img :src="urlbe + letterData.ttd_doctor" style="width: 120px;" />
                                </template> -->
                                <!-- <div>{{ letterData.doctor_in_charge_name }}</div> -->
                            </div>
                            <div class="col-5 text-center">
                                <div :class="letterData.ttd_doctor ? '' : 'mb-80'">Petugas instansi</div>
                                <!-- <div> {{ ('Materi 10000') }}</div> -->
                                <!-- <div :class="letterData.ttd_doctor ? '' : 'mb-80'">tanda dan nama jelas -->
                                <br></br>
                                <br></br>
                                    <div>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</div>
                                <!-- <template v-if="letterData.ttd_doctor">
                                    <b-img :src="urlbe + letterData.ttd_doctor" style="width: 120px;" />
                                </template>
                                <div>{{ letterData.doctor_in_charge_name }}</div> -->
                            </div>
                        </div>
                        <div class="mt-1 text-center">
                            <div :class="letterData.ttd_doctor ? '' : 'mb-80'">Mengetahui</div>
                            <template v-if="letterData.ttd_doctor">
                                    <b-img :src="urlbe + letterData.ttd_doctor" style="width: 120px;" />
                                </template>
                            <div>{{ letterData.doctor_in_charge_name }}</div>
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
            urlbe:process.env.VUE_APP_IMAGE,
            regData:{},
            soapData:{},
            letterData:{},
            letterDataDetail:{},
            days:1,
            soap_id:'',
            today: moment(new Date()).locale('id').format('LL')
        }
    },
    computed: {
        ...mapState({
            items: state => state.soap.items,
        })
    },
    filters:{
        convert_date(value) {
            return moment(new Date(value)).locale('id').format('LL')
        }
    },
    methods: {
        getData (id) {
            this.$store.dispatch('registration/getById', id).then(data => {
                this.regData = data[0]
                // console.log('regdata:', this.regData);
                this.$store.dispatch('soap/getByNorm', this.regData.norm).then(data => {
                    this.soapData = data[0]
                    console.log('this.soapData', this.soapData)
                })
            })
        },
        // getSoap (id) {
		// 	this.$store.dispatch('soap/getBySoapId', id).then(data => {
        //         this.soapData = data[0]
        //     })
        // },
        geLetter (id) {
			this.$store.dispatch('letter/getById', id).then(data => {
                console.log(JSON.parse(data[0].content))
                this.letterDataDetail = JSON.parse(data[0].content)
                this.letterData = data[0]
                const dateB = moment(data[0].end);
                const dateC = moment(data[0].start);
                this.days = dateB.diff(dateC, 'days')+1
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
        localDate(input_date) {
            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },
        localDateDay(input_date) {
            return moment(input_date).locale("id").format('dddd')
        },
        localDateHour(input_date) {
            return moment(input_date).locale("id").format('HH:mm')
        },
        localDateMonth(input_date) {
            return moment(input_date).locale("id").format('MMMM')
        },
        localDateYear(input_date) {
            return moment(input_date).locale("id").format('YYYY')
        },
        getReligion(code) {
            const religions = {
                '1': 'Islam',
                '2': 'Katolik',
                '3': 'Protestan',
                '4': 'Hindu',
                '5': 'Buddha',
                '6': 'Khonghucu',
            };
            return religions[code] || '-';
        },
        getMarried(code) {
            const marrieds = {
                '1': 'Lajang',
                '2': 'Kawin',
                '3': 'Janda',
                '4': 'Duda',
            };
            return marrieds[code] || '-';
        },
        getProffesion(code) {
            const proffesions = {
                '1': 'Pegawai Negeri',
                '2': 'Pegawai Swasta',
                '3': 'Pekerja Lepas',
                '4': 'Pelajar',
                '5': 'Mahasiswa',
                '6': 'Ibu Rumah Tangga',
                '7': 'TNI / POLRI',
                '8': 'Pensiunan',
                '9': 'Tidak Bekerja',
                '10': 'Lain Lain',
                '11': 'BUMN',
                '12': 'Buruh',
            };
            return proffesions[code] || '-';
        },

        // parsed_age(input_date) {
        //     const birthDate = moment(input_date, 'YYYY-MM-DD');
        //     const currentDate = moment();

        //     const years = currentDate.diff(birthDate, 'years');
        //     birthDate.add(years, 'years');
        //     const months = currentDate.diff(birthDate, 'months');
        //     birthDate.add(months, 'months');
        //     const days = currentDate.diff(birthDate, 'days');

        //     return `${years} th ${months} bln ${days} hr`;
        // },

    },
    mounted() {
        this.getData(this.$route.params.reg_des_id)
        // this.getSoap(this.$route.params.soap_id)
        this.geLetter(this.$route.params.letter_id)
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

/* #canvas {
    width: 21.2cm !important;
    height: 29.7cm !important;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    padding: 40px;
} */

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
