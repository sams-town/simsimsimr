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
                    <div class="row align-items-end pb-32" style="border-bottom: 2px solid black;" >
                        <div class="col-7">
                            <div class="text-bold-36">ORDER PEMBELIAN</div>
                            <div>TANGGAL : {{ data.date }}</div>
                        </div>
                        <div class="col-5 text-center">
                            <div class="text-regular-12">NOMOR PO</div>
                            <div class="text-semi-16">{{data.no_document}}</div>
                        </div>
                    </div>
                    <div class="mt-32">
                        <section>
                            <div class="row mt-28">
                                <div class="col-8">
                                    <table class="text-regular-14">
                                        <tr>
                                            <td class="first-cell">Nama Supplier</td>
                                            <td class="px-3">:</td>
                                            <td>{{ data.supplier_name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">No. Telp / No. Fax</td>
                                            <td class="px-3">:</td>
                                            <td>{{ data.phone }}</td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Alamat Supplier</td>
                                            <td class="px-3">:</td>
                                            <td>{{ data.address}}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-4">
                                    <table class="text-regular-14">
                                        <tr>
                                            <td class="first-cell">Term Of Payment</td>
                                            <td class="px-3">:</td>
                                            <td>{{ data.payment_method_name }}</td>
                                        </tr>
                                        <!-- <tr>
                                            <td class="first-cell">Email</td>
                                            <td class="px-3">:</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="first-cell">Tgl. Pengiriman</td>
                                            <td class="px-3">:</td>
                                            <td></td>
                                        </tr> -->
                                    </table>
                                </div>
                            </div>
                        </section>
                        <section class="mt-24">
                            <b-table-simple small class="text-center">
                                <b-thead class="text-bold-14">
                                    <b-tr class="bg-neutral-100">
                                        <b-th>NO</b-th>
                                        <b-th>KODE ITEM</b-th>
                                        <b-th>NAMA ITEM</b-th>
                                        <b-th>JUMLAH</b-th>
                                        <b-th>SATUAN</b-th>
                                        <b-th>@Harga (Rp)</b-th>
                                        <b-th>DISC (%)</b-th>
                                        <b-th>DISKON (Rp)</b-th>
                                        <b-th>SUB TOTAL (Rp)</b-th>
                                    </b-tr>
                                </b-thead>
                                <b-tbody class="text-regular-14">
                                    <template v-for="(item,index) in data.purchase_order_items">
                                        <b-tr>
                                            <b-td>{{index+1}}</b-td>
                                            <b-td> {{item.code_catalog}}</b-td>
                                            <b-td class="text-left">{{item.pharmacy_catalog_name}}</b-td>
                                            <b-td>{{item.value_package}}</b-td>
                                            <b-td>{{item.package_name}}</b-td>
                                            <b-td class="text-right">{{item.price_item | currency}}</b-td>
                                            <b-td>{{item.discount_item}}</b-td>
                                            <b-td>{{item.discount_price | currency}}</b-td>
                                            <b-td class="text-right">{{item.price_package | currency}}</b-td>
                                        </b-tr>
                                    </template>
                                    <b-tr class="text-right">
                                        <b-td colspan="8" class="font-weight-bold">TOTAL</b-td>
                                        <b-td>{{data.ntotal | currency}}</b-td>
                                    </b-tr>
                                    <b-tr class="text-right">
                                        <b-td colspan="8" class="font-weight-bold">DISKON FINAL</b-td>
                                        <b-td>{{data.ndiscounttotal+disc_item | currency}}</b-td>
                                    </b-tr>
                                    <b-tr class="text-right">
                                        <b-td colspan="8" class="font-weight-bold">PPn</b-td>
                                        <b-td>{{(data.ntotal-(data.ndiscounttotal+disc_item))*11/100 | currency}}</b-td>
                                    </b-tr>
                                    <b-tr class="text-right">
                                        <b-td colspan="8" class="font-weight-bold">GRAND TOTAL</b-td>
                                        <b-td>{{((data.ntotal-(data.ndiscounttotal+disc_item))*11/100)+(data.ntotal-(data.ndiscounttotal+disc_item)) | currency}}</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td colspan="9" class="text-left">Keterangan: </b-td>
                                    </b-tr>
                                </b-tbody>
                            </b-table-simple>
                        </section>
                        <section class="col mt-64">
                            <b-table-simple bordered>
                                <b-tbody>
                                    <b-tr>
                                        <b-td class="w-25">
                                            <div class="text-center">
                                                <p class="text-regular-14">Bekasi, {{ localDate(date_now) }}</p>
                                                <p class="text-regular-14"> Apotker Penanggung Jawab </p>
                                                <div class="font-weight-bold mt-64">
                                                    <div style="text-decoration: underline" >(Apt. Viren Ramadhan, M.Clin.Pharm)</div>
                                                    <div>63/ SIPAR/ DPMPTSP-BTM/01/III/2025</div>
                                                </div>
                                            </div>
                                        </b-td>
                                        <b-td class="w-25">
                                            <div class="text-center">
                                                <p class="text-regular-14">Mengetahui,</p>
                                                <p class="text-regular-14">Wakil Direktur Medik</p>
                                                <div class="font-weight-bold mt-64">
                                                    <div style="text-decoration: underline" >(drg. Pretty Kristianti D, MARS, FISQua)</div>
                                                </div>
                                            </div>
                                        </b-td>
                                        <b-td class="w-25">
                                            <div class="text-center">
                                                <p class="text-regular-14">Menyetujui,</p>
                                                <p class="text-regular-14">Direktur</p>
                                                <div class="font-weight-bold mt-64">
                                                    <div style="text-decoration: underline" >(dr. Kharisma Ersha Mufti, MARS, M.H)</div>
                                                </div>
                                            </div>
                                        </b-td>
                                    </b-tr>
                                </b-tbody>
                            </b-table-simple>


                        </section>
                        <div class="mt-32 ">
                            <ul style="list-style-type:square">
                                <li>Penukaran faktur setiap hari Senin dan Selasa, Pkl 09.00 - 12.00</li>
                                <li>Pengiriman barang setiap hari Senin s/d Jumat, Pkl 08.00 - 16.00, Sabtu 08.00 - 13.00 Kecuali barang cito</li>
                                <li>Syarat dan tempo pembayaran : 30 hari setelah tukar faktur</li>
                                <li>Catatan</li>
                            </ul>
                        </div>
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
			this.$store.dispatch('purchaseorder/getById', this.$route.params.id).then(data => {
                this.data = data[0]
                for (let i = 0; i < data[0].purchase_order_items.length; i++) {
                    this.disc_item += data[0].purchase_order_items[i].discount_price
                }
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
