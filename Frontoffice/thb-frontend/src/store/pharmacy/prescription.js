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
    { key: 'is_delete', label: 'Status', sortable: true },
    { key: 'action', label: 'Aksi'},
  ],
  items: [],
  pageOptions: [20, 50, 100],
  sortOptions: [],
  options:{
    catalog:[]
  },
  OutpatientTable: {
    fields: [
      { key: 'date_added', label: 'TANGGAL RESEP', thStyle: { minWidth: '140px' } },
      { key: 'depo_name', label: 'APOTIK' },
      { key: 'health_facility_item_name', label: 'POLIKLINIK/RUANGAN', thStyle: { minWidth: '100px' } },
      { key: 'doctor_in_charge_name', label: 'NAMA DOKTER', thStyle: { minWidth: '150px' } },
      { key: 'norm', label: 'NORM' },
      { key: 'patient_name', label: 'PASIEN', thStyle: { minWidth: '150px' } },
      { key: 'insurance_name', label: 'PENJAMIN', thStyle: { minWidth: '140px' } },
      { key: 'pharmacy_verif', label: 'STATUS', thStyle: { minWidth: '140px' } },
    ],
    fieldsFree: [
      { key: 'date_added', label: 'TANGGAL RESEP', thStyle: { minWidth: '140px' } },
      { key: 'depo_name', label: 'APOTIK' },
      { key: 'patient_name', label: 'PASIEN', thStyle: { minWidth: '150px' } },
      { key: 'pharmacy_verif', label: 'STATUS', thStyle: { minWidth: '140px' } },
    ],
    items: [],
    itemstransaction: [],
    totalRows: 1,
    pageOptions: [20, 50, 100]
  }
})

// getters
const getters = {}

