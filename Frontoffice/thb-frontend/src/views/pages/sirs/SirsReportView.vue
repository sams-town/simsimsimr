<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'SIRS' },
                // { text: 'Kartu Stok' },
            ]">        
            <b-overlay
                :show="show"
                spinner-variant="primary"
                spinner-type="grow"
                spinner-small
                rounded="sm"
                >
                <div class="card">
                    <div class="card-header bg-primary fw-bold text-center text-white">Laporan SIRS</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-center">
                            <div class="col-lg-9">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <label class="c-form-label">RL</label>
                                        <v-select v-model="params.type" aria-readonly="true" :options="OptionsType" :reduce="OptionsType => OptionsType.value" label="title" placeholder="-- PILIH RL --"></v-select>
                                    </div>
                                    <div class="col-lg-2">
                                        <label class="c-form-label">Tanggal Mulai</label>
                                        <b-form-input type="date" v-model="params.date_start" :max="params.date_end" />
                                    </div>
                                    <div class="col-lg-2 mt-12 mt-lg-0">
                                        <label class="c-form-label">Tanggal Akhir</label>
                                        <b-form-input type="date" v-model="params.date_end" :min="params.date_start" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-center">
                            <b-button @click="filter" :disabled="disabled.filter" variant="w3green" class="px-16">
                                <feather-icon icon="FileTextIcon" /> Filter
                            </b-button>
                            <b-button @click="exportExcel" :disabled="disabled.export" variant="w3green" class="px-16">
                                <feather-icon icon="FileTextIcon" /> Export Excel
                            </b-button>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header font-weight-bold bg-primary-200">List Data</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-center">
                            <div class="col-lg-12">
                                <div class="row">
                                    <b-table bordered responsive hover small head-variant="light" :items="items" :fields="fields" :per-page="0" :current-page="currentPage" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                                        <!-- <template #cell(batch)="row">
                                            <div>{{ row.item.batch  }} | {{ dateFormat(row.item.expired_date)  }}</div>
                                        </template>
                                        <template #cell(date_created)="row">
                                            <div>{{ dateFormatTime(row.item.date_created)  }}</div>
                                        </template>
                                        <template #cell(flag)="row">
                                            <b-badge v-if="row.item.flag === 1" class="text-uppercase p-8" style="font-size: 12px; font-weight: 500;" variant="info-500">Penerimaan</b-badge>
                                            <b-badge v-if="row.item.flag === 11" class="text-uppercase p-8" style="font-size: 12px; font-weight: 500;" variant="info-500">Batal/Retur Penerimaan</b-badge>
                                            <b-badge v-if="row.item.flag === 3" class="text-uppercase p-8" style="font-size: 12px; font-weight: 500;" variant="success-500">Pemakaian</b-badge>
                                            <b-badge v-if="row.item.flag === 4" class="text-uppercase p-8" style="font-size: 12px; font-weight: 500;" variant="primary-500">Mutasi</b-badge>
                                            <b-badge v-if="row.item.flag === 6" class="text-uppercase p-8" style="font-size: 12px; font-weight: 500;" variant="warning-500">Pemakaian Obat Kronis</b-badge>
                                            <b-badge v-if="row.item.flag === 7" class="text-uppercase p-8" style="font-size: 12px; font-weight: 500;" variant="warning-500">Opname</b-badge>
                                            <b-badge v-if="row.item.flag === 8" class="text-uppercase p-8" style="font-size: 12px; font-weight: 500;" variant="danger-500">Pengurangan Stok</b-badge>
                                        </template>
                                        <template #cell(action)="row">
                                            <div class="row mx-0">
                                                <b-button variant="warning-500" size="sm" class="mr-1">
                                                    <feather-icon icon="EditIcon" @click="editData(row.item)" />
                                                </b-button>
                                                <b-button variant="danger-500" size="sm">
                                                    <feather-icon icon="TrashIcon" @click="rowOnDelete(row.item.id)" />
                                                    <b-link v-b-modal.modal-center>
                                                    </b-link>
                                                </b-button>
                                            </div>
                                        </template> -->
                                    </b-table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </b-overlay>
        </Layout>
    </div>
</template>

<script>
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import moment from 'moment'
import vSelect from 'vue-select'

