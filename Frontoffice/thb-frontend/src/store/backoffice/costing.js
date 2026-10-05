import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  perPage: 20,
  pageOptions: [20, 50, 100],
  totalRows: 1,
  firstRow: 1,
  lastRow: 1,
  totalRows: 1,
  sortOptions: [],
  fields: [
    { key: 'type_costing_id', label: 'Jenis Tarif', sortable: true },
    { key: 'name', label: 'Nama', sortable: true },
    { key: 'service_class_id', label: 'Kelas Layanan', sortable: true },
    { key: 'var_1', label: 'Tarif', sortable: true },
    { key: 'is_delete', label: 'Status', sortable: true },
    { key: 'action', label: 'Aksi'},
  ],
  items: [],
  options:{
    costing:[]
  }
})

// getters
const getters = {}

// actions
const actions = {
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
  getAll ({ commit }) {
    let param = {status: 'finance/costing/service_class'}
    useIPA.get(param).then(response => {
      const costing = {
        rows : response.data.data
      }
      commit('setData', {...costing})
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
  remove ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'hr/employee/delete', id: id }
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
    state.items = costing.rows
    state.totalRows = costing.rows.length
  },
  setParamCosting (state, data) {
    state.options.costing = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = []
      for (const key in data.rows[i]) {
        if (key === 'id') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'is_delete') { option['costing'] = data.rows[i]['var_1'] } else if (key === 'id') { option['oid'] = data.rows[i][key] }
      }
      state.options.costing.push(Object.assign({}, option))
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
