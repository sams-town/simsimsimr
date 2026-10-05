<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="generateToImage" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper mt-16">
                <div id="canvas" ref="canvas" style="position: relative;">


                    <div class="row align-items-center pb-40" style="border-bottom: 2px solid black;display:none">
                        <div class="col-2 text-center">
                            <img src="../../../../../@core/assets/images/kemenkes.png" width="60%" alt="rs-logo"> <br>
                        </div>
                        <div class="col text-left">
                            <p class="text-bold-18">KEMENTERIAN KESEHATAN REBULIK INDONESIA</p>
                            <i><p>Berkas Klaim Individual Pasien</p></i>
                            <!-- <p>Info: +62 778 4161212 / 0853 7111 1212</p>
                             -->
                        </div>
                        <div class="col-2 text-right">
                            <p>JKN</p>
                            <p>{{localDateEklaim(tgl_masuk(regData.register_date))}}</p>
                        </div>
                    </div>


                    <div class="canvas-body mt-32 px-40" style="display:none">
                        <section class="chead">
                            <div class="row mx-0 mt-28">
                                <div class="col-6">
                                    <table>
                                        <tr>
                                            <td class="first-cell">Kode Rumah Sakit</td>
                                            <td class="px-3">:</td>
                                            <td>2171065</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Nama Rumah Sakit</td>
                                            <td class="px-3">:</td>
                                            <td>RS Taman Harapan Baru Bekasi</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-6">
                                    <table>
                                        <tr></tr>
                                        <tr>
                                            <td class="first-cell">Kelas Rumah Sakit</td>
                                            <td class="px-3">:</td>
                                            <td>B</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Jenis Tarif</td>
                                            <td class="px-3">:</td>
                                            <td>TARIF RS KELAS B SWASTA</td>
                                        </tr>
                                    </table>
                                </div>
                            </div><hr>
                            <div class="row mx-0 mt-28">
                                <div class="col-6">
                                    <table>
                                        <tr>
                                            <td class="first-cell">Nomor Peserta</td>
                                            <td class="px-3">:</td>
                                            <td>{{ pesertaData?.peserta ? pesertaData?.peserta.noKartu : '' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Nomer Rekam Medis</td>
                                            <td class="px-3">:</td>
                                            <td>{{ regData.norm }}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Umur Ulang Tahun</td>
                                            <td class="px-3">:</td>
                                            <td>{{parsed_age_year(regData.birth_date)}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Umur Hari</td>
                                            <td class="px-3">:</td>
                                            <td>{{parsed_age_day(regData.birth_date)}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Tanggal Lahir</td>
                                            <td class="px-3">:</td>
                                            <td>{{localDateEklaim(regData.birth_date)}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Jenis Kelamin</td>
                                            <td class="px-3">:</td>
                                            <td>{{ this.patient.sex === 0 ? '1' : this.patient.sex }} - {{ this.patient.sex_name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Kelas Keperawatan</td>
                                            <td class="px-3">:</td>
                                            <td>Regular</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-6">
                                    <table>
                                        <tr></tr>
                                        <tr>
                                            <td class="first-cell">Nomor SEP</td>
                                            <td class="px-3">:</td>
                                            <td>{{pesertaData?.noSep}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Tanggal Masuk</td>
                                            <td class="px-3">:</td>
                                            <td>{{localDateEklaim(tgl_masuk(regData.register_date))}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Tanggal Keluar</td>
                                            <td class="px-3">:</td>
                                            <td>{{ localDateEklaim(regData.checkout_date ? regData.checkout_date : tgl_masuk(regData.register_date)) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Jenis Perawatan</td>
                                            <td class="px-3">:</td>
                                            <td>{{ regData.health_facility_id === '3cb626f5-9148-4fb1-8663-3d7f028b01a6' || '444b3134-5b75-4b67-af72-a558d46716d7' ? '1 - Rawat Jalan' : '2 - Rawat Inap' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Cara Pulang</td>
                                            <td class="px-3">:</td>
                                            <td>{{ cek_status(regData.exit_name) }} - {{ regData.exit_name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">LOS</td>
                                            <td class="px-3">:</td>
                                            <td>{{ regData.health_facility_id === '3cb626f5-9148-4fb1-8663-3d7f028b01a6' || '444b3134-5b75-4b67-af72-a558d46716d7' ? '1 hari' : los(regData.checkout_date ? regData.checkout_date : tgl_masuk(regData.register_date)) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Berat Lahir</td>
                                            <td class="px-3">:</td>
                                            <td>-</td>
                                        </tr>
                                    </table>
                                </div>
                            </div><hr>
                            <div class="row mx-0 mt-28">
                                <div class="col-8">
                                    <table>
                                        <tr>
                                            <td class="first-cell">Diagnosa Utama</td>
                                            <td class="px-3">:</td>
                                            <td>
                                                <table>
                                                    <template v-for="(item,index) in result.icd10">
                                                        <tr>
                                                            <template v-if="index == 0">
                                                                <td>{{ item.code }}</td>
                                                                <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                                                <td>{{ item.name }}</td>
                                                            </template>
                                                        </tr>
                                                    </template>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Diagnosa Sekunder</td>
                                            <td class="px-3">:</td>
                                            <td>
                                                <table>
                                                    <template v-for="(item,index) in result.icd10">
                                                        <tr>
                                                            <template v-if="index != 0">
                                                                <td>{{ item.code }}</td>
                                                                <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                                                <td>{{ item.name }}</td>
                                                            </template>
                                                        </tr>
                                                    </template>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <div class="row mx-0 mt-28 mb-28"></div>
                                                <div class="row mx-0 mt-28 mb-28"></div>
                                                <div class="row mx-0 mt-28 mb-28"></div>
                                                <div class="row mx-0 mt-28 mb-28"></div>
                                                <div class="row mx-0 mt-28 mb-28"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Prosedur</td>
                                            <td class="px-3">:</td>
                                            <td>
                                                <table>
                                                    <template v-for="(item,index) in result.icd9cm">
                                                        <tr>
                                                            <template>
                                                                <td>{{ item.code }}</td>
                                                                <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                                                <td>{{ item.name }}</td>
                                                            </template>
                                                        </tr>
                                                    </template>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <div class="row mx-0 mt-28 mb-28"></div>
                                                <div class="row mx-0 mt-28 mb-28"></div>
                                                <div class="row mx-0 mt-28 mb-28"></div>
                                                <div class="row mx-0 mt-28 mb-28"></div>
                                                <div class="row mx-0 mt-28 mb-28"></div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="row mx-0 mt-28">
                                <div class="col-6">
                                    <table>
                                        <tr>
                                            <td class="first-cell">ADL Sub Acute</td>
                                            <td class="px-3">:</td>
                                            <td>-</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-6">
                                    <table>
                                        <tr>
                                            <td class="first-cell">ADL Chronic</td>
                                            <td class="px-3">:</td>
                                            <td>-</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-12">
                                    <table>
                                        <tr>
                                            <td>
                                                <div class="row mx-0 mt-28 mb-28"></div>
                                                <div class="row mx-0 mt-28 mb-28"></div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-6">
                                    <table>
                                        <tr>
                                            <td class="first-cell"><b>Hasil Grouping</b></td>
                                        </tr>
                                    </table>
                                </div>
                            </div><hr>
                            <div class="row mx-0 mt-28">
                                <div class="col-12">
                                    <table>
                                        <tr>
                                            <td width="10%" class="first-cell">INA-CBG</td>
                                            <td width="5%"class="px-3">:</td>
                                            <td width="15%">{{ eklaim.json_obj !== null ? eklaim.json_obj.response.cbg.code : '' }}</td>
                                            <td width="65%">{{ eklaim.json_obj !== null ? eklaim.json_obj.response.cbg.description : '' }}</td>
                                            <td width="5%">Rp</td>
                                            <td class="text-right" width="20%">{{ eklaim.json_obj !== null ? (eklaim.dializer_single_use == 0 ? eklaim.json_obj.response.cbg.base_tariff : eklaim.json_obj.response.cbg.base_tariff) : 0 | currencys }}.00</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Sub Acute</td>
                                            <td width="5%"class="px-3">:</td>
                                            <td width="15%">-</td>
                                            <td width="65%">-</td>
                                            <td width="5%">Rp</td>
                                            <td class="text-right" width="20%">{{ 0 | currencys }}.00</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Chronic</td>
                                            <td width="5%"class="px-3">:</td>
                                            <td width="15%">-</td>
                                            <td width="65%">-</td>
                                            <td width="5%">Rp</td>
                                            <td class="text-right" width="20%">{{ 0 | currencys }}.00</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Special CMG</td>
                                            <td width="5%"class="px-3">:</td>
                                            <td width="15%">{{ eklaim.json_obj !== null ? eklaim.json_obj.response.special_cmg ? eklaim.json_obj.response.special_cmg[0].code : '' : '-' }}</td>
                                            <td width="65%">{{ eklaim.json_obj !== null ? eklaim.json_obj.response.special_cmg ? eklaim.json_obj.response.special_cmg[0].description : '' : '-' }}</td>
                                            <td width="5%">Rp</td>
                                            <td class="text-right" width="20%">{{ eklaim.json_obj !== null ? eklaim.json_obj.response.special_cmg ? (eklaim.json_obj.response.special_cmg && eklaim.dializer_single_use == 0 ? eklaim.json_obj.response.special_cmg[0].tariff : eklaim.json_obj.response.special_cmg[0].base_tariff) : 0 : 0 | currencys }}.00</td>
                                        </tr>
                                    </table>
                                </div>
                            </div><hr>
                            <div class="row mx-0 mt-28">
                                <div class="col-12">
                                    <table>
                                        <tr>
                                            <td width="10%" class="first-cell">Total Tarif</td>
                                            <td width="5%"class="px-3">:</td>
                                            <td width="15%"></td>
                                            <td width="65%"></td>
                                            <td width="5%">Rp</td>
                                            <td class="text-right" width="20%">{{ eklaim.json_obj !== null ? eklaim.json_obj.response.special_cmg ? (eklaim.dializer_single_use == 0 ? eklaim.json_obj.response.cbg.tariff : eklaim.json_obj.response.cbg.tariff) : eklaim.json_obj.response.cbg.base_tariff : 0 | currencys }}.00</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div style="margin-top: 200px;">
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
                                            <td>{{pesertaData?.noSep}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Tgl. SEP	</td>
                                            <td class="px-3">:</td>
                                            <td>{{pesertaData?.tglSep}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">No. Kartu</td>
                                            <td class="px-3">:</td>
                                            <td>{{ pesertaData?.peserta ? pesertaData?.peserta.noKartu : '' }} <span class="ml-16">MR. {{ pesertaData?.peserta ? pesertaData?.peserta.noMr : '' }}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Nama Peserta</td>
                                            <td class="px-3">:</td>
                                            <td class="text-uppercase">{{pesertaData?.peserta ? pesertaData?.peserta.nama : '' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Tgl.Lahir</td>
                                            <td class="px-3">:</td>
                                            <td>{{ pesertaData?.peserta ? pesertaData?.peserta.tglLahir : '' }} Kelamin {{ pesertaData?.peserta ? pesertaData?.peserta.kelamin : '' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">No.Telepon</td>
                                            <td class="px-3">:</td>
                                            <td>{{regData.phone}}</td>
                                        </tr>
                                        <tr v-if="pesertaData?.poli">
                                            <td class="first-cell">Sub/Spesialis</td>
                                            <td class="px-3">:</td>
                                            <td>{{pesertaData?.poli}}</td>
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
                                                <template v-if="pesertaData?.kontrol.nmDPJP">
                                                    {{pesertaData?.dpjp?.nmDPJP}}
                                                </template>
                                                <template v-if="pesertaData?.kontrol.noSurat">
                                                    {{pesertaData?.kontrol.nmDokter}}
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
                                            <td>{{pesertaData?.diagnosa}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Catatan</td>
                                            <td class="px-3">:</td>
                                            <td>{{pesertaData?.catatan}}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-6">
                                    <table>
                                        <tr>
                                            <td class="first-cell">Peserta</td>
                                            <td class="px-3">:</td>
                                            <td class="text-uppercase">{{ pesertaData?.peserta ? pesertaData?.peserta.jnsPeserta : '' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">COB</td>
                                            <td class="px-3">:</td>
                                            <td>{{pesertaData?.cob == "0" ? "Tidak" : "Ya" }}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell" style="vertical-align:top;">Jns.Kunjungan</td>
                                            <td class="px-3" style="vertical-align:top;">:</td>
                                            <td style="vertical-align:top;">
                                                <li>{{ pesertaData?.tujuanKunj ? pesertaData?.tujuanKunj.nama : '' }}</li>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Jns.Rawat</td>
                                            <td class="px-3">:</td>
                                            <td>{{pesertaData?.jnsPelayanan}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Kls.Hak</td>
                                            <td class="px-3">:</td>
                                            <td>{{ pesertaData?.peserta ? pesertaData?.peserta.hakKelas : '' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Kls.Rawat</td>
                                            <td class="px-3">:</td>
                                            <td>{{ pesertaData?.peserta ? pesertaData?.kelasRawat : '' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Penjamin</td>
                                            <td class="px-3">:</td>
                                            <td>{{ pesertaData?.penjamin }}</td>
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
                                        <div>{{ pesertaData?.peserta ? pesertaData?.peserta.nama : '' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top: 50px;">
                        <div class="d-flex justify-content-between w-100 px-3 mt-8">
                            <div class="d-flex align-items-center">
                                <img src="@/@core/assets/images/bpjs/bpjs_logo.png" height="40px" alt="rs-logo"> <br>
                                <div class="font-weight-bold ml-24">
                                    <div>{{ pesertaData?.jnsPelayanan === 'Rawat Inap' ? 'SURAT RENCANA RAWAT INAP' : 'SURAT RENCANA KONTROL' }}</div>
                                    <div>RS Taman Harapan Baru</div>
                                </div>
                            </div>
                            <div class="font-weight-bold">{{ pesertaData?.kontrol?.noSurat }}</div>
                        </div>
                        <div class="mt-24">
                            <div class="col-12">
                                <table class="text-regular-14">
                                    <tr>
                                        <td class="first-cell" style="vertical-align: top;">Kepada Yth</td>
                                        <td class="px-3" style="vertical-align: top;">:</td>
                                        <td>
                                            <div>{{regData.doctor_in_charge_name}}</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="pt-3">
                                            <div>Mohon Pemeriksaan Lebih Lanjut :</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">No. Kartu</td>
                                        <td class="px-3">:</td>
                                        <td>
                                            {{pesertaData?.peserta ? pesertaData?.peserta.noKartu : ''}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Nama Peserta</td>
                                        <td class="px-3">:</td>
                                        <td class="text-uppercase">
                                            {{regData.name}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Tgl.Lahir</td>
                                        <td class="px-3">:</td>
                                        <td>
                                            {{regData.birth_date}} ({{regData.sex_name}})
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Diagnosa Awal</td>
                                        <td class="px-3">:</td>
                                        <td>{{pesertaData?.diagnosa}}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">{{pesertaData?.jnsPelayanan === 'Rawat Inap' ? 'Rencana Inap' : 'Rencana Kontrol'}}</td>
                                        <td class="px-3">:</td>
                                        <td>{{pesertaData?.tglSep}}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="ml-3 mt-16 row text-regular-14">Demikian atas bantuannya, diucapkan banyak terima kasih.</div>
                            <div class="d-flex justify-content-end px-3 mt-16">
                                <div class="col-4">
                                    <div class="text-center text-regular-14">
                                        <div :class="regData.employee_signature ? '' : 'mb-64'" >Mengetahui DPJP,</div>
                                        <template v-if="regData.employee_signature">
                                            <b-img :src="urlbe+regData.employee_signature" style="width: 120px;"/>
                                        </template>
                                        <div>({{ regData.doctor_in_charge_name }})</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <template v-if="result.health_facility_id === '3cb626f5-9148-4fb1-8663-3d7f028b01a6'">
                        <div class="row align-items-center pb-40" style="border-bottom: 2px solid black;margin-top: 1500px;">
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
                        <div class="text-regular-16 mt-24" style="line-height: 140%;">
                            <table class="mt-16">
                                <tr>
                                    <td class="col-3" style="vertical-align: top;">Nomor RM</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ this.patient.norm }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3" style="vertical-align: top;">Nama</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ this.patient.fullname }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3" style="vertical-align: top;">Tanggal Lahir</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ this.patient.birth_date }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3" style="vertical-align: top;">Jenis Kelamin</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ this.patient.sex_name }}</td>
                                </tr>
                            </table>
                            <div class="mt-16 text-center text-bold-16">
                                Assesment Dokter : {{ this.result.date_added }}
                            </div>

                            <div class="card mt-16">
                                <!-- <div class="card-header text-center font-weight-bold">PENGKAJIAN DOKTER UMUM</div> -->
                                <div class="card-body">
                                    <b-table-simple>
                                        <b-tbody class="text-regular-16">
                                            <b-tr>
                                                <b-td class="col-3">Keluhan Utama</b-td>
                                                <b-td class="px-8">:</b-td>
                                                <b-td>{{ (this.result && this.result.main_complaint) || (this.nurseData && this.nurseData.main_complaint) || '-' }}</b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td class="col-3">Riwayat Penyakit Sekarang</b-td>
                                                <b-td class="px-8">:</b-td>
                                                <b-td>{{ (this.result && this.result.history_disease_now) || (this.nurseData && this.nurseData.history_disease_now) || '-' }}</b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td class="col-3">Riwayat Penyakit Dahulu</b-td>
                                                <b-td class="px-8">:</b-td>
                                                <b-td>{{ (this.result && this.result.history_disease_past) || (this.nurseData && this.nurseData.history_disease_past) || '-' }}</b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td class="col-3">Riwayat Alergi</b-td>
                                                <b-td class="px-8">:</b-td>
                                                <b-td>{{ (this.result && this.result.history_allergy) || (this.nurseData && this.nurseData.history_allergy) || '-' }}</b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td class="col-3">Riwayat Obat</b-td>
                                                <b-td class="px-8">:</b-td>
                                                <b-td>{{ (this.result && this.result.history_drugs) || (this.nurseData && this.nurseData.history_drugs) || '-' }}</b-td>
                                            </b-tr>
                                        </b-tbody>
                                    </b-table-simple>
                                </div>
                            </div>

                            <div class="card mt-16">
                                <div class="card-header text-center font-weight-bold">PENGKAJIAN DOKTER</div>
                                <div class="card-body">
                                    <b-table-simple>
                                        <b-tbody class="text-regular-16">
                                            <b-tr>
                                                <b-td class="col-3">Pemeriksaan Fisik</b-td>
                                                <b-td class="px-8">:</b-td>
                                                <b-td>
                                                    <div>{{ this.result.physical_check }}</div>
                                                    <div class="mt-16">
                                                        <b>Systolic: </b>{{ this.result.systolic_blood_pressure }},
                                                        <b>Diastolic: </b>{{ this.result.diastolic_blood_pressure }},
                                                        <b>Frekuensi Nadi: </b>{{ this.result.pulse_frequency }},
                                                        <b>Frekuensi Nafas: </b>{{ this.result.breath_frequency }},
                                                        <b>Saturasi Oksigen: </b>{{ this.result.oximetry_frequency }},
                                                        <b>Suhu: </b>{{ this.result.temperature }},
                                                        <b>Gula Darah: </b>{{ this.result.blood_sugar }},
                                                    </div>
                                                    <div class="mt-16">
                                                        <b>Berat Badan: </b>{{ this.result.weight }},
                                                        <b>Tinggi Badan: </b>{{ this.result.height }},
                                                        <b>IMT: </b>{{ this.result.imt }},
                                                        <b>Lingkar Kepala: </b>{{ this.result.head_circumference }},
                                                        <b>Lingkar Dada: </b>{{ this.result.chest_circumference }},
                                                        <b>Lingkar Lengan Atas: </b>{{ this.result.arm_circumference }},
                                                        <b>Lingkar Perut: </b>{{ this.result.stomach_circumference }},
                                                        <b>Lingkar Lutut: </b>{{ this.result.knee_circumference }},
                                                        <b>Lainnya: </b>{{ this.result.soap_o_anythingelse ? this.result.soap_o_anythingelse : '-' }}
                                                    </div>
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td class="col-3">Diagnosis Primer</b-td>
                                                <b-td class="px-8">:</b-td>
                                                <b-td>
                                                    <li v-for="item in this.result.icd10">{{ item.code }} {{ item.name }}</li>
                                                    <p><b>Lainnya: </b>{{ result.soap_a_anythingelse }}</p>
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td class="col-3">Tindakan</b-td>
                                                <b-td class="px-8">:</b-td>
                                                <b-td>
                                                    <li v-for="item in this.result.transaction_doctor">{{ item.costing_name }} ( {{ item.date_added }} )</li>
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td class="col-3">Rencana Pemeriksaan Penunjang</b-td>
                                                <b-td class="px-8">:</b-td>
                                                <b-td>
                                                    <div v-if="result.doctor_in_charge_id">
                                                        <div v-if="DPJP_doctor.laboratory_list.length != 0 || DPJP_doctor.radiology_list.length != 0" class="mt-12">
                                                            <div class="font-weight-bold" style="color: blue; text-decoration: underline;">{{ result.employee_doctor_name }} (Dokter DPJP)</div>
                                                            <section v-if="DPJP_doctor.laboratory_list.length != 0" class="mt-8">
                                                                <div class="font-weight-bold">Laboratorium {{ DPJP_doctor.laboratory_list ? '' : ': -' }}</div>
                                                                <div v-for="(item, i) in DPJP_doctor.laboratory_list" :key="i" :class="i == 0 ? '' : 'mt-8'">
                                                                    <div>No Order: {{ item.no_request ? item.no_request : '-' }}</div>
                                                                    <div v-if="item.laboratory_request_item">
                                                                        <li v-for="(list, j) in item.laboratory_request_item" :key="j">{{ list.costing_name }}</li>
                                                                    </div>
                                                                </div>
                                                            </section>
                                                            <section class="mt-20" v-if="DPJP_doctor.radiology_list.length != 0">
                                                                <div class="font-weight-bold">Radiologi {{ DPJP_doctor.radiology_list ? '' : ': -' }}</div>
                                                                <div v-for="(item, i) in DPJP_doctor.radiology_list" :key="i" :class="i == 0 ? '' : 'mt-8'">
                                                                    <div>No Order: {{ item.no_request ? item.no_request : '-' }}</div>
                                                                    <div v-if="item.radiology_request_item">
                                                                        <li v-for="(list, j) in item.radiology_request_item" :key="j">{{ list.costing_name }}</li>
                                                                    </div>
                                                                </div>
                                                            </section>
                                                        </div>
                                                    </div>
                                                    <div v-if="result.doctor_in_charge_id_1">
                                                        <div v-if="FIRST_doctor.laboratory_list.length != 0 || FIRST_doctor.radiology_list.length != 0" class="mt-12">
                                                            <div class="font-weight-bold" style="color: blue; text-decoration: underline;">{{ result.doctor_in_charge_name_1 }} (Dokter Spesialis)</div>
                                                            <section v-if="FIRST_doctor.laboratory_list.length != 0" class="mt-8">
                                                                <div class="font-weight-bold">Laboratorium {{ FIRST_doctor.laboratory_list ? '' : ': -' }}</div>
                                                                <div v-for="(item, i) in FIRST_doctor.laboratory_list" :key="i" :class="i == 0 ? '' : 'mt-8'">
                                                                    <div>No Order: {{ item.no_request ? item.no_request : '-' }}</div>
                                                                    <div v-if="item.laboratory_request_item">
                                                                        <li v-for="(list, j) in item.laboratory_request_item" :key="j">{{ list.costing_name }}</li>
                                                                    </div>
                                                                </div>
                                                            </section>
                                                            <section class="mt-20" v-if="FIRST_doctor.radiology_list.length != 0">
                                                                <div class="font-weight-bold">Radiologi {{ FIRST_doctor.radiology_list ? '' : ': -' }}</div>
                                                                <div v-for="(item, i) in FIRST_doctor.radiology_list" :key="i" :class="i == 0 ? '' : 'mt-8'">
                                                                    <div>No Order: {{ item.no_request ? item.no_request : '-' }}</div>
                                                                    <div v-if="item.radiology_request_item">
                                                                        <li v-for="(list, j) in item.radiology_request_item" :key="j">{{ list.costing_name }}</li>
                                                                    </div>
                                                                </div>
                                                            </section>
                                                        </div>
                                                    </div>
                                                    <div v-if="result.doctor_in_charge_id_2">
                                                        <div v-if="SECOND_doctor.laboratory_list.length != 0 || SECOND_doctor.radiology_list.length != 0" class="mt-12">
                                                            <div class="font-weight-bold" style="color: blue; text-decoration: underline;">{{ result.doctor_in_charge_name_2 }} (Dokter Spesialis)</div>
                                                            <section v-if="SECOND_doctor.laboratory_list.length != 0" class="mt-8">
                                                                <div class="font-weight-bold">Laboratorium {{ SECOND_doctor.laboratory_list ? '' : ': -' }}</div>
                                                                <div v-for="(item, i) in SECOND_doctor.laboratory_list" :key="i" :class="i == 0 ? '' : 'mt-8'">
                                                                    <div>No Order: {{ item.no_request ? item.no_request : '-' }}</div>
                                                                    <div v-if="item.laboratory_request_item">
                                                                        <li v-for="(list, j) in item.laboratory_request_item" :key="j">{{ list.costing_name }}</li>
                                                                    </div>
                                                                </div>
                                                            </section>
                                                            <section class="mt-20" v-if="SECOND_doctor.radiology_list.length != 0">
                                                                <div class="font-weight-bold">Radiologi {{ SECOND_doctor.radiology_list ? '' : ': -' }}</div>
                                                                <div v-for="(item, i) in SECOND_doctor.radiology_list" :key="i" :class="i == 0 ? '' : 'mt-8'">
                                                                    <div>No Order: {{ item.no_request ? item.no_request : '-' }}</div>
                                                                    <div v-if="item.radiology_request_item">
                                                                        <li v-for="(list, j) in item.radiology_request_item" :key="j">{{ list.costing_name }}</li>
                                                                    </div>
                                                                </div>
                                                            </section>
                                                        </div>
                                                    </div>
                                                    <!-- <div v-if="this.result.laboratory_request_items">
                                                        <div><b>LABORATORIUM</b></div>
                                                        <div v-for="item in this.result.laboratory_request_items" class="d-flex align-items-center">
                                                            <input type="checkbox" disabled checked>
                                                            <div class="ml-2">{{ item.costing_name }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-24" v-if="this.result.radiology_request_items">
                                                        <div><b>RADIOLOGI</b></div>
                                                        <div v-for="item in this.result.radiology_request_items" class="d-flex align-items-center">
                                                            <input type="checkbox" disabled checked>
                                                            <div class="ml-2">{{ item.costing_name }}</div>
                                                        </div>
                                                    </div> -->
                                                    <div class="mt-24" v-if="this.result.physiotherapy_request">
                                                        <div><b>FISIOTERAPI</b></div>
                                                        <div v-for="item in this.result.physiotherapy_request">
                                                            <div v-for="(list, index) in item.physiotherapy_request_items">
                                                                <div class="d-flex align-items-center">
                                                                    <input type="checkbox" disabled checked>
                                                                    <div class="ml-2">{{ list.costing_name }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td class="col-3">Terapi</b-td>
                                                <b-td class="px-8">:</b-td>
                                                <b-td>
                                                    <!-- <p v-for="item in this.result.form_prescription">> <b>{{ item.pharmacy_catalog_name }} x{{ item.qty }}</b></p> -->
                                                    <div v-if="result.doctor_in_charge_id && DPJP_doctor.prescription_list.length != 0">
                                                        <section class="mt-8">
                                                            <div class="font-weight-bold" style="color: blue; text-decoration: underline;">{{ result.employee_doctor_name }} (Dokter DPJP)</div>
                                                            <div>
                                                                <template v-if="DPJP_doctor.prescription_list">
                                                                    <div v-for="(item, i) in DPJP_doctor.prescription_list" :key="i" :class="i == 0 ? '' : 'mt-8'">
                                                                        <div>No Order: {{ item.no_order? item.no_order: '-' }}</div>
                                                                        <div v-if="item.form_prescription">
                                                                            <li v-for="(list, j) in item.form_prescription" :key="j">{{ list.pharmacy_catalog_name }}, {{ list.used }}</li>
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </section>
                                                    </div>

                                                    <div v-if="result.doctor_in_charge_id_1 && FIRST_doctor.prescription_list.length != 0">
                                                        <section class="mt-8">
                                                            <div class="font-weight-bold" style="color: blue; text-decoration: underline;">{{ result.doctor_in_charge_name_1 }} (Dokter Spesialis)</div>
                                                            <div>
                                                                <template v-if="FIRST_doctor.prescription_list">
                                                                    <div v-for="(item, i) in FIRST_doctor.prescription_list" :key="i" :class="i == 0 ? '' : 'mt-8'">
                                                                        <div>No Order: {{ item.no_order? item.no_order: '-' }}</div>
                                                                        <div v-if="item.form_prescription">
                                                                            <li v-for="(list, j) in item.form_prescription" :key="j">{{ list.pharmacy_catalog_name }}, {{ list.used }}</li>
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </section>
                                                    </div>

                                                    <div v-if="result.doctor_in_charge_id_2 && SECOND_doctor.prescription_list.length != 0">
                                                        <section class="mt-8">
                                                            <div class="font-weight-bold" style="color: blue; text-decoration: underline;">{{ result.doctor_in_charge_name_2 }} (Dokter Spesialis)</div>
                                                            <div>
                                                                <template v-if="SECOND_doctor.prescription_list">
                                                                    <div v-for="(item, i) in SECOND_doctor.prescription_list" :key="i" :class="i == 0 ? '' : 'mt-8'">
                                                                        <div>No Order: {{ item.no_order? item.no_order: '-' }}</div>
                                                                        <div v-if="item.form_prescription">
                                                                            <li v-for="(list, j) in item.form_prescription" :key="j">{{ list.pharmacy_catalog_name }}, {{ list.used }}</li>
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </section>
                                                    </div>


                                                </b-td>
                                            </b-tr>
                                        </b-tbody>
                                    </b-table-simple>
                                </div>
                            </div>

                            <div class="card mt-16">
                                <div class="card-header text-center font-weight-bold">INFORMASI DOKTER</div>
                                <div class="card-body">
                                    <b-table-simple>
                                        <b-tbody class="text-regular-16">
                                            <b-tr>
                                                <b-td class="col-3">Nama Dokter</b-td>
                                                <b-td class="px-8">:</b-td>
                                                <b-td>{{ this.result.employee_doctor_name }}</b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td class="col-3">Tanggal & Jam Input </b-td>
                                                <b-td class="px-8">:</b-td>
                                                <b-td>{{ this.result.date_added }}</b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td class="col-3">Poli</b-td>
                                                <b-td class="px-8">:</b-td>
                                                <b-td>{{ this.patient.health_facility_item_name }}</b-td>
                                            </b-tr>
                                        </b-tbody>
                                    </b-table-simple>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end w-100 mt-20">
                                <div class="col-5 text-center">
                                    <div :class="result.employee_signature ? '' : 'mb-64'" >DPJP</div>
                                    <template v-if="result.employee_signature">
                                        <b-img :src="urlbe+result.employee_signature" style="width: 120px;"/>
                                    </template>
                                    <div>( {{ this.result.employee_doctor_name }} )</div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template v-if="this.emergencyData">

                        <div class="row align-items-center pb-40" style="border-bottom: 2px solid black;margin-top: 1500px;">
                            <img src="../../../../../@core/assets/images/logo-thb-small.png" width="80px" alt="rs-logo"> <br>
                            <div class="text-regular-14 pl-16">
                                <p class="text-bold-18">RS Taman Harapan Baru</p>
                                <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                                <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                            </div>
                        </div>
                        <section class="text-regular-16 mt-24" style="line-height: 140%;">
                            <div class="row">
                                <div class="col-6">
                                    <table class="mt-16">
                                        <tr>
                                            <td class="align-top" style="width: 160px">Nomor RM</td>
                                            <td class="align-top text-right px-3">:</td>
                                            <td>{{ this.regData.norm }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top" style="">Nama</td>
                                            <td class="align-top text-right px-3">:</td>
                                            <td>{{ this.regData.alias_name }} {{ this.regData.fullname }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top" style="">Tanggal Lahir</td>
                                            <td class="align-top text-right px-3">:</td>
                                            <td>{{ localDate(this.regData.birth_date) }} ({{ parsed_age(regData.birth_date) }})</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top" style="">Jenis Kelamin</td>
                                            <td class="align-top text-right px-3">:</td>
                                            <td>{{ this.regData.sex_name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top" style="">Tanggal Registrasi</td>
                                            <td class="align-top text-right px-3">:</td>
                                            <td>{{ tgl_masuk(this.regData.register_date) }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <!-- <div class="col-6">
                                    <table class="mt-16">
                                        <tr>
                                            <td class="align-top" style="width: 200px">Tanggal Entri Triase</td>
                                            <td class="align-top text-right px-3">:</td>
                                            <td class="align-top">{{ this.triageData && this.triageData.date_added ? this.triageData.date_added : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top" style="">Tanggal Entri Asesmen Perawat</td>
                                            <td class="align-top text-right px-3">:</td>
                                            <td class="align-top">{{ this.nurseData && this.nurseData.date_added ? this.nurseData.date_added : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top" style="">Tanggal Entri Asesmen Dokter</td>
                                            <td class="align-top text-right px-3">:</td>
                                            <td class="align-top">{{ this.emergencyData && this.emergencyData.date_added ? this.emergencyData.date_added : '-' }}</td>
                                        </tr>
                                    </table>
                                </div> -->
                            </div>

                        </section>

                        <section class="card mt-32">
                            <div class="card-header text-center text-bold-18">TRIASE</div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td class="align-top" style="width: 200px;">Tanggal Entri Triase</td>
                                                    <td class="align-top px-3" style="width: 30px;">:</td>
                                                    <td class="align-top">{{ this.triageData && this.triageData.date_added ? this.triageData.date_added : '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Rujukan</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td class="align-top">{{ this.triageData && this.triageData.referral_type_id == 1 ? `Ya, dari ${this.triageData.referral}` : 'Tidak' }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Tingkat Kesadaran</td>
                                                    <td class="px-3">:</td>
                                                    <td>
                                                        <template v-if="triageData && triageData.level_of_consciousness_type_id">
                                                            <div v-if="triageData.level_of_consciousness_type_id == 1">Alert</div>
                                                            <div v-else-if="triageData.level_of_consciousness_type_id == 2">Verbal</div>
                                                            <div v-else-if="triageData.level_of_consciousness_type_id == 3">Painful</div>
                                                            <div v-else-if="triageData.level_of_consciousness_type_id == 4">Unresponsive</div>
                                                        </template>
                                                        <span v-else>-</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Status Imunisasi (Khusus untuk bayi/anak)</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td class="align-top">{{ this.triageData && this.triageData.immunization_status_type_id ? 'Lengkap' : 'Tidak Lengkap' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-6">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td class="align-top" style="width: 200px;">Alergi Obat</td>
                                                    <td class="align-top px-3" style="width: 30px;">:</td>
                                                    <td class="align-top">{{ this.triageData && this.triageData.drug_allergy_type_id == 1 ? `Ya, dari ${this.triageData.drug_allergy}` : 'Tidak' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Keluhan Utama</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td class="align-top">{{ this.triageData && this.triageData.main_complaint ? this.triageData.main_complaint : '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Macam Kasus</td>
                                                    <td class="px-3">:</td>
                                                    <td>
                                                        <template v-if="triageData && triageData.type_of_cases_type_id">
                                                            <div v-if="triageData.type_of_cases_type_id == 1">Visum</div>
                                                            <div v-else-if="triageData.type_of_cases_type_id == 2">DOA</div>
                                                            <div v-else-if="triageData.type_of_cases_type_id == 3">Non Trauma</div>
                                                            <div v-else-if="triageData.type_of_cases_type_id == 4">Trauma</div>
                                                        </template>
                                                        <span v-else>-</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Trauma</td>
                                                    <td class="px-3">:</td>
                                                    <td>
                                                        <template v-if="triageData && triageData.trauma_type_id">
                                                            <div v-if="triageData.trauma_type_id == 1">KLL</div>
                                                            <div v-else-if="triageData.trauma_type_id == 2">KDRT</div>
                                                            <div v-else-if="triageData.trauma_type_id == 3">Kecelakaan Kerja</div>
                                                            <div v-else-if="triageData.trauma_type_id == 4">Kekerasan Pada Anak</div>
                                                        </template>
                                                        <span v-else>-</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- <div>
                                    <ModernLabel label="Tanda Vital" class="mt-32" />
                                    <div class="row mt-10">
                                        <div class="col-6">
                                            <table>
                                                <tbody>
                                                    <tr>
                                                        <td class="align-top" style="width: 200px;">Systolic</td>
                                                        <td class="align-top px-3" style="width: 30px;">:</td>
                                                        <td class="align-top">{{ this.triageData && this.triageData.systolic ? this.triageData.systolic : '-' }} mmHg</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Diastolic</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td class="align-top">{{ this.triageData && this.triageData.diastolic ? this.triageData.diastolic : '-' }} mmHg</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Frekuensi Nadi</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td class="align-top">{{ this.triageData && this.triageData.pulse_frequency ? this.triageData.pulse_frequency : '-' }} x/mnt</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Saturasi Nafas</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td class="align-top">{{ this.triageData && this.triageData.breath_saturation ? this.triageData.breath_saturation : '-' }} x/mnt</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-6">
                                            <table>
                                                <tbody>
                                                    <tr>
                                                        <td class="align-top" style="width: 200px;">Saturasi Oksigen</td>
                                                        <td class="align-top px-3" style="width: 30px;">:</td>
                                                        <td class="align-top">{{ this.triageData && this.triageData.oxygen_saturation ? this.triageData.oxygen_saturation : '-' }} %</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Suhu</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td class="align-top">{{ this.triageData && this.triageData.temperature ? this.triageData.temperature : '-' }} ᵒC</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Berat Badan</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td class="align-top">{{ this.triageData && this.triageData.weight ? this.triageData.weight : '-' }} kg</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Tinggi Badan</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td class="align-top">{{ this.triageData && this.triageData.height ? this.triageData.height : '-' }} cm</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div> -->
                                <div class="mt-24">
                                    <ModernLabel label="Skala Nyeri" class="mt-32" />
                                    <table class="mt-10">
                                        <tbody>
                                            <tr>
                                                <td class="align-top" style="width: 200px;">Area Nyeri</td>
                                                <td class="align-top px-3" style="width: 30px;">:</td>
                                                <td class="align-top">{{ this.triageData && this.triageData.pain_area ? this.triageData.pain_area : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="align-top" style="width: 200px;">Skala Nyeri</td>
                                                <td class="align-top px-3" style="width: 30px;">:</td>
                                                <td class="align-top"></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="row mt-12">
                                        <div class="col text-center">
                                            <b-icon :icon="getPainIcon('0')" :variant="getPainIconVariant('0')" />
                                            <label class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">0</label>
                                            <div class="mt-1">
                                                <img src="@/@core/assets/images/nurse/wong_baker/0.png" width="75px">
                                                <div class="c-form-label mt-10">Tidak nyeri</div>
                                            </div>
                                        </div>
                                        <div class="col text-center">
                                            <b-icon :icon="getPainIcon('1')" :variant="getPainIconVariant('1')" />
                                            <label class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">1</label>
                                        </div>
                                        <div class="col text-center">
                                            <b-icon :icon="getPainIcon('2')" :variant="getPainIconVariant('2')" />
                                            <label class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">2</label>
                                            <div class="mt-1">
                                                <img src="@/@core/assets/images/nurse/wong_baker/2.png" width="75px">
                                                <div class="c-form-label mt-10">Nyeri Ringan</div>
                                            </div>
                                        </div>
                                        <div class="col text-center">
                                            <b-icon :icon="getPainIcon('3')" :variant="getPainIconVariant('3')" />
                                            <label class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">3</label>
                                        </div>
                                        <div class="col text-center">
                                            <b-icon :icon="getPainIcon('4')" :variant="getPainIconVariant('4')" />
                                            <label class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">4</label>
                                            <div class="mt-1">
                                                <img src="@/@core/assets/images/nurse/wong_baker/4.png" width="75px">
                                                <div class="c-form-label mt-10">Nyeri yang mengganggu</div>
                                            </div>
                                        </div>
                                        <div class="col text-center">
                                            <b-icon :icon="getPainIcon('5')" :variant="getPainIconVariant('5')" />
                                            <label class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">5</label>
                                        </div>
                                        <div class="col text-center">
                                            <b-icon :icon="getPainIcon('6')" :variant="getPainIconVariant('6')" />
                                            <label class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">6</label>
                                            <div class="mt-1">
                                                <img src="@/@core/assets/images/nurse/wong_baker/6.png" width="75px">
                                                <div class="c-form-label mt-10">Nyeri yang menyusahkan</div>
                                            </div>
                                        </div>
                                        <div class="col text-center">
                                            <b-icon :icon="getPainIcon('7')" :variant="getPainIconVariant('7')" />
                                            <label class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">7</label>
                                        </div>
                                        <div class="col text-center">
                                            <b-icon :icon="getPainIcon('8')" :variant="getPainIconVariant('8')" />
                                            <label class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">8</label>
                                            <div class="mt-1">
                                                <img src="@/@core/assets/images/nurse/wong_baker/8.png" width="75px">
                                                <div class="c-form-label mt-10">Nyeri hebat</div>
                                            </div>
                                        </div>
                                        <div class="col text-center">
                                            <b-icon :icon="getPainIcon('9')" :variant="getPainIconVariant('9')" />
                                            <label class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">9</label>
                                        </div>
                                        <div class="col text-center">
                                            <b-icon :icon="getPainIcon('10')" :variant="getPainIconVariant('10')" />
                                            <label class="text-bold-24 text-neutral-600 d-block" style="line-height: 100%">10</label>
                                            <div class="mt-1">
                                                <img src="@/@core/assets/images/nurse/wong_baker/10.png" width="75px">
                                                <div class="c-form-label mt-10">Nyeri sangat hebat</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <ModernLabel :label="`Hasil Pemeriksaan Triase: ${triageData && triageData.triage_result ? triageData.triage_result : '-'}`" class="mt-32" />

                                <section class="mt-20">
                                    <b-table-simple responsive>
                                        <b-thead class="text-bold-14 text-center">
                                            <b-tr>
                                                <b-th>PENGKAJIAN</b-th>
                                                <b-th variant="dark">LEVEL 1</b-th>
                                                <b-th variant="danger">LEVEL 2</b-th>
                                                <b-th variant="warning">LEVEL 3</b-th>
                                                <b-th variant="success">LEVEL 4</b-th>
                                                <b-th>LEVEL 5</b-th>
                                            </b-tr>
                                        </b-thead>
                                        <b-tbody class="text-regular-14">
                                            <b-tr>
                                                <b-td>
                                                    <span class="font-weight-bold">JALAN NAFAS</span>
                                                </b-td>
                                                <b-td>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getAirwayIcon('A1')" :variant="getAirwayIconVariant('A1')" class="mr-8" />
                                                        <div class="lh-110">Ada Sumbatan Jalan Nafas</div>
                                                    </div>
                                                </b-td>
                                                <b-td>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getAirwayIcon('B1')" :variant="getAirwayIconVariant('B1')" class="mr-8" />
                                                        <div class="lh-110">Bebas Atau Ada Ancaman Sumbatan</div>
                                                    </div>
                                                </b-td>
                                                <b-td>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getAirwayIcon('C1')" :variant="getAirwayIconVariant('C1')" class="mr-8" />
                                                        <div class="lh-110">Bebas</div>
                                                    </div>
                                                </b-td>
                                                <b-td>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getAirwayIcon('D1')" :variant="getAirwayIconVariant('D1')" class="mr-8" />
                                                        <div class="lh-110">Bebas</div>
                                                    </div>
                                                </b-td>
                                                <b-td>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getAirwayIcon('E1')" :variant="getAirwayIconVariant('E1')" class="mr-8" />
                                                        <div class="lh-110">Bebas</div>
                                                    </div>
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <span class="font-weight-bold">PERNAFASAN</span>
                                                </b-td>
                                                <b-td>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getRespiratoryIcon('A1')" :variant="getRespiratoryIconVariant('A1')" class="mr-8" />
                                                        <div class="lh-110">Henti Nafas/Apnoe</div>
                                                    </div>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getRespiratoryIcon('A2')" :variant="getRespiratoryIconVariant('A2')" class="mr-8" />
                                                        <div class="lh-110">RR &lt; 10x/menit</div>
                                                    </div>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getRespiratoryIcon('A3')" :variant="getRespiratoryIconVariant('A3')" class="mr-8" />
                                                        <div class="lh-110">Sianosis</div>
                                                    </div>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getRespiratoryIcon('A4')" :variant="getRespiratoryIconVariant('A4')" class="mr-8" />
                                                        <div class="lh-110">SPO2 &lt; 90%</div>
                                                    </div>
                                                </b-td>
                                                <b-td>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getRespiratoryIcon('B1')" :variant="getRespiratoryIconVariant('B1')" class="mr-8" />
                                                        <div class="lh-110">RR &ge; 32x/menit</div>
                                                    </div>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getRespiratoryIcon('B2')" :variant="getRespiratoryIconVariant('B2')" class="mr-8" />
                                                        <div class="lh-110">SPO2 &lt; 92%</div>
                                                    </div>
                                                </b-td>
                                                <b-td>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getRespiratoryIcon('C1')" :variant="getRespiratoryIconVariant('C1')" class="mr-8" />
                                                        <div class="lh-110">RR 25-32x/menit</div>
                                                    </div>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getRespiratoryIcon('C2')" :variant="getRespiratoryIconVariant('C2')" class="mr-8" />
                                                        <div class="lh-110">SPO2 92-95%</div>
                                                    </div>
                                                </b-td>
                                                <b-td>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getRespiratoryIcon('D1')" :variant="getRespiratoryIconVariant('D1')" class="mr-8" />
                                                        <div class="lh-110">RR 20-24x/menit</div>
                                                    </div>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getRespiratoryIcon('D2')" :variant="getRespiratoryIconVariant('D2')" class="mr-8" />
                                                        <div class="lh-110">SPO2 96-100%</div>
                                                    </div>
                                                </b-td>
                                                <b-td>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getRespiratoryIcon('E1')" :variant="getRespiratoryIconVariant('E1')" class="mr-8" />
                                                        <div class="lh-110">RR 16-20 x/mnt</div>
                                                    </div>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getRespiratoryIcon('E2')" :variant="getRespiratoryIconVariant('E2')" class="mr-8" />
                                                        <div class="lh-110">SPO2 96-100%</div>
                                                    </div>
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <span class="font-weight-bold">SIRKULASI</span>
                                                </b-td>
                                                <b-td>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getCirculationIcon('A1')" :variant="getCirculationIconVariant('A1')" class="mr-8" />
                                                        <div class="lh-110">Henti Jantung / arrest</div>
                                                    </div>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getCirculationIcon('A2')" :variant="getCirculationIconVariant('A2')" class="mr-8" />
                                                        <div class="lh-110">Nadi Tidak Teraba</div>
                                                    </div>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getCirculationIcon('A3')" :variant="getCirculationIconVariant('A3')" class="mr-8" />
                                                        <div class="lh-110">Akral Dingin</div>
                                                    </div>
                                                </b-td>
                                                <b-td>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getCirculationIcon('B1')" :variant="getCirculationIconVariant('B1')" class="mr-8" />
                                                        <div class="lh-110">Nadi Lemah</div>
                                                    </div>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getCirculationIcon('B2')" :variant="getCirculationIconVariant('B2')" class="mr-8" />
                                                        <div class="lh-110">HR &lt; 50 x/mnt atau &gt; 150 x/mnt</div>
                                                    </div>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getCirculationIcon('B3')" :variant="getCirculationIconVariant('B3')" class="mr-8" />
                                                        <div class="lh-110">Pucat, Akral Dingin</div>
                                                    </div>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getCirculationIcon('B4')" :variant="getCirculationIconVariant('B4')" class="mr-8" />
                                                        <div class="lh-110">CRT &gt; 2 Detik</div>
                                                    </div>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getCirculationIcon('B5')" :variant="getCirculationIconVariant('B5')" class="mr-8" />
                                                        <div class="lh-110">TD &lt; 100/&lt; 60 mmHg</div>
                                                    </div>
                                                </b-td>
                                                <b-td>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getCirculationIcon('C1')" :variant="getCirculationIconVariant('C1')" class="mr-8" />
                                                        <div class="lh-110">Nadi Kuat</div>
                                                    </div>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getCirculationIcon('C2')" :variant="getCirculationIconVariant('C2')" class="mr-8" />
                                                        <div class="lh-110">HR 121-150x/menit</div>
                                                    </div>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getCirculationIcon('C3')" :variant="getCirculationIconVariant('C3')" class="mr-8" />
                                                        <div class="lh-110">TD &gt; 160/ &gt; 100 mmHg</div>
                                                    </div>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getCirculationIcon('C4')" :variant="getCirculationIconVariant('C4')" class="mr-8" />
                                                        <div class="lh-110">CRT &lt; 2 Detik</div>
                                                    </div>
                                                </b-td>
                                                <b-td>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getCirculationIcon('D1')" :variant="getCirculationIconVariant('D1')" class="mr-8" />
                                                        <div class="lh-110">HR 101-120x/menit</div>
                                                    </div>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getCirculationIcon('D2')" :variant="getCirculationIconVariant('D2')" class="mr-8" />
                                                        <div class="lh-110">TD 121 - 140/80-100 mmHg</div>
                                                    </div>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getCirculationIcon('D3')" :variant="getCirculationIconVariant('D3')" class="mr-8" />
                                                        <div class="lh-110">CRT &lt; 2 Detik</div>
                                                    </div>
                                                </b-td>
                                                <b-td>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getCirculationIcon('E1')" :variant="getCirculationIconVariant('E1')" class="mr-8" />
                                                        <div class="lh-110">HR 80-100x/menit</div>
                                                    </div>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getCirculationIcon('E2')" :variant="getCirculationIconVariant('E2')" class="mr-8" />
                                                        <div class="lh-110">TD 120/80 mmHg</div>
                                                    </div>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getCirculationIcon('E3')" :variant="getCirculationIconVariant('E3')" class="mr-8" />
                                                        <div class="lh-110">CRT &lt; 2 Detik</div>
                                                    </div>
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <span class="font-weight-bold">KESADARAN</span>
                                                </b-td>
                                                <b-td>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getConsciousnessIcon('A1')" :variant="getConsciousnessIconVariant('A1')" class="mr-8" />
                                                        <div class="lh-110">GCS &lt; 9</div>
                                                    </div>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getConsciousnessIcon('A2')" :variant="getConsciousnessIconVariant('A2')" class="mr-8" />
                                                        <div class="lh-110">Kejang</div>
                                                    </div>
                                                </b-td>
                                                <b-td>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getConsciousnessIcon('B1')" :variant="getConsciousnessIconVariant('B1')" class="mr-8" />
                                                        <div class="lh-110">GCS 9-12</div>
                                                    </div>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getConsciousnessIcon('B2')" :variant="getConsciousnessIconVariant('B2')" class="mr-8" />
                                                        <div class="lh-110">Gelisah</div>
                                                    </div>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getConsciousnessIcon('B3')" :variant="getConsciousnessIconVariant('B3')" class="mr-8" />
                                                        <div class="lh-110">Nyeri Dada</div>
                                                    </div>
                                                </b-td>
                                                <b-td>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getConsciousnessIcon('C1')" :variant="getConsciousnessIconVariant('C1')" class="mr-8" />
                                                        <div class="lh-110">GCS 13-14</div>
                                                    </div>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getConsciousnessIcon('C2')" :variant="getConsciousnessIconVariant('C2')" class="mr-8" />
                                                        <div class="lh-110">Apatis / Samnolen</div>
                                                    </div>
                                                </b-td>
                                                <b-td>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getConsciousnessIcon('D1')" :variant="getConsciousnessIconVariant('D1')" class="mr-8" />
                                                        <div class="lh-110">GCS 15</div>
                                                    </div>
                                                </b-td>
                                                <b-td>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getConsciousnessIcon('E1')" :variant="getConsciousnessIconVariant('D1')" class="mr-8" />
                                                        <div class="lh-110">GCS 15</div>
                                                    </div>
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <span class="font-weight-bold">SUMBER DAYA</span>
                                                </b-td>
                                                <b-td></b-td>
                                                <b-td></b-td>
                                                <b-td>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getResourceIcon('C1')" :variant="getResourceIconVariant('C1')" class="mr-8" />
                                                        <div class="lh-110">Menggunakan &gt; 2 sumber daya</div>
                                                    </div>
                                                </b-td>
                                                <b-td>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getResourceIcon('D1')" :variant="getResourceIconVariant('D1')" class="mr-8" />
                                                        <div class="lh-110">Menggunakan 1 sumber daya</div>
                                                    </div>
                                                </b-td>
                                                <b-td>
                                                    <div class="d-flex pt-2">
                                                        <b-icon :icon="getResourceIcon('E1')" :variant="getResourceIconVariant('E1')" class="mr-8" />
                                                        <div class="lh-110">Tidak menggunakan sumber daya</div>
                                                    </div>
                                                </b-td>
                                            </b-tr>
                                        </b-tbody>
                                    </b-table-simple>
                                </section>

                                <section class="mt-32">
                                    <b-table-simple bordered class="mt-20">
                                        <b-tr>
                                            <b-th>Sumber Daya</b-th>
                                            <b-th colspan="5">Danger Zone</b-th>
                                        </b-tr>
                                        <b-tr>
                                            <b-td rowspan="5">
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getSecondResourceIcon('a')" :variant="getSecondResourceIconVariant('a')" class="mr-8" />
                                                    <div class="lh-110">Laboratoriun darah</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getSecondResourceIcon('b')" :variant="getSecondResourceIconVariant('b')" class="mr-8" />
                                                    <div class="lh-110">Laboratoriun urine</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getSecondResourceIcon('c')" :variant="getSecondResourceIconVariant('c')" class="mr-8" />
                                                    <div class="lh-110">EKG</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getSecondResourceIcon('d')" :variant="getSecondResourceIconVariant('d')" class="mr-8" />
                                                    <div class="lh-110">X-Ray</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getSecondResourceIcon('e')" :variant="getSecondResourceIconVariant('e')" class="mr-8" />
                                                    <div class="lh-110">Pemasangan infus unt rehidrasi</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getSecondResourceIcon('f')" :variant="getSecondResourceIconVariant('f')" class="mr-8" />
                                                    <div class="lh-110">Pemberian obat IV/IM/Anal</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getSecondResourceIcon('g')" :variant="getSecondResourceIconVariant('g')" class="mr-8" />
                                                    <div class="lh-110">Pemasangan kateter</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getSecondResourceIcon('h')" :variant="getSecondResourceIconVariant('h')" class="mr-8" />
                                                    <div class="lh-110">Pemasangan NGT</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getSecondResourceIcon('i')" :variant="getSecondResourceIconVariant('i')" class="mr-8" />
                                                    <div class="lh-110">Nebulizer</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getSecondResourceIcon('j')" :variant="getSecondResourceIconVariant('j')" class="mr-8" />
                                                    <div class="lh-110">Jahit/hecting</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getSecondResourceIcon('k')" :variant="getSecondResourceIconVariant('k')" class="mr-8" />
                                                    <div class="lh-110">Konsultasi spesialis</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getSecondResourceIcon('l')" :variant="getSecondResourceIconVariant('l')" class="mr-8" />
                                                    <div class="lh-110">Pemasangan monitor</div>
                                                </div>
                                                <div class="d-flex pt-2">
                                                    <b-icon :icon="getSecondResourceIcon('m')" :variant="getSecondResourceIconVariant('m')" class="mr-8" />
                                                    <div class="lh-110">Pemasangan ETT</div>
                                                </div>
                                            </b-td>
                                            <b-th>Usia</b-th>
                                            <b-th>HR</b-th>
                                            <b-th>RR</b-th>
                                            <b-th>S</b-th>
                                            <b-th>SPO2</b-th>
                                        </b-tr>
                                        <b-tr>
                                            <b-td>
                                                <div class="d-flex">
                                                    <b-icon :icon="getRadioZoneIcon('1')" :variant="getRadioZoneIconVariant('1')" class="mr-8" />
                                                    <div class="lh-110">&lt; 3 bulan</div>
                                                </div>
                                            </b-td>
                                            <b-td>&gt; 180</b-td>
                                            <b-td>&gt; 50</b-td>
                                            <b-td>&gt; 38ᵒ</b-td>
                                            <b-td>92%</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td>
                                                <div class="d-flex">
                                                    <b-icon :icon="getRadioZoneIcon('2')" :variant="getRadioZoneIconVariant('2')" class="mr-8" />
                                                    <div class="lh-110">3 bulan - 3 tahun</div>
                                                </div>
                                            </b-td>
                                            <b-td>&gt; 160</b-td>
                                            <b-td>&gt; 40</b-td>
                                            <b-td>&gt; 38ᵒ</b-td>
                                            <b-td>92%</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td>
                                                <div class="d-flex">
                                                    <b-icon :icon="getRadioZoneIcon('3')" :variant="getRadioZoneIconVariant('3')" class="mr-8" />
                                                    <div class="lh-110">3-8 tahun</div>
                                                </div>
                                            </b-td>
                                            <b-td>&gt; 140</b-td>
                                            <b-td>&gt; 30</b-td>
                                            <b-td>&gt; 38ᵒ</b-td>
                                            <b-td>92%</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td>
                                                <div class="d-flex">
                                                    <b-icon :icon="getRadioZoneIcon('4')" :variant="getRadioZoneIconVariant('4')" class="mr-8" />
                                                    <div class="lh-110">&gt; 8 tahun</div>
                                                </div>
                                            </b-td>
                                            <b-td>&gt; 100</b-td>
                                            <b-td>&gt; 20</b-td>
                                            <b-td>&gt; 38ᵒ</b-td>
                                            <b-td>92%</b-td>
                                        </b-tr>
                                    </b-table-simple>

                                    <table class="mt-16">
                                        <tbody>
                                            <tr>
                                                <td class="align-top" style="width: 200px;">Zona</td>
                                                <td class="align-top px-3" style="width: 30px;">:</td>
                                                <td class="align-top">
                                                    <template v-if="triageData && triageData.zone_type_id">
                                                        <div v-if="triageData.zone_type_id == 1">Resusitasi</div>
                                                        <div v-else-if="triageData.zone_type_id == 2">Tindakan</div>
                                                        <div v-else-if="triageData.zone_type_id == 3">Observasi</div>
                                                        <div v-else-if="triageData.zone_type_id == 4">Rawat Jalan</div>
                                                        <div v-else-if="triageData.zone_type_id == 5">Ponek</div>
                                                    </template>
                                                    <span v-else>-</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </section>
                            </div>
                        </section>

                        <section class="card mt-32">
                            <div class="card-header text-center text-bold-18">PERAWAT</div>
                            <div class="card-body">
                                <section>
                                    <table class="w-100">
                                        <tbody>
                                            <tr>
                                                <td class="align-top" style="width: 200px;">Tanggal Entri Asesmen Perawat</td>
                                                <td class="align-top px-3" style="width: 30px;">:</td>
                                                <td class="align-top">{{ this.nurseData && this.nurseData.date_added ? this.nurseData.date_added : '-' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <ModernLabel label="ANAMNESA" class="mt-16" />
                                    <table class="w-100 mt-16">
                                        <tbody>
                                            <tr>
                                                <td style="width: 200px;">Anamnesa</td>
                                                <td style="width: 30px;" class="px-3">:</td>
                                                <td>{{ nurseData && nurseData.anamesa_type_id == '1' ? 'Auto Anamnesa' : 'Allo Anamnesa' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Keluarga</td>
                                                <td class="px-3">:</td>
                                                <td>{{ nurseData && nurseData.family_name ? nurseData.family_name : '-' }}
                                                    <span v-if="nurseData && nurseData.family_relation_id != '0'">
                                                        <template v-if="nurseData && nurseData.family_relation_id == '1'">Ayah</template>
                                                        <template v-else-if="nurseData && nurseData.family_relation_id == '2'">Ibu</template>
                                                        <template v-else-if="nurseData && nurseData.family_relation_id == '3'">Kakak</template>
                                                        <template v-ele-if="nurseData && nurseData.family_relation_id == '4'">Adik</template>
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top" >Keluhan Utama</td>
                                                <td class="align-top px-3">:</td>
                                                <td>{{ nurseData && nurseData.main_complaint ? nurseData.main_complaint : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="align-top">Riwayat Penyakit Sekarang</td>
                                                <td class="align-top px-3">:</td>
                                                <td>
                                                    <div v-html="parsedBody(nurseData && nurseData.history_disease_now ? nurseData.history_disease_now : '-')"></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top pt-2">Riwayat Penyakit Dahulu</td>
                                                <td class="align-top pt-2 px-3">:</td>
                                                <td class="pt-2">
                                                    <div v-html="parsedBody(nurseData && nurseData.history_disease_past ? nurseData.history_disease_past : '-')"></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top pt-2">Riwayat Obat</td>
                                                <td class="align-top pt-2 px-3">:</td>
                                                <td class="pt-2">
                                                    <div v-html="parsedBody(nurseData && nurseData.history_drugs ? nurseData.history_drugs : '-')"></div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </section>
                                <div class="mt-20">
                                    <ModernLabel label="Pemeriksaan Fisik" />
                                    <table class="w-100 mt-16">
                                        <tbody>
                                            <tr>
                                                <td style="width: 200px;">Alat Bantu</td>
                                                <td style="width: 30px;" class="px-3">:</td>
                                                <td>{{ nurseData && nurseData.aids ? nurseData.aids : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Prothesa</td>
                                                <td class="px-3">:</td>
                                                <td>{{ nurseData && nurseData.prothesa ? nurseData.prothesa : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Cacat Tubuh</td>
                                                <td class="px-3">:</td>
                                                <td>{{ nurseData && nurseData.disabled ? nurseData.disabled : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Activity Daily Living</td>
                                                <td class="px-3">:</td>
                                                <td>
                                                    <span v-if="nurseData && nurseData.adl_type_id != '0'">
                                                        <template v-if="nurseData.adl_type_id == '1'">Mandiri</template>
                                                        <template v-else-if="nurseData.adl_type_id == '2'">Dibantu</template>
                                                        <template v-else-if="nurseData.adl_type_id == '3'">Dibantu Sebagian</template>
                                                        <template v-else>-</template>
                                                    </span>
                                                    <template v-else>-</template>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">
                                                    <div class="row pt-20">
                                                        <div class="col-6">
                                                            <h6 class="text-uppercase">Tanda Vital</h6>
                                                            <table class="w-100">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 200px;">Sistolik</td>
                                                                        <td style="width: 30px;" class="px-1 text-center">:</td>
                                                                        <td>{{ nurseData && nurseData.systolic_blood_pressure ? nurseData.systolic_blood_pressure : '-' }} mmHg</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Diastolik</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ nurseData && nurseData.diastolic_blood_pressure ? nurseData.diastolic_blood_pressure : '-' }} mmHg</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Frekuensi Nadi</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ nurseData && nurseData.pulse_frequency ? nurseData.pulse_frequency : '-' }} x/mnt</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Frekuensi Nafas</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ nurseData && nurseData.breath_frequency ? nurseData.breath_frequency : '-' }} x/mnt</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Saturasi Oksigen</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ nurseData && nurseData.oximetry_frequency ? nurseData.oximetry_frequency : '-'}} %</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Suhu</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ nurseData && nurseData.temperature ? nurseData.temperature : '-' }} ᵒC</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Skor Nyeri</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ nurseData && nurseData.sore_score ? nurseData.sore_score : '-' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>EWS</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ nurseData && nurseData.ews ? nurseData.ews : '-' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Gula Darah</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ nurseData && nurseData.blood_sugar ? nurseData.blood_sugar : '-' }} mg/dL</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="col-6">
                                                            <h6 class="text-uppercase">Antropometri</h6>
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 200px;">Berat Badan</td>
                                                                        <td style="width: 30px;" class="px-1 text-center">:</td>
                                                                        <td>{{ nurseData && nurseData.weight ? nurseData.weight : '-' }} kg</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Tinggi Badan</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ nurseData && nurseData.height ? nurseData.height : '-' }} cm</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>IMT</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ nurseData && nurseData.imt ? nurseData.imt : '-' }} kg/m²</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Lingkar Kepala</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ nurseData && nurseData.head_circumference ? nurseData.head_circumference : '-' }} cm</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Lingkar Dada</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ nurseData && nurseData.chest_circumference ? nurseData.chest_circumference : '-' }} cm</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Lingkar Lengan Atas</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ nurseData && nurseData.arm_circumference ? nurseData.arm_circumference : '-' }} cm</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Lingkar Perut</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ nurseData && nurseData.stomach_circumference ? nurseData.stomach_circumference : '-' }} cm</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Lingkar Lutut</td>
                                                                        <td class="px-1 text-center">:</td>
                                                                        <td>{{ nurseData && nurseData.knee_circumference ? nurseData.knee_circumference : '-' }} cm</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-20">
                                    <ModernLabel label="RIWAYAT KESEHATAN" />
                                    <table class="w-100 mt-16">
                                        <tbody>
                                            <tr>
                                                <td style="width: 200px;">Pernah Dirawat Sebelumnya</td>
                                                <td style="width: 30px;" class="px-3">:</td>
                                                <td>
                                                    <span v-if="emergencyData && emergencyData.been_hospitalized_type_id">
                                                        {{ emergencyData.been_hospitalized_type_id == 1 ? 'Iya, ' : 'Tidak' }}
                                                        <span>{{ emergencyData && emergencyData.been_hospitalized_answer ? emergencyData.been_hospitalized_answer : '' }}</span>
                                                    </span>
                                                    <span v-else>-</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Riwayat Penyakit Keluarga</td>
                                                <td class="px-3">:</td>
                                                <td>
                                                    {{ emergencyData && emergencyData.any_history_family_disease_type_id == '1' ? 'Ya: ' : 'Tidak' }}
                                                    <span v-if="emergencyData && emergencyData.any_history_family_answer_arry">
                                                        <template v-if="emergencyData.any_history_family_answer_arry.includes('a')">DM, </template>
                                                        <template v-if="emergencyData.any_history_family_answer_arry.includes('b')">Hipertensi, </template>
                                                        <template v-if="emergencyData.any_history_family_answer_arry.includes('c')">Cancer, </template>
                                                        <template v-if="emergencyData.any_history_family_answer_arry.includes('d')">Jantung, </template>
                                                        <template v-if="emergencyData.any_history_family_answer_arry.includes('e')">TBC, </template>
                                                        <template v-if="emergencyData.any_history_family_answer_arry.includes('f')">Anemia, </template>
                                                        <template v-if="emergencyData.any_history_family_answer_arry.includes('g')">
                                                            <span>{{ emergencyData && emergencyData.any_history_family_answer ? emergencyData.any_history_family_answer : '' }}</span>
                                                        </template>
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Riwayat Alergi</td>
                                                <td class="px-3">:</td>
                                                <td>
                                                    {{ emergencyData && emergencyData.history_allergy_type_id == '1' ? 'Ya: ' : '' }}
                                                    <span>{{ emergencyData && emergencyData.history_allergy ? emergencyData.history_allergy : '-' }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="font-weight-bold">Operasi Yang Pernah Alami</td>
                                            </tr>
                                            <tr>
                                                <td>Pernah Operasi</td>
                                                <td class="px-3">:</td>
                                                <td>{{ emergencyData && emergencyData.any_surgery_type_id == '1' ? 'Ya' : 'Tidak' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Tanggal</td>
                                                <td class="px-3">:</td>
                                                <td>{{ emergencyData && emergencyData.any_surgery_date ? localDate(emergencyData.any_surgery_date) : '-' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-20">
                                    <div class="row">
                                        <div class="col-6">
                                            <div>
                                                <ModernLabel label="RIWAYAT PSIKOLOGIS" />
                                                <table class="w-100 mt-16">
                                                    <tbody>
                                                        <tr>
                                                            <td style="width: 200px;" class="align-top">Riwayat Psikologis</td>
                                                            <td style="width: 30px;" class="align-top px-3">:</td>
                                                            <td>
                                                                <template v-if="emergencyData && emergencyData.history_psychological_arry">
                                                                    <li v-if="emergencyData.history_psychological_arry.includes('a')">Tidak Semangat</li>
                                                                    <li v-if="emergencyData.history_psychological_arry.includes('b')">Cepat Lelah</li>
                                                                    <li v-if="emergencyData.history_psychological_arry.includes('c')">Menggunakan Obat Penenang</li>
                                                                    <li v-if="emergencyData.history_psychological_arry.includes('d')">Rasa Tertekan</li>
                                                                    <li v-if="emergencyData.history_psychological_arry.includes('e')">Sulit Berbicara</li>
                                                                    <li v-if="emergencyData.history_psychological_arry.includes('f')">Merasa Bersalah</li>
                                                                    <li v-if="emergencyData.history_psychological_arry.includes('g')">Depresi</li>
                                                                    <li v-if="emergencyData.history_psychological_arry.includes('h')">Kurang Nafsu Makan</li>
                                                                    <li v-if="emergencyData.history_psychological_arry.includes('i')">Tenang</li>
                                                                    <li v-if="emergencyData.history_psychological_arry.includes('j')">Sulit Tidur</li>
                                                                    <li v-if="emergencyData.history_psychological_arry.includes('k')">Sulit Konsentrasi</li>
                                                                    <li v-if="emergencyData.history_psychological_arry.includes('l')">Tidak Ada Keluhan</li>
                                                                </template>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="mt-20">
                                                <ModernLabel label="PENGKAJIAN RISIKO JATUH" />
                                                <table class="w-100 mt-16">
                                                    <tbody>
                                                        <tr>
                                                            <td style="width: 200px;">Risiko</td>
                                                            <td style="width: 30px;" class="px-3">:</td>
                                                            <td>
                                                                <span v-if="emergencyData && emergencyData.fall_risk_type_id">
                                                                    <template v-if="emergencyData.fall_risk_type_id == '1'">Tidak Beresiko</template>
                                                                    <template v-if="emergencyData.fall_risk_type_id == '2'">Beresiko Rendah</template>
                                                                    <template v-if="emergencyData.fall_risk_type_id == '3'">Beresiko Tinggi</template>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3">
                                                                <b-table-simple bordered responsive class="mt-12">
                                                                    <b-thead>
                                                                        <b-tr>
                                                                            <b-th>Komponen Penilaian</b-th>
                                                                            <b-th>Tidak</b-th>
                                                                            <b-th>Ya</b-th>
                                                                        </b-tr>
                                                                    </b-thead>
                                                                    <b-tbody>
                                                                        <b-tr>
                                                                            <b-td>
                                                                                <ol type="a">
                                                                                    <li>Perhatikan cara jalan pasien saat akan duduk di kursi. Apakah pasien tampak seimbang (Sempoyong/ seimbang) ?</li>
                                                                                </ol>
                                                                            </b-td>
                                                                            <b-td style="width: 70px;">
                                                                                <b-icon :icon="getFallAIcon('0')" :variant="getFallAIconVariant('0')" class="mr-8" />
                                                                            </b-td>
                                                                            <b-td style="width: 70px;">
                                                                                <b-icon :icon="getFallAIcon('1')" :variant="getFallAIconVariant('1')" class="mr-8" />
                                                                            </b-td>
                                                                        </b-tr>
                                                                        <b-tr>
                                                                            <b-td>
                                                                                <ol type="a" start="2">
                                                                                    <li>Apakah pasien memegang pinggiran kursi atau benda lain sebagai penopang saat akan duduk ?</li>
                                                                                </ol>
                                                                            </b-td>
                                                                            <b-td style="width: 70px;">
                                                                                <b-icon :icon="getFallBIcon('0')" :variant="getFallBIconVariant('0')" class="mr-8" />
                                                                            </b-td>
                                                                            <b-td style="width: 70px;">
                                                                                <b-icon :icon="getFallBIcon('1')" :variant="getFallBIconVariant('1')" class="mr-8" />
                                                                            </b-td>
                                                                        </b-tr>
                                                                    </b-tbody>
                                                                </b-table-simple>
                                                            </td>
                                                        </tr>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <ModernLabel label="SKRINING GIZI" />
                                            <table class="w-100 mt-16">
                                                <tbody>
                                                    <tr>
                                                        <td style="width: 200px;">Indeks Masa Tubuh</td>
                                                        <td style="width: 30px;" class="px-3">:</td>
                                                        <td>
                                                            <span>{{ nurseData && nurseData.imt ? nurseData.imt : '0' }} kg/m² </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Asupan Makan Pasien</td>
                                                        <td class="px-3">:</td>
                                                        <td>
                                                            <span v-if="emergencyData && emergencyData.patient_food_intake">
                                                                <template v-if="emergencyData.patient_food_intake == '1'">Baik</template>
                                                                <template v-else-if="emergencyData.patient_food_intake == '2'">Kurang</template>
                                                                <template v-else-if="emergencyData.patient_food_intake == '3'">Kurang Semangat</template>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Kehilangan Berat Badan (Dalam Waktu 6 Bulan Terakhir)</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td class="align-top">
                                                            <span v-if="emergencyData && emergencyData.six_month_lose_weight">
                                                                <template v-if="emergencyData.six_month_lose_weight == '1'">Tidak</template>
                                                                <template v-else-if="emergencyData.six_month_lose_weight == '2'">Tidak Yakin</template>
                                                                <template v-else-if="emergencyData.six_month_lose_weight == '3'">Ya, </template>
                                                            </span>
                                                            <span v-if="emergencyData && emergencyData.lose_weight">
                                                                <template v-if="emergencyData.lose_weight == '1'">1 - 5 kg</template>
                                                                <template v-else-if="emergencyData.lose_weight == '2'">6 - 10 kg</template>
                                                                <template v-else-if="emergencyData.lose_weight == '3'"></template>
                                                                <template v-else-if="emergencyData.lose_weight == '4'">&gt; 15 kg</template>
                                                                <template v-else-if="emergencyData.lose_weight == '5'">Tidak Tahu Berapa kg</template>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Kondisi Penyakit Pasien</td>
                                                        <td class="px-3">:</td>
                                                        <td>
                                                            <span v-if="emergencyData && emergencyData.patient_disease_condition">
                                                                <template v-if="emergencyData.patient_disease_condition == '1'">Tidak</template>
                                                                <template v-else-if="emergencyData.patient_disease_condition == '2'">Ya</template>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Pasien Sedang Mendapat Diet Makanan Tertentu</td>
                                                        <td class="px-3">:</td>
                                                        <td>
                                                            <span v-if="emergencyData && emergencyData.patient_certain_diets">
                                                                <template v-if="emergencyData.patient_certain_diets == '1'">Tidak</template>
                                                                <template v-else-if="emergencyData.patient_certain_diets == '2'">Ya</template>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Total Skor</td>
                                                        <td class="px-3">:</td>
                                                        <td>{{ emergencyData && emergencyData.nutrition_total_score ? emergencyData.nutrition_total_score : '-' }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-20">
                                    <ModernLabel label="DAFTAR MASALAH KEPERAWATAN" />
                                    <div class="row">
                                        <div class="col-6">
                                            <table class="w-100 mt-16">
                                                <tbody>
                                                    <tr>
                                                        <td style="width: 200px;" class="align-top">Diagnosa Keperawatan</td>
                                                        <td style="width: 30px;" class="align-top px-3">:</td>
                                                        <td>
                                                            <template v-if="emergencyData && emergencyData.nursing_diagnoses_arry">
                                                                <li v-if="emergencyData.nursing_diagnoses_arry.includes('a')">Bersihkan jalan nafas</li>
                                                                <li v-if="emergencyData.nursing_diagnoses_arry.includes('b')">Cemas/ Ansietas</li>
                                                                <li v-if="emergencyData.nursing_diagnoses_arry.includes('c')">Ketidakefektifan jalan nafas</li>
                                                                <li v-if="emergencyData.nursing_diagnoses_arry.includes('d')">Hipertemi</li>
                                                                <li v-if="emergencyData.nursing_diagnoses_arry.includes('e')">Gangguan mobilitas fisik</li>
                                                                <li v-if="emergencyData.nursing_diagnoses_arry.includes('f')">Keterlambatan pertumbuhan dan perkembangan</li>
                                                                <li v-if="emergencyData.nursing_diagnoses_arry.includes('g')">Intoleransi aktifitas</li>
                                                                <li v-if="emergencyData.nursing_diagnoses_arry.includes('h')">Risiko tinggi infeksi</li>
                                                                <li v-if="emergencyData.nursing_diagnoses_arry.includes('i')">Gangguan persepsi sensori pendengaran</li>
                                                                <li v-if="emergencyData.nursing_diagnoses_arry.includes('j')">Risiko tinggi cedera</li>
                                                                <li v-if="emergencyData.nursing_diagnoses_arry.includes('k')">Gangguan persepsi sensori penglihatan</li>
                                                                <li v-if="emergencyData.nursing_diagnoses_arry.includes('l')">Risiko kekurangan cairan</li>
                                                                <li v-if="emergencyData.nursing_diagnoses_arry.includes('m')">Gangguan integritas kulit</li>
                                                                <li v-if="emergencyData.nursing_diagnoses_arry.includes('n')">Hipertensi</li>
                                                                <li v-if="emergencyData.nursing_diagnoses_arry.includes('o')">Kurang pengetahuan</li>
                                                            </template>
                                                            <template v-else>-</template>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Catatan</td>
                                                        <td class="px-3 align-top">:</td>
                                                        <td>
                                                            <div v-html="parsedBody(emergencyData && emergencyData.nursing_diagnoses ? emergencyData.nursing_diagnoses : '-')"></div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-6">
                                            <table class="w-100 mt-16">
                                                <tbody>
                                                    <tr>
                                                        <td style="width: 200px;" class="align-top">Implementasi</td>
                                                        <td style="width: 30px;" class="align-top px-3">:</td>
                                                        <td>
                                                            <template v-if="emergencyData && emergencyData.implementation_arry">
                                                                <li v-if="emergencyData.implementation_arry.includes('a')">Menjelaskan cara pencegahan dan perawatan kulit</li>
                                                                <li v-if="emergencyData.implementation_arry.includes('b')">Sehat</li>
                                                                <li v-if="emergencyData.implementation_arry.includes('c')">Mengajarkan teknik nafas dalam</li>
                                                                <li v-if="emergencyData.implementation_arry.includes('d')">MCU</li>
                                                                <li v-if="emergencyData.implementation_arry.includes('e')">Mengajarkan batuk efektif</li>
                                                                <li v-if="emergencyData.implementation_arry.includes('f')">Menganjurkan untuk banyak makan buah segar dan kacang-kacangan</li>
                                                                <li v-if="emergencyData.implementation_arry.includes('g')">Menginformasikan tidak merokok dalam ruangan</li>
                                                                <li v-if="emergencyData.implementation_arry.includes('h')">SKPN dan Rapid test</li>
                                                                <li v-if="emergencyData.implementation_arry.includes('i')">Memantau tanda-tanda sianosis</li>
                                                                <li v-if="emergencyData.implementation_arry.includes('j')">MCU calon karyawan</li>
                                                                <li v-if="emergencyData.implementation_arry.includes('k')">Mengukur warna dan suhu kulit</li>
                                                                <li v-if="emergencyData.implementation_arry.includes('l')">Tidak menahan BAK</li>
                                                                <li v-if="emergencyData.implementation_arry.includes('m')">Mengatur posisi nyaman</li>
                                                                <li v-if="emergencyData.implementation_arry.includes('n')">Memberikan obat tetes mata</li>
                                                                <li v-if="emergencyData.implementation_arry.includes('o')">Menganjurkan banyak minum</li>
                                                                <li v-if="emergencyData.implementation_arry.includes('p')">Manganjurkan pola hidup sehat</li>
                                                                <li v-if="emergencyData.implementation_arry.includes('q')">Kompres air hangat pada daerah yang gatal</li>
                                                                <li v-if="emergencyData.implementation_arry.includes('r')">Swab PCR some day</li>
                                                                <li v-if="emergencyData.implementation_arry.includes('s')">Jaga kebersihan</li>
                                                                <li v-if="emergencyData.implementation_arry.includes('t')">Injeksi Vit. C</li>
                                                                <li v-if="emergencyData.implementation_arry.includes('u')">SKPN</li>
                                                                <li v-if="emergencyData.implementation_arry.includes('v')">Menganjurkan banyak istirahat</li>
                                                                <li v-if="emergencyData.implementation_arry.includes('w')">Anjurkan banyak minum air putih</li>
                                                                <li v-if="emergencyData.implementation_arry.includes('x')">Infus Vit. C</li>
                                                                <li v-if="emergencyData.implementation_arry.includes('y')">Makan porsi kecil tapi sering</li>
                                                                <li v-if="emergencyData.implementation_arry.includes('z')">Istirahatkan mata</li>
                                                                <li v-if="emergencyData.implementation_arry.includes('aa')">Nyeri</li>
                                                                <li v-if="emergencyData.implementation_arry.includes('ab')">Menganjurkan banyak makan sayur</li>
                                                                <li v-if="emergencyData.implementation_arry.includes('ac')">Anti HIV rapid</li>
                                                            </template>
                                                            <template v-else>-</template>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Catatan</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>
                                                            <div v-html="parsedBody(emergencyData.implementation ? emergencyData.implementation : '-')"></div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="card mt-32">
                            <div class="card-header text-center text-bold-18">PENGKAJIAN MEDIS IGD</div>
                            <div class="card-body">
                                <section>
                                    <div class="col">
                                        <table class="w-100">
                                            <tr>
                                                <td class="align-top" style="width: 200px;">Tanggal Entri Asesmen Dokter</td>
                                                <td class="px-1 align-top text-center" style="width: 30px;" >:</td>
                                                <td class="align-top">{{ this.emergencyData && this.emergencyData.date_added ? this.emergencyData.date_added : '-' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <ModernLabel label="Diagnosa (ICD10)" class="mt-16" />
                                    <div class="col">
                                        <table class="mt-8 w-100">
                                            <tr>
                                                <td style="width: 200px;" class="align-top">Diagnosis</td>
                                                <td style="width: 30px;" class="px-1 align-top text-center">:</td>
                                                <td>
                                                    <div v-if="result.icd10">
                                                        <li v-for="(item, index) in result.icd10" >{{ item.code }} - {{ item.name }}</li>
                                                    </div>
                                                    <div v-else>-</div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top">Catatan</td>
                                                <td class="align-top px-1 text-center">:</td>
                                                <td>
                                                    <div v-html="parsedBody(result.icd10_notes ? result.icd10_notes : '-')"></div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </section>
                                <section class="mt-20">
                                    <ModernLabel label="Diagnosis Non-ICD10" />
                                    <div class="col">
                                        <table class="mt-8 w-100">
                                            <tr>
                                                <td style="width: 200px;" class="align-top">Non-ICD10</td>
                                                <td style="width: 30px;" class="px-1 align-top text-center">:</td>
                                                <td>
                                                    <div v-if="result.non_icd10">
                                                        <li v-for="(item, index) in result.non_icd10" >{{ item }}</li>
                                                    </div>
                                                    <div v-else>-</div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top">Assesment Lainnya</td>
                                                <td class="align-top px-1 text-center">:</td>
                                                <td>
                                                    <div v-html="parsedBody(result.soap_a_anythingelse ? result.soap_a_anythingelse : '-')"></div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </section>
                                <section class="mt-20">
                                    <ModernLabel label="ANAMNESA" />
                                    <table class="w-100 mt-16">
                                        <tbody>
                                            <tr>
                                                <td style="width: 200px;">Anamnesa</td>
                                                <td style="width: 30px;" class="px-3">:</td>
                                                <td>{{ result.anamesa_type_id == '1' ? 'Auto Anamnesa' : 'Allo Anamnesa' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Keluarga</td>
                                                <td class="px-3">:</td>
                                                <td>{{ result.family_name ? result.family_name : '-' }}
                                                    <span v-if="result.family_relation_id != '0'">
                                                        <template v-if="result.family_relation_id == '1'">Ayah</template>
                                                        <template v-else-if="result.family_relation_id == '2'">Ibu</template>
                                                        <template v-else-if="result.family_relation_id == '3'">Kakak</template>
                                                        <template v-ele-if="result.family_relation_id == '4'">Adik</template>
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top" >Keluhan Utama</td>
                                                <td class="align-top px-3">:</td>
                                                <td>{{ result.main_complaint ? result.main_complaint : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="align-top">Riwayat Penyakit Sekarang</td>
                                                <td class="align-top px-3">:</td>
                                                <td>
                                                    <div v-html="parsedBody(result.history_disease_now ? result.history_disease_now : '-')"></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top pt-2">Riwayat Penyakit Dahulu</td>
                                                <td class="align-top pt-2 px-3">:</td>
                                                <td class="pt-2">
                                                    <div v-html="parsedBody(result.history_disease_past ? result.history_disease_past : '-')"></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top pt-2">Riwayat Obat</td>
                                                <td class="align-top pt-2 px-3">:</td>
                                                <td class="pt-2">
                                                    <div v-html="parsedBody(result.history_drugs ? result.history_drugs : '-')"></div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </section>
                                <section class="mt-20">
                                    <ModernLabel label="Pemeriksaan Fisik" />
                                    <div class="row mt-16">
                                        <div class="col-6">
                                            <table class="w-100">
                                                <tbody>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold">Kepala</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 200px;">Kepala</td>
                                                        <td style="width: 30px;" class="px-3">:</td>
                                                        <td>{{ generalist.head }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Warna Rambut</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.head_hair_color }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Kondisi Rambut</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.head_hair_condition }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold">Mata</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top" >Konjungtiva</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.eye_conjunctive }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Sklera</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.eye_sclera }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Pupil</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.eye_pupil }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Reflek Cahaya</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.eye_light_reflex }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold">Mulut</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Mukosa</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.mouth_mucosa }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Perubahan Warna</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.mouth_discoloration }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Lidah</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.mouth_tongue }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Karies</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.mouth_caries }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold">Leher</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">KGB</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.neck_kgb }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Posisi Trakea</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.neck_trachea_position }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Massa</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.neck_massa }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold">Ekstremitas</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Akral</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.extremity_acral }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">CRT</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.extremity_crt }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Edema</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.extremity_edema }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-6">
                                            <table class="w-100">
                                                <tbody>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold">Thoraks</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold text-primary">Inspeksi</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top" style="width: 200px;">Dada</td>
                                                        <td style="width: 30px;" class="align-top px-3">:</td>
                                                        <td>{{ generalist.thorax_insp_chest }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Retraksi</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.thorax_insp_retraction }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold text-primary">Palpasi</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Dada</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.thorax_palp_chest }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Fremitus</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.thorax_palp_fremitus }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Krepitasi</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.thorax_palp_crepitation }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold text-primary">Auskultasi</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Pulmo Vasikuler</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.thorax_ausk_pul_vasiculer }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Pulmo Rhonki</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.thorax_ausk_pul_rhonki }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Pulmo Wheezing</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.thorax_ausk_pul_wheezing }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">COR Bunyi Jantung</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.thorax_ausk_cor_heart_sound }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">COR Murmur</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.thorax_ausk_cor_murmur }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">COR Gallop</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.thorax_ausk_cor_gallop }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold">Abdomen</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Supel</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.abdomen_supel }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Bising Usus</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.abdomen_bowel_sounds }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Nyeri Tekan</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.abdomen_tenderness }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Hepar & Lien</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td>{{ generalist.abdomen_hepar_lien }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Pemeriksaan Abdomen Lainnya</td>
                                                        <td class="align-top px-3">:</td>
                                                        <td class="align-top">
                                                            <div v-html="parsedBody(generalist.abdomen_other_physical_check ? generalist.abdomen_other_physical_check : '-')"></div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="mt-16">
                                        <table class="w-100">
                                            <tbody>
                                                <tr>
                                                    <td colspan="3" class="font-weight-bold">Genitalia</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top" style="width: 200px;">Genitalia</td>
                                                    <td class="align-top px-3" style="width: 30px;">:</td>
                                                    <td>
                                                        <div v-html="parsedBody(generalist.genitalia ? generalist.genitalia : '-')"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="font-weight-bold">Status Lokalis</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Status Lokalis</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>
                                                        <div v-html="parsedBody(generalist.localist_status ? generalist.localist_status : '-')"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="font-weight-bold">Status Oftalmologis</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Status Oftalmologis</td>
                                                    <td class="align-top px-3">:</td>
                                                    <td>
                                                        <div v-html="parsedBody(generalist.ophthal_status ? generalist.ophthal_status : '-')"></div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <template v-if="active.ophtal">
                                            <b-table-simple bordered fixed small class="mt-16">
                                                <b-thead>
                                                    <b-tr class="text-center">
                                                        <b-th class="bg-warning-200">OD</b-th>
                                                        <b-th class="bg-primary-100">Pemeriksaan</b-th>
                                                        <b-th class="bg-warning-200">OS</b-th>
                                                    </b-tr>
                                                </b-thead>
                                                <b-tbody>
                                                    <b-tr>
                                                        <b-td>{{ generalist.ophthal_od.visus }}</b-td>
                                                        <b-th class="text-center bg-primary-100">Visus</b-th>
                                                        <b-td>{{ generalist.ophthal_os.visus }}</b-td>
                                                    </b-tr>
                                                    <b-tr>
                                                        <b-td>
                                                            <div>
                                                                <span class="text-semi-16">Digital: </span>
                                                                <span>{{ generalist.ophthal_od.tio_digital }}</span>
                                                            </div>
                                                            <div class="mt-8">
                                                                <span class="text-semi-16">Tonometri Sciotz: </span>
                                                                <span>{{ generalist.ophthal_od ? generalist.ophthal_od.tio_tonometri_sciots : '-' }}</span>
                                                            </div>
                                                        </b-td>
                                                        <b-th class="text-center bg-primary-100">TIO</b-th>
                                                        <b-td>
                                                            <div>
                                                                <span class="text-semi-16">Digital: </span>
                                                                <span>{{ generalist.ophthal_os.tio_digital }}</span>
                                                            </div>
                                                            <div class="mt-8">
                                                                <span class="text-semi-16">Tonometri Sciotz: </span>
                                                                <span>{{ generalist.ophthal_os ? generalist.ophthal_os.tio_tonometri_sciots : '-' }}</span>
                                                            </div>
                                                        </b-td>
                                                    </b-tr>
                                                    <b-tr>
                                                        <b-td>{{ generalist.ophthal_od.eye_position }}</b-td>
                                                        <b-th class="text-center bg-primary-100">Posisi Mata</b-th>
                                                        <b-td>{{ generalist.ophthal_os.eye_position }}</b-td>
                                                    </b-tr>
                                                    <b-tr>
                                                        <b-td>{{ generalist.ophthal_od.eye_movement }}</b-td>
                                                        <b-th class="text-center bg-primary-100">Pergerakan Mata</b-th>
                                                        <b-td>{{ generalist.ophthal_os.eye_movement }}</b-td>
                                                    </b-tr>
                                                    <b-tr>
                                                        <b-td>{{ generalist.ophthal_od.super_silia }}</b-td>
                                                        <b-th class="text-center bg-primary-100">Super Silia</b-th>
                                                        <b-td>{{ generalist.ophthal_os.super_silia }}</b-td>
                                                    </b-tr>
                                                    <b-tr>
                                                        <b-td>
                                                            <div>
                                                                <span class="text-semi-16">Edema: </span>
                                                                <span>{{ generalist.ophthal_od.palpebra_edema }}</span>
                                                            </div>
                                                            <div class="mt-8">
                                                                <span class="text-semi-16">Ptosis: </span>
                                                                <span>{{ generalist.ophthal_od.palpebra_ptosis }}</span>
                                                            </div>
                                                            <div class="mt-8">
                                                                <span class="text-semi-16">Lagophtalmus: </span>
                                                                <span>{{ generalist.ophthal_od.palpebra_lagophtalmus }}</span>
                                                            </div>
                                                            <div class="mt-8">
                                                                <span class="text-semi-16">Tumor: </span>
                                                                <span>{{ generalist.ophthal_od.palpebra_tumor }}</span>
                                                            </div>
                                                        </b-td>
                                                        <b-th class="text-center bg-primary-100">Palpebra</b-th>
                                                        <b-td>
                                                            <div>
                                                                <span class="text-semi-16">Edema: </span>
                                                                <span>{{ generalist.ophthal_os.palpebra_edema }}</span>
                                                            </div>
                                                            <div class="mt-8">
                                                                <span class="text-semi-16">Ptosis: </span>
                                                                <span>{{ generalist.ophthal_os.palpebra_ptosis }}</span>
                                                            </div>
                                                            <div class="mt-8">
                                                                <span class="text-semi-16">Lagophtalmus: </span>
                                                                <span>{{ generalist.ophthal_os.palpebra_lagophtalmus }}</span>
                                                            </div>
                                                            <div class="mt-8">
                                                                <span class="text-semi-16">Tumor: </span>
                                                                <span>{{ generalist.ophthal_os.palpebra_tumor }}</span>
                                                            </div>
                                                        </b-td>
                                                    </b-tr>
                                                    <b-tr>
                                                        <b-td>{{ generalist.ophthal_od.apparatus_lakrimalis }}</b-td>
                                                        <b-th class="text-center bg-primary-100">Apparatus Lakrimalis</b-th>
                                                        <b-td>{{ generalist.ophthal_os.apparatus_lakrimalis }}</b-td>
                                                    </b-tr>
                                                    <b-tr>
                                                        <b-td>{{ generalist.ophthal_od.silia }}</b-td>
                                                        <b-th class="text-center bg-primary-100">Silia</b-th>
                                                        <b-td>{{ generalist.ophthal_os.silia }}</b-td>
                                                    </b-tr>
                                                    <b-tr>
                                                        <b-td>
                                                            <div>
                                                                <span class="text-semi-16">Hiperemis: </span>
                                                                <span>{{ generalist.ophthal_od.conjunctive_hiperemis }}</span>
                                                            </div>
                                                            <div class="mt-8">
                                                                <span class="text-semi-16">Pendarahan: </span>
                                                                <span>{{ generalist.ophthal_od.conjunctive_bleed }}</span>
                                                            </div>
                                                        </b-td>
                                                        <b-th class="text-center bg-primary-100">Konjungtiva</b-th>
                                                        <b-td>
                                                            <div>
                                                                <span class="text-semi-16">Hiperemis: </span>
                                                                <span>{{ generalist.ophthal_os.conjunctive_hiperemis }}</span>
                                                            </div>
                                                            <div class="mt-8">
                                                                <span class="text-semi-16">Pendarahan: </span>
                                                                <span>{{ generalist.ophthal_os.conjunctive_bleed }}</span>
                                                            </div>
                                                        </b-td>
                                                    </b-tr>
                                                    <b-tr>
                                                        <b-td>{{ generalist.ophthal_od.cornea }}</b-td>
                                                        <b-th class="text-center bg-primary-100">Kornea</b-th>
                                                        <b-td>{{ generalist.ophthal_os.cornea }}</b-td>
                                                    </b-tr>
                                                    <b-tr>
                                                        <b-td>{{ generalist.ophthal_od.camera_oculi }}</b-td>
                                                        <b-th class="text-center bg-primary-100">Camera Oculi Anterior</b-th>
                                                        <b-td>{{ generalist.ophthal_os.camera_oculi }}</b-td>
                                                    </b-tr>
                                                    <b-tr>
                                                        <b-td>
                                                            <div>
                                                                <span class="text-semi-16">Warna Iris: </span>
                                                                <span>{{ generalist.ophthal_od.iris_color }}</span>
                                                            </div>
                                                            <div class="mt-8">
                                                                <span class="text-semi-16">Kripte: </span>
                                                                <span>{{ generalist.ophthal_od.iris_cripte }}</span>
                                                            </div>
                                                        </b-td>
                                                        <b-th class="text-center bg-primary-100">Iris</b-th>
                                                        <b-td>
                                                            <div>
                                                                <span class="text-semi-16">Warna Iris: </span>
                                                                <span>{{ generalist.ophthal_os.iris_color }}</span>
                                                            </div>
                                                            <div class="mt-8">
                                                                <span class="text-semi-16">Kripte: </span>
                                                                <span>{{ generalist.ophthal_os.iris_cripte }}</span>
                                                            </div>
                                                        </b-td>
                                                    </b-tr>
                                                    <b-tr>
                                                        <b-td>
                                                            <div>
                                                                <span class="text-semi-16">Bentuk Pupil: </span>
                                                                <span>{{ generalist.ophthal_od.pupil_form }}</span>
                                                            </div>
                                                            <div class="mt-8">
                                                                <span class="text-semi-16">Diameter Pupil: </span>
                                                                <span>{{ generalist.ophthal_od.pupil_diameter }}</span>
                                                            </div>
                                                            <div class="mt-8">
                                                                <span class="text-semi-16">Refleksi Cahaya Langsung: </span>
                                                                <span>{{ generalist.ophthal_od.pupil_direct_light }}</span>
                                                            </div>
                                                            <div class="mt-8">
                                                                <span class="text-semi-16">Refleksi Cahaya Tak Langsung: </span>
                                                                <span>{{ generalist.ophthal_od.pupil_indirect_light }}</span>
                                                            </div>
                                                        </b-td>
                                                        <b-th class="text-center bg-primary-100">Pupil</b-th>
                                                        <b-td>
                                                            <div>
                                                                <span class="text-semi-16">Bentuk Pupil: </span>
                                                                <span>{{ generalist.ophthal_os.pupil_form }}</span>
                                                            </div>
                                                            <div class="mt-8">
                                                                <span class="text-semi-16">Diameter Pupil: </span>
                                                                <span>{{ generalist.ophthal_os.pupil_diameter }}</span>
                                                            </div>
                                                            <div class="mt-8">
                                                                <span class="text-semi-16">Refleksi Cahaya Langsung: </span>
                                                                <span>{{ generalist.ophthal_os.pupil_direct_light }}</span>
                                                            </div>
                                                            <div class="mt-8">
                                                                <span class="text-semi-16">Refleksi Cahaya Tak Langsung: </span>
                                                                <span>{{ generalist.ophthal_os.pupil_indirect_light }}</span>
                                                            </div>
                                                        </b-td>
                                                    </b-tr>
                                                    <b-tr>
                                                        <b-td>{{ generalist.ophthal_od.lens }}</b-td>
                                                        <b-th class="text-center bg-primary-100">Lensa</b-th>
                                                        <b-td>{{ generalist.ophthal_os.lens }}</b-td>
                                                    </b-tr>
                                                    <b-tr>
                                                        <b-td>{{ generalist.ophthal_od.shadow_test }}</b-td>
                                                        <b-th class="text-center bg-primary-100">Shadow Test</b-th>
                                                        <b-td>{{ generalist.ophthal_os.shadow_test }}</b-td>
                                                    </b-tr>
                                                    <b-tr>
                                                        <b-td>{{ generalist.ophthal_od.glandula }}</b-td>
                                                        <b-th class="text-center bg-primary-100">Glandula pre-aurikuler</b-th>
                                                        <b-td>{{ generalist.ophthal_os.glandula }}</b-td>
                                                    </b-tr>
                                                    <b-tr>
                                                        <b-td>{{ generalist.ophthal_od.field_view }}</b-td>
                                                        <b-th class="text-center bg-primary-100">Lapang Pandang</b-th>
                                                        <b-td>{{ generalist.ophthal_os.field_view }}</b-td>
                                                    </b-tr>
                                                    <b-tr>
                                                        <b-td>{{ generalist.ophthal_od ? generalist.ophthal_od.fundus : '-'}}</b-td>
                                                        <b-th class="text-center bg-primary-100">Fundus</b-th>
                                                        <b-td>{{ generalist.ophthal_os ? generalist.ophthal_os.fundus : '-'}}</b-td>
                                                    </b-tr>
                                                </b-tbody>
                                            </b-table-simple>
                                        </template>

                                        <div class="mt-12">
                                            <table class="w-100">
                                                <tbody>
                                                    <tr>
                                                        <td class="align-top" style="width: 200px;">Pemeriksaan Fisik Lainnya</td>
                                                        <td style="width: 30px;" class="align-top px-3">:</td>
                                                        <td>
                                                            <div v-html="parsedBody(result.soap_o_anythingelse ? result.soap_o_anythingelse : '-')"></div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </section>

                                <section v-if="result.doctor_in_charge_id" class="mt-32">
                                    <ModernLabel :label="`${ result.employee_doctor_name } (Dokter DPJP)`" />
                                    <div class="col mt-12">
                                        <section>
                                            <div class="font-weight-bold">LABORATORIUM {{ IGD_doctor.laboratory_list.length != 0 ? '' : ': -' }}</div>
                                            <template v-if="IGD_doctor.laboratory_list">
                                                <div v-for="(item, i) in IGD_doctor.laboratory_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                    <div>No Order : {{ item.no_request ? item.no_request : '-' }}</div>
                                                    <div v-if="item.laboratory_request_item">
                                                        <li v-for="(list, j) in item.laboratory_request_item" :key="j">{{ list.costing_name }}</li>
                                                    </div>
                                                </div>
                                            </template>
                                        </section>
                                        <section class="mt-20">
                                            <div class="font-weight-bold">RADIOLOGI {{ IGD_doctor.radiology_list.length != 0 ? '' : ': -' }}</div>
                                            <template v-if="IGD_doctor.radiology_list">
                                                <div v-for="(item, i) in IGD_doctor.radiology_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                    <div>No Order : {{ item.no_request ? item.no_request : '-' }}</div>
                                                    <div v-if="item.radiology_request_item">
                                                        <li v-for="(list, j) in item.radiology_request_item" :key="j">{{ list.costing_name }}</li>
                                                    </div>
                                                </div>
                                            </template>
                                        </section>
                                        <section class="mt-20">
                                            <div class="font-weight-bold">TINDAKAN (ICD9CM) {{ IGD_doctor.soap_p_list ? '' : ': -' }}</div>
                                            <template v-if="IGD_doctor.soap_p_list">
                                                <div class="mb-12">
                                                    <div>
                                                        <div>Tindakan : {{ IGD_doctor.soap_p_list.icd9cm ? '' : '-' }}</div>
                                                        <div v-if="IGD_doctor.soap_p_list.icd9cm">
                                                            <li v-for="(list, j) in IGD_doctor.soap_p_list.icd9cm" :key="j">{{ list.code }} {{ list.name }}</li>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex mt-1">
                                                        <div class="mr-2">Catatan: </div>
                                                        <div v-html="parsedBody(IGD_doctor.soap_p_list.therapy_plan ? IGD_doctor.soap_p_list.therapy_plan : '-')"></div>
                                                    </div>
                                                </div>
                                            </template>
                                        </section>
                                        <section class="mt-20">
                                            <div class="font-weight-bold">PROSEDUR {{ IGD_doctor.transaction_list.length != 0 ? '' : ': -' }}</div>
                                            <div v-if="IGD_doctor.transaction_list">
                                                <li v-for="(item, i) in IGD_doctor.transaction_list" :key="i">{{ item.costing_name }}, jumlah: {{ item.var_2 }}</li>
                                            </div>
                                        </section>
                                        <section class="mt-8">
                                            <div class="font-weight-bold">RESEP {{ IGD_doctor.prescription_list.length != 0 ? '' : ': -' }}</div>
                                            <template v-if="IGD_doctor.prescription_list">
                                                <div v-for="(item, i) in IGD_doctor.prescription_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                    <div>No Order : {{ item.no_order? item.no_order: '-' }}</div>
                                                    <div v-if="item.form_prescription">
                                                        <li v-for="(list, j) in item.form_prescription" :key="j">{{ list.pharmacy_catalog_name }}, {{ list.used }}</li>
                                                    </div>
                                                </div>
                                            </template>
                                        </section>
                                    </div>
                                </section>

                                <section v-if="result.doctor_in_charge_id_1" class="mt-32">
                                    <ModernLabel :label="`${ result.doctor_in_charge_name_1 } (Dokter Spesialis)`" />
                                    <div class="col mt-12">
                                        <section>
                                            <div class="font-weight-bold">LABORATORIUM {{ FIRST_doctor.laboratory_list.length != 0 ? '' : ': -' }}</div>
                                            <template v-if="FIRST_doctor.laboratory_list">
                                                <div v-for="(item, i) in FIRST_doctor.laboratory_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                    <div>No Order : {{ item.no_request ? item.no_request : '-' }}</div>
                                                    <div v-if="item.laboratory_request_item">
                                                        <li v-for="(list, j) in item.laboratory_request_item" :key="j">{{ list.costing_name }}</li>
                                                    </div>
                                                </div>
                                            </template>
                                        </section>
                                        <section class="mt-20">
                                            <div class="font-weight-bold">RADIOLOGI {{ FIRST_doctor.radiology_list.length != 0 ? '' : ': -' }}</div>
                                            <template v-if="FIRST_doctor.radiology_list">
                                                <div v-for="(item, i) in FIRST_doctor.radiology_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                    <div>No Order : {{ item.no_request ? item.no_request : '-' }}</div>
                                                    <div v-if="item.radiology_request_item">
                                                        <li v-for="(list, j) in item.radiology_request_item" :key="j">{{ list.costing_name }}</li>
                                                    </div>
                                                </div>
                                            </template>
                                        </section>
                                        <section class="mt-20">
                                            <div class="font-weight-bold">TINDAKAN (ICD9CM) {{ FIRST_doctor.soap_p_list ? '' : ': -' }}</div>
                                            <template v-if="FIRST_doctor.soap_p_list">
                                                <div class="mb-12">
                                                    <div>
                                                        <div>Tindakan : {{ FIRST_doctor.soap_p_list.icd9cm ? '' : '-' }}</div>
                                                        <div v-if="FIRST_doctor.soap_p_list.icd9cm">
                                                            <li v-for="(list, j) in FIRST_doctor.soap_p_list.icd9cm" :key="j">{{ list.code }} {{ list.name }}</li>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex mt-1">
                                                        <div class="mr-2">Catatan: </div>
                                                        <div v-html="parsedBody(FIRST_doctor.soap_p_list.therapy_plan ? FIRST_doctor.soap_p_list.therapy_plan : '-')"></div>
                                                    </div>
                                                </div>
                                            </template>
                                        </section>
                                        <section class="mt-20">
                                            <div class="font-weight-bold">PROSEDUR {{ FIRST_doctor.transaction_list.length != 0 ? '' : ': -' }}</div>
                                            <div v-if="FIRST_doctor.transaction_list">
                                                <li v-for="(item, i) in FIRST_doctor.transaction_list" :key="i">{{ item.costing_name }}, jumlah: {{ item.var_2 }}</li>
                                            </div>
                                        </section>
                                        <section class="mt-20">
                                            <div class="font-weight-bold">RESEP {{ FIRST_doctor.prescription_list.length != 0 ? '' : ': -' }}</div>
                                            <template v-if="FIRST_doctor.prescription_list">
                                                <div v-for="(item, i) in FIRST_doctor.prescription_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                    <div>No Order: {{ item.no_order? item.no_order: '-' }}</div>
                                                    <div v-if="item.form_prescription">
                                                        <li v-for="(list, j) in item.form_prescription" :key="j">{{ list.pharmacy_catalog_name }}, {{ list.used }}</li>
                                                    </div>
                                                </div>
                                            </template>
                                        </section>
                                    </div>
                                </section>

                                <section v-if="result.doctor_in_charge_id_2" class="mt-32">
                                    <ModernLabel :label="`${ result.doctor_in_charge_name_2 } (Dokter Spesialis)`" />
                                    <div class="col mt-12">
                                        <section class="mt-8">
                                            <div class="font-weight-bold">LABORATORIUM {{ SECOND_doctor.laboratory_list.length != 0 ? '' : ': -' }}</div>
                                            <template v-if="SECOND_doctor.laboratory_list">
                                                <div v-for="(item, i) in SECOND_doctor.laboratory_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                    <div>No Order : {{ item.no_request ? item.no_request : '-' }}</div>
                                                    <div v-if="item.laboratory_request_item">
                                                        <li v-for="(list, j) in item.laboratory_request_item" :key="j">{{ list.costing_name }}</li>
                                                    </div>
                                                </div>
                                            </template>
                                        </section>
                                        <section class="mt-20">
                                            <div class="font-weight-bold">RADIOLOGI {{ SECOND_doctor.radiology_list.length != 0 ? '' : ': -' }}</div>
                                            <template v-if="SECOND_doctor.radiology_list">
                                                <div v-for="(item, i) in SECOND_doctor.radiology_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                    <div>No Order : {{ item.no_request ? item.no_request : '-' }}</div>
                                                    <div v-if="item.radiology_request_item">
                                                        <li v-for="(list, j) in item.radiology_request_item" :key="j">{{ list.costing_name }}</li>
                                                    </div>
                                                </div>
                                            </template>
                                        </section>
                                        <section class="mt-20">
                                            <div class="font-weight-bold">TINDAKAN (ICD9CM) {{ SECOND_doctor.soap_p_list ? '' : ': -' }}</div>
                                            <template v-if="SECOND_doctor.soap_p_list">
                                                <div class="mb-12">
                                                    <div>
                                                        <div>Tindakan : {{ SECOND_doctor.soap_p_list.icd9cm ? '' : '-' }}</div>
                                                        <div v-if="SECOND_doctor.soap_p_list.icd9cm">
                                                            <li v-for="(list, j) in SECOND_doctor.soap_p_list.icd9cm" :key="j">{{ list.code }} {{ list.name }}</li>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex mt-1">
                                                        <div class="mr-2">Catatan: </div>
                                                        <div v-html="parsedBody(SECOND_doctor.soap_p_list.therapy_plan ? SECOND_doctor.soap_p_list.therapy_plan : '-')"></div>
                                                    </div>
                                                </div>
                                            </template>
                                        </section>
                                        <section class="mt-20">
                                            <div class="font-weight-bold">PROSEDUR {{ SECOND_doctor.transaction_list.length != 0 ? '' : ': -' }}</div>
                                            <div v-if="SECOND_doctor.transaction_list">
                                                <li v-for="(item, i) in SECOND_doctor.transaction_list" :key="i">{{ item.costing_name }}, jumlah: {{ item.var_2 }}</li>
                                            </div>
                                        </section>
                                        <section class="mt-20">
                                            <div class="font-weight-bold">RESEP {{ SECOND_doctor.prescription_list.length != 0 ? '' : ': -' }}</div>
                                            <template v-if="SECOND_doctor.prescription_list">
                                                <div v-for="(item, i) in SECOND_doctor.prescription_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                    <div>No Order: {{ item.no_order? item.no_order: '-' }}</div>
                                                    <div v-if="item.form_prescription">
                                                        <li v-for="(list, j) in item.form_prescription" :key="j">{{ list.pharmacy_catalog_name }}, {{ list.used }}</li>
                                                    </div>
                                                </div>
                                            </template>
                                        </section>
                                    </div>
                                </section>

                                <section v-if="result.doctor_in_charge_id_3" class="mt-32">
                                    <ModernLabel :label="`${ result.doctor_in_charge_name_3 } (Dokter Spesialis)`" />
                                    <div class="col mt-12">
                                        <section class="mt-8">
                                            <div class="font-weight-bold">LABORATORIUM {{ THIRD_doctor.laboratory_list.length != 0 ? '' : ': -' }}</div>
                                            <template v-if="THIRD_doctor.laboratory_list">
                                                <div v-for="(item, i) in THIRD_doctor.laboratory_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                    <div>No Order : {{ item.no_request ? item.no_request : '-' }}</div>
                                                    <div v-if="item.laboratory_request_item">
                                                        <li v-for="(list, j) in item.laboratory_request_item" :key="j">{{ list.costing_name }}</li>
                                                    </div>
                                                </div>
                                            </template>
                                        </section>
                                        <section class="mt-20">
                                            <div class="font-weight-bold">RADIOLOGI {{ THIRD_doctor.radiology_list.length != 0 ? '' : ': -' }}</div>
                                            <template v-if="THIRD_doctor.radiology_list">
                                                <div v-for="(item, i) in THIRD_doctor.radiology_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                    <div>No Order : {{ item.no_request ? item.no_request : '-' }}</div>
                                                    <div v-if="item.radiology_request_item">
                                                        <li v-for="(list, j) in item.radiology_request_item" :key="j">{{ list.costing_name }}</li>
                                                    </div>
                                                </div>
                                            </template>
                                        </section>
                                        <section class="mt-20">
                                            <div class="font-weight-bold">TINDAKAN (ICD9CM) {{ THIRD_doctor.soap_p_list ? '' : ': -' }}</div>
                                            <template v-if="THIRD_doctor.soap_p_list">
                                                <div class="mb-12">
                                                    <div>
                                                        <div>Tindakan : {{ THIRD_doctor.soap_p_list.icd9cm ? '' : '-' }}</div>
                                                        <div v-if="THIRD_doctor.soap_p_list.icd9cm">
                                                            <li v-for="(list, j) in THIRD_doctor.soap_p_list.icd9cm" :key="j">{{ list.code }} {{ list.name }}</li>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex mt-1">
                                                        <div class="mr-2">Catatan: </div>
                                                        <div v-html="parsedBody(THIRD_doctor.soap_p_list.therapy_plan ? THIRD_doctor.soap_p_list.therapy_plan : '-')"></div>
                                                    </div>
                                                </div>
                                            </template>
                                        </section>
                                        <section class="mt-20">
                                            <div class="font-weight-bold">PROSEDUR {{ THIRD_doctor.transaction_list.length != 0 ? '' : ': -' }}</div>
                                            <div v-if="THIRD_doctor.transaction_list">
                                                <li v-for="(item, i) in THIRD_doctor.transaction_list" :key="i">{{ item.costing_name }}, jumlah: {{ item.var_2 }}</li>
                                            </div>
                                        </section>
                                        <section class="mt-20">
                                            <div class="font-weight-bold">RESEP {{ THIRD_doctor.prescription_list.length != 0 ? '' : ': -' }}</div>
                                            <template v-if="THIRD_doctor.prescription_list">
                                                <div v-for="(item, i) in THIRD_doctor.prescription_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                    <div>No Order: {{ item.no_order? item.no_order: '-' }}</div>
                                                    <div v-if="item.form_prescription">
                                                        <li v-for="(list, j) in item.form_prescription" :key="j">{{ list.pharmacy_catalog_name }}, {{ list.used }}</li>
                                                    </div>
                                                </div>
                                            </template>
                                        </section>
                                    </div>
                                </section>

                                <section v-if="result.doctor_in_charge_id_4" class="mt-32">
                                    <ModernLabel :label="`${ result.doctor_in_charge_name_4 } (Dokter Spesialis)`" />
                                    <div class="col mt-12">
                                        <section class="mt-8">
                                            <div class="font-weight-bold">LABORATORIUM {{ FOURTH_doctor.laboratory_list.length != 0 ? '' : ': -' }}</div>
                                            <template v-if="FOURTH_doctor.laboratory_list">
                                                <div v-for="(item, i) in FOURTH_doctor.laboratory_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                    <div>No Order : {{ item.no_request ? item.no_request : '-' }}</div>
                                                    <div v-if="item.laboratory_request_item">
                                                        <li v-for="(list, j) in item.laboratory_request_item" :key="j">{{ list.costing_name }}</li>
                                                    </div>
                                                </div>
                                            </template>
                                        </section>
                                        <section class="mt-20">
                                            <div class="font-weight-bold">RADIOLOGI {{ FOURTH_doctor.radiology_list.length != 0 ? '' : ': -' }}</div>
                                            <template v-if="FOURTH_doctor.radiology_list">
                                                <div v-for="(item, i) in FOURTH_doctor.radiology_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                    <div>No Order : {{ item.no_request ? item.no_request : '-' }}</div>
                                                    <div v-if="item.radiology_request_item">
                                                        <li v-for="(list, j) in item.radiology_request_item" :key="j">{{ list.costing_name }}</li>
                                                    </div>
                                                </div>
                                            </template>
                                        </section>
                                        <section class="mt-20">
                                            <div class="font-weight-bold">TINDAKAN (ICD9CM) {{ FOURTH_doctor.soap_p_list ? '' : ': -' }}</div>
                                            <template v-if="FOURTH_doctor.soap_p_list">
                                                <div class="mb-12">
                                                    <div>
                                                        <div>Tindakan : {{ FOURTH_doctor.soap_p_list.icd9cm ? '' : '-' }}</div>
                                                        <div v-if="FOURTH_doctor.soap_p_list.icd9cm">
                                                            <li v-for="(list, j) in FOURTH_doctor.soap_p_list.icd9cm" :key="j">{{ list.code }} {{ list.name }}</li>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex mt-1">
                                                        <div class="mr-2">Catatan: </div>
                                                        <div v-html="parsedBody(FOURTH_doctor.soap_p_list.therapy_plan ? FOURTH_doctor.soap_p_list.therapy_plan : '-')"></div>
                                                    </div>
                                                </div>
                                            </template>
                                        </section>
                                        <section class="mt-20">
                                            <div class="font-weight-bold">PROSEDUR {{ FOURTH_doctor.transaction_list.length != 0 ? '' : ': -' }}</div>
                                            <div v-if="FOURTH_doctor.transaction_list">
                                                <li v-for="(item, i) in FOURTH_doctor.transaction_list" :key="i">{{ item.costing_name }}, jumlah: {{ item.var_2 }}</li>
                                            </div>
                                        </section>
                                        <section class="mt-20">
                                            <div class="font-weight-bold">RESEP {{ FOURTH_doctor.prescription_list.length != 0 ? '' : ': -' }}</div>
                                            <template v-if="FOURTH_doctor.prescription_list">
                                                <div v-for="(item, i) in FOURTH_doctor.prescription_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                    <div>No Order: {{ item.no_order? item.no_order: '-' }}</div>
                                                    <div v-if="item.form_prescription">
                                                        <li v-for="(list, j) in item.form_prescription" :key="j">{{ list.pharmacy_catalog_name }}, {{ list.used }}</li>
                                                    </div>
                                                </div>
                                            </template>
                                        </section>
                                    </div>
                                </section>

                                <section v-if="result.doctor_in_charge_id_5" class="mt-32">
                                    <ModernLabel :label="`${ result.doctor_in_charge_name_5 } (Dokter Spesialis)`" />
                                    <div class="col mt-12">
                                        <section class="mt-8">
                                            <div class="font-weight-bold">LABORATORIUM {{ FIFTH_doctor.laboratory_list.length != 0 ? '' : ': -' }}</div>
                                            <template v-if="FIFTH_doctor.laboratory_list">
                                                <div v-for="(item, i) in FIFTH_doctor.laboratory_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                    <div>No Order : {{ item.no_request ? item.no_request : '-' }}</div>
                                                    <div v-if="item.laboratory_request_item">
                                                        <li v-for="(list, j) in item.laboratory_request_item" :key="j">{{ list.costing_name }}</li>
                                                    </div>
                                                </div>
                                            </template>
                                        </section>
                                        <section class="mt-20">
                                            <div class="font-weight-bold">RADIOLOGI {{ FIFTH_doctor.radiology_list.length != 0 ? '' : ': -' }}</div>
                                            <template v-if="FIFTH_doctor.radiology_list">
                                                <div v-for="(item, i) in FIFTH_doctor.radiology_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                    <div>No Order : {{ item.no_request ? item.no_request : '-' }}</div>
                                                    <div v-if="item.radiology_request_item">
                                                        <li v-for="(list, j) in item.radiology_request_item" :key="j">{{ list.costing_name }}</li>
                                                    </div>
                                                </div>
                                            </template>
                                        </section>
                                        <section class="mt-20">
                                            <div class="font-weight-bold">TINDAKAN (ICD9CM) {{ FIFTH_doctor.soap_p_list ? '' : ': -' }}</div>
                                            <template v-if="FIFTH_doctor.soap_p_list">
                                                <div class="mb-12">
                                                    <div>
                                                        <div>Tindakan : {{ FIFTH_doctor.soap_p_list.icd9cm ? '' : '-' }}</div>
                                                        <div v-if="FIFTH_doctor.soap_p_list.icd9cm">
                                                            <li v-for="(list, j) in FIFTH_doctor.soap_p_list.icd9cm" :key="j">{{ list.code }} {{ list.name }}</li>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex mt-1">
                                                        <div class="mr-2">Catatan: </div>
                                                        <div v-html="parsedBody(FIFTH_doctor.soap_p_list.therapy_plan ? FIFTH_doctor.soap_p_list.therapy_plan : '-')"></div>
                                                    </div>
                                                </div>
                                            </template>
                                        </section>
                                        <section class="mt-20">
                                            <div class="font-weight-bold">PROSEDUR {{ FIFTH_doctor.transaction_list.length != 0 ? '' : ': -' }}</div>
                                            <div v-if="FIFTH_doctor.transaction_list">
                                                <li v-for="(item, i) in FIFTH_doctor.transaction_list" :key="i">{{ item.costing_name }}, jumlah: {{ item.var_2 }}</li>
                                            </div>
                                        </section>
                                        <section class="mt-20">
                                            <div class="font-weight-bold">RESEP {{ FIFTH_doctor.prescription_list.length != 0 ? '' : ': -' }}</div>
                                            <template v-if="FIFTH_doctor.prescription_list">
                                                <div v-for="(item, i) in FIFTH_doctor.prescription_list" :key="i" :class="i == 0 ? 'mt-1' : 'mt-16'">
                                                    <div>No Order: {{ item.no_order? item.no_order: '-' }}</div>
                                                    <div v-if="item.form_prescription">
                                                        <li v-for="(list, j) in item.form_prescription" :key="j">{{ list.pharmacy_catalog_name }}, {{ list.used }}</li>
                                                    </div>
                                                </div>
                                            </template>
                                        </section>
                                    </div>
                                </section>
                            </div>
                        </section>

                        <div class="d-flex justify-content-end w-100 mt-20">
                            <div class="col-5 text-center">
                                <div :class="result.employee_signature ? '' : 'mb-64'" >DPJP</div>
                                <template v-if="result.employee_signature">
                                    <b-img :src="urlbe+result.employee_signature" style="width: 120px;"/>
                                </template>
                                <div>( {{ this.result.employee_doctor_name }} )</div>
                            </div>
                        </div>

                    </template>

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
                                                <!-- <b>Skor Nyeri: </b>{{ this.result.sore_score ? this.result.sore_score : '-' }},
                                                <b>EWS: </b>{{ this.result.ews ? this.result.ews : '-' }},
                                                <b>Gula Darah: </b>{{ this.result.blood_sugar ? this.result.blood_sugar : '-' }} mg/dL, -->
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
                            <b-td colspan="5" >
                                <div class="font-weight-bold" >CATATAN ICD9CM</div>
                            </b-td>
                            <!-- <b-td></b-td> -->
                        </b-tr>
                        <template>
                            <b-tr>
                                <b-td colspan="5">{{result.therapy_plan}}</b-td>
                                <!-- <b-td></b-td> -->
                            </b-tr>
                        </template>
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

                    <template v-if="resumeData" style="margin-top: 800px;">
                        <div class="row align-items-center pb-40" style="border-bottom: 3px solid black;margin-top: 800px;">
                            <div class="col-2 text-center">
                                <img src="../../../../../@core/assets/images/logo-thb-small.png" width="60%" alt="rs-logo">
                            </div>
                            <div class="col text-center">
                                <p class="text-bold-18">RS Taman Harapan Baru</p>
                                <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                                <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                            </div>
                            <div class="col-2 text-center mr-16">
                                <img src="../../../../../@core/assets/images/lafki-logo.png" width="50%" alt="lafki-logo">
                                <img src="../../../../../@core/assets/images/apexa.png" width="50%" alt="lafki-logo">
                            </div>
                        </div>
                        <section class="mt-32">
                            <div class="text-center">
                                <div class="font-weight-bold">RESUME MEDIS PASIEN PULANG</div>
                                <div class="text-regular-12"><i>(Discharge Summary)</i></div>
                            </div>
                            <div class="row mt-16">
                                <div class="col-6">
                                    <table class="mt-16">
                                        <tr>
                                            <td class="col-3" style="vertical-align: top; white-space: nowrap;">Tanggal Masuk (<i>Admission Date</i>)</td>
                                            <td class="col-1 text-right">:</td>
                                            <td>{{ tgl_masuk(regData.register_date) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="col-3" style="vertical-align: top;">Tanggal Keluar (<i>Discharge Date</i>)</td>
                                            <td class="col-1 text-right">:</td>
                                            <td>{{ regData.checkout_date }}</td>
                                        </tr>
                                        <tr>
                                            <td class="col-3" style="vertical-align: top; white-space: nowrap;">Ruang Rawat Terakhir (<i>Last Ward</i>)</td>
                                            <td class="col-1 text-right">:</td>
                                            <td>{{ regData.room_name }}</td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-6">
                                    <table class="mt-16">
                                        <tr>
                                            <td class="col-3" style="vertical-align: top; white-space: nowrap;">Nama</td>
                                            <td class="col-1 text-right">:</td>
                                            <td>{{regData.alias_name}}. {{ regData.fullname }}</td>
                                        </tr>
                                        <tr>
                                            <td class="col-3" style="vertical-align: top;">No. RM</td>
                                            <td class="col-1 text-right">:</td>
                                            <td>{{ regData.norm }}</td>
                                        </tr>
                                        <tr>
                                            <td class="col-3" style="vertical-align: top; white-space: nowrap;">Tanggal Lahir</td>
                                            <td class="col-1 text-right">:</td>
                                            <td>{{ localDate(regData.birth_date) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </section>
                        <div class="mt-24 p-16" style="border: 4px solid black;">
                            <ol type="1">
                                <li class="mt-32">
                                    <div>
                                        <div class="font-weight-bold">Indikasi Rawat Inap</div>
                                        <div class="text-regular-14"><i>(Indications for Hospitalization)</i></div>
                                    </div>
                                    <div v-html="parsedBody(resumeData?.indication_for_hospitalization ? resumeData?.indication_for_hospitalization : '-')"class="mt-12"></div>
                                </li>

                                <li class="mt-32">
                                    <div>
                                        <div class="font-weight-bold">Riwayat Ringkasan Penyakit</div>
                                        <div class="text-regular-14"><i>(History of Desease - Summary)</i></div>
                                    </div>
                                    <div v-html="parsedBody(resumeData?.summary_medical_history ? resumeData?.summary_medical_history : '-')"class="mt-12"></div>
                                </li>
                                <li class="mt-32">
                                    <div>
                                        <div class="font-weight-bold">Hasil Pemeriksaan Fisik Penting dan Temuan Lain</div>
                                        <div class="text-regular-14"><i>(Significant physical and other findings)</i></div>
                                    </div>
                                    <div v-html="parsedBody(resumeData?.physical_check ? resumeData?.physical_check : '-')"class="mt-12"></div>
                                    <div v-if="meaningful?.length > 0" class="row mt-12">
                                        <!-- <div class="font-weight-bold text-w3green">ST.GENERALIS BERMAKNA:</div> -->
                                        <div v-for="(column, colIndex) in chunkedData" :key="colIndex" :class="`col-${12 / chunkedData.length}`">
                                            <div v-for="(item, index) in column" :key="index" class="d-flex mb-2">
                                                <div style="min-width: 140px;">{{ item.key }}</div>
                                                <div style="width: 10px;" class="text-center">:</div>
                                                <div class="flex-grow-1">{{ item.value }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                    <li class="mt-32">
                                    <div>
                                        <div class="font-weight-bold">Diagnosis Utama (ICD10)</div>
                                        <div class="text-regular-14"><i>(Diagnose)</i></div>
                                    </div>
                                    <div class="mt-12">
                                        <template v-for="(item, index) in resumeData.icd10">
                                            <div :key="index" v-if="index == 0">
                                                <div>{{ item.icd10_code }} {{ item.icd10_name }}</div>
                                            </div>
                                        </template>
                                        <div>
                                            <div v-html="parsedBody(resumeData.icd10_notes ? resumeData.icd10_notes : '-')"class="mt-12"></div>
                                        </div>
                                    </div>
                                </li>
                                <li class="mt-32">
                                    <div>
                                        <div class="font-weight-bold">Diagnosis Tambahan (ICD10)</div>
                                        <div class="text-regular-14"><i>(Comorbidities)</i></div>
                                    </div>
                                    <div class="mt-12">
                                        <template v-for="(item, index) in resumeData.icd10">
                                            <div :key="index" v-if="index > 0">
                                                <div>{{ item.icd10_code }} {{ item.icd10_name }}</div>
                                            </div>
                                        </template>
                                    </div>
                                </li>
                                <li class="mt-32">
                                    <div>
                                        <div class="font-weight-bold">Prosedur Diagnostik dan Terapeutik yang dilakukan (ICD9CM)</div>
                                        <div class="text-regular-14"><i>(Diagnostic and Therapeutik procedures performed)</i></div>
                                    </div>
                                    <div class="mt-12">
                                        <template v-if="resumeData.icd9cm">
                                            <div v-for="(item, index) in resumeData.icd9cm" :key="index">{{ item.icd9cm_code }} {{ item.icd9cm_name }}</div>
                                        </template>
                                    </div>
                                </li>
                                <li class="mt-32">
                                    <div>
                                        <div class="font-weight-bold">Penunjang</div>
                                        <!-- <div class="text-regular-14"><i>(Diagnostic and Therapeutik procedures performed)</i></div> -->
                                    </div>
                                    <div class="mt-12">
                                        <div class="text-bold-14">Laboratorium : <span>{{ !resumeData.laboratory_request_items ? '-' : '' }}</span></div>
                                        <template v-if="resumeData.laboratory_request_items">
                                            <div v-for="(item, index) in resumeData.laboratory_request_items" :key="index">
                                                <template v-if="item?.result.length > 0">
                                                    <div v-for="(data, index) in item.result" :key="index">
                                                        <template v-if="data.Parameter && data.hasil">
                                                            <div v-if="data.flag.toLowerCase() == 'low' || data.flag.toLowerCase() == 'high'">
                                                                {{ item.costing_name }}: {{ data.Parameter}} {{data.hasil}} {{data.satuan}}
                                                            </div>
                                                            <div v-else>
                                                                {{ item.costing_name }}: {{ data.Parameter}} Dalam Batas Normal (DBN)
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                                <div v-else>
                                                    {{ item.costing_name }}
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="mt-12">
                                        <div class="text-bold-14">Radiology : <span>{{ !resumeData.radiology_request_items ? '-' : '' }}</span></div>
                                        <template v-if="resumeData.radiology_request_items">
                                            <div v-for="(item, index) in resumeData.radiology_request_items" :key="index">{{ item.costing_name }}</div>
                                        </template>
                                    </div>
                                </li>
                                <li class="mt-32">
                                    <div>
                                        <div class="font-weight-bold">Obat-obatan Penting yang diberikan selama perawatan</div>
                                        <div class="text-regular-14"><i>(Significant administered medications during hospitalization)</i></div>
                                    </div>
                                    <div v-html="parsedBody(resumeData.significant_medications ? resumeData.significant_medications : '-')"class="mt-12"></div>
                                </li>
                                <li class="mt-32">
                                    <div>
                                        <div class="font-weight-bold">Obat yang dibawa pulang</div>
                                        <div class="text-regular-14"><i>(Take-home medications)</i></div>
                                    </div>
                                    <div v-html="parsedBody(resumeData.take_home_medications ? resumeData.take_home_medications : '-')"class="mt-12"></div>
                                </li>
                                <li class="mt-32">
                                    <div class="font-weight-bold">Cara pulang *</div>
                                    <div class="text-regular-14"><i>(Reason for Discharge)</i></div>

                                    <div class="d-flex mt-12 text-regular-16">
                                        <div class="d-flex align-items-center pr-3">
                                            <b-icon :icon="getRadioReasonIcon('4a6d28c4-e020-425f-91e1-d39699cf679b')" :variant="getRadioReasonIconVariant('4a6d28c4-e020-425f-91e1-d39699cf679b')" class="mr-8"/>
                                            <div class="lh-110">Meninggal</div>
                                        </div>
                                        <div class="d-flex align-items-center pr-3">
                                            <b-icon :icon="getRadioReasonIcon('807ca005-5827-4e19-9c97-0bc3d4e924ca')" :variant="getRadioReasonIconVariant('807ca005-5827-4e19-9c97-0bc3d4e924ca')" class="mr-8" />
                                            <div class="lh-110">Atas Persetujuan Dokter</div>
                                        </div>
                                        <div class="d-flex align-items-center pr-3">
                                            <b-icon :icon="getRadioReasonIcon('a1631acc-6a94-414d-95de-a9faea0f1c2f')" :variant="getRadioReasonIconVariant('a1631acc-6a94-414d-95de-a9faea0f1c2f')" class="mr-8" />
                                            <div class="lh-110">Dirujuk</div>
                                        </div>
                                        <div class="d-flex align-items-center pr-3">
                                            <b-icon :icon="getRadioReasonIcon('ae0aabfa-1600-443f-8a60-62f9e76b1608')" :variant="getRadioReasonIconVariant('ae0aabfa-1600-443f-8a60-62f9e76b1608')" class="mr-8" />
                                            <div class="lh-110">Permintaan Sendiri</div>
                                        </div>
                                        <div class="d-flex align-items-center pr-3">
                                            <b-icon :icon="getRadioReasonIcon('bbc9fdce-affb-4b32-a386-0ed7e91c1e89')" :variant="getRadioReasonIconVariant('bbc9fdce-affb-4b32-a386-0ed7e91c1e89')" class="mr-8" />
                                            <div class="lh-110">Pulang Non Ranap</div>
                                        </div>
                                        <div class="d-flex align-items-center pr-3">
                                            <b-icon :icon="getRadioReasonIcon('adb7ce0b-ede3-4fee-b273-ce159d286732')" :variant="getRadioReasonIconVariant('adb7ce0b-ede3-4fee-b273-ce159d286732')" class="mr-8" />
                                            <div class="lh-110">Lainnya: <span v-if="resumeData.exit_id == 'adb7ce0b-ede3-4fee-b273-ce159d286732'">{{ resumeData.exit_anythingelse }}</span></div>
                                        </div>
                                    </div>
                                </li>
                                <li class="mt-32">
                                    <div class="font-weight-bold">Kondisi saat pulang *</div>
                                    <div class="text-regular-14"><i>(Continuous Care Instruction)</i></div>

                                    <div class="d-flex mt-12 text-regular-16">
                                        <div class="d-flex pr-3">
                                            <b-icon :icon="getRadioConditionIcon('1')" :variant="getRadioConditionIconVariant('1')" class="mt-1 mr-8"/>
                                            <div>
                                                <div class="lh-110">Perbaikan</div>
                                                <div><i>(Discharged with improvement)</i></div>
                                            </div>
                                        </div>
                                        <div class="d-flex pr-3">
                                            <b-icon :icon="getRadioConditionIcon('2')" :variant="getRadioConditionIconVariant('2')" class="mt-1 mr-8" />
                                            <div>
                                                <div class="lh-110">Tidak Sembuh</div>
                                                <div><i>(Not Cured)</i></div>
                                            </div>
                                        </div>
                                        <div class="d-flex pr-3">
                                            <b-icon :icon="getRadioConditionIcon('3')" :variant="getRadioConditionIconVariant('3')" class="mt-1 mr-8" />
                                            <div>
                                                <div class="lh-110">Meninggal < 48 jam</div>
                                                <div><i>(Died < 48 hrs)</i></div>
                                            </div>
                                        </div>
                                        <div class="d-flex pr-3">
                                            <b-icon :icon="getRadioConditionIcon('4')" :variant="getRadioConditionIconVariant('4')" class="mt-1 mr-8" />
                                            <div>
                                                <div class="lh-110">Lainnya: <span v-if="resumeData.exit_condition_type_id == '4'">{{ resumeData.exit_condition_anythingelse }}</span></div>
                                                <div><i>Other</i></div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="mt-32">
                                    <div class="font-weight-bold">Instruksi Rawat Lanjutan</div>
                                    <div class="text-regular-14"><i>(Continuous Care Instruction)</i></div>

                                    <div class="row mt-24">
                                        <div class="col-6 text-regular-14">
                                            <div>
                                                <div class="font-weight-bold">Kontrol Pasca Rawat Inap:</div>
                                                <div><i>(Post-inpatient Control)</i></div>
                                            </div>

                                            <div class="mt-12 text-regular-16">
                                                <div class="d-flex py-1">
                                                    <b-icon :icon="getRadioControlIcon('1')" :variant="getRadioControlIconVariant('1')" class="mt-1 mr-8"/>
                                                    <div>
                                                        <div class="lh-110">RS Taman Harapan Baru</div>
                                                        <div v-if="resumeData.care_instruction_control == 1">Tanggal {{ resumeData.care_instruction_control_date }} {{ resumeData.health_facility_item_name_control ? 'di' : '' }} {{ resumeData.health_facility_item_name_control }}</div>
                                                    </div>
                                                </div>
                                                <div class="d-flex py-1">
                                                    <b-icon :icon="getRadioControlIcon('2')" :variant="getRadioControlIconVariant('2')" class="mt-1 mr-8" />
                                                    <div>
                                                        <div class="lh-110">Dokter Keluarga / Klinik Luar</div>
                                                        <div><i>(Family Phsician / Outer Clinic)</i></div>
                                                    </div>
                                                </div>
                                                <div class="d-flex py-1">
                                                    <b-icon :icon="getRadioControlIcon('3')" :variant="getRadioControlIconVariant('3')" class="mt-1 mr-8" />
                                                    <div>
                                                        <div class="lh-110">Puskesmas</div>
                                                        <div><i>(Primary Health Care)</i></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li class="mt-32">
                                    <div>
                                        <div class="font-weight-bold">Edukasi Selama Di Rumah</div>
                                    </div>
                                    <div v-html="parsedBody(resumeData.home_education ? resumeData.home_education : '-')"class="mt-12"></div>
                                </li>
                            </ol>
                        </div>

                        <div class="d-flex justify-content-between align-items-end mt-24 text-left">
                            <div>
                                <template v-if="globalQRText">
                                    <div id="renderQRR">
                                        <div>Pasien / Keluarga</div>
                                        <qrcode-vue id="qrcodeCanvasRR" :value="globalQRText" :size="70" level="H" ></qrcode-vue>
                                    </div>
                                </template>
                                <div>{{ pesertaData?.peserta ? pesertaData?.peserta.nama : '' }}</div>
                            </div>
                            <div>
                                <div>Bekasi, {{ regData.checkout_date ? localDate(regData.checkout_date) : today }}</div>
                                <div>Dokter Penanggung Jawab Pelayanan</div>
                                <div :class="regData.employee_signature ? '' : 'mb-72'"><i>(Responsible Physician)</i></div>

                                <template v-if="regData.employee_signature">
                                    <b-img :src="urlbe+regData.employee_signature" style="width: 120px;"/>
                                </template>
                                <div>({{ regData.doctor_in_charge_name }})</div>
                                <div>Tanda Tangan dan Nama Jelas</div>
                                <div><i>(Name & Signature)</i></div>

                            </div>
                        </div>

                        <div class="mt-32">
                            <small class="d-block">* Beri tanda centang ( ✔ ) sesuai pilihan, Give check ( ✔ ) sign for suitable option</small>
                            <small class="d-block">Dibuat rangkap 3 ( 1 untuk rekam medis, 1 untuk penjamin dan 1 untuk pasien  )</small>
                        </div>
                    </template>

                    <template v-for="OrderLab in orderData" v-if="orderData.length > 0">
                        <div class="row align-items-center pb-40" style="border-bottom: 2px solid black;margin-top: 2000px;" v-if="OrderLab">
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
                        <div class="mt-20" v-if="OrderLab">
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
                                            <td style="vertical-align:top;">{{regData.idcard}}</td>
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
                                            <td style="vertical-align:top;">{{OrderLab.date_request}}</td>
                                        </tr>
                                        <tr>
                                            <td>Tgl. Hasil <br> <i class="text-regular-12">Date Reported</i></td>
                                            <td style="vertical-align:top;" class="px-3">:</td>
                                            <td style="vertical-align:top;">{{OrderLab.date_result}}</td>
                                        </tr>
                                        <!-- <tr>
                                            <td>Tgl. Cetak <br> <i class="text-regular-12">Print Out Date</i></td>
                                            <td style="vertical-align:top;" class="px-3">:</td>
                                            <td style="vertical-align:top;">{{OrderLab.date_request}}</td>
                                        </tr> -->
                                    </table>
                                </div>
                                <div class="col-6">
                                    <table class="text-regular-14">
                                        <tr>
                                            <td>No. Lab <br> <i class="text-regular-12">Lab Number</i></td>
                                            <td style="vertical-align:top;" class="px-3">:</td>
                                            <td style="vertical-align:top;">{{OrderLab.no_request}}</td>
                                        </tr>
                                        <tr>
                                            <td>Tanggal Lahir <br> <i class="text-regular-12">Date of Birth</i></td>
                                            <td style="vertical-align:top;" class="px-3">:</td>
                                            <td style="vertical-align:top;">{{regData?.birth_date}}</td>
                                        </tr>
                                        <tr>
                                            <td>Umur <br> <i class="text-regular-12">Age</i></td>
                                            <td style="vertical-align:top;" class="px-3">:</td>
                                            <td style="vertical-align:top;">{{parsed_age(regData?.birth_date)}}</td>
                                        </tr>
                                        <tr>
                                            <td>Kelamin <br> <i class="text-regular-12">Sex</i></td>
                                            <td style="vertical-align:top;" class="px-3">:</td>
                                            <td style="vertical-align:top;">{{regData.sex_name}}</td>
                                        </tr>
                                        <tr>
                                            <td>Dokter Pengirim <br> <i class="text-regular-12">Reffering Doctor</i></td>
                                            <td style="vertical-align:top;" class="px-3">:</td>
                                            <td style="vertical-align:top;">{{OrderLab.doctor_in_charge_name}}</td>
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
                                <template v-for="item in resultItems">
                                    <b-table v-if="item.order_id == OrderLab.id" bordered responsive head-variant="light" hover small :fields="resultFields"
                                        :items="item.result" tbody-tr-class="text-medium-14" class="mt-12 text-center">
                                    </b-table>
                                </template>
                                <!-- <template v-for="OrderLabItem in OrderLab.laboratory_request_items">
                                    <b-table bordered responsive head-variant="light" hover small :fields="resultFields"
                                        :items="OrderLabItem.result" tbody-tr-class="text-medium-14" class="mt-12 text-center">
                                    </b-table>
                                </template> -->
                            </div>
                        </div>
                    </template>
                    <template v-for="OrderRad in orderDataRad" v-if="orderDataRad.length > 0">
                        <div class="row align-items-center pb-40" style="border-bottom: 2px solid black;margin-top: 3000px;" v-if="OrderRad && OrderRad?.result_done == 1">
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
                        <div class="mt-20" v-if="OrderRad && OrderRad?.result_done == 1">
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
                                            <td>{{OrderRad.date_request ? localDate(OrderRad.date_request) : ''}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Tanggal Hasil</td>
                                            <td class="px-3">:</td>
                                            <td>
                                            <template v-if="OrderRad.radiology_request_items">
                                                <template v-for="list in OrderRad.radiology_request_items[0].url">
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
                                            <td>{{OrderRad.no_request}}</td>
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
                                <div class="mt-12">{{ OrderRad.radiology_request_items[0].costing_name }}</div>
                                <template v-if="OrderRad.radiology_request_items[0].url">
                                    <template v-for="list in OrderRad.radiology_request_items[0].url">
                                        <template v-if="list.status !== 1 && list.conclusion !== null">
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
                                                    <div>Salam Sejawat</div>
                                                    <b-img :src="urlbe+'assets/document/ttd/ttd-dr-indri.png'" style="width: 220px;" />
                                                    <!-- <div>{{ list.ttd_name }}</div> -->
                                                    <div>{{ list.doctor_lab_name }}</div>
                                                </div>
                                            </div>
                                        </template>
                                    </template>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- <div :src="`data:application/pdf;base64,`+`http://192.168.100.3:5000/${patient.registration_insurance[0].file_kronologis}`"></div> -->
                    <canvas id="pdf-canvas" style="display: none;"></canvas>
                    <img style="width:900px" :src="thumbnail" v-if="thumbnail" />

                    <!-- <iframe style="border: none" width="100%" height="1300" :src="`http://192.168.100.3:5000/${patient.registration_insurance[0].file_kronologis}`" v-if="patient.registration_insurance[0].file_kronologis" /> -->
                    <template v-for="(item,i) in examination_list" v-if="examination_list.length > 0">
                        <template v-for="(item_data,x) in item.data.files" v-if="item.data.files.length > 0">
                            <!-- <iframe style="width: 100vw; height: 100vh" :src="`http://192.168.100.3:5000/${item_data.url}`" v-if="item_data.url" /> -->
                            <img style="width:900px" :src="`http://192.168.100.3:5000/${item_data.url}`" v-if="item_data.url" />
                            {{ item_data?.expertise }}
                        </template>
                    </template>

                    <div class="row align-items-center pb-40" style="border-bottom: 2px solid black;margin-top: 2000px;" v-if="invoices && pesertaData?.tglSep">
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
                    <div class="canvas-body mt-32 px-40" v-if="invoices && pesertaData?.tglSep">
                        <section class="chead">
                            <div class="row mx-0 mt-28">
                                <div class="col-6">
                                    <table>
                                        <tr>
                                            <td class="first-cell">No. Nota</td>
                                            <td class="px-3">:</td>
                                            <td>{{ invoices?.code }}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">No. RM</td>
                                            <td class="px-3">:</td>
                                            <td>{{ regData.norm }}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Nama Pasien</td>
                                            <td class="px-3">:</td>
                                            <td>{{ regData.alias_name }} {{ regData.name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Dokter</td>
                                            <td class="px-3">:</td>
                                            <td>{{ regData.doctor_in_charge_name }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-6">
                                    <table>
                                        <tr>
                                            <td class="first-cell">Ruangan/Poli</td>
                                            <td class="px-3">:</td>
                                            <td>{{ regData.health_facility_item_name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Kelas</td>
                                            <td class="px-3">:</td>
                                            <td>
                                                <div v-if="this.regData.registration_insurance">
                                                    <div v-for="list in this.regData.registration_insurance">
                                                        <template>
                                                            {{ list.service_class_name }}
                                                        </template>
                                                        <b-badge v-if="list.is_cob == 1"class="rounded text-neutral-500 text-uppercase border border-warning-500 px-10 py-1" variant="warning-200">COB</b-badge>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Jenis Bayar</td>
                                            <td class="px-3">:</td>
                                            <td>
                                                <div v-if="this.regData.registration_insurance">
                                                    <div v-for="list in this.regData.registration_insurance">
                                                        <template>
                                                            {{ list.insurance_name }} - {{ list.company_name }}
                                                        </template>
                                                        <b-badge v-if="list.is_cob == 1"class="rounded text-neutral-500 text-uppercase border border-warning-500 px-10 py-1" variant="warning-200">COB</b-badge>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr v-if="regData.checkout_date">
                                            <td class="first-cell">Tanggal Masuk</td>
                                            <td class="px-3">:</td>
                                            <td>{{ localDateRanap(tgl_masuk(regData.register_date)) }}</td>
                                        </tr>
                                        <tr v-if="regData.checkout_date">
                                            <td class="first-cell">Tanggal Keluar</td>
                                            <td class="px-3">:</td>
                                            <td>{{ localDateRanap(regData.checkout_date) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </section>
                        <section class="cmain mt-24">
                            <table class="text-center w-100">
                                <thead class="">
                                    <tr class="bg-neutral-100">
                                        <th width="20%">TANGGAL</th>
                                        <th>JENIS TINDAKAN</th>
                                        <th>QTY</th>
                                        <th>HARGA</th>
                                        <th>SUBTOTAL</th>
                                        <th>LOKASI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template v-if="trans_1.length > 0" v-for="(item,i) in trans_1">
                                        <tr v-if="i == trans_1.length-1" class="text-right" style="background-color: #FEF8D8">
                                            <td colspan="2" class="text-left">Keperawatan</td>
                                            <td colspan="3"></td>
                                            <td colspan="1"></td>
                                        </tr>
                                    </template>
                                    <template v-if="TypeCostingOptions" v-for="(typec,i) in group_trans_1">
                                        <tr class="text-right">
                                            <td colspan="6" class="text-left">{{typec.title}}</td>
                                        </tr>
                                        <template v-if="trans_1.length > 0" v-for="(item,i) in trans_1">
                                            <tr v-if="item.classification_three_type_id === typec.value">
                                                <td class="text-center">{{ item.date_added }}</td>
                                                <td class="text-left">{{ item.costing_name }}</td>
                                                <td class="text-center">{{ item.var_1 }}</td>
                                                <td class="text-right">{{ item.var_2 | currency }}</td>
                                                <td class="text-right">{{ item.var_1*item.var_2 | currency }}</td>
                                                <td class="text-center">{{ item.health_facility_item_name }}</td>
                                            </tr>
                                        </template>
                                    </template>
                                    <template v-if="trans_1.length > 0" v-for="(item,i) in trans_1">
                                        <tr v-if="i == trans_1.length-1" class="text-right" style="background-color: #FEF8D8">
                                            <td colspan="2" class="text-left"></td>
                                            <td colspan="3">{{ trans_1_total | currency }}</td>
                                            <td colspan="1"></td>
                                        </tr>
                                    </template>
                                    <template v-if="trans_2.length > 0" v-for="(item,i) in trans_2">
                                        <tr v-if="i == trans_2.length-1" class="text-right" style="background-color: #FEF8D8">
                                            <td colspan="2" class="text-left">Tindakan/Konsul Dokter</td>
                                            <td colspan="3"></td>
                                            <td colspan="1"></td>
                                        </tr>
                                    </template>
                                    <template v-if="TypeCostingOptions" v-for="(typec,i) in group_trans_2">
                                        <tr class="text-right">
                                            <td colspan="6" class="text-left">{{typec.title}}</td>
                                        </tr>
                                        <template v-for="(item,i) in trans_2"  v-if="trans_2.length > 0" >
                                            <tr v-if="item.classification_three_type_id === typec.value">
                                                <td class="text-center">{{ item.date_added }}</td>
                                                <td class="text-left">{{item.costing_name}}<br>{{item.doctor_in_charge_name}}</td>
                                                <td>{{ item.var_2 }}</td>
                                                <td class="text-right">{{ item.var_1 | currency }}</td>
                                                <td class="text-right">{{ item.count_total | currency }}</td>
                                                <td>{{item.health_facility_item_name}}</td>
                                            </tr>
                                        </template>
                                    </template>.
                                    <template v-if="trans_2.length > 0" v-for="(item,i) in trans_2">
                                        <tr v-if="i == trans_2.length-1" class="text-right" style="background-color: #FEF8D8">
                                            <td colspan="2" class="text-left"></td>
                                            <td colspan="3">{{ trans_2_total | currency }}</td>
                                            <td colspan="1"></td>
                                        </tr>
                                    </template>
                                    <template v-if="trans_5.length > 0" v-for="(item,i) in trans_5">
                                        <tr v-if="i == trans_5.length-1" class="text-right" style="background-color: #FEF8D8">
                                            <td colspan="2" class="text-left">Pemakaian Alat</td>
                                            <td colspan="3"></td>
                                            <td colspan="1"></td>
                                        </tr>
                                    </template>

                                    <template v-if="trans_5.length > 0" v-for="(item,i) in trans_5">
                                        <tr>
                                            <td class="text-center">{{ item.date_added }}</td>
                                            <td class="text-left">{{item.costing_name}}<br>{{item.doctor_in_charge_name}}</td>
                                            <td>1</td>
                                            <td class="text-right">{{ item.var_1 | currency}}</td>
                                            <td class="text-right">{{ item.var_1 | currency}}</td>
                                            <td>{{item.health_facility_item_name}}</td>
                                        </tr>
                                    </template>
                                    <template v-if="trans_5.length > 0" v-for="(item,i) in trans_5">
                                        <tr v-if="i == trans_5.length-1" class="text-right" style="background-color: #FEF8D8">
                                            <td colspan="2" class="text-left"></td>
                                            <td colspan="3">{{ trans_5_total | currency }}</td>
                                            <td colspan="1"></td>
                                        </tr>
                                    </template>
                                    <template v-if="trans_7.length > 0" v-for="(item,i) in trans_7">
                                        <tr v-if="i == trans_7.length-1" class="text-right" style="background-color: #FEF8D8">
                                            <td colspan="2" class="text-left"></td>
                                            <td colspan="3"></td>
                                            <td colspan="1"></td>
                                        </tr>
                                    </template>

                                    <template v-if="trans_7.length > 0" v-for="(item,i) in trans_7">
                                        <tr>
                                            <td class="text-center">{{ item.date_added }}</td>
                                            <td class="text-left">{{item.costing_name}}<br>{{item.doctor_in_charge_name}}</td>
                                            <td>{{item.var_2}}</td>
                                            <td class="text-right">{{ item.var_1 | currency}}</td>
                                            <td class="text-right">{{ item.var_1*item.var_2 | currency}}</td>
                                            <td>{{item.health_facility_item_name}}</td>
                                        </tr>
                                    </template>
                                    <template v-if="trans_7.length > 0" v-for="(item,i) in trans_7">
                                        <tr v-if="i == trans_7.length-1" class="text-right" style="background-color: #FEF8D8">
                                            <td colspan="2" class="text-left"></td>
                                            <td colspan="3">{{ trans_7_total | currency }}</td>
                                            <td colspan="1"></td>
                                        </tr>
                                    </template>
                                    <template v-if="trans_11.length > 0" v-for="(item,i) in trans_11">
                                        <tr v-if="i == trans_11.length-1" class="text-right" style="background-color: #FEF8D8">
                                            <td colspan="2" class="text-left">LABORATORIUM</td>
                                            <td colspan="3"></td>
                                            <td colspan="1"></td>
                                        </tr>
                                    </template>

                                    <template v-if="trans_11.length > 0" v-for="(item,i) in trans_11">
                                        <tr>
                                            <td class="text-center">{{ item.date_added }}</td>
                                            <td class="text-left">{{ item.costing_name }}</td>
                                            <td>1</td>
                                            <td class="text-right">{{ item.var_1 | currency }}</td>
                                            <td class="text-right">{{ item.var_1 | currency }}</td>
                                            <td>LABORATORIUM</td>
                                        </tr>
                                    </template>
                                    <template v-if="trans_11.length > 0" v-for="(item,i) in trans_11">
                                        <tr v-if="i == trans_11.length-1" class="text-right" style="background-color: #FEF8D8">
                                            <td colspan="2" class="text-left"></td>
                                            <td colspan="3">{{ trans_11_total | currency }}</td>
                                            <td colspan="1"></td>
                                        </tr>
                                    </template>
                                    <template v-if="trans_12.length > 0" v-for="(item,i) in trans_12">
                                        <tr v-if="i == trans_12.length-1" class="text-right" style="background-color: #FEF8D8">
                                            <td colspan="2" class="text-left">RADIOLOGI</td>
                                            <td colspan="3"></td>
                                            <td colspan="1"></td>
                                        </tr>
                                    </template>

                                    <template v-if="trans_12.length > 0" v-for="(item,i) in trans_12">
                                        <tr>
                                            <td class="text-center">{{ item.date_added }}</td>
                                            <td class="text-left">{{ item.costing_name }}</td>
                                            <td>1</td>
                                            <td class="text-right">{{ item.var_1 | currency }}</td>
                                            <td class="text-right">{{ item.var_1 | currency }}</td>
                                            <td>RADIOLOGI</td>
                                        </tr>
                                    </template>
                                    <template v-if="trans_12.length > 0" v-for="(item,i) in trans_12">
                                        <tr v-if="i == trans_12.length-1" class="text-right" style="background-color: #FEF8D8">
                                            <td colspan="2" class="text-left"></td>
                                            <td colspan="3">{{ trans_12_total | currency }}</td>
                                            <td colspan="1"></td>
                                        </tr>
                                    </template>
                                    <template v-if="trans_15.length > 0" v-for="(item,i) in trans_15">
                                        <tr v-if="i == trans_15.length-1" class="text-right" style="background-color: #FEF8D8">
                                            <td colspan="2" class="text-left"></td>
                                            <td colspan="3"></td>
                                            <td colspan="1"></td>
                                        </tr>
                                    </template>

                                    <template v-if="trans_15.length > 0" v-for="(item,i) in trans_15">
                                        <tr>
                                            <td class="text-center">{{ item.date_added }}</td>
                                            <td class="text-left">{{ item.costing_name }}</td>
                                            <td>1</td>
                                            <td class="text-right">{{ item.var_1 | currency }}</td>
                                            <td class="text-right">{{ item.var_1 | currency }}</td>
                                            <td>{{ regData.health_facility_item_name }}</td>
                                        </tr>
                                    </template>
                                    <template v-if="trans_15.length > 0" v-for="(item,i) in trans_15">
                                        <tr v-if="i == trans_15.length-1" class="text-right" style="background-color: #FEF8D8">
                                            <td colspan="2" class="text-left"></td>
                                            <td colspan="3">{{ trans_15_total | currency }}</td>
                                            <td colspan="1"></td>
                                        </tr>
                                    </template>
                                    <template v-if="trans_10.length > 0" v-for="(item,i) in trans_10">
                                        <tr v-if="i == trans_10.length-1" class="text-right" style="background-color: #FEF8D8">
                                            <td colspan="2" class="text-left">KAMAR</td>
                                            <td colspan="3"></td>
                                            <td colspan="1"></td>
                                        </tr>
                                    </template>

                                    <template v-if="trans_10.length > 0" v-for="(item,i) in trans_10">
                                        <tr>
                                            <td class="text-center">{{ item.date_added }}</td>
                                            <td class="text-left">{{ item.costing_name }}</td>
                                            <td>{{ item.var_2 }}</td>
                                            <td class="text-right">{{ item.var_1 | currency }}</td>
                                            <td class="text-right">{{ item.var_1*item.var_2 | currency }}</td>
                                            <td>{{ item.health_facility_item_name }}</td>
                                        </tr>
                                    </template>
                                    <template v-if="trans_10.length > 0" v-for="(item,i) in trans_10">
                                        <tr v-if="i == trans_10.length-1" class="text-right" style="background-color: #FEF8D8">
                                            <td colspan="2" class="text-left"></td>
                                            <td colspan="3">{{ trans_10_total | currency }}</td>
                                            <td colspan="1"></td>
                                        </tr>
                                    </template>

                                    <!-- <tr>
                                        <td colspan="6" class="text-left">FARMASI</td>
                                    </tr> -->
                                    <template v-if="trans_14.length > 0" v-for="(item,i) in trans_14">
                                        <tr v-if="i == trans_14.length-1" class="text-right" style="background-color: #FEF8D8">
                                            <td colspan="2" class="text-left">FARMASI</td>
                                            <td colspan="3"></td>
                                            <td colspan="1"></td>
                                        </tr>
                                    </template>
                                    <template v-if="trans_14.length > 0" v-for="(item,i) in trans_14">
                                        <template v-for="(list, j) in item.prescription_item">
                                            <tr v-if="(list.is_retur_full !== 1 || list.pharmacy_catalog_id_retur) && list.qty-(list.is_retur_full === 1 && list.pharmacy_catalog_id_retur || list.pharmacy_catalog_id_ref ? 0 : list.qty_retur) !== 0">
                                                <td class="text-center">{{ item.date_added }}</td>
                                                <td class="text-left">{{list.pharmacy_catalog_name}}</td>
                                                <td>{{list.qty-(list.is_retur_full === 1 && list.pharmacy_catalog_id_retur ? 0 : list.qty_retur)}}</td>
                                                <!-- <td>{{ item.depo_name }}</td> -->
                                                <td class="text-right">{{list.var_1 | currency}}</td>
                                                <td class="text-right">{{ list.var_1*(list.qty-(list.is_retur_full === 1 && list.pharmacy_catalog_id_retur ? 0 : list.qty_retur)) | currency }}</td>
                                                <td class="text-center">FARMASI</td>
                                            </tr>
                                        </template>
                                        <!-- <tr v-if="i == trans_14.length-1" class="text-right bg-warning-100">
                                            <td colspan="6">{{ trans_14_total | currency }}</td>
                                        </tr> -->
                                    </template>
                                    <template v-if="trans_14.length > 0" v-for="(item,i) in trans_14">
                                        <tr v-if="i == trans_14.length-1" class="text-right" style="background-color: #FEF8D8">
                                            <td colspan="2" class="text-left"></td>
                                            <td colspan="3">{{ trans_14_total | currency }}</td>
                                            <td colspan="1"></td>
                                        </tr>
                                    </template>

                                    <tr>
                                        <td colspan="6" class="text-left">ADMINISTRASI</td>
                                    </tr>

                                    <template v-if="trans_13.length > 0" v-for="(item,i) in trans_13">
                                        <tr>
                                            <td class="text-center">{{ item.date_added }}</td>
                                            <td class="text-left">{{ item.costing_name }}</td>
                                            <td>1</td>
                                            <td class="text-right">{{ item.var_1 | currency}}</td>
                                            <td class="text-right">{{ item.count_total | currency}}</td>
                                            <td>{{ regData.health_facility_item_name }}</td>
                                        </tr>
                                        <tr v-if="i == trans_13.length-1" class="text-right bg-warning-100">
                                            <td colspan="6">{{ trans_13_total | currency }}</td>
                                        </tr>
                                    </template>

                                    <tr>
                                        <th colspan="5" class="text-right">Biaya</th>
                                        <td class="text-right">{{grand_total | currency}}</td>
                                    </tr>

                                    <tr>
                                        <th colspan="5" class="text-right">Grand Total</th>
                                        <td class="text-right">{{grand_total | currency}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </section>
                        <!-- <section class="mt-24">
                            <b-table-simple bordered small class="text-center">
                                <b-thead class="">
                                    <b-tr class="bg-neutral-100">
                                        <b-th>JENIS TINDAKAN</b-th>
                                        <b-th>LOKASI</b-th>
                                        <b-th>QTY</b-th>
                                        <b-th>HARGA</b-th>
                                        <b-th>DISKON</b-th>
                                        <b-th>TOTAL</b-th>
                                    </b-tr>
                                </b-thead>
                                <b-tbody>
                                    <template v-if="trans_1.length > 0" v-for="(item,i) in trans_1">
                                        <b-tr>
                                            <b-td class="text-left">{{ item.costing_name }}</b-td>
                                            <b-td>{{ regData.health_facility_item_name }}</b-td>
                                            <b-td>1</b-td>
                                            <b-td class="text-right">{{ item.var_2 | currency }}</b-td>
                                            <b-td class="text-right"></b-td>
                                            <b-td class="text-right">{{ item.var_1*item.var_2 | currency }}</b-td>
                                        </b-tr>
                                        <b-tr v-if="i == trans_1.length-1" class="text-right" style="background-color: aqua !important">
                                            <b-td colspan="6">{{ trans_1_total | currency }}</b-td>
                                        </b-tr>
                                    </template>

                                    <template v-if="trans_2.length > 0" v-for="(item,i) in trans_2">
                                        <b-tr>
                                            <b-td class="text-left">{{item.costing_name}}<br>{{item.doctor_in_charge_name}}</b-td>
                                            <b-td>{{item.health_facility_item_name}}</b-td>
                                            <b-td>{{ item.var_2 }}</b-td>
                                            <b-td class="text-right">{{ item.var_1 | currency }}</b-td>
                                            <b-td class="text-right">{{ item.var_4 ? item.var_4 : 0 | currency }}</b-td>
                                            <b-td class="text-right">{{ item.count_total | currency }}</b-td>
                                        </b-tr>
                                        <b-tr v-if="i == trans_2.length-1" class="text-right bg-warning-100">
                                            <b-td colspan="6">{{ trans_2_total | currency }}</b-td>
                                        </b-tr>
                                    </template>

                                    <template v-if="trans_5.length > 0" v-for="(item,i) in trans_5">
                                        <b-tr>
                                            <b-td class="text-left">{{item.costing_name}}<br>{{item.doctor_in_charge_name}}</b-td>
                                            <b-td>{{item.health_facility_item_name}}</b-td>
                                            <b-td>1</b-td>
                                            <b-td class="text-right">{{ item.var_1 | currency}}</b-td>
                                            <b-td class="text-right"></b-td>
                                            <b-td class="text-right">{{ item.var_1 | currency}}</b-td>
                                        </b-tr>
                                        <b-tr v-if="i == trans_5.length-1" class="text-right bg-warning-100">
                                            <b-td colspan="6">{{ trans_5_total | currency }}</b-td>
                                        </b-tr>
                                    </template>

                                    <template v-if="trans_11.length > 0" v-for="(item,i) in trans_11">
                                        <b-tr>
                                            <b-td class="text-left">{{ item.costing_name }}</b-td>
                                            <b-td>LABORATORIUM</b-td>
                                            <b-td>1</b-td>
                                            <b-td class="text-right">{{ item.var_1 | currency }}</b-td>
                                            <b-td class="text-right"></b-td>
                                            <b-td class="text-right">{{ item.var_1 | currency }}</b-td>
                                        </b-tr>
                                        <b-tr v-if="i == trans_11.length-1" class="text-right bg-warning-100">
                                            <b-td colspan="6">{{ trans_11_total | currency }}</b-td>
                                        </b-tr>
                                    </template>

                                    <template v-if="trans_12.length > 0" v-for="(item,i) in trans_12">
                                        <b-tr>
                                            <b-td class="text-left">{{ item.costing_name }}</b-td>
                                            <b-td>RADIOLOGI</b-td>
                                            <b-td>1</b-td>
                                            <b-td class="text-right">{{ item.var_1 | currency }}</b-td>
                                            <b-td class="text-right"></b-td>
                                            <b-td class="text-right">{{ item.var_1 | currency }}</b-td>
                                        </b-tr>
                                        <b-tr v-if="i == trans_12.length-1" class="text-right bg-warning-100">
                                            <b-td colspan="6">{{ trans_12_total | currency }}</b-td>
                                        </b-tr>
                                    </template>

                                    <template v-if="trans_15.length > 0" v-for="(item,i) in trans_15">
                                        <b-tr>
                                            <b-td class="text-left">{{ item.costing_name }}</b-td>
                                            <b-td>{{ regData.health_facility_item_name }}</b-td>
                                            <b-td>1</b-td>
                                            <b-td class="text-right">{{ item.var_1 | currency }}</b-td>
                                            <b-td class="text-right"></b-td>
                                            <b-td class="text-right">{{ item.var_1 | currency }}</b-td>
                                        </b-tr>
                                        <b-tr v-if="i == trans_15.length-1" class="text-right bg-warning-100">
                                            <b-td colspan="6">{{ trans_15_total | currency }}</b-td>
                                        </b-tr>
                                    </template>

                                    <template v-if="trans_10.length > 0" v-for="(item,i) in trans_10">
                                        <b-tr>
                                            <b-td class="text-left">{{ item.costing_name }}</b-td>
                                            <b-td>{{ item.health_facility_item_name }}</b-td>
                                            <b-td>{{ item.var_2 }}</b-td>
                                            <b-td class="text-right">{{ item.var_1 | currency }}</b-td>
                                            <b-td class="text-right"></b-td>
                                            <b-td class="text-right">{{ item.var_1*item.var_2 | currency }}</b-td>
                                        </b-tr>
                                        <b-tr v-if="i == trans_10.length-1" class="text-right bg-warning-100">
                                            <b-td colspan="6">{{ trans_10_total | currency }}</b-td>
                                        </b-tr>
                                    </template>

                                    <b-tr>
                                        <b-td colspan="6" class="text-left">FARMASI</b-td>
                                    </b-tr>
                                    <template v-if="trans_14.length > 0" v-for="(item,i) in trans_14">
                                        <template v-for="(list, j) in item.prescription_item">
                                            <b-tr v-if="list.is_retur_full !== 1">
                                                <b-td class="text-left">{{list.pharmacy_catalog_name}}</b-td>
                                                <b-td>{{ item.depo_name }}</b-td>
                                                <b-td>{{list.qty-(list.is_retur_full === 1 && list.pharmacy_catalog_id_retur || list.pharmacy_catalog_id_ref ? 0 : list.qty_retur)}}</b-td>
                                                <b-td class="text-right">{{list.var_1 | currency}}</b-td>
                                                <b-td class="text-right"></b-td>
                                                <b-td class="text-right">{{ list.var_1*(list.qty-(list.qty_retur !== '' || list.qty_retur !== null ? list.qty_retur : 0)) | currency }}</b-td>
                                            </b-tr>
                                        </template>
                                        <b-tr v-if="i == trans_14.length-1" class="text-right bg-warning-100">
                                            <b-td colspan="6">{{ trans_14_total | currency }}</b-td>
                                        </b-tr>
                                    </template>

                                    <b-tr>
                                        <b-td colspan="6" class="text-left">ADMINISTRASI</b-td>
                                    </b-tr>

                                    <template v-if="trans_13.length > 0" v-for="(item,i) in trans_13">
                                        <b-tr>
                                            <b-td class="text-left">{{ item.costing_name }}</b-td>
                                            <b-td>{{ regData.health_facility_item_name }}</b-td>npm ru
                                            <b-td>1</b-td>
                                            <b-td class="text-right">{{ item.var_1 | currency}}</b-td>
                                            <b-td class="text-right">{{ item.var_4 | currency}}</b-td>
                                            <b-td class="text-right">{{ item.count_total | currency}}</b-td>
                                        </b-tr>
                                        <b-tr v-if="i == trans_13.length-1" class="text-right bg-warning-100">
                                            <b-td colspan="6">{{ trans_13_total | currency }}</b-td>
                                        </b-tr>
                                    </template>

                                    <b-tr>
                                        <b-th colspan="5" class="text-right">Biaya</b-th>
                                        <b-td class="text-right">{{invoices?.var_3 | currency}}</b-td>
                                    </b-tr>

                                    <b-tr>
                                        <b-th colspan="5" class="text-right">Grand Total</b-th>
                                        <b-td class="text-right">{{invoices?.var_3 | currency}}</b-td>
                                    </b-tr>
                                </b-tbody>
                            </b-table-simple>
                        </section> -->
                        <section class="row mt-64">
                            <div class="col-6 text-center">
                                <p>Pasien / Keluarga Pasien</p>
                                <template v-if="globalQRText">
                                    <div class="my-2" id="rendertQR">
                                        <qrcode-vue id="qrcodeCanvast" :value="globalQRText" :size="70" level="H" ></qrcode-vue>
                                    </div>
                                </template>
                                <p class="text-uppercase">({{ regData.alias_name }} {{ regData.name }})</p>
                            </div>
                            <div class="col-6 text-center">
                                <p>Bekasi, {{ localDate(tgl_masuk(regData.register_date)) }}</p>
                                <template v-if="invoices?.ttd">
                                    <b-img :src="urlbe+invoices?.ttd" style="width: 120px;"/>
                                </template>
                                <p>( Kasir )</p>
                                <p class="text-uppercase mt-2">({{ invoices?.cashier_name }})</p>
                            </div>

                        </section>
                    </div>


                    <div class="row align-items-center pb-40" style="border-bottom: 2px solid black;margin-top: 2000px;" v-if="letterData?.id">
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

                    <div class="text-center mt-32" v-if="letterData?.id">
                        <div class="text-uppercase text-bold-24 with-line">SURAT KETERANGAN KEMATIAN</div>
                        <div class="mt-1 text-bold-16">Nomor: {{ letterData.no }}</div>
                    </div>
                    <div class="text-regular-18 mt-32" style="line-height: 140%;margin-top: 2000px;" v-if="letterData?.id">
                        <div>
                            <div>Yang Bertanda tangan dibawah ini {{ letterData.doctor_in_charge_name }} menerangkan sesungguhnya bahwa :</div>
                            <table class="mt-16 w-100">
                                <tr>
                                    <td class="col-3">Nama</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ regData.alias_name }}. {{ regData.fullname }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3">No. RM</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ regData.norm }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3">Tempat / Tanggal Lahir</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ localDate(regData.birth_date) }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3">Jenis Kelamin</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ regData.sex_name }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3">Warga Negara/Bangsa</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ regData.citizenship_id == '1' ? 'WNI' : 'WNA' }}</td>
                                </tr>
                                <!-- <tr>
                                    <td class="col-3 align-top">Agama</td>
                                    <td class="col-1 text-right align-top">:</td>
                                    <td>{{ getReligion(regData.religion_id) }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3 align-top">Status Perkawinan</td>
                                    <td class="col-1 text-right align-top">:</td>
                                    <td>{{ getMarried(regData.married_id) }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3 align-top">Pekerjaan</td>
                                    <td class="col-1 text-right align-top">:</td>
                                    <td>{{ getProffesion(regData.profession_id) }}</td>
                                </tr> -->
                                <tr>
                                    <td class="col-3 align-top">Alamat</td>
                                    <td class="col-1 text-right align-top">:</td>
                                    <td>{{ regData.address }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="mt-16">
                            <div>Telah meninggal dunia di RS Taman Harapan Baru Bekasi Pada :</div>
                            <table class="mt-16 w-100">
                                <tr>
                                    <td class="col-3">Hari</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ localDateDay(letterData.deathtime) }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3">Pukul</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ localDateHour(letterData.deathtime) }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3">Tanggal</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ localDate(letterData.deathtime) }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3">Bulan</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ localDateMonth(letterData.deathtime) }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3">Tahun</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ localDateYear(letterData.deathtime) }}</td>
                                </tr>
                                <!-- <tr>
                                    <td class="col-3">Tempat Kematian</td>
                                    <td class="col-1 text-right">:</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="col-3">Sebab Kematian</td>
                                    <td class="col-1 text-right">:</td>
                                    <td></td>
                                </tr> -->
                            </table>
                        </div>
                        <div class="mt-24">Demikian surat keterangan ini kami buat dengan sebenarnya.</div>

                        <div class="d-flex justify-content-end w-100 mt-64">
                            <div class="col-4 text-center">
                                <div :class="letterData.ttd_doctor ? '' : 'mb-64'">Bekasi, {{localDate(letterData.deathtime)}}<br>Dokter yang menyatakan,</br></div>
                                <template v-if="letterData.ttd_doctor">
                                    <b-img :src="urlbe+letterData.ttd_doctor" style="width: 120px;"/>
                                </template>
                                <div>{{ letterData.doctor_in_charge_name }}</div>
                            </div>
                        </div>

                        <div>
                            <div class="text-bold-15 with-line">Penerima,</div>
                            <table class="mt-16 w-100">
                                <tr>
                                    <td class="col-3">Nama Lengkap dan Tanda Tangan</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ letterDataDetail.receipt_name }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3">No. Kartu Identitas</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ letterDataDetail.receipt_idcard }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3">No. HP</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ letterDataDetail.receipt_phone }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3">Hubungan Dengan Pasien</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ letterDataDetail.receipt_relation }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3 align-top">Alamat</td>
                                    <td class="col-1 text-right align-top">:</td>
                                    <td>{{ letterDataDetail.receipt_address }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>


                    <div class="row align-items-center" v-if="letterDatabirth?.no">
                        <div class="col-2">
                            <img src="../../../../../@core/assets/images/logo-thb.jpg" width="300%" alt="rs-logo">
                            <br>
                        </div>
                        <div class="col text-center text-bold-28">
                            <!-- <p class="text-bold-14">RS Taman Harapan Baru</p>
                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                            <p>Info: +62 778 4161212 / 0853 7111 1212</p>
                             -->
                        </div>
                        <div class="col-2">
                            <tr>
                                <td><img src="../../../../../@core/assets/images/lafki-logo.png" width="85%" alt="lafki-logo"
                                        align="right">
                                </td>
                            </tr>
                        </div>
                        <div class="col-2">
                            <tr>
                                <td><img src="../../../../../@core/assets/images/apexa.png" width="80%" alt="-logo"
                                        align="right">
                                </td>
                            </tr>
                        </div>
                    </div>
                    <div class="text-uppercase text-center mt-5" v-if="letterDatabirth?.no">
                        <div class="text-bold-24 with-line">SURAT KETERANGAN KELAHIRAN</div>
                        <div class="text-bold-24">( Birth Certificate )</div>
                        <div class="text-regular-24">Nomor: {{ letterDatabirth?.no }}</div>
                    </div>
                    <div class="text-regular-18 mt-32" style="line-height: 140%;" v-if="letterDatabirth?.no">
                        <div>Yang bertanda tangan dibawah ini: <span>{{
                                getNewbornData?.doctor_name
                                }}</span>, menerangkan dengan sesungguhnya, bahwa telah lahir
                            seorang Bayi dengan Jenis Kelamin {{ regData.sex_name || '.....' }} :
                        </div>
                        <table class="mt-16 ml-3">
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">No. Rekam Medik</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.norm  }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Nama Bayi</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ getNewbornData.baby_name }}</td>
                            </tr>
                            <!-- <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Jenis Kelamin</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.birth_date }}</td>
                            </tr> -->
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Anak dari Ibu</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ letterDatabirth?.name_mother }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Nama Ayah</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ letterDatabirth?.name_father }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">No KTP Ibu</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ letterDatabirth?.idcard_mother }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">No KTP Ayah</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ letterDatabirth?.idcard_father }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Pekerjaan</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ letterDatabirth?.profession_name }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Pada Tanggal</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData?.birth_date  | convert_dateStr }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Pukul</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ getNewbornData?.birth_hour | convert_timeStr }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Alamat</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData?.address }}</td>
                            </tr>
                        </table>
                        <div class="row mt-5">
                            <div class="col-6">
                                <div><b>Keterangan Medis</b>:</div>
                                <table class="mt-16">
                                    <tr>
                                        <td class="col-6 pl-0" style="vertical-align: top;">Proses Kelahiran</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>{{ getJenisKelahiran(getNewbornData) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="col-3 pl-0" style="vertical-align: top;">Anak Ke -</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>{{ getNewbornData?.birth_order }}</td>
                                    </tr>
                                    <tr>
                                        <td class="col-3 pl-0" style="vertical-align: top;">Diagnosa</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>{{ getDiagnosa(getNewbornData) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="col-3 pl-0" style="vertical-align: top;">Ketuban</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>{{ getNewbornData?.water_breaks_color }}</td>
                                    </tr>
                                    <tr>
                                        <td class="col-3 pl-0" style="vertical-align: top;">Penyulit</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>{{ getNewbornData?.complication }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-6">
                                <div><b>Keterangan Fisik</b>:</div>
                                <table class="mt-16">
                                    <tr>
                                        <td class="col-6 pl-0" style="vertical-align: top;">Berat Badan</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>{{ getNewbornData && getNewbornData.weight ? getNewbornData.weight : '-' }} g</td>
                                    </tr>
                                    <tr>
                                        <td class="col-3 pl-0" style="vertical-align: top;">Panjang Badan</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>{{ getNewbornData && getNewbornData.height ? getNewbornData.height : '-' }} cm</td>
                                    </tr>
                                    <tr>
                                        <td class="col-3 pl-0" style="vertical-align: top;">Lingkar Kepala</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>{{ getNewbornData && getNewbornData.hfb_circumference_fronto_occipitalis ? getNewbornData.hfb_circumference_fronto_occipitalis : '-' }} cm</td>
                                    </tr>
                                    <tr>
                                        <td class="col-3 pl-0" style="vertical-align: top;">Lingkar Dada</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>{{ getNewbornData && getNewbornData.hfb_chest_circumference ? getNewbornData.hfb_chest_circumference : '-' }} cm</td>
                                    </tr>
                                    <tr>
                                        <td class="col-7 pl-0" style="vertical-align: top;">Lingkar Lengan Atas</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>{{ getNewbornData && getNewbornData.hfb_upper_arm_circumference ? getNewbornData.hfb_upper_arm_circumference : '-' }} cm</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="d-flex justify-content-start w-100 mt-64">
                            <div>
                                <div>Bekasi {{ nurseData.date_added | convert_dateStr}}</div>
                                <div :class="getNewbornData?.doctor_signature ? '' : 'mb-64'">Dokter yang menangani,</div>
                                <!-- <div :class="soapData.doctor_signature ? '' : 'mb-64'">Nama Jelas Dan Tanda Tangan
                                </div> -->
                                <template v-if="getNewbornData?.doctor_signature">
                                    <b-img :src="urlbe + getNewbornData?.doctor_signature" style="width: 120px;" />
                                </template>
                                <div>{{ getNewbornData?.doctor_name }}</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </b-container>
    </div>
</template>
<script>
import moment from 'moment'
import QrcodeVue from 'qrcode.vue'

import * as pdfjsLib from 'pdfjs-dist/build/pdf'

import workerSrc from 'pdfjs-dist/build/pdf.worker.min.js'
import { mapState } from 'vuex'

// Set the worker
pdfjsLib.GlobalWorkerOptions.workerSrc = workerSrc


export default {
    components: {
        QrcodeVue
    },
    data() {
        return {
            pdfAsDataUri:'',
            jknData:{},
            pdfUrl: '',
            letterData:{},
            letterDatabirth:{},
            days: 1,
            patientMotherData:{},
            getNewbornData:{},
            hfb_delivery: [
                { value: 1, title: 'SC' },
                { value: 2, title: 'Spontan' },
                { value: 3, title: 'VE' },
                { value: 4, title: 'Lainnya' },
            ],
            hfb_newborn_baby: [
                { value: 1, title: 'Aterm' },
                { value: 2, title: 'Prematur' },
                { value: 3, title: 'Immatur' },
                { value: 4, title: 'Dysmatur' },
                { value: 5, title: 'Serotinus' },
            ],
            letterDataDetail:{},
            thumbnail: null,
            eklaim: {
                special_cmg_option:'',
                json_obj:null,
                dializer_single_use:0,
                kantong_darah:0,
                procedur_non_bedah:0
            },
            examination_list:[],
            OptionsSpecialCMG:'',
            urlbe:process.env.VUE_APP_IMAGE,
            result: {},
            resultlab: {},
            patient: {},
            invoices:{},
            date_now:moment(new Date()).locale('id').format('LL'),
            tgl_sep:moment(new Date()).locale('id').format('YYYY-MM-DD'),
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
            regDataInap:{},
            resumeData:{},
            pesertaData: {},
            pesertaDataControl: {},
            globalQRText: '',
            regIns:{},
            regDataInv:{},
            orderData:{},
            urlData:{},
            orderDataRad:[],
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
            trans_7: [],
            trans_10: [],
            trans_11: [],
            trans_12: [],
            trans_13: [],
            trans_14: [],
            trans_15: [],

            trans_1_total: 0,
            trans_2_total: 0,
            trans_5_total: 0,
            trans_7_total: 0,
            trans_10_total: 0,
            trans_11_total: 0,
            trans_12_total: 0,
            trans_13_total: 0,
            trans_14_total: 0,
            trans_15_total: 0,

            grand_total: 0,

            group_trans_1:[],
            group_trans_2:[],
            active: {
                ophtal: false,
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

            fieldsTable: [
                { key: 'date_added', label: 'Tanggal/Jam', thClass:'text-center align-middle' },
                { key: 'employee_doctor_name', label: 'PROFESI PEMBERI ASUHAN (PPA)', thClass:'text-center align-middle' },
                { key: 'check_result', label: 'Hasil Pemeriksaan, Analisis, Rencana Penatalaksanaan Pasien', thClass:'text-center align-middle' },
                { key: 'health_facility_name', label: 'Instruksi Tenaga Kesehatan Termasuk Prosedur Pasca Bedah', thClass:'text-center', thStyle:'width: 20%' },
                { key: 'sign', label: 'Nama, Paraf, dan Verifikasi PPA', thClass:'text-center align-middle', thStyle:'width: 15%' },
            ],
            result_arry: [],
            IGD_doctor: {
                soap_p_list: [],
                transaction_list: [],
                laboratory_list: [],
                radiology_list: [],
                prescription_list: [],
            },

            FIRST_doctor: {
                soap_p_list: [],
                transaction_list: [],
                laboratory_list: [],
                radiology_list: [],
                prescription_list: [],
            },

            SECOND_doctor: {
                soap_p_list: [],
                transaction_list: [],
                laboratory_list: [],
                radiology_list: [],
                prescription_list: [],
            },

            THIRD_doctor: {
                soap_p_list: [],
                transaction_list: [],
                laboratory_list: [],
                radiology_list: [],
                prescription_list: [],
            },

            FOURTH_doctor: {
                soap_p_list: [],
                transaction_list: [],
                laboratory_list: [],
                radiology_list: [],
                prescription_list: [],
            },

            FIFTH_doctor: {
                soap_p_list: [],
                transaction_list: [],
                laboratory_list: [],
                radiology_list: [],
                prescription_list: [],
            },

            tableData: {
                laboratory: [
                    { key: 'no', label: 'No', thStyle: 'width: 60px' },
                    { key: 'no_request', label: 'Nomor Permintaan', thStyle: 'width: 200px' },
                    { key: 'laboratory_request_item', label: 'Permintaan' },
                ],
                radiology: [
                    { key: 'no', label: 'No', thStyle: 'width: 60px' },
                    { key: 'no_request', label: 'Nomor Permintaan', thStyle: 'width: 200px' },
                    { key: 'date_request', label: 'Tanggal Permintaan', thStyle: 'width: 200px' },
                    { key: 'radiology_request_item', label: 'Permintaan' },
                    { key: 'action', label: 'Aksi', thStyle: 'width: 140px' },
                ],
            },
            triageData: {},
            nurseData: {},
            emergencyData: {},
            employeeData: {},
            meaningful: [],
            generalis_key: [
                { default: 1, label: 'Kepala', key: "head", name: 'getHead' },
                { default: 1, label: 'Warna Rambut', key: "head_hair_color", name: 'getHairColor' },
                { default: 1, label: 'Kondisi Rambut', key: "head_hair_condition", name: 'getHairCondition' },
                { default: 1, label: 'Conjunctiva', key: "eye_conjunctive", name: 'getEyeConjunctive' },
                { default: 1, label: 'Sklera', key: "eye_sclera", name: 'getEyeSclera' },
                { default: 1, label: 'Pupil', key: "eye_pupil", name: 'getEyePupil' },
                { default: 1, label: 'Reflek Cahaya', key: "eye_light_reflex", name: 'getEyeReflect' },
                { default: 1, label: 'Mukosa', key: "mouth_mucosa", name: 'getMouthMucosa' },
                { default: 1, label: 'Perubahan Warna', key: "mouth_discoloration", name: 'getMouthDiscoloration' },
                { default: 1, label: 'Lidah', key: "mouth_tongue", name: 'getMouthTongue' },
                { default: 1, label: 'Karies', key: "mouth_caries", name: 'getMouthCaries' },
                { default: 1, label: 'KGB', key: "neck_kgb", name: 'getNeckKGB' },
                { default: 1, label: 'Posisi Trakea', key: "neck_trachea_position", name: 'getNeckTracheaPosition' },
                { default: 1, label: 'Massa', key: "neck_massa", name: 'getNeckMassa' },
                { default: 1, label: 'JVP', key: "neck_jvp", name: 'getNeckJVP' },
                { default: 1, label: 'Inspeksi Dada', key: "thorax_insp_chest", name: 'getInspectionChest' },
                { default: 1, label: 'Retraksi', key: "thorax_insp_retraction", name: 'getInspectionRetractiont' },
                { default: 1, label: 'Palpasi Dada', key: "thorax_palp_chest", name: 'getPalpationChest' },
                { default: 1, label: 'Fremitus', key: "thorax_palp_fremitus", name: 'getPalpationFremitus' },
                { default: 1, label: 'Krepitasi', key: "thorax_palp_crepitation", name: 'getPalpationKrepitasi' },
                { default: 1, label: 'Pulmo Vasikuler', key: "thorax_ausk_pul_vasiculer", name: 'getAuskultationPulmoVasikuler' },
                { default: 1, label: 'Pulmo Rhonki', key: "thorax_ausk_pul_rhonki", name: 'getAuskultationPulmoRhonki' },
                { default: 1, label: 'Pulmo Wheezing', key: "thorax_ausk_pul_wheezing", name: 'getAuskultationPulmoWheezing' },
                { default: 1, label: 'Bunyi Jantung', key: "thorax_ausk_cor_heart_sound", name: 'getAuskultationCORBJ' },
                { default: 1, label: 'Murmur', key: "thorax_ausk_cor_murmur", name: 'getAuskultationCORMurmur' },
                { default: 1, label: 'Gallop', key: "thorax_ausk_cor_gallop", name: 'getAuskultationCORGallop' },
                { default: 1, label: 'Supel', key: "abdomen_supel", name: 'getAbdomenSupel' },
                { default: 1, label: 'Bising Usus', key: "abdomen_bowel_sounds", name: 'getAbdomenBising' },
                { default: 1, label: 'Nyeri Tekan', key: "abdomen_tenderness", name: 'getAbdomenPress' },
                { default: 1, label: 'Hepar & Lien', key: "abdomen_hepar_lien", name: 'getAbdomenHepar' },
                { default: 1, label: 'Akral', key: "extremity_acral", name: 'getEkstremitasAkral' },
                { default: 1, label: 'extremity_crt', key: "extremity_crt", name: 'getEkstremitasCRT' },
                { default: 1, label: 'extremity_edema', key: "extremity_edema", name: 'getEkstremitasEdema' },
            ],
            today: moment(new Date()).format('DD-MM-YYYY'),
            resultItems: []
        }
    },
    mounted() {
        this.$store.dispatch('soap/getByRegId', this.$route.params.soap_id).then(data => {
            this.getResume(data[0].registration_destination_id)
			this.$store.dispatch('registration/getById', data[0].registration_destination_id).then(data => {
                this.regData = data[0]
                this.getDataReg(data[0].registration_destination_id)
            })
            this.$store.dispatch('soap/getByRegistrationId', data[0].registration_id).then(dataList => {
                // Cari soap berdasarkan health_facility_id dengan prioritas: Rawat Inap > Rawat Jalan > IGD
                let dataReg = dataList.find(item => item.health_facility_id === '444b3134-5b75-4b67-af72-a558d46716d7')
                if (!dataReg) {
                    dataReg = dataList.find(item => item.health_facility_id === '3cb626f5-9148-4fb1-8663-3d7f028b01a6')
                    if (!dataReg) {
                        dataReg = dataList.find(item => item.health_facility_id === '40f06534-07ec-4221-ad9d-dea08196079f')
                    }
                }
                // Jika tidak ada yang match, gunakan soap pertama dari list atau soap dari parameter URL
                if (!dataReg && dataList.length > 0) {
                    dataReg = dataList[0]
                } else if (!dataReg) {
                    dataReg = data[0]
                }
                this.getData(dataReg.id)
                window.addEventListener('keydown', this.handleKeyPress);
            })
        })
    },
    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },

    filters: {
        convert_date(value) {
            return moment(new Date(value)).locale('id').format('LL')
        },
        convert_time(value) {
            return moment(new Date(value)).locale('id').format('LT')
        },
        convert_dateStr(value) {
            if(value) return moment(value).locale('id').format('LL')
        },
        convert_timeStr(value) {
            if(value) return moment(value, 'HH:mm:ss').locale('id').format('HH.mm');
        },
        currency(value) {
            return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'IDR' }).format(value)
        },
        currencys(value) {
            return value != null ?  value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') : 0
        }
    },
    computed: {
        ...mapState({
            TypeCostingOptions: state => state.helper.options.typecostingc,
            // generalis option
            OptionsHeadCircumference: state => state.soap.generalis.OptionsHeadCircumference,
            OptionsHeadHairColor: state => state.soap.generalis.OptionsHeadHairColor,
            OptionsHeadHairCondition: state => state.soap.generalis.OptionsHeadHairCondition,
            OptionsEyeConjunctiva: state => state.soap.generalis.OptionsEyeConjunctiva,
            OptionsEyeSclera: state => state.soap.generalis.OptionsEyeSclera,
            OptionsEyePupil: state => state.soap.generalis.OptionsEyePupil,
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

    },
    methods: {
        getDiagnosa(newBornData){
            if(newBornData?.hfb_newborn_baby_type_id){
                const result = this.hfb_newborn_baby.filter(item => item.value == newBornData.hfb_newborn_baby_type_id)
                if(result.length > 0) {
                    return result[0].title
                }
            }
        },
        getJenisKelahiran(newBornData){
            if(newBornData?.hfb_delivery_type_id){
                if(newBornData?.hfb_delivery_type_id == 4){
                    return newBornData.hfb_delivery_other
                } else {
                    const result = this.hfb_delivery.filter(item => item.value == newBornData.hfb_delivery_type_id)
                    if(result.length > 0) {
                        return result[0].title
                    }
                }
            }
        },
        getDataNurse (nurse_id) {
            this.$store.dispatch('nurse/getNewbornInpatientDetail', { nurse_id: nurse_id }).then((response) => {
                this.getNewbornData = response[0]
                if(this.getNewbornData?.letter.length > 0) {
                    this.letterDatabirth = this.getNewbornData?.letter[0]
                }
            })
        },
        localDateDay(input_date) {
            return moment(input_date).locale("id").format('dddd')
        },
        localDateHour(input_date) {
            return moment(input_date).locale("id").format('HH:mm')
        },
        localDateMonth(input_date) {
            return moment(input_date).locale("id").format('MMMM')
        },
        localDateYear(input_date) {
            return moment(input_date).locale("id").format('YYYY')
        },
        tgl_masuk(value) {
            return value != null ?  this.tgl_sep + ' ' + moment(value).format('HH:mm:ss') : 0
        },
        getRadioReasonIcon(value) {
            if (this.resumeData && this.resumeData && this.resumeData.exit_id) {
                return this.resumeData.exit_id == value ? "check-circle-fill" : "circle-fill";
            } else {
                return "circle-fill";
            }
        },
        getRadioReasonIconVariant(value) {
            if (this.resumeData && this.resumeData && this.resumeData.exit_id) {
                return this.resumeData.exit_id == value ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },
        getRadioConditionIcon(value) {
            if (this.resumeData && this.resumeData && this.resumeData.exit_condition_type_id) {
                return this.resumeData.exit_condition_type_id == value ? "check-circle-fill" : "circle-fill";
            } else {
                return "circle-fill";
            }
        },
        getRadioConditionIconVariant(value) {
            if (this.resumeData && this.resumeData && this.resumeData.exit_condition_type_id) {
                return this.resumeData.exit_condition_type_id == value ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },
        getRadioControlIcon(value) {
            if (this.resumeData && this.resumeData && this.resumeData.care_instruction_control) {
                return this.resumeData.care_instruction_control == value ? "check-circle-fill" : "circle-fill";
            } else {
                return "circle-fill";
            }
        },
        getRadioControlIconVariant(value) {
            if (this.resumeData && this.resumeData && this.resumeData.care_instruction_control) {
                return this.resumeData.care_instruction_control == value ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },
        getRadioOtherIcon(value) {
            if (this.resumeData && this.resumeData && this.resumeData.care_instruction_anythingelse) {
                return this.resumeData.care_instruction_anythingelse == value ? "check-circle-fill" : "circle-fill";
            } else {
                return "circle-fill";
            }
        },
        getRadioOtherIconVariant(value) {
            if (this.resumeData && this.resumeData && this.resumeData.care_instruction_anythingelse) {
                return this.resumeData.care_instruction_anythingelse == value ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },
        getResume (id) {
            this.$store.dispatch('resume/getAllList', {registration_destination_id:id}).then(data => {
                this.resumeData = data[0]
                this.getSTGeneralisMeaningful(this.resumeData)
                this.$store.dispatch('employee/getDetailById', this.resumeData.doctor_in_charge_id).then(employee => {
                    this.employeeData = employee[0]
                })
            })
        },
        getMeaningVariant(value) {
            if (value && value != 1) return 'text-danger-500'
            return ''
        },
        getSTGeneralisMeaningful(obj){
            let all_key = this.generalis_key

            let temp_array = []
            for (let i = 0; i < all_key.length; i++) {
                const el = all_key[i];
                if (obj[el.key] && obj[el.key] != el.default ) {
                    const func = this[el.name]
                    const result = func(obj[el.key])
                    temp_array.push({
                        key: el.label,
                        value: result
                    });
                }
            }

            this.meaningful = temp_array
        },
        getJKN (soap_id) {
            try {
                this.$store.dispatch('jkn/getJknAssessment', soap_id).then(data => {
                    this.jknData = data[0]
                    this.eklaim.json_obj = data[0].inacbgs
                    this.OptionsSpecialCMG = []
                    if (data[0].inacbgs != null && data[0].inacbgs.special_cmg_option && data[0].inacbgs.special_cmg_option.length > 0) {
                        for (let cmg = 0; cmg < data[0].inacbgs.special_cmg_option.length; cmg++) {
                            this.OptionsSpecialCMG.push({value:data[0].inacbgs.special_cmg_option[cmg].code,title:data[0].inacbgs.special_cmg_option[cmg].description})
                        }
                    }
                    // "special_cmg_option": [
                    //     {
                    //     "code": "YY10",
                    //     "description": "Phacoemulsification",
                    //     "type": "Special Procedure"
                    //     }
                    // ],
                })

            } catch (error) {
                console.log('Error while GET SOAP DATA at: ', error);
            }
        },
        convertDataURIToBinary (dataURI) {
            var BASE64_MARKER = ';base64,';
            var base64Index = dataURI.indexOf(BASE64_MARKER) + BASE64_MARKER.length;
            var base64 = dataURI.substring(base64Index);
            var raw = window.atob(base64);
            var rawLength = raw.length;
            var array = new Uint8Array(new ArrayBuffer(rawLength));

            for(var i = 0; i < rawLength; i++) {
                array[i] = raw.charCodeAt(i);
            }
            return array;
        },
        async generateThumbnail(data) {
            try {
                const response = await this.$store.dispatch('soap/pdf_to_base64', data)
                this.pdfAsDataUri = response.base64
                console.log('test'+this.pdfAsDataUri)
                var pdfAsArray = this.convertDataURIToBinary(this.pdfAsDataUri);

                const loadingTask =  pdfjsLib.getDocument(pdfAsArray)

                const pdf = await loadingTask.promise
                const page = await pdf.getPage(1)

                const scale = 1
                const viewport = page.getViewport({ scale })

                const canvas = document.getElementById('pdf-canvas')
                const context = canvas.getContext('2d')

                canvas.width = viewport.width
                canvas.height = viewport.height

                await page.render({ canvasContext: context, viewport }).promise

                this.thumbnail = canvas.toDataURL()
                console.log('testste'+this.thumbnail)
            } catch (error) {
                console.error('Error rendering PDF:', error)
            }
        },
        async getDataBpjs(id) {
            try {
                const response = await this.$store.dispatch('patient/bpjs', {url:'Sep',idx:id})
                this.pesertaData = response.data.response
                this.getDataBpjsSPRI(response.data.response.kontrol.noSurat)
                this.globalQRText = this.pesertaData?.peserta.noKartu
                this.tgl_sep = this.pesertaData?.tglSep
                console.log('SEP '+this.pesertaData)
                // this.generateToImage()
            } catch (error) {
            }
        },
        async getDataBpjsSPRI(id) {
            try {
                const response = await this.$store.dispatch('patient/bpjs', {url:'Control',idx:id})
                this.pesertaDataControl = response.data.response
                console.log('SEP '+this.pesertaDataControl)
                // this.generateToImage()
            } catch (error) {
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

        getGeneralist(obj) {
            console.log(obj)
            const objHead = this.OptionsHeadCircumference.find(el => el.value === obj.head)
            const objHairColor = this.OptionsHeadHairColor.find(el => el.value === obj.head_hair_color)
            const objHairCondition = this.OptionsHeadHairCondition.find(el => el.value === obj.head_hair_condition)
            const objEyeConjunctive = this.OptionsEyeConjunctiva.find(el => el.value === obj.eye_conjunctive)
            const objEyeSclera = this.OptionsEyeSclera.find(el => el.value === obj.eye_sclera)
            const objEyePupil = this.OptionsEyePupil.find(el => el.value === obj.eye_pupil)
            const objEyeReflect = this.OptionsEyeReflect.find(el => el.value === obj.eye_light_reflex)
            const objMouthMucosa = this.OptionsMouthMukosa.find(el => el.value === obj.mouth_mucosa)
            const objMouthDiscoloration = this.OptionsMouthHiperemis.find(el => el.value === obj.mouth_discoloration)
            const objMouthTongue = this.OptionsMouthTongue.find(el => el.value === obj.mouth_tongue)
            const objMouthCaries = this.OptionsMouthKaries.find(el => el.value === obj.mouth_caries)
            const objNeckKGB = this.OptionsNeckKGB.find(el => el.value === obj.neck_kgb)
            const objNeckTracheaPosition = this.OptionsNeckTrakea.find(el => el.value === obj.neck_trachea_position)
            const objNeckMassa = this.OptionsNeckMassa.find(el => el.value === obj.neck_massa)
            const objInspectionChest = this.OptionsInspectionChest.find(el => el.value === obj.thorax_insp_chest)
            const objInspectionRetractiont = this.OptionsInspectionRetraction.find(el => el.value === obj.thorax_insp_retraction)
            const objPalpationChest = this.OptionsPalpationChest.find(el => el.value === obj.thorax_palp_chest)
            const objPalpationFremitus = this.OptionsPalpationFremitus.find(el => el.value === obj.thorax_palp_fremitus)
            const objPalpationKrepitasi = this.OptionsPalpationKrepitasi.find(el => el.value === obj.thorax_palp_crepitation)
            const objAuskultationPulmoVasikuler = this.OptionsAuskultationPulmoVasikuler.find(el => el.value === obj.thorax_ausk_pul_vasiculer)
            const objAuskultationPulmoRhonki = this.OptionsAuskultationPulmoRhonki.find(el => el.value === obj.thorax_ausk_pul_rhonki)
            const objAuskultationPulmoWheezing = this.OptionsAuskultationPulmoWheezing.find(el => el.value === obj.thorax_ausk_pul_wheezing)
            const objAuskultationCORBJ = this.OptionsAuskultationCORBJ.find(el => el.value === obj.thorax_ausk_cor_heart_sound)
            const objAuskultationCORMurmur = this.OptionsAuskultationCORMurmur.find(el => el.value === obj.thorax_ausk_cor_murmur)
            const objAuskultationCORGallop = this.OptionsAuskultationCORGallop.find(el => el.value === obj.thorax_ausk_cor_gallop)
            const objAbdomenSupel = this.OptionsAbdomenSupel.find(el => el.value === obj.abdomen_supel)
            const objAbdomenBising = this.OptionsAbdomenBising.find(el => el.value === obj.abdomen_bowel_sounds)
            const objAbdomenPress = this.OptionsAbdomenPress.find(el => el.value === obj.abdomen_tenderness)
            const objAbdomenHepar = this.OptionsAbdomenHepar.find(el => el.value === obj.abdomen_hepar_lien)
            const objEkstremitasAkral = this.OptionsEkstremitasAkral.find(el => el.value === obj.extremity_acral)
            const objEkstremitasCRT = this.OptionsEkstremitasCRT.find(el => el.value === obj.extremity_crt)
            const objEkstremitasEdema = this.OptionsEkstremitasEdema.find(el => el.value === obj.extremity_edema)

            let objOdOftalVisus = ''
            let objOdOftalTIO = ''
            let objOdOftalEyePosition = ''
            let objOdOftalMovement = ''
            let objOdOftalSuperSilia = ''
            let objOdOftalPalpebraEdema = ''
            let objOdOftalPalpebraPtosis = ''
            let objOdOftalPalpebraLagophtalmus = ''
            let objOdOftalPalpebraTumor = ''
            let objOdOftalApparetus = ''
            let objOdOftalSilia = ''
            let objOdOftalConjHiperemis = ''
            let objOdOftalConjBleed = ''
            let objOdOftalKornea = ''
            let objOdOftalAnterior = ''
            let objOdOftalIrisColor = ''
            let objOdOftalIrisKripte = ''
            let objOdOftalPupilForm = ''
            let objOdOftalPupilDiameter = ''
            let objOdOftalPupilDirectLight = ''
            let objOdOftalPupilIndirectLight = ''
            let objOdOftalLens = ''
            let objOdOftalShadow = ''
            let objOdOftalGlandula = ''
            let objOdOftalLapang = ''

            if (obj.ophthal_od && obj.ophthal_od.visus) {
                objOdOftalVisus = this.OptionsOftalVisus.find(el => el.value === obj.ophthal_od.visus)
            }
            if (obj.ophthal_od && obj.ophthal_od.tio_digital) {
                objOdOftalTIO = this.OptionsOftalTIO.find(el => el.value === obj.ophthal_od.tio_digital)
            }
            if (obj.ophthal_od && obj.ophthal_od.eye_position) {
                objOdOftalEyePosition = this.OptionsOftalEyePosition.find(el => el.value === obj.ophthal_od.eye_position)
            }
            if (obj.ophthal_od && obj.ophthal_od.eye_movement) {
                objOdOftalMovement = this.OptionsOftalMovement.find(el => el.value === obj.ophthal_od.eye_movement)
            }
            if (obj.ophthal_od && obj.ophthal_od.super_silia) {
                objOdOftalSuperSilia = this.OptionsOftalSuperSilia.find(el => el.value === obj.ophthal_od.super_silia)
            }
            if (obj.ophthal_od && obj.ophthal_od.palpebra_edema) {
                objOdOftalPalpebraEdema = this.OptionsOftalPalpebraEdema.find(el => el.value === obj.ophthal_od.palpebra_edema)
            }
            if (obj.ophthal_od && obj.ophthal_od.palpebra_ptosis) {
                objOdOftalPalpebraPtosis = this.OptionsOftalPalpebraPtosis.find(el => el.value === obj.ophthal_od.palpebra_ptosis)
            }
            if (obj.ophthal_od && obj.ophthal_od.palpebra_lagophtalmus) {
                objOdOftalPalpebraLagophtalmus = this.OptionsOftalPalpebraLagophtalmus.find(el => el.value === obj.ophthal_od.palpebra_lagophtalmus)
            }
            if (obj.ophthal_od && obj.ophthal_od.palpebra_tumor) {
                objOdOftalPalpebraTumor = this.OptionsOftalPalpebraTumor.find(el => el.value === obj.ophthal_od.palpebra_tumor)
            }
            if (obj.ophthal_od && obj.ophthal_od.apparatus_lakrimalis) {
                objOdOftalApparetus = this.OptionsOftalApparetus.find(el => el.value === obj.ophthal_od.apparatus_lakrimalis)
            }
            if (obj.ophthal_od && obj.ophthal_od.silia) {
                objOdOftalSilia = this.OptionsOftalSilia.find(el => el.value === obj.ophthal_od.silia)
            }
            if (obj.ophthal_od && obj.ophthal_od.conjunctive_hiperemis) {
                objOdOftalConjHiperemis = this.OptionsOftalConjHiperemis.find(el => el.value === obj.ophthal_od.conjunctive_hiperemis)
            }
            if (obj.ophthal_od && obj.ophthal_od.conjunctive_bleed) {
                objOdOftalConjBleed = this.OptionsOftalConjBleed.find(el => el.value === obj.ophthal_od.conjunctive_bleed)
            }
            if (obj.ophthal_od && obj.ophthal_od.cornea) {
                objOdOftalKornea = this.OptionsOftalKornea.find(el => el.value === obj.ophthal_od.cornea)
            }
            if (obj.ophthal_od && obj.ophthal_od.camera_oculi) {
                objOdOftalAnterior = this.OptionsOftalAnterior.find(el => el.value === obj.ophthal_od.camera_oculi)
            }
            if (obj.ophthal_od && obj.ophthal_od.iris_color) {
                objOdOftalIrisColor = this.OptionsOftalIrisColor.find(el => el.value === obj.ophthal_od.iris_color)
            }
            if (obj.ophthal_od && obj.ophthal_od.iris_cripte) {
                objOdOftalIrisKripte = this.OptionsOftalIrisKripte.find(el => el.value === obj.ophthal_od.iris_cripte)
            }
            if (obj.ophthal_od && obj.ophthal_od.pupil_form) {
                objOdOftalPupilForm = this.OptionsOftalPupilForm.find(el => el.value === obj.ophthal_od.pupil_form)
            }
            if (obj.ophthal_od && obj.ophthal_od.pupil_diameter) {
                objOdOftalPupilDiameter = this.OptionsOftalPupilDiameter.find(el => el.value === obj.ophthal_od.pupil_diameter)
            }
            if (obj.ophthal_od && obj.ophthal_od.pupil_direct_light) {
                objOdOftalPupilDirectLight = this.OptionsOftalPupilDirectLight.find(el => el.value === obj.ophthal_od.pupil_direct_light)
            }
            if (obj.ophthal_od && obj.ophthal_od.pupil_indirect_light) {
                objOdOftalPupilIndirectLight = this.OptionsOftalPupilIndirectLight.find(el => el.value === obj.ophthal_od.pupil_indirect_light)
            }
            if (obj.ophthal_od && obj.ophthal_od.lens) {
                objOdOftalLens = this.OptionsOftalLens.find(el => el.value === obj.ophthal_od.lens)
            }
            if (obj.ophthal_od && obj.ophthal_od.shadow_test) {
                objOdOftalShadow = this.OptionsOftalShadow.find(el => el.value === obj.ophthal_od.shadow_test)
            }
            if (obj.ophthal_od && obj.ophthal_od.glandula) {
                objOdOftalGlandula = this.OptionsOftalGlandula.find(el => el.value === obj.ophthal_od.glandula)
            }
            if (obj.ophthal_od && obj.ophthal_od.field_view) {
                objOdOftalLapang = this.OptionsOftalLapang.find(el => el.value === obj.ophthal_od.field_view)
            }


            let objOsOftalVisus = ''
            let objOsOftalTIO = ''
            let objOsOftalEyePosition = ''
            let objOsOftalMovement = ''
            let objOsOftalSuperSilia = ''
            let objOsOftalPalpebraEdema = ''
            let objOsOftalPalpebraPtosis = ''
            let objOsOftalPalpebraLagophtalmus = ''
            let objOsOftalPalpebraTumor = ''
            let objOsOftalApparetus = ''
            let objOsOftalSilia = ''
            let objOsOftalConjHiperemis = ''
            let objOsOftalConjBleed = ''
            let objOsOftalKornea = ''
            let objOsOftalAnterior = ''
            let objOsOftalIrisColor = ''
            let objOsOftalIrisKripte = ''
            let objOsOftalPupilForm = ''
            let objOsOftalPupilDiameter = ''
            let objOsOftalPupilDirectLight = ''
            let objOsOftalPupilIndirectLight = ''
            let objOsOftalLens = ''
            let objOsOftalShadow = ''
            let objOsOftalGlandula = ''
            let objOsOftalLapang = ''

            if (obj.ophthal_os && obj.ophthal_os.visus) {
                objOsOftalVisus = this.OptionsOftalVisus.find(el => el.value === obj.ophthal_os.visus)
            }
            if (obj.ophthal_os && obj.ophthal_os.tio_digital) {
                objOsOftalTIO = this.OptionsOftalTIO.find(el => el.value === obj.ophthal_os.tio_digital)
            }
            if (obj.ophthal_os && obj.ophthal_os.eye_position) {
                objOsOftalEyePosition = this.OptionsOftalEyePosition.find(el => el.value === obj.ophthal_os.eye_position)
            }
            if (obj.ophthal_os && obj.ophthal_os.eye_movement) {
                objOsOftalMovement = this.OptionsOftalMovement.find(el => el.value === obj.ophthal_os.eye_movement)
            }
            if (obj.ophthal_os && obj.ophthal_os.super_silia) {
                objOsOftalSuperSilia = this.OptionsOftalSuperSilia.find(el => el.value === obj.ophthal_os.super_silia)
            }
            if (obj.ophthal_os && obj.ophthal_os.palpebra_edema) {
                objOsOftalPalpebraEdema = this.OptionsOftalPalpebraEdema.find(el => el.value === obj.ophthal_os.palpebra_edema)
            }
            if (obj.ophthal_os && obj.ophthal_os.palpebra_ptosis) {
                objOsOftalPalpebraPtosis = this.OptionsOftalPalpebraPtosis.find(el => el.value === obj.ophthal_os.palpebra_ptosis)
            }
            if (obj.ophthal_os && obj.ophthal_os.palpebra_lagophtalmus) {
                objOsOftalPalpebraLagophtalmus = this.OptionsOftalPalpebraLagophtalmus.find(el => el.value === obj.ophthal_os.palpebra_lagophtalmus)
            }
            if (obj.ophthal_os && obj.ophthal_os.palpebra_tumor) {
                objOsOftalPalpebraTumor = this.OptionsOftalPalpebraTumor.find(el => el.value === obj.ophthal_os.palpebra_tumor)
            }
            if (obj.ophthal_os && obj.ophthal_os.apparatus_lakrimalis) {
                objOsOftalApparetus = this.OptionsOftalApparetus.find(el => el.value === obj.ophthal_os.apparatus_lakrimalis)
            }
            if (obj.ophthal_os && obj.ophthal_os.silia) {
                objOsOftalSilia = this.OptionsOftalSilia.find(el => el.value === obj.ophthal_os.silia)
            }
            if (obj.ophthal_os && obj.ophthal_os.conjunctive_hiperemis) {
                objOsOftalConjHiperemis = this.OptionsOftalConjHiperemis.find(el => el.value === obj.ophthal_os.conjunctive_hiperemis)
            }
            if (obj.ophthal_os && obj.ophthal_os.conjunctive_bleed) {
                objOsOftalConjBleed = this.OptionsOftalConjBleed.find(el => el.value === obj.ophthal_os.conjunctive_bleed)
            }
            if (obj.ophthal_os && obj.ophthal_os.cornea) {
                objOsOftalKornea = this.OptionsOftalKornea.find(el => el.value === obj.ophthal_os.cornea)
            }
            if (obj.ophthal_os && obj.ophthal_os.camera_oculi) {
                objOsOftalAnterior = this.OptionsOftalAnterior.find(el => el.value === obj.ophthal_os.camera_oculi)
            }
            if (obj.ophthal_os && obj.ophthal_os.iris_color) {
                objOsOftalIrisColor = this.OptionsOftalIrisColor.find(el => el.value === obj.ophthal_os.iris_color)
            }
            if (obj.ophthal_os && obj.ophthal_os.iris_cripte) {
                objOsOftalIrisKripte = this.OptionsOftalIrisKripte.find(el => el.value === obj.ophthal_os.iris_cripte)
            }
            if (obj.ophthal_os && obj.ophthal_os.pupil_form) {
                objOsOftalPupilForm = this.OptionsOftalPupilForm.find(el => el.value === obj.ophthal_os.pupil_form)
            }
            if (obj.ophthal_os && obj.ophthal_os.pupil_diameter) {
                objOsOftalPupilDiameter = this.OptionsOftalPupilDiameter.find(el => el.value === obj.ophthal_os.pupil_diameter)
            }
            if (obj.ophthal_os && obj.ophthal_os.pupil_direct_light) {
                objOsOftalPupilDirectLight = this.OptionsOftalPupilDirectLight.find(el => el.value === obj.ophthal_os.pupil_direct_light)
            }
            if (obj.ophthal_os && obj.ophthal_os.pupil_indirect_light) {
                objOsOftalPupilIndirectLight = this.OptionsOftalPupilIndirectLight.find(el => el.value === obj.ophthal_os.pupil_indirect_light)
            }
            if (obj.ophthal_os && obj.ophthal_os.lens) {
                objOsOftalLens = this.OptionsOftalLens.find(el => el.value === obj.ophthal_os.lens)
            }
            if (obj.ophthal_os && obj.ophthal_os.shadow_test) {
                objOsOftalShadow = this.OptionsOftalShadow.find(el => el.value === obj.ophthal_os.shadow_test)
            }
            if (obj.ophthal_os && obj.ophthal_os.glandula) {
                objOsOftalGlandula = this.OptionsOftalGlandula.find(el => el.value === obj.ophthal_os.glandula)
            }
            if (obj.ophthal_os && obj.ophthal_os.field_view) {
                objOsOftalLapang = this.OptionsOftalLapang.find(el => el.value === obj.ophthal_os.field_view)
            }

            this.generalist.head = objHead ? objHead.title : '-'
            this.generalist.head_hair_color = objHairColor ? objHairColor.title : '-'
            this.generalist.head_hair_condition = objHairCondition ? objHairCondition.title : '-'
            this.generalist.eye_conjunctive = objEyeConjunctive ? objEyeConjunctive.title : '-'
            this.generalist.eye_sclera = objEyeSclera ? objEyeSclera.title : '-'
            this.generalist.eye_pupil = objEyePupil ? objEyePupil.title : '-'
            this.generalist.eye_light_reflex = objEyeReflect ? objEyeReflect.title : '-'
            this.generalist.mouth_mucosa = objMouthMucosa ? objMouthMucosa.title : '-'
            this.generalist.mouth_discoloration = objMouthDiscoloration ? objMouthDiscoloration.title : '-'
            this.generalist.mouth_tongue = objMouthTongue ? objMouthTongue.title : '-'
            this.generalist.mouth_caries = objMouthCaries ? objMouthCaries.title : '-'
            this.generalist.neck_kgb = objNeckKGB ? objNeckKGB.title : '-'
            this.generalist.neck_trachea_position = objNeckTracheaPosition ? objNeckTracheaPosition.title : '-'
            this.generalist.neck_massa = objNeckMassa ? objNeckMassa.title : '-'
            this.generalist.thorax_insp_chest = objInspectionChest ? objInspectionChest.title : '-'
            this.generalist.thorax_insp_retraction = objInspectionRetractiont ? objInspectionRetractiont.title : '-'
            this.generalist.thorax_palp_chest = objPalpationChest ? objPalpationChest.title : '-'
            this.generalist.thorax_palp_fremitus = objPalpationFremitus ? objPalpationFremitus.title : '-'
            this.generalist.thorax_palp_crepitation = objPalpationKrepitasi ? objPalpationKrepitasi.title : '-'
            this.generalist.thorax_ausk_pul_vasiculer = objAuskultationPulmoVasikuler ? objAuskultationPulmoVasikuler.title : '-'
            this.generalist.thorax_ausk_pul_rhonki = objAuskultationPulmoRhonki ? objAuskultationPulmoRhonki.title : '-'
            this.generalist.thorax_ausk_pul_wheezing = objAuskultationPulmoWheezing ? objAuskultationPulmoWheezing.title : '-'
            this.generalist.thorax_ausk_cor_heart_sound = objAuskultationCORBJ ? objAuskultationCORBJ.title : '-'
            this.generalist.thorax_ausk_cor_murmur = objAuskultationCORMurmur ? objAuskultationCORMurmur.title : '-'
            this.generalist.thorax_ausk_cor_gallop = objAuskultationCORGallop ? objAuskultationCORGallop.title : '-'
            this.generalist.abdomen_supel = objAbdomenSupel ? objAbdomenSupel.title : '-'
            this.generalist.abdomen_bowel_sounds = objAbdomenBising ? objAbdomenBising.title : '-'
            this.generalist.abdomen_tenderness = objAbdomenPress ? objAbdomenPress.title : '-'
            this.generalist.abdomen_hepar_lien = objAbdomenHepar ? objAbdomenHepar.title : '-'
            this.generalist.extremity_acral = objEkstremitasAkral ? objEkstremitasAkral.title : '-'
            this.generalist.extremity_crt = objEkstremitasCRT ? objEkstremitasCRT.title : '-'
            this.generalist.extremity_edema = objEkstremitasEdema ? objEkstremitasEdema.title : '-'
            this.generalist.abdomen_other_physical_check = obj.abdomen_other_physical_check
            this.generalist.genitalia = obj.genitalia
            this.generalist.localist_status = obj.localist_status
            this.generalist.ophthal_status = obj.ophthal_status

            this.generalist.ophthal_od.visus = objOdOftalVisus ? objOdOftalVisus.title : '-'
            this.generalist.ophthal_od.tio_digital = objOdOftalTIO ? objOdOftalTIO.title : '-'
            this.generalist.ophthal_od.eye_position = objOdOftalEyePosition ? objOdOftalEyePosition.title : '-'
            this.generalist.ophthal_od.eye_movement = objOdOftalMovement ? objOdOftalMovement.title : '-'
            this.generalist.ophthal_od.super_silia = objOdOftalSuperSilia ? objOdOftalSuperSilia.title : '-'
            this.generalist.ophthal_od.palpebra_edema = objOdOftalPalpebraEdema ? objOdOftalPalpebraEdema.title : '-'
            this.generalist.ophthal_od.palpebra_ptosis = objOdOftalPalpebraPtosis ? objOdOftalPalpebraPtosis.title : '-'
            this.generalist.ophthal_od.palpebra_lagophtalmus = objOdOftalPalpebraLagophtalmus ? objOdOftalPalpebraLagophtalmus.title : '-'
            this.generalist.ophthal_od.palpebra_tumor = objOdOftalPalpebraTumor ? objOdOftalPalpebraTumor.title : '-'
            this.generalist.ophthal_od.apparatus_lakrimalis = objOdOftalApparetus ? objOdOftalApparetus.title : '-'
            this.generalist.ophthal_od.silia = objOdOftalSilia ? objOdOftalSilia.title : '-'
            this.generalist.ophthal_od.conjunctive_hiperemis = objOdOftalConjHiperemis ? objOdOftalConjHiperemis.title : '-'
            this.generalist.ophthal_od.conjunctive_bleed = objOdOftalConjBleed ? objOdOftalConjBleed.title : '-'
            this.generalist.ophthal_od.cornea = objOdOftalKornea ? objOdOftalKornea.title : '-'
            this.generalist.ophthal_od.camera_oculi = objOdOftalAnterior ? objOdOftalAnterior.title : '-'
            this.generalist.ophthal_od.iris_color = objOdOftalIrisColor ? objOdOftalIrisColor.title : '-'
            this.generalist.ophthal_od.iris_cripte = objOdOftalIrisKripte ? objOdOftalIrisKripte.title : '-'
            this.generalist.ophthal_od.pupil_form = objOdOftalPupilForm ? objOdOftalPupilForm.title : '-'
            this.generalist.ophthal_od.pupil_diameter = objOdOftalPupilDiameter ? objOdOftalPupilDiameter.title : '-'
            this.generalist.ophthal_od.pupil_direct_light = objOdOftalPupilDirectLight ? objOdOftalPupilDirectLight.title : '-'
            this.generalist.ophthal_od.pupil_indirect_light = objOdOftalPupilIndirectLight ? objOdOftalPupilIndirectLight.title : '-'
            this.generalist.ophthal_od.lens = objOdOftalLens ? objOdOftalLens.title : '-'
            this.generalist.ophthal_od.shadow_test = objOdOftalShadow ? objOdOftalShadow.title : '-'
            this.generalist.ophthal_od.glandula = objOdOftalGlandula ? objOdOftalGlandula.title : '-'
            this.generalist.ophthal_od.field_view = objOdOftalLapang ? objOdOftalLapang.title : '-'
            this.generalist.ophthal_od.tio_tonometri_sciots = obj.ophthal_od && obj.ophthal_od.tio_tonometri_sciots ? obj.ophthal_od.tio_tonometri_sciots : '-'
            this.generalist.ophthal_od.fundus = obj.ophthal_od && obj.ophthal_od.fundus ? obj.ophthal_od.fundus : '-'

            this.generalist.ophthal_os.visus = objOsOftalVisus ? objOsOftalVisus.title : '-'
            this.generalist.ophthal_os.tio_digital = objOsOftalTIO ? objOsOftalTIO.title : '-'
            this.generalist.ophthal_os.eye_position = objOsOftalEyePosition ? objOsOftalEyePosition.title : '-'
            this.generalist.ophthal_os.eye_movement = objOsOftalMovement ? objOsOftalMovement.title : '-'
            this.generalist.ophthal_os.super_silia = objOsOftalSuperSilia ? objOsOftalSuperSilia.title : '-'
            this.generalist.ophthal_os.palpebra_edema = objOsOftalPalpebraEdema ? objOsOftalPalpebraEdema.title : '-'
            this.generalist.ophthal_os.palpebra_ptosis = objOsOftalPalpebraPtosis ? objOsOftalPalpebraPtosis.title : '-'
            this.generalist.ophthal_os.palpebra_lagophtalmus = objOsOftalPalpebraLagophtalmus ? objOsOftalPalpebraLagophtalmus.title : '-'
            this.generalist.ophthal_os.palpebra_tumor = objOsOftalPalpebraTumor ? objOsOftalPalpebraTumor.title : '-'
            this.generalist.ophthal_os.apparatus_lakrimalis = objOsOftalApparetus ? objOsOftalApparetus.title : '-'
            this.generalist.ophthal_os.silia = objOsOftalSilia ? objOsOftalSilia.title : '-'
            this.generalist.ophthal_os.conjunctive_hiperemis = objOsOftalConjHiperemis ? objOsOftalConjHiperemis.title : '-'
            this.generalist.ophthal_os.conjunctive_bleed = objOsOftalConjBleed ? objOsOftalConjBleed.title : '-'
            this.generalist.ophthal_os.cornea = objOsOftalKornea ? objOsOftalKornea.title : '-'
            this.generalist.ophthal_os.camera_oculi = objOsOftalAnterior ? objOsOftalAnterior.title : '-'
            this.generalist.ophthal_os.iris_color = objOsOftalIrisColor ? objOsOftalIrisColor.title : '-'
            this.generalist.ophthal_os.iris_cripte = objOsOftalIrisKripte ? objOsOftalIrisKripte.title : '-'
            this.generalist.ophthal_os.pupil_form = objOsOftalPupilForm ? objOsOftalPupilForm.title : '-'
            this.generalist.ophthal_os.pupil_diameter = objOsOftalPupilDiameter ? objOsOftalPupilDiameter.title : '-'
            this.generalist.ophthal_os.pupil_direct_light = objOsOftalPupilDirectLight ? objOsOftalPupilDirectLight.title : '-'
            this.generalist.ophthal_os.pupil_indirect_light = objOsOftalPupilIndirectLight ? objOsOftalPupilIndirectLight.title : '-'
            this.generalist.ophthal_os.lens = objOsOftalLens ? objOsOftalLens.title : '-'
            this.generalist.ophthal_os.shadow_test = objOsOftalShadow ? objOsOftalShadow.title : '-'
            this.generalist.ophthal_os.glandula = objOsOftalGlandula ? objOsOftalGlandula.title : '-'
            this.generalist.ophthal_os.field_view = objOsOftalLapang ? objOsOftalLapang.title : '-'
            this.generalist.ophthal_os.tio_tonometri_sciots = obj.ophthal_os && obj.ophthal_os.tio_tonometri_sciots ? obj.ophthal_os.tio_tonometri_sciots : '-'
            this.generalist.ophthal_os.fundus = obj.ophthal_os && obj.ophthal_os.fundus ? obj.ophthal_os.fundus : '-'

        },

        getAirwayIcon(value) {
            if (this.triageData && this.triageData.breathing_path_arry) {
                return this.triageData.breathing_path_arry.includes(value) ? "check-square-fill" : "square-fill";
            } else {
                return "square-fill";
            }
        },
        getAirwayIconVariant(value) {
            if (this.triageData && this.triageData.breathing_path_arry) {
                return this.triageData.breathing_path_arry.includes(value) ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },
        getRespiratoryIcon(value) {
            if (this.triageData && this.triageData.breathing_arry) {
                return this.triageData.breathing_arry.includes(value) ? "check-square-fill" : "square-fill";
            } else {
                return "square-fill";
            }
        },
        getRespiratoryIconVariant(value) {
            if (this.triageData && this.triageData.breathing_arry) {
                return this.triageData.breathing_arry.includes(value) ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },
        getCirculationIcon(value) {
            if (this.triageData && this.triageData.circulation_arry) {
                return this.triageData.circulation_arry.includes(value) ? "check-square-fill" : "square-fill";
            } else {
                return "square-fill";
            }
        },
        getCirculationIconVariant(value) {
            if (this.triageData && this.triageData.circulation_arry) {
                return this.triageData.circulation_arry.includes(value) ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },
        getConsciousnessIcon(value) {
            if (this.triageData && this.triageData.consciousness_arry) {
                return this.triageData.consciousness_arry.includes(value) ? "check-square-fill" : "square-fill";
            } else {
                return "square-fill";
            }
        },
        getConsciousnessIconVariant(value) {
            if (this.triageData && this.triageData.consciousness_arry) {
                return this.triageData.consciousness_arry.includes(value) ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },
        getResourceIcon(value) {
            if (this.triageData && this.triageData.resource_arry) {
                return this.triageData.resource_arry.includes(value) ? "check-square-fill" : "square-fill";
            } else {
                return "square-fill";
            }
        },
        getResourceIconVariant(value) {
            if (this.triageData && this.triageData.resource_arry) {
                return this.triageData.resource_arry.includes(value) ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },
        getSecondResourceIcon(value) {
            if (this.triageData && this.triageData.resuscitation_resource_arry) {
                return this.triageData.resuscitation_resource_arry.includes(value) ? "check-square-fill" : "square-fill";
            } else {
                return "square-fill";
            }
        },
        getSecondResourceIconVariant(value) {
            if (this.triageData && this.triageData.resuscitation_resource_arry) {
                return this.triageData.resuscitation_resource_arry.includes(value) ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },
        getRadioZoneIcon(value) {
            if (this.triageData && this.triageData.age_type_id) {
                return this.triageData.age_type_id == value ? "check-circle-fill" : "circle-fill";
            } else {
                return "circle-fill";
            }
        },
        getRadioZoneIconVariant(value) {
            if (this.triageData && this.triageData.age_type_id) {
                return this.triageData.age_type_id == value ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },
        getPainIcon(value) {
            if (this.triageData && this.triageData && this.triageData.pain_score) {
                return this.triageData.pain_score == value ? "check-circle-fill" : "circle-fill";
            } else {
                return "circle-fill";
            }
        },
        getPainIconVariant(value) {
            if (this.triageData && this.triageData && this.triageData.pain_score) {
                return this.triageData.pain_score == value ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },
        getFallAIcon(value) {
            if (this.emergencyData && this.emergencyData.fall_risk_component_a === 0 || this.emergencyData && this.emergencyData.fall_risk_component_a === 1) {
                return this.emergencyData.fall_risk_component_a == value ? "check-circle-fill" : "circle-fill";
            } else {
                return "circle-fill";
            }
        },
        getFallAIconVariant(value) {
            if (this.emergencyData && this.emergencyData.fall_risk_component_a === 0 || this.emergencyData && this.emergencyData.fall_risk_component_a === 1) {
                return this.emergencyData.fall_risk_component_a == value ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },
        getFallBIcon(value) {
            if (this.emergencyData && this.emergencyData.fall_risk_component_b === 0 || this.emergencyData && this.emergencyData.fall_risk_component_b === 1) {
                return this.emergencyData.fall_risk_component_b == value ? "check-circle-fill" : "circle-fill";
            } else {
                return "circle-fill";
            }
        },
        getFallBIconVariant(value) {
            if (this.emergencyData && this.emergencyData.fall_risk_component_b === 0 || this.emergencyData && this.emergencyData.fall_risk_component_b === 1) {
                return this.emergencyData.fall_risk_component_b == value ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },
        handleKeyPress(event) {
            if (event.ctrlKey && event.key === 'p') {
                event.preventDefault();
                this.printCanvas();
            }
        },
        localDate(input_date) {
            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },
        localDateRanap(input_date) {
            return moment(input_date).locale("id").format('LLLL')
        },
        localDateEklaim(input_date) {
            return moment(input_date).locale("id").format('DD/MM/YYYY')
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
        parsed_age_day(input_date) {
            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const currentDate = moment();
            const days = currentDate.diff(birthDate, 'days');
            return days;
        },
        cek_status (data) {
            if (data === 'Atas Persetujuan Dokter') {
                return '1';
            } else if (data === 'Dirujuk') {
                return '2';
            } else if (data === 'Permintaan Sendiri') {
                return '3';
            } else if (data === 'Meninggal') {
                return '4';
            } else {
                return '5';
            }
        },
        los(input_date) {
            const birthDate = moment(this.regData.register_date, 'YYYY-MM-DD');
            const currentDate = moment(input_date, 'YYYY-MM-DD');
            const days = currentDate.diff(birthDate, 'days');
            return `${days+1} hari`;
        },
        parsed_age_year(input_date) {
            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const currentDate = moment();
            const years = currentDate.diff(birthDate, 'years');
            birthDate.add(years, 'years');
            const months = currentDate.diff(birthDate, 'months');
            birthDate.add(months, 'months');
            const days = currentDate.diff(birthDate, 'days');
            return years;
        },
        getDataLab (id) {
            this.resultItems = []
            let temp_i = []
            let temp_j = []
			this.$store.dispatch('laboratory/getOrderByRegisterId', id).then(data => {
                if (data.rows.length > 0) {
                    this.orderData = data.rows

                    for (let i = 0; i < this.orderData.length; i++) {
                        const el = this.orderData[i];
                        temp_j = []
                        for (let j = 0; j < el.laboratory_request_items.length; j++) {
                            const list = el.laboratory_request_items[j];
                            if (list?.result?.length > 0) {
                                temp_j.push(...list.result)
                            }
                        }
                        temp_i.push({result: [...temp_j], order_id: el.id})
                    }
                    this.resultItems = temp_i
                }
                // this.$store.dispatch('laboratory/getResultById', id).then(result => {
                //     this.resultlab = result[0]
                //     // console.log('this.result', this.result);
                // })
            })
        },
        getDataRad (id) {
			this.$store.dispatch('radiology/getOrderByRegisterId', id).then(data => {
                if (data.rows.length > 0) {
                    this.orderDataRad = data.rows
                    console.log('console rad',data)
                    // this.reqItemData = this.orderDataRad?.radiology_request_item
                    // this.reqItemData = req_item.filter((item) => item.status !== 1)
                }
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
                this.trans_7_total = 0
                this.trans_10_total = 0
                this.trans_11_total = 0
                this.trans_12_total = 0
                this.trans_13_total = 0
                this.trans_14_total = 0
                this.trans_15_total = 0

                this.grand_total = 0


                for (let index = 0; index < transactions.length; index++) {
                    const element = transactions[index];


                    if (element.type_transaction_id === 1) {
                        let price = element.var_1 != '' ? element.var_1 : 0
                        let qty = element.var_2 != '' ? element.var_2 : 0

                        this.trans_1.push(element)
                        this.trans_1.sort(function(a,b) {return (a.costing_name > b.costing_name) ? 1 : ((b.costing_name > a.costing_name) ? -1 : 0);} );
                        this.trans_1_total += (price*qty)
                        this.trans_1.sort((a, b) => new Date(a.date_added) - new Date(b.date_added));
                        this.group_trans_1.push({title:element.classification_three_type_name,value:element.classification_three_type_id})
                    }

                    if (element.type_transaction_id === 2) {
                        let discount = element.var_4 != '' ? element.var_4 : 0
                        let price = element.var_1 != '' ? element.var_1 : 0
                        let qty = element.var_2 != '' ? element.var_2 : 0
                        let temp_total = (price*qty) - discount

                        this.trans_2.push({ ...element, count_total: temp_total })
                        this.trans_2.sort(function(a,b) {return (a.costing_name > b.costing_name) ? 1 : ((b.costing_name > a.costing_name) ? -1 : 0);} );
                        this.trans_2_total += temp_total
                        this.group_trans_2.push({title:element.classification_three_type_name,value:element.classification_three_type_id})
                        this.trans_2.sort((a, b) => new Date(a.date_added) - new Date(b.date_added));
                    }

                    if (element.type_transaction_id === 5) {
                        this.trans_5.push(element)
                        this.trans_5_total += element.var_1
                        this.trans_5.sort((a, b) => new Date(a.date_added) - new Date(b.date_added));
                    }

                    if (element.type_transaction_id === 10) {
                        let price = element.var_1 != '' ? element.var_1 : 0
                        let qty = element.var_2 != '' ? element.var_2 : 0

                        this.trans_10.push(element)
                        this.trans_10_total += (price*qty)
                        this.trans_10.sort((a, b) => new Date(a.date_added) - new Date(b.date_added));
                    }

                    if (element.type_transaction_id === 11) {
                        this.trans_11.push(element)
                        this.trans_11_total += element.var_1
                        this.trans_11.sort((a, b) => new Date(a.date_added) - new Date(b.date_added));
                    }

                    if (element.type_transaction_id === 12) {
                        this.trans_12.push(element)
                        this.trans_12_total += element.var_1
                        this.trans_12.sort((a, b) => new Date(a.date_added) - new Date(b.date_added));
                    }

                    if (element.type_transaction_id === 13) {
                        let discount = element.var_4 != '' ? element.var_4 : 0
                        let price = element.var_1 != '' ? element.var_1 : 0
                        let temp_total = price - discount

                        this.trans_13.push({ ...element, count_total: temp_total })
                        this.trans_13_total += temp_total
                        this.trans_13.sort((a, b) => new Date(a.date_added) - new Date(b.date_added));
                    }

                    if (element.type_transaction_id === 14) {
                        this.trans_14.push(element)
                    }

                    if (element.type_transaction_id === 15) {
                        this.trans_15.push(element)
                        this.trans_15_total += element.var_1
                        this.trans_15.sort((a, b) => new Date(a.date_added) - new Date(b.date_added));
                    }

                    if (element.type_transaction_id === 7) {
                        let discount = 0
                        let price = element.var_1 != '' ? element.var_1 : 0
                        let qty = element.var_2 != '' ? element.var_2 : 0
                        let temp_total = (price*qty) - discount

                        this.trans_7.push({ ...element, count_total: temp_total })
                        this.trans_7.sort(function(a,b) {return (a.costing_name > b.costing_name) ? 1 : ((b.costing_name > a.costing_name) ? -1 : 0);} );
                        this.trans_7_total += temp_total
                        this.trans_7.sort((a, b) => new Date(a.date_added) - new Date(b.date_added));
                    }
                }
                this.getTotalPrec(this.trans_14)

                const group_trans_1 = this.group_trans_1.filter((item, index, self) =>
                    index === self.findIndex(t => JSON.stringify(t) === JSON.stringify(item))
                );
                this.group_trans_1 = group_trans_1
                this.group_trans_1.sort((a, b) => new Date(a.value) - new Date(b.value));

                const group_trans_2 = this.group_trans_2.filter((item, index, self) =>
                    index === self.findIndex(t => JSON.stringify(t) === JSON.stringify(item))
                );
                this.group_trans_2 = group_trans_2
                this.group_trans_2.sort((a, b) => new Date(a.value) - new Date(b.value));
                this.grand_total = this.trans_1_total  + this.trans_2_total  + this.trans_5_total  + this.trans_7_total  + this.trans_10_total  + this.trans_11_total  + this.trans_12_total + this.trans_14_total  + this.trans_15_total

                if (this.grand_total) {
                    this.trans_13 = []
                    this.trans_13_total = 0
                    for (let index = 0; index < transactions.length; index++) {
                        const element = transactions[index]
                        if (element.type_transaction_id === 13) {
                            let discount = element.var_4 != '' ? element.var_4 : 0
                            let price = element.var_1 != '' ? element.var_1 : 0
                            if (element.costing_name === 'SERVICE CHARGE') {
                                price = 0
                                price = (this.grand_total * 3)/100 > 5000000 ? 5000000 : (this.grand_total * 3)/100
                                element.var_1 = price
                            }
                            let temp_total = price - discount
                            this.trans_13.push({ ...element, count_total: temp_total })
                            this.trans_13_total += temp_total
                            this.trans_13.sort((a, b) => new Date(a.date_added) - new Date(b.date_added));
                        }
                    }
                    this.grand_total = 0
                    this.grand_total = this.trans_1_total  + this.trans_2_total  + this.trans_5_total  + this.trans_7_total  + this.trans_10_total  + this.trans_11_total  + this.trans_12_total  + this.trans_13_total  + this.trans_14_total  + this.trans_15_total
                }
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
                                let tmp_qty_retur = data.is_retur_full === 1 && data.pharmacy_catalog_id_retur ? 0 : data.qty_retur
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
            const canvasRR = document.querySelector("#qrcodeCanvasRR canvas");
            if (canvasRR) {
                const dataURL = canvas.toDataURL();
                console.log('dataURL: ', dataURL);

                const renderQRRDiv = document.getElementById("renderQRR");

                const img = document.createElement("img");
                img.src = dataURL;
                img.alt = "Generated QR Code";
                img.style.width = "70px";
                canvas.style.display = "none"

                renderQRRDiv.appendChild(img);
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
                console.log('soap:', this.result)
                this.getJKN(soap_id)
                this.result_arry = data
                // console.log('soap:', this.result)

                if (this.result && this.result.doctor_in_charge_id) {
                    this.getFIRSTStudiesRajal(this.result.id, this.result.doctor_in_charge_id)
                }

                if (this.result && this.result.doctor_in_charge_id) {
                    this.getIGDStudies(this.result.id, this.result.doctor_in_charge_id)
                }

                if (this.result && this.result.doctor_in_charge_id_1) {
                    this.getFIRSTStudies(this.result.id, this.result.doctor_in_charge_id_1)
                }

                if (this.result && this.result.doctor_in_charge_id_2) {
                    this.getSECONDStudies(this.result.id, this.result.doctor_in_charge_id_2)
                }

                if (this.result && this.result.doctor_in_charge_id_3) {
                    this.getTHIRDStudies(this.result.id, this.result.doctor_in_charge_id_3)
                }

                if (this.result && this.result.doctor_in_charge_id_4) {
                    this.getFOURTHStudies(this.result.id, this.result.doctor_in_charge_id_4)
                }

                if (this.result && this.result.doctor_in_charge_id_5) {
                    this.getFIFTHStudies(this.result.id, this.result.doctor_in_charge_id_5)
                }

                if (this.result) {
                    this.getGeneralist(this.result)

                    const obj_od = Object.assign({}, this.result.ophthal_od);
                    const obj_os = Object.assign({}, this.result.ophthal_os);

                    const isODEmpty = Object.values(obj_od).every(value => value === null);
                    const isOSEmpty = Object.values(obj_os).every(value => value === null);

                    if (isODEmpty && isOSEmpty) {
                        this.active.ophtal = false
                        // console.log('empty');
                    } else {
                        this.active.ophtal = true
                        // console.log('not empty');
                    }

                }

                this.$store.dispatch('registration/getById', this.result.registration_destination_id).then(data => {
                    this.patient = data[0]
                    // console.log('patient', this.patient);
                })

                this.$store.dispatch('triage/getAllListV2', { registration_destination_id: this.result.registration_destination_id }).then(data => {
                    this.triageData = data[0]
                    // console.log('this.triageData', this.triageData);
                })

                this.$store.dispatch('nurse/getDetail', { registration_destination_id: this.result.registration_destination_id }).then((data ) => {
                    this.nurseData = data[0]
                    this.getDataNurse(data[0].id)
                    console.log('this.nurseData', this.nurseData);

                    if (this.nurseData) {
                        this.$store.dispatch('nurse/getEmergencyDetail', { nurse_id: this.nurseData.id }).then((igd) => {
                            this.emergencyData = igd[0]
                            // console.log('this.emergencyData', this.emergencyData);
                        })
                    }

                })

                this.$store.dispatch('registration/getById', this.result.registration_destination_id).then(data => {
                    this.patient = data[0]
                    if (this.patient.registration_insurance[0].file_kronologis) {
                        this.generateThumbnail('kronologis_'+this.patient.registration_insurance[0].registration_destination_id+'.pdf')
                    }
                    this.getDataInv(this.patient.registration_id)
                    console.log('patient', this.patient);
                })

                this.geLetterDeath(this.$route.params.soap_id)
                this.geLetterBirth(this.$route.params.soap_id)

                console.log(data[0].registration_id)
                if (data[0].registration_id) {
                    // if(data[0].laboratory_request){
                        this.getDataLab(data[0].registration_id)
                    // }
                    // if(data[0].radiology_request){
                        this.getDataRad(data[0].registration_id)
                    // }
                    this.$store.dispatch('soap/getExamination', {registration_id: data[0].registration_id}).then(response => {
                        this.examination_list = response
                        // console.log('this.examination_list: ', this.examination_list);
                    })
                }
            })
        },

        geLetterDeath (id) {
            this.$store.dispatch('letter/getByIdDeath', id).then(data => {
                if (data[0]?.content) {
                    this.letterDataDetail = JSON.parse(data[0]?.content)
                    this.letterData = data[0]
                    const dateB = moment(data[0].end);
                    const dateC = moment(data[0].start);
                    this.days = dateB.diff(dateC, 'days')+1
                }
            })
        },

        geLetterBirth (id) {
            // this.$store.dispatch('letter/getByIdBirth', id).then(data => {
            //     console.log(JSON.parse(data[0].content))
            //     this.letterDataDetailBirth = JSON.parse(data[0].content)
            //     this.letterData = data[0]
            //     const dateB = moment(data[0].end);
            //     const dateC = moment(data[0].start);
            //     this.days = dateB.diff(dateC, 'days')+1
            // })
        },

        getDataReg (id) {
			this.$store.dispatch('registration/getById', id).then(data => {
                console.log(data[0])
                this.getDataBpjs(data[0].registration_insurance[0].sep)
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

        getIGDStudies(soap_id, doctor_in_charge_id) {
            let param = { soap_id: soap_id, doctor_in_charge_id: doctor_in_charge_id }

            try {
                this.$store.dispatch('soap/getSoapP', param).then((response) => {
                    this.IGD_doctor.soap_p_list = response[0]
                })

                this.$store.dispatch('soap/getTransaction', param).then((response) => {
                    this.IGD_doctor.transaction_list = response
                    // console.log('this.IGD_doctor.transaction_list: ', this.IGD_doctor.transaction_list);
                })

                this.$store.dispatch('laboratory/getOrderDetail', param).then((response) => {
                    this.IGD_doctor.laboratory_list = response
                    // console.log('this.IGD_doctor.laboratory_list: ', this.IGD_doctor.laboratory_list);
                })

                this.$store.dispatch('radiology/getOrderDetail', param).then((response) => {
                    this.IGD_doctor.radiology_list = response
                    // console.log('this.IGD_doctor.radiology_list: ', this.IGD_doctor.radiology_list);
                })

                this.$store.dispatch('prescription/getDetail', param).then((response) => {
                    this.IGD_doctor.prescription_list = response
                    // console.log('this.IGD_doctor.prescription_list: ', this.IGD_doctor.prescription_list);
                })

            } catch (error) {
                console.error('Error at: ', error);
            }
        },

        getFIRSTStudiesRajal(soap_id, doctor_in_charge_id) {
            let param = { soap_id: soap_id, doctor_in_charge_id: doctor_in_charge_id }

            try {
                this.getDPJPStudies(param)
                this.$store.dispatch('soap/getSoapP', param).then((response) => {
                    this.FIRST_doctor.soap_p_list = response[0]
                })

                this.$store.dispatch('soap/getTransaction', param).then((response) => {
                    this.FIRST_doctor.transaction_list = response
                    // console.log('this.FIRST_doctor.transaction_list: ', this.FIRST_doctor.transaction_list);
                })

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

        getFIRSTStudies(soap_id, doctor_in_charge_id) {
            let param = { soap_id: soap_id, doctor_in_charge_id: doctor_in_charge_id }

            try {
                this.$store.dispatch('soap/getSoapP', param).then((response) => {
                    this.FIRST_doctor.soap_p_list = response[0]
                })

                this.$store.dispatch('soap/getTransaction', param).then((response) => {
                    this.FIRST_doctor.transaction_list = response
                    // console.log('this.FIRST_doctor.transaction_list: ', this.FIRST_doctor.transaction_list);
                })

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

        getSECONDStudies(soap_id, doctor_in_charge_id) {
            let param = { soap_id: soap_id, doctor_in_charge_id: doctor_in_charge_id }

            try {
                this.$store.dispatch('soap/getSoapP', param).then((response) => {
                    this.SECOND_doctor.soap_p_list = response[0]
                    // console.log('this.SECOND_doctor.soap_p_list: ', this.SECOND_doctor.soap_p_list);
                })

                this.$store.dispatch('soap/getTransaction', param).then((response) => {
                    this.SECOND_doctor.transaction_list = response
                    // console.log('this.SECOND_doctor.transaction_list: ', this.SECOND_doctor.transaction_list);
                })

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

        getTHIRDStudies(soap_id, doctor_in_charge_id) {
            let param = { soap_id: soap_id, doctor_in_charge_id: doctor_in_charge_id }

            try {
                this.$store.dispatch('soap/getSoapP', param).then((response) => {
                    this.THIRD_doctor.soap_p_list = response[0]
                })

                this.$store.dispatch('soap/getTransaction', param).then((response) => {
                    this.THIRD_doctor.transaction_list = response
                })

                this.$store.dispatch('laboratory/getOrderDetail', param).then((response) => {
                    this.THIRD_doctor.laboratory_list = response
                })

                this.$store.dispatch('radiology/getOrderDetail', param).then((response) => {
                    this.THIRD_doctor.radiology_list = response
                })

                this.$store.dispatch('prescription/getDetail', param).then((response) => {
                    this.THIRD_doctor.prescription_list = response
                })

            } catch (error) {
                console.error('Error at: ', error);
            }
        },

        getFOURTHStudies(soap_id, doctor_in_charge_id) {
            let param = { soap_id: soap_id, doctor_in_charge_id: doctor_in_charge_id }

            try {
                this.$store.dispatch('soap/getSoapP', param).then((response) => {
                    this.FOURTH_doctor.soap_p_list = response[0]
                })

                this.$store.dispatch('soap/getTransaction', param).then((response) => {
                    this.FOURTH_doctor.transaction_list = response
                })

                this.$store.dispatch('laboratory/getOrderDetail', param).then((response) => {
                    this.FOURTH_doctor.laboratory_list = response
                })

                this.$store.dispatch('radiology/getOrderDetail', param).then((response) => {
                    this.FOURTH_doctor.radiology_list = response
                })

                this.$store.dispatch('prescription/getDetail', param).then((response) => {
                    this.FOURTH_doctor.prescription_list = response
                })

            } catch (error) {
                console.error('Error at: ', error);
            }
        },

        getFIFTHStudies(soap_id, doctor_in_charge_id) {
            let param = { soap_id: soap_id, doctor_in_charge_id: doctor_in_charge_id }

            try {
                this.$store.dispatch('soap/getSoapP', param).then((response) => {
                    this.FIFTH_doctor.soap_p_list = response[0]
                })

                this.$store.dispatch('soap/getTransaction', param).then((response) => {
                    this.FIFTH_doctor.transaction_list = response
                })

                this.$store.dispatch('laboratory/getOrderDetail', param).then((response) => {
                    this.FIFTH_doctor.laboratory_list = response
                })

                this.$store.dispatch('radiology/getOrderDetail', param).then((response) => {
                    this.FIFTH_doctor.radiology_list = response
                })

                this.$store.dispatch('prescription/getDetail', param).then((response) => {
                    this.FIFTH_doctor.prescription_list = response
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
