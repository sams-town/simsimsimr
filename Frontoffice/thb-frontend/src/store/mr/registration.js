import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'
import axios from 'axios'

// initial state
const state = () => ({
  start: 20,
  currentPage: 1,
  totalRows: 0,
  pageOptions: [20, 50, 100],
  sortOptions: [],
  fields: [
    { sortable: true, key: 'code', label: 'No Pendaftaran', thStyle: { minWidth: '140px' } },
    { sortable: true, key: 'insurance_name', label: 'Cara Bayar', thStyle: { minWidth: '150px' } },
    { sortable: true, key: 'type_how_to_enter_name', label: 'Cara Masuk', thStyle: { minWidth: '140px' } },
    { sortable: true, key: 'exit_name', label: 'Status Pasien', thStyle: { minWidth: '140px' } },
    { sortable: true, key: 'norm', label: 'No RM' },
    { sortable: true, key: 'name', label: 'Nama', thStyle: { minWidth: '140px' } },
    { sortable: true, key: 'is_new', label: 'Jenis Pasien', thStyle: { minWidth: '120px' } },
    { sortable: true, key: 'birth_date', label: 'Tanggal Lahir', thStyle: { minWidth: '120px' } },
    { sortable: true, key: 'age', label: 'Kelamin&Umur', thStyle: { minWidth: '140px' } },
    { sortable: true, key: 'health_facility_item_name', label: 'Poli/Ruangan&DPJP', thStyle: { minWidth: '140px' } },
    { sortable: true, key: 'queue', label: 'No Urut', thStyle: { minWidth: '80px' } },
    { sortable: true, key: 'action', label: 'Aksi' },
  ],
  fieldsInpatient: [
    { sortable: true, key: 'code', label: 'No Pendaftaran', thStyle: { minWidth: '140px' } },
    { sortable: true, key: 'insurance_name', label: 'Cara Bayar', thStyle: { minWidth: '150px' } },
    { sortable: true, key: 'type_how_to_enter_name', label: 'Cara Masuk', thStyle: { minWidth: '140px' } },
    { sortable: true, key: 'exit_name', label: 'Status Pasien', thStyle: { minWidth: '140px' } },
    { sortable: true, key: 'norm', label: 'No RM' },
    { sortable: true, key: 'name', label: 'Nama', thStyle: { minWidth: '140px' } },
    { sortable: true, key: 'age', label: 'Kelamin&Umur', thStyle: { minWidth: '140px' } },
    { sortable: true, key: 'los', label: 'LoS', thStyle: { minWidth: '140px' } },
    { sortable: true, key: 'health_facility_item_name', label: 'Poli/Ruangan&DPJP', thStyle: { minWidth: '140px' } },
    { sortable: true, key: 'action', label: 'Aksi' },
  ],
  patientMasterVisit: {
    fields: [
      { key: 'action', label: 'Aksi', thStyle: { minWidth: '60px' } },
      { key: 'norm', label: 'NRM', thStyle: { minWidth: '50px' } },
      { sortable: true, key: 'name', label: 'Identitas pasien', thStyle: { minWidth: '140px' } },
      { key: 'birth_date', label: 'Tanggal Lahir', thStyle: { minWidth: '140px' } },
      // { sortable: true, key: 'doctor_nickname', label: 'DPJP' },
      { sortable: true, key: 'health_facility_item_name', label: 'Poli/Ruangan&DPJP' },
      // { sortable: true, key: 'health_facility_item_name', label: 'Poliklinik' },
      { sortable: true, key: 'insurance_name', label: 'Cara Bayar' },
      // { key: 'birth_place', label: 'Tempat Lahir', thStyle: { minWidth: '140px' } },
    ],
  },
  items: [],
  options:{
    ranap: []
  },
  fast: {
    items: [],
    totalRows: 0,
    start: 20,
  },
  master_data: {
    items: [],
    totalRows: 0,
    start: 20,
  },
  patient_visit_data: {
    items: [],
    totalRows: 0,
    start: 20,
  }
})

// getters
const getters = {}

