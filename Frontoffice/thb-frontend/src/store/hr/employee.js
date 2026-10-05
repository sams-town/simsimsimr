import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  start: 20,
  totalRows: 1,
  fields: [
    { key: 'fullname', label: 'Nama Lengkap', sortable: true },
    { key: 'health_facility_name', label: 'Fasilitas Kesehatan', sortable: true },
    { key: 'position_name', label: 'Jabatan', sortable: true },
    { key: 'sex_name', label: 'Kelamin', sortable: true },
    { key: 'birth_date', label: 'Tanggal Lahir', sortable: true },
    { key: 'address', label: 'Alamat', sortable: true },
    { key: 'is_delete', label: 'Status', sortable: true },
    { key: 'action', label: 'Aksi'},
  ],
  items: [],
  pageOptions: [20, 50, 100],
  sortOptions: [],
  options:{
    employee_bride:[],
    employee:[],
    rad:[],
    labor:[],
    specialist: [],
    allEmployee: [],
  }
})

// getters
const getters = {}

// actions
const actions = {
  getDoctorList ({ commit }, params) {
    return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.is_specialist !== undefined) {
        queryParams.push(`is_specialist=${params.is_specialist}`)
      }

      let paramsall = queryParams.join('&')

      let param = {status: `hr/employee/list_doctor?${paramsall}`}

      // console.log('param', param)

      useIPA.get(param).then(response => {
        let resData = response.data.data
        let temp_arry = []

        for (let i = 0; i < resData.length; i++) {
          const element = resData[i]

          let temp_obj = {
            value: element.id,
            title: element.fullname,
          }
          temp_arry.push(Object.assign({}, temp_obj))
        }
        resolve(temp_arry)
      })
    })
  },

  getNurseList ({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {status: `hr/employee/list_nurse`}

      useIPA.get(param).then(response => {
        let resData = response.data.data
        let temp_arry = []

        for (let i = 0; i < resData.length; i++) {
          const element = resData[i]

          let temp_obj = {
            value: element.id,
            title: element.fullname,
          }
          temp_arry.push(Object.assign({}, temp_obj))
        }
        resolve(temp_arry)
      })
    })
  },

  getMidwifeList ({ commit }, params) {
    return new Promise((resolve, reject) => {
      const position = params.position || 'midwife';
      const queryParams = [`position=${encodeURIComponent(position)}`];
      const paramsall = queryParams.join('&')

      let param = {status: `hr/employee/list_nurse?${paramsall}`}
      // console.log('param midwife', param)

      useIPA.get(param).then(response => {
        let resData = response.data.data
        let temp_arry = []

        for (let i = 0; i < resData.length; i++) {
          const element = resData[i]

          let temp_obj = {
            value: element.id,
            title: element.fullname,
          }
          temp_arry.push(Object.assign({}, temp_obj))
        }
        resolve(temp_arry)
      })
    })
  },

  getParam({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `hr/employee/doctor_detail?health_facility_item_id=${params}`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamEmployee', { ...Param })
      })
    })
  },
  getParamPhysio({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `hr/employee/doctor_detail?health_facility_item_id=${params}`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamPhysioEmployee', { ...Param })
      })
    })
  },
  getParamAllSpecialist({ commit }) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `hr/employee/doctor_specialist`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamSpecialist', { ...Param })
      })
    })
  },
  getParamSpecialist({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `hr/employee/doctor_detail?health_facility_item_id=40f06534-07ec-4221-ad9d-dea08196079f`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamEmployee', { ...Param })
      })
    })
  },
  getParamScheduleDay({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `hr/employee/doctor_schedule_day?bride_bpjs=${params}`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamEmployeeSchedule', { ...Param })
      })
    })
  },
  getHealthFacilityId ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'setting/health_facility_item/detail?bride_bpjs=' + id}
      useIPA.get(param).then(response => {
          resolve(response.data.data)
      })
    })
  },
  getParamLabor({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `hr/employee/doctor_detail?health_facility_item_id=68be017e-9aa8-4847-8d89-646323f52d23`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamLabor', { ...Param })
      })
    })
  },
  getParamSpecialistLab({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `hr/employee/doctor_detail?health_facility_item_id=68be017e-9aa8-4847-8d89-646323f52d23`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamEmployee', { ...Param })
      })
    })
  },
  getParamRador({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `hr/employee/doctor_detail?health_facility_item_id=0233f15e-cd67-4c92-a3c1-b23928279d1d`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamRador', { ...Param })
      })
    })
  },
  getParamSpecialistRad({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `hr/employee/doctor_detail?health_facility_item_id=0233f15e-cd67-4c92-a3c1-b23928279d1d`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamEmployee', { ...Param })
      })
    })
  },
  getParamAll({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `hr/employee/detail`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamEmployee', { ...Param })
      })
    })
  },
  getDetailById ({ commit }, employee_id) {
		return new Promise((resolve, reject) => {
      let param = {status: `hr/employee/detail/${employee_id}`}
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
  getDoctorOptions ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.health_facility_item_id) {
        queryParams.push(`health_facility_item_id=${params.health_facility_item_id}`)
      }

      let paramsall = queryParams.join('&')
      let param = {status: `hr/employee/doctor_detail?${paramsall}`}

      useIPA.get(param).then(response => {
        let key = response.data.data
        const fixed = []
        for (let i = 0; i < key.length; i++) {
          const element = key[i];
          let temp = {
            value: element.id,
            title: element.fullname,
          }
          fixed.push(temp)
        }
        resolve(fixed)
      })
    })
  },
  getAll ({ commit }, params) {
    let queryParams = [];

    if (params.offset) {
      queryParams.push(`offset=${params.offset}`);
    } else if (params.offset === 0) {
      queryParams.push(`offset=${params.offset}`);
    }

    if (params.fullname) {
      queryParams.push(`fullname=${params.fullname}`);
    }

    if (params.limit) {
      queryParams.push(`limit=${params.limit}`);
    }

    if (params.health_facility_id) {
      queryParams.push(`health_facility_id=${params.health_facility_id}`);
    }

    if (params.position_id) {
      queryParams.push(`position_id=${params.position_id}`);
    }

    let paramsall = queryParams.join('&');

    let param = {status: `hr/employee/list?${paramsall}`};

    useIPA.get(param).then(response => {
      const employee = {
        rows : response.data.data
      }
      commit('setData', {...employee})
    })
  },

  getAllEmployee ({ commit }, params) {
    let queryParams = [];

    if (params.offset) {
      queryParams.push(`offset=${params.offset}`);
    } else if (params.offset === 0) {
      queryParams.push(`offset=${params.offset}`);
    }

    if (params.fullname) {
      queryParams.push(`fullname=${params.fullname}`);
    }

    if (params.limit) {
      queryParams.push(`limit=${params.limit}`);
    }

    if (params.health_facility_id) {
      queryParams.push(`health_facility_id=${params.health_facility_id}`);
    }

    if (params.position_id) {
      queryParams.push(`position_id=${params.position_id}`);
    }

    let paramsall = queryParams.join('&');

    let param = {status: `hr/employee/list?${paramsall}`};

    useIPA.get(param).then(response => {
      const employee = {
        rows : response.data.data.rows
      }
      commit('setAllEmployee', {...employee})
    })
  },
  // getAll ({ commit }) {
  //   let param = {status: 'hr/employee/detail'}
  //   useIPA.get(param).then(response => {
  //     const employee = {
  //       rows : response.data.data
  //     }
  //     commit('setData', {...employee})
  //   })
  // },
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
        f_title: formData.f_title,
        name: formData.name,
        fullname: formData.fullname,
        b_title: formData.b_title,
        birth_date: formData.birth_date,
        sex: formData.sex,
        address: formData.address,
        position_id: formData.position_id,
        bride_satusehat: formData.bride_satusehat,
        bride_satusehat_name: formData.bride_satusehat_name,
        date_expired: formData.date_expired
			}
      let url = 'hr/employee/create'
      if (formData.id) {
        url = 'hr/employee/update'
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
      const param = { rawData: rawData, url: 'hr/employee/delete', id: id }
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
  setData (state, employee) {
    state.items = employee.rows.rows
    state.totalRows = employee.rows.iTotal
    state.start = employee.rows.start
  },
  setParamEmployee (state, data) {
    state.options.employee = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = []
      for (const key in data.rows[i]) {
        if (key === 'id') { option['value'] = data.rows[i][key] } else if (key === 'fullname') { option['title'] = data.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows[i][key] }
      }
      state.options.employee.push(Object.assign({}, option))
    }
  },
  setParamPhysioEmployee (state, data) {
    state.options.physio_employee = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = []
      for (const key in data.rows[i]) {
        if (key === 'id') { option['value'] = data.rows[i][key] } else if (key === 'fullname') { option['title'] = data.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows[i][key] }
      }
      state.options.physio_employee.push(Object.assign({}, option))
    }
  },
  setParamEmployeeSchedule (state, data) {
    state.options.employee_bride = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = []
      for (const key in data.rows[i]) {
        if (key === 'id') { option['value'] = data.rows[i][key] } else if (key === 'fullname') { option['title'] = data.rows[i][key]+' '+data.rows[i]['hour_start']+'-'+data.rows[i]['hour_end'] } else if (key === 'id') { option['oid'] = data.rows[i][key] }
      }
      state.options.employee_bride.push(Object.assign({}, option))
    }
  },
  setAllEmployee (state, data) {
    state.options.allEmployee = []
    for (let i = 0; i < data.rows.length; i++) {
      let key = data.rows[i]
      let temp = {
        value: key.id,
        title: key.fullname,
        position_id: key.position_id,
        position_name: key.position_name,
      }
      state.options.allEmployee.push(Object.assign({}, temp))
    }
  },
  setParamSpecialist (state, data) {
    state.options.specialist = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = []
      for (const key in data.rows[i]) {
        if (key === 'id') { option['value'] = data.rows[i][key] } else if (key === 'fullname') { option['title'] = data.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows[i][key] }
      }
      state.options.specialist.push(Object.assign({}, option))
    }
  },
  setParamLabor (state, data) {
    state.options.labor = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = []
      for (const key in data.rows[i]) {
        if (key === 'id') { option['value'] = data.rows[i][key] } else if (key === 'fullname') { option['title'] = data.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows[i][key] }
      }
      state.options.labor.push(Object.assign({}, option))
    }
  },
  setParamRador (state, data) {
    state.options.rador = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = []
      for (const key in data.rows[i]) {
        if (key === 'id') { option['value'] = data.rows[i][key] } else if (key === 'fullname') { option['title'] = data.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows[i][key] }
      }
      state.options.rador.push(Object.assign({}, option))
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
