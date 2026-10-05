<template>
    <div>
        <topMenu selectedMenu="menu9" class="mt-2" />
        <filterForm class="px-2 mt-3" />
        <div class="mt-15 mt-lg-32">
            <div class="col">
                <p class="mb-0 text-bold-24">List Data</p>
            </div>
            <div class="row mx-0">
                <div class="col-lg">
                    <div class="row mx-0">
                        <span class="text-regular-14">Tampilkan</span>
                        <select id="per-page-select" class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                            <option value="5" selected>5</option>
                            <option value="10" selected>10</option>
                            <option value="15" selected>15</option>
                        </select>
                        <span class="text-regular-14">Entri</span>
                    </div>
                </div>
                <div class="col-lg d-flex">
                    <b-button pill size="sm" variant="outline-indigo-500" class="ml-auto">
                        <b-icon icon="filter"></b-icon>
                        <span>Filter Data</span>
                    </b-button>
                </div>
            </div>
        </div>
        <div class="mt-20 col">
            <b-table-simple bordered responsive small>
                <b-thead class="bg-neutral-100 text-bold-14">
                    <b-tr>
                        <b-th colspan="6" class="text-center py-3">Data Pendaftaran</b-th>
                        <b-th colspan="6" class="text-center py-3">Data Pasien</b-th>
                        <b-th colspan="4" class="text-center py-3">Data Tujuan</b-th>
                        <b-th rowspan="2" class="text-center py-3" style="min-width: 300px; vertical-align: middle;">Data Aksi</b-th>
                    </b-tr>
                    <b-tr>
                        <b-th class="text-center" style="min-width: 125px; max-width: 150px; vertical-align: middle;">Kode</b-th>
                        <b-th class="text-center" style="min-width: 125px; max-width: 150px; vertical-align: middle;">Tanggal</b-th>
                        <b-th class="text-center" style="min-width: 125px; max-width: 150px; vertical-align: middle;">Cara Bayar</b-th>
                        <b-th class="text-center" style="min-width: 125px; max-width: 150px; vertical-align: middle;">Cara Masuk</b-th>
                        <b-th class="text-center" style="min-width: 125px; max-width: 150px; vertical-align: middle;">Status Pasien</b-th>
                        <b-th class="text-center" style="min-width: 125px; max-width: 150px; vertical-align: middle;">Status Dokumen</b-th>
                        <b-th class="text-center" style="min-width: 125px; max-width: 150px; vertical-align: middle;">No RM</b-th>
                        <b-th class="text-center" style="min-width: 125px; max-width: 150px; vertical-align: middle;">Nama</b-th>
                        <b-th class="text-center" style="min-width: 125px; max-width: 150px; vertical-align: middle;">Jenis Pasien</b-th>
                        <b-th class="text-center" style="min-width: 125px; max-width: 150px; vertical-align: middle;">Tanggal Lahir</b-th>
                        <b-th class="text-center" style="min-width: 125px; max-width: 150px; vertical-align: middle;">Umur</b-th>
                        <b-th class="text-center" style="min-width: 125px; max-width: 150px; vertical-align: middle;">Kelamin</b-th>
                        <b-th class="text-center" style="min-width: 125px; max-width: 150px; vertical-align: middle;">Instalasi</b-th>
                        <b-th class="text-center" style="min-width: 125px; max-width: 150px; vertical-align: middle;">Poli/Ruangan</b-th>
                        <b-th class="text-center" style="min-width: 125px; max-width: 150px; vertical-align: middle;">Dokter</b-th>
                        <b-th class="text-center" style="min-width: 125px; max-width: 150px; vertical-align: middle;">No Urut</b-th>
                    </b-tr>
                </b-thead>
                <b-tbody class="text-medium-14">
                    <b-tr class="text-center">
                        <b-td><b-badge variant="success-200">20221026100002</b-badge></b-td>
                        <b-td style="white-space:nowrap">26-10-2022 <br> 17:14:27</b-td>
                        <b-td>Pribadi</b-td>
                        <b-td>Langsung</b-td>
                        <b-td><b-badge variant="info-200">Dalam Penanganan Medis</b-badge></b-td>
                        <b-td><b-badge variant="danger-200">Belum Kirim Dokumen Rekam Medis</b-badge></b-td>
                        <b-td><b-badge variant="info-700"><b-icon icon="file-text" class="mr-2" />00013595</b-badge></b-td>
                        <b-td><span class="text-uppercase">firly ahyar agni aradea </span> [tn]</b-td>
                        <b-td><b-badge variant="info-200">Pasien Baru</b-badge></b-td>
                        <b-td>2000-01-05</b-td>
                        <b-td>22 Tahun 10 Bulan 11 Hari</b-td>
                        <b-td>Perempuan</b-td>
                        <b-td>Instalasi Rawat Inap</b-td>
                        <b-td>OK</b-td>
                        <b-td>Dr. Magfirah</b-td>
                        <b-td>1</b-td>
                        <b-td>
                            <div class="row mx-0">
                                <b-button size="sm" class="m-1" variant="info-600"> <img src="@core/assets/icons/registration/detail.svg" alt="" width="15px"> Detail</b-button>
                                <b-button size="sm" class="m-1" variant="neutral-200"> <img src="@core/assets/icons/registration/label.svg" alt="" width="15px"> Label</b-button>
                                <b-button size="sm" class="m-1" variant="neutral-200"> <img src="@core/assets/icons/registration/struk.svg" alt="" width="15px"> Struk</b-button>
                            </div>
                        </b-td>
                    </b-tr>
                    <b-tr class="text-center">
                        <b-td><b-badge variant="success-200">20221026100001</b-badge></b-td>
                        <b-td style="white-space:nowrap">26-10-2022 <br> 17:14:27</b-td>
                        <b-td>BPJS</b-td>
                        <b-td>Rujukan</b-td>
                        <b-td><b-badge variant="success-200">Pulang</b-badge></b-td>
                        <b-td><b-badge variant="warning-200">Belum Terima Dokumen Perawat</b-badge></b-td>
                        <b-td><b-badge variant="info-700"><b-icon icon="file-text" class="mr-2" />00013595</b-badge></b-td>
                        <b-td><span class="text-uppercase">opik</span> [tn]</b-td>
                        <b-td><b-badge variant="success-200">Pasien Lama</b-badge></b-td>
                        <b-td>2000-01-05</b-td>
                        <b-td>42 Tahun 10 Bulan 12 Hari</b-td>
                        <b-td>Laki-laki</b-td>
                        <b-td>Instalasi Rawat Inap</b-td>
                        <b-td>ALAMANDA 311</b-td>
                        <b-td>Dr. Magfirah</b-td>
                        <b-td>2</b-td>
                        <b-td>
                            <div class="row mx-0">
                                <b-button size="sm" class="m-1" variant="info-600"> <img src="@core/assets/icons/registration/detail.svg" alt="" width="15px"> Detail</b-button>
                                <b-button size="sm" class="m-1" variant="neutral-200"> <img src="@core/assets/icons/registration/label.svg" alt="" width="15px"> Label</b-button>
                                <b-button size="sm" class="m-1" variant="neutral-200"> <img src="@core/assets/icons/registration/struk.svg" alt="" width="15px"> Struk</b-button>
                            </div>
                        </b-td>
                    </b-tr>
                </b-tbody>
            </b-table-simple>
        </div>
        <!-- <exList_table /> -->
    </div>
</template>
<script>
import topMenu from '@/@core/components/top-menu/topMenu.vue'
import filterForm from '@/@core/components/registration/filterForm.vue'
// import exList_table from '../externalList/exList_table.vue';

export default {
    components: { topMenu, filterForm, },
    
}
</script>
<style>
@import '../../../../@core/css/utility.css';

.title {
    font-size: 18px;
    line-height: 22px;
}
</style>