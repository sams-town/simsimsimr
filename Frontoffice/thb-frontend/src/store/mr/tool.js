import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  perPage: 20,
  pageOptions: [20, 50, 100],
  totalRows: 1,
  sortOptions: [],
  fields: [
    { key: 'norm', label: 'No RM', sortable: true },
    { key: 'name', label: 'Nama Pasien'},
    { key: 'sex', label: 'Jenis Kelamin'},
    { key: 'birth_place', label: 'Tempat Lahir'},
    { key: 'birth_date', label: 'Tanggal Lahir'},
    { key: 'address', label: 'Alamat'},
    { key: 'action', label: 'Aksi'}
  ],
  items: [],
  fieldstransaction: [
    { key: 'date_added', label: 'Tanggal & Jam', sortable: true },
    { key: 'nurse_id', label: 'Nama Pasien'},
    { key: 'costing_name', label: 'Nama Pasien'},
    { key: 'notes', label: 'Keterangan'},
    { key: 'var_1', label: 'Harga'},
    { key: 'action', label: 'Aksi'}
  ],
  itemstransaction: [],
  tool: {
    items: '',
    totalRows: '',
  },
})

// getters
const getters = {}

// actions
const actions = {
  getToolTransactionStateList ({ commit }, params) {
    let queryParams = [];

    if (params.offset) {
      queryParams.push(`offset=${params.offset}`);
    } else if (params.offset === 0) {
      queryParams.push(`offset=${params.offset}`);
    }

    if (params.limit) {
      queryParams.push(`limit=${params.limit}`);
    } else if (params.limit === 0) {
      queryParams.push(`limit=${params.limit}`);
    }

    if (params.norm) {
      queryParams.push(`norm=${params.norm}`);
    }

    if (params.registration_id) {
      queryParams.push(`registration_id=${params.registration_id}`);
    }

    let paramsall = queryParams.join('&')
    let param = {status: `tr/transaction_alkes/list?${paramsall}`}

    useIPA.get(param).then(response => {
      const data = response.data.data
      commit('setToolTransaction', {...data})
    })
  },
  getAll ({ commit}, id ) {
    let param = {status: 'mr/nurse/detail?registration_destination_id='  + id}
    useIPA.get(param).then(response => {
      const patient = {
        rows : response.data.data
      }
      commit('setData', {...patient})
    })
  },
  getAllResolve ({ commit}, id) {
		return new Promise((resolve, reject) => {
    let param = {status: 'mr/nurse/detail?registration_destination_id='  + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getByNurseId ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'mr/nurse/detail?nurse_id=' + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getByNurseAdultId ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'mr/nurse_adult/detail?nurse_id=' + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getById ({ commit }, id) {
    let param = {status: 'mr/nurse/detail?registration_destination_id='  + id}
    useIPA.get(param).then(response => {
      const patient = {
        rows : response.data.data
      }
      commit('setData', {...patient})
    })
  },
  getByIdTransaction ({ commit }, id) {
    let param = {status: 'tr/transaction_alkes/detail?registration_destination_id=' + id}
    useIPA.get(param).then(response => {
      const transaction = {
        rows : response.data.data
      }
      commit('setDataTransaction', {...transaction})
    })
  },
  getByRegistrationIdTransaction ({ commit }, id) {
    let param = {status: 'tr/transaction_alkes/detail?registration_id=' + id}
    useIPA.get(param).then(response => {
      const transaction = {
        rows : response.data.data
      }
      commit('setDataTransaction', {...transaction})
    })
  },
  formPostPut ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      // const param = { rawData: rawData, url: 'xx', id: formData.id }
      const param = { rawData: rawData, url: 'mr/nurse/create_full', id: formData.id }
      useIPA.postput(param)
			.then(response => {
        // router.push({ name: 'RegisMasterView' })
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
  formPostPutTransaction ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      // const param = { rawData: rawData, url: 'xx', id: formData.id }

      let url = 'tr/transaction_alkes/create'
      if (rawData.view === 'edit') {
        url = 'tr/transaction_alkes/update'
      }
      const param = { rawData: rawData, url: url, id: rawData.id }
      useIPA.postput(param)
			.then(response => {
        // router.push({ name: 'RegisMasterView' })
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
  postPutTool ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      let url = formData.id ? 'tr/transaction_alkes/update' : 'tr/transaction_alkes/create'

      const param = { rawData: rawData, url: url, id: formData.id }
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
  removeTool ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'tr/transaction_alkes/delete', id: id }
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
        reject({message: response.data.message, debug: response.data.debug, response: response.data,})
      })
		})
	},
  remove ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'tr/transaction_alkes/delete', id: id }
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
  setData (state, patient) {
    state.items = patient.rows
    state.totalRows = patient.rows.length
  },
  setDataTransaction (state, transaction) {
    state.itemstransaction = []
    state.itemstransaction = transaction.rows.sort((a, b) => new Date(a.date_added) - new Date(b.date_added))
  },
  setToolTransaction (state, res) {
    state.tool.items = res.rows
    state.tool.totalRows = res.iTotal
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
