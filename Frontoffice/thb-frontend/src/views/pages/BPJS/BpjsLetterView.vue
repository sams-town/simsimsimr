<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Poliklinik' }
            ]"
        >
        <BpjsLetter />
        </Layout>
    </div>
</template>
<script>
import Layout from '@/@core/light/Layout.vue';
import BpjsLetter from '@/views/layouts/BPJS/BpjsLetter.vue';
  
  export default {
    name: 'BpjsLetterView',
    components: {
      Layout, BpjsLetter
    },
    
  }
</script>