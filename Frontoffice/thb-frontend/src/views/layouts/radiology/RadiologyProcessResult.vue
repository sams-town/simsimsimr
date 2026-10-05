<template>
    <div>
        <div class="col label-head">
            <span class="px-16 text-medium-24 text-neutral-500">Input Hasil Pemeriksaan Radiologi</span>
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
                        <b-td style="width: 10%;">Tgl. Hasil</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">-</b-td>
                        <b-td style="width: 10%;">Kelamin</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">PEREMPUAN</b-td>
                    </b-tr>
                    <b-tr>
                        <b-td style="width: 10%;">Alamat</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">JL CEMARA 2</b-td>
                        <b-td style="width: 10%;">Dokter Pengirim</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">simrs, Adm</b-td>
                    </b-tr>
                    <b-tr>
                        <b-td style="width: 10%;">Nomor Tindakan</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;"><b-form-input></b-form-input></b-td>
                    </b-tr>
                </b-tbody>
            </b-table-simple>
        </section>
        <section class="col mt-40">
            <p class="text-bold-14 text-center">HASIL PEMERIKSAAN RADIOLOGI Vert. Thoracal AP/Lat/Obl</p>
            <div class="mt-16">
                <b-table fixed responsive bordered :fields="ResultTable.fields" :items="ResultTable.items" thead-class="text-bold-14 text-center" tbody-tr-class="text-regular-14">
                    <template #cell(result)>
                        <div>
                            <b-textarea rows=4></b-textarea>
                        </div>
                    </template>
                    <template #cell(impression)>
                        <div>
                            <b-textarea rows=4></b-textarea>
                        </div>
                    </template>
                </b-table>
            </div>
            <div class="row mt-24 mx-0">
                <b-button size="sm" variant="primary-500" class="mr-2">
                    <feather-icon icon="SaveIcon" /> Simpan
                </b-button>
                <router-link :to="{ name: 'RadiologyPrintResultView' }">
                    <b-button size="sm" variant="outline-secondary" class="mr-2">
                        <feather-icon icon="PrinterIcon" /> Cetak
                    </b-button>
                </router-link>
            </div>
        </section>
    </div>
</template>
<script>
export default{
    data() {
        return {
            ResultTable:{
                fields: [
                    { key: 'inspection', label: 'PEMERIKSAAN' },
                    { key: 'result', label: 'HASIL' },
                    { key: 'impression', label: 'KESAN' },
                ],
                items: [
                    { inspection: 'Vert. Thoracal AP/Lat/Obl', },
                ]
            }
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