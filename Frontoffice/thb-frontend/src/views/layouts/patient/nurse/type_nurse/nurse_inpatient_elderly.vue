<template>
    <div>
        <section>
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Riwayat Kesehatan</div>
            <div class="row mt-20">
                <section class="col-lg-6">
                    <div>
                        <ModernLabel label="Riwayat penyakit dahulu/ sebelumnya" />
                        <b-form-radio-group v-model="form.illness_history_type_id" class="mt-8 mt-lg-20">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya, Penyakit</b-form-radio>
                        </b-form-radio-group>
                        <b-form-input v-model="form.illness_history" :disabled="form.illness_history_type_id !== '1'" class="mt-10 col-lg-8" />
                    </div>
                    <div class="mt-32">
                        <ModernLabel label="Riwayat Alergi" />
                        <b-form-radio-group v-model="form.allergy_history_type_id" class="mt-8 mt-lg-20">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya, sebutkan</b-form-radio>
                        </b-form-radio-group>
                        <b-form-input v-model="form.allergy_history" :disabled="form.allergy_history_type_id !== '1'" class="mt-10 col-lg-8" />
                    </div>
                    <div class="mt-32">
                        <ModernLabel label="Pernah Dirawat sebelumnya" />
                        <b-form-radio-group v-model="form.been_hospitalized_type_id" class="mt-8 mt-lg-20">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya, dimana?</b-form-radio>
                        </b-form-radio-group>
                        <b-form-input v-model="form.been_hospitalized_answer" :disabled="form.been_hospitalized_type_id !== '1'" class="mt-10 col-lg-8" />
                    </div>
                    <div class="mt-32">
                        <ModernLabel label="Ketergantungan Terhadap" />
                        <b-form-radio-group v-model="form.dependence_type_id" class="mt-8 mt-lg-20">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya, Sebutkan</b-form-radio>
                        </b-form-radio-group>
                        <b-form-input v-model="form.dependence" :disabled="form.dependence_type_id !== '1'" class="mt-10 col-lg-8" />
                    </div>
                </section>
                <section class="col-lg-6 mt-20 mt-lg-0">
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
                </section>
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
                    <b-form-input v-model="form.sore_complaints" :disabled="form.sore_complaints_type_id !== '1'" class="mt-10 col-lg-8" />
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
                    <b-form-input v-model="form.sore_goes_away" :disabled="form.sore_complaints_type_id !== '1' || form.sore_goes_away_arry.indexOf('e') === -1" class="mt-10 col-lg-8" />
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
            <div class="mt-20">
                <ModernLabel label="Kualitas Nyeri" />
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
                        <b-form-checkbox-group v-model="form.next_nrs_score" size="sm">
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
                        <b-form-input v-model="form.tell_doctor_date" :disabled="form.tell_doctor_type_id == '0'" type="time" class="mt-10"/>
                    </div>
                </div>
            </div>
            <div class="row mt-20">
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Skor</label>
                    <b-input-group prepend="Skor">
                        <b-form-input v-model="form.sore_total_score" type="number" />
                    </b-input-group>
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Durasi</label>
                    <b-form-input v-model="form.duration" type="number" />
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Lokasi</label>
                    <b-form-input v-model="form.location" />
                </div>
            </div>
            <div class="row mt-20">
                <div class="col-lg-4 mt-20">
                    <label class="c-form-label">Kualitas Nyeri</label>
                    <b-form-checkbox-group v-model="form.sore_quality_arry" size="sm" class="mt-10 mt-lg-0">
                        <b-form-checkbox value="a">Tertusuk</b-form-checkbox>
                        <b-form-checkbox value="b">Terbakar</b-form-checkbox>
                        <b-form-checkbox value="c">Tertekan</b-form-checkbox>
                    </b-form-checkbox-group>
                </div>
                <div class="col-lg-4 mt-20">
                    <label class="c-form-label">Frekuensi Nyeri</label>
                    <b-input-group append="x/hari">
                        <b-form-input v-model="form.frequency" type="number"></b-form-input>
                    </b-input-group>
                </div>
                <div class="col-lg-4 mt-20">
                    <label class="c-form-label">Nyeri Mempengaruhi</label>
                    <b-form-checkbox-group v-model="form.sore_affects_arry" size="sm" class="mt-10 mt-lg-0">
                        <b-form-checkbox value="a">Nyeri Mempengaruhi</b-form-checkbox>
                        <b-form-checkbox value="b">Tidur</b-form-checkbox>
                        <b-form-checkbox value="c">Aktifitas Fisik</b-form-checkbox>
                        <b-form-checkbox value="d">Emosi</b-form-checkbox>
                        <b-form-checkbox value="e">Nafsu Makan</b-form-checkbox>
                        <b-form-checkbox value="f">Lain-lain</b-form-checkbox>
                    </b-form-checkbox-group>
                    <b-form-input v-model="form.sore_affects" class="col" :disabled="form.sore_affects_arry.indexOf('f') === -1"/>
                </div>
            </div>
        </section>

        <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Status Fungsional
            </div>
            <div class="row mt-lg-20">
                <div class="col-lg-6 mt-20 mt-lg-0">
                    <ModernLabel label="Sensorik" />
                    <div class="mt-12">
                        <label class="c-form-label">Penglihatan</label>
                        <b-form-radio-group size="sm" v-model="form.sensoric_vision_type_id">
                            <b-form-radio value="a">Normal</b-form-radio>
                            <b-form-radio value="b">Kabur</b-form-radio>
                            <b-form-radio value="c">Kacamata</b-form-radio>
                            <b-form-radio value="d">Lensa Kotak</b-form-radio>
                        </b-form-radio-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Penciuman</label>
                        <b-form-radio-group size="sm" v-model="form.sensoric_olfactory_type_id" >
                            <b-form-radio value="a">Normal</b-form-radio>
                            <b-form-radio value="b">Tidak</b-form-radio>
                        </b-form-radio-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Pendengaran</label>
                        <b-form-radio-group size="sm" v-model="form.sensoric_hearing_type_id" > 
                            <b-form-radio value="a">Normal</b-form-radio>
                            <b-form-radio value="b">Tuli kanan/kiri</b-form-radio>
                            <b-form-radio value="c">Alat bantu dengar kanan/kiri</b-form-radio>
                        </b-form-radio-group>
                    </div>
                </div>
                <div class="col-lg-6 mt-20 mt-lg-0">
                    <div>
                        <ModernLabel label="Kognitif" />
                        <b-form-radio-group v-model="form.cognitive_arry" size="sm" class="mt-10 mt-lg-20">
                            <b-form-radio value="a">Orientasi penuh</b-form-radio>
                            <b-form-radio value="b">Pelupa</b-form-radio>
                            <b-form-radio value="c">Bingung</b-form-radio>
                            <b-form-radio value="d">Tidak dapat mengerti</b-form-radio>
                        </b-form-radio-group>
                    </div>
                    <div class="mt-32">
                        <ModernLabel label="Motorik" />
                        <div class="mt-10 mt-lg-20">
                            <label class="c-form-label">Aktifitas Sehari-hari</label>
                            <b-form-radio-group v-model="form.motor_daily_arry" size="sm">
                                <b-form-radio value="Mandiri">Mandiri</b-form-radio>
                                <b-form-radio value="Bantuan Minimal">Bantuan Minimal</b-form-radio>
                                <b-form-radio value="Bantuan Sebagian">Bantuan Sebagian</b-form-radio>
                                <b-form-radio value="Ketergantungan Total">Ketergantungan Total</b-form-radio>
                            </b-form-radio-group>
                        </div>
                        <div class="mt-10 mt-lg-20">
                            <label class="c-form-label">Berjalan</label>
                            <b-form-radio-group v-model="form.motor_walking_arry" size="sm">
                                <b-form-radio value="a">Tidak ada kesulitan</b-form-radio>
                                <b-form-radio value="b">Sering jatuh</b-form-radio>
                                <b-form-radio value="c">Perlu bantuan</b-form-radio>
                                <b-form-radio value="d">Kelumpuhan</b-form-radio>
                            </b-form-radio-group>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Skala Braden
            </div>
            <div class="mt-10 mt-lg-20">
                <b-table-simple responsive small bordered>
                    <b-thead class="bg-neutral-100 text-bold-14">
                        <b-tr>
                            <b-th class="text-center">No</b-th>
                            <b-th>Parameter</b-th>
                            <b-th class="text-center">Skor</b-th>
                            <b-th>Kondisi</b-th>
                        </b-tr>
                    </b-thead>
                    <b-tbody class="text-regular-12">
                        <b-tr>
                            <b-td rowspan="4" class="text-center">1</b-td>
                            <b-td rowspan="4">Aktifitas</b-td>
                            <b-td class="text-center"><b-form-radio v-model="form.braden_scale_activity" size="sm" value="1">1</b-form-radio></b-td>
                            <b-td>Bedrest</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td class="text-center"><b-form-radio v-model="form.braden_scale_activity" size="sm" value="2">2</b-form-radio></b-td>
                            <b-td>Mobilitas dengan kursi roda</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td class="text-center"><b-form-radio v-model="form.braden_scale_activity" size="sm" value="3">3</b-form-radio></b-td>
                            <b-td>Kadang-kadang berjalan</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td class="text-center"><b-form-radio v-model="form.braden_scale_activity" size="sm" value="4">4</b-form-radio></b-td>
                            <b-td>Sering berjalan</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td rowspan="4" class="text-center">2</b-td>
                            <b-td rowspan="4">Mobilitas</b-td>
                            <b-td class="text-center"><b-form-radio v-model="form.braden_scale_mobilization" size="sm" value="1">1</b-form-radio></b-td>
                            <b-td>Immobilisasi</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td class="text-center"><b-form-radio v-model="form.braden_scale_mobilization" size="sm" value="2">2</b-form-radio></b-td>
                            <b-td>Bergerak sangat terbatas dan memerlukan bantuan minimal</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td class="text-center"><b-form-radio v-model="form.braden_scale_mobilization" size="sm" value="3">3</b-form-radio></b-td>
                            <b-td>Sedikit keterbatasan tetapi dapat bergerak tanpa bantuan orang lain</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td class="text-center"><b-form-radio v-model="form.braden_scale_mobilization" size="sm" value="4">4</b-form-radio></b-td>
                            <b-td>Tanpa keterbatasan</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td rowspan="4" class="text-center">3</b-td>
                            <b-td rowspan="4">Nutrisi</b-td>
                            <b-td class="text-center"><b-form-radio v-model="form.braden_scale_nutrition" size="sm" value="1">1</b-form-radio></b-td>
                            <b-td>Sangat buruk</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td class="text-center"><b-form-radio v-model="form.braden_scale_nutrition" size="sm" value="2">2</b-form-radio></b-td>
                            <b-td>Kurang adekuat</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td class="text-center"><b-form-radio v-model="form.braden_scale_nutrition" size="sm" value="3">3</b-form-radio></b-td>
                            <b-td>Cukup</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td class="text-center"><b-form-radio v-model="form.braden_scale_nutrition" size="sm" value="4">4</b-form-radio></b-td>
                            <b-td>Sangat baik</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td rowspan="3" class="text-center">4</b-td>
                            <b-td rowspan="3">Gesekan</b-td>
                            <b-td class="text-center"><b-form-radio v-model="form.braden_scale_friction" size="sm" value="1">1</b-form-radio></b-td>
                            <b-td>Masalah</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td class="text-center"><b-form-radio v-model="form.braden_scale_friction" size="sm" value="2">2</b-form-radio></b-td>
                            <b-td>Potensi bermasalah</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td class="text-center"><b-form-radio v-model="form.braden_scale_friction" size="sm" value="3">3</b-form-radio></b-td>
                            <b-td>Tidak ada masalah yang berarti</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td rowspan="4" class="text-center">5</b-td>
                            <b-td rowspan="4">Sensori Persepsi</b-td>
                            <b-td class="text-center"><b-form-radio v-model="form.braden_scale_perception" size="sm" value="1">1</b-form-radio></b-td>
                            <b-td>Tidak ada respon terhadap stimulasi nyeri</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td class="text-center"><b-form-radio v-model="form.braden_scale_perception" size="sm" value="2">2</b-form-radio></b-td>
                            <b-td>Respon hanya bila dilakukan stimulasi nyeri</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td class="text-center"><b-form-radio v-model="form.braden_scale_perception" size="sm" value="3">3</b-form-radio></b-td>
                            <b-td>Respon terhadap perintah</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td class="text-center"><b-form-radio v-model="form.braden_scale_perception" size="sm" value="4">4</b-form-radio></b-td>
                            <b-td>Normal</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td rowspan="4" class="text-center">6</b-td>
                            <b-td rowspan="4">Kelembaban</b-td>
                            <b-td class="text-center"><b-form-radio v-model="form.braden_scale_humidity" size="sm" value="1">1</b-form-radio></b-td>
                            <b-td>Selalu lembab</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td class="text-center"><b-form-radio v-model="form.braden_scale_humidity" size="sm" value="2">2</b-form-radio></b-td>
                            <b-td>Selalu kering</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td class="text-center"><b-form-radio v-model="form.braden_scale_humidity" size="sm" value="3">3</b-form-radio></b-td>
                            <b-td>Kadang lembab</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td class="text-center"><b-form-radio v-model="form.braden_scale_humidity" size="sm" value="4">4</b-form-radio></b-td>
                            <b-td>Jarang lembab</b-td>
                        </b-tr>
                    </b-tbody>
                </b-table-simple>
            </div>
        </section>
        <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Nutrisi
            </div>
            <div class="mt-20">
                <div>
                    <p class="mb-0 text-medium-14">Skrining gizi (berdasarkan <span class="font-italic font-weight-bold">Malnutrition Screening Tools</span>)</p>
                    <p class="mb-0 text-medium-14 mt-10">(bila skor > 2 dan atau pasien dengan diagnosis / kondisi khusus dilakukan pengkajian lanjut oleh dietisien)</p>
                </div>
                <div class="mt-20">
                    <b-table-simple responsive small>
                        <b-thead class="text-bold-14 bg-neutral-200">
                            <b-tr>
                                <b-th class="text-center">No</b-th>
                                <b-th >Parameter</b-th>
                                <b-th style="width: 250px;">Skor</b-th>
                            </b-tr>
                        </b-thead>
                        <b-tbody class="text-regular-14">
                            <b-tr>
                                <b-td class="text-center">1</b-td>
                                <b-td colspan="2">Apakah pasien mengalami penurunan berat badan yang tidak diinginkan dalam 6 bulan terakhir?</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td></b-td>
                                <b-td>a. Tidak penurunan beratbadan</b-td>
                                <b-td>0</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td></b-td>
                                <b-td>b. Tidak yakin/tidak tahu/terasa baju lebih longgar</b-td>
                                <b-td>2</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td></b-td>
                                <b-td>c. Jika ya, berapa penurunan berat badan tersebut</b-td>
                                <b-td>-</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td></b-td>
                                <b-td>1-5 kg</b-td>
                                <b-td>1</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td></b-td>
                                <b-td>6-10 kg</b-td>
                                <b-td>2</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td></b-td>
                                <b-td>11-15 kg</b-td>
                                <b-td>3</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td></b-td>
                                <b-td>>15 kg</b-td>
                                <b-td>4</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td></b-td>
                                <b-td>Tidak yakin penurunannya</b-td>
                                <b-td>2</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td class="text-center">2</b-td>
                                <b-td colspan="2">Apakah asupan makan berkurang karena berkurangnya nafsu makan?</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td></b-td>
                                <b-td>a. Tidak</b-td>
                                <b-td>0</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td></b-td>
                                <b-td>b. Ya</b-td>
                                <b-td>1</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td>--------------+</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td></b-td>
                                <b-td class="text-right">Total skor</b-td>
                                <b-td>
                                    <b-form-input v-model="form.nutrition_total_score" type="number" placeholder="-"></b-form-input>
                                </b-td>
                            </b-tr>
                        </b-tbody>
                    </b-table-simple>
                </div>
            </div>
        </section>
        <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Sosial Budaya
            </div>
            <div class="mt-20">
                <label class="c-form-label">Keyakinan / Nilai-nilai budaya</label>
                <b-form-checkbox-group v-model="form.sociocultural_arry" size="sm" stacked class="mt-10 mt-lg-0">
                    <b-form-checkbox value="a">Tidak bersedia di transfusi</b-form-checkbox>
                    <b-form-checkbox value="b">Tidak bersedia di imunisasi/vaksin</b-form-checkbox>
                    <b-form-checkbox value="c">Tidak bersedia di terapi lavenoc</b-form-checkbox>
                    <b-form-checkbox value="d">Tidak bersedia diberikan makanan hewani</b-form-checkbox>
                    <b-form-checkbox value="e">Tidak bersedia dipulangkan pada hari tertentu, sebutkan harinya</b-form-checkbox>
                    <b-form-input v-model="form.sociocultural_days" v-if="form.sociocultural_arry.indexOf('e') !== -1" :disabled="form.sociocultural_arry.indexOf('e') === -1" class="my-10 col-lg-8" />
                    <b-form-checkbox value="f">Lain-lain</b-form-checkbox>
                    <b-form-input v-model="form.sociocultural_other" v-if="form.sociocultural_arry.indexOf('f') !== -1" class="mt-10 col-lg-8" :disabled="form.sociocultural_arry.indexOf('f') === -1" />
                </b-form-checkbox-group>
            </div>
            
        </section>
        <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Pengkajian Risiko Jatuh
            </div>
            <div class="mt-20">
                <label class="c-form-label">Pengkajian risiko jatuh menggunakan</label>
                <b-form-checkbox-group v-model="form.fall_risk_method_arry" size="sm" class="mt-10 mt-lg-0">
                    <b-form-checkbox value="a">Sydney scoring (lanjutkan ke form)</b-form-checkbox>
                </b-form-checkbox-group>
            </div>
        </section>
        <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Kebutuhan Komunikasi / Edukasi dan Pengajaran
            </div>
            <div class="row mt-20">
                <div class="col-lg-6">
                    <div>
                        <label class="c-form-label">Bahasa sehari-hari</label>
                        <b-form-checkbox-group v-model="form.language_arry" size="sm" stacked class="mt-10 mt-lg-0">
                            <b-form-checkbox value="a">Indonesia</b-form-checkbox>
                            <b-form-checkbox value="b">Daerah</b-form-checkbox>
                            <b-form-input v-model="form.local_language" v-if="form.language_arry.indexOf('b') !== -1" :disabled="form.language_arry.indexOf('b') === -1" class="my-10 col-lg-8" />
                            <b-form-checkbox value="c">Lain-lain</b-form-checkbox>
                            <b-form-input v-model="form.other_language" v-if="form.language_arry.indexOf('c') !== -1" :disabled="form.language_arry.indexOf('c') === -1" class="mt-10 col-lg-8" />
                        </b-form-checkbox-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Ekspresi dan emosi</label>
                        <b-form-checkbox-group v-model="form.expression_emotion_arry" size="sm" class="mt-10 mt-lg-0">
                            <b-form-checkbox value="a">Cemas</b-form-checkbox>
                            <b-form-checkbox value="b">Takut</b-form-checkbox>
                            <b-form-checkbox value="c">Sedih</b-form-checkbox>
                            <b-form-checkbox value="d">Gelisah</b-form-checkbox>
                            <b-form-checkbox value="e">Tegang</b-form-checkbox>
                            <b-form-checkbox value="f">Marah</b-form-checkbox>
                        </b-form-checkbox-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Hubungan dengan keluarga</label>
                        <b-form-checkbox-group v-model="form.relationship_family_arry" size="sm" class="mt-10 mt-lg-0">
                            <b-form-checkbox value="a">Harmonis</b-form-checkbox>
                            <b-form-checkbox value="b">Biasa</b-form-checkbox>
                            <b-form-checkbox value="c">Terganggu</b-form-checkbox>
                            <b-form-checkbox value="d">Lain-lain</b-form-checkbox>
                        </b-form-checkbox-group>
                        <b-form-input v-model="form.relationship_family" class="mt-10 col-lg-8" :disabled="form.relationship_family_arry.indexOf('d') === -1" />
                    </div>
                </div>
                <div class="col-lg-6 mt-12 mt-lg-0">
                    <div>
                        <label class="c-form-label">Proses berfikir</label>
                        <b-form-checkbox-group v-model="form.thought_process_arry" size="sm" class="mt-10 mt-lg-0">
                            <b-form-checkbox value="a">Utuh/normal</b-form-checkbox>
                            <b-form-checkbox value="b">Bingung</b-form-checkbox>
                            <b-form-checkbox value="c">Ingatan berkurang</b-form-checkbox>
                        </b-form-checkbox-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Pasien dan keluarga tidak mengerti tentang</label>
                        <b-form-checkbox-group v-model="form.family_not_understand_arry" size="sm" stacked class="mt-10 mt-lg-0">
                            <b-form-checkbox value="a">Penyakit yang diderita</b-form-checkbox>
                            <b-form-checkbox value="b">Tindakan pengobatan yang diberikan</b-form-checkbox>
                            <b-form-checkbox value="c">Diit</b-form-checkbox>
                            <b-form-checkbox value="d">Tindakan pemeriksaan lanjutan</b-form-checkbox>
                            <b-form-checkbox value="e">Manajemen penatalaksaan perawatan penyakit/pasca operasi di rumah</b-form-checkbox>
                        </b-form-checkbox-group>
                    </div>
                </div>
            </div>
        </section>
        <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Masalah Keperawatan
            </div>
            <div class="mt-20">
                <b-form-checkbox-group v-model="form.nursing_problems_arry" size="sm" class="mt-10 mt-lg-0">
                    <b-form-checkbox value="a" class="col-lg-4 my-1 mr-0">Hipertermi</b-form-checkbox>
                    <b-form-checkbox value="b" class="col-lg-4 my-1 mr-0">Kekurangan volume cairan</b-form-checkbox>
                    <b-form-checkbox value="c" class="col-lg-4 my-1 mr-0">Nyeri akut</b-form-checkbox>
                    <b-form-checkbox value="d" class="col-lg-4 my-1 mr-0">Volume cairan berlebihan</b-form-checkbox>
                    <b-form-checkbox value="e" class="col-lg-4 my-1 mr-0">Risiko jatuh</b-form-checkbox>
                    <b-form-checkbox value="f" class="col-lg-4 my-1 mr-0">Nyeri kronis</b-form-checkbox>
                    <b-form-checkbox value="g" class="col-lg-4 my-1 mr-0">Perubahan nutrisi kurang dari kebutuhan</b-form-checkbox>
                    <b-form-checkbox value="h" class="col-lg-4 my-1 mr-0">Penurunan curah jantung</b-form-checkbox>
                    <b-form-checkbox value="i" class="col-lg-4 my-1 mr-0">Kerusakan integritas kulit</b-form-checkbox>
                    <b-form-checkbox value="j" class="col-lg-4 my-1 mr-0">Diare</b-form-checkbox>
                </b-form-checkbox-group>
            </div>
        </section>
        <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Perencanaan Pulang (Discharge Planning)
            </div>
            <div class="mt-20">
                <label class="c-form-label">Kriteria discharge planning</label>
                <b-form-checkbox-group v-model="form.discharge_criteria_arry" size="sm" class="mt-10 mt-lg-0">
                    <b-form-checkbox value="a">Umur > 65 tahun</b-form-checkbox>
                    <b-form-checkbox value="b">Keterbatasan mobilitas</b-form-checkbox>
                    <b-form-checkbox value="c">Perawatan atau pengobatan lanjut</b-form-checkbox>
                    <b-form-checkbox value="d">Bantuan untuk melakukan aktifitas sehari-hari</b-form-checkbox>
                </b-form-checkbox-group>
            </div>
            <div class="mt-20">
                <label class="c-form-label">Bila salah satu jawaban “YA” dari kriteria perencanaan pulang diatas, makan akan dilanjutkan dengan perencanaan pulang sebagai berikut:</label>
                <b-form-checkbox-group v-model="form.home_planning_arry" size="sm" class="mt-10 mt-lg-0">
                    <b-form-checkbox value="a">Perawatan diri (mandi, BAB/BAK)</b-form-checkbox>
                    <b-form-checkbox value="b">Perawatan luka</b-form-checkbox>
                    <b-form-checkbox value="c">Pemberian obat</b-form-checkbox>
                    <b-form-checkbox value="d">Aktivitas (makan, berjalan)</b-form-checkbox>
                    <b-form-checkbox value="e">Diet</b-form-checkbox>
                    <b-form-checkbox value="f">Latihan fisik lanjutan</b-form-checkbox>
                    <b-form-checkbox value="g">Manajemen penatalaksanaan perawatan penyakit/pasca operasi di rumah</b-form-checkbox>
                    <b-form-checkbox value="h">Lain-lain</b-form-checkbox>
                </b-form-checkbox-group>
                <b-form-input v-model="form.home_planning" :disabled="form.home_planning_arry.indexOf('h') === -1" class="col-lg-8 mt-10"/>
            </div>
        </section>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import vSelect from 'vue-select'
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
// import { watch } from 'node_modules/vue/types/v3-generated';

