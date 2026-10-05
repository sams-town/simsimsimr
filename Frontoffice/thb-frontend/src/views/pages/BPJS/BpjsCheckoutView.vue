<template>
  <div>
      <Layout
          :layout_breadcrumb_items="[
              { text: 'Home', link: '/' },
              { text: 'Poliklinik' }
          ]"
      >
      <BpjsCheckout />
      </Layout>
  </div>
</template>
<script>
import Layout from '@/@core/light/Layout.vue';
import BpjsCheckout from '@/views/layouts/BPJS/BpjsCheckout.vue';

export default {
  name: 'BpjsCheckoutView',
  components: {
    Layout, BpjsCheckout
  },
  
}
</script>