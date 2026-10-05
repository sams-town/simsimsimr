<template>
    <div>
        <patientMenu selectedMenu="menu22" />
        <div class="label-head my-16">
            <span class="px-16 text-bold-24 text-neutral-700">Rawat Inap</span>
        </div>
        <PatientBanner />
        <section class="row mt-32">
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-header bg-primary"></div>
                    <div class="card-body">
                        <div class="text-uppercase">Monitoring Tanda Vital</div>
                    </div>
                    <router-link :to="{ path: '/patient/inpatient/ttv-monitoring/'+ regData.registration_destination_id }">
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>Detail</div>
                                <feather-icon icon="ChevronRightIcon" />
                            </div>
                        </div>
                    </router-link>
                </div>
            </div>
            <div class="col-lg-3 mt-12 mt-lg-0">
                <div class="card">
                    <div class="card-header bg-w3green"></div>
                    <div class="card-body">
                        <div class="text-uppercase">Detak Jantung Janin</div>
                    </div>
                    <router-link :to="{ path: '/patient/inpatient/heart-monitoring/'+ regData.registration_destination_id }">
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>Detail</div>
                                <feather-icon icon="ChevronRightIcon" />
                            </div>
                        </div>
                    </router-link>
                </div>
            </div>
            <div class="col-lg-3 mt-12 mt-lg-0">
                <div class="card">
                    <div class="card-header bg-orange-500"></div>
                    <div class="card-body">
                        <div class="text-uppercase">Sliding Scale Insulin</div>
                    </div>
                    <router-link :to="{ path: '/patient/inpatient/sliding-scale/'+ regData.registration_destination_id }">
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>Detail</div>
                                <feather-icon icon="ChevronRightIcon" />
                            </div>
                        </div>
                    </router-link>
                </div>
            </div>
            <div class="col-lg-3 mt-12 mt-lg-0">
                <div class="card">
                    <div class="card-header bg-danger-500"></div>
                    <div class="card-body">
                        <div class="text-uppercase">Tranfusi Darah</div>
                    </div>
                    <router-link :to="{ path: '/patient/inpatient/blood-transfusion/'+ regData.registration_destination_id }">
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>Detail</div>
                                <feather-icon icon="ChevronRightIcon" />
                            </div>
                        </div>
                    </router-link>
                </div>
            </div>
            <div class="col-lg-3 mt-12">
                <div class="card">
                    <div class="card-header bg-warning-500"></div>
                    <div class="card-body">
                        <div class="text-uppercase">EWS</div>
                    </div>
                    <router-link :to="{ path: '/patient/inpatient/ews/'+ regData.registration_destination_id }">
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>Detail</div>
                                <feather-icon icon="ChevronRightIcon" />
                            </div>
                        </div>
                    </router-link>
                </div>
            </div>
            <div class="col-lg-3 mt-12">
                <div class="card">
                    <div class="card-header bg-neutral-700"></div>
                    <div class="card-body">
                        <div class="text-uppercase">Grafik Suhu</div>
                    </div>
                    <router-link :to="{ path: '/patient/inpatient/temperature-chart/'+ regData.registration_destination_id }">
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>Detail</div>
                                <feather-icon icon="ChevronRightIcon" />
                            </div>
                        </div>
                    </router-link>
                </div>
            </div>
            <div class="col-lg-3 mt-12">
                <div class="card">
                    <div class="card-header bg-success-500"></div>
                    <div class="card-body">
                        <div class="text-uppercase">Balance Cairan</div>
                    </div>
                    <router-link :to="{ path: '/patient/inpatient/fluid-balance/'+ regData.registration_destination_id }">
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>Detail</div>
                                <feather-icon icon="ChevronRightIcon" />
                            </div>
                        </div>
                    </router-link>
                </div>
            </div>
            <div class="col-lg-3 mt-12">
                <div class="card">
                    <div class="card-header bg-success-500"></div>
                    <div class="card-body">
                        <div class="text-uppercase">Observasi Pasien Nyeri</div>
                    </div>
                    <router-link :to="{ path: '/patient/inpatient/pain-observation/'+ regData.registration_destination_id }">
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>Detail</div>
                                <feather-icon icon="ChevronRightIcon" />
                            </div>
                        </div>
                    </router-link>
                </div>
            </div>
            <div class="col-lg-3 mt-12">
                <div class="card">
                    <div class="card-header bg-success-500"></div>
                    <div class="card-body">
                        <div class="text-uppercase">Partograf Kebidanan</div>
                    </div>
                    <router-link :to="{ path: '/patient/inpatient/partograph/'+ regData.registration_destination_id }">
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>Detail</div>
                                <feather-icon icon="ChevronRightIcon" />
                            </div>
                        </div>
                    </router-link>
                </div>
            </div>
        </section>
    </div>
</template>
<script>
import patientMenu from '@/@core/components/tab-menu/patientMenu.vue';
import administration from '@/views/layouts/patient/doctor/administration.vue';
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import vSelect from 'vue-select'

export default {
    name: 'PatientInpatient',
    components: {
        patientMenu, administration, ModernLabel, vSelect
    },
    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),
            reg_des_id: '',
            regData: {},
        }
    },
    methods: {
        getData(regDesId) {
			this.$store.dispatch('registration/getById', regDesId).then(data => {
                this.regData = data[0]
            })
        },
    },
   
    created () {
        if (this.$route.params.id) {
            this.reg_des_id = this.$route.params.id
            this.getData(this.$route.params.id)
        }
    }
}
</script>
<style scoped>
.label-head::before {
    content: '';
    border-left: 6px solid #648DE1;
}
.c-img {
    aspect-ratio: 1/1;
    object-fit: cover;
    max-width: 150px;
    max-height: 150px;
    border: 10px solid white;
}

</style>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>