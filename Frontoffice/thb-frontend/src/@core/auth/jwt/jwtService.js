import jwtDefaultConfig from './jwtDefaultConfig'
import { initialAbility } from '@/libs/acl/config'
import useJwt from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

export default class JwtService {
  // Will be used by this service for making API calls
  axiosIns = null

  // jwtConfig <= Will be used by this service
  jwtConfig = { ...jwtDefaultConfig }

  // For Refreshing Token
  isAlreadyFetchingAccessToken = false

  // For Refreshing Token
  subscribers = []

  constructor(axiosIns, jwtOverrideConfig) {
    this.axiosIns = axiosIns
    this.jwtConfig = { ...this.jwtConfig, ...jwtOverrideConfig }

    // Request Interceptor
    this.axiosIns.interceptors.request.use(
      config => {
        // Get token from localStorage
        const accessToken = this.getToken()

        // If token is present add it to request's Authorization Header
        if (accessToken) {
          // eslint-disable-next-line no-param-reassign
          config.headers.Authorization = `${this.jwtConfig.tokenType} ${accessToken}`
        }
        return config
      },
      error => Promise.reject(error),
    )

    // Add request/response interceptor
    this.axiosIns.interceptors.response.use(
      response => response,
      error => {
        // const { config, response: { status } } = error
        const { config, response } = error
        const originalRequest = config

        // if (status === 401) {
        if (response && response.status === 401) {
          // Remove userData from localStorage
          // ? You just removed token from localStorage. If you like, you can also make API call to backend to blacklist used token
          localStorage.removeItem(this.jwtConfig.storageTokenKeyName)
          localStorage.removeItem(this.jwtConfig.storageRefreshTokenKeyName)
          // Remove userData from localStorage
          localStorage.removeItem('userData')
          // Redirect to login page
          router.push({ name: 'login' })
          if (!this.isAlreadyFetchingAccessToken) {
            this.isAlreadyFetchingAccessToken = true
            this.refreshToken().then(r => {
              this.isAlreadyFetchingAccessToken = false

              // Update accessToken in localStorage
              this.setToken(r.data.accessToken)
              this.setRefreshToken(r.data.refreshToken)

              this.onAccessTokenFetched(r.data.accessToken)
            })
          }
          const retryOriginalRequest = new Promise(resolve => {
            this.addSubscriber(accessToken => {
              // Make sure to assign accessToken according to your response.
              // Check: https://pixinvent.ticksy.com/ticket/2413870
              // Change Authorization header
              originalRequest.headers.Authorization = `${this.jwtConfig.tokenType} ${accessToken}`
              resolve(this.axiosIns(originalRequest))
            })
          })
          return retryOriginalRequest
        }
        return Promise.reject(error)
      },
    )
  }

  onAccessTokenFetched(accessToken) {
    this.subscribers = this.subscribers.filter(callback => callback(accessToken))
  }

  addSubscriber(callback) {
    this.subscribers.push(callback)
  }

  getToken() {
    return localStorage.getItem(this.jwtConfig.storageTokenKeyName)
  }

  getRefreshToken() {
    return localStorage.getItem(this.jwtConfig.storageRefreshTokenKeyName)
  }

  setToken(value) {
    localStorage.setItem(this.jwtConfig.storageTokenKeyName, value)
  }

  setRefreshToken(value) {
    localStorage.setItem(this.jwtConfig.storageRefreshTokenKeyName, value)
  }

  async login(...args) {
    return await this.axiosIns.post(this.jwtConfig.loginEndpoint, ...args)
  }

  getProfile (...args) {
    return this.axiosIns.get(this.jwtConfig.getProfileEndpoint, ...args)
  }

  getPrivilege (...args) {
    return this.axiosIns.get(this.jwtConfig.getPrivilegeEndpoint, ...args)
  }

  getUnverified (...args) {
    return this.axiosIns.get(this.jwtConfig.getUnverifiedEndpoint, ...args)
  }

  register(...args) {
    return this.axiosIns.post(this.jwtConfig.registerEndpoint, ...args)
  }

  refreshToken() {
    // return this.axiosIns.post(this.jwtConfig.refreshEndpoint, {
    //   refreshToken: this.getRefreshToken(),
    // })
    // Remove userData from localStorage
    // ? You just removed token from localStorage. If you like, you can also make API call to backend to blacklist used token
    localStorage.removeItem(useJwt.jwtConfig.storageTokenKeyName)
    localStorage.removeItem(useJwt.jwtConfig.storageRefreshTokenKeyName)

    // Remove userData from localStorage
    localStorage.removeItem('userData')

    // Redirect to login page
    router.push({ name: 'login' })

    // Reset ability
    this.$ability.update(initialAbility)
  }

  getByPath (...args) {
    let param = args[0]
    const url = param.url + param.status;
    return this.axiosIns.get(url, ...args);
  }

  get (...args) {
    let param = args[0]
    return this.axiosIns.get(this.jwtConfig.Endpoint + param.status, ...args)
  }

  getGlobe (...args) {
    let param = args[0]
    return this.axiosIns.get(param.status, ...args)
  }
  
  postput (...args) {
    const param = args[0]
    if (param.id === "") {
      return this.axiosIns.post(param.url, param.rawData)
    } else {
      return this.axiosIns.put(param.url + '/' + param.id, param.rawData)
    }
  }

  putonly (...args) {
    const param = args[0]
    return this.axiosIns.put(param.url, param.rawData)
  }

  postid (...args) {
    const param = args[0]
    return this.axiosIns.post(param.url + '/' + param.id, param.rawData)
  }

  postputnoid (...args) {
    const param = args[0]
    if (param.id === null) {
      return this.axiosIns.put(param.url, param.rawData)
    } else {
      return this.axiosIns.post(param.url, param.rawData)
    }
  }

  patchpassword (...args) {
    const param = args[0]
    return this.axiosIns.patch(param.url, param.rawData)
  }

  delete (...args) {
    const param = args[0]
    return this.axiosIns.delete(param.url + '/' + param.id, {data: param.rawData})
  }
}
