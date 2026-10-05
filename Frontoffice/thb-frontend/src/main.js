import Vue from "vue";
import router from "./router";
import store from "./store";
import App from "./App.vue";
import VueCompositionAPI from "@vue/composition-api";
import VueQuillEditor from 'vue-quill-editor'

import 'quill/dist/quill.core.css' // import styles
import 'quill/dist/quill.snow.css' // for snow theme
import 'quill/dist/quill.bubble.css' // for bubble theme
// 3rd party plugins
import "@/libs/toastification";

// Vue-Barcode
import VueBarcode from "@chenfengyuan/vue-barcode";
const app = Vue;
app.component(VueBarcode.name, VueBarcode);

Vue.use(VueQuillEditor)

// Bootstrap Vue
import { BootstrapVue, ToastPlugin, IconsPlugin } from "bootstrap-vue";
import "bootstrap/dist/css/bootstrap.css";
import "bootstrap-vue/dist/bootstrap-vue.css";
import VueSignaturePad from 'vue-signature-pad';

Vue.use(BootstrapVue);
Vue.use(ToastPlugin);
Vue.use(IconsPlugin);
Vue.use(VueSignaturePad);

// Composition API
Vue.use(VueCompositionAPI);

// V-calendar
import Calendar from "v-calendar";
import DatePicker from "v-calendar";
Vue.component("calendar", Calendar);
Vue.component("date-picker", DatePicker);

// Global Components
import "./global-components";

// import core styles
require('@core/scss/core.scss')

Vue.config.productionTip = false;

new Vue({
  router,
  store,
  render: (h) => h(App),
}).$mount("#app");
