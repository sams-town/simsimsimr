<template>
    <div class="row">
        <section class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-header bg-primary fw-bold text-center text-white">RL 1.3 Tempat Tidur</div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        <b-button @click="exportBed" :disabled="disabled.bed" variant="w3green" class="px-16">
                            <feather-icon icon="FileTextIcon" /> Export Excel
                        </b-button>
                    </div>
                </div>
            </div>
        </section>
        <section class="col-md-6 col-lg-4 mt-16 mt-md-0">
            <div class="card">
                <div class="card-header bg-primary fw-bold text-center text-white">RL 3.3 Gigi & Mulut</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <label class="c-form-label">Tanggal Mulai</label>
                            <b-form-input type="date" v-model="form_tooth.date_start" :max="form_tooth.date_end" />
                        </div>
                        <div class="col-lg-6">
                            <label class="c-form-label">Tanggal Akhir</label>
                            <b-form-input type="date" v-model="form_tooth.date_end" :min="form_tooth.date_start" />
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        <b-button @click="exportSelected('tooth','rl33')" :disabled="disabled.tooth" variant="w3green" class="px-16">
                            <feather-icon icon="FileTextIcon" /> Export Excel
                        </b-button>
                    </div>
                </div>
            </div>
        </section>
        <section class="col-md-6 col-lg-4 mt-16 mt-lg-0">
            <div class="card">
                <div class="card-header bg-primary fw-bold text-center text-white">RL 3.4 Kebidanan</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <label class="c-form-label">Tanggal Mulai</label>
                            <b-form-input type="date" v-model="form_midwifery.date_start" :max="form_midwifery.date_end" />
                        </div>
                        <div class="col-lg-6">
                            <label class="c-form-label">Tanggal Akhir</label>
                            <b-form-input type="date" v-model="form_midwifery.date_end" :min="form_midwifery.date_start" />
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        <b-button @click="exportSelected('midwifery','rl34')" :disabled="disabled.midwifery" variant="w3green" class="px-16">
                            <feather-icon icon="FileTextIcon" /> Export Excel
                        </b-button>
                    </div>
                </div>
            </div>
        </section>
        <section class="col-md-6 col-lg-4 mt-16">
            <div class="card">
                <div class="card-header bg-primary fw-bold text-center text-white">RL 3.7 Radiologi</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <label class="c-form-label">Tanggal Mulai</label>
                            <b-form-input type="date" v-model="form_radiology.date_start" :max="form_radiology.date_end" />
                        </div>
                        <div class="col-lg-6">
                            <label class="c-form-label">Tanggal Akhir</label>
                            <b-form-input type="date" v-model="form_radiology.date_end" :min="form_radiology.date_start" />
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        <b-button @click="exportSelected('radiology','rl37')" :disabled="disabled.radiology" variant="w3green" class="px-16">
                            <feather-icon icon="FileTextIcon" /> Export Excel
                        </b-button>
                    </div>
                </div>
            </div>
        </section>
        <section class="col-md-6 col-lg-4 mt-16">
            <div class="card">
                <div class="card-header bg-primary fw-bold text-center text-white">RL 3.8 Laboratorium</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <label class="c-form-label">Tanggal Mulai</label>
                            <b-form-input type="date" v-model="form_laboratory.date_start" :max="form_laboratory.date_end" />
                        </div>
                        <div class="col-lg-6">
                            <label class="c-form-label">Tanggal Akhir</label>
                            <b-form-input type="date" v-model="form_laboratory.date_end" :min="form_laboratory.date_start" />
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        <b-button @click="exportSelected('laboratory','rl38')" :disabled="disabled.laboratory" variant="w3green" class="px-16">
                            <feather-icon icon="FileTextIcon" /> Export Excel
                        </b-button>
                    </div>
                </div>
            </div>
        </section>
        <section class="col-md-6 col-lg-4 mt-16">
            <div class="card">
                <div class="card-header bg-primary fw-bold text-center text-white">RL 3.9 Rehabilitasi Medik</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <label class="c-form-label">Tanggal Mulai</label>
                            <b-form-input type="date" v-model="form_rehabilitation.date_start" :max="form_rehabilitation.date_end" />
                        </div>
                        <div class="col-lg-6">
                            <label class="c-form-label">Tanggal Akhir</label>
                            <b-form-input type="date" v-model="form_rehabilitation.date_end" :min="form_rehabilitation.date_start" />
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        <b-button @click="exportSelected('rehabilitation','rl39')" :disabled="disabled.rehabilitation" variant="w3green" class="px-16">
                            <feather-icon icon="FileTextIcon" /> Export Excel
                        </b-button>
                    </div>
                </div>
            </div>
        </section>
        <section class="col-md-6 col-lg-4 mt-16">
            <div class="card">
                <div class="card-header bg-primary fw-bold text-center text-white">RL 3.11 Kesehatan Jiwa</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <label class="c-form-label">Tanggal Mulai</label>
                            <b-form-input type="date" v-model="form_mental.date_start" :max="form_mental.date_end" />
                        </div>
                        <div class="col-lg-6">
                            <label class="c-form-label">Tanggal Akhir</label>
                            <b-form-input type="date" v-model="form_mental.date_end" :min="form_mental.date_start" />
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        <b-button @click="exportSelected('mental','rl311')" :disabled="disabled.mental" variant="w3green" class="px-16">
                            <feather-icon icon="FileTextIcon" /> Export Excel
                        </b-button>
                    </div>
                </div>
            </div>
        </section>
        <section class="col-md-6 col-lg-4 mt-16">
            <div class="card">
                <div class="card-header bg-primary fw-bold text-center text-white">RL 3.12 Keluarga Berencana</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <label class="c-form-label">Tanggal Mulai</label>
                            <b-form-input type="date" v-model="form_family.date_start" :max="form_family.date_end" />
                        </div>
                        <div class="col-lg-6">
                            <label class="c-form-label">Tanggal Akhir</label>
                            <b-form-input type="date" v-model="form_family.date_end" :min="form_family.date_start" />
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        <b-button @click="exportSelected('family','rl312')" :disabled="disabled.family" variant="w3green" class="px-16">
                            <feather-icon icon="FileTextIcon" /> Export Excel
                        </b-button>
                    </div>
                </div>
            </div>
        </section>
        <section class="col-md-6 col-lg-4 mt-16">
            <div class="card">
                <div class="card-header bg-primary fw-bold text-center text-white">RL 3.15 Cara Bayar</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <label class="c-form-label">Tanggal Mulai</label>
                            <b-form-input type="date" v-model="form_pay.date_start" :max="form_pay.date_end" />
                        </div>
                        <div class="col-lg-6">
                            <label class="c-form-label">Tanggal Akhir</label>
                            <b-form-input type="date" v-model="form_pay.date_end" :min="form_pay.date_start" />
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        <b-button @click="exportSelected('pay','rl315')" :disabled="disabled.pay" variant="w3green" class="px-16">
                            <feather-icon icon="FileTextIcon" /> Export Excel
                        </b-button>
                    </div>
                </div>
            </div>
        </section>
        <section class="col-md-6 col-lg-4 mt-16">
            <div class="card">
                <div class="card-header bg-primary fw-bold text-center text-white">RL 4.a Penyakit Rawat Inap</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <label class="c-form-label">Tanggal Mulai</label>
                            <b-form-input type="date" v-model="form_inpatient_ill.date_start" :max="form_inpatient_ill.date_end" />
                        </div>
                        <div class="col-lg-6">
                            <label class="c-form-label">Tanggal Akhir</label>
                            <b-form-input type="date" v-model="form_inpatient_ill.date_end" :min="form_inpatient_ill.date_start" />
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        <b-button @click="exportSelected('inpatient_ill','rl4a')" :disabled="disabled.inpatient_ill" variant="w3green" class="px-16">
                            <feather-icon icon="FileTextIcon" /> Export Excel
                        </b-button>
                    </div>
                </div>
            </div>
        </section>
        <section class="col-md-6 col-lg-4 mt-16">
            <div class="card">
                <div class="card-header bg-primary fw-bold text-center text-white">RL 4.b Penyakit Rawat Jalan</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <label class="c-form-label">Tanggal Mulai</label>
                            <b-form-input type="date" v-model="form_outpatient_ill.date_start" :max="form_outpatient_ill.date_end" />
                        </div>
                        <div class="col-lg-6">
                            <label class="c-form-label">Tanggal Akhir</label>
                            <b-form-input type="date" v-model="form_outpatient_ill.date_end" :min="form_outpatient_ill.date_start" />
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        <b-button @click="exportSelected('outpatient_ill','rl4b')" :disabled="disabled.outpatient_ill" variant="w3green" class="px-16">
                            <feather-icon icon="FileTextIcon" /> Export Excel
                        </b-button>
                    </div>
                </div>
            </div>
        </section>
        <section class="col-md-6 col-lg-4 mt-16">
            <div class="card">
                <div class="card-header bg-primary fw-bold text-center text-white">RL 5.1 Pengunjung Rumah Sakit</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <label class="c-form-label">Tanggal Mulai</label>
                            <b-form-input type="date" v-model="form_hospital_visitors.date_start" :max="form_hospital_visitors.date_end" />
                        </div>
                        <div class="col-lg-6">
                            <label class="c-form-label">Tanggal Akhir</label>
                            <b-form-input type="date" v-model="form_hospital_visitors.date_end" :min="form_hospital_visitors.date_start" />
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        <b-button @click="exportSelected('hospital_visitors','rl51')" :disabled="disabled.hospital_visitors" variant="w3green" class="px-16">
                            <feather-icon icon="FileTextIcon" /> Export Excel
                        </b-button>
                    </div>
                </div>
            </div>
        </section>
        <section class="col-md-6 col-lg-4 mt-16">
            <div class="card">
                <div class="card-header bg-primary fw-bold text-center text-white">RL 5.2 Kunjungan Rawat Jalan</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <label class="c-form-label">Tanggal Mulai</label>
                            <b-form-input type="date" v-model="form_outpatient_visit.date_start" :max="form_outpatient_visit.date_end" />
                        </div>
                        <div class="col-lg-6">
                            <label class="c-form-label">Tanggal Akhir</label>
                            <b-form-input type="date" v-model="form_outpatient_visit.date_end" :min="form_outpatient_visit.date_start" />
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        <b-button @click="exportSelected('outpatient_visit','rl52')" :disabled="disabled.outpatient_visit" variant="w3green" class="px-16">
                            <feather-icon icon="FileTextIcon" /> Export Excel
                        </b-button>
                    </div>
                </div>
            </div>
        </section>
        <section class="col-md-6 col-lg-4 mt-16">
            <div class="card">
                <div class="card-header bg-primary fw-bold text-center text-white">RL 5.3 10 Besar Penyakit Rawat Inap</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <label class="c-form-label">Tanggal Mulai</label>
                            <b-form-input type="date" v-model="form_top_10_inpatient_diseases.date_start" :max="form_top_10_inpatient_diseases .date_end" />
                        </div>
                        <div class="col-lg-6">
                            <label class="c-form-label">Tanggal Akhir</label>
                            <b-form-input type="date" v-model="form_top_10_inpatient_diseases .date_end" :min="form_top_10_inpatient_diseases .date_start" />
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        <b-button @click="exportSelected('top_10_inpatient_diseases','rl53')" :disabled="disabled.top_10_inpatient_diseases" variant="w3green" class="px-16">
                            <feather-icon icon="FileTextIcon" /> Export Excel
                        </b-button>
                    </div>
                </div>
            </div>
        </section>
                <section class="col-md-6 col-lg-4 mt-16">
            <div class="card">
                <div class="card-header bg-primary fw-bold text-center text-white">RL 5.4 10 Besar Penyakit Rawat Jalan</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <label class="c-form-label">Tanggal Mulai</label>
                            <b-form-input type="date" v-model="form_top_10_outpatient_diseases.date_start" :max="form_top_10_outpatient_diseases.date_end" />
                        </div>
                        <div class="col-lg-6">
                            <label class="c-form-label">Tanggal Akhir</label>
                            <b-form-input type="date" v-model="form_top_10_outpatient_diseases.date_end" :min="form_top_10_outpatient_diseases.date_start" />
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        <b-button @click="exportSelected('top_10_outpatient_diseases','rl54')" :disabled="disabled.top_10_outpatient_diseases" variant="w3green" class="px-16">
                            <feather-icon icon="FileTextIcon" /> Export Excel
                        </b-button>
                    </div>
                </div>
            </div>
        </section>
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
            disabled: {
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