<template>
  <div>
    <div class="label-head my-16 my-lg-24">
      <span class="px-16 text-bold-24 text-neutral-700">Penjamin</span>
    </div>
    <!-- ////////  -->
    <div class="my-16 my-lg-24">
      <span class="px-16 text-bold-24 text-neutral-700">Surat Eligibilitas Peserta</span>
      <!-- <span class="px-16 text-bold-24 text-neutral-700">Surat Eligibilitas Peserta : 0301R01690444513 (Rawat Jalan)</span> -->
    </div>
    <div class="card-body shadow-sm rounded">
      <div class="row mx-0">
        <div class="col-12 col-lg">
          <div class="row">
            <div class="col">
              <!-- <div class="text-medium-18">Surat Eligibilitas Peserta : 0301R01690444513 (Rawat Jalan)</div> -->
              <div class="row mt-10">
                <section class="col-lg-3">
                  <div class="label-head">
                    <span class="px-16 text-bold-24 text-neutral-700">Data Peserta</span>
                  </div>
                  <b-list-group class="mt-16 text-uppercase">
                    <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                      <feather-icon icon="UserIcon" class="mr-2" /> {{ pesertaData.nama }} ({{ pesertaData.sex }})
                    </b-list-group-item>
                    <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                      <feather-icon icon="UserIcon" class="mr-2" /> {{ pesertaData.noKartu }}
                    </b-list-group-item>
                    <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                      <feather-icon icon="ClipboardIcon" class="mr-2" /> {{ pesertaData.nik }}
                    </b-list-group-item>
                    <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                      <feather-icon icon="CalendarIcon" class="mr-2" /> {{ pesertaData.tglLahir }}
                    </b-list-group-item>
                    <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                      <feather-icon icon="FolderPlusIcon" class="mr-2" /> {{ pesertaData.hakKelas && pesertaData.hakKelas.keterangan ? pesertaData.hakKelas.keterangan : '' }}
                    </b-list-group-item>
                    <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                      <feather-icon icon="BriefcaseIcon" class="mr-2" /> {{  pesertaData.provUmum.nmProvider }} ( {{pesertaData.provUmum.kdProvider}} )
                    </b-list-group-item>
                    <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                      <feather-icon icon="CalendarIcon" class="mr-2" /> {{ pesertaData.tglTMT }} s.d {{pesertaData.tglTAT}}
                    </b-list-group-item>
                    <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                      <feather-icon icon="CalendarIcon" class="mr-2" /> {{ pesertaData.jenisPeserta && pesertaData.jenisPeserta.keterangan ? pesertaData.jenisPeserta.keterangan : '' }}
                    </b-list-group-item>
                  </b-list-group>
                </section>
                <section class="col-lg-9 mt-32 mt-lg-0">
                  <div class="label-head ml-10">
                    <span class="px-16 text-bold-24 text-neutral-700">Data Peserta</span>
                  </div>
                  <div class="d-flex justify-content-center">
                    <div class="col-lg-12">
                      <template v-if="regData.health_facility_item_name == 'IGD'">
                        <div class="row">
                          <div class="col-lg-12" style="display:none;">
                            <div>
                              <label class="c-form-label"><span class="text-danger-500">*</span></label>
                              <b-input-group>
                                <b-form-input v-model="form.poli_destination" readonly></b-form-input>
                              </b-input-group>
                            </div>
                          </div>
                          <div class="col-lg-12" style="display:none;">
                            <div class="mt-12">
                              <label class="c-form-label">Tanggal SEP</label>
                              <b-form-input v-model="form.tglSep" type="date"></b-form-input>
                            </div>
                          </div>
                          <div class="col-lg-12">
                            <div class="mt-12">
                              <label class="c-form-label">No MR <span class="text-danger-500">*</span></label>
                              <b-input-group>
                                <b-form-input v-model="form.noMr"></b-form-input>
                                <b-input-group-append is-text>
                                  <b-form-checkbox v-model="form.cob">
                                    <span>Peserta COB</span>
                                  </b-form-checkbox>
                                </b-input-group-append>
                              </b-input-group>
                            </div>
                          </div>
                          <div class="col-lg-12" style="display:none;">
                            <div class="mt-12">
                              <label class="c-form-label">DPJP <span class="text-danger-500">*</span></label>
                              <v-select v-model="form.doctor_in_charge_id" aria-readonly="true" :options="OptionsDoctor" :reduce="OptionsDoctor => OptionsDoctor.value" label="title" placeholder="-- Dokter --"></v-select>
                            </div>
                          </div>
                          <div class="col-lg-12">
                            <div class="mt-12">
                              <label class="c-form-label">Diagnosa <span class="text-danger-500">*</span></label>
                              <v-select v-model="form.diagAwal" @search="onSearchIcd10" :options="OptionsIcd10" :reduce="OptionsIcd10 => OptionsIcd10.value" label="title" placeholder="-- Diagnosas --"></v-select>
                            </div>
                          </div>
                          <div class="col-lg-12">
                            <div class="mt-12">
                              <label class="c-form-label">No Telepon <span class="text-danger-500">*</span></label>
                              <b-form-input v-model="form.noTelp"></b-form-input>
                            </div>
                          </div>
                          <div class="col-lg-12">
                            <div class="mt-12">
                              <label class="c-form-label">Catatan</label>
                              <b-textarea v-model="form.catatan"></b-textarea>
                            </div>
                          </div>
                          <div class="col-lg-12" style="display:none">
                            <!-- <div class="col-lg-12" v-if="!form.control"> -->
                            <div class="mt-12">
                              <label class="c-form-label">Tujuan Kunjungan <span class="text-danger-500">*</span></label>
                              <v-select v-model="form.tujuanKunj" :options="OptionsPurpose" :reduce="OptionsPurpose => OptionsPurpose.value" label="title" placeholder="-- Tujuan Kunjungan --"></v-select>
                            </div>
                          </div>
                          <div class="col-lg-12" v-if="form.tujuanKunj === 0" style="display:none">
                            <div class="mt-12">
                              <label class="c-form-label">Prosedur <span class="text-danger-500">*</span></label>
                              <v-select v-model="form.flagProcedure" :options="OptionsProcedure" :reduce="OptionsProcedure => OptionsProcedure.value" label="title" placeholder="-- Prosedur --"></v-select>
                            </div>
                          </div>
                          <div class="col-lg-12" v-if="form.flagProcedure == 1 && form.tujuanKunj == 1">
                            <div class="mt-12">
                              <label class="c-form-label">Prosedur Tidak Berkelanjutan<span class="text-danger-500">*</span></label>
                              <v-select v-model="form.kdPenunjang" :options="OptionsProcedureNot" :reduce="OptionsProcedureNot => OptionsProcedureNot.value" label="title" placeholder="-- Prosedur --"></v-select>
                            </div>
                          </div>
                          <div class="col-lg-12" v-if="form.flagProcedure == 2 && form.tujuanKunj == 1">
                            <div class="mt-12">
                              <label class="c-form-label">Prosedur Berkelanjutan<span class="text-danger-500">*</span></label>
                              <v-select v-model="form.kdPenunjang" :options="OptionsProcedureCon" :reduce="OptionsProcedureCon => OptionsProcedureCon.value" label="title" placeholder="-- Prosedur --"></v-select>
                            </div>
                          </div>
                          <div class="col-lg-12" v-if="form.tujuanKunj == 2">
                            <div class="mt-12">
                              <label class="c-form-label">Assesmen Pelayanan <span class="text-danger-500">*</span></label>
                              <v-select v-model="form.assesmentPel" :options="OptionsConsuleDoctor" :reduce="OptionsConsuleDoctor => OptionsConsuleDoctor.value" label="title" placeholder="-- Assesmen Pelayanan --"></v-select>
                            </div>
                          </div>
                          <div class="col-lg-12">
                            <div class="mt-12">
                              <label class="c-form-label">Jaminan <span class="text-danger-500">*</span></label>
                              <v-select v-model="form.lakaLantas" :options="OptionsKLL" @input="inputKLL()" :reduce="OptionsKLL => OptionsKLL.value" label="title" placeholder="-- Jaminan --"></v-select>
                            </div>
                          </div>
                          <div v-if="form.lakaLantas === '1' || form.lakaLantas === '2' || form.lakaLantas === '3'" class="col-lg-12">
                            <div class="col-lg-12">
                              <div class="mt-12">
                                <label class="c-form-label">No LP <span class="text-danger-500">*</span></label>
                                <b-form-input v-model="form.noLP"></b-form-input>
                              </div>
                            </div>
                            <div class="col-lg-12">
                              <div class="mt-12">
                                <label class="c-form-label">Tanggal Kejadian <span class="text-danger-500">*</span></label>
                                <b-form-input type="date" v-model="form.tglKejadian" @input="validateTglKejadian"></b-form-input>
                              </div>
                            </div>
                            <div class="col-lg-12">
                              <div class="mt-12">
                                <b-form-group label="Suplesi" v-slot="{ ariaDescribedby }">
                                  <b-form-radio v-model="form.suplesi" :aria-describedby="ariaDescribedby" name="some-radios" value="1">Ya</b-form-radio>
                                  <b-form-radio v-model="form.suplesi" :aria-describedby="ariaDescribedby" name="some-radios" value="0">Tidak</b-form-radio>
                                </b-form-group>
                              </div>
                            </div>
                            <div class="col-lg-12" v-if="form.suplesi == 1">
                              <div class="mt-12">
                                <label class="c-form-label">No.SEP yang Jika Terdapat Suplesi <span class="text-danger-500">*</span></label>
                                <b-form-input v-model="form.noSepSuplesi"></b-form-input>
                              </div>
                            </div>
                            <div class="col-lg-12" v-if="form.suplesi == 1">
                              <div class="mt-12">
                                <label class="c-form-label">Lokasi Kejadian <span class="text-danger-500">*</span></label>
                                <v-select v-model="form.kodepropinsi" :options="OptionsPropinsi" :reduce="OptionsPropinsi => OptionsPropinsi.value" label="title" placeholder="-- Provinsi --"></v-select>
                                <v-select v-model="form.kodekabupaten" :options="OptionsKabupaten" :reduce="OptionsKabupaten => OptionsKabupaten.value" label="title" placeholder="-- Kabupaten --"></v-select>
                                <v-select v-model="form.kodekecamatan" :options="OptionsKecamatan" :reduce="OptionsKecamatan => OptionsKecamatan.value" label="title" placeholder="-- Kecamatan --"></v-select>
                              </div>
                            </div>
                            <div class="col-lg-12">
                              <div class="mt-12">
                                <label class="c-form-label">Catatan</label>
                                <b-textarea v-model="form.catatan"></b-textarea>
                              </div>
                            </div>
                          </div>
                        </div>
                      </template>
                      <template v-if="regData.health_facility_name == 'RAWAT JALAN'">
                        <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-6">
                              <div class="row">
                                <div class="col-lg-12">
                                  <div>
                                    <label class="c-form-label">Spesialis/SubSpesialis <span class="text-danger-500">*</span></label>
                                    <b-input-group>
                                      <b-input-group-prepend is-text>
                                        <b-form-checkbox v-model="form.eksekutif">
                                          <span>Eksekutif</span>
                                        </b-form-checkbox>
                                      </b-input-group-prepend>
                                      <b-form-input v-model="form.poli_destination" readonly></b-form-input>
                                    </b-input-group>
                                  </div>
                                </div>
                                <div class="col-lg-12">
                                  <div class="mt-12">
                                    <label class="c-form-label">Kontrol</label>
                                    <b-form-checkbox @change="is_control" v-model="form.control">
                                      <span>Ya</span>
                                    </b-form-checkbox>
                                  </div>
                                </div>
                                <div class="col-lg-12" v-if="form.control">
                                  <div class="mt-12">
                                    <label class="c-form-label">No Surat <span class="text-danger-500">*</span></label>
                                    <b-input-group>
                                      <b-form-input v-model="form.noSurat"></b-form-input>
                                      <!-- <b-button variant="primary-600" @click="isRefcontrol()" class="mb-3">Cari</b-button> -->
                                    </b-input-group>
                                  </div>
                                </div>
                                <b-modal v-model="active.refcontrol" @close="isRefcontrol()" title="Referensi Kontrol" hide-footer>
                                  <div class="row">
                                    <div class="col-12 col-lg-12 mt-1">
                                      <label class="c-form-label">Nomor Anggota </label>
                                      <b-input-group>
                                        <b-form-input v-model="pesertaData.noKartu"  @keyup.enter="getRefControl()"></b-form-input>
                                        <b-input-group-append>
                                            <b-button variant="primary-500" @click="getRefControl()"><feather-icon icon="SearchIcon" /></b-button>
                                        </b-input-group-append>
                                      </b-input-group>
                                      <b-table bordered responsive small head-variant="light" style="height: 400px" :items="refer_bpjs_data" :fields="ReferBpjsTable.fields" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                                        <template #cell(action)="row">
                                          <div>
                                            <b-button size="sm" variant="primary-500" @click="pickControl(row.item)">Pilih Kontrol</b-button>
                                          </div>
                                        </template>
                                      </b-table>
                                    </div>
                                  </div>
                                </b-modal>
                                <div class="col-lg-12">
                                  <div>
                                    <label class="c-form-label">DPJP <span class="text-danger-500">*</span></label>
                                    <v-select v-model="form.doctor_in_charge_id" aria-readonly="true" :options="OptionsDoctor" :reduce="OptionsDoctor => OptionsDoctor.value" label="title" placeholder="-- Dokter --"></v-select>
                                  </div>
                                </div>
                                <div class="col-lg-12">
                                  <div class="mt-12">
                                    <label class="c-form-label">Asal Rujukan <span class="text-danger-500">*</span></label>
                                    <v-select v-model="form.ppkRujukan" aria-readonly="true" :options="OptionsFaskes" :reduce="OptionsFaskes => OptionsFaskes.value" label="title" placeholder="-- Faskes Rujukan --"></v-select>
                                  </div>
                                </div>
                                <div class="col-lg-12">
                                  <div class="mt-12">
                                    <label class="c-form-label">Tanggal Rujukan</label>
                                    <b-form-input v-model="form.tglRujukan" type="date"></b-form-input>
                                  </div>
                                </div>
                                <div class="col-lg-12">
                                  <div class="mt-12">
                                    <label class="c-form-label">Asal rujukan <span class="text-danger-500">*</span></label>
                                    <v-select v-model="form.asalRujukan" aria-readonly="true" :options="OptionsAsalRujukan" :reduce="OptionsAsalRujukan => OptionsAsalRujukan.value" label="title" placeholder="-- Asal Rujukan --"></v-select>
                                  </div>
                                </div>
                                <div class="col-lg-12">
                                  <div class="mt-12">
                                    <label class="c-form-label">No Rujukan <span class="text-danger-500">*</span></label>
                                    <b-form-input v-model="form.noRujukan"></b-form-input>
                                  </div>
                                </div>
                                <div class="col-lg-12">
                                  <div class="mt-12">
                                    <label class="c-form-label">Tanggal SEP</label>
                                    <b-form-input v-model="form.tglSep" type="date"></b-form-input>
                                  </div>
                                </div>
                                <div class="col-lg-12">
                                  <div class="mt-12">
                                    <label class="c-form-label">No MR <span class="text-danger-500">*</span></label>
                                    <b-input-group>
                                      <b-input-group-append is-text>
                                        <b-form-checkbox v-model="form.cob">
                                          <span>Peserta COB</span>
                                        </b-form-checkbox>
                                      </b-input-group-append>
                                      <b-form-input v-model="form.noMr"></b-form-input>
                                    </b-input-group>
                                  </div>
                                </div>
                                <div class="col-lg-12" style="display:none">
                                  <div>
                                    <label class="c-form-label">Asal Rujukan</label>
                                    <b-form-select v-model="form.ppkRujukan"></b-form-select>
                                  </div>
                                </div>
                                <div class="col-lg-12" style="display:none">
                                  <div class="mt-12">
                                    <label class="c-form-label">PPK Asal Rujukan <span class="text-danger-500">*</span></label>
                                    <b-form-input v-model="form.ppk_origin"></b-form-input>
                                  </div>
                                </div>
                                <div class="col-lg-12" style="display:none">
                                  <div class="mt-12">
                                    <label class="c-form-label">Kontrol</label>
                                    <b-form-checkbox v-model="form.control">Ya</b-form-checkbox>
                                  </div>
                                </div>
                                <div class="col-lg-12">
                                  <div class="mt-12">
                                    <label class="c-form-label">Diagnosa <span class="text-danger-500">*</span></label>
                                    <v-select v-model="form.diagAwal" @search="onSearchIcd10" :options="OptionsIcd10" :reduce="OptionsIcd10 => OptionsIcd10.value" label="title" placeholder="-- Diagnosas --"></v-select>
                                  </div>
                                </div>
                                <div class="col-lg-12">
                                  <div class="mt-12">
                                    <label class="c-form-label">No Telepon <span class="text-danger-500">*</span></label>
                                    <b-form-input v-model="form.noTelp"></b-form-input>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="col-lg-6">
                              <div class="row">
                                <div class="col-lg-12" v-if="form.poli_destination === 'POLIKLINIK MATA'">
                                  <div class="mt-12">
                                    <b-form-group label="Katarak" v-slot="{ ariaDescribedby }">
                                      <b-form-radio v-model="form.katarak" :aria-describedby="ariaDescribedby" name="some-radios" value="0">Tidak</b-form-radio>
                                      <b-form-radio v-model="form.katarak" :aria-describedby="ariaDescribedby" name="some-radios" value="1">Iya</b-form-radio>
                                    </b-form-group>
                                  </div>
                                </div>
                                <div class="col-lg-12">
                                  <div class="mt-12">
                                    <label class="c-form-label">Catatan</label>
                                    <b-textarea v-model="form.catatan"></b-textarea>
                                  </div>
                                </div>
                                <!-- <div class="col-lg-12" v-if="!form.control"> -->
                                <div class="col-lg-12">
                                  <div class="mt-12">
                                    <label class="c-form-label">Tujuan Kunjungan <span class="text-danger-500">*</span></label>
                                    <v-select v-model="form.tujuanKunj" :options="OptionsPurpose" :reduce="OptionsPurpose => OptionsPurpose.value" label="title" placeholder="-- Tujuan Kunjungan --"></v-select>
                                  </div>
                                </div>
                                <div class="col-lg-12" v-if="form.tujuanKunj === 1  || form.tujuanKunj == 2">
                                  <div class="mt-12">
                                    <label class="c-form-label">Prosedur <span class="text-danger-500">*</span></label>
                                    <v-select v-model="form.flagProcedure" :options="OptionsProcedure" :reduce="OptionsProcedure => OptionsProcedure.value" label="title" placeholder="-- Prosedur --"></v-select>
                                  </div>
                                </div>
                                <div class="col-lg-12" v-if="form.flagProcedure == 1 && form.tujuanKunj == 2">
                                  <div class="mt-12">
                                    <label class="c-form-label">Prosedur Tidak Berkelanjutan<span class="text-danger-500">*</span></label>
                                    <v-select v-model="form.kdPenunjang" :options="OptionsProcedureNot" :reduce="OptionsProcedureNot => OptionsProcedureNot.value" label="title" placeholder="-- Prosedur --"></v-select>
                                  </div>
                                </div>
                                <div class="col-lg-12" v-if="form.flagProcedure == 2 && form.tujuanKunj == 2">
                                  <div class="mt-12">
                                    <label class="c-form-label">Prosedur Berkelanjutan<span class="text-danger-500">*</span></label>
                                    <v-select v-model="form.kdPenunjang" :options="OptionsProcedureCon" :reduce="OptionsProcedureCon => OptionsProcedureCon.value" label="title" placeholder="-- Prosedur --"></v-select>
                                  </div>
                                </div>
                                <div class="col-lg-12">
                                  <div class="mt-12">
                                    <label class="c-form-label">Assesmen Pelayanan <span class="text-danger-500">*</span></label>
                                    <v-select v-model="form.assesmentPel" :options="OptionsConsuleDoctor" :reduce="OptionsConsuleDoctor => OptionsConsuleDoctor.value" label="title" placeholder="-- Assesmen Pelayanan --"></v-select>
                                  </div>
                                </div>
                                <div class="col-lg-12">
                                  <div class="mt-12">
                                    <label class="c-form-label">Jaminan</label>
                                    <v-select v-model="form.lakaLantas" :options="OptionsKLL" @input="inputKLL()" :reduce="OptionsKLL => OptionsKLL.value" label="title" placeholder="-- Jaminan --"></v-select>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </template>
                      <template v-if="regData.health_facility_name == 'RAWAT INAP'">
                        <div class="row">
                          <div class="col-lg-12">
                            <div class="mt-12">
                              <label class="c-form-label">No. Surat Kontrol/SPRI<span class="text-danger-500">*</span></label>
                              <b-input-group>
                                <b-form-input v-model="form.noSurat"></b-form-input>
                                <b-button variant="primary-600" @click="isRefcontrol()" class="mb-3">Cari</b-button>
                              </b-input-group>
                            </div>
                          </div>
                          <b-modal v-model="active.refcontrol" @close="isRefcontrol()" title="Referensi Kontrol" hide-footer>
                            <div class="row">
                              <div class="col-12 col-lg-12 mt-1">
                                <label class="c-form-label">Nomor Anggota </label>
                                <b-input-group>
                                  <b-form-input v-model="pesertaData.noKartu"  @keyup.enter="getRefControl()"></b-form-input>
                                  <b-input-group-append>
                                      <b-button variant="primary-500" @click="getRefControl()"><feather-icon icon="SearchIcon" /></b-button>
                                  </b-input-group-append>
                                </b-input-group>
                                <b-table bordered responsive small head-variant="light" style="height: 400px" :items="refer_bpjs_data" :fields="ReferBpjsTable.fields" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                                  <template #cell(action)="row">
                                    <div>
                                      <b-button size="sm" variant="primary-500" @click="pickControl(row.item)">Pilih Kontrol</b-button>
                                    </div>
                                  </template>
                                </b-table>
                              </div>
                            </div>
                          </b-modal>
                          <div class="col-lg-12">
                            <div class="mt-12">
                              <label class="c-form-label">Doktor Penerbit Surat Kontrol</label>
                              <b-form-input v-model="form.kodeDPJPName" readonly></b-form-input>
                            </div>
                          </div>
                          <!-- <div class="col-lg-12">
                            <div>
                              <label class="c-form-label">DPJP <span class="text-danger-500">*</span></label>
                              <v-select v-model="form.doctor_in_charge_id" aria-readonly="true" :options="OptionsDoctor" :reduce="OptionsDoctor => OptionsDoctor.value" label="title" placeholder="-- Dokter --"></v-select>
                            </div>
                          </div> -->
                          <div class="col-lg-12" style="display:none">
                            <div class="mt-12">
                              <label class="c-form-label">Asal Rujukan <span class="text-danger-500">*</span></label>
                              <v-select v-model="form.ppkRujukan" aria-readonly="true" :options="OptionsFaskes" :reduce="OptionsFaskes => OptionsFaskes.value" label="title" placeholder="-- Faskes Rujukan --"></v-select>
                            </div>
                          </div>
                          <div class="col-lg-12">
                            <div class="mt-12">
                              <label class="c-form-label">Tanggal Rujukan</label>
                              <b-form-input v-model="form.tglRujukan" type="date"></b-form-input>
                            </div>
                          </div>
                          <div class="col-lg-12" style="display:none">
                            <div class="mt-12">
                              <label class="c-form-label">Asal rujukan <span class="text-danger-500">*</span></label>
                              <v-select v-model="form.asalRujukan" aria-readonly="true" :options="OptionsAsalRujukan" :reduce="OptionsAsalRujukan => OptionsAsalRujukan.value" label="title" placeholder="-- Asal Rujukan --"></v-select>
                            </div>
                          </div>
                          <div class="col-lg-12">
                            <div class="mt-12">
                              <label class="c-form-label">No Rujukan <span class="text-danger-500">*</span></label>
                              <b-form-input v-model="form.noRujukan"></b-form-input>
                            </div>
                          </div>
                          <div class="col-lg-12">
                            <div class="mt-12">
                              <label class="c-form-label">Tanggal SEP</label>
                              <b-form-input v-model="form.tglSep" type="date"></b-form-input>
                            </div>
                          </div>
                          <div class="col-lg-12">
                            <div class="mt-12">
                              <label class="c-form-label">No MR <span class="text-danger-500">*</span></label>
                              <b-input-group>
                                <b-input-group-append is-text>
                                  <b-form-checkbox v-model="form.cob">
                                    <span>Peserta COB</span>
                                  </b-form-checkbox>
                                </b-input-group-append>
                                <b-form-input v-model="form.noMr"></b-form-input>
                              </b-input-group>
                            </div>
                          </div>
                          <div class="col-lg-12">
                            <div class="mt-12">
                              <label class="c-form-label">Kelas Rawat <span class="text-danger-500"></span></label>
                              <v-select v-model="form.klsRawatNaik" :options="OptionsClass" :reduce="OptionsClass => OptionsClass.value" label="title" placeholder="-- Kelas Rawat --"></v-select>
                            </div>
                          </div>
                          <div class="col-lg-12" v-if="form.klsRawatNaik != pesertaData.hakKelas.kode">
                            <div class="mt-12">
                              <label class="c-form-label">Pembiayaan <span class="text-danger-500"></span></label>
                              <v-select v-model="form.pembiayaan" :options="OptionsPaymentMethod" :reduce="OptionsPaymentMethod => OptionsPaymentMethod.value" label="title" placeholder="-- Pembiayaan --"></v-select>
                            </div>
                          </div>
                          <div class="col-lg-12" v-if="form.klsRawatNaik != pesertaData.hakKelas.kode">
                            <div class="mt-12">
                              <label class="c-form-label">Penanggung Jawab <span class="text-danger-500">*</span></label>
                              <b-form-input v-model="form.penanggungJawab" :value="form.pembiayaan === '1' ? 'Pribadi' : ''"></b-form-input>
                            </div>
                          </div>
                          <div class="col-lg-12">
                            <div class="mt-12">
                              <label class="c-form-label">Diagnosa <span class="text-danger-500">*</span></label>
                              <v-select v-model="form.diagAwal" @search="onSearchIcd10" :options="OptionsIcd10" :reduce="OptionsIcd10 => OptionsIcd10.value" label="title" placeholder="-- Diagnosas --"></v-select>
                              <!-- <v-select v-if="form.diagAwalName === 'null - null'" v-model="form.diagAwal" @search="onSearchIcd10" :options="OptionsIcd10" :reduce="OptionsIcd10 => OptionsIcd10.value" label="title" placeholder="-- Diagnosa --"></v-select>
                              <b-form-input v-if="form.diagAwalName !== 'null - null'" v-model="form.diagAwalName"></b-form-input> -->
                            </div>
                          </div>
                          <div class="col-lg-12">
                            <div class="mt-12">
                              <label class="c-form-label">No Telepon <span class="text-danger-500">*</span></label>
                              <b-form-input v-model="form.noTelp"></b-form-input>
                            </div>
                          </div>
                          <div class="col-lg-12">
                            <div class="mt-12">
                              <label class="c-form-label">Catatan</label>
                              <b-textarea v-model="form.catatan"></b-textarea>
                            </div>
                          </div>
                          <!-- <div class="col-lg-12" v-if="!form.control"> -->
                          <div class="col-lg-12">
                            <div class="mt-12">
                              <label class="c-form-label">Tujuan Kunjungan <span class="text-danger-500">*</span></label>
                              <v-select v-model="form.tujuanKunj" :options="OptionsPurpose" :reduce="OptionsPurpose => OptionsPurpose.value" label="title" placeholder="-- Tujuan Kunjungan --"></v-select>
                            </div>
                          </div>
                          <div class="col-lg-12" v-if="form.tujuanKunj == 2">
                            <div class="mt-12">
                              <label class="c-form-label">Prosedur <span class="text-danger-500">*</span></label>
                              <v-select v-model="form.flagProcedure" :options="OptionsProcedure" :reduce="OptionsProcedure => OptionsProcedure.value" label="title" placeholder="-- Prosedur --"></v-select>
                            </div>
                          </div>
                          <div class="col-lg-12" v-if="form.flagProcedure == 1 && form.tujuanKunj == 2">
                            <div class="mt-12">
                              <label class="c-form-label">Prosedur Tidak Berkelanjutan<span class="text-danger-500">*</span></label>
                              <v-select v-model="form.kdPenunjang" :options="OptionsProcedureNot" :reduce="OptionsProcedureNot => OptionsProcedureNot.value" label="title" placeholder="-- Prosedur --"></v-select>
                            </div>
                          </div>
                          <div class="col-lg-12" v-if="form.flagProcedure == 2 && form.tujuanKunj == 2">
                            <div class="mt-12">
                              <label class="c-form-label">Prosedur Berkelanjutan<span class="text-danger-500">*</span></label>
                              <v-select v-model="form.kdPenunjang" :options="OptionsProcedureCon" :reduce="OptionsProcedureCon => OptionsProcedureCon.value" label="title" placeholder="-- Prosedur --"></v-select>
                            </div>
                          </div>
                          <div class="col-lg-12" v-if="form.tujuanKunj == 3">
                            <div class="mt-12">
                              <label class="c-form-label">Assesmen Pelayanan <span class="text-danger-500">*</span></label>
                              <v-select v-model="form.assesmentPel" :options="OptionsConsuleDoctor" :reduce="OptionsConsuleDoctor => OptionsConsuleDoctor.value" label="title" placeholder="-- Assesmen Pelayanan --"></v-select>
                            </div>
                          </div>
                          <div class="col-lg-12">
                            <div class="mt-12">
                              <label class="c-form-label">Jaminan</label>
                              <v-select v-model="form.lakaLantas" :options="OptionsKLL" @input="inputKLL()" :reduce="OptionsKLL => OptionsKLL.value" label="title" placeholder="-- Jaminan --"></v-select>
                            </div>
                          </div>
                        </div>
                      </template>
                    </div>
                  </div>
                </section>
                <section class="mt-32 row mx-0">
                  <b-button variant="primary-600" :disabled="isDisabled" @click.prevent="validationForm" class="m-1">Simpan</b-button>
                  <template v-if="!regData.sep">
                  </template>
                  <template v-else>
                    <router-link :to="{ path: '/prints/sep/'+ regData.registration_destination_id + '/' + regData.sep}">
                      <b-button variant="warning-500" class="m-1">
                        <feather-icon icon="PrinterIcon" class="m-1" /> Cetak SEP
                      </b-button>
                    </router-link>
                    <router-link :to="{ path: '/bpjs/checkout/'+ regData.registration_destination_id + '/' + regData.sep}">
                      <b-button variant="warning-500" class="m-1">
                        <feather-icon icon="CalendarIcon" class="m-1" /> Update Tgl Pulang
                      </b-button>
                    </router-link>
                    <b-button variant="danger-600" :disabled="isDisabled" @click.prevent="validationFormDelete" class="m-1">Hapus SEP</b-button>
                  </template>
                  <!-- <b-button variant="info-600" class="m-1">Edit</b-button>
                  <b-button variant="info-600" class="m-1">Edit Tanggal Pulang</b-button>
                  <b-button variant="danger-600" class="m-1">Hapus</b-button>
                  <router-link :to="{ name: 'PrintsBpjsMemberView' }">
                    <b-button variant="warning-500" class="m-1">
                      <feather-icon icon="PrinterIcon" class="m-1" /> Cetak
                    </b-button>
                  </router-link>
                  <b-button variant="warning-500" class="m-1">
                      <feather-icon icon="PrinterIcon" class="m-1" /> Cetakan ke 1
                  </b-button>
                  <b-button variant="outline-primary-500" class="m-1">Kembali</b-button> -->
                </section>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { mapState } from 'vuex';

