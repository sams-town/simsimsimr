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
  putSaveFull ({ commit }, formData) {
    return new Promise((resolve, reject) => {
      const rawData = formData
      const param = { rawData: rawData, url: 'mr/sbar/save_full' }
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

  getBySBARId ({ commit }, sbar_id) {
		return new Promise((resolve, reject) => {
      let param = {status: `mr/sbar/detail/${sbar_id}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  putSections ({ commit }, formData) {
    return new Promise((resolve, reject) => {
      const rawData = formData.data

      const fixed_url = {
          VERIFICATION: 'mr/sbar/save_verification',
          SBAR: 'mr/sbar/save_sbar',
          LABORATORY: 'mr/sbar/save_laboratory_request',
          RADIOLOGY: 'mr/sbar/save_radiology_request',
          PHYSIOTHERAPY: 'mr/sbar/save_physiotherapy_request',
          PRESC: 'mr/sbar/save_prescription',
      };

      let url= ''
      if (fixed_url[formData.exist]) {
          url = fixed_url[formData.exist]
      }

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

  postPutSBAR ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      let url = ''
      formData.id ? url= 'mr/sbar/update' : url= 'mr/sbar/create'

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
  getListSBAR ({ commit }, params) {
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
        queryParams.push(`registration_destination_id=${params.registration_destination_id}`)
      }

      if (params.doctor_in_charge_id) {
        queryParams.push(`doctor_in_charge_id=${params.doctor_in_charge_id}`)
      }
    
      let paramsall = queryParams.join('&')
      let param = {status: `mr/sbar/list?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data.rows)
      })
    })
  },
  removeSBAR ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'mr/sbar/delete/', id: id }
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

  verifySBAR ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const { sbar_id, ...rawData } = formData;  // Destructure and exclude `sbar_id`
      const param = { rawData: rawData, url: "mr/sbar/verify", id: sbar_id }
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

  declineSBAR ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const { sbar_id, ...rawData } = formData; 
      const param = { rawData: rawData, url: "mr/sbar/decline", id: sbar_id }
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

  validateSBAR ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const { sbar_id, ...rawData } = formData; 
      const param = { rawData: rawData, url: "mr/sbar/validate", id: sbar_id }
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

  verifyAllSBAR ({ commit }, reg_des_id) {
		return new Promise((resolve, reject) => {
      
      const rawData = {
        status: 2
			}

      const param = { rawData: rawData, url: "mr/sbar/verify_all", id: reg_des_id }
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
