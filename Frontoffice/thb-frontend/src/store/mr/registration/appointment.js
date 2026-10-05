import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  start: 20,
  totalRows: 1,
  sortOptions: [],
  // fields: [
  //     { key: 'norm', label: 'No RM', thStyle: { minWidth: '140px' } },
  //     { key: 'name', label: 'Nama', thStyle: { minWidth: '140px' } },
  //     { key: 'birth_date', label: 'Tanggal Lahir', thStyle: { minWidth: '140px' } },
  //     { key: 'birth_place', label: 'Tempat Lahir', thStyle: { minWidth: '140px' } },
  //     { key: 'phone', label: 'No Telp/HP', thStyle: { minWidth: '140px' } },
  //     { key: 'health_facility_name', label: 'Instalasi', thStyle: { minWidth: '140px' } },
  //     { key: 'health_facility_item_name', label: 'Poli/Ruangan', thStyle: { minWidth: '140px' } },
  //     { key: 'doctor_in_charge_name', label: 'Dokter', thStyle: { minWidth: '140px' } },
  //     { key: 'sex_name', label: 'Kelamin', thStyle: { minWidth: '140px' } },
  //     { key: 'date_appointment', label: 'Waktu Appointment', thStyle: { minWidth: '100px' } },
  //     { key: 'time_appointment', label: 'Jam', thStyle: { minWidth: '100px' } },
  //     { key: 'queue', label: 'No Antrian', thStyle: { minWidth: '100px' } },
  //     { key: 'action', label: 'Aksi', thStyle: { minWidth: '170px' } },
  // ],
  fields: [
      { key: 'company_name', label: 'Penjamin', thStyle: { minWidth: '140px' } },
      { key: 'norm', label: 'No RM', thStyle: { minWidth: '140px' } },
      { key: 'name', label: 'Nama', thStyle: { minWidth: '140px' } },
      { key: 'phone', label: 'No Telp/HP', thStyle: { minWidth: '140px' } },
      { key: 'health_facility_item_name', label: 'Poli&Dokter', thStyle: { minWidth: '140px' } },
      { key: 'date_appointment', label: 'Waktu Appointment', thStyle: { minWidth: '100px' } },
      { key: 'queue', label: 'No Antrian', thStyle: { minWidth: '100px' } },
      { key: 'nobooking', label: 'Asal', thStyle: { minWidth: '100px' } },
      { key: 'action', label: 'Aksi', thStyle: { minWidth: '170px' } },
  ],
  fieldscheckin: [
      { key: 'norm', label: 'No Booking', thStyle: { minWidth: '140px' } },
      { key: 'name', label: 'Nama', thStyle: { minWidth: '140px' } },
      { key: 'health_facility_name', label: 'Instalasi', thStyle: { minWidth: '140px' } },
      { key: 'health_facility_item_name', label: 'Poli/Ruangan', thStyle: { minWidth: '140px' } },
      { key: 'doctor_in_charge_name', label: 'Dokter', thStyle: { minWidth: '140px' } },
      { key: 'date_appointment', label: 'Waktu Appointment', thStyle: { minWidth: '100px' } },
      { key: 'time_appointment', label: 'Jam', thStyle: { minWidth: '100px' } },
      { key: 'queue', label: 'No Antrian', thStyle: { minWidth: '100px' } },
  ],
  pageOptions: [20, 50, 100],
  items: [],
  options:{
    appointment:[]
  }
})

// getters
const getters = {}

// actions
const actions = {
  getParam({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `mr/appointment/detail`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        if (params === 'appointment') {
          commit('setParamAppointment', { ...Param })
        }
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
  
    if (params.limit) {
      queryParams.push(`limit=${params.limit}`);
    }
  
    if (params.norm) {
      queryParams.push(`kodebooking=${params.norm}`);
    }
  
    if (params.name) {
      queryParams.push(`name=${params.name}`);
    }
    if (params.phone) {
      queryParams.push(`phone=${params.phone}`);
    }
    if (params.birth_date) {
      queryParams.push(`birth_date=${params.birth_date}`);
    }
    if (params.birth_place) {
      queryParams.push(`birth_place=${params.birth_place}`);
    }
    if (params.health_facility_id) {
      queryParams.push(`health_facility_id=${params.health_facility_id}`);
    }
    if (params.health_facility_item_id) {
      queryParams.push(`health_facility_item_id=${params.health_facility_item_id}`);
    }
    if (params.doctor_in_charge_id) {
      queryParams.push(`doctor_in_charge_id=${params.doctor_in_charge_id}`);
    }
    if (params.date_start && params.date_end) {
      queryParams.push(`date_start=${params.date_start}`)
      queryParams.push(`date_end=${params.date_end}`)
    }
    
    let paramsall = queryParams.join('&');
  
    let param = {status: `mr/appointment/list?${paramsall}`};
    // let param = {status: 'mr/appointment/detail'}
    useIPA.get(param).then(response => {
      const appointment = {
        rows : response.data.data
      }
      commit('setListData', {...appointment})
    })
  },
  getDetail ({ commit }) {
    let param = {status: 'mr/appointment/detail'}
    useIPA.get(param).then(response => {
      const appointment = {
        rows : response.data.data
      }
      commit('setData', {...appointment})
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
  formPostPut ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = {
        id: formData.id,
        norm: formData.norm,
        name: formData.name,
        birth_date: formData.birth_date,
        birth_place: formData.birth_place,
        sex: formData.sex,
        phone: formData.phone,
        date_appointment: formData.date_appointment,
        doctor_schedule_item_id: formData.doctor_schedule_item_id,
        queue: formData.queue,
			}
      let url = 'mr/appointment/create'
      if (formData.id) {
        url = 'mr/appointment/update'
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
  async updateFlagAppointment({ commit }, appointmentId) {
    try {
      const rawData = {
        id: appointmentId,
        flag_register: '2', // Ubah flag_register sesuai kebutuhan Anda
      };
      const param = { rawData: rawData, url: 'mr/appointment/update', id: appointmentId };
      const response = await useIPA.postput(param);
      if (response.data.hasErrors) {
        throw new Error(response.data.message);
      }
      // Sukses mengupdate flag_register
    } catch (error) {
      throw new Error(error.message);
    }
  },
  remove ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'mr/appointment/delete', id: id }
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
  setData (state, appointment) {
    state.items = appointment.rows
    state.totalRows = appointment.rows.length
  },
  setListData (state, appointment) {
    state.items = appointment.rows.rows
    state.totalRows = appointment.rows.iTotal
    state.start = appointment.rows.start
  },
  setParamAppointment (state, data) {
    state.options.appointment = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = []
      for (const key in data.rows[i]) {
        if (key === 'id') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows[i][key] }
      }
      state.options.appointment.push(Object.assign({}, option))
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
