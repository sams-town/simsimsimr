<style>
/* @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap'); */

</style>
<template>
    <div
    id="app"
    class="h-100"
  >
    <b-overlay :show="show" blur="5rem">
      <router-view />
      <template #overlay>
        <div style="width: 100%; height: 100vh;" class="d-flex justify-content-center align-items-center">
          <div class="position-relative d-flex justify-content-center align-items-center">
            <img src="./@core/assets/images/login/grahakara_on black.png" class="position-absolute start-0" style="width: 64px;">
            <img src="./@core/assets/icons/apexa-spinner.svg" alt="-spinner" class="position-relative apexa-spinner">
          </div>
        </div>
      </template>
    </b-overlay>
  </div>
  <!-- <b-overlay :show="show" blur="5rem">
    <router-view />

    <template #overlay>
      <div style="width: 100%; height: 100vh;" class="d-flex justify-content-center align-items-center">
        <div class="position-relative d-flex justify-content-center align-items-center">
          <img src="./@core/assets/images/apexa.png" class="position-absolute start-0" style="width: 64px;">
          <img src="./@core/assets/icons/apexa-spinner.svg" alt="-spinner" class="position-relative apexa-spinner">
        </div>
      </div>
    </template>
  </b-overlay> -->
</template>
<script>
import store from '@/store';
import { $themeBreakpoints, $themeConfig } from '@themeConfig';

