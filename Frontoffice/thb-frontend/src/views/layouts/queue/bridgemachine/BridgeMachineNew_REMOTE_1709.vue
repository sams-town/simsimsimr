<template>
  <b-overlay :show="show" rounded="sm">
    <div class="position-relative">
      <div class="position-absolute p-40" v-if="print === false && print_poli === false" style="right: 0; top: 0;">
        <b-img src="../../../../@core/assets/images/apexa.png" class="mr-24"/>
        <b-button @click="fullScreenMode()" v-b-tooltip.hover.left :title="active.tooltip" variant="neutral-100">
          <feather-icon :icon="active.tooltip == 'Maximize' ? 'MaximizeIcon' : 'MinimizeIcon'" size="32"/>
        </b-button>
      </div>
      <div v-if="active.screen != 1 && print === false && print_poli === false" class="position-absolute p-40" style="left: 0; top: 0;">
        <b-button @click="backToScreen(active.screen)" size="lg" variant="primary">
          <feather-icon icon="ArrowLeftIcon" size="18" /> KEMBALI
        </b-button>
        <b-button @click="changeToScreen(6)" variant="orange-500" size="lg">
          CHECK IN MJKN
        </b-button>
      </div>
      <template>
        <div class="full-page d-flex justify-content-center align-items-center">
            <!-- MAIN SCREEN -->
            <section v-if="active.screen == 1" class="col-8" >
              <div class="text-center">
                <div class="text-bold-72">Selamat Datang</div>
                <div class="text-semi-24">di FASKES TINGKAT LANJUT "RS Taman Harapan Baru"</div>
                <div class="text-semi-18" >Silahkan pilih menu dibawah ini.</div>
              </div>
              <div class="d-flex mt-64 gap-16">
                <div class="col-6">
                  <b-button @click="changeToScreen(2)" variant="primary-500" class="btn-block py-64">
                    <div class="text-bold-36">Peserta Baru</div>
                    <div>Peserta yang belum memiliki Rekam Medis</div>
                  </b-button>
                </div>
                <div class="col-6">
                  <b-button @click="changeToScreen(4)" variant="success-700" class="btn-block py-64">
                    <div class="text-bold-36">Peserta Lama</div>
                    <div>Peserta yang telah memiliki Rekam Medis</div>
                  </b-button>
                </div>
              </div>
            </section>

            <section v-if="active.screen == 2" class="col-10" >
              <template v-if="print === false">
                <div class="text-center">
                  <b-img src="../../../../@core/assets/images/logo-thb.jpg" width="500px" />
                </div>
                <div class="row justify-content-center mt-64 gap-64">
                    <div class="col-lg-4 px-32">
                      <b-img @click.prevent="onClick(1)" src="../../../../@core/assets/images/opsibpjs.png" class="w-100" />
                    </div>
                    <div class="col-lg-4 px-32">
                      <b-img @click.prevent="onClick(2)" src="../../../../@core/assets/images/opsiumum.png" class="w-100" />
                    </div>
                      <!-- <div class="col-lg-4">
                        <b-img @click.prevent="onClick(3)" src="../../../../@core/assets/images/opsijkn.png"/>
                      </div> -->
                </div>
              </template>
              <template v-if="print === true">
                <!-- PRINT ANTRIAN ADMISI -->
                <div class="canvas-wrapper">
                  <section id="canvas" ref="canvas" class="mt-16 p-40" style="position: relative;">
                    <section class="d-flex flex-column justify-content-center text-regular-14 text-center mt-40">
                      <!-- <p class="text-uppercase">{{ regData.health_facility_name }}</p> -->
                      <p style="font-size:32px">Tiket Antrian</p>
                      <p style="font-size:32px">RS Taman Harapan Baru Bekasi</p>
                      <p style="font-size:32px" v-if="queue_code_1">Kode Booking : {{queue_code_1.code_booking}}</p>
                    </section>
                    <h1 style="font-size:72px" class="text-center"><b>{{queue_code_1.queue}}</b></h1>
                    <p style="font-size:32px" class="text-regular-14 text-center">( {{ dateNow }} )</p>
                  </section>
                </div>
              </template>
            </section>

            <section v-if="active.screen == 3" class="col-10" >
              <div class="text-center">
                <div class="text-bold-72">Selamat Datang</div>
                <div class="text-semi-24">di FASKES TINGKAT LANJUT "RS Taman Harapan Baru"</div>
                <div class="text-semi-18" >Silahkan pilih menu dibawah ini.</div>
              </div>
              <div class="d-flex mt-64">
                <div class="col-4">
                  <b-button @click="changeToScreen(4)" variant="primary-500" class="btn-block py-64">
                    <div class="text-bold-36">BPJS</div>
                    <div>Peserta BPJS</div>
                  </b-button>
                </div>
                <div class="col-4">
                  <b-button @click="changeToScreen(5)" variant="success-700" class="btn-block py-64">
                    <div class="text-bold-36">NON BPJS</div>
                    <div>Peserta Non BPJS</div>
                  </b-button>
                </div>
                <div class="col-4">
                  <b-button @click="changeToScreen(6)" variant="orange-500" class="btn-block py-48 h-100">
                    <div class="text-bold-36">CHECK IN</div>
                    <div>Peserta yang sudah mengambil Nomor Antrian via Mobile JKN & WhatsApp</div>
                  </b-button>
                </div>
              </div>

            </section>

            <section v-if="active.screen == 4 && print_poli === false" class="col-10">
              <template v-if="print === false">
              <div class="d-flex flex-column align-items-center text-center">
                <div class="text-bold-72">PESERTA BPJS</div>
                <div class="mt-32 col-5">
                  <b-form-radio-group v-model="bpjs.idcard_type_id" size="lg" >
                    <b-form-radio value="1">NIK</b-form-radio>
                    <b-form-radio value="2">NO KARTU BPJS</b-form-radio>
                  </b-form-radio-group>

                  <div class="mt-8">
                    <b-input-group>
                      <b-form-input v-model="bpjs.idcard_number" placeholder="NIK/NOKA" autocomplete="false" type="number" size="lg"></b-form-input>
                      <b-input-group-append>
                        <b-button @click="searchByCardNumber(bpjs.idcard_type_id)" variant="warning"><b-icon icon="search" /></b-button>
                        <b-button @click="openKeyBpjs('card')" variant="primary"><b-icon icon="keyboard" /></b-button>
                      </b-input-group-append>
                    </b-input-group>
                  </div>
                  <VueNumericKeypad style="position:relative"
                    :class="active.keyboard_card_number === true ? 'd-block' : 'd-none'"
                    class="mt-16"
                    :value.sync="bpjs.idcard_number"
                    :show.sync="keyboard.show"
                    :options="keyboard.options"
                  />

                  <div class="mt-12">
                    <b-input-group>
                      <b-form-input v-model="form.noKunjungan" readonly placeholder="NO RUJUKAN" type="text" size="lg"></b-form-input>

                      <!-- <b-input-group-append>
                        <b-button @click="openKeyBpjs('ref')" variant="primary"><b-icon icon="keyboard" /></b-button>
                      </b-input-group-append> -->
                    </b-input-group>
                  </div>
                  <VueNumericKeypad style="position:relative"
                    :class="active.keyboard_ref_number === true ? 'd-block' : 'd-none'"
                    class="mt-16"
                    :value.sync="bpjs.reference_number"
                    :show.sync="keyboard.show"
                    :options="keyboard.options"
                  />

                  <v-select v-model="form.doctor_in_charge_id" aria-readonly="true" :options="OptionsDoctor" :reduce="OptionsDoctor => OptionsDoctor" @input="checkjadwal()" label="title" placeholder="-- Dokter --" class="mt-12"></v-select>

                  <!-- <div v-if="showcaution === true" class="card-body bg-warning mb-16">
                      <div class="d-flex align-items-center">
                          <b-icon-exclamation-triangle-fill variant="neutral-800" style="width: 24px; height: 24px;"/>
                          <div class="ml-3 font-italic">Jadwal Praktek Dokter kurang dari 2 jam silahkan datang kembali</div>
                      </div>
                  </div>
                  <b-button v-if="showcaution === false" variant="primary" size="lg" :disabled="isDisabled" @click.prevent="validationForm" class="btn-block mt-24">AMBIL ANTRIAN</b-button> -->
                  <b-button variant="primary" size="lg" :disabled="isDisabled" @click.prevent="validationForm" class="btn-block mt-24">AMBIL ANTRIAN</b-button>

                </div>
              </div>
              <b-modal v-model="active.modalSearch" hide-header hide-footer scrollable size="md">
                <div v-for="n in refer" class="card mb-24">
                  <div class="card-header font-weight-bold bg-primary-200 text-center">{{n.noKunjungan}}</div>
                    <b-table-simple bordered >
                      <!-- <b-tr>
                        <b-th colspan="2" class="text-center">100200300400500600</b-th>
                      </b-tr> -->
                      <b-tr>
                        <b-th style="width: 40%;">Perujuk</b-th>
                        <b-td>{{ n.nmProviderPerujuk }}</b-td>
                      </b-tr>
                      <b-tr>
                        <b-th style="width: 40%;">Tanggal Rujukan</b-th>
                        <b-td>{{ n.tglKunjungan }}</b-td>
                      </b-tr>
                      <b-tr>
                        <b-th style="width: 40%;">Poli</b-th>
                        <b-td>{{ n.nama_poli }}</b-td>
                      </b-tr>
                      <b-tr v-if="refer_bpjs_data.length > 0">
                        <b-th style="width: 40%;">Data Kontrol</b-th>
                        <b-td>
                          <table>
                            <tr>
                              <td>Tgl Kontrol</td>
                              <td>No Kontrol</td>
                            </tr>
                            <tr v-for="n_refer in refer_bpjs_data">
                              <td>{{ n_refer.tglRencanaKontrol }}</td>
                              <td v-if="n_refer.tglRencanaKontrol != datenow"><b-button variant="warning-700" class="btn-block" @click="UpdateKontrol(n_refer,n)" >Tanggal rencana kontrol sudah lewat, klik untuk update.</b-button></td>
                              <td v-if="n_refer.tglRencanaKontrol == datenow"><b-button variant="success-700" class="btn-block" @click="pickControlTableKontrol(n_refer)" >{{ n_refer.noSuratKontrol }}</b-button></td>
                            </tr>
                          </table>
                        </b-td>
                      </b-tr>
                      <b-tr v-if="refer_internal_data.length > 0">
                        <b-th style="width: 40%;" colspan="2">Data Rujukan Internal</b-th>
                      </b-tr>
                      <b-tr v-if="refer_internal_data.length > 0">
                        <b-td colspan="2">
                          <table>
                            <tr>
                              <td>Tgl Rujukan</td>
                              <td>Poli</td>
                              <td>Dokter</td>
                              <td>No Rujukan</td>
                            </tr>
                            <tr v-for="n_refer in refer_internal_data">
                              <td>{{ n_refer.consultation_date }}</td>
                              <td>{{ n_refer.health_facility_item_name }}</td>
                              <td>{{ n_refer.doctor_name }}</td>
                              <td><b-button variant="success-700" class="btn-block" @click="pickControlTableReferInternal(n_refer)" >{{ n_refer.no_refer }}</b-button></td>
                            </tr>
                          </table>
                        </b-td>
                      </b-tr>
                    </b-table-simple>
                    <b-button variant="success-700" class="btn-block" @click="pickControlTable(n)" >Pilih</b-button>
                    <div class="card">
                      <template v-if="itemsPatient.length === 0">
                        <p>Pasien Belum terdaftar di SIMRS</p>
                      </template>
                      <template v-else>
                        <b-table small hover responsive head-variant="light" :items="itemsPatient" :fields="fieldsPatient" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                          <template #cell(action)="row">
                            <div class="d-flex">
                              <b-button size="sm" @click="getPatientOnReg(row.item)" variant="info" class="mr-1">Daftarkan</b-button>
                            </div>
                          </template>
                        </b-table>
                      </template>
                      <template>.
                        <div class="col-lg-4 px-32">
                          <b-button size="sm" @click.prevent="onClick(1)"class="w-100" >Pasien Belum Terdaftar Silahkan Ambil Antrian</b-button>
                        </div>
                      </template>
                    </div>
                </div>

              </b-modal>
              </template>
              <template v-if="print === true">
                <!-- PRINT ANTRIAN ADMISI -->
                <div class="canvas-wrapper">
                  <section id="canvas" ref="canvas" class="mt-16 p-40" style="position: relative;">
                    <section class="d-flex flex-column justify-content-center text-regular-14 text-center mt-40">
                      <!-- <p class="text-uppercase">{{ regData.health_facility_name }}</p> -->
                      <p style="font-size:32px">Tiket Antrian</p>
                      <p style="font-size:32px">RS Taman Harapan Baru Bekasi</p>
                      <p style="font-size:32px" v-if="queue_code_1">Kode Booking : {{queue_code_1.code_booking}}</p>
                    </section>
                    <h1 style="font-size:72px" class="text-center"><b>{{queue_code_1.queue}}</b></h1>
                    <p style="font-size:32px" class="text-regular-14 text-center">( {{ dateNow }} )</p>
                  </section>
                </div>
              </template>
            </section>

            <section v-if="active.screen == 5" class="col-10">
              <div class="d-flex flex-column align-items-center text-center">
                <div class="text-bold-72">BUKAN PESERTA BPJS</div>

                <div class="mt-32 col-5">
                  <b-form-radio-group v-model="nonBpjs.idcard_type_id" size="lg" >
                    <b-form-radio value="1">NIK</b-form-radio>
                  </b-form-radio-group>

                  <div class="mt-8">
                    <b-input-group>
                      <b-form-input v-model="nonBpjs.idcard_number" placeholder="NIK" type="number" size="lg"></b-form-input>

                      <b-input-group-append>
                        <b-button @click="active.keyboard_nonbpjs = !active.keyboard_nonbpjs" variant="primary"><b-icon icon="keyboard" /></b-button>
                      </b-input-group-append>
                    </b-input-group>
                  </div>
                  <VueNumericKeypad style="position:relative"
                    :class="active.keyboard_nonbpjs === true ? 'd-block' : 'd-none'"
                    class="mt-16"
                    :value.sync="nonBpjs.idcard_number"
                    :show.sync="keyboard.show"
                    :options="keyboard.options"
                  />

                  <v-select v-model="nonBpjs.health_facility_id" aria-readonly="true" :options="OptionsHealthFacility" :reduce="OptionsHealthFacility => OptionsHealthFacility.value" label="title" placeholder="-- Instalasi --" class="mt-12" ></v-select>

                  <v-select v-model="nonBpjs.health_facility_item_id" aria-readonly="true" :options="OptionsHealthFacilityItem" :reduce="OptionsHealthFacilityItem => OptionsHealthFacilityItem.value" label="title" placeholder="-- Poliklinik --" class="mt-12"></v-select>

                  <v-select v-model="nonBpjs.doctor_in_charge_id" aria-readonly="true" :options="OptionsDoctor" :reduce="OptionsDoctor => OptionsDoctor.value" label="title" placeholder="-- Dokter --" class="mt-12"></v-select>

                  <b-button variant="primary" size="lg" class="btn-block mt-24">AMBIL ANTRIAN</b-button>

                </div>
              </div>
            </section>

            <section v-if="active.screen == 6 && print_poli === false" class="col-10">
              <div class="d-flex flex-column align-items-center text-center">
                <div class="text-bold-72">CHECK IN</div>

                <div class="mt-32 col-5">
                  <b-form-radio-group v-model="checkin.booking_type_id" size="lg" >
                    <b-form-radio value="1">MOBILE JKN</b-form-radio>
                    <!-- <b-form-radio value="2">WHATSAPP</b-form-radio> -->
                  </b-form-radio-group>

                  <div class="mt-16">
                    <label>* Masukan Kode Booking</label>
                    <b-input-group>
                      <b-form-input v-model="checkin.booking_code" type="number" size="lg"></b-form-input>

                      <b-input-group-append>
                        <b-button @click="active.keyboard_checkin = !active.keyboard_checkin" variant="primary"><b-icon icon="keyboard" /></b-button>
                      </b-input-group-append>
                    </b-input-group>
                  </div>

                  <VueNumericKeypad style="position:relative"
                    :class="active.keyboard_checkin === true ? 'd-block' : 'd-none'"
                    class="mt-16"
                    :value.sync="checkin.booking_code"
                    :show.sync="keyboard.show"
                    :options="keyboard.options"
                  />
                  <b-button variant="primary" size="lg" :disabled="isDisabled" @click.prevent="validationFormCheckin" class="btn-block mt-24">AMBIL ANTRIAN</b-button>
                </div>
              </div>
            </section>
            <template v-if="print_poli === true">
              <!-- PRINT ANTRIAN POLI -->
              <div class="canvas-wrapper">
                <!-- <section id="canvas" ref="canvas" class="mt-40 p-40" style="position: relative;">
                  <section class="d-flex flex-column justify-content-start text-regular-14 text-center mt-10">
                    <p style="font-size:32px" v-if="queue_code_3">Kode Booking : {{queue_code_3.code_booking}}</p>
                  </section>
                  <h1 style="font-size:42px" class="text-center"><b>{{queue_code_3.queue}}</b></h1>
                  <p style="font-size:42px" class="text-center">No.RM <b>{{queue_code_3.norm}}</b></p>
                  <p style="font-size:42px" class="text-center">Nama <b>{{queue_code_3.name}}</b></p>
                  <p style="font-size:32px" class="text-center">SEP. <b>{{queue_code_3.sep}}</b></p>
                  <p style="font-size:32px" class="text-center">Dokter <b>{{queue_code_3.dpjp}}</b></p>
                  <p style="font-size:32px" class="text-center">Poli <b>{{queue_code_3.destination}}</b></p>
                  <p style="font-size:62px" class="text-regular-14 text-left">1_________________</p>
                  <p style="font-size:62px" class="text-regular-14 text-left">2_________________</p>
                  <p style="font-size:62px" class="text-regular-14 text-left">3_________________</p>
                  <p style="font-size:62px" class="text-regular-14 text-left">4_________________</p>
                  <p style="font-size:62px;color:white;" class="mt-120 mb-120 text-regular-14 text-left customespace">.</p>
                  <p style="font-size:62px;color:white;" class="mt-120 mb-120 text-regular-14 text-left customespace">.</p>
                </section> -->
                <div class="canvas-wrapper">
                <section id="canvas" ref="canvas" class="mt-40 p-40" style="position: relative;">

                  <div class="py-16">
                    <hr class="my-1" style="border: none; border-top: 2px dashed black;">
                    <hr class="my-1" style="border: none; border-top: 2px dashed black;">
                  </div>
                  <div class="text-center">
                    <div style="font-size:32px">&nbsp;.</div>
                    <div style="font-size:32px">&nbsp;.</div>
                    <!-- <div style="font-size:32px">Info: +62 778 4161212 / 0853 7111 1212</div> -->
                    <div style="font-size:32px"><b>RS Taman Harapan Baru</b></div>
                    <div style="font-size:32px">Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</div>
                    <div style="font-size:32px">{{ datetimenow }}</div>
                  </div>

                  <div class="py-16">
                    <hr class="my-1" style="border: none; border-top: 2px dashed black;">
                    <hr class="my-1" style="border: none; border-top: 2px dashed black;">
                  </div>

                  <div class="text-center text-uppercase">
                    <div style="font-size:32px" class="fs-28">BPJS KESEHATAN</div>
                  </div>

                  <div class="text-center mt-12">
                    <!-- <div>Kode Booking: </div> -->
                    <div style="font-size:32px">SEP: <b>{{ queue_code_3.sep }}</b></div>
                    <div style="font-size:32px">No.RM: <b>{{ queue_code_3.norm }}</b></div>
                    <div style="font-size:32px" class="text-uppercase fs-20 mt-12"><b>{{queue_code_3.name}} ({{queue_code_3.sex}})</b></div>
                    <div style="font-size:32px"><b>{{parsed_age(queue_code_3.birth_date)}} | {{queue_code_3.birth_date}}</b></div>
                    <!-- <div style="font-size:32px">Selasa, 18.30 - 10:30</div> -->
                    <div style="font-size:38px" class="fs-20 mt-12"><b>{{queue_code_3.destination}}</b></div>
                    <div style="font-size:38px"><b>{{queue_code_3.dpjp}}</b></div>
                    <div style="font-size:32px">Antrian : </div>
                    <div style="font-size:62px" class="fs-40 mt-12"><b>{{queue_code_3.queue}}</b></div>
                  </div>

                  <div class="py-16">
                    <hr class="my-1" style="border: none; border-top: 2px dashed black;">
                    <hr class="my-1" style="border: none; border-top: 2px dashed black;">
                  </div>

                  <table class="w-100 text-center" >
                    <tr>
                      <td colspan="3" style="font-size:30px">Rencana</td>
                      <td></td>
                    </tr>
                    <tr v-for="item in visitPlan">
                      <td class="text-left" style="font-size:40px"><b>{{ item }}</b></td>
                      <td style="font-size:40px">:</td>
                      <td style="font-size:37px">[&nbsp;&nbsp;]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                      <td></td>
                      <td>&nbsp;<br></td>
                      <td>&nbsp;<br></td>
                      <td>&nbsp;<br></td>
                      <td></td>
                    </tr>
                  </table>

                  <!-- <div class="text-center mt-12">Tindakan</div>
                  <div class="w-100 mt-8" style="height: 150px; border: 2px solid black;"></div> -->

                  <div style="font-size:32px" class="text-center mt-12">Harap simpan tiket ini, sampai pelayanan selesai</div>
                </section>
              </div>
              </div>
            </template>

        </div>
      </template>
    </div>
  </b-overlay>
