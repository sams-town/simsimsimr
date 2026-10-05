import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  perPage: 20,
  pageOptions: [20, 50, 100],
  totalRows: 1,
  sortOptions: [],
  items: [],
})

// getters
const getters = {}

// actions
const actions = {
  getAllList ({ commit }, params) {
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
    
      if (params.status) {
        queryParams.push(`status=${params.status}`)
      }
  
      if (params.name) {
        queryParams.push(`patient_name=${params.name}`)
      }

      if (params.date_start && params.date_end) {
        queryParams.push(`date_start=${params.date_start}`)
        queryParams.push(`date_end=${params.date_end}`)
      }    

      if (params.registration_destination_id) {
        queryParams.push(`registration_destination_id=${params.registration_destination_id}`)
      }

      if (params.registration_id) {
        queryParams.push(`registration_id=${params.registration_id}`)
      }

      if (params.date_type) {
        queryParams.push(`date_type=${params.date_type}`)
      }
    
      let paramsall = queryParams.join('&')
      let param = {status: `health_facility/operating_room/list?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  postPutOrder ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      let url = formData.id ? 'health_facility/operating_room/update' : 'health_facility/operating_room/create'

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

  putConfirmOrder ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData

      const param = { rawData: rawData, url: 'health_facility/operating_room/approve/', id: formData.order_id }
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

  delRemoveOrder ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'health_facility/operating_room/delete', id: id }
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

  getDetailById ({ commit }, params) {
		return new Promise((resolve, reject) => {
    
      let param = {status: `health_facility/operating_room/detail/${params.ok_id}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  getOperatorByOkId ({ commit }, params) {
		return new Promise((resolve, reject) => {
    
      let param = {status: `health_facility/operating_room/operator/${params.ok_id}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  postAddOperator ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData

      const param = { rawData: rawData, url: 'health_facility/operating_room/add_operator', id: '' }
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

  delRemoveOperator ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'health_facility/operating_room/delete_operator', id: id }
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

  postAddOperativeReport ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData

      const param = { rawData: rawData, url: 'mr/operating_room/operative_report/create', id: '' }
      useIPA.postput(param)
			.then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      },
      error => {
        const hasErrors = error?.response?.data?.hasOwnProperty('hasErrors')

        if (hasErrors) {
          reject(error.response.data.message)
        } else {
          reject(error.message)
        }
      })
		})
	},

  postPutOperativeReport ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData.form

      const param = { rawData: rawData, url: 'mr/operating_room/operative_report/update', id: formData.id }
      useIPA.postput(param)
			.then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      },
      error => {
        const hasErrors = error?.response?.data?.hasOwnProperty('hasErrors')
        if (hasErrors) {
          reject(error.response.data.message)
        } else {
          reject(error.message)
        }
      })
		})
	},

  getDetailOperativeReportById ({ commit }, params) {
		return new Promise((resolve, reject) => {
    
      let param = {status: `mr/operating_room/operative_report/detail/?operating_room_id=${params.ok_id}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  getDetailOperatingRoomByRegDes({ commit }, params) {
    return new Promise((resolve, reject) => {
    
      let param = {status: `health_facility/operating_room/detail/?registration_destination_id=${params.registration_destination_id}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  post_pre_op_handover_form ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData.form

      const param = { rawData: rawData, url: 'health_facility/operating_room/pre_op_handover', id: formData.id }
      useIPA.postput(param)
			.then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      },
      error => {
        const hasErrors = error?.response?.data?.hasOwnProperty('hasErrors')
        if (hasErrors) {
          reject(error.response.data.message)
        } else {
          reject(error.message)
        }
      })
		})
	},

  post_preoperative_nursing_form({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData.form
      const param = { rawData: rawData, url: 'health_facility/operating_room/preoperative_nursing', id: formData.id }
      useIPA.postput(param)
			.then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      },
      error => {
        const hasErrors = error?.response?.data?.hasOwnProperty('hasErrors')
        if (hasErrors) {
          reject(error.response.data.message)
        } else {
          reject(error.message)
        }
      })
		})
	},

  post_intraoperative_nursing_form({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData.form
      const param = { rawData: rawData, url: 'health_facility/operating_room/intraoperative_nursing', id: formData.id }
      useIPA.postput(param)
			.then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      },
      error => {
        const hasErrors = error?.response?.data?.hasOwnProperty('hasErrors')
        if (hasErrors) {
          reject(error.response.data.message)
        } else {
          reject(error.message)
        }
      })
		})
	},

  post_postoperative_nursing_form({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData.form
      const param = { rawData: rawData, url: 'health_facility/operating_room/postoperative_nursing', id: formData.id }
      useIPA.postput(param)
			.then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      },
      error => {
        const hasErrors = error?.response?.data?.hasOwnProperty('hasErrors')
        if (hasErrors) {
          reject(error.response.data.message)
        } else {
          reject(error.message)
        }
      })
		})
	},

  post_operational_safety_sign_in({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData.form
      const param = { rawData: rawData, url: 'health_facility/operating_room/operational_safety_sign_in', id: formData.id }
      useIPA.postput(param)
			.then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      },
      error => {
        const hasErrors = error?.response?.data?.hasOwnProperty('hasErrors')
        if (hasErrors) {
          reject(error.response.data.message)
        } else {
          reject(error.message)
        }
      })
		})
	},

  post_operational_safety_sign_out({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData.form
      const param = { rawData: rawData, url: 'health_facility/operating_room/operational_safety_sign_out', id: formData.id }
      useIPA.postput(param)
			.then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      },
      error => {
        const hasErrors = error?.response?.data?.hasOwnProperty('hasErrors')
        if (hasErrors) {
          reject(error.response.data.message)
        } else {
          reject(error.message)
        }
      })
		})
	},

  post_operational_safety_time_out({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData.form
      const param = { rawData: rawData, url: 'health_facility/operating_room/operational_safety_time_out', id: formData.id }
      useIPA.postput(param)
			.then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      },
      error => {
        const hasErrors = error?.response?.data?.hasOwnProperty('hasErrors')
        if (hasErrors) {
          reject(error.response.data.message)
        } else {
          reject(error.message)
        }
      })
		})
	},

  post_preanesthesia_evaluation({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData.form
      const param = { rawData: rawData, url: 'health_facility/operating_room/preanesthesia_evaluation', id: formData.id }
      useIPA.postput(param)
			.then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      },
      error => {
        const hasErrors = error?.response?.data?.hasOwnProperty('hasErrors')
        if (hasErrors) {
          reject(error.response.data.message)
        } else {
          reject(error.message)
        }
      })
		})
	},

  post_anesthesia_note({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData.form
      const param = { rawData: rawData, url: 'health_facility/operating_room/anesthesia_note', id: formData.id }
      useIPA.postput(param)
			.then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      },
      error => {
        const hasErrors = error?.response?.data?.hasOwnProperty('hasErrors')
        if (hasErrors) {
          reject(error.response.data.message)
        } else {
          reject(error.message)
        }
      })
		})
	},

  post_monitored_anesthesia({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData.form
      const param = { rawData: rawData, url: 'health_facility/operating_room/monitored_anesthesia', id: formData.id }
      useIPA.postput(param)
			.then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      },
      error => {
        const hasErrors = error?.response?.data?.hasOwnProperty('hasErrors')
        if (hasErrors) {
          reject(error.response.data.message)
        } else {
          reject(error.message)
        }
      })
		})
	},

  post_bromage_score({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData.form
      const param = { rawData: rawData, url: 'health_facility/operating_room/bromage_score', id: formData.id }
      useIPA.postput(param)
			.then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      },
      error => {
        const hasErrors = error?.response?.data?.hasOwnProperty('hasErrors')
        if (hasErrors) {
          reject(error.response.data.message)
        } else {
          reject(error.message)
        }
      })
		})
	},

  post_padss_score({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData.form
      const param = { rawData: rawData, url: 'health_facility/operating_room/padss_score', id: formData.id }
      useIPA.postput(param)
			.then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      },
      error => {
        const hasErrors = error?.response?.data?.hasOwnProperty('hasErrors')
        if (hasErrors) {
          reject(error.response.data.message)
        } else {
          reject(error.message)
        }
      })
		})
	},

  post_aldrete_score({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData.form
      const param = { rawData: rawData, url: 'health_facility/operating_room/aldrete_score', id: formData.id }
      useIPA.postput(param)
			.then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      },
      error => {
        const hasErrors = error?.response?.data?.hasOwnProperty('hasErrors')
        if (hasErrors) {
          reject(error.response.data.message)
        } else {
          reject(error.message)
        }
      })
		})
	},

  post_steward_score({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData.form
      const param = { rawData: rawData, url: 'health_facility/operating_room/steward_score', id: formData.id }
      useIPA.postput(param)
			.then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      },
      error => {
        const hasErrors = error?.response?.data?.hasOwnProperty('hasErrors')
        if (hasErrors) {
          reject(error.response.data.message)
        } else {
          reject(error.message)
        }
      })
		})
	},

  post_rr_action({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData.form
      const param = { rawData: rawData, url: 'health_facility/operating_room/rr_action', id: formData.id }
      useIPA.postput(param)
			.then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      },
      error => {
        const hasErrors = error?.response?.data?.hasOwnProperty('hasErrors')
        if (hasErrors) {
          reject(error.response.data.message)
        } else {
          reject(error.message)
        }
      })
		})
	},

  post_post_op_handover({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData.form
      const param = { rawData: rawData, url: 'health_facility/operating_room/post_op_handover', id: formData.id }
      useIPA.postput(param)
			.then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      },
      error => {
        const hasErrors = error?.response?.data?.hasOwnProperty('hasErrors')
        if (hasErrors) {
          reject(error.response.data.message)
        } else {
          reject(error.message)
        }
      })
		})
	},

}

// mutations
const mutations = {
  // setData (state, patient) {
  //   state.items = patient.rows
  //   state.totalRows = patient.rows.length
  // },
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