import patientMenu from '@/@core/components/tab-menu/patientMenu.vue';
import administration from '@/views/layouts/patient/doctor/administration.vue';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import vSelect from 'vue-select'
import router from '@/router/index.js'

// import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import moment from 'moment'

export default {
  name: 'DoctorStudies',
  components: {
    patientMenu, administration, vSelect, ToastificationContent
  },
  data() {
    return {
      isDisabled: false,
      active:{
        refcontrol:false
      },
      ReferBpjsTable: {
        fields: [
          { key: 'tglRencanaKontrol', label: 'Tgl.Rencana Kontrol' },
          { key: 'noSuratKontrol', label: 'No Surat' },
          { key: 'namaJnsKontrol', label: 'Jenis Kontrol' },
          { key: 'nama', label: 'Nama' },
          { key: 'namaPoliTujuan', label: 'Sub/Specialist' },
          { key: 'namaDokter', label: 'Dokter' },
          { key: 'action', label: '' }
        ],
      },
      pesertaData: {},
      regData: {},
      refer_bpjs_data:[],
      form: {
        pembiayaan:'',
        penanggungJawab:'',
        noKartu: '',
        tglSep: moment(new Date()).format('YYYY-MM-DD'),
        jnsPelayanan:'2',
        klsRawat:'',
        noMr: '',
        asalRujukan: '1',
        tglRujukan: '',
        klsRawatNaik:'',
        noRujukan: '',
        ppkRujukan: '',
        catatan: '',
        diagAwal: '',
        tujuan:'',
        eksekutif:'',
        cob:'',
        katarak:'0',
        kontrol: '',
        diagAwalName:'',
        lakaLantas: '0',
        tglKejadian: '',
        suplesi: 0,
        sep: '',
        noSepSuplesi: '',
        kodepropinsi: '',
        kodekabupaten: '',
        kodekecamatan: '',
        keteranganKll: '',
        tujuanKunj:0,
        dpjpLayan:'',
        kodeDPJP:'',
        kodeDPJPName:'',
        noSurat:'',
        noTelp:'',
        user:JSON.parse(localStorage.getItem('userData')).employee_name,
        penjamin: [],
        doctor_in_charge_id:'',
        poli_destination: '',
        ppk_origin: '',
        control: '',
        registration_destination_id: '',
        accident: '',
        flagProcedure:'',
        assesmentPel:''
      },
      OptionsIcd10:[],
      OptionsFaskes: [
        { value: '0070R032', title: 'RSHBH' },
      ],
      OptionsAsalRujukan: [
        { value: '1', title: 'Faskes 1' },
        { value: '2', title: 'Faskes 2 (RS)' },
      ],
      OptionsPurpose: [
        { value: 0, title: 'Normal' },
        { value: 1, title: 'Prosedur' },
        { value: 2, title: 'Konsul Dokter' }
      ],
      OptionsProcedure: [
        { value: 1, title: 'Tidak Berkelanjutan' },
        { value: 2, title: 'Prosedur dan Terapi Berkelanjutan' },
      ],
      OptionsProcedureCon: [
        {value: "1", title: "Radioterapi"},
        {value: "2", title: "Kemoterapi"},
        {value: "3", title: "Rehabilitasi Medik"},
        {value: "4", title: "Rehabilitasi Psikososial"},
        {value: "5", title: "Transfusi Darah"},
        {value: "6", title: "Pelayanan Gigi"}
      ],
      OptionsProcedureNot: [
        {value: "7", title: "Laboratorium"},
        {value: "8", title: "USG"},
        {value: "9", title: "Farmasi"},
        {value: "10", title: "Lain-Lain"},
        {value: "11", title: "MRI"},
        {value: "12", title: "HEMODIALISA"}
      ],
      OptionsConsuleDoctor: [
        {value: 1, title: "Poli spesialis tidak tersedia pada hari sebelumnya"},
        {value: 2, title: "Jam Poli telah berakhir pada hari sebelumnya"},
        {value: 3, title: "Dokter Spesialis yang dimaksud tidak praktek pada hari sebelumnya"},
        {value: 4, title: "Atas Instruksi RS"},
        {value: 5, title: "Tujuan Kontrol"}
      ],
      OptionsKLL: [
        {value: "0", title: "Bukan Kecelakaan lalu lintas"},
        {value: "1", title: "KLL dan bukan kecelakaan Kerja"},
        {value: "2", title: "KLL dan KK"},
      ],
      OptionsClass: [
        {value: "1", title: "VVIP"},
        {value: "2", title: "VIP"},
        {value: "3", title: "KELAS 1"},
        {value: "4", title: "KELAS 2"},
        {value: "5", title: "KELAS 3"},
        {value: "6", title: "ICCU"},
        {value: "7", title: "ICU"},
        {value: "8", title: "Diatas Kelas 1"},
      ],
      OptionsPaymentMethod: [
        { value: "1", title: 'Pribadi' },
        { value: "2", title: 'Pemberi Kerja' },
        { value: "3", title: 'Asuransi Kesehatan Tambahan' },
      ],
    }
  },
  watch: {
    'form.kodepropinsi'(obj) {
      if (obj) {
        this.$store.dispatch('patient/getKabupaten', obj)
      }
    },
    'form.kodekabupaten'(obj) {
      if (obj) {
        this.$store.dispatch('patient/getKecamatan', obj)
      }
    },
  //   'form.health_facility_item_id'(obj) {
  //     if (obj) {
  //       return this.$store.dispatch('employee/getParamSpecialist', obj.value)
  //     }
  //   },
  //   'form.type_room_id'(value) {
  //     if (value) {
  //       return this.$store.dispatch('room/getParamRoom', value)
  //     }
  //   },
  //   'form.room_id'(value) {
  //     if (value) {
  //       return this.$store.dispatch('room/getParamBed', value)
  //     }
  //   },
  //   'form.dpjp_doctor_in_charge_id'(value) {
  //     if (value == 1) {
  //       this.form.doctor_in_charge_utama = this.form.doctor_in_charge_id_1
  //     } else if (value == 2) {
  //       this.form.doctor_in_charge_utama = this.form.doctor_in_charge_id_2
  //     }
  //   },
  },
  methods: {
    isRefcontrol() {
      this.active.refcontrol = !this.active.refcontrol
    },
    pickControl(data) {
      // { key: 'tglRencanaKontrol', label: 'Tgl.Rencana Kontrol' },
      // { key: 'noSuratKontrol', label: 'No Surat' },
      // { key: 'namaJnsKontrol', label: 'Jenis Kontrol' },
      let noSepAsalKontrol = ''
      if (data.jnsKontrol === "1") {
        this.form.noRujukan = noSepAsalKontrol
        this.form.jnsPelayanan = '1'
        if (this.pesertaData.hakKelas.kode === '1'){
          this.form.klsRawatNaik = "3"
        } else if (this.pesertaData.hakKelas.kode === '2'){ 
          this.form.klsRawatNaik = "4"
        } else if (this.pesertaData.hakKelas.kode === '3'){ 
          this.form.klsRawatNaik = "5"
        }
        this.form.ppkRujukan = '0070R032'
        this.form.tglRujukan = data.tglRencanaKontrol
      } else {
        this.$store.dispatch('patient/bpjs', {url:'Sep',idx:data.noSepAsalKontrol})
        .then(response_sep => {
          noSepAsalKontrol = response_sep.data.response.noRujukan
          this.form.noRujukan = noSepAsalKontrol
          this.$store.dispatch('patient/bpjs', {url:'Rujukan_first',idx:noSepAsalKontrol})
          .then(response_control_first => {
            this.form.tglRujukan = response_control_first.data[0].tglKunjungan
            this.form.ppkRujukan = response_control_first.data[0].ppkRujukan
          })
        })
        this.form.jnsPelayanan = '2'
        this.form.kontrol = 1
      }
      // { key: 'nama', label: 'Nama' },
      // { key: 'namaPoliTujuan', label: 'Sub/Specialist' },
      // { key: 'namaDokter', label: 'Dokter' },
      // { key: 'action', label: '' }
      this.form.noSurat = data.noSuratKontrol
      this.form.tujuan = data.poliTujuan
      this.form.kodeDPJP = data.kodeDokter
      this.form.kodeDPJPName = data.namaDokter
      this.active.refcontrol = false
    },
    getRefControl () {
      this.$store.dispatch('patient/bpjs', {url:'RujukanKontrolList',idx:this.pesertaData.noKartu})
      .then(response_refer => {
        if (response_refer.status == 201) {
          alert(response_refer.message)
        } else {
          this.refer_bpjs_data = response_refer.data.response.list
        }
        this.active.refer_bpjs = true
      })
    },
    is_control () {
      if (this.form.control) {

      } else {

      }
    },
    inputKLL() {
      let text = "Apakah ini merupakan kasus kecelakaan baru ?";
      if (confirm(text) == true) {
        this.inputKLLYes()
        this.lakaLantas = '1'
      } else {
        this.lakaLantas = '2'
      }
    },
    inputKLLYes() {
      let text = "1. Apakah kasus berhubungan dengan pekerjaan ?\n2. Berangkat dari rumah menuju tempat kerja ?\n3. Pulang dari tempat kerja menuju ke rumah ?";
      if (confirm(text) == true) {
      } else {
      }
    },
    validateTglKejadian() {
      alert("tgl Kejadian tidak boleh lebih besar dari tglSep")
      alert("Pilihan Laka Lantas Belum ditentukan")
      alert("Pilihan Laka Lantas Menunjukan Kecelakaan, Mohon Pilih Provinsi Kejadian")
      alert("No.SEP Suplesi KLL Tidak Ada")
      alert("Tanggal SEP lebih besar dari tanggal sekarang")
      alert("Tanggal Rujukan lebih besar dari tanggal Pembuatan SEP")
      alert("SEP Backdate harus mengajukan persetujuan backdate (Pengajuan belum di create)")
      alert("Data ats Pasien ini belum dipulangkan pada rawat inap sebelumnya")
      alert("Kelas Rawat Tidak sesuai format")
      alert("Diagnosa mohon isi dengan benar")
      alert("No.Telepon/HP Pasien tidak boleh kosong")
      alert("Sesi Login sepertinya habis, coba login kembali")
      alert("No.SEP Sudah Dibuatkan Rujukan Antar RS, Tidak Dapat Diproses")
      alert("Edit SEP Berhasil")
      alert("Hapus SEP Berhasil")
      alert("Gagal Hapus! SEP Sudah digunakan untuk Rujukan Kontrol!")
      alert("Setting Pulang Berhasil")
    },
    validationForm () {
      const formData = this.form 
      console.log('fromdata: ', formData);
      this.isDisabled = true
      this.$store.dispatch('patient/formPostPutSEP', formData)
      .then(res => {
        // this.notification('Success','CheckIcon','success','You have successfully')
        if (res.status == 200) {
          this.notification('Warning','AlertCircleIcon','warning',res.data.metaData.message)
        } else if (res.status == 404) {
          this.notification('Warning','AlertCircleIcon','warning','Error')
        } else if (res.status == 422) {
          this.notification('Warning','AlertCircleIcon','warning',"Error")
        } else if (res.status == 201) {
          this.notification('Success','CheckIcon','success','You have successfully. SEP: '+res.data.sep)
          // this.notification('Success','CheckIcon','success','You have successfully. SEP: '+res.data.metaData.message)
          this.getData(this.$route.params.id)
        }
        this.isDisabled = false
      }, 
      err => {
        this.isDisabled = false
        this.notification('Failed','CloseIcon','danger',err)
      })
    },
    validationFormDelete () {
      const formData = this.form 
      console.log('fromdata: ', formData);
      this.isDisabled = true
      this.$store.dispatch('patient/formDeleteSEP', formData)
      .then(res => {
        // this.notification('Success','CheckIcon','success','You have successfully')
        if (res.status == 200 || res.status == 201) {
          this.notification('Success','CheckIcon','success',res.message)
        } else if (res.status == 404) {
          this.notification('Warning','AlertCircleIcon','warning','Error')
        } else if (res.status == 422) {
          this.notification('Warning','AlertCircleIcon','warning',"Error")
        }
        location.reload()
        this.isDisabled = false
      }, 
      err => {
        this.isDisabled = false
        this.notification('Failed','CloseIcon','danger',err)
      })
    },
    notification(a, b, c, d) {
      this.$toast({
        component: ToastificationContent,
        position: 'top-right',
        props: { title: a, icon: b, variant: c, text: d }
      })
    },
    async getDataBpjs(id) {
      try {
        const response = await this.$store.dispatch('patient/bpjs', {url:'noka',idx:id})
        this.pesertaData = response.response.peserta
        this.OptionsFaskes = []
        this.OptionsFaskes = [
          { value: '0070R032', title: 'RSHBH' },
          { value: this.pesertaData.provUmum.kdProvider, title: this.pesertaData.provUmum.nmProvider }
        ]
        this.form.klsRawat = this.pesertaData.hakKelas.kode
        this.form.noKartu = id
      } catch (error) {
        this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
      }
    },
    async getRujukan(id) {
      try {
        const response = await this.$store.dispatch('patient/bpjs', {url:'Rujukan',idx:id})
        if(response.data[0].code === '201'){
          this.getKontrol(id)
        } else {
          this.form.asalRujukan = response.data[0].AsalRujukanFaskes === '4' ? '2' : '1'
          this.form.ppkRujukan = response.data[0].ppkRujukan
          this.form.tglRujukan = response.data[0].tglKunjungan
        }
      } catch (error) {
        this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
      }
    },
    async getKontrol(id) {
      try {
        const response = await this.$store.dispatch('patient/bpjs', {url:'Control',idx:id})
        console.log(response)
        if(response.data){
          if (response.data.response.sep.provPerujuk.kdProviderPerujuk === '0070R032') {
            this.form.asalRujukan = '2'
            this.form.ppkRujukan = response.data.response.sep.provPerujuk.kdProviderPerujuk
            this.form.noSurat = response.data.response.noSuratKontrol
            this.form.noRujukan = response.data.response.sep.noSep
            this.form.tglRujukan = response.data.response.tglRencanaKontrol
          } else {
            this.form.asalRujukan = '1'
            this.form.noSurat = id
            this.form.noRujukan = response.data.response.sep.provPerujuk.noRujukan
            this.form.ppkRujukan = response.data.response.sep.provPerujuk.kdProviderPerujuk
            this.form.tglRujukan = response.data.response.sep.provPerujuk.tglRujukan
          }
        }
      } catch (error) {
        this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
      }
    },
    async getData(id) {
      try {
        // this.form.tglSep = response[0].
        const response = await this.$store.dispatch('registration/getByInsuranceByNoka', {registration_destination_id:id,number_insurance:this.$route.params.no})
        this.regData = response[0]
        this.form.noRujukan = response[0].noKunjungan
        if(response[0].noKunjungan){
          this.getRujukan(response[0].noKunjungan)
        }
        if(this.regData.health_facility_name == 'RAWAT INAP') {
          this.form.poli_destination = 'RAWAT INAP'
        } else {
          this.form.poli_destination = response[0].health_facility_item_name
        }
        this.form.noMr = response[0].norm
        this.form.sep = response[0].sep
        // this.form.diagAwalName = response[0].icd10_code+' - '+response[0].icd10_name
        // this.form.diagAwal = response[0].icd10_id
        this.form.doctor_in_charge_id = response[0].doctor_in_charge_id
        this.getDataBpjs(this.$route.params.no)
        this.form.noTelp = response[0].phone
      } catch (error) {
        this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
      }
    },
    resetTransferForm() {
      this.form = {
        // soap_s_id: '',
        // soap_a_id: '',
      }
    },
    onSearchIcd10(search, loading) {
      if(search.length >= 3) {
        loading(true);
        this.$store.dispatch('patient/getDiagnosa', search).then(data => {
          this.OptionsIcd10 = []
          for (let i = 0; i < data.response.diagnosa.length; i++) {
            const option = []
            for (const key in data.response.diagnosa[i]) {
              if (key === 'kode') { option['value'] = data.response.diagnosa[i][key] } else if (key === 'nama') { option['title'] = data.response.diagnosa[i][key] } else if (key === 'kode') { option['oid'] = data.response.diagnosa[i][key] }
            }
            this.OptionsIcd10.push(Object.assign({}, option))
          }
          loading(false)
        })
      }
    }
  },
  mounted() {
    if (this.$route.params.id) {
      // this.show = true
      this.id = this.$route.params.id
      this.form.registration_destination_id = this.$route.params.id
      this.getData(this.$route.params.id)
    }
  },
  computed: {
    ...mapState({
      OptionsPropinsi: state => state.patient.options.propinsi,
      OptionsKabupaten: state => state.patient.options.kabupaten,
      OptionsKecamatan: state => state.patient.options.kecamatan,
      // OptionsHealthFacility: state => state.healthfacility.options.healthfacility,
      // OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,
      OptionsDoctor: state => state.employee.options.employee,
      // TypeRoomOptions: state => state.helper.options.type_room,
      // OptionsRoom: state => state.room.options.room,
      // OptionsBed: state => state.room.options.bed,
      // OptionsInsurance: state => state.helper.options.insurance,
      // Options3rdCompany: state => state.helper.options.third_party_company,
      // OptionsServiceClass: state => state.helper.options.service_class,
    })
  },
  created() {
    this.$store.dispatch('employee/getParamSpecialist')
    this.$store.dispatch('patient/getPropinsi')
    // this.$store.dispatch('healthfacility/getInpatientOption', 'inpatientOption')
    // this.$store.dispatch('helper/getParam', 'type_room')
    // this.$store.dispatch('helper/getParam', 'insurance')
    // this.$store.dispatch('helper/getParamThirdPartyCompany', 'third_party_company')
    // this.$store.dispatch('helper/getParamServiceClass', 'service_class')
  },
}
</script>
<style lang="scss">
@import "@core/scss/vue/libs/vue-select.scss";
</style>
<style scoped>
.label-head::before {
  content: "";
  border-left: 6px solid #648DE1;
}

.c-img {
  aspect-ratio: 1/1;
  object-fit: cover;
  max-width: 150px;
  max-height: 150px;
  border: 10px solid white;
}
</style>
