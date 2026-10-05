<template>
    <div>
        <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0" style="margin-left:-16px; margin-right:-16px;">SDKLI SLKI SIKI</div>
        <div class="mt-20">
            <section ref="Diagnosis">
                <div class="c-form w-100">
                    <label>Diagnosis</label>
                    <b-form-select v-model="Form.Diagnosis">
                        <b-form-select-option value="D0001">D0001 Bersihan Jalan Tidak Efektif</b-form-select-option>
                        <b-form-select-option value="D0010">D0010 Risiko Gangguan Sirkulasi</b-form-select-option>
                        <b-form-select-option value="D0100">D0100 Resiko Distress Spiritual</b-form-select-option>
                        <b-form-select-option value="D0105">D0105 Resiko Disertas Spiritual</b-form-select-option>
                        <b-form-select-option value="D0101">D0101 Resiko Harga diri rendah kronis</b-form-select-option>
                        <b-form-select-option value="D0104">D0104 Sindrom Pasca Trauma</b-form-select-option>
                        <b-form-select-option value="D0103">D0103 Resiko Ketidakberdayaan</b-form-select-option>
                        <b-form-select-option value="D0102">D0102 Resiko Harga diri rendah Situasional</b-form-select-option>
                    </b-form-select>
                </div>
                <div v-if="Form.Diagnosis === 'D0001'" class="mt-12">
                    <label class="c-form-label">Berhubungan dengan</label>
                    <b-form-checkbox-group size="sm" stacked>
                        <div class="row">
                            <div class="col-lg-4">
                                <b-form-checkbox class="my-1" value="1">1. Spasme Jalan Napas</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="2">2. Hipersekresi jalan napas</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="3">3. Disfungsi Neouromuskuler</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="4">4. Benda asing dalam jalan napas</b-form-checkbox>
                            </div>
                            <div class="col-lg-4">
                                <b-form-checkbox class="my-1" value="5">5. Adanya jalan napas buatan</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="6">6. Sekresi yang tertahan</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="7">7. Hyperlesia Dinding Jalan Napas</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="8">8. Proses Infeksi</b-form-checkbox>
                            </div>
                            <div class="col-lg-4">
                                <b-form-checkbox class="my-1" value="9">9. Respon Alergi</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="10">10. Efek Agen Farmakologis  (Mis Anestesi)</b-form-checkbox>
                            </div>
                        </div>
                    </b-form-checkbox-group>
                </div>
                <div v-if="['D0100', 'D0101', 'D0102', 'D0103', 'D0104', 'D0105'].includes(Form.Diagnosis)" class="mt-12">
                    <label class="c-form-label">Berhubungan dengan</label>
                    <b-form-checkbox-group size="sm" stacked>
                        <div class="row">
                            <div class="col-lg-4">
                                <b-form-checkbox class="my-1" value="1">1. Perubahan Hidup</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="2">2. Perubahan Lingkungan</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="3">3. Bencana Alam</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="4">4. Sakit Kronis </b-form-checkbox>
                                <b-form-checkbox class="my-1" value="5">5. Sakit Fisik</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="6">6. Penyalahgunaan Zat</b-form-checkbox>
                            </div>
                            <div class="col-lg-4">
                                <b-form-checkbox class="my-1" value="7">7. Kecemasan Hidup</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="8">8. Perubahan dalam Ritual Agama</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="9">9. Perubahan dalam praktik spirirtual</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="10">10. Depresi</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="11">11. Ketidakmampuan memafkan</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="12"> 12. Kehilangan</b-form-checkbox>
                            </div>
                            <div class="col-lg-4">
                                <b-form-checkbox class="my-1" value="13">13. Harga diri rendah</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="14">14. Hubungan Buruk</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="15">15. Konflik Rasial</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="16">16. Berpisah dengan sistem Pendukung</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="17">17. Stres</b-form-checkbox>
                            </div>
                        </div>
                    </b-form-checkbox-group>
                </div>
            </section>
            <section ref="Luaran">
                <div v-if="Form.Diagnosis" class="c-form w-100 mt-16">
                    <label>Luaran</label>
                    <b-form-select v-model="Form.Luaran">
                        <b-form-select-option v-if="['D0001', 'D0010'].includes(Form.Diagnosis)" value="L0001">L0001 Bersihin Jalan Napas</b-form-select-option>
                        <b-form-select-option v-if="Form.Diagnosis === 'D0010'" value="L0002">L0002 Status Sirkulasi</b-form-select-option>
                        <b-form-select-option v-if="['D0100', 'D0105'].includes(Form.Diagnosis)" value="L09091">L09091 Status Spiritual</b-form-select-option>
                        <b-form-select-option v-if="['D0101', 'D0102'].includes(Form.Diagnosis)" value="L009069">L009069 Harga Diri</b-form-select-option>
                        <b-form-select-option v-if="['D0103', 'D0104'].includes(Form.Diagnosis)" value="L09092">L09092 Keberdayaan</b-form-select-option>
                    </b-form-select>
                </div>
                <div v-if="['L0001'].includes(Form.Luaran)" class="mt-12">
                    <label class="c-form-label">Dengan Kriteria Hasil</label>
                    <b-form-checkbox-group size="sm" stacked>
                        <div class="row">
                            <div class="col-lg-4">
                                <b-form-checkbox class="my-1" value="1">1. Kekuatan Nadi Meningkat</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="2">2. Output Urin Meningkat</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="3">3. Saturasi Meningkat</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="4">4. P02 Meningkat</b-form-checkbox>
                            </div>
                            <div class="col-lg-4">
                                <b-form-checkbox class="my-1" value="5">5. Pucat Menurun</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="6">6. Akral Dingin Menurun</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="7">7. PC02 Menurun</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="8">8. Tekanan Darah Membaik</b-form-checkbox>
                            </div>
                            <div class="col-lg-4">
                                <b-form-checkbox class="my-1" value="9">9. Tekanan Nadi Membaik</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="10">10. Tekanan Arteri Rata rata Membaik</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="11">11. Pengisian Kapiler Membaik</b-form-checkbox>
                            </div>
                        </div>
                    </b-form-checkbox-group>
                </div>
                <div v-if="['L09092'].includes(Form.Luaran)" class="mt-12">
                    <label class="c-form-label">Dengan Kriteria Hasil</label>
                    <b-form-checkbox-group size="sm" stacked>
                        <div class="row">
                            <div class="col-lg-4">
                                <b-form-checkbox class="my-1" value="1">1. Ungkapan Makna dan tujuan hidup meningkat</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="2">2. Ungkapan kepuasan terhadap hidup   meningkat</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="3">3. Ungkapan Rasa keberdayaan meningkat perilaku marah pada Tuhan menurun</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="4">4. Ungkapan Perasaan yang meningkat</b-form-checkbox>
                            </div>
                            <div class="col-lg-4">
                                <b-form-checkbox class="my-1" value="5">5. Ungkapan Percaya kepada orang lain meningkat</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="6">6. Ungkapan Rasa Bersalah Menurun</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="7">7. Ungkapan Perasaan Asing Menurun</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="8">8. Ungkapan Rasa diabaikan Menurun</b-form-checkbox>
                            </div>
                        </div>
                    </b-form-checkbox-group>
                </div>
                <div v-if="['L09091'].includes(Form.Luaran) && ['D0100'].includes(Form.Diagnosis)" class="mt-12">
                    <label class="c-form-label">Dengan Kriteria Hasil</label>
                    <b-form-checkbox-group size="sm" stacked>
                        <div class="row">
                            <div class="col-lg-4">
                                <b-form-checkbox class="my-1" value="1">1. Ungkapan Makna dan tujuan hidup meningkat</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="2">2. Ungkapan kepuasan terhadap hidup   meningkat</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="3">3. Ungkapan Rasa keberdayaan meningkat perilaku marah pada Tuhan menurun</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="4">4. Ungkapan Perasaan yang meningkat</b-form-checkbox>
                            </div>
                            <div class="col-lg-4">
                                <b-form-checkbox class="my-1" value="5">5. Ungkapan Percaya kepada orang lain meningkat</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="6">6. Ungkapan Rasa Bersalah Menurun</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="7">7. Ungkapan Perasaan Asing Menurun</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="8">8. Ungkapan Rasa diabaikan Menurun</b-form-checkbox>
                            </div>
                        </div>
                    </b-form-checkbox-group>
                </div>
                <div v-if="['L09091'].includes(Form.Luaran) && ['D0105'].includes(Form.Diagnosis)" class="mt-12">
                    <label class="c-form-label">Dengan Kriteria Hasil</label>
                    <b-form-checkbox-group size="sm" stacked>
                        <div class="row">
                            <div class="col-lg-4">
                                <b-form-checkbox class="my-1" value="1">1. Ungkapan Makna dan tujuan hidup meningkat</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="2">2. Ungkapan Kepuasan Terhadap Makna hidup meningkat</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="3">3. Ungkapan rasa keberdayaan meningkat perilaku marah pada Tuhan menurun</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="4">4. Takikardia Menurun</b-form-checkbox>
                            </div>
                            <div class="col-lg-4">
                                <b-form-checkbox class="my-1" value="5">5. Gambaran EKG Aritma Menurun</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="6">6. Lelah Menurun</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="7">7. Edema Menurun</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="8">8. Distensiavena Jugularis Menurun</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="9">9. Dispnea Menurun</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="10">10. Oligoria Menurun</b-form-checkbox>
                            </div>
                            <div class="col-lg-4">
                                <b-form-checkbox class="my-1" value="11">11. SIanosi Menurun</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="12">12. Ortopna Menurun</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="13">13. Batuk Menurun</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="14">14. Suara Jantung S3, S4 Menrun</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="15">15. Berat Badan Membaik</b-form-checkbox>
                            </div>
                        </div>
                    </b-form-checkbox-group>
                </div>
                <div v-if="['L009069'].includes(Form.Luaran)" class="mt-12">
                    <label class="c-form-label">Dengan Kriteria Hasil</label>
                    <b-form-checkbox-group size="sm" stacked>
                        <div class="row">
                            <div class="col-lg-4">
                                <b-form-checkbox class="my-1" value="1">1. Ungkapan Makna dan tujuan hidup meningkat</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="2">2. Ungkapan Kepuasan Terhadap Makna hidup meningkat</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="3">3. Ungkapan rasa keberdayaan meningkat perilaku marah pada Tuhan menurun</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="4">4. Takikardia Menurun</b-form-checkbox>
                            </div>
                            <div class="col-lg-4">
                                <b-form-checkbox class="my-1" value="5">5. Gambaran EKG Aritma Menurun</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="6">6. Lelah Menurun</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="7">7. Edema Menurun</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="8">8. Distensiavena Jugularis Menurun</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="9">9. Dispnea Menurun</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="10">10. Oligoria Menurun</b-form-checkbox>
                            </div>
                            <div class="col-lg-4">
                                <b-form-checkbox class="my-1" value="11">11. SIanosi Menurun</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="12">12. Ortopna Menurun</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="13">13. Batuk Menurun</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="14">14. Suara Jantung S3, S4 Menrun</b-form-checkbox>
                                <b-form-checkbox class="my-1" value="15">15. Berat Badan Membaik</b-form-checkbox>
                            </div>
                        </div>
                    </b-form-checkbox-group>
                </div>
            </section>
            <section ref="Intervensi">
                <div v-if="Form.Luaran && Form.Luaran != 'L0001' && Form.Luaran != 'L0002'" class="c-form w-100 mt-16">
                    <label>Intervensi</label>
                    <b-form-select v-model="Form.Intervensi">
                        <b-form-select-option v-if="Form.Diagnosis === 'D0100' && Form.Luaran === 'L09091'" value="L09269">L09269 Dukungan Perkembangan Spiritual</b-form-select-option>
                        <b-form-select-option v-if="Form.Diagnosis === 'D0105' && Form.Luaran === 'L09091'" value="L02075">L02075 Perawatan Jantung</b-form-select-option>
                        <b-form-select-option v-if="Form.Diagnosis === 'D0101' && Form.Luaran === 'L009069'" value="L9309">L9309 Promosi Harga Diri</b-form-select-option>
                        <b-form-select-option v-if="['D0103', 'D0104'].includes(Form.Diagnosis) && Form.Luaran === 'L09092'" value="L09269">L09269 Promosi Harapan</b-form-select-option>
                        <b-form-select-option v-if="Form.Diagnosis === 'D0102' && Form.Luaran === 'L009069'" value="L09312">L09312 Promosi Koping</b-form-select-option>
                    </b-form-select>
                </div>
                <div v-if="['L02075', 'L9309', 'L09312'].includes(Form.Intervensi)" class="mt-12">
                    <label class="text-semi-14 mb-0">Observasi</label>
                    <b-form-checkbox-group size="sm" stacked>
                        <div class="row">
                            <div class="col-lg-4">
                                <b-form-checkbox class="my-1 my-lg-2" value="1.1">1.1 Observasi Tanda / Gejala Primer PenurunanCurah Jantung (Meliputi Dyspnea, Kelelahan, Edema , Ortopnea  ParoxsmalNoctrunal Dyspnea Peningkatan CVP</b-form-checkbox>
                                <b-form-checkbox class="my-1 my-lg-2" value="1.2">1.2. Identifikasi Tanda / Gejala SekunderPenurunan Curah Jantung (Meliputi Berat Badan, hepatomegall , Distensi Vega Julgaris, Palpitasi, ROnkhoni Basah, Oliguria Batuk , Kulit Pucat)</b-form-checkbox>
                                <b-form-checkbox class="my-1 my-lg-2" value="1.3">1.3. Monitor Tekanan Darah (Termasuk Tekanan Darah Ortostasik Jika Perlu )</b-form-checkbox>
                                <b-form-checkbox class="my-1 my-lg-2" value="1.4">1.4 Monitor Intake dan Output Cairan</b-form-checkbox>
                                <b-form-checkbox class="my-1 my-lg-2" value="1.5">1.5 Monitor Berat Badan Setiap Hari Pada waktu yang sama</b-form-checkbox>
                                <b-form-checkbox class="my-1 my-lg-2" value="1.6">1.6  Monitor Saturasi Oksigen</b-form-checkbox>
                                <b-form-checkbox class="my-1 my-lg-2" value="1.7">1.7 Monitor Keluhan Nyeri Dada (Mis Intensitas, Lokasi , radiasai, Durasi , Previtasi, Lokasi , Radiasai, Durasi, Previtas yang mengulang Nteri)</b-form-checkbox>
                                <b-form-checkbox class="my-1 my-lg-2" value="1.8">1.8 Monitor EKG Sadaran</b-form-checkbox>
                                <b-form-checkbox class="my-1 my-lg-2" value="1.9">1.9 Monitoring Aritma (Kelainan irama)</b-form-checkbox>
                            </div>
                            <div class="col-lg-4">
                                <b-form-checkbox class="my-1 my-lg-2" value="1.14">1.14 Posisikan Pasien Semi flower atau flower dengan kaki kebawah atau posisi nyaman</b-form-checkbox>
                                <b-form-checkbox class="my-1 my-lg-2" value="1.15">1.15 Berikan  Diet Jantung  yang sesuai (IMS, Batas Asupan Kafein, Natrium , , Kolesterol dan makanan tinggi lemak)</b-form-checkbox>
                                <b-form-checkbox class="my-1 my-lg-2" value="1.16">1.16 Gunakan Stoking elastis atau penaumetic intermiten, Sesuai indikasi</b-form-checkbox>
                                <b-form-checkbox class="my-1 my-lg-2" value="1.19">1.19 Beeikan dukungan emosional dan spriritual</b-form-checkbox>
                                <b-form-checkbox class="my-1 my-lg-2" value="1.20">1.20 Berikan oksigen untuk mempertahan  saturasi oksigen > 94%</b-form-checkbox>
                            </div>
                            <div class="col-lg-4">
                                <b-form-checkbox class="my-1 my-lg-2" value="1.21">1.21 Anjurkan aktivitas fisik sesuai toleransi</b-form-checkbox>
                                <b-form-checkbox class="my-1 my-lg-2" value="1.22">1.22 Anjurkan aktivitas fisik secara bertahap</b-form-checkbox>
                                <b-form-checkbox class="my-1 my-lg-2" value="1.23">1.23 Anjurkan berhenti merokok</b-form-checkbox>
                                <b-form-checkbox class="my-1 my-lg-2" value="1.24">1.24 Anjurkan pasien dan keluarga mengukur berat badan harian</b-form-checkbox>
                                <b-form-checkbox class="my-1 my-lg-2" value="1.25">1.25 Anjurkan pasien dan keluarga mengukur intake dan output cairan harian</b-form-checkbox>
                            </div>
                        </div>
                    </b-form-checkbox-group>
                </div>
                <div v-if="['L09269', 'L09092'].includes(Form.Intervensi)" class="mt-12">
                    <div class="row">
                        <div class="col-lg-4">
                            <label class="text-semi-14 mb-0">Observasi</label>
                        </div>
                        <div class="col-lg-4">
                            <label class="text-semi-14 mb-0">Teraupeutik</label>
                            <b-form-checkbox-group size="sm" stacked>
                                <b-form-checkbox class="my-1 my-lg-2" value="1.1">1.1 DIsediakan Lingkungan yang tenang untuk refleksi diri</b-form-checkbox>
                                <b-form-checkbox class="my-1 my-lg-2" value="1.2">1.2 Fasilitas Mengidentifikasi Masalah Spiritual</b-form-checkbox>
                                <b-form-checkbox class="my-1 my-lg-2" value="1.3">1.3 Fasilitas Mengidentifikasi Hambatan dalam mengenalkan diri</b-form-checkbox>
                                <b-form-checkbox class="my-1 my-lg-2" value="1.4">1.4 Fasilitas Mengeksplorasi keyakinan, terkait  pemulihan Tumbuh, PIkiran  dan JIwa</b-form-checkbox>
                                <b-form-checkbox class="my-1 my-lg-2" value="1.5">1.5 Fasilital Hubungan Persahabatan</b-form-checkbox>
                            </b-form-checkbox-group>
                        </div>
                        <div class="col-lg-4">
                            <label class="text-semi-14 mb-0">Edukasi</label>
                            <b-form-checkbox-group size="sm" stacked>
                                <b-form-checkbox class="my-1 my-lg-2" value="1.6">1.6 Anjurkan Membuat Komitmen Spiritual Berdasarkan Keyakinan dan NIlai</b-form-checkbox>
                                <b-form-checkbox class="my-1 my-lg-2" value="1.7">1.7 Anjuran berpatisipasi dalam kegiatan Ibadah dan Mediasi</b-form-checkbox>
                            </b-form-checkbox-group>
                        </div>
                    </div>
                    <div class="row mt-16">
                        <div class="col-lg-4">
                            <label class="text-semi-14 mb-0">Kolaborasi</label>
                            <b-form-checkbox-group size="sm" stacked>
                                <b-form-checkbox class="my-1 my-lg-2" value="1.8">1.8 Rujuk Pada Pemuka Agama, JIka Perlu</b-form-checkbox>
                                <b-form-checkbox class="my-1 my-lg-2" value="1.9">1.9 Rujuk Pada Kelompok Pendukung, Swabantu atau program spiritual Jika perlu</b-form-checkbox>
                            </b-form-checkbox-group>
                        </div>
                    </div>
                </div>
            </section>
        </div> <!-- /form -->
        <div class="d-flex flex-column mt-40 text-center">
            <div class="col-lg-10 mx-auto">
                <p class="mb-0 mt-8 text-medium-16">PASTIKAN INPUTAN SDK SLKI SIKI  SUDAH BENAR, KESALAHAN INPUTAN DAPAT MEMPENGARUHI HASIL PENGAMATAN MAUPUN TINDAKAN YANG DIBERIKAN KEPADA PASIEN</p>
                <p class="mb-0 mt-8 text-medium-16">10 FEBRUARI 2023</p>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            Form: {
                Diagnosis: '',
                Luaran: '',
                Intervensi: '',
            }
        }
    },
}
</script>
<style scoped>
@import '../../../../../@core/css/utility.css';
</style>