import Vue from 'vue'

import FeatherIcon from '@core/components/feather-icon/FeatherIcon.vue'
import ModernLabel from "@core/components/label/ModernLabel.vue";
import Layout from './@core/light/Layout.vue';
import vSelect from 'vue-select'
import PatientBanner from '@/@core/components/top-menu/PatientBanner.vue';
import patientMenu from './@core/components/tab-menu/patientMenu.vue';
import PrescriptionInstruction from '@core/components/instruction/PrescriptionInstruction.vue'
import ApexaSpinner from '@/@core/components/spinner/ApexaSpinner.vue'

Vue.component(FeatherIcon.name, FeatherIcon)
Vue.component("ModernLabel", ModernLabel);
Vue.component("Layout", Layout);
Vue.component("v-select", vSelect);
Vue.component("PatientBanner", PatientBanner);
Vue.component("patientMenu", patientMenu);
Vue.component("PrescInstruction", PrescriptionInstruction);
Vue.component("ApexaSpinner", ApexaSpinner);