<template>
    <div>
        <patientMenu selectedMenu="menu3" />
        <div class="label-head my-16 my-lg-24">
            <span class="px-16 text-bold-24 text-neutral-700">Resume</span>
        </div>
        <!-- ////////  -->
        <div class="card-body shadow-sm rounded">
            <div class="row mx-0">
                <div class="col-12 col-lg">
                    <div class="row">
                        <div class="col-12 col-lg-3">
                            <img src="@/@core/assets/images/pendaftaran/user.png" alt="your-photo"
                                class="rounded-circle shadow-sm c-img">
                        </div>
                        <div class="col-12 col-lg">
                            <span class="text-bold-18 text-indigo">
                                <img src="@/@core/assets/icons/nurse/user.svg" alt="user-icon">{{this.regData.norm}}
                            </span>
                            <p class="mb-0 mt-12 text-semi-24 text-neutral-800">
                                <span>({{this.regData.alias_name}}) </span>
                                <span>{{this.regData.name}}</span>
                            </p>
                            <div class="row mx-0 mt-16">
                                <b-badge class="rounded text-neutral-500 text-uppercase border border-success-200 px-10 py-1" :variant="this.regData.is_new == 'N' ? 'success-200' : 'info-200'">{{ this.regData.is_new == 'N' ? 'Pasien Lama' : 'pasien Baru' }}</b-badge>
                                <b-badge
                                    class="rounded text-neutral-500 text-uppercase border border-success-500 px-10 py-1 mx-lg-12 "
                                    variant="success-200">{{this.regData.health_facility_name}}</b-badge>
                                <b-badge
                                    class="rounded text-neutral-500 text-uppercase border border-warning-500 px-10 py-1 "
                                    variant="warning-200">{{this.regData.health_facility_item_name}}</b-badge>
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
                            <b-button pill variant="outline-indigo" @click="refresh()" size="sm">
                                <b-icon icon="arrow-clockwise" /> <span>Refresh</span>
                            </b-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex mt-32">
            <b-button pill @click="goToTab('LIST')" :variant="active.tab == 'LIST' ? 'primary' : 'outline-primary'" class="px-24">List</b-button>
            <b-button pill @click="goToTab('ADD')" :variant="active.tab == 'ADD' ? 'primary' : 'outline-primary'" class="px-24 ml-2">Tambah Resume</b-button>
        </div>
        <section v-if="active.tab === 'LIST'" class="mt-24 row">
            <div class="col">
                <b-table :fields="tableFields" :items="items_resume" bordered fixed thead-class="text-bold-14" tbody-tr-class="text-regular-12"  >
                    <template #cell(no)="row">
                        <div class="text-center">
                            {{ row.index+1 }}
                        </div>
                    </template>
                    <template #cell(action)="row">
                        <div class="d-flex">
                            <router-link :to="{ path: '/patient/resume/print/'+ reg_des_id + '/' + row.item.id}" target="_blank">
                                <b-button size="sm" variant="w3green">
                                    <b-icon icon="printer" class="mr-1"/>Cetak Resume
                                </b-button>
                            </router-link>
                            <b-button @click="onEditResume(row.item)" size="sm" variant="warning-500" class="ml-1">
                                <feather-icon icon="EditIcon" class="mr-1" />Edit
                            </b-button>
                        </div>
                    </template>
                </b-table>
            </div>
        </section>
        <section v-if="active.tab === 'ADD'" class="mt-32 row">
            <section class="col">
                <div class="card">
                    <div class="card-header bg-primary font-weight-bold text-white">Form Resume</div>
                    <div class="card-body">
                        <div>
                            <label class="c-form-label">Ringkasan Riwayat Penyakit</label>
                            <b-form-textarea rows="3" v-model="form.summary_medical_history" autocomplete="off"></b-form-textarea>
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">Hasil Pemeriksaan Fisik Penting dan Temuan Lain</label>
                            <b-form-textarea rows="3" v-model="form.physical_check" autocomplete="off"></b-form-textarea>
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">Indikasi Rawat Inap</label>
                            <b-form-textarea v-model="form.indication_for_hospitalization" rows="3" autocomplete="off" />
                        </div>
                        <div class="mt-20">
                            <label class="c-form-label">Diagnosis</label>
                            <ModernLabel label="Diagnosa (ICD10)" class="mt-1"/>

                            <div class="mt-8">
                                <b-table :fields="ICD10TableFields" :items="form.icd10" responsive bordered class="mt-8" style="font-size: 13px;">
                                    <template #cell(no)="row">
                                        <div class="text-center">{{ row.index+1 }}</div>
                                    </template>
                                    <template #cell(action)="row">
                                        <div class="d-flex justify-content-center">
                                            <b-button variant="danger-600" class="mr-2" size="sm" @click="delete10(row.index)">
                                                <feather-icon icon="TrashIcon" />
                                            </b-button>
                                            <b-button variant="info-600" size="sm" @click="move('icd10',row.index, -1)" :disabled="row.index === 0"  class="mr-2"> <feather-icon icon="ArrowUpIcon" /> </b-button>
                                            <b-button variant="info-600" size="sm" @click="move('icd10',row.index, 1)" :disabled="row.index === row.item.length - 1" class="mr-2"> <feather-icon icon="ArrowDownIcon" /> </b-button>
                                        </div>
                                    </template>
                                </b-table>
                                <v-select v-model="icd10_id" @input="onAdd10($event)" @search="onSearchIcd10" :options="OptionsIcd10" :reduce="OptionsIcd10 => OptionsIcd10" label="title" placeholder="-- Pilih ICD10 --" aria-readonly="true" class="mt-12" ></v-select>
                            </div>
                        </div>

                        <div class="mt-20">
                            <label class="c-form-label">Prosedur Diagnostik dan Terapeutik yang dilakukan</label>
                            <ModernLabel label="Tindakan (ICD9CM)" class="mt-1"/>

                            <div class="mt-8">
                                <b-table :fields="ICD9TableFields" :items="form.icd9cm" responsive bordered class="mt-8" style="font-size: 13px;">
                                    <template #cell(no)="row">
                                        <div class="text-center">{{ row.index+1 }}</div>
                                    </template>
                                    <template #cell(action)="row">
                                        <div class="d-flex justify-content-center">
                                            <b-button variant="danger-600" size="sm" class="mr-2" @click="delete9cm(row.index)">
                                                <feather-icon icon="TrashIcon" />
                                            </b-button>
                                        <b-button variant="info-600" size="sm" @click="move('icd9cm',row.index, -1)" :disabled="row.index === 0"  class="mr-2"> <feather-icon icon="ArrowUpIcon" /> </b-button>
                                        <b-button variant="info-600" size="sm" @click="move('icd9cm',row.index, 1)" :disabled="row.index === row.item.length - 1" class="mr-2"> <feather-icon icon="ArrowDownIcon" /> </b-button>
                                        </div>
                                    </template>
                                </b-table>

                                <v-select v-model="icd9cm_id" @input="onAdd9cm($event)" aria-readonly="true" @search="onSearchIcd9cm" :options="OptionsIcd9cm" :reduce="OptionsIcd9cm => OptionsIcd9cm" label="title" placeholder="-- Pilih ICD9CM --" class="mt-12"></v-select>
                            </div>
                        </div>

                        <div class="mt-20">
                            <ModernLabel label="Penunjang Laboratorium"/>
                            <div class="mt-8">
                                <template v-if="form.laboratory_request_items.length > 0">
                                    <b-table :fields="LaboratoryTableFields" :items="form.laboratory_request_items" responsive bordered class="mt-8" style="font-size: 13px;">
                                        <template #cell(no)="row">
                                            <div class="text-center">{{ row.index+1 }}</div>
                                        </template>
                                        <template #cell(action)="row">
                                            <div class="d-flex justify-content-center">
                                                <b-button variant="danger-600" size="sm" @click="deleteLabItem(row.index)">
                                                    <feather-icon icon="TrashIcon" />
                                                </b-button>
                                            </div>
                                        </template>
                                    </b-table>
                                </template>
                                <div v-else class="col font-italic text-center">Silakan tambah item penunjang laboratorium dari riwayat kajian sebelumnya</div>
                            </div>
                        </div>

                        <div class="mt-20">
                            <ModernLabel label="Penunjang Radiologi"/>
                            <div class="mt-8">
                                <template v-if="form.radiology_request_items.length > 0">
                                    <b-table :fields="RadiologyTableFields" :items="form.radiology_request_items" responsive bordered class="mt-8" style="font-size: 13px;">
                                        <template #cell(no)="row">
                                            <div class="text-center">{{ row.index+1 }}</div>
                                        </template>
                                        <template #cell(action)="row">
                                            <div class="d-flex justify-content-center">
                                                <b-button variant="danger-600" size="sm" @click="deleteRadItem(row.index)">
                                                    <feather-icon icon="TrashIcon" />
                                                </b-button>
                                            </div>
                                        </template>
                                    </b-table>
                                </template>
                                <div v-else class="col font-italic text-center">Silakan tambah item penunjang radiologi dari riwayat kajian sebelumnya</div>
                            </div>
                        </div>
                        <div class="mt-20">
                            <label class="c-form-label">Obat-obatan Penting yang diberikan selama perawatan</label>
                            <b-form-textarea rows="3" v-model="form.significant_medications" autocomplete="off"></b-form-textarea>
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">Obat yang dibawa pulang</label>
                            <b-form-textarea rows="3" v-model="form.take_home_medications" autocomplete="off"></b-form-textarea>
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">Cara pulang *</label>
                            <b-form-radio-group v-model="form.exit_id" class="text-regular-14">
                                <b-form-radio value="4a6d28c4-e020-425f-91e1-d39699cf679b">
                                    <div>Meninggal</div>
                                </b-form-radio>

                                <b-form-radio value="807ca005-5827-4e19-9c97-0bc3d4e924ca">
                                    <div>Atas Persetujuan Dokter</div>
                                </b-form-radio>

                                <b-form-radio value="a1631acc-6a94-414d-95de-a9faea0f1c2f">
                                    <div>Dirujuk</div>
                                </b-form-radio>

                                <b-form-radio value="ae0aabfa-1600-443f-8a60-62f9e76b1608">
                                    <div>Permintaan Sendiri</div>
                                </b-form-radio>

                                <b-form-radio value="bbc9fdce-affb-4b32-a386-0ed7e91c1e89">
                                    <div>Pulang Non Ranap</div>
                                </b-form-radio>

                                <b-form-radio value="adb7ce0b-ede3-4fee-b273-ce159d286732">
                                    <div>Lainnya</div>
                                    <b-form-input type="text" v-model="form.exit_anythingelse" v-show="form.exit_id == 'adb7ce0b-ede3-4fee-b273-ce159d286732'" autocomplete="off"></b-form-input>
                                </b-form-radio>
                            </b-form-radio-group>
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">Kondisi saat pulang *</label>
                            <b-form-radio-group v-model="form.exit_condition_type_id" class="text-regular-14">
                                <b-form-radio value="1">
                                    <div>Sembuh</div>
                                    <div><i>(Cured)</i></div>
                                </b-form-radio>

                                <b-form-radio value="2">
                                    <div>Tidak Sembuh</div>
                                    <div><i>(Not Cured)</i></div>
                                </b-form-radio>

                                <b-form-radio value="3">
                                    <div>Meninggal < 48 jam</div>
                                    <div><i>(Died < 48 hrs)</i></div>
                                </b-form-radio>

                                <b-form-radio value="4">
                                    <div>Lainnya</div>
                                    <div><i>(Other)</i></div>
                                    <b-form-input type="text" v-model="form.exit_condition_anythingelse" v-show="form.exit_condition_type_id == 4" autocomplete="off"></b-form-input>
                                </b-form-radio>

                            </b-form-radio-group>
                        </div>

                        <ModernLabel label="Instruksi Rawat Lanjutan" small="(Continuous Care Instruction)" class="mt-24" />

                        <div class="mt-12">
                            <label class="c-form-label">Kontrol</label>
                            <b-form-radio-group v-model="form.care_instruction_control" class="text-regular-14">
                                <b-form-radio value="1">
                                    <div>RS Taman Harapan Baru</div>
                                </b-form-radio>

                                <b-form-radio value="2">
                                    <div>Dokter Keluarga / Klinik Luar</div>
                                    <div><i>(Family Phsician / Outer Clinic)</i></div>
                                </b-form-radio>

                                <b-form-radio value="3">
                                    <div>Puskesmas</div>
                                    <div><i>(Primary Health Care)</i></div>
                                </b-form-radio>

                                <b-form-radio value="4">
                                    <div>Meninggal > 48 jam</div>
                                    <div><i>(Died > 48 hrs)</i></div>
                                </b-form-radio>

                                <b-form-radio value="5">
                                    <div>Ada Perbaikan Kondisi</div>
                                    <div><i>(Improved Condition)</i></div>
                                </b-form-radio>

                                <b-form-radio value="6">
                                    <div>Kondisi Tidak Berubah</div>
                                    <div><i>(No Significant Change)</i></div>
                                </b-form-radio>

                            </b-form-radio-group>
                            <div v-if="form.care_instruction_control == '1'" class="row mt-12">
                                <div class="col-lg-3">
                                    <label class="c-form-label">Tanggal Kontrol</label>
                                    <b-form-input type="date" v-model="form.care_instruction_control_date" autocomplete="off"></b-form-input>
                                </div>
                                <div class="col-lg-3">
                                    <label class="c-form-label">Kontrol di poliklinik: </label>
                                    <v-select v-model="form.health_facility_item_id" aria-readonly="true" :options="OptionsHealthFacilityItem" :reduce="OptionsHealthFacilityItem => OptionsHealthFacilityItem.value" label="title" placeholder="-- Poliklinik --"></v-select>
                                </div>
                            </div>
                        </div>

                        <div class="mt-12">
                            <label class="c-form-label">Lain-lain</label>
                            <b-form-radio-group v-model="form.care_instruction_anythingelse" class="text-regular-14">

                                <b-form-radio value="1">
                                    <div>Diet</div>
                                    <div><i>(Diet)</i></div>
                                </b-form-radio>

                                <b-form-radio value="2">
                                    <div>Aktifitas</div>
                                    <div><i>(Activity)</i></div>
                                </b-form-radio>

                                <b-form-radio value="3">
                                    <div>Kondisi Tidak Berubah</div>
                                    <div><i>(No Significant Change)</i></div>
                                </b-form-radio>

                            </b-form-radio-group>
                        </div>

                        <div class="mt-12">
                            <label class="c-form-label">Edukasi Selama Di Rumah</label>
                            <b-form-textarea rows="3" v-model="form.home_education" autocomplete="off"></b-form-textarea>
                        </div>
                    </div>
                    <div class="card-footer mt-32">
                        <div class="d-flex justify-content-end">
                            <!-- <b-button :disabled="isDisabled" @click="resetForm()" variant="outline-primary" class="px-24">Reset</b-button> -->
                            <b-button :disabled="isDisabled" @click="validationResume()" variant="primary" class="px-24 ml-16">Simpan</b-button>
                        </div>
                    </div>
                </div>
            </section>
            <section class="col-3 card position-sticky" :style="{top: '0', height: '100vh', overflowY: 'auto'}">
                <div class="card-header row text-center font-weight-bold mb-16 justify-content-center">RIWAYAT KAJIAN</div>
                <div v-for="(item, index) in this.soap_list" class="accordion" role="tablist">
                    <b-card no-body class="mb-1">
                        <b-card-header block v-b-toggle="'accordion-'+index" header-tag="header" class="font-weight-bold pointer d-flex justify-content-between align-items-center" role="tab" onMouseOver="this.style.backgroundColor='#648DE1'; this.style.color='#FFFFFF'" onMouseOut="this.style.backgroundColor='#FFFFFF'; this.style.color='#212529'" >
                            <div>
                                <div class="font-weight-bold">Kajian {{ localDateTime(item.date_added) }}</div>
                                <div>{{item.employee_doctor_name}} | {{ item.health_facility_item_name ? item.health_facility_item_name : '-' }}</div>
                            </div>
                            <feather-icon icon="ChevronDownIcon" size="18" />
                        </b-card-header>
                        <b-collapse :id="`accordion-`+index" accordion="my-accordion" role="tabpanel">
                            <b-card-body>
                                <b-button @click="copyAllStudies($event, item)" variant="primary" class="btn-block mt-16">Copy All</b-button>
                                <div class="card mt-16">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <div class="font-weight-bold">Subjective</div>
                                            <b-button @click="copySelected($event, copySubjective, item)" size="sm" variant="primary">Copy</b-button>
                                        </div>
                                    </div>
                                    <div class="card-body" style="font-size: 14px">
                                        <div>
                                            <div><b>Riwayat Penyakit Sekarang: </b>{{ item.history_disease_now ? item.history_disease_now : '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card mt-16">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <div class="font-weight-bold">Objective</div>
                                            <b-button @click="copySelected($event, copyObjective, item)" size="sm" variant="primary">Copy</b-button>
                                        </div>
                                    </div>
                                    <div class="card-body" style="font-size: 14px">
                                        <div><b>Pemeriksaan Fisik: </b> {{ item.physical_check ? item.physical_check : '-' }}</div>
                                    </div>
                                </div>
                                <div class="card mt-16">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <div class="font-weight-bold">Assesment</div>
                                            <b-button @click="copySelected($event, copyAssesment, item)" size="sm" variant="primary">Copy</b-button>
                                        </div>
                                    </div>
                                    <div class="card-body" style="font-size: 14px">
                                        <div><b>Diagnosa (ICD10): </b><span v-if="!item.icd10 || item.icd10.length == 0">-</span></div>
                                        <template v-if="item.icd10 && item.icd10.length > 0">
                                            <li v-for="icd10 in item.icd10">{{ icd10.code }} {{ icd10.name }}</li>
                                        </template>
                                    </div>
                                </div>
                                <div class="card mt-16">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <div class="font-weight-bold">Plan</div>
                                            <b-button @click="copySelected($event, copyPlan, item)" size="sm" variant="primary">Copy</b-button>
                                        </div>
                                    </div>
                                    <div class="card-body" style="font-size: 14px">
                                        <!-- <div>
                                            <div>
                                                <b>Pemeriksaan: </b>
                                                <span v-if="!item.transaction_doctor || item.transaction_doctor.length == 0">-</span>
                                            </div>
                                            <template v-if="item.transaction_doctor && item.transaction_doctor.length > 0">
                                                <li v-for="trans_list in item.transaction_doctor">{{ trans_list.costing_name }}</li>
                                            </template>
                                        </div> -->
                                        <div>
                                            <div>
                                                <b>Tindakan (ICD9): </b>
                                                <span v-if="!item.icd9cm || item.icd9cm.length == 0">-</span>
                                            </div>
                                            <template v-if="item.icd9cm && item.icd9cm.length > 0">
                                                <li v-for="icd9 in item.icd9cm">{{ icd9.code }} {{ icd9.name }}</li>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <div class="card mt-16">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <div class="font-weight-bold">Laboratorium</div>
                                            <b-button @click="copySelected($event, copyLaboratory, item)" size="sm" variant="primary">Copy</b-button>
                                        </div>
                                    </div>
                                    <div class="card-body" style="font-size: 14px">
                                        <div>
                                            <template v-if="item.laboratory_request_items && item.laboratory_request_items.length > 0">
                                                <li v-for="(lab_list, index) in item.laboratory_request_items" :class="index == 0 ? 'mb-2' : 'my-2'" style="list-style-type: none;">
                                                    <b-button @click="copyItemLaboratory(lab_list)" size="sm" variant="outline-primary">
                                                        <feather-icon icon="PlusIcon" />
                                                    </b-button>
                                                    {{ lab_list.costing_name }}
                                                </li>
                                            </template>
                                            <span v-if="!item.laboratory_request_items || item.laboratory_request_items.length == 0">-</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card mt-16">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <div class="font-weight-bold">Radiologi</div>
                                            <b-button @click="copySelected($event, copyRadiology, item)" size="sm" variant="primary">Copy</b-button>
                                        </div>
                                    </div>
                                    <div class="card-body" style="font-size: 14px">
                                        <div class="">
                                            <template v-if="item.radiology_request_items && item.radiology_request_items.length > 0">
                                                <li v-for="(rad_list, index) in item.radiology_request_items" :class="index == 0 ? 'mb-2' : 'my-2'" style="list-style-type: none;">
                                                    <b-button @click="copyItemRadiology(rad_list)" size="sm" variant="outline-primary">
                                                        <feather-icon icon="PlusIcon" />
                                                    </b-button>
                                                    {{ rad_list.costing_name }}
                                                </li>
                                            </template>
                                            <span v-if="!item.radiology_request_items || item.radiology_request_items.length == 0">-</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card mt-16">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <div class="font-weight-bold">Resep</div>
                                            <b-button @click="copySelected($event, copyPrescription, item)" size="sm" variant="primary">Copy</b-button>
                                        </div>
                                    </div>
                                    <div class="card-body" style="font-size: 14px">
                                        <div>
                                            <b>Resep: </b>
                                            <span v-if="!item.prescription_new || item.prescription_new.length == 0">-</span>
                                        </div>
                                        <template v-if="item.prescription_new?.length > 0">
                                            <template v-for="(list, index_i) in item.prescription_new">
                                                <div class="mt-2"><b>{{list.type_prescription_name}} | {{list.no_order}} | {{ list.date_0 }}</b></div>
                                                <template v-if="list.form_prescription?.length > 0">
                                                    <li v-for="iip in list.form_prescription">
                                                        {{ iip.pharmacy_catalog_name }}, {{ iip.qty }} {{ iip.package_name }}, {{ iip.used }}, {{ iip.desc }}
                                                    </li>
                                                </template>
                                            </template>
                                        </template>
                                    </div>
                                </div>
                                <!-- <div class="card mt-16">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <div class="font-weight-bold">Resep</div>
                                            <b-button @click="copySelected($event, copyPrescription, item)" size="sm" variant="primary">Copy</b-button>
                                        </div>
                                    </div>
                                    <div class="card-body" style="font-size: 14px">
                                        <div>
                                            <b>Resep: </b>
                                            <span v-if="!item.form_prescription || item.form_prescription.length == 0">-</span>
                                        </div>
                                        <template v-if="item.form_prescription && item.form_prescription.length > 0">
                                            <li v-for="iip in item.form_prescription">
                                                {{ iip.pharmacy_catalog_name }}, {{ iip.qty }} {{ iip.package_name }}, {{ iip.used }}, {{ iip.desc }}
                                            </li>
                                        </template>
                                    </div>
                                </div> -->

                            </b-card-body>
                        </b-collapse>
                    </b-card>
                </div>
            </section>
        </section>

    </div>
</template>
<script>
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import patientMenu from '@/@core/components/tab-menu/patientMenu.vue';
import administration from '@/views/layouts/patient/doctor/administration.vue';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue';
import moment from 'moment';
import vSelect from 'vue-select';
import { mapState } from 'vuex';

export default {
    name: 'Patient_Resume',
    components: {
        patientMenu, administration, ModernLabel, vSelect
    },
    data() {
        return {
            id: '',
            userData: JSON.parse(localStorage.getItem('userData')),
            reg_des_id: '',
            get_norm: '',
            get_registration_id: '',
            regData: {},
            form: {
                id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                summary_medical_history: '',
                physical_check: '',
                icd10: [],
                icd9cm: [],
                significant_medications: '',
                take_home_medications: '',
                exit_id: '',
                exit_anythingelse: '',
                exit_condition_type_id: '',
                exit_condition_anythingelse: '',
                care_instruction_control: '',
                care_instruction_control_date: '',
                care_instruction_anythingelse: '',
                is_resume_exit: 1,
                laboratory_request_items: [],
                radiology_request_items: [],
                home_education: '',
                health_facility_item_id: '',
                indication_for_hospitalization: '',
            },
            searchlab: '',
            searchrad: '',
            searchphy: '',
            icd9cm_id: '',
            OptionsIcd9cm: [],
            icd10_id: '',
            OptionsIcd10: [],
            soap_list: [],
            active: {
                tab: 'LIST',
            },
            tableFields: [
                { key: 'no', label: 'No', thStyle: 'width:50px; text-align: center' },
                { key: 'doctor_in_charge_name', label: 'Nama Dokter' },
                { key: 'health_facility_item_name', label: 'Poli/Ruangan' },
                { key: 'date_added', label: 'Tanggal Resume Dibuat' },
                { key: 'action', label: 'Aksi' },
            ],
            supportPackage: {
                lab_package_list: [],
                rad_package_list: [],
            },
            items_resume: [],
            paramsInsuranceServiceClassLab:{},
            isDisabled: false,
            ICD10TableFields: [
                { key: 'no', label: 'No', thStyle: 'width: 50px; text-align: center' },
                { key: 'title', label: 'ICD10' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 100px; text-align: center' },
            ],
            ICD9TableFields: [
                { key: 'no', label: 'No', thStyle: 'width: 50px; text-align: center' },
                { key: 'title', label: 'ICD9CM' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 100px; text-align: center' },
            ],
            LaboratoryTableFields: [
                { key: 'no', label: 'No', thStyle: 'width: 50px; text-align: center' },
                { key: 'laboratory_request_item_name', label: 'Laboratorium' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 100px; text-align: center' },
            ],
            RadiologyTableFields: [
                { key: 'no', label: 'No', thStyle: 'width: 50px; text-align: center' },
                { key: 'radiology_request_item_name', label: 'Radiologi' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 100px; text-align: center' },
            ],
        }
    },
    computed: {
        ...mapState({
            itemsLaboratory: state => state.laboratory.package.items,
            itemsRadiology: state => state.radiology.package.items,
            OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,
        }),
    },
    watch : {
        "form.exit_id"(newValue) {
            if (newValue != "adb7ce0b-ede3-4fee-b273-ce159d286732") {
                this.form.exit_anythingelse = ""
            }
        },
        "form.exit_condition_type_id"(newValue) {
            if (newValue != 4) {
                this.form.exit_condition_anythingelse = ""
            }
        },
        "form.care_instruction_control"(newValue) {
            if (newValue != 1) {
                this.form.care_instruction_control_date = ""
                this.form.health_facility_item_id = ""
            }
        },
    },
    methods: {
        move(data,index,direction){
            if (data === 'icd9cm') {
                const newIndex = index + direction;
                if (newIndex < 0 || newIndex >= this.form.icd9cm.length) return;
                const temp = this.form.icd9cm[index];
                this.form.icd9cm.splice(index, 1);
                this.form.icd9cm.splice(newIndex, 0, temp);
            } else {
                const newIndex = index + direction;
                if (newIndex < 0 || newIndex >= this.form.icd10.length) return;
                const temp = this.form.icd10[index];
                this.form.icd10.splice(index, 1);
                this.form.icd10.splice(newIndex, 0, temp);
            }
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
        validationResume() {
            // console.log('this.form: ', this.form);
            if (this.form.id == '') {
                this.form.registration_destination_id = this.reg_des_id
                this.form.doctor_in_charge_id = this.regData.doctor_in_charge_id
            }

            if (this.form.exit_id == 0 || this.form.exit_id == '') {
                this.notification('Warning','CloseIcon','warning','Cara pulang wajib diisi')
            } else {
                try {
                    this.isDisabled = true

                    this.$store.dispatch('resume/postPutResume', this.form).then((res) => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.isDisabled = false
                        this.getResumeList(this.regData.norm)
                        this.active.tab = 'LIST'
                        this.resetForm()
                    })
                } catch (error) {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    this.isDisabled = false
                    console.error('Error at: ', error)
                }
            }

        },
        getResumeList(norm) {
            this.$store.dispatch('resume/getAllList', { norm: norm }).then((response) => {
                if (response) {
                    this.items_resume = response
                    // console.log('this.items_resume: ', this.items_resume);
                }
            })
        },

        onEditResume(obj) {
            console.info('obj: ', obj)
            this.resetForm()
            this.active.tab = 'ADD'

            this.form.id = obj.id ? obj.id : ''
            this.form.registration_destination_id = obj.registration_destination_id ? obj.registration_destination_id : ''
            this.form.doctor_in_charge_id = obj.doctor_in_charge_id ? obj.doctor_in_charge_id : ''
            this.form.summary_medical_history = obj.summary_medical_history ? obj.summary_medical_history : ''
            this.form.physical_check = obj.physical_check ? obj.physical_check : ''

            if (obj.icd10) {
                const mappedIcd10 = obj.icd10.map(item => {
                    return {
                        icd10_id: item.icd10_id,
                        value: item.icd10_id,
                        title: `${item.icd10_code} ${item.icd10_name}`,
                        action: "action"
                    };
                });
                this.form.icd10 = mappedIcd10;
            }

            if (obj.icd9cm) {
                const mappedIcd9cm = obj.icd9cm.map(item => {
                    return {
                        icd9cm_id: item.icd9cm_id,
                        value: item.icd9cm_id,
                        title: `${item.icd9cm_code} ${item.icd9cm_name}`,
                        action: "action"
                    };
                });
                this.form.icd9cm = mappedIcd9cm;
            }

            this.form.significant_medications = obj.significant_medications ? obj.significant_medications : ''
            this.form.take_home_medications = obj.take_home_medications ? obj.take_home_medications : ''
            this.form.exit_id = obj.exit_id ? obj.exit_id : ''
            this.form.exit_anythingelse = obj.exit_anythingelse ? obj.exit_anythingelse : ''
            this.form.exit_condition_type_id = obj.exit_condition_type_id ? obj.exit_condition_type_id : ''
            this.form.exit_condition_anythingelse = obj.exit_condition_anythingelse ? obj.exit_condition_anythingelse : ''
            this.form.care_instruction_control = obj.care_instruction_control ? obj.care_instruction_control : ''
            this.form.care_instruction_control_date = obj.care_instruction_control_date ? obj.care_instruction_control_date : ''
            this.form.care_instruction_anythingelse = obj.care_instruction_anythingelse ? obj.care_instruction_anythingelse : ''
            this.form.is_resume_exit =obj.is_resume_exit ? obj.is_resume_exit :  1

            if (obj.laboratory_request_items) {
                obj.laboratory_request_items.map(item => {
                    let temp = {
                        laboratory_request_item_id: item.item_id,
                        laboratory_request_item_name: item.costing_name,
                        laboratory_package_id: item.laboratory_package_id
                    };

                    if (this.form.laboratory_request_items.some(item => item.laboratory_package_id === temp.laboratory_package_id)) {
                        this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan item laboratorium yang sama')
                    } else {
                        this.form.laboratory_request_items.push(temp)
                    }
                });
            }

            if (obj.radiology_request_items) {
                obj.radiology_request_items.map(item => {
                    let temp = {
                        radiology_request_item_id: item.item_id,
                        radiology_request_item_name: item.costing_name,
                        radiology_package_id: item.radiology_package_id
                    };

                    if (this.form.radiology_request_items.some(item => item.radiology_package_id === temp.radiology_package_id)) {
                        this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan item radiologi yang sama')
                    } else {
                        this.form.radiology_request_items.push(temp)
                    }
                });
            }
            // this.form.laboratory_request_items = obj.laboratory_request_items ? obj.laboratory_request_items : []
            // this.form.radiology_request_items = obj.radiology_request_items ? obj.radiology_request_items : []
            this.form.home_education = obj.home_education ? obj.home_education : ''
            this.form.health_facility_item_id = obj.health_facility_item_id ? obj.health_facility_item_id : ''
            this.form.indication_for_hospitalization = obj.indication_for_hospitalization ? obj.indication_for_hospitalization : ''

            // this.copyAssesment(obj)
            // this.copyPlan(obj)
            // this.copyLaboratory(obj)
            // this.copyRadiology(obj)
        },

        resetForm(){
            this.form.id = ''
            this.form.registration_destination_id = ''
            this.form.doctor_in_charge_id = ''
            this.form.summary_medical_history = ''
            this.form.physical_check = ''
            this.form.icd10 = []
            this.form.icd9cm = []
            this.form.significant_medications = ''
            this.form.take_home_medications = ''
            this.form.exit_id = ''
            this.form.exit_anythingelse = ''
            this.form.exit_condition_type_id = ''
            this.form.exit_condition_anythingelse = ''
            this.form.care_instruction_control = ''
            this.form.care_instruction_control_date = ''
            this.form.care_instruction_anythingelse = ''
            this.form.is_resume_exit = 1
            this.form.laboratory_request_items = []
            this.form.radiology_request_items = []
            this.form.home_education = ''
            this.form.health_facility_item_id = ''
            this.form.indication_for_hospitalization = ''
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        refresh () {
            router.go(router.currentRoute)
        },
        getData (id) {
			this.$store.dispatch('registration/getById', id).then(data => {
                this.regData = data[0]
                // this.form.registration_destination_id = id
                // this.form.doctor_in_charge_id = this.regData.doctor_in_charge_id
                this.get_registration_id = this.regData.registration_id

                this.paramsInsuranceServiceClassLab = {
                    type_costing_id:4,
                }
                this.$store.dispatch('laboratory/getPackageAll', this.paramsInsuranceServiceClassLab)

                this.$store.dispatch('soap/getByRegistrationId', this.get_registration_id).then(data => {
                    this.soap_list = data
                    // console.log('this.soap_list: ', this.soap_list);
                    // this.copyAllStudies(null, this.soap_list[0])
                })

                this.getResumeList(this.regData.norm)
            })
        },
        onAdd10 (event) {
            this.icd10_id = ''

            if (this.form.icd10.some(item => item.icd10_id === event.value)) {
                this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list ICD10 yang sama')
            } else {
                this.form.icd10.push({ icd10_id:event.value, title:event.title, value:event.value, action: 'action'})
            }
        },
        delete10 (index) {
            this.form.icd10.splice(index, 1)
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
        onAdd9cm (event) {
            this.icd9cm_id = ''
            if (this.form.icd9cm.some(item => item.icd9cm_id === event.value)) {
                this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list ICD9CM yang sama')
            } else {
                this.form.icd9cm.push({ icd9cm_id:event.value, title:event.title, value:event.value, action: 'action'})
            }
        },
        delete9cm (index) {
            this.form.icd9cm.splice(index, 1)
        },
        onSearchIcd9cm(search, loading) {
            if(search.length >= 2) {
                loading(true);
                this.$store.dispatch('icd9cm/getParam', search).then(data => {
                    this.OptionsIcd9cm = []
                    for (let i = 0; i < data.length; i++) {
                        const option = []
                        for (const key in data[i]) {
                            if (key === 'id') { option['value'] = data[i][key] } else if (key === 'name') { option['title'] = data[i]['code']+' '+data[i][key] } else if (key === 'id') { option['oid'] = data[i][key] }
                        }
                        this.OptionsIcd9cm.push(Object.assign({}, option))
                    }
                    loading(false)
                })
            }
        },
        localDateTime(input) {
            return moment(input).locale("id").format('DD MMMM YYYY HH:MM')
        },
        getRelation(code) {
            const relations = {
                '1': 'Ayah',
                '2': 'Ibu',
                '3': 'Kakak',
                '4': 'Adik',
            };
            return relations[code] || '-';
        },
        copyAllStudies(event, obj) {
            this.copySubjective(obj)
            this.copyObjective(obj)
            this.copyAssesment(obj)
            this.copyPlan(obj)
            this.copyLaboratory(obj)
            this.copyRadiology(obj)
            this.copyPrescription(obj)

            if (event) {
                const button = event.target;
                button.innerText = 'Copied All';
                setTimeout(() => {
                    button.innerText = 'Copy All';
                }, 1000);
            }
        },
        copySelected(event, func, obj) {
            const button = event.target;
            button.innerText = 'Copied';

            func(obj);

            setTimeout(() => {
                button.innerText = 'Copy';
            }, 1000);
        },
        copySubjective(obj) {
            this.form.summary_medical_history = obj.history_disease_now
            // this.form.summary_medical_history = obj.history_disease_past
        },
        copyObjective(obj) {
            this.form.physical_check = obj.physical_check
        },
        copyAssesment(obj) {
            if (obj.icd10) {
                const mappedIcd10 = obj.icd10.map(item => {
                    return {
                        icd10_id: item.icd10_id,
                        value: item.icd10_id,
                        title: `${item.code} ${item.name}`,
                        action: "action"
                    };
                });
                this.form.icd10 = mappedIcd10;
            }
        },
        copyPlan(obj) {
            if (obj.icd9cm) {
                const mappedIcd9cm = obj.icd9cm.map(item => {
                    return {
                        icd9cm_id: item.icd9cm_id,
                        value: item.icd9cm_id,
                        title: `${item.code} ${item.name}`,
                        action: "action"
                    };
                });
                this.form.icd9cm = mappedIcd9cm;
            }

        },
        copyLaboratory(arry) {
            if (arry.laboratory_request_items) {
                arry.laboratory_request_items.map(item => {
                    let temp = {
                        laboratory_request_item_id: item.id,
                        laboratory_request_item_name: item.costing_name,
                        laboratory_package_id: item.laboratory_package_id
                    };

                    if (this.form.laboratory_request_items.some(item => item.laboratory_package_id === temp.laboratory_package_id)) {
                        this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan item laboratorium yang sama')
                    } else {
                        this.form.laboratory_request_items.push(temp)
                    }
                });
            }
        },
        copyItemLaboratory(obj) {
            // console.log(obj);
            if (obj) {
                let temp = {
                    laboratory_request_item_id: obj.id,
                    laboratory_request_item_name: obj.costing_name,
                    laboratory_package_id: obj.laboratory_package_id
                };

                if (this.form.laboratory_request_items.some(item => item.laboratory_package_id === temp.laboratory_package_id)) {
                    this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan item laboratorium yang sama')
                } else {
                    this.form.laboratory_request_items.push(temp)
                }
            }
        },
        deleteLabItem (index) {
            this.form.laboratory_request_items.splice(index, 1)
        },
        copyRadiology(arry) {
            if (arry.radiology_request_items) {
                arry.radiology_request_items.map(item => {
                    let temp = {
                        radiology_request_item_id: item.id,
                        radiology_request_item_name: item.costing_name,
                        radiology_package_id: item.radiology_package_id
                    };

                    if (this.form.radiology_request_items.some(item => item.radiology_package_id === temp.radiology_package_id)) {
                        this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan item radiologi yang sama')
                    } else {
                        this.form.radiology_request_items.push(temp)
                    }
                });
            }
        },
        copyItemRadiology(obj) {
            // console.log(obj);

            if (obj) {
                let temp = {
                    radiology_request_item_id: obj.id,
                    radiology_request_item_name: obj.costing_name,
                    radiology_package_id: obj.radiology_package_id
                };

                if (this.form.radiology_request_items.some(item => item.radiology_package_id === temp.radiology_package_id)) {
                    this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan item radiologi yang sama')
                } else {
                    this.form.radiology_request_items.push(temp)
                }
            }
        },
        deleteRadItem (index) {
            this.form.radiology_request_items.splice(index, 1)
        },

        copyPrescription(obj) {
            if (obj.prescription_new?.length > 0) {
                for (let i = 0; i < obj.prescription_new.length; i++) {
                    const el_i = obj.prescription_new[i];

                    if (el_i.form_prescription?.length > 0) {
                        let text = ''
                        let text_2 = ''
                        for (let j = 0; j < el_i.form_prescription.length; j++) {
                            const el_j = el_i.form_prescription[j];
                            let temp = ''
                            console.log(el_j.type_prescription_id)
                            if (el_j.type_prescription_id === 2) {
                                temp = el_j.pharmacy_catalog_name
                                text_2 += temp + '\n'
                            } else {
                                temp = el_j.pharmacy_catalog_name
                                text += temp + '\n'
                            }
                        }
                        this.form.significant_medications += text
                        this.form.take_home_medications += text_2
                    }
                }
            }
        },
        // copyPrescription(obj) {
        //     if (obj.form_prescription) {
        //         let text = ''
        //         for (let i = 0; i < obj.form_prescription.length; i++) {
        //             const el = obj.form_prescription[i];
        //             let temp = ''
        //             temp = el.pharmacy_catalog_name
        //             text += temp + '\n'
        //             // text += temp + ', '
        //         }
        //         this.form.significant_medications += text
        //     }
        // },
        goToTab(tab){
            this.resetForm()
            this.active.tab = tab
            if (this.active.tab == 'ADD') {
                this.copyAllStudies(null, this.soap_list[0])
            }
        },

    },

    created () {
        if (this.$route.params.id) {
            this.id = this.$route.params.id
            this.reg_des_id = this.$route.params.id
            this.getData(this.$route.params.id)
        }

        this.$store.dispatch('employee/getParamSpecialist')
        this.$store.dispatch('radiology/getPackageAll')
        this.$store.dispatch('healthfacilityitem/getParam', '3cb626f5-9148-4fb1-8663-3d7f028b01a6')

        this.getLabPackageListV2()
        this.getRadPackageListV2()
        // this.$store.dispatch('physiotherapy/getPackageAll')

    }
}
</script>
<style scoped>
.label-head::before {
    content: '';
    border-left: 6px solid #648DE1;
}
.c-img {
    aspect-ratio: 1/1;
    object-fit: cover;
    max-width: 150px;
    max-height: 150px;
    border: 10px solid white;
}
/* li {
    list-style-type: none;
}
li a {
    color: #78838C;
}
li a:hover {
    text-decoration: none;
    color: #101725;
    background-color: rgb(245, 239, 254);
    cursor: pointer;
}
li a.active {
    background-color: rgba(132, 66, 243, 0.05);
    color: indigo;
    border: 1px solid indigo;
    color: #1B1B1B;
}
li a.active:hover {
    border: 1px solid indigo;
    background-color: rgb(245, 239, 254);
    color: indigo;
    cursor: pointer;
} */

</style>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>