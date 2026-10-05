<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex justify-content-end">
                <!-- <div class="col text-center">
                    <div class="bg-warning-200 p-16 rounded">
                        Triase yang telah diinput sebelum 26 November 2024 dapat dicetak melalui
                        <router-link :to="{ path: '/prints/doctor-emergency-print/v1/'+ this.$route.params.soap_id}" class="font-weight-bold">tautan ini</router-link>
                    </div>
                </div> -->
                <b-button @click="closeWindow" size="sm" class="px-32 mr-2" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper mt-16">
                <div id="canvas" ref="canvas" style="position: relative;">
                    <div class="row align-items-center justify-content-center">
                        <img src="../../../../../@core/assets/images/logo-thb-small.png" width="80px" alt="rs-logo"> <br>
                        <div class="text-regular-14 pl-16">
                            <p class="text-bold-18">RS Taman Harapan Baru</p>
                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                            <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                        </div>
                    </div>
                    <section class="text-regular-16 mt-24" style="line-height: 140%;">
                        <div class="row">
                            <div class="col-6">
                                <table class="mt-16">
                                    <tr>
                                        <td class="align-top" style="width: 160px">Nomor RM</td>
                                        <td class="align-top text-right px-3">:</td>
                                        <td>{{ this.patient.norm }}</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top" style="">Nama</td>
                                        <td class="align-top text-right px-3">:</td>
                                        <td>{{ this.patient.alias_name }} {{ this.patient.fullname }}</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top" style="">Tanggal Lahir</td>
                                        <td class="align-top text-right px-3">:</td>
                                        <td>{{ localFullDate(this.patient.birth_date) }} ({{ parsedAge(patient.birth_date) }})</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top" style="">Jenis Kelamin</td>
                                        <td class="align-top text-right px-3">:</td>
                                        <td>{{ this.patient.sex_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top" style="">Tanggal Registrasi</td>
                                        <td class="align-top text-right px-3">:</td>
                                        <td>{{ this.patient.register_date }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                    </section>

                    <section class="card mt-32">
                        <div class="card-header text-center text-bold-18">TRIASE</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td class="align-top" style="width: 200px;">Tanggal Entri Triase</td>
                                                <td class="align-top px-3" style="width: 30px;">:</td>
                                                <td class="align-top">{{ triageData?.date_added || '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="align-top">Rujukan</td>
                                                <td class="align-top px-3">:</td>
                                                <td class="align-top">{{ triageData?.referral_type_id == 1 ? `Ya, dari ${triageData?.referral}` : 'Tidak' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Tingkat Kesadaran</td>
                                                <td class="px-3">:</td>
                                                <td>
                                                    <template v-if="triageData?.level_of_consciousness_type_id">
                                                        <div v-if="triageData?.level_of_consciousness_type_id == 1">Alert</div>
                                                        <div v-else-if="triageData?.level_of_consciousness_type_id == 2">Verbal</div>
                                                        <div v-else-if="triageData?.level_of_consciousness_type_id == 3">Painful</div>
                                                        <div v-else-if="triageData?.level_of_consciousness_type_id == 4">Unresponsive</div>
                                                    </template>
                                                    <span v-else>-</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top">Status Imunisasi (Khusus untuk bayi/anak)</td>
                                                <td class="align-top px-3">:</td>
                                                <td class="align-top">{{ triageData?.immunization_status_type_id ? 'Lengkap' : 'Tidak Lengkap' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-6">
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td class="align-top" style="width: 200px;">Alergi Obat</td>
                                                <td class="align-top px-3" style="width: 30px;">:</td>
                                                <td class="align-top">{{ triageData?.drug_allergy_type_id == 1 ? `Ya, dari ${triageData?.drug_allergy}` : 'Tidak' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="align-top">Keluhan Utama</td>
                                                <td class="align-top px-3">:</td>
                                                <td class="align-top">{{ triageData?.main_complaint || '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Macam Kasus</td>
                                                <td class="px-3">:</td>
                                                <td>
                                                    <template v-if="triageData?.type_of_cases_type_id">
                                                        <div v-if="triageData?.type_of_cases_type_id == 1">Visum</div>
                                                        <div v-else-if="triageData?.type_of_cases_type_id == 2">DOA</div>
                                                        <div v-else-if="triageData?.type_of_cases_type_id == 3">Non Trauma</div>
                                                        <div v-else-if="triageData?.type_of_cases_type_id == 4">Trauma</div>
                                                    </template>
                                                    <span v-else>-</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Trauma</td>
                                                <td class="px-3">:</td>
                                                <td>
                                                    <template v-if="triageData?.trauma_type_id">
                                                        <div v-if="triageData?.trauma_type_id == 1">KLL</div>
                                                        <div v-else-if="triageData?.trauma_type_id == 2">KDRT</div>
                                                        <div v-else-if="triageData?.trauma_type_id == 3">Kecelakaan Kerja</div>
                                                        <div v-else-if="triageData?.trauma_type_id == 4">Kekerasan Pada Anak</div>
                                                    </template>
                                                    <span v-else>-</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="mt-24">
                                <ModernLabel label="Skala Nyeri" class="mt-32" />
                                <table class="mt-10">
                                    <tbody>
                                        <tr>
                                            <td class="align-top" style="width: 200px;">Area Nyeri</td>
                                            <td class="align-top px-3" style="width: 30px;">:</td>
                                            <td class="align-top">{{ triageData?.pain_area || '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top" style="width: 200px;">Skala Nyeri</td>
                                            <td class="align-top px-3" style="width: 30px;">:</td>
                                            <td class="align-top"></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div class="row mt-12">
                                    <div class="col text-center">
                                        <b-icon :icon="getPainIcon('0')" :variant="getPainIconVariant('0')" />
                                        <label class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">0</label>
                                        <div class="mt-1">
                                            <img src="@/@core/assets/images/nurse/wong_baker/0.png" width="75px">
                                            <div class="c-form-label mt-10">Tidak nyeri</div>
                                        </div>
                                    </div>
                                    <div class="col text-center">
                                        <b-icon :icon="getPainIcon('1')" :variant="getPainIconVariant('1')" />
                                        <label class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">1</label>
                                    </div>
                                    <div class="col text-center">
                                        <b-icon :icon="getPainIcon('2')" :variant="getPainIconVariant('2')" />
                                        <label class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">2</label>
                                        <div class="mt-1">
                                            <img src="@/@core/assets/images/nurse/wong_baker/2.png" width="75px">
                                            <div class="c-form-label mt-10">Nyeri Ringan</div>
                                        </div>
                                    </div>
                                    <div class="col text-center">
                                        <b-icon :icon="getPainIcon('3')" :variant="getPainIconVariant('3')" />
                                        <label class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">3</label>
                                    </div>
                                    <div class="col text-center">
                                        <b-icon :icon="getPainIcon('4')" :variant="getPainIconVariant('4')" />
                                        <label class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">4</label>
                                        <div class="mt-1">
                                            <img src="@/@core/assets/images/nurse/wong_baker/4.png" width="75px">
                                            <div class="c-form-label mt-10">Nyeri yang mengganggu</div>
                                        </div>
                                    </div>
                                    <div class="col text-center">
                                        <b-icon :icon="getPainIcon('5')" :variant="getPainIconVariant('5')" />
                                        <label class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">5</label>
                                    </div>
                                    <div class="col text-center">
                                        <b-icon :icon="getPainIcon('6')" :variant="getPainIconVariant('6')" />
                                        <label class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">6</label>
                                        <div class="mt-1">
                                            <img src="@/@core/assets/images/nurse/wong_baker/6.png" width="75px">
                                            <div class="c-form-label mt-10">Nyeri yang menyusahkan</div>
                                        </div>
                                    </div>
                                    <div class="col text-center">
                                        <b-icon :icon="getPainIcon('7')" :variant="getPainIconVariant('7')" />
                                        <label class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">7</label>
                                    </div>
                                    <div class="col text-center">
                                        <b-icon :icon="getPainIcon('8')" :variant="getPainIconVariant('8')" />
                                        <label class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">8</label>
                                        <div class="mt-1">
                                            <img src="@/@core/assets/images/nurse/wong_baker/8.png" width="75px">
                                            <div class="c-form-label mt-10">Nyeri hebat</div>
                                        </div>
                                    </div>
                                    <div class="col text-center">
                                        <b-icon :icon="getPainIcon('9')" :variant="getPainIconVariant('9')" />
                                        <label class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">9</label>
                                    </div>
                                    <div class="col text-center">
                                        <b-icon :icon="getPainIcon('10')" :variant="getPainIconVariant('10')" />
                                        <label class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">10</label>
                                        <div class="mt-1">
                                            <img src="@/@core/assets/images/nurse/wong_baker/10.png" width="75px">
                                            <div class="c-form-label mt-10">Nyeri sangat hebat</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <ModernLabel :label="`Hasil Pemeriksaan Triase: ${triageData && triageData.triage_result ? triageData.triage_result : '-'}`" class="mt-32" />

                            <section class="mt-20">
                                <b-table-simple responsive>
                                    <b-thead class="text-bold-14 text-center">
                                        <b-tr>
                                            <b-th>PENGKAJIAN</b-th>
                                            <b-th variant="dark" class="text-white">LEVEL 1</b-th>
                                            <b-th variant="danger">LEVEL 2</b-th>
                                            <b-th variant="warning">LEVEL 3</b-th>
                                            <b-th variant="success">LEVEL 4</b-th>
                                            <b-th>LEVEL 5</b-th>
                                        </b-tr>
                                    </b-thead>
                                    <b-tbody class="text-regular-14">
                                        <b-tr>
                                            <b-td>
                                                <span class="font-weight-bold">JALAN NAFAS</span>
                                            </b-td>
                                            <b-td>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getAirwayIcon('A1')" :variant="getAirwayIconVariant('A1')" class="mr-8" />
                                                    <div class="lh-110">Ada Sumbatan Jalan Nafas</div>
                                                </div>
                                            </b-td>
                                            <b-td>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getAirwayIcon('B1')" :variant="getAirwayIconVariant('B1')" class="mr-8" />
                                                    <div class="lh-110">Bebas Atau Ada Ancaman Sumbatan</div>
                                                </div>
                                            </b-td>
                                            <b-td>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getAirwayIcon('C1')" :variant="getAirwayIconVariant('C1')" class="mr-8" />
                                                    <div class="lh-110">Bebas</div>
                                                </div>
                                            </b-td>
                                            <b-td>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getAirwayIcon('D1')" :variant="getAirwayIconVariant('D1')" class="mr-8" />
                                                    <div class="lh-110">Bebas</div>
                                                </div>
                                            </b-td>
                                            <b-td>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getAirwayIcon('E1')" :variant="getAirwayIconVariant('E1')" class="mr-8" />
                                                    <div class="lh-110">Bebas</div>
                                                </div>
                                            </b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td>
                                                <span class="font-weight-bold">PERNAFASAN</span>
                                            </b-td>
                                            <b-td>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getRespiratoryIcon('A1')" :variant="getRespiratoryIconVariant('A1')" class="mr-8" />
                                                    <div class="lh-110">Henti Nafas/Apnoe</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getRespiratoryIcon('A2')" :variant="getRespiratoryIconVariant('A2')" class="mr-8" />
                                                    <div class="lh-110">RR &lt; 10x/menit</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getRespiratoryIcon('A3')" :variant="getRespiratoryIconVariant('A3')" class="mr-8" />
                                                    <div class="lh-110">Sianosis</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getRespiratoryIcon('A4')" :variant="getRespiratoryIconVariant('A4')" class="mr-8" />
                                                    <div class="lh-110">SPO2 &lt; 90%</div>
                                                </div>
                                            </b-td>
                                            <b-td>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getRespiratoryIcon('B1')" :variant="getRespiratoryIconVariant('B1')" class="mr-8" />
                                                    <div class="lh-110">RR &ge; 32x/menit</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getRespiratoryIcon('B2')" :variant="getRespiratoryIconVariant('B2')" class="mr-8" />
                                                    <div class="lh-110">SPO2 &lt; 92%</div>
                                                </div>
                                            </b-td>
                                            <b-td>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getRespiratoryIcon('C1')" :variant="getRespiratoryIconVariant('C1')" class="mr-8" />
                                                    <div class="lh-110">RR 25-32x/menit</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getRespiratoryIcon('C2')" :variant="getRespiratoryIconVariant('C2')" class="mr-8" />
                                                    <div class="lh-110">SPO2 92-95%</div>
                                                </div>
                                            </b-td>
                                            <b-td>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getRespiratoryIcon('D1')" :variant="getRespiratoryIconVariant('D1')" class="mr-8" />
                                                    <div class="lh-110">RR 20-24x/menit</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getRespiratoryIcon('D2')" :variant="getRespiratoryIconVariant('D2')" class="mr-8" />
                                                    <div class="lh-110">SPO2 96-100%</div>
                                                </div>
                                            </b-td>
                                            <b-td>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getRespiratoryIcon('E1')" :variant="getRespiratoryIconVariant('E1')" class="mr-8" />
                                                    <div class="lh-110">RR 16-20 x/mnt</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getRespiratoryIcon('E2')" :variant="getRespiratoryIconVariant('E2')" class="mr-8" />
                                                    <div class="lh-110">SPO2 96-100%</div>
                                                </div>
                                            </b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td>
                                                <span class="font-weight-bold">SIRKULASI</span>
                                            </b-td>
                                            <b-td>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getCirculationIcon('A1')" :variant="getCirculationIconVariant('A1')" class="mr-8" />
                                                    <div class="lh-110">Henti Jantung / arrest</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getCirculationIcon('A2')" :variant="getCirculationIconVariant('A2')" class="mr-8" />
                                                    <div class="lh-110">Nadi Tidak Teraba</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getCirculationIcon('A3')" :variant="getCirculationIconVariant('A3')" class="mr-8" />
                                                    <div class="lh-110">Akral Dingin</div>
                                                </div>
                                            </b-td>
                                            <b-td>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getCirculationIcon('B1')" :variant="getCirculationIconVariant('B1')" class="mr-8" />
                                                    <div class="lh-110">Nadi Lemah</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getCirculationIcon('B2')" :variant="getCirculationIconVariant('B2')" class="mr-8" />
                                                    <div class="lh-110">HR &lt; 50 x/mnt atau &gt; 150 x/mnt</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getCirculationIcon('B3')" :variant="getCirculationIconVariant('B3')" class="mr-8" />
                                                    <div class="lh-110">Pucat, Akral Dingin</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getCirculationIcon('B4')" :variant="getCirculationIconVariant('B4')" class="mr-8" />
                                                    <div class="lh-110">CRT &gt; 2 Detik</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getCirculationIcon('B5')" :variant="getCirculationIconVariant('B5')" class="mr-8" />
                                                    <div class="lh-110">TD &lt; 100/&lt; 60 mmHg</div>
                                                </div>
                                            </b-td>
                                            <b-td>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getCirculationIcon('C1')" :variant="getCirculationIconVariant('C1')" class="mr-8" />
                                                    <div class="lh-110">Nadi Kuat</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getCirculationIcon('C2')" :variant="getCirculationIconVariant('C2')" class="mr-8" />
                                                    <div class="lh-110">HR 121-150x/menit</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getCirculationIcon('C3')" :variant="getCirculationIconVariant('C3')" class="mr-8" />
                                                    <div class="lh-110">TD &gt; 160/ &gt; 100 mmHg</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getCirculationIcon('C4')" :variant="getCirculationIconVariant('C4')" class="mr-8" />
                                                    <div class="lh-110">CRT &lt; 2 Detik</div>
                                                </div>
                                            </b-td>
                                            <b-td>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getCirculationIcon('D1')" :variant="getCirculationIconVariant('D1')" class="mr-8" />
                                                    <div class="lh-110">HR 101-120x/menit</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getCirculationIcon('D2')" :variant="getCirculationIconVariant('D2')" class="mr-8" />
                                                    <div class="lh-110">TD 121 - 140/80-100 mmHg</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getCirculationIcon('D3')" :variant="getCirculationIconVariant('D3')" class="mr-8" />
                                                    <div class="lh-110">CRT &lt; 2 Detik</div>
                                                </div>
                                            </b-td>
                                            <b-td>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getCirculationIcon('E1')" :variant="getCirculationIconVariant('E1')" class="mr-8" />
                                                    <div class="lh-110">HR 80-100x/menit</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getCirculationIcon('E2')" :variant="getCirculationIconVariant('E2')" class="mr-8" />
                                                    <div class="lh-110">TD 120/80 mmHg</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getCirculationIcon('E3')" :variant="getCirculationIconVariant('E3')" class="mr-8" />
                                                    <div class="lh-110">CRT &lt; 2 Detik</div>
                                                </div>
                                            </b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td>
                                                <span class="font-weight-bold">KESADARAN</span>
                                            </b-td>
                                            <b-td>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getConsciousnessIcon('A1')" :variant="getConsciousnessIconVariant('A1')" class="mr-8" />
                                                    <div class="lh-110">GCS &lt; 9</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getConsciousnessIcon('A2')" :variant="getConsciousnessIconVariant('A2')" class="mr-8" />
                                                    <div class="lh-110">Kejang</div>
                                                </div>
                                            </b-td>
                                            <b-td>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getConsciousnessIcon('B1')" :variant="getConsciousnessIconVariant('B1')" class="mr-8" />
                                                    <div class="lh-110">GCS 9-12</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getConsciousnessIcon('B2')" :variant="getConsciousnessIconVariant('B2')" class="mr-8" />
                                                    <div class="lh-110">Gelisah</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getConsciousnessIcon('B3')" :variant="getConsciousnessIconVariant('B3')" class="mr-8" />
                                                    <div class="lh-110">Nyeri Dada</div>
                                                </div>
                                            </b-td>
                                            <b-td>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getConsciousnessIcon('C1')" :variant="getConsciousnessIconVariant('C1')" class="mr-8" />
                                                    <div class="lh-110">GCS 13-14</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getConsciousnessIcon('C2')" :variant="getConsciousnessIconVariant('C2')" class="mr-8" />
                                                    <div class="lh-110">Apatis / Samnolen</div>
                                                </div>
                                            </b-td>
                                            <b-td>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getConsciousnessIcon('D1')" :variant="getConsciousnessIconVariant('D1')" class="mr-8" />
                                                    <div class="lh-110">GCS 15</div>
                                                </div>
                                            </b-td>
                                            <b-td>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getConsciousnessIcon('E1')" :variant="getConsciousnessIconVariant('D1')" class="mr-8" />
                                                    <div class="lh-110">GCS 15</div>
                                                </div>
                                            </b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td>
                                                <span class="font-weight-bold">SUMBER DAYA</span>
                                            </b-td>
                                            <b-td></b-td>
                                            <b-td></b-td>
                                            <b-td>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getResourceIcon('C1')" :variant="getResourceIconVariant('C1')" class="mr-8" />
                                                    <div class="lh-110">Menggunakan &gt; 2 sumber daya</div>
                                                </div>
                                            </b-td>
                                            <b-td>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getResourceIcon('D1')" :variant="getResourceIconVariant('D1')" class="mr-8" />
                                                    <div class="lh-110">Menggunakan 1 sumber daya</div>
                                                </div>
                                            </b-td>
                                            <b-td>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getResourceIcon('E1')" :variant="getResourceIconVariant('E1')" class="mr-8" />
                                                    <div class="lh-110">Tidak menggunakan sumber daya</div>
                                                </div>
                                            </b-td>
                                        </b-tr>
                                    </b-tbody>
                                </b-table-simple>
                            </section>

                            <section class="mt-32">
                                <b-table-simple bordered class="mt-20">
                                    <b-tr>
                                        <b-th>Sumber Daya</b-th>
                                        <b-th colspan="5">Danger Zone</b-th>
                                    </b-tr>
                                    <b-tr>
                                        <b-td rowspan="5">
                                            <div class="d-flex pt-2">
                                                <b-icon :icon="getSecondResourceIcon('a')" :variant="getSecondResourceIconVariant('a')" class="mr-8" />
                                                <div class="lh-110">Laboratoriun darah</div>
                                            </div>
                                            <div class="d-flex pt-2">
                                                <b-icon :icon="getSecondResourceIcon('b')" :variant="getSecondResourceIconVariant('b')" class="mr-8" />
                                                <div class="lh-110">Laboratoriun urine</div>
                                            </div>
                                            <div class="d-flex pt-2">
                                                <b-icon :icon="getSecondResourceIcon('c')" :variant="getSecondResourceIconVariant('c')" class="mr-8" />
                                                <div class="lh-110">EKG</div>
                                            </div>
                                            <div class="d-flex pt-2">
                                                <b-icon :icon="getSecondResourceIcon('d')" :variant="getSecondResourceIconVariant('d')" class="mr-8" />
                                                <div class="lh-110">X-Ray</div>
                                            </div>
                                            <div class="d-flex pt-2">
                                                <b-icon :icon="getSecondResourceIcon('e')" :variant="getSecondResourceIconVariant('e')" class="mr-8" />
                                                <div class="lh-110">Pemasangan infus unt rehidrasi</div>
                                            </div>
                                            <div class="d-flex pt-2">
                                                <b-icon :icon="getSecondResourceIcon('f')" :variant="getSecondResourceIconVariant('f')" class="mr-8" />
                                                <div class="lh-110">Pemberian obat IV/IM/Anal</div>
                                            </div>
                                            <div class="d-flex pt-2">
                                                <b-icon :icon="getSecondResourceIcon('g')" :variant="getSecondResourceIconVariant('g')" class="mr-8" />
                                                <div class="lh-110">Pemasangan kateter</div>
                                            </div>
                                            <div class="d-flex pt-2">
                                                <b-icon :icon="getSecondResourceIcon('h')" :variant="getSecondResourceIconVariant('h')" class="mr-8" />
                                                <div class="lh-110">Pemasangan NGT</div>
                                            </div>
                                            <div class="d-flex pt-2">
                                                <b-icon :icon="getSecondResourceIcon('i')" :variant="getSecondResourceIconVariant('i')" class="mr-8" />
                                                <div class="lh-110">Nebulizer</div>
                                            </div>
                                            <div class="d-flex pt-2">
                                                <b-icon :icon="getSecondResourceIcon('j')" :variant="getSecondResourceIconVariant('j')" class="mr-8" />
                                                <div class="lh-110">Jahit/hecting</div>
                                            </div>
                                            <div class="d-flex pt-2">
                                                <b-icon :icon="getSecondResourceIcon('k')" :variant="getSecondResourceIconVariant('k')" class="mr-8" />
                                                <div class="lh-110">Konsultasi spesialis</div>
                                            </div>
                                            <div class="d-flex pt-2">
                                                <b-icon :icon="getSecondResourceIcon('l')" :variant="getSecondResourceIconVariant('l')" class="mr-8" />
                                                <div class="lh-110">Pemasangan monitor</div>
                                            </div>
                                            <div class="d-flex pt-2">
                                                <b-icon :icon="getSecondResourceIcon('m')" :variant="getSecondResourceIconVariant('m')" class="mr-8" />
                                                <div class="lh-110">Pemasangan ETT</div>
                                            </div>
                                        </b-td>
                                        <b-th>Usia</b-th>
                                        <b-th>HR</b-th>
                                        <b-th>RR</b-th>
                                        <b-th>S</b-th>
                                        <b-th>SPO2</b-th>
                                    </b-tr>
                                    <b-tr>
                                        <b-td>
                                            <div class="d-flex">
                                                <b-icon :icon="getRadioZoneIcon('1')" :variant="getRadioZoneIconVariant('1')" class="mr-8" />
                                                <div class="lh-110">&lt; 3 bulan</div>
                                            </div>
                                        </b-td>
                                        <b-td>&gt; 180</b-td>
                                        <b-td>&gt; 50</b-td>
                                        <b-td>&gt; 38ᵒ</b-td>
                                        <b-td>92%</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td>
                                            <div class="d-flex">
                                                <b-icon :icon="getRadioZoneIcon('2')" :variant="getRadioZoneIconVariant('2')" class="mr-8" />
                                                <div class="lh-110">3 bulan - 3 tahun</div>
                                            </div>
                                        </b-td>
                                        <b-td>&gt; 160</b-td>
                                        <b-td>&gt; 40</b-td>
                                        <b-td>&gt; 38ᵒ</b-td>
                                        <b-td>92%</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td>
                                            <div class="d-flex">
                                                <b-icon :icon="getRadioZoneIcon('3')" :variant="getRadioZoneIconVariant('3')" class="mr-8" />
                                                <div class="lh-110">3-8 tahun</div>
                                            </div>
                                        </b-td>
                                        <b-td>&gt; 140</b-td>
                                        <b-td>&gt; 30</b-td>
                                        <b-td>&gt; 38ᵒ</b-td>
                                        <b-td>92%</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td>
                                            <div class="d-flex">
                                                <b-icon :icon="getRadioZoneIcon('4')" :variant="getRadioZoneIconVariant('4')" class="mr-8" />
                                                <div class="lh-110">&gt; 8 tahun</div>
                                            </div>
                                        </b-td>
                                        <b-td>&gt; 100</b-td>
                                        <b-td>&gt; 20</b-td>
                                        <b-td>&gt; 38ᵒ</b-td>
                                        <b-td>92%</b-td>
                                    </b-tr>
                                </b-table-simple>

                                <table class="mt-16">
                                    <tbody>
                                        <tr>
                                            <td class="align-top" style="width: 200px;">Zona</td>
                                            <td class="align-top px-3" style="width: 30px;">:</td>
                                            <td class="align-top">
                                                <template v-if="triageData?.zone_type_id">
                                                    <div v-if="triageData?.zone_type_id == 1">Resusitasi</div>
                                                    <div v-else-if="triageData?.zone_type_id == 2">Tindakan</div>
                                                    <div v-else-if="triageData?.zone_type_id == 3">Observasi</div>
                                                    <div v-else-if="triageData?.zone_type_id == 4">Rawat Jalan</div>
                                                    <div v-else-if="triageData?.zone_type_id == 5">Ponek</div>
                                                </template>
                                                <span v-else>-</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </section>
                        </div>
                    </section>

                    <section class="card mt-32">
                        <div class="card-header text-center text-bold-18">PERAWAT</div>
                        <div class="card-body">
                            <section>
                                <table class="w-100">
                                    <tbody>
                                        <tr>
                                            <td class="align-top" style="width: 200px;">Tanggal Entri Asesmen Perawat</td>
                                            <td class="align-top px-3" style="width: 30px;">:</td>
                                            <td class="align-top">{{ this.nurseData && this.nurseData.date_added ? this.nurseData.date_added : '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <ModernLabel label="ANAMNESA" class="mt-16" />
                                <table class="w-100 mt-16">
                                    <tbody>
                                        <tr>
                                            <td style="width: 200px;">Anamnesa</td>
                                            <td style="width: 30px;" class="px-3">:</td>
                                            <td>{{ nurseData?.anamesa_type_id == '1' ? 'Auto Anamnesa' : 'Allo Anamnesa' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Keluarga</td>
                                            <td class="px-3">:</td>
                                            <td>
                                                <template v-if="nurseData?.anamesa_type_id == 2">
                                                    {{ nurseData && nurseData.family_name ? nurseData.family_name : '-' }}
                                                    ({{ getFamilyName(nurseData?.family_relation_id || '-') }})
                                                </template>
                                                <template v-else>-</template>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-top" >Keluhan Utama</td>
                                            <td class="align-top px-3">:</td>
                                            <td>{{ nurseData && nurseData.main_complaint ? nurseData.main_complaint : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top">Riwayat Penyakit Sekarang</td>
                                            <td class="align-top px-3">:</td>
                                            <td>
                                                <div v-html="parsedBody(nurseData && nurseData.history_disease_now ? nurseData.history_disease_now : '-')"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-top pt-2">Riwayat Penyakit Dahulu</td>
                                            <td class="align-top pt-2 px-3">:</td>
                                            <td class="pt-2">
                                                <div v-html="parsedBody(nurseData && nurseData.history_disease_past ? nurseData.history_disease_past : '-')"></div>
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
                                                                    <td>{{ nurseData && nurseData.systolic_blood_pressure ? nurseData.systolic_blood_pressure : '-' }} mmHg</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Diastolik</td>
                                                                    <td class="px-1 text-center">:</td>
                                                                    <td>{{ nurseData && nurseData.diastolic_blood_pressure ? nurseData.diastolic_blood_pressure : '-' }} mmHg</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Frekuensi Nadi</td>
                                                                    <td class="px-1 text-center">:</td>
                                                                    <td>{{ nurseData && nurseData.pulse_frequency ? nurseData.pulse_frequency : '-' }} x/mnt</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Frekuensi Nafas</td>
                                                                    <td class="px-1 text-center">:</td>
                                                                    <td>{{ nurseData && nurseData.breath_frequency ? nurseData.breath_frequency : '-' }} x/mnt</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Saturasi Oksigen</td>
                                                                    <td class="px-1 text-center">:</td>
                                                                    <td>{{ nurseData && nurseData.oximetry_frequency ? nurseData.oximetry_frequency : '-'}} %</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Suhu</td>
                                                                    <td class="px-1 text-center">:</td>
                                                                    <td>{{ nurseData && nurseData.temperature ? nurseData.temperature : '-' }} ᵒC</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="align-top">Oksigen</td>
                                                                    <td class="align-top px-1 text-center">:</td>
                                                                    <td>{{ getOxygenName(nurseData?.oxygen_type_id || '-') }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="align-top">Kesadaran</td>
                                                                    <td class="align-top px-1 text-center">:</td>
                                                                    <td>{{ getAwareName(nurseData?.aware) }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="align-top">GCS Mata</td>
                                                                    <td class="align-top px-1 text-center">:</td>
                                                                    <td>{{ getGcsEyeName(nurseData?.eye_score || '-') }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="align-top">GCS Verbal</td>
                                                                    <td class="align-top px-1 text-center">:</td>
                                                                    <td>{{ getGcsVerbalName(nurseData?.verbal_score || '-') }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="align-top">GCS Motorik</td>
                                                                    <td class="align-top px-1 text-center">:</td>
                                                                    <td>{{ getGcsMotoricName(nurseData?.motor_score || '-') }}</td>
                                                                </tr>
                                                                <!-- <tr>
                                                                    <td>Skor Nyeri</td>
                                                                    <td class="px-1 text-center">:</td>
                                                                    <td>{{ nurseData && nurseData.sore_score ? nurseData.sore_score : '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>EWS</td>
                                                                    <td class="px-1 text-center">:</td>
                                                                    <td>{{ nurseData && nurseData.ews ? nurseData.ews : '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Gula Darah</td>
                                                                    <td class="px-1 text-center">:</td>
                                                                    <td>{{ nurseData && nurseData.blood_sugar ? nurseData.blood_sugar : '-' }} mg/dL</td>
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
                                                                    <td>{{ nurseData && nurseData.weight ? nurseData.weight : '-' }} kg</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Tinggi Badan</td>
                                                                    <td class="px-1 text-center">:</td>
                                                                    <td>{{ nurseData && nurseData.height ? nurseData.height : '-' }} cm</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>IMT</td>
                                                                    <td class="px-1 text-center">:</td>
                                                                    <td>{{ nurseData && nurseData.imt ? nurseData.imt : '-' }} kg/m²</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Lingkar Kepala</td>
                                                                    <td class="px-1 text-center">:</td>
                                                                    <td>{{ nurseData && nurseData.head_circumference ? nurseData.head_circumference : '-' }} cm</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Lingkar Dada</td>
                                                                    <td class="px-1 text-center">:</td>
                                                                    <td>{{ nurseData && nurseData.chest_circumference ? nurseData.chest_circumference : '-' }} cm</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Lingkar Lengan Atas</td>
                                                                    <td class="px-1 text-center">:</td>
                                                                    <td>{{ nurseData && nurseData.arm_circumference ? nurseData.arm_circumference : '-' }} cm</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Lingkar Perut</td>
                                                                    <td class="px-1 text-center">:</td>
                                                                    <td>{{ nurseData && nurseData.stomach_circumference ? nurseData.stomach_circumference : '-' }} cm</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Lingkar Lutut</td>
                                                                    <td class="px-1 text-center">:</td>
                                                                    <td>{{ nurseData && nurseData.knee_circumference ? nurseData.knee_circumference : '-' }} cm</td>
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
                            <div class="mt-20">
                                <ModernLabel label="RIWAYAT KESEHATAN" />
                                <table class="w-100 mt-16">
                                    <tbody>
                                        <tr>
                                            <td style="width: 200px;">Pernah Dirawat Sebelumnya</td>
                                            <td style="width: 30px;" class="px-3">:</td>
                                            <td>
                                                <span v-if="emergencyData && emergencyData.been_hospitalized_type_id">
                                                    {{ emergencyData.been_hospitalized_type_id == 1 ? 'Iya, ' : 'Tidak' }}
                                                    <span>{{ emergencyData && emergencyData.been_hospitalized_answer ? emergencyData.been_hospitalized_answer : '' }}</span>
                                                </span>
                                                <span v-else>-</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Riwayat Penyakit Keluarga</td>
                                            <td class="px-3">:</td>
                                            <td>
                                                {{ emergencyData && emergencyData.any_history_family_disease_type_id == '1' ? 'Ya: ' : 'Tidak' }}
                                                <span v-if="emergencyData && emergencyData.any_history_family_answer_arry">
                                                    <template v-if="emergencyData.any_history_family_answer_arry.includes('a')">DM, </template>
                                                    <template v-if="emergencyData.any_history_family_answer_arry.includes('b')">Hipertensi, </template>
                                                    <template v-if="emergencyData.any_history_family_answer_arry.includes('c')">Cancer, </template>
                                                    <template v-if="emergencyData.any_history_family_answer_arry.includes('d')">Jantung, </template>
                                                    <template v-if="emergencyData.any_history_family_answer_arry.includes('e')">TBC, </template>
                                                    <template v-if="emergencyData.any_history_family_answer_arry.includes('f')">Anemia, </template>
                                                    <template v-if="emergencyData.any_history_family_answer_arry.includes('g')">
                                                        <span>{{ emergencyData && emergencyData.any_history_family_answer ? emergencyData.any_history_family_answer : '' }}</span>
                                                    </template>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Riwayat Alergi</td>
                                            <td class="px-3">:</td>
                                            <td>
                                                {{ emergencyData && emergencyData.history_allergy_type_id == '1' ? 'Ya: ' : '' }}
                                                <span>{{ emergencyData && emergencyData.history_allergy ? emergencyData.history_allergy : '-' }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="font-weight-bold">Operasi Yang Pernah Alami</td>
                                        </tr>
                                        <tr>
                                            <td>Pernah Operasi</td>
                                            <td class="px-3">:</td>
                                            <td>{{ emergencyData && emergencyData.any_surgery_type_id == '1' ? 'Ya' : 'Tidak' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Tanggal</td>
                                            <td class="px-3">:</td>
                                            <td>{{ emergencyData && emergencyData.any_surgery_date ? localFullDate(emergencyData.any_surgery_date) : '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-20">
                                <div class="row">
                                    <div class="col-6">
                                        <div>
                                            <ModernLabel label="RIWAYAT PSIKOLOGIS" />
                                            <table class="w-100 mt-16">
                                                <tbody>
                                                    <tr>
                                                        <td style="width: 200px;" class="align-top">Riwayat Psikologis</td>
                                                        <td style="width: 30px;" class="align-top px-3">:</td>
                                                        <td>
                                                            <template v-if="emergencyData && emergencyData.history_psychological_arry">
                                                                <li v-if="emergencyData.history_psychological_arry.includes('a')">Tidak Semangat</li>
                                                                <li v-if="emergencyData.history_psychological_arry.includes('b')">Cepat Lelah</li>
                                                                <li v-if="emergencyData.history_psychological_arry.includes('c')">Menggunakan Obat Penenang</li>
                                                                <li v-if="emergencyData.history_psychological_arry.includes('d')">Rasa Tertekan</li>
                                                                <li v-if="emergencyData.history_psychological_arry.includes('e')">Sulit Berbicara</li>
                                                                <li v-if="emergencyData.history_psychological_arry.includes('f')">Merasa Bersalah</li>
                                                                <li v-if="emergencyData.history_psychological_arry.includes('g')">Depresi</li>
                                                                <li v-if="emergencyData.history_psychological_arry.includes('h')">Kurang Nafsu Makan</li>
                                                                <li v-if="emergencyData.history_psychological_arry.includes('i')">Tenang</li>
                                                                <li v-if="emergencyData.history_psychological_arry.includes('j')">Sulit Tidur</li>
                                                                <li v-if="emergencyData.history_psychological_arry.includes('k')">Sulit Konsentrasi</li>
                                                                <li v-if="emergencyData.history_psychological_arry.includes('l')">Tidak Ada Keluhan</li>
                                                            </template>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="mt-20">
                                            <ModernLabel label="PENGKAJIAN RISIKO JATUH" />
                                            <table class="w-100 mt-16">
                                                <tbody>
                                                    <tr>
                                                        <td style="width: 200px;">Risiko</td>
                                                        <td style="width: 30px;" class="px-3">:</td>
                                                        <td>
                                                            <span v-if="emergencyData && emergencyData.fall_risk_type_id">
                                                                <template v-if="emergencyData.fall_risk_type_id == '1'">Tidak Beresiko</template>
                                                                <template v-if="emergencyData.fall_risk_type_id == '2'">Beresiko Rendah</template>
                                                                <template v-if="emergencyData.fall_risk_type_id == '3'">Beresiko Tinggi</template>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3">
                                                            <b-table-simple bordered responsive class="mt-12">
                                                                <b-thead>
                                                                    <b-tr>
                                                                        <b-th>Komponen Penilaian</b-th>
                                                                        <b-th>Tidak</b-th>
                                                                        <b-th>Ya</b-th>
                                                                    </b-tr>
                                                                </b-thead>
                                                                <b-tbody>
                                                                    <b-tr>
                                                                        <b-td>
                                                                            <ol type="a">
                                                                                <li>Perhatikan cara jalan pasien saat akan duduk di kursi. Apakah pasien tampak seimbang (Sempoyong/ seimbang) ?</li>
                                                                            </ol>
                                                                        </b-td>
                                                                        <b-td style="width: 70px;">
                                                                            <b-icon :icon="getFallAIcon('0')" :variant="getFallAIconVariant('0')" class="mr-8" />
                                                                        </b-td>
                                                                        <b-td style="width: 70px;">
                                                                            <b-icon :icon="getFallAIcon('1')" :variant="getFallAIconVariant('1')" class="mr-8" />
                                                                        </b-td>
                                                                    </b-tr>
                                                                    <b-tr>
                                                                        <b-td>
                                                                            <ol type="a" start="2">
                                                                                <li>Apakah pasien memegang pinggiran kursi atau benda lain sebagai penopang saat akan duduk ?</li>
                                                                            </ol>
                                                                        </b-td>
                                                                        <b-td style="width: 70px;">
                                                                            <b-icon :icon="getFallBIcon('0')" :variant="getFallBIconVariant('0')" class="mr-8" />
                                                                        </b-td>
                                                                        <b-td style="width: 70px;">
                                                                            <b-icon :icon="getFallBIcon('1')" :variant="getFallBIconVariant('1')" class="mr-8" />
                                                                        </b-td>
                                                                    </b-tr>
                                                                </b-tbody>
                                                            </b-table-simple>
                                                        </td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <ModernLabel label="SKRINING GIZI" />
                                        <table class="w-100 mt-16">
                                            <tbody>
                                                <tr>
                                                    <td style="width: 200px;">Indeks Masa Tubuh</td>
                                                    <td style="width: 30px;" class="px-3">:</td>
                                                    <td>
                                                        <span>{{ nurseData && nurseData.imt ? nurseData.imt : '0' }} kg/m² </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Asupan Makan Pasien</td>
                                                    <td class="px-3">:</td>
                                                    <td>
                                                        <span v-if="emergencyData && emergencyData.patient_food_intake">
                                                            <template v-if="emergencyData.patient_food_intake == '1'">Baik</template>
                                                            <template v-else-if="emergencyData.patient_food_intake == '2'">Kurang</template>
                                                            <template v-else-if="emergencyData.patient_food_intake == '3'">Kurang Semangat</template>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Kehilangan Berat Badan (Dalam Waktu 6 Bulan Terakhir)</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td class="align-top">
                                                        <span v-if="emergencyData && emergencyData.six_month_lose_weight">
                                                            <template v-if="emergencyData.six_month_lose_weight == '1'">Tidak</template>
                                                            <template v-else-if="emergencyData.six_month_lose_weight == '2'">Tidak Yakin</template>
                                                            <template v-else-if="emergencyData.six_month_lose_weight == '3'">Ya, </template>
                                                        </span>
                                                        <span v-if="emergencyData && emergencyData.lose_weight">
                                                            <template v-if="emergencyData.lose_weight == '1'">1 - 5 kg</template>
                                                            <template v-else-if="emergencyData.lose_weight == '2'">6 - 10 kg</template>
                                                            <template v-else-if="emergencyData.lose_weight == '3'"></template>
                                                            <template v-else-if="emergencyData.lose_weight == '4'">&gt; 15 kg</template>
                                                            <template v-else-if="emergencyData.lose_weight == '5'">Tidak Tahu Berapa kg</template>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Kondisi Penyakit Pasien</td>
                                                    <td class="px-3">:</td>
                                                    <td>
                                                        <span v-if="emergencyData && emergencyData.patient_disease_condition">
                                                            <template v-if="emergencyData.patient_disease_condition == '1'">Tidak</template>
                                                            <template v-else-if="emergencyData.patient_disease_condition == '2'">Ya</template>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Pasien Sedang Mendapat Diet Makanan Tertentu</td>
                                                    <td class="px-3">:</td>
                                                    <td>
                                                        <span v-if="emergencyData && emergencyData.patient_certain_diets">
                                                            <template v-if="emergencyData.patient_certain_diets == '1'">Tidak</template>
                                                            <template v-else-if="emergencyData.patient_certain_diets == '2'">Ya</template>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Total Skor</td>
                                                    <td class="px-3">:</td>
                                                    <td>{{ emergencyData && emergencyData.nutrition_total_score ? emergencyData.nutrition_total_score : '-' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-20">
                                <ModernLabel label="DAFTAR MASALAH KEPERAWATAN" />
                                <div class="row">
                                    <div class="col-6">
                                        <table class="w-100 mt-16">
                                            <tbody>
                                                <tr>
                                                    <td style="width: 200px;" class="align-top">Diagnosa Keperawatan</td>
                                                    <td style="width: 30px;" class="align-top px-3">:</td>
                                                    <td>
                                                        <template v-if="emergencyData && emergencyData.nursing_diagnoses_arry">
                                                            <li v-if="emergencyData.nursing_diagnoses_arry.includes('a')">Bersihkan jalan nafas</li>
                                                            <li v-if="emergencyData.nursing_diagnoses_arry.includes('b')">Cemas/ Ansietas</li>
                                                            <li v-if="emergencyData.nursing_diagnoses_arry.includes('c')">Ketidakefektifan jalan nafas</li>
                                                            <li v-if="emergencyData.nursing_diagnoses_arry.includes('d')">Hipertemi</li>
                                                            <li v-if="emergencyData.nursing_diagnoses_arry.includes('e')">Gangguan mobilitas fisik</li>
                                                            <li v-if="emergencyData.nursing_diagnoses_arry.includes('f')">Keterlambatan pertumbuhan dan perkembangan</li>
                                                            <li v-if="emergencyData.nursing_diagnoses_arry.includes('g')">Intoleransi aktifitas</li>
                                                            <li v-if="emergencyData.nursing_diagnoses_arry.includes('h')">Risiko tinggi infeksi</li>
                                                            <li v-if="emergencyData.nursing_diagnoses_arry.includes('i')">Gangguan persepsi sensori pendengaran</li>
                                                            <li v-if="emergencyData.nursing_diagnoses_arry.includes('j')">Risiko tinggi cedera</li>
                                                            <li v-if="emergencyData.nursing_diagnoses_arry.includes('k')">Gangguan persepsi sensori penglihatan</li>
                                                            <li v-if="emergencyData.nursing_diagnoses_arry.includes('l')">Risiko kekurangan cairan</li>
                                                            <li v-if="emergencyData.nursing_diagnoses_arry.includes('m')">Gangguan integritas kulit</li>
                                                            <li v-if="emergencyData.nursing_diagnoses_arry.includes('n')">Hipertensi</li>
                                                            <li v-if="emergencyData.nursing_diagnoses_arry.includes('o')">Kurang pengetahuan</li>
                                                        </template>
                                                        <template v-else>-</template>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Catatan</td>
                                                    <td class="px-3 align-top">:</td>
                                                    <td>
                                                        <div v-html="parsedBody(emergencyData && emergencyData.nursing_diagnoses ? emergencyData.nursing_diagnoses : '-')"></div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-6">
                                        <table class="w-100 mt-16">
                                            <tbody>
                                                <tr>
                                                    <td style="width: 200px;" class="align-top">Implementasi</td>
                                                    <td style="width: 30px;" class="align-top px-3">:</td>
                                                    <td>
                                                        <template v-if="emergencyData && emergencyData.implementation_arry">
                                                            <li v-if="emergencyData.implementation_arry.includes('a')">Menjelaskan cara pencegahan dan perawatan kulit</li>
                                                            <li v-if="emergencyData.implementation_arry.includes('b')">Sehat</li>
                                                            <li v-if="emergencyData.implementation_arry.includes('c')">Mengajarkan teknik nafas dalam</li>
                                                            <li v-if="emergencyData.implementation_arry.includes('d')">MCU</li>
                                                            <li v-if="emergencyData.implementation_arry.includes('e')">Mengajarkan batuk efektif</li>
                                                            <li v-if="emergencyData.implementation_arry.includes('f')">Menganjurkan untuk banyak makan buah segar dan kacang-kacangan</li>
                                                            <li v-if="emergencyData.implementation_arry.includes('g')">Menginformasikan tidak merokok dalam ruangan</li>
                                                            <li v-if="emergencyData.implementation_arry.includes('h')">SKPN dan Rapid test</li>
                                                            <li v-if="emergencyData.implementation_arry.includes('i')">Memantau tanda-tanda sianosis</li>
                                                            <li v-if="emergencyData.implementation_arry.includes('j')">MCU calon karyawan</li>
                                                            <li v-if="emergencyData.implementation_arry.includes('k')">Mengukur warna dan suhu kulit</li>
                                                            <li v-if="emergencyData.implementation_arry.includes('l')">Tidak menahan BAK</li>
                                                            <li v-if="emergencyData.implementation_arry.includes('m')">Mengatur posisi nyaman</li>
                                                            <li v-if="emergencyData.implementation_arry.includes('n')">Memberikan obat tetes mata</li>
                                                            <li v-if="emergencyData.implementation_arry.includes('o')">Menganjurkan banyak minum</li>
                                                            <li v-if="emergencyData.implementation_arry.includes('p')">Manganjurkan pola hidup sehat</li>
                                                            <li v-if="emergencyData.implementation_arry.includes('q')">Kompres air hangat pada daerah yang gatal</li>
                                                            <li v-if="emergencyData.implementation_arry.includes('r')">Swab PCR some day</li>
                                                            <li v-if="emergencyData.implementation_arry.includes('s')">Jaga kebersihan</li>
                                                            <li v-if="emergencyData.implementation_arry.includes('t')">Injeksi Vit. C</li>
                                                            <li v-if="emergencyData.implementation_arry.includes('u')">SKPN</li>
                                                            <li v-if="emergencyData.implementation_arry.includes('v')">Menganjurkan banyak istirahat</li>
                                                            <li v-if="emergencyData.implementation_arry.includes('w')">Anjurkan banyak minum air putih</li>
                                                            <li v-if="emergencyData.implementation_arry.includes('x')">Infus Vit. C</li>
                                                            <li v-if="emergencyData.implementation_arry.includes('y')">Makan porsi kecil tapi sering</li>
                                                            <li v-if="emergencyData.implementation_arry.includes('z')">Istirahatkan mata</li>
                                                            <li v-if="emergencyData.implementation_arry.includes('aa')">Nyeri</li>
                                                            <li v-if="emergencyData.implementation_arry.includes('ab')">Menganjurkan banyak makan sayur</li>
                                                            <li v-if="emergencyData.implementation_arry.includes('ac')">Anti HIV rapid</li>
                                                        </template>
                                                        <template v-else>-</template>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Catatan</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>
                                                        <div v-html="parsedBody(emergencyData?.implementation || '-')"></div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="card mt-32">
                        <div class="card-header text-center text-bold-18">PENGKAJIAN MEDIS IGD</div>
                        <div class="card-body">
                            <section>
                                <div class="col">
                                    <table class="w-100">
                                        <tr>
                                            <td class="align-top" style="width: 200px;">Tanggal Entri Asesmen Dokter</td>
                                            <td class="px-1 align-top text-center" style="width: 30px;" >:</td>
                                            <td class="align-top">{{ this.emergencyData && this.emergencyData.date_added ? this.emergencyData.date_added : '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <ModernLabel label="Diagnosa (ICD10)" class="mt-16" />
                                <div class="col">
                                    <table class="mt-8 w-100">
                                        <tr>
                                            <td style="width: 200px;" class="align-top">Diagnosis</td>
                                            <td style="width: 30px;" class="px-1 align-top text-center">:</td>
                                            <td>
                                                <div v-if="result.icd10">
                                                    <li v-for="(item, index) in result.icd10" >{{ item.code }} - {{ item.name }}</li>
                                                </div>
                                                <div v-else>-</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-top">Catatan</td>
                                            <td class="align-top px-1 text-center">:</td>
                                            <td>
                                                <div v-html="parsedBody(result.icd10_notes ? result.icd10_notes : '-')"></div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </section>
                            <section class="mt-20">
                                <ModernLabel label="Diagnosis Non-ICD10" />
                                <div class="col">
                                    <table class="mt-8 w-100">
                                        <tr>
                                            <td style="width: 200px;" class="align-top">Non-ICD10</td>
                                            <td style="width: 30px;" class="px-1 align-top text-center">:</td>
                                            <td>
                                                <div v-if="result.non_icd10">
                                                    <li v-for="(item, index) in result.non_icd10" >{{ item }}</li>
                                                </div>
                                                <div v-else>-</div>
                                            </td>
                                        </tr>
                                        <!-- <tr>
                                            <td class="align-top">Assesment Lainnya</td>
                                            <td class="align-top px-1 text-center">:</td>
                                            <td>
                                                <div v-html="parsedBody(result.soap_a_anythingelse ? result.soap_a_anythingelse : '-')"></div>
                                            </td>
                                        </tr> -->
                                    </table>
                                </div>
                            </section>
                            <section class="mt-20">
                                <ModernLabel label="ANAMNESA" />
                                <table class="w-100 mt-16">
                                    <tbody>
                                        <tr>
                                            <td style="width: 200px;">Anamnesa</td>
                                            <td style="width: 30px;" class="px-3">:</td>
                                            <td>{{ result.anamesa_type_id == '1' ? 'Auto Anamnesa' : 'Allo Anamnesa' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Keluarga</td>
                                            <td class="px-3">:</td>
                                            <td>
                                                <template v-if="result?.anamesa_type_id == 2">
                                                    {{ result && result.family_name ? result.family_name : '-' }}
                                                    ({{ getFamilyName(result?.family_relation_id || '-') }})
                                                </template>
                                                <template v-else>-</template>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-top" >Keluhan Utama</td>
                                            <td class="align-top px-3">:</td>
                                            <td>{{ result.main_complaint ? result.main_complaint : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top">Riwayat Penyakit Sekarang</td>
                                            <td class="align-top px-3">:</td>
                                            <td>
                                                <div v-html="parsedBody(result.history_disease_now ? result.history_disease_now : '-')"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-top pt-2">Riwayat Penyakit Dahulu</td>
                                            <td class="align-top pt-2 px-3">:</td>
                                            <td class="pt-2">
                                                <div v-html="parsedBody(result.history_disease_past ? result.history_disease_past : '-')"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-top pt-2">Riwayat Obat</td>
                                            <td class="align-top pt-2 px-3">:</td>
                                            <td class="pt-2">
                                                <div v-html="parsedBody(result.history_drugs ? result.history_drugs : '-')"></div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </section>
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
                                                    <td class="align-top">Sklera</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.eye_sclera }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Pupil</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.eye_pupil }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Reflek Cahaya</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>{{ generalist.eye_light_reflex }}</td>
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

                                    <template v-if="active.ophtal">
                                        <b-table-simple bordered fixed small class="mt-16">
                                            <b-thead>
                                                <b-tr class="text-center">
                                                    <b-th class="bg-warning-200">OD</b-th>
                                                    <b-th class="bg-primary-100">Pemeriksaan</b-th>
                                                    <b-th class="bg-warning-200">OS</b-th>
                                                </b-tr>
                                            </b-thead>
                                            <b-tbody>
                                                <b-tr>
                                                    <b-td>{{ generalist.ophthal_od.visus }}</b-td>
                                                    <b-th class="text-center bg-primary-100">Visus</b-th>
                                                    <b-td>{{ generalist.ophthal_os.visus }}</b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>
                                                        <div>
                                                            <span class="text-semi-16">Digital: </span>
                                                            <span>{{ generalist.ophthal_od.tio_digital }}</span>
                                                        </div>
                                                        <div class="mt-8">
                                                            <span class="text-semi-16">Tonometri Sciotz: </span>
                                                            <span>{{ generalist.ophthal_od ? generalist.ophthal_od.tio_tonometri_sciots : '-' }}</span>
                                                        </div>
                                                    </b-td>
                                                    <b-th class="text-center bg-primary-100">TIO</b-th>
                                                    <b-td>
                                                        <div>
                                                            <span class="text-semi-16">Digital: </span>
                                                            <span>{{ generalist.ophthal_os.tio_digital }}</span>
                                                        </div>
                                                        <div class="mt-8">
                                                            <span class="text-semi-16">Tonometri Sciotz: </span>
                                                            <span>{{ generalist.ophthal_os ? generalist.ophthal_os.tio_tonometri_sciots : '-' }}</span>
                                                        </div>
                                                    </b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>{{ generalist.ophthal_od.eye_position }}</b-td>
                                                    <b-th class="text-center bg-primary-100">Posisi Mata</b-th>
                                                    <b-td>{{ generalist.ophthal_os.eye_position }}</b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>{{ generalist.ophthal_od.eye_movement }}</b-td>
                                                    <b-th class="text-center bg-primary-100">Pergerakan Mata</b-th>
                                                    <b-td>{{ generalist.ophthal_os.eye_movement }}</b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>{{ generalist.ophthal_od.super_silia }}</b-td>
                                                    <b-th class="text-center bg-primary-100">Super Silia</b-th>
                                                    <b-td>{{ generalist.ophthal_os.super_silia }}</b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>
                                                        <div>
                                                            <span class="text-semi-16">Edema: </span>
                                                            <span>{{ generalist.ophthal_od.palpebra_edema }}</span>
                                                        </div>
                                                        <div class="mt-8">
                                                            <span class="text-semi-16">Ptosis: </span>
                                                            <span>{{ generalist.ophthal_od.palpebra_ptosis }}</span>
                                                        </div>
                                                        <div class="mt-8">
                                                            <span class="text-semi-16">Lagophtalmus: </span>
                                                            <span>{{ generalist.ophthal_od.palpebra_lagophtalmus }}</span>
                                                        </div>
                                                        <div class="mt-8">
                                                            <span class="text-semi-16">Tumor: </span>
                                                            <span>{{ generalist.ophthal_od.palpebra_tumor }}</span>
                                                        </div>
                                                    </b-td>
                                                    <b-th class="text-center bg-primary-100">Palpebra</b-th>
                                                    <b-td>
                                                        <div>
                                                            <span class="text-semi-16">Edema: </span>
                                                            <span>{{ generalist.ophthal_os.palpebra_edema }}</span>
                                                        </div>
                                                        <div class="mt-8">
                                                            <span class="text-semi-16">Ptosis: </span>
                                                            <span>{{ generalist.ophthal_os.palpebra_ptosis }}</span>
                                                        </div>
                                                        <div class="mt-8">
                                                            <span class="text-semi-16">Lagophtalmus: </span>
                                                            <span>{{ generalist.ophthal_os.palpebra_lagophtalmus }}</span>
                                                        </div>
                                                        <div class="mt-8">
                                                            <span class="text-semi-16">Tumor: </span>
                                                            <span>{{ generalist.ophthal_os.palpebra_tumor }}</span>
                                                        </div>
                                                    </b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>{{ generalist.ophthal_od.apparatus_lakrimalis }}</b-td>
                                                    <b-th class="text-center bg-primary-100">Apparatus Lakrimalis</b-th>
                                                    <b-td>{{ generalist.ophthal_os.apparatus_lakrimalis }}</b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>{{ generalist.ophthal_od.silia }}</b-td>
                                                    <b-th class="text-center bg-primary-100">Silia</b-th>
                                                    <b-td>{{ generalist.ophthal_os.silia }}</b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>
                                                        <div>
                                                            <span class="text-semi-16">Hiperemis: </span>
                                                            <span>{{ generalist.ophthal_od.conjunctive_hiperemis }}</span>
                                                        </div>
                                                        <div class="mt-8">
                                                            <span class="text-semi-16">Pendarahan: </span>
                                                            <span>{{ generalist.ophthal_od.conjunctive_bleed }}</span>
                                                        </div>
                                                    </b-td>
                                                    <b-th class="text-center bg-primary-100">Konjungtiva</b-th>
                                                    <b-td>
                                                        <div>
                                                            <span class="text-semi-16">Hiperemis: </span>
                                                            <span>{{ generalist.ophthal_os.conjunctive_hiperemis }}</span>
                                                        </div>
                                                        <div class="mt-8">
                                                            <span class="text-semi-16">Pendarahan: </span>
                                                            <span>{{ generalist.ophthal_os.conjunctive_bleed }}</span>
                                                        </div>
                                                    </b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>{{ generalist.ophthal_od.cornea }}</b-td>
                                                    <b-th class="text-center bg-primary-100">Kornea</b-th>
                                                    <b-td>{{ generalist.ophthal_os.cornea }}</b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>{{ generalist.ophthal_od.camera_oculi }}</b-td>
                                                    <b-th class="text-center bg-primary-100">Camera Oculi Anterior</b-th>
                                                    <b-td>{{ generalist.ophthal_os.camera_oculi }}</b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>
                                                        <div>
                                                            <span class="text-semi-16">Warna Iris: </span>
                                                            <span>{{ generalist.ophthal_od.iris_color }}</span>
                                                        </div>
                                                        <div class="mt-8">
                                                            <span class="text-semi-16">Kripte: </span>
                                                            <span>{{ generalist.ophthal_od.iris_cripte }}</span>
                                                        </div>
                                                    </b-td>
                                                    <b-th class="text-center bg-primary-100">Iris</b-th>
                                                    <b-td>
                                                        <div>
                                                            <span class="text-semi-16">Warna Iris: </span>
                                                            <span>{{ generalist.ophthal_os.iris_color }}</span>
                                                        </div>
                                                        <div class="mt-8">
                                                            <span class="text-semi-16">Kripte: </span>
                                                            <span>{{ generalist.ophthal_os.iris_cripte }}</span>
                                                        </div>
                                                    </b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>
                                                        <div>
                                                            <span class="text-semi-16">Bentuk Pupil: </span>
                                                            <span>{{ generalist.ophthal_od.pupil_form }}</span>
                                                        </div>
                                                        <div class="mt-8">
                                                            <span class="text-semi-16">Diameter Pupil: </span>
                                                            <span>{{ generalist.ophthal_od.pupil_diameter }}</span>
                                                        </div>
                                                        <div class="mt-8">
                                                            <span class="text-semi-16">Refleksi Cahaya Langsung: </span>
                                                            <span>{{ generalist.ophthal_od.pupil_direct_light }}</span>
                                                        </div>
                                                        <div class="mt-8">
                                                            <span class="text-semi-16">Refleksi Cahaya Tak Langsung: </span>
                                                            <span>{{ generalist.ophthal_od.pupil_indirect_light }}</span>
                                                        </div>
                                                    </b-td>
                                                    <b-th class="text-center bg-primary-100">Pupil</b-th>
                                                    <b-td>
                                                        <div>
                                                            <span class="text-semi-16">Bentuk Pupil: </span>
                                                            <span>{{ generalist.ophthal_os.pupil_form }}</span>
                                                        </div>
                                                        <div class="mt-8">
                                                            <span class="text-semi-16">Diameter Pupil: </span>
                                                            <span>{{ generalist.ophthal_os.pupil_diameter }}</span>
                                                        </div>
                                                        <div class="mt-8">
                                                            <span class="text-semi-16">Refleksi Cahaya Langsung: </span>
                                                            <span>{{ generalist.ophthal_os.pupil_direct_light }}</span>
                                                        </div>
                                                        <div class="mt-8">
                                                            <span class="text-semi-16">Refleksi Cahaya Tak Langsung: </span>
                                                            <span>{{ generalist.ophthal_os.pupil_indirect_light }}</span>
                                                        </div>
                                                    </b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>{{ generalist.ophthal_od.lens }}</b-td>
                                                    <b-th class="text-center bg-primary-100">Lensa</b-th>
                                                    <b-td>{{ generalist.ophthal_os.lens }}</b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>{{ generalist.ophthal_od.shadow_test }}</b-td>
                                                    <b-th class="text-center bg-primary-100">Shadow Test</b-th>
                                                    <b-td>{{ generalist.ophthal_os.shadow_test }}</b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>{{ generalist.ophthal_od.glandula }}</b-td>
                                                    <b-th class="text-center bg-primary-100">Glandula pre-aurikuler</b-th>
                                                    <b-td>{{ generalist.ophthal_os.glandula }}</b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>{{ generalist.ophthal_od.field_view }}</b-td>
                                                    <b-th class="text-center bg-primary-100">Lapang Pandang</b-th>
                                                    <b-td>{{ generalist.ophthal_os.field_view }}</b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>{{ generalist.ophthal_od ? generalist.ophthal_od.fundus : '-'}}</b-td>
                                                    <b-th class="text-center bg-primary-100">Fundus</b-th>
                                                    <b-td>{{ generalist.ophthal_os ? generalist.ophthal_os.fundus : '-'}}</b-td>
                                                </b-tr>
                                            </b-tbody>
                                        </b-table-simple>
                                    </template>

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

                            <section v-if="result.doctor_in_charge_id" class="mt-32">
                                <ModernLabel :label="`${ result.employee_doctor_name } (Dokter DPJP)`" />
                                <div class="col mt-12">
                                    <section>
                                        <div class="font-weight-bold">LABORATORIUM {{ IGD_doctor.laboratory_list.length != 0 ? '' : ': -' }}</div>
                                        <template v-if="IGD_doctor.laboratory_list">
                                            <div v-for="(item, i) in IGD_doctor.laboratory_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                <div>No Order : {{ item.no_request ? item.no_request : '-' }}</div>
                                                <div v-if="item.laboratory_request_item">
                                                    <li v-for="(list, j) in item.laboratory_request_item" :key="j">{{ list.costing_name }}</li>
                                                </div>
                                            </div>
                                        </template>
                                    </section>
                                    <section class="mt-20">
                                        <div class="font-weight-bold">RADIOLOGI {{ IGD_doctor.radiology_list.length != 0 ? '' : ': -' }}</div>
                                        <template v-if="IGD_doctor.radiology_list">
                                            <div v-for="(item, i) in IGD_doctor.radiology_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                <div>No Order : {{ item.no_request ? item.no_request : '-' }}</div>
                                                <div v-if="item.radiology_request_item">
                                                    <li v-for="(list, j) in item.radiology_request_item" :key="j">{{ list.costing_name }}</li>
                                                </div>
                                            </div>
                                        </template>
                                    </section>
                                    <section class="mt-20">
                                        <div class="font-weight-bold">TINDAKAN (ICD9CM) {{ IGD_doctor.soap_p_list ? '' : ': -' }}</div>
                                        <template v-if="IGD_doctor.soap_p_list">
                                            <div class="mb-12">
                                                <div>
                                                    <div>Tindakan : {{ IGD_doctor.soap_p_list.icd9cm ? '' : '-' }}</div>
                                                    <div v-if="IGD_doctor.soap_p_list.icd9cm">
                                                        <li v-for="(list, j) in IGD_doctor.soap_p_list.icd9cm" :key="j">{{ list.code }} {{ list.name }}</li>
                                                    </div>
                                                </div>
                                                <div class="d-flex mt-1">
                                                    <div class="mr-2">Catatan: </div>
                                                    <div v-html="parsedBody(IGD_doctor.soap_p_list.therapy_plan ? IGD_doctor.soap_p_list.therapy_plan : '-')"></div>
                                                </div>
                                            </div>
                                        </template>
                                    </section>
                                    <section class="mt-20">
                                        <div class="font-weight-bold">PROSEDUR {{ IGD_doctor.transaction_list.length != 0 ? '' : ': -' }}</div>
                                        <div v-if="IGD_doctor.transaction_list">
                                            <li v-for="(item, i) in IGD_doctor.transaction_list" :key="i">{{ item.costing_name }}, jumlah: {{ item.var_2 }}</li>
                                        </div>
                                    </section>
                                    <section class="mt-8">
                                        <div class="font-weight-bold">RESEP {{ IGD_doctor.prescription_list.length != 0 ? '' : ': -' }}</div>
                                        <template v-if="IGD_doctor.prescription_list">
                                            <div v-for="(item, i) in IGD_doctor.prescription_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                <div>No Order : {{ item.no_order? item.no_order: '-' }}</div>
                                                <div v-if="item.form_prescription">
                                                    <li v-for="(list, j) in item.form_prescription" :key="j">{{ list.pharmacy_catalog_name }}, {{ list.used }}</li>
                                                </div>
                                            </div>
                                        </template>
                                    </section>
                                </div>
                            </section>

                            <section v-if="result.doctor_in_charge_id_1" class="mt-32">
                                <ModernLabel :label="`${ result.doctor_in_charge_name_1 } (Dokter Spesialis)`" />
                                <div class="col mt-12">
                                    <section>
                                        <div class="font-weight-bold">LABORATORIUM {{ FIRST_doctor.laboratory_list.length != 0 ? '' : ': -' }}</div>
                                        <template v-if="FIRST_doctor.laboratory_list">
                                            <div v-for="(item, i) in FIRST_doctor.laboratory_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                <div>No Order : {{ item.no_request ? item.no_request : '-' }}</div>
                                                <div v-if="item.laboratory_request_item">
                                                    <li v-for="(list, j) in item.laboratory_request_item" :key="j">{{ list.costing_name }}</li>
                                                </div>
                                            </div>
                                        </template>
                                    </section>
                                    <section class="mt-20">
                                        <div class="font-weight-bold">RADIOLOGI {{ FIRST_doctor.radiology_list.length != 0 ? '' : ': -' }}</div>
                                        <template v-if="FIRST_doctor.radiology_list">
                                            <div v-for="(item, i) in FIRST_doctor.radiology_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                <div>No Order : {{ item.no_request ? item.no_request : '-' }}</div>
                                                <div v-if="item.radiology_request_item">
                                                    <li v-for="(list, j) in item.radiology_request_item" :key="j">{{ list.costing_name }}</li>
                                                </div>
                                            </div>
                                        </template>
                                    </section>
                                    <section class="mt-20">
                                        <div class="font-weight-bold">TINDAKAN (ICD9CM) {{ FIRST_doctor.soap_p_list ? '' : ': -' }}</div>
                                        <template v-if="FIRST_doctor.soap_p_list">
                                            <div class="mb-12">
                                                <div>
                                                    <div>Tindakan : {{ FIRST_doctor.soap_p_list.icd9cm ? '' : '-' }}</div>
                                                    <div v-if="FIRST_doctor.soap_p_list.icd9cm">
                                                        <li v-for="(list, j) in FIRST_doctor.soap_p_list.icd9cm" :key="j">{{ list.code }} {{ list.name }}</li>
                                                    </div>
                                                </div>
                                                <div class="d-flex mt-1">
                                                    <div class="mr-2">Catatan: </div>
                                                    <div v-html="parsedBody(FIRST_doctor.soap_p_list.therapy_plan ? FIRST_doctor.soap_p_list.therapy_plan : '-')"></div>
                                                </div>
                                            </div>
                                        </template>
                                    </section>
                                    <section class="mt-20">
                                        <div class="font-weight-bold">PROSEDUR {{ FIRST_doctor.transaction_list.length != 0 ? '' : ': -' }}</div>
                                        <div v-if="FIRST_doctor.transaction_list">
                                            <li v-for="(item, i) in FIRST_doctor.transaction_list" :key="i">{{ item.costing_name }}, jumlah: {{ item.var_2 }}</li>
                                        </div>
                                    </section>
                                    <section class="mt-20">
                                        <div class="font-weight-bold">RESEP {{ FIRST_doctor.prescription_list.length != 0 ? '' : ': -' }}</div>
                                        <template v-if="FIRST_doctor.prescription_list">
                                            <div v-for="(item, i) in FIRST_doctor.prescription_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                <div>No Order: {{ item.no_order? item.no_order: '-' }}</div>
                                                <div v-if="item.form_prescription">
                                                    <li v-for="(list, j) in item.form_prescription" :key="j">{{ list.pharmacy_catalog_name }}, {{ list.used }}</li>
                                                </div>
                                            </div>
                                        </template>
                                    </section>
                                </div>
                            </section>

                            <section v-if="result.doctor_in_charge_id_2" class="mt-32">
                                <ModernLabel :label="`${ result.doctor_in_charge_name_2 } (Dokter Spesialis)`" />
                                <div class="col mt-12">
                                    <section class="mt-8">
                                        <div class="font-weight-bold">LABORATORIUM {{ SECOND_doctor.laboratory_list.length != 0 ? '' : ': -' }}</div>
                                        <template v-if="SECOND_doctor.laboratory_list">
                                            <div v-for="(item, i) in SECOND_doctor.laboratory_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                <div>No Order : {{ item.no_request ? item.no_request : '-' }}</div>
                                                <div v-if="item.laboratory_request_item">
                                                    <li v-for="(list, j) in item.laboratory_request_item" :key="j">{{ list.costing_name }}</li>
                                                </div>
                                            </div>
                                        </template>
                                    </section>
                                    <section class="mt-20">
                                        <div class="font-weight-bold">RADIOLOGI {{ SECOND_doctor.radiology_list.length != 0 ? '' : ': -' }}</div>
                                        <template v-if="SECOND_doctor.radiology_list">
                                            <div v-for="(item, i) in SECOND_doctor.radiology_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                <div>No Order : {{ item.no_request ? item.no_request : '-' }}</div>
                                                <div v-if="item.radiology_request_item">
                                                    <li v-for="(list, j) in item.radiology_request_item" :key="j">{{ list.costing_name }}</li>
                                                </div>
                                            </div>
                                        </template>
                                    </section>
                                    <section class="mt-20">
                                        <div class="font-weight-bold">TINDAKAN (ICD9CM) {{ SECOND_doctor.soap_p_list ? '' : ': -' }}</div>
                                        <template v-if="SECOND_doctor.soap_p_list">
                                            <div class="mb-12">
                                                <div>
                                                    <div>Tindakan : {{ SECOND_doctor.soap_p_list.icd9cm ? '' : '-' }}</div>
                                                    <div v-if="SECOND_doctor.soap_p_list.icd9cm">
                                                        <li v-for="(list, j) in SECOND_doctor.soap_p_list.icd9cm" :key="j">{{ list.code }} {{ list.name }}</li>
                                                    </div>
                                                </div>
                                                <div class="d-flex mt-1">
                                                    <div class="mr-2">Catatan: </div>
                                                    <div v-html="parsedBody(SECOND_doctor.soap_p_list.therapy_plan ? SECOND_doctor.soap_p_list.therapy_plan : '-')"></div>
                                                </div>
                                            </div>
                                        </template>
                                    </section>
                                    <section class="mt-20">
                                        <div class="font-weight-bold">PROSEDUR {{ SECOND_doctor.transaction_list.length != 0 ? '' : ': -' }}</div>
                                        <div v-if="SECOND_doctor.transaction_list">
                                            <li v-for="(item, i) in SECOND_doctor.transaction_list" :key="i">{{ item.costing_name }}, jumlah: {{ item.var_2 }}</li>
                                        </div>
                                    </section>
                                    <section class="mt-20">
                                        <div class="font-weight-bold">RESEP {{ SECOND_doctor.prescription_list.length != 0 ? '' : ': -' }}</div>
                                        <template v-if="SECOND_doctor.prescription_list">
                                            <div v-for="(item, i) in SECOND_doctor.prescription_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                <div>No Order: {{ item.no_order? item.no_order: '-' }}</div>
                                                <div v-if="item.form_prescription">
                                                    <li v-for="(list, j) in item.form_prescription" :key="j">{{ list.pharmacy_catalog_name }}, {{ list.used }}</li>
                                                </div>
                                            </div>
                                        </template>
                                    </section>
                                </div>
                            </section>

                            <section v-if="result.doctor_in_charge_id_3" class="mt-32">
                                <ModernLabel :label="`${ result.doctor_in_charge_name_3 } (Dokter Spesialis)`" />
                                <div class="col mt-12">
                                    <section class="mt-8">
                                        <div class="font-weight-bold">LABORATORIUM {{ THIRD_doctor.laboratory_list.length != 0 ? '' : ': -' }}</div>
                                        <template v-if="THIRD_doctor.laboratory_list">
                                            <div v-for="(item, i) in THIRD_doctor.laboratory_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                <div>No Order : {{ item.no_request ? item.no_request : '-' }}</div>
                                                <div v-if="item.laboratory_request_item">
                                                    <li v-for="(list, j) in item.laboratory_request_item" :key="j">{{ list.costing_name }}</li>
                                                </div>
                                            </div>
                                        </template>
                                    </section>
                                    <section class="mt-20">
                                        <div class="font-weight-bold">RADIOLOGI {{ THIRD_doctor.radiology_list.length != 0 ? '' : ': -' }}</div>
                                        <template v-if="THIRD_doctor.radiology_list">
                                            <div v-for="(item, i) in THIRD_doctor.radiology_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                <div>No Order : {{ item.no_request ? item.no_request : '-' }}</div>
                                                <div v-if="item.radiology_request_item">
                                                    <li v-for="(list, j) in item.radiology_request_item" :key="j">{{ list.costing_name }}</li>
                                                </div>
                                            </div>
                                        </template>
                                    </section>
                                    <section class="mt-20">
                                        <div class="font-weight-bold">TINDAKAN (ICD9CM) {{ THIRD_doctor.soap_p_list ? '' : ': -' }}</div>
                                        <template v-if="THIRD_doctor.soap_p_list">
                                            <div class="mb-12">
                                                <div>
                                                    <div>Tindakan : {{ THIRD_doctor.soap_p_list.icd9cm ? '' : '-' }}</div>
                                                    <div v-if="THIRD_doctor.soap_p_list.icd9cm">
                                                        <li v-for="(list, j) in THIRD_doctor.soap_p_list.icd9cm" :key="j">{{ list.code }} {{ list.name }}</li>
                                                    </div>
                                                </div>
                                                <div class="d-flex mt-1">
                                                    <div class="mr-2">Catatan: </div>
                                                    <div v-html="parsedBody(THIRD_doctor.soap_p_list.therapy_plan ? THIRD_doctor.soap_p_list.therapy_plan : '-')"></div>
                                                </div>
                                            </div>
                                        </template>
                                    </section>
                                    <section class="mt-20">
                                        <div class="font-weight-bold">PROSEDUR {{ THIRD_doctor.transaction_list.length != 0 ? '' : ': -' }}</div>
                                        <div v-if="THIRD_doctor.transaction_list">
                                            <li v-for="(item, i) in THIRD_doctor.transaction_list" :key="i">{{ item.costing_name }}, jumlah: {{ item.var_2 }}</li>
                                        </div>
                                    </section>
                                    <section class="mt-20">
                                        <div class="font-weight-bold">RESEP {{ THIRD_doctor.prescription_list.length != 0 ? '' : ': -' }}</div>
                                        <template v-if="THIRD_doctor.prescription_list">
                                            <div v-for="(item, i) in THIRD_doctor.prescription_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                <div>No Order: {{ item.no_order? item.no_order: '-' }}</div>
                                                <div v-if="item.form_prescription">
                                                    <li v-for="(list, j) in item.form_prescription" :key="j">{{ list.pharmacy_catalog_name }}, {{ list.used }}</li>
                                                </div>
                                            </div>
                                        </template>
                                    </section>
                                </div>
                            </section>

                            <section v-if="result.doctor_in_charge_id_4" class="mt-32">
                                <ModernLabel :label="`${ result.doctor_in_charge_name_4 } (Dokter Spesialis)`" />
                                <div class="col mt-12">
                                    <section class="mt-8">
                                        <div class="font-weight-bold">LABORATORIUM {{ FOURTH_doctor.laboratory_list.length != 0 ? '' : ': -' }}</div>
                                        <template v-if="FOURTH_doctor.laboratory_list">
                                            <div v-for="(item, i) in FOURTH_doctor.laboratory_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                <div>No Order : {{ item.no_request ? item.no_request : '-' }}</div>
                                                <div v-if="item.laboratory_request_item">
                                                    <li v-for="(list, j) in item.laboratory_request_item" :key="j">{{ list.costing_name }}</li>
                                                </div>
                                            </div>
                                        </template>
                                    </section>
                                    <section class="mt-20">
                                        <div class="font-weight-bold">RADIOLOGI {{ FOURTH_doctor.radiology_list.length != 0 ? '' : ': -' }}</div>
                                        <template v-if="FOURTH_doctor.radiology_list">
                                            <div v-for="(item, i) in FOURTH_doctor.radiology_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                <div>No Order : {{ item.no_request ? item.no_request : '-' }}</div>
                                                <div v-if="item.radiology_request_item">
                                                    <li v-for="(list, j) in item.radiology_request_item" :key="j">{{ list.costing_name }}</li>
                                                </div>
                                            </div>
                                        </template>
                                    </section>
                                    <section class="mt-20">
                                        <div class="font-weight-bold">TINDAKAN (ICD9CM) {{ FOURTH_doctor.soap_p_list ? '' : ': -' }}</div>
                                        <template v-if="FOURTH_doctor.soap_p_list">
                                            <div class="mb-12">
                                                <div>
                                                    <div>Tindakan : {{ FOURTH_doctor.soap_p_list.icd9cm ? '' : '-' }}</div>
                                                    <div v-if="FOURTH_doctor.soap_p_list.icd9cm">
                                                        <li v-for="(list, j) in FOURTH_doctor.soap_p_list.icd9cm" :key="j">{{ list.code }} {{ list.name }}</li>
                                                    </div>
                                                </div>
                                                <div class="d-flex mt-1">
                                                    <div class="mr-2">Catatan: </div>
                                                    <div v-html="parsedBody(FOURTH_doctor.soap_p_list.therapy_plan ? FOURTH_doctor.soap_p_list.therapy_plan : '-')"></div>
                                                </div>
                                            </div>
                                        </template>
                                    </section>
                                    <section class="mt-20">
                                        <div class="font-weight-bold">PROSEDUR {{ FOURTH_doctor.transaction_list.length != 0 ? '' : ': -' }}</div>
                                        <div v-if="FOURTH_doctor.transaction_list">
                                            <li v-for="(item, i) in FOURTH_doctor.transaction_list" :key="i">{{ item.costing_name }}, jumlah: {{ item.var_2 }}</li>
                                        </div>
                                    </section>
                                    <section class="mt-20">
                                        <div class="font-weight-bold">RESEP {{ FOURTH_doctor.prescription_list.length != 0 ? '' : ': -' }}</div>
                                        <template v-if="FOURTH_doctor.prescription_list">
                                            <div v-for="(item, i) in FOURTH_doctor.prescription_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                <div>No Order: {{ item.no_order? item.no_order: '-' }}</div>
                                                <div v-if="item.form_prescription">
                                                    <li v-for="(list, j) in item.form_prescription" :key="j">{{ list.pharmacy_catalog_name }}, {{ list.used }}</li>
                                                </div>
                                            </div>
                                        </template>
                                    </section>
                                </div>
                            </section>

                            <section v-if="result.doctor_in_charge_id_5" class="mt-32">
                                <ModernLabel :label="`${ result.doctor_in_charge_name_5 } (Dokter Spesialis)`" />
                                <div class="col mt-12">
                                    <section class="mt-8">
                                        <div class="font-weight-bold">LABORATORIUM {{ FIFTH_doctor.laboratory_list.length != 0 ? '' : ': -' }}</div>
                                        <template v-if="FIFTH_doctor.laboratory_list">
                                            <div v-for="(item, i) in FIFTH_doctor.laboratory_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                <div>No Order : {{ item.no_request ? item.no_request : '-' }}</div>
                                                <div v-if="item.laboratory_request_item">
                                                    <li v-for="(list, j) in item.laboratory_request_item" :key="j">{{ list.costing_name }}</li>
                                                </div>
                                            </div>
                                        </template>
                                    </section>
                                    <section class="mt-20">
                                        <div class="font-weight-bold">RADIOLOGI {{ FIFTH_doctor.radiology_list.length != 0 ? '' : ': -' }}</div>
                                        <template v-if="FIFTH_doctor.radiology_list">
                                            <div v-for="(item, i) in FIFTH_doctor.radiology_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                <div>No Order : {{ item.no_request ? item.no_request : '-' }}</div>
                                                <div v-if="item.radiology_request_item">
                                                    <li v-for="(list, j) in item.radiology_request_item" :key="j">{{ list.costing_name }}</li>
                                                </div>
                                            </div>
                                        </template>
                                    </section>
                                    <section class="mt-20">
                                        <div class="font-weight-bold">TINDAKAN (ICD9CM) {{ FIFTH_doctor.soap_p_list ? '' : ': -' }}</div>
                                        <template v-if="FIFTH_doctor.soap_p_list">
                                            <div class="mb-12">
                                                <div>
                                                    <div>Tindakan : {{ FIFTH_doctor.soap_p_list.icd9cm ? '' : '-' }}</div>
                                                    <div v-if="FIFTH_doctor.soap_p_list.icd9cm">
                                                        <li v-for="(list, j) in FIFTH_doctor.soap_p_list.icd9cm" :key="j">{{ list.code }} {{ list.name }}</li>
                                                    </div>
                                                </div>
                                                <div class="d-flex mt-1">
                                                    <div class="mr-2">Catatan: </div>
                                                    <div v-html="parsedBody(FIFTH_doctor.soap_p_list.therapy_plan ? FIFTH_doctor.soap_p_list.therapy_plan : '-')"></div>
                                                </div>
                                            </div>
                                        </template>
                                    </section>
                                    <section class="mt-20">
                                        <div class="font-weight-bold">PROSEDUR {{ FIFTH_doctor.transaction_list.length != 0 ? '' : ': -' }}</div>
                                        <div v-if="FIFTH_doctor.transaction_list">
                                            <li v-for="(item, i) in FIFTH_doctor.transaction_list" :key="i">{{ item.costing_name }}, jumlah: {{ item.var_2 }}</li>
                                        </div>
                                    </section>
                                    <section class="mt-20">
                                        <div class="font-weight-bold">RESEP {{ FIFTH_doctor.prescription_list.length != 0 ? '' : ': -' }}</div>
                                        <template v-if="FIFTH_doctor.prescription_list">
                                            <div v-for="(item, i) in FIFTH_doctor.prescription_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                <div>No Order: {{ item.no_order? item.no_order: '-' }}</div>
                                                <div v-if="item.form_prescription">
                                                    <li v-for="(list, j) in item.form_prescription" :key="j">{{ list.pharmacy_catalog_name }}, {{ list.used }}</li>
                                                </div>
                                            </div>
                                        </template>
                                    </section>
                                </div>
                            </section>
                        </div>
                    </section>

                    <div class="d-flex justify-content-end w-100 mt-20">
                        <div class="col-5 text-center">
                            <div :class="result.employee_signature ? '' : 'mb-64'" >DPJP</div>
                            <template v-if="result.employee_signature">
                                <b-img :src="urlbe+result.employee_signature" style="width: 120px;"/>
                            </template>
                            <div>( {{ this.result.employee_doctor_name }} )</div>
                        </div>
                    </div>

                </div>
            </div>
        </b-container>
    </div>
</template>
<script>
import dateMixin from '@/mixins/dateMixin';
import optionMixin from '@/mixins/optionMixin';
import moment from 'moment';
import { mapState } from 'vuex';

export default {
    mixins: [optionMixin, dateMixin],
    data() {
        return {
            urlbe:process.env.VUE_APP_IMAGE,
            active: {
                ophtal: false,
            },
            result: {},
            patient: {},
            generalist: {
                head: '',
                head_hair_color: '',
                head_hair_condition: '',
                eye_conjunctive: '',
                eye_sclera: '',
                eye_pupil: '',
                eye_light_reflex: '',
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

            fieldsTable: [
                { key: 'date_added', label: 'Tanggal/Jam', thClass:'text-center align-middle' },
                { key: 'employee_doctor_name', label: 'PROFESI PEMBERI ASUHAN (PPA)', thClass:'text-center align-middle' },
                { key: 'check_result', label: 'Hasil Pemeriksaan, Analisis, Rencana Penatalaksanaan Pasien', thClass:'text-center align-middle' },
                { key: 'health_facility_name', label: 'Instruksi Tenaga Kesehatan Termasuk Prosedur Pasca Bedah', thClass:'text-center', thStyle:'width: 20%' },
                { key: 'sign', label: 'Nama, Paraf, dan Verifikasi PPA', thClass:'text-center align-middle', thStyle:'width: 15%' },
            ],
            result_arry: [],
            IGD_doctor: {
                soap_p_list: [],
                transaction_list: [],
                laboratory_list: [],
                radiology_list: [],
                prescription_list: [],
            },

            FIRST_doctor: {
                soap_p_list: [],
                transaction_list: [],
                laboratory_list: [],
                radiology_list: [],
                prescription_list: [],
            },

            SECOND_doctor: {
                soap_p_list: [],
                transaction_list: [],
                laboratory_list: [],
                radiology_list: [],
                prescription_list: [],
            },

            THIRD_doctor: {
                soap_p_list: [],
                transaction_list: [],
                laboratory_list: [],
                radiology_list: [],
                prescription_list: [],
            },

            FOURTH_doctor: {
                soap_p_list: [],
                transaction_list: [],
                laboratory_list: [],
                radiology_list: [],
                prescription_list: [],
            },

            FIFTH_doctor: {
                soap_p_list: [],
                transaction_list: [],
                laboratory_list: [],
                radiology_list: [],
                prescription_list: [],
            },

            tableData: {
                laboratory: [
                    { key: 'no', label: 'No', thStyle: 'width: 60px' },
                    { key: 'no_request', label: 'Nomor Permintaan', thStyle: 'width: 200px' },
                    { key: 'laboratory_request_item', label: 'Permintaan' },
                ],
                radiology: [
                    { key: 'no', label: 'No', thStyle: 'width: 60px' },
                    { key: 'no_request', label: 'Nomor Permintaan', thStyle: 'width: 200px' },
                    { key: 'date_request', label: 'Tanggal Permintaan', thStyle: 'width: 200px' },
                    { key: 'radiology_request_item', label: 'Permintaan' },
                    { key: 'action', label: 'Aksi', thStyle: 'width: 140px' },
                ],
            },
            triageData: {},
            nurseData: {},
            emergencyData: {},

            // OptionsOxygen: [
            //     { value: 2, title: 'Tidak' },
            //     { value: 1, title: 'Ya' },
            // ],
            // OptionsAware: [
            //     { value: 3, title: 'Tidak' },
            //     { value: 0, title: 'Ya' },
            // ],

            // OptionGcsEye: [
            //     { value: 1, title: 'Mata tidak terbuka sama sekali' },
            //     { value: 2, title: 'Mata hanya terbuka sebagai respons terhadap tekanan' },
            //     { value: 3, title: 'Hanya membuka mata saat diminta, dan tetap tertutup jika tidak' },
            //     { value: 4, title: 'Dapat membuka mata secara mandiri' },
            // ],
            // OptionGcsVerbal: [
            //     { value: 1, title: 'Tidak bisa berbicara atau membuat suara sama sekali' },
            //     { value: 2, title: 'Hanya bisa membuat suara, tetapi tidak berbicara' },
            //     { value: 3, title: 'Bisa berbicara, tetapi respons tidak masuk akal' },
            //     { value: 4, title: 'Mengalami kebingungan, namun masih bisa menjawab meski kurang paham situasi' },
            //     { value: 5, title: 'Punya orientasi baik karena mampu menjawab siapa, di mana, serta tanggal atau tahun' },
            // ],
            // OptionGcsMotoric: [
            //     { value: 1, title: 'Tidak merespons atau bergerak sama sekali' },
            //     { value: 2, title: 'Otot meregang (mengulur ke luar) sebagai respons terhadap tekanan' },
            //     { value: 3, title: 'Otot menegang (menarik ke dalam) saat merasakan tekanan' },
            //     { value: 4, title: 'Bergerak sebagai refleks terhadap tekanan' },
            //     { value: 5, title: 'Bergerak menjauh dari tekanan yang dirasakan secara sengaja' },
            //     { value: 6, title: 'Mampu mengikuti instruksi kapan dan bagaimana harus bergerak' },
            // ],
            // OptionGcsPupil: [
            //     { value: 0, title: 'Kedua pupil bereaksi terhadap cahaya' },
            //     { value: 1, title: 'Satu pupil tidak bereaksi terhadap cahaya' },
            //     { value: 2, title: 'Kedua pupil tidak bereaksi terhadap cahaya' },
            // ],
        }
    },

    computed: {
        ...mapState({
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
            OptionsOftalPalpebraPtosis: state => state.soap.generalis.OptionsOftalPalpebraPtosis,
            OptionsOftalPalpebraLagophtalmus: state => state.soap.generalis.OptionsOftalPalpebraLagophtalmus,
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

        }),

    },

    methods: {
        // getOxygenName(value) {
        //     const result = this.OptionsOxygen.find((item) => item.value == value);
        //     return result ? result.title : '-';
        // },
        // getAwareName(value) {
        //     const result = this.OptionsAware.find((item) => item.value == value);
        //     return result ? result.title : '-';
        // },
        // getGcsEyeName(value) {
        //     const result = this.OptionGcsEye.find((item) => item.value == value);
        //     return result ? result.title : '-';
        // },
        // getGcsVerbalName(value) {
        //     const result = this.OptionGcsVerbal.find((item) => item.value == value);
        //     return result ? result.title : '-';
        // },
        // getGcsMotoricName(value) {
        //     const result = this.OptionGcsMotoric.find((item) => item.value == value);
        //     return result ? result.title : '-';
        // },
        // getGcsPupilName(value) {
        //     const result = this.OptionGcsPupil.find((item) => item.value == value);
        //     return result ? result.title : '-';
        // },
        // saveChangedNurse($event){
        //     localStorage.setItem("nursestudies", JSON.stringify(this.form_nurse))
        // },

        parsedBody(text){
            return String(text || '').replace(/\n/g, "<br>");
        },

        getGeneralist(obj) {
            const objHead = this.OptionsHeadCircumference.find(el => el.value === obj.head)
            const objHairColor = this.OptionsHeadHairColor.find(el => el.value === obj.head_hair_color)
            const objHairCondition = this.OptionsHeadHairCondition.find(el => el.value === obj.head_hair_condition)
            const objEyeConjunctive = this.OptionsEyeConjunctiva.find(el => el.value === obj.eye_conjunctive)
            const objEyeSclera = this.OptionsEyeSclera.find(el => el.value === obj.eye_sclera)
            const objEyePupil = this.OptionsEyePupil.find(el => el.value === obj.eye_pupil)
            const objEyeReflect = this.OptionsEyeReflect.find(el => el.value === obj.eye_light_reflex)
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
                objOdOftalVisus = this.OptionsOftalVisus.find(el => el.value === obj.ophthal_od.visus)
            }
            if (obj.ophthal_od && obj.ophthal_od.tio_digital) {
                objOdOftalTIO = this.OptionsOftalTIO.find(el => el.value === obj.ophthal_od.tio_digital)
            }
            if (obj.ophthal_od && obj.ophthal_od.eye_position) {
                objOdOftalEyePosition = this.OptionsOftalEyePosition.find(el => el.value === obj.ophthal_od.eye_position)
            }
            if (obj.ophthal_od && obj.ophthal_od.eye_movement) {
                objOdOftalMovement = this.OptionsOftalMovement.find(el => el.value === obj.ophthal_od.eye_movement)
            }
            if (obj.ophthal_od && obj.ophthal_od.super_silia) {
                objOdOftalSuperSilia = this.OptionsOftalSuperSilia.find(el => el.value === obj.ophthal_od.super_silia)
            }
            if (obj.ophthal_od && obj.ophthal_od.palpebra_edema) {
                objOdOftalPalpebraEdema = this.OptionsOftalPalpebraEdema.find(el => el.value === obj.ophthal_od.palpebra_edema)
            }
            if (obj.ophthal_od && obj.ophthal_od.palpebra_ptosis) {
                objOdOftalPalpebraPtosis = this.OptionsOftalPalpebraPtosis.find(el => el.value === obj.ophthal_od.palpebra_ptosis)
            }
            if (obj.ophthal_od && obj.ophthal_od.palpebra_lagophtalmus) {
                objOdOftalPalpebraLagophtalmus = this.OptionsOftalPalpebraLagophtalmus.find(el => el.value === obj.ophthal_od.palpebra_lagophtalmus)
            }
            if (obj.ophthal_od && obj.ophthal_od.palpebra_tumor) {
                objOdOftalPalpebraTumor = this.OptionsOftalPalpebraTumor.find(el => el.value === obj.ophthal_od.palpebra_tumor)
            }
            if (obj.ophthal_od && obj.ophthal_od.apparatus_lakrimalis) {
                objOdOftalApparetus = this.OptionsOftalApparetus.find(el => el.value === obj.ophthal_od.apparatus_lakrimalis)
            }
            if (obj.ophthal_od && obj.ophthal_od.silia) {
                objOdOftalSilia = this.OptionsOftalSilia.find(el => el.value === obj.ophthal_od.silia)
            }
            if (obj.ophthal_od && obj.ophthal_od.conjunctive_hiperemis) {
                objOdOftalConjHiperemis = this.OptionsOftalConjHiperemis.find(el => el.value === obj.ophthal_od.conjunctive_hiperemis)
            }
            if (obj.ophthal_od && obj.ophthal_od.conjunctive_bleed) {
                objOdOftalConjBleed = this.OptionsOftalConjBleed.find(el => el.value === obj.ophthal_od.conjunctive_bleed)
            }
            if (obj.ophthal_od && obj.ophthal_od.cornea) {
                objOdOftalKornea = this.OptionsOftalKornea.find(el => el.value === obj.ophthal_od.cornea)
            }
            if (obj.ophthal_od && obj.ophthal_od.camera_oculi) {
                objOdOftalAnterior = this.OptionsOftalAnterior.find(el => el.value === obj.ophthal_od.camera_oculi)
            }
            if (obj.ophthal_od && obj.ophthal_od.iris_color) {
                objOdOftalIrisColor = this.OptionsOftalIrisColor.find(el => el.value === obj.ophthal_od.iris_color)
            }
            if (obj.ophthal_od && obj.ophthal_od.iris_cripte) {
                objOdOftalIrisKripte = this.OptionsOftalIrisKripte.find(el => el.value === obj.ophthal_od.iris_cripte)
            }
            if (obj.ophthal_od && obj.ophthal_od.pupil_form) {
                objOdOftalPupilForm = this.OptionsOftalPupilForm.find(el => el.value === obj.ophthal_od.pupil_form)
            }
            if (obj.ophthal_od && obj.ophthal_od.pupil_diameter) {
                objOdOftalPupilDiameter = this.OptionsOftalPupilDiameter.find(el => el.value === obj.ophthal_od.pupil_diameter)
            }
            if (obj.ophthal_od && obj.ophthal_od.pupil_direct_light) {
                objOdOftalPupilDirectLight = this.OptionsOftalPupilDirectLight.find(el => el.value === obj.ophthal_od.pupil_direct_light)
            }
            if (obj.ophthal_od && obj.ophthal_od.pupil_indirect_light) {
                objOdOftalPupilIndirectLight = this.OptionsOftalPupilIndirectLight.find(el => el.value === obj.ophthal_od.pupil_indirect_light)
            }
            if (obj.ophthal_od && obj.ophthal_od.lens) {
                objOdOftalLens = this.OptionsOftalLens.find(el => el.value === obj.ophthal_od.lens)
            }
            if (obj.ophthal_od && obj.ophthal_od.shadow_test) {
                objOdOftalShadow = this.OptionsOftalShadow.find(el => el.value === obj.ophthal_od.shadow_test)
            }
            if (obj.ophthal_od && obj.ophthal_od.glandula) {
                objOdOftalGlandula = this.OptionsOftalGlandula.find(el => el.value === obj.ophthal_od.glandula)
            }
            if (obj.ophthal_od && obj.ophthal_od.field_view) {
                objOdOftalLapang = this.OptionsOftalLapang.find(el => el.value === obj.ophthal_od.field_view)
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
                objOsOftalVisus = this.OptionsOftalVisus.find(el => el.value === obj.ophthal_os.visus)
            }
            if (obj.ophthal_os && obj.ophthal_os.tio_digital) {
                objOsOftalTIO = this.OptionsOftalTIO.find(el => el.value === obj.ophthal_os.tio_digital)
            }
            if (obj.ophthal_os && obj.ophthal_os.eye_position) {
                objOsOftalEyePosition = this.OptionsOftalEyePosition.find(el => el.value === obj.ophthal_os.eye_position)
            }
            if (obj.ophthal_os && obj.ophthal_os.eye_movement) {
                objOsOftalMovement = this.OptionsOftalMovement.find(el => el.value === obj.ophthal_os.eye_movement)
            }
            if (obj.ophthal_os && obj.ophthal_os.super_silia) {
                objOsOftalSuperSilia = this.OptionsOftalSuperSilia.find(el => el.value === obj.ophthal_os.super_silia)
            }
            if (obj.ophthal_os && obj.ophthal_os.palpebra_edema) {
                objOsOftalPalpebraEdema = this.OptionsOftalPalpebraEdema.find(el => el.value === obj.ophthal_os.palpebra_edema)
            }
            if (obj.ophthal_os && obj.ophthal_os.palpebra_ptosis) {
                objOsOftalPalpebraPtosis = this.OptionsOftalPalpebraPtosis.find(el => el.value === obj.ophthal_os.palpebra_ptosis)
            }
            if (obj.ophthal_os && obj.ophthal_os.palpebra_lagophtalmus) {
                objOsOftalPalpebraLagophtalmus = this.OptionsOftalPalpebraLagophtalmus.find(el => el.value === obj.ophthal_os.palpebra_lagophtalmus)
            }
            if (obj.ophthal_os && obj.ophthal_os.palpebra_tumor) {
                objOsOftalPalpebraTumor = this.OptionsOftalPalpebraTumor.find(el => el.value === obj.ophthal_os.palpebra_tumor)
            }
            if (obj.ophthal_os && obj.ophthal_os.apparatus_lakrimalis) {
                objOsOftalApparetus = this.OptionsOftalApparetus.find(el => el.value === obj.ophthal_os.apparatus_lakrimalis)
            }
            if (obj.ophthal_os && obj.ophthal_os.silia) {
                objOsOftalSilia = this.OptionsOftalSilia.find(el => el.value === obj.ophthal_os.silia)
            }
            if (obj.ophthal_os && obj.ophthal_os.conjunctive_hiperemis) {
                objOsOftalConjHiperemis = this.OptionsOftalConjHiperemis.find(el => el.value === obj.ophthal_os.conjunctive_hiperemis)
            }
            if (obj.ophthal_os && obj.ophthal_os.conjunctive_bleed) {
                objOsOftalConjBleed = this.OptionsOftalConjBleed.find(el => el.value === obj.ophthal_os.conjunctive_bleed)
            }
            if (obj.ophthal_os && obj.ophthal_os.cornea) {
                objOsOftalKornea = this.OptionsOftalKornea.find(el => el.value === obj.ophthal_os.cornea)
            }
            if (obj.ophthal_os && obj.ophthal_os.camera_oculi) {
                objOsOftalAnterior = this.OptionsOftalAnterior.find(el => el.value === obj.ophthal_os.camera_oculi)
            }
            if (obj.ophthal_os && obj.ophthal_os.iris_color) {
                objOsOftalIrisColor = this.OptionsOftalIrisColor.find(el => el.value === obj.ophthal_os.iris_color)
            }
            if (obj.ophthal_os && obj.ophthal_os.iris_cripte) {
                objOsOftalIrisKripte = this.OptionsOftalIrisKripte.find(el => el.value === obj.ophthal_os.iris_cripte)
            }
            if (obj.ophthal_os && obj.ophthal_os.pupil_form) {
                objOsOftalPupilForm = this.OptionsOftalPupilForm.find(el => el.value === obj.ophthal_os.pupil_form)
            }
            if (obj.ophthal_os && obj.ophthal_os.pupil_diameter) {
                objOsOftalPupilDiameter = this.OptionsOftalPupilDiameter.find(el => el.value === obj.ophthal_os.pupil_diameter)
            }
            if (obj.ophthal_os && obj.ophthal_os.pupil_direct_light) {
                objOsOftalPupilDirectLight = this.OptionsOftalPupilDirectLight.find(el => el.value === obj.ophthal_os.pupil_direct_light)
            }
            if (obj.ophthal_os && obj.ophthal_os.pupil_indirect_light) {
                objOsOftalPupilIndirectLight = this.OptionsOftalPupilIndirectLight.find(el => el.value === obj.ophthal_os.pupil_indirect_light)
            }
            if (obj.ophthal_os && obj.ophthal_os.lens) {
                objOsOftalLens = this.OptionsOftalLens.find(el => el.value === obj.ophthal_os.lens)
            }
            if (obj.ophthal_os && obj.ophthal_os.shadow_test) {
                objOsOftalShadow = this.OptionsOftalShadow.find(el => el.value === obj.ophthal_os.shadow_test)
            }
            if (obj.ophthal_os && obj.ophthal_os.glandula) {
                objOsOftalGlandula = this.OptionsOftalGlandula.find(el => el.value === obj.ophthal_os.glandula)
            }
            if (obj.ophthal_os && obj.ophthal_os.field_view) {
                objOsOftalLapang = this.OptionsOftalLapang.find(el => el.value === obj.ophthal_os.field_view)
            }

            this.generalist.head = objHead ? objHead.title : '-'
            this.generalist.head_hair_color = objHairColor ? objHairColor.title : '-'
            this.generalist.head_hair_condition = objHairCondition ? objHairCondition.title : '-'
            this.generalist.eye_conjunctive = objEyeConjunctive ? objEyeConjunctive.title : '-'
            this.generalist.eye_sclera = objEyeSclera ? objEyeSclera.title : '-'
            this.generalist.eye_pupil = objEyePupil ? objEyePupil.title : '-'
            this.generalist.eye_light_reflex = objEyeReflect ? objEyeReflect.title : '-'
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

        getAirwayIcon(value) {
            if (this.triageData && this.triageData.breathing_path_arry) {
                return this.triageData.breathing_path_arry.includes(value) ? "check-square-fill" : "square-fill";
            } else {
                return "square-fill";
            }
        },
        getAirwayIconVariant(value) {
            if (this.triageData && this.triageData.breathing_path_arry) {
                return this.triageData.breathing_path_arry.includes(value) ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },
        getRespiratoryIcon(value) {
            if (this.triageData && this.triageData.breathing_arry) {
                return this.triageData.breathing_arry.includes(value) ? "check-square-fill" : "square-fill";
            } else {
                return "square-fill";
            }
        },
        getRespiratoryIconVariant(value) {
            if (this.triageData && this.triageData.breathing_arry) {
                return this.triageData.breathing_arry.includes(value) ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },
        getCirculationIcon(value) {
            if (this.triageData && this.triageData.circulation_arry) {
                return this.triageData.circulation_arry.includes(value) ? "check-square-fill" : "square-fill";
            } else {
                return "square-fill";
            }
        },
        getCirculationIconVariant(value) {
            if (this.triageData && this.triageData.circulation_arry) {
                return this.triageData.circulation_arry.includes(value) ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },
        getConsciousnessIcon(value) {
            if (this.triageData && this.triageData.consciousness_arry) {
                return this.triageData.consciousness_arry.includes(value) ? "check-square-fill" : "square-fill";
            } else {
                return "square-fill";
            }
        },
        getConsciousnessIconVariant(value) {
            if (this.triageData && this.triageData.consciousness_arry) {
                return this.triageData.consciousness_arry.includes(value) ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },
        getResourceIcon(value) {
            if (this.triageData && this.triageData.resource_arry) {
                return this.triageData.resource_arry.includes(value) ? "check-square-fill" : "square-fill";
            } else {
                return "square-fill";
            }
        },
        getResourceIconVariant(value) {
            if (this.triageData && this.triageData.resource_arry) {
                return this.triageData.resource_arry.includes(value) ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },
        getSecondResourceIcon(value) {
            if (this.triageData && this.triageData.resuscitation_resource_arry) {
                return this.triageData.resuscitation_resource_arry.includes(value) ? "check-square-fill" : "square-fill";
            } else {
                return "square-fill";
            }
        },
        getSecondResourceIconVariant(value) {
            if (this.triageData && this.triageData.resuscitation_resource_arry) {
                return this.triageData.resuscitation_resource_arry.includes(value) ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },
        getRadioZoneIcon(value) {
            if (this.triageData && this.triageData.age_type_id) {
                return this.triageData.age_type_id == value ? "check-circle-fill" : "circle-fill";
            } else {
                return "circle-fill";
            }
        },
        getRadioZoneIconVariant(value) {
            if (this.triageData && this.triageData.age_type_id) {
                return this.triageData.age_type_id == value ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },
        getPainIcon(value) {
            if (this.triageData && this.triageData && this.triageData.pain_score) {
                return this.triageData.pain_score == value ? "check-circle-fill" : "circle-fill";
            } else {
                return "circle-fill";
            }
        },
        getPainIconVariant(value) {
            if (this.triageData && this.triageData && this.triageData.pain_score) {
                return this.triageData.pain_score == value ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },
        getFallAIcon(value) {
            if (this.emergencyData && this.emergencyData.fall_risk_component_a === 0 || this.emergencyData && this.emergencyData.fall_risk_component_a === 1) {
                return this.emergencyData.fall_risk_component_a == value ? "check-circle-fill" : "circle-fill";
            } else {
                return "circle-fill";
            }
        },
        getFallAIconVariant(value) {
            if (this.emergencyData && this.emergencyData.fall_risk_component_a === 0 || this.emergencyData && this.emergencyData.fall_risk_component_a === 1) {
                return this.emergencyData.fall_risk_component_a == value ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },
        getFallBIcon(value) {
            if (this.emergencyData && this.emergencyData.fall_risk_component_b === 0 || this.emergencyData && this.emergencyData.fall_risk_component_b === 1) {
                return this.emergencyData.fall_risk_component_b == value ? "check-circle-fill" : "circle-fill";
            } else {
                return "circle-fill";
            }
        },
        getFallBIconVariant(value) {
            if (this.emergencyData && this.emergencyData.fall_risk_component_b === 0 || this.emergencyData && this.emergencyData.fall_risk_component_b === 1) {
                return this.emergencyData.fall_risk_component_b == value ? "success" : "neutral-200";
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
        async getData (soap_id) {
            this.$store.dispatch('soap/getBySoapId', soap_id).then((data) => {
                this.result = data[0]
                this.result_arry = data
                // console.log('soap:', this.result)

                if (this.result && this.result.doctor_in_charge_id) {
                    this.getIGDStudies(this.result.id, this.result.doctor_in_charge_id)
                }

                if (this.result && this.result.doctor_in_charge_id_1) {
                    this.getFIRSTStudies(this.result.id, this.result.doctor_in_charge_id_1)
                }

                if (this.result && this.result.doctor_in_charge_id_2) {
                    this.getSECONDStudies(this.result.id, this.result.doctor_in_charge_id_2)
                }

                if (this.result && this.result.doctor_in_charge_id_3) {
                    this.getTHIRDStudies(this.result.id, this.result.doctor_in_charge_id_3)
                }

                if (this.result && this.result.doctor_in_charge_id_4) {
                    this.getFOURTHStudies(this.result.id, this.result.doctor_in_charge_id_4)
                }

                if (this.result && this.result.doctor_in_charge_id_5) {
                    this.getFIFTHStudies(this.result.id, this.result.doctor_in_charge_id_5)
                }

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

                this.$store.dispatch('registration/getById', this.result.registration_destination_id).then(data => {
                    this.patient = data[0]
                    // console.log('patient', this.patient);
                })

                this.$store.dispatch('triage/getAllListV2', { registration_destination_id: this.result.registration_destination_id }).then(data => {
                    this.triageData = data[0]
                    // console.log('this.triageData', this.triageData);
                })

                this.$store.dispatch('nurse/getDetail', { registration_destination_id: this.result.registration_destination_id }).then((data ) => {
                    this.nurseData = data[0]
                    // console.log('this.nurseData', this.nurseData);

                    if (this.nurseData) {
                        this.$store.dispatch('nurse/getEmergencyDetail', { nurse_id: this.nurseData.id }).then((igd) => {
                            this.emergencyData = igd[0]
                            // console.log('this.emergencyData', this.emergencyData);
                        })
                    }

                })
            })
        },

        getIGDStudies(soap_id, doctor_in_charge_id) {
            let param = { soap_id: soap_id, doctor_in_charge_id: doctor_in_charge_id }

            try {
                this.$store.dispatch('soap/getSoapP', param).then((response) => {
                    this.IGD_doctor.soap_p_list = response[0]
                })

                this.$store.dispatch('soap/getTransaction', param).then((response) => {
                    this.IGD_doctor.transaction_list = response
                    // console.log('this.IGD_doctor.transaction_list: ', this.IGD_doctor.transaction_list);
                })

                this.$store.dispatch('laboratory/getOrderDetail', param).then((response) => {
                    this.IGD_doctor.laboratory_list = response
                    // console.log('this.IGD_doctor.laboratory_list: ', this.IGD_doctor.laboratory_list);
                })

                this.$store.dispatch('radiology/getOrderDetail', param).then((response) => {
                    this.IGD_doctor.radiology_list = response
                    // console.log('this.IGD_doctor.radiology_list: ', this.IGD_doctor.radiology_list);
                })

                this.$store.dispatch('prescription/getDetail', param).then((response) => {
                    this.IGD_doctor.prescription_list = response
                    // console.log('this.IGD_doctor.prescription_list: ', this.IGD_doctor.prescription_list);
                })

            } catch (error) {
                console.error('Error at: ', error);
            }
        },

        getFIRSTStudies(soap_id, doctor_in_charge_id) {
            let param = { soap_id: soap_id, doctor_in_charge_id: doctor_in_charge_id }

            try {
                this.$store.dispatch('soap/getSoapP', param).then((response) => {
                    this.FIRST_doctor.soap_p_list = response[0]
                })

                this.$store.dispatch('soap/getTransaction', param).then((response) => {
                    this.FIRST_doctor.transaction_list = response
                    // console.log('this.FIRST_doctor.transaction_list: ', this.FIRST_doctor.transaction_list);
                })

                this.$store.dispatch('laboratory/getOrderDetail', param).then((response) => {
                    this.FIRST_doctor.laboratory_list = response
                    // console.log('this.FIRST_doctor.laboratory_list: ', this.FIRST_doctor.laboratory_list);
                })

                this.$store.dispatch('radiology/getOrderDetail', param).then((response) => {
                    this.FIRST_doctor.radiology_list = response
                    // console.log('this.FIRST_doctor.radiology_list: ', this.FIRST_doctor.radiology_list);
                })

                this.$store.dispatch('prescription/getDetail', param).then((response) => {
                    this.FIRST_doctor.prescription_list = response
                    // console.log('this.FIRST_doctor.prescription_list: ', this.FIRST_doctor.prescription_list);
                })

            } catch (error) {
                console.error('Error at: ', error);
            }
        },

        getSECONDStudies(soap_id, doctor_in_charge_id) {
            let param = { soap_id: soap_id, doctor_in_charge_id: doctor_in_charge_id }

            try {
                this.$store.dispatch('soap/getSoapP', param).then((response) => {
                    this.SECOND_doctor.soap_p_list = response[0]
                    // console.log('this.SECOND_doctor.soap_p_list: ', this.SECOND_doctor.soap_p_list);
                })

                this.$store.dispatch('soap/getTransaction', param).then((response) => {
                    this.SECOND_doctor.transaction_list = response
                    // console.log('this.SECOND_doctor.transaction_list: ', this.SECOND_doctor.transaction_list);
                })

                this.$store.dispatch('laboratory/getOrderDetail', param).then((response) => {
                    this.SECOND_doctor.laboratory_list = response
                    // console.log('this.SECOND_doctor.laboratory_list: ', this.SECOND_doctor.laboratory_list);
                })

                this.$store.dispatch('radiology/getOrderDetail', param).then((response) => {
                    this.SECOND_doctor.radiology_list = response
                    // console.log('this.SECOND_doctor.radiology_list: ', this.SECOND_doctor.radiology_list);
                })

                this.$store.dispatch('prescription/getDetail', param).then((response) => {
                    this.SECOND_doctor.prescription_list = response
                    // console.log('this.SECOND_doctor.prescription_list: ', this.SECOND_doctor.prescription_list);
                })

            } catch (error) {
                console.error('Error at: ', error);
            }
        },

        getTHIRDStudies(soap_id, doctor_in_charge_id) {
            let param = { soap_id: soap_id, doctor_in_charge_id: doctor_in_charge_id }

            try {
                this.$store.dispatch('soap/getSoapP', param).then((response) => {
                    this.THIRD_doctor.soap_p_list = response[0]
                })

                this.$store.dispatch('soap/getTransaction', param).then((response) => {
                    this.THIRD_doctor.transaction_list = response
                })

                this.$store.dispatch('laboratory/getOrderDetail', param).then((response) => {
                    this.THIRD_doctor.laboratory_list = response
                })

                this.$store.dispatch('radiology/getOrderDetail', param).then((response) => {
                    this.THIRD_doctor.radiology_list = response
                })

                this.$store.dispatch('prescription/getDetail', param).then((response) => {
                    this.THIRD_doctor.prescription_list = response
                })

            } catch (error) {
                console.error('Error at: ', error);
            }
        },

        getFOURTHStudies(soap_id, doctor_in_charge_id) {
            let param = { soap_id: soap_id, doctor_in_charge_id: doctor_in_charge_id }

            try {
                this.$store.dispatch('soap/getSoapP', param).then((response) => {
                    this.FOURTH_doctor.soap_p_list = response[0]
                })

                this.$store.dispatch('soap/getTransaction', param).then((response) => {
                    this.FOURTH_doctor.transaction_list = response
                })

                this.$store.dispatch('laboratory/getOrderDetail', param).then((response) => {
                    this.FOURTH_doctor.laboratory_list = response
                })

                this.$store.dispatch('radiology/getOrderDetail', param).then((response) => {
                    this.FOURTH_doctor.radiology_list = response
                })

                this.$store.dispatch('prescription/getDetail', param).then((response) => {
                    this.FOURTH_doctor.prescription_list = response
                })

            } catch (error) {
                console.error('Error at: ', error);
            }
        },

        getFIFTHStudies(soap_id, doctor_in_charge_id) {
            let param = { soap_id: soap_id, doctor_in_charge_id: doctor_in_charge_id }

            try {
                this.$store.dispatch('soap/getSoapP', param).then((response) => {
                    this.FIFTH_doctor.soap_p_list = response[0]
                })

                this.$store.dispatch('soap/getTransaction', param).then((response) => {
                    this.FIFTH_doctor.transaction_list = response
                })

                this.$store.dispatch('laboratory/getOrderDetail', param).then((response) => {
                    this.FIFTH_doctor.laboratory_list = response
                })

                this.$store.dispatch('radiology/getOrderDetail', param).then((response) => {
                    this.FIFTH_doctor.radiology_list = response
                })

                this.$store.dispatch('prescription/getDetail', param).then((response) => {
                    this.FIFTH_doctor.prescription_list = response
                })

            } catch (error) {
                console.error('Error at: ', error);
            }
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
.custom {
    border-bottom: 1px solid black;
}
td {
    padding: 2px 0;
}

p {
    margin: 0;
    padding: 0;
}

.lh-110 {
    line-height: 110%;
}
.main-content {
    margin-top: 32px;
    padding-bottom: 32px;
}

.canvas-wrapper {
    display: flex;
    justify-content: center;
    padding-bottom: 40px;
}
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
</style>
