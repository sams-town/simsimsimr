<template>
    <div>
        <div class="d-flex">
            <b-button @click="openModal" class="mr-2">Open Modal</b-button>
            <b-button @click="openSecondModal">Open Second Modal</b-button>
        </div>
        <b-modal v-model="active.main_modal" scrollable size="lg" title="CATATAN PERSALINAN" hide-footer>
            <section>
                <!-- <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                    style="margin-left:-16px; margin-right:-16px;">CATATAN PERSALINAN
                </div> -->
                <section class="">
                    <div>
                        <label class="c-form-label">Tanggal</label>
                        <b-form-input v-model="form_record.date_birth" type="date" />
                    </div>
                    <!-- <div class="mt-12">
                        <label class="c-form-label">Nama Bidan</label>
                        <b-form-input v-model="form_record."/>
                    </div> -->
                    <div class="mt-12">
                        <label class="c-form-label">Tempat Persalinan</label>
                        <b-form-radio-group v-model="form_record.birthing_place_type_id" class="">
                            <b-form-radio value="1">Rumah Ibu</b-form-radio>
                            <b-form-radio value="2">Polindes</b-form-radio>
                            <b-form-radio value="3">Klinik Swasta</b-form-radio>
                            <b-form-radio value="4">Puskesmas</b-form-radio>
                            <b-form-radio value="5">Rumah Sakit</b-form-radio>
                            <b-form-radio value="6">Lainnya</b-form-radio>
                        </b-form-radio-group>
                        <template v-if="form_record.birthing_place_type_id == 6">                                
                            <b-form-input v-model="form_record.birthing_place_other" class="mt-10" />
                        </template>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Alamat tempat persalinan</label>
                        <b-textarea v-model="form_record.birthing_place_address" rows="2" />                                 
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Catatan</label>
                        <b-textarea v-model="form_record.note" rows="2" />                                 
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Alasan merujuk</label>
                        <b-textarea v-model="form_record.referral_reason" rows="2" />                                 
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Tempat Rujukan</label>
                        <b-form-input v-model="form_record.referral_place" />
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Pendamping pada saat merujuk</label>
                        <b-form-radio-group v-model="form_record.referral_companion_type_id" class="">
                            <b-form-radio value="1">Bidan</b-form-radio>
                            <b-form-radio value="2">Teman</b-form-radio>
                            <b-form-radio value="3">Suami</b-form-radio>
                            <b-form-radio value="4">Dukun</b-form-radio>
                            <b-form-radio value="5">Keluarga</b-form-radio>
                            <b-form-radio value="6">Tidak ada</b-form-radio>
                        </b-form-radio-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Masalah dalam kehamilan/persalinan ini</label>
                        <b-form-radio-group v-model="form_record.problem_type_id" class="">
                            <b-form-radio value="1">Gawat daruratn</b-form-radio>
                            <b-form-radio value="2">Pendarahan</b-form-radio>
                            <b-form-radio value="3">HDK</b-form-radio>
                            <b-form-radio value="4">Infeksi</b-form-radio>
                            <b-form-radio value="5">PMTCT</b-form-radio>
                        </b-form-radio-group>
                    </div>
    
                </section>
            </section>

            <section class="mt-32">
                <ModernLabel label="KALA I" />
                <div class="mt-16">
                    <div>
                        <label class="c-form-label">Temuan pada fase laten</label>
                        <b-form-input v-model="form_kala.kala_one_latent_phase" />
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Perlu Intervensi</label>
                        <b-form-radio-group v-model="form_kala.kala_one_intervention_type_id" class="">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya</b-form-radio>
                        </b-form-radio-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Grafis dilatasi melewati garis waspada</label>
                        <b-form-radio-group v-model="form_kala.kala_one_alert_line_type_id" class="">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya</b-form-radio>
                        </b-form-radio-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Masalah pada fase aktif, sebutkan</label>
                        <b-form-input v-model="form_kala.kala_one_active_phase"/>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Penatalaksanaan maslaah tersebut</label>
                        <b-form-input v-model="form_kala.kala_one_problem_management" />
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Hasil</label>
                        <b-textarea v-model="form_kala.kala_one_result" rows="2"/>                              
                    </div>
                </div>
            </section>

            <section class="mt-32">
                <ModernLabel label="KALA II" />
                <div class="mt-16">
                    <div>
                        <label class="c-form-label">Episiotomi</label>
                        <b-form-radio-group v-model="form_kala.kala_two_episiotomy_type_id" class="">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya, Indikasi</b-form-radio>
                        </b-form-radio-group>
                        <template v-if="form_kala.kala_two_episiotomy_type_id == 1">
                            <b-form-input v-model="form_kala.kala_two_episiotomy" class="mt-10" />                                
                        </template>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Pendampingan pada saat persalinan</label>
                        <b-form-radio-group v-model="form_kala.kala_two_companion_type_id" class="">
                            <b-form-radio value="1">Suami</b-form-radio>
                            <b-form-radio value="2">Keluarga</b-form-radio>
                            <b-form-radio value="3">Teman</b-form-radio>
                            <b-form-radio value="4">Dukun</b-form-radio>
                            <b-form-radio value="5">Tidak ada</b-form-radio>
                        </b-form-radio-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Gawat Janin</label>
                        <b-form-radio-group v-model="form_kala.kala_two_fetal_distress_type_id" class="">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya, tindakan yang dilakukan</b-form-radio>
                        </b-form-radio-group>
                    </div>
                    <div v-if="form_kala.kala_two_fetal_distress_type_id == 1" class="mt-16">
                        <b-table-simple responseived bordered style="font-size: 13px">
                            <b-thead>
                                <b-tr>
                                    <b-td>No</b-td>
                                    <b-td>Tindakan</b-td>
                                    <b-td>Aksi</b-td>
                                </b-tr>
                            </b-thead>
                            <b-tbody>
                                <b-tr v-for="(item, index) in form_kala.kala_two_fetal_distress_arry" :key="index">  
                                    <b-td>{{ index+1 }}</b-td>
                                    <b-td>{{ item }}</b-td>
                                    <b-td>
                                        <b-button @click="removeFetal(index)" variant="danger-700" size="sm" >
                                            <feather-icon icon="TrashIcon" />
                                        </b-button>
                                    </b-td>
                                </b-tr>
                            </b-tbody>
                        </b-table-simple>
                        <div class="mt-12">
                            <b-textarea v-model="input_fetal" />                                    
                            <b-button @click="addFetal" variant="primary" class="mt-12">Tambah</b-button>
                        </div>
                    </div>
                    <div v-if="form_kala.kala_two_fetal_distress_type_id == 0" class="mt-12">
                        <label class="c-form-label">Pemantauan DJJ setiap 5-10 menit selama kala II, Hasil</label>
                        <b-textarea v-model="form_kala.kala_two_monitoring_result" rows="2" />                                    
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Distosia Bahu</label>
                        <b-form-radio-group v-model="form_kala.kala_two_shoulder_dystocia_type_id" class="">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya, Tindakan yang dilakukan</b-form-radio>
                        </b-form-radio-group>
                        <template v-if="form_kala.form_kalakala_two_shoulder_dystocia_type_id == 1">
                            <b-form-input v-model="form_kala.kala_two_shoulder_dystocia" class="mt-10" />
                        </template>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Masalah lain, Penatalaksanaan masalah tsb dan hasilnya</label>
                        <b-textarea v-model="form_kala.kala_two_problem_management" rows="2"/>                                    
                    </div>
                </div>
            </section>

            <section class="mt-32">
                <ModernLabel label="KALA III" />
                <div class="mt-16">
                    <div>
                        <label class="c-form-label">Inisiasi Menyusu Dini</label>
                        <b-form-radio-group v-model="form_kala.kala_three_initiation_type_id" class="">
                            <b-form-radio value="0">Tidak, alasannya</b-form-radio>
                            <b-form-radio value="1">Ya</b-form-radio>
                        </b-form-radio-group>
                        <template v-if="form_kala.kala_three_initiation_type_id == 1">                                
                            <b-form-input v-model="form_kala.kala_three_initiation" class="mt-10" />
                        </template>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Lama kala III</label>
                        <b-input-group append="menit">
                            <b-form-input v-model="form_kala.kala_three_long" type="number" autocomplete="off"></b-form-input>
                        </b-input-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Pemberian Oksitosin 10 U im</label>
                        <b-form-radio-group v-model="form_kala.kala_three_oxytocin_type_id" class="">
                            <b-form-radio value="0">Tidak, alasan</b-form-radio>
                            <b-form-radio value="1">Ya, waktu</b-form-radio>
                        </b-form-radio-group>
                        <template v-if="form_kala.kala_three_oxytocin_type_id == 0">                                
                            <b-form-input v-model="form_kala.kala_three_oxytocin_reason" placeholder="alasan"/>
                        </template>
                        <template v-else-if="form_kala.kala_three_oxytocin_type_id == 1">                                
                            <b-input-group append="menit sesudah persalinan">
                                <b-form-input v-model="form_kala.kala_three_oxytocin" type="number" placeholder="waktu" autocomplete="off"></b-form-input>
                            </b-input-group>
                        </template>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Pemberian ulang oksitosan (2x)</label>
                        <b-form-radio-group v-model="form_kala.kala_three_oxytocin_second_type_id" class="">
                            <b-form-radio value="0">Tidak, alasannya</b-form-radio>
                            <b-form-radio value="1">Ya</b-form-radio>
                        </b-form-radio-group>
                        <template v-if="form_kala.kala_three_oxytocin_second_type_id == 0">                                
                            <b-form-input v-model="form_kala.kala_three_oxytocin_second" class="mt-10" />      
                        </template>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Penegangan tali pusat terkendali</label>
                        <b-form-radio-group v-model="form_kala.kala_three_umbilical_cord_type_id" class="">
                            <b-form-radio value="0">Tidak, alasannya</b-form-radio>
                            <b-form-radio value="1">Ya</b-form-radio>
                        </b-form-radio-group>
                        <template v-if="form_kala.kala_three_umbilical_cord_type_id == 0">                                
                            <b-form-input v-model="form_kala.kala_three_umbilical_cord" class="mt-10" />      
                        </template>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Massase fundus uteri</label>
                        <b-form-radio-group v-model="form_kala.kala_three_uterine_fundus_type_id" class="">
                            <b-form-radio value="0">Tidak, alasannya</b-form-radio>
                            <b-form-radio value="1">Ya</b-form-radio>
                        </b-form-radio-group>
                        <template v-if="form_kala.kala_three_uterine_fundus_type_id == 0">                                
                            <b-form-input v-model="form_kala.kala_three_uterine_fundus" class="mt-10" />      
                        </template>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Plasenta dahi lengkap (intact)</label>
                        <b-form-radio-group v-model="form_kala.kala_three_placenta_type_id" class="">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya</b-form-radio>
                        </b-form-radio-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Jika tidak lengkap, tindakan yang dilakukan</label>
                        <!-- disini juga belom -->
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Plasenta tidak lahir &gt; 30 menit</label>
                        <b-form-radio-group v-model="form_kala.kala_three_placenta_not_type_id" class="">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya, Tindakan</b-form-radio>
                        </b-form-radio-group>
                        <template v-if="form_kala.kala_three_placenta_not_type_id == 1">                                
                            <b-form-input v-model="form_kala.kala_three_placenta_not" class="mt-10" />      
                        </template>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Laserasi</label>
                        <b-form-radio-group v-model="form_kala.kala_three_laceration_type_id" class="">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya, dimana</b-form-radio>
                        </b-form-radio-group>
                        <template v-if="form_kala.kala_three_laceration_type_id == 1">                                
                            <b-form-input v-model="form_kala.kala_three_laceration" class="mt-10" />      
                        </template>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Jika laserasi parineum, derajat</label>
                        <b-form-radio-group v-model="form_kala.kala_three_parineum_degree_type_id" class="">
                            <b-form-radio value="1">1</b-form-radio>
                            <b-form-radio value="2">2</b-form-radio>
                            <b-form-radio value="3">3</b-form-radio>
                            <b-form-radio value="4">4</b-form-radio>
                        </b-form-radio-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Jika laserasi parineum, tindakan</label>
                        <b-form-radio-group v-model="form_kala.kala_three_parineum_act_type_id" class="">
                            <b-form-radio value="1">Penjahitan, dengan / tanpa anestesi</b-form-radio>
                            <b-form-radio value="2">Tidak dijahit, alasan</b-form-radio>
                        </b-form-radio-group>
                        <template v-if="form_kala.kala_three_parineum_act_type_id == 2">                                
                            <b-form-input v-model="form_kala.kala_three_parineum_act" class="mt-10" />
                        </template>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Atoni uteri</label>
                        <b-form-radio-group v-model="form_kala.kala_three_uterine_atony_type_id" class="">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya, tindakan</b-form-radio>
                        </b-form-radio-group>
                        <template v-if="form_kala.kala_three_uterine_atony_type_id == 1">                                
                            <b-form-input v-model="form_kala.kala_three_uterine_atony" class="mt-10" />
                        </template>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Jumlah darah yang keluar / pendarahan</label>
                        <b-input-group append="ml">
                            <b-form-input v-model="form_kala.kala_three_amount_blood" type="number" autocomplete="off"></b-form-input>
                        </b-input-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Masalah lain pada kala III dan Penatalaksanaannya</label>
                        <b-form-input v-model="form_kala.kala_three_problem_management" />
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Hasilnya</label>
                        <b-textarea v-model="form_kala.kala_three_result" rows="2"/>                                    
                    </div>
                </div>
            </section>

            <section class="mt-32">
                <ModernLabel label="KALA IV" />
                <div class="mt-16">
                    <label class="c-form-label">Kondisi Ibu</label>
                    <div>
                        <label class="c-form-label">KU</label>
                        <b-form-input v-model="form_kala.kala_four_mother_ku" />
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">TD</label>
                        <b-input-group append="mmHg">
                            <b-form-input v-model="form_kala.kala_four_mother_td" type="number" autocomplete="off" />
                        </b-input-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Nadi</label>
                        <b-input-group append="x/i">
                            <b-form-input v-model="form_kala.kala_four_pulse" type="number" autocomplete="off" />
                        </b-input-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Napas</label>
                        <b-input-group append="x/i">
                            <b-form-input v-model="form_kala.kala_four_breath" type="number" autocomplete="off"></b-form-input>
                        </b-input-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Masalah kala IV dan Penatalaksanaannya</label>
                        <b-form-input v-model="form_kala.kala_four_problem_management" />
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Hasilnya</label>
                        <b-textarea v-model="form_kala.kala_four_result" rows="2" />                               
                    </div>
                </div>
            </section>

            <section class="mt-32">
                <ModernLabel label="Bayi baru lahir" />
                <div class="mt-16">
                    <div>
                        <label class="c-form-label">Berat Badan</label>
                        <b-input-group append="gram">
                            <b-form-input v-model="form_record.newborn_baby_weight" type="number" autocomplete="off" />
                        </b-input-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Panjang Badan</label>
                        <b-input-group append="cm">
                            <b-form-input v-model="form_record.newborn_baby_height" type="number" autocomplete="off" />
                        </b-input-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Jenis Kelamin</label>
                        <b-form-radio-group v-model="form_record.newborn_baby_sex_type_id" class="">
                            <b-form-radio value="1">Laki-laki</b-form-radio>
                            <b-form-radio value="2">Perempuan</b-form-radio>
                        </b-form-radio-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Penilaian Bayi Baru Lahir</label>
                        <b-form-radio-group v-model="form_record.newborn_baby_assessment_type_id" class="">
                            <b-form-radio value="1">Baik</b-form-radio>
                            <b-form-radio value="2">Ada penyulit</b-form-radio>
                        </b-form-radio-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Bayi Lahir</label>
                        <b-form-radio-group v-model="form_record.newborn_baby_type_id" class="">
                            <b-form-radio value="1">Normal</b-form-radio>
                            <b-form-radio value="2">Asfiksia</b-form-radio>
                            <b-form-radio value="3">Cacat Bawaan</b-form-radio>
                            <b-form-radio value="4">Hipotemi</b-form-radio>
                        </b-form-radio-group>
                    </div>
                    <div v-if="form_record.newborn_baby_type_id == 1" class="mt-12">
                        <label class="c-form-label">Tindakan</label>
                        <b-form-checkbox-group v-model="form_record.newborn_baby_normal_arry" class="">
                            <b-form-checkbox value="a" class="my-1">Menghangatkan</b-form-checkbox>
                            <b-form-checkbox value="b" class="my-1">Mengeringkan</b-form-checkbox>
                            <b-form-checkbox value="c" class="my-1">IMD atau naluri menyusu segera</b-form-checkbox>
                            <b-form-checkbox value="d" class="my-1">Rangsangan taktil</b-form-checkbox>
                            <b-form-checkbox value="e" class="my-1">Tetes mata profilaksis, vit K, Imunisasi Hepatitis B</b-form-checkbox>
                        </b-form-checkbox-group>
                    </div>
                    <div v-if="form_record.newborn_baby_type_id == 2" class="mt-12">
                        <label class="c-form-label">Tindakan</label>
                        <b-form-checkbox-group v-model="form_record.newborn_baby_asphyxia_arry" class="">
                            <b-form-checkbox value="a" class="my-1">Menghangatkan</b-form-checkbox>
                            <b-form-checkbox value="b" class="my-1">Mengeringkan</b-form-checkbox>
                            <b-form-checkbox value="c" class="my-1">Rangsangan taktil</b-form-checkbox>
                            <b-form-checkbox value="d" class="my-1">Asuhann pasca resusitasi</b-form-checkbox>
                            <b-form-checkbox value="e" class="my-1">Bebaskan jalan napas (posisi & isap lendir)</b-form-checkbox>
                            <b-form-checkbox value="f" class="my-1">Ventilasi positif (jika perlu)</b-form-checkbox>
                            <b-form-checkbox value="g" class="my-1">Lain-lain, sebutkan</b-form-checkbox>
                        </b-form-checkbox-group>
                        <template v-if="form_record.newborn_baby_asphyxia_arry.includes('g')">
                            <b-form-input v-model="form_record.newborn_baby_asphyxia" class="mt-10"/>
                        </template>
                    </div>
                    <div v-if="form_record.newborn_baby_type_id == 3" class="mt-12">
                        <label class="c-form-label">Sebutkan</label>
                        <b-form-input v-model="form_record.newborn_baby_disabled" class=""/>
                    </div>
                    <div v-if="form_record.newborn_baby_type_id == 4" class="mt-12">
                        <label class="c-form-label">Tindakan</label>
                        <b-table-simple responseived bordered style="font-size: 13px">
                            <b-thead>
                                <b-tr>
                                    <b-td>No</b-td>
                                    <b-td>Tindakan</b-td>
                                    <b-td>Aksi</b-td>
                                </b-tr>
                            </b-thead>
                            <b-tbody>
                                <b-tr v-for="(item, index) in form_record.newborn_baby_hypothermia_arry" :key="index">  
                                    <b-td>{{ index+1 }}</b-td>
                                    <b-td>{{ item }}</b-td>
                                    <b-td>
                                        <b-button @click="removeHipotemi(index)" variant="danger-700" size="sm" >
                                            <feather-icon icon="TrashIcon" />
                                        </b-button>
                                    </b-td>
                                </b-tr>
                            </b-tbody>
                        </b-table-simple>
                        <div class="mt-12">
                            <b-textarea v-model="input_hipotemi" />                                    
                            <b-button @click="addHipotemi" variant="primary" class="mt-12">Tambah</b-button>
                        </div>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Pemberian ASI setelah jam pertama bayi lahir</label>
                        <b-form-radio-group v-model="form_record.newborn_baby_breast_milk_type_id" class="">
                            <b-form-radio value="0">Tidak, alasan</b-form-radio>
                            <b-form-radio value="1">Ya, waktu</b-form-radio>
                        </b-form-radio-group>
                        <template v-if="form_record.newborn_baby_breast_milk_type_id == 0">
                            <b-form-input v-model="form_record.newborn_baby_breast_milk_not" class="mt-10"/>
                        </template>
                        <template v-if="form_record.newborn_baby_breast_milk_type_id == 1">
                            <b-input-group append="Jam setelah bayi lahir" class="mt-10">
                                <b-form-input v-model="form_record.newborn_baby_breast_milk_hours" type="number" autocomplete="off" />
                            </b-input-group>
                        </template>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Masalah lain</label>
                        <b-form-input v-model="form_record.newborn_baby_problem_management"/>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Penatalaksanaan dan Hasilnya</label>
                        <b-textarea v-model="form_record.newborn_baby_result" rows="2" />                        
                    </div>
                </div>
            </section>

            <section class="mt-32">
                <b-table responsived bordered :fields="kala.fields" :items="kala.items">
                    <template #cell(no)="row">{{ row.index+1 }}</template>
                    <template #cell(action)="row">
                        <b-button @click="removeKala(row.index)" variant="danger-700" size="sm" >
                            <feather-icon icon="TrashIcon" />
                        </b-button>
                    </template>
                </b-table>
                <div class="row">
                    <div class="col-lg-2 mt-12">
                        <label class="c-form-label">Jam ke</label>
                        <b-form-input v-model="input_kala.shift"/>
                    </div>
                    <div class="col-lg-2 mt-12">
                        <label class="c-form-label">Waktu</label>
                        <b-form-input type="time" v-model="input_kala.time"/>
                    </div>
                    <div class="col-lg-2 mt-12">
                        <label class="c-form-label">Tekanan Darah</label>
                        <b-form-input type="number" v-model="input_kala.blood_pressure"/>
                    </div>
                    <div class="col-lg-2 mt-12">
                        <label class="c-form-label">Tekanan Nadi</label>
                        <b-form-input type="number" v-model="input_kala.pulse"/>
                    </div>
                    <div class="col-lg-2 mt-12">
                        <label class="c-form-label">Temperatur (c)</label>
                        <b-form-input type="number" v-model="input_kala.temperature"/>
                    </div>
                    <div class="col-lg-2 mt-12">
                        <label class="c-form-label">Tinggi Fundus Uteri</label>
                        <b-form-input v-model="input_kala.fundus"/>
                    </div>
                    <div class="col-lg-2 mt-12">
                        <label class="c-form-label">Kontraksi Uterus</label>
                        <b-form-input v-model="input_kala.contraction"/>
                    </div>
                    <div class="col-lg-2 mt-12">
                        <label class="c-form-label">Kandung Kemih ≤ Urine</label>
                        <b-form-input v-model="input_kala.bladder"/>
                    </div>
                    <div class="col-lg-2 mt-12">
                        <label class="c-form-label">≤ Darah Keluar</label>
                        <b-form-input v-model="input_kala.out_blood"/>
                    </div>
                </div>
                <b-button @click="addKala" variant="primary" class="mt-12">Tambah</b-button>
            </section>

        </b-modal>

        <b-modal v-model="active.second_modal" scrollable size="lg" title="PERTOLONGAN TERHADAP BAYI" hide-footer>
            <section>
                <div>
                    <label class="c-form-label">Pengisapan Lendir</label>
                    <b-form-radio-group class="">
                        <b-form-radio value="0">Tidak</b-form-radio>
                        <b-form-radio value="1">Ya</b-form-radio>
                    </b-form-radio-group>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Rangsangan</label>
                    <b-form-radio-group class="">
                        <b-form-radio value="0">Tidak</b-form-radio>
                        <b-form-radio value="1">Ya</b-form-radio>
                    </b-form-radio-group>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Ambu</label>
                    <b-form-radio-group class="">
                        <b-form-radio value="0">Tidak</b-form-radio>
                        <b-form-radio value="1">Ya, lamanya</b-form-radio>
                    </b-form-radio-group>
                    <b-input-group append="menit" class="mt-10">
                        <b-form-input type="number" autocomplete="off" />
                    </b-input-group>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Message Jantung</label>
                    <b-form-radio-group class="">
                        <b-form-radio value="0">Tidak</b-form-radio>
                        <b-form-radio value="1">Ya, lamanya</b-form-radio>
                    </b-form-radio-group>
                    <b-input-group append="menit" class="mt-10">
                        <b-form-input type="number" autocomplete="off" />
                    </b-input-group>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Pasang ETT</label>
                    <b-form-radio-group class="">
                        <b-form-radio value="0">Tidak</b-form-radio>
                        <b-form-radio value="1">Ya, nomor</b-form-radio>
                    </b-form-radio-group>
                    <b-form-input class="mt-10" />
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Pemberian oksigen</label>
                    <b-form-radio-group class="">
                        <b-form-radio value="0">Tidak</b-form-radio>
                        <b-form-radio value="1">Ya</b-form-radio>
                    </b-form-radio-group>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Resusitasi</label>
                    <b-form-radio-group class="">
                        <b-form-radio value="0">Tidak</b-form-radio>
                        <b-form-radio value="1">Ya</b-form-radio>
                    </b-form-radio-group>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Mata diolesi dengan</label>
                    <b-textarea rows="2" />                                 
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Ujugn tali pusat diolesi dengan</label>
                    <b-textarea rows="2" />                                 
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Obat-oabt yang sudah diberikan</label>
                    <b-textarea rows="2" />                                 
                </div>
            </section>

            <section class="mt-32">
                <ModernLabel label="Ukuran Kepala Bayi" />
                <div class="">
                    <label class="c-form-label">Dist. Bitemporalis</label>
                    <b-input-group append="cm">
                        <b-form-input type="number" autocomplete="off" />
                    </b-input-group>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Dist. Biparietalis</label>
                    <b-input-group append="cm">
                        <b-form-input type="number" autocomplete="off" />
                    </b-input-group>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Dist. Fronto Occipitalis</label>
                    <b-input-group append="cm">
                        <b-form-input type="number" autocomplete="off" />
                    </b-input-group>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Dist. Mento Occipitalis</label>
                    <b-input-group append="cm">
                        <b-form-input type="number" autocomplete="off" />
                    </b-input-group>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Dist. Suboccipito Bregmatica</label>
                    <b-input-group append="cm">
                        <b-form-input type="number" autocomplete="off" />
                    </b-input-group>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Circ. Mento Occipitalis</label>
                    <b-input-group append="cm">
                        <b-form-input type="number" autocomplete="off" />
                    </b-input-group>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Circ. fronto Occipitalis (LK)</label>
                    <b-input-group append="cm">
                        <b-form-input type="number" autocomplete="off" />
                    </b-input-group>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Circ. Suboccipito Bregmatica</label>
                    <b-input-group append="cm">
                        <b-form-input type="number" autocomplete="off" />
                    </b-input-group>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Lingkar Dada</label>
                    <b-input-group append="cm">
                        <b-form-input type="number" autocomplete="off" />
                    </b-input-group>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Lingkar Lengan Atas</label>
                    <b-input-group append="cm">
                        <b-form-input type="number" autocomplete="off" />
                    </b-input-group>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Jenis Persalinan</label>
                    <b-form-radio-group class="">
                        <b-form-radio value="1">SC</b-form-radio>
                        <b-form-radio value="2">Spontan</b-form-radio>
                        <b-form-radio value="3">VE</b-form-radio>
                        <b-form-radio value="4">lainnya</b-form-radio>
                    </b-form-radio-group>
                    <b-form-input class="mt-10" />
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Ditolong Oleh</label>
                    <b-form-input class="mt-10" />
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Bayi Lahir</label>
                    <b-form-radio-group class="">
                        <b-form-radio value="1">Aterm</b-form-radio>
                        <b-form-radio value="2">Prematur</b-form-radio>
                        <b-form-radio value="3">Immatur</b-form-radio>
                        <b-form-radio value="4">Dysmatur</b-form-radio>
                        <b-form-radio value="5">Serotinus</b-form-radio>
                    </b-form-radio-group>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Usia Kehamilan</label>
                    <b-input-group append="mgg">
                        <b-form-input type="number" autocomplete="off" />
                    </b-input-group>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Ketuban Pecah</label>
                    <div class="row">
                        <div class="col-6">
                            <b-input-group prepend="Jam">
                                <b-form-input type="time" autocomplete="off" />
                            </b-input-group>
                        </div>
                        <div class="col-6">
                            <b-input-group prepend="warna">
                                <b-form-input autocomplete="off" />
                            </b-input-group>
                        </div>
                    </div>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Lilitan Tali Pusat</label>
                    <b-form-radio-group class="">
                        <b-form-radio value="0">Tidak</b-form-radio>
                        <b-form-radio value="1">Ya</b-form-radio>
                    </b-form-radio-group>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Catatan Khusus</label>
                    <b-textarea rows="3" />                                 
                </div>
                <div class="mt-12">
                    <label class="c-form-label">dipindahkan ke kamar bayi</label>
                    <div class="row">
                        <div class="col-6">
                            <b-input-group prepend="Tanggal">
                                <b-form-input type="date" autocomplete="off" />
                            </b-input-group>
                        </div>
                        <div class="col-6">
                            <b-input-group prepend="jam" append="WIB">
                                <b-form-input type="time" autocomplete="off" />
                            </b-input-group>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mt-32">
                <ModernLabel label="Identitas Bayi" />
                <div class="">
                    <label class="c-form-label">Dokter / Bidan Penolong</label>
                    <b-form-input autocomplete="off" />
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Nama Ibu</label>
                    <b-form-input autocomplete="off" />
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Nama Ayah</label>
                    <b-form-input autocomplete="off" />
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Jenis Kelamin</label>
                    <b-form-radio-group class="">
                        <b-form-radio value="1">Laki-laki</b-form-radio>
                        <b-form-radio value="2">Perempuan</b-form-radio>
                    </b-form-radio-group>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Berat Badan</label>
                    <b-input-group append="gram">
                        <b-form-input type="number" autocomplete="off" />
                    </b-input-group>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Panjang Badan</label>
                    <b-input-group append="cm">
                        <b-form-input type="number" autocomplete="off" />
                    </b-input-group>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Warna Kulit</label>
                    <b-form-input autocomplete="off" />
                </div>
            </section>
        </b-modal>
    </div>
