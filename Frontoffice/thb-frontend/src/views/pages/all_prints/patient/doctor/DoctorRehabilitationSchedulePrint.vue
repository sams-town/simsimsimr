<template>
    <div>
        <b-container fluid="xl" class="mt-32 pb-32">
            <div class="d-flex justify-content-end">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="d-flex kustify-content-center w-100 pb-40 mt-16">
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

                            </div>
                        </div>
                        <div>
                            <!-- <img src="../../../../../@core/assets/images/lafki-logo.png" width="114px" alt="lafki-logo"> -->
                        </div>
                    </div>
                    <div class="text-uppercase text-center font-weight-bold mt-32" style="font-size: 24px; line-height: 120%;">
                        <div>Jadwal Fisioterapi Pasien</div>
                    </div>
                    <section class="text-regular-12 mt-24" style="line-height: 140%; font-size: 13px;">
                        <div class="row">
                            <div class="col">
                                <table class="custom mt-16">
                                    <tr>
                                        <td class="align-top" style="width: 140px">Nomor RM</td>
                                        <td class="align-top text-right px-3">:</td>
                                        <td>{{ regData.norm }}</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top" style="">Nama</td>
                                        <td class="align-top text-right px-3">:</td>
                                        <td>{{ regData.alias_name }} {{ regData.fullname }} ({{ regData.sex_name }})</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top" style="">Tanggal Lahir</td>
                                        <td class="align-top text-right px-3">:</td>
                                        <td>{{ localDate(regData.birth_date) }} ({{ parsed_age(regData.birth_date) }})</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top" style="">Tanggal Registrasi</td>
                                        <td class="align-top text-right px-3">:</td>
                                        <td>{{ localDate(regData.register_date) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top">Diagnosis ICD-10</td>
                                        <td class="align-top px-3">:</td>
                                        <td class="align-top list-unstyled">
                                            <template v-if="soapData.icd10 && soapData.icd10.length > 0">
                                                <li v-for="(item, index) in soapData.icd10" :key="index">{{ item.code }} {{ item.name }}</li>
                                            </template>
                                            <template v-else>-</template>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top">Catatan ICD-10</td>
                                        <td class="align-top text-right px-3">:</td>
                                        <td class="align-top">
                                            <div v-html="parsedBody(soapData.icd10_notes ? soapData.icd10_notes : '-')"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top">Diagnosis Non ICD-10</td>
                                        <td class="align-top text-right px-3">:</td>
                                        <td class="align-top">
                                            <template v-if="soapData.non_icd10 && soapData.non_icd10.length > 0">
                                                <li v-for="(item, index) in soapData.non_icd10" :key="index">{{ item }}</li>
                                            </template>
                                            <template v-else>-</template>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top">Pemeriksaan Fisioterapi</td>
                                        <td class="align-top text-right px-3">:</td>
                                        <td class="align-top">
                                            <template v-if="soapData.physiotherapy_request && soapData.physiotherapy_request.length > 0">
                                                <div v-for="(item, index_i) in soapData.physiotherapy_request" :key="index_i">
                                                    <span class="font-weight-bold">{{ item.no_request }}</span>
                                                </div>
                                            </template>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                    </section>

                    <section class="mt-32">
                        <b-table-simple bordered class="custom-box" style="font-size: 12px;">
                            <b-thead class="text-uppercase text-center">
                            <b-tr>
                                <b-th rowspan="2" class="align-middle">Program</b-th>
                                <b-th rowspan="2" class="align-middle" style="width: 200px;">Tanggal</b-th>
                                <b-th colspan="3">TTD</b-th>
                            </b-tr>
                            <b-tr>
                                <b-th style="width: 150px;">Pasien</b-th>
                                <b-th style="width: 150px;">Dokter</b-th>
                                <b-th style="width: 150px;">Fisioterapis</b-th>
                            </b-tr>
                            </b-thead>
                            <b-tbody>
                            <template v-if="soapData.transaction_doctor && soapData.transaction_doctor.length">
                                <b-tr v-for="(item, index) in soapData.transaction_doctor" :key="item.id">
                                <b-td>{{ index + 1 }}. {{ item.costing_name }}</b-td>
                                <b-td class="text-center">{{ localDate(item.date_added) }}</b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                </b-tr>
                            </template>

                            <b-tr v-for="n in Math.max(0, 10 - (soapData.transaction_doctor ? soapData.transaction_doctor.length : 0))" :key="`empty-${n}`">
                                <b-td>&nbsp;</b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                            </b-tr>
                            </b-tbody>
                        </b-table-simple>
                    </section>

                    <div class="d-flex justify-content-end w-100 mt-32" style="font-size: 13px">
                        <div class="col-3 text-center">
                            <div>Bekasi, ............................................</div>
                            <div class="mb-80">Tanda Tangan Pasien</div>
                            <div>( {{ regData.name }} )</div>
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
            soapData: {},
            regData: {},
        }
    },

    methods: {
        parsedBody(text){
            return String(text || '').replace(/\n/g, "<br>");
        },

        localDate(input_date) {
            if (!input_date || input_date === "0000-00-00") {
                return "";
            }

            const parsed = moment(input_date);
            if (!parsed.isValid()) {
                return "";
            }

            return moment(input_date).locale("id").format('DD MMMM YYYY')
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

        async getData (soap_id) {
            this.$store.dispatch('soap/getBySoapId', soap_id).then((data) => {
                this.soapData = data[0]

                this.$store.dispatch('registration/getById', this.soapData.registration_destination_id).then(data => {
                    this.regData = data[0]
                })

            })
        },

        handleKeyPress(event) {
            if (event.ctrlKey && event.key === 'p') {
                event.preventDefault();
                this.printCanvas();
            }
        },
        closeWindow() {
            window.close()
        },
        printCanvas() {
            const canvas = this.$refs.canvas;
            const originalContents = document.body.innerHTML;
            document.body.innerHTML = canvas.innerHTML;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload()
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

.custom td {
    padding: 2px 0;
}

.custom-box td, .custom-box th, .custom-box tr {
    border: 1px solid black;
    padding: 8px;
}

</style>
