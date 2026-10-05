<template>
    <div>
        <section>
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Riwayat Psikososial dan Spiritual
            </div>
            <div class="row mt-16 mt-lg-20">
                <div class="col-lg-6">
                    <ModernLabel label="Status Psikologis" />
                    <b-form-checkbox-group size="sm" v-model="form.psychological_status_arry" class="mt-10 mt-lg-20">
                        <b-form-checkbox value="a">Cemas</b-form-checkbox>
                        <b-form-checkbox value="b">Takut</b-form-checkbox>
                        <b-form-checkbox value="c">Marah</b-form-checkbox>
                        <b-form-checkbox value="d">Sedih</b-form-checkbox>
                        <b-form-checkbox value="e">Cenderung ingin bunuh diri</b-form-checkbox>
                        <b-form-checkbox value="f">Lain-lain</b-form-checkbox>
                    </b-form-checkbox-group>
                    <b-form-input v-model="form.psychological_status" :disabled="form.psychological_status_arry.indexOf('f') === -1" class="mt-10 col-lg-8"  />
                </div>
                <div class="col-lg-6 mt-32 mt-lg-0">
                    <ModernLabel label="Status Mental" />
                    <b-form-checkbox-group stacked size="sm" v-model="form.mental_status_arry" class="mt-10 mt-lg-20">
                        <b-form-checkbox value="a" class="my-1">Sadar dan orientasi baik</b-form-checkbox>
                        <b-form-checkbox value="b" class="my-1 my-lg-auto">Ada masalah perilaku, sebutkan</b-form-checkbox>
                        <b-form-input v-model="form.mental_status_problems" :disabled="form.mental_status_arry.indexOf('b') === -1" class="mt-10 col-lg ml-auto" />
                        <b-form-checkbox value="c" class="my-1">Perilaku kekerasan yang dialami pasien sebelumnya</b-form-checkbox>
                        <b-form-input v-model="form.mental_status_violence" :disabled="form.mental_status_arry.indexOf('c') === -1" class="mt-10 col-lg" />
                    </b-form-checkbox-group>
                </div>
            </div>
            <div class="mt-32">
                <ModernLabel label="Status Spiritual" />
                <div class="row mt-16 mt-lg-20">
                    <div class="col-lg">
                        <label class="c-form-label">Kegiatan keagamaan yang biasa dilakukan</label>
                        <b-textarea rows="5" v-model="form.spiritual_status_a" />
                    </div>
                    <div class="col-lg mt-12 mt-lg-0">
                        <label class="c-form-label">Kegiatan spiritual yang dibutuhkan selama perawatan</label>
                        <b-textarea rows="5" v-model="form.spiritual_status_b" />
                    </div>
                </div>
            </div>
        </section>
        <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Riwayat Nutrisi
            </div>
            <div class="row mt-10 mt-lg-20">
                <div class="col-lg-6">
                    <label class="c-form-label">Pola Makan</label>
                    <b-input-group append="x/hari">
                        <b-form-input type="number" v-model="form.dietary_habit"></b-form-input>
                    </b-input-group>
                </div>
                <div class="col-lg-6 mt-10 mt-lg-0">
                    <label class="c-form-label">Riwayat</label>
                    <b-form-checkbox-group size="sm" v-model="form.nutrition_arry">
                        <b-form-checkbox value="a" class="my-1">Mual</b-form-checkbox>
                        <b-form-checkbox value="b" class="my-1">Muntah</b-form-checkbox>
                        <b-form-checkbox value="c" class="my-1">Sulit Menelan</b-form-checkbox>
                        <b-form-checkbox value="d" class="my-1">Tidak ada masalah</b-form-checkbox>
                        <b-form-checkbox value="e" class="my-1 my-lg-auto">Lain-lain</b-form-checkbox>
                    </b-form-checkbox-group>
                    <b-form-input v-model="form.nutrition" :disabled="form.nutrition_arry.indexOf('e') === -1" class="mt-10" ></b-form-input>
                </div>
            </div>
        </section>
        <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Eliminasi
            </div>
            <div class="row mt-16 mt-lg-20">
                <div class="col-lg-6">
                    <p class="text-medium-14">Eliminasi</p>
                    <div class="mt-10 row">
                        <label class="c-form-label col-lg-4">BAB, Frekuensi</label>
                        <b-input-group append="x/hari" class="col-lg">
                            <b-form-input type="number" v-model="form.elimination_defecate" ></b-form-input>
                        </b-input-group>
                    </div>
                    <div class="mt-10 row">
                        <label class="c-form-label col-lg-4">BAK, Frekuensi</label>
                        <b-input-group append="x/hari" class="col-lg">
                            <b-form-input type="number" v-model="form.elimination_urination" ></b-form-input>
                        </b-input-group>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div>
                        <label class="c-form-label">Konsistensi</label>
                        <b-form-radio-group size="sm" v-model="form.elimination_consistency_type_id" >
                            <b-form-radio value="1">Lunak</b-form-radio>
                            <b-form-radio value="2">Keras</b-form-radio>
                            <b-form-radio value="3">Cair</b-form-radio>
                            <b-form-radio value="4">Ampas</b-form-radio>
                            <b-form-radio value="5">Tidak</b-form-radio>
                        </b-form-radio-group>
                    </div>
                    <div class="mt-10">
                        <label class="c-form-label">Warna</label>
                        <b-form-radio-group size="sm" v-model="form.elimination_color_type_id">
                            <b-form-radio value="1">Kuning Jernih</b-form-radio>
                            <b-form-radio value="2">Kuning Keruh</b-form-radio>
                            <b-form-radio value="3">Lainnya</b-form-radio>
                            <b-form-input class="col-lg-8 mt-lg-10" v-model="form.elimination_color" :disabled="form.elimination_color_type_id !== '3'"></b-form-input>
                        </b-form-radio-group>
                    </div>
                </div>
            </div>
        </section>
        <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Riwayat Imunisasi
            </div>
            <div class="mt-10 mt-lg-20">
                <b-form-checkbox-group stacked size="sm" v-model="form.immunization_arry" class="mt-10 mt-lg-0">
                    <div class="row">
                        <div class="col-lg">
                            <b-form-checkbox value="a" class="my-1">BCG</b-form-checkbox>
                            <b-form-checkbox value="b" class="my-1">Polio nol</b-form-checkbox>
                            <b-form-checkbox value="c" class="my-1">DPT I</b-form-checkbox>
                            <b-form-checkbox value="d" class="my-1">Campak</b-form-checkbox>
                        </div>
                        <div class="col-lg">
                            <b-form-checkbox value="e" class="my-1">Hep B I</b-form-checkbox>
                            <b-form-checkbox value="f" class="my-1">Polio I</b-form-checkbox>
                            <b-form-checkbox value="g" class="my-1">DPT II</b-form-checkbox>
                            <b-form-checkbox value="h" class="my-1">MMR</b-form-checkbox>                            
                        </div>
                        <div class="col-lg">
                            <b-form-checkbox value="i" class="my-1">Hep B II</b-form-checkbox>
                            <b-form-checkbox value="j" class="my-1">Polio II</b-form-checkbox>
                            <b-form-checkbox value="k" class="my-1">DPT III</b-form-checkbox>
                            <b-form-checkbox value="l" class="my-1">HiB</b-form-checkbox>                            
                        </div>
                        <div class="col-lg">
                            <b-form-checkbox value="m" class="my-1">Hep B III</b-form-checkbox>
                            <b-form-checkbox value="n" class="my-1">Polio III</b-form-checkbox>
                            <b-form-checkbox value="o" class="my-1">DPT IV</b-form-checkbox>
                            <b-form-checkbox value="p" class="my-1">Polio IV</b-form-checkbox>
                        </div>
                        <div class="col-lg">
                            <b-form-checkbox value="q" class="my-1">DPT V</b-form-checkbox>
                            <b-form-checkbox value="r" class="my-1">Polio V</b-form-checkbox>
                            <div class="d-flex">
                                <b-form-checkbox value="s" class="my-1 my-lg-auto"></b-form-checkbox>
                                <b-form-input class="mt-10 col-lg" v-model="form.immunization" :disabled="form.immunization_arry.indexOf('s') === -1"></b-form-input>
                            </div>        
                        </div>
                    </div>
                </b-form-checkbox-group>
            </div>
        </section>
        <section id="Imunisasi" class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Riwayat Tumbuh Kembang
            </div>
            <div class="mt-10 mt-lg-20">
                <b-form-checkbox-group stacked size="sm" v-model="form.development_history_arry" class="row mt-10 mt-lg-0">
                    <div class="col-lg-3 my-2">
                        <b-form-checkbox value="a">Senyum</b-form-checkbox>
                        <b-input-group append="bln">
                            <b-form-input type="number" v-model="form.development_smile" :disabled="form.development_history_arry.indexOf('a') === -1"></b-form-input>
                        </b-input-group>
                    </div>
                    <div class="col-lg-3 my-2">
                        <b-form-checkbox value="b">Tengkurap</b-form-checkbox>
                        <b-input-group append="bln">
                            <b-form-input type="number" v-model="form.development_prone" :disabled="form.development_history_arry.indexOf('b') === -1"></b-form-input>
                        </b-input-group>
                    </div>
                    <div class="col-lg-3 my-2">
                        <b-form-checkbox value="c">Duduk</b-form-checkbox>
                        <b-input-group append="bln">
                            <b-form-input type="number" v-model="form.development_sit" :disabled="form.development_history_arry.indexOf('c') === -1"></b-form-input>
                        </b-input-group>
                    </div>
                    <div class="col-lg-3 my-2">
                        <b-form-checkbox value="d">Merangkak</b-form-checkbox>
                        <b-input-group append="bln">
                            <b-form-input type="number" v-model="form.development_crawl" :disabled="form.development_history_arry.indexOf('d') === -1"></b-form-input>
                        </b-input-group>
                    </div>
                    <div class="col-lg-3 my-2">
                        <b-form-checkbox value="e">berdiri</b-form-checkbox>
                        <b-input-group append="bln">
                            <b-form-input type="number" v-model="form.development_stand" :disabled="form.development_history_arry.indexOf('e') === -1"></b-form-input>
                        </b-input-group>
                    </div>
                    <div class="col-lg-3 my-2">
                        <b-form-checkbox value="f">Berjalan</b-form-checkbox>
                        <b-input-group append="bln">
                            <b-form-input type="number" v-model="form.development_walk" :disabled="form.development_history_arry.indexOf('f') === -1"></b-form-input>
                        </b-input-group>
                    </div>
                    <div class="col-lg-3 my-2">
                        <b-form-checkbox value="g">Berbicara</b-form-checkbox>
                        <b-input-group append="bln">
                            <b-form-input type="number" v-model="form.development_speak" :disabled="form.development_history_arry.indexOf('g') === -1"></b-form-input>
                        </b-input-group>
                    </div>
                    <div class="col-lg-3 my-2">
                        <b-form-checkbox value="h">Sekolah</b-form-checkbox>
                        <b-input-group append="bln">
                            <b-form-input type="number" v-model="form.development_school" :disabled="form.development_history_arry.indexOf('h') === -1"></b-form-input>
                        </b-input-group>
                    </div>
                </b-form-checkbox-group>
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
        <section class="mt-32" id="keperawatan">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Skala Jatuh/Humpty Dumpty
            </div>
            <b-form-radio-group v-model="form.humpty_dumpty_scale_type_id" stacked size="sm" class="mt-10 mt-lg-20">
                <b-form-radio value="a" class="mt-10">Skor 7 - 11 Risiko rendah untuk jatuh <span class="text-muted text-regular-12 ml-2">*Skor minimal: 7</span></b-form-radio>
                <b-form-radio value="b" class="mt-10">Skor 7 ≥ 11 Risiko tinggi untuk jatuh <span class="text-muted text-regular-12 ml-2">*Skor maksimal: 23</span></b-form-radio>
            </b-form-radio-group>
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
                style="margin-left:-16px; margin-right:-16px;">Skrining Gizi
            </div>
            <div class="mt-20">
                <label class="c-form-label">(bila skor ≥ 2 dan / atau dengan diagnosa / kondisi khusus dilakukan asesmen lanjut oleh Ahli Gizi)</label>
                <div>
                    <div class="row mt-10">
                        <div class="col-lg-5">
                            <label class="c-form-label w-100">Apakah pasien mengalami penurunan BB dalam 6 bulan terakhir?</label>
                        </div>
                        <div class="col-lg">
                            <b-form-radio-group v-model="form.six_month_lose_weight_type_id"  size="sm" >
                                <b-form-radio class="my-1" value="1">Tidak (skor 0)</b-form-radio>
                                <b-form-radio class="my-1" value="2">Ya/Tidak yakin (skor 2)</b-form-radio>
                            </b-form-radio-group>
                        </div>
                    </div>
                    <div class="row mt-10">
                        <div class="col-lg-5">
                            <label class="c-form-label w-100">Jika ya/tidak yakin, berapa kira-kira penurunan BB tersebut?</label>
                        </div>
                        <div class="col-lg">
                            <b-form-radio-group size="sm" v-model="form.total_lose_weight_type_id" >
                                <b-form-radio class="my-1" value="1">1-5 kg (skor 1)</b-form-radio>
                                <b-form-radio class="my-1" value="2">6-10 kg (skor 2)</b-form-radio>
                                <b-form-radio class="my-1" value="3">11-15 kg (skor 3)</b-form-radio>
                                <b-form-radio class="my-1" value="4">>15 kg (skor 4)</b-form-radio>
                            </b-form-radio-group>
                        </div>
                    </div>
                    <div class="row mt-10">
                        <div class="col-lg-5">
                            <label class="c-form-label w-100">Apakah penurunan BB karena tidak nafsu makan?</label>
                        </div>
                        <div class="col-lg">
                            <b-form-radio-group v-model="form.lack_of_appetite_type_id" size="sm" >
                                <b-form-radio class="my-1" value="1">Tidak (skor 0)</b-form-radio>
                                <b-form-radio class="my-1" value="2">Ya (skor 1)</b-form-radio>
                            </b-form-radio-group>
                        </div>
                    </div>
                    <div class="row mt-10">
                        <div class="col-lg-5">
                            <label class="c-form-label w-100">Total Skor</label>
                        </div>
                        <div class="col-lg">
                            <label class="c-form-label w-100">{{ form.screening_total_score }}</label>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Kriteria Pasien yang Beresiko
            </div>
            <b-form-checkbox-group v-model="form.criteria_patients_at_risk_arry" size="sm" class="px-0 mt-20">
                <b-form-checkbox class="col-lg-4 mr-0 my-1" value="a">Pasien gangguan jiwa</b-form-checkbox>
                <b-form-checkbox class="col-lg-4 mr-0 my-1" value="b">Pasien dengan imun terganggu</b-form-checkbox>
                <b-form-checkbox class="col-lg-4 mr-0 my-1" value="c">Geratri</b-form-checkbox>
                <b-form-checkbox class="col-lg-4 mr-0 my-1" value="d">Pasien ketergantungan obat</b-form-checkbox>
                <b-form-checkbox class="col-lg-4 mr-0 my-1" value="e">Pasien kemoterapi</b-form-checkbox>
                <b-form-checkbox class="col-lg-4 mr-0 my-1" value="f">Bayi baru lahir</b-form-checkbox>
                <b-form-checkbox class="col-lg-4 mr-0 my-1" value="g">Pasien korban penganiayaan</b-form-checkbox>
                <b-form-checkbox class="col-lg-4 mr-0 my-1" value="h">Kebidanan dan kandungan</b-form-checkbox>
                <b-form-checkbox class="col-lg-4 mr-0 my-1" value="i">Anak-anak</b-form-checkbox>
                <b-form-checkbox class="col-lg-4 mr-0 my-1" value="j">Pasien dengan penyakit menular</b-form-checkbox>
                <b-form-checkbox class="col-lg-4 mr-0 my-1" value="k">Pasien paliaktif</b-form-checkbox>
            </b-form-checkbox-group>
        </section>
        <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Kebutuhan Edukasi
            </div>
            <div class="mt-20">
                <div>
                    <label class="c-form-label">Kebutuhan pembelajaran pasien (pilih topik pembelajaran pada kotak yang tersedia)</label>
                    <b-form-checkbox-group v-model="form.educational_needs_arry" size="sm" class="mt-10">
                        <b-form-checkbox value="a">Proses Penyakit</b-form-checkbox>
                        <b-form-checkbox value="b">Terapi/obat</b-form-checkbox>
                        <b-form-checkbox value="c">Perawatan perioperatif</b-form-checkbox>
                        <b-form-checkbox value="d">Teknik fisioterapi</b-form-checkbox>
                        <b-form-checkbox value="e">Nutrisi/diet</b-form-checkbox>
                        <b-form-checkbox value="f">Mengatasi nyeri</b-form-checkbox>
                        <b-form-checkbox value="g">Tindakan keperawatan</b-form-checkbox>
                        <b-form-checkbox value="h">Lain-lain</b-form-checkbox>
                    </b-form-checkbox-group>
                    <b-form-input v-model="form.educational_needs" :disabled="form.educational_needs_arry.indexOf('h') === -1" class="mt-10" />
                </div>
                <div class="mt-20">
                    <label class="c-form-label">Terdapat hambatan dalam pembelajaran</label>
                    <b-form-radio-group v-model="form.educational_obstacle_type_id" size="sm" class="mt-10">
                        <b-form-radio value="1">Tidak</b-form-radio>
                        <b-form-radio value="2">Ya, Terdapat hambatan dalam pembelajaran</b-form-radio>
                    </b-form-radio-group>
                    <b-form-checkbox-group v-model="form.educational_obstacle_arry" :disabled="form.educational_obstacle_type_id != '2'" size="sm" class="col-lg-10 px-0 mt-10">
                        <b-form-checkbox class="my-1" value="a">Pendengaran</b-form-checkbox>
                        <b-form-checkbox class="my-1" value="b">Penglihatan</b-form-checkbox>
                        <b-form-checkbox class="my-1" value="c">Kognitif</b-form-checkbox>
                        <b-form-checkbox class="my-1" value="d">Fisik</b-form-checkbox>
                        <b-form-checkbox class="my-1" value="e">Budaya</b-form-checkbox>
                        <b-form-checkbox class="my-1" value="f">Agama</b-form-checkbox>
                        <b-form-checkbox class="my-1" value="g">Emosi</b-form-checkbox>
                        <b-form-checkbox class="my-1" value="h">Bahasa</b-form-checkbox>
                        <b-form-checkbox class="my-1" value="i">Lainnya</b-form-checkbox>
                    </b-form-checkbox-group>
                    <b-form-input v-model="form.educational_obstacle" :disabled="form.educational_obstacle_type_id != '2' || form.educational_obstacle_arry.indexOf('i') === -1" class="mt-10" />
                </div>
                <div class="mt-20">
                    <label class="c-form-label">Dibutuhkan penerjemah</label>
                    <b-form-radio-group v-model="form.translator_type_id" size="sm">
                        <b-form-radio value="0">Tidak</b-form-radio>
                        <b-form-radio value="1">Ya, Sebutkan</b-form-radio>
                    </b-form-radio-group>
                    <b-form-input v-model="form.translator" :disabled="form.translator_type_id != '1'" class="mt-10" />
                </div>
            </div>
        </section>
        <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">DISCHARGE PLANNING <br/> <i class="text-regular-12">(Dilengkapi dalam 48 jam pertama pasien masuk ruang rawat)</i>
            </div>
            <b-table-simple responsive class="mt-20" style="white-space: nowrap">
                <b-thead class="bg-primary-100 text-bold-14">
                    <b-tr>
                        <b-th style="width:50%">Kebutuhan pelayanan</b-th>
                        <b-th class="text-center">Ya</b-th>
                        <b-th class="text-center">Tidak</b-th>
                        <b-th>Edukasi dan Target Edukasi</b-th>
                    </b-tr>
                </b-thead>
                <b-tbody class="text-regular-14">
                    <b-tr>
                        <b-td>Perlu pelayanan home care</b-td>
                        <b-td class="text-center"><b-form-radio v-model="form.needs_home_care_type_id" value="0"></b-form-radio></b-td>
                        <b-td class="text-center"><b-form-radio v-model="form.needs_home_care_type_id" value="1"></b-form-radio></b-td>
                        <b-td><b-form-input v-model="form.needs_home_care" size="sm"></b-form-input></b-td>
                    </b-tr>
                    <b-tr>
                        <b-td>Perlu pemasangan implant</b-td>
                        <b-td class="text-center"><b-form-radio v-model="form.needs_implant_type_id" value="0"></b-form-radio></b-td>
                        <b-td class="text-center"><b-form-radio v-model="form.needs_implant_type_id" value="1"></b-form-radio></b-td>
                        <b-td><b-form-input v-model="form.needs_implant" size="sm"></b-form-input></b-td>
                    </b-tr>
                    <b-tr>
                        <b-td>Penggunaan alat bantu</b-td>
                        <b-td class="text-center"><b-form-radio v-model="form.needs_aids_type_id" value="0"></b-form-radio></b-td>
                        <b-td class="text-center"><b-form-radio v-model="form.needs_aids_type_id" value="1"></b-form-radio></b-td>
                        <b-td><b-form-input v-model="form.needs_aids" size="sm"></b-form-input></b-td>
                    </b-tr>
                    <b-tr>
                        <b-td>Telah dilakukan pemasangan alat</b-td>
                        <b-td class="text-center"><b-form-radio v-model="form.needs_install_equip_type_id" value="0"></b-form-radio></b-td>
                        <b-td class="text-center"><b-form-radio v-model="form.needs_install_equip_type_id" value="1"></b-form-radio></b-td>
                        <b-td><b-form-input v-model="form.needs_install_equip" size="sm"></b-form-input></b-td>
                    </b-tr>
                    <b-tr>
                        <b-td>Dirunjuk ke komunitas tertentu</b-td>
                        <b-td class="text-center"><b-form-radio v-model="form.needs_community_type_id" value="0"></b-form-radio></b-td>
                        <b-td class="text-center"><b-form-radio v-model="form.needs_community_type_id" value="1"></b-form-radio></b-td>
                        <b-td><b-form-input v-model="form.needs_community" size="sm"></b-form-input></b-td>
                    </b-tr>
                    <b-tr>
                        <b-td>Dirujuk ke tim terapis</b-td>
                        <b-td class="text-center"><b-form-radio v-model="form.needs_therapist_type_id" value="0"></b-form-radio></b-td>
                        <b-td class="text-center"><b-form-radio v-model="form.needs_therapist_type_id" value="1"></b-form-radio></b-td>
                        <b-td><b-form-input v-model="form.needs_therapist" size="sm"></b-form-input></b-td>
                    </b-tr>
                    <b-tr>
                        <b-td>Dirujuk ke ahli gizi</b-td>
                        <b-td class="text-center"><b-form-radio v-model="form.needs_nutrient_type_id" value="0"></b-form-radio></b-td>
                        <b-td class="text-center"><b-form-radio v-model="form.needs_nutrient_type_id" value="1"></b-form-radio></b-td>
                        <b-td><b-form-input v-model="form.needs_nutrient" size="sm"></b-form-input></b-td>
                    </b-tr>
                    <b-tr>
                        <b-td>Lain-lain</b-td>
                        <b-td class="text-center"><b-form-radio v-model="form.needs_other_type_id" value="0"></b-form-radio></b-td>
                        <b-td class="text-center"><b-form-radio v-model="form.needs_other_type_id" value="1"></b-form-radio></b-td>
                        <b-td><b-form-input v-model="form.needs_other" size="sm"></b-form-input></b-td>
                    </b-tr>
                </b-tbody>
            </b-table-simple>
        </section>
        <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Daftar Masalah Keperawatan
            </div>
            <b-form-checkbox-group size="sm" v-model="form.nursing_problems_arry" class="mt-20">
                <b-form-checkbox class="col-lg-3 mr-0 my-1" value="a">Integritas kulit</b-form-checkbox>
                <b-form-checkbox class="col-lg-3 mr-0 my-1" value="b">Jalan nafas/pertukaran gas</b-form-checkbox>
                <b-form-checkbox class="col-lg-3 mr-0 my-1" value="c">Konflik peran orang tua</b-form-checkbox>
                <b-form-checkbox class="col-lg-3 mr-0 my-1" value="d">Keseimbangan cairan/elektrolit</b-form-checkbox>
                <b-form-checkbox class="col-lg-3 mr-0 my-1" value="e">Keselamatan pasien/injuri</b-form-checkbox>
                <b-form-checkbox class="col-lg-3 mr-0 my-1" value="f">Perawatan diri</b-form-checkbox>
                <b-form-checkbox class="col-lg-3 mr-0 my-1" value="g">Perfusi jaringan</b-form-checkbox>
                <b-form-checkbox class="col-lg-3 mr-0 my-1" value="h">Nyeri</b-form-checkbox>
                <b-form-checkbox class="col-lg-3 mr-0 my-1" value="i">Suhu tubuh</b-form-checkbox>
                <b-form-checkbox class="col-lg-3 mr-0 my-1" value="j">Eliminasi</b-form-checkbox>
                <b-form-checkbox class="col-lg-3 mr-0 my-1" value="k">Intoleransi aktifitas</b-form-checkbox>
                <b-form-checkbox class="col-lg-3 mr-0 my-1" value="l">Pola tidur</b-form-checkbox>
                <b-form-checkbox class="col-lg-3 mr-0 my-1" value="m">Mobilitas/aktifitas</b-form-checkbox>
                <b-form-checkbox class="col-lg-3 mr-0 my-1" value="n">Kurang pengetahuan orang tua</b-form-checkbox>
                <b-form-checkbox class="col-lg-3 mr-0 my-1" value="o">Pengangan nutrisi</b-form-checkbox>
                <b-form-checkbox class="col-lg-3 mr-0 my-1" value="p">Tumbuh kembang</b-form-checkbox>
                <b-form-checkbox class="col-lg-3 mr-0 my-1" value="q">Cemas</b-form-checkbox>
                <b-form-checkbox class="col-lg-3 mr-0 my-1" value="r"> Lainnya
                    <b-form-input v-model="form.nursing_problems" :disabled="form.nursing_problems_arry.indexOf('r') === -1"></b-form-input>
                </b-form-checkbox>
            </b-form-checkbox-group>
        </section>

    </div>
