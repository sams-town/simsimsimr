<template>
    <div>
        <patientMenu selectedMenu="menu21" />
        <div class="label-head my-16 my-lg-24">
            <span class="px-16 text-bold-24 text-neutral-700">Triase</span>
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
            <b-button pill @click="goToTab('ADD')" :variant="active.tab == 'ADD' ? 'primary' : 'outline-primary'" class="px-24 ml-2">Tambah Triase</b-button>
        </div>

        <section v-if="active.tab == 'LIST'" class="card-body mt-32 shadow-sm">
            <b-table :fields="tableFields" :items="triageData" bordered >
                <template #cell(no)="row">
                    <div class="text-center">
                        {{ row.index+1 }}
                    </div>
                </template>
                <template #cell(action)="row">
                    <div class="d-flex">
                        <b-button @click="onEditform(row.item)" size="sm" variant="warning">
                            <feather-icon icon="EditIcon" class="mr-1"/> Edit
                        </b-button>
                        <b-button @click="deleteRow(row.item.id)" size="sm" variant="danger" class="ml-2">
                            <feather-icon icon="TrashIcon" class="mr-1"/> Hapus
                        </b-button>
                    </div>
                </template>
            </b-table>
        </section>

        <section v-if="active.tab == 'ADD'" class="card-body mt-32 shadow-sm">
            <div>
                <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0">Triase</div>
                <!-- <section class="row">
                    <div class="col-lg-3 mt-12">
                        <label class="c-form-label">Frekuensi Nafas</label>
                        <b-input-group append="x/mnt">
                            <b-form-input type="number" autocomplete="off" v-model="form.breath_frequency"></b-form-input>
                        </b-input-group>
                    </div>
                    <div class="col-lg-3 mt-12">
                        <label class="c-form-label">Systolic</label>
                        <b-input-group append="mmHg">
                            <b-form-input type="number" autocomplete="off" v-model="form.systolic_blood_pressure"></b-form-input>
                        </b-input-group>
                    </div>
                    <div class="col-lg-3 mt-12">
                        <label class="c-form-label">Diastolic</label>
                        <b-input-group append="mmHg">
                            <b-form-input type="number" autocomplete="off" v-model="form.diastolic_blood_pressure"></b-form-input>
                        </b-input-group>
                    </div>
                    <div class="col-lg-3 mt-12">
                        <label class="c-form-label">Frekuensi Nadi</label>
                        <b-input-group append="x/mnt">
                            <b-form-input type="number" autocomplete="off" v-model="form.pulse_frequency"></b-form-input>
                        </b-input-group>
                    </div>
                    <div class="col-lg-3 mt-12">
                        <label class="c-form-label">Suhu</label>
                        <b-input-group append="ᵒC">
                            <b-form-input type="number" autocomplete="off" v-model="form.temperature"></b-form-input>
                        </b-input-group>
                    </div>
                </section> -->

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
                            <v-select v-model="form.eye_response" aria-readonly="true" :options="OptionsEye" :reduce="OptionsEye => OptionsEye.value" label="title"
                                placeholder="-- Pilih --"></v-select>
                        </div>
                        <div class="col-lg-3 mt-12 mt-lg-0">
                            <label class="c-form-label">Verbal (Respon verbal)</label>
                            <v-select v-model="form.verbal_response" aria-readonly="true" :options="OptionsVerbal" :reduce="OptionsVerbal => OptionsVerbal.value" label="title"
                                placeholder="-- Pilih --"></v-select>
                        </div>
                        <div class="col-lg-3 mt-12 mt-lg-0">
                            <label class="c-form-label">Motorik (Gerakan)</label>
                            <v-select v-model="form.motor_response" aria-readonly="true" :options="OptionsMotoric" :reduce="OptionsMotoric => OptionsMotoric.value" label="title"
                                placeholder="-- Pilih --"></v-select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 mt-12">
                            <label class="c-form-label">Capillary Refill Time (Crit >2 detik)</label>
                            <b-form-radio-group v-model="form.capillary_refill_time" size="sm">
                                <b-form-radio value="0">Tidak</b-form-radio>
                                <b-form-radio value="1">Ya</b-form-radio>
                            </b-form-radio-group>
                        </div>
                        <div class="col-lg-3 mt-12">
                            <label class="c-form-label">Deformitas</label>
                            <b-form-radio-group v-model="form.deformity_type_id" size="sm">
                                <b-form-radio value="0">Tidak</b-form-radio>
                                <b-form-radio value="1">Ya</b-form-radio>
                            </b-form-radio-group>
                            <b-form-input v-if="form.deformity_type_id == '1'" v-model="form.deformity" />
                        </div>
                        <div class="col-lg-3 mt-12">
                            <label class="c-form-label">Penetrasi</label>
                            <b-form-radio-group v-model="form.penetration_type_id" size="sm">
                                <b-form-radio value="0">Tidak</b-form-radio>
                                <b-form-radio value="1">Ya</b-form-radio>
                            </b-form-radio-group>
                            <b-form-input v-if="form.penetration_type_id == '1'" v-model="form.penetration" />
                        </div>
                        <div class="col-lg-3 mt-12">
                            <label class="c-form-label">Contusio</label>
                            <b-form-radio-group v-model="form.contusion_type_id" size="sm">
                                <b-form-radio value="0">Tidak</b-form-radio>
                                <b-form-radio value="1">Ya</b-form-radio>
                            </b-form-radio-group>
                            <b-form-input v-if="form.contusion_type_id == '1'" v-model="form.contusion" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 mt-12">
                            <label class="c-form-label">Laserasi</label>
                            <b-form-radio-group v-model="form.laceration_type_id" size="sm">
                                <b-form-radio value="0">Tidak</b-form-radio>
                                <b-form-radio value="1">Ya</b-form-radio>
                            </b-form-radio-group>
                            <b-form-input v-if="form.laceration_type_id == '1'" v-model="form.laceration" />
                        </div>
                        <div class="col-lg-3 mt-12">
                            <label class="c-form-label">Abrasi</label>
                            <b-form-radio-group v-model="form.abrasion_type_id" size="sm">
                                <b-form-radio value="0">Tidak</b-form-radio>
                                <b-form-radio value="1">Ya</b-form-radio>
                            </b-form-radio-group>
                            <b-form-input v-if="form.abrasion_type_id == '1'" v-model="form.abrasion" />
                        </div>
                        <div class="col-lg-3 mt-12">
                            <label class="c-form-label">Kedalaman Luka</label>
                            <b-form-radio-group v-model="form.wound_depth_type_id" size="sm">
                                <b-form-radio value="0">Tidak</b-form-radio>
                                <b-form-radio value="1">Ya</b-form-radio>
                            </b-form-radio-group>
                            <b-form-input v-if="form.wound_depth_type_id == '1'" v-model="form.wound_depth" />
                        </div>
                        <div class="col-lg-3 mt-12">
                            <label class="c-form-label">Lainnya</label>
                            <b-form-input v-model="form.anythingelse" />
                        </div>
                    </div>
                    <div class="mt-24">
                        <label class="c-form-label">Skala Nyeri</label>
                        <b-form-radio-group v-model="form.wong_baker_score">
                            <b-form-radio value="0" class="col-2 mr-0">
                                <div class="text-center">
                                    <img src="@/@core/assets/images/nurse/wong_baker/0.png" width="50%">
                                    <div class="c-form-label mt-10">
                                        <div>0</div>
                                        <div>Tidak Nyeri</div>
                                    </div>
                                </div>
                            </b-form-radio>
                            <b-form-radio value="2" class="col-2 mr-0">
                                <div class="text-center">
                                    <img src="@/@core/assets/images/nurse/wong_baker/2.png" width="50%">
                                    <div class="c-form-label mt-10">
                                        <div>2</div>
                                        <div>Sedikit Nyeri</div>
                                    </div>
                                </div>
                            </b-form-radio>
                            <b-form-radio value="4" class="col-2 mr-0">
                                <div class="text-center">
                                    <img src="@/@core/assets/images/nurse/wong_baker/4.png" width="50%">
                                    <div class="c-form-label mt-10">
                                        <div>4</div>
                                        <div>Sedikit Lebih Nyeri</div>
                                    </div>
                                </div>
                            </b-form-radio>
                            <b-form-radio value="6" class="col-2 mr-0">
                                <div class="text-center">
                                    <img src="@/@core/assets/images/nurse/wong_baker/6.png" width="50%">
                                    <div class="c-form-label mt-10">
                                        <div>6</div>
                                        <div>Lebih Nyeri</div>
                                    </div>
                                </div>
                            </b-form-radio>
                            <b-form-radio value="8" class="col-2 mr-0">
                                <div class="text-center">
                                    <img src="@/@core/assets/images/nurse/wong_baker/8.png" width="50%">
                                    <div class="c-form-label mt-10">
                                        <div>8</div>
                                        <div>Sangat Nyeri</div>
                                    </div>
                                </div>
                            </b-form-radio>
                            <b-form-radio value="10" class="col-2 mr-0">
                                <div class="text-center">
                                    <img src="@/@core/assets/images/nurse/wong_baker/10.png" width="50%">
                                    <div class="c-form-label mt-10">
                                        <div>10</div>
                                        <div>Nyeri Sangat Hebat</div>
                                    </div>
                                </div>
                            </b-form-radio>
                        </b-form-radio-group>
                    </div>
                    <!-- <div class="mt-32">
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
                    </div> -->
                </section>
                <section class="mt-40">
                    <b-table-simple responsive>
                        <b-thead class="text-bold-14 text-center">
                            <b-tr>
                                <b-th>PENGKAJIAN</b-th>
                                <b-th variant="primary">BIRU</b-th>
                                <b-th variant="danger">MERAH</b-th>
                                <b-th variant="warning">KUNING</b-th>
                                <b-th variant="success">HIJAU</b-th>
                                <b-th>PUTIH</b-th>
                            </b-tr>
                        </b-thead>
                        <b-tbody class="text-regular-12">
                            <b-tr>
                                <b-td>
                                    <span class="font-weight-bold">JALAN NAFAS</span>
                                    <!-- <b-form-radio-group v-model="form.airway_type_id" size="sm">
                                        <b-form-radio value="1">Bebas</b-form-radio>
                                        <b-form-radio value="2">Sumbatan</b-form-radio>
                                    </b-form-radio-group> -->
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.airway_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="A1">Ada Sumbatan Jalan Nafas</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.airway_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="B1">Bebas Atau Ada Ancaman Sumbatan</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.airway_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="C1">Bebas</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.airway_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="D1">Bebas</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.airway_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="E1">Bebas</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                            </b-tr>
                            <b-tr>
                                <b-td>
                                    <span class="font-weight-bold">PERNAFASAN</span>
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.respiratory_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="A1">Henti Nafas/Apnoe</b-form-checkbox>
                                        <b-form-checkbox value="A2">RR &lt; 10x/menit</b-form-checkbox>
                                        <b-form-checkbox value="A3">Sianosis</b-form-checkbox>
                                        <b-form-checkbox value="A4">SPO2 &lt; 90%</b-form-checkbox>
                                        <b-form-checkbox value="A5">Mengi</b-form-checkbox>
                                        <b-form-checkbox value="A6">Tidak Ada Keluhan</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.respiratory_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="B1">Henti Nafas</b-form-checkbox>
                                        <b-form-checkbox value="B2">RR &ge; 32x/menit</b-form-checkbox>
                                        <b-form-checkbox value="B3">Sianisis</b-form-checkbox>
                                        <b-form-checkbox value="B4">SPO2 &lt; 92%</b-form-checkbox>
                                        <b-form-checkbox value="B5">Mengi</b-form-checkbox>
                                        <b-form-checkbox value="B6">Tidak Ada Keluhan</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.respiratory_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="C1">RR 25-32x/menit</b-form-checkbox>
                                        <b-form-checkbox value="C2">Henti Nafas</b-form-checkbox>
                                        <b-form-checkbox value="C3">Sianisis</b-form-checkbox>
                                        <b-form-checkbox value="C4">SPO2 &lt; 92-95%</b-form-checkbox>
                                        <b-form-checkbox value="C5">Mengi</b-form-checkbox>
                                        <b-form-checkbox value="C6">Tidak Ada Keluhan</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.respiratory_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="D1">RR 20-24x/menit</b-form-checkbox>
                                        <b-form-checkbox value="D2">SPO2 &lt; 96-100%</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.respiratory_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="E1">RR 16-20 x/mnt</b-form-checkbox>
                                        <b-form-checkbox value="E2">SPO2 &lt; 96-100%</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                            </b-tr>
                            <b-tr>
                                <b-td>
                                    <span class="font-weight-bold">SIRKULASI</span>
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.circulation_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="A1">Henti Jantung</b-form-checkbox>
                                        <b-form-checkbox value="A2">Nadi Tidak Teraba</b-form-checkbox>
                                        <b-form-checkbox value="A3">Nadi Teraba Lemah</b-form-checkbox>
                                        <b-form-checkbox value="A4">Pucat, Akral Dingin</b-form-checkbox>
                                        <b-form-checkbox value="A5">Tidak Ada Keluhan</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.circulation_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="B1">Henti Jantung</b-form-checkbox>
                                        <b-form-checkbox value="B2">Nadi Tidak Teraba</b-form-checkbox>
                                        <b-form-checkbox value="B3">Nadi Teraba Lemah</b-form-checkbox>
                                        <b-form-checkbox value="B4">HR &lt; 50 atau &gt; 150 x/mnt</b-form-checkbox>
                                        <b-form-checkbox value="B5">Pucat, Akral Dingin</b-form-checkbox>
                                        <b-form-checkbox value="B6">Tidak Ada Keluhan</b-form-checkbox>
                                        <b-form-checkbox value="B7">CRT &gt; 2 Detik</b-form-checkbox>
                                        <b-form-checkbox value="B8">TD &lt; 100/&lt; 60 mmHg</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.circulation_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="C1">Nadi Kuat</b-form-checkbox>
                                        <b-form-checkbox value="C2">HR 121-150x/menit</b-form-checkbox>
                                        <b-form-checkbox value="C3">TD 160/ &gt; 100 mmHg</b-form-checkbox>
                                        <b-form-checkbox value="C4">CRT &lt; 2 Detik</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.circulation_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="D1">HR 101-120x/menit</b-form-checkbox>
                                        <b-form-checkbox value="D2">TD 121 - 140/80-100 mmHg</b-form-checkbox>
                                        <b-form-checkbox value="D3">CRT &lt; 2 Detik</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.circulation_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="E1">HR 10-100x/menit</b-form-checkbox>
                                        <b-form-checkbox value="E2">TD 120/80-100 mmHg</b-form-checkbox>
                                        <b-form-checkbox value="E3">CRT &lt; 2 Detik</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                            </b-tr>
                            <b-tr>
                                <b-td>
                                    <span class="font-weight-bold">KESADARAN</span>
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.consciousness_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="A1">GCS &lt; 9</b-form-checkbox>
                                        <b-form-checkbox value="A2">Kejang</b-form-checkbox>
                                        <b-form-checkbox value="A3">Tidak Ada Respon</b-form-checkbox>
                                        <b-form-checkbox value="A4">Gelisah</b-form-checkbox>
                                        <b-form-checkbox value="A5">Hamparesis</b-form-checkbox>
                                        <b-form-checkbox value="A6">Nyeri Dada</b-form-checkbox>
                                        <b-form-checkbox value="A7">Tidak Ada Keluhan</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.consciousness_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="B1">GCS 9-12</b-form-checkbox>
                                        <b-form-checkbox value="B2">Kejang</b-form-checkbox>
                                        <b-form-checkbox value="B3">Tidak Ada Respon</b-form-checkbox>
                                        <b-form-checkbox value="B4">Gelisah</b-form-checkbox>
                                        <b-form-checkbox value="B5">Hamparesis</b-form-checkbox>
                                        <b-form-checkbox value="B6">Nyeri Dada</b-form-checkbox>
                                        <b-form-checkbox value="B7">Tidak Ada Keluhan</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.consciousness_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="C1">GCS 13-14</b-form-checkbox>
                                        <b-form-checkbox value="C2">Apatis / Samnolen</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.consciousness_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="D1">GCS 15</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.consciousness_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="E1">GCS 15</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                            </b-tr>
                            <b-tr>
                                <b-td>
                                    <span class="font-weight-bold">PAPARAN</span>
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.exposure_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="A1">Kejang Berkelanjutan</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.exposure_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="B1">Nyeri dada tipikal</b-form-checkbox>
                                        <b-form-checkbox value="B2">Nyeri hebat</b-form-checkbox>
                                        <b-form-checkbox value="B3">Deficit neurologis (hemiparese, dispasia)</b-form-checkbox>
                                        <b-form-checkbox value="B4">Mata terpecik zat asam atau basa</b-form-checkbox>
                                        <b-form-checkbox value="B5">Multiple trauma</b-form-checkbox>
                                        <b-form-checkbox value="B6">Intoksikasi</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.exposure_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="C1">Riwayat kejang</b-form-checkbox>
                                        <b-form-checkbox value="C2">Nyeri sedang</b-form-checkbox>
                                        <b-form-checkbox value="C3">Riwayat pingsan</b-form-checkbox>
                                        <b-form-checkbox value="C4">Deformitas</b-form-checkbox>
                                        <b-form-checkbox value="C5">Laserasi</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.exposure_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="D1">Nyeri ringan</b-form-checkbox>
                                        <b-form-checkbox value="D2">Trauma ekstrimitas minor</b-form-checkbox>
                                        <b-form-checkbox value="D3">Radang / benda asing di mata</b-form-checkbox>
                                        <b-form-checkbox value="D4">Penglihatan normal</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.exposure_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="E1">Keluhan minor yang saat berkunjung masih dirasakan</b-form-checkbox>
                                        <b-form-checkbox value="E2">Luka kecil (luka lecet, luka robek kecil)</b-form-checkbox>
                                        <b-form-checkbox value="E3">Kunjungan ulang untuk ganti verban</b-form-checkbox>
                                        <b-form-checkbox value="E4">Evaluasi jahitan</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                            </b-tr>
                            <b-tr>
                                <b-td>
                                    <span class="font-weight-bold">PSIKOLOGI</span>
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.psychology_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="A1">Gangguan perilaku berat yang mengancam diri pasien dan orang lain</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.psychology_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="B1">Perilaku agresif dan kasar </b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.psychology_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="C1">Menarik diri, agitasi, gangguan isi dan bentuk pikiran akut</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.psychology_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="D1">Riwayat gangguan yang merusak diri dan mengganggu orang lain</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                                <b-td>
                                    <b-form-checkbox-group v-model="form.psychology_arry" @change="handleTriageResult" stacked size="sm">
                                        <b-form-checkbox value="E1">Normal</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </b-td>
                            </b-tr>
                        </b-tbody>
                    </b-table-simple>
                </section>
                <section class="mt-24">
                    <ModernLabel label="Hasil Pemeriksaan Triase: Resusitasi" />
                    <b-form-input v-model="form.triage_result" disabled class="mt-16"></b-form-input>
                    <div class="mt-12">
                        <label>Diteruskan kepada: </label>
                        <b-table-simple bordered>
                            <b-tbody>
                                <b-tr>
                                    <b-td variant="primary" class="text-bold-14 text-center">BIRU</b-td>
                                    <b-td>
                                        <b-form-checkbox-group v-model="form.forward_destination_arry" stacked size="sm">
                                            <b-form-checkbox value="1">1. Ruang Restalasi</b-form-checkbox>
                                        </b-form-checkbox-group>
                                    </b-td>
                                </b-tr>
                                <b-tr>
                                    <b-td variant="danger" class="text-bold-14 text-center">MERAH</b-td>
                                    <b-td rowspan="2">
                                        <b-form-checkbox-group v-model="form.forward_destination_arry" stacked size="sm">
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <b-form-checkbox value="2">2. SMF Bedah Umum</b-form-checkbox>
                                                    <b-form-checkbox value="3">3. SMF Bedah Ortopedi</b-form-checkbox>
                                                    <b-form-checkbox value="4">4. SMF Urologi</b-form-checkbox>
                                                    <b-form-checkbox value="5">5. SMF Bedah Syaraf</b-form-checkbox>
                                                    <b-form-checkbox value="6">6. SMF Bedah Plastik</b-form-checkbox>
                                                    <b-form-checkbox value="7">7. SMF Kesehaan Anak</b-form-checkbox>
                                                </div>
                                                <div class="col-lg-4">
                                                    <b-form-checkbox value="8">8. SMF Penyakit Dalam</b-form-checkbox>
                                                    <b-form-checkbox value="9">9. SMF Jantung dan Pembuluh Darah</b-form-checkbox>
                                                    <b-form-checkbox value="10">10. SMF Paru</b-form-checkbox>
                                                    <b-form-checkbox value="11">11. SMF Kebidanan dan Kandungan</b-form-checkbox>
                                                    <b-form-checkbox value="12">12. SMF THT</b-form-checkbox>
                                                    <b-form-checkbox value="13">13. SMF Mata</b-form-checkbox>
                                                </div>
                                                <div class="col-lg-4">
                                                    <b-form-checkbox value="14">14. SMF Syaraf</b-form-checkbox>
                                                    <b-form-checkbox value="15">15. SMF Kedokteran Jiwa</b-form-checkbox>
                                                    <b-form-checkbox value="16">16. SMF Kulit dan Kelamin</b-form-checkbox>
                                                    <b-form-checkbox value="17">17. SMF Dokter Umum</b-form-checkbox>
                                                    <b-form-checkbox value="18">18. SMF Gigi dan Mulut  </b-form-checkbox>
                                                </div>
                                            </div>
                                        </b-form-checkbox-group>
                                    </b-td>
                                </b-tr>
                                <b-tr>
                                    <b-td variant="warning" class="text-bold-14 text-center">KUNING</b-td>
                                </b-tr>
                                <b-tr>
                                    <b-td variant="success" class="text-bold-14 text-center">HIJAU</b-td>
                                    <b-td>
                                        <b-form-checkbox-group v-model="form.forward_destination_arry" stacked size="sm">
                                            <b-form-checkbox value="19">19. SMF Dokter Umum</b-form-checkbox>
                                        </b-form-checkbox-group>
                                    </b-td>
                                </b-tr>
                                <b-tr>
                                    <b-td class="text-bold-14 text-center">PUTIH</b-td>
                                    <b-td>
                                        <b-form-checkbox-group v-model="form.forward_destination_arry" stacked size="sm">
                                            <b-form-checkbox value="20">20. SMF Dokter Umum</b-form-checkbox>
                                        </b-form-checkbox-group>
                                    </b-td>
                                </b-tr>
                            </b-tbody>
                        </b-table-simple>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Kriteria Isolasi</label>
                        <b-form-radio-group v-model="form.isolation_type_id" size="sm">
                            <b-form-radio value="1">Isolasi</b-form-radio>
                            <b-form-radio value="2">Non Isolasi</b-form-radio>
                        </b-form-radio-group>
                        <b-form-checkbox-group v-model="form.isolation_criteria_arry" v-if="form.isolation_type_id == '1'" stacked size="sm" class="mt-8">
                            <b-form-checkbox value="1">Batuk &gt; 2 Minggu</b-form-checkbox>
                            <b-form-checkbox value="2">Demam</b-form-checkbox>
                            <b-form-checkbox value="3">Tidak Ada Keluhan</b-form-checkbox>
                            <b-form-checkbox value="4">Traveling Dari Daerah Endemi, Sebutkan:</b-form-checkbox>
                        </b-form-checkbox-group>
                        <b-form-input v-if="form.isolation_type_id == '1' && form.isolation_criteria_arry.indexOf('4') !== -1" v-model="form.isolation_criteria"
                            class="mt-16"></b-form-input>
                    </div>
                </section>
                <div class="card-footer mt-32">
                    <div class="w-100 d-flex justify-content-end align-items-end">
                        <b-button :disabled="isDisabled" @click="resetForm" variant="outline-primary" class="mr-2">Reset</b-button>
                        <b-button :disabled="isDisabled" @click="validationForm" variant="primary">Simpan</b-button>
                    </div>
                </div>
            </div>
        </section>
    </div>
 
