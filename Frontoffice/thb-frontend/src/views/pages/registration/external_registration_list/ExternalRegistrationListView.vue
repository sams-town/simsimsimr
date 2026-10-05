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
      <Layout selected_menu="menu2"/>
      <section class="content px-0">
        <div class="row mx-0">
          <div class="card col px-0">
            <div class="body-header">
              <ul class="breadcrumb float-md-right rounded-pill">
                <li class="breadcrumb-item"><a href="index.html"><b-icon icon="house-fill"></b-icon></a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">Sample Pages</a></li>
                <li class="breadcrumb-item active">Stater Page</li>
              </ul>
              <h4>List Pendaftaran Pasien Eksternal</h4>
            </div>
            <div class="container-fluid">
              <div class="row clearfix">
                <div class="col-lg-12 py-lg-16">
                  <ExternalRegistrationList />
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
  import ExternalRegistrationList from '@/views/layouts/_registration/external_registration_list/ExternalRegistrationList.vue';
  
  export default {
    name: 'app',
    components: {
      Layout,
      ExternalRegistrationList
    },
    
  }
  </script>