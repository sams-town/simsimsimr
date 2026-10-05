<template>
    <div class="d-flex w-100 justify-content-center">
        <div>
            <div class="d-flex justify-content-end py-20" ref="buttons">
                <b-button @click="closeWindow" size="sm" variant="danger-400" class="px-32">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" variant="success-500" class="px-32 ml-2">Print</b-button>
            </div>
            <section id="canvas" ref="canvas">
                <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex">
                            <div>
                                <img src="../../../../../@core/assets/images/logo-thb-small.png" width="80px" alt="rs-logo"> <br>
                            </div>
                            <div class="text-regular-14 pl-16">
                                <p class="text-bold-18">RS Taman Harapan Baru</p>
                                <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                                <p>Info: +62 778 4161212 / 0853 7111 1212</p>
                                <p>Email: rs_thb@yahoo.com</p>
                            </div>
                        </div>
                        <div>
                            <!-- <img src="../../../../../@core/assets/images/lafki-logo.png" width="114px" alt="lafki-logo"> -->
                        </div>
                    </div>

                <div class="divider-line my-8"></div>

                <div class="text-uppercase text-center font-weight-bold mt-32" style="font-size: 24px; line-height: 120%;">
                        PEMANTAUAN INTRADIALITIK
                    </div>
                <section class="mt-24" style="line-height: 140%; font-size: 13px">
                                        <div class="row">
                                            <div class="col-6">
                                                <table class="mt-16">
                                                    <tr>
                                                        <td class="align-top" style="width: 160px">Nomor RM</td>
                                                        <td class="align-top text-right px-3">:</td>
                                                        <td>{{ this.regData.norm }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top" style="">Nama</td>
                                                        <td class="align-top text-right px-3">:</td>
                                                        <td>{{ this.regData.alias_name }} {{ this.regData.fullname }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top" style="">Tanggal Lahir</td>
                                                        <td class="align-top text-right px-3">:</td>
                                                        <td>{{ localDate(this.regData.birth_date, false) }} ({{ parsed_age(regData.birth_date) }})</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </section>

                <div class="table-responsive mt-10">
                    <b-table-simple bordered small class="mb-0 mt-10" style="width: 100%; table-layout: fixed; font-size: 9px; word-break: break-word;">
                    <b-thead>
                        <b-tr>
                            <b-th class="text-center align-middle" style="width: 7%;">Tanggal/ Jam</b-th>
                            <b-th class="text-center align-middle" style="width: 5%;">Pemantauan Jam Ke</b-th>
                            <b-th class="text-center align-middle" style="width: 8%;">Keluhan</b-th>
                            <b-th class="text-center align-middle" style="width: 4%;">BB (Kg)</b-th>
                            <b-th class="text-center align-middle" style="width: 6%;">Kesadaran</b-th>
                            <b-th class="text-center align-middle" style="width: 6%;">Tensi (mmHg)</b-th>
                            <b-th class="text-center align-middle" style="width: 5%;">Nadi (x/mnt)</b-th>
                            <b-th class="text-center align-middle" style="width: 6%;">Respirasi (x/mnt)</b-th>
                            <b-th class="text-center align-middle" style="width: 5%;">Qb (ml/mnt)</b-th>
                            <b-th class="text-center align-middle" style="width: 5%;">Qd (ml/mnt)</b-th>
                            <b-th class="text-center align-middle" style="width: 6%;">Tekanan Vena (mmHg)</b-th>
                            <b-th class="text-center align-middle" style="width: 5%;">TMP (mmHg)</b-th>
                            <b-th class="text-center align-middle" style="width: 6%;">Volume ditarik (ml)</b-th>
                            <b-th class="text-center align-middle" style="width: 13%;">Asesmen/ Intervensi</b-th>
                            <b-th class="text-center align-middle" style="width: 9%;">Nama Petugas</b-th>
                        </b-tr>
                    </b-thead>
                    <b-tbody>
                        <b-tr v-for="(row, index) in monitoringRows" :key="index">
                            <b-td>{{ row.datetime }}</b-td>
                            <b-td class="text-center">{{ row.monitoring_session }}</b-td>
                            <b-td class="text-center">{{ row.complaint }}</b-td>
                            <b-td class="text-center">{{ row.weight }}</b-td>
                            <b-td>{{ row.consciousness }}</b-td>
                            <b-td class="text-center">{{ row.blood_pressure }}</b-td>
                            <b-td class="text-center">{{ row.pulse }}</b-td>
                            <b-td class="text-center">{{ row.respiration }}</b-td>
                            <b-td class="text-center">{{ row.qb }}</b-td>
                            <b-td class="text-center">{{ row.qd }}</b-td>
                            <b-td class="text-center">{{ row.venous_pressure }}</b-td>
                            <b-td class="text-center">{{ row.tmp }}</b-td>
                            <b-td class="text-center">{{ row.volume_removed }}</b-td>
                            <b-td class="text-center">{{ row.assessment }}</b-td>
                            <b-td>{{ row.nurse_name }}</b-td>
                        </b-tr>
                        <b-tr v-for="n in emptyRows" :key="'e'+n">
                            <b-td v-for="c in 15" :key="c">&nbsp;</b-td>
                        </b-tr>
                    </b-tbody>
                </b-table-simple>
                </div>

                <div class="mt-16" style="font-size: 13px; font-weight: bold;">PERENCANAAN PULANG (DISCHARGE PLANNING)</div>
                <div class="row">
                    <div class="col-7">
                        <table class="mt-8" style="font-size: 13px !important;">
                            <tr>
                                <td>1. Hari dan tanggal tindakan HD</td>
                                <td class="px-3">:</td>
                                <td>{{ localDate(hdData.next_hd_date) || '-' }}</td>
                            </tr>
                            <tr>
                                <td>2. Edukasi</td>
                                <td class="px-3">:</td>
                                <td>{{ hdData.education || '-' }}</td>
                            </tr>
                            <tr>
                                <td>3. Rencana konsultasi</td>
                                <td class="px-3">:</td>
                                <td>{{ hdData.consult_planning || '-' }}</td>
                            </tr>
                            <tr>
                                <td>4. Rencana pemeriksaan</td>
                                <td class="px-3">:</td>
                                <td class="align-top list-unstyled">
                                    <template v-if="hdData.lab_item && hdData.lab_item.length > 0">
                                        <li v-for="(item, index) in hdData.lab_item" :key="index">{{ item.code }} {{ item.name }}</li>
                                    </template>
                                    <template v-else>-</template>
                                </td>
                            </tr>
                            <tr>
                                <td>5. Lain - lain</td>
                                <td class="px-3">:</td>
                                <td>{{ hdData.other_2 || '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-5">
                        <div class="d-flex justify-content-between" style="gap: 80px;">
                            <div class="text-center">
                                <div style="font-size: 13px;">Perawat,</div>
                                <div class="mt-8">
                                    <img v-if="qrNurse" :src="qrNurse" width="100px" alt="QR Perawat">
                                    <div v-else style="width: 80px; height: 100px;"></div>
                                </div>
                                <div class="mt-4" style="font-size: 13px;">{{ regData.nurse_name }}</div>
                            </div>
                            <div class="text-center">
                                <div style="font-size: 13px;">Dokter,</div>
                                <div class="mt-8">
                                    <img v-if="qrDoctor" :src="qrDoctor" width="100px" alt="QR Dokter">
                                    <div v-else style="width: 80px; height: 100px;"></div>
                                </div>
                                <div class="mt-2" style="font-size: 13px;">{{ regData.doctor_in_charge_name }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>
<script>
import moment from 'moment';
import QRCode from 'qrcode';
import { mapState } from 'vuex';

export default {
    // components: {ModernLabel},
    data() {
        return {
            urlbe:process.env.VUE_APP_IMAGE,
            qrDoctor: '',
            qrNurse: '',
            soapData: {},
            regData: {},
            hdData: {},
        }
    },
    computed: {
        monitoringRows() {
            const intra = this.hdData?.intra || [];
            return intra.map(item => ({
            datetime: item.updated_at ? moment(item.updated_at).format('DD/MM/YYYY HH:mm') : '',
            monitoring_session: item.hour_observation,
            complaint: item.complaint,
            weight: item.bb,
            consciousness: item.awareness,
            blood_pressure: item.td,
            pulse: item.pulse,
            respiration: item.rr,
            qb: item.qb,
            qd: item.qd,
            venous_pressure: item.venous_pressure,
            tmp: item.tmp,
            volume_removed: item.drawn_volume,
            assessment: item.assessment,
            nurse_hd_name: item.nurse_hd_name || '-'
            }));
        },
        emptyRows() {
            const maxRows = 8; // changed from 10
            const filled = this.monitoringRows.length;
            return filled < maxRows ? maxRows - filled : 0;
        },
    },
    methods: {
        async generateQR(text) {
            return await QRCode.toDataURL(text, {
                errorCorrectionLevel: 'M',
                type: 'image/png',
                width: 120,
                margin: 1,
                color: { dark: '#000000', light: '#ffffff' },
            })
        },

        buildPayload(name) {
            return `Ditandatangani secara elektronik oleh ${name}`
        },

        async generateAllQRCodes() {
            const doctorName = this.regData.doctor_in_charge_name ||  ''
            const nurseName  = this.soapData.nurse_name           ||  ''

            if (doctorName) {
                this.qrDoctor = await this.generateQR(this.buildPayload(doctorName))
            }
            if (nurseName) {
                this.qrNurse = await this.generateQR(this.buildPayload(nurseName))
            }
        },
        parsedBody(text){
            return String(text || '').replace(/\n/g, "<br>");
        },

        localDate(input_date, showDay = true) {
            if (!input_date || input_date === "0000-00-00") {
                return "";
            }

            const parsed = moment(input_date);
            if (!parsed.isValid()) {
                return "";
            }

            const formatString = showDay ? 'dddd, DD MMMM YYYY' : 'DD MMMM YYYY';

            return parsed.locale("id").format(formatString);
        },

        parsed_age(input_date) {
            if (!input_date || input_date === "0000-00-00") {
                return "";
            }

            const parsed = moment(input_date);
            if (!parsed.isValid()) {
                return "";
            }

            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const currentDate = moment();

            const years = currentDate.diff(birthDate, 'years');
            birthDate.add(years, 'years');
            const months = currentDate.diff(birthDate, 'months');
            birthDate.add(months, 'months');
            const days = currentDate.diff(birthDate, 'days');

            return `${years} th ${months} bln ${days} hr`;
        },

        async getData(soap_id) {
            this.$store.dispatch('soap/getBySoapId', soap_id).then((data) => {
                this.soapData = data[0]

                this.$store.dispatch('registration/getById', this.soapData.registration_destination_id).then(async data => {
                    this.regData = data[0]

                    this.$store.dispatch('nurse/getById', this.soapData.registration_destination_id).then((data) => {
                        this.hdData = data?.[0]?.hd
                    }, error => {
                        console.error('Error while GET nurse/getNurseById at: ', error);
                    })

                    this.$nextTick(async () => {
                        await this.generateAllQRCodes()
                    })
                })
            })
        },

        printCanvas() {
                    const canvas = this.$refs.canvas;
                    const originalContents = document.body.innerHTML;
                    document.body.innerHTML = canvas.innerHTML;
                    window.print();
                    document.body.innerHTML = originalContents;
                    location.reload();
                },

        handleKeyPress(event) {
            if (event.ctrlKey && event.key === 'p') {
                event.preventDefault()
                this.printCanvas()
            }
        },

        closeWindow() {
            window.close()
        },
    },

    mounted() {
        window.addEventListener('keydown', this.handleKeyPress);
    },
    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },

    created() {
        this.getData(this.$route.params.soap_id)
    },
};

</script>
<style>
@import './../../../../../@core/css/main/main.min.css';

@media print {
    @page {
        size: A4 landscape;
        margin: 16mm;
    }
}

#canvas {
    width: 257mm;
    min-height: 178mm;
    background-color: white;
    padding: 0;
    box-sizing: border-box;
}

.table-responsive {
        overflow: visible !important;
    }

table {
      font-size: 9px !important;
    }

td {
    padding: 2px 0;
}

p {
    margin: 0;
    padding: 0;
}
</style>