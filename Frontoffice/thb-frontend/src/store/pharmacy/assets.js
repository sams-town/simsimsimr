import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  start: 20,
  currentPage: 1,
  totalRows: 1,
  fields: [
    { key: 'id', label: 'ID Reff', sortable: true },
    { key: 'flag', label: 'Desc Reff', sortable: true },
    { key: 'depo_name', label: 'Gudang/Apotik/Satelit', sortable: true },
    { key: 'catalog_name', label: 'Katalog', sortable: true },
    { key: 'in_amount', label: 'Jml. Stok (+)', sortable: true },
    { key: 'out_amount', label: 'Jml. Stok (-)', sortable: true },
    { key: 'prev_amount', label: 'Jml. Sebelum', sortable: true },
    { key: 'current_amount', label: 'Jml. Sesudah', sortable: true },
    { key: 'action', label: 'Aksi'},
  ],

  fieldsPrice: [
    { key: 'id', label: 'ID Reff', sortable: true },
    { key: 'depo_name', label: 'Gudang/Apotik/Satelit', sortable: true },
    { key: 'catalog_name', label: 'Katalog', sortable: true },
    { key: 'hna_item', label: 'HNA', sortable: true },
    { key: 'hja_item_pb', label: 'DISC', sortable: true },
    { key: 'hp_item', label: 'HP', sortable: true },
    { key: 'action', label: 'Aksi'},
  ],

  CardFields: [
    { key: 'flag', label: 'TYPE TRANSAKSI', sortable: true },
    { key: 'batch', label: 'Batch&ED', sortable: true },
    { key: 'flag_desc', label: 'DETAIL TRANSAKSI', sortable: true },
    { key: 'date_created', label: 'TANGGAL TRANSAKSI', sortable: true },
    { key: 'catalog_name', label: 'KATALOG', sortable: true },
    { key: 'prev_amount', label: 'AWAL', sortable: true },
    { key: 'in_amount', label: 'MASUK', sortable: true },
    { key: 'out_amount', label: 'KELUAR', sortable: true },
    { key: 'current_amount', label: 'AKHIR', sortable: true },
  ],

  MonitorFields: [
    { key: 'depo_name', label: 'Gudang/Apotik/Satelit', sortable: true },
    { key: 'catalog_name', label: 'Katalog', sortable: true },
    { key: 'admin_amount', label: 'Jml. Stok', sortable: true },
    { key: 'physical_amount', label: 'Jml. Fisik', sortable: true },
    { key: 'prev_amount', label: 'Jml. Sebelum', sortable: true },
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
  getCard ({ commit }, params) {
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
  
    if (params.start) {
      queryParams.push(`start_date=${params.start}`);
    }
  
    if (params.end) {
      queryParams.push(`end_date=${params.end}`);
    }
  
    if (params.pharmacy_depo_id) {
      queryParams.push(`pharmacy_depo_id=${params.pharmacy_depo_id}`);
    }
  
    if (params.pharmacy_catalog_id) {
      queryParams.push(`pharmacy_catalog_id=${params.pharmacy_catalog_id}`);
    }
    
    let paramsall = queryParams.join('&');
  
    let param = {status: `pharmacy/assets/card?${paramsall}`};
  
    useIPA.get(param).then(response => {
      const catalog = {
        rows: response.data.data
      };
      commit('setData', {...catalog});
    });
  },
  getCardPrice ({ commit }, params) {
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
  
    if (params.pharmacy_depo_id) {
      queryParams.push(`pharmacy_depo_id=${params.pharmacy_depo_id}`);
    }
  
    if (params.pharmacy_catalog_id) {
      queryParams.push(`pharmacy_catalog_id=${params.pharmacy_catalog_id}`);
    }
    
    let paramsall = queryParams.join('&');
  
    let param = {status: `pharmacy/assets/price?${paramsall}`};
  
    useIPA.get(param).then(response => {
      const catalog = {
        rows: response.data.data
      };
      commit('setData', {...catalog});
    });
  },
  getMonitoring ({ commit }, params) {
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
  
    if (params.pharmacy_depo_id) {
      queryParams.push(`pharmacy_depo_id=${params.pharmacy_depo_id}`);
    }
  
    if (params.pharmacy_catalog_id) {
      queryParams.push(`pharmacy_catalog_id=${params.pharmacy_catalog_id}`);
    }
  
    if (params.name) {
      queryParams.push(`name=${params.name}`);
    }
    
    let paramsall = queryParams.join('&');
  
    let param = {status: `pharmacy/assets/monitoring?${paramsall}`};
  
    useIPA.get(param).then(response => {
      const catalog = {
        rows: response.data.data
      };
      commit('setDataMonitoring', {...catalog});
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
      const param = { rawData: rawData, url: 'pharmacy/catalog/delete', id: id }
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
    state.items = catalog.rows ? catalog.rows.rows : []
    state.totalRows = catalog.rows ? catalog.rows.iTotal : 1
    state.start = catalog.rows ? catalog.rows.start : 20
  },
  setDataMonitoring (state, catalog) {
    state.fields = [
      { key: 'depo_name', label: 'Gudang/Apotik/Satelit', sortable: true },
      { key: 'catalog_name', label: 'Katalog', sortable: true },
      { key: 'admin_amount', label: 'Jml. Stok', sortable: true },
      { key: 'physical_amount', label: 'Jml. Fisik', sortable: true },
      { key: 'prev_amount', label: 'Jml. Sebelum', sortable: true },
    ]
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
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
