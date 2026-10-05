<template>
  <div
    class="vertical-layout h-100"
    :class="layoutClasses"
    :data-col="isNavMenuHidden ? '1-column' : null"
  >
    <b-navbar
      :toggleable="false"
      :variant="navbarBackgroundColor"
      class="header-navbar navbar shadow align-items-center"
      :class="[navbarTypeClass]"
    >
      <slot
        name="navbar"
        :toggleVerticalMenuActive="toggleVerticalMenuActive"
        :navbarBackgroundColor="navbarBackgroundColor"
        :navbarTypeClass="[...navbarTypeClass, 'header-navbar navbar navbar-shadow align-items-center']"
      >
        <NavBar 
          :breadcrumb_items="layout_breadcrumb_items" 
          :toggle-vertical-menu-active="toggleVerticalMenuActive"
        />
      </slot>
    </b-navbar>

    <SideBar
      v-if="!isNavMenuHidden"
      :is-vertical-menu-active="isVerticalMenuActive"
      :toggle-vertical-menu-active="toggleVerticalMenuActive"
      :selected_sidebar="selected_menu"
      :selected_sidebar_submenu="selected_submenu"
    >
      <template #header="slotProps">
        <slot
          name="vertical-menu-header"
          v-bind="slotProps"
        />
      </template>
    </SideBar>

    <!-- Vertical Nav Menu Overlay -->
    <div
      class="sidenav-overlay"
      :class="overlayClasses"
      @click="isVerticalMenuActive = false"
    />
    <!-- /Vertical Nav Menu Overlay -->

    <transition
      :name="routerTransition"
      mode="out-in"
    >
      <component
        :is="layoutContentRenderer"
        :key="layoutContentRenderer === 'layout-content-renderer-left' ? $route.meta.navActiveLink || $route.name : null"
      >
        <template
          v-for="(index, name) in $scopedSlots"
          v-slot:[name]="data"
        >
          <slot
            :name="name"
            v-bind="data"
          />
        </template>
      </component>
    </transition>
  </div>
</template>

<script>
import NavBar from '../light/NavBar.vue'
import SideBar from '../light/SideBar.vue'
import { BNavbar } from 'bootstrap-vue'
import mixinVerticalLayout from '@core/layouts/layout-vertical/mixinVerticalLayout'
import useVerticalLayout from '@/mixins/useVerticalLayoutMixin'
import useAppConfigMixin from '@/mixins/useAppConfigMixin'
import LayoutContentRendererDefault from '@core/layouts/components/layout-content-renderer/LayoutContentRendererDefault.vue'

export default {
  name: 'VerticalLayout',
  components: {
    BNavbar,
    NavBar,
    SideBar,
    LayoutContentRendererDefault,
  },
  mixins: [mixinVerticalLayout,useVerticalLayout,useAppConfigMixin],
  computed: {
    layoutContentRenderer() {
      return 'layout-content-renderer-default'
    },
  },
  props: {
    selected_menu: {
      type: String,
      default: 'menu1',
    },
    selected_submenu: {
      type: String,
      default: 'submenu1',
    },
    layout_breadcrumb_items: {
      type: Array,
      default: () => [],
    },
  },
  methods: {},
  mounted() {
    this.resizeHandler()
    window.addEventListener('resize', this.resizeHandler)
  },

  beforeDestroy() {
    window.removeEventListener('resize', this.resizeHandler)
  },
}
</script>

<style scoped>
@import '../assets/plugins/bootstrap/css/bootstrap.min.css';
@import '../css/main/main.min.css';
@import '../css/layout.css';
</style>


