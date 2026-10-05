import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
  perPage: 20,
  pageOptions: [20, 50, 100],
  totalRows: 1,
  sortOptions: [],
  fields: [
    // { sortable: true, key: 'code', label: 'Kode', thStyle: { minWidth: '140px' } },
    // { sortable: true, key: 'register_date', label: 'Tanggal', thStyle: { minWidth: '140px' } },
    // { sortable: true, key: 'insurance_name', label: 'Cara Bayar', thStyle: { minWidth: '150px' } },
    // { sortable: true, key: 'type_how_to_enter_name', label: 'Cara Masuk', thStyle: { minWidth: '140px' } },
    // { sortable: true, key: 'exit_name', label: 'Status Pasien', thStyle: { minWidth: '140px' } },
    // { sortable: true, key: 'norm', label: 'No RM' },
    // { sortable: true, key: 'name', label: 'Nama', thStyle: { minWidth: '140px' } },
    // { sortable: true, key: 'is_new', label: 'Jenis Pasien', thStyle: { minWidth: '120px' } },
    // { sortable: true, key: 'birth_date', label: 'Tanggal Lahir', thStyle: { minWidth: '120px' } },
    // { sortable: true, key: 'age', label: 'Umur', thStyle: { minWidth: '140px' } },
    // { sortable: true, key: 'sex_name', label: 'Kelamin', thStyle: { minWidth: '140px' } },
    // { sortable: true, key: 'health_facility_name', label: 'Instalasi', thStyle: { minWidth: '140px' } },
    // { sortable: true, key: 'health_facility_item_name', label: 'Poli/Ruangan', thStyle: { minWidth: '140px' } },
    // { sortable: true, key: 'doctor_in_charge_name', label: 'Dokter', thStyle: { minWidth: '140px' } },
    // { sortable: true, key: 'queue', label: 'No Urut', thStyle: { minWidth: '80px' } },
    // { sortable: true, key: 'action', label: 'Aksi' },
    { sortable: true, key: 'code', label: 'No Pendaftaran', thStyle: { minWidth: '140px' } },
    { sortable: true, key: 'insurance_name', label: 'Cara Bayar', thStyle: { minWidth: '150px' } },
    { sortable: true, key: 'exit_name', label: 'Status Pasien', thStyle: { minWidth: '140px' } },
    { sortable: true, key: 'norm', label: 'No RM' },
    { sortable: true, key: 'name', label: 'Nama', thStyle: { minWidth: '140px' } },
    { sortable: true, key: 'is_new', label: 'Jenis Pasien', thStyle: { minWidth: '120px' } },
    { sortable: true, key: 'health_facility_item_name', label: 'Poli/Ruangan&DPJP', thStyle: { minWidth: '140px' } },
    { key: 'action', label: 'Aksi' },
  ],
  dp: {
    items: [],
    fields: [
      { key: 'var_1', label: 'Uang Muka' },
      { key: 'date_added', label: 'Tanggal' },
      { key: 'action', label: 'Aksi' }
    ]
  },
  items: [],
  invoice:{
    perPage: 20,
    pageOptions: [20, 50, 100],
    totalRows: 1,
    sortOptions: [],
    fields: [
      { key: 'no', label: 'No' },
      { key: 'var_3', label: 'Tagihan Pasien' },
      { key: 'var_2', label: 'Jumlah Terbayar' },
      // { key: 'remain', label: 'Sisa' },
      { key: 'date_added', label: 'Waktu' },
      { key: 'action_pay', label: 'Aksi' }
    ],
    fieldsInvoice: [
      { key: 'code', label: 'No.Invoice' },
      { key: 'var_3', label: 'Tagihan Pasien' },
      { key: 'var_2', label: 'Jumlah Terbayar' },
      // { key: 'remain', label: 'Sisa' },
      { key: 'date_added', label: 'Waktu' },
      { key: 'action_pay', label: 'Aksi' }
    ],
    items: []
  }
})

// getters
const getters = {}

