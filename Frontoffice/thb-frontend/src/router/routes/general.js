export default [
  {
    path: '/error-404',
    name: 'error-404',
    component: () => import('@/views/error/Error404.vue'),
    meta: {
      layout: 'full'
    }
  },
  {
    path: '/dev-progress',
    name: 'DevProgress',
    component: () => import('@/views/pages/general/DevProgressView.vue'),
    meta: {
      layout: 'full'
    }
  }
]
