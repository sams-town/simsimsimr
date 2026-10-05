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
  getAutoBed ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.type_room_id) {
        queryParams.push(`type_room_id=${params.type_room_id}`);
      }

      if (params.service_class_id) {
        queryParams.push(`service_class_id=${params.service_class_id}`);
      }

      if (params.doctor_in_charge_id) {
        queryParams.push(`doctor_in_charge_id=${params.doctor_in_charge_id}`);
      }

      if (params.icd10_code) {
        queryParams.push(`icd10_code=${params.icd10_code}`);
      }

      if (params.sex != undefined) {
        queryParams.push(`sex=${params.sex}`);
      }

      let paramsall = queryParams.join('&')
      let param = {status: `health_facility/room/find_bed?${paramsall}`}

      useIPA.get(param)
      .then(response => {
        resolve(response.data)
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

  postPutBed ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      
      let url = formData.id ? 'health_facility/bed/update': 'health_facility/bed/create'
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

  deleteBed ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'health_facility/bed/delete', id: id }
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

  getByIdTransaction ({ commit }, id) {
    let param = {status: 'tr/transaction_bed/detail?registration_destination_id=' + id}
    useIPA.get(param).then(response => {
      const transaction = {
        rows : response.data.data
      }
      commit('setDataTransaction', {...transaction})
    })
  },
  getByRegistrationIdTransaction ({ commit }, id) {
    let param = {status: 'tr/transaction_bed/detail?registration_id=' + id}
    useIPA.get(param).then(response => {
      const transaction = {
        rows : response.data.data
      }
      commit('setDataTransaction', {...transaction})
    })
  },
  formPostPutTransaction ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      // const param = { rawData: rawData, url: 'xx', id: formData.id }

      let url = 'tr/transaction_bed/create'
      if (rawData.view === 'edit') {
        url = 'tr/transaction_bed/update'
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
  async formPostPutCheckout({ commit }, obj) {
    return new Promise((resolve, reject) => {
      let rawData = {}
      rawData = {
        checkout_date: obj?.checkout_date,
        control_health_facility_item_id: obj?.health_facility_item_id,
        control_doctor_id: obj?.doctor_id,
        control_date: obj?.date_control,
        is_bpjs: obj?.is_bpjs,
        noSEP: obj?.noSEP,
        is_death: obj?.is_death,
        is_control: obj?.is_control,
        noSuratKontrol: obj?.noSuratKontrol
      }
      const param = { rawData: rawData, url: 'mr/registration_destination/checkout_request', id: obj.registration_destination_id };
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
  getControlBPJSByRegistrationDesId({ commit }, id) {
    return new Promise((resolve, reject) => {
      let param = {status: 'mr/control_bpjs/noka?registration_destination_id=' + id}
      useIPA.get(param).then(response => {
        resolve(response)
      })
    })
  },
  remove ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'tr/transaction_bed/delete', id: id }
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
