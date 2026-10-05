import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  start: 20,
  totalRows: 1,
  items: [],
  pageOptions: [20, 50, 100],
  sortOptions: [],
  options:{
    employee:[],
  }
})

// getters
const getters = {}

// actions
const actions = {
  getPractitioner({ commit }, { params, cancelToken }) {
    return new Promise((resolve, reject) => {
      let queryParams = [];
  
      if (params.offset !== undefined) {
        queryParams.push(`offset=${params.offset}`);
      }
  
      if (params.limit !== undefined) {
        queryParams.push(`limit=${params.limit}`);
      }
  
      if (params.name) {
        queryParams.push(`name=${params.name}`);
      }
  
      if (params.birth_date) {
        queryParams.push(`birth_date=${params.birth_date}`);
      }

      if (params.health_facility_item_id) {
        queryParams.push(`health_facility_item_id=${params.health_facility_item_id}`)
      }

      let paramsall = queryParams.join('&');
      let param = { status: `satusehat/practitioner/list?${paramsall}` };
  
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
  getPractitionerBride({ commit }, { params, cancelToken }) {
    return new Promise((resolve, reject) => {
      let queryParams = [];
  
      if (params.search) {
        queryParams.push(`search=${params.search}`);
      }

      let paramsall = queryParams.join('&');
      let param = { status: `satusehat/practitioner/bride?${paramsall}` };
  
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
  getDepartment({ commit }, { params, cancelToken }) {
    return new Promise((resolve, reject) => {
      let queryParams = [];
  
      if (params.offset !== undefined) {
        queryParams.push(`offset=${params.offset}`);
      }
  
      if (params.limit !== undefined) {
        queryParams.push(`limit=${params.limit}`);
      }
  
      if (params.name) {
        queryParams.push(`name=${params.name}`);
      }
      
      let paramsall = queryParams.join('&');
      let param = { status: `satusehat/department/list?${paramsall}` };
  
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
  getDepartmentBride({ commit }, { params, cancelToken }) {
    return new Promise((resolve, reject) => {
      let queryParams = [];
  
      if (params.search) {
        queryParams.push(`search=${params.search}`);
      }

      let paramsall = queryParams.join('&');
      let param = { status: `satusehat/department/bride?${paramsall}` };
  
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
}

// mutations
const mutations = {
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
