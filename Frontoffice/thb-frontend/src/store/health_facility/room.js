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
  getOptAllBed ({ commit }, params) {
		return new Promise((resolve, reject) => {
      // if (!params.room_id) return []

      let queryParams = [];
      if (params.room_id) {
        queryParams.push(`room_id=${params.room_id}`);
      }
      
      let paramsall = queryParams.join('&');
      let param = {status: `health_facility/bed/detail?${paramsall}`};
    
      useIPA.get(param).then(response => {
        let resData = response.data.data
        let temp_arry = []

        for (let i = 0; i < resData.length; i++) {
          const element = resData[i]

          let temp_obj = {
            value: element.id,
            title: element.name,
          }
          temp_arry.push(Object.assign({}, temp_obj))
        }
        resolve(temp_arry)
      })
    })
  },

  putRoomTemp ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      
      const param = { rawData: formData, url: 'health_facility/room/update_temp', id: formData.room_id }

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

  putSwapBed ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      
      const param = { rawData: formData, url: 'health_facility/bed/swap/', id: formData.id }

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

  getFloorPlan ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.health_facility_item_id) {
        queryParams.push(`health_facility_item_id=${params.health_facility_item_id}`);
      }
      
      let paramsall = queryParams.join('&');
      let param = {status: `health_facility/room/floor_plan?${paramsall}`};
    
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  getRoomListV2 ({ commit }, params) {
		return new Promise((resolve, reject) => {
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
          
      let paramsall = queryParams.join('&');
      let param = {status: `health_facility/room/list_v2?${paramsall}`};
    
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  getRoomHistory ({ commit }, params) {
		return new Promise((resolve, reject) => {
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
    
      if (params.registration_destination_id) {
        queryParams.push(`registration_destination_id=${params.registration_destination_id}`);
      } else if (params.registration_destination_id === 0) {
        queryParams.push(`registration_destination_id=${params.registration_destination_id}`);
      }
          
      let paramsall = queryParams.join('&');
      let param = {status: `mr/treatment_room/list?${paramsall}`};
    
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  getMonitoringClass ({ commit }) {
		return new Promise((resolve, reject) => {
    
      let param = {status: `health_facility/room/class_monitoring`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getRoombyFacilityID({ commit }, params){
    return new Promise((resolve, reject) => {
      let queryParams = [];
  
      if (params.health_facility_item_id) {
        queryParams.push(`health_facility_item_id=${params.health_facility_item_id}`);
      }
      
      let paramsall = queryParams.join('&');
    
      let param = {status: `health_facility/room/detail?${paramsall}`};
    
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getDashboardRoom ({ commit }) {
		return new Promise((resolve, reject) => {
    
      let param = {status: `health_facility/room/ward`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getAvailabilityRoom ({ commit }) {
		return new Promise((resolve, reject) => {
    
      let param = {status: `health_facility/room/availability`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getParamRoom({ commit }, params) {
    return new Promise((resolve, reject) => {
      if (params === '') {
        commit('setParamRoomClear')
      } else {
        let param = {
          status: `health_facility/room/detail?type_room_id=${params}`
        }
        useIPA.get(param).then((response) => {
          const Param = {
            rows: response.data.data
          }
          commit('setParamRoom', { ...Param })
        })
      }
    })
  },
  getOptRoom ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.type_room_id) {
        queryParams.push(`type_room_id=${params.type_room_id}`);
      }

      if (params.health_facility_item_id) {
        queryParams.push(`health_facility_item_id=${params.health_facility_item_id}`);
      }
          
      let paramsall = queryParams.join('&');
      let param = {status: `health_facility/room/detail?${paramsall}`};
    
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
  getAvailableRoom({ commit }, params) {
    return new Promise((resolve, reject) => {
      if (params === '') {
        commit('setAvaRoomClear')
      } else {
        let param = {
          status: `health_facility/room/transfer_available?type_room_id=${params}`
        }
        useIPA.get(param).then((response) => {
          const Param = {
            rows: response.data.data
          }
          commit('setAvailableRoom', { ...Param })
        })
      }
    })
  },
  getParamBed({ commit }, params) {
    return new Promise((resolve, reject) => {
      if (params === '') {
        commit('setParamBedClear')
      } else {
        let param = {
          status: `health_facility/bed/detail?type_status_bed_id=1&room_id=${params}`
        }
        useIPA.get(param).then((response) => {
          const Param = {
            rows: response.data.data
          }
          commit('setParamBed', { ...Param })
        })
      }
    })
  },

  getAll ({ commit }) {
    let param = {status: 'health_facility/room/detail'}
    useIPA.get(param).then(response => {
      const parameter = {
        rows : response.data.data
      }
      commit('setData', {...parameter})
    })
  },

  getAllList ({ commit }, { params, cancelToken }) {
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

      if (params.type_room_id) {
        queryParams.push(`type_room_id=${params.type_room_id}`);
      }
      
      let paramsall = queryParams.join('&');
      let param = {status: `health_facility/room/list?${paramsall}`};

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
  // getAllList ({ commit }, params) {
  //   let queryParams = [];

  //   if (params.offset) {
  //     queryParams.push(`offset=${params.offset}`);
  //   } else if (params.offset === 0) {
  //     queryParams.push(`offset=${params.offset}`);
  //   }
  
  //   if (params.limit) {
  //     queryParams.push(`limit=${params.limit}`);
  //   }
  
  //   if (params.name) {
  //     queryParams.push(`name=${params.name}`);
  //   }

  //   if (params.type_room_id) {
  //     queryParams.push(`type_room_id=${params.type_room_id}`);
  //   }
    
  //   let paramsall = queryParams.join('&');
  
  //   let param = {status: `health_facility/room/list?${paramsall}`};

  //   useIPA.get(param).then(response => {
  //     const parameter = {
  //       rows : response.data.data
  //     }
  //     commit('setData', {...parameter})
  //   })
  // },

  getRoombyFacilityID({ commit }, params){
    return new Promise((resolve, reject) => {
      let queryParams = [];
  
      if (params.health_facility_item_id) {
        queryParams.push(`health_facility_item_id=${params.health_facility_item_id}`);
      }
      
      let paramsall = queryParams.join('&');
    
      let param = {status: `health_facility/room/detail?${paramsall}`};
    
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getTypeRoomMonitoring ({ commit }) {
    let param = {status: 'health_facility/room/type_monitoring'}
    useIPA.get(param).then(response => {
      commit('setTypeRoomData', response.data.data)
    })
  },
  getRoomMonitoring ({ commit }, type_room_id) {
    let param = {status: `health_facility/room/monitoring?type_room_id=${type_room_id}`}
    useIPA.get(param).then(response => {
      commit('setRoomData', response.data.data)
    })
  },
  getBed ({ commit }, room_id) {
    let param = {status: `health_facility/bed/detail?room_id=${room_id}`}
    useIPA.get(param).then(response => {
      commit('setBedData', response.data.data)
    })
  },
  // getById ({ commit }, id) {
	// 	return new Promise((resolve, reject) => {
  //     let param = {status: 'master/bank/' + id}
  //     useIPA.get(param).then(response => {
  //         resolve(response.data.data)
  //     })
  //   })
  // },
  // formPostPut ({ commit }, formData) {
	// 	return new Promise((resolve, reject) => {
  //     const data = {
  //       id: '',
  //       health_facility_item_id: '',
  //       costing_id: '',
  //       service_class_id: '',
  //       type_room_id: '',
  //       name: '',
  //       beds: [],
  //       beds_deleted: []
  //     }

  //     data.id = formData.id
  //     data.health_facility_item_id = formData.health_facility_item_id
  //     data.costing_id = formData.costing_id
  //     data.service_class_id = formData.service_class_id
  //     data.type_room_id = formData.type_room_id
  //     data.name = formData.name
  //     data.beds_deleted = formData.beds_deleted

  //     const beds = formData.beds

  //     for (let i = 0; i < beds.length; i++) {
  //       let el = beds[i];
  //       let temp_obj= {}
  //       temp_obj = {
  //         id: el.id,
  //         name: el.name,
  //         type_status_bed_id: el.type_status_bed_id,
  //       }
  //       data.beds.push(temp_obj)
  //     }

  //     let url = 'health_facility/room/create'
  //     if (data.id) {
  //       url = 'health_facility/room/update'
  //     }
      
  //     const param = { rawData: data, url: url, id: data.id }
  //     useIPA.postput(param)
	// 		.then(response => {
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

  postPutRoom ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      
      let url = formData.id ? 'health_facility/room/update': 'health_facility/room/create'
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
      const param = { rawData: rawData, url: 'health_facility/room/delete', id: id }
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
    state.options.room = []
    for (let i = 0; i < room.rows.length; i++) {
      const option = []
      for (const key in room.rows[i]) {
        if (key === 'id') { option['value'] = room.rows[i][key] 
        } else if (key === 'name') { option['title'] = room.rows[i][key] 
        } else if (key === 'value') { option['val'] = room.rows[i]['value'] 
        } else if (key === 'unit') { option['unit'] = room.rows[i][key] 
        } else if (key === 'id') { option['oid'] = room.rows[i][key] }
      }
      state.options.room.push(Object.assign({}, option))
    }
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
