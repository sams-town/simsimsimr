<template>
    <div>
        <section>
            <b-table small bordered responsive :fields="administration.fields" :items="administration.items" tbody-tr-class="text-regular-14" thead-class="text-bold-14 bg-success-100">
                <template #thead-top="data">
                    <b-tr>
                        <b-th colspan="7" variant="neutral-100">RINCIAN ADMINISTRASI</b-th>
                    </b-tr>
                </template>
                <template #cell(detail_name)="data">
                    <div style="min-width: 400px;">
                        {{ data.value }}
                    </div>
                </template>
                <template #cell(price)="data">
                    <div style="min-width: 100px;">
                        {{ data.value }}
                    </div>
                </template>
                <template #cell(qty)="data">
                    <div style="min-width: 100px;">
                        {{ data.value }}
                    </div>
                </template>
                <template #cell(discount)="data">
                    <div style="min-width: 100px;">
                        {{ data.value }}
                    </div>
                </template>
                <template #cell(sub_total)="data">
                    <div style="min-width: 100px;">
                        {{ data.value }}
                    </div>
                </template>
                <template #cell(pay)>
                    <div>
                        <feather-icon icon="CheckSquareIcon" />
                    </div>
                </template>
            </b-table>
        </section>
        <section class="mt-32">
            <b-table small bordered responsive :fields="doctor.fields" :items="doctor.items" tbody-tr-class="text-regular-14" thead-class="text-bold-14 bg-success-100">
                <template #thead-top="data">
                    <b-tr>
                        <b-th colspan="7" variant="neutral-100">RINCIAN TINDAKAN DOKTER</b-th>
                    </b-tr>
                </template>
                <template #cell(detail_name)="data">
                    <div style="min-width: 400px;">
                        {{ data.value }}
                    </div>
                </template>
                <template #cell(price)="data">
                    <div style="min-width: 100px;">
                        {{ data.value }}
                    </div>
                </template>
                <template #cell(qty)="data">
                    <div style="min-width: 100px;">
                        {{ data.value }}
                    </div>
                </template>
                <template #cell(discount)="data">
                    <div style="min-width: 100px;">
                        {{ data.value }}
                    </div>
                </template>
                <template #cell(sub_total)="data">
                    <div style="min-width: 100px;">
                        {{ data.value }}
                    </div>
                </template>
                <template #cell(pay)>
                    <div>
                        <feather-icon icon="CheckSquareIcon" />
                    </div>
                </template>
            </b-table>
        </section>
        <section class="mt-32">
            <b-table small bordered responsive :fields="laboratory.fields" :items="laboratory.items" tbody-tr-class="text-regular-14" thead-class="text-bold-14 bg-success-100">
                <template #thead-top="data">
                    <b-tr>
                        <b-th colspan="7" variant="neutral-100">RINCIAN TINDAKAN LABORATORIUM</b-th>
                    </b-tr>
                </template>
                <template #cell(detail_name)="data">
                    <div style="min-width: 400px;">
                        {{ data.value }}
                    </div>
                </template>
                <template #cell(price)="data">
                    <div style="min-width: 100px;">
                        {{ data.value }}
                    </div>
                </template>
                <template #cell(qty)="data">
                    <div style="min-width: 100px;">
                        {{ data.value }}
                    </div>
                </template>
                <template #cell(discount)="data">
                    <div style="min-width: 100px;">
                        {{ data.value }}
                    </div>
                </template>
                <template #cell(sub_total)="data">
                    <div style="min-width: 100px;">
                        {{ data.value }}
                    </div>
                </template>
                <template #cell(pay)>
                    <div>
                        <feather-icon icon="CheckSquareIcon" />
                    </div>
                </template>
            </b-table>
        </section>
        <section class="mt-32">
            <b-table small bordered responsive :fields="radiology.fields" :items="radiology.items" tbody-tr-class="text-regular-14" thead-class="text-bold-14 bg-success-100">
                <template #thead-top="data">
                    <b-tr>
                        <b-th colspan="7" variant="neutral-100">RINCIAN TINDAKAN RADIOLOGI</b-th>
                    </b-tr>
                </template>
                <template #cell(detail_name)="data">
                    <div style="min-width: 400px;">
                        {{ data.value }}
                    </div>
                </template>
                <template #cell(price)="data">
                    <div style="min-width: 100px;">
                        {{ data.value }}
                    </div>
                </template>
                <template #cell(qty)="data">
                    <div style="min-width: 100px;">
                        {{ data.value }}
                    </div>
                </template>
                <template #cell(discount)="data">
                    <div style="min-width: 100px;">
                        {{ data.value }}
                    </div>
                </template>
                <template #cell(sub_total)="data">
                    <div style="min-width: 100px;">
                        {{ data.value }}
                    </div>
                </template>
                <template #cell(pay)>
                    <div>
                        <feather-icon icon="CheckSquareIcon" />
                    </div>
                </template>
            </b-table>
        </section>
        <section class="mt-32">
            <b-table small bordered responsive :fields="tool.fields" :items="tool.items" tbody-tr-class="text-regular-14" thead-class="text-bold-14 bg-success-100">
                <template #thead-top="data">
                    <b-tr>
                        <b-th colspan="7" variant="neutral-100">RINCIAN PEMAKAIAN ALAT</b-th>
                    </b-tr>
                </template>
                <template #cell(detail_name)="data">
                    <div style="min-width: 400px;">
                        {{ data.value }}
                    </div>
                </template>
                <template #cell(price)="data">
                    <div style="min-width: 100px;">
                        {{ data.value }}
                    </div>
                </template>
                <template #cell(qty)="data">
                    <div style="min-width: 100px;">
                        {{ data.value }}
                    </div>
                </template>
                <template #cell(discount)="data">
                    <div style="min-width: 100px;">
                        {{ data.value }}
                    </div>
                </template>
                <template #cell(sub_total)="data">
                    <div style="min-width: 100px;">
                        {{ data.value }}
                    </div>
                </template>
                <template #cell(pay)>
                    <div>
                        <feather-icon icon="CheckSquareIcon" />
                    </div>
                </template>
            </b-table>
        </section>
        <section class="mt-20">
            <b-table-simple bordered>
                <b-tbody class="text-bold-14">
                    <b-tr>
                        <b-td style="vertical-align: middle;">TOTAL PEMBAYARAN (Rp)</b-td>
                        <b-td class="text-right" style="width: 250px;">3.475.000,00</b-td>
                    </b-tr>
                </b-tbody>
            </b-table-simple>
        </section>
        <section class="mt-32 d-flex">
            <router-link :to="{ name: 'PatientPaymentListDetailView' }">
                <b-button variant="info-700" class="mr-2">
                    <feather-icon icon="PrinterIcon" /> Ke Rincian Pembayaran
                </b-button>
            </router-link>
            <router-link :to="{ name: 'PatientPaymentInvoiceView' }">
                <b-button variant="info-400" class="mr-2">
                    <feather-icon icon="PrinterIcon" /> Cetak Invoice
                </b-button>
            </router-link>
            <router-link :to="{ name: 'PatientPaymentKwitansiView' }">
                <b-button variant="info-400">
                    <feather-icon icon="PrinterIcon" /> Cetak Kwitansi
                </b-button>
            </router-link>
        </section>
    </div>
