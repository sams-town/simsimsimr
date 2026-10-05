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
  itemstransaction: []
})

// getters
const getters = {}

// actions
const actions = {
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
    let param = {status: 'tr/transaction_bhp/detail?registration_destination_id=' + id}
    useIPA.get(param).then(response => {
      const transaction = {
        rows : response.data.data
      }
      commit('setDataTransaction', {...transaction})
    })
  },
  getByRegistrationIdTransaction ({ commit }, id) {
    let param = {status: 'tr/transaction_bhp/detail?registration_id=' + id}
    useIPA.get(param).then(response => {
      const transaction = {
        rows : response.data.data
      }
      commit('setDataTransaction', {...transaction})
    })
  },
  // getByIdTransaction ({ commit }, id) {
  //   let param = {status: 'tr/transaction_tool/detail'}
  //   useIPA.get(param).then(response => {
  //     const transaction = {
  //       rows : response.data.data
  //     }
  //     commit('setDataTransaction', {...transaction})
  //   })
  // },
  formPostPut ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      console.log(rawData)
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

      let url = 'tr/transaction_bhp/create'
      if (rawData.view === 'edit') {
        url = 'tr/transaction_bhp/update'
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
  // formPostPut ({ commit }, formData) {
	// 	return new Promise((resolve, reject) => {
  //     const rawData = new FormData()
  //     rawData.set('name', formData.text_1)
  //     rawData.set('desc', formData.text_2)
  //     if (formData.setImage === 'upload') {
  //       rawData.append('icon', formData.icon)
	// 		} else {
  //       rawData.set('icon', formData.icon)
	// 		}
  //     const param = { rawData: rawData, url: 'backoffice/master/bank', id: formData.id}
  //     useIPA.postput(param)
	// 		.then(response => {
  //       router.push({ name: 'app-bank' })
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
  remove ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'tr/transaction_bhp/delete', id: id }
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