// actions
const actions = {
  putChangeDateControl ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData

      let url= `mr/registration_destination/change_date_control/${formData.registration_destination_id}`

      const param = { rawData: rawData, url: url }
      useIPA.putonly(param)
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

  getTreatmentRoom ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.registration_destination_id) {
        queryParams.push(`registration_destination_id=${params.registration_destination_id}`);
      }
    
      let paramsall = queryParams.join('&')
      let param = {status: `mr/treatment_room/detail?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  getTreatmentRoomById ({ commit }, id) {
		return new Promise((resolve, reject) => {    
      let param = {status: `mr/treatment_room/detail/${id}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  
  putConfirmTransferV2 ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      let url= `mr/registration_destination/confirm_transfer/${formData.registration_destination_id}`

      const param = { rawData: formData, url: url }
      useIPA.putonly(param)
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

  newPostTransferV2({ commit }, formData) {
    return new Promise((resolve, reject) => {
      const rawData = formData

      const param = { rawData: rawData, url: 'mr/registration_destination/transfer_v2', id: '' }
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

  putSignature ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      
      let url = `mr/registration_destination/upload_patient_signature/${formData.registration_destination_id}`

      const param = { rawData: formData, url: url }
      // useIPA.postput(param)
      useIPA.putonly(param)
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

  getAll ({ commit }, params) {
    return new Promise((resolve, reject) => {
      let queryParams = [];
    
      if (params.offset) {
        queryParams.push(`offset=${params.offset}`)
      } else if (params.offset === 0) {
        queryParams.push(`offset=${params.offset}`)
      }
    
      if (params.limit) {
        queryParams.push(`limit=${params.limit}`)
      }

      if (params.code) {
        queryParams.push(`code=${params.code}`)
      }

      if (params.norm) {
        queryParams.push(`norm=${params.norm}`)
      }

      if (params.name) {
        queryParams.push(`name=${params.name}`)
      }

      if (params.is_checkout != undefined) {
        queryParams.push(`is_checkout=${params.is_checkout}`)
      }
      // queryParams.push(`is_checkout=${params.is_checkout}`)

      if (params.is_new) {
        if (params.is_new == 1) {
          queryParams.push(`is_new=N`)
        } else if (params.is_new == 2) {
          queryParams.push(`is_new=Y`)
        }
      }

      if (params.type_how_to_enter_id) {
        queryParams.push(`type_how_to_enter_id=${params.type_how_to_enter_id}`)
      }

      if (params.sex) {
        queryParams.push(`sex=${params.sex}`)
      }

      if (params.health_facility_id) {
        queryParams.push(`health_facility_id=${params.health_facility_id}`)
      }

      if (params.health_facility_item_id) {
        queryParams.push(`health_facility_item_id=${params.health_facility_item_id}`)
      }

      if (params.doctor_in_charge_id) {
        queryParams.push(`doctor_in_charge_id=${params.doctor_in_charge_id}`)
      }

      if (params.date_start && params.date_end) {
        queryParams.push(`date_start=${params.date_start}`)
        queryParams.push(`date_end=${params.date_end}`)
      }

      let paramsall = queryParams.join('&')
    
      let param = {status: `mr/registration/list?${paramsall}`}
      useIPA.get(param).then(response => {
        const registration = {
          rows : response.data.data
        }
        commit('setListData', {...registration})
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      })
    })
  },

  getListMasterData ({ commit }, params) {
    return new Promise((resolve, reject) => {
      let queryParams = [];
    
      if (params.offset) {
        queryParams.push(`offset=${params.offset}`)
      } else if (params.offset === 0) {
        queryParams.push(`offset=${params.offset}`)
      }
    
      if (params.limit) {
        queryParams.push(`limit=${params.limit}`)
      }

      if (params.norm) {
        queryParams.push(`norm=${params.norm}`)
      }

      if (params.date_start && params.date_end) {
        queryParams.push(`date_start=${params.date_start}`);
        queryParams.push(`date_end=${params.date_end}`);
      }

      if (params.name) {
        queryParams.push(`name=${params.name}`);
      }

      let paramsall = queryParams.join('&')
    
      let param = {status: `mr/registration/list_master_data?${paramsall}`}
      useIPA.get(param).then(response => {
        const registration = {
          rows : response.data.data
        }
        commit('setListMasterData', {...registration})
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      })
    })
  },

  getListPatientVisitMasterData ({ commit }, params) {
    return new Promise((resolve, reject) => {
      let queryParams = [];
    
      if (params.offset) {
        queryParams.push(`offset=${params.offset}`)
      } else if (params.offset === 0) {
        queryParams.push(`offset=${params.offset}`)
      }
    
      if (params.limit) {
        queryParams.push(`limit=${params.limit}`)
      }

      if (params.norm) {
        queryParams.push(`norm=${params.norm}`)
      }

      let paramsall = queryParams.join('&')
    
      let param = {status: `mr/registration/list_patient_visit?${paramsall}`}
      useIPA.get(param).then(response => {
        const registration = {
          rows : response.data.data
        }
        commit('setListPatientVisitMasterData', {...registration})
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      })
    })
  },

  getListFast({ commit }, { params, cancelToken }) {
    return new Promise((resolve, reject) => {
      let queryParams = [];
  
      if (params.offset !== undefined) {
        queryParams.push(`offset=${params.offset}`);
      }
  
      if (params.limit !== undefined) {
        queryParams.push(`limit=${params.limit}`);
      }
  
      if (params.norm_like) {
        queryParams.push(`norm_like=${params.norm_like}`);
      }
  
      if (params.name) {
        queryParams.push(`name=${params.name}`);
      }
  
      if (params.birth_date) {
        queryParams.push(`birth_date=${params.birth_date}`);
      }
  
      if (params.date_start && params.date_end) {
        queryParams.push(`date_start=${params.date_start}`);
        queryParams.push(`date_end=${params.date_end}`);
      }

      if (params.health_facility_id) {
        queryParams.push(`health_facility_id=${params.health_facility_id}`)
      }

      if (params.health_facility_item_id) {
        queryParams.push(`health_facility_item_id=${params.health_facility_item_id}`)
      }

      if (params.doctor_in_charge_id) {
        queryParams.push(`doctor_in_charge_id=${params.doctor_in_charge_id}`)
      }

      if (params.is_checkout !== undefined) {
        queryParams.push(`is_checkout=${params.is_checkout}`)
      }
  
      let paramsall = queryParams.join('&');
      let param = { status: `mr/registration/list_fast?${paramsall}` };
  
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
    });
  },
  
  getAllFast ({ commit }, params) {
    return new Promise((resolve, reject) => {
      let queryParams = [];
    
      if (params.offset) {
        queryParams.push(`offset=${params.offset}`)
      } else if (params.offset === 0) {
        queryParams.push(`offset=${params.offset}`)
      }
    
      if (params.is_checkout !== undefined) {
        queryParams.push(`is_checkout=${params.is_checkout}`);
      }
    
      if (params.limit) {
        queryParams.push(`limit=${params.limit}`)
      }

      if (params.code) {
        queryParams.push(`code=${params.code}`)
      }

      if (params.norm) {
        queryParams.push(`norm=${params.norm}`)
      }

      if (params.name) {
        queryParams.push(`name=${params.name}`)
      }

      if (params.sep) {
        queryParams.push(`sep=${params.sep}`)
      }

      if (params.is_new) {
        if (params.is_new == 1) {
          queryParams.push(`is_new=N`)
        } else if (params.is_new == 2) {
          queryParams.push(`is_new=Y`)
        }
      }

      if (params.type_how_to_enter_id) {
        queryParams.push(`type_how_to_enter_id=${params.type_how_to_enter_id}`)
      }

      if (params.sex === 1 || params.sex === 0) {
        queryParams.push(`sex=${params.sex}`)
        // queryParams.push(`sex=${params.sex}`)
      }

      if (params.health_facility_id) {
        queryParams.push(`health_facility_id=${params.health_facility_id}`)
      }

      if (params.health_facility_item_id) {
        queryParams.push(`health_facility_item_id=${params.health_facility_item_id}`)
      }

      if (params.doctor_in_charge_id) {
        queryParams.push(`doctor_in_charge_id=${params.doctor_in_charge_id}`)
      }

      if (params.insurance_id) {
        queryParams.push(`insurance_id=${params.insurance_id}`)
      }

      if (params.company_id) {
        queryParams.push(`company_id=${params.company_id}`)
      }

      if (params.doctor_in_charge_id) {
        queryParams.push(`doctor_in_charge_id=${params.doctor_in_charge_id}`)
      }
      
      // queryParams.push(`is_checkout=${params.is_checkout}`)

      if (params.date_start && params.date_end) {
        queryParams.push(`date_start=${params.date_start}`)
        queryParams.push(`date_end=${params.date_end}`)
      }

      let paramsall = queryParams.join('&')
    
      let param = {status: `mr/registration/list_admission?${paramsall}`}
      useIPA.get(param).then(response => {
        const registration = {
          rows : response.data.data
        }
        commit('setFastListData', {...registration})
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      })
    })
  },
  getRanapAll ({ commit }, params) {
    return new Promise((resolve, reject) => {
      let queryParams = [];
    
      if (params.offset) {
        queryParams.push(`offset=${params.offset}`)
      } else if (params.offset === 0) {
        queryParams.push(`offset=${params.offset}`)
      }
    
        queryParams.push(`limit=0`)

      if (params.code) {
        queryParams.push(`code=${params.code}`)
      }

      if (params.norm) {
        queryParams.push(`norm=${params.norm}`)
      }

      if (params.name) {
        queryParams.push(`name=${params.name}`)
      }

      if (params.is_new) {
        if (params.is_new == 1) {
          queryParams.push(`is_new=N`)
        } else if (params.is_new == 2) {
          queryParams.push(`is_new=Y`)
        }
      }

      if (params.type_how_to_enter_id) {
        queryParams.push(`type_how_to_enter_id=${params.type_how_to_enter_id}`)
      }

      if (params.sex) {
        queryParams.push(`sex=${params.sex}`)
      }

      if (params.health_facility_id) {
        queryParams.push(`health_facility_id=${params.health_facility_id}`)
      }

      if (params.health_facility_item_id) {
        queryParams.push(`health_facility_item_id=${params.health_facility_item_id}`)
      }

      if (params.doctor_in_charge_id) {
        queryParams.push(`doctor_in_charge_id=${params.doctor_in_charge_id}`)
      }

      if (params.health_facility_id !== '40f06534-07ec-4221-ad9d-dea08196079f') {
        if (params.date_start && params.date_start) {
          queryParams.push(`date_start=${params.date_start}`)
          queryParams.push(`date_end=${params.date_start}`)
        }
      }

      let paramsall = queryParams.join('&')
    
      let param = {status: `mr/registration/pharmacy?${paramsall}`}
      useIPA.get(param).then(response => {
        const registration = {
          rows : response.data.data
        }
        commit('setDataRanapAll', {...registration})
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      })
    })
  },
  getDetail ({ commit }) {
    let param = {status: 'mr/registration/detail'}
    useIPA.get(param).then(response => {
      const registration = {
        rows : response.data.data
      }
      commit('setData', {...registration})
    })
  },
  getCancelledAll ({ commit }, params) {
    let queryParams = [];
  
    if (params.offset) {
      queryParams.push(`offset=${params.offset}`)
    } else if (params.offset === 0) {
      queryParams.push(`offset=${params.offset}`)
    }
  
    if (params.limit) {
      queryParams.push(`limit=${params.limit}`)
    }

    if (params.code) {
      queryParams.push(`code=${params.code}`)
    }

    if (params.norm) {
      queryParams.push(`norm=${params.norm}`)
    }

    if (params.name) {
      queryParams.push(`name=${params.name}`)
    }

    if (params.is_new) {
      if (params.is_new == 1) {
        queryParams.push(`is_new=N`)
      } else if (params.is_new == 2) {
        queryParams.push(`is_new=Y`)
      }
    }

    if (params.type_how_to_enter_id) {
      queryParams.push(`type_how_to_enter_id=${params.type_how_to_enter_id}`)
    }

    if (params.sex) {
      queryParams.push(`sex=${params.sex}`)
    }

    if (params.health_facility_id) {
      queryParams.push(`health_facility_id=${params.health_facility_id}`)
    }

    if (params.health_facility_item_id) {
      queryParams.push(`health_facility_item_id=${params.health_facility_item_id}`)
    }

    if (params.doctor_in_charge_id) {
      queryParams.push(`doctor_in_charge_id=${params.doctor_in_charge_id}`)
    }

    if (params.date_start && params.date_end) {
      queryParams.push(`date_start=${params.date_start}`)
      queryParams.push(`date_end=${params.date_end}`)
    }

    let paramsall = queryParams.join('&')
  
    let param = {status: `mr/registration/list_cancelled?${paramsall}`}
    useIPA.get(param).then(response => {
      const cancelled_reg = {
        rows : response.data.data
      }
      commit('setCancelListData', {...cancelled_reg})
    })
  },
  getById ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'mr/registration/detail?registration_destination_id=' + id}
      useIPA.get(param).then(response => {
          resolve(response.data.data)
      })
    })
  },
  getByInsuranceByNoka ({ commit }, obj) {
		return new Promise((resolve, reject) => {
      let param = {status: `mr/registration_insurance/noka?registration_destination_id=${obj.registration_destination_id}&number_insurance=${obj.number_insurance}` }
      useIPA.get(param).then(response => {
          resolve(response.data.data)
      })
    })
  },
  getByControlByNoka ({ commit }, obj) {
		return new Promise((resolve, reject) => {
      let param = {status: `mr/control_bpjs/noka?registration_destination_id=${obj.registration_destination_id}&number_insurance=${obj.number_insurance}` }
      useIPA.get(param).then(response => {
          resolve(response.data.data)
      })
    })
  },
  getByReferBySep ({ commit }, obj) {
		return new Promise((resolve, reject) => {
      let param = {status: `mr/refer_bpjs/sep?registration_destination_id=${obj.registration_destination_id}&sep=${obj.sep}` }
      useIPA.get(param).then(response => {
          resolve(response.data.data)
      })
    })
  },
  getByPrbBySep ({ commit }, obj) {
		return new Promise((resolve, reject) => {
      let param = {status: `mr/prb_bpjs/sep?registration_destination_id=${obj.registration_destination_id}&sep=${obj.sep}` }
      useIPA.get(param).then(response => {
          resolve(response.data.data)
      })
    })
  },
  getByRegistrationId ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'mr/registration/detail?registration_id=' + id}
      useIPA.get(param).then(response => {
          resolve(response.data.data)
      })
    })
  },
  getInsuranceByNorm ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'mr/registration_insurance/history?norm=' + id}
      useIPA.get(param).then(response => {
          resolve(response.data.data)
      })
    })
  },
  getInsuranceById ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'mr/registration_insurance/detail/' + id}
      useIPA.get(param).then(response => {
          resolve(response.data.data)
      })
    })
  },
  getReferInternalByNorm ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'mr/refer_internal/detail?norm=' + id}
      useIPA.get(param).then(response => {
          resolve(response.data.data)
      })
    })
  },
  getReferInternalByNoka ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'mr/refer_internal/detail?nokartu=' + id}
      useIPA.get(param).then(response => {
          resolve(response.data.data)
      })
    })
  },
  getListInpatient ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];
    
      if (params.offset) {
        queryParams.push(`offset=${params.offset}`)
      } else if (params.offset === 0) {
        queryParams.push(`offset=${params.offset}`)
      }
    
      if (params.limit) {
        queryParams.push(`limit=${params.limit}`)
      }

      if (params.code) {
        queryParams.push(`code=${params.code}`)
      }

      if (params.norm) {
        queryParams.push(`norm=${params.norm}`)
      }

      if (params.name) {
        queryParams.push(`name=${params.name}`)
      }

      if (params.type_how_to_enter_id) {
        queryParams.push(`type_how_to_enter_id=${params.type_how_to_enter_id}`)
      }

      if (params.sex === 1 || params.sex === 0) {
        queryParams.push(`sex=${params.sex}`)
      }

      if (params.health_facility_id) {
        queryParams.push(`health_facility_id=${params.health_facility_id}`)
      }

      if (params.health_facility_item_id) {
        queryParams.push(`health_facility_item_id=${params.health_facility_item_id}`)
      }

      let paramsall = queryParams.join('&')
    
      let param = {status: `mr/registration/list_new?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  getInpatientAll ({ commit }, params) {
    let queryParams = [];
  
    if (params.offset) {
      queryParams.push(`offset=${params.offset}`)
    } else if (params.offset === 0) {
      queryParams.push(`offset=${params.offset}`)
    }
  
    if (params.limit) {
      queryParams.push(`limit=${params.limit}`)
    }

    if (params.code) {
      queryParams.push(`code=${params.code}`)
    }

    if (params.norm) {
      queryParams.push(`norm=${params.norm}`)
    }

    if (params.name) {
      queryParams.push(`name=${params.name}`)
    }

    if (params.is_new) {
      if (params.is_new == 1) {
        queryParams.push(`is_new=N`)
      } else if (params.is_new == 2) {
        queryParams.push(`is_new=Y`)
      }
    }

    if (params.type_how_to_enter_id) {
      queryParams.push(`type_how_to_enter_id=${params.type_how_to_enter_id}`)
    }

    if (params.sex === 1 || params.sex === 0) {
      queryParams.push(`sex=${params.sex}`)
    }

    if (params.health_facility_id) {
      queryParams.push(`health_facility_id=${params.health_facility_id}`)
    }

    if (params.health_facility_item_id) {
      queryParams.push(`health_facility_item_id=${params.health_facility_item_id}`)
    }

    if (params.doctor_in_charge_id) {
      queryParams.push(`doctor_in_charge_id=${params.doctor_in_charge_id}`)
    }

    if (params.date_start && params.date_end) {
      queryParams.push(`date_start=${params.date_start}`)
      queryParams.push(`date_end=${params.date_end}`)
    }

    let paramsall = queryParams.join('&')
  
    let param = {status: `mr/registration/list_new?${paramsall}`}
    useIPA.get(param).then(response => {
      const inpatient = {
        rows : response.data.data
      }
      commit('setInpatientListData', {...inpatient})
    })
  },
  formChange ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      // const param = { rawData: rawData, url: 'xx', id: formData.id }
      const param = { rawData: formData, url: 'mr/registration/change', id: formData.id }
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
  formPostPut ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = {
        code_booking: formData.code_booking,
        noKunjungan: formData.noKunjungan,
        poliRujukan: formData.poliRujukan,
        norm: formData.norm,
        queue_manual: formData.queue_manual,
        type_register_id: formData.type_register_id,
        type_visit_id: formData.type_visit_id,
        patient: {
            idcard: formData.idcard,
            typeidcard: formData.typeidcard,
            alias: formData.alias,
            name: formData.name,
            birth_date: formData.birth_date,
            sex: formData.sex,
            phone: formData.phone,
            responsible_id: formData.responsible_id, 
            responsible_person_idcard: formData.responsible_person_idcard,
            responsible_person_typeidcard: formData.responsible_person_typeidcard,
            responsible_person_name: formData.responsible_person_name,
            responsible_person_phone: formData.responsible_person_phone,
        },
        how_to_enter_id: formData.how_to_enter_id.value,
        icd10_id: formData.icd10_id.value,
        destinations: [
          {
            // insurances: [
              //   {
            //     insurance_id: 1,
            //     company_id: '',
            //     service_class_id: '',
            //     number_insurance: '',
            //     expired: '',
            //   }
            // ],
            jeniskunjungan: formData.jeniskunjungan,
            insurances: formData.insuranceToInput,
            health_facility_item_id: formData.health_facility_item_id.value,
            doctor_in_charge_id: formData.doctor_in_charge_id.value,
            bed_id: formData.bed_id, // belom ada
            type_emergency_id: formData.type_emergency_id.value, 
            type_emergency_case_id: formData.type_emergency_case_id.value,
            case_location: formData.case_location,
            name_location: formData.name_location,
            appointment_id: formData.appointment_id,
          }
        ]
			}
      // const param = { rawData: rawData, url: 'xx', id: formData.id }
      const param = { rawData: rawData, url: 'mr/registration/create', id: '' }
      useIPA.postput(param)
			.then(response => {
        if (response.data.status == 200 || response.data.status == 201) {
          // useIPA.get({status:`mr/registration/list?offset=0&limit=20`}).then(response => {
          useIPA.get({status:`mr/registration/list_admission?offset=0&limit=20`}).then(response => {
            const registration = {
              rows : response.data.data
            }
            commit('setFastListData', {...registration})
            router.push({ name: 'PatientListView' })
          })
        }
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
          reject(error.response.data.message)
          // reject(error.message)
        }
      })
		})
	},
  formPostPutAPM ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = {
        code_booking: formData.code_booking,
        noKunjungan: formData.noKunjungan,
        poliRujukan: formData.poliRujukan,
        norm: formData.norm,
        queue_manual: formData.queue_manual,
        type_register_id: formData.type_register_id,
        type_visit_id: formData.type_visit_id,
        patient: {
            idcard: formData.idcard,
            typeidcard: formData.typeidcard,
            alias: formData.alias,
            name: formData.name,
            birth_date: formData.birth_date,
            sex: formData.sex,
            phone: formData.phone,
            responsible_id: formData.responsible_id, 
            responsible_person_idcard: formData.responsible_person_idcard,
            responsible_person_typeidcard: formData.responsible_person_typeidcard,
            responsible_person_name: formData.responsible_person_name,
            responsible_person_phone: formData.responsible_person_phone,
        },
        how_to_enter_id: formData.how_to_enter_id.value,
        icd10_id: formData.icd10_id.value,
        destinations: [
          {
            // insurances: [
              //   {
            //     insurance_id: 1,
            //     company_id: '',
            //     service_class_id: '',
            //     number_insurance: '',
            //     expired: '',
            //   }
            // ],
            jeniskunjungan: formData.jeniskunjungan,
            insurances: formData.insuranceToInput,
            health_facility_item_id: formData.health_facility_item_id.value,
            doctor_in_charge_id: formData.doctor_in_charge_id.value,
            bed_id: formData.bed_id, // belom ada
            type_emergency_id: formData.type_emergency_id.value, 
            type_emergency_case_id: formData.type_emergency_case_id.value,
            case_location: formData.case_location,
            name_location: formData.name_location,
            appointment_id: formData.appointment_id,
          }
        ]
			}
      // const param = { rawData: rawData, url: 'xx', id: formData.id }
      const param = { rawData: rawData, url: 'mr/registration/createAPM', id: '' }
      useIPA.postput(param)
			.then(response => {
        if (response.data.status == 200 || response.data.status == 201) {
          // useIPA.get({status:`mr/registration/list?offset=0&limit=20`}).then(response => {
          // useIPA.get({status:`mr/registration/list_fast?offset=0&limit=20`}).then(response => {
          //   const registration = {
          //     rows : response.data.data
          //   }
          //   commit('setFastListData', {...registration})
            // console.log(response.data)
            // console.log(response.data.data.registration_destination_id)
            // router.push({ name: 'PrintsStrukView', params: { id: response.data.registration_destination_id }})
          // })
        }
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
          reject(error.response.data.message)
          // reject(error.message)
        }
      })
		})
	},
  formPostPutInpatient ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = {
        code_booking: formData.code_booking,
        noKunjungan: formData.noKunjungan,
        poliRujukan: formData.poliRujukan,
        norm: formData.norm,
        norm_mother: formData.norm_mother,
        queue_manual: formData.queue_manual,
        type_register_id: formData.type_register_id,
        is_surgery: formData.is_surgery,
        patient: {
            idcard: formData.idcard,
            typeidcard: formData.typeidcard,
            alias: formData.alias,
            name: formData.name,
            birth_date: formData.birth_date,
            sex: formData.sex,
            phone: formData.phone,
            responsible_id: formData.responsible_id, 
            responsible_person_idcard: formData.responsible_person_idcard,
            responsible_person_typeidcard: formData.responsible_person_typeidcard,
            responsible_person_name: formData.responsible_person_name,
            responsible_person_phone: formData.responsible_person_phone,
        },
        how_to_enter_id: formData.how_to_enter_id.value,
        icd10_id: formData.icd10_id.value,
        destination: [
          {
            jeniskunjungan: formData.jeniskunjungan,
            insurances: formData.insuranceToInput,
            doctor_in_charge_id: formData.doctor_in_charge_id.value,
            bed_id: formData.bed_id, // belom ada
          }
        ]
			}
      // const param = { rawData: rawData, url: 'xx', id: formData.id }
      const param = { rawData: rawData, url: 'mr/registration/create_inpatient', id: '' }
      useIPA.postput(param)
			.then(response => {
        if (response.data.status == 200 || response.data.status == 201) {
          // useIPA.get({status:`mr/registration/list?offset=0&limit=20`}).then(response => {
          useIPA.get({status:`mr/registration/list_fast?offset=0&limit=20`}).then(response => {
            const registration = {
              rows : response.data.data
            }
            commit('setFastListData', {...registration})
            router.push({ name: 'PatientListView' })
          })
        }
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
          reject(error.response.data.message)
          // reject(error.message)
        }
      })
		})
	},
  formPostPutExternal ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = {
        norm: formData.norm,
        type_register_id: formData.type_register_id,
        how_to_enter_id: formData.how_to_enter_id.value,
        icd10_id: formData.icd10_id.value,
        destination: [
          {
            jeniskunjungan: formData.jeniskunjungan,
            insurances: formData.insuranceToInput,
            health_facility_item_id:formData.health_facility_item_id_external.value,
            doctor_in_charge_id: formData.doctor_in_charge_id_external.value,
          }
        ],
        laboratory_request: formData.laboratory_request_items.length > 0 ? {date_request: formData.date_request,laboratory_request_items:formData.laboratory_request_items} : [],
        radiology_request: formData.radiology_request_items.length > 0 ? {date_request: formData.date_request,radiology_request_items:formData.radiology_request_items} : []
			}
      // const param = { rawData: rawData, url: 'xx', id: formData.id }
      const param = { rawData: rawData, url: 'mr/registration/create_external', id: '' }
      useIPA.postput(param)
			.then(response => {
        if (response.data.status == 200 || response.data.status == 201) {
          // useIPA.get({status:`mr/registration/list?offset=0&limit=20`}).then(response => {
          useIPA.get({status:`mr/registration/list_fast?offset=0&limit=20`}).then(response => {
            const registration = {
              rows : response.data.data
            }
            commit('setFastListData', {...registration})
            router.push({ name: 'PatientListView' })
          })
        }
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
          reject(error.response.data.message)
          // reject(error.message)
        }
      })
		})
	},
  formPostPutCheckinFinger ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      console.log(formData)
      // const param = { rawData: rawData, url: 'xx', id: formData.id }
      const param = { status:'mr/patient/finger/'+formData.booking_code}
      useIPA.get(param)
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
          reject(error.response.data.message)
          // reject(error.message)
        }
      })
		})
	},
  formPostPutCheckin ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      console.log(formData)
      const rawData = formData
      // const param = { rawData: rawData, url: 'xx', id: formData.id }
      const param = { rawData: rawData, url: 'mr/registration/create_checkin', id: '' }
      useIPA.postput(param)
			.then(response => {
        if (formData.code_booking) {
          if (response.data.status == 200 || response.data.status == 201) {
            useIPA.get({status:`mr/registration/list?offset=0&limit=20`}).then(response => {
              const registrationdata = {
                rows : response.data.data
              }
              commit('setListData', {...registrationdata})
              router.push({ name: 'PatientListView' })
            })
          }
        }
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
          reject(error.response.data.message)
          // reject(error.message)
        }
      })
		})
	},
  remove ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const param = { url: 'backoffice/master/bank', id: id }
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
  async updateIsCancelRegistration({ commit }, obj) {
    try {
      let rawData = {}
      if (obj.purpose == 'cancel') {
        rawData = {
          cancel_reason: obj.reason,
          is_cancel: '1',
        }
      } else if (obj.purpose == 'restore') {
        rawData = {
          is_cancel: '0',
        }
      }
      const param = { rawData: rawData, url: 'mr/registration/cancel', id: obj.registration_id };
      const response = await useIPA.postput(param);
      if (response.data.hasErrors) {
        throw new Error(response.data.message);
      }
    } catch (error) {
      throw new Error(error.message);
    }
  },
  async PostSEP({ commit }, obj) {
    try {
      const rawData = new FormData()
      rawData.set('sep', obj.sep)
      rawData.set('file_insurance', obj.file_insurance)

      const param = { rawData: rawData, url: 'mr/registration_insurance/upload_pdf/' + obj.registration_destination_id, id: '' };
      const response = await useIPA.postput(param);
      if (response.data.hasErrors) {
        throw new Error(response.data.message);
      }
    } catch (error) {
      throw new Error(error.message);
    }
  },
  async PostKronologis({ commit }, obj) {
    try {
      const rawData = new FormData()
      rawData.set('file_kronologis', obj.file_kronologis)

      const param = { rawData: rawData, url: 'mr/registration_insurance/upload_kronologis/' + obj.registration_destination_id, id: '' };
      const response = await useIPA.postput(param);
      if (response.data.hasErrors) {
        throw new Error(response.data.message);
      }
    } catch (error) {
      throw new Error(error.message);
    }
  },

  checkout ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      let url= `mr/registration_destination/checkout`

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

  formTransferPost ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = {
        registration_destination_past_id: formData["registration_destination_past_id"],
        registration_id: formData["registration_id"],
        type_room_id: formData["type_room_id"],
        room_id: formData["room_id"],
        bed_id: formData["bed_id"],
        doctor_in_charge_id: formData["doctor_in_charge_id"],
        bed_id: formData["bed_id"],
        nurse_id: formData["nurse_id"],
        soap_s_id: formData["soap_s_id"],
        soap_a_id: formData["soap_a_id"],
        doctor_in_charge_sec_id: formData["doctor_in_charge_sec_id"],
        general_reason: formData["general_reason"],
        exam_done: formData["exam_done"],
        treatment_done: formData["treatment_done"],
        infus_sts: formData["infus_sts"],
        infus: formData["infus"],
        drug_inj_sts: formData["drug_inj_sts"],
        drug_inj: formData["drug_inj"],
        drug_orl_sts: formData["drug_orl_sts"],
        drug_orl: formData["drug_orl"],
        derajat: formData["derajat"],
        transfer_reason: formData["transfer_reason"],
        insurances: formData["insuranceToInput"],
        ver: formData["version"]
    };
    const param = { rawData: rawData, url: 'mr/registration_destination/transfer', id: '' }
      useIPA.postput(param)
      .then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message);
        } else {
          commit('setPrintData', response.data);
          resolve(response.data);
          console.log(response.data)
        }
      })
      .catch(error => {
        if (error.response) {
          // Check if error response has 'error' object
          if (error.response.status === 422 && error.response.data.error) {
            reject(error.response.data.message);
          } else if (error.response.data.hasOwnProperty('hasErrors')) {
            reject(error.response.data.message);
          } else {
            reject(error.message);
          }
        } else {
          reject(error.message);
        }
      });
		})
	},

  formTransferPostV2 ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = {
        registration_destination_past_id: formData["registration_destination_past_id"],
        registration_id: formData["registration_id"],
        // health_facility_item_id: formData["health_facility_item_id"],
        type_room_id: formData["type_room_id"],
        // room_id: formData["room_id"],
        bed_id: formData["bed_id"],
        doctor_in_charge_id: formData["doctor_in_charge_id"],
        bed_id: formData["bed_id"],
        nurse_id: formData["nurse_id"],
        doctor_in_charge_sec_id: formData["doctor_in_charge_sec_id"],
        general_reason: formData["general_reason"],
        exam_done: formData["exam_done"],
        treatment_done: formData["treatment_done"],
        infus_sts: formData["infus_sts"],
        infus: formData["infus"],
        drug_inj_sts: formData["drug_inj_sts"],
        drug_inj: formData["drug_inj"],
        drug_orl_sts: formData["drug_orl_sts"],
        drug_orl: formData["drug_orl"],
        transfer_reason: formData["transfer_reason"],
        insurances: formData["insuranceToInput"],
        transfer_reason_type_id: formData["transfer_reason_type_id"], //ini
        isTransportable: formData["isTransportable"], //ini
        main_complaint: formData["main_complaint"], //ini
        ver: formData["version"], //ini
    };
    const param = { rawData: rawData, url: 'mr/registration_destination/transfer_v2', id: '' }
      useIPA.postput(param)
      .then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message);
        } else {
          commit('setPrintData', response.data);
          resolve(response.data);
          console.log(response.data)
        }
      })
      .catch(error => {
        if (error.response) {
          // Check if error response has 'error' object
          if (error.response.status === 422 && error.response.data.error) {
            reject(error.response.data.message);
          } else if (error.response.data.hasOwnProperty('hasErrors')) {
            reject(error.response.data.message);
          } else {
            reject(error.message);
          }
        } else {
          reject(error.message);
        }
      });
		})
	},

  getAntrol({ commit }, { params, cancelToken }) {
    return new Promise((resolve, reject) => {
      let queryParams = [];
  
      if (params.offset !== undefined) {
        queryParams.push(`offset=${params.offset}`);
      }
  
      if (params.limit !== undefined) {
        queryParams.push(`limit=${params.limit}`);
      }
  
      if (params.norm_like) {
        queryParams.push(`norm_like=${params.norm_like}`);
      }
  
      if (params.name) {
        queryParams.push(`name=${params.name}`);
      }
  
      if (params.birth_date) {
        queryParams.push(`birth_date=${params.birth_date}`);
      }
  
      if (params.date_start && params.date_end) {
        queryParams.push(`date_start=${params.date_start}`);
        queryParams.push(`date_end=${params.date_end}`);
      }

      if (params.health_facility_id) {
        queryParams.push(`health_facility_id=${params.health_facility_id}`)
      }

      if (params.health_facility_item_id) {
        queryParams.push(`health_facility_item_id=${params.health_facility_item_id}`)
      }

      if (params.company_id) {
        queryParams.push(`company_id=${params.company_id}`)
      }

      if (params.doctor_in_charge_id) {
        queryParams.push(`doctor_in_charge_id=${params.doctor_in_charge_id}`)
      }

      if (params.is_checkout !== undefined) {
        queryParams.push(`is_checkout=${params.is_checkout}`)
      }

      if (params.exit_id !== undefined) {
        queryParams.push(`exit_id=${params.exit_id}`)
      }
      let paramsall = queryParams.join('&');
      let param = { status: `mr/registration/list_antrol?${paramsall}` };
  
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
    });
  },

  cancelAntrol ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'mr/registration/cancel_antrol', id: id }
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

  getListFast({ commit }, { params, cancelToken }) {
    return new Promise((resolve, reject) => {
      let queryParams = [];
  
      if (params.offset !== undefined) {
        queryParams.push(`offset=${params.offset}`);
      }
  
      if (params.limit !== undefined) {
        queryParams.push(`limit=${params.limit}`);
      }
  
      if (params.norm_like) {
        queryParams.push(`norm_like=${params.norm_like}`);
      }
  
      if (params.name) {
        queryParams.push(`name=${params.name}`);
      }
  
      if (params.birth_date) {
        queryParams.push(`birth_date=${params.birth_date}`);
      }
  
      if (params.date_start && params.date_end) {
        queryParams.push(`date_start=${params.date_start}`);
        queryParams.push(`date_end=${params.date_end}`);
      }

      if (params.health_facility_id) {
        queryParams.push(`health_facility_id=${params.health_facility_id}`)
      }

      if (params.health_facility_item_id) {
        queryParams.push(`health_facility_item_id=${params.health_facility_item_id}`)
      }

      if (params.company_id) {
        queryParams.push(`company_id=${params.company_id}`)
      }

      if (params.doctor_in_charge_id) {
        queryParams.push(`doctor_in_charge_id=${params.doctor_in_charge_id}`)
      }

      if (params.is_checkout !== undefined) {
        queryParams.push(`is_checkout=${params.is_checkout}`)
      }

      if (params.exit_id !== undefined) {
        queryParams.push(`exit_id=${params.exit_id}`)
      }
      let paramsall = queryParams.join('&');
      let param = { status: `mr/registration/list_fast?${paramsall}` };
  
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
    });
  },

  getListFastReg({ commit }, params) {
    return new Promise((resolve, reject) => {
      let queryParams = [];
  
      if (params.offset !== undefined) {
        queryParams.push(`offset=${params.offset}`);
      }
  
      if (params.limit !== undefined) {
        queryParams.push(`limit=${params.limit}`);
      }
  
      if (params.norm) {
        queryParams.push(`norm=${params.norm}`);
      }
  
      if (params.name) {
        queryParams.push(`name=${params.name}`);
      }
  
      if (params.birth_date) {
        queryParams.push(`birth_date=${params.birth_date}`);
      }
  
      if (params.date_start && params.date_end) {
        queryParams.push(`date_start=${params.date_start}`);
        queryParams.push(`date_end=${params.date_end}`);
      }

      if (params.health_facility_id) {
        queryParams.push(`health_facility_id=${params.health_facility_id}`)
      }

      if (params.health_facility_item_id) {
        queryParams.push(`health_facility_item_id=${params.health_facility_item_id}`)
      }

      if (params.company_id) {
        queryParams.push(`company_id=${params.company_id}`)
      }

      if (params.doctor_in_charge_id) {
        queryParams.push(`doctor_in_charge_id=${params.doctor_in_charge_id}`)
      }

      if (params.is_checkout !== undefined) {
        queryParams.push(`is_checkout=${params.is_checkout}`)
      }

      if (params.exit_id !== undefined) {
        queryParams.push(`exit_id=${params.exit_id}`)
      }
      let paramsall = queryParams.join('&');
      // let param = { status: `mr/registration/list_fast_register?${paramsall}` };
      let param = { status: `mr/registration/list_admission?${paramsall}` };
  
      useIPA.get(param)
        .then(response => {
          resolve(response.data.data);
          const registrationdata = {
            rows : response.data.data
          }
          commit('setFastListData', {...registrationdata})
        })
        .catch(error => {
          if (axios.isCancel(error)) {
            console.log("Request dibatalkan:", error.message);
          } else {
            reject(error);
          }
        });
    });
  },

  getListFastJKN({ commit }, { params, cancelToken }) {
    return new Promise((resolve, reject) => {
      let queryParams = [];
  
      if (params.offset !== undefined) {
        queryParams.push(`offset=${params.offset}`);
      }
  
      if (params.limit !== undefined) {
        queryParams.push(`limit=${params.limit}`);
      }
  
      if (params.norm_like) {
        queryParams.push(`norm_like=${params.norm_like}`);
      }
  
      if (params.name) {
        queryParams.push(`name=${params.name}`);
      }
  
      if (params.sep) {
        queryParams.push(`sep=${params.sep}`);
      }
  
      if (params.birth_date) {
        queryParams.push(`birth_date=${params.birth_date}`);
      }
  
      if (params.date_start && params.date_end) {
        queryParams.push(`date_start=${params.date_start}`);
        queryParams.push(`date_end=${params.date_end}`);
      }

      if (params.health_facility_id) {
        queryParams.push(`health_facility_id=${params.health_facility_id}`)
      }

      if (params.health_facility_item_id) {
        queryParams.push(`health_facility_item_id=${params.health_facility_item_id}`)
      }

      if (params.company_id) {
        queryParams.push(`company_id=${params.company_id}`)
      }

      if (params.doctor_in_charge_id) {
        queryParams.push(`doctor_in_charge_id=${params.doctor_in_charge_id}`)
      }

      if (params.is_checkout !== undefined) {
        queryParams.push(`is_checkout=${params.is_checkout}`)
      }

      if (params.exit_id !== undefined) {
        queryParams.push(`exit_id=${params.exit_id}`)
      }
      let paramsall = queryParams.join('&');
      let param = { status: `mr/registration/list_fast_jkn?${paramsall}` };
  
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
    });
  },
  
  formReferInternal ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      // const param = { rawData: rawData, url: 'xx', id: formData.id }
      const param = { rawData: formData, url: 'mr/registration/refer_internal', id: formData.id }
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
  formChangeCheckout ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const param = { rawData: formData.checkout, url: `mr/registration_destination/change_checkout_date`, id: formData.registration_destination_id }
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
  checkoutDoctor ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const param = { rawData: formData.checkout, url: `mr/registration_destination/checkout_doctor`, id: formData.registration_destination_id }
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
  checkoutV2 ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const param = { rawData: formData.checkout, url: `mr/registration_destination/checkout_v2`, id: formData.registration_destination_id }
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
  putDoctorDestination ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const param = { rawData: formData, url: `mr/registration_destination/update_doctor`, id: formData.registration_destination_id }
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
	}
}

// mutations
const mutations = {
  setData (state, registration) {
    state.items = registration.rows
    state.totalRows = registration.rows.length
  },
  setListData (state, registration) {
    state.items = registration.rows.rows
    state.totalRows = registration.rows.iTotal
    state.start = registration.rows.start
  },
  setFastListData (state, registration) {
    state.fast.items = registration.rows.rows
    state.fast.totalRows = registration.rows.iTotal
    state.fast.start = registration.rows.start
  },
  setListMasterData (state, registration) {
    state.master_data.items = registration.rows.rows
    state.master_data.totalRows = registration.rows.iTotal
    state.master_data.start = registration.rows.start
  },
  setListPatientVisitMasterData (state, registration) {
    state.patient_visit_data.items = registration.rows.rows
    state.patient_visit_data.totalRows = registration.rows.iTotal
    state.patient_visit_data.start = registration.rows.start
  },
  setCancelListData (state, cancelled_reg) {
    state.items = cancelled_reg.rows.rows
    state.totalRows = cancelled_reg.rows.iTotal
    state.start = cancelled_reg.rows.start
  },
  setInpatientListData (state, inpatient) {
    state.items = inpatient.rows.rows
    state.totalRows = inpatient.rows.iTotal
    state.start = inpatient.rows.start
  },
  setDataRanapAll (state, data) {
    state.options.ranap = []
    const option = []
    console.log(data.rows)
    for (let i = 0; i < data.rows.rows.length; i++) {
      for (const key in data.rows.rows[i]) {
        if (key === 'id') { option['value'] = data.rows.rows[i][key] } else if (key === 'fullname') { option['title'] = data.rows.rows[i]['norm']+'-'+data.rows.rows[i]['fullname'] } else if (key === 'id') { option['oid'] = data.rows.rows[i][key] }
      }
      state.options.ranap.push(Object.assign({}, option))
    }
  },
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
