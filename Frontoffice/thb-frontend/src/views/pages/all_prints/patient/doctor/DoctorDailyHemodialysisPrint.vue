<template>
    <div>
        <b-container fluid="xl" class="mt-32 pb-32">
           <div class="d-flex justify-content-end">
                <b-button @click="closeWindow" size="sm" variant="danger-400" class="px-32">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" variant="success-500" class="px-32 ml-2">Print</b-button>
            </div>
            <div class="d-flex justify-content-center w-100 pb-40 mt-16">
                <div id="canvas" ref="canvas" class="w-100" style="position: relative;">
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

                    <hr class="mt-10 mb-10" style="border-top: 2px solid black;" />

                    <div class="text-uppercase text-center font-weight-bold mt-32" style="font-size: 24px; line-height: 120%;">
                        PEMANTAUAN PELAKSANAAN HEMODIALISIS HARIAN
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
                                    <tr>
                                        <td class="align-top" style="">Jenis Kelamin</td>
                                        <td class="align-top text-right px-3">:</td>
                                        <td>{{ this.regData.sex_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top" style="">Tanggal Registrasi</td>
                                        <td class="align-top text-right px-3">:</td>
                                        <td>{{ this.regData.register_date }}</td>
                                    </tr>
                                    <tr>
                                        <td>Riwayat Alergi</td>
                                        <td class="px-3">:</td>
                                        <td>{{ regData.patient_allergy && regData.patient_allergy.length ? regData.patient_allergy : '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </section>
                    <section class="card mt-32">
                        <div class="card-body">
                            <ModernLabel label="AKSES VASKULAR" />
                            <table class="mt-4" style="font-size: 13px;">
                                <tr>
                                    <td style="width: 140px; padding-left: 16px;">Jenis</td>
                                    <td class="px-3">:</td>
                                    <td>{{ hdData.vascular_access_type ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 16px;">Lokasi</td>
                                    <td class="px-3">:</td>
                                    <td>{{ hdData.vascular_access_location ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 16px;">Kondisi</td>
                                    <td class="px-3">:</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 32px;">Tanda Infeksi</td>
                                    <td class="px-3">:</td>
                                    <td>{{ hdData.condition_infection_sign ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 32px;">Aneurisma</td>
                                    <td class="px-3">:</td>
                                    <td>{{ hdData.condition_aneurisma ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 32px;">Thrill (AV Fistula)</td>
                                    <td class="px-3">:</td>
                                    <td>{{ hdData.condition_thrill ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 32px;">Bruit (AV Fistula)</td>
                                    <td class="px-3">:</td>
                                    <td>{{ hdData.condition_bruit ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 32px;">Lain-lain</td>
                                    <td class="px-3">:</td>
                                    <td>{{ hdData.condition_other ?? '-' }}</td>
                                </tr>
                            </table>

                            <div class="mt-12" style="font-size: 13px;">Untuk Tunnel dan Double Lumen Catheter:</div>
                            <div class="d-flex mt-6" style="gap: 40px; font-size: 13px;">
                                <table>
                                    <tr>
                                        <td style="width: 130px; padding-left: 16px;">Ukuran lumen</td>
                                        <td class="px-3">:</td>
                                        <td>Arteri : {{ hdData.tunnel_lumen_size_arteri ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left: 16px;">Heparin lock</td>
                                        <td class="px-3">:</td>
                                        <td>Arteri : {{ hdData.herparin_lock_arteri ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left: 16px;">Antibiotic lock</td>
                                        <td class="px-3">:</td>
                                        <td>Arteri : {{ hdData.antibiotik_lock_arteri ?? '-' }}</td>
                                    </tr>
                                </table>
                                <table>
                                    <tr><td>Vena : {{ hdData.tunnel_lumen_size_vena ?? '-' }}</td></tr>
                                    <tr><td>Vena : {{ hdData.herparin_lock_vena ?? '-' }}</td></tr>
                                    <tr><td>Vena : {{ hdData.antibiotik_lock_vena ?? '-' }}</td></tr>
                                </table>
                            </div>

                            <hr class="mt-12 mb-8" />

                            <div class="d-flex mt-8" style="gap: 40px; font-size: 13px;">
                                <table style="flex: 1;">
                                    <tr>
                                        <td style="width: 120px;" class="font-weight-bold">MESIN HD</td>
                                        <td class="px-3">:</td>
                                        <td>{{ hdData.hd_machine }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">DIALISAT</td>
                                        <td class="px-3">:</td>
                                        <td>{{ hdData.dialisat }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">DIALISER Model</td>
                                        <td class="px-3">:</td>
                                        <td>{{ hdData.dialiser_model }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">BB KERING</td>
                                        <td class="px-3">:</td>
                                        <td>{{ hdData.bb_dry }}</td>
                                    </tr>
                                </table>
                                <table style="flex: 1;">
                                    <tr>
                                        <td style="width: 100px;" class="font-weight-bold">No. Mesin</td>
                                        <td>{{ hdData.hd_machine_number }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Suhu</td>
                                        <td>{{ hdData.temp }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Tipe Dialiser</td>
                                        <td>{{ hdData.dialiser_model }}</td>
                                    </tr>
                                </table>
                            </div>

                            <hr class="mt-12 mb-8" />

                            <ModernLabel label="RESEP HD" />
                            <div class="d-flex mt-8" style="gap: 40px; font-size: 13px;">
                            <table style="flex: 1;">
                                <tr>
                                    <td style="width: 160px;">Lama HD</td>
                                    <td class="px-3">:</td>
                                    <td>{{ hdData.duration_2 }}</td>
                                </tr>
                                <tr>
                                    <td>Ultrafiltration Goal</td>
                                    <td class="px-3">:</td>
                                    <td>{{ hdData.ufg }}</td>
                                </tr>
                            </table>
                            <table style="flex: 1;">
                                <tr>
                                    <td style="width: 160px;">Blood Flow Rate (QB)</td>
                                    <td class="px-3">:</td>
                                    <td>{{ hdData.qb_2 }}</td>
                                </tr>
                                <tr>
                                    <td>Heparin</td>
                                    <td class="px-3">:</td>
                                    <td>{{ hdData.heparin }}</td>
                                </tr>
                                <tr>
                                    <td>Total</td>
                                    <td class="px-3">:</td>
                                    <td>{{ hdData.total }}</td>
                                </tr>
                                <tr>
                                    <td>Bolus awal</td>
                                    <td class="px-3">:</td>
                                    <td>{{ hdData.bolus_start }}</td>
                                </tr>
                                <tr>
                                    <td>Kontinyu</td>
                                    <td class="px-3">:</td>
                                    <td>{{ hdData.continue }}</td>
                                </tr>
                            </table>
                        </div>

                            <hr class="mt-12 mb-8" />

                            <table style="font-size: 13px;">
                                <tr>
                                    <td style="width: 160px;">Lain - Lain</td>
                                    <td class="px-3">:</td>
                                    <td>{{ hdData.other_3 }}</td>
                                </tr>
                                <tr>
                                    <td>PERUBAHAN OBAT RUTIN</td>
                                    <td class="px-3">:</td>
                                    <td>{{ hdData.reguler_drug_change }}</td>
                                </tr>
                            </table>

                        </div>
                    </section>
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
            urlbe: process.env.VUE_APP_IMAGE,
            soapData: {},
            regData: {},
            hdData: {},
        }
    },

    methods: {
        parsedBody(text) {
            return String(text || '').replace(/\n/g, "<br>");
        },

        localDate(input_date, showDay = true) {
            if (!input_date || input_date === "0000-00-00") return "";
            const parsed = moment(input_date);
            if (!parsed.isValid()) return "";
            const formatString = showDay ? 'dddd, DD MMMM YYYY' : 'DD MMMM YYYY';
            return parsed.locale("id").format(formatString);
        },

        parsed_age(input_date) {
            if (!input_date || input_date === "0000-00-00") return "";
            const parsed = moment(input_date);
            if (!parsed.isValid()) return "";
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
                this.soapData = data[0];
                this.$store.dispatch('registration/getById', this.soapData.registration_destination_id).then(data => {
                    this.regData = data[0];

                    this.$store.dispatch('nurse/getById', this.soapData.registration_destination_id).then((data) => {
                        this.hdData = data?.[0]?.hd
                    }, error => {
                        console.error('Error while GET nurse/getNurseById at: ', error);
                    })
                });
            });
        },

        handleKeyPress(event) {
            if (event.ctrlKey && event.key === 'p') {
                event.preventDefault();
                this.printCanvas();
            }
        },

        closeWindow() {
            window.close();
        },

        printCanvas() {
            const canvas = this.$refs.canvas;
            const originalContents = document.body.innerHTML;
            document.body.innerHTML = canvas.innerHTML;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        },
    },

    mounted() {
        window.addEventListener('keydown', this.handleKeyPress);
    },
    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },
    created() {
        this.getData(this.$route.params.soap_id);
    },
};
</script>

<style>
@import './../../../../../@core/css/main/main.min.css';

@media print {
    @page {
        size: A4;
        margin: 16mm;
    }
}

#canvas {
    width: 210mm;
    min-height: 297mm;
    background-color: white;
    padding: 12mm;
    box-sizing: border-box;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.15);
}

td {
    padding: 2px 0;
}

p {
    margin: 0;
    padding: 0;
}
</style>