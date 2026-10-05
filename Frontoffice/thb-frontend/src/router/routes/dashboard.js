export default [
  {
    path: '/dashboard',
    name: 'DashboardView',
    component: () => import('@/views/pages/registration/Dashboard.vue')
  },
  {
    path: '/dashboard/checkout-monitoring',
    name: 'DashboardCheckoutMonitoringView',
    component: () => import('@/views/pages/registration/DashboardCheckoutMonitoring.vue')
  },
  {
    path: '/dashboard/balancing',
    name: 'BalancingView',
    component: () => import('@/views/pages/DashboardBalancing.vue')
  },
  {
    path: '/dashboard/executive',
    name: 'ExecutiveView',
    component: () => import('@/views/pages/dashboard/Executive.vue')
  }
]
