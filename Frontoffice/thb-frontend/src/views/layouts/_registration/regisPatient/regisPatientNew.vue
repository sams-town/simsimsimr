<template>
    <div>
        <topMenu selectedMenu="menu7" class="mt-2" />
        <div class="card shadow-sm mt-32">
            <div class="card-body">
                <span class="text-medium-24">Pendaftaran Pasien</span>
                <div class="col col-lg-10 mt-2 px-0">
                    <div class="row mx-0">
                        <add_patient @getFromCombinedAddPatient="selectedFromCombinedAddPatient" :dataFromAppointment="dataFromAppointment" />&nbsp;
                        <appointment @getFromAppointment="selectedFromAppointment" :dataFromSearch="dataFromSearch" @onSearchOldPatient="triggerSearchPatient" />&nbsp;
                        <!-- <checkin @getFromAppointment="selectedFromAppointment" :dataFromSearch="dataFromSearch" @onSearchOldPatient="triggerSearchPatient" />&nbsp; -->
                        <!-- <BPJSModal />&nbsp; -->
                        <div class="px-1 d-flex">
                            <search_patient @getPatientOnReg="selectedPatientOnReg" @getPatientOnAppointment="selectedPatientOnAppointment" :dataTriggerSearch="dataTriggerSearch" />
                        </div>
                        <!-- <candidate_patient class="px-1" /> -->
                        <!-- <div class="col px-1 d-flex align-self-stretch">
                            <router-link to="#" class="col px-0 d-flex align-self-stretch">
                                <b-button size="sm" variant="pink-500" class="col text-white">
                                    <b-icon icon="search" scale="0.8"></b-icon>
                                    <span>No RM</span>
                                </b-button>
                            </router-link>
                        </div> -->
                    </div>
                </div>
                <hr class="my-24">
                <regisFormNew :dataSelected="dataSelected" :dataFromCombinedAddPatient="dataFromCombinedAddPatient" :dataFromAppointment="dataFromAppointment" />
            </div>
        </div>
    </div>
</template>
<script>
import regisFormNew from '@/views/layouts/_registration/regisPatient/regisFormNew.vue'
import topMenu from '@/@core/components/top-menu/topMenu.vue'
import candidate_patient from './modal/candidate_patient.vue'
import search_patient from './modal/search_patient.vue'
import appointment from './modal/appointment.vue'
import checkin from './modal/checkin.vue'
import add_patient from './modal/add_patient.vue'
import BPJSModal from './modal/BPJSModal.vue'

export default {
    components: { regisFormNew, topMenu, candidate_patient, search_patient, appointment, checkin, add_patient, BPJSModal },
    data() {
        return {
            dataSelected:'',
            dataFromAppointment: '',
            dataFromCombinedAddPatient: '',
            dataFromSearch: '',
            dataTriggerSearch: ''
        }
    },
    methods: {
        selectedPatientOnReg (value) {
            this.dataSelected = value
        },
        selectedPatientOnAppointment (value) {
            this.dataFromSearch = value
        },
        selectedFromAppointment (value) {
            this.dataFromAppointment = value
        },
        selectedFromCombinedAddPatient (value) {
            this.dataFromCombinedAddPatient = value
        },
        triggerSearchPatient (value) {
            this.dataTriggerSearch = value
        }
    }
}
</script>
<style scoped>
@import '../../../../@core/css/utility.css';

</style>