</template>
<script>
    export default{
        data() {
            return {
                administration: {
                    fields: [
                        { key: 'no', label: 'NO',},
                        { key: 'detail_name', label: 'NAMA RINCIAN' },
                        { key: 'price', label: 'HARGA (Rp)' },
                        { key: 'qty', label: 'QTY' },
                        { key: 'discount', label: 'DISC (%)' },
                        { key: 'sub_total', label: 'SUB TOTAL (Rp)' },
                        { key: 'pay', label: 'BAYAR' },
                    ],
                    items: [
                        {
                            no: 1,
                            detail_name: 'ADM PASIEN LAMA RAWAT JALAN',
                            price: '15.000',
                            qty: 1,
                            discount: 0,
                            sub_total: '15.000',
                        }

                    ]
                },
                doctor: {
                    fields: [
                        { key: 'no', label: 'NO',},
                        { key: 'detail_name', label: 'NAMA RINCIAN' },
                        { key: 'price', label: 'HARGA (Rp)' },
                        { key: 'qty', label: 'QTY' },
                        { key: 'discount', label: 'DISC (%)' },
                        { key: 'sub_total', label: 'SUB TOTAL (Rp)' },
                        { key: 'pay', label: 'BAYAR' },
                    ],
                    items: [
                        {
                            no: 1,
                            detail_name: 'Konsultasi Dokter Gigi',
                            price: '100.000',
                            qty: 1,
                            discount: 0,
                            sub_total: '100.000',
                        },
                        {
                            no: 2,
                            detail_name: 'Ekstraksi Gigi Dewasa Sulit',
                            price: '400.000',
                            qty: 1,
                            discount: 0,
                            sub_total: '400.000',
                        },

                    ]
                },
                laboratory: {
                    fields: [
                        { key: 'no', label: 'NO',},
                        { key: 'detail_name', label: 'NAMA RINCIAN' },
                        { key: 'price', label: 'HARGA (Rp)' },
                        { key: 'qty', label: 'QTY' },
                        { key: 'discount', label: 'DISC (%)' },
                        { key: 'sub_total', label: 'SUB TOTAL (Rp)' },
                        { key: 'pay', label: 'BAYAR' },
                    ],
                    items: [
                        {
                            no: 1,
                            detail_name: 'Hematologi Lengkap',
                            price: '104.500',
                            qty: 1,
                            discount: 0,
                            sub_total: '104.500',
                        },

                    ]
                },
                radiology: {
                    fields: [
                        { key: 'no', label: 'NO',},
                        { key: 'detail_name', label: 'NAMA RINCIAN' },
                        { key: 'price', label: 'HARGA (Rp)' },
                        { key: 'qty', label: 'QTY' },
                        { key: 'discount', label: 'DISC (%)' },
                        { key: 'sub_total', label: 'SUB TOTAL (Rp)' },
                        { key: 'pay', label: 'BAYAR' },
                    ],
                    items: [
                        {
                            no: 1,
                            detail_name: 'CT Scan Orbita Kontras',
                            price: '2.445.500',
                            qty: 1,
                            discount: 0,
                            sub_total: '2.445.500',
                        },

                    ]
                },
                tool: {
                    fields: [
                        { key: 'no', label: 'NO',},
                        { key: 'detail_name', label: 'NAMA RINCIAN' },
                        { key: 'price', label: 'HARGA (Rp)' },
                        { key: 'qty', label: 'QTY' },
                        { key: 'discount', label: 'DISC (%)' },
                        { key: 'sub_total', label: 'SUB TOTAL (Rp)' },
                        { key: 'pay', label: 'BAYAR' },
                    ],
                    items: [
                        {
                            no: 1,
                            detail_name: 'Udara Tekan per 12 jam',
                            price: '410.000',
                            qty: 1,
                            discount: 0,
                            sub_total: '410.000',
                        },

                    ]
                },
            }
        },
    }
</script>