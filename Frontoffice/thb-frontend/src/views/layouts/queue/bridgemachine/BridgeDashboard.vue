<template>
    <div>
        <section>
            <!-- <div class="row mx-0">
                <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0 mr-1">
                    <span class="text-regular-14">Tampilkan</span>
                    <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions"/>
                    <span class="text-regular-14">Entri</span>
                </b-form-group>
            </div> -->
            <!-- <b-table :fields="BridgeTable.fields" :items="BridgeTable.items" :per-page="0" :current-page="currentPage" bordered responsive class="mt-16" style="font-size: 14px;"> -->
            <b-table :items="bridgeItems" bordered responsive class="mt-16" style="font-size: 14px;">
                <!-- <template #cell(patient)="row">
                    <div>
                        <b>NIK :</b>
                        <div>{{ row.item.idcard_number }}</div>
                    </div>
                    <div>
                        <b>No. Peserta :</b>
                        <div>{{ row.item.code }}</div>
                    </div>
                    <div>
                        <b>No. RM :</b>
                        <div>{{ row.item.norm }}</div>
                    </div>
                    <div>
                        <b>Nama :</b>
                        <div>{{ row.item.name }}</div>
                    </div>
                </template>
                <template #cell(checkin)="data">
                    <div>
                        <b-icon :icon="data.value == 1 ? 'check-lg' : 'x-lg'" :variant="data.value == 1 ? 'success' : 'danger'"></b-icon>
                    </div>
                </template> -->
            </b-table>
            <!-- <div class="col px-0 mt-20">
                <div class="d-flex">
                    <span class="text-regular-14">Menampilkan {{ firstRows }} hingga <template
                            v-if="lastRows < totalRows">{{ lastRows }}</template> <template v-else>{{ totalRows
                            }}</template> dari {{ totalRows }} entri</span>
                    <div class="ml-auto">
                        <b-pagination v-model="currentPage" :total-rows="totalRows" :per-page="perPage"
                            align="fill" size="sm" class="my-0"></b-pagination>
                    </div>
                </div>
            </div> -->

        </section>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import moment from 'moment'
import vSelect from 'vue-select'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default {
    name: 'OK',
    components: {
        vSelect
    },
    data() {
        return {
            // BridgeTable: {
            //     fields: [
            //         { key: 'no', label: 'No' },
            //         { key: 'patient', label: 'Pasien', thStyle: 'width: 200px' },
            //         { key: 'health_facility_item_name', label: 'Poli' },
            //         { key: 'employee_name', label: 'Dokter / Bidan' },
            //         { key: 'insurance_name', label: 'Peserta' },
            //         { key: 'booking_code', label: 'Kode Booking' },
            //         { key: 'queue', label: 'Nomor' },
            //         { key: 'time_added', label: 'Jam Daftar (WIB)' },
            //         { key: 'admision_time', label: 'Jam Panggil (WIB)' },
            //         { key: 'status', label: 'Status' },
            //         { key: 'source', label: 'Sumber' },
            //         { key: 'checkin', label: 'Checkin' },
            //     ],
            //     items: [
            //         { no: '1', idcard_number: '1122334455', code: '0007474747474', norm: '176653', name: 'John Doe', health_facility_item_name: 'Poli Bedah', employee_name: 'Tenaga Medis 774947', insurance_name: 'JKN', booking_code: '100200300400', queue: '1', time_added: '11:12:13', admision_time: '12:14:16', status: 'Menunggu', source: 'Mobile JKN', checkin: '1' },
            //         { no: '2', idcard_number: '1122334455', code: '0007474747474', norm: '239877', name: 'Vinsmoke Sanji', health_facility_item_name: 'Poli Bedah', employee_name: 'Tenaga Medis 774947', insurance_name: 'JKN', booking_code: '100200300400', queue: '2', time_added: '11:47:48', admision_time: '12:30:31', status: 'Menunggu', source: 'Mobile JKN', checkin: '0' },
            //     ]
            // },
            perPage: 20,
            params: {
                offset: 0,
                limit: 20,
            },
            pageOptions: [20, 50, 100],
            firstRows: 1,
            lastRows: 20,
            currentPage: 1,
            show: false,
            isDisabled: false,

            bridgeItems: []
        }
    },
    // watch: {
    //     'items': function( val) {
    //         console.log(val)
    //     },
    //     currentPage: {
    //         handler: function (value) {
    //             console.log(value)
    //             this.firstRows = this.perPage * (value - 1) + 1
    //             this.lastRows = this.perPage * (value) + 1
    //             this.params.offset = this.perPage * (value - 1)
    //             this.params.limit = this.perPage
    //             // this.$store.dispatch('ok/getAll', this.params)
    //         }
    //     },
    //     perPage: {
    //         handler: function (value) {
    //             this.params.limit = this.perPage
    //             // this.$store.dispatch('ok/getAll', this.params)
    //         }
    //     }
    // },
    computed: {
        ...mapState({
            OptionsHealthFacility: state => state.healthfacility.options.healthfacility,
            OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,
            OptionsDoctor: state => state.employee.options.employee,


            // items: state => state.ok.items,
            // pageOptions: state => state.ok.pageOptions,
            // totalRows: state => state.ok.totalRows,
        }),
    },
    methods: {
        async getData() {
            const response = await this.$store.dispatch('patient/getBridge').then(data => {
                this.bridgeItems = data.response.list
                console.log('data: ', this.bridgeItems);

            })
        }
        // notification(a, b, c, d) {
        //     this.$toast({
        //         component: ToastificationContent,
        //         position: 'top-right',
        //         props: { title: a, icon: b, variant: c, text: d }
        //     })
        // },

    },
    mounted() {
        // this.$store.state.totalRows = this.items.length
        this.show = false
    },
    created() {
        this.getData()
        this.show = false
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>