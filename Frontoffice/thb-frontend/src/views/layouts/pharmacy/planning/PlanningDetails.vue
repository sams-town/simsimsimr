<template>
    <div>
        <div class="col">
            <section class="card">
                <div class="card-header bg-primary-200 font-weight-bold">DATA PERENCANAAN</div>
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <div class="col-lg-11">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-lg-3 text-lg-right">
                                            <label>No. Transaksi</label>
                                        </div>
                                        <div class="col-lg">
                                            <b-form-input value="PE00202301000002" disabled/>
                                        </div>
                                    </div>
                                    <div class="row mt-12">
                                        <div class="col-lg-3 text-lg-right">
                                            <label>No. Dokumen</label>
                                        </div>
                                        <div class="col-lg">
                                            <div class="row">
                                                <div class="col-lg-8">
                                                    <b-form-input value="/Med/01/2023/00002" disabled />
                                                </div>
                                                <div class="col-lg">  
                                                    <b-form-select />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-12">
                                        <div class="col-lg-3 text-lg-right">
                                            <label>Tanggal</label>
                                        </div>
                                        <div class="col-lg">
                                            <b-input-group>
                                                <b-form-input disabled v-model="datePick" type="text" placeholder="YYYY-MM-DD" autocomplete="off"></b-form-input>
                                                <b-input-group-append>
                                                    <b-form-datepicker v-model="datePick" disabled button-only right locale="en-US" aria-controls="example-input"></b-form-datepicker>
                                                </b-input-group-append>
                                            </b-input-group>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-lg-3 text-lg-right">
                                            <label>No. Transaksi</label>
                                        </div>
                                        <div class="col-lg">
                                            <b-input-group>
                                                <b-input-group-prepend is-text>
                                                    <input type="radio" aria-label="Radio for following text input">
                                                </b-input-group-prepend>
                                                <b-form-input value="LOGISTIK UMUM" disabled></b-form-input>
                                            </b-input-group>
                                            <b-input-group>
                                                <b-input-group-prepend is-text>
                                                    <input type="radio" aria-label="Radio for following text input">
                                                </b-input-group-prepend>
                                                <b-form-input value="INSTALASI FARMASI" disabled></b-form-input>
                                            </b-input-group>
                                        </div>
                                    </div>
                                    <div class="row mt-12">
                                        <div class="col-lg-3 text-lg-right">
                                            <label>Total</label>
                                        </div>
                                        <div class="col-lg">
                                            <b-input-group prepend="Nilai">
                                                <b-form-input disabled value="578.090,00" class="text-right" />
                                            </b-input-group>
                                            <b-input-group prepend="Pembulatan">
                                                <b-form-input disabled value="0,00" class="text-right" />
                                            </b-input-group>
                                            <b-input-group prepend="Akhir">
                                                <b-form-input disabled value="578.090,00" class="text-right" />
                                            </b-input-group>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="card mt-32">
                <div class="card-header bg-primary-200 font-weight-bold">Total : Rp. 578.090,00</div>
                <div class="card-body">
                    <b-table-simple responsive small bordered>
                        <b-thead class="text-center text-bold-14">
                            <b-tr>
                                <b-th rowspan="2">
                                    <b-form-checkbox />
                                </b-th>
                                <b-th rowspan="2">NO</b-th>
                                <b-th rowspan="2">KODE</b-th>
                                <b-th rowspan="2" style="min-width: 200px;">NAMA BARANG</b-th>
                                <b-th rowspan="2" style="min-width: 140px;">STOK AKHIR</b-th>
                                <b-th rowspan="2" style="min-width: 140px;">KEMASAN</b-th>
                                <b-th rowspan="2">SEDIAAN</b-th>
                                <b-th rowspan="2">QTY ORDER</b-th>
                                <b-th colspan="2">JUMLAH</b-th>
                                <b-th colspan="2">HARGA</b-th>
                                <b-th rowspan="2">DISKON %</b-th>
                                <b-th colspan="3">TOTAL</b-th>
                            </b-tr>
                            <b-tr>
                                <b-th style="min-width: 140px;">KEMASAN</b-th>
                                <b-th style="min-width: 140px;">SATUAN</b-th>
                                <b-th style="min-width: 140px;">KEMASAN</b-th>
                                <b-th style="min-width: 140px;">SATUAN</b-th>
                                <b-th style="min-width: 140px;">SEBELUM DISC</b-th>
                                <b-th style="min-width: 140px;">DISC</b-th>
                                <b-th style="min-width: 140px;">RP</b-th>
                            </b-tr>
                        </b-thead>
                        <b-tbody class="text-regular-14 text-center">
                            <b-tr>
                                <b-td><b-form-checkbox /></b-td>
                                <b-td>1</b-td>
                                <b-td>TAB0000000085</b-td>
                                <b-td>
                                    PARACETAMOL 500 MG TABLET
                                    <b-badge variant="warning-500" class="p-1">STOCK</b-badge>
                                </b-td>
                                <b-td>0.00 TABLET</b-td>
                                <b-td><b-form-select disabled /></b-td>
                                <b-td></b-td>
                                <b-td><b-form-input disabled value="5" /></b-td>
                                <b-td><b-form-input disabled value="100 Tablet" /></b-td>
                                <b-td><b-form-input disabled value="500 Tablet" /></b-td>
                                <b-td><b-form-input disabled value="40.000,00" /></b-td>
                                <b-td><b-form-input disabled value="400,00" /></b-td>
                                <b-td><b-form-input disabled value="0,00" /></b-td>
                                <b-td><b-form-input disabled value="200.000,00" /></b-td>
                                <b-td><b-form-input disabled value="0,00" /></b-td>
                                <b-td><b-form-input disabled value="200.000,00" /></b-td>
                            </b-tr>
                            <b-tr>
                                <b-td></b-td>
                                <b-td>2</b-td>
                                <b-td></b-td>
                                <b-td><b-form-select disabled /></b-td>
                                <b-td></b-td>
                                <b-td><b-form-select disabled /></b-td>
                                <b-td></b-td>
                                <b-td><b-form-input disabled value="1" /></b-td>
                                <b-td><b-form-input disabled value="0 BOX" /></b-td>
                                <b-td><b-form-input disabled value="0 BOX" /></b-td>
                                <b-td><b-form-input disabled value="0,00" /></b-td>
                                <b-td><b-form-input disabled value="0,00" /></b-td>
                                <b-td><b-form-input disabled value="0,00" /></b-td>
                                <b-td><b-form-input disabled value="0,00" /></b-td>
                                <b-td><b-form-input disabled value="0,00" /></b-td>
                                <b-td><b-form-input disabled value="0,00" /></b-td>
                            </b-tr>
                            <b-tr>
                                <b-td colspan="13" class="text-right font-weight-bold">TOTAL</b-td>
                                <b-td><b-form-input disabled value="200.000,00" /></b-td>
                                <b-td><b-form-input disabled value="0,00" /></b-td>
                                <b-td><b-form-input disabled value="200.000,00" /></b-td>
                            </b-tr>
                        </b-tbody>
                    </b-table-simple>
                </div>
            </section>
            <section class="card mt-32">
                <div class="card-header bg-primary-200">
                    <div class="d-flex justify-content-lg-between">
                        <span class="font-weight-bold">HISTORI VALIDASI PERENCANAAN</span>
                        <div class="d-flex">
                            <router-link :to="{ name: 'PharmacyPlanningProcurementView' }" target="_blank">
                                <b-button variant="primary-600" size="sm" class="mr-1">
                                    <feather-icon icon="PrinterIcon" />
                                </b-button>
                            </router-link>
                            <b-button variant="primary-600" size="sm" class="mr-1">
                                <feather-icon icon="SaveIcon" />
                            </b-button>
                            <b-button variant="primary-600" size="sm">
                                <feather-icon icon="RefreshCcwIcon" />
                            </b-button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <b-table small responsive bordered :fields="ValidationHistory.fields" :items="ValidationHistory.items" thead-class="text-bold-14" tbody-tr-class="text-regular-14">
                        <template #cell(ver)>
                            <div>
                                <b-form-checkbox />
                            </div>
                        </template>
                    </b-table>
                </div>
            </section>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            datePick: null,
            ValidationHistory:{
                fields: [
                    { key: 'no_ver', label: 'Ver No' },
                    { key: 'ver', label: 'Ver' },
                    { key: 'authorization', label: 'Otorisasi' },
                    { key: 'user', label: 'User' },
                    { key: 'date', label: 'Tanggal' },
                ],
                items: [
                    {
                        no_ver: '1',
                        authorization: 'Tim Perencanaan',
                        user: 'Vidyanti Permata D, S.Farm, Apt',
                        date: '29-01-2023 14:12:27',
                    },
                    {
                        no_ver: '2',
                        authorization: 'Tim Perencanaan',
                        user: 'Vidyanti Permata D, S.Farm, Apt',
                        date: '29-01-2023 14:12:27',
                    },
                    {
                        no_ver: '3',
                        authorization: 'Tim Perencanaan',
                        user: 'Vidyanti Permata D, S.Farm, Apt',
                        date: '29-01-2023 14:12:27',
                    },
                    {
                        no_ver: '4',
                        authorization: 'Tim Perencanaan',
                        user: 'Vidyanti Permata D, S.Farm, Apt',
                        date: '29-01-2023 14:12:27',
                    },
                ]
            }
        };
    },
};
</script>