<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: this.regData.health_facility_item_name },
                { text: 'Triase' },
                { text: this.regData.name }
            ]"
        >
            <section class="">
                <patientMenu selectedMenu="menu21" />
                <PatientBanner />
                <div class="d-flex mt-8">
                    <b-button pill @click="goToTab('LIST')" :variant="active.tab == 'LIST' ? 'primary' : 'outline-primary'" size="sm" class="px-24">List</b-button>
                    <b-button pill @click="goToTab('ADD')" :variant="active.tab == 'ADD' ? 'primary' : 'outline-primary'" size="sm" class="px-24 ml-2">Tambah Triase</b-button>
                </div>

                <section v-if="active.tab == 'LIST'" class="mt-16">
                    <b-table :fields="tableFields" :items="triageData" bordered >
                        <template #cell(no)="row">
                            <div class="text-center">
                                {{ row.index+1 }}
                            </div>
                        </template>
                        <template #cell(action)="row">
                            <div class="d-flex">
                                <b-button @click="onEditform(row.item)" size="sm" variant="warning">
                                    <feather-icon icon="EditIcon" class="mr-1"/> Edit
                                </b-button>
                                <b-button @click="deleteTriage(row.item)" size="sm" variant="danger" class="ml-2">
                                    <feather-icon icon="TrashIcon" class="mr-1"/> Hapus
                                </b-button>
                            </div>
                        </template>
                    </b-table>
                </section>

                <div v-if="active.tab == 'ADD'" class="card mt-16 shadow-sm">
                    <section class="card-body">
                        <section class="">
                            <ModernLabel label="Triase" />
                            <div class="row">
                                <div class="col-lg-3 mt-12">
                                    <label class="c-form-label">Rujukan</label>
                                    <b-form-radio-group v-model="form.referral_type_id" size="sm">
                                        <b-form-radio value="0">Tidak</b-form-radio>
                                        <b-form-radio value="1">Ya, dari</b-form-radio>
                                    </b-form-radio-group>
                                    <b-form-input v-if="form.referral_type_id == 1" v-model="form.referral" class="mt-10"/>
                                </div>
                                <div class="col-lg-3 mt-12">
                                    <label class="c-form-label">Tingkat Kesadaran</label>
                                    <b-form-radio-group v-model="form.level_of_consciousness_type_id" size="sm">
                                        <b-form-radio value="1">Alert</b-form-radio>
                                        <b-form-radio value="2">Verbal</b-form-radio>
                                        <b-form-radio value="3">Painful</b-form-radio>
                                        <b-form-radio value="4">Unresponsive</b-form-radio>
                                    </b-form-radio-group>
                                </div>
                                <div class="col-lg-3 mt-12">
                                    <label class="c-form-label">Status Imunisasi (Khusus untuk bayi/anak)</label>
                                    <b-form-radio-group v-model="form.immunization_status_type_id" size="sm">
                                        <b-form-radio value="0">Tidak Lengkap</b-form-radio>
                                        <b-form-radio value="1">Lengkap</b-form-radio>
                                    </b-form-radio-group>
                                </div>
                                <div class="col-lg-3 mt-12">
                                    <label class="c-form-label">Alergi Obat</label>
                                    <b-form-radio-group v-model="form.drug_allergy_type_id" size="sm">
                                        <b-form-radio value="0">Tidak</b-form-radio>
                                        <b-form-radio value="1">Ya, sebutkan</b-form-radio>
                                    </b-form-radio-group>
                                    <b-form-input v-if="form.drug_allergy_type_id == 1" v-model="form.drug_allergy" class="mt-10"/>
                                </div>
                                <div class="col-lg-3 mt-12">
                                    <label class="c-form-label">Keluhan Utama</label>
                                    <b-form-input v-model="form.main_complaint"/>
                                </div>
                                <div class="col-lg-3 mt-12">
                                    <label class="c-form-label">Macam Kasus</label>
                                    <b-form-radio-group v-model="form.type_of_cases_type_id" size="sm">
                                        <b-form-radio value="1">Visum</b-form-radio>
                                        <b-form-radio value="2">DOA</b-form-radio>
                                        <b-form-radio value="3">Non Trauma</b-form-radio>
                                        <b-form-radio value="4">Trauma</b-form-radio>
                                    </b-form-radio-group>
                                </div>
                                <div v-if="form.type_of_cases_type_id == 4" class="col-lg-3 mt-12">
                                    <label class="c-form-label">Trauma</label>
                                    <b-form-radio-group v-model="form.trauma_type_id" size="sm">
                                        <b-form-radio value="1">KLL</b-form-radio>
                                        <b-form-radio value="2">KDRT</b-form-radio>
                                        <b-form-radio value="3">Kecelakaan Kerja</b-form-radio>
                                        <b-form-radio value="4">Kekerasan Pada Anak</b-form-radio>
                                    </b-form-radio-group>
                                </div>
                                
                            </div>
                        </section>
                        <!-- <section class="mt-40">
                            <ModernLabel label="Tanda-tanda vital" />
                            <div class="row">
                                <div class="col-lg-3 mt-12">
                                    <label class="c-form-label">Systolic</label>
                                    <b-input-group append="mmHg">
                                        <b-form-input v-model="form.systolic" type="number" autocomplete="off" />
                                    </b-input-group>
                                </div>
                                <div class="col-lg-3 mt-12">
                                    <label class="c-form-label">Diastolic</label>
                                    <b-input-group append="mmHg">
                                        <b-form-input v-model="form.diastolic" type="number" autocomplete="off" />
                                    </b-input-group>
                                </div>
                                <div class="col-lg-3 mt-12">
                                    <label class="c-form-label">Frekuensi Nadi</label>
                                    <b-input-group append="x/mnt">
                                        <b-form-input v-model="form.pulse_frequency" type="number" autocomplete="off" />
                                    </b-input-group>
                                </div>
                                <div class="col-lg-3 mt-12">
                                    <label class="c-form-label">Saturasi Nafas</label>
                                    <b-input-group append="x/mnt">
                                        <b-form-input v-model="form.breath_saturation" type="number" autocomplete="off" />
                                    </b-input-group>
                                </div>
                                <div class="col-lg-3 mt-12">
                                    <label class="c-form-label">Saturasi Oksigen</label>
                                    <b-input-group append="%">
                                        <b-form-input v-model="form.oxygen_saturation" type="number" autocomplete="off" />
                                    </b-input-group>
                                </div>
                                <div class="col-lg-3 mt-12">
                                    <label class="c-form-label">Suhu</label>
                                    <b-input-group append="ᵒC">
                                        <b-form-input v-model="form.temperature" type="number" autocomplete="off" />
                                    </b-input-group>
                                </div>
                                <div class="col-lg-3 mt-12">
                                    <label class="c-form-label">Berat Badan</label>
                                    <b-input-group append="kg">
                                        <b-form-input v-model="form.weight" type="number" autocomplete="off" />
                                    </b-input-group>
                                </div>
                                <div class="col-lg-3 mt-12">
                                    <label class="c-form-label">Tinggi Badan</label>
                                    <b-input-group append="cm">
                                        <b-form-input v-model="form.height" type="number" autocomplete="off" />
                                    </b-input-group>
                                </div>
                            </div>
                        </section> -->
                        <section class="mt-40">
                            <ModernLabel label="Skala Nyeri" />
                            <div class="row mt-12">
                                <div class="col-lg-3">
                                    <label class="c-form-label">Area Nyeri</label>
                                    <b-form-input v-model="form.pain_area" autocomplete="off" />
                                </div>
                            </div>
                            <div class="mt-20">
                                <b-form-radio-group v-model="form.pain_score">
                                    <div class="row">
                                        <div class="col text-center">
                                            <b-form-radio id="val0" value="0" class="mr-0"></b-form-radio>
                                            <label for="val0" class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">0</label>
                                            <div class="mt-1">
                                                <img src="@/@core/assets/images/nurse/wong_baker/0.png" width="75px">
                                                <div class="c-form-label mt-10">
                                                    <div>Tidak nyeri</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col text-center">
                                            <b-form-radio value="1" class="mr-0"></b-form-radio>
                                            <label for="val1" class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">1</label>
                                        </div>
                                        <div class="col text-center">
                                            <b-form-radio id="val2" value="2" class="mr-0"></b-form-radio>
                                            <label for="val2" class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">2</label>
                                            <div class="mt-1">
                                                <img src="@/@core/assets/images/nurse/wong_baker/2.png" width="75px">
                                                <div class="c-form-label mt-10">
                                                    <div>Nyeri ringan</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col text-center">
                                            <b-form-radio id="val3" value="3" class="mr-0"></b-form-radio>
                                            <label for="val3" class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">3</label>
                                        </div>
                                        <div class="col text-center">
                                            <b-form-radio id="val4" value="4" class="mr-0"></b-form-radio>
                                            <label for="val4" class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">4</label>
                                            <div class="mt-1">
                                                <img src="@/@core/assets/images/nurse/wong_baker/4.png" width="75px">
                                                <div class="c-form-label mt-10">
                                                    <div>Nyeri yang mengganggu</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col text-center">
                                            <b-form-radio id="val5" value="5" class="mr-0"></b-form-radio>
                                            <label for="val5" class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">5</label>
                                        </div>
                                        <div class="col text-center">
                                            <b-form-radio id="val6" value="6" class="mr-0"></b-form-radio>
                                            <label for="val6" class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">6</label>
                                            <div class="mt-1">
                                                <img src="@/@core/assets/images/nurse/wong_baker/6.png" width="75px">
                                                <div class="c-form-label mt-10">
                                                    <div>Nyeri yang menyusahkan</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col text-center">
                                            <b-form-radio id="val7" value="7" class="mr-0"></b-form-radio>
                                            <label for="val7" class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">7</label>
                                        </div>
                                        <div class="col text-center">
                                            <b-form-radio id="val8" value="8" class="mr-0"></b-form-radio>
                                            <label for="val8" class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">8</label>
                                            <div class="mt-1">
                                                <img src="@/@core/assets/images/nurse/wong_baker/8.png" width="75px">
                                                <div class="c-form-label mt-10">
                                                    <div>Nyeri hebat</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col text-center">
                                            <b-form-radio id="val9" value="9" class="mr-0"></b-form-radio>
                                            <label for="val9" class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">9</label>
                                        </div>
                                        <div class="col text-center">
                                            <b-form-radio id="val10" value="10" class="mr-0"></b-form-radio>
                                            <label for="val10" class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">10</label>
                                            <div class="mt-1">
                                                <img src="@/@core/assets/images/nurse/wong_baker/10.png" width="75px">
                                                <div class="c-form-label mt-10">
                                                    <div>Nyeri sangat hebat</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </b-form-radio-group>
                            </div>
                        </section>

                        <section class="mt-40">
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
                                <b-tbody class="text-regular-12">
                                    <b-tr>
                                        <b-td>
                                            <span class="font-weight-bold">JALAN NAFAS</span>
                                        </b-td>
                                        <b-td>
                                            <b-form-checkbox-group v-model="form.breathing_path_arry" @change="handleTriageResult" stacked size="sm">
                                                <b-form-checkbox value="A1">Ada Sumbatan Jalan Nafas</b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </b-td>
                                        <b-td>
                                            <b-form-checkbox-group v-model="form.breathing_path_arry" @change="handleTriageResult" stacked size="sm">
                                                <b-form-checkbox value="B1">Bebas Atau Ada Ancaman Sumbatan</b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </b-td>
                                        <b-td>
                                            <b-form-checkbox-group v-model="form.breathing_path_arry" @change="handleTriageResult" stacked size="sm">
                                                <b-form-checkbox value="C1">Bebas</b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </b-td>
                                        <b-td>
                                            <b-form-checkbox-group v-model="form.breathing_path_arry" @change="handleTriageResult" stacked size="sm">
                                                <b-form-checkbox value="D1">Bebas</b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </b-td>
                                        <b-td>
                                            <b-form-checkbox-group v-model="form.breathing_path_arry" @change="handleTriageResult" stacked size="sm">
                                                <b-form-checkbox value="E1">Bebas</b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td>
                                            <span class="font-weight-bold">PERNAFASAN</span>
                                        </b-td>
                                        <b-td>
                                            <b-form-checkbox-group v-model="form.breathing_arry" @change="handleTriageResult" stacked size="sm">
                                                <b-form-checkbox value="A1">Henti Nafas/Apnoe</b-form-checkbox>
                                                <b-form-checkbox value="A2">RR &lt; 10x/menit</b-form-checkbox>
                                                <b-form-checkbox value="A3">Sianosis</b-form-checkbox>
                                                <b-form-checkbox value="A4">SPO2 &lt; 90%</b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </b-td>
                                        <b-td>
                                            <b-form-checkbox-group v-model="form.breathing_arry" @change="handleTriageResult" stacked size="sm">
                                                <b-form-checkbox value="B1">RR &ge; 32x/menit</b-form-checkbox>
                                                <b-form-checkbox value="B2">SPO2 &lt; 92%</b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </b-td>
                                        <b-td>
                                            <b-form-checkbox-group v-model="form.breathing_arry" @change="handleTriageResult" stacked size="sm">
                                                <b-form-checkbox value="C1">RR 25-32x/menit</b-form-checkbox>
                                                <b-form-checkbox value="C2">SPO2 92-95%</b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </b-td>
                                        <b-td>
                                            <b-form-checkbox-group v-model="form.breathing_arry" @change="handleTriageResult" stacked size="sm">
                                                <b-form-checkbox value="D1">RR 20-24x/menit</b-form-checkbox>
                                                <b-form-checkbox value="D2">SPO2 96-100%</b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </b-td>
                                        <b-td>
                                            <b-form-checkbox-group v-model="form.breathing_arry" @change="handleTriageResult" stacked size="sm">
                                                <b-form-checkbox value="E1">RR 16-20 x/mnt</b-form-checkbox>
                                                <b-form-checkbox value="E2">SPO2 96-100%</b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td>
                                            <span class="font-weight-bold">SIRKULASI</span>
                                        </b-td>
                                        <b-td>
                                            <b-form-checkbox-group v-model="form.circulation_arry" @change="handleTriageResult" stacked size="sm">
                                                <b-form-checkbox value="A1">Henti Jantung / arrest</b-form-checkbox>
                                                <b-form-checkbox value="A2">Nadi Tidak Teraba</b-form-checkbox>
                                                <b-form-checkbox value="A3">Akral Dingin</b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </b-td>
                                        <b-td>
                                            <b-form-checkbox-group v-model="form.circulation_arry" @change="handleTriageResult" stacked size="sm">
                                                <b-form-checkbox value="B1">Nadi Lemah</b-form-checkbox>
                                                <b-form-checkbox value="B2">HR &lt; 50 x/mnt atau &gt; 150 x/mnt</b-form-checkbox>
                                                <b-form-checkbox value="B3">Pucat, Akral Dingin</b-form-checkbox>
                                                <b-form-checkbox value="B4">CRT &gt; 2 Detik</b-form-checkbox>
                                                <b-form-checkbox value="B5">TD &lt; 100/&lt; 60 mmHg</b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </b-td>
                                        <b-td>
                                            <b-form-checkbox-group v-model="form.circulation_arry" @change="handleTriageResult" stacked size="sm">
                                                <b-form-checkbox value="C1">Nadi Kuat</b-form-checkbox>
                                                <b-form-checkbox value="C2">HR 121-150x/menit</b-form-checkbox>
                                                <b-form-checkbox value="C3">TD &gt; 160/ &gt; 100 mmHg</b-form-checkbox>
                                                <b-form-checkbox value="C4">CRT &lt; 2 Detik</b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </b-td>
                                        <b-td>
                                            <b-form-checkbox-group v-model="form.circulation_arry" @change="handleTriageResult" stacked size="sm">
                                                <b-form-checkbox value="D1">HR 101-120x/menit</b-form-checkbox>
                                                <b-form-checkbox value="D2">TD 121 - 140/80-100 mmHg</b-form-checkbox>
                                                <b-form-checkbox value="D3">CRT &lt; 2 Detik</b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </b-td>
                                        <b-td>
                                            <b-form-checkbox-group v-model="form.circulation_arry" @change="handleTriageResult" stacked size="sm">
                                                <b-form-checkbox value="E1">HR 80-100x/menit</b-form-checkbox>
                                                <b-form-checkbox value="E2">TD 120/80 mmHg</b-form-checkbox>
                                                <b-form-checkbox value="E3">CRT &lt; 2 Detik</b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td>
                                            <span class="font-weight-bold">KESADARAN</span>
                                        </b-td>
                                        <b-td>
                                            <b-form-checkbox-group v-model="form.consciousness_arry" @change="handleTriageResult" stacked size="sm">
                                                <b-form-checkbox value="A1">GCS &lt; 9</b-form-checkbox>
                                                <b-form-checkbox value="A2">Kejang</b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </b-td>
                                        <b-td>
                                            <b-form-checkbox-group v-model="form.consciousness_arry" @change="handleTriageResult" stacked size="sm">
                                                <b-form-checkbox value="B1">GCS 9-12</b-form-checkbox>
                                                <b-form-checkbox value="B2">Gelisah</b-form-checkbox>
                                                <b-form-checkbox value="B3">Nyeri Dada</b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </b-td>
                                        <b-td>
                                            <b-form-checkbox-group v-model="form.consciousness_arry" @change="handleTriageResult" stacked size="sm">
                                                <b-form-checkbox value="C1">GCS 13-14</b-form-checkbox>
                                                <b-form-checkbox value="C2">Apatis / Somnolen</b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </b-td>
                                        <b-td>
                                            <b-form-checkbox-group v-model="form.consciousness_arry" @change="handleTriageResult" stacked size="sm">
                                                <b-form-checkbox value="D1">GCS 15</b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </b-td>
                                        <b-td>
                                            <b-form-checkbox-group v-model="form.consciousness_arry" @change="handleTriageResult" stacked size="sm">
                                                <b-form-checkbox value="E1">GCS 15</b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td>
                                            <span class="font-weight-bold">SUMBER DAYA</span>
                                        </b-td>
                                        <b-td></b-td>
                                        <b-td></b-td>
                                        <b-td>
                                            <b-form-checkbox-group v-model="form.resource_arry" disabled @change="handleTriageResult" stacked size="sm">
                                                <b-form-checkbox value="C1">Menggunakan &ge; 2 sumber daya</b-form-checkbox>
                                                <!-- <b-form-checkbox value="C1">Menggunakan &gt; 2 sumber daya</b-form-checkbox> -->
                                            </b-form-checkbox-group>
                                        </b-td>
                                        <b-td>
                                            <b-form-checkbox-group v-model="form.resource_arry" disabled @change="handleTriageResult" stacked size="sm">
                                                <b-form-checkbox value="D1">Menggunakan 1 sumber daya</b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </b-td>
                                        <b-td>
                                            <b-form-checkbox-group v-model="form.resource_arry" disabled @change="handleTriageResult" stacked size="sm">
                                                <b-form-checkbox value="E1">Tidak menggunakan sumber daya</b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </b-td>
                                    </b-tr>
                                </b-tbody>
                            </b-table-simple>
                        </section>
                        <section class="mt-24">
                            <!-- <ModernLabel label="Hasil Pemeriksaan Triase: Resusitasi" /> -->
                            <b-form-input v-model="form.triage_result" disabled class="mt-16"></b-form-input>
                            
                            <b-table-simple bordered class="mt-20">
                                <b-tr>
                                    <b-th>Sumber Daya</b-th>
                                    <b-th colspan="5">Danger Zone</b-th>
                                </b-tr>
                                <b-tr>
                                    <b-td rowspan="5">
                                        <b-form-checkbox-group v-model="form.resuscitation_resource_arry" @change="handleTriageResult" stacked size="sm">
                                            <b-form-checkbox value="a">Laboratoriun darah</b-form-checkbox>
                                            <b-form-checkbox value="b">Laboratoriun urine</b-form-checkbox>
                                            <b-form-checkbox value="c">EKG</b-form-checkbox>
                                            <b-form-checkbox value="d">X-Ray</b-form-checkbox>
                                            <b-form-checkbox value="e">Pemasangan infus unt rehidrasi</b-form-checkbox>
                                            <b-form-checkbox value="f">Pemberian ibat IV/IM/Anal</b-form-checkbox>
                                            <b-form-checkbox value="g">Pemasangan kateter</b-form-checkbox>
                                            <b-form-checkbox value="h">Pemasangan NGT</b-form-checkbox>
                                            <b-form-checkbox value="i">Nebulizer</b-form-checkbox>
                                            <b-form-checkbox value="j">Jahit/hecting</b-form-checkbox>
                                            <b-form-checkbox value="k">Konsultasi spesialis</b-form-checkbox>
                                            <b-form-checkbox value="l">Pemasangan monitor</b-form-checkbox>
                                            <b-form-checkbox value="m">Pemasangan ETT</b-form-checkbox>
                                        </b-form-checkbox-group>
                                    </b-td>
                                    <b-th>Usia</b-th>
                                    <b-th>HR</b-th>
                                    <b-th>RR</b-th>
                                    <b-th>S</b-th>
                                    <b-th>SPO2</b-th>
                                </b-tr>
                                <b-tr>
                                    <b-td>
                                        <b-form-radio-group v-model="form.age_type_id" size="sm">
                                            <b-form-radio value="1">&lt; 3 bulan</b-form-radio>
                                        </b-form-radio-group>
                                    </b-td>
                                    <b-td>&gt; 180</b-td>
                                    <b-td>&gt; 50</b-td>
                                    <b-td>&gt; 38ᵒ</b-td>
                                    <b-td>92%</b-td>
                                </b-tr>
                                <b-tr>
                                    <b-td>
                                        <b-form-radio-group v-model="form.age_type_id" size="sm">
                                            <b-form-radio value="2">3 bulan - 3 tahun</b-form-radio>
                                        </b-form-radio-group>
                                    </b-td>
                                    <b-td>&gt; 160</b-td>
                                    <b-td>&gt; 40</b-td>
                                    <b-td>&gt; 38ᵒ</b-td>
                                    <b-td>92%</b-td>
                                </b-tr>
                                <b-tr>
                                    <b-td>
                                        <b-form-radio-group v-model="form.age_type_id" size="sm">
                                            <b-form-radio value="3">3-8 tahun</b-form-radio>
                                        </b-form-radio-group>
                                    </b-td>
                                    <b-td>&gt; 140</b-td>
                                    <b-td>&gt; 30</b-td>
                                    <b-td>&gt; 38ᵒ</b-td>
                                    <b-td>92%</b-td>
                                </b-tr>
                                <b-tr>
                                    <b-td>
                                        <b-form-radio-group v-model="form.age_type_id" size="sm">
                                            <b-form-radio value="4">&gt; 8 tahun</b-form-radio>
                                        </b-form-radio-group>
                                    </b-td>
                                    <b-td>&gt; 100</b-td>
                                    <b-td>&gt; 20</b-td>
                                    <b-td>&gt; 38ᵒ</b-td>
                                    <b-td>92%</b-td>
                                </b-tr>
                            </b-table-simple>
                            <div class="">
                                <label class="c-form-label">Zona</label>
                                <b-form-radio-group v-model="form.zone_type_id" size="sm">
                                    <b-form-radio value="1">Resusitasi</b-form-radio>
                                    <b-form-radio value="2">Tindakan</b-form-radio>
                                    <b-form-radio value="3">Observasi</b-form-radio>
                                    <b-form-radio value="4">Rawat jalan</b-form-radio>
                                    <b-form-radio value="5">Ponek</b-form-radio>
                                </b-form-radio-group>
                            </div>
                        </section>
                    </section>
                    <div class="card-footer">
                        <div class="w-100 d-flex justify-content-end align-items-end">
                            <b-button :disabled="isDisabled" @click="resetForm" variant="outline-primary" class="mr-2">Reset</b-button>
                            <b-button :disabled="isDisabled" @click="validationForm" variant="primary">Simpan</b-button>
                        </div>
                    </div>
                </div>
            </section>
        </Layout>
    </div>
