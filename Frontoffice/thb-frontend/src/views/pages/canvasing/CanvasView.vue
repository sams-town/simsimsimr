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
        <Layout/>
        <section class="content">
            <div class="row custom-row">
                <div class="card col px-0">
                    <div class="body-header">
                        <ul class="breadcrumb float-md-right rounded-pill">
                            <li class="breadcrumb-item"><a href="index.html"><b-icon icon="house-fill"></b-icon></a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Sample Pages</a></li>
                            <li class="breadcrumb-item active">Stater Page</li>
                        </ul>
                        <h4>Canvasing</h4>
                    </div>
                    <div class="container-fluid">
                        <div class="row clearfix">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="body">
                                        <CanvasContainer/>
                                        <KajianMata/>
                                        <!-- <KajianGigi/> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
<style>
@import '../../../@core/css/utility.css';

.row.custom-row {
    margin: 0;
}

section.content {
    padding: 0;
}
</style>
  <script>
  import Layout from '@/@core/light/Layout.vue';
  import CanvasContainer from '@/views/pages/canvasing/CanvasContainer.vue';
  import KajianMata from '@/views/pages/canvasing/jsView/KajianMataView.vue';
  import KajianGigi from '@/views/pages/canvasing/jsView/KajianGigiView.vue';

  export default {
    name: 'app',
    components: {
      Layout, CanvasContainer, KajianMata, KajianGigi
    },
    
  }
  </script>