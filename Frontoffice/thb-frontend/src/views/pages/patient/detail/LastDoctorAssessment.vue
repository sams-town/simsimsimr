<template>
    <section class="card mt-16 shadow-sm">
        <div class="card-header p-12 bg-white">
            <!-- <b-button @click="testStoreAccess">testStoreAccess</b-button> -->
            <ModernLabel :label="getSoapLabel(result?.health_facility_id, result?.health_facility_item_name, result?.is_initial_inpatient, result?.is_booking)" :small="`Ditambahkan pada ${localFullDay(result?.date_added)} Oleh ${result?.employee_doctor_name}`" />
            <section class="mt-8">
                <b-button v-b-toggle="'accordion-'+result?.id" size="sm" variant="primary" class="mr-1">
                    <feather-icon icon="EyeIcon" size="12" class="mr-1" /> 
                    <span class="fs-12">LIHAT ASESMEN DISINI</span>
                </b-button>
                <template v-if="isMasterPatient">
                    <template v-if="!(result?.health_facility_id == hfi_inpatient && result?.is_initial_inpatient == 0)">
                        <router-link :to="{ path: '/prints/doctor-sbpk-print/'+ result?.id}" target="_blank">
                            <b-button size="sm" variant="outline-primary-500" class="px-16 mr-1">
                                <feather-icon icon="PrinterIcon" size="12" class="mr-1" /> 
                                <span class="fs-12">SBPK</span>
                            </b-button>
                        </router-link>
                        
                        <router-link :to="{ path: '/prints/doctor-studies-print/'+ result?.id}" target="_blank">
                            <b-button size="sm" variant="outline-success-500" class="px-16 mr-1">
                                <feather-icon icon="PrinterIcon" size="12" class="mr-1" /> 
                                <span class="fs-12">ASESMEN</span>
                            </b-button>
                        </router-link>

                        <!-- CONDITIONAL BUTTONs -->

                        <template v-if="result.health_facility_id == hfi_emergency">
                            <router-link :to="{ path: '/prints/doctor-emergency-print/'+ result?.id}" target="_blank">
                                <b-button size="sm" variant="outline-success-500" class="px-16 mr-1">
                                    <feather-icon icon="PrinterIcon" size="12" class="mr-1" /> 
                                    <span class="fs-12">ASESMEN IGD</span>
                                </b-button>
                            </router-link>
                        </template>

                        <b-dropdown v-if="result.health_facility_item_id == hfii_rehabilitation && result.is_medical_rehabilitation == 1" size="sm" id="dropdown-aria" text="UJI FUNGSI" variant="success-500" class="mr-1 fs-12" menu-class="w-100">
                            <router-link :to="{ path: '/prints/doctor-rehabilitation-print/'+ result?.id}" target="_blank">
                                <b-dropdown-item-button>Cetak Formulir</b-dropdown-item-button>
                            </router-link>
                            <router-link :to="{ path: '/prints/doctor-rehabilitation-result-print/'+ result?.id}" target="_blank">
                                <b-dropdown-item-button>Cetak Hasil</b-dropdown-item-button>
                            </router-link>
                        </b-dropdown>

                        <template v-if="result.health_facility_item_id == hfii_hemodialysis && result.is_hemodialysis == 1">
                            <router-link :to="{ path: '/prints/doctor-hemodialysis-print/'+ result?.id}" target="_blank">
                                <b-button size="sm" variant="outline-success-500" class="px-16 mr-1">
                                    <feather-icon icon="PrinterIcon" size="12" class="mr-1" /> 
                                    <span class="fs-12">HEMODIALISA</span>
                                </b-button>
                            </router-link>
                        </template>
                    </template>
                </template>
            </section>
        </div>

        <b-collapse accordion="my-accordion" :id="`accordion-`+result?.id">
            <div class="card-body px-0 py-8">
                <div class="row mx-0">
                    <div class="col-lg-9">
                        <div class="row">
                            <section class="col-lg-4 px-8 fs-12">
                                <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">SUBJECTIVE</div>
                                <section class="mt-8">
                                    <div class="font-weight-bold text-ax-primary-500">KELUHAN UTAMA:</div>
                                    <div>{{result?.main_complaint || '-' }}</div>
                                </section>
                                <section class="mt-12">
                                    <div class="font-weight-bold text-ax-primary-500">RIWAYAT PENYAKIT SEKARANG:</div>
                                    <div v-html="parsedBody(result?.history_disease_now || '-')"></div>
                                </section>
                                <section class="mt-12">
                                    <div class="font-weight-bold text-ax-primary-500">RIWAYAT PENYAKIT DAHULU:</div>
                                    <div v-html="parsedBody(result?.history_disease_past || '-')"></div>
                                </section>
                                <section class="mt-12">
                                    <div class="font-weight-bold text-ax-primary-500">RIWAYAT OBAT:</div>
                                    <div v-html="parsedBody(result?.history_drugs || '-')"></div>
                                </section>
                                <section class="mt-12">
                                    <div class="font-weight-bold text-ax-primary-500">ALERGI:</div>
                                    <template v-if="result?.allergies?.length > 0">
                                        <li v-for="list in result?.allergies">{{ list.name }}</li>
                                    </template>
                                    <div v-else>-</div>
                                </section>
                            </section>

                            <section class="col-lg-4 px-8 fs-12">
                                <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">OBJECTIVE</div>
                                <section class="mt-8">
                                    <div class="font-weight-bold text-ax-primary-500">TANDA VITAL:</div>
                                    <div>
                                        Systolic = {{ result?.systolic_blood_pressure || '-' }},
                                        Diastolic = {{ result?.diastolic_blood_pressure || '-' }},
                                        Frekuensi Nadi = {{ result?.pulse_frequency || '-' }},
                                        Frekuensi Nafas = {{ result?.breath_frequency || '-' }},
                                        Saturasi Oksigen = {{ result?.oximetry_frequency || '-' }},
                                        Suhu = {{ result?.temperature || '-' }},
                                        Oksigenasi = {{ getOxygenName(result?.oxygen_type_id) }},                                        
                                        Kesadaran = {{ getAwareName(result?.aware) }},
                                        GCS Mata = {{ getGcsEyeName(result?.eye_score || '-') }},
                                        GCS Verbal = {{ getGcsVerbalName(result?.verbal_score || '-') }},
                                        GCS Motorik = {{ getGcsMotoricName(result?.motor_score || '-') }},
                                    </div>
                                </section>
                                <section class="mt-12">
                                    <div class="font-weight-bold text-ax-primary-500">ANTROPOMETRI:</div>
                                    <div>
                                        BB = {{ result?.weight || '-' }},
                                        TB = {{ result?.height || '-' }},
                                        IMT = {{ result?.imt || '-' }},
                                        L.Kepala = {{ result?.head_circumference || '-' }},
                                        L.Dada = {{ result?.chest_circumference || '-' }},
                                        L.Lengan Atas = {{ result?.arm_circumference || '-' }},
                                        L.Perut = {{ result?.stomach_circumference || '-' }},
                                        L.Lutut = {{ result?.knee_circumference || '-' }}
                                    </div>
                                </section>
                            </section>

                            <section class="col-lg-4 px-8 fs-12">
                                <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">ASSESSMENT</div>
                                <section class="mt-8">
                                    <div class="font-weight-bold text-ax-primary-500">DIAGNOSIS:</div>
                                    <template v-if="result?.icd10?.length > 0">
                                        <li v-for="(list, index) in result?.icd10">{{ list.code }} {{ list.name }}</li>
                                    </template>
                                    <div v-else class="text-italic">-</div>
                                </section>
                                <section class="mt-12">
                                    <div class="font-weight-bold text-ax-primary-500">CATATAN ICD10:</div>
                                    <div v-html="parsedBody(result?.icd10_notes || '-')"></div>                                    
                                </section>
                                <section class="mt-12">
                                    <div class="font-weight-bold text-ax-primary-500">NON-ICD10:</div>
                                    <template v-if="result?.non_icd10?.length > 0">
                                        <li v-for="(list, index) in result?.non_icd10">{{ list }}</li>
                                    </template>
                                    <div v-else class="text-italic">-</div>
                                </section>
                            </section>

                            <section class="col-12 px-8 fs-12 mt-20">
                                <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">STATUS GENERALIS</div>
                                <div class="mt-8">
                                    <i class="text-danger-500">*teks merah untuk menandai pemeriksaan fisik bermakna</i>
                                    <div class="row fs-11 mt-1">
                                        <div class="col-lg-4">
                                            <table class="w-100">
                                                <tbody>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold text-ax-primary-500 fs-12">KEPALA</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.head)">
                                                        <td style="width: 100px;">Kepala</td>
                                                        <td style="width: 15px;" class="px-1">:</td>
                                                        <td>{{ getHead(result?.head) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.head_hair_color)">
                                                        <td class="align-top">Warna Rambut</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getHairColor(result?.head_hair_color) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.head_hair_condition)">
                                                        <td class="align-top">Kondisi Rambut</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getHairCondition(result?.head_hair_condition) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold text-ax-primary-500 fs-12">MATA</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.eye_conjunctive)">
                                                        <td class="align-top" >Konjungtiva</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getEyeConjunctive(result?.eye_conjunctive) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.eye_sclera)">
                                                        <td class="align-top">Sklera</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getEyeSclera(result?.eye_sclera) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.eye_pupil)">
                                                        <td class="align-top">Pupil</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getEyePupil(result?.eye_pupil) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.eye_light_reflex)">
                                                        <td class="align-top">Reflek Cahaya</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getEyeReflect(result?.eye_light_reflex) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold text-ax-primary-500 fs-12">MULUT</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.mouth_mucosa)">
                                                        <td class="align-top">Mukosa</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getMouthMucosa(result?.mouth_mucosa) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.mouth_discoloration)">
                                                        <td class="align-top">Perubahan Warna</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getMouthDiscoloration(result?.mouth_discoloration) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.mouth_tongue)">
                                                        <td class="align-top">Lidah</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getMouthTongue(result?.mouth_tongue) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.mouth_caries)">
                                                        <td class="align-top">Karies</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getMouthCaries(result?.mouth_caries) }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-lg-4">
                                            <table class="w-100">
                                                <tbody>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold text-ax-primary-500 fs-12">LEHER</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.neck_kgb)">
                                                        <td class="align-top" style="width: 100px;">KGB</td>
                                                        <td style="width: 15px;" class="align-top px-1">:</td>
                                                        <td>{{ getNeckKGB(result?.neck_kgb) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.neck_trachea_position)">
                                                        <td class="align-top">Posisi Trakea</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getNeckTracheaPosition(result?.neck_trachea_position) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.neck_massa)">
                                                        <td class="align-top">Massa</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getNeckMassa(result?.neck_massa) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.neck_jvp)">
                                                        <td class="align-top">JVP</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getNeckJVP(result?.neck_jvp) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold text-ax-primary-500 fs-12">EKSTREMITAS</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.extremity_acral)">
                                                        <td class="align-top">Akral</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getEkstremitasAkral(result?.extremity_acral) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.extremity_crt)">
                                                        <td class="align-top">CRT</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getEkstremitasCRT(result?.extremity_crt) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.extremity_edema)">
                                                        <td class="align-top">Edema</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getEkstremitasEdema(result?.extremity_edema) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold text-ax-primary-500 fs-12">ABDOMEN</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.abdomen_supel)">
                                                        <td class="align-top">Supel</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getAbdomenSupel(result?.abdomen_supel) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.abdomen_bowel_sounds)">
                                                        <td class="align-top">Bising Usus</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getAbdomenBising(result?.abdomen_bowel_sounds) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.abdomen_bowel_sounds)">
                                                        <td class="align-top">Nyeri Tekan</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getAbdomenPress(result?.abdomen_tenderness) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.abdomen_hepar_lien)">
                                                        <td class="align-top">Hepar & Lien</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getAbdomenHepar(result?.abdomen_hepar_lien) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Pemeriksaan Abdomen Lainnya</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td class="align-top">
                                                            <div v-html="parsedBody((result?.abdomen_other_physical_check) ? (result?.abdomen_other_physical_check) : '-')"></div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-lg-4">
                                            <table class="w-100">
                                                <tbody>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold text-ax-primary-500 fs-12">THORAKS</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold">INSPEKSI</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.thorax_insp_chest)">
                                                        <td class="align-top" style="width: 120px;">Dada</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getInspectionChest(result?.thorax_insp_chest) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.thorax_insp_retraction)">
                                                        <td class="align-top">Retraksi</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getInspectionRetractiont(result?.thorax_insp_retraction) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold">PALPASI</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.thorax_insp_chest)">
                                                        <td class="align-top">Dada</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getPalpationChest(result?.thorax_insp_chest) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.thorax_palp_fremitus)">
                                                        <td class="align-top">Fremitus</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getPalpationFremitus(result?.thorax_palp_fremitus) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.thorax_palp_crepitation)">
                                                        <td class="align-top">Krepitasi</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getPalpationKrepitasi(result?.thorax_palp_crepitation) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold">AUSKULTASI</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.thorax_ausk_pul_vasiculer)">
                                                        <td class="align-top">Pulmo Vasikuler</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getAuskultationPulmoVasikuler(result?.thorax_ausk_pul_vasiculer) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.thorax_ausk_pul_rhonki)">
                                                        <td class="align-top">Pulmo Rhonki</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getAuskultationPulmoRhonki(result?.thorax_ausk_pul_rhonki) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.thorax_ausk_pul_wheezing)">
                                                        <td class="align-top">Pulmo Wheezing</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getAuskultationPulmoWheezing(result?.thorax_ausk_pul_wheezing) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.thorax_ausk_cor_heart_sound)">
                                                        <td class="align-top">COR Bunyi Jantung</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getAuskultationCORBJ(result?.thorax_ausk_cor_heart_sound) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.thorax_ausk_cor_murmur)">
                                                        <td class="align-top">COR Murmur</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getAuskultationCORMurmur(result?.thorax_ausk_cor_murmur) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(result?.thorax_ausk_cor_gallop)">
                                                        <td class="align-top">COR Gallop</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getAuskultationCORGallop(result?.thorax_ausk_cor_gallop) }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <table class="w-100 mt-1">
                                        <tbody>
                                            <tr>
                                                <td class="align-top" style="width: 100px;">Genitalia</td>
                                                <td class="align-top px-1" style="width: 15px;">:</td>
                                                <td>
                                                    <div v-html="parsedBody(result?.genitalia || '-')"></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top">Status Lokalis</td>
                                                <td class="align-top px-1">:</td>
                                                <td>
                                                    <div v-html="parsedBody(result?.localist_status || '-')"></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top">Status Neurologis</td>
                                                <td class="align-top px-1">:</td>
                                                <td>
                                                    <div v-html="parsedBody(result?.neurologist_status || '-')"></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top">Status Oftalmologis</td>
                                                <td class="align-top px-1">:</td>
                                                <td>
                                                    <div v-html="parsedBody(result?.ophthal_status || '-')"></div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </section>

                            <template v-if="result?.health_facility_item_id == hfii_hemodialysis">
                                <div class="col-12 px-8 fs-12 mt-20">                                                    
                                    <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">HEMODIALISA</div>
                                    <table class="w-100 mt-8">
                                        <tbody>
                                            <tr>
                                                <td class="align-top" style="width: 120px;">Diagnosis Utama</td>
                                                <td class="align-top px-1 text-center" style="width: 30px;">:</td>
                                                <td class="align-top">{{ result?.main_diagnosis || '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="align-top">Etiologi</td>
                                                <td class="align-top px-1 text-center">:</td>
                                                <td class="align-top">{{ result?.etiology || '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="align-top">Penyakit Penyerta</td>
                                                <td class="align-top px-1 text-center">:</td>
                                                <td class="align-top">{{ result?.comorbidities || '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="align-top">Frekuensi HD</td>
                                                <td class="align-top px-1 text-center">:</td>
                                                <td class="align-top">{{ result?.frequency_hd || '-' }} x/minggu</td>
                                            </tr>
                                            <tr>
                                                <td class="align-top">Akses Vaskular</td>
                                                <td class="align-top px-1 text-center">:</td>
                                                <td class="align-top">
                                                    <div v-html="parsedBody(result?.vascular_access || '-')"></div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </template>

                            <template v-if="result?.health_facility_item_id == hfii_rehabilitation">
                                <div class="col-12 px-8 fs-12 mt-20">
                                    <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">UJI FUNGSI / PROSEDUR KFR</div>
                                    <div class="row fs-12">
                                        <div class="col-lg-4">
                                            <section class="mt-8">
                                                <div class="font-weight-bold text-ax-primary-500">SUSPEK PENYAKIT AKIBAT:</div>
                                                <div>{{ result?.suspected_disease_type_id == '1' ?  `Ya, ${result.suspected_disease}` : 'Tidak' }}</div>
                                            </section>
                                            <section class="mt-12">
                                                <div class="font-weight-bold text-ax-primary-500">PEMERIKSAAN & UJI FUNGSI:</div>
                                                <div v-html="parsedBody(result?.functional_testing || '-')"></div>
                                            </section>
                                            <section class="mt-12">
                                                <div class="font-weight-bold text-ax-primary-500">DIAGNOSA FUNGSIONAL:</div>
                                                <div v-html="parsedBody(result?.functional_diagnosis || '-')"></div>
                                            </section>
                                        </div>
                                        <div class="col-lg-4">
                                            <section class="mt-8">
                                                <div class="font-weight-bold text-ax-primary-500">DIAGNOSA MEDIS:</div>
                                                <div v-html="parsedBody(result?.medical_diagnosis || '-')"></div>
                                            </section>
                                            <section class="mt-12">
                                                <div class="font-weight-bold text-ax-primary-500">HASIL YANG DIDAPAT:</div>
                                                <div v-html="parsedBody(result?.result || '-')"></div>
                                            </section>
                                        </div>
                                        <div class="col-lg-4">
                                            <section class="mt-8">
                                                <div class="font-weight-bold text-ax-primary-500">KESIMPULAN:</div>
                                                <div v-html="parsedBody(result?.conclusion || '-')"></div>
                                            </section>
                                            <section class="mt-12">
                                                <div class="font-weight-bold text-ax-primary-500">REKOMENDASI:</div>
                                                <div v-html="parsedBody(result?.recommendation || '-')"></div>
                                            </section>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template v-if="result.health_facility_item_id == hfii_mcu">
                                <div class="col-12 px-8 fs-12 mt-20">
                                    <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">MEDICAL CHECK UP</div>
                                    <table class="w-100 mt-8">
                                        <tbody>
                                            <tr>
                                                <td class="align-top" style="width: 150px;">Kesimpulan</td>
                                                <td class="align-top px-1 text-center" style="width: 30px;">:</td>
                                                <td class="align-top">
                                                    <div v-html="parsedBody(result?.fit_to_work || '-')"></div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <section class="col-lg-3 px-8 fs-12">
                        <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">PLANNING</div>
                        <section class="mt-8">
                            <div class="font-weight-bold text-ax-primary-500">PROSEDUR:</div>
                            <template v-if="result?.transaction_doctor?.length > 0">
                                <li v-for="list in result.transaction_doctor">{{ list.costing_name }}</li>
                            </template>
                            <div v-else>-</div>
                        </section>
                        <section class="mt-12">
                            <div class="font-weight-bold text-ax-primary-500">ICD9CM:</div>
                            <div>
                                <div v-if="result?.icd9cm?.length > 0">
                                    <li v-for="list in result.icd9cm">{{ list.code }} {{ list.name }}</li>
                                </div>
                                <div v-else>-</div>
                            </div>
                            <div class="mt-1">{{ result?.therapy_plan || '-' }}</div>
                        </section>
                        <section class="mt-12">
                            <div class="font-weight-bold text-ax-primary-500">LABORATORIUM:</div>
                            <template v-if="result?.laboratory_request_new?.length > 0">
                                <template v-for="(il, i) in result.laboratory_request_new">
                                    <div class="mb-12">
                                        <div class="font-weight-bold">{{localDateTime(il.date_request)}}</div>
                                        <li v-for="(list, j) in il.laboratory_request_items">
                                            <template v-if="list.result?.length > 0">
                                                <feather-icon icon="EyeIcon" @click="triggerLabResult({ alias_name: regData.alias_name, name: regData.name, result: list.result, url: list.url,costing_name: list.costing_name, laboratory_request_id: list.laboratory_request_id })" color="blue" v-b-tooltip.hover.left="`Lihat hasil ${list.costing_name}`"/>
                                            </template>
                                            <template v-else>
                                                <img src="./../../../../@core/assets/icons/hourglass.svg" alt="" width="14px" v-b-tooltip.hover.left="`${list.costing_name} telah di order, belum terbit hasil`">
                                            </template>
                                            {{ list.costing_name }} 
                                        </li>
                                    </div>
                                </template>
                            </template>
                            <div v-else>-</div>
                        </section>
                        <section class="mt-12">
                            <div class="font-weight-bold text-ax-primary-500">RADIOLOGI:</div>
                            <template v-if="result?.radiology_request_new?.length > 0">
                                <template v-for="(ir, i) in result.radiology_request_new">
                                    <div class="mb-12">
                                        <div class="font-weight-bold">{{localDateTime(ir.date_request)}}</div>
                                        <li v-for="(list, j) in ir.radiology_request_items">
                                            <template v-if="list.url.length > 0">
                                                <feather-icon icon="EyeIcon" @click="triggerRadResult({ alias_name: regData.alias_name, name: regData.name, url: list.url, costing_name: list.costing_name })" color="blue" v-b-tooltip.hover.left="`Lihat hasil ${list.costing_name}`"/>
                                            </template>
                                            <template v-else>
                                                <img src="./../../../../@core/assets/icons/hourglass.svg" alt="" width="14px" v-b-tooltip.hover.left="`${list.costing_name} telah di order, belum terbit hasil`">
                                            </template>
                                            {{ list.costing_name }} 
                                        </li>
                                    </div>
                                </template>
                            </template>
                            <div v-else class="font-weight-bold">-</div>
                        </section>
                        <section class="mt-12">
                            <div class="font-weight-bold text-ax-primary-500">FISIOTERAPI:</div>
                            <div v-if="result?.physiotherapy_request?.length > 0">
                                <template v-for="(phy, i) in result.physiotherapy_request">
                                    <div class="mb-12">
                                        <div class="font-weight-bold">{{localDateTime(phy.date_request)}}</div>
                                        <li v-for="(list, j) in phy.physiotherapy_request_items">{{ list.costing_name }} </li>
                                    </div>
                                </template>
                            </div>
                            <div v-else>-</div>
                        </section>
                        <section class="mt-12">
                            <div class="font-weight-bold text-ax-primary-500">RESEP:</div>
                            <template v-if="result?.prescription_new?.length > 0">
                                <div v-for="(ipre, i) in result.prescription_new" class="mt-8">
                                    <div class="font-weight-bold">
                                        <template>
                                            <feather-icon v-if="ipre?.flag == 1" icon="CheckSquareIcon" color="green" v-b-tooltip.hover.left="`Berhasil diorder`"/>
                                            <img v-else-if="ipre?.flag == 0" src="./../../../../@core/assets/icons/hourglass.svg" alt="" width="14px" v-b-tooltip.hover.left="`Diproses oleh farmasi`">
                                        </template>
                                        {{localDateTime(ipre.date_added)}}
                                    </div>
                                    <div v-for="(list, idx_p) in ipre.form_prescription_parent" class="mt-1">
                                        <!-- ntr pake ini, store nya udah bisa, kerjainnya pas lagi free aja krn bayak yg perlu dibenerin -->
                                        <!-- <div :class="getParentVariant(list.id)" class="px-12 py-1">
                                            <div class="font-weight-bold">{{ getParentName(list.id) }}</div>
                                            <div v-if="list.id != 1" class="fs-12 text-italic" style="font-weight: 400">  
                                                <span>Kemasan: {{ getParentPackageName(list?.package) }}, </span>
                                                <span>
                                                    {{ list?.value | number }}
                                                    TIAP {{ getDynamicOptName(list?.signatura_times_id, signature_time)}} 
                                                    {{ getDynamicOptName(list?.administer_id, catalog_instruction) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div v-if="ipre?.form_prescription" class="mt-1">
                                            <li v-for="unit in ipre.form_prescription" v-if="list.id == unit.prescription_item_type_id">
                                                {{ unit?.pharmacy_catalog_name }}, {{ unit?.dose }} {{ getDynamicOptName(unit?.dose_unit_id, signature_units) }}
                                                <span v-if="unit?.prescription_item_type_id == 1">
                                                    TIAP {{ getDynamicOptName(unit?.signatura_times_id, signature_time)}} 
                                                    {{ getDynamicOptName(unit?.administer_id, catalog_instruction) }}
                                                </span>
                                            </li>
                                        </div> -->

                                        <div :class="getParentVariant(list.id)" class="px-12 py-1">
                                            <div class="font-weight-bold">{{ getParentName(list.id) }}</div>
                                            <div v-if="list.id != 1" class="fs-12 text-italic" style="font-weight: 400">  
                                                <span>Jumlah: {{ list?.value | number}}, </span>
                                                <!-- <span>Kemasan: {{ getParentPackageName(list.package) }}, </span>
                                                <span>Aturan: {{ list.used }}</span>
                                                <span>Ket: {{ list?.desc }}</span> -->
                                            </div>
                                        </div>
                                        <div v-if="ipre?.form_prescription" class="mt-1">
                                            <li v-for="unit in ipre.form_prescription" v-if="list.id == unit.prescription_item_type_id">
                                                {{ unit?.pharmacy_catalog_name }}
                                                <!-- {{ unit?.pharmacy_catalog_name }}, jml: {{ unit?.qty | number}}
                                                <span v-if="unit?.prescription_item_type_id == 1">, {{ unit?.used }}</span> -->
                                            </li>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div v-else>-</div>
                        </section>
                        <section class="mt-12">
                            <div class="font-weight-bold text-ax-primary-500">ADVICE:</div>
                            <div>{{ result?.advice || '-' }}</div>
                        </section>
                    </section>
                </div>                        
            </div>
        </b-collapse>

        <LabResultModalView ref="labModalResult"/>
        <RadResultModalView ref="radModalResult"/>
        <!-- <div>{{ testStoreAccess() }}</div> -->
    </section>
</template>

<script>
import dateMixin from "@/mixins/dateMixin";
import RadResultModalView from '../medical_support/RadResultModalView.vue';
import LabResultModalView from '../medical_support/LabResultModalView.vue';
// import store from "../../../../store";

export default {
    mixins: [dateMixin],
    components: {
        RadResultModalView,
        LabResultModalView,
    },
    data() {
        return {
            signature_time: [],
            catalog_instruction: [],
            signature_units: [],

            hfi_inpatient: '40f06534-07ec-4221-ad9d-dea08196079f',
            hfi_outpatient: '3cb626f5-9148-4fb1-8663-3d7f028b01a6',
            hfi_emergency: '444b3134-5b75-4b67-af72-a558d46716d7',
            hfii_hemodialysis: 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e',
            hfii_rehabilitation: '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a',
            hfii_mcu: 'd46e2eb6-85a9-4bd4-b515-a0858cc50c8f',

            OptionsOxygen: [
                // { value: 1, title: 'Ya' },
                { value: 2, title: 'Room air' },
                { value: 3, title: 'Nasal kanul (NK)' },
                { value: 4, title: 'Simple mask (SM)' },
                { value: 5, title: 'Rebreathing mask (RM)' },
                { value: 6, title: 'Non-rebreathing mask (NRM)' },
                { value: 7, title: 'Laryngeal mask airways (LMA)' },
                { value: 8, title: 'Endotracheal Tube (ETT)' },
            ],
            OptionsOxygenPoint: [
                { value: 2, title: 'RA' },
                { value: 3, title: 'NK' },
                { value: 4, title: 'SM' },
                { value: 5, title: 'RM' },
                { value: 6, title: 'NRM' },
                { value: 7, title: 'LMA' },
                { value: 8, title: 'ETT' },
            ],
            OptionsAware: [
                { value: 0, title: 'Alert (A)' },
                { value: 1, title: 'Voice (V)' },
                { value: 2, title: 'Pain (P)' },
                { value: 3, title: 'Unresponsive (U)' },
            ],
            OptionsAwarePoint: [
                { value: 0, title: 'A' },
                { value: 1, title: 'V' },
                { value: 2, title: 'P' },
                { value: 3, title: 'U' },
            ],
            OptionGcsEye: [
                { value: 4, title: '(4) Dapat membuka mata secara mandiri' },
                { value: 3, title: '(3) Hanya membuka mata saat diminta, dan tetap tertutup jika tidak' },
                { value: 2, title: '(2) Mata hanya terbuka sebagai respons terhadap tekanan' },
                { value: 1, title: '(1) Mata tidak terbuka sama sekali' },
            ],  
            OptionGcsVerbal: [
                { value: 5, title: '(5) Punya orientasi baik karena mampu menjawab siapa, di mana, serta tanggal atau tahun' },
                { value: 4, title: '(4) Mengalami kebingungan, namun masih bisa menjawab meski kurang paham situasi' },
                { value: 3, title: '(3) Bisa berbicara, tetapi respons tidak masuk akal' },
                { value: 2, title: '(2) Hanya bisa membuat suara, tetapi tidak berbicara' },
                { value: 1, title: '(1) Tidak bisa berbicara atau membuat suara sama sekali' },
            ],
            OptionGcsMotoric: [
                { value: 6, title: '(6) Mampu mengikuti instruksi kapan dan bagaimana harus bergerak' },
                { value: 5, title: '(5) Bergerak menjauh dari tekanan yang dirasakan secara sengaja' },
                { value: 4, title: '(4) Bergerak sebagai refleks terhadap tekanan' },
                { value: 3, title: '(3) Otot menegang (menarik ke dalam) saat merasakan tekanan' },
                { value: 2, title: '(2) Otot meregang (mengulur ke luar) sebagai respons terhadap tekanan' },
                { value: 1, title: '(1) Tidak merespons atau bergerak sama sekali' },
            ],
            OptionsHeadCircumference: [
                { value: 1, title: 'Normocefalus' },
                { value: 2, title: 'Microcefalus' },
                { value: 3, title: 'Hidrocefalus' },
            ],

            OptionsHeadHairColor: [
                { value: 1, title: 'Rambut hitam tebal' },
                { value: 2, title: 'Rambut pirang' },
                { value: 3, title: 'Rambut putih' },
            ],

            OptionsHeadHairCondition: [
                { value: 1, title: 'Tidak mudah dicabut' },
                { value: 2, title: 'Mudah dicabut' },
            ],

            OptionsEyeConjunctiva: [
                { value: 1, title: 'Conjunctiva anemis (-/-)' },
                { value: 2, title: 'Conjunctiva anemis (+/+)' },
            ],
            
            OptionsEyeSclera: [
                { value: 1, title: 'Sklera ikterik (-/-)' },
                { value: 2, title: 'Sklera ikterik (+/+)' },
            ],
            
            OptionsEyePupil: [
                { value: 1, title: 'Pupil isokor' },
                { value: 2, title: 'Pupil anisokor' },
            ],
            
            OptionsEyeReflect: [
                { value: 1, title: 'Reflek cahaya (+/+)' },
                { value: 2, title: 'Reflek cahaya (-/-)' },
            ],

            OptionsMouthMukosa: [
                { value: 1, title: 'Mukosa basah' },
                { value: 2, title: 'Mukosa kering' },
            ],
            
            OptionsMouthHiperemis: [
                { value: 1, title: 'Tidak hiperemis' },
                { value: 2, title: 'Hiperemis' },
            ],
            
            OptionsMouthTongue : [
                { value: 1, title: 'Mukosa & lidah bersih' },
                { value: 2, title: 'Mukosa & lidah kotor' },
                { value: 3, title: 'Terdapat leukoplakia' },
            ],
            
            OptionsMouthKaries : [
                { value: 1, title: 'Karies gigi (-)' },
                { value: 2, title: 'Karies gigi (+)' },
            ],

            OptionsNeckKGB: [
                { value: 1, title: 'Pembesaran KGB (-)' },
                { value: 2, title: 'Pembesaran KGB (+)' },
            ],
            
            OptionsNeckTrakea: [
                { value: 1, title: 'Trakea simetris' },
                { value: 2, title: 'Trakea asimetris' },
            ],
            
            OptionsNeckMassa: [
                { value: 1, title: 'Tidak teraba massa ' },
                { value: 2, title: 'Teraba massa simetris ' },
                { value: 3, title: 'Teraba massa asimetris ' },
            ],

            OptionsNeckJVP: [
                { value: 1, title: 'Peningkatan JVP (-)' },
                { value: 2, title: 'Peningkatan JVP (+)' },
            ],

            OptionsInspectionChest: [
                { value: 1, title: 'Dada simetris, statis & dinamis' },
                { value: 2, title: 'Dada asimetris' },
                { value: 3, title: 'Barrel Chest' },
            ],

            OptionsInspectionRetraction: [
                { value: 1, title: 'Tidak ada retraksi sela iga' },
                { value: 2, title: 'Terdapat retraksi sela iga' },
            ],
            
            OptionsPalpationChest: [
                { value: 1, title: 'Pengembangan dinding dada simetris' },
                { value: 2, title: 'Pengembangan dinding dada asimetris' },
            ],
            
            OptionsPalpationFremitus: [
                { value: 1, title: 'Fremitus normal' },
                { value: 2, title: 'Fremitus kanan lebih keras' },
                { value: 3, title: 'Fremitus kiri lebih keras' },
            ],
            
            OptionsPalpationKrepitasi: [
                { value: 1, title: 'Krepitasi (-)' },
                { value: 2, title: 'Krepitasi (+)' },
            ],

            OptionsAuskultationPulmoVasikuler: [
                { value: 1, title: 'Vesikular (+/+)' },
                { value: 3, title: 'Vesikular (-/-)' },
                { value: 4, title: 'Vesikular (+/-)' },
                { value: 5, title: 'Vesikular (-/+)' },
                { value: 2, title: 'Bronchovesikular (+/+)' },
            ],
            
            OptionsAuskultationPulmoRhonki: [
                { value: 1, title: 'Rhonki (-/-)' },
                { value: 2, title: 'Rhonki (+/+)' },
                { value: 3, title: 'Rhonki (-/+)' },
                { value: 4, title: 'Rhonki (+/-)' },
            ],

            OptionsAuskultationPulmoWheezing: [
                { value: 1, title: 'Wheezing (-/-)' },
                { value: 2, title: 'Wheezing (+/+)' },
                { value: 3, title: 'Wheezing (-/+)' },
                { value: 4, title: 'Wheezing (+/-)' },
            ],

            OptionsAuskultationCORBJ: [
                { value: 1, title: 'Bunyi jantung 1 & 2 reguler' },
                { value: 2, title: 'Bunyi jantung 1 & 2 irreguler' },
            ],
            
            OptionsAuskultationCORMurmur: [
                { value: 1, title: 'Murmur (-)' },
                { value: 2, title: 'Murmur (+)' },
            ],
            
            OptionsAuskultationCORGallop: [
                { value: 1, title: 'Gallop (-)' },
                { value: 2, title: 'Gallop (+)' },
            ],

            OptionsAbdomenSupel: [
                { value: 1, title: 'Supel' },
                { value: 2, title: 'Buncit' },
                { value: 3, title: 'Kempis' },
            ],
            
            OptionsAbdomenBising: [
                { value: 1, title: 'Bising usus (dbn)' },
                { value: 2, title: 'Bising usus menurun' },
                { value: 3, title: 'Bising usus meningkat' },
            ],
            
            OptionsAbdomenPress: [
                { value: 1, title: 'Nyeri Tekan (-)' },
                { value: 2, title: 'Nyeri Tekan ulu hati (+)' },
                { value: 3, title: 'Nyeri Tekan Kanan atas (+)' },
                { value: 4, title: 'Nyeri Tekan Kanan bawah (+)' },
                { value: 5, title: 'Nyeri Tekan perut bawah (+)' },
                { value: 6, title: 'Nyeri Tekan kiri bawah (+)' },
            ],

            OptionsAbdomenHepar: [
                { value: 1, title: 'Hepar & Lien tidak teraba' },
                { value: 2, title: 'Teraba pembesaran hepar' },
                { value: 3, title: 'Teraba pembesaran Lien' },
            ],

            OptionsEkstremitasAkral: [
                { value: 1, title: 'Akral hangat' },
                { value: 2, title: 'Akral dingin' },
            ],
            
            OptionsEkstremitasCRT: [
                { value: 1, title: 'CRT < 2 detik' },
                { value: 2, title: 'CRT ≥ 2 detik' },
            ],
            
            OptionsEkstremitasEdema: [
                { value: 1, title: 'Edema (-)' },
                { value: 2, title: 'Edema keempat eksteimitas (+)' },
                { value: 3, title: 'Edema ekstremitas atas (+/+)' },
                { value: 4, title: 'Edema ekstremitas atas (+/-)' },
                { value: 5, title: 'Edema ekstremitas atas (-/+)' },
                { value: 6, title: 'Edema ekstremitas bawah (+/+)' },
                { value: 7, title: 'Edema ekstremitas bawah (+/-)' },
                { value: 8, title: 'Edema ekstremitas bawah (-/+)' },
            ],

            OptionsOftalVisus: [
                { value: 1, title: '6/60' },
                { value: 2, title: '5/60' },
                { value: 3, title: '4/60' },
                { value: 4, title: '3/60' },
                { value: 5, title: '2/60' },
                { value: 6, title: '1/60' },
                { value: 7, title: '6/36' },
                { value: 8, title: '6/24' },
                { value: 9, title: '6/18' },
                { value: 10, title: '6/12' },
                { value: 11, title: '6/9' },
                { value: 12, title: '6/6' },
                { value: 13, title: '6/5' },
                { value: 14, title: '6/4' },
                { value: 15, title: '6/3' },
                { value: 16, title: '6/2' },
                { value: 17, title: '6/1' },
                { value: 18, title: '1/∞' },
                { value: 19, title: 'NLP' },
            ],

            OptionsOftalTIO: [
                { value: 1, title: 'Normal/palpasi' },
                { value: 2, title: 'Meningkat/palpasi' },
            ],

            OptionsOftalEyePosition: [
                { value: 1, title: 'Orthoporia' },
                { value: 2, title: 'Exothrophia' },
                { value: 3, title: 'Esothrophia' },
                { value: 4, title: 'Exophoria' },
                { value: 5, title: 'Esophoria' },
            ],

            OptionsOftalMovement: [
                { value: 1, title: 'Baik ke segala arah' },
                { value: 2, title: 'Paresis nervus 3' },
                { value: 3, title: 'paresis nervus 4' },
                { value: 4, title: 'paresis nervus 6' },
            ],
            
            OptionsOftalSuperSilia: [
                { value: 1, title: 'Normal' },
                { value: 2, title: 'Tidak normal' },
            ],
            
            OptionsOftalPalpebraEdema: [
                { value: 1, title: 'Edema (-)' },
                { value: 2, title: 'Edema (+)' },
            ],

            OptionsOftalPalpebraPtosis: [
                { value: 1, title: 'Ptosis (-)' },
                { value: 2, title: 'Ptosis (+)' },
            ],

            OptionsOftalPalpebraLagophtalmus: [
                { value: 1, title: 'Lagophtalmus (-)' },
                { value: 2, title: 'Lagophtalmus (+)' },
            ],

            OptionsOftalPalpebraTumor: [
                { value: 1, title: 'Tumor (-)' },
                { value: 2, title: 'Tumor (+)' },
            ],
            
            OptionsOftalApparetus: [
                { value: 1, title: 'Laksrimasi (-)' },
                { value: 2, title: 'Laksrimasi (+)' },
            ],

            OptionsOftalSilia: [
                { value: 1, title: 'Sekret (-)' },
                { value: 2, title: 'Sekret (+)' },
            ],

            OptionsOftalConjHiperemis: [
                { value: 1, title: 'Hiperemis (-)' },
                { value: 2, title: 'Hiperemis (+)' },
            ],

            OptionsOftalConjBleed: [
                { value: 1, title: 'Pendarahan sub konjungtiva (-)' },
                { value: 2, title: 'Pendarahan sub konjungtiva (+)' },
            ],

            OptionsOftalKornea: [
                { value: 1, title: 'Jernih' },
                { value: 2, title: 'Keruh Sebagian' },
                { value: 3, title: 'Keruh' },
            ],

            OptionsOftalAnterior: [
                { value: 1, title: 'Dalam' },
                { value: 2, title: 'Dangkal' },
            ],

            OptionsOftalIrisColor: [
                { value: 1, title: 'Biru' },
                { value: 2, title: 'Coklat' },
                { value: 3, title: 'Hitam' },
            ],

            OptionsOftalIrisKripte: [
            { value: 1, title: 'Kripte (+/Jernih)' },
            { value: 2, title: 'Kripte (-/Keruh)' },
            ],

            OptionsOftalPupilForm: [
                { value: 1, title: 'Regular' },
                { value: 2, title: 'Irregular' },
            ],
            
            OptionsOftalPupilDiameter: [
                { value: 1, title: '1 mm' },
                { value: 2, title: '2 mm' },
                { value: 3, title: '3 mm' },
                { value: 4, title: '4 mm' },
                { value: 5, title: '5 mm' },
                { value: 6, title: '6 mm' },
                { value: 7, title: '7 mm' },
                { value: 8, title: '8 mm' },
                { value: 9, title: '9 mm' },
            ],

            OptionsOftalPupilDirectLight: [
                { value: 1, title: 'RCL (+)' },
                { value: 2, title: 'RCL (-)' },
            ],

            OptionsOftalPupilIndirectLight: [
                { value: 1, title: 'RCTL (+)' },
                { value: 2, title: 'RCTL (-)' },
            ],

            OptionsOftalLens: [
                { value: 1, title: 'Jernih' },
                { value: 2, title: 'Keruh Sebagian' },
                { value: 3, title: 'Keruh' },
            ],

            OptionsOftalGlandula: [
                { value: 1, title: 'Pembesaran (+)' },
                { value: 2, title: 'Penyempitan (-)' },
            ],

            OptionsOftalLapang: [
                { value: 1, title: 'Normal' },
                { value: 2, title: 'Menyempit' },
                { value: 3, title: 'Scotoma central' },
                { value: 4, title: 'Tertutup tirai' },
            ],

            OptionsOftalShadow: [
                { value: 1, title: 'Positif' },
                { value: 2, title: 'Negatif' },
            ],

            parent_package: [
                { value: 1, title: 'PUYER' },
                { value: 2, title: 'KAPSUL' },
                { value: 3, title: 'SALEP' },
                { value: 4, title: 'SIRUP' },
                { value: 5, title: 'POT' },
            ],
        }
    },

    props: {
        result: {
            type: Object,
        },
        regData: {
            type: Object,
        },
        isMasterPatient:{
            type:Boolean,
        }
    },

    filters:{
        number(value) {
            return Number(value)
        },
    },

    methods: {  
        // testStoreAccess() {
        //     try {
        //         console.log('Store state:', store);
        //         // Test 1: Akses store langsung
        //         // console.log('Store state:', this.$store.state);
                
        //         // // Test 2: Akses root state
        //         // console.log('Root state:', this.$store.state?.soap);
                
        //         // // Test 3: Panggil action sederhana
        //         // this.$store.dispatch('global/loadConfig').then(() => {
        //         // console.log('Store dispatch berhasil');
        //         // });
                
        //         return 'Store access berhasil';
        //     } catch (e) {
        //         return `Gagal akses store: ${e.message}`;
        //     }
        // },
        getSoapLabel(unit, hfin, initial, booking) {
            if (unit == this.hfi_outpatient && booking == 1) {
                return 'BOOKING PENUNJANG.'  
            } else if (unit == this.hfi_inpatient && initial != 1) {
                return 'CPPT DOKTER RAWAT INAP'  
            } else {
                return hfin
            }
        },

        getParentVariant(parent_id){
            if (parent_id == 1) return 'bg-primary-100'
            if (parent_id == 2) return 'bg-success-100'
            if (parent_id == 3) return 'bg-orange-100'
            if (parent_id == 4) return 'bg-danger-100'
            if (parent_id == 5) return 'bg-pink-100'
            return 'bg-white'
        },

        getParentName(parent_id){
            if (parent_id == 1) return 'OBAT SATUAN'
            if (parent_id == 2) return 'RACIK 1'
            if (parent_id == 3) return 'RACIK 2'
            if (parent_id == 4) return 'RACIK 3'
            if (parent_id == 5) return 'RACIK 4'
            return '-'
        },

        getMeaningVariant(value) {
            if (value && value != 1) return 'text-danger-500'
            return ''
        },

        triggerRadResult(message) {
            this.$refs.radModalResult.seeRadiologyResult(message);
        },

        triggerLabResult(message) {
            this.$refs.labModalResult.seeLaboratoryResult(message);
        },

        parsedBody(text){
            return String(text || '').replace(/\n/g, "<br>");
        },


        // Anamnesa
        getAnamnesaName(value) {
            const result = this.OptionAnamnesa.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        getFamilyName(value) {
            const result = this.OptionFamily.find((item) => item.value == value);
            return result ? result.title : '-';
        },

        // Tanda Vital
        getOxygenName(value) {
            const result = this.OptionsOxygen.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        getAwareName(value) {
            const result = this.OptionsAware.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        getGcsEyeName(value) {
            const result = this.OptionGcsEye.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        getGcsVerbalName(value) {
            const result = this.OptionGcsVerbal.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        getGcsMotoricName(value) {
            const result = this.OptionGcsMotoric.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        getOxygenPoint(value) {
            const result = this.OptionsOxygenPoint.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        getAwarePoint(value) {
            const result = this.OptionsAwarePoint.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        getGcsEyePoint(value) {
            const result = this.OptionGcsEye.find((item) => item.value == value);
            return result ? result.value : '-';
        },
        getGcsVerbalPoint(value) {
            const result = this.OptionGcsVerbal.find((item) => item.value == value);
            return result ? result.value : '-';
        },
        getGcsMotoricPoint(value) {
            const result = this.OptionGcsMotoric.find((item) => item.value == value);
            return result ? result.value : '-';
        },

        getGcsScore(obj) {
            let eye = Number(this.getGcsEyePoint(obj.eye)) || 0
            let verbal = Number(this.getGcsVerbalPoint(obj.verbal)) || 0
            let motor = Number(this.getGcsMotoricPoint(obj.motor)) || 0
            return eye + verbal + motor
        },

        // Status Generalis
        getHead(param) {
            const obj = this.OptionsHeadCircumference.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getHairColor(param) {
            const obj = this.OptionsHeadHairColor.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getHairCondition(param) {
            const obj = this.OptionsHeadHairCondition.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getEyeConjunctive(param) {
            const obj = this.OptionsEyeConjunctiva.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getEyeSclera(param) {
            const obj = this.OptionsEyeSclera.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getEyePupil(param) {
            const obj = this.OptionsEyePupil.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getEyeReflect(param) {
            const obj = this.OptionsEyeReflect.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getMouthMucosa(param) {
            const obj = this.OptionsMouthMukosa.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getMouthDiscoloration(param) {
            const obj = this.OptionsMouthHiperemis.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getMouthTongue(param) {
            const obj = this.OptionsMouthTongue.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getMouthCaries(param) {
            const obj = this.OptionsMouthKaries.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getNeckKGB(param) {
            const obj = this.OptionsNeckKGB.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getNeckTracheaPosition(param) {
            const obj = this.OptionsNeckTrakea.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getNeckMassa(param) {
            const obj = this.OptionsNeckMassa.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getNeckJVP(param) {
            const obj = this.OptionsNeckJVP.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getInspectionChest(param) {
            const obj = this.OptionsInspectionChest.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getInspectionRetractiont(param) {
            const obj = this.OptionsInspectionRetraction.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getPalpationChest(param) {
            const obj = this.OptionsPalpationChest.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getPalpationFremitus(param) {
            const obj = this.OptionsPalpationFremitus.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getPalpationKrepitasi(param) {
            const obj = this.OptionsPalpationKrepitasi.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getAuskultationPulmoVasikuler(param) {
            const obj = this.OptionsAuskultationPulmoVasikuler.find(el => el.value === param)
            return obj ? obj.title : '-'
        },
        
        getAuskultationPulmoRhonki(param) {
            const obj = this.OptionsAuskultationPulmoRhonki.find(el => el.value === param)
            return obj ? obj.title : '-'
        },
        
        getAuskultationPulmoWheezing(param) {
            const obj = this.OptionsAuskultationPulmoWheezing.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getAuskultationCORBJ(param) {
            const obj = this.OptionsAuskultationCORBJ.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getAuskultationCORMurmur(param) {
            const obj = this.OptionsAuskultationCORMurmur.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getAuskultationCORGallop(param) {
            const obj = this.OptionsAuskultationCORGallop.find(el => el.value === param)
            return obj ? obj.title : '-'
        },
        getAbdomenSupel(param) {
            const obj = this.OptionsAbdomenSupel.find(el => el.value === param)
            return obj ? obj.title : '-'
        },
        getAbdomenBising(param) {
            const obj = this.OptionsAbdomenBising.find(el => el.value === param)
            return obj ? obj.title : '-'
        },
        getAbdomenPress(param) {
            const obj = this.OptionsAbdomenPress.find(el => el.value === param)
            return obj ? obj.title : '-'
        },
        getAbdomenHepar(param) {
            const obj = this.OptionsAbdomenHepar.find(el => el.value === param)
            return obj ? obj.title : '-'
        },
        getEkstremitasAkral(param) {
            const obj = this.OptionsEkstremitasAkral.find(el => el.value === param)
            return obj ? obj.title : '-'
        },
        getEkstremitasCRT(param) {
            const obj = this.OptionsEkstremitasCRT.find(el => el.value === param)
            return obj ? obj.title : '-'
        },
        getEkstremitasEdema(param) {
            const obj = this.OptionsEkstremitasEdema.find(el => el.value === param)
            return obj ? obj.title : '-'
        },   

        getParentPackageName(value) {
            const result = this.parent_package.find((item) => item.value == value);
            return result ? result.title : '-';
        },

        // getDynamicOptName(value, list) {
        //     const result = list.find((item) => item.value === value);
        //     return result ? result.title : '-';
        // },
        
        // async getOptSignTime() {
        //     try {
        //         await store.dispatch('kardex/getOptSignaturaTime')
        //         .then(response => {
        //             this.signature_time = response
        //         })
        //         .catch(({message, debug, response}) => {
        //             console.error('Error at: ', response);
        //         })
        //     } catch (error) {
        //         console.error('Error catch at: ', error)
        //     }
        // },

        // async getOptCatInstruction() {
        //     try {
        //         await store.dispatch('catalog/getOptCatalogInstruction')
        //         .then(response => {
        //             this.catalog_instruction = response
        //         })
        //         .catch(({message, debug, response}) => {
        //             console.error('Error at: ', response);
        //         })
        //     } catch (error) {
        //         console.error('Error catch at: ', error)
        //     }
        // },

        // async getSignaturaUnits() {
        //     try {                
        //         await store.dispatch('kardex/getOptUnit', {})
        //         .then(
        //             async response => {
        //                 this.signature_units = response
        //             }
        //         )
        //         .catch(({message, debug, response}) => {
        //             console.error('Error GET kardex/getOptUnit at: ', response);
        //         })
        //     } catch (error) {
        //         console.error('Error catch while GET kardex/getOptUnit at: ', error);
        //     }
        // },
    },

    // created() {
    //     this.getOptSignTime()
    //     this.getOptCatInstruction()
    //     this.getSignaturaUnits()
    // }
}
</script>