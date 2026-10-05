import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  start: 20,
  perPage: 20,
  pageOptions: [20, 50, 100],
  totalRows: 1,
  firstRow: 1,
  lastRow: 1,
  sortOptions: [],
  // fields: [
  //   { key: 'type_schedule_id', label: 'Jenis Tarif', sortable: true },
  //   { key: 'name', label: 'Nama', sortable: true },
  //   { key: 'service_class_id', label: 'Kelas Layanan', sortable: true },
  //   { key: 'var_1', label: 'Tarif', sortable: true },
  //   { key: 'is_delete', label: 'Status', sortable: true },
  //   { key: 'action', label: 'Aksi'},
  // ],
  items: [],
  options: {
    schedule: []
  }
})

// getters
const getters = {}

// actions
const actions = {
  getParam({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `medical_support/doctor_schedule/detail?doctor_id=${params}`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamSchedule', { ...Param })
      })
    })
  },
  getAll({ commit }, params) {
    let queryParams = [];

    if (params.offset) {
      queryParams.push(`offset=${params.offset}`);
    } else if (params.offset === 0) {
      queryParams.push(`offset=${params.offset}`);
    }

    if (params.limit) {
      queryParams.push(`limit=${params.limit}`);
    }

    let paramsall = queryParams.join('&');

    let param = { status: `medical_support/doctor_schedule/list?${paramsall}` };
    useIPA.get(param).then(response => {
      const schedule = {
        rows: response.data.data
      }
      commit('setListData', { ...schedule })
    })
  },
  getDetail({ commit }) {
    let param = { status: 'medical_support/doctor_schedule/detail' }
    useIPA.get(param).then(response => {
      const schedule = {
        rows: response.data.data
      }
      commit('setData', { ...schedule })
    })
  },
  getById({ commit }, id) {
    return new Promise((resolve, reject) => {
      let param = { status: 'medical_support/doctor_schedule/detail/' + id }
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getByDoctorId({ commit }, doctor_id) {
    return new Promise((resolve, reject) => {
      let param = { status: 'medical_support/doctor_schedule/detail/?doctor_id=' + doctor_id }
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  formPostPut({ commit }, formData) {
    return new Promise((resolve, reject) => {
      let url = 'medical_support/doctor_schedule/create'
      // if (formData.id) {
      //   url = 'medical_support/doctor_schedule/update'
      // }
      const param = { rawData: formData, url: url, id: '' }
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
  remove({ commit }, id) {
    return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
      }
      const param = { rawData: rawData, url: 'medical_support/doctor_schedule/delete', id: id }
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
  setData(state, schedule) {
    state.items = schedule.rows
    state.totalRows = schedule.rows.length
  },
  setListData(state, schedule) {
    state.items = schedule.rows.rows
    state.totalRows = schedule.rows.iTotal
    state.start = schedule.rows.start
  },
  setParamSchedule(state, data) {
    state.options.schedule = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = []
      for (const key in data.rows[i]) {
        if (key === 'id') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'is_delete') { option['schedule'] = data.rows[i]['var_1'] } else if (key === 'id') { option['oid'] = data.rows[i][key] }
      }
      state.options.schedule.push(Object.assign({}, option))
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
