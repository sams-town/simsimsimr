import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  start: 20,
  totalRows: 1,
  fields: [
    { key: 'code', label: 'Kode', sortable: true },
    { key: 'name', label: 'Nama', sortable: true },
    { key: 'variable', label: 'Varibel', sortable: true },
    { key: 'description', label: 'Deskripsi', sortable: true },
    { key: 'is_delete', label: 'Status', sortable: true },
    { key: 'action', label: 'Aksi' },
  ],
  items: [],
  pageOptions: [20, 50, 100],
  sortOptions: [],
  perPage: 20,
  options: {
    code: [],
    alias: [],
    sex: [],
    religion: [],
    married: [],
    citizenship: [],
    blood: [],
    lastedu: [],
    profession: [],
    typeallergy: [],
    responsible: [],
    insurance: [],
    howtoenter: [],
    typecosting: [],
    typecostingc: [],
    province: [],
    regency: [],
    district: [],
    village: [],
    third_party_company: [],
    service_class: [],
    patient_issue: [],
    type_idcard: [],
    day: [],
    isNew: [],
    type_status_bed: [],
    type_room: [],
    typeRegister: [],
    typeEmergency: [],
    typeEmergencyCase: [],
    letter_type_id: [],
    typepurchaseorder:[],
    typereduction:[],
    typeprice:[],
    typepaymentmethod:[],
    loket:[],
    type_catalog: []
  }
})

// getters
const getters = {}

