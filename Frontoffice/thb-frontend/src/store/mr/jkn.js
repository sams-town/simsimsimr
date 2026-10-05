import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  perPage: 20,
  pageOptions: [20, 50, 100],
  totalRows: 1,
  sortOptions: [],
  items: [],
})

// getters
const getters = {}

// actions
const actions = {

  getJknAssessment ({ commit }, soap_id) {
		return new Promise((resolve, reject) => {
    
      let param = {status: `mr/soap/detail_jkn/${soap_id}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  getJknCost ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: `mr/soap/cost/${id}`}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  putJknAssessment ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData.data

      const param = { rawData: rawData, url: 'mr/soap/update_jkn', id: formData.soap_id }
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

  putSpecialCMG ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData.data

      const param = { rawData: rawData, url: 'mr/soap/GroupingStage2', id: formData.registration_destination_id }
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
  // setData (state, patient) {
  //   state.items = patient.rows
  //   state.totalRows = patient.rows.length
  // },
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
