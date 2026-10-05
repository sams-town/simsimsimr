export default [
  {
    path: '/signup',
    name: 'SignupView',
    component: () => import('@/views/pages/auth/signUp1.vue'),
    meta: {
      layout: 'full',
      resource: 'Auth',
      redirectIfLoggedIn: false
    }
  },
  {
    path: '/verify-email',
    name: 'login3',
    component: () => import('@/views/pages/auth/emailVerify.vue')
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/pages/auth/login.vue'),
    meta: {
      layout: 'full',
      resource: 'Auth',
      redirectIfLoggedIn: true
    }
  }
]
