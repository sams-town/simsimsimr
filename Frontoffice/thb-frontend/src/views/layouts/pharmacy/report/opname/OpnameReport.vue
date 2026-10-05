<template>
    <div>
        <TopPharmacyMenu selectedMenu="menu12" />
        <div class="col mt-32">
            <div class="card">
                <div class="card-header bg-primary-200 fw-bold">Laporan Stok Opname</div>
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <div class="col-lg-9">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div>
                                        <label class="c-form-label">Opname</label>
                                        <v-select v-model="opname_id" aria-readonly="true" :options="OpnameOptions" :reduce="OpnameOptions => OpnameOptions.value" label="title" placeholder="-- Pilih Data Stok Opname --"></v-select>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Laporan</label>
                                        <b-form-radio-group v-model="report_type">
                                            <div class="p-2 rounded mt-2" :class="report_type === '1'? 'bg-pink-500 text-white' :'bg-pink-100'">
                                                <b-form-radio value="1">Export Laporan (Excel)</b-form-radio>
                                            </div>
                                        </b-form-radio-group>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-24 d-flex">
                                <!-- <b-button pill variant="outline-primary" class="ml-auto px-3 mr-2">
                                    <feather-icon icon="XIcon" /> Clear Filter
                                </b-button> -->
                                <!-- <router-link :to="report_type === '1' ? { name: 'PharmacyReportAcceptancePrintView' } : '#'" target="_blank"> -->
                                <a href="asd" target="_blank">
                                    <b-button pill variant="primary" @click="exportData()" class="px-3">Download Data</b-button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import TopPharmacyMenu from '@/@core/components/top-menu/TopPharmacyMenu.vue';
import { mapState } from 'vuex'
import vSelect from 'vue-select'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default {
    components: {
        TopPharmacyMenu,vSelect
    },
    data() {
        return {
            opname_id:'',
            datePick: {
                start: null,
                end: null
            },
            report_type: '1'
        }
    },
    computed: {
        ...mapState({ 
            OpnameOptions: state => state.opname.options
        }),
    },
    methods: {
        exportData () {
            window.open(process.env.VUE_APP_ROOT_API+"pharmacy/opname_activation/excel?id="+this.opname_id)
        }
    },
    created () {
        this.$store.dispatch('opname/getParam')
    }
} 
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>