// actions
const actions = {
  getDetail ({ commit }) {
    let param = {status: 'mr/registration/detail'}
    useIPA.get(param).then(response => {
      const patient = {
        rows : response.data.data
      }
      commit('setData', {...patient})
    })
  },
  getAll ({ commit }, params) {
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

    if (params.insurance_id) {
      if (params.company_id) {
      } else {
        queryParams.push(`insurance_id=${params.insurance_id}`)
      }
    }

    if (params.company_id) {
      queryParams.push(`company_id=${params.company_id}`)
    }

    if (params.date_start && params.date_end) {
      queryParams.push(`date_start=${params.date_start}`)
      queryParams.push(`date_end=${params.date_end}`)
    }

    let paramsall = queryParams.join('&')
  
    let param = {status: `mr/registration/list_new_inv?${paramsall}`}
    useIPA.get(param).then(response => {
      const registration = {
        rows : response.data.data
      }
      commit('setListData', {...registration})
    })
  },
  getByAllInvoice ({ commit }, id) {
		return new Promise((resolve, reject) => {
     let param = {status: 'tr/transaction_invoice/detail?registration_id=' + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getReport ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let param = {status: `report/cashier/daily_all?by=${params.daily_all}&date_start=${params.date_start}&date_end=${params.date_end}`}
      useIPA.get(param).then(response => {
        console.log(response)
        var bin = atob(response.data);
        var ab = s2ab(bin); // from example above
        var blob = new Blob([ab], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;' });
    
        var link = document.createElement('a');
        link.href = window.URL.createObjectURL(blob);
        link.download = 'demo.xlsx';
    
        document.body.appendChild(link);
    
        link.click();
    
        document.body.removeChild(link);
      })
    })
  },
  getReports ({ commit }, params) {
		return new Promise((resolve, reject) => {
      let queryParams = []
      if (params.daily_all) {
        queryParams.push(`by=${params.daily_all}`)
      }

      if (params.company_id) {
        queryParams.push(`company_id=${params.company_id}`)
      }
  
      if (params.date_start && params.date_end) {
        queryParams.push(`date_start=${params.date_start}`)
        queryParams.push(`date_end=${params.date_end}`)
      }
  
      let paramsall = queryParams.join('&')

      let param = {status: `report/cashier/daily_all?${paramsall}`}
      useIPA.get(param).then(response => {
        resolve(response.data)
      })
    })
  },
  getByAllInvoiceFree ({ commit }, id) {
		return new Promise((resolve, reject) => {
     let param = {status: 'tr/transaction_invoice/detail_free?pharmacy_prescription_id=' + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  // getByAllInvoice ({ commit }, id) {
  //   let param = {status: 'tr/transaction_invoice/detail?registration_id=' + id}
  //   useIPA.get(param).then(response => {
  //     const invoice = {
  //       rows : response.data.data
  //     }
  //     commit('setDataInvoice', {...invoice})
  //   },
  //   error => {
  //     commit('setDataInvoice', [])
  //     if (error.response.data.hasOwnProperty('hasErrors')) {
  //       commit('setDataInvoice', [])
  //       } else {
  //       commit('setDataInvoice', [])
  //     }
  //   })
  // },
  getByAllDP ({ commit }, id) {
    let param = {status: 'finance/downpayment/detail?registration_id=' + id}
    useIPA.get(param).then(response => {
      const invoice = {
        rows : response.data.data
      }
      commit('setDataDP', {...invoice})
    },
    error => {
      if (error.response.data.hasOwnProperty('hasErrors')) {
        // commit('setDataInvoice', [])
        } else {
        // commit('setDataInvoice', [])
      }
    })
  },
  getByDpId ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'finance/downpayment/detail?id=' + id}
      useIPA.get(param).then(response => {
          resolve(response.data.data)
      })
    })
  },
  getByInvoiceId ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'tr/transaction_invoice/detail?id=' + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getDepositByRegId ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'finance/downpayment/detail?registration_id=' + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  getByInvoiceRegisterId ({ commit }, id) {
		return new Promise((resolve, reject) => {
      let param = {status: 'tr/transaction_invoice/detail?registration_id=' + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
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
  formPostPutPayment ({ commit }, formData) {
    return new Promise((resolve, reject) => {
      let url = 'finance/payment/create'
      if (formData.id) {
        url = 'finance/payment/update'
      }
      const param = { rawData: formData, url: url, id: formData.id }
      useIPA.postput(param)
			.then(response => {
        if (response.data.hasErrors) {
          reject(response)
        } else {
          // router.push({ name: 'PatientPaymentListDetailView', params: { id: formData.registration_id}})
          router.push({ name: 'PaymentKwitansiView', params: { id: formData.invoice_id}})
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
  getCheckTransaction ({ commit }, id) {
		return new Promise((resolve, reject) => {
     let param = {status: 'tr/transaction_invoice/check_transaction?registration_id=' + id}
      useIPA.get(param).then(response => {
        resolve(response.data.data)
      })
    })
  },
  formPostPutPaymentFree ({ commit }, formData) {
    return new Promise((resolve, reject) => {
      console.log(formData)
      let url = 'finance/payment/create'
      if (formData.id) {
        url = 'finance/payment/update'
      }
      const param = { rawData: formData, url: url, id: formData.id }
      useIPA.postput(param)
			.then(response => {
        if (response.data.hasErrors) {
          reject(response.data.message)
        } else {
          // router.push({ name: 'PatientPaymentListDetailView', params: { id: formData.registration_id}})
          router.push({ name: 'PaymentKwitansiView', params: { id: formData.invoice_id}})
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
  formPostPutDP ({ commit }, formData) {
    return new Promise((resolve, reject) => {
      console.log(formData)
      let url = 'finance/downpayment/create'
      if (formData.id) {
        url = 'finance/downpayment/update'
      }
      const param = { rawData: formData, url: url, id: formData.id }
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
  formPostPutInvoice ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      console.log('formData at store: ', formData)

      const transactions = formData.transaction
      let checked_transaction = []

      if (formData && transactions) {
        for (let i = 0; i < transactions.length; i++) {
          const el = transactions[i];
          // checked_transaction.push(el)
          if (el.checked === true) {
            let temp_obj = {}
            if (el.transaction_from == 'itemsNurse') {
              temp_obj = {
                transaction_id: el.id,
                flag_status_id: 2,
                type_transaction_id: 1
              }
              checked_transaction.push(temp_obj)

            } else if (el.transaction_from == 'itemsAdministration') {
              temp_obj = {
                transaction_id: el.id,
                flag_status_id: 2,
                type_transaction_id: 13
              }
              checked_transaction.push(temp_obj)

            } else if (el.transaction_from == 'itemsDoctor') {
              temp_obj = {
                transaction_id: el.id,
                disc_id: el.disc_id,
                var_1: el.var_1,
                var_2: el.var_2,
                var_3: el.var_3,
                var_4: el.var_4,
                transaction_id: el.id,
                flag_status_id: 3,
                type_transaction_id: 2
              }
              checked_transaction.push(temp_obj)

            } else if (el.transaction_from == 'itemsLab') {
              temp_obj = {
                transaction_id: el.id,
                flag_status_id: 4,
                type_transaction_id: 11
              }
              checked_transaction.push(temp_obj)

            } else if (el.transaction_from == 'itemsRad') {
              temp_obj = {
                transaction_id: el.id,
                flag_status_id: 5,
                type_transaction_id: 12
              }
              checked_transaction.push(temp_obj)

            } else if (el.transaction_from == 'itemsPres') {
              temp_obj = {
                transaction_id: el.id,
                flag_status_id: 2,
                type_transaction_id: 14
              }
              checked_transaction.push(temp_obj)

            } else if (el.transaction_from == 'itemsPhy') {
              temp_obj = {
                transaction_id: el.id,
                flag_status_id: 2,
                type_transaction_id: 15
              }
              checked_transaction.push(temp_obj)

            } else if (el.transaction_from == 'itemsTool') {
              temp_obj = {
                transaction_id: el.id,
                flag_status_id: 8,
                type_transaction_id: 5
              }
              checked_transaction.push(temp_obj)

            } else if (el.transaction_from == 'itemsBlood') {
              temp_obj = {
                transaction_id: el.id,
                flag_status_id: 9,
                type_transaction_id: 7
              }
              checked_transaction.push(temp_obj)

            } else if (el.transaction_from == 'itemsBhp') {
              temp_obj = {
                transaction_id: el.id,
                flag_status_id: 10,
                type_transaction_id: 6
              }
              checked_transaction.push(temp_obj)

            } else if (el.transaction_from == 'itemsBed') {
              temp_obj = {
                transaction_id: el.id,
                flag_status_id: 11,
                type_transaction_id: 10
              }
              checked_transaction.push(temp_obj)
            }
          }
        }
      } else {
        console.log('No transaction list')
      }

      // console.log('checked_transaction', checked_transaction)

      const rawData = {
        registration_id: formData.toInvoice.registration_id,
        cashier_id: '3ada9806-8d98-4887-8bf8-c510483f454c',
        var_1: 0,
        var_2: 0,
        var_3: formData.toInvoice.grand_total,
        transactions: checked_transaction
			}

      // console.log('rawData: ', rawData);
      const param = { rawData: rawData, url: 'tr/transaction_invoice/create', id: '' }
      useIPA.postput(param)
			.then(response => {
        if (response.data.hasErrors) {
          // router.push({ path: 'payment/detail/'+formData.registration_id })
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
  formPostPutInvoiceFree ({ commit }, formData) {
		return new Promise((resolve, reject) => {
      console.log('formData at store: ', formData)
      // console.log('checked_transaction', checked_transaction)
      const rawData = {
        registration_id: formData.toInvoice.registration_id,
        cashier_id: '3ada9806-8d98-4887-8bf8-c510483f454c',
        var_1: 0,
        var_2: 0,
        var_3: formData.toInvoice.grand_total,
        transactions: formData.prescription
			}

      // console.log('rawData: ', rawData);
      const param = { rawData: rawData, url: 'tr/transaction_invoice/create_free', id: '' }
      useIPA.postput(param)
			.then(response => {
        if (response.data.hasErrors) {
          // router.push({ path: 'payment/detail/'+formData.registration_id })
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
  deleteInvoice ({ commit }, params) {
		return new Promise((resolve, reject) => {
      const param = { rawData: params, url: 'tr/transaction_invoice/delete', id: params.id }
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
  deletePayment ({ commit }, params) {
    return new Promise((resolve, reject) => {
      const param = { rawData: params, url: 'finance/payment/delete', id: params.id }
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
  setData (state, patient) {
    state.items = patient.rows
    state.totalRows = patient.rows.length
  },
  setListData (state, registration) {
    state.items = registration.rows.rows
    state.totalRows = registration.rows.iTotal
    state.start = registration.rows.start
  },
  setDataInvoice (state, invoice) {
    if (invoice.rows.length > 0) {
      invoice.rows.forEach(function (element,index) {
        element.no = index+1
      })
      state.invoice.items = invoice.rows
      state.invoice.totalRows = invoice.rows.length
    }
  },
  setDataDP (state, invoice) {
    invoice.rows.forEach(function (element,index) {
      element.no = index+1
    })
    state.dp.items = invoice.rows
    state.dp.totalRows = invoice.rows.length
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
