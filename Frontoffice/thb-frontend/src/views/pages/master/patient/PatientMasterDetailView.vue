<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Master Pasien' },
                { text: 'Detail' },
            ]"
        >
        <section class="">
            <div>
                <section class="col-12">
                    <div class="card" style="box-shadow: 0 0.25rem 1rem 0 rgba(47, 91, 234, 0.125)">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg">
                                    <!-- <router-link :to="{ path: '/patient/detail/' + patientData.registration_destination_id }">
                                        <span class="fs-24 text-uppercase font-weight-bold text-neutral-900" style="line-height: 100%;">{{ patientData.alias_name }}. {{ patientData.fullname }}  </span>
                                    </router-link> -->
                                    <span class="fs-24 text-uppercase font-weight-bold text-neutral-900" style="line-height: 100%;">{{ patientData.alias_name }}. {{ patientData.fullname }}  </span>
                                    <div class="fs-14 text-neutral-900">
                                        <span>{{ patientData.birth_place ? patientData.birth_place : '' }}, {{ localDate(patientData.birth_date) }} ({{ parsedAge(patientData.birth_date) }}) </span>
                                        <span>|  {{ patientData.norm }}  </span>
                                        <span>|  {{ patientData.is_new == 'N' ? 'Pasien Lama' : 'Pasien Baru' }}  </span>
                                        <span>|  {{ patientData.health_facility_item_name | capitalize }}</span>
                                    </div>
                                    <div class="fs-13 text-neutral-500">
                                        <span>|  Nomor KTP: {{ patientData.idcard ? patientData.idcard : '-' }}  </span>
                                        <span>|  Nomor Telepon: {{ patientData.phone ? patientData.phone : '-' }}  </span>
                                        <span>|  Agama: {{ patientData.religion_name ? patientData.religion_name : '-' }}  </span>
                                    </div>
                                </div>
                                <!-- <div class="col-lg-1">
                                    <div class="d-flex w-100 justify-content-end">
                                        <b-button variant="outline-primary" size="sm" >Edit</b-button>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </section>
                <!-- ================================= -->
                <section class="timeline_area section_padding_130">
                    <div class="col-12">
                        <!-- Timeline Area-->
                        <div v-if="groupArry.length > 0" class="apland-timeline-area">
                            <!-- Single Timeline Content-->
                            <section v-for="(item_reg, index_reg) in groupArry" :key="index_reg" class="single-timeline-area">
                                <div class="timeline-date wow fadeInLeft" data-wow-delay="0.1s" style="visibility: visible; animation-delay: 0.1s; animation-name: fadeInLeft;">
                                    <p class="text-right" >{{ localDateTime(item_reg.checkin_date) }}</p>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="single-timeline-content wow fadeInLeft" data-wow-delay="0.3s" style="visibility: visible; animation-delay: 0.3s; animation-name: fadeInLeft;">
                                            <div class="row justify-content-between">
                                                <div class="col-lg timeline-text">
                                                    <h5>{{ item_reg.health_facility_name }} <span class="text-regular-16" >(oleh {{ item_reg.doctor_in_charge_name }})</span> </h5>
                                                    <p>Poliklinik / Ruangan: {{ item_reg.health_facility_item_name }}</p>
                                                    <p>Cara Masuk: {{ item_reg.how_to_enter_name }}</p>
                                                </div>
                                                <div class="col-lg-3 mt-16 mt-lg-0">
                                                    <div class="d-flex w-100 justify-content-lg-end">
                                                        <b-button
                                                            @click="collapseHit(item_reg.registration_destination_id, index_reg)"
                                                            v-b-toggle="'collapse-'+index_reg"
                                                            variant="outline-primary" size="sm">
                                                            <feather-icon icon="ChevronsDownIcon" />
                                                            Tampilkan Semua
                                                        </b-button>
                                                    </div>
                                                </div>
                                            </div>
                                            <b-collapse :id="`collapse-`+index_reg" class="mt-32">
                                                <b-overlay :show="isShowLoading[index_reg]" blur="5rem">
                                                    <div>
                                                        <ModernLabel label="Pendaftaran" :small="`Oleh: ` + item_reg.user_added_name " />
                                                        <div class="mt-16">
                                                            <div v-if="item_reg?.registration_insurances" class="mt-16">
                                                                <h6 class="text-uppercase">Metode Pembayaran</h6>
                                                                <b-table :fields="regInsuranceTable" :items="item_reg.registration_insurances" small bordered style="font-size: 14px;" ></b-table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <b-card no-body class="card mt-16 shadow-sm">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between cursor-pointer header-grey"
                                                            v-b-toggle.riwayat-soap
                                                        >
                                                            <ModernLabel label="RIWAYAT BEROBAT" />
                                                            <b-icon class="mr-3" :icon="isOpenSOAP ? 'chevron-up' : 'chevron-down'" />
                                                        </div>
                                                        <b-collapse id="riwayat-soap" v-model="isOpenSOAP">
                                                            <div class="soap-scroll p-2">
                                                                <template v-if="item_reg.reg_info_obj?.health_facility_id == hfi_inpatient">
                                                                    <b-button
                                                                    :to="`/patient/resume/print/${item_reg.reg_info_obj.registration_destination_id}/${item_reg.resume_list_arry?.[0]?.id}`"
                                                                    :disabled="!item_reg.resume_list_arry || item_reg.resume_list_arry.length === 0"
                                                                    target="_blank"
                                                                    size="sm" variant="w3green"
                                                                    >
                                                                        <feather-icon icon="PrinterIcon" class="mr-1" /> Resume
                                                                    </b-button>
                                                                </template>
                                                                <template>
                                                                    <div v-if="Object.keys(operativeReportData).length !== 0" class="fs-13 mb-3 d-flex justify-content-start">
                                                                        <b-button
                                                                        :to="`/operatie-kamer/print/${ok_id}/${operativeReportData.id}`"
                                                                        target="_blank"
                                                                        size="sm" variant="w3green">
                                                                            <feather-icon icon="PrinterIcon" class="mr-1" /> Laporan Operasi
                                                                        </b-button>
                                                                    </div>
                                                                </template>
                                                                <template v-if="(item_reg?.soap_info_arry || []).length > 0">
                                                                    <div v-for="(item_soap, index_soap) in item_reg.soap_info_arry" class="accordion" role="tablist" :key="index_soap">
                                                                        <section v-if="!(item_soap.health_facility_id == hfi_inpatient && item_soap.is_initial_inpatient == 0)">
                                                                            <LastDoctorAssessment
                                                                                :result="item_soap"
                                                                                :regData="item_reg.reg_info_obj"
                                                                                :isMasterPatient="true"
                                                                            />
                                                                        </section>
                                                                    </div>
                                                                </template>
                                                                <template v-else>
                                                                    <div class="fs-13 mt-8">
                                                                        <i>Tidak ada asesmen dokter yang ditambahkan pada registrasi ini</i>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </b-collapse>
                                                    </b-card>
                                                    <b-card no-body class="mt-16 shadow-sm">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between cursor-pointer header-grey"
                                                            v-b-toggle.riwayat-cppt
                                                        >
                                                            <ModernLabel
                                                                variant="w3green"
                                                                label="RIWAYAT CPPT"
                                                            />
                                                            <b-icon class="mr-3" :icon="isOpenCPPT ? 'chevron-up' : 'chevron-down'" />
                                                        </div>
                                                        <b-collapse id="riwayat-cppt" v-model="isOpenCPPT">
                                                            <div class="cppt-scroll p-2">
                                                                <template v-if="(item_reg?.cppt_info_arry||[]).length > 0">
                                                                    <div v-for="(item_cppt, index_cppt) in item_reg.cppt_info_arry" class="accordion" role="tablist" :key="index_cppt">
                                                                        <template v-if="item_reg?.reg_info_obj">
                                                                            <CpptHistory :visible="true" :result="item_cppt" :regData="item_reg.reg_info_obj" :signature_time="signature_time" :catalog_instruction="catalog_instruction" :signature_units="signature_units" />
                                                                        </template>
                                                                    </div>
                                                                </template>
                                                                <template v-else>
                                                                    <div class="fs-13 mt-8">
                                                                        <i>Tidak ada cppt yang ditambahkan pada registrasi ini</i>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </b-collapse>
                                                    </b-card>

                                                    <b-card no-body class="mt-16 shadow-sm">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between cursor-pointer header-grey"
                                                            v-b-toggle.riwayat-sbar
                                                        >
                                                            <ModernLabel variant="brown" label="RIWAYAT SBAR" />
                                                            <b-icon class="mr-3" :icon="isOpenSBAR ? 'chevron-up' : 'chevron-down'" />
                                                        </div>
                                                        <b-collapse id="riwayat-sbar" v-model="isOpenSBAR">
                                                            <div class="sbar-scroll p-2">
                                                                <template v-if="(item_reg?.sbar_info_arry||[]).length > 0">
                                                                    <div v-for="(item_sbar, index_sbar) in item_reg.sbar_info_arry" class="accordion" role="tablist" :key="index_sbar">
                                                                        <template v-if="item_reg?.reg_info_obj">
                                                                            <SbarHistory :visible="true" :result="item_sbar" :regData="item_reg.reg_info_obj" :signature_time="signature_time" :catalog_instruction="catalog_instruction" :signature_units="signature_units" />
                                                                        </template>
                                                                    </div>
                                                                </template>
                                                                <template v-else>
                                                                    <div class="fs-13 mt-8">
                                                                        <i>Tidak ada sbar yang ditambahkan pada registrasi ini</i>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </b-collapse>
                                                    </b-card>
                                                    <template #overlay>
                                                        <div style="width: 100%; height: 100vh;" class="d-flex justify-content-center align-items-center">
                                                            <div class="position-relative d-flex justify-content-center align-items-center">
                                                            <img src="@/@core/assets/images/apexa.png" class="position-absolute start-0" style="width: 64px;">
                                                            <img src="@/@core/assets/icons/apexa-spinner.svg" alt="-spinner" class="position-relative apexa-spinner">
                                                            </div>
                                                        </div>
                                                    </template>
                                                </b-overlay>
                                            </b-collapse>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                        <div v-else class="apland-timeline-area">
                            <section class="single-timeline-area">
                                <div class="timeline-date wow fadeInLeft" data-wow-delay="0.1s" style="visibility: visible; animation-delay: 0.1s; animation-name: fadeInLeft;">
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="single-timeline-content wow fadeInLeft" data-wow-delay="0.3s" style="visibility: visible; animation-delay: 0.3s; animation-name: fadeInLeft;">
                                            <div class="row justify-content-between">
                                                <div class="col-lg timeline-text">
                                                    <h5> <span class="text-regular-16" >Tidak ada data rekam medis</span> </h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </section>
            </div>
        </section>
        </Layout>
    </div>
