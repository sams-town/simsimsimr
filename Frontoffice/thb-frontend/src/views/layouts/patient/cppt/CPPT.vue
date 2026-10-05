<template>
    <div>
        <patientMenu selectedMenu="menu2" />
        <div class="label-head my-16 my-lg-24">
            <span class="px-16 text-bold-24 text-neutral-700">Pengkajian Dokter</span>
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

        <div class="card-body mt-20 shadow-sm">
            <div class="d-flex justify-content-between">
                <div>
                    <template v-if="cppt_list.length === 0">
                        <b-button disabled pill variant="pink-500" size="sm" class="px-16 mt-12 mr-2">
                            <b-icon icon="printer" /> <span>Cetak CPPT</span>
                        </b-button>
                    </template>
                    <template v-else>
                        <router-link :to="{ path: '/patient/cppt/print/'+ reg_des_id}" target="_blank">
                            <b-button pill variant="pink-500" size="sm" class="px-16 mt-12 mr-2">
                                <b-icon icon="printer" /> <span>Cetak CPPT</span>
                            </b-button>
                        </router-link>
                    </template>
                </div>
                <div>
                    <b-button @click="openModalSBAR" pill variant="success" size="sm" class="px-16 mt-12 mr-2">
                        <b-icon icon="chat-square-dots-fill" /> <span>SBAR</span>
                    </b-button>
                    
                    <b-button @click="openModalCPPT('ADD', null)" :disabled="is_soap_null === true" pill variant="primary" size="sm" class="px-16 mt-12 mr-2">
                        <b-icon icon="plus-circle-fill" /> <span>Tambah CPPT</span>
                    </b-button>

                    <template v-if="regData.doctor_in_charge_id == userData.employee_id">
                        <b-button @click="verifyAllList()" pill variant="orange-500" size="sm" class="px-16 mt-12">
                            <b-icon icon="patch-check-fill" /> <span>Verifikasi Semua</span>
                        </b-button>
                    </template>
                </div>
            </div>

            
            <template v-if="is_soap_null === false">
                <b-table :fields="CPPTFields" :items="cppt_list" responsive bordered class="w-100 mt-24" style="font-size: 13px">
                    <template #cell(no)="row">
                        <div>{{ row.index+1 }}</div>
                    </template>
                    <template #cell(user)="row">
                        <div>{{ row.item.user_added_name ? row.item.user_added_name : '' }} ({{ row.item.user_added_role ? row.item.user_added_role : '' }})</div>
                        <div>{{ row.item.date_added ? row.item.date_added : '' }}</div>
                    </template>
                    <template #cell(content)="row">
                        <div>
                            <div>
                                <div class="font-weight-bold">SUBJECTIVE</div>
                                <div v-html="parsedBody(row.item?.subjective)"></div>
                                <!-- <table class="w-100 custom-border table-sm" style="border: 0px;">
                                    <tr>
                                        <td class="align-top font-weight-bold" style="width: 150px;">SUBJECTIVE</td>
                                        <td class="align-top px-1 text-center" style="width: 20px;">:</td>
                                        <td>{{ row.item.subjective ? row.item.subjective : '-' }}</td>
                                    </tr>
                                </table> -->
                            </div>
                            <div class="mt-8">
                                <div class="font-weight-bold">OBJECTIVE</div>
                                <div v-html="parsedBody(row.item?.objective)"></div>
                            </div>
                            <!-- <div class="mt-8">
                                <div class="font-weight-bold">ASSESSMENT</div>
                                <div v-html="parsedBody(row.item?.assessment)"></div>
                            </div> -->
                            <!-- <div class="mt-8">
                                <table class="w-100 custom-border table-sm" style="border: 0px;">
                                    <tr>
                                        <td colspan="3" class="font-weight-bold">OBJECTIVE</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top" style="width: 150px;">Objective</td>
                                        <td class="px-1 text-center" style="width: 20px;">:</td>
                                        <td>
                                            <div>{{ row.item.objective ? row.item.objective : '-' }}</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top">Tanda Vital</td>
                                        <td class="px-1 text-center">:</td>
                                        <td>
                                            <div>
                                                <b>Systolic: </b>{{ row.item.systolic_blood_pressure }} mmHg,
                                                <b>Diastolic: </b>{{ row.item.diastolic_blood_pressure }} mmHg,
                                                <b>Frekuensi Nadi: </b>{{ row.item.pulse_frequency }} x/mnt,
                                                <b>Frekuensi Nafas: </b>{{ row.item.breath_frequency }} x/mnt,
                                                <b>Saturasi Oksigen: </b>{{ row.item.oximetry_frequency }} %,
                                                <b>Suhu: </b>{{ row.item.temperature }} ᵒC,
                                                <b>Gula Darah: </b>{{ row.item.blood_sugar }} mg/dL,
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top">Antropometri</td>
                                        <td class="px-1 text-center">:</td>
                                        <td>
                                            <div class="mt-8">
                                                <b>Berat Badan: </b>{{ row.item.weight }} kg,
                                                <b>Tinggi Badan: </b>{{ row.item.height }} cm,
                                                <b>IMT: </b>{{ row.item.imt }} kg/m²,
                                                <b>Lingkar Kepala: </b>{{ row.item.head_circumference }} cm,
                                                <b>Lingkar Dada: </b>{{ row.item.chest_circumference }} cm,
                                                <b>Lingkar Lengan Atas: </b>{{ row.item.arm_circumference }} cm,
                                                <b>Lingkar Perut: </b>{{ row.item.stomach_circumference }} cm,
                                                <b>Lingkar Lutut: </b>{{ row.item.knee_circumference }} cm,
                                            </div>
                                            <div>{{ row.item.objective ? row.item.objective : '-' }}</div>
                                        </td>
                                    </tr>
                                </table>
                            </div> -->
                            <div class="mt-8">
                                <table class="w-100 custom-border table-sm" style="border: 0px;">
                                    <tr>
                                        <td colspan="3"class="font-weight-bold">ASSESSMENT</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top" style="width: 150px;">Assessment</td>
                                        <td class="align-top px-1 text-center" style="width: 20px;">:</td>
                                        <td>{{ row.item.assessment ? row.item.assessment : '-' }}</td>
                                    </tr>
                                    <tr v-if="row.item.icd10">
                                        <td class="align-top">Diagnosa (ICD10)</td>
                                        <td class="px-1 text-center" style="width: 20px;">:</td>
                                        <td>
                                            <template v-if="row.item.icd10">
                                                <li v-for="(list, index) in row.item.icd10" :key="index">{{ list.code }} {{ list.name }}</li>
                                            </template>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="mt-8">
                                <div class="font-weight-bold">PLAN</div>
                                <div v-html="parsedBody(row.item?.plan)"></div>

                                <table class="w-100 custom-border table-sm mt-1" style="border: 0px;">
                                    <tr v-if="row.item.laboratory_request && row.item.laboratory_request.no_request">
                                        <td class="align-top" style="width: 150px;">Laboratorium</td>
                                        <td class="align-top px-1 text-center" style="width: 20px;">:</td>
                                        <td>
                                            <div class="font-weight-bold">{{ row.item.laboratory_request && row.item.laboratory_request.no_request ? row.item.laboratory_request.no_request : '' }}</div>
                                            <template v-if="row.item.laboratory_request_items">
                                                <li v-for="(list, index) in row.item.laboratory_request_items">{{ list.costing_name }}</li>
                                            </template>
                                        </td>
                                    </tr>
                                    <tr v-if="row.item.radiology_request && row.item.radiology_request.no_request">
                                        <td class="align-top">Radiologi</td>
                                        <td class="align-top px-1 text-center">:</td>
                                        <td>
                                            <div class="font-weight-bold">{{ row.item.radiology_request && row.item.radiology_request.no_request ? row.item.radiology_request.no_request : '' }}</div>
                                            <template v-if="row.item.radiology_request_items">
                                                <li v-for="(list, j) in row.item.radiology_request_items">
                                                    {{ list.code }} {{ list.costing_name }} 
                                                    <template v-if="list.url.length > 0">
                                                        <b-button size="sm" variant="outline-secondary" class="ml-2" @click="isRefcontrol(urlbe,list.url)"><feather-icon icon="EyeIcon" /> Lihat Hasil</b-button>
                                                    </template>
                                                </li>
                                            </template>
                                        </td>
                                    </tr>
                                    <tr v-if="row.item.prescription && row.item.prescription.no_order">
                                        <td class="align-top">Resep</td>
                                        <td class="align-top px-1 text-center">:</td>
                                        <td>
                                            <div class="font-weight-bold">{{ row.item.prescription && row.item.prescription.no_order ? row.item.prescription.no_order : '' }}</div>
                                            <template v-if="row.item.prescription && row.item.prescription.form_prescription && row.item.prescription.form_prescription.length > 0">
                                                <li v-for="(list, index) in row.item.prescription.form_prescription">{{ list.pharmacy_catalog_name }}</li>
                                            </template>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </template>
                    <template #cell(instructions)="row">
                        <div v-html="parsedBody(row.item?.instructions)"></div>
                    </template>
                    <template #cell(action)="row">
                        <div v-if="row.item.status == 1" class="d-flex">

                            <template v-if="row.item.user_added == userData.user_id">
                                <b-button @click="openModalCPPT('EDIT', row.item)" size="sm" variant="warning-500" class="mr-2">
                                    <feather-icon icon="EditIcon" />
                                </b-button> 
                                <b-button @click="removeCpptList(row.item)" size="sm" variant="danger-700" class="mr-2">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </template>
                            <template v-if="regData.doctor_in_charge_id == userData.employee_id">
                                <b-button @click="verifyCpptList(row.item)" size="sm" variant="orange-500" class="mr-2">Verifikasi</b-button>
                            </template>

                        </div>
                        <div v-else>Telah di verifikasi oleh DPJP</div>
                    </template>
                </b-table>
            </template>

            <div v-if="is_soap_null === true" class="w-100 bg-warning-100 p-12 mt-24 text-center" style="font-size: 13px;">
                <span>Apakah Anda hendak menambahkan CPPT?</span>
                <b-button @click="createEmptySoap" size="sm" variant="w3green" class="ml-2">Ya, Lanjutkan</b-button>
            </div>
            <!-- <div v-if="is_soap_null === true" class="w-100 bg-warning-100 p-12 mt-24 text-center" style="font-size: 13px;">
                Agar dapat menambahkan CPPT, mohon buat Assessment dokter terlebih dahulu melalui
                <router-link :to="{ path: '/patient/doctor/'+ reg_des_id}">tautan ini</router-link> 
            </div> -->
        </div>

        <b-modal v-model="active.refcontrol" scrollable size="xl" @close="isRefcontrol()" title="Hasil Radiologi" hide-footer>
            <div>
                <b-table :fields="radiologyResultField" :items="imageview" bordered responsive small style="font-size: 14px;">
                    <template #cell(no)="row">
                        <div>{{ row.index+1 }}</div>
                    </template>
                    <template #cell(expertise)="row">
                        <div>
                            <table class="w-100 custom-border">
                                <tr>
                                    <td style="width: 150px;">Pemeriksa Radiologi</td>
                                    <td class="align-top text-center" style="width: 30px;">:</td>
                                    <td>{{ row.item.user_lab_name }}</td>
                                </tr>
                                <tr>
                                    <td>Verifikator</td>
                                    <td class="align-top text-center">:</td>
                                    <td>{{ row.item.doctor_lab_name }}</td>
                                </tr>
                                <tr>
                                    <td>Tanggal Order</td>
                                    <td class="align-top text-center">:</td>
                                    <td>{{ row.item.date_request ? localDate(row.item.date_request) : '' }}</td>
                                </tr>
                                <tr>
                                    <td>Tanggal Diproses</td>
                                    <td class="align-top text-center">:</td>
                                    <td>{{ row.item.date_process ? localDate(row.item.date_process) : '' }}</td>
                                </tr>
                                <tr>
                                    <td>Expertise</td>
                                    <td class="align-top text-center">:</td>
                                    <td><span v-html="parsedBody(row.item.conclusion)"></span></td>
                                </tr>
                                <tr>
                                    <td>Kesan</td>
                                    <td class="align-top text-center">:</td>
                                    <td><span v-html="parsedBody(row.item.advice)"></span></td>
                                </tr>
                            </table>
                        </div>
                    </template>
                    <template #cell(url)="data">
                        <div>
                            <b-img rounded :src="urlbe+data.value" style="width: 100%;" />
                        </div>
                    </template>
                    <template #cell(action)="row">
                        <div class="d-flex">
                            <b-button @click="blankToPrint({radiology_request_id: row.item.radiology_request_id, radiology_request_item_id: row.item.radiology_request_item_id , image_id: row.item.id})" v-b-popover.hover.top="'Cetak'" size="sm" variant="w3green" class="mr-2">
                                <feather-icon icon="PrinterIcon" />
                            </b-button>
                            <b-button @click="expandImage(row.item.url)" v-b-popover.hover.top="'Expand Gambar'" size="sm" variant="primary">
                                <feather-icon icon="ExternalLinkIcon" />
                                <!-- <feather-icon icon="Maximize2Icon" /> -->
                            </b-button>
                        </div>
                    </template>
                </b-table>
            </div>
        </b-modal>
        
        <!-- <b-modal v-model="active.modal_cppt" scrollable size="xl" title="CPPT" hide-footer> -->
        <b-modal v-model="active.modal_cppt" scrollable size="xl" hide-footer>
            <template #modal-header>
                <div>
                    <div class="font-weight-bold" style="font-size: 18px">CPPT</div>
                    <div>
                        <b-badge variant="primary" class="mr-1" style="font-weight: 600">BB {{ vitalData?.weight || 0}}</b-badge>
                        <b-badge variant="primary" class="mr-1" style="font-weight: 600">TB {{ vitalData?.height || 0}}</b-badge>
                        <b-badge variant="primary" class="mr-1" style="font-weight: 600">Sistolik {{ vitalData?.systolic || 0}}</b-badge>
                        <b-badge variant="primary" class="mr-1" style="font-weight: 600">Diastolik {{ vitalData?.diastolic || 0}}</b-badge>
                        <b-badge variant="primary" class="mr-1" style="font-weight: 600">Nadi {{ vitalData?.pulse || 0}}</b-badge>
                        <b-badge variant="primary" class="mr-1" style="font-weight: 600">Suhu {{ vitalData?.temperature || 0}}</b-badge>
                        <b-badge variant="primary" class="mr-1" style="font-weight: 600">Nafas {{ vitalData?.respiratory_rate || 0}}</b-badge>
                        <b-badge variant="primary" class="mr-1" style="font-weight: 600">Oksigen {{ vitalData?.oxygen_saturation || 0}}</b-badge>
                        <b-badge variant="primary" class="mr-1" style="font-weight: 600">Skala Nyeri {{ vitalData?.sore_score || 0}}</b-badge>
                        <b-badge variant="primary" class="mr-1" style="font-weight: 600">EWS {{ vitalData?.ews || 0}}</b-badge>
                    </div>
                </div>
                <feather-icon icon="XIcon" @click="active.modal_cppt = false" size="24" class="pointer" />
            </template>
            
            <div class="row">
                <section class="col-9">
                    <div class="card">
                        <div class="card-header bg-primary text-white font-weight-bold text-center">FORM TAMBAH CPPT</div>
                        <div class="card-body">
                            <div class="card">
                                <div class="card-header font-weight-bold bg-info-200">SUBJECTIVE</div>
                                <div class="card-body">
                                    <div>
                                        <label class="c-form-label">Subjective</label>
                                        <b-form-textarea rows="4" v-model="form_cppt.subjective" autocomplete="off" ></b-form-textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="card mt-20">
                                <div class="card-header font-weight-bold bg-info-200">OBJECTIVE</div>
                                <div class="card-body">
                                    <div>
                                        <label class="c-form-label">Objective</label>
                                        <b-form-textarea rows="5" v-model="form_cppt.objective" autocomplete="off" ></b-form-textarea>
                                    </div>
                                    <!-- <div class="col mt-12 px-0">
                                        <b-card no-body class="mb-1">
                                            <b-card-header v-b-toggle.ttv class="p-1" style="background-color: #ECF2F5">
                                                <div class="d-flex align-items-center">
                                                    <ModernLabel label="Tanda Vital" class="w-100" />
                                                    <feather-icon icon="ChevronDownIcon" size="20" />
                                                </div>
                                            </b-card-header>
                                            <b-collapse id="ttv" visible>
                                                <b-card-body>
                                                    <div class="row">
                                                        <div class="col-lg-4">
                                                            <div class="mt-1">
                                                                <label class="c-form-label">Systolic</label>
                                                                <b-input-group append="mmHg">
                                                                    <b-form-input type="number" autocomplete="off" v-model="form_cppt.systolic_blood_pressure"></b-form-input>
                                                                </b-input-group>
                                                            </div>
                                                            <div class="mt-1">
                                                                <label class="c-form-label">Diastolic</label>
                                                                <b-input-group append="mmHg">
                                                                    <b-form-input type="number" autocomplete="off" v-model="form_cppt.diastolic_blood_pressure"></b-form-input>
                                                                </b-input-group>
                                                            </div>
                                                            <div class="mt-1">
                                                                <label class="c-form-label">Frekuensi Nadi</label>
                                                                <b-input-group append="x/mnt">
                                                                    <b-form-input type="number" autocomplete="off" v-model="form_cppt.pulse_frequency"></b-form-input>
                                                                </b-input-group>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 border border-top-0 border-bottom-0">
                                                            <div class="mt-1">
                                                                <label class="c-form-label">Frekuensi Nafas</label>
                                                                <b-input-group append="x/mnt">
                                                                    <b-form-input type="number" autocomplete="off" v-model="form_cppt.breath_frequency"></b-form-input>
                                                                </b-input-group>
                                                            </div>
                                                            <div class="mt-1">
                                                                <label class="c-form-label">Saturasi Oksigen</label>
                                                                <b-input-group append="%">
                                                                    <b-form-input type="number" autocomplete="off" v-model="form_cppt.oximetry_frequency"></b-form-input>
                                                                </b-input-group>
                                                            </div>
                                                            <div class="mt-1">
                                                                <label class="c-form-label">Suhu</label>
                                                                <b-input-group append="ᵒC">
                                                                    <b-form-input type="number" autocomplete="off" v-model="form_cppt.temperature"></b-form-input>
                                                                </b-input-group>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <div class="mt-1">
                                                                <label class="c-form-label">Skor Nyeri</label>
                                                                <b-form-input autocomplete="off" v-model="form_cppt.sore_score"></b-form-input>
                                                            </div>
                                                            <div class="mt-1">
                                                                <label class="c-form-label">EWS</label>
                                                                <b-form-input autocomplete="off" v-model="form_cppt.ews"></b-form-input>
                                                            </div>
                                                            <div class="mt-1">
                                                                <label class="c-form-label">Gula Darah</label>
                                                                <b-input-group append="mg/dL">
                                                                    <b-form-input type="number" autocomplete="off" v-model="form_cppt.blood_sugar"></b-form-input>
                                                                </b-input-group>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </b-card-body>
                                            </b-collapse>
                                        </b-card>
                                    </div> -->
                                    <!-- <div class="col px-0">
                                        <b-card no-body class="mb-1">
                                            <b-card-header v-b-toggle.ant class="p-1" style="background-color: #ECF2F5">
                                                <div class="d-flex align-items-center">
                                                    <ModernLabel label="Antropometri" class="w-100" />
                                                    <feather-icon icon="ChevronDownIcon" size="20" />
                                                </div>
                                            </b-card-header>
                                            <b-collapse id="ant">
                                                <b-card-body>
                                                    <div class="row mt-12">
                                                        <div class="col-lg-4">
                                                            <label class="c-form-label">Berat Badan</label>
                                                            <b-input-group append="kg">
                                                                <b-form-input type="number" autocomplete="off" @input="imtChanged($event)" v-model="form_cppt.weight"></b-form-input>
                                                            </b-input-group>
                                                        </div>
                                                        <div class="col-lg-4 mt-10 mt-lg-0">
                                                            <label class="c-form-label">Tinggi Badan</label>
                                                            <b-input-group append="cm">
                                                                <b-form-input type="number" autocomplete="off" @input="imtChanged($event)" v-model="form_cppt.height"></b-form-input>
                                                            </b-input-group>
                                                        </div>
                                                        <div class="col-lg-4 mt-10 mt-lg-0">
                                                            <label class="c-form-label">IMT</label>
                                                            <b-input-group append="kg/m²">
                                                                <b-form-input type="number" autocomplete="off" v-model="form_cppt.imt"></b-form-input>
                                                            </b-input-group>
                                                        </div>
                                                        <div class="col-lg-4 mt-10">
                                                            <label class="c-form-label">Lingkar Kepala</label>
                                                            <b-input-group append="cm">
                                                                <b-form-input type="number" autocomplete="off" v-model="form_cppt.head_circumference"></b-form-input>
                                                            </b-input-group>
                                                        </div>
                                                        <div class="col-lg-4 mt-10">
                                                            <label class="c-form-label">Lingkar Dada</label>
                                                            <b-input-group append="cm">
                                                                <b-form-input type="number" autocomplete="off" v-model="form_cppt.chest_circumference"></b-form-input>
                                                            </b-input-group>
                                                        </div>
                                                        <div class="col-lg-4 mt-10">
                                                            <label class="c-form-label">Lingkar Lengan Atas</label>
                                                            <b-input-group append="cm">
                                                                <b-form-input type="number" autocomplete="off" v-model="form_cppt.arm_circumference"></b-form-input>
                                                            </b-input-group>
                                                        </div>
                                                        <div class="col-lg-4 mt-10">
                                                            <label class="c-form-label">Lingkar Perut</label>
                                                            <b-input-group append="cm">
                                                                <b-form-input type="number" autocomplete="off" v-model="form_cppt.stomach_circumference"></b-form-input>
                                                            </b-input-group>
                                                        </div>
                                                        <div class="col-lg-4 mt-10">
                                                            <label class="c-form-label">Lingkar Lutut</label>
                                                            <b-input-group append="cm">
                                                                <b-form-input type="number" autocomplete="off" v-model="form_cppt.knee_circumference"></b-form-input>
                                                            </b-input-group>
                                                        </div>
                                                    </div>
                                                </b-card-body>
                                            </b-collapse>
                                        </b-card>
                                    </div> -->
                                </div>
                            </div>
                            
                            <div class="card mt-20">
                                <div class="card-header font-weight-bold bg-info-200">ASSESSMENT</div>
                                <div class="card-body">
                                    <div>
                                        <label class="c-form-label">Assessment</label>
                                        <b-form-textarea rows="4" v-model="form_cppt.assessment" autocomplete="off" ></b-form-textarea>
                                    </div>
                                    <div class="mt-20">
                                        <ModernLabel label="Diagnosa (ICD10)" />
                                        <table v-if="form_cppt.icd10.length > 0" class="table table-bordered mt-12">
                                            <tr>
                                                <th class="text-center" style="width: 60px;">No</th>
                                                <th>ICD10</th>
                                                <th class="text-center" style="width: 120px;">Aksi</th>
                                            </tr>
                                            <tr class="item" v-for="(item,index) in form_cppt.icd10">
                                                <td class="text-center">{{index+1 }}</td>
                                                <td>{{ item.title }}</td>
                                                <td>
                                                    <div class="d-flex justify-content-center">
                                                        <b-button variant="danger-600" size="sm" @click="delete10(index)" class="mr-2 mx-auto">
                                                            <feather-icon icon="TrashIcon" />
                                                        </b-button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                        
                                        <v-select v-model="icd10_id" @input="onAdd10($event)" aria-readonly="true" @search="onSearchIcd10" :options="OptionsIcd10" :reduce="OptionsIcd10 => OptionsIcd10" label="title" placeholder="-- Diagnosa --" class="mt-12"></v-select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mt-20" v-if="userData.user_group.name === 'Super User' || userData.user_group.name === 'Doctor'">
                                <div class="card-header font-weight-bold bg-info-200">PLAN</div>
                                <div class="card-body">
                                    <div>
                                        <label class="c-form-label">Plan</label>
                                        <b-form-textarea rows="4" v-model="form_cppt.plan" autocomplete="off" ></b-form-textarea>
                                    </div>

                                    <b-card no-body class="mt-32">
                                        <b-tabs card>
                                            <b-tab title="Laboratorium" active>
                                                <section :class="active.cppt_view != 'ADD' && active.editAccess.laboratory == false ? 'bg-warning-100 pb-20': ''">
                                                    <div v-if="active.cppt_view != 'ADD' && active.editAccess.laboratory == false" class="card-body bg-warning mb-16">
                                                        <div class="d-flex align-items-center">
                                                            <b-icon-exclamation-triangle-fill variant="neutral-800" style="width: 24px; height: 24px;"/>
                                                            <div class="ml-3 font-italic">Form penunjang laboratorium tidak dapat diubah karena sudah diproses oleh laboratorium</div>
                                                        </div>
                                                    </div>

                                                    <!-- <ModernLabel label="Laboratorium" /> -->

                                                    <div class="mt-12">
                                                        <b-form-input type="text" v-model="searchlab" placeholder="Pencarian"></b-form-input>
                                                        <div class="col-lg-12 mt-3">
                                                            <h5>Checked Items:</h5>
                                                            <b-form-checkbox-group v-model="form_cppt.laboratory_request_items" :disabled="active.cppt_view != 'ADD' && active.editAccess.laboratory == false">
                                                                <b-form-checkbox
                                                                    v-for="(item, index) in form_cppt.laboratory_request_items"
                                                                    :key="index"
                                                                    :value="item"
                                                                    :disabled="active.cppt_view != 'ADD' && active.editAccess.laboratory == false"
                                                                    >
                                                                    {{ item.costing_name }}
                                                                </b-form-checkbox>
                                                            </b-form-checkbox-group>
                                                        </div>
                                                        <div class="col mt-12 py-2 bg-neutral-100" style="height: 400px; overflow-y: scroll;">
                                                            <div class="row">
                                                                <b-form-checkbox-group size="sm" stacked>
                                                                    <b-form-checkbox-group class="col-lg-12" v-model="form_cppt.laboratory_request_items" :disabled="active.cppt_view != 'ADD' && active.editAccess.laboratory == false">
                                                                        <div v-for="(item, index) in filteredListLab" :key="index" :class="index != 0 ? 'mt-32' : 'mt-0'">
                                                                            <div class="font-weight-bold text-uppercase">{{ item.category_name }}</div>
                                                                            <div class="row mx-0"> 
                                                                                <b-form-checkbox v-for="(list, packIndex) in item.category_items" v-bind:key="packIndex" :value="{'service_class_id': list.costing_services_class.service_class_id ,'laboratory_package_id':list.id, 'costing_name':list.costing_name}" class="col-lg-5">{{list.costing_name }}</b-form-checkbox>
                                                                            </div>
                                                                        </div>
                                                                    </b-form-checkbox-group>
                                                                </b-form-checkbox-group>
                                                                <!-- <b-form-checkbox-group size="sm" stacked>
                                                                    <b-form-checkbox-group class="col-lg-12" v-model="form_cppt.laboratory_request_items" :disabled="active.cppt_view != 'ADD' && active.editAccess.laboratory == false">
                                                                        <b-form-checkbox v-for="(il, index) in filteredListLab" v-bind:key="index" :value="{'service_class_id':'9b702245-79c5-4beb-8218-53afb08647aa','laboratory_package_id':il.id, 'costing_name':il.costing_name}" class="col-lg-5">{{il.costing_name }}</b-form-checkbox>
                                                                    </b-form-checkbox-group>
                                                                </b-form-checkbox-group> -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </section>
                                            </b-tab>
                                            <b-tab title="Radiologi">
                                                <section :class="active.cppt_view != 'ADD' && active.editAccess.radiology == false ? 'bg-warning-100 pb-20': ''">
                                                    <div v-if="active.cppt_view != 'ADD' && active.editAccess.radiology == false" class="card-body bg-warning mb-16">
                                                        <div class="d-flex align-items-center">
                                                            <b-icon-exclamation-triangle-fill variant="neutral-800" style="width: 24px; height: 24px;"/>
                                                            <div class="ml-3 font-italic">Form penunjang radiologi tidak dapat diubah karena sudah diproses oleh laboratorium</div>
                                                        </div>
                                                    </div>

                                                    <!-- <ModernLabel label="Radiologi" /> -->

                                                    <div class="mt-12">
                                                        <b-form-input type="text" v-model="searchrad" placeholder="Pencarian"></b-form-input>
                                                        <div class="col-lg-12 mt-3">
                                                            <h5>Checked Items:</h5>
                                                            <b-form-checkbox-group v-model="form_cppt.radiology_request_items" :disabled="active.cppt_view != 'ADD' && active.editAccess.radiology == false">
                                                                <b-form-checkbox
                                                                    v-for="(item, index) in form_cppt.radiology_request_items"
                                                                    :key="index"
                                                                    :value="item"
                                                                    :disabled="active.cppt_view != 'ADD' && active.editAccess.radiology == false"
                                                                    >
                                                                    {{ item.costing_name }}
                                                                </b-form-checkbox>
                                                            </b-form-checkbox-group>
                                                        </div>
                                                        <div class="col mt-12 py-2 bg-neutral-100" style="height: 400px; overflow-y: scroll;">
                                                            <div class="row">
                                                                <b-form-checkbox-group size="sm" stacked>
                                                                    <b-form-checkbox-group class="col-lg-12" v-model="form_cppt.radiology_request_items" :disabled="active.cppt_view != 'ADD' && active.editAccess.radiology == false">
                                                                        <div v-for="(item, index) in filteredListRad" :key="index" :class="index != 0 ? 'mt-32' : 'mt-0'">
                                                                            <div class="font-weight-bold text-uppercase">{{ item.category_name }}</div>
                                                                            <div class="row mx-0"> 
                                                                                <b-form-checkbox v-for="(list, packIndex) in item.category_items" v-bind:key="packIndex" :value="{'service_class_id': list.costing_services_class.service_class_id ,'radiology_package_id':list.id, 'costing_name':list.costing_name}" class="col-lg-5">{{list.costing_name }}</b-form-checkbox>
                                                                            </div>
                                                                        </div>
                                                                    </b-form-checkbox-group>
                                                                </b-form-checkbox-group>
                                                                <!-- <b-form-checkbox-group size="sm" stacked>
                                                                    <b-form-checkbox-group class="col-lg-12" v-model="form_cppt.radiology_request_items" :disabled="active.cppt_view != 'ADD' && active.editAccess.radiology == false">
                                                                        <b-form-checkbox v-for="(ir, index) in filteredListRad" v-bind:key="index" :value="{'service_class_id':'9b702245-79c5-4beb-8218-53afb08647aa','radiology_package_id':ir.id, 'costing_name': ir.costing_name}" class="col-lg-5">{{ir.costing_name }}</b-form-checkbox>
                                                                    </b-form-checkbox-group>
                                                                </b-form-checkbox-group> -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </section>
                                            </b-tab>
                                        </b-tabs>
                                    </b-card>
                                </div>
                            </div>
                            
                            
                            <template v-if="userData.user_group.name === 'Super User' || userData.user_group.name === 'Doctor'">
                                <ModernLabel label="Resep" class="mt-20 mb-8 w-100" />
                                <div :class="active.cppt_view != 'ADD' && active.editAccess.prescription == false ? 'bg-warning-100 py-20' : ''">
                                    <div v-if="active.cppt_view != 'ADD' && active.editAccess.prescription == false" class="card-body bg-warning mb-16">
                                        <div class="d-flex align-items-center">
                                            <b-icon-exclamation-triangle-fill variant="neutral-800" style="width: 24px; height: 24px;"/>
                                            <div class="ml-3 font-italic">Form resep tidak dapat diubah karena resep obat sudah diproses oleh farmasi.</div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <label class="c-form-label">Tipe Resep</label>
                                            <v-select v-model="form_cppt.type_prescription_id" :options="PrescriptionTypeOptions" :reduce="PrescriptionTypeOptions => PrescriptionTypeOptions.value" label="title" placeholder="-- Tipe Resep --"></v-select>
                                        </div>
                                        <template v-if="regData.health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f' && regData.health_facility_item_id == '6c7d7e53-ff24-4246-85b2-07ea77ac801f'">
                                            <div class="col-lg-6 mt-10 mt-lg-0">
                                                <label class="c-form-label">Tujuan Order</label>
                                                <v-select v-model="form_cppt.pharmacy_depo_id" :options="DepoOptions" :reduce="DepoOptions => DepoOptions.value" label="title" placeholder="-- Pilih Depo --"></v-select>
                                            </div>
                                        </template>
                                    </div>

                                    <table class="table table-bordered mt-16">
                                        <thead class="text-center text-bold-14">
                                            <tr>
                                                <th rowspan="2"></th>
                                                <th rowspan="2" style="min-width: 250px;">NAMA BARANG</th>
                                                <th colspan="4">ESTIMASI ORDER</th>
                                                <th></th>
                                            </tr>
                                            <tr>
                                                <th style="min-width: 50px;">SEDIAAN OBAT</th>
                                                <th style="min-width: 50px;">DOSIS DOKTER</th>
                                                <th style="min-width: 80px;">JUMLAH<br>KEMASAN</th>
                                                <!-- <th style="min-width: 140px;">KEMASAN</th> -->
                                                <th style="min-width: 50px;">ATURAN PAKAI<br>KETERANGAN</th>
                                            </tr>
                                        </thead>
                                        <template v-for="(item_p, index_p) in form_cppt.form_prescription_parent">
                                            <tr>
                                                <td>
                                                    <b>{{ item_p.prescription }}</b>
                                                </td>
                                                <td v-if="item_p.id !== 1">
                                                    <b-input-group append="Jumlah">
                                                        <b-form-input :disabled="active.editAccess.prescription == false" style="width:150px" type="number" v-model="item_p.value" />
                                                    </b-input-group>
                                                    <b-input-group append="Aturan Pakai">
                                                        <b-form-input :disabled="active.editAccess.prescription == false" style="width:150px" type="text" v-model="item_p.used" />
                                                    </b-input-group>
                                                </td>
                                            </tr>
                                            <tr class="item" v-for="(item, index) in form_cppt.form_prescription" v-if="item_p.id === item.prescription_type_id">
                                                <td>
                                                    <template v-if="active.editAccess.prescription == true">
                                                        <b-button variant="danger-600" size="sm" @click="deletePRES(index)" class="mr-2">
                                                            <feather-icon icon="TrashIcon" />
                                                        </b-button>
                                                    </template>
                                                </td>
                                                <td>{{ item.name }}</td>
                                                <td>{{ item.sum_dose }}</td>
                                                <td>
                                                    <b-form-input v-model="item.value_doctor" :disabled="active.editAccess.prescription == false" :readonly="item.prescription_type_id === 1" style="width:100px" type="number"/>

                                                    <!-- <template v-if="item.prescription_type_id === 1">
                                                        <b-form-input :disabled="active.editAccess.prescription == false" style="width:100px" readonly type="number" v-model="item.value_doctor"/>
                                                    </template>
                                                    <template v-if="item.prescription_type_id !== 1">
                                                        <b-form-input :disabled="active.editAccess.prescription == false" style="width:100px" type="number" v-model="item.value_doctor"/>
                                                    </template> -->
                                                </td>
                                                <td>
                                                    <b-form-input :disabled="active.editAccess.prescription == false" style="width:100px" type="number" v-model="item.value_package" />
                                                    <v-select :disabled="active.editAccess.prescription == false" v-model="item.package_id" :options="PackageOptions" :reduce="PackageOptions => PackageOptions.value" label="title" placeholder="Kemasan"></v-select>
                                                </td>
                                                <td>
                                                    <b-input-group append="Aturan Pakai" v-if="item.prescription_type_id === 1">
                                                        <b-form-input :disabled="active.editAccess.prescription == false" style="width:150px" type="text" v-model="item.used" />
                                                    </b-input-group>
                                                    <b-input-group append="Keterangan">
                                                        <b-form-input :disabled="active.editAccess.prescription == false" style="width:150px" type="text" v-model="item.desc" />
                                                    </b-input-group>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr v-if="active.editAccess.prescription == true">
                                            <td></td>
                                            <td colspan="2">
                                                <v-select v-model="prescription" @input="onAddPRES($event)" :options="PrescriptionOptions" :reduce="PrescriptionOptions => PrescriptionOptions" label="title" placeholder="-- Racikan --"></v-select>
                                                <v-select v-model="catalog" @input="onAddCAT($event)" @search="onSearchCatalog" :options="OptionsCatalog" :reduce="CatalogOptions => CatalogOptions" label="title" placeholder="-- Pilih Katalog --"></v-select>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </template>                            

                            <div class="mt-12">
                                <label class="c-form-label">Plan Non Farmakologis</label>
                                <b-form-textarea rows="4" v-model="form_cppt.instructions" autocomplete="off" ></b-form-textarea>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="w-100 d-flex justify-content-end">
                                <b-button @click="validationCPPT" :disabled="active.disabledCPPT" variant="primary">Simpan</b-button>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="col-3 card position-sticky" :style="{top: '0', height: '100vh', overflowY: 'auto'}">
                    <div class="card-header row text-center font-weight-bold mb-16 justify-content-center">RIWAYAT CPPT PASIEN</div>
                    <div v-for="(item, index) in cppt_history" class="accordion" role="tablist">
                        <b-card no-body class="mb-1">
                            <b-card-header block v-b-toggle="'accordion-'+index" header-tag="header" class="pointer d-flex justify-content-between align-items-center" role="tab" onMouseOver="this.style.backgroundColor='#648DE1'; this.style.color='#FFFFFF'" onMouseOut="this.style.backgroundColor='#FFFFFF'; this.style.color='#212529'" >
                                <div>
                                    <div class="font-weight-bold">CPPT {{ localDateTime(item.date_added) }}</div>
                                    <div>{{ item.user_added_name }} || {{ item.user_added_role }}</div>
                                </div>
                                <feather-icon icon="ChevronDownIcon" size="18" />
                            </b-card-header>
                            <b-collapse :id="`accordion-`+index" accordion="my-accordion" role="tabpanel">
                                <b-card-body>
                                    <b-button @click="copyAllCppt($event, item)" variant="primary" class="btn-block mt-16">Copy All</b-button>
                                    <div class="card mt-16">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between">
                                                <div class="font-weight-bold">Subjective</div>
                                                <b-button @click="copySelected($event, copySubjective, item)" size="sm" variant="primary">Copy</b-button>
                                            </div>
                                        </div>
                                        <div class="card-body" style="font-size: 14px">
                                            <div>{{ item.subjective ? item.subjective : '-' }}</div>
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
                                            <div>{{ item?.objective || '-' }}</div>
                                        </div>
                                    </div>
                                    <!-- <div class="card mt-16">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between">
                                                <div class="font-weight-bold">Objective</div>
                                                <b-button @click="copySelected($event, copyObjective, item)" size="sm" variant="primary">Copy</b-button>
                                            </div>
                                        </div>
                                        <div class="card-body" style="font-size: 14px">
                                            <div>
                                                <b>Systolic: </b>{{ item.systolic_blood_pressure }},
                                                <b>Diastolic: </b>{{ item.diastolic_blood_pressure }},
                                                <b>Frekuensi Nadi: </b>{{ item.pulse_frequency }},
                                                <b>Frekuensi Nafas: </b>{{ item.breath_frequency }},
                                                <b>Saturasi Oksigen: </b>{{ item.oximetry_frequency }},
                                                <b>Suhu: </b>{{ item.temperature }},
                                                <b>Gula Darah: </b>{{ item.blood_sugar }},
                                            </div>
                                            <div class="mt-16">
                                                <b>Berat Badan: </b>{{ item.weight }},
                                                <b>Tinggi Badan: </b>{{ item.height }},
                                                <b>IMT: </b>{{ item.imt }},
                                                <b>Lingkar Kepala: </b>{{ item.head_circumference }},
                                                <b>Lingkar Dada: </b>{{ item.chest_circumference }},
                                                <b>Lingkar Lengan Atas: </b>{{ item.arm_circumference }},
                                                <b>Lingkar Perut: </b>{{ item.stomach_circumference }},
                                                <b>Lingkar Lutut: </b>{{ item.knee_circumference }},
                                            </div>
                                        </div>
                                    </div> -->
                                    <div class="card mt-16">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between">
                                                <div class="font-weight-bold">Assessment</div>
                                                <b-button @click="copySelected($event, copyAssesment, item)" size="sm" variant="primary">Copy</b-button>
                                            </div>
                                        </div>
                                        <div class="card-body" style="font-size: 14px">
                                            <div>
                                                <div class="font-weight-bold">Assessment</div>
                                                <div>{{ item.assessment ? item.assessment : '-' }}</div>
                                            </div>
                                            <div class="mt-16">
                                                <div><b>Diagnosa (ICD10): </b><span v-if="!item.icd10 || item.icd10.length == 0">-</span></div>
                                                <template v-if="item.icd10 && item.icd10.length > 0">
                                                    <li v-for="icd10 in item.icd10">{{ icd10.code }} {{ icd10.name }}</li>
                                                </template>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="card mt-16">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between">
                                                <div class="font-weight-bold">Penunjang</div>
                                                <!-- <b-button @click="copySelected($event, copyPenunjang, item)" size="sm" variant="primary">Copy</b-button> -->
                                            </div>
                                        </div>
                                        <div class="card-body" style="font-size: 14px">
                                            <div>
                                                <div>
                                                    <b>Laboratorium: </b>
                                                    <span v-if="!item.laboratory_request_items || item.laboratory_request_items.length == 0">-</span>
                                                </div>
                                                <template v-if="item.laboratory_request_items && item.laboratory_request_items.length > 0">
                                                    <li v-for="lab_list in item.laboratory_request_items">{{ lab_list.costing_name }}</li>
                                                </template>
                                            </div>
                                            <div class="mt-16">
                                                <div>
                                                    <b>Radiologi: </b>
                                                    <span v-if="!item.radiology_request_items || item.radiology_request_items.length == 0">-</span>
                                                </div>
                                                <template v-if="item.radiology_request_items && item.radiology_request_items.length > 0">
                                                    <li v-for="rad_list in item.radiology_request_items">{{ rad_list.costing_name }}</li>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card mt-16">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between">
                                                <div class="font-weight-bold">Resep</div>
                                                <template v-if="active.editAccess.prescription == true">
                                                    <b-button @click="copySelected($event, copyPrescription, item)" size="sm" variant="primary">Copy</b-button>                                                        
                                                </template>
                                            </div>
                                        </div>
                                        <div class="card-body" style="font-size: 14px">
                                            <template v-if="item.prescription && item.prescription.form_prescription && item.prescription.form_prescription.length > 0">
                                                <li v-for="iip in item.prescription.form_prescription">
                                                    {{ iip.pharmacy_catalog_name }}, {{ iip.qty }} {{ iip.package_name }}, {{ iip.used }}, {{ iip.desc }}
                                                </li>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="card mt-16">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between">
                                                <div class="font-weight-bold">Plan Non Farmakologis</div>
                                                <b-button @click="copySelected($event, copyInstruction, item)" size="sm" variant="primary">Copy</b-button>
                                            </div>
                                        </div>
                                        <div class="card-body" style="font-size: 14px">
                                            <div>{{ item.instructions ? item.instructions : '-' }}</div>
                                        </div>
                                    </div>
                                </b-card-body>
                            </b-collapse>
                        </b-card>
                    </div>
                </section>
            </div>
        </b-modal> 
        
        <b-modal v-model="active.modal_sbar" scrollable size="lg" title="SBAR" hide-footer>
            <div class="d-flex justify-content-between">
                <div class="d-flex">
                    <b-button @click="changeTab('menu1')" pill size="sm" :variant="active.tabSbar == 'menu1' ? 'primary' : 'outline-primary'" class="px-16 mr-2">List SBAR</b-button>
                    <b-button @click="changeTab('menu2')" pill  size="sm" :variant="active.tabSbar == 'menu2' ? 'primary' : 'outline-primary'" class="px-16">Tambah SBAR</b-button>
                </div>
                <template v-if="regData.doctor_in_charge_id == userData.employee_id">
                    <b-button @click="verifyAllSBARList()" pill variant="orange-500" size="sm" class="px-16">
                        <b-icon icon="patch-check-fill" /> <span>Verifikasi Semua</span>
                    </b-button>
                </template>
            </div>
            <section class="card mt-20">
                <div v-if="active.tabSbar == 'menu1'" class="card-body">
                    <b-table :fields="SBARFields" :items="sbar_list" bordered responsive style="font-size: 13px">
                        <template #cell(no)="row">
                            <div>{{ row.index+1 }}</div>
                        </template>
                        <template #cell(user)="row">
                            <div>{{ row.item.user_added_name ? row.item.user_added_name : '' }} ({{ row.item.user_added_role ? row.item.user_added_role : '' }})</div>
                            <div>{{ row.item.date_added ? row.item.date_added : '' }}</div>
                        </template>
                        <template #cell(content)="row">
                            <div>
                                <div>
                                    <div class="font-weight-bold">Situation</div>
                                    <div>{{ row.item.situation ? row.item.situation : '-' }}</div>
                                </div>
                                <div class="mt-12">
                                    <div class="font-weight-bold">Background</div>
                                    <div>{{ row.item.background ? row.item.background : '-' }}</div>
                                </div>
                                <div class="mt-12">
                                    <div class="font-weight-bold">Assessment</div>
                                    <div>{{ row.item.assessment ? row.item.assessment : '-' }}</div>
                                </div>
                                <div class="mt-12">
                                    <div class="font-weight-bold">Recommendation</div>
                                    <div>{{ row.item.recommendation ? row.item.recommendation : '-' }}</div>
                                </div>
                                <div class="mt-12">
                                    <div class="font-weight-bold">Advice</div>
                                    <div>{{ row.item.advice ? row.item.advice : '-' }}</div>
                                </div>                                
                            </div>
                        </template>
                        <template #cell(action)="row">
                            <div v-if="row.item.status == 1" class="d-flex">

                                <template v-if="row.item.user_added == userData.user_id">
                                    <b-button @click="toEditSbar(row.item)" size="sm" variant="warning-500" class="mr-2">
                                        <feather-icon icon="EditIcon" />
                                    </b-button> 
                                    <b-button @click="removeSbarList(row.item)" size="sm" variant="danger-700" class="mr-2">
                                        <feather-icon icon="TrashIcon" />
                                    </b-button>
                                </template>
                                <template v-if="row.item.doctor_in_charge_id == userData.employee_id">
                                    <b-button @click="verifySbarList(row.item)" size="sm" variant="orange-500" class="mr-2">Verifikasi</b-button>
                                </template>

                            </div>
                            <div v-else>Telah di verifikasi oleh DPJP</div>
                        </template>

                    </b-table>
                </div>

                <div v-if="active.tabSbar == 'menu2'">
                    <div class="card-body">
                        <div>
                            <label class="c-form-label">Siatuation</label>
                            <b-form-textarea rows="4" v-model="form_sbar.situation" autocomplete="off" ></b-form-textarea>
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">Background</label>
                            <b-form-textarea rows="4" v-model="form_sbar.background" autocomplete="off" ></b-form-textarea>
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">Assessment</label>
                            <b-form-textarea rows="4" v-model="form_sbar.assessment" autocomplete="off" ></b-form-textarea>
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">Recommendation</label>
                            <b-form-textarea rows="4" v-model="form_sbar.recommendation" autocomplete="off" ></b-form-textarea>
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">Advice</label>
                            <b-form-textarea rows="4" v-model="form_sbar.advice" autocomplete="off" ></b-form-textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-end">
                            <b-button :disabled="active.disabledSBAR" @click="validationSBAR" variant="primary">Simpan</b-button>
                        </div>
                    </div>
                </div>
            </section>            
        </b-modal>
        
    </div>
