import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  pageOptions: [20, 50, 100],
  perPage: 20,
  totalRows: 1,
  firstRow: 1,
  lastRow: 1,
  totalRows: 1,
  sortOptions: [],
  items: [],
  items_type_room_monitoring: [],
  items_room_monitoring: [],
  items_bed: [],
  options:{
    room:[],
    available_room:[],
    bed:[],
  }
})

// getters
const getters = {}

// actions
const actions = {
    getDeptList ({ commit }, params) {
        return new Promise((resolve, reject) => {
            let queryParams = [];
            console.log("params dept", params.params)
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

            if (params.params.name) {
              queryParams.push(`name=${params.params.name}`);
            }
            console.log(queryParams, 'queryParams')
            let paramsall = queryParams.join('&');
            let param = {status: `setting/department/list?${paramsall}`};

            useIPA.get(param).then(response => {
                resolve(response.data.data);
            //   const parameter = {
            //     rows : response.data.data
            //   }
            //   commit('setData', {...parameter})
            })
        })
    },

    postPutDepartment ({ commit }, formData) {
        return new Promise((resolve, reject) => {
        let url = formData.id ? 'setting/department/update': 'setting/department/create'
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

    remove ({ commit }, id) {
        return new Promise((resolve, reject) => {
            const rawData = {
                is_delete: 1,
            }
            const param = { rawData: rawData, url: 'setting/department/delete', id: id }
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
    }
}

// mutations
const mutations = {
  setData (state, room) {
    state.items = room.rows.rows
    state.totalRows = room.rows.iTotal
    // state.options.room = []
  },
  setTypeRoomData (state, type_room) {
    state.items_type_room_monitoring = type_room
  },
  setRoomData (state, room) {
    state.items_room_monitoring = room
  },
  setBedData (state, bed) {
    state.items_bed = bed
  },
  setParamRoom (state, data) {
    state.options.room = []
    const option = []
    for (let i = 0; i < data.rows.length; i++) {
      for (const key in data.rows[i]) {
        if (key === 'id') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows[i][key] }
      }
      state.options.room.push(Object.assign({}, option))
    }
  },
  setParamRoomClear (state) {
    state.options.room = []
  },
  setAvailableRoom (state, data) {
    state.options.available_room = []
    let resData = data.rows
    
    for (let i = 0; i < resData.length; i++) {
      const element = resData[i]

      let temp_obj = {
        value: element.id,
        title: element.name,
      }
      state.options.available_room.push(Object.assign({}, temp_obj))
    }
  },
  setAvaRoomClear (state) {
    state.options.available_room = []
  },
  setParamBedClear (state) {
    state.options.bed = []
  },
  setParamBed (state, data) {
    state.options.bed = []
    const option = []
    for (let i = 0; i < data.rows.length; i++) {
      for (const key in data.rows[i]) {
        if (key === 'id') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows[i][key] }
      }
      state.options.bed.push(Object.assign({}, option))
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
