<template>
    <div>
        <section>
            <!-- <feather-icon icon="FolderPlusIcon" /> -->
            <!-- <b-icon icon="folder-plus"></b-icon> -->
            <span class="text-bold-24">Coding Peserta : 202306080600002</span>
        </section>
        <section class="row mt-24">
            <div class="col-lg-3">
                <div class="label-head">
                    <span class="px-16 text-semi-18 text-neutral-700">Mapping Tarif Perusahaan</span>
                </div>
                <b-list-group class="mt-20">
                    <b-list-group-item>
                        <feather-icon icon="UserIcon" /> 00610992
                    </b-list-group-item>
                    <b-list-group-item>
                        <feather-icon icon="UserCheckIcon" /> BINTANG
                    </b-list-group-item>
                    <b-list-group-item>
                        <feather-icon icon="CalendarIcon" /> 1989-11-15
                    </b-list-group-item>
                    <b-list-group-item>
                        <feather-icon icon="SlidersIcon" /> 202306080600002
                    </b-list-group-item>
                    <b-list-group-item>
                        <feather-icon icon="HearthIcon" /> Perempuan
                    </b-list-group-item>
                </b-list-group>
            </div>
            <div class="col-lg">
                <b-table-simple responsive striped>
                    <b-tbody>
                        <b-tr>
                            <b-td>INA-CBG</b-td>
                            <b-td>:</b-td>
                            <b-td>Q-5-44-0</b-td>
                            <b-td>PENYAKIT KRONIS KECIL LAIN-LAIN</b-td>
                            <b-td>Rp 181400</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td>Sub Acute</b-td>
                            <b-td>:</b-td>
                            <b-td></b-td>
                            <b-td></b-td>
                            <b-td>Rp</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td>Chronic</b-td>
                            <b-td>:</b-td>
                            <b-td></b-td>
                            <b-td></b-td>
                            <b-td>Rp</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td>Special Procedure</b-td>
                            <b-td>:</b-td>
                            <b-td></b-td>
                            <b-td></b-td>
                            <b-td>Rp</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td>Special Prothesis</b-td>
                            <b-td>:</b-td>
                            <b-td></b-td>
                            <b-td></b-td>
                            <b-td>Rp</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td>Special Investigation</b-td>
                            <b-td>:</b-td>
                            <b-td></b-td>
                            <b-td></b-td>
                            <b-td>Rp</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td>Special Drug</b-td>
                            <b-td>:</b-td>
                            <b-td></b-td>
                            <b-td></b-td>
                            <b-td>Rp</b-td>
                        </b-tr>
                        <b-tr class="bg-warning-100">
                            <b-th>Total</b-th>
                            <b-td>:</b-td>
                            <b-td></b-td>
                            <b-td></b-td>
                            <b-td>Rp</b-td>
                        </b-tr>
                        <b-tr class="bg-white">
                            <b-td colspan="5">
                                <div class="d-flex justify-content-end">
                                    <b-button variant="danger-500" size="sm" class="mr-1">
                                        <feather-icon icon="EditIcon" /> Edit Ulang
                                    </b-button>
                                    <b-button variant="warning-500" size="sm" class="mr-1">
                                        <feather-icon icon="PrinterIcon" /> Cetak Klaim
                                    </b-button>
                                    <b-button variant="info-600" size="sm" class="mr-1">
                                        <feather-icon icon="SendIcon" /> Kirim Klaim
                                    </b-button>
                                    <b-button variant="neutral-100" size="sm">
                                        <feather-icon icon="ArrowLeftIcon" /> Back
                                    </b-button>
                                </div>
                            </b-td>
                        </b-tr>
                    </b-tbody>
                </b-table-simple>
                <small class="font-italic text-danger-500">Perubahan langsung update ke inacbg lokal</small>
            </div>
        </section>
    </div>
</template>

<script>

export default {
}

</script>

<style>
.label-head::before {
    content: '';
    border-left: 6px solid #648DE1;
}
</style>