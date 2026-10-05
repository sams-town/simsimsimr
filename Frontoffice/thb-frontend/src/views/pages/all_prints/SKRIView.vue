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
                            <img src="../../../@core/assets/images/logo-thb-small.png" width="60%" alt="rs-logo"> <br>
                        </div>
                        <div class="col text-center text-bold-28">
                            <p class="text-bold-14">RS Taman Harapan Baru</p>
                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                            <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                        </div>
                        <div class="col-2">
                            <img src="../../../@core/assets/images/lafki-logo.png" width="60%" alt="lafki-logo"> <br>
                        </div>
                    </div>
                    <div class="text-uppercase text-center mt-24">
                        <div class="text-bold-24 with-line">SURAT KETERANGAN RAWAT INAP</div>
                        <div class="text-regular-24">No. {{letterData.no}}</div>
                    </div>
                    <div class="text-regular-18 mt-32" style="line-height: 140%;">
                        <div>Yang Bertanda tangan dibawah ini, <span>{{ regData.doctor_in_charge_name }}</span> menerangkan bahwa:</div>
                        <table class="mt-16">
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Nama</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.alias_name }}. {{ regData.fullname }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Umur</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ parsed_age(regData.birth_date) }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">No RM</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.norm }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Alamat</td>
                                <td class="col-1 text-right" style="vertical-align: top;">:</td>
                                <td>{{ regData.address }}</td>
                            </tr>
                            <!-- <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Diagnosa</td>
                                <td class="col-1 text-right" style="vertical-align: top;">:</td>
                                <td>
                                    <p v-for="(list, index) in soapData.icd10">
                                        {{ list.code }} - {{ list.name }}
                                    </p>
                                </td>
                            </tr> -->
                        </table>
                        <div class="mt-16">Menyatakan bahwa nama diatas sedang dirawat inap di RS Taman Harapan Baru Bekasi  pada tanggal {{letterData.start | convert_date}} sampai dengan {{letterData.end | convert_date}}, Demikian surat keterangan ini dibuat dengan benar untuk dapat digunakan sebaik-baiknya.</div>

                        <div class="d-flex justify-content-end w-100 mt-64">
                            <div class="col-4 text-center">
                                <div :class="regData.employee_signature ? '' : 'mb-64'">Bekasi, {{letterData.date | convert_date}} <br> Dokter Pemeriksa,</div>
                                <template v-if="regData.employee_signature">
                                    <b-img :src="urlbe+regData.employee_signature" style="width: 120px;"/>
                                </template>
                                <div>{{ regData.doctor_in_charge_name }}</div>
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
            urlbe:process.env.VUE_APP_IMAGE,
            regData:{},
            letterData:{},
            days:1,
            soap_id:''
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
            })
        },
        geLetter (id) {
			this.$store.dispatch('letter/getById', id).then(data => {
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
        parsed_age(input_date) {
            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const currentDate = moment();

            const years = currentDate.diff(birthDate, 'years');
            birthDate.add(years, 'years');
            const months = currentDate.diff(birthDate, 'months');
            birthDate.add(months, 'months');
            const days = currentDate.diff(birthDate, 'days');

            // return `${years} th ${months} bln ${days} hr`;
            return `${years} th`;
            },
            parsed_start_from_date(input_date) {
            return moment(input_date).format('DD-MM-YYYY');
        },
    },
    mounted() {
        this.getData(this.$route.params.id)
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
