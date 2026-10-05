<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <div class="row align-items-center w-100">
                        <div class="col-2 text-center">
                            <img src="@/@core/assets/images/logo-thb-small.png" width="60%" alt="rs-logo"> <br>
                        </div>
                        <div class="col text-center">
                            <p>RS Taman Harapan Baru</p>
                            <p>Jl. Kaliabang Tengah No.2, RT 004/023 Pejuang, Medan Satria, Bekasi, Jawa Barat</p>
                            <p>021-8898 1055, E-mail : rs_thb@yahoo.com</p>
                        </div>
                        <div class="col-2 text-center">
                            <img src="@/@core/assets/images/lafki-logo.png" width="60%" alt="lafki-logo"> <br>
                        </div>
                    </div>

                    <div class="canvas-body mt-32 px-40">
                        <h4 class="text-center kwitansi-title">KWITANSI</h4>

                        <section class="mt-28">
                            <div class="row mx-0">
                                <div class="col-12">
                                    <table class="w-100">
                                        <tr>
                                            <td class="first-cell align-top">No. Kwitansi</td>
                                            <td class="px-3 align-top">:</td>
                                            <td>{{ invoices.code }}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell align-top">Telah terima dari</td>
                                            <td class="px-3 align-top">:</td>
                                            <td>{{ regData.alias_name }} {{ regData.name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell align-top">Uang Sebanyak</td>
                                            <td class="px-3 align-top">:</td>
                                            <td class="text-uppercase-first">{{ terbilangRupiah(totalAmount) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell align-top">Untuk Pembayaran</td>
                                            <td class="px-3 align-top">:</td>
                                            <td>
                                                Pelayanan Kesehatan {{ serviceTypeText }} di RS Taman Harapan Baru
                                                a/n {{ regData.alias_name }} {{ regData.name }} ({{ regData.age }}Th),
                                                RM {{ regData.norm }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="row mx-0 mt-32">
                                <div class="col-6">
                                    <p class="font-weight-bold mb-0">
                                        Terbilang Rp. {{ totalAmount | currency }}
                                    </p>
                                </div>
                            </div>
                        </section>

                        <section class="row mt-64 mx-0">
                            <div class="col-6 text-center d-flex flex-column justify-content-end">
                                <p class="mb-1">(________________________)</p>
                                <p class="mb-0">Pasien/Keluarga Pasien</p>
                            </div>
                            <div class="col-6 text-center">
                                <p class="mb-1">Bekasi, {{ localDate(regData.register_date) }}</p>
                                <!-- Sesuaikan nama komponen/import qrcode di bawah ini
                                     dengan library QR code yang sudah dipakai di project -->
                                <qrcode-vue
                                    :value="qrValue"
                                    :size="80"
                                    level="H"
                                    class="mx-auto"
                                />
                                <p class="text-uppercase mt-2 mb-0">( {{ invoices.cashier_name }} )</p>
                                <p class="mb-0">Adm. Keuangan</p>
                            </div>
                        </section>
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
        QrcodeVue,
    },
    data() {
        return {
            id: '',
            date_now: moment(new Date()).locale('id').format('LL'),
            regData: {},
            invoices: {},
        }
    },
    computed: {
        // Total nilai yang dibayarkan, dijumlahkan dari seluruh baris payment.
        // Sesuaikan sumber field-nya kalau total sudah tersedia langsung di
        // this.invoices (mis. this.invoices.total) tanpa perlu dijumlah manual.
        totalAmount() {
            if (!this.invoices.payment || !Array.isArray(this.invoices.payment)) {
                return 0
            }
            return this.invoices.payment.reduce((sum, item) => sum + Number(item.var_3 || 0), 0)
        },
        // Menerjemahkan kode jenis pelayanan dari No. Kwitansi, contoh:
        // "436/RJ/Prb/08/2026" -> segmen kedua "RJ" -> "Rawat Jalan".
        // ASUMSI: format invoices.code selalu "NNN/KODE/..." - sesuaikan kalau
        // ternyata jenis pelayanan diambil dari field lain (mis. regData.service_type).
        serviceTypeText() {
            if (!this.invoices.code) {
                return ''
            }
            const parts = String(this.invoices.code).split('/')
            const typeCode = parts.length > 1 ? parts[1] : ''
            const map = {
                RJ: 'Rawat Jalan',
                RI: 'Rawat Inap',
            }
            return map[typeCode] || typeCode
        },
        // Value yang di-encode ke QR code. Ganti sesuai kebutuhan
        // (mis. URL verifikasi kwitansi, bukan cuma nomor kwitansi).
        qrValue() {
            return this.invoices.code || ''
        },
    },
    methods: {
        localDate(input_date) {
            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },
        printCanvas() {
            const canvas = this.$refs.canvas;
            const originalContents = document.body.innerHTML;
            document.body.innerHTML = canvas.innerHTML;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload()
        },
        closeWindow() {
            window.close()
        },
        getData(id) {
            this.$store.dispatch('payment/getByInvoiceId', id).then(data => {
                this.invoices = data[0]
                this.$store.dispatch('registration/getByRegistrationId', data[0].registration_id).then(data => {
                    this.regData = data[0]
                })
            })
        },
        // Konversi angka menjadi kata (terbilang) Bahasa Indonesia.
        // Contoh: terbilang(399780) -> "tiga ratus sembilan puluh sembilan ribu tujuh ratus delapan puluh"
        terbilang(angka) {
            angka = Math.floor(Math.abs(Number(angka)) || 0)

            const satuan = [
                '', 'satu', 'dua', 'tiga', 'empat', 'lima',
                'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas',
            ]

            let hasil = ''

            if (angka < 12) {
                hasil = satuan[angka]
            } else if (angka < 20) {
                hasil = this.terbilang(angka - 10) + ' belas'
            } else if (angka < 100) {
                const puluh = Math.floor(angka / 10)
                const sisa = angka % 10
                hasil = this.terbilang(puluh) + ' puluh' + (sisa > 0 ? ' ' + this.terbilang(sisa) : '')
            } else if (angka < 200) {
                const sisa = angka - 100
                hasil = 'seratus' + (sisa > 0 ? ' ' + this.terbilang(sisa) : '')
            } else if (angka < 1000) {
                const ratus = Math.floor(angka / 100)
                const sisa = angka % 100
                hasil = this.terbilang(ratus) + ' ratus' + (sisa > 0 ? ' ' + this.terbilang(sisa) : '')
            } else if (angka < 2000) {
                const sisa = angka - 1000
                hasil = 'seribu' + (sisa > 0 ? ' ' + this.terbilang(sisa) : '')
            } else if (angka < 1000000) {
                const ribu = Math.floor(angka / 1000)
                const sisa = angka % 1000
                hasil = this.terbilang(ribu) + ' ribu' + (sisa > 0 ? ' ' + this.terbilang(sisa) : '')
            } else if (angka < 1000000000) {
                const juta = Math.floor(angka / 1000000)
                const sisa = angka % 1000000
                hasil = this.terbilang(juta) + ' juta' + (sisa > 0 ? ' ' + this.terbilang(sisa) : '')
            } else if (angka < 1000000000000) {
                const miliar = Math.floor(angka / 1000000000)
                const sisa = angka % 1000000000
                hasil = this.terbilang(miliar) + ' miliar' + (sisa > 0 ? ' ' + this.terbilang(sisa) : '')
            } else {
                const triliun = Math.floor(angka / 1000000000000)
                const sisa = angka % 1000000000000
                hasil = this.terbilang(triliun) + ' triliun' + (sisa > 0 ? ' ' + this.terbilang(sisa) : '')
            }

            return hasil.trim().replace(/\s+/g, ' ')
        },
        // Wrapper terbilang khusus nominal rupiah, dipakai di baris "Uang Sebanyak".
        terbilangRupiah(angka) {
            if (angka === null || angka === undefined || isNaN(angka)) {
                return ''
            }
            if (Number(angka) === 0) {
                return 'nol rupiah'
            }
            return `${this.terbilang(angka)} rupiah`
        },
    },
    filters: {
        // Format angka ala Rupiah tanpa simbol mata uang, mis. 399780 -> "399.780"
        // dipakai bersama teks "Rp." manual di template (lihat "Terbilang Rp. ...").
        currency(value) {
            if (value === null || value === undefined || isNaN(value)) {
                return '0'
            }
            return new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            }).format(value)
        },
    },
    mounted() {
        if (this.$route.params.id) {
            this.id = this.$route.params.id
            this.getData(this.$route.params.id)
        }
    }
};
</script>
<style>
@import '@/@core/css/main/main.min.css';
@import url('https://fonts.cdnfonts.com/css/lucida-console');

@media print {
    @page {
        size: A4;
    }
}
body {
    letter-spacing: 1px;
    color: #000000;
    font-weight: 800;
    line-height: 1.5;
    font-family: 'Lucida Console', sans-serif;
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

.w-40 {
    width: 40%;
}

.kwitansi-title {
    text-decoration: underline;
    letter-spacing: 4px;
}

.first-cell {
    width: 160px;
    white-space: nowrap;
}

.align-top {
    vertical-align: top;
}
</style>