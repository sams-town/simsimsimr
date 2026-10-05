<template>
    <div>
        <topMenu selectedMenu="menu13" />
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
                    <!-- <b-table bordered responsive head-variant="light" :fields="PolyclinicTable.fields"
                        :items="PolyclinicTable.items" thead-class="text-bold-14" tbody-tr-class="text-regular-12"
                        class="mt-12 text-center">
                    </b-table> -->

                    <b-table-simple bordered responsive head-variant="light" thead-class="text-bold-14"
                        tbody-tr-class="text-regular-12" class="mt-12 text-center">
                        <b-thead>
                            <b-tr>
                                <b-th>No</b-th>
                                <b-th>Poli</b-th>
                                <b-th v-for="(monthLabel, indexLabel) in months" :key="indexLabel">
                                    <template>{{ monthLabel }}</template>
                                </b-th>
                                <b-th>Total</b-th>
                            </b-tr>
                        </b-thead>

                        <b-tbody>
                            <b-tr v-for="(serviceFirstIndex, indexTMonth) in serviceActivity[1]" :key="indexTMonth">
                                <b-td>{{ indexTMonth + 1 }}</b-td>
                                <b-td>
                                    <template>{{ serviceFirstIndex.health_facility_item_name }}</template>
                                </b-td>
                                <b-td v-for="(serviceTotal, indexTotal) in serviceActivity[0]" :key="indexTotal">
                                    {{ serviceTotal.total }}
                                </b-td>
                            </b-tr>
                        </b-tbody>
                    </b-table-simple>



                </section>
                <section class="mt-24">
                    <p class="text-semi-18">2. POLIKLINIK PERDOKTER</p>
                    <b-table-simple bordered responsive class="mt-12">
                        <!-- table sample -->
                        <!-- <b-thead class="text-bold-14">
                            <b-tr>
                                <th>No</th>
                                <th>DOKTER</th>
                                <th>JAN</th>
                                <th>FEB</th>
                                <th>MAR</th>
                                <th>APR</th>
                                <th>MEI</th>
                                <th>JUN</th>
                                <th>JUL</th>
                                <th>AGU</th>
                                <th>SEP</th>
                                <th>OKT</th>
                                <th>NOV</th>
                                <th>DES</th>
                                <th>TOTAL</th>
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
                                <b-td>2</b-td>
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
                                <b-td>2</b-td>
                            </b-tr>
                            <b-tr class="font-weight-bold">
                                <b-td rowspan="2">2</b-td>
                                <b-td>Poli Mata</b-td>
                                <b-td>2</b-td>
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
                                <b-td>5</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td>dr. Sukara S. Kusumajaya, MARS.,Sp.P</b-td>
                                <b-td>2</b-td>
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
                                <b-td>5</b-td>

                            </b-tr>
                        </b-tbody> -->
                        <!-- end sample table -->

                        <!-- table sample -->
                        <!-- <b-thead class="text-bold-14">
                            <b-tr>
                                <th>No</th>
                                <th>DOKTER</th>
                                <th>JAN</th>
                                <th>FEB</th>
                                <th>MAR</th>
                                <th>APR</th>
                                <th>MEI</th>
                                <th>JUN</th>
                                <th>JUL</th>
                                <th>AGU</th>
                                <th>SEP</th>
                                <th>OKT</th>
                                <th>NOV</th>
                                <th>DES</th>
                                <th>TOTAL</th>
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
                                <b-td>2</b-td>
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
                                <b-td>2</b-td>
                            </b-tr>
                            <b-tr class="font-weight-bold">
                                <b-td rowspan="2">2</b-td>
                                <b-td>Poli Mata</b-td>
                                <b-td>2</b-td>
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
                                <b-td>5</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td>dr. Sukara S. Kusumajaya, MARS.,Sp.P</b-td>
                                <b-td>2</b-td>
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
                                <b-td>5</b-td>

                            </b-tr>
                        </b-tbody>
                        end sample table -->


                        <b-thead>
                            <b-tr>
                                <b-th>No</b-th>
                                <b-th>Dokter</b-th>
                                <b-th v-for="(monthLabel, indexLabel) in months" :key="indexLabel">
                                    <template>{{ monthLabel }}</template>
                                </b-th>
                                <b-th>Total</b-th>
                            </b-tr>
                        </b-thead>
                        <template>
                            <b-tbody>
                                <template v-for="(getDoctorPoli, indexDoctorPoli) in serviceActivity[0]">
                                    <b-tr>
                                        <b-td>
                                            <template v-for="poliItem in getDoctorPoli['data']">
                                                <template v-for="doctorName in doctorNames">
                                                    <template v-if="poliItem.employee_name === doctorName">
                                                        {{ poliItem.employee_name }}
                                                    </template>
                                                </template>
                                            </template>
                                        </b-td>
                                        <b-td>
                                            <template v-for="poliItem in getDoctorPoli['data']">
                                                {{ poliItem.total }}
                                            </template>
                                        </b-td>
                                    </b-tr>
                                </template>
                            </b-tbody>
                        </template>
                    </b-table-simple>
                </section>
                <section class="mt-24">
                    <p class="text-semi-18">3. IGD</p>
                    <b-table bordered responsive head-variant="light" :fields="IGDTable.fields" :items="IGDTable.items"
                        thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                    </b-table>
                </section>
                <section class="mt-24">
                    <p class="text-semi-18">4. IGD PERDOKTER</p>
                    <b-table bordered responsive head-variant="light" :fields="IGDDoctorTable.fields"
                        :items="IGDDoctorTable.items" thead-class="text-bold-14" tbody-tr-class="text-regular-12"
                        class="mt-12 text-center">
                    </b-table>

                </section>
            </section>
        </div>
    </div>
