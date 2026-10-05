<template>
    <div>
        <TopPharmacyMenu selectedMenu="menu9" />
        <div class="mt-32 col">
            <section class="card">
                <div class="card-header bg-primary-200 font-weight-bold">Review Harga Penerimaan/Harga Jual Farmasi</div>
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <div class="col-lg-10">
                            <div class="row">
                                <div class="col-lg-6 px-lg-4">
                                    <div>
                                        <label class="c-form-label">Gudang/Apotik/Satelit</label>
                                        <b-form-input disabled />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Nama Obat/Alat Medis</label>
                                        <b-form-input disabled />
                                    </div>
                                </div>
                                <div class="col-lg-6 px-lg-4">
                                    <div class="mt-12 mt-lg-0">
                                        <label class="c-form-label">Satuan Jual</label>
                                        <b-form-input disabled />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Tanggal Filter</label>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <b-form-datepicker disabled v-model="datePick.start"></b-form-datepicker>
                                            </div>
                                            <div class="col-lg-6">
                                                <b-form-datepicker disabled v-model="datePick.end"></b-form-datepicker>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="mt-24">
                <b-table responsive bordered :fields="SPupdateTable.fields" :items="SPupdateTable.items"
                    thead-class="text-bold-14" tbody-tr-class="text-regular-14"></b-table>
            </section>
        </div>
    </div>
</template>
<script>
import TopPharmacyMenu from '@/@core/components/top-menu/TopPharmacyMenu.vue';

export default {
    components: {
        TopPharmacyMenu
    },
    data() {
        return {
            datePick: {
                start: null,
                end: null
            },
            SPupdateTable: {
                fields: [
                    { key: 'no', label: 'No' },
                    { key: 'acceptance_date', label: 'TGL PENERIMAAN', },
                    { key: 'acceptance_number', label: 'NO PENERIMAAN', },
                    { key: 'hna', label: 'HNA', },
                    { key: 'disc', label: 'DISC', },
                    { key: 'hp', label: 'HP', },
                    { key: 'bpjs', label: 'HJA BPJS', },
                    { key: 'general', label: 'HJA UMUM', },
                    { key: 'activation', label: 'AKTIVASI HJA', },
                    { key: 'effective_date', label: 'TGL BERLAKU', },
                    { key: 'notes', label: 'Keterangan', thStyle: { minWidth: '200px' } },
                ],
                items: [
                    {
                        no: '1',
                        acceptance_date: '-',
                        acceptance_number: '-',
                        hna: '-',
                        disc: '-',
                        hp: '-',
                        bpjs: '-',
                        general: '-',
                        activation: '-',
                        effective_date: '-',
                        notes: '-',
                    }
                ]
            }
        }
    },
}
</script>