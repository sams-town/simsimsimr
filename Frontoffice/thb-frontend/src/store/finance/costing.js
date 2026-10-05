import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  start: 20,
  currentPage: 1,
  totalRows: 1,
  fields: [
    { key: 'type_costing_name', label: 'Jenis Tarif', sortable: true },
    { key: 'name', label: 'Nama Tarif', sortable: true },
    { key: 'company_name', label: 'Penjamin', sortable: true },
    { key: 'service_class_name', label: 'Kelas', sortable: true },
    { key: 'health_facility_item_name', label: 'Poliklinik/Ruangan', sortable: true },
    // { key: 'desc', label: 'Detail', sortable: true },
    { key: 'var_1', label: 'Harga', sortable: true },
    { key: 'is_delete', label: 'Status', sortable: true },
    { key: 'action', label: 'Aksi'},
  ],
  ms:{
    fields: [
      { key: 'type_costing_name', label: 'Jenis Tarif', sortable: true },
      { key: 'name', label: 'Nama', sortable: true },
      { key: 'desc', label: 'Detail', sortable: true },
      // { key: 'company_name', label: 'Penjamin', sortable: true },
      { key: 'service_class_name', label: 'Kelas', sortable: true },
      { key: 'var_1', label: 'Harga', sortable: true },
      { key: 'is_delete', label: 'Status', sortable: true },
      { key: 'action', label: 'Aksi'},
    ],
    start: 20,
    totalRows: 1,
    group: [],
    doctor: [],
    items: [],
  },
  items: [],
  pageOptions: [20, 50, 100],
  sortOptions: [],
  options:{
    costing:[],
    serviceData: [],
    newCosting: [],
  }
})

// getters
const getters = {}

