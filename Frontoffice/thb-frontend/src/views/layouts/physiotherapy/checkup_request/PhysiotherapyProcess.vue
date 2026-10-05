<template>
    <div>
        <div class="col label-head">
            <span class="px-16 text-medium-24 text-neutral-500">Data Permintaan Fisioterapi Pasien</span>
        </div>
        <section class="col mt-32">
            <b-table-simple responsive class="text-medium-14">
                <b-tbody>
                    <b-tr>
                        <b-td style="width: 10%;">No. Reg / RM</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">202303240300004 / 00013612</b-td>
                        <b-td style="width: 10%;">Unit</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">LABORATORIUM</b-td>
                    </b-tr>
                    <b-tr>
                        <b-td style="width: 10%;">Nama Pasien</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">NURUL AKMAL</b-td>
                        <b-td style="width: 10%;">Tanggal Lahir</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">04-03-1999</b-td>
                    </b-tr>
                    <b-tr>
                        <b-td style="width: 10%;">Tgl. Permintaan</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">24-03-2023 11:04:05</b-td>
                        <b-td style="width: 10%;">Umur</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">24 Tahun 2 Bulan 8 Hari</b-td>
                    </b-tr>
                    <b-tr>
                        <b-td style="width: 10%;">Kelamin</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">PEREMPUAN</b-td>
                        <b-td style="width: 10%;">Dokter Pengirim</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">simrs, Adm</b-td>
                    </b-tr>
                </b-tbody>
            </b-table-simple>
        </section>
        <section class="col mt-40">
            <b-table bordered responsive head-variant="light" hover small :fields="laboratory_action.field"
                :items="laboratory_action.item" tbody-tr-class="text-medium-14" class="mt-12 text-center">
                <template #cell(bill_in)>
                    <div>
                        <b-form-checkbox />
                    </div>
                </template>
            </b-table>
        </section>
        <section class="col mt-24">
            <div class="row mx-0">
                <b-button size="sm" variant="primary-500" class="mr-2">
                    <feather-icon icon="SaveIcon" /> Simpan
                </b-button>
                <router-link to="#">
                    <b-button size="sm" variant="outline-secondary" class="mr-2">
                        <feather-icon icon="FileTextIcon" /> Tambah Tindakan
                    </b-button>
                </router-link>
                <router-link to="#">
                    <b-button size="sm" variant="outline-secondary">
                        <feather-icon icon="PrinterIcon" /> Cetak Order
                    </b-button>
                </router-link>
            </div>
        </section>
    </div>
</template>

<script>
export default {
    data() {
        return {
            laboratory_action: {
                field: [
                    { key: 'no', label: 'No' },
                    { key: 'action_physio', label: 'Tindakan Fisioterapi' },
                    { key: 'action_price', label: 'Harga Tindakan' },
                    { key: 'action', label: 'Tindakan' },
                    { key: 'bill_status', label: 'Status Billing' },
                    { key: 'bill_in', label: 'Masukkan ke Tagihan' },
                ],
                item: [
                    {
                        no: '1',
                        action_physio: 'Package B ( 3 Modalities )',
                        action_price: 'IDR 200.000,00',
                        action: 'Order Baru',
                        bill_status: 'Order Baru',
                    },
                ],
            },
        }
    },
}
</script>
<style scoped>
.label-head::before {
    content: '';
    border-left: 6px solid #648DE1;
}
</style>