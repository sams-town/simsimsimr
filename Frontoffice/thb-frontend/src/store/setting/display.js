import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  start: 20,
  currentPage: 1,
  totalRows: 1,
  fields: [
    { key: 'name', label: 'Nama', sortable: true },
    { key: 'is_delete', label: 'Status', sortable: true },
    { key: 'action', label: 'Aksi'},
  ],
  items: [],
  pageOptions: [20, 50, 100],
  sortOptions: [],
  options:{
    display:[]
  }
})

// getters
const getters = {}

// actions
const actions = {
  getParam({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `setting/display/detail?code_s=${params}`
      }
      useIPA.get(param).then((response) => {
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
  
    if (params.code) {
      queryParams.push(`code_s=${params.code}`);
    }
  
    if (params.name) {
      queryParams.push(`name=${params.name}`);
    }
    
    let paramsall = queryParams.join('&');
  
    let param = {status: `setting/display/list?${paramsall}`};
  
    useIPA.get(param).then(response => {
      const display = {
        rows: response.data.data
      };
      commit('setData', {...display});
    });
  },
  
  // getAll ({ commit }, params) {
  //   let paramsall = `offset=${params.offset}&limit=${params.limit}`
  //   if (params.code && params.name) {
  //     paramsall = `offset=${params.offset}&limit=${params.limit}&code_s=${params.code}&name=${params.name}`
  //   } else if (params.name) {
  //     paramsall = `offset=${params.offset}&limit=${params.limit}&name=${params.name}`
  //   } else if (params.code) {
  //     paramsall = `offset=${params.offset}&limit=${params.limit}&code_s=${params.code}`
  //   }
  //   let param = {status: 'setting/display/list?'+paramsall}
  //   useIPA.get(param).then(response => {
  //     const display = {
  //       rows : response.data.data
  //     }
  //     commit('setData', {...display})
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
      let url = 'setting/display/create'
      if (formData.id) {
        url = 'setting/display/update'
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
      const param = { rawData: rawData, url: 'setting/display/delete', id: id }
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
  setData (state, display) {
    state.items = display.rows.rows
    state.totalRows = display.rows.iTotal
    state.start = display.rows.start
  },
  setParamDisplay (state, data) {
    state.options.display = []
    for (let i = 0; i < data.rows.rows.length; i++) {
      const option = []
      for (const key in data.rows.rows[i]) {
        if (key === 'id') { option['value'] = data.rows.rows[i][key] } else if (key === 'name') { option['title'] = data.rows.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows.rows[i][key] }
      }
      state.options.display.push(Object.assign({}, option))
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
