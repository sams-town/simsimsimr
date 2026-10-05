<template>
  <div>
    <patientMenu selectedMenu="menu1" />
    <div class="label-head my-16 my-lg-24">
      <span class="px-16 text-bold-24 text-neutral-700">Detail Pendaftaran Pasien</span>
    </div>
    <!-- ////////  -->
    <div class="card-body shadow-sm rounded">
      <div class="row mx-0">
        <div class="col-12 col-lg">
          <div class="row">
            <div class="col-12 col-lg-3">
              <img src="@/@core/assets/images/pendaftaran/user.png" alt="your-photo"
                class="rounded-circle shadow-sm c-img" />
            </div>
            <div class="col-12 col-lg">
              <span class="text-bold-18 text-indigo">
                <img src="@/@core/assets/icons/nurse/user.svg" alt="user-icon" />{{ this.regData.norm }}
              </span>
              <p class="mb-0 mt-12 text-semi-24 text-neutral-800">
                <span>({{ this.regData.alias_name }}) </span>
                <span>{{ this.regData.name }}</span>
              </p>
              <div class="row mx-0 mt-16">
                <b-badge class="rounded text-neutral-500 text-uppercase border border-success-200 px-10 py-1"
                :variant="this.regData.is_new == 'N' ? 'success-200' : 'info-200'">{{ this.regData.is_new == 'N' ? 'Pasien Lama' : 'pasien Baru' }}</b-badge>
                <b-badge class="rounded text-neutral-500 text-uppercase border border-success-500 px-10 py-1 mx-lg-12"
                  variant="success-200">{{ this.regData.health_facility_name }}</b-badge>
                <b-badge class="rounded text-neutral-500 text-uppercase border border-warning-500 px-10 py-1"
                  variant="warning-200">{{ this.regData.health_facility_item_name }}</b-badge>
                <div v-if="this.regData.registration_insurance">
                  <div v-for="list in this.regData.registration_insurance">
                    <b-badge v-if="list.is_cob == 1"class="rounded text-neutral-500 text-uppercase border border-warning-500 px-10 py-1" variant="warning-200">COB</b-badge>
                  </div>
                </div>
              </div>
              <p class="text-neutral-800 mt-8 mt-lg-16">
                <span class="text-regular-14 mr-2">Status:</span>
                <span class="text-bold-14">Dalam Penanganan Medis</span>
              </p>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-4 mt-16 mt-lg-0">
          <div class="d-flex flex-column align-items-lg-end h-100">
            <div class="row mx-0">
              <b-button pill variant="outline-indigo" size="sm" class="mr-2">
                <b-icon icon="arrow-left" /> <span>Kembali</span>
              </b-button>
              <administration />
            </div>
            <div class="mt-2 mt-lg-auto">
              <b-button pill variant="outline-indigo" size="sm">
                <b-icon icon="arrow-clockwise" /> <span>Refresh</span>
              </b-button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row mx-0 mt-32">
      <div class="col-lg-4">
        <div class="card">
          <div class="card-header bg-indigo-200 d-flex">
            <span class="text-bold-18">Data Pendaftaran</span>
            <b-icon icon="chevron-down" class="ml-auto" />
          </div>
          <div class="card-body">
            <div class="row mx-0 ml-10">
              <div v-if="regData.is_transfer === 0" class="d-flex flex-wrap">
                <!-- <b-button @click="onTransferModal()" size="sm" variant="success-600">
                  <feather-icon icon="SendIcon" class="mr-1" /> Transfer Pasien
                </b-button> -->
              </div>
              <!-- <b-button @click="onEditModal()" size="sm" variant="info-600" class="ml-2" v-if="regData.health_facility_id === '3cb626f5-9148-4fb1-8663-3d7f028b01a6' || regData.health_facility_id === '444b3134-5b75-4b67-af72-a558d46716d7'"> -->
              <b-button @click="onEditModal()" size="sm" variant="info-600" class="ml-2">
                <feather-icon icon="EditIcon" class="mr-1" /> Edit Pasien
              </b-button>
              <b-button @click="onReferInternalModal()" size="sm" variant="info-600" class="ml-2">
                <feather-icon icon="EditIcon" class="mr-1" /> Rujuk Internal
              </b-button>

              <template v-if="regData.is_checkout == 1 && regData.health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f'">
                <b-button @click="onCheckoutModal" size="sm" variant="info-600" class="ml-2">
                  <feather-icon icon="EditIcon" class="mr-1" /> Ganti Tanggal Checkout
                </b-button>
              </template>
            </div>
            <b-table-simple class="mt-16">
              <b-tbody>
                <b-tr>
                  <b-td class="text-regular-14">Nomor Pendaftaran</b-td>
                  <b-td class="text-bold-14">{{ this.regData.code }}</b-td>
                </b-tr>
                <b-tr>
                  <b-td class="text-regular-14">Identitas Pasien</b-td>
                  <b-td class="text-bold-14">{{ this.regData.idcard }}</b-td>
                </b-tr>
                <b-tr>
                  <b-td class="text-regular-14">No. RM</b-td>
                  <b-td class="text-bold-14">{{ this.regData.norm }}</b-td>
                </b-tr>
                <b-tr>
                  <b-td class="text-regular-14">Nama</b-td>
                  <b-td class="text-bold-14">({{ this.regData.alias_name }}) {{ this.regData.name }}</b-td>
                </b-tr>
                <b-tr v-if="this.regData?.patient_responsible_person?.name">
                  <b-td class="text-regular-14">Alamat</b-td>
                  <b-td class="text-bold-14">{{ this.regData.address }}</b-td>
                </b-tr>
                <b-tr v-if="this.regData?.patient_responsible_person?.name">
                  <b-td class="text-regular-14">Penanggung Jawab</b-td>
                  <b-td class="text-bold-14">{{ this.regData?.patient_responsible_person?.name }} ({{ this.regData?.patient_responsible_person?.responsible_name }})</b-td>
                </b-tr>
                <b-tr v-if="this.regData?.patient_responsible_person?.name">
                  <b-td class="text-regular-14">Identitas Penanggung Jawab</b-td>
                  <b-td class="text-bold-14">{{ this.regData?.patient_responsible_person?.idcard }}</b-td>
                </b-tr>
                <b-tr v-if="this.regData?.patient_responsible_person?.name">
                  <b-td class="text-regular-14">Telepon Penanggung Jawab</b-td>
                  <b-td class="text-bold-14">{{ this.regData?.patient_responsible_person?.phone }}</b-td>
                </b-tr>
                <b-tr>
                  <b-td class="text-regular-14">Tanggal Lahir</b-td>
                  <b-td class="text-bold-14">{{ localDate(this.regData.birth_date) }}</b-td>
                </b-tr>
                <b-tr>
                  <b-td class="text-regular-14">Usia</b-td>
                  <b-td class="text-bold-14">{{ parsed_age(this.regData.birth_date) }}</b-td>
                </b-tr>
                <b-tr>
                  <b-td class="text-regular-14">Jenis Kelamin</b-td>
                  <b-td class="text-bold-14">{{ this.regData.sex_name }}</b-td>
                </b-tr>
                <b-tr>
                  <b-td class="text-regular-14">Dokter</b-td>
                  <b-td class="text-bold-14">{{
                    this.regData.doctor_in_charge_name
                  }}</b-td>
                </b-tr>
                <b-tr>
                  <b-td class="text-regular-14">{{
                    this.regData.health_facility_name
                  }}</b-td>
                  <b-td class="text-bold-14">{{
                    this.regData.health_facility_item_name
                  }}-{{
                    this.regData.room_name
                  }}</b-td>
                </b-tr>
                <b-tr>
                  <b-td class="text-regular-14">Tanggal Daftar</b-td>
                  <b-td class="text-bold-14">{{
                    this.regData.register_date
                  }}</b-td>
                </b-tr>
              </b-tbody>
            </b-table-simple>
          </div>
        </div>
      </div>
      <div class="col-lg-8">
        <div class="card">
          <div class="card-header bg-indigo-200 d-flex">
            <span class="text-bold-18">Penjamin</span>
            <b-icon icon="chevron-down" class="ml-auto" />
          </div>
          <div class="card-body" style="height: 400px">
            <b-table bordered responsive small head-variant="light" style="height: 400px" :items="InsuranceItems" :fields="InsuranceTable.fields" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
              <template #cell(register_date)="row">
                <div>
                  <div>{{ row.item.health_facility_name }}</div>
                  <div>{{ row.item.register_date }}</div>
                </div>
              </template>
              <template #cell(sep)="row">
                <div>
                  <span>{{ row.item.sep }} </span>
                  <span>{{ jnssep(row.item.jns_sep) }}</span>
                </div>
              </template>
              <template #cell(insurance_name)="row">
                <div>
                  <span>{{ row.item.insurance_name }}, </span>
                  <span>{{ row.item.company_name }}</span>
                </div>
              </template>
              <template #cell(action)="row">
                <template v-if="row.item.company_name == 'BPJS Kesehatan' || row.item.company_name == 'MANDIRI INHEALTH' || row.item.company_name == 'JASARAHARJA'  || row.item.company_name == 'MANDIRI INHEALTH COB'">
                  <div class="d-flex w-100 align-items-cener">
                    <b-button @click="onModalSEP(row.item)" size="sm" variant="success" class="mr-2" >Upload SEP</b-button>
                    <b-button @click="onModalKronologis(row.item.registration_destination_id)" size="sm" variant="success" class="mr-2" >Upload File</b-button>
                    <b-button v-if="row.item.file" @click="downloadSep(urlbe, row.item.file)" size="sm" variant="outline-success" class="mr-2">Download SEP</b-button>
                    <b-button v-if="row.item.file_kronologis" @click="downloadSep(urlbe, row.item.file_kronologis)" size="sm" variant="outline-success" class="mr-2">Download File</b-button>
                    <b-button v-if="row.item.sep_json" :to="{ path: '/prints/sep/'+ row.item.registration_destination_id + '/' + row.item.sep}" size="sm" variant="outline-success" class="mr-2">Download SEP</b-button>
                    <!-- <b-dropdown size="sm" toggle-class="customDropdown" variant="outline-primary" id="dropdown-1" text="Menu BPJS">
                      <b-dropdown-item :to="{ path: '/patient/insurance/'+ row.item.registration_destination_id + '/' + row.item.number_insurance}">Buat SEP</b-dropdown-item>
                      <b-dropdown-item :to="{ path: '/bpjs/letter/'+ row.item.registration_destination_id + '/' + row.item.number_insurance}">SPRI / SPRJ</b-dropdown-item>
                      <b-dropdown-item :to="{ path: '/bpjs/refer/'+ row.item.registration_destination_id + '/' + row.item.number_insurance}">Rujuk</b-dropdown-item>
                      <b-dropdown-item :to="{ path: '/bpjs/refer-special/'+ row.item.registration_destination_id + '/' + row.item.number_insurance}">Rujukan Khusus</b-dropdown-item>
                      <b-dropdown-item :to="{ path: '/bpjs/refer-prb/'+ row.item.registration_destination_id + '/' + row.item.number_insurance}">PRB</b-dropdown-item>
                      <b-dropdown-item :to="{ path: '/prints/bpjs-member/'+ row.item.registration_destination_id + '/' + row.item.number_insurance}">Lihat SEP</b-dropdown-item>
                      <b-dropdown-item :to="{ path: '/patient/insurance/'+ row.item.registration_destination_id + '/' + row.item.number_insurance}">Cetak SEP</b-dropdown-item>
                    </b-dropdown> -->
                    <b-dropdown size="sm" toggle-class="customDropdown" variant="outline-primary" id="dropdown-1" text="Menu BPJS">
                      <b-dropdown-item :to="{ path: '/patient/insurance/'+ row.item.registration_destination_id + '/' + row.item.number_insurance}">Buat SEP</b-dropdown-item>
                      <b-dropdown-item :to="{ path: '/bpjs/letter/'+ row.item.registration_destination_id + '/' + row.item.number_insurance}">SPRI / SPRJ</b-dropdown-item>
                      <b-dropdown-item :to="{ path: '/bpjs/refer/'+ row.item.registration_destination_id + '/' + row.item.sep}">Rujuk</b-dropdown-item>
                      <b-dropdown-item :to="{ path: '/bpjs/refer-special'}">Rujukan Khusus</b-dropdown-item>
                      <b-dropdown-item :to="{ path: '/bpjs/refer-prb/'+ row.item.registration_destination_id + '/' + row.item.sep}">PRB</b-dropdown-item>
                      <b-dropdown-item :to="{ path: '/bpjs/backdate/'+ row.item.registration_destination_id + '/' + row.item.number_insurance}">Backdate</b-dropdown-item>
                      <!-- <b-dropdown-item :to="{ path: '/prints/bpjs-member/'+ row.item.registration_destination_id + '/' + row.item.sep}">Cetak SEP</b-dropdown-item> -->
                      <b-dropdown-item :to="{ path: '/patient/insurance/'+ row.item.registration_destination_id + '/' + row.item.number_insurance}">Lihat SEP</b-dropdown-item>
                      <!-- <b-dropdown-divider></b-dropdown-divider>
                      <b-dropdown-item active>Active action</b-dropdown-item>
                      <b-dropdown-item disabled>Disabled action</b-dropdown-item> -->
                    </b-dropdown>
                  </div>
                </template>
              </template>
            </b-table>
          </div>
        </div>
      </div>
    </div>
    <b-modal v-model="active.modal_sep" size="md" title="Upload SEP" hide-footer no-close-on-backdrop>
      <b-form-group label="Berkas SEP" label-cols-sm="2">
        <b-form-file v-model="form_sep.file_insurance"></b-form-file>
        <!-- <b-form-file @change="inputImageRenderer" v-model=""></b-form-file> -->
      </b-form-group>
      <b-form-group label="Nomor SEP" label-cols-sm="2" class="mt-12">
          <v-select v-model="form_sep.jns_sep" aria-readonly="true" :options="OptionsSEP" :reduce="(OptionsSEP) => OptionsSEP.value" label="title" placeholder="-- Pilih --"></v-select>
          <b-form-input v-model="form_sep.sep" placeholder="-- Nomor SEP --" />
      </b-form-group>
      <template v-if="regData.health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f'">
        <b-form-group label="Berkas SPRI" label-cols-sm="2" class="mt-12">
          <b-form-file v-model="form_sep.file_spri"></b-form-file>
          <!-- <b-form-file @change="inputImageRenderer" v-model=""></b-form-file> -->
        </b-form-group>
        <b-form-group label="Nomor SPRI" label-cols-sm="2" class="mt-12">
            <b-form-input v-model="form_sep.noKunjungan" />
        </b-form-group>
      </template>
      <div class="modal-footer d-flex justify-content-end mt-24">
        <b-button :disabled="isDisabledSep" @click="resetSepFile" variant="outline-primary">Reset</b-button>
        <b-button :disabled="isDisabledSep" @click="validationSepFile" variant="primary">Simpan</b-button>
      </div>
    </b-modal>
    <b-modal v-model="active.modal_kronologis" size="md" title="Upload" hide-footer no-close-on-backdrop>
        <b-form-group label="File" label-cols-sm="2">
            <b-form-file v-model="form_kronologis.file_kronologis"></b-form-file>
            <!-- <b-form-file @change="inputImageRenderer" v-model=""></b-form-file> -->
        </b-form-group>
        <div class="modal-footer d-flex justify-content-end mt-24">
            <b-button :disabled="isDisabledKronologis" @click="resetKronologisFile" variant="outline-primary">Reset</b-button>
            <b-button :disabled="isDisabledKronologis" @click="validationKronologisFile" variant="primary">Simpan</b-button>
        </div>
    </b-modal>

    <!-- <b-modal v-model="active.modal_edit" size="lg" no-close-on-backdrop title="Edit Data DPJP/PENJAMIN" v-if="regData.health_facility_id === '3cb626f5-9148-4fb1-8663-3d7f028b01a6' || regData.health_facility_id === '444b3134-5b75-4b67-af72-a558d46716d7'"> -->
      <b-modal v-model="active.modal_edit" size="lg" no-close-on-backdrop title="Edit Data DPJP/PENJAMIN">
        <section class="card">
            <div class="card-header font-weight-bold">Penjamin</div>
            <div class="card-body">
                <b-table-simple fixed small bordered class="text-center">
                    <b-thead head-variant="light" class="text-bold-14">
                        <b-th></b-th>
                        <b-th>Jenis Penjamin</b-th>
                        <b-th>Nama Penjamin</b-th>
                        <b-th>COB</b-th>
                        <b-th>No Anggota / NIP</b-th>
                        <b-th>Kelas Layanan</b-th>
                        <b-th>Aksi</b-th>
                    </b-thead>
                    <b-tbody class="text-regular-12">
                        <b-tr v-for="(item,index) in form_dpjp.insurances" :key="index">
                            <template v-if="item.is_delete == 0">
                                <b-td>
                                    <template v-if="item.is_primary === 0">
                                        <b-button variant="warning-600" size="sm" @click="keyDpjpInsurance(index)">
                                            <feather-icon icon="KeyIcon" />
                                        </b-button>
                                    </template>
                                    <template v-if="item.is_primary === 1">
                                        <b-button variant="info-600" size="sm">Utama</b-button>
                                    </template>
                                </b-td>
                                <b-td>{{ item.insurance_name }}</b-td>
                                <b-td>{{ item.company_name }}</b-td>
                                <b-td>{{ item.company_name_cob }}</b-td>
                                <b-td>{{ item.number_insurance }}</b-td>
                                <b-td>{{ item.service_class_name }}</b-td>
                                <b-td>
                                    <b-button variant="danger-600" size="sm" @click="deleteDpjpInsurance(index, item.is_exist, item.is_primary)">
                                        <feather-icon icon="TrashIcon" />
                                    </b-button>  
                                </b-td>
                            </template>
                        </b-tr>
                    </b-tbody>
                </b-table-simple>
                <!-- <b-table fixed small bordered responsive :items="form_dpjp.insurances" :fields="insuranceFields" head-variant="light" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="text-center">
                    <template #cell(is_primary)="row">
                        <template v-if="row.item.is_primary === 0">
                            <b-button variant="warning-600" size="sm" @click="keyDpjpInsurance(row.index)">
                                <feather-icon icon="KeyIcon" />
                            </b-button>
                        </template>
                        <template v-if="row.item.is_primary === 1">
                            <b-button variant="info-600" size="sm">Utama</b-button>
                        </template>
                    </template>
    
                    <template #cell(Aksi)="row">
                        <b-button variant="danger-600" size="sm" @click="deleteDpjpInsurance(row.index, row.item.is_exist, row.item.is_primary)">
                            <feather-icon icon="TrashIcon" />
                        </b-button>                                
                    </template>
                </b-table> -->
    
                <div class="row mt-2 mt-lg-0">
                    <div class="col-lg-4">
                        <label class="c-form-label">Jenis Penjamin</label>
                        <v-select v-model="temp_form_insurance.insurance_id" :options="OptionsInsurance" :reduce="OptionsInsurance => OptionsInsurance" label="title" placeholder="-- Jenis Penjamin --"></v-select>
                    </div>
                    <template v-if="temp_form_insurance.insurance_id.value == 2 || temp_form_insurance.insurance_id.value == 3">
                        <div class="col-lg-4 mt-12 mt-lg-0">
                            <label class="c-form-label">Nama Penjamin </label>
                            <v-select v-model="temp_form_insurance.company_id" :options="Options3rdCompany" :reduce="Options3rdCompany => Options3rdCompany" label="title" placeholder="-- Nama Penjamin --"></v-select>
                        </div>
                        <div class="col-lg-4 mt-12 mt-lg-0">
                            <label class="c-form-label">COB </label>
                            <v-select v-model="temp_form_insurance.company_id_cob" :options="Options3rdCompany" :reduce="Options3rdCompany => Options3rdCompany" label="title" placeholder="-- Nama Penjamin Cob --"></v-select>
                        </div>
                        <div class="col-lg-4 mt-12 mt-lg-0">
                            <label class="c-form-label">{{ temp_form_insurance.insurance_id.value == 2 ? 'Nomor Anggota' : 'NIP' }}</label>
                            <b-input-group v-if="temp_form_insurance.insurance_id.value == 2">
                                <b-form-input v-model="temp_form_insurance.number_insurance" @keyup.enter="getByNoka()" />
                                <b-input-group-append>
                                    <b-button variant="primary-500" @click="getByNoka()"><feather-icon icon="SearchIcon" /></b-button>
                                </b-input-group-append>
                            </b-input-group>
                            <b-form-input v-if="temp_form_insurance.insurance_id.value == 3" v-model="temp_form_insurance.number_insurance" />
                        </div>
                    </template>
                    <div class="col-lg-4" :class="temp_form_insurance.insurance_id.value == 2 || temp_form_insurance.insurance_id.value == 3 ? 'mt-12' : ''">
                        <label class="c-form-label">Hak Kelas </label>
                        <v-select v-model="temp_form_insurance.service_class_id" :options="OptionsServiceClass" :reduce="OptionsServiceClass => OptionsServiceClass" label="title" placeholder="-- Hak Kelas --"></v-select>
                    </div>
                </div>
            </div>
            
            <div class="card-footer">
                <div class="d-flex justify-content-center">
                    <b-button pill variant="outline-indigo" class="px-3 mr-2">Batal</b-button>
                    <b-button @click.prevent="addDpjpInsurance" pill variant="indigo" class="px-3">Tambah Penjamin</b-button>
                </div>
            </div>
        </section>

        <section class="card mt-32">
            <div class="card-header font-weight-bold">Dokter</div>
            <div class="card-body">
                <template v-if="regData.health_facility_id == '3cb626f5-9148-4fb1-8663-3d7f028b01a6'">
                    <div class="row">
                      <div class="col-lg-6">
                          <label class="c-form-label">Poli</label>
                          <v-select v-model="form_dpjp.health_facility_item_id" aria-readonly="true" :options="OptionsHealthFacilityItem" :reduce="OptionsHealthFacilityItem => OptionsHealthFacilityItem.value" label="title" placeholder="-- Poliklinik --"></v-select>
                      </div>
                      <div class="col-lg-6">
                          <label class="c-form-label">Dokter 1 (DPJP)</label>
                          <v-select v-model="form_dpjp.doctor_in_charge_id" @input="changeDoctor1" :options="OptionsDoctor" :reduce="OptionsDoctor => OptionsDoctor.value" label="title" placeholder="-- Dokter --"></v-select>
                      </div>
                    </div>
                </template>
                <template v-else>
                    <div class="row">
                        <div class="col-lg-6">
                            <label class="c-form-label">Dokter 1 (DPJP)</label>
                            <v-select v-model="form_dpjp.doctor_in_charge_id" @input="changeDoctor1" :options="doctor_list" :reduce="doctor_list => doctor_list.value" label="title" placeholder="-- Dokter --" />
                        </div>
                        <div class="col-lg-6 mt-12 mt-lg-0">
                            <label class="c-form-label">Dokter 2</label>
                            <v-select v-model="form_dpjp.doctor_in_charge_id_2" @input="changeDoctor2" :options="doctor_list" :reduce="doctor_list => doctor_list.value" label="title" placeholder="-- Dokter --" />
                        </div>
                        <div class="col-lg-6 mt-12">
                            <label class="c-form-label">Dokter 3</label>
                            <v-select v-model="form_dpjp.doctor_in_charge_id_3" @input="changeDoctor3" :options="doctor_list" :reduce="doctor_list => doctor_list.value" label="title" placeholder="-- Dokter --" />
                        </div>
                        <div class="col-lg-6 mt-12">
                            <label class="c-form-label">Dokter 4</label>
                            <v-select v-model="form_dpjp.doctor_in_charge_id_4" @input="changeDoctor4" :options="doctor_list" :reduce="doctor_list => doctor_list.value" label="title" placeholder="-- Dokter --" />
                        </div>
                        <div class="col-lg-6 mt-12">
                            <label class="c-form-label">Dokter 5</label>
                            <v-select v-model="form_dpjp.doctor_in_charge_id_5" @input="changeDoctor5" :options="doctor_list" :reduce="doctor_list => doctor_list.value" label="title" placeholder="-- Dokter --" />
                        </div>
                    </div>
                </template>
            </div>
        </section>
          
        <template #modal-footer>
            <div class="d-flex justify-content-end">
                <b-button variant="primary-500" :disabled="disabled.dpjp" @click.prevent="onSaveEditDpjp">Simpan</b-button>
            </div>
        </template>
    </b-modal>

    <b-modal v-model="active.modal_transfer" no-close-on-backdrop size="lg" title="Transfer Pasien" hide-footer @close="onTransferModal()">
        <section class="card" style="display:none">
            <div class="card-header bg-primary-200 font-weight-bold">Keadaan</div>
            <div class="card-body">
                <div>
                    <label class="c-form-label">Keadaan umum</label>
                    <v-select v-model="form.general_reason" aria-readonly="true" :options="OptionsGeneralReason" :reduce="(OptionsGeneralReason) => OptionsGeneralReason.value" label="title" placeholder="-- Pilih --"></v-select>
                  </div>
                  <div class="mt-12">
                    <label class="c-form-label">Alasan Transfer</label>
                    <v-select v-model="form.transfer_reason_type_id" aria-readonly="true" :options="OptionsTransferReason" :reduce="(OptionsTransferReason) => OptionsTransferReason.value" label="title" placeholder="-- Pilih --"></v-select>
                    <b-textarea v-if="form.transfer_reason_type_id == 5" v-model="form.transfer_reason" rows="2" placeholder="tuliskan alasan transfer" class="mt-12" />
                </div>
                <div class="mt-24">
                  <b-form-checkbox value="1" v-model="form.isTransportable">Pasien stabil & transportable</b-form-checkbox>
                </div>
            </div>
        </section>
        <section class="card mt-20">
          <div class="card-header bg-primary-200 font-weight-bold">Perintah Rawat Inap</div>
            <div class="card-body">
              <div>
                  <label class="c-form-label">Indikasi Rawat Inap</label>
                  <b-textarea v-model="form.indication" readonly rows="3" />
              </div>
              <div class="mt-24">
                <div>
                  <ModernLabel label="Diagnosis (ICD10)" />
                  <b-table-simple bordered responsive class="mt-12" style="font-size: 14px">
                      <b-tr>
                          <b-th style="width: 60px;">No</b-th>
                          <b-th>ICD10</b-th>
                          <!-- <b-th style="width: 100px;">Aksi</b-th> -->
                      </b-tr>
                      <template v-for="(item,index) in form.icd10">
                        <b-tr class="item">
                            <b-td>{{ index+1 }}</b-td>
                            <b-td>
                                <div>{{ item.title }}</div>
                            </b-td>
                            <!-- <b-td>
                                <b-button variant="danger-600" size="sm" @click="delete10(index)" class="mr-2">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </b-td> -->
                        </b-tr>
                      </template>
                  </b-table-simple>
                  <div class="mt-10" style="display: none">
                      <label class="text-semi-14">Diagnosa</label>
                      <v-select v-model="icd10_id" aria-readonly="true" @input="onAdd10($event)" @search="onSearchIcd10" :options="OptionsIcd10" :reduce="OptionsIcd10 => OptionsIcd10" label="title" placeholder="-- Diagnosa --"></v-select>
                  </div>
              </div>
              <div class="mt-12">
                  <label class="c-form-label">Rencana Tindakan</label>
                  <b-textarea v-model="form.plan" readonly rows="3" />
              </div>
              <div class="mt-12">
                  <label class="c-form-label">Hasil yang Diharapkan</label>
                  <b-textarea v-model="form.result" readonly rows="3" />
              </div>
              <div class="mt-12">
                  <label class="c-form-label">keadaan Umum Pasien</label>
                  <b-textarea v-model="form.condition" readonly rows="3" />
              </div>
              <div style="display:none">
                  <label class="c-form-label">Dokter</label>
                    <div class="d-flex align-items-center">
                        <div class="col-lg-6 pl-0">
                            <v-select v-model="form.doctor_in_charge_id_1" aria-readonly="true" :options="OptionsDoctor" :reduce="(OptionsDoctor) => OptionsDoctor" label="title" placeholder="-- Dokter --"></v-select>
                        </div>
                        <b-form-radio-group v-model="form.dpjp_doctor_in_charge_id" :disabled="form.doctor_in_charge_id_1 == '' || form.doctor_in_charge_id_1 == null" size="sm">
                            <b-form-radio value="1"></b-form-radio>
                        </b-form-radio-group>
                    </div>
                </div>

                <div class="mt-12" style="display:none">
                  <label class="c-form-label">DPJP Asal</label>
                  <div class="d-flex align-items-center">
                      <div class="col-lg-6 pl-0">
                          <v-select v-model="form.doctor_in_charge_id_2" label="title" aria-readonly="true" aria-disabled="true" disabled></v-select>
                      </div>
                      <b-form-radio-group v-model="form.dpjp_doctor_in_charge_id" size="sm" stacked>
                          <b-form-radio value="2"></b-form-radio>
                      </b-form-radio-group>
                  </div>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">DPJP UTAMA</label>
                    <v-select v-model="form.doctor_in_charge_utama" aria-readonly="true" label="title" readonly disabled></v-select>
                </div>
            </div>
          </div>
        </section>

        

      <section class="card mt-20">
          <div class="card-header bg-primary-200 font-weight-bold">Penjamin</div>
          <div class="card-body">
              <div class="row">
                <div class="col-12 col-lg-4 px-0 px-lg-3 mt-1">
                    <label class="c-form-label">Hubungan Keluarga *</label>
                    <v-select v-model="form.responsible.responsible_id" :options="OptionsResponsible" :reduce="OptionsResponsible => OptionsResponsible.value" required label="title" placeholder="--Pilih Hubungan Keluarga --"></v-select>
                </div>
                <div class="col-12 col-lg-4 px-0 px-lg-3 mt-1">
                    <label class="c-form-label">Nama Penanggung Jawab *</label>
                    <b-form-input required autocomplete="off" v-model="form.responsible.name" type="text"></b-form-input>
                </div>
                <div class="col-12 col-lg-4 px-0 px-lg-3 mt-1">
                    <label class="c-form-label">Tanggal Lahir *</label>
                    <b-form-input required autocomplete="off" v-model="form.responsible.birth_date" type="date"></b-form-input>
                </div>
                <div class="col-12 col-lg-4 px-0 px-lg-3 mt-1">
                    <label class="c-form-label">Telepon *</label>
                    <b-form-input required autocomplete="off" v-model="form.responsible.phone" type="text"></b-form-input>
                </div>
                <div class="col-12 col-lg-6 px-0 px-lg-3 mt-1">
                    <label class="c-form-label">Alamat *</label>
                    <b-textarea required autocomplete="off" v-model="form.responsible.address_id" type="text"></b-textarea>
                </div>
              </div>
              <div class="mt-20">
                  <b-table bordered responsive :items="form.insuranceTable.items" :fields="form.insuranceTable.fields" head-variant="light" class="text-center" style="font-size: 13px;">
                      <template #cell(is_primary)="data">
                          <b-badge v-if="data.value == '1'" variant="primary" style="font-size: 16px; font-weight: normal">Utama</b-badge>
                      </template>
                      <template #cell(Aksi)="data">
                          <b-button variant="danger-600" size="sm" @click="deleteInsurance(data.index)">
                              <feather-icon icon="TrashIcon" />
                          </b-button>
                      </template>
                  </b-table>
              </div>
              <div class="d-flex justify-content-end mt-32">
                  <b-button @click="expandInsuranceForm" :class="active.insurence_form ? null : 'collapsed'" :aria-expanded="active.insurence_form ? 'true' : 'false'" aria-controls="collapse-insurance" pill size="sm" variant="indigo-200" class="px-3">
                      <feather-icon icon="PlusIcon" class="mr-2" />
                      <span class="text-indigo-700 fw-medium">Penjamin</span>
                  </b-button>
              </div>
              <!-- <div v-show="active.insurence_form" class="mt-2 mt-lg-0"> -->
              <b-collapse id="collapse-insurance" v-model="active.insurence_form" class="mt-2 mt-lg-0">
                  <div>
                      <div class="row mx-0">
                          <div class="col-lg-4 mt-12">
                            <label class="c-form-label">Jenis Penjamin</label>
                            <v-select v-model="form.insuranceInput.insurance_id" :options="OptionsInsurance" :reduce="(OptionsInsurance) => OptionsInsurance" label="title" placeholder="-- Jenis Penjamin --"></v-select>
                          </div>
                          <template v-if="form.insuranceInput.insurance_id && form.insuranceInput.insurance_id.title === 'Asuransi'">
                              <div class="col-lg-4 mt-12">
                                  <label class="c-form-label">Nama Penjamin </label>
                                  <v-select v-model="form.insuranceInput.company_id" :options="Options3rdCompany" :reduce="(Options3rdCompany) => Options3rdCompany" label="title" placeholder="-- Nama Penjamin --"></v-select>
                              </div>
                              <div class="col-lg-4 mt-12">
                                  <label class="c-form-label">Nama Penjamin COB </label>
                                  <v-select v-model="form.insuranceInput.company_id_cob" :options="Options3rdCompany" :reduce="(Options3rdCompany) => Options3rdCompany" label="title" placeholder="-- Nama Penjamin COB --"></v-select>
                              </div>
                              <div class="col-lg-4 mt-12">
                                  <label class="c-form-label">Nomor Anggota </label>
                                  <b-form-input v-model="form.insuranceInput.number_insurance"></b-form-input>
                              </div>
                          </template>
                            <div class="col-lg-4 mt-12">
                                <label class="c-form-label">Kelas Layanan </label>
                                <v-select v-model="form.insuranceInput.service_class_id" :options="OptionsServiceClass" :reduce="(OptionsServiceClass) => OptionsServiceClass" label="title" placeholder="-- Kelas Layanan --"></v-select>
                            </div>
                          <template v-if="form.insuranceInput.insurance_id && form.insuranceInput.insurance_id.title === 'Asuransi'">
                            <div class="col-lg-4 mt-12">
                                <label class="c-form-label">Hak Kelas Asuransi </label>
                                <v-select v-model="form.insuranceInput.service_class_id_right" :options="OptionsServiceClass" :reduce="(OptionsServiceClass) => OptionsServiceClass" label="title" placeholder="-- Hak Kelas Asuransi --"></v-select>
                            </div>
                          </template>
                          <!-- <div class="col-lg-4 mt-12">
                                <label class="c-form-label">Tanggal Akhir </label>
                                <b-form-input v-model="form.insuranceInput.expired" type="date"></b-form-input>
                            </div> -->
                      </div>
                  </div>
                  <div class="col mt-16 mt-lg-24 d-flex">
                      <div class="ml-auto row mx-0">
                          <b-button @click="expandInsuranceForm" pill variant="outline-indigo" class="px-3 mr-2">Batal</b-button>
                          <b-button @click.prevent="validationFormInsurance" pill variant="indigo" class="px-3">Simpan</b-button>
                      </div>
                  </div>
              </b-collapse>
          </div>
      </section>

      <div class="d-flex justify-content-end mt-24">
        <b-button @click="onTransferModal()" variant="outline-primary" class="mr-2">
          <feather-icon icon="XIcon" /> Batal
        </b-button>
        <b-button v-if="regData.is_transfer === 0" @click.prevent="validationTransferNew" :disabled="isDisabled_transfer" variant="primary">
          <feather-icon icon="SaveIcon" /> Simpan SPRI
        </b-button>
        
        <b-button @click="openPrintSPRI(regData.registration_destination_id)" variant="primary" class="ml-2">
          <b-icon icon="printer" /> <span>Print SPRI</span>
        </b-button>
      </div>

        <section class="card mt-24">
            <div class="card-header bg-primary-200 font-weight-bold">Dokumen</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 mt-12">
                        <label class="c-form-label">Dokumen SPRI</label>
                        <b-form-file v-model="form_letter.file" @change="inputPdf($event)"></b-form-file>
                    </div>
                </div>
            </div>
        </section>
      <div class="d-flex justify-content-end mt-24">
        <b-button @click="onTransferModal()" variant="outline-primary" class="mr-2">
          <feather-icon icon="XIcon" /> Batal
        </b-button>
        <b-button v-if="regData.is_transfer === 0" @click.prevent="validationTransferUpload" :disabled="isDisabled_transfer" variant="primary">
          <feather-icon icon="SaveIcon" /> Simpan Dokumen
        </b-button>
      </div>

        <b-card no-body class="mt-32" style="display: none">
            <b-tabs card>
                <b-tab title="Laboratorium" active>
                    <section>
                        <div class="mt-12">
                            <b-form-input type="text" v-model="searchlab" placeholder="Pencarian"></b-form-input>
                            <div class="col-lg-12 mt-3">
                                <h5>Checked Items:</h5>
                                <b-form-checkbox-group v-model="form.laboratory_request_items">
                                    <b-form-checkbox
                                        v-for="(item, index) in form.laboratory_request_items"
                                        :key="index"
                                        :value="item"
                                        >
                                        {{ item.costing_name }}
                                    </b-form-checkbox>
                                </b-form-checkbox-group>
                            </div>
                            <div class="col mt-12 py-2 bg-neutral-100" style="height: 400px; overflow-y: scroll;">
                                <div class="row">
                                    <b-form-checkbox-group size="sm" stacked>
                                        <b-form-checkbox-group class="col-lg-12" v-model="form.laboratory_request_items">
                                            <div v-for="(item, index) in filteredListLab" :key="index" :class="index != 0 ? 'mt-32' : 'mt-0'">
                                                <div class="font-weight-bold text-uppercase">{{ item.category_name }}</div>
                                                <div class="row mx-0"> 
                                                    <b-form-checkbox v-for="(list, packIndex) in item.category_items" v-bind:key="packIndex" :value="{'service_class_id': list.costing_services_class.service_class_id ,'laboratory_package_id':list.id, 'costing_name':list.costing_name}" class="col-lg-5">{{list.costing_name }}</b-form-checkbox>
                                                </div>
                                            </div>
                                        </b-form-checkbox-group>
                                    </b-form-checkbox-group>
                                </div>
                            </div>
                        </div>
                    </section>
                </b-tab>
                <b-tab title="Radiologi">
                    <section>
                        <div class="mt-12">
                            <b-form-input type="text" v-model="searchrad" placeholder="Pencarian"></b-form-input>
                            <div class="col-lg-12 mt-3">
                                <h5>Checked Items:</h5>
                                <b-form-checkbox-group v-model="form.radiology_request_items">
                                    <b-form-checkbox
                                        v-for="(item, index) in form.radiology_request_items"
                                        :key="index"
                                        :value="item"
                                        >
                                        {{ item.costing_name }}
                                    </b-form-checkbox>
                                </b-form-checkbox-group>
                            </div>
                            <div class="col mt-12 py-2 bg-neutral-100" style="height: 400px; overflow-y: scroll;">
                                <div class="row">
                                    <b-form-checkbox-group size="sm" stacked>
                                        <b-form-checkbox-group class="col-lg-12" v-model="form.radiology_request_items">
                                            <div v-for="(item, index) in filteredListRad" :key="index" :class="index != 0 ? 'mt-32' : 'mt-0'">
                                                <div class="font-weight-bold text-uppercase">{{ item.category_name }}</div>
                                                <div class="row mx-0"> 
                                                    <b-form-checkbox v-for="(list, packIndex) in item.category_items" v-bind:key="packIndex" :value="{'service_class_id': list.costing_services_class.service_class_id ,'radiology_package_id':list.id, 'costing_name':list.costing_name}" class="col-lg-5">{{list.costing_name }}</b-form-checkbox>
                                                </div>
                                            </div>
                                        </b-form-checkbox-group>
                                    </b-form-checkbox-group>
                                </div>
                            </div>
                        </div>
                    </section>
                </b-tab>
            </b-tabs>
        </b-card>

        <section class="card mt-24" style="display: none">
            <div class="card-header bg-primary-200 font-weight-bold">Pemberian Terapi</div>
            <div class="card-body">
                <div>
                    <b-form-group>
                        <b-form-checkbox v-model="checkboxChecked1">Infus</b-form-checkbox>
                        <b-form-textarea v-model="form.infus" :disabled="!checkboxChecked1" rows="3"></b-form-textarea>
                    </b-form-group>
                </div>
              <div>
                  <b-form-group>
                      <b-form-checkbox v-model="checkboxChecked2">Obat Injeksi</b-form-checkbox>
                      <b-form-textarea v-model="form.drug_inj" :disabled="!checkboxChecked2" rows="3"></b-form-textarea>
                  </b-form-group>
              </div>
              <div>
                  <b-form-group>
                      <b-form-checkbox v-model="checkboxChecked3">Obat Oral</b-form-checkbox>
                      <b-form-textarea v-model="form.drug_orl" :disabled="!checkboxChecked3" rows="3"></b-form-textarea>
                  </b-form-group>
              </div>
            </div>
        </section>

        <!-- <section class="card mt-24">
            <div class="card-header bg-primary-200 font-weight-bold">Derajat Pasien</div>
            <div class="card-body">
                <div>
                    <label class="c-form-label">Pilih Derajat:</label>
                    <b-form-checkbox-group v-model="form.derajat" id="checkbox-group">
                        <b-form-checkbox value="1">Derajat 0</b-form-checkbox>
                        <b-form-checkbox value="2">Derajat 1</b-form-checkbox>
                        <b-form-checkbox value="3">Derajat 2</b-form-checkbox>
                        <b-form-checkbox value="4">Derajat 3</b-form-checkbox>
                    </b-form-checkbox-group>
                </div>
            </div>
        </section> -->
        <section class="card mt-24">
            <div class="card-header bg-primary-200 font-weight-bold">Ruangan</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 mt-12">
                        <label class="c-form-label">Tipe Ruangan</label>
                        <v-select v-model="form.type_room_id" :options="TypeRoomOptions" :reduce="(TypeRoomOptions) => TypeRoomOptions.value" label="title" placeholder="-- Pilih Tipe Ruangan --"></v-select>
                    </div>

                    <div class="col-lg-4 mt-12">
                        <label class="c-form-label">Ruangan</label>
                        <v-select v-model="form.room_id" :disabled="disabled.room" :options="OptionsRoom" :reduce="(OptionsRoom) => OptionsRoom.value"
                          label="title" placeholder="-- Ruangan --"></v-select>
                    </div>

                    <div class="col-lg-4 mt-12">
                        <label class="c-form-label">Bed</label>
                        <v-select v-model="form.bed_id" :disabled="disabled.bed" :options="OptionsBed" :reduce="(OptionsBed) => OptionsBed.value"
                          label="title" placeholder="-- Bed --"></v-select>
                    </div>
                </div>
            </div>
        </section>

      <div class="d-flex justify-content-end mt-24">
        <b-button @click="onTransferModal()" variant="outline-primary" class="mr-2">
          <feather-icon icon="XIcon" /> Batal
        </b-button>
        <b-button v-if="regData.is_transfer === 0" @click.prevent="validationTransfer" :disabled="isDisabled_transfer" variant="primary">
          <feather-icon icon="SendIcon" /> Transfer
        </b-button>
      </div>

    </b-modal>

    <b-modal v-model="active.modal_checkout" title="Ganti Tanggal Checkout">
      <div>
          <label class="c-form-label">Tanggal Checkout</label>
          <b-form-input v-model="form_checkout.checkout_date" type="datetime-local" autocomplete="off" />
      </div>

      <template #modal-footer>
        <div class="d-flex w-100 justify-content-end">
          <b-button @click="validationCheckout" :disabled="active.disabled_checkout" variant="primary">Simpan</b-button>
        </div>
      </template>
    </b-modal>

    <b-modal v-model="active.modal_refer_internal" size="lg" title="Rujuk Internal" hide-footer>
      <div>
        <div class="card">
          <div class="card-header bg-primary text-white font-weight-bold">Form Rujuk Internal</div>
          <div class="card-body">
            <div class="row">
              <div class="col-lg-4 mt-12 mt-lg-0" style="display:none">
                <label class="c-form-label">Instalasi</label>
                <v-select v-model="form_refer_internal.health_facility_id" disabled aria-readonly="true" :options="OptionsHealthFacility" :reduce="OptionsHealthFacility => OptionsHealthFacility.value" label="title" placeholder="-- Instalasi --"></v-select>
              </div>
              <div class="col-lg-4 mt-12 mt-lg-0">
                <label class="c-form-label">Tanggal</label>
                <b-input type="date" v-model="form_refer_internal.consultation_date" />
              </div>
              <div class="col-lg-4 mt-12 mt-lg-0">
                <label class="c-form-label">Poli</label>
                <v-select v-model="form_refer_internal.health_facility_item_id" aria-readonly="true" :options="OptionsHealthFacilityItem" :reduce="OptionsHealthFacilityItem => OptionsHealthFacilityItem.value" label="title" placeholder="-- Poliklinik --"></v-select>
              </div>
              <div class="col-lg-4 mt-12 mt-lg-0">
                <label class="c-form-label">Dokter</label>
                <v-select v-model="form_refer_internal.doctor_in_charge_id" class="col px-0" aria-readonly="true" :options="OptionsDoctor" :reduce="OptionsDoctor => OptionsDoctor.value" label="title" placeholder="-- Dokter --"></v-select>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <div class="d-flex justify-content-end">
                <b-button @click="onCancelReferInternal" class="mr-2">Batal</b-button>
                <b-button variant="primary-500" :disabled="isDisabled" @click.prevent="onSaveReferInternal">Simpan</b-button>
            </div>
          </div>
          <div class="card">
            <b-table bordered responsive small head-variant="light" style="height: 400px" :items="ReferInternalItems" :fields="ReferInternalTable.fields" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center"></b-table>
          </div>
        </div>
      </div>
    </b-modal>
  </div>
