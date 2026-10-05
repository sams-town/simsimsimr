import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  parameter: {
    perPage: 20,
    pageOptions: [20, 50, 100],
    totalRows: 1,
    firstRow: 1,
    lastRow: 1,
    totalRows: 1,
    sortOptions: [],
    fields: [
      { key: 'name', label: 'Parameter', sortable: true },
      { key: 'value', label: 'Value', sortable: true },
      { key: 'unit', label: 'Satuan', sortable: true },
      { key: 'is_delete', label: 'Status', sortable: true },
      { key: 'action', label: 'Aksi'},
    ],
    items: [],
  },
  collation: {
    perPage: 20,
    pageOptions: [20, 50, 100],
    totalRows: 1,
    firstRow: 1,
    lastRow: 1,
    totalRows: 1,
    sortOptions: [],
    fields: [
      { key: 'name', label: 'Kelompok', sortable: true },
      { key: 'is_delete', label: 'Status', sortable: true },
      { key: 'action', label: 'Aksi'},
    ],
    items: [],
  },
  package: {
    perPage: 20,
    pageOptions: [20, 50, 100],
    totalRows: 1,
    firstRow: 1,
    lastRow: 1,
    totalRows: 1,
    sortOptions: [],
    fields: [
      { key: 'name', label: 'Kelompok', sortable: true },
      { key: 'is_delete', label: 'Status', sortable: true },
      { key: 'action', label: 'Aksi'},
    ],
    items: [],
  },
  fields: [
    { sortable: true, key: 'no_request', label: 'No Order', thStyle: { minWidth: '140px' } },
    { sortable: true, key: 'insurance_name', label: 'Cara Bayar', thStyle: { minWidth: '150px' } },
    { sortable: true, key: 'type_how_to_enter_name', label: 'Cara Masuk', thStyle: { minWidth: '140px' } },
    { sortable: true, key: 'exit_name', label: 'Status Pasien', thStyle: { minWidth: '140px' } },
    { sortable: true, key: 'norm', label: 'No RM' },
    { sortable: true, key: 'name', label: 'Nama', thStyle: { minWidth: '140px' } },
    { sortable: true, key: 'is_new', label: 'Jenis Pasien', thStyle: { minWidth: '120px' } },
    { sortable: true, key: 'birth_date', label: 'Tanggal Lahir', thStyle: { minWidth: '120px' } },
    { sortable: true, key: 'age', label: 'Kelamin&Umur', thStyle: { minWidth: '140px' } },
    { sortable: true, key: 'health_facility_item_name', label: 'Poli/Ruangan', thStyle: { minWidth: '140px' } },
    { sortable: true, key: 'doctor_in_charge_name', label: 'Dokter', thStyle: { minWidth: '140px' } },
    { sortable: true, key: 'action', label: 'Aksi' },
  ],
  start: 20,
  currentPage: 1,
  totalRows: 1,
  pageOptions: [20, 50, 100],
  sortOptions: [],
  items: [],
  order: [],
  itemstransaction: [],
  options:{
    parameter:[],
    collation:[]
  }
})

// getters
const getters = {}

