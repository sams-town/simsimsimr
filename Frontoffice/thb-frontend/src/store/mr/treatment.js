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
  getTreatmentList ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.registration_destination_id) {
        queryParams.push(`registration_destination_id=${params.registration_destination_id}`)
      }
    
      let paramsall = queryParams.join('&')
      let param = {status: `mr/treatment_room/list?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  postTransfer ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData

      const param = { rawData: rawData, url: 'mr/treatment_room/transfer', id: '' }
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

  postNewTransfer ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData

      const param = { rawData: rawData, url: 'mr/treatment_room/transfer', id: '' }
      useIPA.postput(param)
      .then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
        return response;
      })
      .catch(({response}) => {
        reject({message: response.data.message, debug: response.data.debug, response: response.data,})
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
