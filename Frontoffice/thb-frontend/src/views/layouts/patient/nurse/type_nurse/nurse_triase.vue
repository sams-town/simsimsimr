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
                    <v-select v-model="form.eye_response" aria-readonly="true" :options="OptionsEye" label="title"
                        placeholder="-- Pilih --"></v-select>
                </div>
                <div class="col-lg-3 mt-12 mt-lg-0">
                    <label class="c-form-label">Verbal (Respon verbal)</label>
                    <v-select v-model="form.verbal_response" aria-readonly="true" :options="OptionsVerbal" label="title"
                        placeholder="-- Pilih --"></v-select>
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
                        <b-form-radio value="1">Tidak</b-form-radio>
                        <b-form-radio value="2">Ya</b-form-radio>
                    </b-form-radio-group>
                </div>
                <div class="col-lg-3 mt-12">
                    <label class="c-form-label">Deformitas</label>
                    <b-form-radio-group v-model="form.deformity_type_id" size="sm">
                        <b-form-radio value="1">Tidak</b-form-radio>
                        <b-form-radio value="2">Ya</b-form-radio>
                    </b-form-radio-group>
                    <b-form-input v-if="form.deformity_type_id == '2'" v-model="form.deformity" />
                </div>
                <div class="col-lg-3 mt-12">
                    <label class="c-form-label">Penetrasi</label>
                    <b-form-radio-group v-model="form.penetration_type_id" size="sm">
                        <b-form-radio value="1">Tidak</b-form-radio>
                        <b-form-radio value="2">Ya</b-form-radio>
                    </b-form-radio-group>
                    <b-form-input v-if="form.penetration_type_id == '2'" v-model="form.penetration" />
                </div>
                <div class="col-lg-3 mt-12">
                    <label class="c-form-label">Contusio</label>
                    <b-form-radio-group v-model="form.contusion_type_id" size="sm">
                        <b-form-radio value="1">Tidak</b-form-radio>
                        <b-form-radio value="2">Ya</b-form-radio>
                    </b-form-radio-group>
                    <b-form-input v-if="form.contusion_type_id == '2'" v-model="form.contusion" />
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 mt-12">
                    <label class="c-form-label">Laserasi</label>
                    <b-form-radio-group v-model="form.laceration_type_id" size="sm">
                        <b-form-radio value="1">Tidak</b-form-radio>
                        <b-form-radio value="2">Ya</b-form-radio>
                    </b-form-radio-group>
                    <b-form-input v-if="form.laceration_type_id == '2'" v-model="form.laceration" />
                </div>
                <div class="col-lg-3 mt-12">
                    <label class="c-form-label">Abrasi</label>
                    <b-form-radio-group v-model="form.abrasion_type_id" size="sm">
                        <b-form-radio value="1">Tidak</b-form-radio>
                        <b-form-radio value="2">Ya</b-form-radio>
                    </b-form-radio-group>
                    <b-form-input v-if="form.abrasion_type_id == '2'" v-model="form.abrasion" />
                </div>
                <div class="col-lg-3 mt-12">
                    <label class="c-form-label">Kedalaman Luka</label>
                    <b-form-radio-group v-model="form.wound_depth_type_id" size="sm">
                        <b-form-radio value="1">Tidak</b-form-radio>
                        <b-form-radio value="2">Ya</b-form-radio>
                    </b-form-radio-group>
                    <b-form-input v-if="form.wound_depth_type_id == '2'" v-model="form.wound_depth" />
                </div>
                <div class="col-lg-3 mt-12">
                    <label class="c-form-label">Lainnya</label>
                    <b-form-input v-model="form.anythingelse" />
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
        <!-- <section class="mt-24">
            <ModernLabel label="Hasil Pemeriksaan Triase: Resusitasi" />
            <b-form-input v-model="form.triage_result" :disabled="form.dead_on_arrival_arry.length == 0"
                class="mt-16"></b-form-input>
            <div class="mt-12">
                <label class="c-form-label">Kriteria Isolasi</label>
                <b-form-checkbox-group v-model="form.isolation_criteria_arry" stacked size="sm">
                    <b-form-checkbox value="1">Batuk &gt; 2 Minggu</b-form-checkbox>
                    <b-form-checkbox value="2">Demam</b-form-checkbox>
                    <b-form-checkbox value="3">Tidak Ada Keluhan</b-form-checkbox>
                    <b-form-checkbox value="4">Traveling Dari Daerah Endemij, Sebutkan:</b-form-checkbox>
                </b-form-checkbox-group>
                <b-form-input v-if="form.isolation_criteria_arry.indexOf('4') !== -1" v-model="form.isolation_criteria"
                    class="mt-16"></b-form-input>
            </div>
        </section> -->
    </div>