// actions
const actions = {
  getLaboratoryHistoryV2 ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.limit) {
        queryParams.push(`limit=${params.limit}`)
      }
      
      if (params.norm) {
        queryParams.push(`norm=${params.norm}`)
      }

      if (params.registration_destination_id) {
        queryParams.push(`registration_destination_id=${params.registration_destination_id}`)
      }
      
      let paramsall = queryParams.join('&')
      let param = {status: `health_facility/laboratory_request/history_v2?${paramsall}`}
      
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
  getLaboratoryListFast ({ commit }, params) {
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

      if (params.date_start) {
        queryParams.push(`date_start=${params.date_start}`)
      }

      if (params.date_end) {
        queryParams.push(`date_end=${params.date_end}`)
      }

      if (params.name) {
        queryParams.push(`name=${params.name}`)
      }

      if (params.norm) {
        queryParams.push(`norm=${params.norm}`)
      }

      if (params.doctor_in_charge_id) {
        queryParams.push(`doctor_in_charge_id=${params.doctor_in_charge_id}`)
      }

      if (params.health_facility_item_id) {
        queryParams.push(`health_facility_item_id=${params.health_facility_item_id}`)
      }

      if (params.no_request) {
        queryParams.push(`no_request=${params.no_request}`)
      }
    
      let paramsall = queryParams.join('&')
      let param = {status: `health_facility/laboratory_request/list_fast?${paramsall}`}
      
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  getPackage ({ commit }, {params, cancelToken}) {
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

      if (params.name) {
        queryParams.push(`name=${params.name}`)
      }

      let paramsall = queryParams.join('&')
      let param = {status: `health_facility/laboratory_package/list?${paramsall}`}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  getCollation ({ commit }, {params, cancelToken}) {
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

      if (params.name) {
        queryParams.push(`name=${params.name}`)
      }

      let paramsall = queryParams.join('&')
      let param = {status: `health_facility/laboratory_collation/list?${paramsall}`}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  postPutHPP ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      
      let url = formData.id ? 'health_facility/laboratory_package_hpp/update': 'health_facility/laboratory_package_hpp/create'
      const param = { rawData: formData, url: url, id: formData.id }

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

  sendValidationResult ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      
      let url = formData.id ? 'health_facility/laboratory_request/result_validation': 'health_facility/laboratory_request/result_validation'
      const param = { rawData: formData, url: url, id: formData.id, laboratory_request_id:formData.laboratory_request_id }

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

  sendValidationSampling ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      
      let url = formData.id ? 'health_facility/laboratory_request/sampling': 'health_facility/laboratory_request/sampling'
      const param = { rawData: formData, url: url, id: formData.id }

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

  getCategory ({ commit }) {
		return new Promise((resolve, reject) => {
      let param = {status: 'health_facility/laboratory_package/category'}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  getLaboratoryPackageV2 ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];

      if(params){
        if (params.company_insurance_id) {
          queryParams.push(`company_insurance_id=${params.company_insurance_id}`)
        }
    
        if (params.service_class_id) {
          queryParams.push(`service_class_id=${params.service_class_id}`)
        }
      }

      let paramsall = queryParams.join('&')
      let param = {status: `health_facility/laboratory_package/detail_v2?${paramsall}`}
      
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  getOrder ({ commit }, params) {
    let queryParams = [];
  
    if (params.offset) {
      queryParams.push(`offset=${params.offset}`)
    } else if (params.offset === 0) {
      queryParams.push(`offset=${params.offset}`)
    }
  
    if (params.limit) {
      queryParams.push(`limit=${params.limit}`)
    }

    if (params.no_request) {
      queryParams.push(`no_request=${params.no_request}`)
    }

    if (params.norm) {
      queryParams.push(`norm=${params.norm}`)
    }

    if (params.name) {
      queryParams.push(`name=${params.name}`)
    }

    if (params.is_new) {
      if (params.is_new == 1) {
        queryParams.push(`is_new=N`)
      } else if (params.is_new == 2) {
        queryParams.push(`is_new=Y`)
      }
    }

    if (params.type_how_to_enter_id) {
      queryParams.push(`type_how_to_enter_id=${params.type_how_to_enter_id}`)
    }

    if (params.sex) {
      queryParams.push(`sex=${params.sex}`)
    }

    if (params.health_facility_id) {
      queryParams.push(`health_facility_id=${params.health_facility_id}`)
    }

    if (params.health_facility_item_id) {
      queryParams.push(`health_facility_item_id=${params.health_facility_item_id}`)
    }

    if (params.doctor_in_charge_id) {
      queryParams.push(`doctor_in_charge_id=${params.doctor_in_charge_id}`)
    }

    if (params.date_start && params.date_end) {
      queryParams.push(`date_start=${params.date_start}`)
      queryParams.push(`date_end=${params.date_end}`)
    }

    let paramsall = queryParams.join('&')
  
    let param = {status: `health_facility/laboratory_request/list?${paramsall}`}
    useIPA.get(param).then(response => {
      const registration = {
        rows : response.data.data
      }
      commit('setListData', {...registration})
    })
  },
  getOrderDetail ({ commit }, params) {
    return new Promise((resolve, reject) => {
      let queryParams = [];
  
      if (params.soap_id) {
        queryParams.push(`soap_id=${params.soap_id}`)
      }

      if (params.doctor_in_charge_id) {
        queryParams.push(`doctor_in_charge_id=${params.doctor_in_charge_id}`)
      }

      let paramsall = queryParams.join('&')
    
      let param = {status: `health_facility/laboratory_request/detail?${paramsall}`}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getByIdOrder ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'health_facility/laboratory_request/detail/' + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getByRegistrationId ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'health_facility/laboratory_request/history?registration_id=' + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data.rows)
      })
    })
  },
  getOrderByNorm ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'health_facility/laboratory_request/history?norm=' + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getOrderByRegisterId ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'health_facility/laboratory_request/history_v2?status=3&registration_id=' + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getResultById ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'health_facility/laboratory_request/result/' + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getResultInputById ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'health_facility/laboratory_request/result_input/' + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getByRegistrationIdTransaction ({ commit }, id) {
    let param = {status: 'health_facility/laboratory_request_item/detail?registration_id=' + id}
    useIPA.get(param).then(response => {
      const transaction = {
        rows : response.data.data
      }
      commit('setDataTransaction', {...transaction})
    })
  },
  getParam({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `finance/costing/service_class?type_costing_id=${params}`
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
  getParameterAll ({ commit }) {
    let param = {status: 'health_facility/laboratory_parameter/detail'}
    useIPA.get(param).then(response => {
      const parameter = {
        rows : response.data.data
      }
      commit('setDataParameter', {...parameter})
    })
  },
  getCollationAll ({ commit }) {
    let param = {status: 'health_facility/laboratory_collation/detail'}
    useIPA.get(param).then(response => {
      const collation = {
        rows : response.data.data
      }
      commit('setDataCollation', {...collation})
    })
  },
  getPackageAll ({ commit }, params) {
    let queryParams = [];

    if (params.company_id) {
      queryParams.push(`company_id=${params.company_id}`)
    }

    if (params.service_class_id) {
      queryParams.push(`service_class_id=${params.service_class_id}`)
    }
    let paramsall = queryParams.join('&')
  
    let param = {status: `health_facility/laboratory_package/detail?${paramsall}`}
    useIPA.get(param).then(response => {
      const packages = {
        rows : response.data.data
      }
      commit('setDataPackage', {...packages})
    })
  },
  getById ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'master/bank/' + id}
      useIPA.get(param).then(response => {
          resolve(response.data.data)
      })
    })
  },

  formPostPutProcessLis ({ commit }, formData) {
		return new Promise((resolve, reject) => {

      let url = 'health_facility/laboratory_request/process_lis'
      const param = { rawData: formData, url: url, id: formData.id }

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

  formPostPutProcess ({ commit }, formData) {
		return new Promise((resolve, reject) => {

      let url = 'health_facility/laboratory_request/process'
      const param = { rawData: formData, url: url, id: formData.id }

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
  
  formPostPutVerify ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      let url = 'health_facility/laboratory_request/verify'
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
  formPostPut({ commit }, formData) {
		return new Promise((resolve, reject) => {
      let url = formData.id ? 'health_facility/laboratory_request/update' : 'health_facility/laboratory_request/create'

      const param = { rawData: formData, url: url, id: formData.id }
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
        reject({message: response.data.message, debug: response.data.debug, response})
      })
		})
	},
  postPutCollation({ commit }, formData) {
		return new Promise((resolve, reject) => {
      let url = formData.id ? 'health_facility/laboratory_collation_parameter/update' : 'health_facility/laboratory_collation_parameter/create'

      const param = { rawData: formData, url: url, id: formData.id }
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
        reject({message: response.data.message, debug: response.data.debug, response})
      })
		})
	},
  sendFile ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      let url = 'health_facility/laboratory_request/sendFile'
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
  formPostPutParameter ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      let url = 'health_facility/laboratory_parameter/create'
      if (formData.id) {
        url = 'health_facility/laboratory_parameter/update'
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
  formPostPutCollation ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      let url = 'health_facility/laboratory_collation/create'
      if (formData.id) {
        url = 'health_facility/laboratory_collation/update'
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
  formPostPutPackage ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      let url = 'health_facility/laboratory_package/create'
      if (formData.id) {
        url = 'health_facility/laboratory_package/update'
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
  removeParameter ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'health_facility/laboratory_parameter/delete', id: id }
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
  removeCollation ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'health_facility/laboratory_collation/delete', id: id }
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
  
  removePackage ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'health_facility/laboratory_package/delete', id: id }
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

  removeRequest ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'health_facility/laboratory_request/delete', id: id }
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
        reject({message: response.data.message, debug: response.data.debug, response})
      })
		})
	},

  removeItemRequest ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'health_facility/laboratory_request_item/delete', id: id }
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
        reject({message: response.data.message, debug: response.data.debug, response})
      })
		})
	}
}

