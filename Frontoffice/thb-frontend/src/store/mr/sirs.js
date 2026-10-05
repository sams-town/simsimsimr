import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'
import prescription from '../pharmacy/prescription'

// initial state
const state = () => ({
  sirs: {
  }
})

// getters
const getters = {}

// actions
const actions = {
  getFilter ({ commit }, params) {
    return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.date_start) {
        queryParams.push(`date_start=${params.date_start}`)
      }

      if (params.date_end) {
        queryParams.push(`date_end=${params.date_end}`)
      }

      if (params.type) {
        queryParams.push(`type=${params.type}`)
      }
    
      let paramsall = queryParams.join('&')
      let param = {status: `report/sirs/rl/filter?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  // putSections ({ commit }, formData) {
	// 	return new Promise((resolve, reject) => {
  //     const rawData = formData.data

  //     const fixed_url = {
  //         ANAMNESA: 'mr/soap/save_anamnesis',
  //         VITAL: 'mr/soap/save_ttv_antropometri',
  //         GENERALIS: 'mr/soap/save_st_generalist',
  //         DIAGNOSIS: 'mr/soap/save_diagnose',
  //         PROCEDURE: 'mr/soap/save_procedure',
  //         LABORATORY: 'mr/soap/save_laboratory_request',
  //         RADIOLOGY: 'mr/soap/save_radiology_request',
  //         PHYSIOTHERAPY: 'mr/soap/save_physiotherapy_request',
  //         PRESC: 'mr/soap/save_prescription',
  //         DISCHARGE: 'mr/soap/save_discharge_planning',
  //         HEMODIALYSIS: 'mr/soap/save_hemodialysis_initiation',
  //         REHABILITATION: 'mr/soap/save_medical_rehabilitation',
  //         CHECKUP: 'mr/soap/save_medical_check_up',
  //     };

  //     let url= ''
  //     if (fixed_url[formData.exist]) {
  //         url = fixed_url[formData.exist]
  //     }

  //     const param = { rawData: rawData, url: url }
  //     useIPA.putonly(param)
  //     .then(response => {
  //       if (response.data.hasErrors) {
  //         reject(response.data.message)
  //       } else {
  //         resolve(response.data)
  //       }
  //       return response;
  //     })
  //     .catch(({response}) => {
  //       reject({message: response.data.message, debug: response.data.debug, response: response.data,})
  //     })
	// 	})
	// },
}

// mutations
const mutations = {
  setData (state, patient) {
    state.items = patient.rows
    state.totalRows = patient.rows.length
  },
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
