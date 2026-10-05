<template>
    <div>
        <patientMenu selectedMenu="menu2" />
        <div class="label-head my-16">
            <span class="px-16 text-bold-24 text-neutral-700">Pengkajian Dokter</span>
        </div>
        <PatientBanner />

        <div class="card-body mt-20 shadow-sm">
            <div class="d-flex justify-content-between">
                <div>
                    <template v-if="cppt_list.length === 0">
                        <b-button disabled pill variant="pink-500" size="sm" class="px-16 mr-2">
                            <b-icon icon="printer" /> <span>Cetak CPPT</span>
                        </b-button>
                    </template>
                    <template v-else>
                        <router-link :to="{ path: '/patient/cppt/print/'+ reg_des_id}" target="_blank">
                            <b-button pill variant="pink-500" size="sm" class="px-16 mr-2">
                                <b-icon icon="printer" /> <span>Cetak CPPT</span>
                            </b-button>
                        </router-link>
                    </template>
                </div>
                <div>
                    <b-button v-if="active.is_consultation === true" @click="openModalConsultation" pill variant="outline-primary" size="sm" class="px-16 mr-2">Konsultasi</b-button>

                    <b-button @click="openModalSBAR" pill variant="success" size="sm" class="px-16 mr-2">
                        <b-icon icon="chat-square-dots-fill" /> <span>SBAR</span>
                    </b-button>
                    
                    <b-button @click="openModalCPPT('ADD', null)" :disabled="is_soap_null === true" pill variant="primary" size="sm" class="px-16 mr-2">
                        <b-icon icon="plus-circle-fill" /> <span>Tambah CPPT</span>
                    </b-button>

                    <template v-if="regData.doctor_in_charge_id == userData.employee_id">
                        <b-button @click="verifyAllList()" pill variant="orange-500" size="sm" class="px-16">
                            <b-icon icon="patch-check-fill" /> <span>Verifikasi Semua</span>
                        </b-button>
                    </template>
                </div>
            </div>

            <div class="card mt-16">
                <div class="card-header text-bold-14 text-white bg-primary">Filter CPPT</div>
                <div class="card-body pt-1 pb-12">
                    <div class="row">
                        <div class="col-lg-3">
                            <label class="c-form-label">PPA</label>
                            <v-select v-model="params.user_added" :options="OptionsPPA" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                        </div>
                        <div class="col-lg-3">
                            <label class="c-form-label">Tanggal</label>
                            <b-form-input v-model="params.date_added" type="date" />
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <b-button @click="resetFilter" :disabled="isDisabledFilter" size="sm" variant="outline-primary" class="mr-1">
                        <feather-icon icon="XIcon" /> Reset
                    </b-button>
                    <b-button @click="getCPPTList" :disabled="isDisabledFilter" size="sm" variant="primary" class="px-20">Filter</b-button>
                </div>
            </div>
            
            <template v-if="is_soap_null === false">
                <div class="d-flex justify-content-between align-items-center">
                    <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0">
                        <span class="text-regular-14">Tampilkan</span>
                        <b-form-select v-model="perPage" size="sm" :options="pageOptions" class="mx-1"/>
                        <span class="text-regular-14">Entri</span>
                    </b-form-group>
                </div>

                <b-table :fields="CPPTFields" :items="cppt_list" responsive bordered class="w-100 mt-8" style="font-size: 13px">
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
                            </div>
                            <div class="mt-8">
                                <div class="font-weight-bold">OBJECTIVE</div>
                                <div v-html="parsedBody(row.item?.objective)"></div>
                                <table class="w-100 custom-border table-sm" style="border: 0px;">
                                    <tr>
                                        <td class="align-top" style="width: 150px;">Tanda Vital</td>
                                        <td class="px-1 text-center" style="width: 20px;">:</td>
                                        <td>
                                            <div>
                                                <b>Systolic: </b>{{ row.item.systolic_blood_pressure }} mmHg,
                                                <b>Diastolic: </b>{{ row.item.diastolic_blood_pressure }} mmHg,
                                                <b>Frekuensi Nadi: </b>{{ row.item.pulse_frequency }} x/mnt,
                                                <b>Frekuensi Nafas: </b>{{ row.item.breath_frequency }} x/mnt,
                                                <b>Saturasi Oksigen: </b>{{ row.item.oximetry_frequency }} %,
                                                <b>Suhu: </b>{{ row.item.temperature }} ᵒC,
                                                <b>Oksigen: </b>{{ getOxygenName(row.item.oxygen_type_id || '-') }},
                                                <b>Kesadaran:</b> {{ getAwareName(row.item.aware) }},
                                                <b>GCS Mata: </b>{{ getGcsEyeName(row.item.eye_score || '-') }},
                                                <b>GCS Verbal: </b>{{ getGcsVerbalName(row.item.verbal_score || '-') }},
                                                <b>GCS Motorik: </b>{{ getGcsMotoricName(row.item.motor_score || '-') }},
                                                <!-- <b>GCS Pupil: </b>{{ getGcsPupilName(row.item.pupil_score) }}, -->
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- <tr>
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
                                    </tr> -->
                                </table>
                            </div>
                            <div class="mt-8">
                                <div class="font-weight-bold">ASSESSMENT</div>
                                <div v-html="parsedBody(row.item?.assessment)"></div>
                                <table class="w-100 custom-border table-sm" style="border: 0px;">
                                    <tr>
                                        <td class="align-top" style="width: 150px;">Diagnosa (ICD10)</td>
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
                                    <tr>
                                        <td class="align-top" style="width: 150px;">Laboratorium</td>
                                        <td class="align-top px-1 text-center" style="width: 20px;">:</td>
                                        <td>
                                            <div class="font-weight-bold">{{ row.item.laboratory_request && row.item.laboratory_request.no_request ? row.item.laboratory_request.no_request : '-' }}</div>
                                            <template v-if="row.item.laboratory_request_items">
                                                <li v-for="(list, index) in row.item.laboratory_request_items">{{ list.costing_name }}</li>
                                            </template>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top">Radiologi</td>
                                        <td class="align-top px-1 text-center">:</td>
                                        <td>
                                            <div class="font-weight-bold">{{ row.item.radiology_request && row.item.radiology_request.no_request ? row.item.radiology_request.no_request : '-' }}</div>
                                            <template v-if="row.item.radiology_request_items">
                                                <li v-for="(list, j) in row.item.radiology_request_items">
                                                    {{ list.costing_name }} 
                                                    <template v-if="list.url.length > 0">
                                                        <b-button size="sm" variant="outline-secondary" class="ml-2" @click="isRefcontrol(list.url)"><feather-icon icon="EyeIcon" /> Lihat Hasil</b-button>
                                                    </template>
                                                </li>
                                            </template>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top">Fisioterapi</td>
                                        <td class="align-top px-1 text-center">:</td>
                                        <td>
                                            <div class="font-weight-bold">{{ row.item.physiotherapy_request?.no_request || '-' }}</div>
                                            <template v-if="row.item.physiotherapy_request_items">
                                                <li v-for="(list, j) in row.item.physiotherapy_request_items">
                                                    {{ list.costing_name }} 
                                                </li>
                                            </template>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top">Resep</td>
                                        <td class="align-top px-1 text-center">:</td>
                                        <td>
                                            <div class="font-weight-bold">{{ row.item.prescription && row.item.prescription.no_order ? row.item.prescription.no_order : '-' }}</div>
                                            <template v-if="row.item.prescription && row.item.prescription.form_prescription && row.item.prescription.form_prescription.length > 0">
                                                <li v-for="(list, index) in row.item.prescription.form_prescription">{{ list.pharmacy_catalog_name }}</li>
                                            </template>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </template>
                    <!-- <template #cell(instructions)="row">
                        <div v-html="parsedBody(row.item?.instructions)"></div>
                    </template> -->
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

                <div class="col px-0 mt-20">
                    <div class="d-flex">
                        <span class="text-regular-14">Menampilkan {{ firstRows }} hingga <template v-if="lastRows < totalRows">{{ lastRows }}</template> <template v-else>{{ totalRows }}</template> dari {{ totalRows }} entri</span>
                        <div class="ml-auto">
                            <b-pagination 
                                v-model="currentPage" 
                                :total-rows="totalRows"
                                :per-page="perPage" 
                                align="fill" size="sm" class="my-0">
                            </b-pagination>
                        </div>
                    </div>
                </div>
            </template>

            <div v-if="is_soap_null === true" class="w-100 bg-warning-100 p-12 mt-24 text-center" style="font-size: 13px;">
                <span>Apakah Anda hendak menambahkan CPPT?</span>
                <b-button @click="createEmptySoap" size="sm" variant="w3green" class="ml-2">Ya, Lanjutkan</b-button>
            </div>
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
                            </b-button>
                        </div>
                    </template>
                </b-table>
            </div>
        </b-modal>

        <b-modal v-model="active.modal_res_lab" scrollable size="lg" title="Hasil Laboratorium" hide-footer>
            <b-table :items="labResults" bordered responsive small style="font-size: 14px;"></b-table>
        </b-modal>
        
        <b-modal v-model="active.modal_cppt" scrollable size="xl" hide-footer>
            <template #modal-header>
                <div>
                    <div class="font-weight-bold" style="font-size: 18px">CPPT</div>
                    <div style="font-size:20px">
                        <b-badge variant="primary" class="mr-1" style="font-weight: 600">BB {{ vitalData?.weight || 0}}</b-badge>
                        <b-badge variant="primary" class="mr-1" style="font-weight: 600">TB {{ vitalData?.height || 0}}</b-badge>
                        <b-badge variant="primary" class="mr-1" style="font-weight: 600">Sistolik {{ vitalData?.systolic || 0}}</b-badge>
                        <b-badge variant="primary" class="mr-1" style="font-weight: 600">Diastolik {{ vitalData?.diastolic || 0}}</b-badge>
                        <b-badge variant="primary" class="mr-1" style="font-weight: 600">Nadi {{ vitalData?.pulse || 0}}</b-badge>
                        <b-badge variant="primary" class="mr-1" style="font-weight: 600">Suhu {{ vitalData?.temperature || 0}}</b-badge>
                        <b-badge variant="primary" class="mr-1" style="font-weight: 600">Nafas {{ vitalData?.respiratory_rate || 0}}</b-badge>
                        <b-badge variant="primary" class="mr-1" style="font-weight: 600">Saturasi {{ vitalData?.oxygen_saturation || 0}}</b-badge>
                        <b-badge variant="primary" class="mr-1" style="font-weight: 600">Oksigen: {{ getOxygenName(vitalData?.oxygen_type_id || 0)}}</b-badge>
                        <b-badge variant="primary" class="mr-1" style="font-weight: 600">Kesadaran: {{ getAwareName(vitalData?.aware) }}</b-badge>
                        <b-badge variant="primary" class="mr-1" style="font-weight: 600">GCS Mata: {{ getGcsEyeName(vitalData?.eye_score || '-') }}</b-badge>
                        <b-badge variant="primary" class="mr-1" style="font-weight: 600">GCS Verbal: {{ getGcsVerbalName(vitalData?.verbal_score || '-') }}</b-badge>
                        <b-badge variant="primary" class="mr-1" style="font-weight: 600">GCS Motorik: {{ getGcsMotoricName(vitalData?.motor_score || '-') }}</b-badge>
                        <!-- <b-badge variant="primary" class="mr-1" style="font-weight: 600">GCS Pupil: {{ getGcsPupilName(vitalData?.pupil_score) }}</b-badge> -->
                        <!-- <b-badge variant="primary" class="mr-1" style="font-weight: 600">GD {{ vitalData?.blood_sugar || 0}}</b-badge>
                        <b-badge variant="primary" class="mr-1" style="font-weight: 600">Kesadaran: {{ getVerbalName(vitalData?.verbal_response || 0)}}</b-badge> -->
                    </div>
                </div>
                <feather-icon icon="XIcon" @click="active.modal_cppt = false" size="24" class="pointer" />
            </template>
            
            <div class="row">
                <section class="col-9">
                    <section class="card">
                        <div class="card-header font-weight-bold bg-info-200">SUBJECTIVE</div>
                        <div class="card-body">
                            <div>
                                <label class="c-form-label">Subjective</label>
                                <b-form-textarea rows="4" v-model="form_cppt.subjective" autocomplete="off" />
                            </div>
                        </div>
                    </section>

                    <section class="card mt-24">
                        <div class="card-header font-weight-bold bg-info-200">OBJECTIVE</div>
                        <div class="card-body">
                            <div>
                                <b-card-header v-b-toggle.ttv class="p-1" style="background-color: #ECF2F5">
                                    <div class="d-flex align-items-center">
                                        <ModernLabel label="Tanda Vital" class="w-100" />
                                        <feather-icon icon="ChevronDownIcon" size="20" />
                                    </div>
                                </b-card-header>
                                <b-collapse id="ttv" visible>
                                    <div class="row mt-8">
                                        <div class="col-lg-4">
                                            <label class="c-form-label">Systolic</label>
                                            <b-input-group append="mmHg">
                                                <b-form-input type="number" autocomplete="off" v-model="form_cppt.systolic_blood_pressure"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="col-lg-4 mt-2 mt-lg-0">
                                            <label class="c-form-label">Diastolic</label>
                                            <b-input-group append="mmHg">
                                                <b-form-input type="number" autocomplete="off" v-model="form_cppt.diastolic_blood_pressure"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="col-lg-4 mt-2 mt-lg-0">
                                            <label class="c-form-label">Frekuensi Nadi</label>
                                            <b-input-group append="x/mnt">
                                                <b-form-input type="number" autocomplete="off" v-model="form_cppt.pulse_frequency"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="col-lg-4 mt-2">
                                            <label class="c-form-label">Frekuensi Nafas</label>
                                            <b-input-group append="x/mnt">
                                                <b-form-input type="number" autocomplete="off" v-model="form_cppt.breath_frequency"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="col-lg-4 mt-2">
                                            <label class="c-form-label">Saturasi Oksigen</label>
                                            <b-input-group append="%">
                                                <b-form-input type="number" autocomplete="off" v-model="form_cppt.oximetry_frequency"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="col-lg-4 mt-2">
                                            <label class="c-form-label">Suhu</label>
                                            <b-input-group append="ᵒC">
                                                <b-form-input type="number" autocomplete="off" v-model="form_cppt.temperature"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="col-lg-4 mt-2">
                                            <label class="c-form-label">Oksigen</label>
                                            <v-select v-model="form_cppt.oxygen_type_id" :options="OptionsOxygen" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                        </div>
                                        <div class="col-lg-4 mt-2">
                                            <label class="c-form-label">Kesadaran</label>
                                            <v-select v-model="form_cppt.aware" :options="OptionsAware" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                        </div>
                                        <div class="col-lg-4 mt-2">
                                            <label class="c-form-label">GCS Mata</label>
                                            <v-select v-model="form_cppt.eye_score" :options="OptionGcsEye" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                        </div>
                                        <div class="col-lg-4 mt-2">
                                            <label class="c-form-label">GCS verbal</label>
                                            <v-select v-model="form_cppt.verbal_score" :options="OptionGcsVerbal" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                        </div>
                                        <div class="col-lg-4 mt-2">
                                            <label class="c-form-label">GCS Motorik</label>
                                            <v-select v-model="form_cppt.motor_score" :options="OptionGcsMotoric" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                        </div>
                                        <!-- <div class="col-lg-4 mt-2">
                                            <label class="c-form-label">GCS Pupil</label>
                                            <v-select v-model="form_cppt.pupil_score" :options="OptionGcsPupil" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                        </div> -->
                                    </div>
                                </b-collapse>
                            </div>
                            <div class="mt-16">
                                <label class="c-form-label">Objective</label>
                                <b-form-textarea rows="5" v-model="form_cppt.objective" autocomplete="off" />
                            </div>
                            <!-- <div class="mt-16 px-0">
                                <b-card-header v-b-toggle.ant class="p-1" style="background-color: #ECF2F5">
                                    <div class="d-flex align-items-center">
                                        <ModernLabel label="Antropometri" class="w-100" />
                                        <feather-icon icon="ChevronDownIcon" size="20" />
                                    </div>
                                </b-card-header>
                                <b-collapse id="ant">
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
                                </b-collapse>
                            </div> -->
                        </div>
                    </section>

                    <section class="card mt-24">
                        <div class="card-header font-weight-bold bg-info-200">ASSESSMENT</div>
                        <div class="card-body">
                            <div>
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
                                <v-select v-model="icd10_id" @input="onAdd10($event)" aria-readonly="true" @search="onSearchIcd10" :options="OptionsIcd10" :reduce="OptionsIcd10 => OptionsIcd10" label="title" placeholder="-- Diagnosa (ICD10) --" class="mt-12"></v-select>
                            </div>
                            <div class="mt-16">
                                <label class="c-form-label">Assessment</label>
                                <b-form-textarea rows="4" v-model="form_cppt.assessment" autocomplete="off" />
                            </div>
                        </div>
                    </section>

                    <section class="card mt-24" v-if="userData.user_group.name === 'Super User' || userData.user_group.name === 'Doctor'">
                        <div class="card-header font-weight-bold bg-info-200">PLAN</div>
                        <div class="card-body">
                            <b-card no-body>
                                <b-tabs card>
                                    <b-tab title="Laboratorium" active>
                                        <section :class="active.cppt_view != 'ADD' && active.editAccess.laboratory == false ? 'bg-warning-100 pb-20': ''">
                                            <div v-if="active.cppt_view != 'ADD' && active.editAccess.laboratory == false" class="card-body bg-warning mb-16">
                                                <div class="d-flex align-items-center">
                                                    <b-icon-exclamation-triangle-fill variant="neutral-800" style="width: 24px; height: 24px;"/>
                                                    <div class="ml-3 font-italic">Form penunjang laboratorium tidak dapat diubah karena sudah diproses oleh laboratorium</div>
                                                </div>
                                            </div>

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
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                    </b-tab>
                                    <b-tab title="Fisioterapi">
                                        <b-form-input type="text" v-model="searchphy" placeholder="Pencarian"></b-form-input>
                                        <div class="col-lg-12 mt-3">
                                            <h5>Checked Items:</h5>
                                            <b-form-checkbox-group v-model="form_cppt.physiotherapy_request_items">
                                                <b-form-checkbox
                                                v-for="(item, index) in form_cppt.physiotherapy_request_items"
                                                :key="index"
                                                :value="item"
                                                >
                                                {{ item.costing_name }}
                                                </b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </div>
                                        <div class="mt-12 py-2 bg-neutral-100" style="height: 400px; overflow-y: scroll;">
                                            <b-form-checkbox-group class="col-lg-12" v-model="form_cppt.physiotherapy_request_items">
                                                <b-form-checkbox v-for="(ip, index) in filteredListPhy" v-bind:key="index" :value="{'service_class_id':'9b702245-79c5-4beb-8218-53afb08647aa','physiotherapy_package_id':ip.id, 'costing_name': ip.costing_name}" class="col-lg-5">{{ip.costing_name }}</b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </div>
                                    </b-tab>
                                </b-tabs>
                            </b-card>
                            <div class="mt-16">
                                <label class="c-form-label">Plan Non Farmakologis</label>
                                <b-form-textarea rows="4" v-model="form_cppt.plan" autocomplete="off" ></b-form-textarea>
                            </div>
                        </div>
                    </section>

                    <section v-if="userData.user_group.name === 'Super User' || userData.user_group.name === 'Doctor'" class="card mt-24">
                        <div class="card-header bg-indigo-200">
                            <span class="text-bold-18">Resep</span>
                        </div>
                        <div class="card-body">                                                        
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
                        </div>
                    </section>

                    <!-- <div class="mt-12">
                        <label class="c-form-label">Plan Non Farmakologis</label>
                        <b-form-textarea rows="4" v-model="form_cppt.instructions" autocomplete="off" ></b-form-textarea>
                    </div> -->

                    <div class="card-footer mt-32">
                        <div class="w-100 d-flex justify-content-end">
                            <b-button @click="validationCPPT" :disabled="active.disabledCPPT" variant="primary">Simpan</b-button>
                        </div>
                    </div>
                </section>

                <section class="col-3 position-sticky" :style="{top: '0', height: '100vh', overflowY: 'auto'}">
                    <ModernLabel label="RIWAYAT ASESMEN PASIEN" class="mb-2"/>
                    <b-tabs small content-class="mt-3 text-regular-12" nav-wrapper-class="text-medium-14">
                        <b-tab title="DOKTER">
                            <div v-for="(item, index) in soap_history" class="accordion" role="tablist">
                                <b-card no-body class="mb-1">
                                    <b-card-header block v-b-toggle="'accordion-'+index" header-tag="header" class="pointer d-flex justify-content-between align-items-center" role="tab" onMouseOver="this.style.backgroundColor='#648DE1'; this.style.color='#FFFFFF'" onMouseOut="this.style.backgroundColor='#FFFFFF'; this.style.color='#212529'" >
                                        <div>
                                            <div class="font-weight-bold">SOAP {{ localDateTime(item.date_added) }}</div>
                                            <div>{{ item.doctor_in_charge_name }} | {{ item.health_facility_item_name }}</div>
                                        </div>
                                        <feather-icon icon="ChevronDownIcon" size="18" />
                                    </b-card-header>
                                    <b-collapse :id="`accordion-`+index" accordion="my-accordion" role="tabpanel">
                                        <b-card-body class="p-12">
                                            <b-button @click="copyAllSoap($event, item)" size="sm" variant="primary" class="btn-block">Copy All</b-button>
                                            <section class="card mt-16">
                                                <div class="card-header px-12 py-8">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="text-bold-14">SUBJECTIVE</div>
                                                        <!-- <b-button size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button> -->
                                                    </div>
                                                </div>
                                                <div class="card-body p-12">
                                                    <div><b>Anamnesa: </b>{{ item.anamesa_type_id == '1' ? 'Auto Anamnesa' : 'Allo Anamnesa' }}</div>
                                                    <div><b>Keluhan Utama: </b>{{ item?.main_complaint || '-' }}</div>
                                                    <div><b>Nama Keluarga: </b>{{ item?.family_name || '-' }}</div>
                                                    <div><b>Hubungan Keluarga: </b>{{ getRelation(item?.family_relation_id || 0) }}</div>
                                                    <div><b>R.Penyakit Sekarang: </b>{{ item?.history_disease_now || '-' }}</div>
                                                    <div><b>R.Penyakit Dahulu: </b>{{ item?.history_disease_past || '-' }}</div>
                                                    <div><b>R.Obat: </b>{{ item?.history_drugs || '-' }}</div>
                                                </div>
                                            </section>
                                            <section class="card mt-16">
                                                <div class="card-header px-12 py-8">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="text-bold-14">OBJECTIVE</div>
                                                        <b-button @click="copySelected($event, soapObjective, item)" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                    </div>
                                                </div>
                                                <div class="card-body p-12">
                                                    <div class="">
                                                        <b>Systolic: </b>{{ item.systolic_blood_pressure }},
                                                        <b>Diastolic: </b>{{ item.diastolic_blood_pressure }},
                                                        <b>Nadi: </b>{{ item.pulse_frequency }},
                                                        <b>Nafas: </b>{{ item.breath_frequency }},
                                                        <b>Saturasi: </b>{{ item.oximetry_frequency }},
                                                        <b>Suhu: </b>{{ item.temperature }},
                                                        <b>GD: </b>{{ item.blood_sugar }},
                                                        <b>Oksigen: </b>{{ getOxygenName(item?.oxygen_type_id || '-') }},
                                                        <b>Kesadaran:</b> {{ getAwareName(item?.aware) }},
                                                        <b>GCS Mata: </b>{{ getGcsEyeName(item?.eye_score || '-') }},
                                                        <b>GCS Verbal: </b>{{ getGcsVerbalName(item?.verbal_score || '-') }},
                                                        <b>GCS Motorik: </b>{{ getGcsMotoricName(item?.motor_score || '-') }},
                                                        <!-- <b>GCS Pupil: </b>{{ getGcsPupilName(item?.pupil_score) }}, -->
                                                        <!-- <b>Kesadaran: </b>{{ getVerbalName(item?.verbal_response || '-') }}, -->
                                                    </div>
                                                    <div class="mt-8">
                                                        <b>BB: </b>{{ item.weight }},
                                                        <b>TB: </b>{{ item.height }},
                                                        <b>IMT: </b>{{ item.imt }},
                                                        <b>L.Kepala: </b>{{ item.head_circumference }},
                                                        <b>L.Dada: </b>{{ item.chest_circumference }},
                                                        <b>L.Lengan Atas: </b>{{ item.arm_circumference }},
                                                        <b>L.Perut: </b>{{ item.stomach_circumference }},
                                                        <b>L.Lutut: </b>{{ item.knee_circumference }},
                                                        <b>Lainnya: </b>{{ item.soap_o_anythingelse ? item.soap_o_anythingelse : '-' }}
                                                    </div>
                                                    <div class="mt-8"><b>Pemeriksaan Fisik: </b> {{ item?.physical_check || '-' }}</div>
                                                    <div><b>Lainnya: </b> {{ item?.soap_o_anythingelse || '-' }}</div>
                                                </div>
                                            </section>
                                            <section class="card mt-16">
                                                <div class="card-header px-12 py-8">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="text-bold-14">ASSESSMENT</div>
                                                        <b-button @click="copySelected($event, soapAssesment, item)" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                    </div>
                                                </div>
                                                <div class="card-body p-12">
                                                    <div><b>Catatan ICD10 : </b>{{ item?.icd10_notes || '-' }}</div>
                                                    <div>
                                                        <div><b>ICD10: </b><span v-if="!item.icd10 || item.icd10.length == 0">-</span></div>
                                                        <template v-if="item.icd10?.length > 0">
                                                            <li v-for="icd10 in item.icd10">{{ icd10.code }} {{ icd10.name }}</li>
                                                        </template>
                                                    </div>
                                                    <div class="mt-8">
                                                        <div><b>Non-ICD10: </b><span v-if="!item.non_icd10 || item.non_icd10?.length == 0">-</span></div>
                                                        <template v-if="item.non_icd10?.length > 0">
                                                            <li v-for="list in item.non_icd10">{{ list }}</li>
                                                        </template>
                                                    </div>
                                                    <!-- <div><b>Lainnya : </b>{{ item?.soap_a_anythingelse || '-' }}</div> -->
                                                </div>
                                            </section>
                                            <section class="card mt-16">
                                                <div class="card-header px-12 py-8">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="text-bold-14">PLAN</div>
                                                        <!-- <b-button size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button> -->
                                                    </div>
                                                </div>
                                                <div class="card-body p-12">
                                                    <div>
                                                        <div>
                                                            <b>PEMERIKSAAN: </b>
                                                            <span v-if="!item.transaction_doctor || item.transaction_doctor.length == 0">-</span>
                                                        </div>
                                                        <template v-if="item.transaction_doctor?.length > 0">
                                                            <li v-for="list in item.transaction_doctor">{{ list.costing_name }}</li>
                                                        </template>
                                                    </div>
                                                    <template v-if="item.soap_p?.length > 0">
                                                        <div v-for="(list, index_i) in item.soap_p" :class="index_i == 0 ? 'mt-8' : 'mt-1'">
                                                            <div>
                                                                <div>
                                                                    <b>ICD9CM: </b>
                                                                    <span v-if="!list.icd9cm || list.icd9cm.length == 0">-</span>
                                                                </div>
                                                                <template v-if="list.icd9cm?.length > 0">
                                                                    <li v-for="unit in list.icd9cm">{{ unit.code }} {{ unit.name }}</li>
                                                                </template>
                                                            </div>
                                                            <div><b>Catatan ICD9CM : </b>{{ list?.therapy_plan || '-' }}</div>
                                                        </div>
                                                    </template>
                                                    <div v-else class="mt-8">
                                                        <div>
                                                            <b>ICD9CM: -</b>
                                                            <b>CATATAN ICD9CM: -</b>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                            <section class="card mt-16">
                                                <div class="card-header px-12 py-8">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="text-bold-14">PENUNJANG</div>
                                                        <b-button @click="copySelected($event, soapSupport, item)" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                    </div>
                                                </div>
                                                <div class="card-body p-12">
                                                    <div>
                                                        <div>
                                                            <b>LABORATORIUM: </b>
                                                            <span v-if="!item.laboratory_request || item.laboratory_request.length == 0">-</span>
                                                        </div>
                                                        <div v-if="item?.laboratory_request?.length > 0">
                                                            <template v-for="list in item.laboratory_request">
                                                                <div class="mt-2">{{ list?.no_request }}
                                                                    <template v-if="list.result?.length > 0">
                                                                        <b-button size="sm" variant="outline-secondary" class="ml-2" @click="openLabResult(list.result)"><feather-icon icon="EyeIcon" /></b-button>
                                                                    </template>
                                                                </div>
                                                                <li v-for="unit in list.laboratory_request_items">
                                                                    {{ unit.laboratory_package_name }}
                                                                    <template v-if="unit.result?.length > 0">
                                                                        <table class="table table-sm" style="font-size: 10px;">
                                                                            <tr><th>Flag</th><th>Nilai</th><th>Rujukan</th><th>Satuan</th></tr>
                                                                            <tr v-for="results in unit.result">
                                                                                <td>{{results.flag}}</td>
                                                                                <td>{{results.hasil}}</td>
                                                                                <td>{{results.n_rujukan}}</td>
                                                                                <td>{{results.satuan}}</td>
                                                                            </tr>                                                                    
                                                                        </table>                                                                    
                                                                    </template>
                                                                    <!-- <b-button @click="linkToLabResult(unit?.laboratory_request_id || 'null')" size="sm" variant="outline-secondary" class="mr-1">
                                                                        <feather-icon icon="EyeIcon" />
                                                                    </b-button> -->
                                                                </li>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="mt-16">
                                                        <div>
                                                            <b>RADIOLOGI: </b>
                                                            <span v-if="!item.radiology_request || item.radiology_request.length == 0">-</span>
                                                        </div>
                                                        <div v-if="item?.radiology_request?.length > 0">
                                                            <template v-for="list in item.radiology_request">
                                                                <div class="mt-2">{{ list?.no_request }}</div>
                                                                <li v-for="unit in list.radiology_request_items">
                                                                    {{ unit.radiology_package_name }}
                                                                    <template v-if="unit.url?.length > 0">
                                                                        <b-button size="sm" variant="outline-secondary" class="ml-2" @click="isRefcontrol(unit.url)"><feather-icon icon="EyeIcon" /></b-button>
                                                                    </template>
                                                                </li>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="mt-16">
                                                        <div>
                                                            <b>FISIOTERAPI: </b>
                                                            <span v-if="!item.physiotherapy_request || item.physiotherapy_request.length == 0">-</span>
                                                        </div>
                                                        <div v-if="item?.physiotherapy_request?.length > 0">
                                                            <template v-for="list in item.physiotherapy_request">
                                                                <div class="mt-2">{{ list?.no_request }}</div>
                                                                <li v-for="unit in list.physiotherapy_request_items">{{ unit.physiotherapy_package_name }}</li>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                            <section class="card mt-16">
                                                <div class="card-header px-12 py-8">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="text-bold-14">RESEP</div>
                                                        <b-button @click="copySelected($event, soapPrescription, item)" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                    </div>
                                                </div>
                                                <div class="card-body p-12">
                                                    <div v-if="item?.prescription" class="mt-1">
                                                        <template v-for="(list, presc_i) in item.prescription">
                                                            <div :class="presc_i > 0 ? 'mt-2' : ''">{{ list?.no_order }}</div>
                                                            <li v-for="unit in list.form_prescription">{{ unit?.pharmacy_catalog_name }}</li>
                                                        </template>
                                                    </div>
                                                    <template v-else>-</template>
                                                </div>
                                            </section>
                                        </b-card-body>
                                    </b-collapse>
                                </b-card>
                            </div>
                        </b-tab>
                        <b-tab title="PERAWAT">
                            <div v-for="(item, index) in nurse_history" class="accordion" role="tablist">
                                <b-card no-body class="mb-1">
                                    <b-card-header block v-b-toggle="'accordion-'+index" header-tag="header" class="pointer d-flex justify-content-between align-items-center" role="tab" onMouseOver="this.style.backgroundColor='#648DE1'; this.style.color='#FFFFFF'" onMouseOut="this.style.backgroundColor='#FFFFFF'; this.style.color='#212529'" >
                                        <div>
                                            <div class="font-weight-bold">PERAWAT {{ localDateTime(item.date_added) }}</div>
                                            <div>{{ item.employee_nurse_name }} | {{ item.health_facility_item_name }}</div>
                                        </div>
                                        <feather-icon icon="ChevronDownIcon" size="18" />
                                    </b-card-header>
                                    <b-collapse :id="`accordion-`+index" accordion="my-accordion" role="tabpanel">
                                        <b-card-body class="p-12">
                                            <b-button @click="copySelected($event, nurseObjective, item)" size="sm" variant="primary" class="btn-block">Copy All</b-button>
                                            <section class="card mt-16">
                                                <div class="card-header px-12 py-8">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="text-bold-14">SUBJECTIVE</div>
                                                        <!-- <b-button size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button> -->
                                                    </div>
                                                </div>
                                                <div class="card-body p-12">
                                                    <div><b>Anamnesa: </b>{{ item.anamesa_type_id == '1' ? 'Auto Anamnesa' : 'Allo Anamnesa' }}</div>
                                                    <div><b>Keluhan Utama: </b>{{ item?.main_complaint || '-' }}</div>
                                                    <div><b>Nama Keluarga: </b>{{ item?.family_name || '-' }}</div>
                                                    <div><b>Hubungan Keluarga: </b>{{ getRelation(item?.family_relation_id || 0) }}</div>
                                                    <div><b>R.Penyakit Sekarang: </b>{{ item?.history_disease_now || '-' }}</div>
                                                    <div><b>R.Penyakit Dahulu: </b>{{ item?.history_disease_past || '-' }}</div>
                                                </div>
                                            </section>
                                            <section class="card mt-16">
                                                <div class="card-header px-12 py-8">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="text-bold-14">OBJECTIVE</div>
                                                        <b-button @click="copySelected($event, nurseObjective, item)" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                    </div>
                                                </div>
                                                <div class="card-body p-12">
                                                    <div class="">
                                                        <b>Systolic: </b>{{ item.systolic_blood_pressure }},
                                                        <b>Diastolic: </b>{{ item.diastolic_blood_pressure }},
                                                        <b>Nadi: </b>{{ item.pulse_frequency }},
                                                        <b>Nafas: </b>{{ item.breath_frequency }},
                                                        <b>Saturasi: </b>{{ item.oximetry_frequency }},
                                                        <b>Suhu: </b>{{ item.temperature }},
                                                        <b>GD: </b>{{ item.blood_sugar }},
                                                        <b>Oksigen: </b>{{ getOxygenName(item?.oxygen_type_id || '-') }},
                                                        <b>Kesadaran:</b> {{ getAwareName(item?.aware) }},
                                                        <b>GCS Mata: </b>{{ getGcsEyeName(item?.eye_score || '-') }},
                                                        <b>GCS Verbal: </b>{{ getGcsVerbalName(item?.verbal_score || '-') }},
                                                        <b>GCS Motorik: </b>{{ getGcsMotoricName(item?.motor_score || '-') }},
                                                        <!-- <b>GCS Pupil: </b>{{ getGcsPupilName(item?.pupil_score) }}, -->
                                                        <!-- <b>Kesadaran: </b>{{ getVerbalName(item?.verbal_response || '-') }}, -->
                                                    </div>
                                                    <div class="mt-8">
                                                        <b>BB: </b>{{ item.weight }},
                                                        <b>TB: </b>{{ item.height }},
                                                        <b>IMT: </b>{{ item.imt }},
                                                        <b>L.Kepala: </b>{{ item.head_circumference }},
                                                        <b>L.Dada: </b>{{ item.chest_circumference }},
                                                        <b>L.Lengan Atas: </b>{{ item.arm_circumference }},
                                                        <b>L.Perut: </b>{{ item.stomach_circumference }},
                                                        <b>L.Lutut: </b>{{ item.knee_circumference }},
                                                        <b>Lainnya: </b>{{ item.soap_o_anythingelse ? item.soap_o_anythingelse : '-' }}
                                                    </div>
                                                </div>
                                            </section>
                                        </b-card-body>
                                    </b-collapse>
                                </b-card>
                            </div>
                        </b-tab>
                        <b-tab title="CPPT" active>
                            <div v-for="(item, index) in cppt_history" class="accordion" role="tablist">
                                <b-card no-body class="mb-1">
                                    <b-card-header block v-b-toggle="'accordion-'+index" header-tag="header" class="pointer d-flex justify-content-between align-items-center" role="tab" onMouseOver="this.style.backgroundColor='#648DE1'; this.style.color='#FFFFFF'" onMouseOut="this.style.backgroundColor='#FFFFFF'; this.style.color='#212529'" >
                                        <div>
                                            <div class="font-weight-bold">CPPT {{ localDateTime(item.date_added) }}</div>
                                            <div>{{ item.user_added_name }} | {{ item.user_added_role }}</div>
                                        </div>
                                        <feather-icon icon="ChevronDownIcon" size="18" />
                                    </b-card-header>
                                    <b-collapse :id="`accordion-`+index" accordion="my-accordion" role="tabpanel">
                                        <b-card-body class="p-12">
                                            <b-button @click="copyAllCppt($event, item)" size="sm" variant="primary" class="btn-block">Copy All</b-button>
                                            <section class="card mt-16">
                                                <div class="card-header px-12 py-8">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="text-bold-14">SUBJECTIVE</div>
                                                        <b-button @click="copySelected($event, cpptSubjective, item)" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                    </div>
                                                </div>
                                                <div class="card-body p-12">
                                                    <div>{{ item?.subjective || '-' }}</div>
                                                </div>
                                            </section>
                                            <section class="card mt-16">
                                                <div class="card-header px-12 py-8">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="text-bold-14">OBJECTIVE</div>
                                                        <b-button @click="copySelected($event, cpptObjective, item)" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                    </div>
                                                </div>
                                                <div class="card-body p-12">
                                                    <div>{{ item?.objective || '-' }}</div>
                                                    <div class="mt-8">
                                                        <b>Systolic: </b>{{ item.systolic_blood_pressure }},
                                                        <b>Diastolic: </b>{{ item.diastolic_blood_pressure }},
                                                        <b>Nadi: </b>{{ item.pulse_frequency }},
                                                        <b>Nafas: </b>{{ item.breath_frequency }},
                                                        <b>Saturasi: </b>{{ item.oximetry_frequency }},
                                                        <b>Suhu: </b>{{ item.temperature }},
                                                        <b>GD: </b>{{ item.blood_sugar }},
                                                        <b>Oksigen: </b>{{ getOxygenName(item?.oxygen_type_id || '-') }},
                                                        <b>Kesadaran:</b> {{ getAwareName(item?.aware) }},
                                                        <b>GCS Mata: </b>{{ getGcsEyeName(item?.eye_score || '-') }},
                                                        <b>GCS Verbal: </b>{{ getGcsVerbalName(item?.verbal_score || '-') }},
                                                        <b>GCS Motorik: </b>{{ getGcsMotoricName(item?.motor_score || '-') }},
                                                        <!-- <b>GCS Pupil: </b>{{ getGcsPupilName(item?.pupil_score) }}, -->
                                                        <!-- <b>Kesadaran: </b>{{ getVerbalName(item?.verbal_response || '-') }}, -->
                                                    </div>
                                                    <!-- <div class="mt-8">
                                                        <b>BB: </b>{{ item.weight }},
                                                        <b>TB: </b>{{ item.height }},
                                                        <b>IMT: </b>{{ item.imt }},
                                                        <b>L.Kepala: </b>{{ item.head_circumference }},
                                                        <b>L.Dada: </b>{{ item.chest_circumference }},
                                                        <b>L.Lengan Atas: </b>{{ item.arm_circumference }},
                                                        <b>L.Perut: </b>{{ item.stomach_circumference }},
                                                        <b>L.Lutut: </b>{{ item.knee_circumference }},
                                                        <b>Lainnya: </b>{{ item.soap_o_anythingelse ? item.soap_o_anythingelse : '-' }}
                                                    </div> -->
                                                </div>
                                            </section>
                                            <section class="card mt-16">
                                                <div class="card-header px-12 py-8">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="text-bold-14">ASSESSMENT</div>
                                                        <b-button @click="copySelected($event, cpptAssesment, item)" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                    </div>
                                                </div>
                                                <div class="card-body p-12">
                                                    <div>{{ item?.assessment || '-' }}</div>
                                                    <div class="mt-8">
                                                        <div><b>ICD10: </b><span v-if="!item.icd10 || item.icd10.length == 0">-</span></div>
                                                        <template v-if="item.icd10?.length > 0">
                                                            <li v-for="icd10 in item.icd10">{{ icd10.code }} {{ icd10.name }}</li>
                                                        </template>
                                                    </div>
                                                </div>
                                            </section>
                                            <section class="card mt-16">
                                                <div class="card-header px-12 py-8">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="text-bold-14">PLAN</div>
                                                        <b-button @click="copySelected($event, cpptPlan, item)" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                    </div>
                                                </div>
                                                <div class="card-body p-12">
                                                    <div>{{ item?.plan || '-' }}</div>
                                                </div>
                                            </section>
                                            <section class="card mt-16">
                                                <div class="card-header px-12 py-8">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="text-bold-14">PENUNJANG</div>
                                                        <b-button @click="copySelected($event, cpptSupport, item)" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                    </div>
                                                </div>
                                                <div class="card-body p-12">
                                                    <div>
                                                        <div>
                                                            <b>LABORATORIUM: </b>
                                                            <span v-if="!item.laboratory_request || item.laboratory_request.length == 0">-</span>
                                                        </div>
                                                        <div v-if="item?.laboratory_request?.length > 0" class="mt-2">
                                                            <template v-for="list in item.laboratory_request">
                                                                <div>{{ list?.no_request }}</div>
                                                                <li v-for="unit in list.laboratory_request_items">
                                                                    {{ unit.laboratory_package_name }}
                                                                    <template v-if="unit.result?.length > 0">
                                                                        <b-button size="sm" variant="outline-secondary" class="ml-2" @click="openLabResult(unit.result)"><feather-icon icon="EyeIcon" /></b-button>
                                                                    </template>
                                                                    <!-- <b-button @click="linkToLabResult(unit?.laboratory_request_id || 'null')" size="sm" variant="outline-secondary" class="mr-1">
                                                                        <feather-icon icon="EyeIcon" />
                                                                    </b-button> -->
                                                                </li>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="mt-16">
                                                        <div>
                                                            <b>RADIOLOGI: </b>
                                                            <span v-if="!item.radiology_request || item.radiology_request.length == 0">-</span>
                                                        </div>
                                                        <div v-if="item?.radiology_request?.length > 0" class="mt-2">
                                                            <template v-for="list in item.radiology_request">
                                                                <div>{{ list?.no_request }}</div>
                                                                <li v-for="unit in list.radiology_request_items">
                                                                    {{ unit.radiology_package_name }}
                                                                    <template v-if="unit.url?.length > 0">
                                                                        <b-button size="sm" variant="outline-secondary" class="ml-2" @click="isRefcontrol(unit.url)"><feather-icon icon="EyeIcon" /></b-button>
                                                                    </template>
                                                                </li>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                            <section class="card mt-16">
                                                <div class="card-header px-12 py-8">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="text-bold-14">RESEP</div>
                                                        <b-button @click="copySelected($event, cpptPrescription, item)" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                    </div>
                                                </div>
                                                <div class="card-body p-12">
                                                    <div v-if="item?.prescription" class="mt-1">
                                                        <template v-for="list in item.prescription">
                                                            <div>{{ list?.no_order }}</div>
                                                            <li v-for="unit in list.form_prescription">{{ unit?.pharmacy_catalog_name }}</li>
                                                        </template>
                                                    </div>
                                                    <template v-else>-</template>
                                                </div>
                                            </section>
                                            <!-- <section class="card mt-16">
                                                <div class="card-header px-12 py-8">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="text-bold-14">PLAN NON-FARMAKOLOGIS</div>
                                                        <b-button @click="copySelected($event, cpptInstruction, item)" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                    </div>
                                                </div>
                                                <div class="card-body p-12">
                                                    <div>{{ item?.instructions || '-' }}</div>
                                                </div>
                                            </section> -->
                                        </b-card-body>
                                    </b-collapse>
                                </b-card>
                            </div>
                        </b-tab>
                    </b-tabs>                    
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

        <b-modal v-model="active.modal_consultation" size="lg" title="Lembar konsultasi" no-close-on-backdrop hide-footer>
            <template v-if="userData.employee_id === this.regData.doctor_in_charge_id || this.userData.employee_id === this.admin_id">
                <div class="d-flex">
                    <b-button @click="changeConsultTab('LIST')" pill size="sm" :variant="active.tabConsult == 'LIST' ? 'primary' : 'outline-primary'" class="px-16 mr-2">List Konsultasi</b-button>
                    <b-button @click="changeConsultTab('ADD')" pill  size="sm" :variant="active.tabConsult == 'ADD' ? 'primary' : 'outline-primary'" class="px-16">Tambah Konsultasi</b-button>
                </div>
            </template>

            <section v-if="active.tabConsult == 'LIST'" class="row mt-16">
                <div class="col">
                    <b-table fixed bordered :fields="consultFields" :items="consultation_list" thead-class="text-bold-14" tbody-tr-class="text-regular-12">
                        <template #cell(consultant_name)="row">
                            <div>{{ row.item.doctor_in_charge_name }} &</div>
                            <div>{{ row.value }}</div>
                        </template>
                        <template #cell(consultation_type_id)="row">{{ getConsultName(row.value) }}</template>
                        <template #cell(icd10)="row">
                            <template v-if="row.value?.length > 0">
                                <li v-for="list in row.value">{{ list.icd10_code }} {{ list.icd10_name }}</li>
                            </template>
                        </template>
                        <template #cell(action)="row">
                            <div v-if="row.item.is_cancel == 0" class="d-flex">
                                <template v-if="userData.employee_id == row.item.doctor_in_charge_id || userData?.employee_id == admin_id">
                                    <b-button @click="onEditConsultation(row.item)" size="sm" variant="warning-500" class="mr-1">
                                        <feather-icon icon="EditIcon"/>
                                    </b-button>
                                    <b-button @click="cancelConsultation(row.item)" size="sm" variant="danger-500" class="mr-1">
                                        <feather-icon icon="TrashIcon" />
                                    </b-button>
                                </template>
                                <template v-if="row.item.consultation_type_id == 3 && (getColaborator(row.item.consultant_id) || userData?.employee_id == admin_id)">
                                    <b-button @click="onAnswerConsultation(row.item)" size="sm" variant="w3green" class="mr-1">
                                        <feather-icon icon="SendIcon" />
                                    </b-button>
                                </template>
                            </div>
                            <div v-if="row.item.is_cancel == 1">Dibatalkan</div>
                        </template>
                    </b-table>
                </div>
            </section>
            
            <section v-if="active.tabConsult == 'ADD'" class="row mt-16">
                <div class="col-lg-4">
                    <label class="c-form-label">Dokter Kolaborator</label>
                    <v-select v-model="form_consultation.consultant_id" :options="doctor_list" :reduce="item => item.value" label="title" placeholder="-- Kolaborator --"></v-select>
                </div>

                <div class="col-lg-4 mt-10 mt-lg-0">
                    <label class="c-form-label">Konsultasi yang diminta</label>
                    <v-select v-model="form_consultation.consultation_type_id" :disabled="disabledConsType" :options="OptionsConsult" :reduce="item => item.value" label="title" placeholder="-- Konsultasi --"></v-select>
                </div>

                <div class="col-lg-4 mt-10 mt-lg-0">
                    <label class="c-form-label">Tanggal</label>
                    <b-form-input v-model="form_consultation.date" type="date" />
                </div>

                <div class="col-12 w-100 mt-16">
                    <ModernLabel label="Diagnosa (ICD10)" />
                    <template v-if="form_consultation.icd10.length > 0">
                        <b-table responsive bordered :fields="icd10Fields" :items="form_consultation.icd10" class="mt-12 text-center" style="font-size: 14px;" >
                            <template #cell(no)="row">{{ row.index+1 }}</template>
                            <template #cell(title)="row">
                                <div class="text-left">{{ row.value }}</div>
                            </template>
                            <template #cell(action)="row">
                                <b-button variant="danger-600" size="sm" @click="deleteConsult10(row.index)" class="mr-2">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </template>
                        </b-table>
                    </template>                                    
                    <v-select v-model="icdConsult10_id" @input="onAddConsult10($event)" aria-readonly="true" @search="onSearchConsult10" :options="ConsultICD10" :reduce="item => item" label="title" placeholder="-- Diagnosa --" class="mt-12"></v-select>
                </div>
                <div class="col-12 mt-10">
                    <label class="c-form-label">Riwayat Klinik Singkat</label>
                    <b-textarea v-model="form_consultation.clinical_history" rows="4" />
                </div>

                <div class="d-flex justify-content-end w-100 mt-24 px-3">
                    <b-button variant="primary" @click="validationConsultation" :disabled="active.disabledConsultation">Simpan</b-button>
                </div>
            </section>

            <section v-if="active.tabConsult == 'ANSWER'" class="mt-16">
                <table class="custom-border" style="font-size: 12px;">
                    <tr>
                        <td class="align-top" style="width: 150px;">Dokter DPJP</td>
                        <td class="align-top" style="width: 20px;">:</td>
                        <td class="align-top">{{ form_consultation.doctor_in_charge_name }}</td>
                    </tr>
                    <tr>
                        <td class="align-top">ICD10</td>
                        <td class="align-top">:</td>
                        <td class="align-top">
                            <template v-if="form_consultation.icd10?.length > 0">
                                <li v-for="list in form_consultation.icd10">{{ list.title }}</li>
                            </template>
                        </td>
                    </tr>
                    <tr>
                        <td class="align-top">Riwayat Klinis</td>
                        <td class="align-top">:</td>
                        <td class="align-top">{{ parsedBody(form_consultation.clinical_history) }}</td>
                    </tr>
                </table>
                <div class="col-12 px-0 mt-10">
                    <label class="c-form-label">Jawaban Konsultasi</label>
                    <b-textarea v-model="form_consultation.consultation_answer" rows="4" />
                </div>
                <div class="d-flex justify-content-end w-100 mt-24">
                    <b-button variant="outline-primary" @click="changeConsultTab('LIST')" class="mr-1">Batal</b-button>
                    <b-button variant="primary" @click="validationAnswer" :disabled="active.disabledAnswer">Simpan</b-button>
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
        patientMenu, administration, ModernLabel, vSelect,
    },
    data() {
        return {
            // socket : io(process.env.VUE_APP_SOCKET_IO, { transports: ["websocket", "polling", "flashsocket"],withCredentials: true }),
            userData: JSON.parse(localStorage.getItem('userData')),
            admin_id: 'd77142c4-4777-47d8-bd0c-fd7dfd134af4',
            reg_des_id: '',
            urlbe:process.env.VUE_APP_IMAGE,
            is_soap_null: false,
            soapData: {},

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
                    physiotherapy: true,
                    prescription: true,
                },
                modal_sbar: false,
                tabSbar: 'menu1',
                disabledSBAR: false,
                refcontrol: false,
                modal_res_lab: false,

                modal_consultation: false,
                tabConsult: 'LIST',
                disabledConsultation: false,
                disabledConsType: false,
                disabledAnswer: false,
                is_consultation: false,
            },
            imageview: '',
            radiologyResultField: [
                { key: 'no', label: 'No', thStyle:'width: 50px' },
                { key: 'expertise', label: 'Expertise', thStyle:'width: 700px' },
                { key: 'url', label: 'Gambar' },
                { key: 'action', label: 'Aksi' },
            ],
            labResults: [],

            vitalData: {},
            cppt_list: [],
            cppt_history: [],
            nurse_history: [],
            soap_history: [],
            form_cppt: {
                id: '',
                doctor_in_charge_id: '',
                registration_destination_id: '',
                subjective: '',
                objective: '',
                plan: '',
                assessment: '',
                // instructions: '',

                systolic_blood_pressure: '',
                diastolic_blood_pressure: '',
                pulse_frequency: '',
                breath_frequency: '',
                oximetry_frequency: '',
                temperature: '',
                oxygen_type_id: '',
                aware: '',
                eye_score: '',
                verbal_score: '',
                motor_score: '',
                // pupil_score: '',
                // verbal_response: '',
                // blood_sugar: '',
                weight: '',
                height: '',
                imt: '',
                head_circumference: '',
                chest_circumference: '',
                arm_circumference: '',
                stomach_circumference: '',
                knee_circumference: '',

                laboratory_request_items: [],
                radiology_request_items: [],
                physiotherapy_request_items: [],

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
            searchphy: '',
            CPPTFields: [
                // { key: 'no', label: 'No', thStyle:'width: 50px', thClass: 'align-middle' },
                { key: 'user', label: 'Profesional Pemberi Asuhan (PPA)', thStyle:'width: 200px', thClass: 'align-middle' },
                { key: 'content', label: 'CPPT', thClass: 'align-middle' },
                // { key: 'instructions', label: 'Plan Non Farmakologis', thStyle:'width: 300px', thClass: 'align-middle' },
                { key: 'action', label: 'Aksi', thStyle:'width: 200px', thClass: 'align-middle' },
            ],
            OptionsPPA: [],
            params: {
                limit: 5,
                offset: 0,
                registration_id: '',
                user_added: '',
                date_added: '',
            },
            pageOptions: [5, 10, 20, 50],
            currentPage:1,
            perPage: 5,
            firstRows: 1,
            lastRows: 5,
            totalRows: 0,
            isDisabledFilter: false,

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
                oxygen_type_id: '',
                aware: '',
                eye_score: '',
                verbal_score: '',
                motor_score: '',
                // pupil_score: '',
                // verbal_response: '',
                // blood_sugar: '',
                weight: '',
                height: '',
                imt: '',
                head_circumference: '',
                chest_circumference: '',
                arm_circumference: '',
                stomach_circumference: '',
                knee_circumference: '',
                soap_o_anythingelse: '',
                icd10: [],
                icd10_notes: '',
                non_icd10: [],
                // soap_a_anythingelse: '',
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
                oxygen_type_id: '',
                aware: '',
                eye_score: '',
                verbal_score: '',
                motor_score: '',
                // pupil_score: '',
                // verbal_response: '',
                // blood_sugar: '',
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
                // soap_a_anythingelse: '',
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
                oxygen_type_id: '',
                aware: '',
                eye_score: '',
                verbal_score: '',
                motor_score: '',
                // pupil_score: '',
                // verbal_response: '',
                // blood_sugar: '',
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
                // soap_a_anythingelse: '',
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
            },

            form_consultation: {
                id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                consultant_id: '',
                date: moment(new Date()).format('YYYY-MM-DD'),
                icd10: [],
                clinical_history: '',
                consultation_type_id: '',
                consultation_answer: '',

                // optional
                doctor_in_charge_name: ''
            },

            icdConsult10_id:'',
            ConsultICD10:[],

            OptionsConsult:[
                { value: 1, title: 'Perawatan Bersama' },
                { value: 2, title: 'Alih Rawat' },
                { value: 3, title: 'Konsultasi' },
            ],    
            
            icd10Fields: [
                { key: 'no', label: 'No', thStyle: 'width: 60px' },
                { key: 'title', label: 'ICD10', thClass: 'text-left' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 100px' },
            ],
            doctor_list: [],

            consultFields: [
                { key: 'consultant_name', label: 'Kolaborator' },
                { key: 'consultation_type_id', label: 'Konsultasi' },
                { key: 'icd10', label: 'ICD10' },
                { key: 'clinical_history', label: 'Riwayat Klinis' },
                { key: 'consultation_answer', label: 'Jawaban Konsultasi' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 130px' },
            ],
            consultation_list: [],            
        }
    },

    computed: {
        ...mapState({ 
            // items: state => state.soap.items, 
            OptionsCosting: state => state.costing.options.costing,
            
            // itemsLaboratory: state => state.laboratory.package.items,
            // itemsRadiology: state => state.radiology.package.items,
            itemsPhysiotherapy: state => state.physiotherapy.package.items,
            PackageOptions: state => state.packages.options.packages,

            OptionsDoctor: state => state.employee.options.employee,            
            OptionsSpecialistDoctor: state => state.employee.options.specialist,            

             // vital option
            OptionsOxygen: state => state.soap.vitals.OptionsOxygen,
            OptionsAware: state => state.soap.vitals.OptionsAware,
            OptionGcsEye: state => state.soap.vitals.OptionGcsEye,
            OptionGcsVerbal: state => state.soap.vitals.OptionGcsVerbal,
            OptionGcsMotoric: state => state.soap.vitals.OptionGcsMotoric,
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
        filteredListPhy() {
            return this.itemsPhysiotherapy.filter((phy) => {
                const computedPhyObj = { ...phy}
                return Object.keys(computedPhyObj)
                    .some(key => ('' + computedPhyObj[key]).toLowerCase().includes(this.searchphy.toLowerCase()))
            })
        },
    },

    watch: {
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage*(value-1)+1
                this.lastRows = this.perPage*(value)+1
                this.params.offset = this.perPage*(value-1)
                this.params.limit = this.perPage
                this.getCPPTList()
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.getCPPTList()
            }
        },
    },
    
    methods: {
        // getVerbalName(value) {
        //     const result = this.OptionsResponse.find((item) => item.value == value);
        //     return result ? result.title : '-';
        // },
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
        // getGcsPupilName(value) {
        //     const result = this.OptionGcsPupil.find((item) => item.value == value);
        //     return result ? result.title : '-';
        // },

        getRelation(code) {
            const relations = {
                '1': 'Ayah',
                '2': 'Ibu',
                '3': 'Kakak',
                '4': 'Adik',
            };
            return relations[code] || '-';
        },

        openModalConsultation() {
            this.active.modal_consultation = true
        },

        changeConsultTab(tab){
            this.resetConsultation()
            this.active.tabConsult = tab
        },

        resetConsultation() {
            this.disabledConsType = false

            this.form_consultation.id = ''
            this.form_consultation.registration_destination_id = ''
            this.form_consultation.doctor_in_charge_id = ''
            this.form_consultation.consultant_id = ''
            this.form_consultation.date = moment(new Date()).format('YYYY-MM-DD'),
            this.form_consultation.icd10 = []
            this.form_consultation.clinical_history = ''
            this.form_consultation.consultation_type_id = ''
            this.form_consultation.consultation_answer = ''
            this.form_consultation.doctor_in_charge_name = ''
        },

        onSearchConsult10(search, loading) {
            if(search.length >= 3) {
                loading(true);
                this.$store.dispatch('icd10/getParam', search).then(data => {
                    this.ConsultICD10 = []
                    for (let i = 0; i < data.length; i++) {
                        const option = []
                        for (const key in data[i]) {
                            if (key === 'id') { option['value'] = data[i][key] } else if (key === 'name') { option['title'] = data[i]['code']+' '+data[i][key] } else if (key === 'id') { option['oid'] = data[i][key] }
                        }
                        this.ConsultICD10.push(Object.assign({}, option))
                    }
                    loading(false)
                })
            }
        },

        onAddConsult10 (event) {
            this.icdConsult10_id = ''

            if (this.form_consultation.icd10.some(item => item.icd10_id === event.value)) {
                this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list ICD10 yang sama')
            } else {
                this.form_consultation.icd10.push({ icd10_id:event.value, title:event.title, value:event.value, action: 'action'})
            }

        },

        deleteConsult10 (index) {
            this.form_consultation.icd10.splice(index, 1)
        },

        getConsultation(){
            const param_consult = {
                registration_destination_id: this.reg_des_id
            }

            try {                
                this.$store.dispatch('consultation/getListConsultation', param_consult).then((response) => {
                    let temp_data = response.rows

                    if (this.userData.employee_id === this.regData.doctor_in_charge_id || this.userData.employee_id === this.admin_id) {
                        this.active.is_consultation = true
                        this.consultation_list = temp_data

                    } else {
                        this.consultation_list = []
                        for (let i = 0; i < temp_data.length; i++) {
                            const el = temp_data[i];
                            if (this.userData.employee_id === el.consultant_id) {
                                this.consultation_list.push(el)
                            }
                        }

                        if (this.consultation_list.length > 0) {
                            this.active.is_consultation = true
                        }
                    }
                    // this.totalRows = response.iTotal
                }, error => {
                    console.error('Error while GET consultation/getListConsultation at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET consultation/getListConsultation at: ', error);
            }
        },

        validationConsultation() {
            if (this.form_consultation.id == '') {
                this.form_consultation.registration_destination_id = this.regData?.registration_destination_id
                this.form_consultation.doctor_in_charge_id = this.regData?.doctor_in_charge_id
            }
            // console.log('form_consult: ', this.form_consultation);

            try {
                this.active.disabledConsultation = true
                this.$store.dispatch('consultation/postPutConsultation', this.form_consultation)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.tabConsult = 'LIST'
                        this.resetConsultation()
                        this.getConsultation()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabledConsultation = false
            }
        },

        getColaborator(consultant_id){
            if (this.userData.employee_id === consultant_id) {
                return true
            }
            return false
        },

        onEditConsultation(obj) {
            this.changeConsultTab('ADD')
            this.disabledConsType = true
            this.fillConsultation(obj)
        },
        
        onAnswerConsultation(obj) {
            this.changeConsultTab('ANSWER')
            this.fillConsultation(obj)
        },
        
        fillConsultation(obj) {
            this.form_consultation.id = obj?.id || ''
            this.form_consultation.registration_destination_id = obj?.registration_destination_id || ''
            this.form_consultation.doctor_in_charge_id = obj?.doctor_in_charge_id || ''
            this.form_consultation.consultant_id = obj?.consultant_id || ''
            this.form_consultation.date = obj?.date || ''

            if (obj.icd10) {
                const mappedIcd10 = obj.icd10.map(item => {
                    return {
                        icd10_id: item.icd10_id,
                        value: item.icd10_id,
                        title: `${item.icd10_code} ${item.icd10_name}`, 
                        action: "action"
                    };
                });                
                this.form_consultation.icd10 = mappedIcd10;
            } else {
                this.form_consultation.icd10 = []
            }

            this.form_consultation.clinical_history = obj?.clinical_history || ''
            this.form_consultation.consultation_type_id = obj?.consultation_type_id || ''
            this.form_consultation.consultation_answer = obj?.consultation_answer || ''
            this.form_consultation.doctor_in_charge_name = obj?.doctor_in_charge_name || ''
        },

        cancelConsultation(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin membatalkan konsultasi ${obj.consultant_name} tanggal ${obj.date}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Batalkan', cancelTitle: 'Tidak', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('consultation/cancelConsultation', obj.id)
                    .then(
                        res => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.getConsultation()
                        }
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    })
                }
            })
        },
        
        validationAnswer() {
            try {
                this.active.disabledAnswer = true
                this.$store.dispatch('consultation/postPutConsultation', this.form_consultation)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.tabConsult = 'LIST'
                        this.resetConsultation()
                        this.getConsultation()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabledAnswer = false
            }
        },

        getConsultName(value) {
            if (value == 1) {
                return 'Perawatan Bersama'
            } else if (value == 2) {
                return 'Alih Rawat'
            } else if (value == 3) {
                return 'Konsultasi'
            }
        },
        
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
        isRefcontrol(data) {
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
                this.getCPPTList() 
                this.getUsersOption() 

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
                this.getDoctorList()
                this.getConsultation()

                // history
                this.getCpptHistory(this.regData.norm)
                this.getNurseHistory(this.regData.norm)
                this.getSoapHistory(this.regData.norm)
            })
        },

        getDoctorList() {
            this.$store.dispatch('employee/getDoctorOptions', '').then((response) => {
                this.doctor_list = response
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
                    this.vitalData = response.rows[0] || {}
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

            this.form_cppt.systolic_blood_pressure = obj?.systolic || '' 
            this.form_cppt.diastolic_blood_pressure = obj?.diastolic || '' 
            this.form_cppt.pulse_frequency = obj?.pulse || '' 
            this.form_cppt.breath_frequency = obj?.respiratory_rate || '' 
            this.form_cppt.oximetry_frequency = obj?.oxygen_saturation || '' 
            this.form_cppt.temperature = obj?.temperature || '' 
            this.form_cppt.oxygen_type_id = obj?.oxygen_type_id || ''
            this.form_cppt.aware = obj?.aware 
            this.form_cppt.eye_score = obj?.eye_score || '' 
            this.form_cppt.verbal_score = obj?.verbal_score || '' 
            this.form_cppt.motor_score = obj?.motor_score || '' 
            // this.form_cppt.pupil_score = obj?.pupil_score 
            this.form_cppt.weight = obj?.weight || ''
            this.form_cppt.height = obj?.height || ''
            // this.form_cppt.blood_sugar = obj?.blood_sugar || ''
            // this.form_cppt.verbal_response = obj?.verbal_response || ''

            let brt_badan = 0
            if (this.form_cppt.weight) {
                brt_badan = parseFloat(this.form_cppt.weight);
            }

            let tng_badan = 0
            if (this.form_cppt.height) {
                tng_badan = parseFloat(this.form_cppt.height);
            }

            let imt = 0.0;    

            if (brt_badan && tng_badan) {
                imt = tng_badan * tng_badan;
                imt = (brt_badan/imt)*100*100;
                this.form_cppt.imt = imt.toFixed(1);
            }

            if (this.cppt_list?.length > 0) {
                let first_cppt = this.cppt_list[0] 
                if (first_cppt?.icd10) {
                    const mappedIcd10 = first_cppt.icd10.map(item => {
                        return {
                            icd10_id: item.icd10_id,
                            value: item.icd10_id,
                            title: `${item.code} ${item.name}`, 
                            action: "action"
                        };
                    });                
                    this.form_cppt.icd10 = mappedIcd10;
                } 
            } else {
                if (this.soapData?.icd10) {
                    const mappedIcd10 = this.soapData.icd10.map(item => {
                        return {
                            icd10_id: item.icd10_id,
                            value: item.icd10_id,
                            title: `${item.code} ${item.name}`, 
                            action: "action"
                        };
                    });                
                    this.form_cppt.icd10 = mappedIcd10;
                } 
            }

            // if (this.soapData?.icd10) {
            //     const mappedIcd10 = this.soapData.icd10.map(item => {
            //         return {
            //             icd10_id: item.icd10_id,
            //             value: item.icd10_id,
            //             title: `${item.code} ${item.name}`, 
            //             action: "action"
            //         };
            //     });                
            //     this.form_cppt.icd10 = mappedIcd10;
            // } 

            // belom ada dari vital-sign
            // this.form_cppt.imt = obj?.imt || ''
            // this.form_cppt.head_circumference = obj?.head_circumference || ''
            // this.form_cppt.chest_circumference = obj?.chest_circumference || ''
            // this.form_cppt.arm_circumference = obj?.arm_circumference || ''
            // this.form_cppt.stomach_circumference = obj?.stomach_circumference || ''
            // this.form_cppt.knee_circumference = obj?.knee_circumference || ''

            // let text = `Berat Badan: ${obj?.weight || 0} kg,\nTinggi Badan: ${obj?.height || 0} cm,\nSistolik: ${obj?.systolic || 0} mmHg,\nDiastolik: ${obj?.diastolic || 0} mmHg,\nFrekuensi Nadi: ${obj?.pulse || 0} x/mnt,\nSuhu: ${obj?.temperature || 0} ᵒC,\nFrekuensi Nafas: ${obj?.respiratory_rate || 0} x/mnt,\nSaturasi Oksigen: ${obj?.oxygen_saturation || 0} x/mnt,\nSkala Nyeri: ${obj?.sore_score || 0},\nEWS: ${obj?.ews || 0}`
            // this.form_cppt.objective = text
        },

        getSoap() {
            try {
                this.$store.dispatch('soap/getDetail', {registration_destination_id: this.regData.registration_destination_id}).then((response) => {
                    if(response.length == 0) {
                        this.is_soap_null = true
                    } else {
                        this.is_soap_null = false
                        this.soapData = response[0] 
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
                }

            } else {
                this.assignVitalToObjective()
                // this.getNurse(this.reg_des_id)
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
                            this.active.disabledCPPT = true
                            try {
                                this.$store.dispatch('cppt/postPutFullCPPT', this.form_cppt)
                                .then((response) => {
                                    this.notification('Success','CheckIcon','success','You have successfully')
                                    // this.socket.emit('SEND_LAB', []);
                                    // this.socket.emit('SEND_RAD', []);
                                    this.getCPPTList() 
                                    this.active.modal_cppt = false
                                    this.getUsersOption()
                                    
                                    this.resetCpptForm()
                                }) 
                            } catch (error) {
                                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                                console.error('Error at: ', error)
                            } finally {
                                this.active.disabledCPPT = false
                            }
                        } 
                    } else {
                        this.notification('Warning','CloseIcon','warning','Tujuan Order wajib diisi')
                    }
                } else {
                    this.notification('Warning','CloseIcon','warning','Tipe resep wajib diisi')
                }
            } else {
                this.active.disabledCPPT = true
                try {
                    this.$store.dispatch('cppt/postPutFullCPPT', this.form_cppt)
                    .then((response) => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.getCPPTList() 
                        this.active.modal_cppt = false
                        
                        this.resetCpptForm()
                    }) 
                } catch (error) {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    console.error('Error at: ', error)
                } finally {
                    this.active.disabledCPPT = false
                }
            }
        },
        resetCpptForm(){
            this.form_cppt.id = ''
            this.form_cppt.subjective = ''
            this.form_cppt.objective = ''
            this.form_cppt.plan = ''
            this.form_cppt.assessment = ''
            // this.form_cppt.instructions = ''
            this.form_cppt.systolic_blood_pressure = ''
            this.form_cppt.diastolic_blood_pressure = ''
            this.form_cppt.pulse_frequency = ''
            this.form_cppt.breath_frequency = ''
            this.form_cppt.oximetry_frequency = ''
            this.form_cppt.temperature = ''
            this.form_cppt.oxygen_type_id = ''
            this.form_cppt.aware = ''
            this.form_cppt.eye_score = '' 
            this.form_cppt.verbal_score = '' 
            this.form_cppt.motor_score = '' 
            // this.form_cppt.pupil_score = ''
            // this.form_cppt.verbal_response = ''
            // this.form_cppt.blood_sugar = ''
            this.form_cppt.weight = ''
            this.form_cppt.height = ''
            this.form_cppt.imt = ''
            this.form_cppt.head_circumference = ''
            this.form_cppt.chest_circumference = ''
            this.form_cppt.arm_circumference = ''
            this.form_cppt.stomach_circumference = ''
            this.form_cppt.knee_circumference = ''
            this.form_cppt.laboratory_request_items = []
            this.form_cppt.radiology_request_items = []
            this.form_cppt.physiotherapy_request_items = []
            this.form_cppt.form_prescription_parent = []
            this.form_cppt.form_prescription = []
            this.form_cppt.icd10 = []
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
            // this.form_cppt.instructions = obj.instructions
            this.form_cppt.systolic_blood_pressure = obj.systolic_blood_pressure
            this.form_cppt.diastolic_blood_pressure = obj.diastolic_blood_pressure
            this.form_cppt.pulse_frequency = obj.pulse_frequency
            this.form_cppt.breath_frequency = obj.breath_frequency
            this.form_cppt.oximetry_frequency = obj.oximetry_frequency
            this.form_cppt.temperature = obj.temperature
            this.form_cppt.oxygen_type_id = obj.oxygen_type_id
            this.form_cppt.aware = obj.aware
            this.form_cppt.eye_score = obj.eye_score 
            this.form_cppt.verbal_score = obj.verbal_score 
            this.form_cppt.motor_score = obj.motor_score 
            // this.form_cppt.pupil_score = obj.pupil_score
            // this.form_cppt.blood_sugar = obj.blood_sugar
            // this.form_cppt.verbal_response = obj.verbal_response
            this.form_cppt.weight = obj.weight
            this.form_cppt.height = obj.height
            this.form_cppt.imt = obj.imt
            this.form_cppt.head_circumference = obj.head_circumference
            this.form_cppt.chest_circumference = obj.chest_circumference
            this.form_cppt.arm_circumference = obj.arm_circumference
            this.form_cppt.stomach_circumference = obj.stomach_circumference
            this.form_cppt.knee_circumference = obj.knee_circumference

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

            if (obj.physiotherapy_request_items?.length > 0) {
                const mappedPhy = obj.physiotherapy_request_items.map(item => {
                    return {
                        costing_name: item.costing_name,
                        physiotherapy_package_id: item.physiotherapy_package_id,
                        service_class_id:'9b702245-79c5-4beb-8218-53afb08647aa'
                    };
                });                
                this.form_cppt.physiotherapy_request_items = mappedPhy;
            } else {
                this.form_cppt.physiotherapy_request_items = []
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

            if (obj.icd10) {
                const mappedIcd10 = obj.icd10.map(item => {
                    return {
                        icd10_id: item.icd10_id,
                        value: item.icd10_id,
                        title: `${item.code} ${item.name}`, 
                        action: "action"
                    };
                });                
                this.form_cppt.icd10 = mappedIcd10;
            } 
            
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
            // console.log(this.form_cppt.form_prescription_parent);

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
        
        getCPPTList() {
            this.params.registration_id = this.regData.registration_id

            try {            
                this.isDisabledFilter = true    
                this.$store.dispatch('cppt/getListCPPT', this.params).then((response) => {
                    this.cppt_list = response.rows
                    this.totalRows = response.iTotal                        
                }, error => {
                    console.error('Error while cppt/getListCPPT at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET cppt/getListCPPT at: ', error);
            } finally {
                this.isDisabledFilter = false
            }
        },

        getUsersOption() {
            let scope_param = {
                registration_id: this.regData.registration_id
            }

            try {            
                this.$store.dispatch('cppt/getListCPPT', scope_param).then((response) => {
                    let res_list = response.rows

                    this.OptionsPPA = res_list
                        .filter((item, index, self) => 
                            index === self.findIndex(el => el.user_added === item.user_added)
                        )
                        .map(item => ({
                            value: item.user_added,
                            title: item.user_added_name,
                        }));

                    // console.log('this.OptionsPPA:', this.OptionsPPA);
                        
                }, error => {
                    console.error('Error while grouping user_added from cppt/getListCPPT at: ', error);
                })
            } catch (error) {
                console.error('Error catch while grouping user_added from cppt/getListCPPT at: ', error);
            }
        },

        resetFilter() {
            this.params.user_added = ''
            this.params.date_added = ''
            this.getCPPTList()
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
                            this.getCPPTList() 
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
                            this.getCPPTList() 
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
                            this.getCPPTList()
                            this.getUsersOption()
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
            this.cpptSubjective(obj)
            this.cpptObjective(obj)
            this.cpptAssesment(obj)
            this.cpptPlan(obj)
            this.cpptSupport(obj)
            this.cpptPrescription(obj)
            this.cpptInstruction(obj)

            setTimeout(() => {
                button.innerText = 'Copy All';
            }, 1000);
        },

        copySelected(event, func, obj) {
            const button = event.target;
            button.innerText = 'COPIED';
            
            func(obj);

            setTimeout(() => {
                button.innerText = 'COPY';
            }, 1000);
        },

        cpptSubjective(obj) {            
            this.form_cppt.subjective = obj.subjective
        },
        cpptPlan(obj) {            
            this.form_cppt.plan = obj.plan
        },
        
        cpptSupport(obj) {
            if (obj.laboratory_request) {
                let seenLabIds = new Set();

                this.form_cppt.laboratory_request_items = obj.laboratory_request.reduce((acc, req) => {
                    req.laboratory_request_items.forEach(item => {
                        if (!seenLabIds.has(item.laboratory_package_id)) {
                            seenLabIds.add(item.laboratory_package_id);
                            acc.push({
                                costing_name: item.laboratory_package_name,
                                laboratory_package_id: item.laboratory_package_id, 
                                service_class_id: item.service_class_id,
                            });
                        }
                    });
                    return acc;
                }, []);
            }
            
            if (obj.radiology_request) {
                let seenRadIds = new Set();

                this.form_cppt.radiology_request_items = obj.radiology_request.reduce((acc, req) => {
                    req.radiology_request_items.forEach(item => {
                        if (!seenRadIds.has(item.radiology_package_id)) {
                            seenRadIds.add(item.radiology_package_id);
                            acc.push({
                                costing_name: item.radiology_package_name,
                                radiology_package_id: item.radiology_package_id, 
                                service_class_id: item.service_class_id,
                            });
                        }
                    });
                    return acc;
                }, []);
            }
        },

        cpptObjective(obj) {            
            this.form_cppt.objective = obj?.objective
            this.form_cppt.systolic_blood_pressure = obj?.systolic_blood_pressure
            this.form_cppt.diastolic_blood_pressure = obj?.diastolic_blood_pressure
            this.form_cppt.pulse_frequency = obj?.pulse_frequency
            this.form_cppt.breath_frequency = obj?.breath_frequency
            this.form_cppt.oximetry_frequency = obj?.oximetry_frequency
            this.form_cppt.temperature = obj?.temperature
            this.form_cppt.oxygen_type_id = obj?.oxygen_type_id
            this.form_cppt.aware = obj?.aware
            this.form_cppt.eye_score = obj?.eye_score 
            this.form_cppt.verbal_score = obj?.verbal_score 
            this.form_cppt.motor_score = obj?.motor_score 
            // this.form_cppt.pupil_score = obj?.pupil_score
            // this.form_cppt.verbal_response = obj?.verbal_response
            // this.form_cppt.blood_sugar = obj?.blood_sugar
            // this.form_cppt.weight = obj?.weight
            // this.form_cppt.height = obj?.height
            // this.form_cppt.imt = obj?.imt
            // this.form_cppt.head_circumference = obj?.head_circumference
            // this.form_cppt.chest_circumference = obj?.chest_circumference
            // this.form_cppt.arm_circumference = obj?.arm_circumference
            // this.form_cppt.stomach_circumference = obj?.stomach_circumference
            // this.form_cppt.knee_circumference = obj?.knee_circumference
        },
        
        cpptAssesment(obj) {            
            this.form_cppt.assessment = obj.assessment

            if (obj.icd10) {
                const mappedIcd10 = obj.icd10.map(item => {
                    return {
                        icd10_id: item.icd10_id,
                        value: item.icd10_id,
                        title: `${item.code} ${item.name}`, 
                        action: "action"
                    };
                });                
                this.form_cppt.icd10 = mappedIcd10;
            }
        },

        // cpptInstruction(obj) {            
        //     this.form_cppt.instructions = obj.instructions
        // },

        getParentName(code) {
            const parent = {
                '1': 'OBAT SATUAN',
                '2': 'RACIK 1',
                '3': 'RACIK 2',
                '4': 'RACIK 3',
                '5': 'RACIK 4',
            };
            return parent[code] || '-';
        },

        cpptPrescription(obj) {
            if (obj.prescription) {
                let seenParentIds = new Set();
                this.form_cppt.form_prescription_parent = obj.prescription.reduce((acc, req) => {
                    req.form_prescription_parent.forEach(item => {
                        if (!seenParentIds.has(item.id)) {
                            seenParentIds.add(item.id);
                            acc.push({
                                id: item.id,
                                used: item.used, 
                                value: item.value,
                                prescription: this.getParentName(item.id),
                            });
                        }
                    });
                    return acc;
                }, []);

                this.form_cppt.form_prescription = obj.prescription.reduce((acc, req) => {
                    req.form_prescription.forEach(item => {
                        acc.push({
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
                            prescription_type_name: this.getParentName(item.prescription_item_type_id),
                        });
                    });
                    return acc;
                }, []);
            }
        },

        copyAllSoap(event, obj) {
            const button = event.target;
            button.innerText = 'Copied All';
            
            // func(obj);
            this.soapObjective(obj)
            this.soapAssesment(obj)
            this.soapSupport(obj)
            this.soapPrescription(obj)

            setTimeout(() => {
                button.innerText = 'Copy All';
            }, 1000);
        },

        soapObjective(obj) {            
            this.form_cppt.systolic_blood_pressure = obj?.systolic_blood_pressure
            this.form_cppt.diastolic_blood_pressure = obj?.diastolic_blood_pressure
            this.form_cppt.pulse_frequency = obj?.pulse_frequency
            this.form_cppt.breath_frequency = obj?.breath_frequency
            this.form_cppt.oximetry_frequency = obj?.oximetry_frequency
            this.form_cppt.temperature = obj?.temperature
            this.form_cppt.oxygen_type_id = obj?.oxygen_type_id
            this.form_cppt.aware = obj?.aware
            this.form_cppt.eye_score = obj?.eye_score 
            this.form_cppt.verbal_score = obj?.verbal_score 
            this.form_cppt.motor_score = obj?.motor_score 
            // this.form_cppt.pupil_score = obj?.pupil_score
            // this.form_cppt.verbal_response = obj?.verbal_response
            // this.form_cppt.blood_sugar = obj?.blood_sugar
            // this.form_cppt.weight = obj?.weight
            // this.form_cppt.height = obj?.height
            // this.form_cppt.imt = obj?.imt
            // this.form_cppt.head_circumference = obj?.head_circumference
            // this.form_cppt.chest_circumference = obj?.chest_circumference
            // this.form_cppt.arm_circumference = obj?.arm_circumference
            // this.form_cppt.stomach_circumference = obj?.stomach_circumference
            // this.form_cppt.knee_circumference = obj?.knee_circumference
        },

        soapAssesment(obj) {            
            if (obj.icd10) {
                const mappedIcd10 = obj.icd10.map(item => {
                    return {
                        icd10_id: item.icd10_id,
                        value: item.icd10_id,
                        title: `${item.code} ${item.name}`, 
                        action: "action"
                    };
                });                
                this.form_cppt.icd10 = mappedIcd10;
            }
        },

        soapSupport(obj) {
            if (obj.laboratory_request) {
                let seenLabIds = new Set();

                this.form_cppt.laboratory_request_items = obj.laboratory_request.reduce((acc, req) => {
                    req.laboratory_request_items.forEach(item => {
                        if (!seenLabIds.has(item.laboratory_package_id)) {
                            seenLabIds.add(item.laboratory_package_id);
                            acc.push({
                                costing_name: item.laboratory_package_name,
                                laboratory_package_id: item.laboratory_package_id, 
                                service_class_id: item.service_class_id,
                            });
                        }
                    });
                    return acc;
                }, []);
            }
            
            if (obj.radiology_request) {
                let seenRadIds = new Set();

                this.form_cppt.radiology_request_items = obj.radiology_request.reduce((acc, req) => {
                    req.radiology_request_items.forEach(item => {
                        if (!seenRadIds.has(item.radiology_package_id)) {
                            seenRadIds.add(item.radiology_package_id);
                            acc.push({
                                costing_name: item.radiology_package_name,
                                radiology_package_id: item.radiology_package_id, 
                                service_class_id: item.service_class_id,
                            });
                        }
                    });
                    return acc;
                }, []);
            }
        },

        soapPrescription(obj) {
            if (obj.prescription) {
                let seenParentIds = new Set();
                this.form_cppt.form_prescription_parent = obj.prescription.reduce((acc, req) => {
                    req.form_prescription_parent.forEach(item => {
                        if (!seenParentIds.has(item.id)) {
                            seenParentIds.add(item.id);
                            acc.push({
                                id: item.id,
                                used: item.used, 
                                value: item.value,
                                prescription: this.getParentName(item.id),
                            });
                        }
                    });
                    return acc;
                }, []);

                this.form_cppt.form_prescription = obj.prescription.reduce((acc, req) => {
                    req.form_prescription.forEach(item => {
                        acc.push({
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
                            prescription_type_name: this.getParentName(item.prescription_item_type_id),
                        });
                    });
                    return acc;
                }, []);
            }
        },

        nurseObjective(obj) {            
            this.form_cppt.systolic_blood_pressure = obj?.systolic_blood_pressure
            this.form_cppt.diastolic_blood_pressure = obj?.diastolic_blood_pressure
            this.form_cppt.pulse_frequency = obj?.pulse_frequency
            this.form_cppt.breath_frequency = obj?.breath_frequency
            this.form_cppt.oximetry_frequency = obj?.oximetry_frequency
            this.form_cppt.temperature = obj?.temperature
            this.form_cppt.oxygen_type_id = obj?.oxygen_type_id
            this.form_cppt.aware = obj?.aware
            this.form_cppt.eye_score = obj?.eye_score 
            this.form_cppt.verbal_score = obj?.verbal_score 
            this.form_cppt.motor_score = obj?.motor_score 
            // this.form_cppt.pupil_score = obj?.pupil_score
            // this.form_cppt.verbal_response = obj?.verbal_response
            // this.form_cppt.blood_sugar = obj?.blood_sugar
            // this.form_cppt.weight = obj?.weight
            // this.form_cppt.height = obj?.height
            // this.form_cppt.imt = obj?.imt
            // this.form_cppt.head_circumference = obj?.head_circumference
            // this.form_cppt.chest_circumference = obj?.chest_circumference
            // this.form_cppt.arm_circumference = obj?.arm_circumference
            // this.form_cppt.stomach_circumference = obj?.stomach_circumference
            // this.form_cppt.knee_circumference = obj?.knee_circumference
        },

        openLabResult(results){
            console.log(results);
            this.active.modal_res_lab = !this.active.modal_res_lab
            this.labResults = results
        },
        
        // linkToLabResult(laboratory_request_id){
        //     const url = this.$router.resolve({ path: '/laboratory/print/'+ laboratory_request_id }).href;
        //     window.open(url, '_blank');
        // },

        getCpptHistory(norm) {
            const param = { norm: norm }
            try {                
                this.$store.dispatch('cppt/getHistoryCPPT', param).then((response) => {
                    this.cppt_history = response
                    // console.log('this.cppt_history:', this.cppt_history);
                }, error => {
                    console.error('Error while cppt/getListCPPT at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET cppt/getListCPPT at: ', error);
            }
        },

        getNurseHistory(norm) {
            const param = { norm: norm }
            try {                
                this.$store.dispatch('nurse/getHistoryNurse', param).then((response) => {
                    this.nurse_history = response
                    // console.log('this.nurse_history:', this.nurse_history);
                }, error => {
                    console.error('Error while nurse/getHistoryNurse at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET nurse/getHistoryNurse at: ', error);
            }
        },

        getSoapHistory(norm) {
            const param = { norm: norm }
            try {                
                this.$store.dispatch('soap/getHistorySOAP', param).then((response) => {
                    this.soap_history = response
                    // console.log('this.soap_history:', this.soap_history);
                }, error => {
                    console.error('Error while soap/getHistorySOAP at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET soap/getHistorySOAP at: ', error);
            }
        },
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