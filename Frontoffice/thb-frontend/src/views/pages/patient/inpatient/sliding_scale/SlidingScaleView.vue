<template>
    <div id="app">
      <component :is="layout">
        <router-view />
      </component>
    </div>
  </template>
  
<script>
import inpatient from '@/router/routes/inpatient';

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
        <Layout selected_menu="menu2"/>
        <section class="content px-0">
            <div class="row mx-0">
                <div class="card col px-0 border-0">
                    <div class="body-header">
                        <ul class="breadcrumb float-md-right rounded-pill">
                            <li class="breadcrumb-item"><a href="index.html"><b-icon icon="house-fill"></b-icon></a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Pasien</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Rawat Inap</a></li>
                            <li class="breadcrumb-item active">Sliding Scale</li>
                        </ul>
                        <h4>Sliding Scale Insulin</h4>
                    </div>
                    <div class="container-fluid">
                        <div class="row clearfix">
                            <div class="col-lg-12 py-lg-16">
                                <SlidingScale />
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
import SlidingScale from '@/views/layouts/patient/inpatient/sliding_scale/SlidingScale.vue';
  
  export default {
    name: 'SlidingScaleView',
    components: {
      Layout, SlidingScale
    },
    data() {
        return {
            id:''
        }
    },
    mounted () {
        if (this.$route.params.id) {
            this.id = this.$route.params.id
        }
    }
    
  }
</script>