</template>
<script>
import { mapState } from 'vuex'
import vSelect from 'vue-select'
import ModernLabel from '@/@core/components/label/ModernLabel.vue';

export default {
    name: 'Triase',
    components: { vSelect, ModernLabel },
    data() {
        return {
            OptionsEye: [ // masih dummy
                { title: 'Spontan', value: '1' },
                { title: 'Sedikit rangsang', value: '2' },
                { title: 'Rangsang nyeri', value: '3' },
                { title: 'Tidak ada respon', value: '4' },
            ],
            OptionsVerbal: [ // masih dummy
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

            // # TYPE_NURSE_ID 3 = TRIASE
            form: {
                case_type_id: '',
                eye_response: '',
                verbal_response: '',
                motor_response: '',
                triage_systolic_blood_pressure: '',
                triage_diastolic_blood_pressure: '',
                capillary_refill_time: '',
                triage_breath_frequency: '',
                triage_pulse_frequency: '',
                triage_temperature: '',
                blood_oxygen: '',
                deformity_type_id: '',
                deformity: '',
                contusion_type_id: '',
                contusion: '',
                abrasion_type_id: '',
                abration: '',
                penetration_type_id: '',
                penetration: '',
                laceration_type_id: '',
                laceration: '',
                wound_depth_type_id: '',
                wound_depth: '',
                anythingelse: '',
                dead_on_arrival_arry: [],
                doa_time: '',
                die_time: '',
                airway_type_id: '',
                respiratory_arry: [],
                circulation_arry: [],
                consciousness_arry: [],
                triage_result: '',
                isolation_criteria_arry: [],
                isolation_criteria: '',
                exposure_arry: [],
                psychology_arry: [],
            }
        }
    },
    methods: {
        resetForm() {
            this.form.case_type_id = ''
            this.form.eye_response = ''
            this.form.verbal_response = ''
            this.form.motor_response = ''
            this.form.triage_systolic_blood_pressure = ''
            this.form.triage_diastolic_blood_pressure = ''
            this.form.capillary_refill_time = ''
            this.form.triage_breath_frequency = ''
            this.form.triage_pulse_frequency = ''
            this.form.triage_temperature = ''
            this.form.blood_oxygen = ''
            this.form.deformity_type_id = ''
            this.form.deformity = ''
            this.form.contusion_type_id = ''
            this.form.contusion = ''
            this.form.abrasion_type_id = ''
            this.form.abration = ''
            this.form.penetration_type_id = ''
            this.form.penetration = ''
            this.form.laceration_type_id = ''
            this.form.laceration = ''
            this.form.wound_depth_type_id = ''
            this.form.wound_depth = ''
            this.form.anythingelse = ''
            this.form.dead_on_arrival_arry = []
            this.form.doa_time = ''
            this.form.die_time = ''
            this.form.airway_type_id = ''
            this.form.respiratory_arry = []
            this.form.circulation_arry = []
            this.form.consciousness_arry = []
            this.form.triage_result = ''
            this.form.isolation_criteria_arry = []
            this.form.isolation_criteria = ''
            this.form.exposure_arry = []
            this.form.psychology_arry = []
        },

    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>