</template>
<script>
import { mapState } from 'vuex'
import patientMenu from '@/@core/components/tab-menu/patientMenu.vue';
import administration from '@/views/layouts/patient/doctor/administration.vue';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import vSelect from 'vue-select'
import ModernLabel from '@/@core/components/label/ModernLabel.vue';

export default {
    name: 'TriageV1',
    components: {
        patientMenu, administration, vSelect, ModernLabel
    },
    data() {
        return {
            urlbe:process.env.VUE_APP_IMAGE,
            id: '',
            isDisabled: false,
            regData: {},
            triageData: [],

            OptionsEye: [
                { title: 'Spontan', value: '1' },
                { title: 'Sedikit rangsang', value: '2' },
                { title: 'Rangsang nyeri', value: '3' },
                { title: 'Tidak ada respon', value: '4' },
            ],
            OptionsVerbal: [
                { title: 'Orientasi baik', value: '1' },
                { title: 'Orientasi tidak baik', value: '2' },
                { title: 'Bicara tidak jelas', value: '3' },
                { title: 'Tidak ada respon', value: '4' },
            ],
            OptionsMotoric: [
                { title: 'Mengikuti perintah', value: '1' },
                { title: 'Gerakan tidak sesuai', value: '2' },
                { title: 'Tidak ada respon', value: '3' },
            ],

            form: {
                registration_destination_id: '',
                doctor_in_charge_id: '',
                id: '',
                case_type_id: '',
                eye_response: '',
                verbal_response: '',
                motor_response: '',
                systolic_blood_pressure: '',
                diastolic_blood_pressure: '',
                capillary_refill_time: '',
                breath_frequency: '',
                pulse_frequency: '',
                temperature: '',
                deformity_type_id: '',
                deformity: '',
                contusion_type_id: '',
                contusion: '',
                abrasion_type_id: '',
                abrasion: '',
                penetration_type_id: '',
                penetration: '',
                laceration_type_id: '',
                laceration: '',
                wound_depth_type_id: '',
                wound_depth: '',
                anythingelse: '',
                // dead_on_arrival_arry: [],
                // doa_time: '',
                // die_time: '',
                // airway_type_id: '',
                respiratory_arry: [],
                circulation_arry: [],
                consciousness_arry: [],
                triage_result: '',
                isolation_criteria_arry: [],
                isolation_criteria: '',
                exposure_arry: [],
                psychology_arry: [],

                airway_arry: [],
                wong_baker_score: null,
                forward_destination_arry: [],
                isolation_type_id: 1,
            },
            active: {
                tab: 'LIST',
            },
            tableFields: [
                { key: 'no', label: 'No', thStyle: 'width:60px; text-align: center' },
                { key: 'doctor_in_charge_name', label: 'Nama Dokter' },
                { key: 'date_updated', label: 'Tanggal Triase Ditambahkan / Edit' },
                { key: 'action', label: 'Aksi' },
            ],
        }
    },
    watch: {
        "form.deformity_type_id"(newValue) {
            if (newValue === "0") {
                this.form.deformity = ""
            }
        },
        "form.contusion_type_id"(newValue) {
            if (newValue === "0") {
                this.form.contusion = ""
            }
        },
        "form.abrasion_type_id"(newValue) {
            if (newValue === "0") {
                this.form.abrasion = ""
            }
        },
        "form.penetration_type_id"(newValue) {
            if (newValue === "0") {
                this.form.penetration = ""
            }
        },
        "form.laceration_type_id"(newValue) {
            if (newValue === "0") {
                this.form.laceration = ""
            }
        },
        "form.wound_depth_type_id"(newValue) {
            if (newValue === "0") {
                this.form.wound_depth = ""
            }
        },
        "form.isolation_type_id"(newValue) {
            if (newValue != "1") {
                this.form.isolation_criteria_arry = []
                this.form.isolation_criteria = ""
            }
        },
        "form.isolation_criteria_arry"(newValues) {
            if (newValues && !newValues.includes("4")) {
                this.form.isolation_criteria = ""
            }
        },
        // "form.dead_on_arrival_arry"(newValues) {
        //     if (newValues && !newValues.includes("a")) {
        //         this.form.doa_time = ""
        //     }
        //     if (newValues && !newValues.includes("b")) {
        //         this.form.die_time = ""
        //     }
        //     if (newValues && !newValues.includes("a") && !newValues.includes("b") && !newValues.includes("c") ) {
        //         this.form.triage_result = ""
        //     }
        // },
    },
    methods: {

        countByPrefix(array, prefix) {
            return array.filter(item => item.startsWith(prefix)).length;
        },

        countTotals(...arrays) {
            const prefixes = ['A', 'B', 'C', 'D', 'E'];
            
            return prefixes.map((prefix, index) => {
                return arrays.reduce((total, arr) => total + arr[index], 0);
            });
        },

        handleTriageResult() {
            const prefixes = ['A', 'B', 'C', 'D', 'E'];

            const airwayCounts = prefixes.map(prefix => this.countByPrefix(this.form.airway_arry, prefix));
            const respiratoryCounts = prefixes.map(prefix => this.countByPrefix(this.form.respiratory_arry, prefix));
            const circulationCounts = prefixes.map(prefix => this.countByPrefix(this.form.circulation_arry, prefix));
            const consciousnessCounts = prefixes.map(prefix => this.countByPrefix(this.form.consciousness_arry, prefix));
            const exposureCounts = prefixes.map(prefix => this.countByPrefix(this.form.exposure_arry, prefix));
            const psychologyCounts = prefixes.map(prefix => this.countByPrefix(this.form.psychology_arry, prefix));

            const totalCounts = this.countTotals(
                airwayCounts,
                respiratoryCounts,
                circulationCounts,
                consciousnessCounts,
                exposureCounts,
                psychologyCounts
            );

            // console.log('Total counts for each prefix:', totalCounts);

            const categories = ['BIRU', 'MERAH', 'KUNING', 'HIJAU', 'PUTIH'];
            const priority = ['BIRU', 'MERAH', 'KUNING', 'HIJAU', 'PUTIH'];

            const maxCount = Math.max(...totalCounts);
            
            const maxIndices = totalCounts
            .map((count, index) => (count === maxCount ? index : -1))
            .filter(index => index !== -1);

            const largestCategory = priority.find(category => maxIndices.includes(categories.indexOf(category)));
            // console.log('The category with the largest value is:', largestCategory);
            
            if (this.form.airway_arry.length == 0 && this.form.respiratory_arry == 0 && this.form.circulation_arry == 0 && this.form.consciousness_arry == 0 && this.form.psychology_arry == 0) {
                this.form.triage_result = ''
            } else {
                this.form.triage_result = largestCategory
            }
        },
        
        refresh () {
            router.go(router.currentRoute)
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        goToTab(tab){
            this.active.tab = tab
            if (tab == 'LIST') {
                this.form.id = ''
                this.resetForm()
            }
        },
        getData (regDesId) {
			this.$store.dispatch('registration/getById', regDesId).then(data => {
                this.regData = data[0]
                this.form.doctor_in_charge_id = this.regData.doctor_in_charge_id 
            })
        },
        getTriage (regDesId) {
			this.$store.dispatch('triage/getAllList', { registration_destination_id: regDesId }).then(data => {
                this.triageData = data
                // console.log('this.triageData', this.triageData);
            })
			// this.$store.dispatch('triage/getByRegDesId', regDesId).then(data => {
            //     this.triageData = data
            // })
        },
        validationForm(){
            // console.log('this.form', this.form);
            
            try {
                this.isDisabled = true
                this.$store.dispatch('triage/formPostPut', this.form).then((response) => {
                    this.notification('Success', 'CheckIcon', 'success', 'You have successfully')
                    // console.log('response', response);
                    this.active.tab = "LIST"
                    this.resetForm()
                    this.isDisabled = false
                    this.getTriage(this.$route.params.id)
                })
                
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error at: ', error)
                this.isDisabled = false
            }
        },
        deleteRow(id){
            try {
                this.$store.dispatch('triage/removeTriage', id).then((response) => {
                    this.notification('Success', 'CheckIcon', 'success', 'You have successfully')
                    this.getTriage(this.$route.params.id)
                    // console.log('response', response);
                })
                
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error at: ', error)
            }
        },
        onEditform (obj){
            // console.log('onEditform', obj);
            this.goToTab('ADD')

            this.form.registration_destination_id = this.$route.params.id
            this.form.id = obj.id
            this.form.case_type_id = obj.case_type_id
            this.form.eye_response = obj.eye_response.toString()
            this.form.verbal_response = obj.verbal_response.toString()
            this.form.motor_response = obj.motor_response.toString()
            this.form.systolic_blood_pressure = obj.systolic_blood_pressure
            this.form.diastolic_blood_pressure = obj.diastolic_blood_pressure
            this.form.capillary_refill_time = obj.capillary_refill_time
            this.form.breath_frequency = obj.breath_frequency
            this.form.pulse_frequency = obj.pulse_frequency
            this.form.temperature = obj.temperature
            this.form.deformity_type_id = obj.deformity_type_id 
            this.form.deformity = obj.deformity
            this.form.contusion_type_id = obj.contusion_type_id 
            this.form.contusion = obj.contusion
            this.form.abrasion_type_id = obj.abrasion_type_id 
            this.form.abrasion = obj.abrasion
            this.form.penetration_type_id = obj.penetration_type_id 
            this.form.penetration = obj.penetration
            this.form.laceration_type_id = obj.laceration_type_id 
            this.form.laceration = obj.laceration
            this.form.wound_depth_type_id = obj.wound_depth_type_id 
            this.form.wound_depth = obj.wound_depth
            this.form.anythingelse = obj.anythingelse
            this.form.dead_on_arrival_arry = obj.dead_on_arrival_arry 
            this.form.doa_time = obj.doa_time
            this.form.die_time = obj.die_time
            this.form.airway_type_id = obj.airway_type_id
            this.form.respiratory_arry = obj.respiratory_arry 
            this.form.circulation_arry = obj.circulation_arry 
            this.form.consciousness_arry = obj.consciousness_arry 
            this.form.triage_result = obj.triage_result
            this.form.isolation_criteria_arry = obj.isolation_criteria_arry 
            this.form.isolation_criteria = obj.isolation_criteria
            this.form.exposure_arry = obj.exposure_arry 
            this.form.psychology_arry = obj.psychology_arry 
            this.form.airway_arry = obj.airway_arry
            this.form.wong_baker_score = obj.wong_baker_score
            this.form.forward_destination_arry = obj.forward_destination_arry
            this.form.isolation_type_id = obj.isolation_type_id
        },
        resetForm() {
            this.form.case_type_id = ''
            this.form.eye_response = ''
            this.form.verbal_response = ''
            this.form.motor_response = ''
            this.form.systolic_blood_pressure = ''
            this.form.diastolic_blood_pressure = ''
            this.form.capillary_refill_time = ''
            this.form.breath_frequency = ''
            this.form.pulse_frequency = ''
            this.form.temperature = ''
            this.form.deformity_type_id = ''
            this.form.deformity = ''
            this.form.contusion_type_id = ''
            this.form.contusion = ''
            this.form.abrasion_type_id = ''
            this.form.abrasion = ''
            this.form.penetration_type_id = ''
            this.form.penetration = ''
            this.form.laceration_type_id = ''
            this.form.laceration = ''
            this.form.wound_depth_type_id = ''
            this.form.wound_depth = ''
            this.form.anythingelse = ''
            // this.form.dead_on_arrival_arry = []
            // this.form.doa_time = ''
            // this.form.die_time = ''
            this.form.airway_type_id = ''
            this.form.respiratory_arry = []
            this.form.circulation_arry = []
            this.form.consciousness_arry = []
            this.form.triage_result = ''
            this.form.isolation_criteria_arry = []
            this.form.isolation_criteria = ''
            this.form.exposure_arry = []
            this.form.psychology_arry = []
            this.form.airway_arry = []
            this.form.wong_baker_score = null
            this.form.forward_destination_arry = []
            this.form.isolation_type_id = 1
        },
    },
    created () {
        if (this.$route.params.id) {
            this.id = this.$route.params.id
            this.getData(this.$route.params.id)
            this.getTriage(this.$route.params.id)
            this.form.registration_destination_id = this.$route.params.id
        }
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>
<style scoped>
/* @import '../../../../@core/css/utility.css'; */

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

li {
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
}
td{
    padding-top: 2px;
    padding-bottom: 2px;
}
</style>