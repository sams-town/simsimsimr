<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <div class="text-center mb-32">
                        <p class="text-bold-18" style="text-decoration: underline;">SURAT PESANAN OBAT JADI PSIKOTROPIKA</p>
                        <p class="text-semi-16 mt-8">Nomer Surat Pesanan : {{ data.no_document }}</p>
                    </div>

                    <div class="mt-32">
                        <p class="text-regular-14">Yang bertandatangan di bawah ini :</p>
                        <table class="text-regular-14 mt-8">
                            <tr>
                                <td class="first-cell">Nama</td>
                                <td class="px-3">:</td>
                                <td>{{ userData.employee_name }}</td>
                            </tr>
                            <tr>
                                <td class="first-cell">Alamat</td>
                                <td class="px-3">:</td>
                                <td>{{ data.apoteker_address }}</td>
                            </tr>
                            <tr>
                                <td class="first-cell">Jabatan</td>
                                <td class="px-3">:</td>
                                <td>Apoteker Penanggung Jawab</td>
                            </tr>
                            <tr>
                                <td class="first-cell">Nomor SIPA</td>
                                <td class="px-3">:</td>
                                <td>{{ userData.sip }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="mt-32">
                        <p class="text-regular-14">Mengajukan pesanan obat jadi Psikotropika kepada :</p>
                        <table class="text-regular-14 mt-8">
                            <tr>
                                <td class="first-cell">Nama PBF</td>
                                <td class="px-3">:</td>
                                <td>{{ data.supplier_name }}</td>
                            </tr>
                            <tr>
                                <td class="first-cell">Alamat</td>
                                <td class="px-3">:</td>
                                <td>{{ data.address }}</td>
                            </tr>
                            <tr>
                                <td class="first-cell">Telp.</td>
                                <td class="px-3">:</td>
                                <td>{{ data.phone }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="mt-32">
                        <p class="text-regular-14 mb-8">Jenis obat-obat jadi Psikotropika yang dipesan adalah :</p>
                        <b-table-simple bordered small class="text-center">
                            <b-thead class="text-bold-14">
                                <b-tr class="bg-neutral-100">
                                    <b-th>No</b-th>
                                    <b-th>Nama obat</b-th>
                                    <b-th>Zat aktif Obat</b-th>
                                    <b-th>Bentuk dan<br>kekuatan sediaan</b-th>
                                    <b-th>Satuan</b-th>
                                    <b-th>Jumlah</b-th>
                                </b-tr>
                            </b-thead>
                            <b-tbody class="text-regular-14">
                                <template v-for="(item, index) in data.purchase_order_items">
                                    <b-tr :key="index">
                                        <b-td>{{ index + 1 }}</b-td>
                                        <b-td class="text-left">{{ item.pharmacy_catalog_name }}</b-td>
                                        <b-td>{{ item.composition_special }}</b-td>
                                        <b-td>{{ item.dose }} {{ item.dose_unit_name }}</b-td>
                                        <b-td>{{ item.package_name }}</b-td>
                                        <b-td>{{ item.value_package }}</b-td>
                                    </b-tr>
                                </template>
                            </b-tbody>
                        </b-table-simple>
                    </div>

                    <div class="mt-32">
                        <p class="text-regular-14">Obat Jadi Psikotropika tersebut akan digunakan untuk memenuhi kebutuhan :</p>
                        <table class="text-regular-14 mt-8">
                            <tr>
                                <td class="first-cell">Nama Rumah Sakit</td>
                                <td class="px-3">:</td>
                                <td>{{ userData.institute_branch.name }}</td>
                            </tr>
                            <tr>
                                <td class="first-cell">Alamat lengkap</td>
                                <td class="px-3">:</td>
                                <td>{{ userData.institute_branch.address }}</td>
                            </tr>
                            <tr>
                                <td class="first-cell">No. Izin</td>
                                <td class="px-3">:</td>
                                <td>{{ userData.institute_branch.code }}</td>
                            </tr>
                            <tr>
                                <td class="first-cell">No. Telpon</td>
                                <td class="px-3">:</td>
                                <td>{{ userData.institute_branch.phone }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="mt-64" style="text-align: right;">
                        <table style="margin-left: auto; text-align: center;">
                            <tr>
                                <td class="text-regular-14">Bekasi, {{ localDate(data.date) }}</td>
                            </tr>
                            <tr>
                                <td class="text-regular-14">Apoteker Penanggung Jawab</td>
                            </tr>
                            <tr>
                                <td style="height: 80px;"></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold" style="text-decoration: underline;">({{ userData.employee_name }})</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">{{ userData.sip }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </b-container>
    </div>
</template>
<script>
import moment from 'moment';
export default {
    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),
            date_now: moment(new Date()).locale('id').format('LL'),
            data: {}
        }
    },
    mounted() {
        if (this.$route.params.id) {
            this.id = this.$route.params.id
            this.getData()
        }
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
            this.$store.dispatch('purchaseorder/getById', this.$route.params.id).then(data => {
                this.data = data[0]
                console.log('Data: ', this.data);
            })
        }
    }
};
</script>
<style>
@import '../../../../../@core/css/main/main.min.css';

@media print {
    @page {
        size: A4;
        margin: 16mm;
    }
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

.first-cell {
    width: 130px;
    vertical-align: top;
}
</style>