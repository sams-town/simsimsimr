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
  <QueuePharmacy />
</template>
<script>
import Layout from '@/@core/light/Layout.vue';
import QueuePharmacy from '@/views/layouts/queue/hf/QueuePharmacy.vue';

export default {
  name: 'app',
  components: {
    Layout,
    QueuePharmacy
  },
  
}
</script>