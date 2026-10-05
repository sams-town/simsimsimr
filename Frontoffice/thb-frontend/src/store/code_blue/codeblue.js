import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({})

// getters
const getters = {}

// actions
const actions = {
    formPostPut ({ commit }, formData) {
        return new Promise((resolve, reject) => {
            const rawData = {
                bed_id: formData.bed_id,
            }
            let url = 'codeblue/create'
            const param = { rawData: formData, url: url, id: '' }
            useIPA.postput(param)
            .then(response => {
                if (response.data.hasErrors) {
                    reject(response.data.message)
                } else {
                    resolve(response.data)
                }
            },
            error => {
                if (error.response.data.hasOwnProperty('hasErrors')) {
                    reject(error.response.data.message)
                } else {
                    reject(error.message)
                }
            })
        })
    },
}

// mutations
const mutations = {
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