</template>
<script>
import { mapState } from 'vuex'
import vSelect from 'vue-select'
import ModernLabel from '@/@core/components/label/ModernLabel.vue';

export default {
    name: 'Inpatient_child',
    components: { vSelect, ModernLabel },
    data() {
        return {
            // # TYPE_NURSE_ID X = INAP ANAK
            form: {
                nurse_id: "",

                // Riwayat Psikososial dan Spiritual
                psychological_status_arry: [],
                psychological_status: "",
                mental_status_arry: [],
                mental_status_problems: "",
                mental_status_violence: "",
                spiritual_status_a: "",
                spiritual_status_b: "",

                // Riwayat Nutrisi
                dietary_habit: "",
                nutrition_arry: [],
                nutrition: "",

                // Eliminasi
                elimination_defecate: "",
                elimination_urination: "",
                elimination_consistency_type_id: "",
                elimination_color_type_id: "",
                elimination_color: "",

                // Riwayat Imunisasi
                immunization_arry: [],
                immunization: "",

                // Riwayat Tumbuh Kembang
                development_history_arry: [],
                development_smile: "",
                development_prone: "",
                development_sit: "",
                development_crawl: "",
                development_stand: "",
                development_walk: "",
                development_speak: "",
                development_school: "",

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

                // HUMPTY DUMPTY
                humpty_dumpty_scale_type_id: "",

                // STATUS FUNGSIONAL
                sensoric_vision_type_id: "",
                sensoric_olfactory_type_id: "",
                sensoric_hearing_type_id: "",
                cognitive_arry: [],
                motor_daily_arry: [],
                motor_walking_arry: [],

                // SKRINING GIZI
                six_month_lose_weight_type_id: "",
                total_lose_weight_type_id: "",
                lack_of_appetite_type_id: "",
                screening_total_score: 0,
                
                // PASIEN BERESIKO
                criteria_patients_at_risk_arry: [],

                // Kebutuhan Edukasi
                educational_needs_arry: [],
                educational_needs: "",
                educational_obstacle_type_id: "",
                educational_obstacle_arry: [],
                educational_obstacle: "",
                translator_type_id: "",
                translator: "",

                // DISCHARGE PLANNING
                needs_home_care_type_id: "",
                needs_home_care: "",
                needs_implant_type_id: "",
                needs_implant: "",
                needs_aids_type_id: "",
                needs_aids: "",
                needs_install_equip_type_id: "",
                needs_install_equip: "",
                needs_community_type_id: "",
                needs_community: "",
                needs_therapist_type_id: "",
                needs_therapist: "",
                needs_nutrient_type_id: "",
                needs_nutrient: "",
                needs_other_type_id: "",
                needs_other: "",

                // Daftar Masalah Keperawatan
                nursing_problems_arry: [],
                nursing_problems: "",
            },

        }
    },
    watch: {
        // Riwayat Psikososial dan Spiritual
        "form.psychological_status_arry"(newValues) {
            if (newValues && !newValues.includes("f")) {
                this.form.psychological_status = ""
            }
        },
        "form.mental_status_arry"(newValues) {
            if (newValues && !newValues.includes("b")) {
                this.form.mental_status_problems = ""
            }
            if (newValues && !newValues.includes("c")) {
                this.form.mental_status_violence = ""
            }
        },

         // Riwayat Nutrisi
        "form.nutrition_arry"(newValues) {
            if (newValues && !newValues.includes("e")) {
                this.form.nutrition = ""
            }
        },

        // Eliminasi
        "form.elimination_color_type_id"(newValues) {
            if (newValues !== "3") {
                this.form.elimination_color = ""
            }
        },

        // Riwayat Imunisasi
        "form.immunization_arry"(newValues) {
            if (newValues && !newValues.includes("s")) {
                this.form.immunization = ""
            }
        },

        // Riwayat Tumbuh Kembang
        "form.development_history_arry"(newValues) {
            if (newValues && !newValues.includes("a")) {
                this.form.development_smile = ""
            }
            if (newValues && !newValues.includes("b")) {
                this.form.development_prone = ""
            }
            if (newValues && !newValues.includes("c")) {
                this.form.development_sit = ""
            }
            if (newValues && !newValues.includes("d")) {
                this.form.development_crawl = ""
            }
            if (newValues && !newValues.includes("e")) {
                this.form.development_stand = ""
            }
            if (newValues && !newValues.includes("f")) {
                this.form.development_walk = ""
            }
            if (newValues && !newValues.includes("g")) {
                this.form.development_speak = ""
            }
            if (newValues && !newValues.includes("h")) {
                this.form.development_school = ""
            }
        },

         // Skala Nyeri
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

        // Kebutuhan Edukasi
        "form.educational_needs_arry"(newValues) {
            if (newValues && !newValues.includes("h")) {
                this.form.educational_needs = ""
            }
        },
        "form.educational_obstacle_type_id"(newValues) {
            if (newValues !== "2") {
                this.form.educational_obstacle_arry = []
                this.form.educational_obstacle = ""
            }
        },
        "form.educational_obstacle_arry"(newValues) {
            if (newValues && !newValues.includes("i")) {
                this.form.educational_obstacle = ""
            }
        },
        "form.translator_type_id"(newValues) {
            if (newValues !== "1") {
                this.form.translator = ""
            }
        },

        // Daftar masalah Keperawatan
        "form.nursing_problems_arry"(newValues) {
            if (newValues && !newValues.includes("r")) {
                this.form.nursing_problems = ""
            }
        },

        // Skrining Gizi
        'form.six_month_lose_weight_type_id': 'handleNutritionScore',
        'form.total_lose_weight_type_id': 'handleNutritionScore',
        'form.lack_of_appetite_type_id': 'handleNutritionScore'
    },
    methods: {
        handleSoreMethodScore () {
            if (this.form.wong_baker_score !== '') {
                this.form.sore_total_score = this.form.wong_baker_score
            }
            
            if (this.form.nrs_score !== '') {
                this.form.sore_total_score = this.form.nrs_score
            }
        },
        handleNutritionScore() {
            const a = this.getSixMonthScore(this.form.six_month_lose_weight_type_id)
            const b = this.getAnyLoseWeightScore(this.form.total_lose_weight_type_id)
            const c = this.getAppetiteScore(this.form.lack_of_appetite_type_id);
            const score = a + b + c
            this.form.screening_total_score = score.toString()
        },
        getSixMonthScore(value) {
            if (value === '2') return 2;
            return 0;
        },
        getAnyLoseWeightScore(value){
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
                }
            }
            return score = 0
        },
        getAppetiteScore(value) {
            if (value === '2') return 1;
            return 0;
        },
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>