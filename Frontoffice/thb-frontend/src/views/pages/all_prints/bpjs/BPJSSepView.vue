<template>
    <div>
        <b-container fluid="xl" class="mt-32 pb-32">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="generateToImage" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="d-flex justify-content-center">
                <div id="canvas" ref="canvas" class="mt-16">
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
                                        <td>{{ pesertaData.peserta ? pesertaData.peserta.noKartu : '' }} <span class="ml-16">MR. {{ pesertaData.peserta ? regData.norm : '' }}</span></td>
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
                                        <td>{{ pesertaData.peserta ? pesertaData.peserta.hakKelas : '' }}</td>
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
            </div>
        </b-container>
    </div>
</template>
<script>
import QrcodeVue from 'qrcode.vue';

export default {
    components: {
        QrcodeVue
    },
    data() {
        return {
            regData: {},
            pesertaData: {},
            globalQRText: '',
            regIns:{}
        }
    },
    methods: {
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
        async getData(id) {
            try {
                const response = await this.$store.dispatch('registration/getById', id)
                this.regData = response[0]
            } catch (error) {
                this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
            }
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
        },

        printCanvas() {
            const canvas = this.$refs.canvas;
            const originalContents = document.body.innerHTML;
            document.body.innerHTML = canvas.innerHTML;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload()
        },

        handleKeyPress(event) {
            if (event.ctrlKey && event.key === 'p') {
                event.preventDefault();
                this.generateToImage()
            }
        },

        closeWindow() {
            window.close()
        }
    },
    created() {
        if (this.$route.params.id) {
            this.getDataBpjs(this.$route.params.no)
            this.getData(this.$route.params.id)
        }
    },

    mounted() {
        window.addEventListener('keydown', this.handleKeyPress);
    },
    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },
};

</script>
<style>
@import './../../../../@core/css/main/main.min.css';

@media print {
    @page {
        size: 24px 14px;
        margin: 0 1cm;
    }
}

td {
    padding: 2px 0;
}

#canvas {
    width: 24cm !important;
    height: 14cm !important;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    padding: 8px 0;
}

</style>