</template>
<script>
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import patientMenu from '@/@core/components/tab-menu/patientMenu.vue'

export default {
    name: 'Triage',
    components: {
        patientMenu
    },
    data() {
        return {
            urlbe:process.env.VUE_APP_IMAGE,
            userData: JSON.parse(localStorage.getItem('userData')),
            reg_des_id: '',
            isDisabled: false,
            regData: {},
            triageData: [],

            form: {
                id: '',
                registration_destination_id: '',
                employee_nurse_id: '',  	
                referral_type_id: 0,
                referral: '',
                level_of_consciousness_type_id: 0,
                type_of_cases_type_id: 0,
                trauma_type_id: 0,
                immunization_status_type_id: 0,
                main_complaint: '',
                drug_allergy_type_id: 0,
                drug_allergy: '',
                // systolic: '',
                // diastolic: '',
                // pulse_frequency: '',
                // breath_saturation: '',
                // oxygen_saturation: '',
                // temperature: '',
                // weight: '',
                // height: '',
                pain_area: '',
                pain_score: 0,
                breathing_path_arry: [],
                breathing_arry: [],
                circulation_arry: [],
                consciousness_arry: [],
                resource_arry: ["E1"],
                resuscitation_resource_arry: [],
                triage_result: '',
                zone_type_id: 0,
                age_type_id: 0,
            },
            active: {
                // tab: 'ADD',
                tab: 'LIST',
            },
            tableFields: [
                { key: 'no', label: 'No', thStyle: 'width:60px; text-align: center' },
                { key: 'employee_nurse_name', label: 'Nama perawat' },
                { key: 'date_updated', label: 'Tanggal Triase Ditambahkan / Edit' },
                { key: 'action', label: 'Aksi' },
            ],
        }
    },
    watch: {
        "form.referral_type_id"(newValue) {
            if (newValue != "1") {
                this.form.referral = ""
            }
        },
        "form.drug_allergy_type_id"(newValue) {
            if (newValue != "1") {
                this.form.drug_allergy = ""
            }
        },
        "form.type_of_cases_type_id"(newValue) {
            if (newValue != "4") {
                this.form.trauma_type_id = 0
            }
        },
        "form.resuscitation_resource_arry"(newValue) {
            this.handleTriageResult()

            if (newValue.length >= 2) {
                this.form.resource_arry = ["C1"]
            } else if (newValue.length == 1) {
                this.form.resource_arry = ["D1"]
            } else if (newValue.length == 0) {
                this.form.resource_arry = ["E1"]
            }
        },

    },
    methods: {
        countByPrefix(array, prefix) {
            return array.filter(item => item.startsWith(prefix)).length;
        },

        countTotals(...arrays) {
            const prefixes = ['A', 'B', 'C', 'D', 'E'];
            
            return prefixes.map((prefix, index) => {
                return arrays.reduce((total, arr) => total + arr[index], 0);
            });
        },

        handleTriageResult() {
            const prefixes = ['A', 'B', 'C', 'D', 'E'];

            const airwayCounts = prefixes.map(prefix => this.countByPrefix(this.form.breathing_path_arry, prefix));
            const respiratoryCounts = prefixes.map(prefix => this.countByPrefix(this.form.breathing_arry, prefix));
            const circulationCounts = prefixes.map(prefix => this.countByPrefix(this.form.circulation_arry, prefix));
            const consciousnessCounts = prefixes.map(prefix => this.countByPrefix(this.form.consciousness_arry, prefix));
            const psychologyCounts = prefixes.map(prefix => this.countByPrefix(this.form.resource_arry, prefix));

            const totalCounts = this.countTotals(
                airwayCounts,
                respiratoryCounts,
                circulationCounts,
                consciousnessCounts,
                psychologyCounts
            );

            // console.log('Total counts for each prefix:', totalCounts);

            const categories = ['LEVEL 1', 'LEVEL 2', 'LEVEL 3', 'LEVEL 4', 'LEVEL 5'];
            const priority = ['LEVEL 1', 'LEVEL 2', 'LEVEL 3', 'LEVEL 4', 'LEVEL 5'];

            const maxCount = Math.max(...totalCounts);
            
            const maxIndices = totalCounts
            .map((count, index) => (count === maxCount ? index : -1))
            .filter(index => index !== -1);

            const largestCategory = priority.find(category => maxIndices.includes(categories.indexOf(category)));
            // console.log('The category with the largest value is:', largestCategory);
            
            if (this.form.breathing_path_arry.length == 0 && this.form.breathing_arry == 0 && this.form.circulation_arry == 0 && this.form.consciousness_arry == 0 && this.form.resource_arry == 0) {
                this.form.triage_result = ''
            } else {
                this.form.triage_result = largestCategory
            }
        },
        
        refresh () {
            router.go(router.currentRoute)
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        goToTab(tab){
            this.active.tab = tab
            if (tab == 'LIST') {
                this.form.id = ''
                this.resetForm()
            }
        },
        getData (regDesId) {
			this.$store.dispatch('registration/getById', regDesId).then(data => {
                this.regData = data[0]
                // this.form.doctor_in_charge_id = this.regData.doctor_in_charge_id 
            })
        },
        getTriage (regDesId) {
			this.$store.dispatch('triage/getAllListV2', { registration_destination_id: regDesId }).then(data => {
                this.triageData = data
                // console.log('this.triageData', this.triageData);
            })
        },
        validationForm(){
            // console.log('this.form', this.form);
            this.form.registration_destination_id = this.reg_des_id
            this.form.employee_nurse_id = this.userData.employee_id
            
            try {
                this.isDisabled = true
                this.$store.dispatch('triage/formPostPutV2', this.form).then((response) => {
                    this.notification('Success', 'CheckIcon', 'success', 'You have successfully')
                    // console.log('response', response);
                    this.active.tab = "LIST"
                    this.resetForm()
                    this.isDisabled = false
                    this.getTriage(this.$route.params.id)
                })
                
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error at: ', error)
                this.isDisabled = false
            }
        },
        deleteTriage (obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus triase ${obj.employee_nurse_name} tanggal ${obj.date_updated} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    try {
                        this.$store.dispatch('triage/removeTriageV2', obj.id).then((response) => {
                            this.notification('Success', 'CheckIcon', 'success', 'You have successfully')
                            this.getTriage(this.reg_des_id)
                        })
                        
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error at: ', error)
                    }
                }
            })
        },

        onEditform (obj){
            this.goToTab('ADD')

            this.form.id = obj.id
            this.form.registration_destination_id = obj.registration_destination_id 
            this.form.employee_nurse_id = obj.employee_nurse_id 
            this.form.referral_type_id = obj.referral_type_id 
            this.form.referral = obj.referral 
            this.form.level_of_consciousness_type_id = obj.level_of_consciousness_type_id 
            this.form.type_of_cases_type_id = obj.type_of_cases_type_id 
            this.form.trauma_type_id = obj.trauma_type_id 
            this.form.immunization_status_type_id = obj.immunization_status_type_id 
            this.form.main_complaint = obj.main_complaint 
            this.form.drug_allergy_type_id = obj.drug_allergy_type_id 
            this.form.drug_allergy = obj.drug_allergy 
            // this.form.systolic = obj.systolic 
            // this.form.diastolic = obj.diastolic 
            // this.form.pulse_frequency = obj.pulse_frequency 
            // this.form.breath_saturation = obj.breath_saturation 
            // this.form.oxygen_saturation = obj.oxygen_saturation 
            // this.form.temperature = obj.temperature 
            // this.form.weight = obj.weight 
            // this.form.height = obj.height 
            this.form.pain_area = obj.pain_area 
            this.form.pain_score = obj.pain_score 
            this.form.breathing_path_arry = obj.breathing_path_arry 
            this.form.breathing_arry = obj.breathing_arry 
            this.form.circulation_arry = obj.circulation_arry 
            this.form.consciousness_arry = obj.consciousness_arry 
            this.form.resource_arry = obj.resource_arry 
            this.form.resuscitation_resource_arry = obj.resuscitation_resource_arry 
            this.form.triage_result = obj.triage_result 
            this.form.zone_type_id = obj.zone_type_id 
            this.form.age_type_id = obj.age_type_id 
        },
        resetForm() {
            this.form.referral_type_id = 0
            this.form.referral = ''
            this.form.level_of_consciousness_type_id = 0
            this.form.type_of_cases_type_id = 0
            this.form.trauma_type_id = 0
            this.form.immunization_status_type_id = 0
            this.form.main_complaint = ''
            this.form.drug_allergy_type_id = 0
            this.form.drug_allergy = ''
            // this.form.systolic = ''
            // this.form.diastolic = ''
            // this.form.pulse_frequency = ''
            // this.form.breath_saturation = ''
            // this.form.oxygen_saturation = ''
            // this.form.temperature = ''
            // this.form.weight = ''
            // this.form.height = ''
            this.form.pain_area = ''
            this.form.pain_score = 0
            this.form.breathing_path_arry = []
            this.form.breathing_arry = []
            this.form.circulation_arry = []
            this.form.consciousness_arry = []
            this.form.resource_arry = []
            this.form.resuscitation_resource_arry = []
            this.form.triage_result = ''
            this.form.zone_type_id = 0
            this.form.age_type_id = 0
        },
    },
    created () {
        this.handleTriageResult()

        if (this.$route.params.id) {
            this.reg_des_id = this.$route.params.id
            this.getData(this.$route.params.id)
            this.getTriage(this.$route.params.id)
        }
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>
<style scoped>
/* @import '../../../../@core/css/utility.css'; */

.label-head::before {
    content: '';
    border-left: 6px solid #648DE1;
}

.c-img {
    aspect-ratio: 1/1;
    object-fit: cover;
    max-width: 150px;
    max-height: 150px;
    border: 10px solid white;
}

li {
    list-style-type: none;
}

li a {
    color: #78838C;
}

li a:hover {
    text-decoration: none;
    color: #101725;
    background-color: rgb(245, 239, 254);
    cursor: pointer;
}

li a.active {
    background-color: rgba(132, 66, 243, 0.05);
    color: indigo;
    border: 1px solid indigo;
    color: #1B1B1B;
}

li a.active:hover {
    border: 1px solid indigo;
    background-color: rgb(245, 239, 254);
    color: indigo;
    cursor: pointer;
}
td{
    padding-top: 2px;
    padding-bottom: 2px;
}
</style>