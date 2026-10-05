<template>
    <div>
        <section class="col-12">
            <div class="card" style="box-shadow: 0 0.25rem 1rem 0 rgba(47, 91, 234, 0.125)">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-2">
                            <b-avatar :text="getInitial(patientData.fullname)" size="8rem" rounded></b-avatar>
                        </div>
                        <div class="col-lg">
                            <div class="text-bold-36" >{{ patientData.alias_name }} {{ patientData.fullname }}</div>
                            <div>{{ patientData.norm }} - {{ patientData.sex_name }} - {{ parsed_age(patientData.birth_date) }}</div>
                            <!-- <div>{{ patientData.norm }} - {{ patientData.sex_name }} - {{ patientData.birth_date }}</div> -->
                            <div class="row mt-16">
                                <div class="col-lg-6">
                                    <table>
                                        <tr>
                                            <th style="width: 150px;">Tempat, Tgl Lahir</th>
                                            <td class="px-3">:</td>
                                            <td>{{ patientData.birth_place ? patientData.birth_place : '' }}, {{ patientData.birth_date ? localDate(patientData.birth_date) : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nomor KTP</th>
                                            <td class="px-3">:</td>
                                            <td>{{ patientData.idcard ? patientData.idcard : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nomor Telepon</th>
                                            <td class="px-3">:</td>
                                            <td>{{ patientData.phone ? patientData.phone : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Agama</th>
                                            <td class="px-3">:</td>
                                            <td>{{ patientData.religion_name ? patientData.religion_name : '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-lg-6">
                                    <table>
                                        <tr>
                                            <th style="width: 150px; vertical-align: top;">Alamat</th>
                                            <td class="px-3" style="vertical-align: top;">:</td>
                                            <td>{{ patientData.address_name ? patientData.address_name : '' }} </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 150px; vertical-align: top;">Kecamatan</th>
                                            <td class="px-3" style="vertical-align: top;">:</td>
                                            <td>{{ patientData.district_name ? patientData.district_name : '' }} </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 150px; vertical-align: top;">Kota/Kabupaten</th>
                                            <td class="px-3" style="vertical-align: top;">:</td>
                                            <td>{{ patientData.regency_name ? patientData.regency_name : '' }} </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 150px; vertical-align: top;">Provinsi</th>
                                            <td class="px-3" style="vertical-align: top;">:</td>
                                            <td>{{ patientData.province_name ? patientData.province_name : '' }} </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1">
                            <div class="d-flex w-100 justify-content-end">
                                <b-button variant="outline-primary" size="sm" >Edit</b-button>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================================= -->

        <section class="timeline_area section_padding_130">
            <div class="col-12">
                <!-- Timeline Area-->
                <div class="apland-timeline-area">
                    <!-- Single Timeline Content-->
                    <section v-for="(item, index) in groupArry" :key="index" class="single-timeline-area">
                        <div class="timeline-date wow fadeInLeft" data-wow-delay="0.1s" style="visibility: visible; animation-delay: 0.1s; animation-name: fadeInLeft;">
                            <p class="text-right" >{{ localDateTime(item.registration.date_added) }}</p>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="single-timeline-content wow fadeInLeft" data-wow-delay="0.3s" style="visibility: visible; animation-delay: 0.3s; animation-name: fadeInLeft;">
                                    <div class="row justify-content-between">
                                        <div class="col-lg timeline-text">
                                            <h5>{{ item.registration.health_facility_name }} <span class="text-regular-16" >(oleh {{ item.registration.doctor_in_charge_name }})</span> </h5>
                                            <p>Poliklinik / Ruangan: {{ item.registration.health_facility_item_name }}</p>
                                            <p>Cara Masuk: {{ item.registration.how_to_enter_name }}</p>
                                        </div>
                                        <div class="col-lg-3 mt-16 mt-lg-0">
                                            <div class="d-flex w-100 justify-content-lg-end">
                                                <b-button 
                                                    @click="collapseHit(item.registration_destination_id)"
                                                    v-b-toggle="'collapse-'+index"
                                                    variant="outline-primary" size="sm">
                                                    <feather-icon icon="ChevronsDownIcon" />
                                                    Tampilkan Semua
                                                </b-button>
                                            </div>
                                        </div>
                                    </div>
                                    <b-collapse :id="`collapse-`+index" class="mt-32">
                                        <div>
                                            <ModernLabel label="Pendaftaran" :small="`Oleh: ` + item.registration.user_added " />
                                            <div class="mt-16">
                                                <div v-if="item.registration && item.registration.registration_insurance" class="mt-16">
                                                    <h6 class="text-uppercase">Metode Pembayaran</h6>
                                                    <b-table :fields="regInsuranceTable" :items="item.registration.registration_insurance" small bordered style="font-size: 14px;" ></b-table>
                                                </div>
                                            </div>
                                        </div>
                                        <template v-if="item.soap_info_arry">
                                            <div v-for="(soap_list, index) in item.soap_info_arry" class="accordion" role="tablist">
                                                <b-card no-body class="mb-1">
                                                    <b-card-header @click="fillSoapData(soap_list.id)" block v-b-toggle="'accordion-'+index" header-tag="header" class="font-weight-bold pointer d-flex justify-content-between align-items-center" role="tab" onMouseOver="this.style.backgroundColor='#648DE1'; this.style.color='#FFFFFF'" onMouseOut="this.style.backgroundColor='#FFFFFF'; this.style.color='#212529'" >
                                                        <div>SOAP Tanggal {{ localDate(soap_list.date_added) }}</div>
                                                        <feather-icon icon="ChevronDownIcon" size="18" />
                                                    </b-card-header>
                                                    <!-- <b-card-header header-tag="header" class="p-1" role="tab">
                                                        <b-button @click="fillSoapData(soap_list.id)" block v-b-toggle="'accordion-'+index" variant="outline-primary" class="text-left">Soap tanggal {{ soap_list.date_added }} {{ soap_list.id }}</b-button>
                                                    </b-card-header> -->
                                                    <b-collapse :id="`accordion-`+index" accordion="my-accordion" role="tabpanel">
                                                        <b-card-body>
                                                            <ModernLabel label="Kajian Dokter" :small="`Oleh: ` + soap_list.employee_doctor_name " />

                                                            <div class="mt-16">
                                                                <h6 class="text-uppercase">Anamnesa</h6>
                                                                <b-table-simple small bordered style="font-size: 13px;">
                                                                    <b-tbody>
                                                                        <b-tr>
                                                                            <b-td>Anamnesa</b-td>
                                                                            <b-td>{{soap_list.anamesa_type_id == 1 ? 'Auto Anamnesa' : soap_list.anamesa_type_id == 2 ? 'Allo Anamnesa': '-'}}</b-td>
                                                                        </b-tr>
                                                                        <b-tr>
                                                                            <b-td>Keluhan Utama</b-td>
                                                                            <b-td>{{soap_list.main_complaint}}</b-td>
                                                                        </b-tr>
                                                                        <b-tr>
                                                                            <b-td>Riwayat Penyakit Sekarang</b-td>
                                                                            <b-td>{{soap_list.history_disease_now}}</b-td>
                                                                        </b-tr>
                                                                        <b-tr>
                                                                            <b-td>Riwayat Penyakit Dahulu</b-td>
                                                                            <b-td>{{soap_list.history_disease_past}}</b-td>
                                                                        </b-tr>
                                                                        <b-tr>
                                                                            <b-td>Riwayat Obat</b-td>
                                                                            <b-td>{{soap_list.history_drugs}}</b-td>
                                                                        </b-tr>
                                                                        <b-tr>
                                                                            <b-td>Riwayar Alergi</b-td>
                                                                            <b-td>{{soap_list.history_allergy}}</b-td>
                                                                        </b-tr>
                                                                    </b-tbody>
                                                                </b-table-simple>
                                                            </div>

                                                            <div class="mt-20">
                                                                <div class="row" style="font-size: 13px;">
                                                                    <div class="col-lg-6">
                                                                        <h6 class="text-uppercase">Tanda Vital</h6>
                                                                        <table>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td>Sistolik</td>
                                                                                    <td class="px-1">:</td>
                                                                                    <td>{{ soap_list.systolic_blood_pressure }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Diastolik</td>
                                                                                    <td class="px-1">:</td>
                                                                                    <td>{{ soap_list.diastolic_blood_pressure }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Frekuensi Nadi</td>
                                                                                    <td class="px-1">:</td>
                                                                                    <td>{{ soap_list.pulse_frequency }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Frekuensi Nafas</td>
                                                                                    <td class="px-1">:</td>
                                                                                    <td>{{ soap_list.breath_frequency }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Saturasi Oksigen</td>
                                                                                    <td class="px-1">:</td>
                                                                                    <td>{{soap_list.oximetry_frequency}}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Suhu</td>
                                                                                    <td class="px-1">:</td>
                                                                                    <td>{{ soap_list.temperature }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Gula Darah</td>
                                                                                    <td class="px-1">:</td>
                                                                                    <td>{{ soap_list.blood_sugar }}</td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                        <h6 class="text-uppercase">Antropometri</h6>
                                                                        <table>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td>Berat Badan</td>
                                                                                    <td class="px-1">:</td>
                                                                                    <td>{{ soap_list.weight }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Tinggi Badan</td>
                                                                                    <td class="px-1">:</td>
                                                                                    <td>{{ soap_list.height }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>IMT</td>
                                                                                    <td class="px-1">:</td>
                                                                                    <td>{{ soap_list.imt }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Lingkar Kepala</td>
                                                                                    <td class="px-1">:</td>
                                                                                    <td>{{ soap_list.head_circumference }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Lingkar Dada</td>
                                                                                    <td class="px-1">:</td>
                                                                                    <td>{{ soap_list.chest_circumference }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Lingkar Lengan Atas</td>
                                                                                    <td class="px-1">:</td>
                                                                                    <td>{{ soap_list.arm_circumference }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Lingkar Perut</td>
                                                                                    <td class="px-1">:</td>
                                                                                    <td>{{ soap_list.stomach_circumference }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Lingkar Lutut</td>
                                                                                    <td class="px-1">:</td>
                                                                                    <td>{{ soap_list.knee_circumference }}</td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                
                                                            <div v-if="soap_list && soap_list.icd10" class="mt-20">
                                                                <h6 class="text-uppercase">ICD10</h6>
                                                                <b-table :fields="icd10Table" :items="soap_list.icd10" small bordered style="font-size: 14px;" ></b-table>
                                                            </div>

                                                        </b-card-body>
                                                    </b-collapse>
                                                </b-card>
                                            </div>
                                        </template>
                                    </b-collapse>
                                </div>
                            </div>
                            
                        </div>
                    </section>

                </div>
            </div>
        </section>
    </div>
</template>
<script>
// import { mapState } from 'vuex'
import moment from 'moment'
import vSelect from 'vue-select'
import ModernLabel from '@/@core/components/label/ModernLabel.vue'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default {
    name: 'PatientMasterDetail',
    components: {
        vSelect, ModernLabel, ToastificationContent
    },
    data() {
        return {
            params: {
                norm: '',
                limit: 10,
                offset: 0,
                date_start: '2025-05-01',
                date_end:'2025-05-15',
            },

            // SELECTED DATA NEEDS FROM ALL DATA
            groupArry: [],

            // RAW DATA FROM API
            patientData: {},
            regData: {},
            soapData: [],

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
        }
    },

    methods: {
        async fillSoapData(soap_id){
            try {
                const soapIndex = this.soapData.findIndex(item => item.id === soap_id);

                if (soapIndex !== -1) {
                    // console.log('Data ditemukan di soapData');

                    const soapItem = this.soapData[soapIndex];

                    const groupIndex = this.groupArry.findIndex(group => 
                        group.soap_info_arry && group.soap_info_arry.some(soap => soap.id === soap_id)
                    );

                    if (groupIndex !== -1) {
                        const soapInfoIndex = this.groupArry[groupIndex].soap_info_arry.findIndex(soap => soap.id === soap_id);

                        if (soapInfoIndex !== -1) {
                            this.$set(this.groupArry[groupIndex].soap_info_arry, soapInfoIndex, soapItem);
                            // console.log('Data di soap_info_arry diupdate', this.groupArry);
                        } else {
                            console.warn('ID tidak ditemukan di soap_info_arry');
                        }
                    } else {
                        console.warn('ID tidak ditemukan di groupArry');
                    }
                } else {
                    console.warn('Data tidak ditemukan di soapData');
                }
                
            } catch (error) {
                console.error('ERR WHILE FILL SOAP DATA at: ', error);
            }
        },

        async collapseHit(reg_id){
            try {
                const res_soap = await this.$store.dispatch('soap/getByRegId', reg_id).then(data => {
                    const temp_soap = data
                    // console.log('soap: ', temp_soap)
                    const itemIndex = this.groupArry.findIndex(item => item.registration_destination_id === reg_id);
                    let obj_soap = {}
                    const soap_info_arry = []
                    let soap_info_obj = {}

                    if (itemIndex !== -1) {
                        // kalo item ketemu
                        for (let i = 0; i < temp_soap.length; i++) {
                            const el = temp_soap[i];

                            soap_info_obj = {
                                id: el.id,
                                date_added: el.date_added,
                            }

                            obj_soap = {
                                anamesa_type_id: el.anamesa_type_id,
                                arm_circumference: el.arm_circumference,
                                blood_sugar: el.blood_sugar,
                                breath_frequency: el.breath_frequency,
                                chest_circumference: el.chest_circumference,
                                classification_blood_pressure_type_id: el.classification_blood_pressure_type_id,
                                classification_oximetry_type_id: el.classification_oximetry_type_id,
                                classification_temperature_type_id: el.classification_temperature_type_id,
                                classification_weight_type_id: el.classification_weight_type_id,
                                date_added: el.date_added,
                                diastolic_blood_pressure: el.diastolic_blood_pressure,
                                employee_doctor_name: el.employee_doctor_name,
                                head_circumference: el.head_circumference,
                                height: el.height,
                                history_allergy: el.history_allergy,
                                history_disease_now: el.history_disease_now,
                                history_disease_past: el.history_disease_past,
                                history_drugs: el.history_drugs,
                                icd10: el.icd10,
                                id: el.id,
                                imt: el.imt,
                                knee_circumference: el.knee_circumference,
                                laboratory_request: el.laboratory_request,
                                oximetry_frequency: el.oximetry_frequency,
                                physical_check: el.physical_check,
                                main_complaint: el.main_complaint,
                                physiotheraphy_request: el.physiotheraphy_request,
                                pulse_frequency: el.pulse_frequency,
                                radiology_request: el.radiology_request,
                                stomach_circumference: el.stomach_circumference,
                                systolic_blood_pressure: el.systolic_blood_pressure,
                                temperature: el.temperature,
                                transaction_doctor: el.transaction_doctor,
                                user_updated: el.user_updated,
                                weight: el.weight,
                            }

                            soap_info_arry.push(soap_info_obj)
                            this.soapData.push(obj_soap)
                            // console.log('thissoapdat', this.soapData);
                        }
                        
                        this.$set(this.groupArry, itemIndex, {
                            ...this.groupArry[itemIndex],
                            soap_info_arry,
                        });
                        
                        // console.log('groupArry:', this.groupArry);
                    } else {
                        // kalo item ga ada
                        console.warn('REGISTRATION_ID IS NOT FOUND');
                    }

                })
            } catch (error) {
                console.error('ERR WHILE HIT ENDPOINT at: ', error);
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
        async getData(obj) {
            try {
                const res_patient = await this.$store.dispatch('patient/getAll', this.params).then(data => {
                    this.patientData = data.data.rows[0]
                    // console.log('patient: ', this.patientData)

                })
                
                const res_registration = await this.$store.dispatch('registration/getAll', this.params).then(data => {
                    this.regData = data.data.rows
                    // console.log('regdata: ', this.regData)
                    
                    let obj_registration = {}

                    for (let i = 0; i < this.regData.length; i++) {
                        const el = this.regData[i];
                        
                        obj_registration = {
                            registration_destination_id: el.registration_destination_id,
                            registration: {
                                checkin_date: el.checkin_date,
                                code: el.code,
                                code_queue: el.code_queue,
                                date_added: el.date_added,
                                doctor_in_charge_name: el.doctor_in_charge_name,
                                health_facility_name: el.health_facility_name,
                                health_facility_item_name: el.health_facility_item_name,
                                how_to_enter_name: el.how_to_enter_name,
                                id: el.id,
                                register_date: el.register_date,
                                registration_destination_id: el.registration_destination_id,
                                registration_insurance: el.registration_insurance,
                                queue: el.queue,
                                user_added: el.user_added,
                            }
                        }
                        this.groupArry.push(obj_registration)
                    }
                    // console.log('groupArry: ',this.groupArry);
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
        }
    },

    mounted() {
        const getNorm = JSON.parse(localStorage.getItem('obj_norm'))
        // console.log('norm from detail', getNorm.norm);

        if (getNorm.norm) {
            this.params.norm = getNorm.norm
            // console.log('params: ',this.params);
            this.getData(this.params)
        } else {
            console.warn('nothing norm here')
        }
    },

    created() {
        this.$store.dispatch('patient/getAll', this.params)
        this.$store.dispatch('helper/getParamProvince', 'province')
        this.show = true
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
</style>