export default {
    components: { vSelect },
    data() {
        const defaultForm = {
            date_start: moment().format('YYYY-MM-DD'),
            date_end: moment().format('YYYY-MM-DD'),
        };

        return {
            urlRoot: process.env.VUE_APP_ROOT_API,
            authToken: localStorage.getItem('accessToken'),
            show:false,
            fields:{},
            items:{},
            params: {type:'',date_start:'2024-11-01',date_end:'2024-11-30'},
            OptionsType: [
                // {value:'13',title:'RL 1.3 TEMPAT TIDUR'},
                {value:'32',title:'RL 3.2 REKAPITULASI KEGIATAN PELAYANAN RAWAT INAP'},
                {value:'33',title:'RL 3.3 REKAPITULASI KEGIATAN PELAYANAN GAWAT DARURAT'},
                {value:'35',title:'RL 3.5 REKAPITULASI KUNJUNGAN'},
                {value:'37',title:'RL 3.7 Radiologi'},
                {value:'38',title:'RL 3.8 Laboratorium'},
                {value:'39',title:'RL 3.9 Rehabilitasi Medik'},
                {value:'311',title:'RL 3.11 Kesehatan Jiwa'},
                {value:'312',title:'RL 3.12 Keluarga Berencana'},
                {value:'315',title:'RL 3.15 Cara Bayar'},
                {value:'4a',title:'RL 4.a Penyakit Rawat Inap'},
                {value:'4b',title:'RL 4.b Penyakit Rawat Jalan'},
                {value:'51',title:'RL 5.1 Pengunjung Rumah Sakit'},
                {value:'52',title:'RL 5.2 Kunjungan Rawat Jalan'},
                {value:'5310',title:'RL 5.3 10 Besar Penyakit Rawat Inap'},
                {value:'5410',title:'RL 5.4 10 Besar Penyakit Rawat Jalan'}
            ],
            disabled: {
                filter: false,
                export: false,
                bed: false,
                tooth: false,
                midwifery: false,
                radiology: false,
                laboratory: false,
                rehabilitation: false,
                mental: false,
                family: false,
                pay: false,
                inpatient_ill: false,
                outpatient_ill: false,
                hospital_visitors: false,
                outpatient_visit: false,
                top_10_inpatient_diseases: false,
                top_10_outpatient_diseases: false,
            },

            form_tooth: { ...defaultForm },
            form_midwifery: { ...defaultForm },
            form_radiology: { ...defaultForm },
            form_laboratory: { ...defaultForm },
            form_rehabilitation: { ...defaultForm },
            form_mental: { ...defaultForm },
            form_family: { ...defaultForm },
            form_pay: { ...defaultForm },
            form_inpatient_ill: { ...defaultForm },
            form_outpatient_ill: { ...defaultForm },
            form_hospital_visitors: { ...defaultForm },
            form_outpatient_visit: { ...defaultForm},
            form_top_10_inpatient_diseases: { ...defaultForm},
            form_top_10_outpatient_diseases: { ...defaultForm},
        }
    },
    methods: {
        exportExcel () {

        },

        async filter () {
            try {
                this.disabled.filter = true
                await this.$store.dispatch('sirs/getFilter', this.params)
                .then(
                    async res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        console.log(res)
                        this.fields = res.fields
                        this.items = res.items
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.disabled.filter = false
            }  
        },

        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },

        async exportBed() {
            this.disabled_bed = true;

            try {
                const fullUrl = `${this.urlRoot}report/sirs/rl_one/rl13`;
                const response = await fetch(fullUrl, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${this.authToken}`,
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Download failed!');
                }

                const blob = await response.blob();
                const downloadUrl = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = downloadUrl;
                a.download = 'report.xlsx';
                document.body.appendChild(a);
                a.click();
                a.remove();

            } catch (error) {
                console.error('Error: ', error);
            } finally {
                this.disabled_bed = false;
            }
        },

        async exportSelected(property, code) {
            const url_date_start = 'date_start=' + this[`form_${property}`].date_start;
            const url_date_end = 'date_end=' + this[`form_${property}`].date_end;
            
            let codeLower = code.toLowerCase();
            let reportType = 'rl_three';
            if (codeLower.includes('rl5')) {
                reportType = 'rl_five';
            } else if (codeLower.includes('rl4')) {
                reportType = 'rl_four';
            } else if (codeLower.includes('rl3')) {
                reportType = 'rl_three';
            }
            const fullUrl = `${this.urlRoot}report/sirs/${reportType}/${code}?${url_date_start}&${url_date_end}`;

            if (this[`form_${property}`].date_start && this[`form_${property}`].date_end) {
                this[`disabled_${property}`] = true;
                try {
                    const response = await fetch(fullUrl, {
                        method: 'GET',
                        headers: {
                            'Authorization': `Bearer ${this.authToken}`,
                            'Content-Type': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Download failed!');
                    }

                    const blob = await response.blob();
                    const downloadUrl = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = downloadUrl;
                    a.download = 'report.xlsx';
                    document.body.appendChild(a);
                    a.click();
                    a.remove();

                } catch (error) {
                    console.error('Error: ', error);
                } finally {
                    this[`disabled_${property}`] = false;
                }
            } else {
                this.notification('Warning','CloseIcon','warning','Tanggal mulai & tanggal akhir wajib diisi')
            }
        },

    },
} 
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>