<template>
    <div>
        <TopPharmacyMenu selectedMenu="menu12" />
        <div class="col mt-32">
            <div class="card">
                <div class="card-header bg-primary fw-bold text-center text-white">Filter Penjualan Laporan Farmasi</div>
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-6">
                                    <label class="c-form-label">Tanggal Mulai</label>
                                    <b-form-input type="date" v-model="form_export.date_start" :max="form_export.date_end" />
                                </div>
                                <div class="col-lg-6">
                                    <label class="c-form-label">Tanggal Akhir</label>
                                    <b-form-input type="date" v-model="form_export.date_end" :min="form_export.date_start" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        <b-button @click="exportExcel" :disabled="disabled_export" variant="w3green" class="px-16">
                            <feather-icon icon="FileTextIcon" /> Export Excel
                        </b-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import TopPharmacyMenu from '@/@core/components/top-menu/TopPharmacyMenu.vue';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import moment from 'moment'

export default {
    components: {
        TopPharmacyMenu
    },
    data() {
        return {
            urlRoot: process.env.VUE_APP_ROOT_API,
            disabled_export: false,
            form_export: {
                date_start: moment(new Date()).format('YYYY-MM-DD'),
                date_end: moment(new Date()).format('YYYY-MM-DD'),
            },
        }
    },
    methods: {
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        exportExcel() {
            this.disabled_export = true
            if (this.form_export.date_start && this.form_export.date_end) {
                window.open(this.urlRoot+`report/pharmacy/selling/get?date_start=${ this.form_export.date_start }&date_end=${ this.form_export.date_end }`)
                this.disabled_export = false
            } else {
                this.notification('Warning','CloseIcon','warning','Tanggal mulai dan tanggal akhir wajib diisi')
                this.disabled_export = false
            }
        }
    },
} 
</script>