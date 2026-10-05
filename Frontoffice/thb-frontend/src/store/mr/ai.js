import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
})

// getters
const getters = {}

// actions
const actions = {
    formPostPut ({ commit }, formData) {
        return new Promise((resolve, reject) => {
            const rawData = formData
            console.log(rawData)
            // const param = { rawData: rawData, url: 'xx', id: formData.id }
            const param = { rawData: rawData, url: '/transcri   pts/upload', id: formData.id }
            useIPA.postput(param)
                .then(response => {
                // router.push({ name: 'RegisMasterView' })
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
  // formPostPut ({ commit }, formData) {
    // 	return new Promise((resolve, reject) => {
  //     const rawData = new FormData()
  //     rawData.set('name', formData.text_1)
  //     rawData.set('desc', formData.text_2)
  //     if (formData.setImage === 'upload') {
  //       rawData.append('icon', formData.icon)
    // 		} else {
  //       rawData.set('icon', formData.icon)
    // 		}
  //     const param = { rawData: rawData, url: 'backoffice/master/bank', id: formData.id}
  //     useIPA.postput(param)
    // 		.then(response => {
  //       router.push({ name: 'app-bank' })
  //       if (response.data.hasErrors) {
  //         reject(response.data.message)
  //       } else {
  //         resolve(response.data)
  //       }
  //     },
  //     error => {
  //       if (error.response.data.hasOwnProperty('hasErrors')) {
  //         reject(error.response.data.message)
  //       } else {
  //         reject(error.message)
  //       }
  //     })
    // 	})
    // },
}

// mutations
const mutations = {}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
