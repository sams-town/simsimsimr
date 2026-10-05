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
                            <li class="breadcrumb-item active">Balance Cairan</li>
                        </ul>
                        <h4>Balance Cairan</h4>
                    </div>
                    <div class="container-fluid">
                        <div class="row clearfix">
                            <div class="col-lg-12 py-lg-16">
                                <FluidBalance />
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
import FluidBalance from '@/views/layouts/patient/inpatient/fluid_balance/FluidBalance.vue';
  
  export default {
    name: 'FluidBalanceView',
    components: {
      Layout, FluidBalance
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