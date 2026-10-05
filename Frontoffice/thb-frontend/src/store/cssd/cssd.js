import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  start: 20,
  currentPage: 1,
  totalRows: 1,
  fields: [
    { key: 'no', label: 'No' },
    { key: 'action', label: 'ACT' },
    { key: 'transaction_number', label: 'No Transaksi' },
    { key: 'date', label: 'Tanggal' },
    { key: 'sender', label: 'Pengirim' },
    { key: 'receiver_unit', label: 'Unit Penerima' },
    { key: 'send_verification', label: 'Verif Pengiriman' },
    { key: 'receiver_verification', label: 'Verif Penerima' },
    { key: 'transaction_status', label: 'Status Transaki' },
    { key: 'transaction_type', label: 'Jenis Transaki' },
    { key: 'user_updt', label: 'User Update' },
    { key: 'last_updt', label: 'Last Update' },
  ],
  items: [
    {
      no: 1,
      action: 'action',
      transaction_number: 'TRX-001',
      date: '2020-01-01 10:00:00',
      sender: 'Depo Rawat Jalan',
      receiver_unit: 'Gudang farmasi',
      send_verification: 'Terkirim',
      receiver_verification: 'Diterima',
      transaction_status: 'Terpenuhi / Diproses',
      transaction_type: 'Dikirimkan / Dikembalikan',
      user_updt: 'Farmasi poli',
      last_updt: '2020-01-01 10:00:00',
    }
  ],
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
  // getParam({ commit }, params) {
  //   return new Promise((resolve, reject) => {
  //     let param = {
  //       status: `pharmacy/catalog/detail`
  //     }
  //     useIPA.get(param).then((response) => {
  //       const Param = {
  //         rows: response.data.data
  //       }
  //       commit('setParamCatalog', { ...Param })
  //       resolve(response.data.data)
  //     })
  //   })
  // },
  // getAll ({ commit }, params) {
  //   let queryParams = [];
  
  //   if (params.offset) {
  //     queryParams.push(`offset=${params.offset}`);
  //   } else if (params.offset === 0) {
  //     queryParams.push(`offset=${params.offset}`);
  //   }
  
  //   if (params.limit) {
  //     queryParams.push(`limit=${params.limit}`);
  //   }
  
  //   if (params.code) {
  //     queryParams.push(`code=${params.code}`);
  //   }
  
  //   if (params.name) {
  //     queryParams.push(`name=${params.name}`);
  //   }
    
  //   let paramsall = queryParams.join('&');
  
  //   let param = {status: `pharmacy/catalog/list?${paramsall}`};
  
  //   useIPA.get(param).then(response => {
  //     const catalog = {
  //       rows: response.data.data
  //     };
  //     commit('setData', {...catalog});
  //   });
  // },
  // getById ({ commit }, id) {
	// 	return new Promise((resolve, reject) => {
  //     let param = {status: 'master/bank/' + id}
  //     useIPA.get(param).then(response => {
  //         resolve(response.data.data)
  //     })
  //   })
  // },
  // formPostPut ({ commit }, formData) {
	// 	return new Promise((resolve, reject) => {
  //     let url = 'pharmacy/catalog/create'
  //     if (formData.id) {
  //       url = 'pharmacy/catalog/update'
  //     }
  //     const param = { rawData: formData, url: url, id: formData.id }
  //     useIPA.postput(param)
	// 		.then(response => {
  //       if (response.data.hasErrors) {
  //         reject(response.data.message)
  //       } else {
  //         resolve(response.data)
  //       }
  //     },
  //     error => {
  //       if (error.response.data.hasOwnProperty('hasErrors')) {
  //         reject(error.response.data.message)
  //       } else {
  //         reject(error.message)
  //       }
  //     })
	// 	})
	// },
  // remove ({ commit }, id) {
	// 	return new Promise((resolve, reject) => {
  //     const rawData = {
  //       is_delete: 1,
	// 		}
  //     const param = { rawData: rawData, url: 'pharmacy/catalog/delete', id: id }
  //     useIPA.delete(param)
	// 		.then(response => {
  //       if (response.data.hasErrors) {
  //         reject(response.data.message)
  //       } else {
  //         resolve(response.data)
  //       }
  //     },
  //     error => {
  //       if (error.response.data.hasOwnProperty('hasErrors')) {
  //         reject(error.response.data.message)
  //       } else {
  //         reject(error.message)
  //       }
  //     })
	// 	})
	// }
}

// mutations
const mutations = {
  setData (state, catalog) {
    state.items = catalog.rows.rows
    state.totalRows = catalog.rows.iTotal
    state.start = catalog.rows.start
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
