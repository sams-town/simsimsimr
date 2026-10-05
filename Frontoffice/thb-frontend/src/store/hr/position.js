import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  perPage: 20,
  pageOptions: [20, 50, 100],
  totalRows: 1,
  firstRow: 1,
  lastRow: 1,
  totalRows: 1,
  sortOptions: [],
  fields: [
    { key: 'health_facility_item_name', label: 'Unit Kerja Sub', sortable: true },
    { key: 'name', label: 'Jabatan', sortable: true },
    { key: 'is_delete', label: 'Status', sortable: true },
    { key: 'action', label: 'Aksi'},
  ],
  items: [],
  options:{
    position:[]
  }
})

// getters
const getters = {}

// actions
const actions = {
  getPositionList ({ commit }, params) {
      return new Promise((resolve, reject) => {
          let queryParams = [];
          let specialQueryParams = [];
          // console.log("getPositionList", params.params)
          if (params.params.position_id) {
            specialQueryParams.push(`/${params.params.position_id}`);
          } 

          if (params.params.offset) {
              queryParams.push(`offset=${params.params.offset}`);
          } else if (params.params.offset === 0) {
              queryParams.push(`offset=${params.params.offset}`);
          }

          if (params.params.limit) {
              queryParams.push(`limit=${params.params.limit}`);
          } else if (params.params.limit === 0) {
              queryParams.push(`limit=${params.params.limit}`);
          }
          
          if (params.params.department_id) {
            queryParams.push(`department_id=${params.params.department_id}`);
          } 

          if (params.params.health_facility_item_id) {
            queryParams.push(`health_facility_item_id=${params.params.health_facility_item_id}`);
          } 

          let paramsall = queryParams.join('&');
          let param = {status: `hr/position/list${specialQueryParams}?${paramsall}`};

          useIPA.get(param).then(response => {
            resolve(response.data.data);
          //   const parameter = {
          //     rows : response.data.data
          //   }
          //   commit('setData', {...parameter})
          })
      })
  },
  getPositionById ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'hr/position/detail/' + id}
      useIPA.get(param).then(response => {
          resolve(response.data.data)
      })
    })
  },
  getOptPosition ({ commit }) {
		return new Promise((resolve, reject) => {          
      let param = {status: 'hr/position/detail'};

      useIPA.get(param)
      .then( response => {
          let resData = response.data.data
          let final_option = []

          for (let i = 0; i < resData.length; i++) {
            const el = resData[i];
            let temp = { value: el.id, title: el.name }
            final_option.push(temp)
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
  getPosition({ commit }, params) {
      return new Promise((resolve, reject) => {
          let queryParams = [];
          
          if (params.health_facility_item_id) {
              queryParams.push(`health_facility_item_id=${params.health_facility_item_id}`);
          }

          let paramsall = queryParams.join('&');

          let param = { status: `hr/position/detail?${paramsall}` };
          useIPA.get(param)
          .then(response => {
              resolve(response.data.data)
              return response;
          })
          .catch(({response}) => {
              reject({message: response.data.message, debug: response.data.debug, response: response.data,})
          })
      })
  },
  
  getParam({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `hr/position/detail`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        if (params === 'position') {
          commit('setParamPosition', { ...Param })
        }
      })
    })
  },
  getAll ({ commit }) {
    let param = {status: 'hr/position/detail'}
    useIPA.get(param).then(response => {
      const icd10 = {
        rows : response.data.data
      }
      commit('setData', {...icd10})
    })
  },
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
      const rawData = {
        health_facility_item_id: formData.health_facility_item_id,
        name: formData.name,
        department_id: formData.department_id
			}
      let url = 'hr/position/create'
      if (formData.id) {
        url = 'hr/position/update'
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
  remove ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'hr/position/delete', id: id }
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
  setData (state, position) {
    state.items = position.rows
    state.totalRows = position.rows.length
  },
  setParamPosition (state, data) {
    state.options.position = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = []
      for (const key in data.rows[i]) {
        if (key === 'id') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows[i][key] }
      }
      state.options.position.push(Object.assign({}, option))
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
