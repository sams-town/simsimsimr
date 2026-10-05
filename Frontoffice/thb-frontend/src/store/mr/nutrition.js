import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  perPage: 20,
  pageOptions: [20, 50, 100],
  totalRows: 1,
  sortOptions: [],
  items: [],
  optionsDiet: [],
  nutrition: {
    items: [],
    totalRows: 1,
  }
})

// getters
const getters = {}

// actions
const actions = {
  getListDiet ({ commit }, params) {
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

      if (params.code) {
        queryParams.push(`code=${params.code}`);
      }

      if (params.name) {
        queryParams.push(`name=${params.name}`);
      }
    
      let paramsall = queryParams.join('&')
      let param = {status: `mr/diet/list?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  postPutDiet ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      let url = formData.id ? 'mr/diet/update' : 'mr/diet/create'

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

  deleteDiet ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'mr/diet/delete', id: id }
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

  getParamDiet({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `mr/diet/list/`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data.rows
        }
        commit('setParamDiet', {...Param})
      })
    })
  },

  postPutNutrition ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      let url = formData.id ? 'mr/inpatient/nutrition/update' : 'mr/inpatient/nutrition/create'

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

  getNutritionStateList ({ commit }, params) {
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

    if (params.norm) {
      queryParams.push(`norm=${params.norm}`);
    }

    if (params.registration_id) {
      queryParams.push(`registration_id=${params.registration_id}`);
    }

    if (params.registration_destination_id) {
      queryParams.push(`registration_destination_id=${params.registration_destination_id}`);
    }

    let paramsall = queryParams.join('&')
    let param = {status: `mr/inpatient/nutrition/list?${paramsall}`}

    useIPA.get(param).then(response => {
      const data = response.data.data
      commit('setNutritionData', {...data})
    })
  },

  getListNutrition ({ commit }, params) {
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

      if (params.patient_name) {
        queryParams.push(`name=${params.patient_name}`);
      }

      if (params.norm) {
        queryParams.push(`norm=${params.norm}`);
      }

      if (params.date) {
        queryParams.push(`date=${params.date}`);
      }

      if (params.date_start) {
        queryParams.push(`date_start=${params.date_start}`);
      }

      if (params.date_end) {
        queryParams.push(`date_end=${params.date_end}`);
      }

      if (params.diet_id) {
        queryParams.push(`diet_id=${params.diet_id}`);
      }

      if (params.type_room_id) {
        queryParams.push(`type_room_id=${params.type_room_id}`);
      }

      if (params.room_id) {
        queryParams.push(`room_id=${params.room_id}`);
      }

      if (params.time_type_id) {
        queryParams.push(`time_type_id=${params.time_type_id}`);
      }
    
      let paramsall = queryParams.join('&')
      let param = {status: `mr/inpatient/nutrition/list?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  deleteNutrition ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'mr/inpatient/nutrition/delete', id: id }
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

  // putApproveNutrition ({ commit }, formData) {
	// 	return new Promise((resolve, reject) => {
  //     const rawData = formData

  //     const param = { rawData: rawData, url: `mr/inpatient/nutrition/approve/`, id: formData.id }
  //     useIPA.postput(param)
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
  setNutritionData (state, res) {
    state.nutrition.items = res.rows
    state.nutrition.totalRows = res.rows.length
  },

  setParamDiet (state, data) {
    state.optionsDiet = []

    for (let i = 0; i < data.rows.length; i++) {
      let el = data.rows[i]
      let temp_obj = {
        value: el.id,
        title: el.name
      }
      state.optionsDiet.push(Object.assign({}, temp_obj))
    }
  },
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
