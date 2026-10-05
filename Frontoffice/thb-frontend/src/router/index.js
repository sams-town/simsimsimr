import Vue from 'vue'
import VueRouter from 'vue-router'

// Routes
import { canNavigate } from '@/libs/acl/routeProtection'
import { isUserLoggedIn, getUserData, getHomeRouteForLoggedInUser } from '@/auth/utils'
import general from './routes/general'
import authentication from './routes/authentication'
import dashboard from './routes/dashboard'
import backoffice from './routes/backoffice'
import mr from './routes/mr'
import payment from './routes/payment'
import ambulance from './routes/ambulance'
import nutrition from './routes/nutrition'
import bpjs from './routes/bpjs'
import laboratory from './routes/laboratory'
import radiology from './routes/radiology'
import inpatient from './routes/inpatient'
import pharmacy from './routes/pharmacy'
import logistic from './routes/logistic'
import rates from './routes/rates'
import coding from './routes/coding'
import physiotherapy from './routes/physiotherapy'
import queue from './routes/queue'
import all_master from './routes/all_master'
import all_prints from './routes/all_prints'
import linen from './routes/linen'
import modules from './routes/modules'
import cssd from './routes/cssd'
import operatie from './routes/operatie'
import sirs from './routes/sirs'
import reference from './routes/reference'
import notification from './routes/notification'
import code_blue from './routes/code_blue'
import satusehat from './routes/satusehat'
import setting from './routes/setting'

Vue.use(VueRouter)

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  scrollBehavior() {
    return { x: 0, y: 0 }
  },
  routes: [
    { path: '/', redirect: { name: 'DashboardView' } },
    ...general,
    ...authentication,
    ...dashboard,
    ...backoffice,
    ...mr,
    ...payment,
    ...ambulance,
    ...nutrition,
    ...bpjs,
    ...laboratory,
    ...radiology,
    ...inpatient,
    ...pharmacy,
    ...logistic,
    ...rates,
    ...coding,
    ...physiotherapy,
    ...queue,
    ...all_master,
    ...all_prints,
    ...linen,
    ...modules,
    ...cssd,
    ...operatie,
    ...sirs,
    ...reference,
    ...notification,
    ...code_blue,
    ...satusehat,
    ...setting,
    {
      path: '*',
      redirect: 'error-404'
    }
  ]
})



router.beforeEach((to, _, next) => {
  const isLoggedIn = isUserLoggedIn()

  if (!canNavigate(to)) {
    // Redirect to login if not logged in
    if (!isLoggedIn) return next({ name: 'login' })

    // If logged in => not authorized
    // return next({ name: 'error-404' })
  }

  // Redirect if logged in
  const lastURL = localStorage.getItem("lastVisitedURL");
  if (to.meta.redirectIfLoggedIn && isLoggedIn) {
    const userData = getUserData()
    next(getHomeRouteForLoggedInUser(userData ? userData.role : null, lastURL || '/'))
  }

  return next()
})

router.afterEach(() => {
  // Remove initial loading
  const appLoading = document.getElementById('loading-bg')
  if (appLoading) {
    appLoading.style.display = 'none'
  }
})

export default router