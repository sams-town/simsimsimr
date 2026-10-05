import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  start: 20,
  currentPage: 1,
  totalRows: 1,
  fields: [
    { key: 'no_document', label: 'No Dokumen', sortable: true },
    { key: 'type_doc_name', label: 'Jenis Mutasi', sortable: true },
    { key: 'date', label: 'Tanggal', sortable: true },
    { key: 'from_depo_name', label: 'Gudang/Depo Pengirim', sortable: true },
    { key: 'dst_depo_name', label: 'Gudang/Depo Tujuan', sortable: true },
    { key: 'validation_key_1', label: 'Verif Permintaan/Retur', sortable: true },
    { key: 'validation_key_2', label: 'Verif Pengirim', sortable: true },
    { key: 'validation_key_3', label: 'Verif Penerima', sortable: true },
    { key: 'flag_veriflag_verification_name', label: 'Status', sortable: true },
    { key: 'action', label: 'Aksi'},
  ],
  items: [],
  pageOptions: [20, 50, 100],
  sortOptions: [],
  options:{
    mutation:[]
  }
})

// getters
const getters = {}

// actions
const actions = {
  getParam({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `pharmacy/mutation/list`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamMutation', { ...Param })
        resolve(response.data.data)
      })
    })
  },
  getRef({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `pharmacy/mutation/refs`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamMutationRef', { ...Param })
        resolve(response.data.data)
      })
    })
  },
  getLowDepo ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {
        status: `pharmacy/mutation/low_depo/${id}`
      }
      useIPA.get(param).then(response => {
          resolve(response.data.data)
      })
    })
  },
  getItemDepo ({ commit }, data) {
		return new Promise((resolve, reject) => {
      let param = {
        status: `pharmacy/mutation/item_depo?pharmacy_depo_id=${data.pharmacy_depo_id}&pharmacy_catalog_id=${data.pharmacy_catalog_id}`
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
  
    if (params.no_document) {
      queryParams.push(`no_document=${params.no_document}`);
    }
  
    if (params.pharmacy_depo_send_id) {
      queryParams.push(`pharmacy_depo_send_id=${params.pharmacy_depo_send_id}`);
    }
  
    if (params.pharmacy_depo_recive_id) {
      queryParams.push(`pharmacy_depo_recive_id=${params.pharmacy_depo_recive_id}`);
    }
    
    let paramsall = queryParams.join('&');
  
    let param = {status: `pharmacy/mutation/list?${paramsall}`};
  
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
      let param = {status: 'pharmacy/mutation/detail/' + id}
      useIPA.get(param).then(response => {
          resolve(response.data.data)
      })
    })
  },
  formPostPut ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      console.log(formData)
      let url = 'pharmacy/mutation/create'
      if (formData.id) {
        url = 'pharmacy/mutation/update'
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
      const param = { rawData: rawData, url: 'pharmacy/mutation/delete', id: id }
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
  setParamMutation (state, data) {
    state.options.mutation = []
    for (let i = 0; i < data.rows.rows.length; i++) {
      const option = []
      for (const key in data.rows.rows[i]) {
        if (key === 'id') { option['value'] = data.rows.rows[i][key] } else if (key === 'mutation_items') { option['mutation'] = data.rows.rows[i] } else if (key === 'no_document') { option['title'] = data.rows.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows.rows[i][key] }
      }
      state.options.mutation.push(Object.assign({}, option))
    }
  },
  setParamMutationRef (state, data) {
    state.options.mutation = []
    for (let i = 0; i < data.rows.rows.length; i++) {
      const option = []
      if (data.rows.rows[i]['validation_key_1']) {
        for (const key in data.rows.rows[i]) {
          if (key === 'id') { option['value'] = data.rows.rows[i][key] } else if (key === 'mutation_items') { option['mutation'] = data.rows.rows[i] } else if (key === 'no_document') { option['title'] = data.rows.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows.rows[i][key] }
        }
        state.options.mutation.push(Object.assign({}, option))
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
