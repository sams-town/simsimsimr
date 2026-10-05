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
  postResumePreCreate ({ commit }, formData) {
		return new Promise((resolve, reject) => {

      const param = { rawData: formData, url: 'mr/resume/pre_create', id: '' }
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
  
  postPutResume ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      const url = formData.id ? 'mr/resume/update' : 'mr/resume/create'

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
          reject(error.response.data.debug)
        } else {
          reject(error.debug)
        }
      })
		})
	},

  getAllList ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.registration_destination_id) {
        queryParams.push(`registration_destination_id=${params.registration_destination_id}`)
      }

      if (params.norm) {
        queryParams.push(`norm=${params.norm}`)
      }

      if (params.limit) {
        queryParams.push(`limit=${params.limit}`)
      }
    
      let paramsall = queryParams.join('&')
      let param = {status: `mr/resume/list?${paramsall}`}

      useIPA.get(param)
      .then(response => {
        resolve(response.data.data)
        return response;
      })
      .catch(({response}) => {
        reject({
          message: response.data.message, 
          debug: response.data.debug, 
          response: response.data,
        })
      })
    })
  },

  getDetail ({ commit }, resume_id) {
		return new Promise((resolve, reject) => {
    
      let param = {status: `mr/resume/detail/${resume_id}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
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
