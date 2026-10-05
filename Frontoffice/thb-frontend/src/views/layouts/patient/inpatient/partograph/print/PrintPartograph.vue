<template>
  <div>
    <b-container fluid="xl" class="main-content">
      <div class="d-flex">
        <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
        <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
      </div>
      <div class="canvas-wrapper">
        <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
          <!-- Header logos -->
          <div class="row align-items-center mb-4">
            <div class="col-2">
              <img src="../../../../../../@core/assets/images/logo-thb.jpg" width="200%" alt="rs-logo">
            </div>
            <div class="col text-center text-bold-28"></div>
            <div class="col-2">
              <!-- <img src="../../../../../../@core/assets/images/lafki-logo.png" width="85%" alt="lafki-logo" class="float-right"> -->
            </div>
            <div class="col-2">
              <!-- <img src="../../../../../../@core/assets/images/apexa.png" width="80%" alt="-logo" class="float-right"> -->
            </div>
          </div>
          <div class="print-header mb-4">
            <div class="print-columns">
              <div class="print-col">
                <div class="print-row">
                  <span class="print-label">Nama:</span>
                  <span class="print-value">{{ regData.name || '-' }}</span>
                </div>
                <div class="print-row">
                  <span class="print-label">Tanggal & Jam Mulai:</span>
                  <span class="print-value">{{ localDateTime(partograph.datetime_start) }}</span>
                </div>
                <div class="print-row">
                  <span class="print-label">Pecah Ketuban Sejak:</span>
                  <span class="print-value">{{ localDateTime(partograph.water_breaks_since) }}</span>
                </div>
              </div>
              <div class="print-col">
                <div class="print-row">
                  <span class="print-label">Tanggal Lahir:</span>
                  <span class="print-value">{{ localDate(regData.birth_date) || '-' }}</span>
                </div>
                <div class="print-row">
                  <span class="print-label">Dokter:</span>
                  <span class="print-value">{{ getDoctorName(partograph.doctor_id) }}</span>
                </div>
                <div class="print-row">
                  <span class="print-label">Mules Sejak:</span>
                  <span class="print-value">{{ localDateTime(partograph.stomachache_since) }}</span>
                </div>
              </div>
              <div class="print-col">
                <div class="print-row">
                  <span class="print-label">No. Rekam Medik:</span>
                  <span class="print-value">{{ regData.norm || '-' }}</span>
                </div>
                <div class="print-row">
                  <span class="print-label">Bidan:</span>
                  <span class="print-value">{{ getMidwifeName(partograph.midwife_id) }}</span>
                </div>
                <div class="print-row">
                  <span class="print-label">Gravida / Para / Abortus:</span>
                  <span class="print-value">{{ partograph.gravida }} / {{ partograph.para }} / {{ partograph.abortus }}</span>
                </div>
              </div>
            </div>
          </div>
        <div class="mt-4">
          <h4 class="mb-3 font-weight-bold" style="color: #222;">Partograf</h4>
        </div>
        <div class="print-chart-section mb-4">
          <h5 class="mb-2">Denyut Jantung Janin</h5>
          <div class="chart-container">
            <LineChart :chartData="getFetalHeartRateChartData()" :height="260" :width="600" />
          </div>
        </div>

        <div class="print-chart-section mb-4">
          <h5 class="mb-2">Air Ketuban & Penyusupan</h5>
          <div v-if="partograph.id" class="chart-container">
            <TableFluidInfiltration
              :partographId="partograph.id"
              :labels="filteredLabels.fluidInfiltration"
              :isEditing="false"
              :height="260" :width="600"
            />
          </div>
        </div>

        <div class="print-chart-section mb-4">
          <h5 class="mb-2">Pembukaan Serviks</h5>
          <div v-if="partograph.id" class="chart-container">
            <LineChartServiks
              :chartData="getCervicalDilationChartData()"
              :height="260" :width="600"
            />
          </div>
        </div>

        <div class="print-chart-section mb-4">
          <h5 class="mb-2">Kontraksi</h5>
          <div v-if="partograph.id" class="chart-container">
            <BarChartKontraksi class="mt-24 w-100"
                :partographId="partograph.id"
                :datetime-start="partograph.datetime_start"
                @labels-updated="labels = $event"
                :height="260" :width="600"
            />
          </div>
        </div>

        <div class="print-chart-section mb-4">
          <h5 class="mb-2">Oksitosin</h5>
          <div v-if="partograph.id" class="chart-container">
            <TableOxytocin
                :labels="filteredLabels.oxytocin"
                :oxytocin="partograph.oxytocin"
                :oxytocinDripRate="partograph.oxytocin_drip_rate"
                :isEditing="false"
                :height="260" :width="600"
            />
          </div>
        </div>

        <div class="print-chart-section mb-4">
          <h5 class="mb-2">Obat & Cairan IV</h5>
          <div v-if="partograph.id" class="chart-container">
            <TableIVFluidsMeds
            :labels="filteredLabels.ivFluidsMeds"
            :IVFluidsMeds="partograph.iv_fluids_meds"
            :isEditing="false"
            :height="260" :width="600"
            />
          </div>
        </div>

        <div class="print-chart-section mb-4">
          <h5 class="mb-2">Tekanan Darah & Nadi</h5>
          <div v-if="partograph.id" class="chart-container">
            <LineChartTensiNadi
              :chartData="getTensiNadiChartData()"
              :height="260" :width="600"
            />
          </div>
        </div>

        <div class="print-chart-section mb-4">
          <h5 class="mb-2">Suhu Badan</h5>
          <div v-if="partograph.id" class="chart-container">
            <TableTemperature
              :labels="filteredLabels.temperature"
              :temperature="partograph.temperature"
              :isEditing="false"
            />
          </div>
        </div>

        <div class="print-chart-section mb-4">
          <h5 class="mb-2">Urin</h5>
          <div v-if="partograph.id" class="chart-container">
          <TableUrine
            :labels="filteredLabels.urine"
            :urine="partograph.urine"
            :isEditing="false"
          />
          </div>
        </div>

          <div class="mt-6">
            <h4 class="mb-3 font-weight-bold" style="color: #222;">Catatan Persalinan</h4>
            <div class="print-section mb-4" v-if="birthNotes && birthNotes.id">
              <table class="table table-bordered table-sm" style="width: 100%; font-size: 13px;">
                <tbody>
                  <tr>
                    <th style="width: 30%;">Tanggal</th>
                    <td>{{ localDate(birthNotes.date_birth) || '-' }}</td>
                  </tr>
                  <tr>
                    <th>Tempat Persalinan</th>
                    <td>
                      {{
                        ([
                          { value: 1, text: 'Rumah Ibu' },
                          { value: 2, text: 'Polindes' },
                          { value: 3, text: 'Klinik Swasta' },
                          { value: 4, text: 'Puskesmas' },
                          { value: 5, text: 'Rumah Sakit' },
                          { value: 6, text: 'Lainnya' }
                        ].find(opt => opt.value == birthNotes.birthing_place_type_id)?.text) || '-'
                      }}
                      <span v-if="birthNotes.birthing_place_type_id == 6 && birthNotes.birthing_place_other">
                        ({{ birthNotes.birthing_place_other }})
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <th>Alamat Tempat Persalinan</th>
                    <td>{{ birthNotes.birthing_place_address || '-' }}</td>
                  </tr>
                  <tr>
                    <th>Rujuk</th>
                    <td>
                      {{ birthNotes.referral_type_id == 1 ? 'Ya' : (birthNotes.referral_type_id == 2 ? 'Tidak' : '-') }}
                      <template v-if="birthNotes.referral_type_id == 1">
                        <div class="mt-1">
                          <strong>Kala:</strong>
                          {{
                            ([
                              { value: 1, text: 'I' },
                              { value: 2, text: 'II' },
                              { value: 3, text: 'III' },
                              { value: 4, text: 'IV' }
                            ].find(opt => opt.value == birthNotes.referral_kala)?.text) || '-'
                          }}
                        </div>
                        <div><strong>Alasan:</strong> {{ birthNotes.referral_reason || '-' }}</div>
                        <div><strong>Tempat Rujukan:</strong> {{ birthNotes.referral_place || '-' }}</div>
                        <div>
                          <strong>Pendamping:</strong>
                          {{
                            birthNotes.referral_companion_type_id == 2
                              ? (birthNotes.referral_companion_arry && birthNotes.referral_companion_arry.length
                                  ? birthNotes.referral_companion_arry.map(val => companionOptions.find(opt => opt.value == val)?.text).filter(Boolean).join(', ')
                                  : 'Ada')
                              : (birthNotes.referral_companion_type_id == 1 ? 'Tidak Ada' : '-')
                          }}
                        </div>
                      </template>
                    </td>
                  </tr>
                  <tr>
                    <th>Masalah dalam kehamilan/persalinan ini</th>
                    <td>
                      <span v-if="birthNotes.problem_arry && birthNotes.problem_arry.length">
                        {{
                          birthNotes.problem_arry
                            .map(val => ({
                              1: 'Gawat darurat',
                              2: 'Perdarahan',
                              3: 'HDK',
                              4: 'Infeksi',
                              5: 'PMTCT'
                            }[val]))
                            .filter(Boolean)
                            .join(', ')
                        }}
                      </span>
                      <span v-else>-</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- KALA I -->
            <div class="mb-4" v-if="birthRecordKala">
              <h5 class="mb-3" style="color: #222;">Kala I</h5>
              <table class="table table-bordered table-sm" style="width: 100%; font-size: 13px;">
                <tbody>
                  <tr>
                    <th style="width: 30%;">Temuan pada fase laten</th>
                    <td>{{ birthRecordKala.kala_one_latent_phase || '-' }}</td>
                  </tr>
                  <tr>
                    <th>Perlu Intervensi</th>
                    <td>
                      {{
                        birthRecordKala.kala_one_intervention_type_id === 2
                          ? 'Ya'
                          : (birthRecordKala.kala_one_intervention_type_id === 1 ? 'Tidak' : '-')
                      }}
                    </td>
                  </tr>
                  <tr>
                    <th>Grafis dilatasi melewati garis waspada</th>
                    <td>
                      {{
                        birthRecordKala.kala_one_alert_line_type_id === 2
                          ? 'Ya'
                          : (birthRecordKala.kala_one_alert_line_type_id === 1 ? 'Tidak' : '-')
                      }}
                    </td>
                  </tr>
                  <tr>
                    <th>Masalah lain yang ditemukan</th>
                    <td>{{ birthRecordKala.kala_one_problem || '-' }}</td>
                  </tr>
                  <tr>
                    <th>Penatalaksanaan masalah</th>
                    <td>{{ birthRecordKala.kala_one_problem_management || '-' }}</td>
                  </tr>
                  <tr>
                    <th>Hasil penatalaksanaan masalah</th>
                    <td>{{ birthRecordKala.kala_one_result || '-' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- KALA II -->
            <div class="mb-4" v-if="birthRecordKala">
              <h5 class="mb-3" style="color: #222;">Kala II</h5>
              <table class="table table-bordered table-sm" style="width: 100%; font-size: 13px;">
                <tbody>
                  <tr>
                    <th style="width: 30%;">Episiotomi</th>
                    <td>
                      {{
                        birthRecordKala.kala_two_episiotomy_type_id === 2
                          ? 'Ya'
                          : (birthRecordKala.kala_two_episiotomy_type_id === 1 ? 'Tidak' : '-')
                      }}
                      <div v-if="birthRecordKala.kala_two_episiotomy">
                        <strong>Indikasi:</strong> {{ birthRecordKala.kala_two_episiotomy }}
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>Gawat Janin</th>
                    <td>
                      {{
                        birthRecordKala.kala_two_fetal_distress_type_id === 2
                          ? 'Ya'
                          : (birthRecordKala.kala_two_fetal_distress_type_id === 1 ? 'Tidak' : '-')
                      }}
                      <div v-if="birthRecordKala.kala_two_fetal_distress">
                        <strong>Tindakan:</strong> {{ birthRecordKala.kala_two_fetal_distress }}
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>Pendamping pada saat persalinan</th>
                    <td>
                      {{
                        birthRecordKala.kala_two_companion_type_id === 2
                          ? (birthRecordKala.kala_two_companion_arry && birthRecordKala.kala_two_companion_arry.length
                              ? birthRecordKala.kala_two_companion_arry.map(val => companionOptions.find(opt => opt.value == val)?.text).filter(Boolean).join(', ')
                              : 'Ada')
                          : (birthRecordKala.kala_two_companion_type_id === 1 ? 'Tidak Ada' : '-')
                      }}
                    </td>
                  </tr>
                  <tr>
                    <th>Distosia Bahu</th>
                    <td>
                      {{
                        birthRecordKala.kala_two_shoulder_dystocia_type_id === 2
                          ? 'Ya'
                          : (birthRecordKala.kala_two_shoulder_dystocia_type_id === 1 ? 'Tidak' : '-')
                      }}
                      <div v-if="birthRecordKala.kala_two_shoulder_dystocia">
                        <strong>Tindakan:</strong> {{ birthRecordKala.kala_two_shoulder_dystocia }}
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>Masalah lain yang ditemukan</th>
                    <td>{{ birthRecordKala.kala_two_problem || '-' }}</td>
                  </tr>
                  <tr>
                    <th>Penatalaksanaan masalah</th>
                    <td>{{ birthRecordKala.kala_two_problem_management || '-' }}</td>
                  </tr>
                  <tr>
                    <th>Hasil penatalaksanaan masalah</th>
                    <td>{{ birthRecordKala.kala_two_result || '-' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- KALA III -->
            <div class="mb-4" v-if="birthRecordKala">
              <h5 class="mb-3" style="color: #222;">Kala III</h5>
              <table class="table table-bordered table-sm" style="width: 100%; font-size: 13px;">
                <tbody>
                  <tr>
                    <th style="width: 30%;">Inisiasi Menyusu Dini</th>
                    <td>
                      {{
                        birthRecordKala.kala_three_initiation_type_id === 2
                          ? 'Ya'
                          : (birthRecordKala.kala_three_initiation_type_id === 1 ? 'Tidak' : '-')
                      }}
                      <div v-if="birthRecordKala.kala_three_initiation">
                        <strong>Alasan:</strong> {{ birthRecordKala.kala_three_initiation }}
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>Lama Kala III</th>
                    <td>
                      {{ birthRecordKala.kala_three_long !== null ? birthRecordKala.kala_three_long + ' menit' : '-' }}
                    </td>
                  </tr>
                  <tr>
                    <th>Pemberian Oksitosin 10 U im</th>
                    <td>
                      {{
                        birthRecordKala.kala_three_oxytocin_type_id === 2
                          ? 'Ya'
                          : (birthRecordKala.kala_three_oxytocin_type_id === 1 ? 'Tidak' : '-')
                      }}
                      <div v-if="birthRecordKala.kala_three_oxytocin">
                        <strong>Waktu:</strong> {{ birthRecordKala.kala_three_oxytocin }} menit sesudah persalinan
                      </div>
                      <div v-if="birthRecordKala.kala_three_oxytocin_reason">
                        <strong>Alasan:</strong> {{ birthRecordKala.kala_three_oxytocin_reason }}
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>Pemberian ulang Oksitosin (2x)</th>
                    <td>
                      {{
                        birthRecordKala.kala_three_oxytocin_second_type_id === 2
                          ? 'Ya'
                          : (birthRecordKala.kala_three_oxytocin_second_type_id === 1 ? 'Tidak' : '-')
                      }}
                      <div v-if="birthRecordKala.kala_three_oxytocin_second">
                        <strong>Waktu:</strong> {{ birthRecordKala.kala_three_oxytocin_second }}
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>Penegangan tali pusat terkendali</th>
                    <td>
                      {{
                        birthRecordKala.kala_three_umbilical_cord_type_id === 2
                          ? 'Ya'
                          : (birthRecordKala.kala_three_umbilical_cord_type_id === 1 ? 'Tidak' : '-')
                      }}
                      <div v-if="birthRecordKala.kala_three_umbilical_cord">
                        <strong>Alasan:</strong> {{ birthRecordKala.kala_three_umbilical_cord }}
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>Massase fundus uteri</th>
                    <td>
                      {{
                        birthRecordKala.kala_three_uterine_fundus_type_id === 2
                          ? 'Ya'
                          : (birthRecordKala.kala_three_uterine_fundus_type_id === 1 ? 'Tidak' : '-')
                      }}
                      <div v-if="birthRecordKala.kala_three_uterine_fundus">
                        <strong>Alasan:</strong> {{ birthRecordKala.kala_three_uterine_fundus }}
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>Plasenta lahir lengkap (intact)</th>
                    <td>
                      {{
                        birthRecordKala.kala_three_placenta_type_id === 2
                          ? 'Ya'
                          : (birthRecordKala.kala_three_placenta_type_id === 1 ? 'Tidak' : '-')
                      }}
                      <div v-if="birthRecordKala.kala_three_placenta">
                        <strong>Tindakan:</strong> {{ birthRecordKala.kala_three_placenta }}
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>Plasenta tidak lahir &gt;30 menit</th>
                    <td>
                      {{
                        birthRecordKala.kala_three_placenta_not_type_id === 2
                          ? 'Ya'
                          : (birthRecordKala.kala_three_placenta_not_type_id === 1 ? 'Tidak' : '-')
                      }}
                      <div v-if="birthRecordKala.kala_three_placenta_not">
                        <strong>Tindakan:</strong> {{ birthRecordKala.kala_three_placenta_not }}
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>Atoni uteri</th>
                    <td>
                      {{
                        birthRecordKala.kala_three_uterine_atony_type_id === 2
                          ? 'Ya'
                          : (birthRecordKala.kala_three_uterine_atony_type_id === 1 ? 'Tidak' : '-')
                      }}
                      <div v-if="birthRecordKala.kala_three_uterine_atony">
                        <strong>Tindakan:</strong> {{ birthRecordKala.kala_three_uterine_atony }}
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>Jumlah darah yang keluar/perdarahan</th>
                    <td>
                      {{ birthRecordKala.kala_three_amount_blood !== null ? birthRecordKala.kala_three_amount_blood + ' ml' : '-' }}
                    </td>
                  </tr>
                  <tr>
                    <th>Laserasi</th>
                    <td>
                      {{
                        birthRecordKala.kala_three_laceration_type_id === 2
                          ? 'Ya'
                          : (birthRecordKala.kala_three_laceration_type_id === 1 ? 'Tidak' : '-')
                      }}
                      <div v-if="birthRecordKala.kala_three_laceration">
                        <strong>Dimana:</strong> {{ birthRecordKala.kala_three_laceration }}
                      </div>
                      <div v-if="birthRecordKala.kala_three_laceration_degree">
                        <strong>Derajat:</strong> {{ birthRecordKala.kala_three_laceration_degree }}
                      </div>
                      <div v-if="birthRecordKala.kala_three_laceration_suture !== null">
                        <strong>Tindakan:</strong>
                        {{
                          birthRecordKala.kala_three_laceration_suture === 2
                            ? 'Penjahitan'
                            : (birthRecordKala.kala_three_laceration_suture === 1 ? 'Tidak dijahit' : '-')
                        }}
                      </div>
                      <div v-if="birthRecordKala.kala_three_laceration_anesthesia !== null">
                        <strong>Anestesi:</strong>
                        {{
                          birthRecordKala.kala_three_laceration_anesthesia === 2
                            ? 'Dengan anestesi'
                            : (birthRecordKala.kala_three_laceration_anesthesia === 1 ? 'Tanpa anestesi' : '-')
                        }}
                      </div>
                      <div v-if="birthRecordKala.kala_three_laceration_not_suture_reason">
                        <strong>Alasan:</strong> {{ birthRecordKala.kala_three_laceration_not_suture_reason }}
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>Masalah lain yang ditemukan</th>
                    <td>{{ birthRecordKala.kala_three_problem || '-' }}</td>
                  </tr>
                  <tr>
                    <th>Penatalaksanaan masalah</th>
                    <td>{{ birthRecordKala.kala_three_problem_management || '-' }}</td>
                  </tr>
                  <tr>
                    <th>Hasil penatalaksanaan masalah</th>
                    <td>{{ birthRecordKala.kala_three_result || '-' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- KALA IV -->
            <div class="mb-4" v-if="birthRecordKala">
              <h5 class="mb-3" style="color: #222;">Kala IV</h5>
              <table class="table table-bordered table-sm" style="width: 100%; font-size: 13px;">
                <tbody>
                  <tr>
                    <th style="width: 30%;">Kondisi Umum</th>
                    <td>
                      {{
                        [
                          { value: 1, text: 'Baik' },
                          { value: 2, text: 'Sedang' },
                          { value: 3, text: 'Lemah' }
                        ].find(opt => opt.value == birthRecordKala.kala_four_mother_ku)?.text || '-'
                      }}
                    </td>
                  </tr>
                  <tr>
                    <th>Tekanan Darah</th>
                    <td>
                      <span v-if="birthRecordKala.kala_four_systolic !== null && birthRecordKala.kala_four_diastolic !== null">
                        {{ birthRecordKala.kala_four_systolic }}/{{ birthRecordKala.kala_four_diastolic }} mmHg
                      </span>
                      <span v-else>-</span>
                    </td>
                  </tr>
                  <tr>
                    <th>Nadi</th>
                    <td>
                      <span v-if="birthRecordKala.kala_four_pulse !== null">{{ birthRecordKala.kala_four_pulse }} bpm</span>
                      <span v-else>-</span>
                    </td>
                  </tr>
                  <tr>
                    <th>Nafas</th>
                    <td>
                      <span v-if="birthRecordKala.kala_four_breath !== null">{{ birthRecordKala.kala_four_breath }} x/menit</span>
                      <span v-else>-</span>
                    </td>
                  </tr>
                  <tr>
                    <th>Masalah lain yang ditemukan</th>
                    <td>{{ birthRecordKala.kala_four_problem || '-' }}</td>
                  </tr>
                  <tr>
                    <th>Penatalaksanaan masalah</th>
                    <td>{{ birthRecordKala.kala_four_problem_management || '-' }}</td>
                  </tr>
                  <tr>
                    <th>Hasil penatalaksanaan masalah</th>
                    <td>{{ birthRecordKala.kala_four_result || '-' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- BAYI BARU LAHIR -->
            <div class="mb-4" v-if="newbornBaby">
              <h5 class="mb-3" style="color: #222;">Bayi Baru Lahir</h5>
              <table class="table table-bordered table-sm" style="width: 100%; font-size: 13px;">
                <tbody>
                  <tr>
                    <th style="width: 30%;">Berat Badan</th>
                    <td>{{ newbornBaby.newborn_baby_weight !== null ? newbornBaby.newborn_baby_weight + ' gram' : '-' }}</td>
                  </tr>
                  <tr>
                    <th>Panjang Badan</th>
                    <td>{{ newbornBaby.newborn_baby_height !== null ? newbornBaby.newborn_baby_height + ' cm' : '-' }}</td>
                  </tr>
                  <tr>
                    <th>Jenis Kelamin</th>
                    <td>
                      {{
                        newbornBaby.newborn_baby_sex_type_id === 1
                          ? 'Laki-laki'
                          : (newbornBaby.newborn_baby_sex_type_id === 2 ? 'Perempuan' : '-')
                      }}
                    </td>
                  </tr>
                  <tr>
                    <th>Penilaian Bayi</th>
                    <td>
                      {{
                        newbornBaby.newborn_baby_assessment_type_id === 1
                          ? 'Normal (Baik)'
                          : (newbornBaby.newborn_baby_assessment_type_id === 2 ? 'Ada penyulit' : '-')
                      }}
                      <!-- Tindakan jika Normal -->
                      <div v-if="newbornBaby.newborn_baby_assessment_type_id === 1 && newbornBaby.newborn_baby_normal_arry && newbornBaby.newborn_baby_normal_arry.length">
                        <strong>Tindakan:</strong>
                        <ul style="margin-bottom:0;">
                          <li v-for="val in newbornBaby.newborn_baby_normal_arry" :key="val">
                            {{ bayiLahirTindakanNormalOptions.find(opt => opt.value == val)?.text || val }}
                          </li>
                        </ul>
                      </div>
                      <!-- Jika Ada penyulit -->
                      <div v-if="newbornBaby.newborn_baby_assessment_type_id === 2">
                        <ul style="margin-bottom:0;">
                          <li v-if="newbornBaby.newborn_baby_abnormal_arry && newbornBaby.newborn_baby_abnormal_arry.includes(1)">
                            Asfiksia
                            <div v-if="newbornBaby.newborn_baby_asphyxia_arry && newbornBaby.newborn_baby_asphyxia_arry.length">
                              <strong>Tindakan Asfiksia:</strong>
                              <ul>
                                <li v-for="val in newbornBaby.newborn_baby_asphyxia_arry" :key="val">
                                  {{ bayiLahirTindakanAsfiksiaOptions.find(opt => opt.value == val)?.text || val }}
                                </li>
                              </ul>
                              <div v-if="newbornBaby.newborn_baby_asphyxia_arry.includes(7)">
                                <strong>Sebutkan:</strong> {{ newbornBaby.newborn_baby_asphyxia_others || '-' }}
                              </div>
                            </div>
                          </li>
                          <li v-if="newbornBaby.newborn_baby_abnormal_arry && newbornBaby.newborn_baby_abnormal_arry.includes(2)">
                            Cacat Bawaan
                            <div>
                              <strong></strong> {{ newbornBaby.newborn_baby_disabled || '-' }}
                            </div>
                          </li>
                          <li v-if="newbornBaby.newborn_baby_abnormal_arry && newbornBaby.newborn_baby_abnormal_arry.includes(3)">
                            Hipotermi
                            <div>
                              <strong>Tindakan:</strong> {{ newbornBaby.newborn_baby_hypothermia || '-' }}
                            </div>
                          </li>
                        </ul>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>Pemberian ASI setelah jam pertama bayi lahir</th>
                    <td>
                      {{
                        newbornBaby.newborn_baby_breast_milk_type_id === 2
                          ? 'Ya'
                          : (newbornBaby.newborn_baby_breast_milk_type_id === 1 ? 'Tidak' : '-')
                      }}
                      <span v-if="newbornBaby.newborn_baby_breast_milk_hours">
                        ({{ newbornBaby.newborn_baby_breast_milk_hours }} menit sesudah persalinan)
                      </span>
                      <span v-if="newbornBaby.newborn_baby_breast_milk_not">
                        <br><strong>Alasan:</strong> {{ newbornBaby.newborn_baby_breast_milk_not }}
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <th>Masalah pada Bayi</th>
                    <td>{{ newbornBaby.newborn_baby_problem || '-' }}</td>
                  </tr>
                  <tr>
                    <th>Penatalaksanaan masalah</th>
                    <td>{{ newbornBaby.newborn_baby_problem_management || '-' }}</td>
                  </tr>
                  <tr>
                    <th>Hasil penatalaksanaan masalah</th>
                    <td>{{ newbornBaby.newborn_baby_result || '-' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- PEMANTAUAN PERSALINAN -->
            <div class="mb-4" v-if="pemantauanPersalinanRows && pemantauanPersalinanRows.length">
              <h5 class="mb-3" style="color: #222;">Pemantauan Persalinan</h5>
              <div class="table-responsive">
                <table class="table table-bordered table-sm text-center align-middle" style="background: #fff; font-size: 12px;">
                  <thead class="thead-light">
                    <tr>
                      <th>Jam</th>
                      <th>Tekanan Darah</th>
                      <th>Nadi</th>
                      <th>Suhu</th>
                      <th>Tinggi Fundus</th>
                      <th>Kontraksi Uterus</th>
                      <th>Volume Kandung Kemih</th>
                      <th>Volume Perdarahan</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(row, idx) in pemantauanPersalinanRows" :key="idx">
                      <td>{{ row.time || '-' }}</td>
                      <td>
                        <span v-if="row.systolic !== null && row.systolic !== undefined && row.systolic !== '' && row.diastolic !== null && row.diastolic !== undefined && row.diastolic !== ''">
                          {{ row.systolic }}/{{ row.diastolic }} mmHg
                        </span>
                        <span v-else>-</span>
                      </td>
                      <td>{{ row.pulse !== null && row.pulse !== undefined && row.pulse !== '' ? row.pulse : '-' }} bpm </td>
                      <td>
                        <span
                          v-if="!(idx === 1 || idx === 2 || idx === 3 || idx === 5) && row.temperature !== null && row.temperature !== undefined && row.temperature !== '' && row.temperature !== 0"
                        >
                          {{ row.temperature }} °C
                        </span>
                        <span
                          v-else
                          style="display: inline-block; width: 100%; min-height: 1em; background: #e9ecef;"
                        >&nbsp;</span>
                      </td>
                      <td>{{ row.fundal_height !== null && row.fundal_height !== undefined && row.fundal_height !== '' ? row.fundal_height : '-' }} jari di bawah pusar</td>
                      <td>
                        {{
                          row.uterus_contraction === 1
                            ? 'Keras'
                            : (row.uterus_contraction === 2
                              ? 'Lemah'
                              : '-')
                        }}
                      </td>
                      <td>{{ row.bladder_volume !== null && row.bladder_volume !== undefined && row.bladder_volume !== '' ? row.bladder_volume : '-' }} cc</td>
                      <td>{{ row.bleeding_volume !== null && row.bleeding_volume !== undefined && row.bleeding_volume !== '' ? row.bleeding_volume : '-' }} cc</td>
                  </tr>
                  </tbody>
                </table>
              </div>
            </div>


          </div>
        </div>
      </div>
    </b-container>
  </div>
</template>

<script>
import moment from 'moment'
import BarChartKontraksi from '../../../doctor/BarChartKontraksi.vue'
import LineChart from '../../../doctor/LineChart.vue'
import LineChartServiks from '../../../doctor/LineChartServiks.vue'
import LineChartTensiNadi from '../../../doctor/LineChartTensiNadi.vue'
import TableFluidInfiltration from '../../../doctor/TableFluidInfiltration.vue'
import TableIVFluidsMeds from '../../../doctor/TableIVFluidsMeds.vue'
import TableOxytocin from '../../../doctor/TableOxytocin.vue'
import TableTemperature from '../../../doctor/TableTemperature.vue'
import TableUrine from '../../../doctor/TableUrine.vue'


// import other chart components as needed

export default {
  name: 'PrintPartograph',
  components: {
    LineChart,
    TableFluidInfiltration,
    LineChartServiks,
    BarChartKontraksi,
    TableOxytocin,
    TableIVFluidsMeds,
    LineChartTensiNadi,
    TableTemperature,
    TableUrine
  },
  data() {
    return {
      regData: {},
      partograph: {},
      doctor_list: [],
      midwife_list: [],
      labels: [],
      birthNotes: {}, // <-- add this
      companionOptions: [ // <-- add this
        { value: 1, text: 'Suami' },
        { value: 2, text: 'Keluarga Lain' },
        { value: 3, text: 'Kader' },
        { value: 4, text: 'Petugas Kesehatan' },
        { value: 5, text: 'Lainnya' }
      ],
      newbornBaby: {
        partograph_id: "",
        newborn_baby_weight: null,
        newborn_baby_height: null,
        newborn_baby_sex_type_id: null,           // 1 = Laki-laki, 2 = Perempuan
        newborn_baby_assessment_type_id: null,    // 1 = Baik, 2 = Ada penyulit
        newborn_baby_normal_arry: [],
        newborn_baby_abnormal_arry: [],
        newborn_baby_asphyxia_arry: [],
        newborn_baby_asphyxia_others: "",
        newborn_baby_disabled: "",
        newborn_baby_hypothermia: "",
        newborn_baby_breast_milk_type_id: null,
        newborn_baby_breast_milk_hours: "",
        newborn_baby_breast_milk_not: "",
        newborn_baby_problem: "",
        newborn_baby_problem_management: "",
        newborn_baby_result: ""
      },
      bayiLahirTindakanNormalOptions: [
        { value: 1, text: 'Menghangatkan' },
        { value: 2, text: 'Mengeringkan' },
        { value: 3, text: 'IMD atau naluri menyusu segera' },
        { value: 4, text: 'Rangsangan taktil' },
        { value: 5, text: 'Tetes mata profilaksis, vit. K, Imunisasi Hepatitis B' }
      ],
      // bayiLahirTindakanAsfiksia: [],
      bayiLahirTindakanAsfiksiaOptions: [
        { value: 1, text: 'Menghangatkan' },
        { value: 2, text: 'Mengeringkan' },
        { value: 3, text: 'Rangsangan taktil' },
        { value: 4, text: 'Asuhan pasca resusitasi' },
        { value: 5, text: 'Bebaskan jalan napas (posisi dan isap lendir)' },
        { value: 6, text: 'Ventilasi positif (jika perlu)' },
        { value: 7, text: 'Lainnya' }
      ],
      birthRecordKala: {
        id: '',
        birth_record_id: '',
        kala_one_latent_phase: "",
        kala_one_intervention_type_id: null,
        kala_one_alert_line_type_id: null,
        kala_one_problem: "",
        kala_one_problem_management: "",
        kala_one_result: "",
        kala_two_episiotomy_type_id: null,
        kala_two_episiotomy: "",
        kala_two_fetal_distress_type_id: null,
        kala_two_fetal_distress: "",
        kala_two_companion_type_id: null,
        kala_two_companion_arry: [],
        kala_two_shoulder_dystocia_type_id: null,
        kala_two_shoulder_dystocia: "",
        // kala_two_active_phase: "",
        kala_two_problem: "",
        kala_two_problem_management: "",
        kala_two_result: "",
        kala_three_initiation_type_id: null,
        kala_three_initiation: "",
        kala_three_long: null,
        kala_three_oxytocin_type_id: null,
        kala_three_oxytocin: null,
        kala_three_oxytocin_reason: "",
        kala_three_oxytocin_second_type_id: null,
        kala_three_oxytocin_second: "",
        kala_three_umbilical_cord_type_id: null,
        kala_three_umbilical_cord: "",
        kala_three_uterine_fundus_type_id: null,
        kala_three_uterine_fundus: "",
        kala_three_placenta_type_id: null,
        kala_three_placenta: "",
        kala_three_placenta_not_type_id: null,
        kala_three_placenta_not: "",
        kala_three_laceration_type_id: null,
        kala_three_laceration: "",
        kala_three_laceration_degree: null,
        kala_three_laceration_suture: null,
        kala_three_laceration_anesthesia: null,
        kala_three_laceration_not_suture_reason: "",
        kala_three_uterine_atony_type_id: null,
        kala_three_uterine_atony: "",
        kala_three_amount_blood: null,
        kala_three_problem: "",
        kala_three_problem_management: "",
        kala_three_result: "",
        kala_four_mother_ku: null,
        kala_four_systolic: null,
        kala_four_diastolic: null,
        kala_four_pulse: null,
        kala_four_breath: null,
        kala_four_problem: "",
        kala_four_problem_management: "",
        kala_four_result: ""
    },
    pemantauanPersalinanRows: [
      { time_id: 1, time: '', systolic: null, diastolic: null, pulse: null, temperature: null, fundal_height: null, uterus_contraction: null, bladder_volume: null, bleeding_volume: null, isEdited: false  },
      { time_id: 2, time: '', systolic: null, diastolic: null, pulse: null, temperature: null, fundal_height: null, uterus_contraction: null, bladder_volume: null, bleeding_volume: null, isEdited: false  },
      { time_id: 3, time: '', systolic: null, diastolic: null, pulse: null, temperature: null, fundal_height: null, uterus_contraction: null, bladder_volume: null, bleeding_volume: null, isEdited: false  },
      { time_id: 4, time: '', systolic: null, diastolic: null, pulse: null, temperature: null, fundal_height: null, uterus_contraction: null, bladder_volume: null, bleeding_volume: null, isEdited: false  },
      { time_id: 5, time: '', systolic: null, diastolic: null, pulse: null, temperature: null, fundal_height: null, uterus_contraction: null, bladder_volume: null, bleeding_volume: null, isEdited: false  },
      { time_id: 6, time: '', systolic: null, diastolic: null, pulse: null, temperature: null, fundal_height: null, uterus_contraction: null, bladder_volume: null, bleeding_volume: null, isEdited: false  },
    ],
      // You may want to fetch or receive data via props or route params
      // cervicalDilationChartData: {},
      // serviksChartOptions: {},
      // birthNotes: {},
      // ...other data
    }
  },

computed: {
  filteredLabels() {
    if (!this.labels.length || !this.partograph) {
      return {
        fluidInfiltration: [],
        oxytocin: [],
        ivFluidsMeds: [],
        temperature: [],
        urine: [],
      };
    }

    const hasDataByField = (arr, label, field) =>
      Array.isArray(arr) &&
      arr.some(row =>
        row.observation_hours && row.observation_hours.slice(0, 5) === label &&
        row[field] !== undefined && row[field] !== null && row[field] !== ''
      );

    const hasDataByFields = (arr, label, fields) =>
      Array.isArray(arr) &&
      arr.some(row =>
        row.observation_hours && row.observation_hours.slice(0, 5) === label &&
        fields.some(field =>
          row[field] !== undefined && row[field] !== null && row[field] !== ''
        )
      );

    return {
      fluidInfiltration: this.labels.filter(label =>
        hasDataByField(this.partograph.amniotic_fluid, label, 'amniotic_fluid') ||
        hasDataByField(this.partograph.infiltration, label, 'infiltration')
      ),
      oxytocin: this.labels.filter(label =>
        hasDataByField(this.partograph.oxytocin, label, 'oxytocin') ||
        hasDataByField(this.partograph.oxytocin_drip_rate, label, 'oxytocin_drip_rate')
      ),
      ivFluidsMeds: this.labels.filter(label =>
        hasDataByField(this.partograph.iv_fluids_meds, label, 'iv_fluids_meds')
      ),
      temperature: this.labels.filter(label =>
        hasDataByField(this.partograph.temperature, label, 'temperature')
      ),
      urine: this.labels.filter(label =>
        hasDataByFields(this.partograph.urine, label, ['protein', 'acetone', 'volume'])
      ),
    };
  },
},
  created () {
      if (this.$route.params.id) {
          this.reg_des_id = this.$route.params.id
          this.getData(this.$route.params.id)
          this.initPartographAndLabels(this.$route.params.id)
          this.fetchDoctorList()
          this.fetchMidwifeList()
      }
  },
  methods: {
    // printCanvas() {
    //     const canvas = this.$refs.canvas;
    //     const originalContents = document.body.innerHTML;
    //     document.body.innerHTML = canvas.innerHTML;
    //     window.print();
    //     document.body.innerHTML = originalContents;
    //     location.reload();
    // },
    printCanvas() {
      window.print();
    },
    closeWindow() {
      window.close();
    },

    async initPartographAndLabels(regDesId) {
      await this.fetchPartograph(regDesId);
      this.getLabels();
      await this.checkOrFetchBirthRecord();
      await this.checkOrFetchBirthRecordKala();
    },

    getData(regDesId) {
			this.$store.dispatch('registration/getById', regDesId).then(data => {
        this.regData = data[0]
      })
    },

    async checkOrFetchBirthRecord() {
      this.loading = true;
      try {
        let params = {};
        if (this.birthNotes.id) {
          params.birth_record_id = this.birthNotes.id;
        } else if (this.partograph.id) {
          params.partograph_id = this.partograph.id;
        } else {
          this.loading = false;
          return;
        }

        // Helper to convert array of strings to array of integers
        function toIntArray(arr) {
          if (!Array.isArray(arr)) return [];
          return arr.map(v => parseInt(v, 10)).filter(v => !isNaN(v));
        }

        // Helper to convert value to number or null
        function toNumber(val) {
          return val !== undefined && val !== null && val !== '' ? Number(val) : null;
        }

        const data = await this.$store.dispatch('birthrecord/getBirthRecordDetail', params);

        if (data && data.data && data.data[0]) {
          const record = data.data[0];

          // Assign birthNotes fields with guards
          this.birthNotes.id = record.id;
          this.birthNotes.partograph_id = record.partograph_id;
          this.birthNotes.date_birth = record.date_birth;
          this.birthNotes.birthing_place_type_id = toNumber(record.birthing_place_type_id);
          this.birthNotes.birthing_place_other = record.birthing_place_other;
          this.birthNotes.birthing_place_address = record.birthing_place_address;
          this.birthNotes.referral_type_id = toNumber(record.referral_type_id);
          this.birthNotes.referral_kala = toNumber(record.referral_kala);
          this.birthNotes.referral_reason = record.referral_reason;
          this.birthNotes.referral_place = record.referral_place;
          this.birthNotes.referral_companion_type_id = toNumber(record.referral_companion_type_id);
          this.$set(this.birthNotes, 'referral_companion_arry', toIntArray(record.referral_companion_arry));
          this.$set(this.birthNotes, 'problem_arry', toIntArray(record.problem_arry));

          // Assign newbornBaby fields with guards
          this.newbornBaby.partograph_id = record.partograph_id;
          this.newbornBaby.newborn_baby_weight = toNumber(record.newborn_baby_weight);
          this.newbornBaby.newborn_baby_height = toNumber(record.newborn_baby_height);
          this.newbornBaby.newborn_baby_sex_type_id = toNumber(record.newborn_baby_sex_type_id);
          this.newbornBaby.newborn_baby_assessment_type_id = toNumber(record.newborn_baby_assessment_type_id);
          this.$set(this.newbornBaby, 'newborn_baby_normal_arry', toIntArray(record.newborn_baby_normal_arry));
          this.$set(this.newbornBaby, 'newborn_baby_abnormal_arry', toIntArray(record.newborn_baby_abnormal_arry));
          this.$set(this.newbornBaby, 'newborn_baby_asphyxia_arry', toIntArray(record.newborn_baby_asphyxia_arry));
          this.newbornBaby.newborn_baby_asphyxia_others = record.newborn_baby_asphyxia_others || "";
          this.newbornBaby.newborn_baby_disabled = record.newborn_baby_disabled || "";
          this.newbornBaby.newborn_baby_hypothermia_type_id = toNumber(record.newborn_baby_hypothermia_type_id);
          this.newbornBaby.newborn_baby_hypothermia = record.newborn_baby_hypothermia || "";
          this.newbornBaby.newborn_baby_breast_milk_type_id = toNumber(record.newborn_baby_breast_milk_type_id);
          this.newbornBaby.newborn_baby_breast_milk_hours = record.newborn_baby_breast_milk_hours || "";
          this.newbornBaby.newborn_baby_breast_milk_not = record.newborn_baby_breast_milk_not || "";
          this.newbornBaby.newborn_baby_problem = record.newborn_baby_problem || "";
          this.newbornBaby.newborn_baby_problem_management = record.newborn_baby_problem_management || "";
          this.newbornBaby.newborn_baby_result = record.newborn_baby_result || "";

          this.birthNotesUpdate = true;
          this.birthRecordKala.birth_record_id = this.birthNotes.id;
        }
      } catch (e) {
        this.notification('Gagal', 'AlertTriangleIcon', 'danger', 'Gagal mengambil data birth record');
      } finally {
        this.loading = false;
      }
    },


    async checkOrFetchBirthRecordKala() {
      // console.log('Checking or fetching birth record kala...');
      this.loading = true;
      try {
        if (!this.birthNotes.id) {
          // No birth record to fetch
          this.resetBirthRecordKala();
          return;
        }

        const params = { birth_record_id: this.birthNotes.id };
        const data = await this.$store.dispatch('birthrecord/getBirthRecordKala', params);

        if (data && data.data && data.data[0]) {
          // Merge fetched data into birthRecordKala
          Object.assign(this.birthRecordKala, data.data[0]);
          // console.log('Fetched birthRecordKala:', this.birthRecordKala);

          // Prepare default rows for monitoring_kala_four
          const defaultRows = Array.from({ length: 6 }, (_, i) => ({
            time_id: i + 1,
            time: '',
            systolic: null,
            diastolic: null,
            pulse: null,
            temperature: null,
            fundal_height: null,
            uterus_contraction: null,
            bladder_volume: null,
            bleeding_volume: null,
            isEdited: false,
          }));

          // If monitoring_kala_four exists, merge it into defaultRows
          if (Array.isArray(data.data[0].monitoring_kala_four) && data.data[0].monitoring_kala_four.length > 0) {
            data.data[0].monitoring_kala_four.forEach((row, idx) => {
              if (defaultRows[idx]) Object.assign(defaultRows[idx], row, { isEdited: false });
            });
          }

          this.pemantauanPersalinanRows = defaultRows;
          this.savedCells = {};

          // Mark saved cells for non-empty values
          this.pemantauanPersalinanRows.forEach((row, rowIdx) => {
            [
              'time', 'systolic', 'diastolic', 'pulse', 'temperature',
              'fundal_height', 'uterus_contraction', 'bladder_volume', 'bleeding_volume'
            ].forEach(col => {
              if (row[col] !== null && row[col] !== '' && row[col] !== undefined) {
                this.$set(this.savedCells, `${rowIdx}-${col}`, true);
              }
            });
          });
        } else {
          // No data found, reset to default
          this.resetBirthRecordKala();
        }
      } catch (e) {
        this.notification('Gagal', 'AlertTriangleIcon', 'danger', 'Gagal mengambil data birth record kala');
        this.resetBirthRecordKala();
      } finally {
        this.loading = false;
      }
    },

    // Helper to reset birthRecordKala and pemantauanPersalinanRows
    resetBirthRecordKala() {
      this.birthRecordKala = {
        id: "",
        birth_record_id: this.birthNotes.id || "",
        kala_one_latent_phase: '',
        kala_one_intervention_type_id: null,
        kala_one_alert_line_type_id: null,
        kala_one_problem: '',
        kala_one_problem_management: '',
        kala_one_result: '',
        kala_two_episiotomy_type_id: null,
        kala_two_episiotomy: "",
        kala_two_fetal_distress_type_id: null,
        kala_two_fetal_distress: "",
        kala_two_companion_type_id: null,
        kala_two_companion_arry: [],
        kala_two_shoulder_dystocia_type_id: null,
        kala_two_shoulder_dystocia: "",
        kala_two_problem: "",
        kala_two_problem_management: "",
        kala_two_result: "",
        kala_three_initiation_type_id: null,
        kala_three_initiation: "",
        kala_three_long: null,
        kala_three_oxytocin_type_id: null,
        kala_three_oxytocin: null,
        kala_three_oxytocin_reason: "",
        kala_three_oxytocin_second_type_id: null,
        kala_three_oxytocin_second: "",
        kala_three_umbilical_cord_type_id: null,
        kala_three_umbilical_cord: "",
        kala_three_uterine_fundus_type_id: null,
        kala_three_uterine_fundus: "",
        kala_three_placenta_type_id: null,
        kala_three_placenta: "",
        kala_three_placenta_not_type_id: null,
        kala_three_placenta_not: "",
        kala_three_laceration_type_id: null,
        kala_three_laceration: "",
        kala_three_laceration_degree: null,
        kala_three_laceration_suture: null,
        kala_three_laceration_anesthesia: null,
        kala_three_laceration_not_suture_reason: "",
        kala_three_uterine_atony_type_id: null,
        kala_three_uterine_atony: "",
        kala_three_amount_blood: null,
        kala_three_problem: "",
        kala_three_problem_management: "",
        kala_three_result: "",
        kala_four_mother_ku: null,
        kala_four_systolic: null,
        kala_four_diastolic: null,
        kala_four_pulse: null,
        kala_four_breath: null,
        kala_four_problem: "",
        kala_four_problem_management: "",
        kala_four_result: ""
      };
      this.pemantauanPersalinanRows = Array.from({ length: 6 }, (_, i) => ({
        time_id: i + 1,
        time: '',
        systolic: null,
        diastolic: null,
        pulse: null,
        temperature: null,
        fundal_height: null,
        uterus_contraction: null,
        bladder_volume: null,
        bleeding_volume: null,
        isEdited: false,
      }));
      this.savedCells = {};
    },

    getLabels() {
      if (this.partograph && Array.isArray(this.partograph.fetal_heart_rate)) {
        this.labels = this.partograph.fetal_heart_rate
          .map(item => item.observation_hours ? item.observation_hours.slice(0, 5) : '')
          .filter(Boolean);

      } else {
        this.labels = [];
      }
    },

    localDate(input_date) {
        if (!input_date || input_date === "0000-00-00") {
            return "";
        }

        const parsed = moment(input_date);
        if (!parsed.isValid()) {
            return "";
        }

        return moment(input_date).locale("id").format('DD MMMM YYYY')
    },

    getFetalHeartRateChartData() {
      // Fetch and align data as before
      const labels = this.labels; // array of time labels
      const allHeartRates = Array.isArray(this.partograph.fetal_heart_rate)
        ? this.partograph.fetal_heart_rate
        : [];
      const heartRateMap = {};
      for (let obj of allHeartRates) {
        const time = obj.observation_hours?.slice(0,5);
        heartRateMap[time] = obj.fetal_heart_rate;
      }
      const alignedData = labels.map(label => heartRateMap[label] !== undefined ? heartRateMap[label] : null);
      return {
        labels,
        datasets: [
          {
            label: 'Fetal Heart Rate',
            backgroundColor: '#FBA2F7',
            borderColor: "rgb(255, 99, 132)",
            data: alignedData,
            trendlineLinear: {
              colorMin: "gray",
              colorMax: "gray",
              lineStyle: "solid",
              width: 2,
              projection: false
            }
          }
        ]
      };
    },

    getCervicalDilationChartData() {
      const labels = this.labels;
      const cervicalDilations = Array.isArray(this.partograph.cervical_dilation)
        ? this.partograph.cervical_dilation
        : [];

        const headDescents = Array.isArray(this.partograph.head_descent)
          ? this.partograph.head_descent
          : [];

      const cervicalDilationMap = {};
      const headDescentMap = {};
      for (let obj of cervicalDilations) {
        const time = obj.observation_hours?.slice(0, 5);
        cervicalDilationMap[time] = Number(obj.cervical_dilation);
      }

      for (let obj of headDescents) {
        const time = obj.observation_hours?.slice(0, 5);
        headDescentMap[time] = Number(obj.head_descent);
      }

      const alignedCervicalDilation = labels.map(label =>
        cervicalDilationMap[label] !== undefined ? cervicalDilationMap[label] : null
      );
      const alignedHeadDescent = labels.map(label =>
        headDescentMap[label] !== undefined ? headDescentMap[label] : null
      );
    //   console.log('alignedCervicalDilation: ', alignedCervicalDilation);
    //   console.log('alignedHeadDescent: ', alignedHeadDescent);
      return {
        labels,
        datasets: [
          {
            label: 'Pembukaan Serviks',
            backgroundColor: '#1b1c1c',
            borderColor: 'rgba(100, 120, 220, 0.7)',
            data: alignedCervicalDilation,
            trendlineLinear: {
              colorMin: "gray",
              colorMax: "gray",
              lineStyle: "solid",
              width: 2,
              projection: false
            },
            pointStyle: this.createAsteriskPoint(18, '#1b1c1c'),
            pointRadius: 10,
            pointHoverRadius: 12
          },
          {
            label: 'Penurunan Kepala',
            backgroundColor: '#1b1c1c',
            borderColor: 'rgba(180, 120, 220, 0.7)',
            data: alignedHeadDescent,
            pointStyle: 'circle',
            pointRadius: 7,
            pointHoverRadius: 9
          }
        ]
      };
    },

    getTensiNadiChartData() {
      const labels = this.labels;
      const bloodPressures = Array.isArray(this.partograph.blood_pressure)
        ? this.partograph.blood_pressure
        : [];
      const pulses = Array.isArray(this.partograph.pulse)
        ? this.partograph.pulse
        : [];

      const systolicMap = {};
      const diastolicMap = {};
      for (let obj of bloodPressures) {
        const time = obj.observation_hours?.slice(0, 5);
        systolicMap[time] = Number(obj.systolic);
        diastolicMap[time] = Number(obj.diastolic);
      }

      const pulseMap = {};
      for (let obj of pulses) {
        const time = obj.observation_hours?.slice(0, 5);
        pulseMap[time] = Number(obj.pulse);
      }

      const alignedSystolic = labels.map(label =>
        systolicMap[label] !== undefined ? systolicMap[label] : null
      );
      const alignedDiastolic = labels.map(label =>
        diastolicMap[label] !== undefined ? diastolicMap[label] : null
      );
      const alignedPulse = labels.map(label =>
        pulseMap[label] !== undefined ? pulseMap[label] : null
      );

      return {
        labels,
        datasets: [
        {
            label: 'Tekanan Darah Sistolik',
            backgroundColor: '#000',
            borderColor: 'rgba(0,0,0,0.15)',
            data: alignedSystolic,
            pointStyle: 'triangle',
            pointRadius: 10,
            pointHoverRadius: 12,
            pointBackgroundColor: '#000',
            pointBorderColor: '#000',
            fill: false,
            yAxisID: 'y',
        },
        {
            label: 'Tekanan Darah Diastolik',
            backgroundColor: '#000',
            borderColor: 'rgba(0,0,0,0.15)',
            data: alignedDiastolic,
            pointStyle: this.createTriangleDown(20, '#000'),
            pointRadius: 10,
            pointHoverRadius: 12,
            pointBackgroundColor: '#000',
            pointBorderColor: '#000',
            fill: false,
            yAxisID: 'y',
        },
        {
            label: 'Nadi',
            backgroundColor: '#7a7a7a',
            borderColor: 'rgba(0,0,0,0.15)',
            data: alignedPulse,
            pointStyle: 'circle',
            pointRadius: 7,
            pointHoverRadius: 9,
            pointBackgroundColor: '#7a7a7a',
            pointBorderColor: '#7a7a7a',
            fill: false,
            yAxisID: 'y',
        }
        ]
      };
    },

    async fetchPartograph(regDesId) {
      const params = { registration_destination_id: regDesId }
      const data = await this.$store.dispatch('partograph/getPartographDetail', params)
      if (data && data.data && data.data[0]) {
        this.partograph = data.data[0]
        console.log('Fetched partograph data:', this.partograph)
      }
    },
    async fetchDoctorList() {
      this.doctor_list = await this.$store.dispatch('employee/getDoctorList', {})
    },
    async fetchMidwifeList() {
      this.midwife_list = await this.$store.dispatch('employee/getMidwifeList', {})
    },
    getDoctorName(id) {
      const found = this.doctor_list.find(d => d.value == id)
      return found ? found.title : '-'
    },
    getMidwifeName(id) {
      const found = this.midwife_list.find(m => m.value == id)
      return found ? found.title : '-'
    },

    localDateTime(dt) {
      if (!dt) return '-'
      return moment(dt).format('DD MMM YYYY HH:mm')
    },

    createAsteriskPoint(size = 16, color = '#1b1c1c') {
      const canvas = document.createElement('canvas');
      canvas.width = size;
      canvas.height = size;
      const ctx = canvas.getContext('2d');
      ctx.strokeStyle = color;
      ctx.lineWidth = 2;
      ctx.beginPath();
      // Draw X only (diagonals)
      ctx.moveTo(3, 3);
      ctx.lineTo(size - 3, size - 3);
      ctx.moveTo(size - 3, 3);
      ctx.lineTo(3, size - 3);
      ctx.stroke();
      return canvas;
    },

    createTriangleDown(size = 20, color = '#000') {
        const canvas = document.createElement('canvas');
        canvas.width = size;
        canvas.height = size;
        const ctx = canvas.getContext('2d');
        ctx.fillStyle = color;
        ctx.beginPath();
        ctx.moveTo(size / 2, size); // bottom center
        ctx.lineTo(size, 0);        // top right
        ctx.lineTo(0, 0);           // top left
        ctx.closePath();
        ctx.fill();
        return canvas;
    },

    // fetchPartographData(id) { ... }
  },
}
</script>

<style>
/* General styles */
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

/* #canvas {
  width: 21.2cm !important;
  height: 29.7cm !important;
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
  padding: 40px;
} */

.print-header {
  border: 1px solid #444;
  padding: 18px 24px;
  background: #f8f9fa;
  margin-bottom: 32px;
  font-size: 1.1rem;
}
.print-header .row > div {
  padding-bottom: 6px;
}

.print-section {
  background: #f8f9fa;
  padding: 18px 24px;
  margin-bottom: 32px;
  font-size: 1.05rem;
}

.print-columns {
  display: flex;
  gap: 32px;
}

.print-col {
  flex: 1 1 0;
  min-width: 0;
}

.print-row {
  display: flex;
  flex-direction: column; /* Always stack label and value */
  margin-bottom: 18px;
}

.print-label {
  font-weight: bold;
  text-align: left;
  padding-right: 0;
}

.print-value {
  text-align: left;
}

.br-on-screen {
  display: inline;
}

/* Print-specific styles */
@media print {
  .b-button, .btn, .text-center.mt-4 { display: none !important; }
  .print-header {
    /* border: none; */
    /* background: none; */
    margin-bottom: 16px;
    font-size: 1.1rem;
  }
  .print-section {
    padding: 0;
    background: none;
  }

  .print-subsection {
    margin-left: 1.5em;
    font-size: 90%;
    color: #444;
  }
  .print-columns {
    gap: 12px;
    flex-direction: row;
  }
  .print-col {
    width: 33.33%;
    max-width: 33.33%;
  }
}

/* Responsive: on small screens, stack columns */
@media screen and (max-width: 900px) {
  .print-columns {
    flex-direction: column;
    gap: 0;
  }
  .print-col {
    width: 100%;
    max-width: 100%;
  }
}

.print-chart-section {
  /* background: #fff; */
  padding: 18px 24px;
  margin-bottom: 32px;
  /* border-radius: 8px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.04); */
}
.chart-container {
  width: 100%;
  max-width: 1000px;
  margin: 0 auto;
}
@media print {
  .print-chart-section {
    background: none;
    box-shadow: none;
    padding: 0;
    margin-bottom: 16px;
  }
  .chart-container {
    max-width: 100%;
    width: 100%;
  }
}

/* table, th, td {
  font-size: 8px !important;
} */


</style>
