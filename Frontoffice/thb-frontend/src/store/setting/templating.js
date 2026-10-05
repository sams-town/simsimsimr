import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
})

// getters
const getters = {}

// actions
const actions = {
  postPutAnamTemplate ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      let url = formData.id ? 'setting/user/template_anamnesis/update' : 'setting/user/template_anamnesis/create'

      const param = { rawData: rawData, url: url, id: formData.id }
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

  getAnamTemplate ({ commit }) {
		return new Promise((resolve, reject) => {
      let param = {status: `setting/user/template_anamnesis/detail`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  deleteAnamTemplate ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'setting/user/template_anamnesis/delete', id: id }
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
	},

  postPutPrescTemplate ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      let url = formData.id ? 'setting/template_prescription/update' : 'setting/template_prescription/create'

      const param = { rawData: rawData, url: url, id: formData.id }
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

  getPrescTemplate ({ commit }) {
		return new Promise((resolve, reject) => {
      let param = {status: `setting/template_prescription/detail`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  deletePrescTemplate ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'setting/template_prescription/delete', id: id }
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
	},

  postPutEducationResumeTemplate ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      let url = formData.id ? 'setting/user/template_resume/update' : 'setting/user/template_resume/create'

      const param = { rawData: rawData, url: url, id: formData.id }
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

  getEducationResumeTemplate ({ commit }) {
		return new Promise((resolve, reject) => {
      let param = {status: `setting/user/template_resume/detail`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  deleteEducationResumeTemplate ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'setting/user/template_resume/delete', id: id }
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