// actions
const actions = {
  getCode({ commit }, params) {
    return new Promise((resolve, reject) => {
      const paramCodeMap = {
        '00f4dcd5-8577-4e7f-b7b6-58a2a1f767b3': ['AB', 'AE', 'BB', 'EF'], // POLIKLINIK ANAK PAEDIATRIC
        '0fd51271-a0e5-4db2-bc6e-12064508e82b': ['AD', 'BF', 'DB'], // POLIKLINIK KEBIDANAN & KANDUNGAN
        '219f52b1-ec3a-4cb6-a2cf-5d87a82175d9': ['DF'], // POLIKLINIK JANTUNG
        '246f50f0-d6b1-40be-8602-af11e105c9c4': ['FA'], // POLIKLINIK PSIKIATRI
        '358bf387-dced-47ad-97cd-cfaae63851aa': ['AC', 'DA', 'DC', 'FB', 'FC'], // POLIKLINIK PENYAKIT DALAM (INTERNA)
        '38b10a7b-7006-451b-aa30-c11fa98635e0': ['ED'], // POLIKLINIK GIGI
        '477baf79-5cae-4da7-8d98-cdad9161533a': ['CE', 'CF'], // POLIKLINIK KULIT & KELAMIN
        '4adbb716-9cf3-49fa-93b6-ba4bf2f54736': ['CD'], // POLIKLINIK BEDAH PLASTIK
        '4bdaf1f5-575a-4c9f-bed9-f17f73802c64': ['DE'], // POLIKLINIK THT
        '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a': ['EB', 'EE'], // POLIKLINIK REHABILITASI MEDIS
        '6292975d-65c5-4d7d-b7ca-1ef85b30c286': ['BC', 'DT'], // POLIKLINIK PARU
        '6e90b7af-67ae-4a26-a1e2-44c151719080': ['DD'], // POLIKLINIK BEDAH UROLOGI
        '73fd97aa-8cec-4058-9bf3-14cdcd9db4c0': ['BD', 'BX', 'EC'], // POLIKLINIK BEDAH UMUM
        '74087789-8f86-4e45-802b-d3b9710a2a3c': ['BT', 'TB'], // POLIKLINIK BEDAH THORAKS & KARDIOVASKULER
        '7759aec0-3f21-42d5-9fad-05d04fb82b24': ['BE'], // POLIKLINIK BEDAH SYARAF
        '820af6c1-0c58-45ae-afb2-990360f9e132': ['AF', 'LD'], // POLIKLINIK BEDAH ORTHOPEDI
        '86607c52-5eae-4b17-861a-2fbaf864f06f': ['CA'], // POLIKLINIK SYARAF
        '8798e026-a5ad-4b98-8f2b-bfc5521e5144': ['DG'], // POLIKLINIK BEDAH DIGESTIF
        '9c89d12e-63c1-4d1e-a082-e37bcadaeec1': ['BA'], // POLIKLINIK BEDAH ANAK
        'bbdf4d7d-3255-4753-8292-70b836e019f4': ['CB'], // POLIKLINIK MATA
      }
      const codes = paramCodeMap[params] || []
      commit('clearParamCode')
      codes.forEach(code => {
        commit('setParamCode', {
          value: code,
          title: code
        })
      })
    })
  },

  getParamLetterTypeId({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `setting/helper/detail?variable=${params}`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamLetterType', { ...Param })
      })
    })
  },

  getParamProvince({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `province/detail?flag_active=1`
      }
      if (localStorage.getItem(`getParamProvince`)) {
        commit('setParamProvince', {...JSON.parse(localStorage.getItem(`getParamProvince`))})
      } else {
        useIPA.get(param).then((response) => {
          const Param = {
            rows: response.data.data
          }
          localStorage.setItem(`getParamProvince`, JSON.stringify(Param))
          if (params === 'province') {
            commit('setParamProvince', { ...Param })
          }
        })
      }
    })
  },
  getParamRegency({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `regency/detail?id_province=${params}`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamRegency', { ...Param })
      })
    })
  },
  getParamDistrict({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `district/detail?id_regency=${params}`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamDistrict', { ...Param })
      })
    })
  },
  getParamVillage({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `village/detail?id_district=${params}`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamVillage', { ...Param })
      })
    })
  },
  getParamThirdPartyCompany({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `third_party/company/detail?flag=1&is_delete=0`
      }
      if (localStorage.getItem(`setParamThirdPartyCompany`)) {
        commit('setParamThirdPartyCompany', {...JSON.parse(localStorage.getItem(`setParamThirdPartyCompany`))})
      } else {
        useIPA.get(param).then((response) => {
          const Param = {
            rows: response.data.data
          }
          localStorage.setItem(`setParamThirdPartyCompany`, JSON.stringify(Param))
          if (params === 'third_party_company') {
            commit('setParamThirdPartyCompany', { ...Param })
          }
        })
      }
    })
  },
  getParamServiceClass({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `setting/service_class/detail?is_delete=0`
      }
      if (localStorage.getItem(`getParamServiceClass`)) {
        commit('setParamServiceClass', {...JSON.parse(localStorage.getItem(`getParamServiceClass`))})
      } else {
        useIPA.get(param).then((response) => {
          const Param = {
            rows: response.data.data
          }
          localStorage.setItem(`getParamServiceClass`, JSON.stringify(Param))
          if (params === 'service_class') {
            commit('setParamServiceClass', { ...Param })
          }
        })
      }
    })
  },
  getParam({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `setting/helper/detail?variable=${params}`
      }
      if (localStorage.getItem(`helper-${params}`)) {
        // get_cookies
          if (params === 'alias') {
            commit('setParamAlias', {...JSON.parse(localStorage.getItem(`helper-${params}`))})
          } else if (params === 'sex') {
            commit('setParamSex', {...JSON.parse(localStorage.getItem(`helper-${params}`))})
          } else if (params === 'religion') {
            commit('setParamReligion', {...JSON.parse(localStorage.getItem(`helper-${params}`))})
          } else if (params === 'married') {
            commit('setParamMarried', {...JSON.parse(localStorage.getItem(`helper-${params}`))})
          } else if (params === 'citizenship') {
            commit('setParamCitizenship', {...JSON.parse(localStorage.getItem(`helper-${params}`))})
          } else if (params === 'blood') {
            commit('setParamBlood', {...JSON.parse(localStorage.getItem(`helper-${params}`))})
          } else if (params === 'last_edu') {
            commit('setParamLastEdu', {...JSON.parse(localStorage.getItem(`helper-${params}`))})
          } else if (params === 'profession') {
            commit('setParamProfession', {...JSON.parse(localStorage.getItem(`helper-${params}`))})
          } else if (params === 'type_allergy') {
            commit('setParamTypeAllergy', {...JSON.parse(localStorage.getItem(`helper-${params}`))})
          } else if (params === 'responsible') {
            commit('setParamResponsible', {...JSON.parse(localStorage.getItem(`helper-${params}`))})
          } else if (params === 'insurance') {
            commit('setParamInsurance', {...JSON.parse(localStorage.getItem(`helper-${params}`))})
          } else if (params === 'type_how_to_enter') {
            commit('setParamHowToEnter', {...JSON.parse(localStorage.getItem(`helper-${params}`))})
          } else if (params === 'type_costing') {
            commit('setParamTypeCosting', {...JSON.parse(localStorage.getItem(`helper-${params}`))})
          } else if (params === 'costing_c_one') {
            commit('setParamTypeCostingC', {...JSON.parse(localStorage.getItem(`helper-${params}`))})
          } else if (params === 'patient_issue') {
            commit('setParamPatientIssue', {...JSON.parse(localStorage.getItem(`helper-${params}`))})
          } else if (params === 'typeidcard') {
            commit('setParamTypeIdcard', {...JSON.parse(localStorage.getItem(`helper-${params}`))})
          } else if (params === 'day') {
            commit('setParamDay', {...JSON.parse(localStorage.getItem(`helper-${params}`))})
          } else if (params === 'is_new') {
            commit('setParamIsNew', {...JSON.parse(localStorage.getItem(`helper-${params}`))})
          } else if (params === 'type_status_bed') {
            commit('setParamTypeStatusBed', {...JSON.parse(localStorage.getItem(`helper-${params}`))})
          } else if (params === 'type_room') {
            commit('setParamTypeRoom', {...JSON.parse(localStorage.getItem(`helper-${params}`))})
          } else if (params === 'type_register') {
            commit('setParamTypeRegister', {...JSON.parse(localStorage.getItem(`helper-${params}`))})
          } else if (params === 'type_emergency') {
            commit('setParamTypeEmergency', {...JSON.parse(localStorage.getItem(`helper-${params}`))})
          } else if (params === 'type_emergency_case') {
            commit('setParamTypeEmergencyCase', {...JSON.parse(localStorage.getItem(`helper-${params}`))})
          } else if (params === 'type_purchase_order') {
            commit('setParamTypePurchaseOrder', {...JSON.parse(localStorage.getItem(`helper-${params}`))})
          } else if (params === 'type_reduction') {
            commit('setParamTypeReduction', {...JSON.parse(localStorage.getItem(`helper-${params}`))})
          } else if (params === 'type_price') {
            commit('setParamTypePrice', {...JSON.parse(localStorage.getItem(`helper-${params}`))})
          } else if (params === 'type_payment_method') {
            commit('setParamTypePaymentMethod', {...JSON.parse(localStorage.getItem(`helper-${params}`))})
          } else if (params === 'type_catalog') {
            commit('setParamTypeCatalog', {...JSON.parse(localStorage.getItem(`helper-${params}`))})
          }
        //cookies
      } else {
        useIPA.get(param).then((response) => {
          const Param = {
            rows: response.data.data
          }
          // set_cookies
          localStorage.setItem(`helper-${params}`, JSON.stringify(Param))
          // set_cookies
          // get_cookies
            if (params === 'alias') {
              commit('setParamAlias', {...Param})
            } else if (params === 'sex') {
              commit('setParamSex', {...Param})
            } else if (params === 'religion') {
              commit('setParamReligion', {...Param})
            } else if (params === 'married') {
              commit('setParamMarried', {...Param})
            } else if (params === 'citizenship') {
              commit('setParamCitizenship', {...Param})
            } else if (params === 'blood') {
              commit('setParamBlood', {...Param})
            } else if (params === 'last_edu') {
              commit('setParamLastEdu', {...Param})
            } else if (params === 'profession') {
              commit('setParamProfession', {...Param})
            } else if (params === 'type_allergy') {
              commit('setParamTypeAllergy', {...Param})
            } else if (params === 'responsible') {
              commit('setParamResponsible', {...Param})
            } else if (params === 'insurance') {
              commit('setParamInsurance', {...Param})
            } else if (params === 'type_how_to_enter') {
              commit('setParamHowToEnter', {...Param})
            } else if (params === 'type_costing') {
              commit('setParamTypeCosting', {...Param})
            } else if (params === 'costing_c_one') {
              commit('setParamTypeCostingC', {...Param})
            } else if (params === 'patient_issue') {
              commit('setParamPatientIssue', {...Param})
            } else if (params === 'typeidcard') {
              commit('setParamTypeIdcard', {...Param})
            } else if (params === 'day') {
              commit('setParamDay', {...Param})
            } else if (params === 'is_new') {
              commit('setParamIsNew', {...Param})
            } else if (params === 'type_status_bed') {
              commit('setParamTypeStatusBed', {...Param})
            } else if (params === 'type_room') {
              commit('setParamTypeRoom', {...Param})
            } else if (params === 'type_register') {
              commit('setParamTypeRegister', {...Param})
            } else if (params === 'type_emergency') {
              commit('setParamTypeEmergency', {...Param})
            } else if (params === 'type_emergency_case') {
              commit('setParamTypeEmergencyCase', {...Param})
            } else if (params === 'type_purchase_order') {
              commit('setParamTypePurchaseOrder', {...Param})
            } else if (params === 'type_reduction') {
              commit('setParamTypeReduction', {...Param})
            } else if (params === 'type_price') {
              commit('setParamTypePrice', {...Param})
            } else if (params === 'type_payment_method') {
              commit('setParamTypePaymentMethod', {...Param})
            }
          //cookies
        })
      }
    })
  },
  getParamInsurance({ commit }, params) {
    commit('setDataParamInsurance', params)
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

    if (params.code) {
      queryParams.push(`code=${params.code}`);
    }

    if (params.name) {
      queryParams.push(`name=${params.name}`);
    }

    if (params.variable) {
      queryParams.push(`variable=${params.variable}`);
    }

    let paramsall = queryParams.join('&');

    let param = { status: `setting/helper/list?${paramsall}` };

    useIPA.get(param).then(response => {
      const helper = {
        rows: response.data.data
      }
      commit('setData', { ...helper })
    })
  },
  // getAll ({ commit }) {
  //   let param = {status: 'setting/helper/detail'}
  //   useIPA.get(param).then(response => {
  //     const helper = {
  //       rows : response.data.data
  //     }
  //     commit('setData', {...helper})
  //   })
  // },
  getById({ commit }, id) {
    return new Promise((resolve, reject) => {
      let param = { status: 'master/bank/' + id }
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  formPostQueueLoket({ commit }, formData) {
    return new Promise((resolve, reject) => {
      const param = { rawData: formData, url: 'queue_loket/create'}
      useIPA.postputnoid(param)
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
  formPostPut({ commit }, formData) {
    return new Promise((resolve, reject) => {
      const rawData = {
        type_sys: formData.product,
        type_branch: formData.type_product,
        code: formData.code,
        name: formData.name,
        phone: formData.phone,
        email: formData.email,
        password: formData.repassword,
      }
      const param = { rawData: rawData, url: 'initial/register'}
      useIPA.postput(param)
        .then(response => {
          router.push({ name: 'login' })
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
        code: formData.code,
        name: formData.name,
        variable: formData.variable,
        description: formData.description,
      }
      let url = 'setting/helper/create'
      if (formData.id) {
        url = 'setting/helper/update'
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
      const param = { rawData: rawData, url: 'setting/helper/delete', id: id }
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
  getParamLoket({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `queue_loket/loket`
      }
      useIPA.get(param).then((response) => {
        const Param = {
          rows: response.data.data
        }
        commit('setParamLoket', { ...Param })
      })
    })
  }
}

// mutations
const mutations = {
  setData(state, helper) {
    state.items = helper.rows.rows
    state.totalRows = helper.rows.iTotal
    state.start = helper.rows.start
  },
  // setData (state, helper) {
  //   state.items = helper.rows
  //   state.totalRows = helper.rows.length
  // },
  clearParamCode(state) {
    state.options.code = []
  },
  setParamCode(state, data) {
    state.options.code.push(Object.assign({}, data))
  },
  setParamAlias(state, data) {
    state.options.alias = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = []
      for (const key in data.rows[i]) {
        if (key === 'code') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'code') { option['oid'] = data.rows[i][key] }
      }
      state.options.alias.push(Object.assign({}, option))
    }
  },
  setParamSex(state, data) {
    state.options.sex = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = []
      for (const key in data.rows[i]) {
        if (key === 'code') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'code') { option['oid'] = data.rows[i][key] }
      }
      state.options.sex.push(Object.assign({}, option))
    }
  },
  setParamMarried(state, data) {
    state.options.married = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = []
      for (const key in data.rows[i]) {
        if (key === 'code') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'code') { option['oid'] = data.rows[i][key] }
      }
      state.options.married.push(Object.assign({}, option))
    }
  },
  setParamReligion(state, data) {
    state.options.religion = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = [];
      for (const key in data.rows[i]) {
        if (key === 'code') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'code') { option['oid'] = data.rows[i][key] }
      }
      state.options.religion.push(Object.assign({}, option))
    }
  },
  setParamCitizenship(state, data) {
    state.options.citizenship = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = [];
      for (const key in data.rows[i]) {
        if (key === 'code') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'code') { option['oid'] = data.rows[i][key] }
      }
      state.options.citizenship.push(Object.assign({}, option))
    }
  },
  setParamBlood(state, data) {
    state.options.blood = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = [];
      for (const key in data.rows[i]) {
        if (key === 'code') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'code') { option['oid'] = data.rows[i][key] }
      }
      state.options.blood.push(Object.assign({}, option))
    }
  },
  setParamLastEdu(state, data) {
    state.options.lastedu = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = [];
      for (const key in data.rows[i]) {
        if (key === 'code') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'code') { option['oid'] = data.rows[i][key] }
      }
      state.options.lastedu.push(Object.assign({}, option))
    }
  },
  setParamProfession(state, data) {
    state.options.profession = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = [];
      for (const key in data.rows[i]) {
        if (key === 'code') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'code') { option['oid'] = data.rows[i][key] }
      }
      state.options.profession.push(Object.assign({}, option))
    }
  },
  setParamTypeAllergy(state, data) {
    state.options.typeallergy = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = [];
      for (const key in data.rows[i]) {
        if (key === 'code') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'code') { option['oid'] = data.rows[i][key] }
      }
      state.options.typeallergy.push(Object.assign({}, option))
    }
  },
  setParamResponsible(state, data) {
    state.options.responsible = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = [];
      for (const key in data.rows[i]) {
        if (key === 'code') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'code') { option['oid'] = data.rows[i][key] }
      }
      state.options.responsible.push(Object.assign({}, option))
    }
  },
  setDataParamInsurance(state, params) {
    const data = JSON.parse(localStorage.getItem(`helper-insurance`))
    console.log(params)
    for (let i = 0; i < data.rows.length; i++) {
      const option = [];
      if(data.rows[i]['insurance_type_id'] === params.value) {
        for (const key in data.rows[i]) {
          if (key === 'code') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'code') { option['oid'] = data.rows[i][key] }
        }
      }
      state.options.insurance.push(Object.assign({}, option))
    }
  },
  setParamInsurance(state, data) {
    state.options.insurance = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = [];
      for (const key in data.rows[i]) {
        if (key === 'code') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'code') { option['oid'] = data.rows[i][key] }
      }
      state.options.insurance.push(Object.assign({}, option))
    }
  },
  setParamHowToEnter(state, data) {
    state.options.howtoenter = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = [];
      for (const key in data.rows[i]) {
        if (key === 'code') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'code') { option['oid'] = data.rows[i][key] }
      }
      state.options.howtoenter.push(Object.assign({}, option))
    }
  },
  setParamTypeCosting(state, data) {
    state.options.typecosting = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = [];
      for (const key in data.rows[i]) {
        if (key === 'code') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'code') { option['oid'] = data.rows[i][key] }
      }
      state.options.typecosting.push(Object.assign({}, option))
    }
  },
  setParamTypeCostingC(state, data) {
    state.options.typecostingc = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = [];
      for (const key in data.rows[i]) {
        if (key === 'code') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'code') { option['oid'] = data.rows[i][key] }
      }
      state.options.typecostingc.push(Object.assign({}, option))
    }
  },
  setParamTypeIdcard(state, data) {
    state.options.type_idcard = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = [];
      for (const key in data.rows[i]) {
        if (key === 'code') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'code') { option['oid'] = data.rows[i][key] }
      }
      state.options.type_idcard.push(Object.assign({}, option))
    }
  },
  setParamDay(state, data) {
    state.options.day = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = [];
      for (const key in data.rows[i]) {
        if (key === 'code') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'code') { option['oid'] = data.rows[i][key] }
      }
      state.options.day.sort((a, b) => a.value - b.value).push(Object.assign({}, option))
    }
  },
  setParamIsNew(state, data) {
    state.options.isNew = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = [];
      for (const key in data.rows[i]) {
        if (key === 'code') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'code') { option['oid'] = data.rows[i][key] }
      }
      state.options.isNew.push(Object.assign({}, option))
    }
  },
  setParamTypeRegister(state, data) {
    state.options.typeRegister = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = [];
      for (const key in data.rows[i]) {
        if (key === 'code') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'code') { option['oid'] = data.rows[i][key] }
      }
      state.options.typeRegister.sort((a, b) => a.value - b.value).push(Object.assign({}, option))
    }
  },
  setParamTypeEmergency(state, data) {
    state.options.typeEmergency = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = [];
      for (const key in data.rows[i]) {
        if (key === 'code') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'code') { option['oid'] = data.rows[i][key] }
      }
      state.options.typeEmergency.sort((a, b) => a.value - b.value).push(Object.assign({}, option))
    }
  },
  setParamTypeEmergencyCase(state, data) {
    state.options.typeEmergencyCase = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = [];
      for (const key in data.rows[i]) {
        if (key === 'code') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'code') { option['oid'] = data.rows[i][key] }
      }
      state.options.typeEmergencyCase.sort((a, b) => a.value - b.value).push(Object.assign({}, option))
    }
  },
  setParamProvince(state, data) {
    state.options.province = []
    const option = []
    for (let i = 0; i < data.rows.length; i++) {
      for (const key in data.rows[i]) {
        if (key === 'id_province') { option['value'] = data.rows[i][key] } else if (key === 'nm_province') { option['title'] = data.rows[i][key] } else if (key === 'id_province') { option['oid'] = data.rows[i][key] }
      }
      state.options.province.push(Object.assign({}, option))
    }
  },
  setParamRegency(state, data) {
    state.options.regency = []
    const option = []
    for (let i = 0; i < data.rows.length; i++) {
      for (const key in data.rows[i]) {
        if (key === 'id_regency') { option['value'] = data.rows[i][key] } else if (key === 'nm_regency') { option['title'] = data.rows[i][key] } else if (key === 'id_regency') { option['oid'] = data.rows[i][key] }
      }
      state.options.regency.push(Object.assign({}, option))
    }
  },
  setParamLetterTypeId(state, data) {
    state.options.letter_type_id = []
    const option = []
    for (let i = 0; i < data.rows.length; i++) {
      for (const key in data.rows[i]) {
        if (key === 'letter_type_id') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'letter_type_id') { option['oid'] = data.rows[i][key] }
      }
      state.options.letter_type_id.push(Object.assign({}, option))
    }
  },
  setParamDistrict(state, data) {
    state.options.district = []
    const option = []
    for (let i = 0; i < data.rows.length; i++) {
      for (const key in data.rows[i]) {
        if (key === 'id_district') { option['value'] = data.rows[i][key] } else if (key === 'nm_district') { option['title'] = data.rows[i][key] } else if (key === 'id_district') { option['oid'] = data.rows[i][key] }
      }
      state.options.district.push(Object.assign({}, option))
    }
  },
  setParamVillage(state, data) {
    state.options.village = []
    const option = []
    for (let i = 0; i < data.rows.length; i++) {
      for (const key in data.rows[i]) {
        if (key === 'id_village') { option['value'] = data.rows[i][key] } else if (key === 'nm_village') { option['title'] = data.rows[i][key] } else if (key === 'id_village') { option['oid'] = data.rows[i][key] }
      }
      state.options.village.push(Object.assign({}, option))
    }
  },
  setParamThirdPartyCompany(state, data) {
    state.options.third_party_company = []
    const option = []
    for (let i = 0; i < data.rows.length; i++) {
      for (const key in data.rows[i]) {
        if (key === 'id') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows[i][key] }
      }
      state.options.third_party_company.push(Object.assign({}, option))
    }
  },
  setParamServiceClass(state, data) {
    state.options.service_class = []
    const option = []
    for (let i = 0; i < data.rows.length; i++) {
      for (const key in data.rows[i]) {
        if (key === 'id') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows[i][key] }
      }
      state.options.service_class.push(Object.assign({}, option))
    }
  },
  setParamPatientIssue(state, data) {
    state.options.patient_issue = []
    const option = []
    for (let i = 0; i < data.rows.length; i++) {
      for (const key in data.rows[i]) {
        if (key === 'code') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'code') { option['oid'] = data.rows[i][key] }
      }
      state.options.patient_issue.push(Object.assign({}, option))
    }
  },
  setParamTypeStatusBed(state, data) {
    state.options.type_status_bed = []
    const option = []
    for (let i = 0; i < data.rows.length; i++) {
      for (const key in data.rows[i]) {
        if (key === 'code') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'code') { option['oid'] = data.rows[i][key] }
      }
      state.options.type_status_bed.push(Object.assign({}, option))
    }
  },
  setParamTypeRoom(state, data) {
    state.options.type_room = []
    const option = []
    for (let i = 0; i < data.rows.length; i++) {
      for (const key in data.rows[i]) {
        if (key === 'code') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'code') { option['oid'] = data.rows[i][key] }
      }
      state.options.type_room.push(Object.assign({}, option))
    }
  },
  setParamTypePurchaseOrder (state, data) {
    state.options.typepurchaseorder = []
    const option = []
    for (let i = 0; i < data.rows.length; i++) {
      for (const key in data.rows[i]) {
        if (key === 'code') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'code') { option['oid'] = data.rows[i][key] }
      }
      state.options.typepurchaseorder.push(Object.assign({}, option))
    }
  },
  setParamTypeReduction (state, data) {
    state.options.typereduction = []
    const option = []
    for (let i = 0; i < data.rows.length; i++) {
      for (const key in data.rows[i]) {
        if (key === 'code') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'code') { option['oid'] = data.rows[i][key] }
      }
      state.options.typereduction.push(Object.assign({}, option))
    }
  },
  setParamTypePrice (state, data) {
    state.options.typeprice = []
    const option = []
    for (let i = 0; i < data.rows.length; i++) {
      for (const key in data.rows[i]) {
        if (key === 'code') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'code') { option['oid'] = data.rows[i][key] }
      }
      state.options.typeprice.push(Object.assign({}, option))
    }
  },
  setParamTypePaymentMethod (state, data) {
    state.options.typepaymentmethod = []
    const option = []
    for (let i = 0; i < data.rows.length; i++) {
      for (const key in data.rows[i]) {
        if (key === 'code') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'code') { option['oid'] = data.rows[i][key] }
      }
      state.options.typepaymentmethod.push(Object.assign({}, option))
    }
  },
  setParamTypeCatalog (state, data) {
    state.options.type_catalog = []
    const option = []
    for (let i = 0; i < data.rows.length; i++) {
      for (const key in data.rows[i]) {
        if (key === 'code') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'code') { option['oid'] = data.rows[i][key] }
      }
      state.options.type_catalog.push(Object.assign({}, option))
    }
  },
  setParamLoket(state, data) {
    state.options.loket = []
    for (let i = 0; i < data.rows.length; i++) {
      const option = [];
      for (const key in data.rows[i]) {
        if (key === 'id') { option['value'] = data.rows[i][key] } else if (key === 'name') { option['title'] = data.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows[i][key] }
      }
      state.options.loket.push(Object.assign({}, option))
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
