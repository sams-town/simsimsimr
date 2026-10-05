<template>
    <div>
        <b-container fluid="xl" class="mt-32 pb-32">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="d-flex justify-content-center mt-32">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <div class="letter-header">
                        <img src="../../../@core/assets/images/logo-thb.jpg" width="300px" alt="rs-logo">
                        <div class="d-flex">
                            <img src="../../../@core/assets/images/lafki-logo.png" width="70px" alt="lafki-logo">
                            <img src="../../../@core/assets/images/apexa.png" width="100px" alt="apex-logo" class="ml-8">
                        </div>
                    </div>
                    <div style="padding: 0 16mm;">
                        <div class="title" >INFORMASI TINDAKAN KEDOKTERAN</div>
                        <table>
                            <tr><th>Nama :</th><td>{{ regData.alias_name }}. {{ regData.fullname }}</td><th>Jenis Kelamin :</th><td>{{ regData.sex_name }}</td></tr>
                            <tr><th>Tgl. Lahir :</th><td>{{ regData.birth_date }}</td><th>Ruang / Poliklinik :</th><td>{{ regData.health_facility_item_name }}</td></tr>
                        </table>
                        <div class="section-title">PEMBERIAN INFORMASI</div>
                        <table>
                            <tr><th>Dokter Pelaksana Tindakan</th><td>{{ regData.doctor_in_charge_name }}</td></tr>
                            <tr><th>Pemberi Informasi</th><td>{{ regData.doctor_in_charge_name }}</td></tr>
                            <tr><th>Penerima Informasi</th><td>{{ regData.alias_name }}. {{ regData.fullname }}</td></tr>
                        </table>
                        <table>
                            <tr>
                                <th>JENIS INFORMASI</th>
                                <th>ISI INFORMASI</th>
                                <th>TANDAI (√)</th>
                            </tr>
                            <tr v-for="(item,i) in informasi" :key="i">
                                <td>{{ item.jenis }}</td>
                                <td>
                                    <template v-if="item.jenis === '1. Diagnosis'">
                                        <p v-for="(list, index) in soapData.icd10">
                                            {{ list.code }} - {{ list.name }}
                                        </p>
                                    </template>
                                    <template v-else>
                                        {{ item.isi }}
                                    </template>
                                </td>
                                <td class="checkbox">
                                    <b-icon icon="square-fill" variant="neutral-200" class="mr-8" />
                                    <!-- <b-icon :icon="getAirwayIcon('A1')" :variant="getAirwayIconVariant('A1')" class="mr-8" /> -->
                                    <!-- <input type="checkbox" v-model="item.check" /> -->
                                </td>
                            </tr>
                        </table>
                        <table>
                            <tr>
                                <td style="width:75%; padding:10px; border:1px solid #000;">
                                    Dengan ini menyatakan bahwa saya telah menerangkan hal-hal di atas secara benar dan jelas dan memberikan kesempatan untuk bertanya dan / atau berdiskusi
                                </td>
                                <td style="width:25%; padding:10px; border:1px solid #000; text-align:center;">
                                    <template v-if="regData.employee_signature">
                                        <b-img :src="urlbe+regData.employee_signature" style="width: 120px;"/>
                                    </template><br/>
                                    ( {{ regData.doctor_in_charge_name }} )<br/>
                                </td>
                            </tr>
                            <tr>
                                <td style="width:75%; padding:10px; border:1px solid #000;">
                                    Dengan ini menyatakan bahwa saya telah menerima informasi sebagaimana di atas yang saya beri tanda/paraf di kolom kanannya, dan telah memahaminya
                                </td>
                                <td style="width:25%; padding:10px; border:1px solid #000; text-align:center;">
                                    <br/><br/><br/><br/>( {{ regData.fullname }} )<br/>
                                </td>
                            </tr>
                        </table>
                        <p style="font-size:12px; margin-top:5px;">
                            *Bila pasien tidak kompeten atau tidak cakap untuk menerima informasi, maka penerima informasi adalah wali atau keluarga terdekat
                        </p>
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
            urlbe: process.env.VUE_APP_IMAGE,
            regData: {},
            soapData:{},
            letterData: {},
            days: 1,
            ttdsalah: 'https://beritareceh.com/portal/news/20231101/BR_1698857276.jpg',
            form: {
                nama: '',
                jk: '',
                tglLahir: '',
                poli: '',
                dokter: '',
                pemberi: '',
                penerima: ''
            },
            informasi: [
                { jenis: '1. Diagnosis', isi: '', check: true },
                { jenis: '2. Dasar Diagnosis', isi: 'Informasi pasien, hasil pemeriksaan fisik, hasil pemeriksaan penunjang', check: true },
                { jenis: '3. Tindakan Kedokteran', isi: 'Kolonoskopi dan pengambilan contoh jaringan (=biopsi) bila perlu', check: true },
                { jenis: '4. Indikasi Tindakan', isi: 'Diagnostik', check: true },
                { jenis: '5. Tata Cara', isi: 'Memasukkan kolonoskop melalui dubur ke usus besar hingga perbatasan usus besar–usus halus. Bila ada indikasi dilakukan biopsi sebesar ± 1–2 mm.', check: true },
                { jenis: '6. Tujuan', isi: 'Visualisasi usus besar. Biopsi untuk pemeriksaan mikroskopik.', check: true },
                { jenis: '7. Risiko', isi: 'Kembung, nyeri', check: true },
                { jenis: '8. Komplikasi', isi: 'Perdarahan, terluka, reaksi obat, gangguan cairan dan elektrolit (pada saat urus-urus)', check: true },
                { jenis: '9. Prognosis', isi: 'Meragukan', check: true },
                { jenis: '10. Alternatif & Risiko', isi: 'Kolonoskopi dengan kolonoskop lebih kecil, colon in loop (CIL). CT-scan. CIL & CT-scan: tak jelas / tak tervisualisasi, tak bisa biopsi.', check: true },
                { jenis: '11. Lain-lain', isi: '', check: true }
            ]
        }
    },

    methods: {
        async getData(reg_des_id) {
            try {
                const response = await this.$store.dispatch('registration/getById', reg_des_id)
                this.regData = response[0]
            } catch (error) {
                console.error('error getData at: ', error)
            }
        },
        getSoap (id) {
			this.$store.dispatch('soap/getByRegId', id).then(data => {
                this.soapData = data[0]
                console.log("soapData", this.soapData);
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
    },

    mounted() {
        window.addEventListener('keydown', this.handleKeyPress);
    },

    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },

    created() {
        this.getData(this.$route.params.reg_des_id)
        this.getSoap(this.$route.params.reg_des_id)
        this.geLetter(this.$route.params.letter_id)
    }
}
</script>

<style scoped>
.letter-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 40px 16mm;
    border-bottom: 2px solid;
    border-image: linear-gradient(to right, #3fe0d0 50%, #e73d58 50%) 1;
}

.custom-table td{
    vertical-align: top;
}

@media print {
    @page {
        size: A4;
        margin: 0mm;
    }
}

body { font-family: Arial, sans-serif; padding: 20px; }
table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
th, td { border: 1px solid #000; padding: 6px; font-size: 14px; }
th { text-align: left; }
.title { font-size: 20px; font-weight: bold; margin-bottom: 10px; }
.section-title { font-weight: bold; margin-top: 20px; }
.checkbox { text-align: center; }
</style>
