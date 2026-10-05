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
  <QueueLaboratory />
</template>
<script>
import Layout from '@/@core/light/Layout.vue';
import QueueLaboratory from '@/views/layouts/queue/hf/QueueLaboratory.vue';

export default {
  name: 'app',
  components: {
    Layout,
    QueueLaboratory
  },
  
}
</script>