export default {
    name: 'Inpatient_Elderly',
    components: { vSelect, ModernLabel },
    data() {
        return {
            // # TYPE_NURSE_ID X = INAP LANSIA
            form: {
                nurse_id: "",

                // Riwayat Kesehatan
                illness_history_type_id: "",
                illness_history: "",
                allergy_history_type_id: "",
                allergy_history: "",
                been_hospitalized_type_id: "",
                been_hospitalized_answer: "",
                dependence_type_id: "",
                dependence: "",
                any_surgery: "",
                any_surgery_date: "",
                complication: "",
                any_history_family_disease_type_id: "",
                any_history_family_answer_arry: [],
                any_history_family_answer: "",

                // SKALA NYERI
                sore_complaints_type_id: "0",
                sore_complaints: "",
                sore_goes_away_arry: [],
                sore_goes_away: "",
                sore_method_type_id: "",
                face_score: "",
                legs_score: "",
                activity_score: "",
                cry_score: "",
                consolability_score: "",
                nrs_score: "",
                next_nrs_score: [],
                tell_doctor_type_id: "0",
                tell_doctor_date: "",
                sore_total_score: "",
                duration: "",
                location: "",
                sore_quality_arry: [],
                frequency: "",
                sore_affects_arry: [],
                sore_affects: "",

                 // STATUS FUNGSIONAL
                sensoric_vision_type_id: "",
                sensoric_olfactory_type_id: "",
                sensoric_hearing_type_id: "",
                cognitive_arry: [],
                motor_daily_arry: [],
                motor_walking_arry: [],

                // SKALA BRADEN
                braden_scale_activity: "",
                braden_scale_mobilization: "",
                braden_scale_nutrition: "",
                braden_scale_friction: "",
                braden_scale_perception: "",
                braden_scale_humidity: "",
                
                // NUTRISI
                nutrition_total_score: "",

                //  SOSIAL BUDAYA 
                sociocultural_arry: [],
                sociocultural_days: "",
                sociocultural_other: "",

                // PENGKAJIAN RISIKO JATUH
                fall_risk_method_arry: "",

                // Kebutuhan Komunikasi / Edukasi dan Pengajaran
                language_arry: [],
                local_language: "",
                other_language: "",
                expression_emotion_arry: [],
                relationship_family_arry: [],
                relationship_family: "",
                thought_process_arry: [],
                family_not_understand_arry: [],

                nursing_problems_arry: [],

                discharge_criteria_arry: [],
                home_planning_arry: [],
                home_planning: "",

                // ========================
                RadioHistorySick: '0',
                been_hospitalized_type_id: '0',
                been_hospitalized_answer: '',
                any_history_family_answer_arry: [],
                any_history_family_answer: '',
                any_history_family_disease_type_id: '0', 
                any_surgery: '',
                any_surgery_date: '',
                complication: '',
                RadioAllergy: '0',
                RadioDependence: '0',
                RadioPainful: '0',
                CheckPainAway: [],
                RadioMetoda: '',
                CheckFrekNyeri: [],
                CheckPengaruh: [],
                braden_activity: '',
                braden_mobility: '',
                braden_nutrition: '',
                braden_friction: '',
                braden_perception: '',
                braden_humidity: '',
                check_culture: [],
                check_day_language: [],
                check_family: [],
                check_nursing_problems: [],
                check_planning: [],
                check_discard_planning: [],
            },
            

        }
    },
    watch: {
        // Riwayat Kesehatan
        "form.illness_history_type_id"(newValues) {
            if (newValues !== '1') {
                this.form.illness_history = ""
            }
        },
        "form.allergy_history_type_id"(newValues) {
            if (newValues !== '1') {
                this.form.allergy_history = ""
            }
        },
        "form.been_hospitalized_type_id"(newValues) {
            if (newValues !== '1') {
                this.form.been_hospitalized = ""
            }
        },
        "form.dependence_type_id"(newValues) {
            if (newValues !== '1') {
                this.form.dependence = ""
            }
        },
        "form.any_history_family_disease_type_id"(newValues) {
            if (newValues !== '1') {
                this.form.any_history_family_answer_arry = []
                this.form.any_history_family_answer = ""
            }
        },
        "form.any_history_family_answer_arry"(newValues) {
            if (newValues && !newValues.includes("g")) {
                this.form.any_history_family_answer = ""
            }
        },

        // skala nyeri
        "form.sore_complaints_type_id"(newValues) {
            if (newValues !== "1") {
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
                this.form.next_nrs_score = []
                this.form.tell_doctor_type_id = "0"
                this.form.tell_doctor_date = ""
                this.form.sore_total_score = "0"
            }
        },
        "form.tell_doctor_type_id"(newValues) {
            if (newValues !== "1") {
                this.form.tell_doctor_date = ""
            }
        },
        "form.sore_affects_arry"(newValues) {
            if (newValues && !newValues.includes("f")) {
                this.form.sore_affects = ""
            }
        },
        
        // Sosial Budaya
        "form.sociocultural_arry"(newValues) {
            if (newValues && !newValues.includes("e")) {
                this.form.sociocultural_days = ""
            }
            if (newValues && !newValues.includes("f")) {
                this.form.sociocultural_other = ""
            }
        },
        // Kebutuhan Komunikasi / Edukasi dan Pengajaran
        "form.language_arry"(newValues) {
            if (newValues && !newValues.includes("b")) {
                this.form.local_language = ""
            }
            if (newValues && !newValues.includes("c")) {
                this.form.other_language = ""
            }
        },
        "form.relationship_family_arry"(newValues) {
            if (newValues && !newValues.includes("d")) {
                this.form.relationship_family = ""
            }
        },

        "form.home_planning_arry"(newValues) {
            if (newValues && !newValues.includes("h")) {
                this.form.home_planning = ""
            }
        },

        
    },
    methods: {
        // resetForm(){
        //     this.form.isolation_criteria = ''
        // },

    }
}
</script>
<style lang="scss">@import '@core/scss/vue/libs/vue-select.scss';</style>