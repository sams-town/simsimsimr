<template>
  <div
    class="app-content content"
    :class="[{'show-overlay': $store.state.app.shallShowOverlay}, $route.meta.contentClass]"
  >
    <div class="content-overlay" />
    <div class="header-navbar-shadow" />
    <div
      class="content-wrapper"
      :class="contentWidth === 'boxed' ? 'container p-0' : null"
    >
      <!-- <slot name="breadcrumb">
        <app-breadcrumb />
      </slot> -->
      <div class="content-body">
        <slot />
        <!-- <transition
          :name="routerTransition"
          mode="out-in"
        >
          <slot />
        </transition> -->
      </div>
    </div>
  </div>
</template>

<script>
// import AppBreadcrumb from '@core/layouts/components/AppBreadcrumb.vue'
import useAppConfigMixin from '@/mixins/useAppConfigMixin'

export default {
  components: {
    // AppBreadcrumb,
  },
  mixins: [useAppConfigMixin],
  mounted() {

  },
}
</script>

<style>

</style>