</template>

<script>
import { mapState } from 'vuex'
import topMenu from '@/@core/components/top-menu/topMenu.vue'
export default {
    name: 'app',
    components: {
        topMenu
    },
    data() {
        return {
            months: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            monthComparrison: ['Januari', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            doctorNames: [],

            PolyclinicTable: {
                fields: [
                    { key: 'no', name: 'No' },
                    { key: 'poli', name: 'Poli' },
                    { key: 'jan', name: 'JAN' },
                    { key: 'feb', name: 'FEB' },
                    { key: 'mar', name: 'MAR' },
                    { key: 'apr', name: 'APR' },
                    { key: 'mei', name: 'MEI' },
                    { key: 'jun', name: 'JUN' },
                    { key: 'jul', name: 'JUL' },
                    { key: 'agu', name: 'AGU' },
                    { key: 'sep', name: 'SEP' },
                    { key: 'okt', name: 'OKT' },
                    { key: 'nov', name: 'NOV' },
                    { key: 'des', name: 'DES' },
                    { key: 'total', name: 'JUMLAH' },
                ],
                items: [
                    { no: 1, poli: 'Poli Mata', jan: '2', feb: '', mar: '', apr: '', mei: '', jun: '', jul: '', agu: '', sep: '', okt: '', nov: '', des: '', total: '2' },
                    { no: 2, poli: 'Poli Paru', jan: '2', feb: '', mar: '', apr: '', mei: '', jun: '3', jul: '', agu: '', sep: '', okt: '', nov: '', des: '', total: '5' },
                ]
            },
            IGDTable: {
                fields: [
                    { key: 'no', name: 'No' },
                    { key: 'igd', name: 'IGD' },
                    { key: 'jan', name: 'JAN' },
                    { key: 'feb', name: 'FEB' },
                    { key: 'mar', name: 'MAR' },
                    { key: 'apr', name: 'APR' },
                    { key: 'mei', name: 'MEI' },
                    { key: 'jun', name: 'JUN' },
                    { key: 'jul', name: 'JUL' },
                    { key: 'agu', name: 'AGU' },
                    { key: 'sep', name: 'SEP' },
                    { key: 'okt', name: 'OKT' },
                    { key: 'nov', name: 'NOV' },
                    { key: 'des', name: 'DES' },
                    { key: 'total', name: 'JUMLAH' },
                ],
                items: [
                    { no: 1, igd: 'Instalasi Gawat Darurat', jan: '2', feb: '', mar: '', apr: '', mei: '', jun: '', jul: '', agu: '', sep: '', okt: '', nov: '', des: '', total: '2' },
                ]
            },
            IGDDoctorTable: {
                fields: [
                    { key: 'no', name: 'No' },
                    { key: 'doctor_name', name: 'Nama Dokter    ' },
                    { key: 'jan', name: 'JAN' },
                    { key: 'feb', name: 'FEB' },
                    { key: 'mar', name: 'MAR' },
                    { key: 'apr', name: 'APR' },
                    { key: 'mei', name: 'MEI' },
                    { key: 'jun', name: 'JUN' },
                    { key: 'jul', name: 'JUL' },
                    { key: 'agu', name: 'AGU' },
                    { key: 'sep', name: 'SEP' },
                    { key: 'okt', name: 'OKT' },
                    { key: 'nov', name: 'NOV' },
                    { key: 'des', name: 'DES' },
                    { key: 'total', name: 'JUMLAH' },
                ],
                items: [
                    { no: 1, doctor_name: 'dr. Magfirah', jan: '2', feb: '', mar: '', apr: '', mei: '', jun: '', jul: '', agu: '', sep: '', okt: '', nov: '', des: '', total: '2' },
                    { no: 2, doctor_name: 'dr. Magfirah', jan: '2', feb: '', mar: '', apr: '', mei: '', jun: '', jul: '', agu: '', sep: '', okt: '', nov: '', des: '', total: '2' },
                ]
            },
            serviceActivity: []
        }
    },
    watch: {
        items(obj) {
            if (obj) {
                this.serviceActivity = obj
                // console.log('data: ', obj)
            }
        }
    },
    computed: {
        ...mapState({
            items: state => state.serviceactivity.items,
        }),
    },

    mounted() {
        this.$store.dispatch('serviceactivity/getByDate', this.params)
            .then((response) => {
                const serviceActivity = response.data;

                // for (let monthData of serviceActivity) {
                //     for (let item of monthData) {
                //         if (item.employee_name) {
                //             // Tambahkan employee_name ke dalam array baru jika ada
                //             this.doctorNames.push(item.employee_name);
                //         }
                //     }
                // }

                for (let getDoctorPoli of response.data) {
                    for (let dataItem of getDoctorPoli) {
                        if (dataItem.employee_name) {
                            // Menambahkan employee_name ke dalam array baru jika ada
                            this.doctorNames.push(dataItem.employee_name);
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Terjadi kesalahan:', error);
            });
    },

    methods: {
        //     calculateTotalByItemName(itemName) {
        //         let total = 0;
        //         this.serviceActivity.forEach(monthData => {
        //             monthData[1].forEach(item => {
        //                 if (item.health_facility_item_name === itemName) {
        //                     total += parseInt(item.total);
        //                 }
        //             });
        //         });
        //         return total;
        // },

    }
}
</script>