// actions
const actions = {
  
  getAllParams({ commit }, params) {
    let queryParams = [];

    if (params.offset) {
        queryParams.push(`offset=${params.offset}`);
    } else if (params.offset === 0) {
        queryParams.push(`offset=${params.offset}`);
    }

    if (params.limit) {
        queryParams.push(`limit=${params.limit}`);
    }

    if (params.name) {
        queryParams.push(`name=${params.name}`);
    }

    if (params.company_id) {
        queryParams.push(`company_insurance_id=${params.company_id}`);
    }

    if (params.type_costing_id) {
        queryParams.push(`type_costing_id=${params.type_costing_id}`);
    }

    if (params.url) {
        queryParams.push(`url=${params.url}`);
    }

    if (params.health_facility_item_id) {
        queryParams.push(`health_facility_item_id=${params.health_facility_item_id}`);
    }

    let paramsall = queryParams.join('&');

    let param = { status: `finance/costing/service_class_list?${paramsall}` };

    useIPA.get(param).then(response => {
        const user = {
            rows: response.data.data
        };
        commit('setData', { ...user });
    });
  },

  getOptionCosting ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.type_costing_id) {
        queryParams.push(`type_costing_id=${params.type_costing_id}`)
      }
  
      if (params.is_nurse) {
        queryParams.push(`is_nurse=${params.is_nurse}`)
      }
  
      if (params.health_facility_item_id) {
        queryParams.push(`health_facility_item_id=${params.health_facility_item_id}`)
      }
  
      if (params.service_class_id) {
        queryParams.push(`service_class_id=${params.service_class_id}`)
      }

      if (params.classification_one_type_id) {
        queryParams.push(`classification_one_type_id=${params.classification_one_type_id}`)
      }
  
      if (params.company_insurance_id) {
        queryParams.push(`company_insurance_id=${params.company_insurance_id}`)
      }  
    
      let paramsall = queryParams.join('&')
      let param = {status: `finance/costing/service_class?${paramsall}`}

      useIPA.get(param).then(response => {
        let resData = response.data.data
        let temp_arry = []

        for (let i = 0; i < resData.length; i++) {
          const element = resData[i]

          let temp_obj = {
            value: element.id,
            title: `${element.name} Kelas: ${element.service_class}`,
            price: element.var_1,
          }
          temp_arry.push(Object.assign({}, temp_obj))
        }
        resolve(temp_arry)
      })
    })
  },

  getNewParamCosting({ commit }, params) {
    let queryParams = [];

    if (params.type_costing_id) {
      queryParams.push(`type_costing_id=${params.type_costing_id}`)
    }

    if (params.is_nurse) {
      queryParams.push(`is_nurse=${params.is_nurse}`)
    }

    if (params.company_id) {
      queryParams.push(`company_id=${params.company_id}`)
    }

    if (params.health_facility_item_id) {
      queryParams.push(`health_facility_item_id=${params.health_facility_item_id}`)
    }

    if (params.service_class_id) {
      queryParams.push(`service_class_id=${params.service_class_id}`)
    }

    if (params.company_insurance_id) {
      queryParams.push(`company_insurance_id=${params.company_insurance_id}`)
    } 
    
    let paramsall = queryParams.join('&')

    return new Promise((resolve, reject) => {
      let param = {status: `finance/costing/service_class?${paramsall}`}
      useIPA.get(param).then((response) => {
        const Param = response.data.data
        commit('setOptionCosting', Param)
      })
    })
  },

  getParam({ commit }, params) {
    let queryParams = [];

    if (params.type_costing_id) {
      queryParams.push(`type_costing_id=${params.type_costing_id}`)
    }

    if (params.is_nurse) {
      queryParams.push(`is_nurse=${params.is_nurse}`)
    }

    if (params.company_id) {
      queryParams.push(`company_id=${params.company_id}`)
    }

    if (params.health_facility_item_id) {
      queryParams.push(`health_facility_item_id=${params.health_facility_item_id}`)
    }

    if (params.classification_one_type_id) {
      queryParams.push(`classification_one_type_id=${params.classification_one_type_id}`)
    }

    if (params.company_insurance_id) {
      queryParams.push(`company_insurance_id=${params.company_insurance_id}`)
    }

    if (params.service_class_id) {
      queryParams.push(`service_class_id=${params.service_class_id}`)
    }

    if (params.company_insurance_id) {
      queryParams.push(`company_insurance_id=${params.company_insurance_id}`)
    }  

    let paramsall = queryParams.join('&')

    return new Promise((resolve, reject) => {
      let param = {
        status: `finance/costing/service_class?${paramsall}`
        // status: `hr/costing/doctor_detail?health_facility_item_id=${params}`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamCosting', { ...Param })
      })
    })
  },
  getParamByTypeCostingId({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `finance/costing/detail?type_costing_id=${params}`
      }
      useIPA.get(param).then((response) => {
        const serviceData = {
          rows: response.data.data
        }
        commit('setByCostingId', { ...serviceData })
      })
    })
  },
  getAll ({ commit }) {
    let param = {status: 'finance/costing/service_class'}
    useIPA.get(param).then(response => {
      const costing = {
        rows : response.data.data
      }
      commit('setData', {...costing})
    })
  },
  getAllFast ({ commit }, params) {
    return new Promise((resolve, reject) => {
      let queryParams = [];
    
      if (params.offset) {
        queryParams.push(`offset=${params.offset}`)
      } else if (params.offset === 0) {
        queryParams.push(`offset=${params.offset}`)
      }
    
    
      if (params.limit) {
        queryParams.push(`limit=${params.limit}`)
      }

     
      if (params.name) {
        queryParams.push(`name=${params.name}`)
      }
      if (params.doctor_in_charge_name) {
        queryParams.push(`doctor_in_charge_name=${params.doctor_in_charge_name}`)
      }
      if (params.health_facility_id) {
        queryParams.push(`health_facility_id=${params.health_facility_id}`)
      }

      if (params.sep) {
        queryParams.push(`sep=${params.sep}`)
      }

     

      if (params.date_start && params.date_end) {
        queryParams.push(`date_start=${params.date_start}`)
        queryParams.push(`date_end=${params.date_end}`)
      }

      let paramsall = queryParams.join('&')
    
      let param = {status: `finance/medical_services/list_fast?${paramsall}`}
      useIPA.get(param).then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
        return response;
      })
    })
  },
  getAllFastResume ({ commit }, params) {
    return new Promise((resolve, reject) => {
      let queryParams = [];
    
      if (params.offset) {
        queryParams.push(`offset=${params.offset}`)
      } else if (params.offset === 0) {
        queryParams.push(`offset=${params.offset}`)
      }
    
    
      if (params.limit) {
        queryParams.push(`limit=${params.limit}`)
      }

     
      if (params.name) {
        queryParams.push(`name=${params.name}`)
      }
      if (params.doctor_in_charge_name) {
        queryParams.push(`doctor_in_charge_name=${params.doctor_in_charge_name}`)
      }
      if (params.health_facility_id) {
        queryParams.push(`health_facility_id=${params.health_facility_id}`)
      }

      if (params.sep) {
        queryParams.push(`sep=${params.sep}`)
      }

     

      if (params.date_start && params.date_end) {
        queryParams.push(`date_start=${params.date_start}`)
        queryParams.push(`date_end=${params.date_end}`)
      }

      let paramsall = queryParams.join('&')
    
      let param = {status: `finance/medical_services/list_fast_resume?${paramsall}`}
      useIPA.get(param).then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
        return response;
      })
    })
  },
  getAllFastDoctor ({ commit }, params) {
    return new Promise((resolve, reject) => {
      let queryParams = [];
    
      if (params.offset) {
        queryParams.push(`offset=${params.offset}`)
      } else if (params.offset === 0) {
        queryParams.push(`offset=${params.offset}`)
      }
    
    
      if (params.limit) {
        queryParams.push(`limit=${params.limit}`)
      }

     
      if (params.name) {
        queryParams.push(`name=${params.name}`)
      }
      if (params.period_id) {
        queryParams.push(`period_id=${params.period_id}`)
      }
      if (params.doctor_in_charge_name) {
        queryParams.push(`doctor_in_charge_name=${params.doctor_in_charge_name}`)
      }
      // if (params.health_facility_id) {
      //   queryParams.push(`health_facility_id=${params.health_facility_id}`)
      // }

      if (params.sep) {
        queryParams.push(`sep=${params.sep}`)
      }

     

      // if (params.date_start && params.date_end) {
      //   queryParams.push(`date_start=${params.date_start}`)
      //   queryParams.push(`date_end=${params.date_end}`)
      // }

      let paramsall = queryParams.join('&')
    
      let param = {status: `finance/medical_services/list_fast_doctor?${paramsall}`}
      useIPA.get(param).then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
        return response;
      })
    })
  },
  getById ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'finance/costing/detail/' + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  formPostPut ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      let url = 'finance/costing/create'
      if (formData.id) {
        url = 'finance/costing/update'
      }
      const param = { rawData: formData, url: url, id: formData.id }
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
  formPostPutV2 ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      let url = 'finance/costing/create_v2'
      if (formData.id) {
        url = 'finance/costing/update'
      }
      const param = { rawData: formData, url: url, id: formData.id }
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
  sendBA ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      let url = 'finance/medical_services/sendBA'
      const param = { rawData: formData, url: url, id: formData.id }
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
  sendValidation ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      let url = 'finance/medical_services/sendValidation'
      const param = { rawData: formData, url: url, id: 1 }
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
  sendValidationPayroll ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      let url = 'finance/medical_services/sendValidationPayroll'
      const param = { rawData: formData, url: url, id: 1 }
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
  remove ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'finance/costing/delete', id: id }
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
  setData (state, costing) {
    state.items = costing.rows.rows
    state.totalRows = costing.rows.iTotal
    state.start = costing.rows.start
  },
  setFastListDataMS (state, registration) {
    state.ms.items = registration.rows.rows
    state.ms.doctor = registration.rows.doctor
    state.ms.group = registration.rows.group
    state.ms.totalRows = registration.rows.iTotal
    state.ms.start = registration.rows.start
  },
  setParamCosting (state, data) {
    state.options.costing = []
    for (let i = 0; i < data.rows?.length; i++) {
      const option = []
      for (const key in data.rows[i]) {
        if (key === 'id') { 
          option['value'] = data.rows[i][key] 
        } else if (key === 'name') { 
          option['title'] = data.rows[i][key]+' Kelas : '+data.rows[i]['service_class'] 
        } else if (key === 'costing_id') { 
          option['costing_id'] = data.rows[i][key] 
        } else if (key === 'is_delete') { 
          option['costing'] = data.rows[i]['var_1'] 
        } else if (key === 'is_ref') { 
          option['data'] = data.rows[i] 
        }  else if (key === 'id') { 
          option['oid'] = data.rows[i][key] 
        }
      }
      state.options.costing.push(Object.assign({}, option))
    }
  },
  setByCostingId(state, serviceData) {
    state.options.serviceData = []
    const option = []
    for (let i = 0; i < serviceData.rows.length; i++) {
      for (const key in serviceData.rows[i]) {
        if (key === 'id') { option['value'] = serviceData.rows[i][key] } else if (key === 'name') { option['title'] = serviceData.rows[i][key] } else if (key === 'id') { option['oid'] = serviceData.rows[i][key] }
      }
      state.options.serviceData.push(Object.assign({}, option))
    }
  },

  setOptionCosting (state, data) {
    state.options.newCosting = []
    for (let i = 0; i < data.length; i++) {
      const element = data[i]

      let temp_obj = {
        value: element.id,
        title: `${element.name} Kelas: ${element.service_class}`,
        price: element.var_1,
        // costing_id: element.costing_id,
        // data: element
      }
      state.options.newCosting.push(Object.assign({}, temp_obj))
    }
  },

}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
