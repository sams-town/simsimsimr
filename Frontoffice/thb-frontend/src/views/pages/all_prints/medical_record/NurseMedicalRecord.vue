<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <div id="canvas" ref="canvas" class="mt-16 w-100" style="position: relative;">
                    <div class="row align-items-center justify-content-center">
                        <img src="../../../../@core/assets/images/logo-thb-small.png" width="80px" alt="rs-logo"> <br>
                        <div class="text-regular-12 pl-16">
                            <p class="text-bold-18">RS Taman Harapan Baru</p>
                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                            <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                        </div>
                    </div>
                    <div class="mt-40">
                        <div class="row mx-0">
                            <div class="col-6">
                                <table class="text-regular-14">
                                    <tr>
                                        <td class="first-cell">No. RM</td>
                                        <td class="px-3">:</td>
                                        <td>{{ regData.norm }}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Nama Pasien</td>
                                        <td class="px-3">:</td>
                                        <td>[{{ regData.alias_name }}] {{ regData.name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Tgl. Lahir</td>
                                        <td class="px-3">:</td>
                                        <td>{{ regData.birth_date }}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Jenis Kelamin</td>
                                        <td class="px-3">:</td>
                                        <td>{{ regData.sex_name }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-6">
                                <table class="text-regular-14">
                                    <tr>
                                        <td class="first-cell">Dokter</td>
                                        <td class="px-3">:</td>
                                        <td>{{ regData.doctor_in_charge_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Jenis Kajian</td>
                                        <td class="px-3">:</td>
                                        <td>{{ getTypeNurseName(nurseData.type_nurse_id) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Poliklinik/Ruang Perawatan</td>
                                        <td class="px-3">:</td>
                                        <td>{{ regData.health_facility_item_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Tanggal Pendafaran</td>
                                        <td class="px-3">:</td>
                                        <td>{{ regData.date_register }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <section class="card mt-32">
                        <div class="card-header text-center text-bold-18">PERAWAT</div>
                        <div class="card-body">
                            <section>
                                <ModernLabel label="ANAMNESA" />
                                <table class="w-100 mt-16">
                                    <tbody>
                                        <tr>
                                            <td style="width: 200px;">Anamnesa</td>
                                            <td style="width: 30px;" class="px-3">:</td>
                                            <td>{{ nurseData.anamesa_type_id == '1' ? 'Auto Anamnesa' : 'Allo Anamnesa' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Keluarga</td>
                                            <td class="px-3">:</td>
                                            <td>{{ nurseData.family_name ? nurseData.family_name : '-' }}
                                                <span v-if="nurseData.family_relation_id != '0'">
                                                    <template v-if="nurseData.family_relation_id != '1'">Ayah</template>
                                                    <template v-else-if="nurseData.family_relation_id != '2'">Ibu</template>
                                                    <template v-else-if="nurseData.family_relation_id != '3'">Kakak</template>
                                                    <template v-ele-if="nurseData.family_relation_id != '4'">Adik</template>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-top" >Keluhan Utama</td>
                                            <td class="align-top px-3">:</td>
                                            <td>{{ nurseData.main_complaint ? nurseData.main_complaint : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top">Riwayat Penyakit Sekarang</td>
                                            <td class="align-top px-3">:</td>
                                            <td>
                                                <div v-html="parsedBody(nurseData.history_disease_now ? nurseData.history_disease_now : '-')"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-top pt-2">Riwayat Penyakit Dahulu</td>
                                            <td class="align-top pt-2 px-3">:</td>
                                            <td class="pt-2">
                                                <div v-html="parsedBody(nurseData.history_disease_past ? nurseData.history_disease_past : '-')"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-top pt-2">Riwayat Obat</td>
                                            <td class="align-top pt-2 px-3">:</td>
                                            <td class="pt-2">
                                                <div v-html="parsedBody(nurseData.history_drugs ? nurseData.history_drugs : '-')"></div>
                                            </td>
                                        </tr>
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
                                                                    <td style="width: 200px;" class="align-top">Sistolik</td>
                                                                    <td style="width: 30px;" class="align-top px-1 text-center">:</td>
                                                                    <td>{{ nurseData && nurseData.systolic_blood_pressure ? nurseData.systolic_blood_pressure : '-' }} mmHg</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="align-top">Diastolik</td>
                                                                    <td class="align-top px-1 text-center">:</td>
                                                                    <td>{{ nurseData && nurseData.diastolic_blood_pressure ? nurseData.diastolic_blood_pressure : '-' }} mmHg</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="align-top">Frekuensi Nadi</td>
                                                                    <td class="align-top px-1 text-center">:</td>
                                                                    <td>{{ nurseData && nurseData.pulse_frequency ? nurseData.pulse_frequency : '-' }} x/mnt</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="align-top">Frekuensi Nafas</td>
                                                                    <td class="align-top px-1 text-center">:</td>
                                                                    <td>{{ nurseData && nurseData.breath_frequency ? nurseData.breath_frequency : '-' }} x/mnt</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="align-top">Saturasi Oksigen</td>
                                                                    <td class="align-top px-1 text-center">:</td>
                                                                    <td>{{ nurseData && nurseData.oximetry_frequency ? nurseData.oximetry_frequency : '-'}} %</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="align-top">Suhu</td>
                                                                    <td class="align-top px-1 text-center">:</td>
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
                                                                <tr>
                                                                    <td class="align-top">GCS Pupil</td>
                                                                    <td class="align-top px-1 text-center">:</td>
                                                                    <td>{{ getGcsPupilName(nurseData?.pupil_score) }}</td>
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
                        </div>
                    </section>

                    <template v-if="dummy_condition == true">
                        <!-- neonatus print -->
                        <section class="card mt-32" v-if="nurseData.type_nurse_id == 12">
                            <div class="card-header text-center text-bold-18">KAJIAN AWAL RAWAT INAP NEONATUS</div>
                            <div class="card-body">
                                <section>
                                    <ModernLabel label="RIWAYAT KESEHATAN BAYI DAN IBU" />
                                    <table class="w-100 mt-16">
                                        <tbody>
                                            <tr>
                                                <td style="width: 300px;">Hasil Laboratorium Ibu</td>
                                                <td style="width: 20px;" class="px-3">:</td>
                                                <td>{{ NeonatusGetHasilLaboratoriumIbu(neonatusData.neonatus_riwayat_kesehatan_bayi_ibu.hasil_laboratorium_ibu)}}</td>
                                            </tr>
                                            <tr>
                                                <td>Golongan Darah Ibu</td>
                                                <td style="width: 20px;" class="px-3">:</td>
                                                <td>{{ NeonatusGolonganDarahIbu(neonatusData.neonatus_riwayat_kesehatan_bayi_ibu.golongan_darah_ibu) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="align-top" >Riwayat Persalinan</td>
                                                <td style="width: 20px;" class="px-3">:</td>
                                                <td>{{ NeonatusRiwayatPersalinan(neonatusData.neonatus_riwayat_kesehatan_bayi_ibu.riwayat_persalinan) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="align-top">Riwayat Kelahiran</td>
                                                <td style="width: 20px;" class="px-3">:</td>
                                                <td> {{ NeonatusRiwayatKelahiran(neonatusData.neonatus_riwayat_kesehatan_bayi_ibu.riwayat_kelahiran) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="align-top pt-2">Jenis Kelamin Bayi</td>
                                                <td style="width: 20px;" class="px-3">:</td>
                                                <td> {{ NeonatusJenisKelaminBayi(neonatusData.neonatus_riwayat_kesehatan_bayi_ibu.jenis_kelamin_bayi) }} </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top pt-2">Pemberian Obat Setelah Lahir</td>
                                                <td style="width: 20px;" class="px-3">:</td>
                                                <td> {{ NeonatusPemberianObatSetelahLahir(neonatusData.neonatus_riwayat_kesehatan_bayi_ibu.pemberian_obat_setelah_lahir) }} </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top pt-2">Riwayat Penyakit Ibu Setelah Hamil</td>
                                                <td class="align-top pt-2 px-3">:</td>
                                                <td class="pt-2"> {{ neonatusData.neonatus_riwayat_kesehatan_bayi_ibu.riwayat_penyakit_ibu_setelah_hamil }} </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top pt-2">Diagnosa Partus</td>
                                                <td class="align-top pt-2 px-3">:</td>
                                                <td class="pt-2"> {{ neonatusData.neonatus_riwayat_kesehatan_bayi_ibu.diagnosa_partus }} </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top pt-2">Riwayat Kehamilan</td>
                                                <td class="align-top pt-2 px-3">:</td>
                                                <td class="pt-2"> {{ neonatusData.neonatus_riwayat_kesehatan_bayi_ibu.riwayat_kehamilan }} </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </section>
                                <div class="mt-20">
                                    <ModernLabel label="KEBUTUHAN EDUKASI ORANG TUA" />
                                    <table class="w-100 mt-16">
                                        <tbody>
                                            <tr>
                                                <td style="width:450px;">Bicara</td>
                                                <td style="width: 20px;" class="px-3">:</td>
                                                <td> {{ NeonatusKebutuhanEdukasiOrangtua(neonatusData.neonatus_kebutuhan_edukasi_orang_tua.bicara) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Bahasa Sehari-Hari</td>
                                                <td class="px-3">:</td>
                                                <td> {{ NeonatusBahasaSehariHari(neonatusData.neonatus_kebutuhan_edukasi_orang_tua.bahasa_sehari_hari) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Perlu Penerjemah</td>
                                                <td class="px-3">:</td>
                                                <td> {{ NeonatusPerluPenerjemah(neonatusData.neonatus_kebutuhan_edukasi_orang_tua.perlu_penerjemah) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Tingkat Pendidikan</td>
                                                <td class="px-3">:</td>
                                                <td> {{ NeonatusTingkatPendidikan(neonatusData.neonatus_kebutuhan_edukasi_orang_tua.tingkat_pendidikan) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Keluarga Menginginkan Informasi Tentang Proses Penyakit</td>
                                                <td class="px-3">:</td>
                                                <td v-html="parsedBody(neonatusData.neonatus_kebutuhan_edukasi_orang_tua.keluarga_menginginkan_informasi_tentang_proses_penyakit)"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-20">
                                    <ModernLabel label="SPIRITUAL" />
                                    <table class="w-100 mt-16">
                                        <tbody>
                                            <tr>
                                                <td style="width: 450px;">Agama</td>
                                                <td style="width: 20px;" class="px-3">:</td>
                                                <td>{{ NeonatusAgama(neonatusData.neonatus_spiritual.agama) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Permintaan khusus terkait dengan adat/keyakinan/kepercayaan</td>
                                                <td class="px-3">:</td>
                                                <td v-html="parsedBody(neonatusData.neonatus_spiritual.permintaan_khusus_terkait_dengan_adat)"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-20">
                                    <ModernLabel label="SOSIAL BUDAYA" />
                                    <table class="w-100 mt-16">
                                        <tbody>
                                            <tr>
                                                <td style="width: 200px;">Suku Bangsa</td>
                                                <td style="width: 20px;" class="px-3">:</td>
                                                <td>{{ neonatusData.neonatus_sosial_budaya.suku_bangsa }}</td>
                                            </tr>
                                            <tr>
                                                <td>Warga Negara</td>
                                                <td class="px-3">:</td>
                                                <td>{{ neonatusData.neonatus_sosial_budaya.warga_negara }}</td>
                                            </tr>
                                            <tr>
                                                <td>Tempat Tinggal</td>
                                                <td class="px-3">:</td>
                                                <td>{{ NeonatusTempatTinggalBayi(neonatusData.neonatus_sosial_budaya.tempat_tinggal) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Yang Merawat Bayi</td>
                                                <td class="px-3">:</td>
                                                <td>{{ NeonatusYangMerawatBayi(neonatusData.neonatus_sosial_budaya.yang_merawat_bayi) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Orang Tua</td>
                                                <td class="px-3">:</td>
                                                <td>{{ neonatusData.neonatus_sosial_budaya.orang_tua }}</td>
                                            </tr>
                                            <tr>
                                                <td>Berkunjung</td>
                                                <td class="px-3">:</td>
                                                <td>{{ NeonatusBerkunjungMenyentuhMenggendongKotakMata(neonatusData.neonatus_sosial_budaya.berkunjung) }}</td>
                                            </tr><tr>
                                                <td>Menyentuh</td>
                                                <td class="px-3">:</td>
                                                <td>{{ NeonatusBerkunjungMenyentuhMenggendongKotakMata(neonatusData.neonatus_sosial_budaya.menyentuh) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Menggendong</td>
                                                <td class="px-3">:</td>
                                                <td>{{ NeonatusBerkunjungMenyentuhMenggendongKotakMata(neonatusData.neonatus_sosial_budaya.menggendong) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Kontak Mata</td>
                                                <td class="px-3">:</td>
                                                <td>{{ NeonatusBerkunjungMenyentuhMenggendongKotakMata(neonatusData.neonatus_sosial_budaya.kontak_mata) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Berbicara</td>
                                                <td class="px-3">:</td>
                                                <td>{{ NeonatusBerkunjungMenyentuhMenggendongKotakMata(neonatusData.neonatus_sosial_budaya.berbicara) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Lainnya</td>
                                                <td class="px-3">:</td>
                                                <td>{{ neonatusData.neonatus_sosial_budaya.lainnya }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-20">
                                    <ModernLabel label="PENANGGUNG JAWAB" />
                                    <table class="w-100 mt-16">
                                        <tbody>
                                            <tr>
                                                <td style="width: 200px;">Wali</td>
                                                <td style="width: 20px;" class="px-3">:</td>
                                                <td>{{ neonatusData.neonatus_penanggung_jawab.wali }}</td>
                                            </tr>
                                            <tr>
                                                <td>Nama</td>
                                                <td class="px-3">:</td>
                                                <td>{{ neonatusData.neonatus_penanggung_jawab.nama }}</td>
                                            </tr>
                                            <tr>
                                                <td>Usia</td>
                                                <td class="px-3">:</td>
                                                <td>{{ neonatusData.neonatus_penanggung_jawab.usia }}</td>
                                            </tr>
                                            <tr>
                                                <td>Perkawinan Ke</td>
                                                <td class="px-3">:</td>
                                                <td>{{ neonatusData.neonatus_penanggung_jawab.perkawinan_ke }}</td>
                                            </tr>
                                            <tr>
                                                <td>Pekerjaan</td>
                                                <td class="px-3">:</td>
                                                <td>{{ neonatusData.neonatus_penanggung_jawab.pekerjaan }}</td>
                                            </tr>
                                            <tr>
                                                <td>Pendidikan Terakhir</td>
                                                <td class="px-3">:</td>
                                                <td>{{ neonatusData.neonatus_penanggung_jawab.pendidikan_terakhir }}</td>
                                            </tr><tr>
                                                <td>Alamat</td>
                                                <td class="px-3">:</td>
                                                <td>{{ neonatusData.neonatus_penanggung_jawab.alamat }}</td>
                                            </tr>
                                            <tr>
                                                <td>Telepon</td>
                                                <td class="px-3">:</td>
                                                <td>{{ neonatusData.neonatus_penanggung_jawab.telepon }}</td>
                                            </tr>
                                            <tr>
                                                <td>Penghasilan Perbulan</td>
                                                <td class="px-3">:</td>
                                                <td>{{ neonatusData.neonatus_penanggung_jawab.penghasilan_perbulan }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-20">
                                    <ModernLabel label="PEMERIKSAAN FISIK" />
                                    <table class="w-100 mt-16">
                                        <tbody>
                                            <tr>
                                                <td colspan="3">
                                                    <div class="row pt-20">
                                                        <div class="col-6">
                                                            <h6 class="text-uppercase">Paru</h6>
                                                            <table class="w-100">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 200px;">Pergerakan Paru</td>
                                                                        <td style="width: 20px;" class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_paru.pergerakan_paru }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Frekuensi Pernapasan Paru</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_paru.frekuensi_pernapasan_paru }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Retraksi</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_paru.retraksi }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Nch</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_paru.nch }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Wheezing Paru Kanan</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_paru.wheezing_paru_kanan }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Ronkhi Paru Kiri</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_paru.ronkhi_paru_kiri }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Grunting</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_paru.grunting }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Dyspnoe</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_paru.dyspnoe }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Tachynoe</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_paru.tachynoe }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Gargling Napas Tambahan</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_paru.gargling_napas_tambahan }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Lainnya Paru Kanan</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_paru.lainnya_paru_kanan }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Wheezing Paru Kiri</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_paru.wheezing_paru_kiri }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Snooring Napas Tambahan</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_paru.snooring_napas_tambahan }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Stridor Napas Tambahan</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_paru.stridor_napas_tambahan }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Peruksi Paru-Paru</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_paru.peruksi_paru_paru }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Ronkhi Paru Kanan</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_paru.ronkhi_paru_kanan }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Perkusi Paru Kiri</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_paru.perkusi_paru_kiri }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Lainnya Paru Kiri</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_paru.lainnya_paru_kiri }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <h6 class="text-uppercase mt-4">Kardiologi</h6>
                                                            <table class="w-100">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 200px;">Kelainan Jantung</td>
                                                                        <td style="width: 20px;" class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_kardiologi.kelainan_jantung }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Denyut Jantung</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_kardiologi.denyut_jantung }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Tipe Denyut Jantung</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_kardiologi.type_denyut_jantung }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Murmur</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_kardiologi.murmur }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Keterangan Murmur</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_kardiologi.keterangan_murmur }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Gallop</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_kardiologi.gallop }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Keterangan Gallop</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_kardiologi.keterangan_gallop }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Sianosis</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_kardiologi.sianosis }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Pucat</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_kardiologi.pucat }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Crt</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_kardiologi.crt }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Akral</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_kardiologi.akral }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <h6 class="text-uppercase mt-4">Muskuloskeletal</h6>
                                                            <table class="w-100">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 200px;">Tonus</td>
                                                                        <td style="width: 20px;" class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_muskuloskeletal.tonus }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Gerakan Bayi</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_muskuloskeletal.gerakan_bayi }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Kelainan Tulang</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_muskuloskeletal.kelainan_tulang }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <h6 class="text-uppercase mt-4">Gastrointesnital</h6>
                                                            <table class="w-100">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 200px;">Stomatitis</td>
                                                                        <td style="width: 20px;" class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_gastrointesnital.stomatitis }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Labio Palatochiziz</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_gastrointesnital.labio_palatochiziz }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Mukosa Kering</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_gastrointesnital.mukosa_kering }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Pendarahan Gusi</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_gastrointesnital.pendarahan_gusi }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Muntah</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_gastrointesnital.muntah }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Frekuensi Muntah</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_gastrointesnital.frekuensi_muntah }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Residu</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_gastrointesnital.residu }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Asites</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_gastrointesnital.asites }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Bising Usus</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_gastrointesnital.bising_usus }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Nyeri Tekan</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_gastrointesnital.nyeri_tekan }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Limpa</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_gastrointesnital.limpa }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Hepar</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_gastrointesnital.hepar }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Lokasi Nyeri Tekan</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_gastrointesnital.lokasi_nyeri_tekan }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="col-6">
                                                            <h6 class="text-uppercase">Down Score</h6>
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 200px;">Frekuensi Napas</td>
                                                                        <td style="width: 20px;" class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_down_score.frekuensi_napas }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Retraksi</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_down_score.retraksi }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Sianosis</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_down_score.sianosis }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Air Entry</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_down_score.air_entry }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Merintih</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_down_score.merintih }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <h6 class="text-uppercase mt-4">Eliminasi</h6>
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 200px;">Anus</td>
                                                                        <td style="width: 20px;" class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_eliminasi.anus }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Defaksi Anus</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_eliminasi.defaksi_anus }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Defaksi Stoma</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_eliminasi.defaksi_stoma }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Frekuensi Defaksi</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_eliminasi.frekuensi_defaksi }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Konsistensi Defaksi</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_eliminasi.konsistensi_defaksi }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Karakteristik Fases</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_eliminasi.karakteristik_fases }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Urin</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_eliminasi.urin }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Diuresis</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_eliminasi.diuresis }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <h6 class="text-uppercase mt-4">Genitalia</h6>
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 200px;">Jenis Kelamin</td>
                                                                        <td style="width: 20px;" class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_genitalia.jenis_kelamin }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Kelainan</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_genitalia.kelainan }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Penis Laki-Laki</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_genitalia.penis_laki_laki }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Labio Perempuan</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_genitalia.labio_perempuan }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Testis Laki-Laki</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_genitalia.testis_laki_laki }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Lainnya</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_genitalia.lainnya }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <h6 class="text-uppercase mt-4">Apgar</h6>
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 200px;">Menit ke</td>
                                                                        <td style="width: 20px;" class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_apgar_score.menit_ke }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Denyut Jantung</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_apgar_score.denyut_jantung }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Usaha Napas</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_apgar_score.usaha_napas }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Tonus Otot</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_apgar_score.tonus_otot }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Refleks</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_apgar_score.refleks }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <h6 class="text-uppercase mt-4">Skala Nyeri</h6>
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 200px;">Nama</td>
                                                                        <td style="width: 20px;" class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_skala_nyeri.nama }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Skor</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_skala_nyeri.skor }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <h6 class="text-uppercase mt-4">Trauma Kulit</h6>
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 200px;">Usia Gestasi</td>
                                                                        <td style="width: 20px;" class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_trauma_kulit.usia_gestasi }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Status Mental</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_trauma_kulit.status_mental }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Mobilisasi</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_trauma_kulit.mobilisasi }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Aktivitas</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_trauma_kulit.aktivitas }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Nutrisi</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_trauma_kulit.nutrisi }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Kelembaban</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_trauma_kulit.kelembaban }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <h6 class="text-uppercase mt-4">Reaksi Alergi</h6>
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 200px;">Makanan</td>
                                                                        <td style="width: 20px;" class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_alergi_reaksi.makanan }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Makanan Reaksi</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_alergi_reaksi.makanan_reaksi }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Obat</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_alergi_reaksi.obat }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Obat Reaksi</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_alergi_reaksi.obat_reaksi }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Lainnya</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_alergi_reaksi.lainnya }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Lainnya Reaksi</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_alergi_reaksi.lainnya_reaksi }}</td>
                                                                    </tr><tr>
                                                                        <td>Klip Tanda Alergi</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_alergi_reaksi.klip_tanda_alergi }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Beritahu Dokter</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_alergi_reaksi.beritahu_dokter }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Beritahu Farmasi</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_alergi_reaksi.beritahu_farmasi }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Beritahu Dietisien</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ neonatusData.neonatus_alergi_reaksi.beritahu_dietisien }}</td>
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
                        </section>
                        <!-- child print -->
                        <section class="card mt-32" v-if="nurseData.type_nurse_id == 6">
                            <div class="card-header text-center text-bold-18">KAJIAN AWAL RAWAT INAP ANAK</div>
                            <div class="card-body">
                                <section>
                                    <ModernLabel label="RIWAYAT KESEHATAN DAHULU" />
                                    <table class="w-100 mt-16">
                                        <tbody>
                                            <tr>
                                                <td style="width: 150px;">Jenis Persalinan</td>
                                                <td style="width: 30px;" class="px-3">:</td>
                                                <td>{{ ChildJenisPersalinan(childData.child_anak_riwayat_kesehatan_dahulu.jenis_persalinan) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Berat Badan Lahir</td>
                                                <td style="width: 30px;" class="px-3">:</td>
                                                <td>{{ childData.child_anak_riwayat_kesehatan_dahulu.berat_badan_lahir }}</td>
                                            </tr>
                                            <tr>
                                                <td class="align-top" >Tinggi Badan Lahir</td>
                                                <td style="width: 30px;" class="px-3">:</td>
                                                <td>{{ childData.child_anak_riwayat_kesehatan_dahulu.tinggi_badan_lahir }}</td>
                                            </tr>
                                            <tr>
                                                <td class="align-top">Status Gizi</td>
                                                <td style="width: 30px;" class="px-3">:</td>
                                                <td> {{ ChildStatusGizi(childData.child_anak_riwayat_kesehatan_dahulu.status_gizi) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="align-top pt-2">Keterangan Lahir</td>
                                                <td style="width: 30px;" class="px-3">:</td>
                                                <td> {{ childData.child_anak_riwayat_kesehatan_dahulu.keterangan_lahir }} </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top pt-2">Tumbuh Kembang</td>
                                                <td style="width: 30px;" class="px-3">:</td>
                                                <td> {{ childData.child_anak_riwayat_kesehatan_dahulu.tumbuh_kembang }} </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top pt-2">Tinggi Badan</td>
                                                <td class="align-top pt-2 px-3">:</td>
                                                <td class="pt-2"> {{ childData.child_anak_riwayat_kesehatan_dahulu.tinggi_badan }} </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top pt-2">Berat Badan</td>
                                                <td class="align-top pt-2 px-3">:</td>
                                                <td class="pt-2"> {{ childData.child_anak_riwayat_kesehatan_dahulu.berat_badan }} </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top pt-2">Lingkar Kepala</td>
                                                <td class="align-top pt-2 px-3">:</td>
                                                <td class="pt-2"> {{ childData.child_anak_riwayat_kesehatan_dahulu.lingkar_kepala }} </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top pt-2">Lila</td>
                                                <td class="align-top pt-2 px-3">:</td>
                                                <td class="pt-2"> {{ childData.child_anak_riwayat_kesehatan_dahulu.lila }} </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top pt-2">IMT</td>
                                                <td class="align-top pt-2 px-3">:</td>
                                                <td class="pt-2"> {{ childData.child_anak_riwayat_kesehatan_dahulu.imt }} </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top pt-2">Perawakan</td>
                                                <td class="align-top pt-2 px-3">:</td>
                                                <td class="pt-2"> {{ childData.child_anak_riwayat_kesehatan_dahulu.perawakan }} </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </section>
                                <div class="mt-20">
                                    <ModernLabel label="RIWAYAT TUMBUH KEMBANG" />
                                    <table class="w-100 mt-16">
                                        <tbody>
                                            <tr>
                                                <td style="width:300px;">BB Anak Saat Lahir</td>
                                                <td style="width: 20px;" class="px-3">:</td>
                                                <td> {{ childData.child_riwayat_tumbuh_kembang.bb_anak_saat_lahir }}</td>
                                            </tr>
                                            <tr>
                                                <td>Susu Formula,dimulai umur</td>
                                                <td class="px-3">:</td>
                                                <td> {{ childData.child_riwayat_tumbuh_kembang.susu_formula }}</td>
                                            </tr>
                                            <tr>
                                                <td>Tengkurap</td>
                                                <td class="px-3">:</td>
                                                <td> {{ childData.child_riwayat_tumbuh_kembang.tengkurap }}</td>
                                            </tr>
                                            <tr>
                                                <td>Berdiri</td>
                                                <td class="px-3">:</td>
                                                <td> {{ childData.child_riwayat_tumbuh_kembang.berdiri }}</td>
                                            </tr>
                                            <tr>
                                                <td>PB Anak Saat Lahir</td>
                                                <td class="px-3">:</td>
                                                <td>{{ childData.child_riwayat_tumbuh_kembang.pb_anak_saat_lahir }}</td>
                                            </tr>
                                            <tr>
                                                <td>Makanan Padat,Dimulai Umur</td>
                                                <td class="px-3">:</td>
                                                <td>{{ childData.child_riwayat_tumbuh_kembang.makanan_padat }}</td>
                                            </tr>
                                            <tr>
                                                <td>Duduk</td>
                                                <td class="px-3">:</td>
                                                <td>{{ childData.child_riwayat_tumbuh_kembang.duduk }}</td>
                                            </tr>
                                            <tr>
                                                <td>Berjalan</td>
                                                <td class="px-3">:</td>
                                                <td>{{ childData.child_riwayat_tumbuh_kembang.berjalan }}</td>
                                            </tr>
                                            <tr>
                                                <td>Asi,Sampai Umur</td>
                                                <td class="px-3">:</td>
                                                <td>{{ childData.child_riwayat_tumbuh_kembang.asi_sampai_umur }}</td>
                                            </tr>
                                            <tr>
                                                <td>Makanan Tambahan,Dimulai Umur</td>
                                                <td class="px-3">:</td>
                                                <td>{{ childData.child_riwayat_tumbuh_kembang.makanan_tambahan }}</td>
                                            </tr>
                                            <tr>
                                                <td>Merangkak</td>
                                                <td class="px-3">:</td>
                                                <td>{{ childData.child_riwayat_tumbuh_kembang.merangkak }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-20">
                                    <ModernLabel label="PEMERIKSAAN FISIK" />
                                    <table class="w-100 mt-16">
                                        <tbody>
                                            <tr>
                                                <td colspan="3">
                                                    <div class="row pt-20">
                                                        <div class="col-6">
                                                            <h6 class="text-uppercase">TTV</h6>
                                                            <table class="w-100">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 200px;">Tekanan Darah</td>
                                                                        <td style="width: 20px;" class="px-3">:</td>
                                                                        <td>{{ childData.child_pemeriksaan_fisik_ttv.tekanan_darah }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Kesadaran</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ childData.child_pemeriksaan_fisik_ttv.kesadaran }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Suhu</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ childData.child_pemeriksaan_fisik_ttv.suhu }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Nadi</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ childData.child_pemeriksaan_fisik_ttv.nadi }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <h6 class="text-uppercase mt-4">Berat badan</h6>
                                                            <table class="w-100">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 200px;">Berat Badan</td>
                                                                        <td style="width: 20px;" class="px-3">:</td>
                                                                        <td>{{ childData.child_pemeriksaan_fisik_berat_badan.berat_badan }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Tinggi Badan</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ childData.child_pemeriksaan_fisik_berat_badan.tinggi_badan }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>LLA</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ childData.child_pemeriksaan_fisik_berat_badan.lla }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <h6 class="text-uppercase mt-4">Neorologi</h6>
                                                            <table class="w-100">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 350px;">Penglihatan</td>
                                                                        <td style="width: 20px;" class="px-3">:</td>
                                                                        <td>{{ ChildPemeriksaanFisik(childData.child_pemeriksaan_fisik_neorologi.penglihatan,'neorologi','penglihatan') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Bicara</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPemeriksaanFisik(childData.child_pemeriksaan_fisik_neorologi.bicara,'neorologi','bicara') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Kekuatan Otot</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPemeriksaanFisik(childData.child_pemeriksaan_fisik_neorologi.kekuatan_otot,'neorologi','kekuatan_otot') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Menggunakan Alat Bantu Penglihatan</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPemeriksaanFisik(childData.child_pemeriksaan_fisik_neorologi.menggunakan_alat_bantu_penglihatan,'neorologi','menggunakan_alat_bantu_penglihatan') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Sensorik</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPemeriksaanFisik(childData.child_pemeriksaan_fisik_neorologi.sensorik,'neorologi','sensorik') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Menggunakan Alat Bantu Pendengaran</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPemeriksaanFisik(childData.child_pemeriksaan_fisik_neorologi.menggunakan_alat_bantu_pendengaran,'neorologi','menggunakan_alat_bantu_pendengaran') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Pendengaran</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPemeriksaanFisik(childData.child_pemeriksaan_fisik_neorologi.pendengaran,'neorologi','pendengaran') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Motorik</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPemeriksaanFisik(childData.child_pemeriksaan_fisik_neorologi.motorik,'neorologi','motorik') }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <h6 class="text-uppercase mt-4">Integument</h6>
                                                            <table class="w-100">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 200px;">Warna Kulit</td>
                                                                        <td style="width: 20px;" class="px-3">:</td>
                                                                        <td>{{ ChildPemeriksaanFisik(childData.child_pemeriksaan_fisik_integument.warna_kulit,'integument','warna_kulit') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Kriteria resiko dekubitus</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPemeriksaanFisik(childData.child_pemeriksaan_fisik_integument.kriteria_resiko_dekubitus,'integument','kriteria_resiko_dekubitus') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Tugor</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPemeriksaanFisik(childData.child_pemeriksaan_fisik_integument.tugor,'integument','tugor') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Kulit</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPemeriksaanFisik(childData.child_pemeriksaan_fisik_integument.kulit,'integument','kulit') }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="col-6">
                                                            <h6 class="text-uppercase">Kardio Vaskuler</h6>
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 200px;">Warna Kulit</td>
                                                                        <td style="width: 20px;" class="px-3">:</td>
                                                                        <td>{{ ChildPemeriksaanFisik(childData.child_pemeriksaan_fisik_kardiovaskuler.warna_kulit,'kardiovaskuler','warna_kulit') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Nyeri Dada</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPemeriksaanFisik(childData.child_pemeriksaan_fisik_kardiovaskuler.nyeri_dada,'kardiovaskuler','nyeri_dada') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Denyut Nadi</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPemeriksaanFisik(childData.child_pemeriksaan_fisik_kardiovaskuler.denyut_nadi,'kardiovaskuler','denyut_nadi') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Pulasi</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPemeriksaanFisik(childData.child_pemeriksaan_fisik_kardiovaskuler.pulasi,'kardiovaskuler','pulasi') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Sirkulasi</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPemeriksaanFisik(childData.child_pemeriksaan_fisik_kardiovaskuler.sirkulasi,'kardiovaskuler','sirkulasi') }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <h6 class="text-uppercase mt-12">Respirasi</h6>
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 200px;">Pola Nafas</td>
                                                                        <td style="width: 20px;" class="px-3">:</td>
                                                                        <td>{{ ChildPemeriksaanFisik(childData.child_pemeriksaan_fisik_respirasi.pola_nafas,'respirasi','pola_nafas') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Volume Pernafasan</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPemeriksaanFisik(childData.child_pemeriksaan_fisik_respirasi.volume_pernafasan,'respirasi','volume_pernafasan') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Irama Nafas</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPemeriksaanFisik(childData.child_pemeriksaan_fisik_respirasi.irama_nafas,'respirasi','irama_nafas') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Kesulitan Bernafas</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPemeriksaanFisik(childData.child_pemeriksaan_fisik_respirasi.kesulitan_bernafas,'respirasi','kesulitan_bernafas') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Batuk</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPemeriksaanFisik(childData.child_pemeriksaan_fisik_respirasi.batuk,'respirasi','batuk') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Jenis Pernafasan</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPemeriksaanFisik(childData.child_pemeriksaan_fisik_respirasi.jenis_pernafasan,'respirasi','jenis_pernafasan') }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <h6 class="text-uppercase mt-12">Gastrointestinal</h6>
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 200px;">Mulut</td>
                                                                        <td style="width: 20px;" class="px-3">:</td>
                                                                        <td>{{ ChildPemeriksaanFisik(childData.child_pemeriksaan_fisik_gastrointestinal.mulut,'gastrointestinal','mulut') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Tenggorokan</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPemeriksaanFisik(childData.child_pemeriksaan_fisik_gastrointestinal.tenggorokan,'gastrointestinal','tenggorokan') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Gigi</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPemeriksaanFisik(childData.child_pemeriksaan_fisik_gastrointestinal.gigi,'gastrointestinal','gigi') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Abdomen</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPemeriksaanFisik(childData.child_pemeriksaan_fisik_gastrointestinal.abdomen,'gastrointestinal','abdomen') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Lidah</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPemeriksaanFisik(childData.child_pemeriksaan_fisik_gastrointestinal.lidah,'gastrointestinal','lidah') }}</td>
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
                                    <ModernLabel label="PENILAIAN PERSISTEM" />
                                    <table class="w-100 mt-16">
                                        <tbody>
                                            <tr>
                                                <td colspan="3">
                                                    <div class="row pt-20">
                                                        <div class="col-6">
                                                            <h6 class="text-uppercase">Penilaian Umum</h6>
                                                            <table class="w-100">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 300px;">Apakah ada nyeri</td>
                                                                        <td style="width: 20px;" class="px-3">:</td>
                                                                        <td>{{ ChildPenilaianPersistem(childData.child_penilaian_persistem_umum.apakah_ada_nyeri,'penilaian_umum','apakah_ada_nyeri') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Riwayat Merokok</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPenilaianPersistem(childData.child_penilaian_persistem_umum.riwayat_merokok,'penilaian_umum','riwayat_merokok') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Riwayat Minuman Keras</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPenilaianPersistem(childData.child_penilaian_persistem_umum.riwayat_minuman_keras,'penilaian_umum','riwayat_minuman_keras') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Pola Istirahat/Tidur</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPenilaianPersistem(childData.child_penilaian_persistem_umum.pola_istirahat,'penilaian_umum','pola_istirahat') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Riwayat Penggunaan Obat Penenang</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPenilaianPersistem(childData.child_penilaian_persistem_umum.riwayat_penggunaan_obat_penenang,'penilaian_umum','riwayat_penggunaan_obat_penenang') }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <h6 class="text-uppercase mt-4">Asesmen Fungsi</h6>
                                                            <table class="w-100">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 350px;">Kemampuan Melakukan Aktivitas Sehari-Hari</td>
                                                                        <td style="width: 20px;" class="px-3">:</td>
                                                                        <td>{{ ChildPenilaianPersistem(childData.child_penilaian_persistem_asesmen_fungsi.kemampuan_melakukan_aktivitas_sehari,'asesmen_fungsi','kemampuan_melakukan_aktivitas') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Aktivitas</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPenilaianPersistem(childData.child_penilaian_persistem_asesmen_fungsi.aktivitas,'asesmen_fungsi','aktivitas') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Ekstremitas Atas</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPenilaianPersistem(childData.child_penilaian_persistem_asesmen_fungsi.ekstremitas_atas,'asesmen_fungsi','ekstremitas_atas') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Kemampuan Menggenggam</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPenilaianPersistem(childData.child_penilaian_persistem_asesmen_fungsi.kemampuan_menggenggam,'asesmen_fungsi','kemampuan_menggenggam') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Alat Ambulasi</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPenilaianPersistem(childData.child_penilaian_persistem_asesmen_fungsi.alat_ambulasi,'asesmen_fungsi','alat_ambulasi') }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <h6 class="text-uppercase mt-4">Keadaan Mental Dan Psikologis</h6>
                                                            <table class="w-100">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 350px;">Status Mental</td>
                                                                        <td style="width: 20px;" class="px-3">:</td>
                                                                        <td>{{ ChildPenilaianPersistem(childData.child_penilaian_persistem_asesmenKeadaan_mental_psikologis.status_mental,'keadaan_mental_psikologis','status_mental') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Status Psikologi</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPenilaianPersistem(childData.child_penilaian_persistem_asesmenKeadaan_mental_psikologis.status_psikologi,'keadaan_mental_psikologis','status_psikologi') }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="col-6">
                                                            <h6 class="text-uppercase">Eliminasi</h6>
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 200px;">Bab</td>
                                                                        <td style="width: 20px;" class="px-3">:</td>
                                                                        <td>{{ ChildPenilaianPersistem(childData.child_penilaian_persistem_eliminasi.bab,'eliminasi','bab') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Bak</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ ChildPenilaianPersistem(childData.child_penilaian_persistem_eliminasi.bak,'eliminasi','bak') }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <h6 class="text-uppercase mt-12">Seksual</h6>
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 200px;">Wanita,haid</td>
                                                                        <td style="width: 20px;" class="px-3">:</td>
                                                                        <td>{{ ChildPenilaianPersistem(childData.child_penilaian_persistem_seksual.wanita_haid,'seksual','wanita_haid') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Laki-laki</td>
                                                                        <td class="px-3">:</td>
                                                                        <td>{{ childData.child_penilaian_persistem_seksual.laki_laki }}</td>
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
                        </section>
                    </template>

                    <section v-if="nurseData.type_nurse_id == 4" class="card mt-32">
                        <div class="card-header text-center font-weight-bold">KAJIAN AWAL IGD</div>
                        <div class="card-body">
                            <div class="">
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
                                            <td>{{ emergencyData && emergencyData.any_surgery_date ? localDate(emergencyData.any_surgery_date) : '-' }}</td>
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
                                    <div class="col-12">
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
                                    <div class="col-12 mt-24">
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
                                                        <div v-html="parsedBody(emergencyData.nursing_diagnoses ? emergencyData.nursing_diagnoses : '-')"></div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section v-if="nurseData.type_nurse_id == 7" class="card mt-32">
                        <div class="card-header text-center font-weight-bold">KAJIAN AWAL RAWAT INAP DEWASA</div>
                        <div class="card-body">
                            <section>
                                <ModernLabel label="Discharge Planning" />
                                <table class="w-100 mt-16">
                                    <tbody>
                                        <tr>
                                            <td class="align-top" style="width: 220px;">Perkiraan Lama Perawatan</td>
                                            <td class="align-top px-3" style="width: 30px;">:</td>
                                            <td class="align-top">{{ inpAdultData?.discharge_treatment_duration || '-' }} hari</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top">Perawatan lanjutan yang dibutuhkan di rumah</td>
                                            <td class="align-top px-3">:</td>
                                            <td class="align-top">
                                                <template v-if="inpAdultData && inpAdultData.discharge_further_care_arry">
                                                    <li v-if="inpAdultData.discharge_further_care_arry.includes('a')">Pemberian obat</li>
                                                    <li v-if="inpAdultData.discharge_further_care_arry.includes('b')">Penggunaan teknologi / alat kesehatan</li>
                                                    <li v-if="inpAdultData.discharge_further_care_arry.includes('c')">Tata laksana diet</li>
                                                    <li v-if="inpAdultData.discharge_further_care_arry.includes('d')">Tata laksana nyeri</li>
                                                    <li v-if="inpAdultData.discharge_further_care_arry.includes('e')">Teknik rehabilitasi</li>
                                                    <li v-if="inpAdultData.discharge_further_care_arry.includes('f')">Perawatan luka</li>
                                                </template>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </section>
                            <section class="mt-20">
                                <ModernLabel label="RENCANA PERAWATAN INTERDISIPLIN / REFFERAL" />
                                <table class="w-100 mt-16">
                                    <tbody>
                                        <tr>
                                            <td class="align-top" style="width: 220px;">Diet dan Nutrisi</td>
                                            <td class="align-top px-3" style="width: 30px;">:</td>
                                            <td class="align-top">{{ inpAdultData.care_plan_diet_nutrition_type_id == 1 ? `Ya, ${inpAdultData.care_plan_diet_nutrition}` : 'Tidak' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top">Rehabilitasi Medik</td>
                                            <td class="align-top px-3">:</td>
                                            <td class="align-top">{{ inpAdultData.care_plan_rehabilitation_type_id == 1 ? `Ya, ${inpAdultData.care_plan_rehabilitation}` : 'Tidak' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top">Wound Care</td>
                                            <td class="align-top px-3">:</td>
                                            <td class="align-top">{{ inpAdultData.care_plan_wound_type_id == 1 ? `Ya, ${inpAdultData.care_plan_wound}` : 'Tidak' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top">Pain Management</td>
                                            <td class="align-top px-3">:</td>
                                            <td class="align-top">{{ inpAdultData.care_plan_pain_management_type_id == 1 ? `Ya, ${inpAdultData.care_plan_pain_management}` : 'Tidak' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top">Lain-lain</td>
                                            <td class="align-top px-3">:</td>
                                            <td class="align-top">
                                                <div v-html="parsedBody(inpAdultData.care_plan_other ? inpAdultData.care_plan_other : '-')"></div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </section>
                            <section class="mt-20">
                                <ModernLabel label="Assesment Keperawatan Anamnesa" />
                                <div class="row">
                                    <div class="col-6">
                                        <table class="w-100 mt-16">
                                            <tbody>
                                                <tr>
                                                    <td class="align-top" style="width: 220px;">Alat kesehatan yang terpasang</td>
                                                    <td class="align-top px-3" style="width: 30px;">:</td>
                                                    <td class="align-top">{{ inpAdultData.medical_devices_type_id == 1 ? 'Ya' : 'Tidak ada' }}</td>
                                                </tr>
                                                <tr v-if="inpAdultData.medical_devices_type_id == 1">
                                                    <td colspan="3">
                                                        <b-table fixed bordered :fields="ToolFields" :items="inpAdultData.medical_devices" style="font-size: 14px;">
                                                            <template #cell(type)="row">
                                                                <div v-html="parsedBody(row.value ? row.value : '-')"></div>
                                                            </template>
                                                            <template #cell(explanation)="row">
                                                                <div v-html="parsedBody(row.value ? row.value : '-')"></div>
                                                            </template>
                                                        </b-table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-6">
                                        <table class="w-100 mt-16">
                                            <tbody>
                                                <tr>
                                                    <td class="align-top" style="width: 320px;">Riwayat pemeriksaan yang pernah dilakukan</td>
                                                    <td class="align-top px-3" style="width: 30px;">:</td>
                                                    <td class="align-top"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">
                                                        <b-table fixed bordered :fields="CheckedFields" :items="inpAdultData.examination_history" style="font-size: 14px;">
                                                            <template #cell(type)="row">
                                                                <div v-html="parsedBody(row.value ? row.value : '-')"></div>
                                                            </template>
                                                            <template #cell(page)="row">
                                                                <div v-html="parsedBody(row.value ? row.value : '-')"></div>
                                                            </template>
                                                            <template #cell(explanation)="row">
                                                                <div v-html="parsedBody(row.value ? row.value : '-')"></div>
                                                            </template>
                                                        </b-table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </section>
                            <section class="mt-20">
                                <ModernLabel label="Penilaian Per Sistem" />
                                <div class="row">
                                    <div class="col-6">
                                        <table class="w-100 mt-16">
                                            <tbody>
                                                <tr>
                                                    <td colspan="3" class="font-weight-bold">Skala Nyeri: </td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top" style="width: 220px;">Terdapat keluhan nyeri?</td>
                                                    <td class="align-top px-3" style="width: 30px;">:</td>
                                                    <td class="align-top">{{ inpAdultData.sore_complaints_type_id == 1 ? `Ya, ${inpAdultData.sore_complaints}` : 'Tidak' }}</td>
                                                </tr>
                                                <template v-if="inpAdultData.sore_complaints_type_id == 1">
                                                    <tr>
                                                        <td class="align-top">Nyeri Hilang Bila</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td class="align-top">
                                                            <template v-if="inpAdultData && inpAdultData.sore_goes_away_arry">
                                                                <li v-if="inpAdultData.sore_goes_away_arry.includes('a')">Minum Obat</li>
                                                                <li v-if="inpAdultData.sore_goes_away_arry.includes('b')">Mendengar Musik</li>
                                                                <li v-if="inpAdultData.sore_goes_away_arry.includes('c')">Istirahat</li>
                                                                <li v-if="inpAdultData.sore_goes_away_arry.includes('d')">Berubah Posisi Tidur</li>
                                                                <li v-if="inpAdultData.sore_goes_away_arry.includes('e')">Lain-lain: {{ inpAdultData.sore_goes_away }}</li>
                                                            </template>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Metoda</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td class="align-top">
                                                            <template v-if="inpAdultData && inpAdultData.sore_method_type_id">
                                                                <template v-if="inpAdultData.sore_method_type_id == 1">NIPS</template>
                                                                <template v-else-if="inpAdultData.sore_method_type_id == 2">FLACC</template>
                                                                <template v-else-if="inpAdultData.sore_method_type_id == 3">
                                                                    <div>VAS</div>
                                                                    <div>Face: {{ inpAdultData.face_score }}, Legs: {{ inpAdultData.legs_score }}, Activity: {{ inpAdultData.activity_score }}, Cry: {{ inpAdultData.cry_score }}, Consolability: {{ inpAdultData.consolability_score }}</div>
                                                                </template>
                                                                <template v-else-if="inpAdultData.sore_method_type_id == 4">BPS</template>
                                                                <template v-else-if="inpAdultData.sore_method_type_id == 5">Wong-Baker:
                                                                    <span v-if="inpAdultData.wong_baker_score == 0">0 Tidak Nyeri</span>
                                                                    <span v-else-if="inpAdultData.wong_baker_score == 2">2 Sedikit Nyeri</span>
                                                                    <span v-else-if="inpAdultData.wong_baker_score == 4">4 Sedikit Lebih Nyeri</span>
                                                                    <span v-else-if="inpAdultData.wong_baker_score == 6">6 Lebih Nyeri</span>
                                                                    <span v-else-if="inpAdultData.wong_baker_score == 8">8 Sangat Nyeri</span>
                                                                    <span v-else-if="inpAdultData.wong_baker_score == 10">10 Nyeri Sangat Hebat</span>
                                                                </template>
                                                                <template v-else-if="inpAdultData.sore_method_type_id == 6">NRS:
                                                                    <span v-if="inpAdultData.nrs_score == 0">0 Tidak Nyeri</span>
                                                                    <span v-else-if="inpAdultData.nrs_score >= 1 && inpAdultData.nrs_score <= 3">{{ inpAdultData.nrs_score }} Nyeri Ringan</span>
                                                                    <span v-else-if="inpAdultData.nrs_score >= 4 && inpAdultData.nrs_score <= 6">{{ inpAdultData.nrs_score }} Nyeri Sedang</span>
                                                                    <span v-else-if="inpAdultData.nrs_score >= 7 && inpAdultData.nrs_score <= 9">{{ inpAdultData.nrs_score }} Nyeri Terkontrol</span>
                                                                    <span v-else-if="inpAdultData.nrs_score == 10">10 Nyeri Berat Tidak Terkontrol</span>
                                                                </template>
                                                            </template>
                                                        </td>
                                                    </tr>
                                                    <tr v-if="inpAdultData.sore_method_type_id == 6">
                                                        <td class="align-top">Diberitahukan ke dokter</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td class="align-top">{{ inpAdultData.tell_doctor_type_id == 1 ? `Ya, ${inpAdultData.tell_doctor_date}` : 'Tidak' }}</td>
                                                    </tr>
                                                </template>
                                                <tr>
                                                    <td class="align-top">Durasi</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td class="align-top">{{ inpAdultData.duration ? inpAdultData.duration : '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Lokasi</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td class="align-top">{{ inpAdultData.location ? inpAdultData.location : '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Frekuensi Nyeri</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td class="align-top">{{ inpAdultData.frequency ? inpAdultData.frequency : '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Kualitas Nyeri</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td class="align-top">
                                                        <template v-if="inpAdultData && inpAdultData.sore_quality_arry">
                                                            <li v-if="inpAdultData.sore_quality_arry.includes('a')">Tertusuk</li>
                                                            <li v-if="inpAdultData.sore_quality_arry.includes('b')">Terbakar</li>
                                                            <li v-if="inpAdultData.sore_quality_arry.includes('c')">Tertekan</li>
                                                        </template>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Nyeri Mempengaruhi</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td class="align-top">
                                                        <template v-if="inpAdultData && inpAdultData.sore_affects_arry">
                                                            <li v-if="inpAdultData.sore_affects_arry.includes('a')">Nyeri Mempengaruhi</li>
                                                            <li v-if="inpAdultData.sore_affects_arry.includes('b')">Tidur</li>
                                                            <li v-if="inpAdultData.sore_affects_arry.includes('c')">Aktifitas Fisik</li>
                                                            <li v-if="inpAdultData.sore_affects_arry.includes('d')">Emosi</li>
                                                            <li v-if="inpAdultData.sore_affects_arry.includes('e')">Nafsu Makan</li>
                                                            <li v-if="inpAdultData.sore_affects_arry.includes('f')">Lain-lain</li>
                                                        </template>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top font-weight-bold">Riwayat Merokok</td>
                                                    <td class="align-top px-3" style="width: 30px;">:</td>
                                                    <td class="align-top">{{ inpAdultData.evaluation_smoke_type_id == 1 ? `Ya, ${inpAdultData.evaluation_smoke_amount ? inpAdultData.evaluation_smoke_amount : '-'} per hari, ${inpAdultData.evaluation_smoke_long}` : 'Tidak' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top font-weight-bold">Riwayat minum minuman keras</td>
                                                    <td class="align-top px-3" style="width: 30px;">:</td>
                                                    <td class="align-top">{{ inpAdultData.evaluation_alcohol_type_id == 1 ? `Ya, jenis: ${inpAdultData.evaluation_alcohol_type ? inpAdultData.evaluation_alcohol_type : '-'}, ${inpAdultData.evaluation_alcohol_amount ? inpAdultData.evaluation_alcohol_amount : '-'} per hari` : 'Tidak' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top font-weight-bold">Riwayat penggunaan obat penenang</td>
                                                    <td class="align-top px-3" style="width: 30px;">:</td>
                                                    <td class="align-top">{{ inpAdultData.evaluation_sedative_type_id == 1 ? `Ya, jenis: ${inpAdultData.evaluation_sedative_type ? inpAdultData.evaluation_sedative_type : '-'}, ${inpAdultData.evaluation_sedative_amount ? inpAdultData.evaluation_sedative_amount : '-'} per hari` : 'Tidak' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top font-weight-bold">Gangguan pola istirahat / tidur</td>
                                                    <td class="align-top px-3" style="width: 30px;">:</td>
                                                    <td class="align-top">{{ inpAdultData.evaluation_sleep_type_id == 1 ? `Ya,  ${inpAdultData.evaluation_sleep ? inpAdultData.evaluation_sleep : '-'}` : 'Tidak' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-6">
                                        <table class="w-100 mt-16">
                                            <tbody>
                                                <tr>
                                                    <td colspan="3" class="font-weight-bold">Asesmen fungsi:</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top" style="width: 220px;">Kemampuan melakukan aktivitas sehari-hari</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td class="align-top">
                                                        <template v-if="inpAdultData && inpAdultData.evaluation_daily_type_id">
                                                            <template v-if="inpAdultData.evaluation_daily_type_id == 1">Mandiri</template>
                                                            <template v-else-if="inpAdultData.evaluation_daily_type_id == 2">Bantuan Minimal</template>
                                                            <template v-else-if="inpAdultData.evaluation_daily_type_id == 3">Bantuan Sebagian</template>
                                                            <template v-else-if="inpAdultData.evaluation_daily_type_id == 4">Ketergantungan Total</template>
                                                        </template>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Aktivitas</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td class="align-top">
                                                        <template v-if="inpAdultData && inpAdultData.evaluation_activity_type_id">
                                                            <template v-if="inpAdultData.evaluation_activity_type_id == 1">Tirah Baring</template>
                                                            <template v-else-if="inpAdultData.evaluation_activity_type_id == 2">Duduk</template>
                                                            <template v-else-if="inpAdultData.evaluation_activity_type_id == 3">Berjalan</template>
                                                        </template>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Ekstremitas Atas</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td class="align-top">
                                                        <template v-if="inpAdultData && inpAdultData.evaluation_extremities_top_type_id">
                                                            <template v-if="inpAdultData.evaluation_extremities_top_type_id == 1">Tidak ada kelainan</template>
                                                            <template v-else-if="inpAdultData.evaluation_extremities_top_type_id == 2">Lemah</template>
                                                        </template>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Kemampuan Menggenggam</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td class="align-top">
                                                        <template v-if="inpAdultData && inpAdultData.evaluation_grip_type_id">
                                                            <template v-if="inpAdultData.evaluation_grip_type_id == 1">Tidak ada kesulitan</template>
                                                            <template v-else-if="inpAdultData.evaluation_grip_type_id == 2">Terkilir</template>
                                                            <template v-else-if="inpAdultData.evaluation_grip_type_id == 3">Lainnya: {{ inpAdultData.evaluation_grip_other ? inpAdultData.evaluation_grip_other : '-' }}</template>
                                                        </template>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Kemampuan koordinasi</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td class="align-top">
                                                        <template v-if="inpAdultData && inpAdultData.evaluation_coordination_type_id">
                                                            <template v-if="inpAdultData.evaluation_coordination_type_id == 1">Tidak ada kesulitan</template>
                                                            <template v-else-if="inpAdultData.evaluation_coordination_type_id == 2">Ada Masalah: {{ inpAdultData.evaluation_coordination_other ? inpAdultData.evaluation_coordination_other : '-' }}</template>
                                                        </template>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Alat Ambulasi</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td class="align-top">
                                                        <template v-if="inpAdultData && inpAdultData.evaluation_ambulation_type_id">
                                                            <template v-if="inpAdultData.evaluation_ambulation_type_id == 1">Walker</template>
                                                            <template v-else-if="inpAdultData.evaluation_ambulation_type_id == 2">Tongkat</template>
                                                            <template v-else-if="inpAdultData.evaluation_ambulation_type_id == 3">Kursi Roda</template>
                                                        </template>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Ekstremitas Bawah</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td class="align-top">
                                                        <template v-if="inpAdultData && inpAdultData.evaluation_extremities_bottom_type_id">
                                                            <template v-if="inpAdultData.evaluation_extremities_bottom_type_id == 1">Tidak ada kesulitan</template>
                                                            <template v-else-if="inpAdultData.evaluation_extremities_bottom_type_id == 2">Penurunan kekuatan / Range of Motion</template>
                                                            <template v-else-if="inpAdultData.evaluation_extremities_bottom_type_id == 3">Paralisis</template>
                                                            <template v-else-if="inpAdultData.evaluation_extremities_bottom_type_id == 4">Sering jatuh</template>
                                                            <template v-else-if="inpAdultData.evaluation_extremities_bottom_type_id == 5">Hilang keseimbangan</template>
                                                            <template v-else-if="inpAdultData.evaluation_extremities_bottom_type_id == 6">Deformitas</template>
                                                            <template v-else-if="inpAdultData.evaluation_extremities_bottom_type_id == 7">Riwayat patah tulang: {{ inpAdultData.evaluation_extremities_bottom_bone ? inpAdultData.evaluation_extremities_bottom_bone : '-' }}</template>
                                                        </template>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="font-weight-bold">Keadaan Mental dan Psikologis:</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Status Psikologis</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td class="align-top">
                                                        <template v-if="inpAdultData && inpAdultData.psychological_status_arry">
                                                            <li v-if="inpAdultData.psychological_status_arry.includes('a')">Cemas</li>
                                                            <li v-if="inpAdultData.psychological_status_arry.includes('b')">Takut</li>
                                                            <li v-if="inpAdultData.psychological_status_arry.includes('c')">Marah</li>
                                                            <li v-if="inpAdultData.psychological_status_arry.includes('d')">Sedih</li>
                                                            <li v-if="inpAdultData.psychological_status_arry.includes('e')">Cenderung ingin bunuh diri</li>
                                                            <li v-if="inpAdultData.psychological_status_arry.includes('f')">Lain-lain: {{ inpAdultData.psychological_status ? inpAdultData.psychological_status : '-' }}</li>
                                                        </template>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Status Mental</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td class="align-top">
                                                        <template v-if="inpAdultData && inpAdultData.mental_status_arry">
                                                            <li v-if="inpAdultData.mental_status_arry.includes('a')">Sadar dan orientasi baik</li>
                                                            <li v-if="inpAdultData.mental_status_arry.includes('b')">Ada masalah perilaku, sebutkan: {{ inpAdultData.mental_status_problems ? inpAdultData.mental_status_problems : '-' }}</li>
                                                            <li v-if="inpAdultData.mental_status_arry.includes('c')">Perilaku kekerasan yang dialami pasien sebelumnya: {{ inpAdultData.mental_status_violence ? inpAdultData.mental_status_violence : '-' }}</li>
                                                        </template>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="font-weight-bold">Eliminasi:</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Frekuensi BAB</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td class="align-top">{{ inpAdultData.elimination_defecate ? inpAdultData.elimination_defecate : '-' }} x/hari</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Konsistensi</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td class="align-top">
                                                        <template v-if="inpAdultData && inpAdultData.elimination_consistency_type_id">
                                                            <template v-if="inpAdultData.elimination_consistency_type_id == 1">Lunak</template>
                                                            <template v-else-if="inpAdultData.elimination_consistency_type_id == 2">Keras</template>
                                                            <template v-else-if="inpAdultData.elimination_consistency_type_id == 3">Cair</template>
                                                            <template v-else-if="inpAdultData.elimination_consistency_type_id == 4">Ampas</template>
                                                            <template v-else-if="inpAdultData.elimination_consistency_type_id == 5">Tidak</template>
                                                        </template>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Frekuensi BAK</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td class="align-top">{{ inpAdultData.elimination_urination ? inpAdultData.elimination_urination : '-' }} x/hari</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Warna</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td class="align-top">
                                                        <template v-if="inpAdultData && inpAdultData.elimination_color_type_id">
                                                            <template v-if="inpAdultData.elimination_color_type_id == 1">Kuning Jernih</template>
                                                            <template v-else-if="inpAdultData.elimination_color_type_id == 2">Kuning Keruh</template>
                                                            <template v-else-if="inpAdultData.elimination_color_type_id == 3">Lainnya: {{ inpAdultData.elimination_color ? inpAdultData.elimination_color : '-' }}</template>
                                                        </template>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </section>

                    <section v-if="nurseData.type_nurse_id == 13" class="card mt-32">
                        <div class="card-header text-center font-weight-bold">KAJIAN AWAL HEMODIALISA</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <section>
                                        <table class="w-100">
                                            <tbody>
                                                <tr>
                                                    <td class="align-top" style="width: 200px;">Tanggal HD Inisiasi</td>
                                                    <td class="align-top px-3" style="width: 30px;">:</td>
                                                    <td class="align-top">{{ hemodialysisData.hemodialysis_date ? hemodialysisData.hemodialysis_date : '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Jenis Hemodialisis</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td class="align-top">
                                                        <template v-if="hemodialysisData && hemodialysisData.hemodialysis_type_id">
                                                            <template v-if="hemodialysisData.hemodialysis_type_id == 1">Rutin</template>
                                                            <template v-else-if="hemodialysisData.hemodialysis_type_id == 2">Cito</template>
                                                            <template v-else-if="hemodialysisData.hemodialysis_type_id == 3">Ekstra</template>
                                                        </template>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Frekuensi Hemodialisis</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td class="align-top">
                                                        <template v-if="hemodialysisData && hemodialysisData.frequency_type_id">
                                                            <template v-if="hemodialysisData.frequency_type_id == 1">1x/minggu</template>
                                                            <template v-else-if="hemodialysisData.frequency_type_id == 2">2x/minggu</template>
                                                            <template v-else-if="hemodialysisData.frequency_type_id == 3">3x/minggu</template>
                                                            <template v-else-if="hemodialysisData.frequency_type_id == 4">Tidak Tentu</template>
                                                        </template>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Jenis Dialisat</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td class="align-top">
                                                        <template v-if="hemodialysisData && hemodialysisData.dialysate_type_id">
                                                            <template v-if="hemodialysisData.dialysate_type_id == 1">Asetat</template>
                                                            <template v-else-if="hemodialysisData.dialysate_type_id == 2">Bikarbonat</template>
                                                            <template v-else-if="hemodialysisData.dialysate_type_id == 3">Suhu Dialisat: {{ hemodialysisData.dialysate_temperature ? hemodialysisData.dialysate_temperature : '-' }} ᵒC</template>
                                                        </template>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Akses Vaskuler</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td class="align-top">
                                                        <template v-if="hemodialysisData && hemodialysisData.vascular_type_id">
                                                            <template v-if="hemodialysisData.vascular_type_id == 1">Cimino</template>
                                                            <template v-else-if="hemodialysisData.vascular_type_id == 2">Femoral</template>
                                                            <template v-else-if="hemodialysisData.vascular_type_id == 3">CDL Subciavia</template>
                                                            <template v-else-if="hemodialysisData.vascular_type_id == 4">CDL Jugularis: {{ hemodialysisData.vascular_jugularis ? hemodialysisData.vascular_jugularis : '-' }}</template>
                                                        </template>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Lama HD</td>
                                                    <td class="align-top px-3" style="width: 30px;">:</td>
                                                    <td class="align-top">{{ hemodialysisData.long_hours ? hemodialysisData.long_hours : '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Kec. Darah (QB)</td>
                                                    <td class="align-top px-3" style="width: 30px;">:</td>
                                                    <td class="align-top">{{ hemodialysisData.blood_velocity ? hemodialysisData.blood_velocity : '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Heparinisasi</td>
                                                    <td class="align-top px-3" style="width: 30px;">:</td>
                                                    <td class="align-top">
                                                        <template v-if="hemodialysisData && hemodialysisData.heparin_type_id">
                                                            <template v-if="hemodialysisData.heparin_type_id == 1">Heparin</template>
                                                            <template v-else-if="hemodialysisData.heparin_type_id == 2">Tanpa Heparin, {{ hemodialysisData.without_heparin }}</template>
                                                        </template>
                                                    </td>
                                                </tr>
                                                <template>
                                                    <tr>
                                                        <td class="align-top">Dosis Awal</td>
                                                        <td class="align-top px-3" style="width: 30px;">:</td>
                                                        <td class="align-top">{{ hemodialysisData.initial_dose ? hemodialysisData.initial_dose : '-' }} IU</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Intermitten</td>
                                                        <td class="align-top px-3" style="width: 30px;">:</td>
                                                        <td class="align-top">{{ hemodialysisData.intermittent ? hemodialysisData.intermittent : '-' }} IU</td>
                                                    </tr>
                                                </template>
                                                <tr>
                                                    <td class="align-top">UFG</td>
                                                    <td class="align-top px-3" style="width: 30px;">:</td>
                                                    <td class="align-top">{{ hemodialysisData.ufg ? hemodialysisData.ufg : '-' }} ml</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Kec. Dialisit (QD)</td>
                                                    <td class="align-top px-3" style="width: 30px;">:</td>
                                                    <td class="align-top">{{ hemodialysisData.dialysate_velocity ? hemodialysisData.dialysate_velocity : '-' }} ml/mnt</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </section>
                                </div>
                                <div class="col-6">
                                    <section>
                                        <table class="w-100">
                                            <tbody>
                                                <tr>
                                                    <td class="align-top" style="width: 200px;">Maintenance</td>
                                                    <td class="align-top px-3" style="width: 30px;">:</td>
                                                    <td class="align-top">{{ hemodialysisData.maintenance ? hemodialysisData.maintenance : '-' }} IU</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">LMWH</td>
                                                    <td class="align-top px-3" style="width: 30px;">:</td>
                                                    <td class="align-top">{{ hemodialysisData.lmwh ? hemodialysisData.lmwh : '-' }} IU</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Program Profiling</td>
                                                    <td class="align-top px-3" style="width: 30px;">:</td>
                                                    <td class="align-top">
                                                        <template v-if="hemodialysisData && hemodialysisData.profiling_type_id">
                                                            <template v-if="hemodialysisData.profiling_type_id == 1">Ultrafiltrasi Mode: {{ hemodialysisData.ultrafiltration ? hemodialysisData.ultrafiltration : '-' }}</template>
                                                            <template v-else-if="hemodialysisData.profiling_type_id == 2">Natriuum Mode: {{ hemodialysisData.natrium ? hemodialysisData.natrium : '-' }}</template>
                                                            <template v-else-if="hemodialysisData.profiling_type_id == 3">Bikarbonat Mode: {{ hemodialysisData.bicarbonate ? hemodialysisData.bicarbonate : '-' }}</template>
                                                        </template>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Terapi</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>
                                                        <div v-html="parsedBody(hemodialysisData.therapy ? hemodialysisData.therapy : '-')"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Obat Rutin</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>
                                                        <div v-html="parsedBody(hemodialysisData.medicine ? hemodialysisData.medicine : '-')"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Injeksi</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>
                                                        <div v-html="parsedBody(hemodialysisData.injection ? hemodialysisData.injection : '-')"></div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </section>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="card mt-32">
                        <div class="card-header text-center font-weight-bold">INFORMASI PERAWAT</div>
                        <div class="card-body">
                            <b-table-simple small class="text-regular-14">
                                <b-tbody>
                                    <b-tr>
                                        <b-td class="border-0" style="width: 20%;">Nama Perawat</b-td>
                                        <b-td class="border-0 text-capitalize">: {{ nurseData.employee_nurse_name }}</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td class="border-0" style="width: 20%;">Tanggal & Jam Input</b-td>
                                        <b-td class="border-0">: {{ nurseData.date_added }}</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td class="border-0" style="width: 20%;">Poli</b-td>
                                        <b-td class="border-0">: {{ regData.health_facility_item_name }}</b-td>
                                    </b-tr>
                                </b-tbody>
                            </b-table-simple>
                        </div>
                    </section>
                    <div class="d-flex justify-content-end px-3 mt-72">
                        <div class="text-right text-regular-14">
                            <p>Perawat Rawat Jalan</p>
                            <p class="mt-40 text-capitalize">( {{ nurseData.employee_nurse_name }} )</p>
                        </div>
                    </div>

                </div>
            </div>
        </b-container>
    </div>
</template>
<script>
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import moment from 'moment';

export default {
    components: {ModernLabel},

    data() {
        return {
            dummy_condition: false,
            regData: {},
            nurseData: {},
            neonatusData: {},
            childData: {},
            emergencyData: {},
            inpAdultData: {},
            hemodialysisData: {},

            ToolFields: [
                { key: 'type', label: 'Jenis' },
                { key: 'explanation', label: 'Keterangan' },
            ],
            CheckedFields: [
                { key: 'type', label: 'Jenis Pemeriksaan' },
                { key: 'page', label: 'Lembar' },
                { key: 'explanation', label: 'Keterangan' },
            ],

            OptionsOxygen: [
                { value: 2, title: 'Tidak' },
                { value: 1, title: 'Ya' },
            ],
            OptionsAware: [
                { value: 3, title: 'Tidak' },
                { value: 0, title: 'Ya' },
            ],

            OptionGcsEye: [
                { value: 1, title: 'Mata tidak terbuka sama sekali' },
                { value: 2, title: 'Mata hanya terbuka sebagai respons terhadap tekanan' },
                { value: 3, title: 'Hanya membuka mata saat diminta, dan tetap tertutup jika tidak' },
                { value: 4, title: 'Dapat membuka mata secara mandiri' },
            ],
            OptionGcsVerbal: [
                { value: 1, title: 'Tidak bisa berbicara atau membuat suara sama sekali' },
                { value: 2, title: 'Hanya bisa membuat suara, tetapi tidak berbicara' },
                { value: 3, title: 'Bisa berbicara, tetapi respons tidak masuk akal' },
                { value: 4, title: 'Mengalami kebingungan, namun masih bisa menjawab meski kurang paham situasi' },
                { value: 5, title: 'Punya orientasi baik karena mampu menjawab siapa, di mana, serta tanggal atau tahun' },
            ],
            OptionGcsMotoric: [
                { value: 1, title: 'Tidak merespons atau bergerak sama sekali' },
                { value: 2, title: 'Otot meregang (mengulur ke luar) sebagai respons terhadap tekanan' },
                { value: 3, title: 'Otot menegang (menarik ke dalam) saat merasakan tekanan' },
                { value: 4, title: 'Bergerak sebagai refleks terhadap tekanan' },
                { value: 5, title: 'Bergerak menjauh dari tekanan yang dirasakan secara sengaja' },
                { value: 6, title: 'Mampu mengikuti instruksi kapan dan bagaimana harus bergerak' },
            ],
            OptionGcsPupil: [
                { value: 0, title: 'Kedua pupil bereaksi terhadap cahaya' },
                { value: 1, title: 'Satu pupil tidak bereaksi terhadap cahaya' },
                { value: 2, title: 'Kedua pupil tidak bereaksi terhadap cahaya' },
            ],
        }
    },
    methods: {
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
        getGcsPupilName(value) {
            const result = this.OptionGcsPupil.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        saveChangedNurse($event){
            localStorage.setItem("nursestudies", JSON.stringify(this.form_nurse))
        },

        getTypeNurseName(type_nurse_id){
            switch (type_nurse_id) {
                case 1:
                    return 'Perawat: Kajian Awal Dewasa'
                case 2:
                    return 'Perawat: Kajian Awal Anak'
                case 3:
                    return 'Perawat: Kajian Awal Obgyn'
                case 4:
                    return 'Perawat: Kajian Awal IGD'
                case 5:
                    return 'Triase'
                case 6:
                    return 'Perawat: Kajian Awal Rawat Inap Anak'
                case 7:
                    return 'Perawat: Kajian Awal Rawat Inap Dewasa'
                case 8:
                    return 'Perawat: Kajian Awal Rawat Inap Lansia'
                case 9:
                    return 'Perawat: Kajian Awal Fisioterapi'
                case 10:
                    return 'Perawat: Kajian SOAP'
                case 11:
                    return 'SDKI SLKI SIKI'
                case 12:
                    return 'Perawat: Kajian Awal Rawat Inap Neonatus'
                case 13:
                    return 'Perawat: Kajian Awal Hemodialisa'
                default:
                    return 'Kajian Perawat'
            }
        },
        parsedBody(text){
            return String(text || '').replace(/\n/g, "<br>");
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

            if (this.emergencyData && this.emergencyData.fall_risk_component_b === 0 || this.emergencyData && this.emergencyData.fall_risk_component_b == 1 ) {
                return this.emergencyData.fall_risk_component_b == value ? "check-circle-fill" : "circle-fill";
            } else {
                return "circle-fill";
            }
        },
        getFallBIconVariant(value) {
            if (this.emergencyData && this.emergencyData.fall_risk_component_b === 0 || this.emergencyData && this.emergencyData.fall_risk_component_b == 1) {
                return this.emergencyData.fall_risk_component_b == value ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },
        localDate(input_date) {
            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },
        getData (nurse_id) {
			this.$store.dispatch('nurse/getByNurseId', nurse_id).then(data => {
                this.nurseData = data[0]

                this.$store.dispatch('registration/getById', this.nurseData.registration_destination_id).then(data => {
                    this.regData = data[0]
                })

                if (this.nurseData.type_nurse_id === 4) {
                    this.$store.dispatch('nurse/getEmergencyDetail', { nurse_id: this.nurseData.id }).then((igd) => {
                        this.emergencyData = igd[0]
                    })
                // } else if(this.nurseData.type_nurse_id === 6) {
                //     this.$store.dispatch('nurse/getByNurseIdOfPrintChild', this.nurseData.id).then(data => {
                //         this.childData = data.data
                //     })
                } else if (this.nurseData.type_nurse_id === 7) {
                    this.$store.dispatch('nurse/getAdultInpatientDetail', { nurse_id: this.nurseData.id }).then((response) => {
                        this.inpAdultData = response[0]
                        // console.log('this.inpAdultData', this.inpAdultData);
                    })
                // } else if (this.nurseData.type_nurse_id === 12) {
                //     this.$store.dispatch('nurse/getByNurseIdOfPrintNeonatus', this.nurseData.id).then(data => {
                //         this.neonatusData = data.data
                //     })
                } else if (this.nurseData.type_nurse_id === 13) {
                    this.$store.dispatch('nurse/getHemodialysisDetail', { nurse_id: this.nurseData.id }).then((response) => {
                        this.hemodialysisData = response[0]
                        // console.log('this.hemodialysisData', this.hemodialysisData);
                    })
                }
            })
        },
        //neonatus:: data collection
        NeonatusGetHasilLaboratoriumIbu(data) {
            const NeonatusGetHasilLaboratoriumIbu = {
                '1' : 'HB',
                '2' : 'HbsAg',
                '3' : 'Leukosit',
                '4' : 'Lainnya',
            }
            return NeonatusGetHasilLaboratoriumIbu[data] || '-'
        },
        NeonatusGolonganDarahIbu(data) {
            const NeonatusGolonganDarahIbu = {
                '1' : 'A',
                '2' : 'B',
                '3' : 'O',
                '4' : 'AB',
                '5' : 'Rhesus',
            }
            return NeonatusGolonganDarahIbu[data] || '-'
        },
        NeonatusRiwayatPersalinan(data){
            const NeonatusRiwayatPersalinan = {
                '1' : 'Spontan',
                '2' : 'Vakum/Forcep',
                '3' : 'SC',
            }
            return NeonatusRiwayatPersalinan[data] || '-'
        },
        NeonatusRiwayatKelahiran(data){
            const NeonatusRiwayatKelahiran = {
                '1' : 'Tunggal',
                '2' : 'Kembar',
                '3' : 'LK',
                '4' : 'LD',
                '5' : 'Anus',
            }
            return NeonatusRiwayatKelahiran[data] || '-'
        },
        NeonatusJenisKelaminBayi(data){
            const NeonatusJenisKelaminBayi = {
                '1' : 'Laki-laki',
                '2' : 'Perempuan'
            }
            return NeonatusJenisKelaminBayi[data] || '-'
        },
        NeonatusPemberianObatSetelahLahir(data){
            const NeonatusPemberianObatSetelahLahir = {
                '1' : 'Vit K',
                '2' : 'Salep Mata',
                '3' : 'Lainnya',
            }
            return NeonatusPemberianObatSetelahLahir[data] || '-'
        },
        NeonatusKebutuhanEdukasiOrangtua(data){
            const NeonatusKebutuhanEdukasiOrangtua = {
                '1': 'Normal',
                '2': 'Gangguan Bicara',
            }
            return NeonatusKebutuhanEdukasiOrangtua[data] || '-'
        },
        NeonatusBahasaSehariHari(data){
            const NeonatusBahasaSehariHari = {
                '1': 'Indonesia',
                '2': 'Inggris',
                '3': 'Daerah',
                '4': 'Lainnya',
            }
            return NeonatusBahasaSehariHari[data] || '-'
        },
        NeonatusPerluPenerjemah(data){
            const NeonatusPerluPenerjemah = {
                '1': 'Ya',
                '2': 'Tidak',
            }
            return NeonatusPerluPenerjemah[data] || '-'
        },
        NeonatusTingkatPendidikan(data){
            const NeonatusTingkatPendidikan = {
                '1': 'SD',
                '2': 'SMP',
                '3': 'SMA',
                '4': 'Akademi',
                '5': 'Lainnya',
            }
            return NeonatusTingkatPendidikan[data] || '-'
        },
        NeonatusAgama(data){
            const NeonatusAgama = {
                '1': 'Islam',
                '2': 'Kristen',
                '3': 'Katolik',
                '4': 'Hindu',
                '5': 'Budha',
                '6': 'Lainnya',
            }
            return NeonatusAgama[data] || '-'
        },
        NeonatusTempatTinggalBayi(data){
            const NeonatusTempatTinggalBayi = {
                '1': 'Penitipan Anak',
                '2': 'Pengasuh',
                '3': 'Rumah',
            }
            return NeonatusTempatTinggalBayi[data] || '-'
        },
        NeonatusYangMerawatBayi(data){
            const NeonatusYangMerawatBayi = {
                '1': 'Ayah',
                '2': 'Ibu',
                '3': 'Nenek',
                '4': 'Pengasuh',
            }
            return NeonatusYangMerawatBayi[data] || '-'
        },
        NeonatusBerkunjungMenyentuhMenggendongKotakMata(data){
            const NeonatusBerkunjungMenyentuhMenggendongKotakMata = {
                '1': 'Ya',
                '2': 'Tidak',
            }
            return NeonatusBerkunjungMenyentuhMenggendongKotakMata[data] || '-'
        },
        //child:: collection
        ChildJenisPersalinan(data) {
            const ChildJenisPersalinan = {
                '1': 'Normal',
                '2': 'Abnormal',
            }
            return ChildJenisPersalinan[data] || '-'
        },
        ChildStatusGizi(data) {
            const ChildStatusGizi = {
                '1': 'Kurang',
                '2': 'Cukup',
            }
            return ChildStatusGizi[data] || '-'
        },
        //pemeriksaan fisik
        //neorologi
        ChildCollectPemeriksaanFisikNeorologiPenglihatan(data) {
            const ChildCollectPemeriksaanFisikNeorologiPenglihatan = {
                    '1': 'Tidak ada kelainan',
                    '2': 'Gangguan Penglihatan',
                };
            return ChildCollectPemeriksaanFisikNeorologiPenglihatan[data] || '-'
        },
        ChildCollectPemeriksaanFisikNeorologiBicara(data) {
            const ChildCollectPemeriksaanFisikNeorologiBicara = {
                    '1': 'Jelas',
                    '2': 'Tidak',
                };
            return ChildCollectPemeriksaanFisikNeorologiBicara[data] || '-'
        },
        ChildCollectPemeriksaanFisikNeorologiKekuatanOtot(data) {
            const ChildCollectPemeriksaanFisikNeorologiKekuatanOtot = {
                    '1': 'Kuat',
                    '2': 'Lemah',
                };
            return ChildCollectPemeriksaanFisikNeorologiKekuatanOtot[data] || '-'
        },
        ChildCollectPemeriksaanFisikNeorologiMenggunakanAlatBantuPendengaranPenglihatan(data) {
            const ChildCollectPemeriksaanFisikNeorologiMenggunakanAlatBantuPendengaranPenglihatan = {
                    '1': 'Tidak',
                    '2': 'Ya',
                };
            return ChildCollectPemeriksaanFisikNeorologiMenggunakanAlatBantuPendengaranPenglihatan[data] || '-'
        },
        ChildCollectPemeriksaanFisikNeorologiSensorik(data) {
            const ChildCollectPemeriksaanFisikNeorologiSensorik = {
                '1': 'Tidak Ada Kelainan',
                '2': 'Nyeri',
                '3': 'Rasa Kebas',
                '4': 'Lainnya'
            };
            return ChildCollectPemeriksaanFisikNeorologiSensorik[data] || '-'
        },
        ChildCollectPemeriksaanFisikNeorologiPendengaran(data) {
            const ChildCollectPemeriksaanFisikNeorologiPendengaran = {
                '1': 'Tidak Ada Kelainan',
                '2': 'Tuli',
                '3': 'Berdengung',
                '4': 'Nyeri',
                '5': 'Keluar Cairan'
            };
            return ChildCollectPemeriksaanFisikNeorologiPendengaran[data] || '-'
        },
        ChildCollectPemeriksaanFisikNeorologiMotorik(data) {
            const ChildCollectPemeriksaanFisikNeorologiMotorik ={
                '1': 'Hemiparase',
                '2': 'Tetrapaase',
                '3': 'Tremor',
                '4': 'Tidak ada kelainan',
            };
            return ChildCollectPemeriksaanFisikNeorologiMotorik[data] || '-'
        },
        //integument
        ChildCollectPemeriksaanFisikIntegumentWarnaKulit(data) {
            const ChildCollectPemeriksaanFisikIntegumentWarnaKulit ={
                '1': 'Pucat',
                '2': 'Kemerahan',
                '3': 'Normal',
            };
            return ChildCollectPemeriksaanFisikIntegumentWarnaKulit[data] || '-'
        },
        ChildCollectPemeriksaanFisikIntegumentKriteriaResikoDekubitus(data) {
            const ChildCollectPemeriksaanFisikIntegumentKriteriaResikoDekubitus ={
                '1': 'Dirawat di intensive care',
                '2': 'Inkontinensia uri/alvi',
                '3': 'Penyakit Kronis (DM,CHF,CKD)',
                '4': 'Pasien immobilisasi',
                '5': 'Obesitas/cachexia',
                '6': 'Paraplegi/vegetatif stage',
            };
            return ChildCollectPemeriksaanFisikIntegumentKriteriaResikoDekubitus[data] || '-'
        },
        ChildCollectPemeriksaanFisikIntegumentTugor(data) {
            const ChildCollectPemeriksaanFisikIntegumentTugor ={
                '1': 'Baik',
                '2': 'Sedang',
                '3': 'Buruk',
                '4': 'Lesi',
                '5': 'Lain-lain..',
            };
            return ChildCollectPemeriksaanFisikIntegumentTugor[data] || '-'
        },
        ChildCollectPemeriksaanFisikIntegumentKulit(data) {
            const ChildCollectPemeriksaanFisikIntegumentKulit ={
                '1': 'Normal',
                '2': 'Kemerahan',
                '3': 'Bula',
                '4': 'Memar',
                '5': 'Ptekie',
                '6': 'Lainnya-lainnya...',
            };
            return ChildCollectPemeriksaanFisikIntegumentKulit[data] || '-'
        },
        //kardiovaskuler
        ChildCollectPemeriksaanFisikKardiovaskulerWarnaKulit(data) {
            const ChildCollectPemeriksaanFisikKardiovaskulerWarnaKulit ={
                '1': 'Normal',
                '2': 'Kemerahan',
                '3': 'Sianosis',
                '4': 'Pucat',
                '5': 'Lain-lain',
            };
            return ChildCollectPemeriksaanFisikKardiovaskulerWarnaKulit[data] || '-'
        },
        ChildCollectPemeriksaanFisikKardiovaskulerNyeriDada(data) {
            const ChildCollectPemeriksaanFisikKardiovaskulerNyeriDada ={
                '1': 'Tidak',
                '2': 'Ya',
            };
            return ChildCollectPemeriksaanFisikKardiovaskulerNyeriDada[data] || '-'
        },
        ChildCollectPemeriksaanFisikKardiovaskulerDenyutNadi(data) {
            const ChildCollectPemeriksaanFisikKardiovaskulerDenyutNadi ={
                '1': 'Teratur',
                '2': 'Tidak teratur',
            };
            return ChildCollectPemeriksaanFisikKardiovaskulerDenyutNadi[data] || '-'
        },
        ChildCollectPemeriksaanFisikKardiovaskulerPulasi(data) {
            const ChildCollectPemeriksaanFisikKardiovaskulerPulasi ={
                '1': 'Kuat',
                '2': 'Lemah',
            };
            return ChildCollectPemeriksaanFisikKardiovaskulerPulasi[data] || '-'
        },
        ChildCollectPemeriksaanFisikKardiovaskulerSirkulasi(data) {
            const ChildCollectPemeriksaanFisikKardiovaskulerSirkulasi ={
                '1': 'Akral hangat',
                '2': 'Akral dingin',
                '3': 'Tachipnea',
                '4': 'Palpitasi',
                '5': 'Edema,lokasi',
            };
            return ChildCollectPemeriksaanFisikKardiovaskulerSirkulasi[data] || '-'
        },
        //respirasi
        ChildCollectPemeriksaanFisikRespirasiPolaNafas(data) {
            const ChildCollectPemeriksaanFisikRespirasiPolaNafas ={
                '1': 'Normal',
                '2': 'Bradipnea',
                '3': 'Tachpnea',
            };
            return ChildCollectPemeriksaanFisikRespirasiPolaNafas[data] || '-'
        },
        ChildCollectPemeriksaanFisikRespirasiVolumePernafasan(data) {
            const ChildCollectPemeriksaanFisikRespirasiVolumePernafasan ={
                '1': 'Normal',
                '2': 'Hiperventilasi',
                '3': 'Hipoventilasi',
            };
            return ChildCollectPemeriksaanFisikRespirasiVolumePernafasan[data] || '-'
        },
        ChildCollectPemeriksaanFisikRespirasiIramaNafas(data) {
            const ChildCollectPemeriksaanFisikRespirasiIramaNafas ={
                '1': 'Teratur',
                '2': 'Tidak teratur',
            };
            return ChildCollectPemeriksaanFisikRespirasiIramaNafas[data] || '-'
        },
        ChildCollectPemeriksaanFisikRespirasiKesulitanBernafas(data) {
            const ChildCollectPemeriksaanFisikRespirasiKesulitanBernafas ={
                '1': 'Tidak',
                '2': 'Ya',
            };
            return ChildCollectPemeriksaanFisikRespirasiKesulitanBernafas[data] || '-'
        },
        ChildCollectPemeriksaanFisikRespirasiBatuk(data) {
            const ChildCollectPemeriksaanFisikRespirasiBatuk ={
                '1': 'Tidak',
                '2': 'Ya',
                '3': 'Hemoptoe',
            };
            return ChildCollectPemeriksaanFisikRespirasiBatuk[data] || '-'
        },
        ChildCollectPemeriksaanFisikRespirasiJenisPernafasan(data) {
            const ChildCollectPemeriksaanFisikRespirasiJenisPernafasan ={
                '1': 'Pernafasan dada',
                '2': 'Pernafasan perut',
                '3': 'Menggunakan alat bantu nafas,sebutkan...',
            };
            return ChildCollectPemeriksaanFisikRespirasiJenisPernafasan[data] || '-'
        },
        //gastrointestinal
        ChildCollectPemeriksaanFisikGastrointestinalMulut(data) {
            const ChildCollectPemeriksaanFisikGastrointestinalMulut ={
                '1': 'Tidak ada kelainan',
                '2': 'Simetris',
                '3': 'Asimetris',
                '4': 'Bibir pucat',
                '5': 'Mukosa mulut kering',
                '6': 'Sariawan',
            };
            return ChildCollectPemeriksaanFisikGastrointestinalMulut[data] || '-'
        },
        ChildCollectPemeriksaanFisikGastrointestinalTenggorokan(data) {
            const ChildCollectPemeriksaanFisikGastrointestinalTenggorokan ={
                '1': 'Gangguan menelan',
                '2': 'Sakit menelan',
            };
            return ChildCollectPemeriksaanFisikGastrointestinalTenggorokan[data] || '-'
        },
        ChildCollectPemeriksaanFisikGastrointestinalGigi(data) {
            const ChildCollectPemeriksaanFisikGastrointestinalGigi ={
                '1': 'Tidak ada kelainan',
                '2': 'Caries',
                '3': 'Goyang',
                '4': 'Berlubang',
                '5': 'Gigi palsu',
                '6': 'Tambal',
            };
            return ChildCollectPemeriksaanFisikGastrointestinalGigi[data] || '-'
        },
        ChildCollectPemeriksaanFisikGastrointestinalAbdomen(data) {
            const ChildCollectPemeriksaanFisikGastrointestinalAbdomen ={
                '1': 'Supel',
                '2': 'Asites',
                '3': 'Tegang',
            };
            return ChildCollectPemeriksaanFisikGastrointestinalAbdomen[data] || '-'
        },
        ChildCollectPemeriksaanFisikGastrointestinalLidah(data) {
            const ChildCollectPemeriksaanFisikGastrointestinalLidah ={
                '1': 'Tidak ada kelainan',
                '2': 'Kotor',
                '3': 'Gerakan asimetris',
            };
            return ChildCollectPemeriksaanFisikGastrointestinalLidah[data] || '-'
        },
        ChildPemeriksaanFisik(data,type,field){
            switch(type){
                case 'neorologi' :
                    if(field == 'penglihatan') return this.ChildCollectPemeriksaanFisikNeorologiPenglihatan(data);
                    if(field == 'bicara') return this.ChildCollectPemeriksaanFisikNeorologiBicara(data);
                    if(field == 'kekuatan_otot') return this.ChildCollectPemeriksaanFisikNeorologiKekuatanOtot(data);
                    if(field == 'menggunakan_alat_bantu_penglihatan') return this.ChildCollectPemeriksaanFisikNeorologiMenggunakanAlatBantuPendengaranPenglihatan(data);
                    if(field == 'sensorik') return this.ChildCollectPemeriksaanFisikNeorologiSensorik(data);
                    if(field == 'menggunakan_alat_bantu_pendengaran') return this.ChildCollectPemeriksaanFisikNeorologiMenggunakanAlatBantuPendengaranPenglihatan(data);
                    if(field == 'pendengaran') return this.ChildCollectPemeriksaanFisikNeorologiPendengaran(data);
                    if(field == 'motorik') return this.ChildCollectPemeriksaanFisikNeorologiMotorik(data);
                    break;
                case 'integument' :
                    if(field == 'warna_kulit') return this.ChildCollectPemeriksaanFisikIntegumentWarnaKulit(data);
                    if(field == 'kriteria_resiko_dekubitus') return this.ChildCollectPemeriksaanFisikIntegumentKriteriaResikoDekubitus(data);
                    if(field == 'tugor') return this.ChildCollectPemeriksaanFisikIntegumentTugor(data);
                    if(field == 'kulit') return this.ChildCollectPemeriksaanFisikIntegumentKulit(data);
                    if(field == 'kulit') return this.ChildCollectPemeriksaanFisikIntegumentKulit(data);
                    break;
                case 'kardiovaskuler' :
                    if(field == 'warna_kulit') return this.ChildCollectPemeriksaanFisikKardiovaskulerWarnaKulit(data);
                    if(field == 'nyeri_dada') return this.ChildCollectPemeriksaanFisikKardiovaskulerNyeriDada(data);
                    if(field == 'denyut_nadi') return this.ChildCollectPemeriksaanFisikKardiovaskulerDenyutNadi(data);
                    if(field == 'pulasi') return this.ChildCollectPemeriksaanFisikKardiovaskulerPulasi(data);
                    if(field == 'sirkulasi') return this.ChildCollectPemeriksaanFisikKardiovaskulerSirkulasi(data);
                    break;
                case 'respirasi' :
                    if(field == 'pola_nafas') return this.ChildCollectPemeriksaanFisikRespirasiPolaNafas(data);
                    if(field == 'volume_pernafasan') return this.ChildCollectPemeriksaanFisikRespirasiVolumePernafasan(data);
                    if(field == 'irama_nafas') return this.ChildCollectPemeriksaanFisikRespirasiIramaNafas(data);
                    if(field == 'kesulitan_bernafas') return this.ChildCollectPemeriksaanFisikRespirasiKesulitanBernafas(data);
                    if(field == 'batuk') return this.ChildCollectPemeriksaanFisikRespirasiBatuk(data);
                    if(field == 'jenis_pernafasan') return this.ChildCollectPemeriksaanFisikRespirasiJenisPernafasan(data);
                    break;
                case 'gastrointestinal' :
                    if(field == 'mulut') return this.ChildCollectPemeriksaanFisikGastrointestinalMulut(data);
                    if(field == 'tenggorokan') return this.ChildCollectPemeriksaanFisikGastrointestinalTenggorokan(data);
                    if(field == 'gigi') return this.ChildCollectPemeriksaanFisikGastrointestinalGigi(data);
                    if(field == 'abdomen') return this.ChildCollectPemeriksaanFisikGastrointestinalAbdomen(data);
                    if(field == 'lidah') return this.ChildCollectPemeriksaanFisikGastrointestinalLidah(data);
                    break;
            }
        },
        //penilaian persistem
        //umum
        ChildCollectPenilaianPersistemUmumApakahAdaNyeri(data) {
            const ChildCollectPenilaianPersistemUmumApakahAdaNyeri ={
                '1': 'Tidak',
                '2': 'Ya (Lakukan pengkajian nyeri)',
            };
            return ChildCollectPenilaianPersistemUmumApakahAdaNyeri[data] || '-'
        },
        ChildCollectPenilaianPersistemUmumRiwayatMerokok(data) {
            const ChildCollectPenilaianPersistemUmumRiwayatMerokok ={
                '1': 'Tidak',
                '2': 'Ya',
            };
            return ChildCollectPenilaianPersistemUmumRiwayatMerokok[data] || '-'
        },
        ChildCollectPenilaianPersistemUmumRiwayatMinumanKeras(data) {
            const ChildCollectPenilaianPersistemUmumRiwayatMinumanKeras ={
                '1': 'Tidak',
                '2': 'Ya',
            };
            return ChildCollectPenilaianPersistemUmumRiwayatMinumanKeras[data] || '-'
        },
        ChildCollectPenilaianPersistemUmumPolaIstirahat(data) {
            const ChildCollectPenilaianPersistemUmumPolaIstirahat ={
                '1': 'Teratur',
                '2': 'Tidak',
            };
            return ChildCollectPenilaianPersistemUmumPolaIstirahat[data] || '-'
        },
        ChildCollectPenilaianPersistemUmumRiwayatObatPenenang(data) {
            const ChildCollectPenilaianPersistemUmumRiwayatObatPenenang ={
                '1': 'Tidak',
                '2': 'Ya',
            };
            return ChildCollectPenilaianPersistemUmumRiwayatObatPenenang[data] || '-'
        },
        //asesmen fungsi
        ChildCollectPenilaianPersistemAsesmenFungsiKemampuanMelakukanAktivitas(data) {
            const ChildCollectPenilaianPersistemAsesmenFungsiKemampuanMelakukanAktivitas ={
                '1': 'Mandiri',
                '2': 'Bantuan sebagian',
                '3': 'Duduk',
                '4': 'Bantuan mininal',
                '5': 'Ketergantungan total',
                '6': 'Berjalan',
            };
            return ChildCollectPenilaianPersistemAsesmenFungsiKemampuanMelakukanAktivitas[data] || '-'
        },
        ChildCollectPenilaianPersistemAsesmenFungsiAktivitas(data) {
            const ChildCollectPenilaianPersistemAsesmenFungsiAktivitas ={
                '1': 'Tirah baring',
                '2': 'Duduk',
                '3': 'Berjalan',
            };
            return ChildCollectPenilaianPersistemAsesmenFungsiAktivitas[data] || '-'
        },
        ChildCollectPenilaianPersistemAsesmenFungsiEkstremitasAtas(data) {
            const ChildCollectPenilaianPersistemAsesmenFungsiEkstremitasAtas ={
                '1': 'Tidak ada kesulitan',
                '2': 'Paralisis',
                '3': 'Defomitas',
                '4': 'Penurunan kekuatan range of motion',
                '5': 'Hilang keseimbangan',
                '6': 'Riwayat patah tulang',
                '7': 'Sering jatuh',
                '8': 'Lemah',
            };
            return ChildCollectPenilaianPersistemAsesmenFungsiEkstremitasAtas[data] || '-'
        },
        ChildCollectPenilaianPersistemAsesmenFungsiKemampuanMenggenggam(data) {
            const ChildCollectPenilaianPersistemAsesmenFungsiKemampuanMenggenggam ={
                '1': 'Tidak ada kesulitan',
                '2': 'Terkilir',
                '3': 'Lain-lain...',
                '4': 'Ada masalah...',
            };
            return ChildCollectPenilaianPersistemAsesmenFungsiKemampuanMenggenggam[data] || '-'
        },
        ChildCollectPenilaianPersistemAsesmenFungsiAlatAmbulasi(data) {
            const ChildCollectPenilaianPersistemAsesmenFungsiAlatAmbulasi ={
                '1': 'Walker',
                '2': 'Tongkat',
                '3': 'Kursi roda...',
            };
            return ChildCollectPenilaianPersistemAsesmenFungsiAlatAmbulasi[data] || '-'
        },
        //keadaan mental dan psikologis
        ChildCollectPenilaianPersistemKeadaanMentalPsikologisStatusMental(data) {
            const ChildCollectPenilaianPersistemKeadaanMentalPsikologisStatusMental ={
                '1': 'Normal',
                '2': 'Konstipasi',
                '3': 'Agltasi',
                '4': 'Koorperatif',
                '5': 'Letargi',
                '6': 'Marah',
                '7': 'Disorientasi',
                '8': 'Orang',
                '9': 'Tempat',
                '10': 'Waktu',
                '11': 'Kejang tipe',
            };
            return ChildCollectPenilaianPersistemKeadaanMentalPsikologisStatusMental[data] || '-'
        },
        ChildCollectPenilaianPersistemKeadaanMentalPsikologisStatusPsikologis(data) {
            const ChildCollectPenilaianPersistemKeadaanMentalPsikologisStatusPsikologis ={
                '1': 'Tenang',
                '2': 'Cemas',
                '3': 'Depresi',
                '4': 'Menyerang',
                '5': 'Hiperaktif',
                '6': 'Gangguan Sekitar',
                '7': 'Lain-lain...',
            };
            return ChildCollectPenilaianPersistemKeadaanMentalPsikologisStatusPsikologis[data] || '-'
        },
        //eliminasi
        ChildCollectPenilaianPersistemEliminasiBab(data) {
            const ChildCollectPenilaianPersistemEliminasiBab ={
                '1': 'Normal',
                '2': 'Konstipasi',
                '3': 'Melena',
                '4': 'Inkontinensia',
                '5': 'Ileustomy',
                '6': 'Colostomy',
                '7': 'Diare,frekuensi',
            };
            return ChildCollectPenilaianPersistemEliminasiBab[data] || '-'
        },
        ChildCollectPenilaianPersistemEliminasiBak(data) {
            const ChildCollectPenilaianPersistemEliminasiBak ={
                '1': 'Normal',
                '2': 'Henmaturi',
                '3': 'Nokturia',
                '4': 'Disuria',
                '5': 'Uretrostomy',
                '6': 'Urine menetes',
                '7': 'Inkontinensia Uri',
            };
            return ChildCollectPenilaianPersistemEliminasiBak[data] || '-'
        },
        //seksual
        ChildCollectPenilaianPersistemSeksualWanitaHaid(data) {
            const ChildCollectPenilaianPersistemSeksualWanitaHaid ={
                '1': 'Belum',
                '2': 'Sudah',
            };
            return ChildCollectPenilaianPersistemSeksualWanitaHaid[data] || '-'
        },
        ChildCollectPenilaianPersistemSeksualLakiLaki(data) {
            const ChildCollectPenilaianPersistemSeksualLakiLaki ={
                '1': 'Sirkumsisi',
                '2': 'Masalah lain',
            };
            return ChildCollectPenilaianPersistemSeksualLakiLaki[data] || '-'
        },
        ChildPenilaianPersistem(data,type,field) {
            switch(type){
                case 'penilaian_umum' :
                    if(field == 'apakah_ada_nyeri') return this.ChildCollectPenilaianPersistemUmumApakahAdaNyeri(data);
                    if(field == 'riwayat_merokok') return this.ChildCollectPenilaianPersistemUmumRiwayatMerokok(data);
                    if(field == 'riwayat_minuman_keras') return this.ChildCollectPenilaianPersistemUmumRiwayatMinumanKeras(data);
                    if(field == 'pola_istirahat') return this.ChildCollectPenilaianPersistemUmumPolaIstirahat(data);
                    if(field == 'riwayat_penggunaan_obat_penenang') return this.ChildCollectPenilaianPersistemUmumRiwayatObatPenenang(data);
                    break;
                case 'asesmen_fungsi' :
                    if(field == 'kemampuan_melakukan_aktivitas') return this.ChildCollectPenilaianPersistemAsesmenFungsiKemampuanMelakukanAktivitas(data);
                    if(field == 'aktivitas') return this.ChildCollectPenilaianPersistemAsesmenFungsiAktivitas(data);
                    if(field == 'ekstremitas_atas') return this.ChildCollectPenilaianPersistemAsesmenFungsiEkstremitasAtas(data);
                    if(field == 'kemampuan_menggenggam') return this.ChildCollectPenilaianPersistemAsesmenFungsiKemampuanMenggenggam(data);
                    if(field == 'alat_ambulasi') return this.ChildCollectPenilaianPersistemAsesmenFungsiAlatAmbulasi(data);
                    break;
                case 'keadaan_mental_psikologis' :
                    if(field == 'status_mental') return this.ChildCollectPenilaianPersistemKeadaanMentalPsikologisStatusMental(data);
                    if(field == 'status_psikologi') return this.ChildCollectPenilaianPersistemKeadaanMentalPsikologisStatusPsikologis(data);
                    break;
                case 'eliminasi' :
                    if(field == 'bab') return this.ChildCollectPenilaianPersistemEliminasiBab(data);
                    if(field == 'bak') return this.ChildCollectPenilaianPersistemEliminasiBak(data);
                    break;
                case 'seksual' :
                    if(field == 'wanita_haid') return this.ChildCollectPenilaianPersistemSeksualWanitaHaid(data);
                    if(field == 'laki_laki') return this.ChildCollectPenilaianPersistemSeksualLakiLaki(data);
                    break;
            }
        },
    },
    mounted() {
        window.addEventListener('keydown', this.handleKeyPress);
    },
    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },
    created () {
        if (this.$route.params.nurse_id) {
            this.id = this.$route.params.nurse_id
            this.getData(this.$route.params.nurse_id)
        }
    }
};

</script>

<style>
@import './../../../../@core/css/main/main.min.css';

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
<!-- <style>
@import './../../../../@core/css/main/main.min.css';

@media print {
    @page {
        size: A4;
        margin: 16mm;
    }
}
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

#canvas {
    /* width: 210mm; */
    width: 21.2cm !important;
    /* height: 29.7cm !important; */
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    padding: 20px 0;
}

.canvas-header {
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
}

.w-40 {
    width: 40%;
}

.canvas-footer {
    position: absolute;
    bottom: 0;
    /* right: 0; */
}
</style> -->
