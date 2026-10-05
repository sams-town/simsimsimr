<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="generateToImage" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper mt-16">
                <div id="canvas" ref="canvas" style="position: relative;">
                    <div>
                        <div class="d-flex align-items-center  w-100 px-3 mt-8">
                            <img src="@/@core/assets/images/bpjs/bpjs_logo.png" height="40px" alt="rs-logo"> <br>
                            <div class="font-weight-bold ml-24">
                                <div>SURAT ELEGIBILITAS PESERTA</div>
                                <div>RS Taman Harapan Baru</div>
                            </div>
                        </div>
                        <div class="mt-20">
                            <div class="row mx-0" style="font-size: 14px;">
                                <div class="col-6">
                                    <table>
                                        <tr>
                                            <td class="first-cell">No. SEP</td>
                                            <td class="px-3">:</td>
                                            <td>{{pesertaData.noSep}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Tgl. SEP	</td>
                                            <td class="px-3">:</td>
                                            <td>{{pesertaData.tglSep}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">No. Kartu</td>
                                            <td class="px-3">:</td>
                                            <td>{{ pesertaData.peserta ? pesertaData.peserta.noKartu : '' }} <span class="ml-16">MR. {{ pesertaData.peserta ? pesertaData.peserta.noMr : '' }}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Nama Peserta</td>
                                            <td class="px-3">:</td>
                                            <td class="text-uppercase">{{pesertaData.peserta ? pesertaData.peserta.nama : '' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Tgl.Lahir</td>
                                            <td class="px-3">:</td>
                                            <td>{{ pesertaData.peserta ? pesertaData.peserta.tglLahir : '' }} Kelamin {{ pesertaData.peserta ? pesertaData.peserta.kelamin : '' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">No.Telepon</td>
                                            <td class="px-3">:</td>
                                            <td>{{regData.phone}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Sub/Spesialis</td>
                                            <td class="px-3">:</td>
                                            <td>{{pesertaData.poli}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Dokter</td>
                                            <td class="px-3">:</td>
                                            <td>{{regData.doctor_in_charge_name}}</td>
                                        </tr>
                                        <!-- <tr>
                                            <td class="first-cell">Dokter</td>
                                            <td class="px-3">:</td>
                                            <td>
                                                <template v-if="pesertaData.kontrol.nmDPJP">
                                                    {{pesertaData.dpjp.nmDPJP}}
                                                </template>
                                                <template v-if="pesertaData.kontrol.noSurat">
                                                    {{pesertaData.kontrol.nmDokter}}
                                                </template>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Faskes Perujuk</td>
                                            <td class="px-3">:</td>
                                            <td class="text-uppercase"></td>
                                        </tr> -->
                                        <tr>
                                            <td class="first-cell">Diagnosa Awal</td>
                                            <td class="px-3">:</td>
                                            <td>{{pesertaData.diagnosa}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Catatan</td>
                                            <td class="px-3">:</td>
                                            <td>{{pesertaData.catatan}}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-6">
                                    <table>
                                        <tr>
                                            <td class="first-cell">Peserta</td>
                                            <td class="px-3">:</td>
                                            <td class="text-uppercase">{{ pesertaData.peserta ? pesertaData.peserta.jnsPeserta : '' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">COB</td>
                                            <td class="px-3">:</td>
                                            <td>{{pesertaData.cob == "0" ? "Tidak" : "Ya" }}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell" style="vertical-align:top;">Jns.Kunjungan</td>
                                            <td class="px-3" style="vertical-align:top;">:</td>
                                            <td style="vertical-align:top;">
                                                <li>{{ pesertaData.tujuanKunj ? pesertaData.tujuanKunj.nama : '' }}</li>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Jns.Rawat</td>
                                            <td class="px-3">:</td>
                                            <td>{{pesertaData.jnsPelayanan}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Kls.Hak</td>
                                            <td class="px-3">:</td>
                                            <td>{{ pesertaData.peserta ? pesertaData.peserta.hakKelas : '' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Kls.Rawat</td>
                                            <td class="px-3">:</td>
                                            <td>-</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Penjamin</td>
                                            <td class="px-3">:</td>
                                            <td>{{ pesertaData.penjamin }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="px-3 mt-10 row">
                                <div class="col-9" style="font-size: 10px; line-height: 120%;">
                                    <div class="">
                                        <div>*Saya menyetujui BPJS Kesehatan untuk :</div>
                                        <ul class="m-0 pl-16">
                                            <li type="a">membuka dan atau menggunakan informasi medis Pasien untuk keperluan administrasi, pembayaran asuransi atau jaminan pembiayaan kesehatan</li>
                                            <li type="a">memberikan akses informasi medis atau riwayat pelayanan kepada dokter/tenaga medis pada RS Taman Harapan Baru untuk kepentingan pemeliharaan kesehatan, pengobatan, penyembuhan, dan perawatan Pasien</li>
                                        </ul>
                                    </div>
                                    <div class="mt-1">
                                        <div>*Saya mengetahui dan memahami :</div>
                                        <ul class="m-0 pl-16">
                                            <li type="a">Rumah Sakit dapat melakukan koordinasi dengan PT Jasa Raharja / PT Taspen / PT ASABRI / BPJS Ketenagakerjaan atau Penjamin lainnya, jika Peserta merupakan pasien yang mengalami kecelakaan lalulintas dan / atau kecelakaan kerja</li>
                                            <li type="a">SEP bukan sebagai bukti penjaminan peserta</li>
                                        </ul>
                                    </div>
                                    <div class="mt-1">
                                        <div class="">**Dengan tampilnya luaran SEP elektronik ini merupakan hasil validasi terhadap eligibilitas Pasien secara elektronik (validasi finger print atau biometrik / sistem validasi lain) dan selanjutnya Pasien dapat mengakses pelayanan kesehatan rujukan sesuai ketentuan berlaku.</div>
                                        <div>Kebenaran dan keaslian atas informasi data Pasien menjadi tanggung jawab penuh FKRTL</div>
                                    </div>
                                </div>
                                <!-- <div class="text-muted col-8" style="font-size: 12px;">
                                    <div class="">*Saya Menyetujui BPJS Kesehatan menggunakan informasi Medis Pasien jika diperlukan.</div>
                                    <div class="">*SEP bukan sebagai bukti penjaminan peserta</div>
                                    <div class=" mt-32">Cetakan ke 1 {{regIns.timestampt}}</div>
                                </div> -->
                                <div class="col-3" style="font-size: 14px;">
                                    <div class="text-center">
                                        <div>Pasien / Keluarga Pasien</div>
                                        <template v-if="globalQRText">
                                            <div class="my-2" id="renderQR">
                                                <qrcode-vue id="qrcodeCanvas" :value="globalQRText" :size="70" level="H" ></qrcode-vue>
                                            </div>
                                        </template>
                                        <div>{{ pesertaData.peserta ? pesertaData.peserta.nama : '' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- <iframe style="width: 100vw; height: 100vh" :src="`http://192.168.100.3:5000/${patient.registration_insurance[0].file}`" /> -->
                    <b-table-simple class="w-100 sbpk" style="margin-top: 1400px;">
                        <b-tr>
                            <b-td colspan="5" class="p-16">
                                <div class="row align-items-center">
                                    <div class="col-5">
                                        <img src="../../../../../@core/assets/images/logo-thb.jpg" width="240px" alt="rs-logo"> <br>
                                        <div class="text-regular-10 mt-12">
                                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                                            <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                                        </div>
                                    </div>
                                    <div class="col text-center text-bold-24">SURAT BUKTI PELAYANAN KESEHATAN (SBPK)</div>
                                </div>
                            </b-td>
                        </b-tr>
                        <b-tr>
                            <b-td colspan="3" class="p-16">
                                <ol type="1">
                                    <li>
                                        <div class="row">
                                            <div class="col-5">Nama Pasien</div>
                                            <div>:</div>
                                            <div class="col">{{ patient.name }}</div>
                                        </div>
                                    </li>
                                    <li class="mt-2">
                                        <div class="row">
                                            <div class="col-5">Nomor RM</div>
                                            <div>:</div>
                                            <div class="col">{{ patient.norm }}</div>
                                        </div>
                                    </li>
                                    <li class="mt-2">
                                        <div class="row">
                                            <div class="col-5">Tanggal Lahir</div>
                                            <div>:</div>
                                            <div class="col">{{ patient.birth_date }}</div>
                                        </div>
                                    </li>
                                    <li class="mt-2">
                                        <div class="row">
                                            <div class="col-5">Jenis Kelamin</div>
                                            <div>:</div>
                                            <div class="col">{{ patient.sex_name }}</div>
                                        </div>
                                    </li>
                                    <li class="mt-2">
                                        <div class="row">
                                            <div class="col-5">Tanggal Masuk</div>
                                            <div>:</div>
                                            <div class="col">{{ patient.checkin_date }}</div>
                                        </div>
                                    </li>
                                </ol>
                            </b-td>
                            <b-td colspan="2" class="p-16">
                                <ol style="list-style-type:square" >
                                    <li>
                                        <div class="row">
                                            <div class="col-5">Kriteria Pasien</div>
                                            <div>:</div>
                                            <div class="col">{{ patient.type_emergency_name }}</div>
                                        </div>
                                    </li>
                                    <li class="mt-2">
                                        <div class="row">
                                            <div class="col-5">Jenis Kunjungan</div>
                                            <div>:</div>
                                            <div class="col">{{ patient.type_visit_name }}</div>
                                        </div>
                                    </li>
                                    <li class="mt-2">
                                        <div class="row">
                                            <div class="col-5">Cara Keluar RS</div>
                                            <div>:</div>
                                            <div class="col">{{ patient.exit_name ? patient.exit_name : '-' }}</div>
                                        </div>
                                    </li>
                                </ol>
                            </b-td>
                        </b-tr>
                        <b-tr>
                            <b-td colspan="5" >
                                <div>
                                    <div class="font-weight-bold" >ANAMNESA :</div>
                                    <div>
                                        <ul type="square" >
                                            <li>
                                                <b>Keluhan Utama: </b>
                                                <div>{{ result.main_complaint }}</div>
                                            </li>
                                            <li class="mt-12">
                                                <b>Riwayat Penyakit Sekarang : </b>
                                                <div v-html="parsedBody(result && result.history_disease_now ? result.history_disease_now : '')"></div>
                                            </li>
                                            <li class="mt-12">
                                                <b>Riwayat Penyakit Dahulu : </b>
                                                <div v-html="parsedBody(result && result.history_disease_past ? result.history_disease_past : '')"></div>
                                            </li>
                                            <li class="mt-12">
                                                <b>Riwayat Obat : </b>
                                                <div v-html="parsedBody(result && result.history_drugs ? result.history_drugs : '')"></div>
                                            </li>
                                            <li class="mt-12">
                                                <b>Systolic: </b>{{ this.result.systolic_blood_pressure ? this.result.systolic_blood_pressure : '-' }} mmHg,
                                                <b>Diastolic: </b>{{ this.result.diastolic_blood_pressure ? this.result.diastolic_blood_pressure : '-' }} mmHg,
                                                <b>Frekuensi Nadi: </b>{{ this.result.pulse_frequency ? this.result.pulse_frequency : '-' }} x/mnt,
                                                <b>Frekuensi Nafas: </b>{{ this.result.breath_frequency ? this.result.breath_frequency : '-' }} x/mnt,
                                                <b>Oksimetri: </b>{{ this.result.oximetry_frequency ? this.result.oximetry_frequency : '-' }} %,
                                                <b>Suhu: </b>{{ this.result.temperature ? this.result.temperature : '-' }} ᵒC,
                                                <b>Skor Nyeri: </b>{{ this.result.sore_score ? this.result.sore_score : '-' }},
                                                <b>EWS: </b>{{ this.result.ews ? this.result.ews : '-' }},
                                                <b>Gula Darah: </b>{{ this.result.blood_sugar ? this.result.blood_sugar : '-' }} mg/dL,
                                            </li>
                                            <li class="mt-12">
                                                <b>Berat Badan: </b>{{ this.result.weight ? this.result.weight : '-' }} kg,
                                                <b>Tinggi Badan: </b>{{ this.result.height ? this.result.height : '-' }} cm,
                                                <b>IMT: </b>{{ this.result.imt ? this.result.imt : '-' }} kg/m²,
                                                <b>Lingkar Kepala: </b>{{ this.result.head_circumference ? this.result.head_circumference : '-' }} cm,
                                                <b>Lingkar Dada: </b>{{ this.result.chest_circumference ? this.result.chest_circumference : '-' }} cm,
                                                <b>Lingkar Lengan Atas: </b>{{ this.result.arm_circumference ? this.result.arm_circumference : '-' }} cm,
                                                <b>Lingkar Perut: </b>{{ this.result.stomach_circumference ? this.result.stomach_circumference : '-' }} cm,
                                                <b>Lingkar Lutut: </b>{{ this.result.knee_circumference ? this.result.knee_circumference : '-' }} cm,
                                                <b>Pemeriksaan Fisik: </b>{{ this.result.physical_check ? this.result.physical_check : '-' }} cm,
                                                <b>Lainnya: </b>{{ this.result.soap_o_anythingelse ? this.result.soap_o_anythingelse : '-' }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </b-td>
                        </b-tr>
                        <b-tr>
                            <b-td colspan="5"></b-td>
                        </b-tr>
                        <b-tr class="text-center text-bold-14">
                            <b-th style="width: 12%;">POLIKLINIK / PENUNJANG</b-th>
                            <b-th>TINDAKAN PEMERIKSAAN PENUNJANG / TETAP</b-th>
                            <b-th>KODE ICD10</b-th>
                            <b-th>HASIL PEMERIKSAAN</b-th>
                            <b-th style="width: 10%;">TDT & NAMA JELAS DOKTER / PETUGAS</b-th>
                        </b-tr>
                        <template v-if="result.laboratory_request_items">
                            <template v-for="(item, index) in this.result.laboratory_request_items">
                                <b-tr>
                                    <b-td>LABORATORIUM</b-td>
                                    <b-td>{{ item.costing_name }}</b-td>
                                    <b-td></b-td>
                                    <b-td></b-td>
                                    <b-td></b-td>
                                </b-tr>
                            </template>
                        </template>
                        <template v-if="result.radiology_request_items">
                            <template v-for="(item, index) in this.result.radiology_request_items">
                                <b-tr>
                                    <b-td>RADIOLOGI</b-td>
                                    <b-td>{{ item.costing_name }}</b-td>
                                    <b-td></b-td>
                                    <b-td></b-td>
                                    <b-td></b-td>
                                </b-tr>
                            </template>
                        </template>
                        <template v-if="result.physiotherapy_request">
                            <template v-for="(item, index) in result.physiotherapy_request">
                                <template v-for="(list, index) in item.physiotherapy_request_items">
                                    <b-tr>
                                        <b-td>FISIOTERAPI</b-td>
                                        <b-td>{{ list.costing_name }}</b-td>
                                        <b-td></b-td>
                                        <b-td></b-td>
                                        <b-td></b-td>
                                    </b-tr>
                                </template>
                            </template>
                        </template>
                        <b-tr>
                            <b-td colspan="5"></b-td>
                        </b-tr>
                        <!-- <b-tr>
                            <b-td colspan="4"></b-td>
                            <b-td>KODE ICD10</b-td>
                        </b-tr> -->
                        <b-tr>
                            <b-td colspan="4" >
                                <div class="font-weight-bold" >DIAGNOSA PRIMER <small>(Diisi hanya boleh 1 diagnosa)</small></div>
                            </b-td>
                            <b-td class="font-weight-bold">KODE ICD10</b-td>
                        </b-tr>
                        <template v-for="(item,index) in result.icd10">
                            <b-tr>
                                <template v-if="index == 0">
                                    <b-td colspan="4">{{ index+1 }}. {{ item.name }}</b-td>
                                    <b-td>{{ item.code }}</b-td>
                                </template>
                                <!-- <template v-else-if="result.icd10.length == 0">
                                    <b-td colspan="4">-</b-td>
                                    <b-td>-</b-td>
                                </template> -->
                            </b-tr>
                        </template>
                        <b-tr>
                            <b-td colspan="4">
                                <div class="font-weight-bold" >DIAGNOSA SEKUNDER <small class="font-italic">(Diisi boleh lebih dari 1 diagnosa)</small> </div>
                            </b-td>
                            <b-td class="font-weight-bold">KODE ICD10</b-td>
                        </b-tr>
                        <template v-for="(item,index) in result.icd10">
                            <b-tr>
                                <template v-if="index != 0">
                                    <b-td colspan="4">{{ index }}. {{ item.name }}</b-td>
                                    <b-td>{{ item.code }}</b-td>
                                </template>
                                <!-- <template v-else>
                                    <b-td colspan="4">-</b-td>
                                    <b-td>-</b-td>
                                </template> -->
                            </b-tr>
                        </template>
                        <b-tr>
                            <b-td colspan="5"></b-td>
                        </b-tr>
                        <!-- here -->
                        <b-tr>
                            <b-td colspan="4">
                                <div class="font-weight-bold">TINDAKAN</div>
                            </b-td>
                            <b-td class="font-weight-bold">KODE ICD9CM</b-td>
                        </b-tr>
                        <b-tr v-for="(item,index) in result.icd9cm">
                            <b-td colspan="4">{{ index+1 }}. {{ item.name }}</b-td>
                            <b-td>{{ item.code }}</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td colspan="5"></b-td>
                        </b-tr>
                        <b-tr>
                            <b-td colspan="5">
                                <div class="font-weight-bold" >LAINNYA</div>
                            </b-td>
                        </b-tr>
                        <b-tr v-if="result.soap_a_anythingelse" >
                            <b-td colspan="5">{{ result.soap_a_anythingelse }}</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td colspan="5"></b-td>
                        </b-tr>
                        <b-tr>
                            <b-td colspan="5" >
                                <div class="font-weight-bold" >TINDAKAN MEDIS</div>
                            </b-td>
                            <!-- <b-td></b-td> -->
                        </b-tr>
                        <template v-for="(item,index) in result.transaction_doctor">
                            <b-tr >
                                <b-td colspan="5">{{ index+1 }}. {{ item.costing_name }}</b-td>
                                <!-- <b-td></b-td> -->
                            </b-tr>
                        </template>
                        <b-tr>
                            <b-td colspan="5">
                                <div class="d-flex justify-content-between w-100">
                                    <div class="text-center d-flex flex-column align-items-center px-32">
                                        <!-- <div> Bekasi, {{ localDate(patient.checkin_date) }}</div> -->
                                        <div >Pasien / Keluarga Pasien</div>
                                        <template v-if="globalQRText">
                                            <div class="my-2" id="renderdQR">
                                                <qrcode-vue id="qrcodeCanvasd" :value="globalQRText" :size="100" level="H" ></qrcode-vue>
                                            </div>
                                        </template>
                                        <div>({{ patient.name }})</div>
                                    </div>
                                    <div class="text-center px-32">
                                        <!-- <div> Bekasi, {{ today }}</div> -->
                                        <div> Bekasi, {{ localDate(patient.checkin_date) }}</div>
                                        <div :class="result.employee_signature ? '' : 'mb-64'" >DPJP</div>
                                        <template v-if="result.employee_signature">
                                            <b-img :src="urlbe+result.employee_signature" style="width: 120px;"/>
                                        </template>
                                        <div>({{ result.employee_doctor_name }})</div>
                                    </div>
                                </div>
                                <div class="mt-24 text-uppercase text-semi-12">
                                    <div style="text-decoration: underline;" >catatan:</div>
                                    <ol type="1">
                                        <li>form ini tidak untuk dibawa pulang pasien</li>
                                        <li>jika pasien pulang atas permintaan sendiri maka tidak bisa menggunakan BPJS (pasien bayar sendiri)</li>
                                    </ol>
                                </div>
                            </b-td>
                        </b-tr>
                    </b-table-simple>
                    <div class="row align-items-center pb-40" style="border-bottom: 2px solid black;margin-top: 2000px;">
                        <section ref="left-section" class="col-7" style="margin-top: -800px;">
                            <div ref="left-header" class="d-flex justify-content-between pb-16" style="border-bottom: 3px solid black;">
                                <div class="d-flex align-items-center justify-content-center">
                                    <img src="../../../../../@core/assets/images/logo-thb-small.png" width="80px" alt="rs-logo"> <br>
                                    <div class="text-regular-12 pl-16">
                                        <p class="text-bold-18">RS Taman Harapan Baru</p>
                                        <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                                        <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                                    </div>
                                </div>
                                <img src="../../../../../@core/assets/images/lafki-logo.png" width="80px" class="text-center" alt="rs-logo"> <br>
                            </div>
                            <div class="row mt-16 text-regular-12">
                                <div class="col-6">
                                    <table class="mt-16">
                                        <tr>
                                            <td class="align-top col-3" style="white-space: nowrap;">Klinik / Ruang</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td>{{ data.health_facility_item_name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top col-3">Nama Dokter</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td>{{ data.doctor_in_charge_first_name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top col-3">SIP Dokter</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="align-top col-3">No. Telp</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="align-top col-3">Diagnosa</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-6">
                                    <table class="mt-16">
                                        <tr>
                                            <td class="align-top col-3">Tanggal</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td>{{ data.date_added }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top col-3" style="white-space: nowrap;">TTD Dokter</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td class="p-32" style="border: 1.5px solid black;"></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <hr>

                            <div class="row text-regular-12">
                                <div class="col-6">
                                    <table>
                                        <tr>
                                            <td class="align-top col-3" style="white-space: nowrap;">Nama Pasien</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td>{{ data.patient_name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top col-3">Tgl. Lahir</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td>{{ data.patient_birth_date }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top col-3">No RM</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td>{{ data.norm }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top col-3">No. Telepon</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td>{{ data.phone }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-6">
                                    <table>
                                        <tr>
                                            <td class="align-top col-3" style="white-space: nowrap;">Jenis Kelamin</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td>{{ data.sex === 1 ? 'Laki - Laki' : 'Perempuan' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top col-3">SEP</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td>{{ data.sep }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top col-3">No. Kartu</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td>{{ data.number_insurance }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top col-3">Alamat</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td>{{ data.address }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="mt-24">
                                <b-table-simple class="text-regular-12">
                                    <b-tbody>
                                        <b-tr v-for="(item,index) in data.form_prescription" :key="index">
                                            <b-td>
                                                <div class="d-flex">
                                                    <div class="mr-8">R/</div>
                                                    <div>{{ item.pharmacy_catalog_name }} - {{ item.package_name }}, No. {{ (item.qty-(item.is_retur_full === 1 && item.pharmacy_catalog_id_retur || item.pharmacy_catalog_id_ref ? 0 : item.qty_retur)) }}</div>
                                                </div>
                                                <div>{{ item.used }}</div>
                                            </b-td>
                                        </b-tr>
                                    </b-tbody>
                                </b-table-simple>
                            </div>
                        </section>

                        <section ref="right-section" class="col-5">
                            <div class="d-flex justify-content-center align-items-center w-100 p-8 text-bold-24" style="border: 3px solid black; height: 60px;">{{ data.company_name }}</div>

                            <div class="mt-8">
                                <table class="mt-16 text-regular-12">
                                    <tr>
                                        <td class="col-3" style="vertical-align: top;">Petugas</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>{{ userData.employee_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="col-3" style="vertical-align: top; white-space: nowrap;">Tgl / Jam</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>{{ dateNow }}</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="mt-16">
                                <div class="font-weight-bold">TELAAH RESEP</div>
                                <b-table bordered small :fields="RecipeTable.fields" :items="RecipeTable.items" style="font-size: 14px;">
                                    <template #cell(criteria)="row">
                                        <div :class="row.item.criteria == 'SYARAT ADMINISTRASI' || row.item.criteria == 'ASPEK FARMASTETIK' || row.item.criteria == 'ASPEK KLINIS' ? 'font-weight-bold' : ''">
                                            {{ row.item.criteria }}
                                        </div>
                                    </template>
                                    <template #cell(yes)="row">
                                        <div class="text-center" style="width: 45px;">
                                            <b-icon v-if="row.item.yes === true"icon="check-lg"></b-icon>
                                        </div>
                                    </template>
                                    <template #cell(no)="row">
                                        <div class="text-center" style="width: 45px;">
                                            <b-icon v-if="row.item.no === true"icon="check-lg"></b-icon>
                                        </div>
                                    </template>
                                </b-table>
                            </div>

                            <div class="mt-16">
                                <div class="font-weight-bold">TELAAH OBAT</div>
                                <b-table bordered small :fields="DrugTable.fields" :items="DrugTable.items" style="font-size: 14px;">
                                    <template #cell(criteria)="row">
                                        <div :class="row.item.criteria == 'SYARAT ADMINISTRASI' ? 'font-weight-bold' : ''">
                                            {{ row.item.criteria }}
                                        </div>
                                    </template>
                                    <template #cell(yes)="row">
                                        <div class="text-center" style="width: 45px;">
                                            <b-icon v-if="row.item.yes === true"icon="check-lg"></b-icon>
                                        </div>
                                    </template>
                                    <template #cell(no)="row">
                                        <div class="text-center" style="width: 45px;">
                                            <b-icon v-if="row.item.no === true"icon="check-lg"></b-icon>
                                        </div>
                                    </template>
                                </b-table>
                            </div>

                            <div class="mt-16">
                                <b-table bordered small :fields="SignTable.fields" :items="SignTable.items" style="font-size: 14px;">
                                    <template #cell(criteria)="row">
                                        <div :class="row.item.criteria == 'SYARAT ADMINISTRASI' ? 'font-weight-bold' : ''">
                                            {{ row.item.criteria }}
                                        </div>
                                    </template>
                                    <template #cell(sign)="row">
                                        <div style="width: 100px;">

                                        </div>
                                    </template>
                                </b-table>
                            </div>

                            <div class="mt-64 d-flex flex-column align-items-center text-regular-12 text-center">
                                <div>Tanda Tangan Penerima</div>
                                <div class="mt-72">(...........................................................................)</div>
                            </div>

                        </section>
                    </div>

                    <div class="row align-items-center pb-40" style="border-bottom: 2px solid black;margin-top: 2000px;">
                        <div class="canvas-header px-3">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="../../../../../@core/assets/images/logo-thb-small.png" width="80px" alt="rshbh-logo"> <br>
                                        <div class="text-semi-12 px-16">
                                            <p>RS Taman Harapan Baru</p>
                                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex flex-column text-right h-100 align-items-end justify-content-center">
                                        <p class="text-regular-12">POLI / RUANGAN:</p>
                                        <p class="text-bold-16">{{ this.data.health_facility_item_name }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-20">
                            <p class="text-center text-bold-12" >Nama Dokter : {{ this.data.doctor_in_charge_name }}</p>
                            <p class="text-center text-bold-12" >Tanggal : {{ this.data.date_added }}</p>
                            <div class="col mt-16">
                                <table class="text-regular-12">
                                    <tr>
                                        <td>No. RM</td>
                                        <td class="px-3">:</td>
                                        <td>{{ this.data.norm }}</td>
                                    </tr>
                                    <tr>
                                        <td>Nama Pasien</td>
                                        <td class="px-3">:</td>
                                        <td>{{ this.data.patient_name }}</td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal Lahir</td>
                                        <td class="px-3">:</td>
                                        <td>{{ this.data.patient_birth_date }}</td>
                                    </tr>
                                    <tr>
                                        <td>Cara Bayar</td>
                                        <td class="px-3">:</td>
                                        <td>{{ this.data.insurance_name }} - {{ this.data.company_name }}</td>
                                    </tr>
                                    <tr>
                                        <td>Alamat</td>
                                        <td class="px-3">:</td>
                                        <td>-</td>
                                    </tr>
                                </table>
                            </div>
                            <!-- /info-table -->
                            <div class="px-3 mt-32">
                                <b-table-simple bordered>
                                    <b-thead class="text-uppercase text-bold-10 text-center">
                                        <b-tr>
                                            <b-th>No</b-th>
                                            <b-th>(Kode) nama Obat</b-th>
                                            <b-th>Signa</b-th>
                                            <b-th>Racik</b-th>
                                            <b-th>Jumlah Obat</b-th>
                                            <b-th>Harga</b-th>
                                            <b-th>Total Harga</b-th>
                                        </b-tr>
                                    </b-thead>
                                    <b-tbody class="text-regular-10 text-center">
                                        <template v-for="(item, index) in this.data.form_prescription">
                                            <b-tr>
                                                <b-td>{{ index + 1 }}</b-td>
                                                <b-td class="text-left">({{ item.pharmacy_catalog_code }}) {{ item.pharmacy_catalog_name }}</b-td>
                                                <b-td>{{ item.used }}</b-td>
                                                <b-td><template v-if="item.prescription_item_type_id !== 1">{{ item.prescription_item_type_id }}</template></b-td>
                                                <b-td>{{ (item.qty-(item.is_retur_full === 1 && item.pharmacy_catalog_id_retur || item.pharmacy_catalog_id_ref ? 0 : item.qty_retur)) }}</b-td>
                                                <b-td class="text-right">{{ item.var_1 | currency }}</b-td>
                                                <b-td class="text-right">{{ item.var_1*(item.qty-(item.is_retur_full === 1 && item.pharmacy_catalog_id_retur || item.pharmacy_catalog_id_ref ? 0 : item.qty_retur)) | currency }}</b-td>
                                            </b-tr>
                                        </template>
                                        <b-tr>
                                            <b-td colspan="4"></b-td>
                                            <b-td colspan="2" class="text-right">Total</b-td>
                                            <b-td class="text-right">{{this.data.var_1 | currency}}</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td colspan="4"></b-td>
                                            <b-td colspan="2" class="text-right">Jumlah Bayar</b-td>
                                            <b-td class="text-right">{{this.data.var_3 | currency}}</b-td>
                                        </b-tr>
                                    </b-tbody>
                                </b-table-simple>
                            </div>
                            <div class="row mt-32 text-regular-12 justify-content-between">
                                <div class="col-6">
                                    <div class="col-4">
                                        <b-table-simple small bordered>
                                            <b-tbody>
                                                <b-tr>
                                                    <b-td>H: </b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>E: </b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>T: </b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>P: </b-td>
                                                </b-tr>
                                            </b-tbody>
                                        </b-table-simple>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="row">
                                        <div class="col-6 ml-auto text-center">
                                            <div :class="result.employee_signature ? '' : 'mb-80'">Dokter</div>
                                            <template v-if="result.employee_signature">
                                                <b-img :src="urlbe+result.employee_signature" style="width: 120px;"/>
                                            </template>
                                            <div>{{ result.employee_doctor_name }}</div>
                                        </div>
                                        <div class="col-6">
                                            <p class="text-center">Apoteker</p>
                                            <p class="text-center mt-80">(........................................)</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-center pb-40" style="border-bottom: 2px solid black;margin-top: 2000px;" v-if="resultlab.result_detail">
                        <div class="col-2 text-center">
                            <img src="../../../../../@core/assets/images/logo-thb-small.png" width="60%" alt="rs-logo"> <br>
                        </div>
                        <div class="col text-center">
                            <p class="text-bold-18">RS Taman Harapan Baru</p>
                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                            <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                        </div>
                        <div class="col-2 text-center">
                            <img src="../../../../../@core/assets/images/lafki-logo.png" width="60%" alt="lafki-logo"> <br>
                        </div>
                    </div>
                    <div class="mt-20" v-if="resultlab.result_detail">
                        <p class="text-center text-bold-16">HASIL PEMERIKSAAN LABORATORIUM</p>
                        <div class="row mx-0 mt-28">
                            <div class="col-6">
                                <table class="text-regular-14">
                                    <tr>
                                        <td>No. Reg / RM <br> <i class="text-regular-12">Medical Record Number	</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{regData.code}} / {{regData.norm}}</td>
                                    </tr>
                                    <tr>
                                        <td>NIK <br> <i class="text-regular-12">Resident ID Number</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{regData.citizen_id}}</td>
                                    </tr>
                                    <tr>
                                        <td>Nama Pasien <br> <i class="text-regular-12">Pattient Name</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{regData.name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Alamat <br> <i class="text-regular-12">Address</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{regData.address}}</td>
                                    </tr>
                                    <tr>
                                        <td>Tgl. Permintaan <br> <i class="text-regular-12">Date Request</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{orderData.date_request}}</td>
                                    </tr>
                                    <tr>
                                        <td>Tgl. Hasil <br> <i class="text-regular-12">Date Reported</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{orderData.date_result}}</td>
                                    </tr>
                                    <!-- <tr>
                                        <td>Tgl. Cetak <br> <i class="text-regular-12">Print Out Date</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{orderData.date_request}}</td>
                                    </tr> -->
                                </table>
                            </div>
                            <div class="col-6">
                                <table class="text-regular-14">
                                    <tr>
                                        <td>No. Lab <br> <i class="text-regular-12">Lab Number</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{orderData.no_request}}</td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal Lahir <br> <i class="text-regular-12">Date of Birth</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{regData.birth_date}}</td>
                                    </tr>
                                    <tr>
                                        <td>Umur <br> <i class="text-regular-12">Age</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{parsed_age(this.regData.birth_date)}}</td>
                                    </tr>
                                    <tr>
                                        <td>Kelamin <br> <i class="text-regular-12">Sex</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{regData.sex_name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Dokter Pengirim <br> <i class="text-regular-12">Reffering Doctor</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{orderData.doctor_in_charge_name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Dokter Lab <br> <i class="text-regular-12">Lab Doctor</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">dr. Andini Triasti Siregar, M.ked, Sp.PK</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <!-- /info-table -->
                        <div class="px-3 mt-32">
                            <b-table bordered responsive head-variant="light" hover small :fields="resultFields"
                                :items="resultlab.result_detail" tbody-tr-class="text-medium-14" class="mt-12 text-center">
                            </b-table>
                        </div>
                    </div>
                    <div class="row align-items-center pb-40" style="border-bottom: 2px solid black;margin-top: 3000px;" v-if="orderDataRad.no_request">
                        <div class="col-2 text-center">
                            <img src="../../../../../@core/assets/images/logo-thb-small.png" width="60%" alt="rs-logo"> <br>
                        </div>
                        <div class="col text-center">
                            <p class="text-bold-18">RS Taman Harapan Baru</p>
                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                            <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                        </div>
                        <div class="col-2 text-center">
                            <img src="../../../../../@core/assets/images/lafki-logo.png" width="60%" alt="lafki-logo"> <br>
                        </div>
                    </div>
                    <div class="mt-20" v-if="orderDataRad.no_request">
                        <p class="text-center text-bold-16">EXPERTISE RADIOLOGI</p>
                        <div class="row mx-0 mt-28">
                            <div class="col-6">
                                <table class="text-regular-14">
                                    <tr>
                                        <td class="first-cell">No. Reg / RM</td>
                                        <td class="px-3">:</td>
                                        <td>{{regData.code}} / {{regData.norm}}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Nama Pasien</td>
                                        <td class="px-3">:</td>
                                        <td>{{ regData.alias_name }} {{regData.name}}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Tanggal Order</td>
                                        <td class="px-3">:</td>
                                        <td>{{orderDataRad.date_request ? localDate(orderDataRad.date_request) : ''}}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Tanggal Hasil</td>
                                        <td class="px-3">:</td>
                                        <td>
                                        <template v-if="reqItemData[0].url">
                                            <template v-for="list in reqItemData[0].url">
                                                <template v-if="list.status !== 1"></template>
                                                {{list.date_result ? localDate(list.date_result) : ''}}
                                            </template>
                                        </template>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Alamat</td>
                                        <td class="px-3">:</td>
                                        <td>{{regData.address}}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-6">
                                <table class="text-regular-14">
                                    <tr>
                                        <td class="first-cell">No. Radiologi</td>
                                        <td class="px-3">:</td>
                                        <td>{{orderDataRad.no_request}}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Tanggal Lahir</td>
                                        <td class="px-3">:</td>
                                        <td>{{regData.birth_date ? localDate(regData.birth_date) : ''}}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Umur</td>
                                        <td class="px-3">:</td>
                                        <td>{{parsed_age(regData.birth_date)}}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Kelamin</td>
                                        <td class="px-3">:</td>
                                        <td>{{regData.sex_name}}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell"> Dokter Pengirim</td>
                                        <td class="px-3">:</td>
                                        <td>{{ regData.doctor_in_charge_name }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="mt-32 col">
                            <div>Yth.{{ regData.doctor_in_charge_name }}</div>
                            <div class="mt-12">{{ reqItemData[0].costing_name }}</div>
                            <template v-if="reqItemData[0].url">
                                <template v-for="list in reqItemData[0].url">
                                    <template v-if="list.status !== 1">
                                        <table class="w-100 custom-border">
                                            <tr>
                                                <td class="align-top" style="width: 150px;">Klinis</td>
                                                <td class="align-top text-center px-8" style="width: 30px;">:</td>
                                                <td>
                                                    <template v-if="result.icd10">
                                                        <li v-for="(item, index) in result.icd10" :key="index">{{ item.code }} {{ item.name }}</li>
                                                    </template>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top">Expertise</td>
                                                <td class="align-top text-center px-8">:</td>
                                                <td><span v-html="parsedBody(list.conclusion ? list.conclusion : '-')"></span></td>
                                            </tr>
                                            <tr class="font-weight-bold">
                                                <td class="align-top pt-32">Kesan</td>
                                                <td class="align-top text-center px-8 pt-32">:</td>
                                                <td class="pt-32"><span v-html="parsedBody(list.advice ? list.advice : '-')"></span></td>
                                            </tr>
                                        </table>
                                        <div class="mt-32 d-flex justify-content-end text-center">
                                            <div>
                                                <div>Bekasi, {{ localDate(list.date_result) }}</div>
                                                <div :class="list.ttd_name ? '' : 'mb-72'">Salam Sejawat</div>
                                                <b-img :src="urlbe+list.ttd_name" style="width: 220px;" />
                                                <!-- <div>{{ list.ttd_name }}</div> -->
                                                <div>{{ list.doctor_lab_name }}</div>
                                            </div>
                                        </div>
                                    </template>
                                </template>
                            </template>
                        </div>
                        <!-- <template v-if="reqItemData?.length > 0">
                            <div v-for="unit in reqItemData" class="mt-32 col">
                                <div>Yth.{{ regData.doctor_in_charge_name }}</div>
                                <div class="mt-12">{{ unit.costing_name }}</div>
                                <template v-if="unit.url">
                                    <template v-for="list in unit.url">
                                        <table class="w-100 custom-border">
                                            <tr>
                                                <td class="align-top" style="width: 150px;">Klinis</td>
                                                <td class="align-top text-center px-8" style="width: 30px;">:</td>
                                                <td>
                                                    <template v-if="assessmentData && assessmentData.icd10">
                                                        <li v-for="(item, index) in assessmentData.icd10" :key="index">{{ item.code }} {{ item.name }}</li>
                                                    </template>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top">Expertise</td>
                                                <td class="align-top text-center px-8">:</td>
                                                <td><span v-html="parsedBody(list.conclusion ? list.conclusion : '-')"></span></td>
                                            </tr>
                                            <tr class="font-weight-bold">
                                                <td class="align-top pt-32">Kesan</td>
                                                <td class="align-top text-center px-8 pt-32">:</td>
                                                <td class="pt-32"><span v-html="parsedBody(list.advice ? list.advice : '-')"></span></td>
                                            </tr>
                                        </table>
                                    </template>
                                </template>
                                <div class="mt-32 d-flex justify-content-end text-center">
                                    <div>
                                        <div>Bekasi, {{ today }}</div>
                                        <div :class="urlData.ttd_name ? '' : 'mb-72'">Salam Sejawat</div>
                                        <b-img :src="urlbe+urlData.ttd_name" style="width: 220px;" />
                                        <div>{{ urlData.doctor_lab_name }}</div>
                                    </div>
                                </div>
                            </div>
                        </template> -->
                    </div>
                </div>
            </div>
        </b-container>
    </div>
</template>
<script>
import moment from 'moment';
import QrcodeVue from 'qrcode.vue';

export default {
    components: {
        QrcodeVue
    },
    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),
            dateNow: null,
            data:{},
            RecipeTable: {
                fields: [
                    { key: 'criteria', label: 'Kriteria Pengkajian' },
                    { key: 'yes', label: 'Ya', thClass: 'text-center' },
                    { key: 'no', label: 'Tidak', thClass: 'text-center' },
                ],
                items: [
                    { criteria: 'SYARAT ADMINISTRASI', yes: '', no: '' },
                    { criteria: 'SEP', yes: true, no: '' },
                    { criteria: 'Identitas Pasien (Nama, Domisili, Tgl Lahir)', yes: true, no: '' },
                    { criteria: 'Paraf Dokter', yes: true, no: '' },
                    { criteria: 'ASPEK FARMASTETIK', yes: '', no: '' },
                    { criteria: 'Nama, Bentuk, Kekuatan', yes: true, no: '' },
                    { criteria: 'Jumlah Obat', yes: true, no: '' },
                    { criteria: 'Signa / Aturan Pakai', yes: true, no: '' },
                    { criteria: 'ASPEK KLINIS', yes: '', no: '' },
                    { criteria: 'Tepat Obat', yes: true, no: '' },
                    { criteria: 'Tepat Indikasi', yes: true, no: '' },
                    { criteria: 'Tepat Dosis', yes: true, no: '' },
                    { criteria: 'Tepat Rute', yes: true, no: '' },
                    { criteria: 'Tepat Waktu', yes: true, no: '' },
                    { criteria: 'Tidak Duplikasi Terapi', yes: true, no: '' },
                    { criteria: 'Tidak Ada Alergi obat & ROTD', yes: true, no: '' },
                    { criteria: 'Tidak Ada interaksi Obat', yes: true, no: '' },
                    { criteria: 'Tidak Ada Kontra Indikasi', yes: true, no: '' },
                ]
            },
            DrugTable: {
                fields: [
                    { key: 'criteria', label: 'Kriteria Pengkajian' },
                    { key: 'yes', label: 'Ya', thClass: 'text-center' },
                    { key: 'no', label: 'Tidak', thClass: 'text-center' },
                ],
                items: [
                    { criteria: 'SYARAT ADMINISTRASI', yes: '', no: '' },
                    { criteria: 'Nama Pasien', yes: true, no: '' },
                    { criteria: 'Nama Obat', yes: true, no: '' },
                    { criteria: 'Dosis, Bentuk, Kekuatan Sediaan', yes: true, no: '' },
                    { criteria: 'Tepat ED', yes: true, no: '' },
                    { criteria: 'Rute Pemberian', yes: true, no: '' },
                    { criteria: 'Waktu Frekuensi Aturan Pakai', yes: true, no: '' },
                ]
            },

            SignTable: {
                fields: [
                    { key: 'criteria', label: 'Kriteria' },
                    { key: 'sign', label: 'Paraf' },
                ],
                items: [
                    { criteria: 'Penerimaan', sign: '' },
                    { criteria: 'Penyiapan Obat Jadi', sign: '' },
                    { criteria: 'Penyiapan Racikan', sign: '' },
                    { criteria: 'Penyerahan', sign: '' },
                ]
            },
            urlbe:process.env.VUE_APP_IMAGE,
            resultlab: {},
            patient: {},
            invoices:{},
            date_now:moment(new Date()).locale('id').format('LL'),
            DPJP_doctor: {
                laboratory_list: [],
                radiology_list: [],
                prescription_list: [],
            },
            FIRST_doctor: {
                laboratory_list: [],
                radiology_list: [],
                prescription_list: [],
            },
            SECOND_doctor: {
                laboratory_list: [],
                radiology_list: [],
                prescription_list: [],
            },
            form: {
                exit_type_id: null
            },
            regData:{},
            pesertaData: {},
            globalQRText: '',
            regIns:{},
            regDataInv:{},
            orderData:{},
            urlData:{},
            orderDataRad:{},
            reqItemData:{},

            resultFields: [
                { key: 'Parameter', label: 'Parameter' },
                { key: 'hasil', label: 'Hasil' },
                { key: 'flag', label: 'Flag' },
                { key: 'n_rujukan', label: 'Nilai Rujukan' },
                { key: 'satuan', label: 'Satuan' },
                { key: 'metoda', label: 'Metoda' },
            ],

            trans_1: [],
            trans_2: [],
            trans_5: [],
            trans_10: [],
            trans_11: [],
            trans_12: [],
            trans_13: [],
            trans_14: [],
            trans_15: [],

            trans_1_total: 0,
            trans_2_total: 0,
            trans_5_total: 0,
            trans_10_total: 0,
            trans_11_total: 0,
            trans_12_total: 0,
            trans_13_total: 0,
            trans_14_total: 0,
            trans_15_total: 0,
            today: moment(new Date()).format('DD-MM-YYYY')
        }
    },
    mounted() {
        this.$store.dispatch('soap/getByRegId', this.$route.params.soap_id).then(data => {
            this.getData(data[0].id)
            window.addEventListener('keydown', this.handleKeyPress);
        })
    },
    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },
    filters:{
        currency(value) {
            return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'IDR' }).format(value)
        }
    },
    methods: {
        localDate(input_date) {
            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },
        localDateTime(input) {
            return moment(input).locale("id").format('DD MMMM YYYY HH:MM')
        },
        getFormattedDateTime() {
            const now = new Date();

            const monthNames = [
                "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                "Juli", "Agustus", "September", "Oktober", "November", "Desember"
            ];

            const day = now.getDate();
            const month = monthNames[now.getMonth()];
            const year = now.getFullYear();

            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');

            return `${day} ${month} ${year} ${hours}:${minutes}`;
        },
        async getDataBpjs(id) {
            try {
                const response = await this.$store.dispatch('patient/bpjs', {url:'Sep',idx:id})
                this.pesertaData = response.data.response
                this.globalQRText = this.pesertaData.peserta.noKartu
                // console.log(this.pesertaData)
                // this.generateToImage()
            } catch (error) {
                this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
            }
        },
        getExitType(){
            const r_jalan = '3cb626f5-9148-4fb1-8663-3d7f028b01a6'
            this.form.exit_type_id = 0
            if (this.patient.health_facility_id == r_jalan) {
                this.form.exit_type_id = 1
            } else {
                if (this.patient.exit_id == '4a6d28c4-e020-425f-91e1-d39699cf679b') {
                    this.form.exit_type_id = 6
                } else if (this.patient.exit_id == 'a1631acc-6a94-414d-95de-a9faea0f1c2f'){
                    this.form.exit_type_id = 4
                } else{
                    this.form.exit_type_id = 0 // sementara, krn exit_id nya null semua
                }
                // exit:[
                //     { value: '807ca005-5827-4e19-9c97-0bc3d4e924ca', text: 'Atas Persetujuan Dokter' },
                //     { value: '4a6d28c4-e020-425f-91e1-d39699cf679b', text: 'Meninggal' },
                //     { value: 'a1631acc-6a94-414d-95de-a9faea0f1c2f', text: 'Dirujuk' },
                //     { value: 'ae0aabfa-1600-443f-8a60-62f9e76b1608', text: 'Permintaan Sendiri' }
                // ],
            }
        },
        parsedBody(text){
            return String(text || '').replace(/\n/g, "<br>");
        },
        localDate(input_date) {
            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },
        localDateRanap(input_date) {
            return moment(input_date).locale("id").format('LLLL')
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
        getDataRad (id) {
			this.$store.dispatch('radiology/getByIdOrder', id).then(data => {
                this.orderDataRad = data[0]
                this.reqItemData = this.orderDataRad?.radiology_request_item
                // this.reqItemData = req_item.filter((item) => item.status !== 1)
            })
        },
        getDataInv (id) {
            this.$store.dispatch('payment/getByAllInvoice', id).then(data => {
                this.invoices = data[0]
                this.mappingArray(this.invoices.transactions)
                this.$store.dispatch('registration/getByRegistrationId', data[0].registration_id).then(data => {
                    this.regDataInv = data[0]
                    // this.show = false
                })
            })
        },
        mappingArray(transactions) {
            if (transactions) {
                this.trans_1_total = 0
                this.trans_2_total = 0
                this.trans_5_total = 0
                this.trans_10_total = 0
                this.trans_11_total = 0
                this.trans_12_total = 0
                this.trans_13_total = 0
                this.trans_14_total = 0
                this.trans_15_total = 0

                for (let index = 0; index < transactions.length; index++) {
                    const element = transactions[index];

                    if (element.type_transaction_id === 1) {
                        let price = element.var_1 != '' ? element.var_1 : 0
                        let qty = element.var_2 != '' ? element.var_2 : 0

                        this.trans_1.push(element)
                        this.trans_1_total += (price*qty)
                    }

                    if (element.type_transaction_id === 2) {
                        let discount = element.var_4 != '' ? element.var_4 : 0
                        let price = element.var_1 != '' ? element.var_1 : 0
                        let qty = element.var_2 != '' ? element.var_2 : 0
                        let temp_total = (price*qty) - discount

                        this.trans_2.push({ ...element, count_total: temp_total })
                        this.trans_2_total += temp_total
                    }

                    if (element.type_transaction_id === 5) {
                        this.trans_5.push(element)
                        this.trans_5_total += element.var_1
                    }

                    if (element.type_transaction_id === 10) {
                        let price = element.var_1 != '' ? element.var_1 : 0
                        let qty = element.var_2 != '' ? element.var_2 : 0

                        this.trans_10.push(element)
                        this.trans_10_total += (price*qty)
                    }

                    if (element.type_transaction_id === 11) {
                        this.trans_11.push(element)
                        this.trans_11_total += element.var_1
                    }

                    if (element.type_transaction_id === 12) {
                        this.trans_12.push(element)
                        this.trans_12_total += element.var_1
                    }

                    if (element.type_transaction_id === 13) {
                        let discount = element.var_4 != '' ? element.var_4 : 0
                        let price = element.var_1 != '' ? element.var_1 : 0
                        let temp_total = price - discount

                        this.trans_13.push({ ...element, count_total: temp_total })
                        this.trans_13_total += temp_total
                    }

                    if (element.type_transaction_id === 14) {
                        this.trans_14.push(element)
                    }

                    if (element.type_transaction_id === 15) {
                        this.trans_15.push(element)
                        this.trans_15_total += element.var_1
                    }
                }
                this.getTotalPrec(this.trans_14)
                // console.log('this.trans_14: ', this.trans_14);

            }
        },
        getTotalPrec(param) {
            if (param.length > 0) {
                let temp_total = 0
                for (let index = 0; index < param.length; index++) {
                    const element = param[index].prescription_item;

                    if (element) {
                        for (let j = 0; j < element.length; j++) {
                            const data = element[j];

                            if (data.is_retur_full === 0 || data.pharmacy_catalog_id_retur) {
                                let tmp_price = data.var_1 ? data.var_1 : 0
                                let tmp_qty = data.qty ? data.qty : 0
                                // let tmp_qty_retur = data.qty_retur != '' || data.qty_retur != null ? data.qty_retur : 0
                                let tmp_qty_retur = data.is_retur_full === 1 && data.pharmacy_catalog_id_retur || data.pharmacy_catalog_id_ref ? 0 : data.qty_retur
                                let final_qty = tmp_qty - tmp_qty_retur

                                temp_total += (tmp_price*final_qty)
                            }
                        }
                    }
                }
                this.trans_14_total = temp_total
                // console.log('temp_total: ', temp_total);
            } else {
                this.trans_14_total = 0
            }
        },
        getExitType(){
            const r_jalan = '3cb626f5-9148-4fb1-8663-3d7f028b01a6'
            this.form.exit_type_id = 0
            if (this.patient.health_facility_id == r_jalan) {
                this.form.exit_type_id = 1
            } else {
                if (this.patient.exit_id == '4a6d28c4-e020-425f-91e1-d39699cf679b') {
                    this.form.exit_type_id = 6
                } else if (this.patient.exit_id == 'a1631acc-6a94-414d-95de-a9faea0f1c2f'){
                    this.form.exit_type_id = 4
                } else{
                    this.form.exit_type_id = 0 // sementara, krn exit_id nya null semua
                }
                // exit:[
                //     { value: '807ca005-5827-4e19-9c97-0bc3d4e924ca', text: 'Atas Persetujuan Dokter' },
                //     { value: '4a6d28c4-e020-425f-91e1-d39699cf679b', text: 'Meninggal' },
                //     { value: 'a1631acc-6a94-414d-95de-a9faea0f1c2f', text: 'Dirujuk' },
                //     { value: 'ae0aabfa-1600-443f-8a60-62f9e76b1608', text: 'Permintaan Sendiri' }
                // ],
            }
        },
        handleKeyPress(event) {
            if (event.ctrlKey && event.key === 'p') {
                event.preventDefault();
                this.printCanvas();
            }
        },
        closeWindow() {
            window.close()
        },
        localDate(input_date) {
            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },
        generateToImage() {
            const canvas = document.querySelector("#qrcodeCanvas canvas");
            if (canvas) {
                const dataURL = canvas.toDataURL();
                console.log('dataURL: ', dataURL);

                const renderQRDiv = document.getElementById("renderQR");

                const img = document.createElement("img");
                img.src = dataURL;
                img.alt = "Generated QR Code";
                img.style.width = "70px";
                canvas.style.display = "none"

                renderQRDiv.appendChild(img);
                setTimeout(() => {
                    this.printCanvas()
                }, 500);
            } else {
                console.error("Canvas not found!");
            }
            const canvasd = document.querySelector("#qrcodeCanvasd canvas");
            if (canvasd) {
                const dataURL = canvasd.toDataURL();
                console.log('dataURL: ', dataURL);

                const renderQRdDiv = document.getElementById("renderdQR");

                const img = document.createElement("img");
                img.src = dataURL;
                img.alt = "Generated QR Code";
                img.style.width = "70px";
                canvasd.style.display = "none"

                renderQRdDiv.appendChild(img);
                setTimeout(() => {
                    this.printCanvas()
                }, 500);
            } else {
                console.error("Canvas not found!");
            }
            const canvast = document.querySelector("#qrcodeCanvast canvas");
            if (canvast) {
                const dataURL = canvast.toDataURL();
                console.log('dataURL: ', dataURL);

                const renderQRdDiv = document.getElementById("rendertQR");

                const img = document.createElement("img");
                img.src = dataURL;
                img.alt = "Generated QR Code";
                img.style.width = "70px";
                canvast.style.display = "none"

                renderQRdDiv.appendChild(img);
                setTimeout(() => {
                    this.printCanvas()
                }, 500);
            } else {
                console.error("Canvas not found!");
            }
        },
        printCanvas() {
            const canvas = this.$refs.canvas;
            const originalContents = document.body.innerHTML;
            document.body.innerHTML = canvas.innerHTML;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload()
        },
        getData (soap_id) {
            this.$store.dispatch('soap/getBySoapId', soap_id).then(data => {
                this.result = data[0]
                this.$store.dispatch('prescription/getById', data[0].prescription_new[0].form_prescription[0].pharmacy_prescription_id).then(datar => {
                    this.data = datar[0]
                    console.log('Data: ', this.data);

                    // this.$store.dispatch('registration/getById', this.data.registration_destination_id).then(data => {
                    //     this.patient = data[0]
                    //     console.log('patient', this.patient);
                    // })
                })
                this.getDataReg(this.result.registration_destination_id)
                console.log('soap:', this.result)
                if (this.result && this.result.doctor_in_charge_id) {
                    let DPJP_param = {soap_id:this.result.id, doctor_in_charge_id:this.result.doctor_in_charge_id}
                    this.getDPJPStudies(DPJP_param)
                }

                if (this.result && this.result.doctor_in_charge_id_1) {
                    let FIRST_param = {soap_id:this.result.id, doctor_in_charge_id:this.result.doctor_in_charge_id_1}
                    this.getFIRSTStudies(FIRST_param)
                }

                if (this.result && this.result.doctor_in_charge_id_2) {
                    let SECOND_param = {soap_id:this.result.id, doctor_in_charge_id:this.result.doctor_in_charge_id_2}
                    this.getSECONDStudies(SECOND_param)
                }

                this.$store.dispatch('registration/getById', this.result.registration_destination_id).then(data => {
                    this.patient = data[0]
                    this.getDataInv(this.patient.registration_id)
                    console.log('patient', this.patient);
                })
                if(data[0].laboratory_request){
                    this.getDataLab(data[0].laboratory_request.id)
                }
                if(data[0].radiology_request){
                    this.getDataRad(data[0].radiology_request.id)
                }
            })
        },
        getDataLab (id) {
			this.$store.dispatch('laboratory/getByIdOrder', id).then(data => {
                this.orderData = data[0]
                this.regData = data[0].regData
                this.$store.dispatch('laboratory/getResultById', id).then(result => {
                    this.resultlab = result[0]
                    console.log('this.result', this.result);
                })
            })
        },
        getDataReg (id) {
			this.$store.dispatch('registration/getById', id).then(data => {
                console.log(data[0])
                this.getDataBpjs(data[0].registration_insurance[0].sep)
                this.regData = data[0]
            })
        },

        getDPJPStudies(param) {
            try {

                this.$store.dispatch('laboratory/getOrderDetail', param).then((response) => {
                    this.DPJP_doctor.laboratory_list = response
                    // console.log('this.DPJP_doctor.laboratory_list: ', this.DPJP_doctor.laboratory_list);
                })

                this.$store.dispatch('radiology/getOrderDetail', param).then((response) => {
                    this.DPJP_doctor.radiology_list = response
                    // console.log('this.DPJP_doctor.radiology_list: ', this.DPJP_doctor.radiology_list);
                })

                this.$store.dispatch('prescription/getDetail', param).then((response) => {
                    this.DPJP_doctor.prescription_list = response
                    // console.log('this.DPJP_doctor.prescription_list: ', this.DPJP_doctor.prescription_list);
                })

            } catch (error) {
                console.error('Error at: ', error);
            }
        },

        getFIRSTStudies(param) {
            try {
                this.$store.dispatch('laboratory/getOrderDetail', param).then((response) => {
                    this.FIRST_doctor.laboratory_list = response
                    // console.log('this.FIRST_doctor.laboratory_list: ', this.FIRST_doctor.laboratory_list);
                })

                this.$store.dispatch('radiology/getOrderDetail', param).then((response) => {
                    this.FIRST_doctor.radiology_list = response
                    // console.log('this.FIRST_doctor.radiology_list: ', this.FIRST_doctor.radiology_list);
                })

                this.$store.dispatch('prescription/getDetail', param).then((response) => {
                    this.FIRST_doctor.prescription_list = response
                    // console.log('this.FIRST_doctor.prescription_list: ', this.FIRST_doctor.prescription_list);
                })

            } catch (error) {
                console.error('Error at: ', error);
            }
        },

        getSECONDStudies(param) {
            try {
                this.$store.dispatch('laboratory/getOrderDetail', param).then((response) => {
                    this.SECOND_doctor.laboratory_list = response
                    // console.log('this.SECOND_doctor.laboratory_list: ', this.SECOND_doctor.laboratory_list);
                })

                this.$store.dispatch('radiology/getOrderDetail', param).then((response) => {
                    this.SECOND_doctor.radiology_list = response
                    // console.log('this.SECOND_doctor.radiology_list: ', this.SECOND_doctor.radiology_list);
                })

                this.$store.dispatch('prescription/getDetail', param).then((response) => {
                    this.SECOND_doctor.prescription_list = response
                    // console.log('this.SECOND_doctor.prescription_list: ', this.SECOND_doctor.prescription_list);
                })

            } catch (error) {
                console.error('Error at: ', error);
            }
        },
         // getFamilyRelation(id) {
        //     let data = ''
        //     if (id == 1) {
        //         return data = 'Ayah'

        //     } else if (id == 2) {
        //         return data = 'Ibu'

        //     } else if (id == 3) {
        //         return data = 'Kakak'

        //     } else if (id == 4) {
        //         return data = 'Adik'

        //     } else {
        //         return data = '-'

        //     }
        // },
    },

};

</script>
<style>
@import './../../../../../@core/css/main/main.min.css';

@media print {
    @page {
        size: A4;
        margin: 16mm;
    }

    .sbpk table, .sbpk th, .sbpk td {
        border: 2px solid black;
        border-collapse: collapse;
    }
}
.sbpk table, .sbpk th, .sbpk td {
    /* border: 0; */
  border: 2px solid black;
  border-collapse: collapse;
}
td {
    padding: 2px 0;
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
    padding-bottom: 40px;
}
/* #canvas {
    width: 21.2cm !important;
    height: 100% !important;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    padding: 40px;
} */

.with-line {
    text-decoration: underline;
    text-decoration-thickness: 2px;
    text-underline-offset: 8px;
}

.w-40 {
    width: 40%;
}

.canvas-footer {
    position: absolute;
    bottom: 0;
}


.cmain td, .cmain th {
    border: 1px solid #dee2e6;
    padding: 5px;
}
</style>