// mutations
const mutations = {
  setListData (state, registration) {
    state.items = registration.rows.rows
    state.totalRows = registration.rows.iTotal
    state.start = registration.rows.start
  },
  setDataParameter (state, parameter) {
    state.parameter.items = parameter.rows
    state.parameter.totalRows = parameter.rows.length
    state.options.parameter = []
    for (let i = 0; i < parameter.rows.length; i++) {
      const option = []
      for (const key in parameter.rows[i]) {
        if (key === 'id') { option['value'] = parameter.rows[i][key] 
        } else if (key === 'name') { option['title'] = parameter.rows[i][key] 
        } else if (key === 'value') { option['val'] = parameter.rows[i]['value'] 
        } else if (key === 'unit') { option['unit'] = parameter.rows[i][key] 
        } else if (key === 'id') { option['oid'] = parameter.rows[i][key] }
      }
      state.options.parameter.push(Object.assign({}, option))
    }
  },
  setDataCollation (state, collation) {
    state.collation.items = collation.rows
    state.collation.totalRows = collation.rows.length
    state.options.collation = []
    for (let i = 0; i < collation.rows.length; i++) {
      const option = []
      for (const key in collation.rows[i]) {
        if (key === 'id') { option['value'] = collation.rows[i][key] 
        } else if (key === 'name') { option['title'] = collation.rows[i][key] 
        } else if (key === 'parameter') { option['parameter'] = collation.rows[i][key] 
        } else if (key === 'id') { option['oid'] = collation.rows[i][key] }
      }
      state.options.collation.push(Object.assign({}, option))
    }
  },
  setDataPackage (state, packages) {
    state.package.items = packages.rows
    state.package.totalRows = packages.rows.length
  },
  setParamCosting (state, data) {
    state.options.costing = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = []
      for (const key in data.rows[i]) {
        if (key === 'id') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'is_delete') { option['costing'] = 200000 } else if (key === 'id') { option['oid'] = data.rows[i][key] }
      }
      state.options.costing.push(Object.assign({}, option))
    }
  },
  setDataOrder (state, transaction) {
    state.order = transaction.rows
  },
  setDataTransaction (state, transaction) {
    state.itemstransaction = []
    state.itemstransaction = transaction.rows.sort((a, b) => new Date(a.date_request) - new Date(b.date_request))
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
