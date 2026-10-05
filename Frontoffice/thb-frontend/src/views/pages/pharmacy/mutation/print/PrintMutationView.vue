<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <div class="d-flex align-items-center">
                        <img src="../../../../../@core/assets/images/logo-thb-small.png" width="80px" alt="rs-logo"> <br>
                        <div class="text-regular-12 pl-16">
                            <p class="text-bold-18">RS Taman Harapan Baru</p>
                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                            <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                        </div>
                    </div>
                    <div class="row align-items-end mt-32 pb-32" style="border-bottom: 2px solid black;" >
                        <div class="col-7">
                            <div class="text-bold-36">MUTASI BARANG</div>
                            <div>TANGGAL : {{ data.date }}</div>
                        </div>
                        <div class="col-5 text-center">
                            <div class="text-regular-12">NOMOR MUTASI</div>
                            <div class="text-semi-16">{{ data.no_document }}</div>
                        </div>
                    </div>
                    <div class="mt-32">
                        <section>
                            <div class="row mt-28">
                                <div class="col-6">
                                    <table class="text-regular-14">
                                        <tr>
                                            <td class="first-cell">JENIS MUTASI</td>
                                            <td class="px-3">:</td>
                                            <td>{{ data.type_doc_name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">GUDANG/DEPO ASAL</td>
                                            <td class="px-3">:</td>
                                            <td>{{ data.from_depo_name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">GUDANG/DEPO PENERIMA</td>
                                            <td class="px-3">:</td>
                                            <td>{{ data.dst_depo_name }}</td>
                                        </tr>
                                        <!-- <tr>
                                            <td class="first-cell">Tanggal Jatuh Tempo</td>
                                            <td class="px-3">:</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">PO Term of Payment</td>
                                            <td class="px-3">:</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Keterangan</td>
                                            <td class="px-3">:</td>
                                            <td></td>
                                        </tr> -->
                                    </table>
                                </div>
                                <!-- <div class="col-6">
                                    <table class="text-regular-14">
                                        <tr>
                                            <td class="first-cell">Asal Barang</td>
                                            <td class="px-3">:</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">No. PO</td>
                                            <td class="px-3">:</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Nama Supplier</td>
                                            <td class="px-3">:</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Penerima</td>
                                            <td class="px-3">:</td>
                                            <td></td>
                                        </tr>
                                    </table>
                                </div> -->
                            </div>
                        </section>
                        <section class="mt-24">
                            <b-table-simple small bordered class="text-center">
                                <b-thead class="text-bold-14">
                                    <b-tr class="bg-neutral-100">
                                        <b-th>NO</b-th>
                                        <b-th>NAMA ITEM</b-th>
                                        <b-th>JUMLAH PERMINTAAN/RETUR</b-th>
                                        <!-- <b-th>SATUAN</b-th> -->
                                        <b-th>JUMLAH DITERIMA</b-th>
                                        <!-- <b-th>SATUAN</b-th> -->
                                        <b-th>EXPIRED DATE</b-th>
                                        <b-th>NO BATCH</b-th>
                                    </b-tr>
                                </b-thead>
                                <b-tbody class="text-regular-14">
                                    <template v-for="(item,index) in data.mutation_items">
                                        <b-tr>
                                            <b-td>{{index+1}}</b-td>
                                            <b-td class="text-left">{{item.catalog_name}}</b-td>
                                            <b-td>{{item.value_send}}</b-td>
                                            <!-- <b-td>{{item.package_name}}</b-td> -->
                                            <b-td>{{item.value_recive}}</b-td>
                                            <!-- <b-td>{{item.package_name}}</b-td> -->
                                            <b-td>{{item.expired_date}}</b-td>
                                            <b-td>{{item.batch}}</b-td>
                                            <!-- <b-td class="text-right">{{item.price_item | currency}}</b-td>
                                            <b-td>{{item.discount_item}}</b-td>
                                            <b-td>{{item.discount_price | currency}}</b-td>
                                            <b-td class="text-right">{{item.price_package | currency}}</b-td>
                                            <b-td>{{item.discount_item}}</b-td> -->
                                        </b-tr>
                                    </template>
                                    <!-- <b-tr class="text-right">
                                        <b-td colspan="7" class="font-weight-bold">TOTAL</b-td>
                                        <b-td>{{data.ntotal | currency}}</b-td>
                                    </b-tr>
                                    <b-tr class="text-right">
                                        <b-td colspan="7" class="font-weight-bold">DISKON FINAL</b-td>
                                        <b-td>{{data.ndiscounttotal+disc_item | currency}}</b-td>
                                    </b-tr>
                                    <b-tr class="text-right">
                                        <b-td colspan="7" class="font-weight-bold">PPn</b-td>
                                        <b-td>0</b-td>
                                    </b-tr>
                                    <b-tr class="text-right">
                                        <b-td colspan="7" class="font-weight-bold">GRAND TOTAL</b-td>
                                        <b-td>{{data.nfinish | currency}}</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td colspan="8" class="text-left">Keterangan: </b-td>
                                    </b-tr> -->
                                </b-tbody>
                            </b-table-simple>
                        </section>
                        <section class="col mt-64">
                            <table class="w-100">
                                <tbody>
                                    <tr>
                                        <td style="width: 33%;">
                                            <div class="text-center">
                                                <p class="text-regular-14 invisible"></p>
                                                <div class="font-weight-bold mt-64">
                                                    <div style="text-decoration: underline" ></div>
                                                    <div></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="width: 33%;">
                                            <div class="text-center">
                                                <p class="text-regular-14">PENGIRIM</p>
                                                <div class="font-weight-bold mt-64">
                                                    <div style="text-decoration: underline" ></div>
                                                    <div>{{data.user_validation_2}}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="width: 33%;">
                                            <div class="text-center">
                                                <!-- <p class="text-regular-14">Bekasi, {{ localDate(date_now) }}</p> -->
                                                <p class="text-regular-14">PENERIMA</p>
                                                <div class="font-weight-bold mt-64">
                                                    <div style="text-decoration: underline" ></div>
                                                    <div>{{data.user_validation_3}}</div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </section>
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
            date_now:moment(new Date()).locale('id').format('LL'),
            disc_item:0,
            data:{}
        }
    },
    mounted() {
        if (this.$route.params.id) {
            this.id = this.$route.params.id
            this.getData()
        }
    },
    filters:{
        currency(value) {
            return new Intl.NumberFormat("es-ES").format(value)
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
        closeWindow(){
            window.close()
        },
        getData (id) {
			this.$store.dispatch('mutation/getById', this.$route.params.id).then(data => {
                this.data = data[0]
                console.log('Data: ', this.data);
                for (let i = 0; i < data[0].mutation_items.length; i++) {
                    this.disc_item += data[0].mutation_items[i].discount_price
                }
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


/* #canvas {
    width: 30cm !important;
    width: 21.2cm !important;
    height: 29.7cm !important;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
} */

.w-40 {
    width: 40%;
}

</style>
