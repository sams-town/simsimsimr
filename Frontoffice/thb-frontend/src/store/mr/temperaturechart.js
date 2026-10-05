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
  getListTemperature ({ commit }, params) {
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
      let param = {status: `mr/inpatient/temperature_chart/list?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  getListTemperatureV2 ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.date_start) {
        queryParams.push(`date_start=${params.date_start}`);
      }

      if (params.date_end) {
        queryParams.push(`date_end=${params.date_end}`);
      }

      if (params.registration_destination_id) {
        queryParams.push(`registration_destination_id=${params.registration_destination_id}`);
      }
    
      let paramsall = queryParams.join('&')
      let param = {status: `mr/inpatient/temperature_chart/list_v2?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  postPutTemperature ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      let url = formData.id ? 'mr/inpatient/temperature_chart/update' : 'mr/inpatient/temperature_chart/create'

      const param = { rawData: rawData, url: url, id: formData.id }
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

  deleteTemperature ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'mr/inpatient/temperature_chart/delete', id: id }
      useIPA.delete(param)
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

  postPutItemTemperature ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      let url = formData.id ? 'mr/inpatient/temperature_chart/update_item' : 'mr/inpatient/temperature_chart/create_item'

      const param = { rawData: rawData, url: url, id: formData.id }
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

  deleteItemTemperature ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'mr/inpatient/temperature_chart/delete_item', id: id }
      useIPA.delete(param)
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
