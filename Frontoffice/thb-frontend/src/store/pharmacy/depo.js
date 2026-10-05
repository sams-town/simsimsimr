import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  start: 20,
  totalRows: 1,
  fields: [
    { key: 'code', label: 'Depo', sortable: true },
    { key: 'name', label: 'Nama', sortable: true },
    { key: 'is_delete', label: 'Status', sortable: true },
    { key: 'action', label: 'Aksi'},
  ],
  items: [],
  pageOptions: [20, 50, 100],
  sortOptions: [],
  perPage: 20,
  options:{
    depo:[]
  }
})

// getters
const getters = {}

// actions
const actions = {
  getParam({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param
      if (params === 'noncentral') {
        param = {
          status: `pharmacy/depo/detail?is_central=0&is_depo=1`
        }
      } else {
        param = {
          status: `pharmacy/depo/detail`
        }
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamDepo', { ...Param })
      })
    })
  },
  getParamProc({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `pharmacy/depo/detail`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamDepoPROC', { ...Param })
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
  
    let param = {status: `pharmacy/depo/list?${paramsall}`};

    useIPA.get(param).then(response => {
      const depo = {
        rows : response.data.data
      }
      commit('setData', {...depo})
    })
  },
  // getAll ({ commit }) {
  //   let param = {status: 'pharmacy/depo/detail'}
  //   useIPA.get(param).then(response => {
  //     const depo = {
  //       rows : response.data.data
  //     }
  //     commit('setData', {...depo})
  //   })
  // },
  getDepoBHP ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'pharmacy/depo/bhp/'+id}
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
      let url = 'pharmacy/depo/create'
      if (formData.id) {
        url = 'pharmacy/depo/update'
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
      const param = { rawData: rawData, url: 'pharmacy/depo/delete', id: id }
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
  setData (state, depo) {
    state.items = depo.rows.rows
    state.totalRows = depo.rows.iTotal
    state.start = depo.rows.start
  },
  setParamDepo (state, data) {
    state.options.depo = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = []
      for (const key in data.rows[i]) {
        if (key === 'id') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows[i][key] }
      }
      state.options.depo.push(Object.assign({}, option))
    }
  },
  setParamDepoPROC (state, data) {
    state.options.depo = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = []
      if (data.rows[i]['code'] == '1'){
        for (const key in data.rows[i]) {
          if (key === 'id') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows[i][key] }
        }
        state.options.depo.push(Object.assign({}, option))
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
