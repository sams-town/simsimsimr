<template>
  <div>
    <Layout
      :layout_breadcrumb_items="[
        { text: 'Home', link: '/' },
        { text: 'OK', link: '/operatie-kamer' }
      ]"
    >
    <section class="">
      <OperatieKamer />
    </section>
    </Layout>
  </div>
</template>

<script>
import OperatieKamer from '@/views/layouts/operatie/OperatieKamer.vue'

export default {
  name: 'OperatieKamerView',
  components: {
    OperatieKamer
  }
}
</script>