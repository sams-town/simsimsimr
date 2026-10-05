<template>
    <div class="d-flex w-100 justify-content-center">
        <div>
            <div class="d-flex justify-content-end py-20" ref="buttons">
                <b-button @click="closeWindow" size="sm" variant="danger-400" class="px-32">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" variant="success-500" class="px-32 ml-2">Print</b-button>
            </div>
            <section id="canvas" ref="canvas">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="d-flex align-items-center">
                        <img src="../../../../../@core/assets/images/logo-thb.jpg" width="70px" alt="logo-thb" class="mr-10">
                        <div>
                            <div class="text-bold-16" style="line-height: 1.3;">RUMAH SAKIT<br>TAMAN HARAPAN BARU</div>
                            <div class="text-regular-12 mt-4" style="line-height: 1.5;">
                                Jl. Kaliabang Tengah No. 2, RT. 004 / RW. 023,<br>
                                Kel. Pejuang Kec. Medan Satria - Kota Bekasi 17131<br>
                                Tep. (021) 8898 1055 Fax. (021) 8886 8558,<br>
                                Email : rs_thb@yahoo.com
                            </div>
                        </div>
                    </div>
                    <table class="mt-4" style="border-collapse: collapse; font-size: 12px; min-width: 210px;">
                        <tr>
                            <td style="border: 1px solid black; padding: 3px 6px; width: 80px;">No. RM</td>
                            <td style="border: 1px solid black; padding: 3px 6px; min-width: 130px;">{{ regData.norm }}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black; padding: 3px 6px;">Nama Pasien</td>
                            <td style="border: 1px solid black; padding: 3px 6px;">{{ regData.name }}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black; padding: 3px 6px;">Jenis Kelamin</td>
                            <td style="border: 1px solid black; padding: 3px 6px;">{{ regData.sex_name }}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black; padding: 3px 6px;">Tanggal Lahir</td>
                            <td style="border: 1px solid black; padding: 3px 6px;">{{ localDate(regData.birth_date, false) }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="border: 1px solid black; padding: 3px 6px; font-size: 10px; font-style: italic; text-align: center;">
                                Harap diisi atau tempelkan stiker bila ada
                            </td>
                        </tr>
                    </table>
                </div>

                <hr style="border: none; border-top: 2px solid black;" class="my-12" />

                <div class="text-uppercase text-center font-weight-bold" style="font-size: 14px; letter-spacing: 0.5px;">
                    LEMBAR PEMAKAIAN ALAT + KASSA
                </div>

                <table class="mt-16" style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <tr>
                        <td style="width: 65px; vertical-align: top; padding: 3px 0;">Diagnosa</td>
                        <td style="width: 10px; padding: 3px 4px; vertical-align: top;">:</td>
                        <td style="vertical-align: top; padding: 3px 0; word-break: break-all; letter-spacing: 0.3px;">{{ regData.diagnosa }}{{ dotFill(regData.diagnosa) }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; padding: 3px 0;">Tindakan</td>
                        <td style="padding: 3px 4px; vertical-align: top;">:</td>
                        <td style="vertical-align: top; padding: 3px 0; word-break: break-all; letter-spacing: 0.3px;">{{ regData.action }}{{ dotFill(regData.action) }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; padding: 3px 0;">Tanggal</td>
                        <td style="padding: 3px 4px; vertical-align: top;">:</td>
                        <td style="vertical-align: top; padding: 3px 0; word-break: break-all; letter-spacing: 0.3px;">{{ regData.action_date }}{{ dotFill(regData.action_date) }}</td>
                    </tr>
                </table>

                <b-table-simple bordered small class="mt-16 mb-0" style="width: 100%; table-layout: fixed; font-size: 13px;">
                    <b-thead>
                        <b-tr>
                            <b-th class="text-center align-middle" style="width: 22%;">Kassa</b-th>
                            <b-th class="text-center align-middle" style="width: 18%;">Persediaan</b-th>
                            <b-th class="text-center align-middle" style="width: 18%;">Terpakai</b-th>
                            <b-th class="text-center align-middle" style="width: 18%;">Sisa</b-th>
                            <b-th class="text-center align-middle" style="width: 24%;">Keterangan</b-th>
                        </b-tr>
                    </b-thead>
                    <b-tbody>
                        <b-tr v-for="(item, index) in kassaItems" :key="index">
                            <b-td class="font-weight-500">{{ item.label }}</b-td>
                            <b-td>{{ item.persediaan }}</b-td>
                            <b-td>{{ item.terpakai }}</b-td>
                            <b-td>{{ item.sisa }}</b-td>
                            <b-td>{{ item.keterangan }}</b-td>
                        </b-tr>
                    </b-tbody>
                </b-table-simple>

                <div class="d-flex justify-content-between mt-32" style="font-size: 13px;">
                    <div class="text-center" style="min-width: 120px;">
                        <div class="font-weight-bold" style="line-height: 1.5; min-height: 48px;">
                            Mengetahui<br>Dokter Operator/<br>Dokter Asisten
                        </div>
                        <div style="height: 60px;"></div>
                        <div>(.................................)</div>
                    </div>
                    <div class="text-center" style="min-width: 120px;">
                        <div class="font-weight-bold" style="line-height: 1.5; min-height: 48px;">
                            Perawat<br>Instrumentator
                        </div>
                        <div style="height: 60px;"></div>
                        <div>(.................................)</div>
                    </div>
                    <div class="text-center" style="min-width: 120px;">
                        <div class="font-weight-bold" style="line-height: 1.5; min-height: 48px;">
                            Perawat Sirkulasi
                        </div>
                        <div style="height: 60px;"></div>
                        <div>(.................................)</div>
                    </div>
                </div>

            </section>
        </div>
    </div>
</template>

<script>
import moment from 'moment';

export default {
    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),
            soapData: {},
            regData: {},
        }
    },

    computed: {
        kassaItems() {
            const groups = [
                { label: 'Kassa Besar',   key: 'kassa_besar' },
                { label: 'Kassa Sedang',  key: 'kassa_sedang' },
                { label: 'Kassa Kecil',   key: 'kassa_kecil' },
                { label: 'Roll Gaas',     key: 'roll_gaas' },
                { label: 'Depper Tonsil', key: 'depper_tonsil' },
                { label: 'Instrument',    key: 'instrument' },
            ]
            const rows = this.soapData.kassa_items || []
            return groups.map((g, i) => {
                const row = rows[i] || {}
                return {
                    label:      g.label,
                    persediaan: row.persediaan || '',
                    terpakai:   row.terpakai   || '',
                    sisa:       row.sisa       || '',
                    keterangan: row.keterangan || '',
                }
            })
        },
    },

    methods: {
        dotFill(value) {
            const total = 100
            const used = value ? value.length + 2 : 0
            return '.'.repeat(Math.max(total - used, 10))
        },

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
            this.$store.dispatch('soap/getBySoapId', soap_id).then(data => {
                this.soapData = data[0]
                this.$store.dispatch('registration/getById', this.soapData.registration_destination_id).then(data => {
                    this.regData = data[0]
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

    created() {
        this.getData(this.$route.params.soap_id)
    },

    mounted() {
        window.addEventListener('keydown', this.handleKeyPress)
    },

    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress)
    },
}
</script>

<style>
@import './../../../../../@core/css/main/main.min.css';

@media print {
    @page {
        size: A4 portrait;
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