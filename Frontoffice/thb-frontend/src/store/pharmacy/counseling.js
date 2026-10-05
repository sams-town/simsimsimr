import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  start: 20,
  currentPage: 1,
  totalRows: 1,
  fields: [
    { key: 'code_reg', label: 'No Registrasi', sortable: true },
    { key: 'norm', label: 'No.RM', sortable: true },
    { key: 'patient_name', label: 'Nama Pasien', sortable: true },
    { key: 'birth_date', label: 'Tgl Lahir', sortable: true },
    { key: 'pharmacist_name', label: 'Petugas', sortable: true },
    { key: 'date', label: 'Tanggal', sortable: true },
    { key: 'diagnosis', label: 'Diagnosa', sortable: true },
    { key: 'allergy', label: 'Alergi', sortable: true },
    { key: 'previous_counseling', label: 'Pernah konseling', sortable: true },
    { key: 'drug_dose_usage', label: 'Nama Obat, Dosis', sortable: true },
    { key: 'action', label: 'Aksi'},
  ],
  items: [],
  pageOptions: [20, 50, 100],
  sortOptions: [],
  options:{
    couselling:[]
  }
})

// getters
const getters = {}

// actions
const actions = {
  getParam({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `pharmacy/counselling/list`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamCounselling', { ...Param })
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
  
    let param = {status: `pharmacy/counselling/list?${paramsall}`};
  
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
  
    let param = {status: `report/pharmacy/counselling/view?${paramsall}`};
  
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
      let param = {status: 'pharmacy/counselling/detail/' + id}
      useIPA.get(param).then(response => {
          resolve(response.data.data)
      })
    })
  },
  getStockToReduction ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let param = {status: 'pharmacy/counselling/stock?pharmacy_depo_id=' + params.pharmacy_depo_id +'&pharmacy_catalog_id='+ params.pharmacy_catalog_id }
      useIPA.get(param).then(response => {
          resolve(response.data.data)
      })
    })
  },
  formPostPut ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      let url = 'pharmacy/counselling/create'
      if (formData.id != "") {
        url = 'pharmacy/counselling/update'
      }
      const param = { rawData: formData, url: url, id: formData.id  }
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
      const param = { rawData: rawData, url: 'pharmacy/counselling/delete', id: id }
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
  setParamCounselling (state, data) {
    state.options.counselling = []
    for (let i = 0; i < data.rows.rows.length; i++) {
      const option = []
      for (const key in data.rows.rows[i]) {
        if (key === 'id') { option['value'] = data.rows.rows[i][key] } else if (key === 'counselling_items') { option['counselling'] = data.rows.rows[i] } else if (key === 'no_document') { option['title'] = data.rows.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows.rows[i][key] }
      }
      state.options.counselling.push(Object.assign({}, option))
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
