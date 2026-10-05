<template>
    <div>
        <TopPharmacyMenu selectedMenu="menu6" />
        <!-- <PharmacyReportMenu selectedMenu="menu1" class="mt-32" /> -->
        <b-overlay
            :show="show"
            spinner-variant="primary"
            spinner-type="grow"
            spinner-small
            rounded="sm"
            >
            <section class="card">
                <div class="card-header bg-primary-200 font-weight-bold">HISTORI PEMAKAIAN OBAT/ALKES PASIEN</div>
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <div class="col-lg-11">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="row mt-12">
                                        <div class="col-lg-3 text-lg-right">
                                            <label>NORM.</label>
                                        </div>
                                        <div class="col-lg">
                                            <b-form-input v-model="search.norm" @keyup.enter="onSearch()"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <b-table :fields="table" :items="prescription_list" :tbody-tr-class="rowPrescription" bordered responsive class="mt-12" style="font-size: 14px;">
                            <template #cell(no)="row">
                                <div>{{ row.index+1 }}</div>
                            </template>
                            <template #cell(no_order)="row">
                                <div>Nomor Resep : <br><b>{{ row.item.no_order }}</b></div>
                                <div>Tanggal Resep : <br><b>{{ row.item.date_added }}</b></div>
                            </template>
                            <template #cell(form_prescription)="row">
                                <div v-for="(category, index) in prescriptionCategories" :key="index" class="mt-8" v-if="hasItemsInCategory(row.value, category.id)">
                                    <b-table-simple responsive small style="font-size: 13px;">
                                        <b-thead>
                                            <b-tr>
                                                <b-th colspan="5" class="text-uppercase bg-success-100"> {{ category.label }}</b-th>
                                            </b-tr>
                                            <b-tr>
                                                <b-th>Nama</b-th>
                                                <b-th style="width: 100px;">Dosis Dokter</b-th>
                                                <b-th style="width: 100px;">Jumlah</b-th>
                                                <b-th style="width: 100px;">Aturan Pakai</b-th>
                                                <b-th style="width: 200px;">Keterangan</b-th>
                                            </b-tr>
                                        </b-thead>
                                        <b-tbody>
                                            <template v-for="(item, itemIndex) in row.value">
                                                <b-tr :key="itemIndex" v-if="item.prescription_item_type_id == category.id">
                                                    <b-td>{{ item.pharmacy_catalog_name }}</b-td>
                                                    <b-td>{{ item.dose }}</b-td>
                                                    <b-td>{{ item.qty }} {{ item.package_name }}</b-td>
                                                    <b-td>{{ item.used }}</b-td>
                                                    <b-td>{{ item.desc }}</b-td>
                                                </b-tr>
                                            </template>
                                        </b-tbody>
                                    </b-table-simple>
                                </div>
                            </template>
                            
                            <template #cell(action)="row">
                                <div v-if="row.item.flag == 0">Resep belum diproses</div>
                                <div v-else>Resep telah diproses<br>
                                <router-link :to="{ path: '/pharmacy/recipe/outpatient/retur/'+ row.item.id}">
                                    <b-button v-if="row.item.flag === 1" size="sm" variant="warning-700">
                                        <feather-icon icon="CornerDownLeftIcon" size="10" class="mb-1" /> Retur
                                    </b-button>
                                </router-link>
                                </div>
                            </template>
                        </b-table>
                    </div>
                </div>
            </section>  
        </b-overlay>
    </div>
</template>

<script>
import moment from 'moment'
import TopPharmacyMenu from '@/@core/components/top-menu/TopPharmacyMenu.vue';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import vSelect from 'vue-select'
// import PharmacyReportMenu from '@/@core/components/tab-menu/PharmacyReportMenu.vue';
import { mapState } from 'vuex'
import router from '@/router/index.js'

export default {
    name: 'OutpatientRecipeDetail',
    components: {
        TopPharmacyMenu, vSelect, //PharmacyReportMenu, 
    },
    data() {
        return {
            // health_facility_id:'40f06534-07ec-4221-ad9d-dea08196079f',
            table:
            [
                { key: 'no', label: 'No', thStyle: 'width: 60px' },
                { key: 'no_order', label: 'Nomor Order', thStyle: 'width: 200px' },
                { key: 'form_prescription', label: 'Resep' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 140px' }
            ],
            prescriptionCategories: [
                { id: 1, label: "Obat Satuan" },
                { id: 2, label: "Racik 1" },
                { id: 3, label: "Racik 2" },
                { id: 4, label: "Racik 3" },
                { id: 5, label: "Racik 4" },
            ],
            prescription_list: [],
            show:false,
            search:{norm:''}
        }
    },
    filters:{
        currency(value) {
            return new Intl.NumberFormat("en-US", { style: "currency", currency: "IDR" }).format(value)
        },
        numberToDecimal(value) {
            let val = (value/1).toFixed(0).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },
        numberToDecimalCurr(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },
        decimalToNumber(value) {
            const val = value
                .replace(/(kr|\$|£|€)/g, '') // getting rid of currency
                .trim()
                .replace(/(.+)[.,](\d+)$/g, "$1x$2") // stripping number into $1: integer and $2: decimal part and putting it together with x as decimal point
                .replace(/[.,]/g, '') // getting rid of . AND ,
                .replace('x', '.'); // replacing x with .
            return parseFloat(val);
        }
    },
    computed: {
        ...mapState({ 
            OptionsPharmacyDepo: state => state.depo.options.depo,
        }),
        // outpatient - tab1, tab2, tab3
        firstRow() {
            return (this.OutpatientTable.currentPage - 1) * this.OutpatientTable.perPage + 1
        },
        lastRow() {
            return Math.min(this.OutpatientTable.currentPage * this.OutpatientTable.perPage, this.OutpatientTable.totalRows)
        }
    },
    methods: {
        hasItemsInCategory(items, categoryId) {
            return items.some(item => item.prescription_item_type_id === categoryId);
        },
        rowPrescription(item, role) {
            if (!item || role !== 'row') return ''
            if (item.flag == 0) return ''
            if (item.flag == 1) return 'table-warning-400'
        },
        onSearch(){
            this.$store.dispatch('prescription/getByNorm', this.search.norm).then(data => {
                console.log(data)
                this.prescription_list = data
                // this.form_nurse.employee_nurse_id = this.userData.employee_id
                // this.form_nurse.registration_destination_id = id
                // this.show = false
            })
        },
        getData (id) {
			this.$store.dispatch('prescription/getById', id).then(data => {
                this.prescData = data[0]
                this.openPrescription(this.prescData)
                // this.form_nurse.employee_nurse_id = this.userData.employee_id
                // this.form_nurse.registration_destination_id = id
                // this.show = false
            })
        }
    },
    mounted () {
        // if (this.$route.params.id) {
        //     this.form_transaction.id = this.$route.params.id
        //     this.getData(this.$route.params.id)
        // }
    },
    created () {
        // this.$store.dispatch('healthfacility/getParam', 'healthfacility')
        // // this.$store.dispatch('registration/getRanapAll',{health_facility_id:this.health_facility_id,date_start:moment(this.form_transaction.date_added).format('YYYY-MM-DD'),date_end:moment(this.form_transaction.date_added).format('YYYY-MM-DD')})
        // this.$store.dispatch('catalog/getParam')
        // this.$store.dispatch('depo/getParam','noncentral')
        // this.$store.dispatch('packages/getParam')
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>