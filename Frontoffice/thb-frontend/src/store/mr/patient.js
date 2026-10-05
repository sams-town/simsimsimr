import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  // perPage: 20,
  start: 20,
  pageOptions: [20, 50, 100],
  totalRows: 1,
  sortOptions: [],
  options: {propinsi:[],kabupaten:[],kecamatan:[]},
  fields: [
    { key: 'action', label: 'Aksi' },
    { key: 'norm', label: 'No RM', sortable: true },
    { key: 'idcard', label: 'ID/KTP/SIM/Passport' },
    { key: 'name', label: 'Nama Pasien' },
    { key: 'sex_name', label: 'Jenis Kelamin' },
    { key: 'birth_place', label: 'Tempat Lahir' },
    { key: 'birth_date', label: 'Tanggal Lahir' },
    { key: 'address', label: 'Alamat' }
  ],
  items: [],
  allergy: {
    perPage: 20,
    pageOptions: [
      { value: '20', text: '20' },
      { value: '50', text: '50' },
      { value: '100', text: '100' },
    ],
    totalRows: 1,
    sortOptions: [],
    fields: [
      { key: 'no', label: 'No' },
      { key: 'name', label: 'Alergi' },
      { key: 'type_allergy_id', label: 'Jenis Alergi' },
      { key: 'reaction', label: 'Reaksi' },
      { key: 'severity', label: 'Tingkat Keparahan' },
      { key: 'side_effect', label: 'Efek Samping' },
      { key: 'date_tragedy', label: 'Tgl Kejadian' }
    ],
    items: []
  },
  patientMaster: {
    fields: [
      { key: 'action', label: 'Aksi', thStyle: { minWidth: '60px' } },
      { key: 'norm', label: 'NRM', thStyle: { minWidth: '140px' } },
      { key: 'name', label: 'Nama', thStyle: { minWidth: '140px' } },
      { key: 'birth_date', label: 'Tanggal Lahir', thStyle: { minWidth: '140px' } },
      // { key: 'birth_place', label: 'Tempat Lahir', thStyle: { minWidth: '140px' } },
      { key: 'idcard', label: 'No Identitas', thStyle: { minWidth: '140px' } },
      // { key: 'phone', label: 'No Telepon', thStyle: { minWidth: '140px' } },
      // { key: 'address', label: 'Alamat', thStyle: { minWidth: '140px' } },
      // { key: 'village_name', label: 'Kelurahan', thStyle: { minWidth: '140px' } },
      // { key: 'district_name', label: 'Kecamatan', thStyle: { minWidth: '140px' } },
      // { key: 'regency_name', label: 'Kodya', thStyle: { minWidth: '140px' } },
      // { key: 'province_name', label: 'Propinsi', thStyle: { minWidth: '140px' } },
      { key: 'date_updated', label: 'Last Updated', thStyle: { minWidth: '140px' } },
    ],
  },
})

// getters
const getters = {}

