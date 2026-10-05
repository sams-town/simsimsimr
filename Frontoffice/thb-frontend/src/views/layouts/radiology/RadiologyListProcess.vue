<template>
    <div>
        <div class="col label-head">
            <span class="px-16 text-medium-24 text-neutral-500">DATA PERMINTAAN RADIOLOGI PASIEN</span>
        </div>
        <section class="col mt-32">
            <b-table-simple responsive class="text-medium-14">
                <b-tbody>
                    <b-tr>
                        <b-td style="width: 10%;">No. Reg / RM</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{regData.code}} / {{regData.norm}}</b-td>
                        <b-td style="width: 10%;">Unit</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">RADIOLOGI</b-td>
                    </b-tr>
                    <b-tr>
                        <b-td style="width: 10%;">Nama Pasien</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{regData.name}}</b-td>
                        <b-td style="width: 10%;">No. Order</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{orderData.no_request}}</b-td>
                    </b-tr>
                    <b-tr>
                        <b-td style="width: 10%;">Tanggal Lahir</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{regData.birth_date}}</b-td>
                        <b-td style="width: 10%;">Tgl. Permintaan</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{orderData.date_request}}</b-td>
                    </b-tr>
                    <b-tr>
                        <b-td style="width: 10%;">Kelamin</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{regData.sex_name}}</b-td>
                        <b-td style="width: 10%;">Dokter Pengirim</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{regData.doctor_in_charge_name}}</b-td>
                    </b-tr>
                    <b-tr>
                        <b-td style="width: 10%;">Usia Pasien</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{parsed_age(regData.birth_date)}}</b-td>
                    </b-tr>
                </b-tbody>
            </b-table-simple>
        </section>
        <section class="col mt-40">
            <b-table-simple responsive bordered>
                <b-thead>
                    <b-tr>
                        <b-th>NO</b-th>
                        <b-th>TGL. PERMINTAAN</b-th>
                        <b-th>TINDAKAN RADIOLOGI</b-th>
                        <b-th>HARGA TINDAKAN</b-th>
                        <b-th>ACTION</b-th>
                        <b-th>STATUS BILLING
                        </b-th>
                    </b-tr>
                </b-thead>
                <b-tbody>
                    <template v-for="(item,index) in orderData.radiology_request_item">
                        <b-tr>
                            <b-td :rowspan="item.url.length != 0 ? 2 : 1"> {{ item.no }} </b-td>
                            <b-td>{{ item.date_request }}</b-td>
                            <b-td>{{ item.costing_name }}</b-td>
                            <b-td>{{ item.var_1 | currency }}</b-td>
                            <b-td>
                                <template v-if="item.status == '1'">Permintaan Baru</template>
                                <template v-else-if="item.status == '2'">
                                    <div class="row">
                                        <div class="col-7">
                                            <b-form-file v-model="input_image" accept="image/*" multiple @change="inputImageUSG($event, item.id)" autocomplete="off"></b-form-file>
                                        </div>
                                        <template v-if="item.url.length != 0">
                                            <b-button @click="isRefcontrol(urlbe,item.url, null)" variant="primary-600">Lihat Semua Hasil</b-button>
                                        </template>
                                    </div>
                                </template>
                            </b-td>
                            <b-td>
                                <b-badge variant="warning-500" class="p-1">
                                    <feather-icon icon="CheckSquareIcon" />
                                </b-badge>
                            </b-td>
                        </b-tr>
                        <b-tr v-if="item.url.length != 0">
                            <b-td colspan="5">
                                <b-table-simple bordered responsive class="w-100" style="font-size: 14px;">
                                    <b-thead>
                                        <b-tr>
                                            <b-th style="width: 300px;">Nama Gambar</b-th>
                                            <b-th>Catatan</b-th>
                                            <b-th style="width: 300px;">Aksi</b-th>
                                        </b-tr>
                                    </b-thead>
                                    <b-tbody>
                                        <b-tr v-for="(list, index) in item.url" :key="index">
                                            <b-td>
                                                <b-img rounded :src="urlbe+list.url" style="width: 100%;" />
                                                <div>{{ list.name ? list.name : `Image-${index+1}` }}</div>
                                            </b-td>
                                            <b-td>
                                                <table class="w-100 custom-border" >
                                                    <tr>
                                                        <td style="width: 150px;">Nama Gambar</td>
                                                        <td class="align-top text-center" style="width: 30px;">:</td>
                                                        <td>{{ list.name ? list.name : `Image-${index+1}` }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Pemeriksa Radiologi</td>
                                                        <td class="align-top text-center">:</td>
                                                        <td>{{ list.user_lab_name ? list.user_lab_name : '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Verifikator</td>
                                                        <td class="align-top text-center">:</td>
                                                        <td>{{ list.doctor_lab_name ? list.doctor_lab_name : '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Tanggal Order</td>
                                                        <td class="align-top text-center">:</td>
                                                        <td>{{ list.date_request ? localDate(list.date_request) : '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Tanggal Diproses</td>
                                                        <td class="align-top text-center">:</td>
                                                        <td>{{ list.date_process ? localDate(list.date_process) : '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Tanggal Hasil</td>
                                                        <td class="align-top text-center">:</td>
                                                        <td>{{ list.date_result ? localDate(list.date_result) : '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Expertise</td>
                                                        <td class="align-top text-center">:</td>
                                                        <td><span v-html="parsedBody(list.conclusion ? list.conclusion : '-')"></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Kesan</td>
                                                        <td class="align-top text-center">:</td>
                                                        <td><span v-html="parsedBody(list.advice ? list.advice : '-')"></span></td>
                                                    </tr>
                                                </table>
                                                <!-- <div><b>Nama Gambar: </b><span>{{ list.name ? list.name : `Image-${index+1}` }}</span></div>
                                                <div><b>Pemeriksa RADIOLOGI: </b><span>{{ list.user_lab_name ? list.user_lab_name : '-' }}</span></div>
                                                <div><b>Verifikator: </b><span>{{ list.doctor_lab_name ? list.doctor_lab_name : '-' }}</span></div>
                                                <div><b>Tanggal Order: </b><span>{{ list.date_request ? localDate(list.date_request) : '-' }}</span></div>
                                                <div><b>Tanggal Diproses: </b><span>{{ list.date_process ? localDate(list.date_process) : '-' }}</span></div>
                                                <div><b>Tanggal Hasil: </b><span>{{ list.date_result ? localDate(list.date_result) : '-' }}</span></div>
                                                <div><b>Expertise: </b><span>{{ list.conclusion ? list.conclusion : '-' }}</span></div>
                                                <div><b>Saran: </b><span>{{ list.advice ? list.advice : '-' }}</span></div> -->
                                            </b-td>
                                            <b-td>
                                                <div class="d-flex">
                                                    <b-button @click="isRefcontrol(urlbe,item.url, index)" size="sm" variant="primary-600">Lihat</b-button>
                                                    <template v-if="orderData.status == '2' || orderData.status == '3'">
                                                        <b-button @click="onNotesModal(list)" variant="primary" size="sm" class="ml-1">Isi Expertise</b-button>
                                                    </template>
                                                    <template v-if="list.status == 2">
                                                        <router-link :to="{ path: '/radiology/list/process/print-expertise/'+ id + '/' + list.radiology_request_item_id + '/' + list.id }" target="_blank">
                                                            <b-button variant="success-500" size="sm" class="ml-1">Cetak</b-button>
                                                        </router-link>
                                                    </template>
                                                    <b-button @click="deleteImage(list)" variant="danger-500" size="sm" class="ml-1">Hapus</b-button>
                                                </div>
                                            </b-td>
                                        </b-tr>
                                    </b-tbody>
                                </b-table-simple>
                            </b-td>
                        </b-tr>
                    </template>
                </b-tbody>
            </b-table-simple>
        </section>
        <b-modal v-model="active.refsoap" scrollable size="xl" @close="isRefsoap()" title="Assesment" hide-footer>
            <template v-for="(result,i) in itemsSoap" >
                <div class="card-body mt-20 shadow-sm">
                    <section class="card mt-24">
                        <div class="card-header bg-white">
                            <section>
                                <ModernLabel label="Soap Dokter" :small="`Added on ${result.date_added} Oleh ${result.employee_doctor_name}`" />
                            </section>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <section class="col-lg-3 mt-16 mt-lg-0">
                                    <div class="text-uppercase text-bold-16 text-success-500">Subjective</div>
                                    <div class="mt-12">
                                        <b-table-simple small class="text-regular-12">
                                            <b-tr>
                                                <div class="text-uppercase text-bold-12 text-primary-500">Keluhan Utama	 :</div>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>{{result.main_complaint ? result.main_complaint : '-' }}</b-td>
                                            </b-tr>
                                        </b-table-simple>
                                    </div>
                                    <div class="mt-12">
                                        <b-table-simple small class="text-regular-12">
                                            <b-tr>
                                                <div class="text-uppercase text-bold-12 text-primary-500">Riwayat Penyakit Sekarang	:</div>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>{{ result.history_disease_now ? result.history_disease_now : '-' }}</b-td>
                                            </b-tr>
                                        </b-table-simple>
                                    </div>
                                    <div class="mt-12">
                                        <b-table-simple small class="text-regular-12">
                                            <b-tr>
                                                <div class="text-uppercase text-bold-12 text-primary-500">Riwayat Penyakit Dahulu :</div>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>{{ result.history_disease_past ? result.history_disease_past : '-' }}</b-td>
                                            </b-tr>
                                        </b-table-simple>
                                    </div>
                                    <div class="mt-12">
                                        <b-table-simple small class="text-regular-12">
                                            <b-tr>
                                                <div class="text-uppercase text-bold-12 text-primary-500">Riwayat Obat :</div>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>{{ result.history_drugs ? result.history_drugs : '-' }}</b-td>
                                            </b-tr>
                                        </b-table-simple>
                                    </div>
                                </section>
                                <section class="col-lg-3 mt-16 mt-lg-0">
                                    <div class="text-uppercase text-bold-16 text-success-500">Objective</div>
                                    <div class="mt-12">
                                        <b-table-simple small class="text-regular-12">
                                            <b-tr>
                                                <div class="text-uppercase text-bold-12 text-primary-500">Vitals :</div>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    Systolic = {{ result.systolic_blood_pressure ? result.systolic_blood_pressure : '0' }},
                                                    Diastolic = {{ result.diastolic_blood_pressure ? result.diastolic_blood_pressure : '0' }},
                                                    Frekuensi Nadi = {{ result.pulse_frequency ? result.pulse_frequency : '0' }},
                                                    Frekuensi Nafas = {{ result.breath_frequency ? result.breath_frequency : '0' }},
                                                    Saturasi Oksigen = {{ result.oximetry_frequency ? result.oximetry_frequency : '0' }},
                                                    Suhu = {{ result.temperature ? result.temperature : '0' }},
                                                    Gula Darah = {{ result.blood_sugar ? result.blood_sugar : '0' }}
                                                </b-td>
                                            </b-tr>
                                        </b-table-simple>
                                    </div>
                                    <div class="mt-12">
                                        <b-table-simple small class="text-regular-12">
                                            <b-tr>
                                                <div class="text-uppercase text-bold-12 text-primary-500">Antropometri :</div>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    Berat Badan = {{ result.weight ? result.weight : '0' }},
                                                    Tinggi Badan = {{ result.height ? result.height : '0' }},
                                                    IMT = {{ result.imt ? result.imt : '0' }},
                                                    Lingkar Kepala = {{ result.head_circumference ? result.head_circumference : '0' }},
                                                    Lingkar Dada = {{ result.chest_circumference ? result.chest_circumference : '0' }},
                                                    Lingkar Lengan Atas = {{ result.arm_circumference ? result.arm_circumference : '0' }},
                                                    Lingkar Perut = {{ result.stomach_circumference ? result.stomach_circumference : '0' }},
                                                    Lingkar Lutut = {{ result.knee_circumference ? result.knee_circumference : '0' }}
                                                </b-td>
                                            </b-tr>
                                        </b-table-simple>
                                    </div>
                                    <div class="mt-12">
                                        <b-table-simple small class="text-regular-12">
                                            <b-tr>
                                                <div class="text-uppercase text-bold-12 text-primary-500">Pemeriksaan Fisik :</div>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>{{ result.physical_check ? result.physical_check : '-' }}</b-td>
                                            </b-tr>
                                        </b-table-simple>
                                    </div>
                                    <div class="mt-12">
                                        <b-table-simple small class="text-regular-12">
                                            <b-tr>
                                                <div class="text-uppercase text-bold-12 text-primary-500">Lainnya :</div>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>{{ result.soap_o_anythingelse ? result.soap_o_anythingelse : '-' }}</b-td>
                                            </b-tr>
                                        </b-table-simple>
                                    </div>
                                </section>
                                <section class="col-lg-3 mt-16 mt-lg-0">
                                    <div class="text-uppercase text-bold-16 text-success-500">Assesment</div>
                                    <div class="mt-12">
                                        <b-table-simple small class="text-regular-12">
                                            <b-tr>
                                                <div class="text-uppercase text-bold-12 text-primary-500">Diagnosis :</div>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <!-- Utama: K59.0 Constipation <br> Tambahan: E66.0 Obesiti due to excess calories -->
                                                    <div v-if="result.icd10 && result.icd10.length != 0">
                                                        <template v-for="(iicd, index) in result.icd10">
                                                            <div v-if="index === 0">
                                                                Utama: {{ iicd.code }} {{ iicd.name }}
                                                            </div>
                                                        </template>
                                                        <template v-for="(iicd, index) in result.icd10">
                                                            <div v-if="index > 0">
                                                                Tambahan: {{ iicd.code }} {{ iicd.name }} <br>
                                                            </div>
                                                            <div v-else-if="index < 1">
                                                                Tambahan: -
                                                            </div>
                                                        </template>
                                                    </div>
                                                    <div v-else>
                                                        <div>Utama: -</div>
                                                        <div>Tambahan: -</div>
                                                    </div>
                                                </b-td>
                                            </b-tr>
                                        </b-table-simple>
                                    </div>
                                </section>
                                <section class="col-lg-3 mt-16 mt-lg-0">
                                    <div class="text-uppercase text-bold-16 text-success-500">Planning</div>
                                    <div class="mt-12">
                                        <b-table-simple small class="text-regular-12">
                                            <b-tr>
                                                <div class="text-uppercase text-bold-12 text-primary-500">Procedure/Tindakan :</div>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <!-- Utama: K59.0 Constipation <br> Tambahan: E66.0 Obesiti due to excess calories -->
                                                    <div v-if="result.transaction_doctor && result.transaction_doctor.length != 0">
                                                        <li v-for="(itd, index) in result.transaction_doctor" class="font-weight-bold">
                                                            {{ itd.costing_name }}
                                                        </li>
                                                    </div>
                                                    <div v-else>-</div>
                                                </b-td>
                                            </b-tr>
                                        </b-table-simple>
                                    </div>
                                    <div class="mt-12">
                                        <b-table-simple small class="text-regular-12">
                                            <b-tr>
                                                <div class="text-uppercase text-bold-12 text-primary-500">ICD 9 :</div>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <!-- Utama: K59.0 Constipation <br> Tambahan: E66.0 Obesiti due to excess calories -->
                                                    <div v-if="result.icd9cm && result.icd9cm.length != 0">
                                                        <li v-for="(iicd9, index) in result.icd9cm" class="font-weight-bold">
                                                            {{ iicd9.code }} {{ iicd9.name }}
                                                        </li>
                                                    </div>
                                                    <div v-else>-</div>
                                                </b-td>
                                            </b-tr>
                                        </b-table-simple>
                                    </div>
                                    <div class="mt-12">
                                        <b-table-simple small class="text-regular-12">
                                            <b-tr>
                                                <div class="text-uppercase text-bold-12 text-primary-500">Pemeriksaan Penunjang :</div>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <div v-if="result.laboratory_request_items && result.laboratory_request_items.length != 0">
                                                        <div class="font-weight-bold">LABORATORIUM :</div>
                                                        <li v-for="(il, index) in result.laboratory_request_items">
                                                            {{ il.code }} {{ il.costing_name }} 
                                                            <router-link :to="{ path: '/laboratory/print/'+ il.laboratory_request_id}" target="_blank">
                                                                <b-button size="sm" variant="outline-secondary" class="ml-2">
                                                                    <feather-icon icon="EyeIcon" /> Lihat Hasil
                                                                </b-button>
                                                            </router-link>
                                                        </li>
                                                    </div>
                                                    <div v-else class="font-weight-bold">LABORATORIUM : - </div>
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <div v-if="result.radiology_request_items && result.radiology_request_items.length != 0">
                                                        <div class="font-weight-bold">RADIOLOGI :</div>
                                                        <li v-for="(ir, index) in result.radiology_request_items">
                                                            {{ ir.code }} {{ ir.costing_name }}
                                                            <b-button size="sm" variant="outline-secondary" class="ml-2" @click="isRefcontrolSoap(urlbe,ir.url)"><feather-icon icon="EyeIcon" /> Lihat Hasil</b-button>
                                                        </li>
                                                    </div>
                                                    <div v-else class="font-weight-bold">RADIOLOGI : -</div>
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <div v-if="result.physiotherapy_request && result.physiotherapy_request.length != 0">
                                                        <div class="font-weight-bold">FISIOTERAPI :</div>
                                                        <!-- <template v-if="result.physiotherapy_request.physiotherapy_request_items"> -->
                                                            <template v-for="(ip, index) in result.physiotherapy_request">
                                                                <li v-for="list in ip.physiotherapy_request_items">{{ list.costing_name }} </li>
                                                            </template>
                                                        <!-- </template> -->
                                                    </div>
                                                    <div v-else class="font-weight-bold">FISIOTERAPI : -</div>
                                                </b-td>
                                            </b-tr>
                                        </b-table-simple>
                                    </div>
                                    <div class="mt-12">
                                        <b-table-simple small class="text-regular-12">
                                            <b-tr>
                                                <div class="text-uppercase text-bold-12 text-primary-500">Prescription :</div>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <div v-if="result.form_prescription && result.form_prescription != 0">
                                                        <li v-for="(iip, index) in result.form_prescription">
                                                            {{ iip.pharmacy_catalog_name }}, {{ iip.qty }} {{ iip.package_name }}, {{ iip.used }}, {{ iip.desc }} <br>
                                                        </li>
                                                    </div>
                                                    <div v-else>-</div>
                                                </b-td>
                                            </b-tr>
                                        </b-table-simple>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </section>
                </div>
            </template>
        </b-modal>
        <b-modal v-model="active.refcontrol" scrollable size="xl" @close="isRefcontrol()" title="Hasil Radiologi" hide-footer>
            <div class="row">
                <div class="col-12 mt-1">
                    <template v-for="(img,index) in imageview">
                        <b-img rounded :src="urlbe+img.url" style="width: 100%;"/>
                    </template>
                </div>
            </div>
        </b-modal>
        <b-modal v-model="active.notes_modal" scrollable size="lg" @close="onNotesModal({})" title="Isi Expertise" hide-footer>
            <div>
                <div>
                    <label class="c-form-label">Ganti Gambar</label>
                    <b-form-file accept="image/*" @change="inputImageAtModal" autocomplete="off"></b-form-file>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Pemeriksa RADIOLOGI:</label>
                    <v-select v-model="form.user_lab_id" aria-readonly="true" :options="OptionsDoctor" :reduce="(OptionsDoctor) => OptionsDoctor.value" label="title" placeholder="-- Dokter --"></v-select>
                    <!-- <v-select v-model="form.user_lab_id" aria-readonly="true" :options="OptionsLabor" :reduce="(OptionsLabor) => OptionsLabor" label="title" placeholder="-- Dokter --"></v-select> -->
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Verifikator:</label>
                    <v-select v-model="form.doctor_lab_id" aria-readonly="true" :options="OptionsDoctor" :reduce="(OptionsDoctor) => OptionsDoctor.value" label="title" placeholder="-- Dokter --"></v-select>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Tanggal Diproses</label>
                    <b-form-input v-model="form.date_process" type="date"></b-form-input>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Tanggal Hasil</label>
                    <b-form-input v-model="form.date_result" type="date"></b-form-input>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Expertise:</label>
                    <b-textarea v-model="form.conclusion" rows="2"></b-textarea>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Kesan :</label>
                    <b-textarea v-model="form.advice" rows="2"></b-textarea>
                </div>
            </div>
            <div class="modal-footer mt-24">
                <template>
                    <b-button size="sm" @click.prevent="validationFormVerifikasi()" variant="orange-400" class="text-white">
                        <feather-icon icon="SaveIcon" /> Verifikasi
                    </b-button>
                </template>
            </div>
        </b-modal>
        <b-modal v-model="active.refcontrolsoap" scrollable size="xl" @close="isRefcontrolSoap()" title="Hasil Radiologi" hide-footer>
            <div>
                <b-table :fields="radiologyResultField" :items="imageview" bordered responsive small style="font-size: 14px;">
                    <template #cell(no)="row">
                        <div>{{ row.index+1 }}</div>
                    </template>
                    <template #cell(expertise)="row">
                        <div>
                            <div>Pemeriksa Radiologi: {{ row.item.user_lab_name }}</div>
                            <div>Verifikator: {{ row.item.doctor_lab_name }}</div>
                            <div>Tanggal Order: {{ row.item.date_order ? localDate(row.item.date_order) : '' }}</div>
                            <div>Tanggal Diproses: {{ row.item.date_process ? localDate(row.item.date_process) : '' }}</div>
                            <div>Expertise: {{ row.item.conclusion }}</div>
                            <div>Kesan: {{ row.item.advice }}</div>
                            <!-- <div>Catatan: {{ row.item.notes }}</div> -->
                        </div>
                    </template>
                    <template #cell(url)="data">
                        <div>
                            <b-img rounded :src="urlbe+data.value" style="width: 100%;" />
                        </div>
                    </template>
                </b-table>
            </div>
        </b-modal>
        <section class="col mt-24">
            <div class="row mx-0">
                <!-- <b-button size="sm" variant="primary-500" class="mr-2">
                    <feather-icon icon="PrinterIcon" /> Print All
                </b-button> -->
                <router-link :to="{ path: '/patient/radiology/'+ regData.registration_destination_id}" class="rounded-pill" >
                    <b-button size="sm" variant="outline-secondary" class="mr-2">
                        <feather-icon icon="FileTextIcon" /> Tambah Tindakan
                    </b-button>
                </router-link>
                <template v-if="orderData.status == '1'">
                    <b-button size="sm" :disabled="isDisabled"  @click.prevent="validationForm(id)" variant="orange-400" class="text-white">
                        <feather-icon icon="SaveIcon" /> Prosess
                    </b-button>
                </template>
                <!-- <template v-if="orderData.status == '2'">
                    <b-button size="sm" :disabled="isDisabled"  @click.prevent="validationFormVerifikasi(id)" variant="orange-400" class="text-white">
                        <feather-icon icon="SaveIcon" /> Verifikasi
                    </b-button>
                </template> -->
                <b-button @click="isRefsoap(null)" size="sm" class="ml-1" variant="primary-600">Lihat Assesment Dokter</b-button>
                <template v-if="orderData.status == '3'">
                    <router-link :to="{ path: '/radiology/print/'+ id}">
                        <b-button size="sm" variant="outline-secondary" class="mr-2">
                            <feather-icon icon="PrinterIcon" /> Cetak
                        </b-button>
                    </router-link>
                </template>
                <!-- <router-link :to="{ name: 'radiologyListProcessOrderView' }">
                    <b-button size="sm" variant="outline-secondary">
                        <feather-icon icon="PrinterIcon" /> Cetak Order
                    </b-button>
                </router-link> -->
            </div>
        </section>
    </div>
</template>

<script>
import { mapState } from 'vuex';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import vSelect from 'vue-select'
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import moment from 'moment'
// import io from 'socket.io-client';

export default {
    components: {
      vSelect, ToastificationContent,ModernLabel
    },
    data() {
        return {
            // socket : io(process.env.VUE_APP_SOCKET_IO, { transports: ["websocket", "polling", "flashsocket"],withCredentials: true }),
            radiologyResultField: [
                { key: 'no', label: 'No', thStyle:'width: 50px' },
                { key: 'expertise', label: 'Expertise', thStyle:'width: 500px' },
                { key: 'url', label: 'Gambar' },
            ],
            input_image: null,
            imageview:[],
            active:{
                refsoap:false,
                refcontrol:false,
                refcontrolsoap:false,
                notes_modal:false,
            },
            urlbe:process.env.VUE_APP_IMAGE,
            radiology_action: {
                field: [
                    { key: 'no', label: 'NO' },
                    { key: 'date_request', label: 'TGL. PERMINTAAN' },
                    { key: 'costing_name', label: 'TINDAKAN RADIOLOGI' },
                    { key: 'var_1', label: 'HARGA TINDAKAN' },
                    { key: 'status', label: 'ACTION' },
                    { key: 'bill_status', label: 'STATUS BILLING' },
                ],
            },
            form:{
                id:'',
                result: '',
                extension: '',
                status: 2,
                date_process: moment(new Date()).format('YYYY-MM-DD'),
                date_result: moment(new Date()).format('YYYY-MM-DD'),
                doctor_lab_id: '',
                user_lab_id:'',
                conclusion:'',
                notes:'',
                advice:'',
            },
            result:{
                file_url:'',
                file_set:'none',
                file:null,
            },
            imageview:{},
            orderData:{},
            id:'',
            isDisabled:false,
            regData:{},
        }
    },
    filters:{
        currency(value) {
            return new Intl.NumberFormat("en-US", { style: "currency", currency: "IDR" }).format(value)
        }
    },
    computed: {
        ...mapState({
            itemsSoap: state => state.soap.items, 
            OptionsDoctor: state => state.employee.options.employee,
            // OptionsLabor: state => state.employee.options.rador,
        }),
    },
    methods: {
        isRefcontrolSoap(url,data) {
            this.imageview = ''
            this.imageview = data
            this.active.refcontrolsoap = !this.active.refcontrolsoap
        },
        parsedBody(text){
            return String(text || '').replace(/\n/g, "<br>");
        },
        onNotesModal(obj) {
            this.active.notes_modal = !this.active.notes_modal

            if (this.active_modal == false) {
                // this.form.result = ''
                // this.form.extension = ''
                // this.form.status = 2
                // this.form.notes =''
                this.form.id = ''
                this.form.doctor_lab_id = ''
                this.form.user_lab_id =''
                this.form.conclusion =''
                this.form.advice =''
            } else {
                // this.form.result = ''
                // this.form.extension = ''
                // this.form.status = 2
                // this.form.notes = obj.notes
                this.form.id = obj.id
                this.form.doctor_lab_id = obj.doctor_lab_id
                this.form.user_lab_id = obj.user_lab_id
                this.form.conclusion = obj.conclusion
                this.form.advice = obj.advice
            }
        },
        deleteImage(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus ${obj.name} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('radiology/removeImage', obj.id)
                    .then(response => {
                        if (response.status === 200 || response.status === 201) {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.getData(this.$route.params.id)
                        } else {
                            this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        }
                    },
                    error => {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error While Delete Using radiology/removeImage at: ', error)
                    }) 
                }
            })
        },
        isRefcontrol(url,data,index) {
            this.imageview = []
            if (index == null) {
                this.imageview = data
            } else {
                this.imageview.push(data[index])
                // console.log('this.imageview', this.imageview);
            }
            this.active.refcontrol = !this.active.refcontrol
        },
        isRefsoap(url,data,index) {
            this.active.refsoap = !this.active.refsoap
        },
        inputImageUSG (e,id) {
            this.imageUSG = true
            // console.log(e)
            for (let i = 0; i < e.target.files.length; i++) {
                const file = e.target.files[i]
                const reader = new FileReader()
                reader.onload = (filez) => {
                    this.result.file = filez.target.result.replace("data:"+ file.type +";base64,", '');
                    this.$store.dispatch('radiology/sendFile', {id:id, result:this.result.file, name:file.name, extension:file.type })
                    .then(data => {
                        this.getData(this.$route.params.id)
                        this.input_image = null
                    })
                }
                reader.readAsDataURL(file)
            }
            // this.result.file_set = 'upload'
            // this.result.file_url = URL.createObjectURL(e.target.files[0])
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
			this.$store.dispatch('radiology/getByIdOrder', id).then(data => {
                // console.log('all data', data);
                this.orderData = data[0]
                this.regData = data[0].regData
                this.$store.dispatch('soap/getByRM', this.regData.norm)
                this.form.conclusion = this.orderData.conclusion
                this.form.notes = this.orderData.notes
                this.form.advice = this.orderData.advice
                // this.form.user_lab_id = {value:this.orderData.user_lab_id,title:this.orderData.user_lab_name}
                // this.form.doctor_lab_id = {value:this.orderData.doctor_lab_id,title:this.orderData.doctor_lab_name}
            })
        },
        inputImageAtModal (e) {
            console.log(e)
            for (let i = 0; i < e.target.files.length; i++) {
                const file = e.target.files[i]
                const reader = new FileReader()
                reader.onload = (filez) => {
                    this.form.result = filez.target.result.replace("data:"+ file.type +";base64,", '');
                    this.form.extension = file.type
                }
                reader.readAsDataURL(file)
            }
        },
        validationForm (id) {
            this.isDisabled = true
            const formData = {id:id}
            this.$store.dispatch('radiology/formPostPutProcess', formData)
            .then(res => {
                this.notification('Success','CheckIcon','success','You have successfully')
                this.getData(this.$route.params.id)
                this.isDisabled = false
                // this.sendNextStep(this.orderData.date_request, this.orderData.queue)
            }, 
            err => {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                this.isDisabled = false
            })
        },

        // sendNextStep(date_request, queue) {
        //     let params = {
        //         date: moment(date_request).format('YYYY-MM-DD'),
        //         queue: queue
        //     }

        //     try {
        //         this.$store.dispatch('queue/postPutQueueNextRad', params)
        //         .then(
        //             res => {
        //                 this.socket.emit('SEND_RAD', params);
        //             } 
        //         )
        //         .catch(({error, response}) => {
        //             this.notification('Failed','CloseIcon','danger',`${error}`)
        //         })
        //     } catch (error) {
        //         this.notification('Failed','CloseIcon','danger','You have unsuccessful')
        //         console.error('Error catch at: ', error)                
        //     }
        // },

        validationFormVerifikasi () {
            this.isDisabled = true
            const formData = this.form
            this.$store.dispatch('radiology/formPutRadiologyImage', formData)
            .then(res => {
                this.notification('Success','CheckIcon','success','You have successfully')
                this.getData(this.$route.params.id)
                this.isDisabled = false
                
                this.active.notes_modal = false
                this.form.id = ''
                this.form.result = ''
                this.form.extension = ''
                this.form.status = 2
                this.form.date_process = moment(new Date()).format('YYYY-MM-DD')
                this.form.date_result = moment(new Date()).format('YYYY-MM-DD')
                this.form.doctor_lab_id =''
                this.form.user_lab_id =''
                this.form.conclusion =''
                this.form.advice = ''
                // this.form.notes =''

                // this.sendNextStep(this.orderData.date_request, this.orderData.queue)
            }, 
            err => {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                this.isDisabled = false
            })
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
        localDate(input_date) {
            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },
    },
    created () {
        if (this.$route.params.id) {
            this.id = this.$route.params.id
            this.getData(this.$route.params.id)
        }
        this.$store.dispatch('employee/getParamSpecialistRad')
        // this.$store.dispatch('employee/getParamRador')
    }
}
</script>
<style scoped>
.label-head::before {
    content: '';
    border-left: 6px solid #648DE1;
}
table.custom-border tr td {
    border: 0px;
    padding: 4px;
}
</style>
<style lang="scss">
@import "@core/scss/vue/libs/vue-select.scss";
</style>