<!-- components/ApexaSpinner.vue -->
<template>
    <div
      v-if="visible"
      class="d-flex justify-content-center align-items-center"
      style="width: 100%; height: 100vh;"
    >
      <div class="position-relative d-flex justify-content-center align-items-center">
        <img src="@/@core/assets/images/apexa.png" class="position-absolute start-0" style="width: 64px;" />
        <img
          src="@/@core/assets/icons/apexa-spinner.svg"
          alt="-spinner"
          class="position-relative apexa-spinner"
        />
      </div>
    </div>
</template>
  
<script>
  export default {
    name: 'ApexaSpinner',
    props: {
      visible: {
        type: Boolean,
        default: true,
      },
    },
  }
</script>
  
<style scoped>
  .apexa-spinner {
    width: 150px;
    height: 150px;
    z-index: 9999999;
    animation:sh1 0.9s infinite linear;
    /* background: #8d7958; */
    /* clip-path: polygon(100.00% 50.00%,64.14% 64.14%,50.00% 100.00%,35.86% 64.14%,0.00% 50.00%,35.86% 35.86%,50.00% 0.00%,64.14% 35.86%);
    -webkit-mask:radial-gradient(circle 5px,#0000 90%,#000); */
  }

  @keyframes sh1{ 
    100%{transform: rotate(1turn)}
  }
</style>