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
      <DisplayMachine />
    </div>
  </template>
  <script>
  import Layout from '@/@core/light/Layout.vue';
  import DisplayMachine from '@/views/layouts/queue/bridgemachine/DisplayMachine.vue';
  
  export default {
    name: 'app',
    components: {
      Layout,
      DisplayMachine
    },
    
  }
  </script>