// actions
const actions = {
  putMergeNorm ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const param = { rawData: formData.norm, url: `mr/patient/merge_norm`, id: formData.new_norm }
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
  getAll({ commit }, params) {
    return new Promise((resolve, reject) => {
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
        queryParams.push(`norm=${params.norm}`);
      }

      if (params.idcard) {
        queryParams.push(`idcard=${params.idcard}`);
      }

      if (params.name) {
        queryParams.push(`name=${params.name}`);
      }

      if (params.birth_date) {
        queryParams.push(`birth_date=${params.birth_date}`);
      }
      if (params.birth_place) {
        queryParams.push(`birth_place=${params.birth_place}`);
      }
      if (params.address) {
        queryParams.push(`address=${params.address}`);
      }
      if (params.id_province) {
        queryParams.push(`province_id=${params.id_province.value}`);
      }
      if (params.regency_id) {
        queryParams.push(`regency_id=${params.regency_id.value}`);
      }
      if (params.district_id) {
        queryParams.push(`district_id=${params.district_id.value}`);
      }
      if (params.village_id) {
        queryParams.push(`village_id=${params.village_id.value}`);
      }
      if (params.zip_code) {
        queryParams.push(`zip_code=${params.zip_code}`);
      }
      if (params.sex) {
        queryParams.push(`sex=${params.sex}`);
      }

      let paramsall = queryParams.join('&');

      let param = { status: `mr/patient/list?${paramsall}` };
      useIPA.get(param).then(response => {
        const patient = {
          rows: response.data.data
        }
        commit('setListData', { ...patient })
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      })
    })
  },
  bpjs({ commit }, params) {
    // console.log(norm);
    return new Promise((resolve, reject) => {
      let url
      // console.log(params.type_search)
      if (params.url == 'noka') {
        if (params.type_search === 'RS') {
          url = `mr/patient/${params.url}/${params.idx}/${params.type_search}`
        } else {
          url = `mr/patient/${params.url}/${params.idx}`
        }
      } else if (params.url == 'nik') {
        url = `mr/patient/${params.url}/${params.idx}`
      } else if (params.url == 'RujukanList') {
        if (params.type_search === 'RS') {
          url = `mr/patient/${params.url}/${params.idx}/${params.type_search}`
        } else {
          url = `mr/patient/${params.url}/${params.idx}`
        }
      } else if (params.url == 'Rujukan') {
        if (params.type_search === 'RS') {
          url = `mr/patient/${params.url}/${params.idx}/${params.type_search}`
        } else {
          url = `mr/patient/${params.url}/${params.idx}`
        }
      } else if (params.url == 'Rujukan_first') {
        url = `mr/patient/${params.url}/${params.idx}`
      } else if (params.url == 'RujukanKontrolList') {
        url = `mr/patient/${params.url}/${params.idx}`
      } else if (params.url == 'Sep') {
        url = `mr/patient/${params.url}/${params.idx}`
      } else if (params.url == 'Control') {
        url = `mr/patient/${params.url}/${params.idx}`
      }
      let param = { status: url }
      useIPA.get(param).then(response => {
        if (params.url == 'noka') {
          resolve(response.data.data)
        } else if (params.url == 'nik') {
          resolve(response.data.data)
        } else if (params.url == 'RujukanList') {
          resolve(response.data)
        } else if (params.url == 'Rujukan') {
          resolve(response.data)
        } else if (params.url == 'Rujukan_first') {
          resolve(response.data)
        } else if (params.url == 'RujukanKontrolList') {
          resolve(response.data)
        } else if (params.url == 'Sep') {
          resolve(response.data)
        } else if (params.url == 'Control') {
          resolve(response.data)
        }
      })
    })
  },
  getDetail({ commit }) {
    let param = { status: 'mr/patient/detail' }
    useIPA.get(param).then(response => {
      const patient = {
        rows: response.data.data
      }
      commit('setData', { ...patient })
    })
  },
  getAllergyByNorm({ commit }, id) {
    let param = { status: 'mr/patient_allergy/detail/' + id }
    useIPA.get(param).then(response => {
      const allergy = {
        rows: response.data.data
      }
      commit('setDataAllergy', { ...allergy })
    })
  },
  getById({ commit }, id) {
    return new Promise((resolve, reject) => {
      let param = { status: 'master/bank/' + id }
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getBackdateNoka({ commit }, data) {
    return new Promise((resolve, reject) => {
      let param = { status: 'mr/patient/backdate/' + data.id + '/' + data.mr }
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getSuplesi({ commit }, data) {
    return new Promise((resolve, reject) => {
      let param = { status: 'mr/patient/suplesi/' + data.noka + '/' + data.tglsep }
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getDatakll({ commit }, data) {
    return new Promise((resolve, reject) => {
      let param = { status: 'mr/patient/datakll/' + data.noka }
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getByNorm({ commit }, norm) {
    // console.log(norm);
    return new Promise((resolve, reject) => {
      let param = { status: 'mr/patient/detail/' + norm }
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getBridge({ commit }) {
    return new Promise((resolve, reject) => {
      let param = { status: 'mr/patient/antrean_active' }
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getListMedRecordByNorm({ commit }, payload) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `mr/patient_manual_rm/list/${payload.norm}`,
        limit: payload.limit,
        offset: payload.offset
      }
      useIPA.get(param).then(response => {
        resolve(response.data)
      }).catch(error => {
        reject(error)
      })
    })
  },
  uploadMedRecord({ commit }, formData) {
    return new Promise((resolve, reject) => {
      let url = 'mr/patient_manual_rm/upload'
      const param = { rawData: formData, url: url, id: "" }
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
  deleteMedRecord({ commit }, id) {
    return new Promise((resolve, reject) => {
      const param = { url: 'mr/patient_manual_rm/delete', id: id }
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
  formPostPut({ commit }, formData) {
    return new Promise((resolve, reject) => {
      console.log('formData: ', formData);
      const rawData = {
        // institute_branch_id: formData.institute_branch_id,
        appointment_id: formData.appointment_id,
        norm: formData.norm,
        norm_mother: formData.norm_mother,
        idcard: formData.idcardparent === 4 ? formData.idcardparent : formData.idcard,
        idcardparent: formData.idcardparent,
        typeidcard: formData.typeidcard,
        alias: formData.alias,
        f_title: '',
        name: formData.name,
        b_title: '',
        fullname: formData.name,
        birth_date: formData.birth_date,
        birth_place: formData.birth_place,
        address: formData.address,
        sex: formData.sex,
        blood: formData.blood,
        married_id: formData.married_id,
        profession_id: formData.profession_id,
        citizenship_id: formData.citizenship_id,
        religion_id: formData.religion_id,
        last_edu_id: formData.last_edu_id,
        phone: formData.phone,
        email: formData.email,
        address_id: formData.address_id,
        zip_code: formData.zip_code,
        patient_issue_id: formData.patient_issue_id,
        patient_issue_text: formData.patient_issue_text,
        allergies: formData.allergyToInput,
        insurances: formData.insuranceToInput,
        responsible_id: formData.responsible_id,
        responsible_person_idcard: formData.responsible_person_idcard,
        responsible_person_typeidcard: formData.responsible_person_typeidcard,
        responsible_person_profession_id: formData.responsible_person_profession_id,
        responsible_person_name: formData.responsible_person_name,
        responsible_person_phone: formData.responsible_person_phone,
        responsible_person_address_id: formData.responsible_person_address_id,
      }
      console.log('rawData: ', rawData)
      // const param = { rawData: rawData, url: 'mr/patient/create', id: '' }
      const param = { rawData: rawData, url: 'mr/patient/create_v2', id: '' }
      useIPA.postput(param)
        .then(response => {
          // router.push({ name: 'RegisMasterView' })
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
  formUpdate({ commit }, formData) {
    return new Promise((resolve, reject) => {
      console.log('formData: ', formData);
      const rawData = {
        // institute_branch_id: formData.institute_branch_id,
        appointment_id: formData.appointment_id,
        norm: formData.norm,
        norm_mother: formData.norm_mother,
        idcard: formData.idcard,
        typeidcard: formData.typeidcard,
        alias: formData.alias,
        f_title: '',
        name: formData.name,
        b_title: '',
        fullname: formData.name,
        birth_date: formData.birth_date,
        birth_place: formData.birth_place,
        address: formData.address,
        sex: formData.sex,
        blood: formData.blood,
        married_id: formData.married_id,
        profession_id: formData.profession_id,
        citizenship_id: formData.citizenship_id,
        religion_id: formData.religion_id,
        last_edu_id: formData.last_edu_id,
        phone: formData.phone,
        email: formData.email,
        address_id: formData.address_id,
        zip_code: formData.zip_code,
        patient_issue_id: formData.patient_issue_id,
        patient_issue_text: formData.patient_issue_text,
        allergies: formData.allergyToInput,
        insurances: formData.insuranceToInput,
        responsible_id: formData.responsible_id,
        responsible_person_idcard: formData.responsible_person_idcard,
        responsible_person_typeidcard: formData.responsible_person_typeidcard,
        responsible_person_profession_id: formData.responsible_person_profession_id,
        responsible_person_name: formData.responsible_person_name,
        responsible_person_phone: formData.responsible_person_phone,
        responsible_person_address_id: formData.responsible_person_address_id,
      }
      console.log('rawData: ', rawData)
      // const param = { rawData: rawData, url: 'xx', id: formData.id }
      const param = { rawData: rawData, url: 'mr/patient/update', id: formData.norm }
      useIPA.postput(param)
        .then(response => {
          // router.push({ name: 'RegisMasterView' })
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
  getPropinsi({ commit }) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `mr/patient/propinsi`
      }
      useIPA.get(param).then((response) => {
        console.log(response)
        const Param = {
          rows: response.data.data
        }
        commit('setPropinsi', { ...Param })
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      })
    })
  },
  getKabupaten({ commit }, params) {
    return new Promise((resolve, reject) => {
      console.log(params)
      let param = {
        status: `mr/patient/kabupaten/${params}`
      }
      useIPA.get(param).then((response) => {
        console.log(response)
        const Param = {
          rows: response.data.data
        }
        commit('setKabupaten', { ...Param })
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      })
    })
  },
  getKecamatan({ commit }, params) {
    return new Promise((resolve, reject) => {
      let param = {
        status: `mr/patient/kecamatan/${params}`
      }
      useIPA.get(param).then((response) => {
        console.log(response)
        const Param = {
          rows: response.data.data
        }
        commit('setKecamatan', { ...Param })
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      })
    })
  },
  getRefPoli({ commit }, obj) {
    return new Promise((resolve, reject) => {
      let param = ''

      if(obj.serve == 2){ // SPRJ
        param = { status: 'mr/patient/ref_poli/2/' + obj.sep  + '/' + obj.tanggal }
      } else if (obj.serve == 1) { // SPRI
        param = { status: 'mr/patient/ref_poli/1/' + obj.number_insurance  + '/' + obj.tanggal }
      }
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getTotalSEP({ commit }, obj) {
    return new Promise((resolve, reject) => {
      let param = ''
      param = { status: 'mr/patient/total_sep/' + obj.jenisrujukan  + '/' + obj.norujukan }
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getRefDokter({ commit }, obj) {
    return new Promise((resolve, reject) => {
      let param = ''

      if (obj.serve == 1) { // SPRI
        param = { status: 'mr/patient/ref_dokter/1/' + obj.kodePoli + '/' + obj.tanggal }
      } else {
        param = { status: 'mr/patient/ref_dokter/2/' + obj.kodePoli + '/' + obj.tanggal }
      }
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getFaskesBPJS({ commit }, keywords) {
    return new Promise((resolve, reject) => {
      let param = { status: 'mr/patient/read_faskes?keywords=' + keywords }

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getObatPRB({ commit }, keywords) {
    return new Promise((resolve, reject) => {
      let param = { status: 'mr/patient/obat_prb/' + keywords }
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getDiagnosa({ commit }, keywords) {
    return new Promise((resolve, reject) => {
      let param = { status: 'mr/patient/diagnosa/' + keywords }
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getTaskid({ commit }, keywords) {
    return new Promise((resolve, reject) => {
      let param = { status: 'mr/patient/cektaskbook/' + keywords }
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  getIcare({ commit }, keywords) {
    return new Promise((resolve, reject) => {
      let param = { status: 'mr/patient/icare/' + keywords.number_insurance + '/' + keywords.bride_bpjs }
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  putIcareStatus ({ commit }, regDesId) {
		return new Promise((resolve, reject) => {
      const rawData = {
        icare_status: 1,
      }
      const param = { rawData: rawData, url: 'mr/registration_destination/icare_status', id: regDesId }
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

  getProcedure({ commit }, keywords) {
    return new Promise((resolve, reject) => {
      let param = { status: 'mr/patient/procedure/' + keywords }
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getPPKspesialis({ commit }, obj) {
    return new Promise((resolve, reject) => {
      let param = { status: 'mr/patient/ppkspesialishfis?PPKRujukan=' + obj.faskes.value + '&TglRujukan=' + obj.visit_date  }

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  formPostPutSEP({ commit }, formData) {
    return new Promise((resolve, reject) => {
      console.log('formData: ', formData);
      // const param = { rawData: rawData, url: 'xx', id: formData.id }
      const param = { rawData: formData, url: 'mr/patient/create_sep', id: '' }
      useIPA.postput(param)
      .then(response => {
        // router.push({ name: 'RegisMasterView' })
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
  formDeleteSEP({ commit }, formData) {
    return new Promise((resolve, reject) => {
      console.log('formData: ', formData);
      // const param = { rawData: rawData, url: 'xx', id: formData.id }
      const param = { rawData: formData, url: 'mr/patient/delete_sep', id: '' }
      useIPA.postput(param)
      .then(response => {
        // router.push({ name: 'RegisMasterView' })
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
  formDeleteKontrol({ commit }, formData) {
    return new Promise((resolve, reject) => {
      console.log('formData: ', formData);
      // const param = { rawData: rawData, url: 'xx', id: formData.id }
      const param = { rawData: formData, url: 'mr/patient/delete_control', id: '' }
      useIPA.postput(param)
      .then(response => {
        // router.push({ name: 'RegisMasterView' })
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
  formDeleteRujukan({ commit }, formData) {
    return new Promise((resolve, reject) => {
      console.log('formData: ', formData);
      // const param = { rawData: rawData, url: 'xx', id: formData.id }
      const param = { rawData: formData, url: 'mr/patient/delete_rujukan', id: '' }
      useIPA.postput(param)
      .then(response => {
        // router.push({ name: 'RegisMasterView' })
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
  formPostPutControl({ commit }, formData) {
    return new Promise((resolve, reject) => {
      console.log('formData: ', formData);
      // const param = { rawData: rawData, url: 'xx', id: formData.id }
      const param = { rawData: formData, url: 'mr/patient/create_sprik', id: '' }
      useIPA.postput(param)
      .then(response => {
        // router.push({ name: 'RegisMasterView' })
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
  updateControl({ commit }, formData) {
    return new Promise((resolve, reject) => {
      console.log('formData: ', formData);
      // const param = { rawData: rawData, url: 'xx', id: formData.id }
      const param = { rawData: formData, url: 'mr/patient/update_sprik', id: '' }
      useIPA.postput(param)
      .then(response => {
        // router.push({ name: 'RegisMasterView' })
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
  formPostPutBackdate({ commit }, formData) {
    return new Promise((resolve, reject) => {
      console.log('formData: ', formData);
      // const param = { rawData: rawData, url: 'xx', id: formData.id }
      const param = { rawData: formData, url: 'mr/patient/create_backdate', id: '' }
      useIPA.postput(param)
      .then(response => {
        // router.push({ name: 'RegisMasterView' })
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
  formPostPutRefer({ commit }, formData) {
    return new Promise((resolve, reject) => {
      console.log('formData: ', formData);
      // const param = { rawData: rawData, url: 'xx', id: formData.id }
      const param = { rawData: formData, url: 'mr/patient/create_refer', id: '' }
      useIPA.postput(param)
      .then(response => {
        // router.push({ name: 'RegisMasterView' })
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
  formPostPutReferSpecial({ commit }, formData) {
    return new Promise((resolve, reject) => {
      console.log('formData: ', formData);
      // const param = { rawData: rawData, url: 'xx', id: formData.id }
      const param = { rawData: formData, url: 'mr/patient/create_refer_special', id: '' }
      useIPA.postput(param)
      .then(response => {
        // router.push({ name: 'RegisMasterView' })
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
  formPostPutPrb({ commit }, formData) {
    return new Promise((resolve, reject) => {
      console.log('formData: ', formData);
      // const param = { rawData: rawData, url: 'xx', id: formData.id }
      const param = { rawData: formData, url: 'mr/patient/create_prb', id: '' }
      useIPA.postput(param)
      .then(response => {
        // router.push({ name: 'RegisMasterView' })
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
  formPostPutAproval({ commit }, formData) {
    return new Promise((resolve, reject) => {
      console.log('formData: ', formData);
      // const param = { rawData: rawData, url: 'xx', id: formData.id }
      const param = { rawData: formData, url: 'mr/patient/create_aproval', id: '' }
      useIPA.postput(param)
      .then(response => {
        // router.push({ name: 'RegisMasterView' })
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
  formPostPutCheckout({ commit }, formData) {
    return new Promise((resolve, reject) => {
      console.log('formData: ', formData);
      // const param = { rawData: rawData, url: 'xx', id: formData.id }
      const param = { rawData: formData, url: 'mr/patient/create_checkout', id: '' }
      useIPA.postput(param)
      .then(response => {
        // router.push({ name: 'RegisMasterView' })
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
  // formPostPut ({ commit }, formData) {
  // 	return new Promise((resolve, reject) => {
  //     const rawData = new FormData()
  //     rawData.set('name', formData.text_1)
  //     rawData.set('desc', formData.text_2)
  //     if (formData.setImage === 'upload') {
  //       rawData.append('icon', formData.icon)
  // 		} else {
  //       rawData.set('icon', formData.icon)
  // 		}
  //     const param = { rawData: rawData, url: 'backoffice/master/bank', id: formData.id}
  //     useIPA.postput(param)
  // 		.then(response => {
  //       router.push({ name: 'app-bank' })
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
  remove({ commit }, id) {
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
  setPropinsi(state, data) {
    state.options.propinsi = []
    console.log(data)
    for (let i = 0; i < data.rows.length; i++) {
      const option = []
      for (const key in data.rows[i]) {
        if (key === 'kodepropinsi') { option['value'] = data.rows[i][key] } else if (key === 'namapropinsi') { option['title'] = data.rows[i][key] } else if (key === 'kodepropinsi') { option['oid'] = data.rows[i][key] }
      }
      state.options.propinsi.push(Object.assign({}, option))
    }
  },
  setKabupaten(state, data) {
    state.options.kabupaten = []
    console.log(data)
    for (let i = 0; i < data.rows.length; i++) {
      const option = []
      for (const key in data.rows[i]) {
        if (key === 'kodekabupaten') { option['value'] = data.rows[i][key] } else if (key === 'namakabupaten') { option['title'] = data.rows[i][key] } else if (key === 'kodekabupaten') { option['oid'] = data.rows[i][key] }
      }
      state.options.kabupaten.push(Object.assign({}, option))
    }
  },
  setKecamatan(state, data) {
    state.options.kecamatan = []
    console.log(data)
    for (let i = 0; i < data.rows.length; i++) {
      const option = []
      for (const key in data.rows[i]) {
        if (key === 'kodekecamatan') { option['value'] = data.rows[i][key] } else if (key === 'namakecamatan') { option['title'] = data.rows[i][key] } else if (key === 'kodekecamatan') { option['oid'] = data.rows[i][key] }
      }
      state.options.kecamatan.push(Object.assign({}, option))
    }
  }
}

// mutations
const mutations = {
  setData(state, patient) {
    state.items = patient.rows
    state.totalRows = patient.rows.length
  },
  setListData(state, patient) {
    state.items = patient.rows.rows
    state.totalRows = patient.rows.iTotal
    state.start = patient.rows.start
  },
  setDataAllergy(state, allergy) {
    state.allergy.items = allergy.rows
    state.allergy.totalRows = allergy.rows.length
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
