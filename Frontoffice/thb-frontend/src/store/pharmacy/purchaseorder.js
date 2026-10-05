import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  start: 20,
  currentPage: 1,
  totalRows: 1,
  fields: [
    { key: 'no_document', label: 'No Dokumen', sortable: true },
    { key: 'po_type_name', label: 'Tipe PO', sortable: true },
    { key: 'payment_method_name', label: 'Cara Bayar', sortable: true },
    { key: 'pharmacy_depo_name', label: 'Gudang/Depo', sortable: true },
    { key: 'supplier_name', label: 'Supplier', sortable: true },
    { key: 'date', label: 'Tgl.PO', sortable: true },
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
    purchase:[]
  }
})

// getters
const getters = {}

// actions
const actions = {
  getParam({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `pharmacy/purchase_order/list`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamPurchaseOrder', { ...Param })
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
    
    let paramsall = queryParams.join('&');
  
    let param = {status: `pharmacy/purchase_order/list?${paramsall}`};
  
    useIPA.get(param).then(response => {
      const catalog = {
        rows: response.data.data
      };
      commit('setData', {...catalog});
    });
  },
  getView ({ commit }, params) {
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
    
    let paramsall = queryParams.join('&');
  
    let param = {status: `report/pharmacy/purchase_order/view?${paramsall}`};
  
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
      let param = {status: 'pharmacy/purchase_order/detail/' + id}
      useIPA.get(param).then(response => {
          resolve(response.data.data)
      })
    })
  },
  formPostPut ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      console.log(formData)
      let url = 'pharmacy/purchase_order/create'
      if (formData.id) {
        url = 'pharmacy/purchase_order/update'
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
      const param = { rawData: rawData, url: 'pharmacy/purchase_order/delete', id: id }
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
  setParamPurchaseOrder (state, data) {
    state.options.purchase = []
    for (let i = 0; i < data.rows.rows.length; i++) {
      const option = []
      for (const key in data.rows.rows[i]) {
        if (key === 'id') { option['value'] = data.rows.rows[i][key] } else if (key === 'purchase_order_items') { option['purchase'] = data.rows.rows[i] } else if (key === 'no_document') { option['title'] = data.rows.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows.rows[i][key] }
      }
      state.options.purchase.push(Object.assign({}, option))
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
