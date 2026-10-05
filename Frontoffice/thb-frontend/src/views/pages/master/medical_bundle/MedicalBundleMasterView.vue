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
    components: { Layout, },
    computed: {
        layout() {
            return `layout`
        }
    }
}
</script>
<template>
    <div>
        <Layout selected_menu="menu16" selected_submenu="submenu3" />
        <section class="content px-0">
            <div class="row mx-0">
                <div class="card col px-0 border-0">
                    <div class="body-header">
                        <ul class="breadcrumb float-md-right rounded-pill">
                            <li class="breadcrumb-item"><a href="index.html"><b-icon icon="house-fill"></b-icon></a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Sample Pages</a></li>
                            <li class="breadcrumb-item active">Stater Page</li>
                        </ul>
                        <h4>Master Paket Kesehatan</h4>
                    </div>
                    <div class="container-fluid">
                        <div class="row clearfix">
                            <div class="col-lg-12 py-lg-16">
                                <MedicalBundleMaster />
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
import MedicalBundleMaster from '@/views/layouts/master/medical_bundle/MedicalBundleMaster.vue';
  
  export default {
    name: 'MedicalBundleMasterView',
    components: {
      Layout, MedicalBundleMaster
    },
    
  }
</script>