</template>
<script>
// import { mapState } from 'vuex'
import ModernLabel from '@/@core/components/label/ModernLabel.vue'
import dateMixin from "@/mixins/dateMixin"
import CpptHistory from '@/views/pages/patient/detail/CpptHistory.vue'
import LastDoctorAssessment from '@/views/pages/patient/detail/LastDoctorAssessment.vue'
import SbarHistory from '@/views/pages/patient/detail/SbarHistory.vue'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import moment from 'moment'
import vSelect from 'vue-select'

export default {
    name: 'PatientMasterDetail',
    mixins: [dateMixin],
    components: {
        vSelect, ModernLabel, ToastificationContent,LastDoctorAssessment,CpptHistory,SbarHistory
    },
    data() {
        return {
            hfi_inpatient: '40f06534-07ec-4221-ad9d-dea08196079f',
            hfi_outpatient: '3cb626f5-9148-4fb1-8663-3d7f028b01a6',
            hfi_emergency: '444b3134-5b75-4b67-af72-a558d46716d7',
            params: {
                norm: '',
                limit: 10,
                offset: 0,
                is_checkout:1,
                // date_start: '2025-05-01',
                // date_end: moment(new Date()).format('YYYY-MM-DD'),
            },
            globalNorm:'',
            // SELECTED DATA NEEDS FROM ALL DATA
            groupArry: [],

            // RAW DATA FROM API
            patientData: {},
            regData: {},
            soapData: [],
            cpptData:[],
            sbarData:[],

            // TABLE INSIDE CARD
            icd10Table: [
                { key: 'code', label: 'Kode' },
                { key: 'name', label: 'Nama' },
            ],
            regInsuranceTable: [
                { key: 'insurance_name', label: 'Jenis Penjamin' },
                { key: 'company_name', label: 'Nama Penjamin' },
                { key: 'number_insurance', label: 'Nomor Anggota' },
                { key: 'service_class_name', label: 'Hak Kelas' },
            ],
            isShowLoading:{},
            resume_list:[],
            //OK
            operativeReportData:{},
            ok_id:'',

            signature_time: [],
            catalog_instruction: [],
            signature_units: [],

            //Accordion
            isOpenSBAR: true,
            isOpenCPPT: true,
            isOpenSOAP: true,
        }
    },
    filters: {
        capitalize(text) {
            if (!text) return "";
            return text.toLowerCase().replace(/\b\w/g, (char) => char.toUpperCase());
        },
        number(value) {
            return Number(value)
        },
        // parsedBody(text){
        //     return String(text || '').replace(/\n/g, "<br>");
        // },
    },
    methods: {
        async collapseHit(regDes_id, index){
            try {
                const item = this.groupArry[index]

                // Check if data already exists
                if (item.soap_info_arry && item.reg_info_obj && item.cppt_info_arry && item.sbar_info_arry) {
                    return
                }

                this.$set(this.isShowLoading, index, true)
                // const res_registration = await this.$store.dispatch('registration/getById', regDes_id).then(data => {
                //     // console.log('res_registration', data)
                //     this.regData = data[0]
                // })
                let getRegdata = this.groupArry.filter(item =>item.registration_destination_id === regDes_id)
                if(getRegdata.length > 0) this.regData = getRegdata[0] || {}
                // console.log('filter regdesId', this.groupArry.filter(item =>item.registration_destination_id === regDes_id))

                const res_soap = await this.$store.dispatch('soap/getByRegId', regDes_id).then(data => {
                    this.soapData = data
                })

                let cpptPayload = {
                    registration_destination_id : regDes_id
                }
                const res_cppt = await this.$store.dispatch('cppt/getListCPPT', cpptPayload).then(data => {
                    this.cpptData = data.rows
                })

                let sbarPayload = {
                    registration_destination_id : regDes_id
                }
                const res_sbar = await this.$store.dispatch('sbar/getListSBAR', sbarPayload).then(data => {
                    this.sbarData = data
                })

                // resume
                let resume_list_arry = [];
                const resumeResponse = await this.$store.dispatch('resume/getAllList', { registration_destination_id: regDes_id });
                if (resumeResponse?.rows?.length > 0) {
                    // console.log(resumeResponse, 'resumeResponse')
                    resume_list_arry = resumeResponse.rows
                }

                //OK
                const okResponse = await this.$store.dispatch('operatie/getDetailOperatingRoomByRegDes', { registration_destination_id: regDes_id });
                if (okResponse?.length > 0) {
                    // console.log(okResponse[0], 'okResponse')
                    this.ok_id = okResponse[0].id
                    await this.$store.dispatch('operatie/getDetailOperativeReportById', { ok_id: this.ok_id })
                    .then(response => {
                        // console.log('getDetailOperativeReportById result: ', response);
                        if(Array.isArray(response) && response.length > 0){
                            this.operativeReportData = response[0]
                        }
                    })
                }


                const itemIndex = this.groupArry.findIndex(item => item.registration_destination_id === regDes_id);
                let soap_info_arry = []
                let reg_info_obj = {}
                let cppt_info_arry = []
                let sbar_info_arry = []

                if (itemIndex !== -1) {
                    // kalo item ketemu
                    soap_info_arry.push(...this.soapData)
                    reg_info_obj = this.regData
                    cppt_info_arry.push(...this.cpptData)
                    sbar_info_arry.push(...this.sbarData)


                    this.$set(this.groupArry, itemIndex, {
                        ...this.groupArry[itemIndex],
                        soap_info_arry,reg_info_obj,
                        cppt_info_arry,resume_list_arry,
                        sbar_info_arry
                    })
                    // console.log(this.groupArry, 'inigrouparry')
                    this.$set(this.isShowLoading, index, false)
                } else {
                    // kalo item ga ada
                    console.warn('REGISTRATION_DESTINATION_ID IS NOT FOUND');
                    this.$set(this.isShowLoading, index, false)

                }
            } catch (error) {
                console.error('ERR WHILE HIT ENDPOINT at: ', error);
                this.$set(this.isShowLoading, index, false)
            }

        },
        // async collapseHit(reg_id){
        //     try {
        //         const res_soap = await this.$store.dispatch('soap/getByRegId', reg_id).then(data => {
        //             this.soapData = data[0]
        //             // console.log('soap: ', this.soapData)
        //             const itemIndex = this.groupArry.findIndex(item => item.registration_destination_id === reg_id);
        //             let obj_soap = {}

        //             if (itemIndex !== -1) {
        //                 // kalo item ketemu
        //                 obj_soap = {
        //                     soap: {
        //                         anamesa_type_id: this.soapData.anamesa_type_id,
        //                         arm_circumference: this.soapData.arm_circumference,
        //                         blood_sugar: this.soapData.blood_sugar,
        //                     }
        //                 }

        //                 this.$set(this.groupArry, itemIndex, {
        //                     ...this.groupArry[itemIndex],
        //                     ...obj_soap
        //                 });

        //                 console.log('groupArry:', this.groupArry);
        //             } else {
        //                 // kalo item ga ada
        //                 console.warn('REGISTRATION_ID IS NOT FOUND');
        //             }

        //         })
        //     } catch (error) {
        //         console.error('ERR WHILE HIT ENDPOINT at: ', error);
        //     }

        // },
        async getData() {
            try {
                let payload_patient = {
                    offset:0,
                    limit:10,
                    norm: this.params.norm
                }
                let payload_registration_master_data = {
                    offset:0,
                    limit:10,
                    norm: this.params.norm
                }
                const res_patient = await this.$store.dispatch('patient/getAll', payload_patient).then(data => {
                    this.patientData = data.data.rows[0]
                    // console.log('patient: ', this.patientData)
                })

                const res_regsMasterDatalist = await this.$store.dispatch('registration/getListPatientVisitMasterData', payload_registration_master_data).then(data => {
                    // console.log('regmasterdatalist', data)
                    this.groupArry = data.data.rows
                })
            } catch (error) {
                console.error('ERR WHILE HIT ENDPOINT at: ', error);
            }

        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        parsed_age(input_date) {
            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const currentDate = moment();

            const years = currentDate.diff(birthDate, 'years');
            birthDate.add(years, 'years');
            const months = currentDate.diff(birthDate, 'months');
            birthDate.add(months, 'months');
            const days = currentDate.diff(birthDate, 'days');

            return `${years} th ${months} bln ${days} hr`;
        },
        localDate(input_date) {
            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },
        localDateTime(input) {
            return moment(input).locale("id").format('DD MMMM YYYY HH:MM')
        },
        getInitial(value){
            if (!value || typeof value !== 'string') return '';
            const words = value.split(' ');
            const initials = words.slice(0, 2).map(word => word.charAt(0).toUpperCase());
            return initials.join('');

            // if (!value || typeof value !== 'string') return '';
            // return value.split(' ').map(word => word.charAt(0).toUpperCase()).join('');
        },

        async getOptSignTime() {
            try {
                await this.$store.dispatch('kardex/getOptSignaturaTime')
                .then(response => {
                    this.signature_time = response
                })
                .catch(({message, debug, response}) => {
                    console.error('Error at: ', response);
                })
            } catch (error) {
                console.error('Error catch at: ', error)
            }
        },

        async getOptCatInstruction() {
            try {
                await this.$store.dispatch('catalog/getOptCatalogInstruction')
                .then(response => {
                    this.catalog_instruction = response
                })
                .catch(({message, debug, response}) => {
                    console.error('Error at: ', response);
                })
            } catch (error) {
                console.error('Error catch at: ', error)
            }
        },

        async getSignaturaUnits() {
            try {
                await this.$store.dispatch('kardex/getOptUnit', {})
                .then(
                    async response => {
                        this.signature_units = response
                    }
                )
                .catch(({message, debug, response}) => {
                    console.error('Error GET kardex/getOptUnit at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET kardex/getOptUnit at: ', error);
            }
        },
    },

    mounted() {
        const getNorm = JSON.parse(localStorage.getItem('obj_norm'))
        // console.log('norm from detail', getNorm.norm);

        if (getNorm?.norm) {
            this.params.norm = getNorm.norm
            this.getData()
            // this.globalNorm = getNorm.norm
            // console.log('params: ',this.params);
        } else {
            console.warn('nothing norm here')
        }

        this.getOptSignTime()
        this.getOptCatInstruction()
        this.getSignaturaUnits()
    },

    created() {
        this.$store.dispatch('helper/getParamProvince', 'province')
        // this.$store.dispatch('patient/getAll', this.params)
        // this.show = true
    },
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>
<style scoped>
    .timeline_area {
        position: relative;
        z-index: 1;
    }
    .single-timeline-area {
        position: relative;
        z-index: 1;
        padding-left: 180px;
    }
    @media only screen and (max-width: 575px) {
        .single-timeline-area {
            padding-left: 100px;
        }
    }
    .single-timeline-area .timeline-date {
        position: absolute;
        width: 180px;
        height: 100%;
        top: 0;
        left: 0;
        z-index: 1;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: start;
        -ms-flex-align: start;
        -ms-grid-row-align: start;
        align-items: start;
        /* -webkit-box-align: center;
        -ms-flex-align: center;
        -ms-grid-row-align: center;
        align-items: center; */
        -webkit-box-pack: end;
        -ms-flex-pack: end;
        justify-content: flex-end;
        padding-right: 60px;
    }
    @media only screen and (max-width: 575px) {
        .single-timeline-area .timeline-date {
            width: 100px;
        }
    }
    .single-timeline-area .timeline-date::after {
        position: absolute;
        width: 3px;
        height: 100%;
        content: "";
        background-color: #ebebeb;
        top: 0;
        right: 30px;
        z-index: 1;
    }
    .single-timeline-area .timeline-date::before {
        position: absolute;
        width: 11px;
        height: 11px;
        border-radius: 50%;
        background-color: #f1c40f;
        content: "";
        /* top: 50%; */
        top: 45px;
        right: 26px;
        z-index: 5;
        margin-top: -5.5px;
    }
    .single-timeline-area .timeline-date p {
        margin-bottom: 0;
        color: #020710;
        font-size: 13px;
        text-transform: uppercase;
        font-weight: 500;
        margin-top: 30px;
    }
    .single-timeline-area .single-timeline-content {
        position: relative;
        z-index: 1;
        padding: 30px 30px 25px;
        border-radius: 6px;
        margin-bottom: 15px;
        margin-top: 15px;
        /* -webkit-box-shadow: 0 0.25rem 1rem 0 rgba(47, 91, 234, 0.125); */
        box-shadow: 0 0.25rem 1rem 0 rgba(47, 91, 234, 0.125);
        border: 1px solid #ebebeb;
    }
    @media only screen and (max-width: 575px) {
        .single-timeline-area .single-timeline-content {
            padding: 20px;
        }
    }
    .single-timeline-area .single-timeline-content .timeline-icon {
        -webkit-transition-duration: 500ms;
        transition-duration: 500ms;
        width: 30px;
        height: 30px;
        background-color: #f1c40f;
        -webkit-box-flex: 0;
        -ms-flex: 0 0 30px;
        flex: 0 0 30px;
        text-align: center;
        max-width: 30px;
        border-radius: 50%;
        margin-right: 15px;
    }
    .single-timeline-area .single-timeline-content .timeline-icon i {
        color: #ffffff;
        line-height: 30px;
    }
    .single-timeline-area .single-timeline-content .timeline-text h6 {
        -webkit-transition-duration: 500ms;
        transition-duration: 500ms;
    }
    .single-timeline-area .single-timeline-content .timeline-text p {
        font-size: 13px;
        margin-bottom: 0;
    }
    .single-timeline-area .single-timeline-content:hover .timeline-icon,
    .single-timeline-area .single-timeline-content:focus .timeline-icon {
        background-color: #020710;
    }
    .single-timeline-area .single-timeline-content:hover .timeline-text h6,
    .single-timeline-area .single-timeline-content:focus .timeline-text h6 {
        color: #3f43fd;
    }

    .sbar-scroll {
        max-height: 500px;
        overflow-y: auto;
    }
    .cppt-scroll {
        max-height: 500px;
        overflow-y: auto;
    }
    .soap-scroll {
        max-height: 500px;
        overflow-y: auto;
    }

    .header-grey {
        background-color: #ECF2F5;
        transition: background-color 0.2s ease;
    }

    .header-grey:hover {
        background-color: #dce6eb;
    }

    .rotate-180 {
       transform: rotate(180deg);
        transition: transform 0.3s ease;
    }
</style>