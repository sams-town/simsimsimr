import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  start: 20,
  currentPage: 1,
  totalRows: 1,
  fields: [
    { key: 'code', label: 'Kode', sortable: true },
    { key: 'name', label: 'Katalog', sortable: true },
    { key: 'is_chronic', label: 'Obat Kronis', sortable: true },
    { key: 'is_antibiotics', label: 'Obat Antibiotik', sortable: true },
    { key: 'package_name', label: 'Satuan Kemasan Primer', sortable: true },
    { key: 'packaging_value', label: 'Kemasan Sediaan', sortable: true },
    { key: 'signatura_unit_name', label: 'Signatura Default', sortable: true },
    { key: 'signatura_times_name', label: 'Signatura Frekuensi', sortable: true },
    { key: 'pharmacy_catalog_instruction_name', label: 'Administer', sortable: true },
    { key: 'is_executive', label: 'Esekutif', sortable: true },
    { key: 'composition', label: 'Komposisi', sortable: true },
    { key: 'restriction', label: 'Restriksi', sortable: true },
    { key: 'package_name', label: 'Satuan', sortable: true },
    { key: 'suppliers_name', label: 'PBF', sortable: true },
    { key: 'factory_name', label: 'Pabrik', sortable: true },
    { key: 'is_delete', label: 'Status', sortable: true },
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
  postPutCatalogInstruction ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      let url = formData.id ? 'pharmacy/catalog_instruction/update' : 'pharmacy/catalog_instruction/create'
      const param = { rawData: formData, url: url, id: formData.id }
      useIPA.postput(param)
      .then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
        return response;
      })
      .catch(({response}) => {
        reject({message: response.data.message, debug: response.data.debug, response: response.data,})
      })
		})
	},

  deleteCatalogInstruction ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'pharmacy/catalog_instruction/delete', id: id }
      useIPA.delete(param)
			.then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }

        return response;
      })
      .catch(({error, response}) => {
        reject({message: response.data.message, debug: response.data.debug, response: response.data,})
      })
		})
	},

  getOptCatalogInstruction ({ commit }) {
		return new Promise((resolve, reject) => {          
      let param = {status: `pharmacy/catalog_instruction/list`};

      useIPA.get(param)
      .then( response => {
          let resData = response.data.data.rows
          let final_option = []

          for (let i = 0; i < resData.length; i++) {
            const el = resData[i];
            let temp = { value: el.id, title: el.name }
            final_option.push(temp)
          }

          const other_administer = 'ea3c2573-b9f5-428d-ada4-0ce6ea67c72f'
          const targetIndex = final_option.findIndex(item => item.value === other_administer)
          if (targetIndex !== -1) {
              const [targetItem] = final_option.splice(targetIndex, 1) // Hapus dari posisi lama & simpan objectnya
              final_option.unshift(targetItem) // Tambahin ke awal array
          }
          
          resolve(final_option)
          return response;
      })
      .catch(({response}) => {
        reject({
          message: response.data.message, 
          debug: response.data.debug, 
          response: response.data,
        })
      })
    })
  },

  getCatalogInstruction ({ commit }, { params, cancelToken }) {
		return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.offset != undefined) {
        queryParams.push(`offset=${params.offset}`);
      }
    
      if (params.limit) {
        queryParams.push(`limit=${params.limit}`);
      }

      if (params.name) {
        queryParams.push(`name=${params.name}`);
      }
          
      let paramsall = queryParams.join('&');
      let param = {status: `pharmacy/catalog_instruction/list?${paramsall}`};

      useIPA.get(param, { cancelToken })
      .then(response => {
        resolve(response.data.data);
      })
      .catch(error => {
        if (axios.isCancel(error)) {
          console.log("Request dibatalkan:", error.message);
        } else {
          reject(error);
        }
      });
    })
  },

  getCatalogReference ({ commit }, catalog_id) {
		return new Promise((resolve, reject) => {
      let param = {status: `pharmacy/catalog/get_signatura/${catalog_id}`}
      
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
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
  getParamSearch({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `pharmacy/catalog/detail?search=${params}`
      }
      useIPA.get(param).then((response) => {
        resolve(response.data.data)
      })
    })
  },
  getParamSearchNew({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `pharmacy/catalog/list_options?name=${params}`
      }
      useIPA.get(param).then((response) => {
        resolve(response.data.data.rows)
      })
    })
  },

  getOptionCatalog({ commit }, params) {
    return new Promise((resolve, reject) => {      
      // let queryParams = [];
      // if (params.is_executive) {
      //   queryParams.push(`is_executive=${params.is_executive}`);
      // }
      // let paramsall = queryParams.join('&');
      // let param = {status: `pharmacy/catalog/detail?${paramsall}`};
      let param = {status: `pharmacy/catalog/list_options`};

      useIPA.get(param).then((response) => {
        let resData = response.data.data.rows
        let final_option = []

        for (let i = 0; i < resData.length; i++) {
            const el = resData[i];
            let temp = {
              value: el.id,
              title: el.name ?? '',
              data_catalog: {
                  id: el.id,
                  code: el.code,
                  name: el.name,
                  composition: el.composition ?? '',
                  package_id: el.package_id,
                  dose: el.dose,
                  dose_name: el.dose_name,
                  package_name: el.package_name,
                  signatura_dose: el.signatura_dose,
                  signatura_unit_id: el.signatura_unit_id,
                  signatura_times_id: el.signatura_times_id,
                  instruction_id: el.pharmacy_catalog_instruction_id,
                  instruction_name: el.pharmacy_catalog_instruction_name,
              },
            }
            final_option.push(temp)
        }
        resolve(final_option)
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
  
    let param = {status: `pharmacy/catalog/list?${paramsall}`};
  
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
      let url = 'pharmacy/catalog/create'
      if (formData.id) {
        url = 'pharmacy/catalog/update'
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
    state.items = catalog.rows.rows
    state.totalRows = catalog.rows.iTotal
    state.start = catalog.rows.start
  },
  
  setParamCatalog (state, data) {
    state.options.catalog = []
    for (let i = 0; i < data.rows.length; i++) {
      const element = data.rows[i]

      let temp_obj = {
        value: element.id,
        title: element.name,
        data_catalog: element,
      }
      state.options.catalog.push(Object.assign({}, temp_obj))
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
