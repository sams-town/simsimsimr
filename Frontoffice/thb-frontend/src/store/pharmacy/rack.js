import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  start: 20,
  totalRows: 1,
  fields: [
    { key: 'pharmacy_depo_name', label: 'Nama Gudang', sortable: true },
    { key: 'name', label: 'Nama Rak', sortable: true },
    { key: 'is_delete', label: 'Status', sortable: true },
    { key: 'action', label: 'Aksi'},
  ],
  items: [],
  pageOptions: [20, 50, 100],
  sortOptions: [],
  perPage: 20,
  options:{
    rack:[]
  }
})

// getters
const getters = {}

// actions
const actions = {
  getParam({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `pharmacy/rack/detail?pharmacy_depo_id=`+params
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamRack', { ...Param })
      })
    })
  },
  getParamProc({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `pharmacy/rack/detail`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamRackPROC', { ...Param })
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
  
    if (params.code) {
      queryParams.push(`code=${params.code}`);
    }
  
    if (params.name) {
      queryParams.push(`name=${params.name}`);
    }
    
    let paramsall = queryParams.join('&');
  
    let param = {status: `pharmacy/rack/list?${paramsall}`};

    useIPA.get(param).then(response => {
      const rack = {
        rows : response.data.data
      }
      commit('setData', {...rack})
    })
  },
  // getAll ({ commit }) {
  //   let param = {status: 'pharmacy/rack/detail'}
  //   useIPA.get(param).then(response => {
  //     const rack = {
  //       rows : response.data.data
  //     }
  //     commit('setData', {...rack})
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
        code: formData.code,
        name: formData.name,
			}
      let url = 'pharmacy/rack/create'
      if (formData.id) {
        url = 'pharmacy/rack/update'
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
      const param = { rawData: rawData, url: 'pharmacy/rack/delete', id: id }
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
  setData (state, rack) {
    state.items = rack.rows.rows
    state.totalRows = rack.rows.iTotal
    state.start = rack.rows.start
  },
  setParamRack (state, data) {
    state.options.rack = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = []
      for (const key in data.rows[i]) {
        if (key === 'id') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows[i][key] }
      }
      state.options.rack.push(Object.assign({}, option))
    }
  },
  setParamRackPROC (state, data) {
    state.options.rack = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = []
      if (data.rows[i]['code'] == '1'){
        for (const key in data.rows[i]) {
          if (key === 'id') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows[i][key] }
        }
        state.options.rack.push(Object.assign({}, option))
      }
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
