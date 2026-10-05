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
    putDoneConsultation ({ commit }, formData) {
      return new Promise((resolve, reject) => {
        const rawData = formData.data

        const param = { rawData: rawData, url: `mr/inpatient/consultation/done/${formData.consultation_id}` }
        useIPA.putonly(param)
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

    putShiftConsultation ({ commit }, formData) {
      return new Promise((resolve, reject) => {
        const rawData = formData.data

        const param = { rawData: rawData, url: `mr/inpatient/consultation/shift/${formData.consultation_id}` }
        useIPA.putonly(param)
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

    getListConsultation ({ commit }, params) {
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
        let param = {status: `mr/inpatient/consultation/list?${paramsall}`}

        useIPA.get(param).then(response => {
          resolve(response.data.data)
        })
      })
    },

    postPutConsultation ({ commit }, formData) {
      return new Promise((resolve, reject) => {
        const rawData = formData
        let url = formData.id ? 'mr/inpatient/consultation/update/' : 'mr/inpatient/consultation/create'

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

    cancelConsultation ({ commit }, id) {
      return new Promise((resolve, reject) => {
        const rawData = {
          is_cancel: 1,
        }

        const param = { rawData: rawData, url: `mr/inpatient/consultation/cancel`, id: id }
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
