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
        <Layout selected_menu="menu6"/>
        <section class="content px-0">
            <div class="row mx-0">
                <div class="card col px-0 border-0">
                    <div class="body-header">
                        <ul class="breadcrumb float-md-right rounded-pill">
                            <li class="breadcrumb-item"><a href="index.html"><b-icon icon="house-fill"></b-icon></a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">BPJS</a></li>
                            <li class="breadcrumb-item active">Perserta Rujuk Balik (PRB)</li>
                        </ul>
                        <h4>Cek Peserta BPJS</h4>
                    </div>
                    <div class="container-fluid">
                        <div class="row clearfix">
                            <div class="col-lg-12 py-lg-16">
                                <BPJSPrb />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
<script>
import Layout from '@/@core/light/Layout.vue';
import BPJSPrb from '@/views/layouts/BPJS/BPJSPrb.vue';
  
  export default {
    name: 'BPJSListView',
    components: {
      Layout, BPJSPrb
    },
    
  }
</script>