<template>
    <div>
        <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Riwayat Kesehatan
            </div>
            <div class="row mt-10 mt-lg-20">
                <div class="col-lg-6">
                    <div>
                        <ModernLabel label="Pernah Dirawat sebelumnya" />
                        <b-form-radio-group v-model="form.been_hospitalized_type_id" class="mt-8 mt-lg-20">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya, dimana?</b-form-radio>
                        </b-form-radio-group>
                        <b-form-input v-model="form.been_hospitalized_answer" :disabled="form.been_hospitalized_type_id !== '1'" class="mt-10 col-lg-8" />
                    </div>
                    <div class="mt-32">
                        <ModernLabel label="Riwayat Penyakit Keluarga" />
                        <b-form-radio-group v-model="form.any_history_family_disease_type_id" class="mt-8 mt-lg-20">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya</b-form-radio>
                        </b-form-radio-group>
                        <b-form-checkbox-group size="sm" v-model="form.any_history_family_answer_arry" class="mt-10" :disabled="form.any_history_family_disease_type_id !== '1'">
                            <b-form-checkbox value="a" class="col-lg-3 my-1 mr-0">DM</b-form-checkbox>
                            <b-form-checkbox value="b" class="col-lg-3 my-1 mr-0">Hipertensi</b-form-checkbox>
                            <b-form-checkbox value="c" class="col-lg-3 my-1 mr-0">Cancer</b-form-checkbox>
                            <b-form-checkbox value="d" class="col-lg-3 my-1 mr-0">Jantung</b-form-checkbox>
                            <b-form-checkbox value="e" class="col-lg-3 my-1 mr-0">TBC</b-form-checkbox>
                            <b-form-checkbox value="f" class="col-lg-3 my-1 mr-0">Anemia</b-form-checkbox>
                            <b-form-checkbox value="g" class="col-lg-6 my-1 mr-0">Lain-lain, sebutkan:</b-form-checkbox>
                        </b-form-checkbox-group>
                        <b-form-input v-model="form.any_history_family_answer" class="mt-10 col-lg-8" :disabled="form.any_history_family_disease_type_id !== '1' || form.any_history_family_answer_arry.indexOf('g') === -1" />
                    </div>
                </div>
                <div class="col-lg-6 mt-32 mt-lg-0">
                    <div>
                        <ModernLabel label="Operasi Yang Pernah Di Alami" />
                        <div>
                            <div class="mt-10">
                                <label class="c-form-label">Jenis</label>
                                <b-form-input v-model="form.any_surgery" />
                            </div>
                            <div class="mt-10">
                                <label class="c-form-label">Kapan</label>
                                <b-form-input type="date" v-model="form.any_surgery_date" />
                            </div>
                            <div class="mt-10">
                                <label class="c-form-label">Komplikasi</label>
                                <b-form-input v-model="form.complication" />
                            </div>
                        </div>
                    </div>
                    <div class="mt-32">
                        <ModernLabel label="Riwayat Alergi" />
                        <b-form-radio-group v-model="form.history_allergy_type_id" class="mt-8 mt-lg-20">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya, sebutkan</b-form-radio>
                        </b-form-radio-group>
                        <b-form-input v-model="form.history_allergy" class="mt-10 col-lg-8" :disabled="form.history_allergy_type_id != '1'" />
                    </div>
                </div>
            </div>
            <div class="mt-16 mt-lg-32">
                <ModernLabel label="Riwayat Imunisasi" />
                <b-form-checkbox-group v-model="form.history_immunization_arry" size="sm" class="mt-10">
                    <b-form-checkbox value="a" class="col-lg-2 my-1 mr-0">BCG</b-form-checkbox>
                    <b-form-checkbox value="b" class="col-lg-2 my-1 mr-0">Polio I</b-form-checkbox>
                    <b-form-checkbox value="c" class="col-lg-2 my-1 mr-0">HIB</b-form-checkbox>
                    <b-form-checkbox value="d" class="col-lg-2 my-1 mr-0">Hep B I</b-form-checkbox>
                    <b-form-checkbox value="e" class="col-lg-2 my-1 mr-0">DPT II</b-form-checkbox>
                    <b-form-checkbox value="f" class="col-lg-2 my-1 mr-0">Polio III</b-form-checkbox>
                    <b-form-checkbox value="g" class="col-lg-2 my-1 mr-0">Polio Nol</b-form-checkbox>
                    <b-form-checkbox value="h" class="col-lg-2 my-1 mr-0">MMR</b-form-checkbox>
                    <b-form-checkbox value="i" class="col-lg-2 my-1 mr-0">DPT IV</b-form-checkbox>
                    <b-form-checkbox value="j" class="col-lg-2 my-1 mr-0">DPT I</b-form-checkbox>
                    <b-form-checkbox value="k" class="col-lg-2 my-1 mr-0">Hep B III</b-form-checkbox>
                    <b-form-checkbox value="l" class="col-lg-2 my-1 mr-0">Polio V</b-form-checkbox>
                    <b-form-checkbox value="m" class="col-lg-2 my-1 mr-0">Campak</b-form-checkbox>
                    <b-form-checkbox value="n" class="col-lg-2 my-1 mr-0">Polio II</b-form-checkbox>
                    <b-form-checkbox value="o" class="col-lg-2 my-1 mr-0">Hep B II</b-form-checkbox>
                    <b-form-checkbox value="p" class="col-lg-2 my-1 mr-0">DPT III</b-form-checkbox>
                    <b-form-checkbox value="q" class="col-lg-4 my-1 mr-0">Lain-lain, sebutkan :</b-form-checkbox>
                </b-form-checkbox-group>
                <b-form-input v-model="form.history_immunization" class="ml-lg-auto col-lg-4 mt-10" :disabled="form.history_immunization_arry.indexOf('q') === -1"></b-form-input>
            </div>

        </section>
        <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Riwayat Psikologis
            </div>
            <ModernLabel label="Pilih Riwayat" class="mt-12" />
            <b-form-checkbox-group v-model="form.history_psychological_arry" size="sm" class="mt-10 mt-lg-20">
                <b-form-checkbox value="a" class="col-lg-3 my-1 mr-0">Tidak Semangat</b-form-checkbox>
                <b-form-checkbox value="b" class="col-lg-3 my-1 mr-0">Cepat Lelah</b-form-checkbox>
                <b-form-checkbox value="c" class="col-lg-3 my-1 mr-0">Menggunakan Obat Penenang</b-form-checkbox>
                <b-form-checkbox value="d" class="col-lg-3 my-1 mr-0">Rasa Tertekan</b-form-checkbox>
                <b-form-checkbox value="e" class="col-lg-3 my-1 mr-0">Sulit Berbicara</b-form-checkbox>
                <b-form-checkbox value="f" class="col-lg-3 my-1 mr-0">Merasa Bersalah</b-form-checkbox>
                <b-form-checkbox value="g" class="col-lg-3 my-1 mr-0">Depresi</b-form-checkbox>
                <b-form-checkbox value="h" class="col-lg-3 my-1 mr-0">Kurang Nafsu Makan</b-form-checkbox>
                <b-form-checkbox value="i" class="col-lg-3 my-1 mr-0">Tenang</b-form-checkbox>
                <b-form-checkbox value="j" class="col-lg-3 my-1 mr-0">Sulit Tidur</b-form-checkbox>
                <b-form-checkbox value="k" class="col-lg-3 my-1 mr-0">Sulit Konsentrasi</b-form-checkbox>
                <b-form-checkbox value="l" class="col-lg-3 my-1 mr-0">Tidak Ada Keluhan</b-form-checkbox>
            </b-form-checkbox-group>
        </section>
        <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Skrining Gizi
            </div>
            <div class="row mt-16 mt-lg-20">
                <div class="col-lg-6">
                    <div class="mb-lg-10">
                        <div class="c-form-label pb-0">Indeks Masa Tubuh</div>
                        <small class="text-muted font-italic">(IMT = Berat Badab/ Tinggi Badan dalam meter)</small>
                    </div>
                    <b-form-select @change="handleNutritionScore" v-model="form.body_mass_index" :options="options_body_mass"></b-form-select>
                    <!-- <v-select v-model="form.body_mass_index" @change="handleNutritionScore" aria-readonly="true" :options="options_body_mass" :reduce="options_body_mass => options_body_mass.value" label="title" placeholder="-- IMT --"></v-select> -->
                </div>
                <div class="col-lg-6 mt-10 mt-lg-0">
                    <div class="mb-lg-10">
                        <div class="c-form-label pb-0">Asupan Makan Pasien</div>
                        <small class="text-muted font-italic">Dalam 5 Hari Terakhir</small>
                    </div>
                    <b-form-select @change="handleNutritionScore" v-model="form.patient_food_intake" :options="options_food_intake"></b-form-select>
                    <!-- <v-select v-model="form.patient_food_intake" @change="handleNutritionScore" aria-readonly="true" :options="options_food_intake" :reduce="options_food_intake => options_food_intake.value" label="title" placeholder="-- Asupan Makan --"></v-select> -->
                </div>
            </div>
            <div class="row mt-lg-20 align-items-lg-end">
                <div class="col-lg-6 mt-10 mt-lg-0">
                    <div class="mb-lg-10">
                        <div class="c-form-label pb-0">Kehilangan Berat Badan</div>
                        <small class="text-muted font-italic">Dalam Waktu  6 Bulan Terakhir</small>
                    </div>
                    <b-form-select @change="handleNutritionScore" v-model="form.six_month_lose_weight" :options="options_six_months"></b-form-select>
                    <!-- <v-select v-model="form.six_month_lose_weight" @change="handleNutritionScore" aria-readonly="true" :options="options_six_months" :reduce="options_six_months => options_six_months.value" label="title" placeholder="-- Kehilangan Berat Badan --"></v-select> -->
                </div>
                <div v-if="form.six_month_lose_weight == '3'" class="col-lg-6 mt-10 mt-lg-0">
                    <div class="mb-lg-10">
                        <div class="c-form-label pb-0">Kehilangan Berat Badan</div>
                    </div>
                    <b-form-select @change="handleNutritionScore" v-model="form.lose_weight" :options="options_lose_weight"></b-form-select>
                    <!-- <v-select v-model="form.lose_weight" @change="handleNutritionScore" aria-readonly="true" :options="options_lose_weight" :reduce="options_lose_weight => options_lose_weight.value" label="title" placeholder="-- Kehilangan Berat Badan --"></v-select> -->
                </div>
            </div>
            <div class="row mt-lg-20 align-items-lg-end">
                <div class="col-lg-6 mt-10 mt-lg-0">
                    <div class="mb-lg-10">
                        <div class="c-form-label pb-0">Kondisi Penyakit Pasien</div>
                        <small class="text-muted font-italic">Yang Mempunyai Risiko Nutrisi</small>
                    </div>
                    <b-form-select @change="handleNutritionScore" v-model="form.patient_disease_condition" :options="options_disease_condition"></b-form-select>
                    <!-- <v-select v-model="form.patient_disease_condition" @change="handleNutritionScore" aria-readonly="true" :options="options_disease_condition" :reduce="options_disease_condition => options_disease_condition.value" label="title" placeholder="-- Risiko Nutrisi --"></v-select> -->
                </div>
                <div class="col-lg-6 mt-10 mt-lg-0">
                    <div class="mb-lg-10">
                        <div class="c-form-label pb-0">Pasien Sedang Mendapat Diet Makanan Tertentu</div>
                    </div>
                    <b-form-select @change="handleNutritionScore" v-model="form.patient_certain_diets" :options="options_certain_diets"></b-form-select>
                    <!-- <v-select v-model="form.patient_certain_diets" @change="handleNutritionScore" aria-readonly="true" :options="options_certain_diets" :reduce="options_certain_diets => options_certain_diets.value" label="title" placeholder="-- Diet Makanan --"></v-select> -->
                </div>
                
            </div>
            <div class="col px-0 mt-32">
                <div class="row">
                    <div class="col-lg-6 mt-10 mt-lg-0">
                        <b-input-group prepend="TOTAL SKOR">
                            <b-form-input v-model="form.nutrition_total_score" disable></b-form-input>
                        </b-input-group>
                    </div>
                </div>
            </div>
        </section>
        <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Skala Nyeri
            </div>
            <div class="row">
                <div class="col-lg-6 mt-20">
                    <ModernLabel label="Apakah terdapat keluhan nyeri?" />
                    <b-form-radio-group v-model="form.sore_complaints_type_id" class="mt-8 mt-lg-20">
                        <b-form-radio value="0">Tidak</b-form-radio>
                        <b-form-radio value="1">Ya, dimana?</b-form-radio>
                    </b-form-radio-group>
                    <b-form-input v-model="form.sore_complaints" class="mt-10 col-lg-8" :disabled="form.sore_complaints_type_id !== '1'" />
                </div>
                <div class="col-lg-6 mt-20">
                    <ModernLabel label="Nyeri Hilang Bila" />
                    <b-form-checkbox-group v-model="form.sore_goes_away_arry" :disabled="form.sore_complaints_type_id !== '1'" size="sm" class="mt-16">
                        <b-form-checkbox value="a" class="col-lg-4 my-1 mr-0">Minum Obat</b-form-checkbox>
                        <b-form-checkbox value="b" class="col-lg-4 my-1 mr-0">Mendengar Musik</b-form-checkbox>
                        <b-form-checkbox value="c" class="col-lg-4 my-1 mr-0">Istirahat</b-form-checkbox>
                        <b-form-checkbox value="d" class="col-lg-4 my-1 mr-0">Berubah Posisi Tidur</b-form-checkbox>
                        <b-form-checkbox value="e" class="col-lg-4 my-1 mr-0">Lain-lain</b-form-checkbox>
                    </b-form-checkbox-group>
                    <b-form-input v-model="form.sore_goes_away" class="mt-10 col-lg-8" :disabled="form.sore_complaints_type_id !== '1' || form.sore_goes_away_arry.indexOf('e') === -1" />
                </div>
            </div>
            <div class="mt-20">
                <ModernLabel label="Metoda" />
                <b-form-radio-group v-model="form.sore_method_type_id" :disabled="form.sore_complaints_type_id !== '1'" class="mt-8 mt-lg-20">
                    <b-form-radio value="a">NIPS</b-form-radio>
                    <b-form-radio value="b">FLACC</b-form-radio>
                    <b-form-radio value="c">VAS</b-form-radio>
                    <b-form-radio value="d">BPS</b-form-radio>
                    <b-form-radio value="e">Wong-Baker</b-form-radio>
                    <b-form-radio value="f">NRS</b-form-radio>
                </b-form-radio-group>
            </div>
            <div v-if="form.sore_method_type_id == 'c'" class="mt-32">
                <b-table-simple fixed responsive small head-variant="light" class="mt-12">
                    <b-thead class="text-bold-14">
                        <b-tr>
                            <b-th>-</b-th>
                            <b-th>0</b-th>
                            <b-th>1</b-th>
                            <b-th>2</b-th>
                            <b-th>Total</b-th>
                        </b-tr>
                    </b-thead>
                    <b-tbody class="text-regular-12">
                        <b-tr>
                            <b-td>FACE</b-td>
                            <b-td>Tidak ekspresisi atau dalam keadaan tersenyum</b-td>
                            <b-td>Menyeringai, mengerutkan dahi, tampak tidak tertarik</b-td>
                            <b-td>Dagi gemetar, gigi gemeretak, rahang terkancing</b-td>
                            <b-td><b-form-input type="number" v-model="form.face_score" /></b-td>
                        </b-tr>
                        <b-tr>
                            <b-td>LEGS</b-td>
                            <b-td>Normal, rileks</b-td>
                            <b-td>Gelisah, tegang, resah</b-td>
                            <b-td>Menendang, kaki tertekuk atau membengkokan kaki</b-td>
                            <b-td><b-form-input type="number" v-model="form.legs_score" /></b-td>
                        </b-tr>
                        <b-tr>
                            <b-td>ACTIVITY</b-td>
                            <b-td>Berbaring terlentang, posisi normal, gerakan mudah</b-td>
                            <b-td>Menggeliat, tidak bisa diam, tegang</b-td>
                            <b-td>Kaku, kejang</b-td>
                            <b-td><b-form-input type="number" v-model="form.activity_score" /></b-td>
                        </b-tr>
                        <b-tr>
                            <b-td>CRY</b-td>
                            <b-td>Tidak menangis</b-td>
                            <b-td>Merintih, merengek</b-td>
                            <b-td>Terus menangis, berteriak, sering mengeluh</b-td>
                            <b-td><b-form-input type="number" v-model="form.cry_score" /></b-td>
                        </b-tr>
                        <b-tr>
                            <b-td>CONSOLABILITY</b-td>
                            <b-td>Rileks</b-td>
                            <b-td>Kadang mengeluh, dapat ditenangkan dengan sentuhan, pelukan, bujukan, dapat dialihkan</b-td>
                            <b-td>-</b-td>
                            <b-td><b-form-input type="number" v-model="form.consolability_score" /></b-td>
                        </b-tr>
                    </b-tbody>
                </b-table-simple>
            </div>
            <div v-if="form.sore_method_type_id == 'e'" class="mt-32">
                <b-form-radio-group v-model="form.wong_baker_score" @change="handleSoreMethodScore">
                    <b-form-radio value="0" class="col-2 mr-0">
                        <div class="text-center">
                            <img src="@/@core/assets/images/nurse/wong_baker/0.png" width="50%">
                            <div class="c-form-label mt-10">
                                <div>0</div>
                                <div>Tidak Nyeri</div>
                            </div>
                        </div>
                    </b-form-radio>
                    <b-form-radio value="2" class="col-2 mr-0">
                        <div class="text-center">
                            <img src="@/@core/assets/images/nurse/wong_baker/2.png" width="50%">
                            <div class="c-form-label mt-10">
                                <div>2</div>
                                <div>Sedikit Nyeri</div>
                            </div>
                        </div>
                    </b-form-radio>
                    <b-form-radio value="4" class="col-2 mr-0">
                        <div class="text-center">
                            <img src="@/@core/assets/images/nurse/wong_baker/4.png" width="50%">
                            <div class="c-form-label mt-10">
                                <div>4</div>
                                <div>Sedikit Lebih Nyeri</div>
                            </div>
                        </div>
                    </b-form-radio>
                    <b-form-radio value="6" class="col-2 mr-0">
                        <div class="text-center">
                            <img src="@/@core/assets/images/nurse/wong_baker/6.png" width="50%">
                            <div class="c-form-label mt-10">
                                <div>6</div>
                                <div>Lebih Nyeri</div>
                            </div>
                        </div>
                    </b-form-radio>
                    <b-form-radio value="8" class="col-2 mr-0">
                        <div class="text-center">
                            <img src="@/@core/assets/images/nurse/wong_baker/8.png" width="50%">
                            <div class="c-form-label mt-10">
                                <div>8</div>
                                <div>Sangat Nyeri</div>
                            </div>
                        </div>
                    </b-form-radio>
                    <b-form-radio value="10" class="col-2 mr-0">
                        <div class="text-center">
                            <img src="@/@core/assets/images/nurse/wong_baker/10.png" width="50%">
                            <div class="c-form-label mt-10">
                                <div>10</div>
                                <div>Nyeri Sangat Hebat</div>
                            </div>
                        </div>
                    </b-form-radio>
                </b-form-radio-group>
            </div>
            <div v-if="form.sore_method_type_id == 'f'" class="mt-32 position-relative">
                <div class="timeline-wrapper p-16 rounded">
                    <b-form-radio-group v-model="form.nrs_score" @change="handleSoreMethodScore" class="w-100">
                        <div class="d-flex justify-content text-center">
                            <div class="col-2">
                                <div class="text-bold-12" style="height:40px">Tidak Nyeri</div>
                                <b-form-radio value="0" class="col-1 d-inline-block mr-0"><div class="w-100">0</div></b-form-radio>
                            </div>
                            <div class="col devider">
                                <div class="text-bold-12" style="height:40px">Nyeri Ringan</div>
                                <div class="d-flex justify-content-between">
                                    <b-form-radio value="1" class="col d-inline-block mr-0">1</b-form-radio>
                                    <b-form-radio value="2" class="col d-inline-block mr-0">2</b-form-radio>
                                    <b-form-radio value="3" class="col d-inline-block mr-0">3</b-form-radio>
                                </div>
                            </div>
                            <div class="col devider">
                                <div class="text-bold-12" style="height:40px">Nyeri Sedang</div>
                                <div class="d-flex justify-content-between">
                                    <b-form-radio value="4" class="col d-inline-block mr-0">4</b-form-radio>
                                    <b-form-radio value="5" class="col d-inline-block mr-0">5</b-form-radio>
                                    <b-form-radio value="6" class="col d-inline-block mr-0">6</b-form-radio>
                                </div>
                            </div>
                            <div class="col devider">
                                <div class="text-bold-12" style="height:40px">Nyeri Terkontrol</div>
                                <div class="d-flex justify-content-between">
                                    <b-form-radio value="7" class="col d-inline-block mr-0">7</b-form-radio>
                                    <b-form-radio value="8" class="col d-inline-block mr-0">8</b-form-radio>
                                    <b-form-radio value="9" class="col d-inline-block mr-0">9</b-form-radio>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="text-bold-12" style="height:40px">Nyeri Berat Tidak Terkontrol</div>
                                <b-form-radio value="10" class="col-2 d-inline-block mr-0">10</b-form-radio>
                            </div>
                        </div>
                    </b-form-radio-group>
                </div>
                <div class="row mt-16">
                    <div class="col-lg-4">
                        <b-form-checkbox-group size="sm">
                            <b-form-checkbox value="a">Nyeri Kronis</b-form-checkbox>
                            <b-form-checkbox value="b">Nyeri Akut</b-form-checkbox>
                        </b-form-checkbox-group>
                    </div>
                    <div class="col-lg-4 mt-12 mt-lg-0">
                        <label class="c-form-label">Diberitahukan ke dokter</label>
                        <b-form-radio-group v-model="form.tell_doctor_type_id" size="sm">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya, Pukul</b-form-radio>
                        </b-form-radio-group>
                        <b-form-input v-model="form.tell_doctor_date" type="time" class="mt-10"/>
                    </div>
                </div>
            </div>
            <div class="row mt-20">
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Skor</label>
                    <b-input-group prepend="Skor">
                        <b-form-input v-model="form.sore_total_score" disabled/>
                    </b-input-group>
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Durasi</label>
                    <b-form-input v-model="form.duration" />
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Lokasi</label>
                    <b-form-input v-model="form.location"/>
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Respon</label>
                    <b-form-input v-model="form.response"/>
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Frekuensi</label>
                    <b-form-input v-model="form.frequency" type="number" />
                </div>
            </div>
        </section>
        <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Pengkajian Risiko Jatuh
            </div>
            <div class="row">
                <div class="col-lg-6 mt-20">
                    <ModernLabel label="Metode" />
                    <div class="mt-10 mt-lg-20">
                        <b-form-radio-group v-model="form.fall_risk_method_type_id" size="sm">
                            <b-form-radio class="my-1" value="a">Morse Fall Scale</b-form-radio>
                            <b-form-radio class="my-1" value="b">Humpty Dumpty</b-form-radio>
                            <b-form-radio class="my-1" value="c">Geriatri</b-form-radio>
                        </b-form-radio-group>
                        <b-input-group prepend="Skor" class="mt-12">
                            <b-form-input v-model="form.fall_risk_total_score" />
                        </b-input-group>
                    </div>
                </div>
                <div class="col-lg mt-20">
                    <ModernLabel label="Risiko" />
                    <b-form-radio-group v-model="form.fall_risk_type_id" class="mt-8 mt-lg-20">
                        <b-form-radio class="my-1" value="a">Rendah</b-form-radio>
                        <b-form-radio class="my-1" value="b">Sedang</b-form-radio>
                        <b-form-radio class="my-1" value="c">Tinggi</b-form-radio>
                    </b-form-radio-group>
                </div>
            </div>
        </section>
        <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Daftar Masalah Keperawatan
            </div>
            <div class="row">
                <div class="col-lg-6 mt-20">
                    <div>
                        <ModernLabel label="Diagnosa Keperawatan" />
                        <b-form-checkbox-group v-model="form.nursing_diagnoses_arry" size="sm" class="mt-10">
                            <b-form-checkbox value="a" class="col-lg-6 my-1 mr-0">Bersihkan jalan nafas</b-form-checkbox>
                            <b-form-checkbox value="b" class="col-lg-6 my-1 mr-0">Cemas/ Ansietas</b-form-checkbox>
                            <b-form-checkbox value="c" class="col-lg-6 my-1 mr-0">Ketidakefektifan jalan nafas</b-form-checkbox>
                            <b-form-checkbox value="d" class="col-lg-6 my-1 mr-0">Hipertemi</b-form-checkbox>
                            <b-form-checkbox value="e" class="col-lg-6 my-1 mr-0">Gangguan mobilitas fisik</b-form-checkbox>
                            <b-form-checkbox value="f" class="col-lg-6 my-1 mr-0">Keterlambatan pertumbuhan dan perkembangan</b-form-checkbox>
                            <b-form-checkbox value="g" class="col-lg-6 my-1 mr-0">Intoleransi aktifitas</b-form-checkbox>
                            <b-form-checkbox value="h" class="col-lg-6 my-1 mr-0">Risiko tinggi infeksi</b-form-checkbox>
                            <b-form-checkbox value="i" class="col-lg-6 my-1 mr-0">Gangguan persepsi sensori pendengaran</b-form-checkbox>
                            <b-form-checkbox value="j" class="col-lg-6 my-1 mr-0">Risiko tinggi cedera</b-form-checkbox>
                            <b-form-checkbox value="k" class="col-lg-6 my-1 mr-0">Gangguan persepsi sensori penglihatan</b-form-checkbox>
                            <b-form-checkbox value="l" class="col-lg-6 my-1 mr-0">Risiko kekurangan cairan</b-form-checkbox>
                            <b-form-checkbox value="m" class="col-lg-6 my-1 mr-0">Gangguan integritas kulit</b-form-checkbox>
                            <b-form-checkbox value="n" class="col-lg-6 my-1 mr-0">Hipertensi</b-form-checkbox>
                            <b-form-checkbox value="o" class="col-lg-6 my-1 mr-0">Kurang pengetahuan</b-form-checkbox>
                            <b-form-checkbox v-for="(diagnosa, index) in diagnosa.arrDiagnosa" :key="index" :value="diagnosa" class="col-lg-6 my-1 mr-0 text-capitalize">{{ diagnosa }}</b-form-checkbox>
                        </b-form-checkbox-group>
                        <div class="mt-10 mt-lg-24">
                            <b-input-group>
                                <template #append>
                                    <b-button @click="addNewDiagnosa" variant="info-700" class="px-16 px-lg-32">+</b-button>
                                </template>
                                <b-form-input v-model="diagnosa.newInput"></b-form-input>
                            </b-input-group>
                            <small class="text-muted font-italic">*digunakan untuk menambahkan diagnosa baru</small>
                        </div>
                    </div>
                    <!-- <div class="mt-20 mt-lg-32">
                        <ModernLabel label="Discharge Planning" />
                        <div>
                            <div class="mt-10">
                                <label class="c-form-label">Diit</label>
                                <b-form-input v-model="form.plan_diit"/>
                            </div>
                            <div class="mt-10">
                                <label class="c-form-label">Aktivitas</label>
                                <b-form-input v-model="form.plan_activity"/>
                            </div>
                            <div class="mt-10">
                                <label class="c-form-label">Obat</label>
                                <b-form-input v-model="form.plan_medicine"/>
                            </div>
                            <div class="mt-10">
                                <label class="c-form-label">Kontrol Ulang</label>
                                <b-form-input v-model="form.plan_recontrol"/>
                            </div>
                        </div>
                    </div> -->
                </div>
                <div class="col-lg-6 mt-20">
                    <ModernLabel label="Implementasi" />
                    <b-form-checkbox-group v-model="form.implementation_arry" size="sm" class="mt-10">
                        <b-form-checkbox value="a" class="col-lg-6 my-1 mr-0">Menjelaskan cara pencegahan dan perawatan kulit</b-form-checkbox>
                        <b-form-checkbox value="b" class="col-lg-6 my-1 mr-0">Sehat</b-form-checkbox>
                        <b-form-checkbox value="c" class="col-lg-6 my-1 mr-0">Mengajarkan teknik nafas dalam</b-form-checkbox>
                        <b-form-checkbox value="d" class="col-lg-6 my-1 mr-0">MCU</b-form-checkbox>
                        <b-form-checkbox value="e" class="col-lg-6 my-1 mr-0">Mengajarkan batuk efektif</b-form-checkbox>
                        <b-form-checkbox value="f" class="col-lg-6 my-1 mr-0">Menganjurkan untuk banyak makan buah segar dan kacang-kacangan</b-form-checkbox>
                        <b-form-checkbox value="g" class="col-lg-6 my-1 mr-0">Menginformasikan tidak merokok dalam ruangan</b-form-checkbox>
                        <b-form-checkbox value="h" class="col-lg-6 my-1 mr-0">SKPN dan Rapid test</b-form-checkbox>
                        <b-form-checkbox value="i" class="col-lg-6 my-1 mr-0">Memantau tanda-tanda sianosis</b-form-checkbox>
                        <b-form-checkbox value="j" class="col-lg-6 my-1 mr-0">MCU calon karyawan</b-form-checkbox>
                        <b-form-checkbox value="k" class="col-lg-6 my-1 mr-0">Mengukur warna dan suhu kulit</b-form-checkbox>
                        <b-form-checkbox value="l" class="col-lg-6 my-1 mr-0">Tidak menahan BAK</b-form-checkbox>
                        <b-form-checkbox value="m" class="col-lg-6 my-1 mr-0">Mengatur posisi nyaman</b-form-checkbox>
                        <b-form-checkbox value="n" class="col-lg-6 my-1 mr-0">Memberikan obat tetes mata</b-form-checkbox>
                        <b-form-checkbox value="o" class="col-lg-6 my-1 mr-0">Menganjurkan banyak minum</b-form-checkbox>
                        <b-form-checkbox value="p" class="col-lg-6 my-1 mr-0">Manganjurkan pola hidup sehat</b-form-checkbox>
                        <b-form-checkbox value="q" class="col-lg-6 my-1 mr-0">Kompres air hangat pada daerah yang gatal</b-form-checkbox>
                        <b-form-checkbox value="r" class="col-lg-6 my-1 mr-0">Swab PCR some day</b-form-checkbox>
                        <b-form-checkbox value="s" class="col-lg-6 my-1 mr-0">Jaga kebersihan</b-form-checkbox>
                        <b-form-checkbox value="t" class="col-lg-6 my-1 mr-0">Injeksi Vit. C</b-form-checkbox>
                        <b-form-checkbox value="u" class="col-lg-6 my-1 mr-0">SKPN</b-form-checkbox>
                        <b-form-checkbox value="v" class="col-lg-6 my-1 mr-0">Menganjurkan banyak istirahat</b-form-checkbox>
                        <b-form-checkbox value="w" class="col-lg-6 my-1 mr-0">Anjurkan banyak minum air putih</b-form-checkbox>
                        <b-form-checkbox value="x" class="col-lg-6 my-1 mr-0">Infus Vit. C</b-form-checkbox>
                        <b-form-checkbox value="y" class="col-lg-6 my-1 mr-0">Makan porsi kecil tapi sering</b-form-checkbox>
                        <b-form-checkbox value="z" class="col-lg-6 my-1 mr-0">Istirahatkan mata</b-form-checkbox>
                        <b-form-checkbox value="aa" class="col-lg-6 my-1 mr-0">Nyeri</b-form-checkbox>
                        <b-form-checkbox value="ab" class="col-lg-6 my-1 mr-0">Menganjurkan banyak makan sayur</b-form-checkbox>
                        <b-form-checkbox value="ac" class="col-lg-6 my-1 mr-0">Anti HIV rapid</b-form-checkbox>
                        <b-form-checkbox v-for="(implement, index) in implement.arrImplement" :key="index" :value="implement" class="col-lg-6 my-1 mr-0 text-capitalize">{{ implement }}</b-form-checkbox>
                    </b-form-checkbox-group>
                    <div class="col-12 px-0 mt-10 mt-lg-24">
                        <b-input-group>
                            <template #append>
                                <b-button @click="addNewImplement" variant="info-700" class="px-16 px-lg-32">+</b-button>
                            </template>
                            <b-form-input v-model="implement.newInput"></b-form-input>
                        </b-input-group>
                        <small class="text-muted font-italic">*digunakan untuk menambahkan implementasi baru</small>
                    </div>
                </div>
            </div>
        </section>

    </div>