export default {
  data() {
    return {
      show: false,
      resizeHandler: null,
    }
  },
  created() {
    // Set breakpoints from CSS vars
    const breakpoints = ['xs', 'sm', 'md', 'lg', 'xl']
    breakpoints.forEach(bp => {
      const rawVal = getComputedStyle(document.documentElement).getPropertyValue(`--breakpoint-${bp}`)
      $themeBreakpoints[bp] = Number(rawVal.trim().replace('px', ''))
    })

    // Set RTL direction
    const isRTL = $themeConfig.layout.isRTL
    document.documentElement.setAttribute('dir', isRTL ? 'rtl' : 'ltr')

    // Commit initial window width
    store.commit('app/UPDATE_WINDOW_WIDTH', window.innerWidth)

    // Set up resize listener
    this.resizeHandler = () => {
      store.commit('app/UPDATE_WINDOW_WIDTH', window.innerWidth)
    }

    window.addEventListener('resize', this.resizeHandler)
  },

  beforeDestroy() {
    window.removeEventListener('resize', this.resizeHandler)
  },
  methods: {
    async loadAsyncFunctions() {
      await this.delay(1000); // Delay for 1 second
      await this.helpertype_how_to_enter();
      await this.helpertype_register();
      await this.helpertype_emergency();
      await this.helpertype_emergency_case();
      await this.helperalias();
      await this.helpertype_room();
      await this.helpertype_status_bed();
      await this.helpersex();
      await this.helperreligion();
      await this.helpermarried();
      await this.helpercitizenship();
      await this.helperblood();
      await this.helperlast_edu();
      await this.helperprofession();
      await this.helperresponsible();
      await this.helpertype_allergy();
      await this.helperinsurance();
      await this.helperpatient_issue();
      await this.helpertypeidcard();
      await this.getParamProvince();
      await this.getParamThirdPartyCompany();
      await this.getParamServiceClass();
      await this.healthfacility();
      await this.delay(2000); // Delay for 2 second
      await this.done();
    },

    async delay(ms) {
      return new Promise(resolve => setTimeout(resolve, ms));
    },
    async helpertype_how_to_enter() {
      this.$store.dispatch('helper/getParam', 'type_how_to_enter')
    },
    async helpertype_register() {
      this.$store.dispatch('helper/getParam', 'type_register')
    },
    async helpertype_emergency() {
      this.$store.dispatch('helper/getParam', 'type_emergency')
    },
    async helpertype_emergency_case() {
      this.$store.dispatch('helper/getParam', 'type_emergency_case')
    },
    async helperalias() {
      this.$store.dispatch('helper/getParam', 'alias')
    },
    async helpertype_room() {
      this.$store.dispatch('helper/getParam', 'type_room')
    },
    async helpertype_status_bed() {
      this.$store.dispatch('helper/getParam', 'type_status_bed')
    },
    async helpersex() {
      this.$store.dispatch('helper/getParam', 'sex')
    },
    async helperreligion() {
      this.$store.dispatch('helper/getParam', 'religion')
    },
    async helpermarried() {
      this.$store.dispatch('helper/getParam', 'married')
    },
    async helpercitizenship() {
      this.$store.dispatch('helper/getParam', 'citizenship')
    },
    async helperblood() {
      this.$store.dispatch('helper/getParam', 'blood')
    },
    async helperlast_edu() {
      this.$store.dispatch('helper/getParam', 'last_edu')
    },
    async helperprofession() {
      this.$store.dispatch('helper/getParam', 'profession')
    },
    async helperresponsible() {
      this.$store.dispatch('helper/getParam', 'responsible')
    },
    async helpertype_allergy() {
      this.$store.dispatch('helper/getParam', 'type_allergy')
    },
    async helperinsurance() {
      this.$store.dispatch('helper/getParam', 'insurance')
    },
    async helperpatient_issue() {
      this.$store.dispatch('helper/getParam', 'patient_issue')
    },
    async helpertypeidcard() {
      this.$store.dispatch('helper/getParam', 'typeidcard')
    },
    async getParamProvince() {
      this.$store.dispatch('helper/getParamProvince', 'province')
    },
    async getParamThirdPartyCompany() {
      this.$store.dispatch('helper/getParamThirdPartyCompany', 'third_party_company')
    },
    async getParamServiceClass() {
      this.$store.dispatch('helper/getParamServiceClass', 'service_class')
    },
    async healthfacility() {
      this.$store.dispatch('healthfacility/getParam', 'healthfacility')
    },
    async done() {
      this.show = false
    },
  },
  mounted() {
    if (localStorage.getItem(`accessToken`)) {
      if (localStorage.getItem(`helper-type_how_to_enter`) &&
        localStorage.getItem(`helper-type_register`) &&
        localStorage.getItem(`helper-type_emergency`) &&
        localStorage.getItem(`helper-type_emergency_case`) &&
        localStorage.getItem(`helper-alias`) &&
        localStorage.getItem(`helper-type_room`) &&
        localStorage.getItem(`helper-type_status_bed`) &&
        localStorage.getItem(`helper-sex`) &&
        localStorage.getItem(`helper-religion`) &&
        localStorage.getItem(`helper-married`) &&
        localStorage.getItem(`helper-citizenship`) &&
        localStorage.getItem(`helper-blood`) &&
        localStorage.getItem(`helper-last_edu`) &&
        localStorage.getItem(`helper-profession`) &&
        localStorage.getItem(`helper-responsible`) &&
        localStorage.getItem(`helper-type_allergy`) &&
        localStorage.getItem(`helper-insurance`) &&
        localStorage.getItem(`helper-patient_issue`) &&
        localStorage.getItem(`helper-typeidcard`) &&
        localStorage.getItem(`getParamProvince`) &&
        localStorage.getItem(`getParamThirdPartyCompany`) &&
        localStorage.getItem(`getParamServiceClass`) &&
        localStorage.getItem(`healthfacility`)) {
        this.show = false
      } else {
        this.show = true
        this.loadAsyncFunctions()
      }
    }
  },
  watch: {
    $route(to) {
      let userData = JSON.parse(localStorage.getItem('userData'))
      let myObj = {
        url: to.fullPath,
        employee_id: userData?.employee_id
      }

      if (to.fullPath !== "/login") {
        localStorage.setItem("lastVisited", JSON.stringify(myObj));
      }
    }
  },
}
</script>

<style>
  .apexa-spinner {
    width: 150px;
    height: 150px;
    z-index: -1;
    animation:sh1 0.9s infinite linear;
    /* background: #8d7958; */
    /* clip-path: polygon(100.00% 50.00%,64.14% 64.14%,50.00% 100.00%,35.86% 64.14%,0.00% 50.00%,35.86% 35.86%,50.00% 0.00%,64.14% 35.86%);
    -webkit-mask:radial-gradient(circle 5px,#0000 90%,#000); */
  }

  @keyframes sh1{
    100%{transform: rotate(1turn)}
  }
</style>

<style lang="scss">
  @import '@core/scss/vue/libs/vue-select.scss';
</style>
