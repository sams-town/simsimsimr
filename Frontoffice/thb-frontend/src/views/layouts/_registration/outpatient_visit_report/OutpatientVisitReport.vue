<template>
    <div>
        <topMenu selectedMenu="menu6" />
        <div class="mt-32">
            <section class="card">
                <div class="card-header font-weight-bold">Filter Data</div>
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <div class="col-lg-5">
                            <div>
                                <label class="c-form-label">Pilih Tahun</label>
                                <b-form-input type="date" />
                            </div>
                            <div class="d-flex flex-wrap mt-24">
                                <b-button variant="success-500" class="mr-2">
                                    <feather-icon icon="XIcon" /> Export
                                </b-button>
                                <b-button variant="outline-danger-500" class="mr-2">
                                    <feather-icon icon="XIcon" /> Clear
                                </b-button>
                                <b-button variant="primary-500">
                                    <feather-icon icon="CheckIcon" /> Submit
                                </b-button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="mt-24">
                <section>
                    <p class="text-semi-18">1. POLIKLINIK</p>
                    <b-table-simple bordered responsive class="mt-12">
                        <b-thead class="text-bold-14">
                            <b-tr>
                                <th rowspan="3">No</th>
                                <th rowspan="3">DOKTER</th>
                                <th colspan="24" class="text-center">BULAN</th>
                                <th rowspan="2" colspan="2">JUMLAH</th>
                            </b-tr>
                            <b-tr>
                                <th colspan="2">JAN</th>
                                <th colspan="2">FEB</th>
                                <th colspan="2">MAR</th>
                                <th colspan="2">APR</th>
                                <th colspan="2">MEI</th>
                                <th colspan="2">JUN</th>
                                <th colspan="2">JUL</th>
                                <th colspan="2">AGU</th>
                                <th colspan="2">SEP</th>
                                <th colspan="2">OKT</th>
                                <th colspan="2">NOV</th>
                                <th colspan="2">DES</th>
                            </b-tr>
                            <b-tr>
                                <b-th>B</b-th>
                                <b-th>L</b-th>
                                <b-th>B</b-th>
                                <b-th>L</b-th>
                                <b-th>B</b-th>
                                <b-th>L</b-th>
                                <b-th>B</b-th>
                                <b-th>L</b-th>
                                <b-th>B</b-th>
                                <b-th>L</b-th>
                                <b-th>B</b-th>
                                <b-th>L</b-th>
                                <b-th>B</b-th>
                                <b-th>L</b-th>
                                <b-th>B</b-th>
                                <b-th>L</b-th>
                                <b-th>B</b-th>
                                <b-th>L</b-th>
                                <b-th>B</b-th>
                                <b-th>L</b-th>
                                <b-th>B</b-th>
                                <b-th>L</b-th>
                                <b-th>B</b-th>
                                <b-th>L</b-th>
                                <b-th>B</b-th>
                                <b-th>L</b-th>
                            </b-tr>
                        </b-thead>
                        <b-tbody class="text-regular-12">
                            <b-tr class="font-weight-bold">
                                <b-td rowspan="2">1</b-td>
                                <b-td>Poli Mata</b-td>
                                <b-td>2</b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td>2</b-td>
                                <b-td></b-td>
                            </b-tr>
                            <b-tr>
                                <b-td>dr. Yoseph M.P.A Siahaan, Sp.M</b-td>
                                <b-td>2</b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td>2</b-td>
                                <b-td></b-td>
                            </b-tr>
                            <b-tr class="font-weight-bold">
                                <b-td rowspan="2">2</b-td>
                                <b-td>Poli Mata</b-td>
                                <b-td>2</b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td>3</b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td>5</b-td>
                                <b-td></b-td>
                            </b-tr>
                            <b-tr>
                                <b-td>dr. Sukara S. Kusumajaya, MARS.,Sp.P</b-td>
                                <b-td>2</b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td>3</b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td>5</b-td>
                                <b-td></b-td>
                            </b-tr>
                        </b-tbody>
                    </b-table-simple>
                </section>
                <section class="mt-24">
                    <p class="text-semi-18">1. POLIKLINIK</p>
                    <b-table-simple bordered responsive class="mt-12">
                        <b-thead class="text-bold-14">
                            <b-tr>
                                <th rowspan="3">No</th>
                                <th rowspan="3">DOKTER</th>
                                <th colspan="24" class="text-center">BULAN</th>
                                <th rowspan="2" colspan="2">JUMLAH</th>
                            </b-tr>
                            <b-tr>
                                <th colspan="2">JAN</th>
                                <th colspan="2">FEB</th>
                                <th colspan="2">MAR</th>
                                <th colspan="2">APR</th>
                                <th colspan="2">MEI</th>
                                <th colspan="2">JUN</th>
                                <th colspan="2">JUL</th>
                                <th colspan="2">AGU</th>
                                <th colspan="2">SEP</th>
                                <th colspan="2">OKT</th>
                                <th colspan="2">NOV</th>
                                <th colspan="2">DES</th>
                            </b-tr>
                            <b-tr>
                                <b-th>B</b-th>
                                <b-th>L</b-th>
                                <b-th>B</b-th>
                                <b-th>L</b-th>
                                <b-th>B</b-th>
                                <b-th>L</b-th>
                                <b-th>B</b-th>
                                <b-th>L</b-th>
                                <b-th>B</b-th>
                                <b-th>L</b-th>
                                <b-th>B</b-th>
                                <b-th>L</b-th>
                                <b-th>B</b-th>
                                <b-th>L</b-th>
                                <b-th>B</b-th>
                                <b-th>L</b-th>
                                <b-th>B</b-th>
                                <b-th>L</b-th>
                                <b-th>B</b-th>
                                <b-th>L</b-th>
                                <b-th>B</b-th>
                                <b-th>L</b-th>
                                <b-th>B</b-th>
                                <b-th>L</b-th>
                                <b-th>B</b-th>
                                <b-th>L</b-th>
                            </b-tr>
                        </b-thead>
                        <b-tbody class="text-regular-12">
                            <b-tr>
                                <b-td>1</b-td>
                                <b-td>dr. Yoseph M.P.A Siahaan, Sp.M</b-td>
                                <b-td>2</b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td>2</b-td>
                                <b-td></b-td>
                            </b-tr>
                            <b-tr>
                                <b-td>2</b-td>
                                <b-td>dr. Sukara S. Kusumajaya, MARS.,Sp.P</b-td>
                                <b-td>2</b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td>3</b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td></b-td>
                                <b-td>5</b-td>
                                <b-td></b-td>
                            </b-tr>
                        </b-tbody>
                    </b-table-simple>
                </section>
            </section>
        </div>
    </div>
</template>

<script>
import topMenu from '@/@core/components/top-menu/topMenu.vue'
export default {
    name: 'app',
    components: {
        topMenu
    },
}
</script>