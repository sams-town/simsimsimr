import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  start: 20,
  currentPage: 1,
  totalRows: 1,
  fields: [
    { key: 'no_document', label: 'No Dokumen', sortable: true },
    { key: 'no_document_purchase', label: 'No PO', sortable: true },
    { key: 'pharmacy_depo_name', label: 'Gudang/Depo', sortable: true },
    { key: 'no_invoice', label: 'Faktur', sortable: true },
    { key: 'payment_method_name', label: 'Cara Bayar', sortable: true },
    { key: 'supplier_name', label: 'Supplier', sortable: true },
    { key: 'ntotal', label: 'Nilai', sortable: true },
    { key: 'ndiscount', label: 'Nilai Diskon', sortable: true },
    { key: 'nfinish', label: 'Nilai Total', sortable: true },
    { key: 'validation_key_1', label: 'Status', sortable: true },
    { key: 'date_added', label: 'Waktu Buat', sortable: true },
    { key: 'action', label: 'Aksi'},
  ],
  items: [],
  pageOptions: [20, 50, 100],
  sortOptions: [],
  options:{
    catalog:[]
  }
})

// getters
const getters = {}

// actions
const actions = {
  getParam({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `pharmacy/catalog/detail`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        // if (params === 'catalog') {
          commit('setParamCatalog', { ...Param })
        // }
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
  
    if (params.no_document_po) {
      queryParams.push(`no_document_po=${params.no_document_po}`);
    }
  
    if (params.supplier_id) {
      queryParams.push(`supplier_id=${params.supplier_id}`);
    }
  
    if (params.pharmacy_catalog_id) {
      queryParams.push(`pharmacy_catalog_id=${params.pharmacy_catalog_id}`);
    }
  
    if (params.date_start) {
      queryParams.push(`date_start=${params.date_start}`);
    }
  
    if (params.date_end) {
      queryParams.push(`date_end=${params.date_end}`);
    }
  
    if (params.no_document) {
      queryParams.push(`no_document=${params.no_document}`);
    }
  
    if (params.name) {
      queryParams.push(`name=${params.name}`);
    }
    
    let paramsall = queryParams.join('&');
  
    let param = {status: `pharmacy/receipt/list?${paramsall}`};
  
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
      let param = {status: 'pharmacy/receipt/detail/' + id}
      useIPA.get(param).then(response => {
          resolve(response.data.data)
      })
    })
  },
  getPrice ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let param = {status: 'pharmacy/assets/price_ref?pharmacy_catalog_id=' + params}
      useIPA.get(param).then(response => {
          resolve(response.data.data)
      })
    })
  },
  formPostPut ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      console.log(formData)
      let url = 'pharmacy/receipt/create'
      if (formData.id) {
        url = 'pharmacy/receipt/update'
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
  formPostPutAccurate ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      console.log(formData)
      const param = { rawData: formData, url: 'pharmacy/receipt/accurate', id: formData.id }
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
      const param = { rawData: rawData, url: 'pharmacy/receipt/delete', id: id }
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
  setData (state, receipt) {
    state.items = receipt.rows.rows
    state.totalRows = receipt.rows.iTotal
    state.start = receipt.rows.start
  },
  setParamCatalog (state, data) {
    state.options.catalog = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = []
      for (const key in data.rows[i]) {
        if (key === 'id') { option['value'] = data.rows[i][key] } else if (key === 'is_delete') { option['data_catalog'] = data.rows[i] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows[i][key] }
      }
      state.options.catalog.push(Object.assign({}, option))
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
