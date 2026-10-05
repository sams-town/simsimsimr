export default [
  {
    path: '/notifications',
    name: 'notifications',
    component: () => import('@/views/notification/AllNotifications.vue'),
    meta: {
      layout: 'full'
    }
  },
]