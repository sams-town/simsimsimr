import Vue from 'vue'

// axios
import axios from 'axios'

const axiosIns = axios.create({
  baseURL: process.env.VUE_APP_ROOT_API,
})

Vue.prototype.$http = axiosIns

export default axiosIns
