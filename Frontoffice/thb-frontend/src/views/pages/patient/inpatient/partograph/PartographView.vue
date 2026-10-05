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
                            <li class="breadcrumb-item active">Partograf</li>
                        </ul>
                        <h4>Partograf</h4>
                    </div>
                    <div class="container-fluid">
                        <div class="row clearfix">
                            <div class="col-lg-12 py-lg-16">
                                <Partograph />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- <patientMenu selectedMenu="menu22" /> -->
        <section class="card mt-32">
            <div class="card-header bg-indigo-200">
                <span class="text-bold-18">Data Dasar</span>
            </div>
            <!-- <div class="card-body">
                <div class="row" style="font-size: 14px">
                    <div class="col-lg-4">
                        <div class="font-weight-bold">Nama</div>
                        <div>{{ regData.name }} ({{ parsed_age(regData.birth_date) }})</div>
                        <div>{{ regData.room_name }} ({{ regData.room_name }})</div>
                    </div>
                    <div class="col-lg-4">
                        <div class="font-weight-bold">Tanggal Lahir</div>
                        <div>{{ localDate(regData.birth_date) }}</div>
                    </div>
                    <div class="col-lg-4">
                        <div class="font-weight-bold">No RM</div>
                        <div>{{ regData.norm }}</div>
                    </div>
                </div>
            </div> -->
        </section>

    </div>
</template>
<script>
import Layout from '@/@core/light/Layout.vue';
import Partograph from '@/views/layouts/patient/inpatient/partograph/Partograph.vue';
  
  export default {
    name: 'PartographView',
    components: {
      Layout, Partograph
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