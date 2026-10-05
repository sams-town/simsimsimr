<template>
    <div>
        
        <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
            style="margin-left:-16px; margin-right:-16px;">Triase</div>
        <section class="mt-20">
            <div class="row">
                <div class="col-lg-3">
                    <label class="c-form-label">Kasus</label>
                    <b-form-radio-group v-model="form.case_type_id" size="sm">
                        <b-form-radio value="1">Trauma</b-form-radio>
                        <b-form-radio value="2">Non Trauma</b-form-radio>
                        <b-form-radio value="3">Obstetri</b-form-radio>
                    </b-form-radio-group>
                </div>
                <div class="col-lg-3 mt-12 mt-lg-0">
                    <label class="c-form-label">Eye (Respon membuka mata)</label>
                    <v-select v-model="form.eye_response" aria-readonly="true" :options="OptionsEyeResponse" :reduce="OptionsEyeResponse => OptionsEyeResponse.value" label="title" placeholder="-- Pilih --"></v-select>
                </div>
                <div class="col-lg-3 mt-12 mt-lg-0">
                    <label class="c-form-label">Verbal (Respon verbal)</label>
                    <v-select v-model="form.verbal_response" aria-readonly="true" :options="OptionsVerbalResponse" :reduce="OptionsVerbalResponse => OptionsVerbalResponse.value" label="title" placeholder="-- Pilih --"></v-select>
                </div>
                <div class="col-lg-3 mt-12 mt-lg-0">
                    <label class="c-form-label">Motorik (Gerakan)</label>
                    <v-select v-model="form.motor_response" aria-readonly="true" :options="OptionsMotoric" label="title"
                        placeholder="-- Pilih --"></v-select>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 mt-12">
                    <label class="c-form-label">Capillary Refill Time (Crit >2 detik)</label>
                    <b-form-radio-group v-model="form.capillary_refill_time" size="sm">
                        <b-form-radio value="0">Tidak</b-form-radio>
                        <b-form-radio value="1">Ya</b-form-radio>
                    </b-form-radio-group>
                </div>
                <div class="col-lg-3 mt-12">
                    <label class="c-form-label">Deformitas</label>
                    <b-form-radio-group v-model="form.deformity_type_id" size="sm">
                        <b-form-radio value="0">Tidak</b-form-radio>
                        <b-form-radio value="1">Ya</b-form-radio>
                    </b-form-radio-group>
                    <b-form-input v-if="form.deformity_type_id == '1'" v-model="form.deformity" />
                </div>
                <div class="col-lg-3 mt-12">
                    <label class="c-form-label">Penetrasi</label>
                    <b-form-radio-group v-model="form.penetration_type_id" size="sm">
                        <b-form-radio value="0">Tidak</b-form-radio>
                        <b-form-radio value="1">Ya</b-form-radio>
                    </b-form-radio-group>
                    <b-form-input v-if="form.penetration_type_id == '1'" v-model="form.penetration" />
                </div>
                <div class="col-lg-3 mt-12">
                    <label class="c-form-label">Contusio</label>
                    <b-form-radio-group v-model="form.contusion_type_id" size="sm">
                        <b-form-radio value="0">Tidak</b-form-radio>
                        <b-form-radio value="1">Ya</b-form-radio>
                    </b-form-radio-group>
                    <b-form-input v-if="form.contusion_type_id == '1'" v-model="form.contusion" />
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 mt-12">
                    <label class="c-form-label">Laserasi</label>
                    <b-form-radio-group v-model="form.laceration_type_id" size="sm">
                        <b-form-radio value="0">Tidak</b-form-radio>
                        <b-form-radio value="1">Ya</b-form-radio>
                    </b-form-radio-group>
                    <b-form-input v-if="form.laceration_type_id == '1'" v-model="form.laceration" />
                </div>
                <div class="col-lg-3 mt-12">
                    <label class="c-form-label">Abrasi</label>
                    <b-form-radio-group v-model="form.abrasion_type_id" size="sm">
                        <b-form-radio value="0">Tidak</b-form-radio>
                        <b-form-radio value="1">Ya</b-form-radio>
                    </b-form-radio-group>
                    <b-form-input v-if="form.abrasion_type_id == '1'" v-model="form.abrasion" />
                </div>
                <div class="col-lg-3 mt-12">
                    <label class="c-form-label">Kedalaman Luka</label>
                    <b-form-radio-group v-model="form.wound_depth_type_id" size="sm">
                        <b-form-radio value="0">Tidak</b-form-radio>
                        <b-form-radio value="1">Ya</b-form-radio>
                    </b-form-radio-group>
                    <b-form-input v-if="form.wound_depth_type_id == '1'" v-model="form.wound_depth" />
                </div>
                <div class="col-lg-3 mt-12">
                    <label class="c-form-label">Lainnya</label>
                    <b-form-input v-model="form.triage_anythingelse" />
                </div>
            </div>
            <div class="mt-32">
                <ModernLabel label="Dead on Arrival" />
                <div class="row">
                    <div class="col-lg-3 mt-12">
                        <b-form-checkbox v-model="form.dead_on_arrival_arry" value="a">Tidak Ada Tanda
                            Kehidupan</b-form-checkbox>
                        <div v-if="form.dead_on_arrival_arry.indexOf('a') !== -1" class="mt-1">
                            <label class="c-form-label">Dinyatakan DOA Jam</label>
                            <b-form-input type="datetime-local" v-model="form.doa_time" />
                        </div>
                    </div>
                    <div class="col-lg-3 mt-12">
                        <b-form-checkbox v-model="form.dead_on_arrival_arry" value="b">Tidak Ada Denyut
                            Nadi</b-form-checkbox>
                        <div v-if="form.dead_on_arrival_arry.indexOf('b') !== -1" class="mt-1">
                            <label class="c-form-label">Dinyatakan meninggal Jam</label>
                            <b-form-input type="datetime-local" v-model="form.die_time" />
                        </div>
                    </div>
                    <div class="col-lg-3 mt-12">
                        <b-form-checkbox v-model="form.dead_on_arrival_arry" value="c">EKG Flat</b-form-checkbox>
                    </div>
                </div>
            </div>
        </section>
        <section class="mt-40">
            <b-table-simple responsive>
                <b-thead class="text-bold-14 text-center">
                    <b-tr>
                        <b-th>PEMERIKSAAN</b-th>
                        <b-th variant="danger">RESUSITASI PRIORITY 1</b-th>
                        <b-th variant="warning">EMERGENCY PRIORITY 2</b-th>
                        <b-th variant="warning">URGENT PRIORITY 3</b-th>
                        <b-th variant="success">NON URGENT PRIORITY 4</b-th>
                        <b-th variant="success">FALSE EMERGENCY PRIORITY 5</b-th>
                    </b-tr>
                </b-thead>
                <b-tbody class="text-regular-12">
                    <b-tr>
                        <b-td colspan="6">
                            <span class="font-weight-bold">JALAN NAFAS</span>
                            <b-form-radio-group v-model="form.airway_type_id" size="sm">
                                <b-form-radio value="1">Bebas</b-form-radio>
                                <b-form-radio value="2">Sumbatan</b-form-radio>
                            </b-form-radio-group>
                        </b-td>
                    </b-tr>
                    <b-tr>
                        <b-td>
                            <span class="font-weight-bold">PERNAFASAN</span>
                        </b-td>
                        <b-td>
                            <b-form-checkbox-group v-model="form.respiratory_arry" stacked size="sm">
                                <b-form-checkbox value="1A">Frek Nafas &lt; 10 x/mnt</b-form-checkbox>
                                <b-form-checkbox value="1B">Henti Nafas</b-form-checkbox>
                                <b-form-checkbox value="1C">Sianisis</b-form-checkbox>
                                <b-form-checkbox value="1D">Mengi</b-form-checkbox>
                                <b-form-checkbox value="1E">Tidak Ada Keluhan</b-form-checkbox>
                            </b-form-checkbox-group>
                        </b-td>
                        <b-td>
                            <b-form-checkbox-group v-model="form.respiratory_arry" stacked size="sm">
                                <b-form-checkbox value="2A">Frek Nafas &gt; 10 x/mnt</b-form-checkbox>
                                <b-form-checkbox value="2B">Henti Nafas</b-form-checkbox>
                                <b-form-checkbox value="2C">Sianisis</b-form-checkbox>
                                <b-form-checkbox value="2D">Mengi</b-form-checkbox>
                                <b-form-checkbox value="2E">Tidak Ada Keluhan</b-form-checkbox>
                            </b-form-checkbox-group>
                        </b-td>
                        <b-td>
                            <b-form-checkbox-group v-model="form.respiratory_arry" stacked size="sm">
                                <b-form-checkbox value="3A">Frek Nafas 24-32 x/mnt</b-form-checkbox>
                                <b-form-checkbox value="3B">Henti Nafas</b-form-checkbox>
                                <b-form-checkbox value="3C">Sianisis</b-form-checkbox>
                                <b-form-checkbox value="3D">Mengi</b-form-checkbox>
                                <b-form-checkbox value="3E">Tidak Ada Keluhan</b-form-checkbox>
                            </b-form-checkbox-group>
                        </b-td>
                        <b-td>
                            <b-form-checkbox-group v-model="form.respiratory_arry" stacked size="sm">
                                <b-form-checkbox value="4A">Frek Nafas 20-24 x/mnt</b-form-checkbox>
                            </b-form-checkbox-group>
                        </b-td>
                        <b-td>
                            <b-form-checkbox-group v-model="form.respiratory_arry" stacked size="sm">
                                <b-form-checkbox value="5A">Frek Nafas 10-20 x/mnt</b-form-checkbox>
                            </b-form-checkbox-group>
                        </b-td>
                    </b-tr>
                    <b-tr>
                        <b-td>
                            <span class="font-weight-bold">SIRKULASI</span>
                        </b-td>
                        <b-td>
                            <b-form-checkbox-group v-model="form.circulation_arry" stacked size="sm">
                                <b-form-checkbox value="1A">Henti Jantung</b-form-checkbox>
                                <b-form-checkbox value="1B">Nadi Tidak Teraba</b-form-checkbox>
                                <b-form-checkbox value="1C">Nadi Teraba Lemah</b-form-checkbox>
                                <b-form-checkbox value="1D">Pucat</b-form-checkbox>
                                <b-form-checkbox value="1E">Akral Dingin</b-form-checkbox>
                                <b-form-checkbox value="1F">Tidak Ada Keluhan</b-form-checkbox>
                            </b-form-checkbox-group>
                        </b-td>
                        <b-td>
                            <b-form-checkbox-group v-model="form.circulation_arry" stacked size="sm">
                                <b-form-checkbox value="2A">Henti Jantung</b-form-checkbox>
                                <b-form-checkbox value="2B">Nadi Tidak Teraba</b-form-checkbox>
                                <b-form-checkbox value="2C">Nadi Teraba Lemah</b-form-checkbox>
                                <b-form-checkbox value="2D">Pucat</b-form-checkbox>
                                <b-form-checkbox value="2E">Akral Dingin</b-form-checkbox>
                                <b-form-checkbox value="2F">Tidak Ada Keluhan</b-form-checkbox>
                                <b-form-checkbox value="2G">Frekuensi Nadi &lt; 50 atau &gt; 150 x/mnt</b-form-checkbox>
                                <b-form-checkbox value="2H">Crit &gt; 2 Detik</b-form-checkbox>
                            </b-form-checkbox-group>
                        </b-td>
                        <b-td>
                            <b-form-checkbox-group v-model="form.circulation_arry" stacked size="sm">
                                <b-form-checkbox value="3A">Frekuensi Nadi 120-150 Menit</b-form-checkbox>
                                <b-form-checkbox value="3B">TD Sistole &gt; 160</b-form-checkbox>
                                <b-form-checkbox value="3C">TD Diastol &gt; 100</b-form-checkbox>
                            </b-form-checkbox-group>
                        </b-td>
                        <b-td>
                            <b-form-checkbox-group v-model="form.circulation_arry" stacked size="sm">
                                <b-form-checkbox value="4A">Frekuensi Nadi 100-120 x/mnt</b-form-checkbox>
                                <b-form-checkbox value="4B">TD Sistole &gt; 120-140</b-form-checkbox>
                                <b-form-checkbox value="4C">TD Diastol &gt; 80-100</b-form-checkbox>
                            </b-form-checkbox-group>
                        </b-td>
                        <b-td>
                            <b-form-checkbox-group v-model="form.circulation_arry" stacked size="sm">
                                <b-form-checkbox value="5A">Frekuensi Nadi 80-100 x/mnt</b-form-checkbox>
                                <b-form-checkbox value="5B">TD Sistole 120</b-form-checkbox>
                                <b-form-checkbox value="5C">TD Diastol 80</b-form-checkbox>
                            </b-form-checkbox-group>
                        </b-td>
                    </b-tr>
                    <b-tr>
                        <b-td>
                            <span class="font-weight-bold">KESADARAN</span>
                        </b-td>
                        <b-td>
                            <b-form-checkbox-group v-model="form.consciousness_arry" stacked size="sm">
                                <b-form-checkbox value="1A">GCS &lt; 9</b-form-checkbox>
                                <b-form-checkbox value="1B">Kejang</b-form-checkbox>
                                <b-form-checkbox value="1C">Tidak Ada Respon</b-form-checkbox>
                                <b-form-checkbox value="1D">Gelisah</b-form-checkbox>
                                <b-form-checkbox value="1E">Hamparesis</b-form-checkbox>
                                <b-form-checkbox value="1F">Nyeri Dada</b-form-checkbox>
                                <b-form-checkbox value="1G">Tidak Ada Keluhan</b-form-checkbox>
                            </b-form-checkbox-group>
                        </b-td>
                        <b-td>
                            <b-form-checkbox-group v-model="form.consciousness_arry" stacked size="sm">
                                <b-form-checkbox value="2A">GCS 9-12</b-form-checkbox>
                                <b-form-checkbox value="2B">Kejang</b-form-checkbox>
                                <b-form-checkbox value="2C">Tidak Ada Respon</b-form-checkbox>
                                <b-form-checkbox value="2D">Gelisah</b-form-checkbox>
                                <b-form-checkbox value="2E">Hamparesis</b-form-checkbox>
                                <b-form-checkbox value="2F">Nyeri Dada</b-form-checkbox>
                                <b-form-checkbox value="2G">Tidak Ada Keluhan</b-form-checkbox>
                            </b-form-checkbox-group>
                        </b-td>
                        <b-td>
                            <b-form-checkbox-group v-model="form.consciousness_arry" stacked size="sm">
                                <b-form-checkbox value="3A">GCS 13-14</b-form-checkbox>
                            </b-form-checkbox-group>
                        </b-td>
                        <b-td>
                            <b-form-checkbox-group v-model="form.consciousness_arry" stacked size="sm">
                                <b-form-checkbox value="4A">GCS 15</b-form-checkbox>
                            </b-form-checkbox-group>
                        </b-td>
                        <b-td>
                            <b-form-checkbox-group v-model="form.consciousness_arry" stacked size="sm">
                                <b-form-checkbox value="5A">GCS 15</b-form-checkbox>
                            </b-form-checkbox-group>
                        </b-td>
                    </b-tr>
                    <b-tr>
                        <b-td>
                            <span class="font-weight-bold">PAPARAN</span>
                        </b-td>
                        <b-td>
                            <b-form-checkbox-group v-model="form.exposure_arry" stacked size="sm">
                                <b-form-checkbox value="1A">Kejang Berkelanjutan</b-form-checkbox>
                            </b-form-checkbox-group>
                        </b-td>
                        <b-td>
                            <b-form-checkbox-group v-model="form.exposure_arry" stacked size="sm">
                                <b-form-checkbox value="2A">Nyeri dada tipikal</b-form-checkbox>
                                <b-form-checkbox value="2B">Nyeri hebat</b-form-checkbox>
                                <b-form-checkbox value="2C">Deficit neurologis (hemiparese, dispasia)</b-form-checkbox>
                                <b-form-checkbox value="2D">Mata terpecik zat asam atau basa</b-form-checkbox>
                                <b-form-checkbox value="2E">Multiple trauma</b-form-checkbox>
                                <b-form-checkbox value="2F">Intoksikasi</b-form-checkbox>
                            </b-form-checkbox-group>
                        </b-td>
                        <b-td>
                            <b-form-checkbox-group v-model="form.exposure_arry" stacked size="sm">
                                <b-form-checkbox value="3A">Riwayat kejang</b-form-checkbox>
                                <b-form-checkbox value="3B">Nyeri sedang</b-form-checkbox>
                                <b-form-checkbox value="3C">Riwayat pingsan</b-form-checkbox>
                                <b-form-checkbox value="3D">Deformitas</b-form-checkbox>
                                <b-form-checkbox value="3E">Laserasi</b-form-checkbox>
                            </b-form-checkbox-group>
                        </b-td>
                        <b-td>
                            <b-form-checkbox-group v-model="form.exposure_arry" stacked size="sm">
                                <b-form-checkbox value="4A">Nyeri ringan</b-form-checkbox>
                                <b-form-checkbox value="4B">Trauma ekstrimitas minor</b-form-checkbox>
                                <b-form-checkbox value="4C">Radang / benda asing di mata</b-form-checkbox>
                                <b-form-checkbox value="4D">Penglihatan normal</b-form-checkbox>
                            </b-form-checkbox-group>
                        </b-td>
                        <b-td>
                            <b-form-checkbox-group v-model="form.exposure_arry" stacked size="sm">
                                <b-form-checkbox value="5A">Keluhan minor yang saat berkunjung masih
                                    dirasakan</b-form-checkbox>
                                <b-form-checkbox value="5B">Luka kecil (luka lecet, luka robek kecil)</b-form-checkbox>
                                <b-form-checkbox value="5C">Kunjungan ulang untuk ganti verban</b-form-checkbox>
                                <b-form-checkbox value="5D">Evaluasi jahitan</b-form-checkbox>
                            </b-form-checkbox-group>
                        </b-td>
                    </b-tr>
                    <b-tr>
                        <b-td>
                            <span class="font-weight-bold">PSIKOLOGI</span>
                        </b-td>
                        <b-td>
                            <b-form-checkbox-group v-model="form.pychology_arry" stacked size="sm">
                                <b-form-checkbox value="1A">Gangguan perilaku berat yang mengancam diri pasien dan orang
                                    lain</b-form-checkbox>
                            </b-form-checkbox-group>
                        </b-td>
                        <b-td>
                            <b-form-checkbox-group v-model="form.pychology_arry" stacked size="sm">
                                <b-form-checkbox value="2A">Perilaku agresif dan kasar </b-form-checkbox>
                            </b-form-checkbox-group>
                        </b-td>
                        <b-td>
                            <b-form-checkbox-group v-model="form.pychology_arry" stacked size="sm">
                                <b-form-checkbox value="3A">Menarik diri, agitasi, gangguan isi dan bentuk pikiran
                                    akut</b-form-checkbox>
                            </b-form-checkbox-group>
                        </b-td>
                        <b-td>
                            <b-form-checkbox-group v-model="form.pychology_arry" stacked size="sm">
                                <b-form-checkbox value="4A">Riwayat gangguan yang merusak diri dan mengganggu orang
                                    lain</b-form-checkbox>
                            </b-form-checkbox-group>
                        </b-td>
                        <b-td>
                            <b-form-checkbox-group v-model="form.pychology_arry" stacked size="sm">
                                <b-form-checkbox value="5A">Normal</b-form-checkbox>
                            </b-form-checkbox-group>
                        </b-td>
                    </b-tr>
                </b-tbody>
            </b-table-simple>
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
                    <b-form-checkbox-group v-model="form.sore_affetcs_arry" size="sm" class="mt-10 mt-lg-0">
                        <b-form-checkbox value="a">Nyeri Mempengaruhi</b-form-checkbox>
                        <b-form-checkbox value="b">Tidur</b-form-checkbox>
                        <b-form-checkbox value="c">Aktifitas Fisik</b-form-checkbox>
                        <b-form-checkbox value="d">Emosi</b-form-checkbox>
                        <b-form-checkbox value="e">Nafsu Makan</b-form-checkbox>
                        <b-form-checkbox value="f">Lain-lain</b-form-checkbox>
                    </b-form-checkbox-group>
                    <b-form-input v-model="form.sore_affetcs" type="number" class="col" :disabled="form.sore_affetcs_arry.indexOf('f') === -1"/>
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
                            <b-form-input v-model="form.fall_risk_total_score" type="number"></b-form-input>
                        </b-input-group>
                    </div>
                </div>
            </div>
        </section>
        <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Skrining Gizi
            </div>
            <div>
                <label class="c-form-label mt-20">(bila skor ≥ 2 dan / atau dengan diagnosa / kondisi khusus dilakukan asesmen lanjut oleh Ahli Gizi)</label>
                <div class="row mt-10">
                    <div class="col-lg-6">
                        <div class="c-form-label text-lg-right">Apakah pasien mengalami penurunan BB dalam 6 bulan terakhir</div>
                    </div>
                    <div class="col-lg-4">
                        <v-select v-model="form.six_month_lose_weight" aria-readonly="true" :options="optionsLoseWeight" :reduce="optionsLoseWeight => optionsLoseWeight.value" label="title" placeholder="-- Pilih --"></v-select>
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-lg-6">
                        <div class="c-form-label text-lg-right">Apakah asupan makanan kurang karena tidak nafsu makan</div>
                    </div>
                    <div class="col-lg-4">
                        <v-select v-model="form.lack_of_appetite" aria-readonly="true" :options="optionsLackOfAppetite" :reduce="optionsLackOfAppetite => optionsLackOfAppetite.value" label="title" placeholder="-- Pilih --"></v-select>
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-lg-6">
                        <div class="c-form-label text-lg-right">Total Skor</div>
                    </div>
                    <div class="col-lg-4">
                        <b-form-input v-model="form.nutrition_total_score" disabled placeholder="0"></b-form-input>
                    </div>
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
        <!-- <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Kebutuhan Edukasi
            </div>
            <div class="mt-20">
                <div>
                    <label class="c-form-label">Kebutuhan pembelajaran pasien (pilih topik pembelajaran pada kotak yang tersedia)</label>
                    <b-form-checkbox-group v-model="form.CheckPembelajaran" size="sm" class="mt-10">
                        <b-form-checkbox value="Proses Penyakit">Proses Penyakit</b-form-checkbox>
                        <b-form-checkbox value="Terapi/obat">Terapi/obat</b-form-checkbox>
                        <b-form-checkbox value="Perawatan perioperatif">Perawatan perioperatif</b-form-checkbox>
                        <b-form-checkbox value="Teknik fisioterapi">Teknik fisioterapi</b-form-checkbox>
                        <b-form-checkbox value="Nutrisi/diet">Nutrisi/diet</b-form-checkbox>
                        <b-form-checkbox value="Mengatasi nyeri">Mengatasi nyeri</b-form-checkbox>
                        <b-form-checkbox value="Tindakan keperawatan">Tindakan keperawatan</b-form-checkbox>
                        <b-form-checkbox value="Other">Lain-lain</b-form-checkbox>
                    </b-form-checkbox-group>
                    <b-form-input :disabled="form.CheckPainAway.indexOf('e') === -1" class="mt-10" />
                </div>
                <div class="mt-20">
                    <label class="c-form-label">Terdapat hambatan dalam pembelajaran</label>
                    <b-form-radio-group v-model="form.radio_learn_barrier" size="sm" class="mt-10">
                        <b-form-radio value="a">Tidak</b-form-radio>
                        <b-form-radio value="b">Ya, Terdapat hambatan dalam pembelajaran</b-form-radio>
                    </b-form-radio-group>
                    <b-form-checkbox-group v-model="form.check_learn_barrier" :disabled="form.radio_learn_barrier != 'b'" size="sm" class="col-lg-10 px-0 mt-10">
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
                    <b-form-input :disabled="form.radio_learn_barrier != 'b' || form.check_learn_barrier.indexOf('i') === -1" class="mt-10" />
                </div>
                <div class="mt-20">
                    <label class="c-form-label">Dibutuhkan penerjemah</label>
                    <b-form-radio-group v-model="form.radio_need_translator" size="sm">
                        <b-form-radio value="a">Tidak</b-form-radio>
                        <b-form-radio value="b">Ya, Sebutkan</b-form-radio>
                    </b-form-radio-group>
                    <b-form-input :disabled="form.radio_need_translator != 'b'" class="mt-10" />
                </div>
            </div>
        </section> -->
        <!-- <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Kriteria Pasien yang Beresiko
            </div>
            <b-form-checkbox-group size="sm" class="mt-20">
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
        </section> -->
        <!-- <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Informasi yang Diberikan Kepada Pasien/Keluarga
            </div>
            <b-form-checkbox-group size="sm" class="mt-20">
                <b-form-checkbox class="col-lg-6 mr-0 my-1" value="a">Hak dan kewajiban pasien selama dirawat</b-form-checkbox>
                <b-form-checkbox class="col-lg-6 mr-0 my-1" value="b">Jam visitor</b-form-checkbox>
                <b-form-checkbox class="col-lg-6 mr-0 my-1" value="c">Fasilitas ruangan yang tersedia</b-form-checkbox>
                <b-form-checkbox class="col-lg-6 mr-0 my-1" value="d">Jam berkunjung</b-form-checkbox>
                <b-form-checkbox class="col-lg-6 mr-0 my-1" value="e">Dokter yang merawat</b-form-checkbox>
                <b-form-checkbox class="col-lg-6 mr-0 my-1" value="f">Jumlah penunggu pasien</b-form-checkbox>
                <b-form-checkbox class="col-lg-6 mr-0 my-1" value="g">Penjelasan tentang tujuan pemasangan nama/tanda lain yang diperlukan sesuai dengan kondisi</b-form-checkbox>
            </b-form-checkbox-group>
        </section> -->
        <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Daftar Masalah Keperawatan
            </div>
            <div >
                <b-form-checkbox-group size="sm" v-model="form.nursing_problems_arry" class="mt-20">
                    <b-form-checkbox class="col-lg-4 my-1" value="a">Pola nafas tidak efektif</b-form-checkbox>
                    <b-form-checkbox class="col-lg-4 my-1" value="b">Gangguan pola tidur</b-form-checkbox>
                    <b-form-checkbox class="col-lg-4 my-1" value="c">Gangguan perfusi serebral</b-form-checkbox>
                    <b-form-checkbox class="col-lg-4 my-1" value="d">Gangguan keseimbangan cairan & elektrolit</b-form-checkbox>
                    <b-form-checkbox class="col-lg-4 my-1" value="e">Gangguan pertukaran gas</b-form-checkbox>
                    <b-form-checkbox class="col-lg-4 my-1" value="f">Gangguan eliminasi: BAK</b-form-checkbox>
                    <b-form-checkbox class="col-lg-4 my-1" value="g">Gangguan eliminasi: BAB</b-form-checkbox>
                    <b-form-checkbox class="col-lg-4 my-1" value="h">Intoleransi aktifitas</b-form-checkbox>
                    <b-form-checkbox class="col-lg-4 my-1" value="i">Hyper</b-form-checkbox>
                    <b-form-checkbox class="col-lg-4 my-1" value="j">Hypothermia</b-form-checkbox>
                    <b-form-checkbox class="col-lg-4 my-1" value="k">Gangguan perfusi jaringan perifer</b-form-checkbox>
                    <b-form-checkbox class="col-lg-4 my-1" value="l">Gangguan integritas kulit</b-form-checkbox>
                    <b-form-checkbox class="col-lg-4 my-1" value="m">Kurang pengetahuan</b-form-checkbox>
                    <b-form-checkbox class="col-lg-4 my-1" value="n">Resiko cedera</b-form-checkbox>
                </b-form-checkbox-group>
            </div>
        </section>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import vSelect from 'vue-select'
