import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  start: 20,
  totalRows: 0,
  fields: [
    { key: 'name', label: 'Nama', sortable: true },
    { key: 'type_module_id', label: 'Tipe Modul'},
    { key: 'is_delete', label: 'Status', sortable: true },
    { key: 'action', label: 'Aksi'},
  ],
  items: [],
  pageOptions: [20, 50, 100],
  sortOptions: [],
  perPage: 20,
  options:{
    healthfacility:[],
    inpatientoption:[],
  }
})

// getters
const getters = {}

// actions
const actions = {
  getListBundle ({ commit }, params) {
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
      let param = {status: `health_facility/medical_support_bundle/list?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  postPutBundle ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      let url = formData.id ? 'health_facility/medical_support_bundle/update' : 'health_facility/medical_support_bundle/create'
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
      .catch(({error, response}) => {
        reject({error: response.data.message, response})
      })
		})
	},
  putItemsBundle ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const param = { rawData: formData.items, url: `health_facility/medical_support_bundle/add_item`, id: formData.medical_support_bundle_id }
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
  removeItemsBundle ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = {
        package_id: formData.package_id,
			}
      const param = { rawData: rawData, url: 'health_facility/medical_support_bundle/delete_item', id: formData.medical_support_bundle_id }
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

  getParam({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `setting/health_facility/detail?type_module_id=2`
      }
      if (localStorage.getItem(`healthfacility`)) {
        commit('setParamHealthFacility', {...JSON.parse(localStorage.getItem(`healthfacility`))})
      } else {
        useIPA.get(param).then((response) => {
          const Param = {
            rows: response.data.data
          }
          localStorage.setItem(`healthfacility`, JSON.stringify(Param))
          if (params === 'healthfacility') {
            commit('setParamHealthFacility', { ...Param })
          }
        })
      }
    })
  },
  getInpatientOption({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `setting/health_facility/detail?id=40f06534-07ec-4221-ad9d-dea08196079f`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        if (params === 'inpatientOption') {
          commit('setParamInpatientOption', { ...Param })
        }
      })
    })
  },
  getAll ({ commit }, params) {
    let queryParams = [];

    if (params.offset) {
      queryParams.push(`offset=${params.offset}`);
    } else if (params.offset === 0) {
      queryParams.push(`offset=${params.offset}`);
    }
  
    if (params.limit) {
      queryParams.push(`limit=${params.limit}`);
    }
  
    if (params.health_facility_id) {
      queryParams.push(`id=${params.health_facility_id}`);
    }
  
    if (params.type_module_name) {
      queryParams.push(`type_module_id=${params.type_module_name}`);
    }
    
    let paramsall = queryParams.join('&');
  
    let param = {status: `setting/health_facility/list?${paramsall}`};
    useIPA.get(param).then(response => {
      const health_facility = {
        rows : response.data.data
      }
      commit('setData', {...health_facility})
    })
  },
  // getAll ({ commit }) {
  //   let param = {status: 'setting/health_facility/detail'}
  //   useIPA.get(param).then(response => {
  //     const health_facility = {
  //       rows : response.data.data
  //     }
  //     commit('setData', {...health_facility})
  //   })
  // },
  getById ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'master/bank/' + id}
      useIPA.get(param).then(response => {
          resolve(response.data.data)
      })
    })
  },
  formPostPut ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = {
        type_sys: formData.product,
        type_branch: formData.type_product,
        code: formData.code,
        name: formData.name,
        phone: formData.phone,
        email: formData.email,
        password: formData.repassword,
			}
      const param = { rawData: rawData, url: 'initial/register', id: null }
      useIPA.postput(param)
			.then(response => {
        router.push({ name: 'login' })
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
  formPostPutMaster ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = {
        name: formData.name,
        type_module_id: formData.type_module_id,
			}
      let url = 'setting/health_facility/create'
      if (formData.id) {
        url = 'setting/health_facility/update'
      }
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
		})
	},
  remove ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'setting/health_facility/delete', id: id }
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
}

// mutations
const mutations = {
  setData (state, health_facility) {
    state.items = health_facility.rows.rows
    state.totalRows = health_facility.rows.iTotal
    state.start = health_facility.rows.start
  },
  setParamHealthFacility (state, data) {
    state.options.healthfacility = []
    const option = []
    for (let i = 0; i < data.rows.length; i++) {
      for (const key in data.rows[i]) {
        if (key === 'id') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows[i][key] }
      }
      state.options.healthfacility.push(Object.assign({}, option))
    }
  },
  setParamInpatientOption (state, data) {
    state.options.inpatientoption = []
    const option = []
    for (let i = 0; i < data.rows.length; i++) {
      for (const key in data.rows[i]) {
        if (key === 'id') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows[i][key] }
      }
      state.options.inpatientoption.push(Object.assign({}, option))
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
