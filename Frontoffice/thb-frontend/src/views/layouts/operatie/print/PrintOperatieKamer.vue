<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <section ref="left-section">
                        <div class="row align-items-center pb-40">
                            <div class="col-9">
                            </div>
                            <div class="col-3">
                                <div id="dataPasien" class="text-regular-12" style="border: 3px solid black; display: flex; align-items: flex-start; justify-content: center;">
                                    <table>
                                        <tr>
                                            <td style="white-space: nowrap;">Nama</td>
                                            <td style="padding-right: 12px;">:</td>
                                            <td>{{regData.alias_name}}. {{ this.formatName(regData.fullname) }}</td>
                                        </tr>
                                        <tr>
                                            <td style=" white-space: nowrap;">No. RM</td>
                                            <td style="padding-right: 12px;">:</td>
                                            <td>{{ regData.norm }}</td>
                                        </tr>
                                        <tr>
                                            <td style="white-space: nowrap;">Tanggal Lahir</td>
                                            <td style="padding-right: 12px;">:</td>
                                            <td >{{ localDate(regData.birth_date) }}</td>
                                        </tr>
                                        <tr>
                                            <td style="white-space: nowrap;">Jenis Kelamin</td>
                                            <td style="padding-right: 12px;">:</td>
                                            <td >{{ regData.sex_name }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <section class="mt-32">
                            <div class="text-center">
                                <div class="font-weight-bold">LAPORAN PEMBEDAHAN / TINDAKAN</div>
                                <!-- <div class="text-regular-12"><i>(Operative / Procedure Report)</i></div> -->
                            </div>
                            <div class="row mt-16">
                                <div class="col-12">
                                    <table class="mt-16" style="width: 100%;">
                                        <tr class="font-weight-bold">
                                            <td style="width: 120px; vertical-align: top; white-space: nowrap;">Tanggal</td>
                                            <td style="width: 10px; vertical-align: top;">:</td>
                                            <td style="vertical-align: top;">
                                                {{ localDate(operatingData.operation_approved_date) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold" style="vertical-align: top; white-space: nowrap;">Petunjuk</td>
                                            <td class="font-weight-bold" style="vertical-align: top;">:</td>
                                            <td style="white-space: normal; vertical-align: top;">
                                                Beri tanda centang ( ✔ ) pada kolom ☐, coret yang tidak perlu (**), isi sesuai kriteria
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </section>
                        <table class="laporan-table mt-3">
                            <!-- Diagnoses -->
                            <tr>
                                <th colspan="2">Diagnosis pra Prosedur</th>
                                <th colspan="2">Diagnosis pasca Prosedur</th>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div>
                                        <template v-for="(item, index) in operativeReportData.pre_non_icd10">
                                            <div>
                                                <div>{{ item }}</div>
                                            </div>
                                        </template>
                                    </div>
                                </td>
                                <td colspan="2">
                                    <div>
                                        <template v-for="(item, index) in operativeReportData.post_icd10">
                                            <div>
                                                <div>{{ item.icd10_code }} {{ item.icd10_name }}</div>
                                            </div>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                            <!-- Jenis Operasi & Jam Operasi -->
                            <tr>
                                <th colspan="2">Jenis Operasi / Tindakan</th>
                                <th>Jam Mulai</th>
                                <th>Jam Selesai</th>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div v-html="parsedBody(operativeReportData?.therapy_plan ? operativeReportData?.therapy_plan : '-')"></div>
                                </td>
                                <td>
                                    <div >{{ onlyHourMinute(operativeReportData.start_time) }}</div>
                                </td>
                                <td>
                                    <div >{{ onlyHourMinute(operativeReportData.finish_time) }}</div>
                                </td>
                            </tr>

                            <!-- Medical Staff & Jam Anestesi-->
                            <tr>
                                <th>Dokter Operator</th>
                                <th>Asisten</th>
                                <th colspan="2">Perawat Instrumen</th>
                            </tr>
                            <tr>
                                <td>
                                    <div >{{ getDoctorOperator() }}</div>
                                </td>
                                <td>
                                    <div >{{ getAssistantDoctor() }}</div>
                                </td>
                                <td colspan="2">
                                    <div >{{ getInstrumentNurse() }}</div>
                                </td>
                            </tr>
                            <tr>
                                <th>Dokter Anestesi</th>
                                <th>Jenis Anestesi</th>
                                <th>Jam Mulai Anestesi</th>
                                <th>Jam Selesai Anestesi</th>
                            </tr>
                            <tr>
                                <td>
                                    <div >{{ getAnesthesiologist() }}</div>
                                </td>
                                <td>
                                    <div v-html="parsedBody(operativeReportData?.anesthesia_type ? operativeReportData?.anesthesia_type : '-')"></div>
                                </td>
                                <td>
                                    <div >{{ onlyHourMinute(operativeReportData.start_anesthesia_time)}}</div>
                                </td>
                                <td>
                                    <div >{{ onlyHourMinute(operativeReportData.finish_anesthesia_time) }}</div>
                                </td>
                            </tr>
                            <!-- Operation Type and Classification -->
                            <tr>
                                <th colspan="2">Khusus Operasi, Jenis**</th>
                                <th colspan="2">Golongan Operasi**</th>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div class="d-flex mt-12 text-regular-16">
                                        <div class="d-flex pr-3">
                                            <b-icon :icon="getRadioSurgicalWoundIcon('1')" :variant="getRadioSurgicalWoundIconVariant('1')" class="mt-1 mr-8"/>
                                            <div>
                                                <div class="lh-110">Bersih</div>
                                            </div>
                                        </div>
                                        <div class="d-flex pr-3">
                                            <b-icon :icon="getRadioSurgicalWoundIcon('2')" :variant="getRadioSurgicalWoundIconVariant('2')" class="mt-1 mr-8" />
                                            <div>
                                                <div class="lh-110">Bersih Terkontaminasi</div>
                                            </div>
                                        </div>
                                        <div class="d-flex pr-3">
                                            <b-icon :icon="getRadioSurgicalWoundIcon('3')" :variant="getRadioSurgicalWoundIconVariant('3')" class="mt-1 mr-8" />
                                            <div>
                                                <div class="lh-110">Terkontaminasi</div>
                                            </div>
                                        </div>
                                        <div class="d-flex pr-3">
                                            <b-icon :icon="getRadioSurgicalWoundIcon('4')" :variant="getRadioSurgicalWoundIconVariant('4')" class="mt-1 mr-8" />
                                            <div>
                                                <div class="lh-110">Kotor</div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td colspan="2">
                                    <div class="d-flex mt-12 text-regular-16">
                                        <div class="d-flex pr-3">
                                            <b-icon :icon="getRadioSurgicalTypeIcon('1')" :variant="getRadioSurgicalTypeIconVariant('1')" class="mt-1 mr-8"/>
                                            <div>
                                                <div class="lh-110">Sedang</div>
                                            </div>
                                        </div>
                                        <div class="d-flex pr-3">
                                            <b-icon :icon="getRadioSurgicalTypeIcon('2')" :variant="getRadioSurgicalTypeIconVariant('2')" class="mt-1 mr-8" />
                                            <div>
                                                <div class="lh-110">Besar</div>
                                            </div>
                                        </div>
                                        <div class="d-flex pr-3">
                                            <b-icon :icon="getRadioSurgicalTypeIcon('3')" :variant="getRadioSurgicalTypeIconVariant('3')" class="mt-1 mr-8" />
                                            <div>
                                                <div class="lh-110">Khusus</div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <!-- Bleeding and Drain -->
                            <tr>
                                <td colspan="2">
                                    <div class="font-weight-bold">Pendarahan</div>
                                    <div >{{ operativeReportData.bleeding }} mL</div>
                                </td>
                                <td colspan="2">
                                    <div class="font-weight-bold">Drain</div>
                                    <div >{{ operativeReportData.drain }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <div>
                                        <div class="font-weight-bold">Laporan pembedahan / tindakan</div>
                                    </div>
                                    <div v-html="parsedBody(operativeReportData.surgical_report ? operativeReportData.surgical_report : '-')"></div>
                                </td>
                            </tr>
                        </table>
                        <div style="margin-top: 800px;">
                            <div class="mt-8 px-12" style="border: 1px solid black;border-bottom:0px;">
                                <table>
                                    <tr>
                                        <td class="font-weight-bold" style="width:200px; vertical-align: top; white-space: nowrap;">Implant</td>
                                        <td class="px-12 text-right">:</td>
                                        <td>
                                            <div class="d-flex align-items-center text-regular-16">
                                                <div class="d-flex pr-3">
                                                    <b-icon :icon="getRadioImplantTypeIcon('1')" :variant="getRadioImplantTypeIconVariant('1')" class="mr-8"/>
                                                    <div style="line-height: 100%;">Ya</div>
                                                </div>
                                                <div class="d-flex align-items-center pr-3">
                                                    <b-icon :icon="getRadioImplantTypeIcon('0')" :variant="getRadioImplantTypeIconVariant('0')" class="mr-8" />
                                                    <div style="line-height: 100%;">Tidak</div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold" style="vertical-align: top;">Kode Implant</td>
                                        <td class="px-12 text-right">:</td>
                                        <td>{{ operativeReportData.implant_code }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width:200px; height:200px;">&nbsp;</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="px-12" style="border: 1px solid black; border-bottom:0px;">
                                <table style="width:100%; table-layout: fixed;">
                                    <tr>
                                        <td style="vertical-align: top; white-space: normal; word-break: break-word;">
                                            <div class="d-flex align-items-start text-regular-16" style="flex-wrap: wrap;">
                                                <b-icon
                                                    :icon="getRadioComplicationTypeIcon()"
                                                    :variant="getRadioComplicationTypeIconVariant()"
                                                    class="mt-1 mr-8"
                                                />
                                                <div class="pr-1 font-weight-bold">
                                                    Penyulit/komplikasi pada prosedur ini, berupa :
                                                </div>
                                                <div
                                                    v-html="parsedBody(operativeReportData.complications ? operativeReportData.complications : '-')"
                                                    style="word-break: break-word; white-space: normal; max-width:100%;"
                                                ></div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="px-12" style="border: 1px solid black; border-bottom:0px;">
                                <table style="width:100%; table-layout: fixed;">
                                    <tr>
                                        <td style="vertical-align: top; white-space: normal; word-break: break-word;">
                                            <div class="d-flex align-items-start text-regular-16" style="flex-wrap: wrap;">
                                                <b-icon
                                                    :icon="getRadioTissuesOrgansTypeIcon()"
                                                    :variant="getRadioTissuesOrgansTypeIconVariant()"
                                                    class="mt-1 mr-8"
                                                />
                                                <div class="pr-1 font-weight-bold">
                                                    Terdapat jaringan / organ yang diangkat, berupa :
                                                </div>
                                                <div
                                                    v-html="parsedBody(operativeReportData.tissues_organs_removed ? operativeReportData.tissues_organs_removed : '-')"
                                                    style="word-break: break-word; white-space: normal; max-width:100%;"
                                                ></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top; white-space: nowrap;">
                                            <div class="d-flex align-items-center text-regular-16">
                                                <div class="d-flex pr-3">
                                                    <b-icon :icon="getRadioSampleTypeIcon()" :variant="getRadioSampleTypeIconVariant()" class="mt-1 mr-8"/>
                                                    <div class="font-weight-bold">Sample dikirim ke Patologi Anatomi</div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="px-12" style="border: 1px solid black;">
                                <div>
                                    <div class="font-weight-bold">Instruksi penatalaksanaan pasca bedah/tindakan</div>
                                </div>
                                <div v-html="parsedBody(operativeReportData.treatment_management_instructions ? operativeReportData.treatment_management_instructions : '-')"class="mt-12"></div>
                            </div>
                            <div class="d-flex justify-content-end align-items-end mt-24 text-left">
                                <div>
                                    <div>Bekasi, {{ operatingData.operation_approved_date ? localDate(operatingData.operation_approved_date) : '' }}</div>
                                    <!-- <div>Dokter Penanggung Jawab Pelayanan</div>
                                    <div :class="employeeData.ttd ? '' : 'mb-72'"><i>(Responsible Physician)</i></div> -->

                                    <template v-if="doctor_operator_detail && doctor_operator_detail.ttd">
                                        <b-img :src="urlbe+doctor_operator_detail.ttd" style="width: 120px;"/>
                                    </template>
                                    <div>({{ doctor_operator_detail.operator_name }})</div>
                                    <div>Nama & Tanda Tangan Dokter</div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </b-container>
    </div>
</template>
<script>
// import id from '@/@core/assets/plugins/fullcalendar/locale/id';
import dateMixin from '@/mixins/dateMixin';
import optionMixin from '@/mixins/optionMixin';
import moment from 'moment';
import { mapState } from 'vuex';

export default {
    mixins: [optionMixin, dateMixin],
    components: {},
    data() {
        return {
            urlbe:process.env.VUE_APP_IMAGE,
            userData: JSON.parse(localStorage.getItem('userData')),
            admin_id: 'd77142c4-4777-47d8-bd0c-fd7dfd134af4',
            doctor_group_id: 'efb3fdff-fbe3-4954-a391-3729e544b62a',
            today: moment(new Date()).locale('id').format('LL'),
            id: '',
            regData: {},
            resumeData: {},
            employeeData: {},
            reg_des_id: '',
            operative_report_id:'',
            operativeReportData:{},
            operatingData:{},
            doctor_list:[],
            nurse_list:[],
            doctor_operator:'',
            doctor_operator_detail:{},
            assistant_doctor:'',
            instrument_nurse:'',
            anesthesiologist:'',
        }
    },
    computed: {
        anesthesiologist_list() {
            const anesthesiologists = this.doctor_list.filter(item => {
                const cleanName = item?.title?.replace(/\s+/g, ' ').replace(/\s*,\s*/g, ',').trim();
                if(cleanName){
                    const parts = cleanName.split(',Sp.');
                    if (parts.length < 2) return false;

                    const specialization = parts[1].trim().toLowerCase();
                    return specialization.includes('an');
                }
            });

            return anesthesiologists
        },
    },
    methods: {
        formatName(nama) {
            if (!nama) return '';

            const parts = nama.trim().toLowerCase().split(/\s+/);

            if (parts.length === 0) return '';

            // Capitalize each part
            const capitalize = word => word.charAt(0).toUpperCase() + word.slice(1);

            const firstTwo = parts.slice(0, 2).map(capitalize).join(' ');

            const initials = parts
                .slice(2)
                .map(word => word.charAt(0).toUpperCase() + '.')
                .join(' ');

            return initials ? `${firstTwo} ${initials}` : firstTwo;
        },
        localDate(input_date) {
            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },
        // getTime(input_time) {
        //     if(input_time){
        //         return input_time.slice(0,5)
        //     }
        // },
        parsedBody(text){
            return String(text || '').replace(/\n/g, "<br>");
        },
        getRadioSurgicalWoundIcon(value) {
            if (this.operativeReportData && this.operativeReportData.special_operation_type_id) {
                return this.operativeReportData.special_operation_type_id == value ? "check-circle-fill" : "circle-fill";
            } else {
                return "circle-fill";
            }
        },
        getRadioSurgicalWoundIconVariant(value) {
            if (this.operativeReportData && this.operativeReportData.special_operation_type_id) {
                return this.operativeReportData.special_operation_type_id == value ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },
        getRadioSurgicalTypeIcon(value){
            if (this.operativeReportData && this.operativeReportData.operation_group_type_id) {
                return this.operativeReportData.operation_group_type_id == value ? "check-circle-fill" : "circle-fill";
            } else {
                return "circle-fill";
            }
        },
        getRadioSurgicalTypeIconVariant(value) {
            if (this.operativeReportData && this.operativeReportData.operation_group_type_id) {
                return this.operativeReportData.operation_group_type_id == value ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },

        getRadioImplantTypeIcon(value){
            if (this.operativeReportData?.implant_type_id != undefined) {
                return this.operativeReportData.implant_type_id == value ? "check-circle-fill" : "circle-fill";
            } else {
                return "circle-fill";
            }
        },
        getRadioImplantTypeIconVariant(value) {
            if (this.operativeReportData?.implant_type_id != undefined) {
                return this.operativeReportData.implant_type_id == value ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },

        getRadioComplicationTypeIcon(){
            if (this.operativeReportData?.complications) {
                return "check-circle-fill"
            } else {
                return "circle-fill";
            }
        },
        getRadioComplicationTypeIconVariant() {
            if (this.operativeReportData?.complications) {
                return "success"
            } else {
                return "neutral-200";
            }
        },

        getRadioTissuesOrgansTypeIcon(){
            if (this.operativeReportData?.tissues_organs_removed) {
                return  "check-circle-fill";
            } else {
                return "circle-fill";
            }
        },
        getRadioTissuesOrgansTypeIconVariant() {
            if (this.operativeReportData?.tissues_organs_removed) {
                return  "success" ;
            } else {
                return "neutral-200";
            }
        },

        getRadioSampleTypeIcon(){
            if (this.operativeReportData?.samples_sent) {
                return "check-circle-fill";
            } else {
                return "circle-fill";
            }
        },
        getRadioSampleTypeIconVariant() {
            if (this.operativeReportData?.samples_sent) {
                return "success";
            } else {
                return "neutral-200";
            }
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
            location.reload()
        },
        closeWindow(){
            window.close()
        },
        handleBack() {
            this.$router.go(-1);
        },
        getData (id) {
			this.$store.dispatch('registration/getById', id).then(data => {
                this.regData = data[0]
                // console.log('Data: ', this.regData);
            })
            this.$store.dispatch('employee/getDoctorOptions', '').then((response) => {
                this.doctor_list = response
            })

            this.$store.dispatch('employee/getNurseList').then((response) => {
                this.nurse_list = response
            })

            this.getOperatingDetail(this.ok_id)

        },
        async getOperativeReport(ok_id) {
            try {
                await this.$store.dispatch('operatie/getDetailOperativeReportById', { ok_id: ok_id })
                .then((response) => {
                    // console.log('getDetailOperativeReportById result: ', response)
                    this.operativeReportData = response[0]
                    this.getData(this.operativeReportData.registration_destination_id)
                    // untuk laporan operasi autofill dokter/perawat
                    this.fillOperatorInReportOK()
                    this.reg_des_id = this.operativeReportData.registration_destination_id

                })
            } catch (error) {
                console.log('Error while GET OperativeReport at: ', error);
            }
        },
        getOperatorList(){
            try {
                this.$store.dispatch('operatie/getOperatorByOkId', { ok_id: this.ok_id }).then(response => {
                    this.operator_list = response
                    // console.log('this.operator_list', this.operator_list);
                    this.getOperativeReport(this.ok_id)
                })
            } catch (error) {
                console.log('Error while GET DATA OperatorByOkId at: ', error);
            }
        },
        fillOperatorInReportOK() {
            // this.resetOperatorInReportOK()
            if (this.operator_list && Array.isArray(this.operator_list)) {
                if(this.operator_list.length > 0){
                    this.operator_list.forEach(item => {
                        if(item.operator_role_type_id == 1){
                            this.doctor_operator = item.operator_id
                            this.doctor_operator_detail = item
                        }
                        if(item.operator_role_type_id == 2){
                            this.assistant_doctor = item.operator_id
                        }
                        if(item.operator_role_type_id == 3){
                            this.instrument_nurse = item.operator_id
                        }
                        if(item.operator_role_type_id == 5){
                            this.anesthesiologist = item.operator_id
                        }
                    })
                }
            }
        },

        // resetOperatorInReportOK(){
        //     this.doctor_operator = ''
        //     this.assistant_doctor = ''
        //     this.instrument_nurse = ''
        //     this.anesthesiologist = ''
        // },

        getOperatingDetail(ok_id) {
            try {
                this.$store.dispatch('operatie/getDetailById', { ok_id: ok_id } ).then(data => {
                    this.operatingData = data[0]
                    // console.log('this.operatingData: ', this.operatingData);
                })
            } catch (error) {
                console.log('Error while GET DATA at: ', error);
            }
        },

        getDoctorOperator(){
            if(this.doctor_operator){
                const doctorOperator = this.doctor_list.filter(item => {
                    return item.value==this.doctor_operator;
                });
                if(doctorOperator.length > 0){
                    return doctorOperator[0].title
                }
            }
        },
        getAssistantDoctor(){
            if(this.assistant_doctor){
                const assistanDoctor = this.doctor_list.filter(item => {
                    return item.value==this.assistant_doctor;
                });
                if(assistanDoctor.length > 0){
                    return assistanDoctor[0].title
                }
            }
        },
        getInstrumentNurse(){
            if(this.instrument_nurse){
                const instrumentNurse = this.nurse_list.filter(item => {
                    return item.value==this.instrument_nurse;
                });

                if(instrumentNurse.length > 0){
                    return instrumentNurse[0].title
                }
            }
        },
        getAnesthesiologist(){
            if(this.anesthesiologist){
                const anesthesiologistDatum = this.anesthesiologist_list.filter(item => {
                    return item.value==this.anesthesiologist;
                });
                if(anesthesiologistDatum.length > 0){
                    return anesthesiologistDatum[0].title
                }
            }
        }

    },

    mounted() {
        window.addEventListener('keydown', this.handleKeyPress);

    },
    created() {
        if (this.$route.params.ok_id) {
            this.ok_id = this.$route.params.ok_id
            this.getOperatorList()
        }

        if (this.$route.params.report_id) {
            this.operative_report_id = this.$route.params.report_id
        }
    },
    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },
};

</script>
<style>
@import '@/@core/css/main/main.min.css';

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

/* .canvas-wrapper {
    display: flex;
    justify-content: center;
    padding-bottom: 40px;
} */
/* #canvas {
    width: 21.2cm !important;
    height: 100% !important;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    padding: 40px;
} */

.with-line {
    text-decoration: underline;
    text-decoration-thickness: 2px;
    text-underline-offset: 8px;
}

.w-40 {
    width: 40%;
}

.canvas-footer {
    position: absolute;
    bottom: 0;
}

.laporan-table {
    width: 100%;
    border-collapse: collapse;
}

.laporan-table td, .laporan-table th {
    border: 1px solid black;
    padding: 4px 6px;
    vertical-align: top;
}

.laporan-table th {
    font-weight: bold;
    text-align: left;
}

.laporan-table td,
.laporan-table th {
    white-space: normal; /* allow wrapping */
    word-break: break-word; /* break long words if needed */
    vertical-align: top; /* align content to the top of the cell */
}

.laporan-table td div {
    white-space: normal;
    word-break: break-word;
}

</style>