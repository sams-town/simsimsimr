import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  perPage: 20,
  pageOptions: [20, 50, 100],
  totalRows: 1,
  sortOptions: [],
  fields: [
    { key: 'type_how_to_enter_id', label: 'Asal', sortable: true },
    { key: 'name', label: 'Nama Faskes', sortable: true },
    { key: 'address', label: 'Alamat', sortable: true },
    { key: 'pic', label: 'Nama PIC', sortable: true },
    { key: 'phone', label: 'Telepon', sortable: true },
    { key: 'email', label: 'Email', sortable: true },
    { key: 'status', label: 'Status', sortable: true },
    { key: 'action', label: 'Aksi' },
  ],
  items: [],
  options: {
    howtoenter: []
  }
})

// getters
const getters = {}

// actions
const actions = {
  getParam({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `setting/how_to_enter/detail?type_how_to_enter_id=${params}`
      }

      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamHowToEnter', { ...Param })
      })
    })
  },

  getAll({ commit }, params) {
    // let param = { status: 'master/banks/status/AC' }
    // useIPA.get(param).then(response => {
    //   const bank = {
    //     rows: response.data.data
    //   }
    //   commit('setData', { ...bank })
    // })

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

    if (params.type_how_to_enter_id) {
      queryParams.push(`type_how_to_enter_id=${params.type_how_to_enter_id}`);
    }


    let paramsall = queryParams.join('&');

    let param = { status: `setting/how_to_enter/list?${paramsall}` };

    useIPA.get(param).then(response => {
      const howToEnter = {
        rows: response.data.data
      };

      commit('setData', { ...howToEnter });
    });


  },
  getById({ commit }, id) {
    return new Promise((resolve, reject) => {
      let param = { status: 'setting/how_to_enter/detail/' + id }
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  // formPostPut({ commit }, formData) {
  //   return new Promise((resolve, reject) => {
  //     const rawData = {
  //       type_how_to_enter_id: formData.type_how_to_enter_id,
  //       name: formData.name,
  //       phone: formData.phone,
  //       email: formData.email,
  //       password: formData.repassword,
  //     }
  //     const param = { rawData: rawData, url: 'initial/register', id: null }
  //     useIPA.postput(param)
  //       .then(response => {
  //         router.push({ name: 'login' })
  //         if (response.data.hasErrors) {
  //           reject(response.data.message)
  //         } else {
  //           resolve(response.data)
  //         }
  //       },
  //         error => {
  //           if (error.response.data.hasOwnProperty('hasErrors')) {
  //             reject(error.response.data.message)
  //           } else {
  //             reject(error.message)
  //           }
  //         })
  //   })
  // },
  formPostPut({ commit }, formData) {
    return new Promise((resolve, reject) => {
      let url = 'setting/how_to_enter/create'
      if (formData.id) {
        url = 'setting/how_to_enter/update'
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

  // remove({ commit }, id) {
  //   return new Promise((resolve, reject) => {
  //     const param = { url: 'backoffice/master/bank', id: id }
  //     useIPA.delete(param)
  //       .then(response => {
  //         if (response.data.hasErrors) {
  //           reject(response.data.message)
  //         } else {
  //           resolve(response.data)
  //         }
  //       },
  //         error => {
  //           if (error.response.data.hasOwnProperty('hasErrors')) {
  //             reject(error.response.data.message)
  //           } else {
  //             reject(error.message)
  //           }
  //         })
  //   })
  // }
  remove({ commit }, id) {
    return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
      }
      const param = { rawData: rawData, url: 'setting/how_to_enter/delete', id: id }
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
  setData(state, howtoenter) {
    state.items = howtoenter.rows.rows
    state.totalRows = howtoenter.rows.iTotal
  },
  setParamHowToEnter(state, data) {
    state.options.howtoenter = []
    const option = []
    for (let i = 0; i < data.rows.length; i++) {
      for (const key in data.rows[i]) {
        if (key === 'id') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows[i][key] }
      }
      state.options.howtoenter.push(Object.assign({}, option))
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