import ModernLabel from '@/@core/components/label/ModernLabel.vue';

export default {
    name: 'nurse_igd',
    components: { vSelect, ModernLabel },
    data() {
        return {
            OptionsEyeResponse: [ // masih dummy
                { title: 'Spontan', value: '1' },
                { title: 'Sedikit rangsang', value: '2' },
                { title: 'Rangsang nyeri', value: '3' },
                { title: 'Tidak ada respon', value: '4' },
            ],
            OptionsVerbalResponse: [ // masih dummy
                { title: 'Orientasi baik', value: '1' },
                { title: 'Orientasi tidak baik', value: '2' },
                { title: 'Bicara tidak jelas', value: '3' },
                { title: 'Tidak ada respon', value: '4' },
            ],
            OptionsMotoric: [ // masih dummy
                { title: 'Mengikuti perintah', value: '1' },
                { title: 'Gerakan tidak sesuai', value: '2' },
                { title: 'Tidak ada respon', value: '3' },
            ],
            optionsLoseWeight:[ // masih dummy
                { value: 1, title: 'Tidak' },
                { value: 2, title: 'Tidak Yakin' },
                { value: 3, title: 'Ya' },
            ],
            optionsLackOfAppetite:[ // masih dummy
                { value: 1, title: 'Tidak' },
                { value: 2, title: 'Tidak Yakin' },
                { value: 3, title: 'Ya' },
            ],
            // # TYPE_NURSE_ID X = IGD
            form: {
                nurse_id: "",

                // TRIASE
                case_type_id: '',
                eye_response: '',
                verbal_response: '',
                motor_response: '',
                capillary_refill_time: '',
                deformity_type_id: '0',
                deformity: '',
                penetration_type_id: '0',
                penetration: '',
                contusion_type_id: '0',
                contusion: '',
                laceration_type_id: '0',
                laceration: '',
                abrasion_type_id: '0',
                abration: '',
                wound_depth_type_id: '0',
                wound_depth: '',
                triage_anythingelse: '',
                dead_on_arrival_arry: [],
                doa_time: '',
                die_time: '',
                airway_type_id: '',                
                respiratory_arry: [],
                circulation_arry: [],
                consciousness_arry: [],
                exposure_arry: [],
                psychology_arry: [],

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
                sore_affetcs_arry: [],
                sore_affects: "",

                // PENGKAJIAN RISIKO JATUH
                fall_risk_method_type_id: "",
                fall_risk_total_score: "",

                // SKRINING GIZI
                six_month_lose_weight: '',
                lack_of_appetite: '',
                nutrition_total_score: '',

                // STATUS FUNGSIONAL
                sensoric_vision_type_id: "",
                sensoric_olfactory_type_id: "",
                sensoric_hearing_type_id: "",
                cognitive_arry: [],
                motor_daily_arry: [],
                motor_walking_arry: [],

                // DAFTAR MASALAH KEPERAWATAN
                nursing_problems_arry: [], 
            },
        }
    },
    watch: {
        //  =========================  TRIASE  =========================
        "form.deformity_type_id"(newValues) {
            if (newValues !== "1") {
                this.form.deformity = ""
            }
        },
        "form.penetration_type_id"(newValues) {
            if (newValues !== "1") {
                this.form.penetration = ""
            }
        },
        "form.contusion_type_id"(newValues) {
            if (newValues !== "1") {
                this.form.contusion = ""
            }
        },
        "form.laceration_type_id"(newValues) {
            if (newValues !== "1") {
                this.form.laceration = ""
            }
        },
        "form.abrasion_type_id"(newValues) {
            if (newValues !== "1") {
                this.form.abrasion = ""
            }
        },
        "form.wound_depth_type_id"(newValues) {
            if (newValues !== "1") {
                this.form.wound_depth = ""
            }
        },

        //  =========================  SKALA NYERI  =========================
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
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';

.timeline-wrapper{
    background: rgb(8,140,32);
    background: -moz-linear-gradient(90deg, rgba(8,140,32,1) 0%, rgba(255,253,0,1) 50%, rgba(255,0,0,1) 100%);
    background: -webkit-linear-gradient(90deg, rgba(8,140,32,1) 0%, rgba(255,253,0,1) 50%, rgba(255,0,0,1) 100%);
    background: linear-gradient(90deg, rgba(8,140,32,1) 0%, rgba(255,253,0,1) 50%, rgba(255,0,0,1) 100%);
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#088c20",endColorstr="#ff0000",GradientType=1);
}

.devider {
    border-left: 1px solid #ffffff55;
    border-right: 1px solid #ffffff55;
}
</style>