</template>
<script>
import { mapState } from 'vuex'
import patientMenu from '@/@core/components/tab-menu/patientMenu.vue';
import administration from '@/views/layouts/patient/doctor/administration.vue';
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import vSelect from 'vue-select'
import moment from 'moment'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
// import io from 'socket.io-client';

export default {
    name: 'DoctorStudies',
    components: {
        patientMenu, administration, ModernLabel, vSelect
    },
    data() {
        return {
            // socket : io(process.env.VUE_APP_SOCKET_IO, { transports: ["websocket", "polling", "flashsocket"],withCredentials: true }),
            userData: JSON.parse(localStorage.getItem('userData')),
            reg_des_id: '',
            urlbe:process.env.VUE_APP_IMAGE,
            is_soap_null: false,

            SBARFields: [
                { key: 'no', label: 'No', thStyle:'width: 50px'},
                { key: 'user', label: 'Profesional Pemberi Asuhan (PPA)', thStyle:'width: 200px' },
                { key: 'Content', label: 'SBAR', },
                { key: 'advice', label: 'Advice', },
                { key: 'action', label: 'Aksi', thStyle:'width: 200px'},
            ],
            PrescriptionTypeOptions:[
                { value: 1, title: 'Resep' },
                { value: 2, title: 'Resep Pulang' },
                { value: 4, title: 'DO' }
            ],
            DepoOptions:[
                { value: '297c44f0-12ef-40e6-b9a7-c831065b3372', title: 'APOTIK CENTRAL' },
                { value: '822d71e8-7a2b-4fc6-a372-40a6b458d8c3', title: 'FARMASI OK' },
            ],
            form_sbar: {
                id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                situation: '',
                background: '',
                assessment: '',
                recommendation: '',
                advice: '',
            },
            sbar_list: [],
            
            active: {
                modal_cppt: false,
                disabledCPPT: false,
                cppt_view: 'ADD',
                editAccess: {
                    laboratory: true,
                    radiology: true,
                    prescription: true,
                },
                modal_sbar: false,
                tabSbar: 'menu1',
                disabledSBAR: false,
                refcontrol: false,

            },
            imageview: '',
            radiologyResultField: [
                { key: 'no', label: 'No', thStyle:'width: 50px' },
                { key: 'expertise', label: 'Expertise', thStyle:'width: 700px' },
                { key: 'url', label: 'Gambar' },
                { key: 'action', label: 'Aksi' },
            ],
            vitalData: {},
            cppt_list: [],
            cppt_history: [],
            form_cppt: {
                id: '',
                doctor_in_charge_id: '',
                registration_destination_id: '',
                subjective: '',
                objective: '',
                plan: '',
                assessment: '',
                instructions: '',

                // systolic_blood_pressure: '',
                // diastolic_blood_pressure: '',
                // pulse_frequency: '',
                // breath_frequency: '',
                // oximetry_frequency: '',
                // temperature: '',
                // blood_sugar: '',
                // sore_score: '',
                // ews: '',
                // weight: '',
                // height: '',
                // imt: '',
                // head_circumference: '',
                // chest_circumference: '',
                // arm_circumference: '',
                // stomach_circumference: '',
                // knee_circumference: '',

                laboratory_request_items: [],
                radiology_request_items: [],

                form_prescription_parent: [],
                form_prescription: [],
                pharmacy_depo_id: '',
                type_prescription_id: 1,

                icd10: [],
                registration_id: '',
            },

            icd10_id:'',
            OptionsIcd10:[],
            supportPackage: {
                lab_package_list: [],
                rad_package_list: [],
            },
            prescription: '',
            catalog: '',
            OptionsCatalog:[],
            prescription_type: {},
            searchlab: '',
            searchrad: '',
            CPPTFields: [
                { key: 'no', label: 'No', thStyle:'width: 50px', thClass: 'align-middle' },
                { key: 'user', label: 'Profesional Pemberi Asuhan (PPA)', thStyle:'width: 200px', thClass: 'align-middle' },
                { key: 'content', label: 'CPPT', thClass: 'align-middle' },
                { key: 'instructions', label: 'Plan Non Farmakologis', thStyle:'width: 300px', thClass: 'align-middle' },
                { key: 'action', label: 'Aksi', thStyle:'width: 200px', thClass: 'align-middle' },
            ],

            regData: {}, 
            OptionsAnamesaType:[
                { value: 1, title: 'Auto Anamnesa' },
                { value: 2, title: 'Allo Anamnesa' }
            ],
            OptionsFamilyRelation:[
                { value: 1, title: 'Ayah' },
                { value: 2, title: 'Ibu' },
                { value: 3, title: 'Kakak' },
                { value: 4, title: 'Adik' },
            ],
            OptionsClassificationBloodPressureType:[
                { value: 0, title: '-' },
                { value: 1, title: 'Normal' },
                { value: 2, title: 'Prephypertension' },
                { value: 3, title: 'Stage 1 HTN' },
                { value: 4, title: 'Stage 2 HTN' },
            ],
            OptionsClassificationOximetryType:[
                { value: 0, title: '-' },
                { value: 1, title: 'Normal' },
                { value: 2, title: 'Hipoksia Ringan Sampai Sedang' },
                { value: 3, title: 'Hipoksia Sedang Sampai Berat' },
                { value: 4, title: 'Hipoksia Berat Mengancam Jiwa' },
            ],
            OptionsClassificationTemperatureType:[
                { value: 0, title: '-' },
                { value: 1, title: 'Normal' },
                { value: 2, title: 'Febris' },
                { value: 3, title: 'Hiperpireksis' },
                { value: 4, title: 'Hipotermi' },
                { value: 5, title: 'Hipertermi' },
                { value: 6, title: 'Hipotensi' },
            ],
            prescriptionCategories: [
                { id: 1, label: "Obat Satuan" },
                { id: 2, label: "Racik 1" },
                { id: 3, label: "Racik 2" },
                { id: 4, label: "Racik 3" },
                { id: 5, label: "Racik 4" },
            ],

            PrescriptionOptions:[
                { value: 1, title: 'OBAT SATUAN' },
                { value: 2, title: 'RACIK 1' },
                { value: 3, title: 'RACIK 2' },
                { value: 4, title: 'RACIK 3' },
                { value: 5, title: 'RACIK 4' }
            ], 

            soap_emergency: {
                id:'', 
                doctor_in_charge_id: '',
                doctor_in_charge_id_1: '',
                doctor_in_charge_id_2: '',
                doctor_in_charge_name: '',
                doctor_in_charge_name_1: '',
                doctor_in_charge_name_2: '',
                registration_destination_id: '',
                anamesa_type_id: 1, 
                family_relation_id: '',
                family_name: '',
                main_complaint: '',
                history_disease_now: '',
                history_disease_past: '',
                history_drugs: '',
                systolic_blood_pressure: '',
                diastolic_blood_pressure: '',
                pulse_frequency: '',
                breath_frequency: '',
                oximetry_frequency: '',
                temperature: '',
                blood_sugar: '',
                weight: '',
                height: '',
                imt: '',
                head_circumference: '',
                chest_circumference: '',
                arm_circumference: '',
                stomach_circumference: '',
                knee_circumference: '',
                sore_score: '',
                ews: '',
                soap_o_anythingelse: '',
                icd10: [],
                icd10_notes: '',
                non_icd10: [],
                soap_a_anythingelse: '',
                therapy_plan: '',
                head: 1,
                head_hair_color: 1,
                head_hair_condition: 1,
                eye_conjunctive: 1,
                eye_sclera: 1,
                eye_pupil: 1,
                eye_light_reflex: 1,
                mouth_mucosa: 1,
                mouth_discoloration: 1,
                mouth_tongue: 1,
                mouth_caries: 1,
                neck_kgb: 1,
                neck_trachea_position: 1,
                neck_massa: 1,
                thorax_insp_chest: 1,
                thorax_insp_retraction: 1,
                thorax_palp_chest: 1,
                thorax_palp_fremitus: 1,
                thorax_palp_crepitation: 1,
                thorax_ausk_pul_vasiculer: 1,
                thorax_ausk_pul_rhonki: 1,
                thorax_ausk_pul_wheezing: 1,
                thorax_ausk_cor_heart_sound: 1,
                thorax_ausk_cor_murmur: 1,
                thorax_ausk_cor_gallop: 1,
                abdomen_supel: 1,
                abdomen_bowel_sounds: 1,
                abdomen_tenderness: 1,
                abdomen_hepar_lien: 1,
                abdomen_other_physical_check: '',
                extremity_acral: 1,
                extremity_crt: 1,
                extremity_edema: 1,
                genitalia: '',
                localist_status: '',
                ophthal_status: '',
            },

            soap_inpatient: {
                id:'', 
                doctor_in_charge_id: '',
                registration_destination_id: '',
                anamesa_type_id: 1, 
                family_relation_id: '',
                family_name: '',
                main_complaint: '',
                history_disease_now: '',
                history_disease_past: '',
                history_drugs: '',
                allergies: [],
                systolic_blood_pressure: '',
                diastolic_blood_pressure: '',
                pulse_frequency: '',
                breath_frequency: '',
                oximetry_frequency: '',
                temperature: '',
                blood_sugar: '',
                sore_score: '',
                ews: '',
                weight: '',
                height: '',
                imt: '',
                head_circumference: '',
                chest_circumference: '',
                arm_circumference: '',
                stomach_circumference: '',
                knee_circumference: '',
                physical_check: '',
                soap_o_anythingelse:'',
                icd10: [],
                icd10_notes:'',
                non_icd10: [],
                soap_a_anythingelse: '',
                icd9cm: [],
                therapy_plan: '',
                transaction_doctor: [],
                laboratory_request_items: [],
                radiology_request_items: [],
                pharmacy_depo_id: '',
                physiotherapy_request_items: [],
                form_prescription_parent: [],
                form_prescription: [],
                advice: '',
                is_initial_inpatient: 1,
                estimated_duration_treatment: '',
                follow_up_care_arry: [],
                diet_and_nutrition_type_id: 0,
                diet_and_nutrition: '',
                wound_care_type_id: 0,
                wound_care: '',
                medical_rehabilitation_type_id: 0,
                medical_rehabilitation: '',
                pain_management_type_id: 0,
                pain_management: '',
                discharge_planning_other: '',

                head: 1,
                head_hair_color: 1,
                head_hair_condition: 1,
                eye_conjunctive: 1,
                eye_sclera: 1,
                eye_pupil: 1,
                eye_light_reflex: 1,
                mouth_mucosa: 1,
                mouth_discoloration: 1,
                mouth_tongue: 1,
                mouth_caries: 1,
                neck_kgb: 1,
                neck_trachea_position: 1,
                neck_massa: 1,
                thorax_insp_chest: 1,
                thorax_insp_retraction: 1,
                thorax_palp_chest: 1,
                thorax_palp_fremitus: 1,
                thorax_palp_crepitation: 1,
                thorax_ausk_pul_vasiculer: 1,
                thorax_ausk_pul_rhonki: 1,
                thorax_ausk_pul_wheezing: 1,
                thorax_ausk_cor_heart_sound: 1,
                thorax_ausk_cor_murmur: 1,
                thorax_ausk_cor_gallop: 1,
                abdomen_supel: 1,
                abdomen_bowel_sounds: 1,
                abdomen_tenderness: 1,
                abdomen_hepar_lien: 1,
                abdomen_other_physical_check: '',
                extremity_acral: 1,
                extremity_crt: 1,
                extremity_edema: 1,
                genitalia: '',
                localist_status: '',
                ophthal_status: '',
            },

            soap_outpatient: {
                id:'', 
                doctor_in_charge_id: '',
                registration_destination_id: '',
                anamesa_type_id: 1, 
                family_relation_id: '',
                family_name: '',
                main_complaint: '',
                history_disease_now: '',
                history_disease_past: '',
                history_drugs: '',
                allergies: [],
                systolic_blood_pressure: '',
                diastolic_blood_pressure: '',
                pulse_frequency: '',
                breath_frequency: '',
                oximetry_frequency: '',
                temperature: '',
                blood_sugar: '',
                sore_score: '',
                ews: '',
                weight: '',
                height: '',
                imt: '',
                head_circumference: '',
                chest_circumference: '',
                arm_circumference: '',
                stomach_circumference: '',
                knee_circumference: '',
                physical_check: '',
                soap_o_anythingelse:'',
                icd10: [],
                icd10_notes:'',
                non_icd10: [],
                soap_a_anythingelse: '',
                icd9cm: [],
                therapy_plan: '',
                transaction_doctor: [],
                laboratory_request_items: [],
                radiology_request_items: [],
                pharmacy_depo_id: '',
                physiotherapy_request_items: [],
                form_prescription_parent: [],
                form_prescription: [],
                advice: '',

                // HFII hemodialysis
                is_hemodialysis: 0,
                main_diagnosis: '',
                etiology: '',
                comorbidities: '',
                frequency_hd: '',
                vascular_access: '',

                // HFII medical_rehabilitation
                is_medical_rehabilitation: 0,
                functional_testing: '',
                suspected_disease_type_id: 0,
                suspected_disease: '',
                result: '',
                conclusion: '',
                recommendation: '',
                functional_diagnosis: '',
                medical_diagnosis: '',

                head: 1,
                head_hair_color: 1,
                head_hair_condition: 1,
                eye_conjunctive: 1,
                eye_sclera: 1,
                eye_pupil: 1,
                eye_light_reflex: 1,
                mouth_mucosa: 1,
                mouth_discoloration: 1,
                mouth_tongue: 1,
                mouth_caries: 1,
                neck_kgb: 1,
                neck_trachea_position: 1,
                neck_massa: 1,
                thorax_insp_chest: 1,
                thorax_insp_retraction: 1,
                thorax_palp_chest: 1,
                thorax_palp_fremitus: 1,
                thorax_palp_crepitation: 1,
                thorax_ausk_pul_vasiculer: 1,
                thorax_ausk_pul_rhonki: 1,
                thorax_ausk_pul_wheezing: 1,
                thorax_ausk_cor_heart_sound: 1,
                thorax_ausk_cor_murmur: 1,
                thorax_ausk_cor_gallop: 1,
                abdomen_supel: 1,
                abdomen_bowel_sounds: 1,
                abdomen_tenderness: 1,
                abdomen_hepar_lien: 1,
                abdomen_other_physical_check: '',
                extremity_acral: 1,
                extremity_crt: 1,
                extremity_edema: 1,
                genitalia: '',
                localist_status: '',
                ophthal_status: '',
                
                // conditional but important
                health_facility_item_id: '',
            }
            
        }
    },

    computed: {
        ...mapState({ 
            // items: state => state.soap.items, 
            OptionsCosting: state => state.costing.options.costing,
            
            // itemsLaboratory: state => state.laboratory.package.items,
            // itemsRadiology: state => state.radiology.package.items,
            PackageOptions: state => state.packages.options.packages,

            OptionsDoctor: state => state.employee.options.employee,            
            OptionsSpecialistDoctor: state => state.employee.options.specialist,            

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
            return this.supportPackage?.rad_package_list.map((rad) => {
                const filteredCategoryItems = rad?.category_items?.filter((item) =>
                    item.costing_name.toLowerCase().includes(this.searchrad?.toLowerCase())
                );

                return {
                    ...rad,
                    category_items: filteredCategoryItems,
                };
            }).filter(rad => rad?.category_items?.length > 0); 
        },
        // filteredListLab() {
        //     return this.itemsLaboratory.filter((lab) => {
        //         const computedLabObj = { ...lab}
        //         return Object.keys(computedLabObj)
        //             .some(key => ('' + computedLabObj[key]).toLowerCase().includes(this.searchlab.toLowerCase()))
        //     })
        // },
        // filteredListRad() {
        //     return this.itemsRadiology.filter((rad) => {
        //         const computedRadObj = { ...rad}
        //         return Object.keys(computedRadObj)
        //             .some(key => ('' + computedRadObj[key]).toLowerCase().includes(this.searchrad.toLowerCase()))
        //     })
        // },

    },

    
    methods: {
        createEmptySoap() {
            const emergency_id = "444b3134-5b75-4b67-af72-a558d46716d7"
            const inpatient_id = "40f06534-07ec-4221-ad9d-dea08196079f"
            const outpatient_id = "3cb626f5-9148-4fb1-8663-3d7f028b01a6"

            if (this.regData.health_facility_id = inpatient_id) {
                this.soap_inpatient.registration_destination_id = this.regData.registration_destination_id
                this.soap_inpatient.doctor_in_charge_id = this.regData.doctor_in_charge_id
                try {
                    this.$store.dispatch('soap/formPostPut', this.soap_inpatient).then(res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        location.reload()
                    }, 
                    err => {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    })
                } catch (error) {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    console.error('Error at: ', error);
                }
            } else if(this.regData.health_facility_id = emergency_id) {
                this.soap_emergency.registration_destination_id = this.regData.registration_destination_id
                this.soap_emergency.doctor_in_charge_id = this.regData.doctor_in_charge_id

                try {
                    this.$store.dispatch('soap/formPostPutDoang', this.soap_emergency).then((response) => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        location.reload()
                    })
                } catch (error) {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    console.error('Error at: ', error)
                }
            } else if (this.regData.health_facility_id = outpatient_id) {
                this.soap_outpatient.registration_destination_id = this.regData.registration_destination_id
                this.soap_outpatient.doctor_in_charge_id = this.regData.doctor_in_charge_id

                if (this.regData.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e') {
                    this.soap_outpatient.is_hemodialysis = 1
                }

                if (this.regData.health_facility_item_id == '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a') {
                    this.soap_outpatient.is_medical_rehabilitation = 1
                }

                try {
                    this.$store.dispatch('soap/formPostPut', this.soap_outpatient).then(res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        location.reload()
                    }, 
                    err => {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    })
                } catch (error) {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    console.error('Error at: ', error);
                }                
            }
            // console.log('going here');
        },

        parsedBody(text){
            return String(text || '-').replace(/\n/g, "<br>");
        },
        isRefcontrol(url,data) {
            if (data) {
                this.imageview = data
            }
            this.active.refcontrol = !this.active.refcontrol
        },
        blankToPrint(obj){
            const url = this.$router.resolve({ path: '/radiology/list/process/print-expertise/'+ obj.radiology_request_id + '/' + obj.radiology_request_item_id + '/' + obj.image_id }).href;
            window.open(url, '_blank');
        },
        expandImage(url){
            const param = this.urlbe + url;
            const newWindow = window.open('', '_blank');
            
            newWindow.document.write(`
                <html>
                    <head>
                        <title>Image Preview</title>
                        <style>
                            body, html {
                                margin: 0;
                                padding: 0;
                                height: 100%;
                                width: 100%;
                            }
                            img {
                                width: 100%;
                                height: auto;
                            }
                        </style>
                    </head>
                    <body>
                        <img src="${param}" alt="Image Preview">
                    </body>
                </html>
            `);
            newWindow.document.close();
        },
        localDate(input_date) {
            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },
        openModalSBAR () {
            this.active.modal_sbar = true
        },

        changeTab(tab){
            this.resetSbarForm()
            this.active.tabSbar = tab

            if(this.active.tabSbar == 'menu1') {
                this.form_sbar.id = ''
            }
        },

        validationSBAR(){            
            try {
                this.active.disabledSBAR = true
                this.$store.dispatch('sbar/postPutSBAR', this.form_sbar).then((response) => {
                    this.active.disabledSBAR = false
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.getSBARData(this.form_sbar.registration_destination_id, this.form_sbar.doctor_in_charge_id)      
                    this.active.tabSbar = 'menu1'
                    this.resetSbarForm()
                })
                
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                this.active.disabledSBAR = false
                console.error('Error at: ', error)

            }
            
        },

        resetSbarForm(){
            this.form_sbar.situation = ''
            this.form_sbar.background = ''
            this.form_sbar.assessment = ''
            this.form_sbar.recommendation = ''
            this.form_sbar.advice = ''
        },

        getSBARData(registration_destination_id, doctor_in_charge_id){
            const param = { registration_destination_id: registration_destination_id, doctor_in_charge_id: doctor_in_charge_id }
            this.$store.dispatch('sbar/getListSBAR', param).then((data) => {
                this.sbar_list = data
            })
        },

        removeSbarList(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus SBAR ${obj.date_updated} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                this.$store.dispatch('sbar/removeSBAR', obj.id)
                    .then(response => {
                        if (response.status === 200) {
                            this.getSBARData(this.form_sbar.registration_destination_id, this.form_sbar.doctor_in_charge_id)      
                            this.notification('Success','CheckIcon','success','You have successfully')
                        }
                        return response;
                    })
                    .catch(({error, response}) => {
                        // console.log(error);
                        // console.log(response);
                        this.notification('Failed','CloseIcon','danger',`${error}`)

                    })
                }
            })
        },

        toEditSbar(obj){
            this.active.tabSbar = 'menu2'
            
            this.form_sbar.id = obj.id
            this.form_sbar.registration_destination_id = obj.registration_destination_id
            this.form_sbar.doctor_in_charge_id = obj.doctor_in_charge_id
            this.form_sbar.situation = obj.situation
            this.form_sbar.background = obj.background
            this.form_sbar.assessment = obj.assessment
            this.form_sbar.recommendation = obj.recommendation
            this.form_sbar.advice = obj.advice
        },

        
        verifySbarList (obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin verifikasi SBAR ${obj.date_updated} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('sbar/verifySBAR', obj.id)
                    .then(response => {
                        if (response.status === 200) {
                            this.getSBARData(this.form_sbar.registration_destination_id, this.form_sbar.doctor_in_charge_id)      
                            this.notification('Success','CheckIcon','success','You have successfully')
                        }
                    },
                    error => {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    }) 
                }
            })
        },

        verifyAllSBARList () {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin verifikasi SEMUA LIST SBAR pasien ini ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('sbar/verifyAllSBAR', this.form_sbar.registration_destination_id)
                    .then(response => {
                        if (response.status === 200) {
                            this.getSBARData(this.form_sbar.registration_destination_id, this.form_sbar.doctor_in_charge_id)      
                            this.notification('Success','CheckIcon','success','You have successfully')
                        } else {
                            this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        }
                    },
                    error => {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    }) 
                }
            })
        },



        localDateTime(input) {
            return moment(input).locale("id").format('DD MMMM YYYY HH:MM')
        },
        imtChanged(){
            let brt_badan = 0
            if (this.form_cppt.weight) {
                brt_badan = parseFloat(this.form_cppt.weight);
            } else {
                brt_badan = 0
            }

            let tng_badan = 0
            if (this.form_cppt.height) {
                tng_badan = parseFloat(this.form_cppt.height);
            } else {
                tng_badan = 0
            }

            let imt = 0.0;    

            if (brt_badan && tng_badan) {
                imt = tng_badan * tng_badan;
                imt = (brt_badan/imt)*100*100;
                this.form_cppt.imt = imt.toFixed(1);
            }
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

            if (this.form_cppt.icd10.some(item => item.icd10_id === event.value)) {
                this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list ICD10 yang sama')
            } else {
                this.form_cppt.icd10.push({ icd10_id:event.value, title:event.title, value:event.value, action: 'action'})
            }

        },
        delete10 (index) {
            this.form_cppt.icd10.splice(index, 1)
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
        deletePRES (index) {
            this.form_cppt.form_prescription.splice(index, 1)
        },
        onSearchCatalog(search, loading) {
            if(search.length >= 3) {
                loading(true);
                this.$store.dispatch('catalog/getParamSearch', search).then(data => {
                    this.OptionsCatalog = []
                    for (let i = 0; i < data.length; i++) {
                        const option = []
                        for (const key in data[i]) {
                            if (key === 'id') { option['value'] = data[i][key] } else if (key === 'is_delete') { option['data_catalog'] = data[i] } else if (key === 'name') { option['title'] = data[i][key] } else if (key === 'id') { option['oid'] = data[i][key] }
                        }
                        this.OptionsCatalog.push(Object.assign({}, option))
                    }
                    loading(false)
                })
            }
        },
        onAddPRES (event) {
            this.prescription_type = event
            let index = this.form_cppt.form_prescription_parent.findIndex((item) => item.id === event.value);
            if (index === -1) {
                this.form_cppt.form_prescription_parent.push({
                    id:event.value,
                    // type_prescription_id: this.type_prescription_id,
                    prescription:event.title,
                    used:'',
                    value:0
                }) 
            } else {
                console.log("Object already exists");
            }
        },
        onAddCAT (event) {
            // localStorage.setItem("soap", JSON.stringify(this.form_cppt))
            this.catalog = ''
            this.collation = ''
            if (this.prescription === '') {
                alert("Pilih Racikan!");
            } else {
                this.form_cppt.form_prescription.push(
                    { 
                        code:event.data_catalog.code,
                        name:event.data_catalog.name,
                        pharmacy_catalog_id:event.data_catalog.id, 
                        package_id:event.data_catalog.package_id,
                        dose:event.data_catalog.dose,
                        sum_dose:event.data_catalog.dose+' '+event.data_catalog.dose_name, 
                        package_name:event.data_catalog.package_name,
                        prescription_type_id:this.prescription_type.value,
                        prescription_type_name:this.prescription_type.title,
                        value_doctor:0,
                        value_package:0,
                        used:'',
                        desc:'',
                        sort:this.form_cppt.form_prescription.length, 
                    }
                )
                // console.log(this.form_cppt.form_prescription)
            }
        },

        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        getData (id) {
			this.$store.dispatch('registration/getById', id).then(data => {
                this.regData = data[0]

                // CPPT
                this.form_cppt.registration_destination_id = this.regData.registration_destination_id
                this.form_cppt.doctor_in_charge_id = this.regData.doctor_in_charge_id
                this.getCPPTList(this.regData.registration_id)     
                this.getCPPTHistory(this.regData.norm)     

                // SBAR
                this.form_sbar.registration_destination_id = this.regData.registration_destination_id
                this.form_sbar.doctor_in_charge_id = this.regData.doctor_in_charge_id
                this.getSBARData(this.form_sbar.registration_destination_id, this.form_sbar.doctor_in_charge_id)      
                
                const emergency_id = '444b3134-5b75-4b67-af72-a558d46716d7'
                const inpatient_id = '40f06534-07ec-4221-ad9d-dea08196079f' 
                const icu_item_id = '6c7d7e53-ff24-4246-85b2-07ea77ac801f'
                const depo_igd_id = '4105d8bc-5769-463e-bc67-aae2fc5adf10'
                const depo_central_id = '297c44f0-12ef-40e6-b9a7-c831065b3372'

                if (this.regData.health_facility_id != inpatient_id && this.regData.health_facility_item_id != icu_item_id) {
                    if (this.regData.health_facility_id === emergency_id) { 
                        this.form_cppt.pharmacy_depo_id = depo_igd_id
    
                    } else {
                        this.form_cppt.pharmacy_depo_id = depo_central_id
                    }
                } else {
                    this.form_cppt.pharmacy_depo_id = '297c44f0-12ef-40e6-b9a7-c831065b3372'
                }

                this.getSoap()
                this.getVitalDetail(this.regData.registration_destination_id)
            })
        },
        
        // getNurse(reg_des_id){
        //     this.$store.dispatch('nurse/getAllResolve', reg_des_id).then(data => {
        //         let nurse = data[0]
        //         if (nurse) {
        //             this.form_cppt.systolic_blood_pressure = nurse.systolic_blood_pressure 
        //             this.form_cppt.diastolic_blood_pressure = nurse.diastolic_blood_pressure 
        //             this.form_cppt.pulse_frequency = nurse.pulse_frequency 
        //             this.form_cppt.breath_frequency = nurse.breath_frequency 
        //             this.form_cppt.oximetry_frequency = nurse.oximetry_frequency 
        //             this.form_cppt.temperature = nurse.temperature 
        //             this.form_cppt.blood_sugar = nurse.blood_sugar
        //             this.form_cppt.sore_score = nurse.sore_score
        //             this.form_cppt.ews = nurse.ews
        //             this.form_cppt.weight = nurse.weight
        //             this.form_cppt.height = nurse.height
        //             this.form_cppt.imt = nurse.imt
        //             this.form_cppt.head_circumference = nurse.head_circumference
        //             this.form_cppt.chest_circumference = nurse.chest_circumference
        //             this.form_cppt.arm_circumference = nurse.arm_circumference
        //             this.form_cppt.stomach_circumference = nurse.stomach_circumference
        //             this.form_cppt.knee_circumference = nurse.knee_circumference
        //         }
        //     })
        // },

        getVitalDetail(reg_des_id){
            let vital_param = {
                offset: 0,
                limit: 5,
                registration_destination_id: reg_des_id,
            }

            try {                
                this.$store.dispatch('vitalsign/getListVitalSign', vital_param).then((response) => {
                    this.vitalData = (response && response.rows && response.rows[0]) || {}
                    // console.log('this.vitalData', this.vitalData);
                }, error => {
                    console.error('Error while GET vitalsign/getListVitalSign at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET vitalsign/getListVitalSign at: ', error);
            }
        },

        assignVitalToObjective() {
            let obj = this.vitalData


            if (this.regData.birth_date && this.regData.birth_date != '0000-00-00') {
                const birthDate = moment(this.regData.birth_date, 'YYYY-MM-DD');
                const currentDate = moment();
                const years = currentDate.diff(birthDate, 'years');
                birthDate.add(years, 'years');
    
                if (years < 17) {
                    let text = `Berat Badan: ${obj?.weight || 0} kg,\nTinggi Badan: ${obj?.height || 0} cm,\nFrekuensi Nadi: ${obj?.pulse || 0} x/mnt,\nSuhu: ${obj?.temperature || 0} ᵒC,\nFrekuensi Nafas: ${obj?.respiratory_rate || 0} x/mnt,\nSaturasi Oksigen: ${obj?.oxygen_saturation || 0} x/mnt,\nSkala Nyeri: ${obj?.sore_score || 0},\nEWS: ${obj?.ews || 0}`
                    this.form_cppt.objective = text
                } else {
                    let text = `Berat Badan: ${obj?.weight || 0} kg,\nTinggi Badan: ${obj?.height || 0} cm,\nSistolik: ${obj?.systolic || 0} mmHg,\nDiastolik: ${obj?.diastolic || 0} mmHg,\nFrekuensi Nadi: ${obj?.pulse || 0} x/mnt,\nSuhu: ${obj?.temperature || 0} ᵒC,\nFrekuensi Nafas: ${obj?.respiratory_rate || 0} x/mnt,\nSaturasi Oksigen: ${obj?.oxygen_saturation || 0} x/mnt,\nSkala Nyeri: ${obj?.sore_score || 0},\nEWS: ${obj?.ews || 0}`
                    this.form_cppt.objective = text
                }
            } else {
                let text = `Berat Badan: ${obj?.weight || 0} kg,\nTinggi Badan: ${obj?.height || 0} cm,\nSistolik: ${obj?.systolic || 0} mmHg,\nDiastolik: ${obj?.diastolic || 0} mmHg,\nFrekuensi Nadi: ${obj?.pulse || 0} x/mnt,\nSuhu: ${obj?.temperature || 0} ᵒC,\nFrekuensi Nafas: ${obj?.respiratory_rate || 0} x/mnt,\nSaturasi Oksigen: ${obj?.oxygen_saturation || 0} x/mnt,\nSkala Nyeri: ${obj?.sore_score || 0},\nEWS: ${obj?.ews || 0}`
                this.form_cppt.objective = text
            }
        },

        getSoap() {
            try {
                this.$store.dispatch('soap/getDetail', {registration_destination_id: this.regData.registration_destination_id}).then((response) => {
                    if(response.length == 0) {
                        this.is_soap_null = true
                    } else {
                        this.is_soap_null = false
                    }
                }, err => {
                    console.error('Error while GET data from soap/getDetail at: ', err)
                })
            } catch (error) {
                console.error('Error while GET data from soap/getDetail at: ', err)
            }
        },

        openModalCPPT(view, data){
            this.active.modal_cppt = !this.active.modal_cppt
            this.resetCpptForm()
            this.active.cppt_view = view

            // clear prescription before add or GET data for edit
            this.form_cppt.form_prescription = []
            this.form_cppt.form_prescription_parent = []
            this.prescription = ''
            this.catalog = ''

            if (view == "EDIT") {
                if (data != null) {
                    this.fillCpptForm(data)

                    // handling if request is processed or not
                    // if (data.laboratory_request) {
                    //     if (data.laboratory_request.status == 2 || data.laboratory_request.status == 3 ) {
                    //         this.active.editAccess.laboratory = false
                    //     } else {
                    //         this.active.editAccess.laboratory = true
                    //     }
                    // }

                    // if (data.radiology_request) {
                    //     if (data.radiology_request.status == 2 || data.radiology_request.status == 3 ) {
                    //         this.active.editAccess.radiology = false
                    //     } else {
                    //         this.active.editAccess.radiology = true
                    //     }
                    // }
                    
                }

            } else {
                this.assignVitalToObjective()
                // this.getNurse(this.reg_des_id)
                console.log(this.cppt_history)
                if (this.cppt_history.length > 0) {
                    const mappedIcd10 = this.cppt_history[0].icd10.map(item => {
                        return {
                            icd10_id: item.icd10_id,
                            value: item.icd10_id,
                            title: `${item.code} ${item.name}`, 
                            action: "action"
                        };
                    });                
                    this.form_cppt.icd10 = mappedIcd10;
                } 
                this.active.editAccess.laboratory = true
                this.active.editAccess.radiology = true
                this.active.editAccess.prescription = true
            }

        },
        
        validationCPPT() {
            // console.log(this.form_cppt)
            let catalog_used_null = []

            if (this.form_cppt.form_prescription.length > 0){
                if (this.form_cppt.type_prescription_id) {
                    if (this.form_cppt.pharmacy_depo_id) {
                        for (let fpi = 0; fpi < this.form_cppt.form_prescription.length; fpi++) {
                            if (this.form_cppt.form_prescription[fpi].used === "" && this.form_cppt.form_prescription[fpi].prescription_type_id == 1) {
                                catalog_used_null.push(this.form_cppt.form_prescription[fpi].name)
                            }
                        }
                        if(catalog_used_null.length > 0) {
                            alert('Katalog '+catalog_used_null+' aturan pakai masih kosong!')
                        } else {
                            try {
                                this.active.disabledCPPT = true
                                this.$store.dispatch('cppt/postPutFullCPPT', this.form_cppt)
                                .then((response) => {
                                    this.notification('Success','CheckIcon','success','You have successfully')
                                    // this.socket.emit('SEND_LAB', []);
                                    // this.socket.emit('SEND_RAD', []);
                                    this.getCPPTList(this.regData.registration_id) 
                                    this.active.modal_cppt = false
                                    this.active.disabledCPPT = false
                                    
                                    this.resetCpptForm()
                                }) 
                            } catch (error) {
                                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                                this.active.disabledCPPT = false
                                console.error('Error at: ', error)
                            }
                        } 
                    } else {
                        this.notification('Warning','CloseIcon','warning','Tujuan Order wajib diisi')
                    }
                } else {
                    this.notification('Warning','CloseIcon','warning','Tipe resep wajib diisi')
                }
            } else {
                try {
                    this.active.disabledCPPT = true
                    this.$store.dispatch('cppt/postPutFullCPPT', this.form_cppt)
                    .then((response) => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.getCPPTList(this.regData.registration_id) 
                        this.active.modal_cppt = false
                        this.active.disabledCPPT = false
                        
                        this.resetCpptForm()
                    }) 
                } catch (error) {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    this.active.disabledCPPT = false
                    console.error('Error at: ', error)
                }
            }
        },
        resetCpptForm(){
            this.form_cppt.id = ''
            this.form_cppt.subjective = ''
            this.form_cppt.objective = ''
            this.form_cppt.plan = ''
            this.form_cppt.assessment = ''
            this.form_cppt.instructions = ''
            // this.form_cppt.systolic_blood_pressure = ''
            // this.form_cppt.diastolic_blood_pressure = ''
            // this.form_cppt.pulse_frequency = ''
            // this.form_cppt.breath_frequency = ''
            // this.form_cppt.oximetry_frequency = ''
            // this.form_cppt.temperature = ''
            // this.form_cppt.blood_sugar = ''
            // this.form_cppt.sore_score = ''
            // this.form_cppt.ews = ''
            // this.form_cppt.weight = ''
            // this.form_cppt.height = ''
            // this.form_cppt.imt = ''
            // this.form_cppt.head_circumference = ''
            // this.form_cppt.chest_circumference = ''
            // this.form_cppt.arm_circumference = ''
            // this.form_cppt.stomach_circumference = ''
            // this.form_cppt.knee_circumference = ''
            this.form_cppt.laboratory_request_items = []
            this.form_cppt.radiology_request_items = []
            this.form_cppt.form_prescription_parent = []
            this.form_cppt.form_prescription = []
            // this.form_cppt.icd10 = []
        },

        fillCpptForm(obj){
            // console.log('obj: ', obj);
            this.form_cppt.registration_destination_id = obj.registration_destination_id
            this.form_cppt.doctor_in_charge_id = obj.doctor_in_charge_id
            this.form_cppt.id = obj.id
            this.form_cppt.subjective = obj.subjective
            this.form_cppt.objective = obj.objective
            this.form_cppt.plan = obj.plan
            this.form_cppt.assessment = obj.assessment
            this.form_cppt.instructions = obj.instructions
            // this.form_cppt.systolic_blood_pressure = obj.systolic_blood_pressure
            // this.form_cppt.diastolic_blood_pressure = obj.diastolic_blood_pressure
            // this.form_cppt.pulse_frequency = obj.pulse_frequency
            // this.form_cppt.breath_frequency = obj.breath_frequency
            // this.form_cppt.oximetry_frequency = obj.oximetry_frequency
            // this.form_cppt.temperature = obj.temperature
            // this.form_cppt.blood_sugar = obj.blood_sugar
            // this.form_cppt.sore_score = obj.sore_score
            // this.form_cppt.ews = obj.ews
            // this.form_cppt.weight = obj.weight
            // this.form_cppt.height = obj.height
            // this.form_cppt.imt = obj.imt
            // this.form_cppt.head_circumference = obj.head_circumference
            // this.form_cppt.chest_circumference = obj.chest_circumference
            // this.form_cppt.arm_circumference = obj.arm_circumference
            // this.form_cppt.stomach_circumference = obj.stomach_circumference
            // this.form_cppt.knee_circumference = obj.knee_circumference

            if (obj.laboratory_request) {
                this.active.editAccess.laboratory = obj.laboratory_request.status == 2 || obj.laboratory_request.status == 3 ? false : true
            }

            if (obj.laboratory_request_items) {
                const mappedLab = obj.laboratory_request_items.map(item => {
                    return {
                        costing_name: item.costing_name,
                        laboratory_package_id: item.laboratory_package_id,
                        service_class_id: item.service_class_id
                    };
                });                
                this.form_cppt.laboratory_request_items = mappedLab;
            } else {
                this.form_cppt.laboratory_request_items = []
            }
            
            if (obj.radiology_request) {
                this.active.editAccess.radiology = obj.radiology_request.status == 2 || obj.radiology_request.status == 3 ? false : true
            }

            if (obj.radiology_request_items) {
                const mappedRad = obj.radiology_request_items.map(item => {
                    return {
                        costing_name: item.costing_name,
                        radiology_package_id: item.radiology_package_id,
                        service_class_id: item.service_class_id
                    };
                });                
                this.form_cppt.radiology_request_items = mappedRad;
            } else {
                this.form_cppt.radiology_request_items = []
            }
                    
            // this.form_cppt.pharmacy_depo_id = obj.prescription && obj.prescription.pharmacy_depo_id ? obj.prescription.pharmacy_depo_id : '297c44f0-12ef-40e6-b9a7-c831065b3372'
            const emergency_id = '444b3134-5b75-4b67-af72-a558d46716d7'
            const inpatient_id = '40f06534-07ec-4221-ad9d-dea08196079f' 
            const icu_item_id = '6c7d7e53-ff24-4246-85b2-07ea77ac801f'
            const depo_igd_id = '4105d8bc-5769-463e-bc67-aae2fc5adf10'
            const depo_central_id = '297c44f0-12ef-40e6-b9a7-c831065b3372'

            if (this.regData.health_facility_id != inpatient_id && this.regData.health_facility_item_id != icu_item_id) {
                this.form_cppt.pharmacy_depo_id = this.regData.health_facility_id === emergency_id ? depo_igd_id : depo_central_id
                // if (this.regData.health_facility_id === emergency_id) { 
                //     this.form_cppt.pharmacy_depo_id = depo_igd_id
                // } else {
                //     this.form_cppt.pharmacy_depo_id = depo_central_id
                // }
            } else {
                this.form_cppt.pharmacy_depo_id = obj.prescription?.pharmacy_depo_id || depo_central_id
            }

            // if (obj.icd10) {
            //     const mappedIcd10 = obj.icd10.map(item => {
            //         return {
            //             icd10_id: item.icd10_id,
            //             value: item.icd10_id,
            //             title: `${item.code} ${item.name}`, 
            //             action: "action"
            //         };
            //     });                
            //     this.form_cppt.icd10 = mappedIcd10;
            // } 
            
            // console.log('obj.prescription: ', obj.prescription);
            // console.log('this.form_cppt.pharmacy_depo_id: ', this.form_cppt.pharmacy_depo_id);
            this.form_cppt.type_prescription_id = obj.prescription && obj.prescription.type_prescription_id ? obj.prescription.type_prescription_id : 1
            let var_presc = obj.prescription && obj.prescription.form_prescription ? obj.prescription.form_prescription : []
            let var_parent = obj.prescription && obj.prescription.form_prescription_parent ? obj.prescription.form_prescription_parent : []
            
            let presc_status = ''
            presc_status = obj.prescription ? obj.prescription.flag : 0
            this.active.editAccess.prescription = presc_status == 0 ? true : false
            // if (presc_status == 0) {
            //     this.active.editAccess.prescription = true
            // } else {
            //     this.active.editAccess.prescription = false
            // }
            // console.log('var_parent', var_parent);
            if (var_parent.length > 0) {
                const mappedPrescParent = var_parent.map(item => {
                    let name = ""
                    if (item.id == 1) {
                        name = 'OBAT SATUAN'
                    } else if (item.id == 2) {
                        name = 'RACIK 1'
                    } else if (item.id == 3) {
                        name = 'RACIK 2'
                    } else if (item.id == 4) {
                        name = 'RACIK 3'
                    } else if (item.id == 5) {
                        name = 'RACIK 4'
                    } else {
                        name = '-'
                    }
                    return {
                        id: item.id,
                        used: item.used,
                        value: item.value,
                        prescription: name,
                    };
                });                
                this.form_cppt.form_prescription_parent = mappedPrescParent;
            } else {
                this.form_cppt.form_prescription_parent = []
            }
            console.log(this.form_cppt.form_prescription_parent);

            if (var_presc) {
                const mappedPresc = var_presc.map(item => {
                    let name = ""
                    if (item.id == 1) {
                        name = 'OBAT SATUAN'
                    } else if (item.id == 2) {
                        name = 'RACIK 1'
                    } else if (item.id == 3) {
                        name = 'RACIK 2'
                    } else if (item.id == 4) {
                        name = 'RACIK 3'
                    } else if (item.id == 5) {
                        name = 'RACIK 4'
                    } else {
                        name = '-'
                    }
                    return {
                        pharmacy_catalog_id: item.pharmacy_catalog_id, 
                        prescription_type_id: item.prescription_item_type_id,
                        package_id: item.package_id,
                        package_name: item.package_name,
                        dose: item.dose,
                        sum_dose: item.dose+' '+ item.dose_name, 
                        value_package: item.qty,
                        sort: item.sort, 
                        used: item.used,
                        desc: item.desc,

                        code: item.pharmacy_catalog_code,
                        name: item.pharmacy_catalog_name,
                        value_doctor: item.qty_doctor,
                        prescription_type_name: name,
                    };
                });      
                this.form_cppt.form_prescription = mappedPresc;                                  
            } else {
                this.form_cppt.form_prescription = [];                                  
            }

        },
        
        getCPPTList(registration_id) {
            const param = { registration_id: registration_id }

            try {                
                this.$store.dispatch('cppt/getListCPPT', param).then((response) => {
                    this.cppt_list = (response && response.rows) || []
                }, error => {
                    console.error('Error while cppt/getListCPPT at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET cppt/getListCPPT at: ', error);
            }
        },

        getCPPTHistory(norm) {
            const param = { norm: norm }

            try {                
                this.$store.dispatch('cppt/getListCPPT', param).then((response) => {
                    this.cppt_history = response
                }, error => {
                    console.error('Error while cppt/getListCPPT at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET cppt/getListCPPT at: ', error);
            }
        },
        
        verifyCpptList (obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin verifikasi CPPT ${obj.date_updated} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('cppt/verifyCPPT', obj.id)
                    .then(response => {
                        if (response.status === 200) {
                            this.getCPPTList(this.regData.registration_id) 
                            this.notification('Success','CheckIcon','success','You have successfully')
                        } else {
                            this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        }
                    },
                    error => {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    }) 
                }
            })
        },

        verifyAllList () {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin verifikasi SEMUA LIST CPPT pasien ini ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('cppt/verifyAllCPPT', this.cppt_list[0].soap_id)
                    .then(response => {
                        if (response.status === 200) {
                            this.getCPPTList(this.regData.registration_id) 
                            this.notification('Success','CheckIcon','success','You have successfully')
                        } else {
                            this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        }
                    },
                    error => {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    }) 
                }
            })
        },
        removeCpptList (obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus CPPT ${obj.date_updated} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                this.$store.dispatch('cppt/removeCPPT', obj.id)
                    .then(response => {
                        if (response.status === 200) {
                            this.getCPPTList(this.regData.registration_id) 
                            this.notification('Success','CheckIcon','success','You have successfully')
                        }
                        return response;
                    })
                    .catch(({error, response}) => {
                        // console.log(error);
                        // console.log(response);
                        this.notification('Failed','CloseIcon','danger',`${error}`)

                    })
                }
            })
        },

        copyAllCppt(event, obj) {
            const button = event.target;
            button.innerText = 'Copied All';
            
            // func(obj);
            this.copySubjective(obj)
            this.copyObjective(obj)
            this.copyAssesment(obj)
            this.copyInstruction(obj)
            this.copyPrescription(obj)

            setTimeout(() => {
                button.innerText = 'Copy All';
            }, 1000);
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
            this.form_cppt.subjective = obj.subjective
        },

        copyObjective(obj) {            
            this.form_cppt.objective = obj.objective
        },

        copyAssesment(obj) {            
            this.form_cppt.assessment = obj.assessment

            // if (obj.icd10) {
            //     const mappedIcd10 = obj.icd10.map(item => {
            //         return {
            //             icd10_id: item.icd10_id,
            //             value: item.icd10_id,
            //             title: `${item.code} ${item.name}`, 
            //             action: "action"
            //         };
            //     });                
            //     this.form_cppt.icd10 = mappedIcd10;
            // }
        },

        copyInstruction(obj) {            
            this.form_cppt.instructions = obj.instructions
        },

        copyPrescription(obj) {

            if (obj.prescription) {
                this.form_cppt.type_prescription_id = obj.prescription && obj.prescription.type_prescription_id ? obj.prescription.type_prescription_id : 1

                let var_presc = obj.prescription && obj.prescription.form_prescription ? obj.prescription.form_prescription : []
                let var_parent = obj.prescription && obj.prescription.form_prescription_parent ? obj.prescription.form_prescription_parent : []
                
                if (var_parent) {
                    const mappedPrescParent = var_parent.map(item => {
                        let name = ""
                        if (item.id == 1) {
                            name = 'OBAT SATUAN'
                        } else if (item.id == 2) {
                            name = 'RACIK 1'
                        } else if (item.id == 3) {
                            name = 'RACIK 2'
                        } else if (item.id == 4) {
                            name = 'RACIK 3'
                        } else if (item.id == 5) {
                            name = 'RACIK 4'
                        } else {
                            name = '-'
                        }
                        return {
                            id: item.id,
                            used: item.used,
                            value: item.value,
                            prescription: name,
                        };
                    });                
                    this.form_cppt.form_prescription_parent = mappedPrescParent;
                } else {
                    this.form_cppt.form_prescription_parent = []
                }

                if (var_presc) {
                    const mappedPresc = var_presc.map(item => {
                        let name = ""
                        if (item.id == 1) {
                            name = 'OBAT SATUAN'
                        } else if (item.id == 2) {
                            name = 'RACIK 1'
                        } else if (item.id == 3) {
                            name = 'RACIK 2'
                        } else if (item.id == 4) {
                            name = 'RACIK 3'
                        } else if (item.id == 5) {
                            name = 'RACIK 4'
                        } else {
                            name = '-'
                        }
                        return {
                            pharmacy_catalog_id: item.pharmacy_catalog_id, 
                            prescription_type_id: item.prescription_item_type_id,
                            package_id: item.package_id,
                            package_name: item.package_name,
                            dose: item.dose,
                            sum_dose: item.dose+' '+ item.dose_name, 
                            value_package: item.qty,
                            sort: item.sort, 
                            used: item.used,
                            desc: item.desc,

                            code: item.pharmacy_catalog_code,
                            name: item.pharmacy_catalog_name,
                            value_doctor: item.qty_doctor,
                            prescription_type_name: name,
                        };
                    });      
                    this.form_cppt.form_prescription = mappedPresc;                                  
                } else {
                    this.form_cppt.form_prescription = [];                                  
                }
            }
        }

    },
    created () {
        // this.$store.dispatch('laboratory/getPackageAll', { type_costing_id: 4 })
        // this.$store.dispatch('radiology/getPackageAll')
        this.$store.dispatch('physiotherapy/getPackageAll')
        this.$store.dispatch('packages/getParam')

        this.getLabPackageListV2()
        this.getRadPackageListV2()

        if (this.$route.params.id) {
            this.reg_des_id = this.$route.params.id
            this.getData(this.$route.params.id)            
        }

    }

}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>
<style scoped>
@import '../../../../@core/css/utility.css';

.pointer:hover {
    cursor: pointer; 
}

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

table.custom-border tr td {
    border: 0px;
}
</style>