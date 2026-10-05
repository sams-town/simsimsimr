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
  <Layout />
  <section class="content">
    <div class="row">
      <div class="card">
        <div class="body-header">
          <ul class="breadcrumb float-md-right rounded-pill">
            <li class="breadcrumb-item"><a href="index.html"><b-icon icon="house-fill"></b-icon></a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0);">Sample Pages</a></li>
            <li class="breadcrumb-item active">Stater Page</li>
          </ul>                
          <h4>Welcome</h4>
        </div>
        <div class="container-fluid">
          <div class="row clearfix">
            <div class="col-lg-12">
              <div class="card">
                <div class="body">
                  <h4>Stater page</h4>
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
<script>
import Layout from '@/@core/light/Layout.vue';
export default {
  name: 'app',
  components: {
    Layout
  }
}
</script>