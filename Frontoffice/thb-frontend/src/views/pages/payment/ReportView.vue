<template>
    <div id="app">
      <component :is="layout">
        <router-view />
      </component>
    </div>
  </template>
  
<script>
const Layout = () => import('@/views/layouts/Main.vue')
export default {
    components: {
        // Layouts
        Layout
    },
    computed: {
        layout() {
            return `layout`
        }
    }
}
</script>
<template>
    <div>
        <Layout
        :layout_breadcrumb_items="[
            { text: 'Home', link: '/' },
            { text: 'Kasir' },
            { text: 'Laporan Pembayaran' },
        ]"
        >
            <Report />
        </Layout>
    </div>
</template>
<script>
import Layout from '@/@core/light/Layout.vue';
import Report from '@/views/layouts/payment/Report.vue';
  
  export default {
    name: 'PatientDetailView',
    components: {
      Layout, Report
    },
    
  }
</script>