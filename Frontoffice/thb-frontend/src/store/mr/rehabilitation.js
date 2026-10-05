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

  getMedicalList ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.offset) {
        queryParams.push(`offset=${params.offset}`);
      } else if (params.offset === 0) {
        queryParams.push(`offset=${params.offset}`);
      }

      if (params.limit) {
        queryParams.push(`limit=${params.limit}`);
      } else if (params.limit === 0) {
        queryParams.push(`limit=${params.limit}`);
      }

      if (params.registration_destination_id) {
        queryParams.push(`registration_destination_id=${params.registration_destination_id}`);
      }
    
      let paramsall = queryParams.join('&')
      let param = {status: `mr/medical_rehabilitation/list?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  postPutMedical ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      let url = formData.id ? 'mr/medical_rehabilitation/update' : 'mr/medical_rehabilitation/create'

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
      .catch(({error, response}) => {
        reject({error: response.data.message, response})
      })
		})
	},

  putResult ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData

      const param = { rawData: rawData, url: 'mr/medical_rehabilitation/update_result', id: formData.medical_id }
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
      .catch(({error, response}) => {
        reject({error: response.data.message, response})
      })
		})
	},

  deleteMedical ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'mr/medical_rehabilitation/delete', id: id }
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
      .catch(({error, response}) => {
        reject({error: response.data.message, response})
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