</template>
<script>
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue';
import moment from 'moment';
import io from 'socket.io-client';
import VueNumericKeypad from "vue-numeric-keypad";
import vSelect from 'vue-select';
import { mapState } from 'vuex';

export default {
  components: {
    ModernLabel, VueNumericKeypad, vSelect
  },
  data() {
    return {
      showcaution:false,
      isDisabled: false,
      show: false,
      datenow:moment(new Date()).format('YYYY-MM-DD'),
      datetimenow:moment(new Date()).format('DD/MM-YYYY HH:mm'),
      visitPlan:['Kontrol Kembali','Farmasi','Radiologi','Laboratorium','Fisioterapi'],
      active: {
        screen: 1,
        tooltip: 'Maximize',
        keyboard_card_number: false,
        keyboard_ref_number: false,
        keyboard_bpjs: false,
        keyboard_nonbpjs: false,
        keyboard_checkin: true,
        temp_key: '',
        modalSearch: false,
      },
      refer:[],
      bpjs: {
        idcard_type_id: 2,
        idcard_number: '',
        reference_number: '',
        doctor_in_charge_id: '',
      },
      databpjs:{},
      nonBpjs: {
        idcard_type_id: 1,
        idcard_number: '',
        health_facility_id: '',
        health_facility_item_id: '',
        doctor_in_charge_id: '',
      },
      checkin: {
        booking_type_id: 1,
        booking_code: '',
      },
      jumlahSEP:0,
      dataRujukanPick:{},
      form: {
        jeniskunjungan:1,
        noKunjungan:'',
        norm:'',
        code_booking:'',
        type_register_id: 1,

        married_id: '', // trigger
        type_room_id: '', // trigger
        room_id: '', // trigger
        idcard: '',
        typeidcard: '',
        alias: '',
        name:'',
        birth_date:'',
        birth_place:'',
        sex: '',
        address: '',
        phone: '',
        responsible_id: '',
        responsible_person_idcard: '',
        responsible_person_typeidcard: '',
        responsible_person_name:'',
        responsible_person_phone: '',

        type_how_to_enter_id:'', // trigger
        how_to_enter_id:'',
        icd10_id:'',

        insuranceInput: {},
        insuranceToInput : [
          {
            is_primary: 1,
            insurance_id: 1,
            company_id: '',
            service_class_id: '',
            number_insurance: '-',
            expired: '',
          }
        ],
        insuranceTable : {
          fields: [
            { key: 'is_primary', label: '' },
            { key: 'insurance_name', label: 'Jenis Penjamin' },
            { key: 'company_name', label: 'Nama Penjamin' },
            { key: 'number_insurance', label: 'No Anggota' },
            { key: 'service_class_name', label: 'Kelas Layanan' },
            { key: 'Aksi' },
          ],
          items: [
            {
              is_primary: 1,
              insurance_name: 'Tunai/Pribadi',
              company_name: '-',
              number_insurance: '-',
              expired: '-',
              service_class_name: '-',
            }
          ]
        },
        health_facility_id:'',
        health_facility_item_id:'',
        doctor_in_charge_id:'',
        bed_id:'',
        type_emergency_id: '',
        type_emergency_case_id: '',
        case_location: '',
        name_location: '',
        appointment_id:'', //back
      },
      refer_bpjs_data:[],
      refer_internal_data:[],
      print:false,
      print_poli:false,
      user: '',
      message: '',
      messages: [],
      socket : io(process.env.VUE_APP_SOCKET_IO, { transports: ["websocket", "polling", "flashsocket"],withCredentials: true }),
      userData: JSON.parse(localStorage.getItem('userData')),
      queue_code1:'',
      queue_code_1:{},
      queue_code:'',
      queue_code_3:{},
      // keyboard config
      keyboard: {
				show: 1,
				options: {
					keyRandomize: false,
					randomizeWhenClick: false,
					fixDeleteKey: true,
					fixBlankKey: true,
					pseudoClick: false,
					pseudoClickDeleteKey: false,
					pseudoClickBlankKey: false,
					stopPropagation: true,
					vibrate: false,
					activeButtonDelay: 300,
					vibratePattern: 200,
					keyArray: [1, 2, 3, 4, 5, 6, 7, 8, 9, '', 0, -1],
					deleteKeyText: 'del',
					clearKeyText: 'clr',
				},
			},
    }
  },
  computed: {
    dateNow() {
      const date = new Date();
      const year = date.getFullYear();
      const month = date.getMonth() + 1;
      const day = date.getDate();
      const hour = date.getHours();
      const minute = date.getMinutes();
      const second = date.getSeconds();
      return `${day}-${month}-${year} ${hour}:${minute}:${second}`;
    },
    ...mapState({
        fieldsPatient: state => state.patient.fields,
        itemsPatient: state => state.patient.items,
        OptionsHealthFacility: state => state.healthfacility.options.healthfacility,
        OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,
        OptionsDoctor: state => state.employee.options.employee_bride,
    })
  },
  watch: {
    'nonBpjs.health_facility_id' (value) {
      if(value){
          return this.$store.dispatch('healthfacilityitem/getParam', value)

      }
    },
    'nonBpjs.health_facility_item_id' (value) {
      if(value){
          return this.$store.dispatch('employee/getParam', value)
      }
    },
  },
  methods: {
    checkjadwal(){
      // console.log(this.form.doctor_in_charge_id)
      // if (this.form.doctor_in_charge_id.value === "e483d663-9707-4cb4-b980-334405197131") {
      //   this.showcaution = true
      // } else {
      //   this.showcaution = false
      // }
    },
    parsed_age(input_date) {
      const birthDate = moment(input_date, 'YYYY-MM-DD');
      const currentDate = moment();

      const years = currentDate.diff(birthDate, 'years');
      birthDate.add(years, 'years');
      const months = currentDate.diff(birthDate, 'months');
      birthDate.add(months, 'months');
      const days = currentDate.diff(birthDate, 'days');

      return `${years} th ${months} bln ${days} hr`;
    },
    pickControlTableKontrol(data) {
      this.form.noKunjungan = data.noSuratKontrol
      this.$store.dispatch('employee/getParamScheduleDay', data.poliTujuan)
    },
    pickControlTableReferInternal(data) {
      this.form.noKunjungan = data.no_refer
      this.form.health_facility_item_id = {value:data.health_facility_item_id}
      this.form.jeniskunjungan = 2
      console.log('jenis kunjungan: '+this.form.jeniskunjungan)
      this.$store.dispatch('employee/getParamScheduleDay', data.poliTujuan)
    },
    pickControlTable(data) {
      alert("Pilih Nomor Rujukan "+data.noKunjungan)
      this.dataRujukanPick = data
      this.form.noKunjungan = data.noKunjungan
      this.$store.dispatch('employee/getParamScheduleDay', data.kd_poli)
      this.$store.dispatch('employee/getHealthFacilityId', data.kd_poli).then(res => {
        this.form.health_facility_item_id = {value:res[0].id}
        this.$store.dispatch('patient/getTotalSEP', {jenisrujukan:data.pelayanan === '2' ? 2 : 2,norujukan:data.noKunjungan}).then(res => {
          this.jumlahSEP = res.response.jumlahSEP
          //FKTP
          if (res.response.jumlahSEP === '0' && data.pelayanan === '2') {
            this.form.jeniskunjungan = 1
          }
          //RS
          if (res.response.jumlahSEP === '0' && data.pelayanan === '1') {
            this.form.jeniskunjungan = 4
          }
          if (res.response.jumlahSEP >= 1) {
            this.$store.dispatch('patient/bpjs', {url:'RujukanKontrolList',idx:data.noKartu})
            .then(response_refer => {
              if (response_refer.status == 201) {
                alert(response_refer.message)
              } else {
                this.refer_bpjs_data = response_refer.data.response.list
              }
            })
            this.$store.dispatch('registration/getReferInternalByNoka', data.noKartu)
            .then(response_refer_internal => {
                this.refer_internal_data = response_refer_internal
            })
            this.form.jeniskunjungan = 3
          }
          if (res.response.jumlahSEP >= 1 && this.form.jeniskunjungan === '2') {
            this.form.jeniskunjungan = 2
          }
          console.log('total sep: '+res.response.jumlahSEP)
          console.log('jenis kunjungan: '+this.form.jeniskunjungan)
        })
      })
    },
    pickControlTableUpdate(data) {
      alert("Pilih Nomor Rujukan"+data.noKunjungan)
      this.dataRujukanPick = data
      this.$store.dispatch('employee/getParamScheduleDay', data.kd_poli)
      this.$store.dispatch('employee/getHealthFacilityId', data.kd_poli).then(res => {
        this.form.health_facility_item_id = {value:res[0].id}
        this.$store.dispatch('patient/getTotalSEP', {jenisrujukan:data.pelayanan === '2' ? 2 : 2,norujukan:data.noKunjungan}).then(res => {
          this.jumlahSEP = res.response.jumlahSEP
          //FKTP
          if (res.response.jumlahSEP === '0' && data.pelayanan === '2') {
            this.form.jeniskunjungan = 1
          }
          //RS
          if (res.response.jumlahSEP === '0' && data.pelayanan === '1') {
            this.form.jeniskunjungan = 4
          }
          if (res.response.jumlahSEP >= 1) {
            this.$store.dispatch('patient/bpjs', {url:'RujukanKontrolList',idx:data.noKartu})
            .then(response_refer => {
              if (response_refer.status == 201) {
                alert(response_refer.message)
              } else {
                this.refer_bpjs_data = response_refer.data.response.list
              }
            })
            this.$store.dispatch('registration/getReferInternalByNoka', data.noKartu)
            .then(response_refer_internal => {
                this.refer_internal_data = response_refer_internal
            })
            this.form.jeniskunjungan = 3
          }
          if (res.response.jumlahSEP >= 1 && this.form.jeniskunjungan === '2') {
            this.form.jeniskunjungan = 2
          }
          console.log('total sep: '+res.response.jumlahSEP)
          console.log('jenis kunjungan: '+this.form.jeniskunjungan)
        })
      })
    },
    UpdateKontrol(data,data_refer) {
      this.$store.dispatch('patient/updateControl', {
        jnsPelayanan:data.jnsKontrol,
        noSuratKontrol:data.noSuratKontrol,
        noSep:data.noSepAsalKontrol,
        kodeDokter:data.kodeDokter,
        poliKontrol:data.poliTujuan,
        tglRencanaKontrol:this.datenow,
        user:'system'
      }).then(res => {
        this.pickControlTableUpdate(data_refer)
      })
    },
    pickControl(data) {
      let hakKelas = ''
      if (data.kodehakKelas == '1') {
        hakKelas = {value:'8d3b8d25-4606-4db5-9ec1-59ec22514210',title:'Kelas I'}
      } else if (data.kodehakKelas == '2') {
        hakKelas = {value:'f39fbb8f-082e-4c71-8802-92943b6e4683',title:'Kelas II'}
      } else if (data.kodehakKelas == '3') {
        hakKelas = {value:'d168732a-fdef-456d-836c-750e003187e0',title:'Kelas III'}
      }
      this.form.insuranceTable.items = []
      // this.form.insuranceTable.items.push({
      //   is_primary: 1,
      //   insurance_id: 2,
      //   insurance_name: 'Asuransi',
      //   company_id: '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8',
      //   company_name: 'BPJS Kesehatan',
      //   number_insurance: data.noKartu,
      //   expired: data.tglTAT,
      //   service_class_name: data.keteranganhakKelas,
      //   noKunjungan: data.noKunjungan
      // })
      this.form.insuranceToInput = []
      // this.form.insuranceToInput.push({
      //   is_primary: 1,
      //   insurance_id: 2,
      //   company_id: '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8',
      //   number_insurance: data.noKartu,
      //   expired: data.tglTAT,
      //   service_class_id: hakKelas,
      //   noKunjungan: data.noKunjungan
      // })
      console.log(data)
      console.log(this.form.insuranceToInput)
      if (data.noKartu) {
        this.form.type_how_to_enter_id = {value:'2', title:'Rujukan Puskesmas/Klinik'}
        this.$store.dispatch('howtoenter/getParam', this.form.type_how_to_enter_id.value)
        this.form.how_to_enter_id = {value:'83ded3be-bf34-4e95-ba22-58f3cece3197', title:data.nmProviderPerujuk}
        this.active.refer_bpjs = false
        // this.$store.dispatch('patient/getBackdateNoka', data.noKartu)
        // .then(res => {
        //     this.$store.dispatch('patient/getAll', {norm:res})
        //     .then(res => {
        //     },
        //     err => {
        //     })
        // })
      }
    },
    pickProccess (data) {
      console.log(data)
    },
    openKeyBpjs(param) {
      if (this.active.temp_key === param) {
        this.active.temp_key = '';
        this.active.keyboard_card_number = false;
        this.active.keyboard_ref_number = false;
      } else {
        this.active.temp_key = param;
        this.active.keyboard_card_number = param === 'card';
        this.active.keyboard_ref_number = param === 'ref';
      }
    },

    fullScreenMode(){
      // console.log('clicked');
      if (!document.fullscreenElement &&
        !document.mozFullScreenElement &&
        !document.webkitFullscreenElement &&
        !document.msFullscreenElement) {
        if (document.documentElement.requestFullscreen) {
            document.documentElement.requestFullscreen();
        } else if (document.documentElement.mozRequestFullScreen) {
            document.documentElement.mozRequestFullScreen();
        } else if (document.documentElement.webkitRequestFullscreen) {
            document.documentElement.webkitRequestFullscreen();
        } else if (document.documentElement.msRequestFullscreen) {
            document.documentElement.msRequestFullscreen();
        }
        this.active.tooltip = 'Minimize'
      } else {
          if (document.exitFullscreen) {
              document.exitFullscreen();
          } else if (document.mozCancelFullScreen) {
              document.mozCancelFullScreen();
          } else if (document.webkitExitFullscreen) {
              document.webkitExitFullscreen();
          } else if (document.msExitFullscreen) {
              document.msExitFullscreen();
          }
          this.active.tooltip = 'Maximize'
      }
    },
    validationForm () {
      const formData = this.form
      console.log('fromdata: ', formData);
      this.isDisabled = true
      this.$store.dispatch('registration/formPostPutAPM', formData)
      .then(res => {
        console.log('response: ', res);
        // this.notification('Success','CheckIcon','success','You have successfully')
        if (res.status == 200 || res.status == 201) {
          this.notification('Success','CheckIcon','success','You have successfully')
          // const url = this.$router.resolve({ path: '/prints/struk/'+ res.data.registration_destination_id }).href;
          // window.open(url);
          this.print_poli = true
          this.queue_code_3 = res.data
          setTimeout(() => { window.print();}, 100);
          setTimeout(() => { this.print_poli = false;}, 200);
        } else if (res.status == 404) {
          this.notification('Warning','AlertCircleIcon','warning','Registration can be done on the recorded appointment date')
        } else if (res.status == 422) {
          this.notification('Warning','AlertCircleIcon','warning',"The doctor's schedule was missed")
        }
          this.isDisabled = false
        },
      err => {
        this.isDisabled = false
        this.notification('Failed','CloseIcon','danger',err)
      })
    },
    validationFormCheckin () {
      const formData = this.checkin
      console.log('fromdata: ', formData);
      this.isDisabled = true
      this.$store.dispatch('registration/formPostPutCheckinFinger', formData)
      .then(res => {
        console.log('response: ', res);
        // this.notification('Success','CheckIcon','success','You have successfully')
        // if (res.data.response.kode === '0') {
        //   this.notification('Warning','AlertCircleIcon','warning',res.data.response.status)
        // } else if (res.data.response.kode === '1') {
          this.validationFormCheckinOK()
        // }
          this.isDisabled = false
        },
        err => {
          this.isDisabled = false
          this.notification('Failed','CloseIcon','danger',err)
        })
    },
    validationFormCheckinOK () {
      const formData = this.checkin
      console.log('fromdata: ', formData);
      this.isDisabled = true
      this.$store.dispatch('registration/formPostPutCheckin', formData)
      .then(res => {
        console.log('response: ', res);
        // this.notification('Success','CheckIcon','success','You have successfully')
        if (res.status == 200 || res.status == 201) {
          this.notification('Success','CheckIcon','success','You have successfully')
          this.print_poli = true
          this.queue_code_3 = res.data
          setTimeout(() => { window.print();}, 100);
          setTimeout(() => { this.print_poli = false;}, 200);
          this.notification('Warning','AlertCircleIcon','warning','Registration can be done on the recorded appointment date')
        } else if (res.status == 422) {
          this.notification('Warning','AlertCircleIcon','warning',"The doctor's schedule was missed")
        }
          this.isDisabled = false
        },
      err => {
        this.isDisabled = false
        this.notification('Failed','CloseIcon','danger',err)
      })
    },

    changeToScreen(number){
      this.active.screen = number

      if (number == 4) {
        this.bpjs.idcard_type_id = 2
        this.bpjs.idcard_number= ''
        this.bpjs.reference_number = ''
        this.bpjs.doctor_in_charge_id = ''
        this.$store.dispatch('employee/getParam', '') // nongol semua tanpa pengaruh getparam dari screen 5

      } else if (number == 5) {
        this.nonBpjs.idcard_type_id = 1
        this.nonBpjs.idcard_number = ''
        this.nonBpjs.health_facility_id = ''
        this.nonBpjs.health_facility_item_id = ''
        this.nonBpjs.doctor_in_charge_id = ''

      } else if (number == 6) {
        this.checkin.booking_type_id =  1
        this.checkin.booking_code = ''
      }
    },
    searchByCardNumber(card_type) {
      this.refer_bpjs_data = []
      this.refer_internal_data = []
      this.jumlahSEP = 0
      this.refer = []
      this.show = true
      if (card_type) {
        this.active.modalSearch = true
        let url = ''
        if (card_type == 1) {
          url = 'nik'
        } else if (card_type == 2) {
          url = 'noka'
        }
        this.show = false
        this.$store.dispatch('patient/bpjs', {url:url,idx:this.bpjs.idcard_number}) .then(response => {
          if (response.metaData.code === '200') {
            alert(response.response.peserta.statusPeserta.keterangan)
            this.form.phone = response.response.peserta.mr.noTelepon
            this.databpjs = response.response.peserta
            this.$store.dispatch('patient/getAll', {idcard:response.response.peserta.nik,bride_no_bpjs:response.response.peserta.noKartu})
            this.$store.dispatch('patient/bpjs', {url:'RujukanList',idx:response.response.peserta.noKartu}).then(response_refer => {
              if (response_refer.status == 201) {
              } else {
                this.refer = response_refer.data
                console.log(this.refer)
              }
              this.show = false
            })
          }else{
            this.show = false
          }
        })
      }
    },
    getPatientOnReg (value) {
      this.pickControl(this.dataRujukanPick)
      this.form.alias = value.alias
      this.form.name = value.name
      this.form.norm = value.norm
      this.form.birth_date = value.birth_date
      this.form.sex = value.sex
      this.form.married_id = value.married_id
      // this.form.phone = value.phone
      this.form.typeidcard = value.typeidcard
      this.form.idcard = value.idcard
      this.active.refer_bpjs = false
      this.active.is_bpjs = false
      this.form.type_how_to_enter_id = {value:2,title:'Rujukan Puskesmas/Klinik'}
      this.$store.dispatch('howtoenter/getParam', 2)
      this.form.how_to_enter_id = {value:'cd67965b-b409-474c-a6a9-b4be1a1a9d5a',title:'Puskesmas A'}
      let hakKelas = ''
      let nmhakKelas = ''
      if (this.databpjs.hakKelas.kode == '1') {
        hakKelas = '8d3b8d25-4606-4db5-9ec1-59ec22514210'
        nmhakKelas = '1'
      } else if (this.databpjs.hakKelas.kode == '2') {
        hakKelas = 'f39fbb8f-082e-4c71-8802-92943b6e4683'
        nmhakKelas = '2'
      } else if (this.databpjs.hakKelas.kode == '3') {
        hakKelas = 'd168732a-fdef-456d-836c-750e003187e0'
        nmhakKelas = '3'
      }
      // this.form.responsible_id = value.patient_responsible_person.responsible_id
      // this.form.responsible_person_name = value.patient_responsible_person.name
      // this.form.responsible_person_idcard = value.patient_responsible_person.idcard
      // this.form.responsible_person_typeidcard = value.patient_responsible_person.typeidcard
      // this.form.responsible_person_phone = value.patient_responsible_person.phone
      // this.form.insuranceTable.items = value.patient_insurance
      // this.form.insuranceToInput = value.patient_insurance
      this.form.insuranceToInput.push({
        company_id: "1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8",
        company_id_cob: null,
        company_name: "BPJS Kesehatan",
        expired: this.databpjs.tglTAT,
        institute_branch_id: "240b8d8b-245e-42ba-85e6-2ba7d8ad5640",
        insurance_id: 2,
        insurance_name: "Asuransi",
        is_cob: 0,
        is_primary: 1,
        is_up: 0,
        norm: value.norm,
        number_insurance: this.databpjs.noKartu,
        service_class_id: hakKelas,
        service_class_id_right: null,
        service_class_name: nmhakKelas
      })
      this.form.insuranceTable.items.push({
        company_id: "1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8",
        company_id_cob: null,
        company_name: "BPJS Kesehatan",
        expired: this.databpjs.tglTAT,
        institute_branch_id: "240b8d8b-245e-42ba-85e6-2ba7d8ad5640",
        insurance_id: 2,
        insurance_name: "Asuransi",
        is_cob: 0,
        is_primary: 1,
        is_up: 0,
        norm: value.norm,
        number_insurance: this.databpjs.noKartu,
        service_class_id: hakKelas,
        service_class_id_right: null,
        service_class_name: nmhakKelas
      })
      this.form.insuranceToInput.map(option => {
        // New properties to be added
        const newPropsObj = {
          noKunjungan:this.form.noKunjungan
        };
        // Assign new properties and return
        return Object.assign(option, newPropsObj);
      });
      // this.$emit('getPatientOnReg', data)
      console.log('getPatientOnReg',value);
      console.log(this.form.insuranceToInput)
      console.log(this.form.insuranceTable.items)
      this.active.modalSearch = false
    },
    backToScreen(number){
      if (number == 2 || number == 3) {
        this.changeToScreen(1)
      } else if (number == 4 || number == 5 || number == 6) {
        this.changeToScreen(3)
      }
    },
    notification (a,b,c,d) {
      this.$toast({
        component: ToastificationContent,
        employee: 'top-right',
        props: { title: a, icon: b, variant: c, text: d}
      })
    },
    onClick(index) {
      this.active.modalSearch = false
      this.print = true
      const formData = {type_loket_id:index}
      this.isDisabled = true
      this.$store.dispatch('helper/formPostQueueLoket', formData)
      .then(res => {
        console.log('response: ', res);
        // this.notification('Success','CheckIcon','success','You have successfully')
        if (res.status == 200 || res.status == 201) {
          // PRINT ANTRIAN ADMISI
          this.queue_code_1 = res.data
          this.socket.emit('SEND_ANJUNGAN', this.queue_code_1);
          // this.queue_code_1 = {code_booking:'01',code_booking:'01'}
          setTimeout(() => { window.print();}, 100);
          setTimeout(() => { this.print = false;}, 200);
          this.isDisabled = false
        }
      },
      err => {
        this.isDisabled = false
        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
      })
    },
  },
  mounted() {
    // const buttons = this.$refs.buttons;
    // buttons.classList.add('d-none');
    // this.socket.on('MESSAGE', (data) => {
    //   this.messages = [...this.messages, data];
    //   console.log(this.messages)
    //   // you can also do this.messages.push(data)
    // });
  },
  created () {
        this.$store.dispatch('healthfacility/getParam', 'healthfacility')
        // this.$store.dispatch('healthfacilityitem/getParam', '40f06534-07ec-4221-ad9d-dea08196079f')
    },
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>

<style>
@import './../../../../@core/css/main/main.min.css';

/* table,
th,
td {
    border: 1px solid black;
} */
 @media print {
    @page{
      size: 80mm 210mm;
      margin: 0;
      padding: 0;
    }
    body{
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      /* align-items: center; */
    }
    p {
      font-size: 40px;
    }
    h1 {
      font-size: 44px;
    }
    .customespace {
      color: white;
    }
 }

 .full-page {
  width: 100%;
  height: 100vh;
 }
td {
    padding: 2px 0;
    vertical-align: text-top;
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
    width: 21.2cm !important;
    /* height: 29.7cm !important; */
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
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
</style>