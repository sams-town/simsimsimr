import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  start: 20,
  currentPage: 1,
  totalRows: 1,
  fields: [
    { key: 'no_document', label: 'No Dokumen', sortable: true },
    { key: 'pharmacy_depo_name', label: 'Gudang/Depo', sortable: true },
    { key: 'ntotal', label: 'Nilai', sortable: true },
    { key: 'ndiscount', label: 'Nilai Diskon', sortable: true },
    { key: 'nfinish', label: 'Nilai Total', sortable: true },
    { key: 'validation_key_1', label: 'Status', sortable: true },
    { key: 'action', label: 'Aksi'},
  ],
  items: [],
  pageOptions: [20, 50, 100],
  sortOptions: [],
  options:{
    planning:[]
  }
})

// getters
const getters = {}

// actions
const actions = {
  getParam({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `pharmacy/planning/list`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamPlanning', { ...Param })
        resolve(response.data.data)
      })
    })
  },
  getRef({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `pharmacy/planning/refs`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamPlanningRef', { ...Param })
        resolve(response.data.data)
      })
    })
  },
  getLowDepo ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {
        status: `pharmacy/planning/low_depo/${id}`
      }
      useIPA.get(param).then(response => {
          resolve(response.data.data)
      })
    })
  },
  getPlanningSupplier ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];
  
      if (params.planning_id) {
        queryParams.push(`planning_id=${params.planning_id}`);
      }
    
      if (params.supplier_id) {
        queryParams.push(`supplier_id=${params.supplier_id}`);
      }
      
      let paramsall = queryParams.join('&');
    
      let param = {status: `pharmacy/planning/supplier?${paramsall}`};

      useIPA.get(param).then(response => {
          resolve(response.data.data)
      })
    })
  },
  getItemDepo ({ commit }, data) {
		return new Promise((resolve, reject) => {
      let param = {
        status: `pharmacy/planning/item_depo?pharmacy_depo_id=${data.pharmacy_depo_id}&pharmacy_catalog_id=${data.pharmacy_catalog_id}`
      }
      useIPA.get(param).then(response => {
          resolve(response.data.data)
      })
    })
  },
  getAll ({ commit }, params) {
    // console.log(params);
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
  
    let param = {status: `pharmacy/planning/list?${paramsall}`};
  
    useIPA.get(param).then(response => {
      const catalog = {
        rows: response.data.data
      };
      commit('setData', {...catalog});
    });
  },
  // getAll ({ commit }) {
  //   let param = {status: 'pharmacy/catalog/detail'}
  //   useIPA.get(param).then(response => {
  //     const catalog = {
  //       rows : response.data.data
  //     }
  //     commit('setData', {...catalog})
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
      console.log(formData)
      let url = 'pharmacy/planning/create'
      if (formData.id) {
        url = 'pharmacy/planning/update'
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
      const param = { rawData: rawData, url: 'planning/delete', id: id }
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
  setData (state, catalog) {
    state.items = catalog.rows.rows
    state.totalRows = catalog.rows.iTotal
    state.start = catalog.rows.start
  },
  setParamPlanning (state, data) {
    state.options.planning = []
    for (let i = 0; i < data.rows.rows.length; i++) {
      const option = []
      for (const key in data.rows.rows[i]) {
        if (key === 'id') { option['value'] = data.rows.rows[i][key] } else if (key === 'planning_items') { option['planning'] = data.rows.rows[i] } else if (key === 'no_document') { option['title'] = data.rows.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows.rows[i][key] }
      }
      state.options.planning.push(Object.assign({}, option))
    }
  },
  setParamPlanningRef (state, data) {
    state.options.planning = []
    for (let i = 0; i < data.rows.rows.length; i++) {
      const option = []
      if (data.rows.rows[i]['validation_key_1']) {
        for (const key in data.rows.rows[i]) {
          if (key === 'id') { option['value'] = data.rows.rows[i][key] } else if (key === 'planning_items') { option['planning'] = data.rows.rows[i] } else if (key === 'no_document') { option['title'] = data.rows.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows.rows[i][key] }
        }
        state.options.planning.push(Object.assign({}, option))
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
