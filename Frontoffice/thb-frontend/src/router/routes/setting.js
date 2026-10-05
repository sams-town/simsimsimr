export default [
  {
    path: '/setting/api-credentials',
    name: 'settingApiCredentials',
    component: () => import('@/views/setting/ApiCredentials.vue'),
    meta: {
      layout: 'full'
    }
  },
]