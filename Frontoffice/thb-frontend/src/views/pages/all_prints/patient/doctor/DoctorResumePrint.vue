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
                            <p>{{localDateEklaim(regData.register_date)}}</p>
                        </div>
                    </div>


                    <div class="canvas-body mt-32 px-40"  style="display:none">
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
                                            <td>{{ pesertaData.peserta ? pesertaData.peserta.noKartu : '' }}</td>
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
                                            <td>{{pesertaData.noSep}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Tanggal Masuk</td>
                                            <td class="px-3">:</td>
                                            <td>{{localDateEklaim(regData.register_date)}}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Tanggal Keluar</td>
                                            <td class="px-3">:</td>
                                            <td>{{ localDateEklaim(regData.checkout_date ? regData.checkout_date : regData.register_date) }}</td>
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
                                            <td>{{ regData.health_facility_id === '3cb626f5-9148-4fb1-8663-3d7f028b01a6' || '444b3134-5b75-4b67-af72-a558d46716d7' ? '1 hari' : los(regData.checkout_date ? regData.checkout_date : regData.register_date) }}</td>
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
                                            <td>{{pesertaData?.dpjp?.nmDPJP}}</td>
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
                    <div style="margin-top: 50px;" v-if="pesertaData?.kontrol?.noSurat">
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
                    <img style="width:900px" :src="thumbnail" v-if="thumbnail" />
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
                                            <b-td>{{ this.result.main_complaint }}</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="col-3">Riwayat Penyakit Sekarang</b-td>
                                            <b-td class="px-8">:</b-td>
                                            <b-td>{{ this.result.history_disease_now }}</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="col-3">Riwayat Penyakit Dahulu</b-td>
                                            <b-td class="px-8">:</b-td>
                                            <b-td>{{ this.result.history_disease_past }}</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="col-3">Riwayat Alergi</b-td>
                                            <b-td class="px-8">:</b-td>
                                            <b-td>{{ this.result.history_allergy }}</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="col-3">Riwayat Obat</b-td>
                                            <b-td class="px-8">:</b-td>
                                            <b-td>{{ this.result.history_drugs }}</b-td>
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
                    <div class="row align-items-center pb-40" style="border-bottom: 2px solid black;margin-top: 3000px;" v-if="orderDataRad.no_request && reqItemData">
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
                    <div class="mt-20" v-if="orderDataRad.no_request && reqItemData">
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

                    <div class="row align-items-center pb-40" style="border-bottom: 2px solid black;margin-top: 2000px;">
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
                    <div class="canvas-body mt-32 px-40">
                        <section class="chead">
                            <div class="row mx-0 mt-28">
                                <div class="col-6">
                                    <table>
                                        <tr>
                                            <td class="first-cell">No. Nota</td>
                                            <td class="px-3">:</td>
                                            <td>{{ invoices.code }}</td>
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
                                            <td>{{ localDateRanap(regData.register_date) }}</td>
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
                                        <th>JENIS TINDAKAN</th>
                                        <th>LOKASI</th>
                                        <th>QTY</th>
                                        <th>HARGA</th>
                                        <th>TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template v-if="trans_1.length > 0" v-for="(item,i) in trans_1">
                                        <tr>
                                            <td class="text-left">{{ item.costing_name }}</td>
                                            <td>{{ regData.health_facility_item_name }}</td>
                                            <td>{{ item.var_1 }}</td>
                                            <td class="text-right">{{ item.var_2 | currency }}</td>
                                            <td class="text-right">{{ item.var_1*item.var_2 | currency }}</td>
                                        </tr>
                                        <tr v-if="i == trans_1.length-1" class="text-right" style="background-color: #FEF8D8">
                                            <td colspan="5">{{ trans_1_total | currency }}</td>
                                        </tr>
                                    </template>

                                    <template v-if="trans_2.length > 0" v-for="(item,i) in trans_2">
                                        <tr>
                                            <td class="text-left">{{item.costing_name}}<br>{{item.doctor_in_charge_name}}</td>
                                            <td>{{item.health_facility_item_name}}</td>
                                            <td>{{ item.var_2 }}</td>
                                            <td class="text-right">{{ item.var_1 | currency }}</td>
                                            <td class="text-right">{{ item.count_total | currency }}</td>
                                        </tr>
                                        <tr v-if="i == trans_2.length-1" class="text-right bg-warning-100">
                                            <td colspan="5">{{ trans_2_total | currency }}</td>
                                        </tr>
                                    </template>

                                    <template v-if="trans_5.length > 0" v-for="(item,i) in trans_5">
                                        <tr>
                                            <td class="text-left">{{item.costing_name}}<br>{{item.doctor_in_charge_name}}</td>
                                            <td>{{item.health_facility_item_name}}</td>
                                            <td>1</td>
                                            <td class="text-right">{{ item.var_1 | currency}}</td>
                                            <td class="text-right">{{ item.var_1 | currency}}</td>
                                        </tr>
                                        <tr v-if="i == trans_5.length-1" class="text-right bg-warning-100">
                                            <td colspan="5">{{ trans_5_total | currency }}</td>
                                        </tr>
                                    </template>

                                    <template v-if="trans_11.length > 0" v-for="(item,i) in trans_11">
                                        <tr>
                                            <td class="text-left">{{ item.costing_name }}</td>
                                            <td>LABORATORIUM</td>
                                            <td>1</td>
                                            <td class="text-right">{{ item.var_1 | currency }}</td>
                                            <td class="text-right">{{ item.var_1 | currency }}</td>
                                        </tr>
                                        <tr v-if="i == trans_11.length-1" class="text-right bg-warning-100">
                                            <b-td colspan="5">{{ trans_11_total | currency }}</b-td>
                                        </tr>
                                    </template>

                                    <template v-if="trans_12.length > 0" v-for="(item,i) in trans_12">
                                        <tr>
                                            <td class="text-left">{{ item.costing_name }}</td>
                                            <td>RADIOLOGI</td>
                                            <td>1</td>
                                            <td class="text-right">{{ item.var_1 | currency }}</td>
                                            <td class="text-right">{{ item.var_1 | currency }}</td>
                                        </tr>
                                        <tr v-if="i == trans_12.length-1" class="text-right bg-warning-100">
                                            <td colspan="5">{{ trans_12_total | currency }}</td>
                                        </tr>
                                    </template>

                                    <template v-if="trans_15.length > 0" v-for="(item,i) in trans_15">
                                        <tr>
                                            <td class="text-left">{{ item.costing_name }}</td>
                                            <td>{{ regData.health_facility_item_name }}</td>
                                            <td>1</td>
                                            <td class="text-right">{{ item.var_1 | currency }}</td>
                                            <td class="text-right">{{ item.var_1 | currency }}</td>
                                        </tr>
                                        <tr v-if="i == trans_15.length-1" class="text-right bg-warning-100">
                                            <td colspan="5">{{ trans_15_total | currency }}</td>
                                        </tr>
                                    </template>

                                    <template v-if="trans_10.length > 0" v-for="(item,i) in trans_10">
                                        <tr>
                                            <td class="text-left">{{ item.costing_name }}</td>
                                            <td>{{ item.health_facility_item_name }}</td>
                                            <td>{{ item.var_2 }}</td>
                                            <td class="text-right">{{ item.var_1 | currency }}</td>
                                            <td class="text-right">{{ item.var_1*item.var_2 | currency }}</td>
                                        </tr>
                                        <tr v-if="i == trans_10.length-1" class="text-right bg-warning-100">
                                            <td colspan="5">{{ trans_10_total | currency }}</td>
                                        </tr>
                                    </template>

                                    <tr>
                                        <td colspan="5" class="text-left">FARMASI</td>
                                    </tr>
                                    <template v-if="trans_14.length > 0" v-for="(item,i) in trans_14">
                                        <template v-for="(list, j) in item.prescription_item">
                                            <tr v-if="(list.is_retur_full !== 1 || list.pharmacy_catalog_id_retur) && list.qty-(list.is_retur_full === 1 && list.pharmacy_catalog_id_retur || list.pharmacy_catalog_id_ref ? 0 : list.qty_retur) !== 0">
                                                <td class="text-left">{{list.pharmacy_catalog_name}}</td>
                                                <td>{{ item.depo_name }}</td>
                                                <td>{{list.qty-(list.is_retur_full === 1 && list.pharmacy_catalog_id_retur || list.pharmacy_catalog_id_ref ? 0 : list.qty_retur)}}</td>
                                                <td class="text-right">{{list.var_1 | currency}}</td>
                                                <td class="text-right">{{ list.var_1*(list.qty-(list.is_retur_full === 1 && list.pharmacy_catalog_id_retur || list.pharmacy_catalog_id_ref ? 0 : list.qty_retur)) | currency }}</td>
                                            </tr>
                                        </template>
                                        <tr v-if="i == trans_14.length-1" class="text-right bg-warning-100">
                                            <td colspan="5">{{ trans_14_total | currency }}</td>
                                        </tr>
                                    </template>

                                    <tr>
                                        <td colspan="5" class="text-left">ADMINISTRASI</td>
                                    </tr>

                                    <template v-if="trans_13.length > 0" v-for="(item,i) in trans_13">
                                        <tr>
                                            <td class="text-left">{{ item.costing_name }}</td>
                                            <td>{{ regData.health_facility_item_name }}</td>
                                            <td>1</td>
                                            <td class="text-right">{{ item.var_1 | currency}}</td>
                                            <td class="text-right">{{ item.count_total | currency}}</td>
                                        </tr>
                                        <tr v-if="i == trans_13.length-1" class="text-right bg-warning-100">
                                            <td colspan="5">{{ trans_13_total | currency }}</td>
                                        </tr>
                                    </template>

                                    <tr>
                                        <th colspan="4" class="text-right">Biaya</th>
                                        <td class="text-right">{{invoices.var_3 | currency}}</td>
                                    </tr>

                                    <tr>
                                        <th colspan="4" class="text-right">Grand Total</th>
                                        <td class="text-right">{{invoices.var_3 | currency}}</td>
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
                                            <b-td colspan="5">{{ trans_1_total | currency }}</b-td>
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
                                            <b-td colspan="5">{{ trans_2_total | currency }}</b-td>
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
                                            <b-td colspan="5">{{ trans_5_total | currency }}</b-td>
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
                                            <b-td colspan="5">{{ trans_11_total | currency }}</b-td>
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
                                            <b-td colspan="5">{{ trans_12_total | currency }}</b-td>
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
                                            <b-td colspan="5">{{ trans_15_total | currency }}</b-td>
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
                                            <b-td colspan="5">{{ trans_10_total | currency }}</b-td>
                                        </b-tr>
                                    </template>

                                    <b-tr>
                                        <b-td colspan="5" class="text-left">FARMASI</b-td>
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
                                            <b-td colspan="5">{{ trans_14_total | currency }}</b-td>
                                        </b-tr>
                                    </template>

                                    <b-tr>
                                        <b-td colspan="5" class="text-left">ADMINISTRASI</b-td>
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
                                            <b-td colspan="5">{{ trans_13_total | currency }}</b-td>
                                        </b-tr>
                                    </template>

                                    <b-tr>
                                        <b-th colspan="5" class="text-right">Biaya</b-th>
                                        <b-td class="text-right">{{invoices.var_3 | currency}}</b-td>
                                    </b-tr>

                                    <b-tr>
                                        <b-th colspan="5" class="text-right">Grand Total</b-th>
                                        <b-td class="text-right">{{invoices.var_3 | currency}}</b-td>
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
                                <p>Bekasi, {{ localDate(regData.register_date) }}</p>
                                <template v-if="invoices.ttd">
                                    <b-img :src="urlbe+invoices.ttd" style="width: 120px;"/>
                                </template>
                                <p>( Kasir )</p>
                                <p class="text-uppercase mt-2">({{ invoices.cashier_name }})</p>
                            </div>

                        </section>
                    </div>
                    <!-- <div :src="`data:application/pdf;base64,`+`http://192.168.100.3:5000/${patient.registration_insurance[0].file_kronologis}`"></div> -->
                    <canvas id="pdf-canvas" style="display: none;"></canvas>

                    <!-- <iframe style="border: none" width="100%" height="1300" :src="`http://192.168.100.3:5000/${patient.registration_insurance[0].file_kronologis}`" v-if="patient.registration_insurance[0].file_kronologis" /> -->
                    <template v-for="(item,i) in examination_list" v-if="examination_list.length > 0">
                        <template v-for="(item_data,x) in item.data.files" v-if="item.data.files.length > 0">
                            <!-- <iframe style="width: 100vw; height: 100vh" :src="`http://192.168.100.3:5000/${item_data.url}`" v-if="item_data.url" /> -->
                            <img style="width:900px" :src="`http://192.168.100.3:5000/${item_data.url}`" v-if="item_data.url" /><br>
                            {{ item_data?.expertise ? 'Expertise :' : '' }}  {{ item_data?.expertise }}
                        </template>
                    </template>

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
        },
        currencys(value) {
            return value != null ?  value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') : 0
        }
    },
    methods: {
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
                this.getDataReg(this.result.registration_destination_id)
                console.log('soap:', this.result)
                this.getJKN(soap_id)
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
                    if (this.patient.registration_insurance[0].file_kronologis) {
                        this.generateThumbnail('kronologis_'+this.patient.registration_insurance[0].registration_destination_id+'.pdf')
                    }
                    this.getDataInv(this.patient.registration_id)
                    console.log('patient', this.patient);
                })
                if(data[0].laboratory_request){
                    this.getDataLab(data[0].laboratory_request.id)
                }
                if(data[0].radiology_request){
                    this.getDataRad(data[0].radiology_request.id)
                }
                this.$store.dispatch('soap/getExamination', {soap_id: this.result.id}).then(response => {
                    this.examination_list = response
                    // console.log('this.examination_list: ', this.examination_list);
                })
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
