import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({

})

// getters
const getters = {}

// actions
const actions = {
  putApproveKardex ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData

      const param = { rawData: rawData, url: 'pharmacy/kardex/approve', id: formData.kardex_id }
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

  putTakeOrder ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData

      const param = { rawData: rawData, url: 'pharmacy/kardex/take_order', id: formData.kardex_id }
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

  getOptUnit ({ commit }, params) {
		return new Promise((resolve, reject) => {     
      let queryParams = [];

      if (params.package_id) {
        queryParams.push(`package_id=${params.package_id}`);
      }
          
      let paramsall = queryParams.join('&');
      let param = {status: `setting/unit/list?${paramsall}`};     

      useIPA.get(param)
      .then( response => {
          let resData = response.data.data.rows
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

  // getOptUnit ({ commit }) {
	// 	return new Promise((resolve, reject) => {          
  //     let param = {status: `setting/unit/list`};

  //     useIPA.get(param)
  //     .then( response => {
  //         let resData = response.data.data.rows
  //         let final_option = []

  //         for (let i = 0; i < resData.length; i++) {
  //           const el = resData[i];
  //           let temp = { value: el.id, title: el.name }
  //           final_option.push(temp)
  //         }
  //         resolve(final_option)
  //         return response;
  //     })
  //     .catch(({response}) => {
  //       reject({
  //         message: response.data.message, 
  //         debug: response.data.debug, 
  //         response: response.data,
  //       })
  //     })
  //   })
  // },

  getKardexStock ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.registration_destination_id) {
        queryParams.push(`registration_destination_id=${params.registration_destination_id}`);
      }
          
      let paramsall = queryParams.join('&');
      let param = {status: `pharmacy/kardex/stock?${paramsall}`};

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

  getOptNonPharma ({ commit }, params) {
		return new Promise((resolve, reject) => {                    
      let param = {status: `pharmacy/non_pharmacological/list`};

      useIPA.get(param)
      .then( response => {
          let resData = response.data.data.rows
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
          response: response,
        })
      })
    })
  },

  postPutSignaturaTime ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      let url = formData.id ? 'pharmacy/signatura_times/update' : 'pharmacy/signatura_times/create'
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

  deleteSignaturaTime ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'pharmacy/signatura_times/delete', id: id }
      useIPA.delete(param)
			.then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }

        return response;
      })
      .catch(({error, response}) => {
        reject({message: response.data.message, debug: response.data.debug, response: response.data,})
      })
		})
	},

  getOptSignaturaTime ({ commit }) {
		return new Promise((resolve, reject) => {          
      let param = {status: `pharmacy/signatura_times/list`};

      useIPA.get(param)
      .then( response => {
          let resData = response.data.data.rows
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

  getSignaturaTime ({ commit }, { params, cancelToken }) {
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
          
      let paramsall = queryParams.join('&');
      let param = {status: `pharmacy/signatura_times/list?${paramsall}`};

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

  postPutSignaturaDose ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      let url = formData.id ? 'pharmacy/signatura_dose/update' : 'pharmacy/signatura_dose/create'
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

  deleteSignaturaDose ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'pharmacy/signatura_dose/delete', id: id }
      useIPA.delete(param)
			.then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }

        return response;
      })
      .catch(({error, response}) => {
        reject({message: response.data.message, debug: response.data.debug, response: response.data,})
      })
		})
	},

  getOptSignaturaDose ({ commit }, params) {
		return new Promise((resolve, reject) => {          
      let queryParams = [];

      if (params.package_id) {
        queryParams.push(`package_id=${params.package_id}`);
      }
          
      let paramsall = queryParams.join('&');
      let param = {status: `pharmacy/signatura_dose/list?${paramsall}`};

      useIPA.get(param)
      .then( response => {
          let resData = response.data.data.rows
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
          response: response,
        })
      })
    })
  },

  getSignaturaDose ({ commit }, { params, cancelToken }) {
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

      if (params.package_id) {
        queryParams.push(`package_id=${params.package_id}`);
      }
          
      let paramsall = queryParams.join('&');
      let param = {status: `pharmacy/signatura_dose/list?${paramsall}`};

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

  putKardex ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData

      const param = { rawData: rawData, url: 'pharmacy/kardex/save' }
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

  putOrderKardex ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData

      const param = { rawData: rawData, url: 'pharmacy/kardex/order', id: formData.kardex_id }
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

  // postKardex ({ commit }, formData) {
	// 	return new Promise((resolve, reject) => {
  //     const rawData = formData

  //     const param = { rawData: rawData, url: 'pharmacy/kardex/create', id: '' }
  //     useIPA.postput(param)
  //     .then(response => {
  //       if (response.data.hasErrors) {
  //         reject(response.data.message)
  //       } else {
  //         resolve(response.data)
  //       }
  //       return response;
  //     })
  //     .catch(({response}) => {
  //       reject({message: response.data.message, debug: response.data.debug, response: response.data,})
  //     })
	// 	})
	// },

  getKardexDetail ({ commit }, params) {
    return new Promise((resolve, reject) => {
    
      let param = {status: `pharmacy/kardex/detail/${params.id}`};
    
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

  getKardexList ({ commit }, params) {
    return new Promise((resolve, reject) => {
      let queryParams = [];
    
      if (params.offset != undefined) {
        queryParams.push(`offset=${params.offset}`);
      }
    
      if (params.limit != undefined) {
        queryParams.push(`limit=${params.limit}`);
      }
    
      if (params.registration_destination_id) {
        queryParams.push(`registration_destination_id=${params.registration_destination_id}`);
      }
    
      if (params.is_active != undefined) {
        queryParams.push(`is_active=${params.is_active}`);
      }

      if (params.date_added) {
        queryParams.push(`date_added=${params.date_added}`);
      }

      if (params.list_type) {
        queryParams.push(`list_type=${params.list_type}`);
      }
      
      let paramsall = queryParams.join('&');
    
      let param = {status: `pharmacy/kardex/list?${paramsall}`};
    
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
}

// mutations
const mutations = {}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