</template>
<script>
import { mapState } from 'vuex';

import patientMenu from '@/@core/components/tab-menu/patientMenu.vue';
import administration from '@/views/layouts/patient/doctor/administration.vue';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import vSelect from 'vue-select'
import moment from 'moment'

import ModernLabel from '@/@core/components/label/ModernLabel.vue';

export default {
  name: 'DoctorStudies',
  components: {
    patientMenu, administration, vSelect, ToastificationContent, ModernLabel
  },
  data() {
    return {
      // general section
      urlbe:process.env.VUE_APP_IMAGE,
      isDisabled: false,
      reg_des_id: '',
      doctor_list: [],

      disabled: {
        room: false,
        bed: false,
        dpjp: false,
      },
      active: {
        allergy: false,
        modal_transfer: false,
        modal_edit: false,
        modal_refer_internal: false,
        insurence_dpjp_form: false,
        insurence_form: false,
        modal_sep: false,
        modal_kronologis: false,
        modal_checkout: false,
        disabled_checkout: false,
      },
      form_refer_internal:{
        id:'',
        registration_destination_id:'',
        health_facility_id:'3cb626f5-9148-4fb1-8663-3d7f028b01a6',
        health_facility_item_id:'',
        doctor_in_charge_id:'',
        consultation_date:'',
      },
      regData: {},

      // DPJP section
      temp_form_insurance: {
        insurance_id: '',
        company_id: '',
        company_id_cob: '',
        number_insurance: '',
        service_class_id: '',
      },

      insuranceFields: [
        { key: 'is_primary', label: '' },
        { key: 'insurance_name', label: 'Jenis Penjamin' },
        { key: 'company_name', label: 'Nama Penjamin' },
        { key: 'number_insurance', label: 'No Anggota / NIP' },
        { key: 'service_class_name', label: 'Kelas Layanan' },
        { key: 'Aksi' },
      ],

      form_dpjp: {
        doctor_in_charge_id: '',
        doctor_in_charge_id_2: '',
        doctor_in_charge_id_3: '',
        doctor_in_charge_id_4: '',
        doctor_in_charge_id_5: '',
        insurances: [],
        health_facility_item_id: '',
      },
      // display_insurances: [],

      // SEP-section
      isDisabledSep: false,
      isDisabledKronologis: false,
      isDisabled_insurance:false,
      form_sep: {
        registration_destination_id: '',
        file_insurance: null,
        file_spri: null,
        noKunjungan: '',
        jns_sep: 1,
        sep: '',
      },
      form_kronologis: {
        registration_destination_id: '',
        file_kronologis: null,
      },
      patient_norm: '',
      InsuranceTable: {
        fields: [
          { key: 'register_date', label: 'Tgl Kunjungan' },
          { key: 'insurance_name', label: 'Penjamin' },
          { key: 'number_insurance', label: 'No Anggota' },
          { key: 'service_class_name', label: 'Kelas' },
          { key: 'sep', label: 'No SEP' },
          { key: 'action', label: 'Aksi' },
        ],
      },
      InsuranceItems:[],
      ReferInternalTable: {
        fields: [
          { key: 'no_refer', label: 'Nomer Rujukan' },
          { key: 'consultation_date', label: 'Tgl Rujukan' },
          { key: 'health_facility_item_name', label: 'Poliklinik' },
          { key: 'doctor_name', label: 'Dokter' },
        ],
      },
      ReferInternalItems:[],
      dataInpatient:{},
      form_letter:{
        registration_destination_id:'',
        letter_type_id:'0cf3a866-010a-4937-be09-b3994f9000ac',
        date:'',
        file:'',
        extension:'',
      },
      // TRANSFER section 
      form: {
        registration_destination_past_id: '',
        registration_id: '',
        health_facility_item_id: '',
        doctor_in_charge_id: '',
        bed_id: '',
        nurse_id: '-',
        doctor_in_charge_sec_id: '',
        general_reason: '',
        exam_done: '',
        treatment_done: '',
        infus_sts: '',
        infus: '',
        drug_inj_sts: '',
        drug_inj: '',
        drug_orl_sts: '',
        drug_orl: '',
        transfer_reason: '',
        indication: '',
        plan: '',
        result: '',
        condition: '',
        responsible: {
          name:'',
          address_id:'',
          birth_date:'',
          phone:'',
          responsible_id:'',
        },

        insuranceToInput: [ 
          {
            is_primary: 1,
            insurance_id: 1,
            company_id: '',
            company_id_cob: '',
            number_insurance: '',
            expired: '',
            service_class_id: '',
            insurance_name: 'Tunai/Pribadi',
            company_name: '',
            service_class_name: '',
          }
        ],

        transfer_reason_type_id: '',
        isTransportable: 0,
        main_complaint: '',

        icd10: [],
        laboratory_request_items: [],
        radiology_request_items: [],
        
        // supporting properties
        dpjp_doctor_in_charge_id: '',
        doctor_in_charge_id_1: '',
        doctor_in_charge_id_2: '',
        doctor_in_charge_utama: '',
        type_room_id: '',
        room_id: '',
        
        insuranceInput: {
          service_class_id:'',
          service_class_id_right:'',
          company_id:'',
          company_id_cob:''
        },

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
      },

      icd10_id: '',
      OptionsIcd10: [],

      checkboxChecked1: false,
      checkboxChecked2: false,
      checkboxChecked3: false,
      isDisabled_transfer:false,

      supportPackage: {
          lab_package_list: [],
          rad_package_list: [],
      },
      searchlab: '',
      searchrad: '',

      OptionsGeneralReason: [
        { value: 1, title: 'Tampak sakit ringan' },
        { value: 2, title: 'Tampak sakit sedang' },
        { value: 3, title: 'Tampak sakit berat' }
      ],

      OptionsSEP: [
        { value: 1, title: 'SEP' },
        { value: 2, title: 'K.Internal' },
        { value: 3, title: 'Raber Post Inap' },
        { value: 4, title: 'Toleransi OP' }
      ],

      OptionsTransferReason: [
        { value: 1, title: 'Fase emergency pasien telah selesai, membutuhkan rawat inap' },
        { value: 2, title: 'Pasien membutuhkan Intensive care (ICU/NICU/PICU/ICCU)' },
        { value: 3, title: 'Pasien naik kelas rawat' },
        { value: 4, title: 'Pasien mengalami keadaan emergency' },
        { value: 5, title: 'Alasan lainnya' },
      ],

      // CHANGE CHECKOUT DATE
      form_checkout: {
        registration_destination_id: '',
        checkout_date: ''
      }
    }
  },
  watch: {
    // 'form.health_facility_id'(obj) {
    //   if (obj) {
    //     this.$store.dispatch('healthfacilityitem/getParam', obj.value)
    //   }
    // },
    // 'form.health_facility_item_id'(obj) {
    //   if (obj) {
    //     return this.$store.dispatch('employee/getParamSpecialist', obj.value)
    //   }
    // },
    
    // edit dpjp
    'form_dpjp.health_facility_item_id'(val) {
      if (val) {
        return this.$store.dispatch('employee/getParam', val)
      }
    },

    // form transfer
    'form.transfer_reason_type_id'(val) {
        if (val != 5 ) {
            this.form.transfer_reason = ''
        }
    },
    'checkboxChecked1'(val) {
        if (val === false ) {
            this.form.infus = ''
        }
    },
    'checkboxChecked2'(val) {
        if (val === false ) {
            this.form.drug_inj = ''
        }
    },
    'checkboxChecked3'(val) {
        if (val === false ) {
            this.form.drug_orl = ''
        }
    },
    // refer internal
    'form_refer_internal.health_facility_id'(val) {
      if (val) {
        this.$store.dispatch('healthfacilityitem/getParam', val)
      }
    },
    'form_refer_internal.health_facility_item_id'(val) {
      if (val) {
        return this.$store.dispatch('employee/getParam', val)
      }
    },

    'form.type_room_id'(value) {
      this.form.room_id = ''
      this.form.bed_id = ''

      if (value) {
        return this.$store.dispatch('room/getParamRoom', value)
      } else {
        this.$store.dispatch('room/getParamRoom', '')
        this.$store.dispatch('room/getParamBed', '')
      }
    },

    'form.room_id'(value) {
      this.form.bed_id = ''

      if (value) {
        return this.$store.dispatch('room/getParamBed', value)
      } else {
        this.$store.dispatch('room/getParamBed', '')
      }
    },
    'form.dpjp_doctor_in_charge_id'(value) {
      if (value == 1) {
        this.form.doctor_in_charge_utama = this.form.doctor_in_charge_id_1
      } else if (value == 2) {
        this.form.doctor_in_charge_utama = this.form.doctor_in_charge_id_2
      }
    },

    'form.doctor_in_charge_id_1'(value) {
      if (!value && this.form.dpjp_doctor_in_charge_id == 1) {
        if (this.form.doctor_in_charge_id_2) {
          this.form.dpjp_doctor_in_charge_id = 2
        } else {
          this.form.dpjp_doctor_in_charge_id = ''
        }
      }
    },
    'form.insuranceInput.insurance_id'(obj) {
        this.form.insuranceInput.service_class_id = ''
        this.form.insuranceInput.service_class_id_right = ''
      if (obj.value != 2) {
        this.form.insuranceInput.company_id = ''
        this.form.insuranceInput.company_id_cob = ''
        this.form.insuranceInput.number_insurance = ''
        this.form.insuranceInput.expired = ''
      }
    },

  },
  methods: {
    jnssep(value) {
        if(value == 2) return 'K.Internal'       
        if(value == 3) return 'Raber Post Inap'       
        if(value == 4) return 'Toleransi Operasi'       
        return ''
    }, 
    inputPdf (e) {
      console.log(e)
      for (let i = 0; i < e.target.files.length; i++) {
        const file = e.target.files[i]
        const reader = new FileReader()
        reader.onload = (filez) => {
          this.form_letter.extension = 'pdf'
          this.form_letter.file = filez.target.result.replace("data:"+ file.type +";base64,", '');
        }
        reader.readAsDataURL(file)
      }
    },
    openPrintSPRI(id){
      const url = this.$router.resolve({ path: '/prints/spri/'+ id }).href;
      window.open(url, '_blank');
    },
    notification(a, b, c, d) {
      this.$toast({
        component: ToastificationContent,
        position: 'top-right',
        props: { title: a, icon: b, variant: c, text: d }
      })
    },
    async getDataInsurance(id) {
      try {
        const response = await this.$store.dispatch('registration/getInsuranceByNorm', id)
        // console.log(response)
        this.InsuranceItems = response.rows
      } catch (error) {
        this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
      }
    },
    async getDataReferInternal(id) {
      try {
        const response = await this.$store.dispatch('registration/getReferInternalByNorm', id)
        console.log(response)
        this.ReferInternalItems = response
      } catch (error) {
        this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
      }
    },
    async getDataInsuranceEdit() {
      try {
        const response = await this.$store.dispatch('registration/getInsuranceById', this.reg_des_id)
        this.form.insuranceTable.items = response
        this.form.insuranceToInput = response

        this.form_dpjp.insurances = []
        for (let i = 0; i < response.length; i++) {
          const el = response[i];
          this.form_dpjp.insurances.push({ ...el, is_delete: 0, is_exist: 1 })
        }

      } catch (error) {
        this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
      }
    },
    // fillDisplayInsurances() {
    //     this.display_insurances = []
    //     let insurances = this.form_dpjp.insurances
        
    //     if (insurances) {
    //         for (let i = 0; i < insurances.length; i++) {
    //             const el = insurances[i];
    //             let temp = {
    //               is_primary: el.is_primary, 
    //               insurance_id: el.insurance_id, 
    //               company_id: el.company_id,
    //               number_insurance: el.number_insurance,
    //               expired: el.expired,
    //               service_class_id: el.service_class_id,
    //               insurance_name: el.insurance_name, 
    //               company_name: el.company_name, 
    //               service_class_name: el.service_class_name,
    //               is_delete: el.is_delete,
    //               is_exist: el.is_exist,
    //             }

    //             if (temp.is_delete == 0) {
    //                 this.display_insurances.push(temp)
    //             }
    //         }            
    //     } else {
    //       this.display_insurances = []
    //     }
        
    //     console.log('display: ', this.display_insurances)
    //     console.log('utama: ', this.form_dpjp.insurances)
    // },
    getInpatient(id) {
      try {
        this.$store.dispatch('soap/getInpatientByRegId', id).then((response)=>{
          if (response) {
            this.form.dpjp_doctor_in_charge_id = 1
            this.dataInpatient = response[0]
            this.form.id = response[0].id
            this.form.indication = response[0].indication
            this.form.norm = this.regData.norm
            this.form.plan = response[0].plan
            this.form.result = response[0].result
            this.form.soap_id = response[0].soap_id
            this.form.condition = response[0].condition
            this.form.doctor_in_charge_id = response[0].doctor_in_charge_id
            this.form.type_room_id = response[0].type_room_id
            this.form.doctor_in_charge_id_1 = {value: response[0].doctor_in_charge_id, title:response[0].employee_doctor_name}

            this.form.responsible.responsible_id = response[0].responsible_person.responsible_id
            this.form.responsible.name = response[0].responsible_person.name
            this.form.responsible.birth_date = response[0].responsible_person.birth_date
            this.form.responsible.phone = response[0].responsible_person.phone
            this.form.responsible.address_id = response[0].responsible_person.address_id
           
          }
        })
        
      } catch (error) {
        console.error('Error fetching data:', error);
        this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
      }
    },
    getListVital(id) {
      try {
        
        // FROM NURSE

        let dataVital = {}
        this.$store.dispatch('nurse/getListVital', id).then((response)=>{
          if (response) {
            dataVital = response[0]
          }
        })

        if (dataVital) {
            this.$set(this.form, 'nurse_id', dataVital["id"] ?? '');
            this.form.doctor_in_charge_id_2 = {
              title: this.regData.doctor_in_charge_name,
              value: this.regData.doctor_in_charge_id
            }
        } else {
          console.error('No nurse data received');
        }

        const outpatient = '3cb626f5-9148-4fb1-8663-3d7f028b01a6' // rawat jalan
        const inpatient = '40f06534-07ec-4221-ad9d-dea08196079f' // rawat inap
        const emergency = '444b3134-5b75-4b67-af72-a558d46716d7' // IGD

        if (this.regData.health_facility_id == inpatient) {
          // FROM CPPT
          this.$store.dispatch('cppt/getListCPPT', { registration_destination_id: id }).then((data) => {
              let cppt_data = data[0]

              if (cppt_data) {
                this.form.laboratory_request_items = cppt_data.laboratory_request_items
                this.form.radiology_request_items = cppt_data.radiology_request_items

                if (cppt_data.icd10) {
                    const mappedIcd10 = cppt_data.icd10.map(item => {
                        return {
                            icd10_id: item.icd10_id,
                            value: item.icd10_id,
                            title: `${item.code} ${item.name}`, 
                            action: "action"
                        };
                    });                
                    this.form.icd10 = mappedIcd10;
                }  
              }
          })
          
        } else if (this.regData.health_facility_id == outpatient || this.regData.health_facility_id == emergency){
          // FROM SOAP
          this.$store.dispatch('soap/getByRegId', id).then((data)=>{
              let soap_data = data[0]

              if (soap_data) {
                this.form.laboratory_request_items = soap_data.laboratory_request_items
                this.form.radiology_request_items = soap_data.radiology_request_items
                this.form.main_complaint = soap_data.main_complaint

                if (soap_data.icd10) {
                    const mappedIcd10 = soap_data.icd10.map(item => {
                        return {
                            icd10_id: item.icd10_id,
                            value: item.icd10_id,
                            title: `${item.code} ${item.name}`, 
                            action: "action"
                        };
                    });                
                    this.form.icd10 = mappedIcd10;
                }  
              }
            })  
        }
        
      } catch (error) {
        console.error('Error fetching data:', error);
        this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
      }
    },

    async getData(regDesId) {
      try {
        const response = await this.$store.dispatch('registration/getById', regDesId)
        this.regData = response[0]

        this.patient_norm = response[0].norm
        this.getDataInsurance(response[0].norm)
        this.getDataReferInternal(response[0].norm)
        this.getDataInsuranceEdit()

        this.$store.dispatch('employee/getDoctorOptions', '').then((response) => {
            this.doctor_list = response
        })

      } catch (error) {
        this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
      }

    },
    validationFormInsurance() {
      this.form.insuranceTable.items = []
      this.form.insuranceToInput = []

      if (this.form.insuranceInput) {
        
        if (this.form.insuranceInput.insurance_id.value === 1 || this.form.insuranceInput.insurance_id.value === 3) {
          this.form.insuranceTable.items.push({
            is_primary: 1,
            insurance_name: this.form.insuranceInput.insurance_id.title,
            company_name: '-',
            number_insurance: '-',
            service_class_id: this.form.insuranceInput.service_class_id.value,
            expired: '-',
            service_class_name: this.form.insuranceInput.service_class_id.title,
          })

          this.form.insuranceToInput.push({
            is_primary: 1,
            insurance_id: this.form.insuranceInput.insurance_id.value,
            company_id: '',
            company_id_cob: '',
            company_name_cob: '-',
            number_insurance: '',
            expired: '',
            service_class_id: this.form.insuranceInput.service_class_id.value,
            insurance_name: this.form.insuranceInput.insurance_id.title,
            company_name: '-',
            service_class_name: this.form.insuranceInput.service_class_id.title,
          })

        } else if (this.form.insuranceInput.insurance_id.value === 2) {
          this.form.insuranceTable.items.push({
            is_primary: 1,
            insurance_id: this.form.insuranceInput.insurance_id.value,
            company_id: this.form.insuranceInput.company_id.value,
            company_id_cob: this.form.insuranceInput.company_id_cob.value,
            number_insurance: this.form.insuranceInput.number_insurance,
            expired: this.form.insuranceInput.expired,
            service_class_id: this.form.insuranceInput.service_class_id.value,
            service_class_id_right: this.form.insuranceInput.service_class_id_right.value,
            insurance_name: this.form.insuranceInput.insurance_id.title,
            company_name: this.form.insuranceInput.company_id.title,
            service_class_name: this.form.insuranceInput.service_class_id.title,
          })
          this.form.insuranceToInput.push({
            is_primary: 1,
            insurance_id: this.form.insuranceInput.insurance_id.value,
            company_id: this.form.insuranceInput.company_id.value,
            company_id_cob: this.form.insuranceInput.company_id_cob.value,
            number_insurance: this.form.insuranceInput.number_insurance,
            expired: this.form.insuranceInput.expired,
            service_class_id: this.form.insuranceInput.service_class_id.value,
            service_class_id_right: this.form.insuranceInput.service_class_id_right.value,
            insurance_name: this.form.insuranceInput.insurance_id.title,
            company_name: this.form.insuranceInput.company_id.title,
            company_name_cob: this.form.insuranceInput.company_id_cob.title,
            service_class_name: this.form.insuranceInput.service_class_id.title,
          })
        }

        this.clearInsuranceForm()
        
      }
    },
    expandInsuranceForm() {
      this.active.insurence_form = !this.active.insurence_form
      if (this.active.insurence_form == false) {
        this.clearInsuranceForm()
      }
    },
    clearInsuranceForm() {
        this.form.insuranceInput.insurance_id = ''
        this.form.insuranceInput.company_id = ''
        this.form.insuranceInput.company_id_cob = ''
        this.form.insuranceInput.number_insurance = ''
        this.form.insuranceInput.expired = ''
        this.form.insuranceInput.service_class_id = ''
        this.active.insurence_form = false
    },
    deleteInsurance(index) {
      this.form.insuranceTable.items.splice(index, 1)
      this.form.insuranceToInput.splice(index, 1)
    },

    onSearchIcd10(search, loading) {
        if(search.length >= 3) {
            loading(true);
            this.$store.dispatch('icd10/getParam', search).then(data => {
                this.OptionsIcd10 = []
                for (let i = 0; i < data.length; i++) {
                    const option = []
                    for (const key in data[i]) {
                        if (key === 'id') { option['value'] = data[i][key] } else if (key === 'name') { option['title'] = data[i]['code']+' '+data[i][key] } else if (key === 'id') { option['oid'] = data[i][key] }
                    }
                    this.OptionsIcd10.push(Object.assign({}, option))
                }
                loading(false)
            })
        }
    },
    onAdd10 (event) {
        this.icd10_id = ''

        if (this.form.icd10.some(item => item.icd10_id === event.value)) {
            this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list ICD10 yang sama')
        } else {
            this.form.icd10.push({ icd10_id:event.value, title:event.title, value:event.value, action: 'action'})
        }
        // console.log('this.form.icd10', this.form.icd10);

    },
    delete10 (index) {
        this.form.icd10.splice(index, 1)
    },

    validationTransfer() {

        try {
            this.isDisabled_transfer = true
            
            this.form.registration_destination_past_id = this.regData.registration_destination_id
            this.form.registration_id = this.regData.registration_id
            this.form.health_facility_item_id = this.regData.health_facility_item_id

            this.form.infus_sts = this.checkboxChecked1
            this.form.drug_inj_sts = this.checkboxChecked2
            this.form.drug_orl_sts = this.checkboxChecked3
            this.form.version = 'v20241122114'

            if (this.form.doctor_in_charge_utama) {
                this.form.doctor_in_charge_id = this.form.doctor_in_charge_utama.value
                
                if (this.form.doctor_in_charge_utama.value === this.form.doctor_in_charge_id_1.value) {
                    this.form.doctor_in_charge_sec_id = this.form.doctor_in_charge_id_2.value

                } else if (this.form.doctor_in_charge_utama.value === this.form.doctor_in_charge_id_2.value) {
                    this.form.doctor_in_charge_sec_id = this.form.doctor_in_charge_id_1.value
                }

                this.$store.dispatch("registration/formTransferPostV2", this.form).then((response) => {
                  this.notification('Success', 'CheckIcon', 'success', 'You have successfully')
                  this.active.modal_transfer = false
                  this.isDisabled_transfer = false
                  this.resetTransferForm()

                  location.reload()
                })
            } else {
                this.notification('Warning', 'CloseIcon', 'warning', 'Silakan Pilih DPJP Utama Terlebih Dahulu');
            }
            
            console.log('send', this.form);
            
        } catch (error) {
          if (this.form.doctor_in_charge_utama) {
            this.notification('Warning', 'CloseIcon', 'warning', 'Silakan Pilih DPJP Utama Terlebih Dahulu');

          } else {
            this.notification('Failed', 'CloseIcon', 'danger', 'You have unsuccessful');
          }

          this.isDisabled_transfer = false
          console.error('Error at: ', error);
        }
    },
    validationTransferNew() {
      try {
          this.isDisabled_transfer = true
          this.$store.dispatch("soap/formPostInPatient", this.form).then((response) => {
            this.notification('Success', 'CheckIcon', 'success', 'You have successfully')
            this.active.modal_transfer = false
            this.isDisabled_transfer = false
            this.resetTransferForm()
            location.reload()
          })
          console.log('send', this.form);
      } catch (error) {
        this.isDisabled_transfer = false
        console.error('Error at: ', error);
      }
    },
    validationTransferUpload() {
      try {
          this.isDisabled_transfer = true
          this.form_letter.registration_destination_id = this.regData.registration_destination_id
          this.form_letter.date = this.dataInpatient.date_updated
          this.form_letter.letter_type_id = '0cf3a866-010a-4937-be09-b3994f9000ac'
          this.$store.dispatch("letter/formPostPut", this.form_letter).then((response) => {
            this.notification('Success', 'CheckIcon', 'success', 'You have successfully')
            this.active.modal_transfer = false
            this.isDisabled_transfer = false
            this.resetTransferForm()
            location.reload()
          })
          console.log('send', this.form);
      } catch (error) {
        this.isDisabled_transfer = false
        console.error('Error at: ', error);
      }
    },
    resetTransferForm() {

      this.form.registration_destination_past_id = ''
      this.form.registration_id = ''
      this.form.health_facility_item_id = ''
      this.form.doctor_in_charge_id = ''
      this.form.bed_id = ''
      this.form.nurse_id = ''
      this.form.doctor_in_charge_sec_id = ''
      this.form.general_reason = ''
      this.form.exam_done = ''
      this.form.treatment_done = ''
      this.form.infus_sts = ''
      this.form.infus = ''
      this.form.drug_inj_sts = ''
      this.form.drug_inj = ''
      this.form.drug_orl_sts = ''
      this.form.drug_orl = ''
      this.form.transfer_reason = ''
      this.form.insuranceInput = {}
      this.form.insuranceToInput = [
        {
          is_primary: 1, 
          insurance_id: 1,
          insurance_name: 'Tunai/Pribadi',
          company_id: '',
          company_id_cob: '',
          company_name: '',
          company_name_cob: '',
          number_insurance: '',
          expired: '',
          service_class_id: '',
          service_class_name: '',
        }
      ]

      this.form.transfer_reason_type_id = ''
      this.form.isTransportable = 0
      this.form.main_complaint = ''
      this.form.icd10 = []
      this.form.laboratory_request_items = []
      this.form.radiology_request_items = []
      
      
      this.form.insuranceTable.items = [
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

    onCheckoutModal() {
      this.active.modal_checkout = !this.active.modal_checkout
      this.form_checkout.registration_destination_id = this.regData.registration_destination_id
      this.form_checkout.checkout_date = this.regData.checkout_date
    },

    validationCheckout(){
      // console.log('checlout: ', this.form_checkout);
      let params = {
        registration_destination_id: this.form_checkout.registration_destination_id,
        checkout: { checkout_date: this.form_checkout.checkout_date }
      }

      try {
        this.active.disabled_checkout = true
        this.$store.dispatch('registration/formChangeCheckout', params).then((response) => {
          this.notification('Success','CheckIcon','success','You have successfully')
          this.active.modal_checkout = false
          this.active.disabled_checkout = false
          this.getData(this.$route.params.id)          
        })
        
      } catch (error) {
        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
        this.active.disabled_checkout = false
        console.error('Error at: ', error);
      }

      
    },

    onReferInternalModal() {
      this.active.modal_refer_internal = !this.active.modal_refer_internal;
      
      this.form_refer_internal.registration_destination_id = this.regData.registration_destination_id
      this.$store.dispatch('healthfacilityitem/getParam', '3cb626f5-9148-4fb1-8663-3d7f028b01a6')
      this.form_refer_internal.health_facility_item_id = ''
      this.form_refer_internal.doctor_in_charge_id = ''
      this.form_refer_internal.consultation_date = ''
            
    },

    onEditModal() {
      this.active.modal_edit = !this.active.modal_edit;      
      this.form_dpjp.health_facility_item_id = this.regData.health_facility_item_id
      this.form_dpjp.doctor_in_charge_id = this.regData.doctor_in_charge_id
      this.form_dpjp.doctor_in_charge_id_2 = this.regData.doctor_in_charge_id_2
      this.form_dpjp.doctor_in_charge_id_3 = this.regData.doctor_in_charge_id_3
      this.form_dpjp.doctor_in_charge_id_4 = this.regData.doctor_in_charge_id_4
      this.form_dpjp.doctor_in_charge_id_5 = this.regData.doctor_in_charge_id_5
      this.getDataInsuranceEdit()
    },

    changeDoctor1() {
        const { doctor_in_charge_id, doctor_in_charge_id_2, doctor_in_charge_id_3, doctor_in_charge_id_4, doctor_in_charge_id_5 } = this.form_dpjp;
        
        if (
            doctor_in_charge_id &&
            (doctor_in_charge_id === doctor_in_charge_id_2 || 
            doctor_in_charge_id === doctor_in_charge_id_3 || 
            doctor_in_charge_id === doctor_in_charge_id_4 || 
            doctor_in_charge_id === doctor_in_charge_id_5)
        ) {
            this.notification('Warning', 'CloseIcon', 'warning', 'Tidak dapat memilih dokter yang sama');
            this.form_dpjp.doctor_in_charge_id = '';
        }
    },

    changeDoctor2() {
        const { doctor_in_charge_id, doctor_in_charge_id_2, doctor_in_charge_id_3, doctor_in_charge_id_4, doctor_in_charge_id_5 } = this.form_dpjp;
        
        if (
            doctor_in_charge_id_2 &&
            (doctor_in_charge_id_2 === doctor_in_charge_id || 
            doctor_in_charge_id_2 === doctor_in_charge_id_3 || 
            doctor_in_charge_id_2 === doctor_in_charge_id_4 || 
            doctor_in_charge_id_2 === doctor_in_charge_id_5)
        ) {
            this.notification('Warning', 'CloseIcon', 'warning', 'Tidak dapat memilih dokter yang sama');
            this.form_dpjp.doctor_in_charge_id_2 = '';
        }
    },

    changeDoctor3() {
        const { doctor_in_charge_id, doctor_in_charge_id_2, doctor_in_charge_id_3, doctor_in_charge_id_4, doctor_in_charge_id_5 } = this.form_dpjp;
        
        if (
            doctor_in_charge_id_3 &&
            (doctor_in_charge_id_3 === doctor_in_charge_id || 
            doctor_in_charge_id_3 === doctor_in_charge_id_2 || 
            doctor_in_charge_id_3 === doctor_in_charge_id_4 || 
            doctor_in_charge_id_3 === doctor_in_charge_id_5)
        ) {
            this.notification('Warning', 'CloseIcon', 'warning', 'Tidak dapat memilih dokter yang sama');
            this.form_dpjp.doctor_in_charge_id_3 = '';
        }
    },

    changeDoctor4() {
        const { doctor_in_charge_id, doctor_in_charge_id_2, doctor_in_charge_id_3, doctor_in_charge_id_4, doctor_in_charge_id_5 } = this.form_dpjp;
        
        if (
            doctor_in_charge_id_4 &&
            (doctor_in_charge_id_4 === doctor_in_charge_id || 
            doctor_in_charge_id_4 === doctor_in_charge_id_2 || 
            doctor_in_charge_id_4 === doctor_in_charge_id_3 || 
            doctor_in_charge_id_4 === doctor_in_charge_id_5)
        ) {
            this.notification('Warning', 'CloseIcon', 'warning', 'Tidak dapat memilih dokter yang sama');
            this.form_dpjp.doctor_in_charge_id_4 = '';
        }
    },

    changeDoctor5() {
        const { doctor_in_charge_id, doctor_in_charge_id_2, doctor_in_charge_id_3, doctor_in_charge_id_4, doctor_in_charge_id_5 } = this.form_dpjp;
        
        if (
            doctor_in_charge_id_5 &&
            (doctor_in_charge_id_5 === doctor_in_charge_id || 
            doctor_in_charge_id_5 === doctor_in_charge_id_2 || 
            doctor_in_charge_id_5 === doctor_in_charge_id_3 || 
            doctor_in_charge_id_5 === doctor_in_charge_id_4)
        ) {
            this.notification('Warning', 'CloseIcon', 'warning', 'Tidak dapat memilih dokter yang sama');
            this.form_dpjp.doctor_in_charge_id_5 = '';
        }
    },

    getByNoka () {
        const param = { 
          url: 'noka',
          idx: this.temp_form_insurance.number_insurance 
        }

        try {
          this.$store.dispatch('patient/bpjs', param).then(response => {
              const peserta = response.response.peserta
              alert(peserta.statusPeserta.keterangan);
  
              if (peserta.hakKelas.kode == '1') {
                  this.temp_form_insurance.service_class_id = { value: '8d3b8d25-4606-4db5-9ec1-59ec22514210', title: 'Kelas I' } 
  
              } else if (peserta.hakKelas.kode == '2') {
                  this.temp_form_insurance.service_class_id = { value: 'f39fbb8f-082e-4c71-8802-92943b6e4683', title: 'Kelas II' } 
  
              } else if (peserta.hakKelas.kode == '3') {
                  this.temp_form_insurance.service_class_id = { value: 'd168732a-fdef-456d-836c-750e003187e0', title: 'Kelas III' }
              }
          })
          
        } catch (error) {
            console.log('Error while GET data from patient/bpjs at: ', error)
        }
    },

    keyDpjpInsurance (param_index) {
      console.log(param_index);
      const newInsurance = this.form_dpjp.insurances.map((item, index) =>
      index === param_index
        ? { ...item, is_primary: 1 }
        : { ...item, is_primary: 0 }
      );
      this.form_dpjp.insurances = newInsurance
    },

    addDpjpInsurance () {
        let input_insurance_id = this.temp_form_insurance.insurance_id.value
        let input_insurance_name = this.temp_form_insurance.insurance_id.title
        let input_company_id = this.temp_form_insurance.company_id.value
        let input_company_name = this.temp_form_insurance.company_id.title
        let input_company_id_cob = this.temp_form_insurance.company_id_cob.value
        let input_company_name_cob = this.temp_form_insurance.company_id_cob.title
        let input_service_class_id = this.temp_form_insurance.service_class_id.value
        let input_service_class_name = this.temp_form_insurance.service_class_id.title
        let input_number_insurance = this.temp_form_insurance.number_insurance

        try {
            if (input_insurance_id === 1) {
                // cek udah ada insurance_id === 1
                // const existingInsurance = this.form_dpjp.insurances.find(item => item.insurance_id === 1);
                const existingInsurance = this.form_dpjp.insurances.find(item => item.insurance_id === 1 && item.is_delete === 0);
                const existingIsPrimary = this.form_dpjp.insurances.find(item => item.is_primary === 1);
            
                let temp_is_primary = null
                temp_is_primary = existingIsPrimary ? 0 : 1 
        
                if (!existingInsurance) {
                    this.form_dpjp.insurances.push({
                      is_primary: temp_is_primary, 
                      insurance_id: input_insurance_id, 
                      company_id: '',
                      company_id_cob: '',
                      number_insurance: '-',
                      expired: '',
                      service_class_id: input_service_class_id,
                      
                      insurance_name: input_insurance_name, 
                      company_name: '-', 
                      company_name_cob: '-', 
                      service_class_name: input_service_class_name,

                      is_delete: 0,
                      is_exist: 0,
                    })
                    // console.log("insurance_id.value == 1 not found in insuranceToInput");
                } else {
                    this.notification('Warning', 'CloseIcon', 'warning', 'Tidak dapat menambahkan pembayaran Tunai/Pribadi lebih dari 1');
                    // console.log('ga bisa nambahin, krn udah ada pembyaran tunai/pribadi');
                    // console.log("insurance_id.value == 1 found in insuranceToInput");
                }
              
            } else if (input_insurance_id === 2) {
                const existingInsurance = this.form_dpjp.insurances.find(item => item.insurance_id === 2 && item.is_delete === 0);
                const existingIsPrimary = this.form_dpjp.insurances.find(item => item.is_primary === 1);
            
                let temp_is_primary = null
                temp_is_primary = existingIsPrimary ? 0 : 1 
                
                if (!existingInsurance) { 
                    if (input_insurance_id == '' || input_company_id == '' || input_number_insurance == '' || input_service_class_id == '') {
                        this.notification('Failed','CloseIcon','danger','Lengkapi form penjamin terlebih dahulu')
                    } else {
                        this.form_dpjp.insurances.push({
                            is_primary: temp_is_primary, 
                            insurance_id: input_insurance_id,
                            company_id: input_company_id,
                            company_id_cob: input_company_id_cob,
                            number_insurance: input_number_insurance,
                            service_class_id: input_service_class_id,

                            insurance_name: input_insurance_name,
                            company_name: input_company_name,
                            company_name_cob: input_company_name_cob,
                            service_class_name: input_service_class_name,

                            is_delete: 0,
                            is_exist: 0,
                        })
                    }
                } else {
                    this.notification('Warning', 'CloseIcon', 'warning', 'Tidak dapat menambahkan pembayaran Asuransi lebih dari 1');
                }
              
            } else if (input_insurance_id === 3) {
                const existingInsurance = this.form_dpjp.insurances.find(item => item.insurance_id === 3 && item.is_delete === 0);
                const existingIsPrimary = this.form_dpjp.insurances.find(item => item.is_primary === 1);
            
                let temp_is_primary = null
                temp_is_primary = existingIsPrimary ? 0 : 1 
                
                if (!existingInsurance) { 
                    if (input_insurance_id == '' || input_company_id == '' || input_number_insurance == '' || input_service_class_id == '') {
                        this.notification('Failed','CloseIcon','danger','Lengkapi form penjamin terlebih dahulu')
                    } else {
                        this.form_dpjp.insurances.push({
                            is_primary: temp_is_primary, 
                            insurance_id: input_insurance_id,
                            company_id: input_company_id,
                            company_id_cob: input_company_id_cob,
                            number_insurance: input_number_insurance,
                            service_class_id: input_service_class_id,

                            insurance_name: input_insurance_name,
                            company_name: input_company_name,
                            company_name_cob: input_company_name_cob,
                            service_class_name: input_service_class_name,

                            is_delete: 0,
                            is_exist: 0,
                        })
                    }
                } else {
                    this.notification('Warning', 'CloseIcon', 'warning', 'Tidak dapat menambahkan pembayaran Perusahaan lebih dari 1');
                }
            } 
            else {
              this.notification('Failed','CloseIcon','danger','Lengkapi form penjamin terlebih dahulu')
            }
          
        } catch (error) {
          this.notification('Failed','CloseIcon','danger','Lengkapi form penjamin terlebih dahulu')
        }

        this.temp_form_insurance.insurance_id = ''
        this.temp_form_insurance.company_id = ''
        this.temp_form_insurance.company_id_cob = ''
        this.temp_form_insurance.number_insurance = ''
        this.temp_form_insurance.expired = ''
        this.temp_form_insurance.service_class_id = ''
    },

    deleteDpjpInsurance(param_index, param_exist, param_primary) {
      // DELETE ROW
      if (param_exist === 1) {
          this.form_dpjp.insurances[param_index].is_delete = 1
          this.form_dpjp.insurances[param_index].is_primary = 0
          this.changePrimaryWhileDeleted(param_primary)

        } else {
          this.form_dpjp.insurances.splice(param_index, 1)
          this.changePrimaryWhileDeleted(param_primary)
      }
    },

    changePrimaryWhileDeleted(param_primary) {
      let first_index_not_deleted = ''
      first_index_not_deleted = this.form_dpjp.insurances.findIndex((element) => element.is_delete == 0)
      
      let some_not_deleted = ''
      some_not_deleted = this.form_dpjp.insurances.some((element) => element.is_delete == 0)
      
      if (param_primary && (first_index_not_deleted || first_index_not_deleted === 0) && some_not_deleted) {
          this.form_dpjp.insurances[first_index_not_deleted].is_primary = 1
      }
      console.log('masuk sini: ', this.form_dpjp.insurances);
    },

    onSaveEditDpjp () {   
      const outpatient_id = "3cb626f5-9148-4fb1-8663-3d7f028b01a6"

      let temp_obj = {}
      let insurances = this.form_dpjp.insurances
      let temp_doctor2 = this.regData.health_facility_id != outpatient_id ? this.form_dpjp.doctor_in_charge_id_2 : ''
      let temp_doctor3 = this.regData.health_facility_id != outpatient_id ? this.form_dpjp.doctor_in_charge_id_3 : ''
      let temp_doctor4 = this.regData.health_facility_id != outpatient_id ? this.form_dpjp.doctor_in_charge_id_4 : ''
      let temp_doctor5 = this.regData.health_facility_id != outpatient_id ? this.form_dpjp.doctor_in_charge_id_5 : ''

      let params = {
        registration_destination_id: this.reg_des_id,
        health_facility_item_id: this.form_dpjp.health_facility_item_id,
        doctor_in_charge_id: this.form_dpjp.doctor_in_charge_id,
        doctor_in_charge_id_2: temp_doctor2,
        doctor_in_charge_id_3: temp_doctor3,
        doctor_in_charge_id_4: temp_doctor4,
        doctor_in_charge_id_5: temp_doctor5,
        insurances: [],
      }

      if (insurances.length > 0) {
        for (let i = 0; i < insurances.length; i++) {
          const el = insurances[i];

          if (el.insurance_id == 1) {
            temp_obj = {
              insurance_id: el.insurance_id,
              number_insurance: el.number_insurance,
              is_primary: el.is_primary,
              is_delete: el.is_delete
            }
          } else {
            temp_obj = {
              insurance_id: el.insurance_id,
              number_insurance:  el.number_insurance,
              is_primary: el.is_primary,
              company_id: el.company_id,
              company_id_cob: el.company_id_cob,
              service_class_id: el.service_class_id,
              is_delete: el.is_delete
            }
          }
          params.insurances.push(temp_obj)
        }
      }

      // console.log(params)
      this.disabled.dpjp = true
      this.$store.dispatch('registration/putDoctorDestination', params).then(res => {
          this.notification('Success','CheckIcon','success','You have successfully')
          this.active.modal_edit = false
          this.disabled.dpjp = false
          this.getData(this.reg_des_id)
      }, 
      err => {
          this.disabled.dpjp = false
          this.notification('Failed','CloseIcon','danger','You have unsuccessful')
      })
    },
    onCancelReferInternal(){
      this.clearReferInternal()
      this.active.modal_refer_internal = false
    },
    onSaveReferInternal () {
      const formData = this.form_refer_internal 
      this.isDisabled = true
      this.$store.dispatch('registration/formReferInternal', formData)
      .then(res => {
        this.notification('Success','CheckIcon','success','You have successfully')
        this.getDataReferInternal(this.regData.norm)
        this.clearReferInternal()
        this.isDisabled = false
      }, 
      err => {
          this.isDisabled = false
          this.notification('Failed','CloseIcon','danger','You have unsuccessful')
      })
    },
    clearReferInternal(){
      this.form_refer_internal.id = ''
      this.form_refer_internal.health_facility_item_id = ''
      this.form_refer_internal.doctor_in_charge_id = ''
      this.form_refer_internal.consultation_date = ''
    },
    onTransferModal() {
      this.active.modal_transfer = !this.active.modal_transfer
      this.isDisabled_transfer = false
      // this.resetTransferForm()
      if (this.$route.params.id) {
        this.getInpatient(this.$route.params.id)
        this.getListVital(this.$route.params.id)
      }
    },
    
    localDate(input_date) {
        return moment(input_date).locale("id").format('DD MMMM YYYY')
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
    onModalSEP(data){
      this.active.modal_sep = !this.active.modal_sep
      if (this.active.modal_sep == true) {
        this.resetSepFile()
        this.form_sep.registration_destination_id = data.registration_destination_id
        this.form_sep.sep = data.sep
        this.form_sep.jns_sep = data.jns_sep
        this.form_sep.noKunjungan = data.file_spri ? data.noKunjungan : ''
      } else {
        this.form_sep.registration_destination_id = ''
        this.form_sep.sep = ''
        this.form_sep.jns_sep = 1
        this.form_sep.noKunjungan = ''
      }
      // console.log('reg id', reg_id);
    },
    onModalKronologis(reg_id){
      this.active.modal_kronologis = !this.active.modal_kronologis
      if (this.active.modal_kronologis == true) {
        this.resetKronologisFile()
        this.form_kronologis.registration_destination_id = reg_id
      } else {
        this.form_kronologis.registration_destination_id = ''
      }
      // console.log('reg id', reg_id);
    },
    downloadSep(beurl, file){
      let url = beurl + file
      window.open(url, '_blank')
      // console.log('url', url);
    },
    validationSepFile(){
      // console.log('form sep', this.form_sep);
      this.isDisabledSep = true
      try {
        this.$store.dispatch('registration/PostSEP', this.form_sep).then((respons) => {
          // console.log('ressss: ', respons);
          this.notification('Success', 'CheckIcon', 'success', 'You have successfully')
          this.getDataInsurance(this.patient_norm)
          this.isDisabledSep = false
          this.onModalSEP(null)
          this.resetSepFile()
        })
      } catch (error) {
        console.error('Error at: ', error);
        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
        this.isDisabledSep = false
      }
    },
    resetSepFile (){
      this.form_sep.file_insurance = null
      this.form_sep.file_spri = null
      this.form_sep.sep = ''
      this.form_sep.jns_sep = 1
      this.form_sep.noKunjungan = ''
      this.form_sep.registration_destination_id = ''
    },
    validationKronologisFile(){
      // console.log('form sep', this.form_sep);
      this.isDisabledKronologis = true
      try {
        this.$store.dispatch('registration/PostKronologis', this.form_kronologis).then((respons) => {
          // console.log('ressss: ', respons);
          this.notification('Success', 'CheckIcon', 'success', 'You have successfully')
          this.getDataInsurance(this.patient_norm)
          this.isDisabledKronologis = false
          this.onModalKronologis(null)
          this.resetKronologisFile()
        })
      } catch (error) {
        console.error('Error at: ', error);
        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
        this.isDisabledKronologis = false
      }
    },
    resetKronologisFile (){
      this.form_kronologis.file_kronologis = null
      this.form_kronologis.registration_destination_id = ''
    },
    getLabPackageListV2(){
        this.$store.dispatch('laboratory/getLaboratoryPackageV2').then((response) => {
            this.supportPackage.lab_package_list = response
        })
    },
    getRadPackageListV2(){
        this.$store.dispatch('radiology/getRadiologyPackageV2').then((response) => {
            this.supportPackage.rad_package_list = response
        })
    },
    // inputImageRenderer(e) {
    //   const file = e.target.files[0]
    //   this.form_sep.file_insurance = file
    // }
  },
  computed: {
    ...mapState({
      OptionsResponsible: state => state.helper.options.responsible,
      OptionsInpatient: state => state.healthfacility.options.inpatientoption,
      OptionsHealthFacility: state => state.healthfacility.options.healthfacility,
      OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,
      OptionsDoctor: state => state.employee.options.employee,
      TypeRoomOptions: state => state.helper.options.type_room,
      OptionsRoom: state => state.room.options.room,
      OptionsBed: state => state.room.options.bed,
      OptionsInsurance: state => state.helper.options.insurance,
      Options3rdCompany: state => state.helper.options.third_party_company,
      OptionsServiceClass: state => state.helper.options.service_class,

      OptionsHowToEnter: state => state.howtoenter.options.howtoenter,
      OptionsTypeHowToEnter: state => state.helper.options.howtoenter,
    }),

    filteredListLab() {
        return this.supportPackage.lab_package_list.map((lab) => {
            const filteredCategoryItems = lab.category_items.filter((item) =>
                item.costing_name.toLowerCase().includes(this.searchlab.toLowerCase())
            );

            return {
                ...lab,
                category_items: filteredCategoryItems,
            };
        }).filter(lab => lab.category_items.length > 0); 
    },
    filteredListRad() {
        return this.supportPackage.rad_package_list.map((rad) => {
            const filteredCategoryItems = rad.category_items.filter((item) =>
                item.costing_name.toLowerCase().includes(this.searchrad.toLowerCase())
            );

            return {
                ...rad,
                category_items: filteredCategoryItems,
            };
        }).filter(rad => rad.category_items.length > 0); 
    },
  },
  created() {
    if (this.$route.params.id) {
      this.reg_des_id = this.$route.params.id
      this.getData(this.$route.params.id)
    }

    this.$store.dispatch('employee/getParamSpecialist')
    this.$store.dispatch('healthfacility/getParam', 'healthfacility')
    this.$store.dispatch('healthfacility/getInpatientOption', 'inpatientOption')
    this.$store.dispatch('helper/getParam', 'type_room')
    this.$store.dispatch('helper/getParam', 'insurance')
    this.$store.dispatch('helper/getParamThirdPartyCompany', 'third_party_company')
    this.$store.dispatch('helper/getParamServiceClass', 'service_class')
    this.$store.dispatch('healthfacilityitem/getParam', '3cb626f5-9148-4fb1-8663-3d7f028b01a6')

    this.getLabPackageListV2()
    this.getRadPackageListV2()

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
