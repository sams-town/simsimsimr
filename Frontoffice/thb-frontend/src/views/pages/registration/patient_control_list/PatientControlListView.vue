<template>
    <div id="app">
      <component :is="layout">
        <router-view />
      </component>
    </div>
  </template>
  
  <script lang="js">
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
      <Layout selected_menu="menu14"/>
      <section class="content px-0">
        <div class="row mx-0">
          <div class="card col px-0 border-0">
            <div class="body-header">
              <ul class="breadcrumb float-md-right rounded-pill">
                <li class="breadcrumb-item"><a href="index.html"><b-icon icon="house-fill"></b-icon></a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">Pendaftaran</a></li>
                <li class="breadcrumb-item active">Jadwal Kontrol Pasien</li>
              </ul>
              <h4>Jadwal Kontrol Pasien</h4>
            </div>
            <div class="container-fluid">
              <div class="row clearfix">
                <div class="col-lg-12 py-lg-16">
                  <control_patient />
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
  import control_patient from '@/views/layouts/_registration/patient_control_list/PatientControlList.vue';
  
  export default {
    name: 'app',
    components: {
      Layout,
      control_patient
    },
    
  }
  </script>