</template>
<script>
import { mapState } from 'vuex'
import vSelect from 'vue-select'
import ModernLabel from '@/@core/components/label/ModernLabel.vue';

export default {
    name: 'nurse_adult',
    components: { vSelect, ModernLabel },
    props: {
        trigger_clear_subnurse: {},
    },
    data() {
        return {
            // # TYPE_NURSE_ID 2 = CHIILD
            options_body_mass:[
                { value: 1, text: '18,5-22.9' },
                { value: 2, text: '17-18.4 / 23-24.9' },
                { value: 3, text: '<17 / >=25' },
            ],
            options_six_months:[
                { value: 1, text: 'Tidak' },
                { value: 2, text: 'Tidak Yakin' },
                { value: 3, text: 'Ya' },
            ],
            options_lose_weight:[
                { value: 1, text: '1 - 5 kg' },
                { value: 2, text: '6 - 10 kg' },
                { value: 3, text: '11 - 15 kg' },
                { value: 4, text: '> 15 kg' },
                { value: 5, text: 'Tidak Tahu Berapa kg' },
            ],
            options_food_intake:[
                { value: 1, text: 'Baik' },
                { value: 2, text: 'Kurang' },
                { value: 3, text: 'Kurang Semangat' },
            ],
            options_disease_condition:[
                { value: 1, text: 'Tidak' },
                { value: 2, text: 'Ya' },
            ],
            options_certain_diets:[
                { value: 1, text: 'Tidak' },
                { value: 2, text: 'Ya' },
            ],
            implement: {
                arrImplement: [],
                newInput: '',
            },
            diagnosa: {
                arrDiagnosa: [],
                newInput: '',
            },
            form: {
                been_hospitalized_type_id: '0',
                been_hospitalized_answer: '',
                any_history_family_disease_type_id: '0',
                any_history_family_answer_arry: [],
                any_history_family_answer: '',
                any_surgery: '',
                any_surgery_date: '',
                complication: '',  
                history_allergy_type_id: '0',
                history_allergy: '',
                history_psychological_arry: [],
                history_immunization_arry: [],
                history_immunization: '',

                // SKRINING GIZI
                body_mass_index: '',
                patient_food_intake: '',
                patient_certain_diets: '',
                six_month_lose_weight: '', // belom ada di postman
                lose_weight: '',
                patient_disease_condition: '',
                nutrition_total_score: '0',

                // SKALA NYERI
                sore_complaints_type_id: '0',
                sore_complaints: '',
                sore_goes_away_arry: [],
                sore_goes_away: '',
                sore_method_type_id: '',
                wong_baker_score: '', // belom ada di postman
                nrs_score: '', // belom ada di postman
                tell_doctor_type_id: '0',
                tell_doctor_date: '',
                face_score: '',
                legs_score: '',
                activity_score: '',
                cry_score: '',
                consolability_score: '',
                sore_total_score: '0',
                duration: '',
                location: '',
                response: '',
                frequency: '',

                // PENGKAJIAN RISIKO JATUH
                fall_risk_method_type_id: '',
                fall_risk_total_score: '',
                fall_risk_type_id: '',
                nursing_diagnoses_arry: [],
                plan_diit: '',
                plan_activity: '',
                plan_medicine: '',
                plan_recontrol: '',
                implementation_arry: [],
            },

        }
    },
    watch: {
        trigger_clear_subnurse(value){
            // console.log('run by child at: ', value)
            this.resetForm()
        },
        //  =========================  PERNAH DIRAWAT SEBELUMNYA  =========================
        "form.been_hospitalized_type_id"(newValue) {
            if (newValue !== "1") {
                this.form.been_hospitalized_answer = ""
            }
        },
        
        //  =========================  RIWAYAT PENYAKIT KELUARGA  =========================
        "form.any_history_family_disease_type_id"(newValue) {
            if (newValue !== "1") {
                this.form.any_history_family_answer_arry = []
                this.form.any_history_family_answer = ""
            }
        },

        "form.any_history_family_answer_arry"(newValues) {
            if (newValues && !newValues.includes("g")) {
                this.form.any_history_family_answer = ""
            }
        },
        
        //  =========================  RIWAYAT ALERGI  =========================
        "form.history_allergy_type_id"(newValue) {
            if (newValue !== "1") {
                this.form.history_allergy = ""
            }
        },
        
        //  =========================  RIWAYAT IMUNISASI  =========================
        "form.history_immunization_arry"(newValues) {
            if (newValues && !newValues.includes("q")) {
                this.form.history_immunization = ""
            }
        },

        //  =========================  SKRINING GIZI  =========================
        "form.six_month_lose_weight"(newValue) {
            if (newValue !== "3") {
                this.form.lose_weight = ""
            }
        },

        //  =========================  SKALA NYERI  =========================
        "form.sore_complaints_type_id"(newValue) {
            if (newValue !== "1") {
                // complaints
                this.form.sore_complaints = ""
                // sore
                this.form.sore_goes_away_arry = []
                this.form.sore_goes_away = ""
                this.form.sore_method_type_id = ""
                this.form.sore_total_score = "0"
                // sore_method_vas
                this.form.face_score = ""
                this.form.legs_score = ""
                this.form.activity_score = ""
                this.form.cry_score = ""
                this.form.consolability_score = ""
                // sore_method_nrs
                this.form.tell_doctor_type_id = "0"
                this.form.tell_doctor_date = ""
            }
        },
        "form.sore_goes_away_arry"(newValues) {
            if (newValues && !newValues.includes("e")) {
                this.form.sore_goes_away = ""
            }
        },
        "form.sore_method_type_id"(newValue) {
            if (newValue !== "c") {
                // console.log('clear VAS');
                this.form.face_score = ""
                this.form.legs_score = ""
                this.form.activity_score = ""
                this.form.cry_score = ""
                this.form.consolability_score = ""
            }
            if (newValue !== "e") {
                // console.log('clear wong baker')
                this.form.wong_baker_score = ""
                this.form.sore_total_score = "0"
            }
            if (newValue !== "f") {
                // console.log('clear NRS');
                this.form.nrs_score = ""
                this.form.tell_doctor_type_id = "0"
                this.form.tell_doctor_date = ""
                this.form.sore_total_score = "0"
            }
        },
    },
    methods: {
        handleNutritionScore() {
            const score_body_mass_index = this.getBodyMassIndexScore(this.form.body_mass_index)
            const score_six_month_lose_weight = this.getSixMonthLoseWeightScore(this.form.six_month_lose_weight)
            const score_lose_weight = this.getLoseWeightScore(this.form.lose_weight)
            const score_patient_food_intake = this.getPatientFoodIntakeScore(this.form.patient_food_intake)
            const score_patient_disease_condition = this.getPatientDiseaseCondition(this.form.patient_disease_condition)
            const score_patient_certain_diets = this.getPatientCertainDiets(this.form.patient_certain_diets)
            
            let result = 0
            result = score_body_mass_index + score_six_month_lose_weight + score_lose_weight + score_patient_food_intake + score_patient_disease_condition + score_patient_certain_diets
            this.form.nutrition_total_score = result.toString()
        },
        getBodyMassIndexScore(value){
            let score = 0
            if (value !== '') {
                if (value == '1') {
                    return score = 0
                } else if (value == '2') {
                    return score = 1
                } else if (value == '3') {
                    return score = 2
                }
            }
            return score = 0
        },
        getSixMonthLoseWeightScore(value){
            let score = 0
            if (value !== '') {
                if (value == '1') {
                    return score = 0
                } else if (value == '2') {
                    return score = 2
                } else if (value == '3') {
                    return score = 0
                }
            }
            return score = 0
        },
        getLoseWeightScore(value){
            let score = 0
            if (value !== '') {
                if (value == '1') {
                    return score = 1
                } else if (value == '2') {
                    return score = 2
                } else if (value == '3') {
                    return score = 3
                } else if (value == '4') {
                    return score = 4
                } else if (value == '5') {
                    return score = 2
                }
            }
            return score = 0
        },
        getPatientFoodIntakeScore(value){
            let score = 0
            if (value !== '') {
                if (value == '1') {
                    return score = 0
                } else if (value == '2') {
                    return score = 1
                } else if (value == '3') {
                    return score = 2
                }
            }
            return score = 0
        },
        getPatientDiseaseCondition(value){
            let score = 0
            if (value !== '') {
                if (value == '1') {
                    return score = 0
                } else if (value == '2') {
                    return score = 2
                }
            }
            return score = 0
        },
        getPatientCertainDiets(value){
            let score = 0
            if (value !== '') {
                if (value == '1') {
                    return score = 0
                } else if (value == '2') {
                    return score = 2
                }
            }
            return score = 0
        },
        handleSoreMethodScore () {
            if (this.form.wong_baker_score !== '') {
                this.form.sore_total_score = this.form.wong_baker_score
            }
            
            if (this.form.nrs_score !== '') {
                this.form.sore_total_score = this.form.nrs_score
            }
        },
        addNewDiagnosa() {
            this.diagnosa.arrDiagnosa.push(this.diagnosa.newInput);
            this.diagnosa.newInput = '';
        },
        addNewImplement() {
            this.implement.arrImplement.push(this.implement.newInput);
            this.implement.newInput = '';
        },
        resetForm(){
            this.form.been_hospitalized_type_id = '0'
            this.form.been_hospitalized_answer = ''
            this.form.any_history_family_disease_type_id = '0'
            this.form.any_history_family_answer_arry = []
            this.form.any_history_family_answer = ''
            this.form.any_surgery = ''
            this.form.any_surgery_date = ''
            this.form.complication = ''  
            this.form.history_allergy_type_id = '0'
            this.form.history_allergy = ''
            this.form.history_psychological_arry = []
            this.form.history_immunization_arry = [],
            this.form.history_immunization = ''

            // SKRINING GIZI
            this.form.body_mass_index = ''
            this.form.patient_food_intake = ''
            this.form.patient_certain_diets = ''
            this.form.six_month_lose_weight = '' // belom ada di postman
            this.form.lose_weight = ''
            this.form.patient_disease_condition = ''
            this.form.nutrition_total_score = '0'

            // SKALA NYERI
            this.form.sore_complaints_type_id = '0'
            this.form.sore_complaints = ''
            this.form.sore_goes_away_arry = []
            this.form.sore_goes_away = ''
            this.form.sore_method_type_id = ''
            this.form.wong_baker_score = '' // belom ada di postman
            this.form.nrs_score = '' // belom ada di postman
            this.form.tell_doctor_type_id = '0'
            this.form.tell_doctor_date = ''
            this.form.face_score = ''
            this.form.legs_score = ''
            this.form.activity_score = ''
            this.form.cry_score = ''
            this.form.consolability_score = ''
            this.form.sore_total_score = '0'
            this.form.duration = ''
            this.form.location = ''
            this.form.response = ''
            this.form.frequency = ''

            // PENGKAJIAN RISIKO JATUH
            this.form.fall_risk_method_type_id = ''
            this.form.fall_risk_total_score = ''
            this.form.fall_risk_type_id = ''
            this.form.nursing_diagnoses_arry = []
            this.form.plan_diit = ''
            this.form.plan_activity = ''
            this.form.plan_medicine = ''
            this.form.plan_recontrol = ''
            this.form.implementation_arry = []
        },
    }
}
</script>
<style lang="scss" scoped>
@import '@core/scss/vue/libs/vue-select.scss';

.timeline-wrapper{
    // height: 100;
    background: rgb(8,140,32);
    background: -moz-linear-gradient(90deg, rgba(8,140,32,1) 0%, rgba(255,253,0,1) 50%, rgba(255,0,0,1) 100%);
    background: -webkit-linear-gradient(90deg, rgba(8,140,32,1) 0%, rgba(255,253,0,1) 50%, rgba(255,0,0,1) 100%);
    background: linear-gradient(90deg, rgba(8,140,32,1) 0%, rgba(255,253,0,1) 50%, rgba(255,0,0,1) 100%);
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#088c20",endColorstr="#ff0000",GradientType=1);
}

.devider {
    border-left: 1px solid #ffffff55;
    border-right: 1px solid #ffffff55;
    // padding: 0 10px;
}
</style>