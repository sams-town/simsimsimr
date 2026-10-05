<template>
    <div>
        <topMenu selectedMenu="menu11" class="mt-2" />
        <div class="px-2 mt-3">
            <div class="col c-box px-0">
                <b-button @click="showFilter = !showFilter" class="col c-btn-between text-dark"
                    style="background-color: var(--c-purple-100);">
                    <span>Filter Data</span>
                    <b-icon icon="chevron-down"></b-icon>
                </b-button>
                <b-form action="index.html" v-show="showFilter" class="c-form pb-3">
                    <div class="mx-auto col col-lg-4">
                        <div class="col px-1 px-lg-3 mt-1">
                            <label>No RM</label>
                            <b-form-input type="number" class="c-input"></b-form-input>
                        </div>
                    </div>
                </b-form>
            </div>
            <div class="col c-box px-0 mt-3 pb-3">
                <div class="col c-btn-between rad-top-1 text-dark" style="background-color: var(--c-purple-100);">
                    Data Pasien
                </div>
                <div class="row mx-0 p-3">
                    <div class="col-12 col-lg-6">
                        <p class="fw-700 c-text-12-14 mb-1">Profil Pasien</p>
                        <table class="fw-400 c-text-12-14">
                            <tr>
                                <td class="py-1">Nama</td>
                                <td class="py-1">:</td>
                                <td class="text-uppercase py-1 px-0 px-lg-3">helma widyawati</td>
                            </tr>
                            <tr>
                                <td class="py-1">Tanggal Lahir</td>
                                <td class="py-1">:</td>
                                <td class="py-1 px-0 px-lg-3">1997-07-08</td>
                            </tr>
                            <tr>
                                <td class="py-1">Jenis Kelamin</td>
                                <td class="py-1">:</td>
                                <td class="text-capitalize py-1 px-0 px-lg-3">perempuan</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-12 col-lg-6">
                        <p class="fw-700 c-text-12-14 mb-1">List Pendaftaran Pasien</p>
                        <table class="fw-400 c-text-12-14">
                            <tr>
                                <td class="py-1">kode pendaftaran</td>
                                <td class="py-1">:</td>
                                <td class="text-uppercase py-1 px-0 px-lg-3">PL202111093400032</td>
                            </tr>
                            <tr>
                                <td class="py-1">Tanggal Pendaftaran</td>
                                <td class="py-1">:</td>
                                <td class="py-1 px-0 px-lg-3">1997-07-08</td>
                            </tr>
                            <tr>
                                <td class="py-1">Instalasi</td>
                                <td class="py-1">:</td>
                                <td class="text-capitalize py-1 px-0 px-lg-3">Instalasi Rawat Jalan</td>
                            </tr>
                            <tr>
                                <td class="py-1">Poli/Ruangan</td>
                                <td class="py-1">:</td>
                                <td class="text-capitalize py-1 px-0 px-lg-3">-</td>

                                <td class="py-1">Bed</td>
                                <td class="py-1">:</td>
                                <td class="text-capitalize py-1 px-0 px-lg-3">-</td>
                            </tr>
                            <tr>
                                <td class="py-1">Dokter</td>
                                <td class="py-1">:</td>
                                <td class="text-capitalize py-1 px-0 px-lg-3">dr. Cici Julia Sri Dewi, Sp.PK</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <!-- End: row -->
                <div class="col">
                    <b-button @click="showDetails = !showDetails" class="col c-btn-between text-dark"
                        style="background-color: var(--c-purple-100);">
                        <span>Rincian</span>
                        <b-icon icon="chevron-down"></b-icon>
                    </b-button>
                    <div v-if="showDetails" class="col pt-3">
                        <p class="fw-700 c-text-12-14 mb-2">List Pendaftaran Pasien</p>
                        <b-table striped hover responsive :items="details_items" :fields="details_fields"></b-table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import topMenu from '@/@core/components/top-menu/topMenu.vue'

export default {
    components: { topMenu },
    data() {
        return {
            showFilter: true,
            showDetails: false,
            details_fields: [
                { key: 'no', label: 'No' },
                { key: 'laboratorium', label: 'Laboratorium' },
                { key: 'tgl', label: 'Tanggal' },
            ],
            details_items: [
                { no: 1, laboratorium: 'SWAB AG', tgl: '2021-11-09', },
                // { no: 2, laboratorium: 'SWAB AG', tgl: '2021-11-09', },
                // { no: 3, laboratorium: 'SWAB AG', tgl: '2021-11-09', },
                // { no: 4, laboratorium: 'SWAB AG', tgl: '2021-11-09', },
            ]
        }
    }
}
</script>
<style>
@import '../../../../@core/css/utility.css';

.c-box {
    background: #F8F9FA;
    box-shadow: 0px 2px 2px rgba(128, 109, 92, 0.4);
    border-radius: 4px;
}

</style>