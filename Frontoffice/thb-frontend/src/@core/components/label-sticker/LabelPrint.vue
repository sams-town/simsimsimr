<template>
  <div class="">
    <div v-for="(row, index) in rows" :key="index" class="label-box">
      <img :src="`${urlAsset}${row.qr_url}`" width="100" :alt="row.qr_url">
      <div class="font-ocrb fs-18 fw-900 text-uppercase" style="line-height: 100%;">
        <div>{{ row.first_split_name }}</div>
        <div class="mt-1">{{ row.norm }}</div>
        <div class="mt-1">{{ row.birth_date }} / {{ row.age }}</div>
        <div class="mt-1">{{ row.sex }}</div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      urlAsset: process.env.VUE_APP_ASSETS_BE,
    }
  },

  props: {
    rows: {
      type: Array,
      required: true
    }
  },

  computed: {
    qrSize() {
      return 80;
    }
  }
};
</script>

<style scoped>
@media print {
  @page {
    size: 55mm 20mm;
    margin: 0;
  }

  body {
    margin: 0;
    padding: 0;
  }
}

.font-ocrb {
    font-family: "OCRB";
    font-smooth: never;
    letter-spacing: -2px;
}

.label-box {
  width: 311.8px;
  height: 113.38px;
  display: flex;
  align-items: center;
  break-inside: avoid;
  overflow: hidden;
}

.label-box:not(:last-child) {
  page-break-after: always;
  break-after: page;
}
</style>
