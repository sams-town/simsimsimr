import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  nurseItems: [],
  perPage: 20,
  pageOptions: [20, 50, 100],
  totalRows: 1,
  sortOptions: [],
  fields: [
    { key: 'norm', label: 'No RM', sortable: true },
    { key: 'name', label: 'Nama Pasien'},
    { key: 'sex', label: 'Jenis Kelamin'},
    { key: 'birth_place', label: 'Tempat Lahir'},
    { key: 'birth_date', label: 'Tanggal Lahir'},
    { key: 'address', label: 'Alamat'},
    { key: 'action', label: 'Aksi'}
  ],
  items: [],
  fieldstransaction: [
    { key: 'date_added', label: 'Tanggal & Jam', sortable: true },
    { key: 'nurse_id', label: 'Nama Pasien'},
    { key: 'costing_name', label: 'Nama Pasien'},
    { key: 'notes', label: 'Keterangan'},
    { key: 'var_1', label: 'Harga'},
    { key: 'action', label: 'Aksi'}
  ],
  itemstransaction: [],
  itemsSoap: [],
})

// getters
const getters = {}

// actions
const actions = {
  getNurseById ({ commit }, nurse_id) {
		return new Promise((resolve, reject) => {
      let param = {status: `mr/nurse/detail/${nurse_id}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  putSections ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData.data

      const fixed_url = {
          ANAMNESA: 'mr/nurse/save_anamnesis',
          VITAL: 'mr/nurse/save_ttv_antropometri',
          FUNCTIONAL: 'mr/nurse/save_functional',
          HEMODIALYSIS: 'mr/nurse/save_hemodialysis',
          EMERGENCY: 'mr/nurse/save_emergency',
          ADULT: 'mr/nurse/save_adult_inpatient',
          CHILD: 'mr/nurse/save_child_inpatient',
          NEWBORNBABY: '/mr/nurse/save_newborn_baby',
          NEONATE: 'mr/nurse/save_neonate_inpatient',
          OBGYN: 'mr/nurse/save_obgyn',
          SPECIALOBG: 'mr/nurse/save_obgyn_special',
          PHYSIOTHERAPY: 'mr/nurse/save_rehab_fisio',
      };

      let url= ''
      if (fixed_url[formData.exist]) {
          url = fixed_url[formData.exist]
      }

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

  getNurseByRM ({ commit }, norm) {
    let param = {status: 'mr/nurse/list?norm='  + norm}
    useIPA.get(param).then(response => {
      const data = response.data.data
      commit('setNurseData', {...data})
    })
  },

  getHistoryNurse ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.limit) {
        queryParams.push(`limit=${params.limit}`)
      }

      if (params.norm) {
        queryParams.push(`norm=${params.norm}`)
      }

      if (params.registration_id) {
        queryParams.push(`registration_id=${params.registration_id}`)
      }

      let paramsall = queryParams.join('&')
      let param = {status: `mr/nurse/history?${paramsall}`}

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
  getAllList ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];
      if (params.norm) {
        queryParams.push(`norm=${params.norm}`)
      }

      let paramsall = queryParams.join('&')
      let param = {status: `mr/nurse/list?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data.rows)
      })
    })
  },

  getNurseList ({ commit }, params) {
    return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.limit != undefined) {
        queryParams.push(`limit=${params.limit}`)
      }

      if (params.offset != undefined) {
        queryParams.push(`offset=${params.offset}`)
      }

      if (params.norm) {
        queryParams.push(`norm=${params.norm}`)
      }

      if (params.registration_destination_id) {
        queryParams.push(`registration_destination_id=${params.registration_destination_id}`)
      }

      let paramsall = queryParams.join('&')
      let param = {status: `mr/nurse/list?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  removeNurse ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'mr/nurse/delete', id: id }
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

  getListVital ({ commit}, id ) {
    return new Promise((resolve, reject) => {
      let param = {status: 'mr/nurse/list_vital?offset=0&limit=25&registration_destination_id='  + id}
      useIPA.get(param).then(response => {
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
  getAll ({ commit }, id) {
      return new Promise((resolve, reject) => {
          let param = { status: 'mr/nurse/detail?registration_destination_id=' + id }
          useIPA.get(param)
          .then(response => {
              const patient = {
                  rows: response.data.data
              }
              commit('setData', { ...patient })
              resolve(response.data)
          })
          .catch(({response}) => {
              reject({message: response.data.message, debug: response.data.debug, response: response.data})
          })
      })
  },

  getDetail ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.registration_destination_id) {
        queryParams.push(`registration_destination_id=${params.registration_destination_id}`)
      }

      let paramsall = queryParams.join('&')
      let param = {status: `mr/nurse/detail?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  getEmergencyDetail ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.nurse_id) {
        queryParams.push(`nurse_id=${params.nurse_id}`)
      }

      let paramsall = queryParams.join('&')
      let param = {status: `mr/nurse_emergency/detail/?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  getAdultInpatientDetail ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.nurse_id) {
        queryParams.push(`nurse_id=${params.nurse_id}`)
      }

      let paramsall = queryParams.join('&')
      let param = {status: `mr/nurse_adult_inpatient/detail?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  getChildInpatientDetail ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.nurse_id) {
        queryParams.push(`nurse_id=${params.nurse_id}`)
      }

      let paramsall = queryParams.join('&')
      let param = {status: `mr/nurse_child_inpatient/detail?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  getNewbornInpatientDetail ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.nurse_id) {
        queryParams.push(`nurse_id=${params.nurse_id}`)
      }

      let paramsall = queryParams.join('&')
      let param = {status: `mr/newborn_baby/detail?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  getNeonatusInpatientDetail ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.nurse_id) {
        queryParams.push(`nurse_id=${params.nurse_id}`)
      }

      let paramsall = queryParams.join('&')
      let param = {status: `mr/nurse_neonate_inpatient/detail?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  getObgynDetail ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.nurse_id) {
        queryParams.push(`nurse_id=${params.nurse_id}`)
      }

      let paramsall = queryParams.join('&')
      let param = {status: `mr/nurse_obgyn/detail?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  getSpecialObgynDetail ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.nurse_id) {
        queryParams.push(`nurse_id=${params.nurse_id}`)
      }

      let paramsall = queryParams.join('&')
      let param = {status: `mr/nurse_obgyn_special/detail?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  //neonatus detail
  getXamNeonatusInpatientDetail ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let param = { status: `mr/neonatus/DetailNeonatus/${params.nurse_id}` };
      useIPA.get(param).then((response) => {
          resolve(response.data);
      });
    });
  },
  //child detail
  getXamChildInpatientDetail ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let param = { status: `mr/child/DetailChild/${params.nurse_id}` };
      useIPA.get(param).then((response) => {
          resolve(response.data);
      });
    });
  },
  getHemodialysisDetail ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = [];

      if (params.nurse_id) {
        queryParams.push(`nurse_id=${params.nurse_id}`)
      }

      let paramsall = queryParams.join('&')
      let param = {status: `mr/nurse_hemodialysis/detail/?${paramsall}`}

      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },

  getAllResolve ({ commit}, id) {
		return new Promise((resolve, reject) => {
    let param = {status: 'mr/nurse/detail?registration_destination_id='  + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getByNurseId ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'mr/nurse/detail?nurse_id=' + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getByNurseAdultId ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'mr/nurse_adult/detail?nurse_id=' + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getByNurseChildId ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'mr/nurse_child/detail?nurse_id=' + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  // getById ({ commit }, id) {
  //   let param = {status: 'mr/nurse/detail?registration_destination_id='  + id}
  //   useIPA.get(param).then(response => {
  //     const patient = {
  //       rows : response.data.data
  //     }
  //     commit('setData', {...patient})
  //   })
  // },
  getById ({ commit }, id) {
    let param = { status: 'mr/nurse/detail?registration_destination_id=' + id }
    return new Promise((resolve, reject) => {
      useIPA.get(param).then(response => {
        const patient = {
          rows: response.data.data
        }
        commit('setData', { ...patient })
        resolve(response.data.data)
      }).catch(error => {
        reject(error)
      })
    })
  },
  getByNorm ({ commit }, norm) {
    let param = {status: 'mr/nurse/detail?norm='  + norm}
    useIPA.get(param).then(response => {
      const patient = {
        rows : response.data.data
      }
      commit('setData', {...patient})
    })
  },
  getByRegistrationIdTransaction ({ commit }, id) {
    let param = {status: 'tr/transaction_nurse/detail?registration_id=' + id}
    useIPA.get(param).then(response => {
      const transaction = {
        rows : response.data.data
      }
      commit('setDataTransaction', {...transaction})
    })
  },
  getByIdTransaction ({ commit }, id) {
    let param = {status: 'tr/transaction_nurse/detail'}
    useIPA.get(param).then(response => {
      const transaction = {
        rows : response.data.data
      }
      commit('setDataTransaction', {...transaction})
    })
  },
  formPostPut ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData

      let url = formData.id ? 'mr/nurse/update' : 'mr/nurse/create_full'
      const param = { rawData: rawData, url: url, id: formData.id }
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
  PostPutNurseHemodialysis ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      let url = ''
      formData.id ? url = 'mr/nurse_hemodialysis/update' : url ='mr/nurse_hemodialysis/create'

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
  formPostPutNurseIGD ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      let url = ''
      formData.id ? url = 'mr/nurse_emergency/update' : url ='mr/nurse_emergency/create'

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
  formPostPutNurseInpatientAdult ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      let url = ''
      formData.id ? url = 'mr/nurse_adult_inpatient/update' : url ='mr/nurse_adult_inpatient/create'

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
  getByNurseIdOfPrintNeonatus({commit},params) {
    return new Promise((resolve, reject) => {
      let param = { status: `mr/neonatus/PrintNeonatus/${params}` };
      useIPA.get(param).then((response) => {
          resolve(response.data);
      });
  });
  },
  getByNurseIdOfPrintChild({commit},params) {
    return new Promise((resolve, reject) => {
      let param = { status: `mr/child/PrintChild/${params}` };
      useIPA.get(param).then((response) => {
          resolve(response.data);
      });
  });
  },
  getPartographById({commit},params) {
    return new Promise((resolve, reject) => {
      let param = { status: `mr/partograph/detail/${params}` };
      useIPA.get(param).then((response) => {
          resolve(response.data);
      });
  });
  },
  getPartographItemById({commit},params) {
    return new Promise((resolve, reject) => {
      let param = { status: `mr/partograph/get_hours/d153ad10-97f6-4d1b-86d7-b02834dec4e5?addition_time=30&max_time=2025-02-20 22:00:00` };
      useIPA.get(param).then((response) => {
          resolve(response.data);
      });
  });
  },
  //neonatus update or create
  formPostPutNurseInpatientNeonatus ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      let url = "mr/neonatus/create";
      const param = { rawData: formData, url: url };
      useIPA
          .postid(param)
          .then((response) => {
              if (response.data.hasErrors) {
                  reject(response.data.message);
              } else {
                  resolve(response.data);
              }
          })
          .catch((error) => {
              if (error.response.data.hasOwnProperty("hasErrors")) {
                  reject(error.response.data.debug);
              } else {
                  reject(error.debug);
              }
          });
      });
	},
  //child update or create
  formPostPutNurseInpatientChild ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      let url = "mr/child/create";
      const param = { rawData: formData, url: url };
      useIPA
          .postid(param)
          .then((response) => {
              if (response.data.hasErrors) {
                  reject(response.data.message);
              } else {
                  resolve(response.data);
              }
          })
          .catch((error) => {
              if (error.response.data.hasOwnProperty("hasErrors")) {
                  reject(error.response.data.debug);
              } else {
                  reject(error.debug);
              }
          });
      });
	},

  formPostPutSoap ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData

      let url = 'mr/nurse_soap/create'
      if (rawData.view === 'edit') {
        url = 'mr/nurse_soap/update'
      }
      const param = { rawData: rawData, url: url, id: rawData.id}
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
  PostPutPartograph ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      let url = ''
      formData.id ? url = 'mr/partograph/update' : url ='mr/partograph/create'

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
  PostPutPartographItem ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      const rawData = formData
      let url = ''
      formData.id ? url = 'mr/partograph/item' : url ='mr/partograph/item'

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
  // formPostSoap ({ commit }, formData) {
	// 	return new Promise((resolve, reject) => {
  //     const rawData = formData

  //     let url = 'mr/nurse_soap/create'
  //     const param = { rawData: rawData, url: url}
  //     useIPA.postputnoid(param)
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
  getSoapByNurseId({ commit }, nurse_id) {
    return new Promise((resolve, reject) => {

      let param = { status: `mr/nurse_soap/list?nurse_id=${nurse_id}` };
      useIPA.get(param).then(response => {
        const soaps = {
          rows: response.data.data
        }
        commit('setDataSoap', { ...soaps })
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          resolve(response.data)
        }
      })
    })
  },

  formPostPutTransaction ({ commit }, formData) {
		return new Promise((resolve, reject) => {

      let url = formData.id ? 'tr/transaction_nurse/update' : 'tr/transaction_nurse/create'

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

  removeTransaction ({ commit }, id) {
		return new Promise((resolve, reject) => {
      const rawData = {
        is_delete: 1,
			}
      const param = { rawData: rawData, url: 'tr/transaction_nurse/delete', id: id }
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
  setNurseData (state, res) {
    state.nurseItems = res.rows
    // state.nurseTotalRows = res.rows.length
  },
  setData (state, patient) {
    state.items = patient.rows
    state.totalRows = patient.rows.length
  },
  setDataTransaction (state, transaction) {
    state.itemstransaction = []
    state.itemstransaction = transaction.rows.sort((a, b) => new Date(a.date_added) - new Date(b.date_added))
  },
  setDataSoap (state, soaps) {
    state.itemsSoap = soaps.rows
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
