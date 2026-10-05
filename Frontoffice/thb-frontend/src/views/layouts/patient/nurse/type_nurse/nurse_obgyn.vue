<template>
    <div>
        <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Riwayat Kesehatan
            </div>
            <div class="row mt-10 mt-lg-20">
                <div class="col-lg-6">
                    <div>
                        <ModernLabel label="Riwayat penyakit dahulu/ sebelumnya" />
                        <b-form-radio-group v-model="form.history_disease_type_id" class="mt-8 mt-lg-20">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya, Penyakit</b-form-radio>
                        </b-form-radio-group>
                        <b-form-input v-model="form.history_disease" class="mt-10 col-lg-8" :disabled="form.history_disease_type_id !== '1'" />
                    </div>
                    <div class="mt-32">
                        <ModernLabel label="Pernah dioperasi" />
                        <b-form-radio-group v-model="form.any_surgery_type_id" class="mt-8 mt-lg-20">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya</b-form-radio>
                        </b-form-radio-group>
                        <div class="row">
                            <div class="col-lg-6 mt-10 mt-lg-0">
                                <label class="c-form-label">Dimana</label>
                                <b-form-input v-model="form.any_surgery_date" :disabled="form.any_surgery_type_id != '1'" />
                            </div>
                            <div class="col-lg-6 mt-10 mt-lg-0">
                                <label class="c-form-label">Kapan</label>
                                <b-form-input v-model="form.any_surgery_place" type="date" :disabled="form.any_surgery_type_id != '1'" />
                            </div>
                        </div>
                    </div>
                    <div class="mt-32">
                        <ModernLabel label="Riwayat Penyakit Keluarga" />
                        <b-form-radio-group v-model="form.any_history_family_disease_type_id" class="mt-8 mt-lg-20">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya</b-form-radio>
                        </b-form-radio-group>
                        <b-form-checkbox-group size="sm" v-model="form.any_history_family_answer_arry" class="mt-10" :disabled="form.any_history_family_disease_type_id != '1'">
                            <b-form-checkbox value="a" class="my-1">DM</b-form-checkbox>
                            <b-form-checkbox value="b" class="my-1">Hipertensi</b-form-checkbox>
                            <b-form-checkbox value="c" class="my-1">Cancer</b-form-checkbox>
                            <b-form-checkbox value="d" class="my-1">Jantung</b-form-checkbox>
                            <b-form-checkbox value="e" class="my-1">TBC</b-form-checkbox>
                            <b-form-checkbox value="f" class="my-1">Anemia</b-form-checkbox>
                            <b-form-checkbox value="g" class="my-1">Lain-lain, sebutkan:</b-form-checkbox>
                        </b-form-checkbox-group>
                        <b-form-input v-model="form.any_history_family_answer" class="mt-10 col-lg-8" :disabled="form.any_history_family_disease_type_id != '1' || form.any_history_family_answer_arry.indexOf('g') === -1" />
                    </div>
                    <div class="mt-32">
                        <div>
                            <ModernLabel label="Riwayat Pekerjaan" small="(Apakah berhubungan dengan zat-zat berbahaya)" />
                            <b-form-radio-group v-model="form.employment_history_type_id" class="mt-8 mt-lg-20">
                                <b-form-radio value="0">Tidak</b-form-radio>
                                <b-form-radio value="1">Ya, sebutkan</b-form-radio>
                            </b-form-radio-group>
                            <b-form-input v-model="form.employment_history" class="col-lg-8 mt-10" :disabled="form.employment_history_type_id != '1'"></b-form-input>
                        </div>
                        <div class="mt-16 mt-lg-32">
                            <ModernLabel label="Status Pernikahan" />
                            <b-form-radio-group v-model="form.marriage_history_type_id" class="mt-8 mt-lg-20">
                                <b-form-radio class="my-1" value="1">Single</b-form-radio>
                                <b-form-radio class="my-1" value="2">Bercerai</b-form-radio>
                                <b-form-radio class="my-1" value="3">Janda/Duda</b-form-radio>
                                <b-form-radio class="my-1" value="4">Menikah</b-form-radio>
                            </b-form-radio-group>
                            <div class="row">
                                <b-input-group append="Kali" class="col-lg-6 mt-10">
                                    <b-form-input v-model="form.marriage_history" type="number" :disabled="form.marriage_history_type_id != '4'"></b-form-input>
                                </b-input-group>
                            </div>
                        </div>
                        <div class="mt-16 mt-lg-32">
                            <ModernLabel label="umur waktu pertama kawin" />
                            <div class="mt-8 mt-lg-20">
                                <b-input-group append="Tahun" class="col-12 mt-10 mt-lg-0 px-0">
                                    <b-form-input v-model="form.age_first_marriage" type="number"></b-form-input>
                                </b-input-group>
                                <!-- <div class="row c-form">
                                    <div class="col-lg-6 mt-10">
                                        <label>Kawin Dengan Suami / Istri 1</label>
                                        <b-input-group append="Tahun" class="col-12 mt-10 mt-lg-0 px-0">
                                            <b-form-input v-model="form.marriage_one" type="number"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-6 mt-10">
                                        <label>Ke 2, 3</label>
                                        <b-input-group append="Tahun" class="col-12 mt-10 mt-lg-0 px-0">
                                            <b-form-input v-model="form.marriage_two" type="number"></b-form-input>
                                            <b-form-input v-model="form.marriage_three" type="number"></b-form-input>
                                        </b-input-group>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mt-32 mt-lg-0">
                        <ModernLabel label="Pernah dirawat sebelumnya" />
                        <b-form-radio-group v-model="form.been_hospitalized_type_id" class="mt-8 mt-lg-20">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya, Diagnosa</b-form-radio>
                        </b-form-radio-group>
                        <b-form-input v-model="form.been_hospitalized_answer" :disabled="form.been_hospitalized_type_id !== '1'" class="mt-10 col-lg-8" />
                        <div class="row">
                            <div class="col-lg-6 mt-10">
                                <label class="c-form-label">Kapan</label>
                                <b-form-input v-model="form.been_hospitalized_date" type="date" :disabled="form.been_hospitalized_type_id != '1'"></b-form-input>
                            </div>
                            <div class="col-lg-6 mt-10">
                                <label class="c-form-label">Dimana</label>
                                <b-form-input v-model="form.been_hospitalized_place" :disabled="form.been_hospitalized_type_id != '1'"></b-form-input>
                            </div>
                        </div>
                    </div>
                    <div class="mt-16 mt-lg-32">
                        <ModernLabel label="Masih dalam pengobatan" />
                        <b-form-radio-group v-model="form.under_treatment_type_id" class="mt-8 mt-lg-20">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya, Obat</b-form-radio>
                        </b-form-radio-group>
                        <b-form-input v-model="form.under_treatment" :disabled="form.under_treatment_type_id !== '1'" class="mt-10 col-lg-8"></b-form-input>
                    </div>
                    <div class="mt-16 mt-lg-32">
                        <ModernLabel label="ketergantungan terhadap zat tertentu" />
                        <b-form-radio-group v-model="form.dependence_substance_type_id" class="mt-8 mt-lg-20">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya</b-form-radio>
                        </b-form-radio-group>
                        <b-form-checkbox-group v-model="form.dependence_substance_arry" size="sm" class="mt-10" :disabled="form.dependence_substance_type_id != '1'">
                            <b-form-checkbox value="a">Obat-obatan</b-form-checkbox>
                            <b-form-checkbox value="b">Alkohol</b-form-checkbox>
                            <b-form-checkbox value="c">Rokok</b-form-checkbox>
                            <b-form-checkbox value="d">Lain-lain, sebutkan :</b-form-checkbox>
                        </b-form-checkbox-group>
                        <b-form-input v-model="form.dependence_substance" :disabled="form.dependence_substance_type_id != '1' || form.dependence_substance_arry.indexOf('d') === -1" class="mt-10" />
                    </div>
                    <div class="mt-16 mt-lg-32">
                        <ModernLabel label="Riwayat alergi" />
                        <b-form-radio-group v-model="form.allergy_history_type_id" class="mt-8 mt-lg-20">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya</b-form-radio>
                        </b-form-radio-group>
                        <b-form-checkbox-group stacked v-model="form.allergy_history_arry" :disabled="form.allergy_history_type_id !== '1'" size="sm" class="mt-10">
                            <div class="d-flex flex-wrap mt-10">
                                <b-form-checkbox value="a" class="mr-0">Obat</b-form-checkbox>
                                <b-form-input v-model="form.allergy_history_medicine" class="col-lg-8 ml-lg-auto" :disabled="form.allergy_history_type_id !== '1' || form.allergy_history_arry.indexOf('a') === -1"></b-form-input>
                            </div>
                            <div class="d-flex flex-wrap mt-10">
                                <b-form-checkbox value="b" class="mr-0">Makanan</b-form-checkbox>
                                <b-form-input v-model="form.allergy_history_food" class="col-lg-8 ml-lg-auto" :disabled="form.allergy_history_type_id !== '1' || form.allergy_history_arry.indexOf('b') === -1"></b-form-input>
                            </div>
                            <div class="d-flex flex-wrap mt-10">
                                <b-form-checkbox value="c" class="mr-0">Lainnya</b-form-checkbox>
                                <b-form-input v-model="form.allergy_history_other" class="col-lg-8 ml-lg-auto" :disabled="form.allergy_history_type_id !== '1' || form.allergy_history_arry.indexOf('c') === -1"></b-form-input>
                            </div>
                        </b-form-checkbox-group>
                    </div>
                    <div class="mt-16 mt-lg-32">
                        <ModernLabel label="Riwayat Pemakaian Alat Kontrasepsi" />
                        <b-form-radio-group v-model="form.contraception_use_type_id" class="mt-8 mt-lg-20">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya, Jenis:</b-form-radio>
                        </b-form-radio-group>
                        <b-form-input v-model="form.contraception_use" :disabled="form.contraception_use_type_id !== '1'" placeholder="jenis" class="mt-10 col-lg-8" />
                        <div class="row">
                            <div class="col-lg-6 mt-10">
                                <label class="c-form-label">Lama Pemakaian</label>
                                <b-form-input v-model="form.contraception_length_of_use" :disabled="form.contraception_use_type_id !== '1'" />
                            </div>
                            <div class="col-lg-6 mt-10">
                                <label class="c-form-label">Keluhan</label>
                                <b-form-input v-model="form.contraception_use_complaint" :disabled="form.contraception_use_type_id !== '1'" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-16 mt-lg-32">
                <ModernLabel label="Riwayat menstruasi" />
                <div class="row mt-10 mt-lg-20">
                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-lg-6 mt-10">
                                <label class="c-form-label">Menarche Umur</label>
                                <b-input-group append="Tahun">
                                    <b-form-input v-model="form.period_menarche_age" type="number"></b-form-input>
                                </b-input-group>
                            </div>
                            <div class="col-lg-6 mt-10">
                                <label class="c-form-label">Siklus</label>
                                <b-input-group append="Hari">
                                    <b-form-input v-model="form.period_cycle" type="number"></b-form-input>
                                </b-input-group>
                            </div>
                        </div>
                        <b-form-radio-group v-model="form.period_regular_type_id" class="mt-8 mt-lg-20">
                            <b-form-radio value="0">Teratur</b-form-radio>
                            <b-form-radio value="1">Tidak Teratur, lama</b-form-radio>
                        </b-form-radio-group>
                        <b-input-group append="Hari" class="mt-10">
                            <b-form-input v-model="form.period_regular" type="number" :disabled="form.period_regular_type_id !== '1'"></b-form-input>
                        </b-input-group>
                    </div>
                    <div class="col-lg-6 mt-32 mt-lg-0">
                        <label class="c-form-label">Ganti Pembalut</label>
                        <b-input-group append="1/24jam">
                            <b-form-input v-model="form.period_volume" type="number"></b-form-input>
                        </b-input-group>
                        <div class="mt-12">
                            <label class="c-form-label">Keluhan Saat Haid</label>
                            <b-form-radio-group v-model="form.period_complaint_type_id" class="my-lg-auto">
                                <b-form-radio value="0">Tidak</b-form-radio>
                                <b-form-radio value="1">Ya</b-form-radio>
                            </b-form-radio-group>
                            <b-form-input v-model="form.period_complaint" type="number" class="mt-10 col-lg-8" :disabled="form.period_complaint_type_id !== '1'"></b-form-input>
                        </div>
                    </div>
                </div>
                <div class="row mt-16">
                    <div class="col-lg-6">
                        <div>
                            <label class="c-form-label">HPHT</label>
                            <b-form-input v-model="form.hpht" type="number" />
                        </div>
                        <div class="row">
                            <div class="col-lg mt-10">
                                <label class="c-form-label">Taksiran Persalinan</label>
                                <b-form-input v-model="form.estimated_delivery" type="number" />
                            </div>
                            <div class="col-lg mt-10">
                                <label class="c-form-label">Usia Kehamilan</label>
                                <b-form-input v-model="form.gestational_age" type="number" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-16 mt-lg-32">
                <ModernLabel label="Riwayat penyakit ginekologi" />
                <b-form-radio-group v-model="form.gynecological_disease_type_id" class="mt-8 mt-lg-20">
                    <b-form-radio value="0">Tidak</b-form-radio>
                    <b-form-radio value="1">Ya</b-form-radio>
                </b-form-radio-group>
                <b-form-checkbox-group v-model="form.gynecological_disease_arry" :disabled="form.gynecological_disease_type_id != '1'" size="sm" class="mt-12">
                    <b-form-checkbox value="a">DM</b-form-checkbox>
                    <b-form-checkbox value="b">Infeksi Virus</b-form-checkbox>
                    <b-form-checkbox value="c">Endometriosis</b-form-checkbox>
                    <b-form-checkbox value="d">Polyp Cervix</b-form-checkbox>
                    <b-form-checkbox value="e">Kanker</b-form-checkbox>
                    <b-form-checkbox value="f">PMS</b-form-checkbox>
                    <b-form-checkbox value="g">Myoma</b-form-checkbox>
                    <b-form-checkbox value="h">Lainnya</b-form-checkbox>
                </b-form-checkbox-group>
                <b-form-input v-model="form.gynecological_disease" class="mt-10" :disabled="form.gynecological_disease_type_id != '1' || form.gynecological_disease_arry.indexOf('h') === -1" />
            </div>
            <div class="mt-16 mt-lg-32">
                <ModernLabel label="Riwayat kehamilan, persalinan dan Nifas" />
                <div class="mt-8 mt-lg-20">
                    <b-table-simple small responsive bordered>
                        <b-thead class="bg-neutral-100 text-bold-12 text-center">
                            <b-tr>
                                <b-th rowspan="2" style="vertical-align: middle;">No</b-th>
                                <b-th rowspan="2" style="vertical-align: middle;">Tanggal/Th Partus</b-th>
                                <b-th rowspan="2" style="vertical-align: middle;">Tempat Partus</b-th>
                                <b-th rowspan="2" style="vertical-align: middle;">Umur Kehamilan</b-th>
                                <b-th rowspan="2" style="vertical-align: middle;">Jenis Persalinan</b-th>
                                <b-th rowspan="2" style="vertical-align: middle;">Penolong</b-th>
                                <b-th rowspan="2" style="vertical-align: middle;">Penyulit</b-th>
                                <b-th colspan="3" style="vertical-align: middle;">Anak</b-th>
                                <b-th rowspan="2" style="vertical-align: middle;">Nifas</b-th>
                                <b-th rowspan="2" style="vertical-align: middle;">Keadaan Anak Sekarang</b-th>
                                <b-th rowspan="2" style="vertical-align: middle;">
                                    <b-button variant="primary" size="sm" @click="historypregnantForm = true"><feather-icon icon="PlusIcon" /></b-button>
                                </b-th>
                            </b-tr>
                            <b-tr>
                                <b-th>JK</b-th>
                                <b-th>BB</b-th>
                                <b-th>PB</b-th>
                            </b-tr>
                        </b-thead>
                        <b-tbody class="text-regular-14" style="white-space:nowrap">
                            <b-tr>
                                <b-td>1</b-td>
                                <b-td>01-11-2022</b-td>
                                <b-td>RS</b-td>
                                <b-td>8M2H</b-td>
                                <b-td>SC</b-td>
                                <b-td>-</b-td>
                                <b-td>-</b-td>
                                <b-td>Laki-laki</b-td>
                                <b-td>2500</b-td>
                                <b-td>15</b-td>
                                <b-td>15</b-td>
                                <b-td>15</b-td>
                                <b-td class="text-center">
                                    <b-button variant="danger-500" size="sm"><feather-icon icon="XIcon" /></b-button>
                                </b-td>
                            </b-tr>
                        </b-tbody>
                    </b-table-simple>
                    <small class="font-italic text-muted">*Boleh kosong atau diisi lebih dari satu</small>
                    <div v-show="historypregnantForm" class="col px-0 mt-32">
                        <form class="c-form">
                            <div class="row mx-0">
                                <div class="col-12 col-lg px-0 px-lg-3">
                                    <label>Jenis Alergi</label>
                                </div>
                                <div class="col-12 col-lg px-0 px-lg-3">
                                    <label>Alergi *</label>
                                    <b-form-input autocomplete="off" type="text" ></b-form-input>
                                </div>
                                <div class="col-12 col-lg px-0 px-lg-3">
                                    <label>Reaksi</label>
                                    <b-form-input autocomplete="off" type="text" ></b-form-input>
                                </div>
                            </div>
                            <div class="row mx-0 mt-0 mt-lg-12">
                                <div class="col-12 col-lg px-0 px-lg-3">
                                    <label>Tingkat Keparahan</label>
                                    <b-form-input autocomplete="off" type="text" ></b-form-input>
                                </div>
                                <div class="col-12 col-lg px-0 px-lg-3">
                                    <label>Efek Samping</label>
                                    <b-form-input autocomplete="off" type="text" ></b-form-input>
                                </div>
                                <div class="col-12 col-lg px-0 px-lg-3">
                                    <label>Tanggal Kejadian</label>
                                    <b-form-input autocomplete="off" type="date" ></b-form-input>
                                </div>
                            </div>
                            <div class="col mt-16 mt-lg-24 d-flex">
                                <div class="ml-auto row mx-0">
                                    <b-button pill variant="outline-indigo" class="px-3 mr-2"
                                        @click="historypregnantForm = !historypregnantForm">Batal</b-button>
                                    <b-button pill variant="indigo" @click.prevent="validationFormAllergy" class="px-3">Simpan</b-button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="mt-16 mt-lg-32">
                <ModernLabel label="Kebutuhan Komunikasi dan Edukasi" />
                <div class="row mt-10 mt-lg-20">
                    <div class="col-lg-4">
                        <div>
                            <label class="c-form-label">Bicara</label>
                            <b-form-radio-group size="sm" v-model="form.speech_disorders_type_id">
                                <b-form-radio value="0">Normal</b-form-radio>
                                <b-form-radio value="1">Gangguan, jelaskan</b-form-radio>
                            </b-form-radio-group>
                            <b-form-input v-model="form.speech_disorders" :disabled="form.speech_disorders_type_id !== '1'" class="mt-10"></b-form-input>
                        </div>
                    </div>
                    <div class="col-lg-4 mt-20 mt-lg-0">
                        <div>
                            <label class="c-form-label">Dibutuhkan Penerjemah</label>
                            <b-form-radio-group size="sm" v-model="form.translator_type_id">
                                <b-form-radio value="0">Tidak</b-form-radio>
                                <b-form-radio value="1">Ya, sebutkan</b-form-radio>
                            </b-form-radio-group>
                            <b-form-input v-model="form.translator" :disabled="form.translator_type_id !== '1'" class="mt-10"></b-form-input>
                        </div>
                    </div>
                    <div class="col-lg-4 mt-20 mt-lg-0">
                        <div class="mt-16">
                            <label class="c-form-label">Bahasa Isyarat</label>
                            <b-form-radio-group size="sm" v-model="form.sign_language_type_id">
                                <b-form-radio value="0">Tidak</b-form-radio>
                                <b-form-radio value="1">Ya</b-form-radio>
                            </b-form-radio-group>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-16 mt-lg-32">
                <ModernLabel label="Risiko Cedera / Jatuh" />
                <div class="mt-20">
                    <label class="c-form-label font-weight-bold">a. Perhatikan cara berjalan pasien saat akan duduk di kursi. Apakah pasien tampak tidak seimbang (sempoyongan)</label>
                    <b-form-radio-group size="sm" v-model="form.risk_injury_a_type_id">
                        <b-form-radio value="0">Tidak</b-form-radio>
                        <b-form-radio value="1">Ya</b-form-radio>
                    </b-form-radio-group>
                </div>
                <div class="mt-20">
                    <label class="c-form-label font-weight-bold">b. Apakah pasien memegang pinggiran kursi atau meja atau benda lain sebagai penopang saat akan duduk?</label>
                    <b-form-radio-group size="sm" v-model="form.risk_injury_b_type_id">
                        <b-form-radio value="0">Tidak</b-form-radio>
                        <b-form-radio value="1">Ya</b-form-radio>
                    </b-form-radio-group>
                </div>
                <div class="col mt-10">
                    <div>
                        <label class="c-form-label">Hasil</label>
                        <b-form-radio-group size="sm" v-model="form.risk_injury_result">
                            <b-form-radio class="my-1" value="1">Tidak Berisiko (tidak ditemukan a dan b)</b-form-radio>
                            <b-form-radio class="my-1" value="2">Risiko Rendah (ditemukan a dan b)</b-form-radio>
                            <b-form-radio class="my-1" value="3">Risiko Tinggi (a dan b ditemukan)</b-form-radio>
                        </b-form-radio-group>
                    </div>
                    <div class="mt-10">
                        <label class="c-form-label">Diberitahukan ke dokter</label>
                        <div class="row mx-0 align-items-lg-center">
                            <b-form-radio-group size="sm" v-model="form.tell_doctor_type_id">
                                <b-form-radio class="my-1" value="0">Tidak</b-form-radio>
                                <b-form-radio class="my-1" value="1">Ya, pukul</b-form-radio>
                            </b-form-radio-group>
                            <b-form-input v-model="form.tell_doctor_date" type="time" :disabled="form.tell_doctor_type_id !== '1'" class="mt-10 col-lg-2"></b-form-input>
                        </div>
                    </div>
                    <p class="text-bold-12 mt-12">Status Fungsional</p>
                    <div class="row">
                        <div class="col-lg-4 mt-10 mt-lg-0">
                            <label class="c-form-label">Aktivitas dan Mobilisasi</label>
                            <b-form-radio-group size="sm" v-model="form.functional_status_type_id">
                                <b-form-radio value="0">Mandiri</b-form-radio>
                                <b-form-radio value="1">Perlu bantuan, sebutkan</b-form-radio>
                            </b-form-radio-group>
                            <b-form-input v-model="form.functional_status" :disabled="form.functional_status_type_id !== '1'" class="mt-10"></b-form-input>
                        </div>
                        <div class="col-lg-4 mt-10 mt-lg-0">
                            <label class="c-form-label">Alat Bantu, sebutkan</label>
                            <b-form-input v-model="form.aids"/>
                        </div>
                    </div>
                </div>
                <div class="mt-20">
                    <small class="font-italic text-muted">*Bila terdapat gangguan fungsional, pasien di konsul ke Rehabilitasi Medis melalui DPJP</small>
                </div>
            </div>
        </section>
        <!-- <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Skala Nyeri
            </div>
            <div class="row">
                <div class="col-lg-6 mt-20">
                    <ModernLabel label="Apakah terdapat keluhan nyeri?" />
                    <b-form-radio-group v-model="form.RadioPainful" class="mt-8 mt-lg-20">
                        <b-form-radio value="0">Tidak</b-form-radio>
                        <b-form-radio value="1">Ya, dimana?</b-form-radio>
                    </b-form-radio-group>
                    <b-form-input class="mt-10 col-lg-8" :disabled="form.RadioPainful === '0'" />
                </div>
                <div class="col-lg-6 mt-20">
                    <ModernLabel label="Nyeri Hilang Bila" />
                    <b-form-checkbox-group v-model="form.CheckPainAway" size="sm" class="mt-16">
                        <b-form-checkbox value="a" class="col-lg-4 my-1 mr-0">Minum Obat</b-form-checkbox>
                        <b-form-checkbox value="b" class="col-lg-4 my-1 mr-0">Mendengar Musik</b-form-checkbox>
                        <b-form-checkbox value="c" class="col-lg-4 my-1 mr-0">Istirahat</b-form-checkbox>
                        <b-form-checkbox value="d" class="col-lg-4 my-1 mr-0">Berubah Posisi Tidur</b-form-checkbox>
                        <b-form-checkbox value="e" class="col-lg-4 my-1 mr-0">Lain-lain</b-form-checkbox>
                    </b-form-checkbox-group>
                    <b-form-input class="mt-10 col-lg-8" :disabled="form.CheckPainAway.indexOf('e') === -1" />
                </div>
            </div>
            <div class="mt-20">
                <ModernLabel label="Metoda" />
                <b-form-radio-group v-model="form.RadioMetoda" class="mt-8 mt-lg-20">
                    <b-form-radio value="a">NIPS</b-form-radio>
                    <b-form-radio value="b">FLACC</b-form-radio>
                    <b-form-radio value="c">VAS</b-form-radio>
                    <b-form-radio value="d">BPS</b-form-radio>
                    <b-form-radio value="e">Wong-Baker</b-form-radio>
                    <b-form-radio value="f">NRS</b-form-radio>
                </b-form-radio-group>
            </div>
            <div class="mt-32">
                <b-table fixed responsive small head-variant="light" :items="PainTable.items" :fields="PainTable.fields" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12">
                    <template #cell(total)>
                        <div>
                            <b-form-input type="number" placeholder="-" />
                        </div>
                    </template>
                </b-table>
            </div>
            <div class="row mt-20">
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Skor</label>
                    <b-input-group prepend="Skor">
                        <b-form-input type="number" />
                    </b-input-group>
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Durasi</label>
                    <b-form-input type="number" />
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Lokasi</label>
                    <b-form-input />
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Respon</label>
                    <b-form-input />
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Frekuensi</label>
                    <b-form-input type="number" />
                </div>
            </div>
        </section> -->
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
                style="margin-left:-16px; margin-right:-16px;">Nutrisi
            </div>
            <div class="mt-20">
                <div class="mt-20">
                    <div>
                        <b-form-checkbox v-model="form.nutrition_arry" size="sm">Untuk pasien dengan masalah obstetri / Kehamilan / Nifas:</b-form-checkbox>
                        <div class="mt-8">
                            <b-table-simple>
                                <b-thead class="text-semi-14">
                                    <b-tr>
                                        <b-th>No</b-th>
                                        <b-th>Parameter</b-th>
                                        <b-th>Penilaian</b-th>
                                    </b-tr>
                                </b-thead>
                                <b-tbody class="text-regular-12">
                                    <b-tr>
                                        <b-td>1</b-td>
                                        <b-td>Apakah asupan makan berkurang karena nafsu makan berkurang?</b-td>
                                        <b-td>
                                            <b-form-radio-group v-model="form.nutrition_one" :disabled="form.nutrition_arry !== true" size="sm">
                                                <b-form-radio value="0">Tidak</b-form-radio>
                                                <b-form-radio value="1">Ya</b-form-radio>
                                            </b-form-radio-group>
                                        </b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td>2</b-td>
                                        <b-td>
                                            <div>Ada gangguan metabolism (DM, gangguan fungsi tiroid, infeksi kronis, lain-lain (sebutkan)</div>    
                                            <b-form-input v-model="form.nutrition_two_answer" :disabled="form.nutrition_arry !== true" class="mt-10" />
                                        </b-td>
                                        <b-td>
                                            <b-form-radio-group v-model="form.nutrition_two" :disabled="form.nutrition_arry !== true" size="sm">
                                                <b-form-radio value="0">Tidak</b-form-radio>
                                                <b-form-radio value="1">Ya</b-form-radio>
                                            </b-form-radio-group>
                                        </b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td>3</b-td>
                                        <b-td>Ada pertambahan berat badan yang kurang atau lebih sesuai usia kehamilan</b-td>
                                        <b-td>
                                            <b-form-radio-group v-model="form.nutrition_three" :disabled="form.nutrition_arry !== true" size="sm">
                                                <b-form-radio value="0">Tidak</b-form-radio>
                                                <b-form-radio value="1">Ya</b-form-radio>
                                            </b-form-radio-group>
                                        </b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td>4</b-td>
                                        <b-td>Nilai Hb &lt; 10b g/dl atau HCT &lt; 30%</b-td>
                                        <b-td>
                                            <b-form-radio-group v-model="form.nutrition_four" :disabled="form.nutrition_arry !== true" size="sm">
                                                <b-form-radio value="0">Tidak</b-form-radio>
                                                <b-form-radio value="1">Ya</b-form-radio>
                                            </b-form-radio-group>
                                        </b-td>
                                    </b-tr>
                                </b-tbody>
                            </b-table-simple>
                        </div>
                        <div class="mt-32">
                            <p class="mb-0 text-medium-14">Bila jawaban ya ≥ 1 dilaporkan kepada Tim Terapi Gizi</p>
                            <div class="row">
                                <div class="col-lg-3">
                                    <label class="c-form-label">Tanggal</label>
                                    <b-form-input v-model="form.nutrition_date" :disabled="form.nutrition_arry !== true" type=date></b-form-input>
                                </div>
                                <div class="col-lg-3">
                                    <label class="c-form-label">Jam</label>
                                    <b-form-input v-model="form.nutrition_time" :disabled="form.nutrition_arry !== true" type=time></b-form-input>
                                </div>
                            </div>
                        </div>
                    </div>
                    <b-form-checkbox v-model="form.ginekologi_problem" class="mt-32" size="sm">Untuk pasien dengan masalah Ginekologi / Onkelogi</b-form-checkbox>
                    <b-table-simple responsive small class="mt-8">
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
                                    <b-form-input v-model="form.nutrition_total_score" :disabled="form.ginekologi_problem !== true" type="number" placeholder="-" />
                                </b-td>
                            </b-tr>
                            <b-tr>
                                <b-td class="text-center">3</b-td>
                                <b-td colspan="2">
                                    <div>Pasien dengan diagnosa khusus :</div>
                                    <b-form-radio-group v-model="form.patient_special_diagnose_type_id" class="mt-10">
                                        <b-form-radio value="0">Tidak</b-form-radio>
                                        <b-form-radio value="1">Ya</b-form-radio>
                                    </b-form-radio-group>
                                    <b-form-checkbox-group v-model="form.patient_special_diagnose_arry" :disabled="form.patient_special_diagnose_type_id !== '1'" class="mt-10">
                                        <b-form-checkbox value="a">DM</b-form-checkbox>
                                        <b-form-checkbox value="b">Ginjal</b-form-checkbox>
                                        <b-form-checkbox value="c">Hati</b-form-checkbox>
                                        <b-form-checkbox value="d">Jantung</b-form-checkbox>
                                        <b-form-checkbox value="e">Paru</b-form-checkbox>
                                        <b-form-checkbox value="f">Stroke</b-form-checkbox>
                                        <b-form-checkbox value="g">Kanker</b-form-checkbox>
                                        <b-form-checkbox value="h">Penurunan Imunitas</b-form-checkbox>
                                        <b-form-checkbox value="i">Geriatri</b-form-checkbox>
                                        <b-form-checkbox value="j">Lain-lain</b-form-checkbox>
                                    </b-form-checkbox-group>
                                    <b-form-input v-model="form.patient_special_diagnose" :disabled="form.patient_special_diagnose_type_id !== '1' || form.patient_special_diagnose_arry.indexOf('j') === -1 " class="mt-10" />
                                </b-td>
                            </b-tr>
                        </b-tbody>
                    </b-table-simple>
                    <div class="mt-16">
                        <p class="text-bold-14">Bila skor ≥ 2 dan atau pasien dengan diagnosis / kondisi khusus dilakukan pengkajian lanjut oleh Tim Terapi Gizi</p>
                        <div>
                            <label class="c-form-label">Sudah dilaporkan ke Tim Terapi Giz</label>
                            <b-form-radio-group v-model="form.reported_nutritional_type_id" size="sm">
                                <b-form-radio value="0">Tidak</b-form-radio>
                                <b-form-radio value="1">Ya, tanggal & jam</b-form-radio>
                            </b-form-radio-group>
                            <b-form-input v-model="form.reported_nutritional_date" :disabled="form.reported_nutritional_type_id !== '1'" class="mt-10" />
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                style="margin-left:-16px; margin-right:-16px;">Daftar Masalah Kebidanan
            </div>
            <div class="row mt-lg-20">
                <div class="col-lg">
                    <label class="c-form-label">Masalah Kebidanan</label>
                    <b-textarea v-model="form.midwifery_problem" rows="5"></b-textarea>
                </div>
                <div class="col-lg mt-10 mt-lg-0">
                    <label class="c-form-label">Tujuan/Target Terukur</label>
                    <b-textarea v-model="form.midwifery_goals" rows="5"></b-textarea>
                </div>
            </div>
            <b-form-checkbox v-model="form.midwifery_type_id" size="sm" class="mt-10">Disusun Rencana Kebidanan</b-form-checkbox>
        </section>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import vSelect from 'vue-select'
import ModernLabel from '@/@core/components/label/ModernLabel.vue';

export default {
    name: 'nurse_obgyn',
    components: { vSelect, ModernLabel },
    data() {
        return {
            // # TYPE_NURSE_ID 3 = OBGYN
            historypregnantForm: false,
            form: {
                // Riwayat Kesehatan
                history_disease_type_id: '0',
                history_disease: '',
                been_hospitalized_type_id: '0',
                been_hospitalized_answer: '',
                been_hospitalized_date: '',
                been_hospitalized_place: '',
                any_surgery_type_id: '0',
                any_surgery_date: '',
                any_surgery_place: '',
                under_treatment_type_id: '',
                under_treatment: '',
                any_history_family_disease_type_id: '0',
                any_history_family_answer_arry: [],
                any_history_family_answer: '',
                dependence_substance_type_id: '',
                dependence_substance_arry: [],
                dependence_substance: '',
                employment_history_type_id: '0',
                employment_history: '',
                allergy_history_type_id: '',
                allergy_history_arry: [],
                allergy_history_medicine: '',
                allergy_history_food: '',
                allergy_history_other: '',
                marriage_history_type_id: '0',
                marriage_history: '',
                contraception_use_type_id: '0',
                contraception_use: '',
                contraception_length_of_use: '',
                contraception_use_complaint: '',
                age_first_marriage: '',
                marriage_one: '',
                marriage_two: '',
                marriage_three: '',
                period_menarche_age: '',
                period_cycle: '',
                period_volume: '',
                period_regular_type_id: '',
                period_regular: '',
                period_complaint_type_id: '',
                period_complaint: '',
                hpht: '',
                estimated_delivery: '',
                gestational_age: '',
                gynecological_disease_type_id: '0',
                gynecological_disease_arry: [],
                gynecological_disease: '',
                speech_disorders_type_id: '0',
                speech_disorders: '',
                translator_type_id: '0',
                translator: '',
                sign_language_type_id: '0',
                risk_injury_a_type_id: '0',
                risk_injury_b_type_id: '0',
                risk_injury_result: '',
                tell_doctor_type_id: '0',
                tell_doctor_date: '', // pake time, tp di postman pake datetime
                functional_status_type_id: '0',
                functional_status: '',
                aids: '',

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

                // Nutrisi
                nutrition_arry: false, // pake boolean, tp di postman pake arry
                nutrition_one: '0',
                nutrition_two: '0',
                nutrition_two_answer: '', // di postman belum ada
                nutrition_three: '0',
                nutrition_four: '0' ,
                nutrition_date: '', // pake date, tp di postman pake datetime
                nutrition_time: '', // di postman belum ada
                ginekologi_problem: false, // di postman belum ada
                nutrition_total_score: '',
                patient_special_diagnose_type_id: '0',
                patient_special_diagnose_arry: [],
                patient_special_diagnose: '',
                reported_nutritional_type_id: '0', // di postman belum ada
                reported_nutritional_date: '', // di postman belum ada

                // Daftar Masalah Kebidanan
                midwifery_problem: '',
                midwifery_goals: '',
                midwifery_type_id: false, // pake boolean, tp di postman pake string


                RadioPainful: '',
                CheckPainAway: [],
                RadioMetoda: '',
            },
            PainTable: {
                fields: [
                    { key: 'no', label: '-' },
                    { key: 'zero', label: '0' },
                    { key: 'one', label: '1' },
                    { key: 'two', label: '2' },
                    { key: 'total', label: 'Total' },
                ],
                items: [
                    {
                        no: 'FACE',
                        zero: 'Tidak ekspresisi atau dalam keadaan tersenyum',
                        one: 'Menyeringai, mengerutkan dahi, tampak tidak tertarik',
                        two:'Dagi gemetar, gigi gemeretak, rahang terkancing'
                    },
                    {
                        no: 'LEGS',
                        zero: 'Normal, rileks',
                        one: 'Gelisah, tegang, resah',
                        two:'Menendang, kaki tertekuk atau membengkokan kaki'
                    },
                    {
                        no: 'ACTIVITY',
                        zero: 'Berbaring terlentang, posisi normal, gerakan mudah',
                        one: 'Menggeliat, tidak bisa diam, tegang',
                        two:'Kaku, kejang'
                    },
                    {
                        no: 'CRY',
                        zero: 'Tidak menangis',
                        one: 'Merintih, merengek',
                        two:'Terus menangis, berteriak, sering mengeluh'
                    },
                    {
                        no: 'CONSOLABILITY',
                        zero: 'Rileks',
                        one: 'Kadang mengeluh, dapat ditenangkan dengan sentuhan, pelukan, bujukan, dapat dialihkan',
                        two:'-'
                    },
                ]
            },
        }
    },
    watch: {
        //  =========================  RIWAYAT PENYAKIT DAHULU/ SEBELUMNYA  =========================
        "form.history_disease_type_id"(newValue) {
            if (newValue !== "1") {
                this.form.history_disease = ""
            }
        },

        //  =========================  PERNAH DIRAWAT SEBELUMNYA  =========================
        "form.been_hospitalized_type_id"(newValue) {
            if (newValue !== "1") {
                this.form.been_hospitalized_answer = ''
                this.form.been_hospitalized_date = ''
                this.form.been_hospitalized_place = ''
            }
        },
        
        //  =========================  PERNAH DIOPERASI  =========================
        "form.any_surgery_type_id"(newValue) {
            if (newValue !== "1") {
                this.form.any_surgery_date = ''
                this.form.any_surgery_place = ''
            }
        },

        //  =========================  MASIH DALAM PENGOBATAN  =========================
        "form.under_treatment_type_id"(newValue) {
            if (newValue !== "1") {
                this.form.under_treatment = ''
            }
        },
        
        //  =========================  RIWAYAT PENYAKIT KELUARGA  =========================
        "form.any_history_family_disease_type_id"(newValue) {
            if (newValue !== "1") {
                this.form.any_history_family_answer_arry = []
                this.form.any_history_family_answer = ''
            }
        },
        "form.any_history_family_answer_arry"(newValues) {
            if (newValues && !newValues.includes("g")) {
                this.form.any_history_family_answer = ''
            }
        },
        
        //  =========================  KETERGANTUNGAN TERHADAP ZAT TERTENTU  =========================
        "form.dependence_substance_type_id"(newValue) {
            if (newValue !== "1") {
                this.form.dependence_substance_arry = []
                this.form.dependence_substance = ''
            }
        },
        "form.dependence_substance_arry"(newValues) {
            if (newValues && !newValues.includes("d")) {
                this.form.dependence_substance = ''
            }
        },

        //  =========================  RIWAYAT PEKERJAAN  =========================
        "form.employment_history_type_id"(newValue) {
            if (newValue !== "1") {
                this.form.employment_history = ''
            }
        },

        //  =========================  RIWAYAT ALERGI  =========================
        "form.allergy_history_type_id"(newValue) {
            if (newValue !== "1") {
                this.form.allergy_history_arry = []
                this.form.allergy_history_medicine = ''
                this.form.allergy_history_food = ''
                this.form.allergy_history_other = ''
            }
        },
        "form.allergy_history_arry"(newValues) {
            if (newValues && !newValues.includes("a")) {
                this.form.allergy_history_medicine = ''
            }
            if (newValues && !newValues.includes("b")) {
                this.form.allergy_history_food = ''
            }
            if (newValues && !newValues.includes("c")) {
                this.form.allergy_history_other = ''
            }
        },

        //  =========================  STATUS PERNIKAHAN  =========================
        "form.marriage_history_type_id"(newValue) {
            if (newValue !== "4") {
                this.form.marriage_history = ''
            }
        },
        
        //  =========================  RIWAYAT PEMAKAIAN ALAT KONTRASEPSI  =========================
        "form.contraception_use_type_id"(newValue) {
            if (newValue !== "1") {
                this.form.contraception_use = ''
                this.form.contraception_length_of_use = ''
                this.form.contraception_use_complaint = ''
            }
        },

        //  =========================  RIWAYAT MENSTRUASI  =========================
        "form.period_regular_type_id"(newValue) {
            if (newValue !== "1") {
                this.form.period_regular = ''
            }
        },
        "form.period_complaint_type_id"(newValue) {
            if (newValue !== "1") {
                this.form.period_complaint = ''
            }
        },

        //  =========================  RIWAYAT PENYAKIT GINEKOLOGI  =========================
        "form.gynecological_disease_type_id"(newValue) {
            if (newValue !== "1") {
                this.form.gynecological_disease_arry = []
                this.form.gynecological_disease = ''
            }
        },
        "form.gynecological_disease_arry"(newValues) {
            if (newValues && !newValues.includes("h")) {
                this.form.gynecological_disease = ''
            }
        },


        //  =========================  KEBUTUHAN KOMUNIKASI DAN EDUKASI  =========================
        "form.speech_disorders_type_id"(newValue) {
            if (newValue !== "1") {
                this.form.speech_disorders = ''
            }
        },
        "form.translator_type_id"(newValue) {
            if (newValue !== "1") {
                this.form.translator = ''
            }
        },

        //  =========================  RISIKO CEDERA / JATUH  =========================
        "form.tell_doctor_type_id"(newValue) {
            if (newValue !== "1") {
                this.form.tell_doctor_date = ''
            }
        },
        "form.functional_status_type_id"(newValue) {
            if (newValue !== "1") {
                this.form.functional_status = ''
            }
        },
        "form.nutrition_arry"(newValue) {
            if (newValue === false) {
                this.form.nutrition_one = '0'
                this.form.nutrition_two = '0'
                this.form.nutrition_two_answer = ''
                this.form.nutrition_three = '0'
                this.form.nutrition_four = '0'
                this.form.nutrition_date = ''
                this.form.nutrition_time = ''
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

        //  =========================  RISIKO CEDERA / JATUH  =========================
        "form.ginekologi_problem"(newValue) {
            if (newValue !== true) {
                this.form.nutrition_total_score = ''
            }
        },

        //  =========================  RISIKO CEDERA / JATUH  =========================
        "form.patient_special_diagnose_type_id"(newValue) {
            if (newValue !== '1') {
                this.form.patient_special_diagnose_arry = []
                this.form.patient_special_diagnose = ''
            }
        },
        "form.patient_special_diagnose_arry"(newValues) {
            if (newValues && !newValues.includes("j")) {
                this.form.patient_special_diagnose = ''
            }
        },

        //  =========================  RISIKO CEDERA / JATUH  =========================
        "form.reported_nutritional_type_id"(newValue) {
            if (newValue !== '1') {
                this.form.reported_nutritional_date = ''
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

        // resetForm(){
        //     this.form.isolation_criteria = ''
        // },

    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>
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