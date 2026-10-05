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
                            <img src="../../../../../@core/assets/images/logo-thb.jpg" width="300%" alt="rs-logo">
                            <br>
                        </div>
                        <div class="col text-center text-bold-28">
                            <!-- <p class="text-bold-14">RS Taman Harapan Baru</p>
                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                            <p>Info: +62 778 4161212 / 0853 7111 1212</p>
                             -->
                        </div>
                        <div class="col-2">
                            <tr>
                                <td><img src="../../../../../@core/assets/images/lafki-logo.png" width="85%" alt="lafki-logo"
                                        align="right">
                                </td>
                            </tr>
                        </div>
                        <div class="col-2">
                            <tr>
                                <td><img src="../../../../../@core/assets/images/apexa.png" width="80%" alt="-logo"
                                        align="right">
                                </td>
                            </tr>
                        </div>
                    </div>
                    <div class="text-uppercase text-center mt-5">
                        <div class="text-bold-24 with-line">SURAT KETERANGAN PEMERIKSAAN</div>
                        <div class="text-regular-24">NOMOR: {{regData.norm}}/MCU-RSHBH/{{romanize(new Date().getMonth() + 1)}}/{{new Date().getFullYear()}}</div>
                    </div>
                    <div class="text-regular-18 mt-32" style="line-height: 140%;">
                        <div>Benar menyatakan bahwa : </div>
                        <table class="mt-16"  style="margin-left: 20px;">
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Nama</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.fullname }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Tanggal Lahir</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ localDate(regData.birth_date) }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Usia</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{parsed_age(regData.birth_date)}}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Jenis Kelamin</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.sex_name }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Perusahaan</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData?.registration_insurance[0]?.company_name }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Tanggal MCU</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ localDate(regData.register_date) }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Lokasi Pelaksanaan</td>
                                <td class="col-1 text-right">:</td>
                                <td>RS Taman Harapan Baru</td>
                            </tr>
                        </table>
                        <div class="mt-5">Telah menjalani pemeriksaan kesehatan menyeluruh (Medical Check Up) di RS Taman Harapan Baru. Berdasarkan hasil pemeriksaan yang dilakukan pada tanggal tersebut, yang bersangkutan dinyatakan : <b>{{ soapData.fit_to_work }}</b>
                        </div>
                        <div class="mt-5">Hasil pemeriksaan lengkap terlampir bersama surat ini. Sehubungan dengan temuan tersebut, kami merekomendasikan agar pihak perusahaan melakukan pemantauan lebih lanjut sesuai dengan hasil evaluasi medis dan anjuran dokter.
                        </div>
                        <div class="d-flex justify-content-start w-100 mt-64">
                            <div>
                                <div>Bekasi, {{ localDate(regData.register_date) }}</div>
                                <div :class="soapData.employee_signature ? '' : 'mb-64'">Dokter MCU</div>
                                <!-- <div :class="soapData.employee_signature ? '' : 'mb-64'">Nama Jelas Dan Tanda Tangan
                                </div> -->
                                <template v-if="soapData.employee_signature">
                                    <b-img :src="urlbe + soapData.employee_signature" style="width: 120px;" />
                                </template>
                                <div>{{ soapData.employee_doctor_name }}</div>
                            </div>
                        </div>
                        <div style="margin-top: 700px;">
                            <div class="mt-20">
                                <section>
                                    <ModernLabel label="ANAMNESA" class="mt-16" />
                                    <table class="w-100 mt-16">
                                        <tbody>
                                            <tr>
                                                <td style="width: 300px;">Anamnesa</td>
                                                <td style="width: 30px;" class="px-3">:</td>
                                                <td>{{ soapData?.anamesa_type_id == '1' ? 'Auto Anamnesa' : 'Allo Anamnesa' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Keluarga</td>
                                                <td class="px-3">:</td>
                                                <td>
                                                    <template v-if="soapData?.anamesa_type_id == 2">
                                                        {{ soapData && soapData.family_name ? soapData.family_name : '-' }}
                                                        ({{ getFamilyName(soapData?.family_relation_id || '-') }})
                                                    </template>
                                                    <template v-else>-</template>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top" >Keluhan Utama</td>
                                                <td class="align-top px-3">:</td>
                                                <td>{{ soapData && soapData.main_complaint ? soapData.main_complaint : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="align-top">Riwayat Penyakit Sekarang</td>
                                                <td class="align-top px-3">:</td>
                                                <td>
                                                    <div v-html="parsedBody(soapData && soapData.history_disease_now ? soapData.history_disease_now : '-')"></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top pt-2">Riwayat Penyakit Dahulu</td>
                                                <td class="align-top pt-2 px-3">:</td>
                                                <td class="pt-2">
                                                    <div v-html="parsedBody(soapData && soapData.history_disease_past ? soapData.history_disease_past : '-')"></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top pt-2">Riwayat Penyakit Keluarga</td>
                                                <td class="align-top pt-2 px-3">:</td>
                                                <td class="pt-2">
                                                    <div v-html="parsedBody(soapData && soapData.history_disease_family ? soapData.history_disease_family : '-')"></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top pt-2">Riwayat Kebiasaan</td>
                                                <td class="align-top pt-2 px-3">:</td>
                                                <td class="pt-2">
                                                    <div v-html="parsedBody(soapData && soapData.history_habitude ? soapData.history_habitude : '-')"></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top pt-2">Riwayat Obat</td>
                                                <td class="align-top pt-2 px-3">:</td>
                                                <td class="pt-2">
                                                    <div v-html="parsedBody(soapData && soapData.history_drugs ? soapData.history_drugs : '-')"></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top pt-2">Riwayat Alergi</td>
                                                <td class="align-top pt-2 px-3">:</td>
                                                <td class="pt-2">
                                                    <div v-html="parsedBody(soapData && soapData.history_allergy ? soapData.history_allergy : '-')"></div>
                                                </td>
                                            </tr>
                                            <!-- <tr>
                                                <td class="align-top pt-2">Riwayat Obat</td>
                                                <td class="align-top pt-2 px-3">:</td>
                                                <td class="pt-2">
                                                    <div v-html="parsedBody(nurseData && nurseData.history_drugs ? nurseData.history_drugs : '-')"></div>
                                                </td>
                                            </tr> -->
                                        </tbody>
                                    </table>
                                </section>
                                <div class="mt-20">
                                    <ModernLabel label="Pemeriksaan Fisik" />
                                    <table class="w-100 mt-16">
                                        <tbody>
                                            <tr>
                                                <td style="width: 300px;">Alat Bantu</td>
                                                <td style="width: 30px;" class="px-3">:</td>
                                                <td>{{ nurseData && nurseData.aids ? nurseData.aids : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Prothesa</td>
                                                <td class="px-3">:</td>
                                                <td>{{ nurseData && nurseData.prothesa ? nurseData.prothesa : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Cacat Tubuh</td>
                                                <td class="px-3">:</td>
                                                <td>{{ nurseData && nurseData.disabled ? nurseData.disabled : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Activity Daily Living</td>
                                                <td class="px-3">:</td>
                                                <td>
                                                    <span v-if="nurseData && nurseData.adl_type_id != '0'">
                                                        <template v-if="nurseData.adl_type_id == '1'">Mandiri</template>
                                                        <template v-else-if="nurseData.adl_type_id == '2'">Dibantu</template>
                                                        <template v-else-if="nurseData.adl_type_id == '3'">Dibantu Sebagian</template>
                                                        <template v-else>-</template>
                                                    </span>
                                                    <template v-else>-</template>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">
                                                    <div class="row pt-20">
                                                        <div class="col-6">
                                                            <h6 class="text-uppercase">Tanda Vital</h6>
                                                            <table class="w-100">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 200px;">Sistolik</td>
                                                                        <td style="width: 30px;" class="px-1 text-center">:</td>
                                                                        <td>{{ soapData && soapData.systolic_blood_pressure ? soapData.systolic_blood_pressure : '-' }} mmHg</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Diastolik</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ soapData && soapData.diastolic_blood_pressure ? soapData.diastolic_blood_pressure : '-' }} mmHg</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Frekuensi Nadi</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ soapData && soapData.pulse_frequency ? soapData.pulse_frequency : '-' }} x/mnt</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Frekuensi Nafas</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ soapData && soapData.breath_frequency ? soapData.breath_frequency : '-' }} x/mnt</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Saturasi Oksigen</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ soapData && soapData.oximetry_frequency ? soapData.oximetry_frequency : '-'}} %</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Suhu</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ soapData && soapData.temperature ? soapData.temperature : '-' }} ᵒC</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="align-top">Oksigen</td>
                                                                        <td class="align-top px-1 text-center">:</td>
                                                                        <td>{{ getOxygenName(soapData?.oxygen_type_id || '-') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="align-top">Kesadaran</td>
                                                                        <td class="align-top px-1 text-center">:</td>
                                                                        <td>{{ getAwareName(soapData?.aware) }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="align-top">GCS Mata</td>
                                                                        <td class="align-top px-1 text-center">:</td>
                                                                        <td>{{ getGcsEyeName(soapData?.eye_score || '-') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="align-top">GCS Verbal</td>
                                                                        <td class="align-top px-1 text-center">:</td>
                                                                        <td>{{ getGcsVerbalName(soapData?.verbal_score || '-') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="align-top">GCS Motorik</td>
                                                                        <td class="align-top px-1 text-center">:</td>
                                                                        <td>{{ getGcsMotoricName(soapData?.motor_score || '-') }}</td>
                                                                    </tr>
                                                                    <!-- <tr>
                                                                        <td>Skor Nyeri</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ soapData && soapData.sore_score ? soapData.sore_score : '-' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>EWS</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ soapData && soapData.ews ? soapData.ews : '-' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Gula Darah</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ soapData && soapData.blood_sugar ? soapData.blood_sugar : '-' }} mg/dL</td>
                                                                    </tr> -->
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="col-6">
                                                            <h6 class="text-uppercase">Antropometri</h6>
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 200px;">Berat Badan</td>
                                                                        <td style="width: 30px;" class="px-1 text-center">:</td>
                                                                        <td>{{ soapData && soapData.weight ? soapData.weight : '-' }} kg</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Tinggi Badan</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ soapData && soapData.height ? soapData.height : '-' }} cm</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>IMT</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ soapData && soapData.imt ? soapData.imt : '-' }} kg/m²</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Lingkar Kepala</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ soapData && soapData.head_circumference ? soapData.head_circumference : '-' }} cm</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Lingkar Dada</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ soapData && soapData.chest_circumference ? soapData.chest_circumference : '-' }} cm</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Lingkar Lengan Atas</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ soapData && soapData.arm_circumference ? soapData.arm_circumference : '-' }} cm</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Lingkar Perut</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ soapData && soapData.stomach_circumference ? soapData.stomach_circumference : '-' }} cm</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Lingkar Lutut</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ soapData && soapData.knee_circumference ? soapData.knee_circumference : '-' }} cm</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div style="margin-top: 700px;display: none;">
                            <div class="mt-20">
                                <ModernLabel label="Pemeriksaan Fisik" />
                                <table class="w-100 mt-16">
                                    <tbody>
                                        <tr>
                                            <td style="width: 200px;">Alat Bantu</td>
                                            <td style="width: 30px;" class="px-3">:</td>
                                            <td>{{ nurseData && nurseData.aids ? nurseData.aids : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Prothesa</td>
                                            <td class="px-3">:</td>
                                            <td>{{ nurseData && nurseData.prothesa ? nurseData.prothesa : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Cacat Tubuh</td>
                                            <td class="px-3">:</td>
                                            <td>{{ nurseData && nurseData.disabled ? nurseData.disabled : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <div class="row pt-20">
                                                    <div class="col-6">
                                                        <h6 class="text-uppercase">Tanda Vital</h6>
                                                        <table class="w-100">
                                                            <tbody>
                                                                <tr>
                                                                    <td style="width: 200px;">Sistolik</td>
                                                                    <td style="width: 30px;" class="px-1 text-center">:</td>
                                                                    <td>{{ soapData && soapData.systolic_blood_pressure ? soapData.systolic_blood_pressure : '-' }} mmHg</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Diastolik</td>
                                                                    <td class="px-1 text-center">:</td>
                                                                    <td>{{ soapData && soapData.diastolic_blood_pressure ? soapData.diastolic_blood_pressure : '-' }} mmHg</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Frekuensi Nadi</td>
                                                                    <td class="px-1 text-center">:</td>
                                                                    <td>{{ soapData && soapData.pulse_frequency ? soapData.pulse_frequency : '-' }} x/mnt</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Frekuensi Nafas</td>
                                                                    <td class="px-1 text-center">:</td>
                                                                    <td>{{ soapData && soapData.breath_frequency ? soapData.breath_frequency : '-' }} x/mnt</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Saturasi Oksigen</td>
                                                                    <td class="px-1 text-center">:</td>
                                                                    <td>{{ soapData && soapData.oximetry_frequency ? soapData.oximetry_frequency : '-'}} %</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Suhu</td>
                                                                    <td class="px-1 text-center">:</td>
                                                                    <td>{{ soapData && soapData.temperature ? soapData.temperature : '-' }} ᵒC</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="align-top">Oksigen</td>
                                                                    <td class="align-top px-1 text-center">:</td>
                                                                    <td>{{ getOxygenName(soapData?.oxygen_type_id || '-') }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="align-top">Kesadaran</td>
                                                                    <td class="align-top px-1 text-center">:</td>
                                                                    <td>{{ getAwareName(soapData?.aware) }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="align-top">GCS Mata</td>
                                                                    <td class="align-top px-1 text-center">:</td>
                                                                    <td>{{ getGcsEyeName(soapData?.eye_score || '-') }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="align-top">GCS Verbal</td>
                                                                    <td class="align-top px-1 text-center">:</td>
                                                                    <td>{{ getGcsVerbalName(soapData?.verbal_score || '-') }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="align-top">GCS Motorik</td>
                                                                    <td class="align-top px-1 text-center">:</td>
                                                                    <td>{{ getGcsMotoricName(soapData?.motor_score || '-') }}</td>
                                                                </tr>
                                                                <!-- <tr>
                                                                    <td>Skor Nyeri</td>
                                                                    <td class="px-1 text-center">:</td>
                                                                    <td>{{ soapData && soapData.sore_score ? soapData.sore_score : '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>EWS</td>
                                                                    <td class="px-1 text-center">:</td>
                                                                    <td>{{ soapData && soapData.ews ? soapData.ews : '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Gula Darah</td>
                                                                    <td class="px-1 text-center">:</td>
                                                                    <td>{{ soapData && soapData.blood_sugar ? soapData.blood_sugar : '-' }} mg/dL</td>
                                                                </tr> -->
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="col-6">
                                                        <h6 class="text-uppercase">Antropometri</h6>
                                                        <table>
                                                            <tbody>
                                                                <tr>
                                                                    <td style="width: 200px;">Berat Badan</td>
                                                                    <td style="width: 30px;" class="px-1 text-center">:</td>
                                                                    <td>{{ soapData && soapData.weight ? soapData.weight : '-' }} kg</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Tinggi Badan</td>
                                                                    <td class="px-1 text-center">:</td>
                                                                    <td>{{ soapData && soapData.height ? soapData.height : '-' }} cm</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>IMT</td>
                                                                    <td class="px-1 text-center">:</td>
                                                                    <td>{{ soapData && soapData.imt ? soapData.imt : '-' }} kg/m²</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <section class="mt-20">
                                <ModernLabel label="ANAMNESA" />
                                <table class="w-100 mt-16">
                                    <tbody>
                                        <tr>
                                            <td style="width: 200px;">Anamnesa</td>
                                            <td style="width: 30px;" class="px-3">:</td>
                                            <td>{{ soapData.anamesa_type_id == '1' ? 'Auto Anamnesa' : 'Allo Anamnesa' }}</td>
                                        </tr>
                                        <tr v-if="soapData?.anamesa_type_id == 2">
                                            <td>Keluarga</td>
                                            <td class="px-3">:</td>
                                            <td>
                                                <template v-if="soapData?.anamesa_type_id == 2">
                                                    {{ soapData && soapData.family_name ? soapData.family_name : '-' }}
                                                    ({{ getFamilyName(soapData?.family_relation_id || '-') }})
                                                </template>
                                                <template v-else>-</template>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-top" >Keluhan Utama</td>
                                            <td class="align-top px-3">:</td>
                                            <td>{{ soapData.main_complaint ? soapData.main_complaint : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top">Riwayat Penyakit Sekarang</td>
                                            <td class="align-top px-3">:</td>
                                            <td>
                                                <div v-html="parsedBody(soapData.history_disease_now ? soapData.history_disease_now : '-')"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-top pt-2">Riwayat Penyakit Dahulu</td>
                                            <td class="align-top pt-2 px-3">:</td>
                                            <td class="pt-2">
                                                <div v-html="parsedBody(soapData.history_disease_past ? soapData.history_disease_past : '-')"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-top pt-2">Riwayat Penyakit Keluarga</td>
                                            <td class="align-top pt-2 px-3">:</td>
                                            <td class="pt-2">
                                                <div v-html="parsedBody(soapData.history_disease_family ? soapData.history_disease_family : '-')"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-top pt-2">Riwayat Kebiasaan</td>
                                            <td class="align-top pt-2 px-3">:</td>
                                            <td class="pt-2">
                                                <div v-html="parsedBody(soapData.history_habitude ? soapData.history_habitude : '-')"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-top pt-2">Riwayat Obat</td>
                                            <td class="align-top pt-2 px-3">:</td>
                                            <td class="pt-2">
                                                <div v-html="parsedBody(soapData.history_drugs ? soapData.history_drugs : '-')"></div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </section>
                        </div>
                        <div style="margin-top: 700px;">
                            <section class="mt-20">
                                <ModernLabel label="Pemeriksaan Fisik" />
                                <div class="row mt-16">
                                    <div class="col-6">
                                        <table class="w-100">
                                            <tbody>
                                                <tr>
                                                    <td colspan="3" class="font-weight-bold">Kepala</td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 200px;">Kepala</td>
                                                    <td style="width: 30px;" class="px-3">:</td>
                                                    <td>{{ generalist.head }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Warna Rambut</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.head_hair_color }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Kondisi Rambut</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.head_hair_condition }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="font-weight-bold">Mata</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top" >Konjungtiva</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.eye_conjunctive }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top" >Visus Mata Kanan</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.ophthal_od.visus }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top" >Visus Mata Kiri</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.ophthal_os.visus }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top" >Ptosis</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.ophthal_od.palpebra_ptosis }} {{ generalist.ophthal_os.palpebra_ptosis }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top" >Lagophtalmus</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.ophthal_od.palpebra_lagophtalmus }} {{ generalist.ophthal_os.palpebra_lagophtalmus }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Sklera</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.eye_sclera }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Reflek Cahaya</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.eye_light_reflex }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Pupil</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.eye_pupil }}</td>
                                                </tr>
                                                <!-- <tr>
                                                    <td class="align-top" >Lapang Pandang</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.ophthal_od.field_view }} {{ generalist.ophthal_os.field_view }}</td>
                                                </tr> -->
                                                <tr>
                                                    <td class="align-top">Buta Warna</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.eye_blind_color }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="font-weight-bold">Mulut</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Mukosa</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.mouth_mucosa }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Perubahan Warna</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.mouth_discoloration }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Lidah</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.mouth_tongue }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Karies</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.mouth_caries }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="font-weight-bold">Leher</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">KGB</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.neck_kgb }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Posisi Trakea</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.neck_trachea_position }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Massa</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.neck_massa }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="font-weight-bold">Ekstremitas</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Akral</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.extremity_acral }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">CRT</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.extremity_crt }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Edema</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.extremity_edema }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-6">
                                        <table class="w-100">
                                            <tbody>
                                                <tr>
                                                    <td colspan="3" class="font-weight-bold">Thoraks</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="font-weight-bold text-primary">Inspeksi</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top" style="width: 200px;">Dada</td>
                                                    <td style="width: 30px;" class="align-top px-3">:</td>
                                                    <td>{{ generalist.thorax_insp_chest }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Retraksi</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.thorax_insp_retraction }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="font-weight-bold text-primary">Palpasi</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Dada</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.thorax_palp_chest }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Fremitus</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.thorax_palp_fremitus }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Krepitasi</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.thorax_palp_crepitation }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="font-weight-bold text-primary">Auskultasi</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Pulmo Vasikuler</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.thorax_ausk_pul_vasiculer }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Pulmo Rhonki</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.thorax_ausk_pul_rhonki }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Pulmo Wheezing</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.thorax_ausk_pul_wheezing }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">COR Bunyi Jantung</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.thorax_ausk_cor_heart_sound }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">COR Murmur</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.thorax_ausk_cor_murmur }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">COR Gallop</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.thorax_ausk_cor_gallop }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="font-weight-bold">Abdomen</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Supel</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.abdomen_supel }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Bising Usus</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.abdomen_bowel_sounds }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Nyeri Tekan</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.abdomen_tenderness }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Hepar & Lien</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.abdomen_hepar_lien }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Pemeriksaan Abdomen Lainnya</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td class="align-top">
                                                        <div v-html="parsedBody(generalist.abdomen_other_physical_check ? generalist.abdomen_other_physical_check : '-')"></div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="mt-16">
                                    <table class="w-100">
                                        <tbody>
                                            <tr>
                                                <td colspan="3" class="font-weight-bold">Genitalia</td>
                                            </tr>
                                            <tr>
                                                <td class="align-top" style="width: 200px;">Genitalia</td>
                                                <td class="align-top px-3" style="width: 30px;">:</td>
                                                <td>
                                                    <div v-html="parsedBody(generalist.genitalia ? generalist.genitalia : '-')"></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="font-weight-bold">Status Lokalis</td>
                                            </tr>
                                            <tr>
                                                <td class="align-top">Status Lokalis</td>
                                                <td class="align-top px-3">:</td>
                                                <td>
                                                    <div v-html="parsedBody(generalist.localist_status ? generalist.localist_status : '-')"></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top">Status Neurologis</td>
                                                <td class="align-top px-3">:</td>
                                                <td>
                                                    <div v-html="parsedBody(generalist.neurologist_status ? generalist.neurologist_status : '-')"></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="font-weight-bold">Status Oftalmologis</td>
                                            </tr>
                                            <tr>
                                                <td class="align-top">Status Oftalmologis</td>
                                                <td class="align-top px-3">:</td>
                                                <td>
                                                    <div v-html="parsedBody(generalist.ophthal_status ? generalist.ophthal_status : '-')"></div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- <div class="mt-12">
                                        <table class="w-100">
                                            <tbody>
                                                <tr>
                                                    <td class="align-top" style="width: 200px;">Pemeriksaan Fisik Lainnya</td>
                                                    <td style="width: 30px;" class="align-top px-3">:</td>
                                                    <td>
                                                        <div v-html="parsedBody(result.soap_o_anythingelse ? result.soap_o_anythingelse : '-')"></div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div> -->
                                </div>
                            </section>
                            <div style="margin-top: 700px;" v-if="orderData">
                                <div class="row align-items-center">
                                    <div class="col-2">
                                        <img src="../../../../../@core/assets/images/logo-thb.jpg" width="300%" alt="rs-logo">
                                        <br>
                                    </div>
                                    <div class="col text-center text-bold-28">
                                        <!-- <p class="text-bold-14">RS Taman Harapan Baru</p>
                                        <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                                        <p>Info: +62 778 4161212 / 0853 7111 1212</p>
                                         -->
                                    </div>
                                    <div class="col-2">
                                        <tr>
                                            <td><img src="../../../../../@core/assets/images/lafki-logo.png" width="85%" alt="lafki-logo" align="right">
                                            </td>
                                        </tr>
                                    </div>
                                    <div class="col-2">
                                        <tr>
                                            <td><img src="../../../../../@core/assets/images/apexa.png" width="80%" alt="-logo" align="right">
                                            </td>
                                        </tr>
                                    </div>
                                </div><br>
                                <p class="text-center text-bold-16">HASIL PEMERIKSAAN LABORATORIUM</p>
                                <div class="row mx-0 mt-28">
                                    <div class="col-6">
                                        <table >
                                            <tr>
                                                <td>Nama</td>
                                                <td style="vertical-align:top;" class="px-3">:</td>
                                                <td style="vertical-align:top;">{{regData.name}}</td>
                                            </tr>
                                            <tr>
                                                <td>Usia</td>
                                                <td style="vertical-align:top;" class="px-3">:</td>
                                                <td style="vertical-align:top;">{{parsed_age(this.regData.birth_date)}}</td>
                                            </tr>
                                            <tr>
                                                <td>Jenis Kelamin</td>
                                                <td style="vertical-align:top;" class="px-3">:</td>
                                                <td style="vertical-align:top;">{{regData.sex_name}}</td>
                                            </tr>
                                            <!-- <tr>
                                                <td>Alamat <br> <i class="text-regular-12">Address</i></td>
                                                <td style="vertical-align:top;" class="px-3">:</td>
                                                <td style="vertical-align:top;">{{regData.address}}</td>
                                            </tr>
                                            <tr>
                                                <td>Tgl. Permintaan <br> <i class="text-regular-12">Date Request</i></td>
                                                <td style="vertical-align:top;" class="px-3">:</td>
                                                <td style="vertical-align:top;">{{orderData.date_request}}</td>
                                            </tr>
                                            <tr>
                                                <td>Tgl. Hasil <br> <i class="text-regular-12">Date Reported</i></td>
                                                <td style="vertical-align:top;" class="px-3">:</td>
                                                <td style="vertical-align:top;">{{orderData.date_result}}</td>
                                            </tr> -->
                                            <!-- <tr>
                                                <td>Tgl. Cetak <br> <i class="text-regular-12">Print Out Date</i></td>
                                                <td style="vertical-align:top;" class="px-3">:</td>
                                                <td style="vertical-align:top;">{{orderData.date_request}}</td>
                                            </tr> -->
                                        </table>
                                    </div>
                                    <div class="col-6">
                                        <table>
                                            <tr>
                                                <td>No. Lab</td>
                                                <td style="vertical-align:top;" class="px-3">:</td>
                                                <td style="vertical-align:top;">{{orderData.no_request}}</td>
                                            </tr>
                                            <tr>
                                                <td>Tanggal MCU</td>
                                                <td style="vertical-align:top;" class="px-3">:</td>
                                                <td style="vertical-align:top;">{{ localDate(regData.register_date) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Perusahaan</td>
                                                <td style="vertical-align:top;" class="px-3">:</td>
                                                <td style="vertical-align:top;">{{ regData?.registration_insurance[0]?.company_name }}</td>
                                            </tr>
                                            <!-- <tr>
                                                <td>Dokter Pengirim <br> <i class="text-regular-12">Reffering Doctor</i></td>
                                                <td style="vertical-align:top;" class="px-3">:</td>
                                                <td style="vertical-align:top;">{{orderData.doctor_in_charge_name}}</td>
                                            </tr>
                                            <tr>
                                                <td>Dokter Lab <br> <i class="text-regular-12">Lab Doctor</i></td>
                                                <td style="vertical-align:top;" class="px-3">:</td>
                                                <td style="vertical-align:top;">dr. Andini Triasti Siregar, M.ked, Sp.PK</td>
                                            </tr> -->
                                        </table>
                                    </div>
                                </div>
                                <!-- /info-table -->
                                <div class="px-3 mt-128">
                                    <div class="text-right">
                                        <i class="text-regular-12"><u>PJ Laboratorium : Novia Liyanti</u></i>
                                    </div>
                                    <div v-for="(group, category) in result" :key="category">
                                        <b-table small :fields="resultFields" :items="group" class="table-borderless" tbody-tr-class="text-regular-16" :style="{ margin: '0', padding: '0' }">
                                            <p class="text-regular-18 mt-4"><b>{{ category }}</b></p>
                                            <template #cell(result)="row" :style="{ margin: '0', padding: '0' }">
                                                <b-table small
                                                    :fields="[
                                                        { key: 'index', label: '', thStyle: { width: '10px' },  },
                                                        { key: 'Parameter', label: row.item.laboratory_package_name, thStyle: { width: '185px' },  },
                                                        { key: 'hasil', label: 'Hasil' , thStyle: { width: '185px' }, class: 'text-center' },
                                                        { key: 'flag', label: 'Flag' , thStyle: { width: '55px' }, class: 'text-center'},
                                                        { key: 'n_rujukan', label: 'Nilai Rujukan' , thStyle: { width: '185px' }, class: 'text-center'},
                                                        { key: 'satuan', label: 'Satuan' , thStyle: { width: '185px' }, class: 'text-center'},
                                                        { key: 'metoda', label: 'Metoda' , thStyle: { width: '185px' }, class: 'text-center'},
                                                    ]"
                                                    :items="row.item.result"
                                                    tbody-tr-class="custom-row" :style="{ margin: '0', padding: '0' }">
                                                >
                                                    <template #cell(Parameter)="row" :style="{ borderStyle: 1, padding: '0' }">
                                                        <p class="ml-3">{{ row.item.Parameter }}</p>
                                                    </template>
                                                </b-table>
                                            </template>
                                        </b-table>
                                    </div>
                                </div>
                                <div class="px-3 mt-32">
                                    <div class="text-center">
                                        <i>Validation by </i> : dr. Andini Triasti Siregar, M.ked, Sp.PK  |
                                        <i>QC by </i> : Novia Liyanti
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- {{ thumbnail }} -->
                        <img v-for="value in thumbnail" :src="value" width="100%" v-if="thumbnail" />
                        <div id="pdf-canvas" style="display: none;"></div>
                        <div class="row align-items-center pb-40" style="border-bottom: 2px solid black;margin-top: 3000px;" v-if="orderDataRad?.no_request && reqItemData">
                            <div class="col-2 text-center">
                                <img src="../../../../../@core/assets/images/logo-thb-small.png" width="60%" alt="rs-logo"> <br>
                            </div>
                            <div class="col text-center">
                                <p class="text-bold-18">RS Taman Harapan Baru</p>
                                <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                                <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                            </div>
                            <div class="col-2 text-center">
                                <img src="../../../../../@core/assets/images/lafki-logo.png" width="60%" alt="lafki-logo"> <br>
                            </div>
                        </div>
                        <div class="mt-20" v-if="orderDataRad?.no_request && reqItemData">
                            <p class="text-center text-bold-16">EXPERTISE RADIOLOGI</p>
                            <div class="row mx-0 mt-28">
                                <div class="col-7">
                                    <table>
                                        <tr>
                                            <td class="first-cell">No. Reg / RM</td>
                                            <td class="px-3">:</td>
                                            <td>{{regData.code}} / {{regData.norm}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Nama Pasien</td>
                                            <td class="px-3">:</td>
                                            <td>{{ regData.alias_name }} {{regData.name}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Tanggal Order</td>
                                            <td class="px-3">:</td>
                                            <td>{{orderDataRad?.date_request ? localDate(orderDataRad?.date_request) : ''}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Tanggal Hasil</td>
                                            <td class="px-3">:</td>
                                            <td>
                                            <template v-if="reqItemData[0].url">
                                                <template v-for="list in reqItemData[0].url">
                                                    <template v-if="list.status !== 1"></template>
                                                    {{list.date_result ? localDate(list.date_result) : ''}}
                                                </template>
                                            </template>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Alamat</td>
                                            <td class="px-3">:</td>
                                            <td>{{regData.address}}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-5">
                                    <table>
                                        <tr>
                                            <td class="first-cell">No. Radiologi</td>
                                            <td class="px-3">:</td>
                                            <td>{{orderDataRad.no_request}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Tanggal Lahir</td>
                                            <td class="px-3">:</td>
                                            <td>{{regData.birth_date ? localDate(regData.birth_date) : ''}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Umur</td>
                                            <td class="px-3">:</td>
                                            <td>{{parsed_age(regData.birth_date)}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Kelamin</td>
                                            <td class="px-3">:</td>
                                            <td>{{regData.sex_name}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell"> Dokter Pengirim</td>
                                            <td class="px-3">:</td>
                                            <td>{{ regData.doctor_in_charge_name }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="mt-32 col">
                                <div>Yth.{{ regData.doctor_in_charge_name }}</div>
                                <div class="mt-12">{{ reqItemData[0].costing_name }}</div>
                                <template v-if="reqItemData[0].url">
                                    <template v-for="list in reqItemData[0].url">
                                        <template v-if="list.status !== 1">
                                            <table class="w-100 custom-border">
                                                <tr>
                                                    <td class="align-top" style="width: 150px;">Klinis</td>
                                                    <td class="align-top text-center px-8" style="width: 30px;">:</td>
                                                    <td>
                                                        <template v-if="result.icd10">
                                                            <li v-for="(item, index) in result.icd10" :key="index">{{ item.code }} {{ item.name }}</li>
                                                        </template>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Expertise</td>
                                                    <td class="align-top text-center px-8">:</td>
                                                    <td><span v-html="parsedBody(list.conclusion ? list.conclusion : '-')"></span></td>
                                                </tr>
                                                <tr class="font-weight-bold">
                                                    <td class="align-top pt-32">Kesan</td>
                                                    <td class="align-top text-center px-8 pt-32">:</td>
                                                    <td class="pt-32"><span v-html="parsedBody(list.advice ? list.advice : '-')"></span></td>
                                                </tr>
                                            </table>
                                            <div class="mt-32 d-flex justify-content-end text-center">
                                                <div>
                                                    <div>Bekasi, {{ localDate(list.date_result) }}</div>
                                                    <div :class="list.ttd_name ? '' : 'mb-72'">Salam Sejawat</div>
                                                    <b-img :src="urlbe+list.ttd_name" style="width: 220px;" />
                                                    <!-- <div>{{ list.ttd_name }}</div> -->
                                                    <div>{{ list.doctor_lab_name }}</div>
                                                </div>
                                            </div>
                                        </template>
                                    </template>
                                </template>
                            </div>
                            <div style="margin-top: 200px;">
                                <template v-if="reqItemData[0].url">
                                    <template v-for="list in reqItemData[0].url">
                                        <img :src="`http://192.168.100.3:5000/${list.url}`" width="90%" v-if="list.url" />
                                    </template>
                                </template>
                            </div>
                        </div>

                        <!-- <iframe style="border: none" width="100%" height="1300" :src="`http://192.168.100.3:5000/${patient.registration_insurance[0].file_kronologis}`" v-if="patient.registration_insurance[0].file_kronologis" /> -->
                        <!-- <iframe style="width: 100vw; height: 100vh" :src="`http://192.168.100.3:5000/${item_data.url}`" v-if="item_data.url" /> -->
                        <!-- <template v-for="(item,i) in examination_list" v-if="examination_list.length > 0">
                            <template v-for="(item_data,x) in item.data.files" v-if="item.data.files.length > 0">
                                <img style="width:900px" :src="`http://192.168.100.3:5000/${item_data.url}`" v-if="item_data.url" />
                                {{ item_data.expertise }}
                            </template>
                        </template> -->
                        <template v-for="(item,i) in examination_list" v-if="examination_list.length > 0">
                            <div class="row align-items-center pb-40" style="border-bottom: 2px solid black;margin-top: 3000px;" v-if="examination_list.length > 0">
                                <div class="col-2 text-center">
                                    <img src="../../../../../@core/assets/images/logo-thb-small.png" width="60%" alt="rs-logo"> <br>
                                </div>
                                <div class="col text-center">
                                    <p class="text-bold-18">RS Taman Harapan Baru</p>
                                    <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                                    <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                                </div>
                                <div class="col-2 text-center">
                                    <img src="../../../../../@core/assets/images/lafki-logo.png" width="60%" alt="lafki-logo"> <br>
                                </div>
                            </div>
                            <p class="text-center text-bold-16 text-uppercase mt-28">PEMERIKSAAN {{ item.category_examination_name }}</p>
                            <div class="row mx-0 mt-28">
                                <div class="col-6">
                                    <table>
                                        <tr>
                                            <td class="first-cell">No. Reg / RM</td>
                                            <td class="px-3">:</td>
                                            <td>{{regData.code}} / {{regData.norm}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Nama Pasien</td>
                                            <td class="px-3">:</td>
                                            <td>{{ regData.alias_name }} {{regData.name}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Tanggal Pemeriksaan</td>
                                            <td class="px-3">:</td>
                                            <td>{{item?.data?.files[0].examination_date ? localDate(item?.data?.files[0].examination_date) : ''}}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-6">
                                    <table>
                                        <tr>
                                            <td class="first-cell">Tanggal Lahir</td>
                                            <td class="px-3">:</td>
                                            <td>{{regData.birth_date ? localDate(regData.birth_date) : ''}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Umur</td>
                                            <td class="px-3">:</td>
                                            <td>{{parsed_age(regData.birth_date)}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Kelamin</td>
                                            <td class="px-3">:</td>
                                            <td>{{regData.sex_name}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell"> Dokter Pengirim</td>
                                            <td class="px-3">:</td>
                                            <td>{{ regData.doctor_in_charge_name }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="mt-32 col">
                                <div>Yth. {{ regData.doctor_in_charge_name }}</div>
                                <div class="mt-12"><b>Pemeriksaan : {{ item.category_examination_name }}</b></div>
                                <template v-if="item?.data?.files[0].examination_date">
                                    <table class="w-100 custom-border">
                                        <tr>
                                            <td class="align-top">Interpretasi : </td>
                                            <td class="align-top text-center px-8"></td>
                                        </tr>
                                        <tr>
                                            <td><span v-html="parsedBody(item?.data?.files ? item?.data?.files[0].expertise : '-')"></span></td>
                                        </tr>
                                    </table>
                                    <img style="width:900px;" :src="`http://192.168.100.3:9001/${item.data?.files[0].url}`" v-if="item.data?.files[0].url && item?.category_examination_name == 'EKG'" />
                                    <div class="mt-32 d-flex justify-content-end text-center">
                                        <div>
                                            <div>Bekasi, {{ localDate(item?.data?.files[0].examination_date) }}</div>
                                            <div :class="item?.ttd_doctor ? '' : 'mb-72'">Salam Sejawat</div>
                                            <b-img :src="urlbe+item?.ttd_doctor" style="width: 220px;" />
                                            <!-- <div>{{ item?.examination_date }}</div> -->
                                            <div>{{ item?.doctor_name }}</div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <!-- <iframe style="width: 100vw; height: 100vh" :src="`http://192.168.100.3:5000/${item_data.url}`" v-if="item_data.url" /> -->
                            <img style="width:900px;margin-top: 100px" :src="`http://192.168.100.3:9001/${item.data?.files[0].url}`" v-if="item.data?.files[0].url && item?.category_examination_name != 'EKG'" />
                        </template>
                        <div style="margin-top: 1200px;">
                            <div class="mt-20">
                                <ModernLabel label="Diagnosa & Saran" />
                                <table class="w-100 mt-16">
                                    <tbody>
                                        <tr>
                                            <td style="width: 200px;"><b>Diagnosa</b></td>
                                        </tr>
                                        <tr>
                                            <td class="pt-2">
                                                <div v-if="soapData.non_icd10">
                                                    <div v-for="(item, index) in soapData.non_icd10" >- {{ item }}</div>
                                                </div>
                                                <div v-else>-</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Saran</b></td>
                                        </tr>
                                        <tr>
                                            <td class="pt-2">
                                                <div v-html="parsedBody(soapData.advice ? soapData.advice : '-')"></div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td><b>Konsultasi</b></td>
                                        </tr>
                                        <tr>
                                            <td class="pt-2">
                                                <div v-html="parsedBody(soapData.consultation ? soapData.consultation : '-')"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Kesimpulan</b></td>
                                        </tr>
                                        <tr>
                                            <td class="pt-2">
                                                <div v-html="parsedBody(soapData.fit_to_work ? soapData.fit_to_work : '-')"></div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
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
import * as pdfjsLib from 'pdfjs-dist/build/pdf';
import workerSrc from 'pdfjs-dist/build/pdf.worker.min.js';
import { mapState } from 'vuex';

import dateMixin from '@/mixins/dateMixin';
import optionMixin from '@/mixins/optionMixin';

export default {
    mixins: [optionMixin, dateMixin],
    data() {
        return {
            urlbe: process.env.VUE_APP_IMAGE,
            pdfAsDataUri:'',
            input_pdf: null,
            thumbnail:[],
            regData: {},
            soapData: {},
            letterData: {},
            orderData:{},
            nurseData:{},
            days: 1,
            soap_id: '',
            result:{},
            orderDataRad:{},
            reqItemData:{},
            examination_list:[],
            resultFields: [
                // { key: 'laboratory_package_name', label: 'Pemeriksaan' },
                { key: 'result', label: '' },
                // { key: 'flag', label: 'Flag' },
                // { key: 'n_rujukan', label: 'Nilai Rujukan' },
                // { key: 'satuan', label: 'Satuan' },
                // { key: 'metoda', label: 'Metoda' },
            ],
            resultFieldsItem: [
                { key: 'Parameter', label: 'Pemeriksaan', thStyle: { width: '185px' }, },
                { key: 'hasil', label: 'Hasil' , thStyle: { width: '185px' },},
                { key: 'flag', label: 'Flag' , thStyle: { width: '185px' },},
                { key: 'n_rujukan', label: 'Nilai Rujukan' , thStyle: { width: '185px' },},
                { key: 'satuan', label: 'Satuan' , thStyle: { width: '185px' },},
                { key: 'metoda', label: 'Metoda' , thStyle: { width: '185px' },},
            ],
            active: {
                ophtal: false,
            },
            generalist: {
                head: '',
                head_hair_color: '',
                head_hair_condition: '',
                eye_conjunctive: '',
                eye_sclera: '',
                eye_pupil: '',
                eye_light_reflex: '',
                eye_blind_color: '',
                mouth_mucosa: '',
                mouth_discoloration: '',
                mouth_tongue: '',
                mouth_caries: '',
                neck_kgb: '',
                neck_trachea_position: '',
                neck_massa: '',
                thorax_insp_chest: '',
                thorax_insp_retraction: '',
                thorax_palp_chest: '',
                thorax_palp_fremitus: '',
                thorax_palp_crepitation: '',
                thorax_ausk_pul_vasiculer: '',
                thorax_ausk_pul_rhonki: '',
                thorax_ausk_pul_wheezing: '',
                thorax_ausk_cor_heart_sound: '',
                thorax_ausk_cor_murmur: '',
                thorax_ausk_cor_gallop: '',
                abdomen_supel: '',
                abdomen_bowel_sounds: '',
                abdomen_tenderness: '',
                abdomen_hepar_lien: '',
                abdomen_other_physical_check: '',
                extremity_acral: '',
                extremity_crt: '',
                extremity_edema: '',
                genitalia: '',
                localist_status: '',
                neurologist_status: '',
                ophthal_status: '',

                ophthal_od: {
                    visus: '',
                    tio_digital: '',
                    tio_tonometri_sciots: '',
                    eye_position: '',
                    eye_movement: '',
                    super_silia: '',
                    palpebra_edema: '',
                    palpebra_ptosis: '',
                    palpebra_lagophtalmus: '',
                    palpebra_tumor: '',
                    apparatus_lakrimalis: '',
                    silia: '',
                    conjunctive_hiperemis: '',
                    conjunctive_bleed: '',
                    cornea: '',
                    camera_oculi: '',
                    iris_color: '',
                    iris_cripte: '',
                    pupil_form: '',
                    pupil_diameter: '',
                    pupil_direct_light: '',
                    pupil_indirect_light: '',
                    lens: '',
                    shadow_test: '',
                    glandula: '',
                    field_view: '',
                    fundus: '',
                },
                ophthal_os: {
                    visus: '',
                    tio_digital: '',
                    tio_tonometri_sciots: '',
                    eye_position: '',
                    eye_movement: '',
                    super_silia: '',
                    palpebra_edema: '',
                    palpebra_ptosis: '',
                    palpebra_lagophtalmus: '',
                    palpebra_tumor: '',
                    apparatus_lakrimalis: '',
                    silia: '',
                    conjunctive_hiperemis: '',
                    conjunctive_bleed: '',
                    cornea: '',
                    camera_oculi: '',
                    iris_color: '',
                    iris_cripte: '',
                    pupil_form: '',
                    pupil_diameter: '',
                    pupil_direct_light: '',
                    pupil_indirect_light: '',
                    lens: '',
                    shadow_test: '',
                    glandula: '',
                    field_view: '',
                    fundus: '',
                },

            },
        }
    },
    computed: {
        ...mapState({
            items: state => state.soap.items,
            // generalis option
            OptionsHeadCircumference: state => state.soap.generalis.OptionsHeadCircumference,
            OptionsHeadHairColor: state => state.soap.generalis.OptionsHeadHairColor,
            OptionsHeadHairCondition: state => state.soap.generalis.OptionsHeadHairCondition,
            OptionsEyeConjunctiva: state => state.soap.generalis.OptionsEyeConjunctiva,
            OptionsEyeSclera: state => state.soap.generalis.OptionsEyeSclera,
            OptionsEyePupil: state => state.soap.generalis.OptionsEyePupil,
            OptionsEyeReflect: state => state.soap.generalis.OptionsEyeReflect,
            OptionsMouthMukosa: state => state.soap.generalis.OptionsMouthMukosa,
            OptionsMouthHiperemis: state => state.soap.generalis.OptionsMouthHiperemis,
            OptionsMouthTongue: state => state.soap.generalis.OptionsMouthTongue,
            OptionsMouthKaries: state => state.soap.generalis.OptionsMouthKaries,
            OptionsNeckKGB: state => state.soap.generalis.OptionsNeckKGB,
            OptionsNeckTrakea: state => state.soap.generalis.OptionsNeckTrakea,
            OptionsNeckMassa: state => state.soap.generalis.OptionsNeckMassa,
            OptionsInspectionChest: state => state.soap.generalis.OptionsInspectionChest,
            OptionsEyeBlindColor: state => state.soap.generalis.OptionsEyeBlindColor,
            OptionsInspectionRetraction: state => state.soap.generalis.OptionsInspectionRetraction,
            OptionsPalpationChest: state => state.soap.generalis.OptionsPalpationChest,
            OptionsPalpationFremitus: state => state.soap.generalis.OptionsPalpationFremitus,
            OptionsPalpationKrepitasi: state => state.soap.generalis.OptionsPalpationKrepitasi,
            OptionsAuskultationPulmoVasikuler: state => state.soap.generalis.OptionsAuskultationPulmoVasikuler,
            OptionsAuskultationPulmoRhonki: state => state.soap.generalis.OptionsAuskultationPulmoRhonki,
            OptionsAuskultationPulmoWheezing: state => state.soap.generalis.OptionsAuskultationPulmoWheezing,
            OptionsAuskultationCORBJ: state => state.soap.generalis.OptionsAuskultationCORBJ,
            OptionsAuskultationCORMurmur: state => state.soap.generalis.OptionsAuskultationCORMurmur,
            OptionsAuskultationCORGallop: state => state.soap.generalis.OptionsAuskultationCORGallop,
            OptionsAbdomenSupel: state => state.soap.generalis.OptionsAbdomenSupel,
            OptionsAbdomenBising: state => state.soap.generalis.OptionsAbdomenBising,
            OptionsAbdomenPress: state => state.soap.generalis.OptionsAbdomenPress,
            OptionsAbdomenHepar: state => state.soap.generalis.OptionsAbdomenHepar,
            OptionsEkstremitasAkral: state => state.soap.generalis.OptionsEkstremitasAkral,
            OptionsEkstremitasCRT: state => state.soap.generalis.OptionsEkstremitasCRT,
            OptionsEkstremitasEdema: state => state.soap.generalis.OptionsEkstremitasEdema,
            OptionsOftalVisus: state => state.soap.generalis.OptionsOftalVisus,
            OptionsOftalTIO: state => state.soap.generalis.OptionsOftalTIO,
            OptionsOftalEyePosition: state => state.soap.generalis.OptionsOftalEyePosition,
            OptionsOftalMovement: state => state.soap.generalis.OptionsOftalMovement,
            OptionsOftalSuperSilia: state => state.soap.generalis.OptionsOftalSuperSilia,
            OptionsOftalPalpebraEdema: state => state.soap.generalis.OptionsOftalPalpebraEdema,
            OptionsOftalPalpebraPtosisMCU: state => state.soap.generalis.OptionsOftalPalpebraPtosisMCU,
            OptionsOftalPalpebraLagophtalmusMCU: state => state.soap.generalis.OptionsOftalPalpebraLagophtalmusMCU,
            OptionsOftalPalpebraTumor: state => state.soap.generalis.OptionsOftalPalpebraTumor,
            OptionsOftalApparetus: state => state.soap.generalis.OptionsOftalApparetus,
            OptionsOftalSilia: state => state.soap.generalis.OptionsOftalSilia,
            OptionsOftalConjHiperemis: state => state.soap.generalis.OptionsOftalConjHiperemis,
            OptionsOftalConjBleed: state => state.soap.generalis.OptionsOftalConjBleed,
            OptionsOftalKornea: state => state.soap.generalis.OptionsOftalKornea,
            OptionsOftalAnterior: state => state.soap.generalis.OptionsOftalAnterior,
            OptionsOftalIrisColor: state => state.soap.generalis.OptionsOftalIrisColor,
            OptionsOftalIrisKripte: state => state.soap.generalis.OptionsOftalIrisKripte,
            OptionsOftalPupilForm: state => state.soap.generalis.OptionsOftalPupilForm,
            OptionsOftalPupilDiameter: state => state.soap.generalis.OptionsOftalPupilDiameter,
            OptionsOftalPupilDirectLight: state => state.soap.generalis.OptionsOftalPupilDirectLight,
            OptionsOftalPupilIndirectLight: state => state.soap.generalis.OptionsOftalPupilIndirectLight,
            OptionsOftalLens: state => state.soap.generalis.OptionsOftalLens,
            OptionsOftalGlandula: state => state.soap.generalis.OptionsOftalGlandula,
            OptionsOftalLapang: state => state.soap.generalis.OptionsOftalLapang,
            OptionsOftalShadow: state => state.soap.generalis.OptionsOftalShadow,
        })
    },
    filters: {
        convert_date(value) {
            return moment(new Date(value)).locale('id').format('LL')
        }
    },
    methods: {
        convertDataURIToBinary (dataURI) {
            var BASE64_MARKER = ';base64,';
            var base64Index = dataURI.indexOf(BASE64_MARKER) + BASE64_MARKER.length;
            var base64 = dataURI.substring(base64Index);
            var raw = window.atob(base64);
            var rawLength = raw.length;
            var array = new Uint8Array(new ArrayBuffer(rawLength));

            for(var i = 0; i < rawLength; i++) {
                array[i] = raw.charCodeAt(i);
            }
            return array;
        },
        async generateThumbnail(data) {
            try {
                const response = await this.$store.dispatch('soap/pdf_to_base64_lab', data)
                this.pdfAsDataUri = response.base64
                // var pdfAsArray = this.convertDataURIToBinary(this.pdfAsDataUri);

                // const loadingTask =  pdfjsLib.getDocument(pdfAsArray)

                // const pdf = await loadingTask.promise
                // const page = await pdf.getPage(1)

                // const scale = 1
                // const viewport = page.getViewport({ scale })

                // const canvas = document.getElementById('pdf-canvas')
                // const context = canvas.getContext('2d')

                // canvas.width = viewport.width
                // canvas.height = viewport.height

                // await page.render({ canvasContext: context, viewport }).promise

                // this.thumbnail = canvas.toDataURL()
                // console.log('testste'+this.thumbnail)

                var pdfAsArray = this.convertDataURIToBinary(this.pdfAsDataUri);
                const loadingTask = pdfjsLib.getDocument(pdfAsArray)
                const pdf = await loadingTask.promise

                let dataurl = []
                // Loop through all pages
                for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                    const page = await pdf.getPage(pageNum)
                    const scale = 1
                    const viewport = page.getViewport({ scale })

                    // Create a new canvas for each page
                    const canvas = document.createElement('canvas')
                    canvas.id = `pdf-canvas-${pageNum}`
                    const context = canvas.getContext('2d')

                    canvas.width = viewport.width
                    canvas.height = viewport.height

                    await page.render({ canvasContext: context, viewport }).promise

                    dataurl.push(canvas.toDataURL())
                    // Append canvas to container
                    document.getElementById('pdf-canvas').appendChild(canvas)
                }
                this.thumbnail = dataurl
            } catch (error) {
                console.error('Error rendering PDF:', error)
            }
        },
        parsedBody(text){
            return String(text || '').replace(/\n/g, "<br>");
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
        getData(id) {
            this.$store.dispatch('registration/getById', id).then(data => {
                this.regData = data[0]
                this.$store.dispatch('nurse/getDetail', { registration_destination_id: id }).then((data ) => {
                    this.nurseData = data[0]
                })
                this.$store.dispatch('laboratory/getByRegistrationId', data[0].registration_id).then(datalab => {
                    this.orderData = datalab[0]
                    this.$store.dispatch('laboratory/getResultById', datalab[0].id).then(result => {
                        this.result = result[0]?.result_new.reduce((acc, item) => {
                            if (!acc[item.category]) {
                            acc[item.category] = [];
                            }
                            acc[item.category].push(item);
                            return acc;
                        }, {})
                        for (let index = 0; index < result[0]?.result_new.length; index++) {
                            if (result[0]?.result_new[index].url) {
                                this.generateThumbnail(result[0]?.result_new[index].url)
                            }
                        }
                    })
                })
                this.$store.dispatch('radiology/getByRegistrationId', data[0].registration_id).then(resultrad => {
                    this.orderDataRad = resultrad[0]
                    this.reqItemData = this.orderDataRad?.radiology_request_item
                    // this.reqItemData = req_item.filter((item) => item.status !== 1)
                })
            })
        },
        getSoap(id) {
            this.$store.dispatch('soap/getBySoapId', id).then(data => {
                this.getData(data[0].registration_destination_id)
                this.result = data[0]
                this.soapData = data[0]
                this.$store.dispatch('soap/getExamination', {soap_id: data[0].id}).then(response => {
                    this.examination_list = response
                    // console.log('this.examination_list: ', this.examination_list);
                })
                if (this.result) {
                    this.getGeneralist(this.result)

                    const obj_od = Object.assign({}, this.result.ophthal_od);
                    const obj_os = Object.assign({}, this.result.ophthal_os);

                    const isODEmpty = Object.values(obj_od).every(value => value === null);
                    const isOSEmpty = Object.values(obj_os).every(value => value === null);

                    if (isODEmpty && isOSEmpty) {
                        this.active.ophtal = false
                        // console.log('empty');
                    } else {
                        this.active.ophtal = true
                        // console.log('not empty');
                    }

                }
            })
        },


        getGeneralist(obj) {
            const objHead = this.OptionsHeadCircumference.find(el => el.value === obj.head)
            const objHairColor = this.OptionsHeadHairColor.find(el => el.value === obj.head_hair_color)
            const objHairCondition = this.OptionsHeadHairCondition.find(el => el.value === obj.head_hair_condition)
            const objEyeConjunctive = this.OptionsEyeConjunctiva.find(el => el.value === obj.eye_conjunctive)
            const objEyeSclera = this.OptionsEyeSclera.find(el => el.value === obj.eye_sclera)
            const objEyePupil = this.OptionsEyePupil.find(el => el.value === obj.eye_pupil)
            const objEyeReflect = this.OptionsEyeReflect.find(el => el.value === obj.eye_light_reflex)
            const objEyeBlindColor = this.OptionsEyeBlindColor.find(el => el.value === obj.eye_blind_color)
            const objMouthMucosa = this.OptionsMouthMukosa.find(el => el.value === obj.mouth_mucosa)
            const objMouthDiscoloration = this.OptionsMouthHiperemis.find(el => el.value === obj.mouth_discoloration)
            const objMouthTongue = this.OptionsMouthTongue.find(el => el.value === obj.mouth_tongue)
            const objMouthCaries = this.OptionsMouthKaries.find(el => el.value === obj.mouth_caries)
            const objNeckKGB = this.OptionsNeckKGB.find(el => el.value === obj.neck_kgb)
            const objNeckTracheaPosition = this.OptionsNeckTrakea.find(el => el.value === obj.neck_trachea_position)
            const objNeckMassa = this.OptionsNeckMassa.find(el => el.value === obj.neck_massa)
            const objInspectionChest = this.OptionsInspectionChest.find(el => el.value === obj.thorax_insp_chest)
            const objInspectionRetractiont = this.OptionsInspectionRetraction.find(el => el.value === obj.thorax_insp_retraction)
            const objPalpationChest = this.OptionsPalpationChest.find(el => el.value === obj.thorax_palp_chest)
            const objPalpationFremitus = this.OptionsPalpationFremitus.find(el => el.value === obj.thorax_palp_fremitus)
            const objPalpationKrepitasi = this.OptionsPalpationKrepitasi.find(el => el.value === obj.thorax_palp_crepitation)
            const objAuskultationPulmoVasikuler = this.OptionsAuskultationPulmoVasikuler.find(el => el.value === obj.thorax_ausk_pul_vasiculer)
            const objAuskultationPulmoRhonki = this.OptionsAuskultationPulmoRhonki.find(el => el.value === obj.thorax_ausk_pul_rhonki)
            const objAuskultationPulmoWheezing = this.OptionsAuskultationPulmoWheezing.find(el => el.value === obj.thorax_ausk_pul_wheezing)
            const objAuskultationCORBJ = this.OptionsAuskultationCORBJ.find(el => el.value === obj.thorax_ausk_cor_heart_sound)
            const objAuskultationCORMurmur = this.OptionsAuskultationCORMurmur.find(el => el.value === obj.thorax_ausk_cor_murmur)
            const objAuskultationCORGallop = this.OptionsAuskultationCORGallop.find(el => el.value === obj.thorax_ausk_cor_gallop)
            const objAbdomenSupel = this.OptionsAbdomenSupel.find(el => el.value === obj.abdomen_supel)
            const objAbdomenBising = this.OptionsAbdomenBising.find(el => el.value === obj.abdomen_bowel_sounds)
            const objAbdomenPress = this.OptionsAbdomenPress.find(el => el.value === obj.abdomen_tenderness)
            const objAbdomenHepar = this.OptionsAbdomenHepar.find(el => el.value === obj.abdomen_hepar_lien)
            const objEkstremitasAkral = this.OptionsEkstremitasAkral.find(el => el.value === obj.extremity_acral)
            const objEkstremitasCRT = this.OptionsEkstremitasCRT.find(el => el.value === obj.extremity_crt)
            const objEkstremitasEdema = this.OptionsEkstremitasEdema.find(el => el.value === obj.extremity_edema)

            let objOdOftalVisus = ''
            let objOdOftalTIO = ''
            let objOdOftalEyePosition = ''
            let objOdOftalMovement = ''
            let objOdOftalSuperSilia = ''
            let objOdOftalPalpebraEdema = ''
            let objOdOftalPalpebraPtosis = ''
            let objOdOftalPalpebraLagophtalmus = ''
            let objOdOftalPalpebraTumor = ''
            let objOdOftalApparetus = ''
            let objOdOftalSilia = ''
            let objOdOftalConjHiperemis = ''
            let objOdOftalConjBleed = ''
            let objOdOftalKornea = ''
            let objOdOftalAnterior = ''
            let objOdOftalIrisColor = ''
            let objOdOftalIrisKripte = ''
            let objOdOftalPupilForm = ''
            let objOdOftalPupilDiameter = ''
            let objOdOftalPupilDirectLight = ''
            let objOdOftalPupilIndirectLight = ''
            let objOdOftalLens = ''
            let objOdOftalShadow = ''
            let objOdOftalGlandula = ''
            let objOdOftalLapang = ''

            if (obj.ophthal_od && obj.ophthal_od.visus) {
                objOdOftalVisus = this.OptionsOftalVisus.find(el => el.value == obj.ophthal_od.visus)
            }
            if (obj.ophthal_od && obj.ophthal_od.tio_digital) {
                objOdOftalTIO = this.OptionsOftalTIO.find(el => el.value == obj.ophthal_od.tio_digital)
            }
            if (obj.ophthal_od && obj.ophthal_od.eye_position) {
                objOdOftalEyePosition = this.OptionsOftalEyePosition.find(el => el.value == obj.ophthal_od.eye_position)
            }
            if (obj.ophthal_od && obj.ophthal_od.eye_movement) {
                objOdOftalMovement = this.OptionsOftalMovement.find(el => el.value == obj.ophthal_od.eye_movement)
            }
            if (obj.ophthal_od && obj.ophthal_od.super_silia) {
                objOdOftalSuperSilia = this.OptionsOftalSuperSilia.find(el => el.value == obj.ophthal_od.super_silia)
            }
            if (obj.ophthal_od && obj.ophthal_od.palpebra_edema) {
                objOdOftalPalpebraEdema = this.OptionsOftalPalpebraEdema.find(el => el.value == obj.ophthal_od.palpebra_edema)
            }
            if (obj.ophthal_od && obj.ophthal_od.palpebra_ptosis) {
                objOdOftalPalpebraPtosis = this.OptionsOftalPalpebraPtosisMCU.find(el => el.value == obj.ophthal_od.palpebra_ptosis)
            }
            if (obj.ophthal_od && obj.ophthal_od.palpebra_lagophtalmus) {
                objOdOftalPalpebraLagophtalmus = this.OptionsOftalPalpebraLagophtalmusMCU.find(el => el.value == obj.ophthal_od.palpebra_lagophtalmus)
            }
            if (obj.ophthal_od && obj.ophthal_od.palpebra_tumor) {
                objOdOftalPalpebraTumor = this.OptionsOftalPalpebraTumor.find(el => el.value == obj.ophthal_od.palpebra_tumor)
            }
            if (obj.ophthal_od && obj.ophthal_od.apparatus_lakrimalis) {
                objOdOftalApparetus = this.OptionsOftalApparetus.find(el => el.value == obj.ophthal_od.apparatus_lakrimalis)
            }
            if (obj.ophthal_od && obj.ophthal_od.silia) {
                objOdOftalSilia = this.OptionsOftalSilia.find(el => el.value == obj.ophthal_od.silia)
            }
            if (obj.ophthal_od && obj.ophthal_od.conjunctive_hiperemis) {
                objOdOftalConjHiperemis = this.OptionsOftalConjHiperemis.find(el => el.value == obj.ophthal_od.conjunctive_hiperemis)
            }
            if (obj.ophthal_od && obj.ophthal_od.conjunctive_bleed) {
                objOdOftalConjBleed = this.OptionsOftalConjBleed.find(el => el.value == obj.ophthal_od.conjunctive_bleed)
            }
            if (obj.ophthal_od && obj.ophthal_od.cornea) {
                objOdOftalKornea = this.OptionsOftalKornea.find(el => el.value == obj.ophthal_od.cornea)
            }
            if (obj.ophthal_od && obj.ophthal_od.camera_oculi) {
                objOdOftalAnterior = this.OptionsOftalAnterior.find(el => el.value == obj.ophthal_od.camera_oculi)
            }
            if (obj.ophthal_od && obj.ophthal_od.iris_color) {
                objOdOftalIrisColor = this.OptionsOftalIrisColor.find(el => el.value == obj.ophthal_od.iris_color)
            }
            if (obj.ophthal_od && obj.ophthal_od.iris_cripte) {
                objOdOftalIrisKripte = this.OptionsOftalIrisKripte.find(el => el.value == obj.ophthal_od.iris_cripte)
            }
            if (obj.ophthal_od && obj.ophthal_od.pupil_form) {
                objOdOftalPupilForm = this.OptionsOftalPupilForm.find(el => el.value == obj.ophthal_od.pupil_form)
            }
            if (obj.ophthal_od && obj.ophthal_od.pupil_diameter) {
                objOdOftalPupilDiameter = this.OptionsOftalPupilDiameter.find(el => el.value == obj.ophthal_od.pupil_diameter)
            }
            if (obj.ophthal_od && obj.ophthal_od.pupil_direct_light) {
                objOdOftalPupilDirectLight = this.OptionsOftalPupilDirectLight.find(el => el.value == obj.ophthal_od.pupil_direct_light)
            }
            if (obj.ophthal_od && obj.ophthal_od.pupil_indirect_light) {
                objOdOftalPupilIndirectLight = this.OptionsOftalPupilIndirectLight.find(el => el.value == obj.ophthal_od.pupil_indirect_light)
            }
            if (obj.ophthal_od && obj.ophthal_od.lens) {
                objOdOftalLens = this.OptionsOftalLens.find(el => el.value == obj.ophthal_od.lens)
            }
            if (obj.ophthal_od && obj.ophthal_od.shadow_test) {
                objOdOftalShadow = this.OptionsOftalShadow.find(el => el.value == obj.ophthal_od.shadow_test)
            }
            if (obj.ophthal_od && obj.ophthal_od.glandula) {
                objOdOftalGlandula = this.OptionsOftalGlandula.find(el => el.value == obj.ophthal_od.glandula)
            }
            if (obj.ophthal_od && obj.ophthal_od.field_view) {
                objOdOftalLapang = this.OptionsOftalLapang.find(el => el.value == obj.ophthal_od.field_view)
            }


            let objOsOftalVisus = ''
            let objOsOftalTIO = ''
            let objOsOftalEyePosition = ''
            let objOsOftalMovement = ''
            let objOsOftalSuperSilia = ''
            let objOsOftalPalpebraEdema = ''
            let objOsOftalPalpebraPtosis = ''
            let objOsOftalPalpebraLagophtalmus = ''
            let objOsOftalPalpebraTumor = ''
            let objOsOftalApparetus = ''
            let objOsOftalSilia = ''
            let objOsOftalConjHiperemis = ''
            let objOsOftalConjBleed = ''
            let objOsOftalKornea = ''
            let objOsOftalAnterior = ''
            let objOsOftalIrisColor = ''
            let objOsOftalIrisKripte = ''
            let objOsOftalPupilForm = ''
            let objOsOftalPupilDiameter = ''
            let objOsOftalPupilDirectLight = ''
            let objOsOftalPupilIndirectLight = ''
            let objOsOftalLens = ''
            let objOsOftalShadow = ''
            let objOsOftalGlandula = ''
            let objOsOftalLapang = ''

            if (obj.ophthal_os && obj.ophthal_os.visus) {
                objOsOftalVisus = this.OptionsOftalVisus.find(el => el.value == obj.ophthal_os.visus)
            }
            if (obj.ophthal_os && obj.ophthal_os.tio_digital) {
                objOsOftalTIO = this.OptionsOftalTIO.find(el => el.value == obj.ophthal_os.tio_digital)
            }
            if (obj.ophthal_os && obj.ophthal_os.eye_position) {
                objOsOftalEyePosition = this.OptionsOftalEyePosition.find(el => el.value == obj.ophthal_os.eye_position)
            }
            if (obj.ophthal_os && obj.ophthal_os.eye_movement) {
                objOsOftalMovement = this.OptionsOftalMovement.find(el => el.value == obj.ophthal_os.eye_movement)
            }
            if (obj.ophthal_os && obj.ophthal_os.super_silia) {
                objOsOftalSuperSilia = this.OptionsOftalSuperSilia.find(el => el.value == obj.ophthal_os.super_silia)
            }
            if (obj.ophthal_os && obj.ophthal_os.palpebra_edema) {
                objOsOftalPalpebraEdema = this.OptionsOftalPalpebraEdema.find(el => el.value == obj.ophthal_os.palpebra_edema)
            }
            if (obj.ophthal_os && obj.ophthal_os.palpebra_ptosis) {
                objOsOftalPalpebraPtosis = this.OptionsOftalPalpebraPtosisMCU.find(el => el.value == obj.ophthal_os.palpebra_ptosis)
            }
            if (obj.ophthal_os && obj.ophthal_os.palpebra_lagophtalmus) {
                objOsOftalPalpebraLagophtalmus = this.OptionsOftalPalpebraLagophtalmusMCU.find(el => el.value == obj.ophthal_os.palpebra_lagophtalmus)
            }
            if (obj.ophthal_os && obj.ophthal_os.palpebra_tumor) {
                objOsOftalPalpebraTumor = this.OptionsOftalPalpebraTumor.find(el => el.value == obj.ophthal_os.palpebra_tumor)
            }
            if (obj.ophthal_os && obj.ophthal_os.apparatus_lakrimalis) {
                objOsOftalApparetus = this.OptionsOftalApparetus.find(el => el.value == obj.ophthal_os.apparatus_lakrimalis)
            }
            if (obj.ophthal_os && obj.ophthal_os.silia) {
                objOsOftalSilia = this.OptionsOftalSilia.find(el => el.value == obj.ophthal_os.silia)
            }
            if (obj.ophthal_os && obj.ophthal_os.conjunctive_hiperemis) {
                objOsOftalConjHiperemis = this.OptionsOftalConjHiperemis.find(el => el.value == obj.ophthal_os.conjunctive_hiperemis)
            }
            if (obj.ophthal_os && obj.ophthal_os.conjunctive_bleed) {
                objOsOftalConjBleed = this.OptionsOftalConjBleed.find(el => el.value == obj.ophthal_os.conjunctive_bleed)
            }
            if (obj.ophthal_os && obj.ophthal_os.cornea) {
                objOsOftalKornea = this.OptionsOftalKornea.find(el => el.value == obj.ophthal_os.cornea)
            }
            if (obj.ophthal_os && obj.ophthal_os.camera_oculi) {
                objOsOftalAnterior = this.OptionsOftalAnterior.find(el => el.value == obj.ophthal_os.camera_oculi)
            }
            if (obj.ophthal_os && obj.ophthal_os.iris_color) {
                objOsOftalIrisColor = this.OptionsOftalIrisColor.find(el => el.value == obj.ophthal_os.iris_color)
            }
            if (obj.ophthal_os && obj.ophthal_os.iris_cripte) {
                objOsOftalIrisKripte = this.OptionsOftalIrisKripte.find(el => el.value == obj.ophthal_os.iris_cripte)
            }
            if (obj.ophthal_os && obj.ophthal_os.pupil_form) {
                objOsOftalPupilForm = this.OptionsOftalPupilForm.find(el => el.value == obj.ophthal_os.pupil_form)
            }
            if (obj.ophthal_os && obj.ophthal_os.pupil_diameter) {
                objOsOftalPupilDiameter = this.OptionsOftalPupilDiameter.find(el => el.value == obj.ophthal_os.pupil_diameter)
            }
            if (obj.ophthal_os && obj.ophthal_os.pupil_direct_light) {
                objOsOftalPupilDirectLight = this.OptionsOftalPupilDirectLight.find(el => el.value == obj.ophthal_os.pupil_direct_light)
            }
            if (obj.ophthal_os && obj.ophthal_os.pupil_indirect_light) {
                objOsOftalPupilIndirectLight = this.OptionsOftalPupilIndirectLight.find(el => el.value == obj.ophthal_os.pupil_indirect_light)
            }
            if (obj.ophthal_os && obj.ophthal_os.lens) {
                objOsOftalLens = this.OptionsOftalLens.find(el => el.value == obj.ophthal_os.lens)
            }
            if (obj.ophthal_os && obj.ophthal_os.shadow_test) {
                objOsOftalShadow = this.OptionsOftalShadow.find(el => el.value == obj.ophthal_os.shadow_test)
            }
            if (obj.ophthal_os && obj.ophthal_os.glandula) {
                objOsOftalGlandula = this.OptionsOftalGlandula.find(el => el.value == obj.ophthal_os.glandula)
            }
            if (obj.ophthal_os && obj.ophthal_os.field_view) {
                objOsOftalLapang = this.OptionsOftalLapang.find(el => el.value == obj.ophthal_os.field_view)
            }

            this.generalist.head = objHead ? objHead.title : '-'
            this.generalist.head_hair_color = objHairColor ? objHairColor.title : '-'
            this.generalist.head_hair_condition = objHairCondition ? objHairCondition.title : '-'
            this.generalist.eye_conjunctive = objEyeConjunctive ? objEyeConjunctive.title : '-'
            this.generalist.eye_sclera = objEyeSclera ? objEyeSclera.title : '-'
            this.generalist.eye_pupil = objEyePupil ? objEyePupil.title : '-'
            this.generalist.eye_light_reflex = objEyeReflect ? objEyeReflect.title : '-'
            this.generalist.eye_blind_color = objEyeBlindColor ? objEyeBlindColor.title : '-'
            this.generalist.mouth_mucosa = objMouthMucosa ? objMouthMucosa.title : '-'
            this.generalist.mouth_discoloration = objMouthDiscoloration ? objMouthDiscoloration.title : '-'
            this.generalist.mouth_tongue = objMouthTongue ? objMouthTongue.title : '-'
            this.generalist.mouth_caries = objMouthCaries ? objMouthCaries.title : '-'
            this.generalist.neck_kgb = objNeckKGB ? objNeckKGB.title : '-'
            this.generalist.neck_trachea_position = objNeckTracheaPosition ? objNeckTracheaPosition.title : '-'
            this.generalist.neck_massa = objNeckMassa ? objNeckMassa.title : '-'
            this.generalist.thorax_insp_chest = objInspectionChest ? objInspectionChest.title : '-'
            this.generalist.thorax_insp_retraction = objInspectionRetractiont ? objInspectionRetractiont.title : '-'
            this.generalist.thorax_palp_chest = objPalpationChest ? objPalpationChest.title : '-'
            this.generalist.thorax_palp_fremitus = objPalpationFremitus ? objPalpationFremitus.title : '-'
            this.generalist.thorax_palp_crepitation = objPalpationKrepitasi ? objPalpationKrepitasi.title : '-'
            this.generalist.thorax_ausk_pul_vasiculer = objAuskultationPulmoVasikuler ? objAuskultationPulmoVasikuler.title : '-'
            this.generalist.thorax_ausk_pul_rhonki = objAuskultationPulmoRhonki ? objAuskultationPulmoRhonki.title : '-'
            this.generalist.thorax_ausk_pul_wheezing = objAuskultationPulmoWheezing ? objAuskultationPulmoWheezing.title : '-'
            this.generalist.thorax_ausk_cor_heart_sound = objAuskultationCORBJ ? objAuskultationCORBJ.title : '-'
            this.generalist.thorax_ausk_cor_murmur = objAuskultationCORMurmur ? objAuskultationCORMurmur.title : '-'
            this.generalist.thorax_ausk_cor_gallop = objAuskultationCORGallop ? objAuskultationCORGallop.title : '-'
            this.generalist.abdomen_supel = objAbdomenSupel ? objAbdomenSupel.title : '-'
            this.generalist.abdomen_bowel_sounds = objAbdomenBising ? objAbdomenBising.title : '-'
            this.generalist.abdomen_tenderness = objAbdomenPress ? objAbdomenPress.title : '-'
            this.generalist.abdomen_hepar_lien = objAbdomenHepar ? objAbdomenHepar.title : '-'
            this.generalist.extremity_acral = objEkstremitasAkral ? objEkstremitasAkral.title : '-'
            this.generalist.extremity_crt = objEkstremitasCRT ? objEkstremitasCRT.title : '-'
            this.generalist.extremity_edema = objEkstremitasEdema ? objEkstremitasEdema.title : '-'
            this.generalist.abdomen_other_physical_check = obj.abdomen_other_physical_check
            this.generalist.genitalia = obj.genitalia
            this.generalist.localist_status = obj.localist_status
            this.generalist.neurologist_status = obj.neurologist_status
            this.generalist.ophthal_status = obj.ophthal_status

            this.generalist.ophthal_od.visus = objOdOftalVisus ? objOdOftalVisus.title : '-'
            this.generalist.ophthal_od.tio_digital = objOdOftalTIO ? objOdOftalTIO.title : '-'
            this.generalist.ophthal_od.eye_position = objOdOftalEyePosition ? objOdOftalEyePosition.title : '-'
            this.generalist.ophthal_od.eye_movement = objOdOftalMovement ? objOdOftalMovement.title : '-'
            this.generalist.ophthal_od.super_silia = objOdOftalSuperSilia ? objOdOftalSuperSilia.title : '-'
            this.generalist.ophthal_od.palpebra_edema = objOdOftalPalpebraEdema ? objOdOftalPalpebraEdema.title : '-'
            this.generalist.ophthal_od.palpebra_ptosis = objOdOftalPalpebraPtosis ? objOdOftalPalpebraPtosis.title : '-'
            this.generalist.ophthal_od.palpebra_lagophtalmus = objOdOftalPalpebraLagophtalmus ? objOdOftalPalpebraLagophtalmus.title : '-'
            this.generalist.ophthal_od.palpebra_tumor = objOdOftalPalpebraTumor ? objOdOftalPalpebraTumor.title : '-'
            this.generalist.ophthal_od.apparatus_lakrimalis = objOdOftalApparetus ? objOdOftalApparetus.title : '-'
            this.generalist.ophthal_od.silia = objOdOftalSilia ? objOdOftalSilia.title : '-'
            this.generalist.ophthal_od.conjunctive_hiperemis = objOdOftalConjHiperemis ? objOdOftalConjHiperemis.title : '-'
            this.generalist.ophthal_od.conjunctive_bleed = objOdOftalConjBleed ? objOdOftalConjBleed.title : '-'
            this.generalist.ophthal_od.cornea = objOdOftalKornea ? objOdOftalKornea.title : '-'
            this.generalist.ophthal_od.camera_oculi = objOdOftalAnterior ? objOdOftalAnterior.title : '-'
            this.generalist.ophthal_od.iris_color = objOdOftalIrisColor ? objOdOftalIrisColor.title : '-'
            this.generalist.ophthal_od.iris_cripte = objOdOftalIrisKripte ? objOdOftalIrisKripte.title : '-'
            this.generalist.ophthal_od.pupil_form = objOdOftalPupilForm ? objOdOftalPupilForm.title : '-'
            this.generalist.ophthal_od.pupil_diameter = objOdOftalPupilDiameter ? objOdOftalPupilDiameter.title : '-'
            this.generalist.ophthal_od.pupil_direct_light = objOdOftalPupilDirectLight ? objOdOftalPupilDirectLight.title : '-'
            this.generalist.ophthal_od.pupil_indirect_light = objOdOftalPupilIndirectLight ? objOdOftalPupilIndirectLight.title : '-'
            this.generalist.ophthal_od.lens = objOdOftalLens ? objOdOftalLens.title : '-'
            this.generalist.ophthal_od.shadow_test = objOdOftalShadow ? objOdOftalShadow.title : '-'
            this.generalist.ophthal_od.glandula = objOdOftalGlandula ? objOdOftalGlandula.title : '-'
            this.generalist.ophthal_od.field_view = objOdOftalLapang ? objOdOftalLapang.title : '-'
            this.generalist.ophthal_od.tio_tonometri_sciots = obj.ophthal_od && obj.ophthal_od.tio_tonometri_sciots ? obj.ophthal_od.tio_tonometri_sciots : '-'
            this.generalist.ophthal_od.fundus = obj.ophthal_od && obj.ophthal_od.fundus ? obj.ophthal_od.fundus : '-'

            this.generalist.ophthal_os.visus = objOsOftalVisus ? objOsOftalVisus.title : '-'
            this.generalist.ophthal_os.tio_digital = objOsOftalTIO ? objOsOftalTIO.title : '-'
            this.generalist.ophthal_os.eye_position = objOsOftalEyePosition ? objOsOftalEyePosition.title : '-'
            this.generalist.ophthal_os.eye_movement = objOsOftalMovement ? objOsOftalMovement.title : '-'
            this.generalist.ophthal_os.super_silia = objOsOftalSuperSilia ? objOsOftalSuperSilia.title : '-'
            this.generalist.ophthal_os.palpebra_edema = objOsOftalPalpebraEdema ? objOsOftalPalpebraEdema.title : '-'
            this.generalist.ophthal_os.palpebra_ptosis = objOsOftalPalpebraPtosis ? objOsOftalPalpebraPtosis.title : '-'
            this.generalist.ophthal_os.palpebra_lagophtalmus = objOsOftalPalpebraLagophtalmus ? objOsOftalPalpebraLagophtalmus.title : '-'
            this.generalist.ophthal_os.palpebra_tumor = objOsOftalPalpebraTumor ? objOsOftalPalpebraTumor.title : '-'
            this.generalist.ophthal_os.apparatus_lakrimalis = objOsOftalApparetus ? objOsOftalApparetus.title : '-'
            this.generalist.ophthal_os.silia = objOsOftalSilia ? objOsOftalSilia.title : '-'
            this.generalist.ophthal_os.conjunctive_hiperemis = objOsOftalConjHiperemis ? objOsOftalConjHiperemis.title : '-'
            this.generalist.ophthal_os.conjunctive_bleed = objOsOftalConjBleed ? objOsOftalConjBleed.title : '-'
            this.generalist.ophthal_os.cornea = objOsOftalKornea ? objOsOftalKornea.title : '-'
            this.generalist.ophthal_os.camera_oculi = objOsOftalAnterior ? objOsOftalAnterior.title : '-'
            this.generalist.ophthal_os.iris_color = objOsOftalIrisColor ? objOsOftalIrisColor.title : '-'
            this.generalist.ophthal_os.iris_cripte = objOsOftalIrisKripte ? objOsOftalIrisKripte.title : '-'
            this.generalist.ophthal_os.pupil_form = objOsOftalPupilForm ? objOsOftalPupilForm.title : '-'
            this.generalist.ophthal_os.pupil_diameter = objOsOftalPupilDiameter ? objOsOftalPupilDiameter.title : '-'
            this.generalist.ophthal_os.pupil_direct_light = objOsOftalPupilDirectLight ? objOsOftalPupilDirectLight.title : '-'
            this.generalist.ophthal_os.pupil_indirect_light = objOsOftalPupilIndirectLight ? objOsOftalPupilIndirectLight.title : '-'
            this.generalist.ophthal_os.lens = objOsOftalLens ? objOsOftalLens.title : '-'
            this.generalist.ophthal_os.shadow_test = objOsOftalShadow ? objOsOftalShadow.title : '-'
            this.generalist.ophthal_os.glandula = objOsOftalGlandula ? objOsOftalGlandula.title : '-'
            this.generalist.ophthal_os.field_view = objOsOftalLapang ? objOsOftalLapang.title : '-'
            this.generalist.ophthal_os.tio_tonometri_sciots = obj.ophthal_os && obj.ophthal_os.tio_tonometri_sciots ? obj.ophthal_os.tio_tonometri_sciots : '-'
            this.generalist.ophthal_os.fundus = obj.ophthal_os && obj.ophthal_os.fundus ? obj.ophthal_os.fundus : '-'

        },

        geLetter(id) {
            this.$store.dispatch('letter/getById', id).then(data => {
                this.letterData = data[0]
                const dateB = moment(data[0].end);
                const dateC = moment(data[0].start);
                this.days = dateB.diff(dateC, 'days') + 1
            })
        },
        localDate(input_date) {
            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },
        romanize(num) {
            if (!+num)
                return false;
            var digits = String(+num).split(""),
                key    = ["","C","CC","CCC","CD","D","DC","DCC","DCCC","CM",
                          "","X","XX","XXX","XL","L","LX","LXX","LXXX","XC",
                          "","I","II","III","IV","V","VI","VII","VIII","IX"],
                roman  = "",
                i      = 3;
            while (i--)
                roman = (key[+digits.pop() + (i * 10)] || "") + roman;
            return Array(+digits.join("") + 1).join("M") + roman;
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
        if (this.$route.params.soap_id) {
            this.getSoap(this.$route.params.soap_id)
        }
        // this.geLetter(this.$route.params.letter_id)
        window.addEventListener('keydown', this.handleKeyPress);
    },
    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },
};

</script>
<style>
@import '../../../../../@core/css/main/main.min.css';
@media print {
    @page {
        size: A4;
        margin: 16mm;
    }
}
.custom-row  {
  border-bottom: 1px dotted #999; /* garis bawah tiap tr */
}
.table-sm th, .table-sm td {
    padding: 0.2rem;
}
/* table,
th,
td {
    border: 1px solid black;
} */
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

/* #canvas {
    width: 210mm;
    width: 21.2cm !important;
    height: 29.7cm !important;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
} */

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

.with-line {
    text-decoration: underline;
    text-decoration-thickness: 2px;
    text-underline-offset: 8px;
}
</style>
