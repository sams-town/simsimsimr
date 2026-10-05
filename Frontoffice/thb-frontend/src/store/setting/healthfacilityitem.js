import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  start: 20,
  totalRows: 1,
  fields: [
    { key: 'name', label: 'Nama', sortable: true },
    { key: 'health_facility_name', label: 'Fasilitas Kesehatan'},
    { key: 'is_delete', label: 'Status', sortable: true },
    { key: 'action', label: 'Aksi'},
  ],
  items: [],
  pageOptions: [20, 50, 100],
  sortOptions: [],
  perPage: 20,
  options:{
    healthfacilityitem:[]
  }
})

// getters
const getters = {}

// actions
const actions = {
  getParam({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `setting/health_facility_item/detail?health_facility_id=${params}`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamHealthFacilityItem', { ...Param })
      })
    })
  },
  getParamAlls({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `setting/health_facility_item/detail`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamHealthFacilityItem', { ...Param })
        resolve(response.data.data)
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
      queryParams.push(`health_facility_id=${params.health_facility_id}`);
    }
  
    if (params.name) {
      queryParams.push(`name=${params.name}`);
    }
    
    let paramsall = queryParams.join('&');
  
    let param = {status: `setting/health_facility_item/list?${paramsall}`};
    useIPA.get(param).then(response => {
      const health_facility_item = {
        rows : response.data.data
      }
      commit('setData', {...health_facility_item})
    })
  },
  getAllList ({ commit }) {
    let param = {status: 'setting/health_facility_item/detail'}
    useIPA.get(param).then(response => {
      const health_facility_item = {
        rows : response.data.data
      }
      commit('setDataList', {...health_facility_item})
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
        health_facility_id: formData.health_facility_id,
			}
      let url = 'setting/health_facility_item/create'
      if (formData.id) {
        url = 'setting/health_facility_item/update'
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
      const param = { rawData: rawData, url: 'setting/health_facility_item/delete', id: id }
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
  setData (state, healthfacilityitem) {
    state.items = healthfacilityitem.rows.rows
    state.totalRows = healthfacilityitem.rows.rows.length
    state.start = healthfacilityitem.rows.start
  },
  setDataList (state, healthfacilityitem) {
    state.items = healthfacilityitem.rows
    state.totalRows = healthfacilityitem.rows.length
    state.start = healthfacilityitem.rows.start
  },
  setParamHealthFacilityItem (state, data) {
    state.options.healthfacilityitem = []
    const option = []
    for (let i = 0; i < data.rows.length; i++) {
      for (const key in data.rows[i]) {
        if (key === 'id') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows[i][key] }
      }
      state.options.healthfacilityitem.push(Object.assign({}, option))
    }
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
