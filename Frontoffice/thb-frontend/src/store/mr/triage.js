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
  formPostPutV2 ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      console.log(rawData)

      let url = ''
      if (formData.id) {
        url = 'mr/emergency/triage_new/update'
      } else {
        url = 'mr/emergency/triage_new/create'
      }
      const param = { rawData: rawData, url: url, id: formData.id }
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

  getDetailByTriageIdV2 ({ commit }, triage_id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'mr/emergency/triage_new/detail/' + triage_id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  getAllListV2 ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.registration_destination_id) {
        queryParams.push(`registration_destination_id=${params.registration_destination_id}`)
      }
    
      let paramsall = queryParams.join('&')
      let param = {status: `mr/emergency/triage_new/list?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data.rows)
      })
    })
  },

  removeTriageV2 ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'mr/emergency/triage_new/delete', id: id }
      useIPA.delete(param)
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


  formPostPut ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      console.log(rawData)

      let url = ''
      if (formData.id) {
        url = 'mr/triage/update'
      } else {
        url = 'mr/triage/create'
      }
      const param = { rawData: rawData, url: url, id: formData.id }
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

  getByRegDesId ({ commit }, regDesId) {
		return new Promise((resolve, reject) => {
      let param = {status: 'mr/triage/detail?registration_destination_id=' + regDesId}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  getAllList ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.registration_destination_id) {
        queryParams.push(`registration_destination_id=${params.registration_destination_id}`)
      }
    
      let paramsall = queryParams.join('&')
      let param = {status: `mr/triage/list?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data.rows)
      })
    })
  },

  removeTriage ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'mr/triage/delete', id: id }
      useIPA.delete(param)
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
	}
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
