<template>
    <div>
        <patientMenu selectedMenu="menu4" />
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
                                <template v-if="this.regData.registration_insurance">
                                    <template v-for="list in this.regData.registration_insurance">
                                    <b-button v-if="list.is_primary === 1 && list.company_id === '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8'" @click="icare(list.number_insurance != '-' || list.number_insurance != '' || list.number_insurance != null ? list.number_insurance : this.regData.idcard) " size="sm" variant="w3green" class="text-white ml-2">i-Care</b-button>
                                    </template>
                                </template>
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
                            <p class="text-neutral-800 mt-8 mt-lg-16">Allergy :
                                <template v-for="(ialler, index) in itemsAllergy" v-if="index === 0">
                                    <b>{{ ialler.name }}</b> 
                                </template>
                            </p>
                            <p class="text-neutral-800 mt-8 mt-lg-16">
                                <span class="text-regular-14 mr-2">Status :</span>
                                <span class="text-bold-14" v-if="!regData.exit_name">Dalam Penanganan Medis</span>
                                <span class="text-bold-14" v-if="regData.exit_name">{{this.regData.exit_name}}</span>
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

        <!-- RAWAT INAP -->
        <b-modal v-if="isNewBorn" v-model="active_act.modal_NewBorn" scrollable size="xl" title="KEADAAN BAYI BARU LAHIR" hide-footer>
            <section class="col-12">
                <div class="card">
                    <div class="card-header bg-indigo-200">
                        <span class="text-bold-18">Data Dasar</span>
                    </div>
                    <div class="card-body">
                        <div class="row" style="font-size: 14px">
                            <div class="col-4">
                                <div class="font-weight-bold">Nama</div>
                                <div>{{ regData.name }} ({{ parsed_age(regData.birth_date) }})</div>
                            </div>
                            <div class="col-4">
                                <div class="font-weight-bold">Tanggal Lahir</div>
                                <div>{{ localDate(regData.birth_date) }}</div>
                            </div>
                            <div class="col-4">
                                <div class="font-weight-bold">No RM</div>
                                <div>{{ regData.norm }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="px-3 mt-20 mt-lg-32">
                <div class="card">
                    <div class="card-header bg-indigo-200">
                        <span class="text-bold-18">Keadaan Bayi Baru Lahir</span>
                    </div>
                    <div class="card-body">
                        <div class="row" > 
                            <div class="col-lg-12 px-0">
                                <div class="row mx-0 mt-10">
                                    <b-table-simple bordered hover resposive>
                                        <b-thead>
                                            <b-tr>
                                                <b-th></b-th>
                                                <b-th></b-th>
                                                <b-th>I</b-th>
                                                <b-th>II</b-th>
                                            </b-tr>
                                        </b-thead>
                                        <b-tbody>
                                            <b-tr>
                                                <b-td>Frekuensi Jantung</b-td>
                                                <b-td>
                                                    <p>Tidak ada : 0</p>
                                                    <p>Kurang dari 100 : 1</p>
                                                    <p>Lebih dari 100 : 2</p>
                                                </b-td>
                                                <b-td><b-form-input type="number"/></b-td>
                                                <b-td><b-form-input type="number"/></b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>Usaha Pernafasan</b-td>
                                                <b-td>
                                                    <p>Tidak ada : 0</p>
                                                    <p>Lemah menangis : 1</p>
                                                    <p>Menangis Kuat dan Baik : 2</p>
                                                </b-td>
                                                <b-td><b-form-input type="number"/></b-td>
                                                <b-td><b-form-input type="number"/></b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>Tonus Otot</b-td>
                                                <b-td>
                                                    <p>Lembek : 0</p>
                                                    <p>Gerakan Sedikit : 1</p>
                                                    <p>Gerakan Baik : 2</p>
                                                    <p>Tidak beraksi : 0</p>
                                                </b-td>
                                                <b-td><b-form-input type="number"/></b-td>
                                                <b-td><b-form-input type="number"/></b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <p>Reaksi Refleks</p>
                                                    <p>a. Sonde Hidung</p>
                                                    <p>b. Telapak kaki</p>
                                                </b-td>
                                                <b-td>
                                                    <p>Reaksi Sedikit : 1</p>
                                                    <p>a. Bersin atau Batuk : 2</p>
                                                    <p>b. Menangis dan Kaki Menarik Kembali</p>
                                                </b-td>
                                                <b-td><b-form-input type="number"/></b-td>
                                                <b-td><b-form-input type="number"/></b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <p>Warna Kulit</p>
                                                </b-td>
                                                <b-td>
                                                    <p>Pucat / Biru : 0</p>
                                                    <p>Badan Merah Muda : 1</p>
                                                    <p>Ekstremitas Biru (Anggota-anggota Badan)</p>
                                                    <p>Seluruhnya Mera Muda : 2</p>
                                                </b-td>
                                                <b-td><b-form-input type="number" /></b-td>
                                                <b-td><b-form-input type="number" /></b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td colspan="2">JUMLAH</b-td>
                                                <b-td></b-td>
                                                <b-td></b-td>
                                            </b-tr>
                                        </b-tbody>
                                    </b-table-simple>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="px-3 mt-20 mt-lg-32">
                <div class="card">
                    <div class="card-header bg-indigo-200">
                        <span class="text-bold-18">Kesimpulan</span>
                    </div>
                    <div class="card-body">
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex">
                                <label class="my-auto text-semi-14">Alat Kelamin Luar</label>
                            </div>
                            <div class="col">
                                <b-form-input type="text" autocomplete="off"></b-form-input>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex">
                                <label class="my-auto text-semi-14">Anus</label>
                            </div>
                            <div class="col">
                                <b-form-input type="text" autocomplete="off"></b-form-input>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex">
                                <label class="my-auto text-semi-14">Meconium</label>
                            </div>
                            <div class="col">
                                <b-form-input type="text" autocomplete="off"></b-form-input>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex">
                                <label class="my-auto text-semi-14">Miksi</label>
                            </div>
                            <div class="col">
                                <b-form-input type="text" autocomplete="off"></b-form-input>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex">
                                <label class="my-auto text-semi-14">Vernik Caseosa / Lanugo</label>
                            </div>
                            <div class="col">
                                <b-form-input type="text" autocomplete="off"></b-form-input>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex">
                                <label class="my-auto text-semi-14">Caput Succedanium</label>
                            </div>
                            <div class="col">
                                <b-form-input type="text" autocomplete="off"></b-form-input>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex">
                                <label class="my-auto text-semi-14">Cephal Haematom</label>
                            </div>
                            <div class="col">
                                <b-form-input type="text" autocomplete="off"></b-form-input>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex">
                                <label class="my-auto text-semi-14">Kepala</label>
                            </div>
                            <div class="col">
                                <b-form-input type="text" autocomplete="off"></b-form-input>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex">
                                <label class="my-auto text-semi-14">Sutura</label>
                            </div>
                            <div class="col">
                                <b-form-input type="text" autocomplete="off"></b-form-input>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex">
                                <label class="my-auto text-semi-14">Ubun-ubun Besar</label>
                            </div>
                            <div class="col">
                                <b-form-input type="text" autocomplete="off"></b-form-input>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex">
                                <label class="my-auto text-semi-14">Ubun-ubun Kecil</label>
                            </div>
                            <div class="col">
                                <b-form-input type="text" autocomplete="off"></b-form-input>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex">
                                <label class="my-auto text-semi-14">Meninggal Dalam Kehamilan</label>
                            </div>
                            <div class="col">
                                <b-form-input type="text" autocomplete="off"></b-form-input>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex">
                                <label class="my-auto text-semi-14">Meninggal Dalam Persalinan</label>
                            </div>
                            <div class="col">
                                <b-form-input type="text" autocomplete="off"></b-form-input>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex">
                                <label class="my-auto text-semi-14">Meninggal sesudah lahir beberapa menit / jam</label>
                            </div>
                            <div class="col">
                                <b-form-input type="text" autocomplete="off"></b-form-input>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex">
                                <label class="my-auto text-semi-14">Kelainan bawaan</label>
                            </div>
                            <div class="col">
                                <b-textarea rows="3"></b-textarea>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex">
                                <label class="my-auto text-semi-14">Trauma Bayi</label>
                            </div>
                            <div class="col">
                                <b-textarea rows="3"></b-textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </b-modal>

        <b-modal v-if="isPartograf" v-model="active_act.modal_Partograf" scrollable size="xl" title="Partograf & Catatan Kehamilan" hide-footer>

            <section class="col-12">
                <div class="card">
                    <div class="card-header bg-indigo-200">
                        <span class="text-bold-18">Data Dasar</span>
                    </div>
                    <div class="card-body">
                        <div class="row" style="font-size: 14px">
                            <div class="col-4">
                                <div class="font-weight-bold">Nama</div>
                                <div>{{ regData.name }} ({{ parsed_age(regData.birth_date) }})</div>
                            </div>
                            <div class="col-4">
                                <div class="font-weight-bold">Tanggal Lahir</div>
                                <div>{{ localDate(regData.birth_date) }}</div>
                            </div>
                            <div class="col-4">
                                <div class="font-weight-bold">No RM</div>
                                <div>{{ regData.norm }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-header bg-indigo-200">
                        <span class="text-bold-18">Partograf Kebidanan</span>
                    </div>
                    <div class="card-body">
                        <div class="row" style="font-size: 14px">
                            <div class="col-lg-4">
                                <label class="font-weight-bold">Tanggal & Jam Mulai</label>
                                <div>
                                    <input type="datetime-local" v-model="partograf.datetime_start" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <label class="font-weight-bold">Dokter</label>
                                <div>
                                    <v-select v-model="partograf.doctor_id" :options="OptionsSpecialistDoctor" :reduce="OptionsSpecialistDoctor => OptionsSpecialistDoctor.value" label="title" placeholder="-- Dokter --" />
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <label class="font-weight-bold">Bidan</label>
                                <div>
                                    <v-select v-model="partograf.midwife_id" :options="OptionsSpecialistDoctor" :reduce="OptionsSpecialistDoctor => OptionsSpecialistDoctor.value" label="title" placeholder="-- Bidan --" />
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <label class="font-weight-bold">Pecah Ketuban Sejak</label>
                                <div>
                                    <input type="datetime-local" v-model="partograf.water_breaks_since" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <label class="font-weight-bold">Mules Sejak</label>
                                <div>
                                    <input type="datetime-local" v-model="partograf.stomachache_since" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <label class="font-weight-bold">Gravida</label>
                                        <div>
                                            <b-form-input v-model="partograf.gravida" type="text" />
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <label class="font-weight-bold">Para</label>
                                        <div>
                                            <b-form-input v-model="partograf.para" type="text" />
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <label class="font-weight-bold">Abortus</label>
                                        <div>
                                            <b-form-input v-model="partograf.abortus" type="text" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-header bg-indigo-200">
                        <span class="text-bold-18">Partograf</span>
                    </div>
                    <div class="card-body">
                        <div class="row" style="font-size: 14px">
                            <div class="col-lg-12">
                                <div class="accordion" role="tablist">
                                    <b-card no-body class="mb-1">
                                        <b-card-header block v-b-toggle="'accordion-'+1" header-tag="header" class="pointer d-flex justify-content-between align-items-center" role="tab" onMouseOver="this.style.backgroundColor='#648DE1'; this.style.color='#FFFFFF'" onMouseOut="this.style.backgroundColor='#FFFFFF'; this.style.color='#212529'" >
                                            <div>
                                                <div class="font-weight-bold">+ DENYUT JANTUNG JANIN, KETUBAN & PENYUSUPAN</div>
                                                <div></div>
                                            </div>
                                            <feather-icon icon="ChevronDownIcon" size="18" />
                                        </b-card-header>
                                        <b-collapse :id="`accordion-`+1" accordion="my-accordion" role="tabpanel">
                                            <b-card-body>
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <div class="row" style="font-size: 14px">
                                                            <div class="col-lg-6">
                                                                <label class="font-weight-bold">Jam Observasi</label>
                                                                <div>
                                                                    <b-form-input v-model="partograf.datetime_start" type="time" />
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <label class="font-weight-bold">Denyut Jantung Janin</label>
                                                                <div>
                                                                    <b-input-group append="bpm">
                                                                        <b-form-input v-model="partograf.nadi" type="text" />
                                                                    </b-input-group>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <b-button @click="addDJJ" variant="primary" class="mt-12">Tambah</b-button>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="row" style="font-size: 14px">
                                                            <div class="col-lg-6">
                                                                <label class="font-weight-bold">Jam Observasi</label>
                                                                <div>
                                                                    <b-form-input v-model="partograf.datetime_start" type="time" />
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <label class="font-weight-bold">air Ketuban</label>
                                                                <div>
                                                                    <b-input-group append="bpm">
                                                                        <b-form-input v-model="partograf.nadi" type="text" />
                                                                    </b-input-group>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <b-button @click="addDJJ" variant="primary" class="mt-12">Tambah</b-button>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="row" style="font-size: 14px">
                                                            <div class="col-lg-6">
                                                                <label class="font-weight-bold">Jam Observasi</label>
                                                                <div>
                                                                    <b-form-input v-model="partograf.datetime_start" type="time" />
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <label class="font-weight-bold">Penyusupan</label>
                                                                <div>
                                                                    <b-input-group append="bpm">
                                                                        <b-form-input v-model="partograf.nadi" type="text" />
                                                                    </b-input-group>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <b-button @click="addDJJ" variant="primary" class="mt-12">Tambah</b-button>
                                                    </div>
                                                </div>
                                            </b-card-body>
                                        </b-collapse>
                                    </b-card>
                                </div>
                                <LineChart class="mt-24 w-100"/><br>
                                <div class="font-weight-bold">Penyusupan & Ketuban</div>
                                <LineChartKetuban class="mt-24 w-100"/><br>
                                <div class="accordion" role="tablist">
                                    <b-card no-body class="mb-1">
                                        <b-card-header block v-b-toggle="'accordion-'+2" header-tag="header" class="pointer d-flex justify-content-between align-items-center" role="tab" onMouseOver="this.style.backgroundColor='#648DE1'; this.style.color='#FFFFFF'" onMouseOut="this.style.backgroundColor='#FFFFFF'; this.style.color='#212529'" >
                                            <div>
                                                <div class="font-weight-bold">+ PEMBUKAAN SERVIKS & KONTRAKSI</div>
                                            </div>
                                            <feather-icon icon="ChevronDownIcon" size="18" />
                                        </b-card-header>
                                        <b-collapse :id="`accordion-`+2" accordion="my-accordion" role="tabpanel">
                                            <b-card-body>
                                                
                                            </b-card-body>
                                        </b-collapse>
                                    </b-card>
                                </div>
                                <LineChartServiks class="mt-24 w-100"/><br>
                                <div class="font-weight-bold">Kontraksi</div>
                                <LineChartKontraksi class="mt-24 w-100"/><br>
                                <div class="accordion" role="tablist">
                                    <b-card no-body class="mb-1">
                                        <b-card-header block v-b-toggle="'accordion-'+3" header-tag="header" class="pointer d-flex justify-content-between align-items-center" role="tab" onMouseOver="this.style.backgroundColor='#648DE1'; this.style.color='#FFFFFF'" onMouseOut="this.style.backgroundColor='#FFFFFF'; this.style.color='#212529'" >
                                            <div>
                                                <div class="font-weight-bold">+ OKSITOSIN & CAIRAN</div>
                                                <div></div>
                                            </div>
                                            <feather-icon icon="ChevronDownIcon" size="18" />
                                        </b-card-header>
                                        <b-collapse :id="`accordion-`+3" accordion="my-accordion" role="tabpanel">
                                            <b-card-body>
                                            </b-card-body>
                                        </b-collapse>
                                    </b-card>
                                </div>
                                <div class="font-weight-bold">Oksitosin</div>
                                <LineChartOksitosin class="mt-24 w-100"/><br>
                                <div class="font-weight-bold">Obat & Cairan</div>
                                <LineChartObatCairan class="mt-24 w-100"/><br>
                                <div class="accordion" role="tablist">
                                    <b-card no-body class="mb-1">
                                        <b-card-header block v-b-toggle="'accordion-'+3" header-tag="header" class="pointer d-flex justify-content-between align-items-center" role="tab" onMouseOver="this.style.backgroundColor='#648DE1'; this.style.color='#FFFFFF'" onMouseOut="this.style.backgroundColor='#FFFFFF'; this.style.color='#212529'" >
                                            <div>
                                                <div class="font-weight-bold">+ TENSI, NADI, SUHU BADAN & URIN</div>
                                                <div></div>
                                            </div>
                                            <feather-icon icon="ChevronDownIcon" size="18" />
                                        </b-card-header>
                                        <b-collapse :id="`accordion-`+3" accordion="my-accordion" role="tabpanel">
                                            <b-card-body>
                                                
                                            </b-card-body>
                                        </b-collapse>
                                    </b-card>
                                </div>
                                <div class="font-weight-bold">Tensi & Nadi</div>
                                <LineChartTensiNadi class="mt-24 w-100"/><br>
                                <div class="font-weight-bold">Suhu Badan</div>
                                <LineChartSuhuBadan class="mt-24 w-100"/><br>
                                <div class="font-weight-bold">Urin</div>
                                <LineChartUrin class="mt-24 w-100"/><br>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <section class="col-12">
                <div class="card-header bg-indigo-200">
                    <span class="text-bold-18">Catatan Kehamilan</span>
                </div>
                <!-- <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                    style="margin-left:-16px; margin-right:-16px;">CATATAN PERSALINAN
                </div> -->
                <section class="">
                    <div>
                        <label class="c-form-label">Tanggal</label>
                        <b-form-input v-model="form_record.date_birth" type="date" />
                    </div>
                    <!-- <div class="mt-12">
                        <label class="c-form-label">Nama Bidan</label>
                        <b-form-input v-model="form_record."/>
                    </div> -->
                    <div class="mt-12">
                        <label class="c-form-label">Tempat Persalinan</label>
                        <b-form-radio-group v-model="form_record.birthing_place_type_id" class="">
                            <b-form-radio value="1">Rumah Ibu</b-form-radio>
                            <b-form-radio value="2">Polindes</b-form-radio>
                            <b-form-radio value="3">Klinik Swasta</b-form-radio>
                            <b-form-radio value="4">Puskesmas</b-form-radio>
                            <b-form-radio value="5">Rumah Sakit</b-form-radio>
                            <b-form-radio value="6">Lainnya</b-form-radio>
                        </b-form-radio-group>
                        <template v-if="form_record.birthing_place_type_id == 6">                                
                            <b-form-input v-model="form_record.birthing_place_other" class="mt-10" />
                        </template>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Alamat tempat persalinan</label>
                        <b-textarea v-model="form_record.birthing_place_address" rows="2" />                                 
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Catatan</label>
                        <b-textarea v-model="form_record.note" rows="2" />                                 
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Alasan merujuk</label>
                        <b-textarea v-model="form_record.referral_reason" rows="2" />                                 
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Tempat Rujukan</label>
                        <b-form-input v-model="form_record.referral_place" />
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Pendamping pada saat merujuk</label>
                        <b-form-radio-group v-model="form_record.referral_companion_type_id" class="">
                            <b-form-radio value="1">Bidan</b-form-radio>
                            <b-form-radio value="2">Teman</b-form-radio>
                            <b-form-radio value="3">Suami</b-form-radio>
                            <b-form-radio value="4">Dukun</b-form-radio>
                            <b-form-radio value="5">Keluarga</b-form-radio>
                            <b-form-radio value="6">Tidak ada</b-form-radio>
                        </b-form-radio-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Masalah dalam kehamilan/persalinan ini</label>
                        <b-form-radio-group v-model="form_record.problem_type_id" class="">
                            <b-form-radio value="1">Gawat daruratn</b-form-radio>
                            <b-form-radio value="2">Pendarahan</b-form-radio>
                            <b-form-radio value="3">HDK</b-form-radio>
                            <b-form-radio value="4">Infeksi</b-form-radio>
                            <b-form-radio value="5">PMTCT</b-form-radio>
                        </b-form-radio-group>
                    </div>
    
                </section>
            </section>

            <section class="col-12 mt-32">
                <ModernLabel label="KALA I" />
                <div class="mt-16">
                    <div>
                        <label class="c-form-label">Temuan pada fase laten</label>
                        <b-form-input v-model="form_kala.kala_one_latent_phase" />
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Perlu Intervensi</label>
                        <b-form-radio-group v-model="form_kala.kala_one_intervention_type_id" class="">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya</b-form-radio>
                        </b-form-radio-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Grafis dilatasi melewati garis waspada</label>
                        <b-form-radio-group v-model="form_kala.kala_one_alert_line_type_id" class="">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya</b-form-radio>
                        </b-form-radio-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Masalah pada fase aktif, sebutkan</label>
                        <b-form-input v-model="form_kala.kala_one_active_phase"/>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Penatalaksanaan maslaah tersebut</label>
                        <b-form-input v-model="form_kala.kala_one_problem_management" />
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Hasil</label>
                        <b-textarea v-model="form_kala.kala_one_result" rows="2"/>                              
                    </div>
                </div>
            </section class="col-12">

            <section class="col-12 mt-32">
                <ModernLabel label="KALA II" />
                <div class="mt-16">
                    <div>
                        <label class="c-form-label">Episiotomi</label>
                        <b-form-radio-group v-model="form_kala.kala_two_episiotomy_type_id" class="">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya, Indikasi</b-form-radio>
                        </b-form-radio-group>
                        <template v-if="form_kala.kala_two_episiotomy_type_id == 1">
                            <b-form-input v-model="form_kala.kala_two_episiotomy" class="mt-10" />                                
                        </template>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Pendampingan pada saat persalinan</label>
                        <b-form-radio-group v-model="form_kala.kala_two_companion_type_id" class="">
                            <b-form-radio value="1">Suami</b-form-radio>
                            <b-form-radio value="2">Keluarga</b-form-radio>
                            <b-form-radio value="3">Teman</b-form-radio>
                            <b-form-radio value="4">Dukun</b-form-radio>
                            <b-form-radio value="5">Tidak ada</b-form-radio>
                        </b-form-radio-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Gawat Janin</label>
                        <b-form-radio-group v-model="form_kala.kala_two_fetal_distress_type_id" class="">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya, tindakan yang dilakukan</b-form-radio>
                        </b-form-radio-group>
                    </div>
                    <div v-if="form_kala.kala_two_fetal_distress_type_id == 1" class="mt-16">
                        <b-table-simple responseived bordered style="font-size: 13px">
                            <b-thead>
                                <b-tr>
                                    <b-td>No</b-td>
                                    <b-td>Tindakan</b-td>
                                    <b-td>Aksi</b-td>
                                </b-tr>
                            </b-thead>
                            <b-tbody>
                                <b-tr v-for="(item, index) in form_kala.kala_two_fetal_distress_arry" :key="index">  
                                    <b-td>{{ index+1 }}</b-td>
                                    <b-td>{{ item }}</b-td>
                                    <b-td>
                                        <b-button @click="removeFetal(index)" variant="danger-700" size="sm" >
                                            <feather-icon icon="TrashIcon" />
                                        </b-button>
                                    </b-td>
                                </b-tr>
                            </b-tbody>
                        </b-table-simple>
                        <div class="mt-12">
                            <b-textarea v-model="input_fetal" />                                    
                            <b-button @click="addFetal" variant="primary" class="mt-12">Tambah</b-button>
                        </div>
                    </div>
                    <div v-if="form_kala.kala_two_fetal_distress_type_id == 0" class="mt-12">
                        <label class="c-form-label">Pemantauan DJJ setiap 5-10 menit selama kala II, Hasil</label>
                        <b-textarea v-model="form_kala.kala_two_monitoring_result" rows="2" />                                    
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Distosia Bahu</label>
                        <b-form-radio-group v-model="form_kala.kala_two_shoulder_dystocia_type_id" class="">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya, Tindakan yang dilakukan</b-form-radio>
                        </b-form-radio-group>
                        <template v-if="form_kala.form_kalakala_two_shoulder_dystocia_type_id == 1">
                            <b-form-input v-model="form_kala.kala_two_shoulder_dystocia" class="mt-10" />
                        </template>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Masalah lain, Penatalaksanaan masalah tsb dan hasilnya</label>
                        <b-textarea v-model="form_kala.kala_two_problem_management" rows="2"/>                                    
                    </div>
                </div>
            </section>

            <section class="col-12 mt-32">
                <ModernLabel label="KALA III" />
                <div class="mt-16">
                    <div>
                        <label class="c-form-label">Inisiasi Menyusu Dini</label>
                        <b-form-radio-group v-model="form_kala.kala_three_initiation_type_id" class="">
                            <b-form-radio value="0">Tidak, alasannya</b-form-radio>
                            <b-form-radio value="1">Ya</b-form-radio>
                        </b-form-radio-group>
                        <template v-if="form_kala.kala_three_initiation_type_id == 1">                                
                            <b-form-input v-model="form_kala.kala_three_initiation" class="mt-10" />
                        </template>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Lama kala III</label>
                        <b-input-group append="menit">
                            <b-form-input v-model="form_kala.kala_three_long" type="number" autocomplete="off"></b-form-input>
                        </b-input-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Pemberian Oksitosin 10 U im</label>
                        <b-form-radio-group v-model="form_kala.kala_three_oxytocin_type_id" class="">
                            <b-form-radio value="0">Tidak, alasan</b-form-radio>
                            <b-form-radio value="1">Ya, waktu</b-form-radio>
                        </b-form-radio-group>
                        <template v-if="form_kala.kala_three_oxytocin_type_id == 0">                                
                            <b-form-input v-model="form_kala.kala_three_oxytocin_reason" placeholder="alasan"/>
                        </template>
                        <template v-else-if="form_kala.kala_three_oxytocin_type_id == 1">                                
                            <b-input-group append="menit sesudah persalinan">
                                <b-form-input v-model="form_kala.kala_three_oxytocin" type="number" placeholder="waktu" autocomplete="off"></b-form-input>
                            </b-input-group>
                        </template>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Pemberian ulang oksitosan (2x)</label>
                        <b-form-radio-group v-model="form_kala.kala_three_oxytocin_second_type_id" class="">
                            <b-form-radio value="0">Tidak, alasannya</b-form-radio>
                            <b-form-radio value="1">Ya</b-form-radio>
                        </b-form-radio-group>
                        <template v-if="form_kala.kala_three_oxytocin_second_type_id == 0">                                
                            <b-form-input v-model="form_kala.kala_three_oxytocin_second" class="mt-10" />      
                        </template>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Penegangan tali pusat terkendali</label>
                        <b-form-radio-group v-model="form_kala.kala_three_umbilical_cord_type_id" class="">
                            <b-form-radio value="0">Tidak, alasannya</b-form-radio>
                            <b-form-radio value="1">Ya</b-form-radio>
                        </b-form-radio-group>
                        <template v-if="form_kala.kala_three_umbilical_cord_type_id == 0">                                
                            <b-form-input v-model="form_kala.kala_three_umbilical_cord" class="mt-10" />      
                        </template>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Massase fundus uteri</label>
                        <b-form-radio-group v-model="form_kala.kala_three_uterine_fundus_type_id" class="">
                            <b-form-radio value="0">Tidak, alasannya</b-form-radio>
                            <b-form-radio value="1">Ya</b-form-radio>
                        </b-form-radio-group>
                        <template v-if="form_kala.kala_three_uterine_fundus_type_id == 0">                                
                            <b-form-input v-model="form_kala.kala_three_uterine_fundus" class="mt-10" />      
                        </template>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Plasenta dahi lengkap (intact)</label>
                        <b-form-radio-group v-model="form_kala.kala_three_placenta_type_id" class="">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya</b-form-radio>
                        </b-form-radio-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Jika tidak lengkap, tindakan yang dilakukan</label>
                        <!-- disini juga belom -->
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Plasenta tidak lahir &gt; 30 menit</label>
                        <b-form-radio-group v-model="form_kala.kala_three_placenta_not_type_id" class="">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya, Tindakan</b-form-radio>
                        </b-form-radio-group>
                        <template v-if="form_kala.kala_three_placenta_not_type_id == 1">                                
                            <b-form-input v-model="form_kala.kala_three_placenta_not" class="mt-10" />      
                        </template>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Laserasi</label>
                        <b-form-radio-group v-model="form_kala.kala_three_laceration_type_id" class="">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya, dimana</b-form-radio>
                        </b-form-radio-group>
                        <template v-if="form_kala.kala_three_laceration_type_id == 1">                                
                            <b-form-input v-model="form_kala.kala_three_laceration" class="mt-10" />      
                        </template>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Jika laserasi parineum, derajat</label>
                        <b-form-radio-group v-model="form_kala.kala_three_parineum_degree_type_id" class="">
                            <b-form-radio value="1">1</b-form-radio>
                            <b-form-radio value="2">2</b-form-radio>
                            <b-form-radio value="3">3</b-form-radio>
                            <b-form-radio value="4">4</b-form-radio>
                        </b-form-radio-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Jika laserasi parineum, tindakan</label>
                        <b-form-radio-group v-model="form_kala.kala_three_parineum_act_type_id" class="">
                            <b-form-radio value="1">Penjahitan, dengan / tanpa anestesi</b-form-radio>
                            <b-form-radio value="2">Tidak dijahit, alasan</b-form-radio>
                        </b-form-radio-group>
                        <template v-if="form_kala.kala_three_parineum_act_type_id == 2">                                
                            <b-form-input v-model="form_kala.kala_three_parineum_act" class="mt-10" />
                        </template>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Atoni uteri</label>
                        <b-form-radio-group v-model="form_kala.kala_three_uterine_atony_type_id" class="">
                            <b-form-radio value="0">Tidak</b-form-radio>
                            <b-form-radio value="1">Ya, tindakan</b-form-radio>
                        </b-form-radio-group>
                        <template v-if="form_kala.kala_three_uterine_atony_type_id == 1">                                
                            <b-form-input v-model="form_kala.kala_three_uterine_atony" class="mt-10" />
                        </template>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Jumlah darah yang keluar / pendarahan</label>
                        <b-input-group append="ml">
                            <b-form-input v-model="form_kala.kala_three_amount_blood" type="number" autocomplete="off"></b-form-input>
                        </b-input-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Masalah lain pada kala III dan Penatalaksanaannya</label>
                        <b-form-input v-model="form_kala.kala_three_problem_management" />
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Hasilnya</label>
                        <b-textarea v-model="form_kala.kala_three_result" rows="2"/>                                    
                    </div>
                </div>
            </section>

            <section class="col-12 mt-32">
                <ModernLabel label="KALA IV" />
                <div class="mt-16">
                    <label class="c-form-label">Kondisi Ibu</label>
                    <div>
                        <label class="c-form-label">KU</label>
                        <b-form-input v-model="form_kala.kala_four_mother_ku" />
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">TD</label>
                        <b-input-group append="mmHg">
                            <b-form-input v-model="form_kala.kala_four_mother_td" type="number" autocomplete="off" />
                        </b-input-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Nadi</label>
                        <b-input-group append="x/i">
                            <b-form-input v-model="form_kala.kala_four_pulse" type="number" autocomplete="off" />
                        </b-input-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Napas</label>
                        <b-input-group append="x/i">
                            <b-form-input v-model="form_kala.kala_four_breath" type="number" autocomplete="off"></b-form-input>
                        </b-input-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Masalah kala IV dan Penatalaksanaannya</label>
                        <b-form-input v-model="form_kala.kala_four_problem_management" />
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Hasilnya</label>
                        <b-textarea v-model="form_kala.kala_four_result" rows="2" />                               
                    </div>
                </div>
            </section>

            <section class="col-12 mt-32">
                <ModernLabel label="Bayi baru lahir" />
                <div class="mt-16">
                    <div>
                        <label class="c-form-label">Berat Badan</label>
                        <b-input-group append="gram">
                            <b-form-input v-model="form_record.newborn_baby_weight" type="number" autocomplete="off" />
                        </b-input-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Panjang Badan</label>
                        <b-input-group append="cm">
                            <b-form-input v-model="form_record.newborn_baby_height" type="number" autocomplete="off" />
                        </b-input-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Jenis Kelamin</label>
                        <b-form-radio-group v-model="form_record.newborn_baby_sex_type_id" class="">
                            <b-form-radio value="1">Laki-laki</b-form-radio>
                            <b-form-radio value="2">Perempuan</b-form-radio>
                        </b-form-radio-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Penilaian Bayi Baru Lahir</label>
                        <b-form-radio-group v-model="form_record.newborn_baby_assessment_type_id" class="">
                            <b-form-radio value="1">Baik</b-form-radio>
                            <b-form-radio value="2">Ada penyulit</b-form-radio>
                        </b-form-radio-group>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Bayi Lahir</label>
                        <b-form-radio-group v-model="form_record.newborn_baby_type_id" class="">
                            <b-form-radio value="1">Normal</b-form-radio>
                            <b-form-radio value="2">Asfiksia</b-form-radio>
                            <b-form-radio value="3">Cacat Bawaan</b-form-radio>
                            <b-form-radio value="4">Hipotemi</b-form-radio>
                        </b-form-radio-group>
                    </div>
                    <div v-if="form_record.newborn_baby_type_id == 1" class="mt-12">
                        <label class="c-form-label">Tindakan</label>
                        <b-form-checkbox-group v-model="form_record.newborn_baby_normal_arry" class="">
                            <b-form-checkbox value="a" class="my-1">Menghangatkan</b-form-checkbox>
                            <b-form-checkbox value="b" class="my-1">Mengeringkan</b-form-checkbox>
                            <b-form-checkbox value="c" class="my-1">IMD atau naluri menyusu segera</b-form-checkbox>
                            <b-form-checkbox value="d" class="my-1">Rangsangan taktil</b-form-checkbox>
                            <b-form-checkbox value="e" class="my-1">Tetes mata profilaksis, vit K, Imunisasi Hepatitis B</b-form-checkbox>
                        </b-form-checkbox-group>
                    </div>
                    <div v-if="form_record.newborn_baby_type_id == 2" class="mt-12">
                        <label class="c-form-label">Tindakan</label>
                        <b-form-checkbox-group v-model="form_record.newborn_baby_asphyxia_arry" class="">
                            <b-form-checkbox value="a" class="my-1">Menghangatkan</b-form-checkbox>
                            <b-form-checkbox value="b" class="my-1">Mengeringkan</b-form-checkbox>
                            <b-form-checkbox value="c" class="my-1">Rangsangan taktil</b-form-checkbox>
                            <b-form-checkbox value="d" class="my-1">Asuhann pasca resusitasi</b-form-checkbox>
                            <b-form-checkbox value="e" class="my-1">Bebaskan jalan napas (posisi & isap lendir)</b-form-checkbox>
                            <b-form-checkbox value="f" class="my-1">Ventilasi positif (jika perlu)</b-form-checkbox>
                            <b-form-checkbox value="g" class="my-1">Lain-lain, sebutkan</b-form-checkbox>
                        </b-form-checkbox-group>
                        <template v-if="form_record.newborn_baby_asphyxia_arry.includes('g')">
                            <b-form-input v-model="form_record.newborn_baby_asphyxia" class="mt-10"/>
                        </template>
                    </div>
                    <div v-if="form_record.newborn_baby_type_id == 3" class="mt-12">
                        <label class="c-form-label">Sebutkan</label>
                        <b-form-input v-model="form_record.newborn_baby_disabled" class=""/>
                    </div>
                    <div v-if="form_record.newborn_baby_type_id == 4" class="mt-12">
                        <label class="c-form-label">Tindakan</label>
                        <b-table-simple responseived bordered style="font-size: 13px">
                            <b-thead>
                                <b-tr>
                                    <b-td>No</b-td>
                                    <b-td>Tindakan</b-td>
                                    <b-td>Aksi</b-td>
                                </b-tr>
                            </b-thead>
                            <b-tbody>
                                <b-tr v-for="(item, index) in form_record.newborn_baby_hypothermia_arry" :key="index">  
                                    <b-td>{{ index+1 }}</b-td>
                                    <b-td>{{ item }}</b-td>
                                    <b-td>
                                        <b-button @click="removeHipotemi(index)" variant="danger-700" size="sm" >
                                            <feather-icon icon="TrashIcon" />
                                        </b-button>
                                    </b-td>
                                </b-tr>
                            </b-tbody>
                        </b-table-simple>
                        <div class="mt-12">
                            <b-textarea v-model="input_hipotemi" />                                    
                            <b-button @click="addHipotemi" variant="primary" class="mt-12">Tambah</b-button>
                        </div>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Pemberian ASI setelah jam pertama bayi lahir</label>
                        <b-form-radio-group v-model="form_record.newborn_baby_breast_milk_type_id" class="">
                            <b-form-radio value="0">Tidak, alasan</b-form-radio>
                            <b-form-radio value="1">Ya, waktu</b-form-radio>
                        </b-form-radio-group>
                        <template v-if="form_record.newborn_baby_breast_milk_type_id == 0">
                            <b-form-input v-model="form_record.newborn_baby_breast_milk_not" class="mt-10"/>
                        </template>
                        <template v-if="form_record.newborn_baby_breast_milk_type_id == 1">
                            <b-input-group append="Jam setelah bayi lahir" class="mt-10">
                                <b-form-input v-model="form_record.newborn_baby_breast_milk_hours" type="number" autocomplete="off" />
                            </b-input-group>
                        </template>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Masalah lain</label>
                        <b-form-input v-model="form_record.newborn_baby_problem_management"/>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Penatalaksanaan dan Hasilnya</label>
                        <b-textarea v-model="form_record.newborn_baby_result" rows="2" />                        
                    </div>
                </div>
            </section>

            <section class="col-12 mt-32">
                <b-table responsived bordered :fields="kala.fields" :items="kala.items">
                    <template #cell(no)="row">{{ row.index+1 }}</template>
                    <template #cell(action)="row">
                        <b-button @click="removeKala(row.index)" variant="danger-700" size="sm" >
                            <feather-icon icon="TrashIcon" />
                        </b-button>
                    </template>
                </b-table>
                <div class="row">
                    <div class="col-lg-2 mt-12">
                        <label class="c-form-label">Jam ke</label>
                        <b-form-input v-model="input_kala.shift"/>
                    </div>
                    <div class="col-lg-2 mt-12">
                        <label class="c-form-label">Waktu</label>
                        <b-form-input type="time" v-model="input_kala.time"/>
                    </div>
                    <div class="col-lg-2 mt-12">
                        <label class="c-form-label">Tekanan Darah</label>
                        <b-form-input type="number" v-model="input_kala.blood_pressure"/>
                    </div>
                    <div class="col-lg-2 mt-12">
                        <label class="c-form-label">Tekanan Nadi</label>
                        <b-form-input type="number" v-model="input_kala.pulse"/>
                    </div>
                    <div class="col-lg-2 mt-12">
                        <label class="c-form-label">Temperatur (c)</label>
                        <b-form-input type="number" v-model="input_kala.temperature"/>
                    </div>
                    <div class="col-lg-2 mt-12">
                        <label class="c-form-label">Tinggi Fundus Uteri</label>
                        <b-form-input v-model="input_kala.fundus"/>
                    </div>
                    <div class="col-lg-2 mt-12">
                        <label class="c-form-label">Kontraksi Uterus</label>
                        <b-form-input v-model="input_kala.contraction"/>
                    </div>
                    <div class="col-lg-2 mt-12">
                        <label class="c-form-label">Kandung Kemih ≤ Urine</label>
                        <b-form-input v-model="input_kala.bladder"/>
                    </div>
                    <div class="col-lg-2 mt-12">
                        <label class="c-form-label">≤ Darah Keluar</label>
                        <b-form-input v-model="input_kala.out_blood"/>
                    </div>
                </div>
                <b-button @click="addKala" variant="primary" class="mt-12">Tambah</b-button>
            </section>
        </b-modal>

        <!-- IGD / RAWAT JALAN -->

        <div class="d-flex w-100 px-3 justify-content-lg-end mt-20">   
            <!-- <template v-if="(isIGD || isRajal) && regData.is_checkout == 0">                     -->
                <template v-if="(isIGD || isRajal)">                    
                <b-button @click="active.modal_checkout = !active.modal_checkout" pill variant="orange-500" size="sm" class="px-16 mr-2">
                    <feather-icon icon="LogOutIcon" /> <span>Checkout</span>
                </b-button>
            </template>
                      
            <template v-if="isIGD || isRajal">                    
                <b-button @click="openModalSPRI()" pill variant="indigo-400" size="sm" class="px-16 mr-2">
                    <b-icon :icon="form_spri.id? 'eye' : 'plus'" /> <span>{{ form_spri.id ? 'Lihat SPRI' : 'SPRI'}}</span>
                </b-button>
            </template>

            <template v-if="isRanap">   
                <b-button @click="triggerInpatientModal('main_gate')" pill variant="info-600" size="sm" class="px-16 ml-lg-2 mt-8 mt-lg-0">
                    <b-icon icon="plus" /> <span>Kajian Awal Ranap</span>
                </b-button>                 
            </template>
            <InpatientStudies ref="childInRef"/>

            <template v-if="isIGD">                    
                <EmergencyStudies />
            </template>

            <template v-if="isRajal">
                <b-button @click="openModalBooking('ADD', null)" pill variant="success-600" size="sm" class="px-16 mr-2">
                    <b-icon icon="plus" /> <span>Booking Penunjang</span>
                </b-button>
                <b-button @click="triggerOutpatientModal('main_gate')" pill variant="info-600" size="sm" class="px-16 mt-8 mt-lg-0">
                    <b-icon icon="plus" /> <span>Kajian Kedokteran</span>
                </b-button>
            </template>
            <OutpatientStudies ref="childOutRef" />

            <!-- <b-button @click="openModalPartograf()" pill variant="indigo-400" size="sm" class="px-16 mr-2">
                <b-icon icon="plus" /> <span>PARTOGRAF</span>
            </b-button>      -->
                          
            <!-- <b-button @click="openModalNewBorn()" pill variant="indigo-400" size="sm" class="px-16 mr-2">
                <b-icon icon="plus" /> <span>BAYI BARU LAHIR</span>
            </b-button> -->
        </div>

        <template v-for="(result,i) in items" >
            <div class="card-body mt-20 shadow-sm">
                <section class="card mt-24" :style="isSimilar(result.registration_destination_id)">
                    <div class="card-header bg-white">
                        <section>
                            <ModernLabel :label="getSoapLabel(result.health_facility_id, result.is_initial_inpatient, result.is_booking)" :small="`Added on ${result.date_added} Oleh ${result.employee_doctor_name}`" />

                            <template v-if="userData.user_group.id == 'a4c04d8d-12d8-4c54-afca-69de6bd5f62d' || userData.employee_id == 'd77142c4-4777-47d8-bd0c-fd7dfd134af4'">
                                <router-link :to="{ path: '/patient/jkn/'+ result.registration_destination_id + '/' + result.id}" target="_blank">
                                    <b-button pill size="sm" variant="orange-500" class="px-16 pull-right mt-2 text-white mr-2">
                                        <feather-icon icon="ZapIcon" /> <span>JKN Access</span>
                                    </b-button>
                                </router-link>
                            </template>
                            <router-link :to="{ path: '/prints/doctor-studies-print/'+ result.id}" target="_blank">
                                <b-button pill size="sm" variant="success-500" class="px-16 pull-right mt-2 text-white mr-2">
                                    <b-icon icon="printer" /> <span>Cetak Kajian</span>
                                </b-button>
                            </router-link>

                            <template v-if="result.health_facility_id == '444b3134-5b75-4b67-af72-a558d46716d7'">
                                <router-link :to="{ path: '/prints/doctor-emergency-print/'+ result.id}" target="_blank">
                                    <b-button pill size="sm" variant="success-500" class="px-16 pull-right mt-2 text-white mr-2">
                                        <b-icon icon="printer" /> <span>Cetak Pengkajian IGD Version</span>
                                    </b-button>
                                </router-link>
                            </template>

                            <router-link :to="{ path: '/prints/doctor-sbpk-print/'+ result.id}" target="_blank">
                                <b-button pill size="sm" variant="primary-500" class="px-16 pull-right mt-2 text-white mr-2">
                                    <b-icon icon="printer" /> <span>Cetak SBPK</span>
                                </b-button>
                            </router-link>

                            <template v-if="getOutpatientCondition(result.doctor_in_charge_id, result.health_facility_id, result.is_resume_exit, result.is_booking)">
                                <b-button @click="triggerOutpatientModal(result)" pill size="sm" variant="primary-600" class="px-16 pull-right mt-2 mr-2">
                                    <i><img src="@/@core/assets/icons/nurse/file-eye.svg" alt=""></i> <span>Buka Kajian</span>
                                </b-button>
                            </template>

                            <template v-if="getInpatientCondition(result.doctor_in_charge_id, result.health_facility_id, result.is_initial_inpatient)">
                                <b-button @click="triggerInpatientModal(result)" pill size="sm" variant="primary-600" class="px-16 pull-right mt-2 mr-2">
                                    <i><img src="@/@core/assets/icons/nurse/file-eye.svg" alt=""></i> <span>Buka Kajian</span>
                                </b-button>
                            </template>

                            <template v-if="result.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e' && result.is_hemodialysis == 1">
                                <router-link :to="{ path: '/prints/doctor-hemodialysis-print/'+ result.id}" target="_blank">
                                    <b-button pill size="sm" variant="success-500" class="px-16 pull-right mt-2 mr-2">
                                        <feather-icon icon="PrinterIcon" class="mr-2" /> Hemodialisa
                                    </b-button>
                                </router-link>
                            </template>

                            <template v-if="isRajal && result.health_facility_id == '3cb626f5-9148-4fb1-8663-3d7f028b01a6' || result.health_facility_id == '444b3134-5b75-4b67-af72-a558d46716d7' || result.health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f' && result.is_resume_exit != 1 && result.is_booking != 1">
                                <b-button pill size="sm" variant="w3green" @click="openModalExamination(result.id)" class="px-16 pull-right mt-2 mr-2">
                                    <feather-icon icon="PlusIcon" class="mr-2" /> Penunjang Lainnya
                                </b-button>
                            </template>

                            <b-dropdown v-if="result.health_facility_id == '3cb626f5-9148-4fb1-8663-3d7f028b01a6' && result.is_booking == 1" size="sm" id="dropdown-aria" text="Booking" variant="success-500" class="mt-2 mr-2 custom-dropdown">
                                <template v-if="isRajal">
                                    <b-dropdown-item-button @click="openModalBooking('EDIT', result)">Edit Booking</b-dropdown-item-button>
                                    <b-dropdown-divider></b-dropdown-divider>
                                </template>

                                <template v-if="result.health_facility_id == '3cb626f5-9148-4fb1-8663-3d7f028b01a6' && result.is_booking == 1 && result.laboratory_request">
                                    <b-dropdown-header id="dropdown-header-1">LABORATORIUM</b-dropdown-header>
                                    <router-link :to="{ path: '/prints/booking-laboratory-print/'+ result.id}" target="_blank">
                                        <b-dropdown-item-button aria-describedby="dropdown-header-1">Cetak</b-dropdown-item-button>
                                    </router-link>
                                    <b-dropdown-item-button @click="cancelLaboratoryRequest(result.laboratory_request)" :disabled="result.laboratory_request.status == 2 || result.laboratory_request.status == 3" aria-describedby="dropdown-header-1">Batalkan Order</b-dropdown-item-button>
                                </template>
                                <template v-if="result.health_facility_id == '3cb626f5-9148-4fb1-8663-3d7f028b01a6' && result.is_booking == 1 && result.radiology_request">
                                    <b-dropdown-header id="dropdown-header-2">RADIOLOGI</b-dropdown-header>
                                    <router-link :to="{ path: '/prints/booking-radiology-print/'+ result.id}" target="_blank">
                                        <b-dropdown-item-button aria-describedby="dropdown-header-2">Cetak</b-dropdown-item-button>
                                    </router-link>
                                    <b-dropdown-item-button @click="cancelRadiologyRequest(result.radiology_request)" :disabled="result.radiology_request.status == 2 || result.radiology_request.status == 3" aria-describedby="dropdown-header-2">Batalkan Order</b-dropdown-item-button>
                                </template>
                                <template v-if="result.health_facility_id == '3cb626f5-9148-4fb1-8663-3d7f028b01a6' && result.is_booking == 1 && result.physiotherapy_request">
                                    <b-dropdown-header id="dropdown-header-2">FISIOTERAPI</b-dropdown-header>
                                    <router-link :to="{ path: '/prints/doctor-rehabilitation-schedule-print/'+ result.id}" target="_blank">
                                        <b-dropdown-item-button>Cetak Jadwal</b-dropdown-item-button>
                                    </router-link>
                                </template>
                            </b-dropdown>

                            <b-dropdown v-if="result.health_facility_item_id == '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a' && result.is_medical_rehabilitation == 1" size="sm" id="dropdown-aria" text="Uji Fungsi" variant="success-500" class="mt-2 mr-2 custom-dropdown">
                                <router-link :to="{ path: '/prints/doctor-rehabilitation-print/'+ result.id}" target="_blank">
                                    <b-dropdown-item-button>Cetak Formulir</b-dropdown-item-button>
                                </router-link>
                                <router-link :to="{ path: '/prints/doctor-rehabilitation-result-print/'+ result.id}" target="_blank">
                                    <b-dropdown-item-button>Cetak Hasil</b-dropdown-item-button>
                                </router-link>
                            </b-dropdown>
                        </section>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-9">
                                <div class="row">
                                    <section class="col-lg-4 mt-16 mt-lg-0">
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
                                    <section class="col-lg-4 mt-16 mt-lg-0">
                                        <div class="text-uppercase text-bold-16 text-success-500">Objective</div>
                                        <div class="mt-12">
                                            <b-table-simple small class="text-regular-12">
                                                <b-tr>
                                                    <div class="text-uppercase text-bold-12 text-primary-500">Vitals :</div>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>
                                                        Systolic = {{ result.systolic_blood_pressure ? result.systolic_blood_pressure : '-' }},
                                                        Diastolic = {{ result.diastolic_blood_pressure ? result.diastolic_blood_pressure : '-' }},
                                                        Frekuensi Nadi = {{ result.pulse_frequency ? result.pulse_frequency : '-' }},
                                                        Frekuensi Nafas = {{ result.breath_frequency ? result.breath_frequency : '-' }},
                                                        Saturasi Oksigen = {{ result.oximetry_frequency ? result.oximetry_frequency : '-' }},
                                                        Suhu = {{ result.temperature ? result.temperature : '-' }},
                                                        Skor Nyeri = {{ result.sore_score ? result.sore_score : '-' }},
                                                        EWS = {{ result.ews ? result.ews : '-' }},
                                                        Gula Darah = {{ result.blood_sugar ? result.blood_sugar : '-' }}
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
                                                        Berat Badan = {{ result.weight ? result.weight : '-' }},
                                                        Tinggi Badan = {{ result.height ? result.height : '-' }},
                                                        IMT = {{ result.imt ? result.imt : '-' }},
                                                        Lingkar Kepala = {{ result.head_circumference ? result.head_circumference : '-' }},
                                                        Lingkar Dada = {{ result.chest_circumference ? result.chest_circumference : '-' }},
                                                        Lingkar Lengan Atas = {{ result.arm_circumference ? result.arm_circumference : '-' }},
                                                        Lingkar Perut = {{ result.stomach_circumference ? result.stomach_circumference : '-' }},
                                                        Lingkar Lutut = {{ result.knee_circumference ? result.knee_circumference : '-' }}
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
                                    <section class="col-lg-4 mt-16 mt-lg-0">
                                        <div class="text-uppercase text-bold-16 text-success-500">Assesment</div>
                                        <div class="mt-12">
                                            <b-table-simple small class="text-regular-12">
                                                <b-tr>
                                                    <div class="text-uppercase text-bold-12 text-primary-500">Diagnosis :</div>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>
                                                        <div>
                                                            <div v-if="result.icd10 && result.icd10.length != 0">
                                                                <template v-for="(iicd, index) in result.icd10">
                                                                    <div v-if="index === 0" class="mt-1">
                                                                        <b>Utama:</b> {{ iicd.code }} {{ iicd.name }}
                                                                    </div>
                                                                </template>
                                                                <template v-for="(iicd, index) in result.icd10">
                                                                    <div v-if="result.icd10.length > 1" class="mt-1">
                                                                        <div v-if="index > 0"><b>Tambahan:</b> {{ iicd.code }} {{ iicd.name }}</div>
                                                                    </div>
                                                                    <div v-else class="mt-1">
                                                                        <b>Tambahan: -</b>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                            <div v-else class="mt-1">
                                                                <div><b>Utama: -</b></div>
                                                                <div><b>Tambahan: -</b></div>
                                                            </div>
                                                        </div>
                                                        <div class="mt-1"><b>Catatan ICD10 :</b> {{ result.icd10_notes ? result.icd10_notes : '-'}}</div>
                                                    </b-td>
                                                </b-tr>
                                            </b-table-simple>
                                        </div>
                                    </section>
                                </div>

                                <!-- <template v-if="result.health_facility_id == '444b3134-5b75-4b67-af72-a558d46716d7'"> -->
                                    <div>
                                        <div class="text-uppercase text-bold-16 text-success-500">Status Generalis</div>
                                        <b-collapse :id="`collapse-`+result.id" class="mb-8">
                                            <div class="row mt-16" style="font-size: 12px">
                                                <div class="col-lg-6">
                                                    <table class="w-100">
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="3" class="font-weight-bold">Kepala</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="width: 120px;">Kepala</td>
                                                                <td style="width: 30px;" class="px-3">:</td>
                                                                <td>{{ getHead(result.head) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Warna Rambut</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td>{{ getHairColor(result.head_hair_color) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Kondisi Rambut</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td>{{ getHairCondition(result.head_hair_condition) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3" class="font-weight-bold">Mata</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top" >Konjungtiva</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td>{{ getEyeConjunctive(result.eye_conjunctive) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Sklera</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td>{{ getEyeSclera(result.eye_sclera) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Pupil</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td>{{ getEyePupil(result.eye_pupil) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Reflek Cahaya</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td>{{ getEyeReflect(result.eye_light_reflex) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3" class="font-weight-bold">Mulut</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Mukosa</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td>{{ getMouthMucosa(result.mouth_mucosa) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Perubahan Warna</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td>{{ getMouthDiscoloration(result.mouth_discoloration) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Lidah</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td>{{ getMouthTongue(result.mouth_tongue) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Karies</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td>{{ getMouthCaries(result.mouth_caries) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3" class="font-weight-bold">Leher</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top" style="width: 120px;">KGB</td>
                                                                <td class="align-top px-3" style="width: 30px;">:</td>
                                                                <td>{{ getNeckKGB(result.neck_kgb) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Posisi Trakea</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td>{{ getNeckTracheaPosition(result.neck_trachea_position) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Massa</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td>{{ getNeckMassa(result.neck_massa) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3" class="font-weight-bold">Ekstremitas</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Akral</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td>{{ getEkstremitasAkral(result.extremity_acral) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">CRT</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td>{{ getEkstremitasCRT(result.extremity_crt) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Edema</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td>{{ getEkstremitasEdema(result.extremity_edema) }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-lg-6">
                                                    <table class="w-100">
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="3" class="font-weight-bold">Thoraks</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3" class="font-weight-bold text-primary">Inspeksi</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top" style="width: 120px;">Dada</td>
                                                                <td style="width: 30px;" class="align-top px-3">:</td>
                                                                <td>{{ getInspectionChest(result.thorax_insp_chest) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Retraksi</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td>{{ getInspectionRetractiont(result.thorax_insp_retraction) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3" class="font-weight-bold text-primary">Palpasi</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Dada</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td>{{ getPalpationChest(result.thorax_palp_chest) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Fremitus</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td>{{ getPalpationFremitus(result.thorax_palp_fremitus) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Krepitasi</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td>{{ getPalpationKrepitasi(result.thorax_palp_crepitation) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3" class="font-weight-bold text-primary">Auskultasi</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Pulmo Vasikuler</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td>{{ getAuskultationPulmoVasikuler(result.thorax_ausk_pul_vasiculer) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Pulmo Rhonki</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td>{{ getAuskultationPulmoRhonki(result.thorax_ausk_pul_rhonki) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Pulmo Wheezing</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td>{{ getAuskultationPulmoWheezing(result.thorax_ausk_pul_wheezing) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">COR Bunyi Jantung</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td>{{ getAuskultationCORBJ(result.thorax_ausk_cor_heart_sound) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">COR Murmur</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td>{{ getAuskultationCORMurmur(result.thorax_ausk_cor_murmur) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">COR Gallop</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td>{{ getAuskultationCORGallop(result.thorax_ausk_cor_gallop) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3" class="font-weight-bold">Abdomen</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Supel</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td>{{ getAbdomenSupel(result.abdomen_supel) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Bising Usus</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td>{{ getAbdomenBising(result.abdomen_bowel_sounds) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Nyeri Tekan</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td>{{ getAbdomenPress(result.abdomen_tenderness) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Hepar & Lien</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td>{{ getAbdomenHepar(result.abdomen_hepar_lien) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Pemeriksaan Abdomen Lainnya</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td class="align-top">
                                                                    <div v-html="parsedBody((result.abdomen_other_physical_check) ? (result.abdomen_other_physical_check) : '-')"></div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </b-collapse>
                                        <div class="w-100 d-flex align-items-center mt-12">
                                            <div class="d-flex pointer" v-b-toggle="'collapse-'+result.id">
                                                <feather-icon icon="ChevronsDownIcon" class="mr-8" />
                                                <small class="text-muted">Tampilkan Status Generalis</small>
                                            </div>
                                        </div>
                                    </div>
                                <!-- </template> -->

                                <template v-if="result.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e'">
                                    <div>
                                        <div class="text-uppercase text-bold-16 text-success-500">Hemodialisa</div>
                                        <b-collapse :id="`collapse-hemo-`+result.id" class="mb-8">
                                            <table class="w-100" style="font-size: 12px;">
                                                <tbody>
                                                    <tr>
                                                        <td class="align-top" style="width: 120px;">Diagnosis Utama</td>
                                                        <td class="align-top px-3" style="width: 30px;">:</td>
                                                        <td class="align-top">{{ result.main_diagnosis ? result.main_diagnosis : '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Etiologi</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td class="align-top">{{ result.etiology ? result.etiology : '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Penyakit Penyerta</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td class="align-top">{{ result.comorbidities ? result.comorbidities : '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Frekuensi HD</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td class="align-top">{{ result.frequency_hd ? result.frequency_hd : '-' }} x/minggu</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Akses Vaskular</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td class="align-top">{{ result.vascular_access ? result.vascular_access : '-' }}</td>
                                                    </tr>
                                                    <!-- <tr>
                                                        <td class="align-top">Resep HD</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td class="align-top">{{ result.prescription_hd }}</td>
                                                    </tr> -->
                                                </tbody>
                                            </table>
                                        </b-collapse>
                                        <div class="w-100 d-flex align-items-center mt-12">
                                            <div class="d-flex pointer" v-b-toggle="'collapse-hemo-'+result.id">
                                                <feather-icon icon="ChevronsDownIcon" class="mr-8" />
                                                <small class="text-muted">Tampilkan Hemodialisa</small>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <template v-if="result.health_facility_item_id == '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a'">
                                    <div>
                                        <div class="text-uppercase text-bold-16 text-success-500">Uji Fungsi/Prosedur KFR</div>
                                        <b-collapse :id="`collapse-rehab-`+result.id" class="mb-8">
                                            <div class="row mt-16" style="font-size: 12px">
                                                <div class="col-lg-6">
                                                    <table class="w-100">
                                                        <tbody>
                                                            <tr>
                                                                <td class="align-top" style="width: 150px;">Suspek Penyakit Akibat</td>
                                                                <td class="align-top px-3" style="width: 30px;">:</td>
                                                                <td class="align-top">{{ result.suspected_disease_type_id == '1' ?  `Ya, ${result.suspected_disease}` : 'Tidak' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Pemeriksaan & Uji Fungsi</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td class="align-top">{{ result.functional_testing ? result.functional_testing : '-' }}</td>
                                                            </tr>
                                                            <!-- <tr>
                                                                <td class="align-top">Evaluasi</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td class="align-top">{{ result.evaluation ? result.evaluation : '-' }}</td>
                                                            </tr> -->
                                                            <tr>
                                                                <td class="align-top">Diagnosa Fungsional</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td class="align-top">{{ result.functional_diagnosis ? result.functional_diagnosis : '-' }}</td>
                                                            </tr>
                                                            <!-- <tr>
                                                                <td class="align-top" style="width: 150px;">Tanggal Pelayanan</td>
                                                                <td class="align-top px-3" style="width: 30px;">:</td>
                                                                <td class="align-top">{{ result.service_date }}</td>
                                                            </tr> -->
                                                            <!-- <tr>
                                                                <td class="align-top">Anamnesa</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td class="align-top">{{ result.anamnesa }}</td>
                                                            </tr> -->
                                                            <!-- <tr>
                                                                <td class="align-top">Pemeriksaan Penunjang</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td class="align-top">{{ result.support_examination }}</td>
                                                            </tr> -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-lg-6">
                                                    <table class="w-100">
                                                        <tbody>                                                            
                                                            <tr>
                                                                <td class="align-top" style="width: 120px;">Diagnosa Medis</td>
                                                                <td class="align-top px-3" style="width: 30px;">:</td>
                                                                <td class="align-top">{{ result.medical_diagnosis ? result.medical_diagnosis : '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Hasil Yang Didapat</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td class="align-top">{{ result.result ? result.result : '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Kesimpulan</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td class="align-top">{{ result.conclusion ? result.conclusion : '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Rekomendasi</td>
                                                                <td class="align-top px-3">:</td>
                                                                <td class="align-top">{{ result.recommendation ? result.recommendation : '-' }}</td>
                                                            </tr>
                                                            <!-- <tr>
                                                                <td class="align-top" style="width: 120px;">Anjuran</td>
                                                                <td class="align-top px-3" style="width: 30px;">:</td>
                                                                <td class="align-top">{{ result.advice_rehab }}</td>
                                                            </tr> -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </b-collapse>
                                        <div class="w-100 d-flex align-items-center mt-12">
                                            <div class="d-flex pointer" v-b-toggle="'collapse-rehab-'+result.id">
                                                <feather-icon icon="ChevronsDownIcon" class="mr-8" />
                                                <small class="text-muted">Tampilkan Uji Fungsi/Prosedur KFR</small>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            
                            <section class="col-lg-3 mt-16 mt-lg-0">
                                <div class="text-uppercase text-bold-16 text-success-500">Planning</div>
                                <div class="mt-12">
                                    <b-table-simple small class="text-regular-12">
                                        <b-tr>
                                            <div class="text-uppercase text-bold-12 text-primary-500">Procedure/Tindakan :</div>
                                        </b-tr>
                                        <b-tr>
                                            <b-td>
                                                <div v-if="result.transaction_doctor && result.transaction_doctor.length != 0">
                                                    <li v-for="(itd, index) in result.transaction_doctor">
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
                                                <div>
                                                    <div v-if="result.icd9cm && result.icd9cm.length != 0">
                                                        <div class="font-weight-bold">ICD9CM :</div>
                                                        <li v-for="(iicd9, index) in result.icd9cm">
                                                            {{ iicd9.code }} {{ iicd9.name }}
                                                        </li>
                                                    </div>
                                                    <div v-else class="font-weight-bold">ICD9CM : - </div>
                                                </div>
                                                <div class="mt-1"><b>Catatan ICD9CM : </b>{{ result.therapy_plan ? result.therapy_plan : '-' }}</div>
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
                                                <div v-if="result.laboratory_request_new && result.laboratory_request_new.length != 0">
                                                    <div class="font-weight-bold">LABORATORIUM :</div>
                                                    <template v-for="(il, i) in result.laboratory_request_new">
                                                        <div class="mb-12">
                                                            <div class="font-weight-bold">{{il.no_request}} [{{ il.queue }}]</div>
                                                            <li v-for="(list, j) in il.laboratory_request_items">
                                                                {{ list.code }} {{ list.costing_name }} 
                                                                <router-link :to="{ path: '/laboratory/print/'+ list.laboratory_request_id}" target="_blank">
                                                                    <b-button size="sm" variant="outline-secondary" class="ml-2">
                                                                        <feather-icon icon="EyeIcon" /> Lihat Hasil
                                                                    </b-button>
                                                                </router-link>
                                                            </li>
                                                        </div>
                                                    </template>
                                                </div>
                                                <div v-else class="font-weight-bold">LABORATORIUM : - </div>
                                            </b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td>
                                                <div v-if="result.radiology_request_new && result.radiology_request_new.length != 0">
                                                    <div class="font-weight-bold">RADIOLOGI :</div>
                                                    <template v-for="(ir, i) in result.radiology_request_new">
                                                        <div class="mb-12">
                                                            <div class="font-weight-bold">{{ir.no_request}} [{{ ir.queue }}]</div>
                                                            <li v-for="(list, j) in ir.radiology_request_items">
                                                                {{ list.code }} {{ list.costing_name }} 
                                                                <template v-if="list.url.length > 0">
                                                                    <b-button size="sm" variant="outline-secondary" class="ml-2" @click="isRefcontrol(urlbe,list.url)"><feather-icon icon="EyeIcon" /> Lihat Hasil</b-button>
                                                                </template>

                                                            </li>
                                                        </div>
                                                    </template>
                                                </div>
                                                <div v-else class="font-weight-bold">RADIOLOGI : - </div>
                                            </b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td>
                                                <div v-if="result.physiotherapy_request && result.physiotherapy_request.length != 0">
                                                    <div class="font-weight-bold">FISIOTERAPI :</div>
                                                    <template v-for="(phy, i) in result.physiotherapy_request">
                                                        <div class="mb-12">
                                                            <div class="font-weight-bold">{{phy.no_request}}</div>
                                                            <li v-for="(list, j) in phy.radiology_request_items">{{ list.code }} {{ list.costing_name }} </li>
                                                        </div>
                                                    </template>
                                                </div>
                                                <div v-else class="font-weight-bold">RADIOLOGI : - </div>
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
                                                <div v-if="result.prescription_new && result.prescription_new.length != 0">
                                                    <template v-for="(ipre, i) in result.prescription_new">
                                                        <div class="mb-12">
                                                            <div class="font-weight-bold">{{ipre.no_order}}</div>
                                                            <li v-for="(list, j) in ipre.form_prescription" v-if="list.qty > 0 && list.prescription_item_type_id">
                                                                {{ list.pharmacy_catalog_name }}, {{ list.qty }} {{ list.package_name }}, {{ list.used }}, {{ list.desc }} <br>                                                                
                                                            </li>
                                                        </div>
                                                    </template>
                                                </div>
                                                <div v-else>-</div>
                                            </b-td>
                                        </b-tr>
                                    </b-table-simple>
                                </div>
                                <div class="mt-12">
                                    <b-table-simple small class="text-regular-12">
                                        <b-tr>
                                            <div class="text-uppercase text-bold-12 text-primary-500">Advice :</div>
                                        </b-tr>
                                        <b-tr>
                                            <b-td>
                                                <div>{{ result.advice ? result.advice : '-' }}</div>
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
        <b-modal v-model="active_act.modal_booking" scrollable size="lg" title="Booking" hide-footer>
            <div class="card">
                <div class="card-header bg-indigo-200">
                    <span class="text-bold-18">Data Dasar</span>
                </div>
                <div class="card-body">
                    <div class="row" style="font-size: 14px">
                        <div class="col-4">
                            <div class="font-weight-bold">Nama</div>
                            <div>{{ regData.name }} ({{ parsed_age(regData.birth_date) }})</div>
                        </div>
                        <div class="col-4">
                            <div class="font-weight-bold">Tanggal Lahir</div>
                            <div>{{ localDate(regData.birth_date) }}</div>
                        </div>
                        <div class="col-4">
                            <div class="font-weight-bold">No RM</div>
                            <div>{{ regData.norm }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-32">
                <div class="col">
                    <ModernLabel label="Diagnosis (ICD10)" />
                    
                    <table class="table mt-12">
                        <tr>
                            <th>ICD10</th>
                            <th>Aksi</th>
                        </tr>
                        <tr class="item" v-for="(item,index) in form_booking.icd10">
                            <td>
                                <b-form-input style="display:none" v-model="item.value" />
                                <b-form-input type="text" v-model="item.title" aria-readonly="true" />
                            </td>
                            <td>
                                <b-button variant="danger-600" size="sm" @click="delete10(index)" class="mr-2">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </td>
                        </tr>
                    </table>
                    
                    <v-select v-model="icd10_id" @input="onAdd10($event)" aria-readonly="true"  @search="onSearchIcd10" :options="OptionsIcd10" :reduce="OptionsIcd10 => OptionsIcd10" label="title" placeholder="-- Diagnosa --"></v-select>
                </div>
            </div>
            <div class="col">
                <ModernLabel label="Penunjang" class="mt-24" />
                <section class="row mt-12">
                    <div class="col-lg-4">
                        <label class="c-form-label">Tanggal Permintaan</label>
                        <b-form-input v-model="form_booking.date_request" :disabled="active.disabled_booking_date" type="date"></b-form-input>
                        <!-- <b-form-input v-model="form_booking.date_request" :disabled="active.booking_view != 'ADD'" type="date"></b-form-input> -->
                    </div>
                </section>

                <section class="mt-32 d-flex flex-wrap">
                    <b-button pill size="sm" :variant="active_act.tab_penunjang === 'menu1' ? 'primary' : 'outline-primary'"
                        class="m-1 px-3 text-semi-14-on" @click="goToPenunjangTab('menu1')">
                        Rencana Laboratorium
                    </b-button>
                    <b-button pill size="sm" :variant="active_act.tab_penunjang === 'menu2' ? 'primary' : 'outline-primary'"
                        class="m-1 px-3 text-semi-14-on" @click="goToPenunjangTab('menu2')">
                        Rencana Radiologi
                    </b-button>
                    <b-button pill size="sm" :variant="active_act.tab_penunjang === 'menu3' ? 'primary' : 'outline-primary'"
                        class="m-1 px-3 text-semi-14-on" @click="goToPenunjangTab('menu3')">
                        Rencana Fisioterapi
                    </b-button>
                </section>

                <section v-if="active_act.tab_penunjang === 'menu1'" class="py-20 col" :class="active.booking_view != 'ADD' && active.editAccess.laboratory == false ? 'bg-warning-100': ''">
                    <div v-if="active.booking_view != 'ADD' && active.editAccess.laboratory == false" class="card-body bg-warning mb-16">
                        <div class="d-flex align-items-center">
                            <b-icon-exclamation-triangle-fill variant="neutral-800" style="width: 24px; height: 24px;"/>
                            <div class="ml-3 font-italic">Form penunjang laboratorium tidak dapat diubah karena sudah diproses oleh laboratorium</div>
                        </div>
                    </div>
                    <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0">Laboratorium</div>
                    <div class="mt-20">
                        <b-form-input type="text" v-model="searchlab" placeholder="Pencarian"></b-form-input>
                        <div class="col-lg-12 mt-3">
                            <h5>Checked Items:</h5>
                            <b-form-checkbox-group v-model="form_booking.laboratory_request_items">
                                <b-form-checkbox
                                    :disabled="active.booking_view != 'ADD' && active.editAccess.laboratory == false"
                                    v-for="(item, index) in form_booking.laboratory_request_items"
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
                                    <b-form-checkbox-group class="col-lg-12" v-model="form_booking.laboratory_request_items" :disabled="active.booking_view != 'ADD' && active.editAccess.laboratory == false">
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

                <section v-if="active_act.tab_penunjang === 'menu2'" class="py-20 col" :class="active.booking_view != 'ADD' && active.editAccess.radiology == false ? 'bg-warning-100': ''">
                    <div v-if="active.booking_view != 'ADD' && active.editAccess.radiology == false" class="card-body bg-warning mb-16">
                        <div class="d-flex align-items-center">
                            <b-icon-exclamation-triangle-fill variant="neutral-800" style="width: 24px; height: 24px;"/>
                            <div class="ml-3 font-italic">Form penunjang radiologi tidak dapat diubah karena sudah diproses oleh radiologi</div>
                        </div>
                    </div>
                    <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0">Radiologi</div>
                    <div class="mt-20">
                        <b-form-input type="text" v-model="searchrad" placeholder="Pencarian"></b-form-input>
                        <div class="col-lg-12 mt-3">
                            <h5>Checked Items:</h5>
                            <b-form-checkbox-group v-model="form_booking.radiology_request_items" :disabled="active.booking_view != 'ADD' && active.editAccess.radiology == false">
                                <b-form-checkbox
                                    v-for="(item, index) in form_booking.radiology_request_items"
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
                                    <b-form-checkbox-group class="col-lg-12" v-model="form_booking.radiology_request_items" :disabled="active.booking_view != 'ADD' && active.editAccess.radiology == false">
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

                <section v-if="active_act.tab_penunjang === 'menu3'" class="py-20 col">
                    <b-form-input type="text" v-model="searchphy" placeholder="Pencarian"></b-form-input>
                    <div class="col-lg-12 mt-3">
                        <h5>Checked Items:</h5>
                        <b-form-checkbox-group v-model="form_booking.physiotherapy_request_items">
                            <b-form-checkbox
                            v-for="(item, index) in form_booking.physiotherapy_request_items"
                            :key="index"
                            :value="item"
                            >
                            {{ item.costing_name }}
                            </b-form-checkbox>
                        </b-form-checkbox-group>
                    </div>
                    <div class="mt-12 py-2 bg-neutral-100" style="height: 400px; overflow-y: scroll;">
                        <b-form-checkbox-group class="col-lg-12" v-model="form_booking.physiotherapy_request_items">
                            <b-form-checkbox v-for="(ip, index) in filteredListPhy" v-bind:key="index" :value="{'service_class_id':'9b702245-79c5-4beb-8218-53afb08647aa','physiotherapy_package_id':ip.id, 'costing_name': ip.costing_name}" class="col-lg-5">{{ip.costing_name }}</b-form-checkbox>
                        </b-form-checkbox-group>
                    </div>
                </section>
            </div>

            <div v-if="active.editAccess.laboratory == true || active.editAccess.radiology == true" class="card-footer mt-32">
                <div class="d-flex justify-content-end">
                    <b-button v-if="active.booking_view == 'ADD'" :disabled="isDisable" @click="resetBooking()" variant="outline-primary">Reset</b-button>
                    <b-button :disabled="isDisable" @click="validationBooking()" variant="primary" class="ml-2">Simpan</b-button>
                </div>
            </div>
        </b-modal>

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

        <b-modal v-if="isSPRI" v-model="active_act.modal_SPRI" size="xl" title="SPRI" hide-footer no-close-on-backdrop>
            <section class="card">
                <div class="card-header bg-indigo-200">
                    <span class="text-bold-18">Data Dasar</span>
                </div>
                <div class="card-body">
                    <div class="row" style="font-size: 14px">
                        <div class="col-4">
                            <div class="font-weight-bold">Nama</div>
                            <div>{{ regData.name }} ({{ parsed_age(regData.birth_date) }})</div>
                        </div>
                        <div class="col-4">
                            <div class="font-weight-bold">Tanggal Lahir</div>
                            <div>{{ localDate(regData.birth_date) }}</div>
                        </div>
                        <div class="col-4">
                            <div class="font-weight-bold">No RM</div>
                            <div>{{ regData.norm }}</div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="mt-20">
                <div class="card">
                    <div class="card-header text-bold-18 bg-indigo-200">Permintaan Rawat Inap Pasien</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 d-flex align-items-center">
                                <label class="text-semi-14">Diagnosis</label>
                            </div>
                            <div class="col-lg">
                                <b-table-simple bordered responsive style="font-size: 14px">
                                    <b-tr>
                                        <b-th style="width: 60px;">No</b-th>
                                        <b-th>ICD10</b-th>
                                    </b-tr>
                                    <template v-for="(item,index) in form_spri.icd10">
                                        <b-tr class="item">
                                            <b-td>{{ index+1 }}</b-td>
                                            <b-td>{{ item.title }}</b-td>
                                        </b-tr>
                                    </template>
                                </b-table-simple>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex align-items-center">
                                <label class="text-semi-14">Indikasi Rawat Inap</label>
                            </div>
                            <div class="col-lg">
                                <b-textarea v-model="form_spri.indication" autocomplete="off"></b-textarea>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex align-items-center">
                                <label class="text-semi-14">Rencana Tindakan</label>
                            </div>
                            <div class="col-lg">
                                <b-textarea v-model="form_spri.plan" autocomplete="off"></b-textarea>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex align-items-center">
                                <label class="text-semi-14">Hasil yang Diharapkan</label>
                            </div>
                            <div class="col-lg">
                                <b-textarea v-model="form_spri.result" autocomplete="off"></b-textarea>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex align-items-center">
                                <label class="text-semi-14">Keadaan Umum Pasien</label>
                            </div>
                            <div class="col-lg">
                                <b-textarea v-model="form_spri.condition" autocomplete="off"></b-textarea>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex align-items-center">
                                <label class="text-semi-14">DPJP Utama</label>
                            </div>
                            <div class="col-lg">
                                <v-select v-model="form_spri.doctor_in_charge_id" :options="OptionsSpecialistDoctor" :reduce="OptionsSpecialistDoctor => OptionsSpecialistDoctor.value" label="title" placeholder="-- Dokter --"></v-select>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex align-items-center">
                                <label class="text-semi-14">Ruang Perawatan</label>
                            </div>
                            <div class="col-lg">
                                <v-select v-model="form_spri.type_room_id" :options="TypeRoomOptions" :reduce="(TypeRoomOptions) => TypeRoomOptions.value" label="title" placeholder="-- Pilih Tipe Ruangan --"></v-select>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <div class="modal-footer mt-32">
                <div class="d-flex justify-content-end">
                    <b-button :disabled="isDisabledSPRI" @click="resetSPRI" variant="outline-primary" class="mr-2">Reset</b-button>
                    <b-button :disabled="isDisabledSPRI" @click="validationSPRI" variant="primary">Simpan</b-button>
                    <!-- <b-button @click="openPrintSPRI(regData.registration_destination_id)" variant="primary" class="mr-2">
                        <b-icon icon="printer" /> <span>Print</span>
                    </b-button> -->
                </div>
            </div>
        </b-modal>        


        <!-- =============================================== PENUNAJANG LAINNYA =============================================== -->
         
        <b-modal v-model="active.modal_examination" size="lg" title="Penunjang Lainnya" hide-footer @close="openModalExamination" no-close-on-backdrop>
            <div>
                <div class="row">
                    <div class="col-lg-3">
                        <label class="c-form-label">Pemeriksaan <span class="text-danger">*</span></label>
                        <v-select v-model="form_examination.category_examination_id" :options="OptionsExamination" :reduce="OptionsExamination => OptionsExamination.value" label="title" placeholder="-- Pilih --" aria-readonly="true"></v-select>
                    </div>
                    <div class="col-lg-3 mt-12 mt-lg-0">
                        <label class="c-form-label">Berkas <span class="text-danger">*</span></label>
                        <b-form-file v-model="form_examination.file" accept="image/*" />
                    </div>
                    <div class="col-lg-3 mt-12 mt-lg-0">
                        <label class="c-form-label">Tanggal <span class="text-danger">*</span></label>
                        <b-form-input v-model="form_examination.examination_date" type="date"  autocomplete="off" />
                    </div>
                </div>
                <div class="d-flex mt-20">
                    <b-button @click="validationExamination" variant="primary">Tambah</b-button>
                </div>
            </div>

            <div class="mt-32">
                <b-table-simple bordered responsive style="font-size: 13px">
                    <b-thead>
                        <b-tr>
                            <b-th style="width: 60px;" class="text-center">No</b-th>
                            <b-th>Pemerikasaan</b-th>
                            <b-th>Tanggal</b-th>
                        </b-tr>
                    </b-thead>
                    <b-tbody>
                        <template v-for="(item, index) in examination_list">
                            <b-tr>
                                <b-td :rowspan="2" class="text-center">{{index+1}}</b-td>
                                <b-td>{{ item.category_examination_name }}</b-td>
                                <b-td style="width: 200px;">{{ item.examination_date }} </b-td>
                            </b-tr>
                            <b-tr>
                                <b-td colspan="2">
                                    <b-table-simple>
                                        <b-tr>
                                            <b-th>Gambar</b-th>
                                            <b-th style="width: 188px;">Aksi</b-th>
                                        </b-tr>
                                        <template v-for="(list, imgIndex) in item.data.files">
                                            <b-tr>
                                                <b-td>
                                                    <b-img rounded :src="urlbe+list.url" style="width: 300px;" />
                                                </b-td>
                                                <b-td>
                                                    <div class="d-flex">
                                                        <b-button @click="removeExamination({ file_code: list.file_code, examination_id: item.id })" v-b-popover.hover.top="'Hapus'" size="sm" variant="danger-700" class="mr-2">
                                                            <feather-icon icon="TrashIcon" />
                                                        </b-button>
                                                        <b-button @click="expandImage(list.url)" v-b-popover.hover.top="'Expand Gambar'" size="sm" variant="primary">
                                                            <feather-icon icon="ExternalLinkIcon" />
                                                        </b-button>
                                                    </div>
                                                </b-td>
                                            </b-tr>
                                        </template>
                                    </b-table-simple>
                                </b-td>
                            </b-tr>
                        </template>
                    </b-tbody>
                </b-table-simple>
            </div>
        </b-modal>

        <!-- =============================================== CHECKOUT =============================================== -->
        <b-modal v-model="active.modal_checkout" size="md" title="Checkout & Jenis Kunjungan" no-close-on-backdrop>
            <div>
                <label class="c-form-label">Kriteria Pasien <span class="text-danger">*</span></label>
                <v-select v-model="form_checkout.type_emergency_id" aria-readonly="true" :options="OptionsTypeEmergency" :reduce="OptionsTypeEmergency => OptionsTypeEmergency.value" label="title" placeholder="-- Pilih --"></v-select>
                <label class="c-form-label">Jenis Kunjungan <span class="text-danger">*</span></label>
                <v-select v-model="form_checkout.type_visit_id" aria-readonly="true" :options="OptionsTypeVisit" :reduce="OptionsTypeVisit => OptionsTypeVisit.value" label="title" placeholder="-- Pilih --"></v-select>
                <!-- <template v-if="(isIGD || isRajal) && regData.is_checkout == 0"> -->
                    <label class="c-form-label">Checkout <span class="text-danger">*</span></label>
                    <v-select v-model="form_checkout.exit_id" aria-readonly="true" :options="OptionsExit" :reduce="OptionsExit => OptionsExit.value" label="title" placeholder="-- Pilih --"></v-select>
                <!-- </template> -->
                <template v-if="(isRajal) && regData.is_checkout == 0">
                    <label class="c-form-label">Tanggal Kontrol <span class="text-danger">*</span></label>
                    <b-form-input v-model="form_checkout.date_control" type="date" />
                </template>
            </div>

            <template #modal-footer>
                <div class="d-flex justify-content-end">
                    <b-button @click="validationExit" :disabled="active.disabled_checkout" variant="primary">Checkout</b-button>
                </div>
            </template>
        </b-modal>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import router from '@/router/index.js'

import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import vSelect from 'vue-select'
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
import moment from 'moment'
// import io from 'socket.io-client';

import patientMenu from '@/@core/components/tab-menu/patientMenu.vue';
import administration from '@/views/layouts/patient/doctor/administration.vue';

import OutpatientStudies from '@/views/layouts/patient/doctor/OutpatientStudies.vue';
import EmergencyStudies from '@/views/layouts/patient/doctor/EmergencyStudies.vue';
import InpatientStudies from '@/views/layouts/patient/doctor/InpatientStudies.vue';

import LineChart from './LineChart.vue';
import LineChartServiks from './LineChartServiks.vue';
import LineChartKetuban from './LineChartKetuban.vue';
import LineChartKontraksi from './LineChartKontraksi.vue';
import LineChartOksitosin from './LineChartOksitosin.vue';
import LineChartObatCairan from './LineChartObatCairan.vue';
import LineChartTensiNadi from './LineChartTensiNadi.vue';
import LineChartSuhuBadan from './LineChartSuhuBadan.vue';
import LineChartUrin from './LineChartUrin.vue';
import physiotherapy from '@/router/routes/physiotherapy';

export default {
    name: 'DoctorStudies',
    components: {
        patientMenu, 
        administration, 
        ModernLabel, 
        vSelect, 
        DatePicker, 
        LineChart, 
        LineChartKetuban, 
        LineChartServiks, 
        LineChartKontraksi, 
        LineChartOksitosin,
        LineChartObatCairan,
        LineChartTensiNadi,
        LineChartSuhuBadan,
        LineChartUrin,

        OutpatientStudies, 
        EmergencyStudies,
        InpatientStudies,
    },
    data() {
        return {
            // socket : io(process.env.VUE_APP_SOCKET_IO, { transports: ["websocket", "polling", "flashsocket"],withCredentials: true }),
            userData: JSON.parse(localStorage.getItem('userData')),
            isDisabledSPRI: false,
            showTimePanel: false,
            showTimeRangePanel: false,
            partograf:{
                datetime_start:'',
                doctor_id:'',
                midwife_id:'',
                water_breaks_since:'',
                stomachache_since:'',
                gravida:'',
                para:'',
                abortus:''
            },
            generalist: {
                head: '',
                head_hair_color: '',
                head_hair_condition: '',
                eye_conjunctive: '',
                eye_sclera: '',
                eye_pupil: '',
                eye_light_reflex: '',
                mouth_mucosa: '',
                mouth_discoloration: '',
                mouth_tongue: '',
                mouth_caries: '',
                neck_kgb: '',
                neck_trachea_position: '',
                neck_massa: '',
                thorax_insp_chest: '',
                thorax_insp_retraction: '',
                thorax_palp_chest: '',
                thorax_palp_fremitus: '',
                thorax_palp_crepitation: '',
                thorax_ausk_pul_vasiculer: '',
                thorax_ausk_pul_rhonki: '',
                thorax_ausk_pul_wheezing: '',
                thorax_ausk_cor_heart_sound: '',
                thorax_ausk_cor_murmur: '',
                thorax_ausk_cor_gallop: '',
                abdomen_supel: '',
                abdomen_bowel_sounds: '',
                abdomen_tenderness: '',
                abdomen_hepar_lien: '',
                abdomen_other_physical_check: '',
                extremity_acral: '',
                extremity_crt: '',
                extremity_edema: '',
                genitalia: '',
                localist_status: '',
                ophthal_status: '',

                ophthal_od: {
                    visus: '',
                    tio_digital: '',
                    tio_tonometri_sciots: '',
                    eye_position: '',
                    eye_movement: '',
                    super_silia: '',
                    palpebra_edema: '',
                    palpebra_ptosis: '',
                    palpebra_lagophtalmus: '',
                    palpebra_tumor: '',
                    apparatus_lakrimalis: '',
                    silia: '',
                    conjunctive_hiperemis: '',
                    conjunctive_bleed: '',
                    cornea: '',
                    camera_oculi: '',
                    iris_color: '',
                    iris_cripte: '',
                    pupil_form: '',
                    pupil_diameter: '',
                    pupil_direct_light: '',
                    pupil_indirect_light: '',
                    lens: '',
                    shadow_test: '',
                    glandula: '',
                    field_view: '',
                    fundus: '',
                },
                ophthal_os: {
                    visus: '',
                    tio_digital: '',
                    tio_tonometri_sciots: '',
                    eye_position: '',
                    eye_movement: '',
                    super_silia: '',
                    palpebra_edema: '',
                    palpebra_ptosis: '',
                    palpebra_lagophtalmus: '',
                    palpebra_tumor: '',
                    apparatus_lakrimalis: '',
                    silia: '',
                    conjunctive_hiperemis: '',
                    conjunctive_bleed: '',
                    cornea: '',
                    camera_oculi: '',
                    iris_color: '',
                    iris_cripte: '',
                    pupil_form: '',
                    pupil_diameter: '',
                    pupil_direct_light: '',
                    pupil_indirect_light: '',
                    lens: '',
                    shadow_test: '',
                    glandula: '',
                    field_view: '',
                    fundus: '',
                },

            },

            supportPackage: {
                lab_package_list: [],
                rad_package_list: [],                
            },
            
            reg_des_id: '',
            form_spri: {
                id:'',
                soap_id:'',
                doctor_in_charge_id:'',
                condition:'',
                indication:'',
                plan:'',
                result:'',
                type_room_id:'',
                icd10:[],
            },
            isPartograf: false,
            isNewBorn: false,
            isSPRI: true,
            isIGD: false,
            isRanap: false,
            isRajal: false,
            radiologyResultField: [
                { key: 'no', label: 'No', thStyle:'width: 50px' },
                { key: 'expertise', label: 'Expertise', thStyle:'width: 500px' },
                { key: 'url', label: 'Gambar' },
                { key: 'action', label: 'Aksi' },
            ],
            TypeRoomOptions:[
                { value: 11, title: 'INFANT' },
                { value: 12, title: 'HCU' },
                { value: 13, title: 'ICU' },
                { value: 14, title: 'PICU' },
                { value: 15, title: 'ICU ISOLASI' },
                { value: 16, title: 'ICCU' },
                { value: 17, title: 'NICU' },
                { value: 18, title: 'PERINATOLOGI' },
                { value: 19, title: 'RUANG PERAWATAN' },
                { value: 20, title: 'BEDAH' },
                { value: 21, title: 'NON BEDAH' },
                { value: 22, title: 'ISOLASI TEKANAN (-)' },
                { value: 23, title: 'ISOLAS TEKANAN (+)' },
                { value: 24, title: 'KEBIDANAN' },
                { value: 25, title: 'ANAK' },
            ],
            // dataFromEdit:{},
            imageview:'',
            active: {
                refcontrol: false,
                booking_view: 'ADD',
                editAccess: {
                    laboratory: true,
                    radiology: true,
                    // physiotherapy: true,
                },
                modal_examination: false,
                modal_checkout: false,
                disabled_checkout: false,
                disabled_booking_date: false,
            },
            urlbe:process.env.VUE_APP_IMAGE,
            active_act: {
                modal_booking: false,
                modal_SPRI: false,
                modal_NewBorn: false,
                modal_Partograf: true,
                modal_IGD: false,
                tab_penunjang: 'menu1',
                modal_procedure: false,
                modal_lab_igd: false,
                modal_rad_igd: false,
                modal_presc_igd: false,
            },
            paramsInsuranceServiceClassDoctor:{},
            regData: {},
            searchlab: '',
            searchrad: '',
            searchphy: '',
            form_booking: {
                id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                icd10: [],
                date_request: moment(new Date()).format('YYYY-MM-DD'),
                laboratory_request_items: [],
                radiology_request_items: [],
                physiotherapy_request_items: [],
            },
            OptionsIcd10:[],
            icd10_id: '',
            isDisable: false,
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

            // Examination
            form_examination: {
                id: '',
                file: null,
                examination_date: moment(new Date()).format('YYYY-MM-DD'),
                category_examination_id: '',
                soap_id: '',
            },
            examination_list: [],

            otherSupportFields: [
                { key: 'no', label: 'No', thStyle: 'width: 50px' },
                { key: 'category_examination_name', label: 'Pemeriksaan' },
                { key: 'file', label: 'Preview', thStyle: 'width: 400px' },
                { key: 'examination_date', label: 'Tanggal' },
                { key: 'action', label: 'Aksi' },
            ], 
            
            // Checkout
            form_checkout: {
                type_emergency_id: '',
                type_visit_id: '',
                exit_id: '',
                date_control: '',
            },
            OptionsTypeVisit:[
                {title:"Kunjungan Awal", value:1},
                {title:"Kontrol Lanjutan", value:2},
                {title:"Observasi", value:3},
                {title:"Post OP", value:4},
                {title:"Post Rawat Inap", value:5},
            ],
            OptionsExit:[
                { value: '807ca005-5827-4e19-9c97-0bc3d4e924ca', title: 'Atas Persetujuan Dokter' },
                { value: '4a6d28c4-e020-425f-91e1-d39699cf679b', title: 'Meninggal' },
                { value: 'a1631acc-6a94-414d-95de-a9faea0f1c2f', title: 'Dirujuk' },
                { value: 'ae0aabfa-1600-443f-8a60-62f9e76b1608', title: 'Permintaan Sendiri' },
                { value: 'dfe2b9c6-5934-4748-aad9-6d19c957ecfd', title: 'Kabur' },
                { value: '03e783b8-92ef-40c2-a867-d5895ffbfc83', title: 'Poliklinik' },
                { value: 'f359a4b5-4406-49d7-9a81-49bf34b4c21a', title: 'Perbaikan' },
                { value: '2542a5f7-570a-449c-9eee-525569c0ce1e', title: 'Rawat Inap' },
            ],

            // Ranap
            form_record: {
                partograph_id: "",
                date_birth: "",
                birthing_place_type_id: 0,
                birthing_place_other: "",
                birthing_place_address: "",
                note: "",
                referral_reason: "",
                referral_place: "",
                referral_companion_type_id: 0,
                problem_type_id: 0,
                newborn_baby_weight: "",
                newborn_baby_height: "",
                newborn_baby_sex_type_id: 0,
                newborn_baby_assessment_type_id: 0,
                newborn_baby_normal_arry: "",
                newborn_baby_asphyxia_arry: "",
                newborn_baby_disabled: "",
                // newborn_baby_hypothermia_type_id: 0,
                // newborn_baby_hypothermia_a: "",
                // newborn_baby_hypothermia_b: "",
                newborn_baby_hypothermia_c: "",
                newborn_baby_breast_milk_type_id: 0,
                newborn_baby_breast_milk_hours: "",
                newborn_baby_breast_milk_not: "",
                newborn_baby_problem_management: "",
                newborn_baby_result: "",

                newborn_baby_type_id: 0,
                newborn_baby_hypothermia_arry: [],
                newborn_baby_asphyxia: '',
            },
            

            form_kala: {
                birth_record_id: "",
                kala_one_latent_phase: "",
                kala_one_intervention_type_id: 0,
                kala_one_alert_line_type_id: 0,
                kala_one_active_phase: "",
                kala_one_problem_management: "",
                kala_one_result: "",
                kala_two_episiotomy_type_id: 0,
                kala_two_episiotomy: "",
                kala_two_companion_type_id: 0,
                kala_two_fetal_distress_type_id: 0,
                // kala_two_fetal_distress_a: "",
                // kala_two_fetal_distress_b: "",
                kala_two_monitoring_result: "",
                kala_two_shoulder_dystocia_type_id: 0,
                kala_two_shoulder_dystocia: "",
                kala_two_problem_management: "",
                kala_three_initiation_type_id: 0,
                kala_three_initiation: "",
                kala_three_long: '',
                kala_three_oxytocin_type_id: 0,
                kala_three_oxytocin: 0,
                kala_three_oxytocin_reason: "",
                kala_three_oxytocin_second_type_id: 0,
                kala_three_oxytocin_second: "",
                kala_three_umbilical_cord_type_id: 0,
                kala_three_umbilical_cord: "",
                kala_three_uterine_fundus_type_id: 0,
                kala_three_uterine_fundus: "",
                kala_three_placenta_type_id: 0,
                // kala_three_placenta_a: "",
                // kala_three_placenta_b: "",
                kala_three_placenta_not_type_id: 0,
                kala_three_placenta_not: "",
                kala_three_laceration_type_id: 0,
                kala_three_laceration: "",
                kala_three_uterine_atony_type_id: 0,
                kala_three_uterine_atony: "",
                kala_three_amount_blood: '',
                kala_three_problem_management: "",
                kala_three_result: "",
                kala_four_mother_ku: '',
                kala_four_mother_td: '',
                kala_four_pulse: '',
                kala_four_breath: '',
                kala_four_problem_management: "",
                kala_four_result: "",

                kala_two_fetal_distress_arry: [],
                kala_three_placenta_arry: [],
                kala_three_parineum_degree_type_id: 0,
                kala_three_parineum_degree: '',
                kala_three_parineum_act_type_id: 0,
            },
            kala: {
                fields: [
                    { key: 'no', label: 'No', thClass: 'text-center', thStyle: 'width:60px' },
                    { key: 'shift', label: 'Jam ke' },    
                    { key: 'time', label: 'Waktu' },    
                    { key: 'blood_pressure', label: 'Tekanan darah' },    
                    { key: 'pulse', label: 'Nadi' },    
                    { key: 'temperature', label: 'Temp (C)' },    
                    { key: 'fundus', label: 'Tinggi Fundus Uteri' },    
                    { key: 'contraction', label: 'Kontraksi Uterus' },    
                    { key: 'bladder', label: 'Kandung Kemih ≤ Urine' },    
                    { key: 'out_blood', label: '≤ Darah Keluar' },    
                    { key: 'action', label: 'Aksi', thStyle: 'width:200px' },
                ],
                items: [],
            },
            input_kala: {
                shift: '',
                time: '',
                blood_pressure: '',
                pulse: '',
                temperature: '',
                fundus: '',
                contraction: '',
                bladder: '',
                out_blood: '',
            },
            input_fetal: '',
            input_hipotemi: '',
        }
    },
    filters:{
        currency(value) {
            return new Intl.NumberFormat("en-US", { style: "currency", currency: "IDR" }).format(value)
        },
    },
    computed: {
        ...mapState({ 
            //SPRI
            // TypeRoomOptions: state => state.helper.options.type_room,
            OptionsTypeEmergency: state => state.helper.options.typeEmergency,
            itemsAllergy: state => state.patient.allergy.items, 

            items: state => state.soap.items, 
            OptionsSpecialistDoctor: state => state.employee.options.specialist,         
            OptionsExamination: state => state.soap.options.examination_category,
            itemsPhysiotherapy: state => state.physiotherapy.package.items,
                        
            // generalis option
            OptionsHeadCircumference: state => state.soap.generalis.OptionsHeadCircumference,
            OptionsHeadHairColor: state => state.soap.generalis.OptionsHeadHairColor,
            OptionsHeadHairCondition: state => state.soap.generalis.OptionsHeadHairCondition,
            OptionsEyeConjunctiva: state => state.soap.generalis.OptionsEyeConjunctiva,
            OptionsEyeSclera: state => state.soap.generalis.OptionsEyeSclera,
            OptionsEyePupil: state => state.soap.generalis.OptionsEyePupil,
            OptionsEyeReflect: state => state.soap.generalis.OptionsEyeReflect,
            OptionsEyeReflect: state => state.soap.generalis.OptionsEyeReflect,
            OptionsMouthMukosa: state => state.soap.generalis.OptionsMouthMukosa,
            OptionsMouthHiperemis: state => state.soap.generalis.OptionsMouthHiperemis,
            OptionsMouthTongue: state => state.soap.generalis.OptionsMouthTongue,
            OptionsMouthKaries: state => state.soap.generalis.OptionsMouthKaries,
            OptionsNeckKGB: state => state.soap.generalis.OptionsNeckKGB,
            OptionsNeckTrakea: state => state.soap.generalis.OptionsNeckTrakea,
            OptionsNeckMassa: state => state.soap.generalis.OptionsNeckMassa,
            OptionsInspectionChest: state => state.soap.generalis.OptionsInspectionChest,
            OptionsInspectionRetraction: state => state.soap.generalis.OptionsInspectionRetraction,
            OptionsPalpationChest: state => state.soap.generalis.OptionsPalpationChest,
            OptionsPalpationFremitus: state => state.soap.generalis.OptionsPalpationFremitus,
            OptionsPalpationKrepitasi: state => state.soap.generalis.OptionsPalpationKrepitasi,
            OptionsAuskultationPulmoVasikuler: state => state.soap.generalis.OptionsAuskultationPulmoVasikuler,
            OptionsAuskultationPulmoRhonki: state => state.soap.generalis.OptionsAuskultationPulmoRhonki,
            OptionsAuskultationPulmoWheezing: state => state.soap.generalis.OptionsAuskultationPulmoWheezing,
            OptionsAuskultationCORBJ: state => state.soap.generalis.OptionsAuskultationCORBJ,
            OptionsAuskultationCORMurmur: state => state.soap.generalis.OptionsAuskultationCORMurmur,
            OptionsAuskultationCORGallop: state => state.soap.generalis.OptionsAuskultationCORGallop,
            OptionsAbdomenSupel: state => state.soap.generalis.OptionsAbdomenSupel,
            OptionsAbdomenBising: state => state.soap.generalis.OptionsAbdomenBising,
            OptionsAbdomenPress: state => state.soap.generalis.OptionsAbdomenPress,
            OptionsAbdomenHepar: state => state.soap.generalis.OptionsAbdomenHepar,
            OptionsEkstremitasAkral: state => state.soap.generalis.OptionsEkstremitasAkral,
            OptionsEkstremitasCRT: state => state.soap.generalis.OptionsEkstremitasCRT,
            OptionsEkstremitasEdema: state => state.soap.generalis.OptionsEkstremitasEdema,
            OptionsOftalVisus: state => state.soap.generalis.OptionsOftalVisus,
            OptionsOftalTIO: state => state.soap.generalis.OptionsOftalTIO,
            OptionsOftalEyePosition: state => state.soap.generalis.OptionsOftalEyePosition,
            OptionsOftalMovement: state => state.soap.generalis.OptionsOftalMovement,
            OptionsOftalSuperSilia: state => state.soap.generalis.OptionsOftalSuperSilia,
            OptionsOftalPalpebraEdema: state => state.soap.generalis.OptionsOftalPalpebraEdema,
            OptionsOftalPalpebraPtosis: state => state.soap.generalis.OptionsOftalPalpebraPtosis,
            OptionsOftalPalpebraLagophtalmus: state => state.soap.generalis.OptionsOftalPalpebraLagophtalmus,
            OptionsOftalPalpebraTumor: state => state.soap.generalis.OptionsOftalPalpebraTumor,
            OptionsOftalApparetus: state => state.soap.generalis.OptionsOftalApparetus,
            OptionsOftalSilia: state => state.soap.generalis.OptionsOftalSilia,
            OptionsOftalConjHiperemis: state => state.soap.generalis.OptionsOftalConjHiperemis,
            OptionsOftalConjBleed: state => state.soap.generalis.OptionsOftalConjBleed,
            OptionsOftalKornea: state => state.soap.generalis.OptionsOftalKornea,
            OptionsOftalAnterior: state => state.soap.generalis.OptionsOftalAnterior,
            OptionsOftalIrisColor: state => state.soap.generalis.OptionsOftalIrisColor,
            OptionsOftalIrisKripte: state => state.soap.generalis.OptionsOftalIrisKripte,
            OptionsOftalPupilForm: state => state.soap.generalis.OptionsOftalPupilForm,
            OptionsOftalPupilDiameter: state => state.soap.generalis.OptionsOftalPupilDiameter,
            OptionsOftalPupilDirectLight: state => state.soap.generalis.OptionsOftalPupilDirectLight,
            OptionsOftalPupilIndirectLight: state => state.soap.generalis.OptionsOftalPupilIndirectLight,
            OptionsOftalLens: state => state.soap.generalis.OptionsOftalLens,
            OptionsOftalGlandula: state => state.soap.generalis.OptionsOftalGlandula,
            OptionsOftalLapang: state => state.soap.generalis.OptionsOftalLapang,
            OptionsOftalShadow: state => state.soap.generalis.OptionsOftalShadow,

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
            return this.supportPackage?.rad_package_list?.map((rad) => {
                const filteredCategoryItems = rad?.category_items?.filter((item) =>
                    item.costing_name.toLowerCase().includes(this.searchrad?.toLowerCase())
                );

                return {
                    ...rad,
                    category_items: filteredCategoryItems,
                };
            }).filter(rad => rad?.category_items?.length > 0); 
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
        
        "form_record.birthing_place_type_id"(newValue) {
            if (newValue != "6") {
                this.form_record.birthing_place_other = ''
            }
        },
        
        "form_kala.kala_two_episiotomy_type_id"(newValue) {
            if (newValue != "1") {
                this.form_kala.kala_two_episiotomy = ''
            }
        },

        "form_kala.kala_two_shoulder_dystocia_type_id"(newValue) {
            if (newValue != "1") {
                this.form_kala.kala_two_shoulder_dystocia = ''
            }
        },

        "form_kala.kala_two_fetal_distress_type_id"(newValue) {
            if (newValue != "1") {
                this.form_kala.kala_two_monitoring_result = ''
            } else if (newValue != 0) {
                this.form_kala.kala_two_fetal_distress_arry = []
            }
        },

        "form_kala.kala_three_initiation_type_id"(newValue) {
            if (newValue != "1") {
                this.form_kala.kala_three_initiation = ''
            }
        },

        "form_kala.kala_three_oxytocin_type_id"(newValue) {
            if (newValue != "0") {
                this.form_kala.kala_three_oxytocin = ''
            } else {
                this.form_kala.kala_three_oxytocin_reason = ''
            }
        },

        "form_kala.kala_three_oxytocin_second_type_id"(newValue) {
            if (newValue != "0") {
                this.form_kala.kala_three_oxytocin_second = ''
            }
        },

        "form_kala.kala_three_umbilical_cord_type_id"(newValue) {
            if (newValue != "0") {
                this.form_kala.kala_three_umbilical_cord = ''
            }
        },

        "form_kala.kala_three_uterine_fundus_type_id"(newValue) {
            if (newValue != "0") {
                this.form_kala.kala_three_uterine_fundus = ''
            }
        },

        "form_kala.kala_three_placenta_not_type_id"(newValue) {
            if (newValue != "1") {
                this.form_kala.kala_three_placenta_not = ''
            }
        },

        "form_kala.kala_three_laceration_type_id"(newValue) {
            if (newValue != "1") {
                this.form_kala.kala_three_laceration = ''
            }
        },

        "form_kala.kala_three_uterine_atony_type_id"(newValue) {
            if (newValue != "1") {
                this.form_kala.kala_three_uterine_atony = ''
            }
        },

        "form_record.newborn_baby_type_id"(newValue) {
            if (newValue != "1") {
                this.form_record.newborn_baby_asphyxia_arry = []
                this.form_record.newborn_baby_asphyxia = ''
                this.form_record.newborn_baby_disabled = ""
                this.form_record.newborn_baby_hypothermia_arry = []
            } else if (condition) {
                this.form_record.newborn_baby_normal_arry = []
                this.form_record.newborn_baby_disabled = ""
                this.form_record.newborn_baby_hypothermia_arry = []
            } else if (condition) {
                this.form_record.newborn_baby_normal_arry = []
                this.form_record.newborn_baby_asphyxia = ''
                this.form_record.newborn_baby_asphyxia_arry = []
                this.form_record.newborn_baby_hypothermia_arry = []
            } else if (condition) {
                this.form_record.newborn_baby_normal_arry = []
                this.form_record.newborn_baby_asphyxia = ''
                this.form_record.newborn_baby_asphyxia_arry = []
                this.form_record.newborn_baby_disabled = ""
            }
        },
        "form_record.newborn_baby_asphyxia_arry"(newValues) {
            if (newValues && !newValues.includes("g")) {
                this.form_record.newborn_baby_asphyxia = ""
            }
        },
    },
    methods: {    
        isSimilar(reg_des_id) {
            if(this.regData.registration_destination_id == reg_des_id) {
                return 'border: 2px solid #03a972;'
            }
            return ''
        },
        cancelRadiologyRequest(obj){
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin membatalkan booking radiologi ${obj.no_request} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('radiology/removeRequest', obj.id)
                    .then(
                        response => {
                            if (response.status === 200 || response.status === 201) {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.$store.dispatch('soap/getByRM', this.regData.norm)
                            } else {
                                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            }
                        },
                        error => {
                            this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        }
                    ) 
                }
            })
        },
        cancelLaboratoryRequest(obj){
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus ${obj.no_request} ?`, {
                title: 'Please Confirm', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true) {
                    this.$store.dispatch('laboratory/removeRequest', obj.id)
                    .then(
                        response => {
                            if (response.status === 200 || response.status === 201) {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.$store.dispatch('soap/getByRM', this.regData.norm)

                            } else {
                                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            }
                        },
                        error => {
                            this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        }
                    ) 
                }
            })
        },
        triggerOutpatientModal(soap) {
            const message = soap
            this.$refs.childOutRef.parentTrigger(message);
        },
        triggerInpatientModal(soap) {
            const message = soap
            this.$refs.childInRef.parentTrigger(message);
        },
        getOutpatientCondition(doctor_in_charge_id, health_facility_id, is_resume_exit, is_booking) {
            // if (health_facility_id == '3cb626f5-9148-4fb1-8663-3d7f028b01a6' && is_resume_exit != 1 && is_booking != 1) {
            if ((doctor_in_charge_id == this.userData.employee_id || this.userData.employee_id == 'd77142c4-4777-47d8-bd0c-fd7dfd134af4') && health_facility_id == '3cb626f5-9148-4fb1-8663-3d7f028b01a6' && is_resume_exit != 1 && is_booking != 1) {
                return true                
            }
            return false
        },
        getInpatientCondition(doctor_in_charge_id, health_facility_id, is_initial_inpatient) {
            // if (health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f' && is_initial_inpatient == 1) {
            if ((doctor_in_charge_id == this.userData.employee_id || this.userData.employee_id == 'd77142c4-4777-47d8-bd0c-fd7dfd134af4') && health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f' && is_initial_inpatient == 1) {
                return true                
            }
            return false
        },
        validationExit() {
            let params = { checkout: { exit_id: this.form_checkout.exit_id, type_visit_id: this.form_checkout.type_visit_id, type_emergency_id: this.form_checkout.type_emergency_id, date_control: this.form_checkout.date_control }, registration_destination_id: this.reg_des_id }

            try {
                this.active.disabled_checkout = true
                this.$store.dispatch('registration/checkoutV2', params).then(response => {
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.getData(this.reg_des_id)
                    this.active.modal_checkout = false
                    this.active.disabled_checkout = false
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                this.active.disabled_checkout = false
                console.error("Error at: ", error)
            }
        },
        icare(data) {
            this.$store.dispatch('patient/getIcare', {number_insurance:data,bride_bpjs:this.regData.bride_bpjs})
            .then(res => {
                window.open(res.response.url, '_blank');
            },
            err => {
            })
        },
        
        openModalExamination(soap_id){
            // console.log(soap_id);
            this.active.modal_examination = !this.active.modal_examination
            this.form_examination.soap_id = soap_id 
            if (this.active.modal_examination == true) {
                this.getExamination(this.form_examination.soap_id)
            }
        },
        getExamination(soap_id){
            this.$store.dispatch('soap/getExamination', {soap_id: soap_id}).then(response => {
                this.examination_list = response
                // console.log('this.examination_list: ', this.examination_list);
            })
        },
        validationExamination(){
            if (this.form_examination.file && this.form_examination.examination_date && this.form_examination.category_examination_id) {
                try {
                    this.$store.dispatch('soap/postPutExamination', this.form_examination).then((response) => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.getExamination(this.form_examination.soap_id)
                        this.resetExaminationForm()
                    })
                    
                } catch (error) {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    console.error('error at: ', error);
                }
    
            } else {
                this.notification('Warning','CloseIcon','warning','Form wajib diisi semua')
            }
        },
        removeExamination (obj) {
            this.$store.dispatch('soap/removeExamination', obj)
            .then(response => {
                if (response.status === 200 || response.status === 201) {
                    this.getExamination(this.form_examination.soap_id)
                    this.notification('Success','CheckIcon','success','You have successfully')
                } else {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                }
            })
        },

        resetExaminationForm(){
            this.form_examination.id = ''
            this.form_examination.file = null
            this.form_examination.examination_date = moment(new Date()).format('YYYY-MM-DD')
            this.form_examination.category_examination_id = ''
        },

        getHead(param) {
            const obj = this.OptionsHeadCircumference.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getHairColor(param) {
            const obj = this.OptionsHeadHairColor.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getHairCondition(param) {
            const obj = this.OptionsHeadHairCondition.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getEyeConjunctive(param) {
            const obj = this.OptionsEyeConjunctiva.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getEyeSclera(param) {
            const obj = this.OptionsEyeSclera.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getEyePupil(param) {
            const obj = this.OptionsEyePupil.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getEyeReflect(param) {
            const obj = this.OptionsEyeReflect.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getMouthMucosa(param) {
            const obj = this.OptionsMouthMukosa.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getMouthDiscoloration(param) {
            const obj = this.OptionsMouthHiperemis.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getMouthTongue(param) {
            const obj = this.OptionsMouthTongue.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getMouthCaries(param) {
            const obj = this.OptionsMouthKaries.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getNeckKGB(param) {
            const obj = this.OptionsNeckKGB.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getNeckTracheaPosition(param) {
            const obj = this.OptionsNeckTrakea.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getNeckMassa(param) {
            const obj = this.OptionsNeckMassa.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getInspectionChest(param) {
            const obj = this.OptionsInspectionChest.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getInspectionRetractiont(param) {
            const obj = this.OptionsInspectionRetraction.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getPalpationChest(param) {
            const obj = this.OptionsPalpationChest.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getPalpationFremitus(param) {
            const obj = this.OptionsPalpationFremitus.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getPalpationKrepitasi(param) {
            const obj = this.OptionsPalpationKrepitasi.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getAuskultationPulmoVasikuler(param) {
            const obj = this.OptionsAuskultationPulmoVasikuler.find(el => el.value === param)
            return obj ? obj.title : '-'
        },
        
        getAuskultationPulmoRhonki(param) {
            const obj = this.OptionsAuskultationPulmoRhonki.find(el => el.value === param)
            return obj ? obj.title : '-'
        },
        
        getAuskultationPulmoWheezing(param) {
            const obj = this.OptionsAuskultationPulmoWheezing.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getAuskultationCORBJ(param) {
            const obj = this.OptionsAuskultationCORBJ.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getAuskultationCORMurmur(param) {
            const obj = this.OptionsAuskultationCORMurmur.find(el => el.value === param)
            return obj ? obj.title : '-'
        },

        getAuskultationCORGallop(param) {
            const obj = this.OptionsAuskultationCORGallop.find(el => el.value === param)
            return obj ? obj.title : '-'
        },
        getAbdomenSupel(param) {
            const obj = this.OptionsAbdomenSupel.find(el => el.value === param)
            return obj ? obj.title : '-'
        },
        getAbdomenBising(param) {
            const obj = this.OptionsAbdomenBising.find(el => el.value === param)
            return obj ? obj.title : '-'
        },
        getAbdomenPress(param) {
            const obj = this.OptionsAbdomenPress.find(el => el.value === param)
            return obj ? obj.title : '-'
        },
        getAbdomenHepar(param) {
            const obj = this.OptionsAbdomenHepar.find(el => el.value === param)
            return obj ? obj.title : '-'
        },
        getEkstremitasAkral(param) {
            const obj = this.OptionsEkstremitasAkral.find(el => el.value === param)
            return obj ? obj.title : '-'
        },
        getEkstremitasCRT(param) {
            const obj = this.OptionsEkstremitasCRT.find(el => el.value === param)
            return obj ? obj.title : '-'
        },
        getEkstremitasEdema(param) {
            const obj = this.OptionsEkstremitasEdema.find(el => el.value === param)
            return obj ? obj.title : '-'
        },   


        addKala() {
            let obj = {
                shift: this.input_kala.shift,
                time: this.input_kala.time,
                blood_pressure: this.input_kala.blood_pressure,
                pulse: this.input_kala.pulse,
                temperature: this.input_kala.temperature,
                fundus: this.input_kala.fundus,
                contraction: this.input_kala.contraction,
                bladder: this.input_kala.bladder,
                out_blood: this.input_kala.out_blood,
            }            
            this.kala.items.push(obj)
            
            this.input_kala.shift = ''
            this.input_kala.time = ''
            this.input_kala.blood_pressure = ''
            this.input_kala.pulse = ''
            this.input_kala.temperature = ''
            this.input_kala.fundus = ''
            this.input_kala.contraction = ''
            this.input_kala.bladder = ''
            this.input_kala.out_blood = ''
        },

        removeKala(index) {
            this.kala.items.splice(index, 1)
        },
        toggleTimePanel() {
            this.showTimePanel = !this.showTimePanel;
        },
        toggleTimeRangePanel() {
            this.showTimeRangePanel = !this.showTimeRangePanel;
        },
        handleOpenChange() {
            this.showTimePanel = false;
        },
        handleRangeClose() {
            this.showTimeRangePanel = false;
        },
        openPrintSPRI(id){
            const url = this.$router.resolve({ path: '/prints/spri/'+ id }).href;
            window.open(url, '_blank');
        },
        parsedBody(text){
            return text ? text.replace(/\n/g,"<br>") : ''
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
        // getFromEdit(data) {
        //     this.dataFromEdit = {}
        //     this.dataFromEdit = data
        //     this.$emit('getFromEdit', data)
        // },
        isRefcontrol(url,data) {
            this.imageview = ''
            this.imageview = data
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
        refresh () {
            router.go(router.currentRoute)
        },
        getSoapLabel(unit, initial, booking) {
            const emergency_id = "444b3134-5b75-4b67-af72-a558d46716d7"
            const inpatient_id = "40f06534-07ec-4221-ad9d-dea08196079f"
            const outpatient_id = "3cb626f5-9148-4fb1-8663-3d7f028b01a6"

            if (unit == emergency_id) {
                return 'ASESMEN DOKTER IGD'
            } else if (unit == outpatient_id && booking == 1) {
                return 'BOOKING PENUNJANG'  
            } else if (unit == outpatient_id) {
                return 'ASESMEN DOKTER RAWAT JALAN'  
            } else if (unit == inpatient_id && initial == 1) {
                return 'KAJIAN AWAL DOKTER RAWAT INAP'  
            } else if (unit == inpatient_id) {
                return 'CPPT DOKTER RAWAT INAP'  
            } else {
                return 'ASESMEN DOKTER'
            }
        },
        getData (id) {
			this.$store.dispatch('registration/getById', id).then(data => {
                this.regData = data[0]
			    this.$store.dispatch('patient/getAllergyByNorm', data[0].norm)
                this.form_booking.registration_destination_id = this.regData.registration_destination_id
                this.form_booking.doctor_in_charge_id = this.regData.doctor_in_charge_id

                this.form_checkout.type_emergency_id = this.regData.type_emergency_id
                this.form_checkout.type_visit_id = this.regData.type_visit_id
                this.form_checkout.exit_id = this.regData.exit_id

                this.$store.dispatch('soap/getByRM', this.regData.norm)
                
                const emergency_id = "444b3134-5b75-4b67-af72-a558d46716d7"
                const inpatient_id = "40f06534-07ec-4221-ad9d-dea08196079f"
                const outpatient_id = "3cb626f5-9148-4fb1-8663-3d7f028b01a6"
                
                if (this.regData.health_facility_id == emergency_id) {
                    this.isIGD = true
                    this.isRanap = false
                    this.isRajal = false
                    
                } else if (this.regData.health_facility_id == inpatient_id) {
                    this.isIGD = false
                    this.isRanap = true
                    this.isRajal = false

                } else if (this.regData.health_facility_id == outpatient_id) {
                    this.isIGD = false
                    this.isRanap = false
                    this.isRajal = true
                }
                    
            })
        },
        goToPenunjangTab(tab){
            this.active_act.tab_penunjang = tab
        },
        // goToIGDPenunjang(tab){
        //     this.active_act.tab_igd_penunjang = tab
        // },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        openModalBooking(view, data){
            this.resetBooking()
            this.active_act.tab_penunjang = 'menu1'
            this.active_act.modal_booking = true
            this.active.booking_view = view
            console.log('data booking: ', data);

            if (this.active.booking_view == 'ADD') {
                this.form_booking.date_request = moment(new Date()).format('YYYY-MM-DD')
                this.form_booking.id = ''
                this.active.editAccess.laboratory = true
                this.active.editAccess.radiology = true
                this.active.disabled_booking_date = false
                
            } else {
                if (data != null) {
                    // console.log('data: ', data);
                    // Necessary fill data
                    if (data.laboratory_request && data.radiology_request && data.physiotherapy_request) {
                        this.form_booking.date_request = moment(data.laboratory_request.date_request).format('YYYY-MM-DD')
                        this.active.disabled_booking_date = true
                    } else if (data.laboratory_request) {
                        this.form_booking.date_request = moment(data.laboratory_request.date_request).format('YYYY-MM-DD')
                        this.active.disabled_booking_date = true
                    } else if (data.radiology_request) {
                        this.form_booking.date_request = moment(data.radiology_request.date_request).format('YYYY-MM-DD')
                        this.active.disabled_booking_date = true
                    } else if (data.physiotherapy_request) {
                        this.form_booking.date_request = moment(data.physiotherapy_request.date_request).format('YYYY-MM-DD')
                        this.active.disabled_booking_date = true
                    } else {
                        this.form_booking.date_request = moment(new Date()).format('YYYY-MM-DD')
                        this.active.disabled_booking_date = false
                    }

                    if (data.icd10) {
                        const mappedIcd10 = data.icd10.map(item => {
                            return {
                                icd10_id: item.icd10_id,
                                value: item.icd10_id,
                                title: `${item.code} ${item.name}`, 
                                action: "action"
                            };
                        });                
                        this.form_booking.icd10 = mappedIcd10;
                    }     

                    this.form_booking.id = data.id
                    
                    if (data.laboratory_request_items) {
                        const mappedLab = data.laboratory_request_items.map(item => {
                            return {
                                costing_name: item.costing_name,
                                laboratory_package_id: item.laboratory_package_id,
                                service_class_id: item.service_class_id
                            };
                        });                
                        this.form_booking.laboratory_request_items = mappedLab;
                    } else {
                        this.form_booking.laboratory_request_items = []
                    }
                    
                    if (data.radiology_request_items) {
                        const mappedRad = data.radiology_request_items.map(item => {
                            return {
                                costing_name: item.costing_name,
                                radiology_package_id: item.radiology_package_id,
                                service_class_id: item.service_class_id
                            };
                        });                
                        this.form_booking.radiology_request_items = mappedRad;
                    } else {
                        this.form_booking.radiology_request_items = []
                    }

                    if (data.physiotherapy_request_items) {
                        const mappedRad = data.physiotherapy_request_items.map(item => {
                            return {
                                costing_name: item.costing_name,
                                physiotherapy_package_id: item.physiotherapy_package_id,
                                service_class_id: '9b702245-79c5-4beb-8218-53afb08647aa'
                            };
                        });                
                        this.form_booking.physiotherapy_request_items = mappedRad;
                    } else {
                        this.form_booking.physiotherapy_request_items = []
                    }

                    // handling if request is processed or not
                    if (data.laboratory_request && data.laboratory_request.status) {
                        if (data.laboratory_request.status == 2 || data.laboratory_request.status == 3 ) {
                            this.active.editAccess.laboratory = false
                        } else {
                            this.active.editAccess.laboratory = true
                        }
                    }

                    if (data.radiology_request && data.radiology_request.status) {
                        if (data.radiology_request.status == 2 || data.radiology_request.status == 3 ) {
                            this.active.editAccess.radiology = false
                        } else {
                            this.active.editAccess.radiology = true
                        }
                    }

                }
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
        validationBooking(){
            this.isDisable = true
            try {
                this.$store.dispatch('soap/postPutFullBook', this.form_booking).then(res => {
                    this.active_act.modal_booking = false
                    this.resetBooking()
                    this.isDisable = false
                    this.notification('Success','CheckIcon','success','You have successfully')
                    // this.socket.emit('SEND_LAB', []);
                    // this.socket.emit('SEND_RAD', []);
                    this.$store.dispatch('soap/getByRM', this.regData.norm)
                })
                
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                this.isDisable = false
                console.error('ERR at: ', error);
            }
        },
        resetBooking() {
            this.form_booking.icd10 = []
            this.form_booking.date_request = ''
            this.form_booking.laboratory_request_items = []
            this.form_booking.radiology_request_items = []
            this.form_booking.physiotherapy_request_items = []
            // console.log('this.form_booking: ', this.form_booking);
        },
        onAdd10 (event) {
            localStorage.setItem("soap", JSON.stringify(this.form_soap))
            this.icd10_id = ''
            if (this.form_booking.icd10.some(item => item.icd10_id === event.value)) {
                this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list ICD10 yang sama')
            } else {
                this.form_booking.icd10.push({ icd10_id:event.value, title:event.title, value:event.value, action: 'action'})
            }
            // console.log('this.form_booking.icd10', this.form_booking.icd10);
        },
        delete10 (index) {
            localStorage.setItem("soap", JSON.stringify(this.form_booking))
            this.form_booking.icd10.splice(index, 1)
        },
        onSearchIcd10(search, loading) {
            // console.log('ini:  ',search, loading);
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
        
        
        openModalPartograf(){
            this.active_act.modal_Partograf = true
        },
        openModalNewBorn(){
            this.active_act.modal_NewBorn = true
        },

        openModalSPRI(){
            this.active_act.modal_SPRI = true
            this.handlingSPRI()
        },

        handlingSPRI() {
            let regDesId = this.reg_des_id

            const params = {
                registration_destination_id: regDesId,
                is_booking: 0
            }
            // this.$store.dispatch('soap/getByRegId', regDesId).then((response) => {
            this.$store.dispatch('soap/getDetail', params).then((response) => {
                const obj = response[0]
                
                if (response.length > 0) {
                    this.$store.dispatch('soap/getInpatientByRegId', regDesId).then((response_inpatient)=>{
                        let res = response_inpatient[0]
                        if (response_inpatient.length > 0) {
                            this.form_spri.indication = res.indication
                            this.form_spri.plan = res.plan
                            this.form_spri.result = res.result
                            this.form_spri.condition = res.condition
                            this.form_spri.doctor_in_charge_id = res.doctor_in_charge_id
                            this.form_spri.type_room_id = res.type_room_id
                            this.form_spri.id = res.id 
                        }
                    })

                    this.form_spri.soap_id = obj.id 
                    if (obj.icd10) {
                        const mappedIcd10 = obj.icd10.map(item => {
                            return {
                                icd10_id: item.icd10_id,
                                value: item.icd10_id,
                                title: `${item.code} ${item.name}`, 
                                action: "action"
                            };
                        });                
                        this.form_spri.icd10 = mappedIcd10;
                    }  
                }
            })
        },

        validationSPRI() {
            // console.log('this.form_spri: ', this.form_spri)
            this.isDisabledSPRI = true
            try {
                this.$store.dispatch('soap/formPostInPatient', this.form_spri).then((response) => {
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.active_act.modal_SPRI = false
                    this.isDisabledSPRI = false
                    this.resetSPRI()
                })
            } catch (error) {
                this.isDisabledSPRI = false
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error at: ', error)
            }
        },

        resetSPRI() {
            this.form_spri.id = ''
            this.form_spri.soap_id = ''
            this.form_spri.doctor_in_charge_id = ''
            this.form_spri.indication = ''
            this.form_spri.plan = ''
            this.form_spri.result = ''
            this.form_spri.condition = ''
            this.form_spri.type_room_id = ''
        },

    },
    created () {
        this.$store.dispatch('helper/getParam', 'type_room')
        this.$store.dispatch('laboratory/getPackageAll', { type_costing_id: 4 })
        this.$store.dispatch('radiology/getPackageAll')
        this.$store.dispatch('physiotherapy/getPackageAll')
        this.$store.dispatch('packages/getParam')
        this.$store.dispatch('employee/getParamAllSpecialist')
        this.$store.dispatch('employee/getParam', '')
        this.$store.dispatch('soap/getExaminationCategory')
        
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