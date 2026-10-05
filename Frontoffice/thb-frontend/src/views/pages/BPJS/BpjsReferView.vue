<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Poliklinik' }
            ]"
        >
        <BPJSRefer />
        </Layout>
    </div>
</template>
<script>
import Layout from '@/@core/light/Layout.vue';
import BPJSRefer from '@/views/layouts/BPJS/BPJSRefer.vue';
  
  export default {
    name: 'BPJSListView',
    components: {
      Layout, BPJSRefer
    },
    
  }
</script>