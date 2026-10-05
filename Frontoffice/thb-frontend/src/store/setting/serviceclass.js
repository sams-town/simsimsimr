import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  perPage: 20,
  pageOptions: [20, 50, 100],
  totalRows: 1,
  sortOptions: [],
  fields: [
    { key: 'name', label: 'Nama', sortable: true },
    { key: 'code', label: 'Kode', sortable: true },
    { key: 'is_delete', label: 'Status', sortable: true },
    { key: 'action', label: 'Aksi' },
  ],
  items: [],
  options: {
    serviceclass: [],
  }
})

// getters
const getters = {}

// actions
const actions = {
  getParam({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `setting/service_class/detail?type_module_id=2`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        if (params === 'serviceclass') {
          commit('setParamserviceclass', { ...Param })
        }
      })
    })
  },
  getDetail({ commit }) {
    let param = { status: 'setting/service_class/detail' }
    useIPA.get(param).then(response => {
      const serviceclass = {
        rows: response.data.data
      }
      commit('setData', { ...serviceclass })
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

    if (params.name) {
      queryParams.push(`name=${params.name}`);
    }

    if (params.code) {
      queryParams.push(`code=${params.code}`);
    }

    let paramsall = queryParams.join('&');

    let param = { status: `setting/service_class/list?${paramsall}` };

    useIPA.get(param).then(response => {
      const serviceClass = {
        rows: response.data.data
      };
      commit('setDataGetAll', { ...serviceClass });
    });
  },

  getById({ commit }, id) {
    return new Promise((resolve, reject) => {
      let param = { status: 'master/bank/' + id }
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  formPostPut({ commit }, formData) {
    return new Promise((resolve, reject) => {
      const rawData = {
        code: formData.code,
        name: formData.name,
      }
      let url = 'setting/service_class/create'
      if (formData.id) {
        url = 'setting/service_class/update'
      }
      const param = { rawData: rawData, url: url, id: formData.id }
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
  formPostPutMaster({ commit }, formData) {
    return new Promise((resolve, reject) => {
      const rawData = {
        name: formData.name,
        type_module_id: formData.type_module_id,
      }
      let url = 'setting/service_class/create'
      if (formData.id) {
        url = 'setting/service_class/update'
      }
      const param = { rawData: rawData, url: url, id: formData.id }
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
      const param = { rawData: rawData, url: 'setting/service_class/delete', id: id }
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
}

// mutations
const mutations = {
  setData(state, serviceclass) {
    state.items = serviceclass.rows
    state.totalRows = serviceclass.rows.length
    state.options.serviceclass = []
    const option = []
    for (let i = 0; i < serviceclass.rows.length; i++) {
      for (const key in serviceclass.rows[i]) {
        if (key === 'id') { option['value'] = serviceclass.rows[i][key] } else if (key === 'name') { option['title'] = serviceclass.rows[i][key] } else if (key === 'id') { option['oid'] = serviceclass.rows[i][key] }
      }
      state.options.serviceclass.push(Object.assign({}, option))
    }
  },

  setDataGetAll(state, serviceclass) {
    state.items = serviceclass.rows.rows
    state.totalRows = serviceclass.rows.iTotal
    state.start = serviceclass.rows.start
  },
  setParamHealthFacility(state, data) {
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