</template>

<script>
import ModernLabel from '@/@core/components/label/ModernLabel.vue';

export default {
    name: 'DoctorStudies',
    components: {
        ModernLabel
    },
    data() {
        return {
            active: {
                main_modal: false,
                second_modal: false,
            },
            kala: {
                fields: [
                    { key: 'no', label: 'No', thClass: 'text-center', thStyle: 'width:60px' },
                    { key: 'shift', label: 'Jam ke' },    
                    { key: 'time', label: 'Waktu' },    
                    { key: 'blood_pressure', label: 'Tekanan darah' },    
                    { key: 'pulse', label: 'Nadi' },    
                    { key: 'temperature', label: 'Temp (C)' },    
                    { key: 'fundus', label: 'Tinggi Fundus Uteri' },    
                    { key: 'contraction', label: 'Kontraksi Uterus' },    
                    { key: 'bladder', label: 'Kandung Kemih ≤ Urine' },    
                    { key: 'out_blood', label: '≤ Darah Keluar' },    
                    { key: 'action', label: 'Aksi', thStyle: 'width:200px' },
                ],
                items: [],
            },
            input_kala: {
                shift: '',
                time: '',
                blood_pressure: '',
                pulse: '',
                temperature: '',
                fundus: '',
                contraction: '',
                bladder: '',
                out_blood: '',
            },

            form_record: {
                partograph_id: "",
                date_birth: "",
                birthing_place_type_id: 0,
                birthing_place_other: "",
                birthing_place_address: "",
                note: "",
                referral_reason: "",
                referral_place: "",
                referral_companion_type_id: 0,
                problem_type_id: 0,
                newborn_baby_weight: "",
                newborn_baby_height: "",
                newborn_baby_sex_type_id: 0,
                newborn_baby_assessment_type_id: 0,
                newborn_baby_normal_arry: "",
                newborn_baby_asphyxia_arry: "",
                newborn_baby_disabled: "",
                // newborn_baby_hypothermia_type_id: 0,
                // newborn_baby_hypothermia_a: "",
                // newborn_baby_hypothermia_b: "",
                newborn_baby_hypothermia_c: "",
                newborn_baby_breast_milk_type_id: 0,
                newborn_baby_breast_milk_hours: "",
                newborn_baby_breast_milk_not: "",
                newborn_baby_problem_management: "",
                newborn_baby_result: "",

                newborn_baby_type_id: 0,
                newborn_baby_hypothermia_arry: [],
                newborn_baby_asphyxia: '',
            },

            form_kala: {
                birth_record_id: "",
                kala_one_latent_phase: "",
                kala_one_intervention_type_id: 0,
                kala_one_alert_line_type_id: 0,
                kala_one_active_phase: "",
                kala_one_problem_management: "",
                kala_one_result: "",
                kala_two_episiotomy_type_id: 0,
                kala_two_episiotomy: "",
                kala_two_companion_type_id: 0,
                kala_two_fetal_distress_type_id: 0,
                // kala_two_fetal_distress_a: "",
                // kala_two_fetal_distress_b: "",
                kala_two_monitoring_result: "",
                kala_two_shoulder_dystocia_type_id: 0,
                kala_two_shoulder_dystocia: "",
                kala_two_problem_management: "",
                kala_three_initiation_type_id: 0,
                kala_three_initiation: "",
                kala_three_long: '',
                kala_three_oxytocin_type_id: 0,
                kala_three_oxytocin: 0,
                kala_three_oxytocin_reason: "",
                kala_three_oxytocin_second_type_id: 0,
                kala_three_oxytocin_second: "",
                kala_three_umbilical_cord_type_id: 0,
                kala_three_umbilical_cord: "",
                kala_three_uterine_fundus_type_id: 0,
                kala_three_uterine_fundus: "",
                kala_three_placenta_type_id: 0,
                // kala_three_placenta_a: "",
                // kala_three_placenta_b: "",
                kala_three_placenta_not_type_id: 0,
                kala_three_placenta_not: "",
                kala_three_laceration_type_id: 0,
                kala_three_laceration: "",
                kala_three_uterine_atony_type_id: 0,
                kala_three_uterine_atony: "",
                kala_three_amount_blood: '',
                kala_three_problem_management: "",
                kala_three_result: "",
                kala_four_mother_ku: '',
                kala_four_mother_td: '',
                kala_four_pulse: '',
                kala_four_breath: '',
                kala_four_problem_management: "",
                kala_four_result: "",

                kala_two_fetal_distress_arry: [],
                kala_three_placenta_arry: [],
                kala_three_parineum_degree_type_id: 0,
                kala_three_parineum_degree: '',
                kala_three_parineum_act_type_id: 0,
            },
            input_fetal: '',
            input_hipotemi: '',
        }
    },
    watch: {
        "form_record.birthing_place_type_id"(newValue) {
            if (newValue != "6") {
                this.form_record.birthing_place_other = ''
            }
        },
        
        "form_kala.kala_two_episiotomy_type_id"(newValue) {
            if (newValue != "1") {
                this.form_kala.kala_two_episiotomy = ''
            }
        },

        "form_kala.kala_two_shoulder_dystocia_type_id"(newValue) {
            if (newValue != "1") {
                this.form_kala.kala_two_shoulder_dystocia = ''
            }
        },

        "form_kala.kala_two_fetal_distress_type_id"(newValue) {
            if (newValue != "1") {
                this.form_kala.kala_two_monitoring_result = ''
            } else if (newValue != 0) {
                this.form_kala.kala_two_fetal_distress_arry = []
            }
        },

        "form_kala.kala_three_initiation_type_id"(newValue) {
            if (newValue != "1") {
                this.form_kala.kala_three_initiation = ''
            }
        },

        "form_kala.kala_three_oxytocin_type_id"(newValue) {
            if (newValue != "0") {
                this.form_kala.kala_three_oxytocin = ''
            } else {
                this.form_kala.kala_three_oxytocin_reason = ''
            }
        },

        "form_kala.kala_three_oxytocin_second_type_id"(newValue) {
            if (newValue != "0") {
                this.form_kala.kala_three_oxytocin_second = ''
            }
        },

        "form_kala.kala_three_umbilical_cord_type_id"(newValue) {
            if (newValue != "0") {
                this.form_kala.kala_three_umbilical_cord = ''
            }
        },

        "form_kala.kala_three_uterine_fundus_type_id"(newValue) {
            if (newValue != "0") {
                this.form_kala.kala_three_uterine_fundus = ''
            }
        },

        "form_kala.kala_three_placenta_not_type_id"(newValue) {
            if (newValue != "1") {
                this.form_kala.kala_three_placenta_not = ''
            }
        },

        "form_kala.kala_three_laceration_type_id"(newValue) {
            if (newValue != "1") {
                this.form_kala.kala_three_laceration = ''
            }
        },

        "form_kala.kala_three_uterine_atony_type_id"(newValue) {
            if (newValue != "1") {
                this.form_kala.kala_three_uterine_atony = ''
            }
        },

        "form_record.newborn_baby_type_id"(newValue) {
            if (newValue != "1") {
                this.form_record.newborn_baby_asphyxia_arry = []
                this.form_record.newborn_baby_asphyxia = ''
                this.form_record.newborn_baby_disabled = ""
                this.form_record.newborn_baby_hypothermia_arry = []
            } else if (condition) {
                this.form_record.newborn_baby_normal_arry = []
                this.form_record.newborn_baby_disabled = ""
                this.form_record.newborn_baby_hypothermia_arry = []
            } else if (condition) {
                this.form_record.newborn_baby_normal_arry = []
                this.form_record.newborn_baby_asphyxia = ''
                this.form_record.newborn_baby_asphyxia_arry = []
                this.form_record.newborn_baby_hypothermia_arry = []
            } else if (condition) {
                this.form_record.newborn_baby_normal_arry = []
                this.form_record.newborn_baby_asphyxia = ''
                this.form_record.newborn_baby_asphyxia_arry = []
                this.form_record.newborn_baby_disabled = ""
            }
        },
        "form_record.newborn_baby_asphyxia_arry"(newValues) {
            if (newValues && !newValues.includes("g")) {
                this.form_record.newborn_baby_asphyxia = ""
            }
        },
    },
    methods: {
        openModal(){
            this.active.main_modal = !this.active.main_modal
        },
        openSecondModal(){
            this.active.second_modal = !this.active.second_modal
        },
        addFetal() {
            const data = this.input_fetal
            this.form_kala.kala_two_fetal_distress_arry.push(data)
            this.input_fetal = ''
        },

        removeFetal(index) {
            this.form_kala.kala_two_fetal_distress_arry.splice(index, 1)
        },

        addHipotemi() {
            const data = this.input_hipotemi
            this.form_record.newborn_baby_hypothermia_arry.push(data)
            this.input_hipotemi = ''
        },

        removeHipotemi(index) {
            this.form_record.newborn_baby_hypothermia_arry.splice(index, 1)
        },

        addKala() {
            let obj = {
                shift: this.input_kala.shift,
                time: this.input_kala.time,
                blood_pressure: this.input_kala.blood_pressure,
                pulse: this.input_kala.pulse,
                temperature: this.input_kala.temperature,
                fundus: this.input_kala.fundus,
                contraction: this.input_kala.contraction,
                bladder: this.input_kala.bladder,
                out_blood: this.input_kala.out_blood,
            }            
            this.kala.items.push(obj)
            
            this.input_kala.shift = ''
            this.input_kala.time = ''
            this.input_kala.blood_pressure = ''
            this.input_kala.pulse = ''
            this.input_kala.temperature = ''
            this.input_kala.fundus = ''
            this.input_kala.contraction = ''
            this.input_kala.bladder = ''
            this.input_kala.out_blood = ''
        },

        removeKala(index) {
            this.kala.items.splice(index, 1)
        },
    },
}
</script>