// actions
const actions = {
  getPrescriptionListV2 ({ commit }, params) {
    return new Promise((resolve, reject) => {
      let queryParams = [];
    
      if (params.offset != undefined) {
        queryParams.push(`offset=${params.offset}`);
      }
    
      if (params.limit != undefined) {
        queryParams.push(`limit=${params.limit}`);
      }
    
      if (params.registration_destination_id) {
        queryParams.push(`registration_destination_id=${params.registration_destination_id}`);
      }
    
      if (params.list_type) {
        queryParams.push(`list_type=${params.list_type}`);
      }
      
      let paramsall = queryParams.join('&');
      let param = {status: `pharmacy/prescription/list_v2?${paramsall}`};
    
      useIPA.get(param)
        .then(response => {
          resolve(response.data.data)
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
  getOrder ({ commit }, params) {
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

    if (params.patient_name) {
      queryParams.push(`patient_name=${params.patient_name}`);
    }

    if (params.norm) {
      queryParams.push(`norm=${params.norm}`);
    }

    if (params.type_prescription_id) {
      queryParams.push(`type_prescription_id=${params.type_prescription_id}`);
    }

    if (params.flag) {
      queryParams.push(`flag=${params.flag}`);
    } else if (params.flag === 0) {
      queryParams.push(`flag=${params.flag}`);
    }

    if (params.pharmacy_depo_id) {
      queryParams.push(`pharmacy_depo_id=${params.pharmacy_depo_id}`);
    }

    if (params.date_start) {
      queryParams.push(`date_start=${params.date_start}`)
    }

    if (params.date_end) {
      queryParams.push(`date_end=${params.date_end}`)
    }

    if (params.name) {
      queryParams.push(`name=${params.name}`);
    }
  
    if (params.code) {
      queryParams.push(`code=${params.code}`);
    }
  
    if (params.farmasi) {
      queryParams.push(`farmasi=${params.farmasi}`);
    }
  
    let paramsall = queryParams.join('&');
  
    let param = {status: `pharmacy/prescription/list?${paramsall}`};
  
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
  getDetail ({ commit }, params) {
    return new Promise((resolve, reject) => {
      let queryParams = [];
  
      if (params.soap_id) {
        queryParams.push(`soap_id=${params.soap_id}`)
      }

      if (params.doctor_in_charge_id) {
        queryParams.push(`doctor_in_charge_id=${params.doctor_in_charge_id}`)
      }

      if (params.registration_destination_id) {
        queryParams.push(`registration_destination_id=${params.registration_destination_id}`)
      }

      if (params.type_prescription_id) {
        queryParams.push(`type_prescription_id=${params.type_prescription_id}`)
      }
      
      let paramsall = queryParams.join('&')
    
      let param = {status: `pharmacy/prescription/detail?${paramsall}`}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getById ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'pharmacy/prescription/detail/' + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getConcoct ({ commit }, prescription_id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'pharmacy/prescription/detail_concoct/' + prescription_id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getByNorm ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'pharmacy/prescription/detail?norm=' + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getByNormLimit ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'pharmacy/prescription/detail?norm=' + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getByNormRegister ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'pharmacy/prescription/registration?norm=' + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getPrescriptionItem ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'pharmacy/prescription/item/' + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  geCatalogItemMutasi ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let param = {status: 'pharmacy/prescription/catalog_mutasi?id=' + params.id + '&pharmacy_depo_id=' + params.pharmacy_depo_id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  geCatalogItem ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let param = {status: 'pharmacy/prescription/catalog?id=' + params.id + '&pharmacy_depo_id=' + params.pharmacy_depo_id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getByRegistrationIdTransaction ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'pharmacy/prescription/billing?registration_id=' + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  // getByRegistrationIdTransaction ({ commit }, id) {
  //   let param = {status: 'pharmacy/prescription/billing?registration_id=' + id}
  //   useIPA.get(param).then(response => {
  //     const transaction = {
  //       rows : response.data.data
  //     }
  //     commit('setDataTransaction', {...transaction})
  //   })
  // },
  getItemById ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];
      queryParams.push(`pharmacy_prescription_id=${params.pharmacy_prescription_id}`);
      queryParams.push(`pharmacy_catalog_id=${params.pharmacy_catalog_id}`);
      let paramsall = queryParams.join('&');
      let param = {status: `pharmacy/prescription_item/detail?${paramsall}`}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  formPostPut ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      let url = formData.id ? 'pharmacy/prescription/update' : 'pharmacy/prescription/create'

      const param = { rawData: rawData, url: url, id: rawData.id }
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
        reject({message: response.data.message, debug: response.data.debug, response})
      })
		})
	},
  // formPostPut ({ commit }, formData) {
	// 	return new Promise((resolve, reject) => {
  //     const rawData = formData

  //     let url = 'pharmacy/prescription/create'
  //     if (rawData.view === 'edit' || formData.id != '') {
  //       url = 'pharmacy/prescription/update'
  //     }
  //     const param = { rawData: rawData, url: url, id: rawData.id }
  //     useIPA.postput(param)
	// 		.then(response => {
  //       // router.push({ name: 'PatientAlkesView' })
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
  formPostPutTransaction ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      // const param = { rawData: rawData, url: 'xx', id: formData.id }

      let url = 'pharmacy/prescription/transaction'
      const param = { rawData: rawData, url: url, id: rawData.id }
      useIPA.postput(param)
			.then(response => {
        // router.push({ name: 'PatientAlkesView' })
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
  formPostPutTransactionDone ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      // const param = { rawData: rawData, url: 'xx', id: formData.id }

      let url = 'pharmacy/prescription/transactiondone'
      const param = { rawData: rawData, url: url, id: rawData.id }
      useIPA.postput(param)
			.then(response => {
        // router.push({ name: 'PatientAlkesView' })
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
  formPostPutRetur ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      // const param = { rawData: rawData, url: 'xx', id: formData.id }

      let url = 'pharmacy/prescription/retur'
      const param = { rawData: rawData, url: url, id: rawData.id }
      useIPA.postput(param)
			.then(response => {
        // router.push({ name: 'PatientAlkesView' })
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
  formPostPutAdd ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      // const param = { rawData: rawData, url: 'xx', id: formData.id }

      let url = 'pharmacy/prescription/add'
      const param = { rawData: rawData, url: url, id: rawData.id }
      useIPA.postput(param)
			.then(response => {
        // router.push({ name: 'PatientAlkesView' })
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
          console.log(response.data)
          router.push({ name: 'PharmacyRecipeOutpatientDetailView', params: { id: response.data.data}})
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
  formPostPutSubs ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      console.log(formData)
      let url = 'pharmacy/prescription/create'
      if (formData.id) {
        url = 'pharmacy/prescription/change_catalog'
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
	},

  removeOrder ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'pharmacy/prescription/delete', id: id }
      useIPA.delete(param)
      .then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
        return response;
      })
      .catch(({response}) => {
        reject({message: response.data.message, debug: response.data.debug, response})
      })
		})
	}
}

// mutations
const mutations = {
  setData (state, receipt) {
    state.OutpatientTable.items = receipt.rows.rows
    state.OutpatientTable.totalRows = receipt.rows.iTotal
    state.OutpatientTable.start = receipt.rows.start
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
  },
  setDataTransaction (state, transaction) {
    state.itemstransaction = []
    state.itemstransaction = transaction.rows
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
