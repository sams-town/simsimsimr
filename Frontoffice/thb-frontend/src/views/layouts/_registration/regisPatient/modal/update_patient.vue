<template>
    <div>
        <!-- <b-button @click="onAddPatient()" size="sm" variant="pink-500" class="text-white">
            <b-icon icon="plus" scale="0.8"></b-icon>Pasien Baru
        </b-button> -->
        <b-modal v-model="modalShow" size="xl" @close="onAddPatient()" title="Data Pasien" hide-footer no-close-on-backdrop>
            <b-form>
                <!-- <patientSection /> -->
                <div class="col px-0">
                    <!-- Heading-Devider -->
                    <div class="card border-0">
                        <!-- payment section -->
                        <div class="card border-0">
                            <div class="card-header bg-indigo rounded">
                                <span class="fw-bold text-white">Metode Pembayaran/Jenis Pasien</span>
                            </div>
                            <div class="card-body">
                                <div>
                                    <b-table small responsive :items="form.insuranceTable.items" :fields="form.insuranceTable.fields" head-variant="light" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="text-center">
                                        <template #cell(is_primary)=row>
                                            <template v-if="row.item.is_primary === 0">
                                                <b-button variant="warning-600" size="sm" @click="keyInsurance(row.item.number_insurance)">
                                                    <feather-icon icon="KeyIcon" />
                                                </b-button>
                                            </template>
                                            <template v-if="row.item.is_primary === 1">
                                                <b-button variant="info-600" size="sm">Utama</b-button>
                                            </template>
                                        </template>
                                        <template #cell(Aksi)=data>
                                            <b-button variant="danger-600" size="sm" @click="deleteInsurance(data.index)">
                                                <feather-icon icon="TrashIcon" />
                                            </b-button>
                                        </template>
                                    </b-table>
                                </div>
                                <div class="d-flex justify-content-end mt-32">
                                    <b-button pill size="sm" variant="indigo-200" @click="active.insurence_form = true" class="px-3">
                                        <feather-icon icon="PlusIcon" class="mr-2" />
                                        <span class="text-indigo-700 fw-medium">Penjamin</span>
                                    </b-button>
                                </div>
                                <div v-show="active.insurence_form" class="mt-2 mt-lg-0">
                                    <div>
                                        <div class="row mx-0">
                                            <div class="col-12 col-lg-4 px-0 px-lg-3 mt-1">
                                                <label class="c-form-label">Jenis Penjamin</label>
                                                <v-select v-model="form.insuranceInput.insurance_id" :options="OptionsInsurance" :reduce="OptionsInsurance => OptionsInsurance" label="title" placeholder="-- Jenis Penjamin --"></v-select>
                                            </div>
                                            <div class="col" v-if="form.insuranceInput.insurance_id && form.insuranceInput.insurance_id.title === 'Asuransi'">
                                                <div class="row">
                                                    <div class="col-12 col-lg-6 px-0 px-lg-3 mt-1">
                                                        <label class="c-form-label">Nama Penjamin </label>
                                                        <v-select v-model="form.insuranceInput.company_id" :options="Options3rdCompany" :reduce="Options3rdCompany => Options3rdCompany" label="title" placeholder="-- Nama Penjamin --"></v-select>
                                                    </div>
                                                    <div class="col-12 col-lg-6 px-0 px-lg-3 mt-1">
                                                        <label class="c-form-label">Nomor Anggota </label>
                                                        <b-input-group>
                                                            <b-form-input v-model="form.insuranceInput.number_insurance"  @keyup.enter="getByNoka()"></b-form-input>
                                                            <b-input-group-append>
                                                                <b-button variant="primary-500" @click="getByNoka()"><feather-icon icon="SearchIcon" /></b-button>
                                                            </b-input-group-append>
                                                        </b-input-group>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col" v-if="form.insuranceInput.insurance_id && form.insuranceInput.insurance_id.title === 'Perusahaan'">
                                                <div class="row">
                                                    <div class="col-12 col-lg-6 px-0 px-lg-3 mt-1">
                                                        <label class="c-form-label">Nama Penjamin </label>
                                                        <v-select v-model="form.insuranceInput.company_id" :options="Options3rdCompany" :reduce="Options3rdCompany => Options3rdCompany" label="title" placeholder="-- Nama Penjamin --"></v-select>
                                                    </div>
                                                    <div class="col-12 col-lg-6 px-0 px-lg-3 mt-1">
                                                        <label class="c-form-label">NIP </label>
                                                        <b-form-input v-model="form.insuranceInput.number_insurance"></b-form-input>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-if="form.insuranceInput.insurance_id && form.insuranceInput.insurance_id.title === 'Asuransi'" class="row mx-0 mt-lg-10">
                                            <div class="col-12 col-lg-4 px-0 px-lg-3 mt-1">
                                                <label class="c-form-label">Hak Kelas </label>
                                                <v-select v-model="form.insuranceInput.service_class_id" :options="OptionsServiceClass" :reduce="OptionsServiceClass => OptionsServiceClass" label="title" placeholder="-- Hak Kelas --"></v-select>
                                            </div>
                                            <div class="col-12 col-lg-3 px-0 px-lg-3 mt-1" style="display:none;">
                                                <label class="c-form-label">Tanggal Akhir </label>
                                                <b-form-input v-model="form.insuranceInput.expired" type="date"></b-form-input>
                                            </div>
                                        </div>
                                        <div v-if="form.insuranceInput.insurance_id && form.insuranceInput.insurance_id.title === 'Perusahaan'" class="row mx-0 mt-lg-10">
                                            <div class="col-12 col-lg-4 px-0 px-lg-3 mt-1">
                                                <label class="c-form-label">Hak Kelas </label>
                                                <v-select v-model="form.insuranceInput.service_class_id" :options="OptionsServiceClass" :reduce="OptionsServiceClass => OptionsServiceClass" label="title" placeholder="-- Hak Kelas --"></v-select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col mt-16 mt-lg-24 d-flex">
                                        <div class="ml-auto row mx-0">
                                            <b-button @click="active.insurence_form = false" pill variant="outline-indigo" class="px-3 mr-2">Batal</b-button>
                                            <b-button @click.prevent="validationFormInsurance" pill variant="indigo" class="px-3">Update</b-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-header rounded bg-indigo">
                            <span class="fw-bold text-white">Data Pasien</span>
                        </div>

                        <!-- RADIO TAB-->
                        <div class="card-body">
                            <div class="col px-0">
                                <div class="row">
                                    <div class="col-6">
                                        <b-button ref="patient_new" :variant="form.patient_type === 'new' ? 'indigo-300' : 'indigo-100'" @click="selectFor('new')" class="btn-block">Pasien Baru</b-button>
                                    </div>
                                    <div class="col-6">
                                        <b-button ref="patient_baby" :variant="form.patient_type === 'baby' ? 'indigo-300' : 'indigo-100'" @click="selectFor('baby')" class="btn-block">Pasien Bayi</b-button>
                                    </div>
                                </div>
                            </div>
                            <!-- FORM PASIEN BARU-->
                            <div>
                                <!-- BLOK 1 -->
                                <div class="col px-0 mt-3 c-form">
                                    <div v-show="form.patient_type === 'baby'" class="col-lg-3">
                                        <label class="c-form-label">No RM</label>
                                        <b-form-input v-model="form.norm_mother" autocomplete="off" />
                                    </div>
                                    <div class="row mx-0 mt-16">
                                        <div class="col-lg-3 d-flex align-items-stretch px-0 px-lg-3">
                                            <div
                                                class="p-2 user-inform d-flex justify-content-start align-items-center w-100">
                                                <div class="row mx-0">
                                                    <b-img 
                                                    ref="previewPatientEl"
                                                    rounded
                                                    :src="form.imagePatientUrl" style="aspect-ratio: 1/1; width: 60px; object-fit: cover;" alt="img user" class="img-user"/>
                                                    <div class="px-3 d-flex flex-column gap-1h">
                                                        <p class="text-semi-12 text-indigo">No RM:</p>
                                                        <p class="text-semi-12 text-indigo">Terdaftar Sejak:</p>
                                                        <div class="d-inline-block pointer p-1">
                                                            <b-form-file
                                                                ref="refInputPatientEl"
                                                                v-model="form.patientPhoto"
                                                                accept=".jpg, .png"
                                                                :hidden="true"
                                                                plain
                                                                @change="inputImageRenderer"
                                                            />
                                                            <label class="text-semi-12 text-indigo pointer" @click="$refs.refInputPatientEl.$el.click()"><i><u>Add Photo</u></i></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col px-0 mt-lg-12 mt-lg-0">
                                            <div class="row mx-0">
                                                <div class="col-lg px-0 px-lg-3 mt-1">
                                                    <label>Nama Pasien *</label>
                                                    <div class="d-flex justify-content-center align-items-center">
                                                        <v-select @change="saveChanged($event)" v-model="form.alias" aria-readonly="true" :options="OptionsAlias" :reduce="OptionsAlias => OptionsAlias.value" label="title" placeholder="--Pilih --"></v-select>
                                                        <b-form-input required autocomplete="off" @change="saveChanged($event)" v-model="form.name" @input="form.name = $event.toUpperCase()" ref="name" :disabled="active.isDisabled === 'view'" class="col"></b-form-input>
                                                    </div>
                                                </div>
                                                <div class="col-lg-7 px-0 px-lg-3 mt-1">
                                                    <div class="row">
                                                        <div class="col-lg mt-1 mt-lg-0">
                                                            <label>Kartu Identitas *</label>
                                                            <v-select @change="saveChanged($event)" v-model="form.typeidcard" aria-readonly="true" :options="OptionsTypeIdcard" :reduce="OptionsTypeIdcard => OptionsTypeIdcard.value" label="title" placeholder="Kartu Identitas"></v-select>
                                                        </div>
                                                        <div class="col-lg-7 mt-1 mt-lg-0">
                                                            <label>Nomor Identitas *</label>
                                                            <b-form-input required autocomplete="off" @change="saveChanged($event)" v-model="form.idcard" ref="idcard_type" :disabled="active.isDisabled === 'view'" type="number"></b-form-input>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mx-0 mt-0 mt-lg-10">
                                                <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                                    <label>Tempat, Tanggal Lahir *</label>
                                                    <div class="row mx-0">
                                                        <b-form-input required autocomplete="off" @change="saveChanged($event)" v-model="form.birth_place" type="text"
                                                            class="col-12 col-lg mr-lg-2"
                                                            placeholder="Tempat"></b-form-input>
                                                        <b-form-input autocomplete="off" required @change="saveChanged($event)" v-model="form.birth_date" ref="birthDate" type="date" :disabled="active.isDisabled === 'view' || active.isDisabled === 'edit'"
                                                            class="col-12 col-lg mt-8 mt-lg-0"></b-form-input>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                                    <label>Jenis Kelamin *</label>
                                                    <v-select @change="saveChanged($event)" v-model="form.sex" :options="OptionsSex" :reduce="OptionsSex => OptionsSex.value" required label="title" placeholder="--Pilih Kelamin --"></v-select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- BLOK 2 -->
                                <div class="col px-0 c-form">
                                    <div class="row mx-0 mt-0 mt-lg-10">
                                        <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                            <label>Perkawinan</label>
                                            <v-select @change="saveChanged($event)" v-model="form.married_id" :options="OptionsMarried" :reduce="OptionsMarried => OptionsMarried.value" label="title" placeholder="--Pilih Perkawinan --"></v-select>
                                        </div>
                                        <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                            <label>Agama </label>
                                            <v-select @change="saveChanged($event)" v-model="form.religion_id" :options="OptionsReligion" :reduce="OptionsReligion => OptionsReligion.value" label="title" placeholder="--Pilih Agama --"></v-select>
                                        </div>
                                        <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                            <label>Kewarganegaraan </label>
                                            <v-select @change="saveChanged($event)" v-model="form.citizenship_id" :options="OptionsCitizenship" :reduce="OptionsCitizenship => OptionsCitizenship.value" label="title" placeholder="--Pilih Kewarganegaraan --"></v-select>
                                        </div>
                                    </div>
                                    <div class="row mx-0 mt-0 mt-lg-10">
                                        <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                            <label>No Telp/No HP </label>
                                            <b-form-input autocomplete="off" type="number" @change="saveChanged($event)" v-model="form.phone"
                                                :disabled="form.patient_type != 'new'"></b-form-input>
                                        </div>
                                        <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                            <label for="idNum">Email</label>
                                            <b-form-input autocomplete="off" id="idNum" type="email" v-model="form.email" placeholder="email"></b-form-input>
                                        </div>
                                        <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                            <label for="blood">Golongan Darah</label>
                                            <v-select v-model="form.blood" :options="OptionsBlood" :reduce="OptionsBlood => OptionsBlood.value" label="title" placeholder="--Pilih Golongan Darah --"></v-select>
                                        </div>
                                    </div>
                                </div>
                                <!-- BLOK 3 -->
                                <div class="col px-0 c-form">
                                    <div class="row mx-0 mt-0 mt-lg-10">
                                        <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                            <label for="edu">Pendidikan</label>
                                            <v-select v-model="form.last_edu_id" :options="OptionsLastEdu" :reduce="OptionsLastEdu => OptionsLastEdu.value" label="title" placeholder="--Pilih Pendidikan --"></v-select>
                                        </div>
                                        <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                            <label for="work">Pekerjaan</label>
                                            <v-select v-model="form.profession_id" :options="OptionsProfession" :reduce="OptionsProfession => OptionsProfession.value" label="title" placeholder="--Pilih Pekerjaan --"></v-select>
                                        </div>
                                    </div>
                                </div>
                                <!-- BLOK 4 -->
                                <div class="col px-0 c-form">
                                    <div class="row mx-0 mt-0 mt-lg-10">
                                        <div class="col-12 col-lg-12 px-0 px-lg-3 mt-1">
                                            <label>Alamat </label>
                                            <b-textarea rows="3" autocomplete="off" v-model="form.address" placeholder="Nama jalan" :disabled="form.patient_type != 'new'" ></b-textarea>
                                        </div>
                                    </div>
                                    <div class="row mx-0 mt-0 mt-lg-10">
                                        <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                            <label>Provinsi</label>
                                            <v-select v-model="address_form.province" :options="OptionsProvince" :reduce="OptionsProvince => OptionsProvince.value" label="title" placeholder="-- Provinsi --" :disabled="form.patient_type != 'new'"></v-select>
                                        </div>
                                        <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                            <label>Kota/Kabupaten </label>
                                            <v-select v-model="address_form.regency" :options="OptionsRegency" :reduce="OptionsRegency => OptionsRegency.value" label="title" placeholder="-- Kota/Kabupaten --" :disabled="form.patient_type != 'new'"></v-select>
                                        </div>
                                    </div>
                                    <div class="row mx-0 mt-0 mt-lg-10">
                                        <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                            <label>Kecamatan </label>
                                            <v-select v-model="address_form.district" :options="OptionsDistrict" :reduce="OptionsDistrict => OptionsDistrict.value" label="title" placeholder="-- Kecamatan --" :disabled="form.patient_type != 'new'"></v-select>
                                        </div>
                                        <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                            <label>Kelurahan </label>
                                            <v-select v-model="form.address_id" :options="OptionsVillage" :reduce="OptionsVillage => OptionsVillage.value" label="title" placeholder="-- Kelurahan --" :disabled="form.patient_type != 'new'"></v-select>
                                        </div>
                                        <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                            <label>Kode Pos</label>
                                            <b-form-input type="number" v-model="form.zip_code" :disabled="form.patient_type != 'new'"></b-form-input>
                                        </div>
                                    </div>
                                    <div class="row mx-0 mt-0 mt-lg-10">
                                        <div class="col-6 col-lg-6 px-0 px-lg-3 mt-1">
                                            <label>Flag Pasien </label>
                                            <v-select v-model="form.patient_issue_id" :options="OptionsPatientIssue" :reduce="OptionsPatientIssue => OptionsPatientIssue.value" label="title" placeholder="-- Pasien --"></v-select>
                                        </div>
                                        <div class="col-6 col-lg-6 px-0 px-lg-3 mt-1">
                                            <label>Keterangan Flag Pasien</label>
                                            <b-form-input v-model="form.patient_issue_text"></b-form-input>
                                        </div>
                                    </div>
                                </div>
                                <!-- UPLOAD FILES -->
                                <div class="col px-0 mt-2" style="display: none;">
                                    <div class="col-lg-6">
                                        <label class="mb-1 c-form-label">Dokumen Pasien</label>
                                        <div v-if="fileSim" class="upload-box d-flex flex-row justify-content-between">
                                            <input type="file" ref="file" id="upload_sim" class="d-none">
                                            <div class="row mx-0 grouping-file d-flex align-items-center gap-5">
                                                <label for="upload_sim" class="d-inline-block pointer">Upload File</label>
                                                <p>Tipe Dokumen : SIM</p>
                                            </div>
                                            <div @click="removeFileList" class="d-flex align-items-center pointer">
                                                <img src="../../../../../../src/@core/assets/icons/close.svg" width="18px"
                                                    alt="remove" class="remove">
                                            </div>
                                        </div>
                                        <div class="available-files">
                                            <div @click="expandSelectedFiles"
                                                class="row mx-0 dropdown-files pointer d-flex justify-content-between align-items-center">
                                                <p>Pilih Tipe Dokumen</p>
                                                <img src="../../../../../../src/@core/assets/icons/drop.svg" alt="">
                                            </div>
                                            <div class="" v-if="expandFiles">
                                                <div class="file-list" v-if="fileSim == false">
                                                    <div
                                                        class="row mx-0 bg-light p-2 d-flex justify-content-between align-items-center">
                                                        <label class="">Surat Izin Mengemudi</label>
                                                        <b-button @click="selectFileList" class="bg-purple">Pilih</b-button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- allergy section -->
                <div class="card border-0">
                    <div class="card-header rounded bg-indigo">
                        <span class="fw-bold text-white">Alergi</span>
                    </div>
                    <div class="card-body">
                        <div>
                            <b-table small responsive :items="form.allergyTable.items" :fields="form.allergyTable.fields" head-variant="light" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="text-center">
                                <template #cell(Aksi)>
                                    <b-button variant="danger-600" size="sm" @click="deleteAllergy(form.allergyTable.items.index+1)" class="mr-2">
                                        <feather-icon icon="TrashIcon" />
                                    </b-button>
                                </template>
                            </b-table>
                        </div>
                        <div class="col px-0 d-flex mt-32">
                            <b-button pill size="sm" variant="indigo-200" @click="alergiForm = true" class="ml-auto px-3">
                                <feather-icon icon="PlusIcon" class="mr-2" />
                                <span class="text-indigo-700 fw-medium">Tambah Alergi</span>
                            </b-button>
                        </div>
                        <div v-show="alergiForm" class="col px-0 mt-32">
                            <form class="c-form">
                                <div class="row mx-0">
                                    <div class="col-12 col-lg px-0 px-lg-3">
                                        <label>Jenis Alergi</label>
                                        <v-select :options="OptionsTypeAllergy" v-model="form.allergyInput.type_allergy_id" :reduce="OptionsTypeAllergy => OptionsTypeAllergy" label="title" placeholder="--Pilih Jenis Alergi --"></v-select>
                                    </div>
                                    <div class="col-12 col-lg px-0 px-lg-3">
                                        <label>Alergi *</label>
                                        <b-form-input autocomplete="off" type="text" v-model="form.allergyInput.name" required></b-form-input>
                                    </div>
                                    <div class="col-12 col-lg px-0 px-lg-3">
                                        <label>Reaksi</label>
                                        <b-form-input autocomplete="off" type="text" v-model="form.allergyInput.reaction"></b-form-input>
                                    </div>
                                </div>
                                <div class="row mx-0 mt-0 mt-lg-12">
                                    <div class="col-12 col-lg px-0 px-lg-3">
                                        <label>Tingkat Keparahan</label>
                                        <b-form-input autocomplete="off" type="text" v-model="form.allergyInput.severity"></b-form-input>
                                    </div>
                                    <div class="col-12 col-lg px-0 px-lg-3">
                                        <label>Efek Samping</label>
                                        <b-form-input autocomplete="off" type="text" v-model="form.allergyInput.side_effect"></b-form-input>
                                    </div>
                                    <div class="col-12 col-lg px-0 px-lg-3">
                                        <label>Tanggal Kejadian</label>
                                        <b-form-input autocomplete="off" type="date" v-model="form.allergyInput.date_tragedy"></b-form-input>
                                    </div>
                                </div>
                                <div class="col mt-16 mt-lg-24 d-flex">
                                    <div class="ml-auto row mx-0">
                                        <b-button pill variant="outline-indigo" class="px-3 mr-2"
                                            @click="alergiForm = !alergiForm">Batal</b-button>
                                        <b-button pill variant="indigo" @click.prevent="validationFormAllergy" class="px-3">Simpan</b-button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- responsible section -->
                <div class="card border-0 mt-5">
                    <div class="card-header bg-indigo rounded">
                        <span class="fw-bold text-white">Data Penanggung Jawab</span>
                    </div>
                    <div class="card-body">
                        <b-form class="c-form">
                            <div class="row mx-0">
                                <div class="col-12 col-lg-4 px-0 px-lg-3 mt-1">
                                    <label class="c-form-label">Kartu Identitas *</label>
                                    <v-select v-model="form.responsible_person_typeidcard" aria-readonly="true" :options="OptionsTypeIdcard" :reduce="OptionsTypeIdcard => OptionsTypeIdcard.value" label="title" placeholder="-- Kartu Identitas --"></v-select>
                                </div>
                                <div class="col-12 col-lg-4 px-0 px-lg-3 mt-1">
                                    <label class="c-form-label">No Identitas *</label>
                                    <b-form-input required autocomplete="off" v-model="form.responsible_person_idcard" type="number"></b-form-input>
                                </div>
                                <div class="col-12 col-lg-4 px-0 px-lg-3 mt-1">
                                    <label class="c-form-label">Hubungan Keluarga *</label>
                                    <v-select v-model="form.responsible_id" :options="OptionsResponsible" :reduce="OptionsResponsible => OptionsResponsible.value" required label="title" placeholder="--Pilih Hubungan Keluarga --"></v-select>
                                </div>
                                <div class="col-12 col-lg-4 px-0 px-lg-3 mt-1">
                                    <label class="c-form-label">Nama Penanggung Jawab *</label>
                                    <b-form-input required autocomplete="off" v-model="form.responsible_person_name" type="text"></b-form-input>
                                </div>
                            </div>
                            <div class="row mx-0 mt-lg-10">
                                <div class="col-12 col-lg-4 px-0 px-lg-3 mt-1">
                                    <label class="c-form-label">No Telp/HP *</label>
                                    <b-form-input required autocomplete="off" v-model="form.responsible_person_phone" type="number"></b-form-input>
                                </div>
                                <div class="col-12 col-lg-4 px-0 px-lg-3 mt-1">
                                    <label class="c-form-label">Alamat *</label>
                                    <b-form-input required autocomplete="off" v-model="form.responsible_person_address_id" type="text"></b-form-input>
                                </div>
                                <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                    <label for="work">Pekerjaan</label>
                                    <v-select v-model="form.responsible_person_profession_id" :options="OptionsProfession" :reduce="OptionsProfession => OptionsProfession.value" label="title" placeholder="--Pilih Pekerjaan --"></v-select>
                                </div>
                            </div>
                            <div class="col mt-3 px-0 d-flex justify-content-end align-items-center">
                                <div class="col mt-16 mt-lg-24 px-0 d-flex">
                                    <div class="ml-auto row mx-0">
                                        <b-button pill variant="outline-indigo" class="px-3 mr-2"
                                            @click="modalShow = !modalShow">Batal</b-button>
                                        <b-button pill variant="indigo" type="submit" :disabled="isDisabled" @click.prevent="validationForm" class="px-3">Simpan</b-button>
                                    </div>
                                </div>
                            </div>
                        </b-form>
                    </div>
                </div>
            </b-form>
        </b-modal>
    </div>
