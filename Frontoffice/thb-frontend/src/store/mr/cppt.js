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
  getCpptList ({ commit }, params) {
    return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.registration_destination_id) {
        queryParams.push(`registration_destination_id=${params.registration_destination_id}`)
      }

      let paramsall = queryParams.join('&')
      let param = {status: `mr/cppt/list?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  getByCpptId ({ commit }, cppt_id) {
		return new Promise((resolve, reject) => {
      let param = {status: `mr/cppt/detail/${cppt_id}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  putSections ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData.data

      const fixed_url = {
          ANAMNESA: 'mr/cppt/save_anamnesis',
          VITAL: 'mr/cppt/save_ttv',
          GENERALIS: 'mr/cppt/save_st_generalist',
          DIAGNOSIS: 'mr/cppt/save_diagnose',
          PROCEDURE: 'mr/cppt/save_procedure',
          PLAN: 'mr/cppt/save_plan',
          LABORATORY: 'mr/cppt/save_laboratory_request',
          RADIOLOGY: 'mr/cppt/save_radiology_request',
          PHYSIOTHERAPY: 'mr/cppt/save_physiotherapy_request',
          PRESC: 'mr/cppt/save_prescription',
          NSDIAGNOSIS: 'mr/cppt/save_diagnose',
          NSPLAN: 'mr/cppt/save_plan',
          NUTRITION: 'mr/cppt/save_nutrition',
      };

      // // let url= ''
      // if (fixed_url[formData.exist]) {
      //     url = fixed_url[formData.exist]
      // }

      const sectionKey = formData.exist;
      const url = fixed_url[sectionKey];
      // console.log('URL for section:', sectionKey, url);

      if (!url) {
          reject(`Section "${sectionKey}" is not valid or not mapped to an endpoint.`);
          return;
      }


      const param = { rawData, url }
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

  putAllSections ({ commit }, formData) {
    return new Promise((resolve, reject) => {
      const rawData = formData

      let url= ''

      const param = { rawData: rawData, url: url }
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


  getHistoryCPPT ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.limit) {
        queryParams.push(`limit=${params.limit}`)
      }

      if (params.norm) {
        queryParams.push(`norm=${params.norm}`)
      }

      if (params.registration_id) {
        queryParams.push(`registration_id=${params.registration_id}`)
      }

      let paramsall = queryParams.join('&')
      let param = {status: `mr/cppt/history?${paramsall}`}

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

  getDiagnosisCPPTHistory ({ commit }, params) {
    return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.reg_des_id) {
        queryParams.push(`registration_destination_id=${params.reg_des_id}`)
      }

      let param = {status: `mr/cppt/last_doctor?${queryParams.join('&')}`}

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

  getListCPPT ({ commit }, params) {
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

      if (params.registration_id) {
        queryParams.push(`registration_id=${params.registration_id}`)
      }

      if (params.registration_destination_id) {
        queryParams.push(`registration_destination_id=${params.registration_destination_id}`)
      }

      if (params.user_added) {
        queryParams.push(`user_added=${params.user_added}`)
      }

      if (params.date_added) {
        queryParams.push(`date_added=${params.date_added}`)
      }

      // if (params.registration_destination_id) {
      //   queryParams.push(`registration_destination_id=${params.registration_destination_id}`)
      // }

      // if (params.doctor_in_charge_id) {
      //   queryParams.push(`doctor_in_charge_id=${params.doctor_in_charge_id}`)
      // }

      // if (params.norm) {
      //   queryParams.push(`norm=${params.norm}`)
      // }

      if (params.norm) {
        queryParams.push(`norm=${params.norm}`)
      }

      let paramsall = queryParams.join('&')
      let param = {status: `mr/cppt/list?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  postPutFullCPPT ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      let url = ''
      formData.id ? url= 'mr/cppt/update' : url= 'mr/cppt/create'

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
  verifyCPPT ({ commit }, id) {
		return new Promise((resolve, reject) => {

      const rawData = {
        status: 2
			}

      const param = { rawData: rawData, url: "mr/cppt/verify", id: id }
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
  verifyAllCPPT ({ commit }, soap_id) {
		return new Promise((resolve, reject) => {

      const rawData = {
        status: 2
			}

      const param = { rawData: rawData, url: "mr/cppt/verify_all", id: soap_id }
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
  removeCPPT ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'mr/cppt/delete', id: id }
      useIPA.delete(param)
			.then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }

        return response;
      })
      .catch(({error, response}) => {
        reject({error: response.data.message, response})
      })
		})
	},
  editCPPTdate ({ commit }, payload) {
    return new Promise((resolve, reject) => {
      const param = {
        url: `mr/cppt/update_cppt_date/${payload.cppt_id}`,
        rawData: {
          updated_at: payload.updated_at
        }
      }
      useIPA.putonly(param).then(response => {
        resolve(response.data)
      }).catch(error => {
        reject(error)
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
