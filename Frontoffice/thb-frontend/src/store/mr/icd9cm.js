import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  start: 20,
  totalRows: 1,
  fields: [
    { key: 'code', label: 'ICD9CM', sortable: true },
    { key: 'name', label: 'Nama', sortable: true },
    { key: 'is_delete', label: 'Status', sortable: true },
    { key: 'action', label: 'Aksi'},
  ],
  items: [],
  pageOptions: [20, 50, 100],
  // currentPage: 1,
  sortOptions: [],
  options:{
    icd9cm:[]
  }
})

// getters
const getters = {}

// actions
const actions = {
  getParam({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `mr/icd9cm/detail?code_s=${params}`
      }
      useIPA.get(param).then((response) => {
        resolve(response.data.data)
        // const Param = {
        //   rows: response.data.data
        // }
        // commit('setParamIcd9cm', { ...Param })
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
      queryParams.push(`code_s=${params.code}`);
    }
  
    if (params.name) {
      queryParams.push(`name=${params.name}`);
    }
    
    let paramsall = queryParams.join('&');
  
    let param = {status: `mr/icd9cm/list?${paramsall}`};
    useIPA.get(param).then(response => {
      const icd9cm = {
        rows : response.data.data
      }
      commit('setData', {...icd9cm})
    })
  },
  // getAll ({ commit }, params) {
  //   params = `offset=${params.offset}&limit=${params.limit}`
  //   let param = {status: 'mr/icd9cm/detail?'+params}
  //   useIPA.get(param).then(response => {
  //     const icd9cm = {
  //       rows : response.data.data
  //     }
  //     commit('setData', {...icd9cm})
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
      let url = 'mr/icd9cm/create'
      if (formData.id) {
        url = 'mr/icd9cm/update'
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
      const param = { rawData: rawData, url: 'mr/icd9cm/delete', id: id }
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
  setData (state, icd9cm) {
    state.items = icd9cm.rows.rows
    state.totalRows = icd9cm.rows.iTotal
    state.start = icd9cm.rows.start
  },
  setParamIcd9cm (state, data) {
    state.options.icd9cm = []
    for (let i = 0; i < data.rows.rows.length; i++) {
      const option = []
      for (const key in data.rows.rows[i]) {
        if (key === 'id') { option['value'] = data.rows.rows[i][key] } else if (key === 'name') { option['title'] = data.rows.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows.rows[i][key] }
      }
      state.options.icd9cm.push(Object.assign({}, option))
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