</template>
<script>
// import $ from 'jquery'
// import '@/views/pages/canvasing/jsCanvas/szsignaturecanvas.js'
import { BImg } from 'bootstrap-vue'
import vSelect from 'vue-select'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import { mapState } from 'vuex'
export default {
    components: { BImg, vSelect, ToastificationContent },
    props: {
        dataFromAppointment: {},
        dataFromUpdatePatientMaster: {},
    },
    data () {
        return {
            modalShow: false,
            isDisabled: false,
            active: {
                isDisabled: '',
            },
            address_form: {
                province: '',
                regency: '',
                district: '',
            },
            // patient section
            form: {
                appointment_id: '',
                norm: '',
                norm_mother: '',
                imagePatientUrl: require('../../../../../../src/@core/assets/images/pendaftaran/user.png'), setPatientImage: 'none', patientPhoto: null,
                id: null,
                patient_type: 'new',
                idcard: '',
                typeidcard: '1',
                alias: '',
                name: '',
                birth_date: '',
                birth_place: '',
                address: '',
                address_rt: '',
                address_rw: '',
                sex: '',
                blood: '',
                married_id: '',
                profession_id: '',
                citizenship_id: '',
                religion_id: '',
                last_edu_id: '',
                phone: '',
                email: '',
                address_id: '',
                zip_code: '',
                patient_issue_id: '',
                patient_issue_text: '',
                responsible_id: '',
                responsible_person_idcard: '',
                responsible_person_typeidcard: '',
                responsible_person_profession_id: '',
                responsible_person_name: '',
                responsible_person_phone: '',
                responsible_person_address_id: '',
                allergyInput: {
                    name:'',
                    reaction:'',
                    severity:'',
                    side_effect:'',
                    date_tragedy:'',
                    type_allergy_id:{value:'0', title:'-'}
                },
                allergyToInput: [],
                allergyTable: {
                    fields: [
                        { key: 'no', label: 'No' },
                        { key: 'name', label: 'Alergi' },
                        { key: 'type_allergy_name', label: 'Jenis Alergi' },
                        { key: 'reaction', label: 'Reaksi' },
                        { key: 'severity', label: 'Tingkat Keparahan' },
                        { key: 'side_effect', label: 'Efek Samping' },
                        { key: 'date_tragedy', label: 'Tgl Kejadian' },
                        { key: 'Aksi' },
                    ],
                    items: [],
                },
                insuranceInput: {},
                insuranceToInput: [
                    { 
                        is_primary: 1, 
                        insurance_id: 1,
                        company_id: '', 
                        number_insurance: '-',
                        expired: '',
                        service_class_id: '',
                        insurance_name: 'Tunai/Pribadi',
                        insurance_bride: {},
                        company_name: '',
                        service_class_name: '',
                    }
                ],
                insuranceTable: {
                    fields: [
                        { key: 'is_primary', label: '' },
                        { key: 'company_name', label: 'Nama Penjamin' },
                        { key: 'insurance_name', label: 'Jenis Penjamin' },
                        { key: 'number_insurance', label: 'No Anggota' },
                        { key: 'expired', label: 'Tanggal Kadaluarsa' },
                        { key: 'service_class_name', label: 'Kelas Layanan' },
                        { key: 'Aksi' },
                    ],
                    items: [
                        { 
                            is_primary: 1, 
                            insurance_name: 'Tunai/Pribadi', 
                            insurance_bride: {},
                            company_name: '-', 
                            number_insurance: '-',
                            expired: '-',
                            service_class_name: '-',
                        }
                    ]
                }
            },
            insurance_bride:{},
            age: 0,
            fileSim: true,
            expandFiles: false,
            active: {
                insurence_form: true,
            },
            alergiForm: false,
            no_sep: false,
        }
    },
    methods: {
        keyInsurance (index) {
            const newinsuranceTable = this.form.insuranceTable.items.map(p =>
            p.number_insurance === index
                ? { ...p, is_primary: 1 }
                : { ...p, is_primary: 0 }
            );
            this.form.insuranceTable.items = newinsuranceTable

            const newinsuranceToInput = this.form.insuranceToInput.map(p =>
            p.number_insurance === index
                ? { ...p, is_primary: 1 }
                : { ...p, is_primary: 0 }
            );
            this.form.insuranceToInput = newinsuranceToInput
        },
        getByNoka () {
            this.$store.dispatch('patient/bpjs', {url:'noka',idx:this.form.insuranceInput.number_insurance})
            .then(response => {
                alert(response.response.peserta.statusPeserta.keterangan);
                if (response.response.peserta.hakKelas.kode == '1') {
                    this.form.insuranceInput.service_class_id = {value:'8d3b8d25-4606-4db5-9ec1-59ec22514210',title:'Kelas I'} 
                } else if (response.response.peserta.hakKelas.kode == '2') {
                    this.form.insuranceInput.service_class_id = {value:'f39fbb8f-082e-4c71-8802-92943b6e4683',title:'Kelas II'} 
                } else if (response.response.peserta.hakKelas.kode == '3') {
                    this.form.insuranceInput.service_class_id = {value:'d168732a-fdef-456d-836c-750e003187e0',title:'Kelas III'}
                }
                this.form.insuranceInput.expired = response.response.peserta.tglTAT
                this.form.name = response.response.peserta.nama
                this.form.idcard = response.response.peserta.nik
                this.form.birth_date = response.response.peserta.tglLahir
                if (response.response.peserta.sex == 'P') {
                    this.form.sex = 0
                } else {
                    this.form.sex = 1
                }
                this.insurance_bride = response.response
                this.validationFormInsurance()
            })
        },
        saveChanged($event){
            localStorage.setItem("savePatient", JSON.stringify(this.form))
        },
        inputImageRenderer (e) {
			const file = e.target.files[0]
			this.form.patientPhoto = file
			this.form.setPatientImage = 'upload'
			this.form.imagePatientUrl = URL.createObjectURL(e.target.files[0])
		},
        validationForm () {
            const formData = this.form 
            this.isDisabled = true
            this.$store.dispatch('patient/formUpdate', formData)
            .then(response => {
                console.log('Response Create: ', response)
                if (response.status === 201) {
                    localStorage.setItem("savePatient", '')
                    this.modalShow = false
                    this.isDisabled = false
                    this.$store.dispatch('patient/getDetail')

                    if (this.dataFromAppointment) {
                        // get norm from response
                        const normValue = response.data.norm;
                        // console.log('Properti "norm" dari respons:', normValue);

                        const combinedData = { ...formData, ...this.dataFromAppointment, norm: normValue, appointment_id: this.dataFromAppointment.id }
                        this.$emit('getFromCombinedAddPatient', combinedData)
                        console.log('combinedData di add_patient', combinedData);

                        // update flag appointment
                        this.updateFlagAppointment(this.dataFromAppointment.id);

                            
                    } else {
                        // get norm from response
                        const normValue = response.data.norm;
                        // console.log('Properti "norm" dari respons:', normValue);

                        const combinedData = { ...formData, norm: normValue }
                        this.$emit('getFromCombinedAddPatient', combinedData)
                        // console.log(combinedData);

                    }

                    this.notification('Success','CheckIcon','success','You have successfully')
                    } else {
                    this.notification('Success','CheckIcon','success','You have successfully')
                    }
                },
            error => {
                console.log('error in: ' + error)
                this.isDisabled = false
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
            })
        },
        async updateFlagAppointment(appointmentId) {
            try {
                await this.$store.dispatch('appointment/updateFlagAppointment', appointmentId);
            } catch (error) {
                console.log('Error in: ' + error);
            }
        },
        validationFormAllergy () {
            this.form.allergyTable.items.push({
                no: this.form.allergyTable.items.length+1,
                name: this.form.allergyInput.name,
                type_allergy_id: this.form.allergyInput.type_allergy_id.value,
                type_allergy_name: this.form.allergyInput.type_allergy_id.title,
                reaction: this.form.allergyInput.reaction,
                severity: this.form.allergyInput.severity,
                side_effect: this.form.allergyInput.side_effect,
                date_tragedy: this.form.allergyInput.date_tragedy,
                // aksi: this.form.allergyInput.date_tragedy,
            })
            this.form.allergyToInput.push({
                type_allergy_id: this.form.allergyInput.type_allergy_id.value,
                name: this.form.allergyInput.name,
                date_tragedy: this.form.allergyInput.date_tragedy,
                side_effect: this.form.allergyInput.side_effect,
                reaction: this.form.allergyInput.reaction,
                severity: this.form.allergyInput.severity,
            })
            this.form.allergyInput.name = ''
            this.form.allergyInput.reaction = ''
            this.form.allergyInput.severity = ''
            this.form.allergyInput.side_effect = ''
            this.form.allergyInput.date_tragedy = ''
            this.form.allergyInput.type_allergy_id = {value:0, title:'-'}
            this.alergiForm = false
        },
        validationFormInsurance () {
            if (this.form.insuranceInput.insurance_id.value === 1) {
                this.form.insuranceTable.items.push({
                    is_primary: 1, 
                    insurance_name: this.form.insuranceInput.insurance_id.title, 
                    insurance_bride: {},
                    company_name: '-', 
                    number_insurance: '-',
                    expired: '-',
                    service_class_name: '-',
                })
                this.form.insuranceToInput.push({
                    is_primary: 1, 
                    insurance_id: this.form.insuranceInput.insurance_id.value, 
                    company_id: '',
                    number_insurance: '-',
                    expired: '',
                    service_class_id: '',
                    insurance_name: this.form.insuranceInput.insurance_id.title, 
                    insurance_bride: {},
                    company_name: '-', 
                    service_class_name: '-',
                })
            } else {
                // this.form.insuranceTable.items = []
                this.form.insuranceTable.items.push({
                    is_primary: 0, 
                    insurance_name: this.form.insuranceInput.insurance_id.title, 
                    insurance_bride: this.insurance_bride,
                    company_name: this.form.insuranceInput.company_id.title, 
                    number_insurance: this.form.insuranceInput.number_insurance,
                    expired: this.form.insuranceInput.expired,
                    service_class_name: this.form.insuranceInput.service_class_id.title,
                })
                this.form.insuranceToInput.push({
                    is_primary: 0, 
                    insurance_id: this.form.insuranceInput.insurance_id.value, 
                    company_id: this.form.insuranceInput.company_id.value,
                    number_insurance: this.form.insuranceInput.number_insurance,
                    expired: this.form.insuranceInput.expired,
                    service_class_id: this.form.insuranceInput.service_class_id.value,
                    insurance_name: this.form.insuranceInput.insurance_id.title, 
                    insurance_bride: this.insurance_bride,
                    company_name: this.form.insuranceInput.company_id.title, 
                    service_class_name: this.form.insuranceInput.service_class_id.title,
                })
            }
            this.form.insuranceInput.insurance_id = ''
            this.form.insuranceInput.company_id = ''
            this.form.insuranceInput.number_insurance = ''
            this.form.insuranceInput.expired = ''
            this.form.insuranceInput.service_class_id = ''
            this.active.insurence_form = false
        },
        onAddPatient () {
            this.modalShow = !this.modalShow
            this.alergiForm = false
            this.form.allergyTable = {
                fields: [
                    { key: 'no', label: 'No' },
                    { key: 'name', label: 'Alergi' },
                    { key: 'type_allergy_name', label: 'Jenis Alergi' },
                    { key: 'reaction', label: 'Reaksi' },
                    { key: 'severity', label: 'Tingkat Keparahan' },
                    { key: 'side_effect', label: 'Efek Samping' },
                    { key: 'date_tragedy', label: 'Tgl Kejadian' },
                    { key: 'Aksi' },
                ],
                items: [],
            }
            this.form.allergyToInput = []
            this.form.insuranceTable = {
                fields: [
                    { key: 'is_primary', label: '' },
                    { key: 'company_name', label: 'Nama Penjamin' },
                    { key: 'insurance_name', label: 'Jenis Penjamin' },
                    { key: 'number_insurance', label: 'No Anggota' },
                    { key: 'service_class_name', label: 'Kelas Layanan' },
                    { key: 'Aksi' },
                ],  
                items: [
                    { 
                        is_primary: '1', 
                        insurance_name: 'Tunai/Pribadi', 
                        insurance_bride: {},
                        company_name: '-', 
                        number_insurance: '-',
                        expired: '-',
                        service_class_name: '-'
                    }
                ]
            }
            this.form.insuranceToInput = [
                { 
                    is_primary: 1, 
                    insurance_id: 1,
                    company_id: '', 
                    number_insurance: '-',
                    expired: '',
                    service_class_id: '',
                    insurance_name: '',
                    insurance_bride: {},
                    company_name: 'Tunai/Pribadi',
                    service_class_name: '',
                }
            ]
            this.imagePatientUrl = require('../../../../../../src/@core/assets/images/pendaftaran/user.png'), this.setPatientImage = 'none', this.patientPhoto = null,
            this.form.id = null
            this.form.patient_type = 'new'
            // this.form.idcard = ''
            this.form.typeidcard = 1
            // this.form.alias = 0
            // this.form.name = ''
            // this.form.birth_date = ''
            // this.form.birth_place = ''
            // this.form.phone = ''
            // this.form.address = ''
            // this.form.address_rt = ''
            // this.form.address_rw = ''
            // this.form.sex = ''
            // this.form.blood = ''
            // this.form.married_id = ''
            // this.form.profession_id = ''
            // this.form.citizenship_id = ''
            // this.form.religion_id = ''
            // this.form.last_edu_id = ''
            // this.form.email = ''
            // this.form.address_id = ''
            // this.form.responsible_id = ''
            // this.form.responsible_person_idcard = ''
            // this.form.responsible_person_typeidcard = ''
            // this.form.responsible_person_name = ''
            // this.form.responsible_person_phone = ''
            // this.form.responsible_person_address_id = ''
        },
        deleteAllergy (index) {
            this.form.allergyTable.items.splice(index, 1)
            this.form.allergyToInput.splice(index, 1)
        },
        deleteInsurance (index) {
            this.form.insuranceTable.items.splice(index, 1)
            this.form.insuranceToInput.splice(index, 1)
            console.log('index: ', index, 'data: ', this.form.insuranceToInput)
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        // patient section
        selectFor(patient) {
            this.form.patient_type = patient;
            if (this.form.patient_type == 'new') {
                this.form.alias = 0
            } else {
                this.form.alias = 0
                this.form.alias = 3

                // disabled
                this.$refs.name
            }
        },
        onDisabled(items = 'default'){
            if (items == 'default' || items == '' || items == null) {
                // console.log('default');
                // return this.active.isDisabled = false
            } else if(items == 'view'){
                this.active.isDisabled = 'view'
                // console.log('view');
            } else if(items == 'edit'){
                this.active.isDisabled = 'edit'
                // console.log('edit');
            }
        },
        removeFileList() {
            this.fileSim = !this.fileSim;
        },
        selectFileList() {
            this.fileSim = !this.fileSim
            this.expandFiles = false
        },
        expandSelectedFiles() {
            this.expandFiles = !this.expandFiles
        },
        calculateAge() {
            const birth_date = new Date(this.form.birth_date);
            const today = new Date();
            const ageInMilliseconds = today - birth_date;
            const ageInYears = ageInMilliseconds / 31556952000; // number of milliseconds in a year
            this.age = Math.floor(ageInYears);
        },
        fillByNorm(fill_data, fill_data_rseponsible){
            // console.log('fill data: ', fill_data);
            this.form.name = fill_data.name
            this.form.idcard = fill_data.idcard
            this.form.phone = fill_data.phone
            this.form.married_id = 1
            this.form.religion_id = fill_data.religion_id
            this.form.address = fill_data.address
            this.address_form.province = fill_data.province_id
            this.address_form.regency = fill_data.regency_id
            this.address_form.district = fill_data.district_id
            this.form.address_id = fill_data.address_id
            this.form.zip_code = fill_data.zip_code
            this.form.responsible_id = fill_data_rseponsible.responsible_id
            this.form.responsible_person_typeidcard = fill_data_rseponsible.typeidcard
            this.form.responsible_person_profession_id = fill_data_rseponsible.profession_id
            this.form.responsible_person_name = fill_data_rseponsible.name
            this.form.responsible_person_phone = fill_data_rseponsible.phone
            this.form.responsible_person_address_id = fill_data_rseponsible.address_id
            this.form.responsible_person_idcard = fill_data_rseponsible.idcard
        }
    },
    watch: {
        
        // 'form.birth_date': function () {
        //     return this.getAlias
        // },
        'form.birth_date': function () {
            return this.getAlias
        },
        'form.sex': function () {
            return this.getAlias
        },
        'form.married_id': function () {
            return this.getAlias
        },
        // search norm if form.norm_mother is filled
        'form.norm_mother': function (value) {
            if (value != null && value != '') {
                this.$store.dispatch('patient/getByNorm', value)
                .then(response => {
                    if(response.length > 0){
                        const data = response[0]
                        const data_responsible = response[0].patient_responsible_person
                        this.fillByNorm(data,data_responsible)
                    }else{
                        this.fillByNorm('','')
                        // console.log("norm_mother doesn't exist" );
                    }
                })
            } else {
                this.fillByNorm('','')
            }
        },
        // 'form.insuranceInput.insurance_id' (obj) {
        //     return this.$store.dispatch('helper/getParamInsurance', obj)
        // },
        'address_form.province' (obj) {
            return this.$store.dispatch('helper/getParamRegency', obj)
        },
        'address_form.regency' (obj) {
            return this.$store.dispatch('helper/getParamDistrict', obj)
        },
        'address_form.district' (obj) {
            return this.$store.dispatch('helper/getParamVillage', obj)
        },
        dataFromAppointment(value) {
            console.log('dataFromAppointment', value);
            if (value.norm == '' || value.norm == null) {
                this.modalShow = true
                this.form.name = value.name
                this.form.birth_date = value.birth_date
                this.form.birth_place = value.birth_place
                this.form.phone = value.phone
                this.form.sex = value.sex
                this.form.appointment_id = value.id
            }
        },
        dataFromUpdatePatientMaster(value) {
            console.log('dataFromUpdatePatientMaster', value);
            this.modalShow = true
            // this.onDisabled('edit')

            // this.form.patient_type = value.patient_type
            
            this.form.alias = value.alias
            this.form.norm = value.norm
            this.form.name = value.name
            this.form.idcard = value.idcard
            this.form.birth_place = value.birth_place
            this.form.birth_date = value.birth_date
            this.form.sex = value.sex
            this.form.religion_id = value.religion_id
            this.form.married_id = value.married_id
            this.form.citizenship_id = value.citizenship_id
            this.form.phone = value.phone
            this.form.email = value.email
            this.form.blood = value.blood
            this.form.last_edu_id = value.last_edu_id
            this.form.profession_id = value.profession_id
            this.form.address_id = value.address_id
            this.zip_code = value.zip_code
            this.form.address = value.address 
            this.form.allergyTable.items = value.patient_allergy
            this.form.insuranceTable.items = value.patient_insurance
            this.address_form.province = value.province_id
            this.address_form.regency = value.regency_id
            this.address_form.district = value.district_id
            this.form.patient_issue_id = value.patient_issue_id
            // this.form.insuranceToInput = value.patient_insurance
            
            this.form.responsible_person_name = value.patient_responsible_person.name
            this.form.responsible_person_typeidcard = value.patient_responsible_person.typeidcard
            this.form.responsible_person_profession_id = value.patient_responsible_person.profession_id
            this.form.responsible_person_phone = value.patient_responsible_person.phone
            this.form.responsible_id = value.patient_responsible_person.responsible_id
            this.form.responsible_person_address_id = value.patient_responsible_person.address_id
            this.form.responsible_person_idcard = value.patient_responsible_person.idcard
        }
    },
    computed: {
        ...mapState({ 
            OptionsAlias: state => state.helper.options.alias,
            OptionsSex: state => state.helper.options.sex,
            OptionsReligion: state => state.helper.options.religion,
            OptionsMarried: state => state.helper.options.married,
            OptionsCitizenship: state => state.helper.options.citizenship,
            OptionsBlood: state => state.helper.options.blood,
            OptionsLastEdu: state => state.helper.options.lastedu,
            OptionsProfession: state => state.helper.options.profession,
            OptionsTypeAllergy: state => state.helper.options.typeallergy,
            OptionsResponsible: state => state.helper.options.responsible,
            OptionsInsurance: state => state.helper.options.insurance,
            OptionsPatientIssue: state => state.helper.options.patient_issue,
            OptionsTypeIdcard: state => state.helper.options.type_idcard,

            OptionsProvince: state => state.helper.options.province,
            OptionsRegency: state => state.helper.options.regency,
            OptionsDistrict: state => state.helper.options.district,
            OptionsVillage: state => state.helper.options.village,

            Options3rdCompany: state => state.helper.options.third_party_company,
            OptionsServiceClass: state => state.helper.options.service_class,
        }),
        upperName: {
            get() {
                return this.form.name;
            },
            set(value) {
                this.form.name = value.toUpperCase();
            },
        },
        getAlias() {
            this.calculateAge()
            console.log(this.form.alias)
            if (this.form.alias != 3) {
                if (this.form.married_id != 1) {
                    if (this.form.sex == 0) {
                        return this.form.alias = 5
                    } else {
                        return this.form.alias = 6
                    }
                } else {
                    if (this.age >= 0 && this.age <= 17) {
                        return this.form.alias = 1
                    } else {
                        if (this.form.sex == 0) {
                            return this.form.alias = 4
                        } else {
                            return this.form.alias = 6
                        }
                    }
                }
            } else {
                return this.form.alias = 3
            }
        },
    },
    created () {
        // this.$store.dispatch('helper/getParam', 'alias')
        // this.$store.dispatch('helper/getParam', 'sex')
        // this.$store.dispatch('helper/getParam', 'religion')
        // this.$store.dispatch('helper/getParam', 'married')
        // this.$store.dispatch('helper/getParam', 'citizenship')
        // this.$store.dispatch('helper/getParam', 'blood')
        // this.$store.dispatch('helper/getParam', 'last_edu')
        // this.$store.dispatch('helper/getParam', 'profession')
        // this.$store.dispatch('helper/getParam', 'responsible')
        // this.$store.dispatch('helper/getParam', 'type_allergy')
        // this.$store.dispatch('helper/getParam', 'insurance')
        // this.$store.dispatch('helper/getParam', 'patient_issue')
        // this.$store.dispatch('helper/getParam', 'typeidcard')
        // this.$store.dispatch('helper/getParamProvince', 'province')
        // this.$store.dispatch('helper/getParamThirdPartyCompany', 'third_party_company')
        // this.$store.dispatch('helper/getParamServiceClass', 'service_class')
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>
<style scoped>
@import '../../../../../../src/@core/css/utility.css';
@import '../../../../../../src/@core/css/_layouts/registration.css';

.user-inform {
    background: #dcf3f3;
    border: 1px dashed #680cff;
    border-radius: 10px;
}
</style>