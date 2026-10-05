import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
    start: 20,
    currentPage: 1,
    totalRows: 1,
    fields: [
        { key: 'type_name', label: 'Tipe', sortable: true },
        { key: 'name', label: 'Nama', sortable: true },
        { key: 'coa', label: 'Akun', sortable: true },
        { key: 'is_delete', label: 'Status', sortable: true },
        { key: 'action', label: 'Aksi' },
    ],
    items: [],
    pageOptions: [20, 50, 100],
    sortOptions: [],
    options: {
        paymentmethod: []
    }
})

// getters
const getters = {}

// actions
const actions = {
    getParam({ commit }, params) {
        return new Promise((resolve, reject) => {
            let param = {
                status: `finance/payment_method/list?type_id=${params}`
            }
            useIPA.get(param).then((response) => {
                const Param = {
                    rows: response.data.data
                }
                commit('setParamPaymentMethod', { ...Param })
            })
        })
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

        if (params.name) {
            queryParams.push(`name=${params.name}`);
        }

        if (params.code) {
            queryParams.push(`code=${params.code}`);
        }

        // if (params.url) {
        //     queryParams.push(`url=${params.url}`);
        // }

        let paramsall = queryParams.join('&');

        let param = { status: `finance/payment_method/list?${paramsall}` };

        useIPA.get(param).then(response => {
            const finance = {
                rows: response.data.data
            };
            commit('setData', { ...finance });
        });
    },

    getById({ commit }, id) {
        return new Promise((resolve, reject) => {
            let param = { status: 'finance/payment_method/detail/' + id }
            useIPA.get(param).then(response => {
                resolve(response.data.data)
            })
        })
    },
    formPostPut({ commit }, formData) {
        return new Promise((resolve, reject) => {
            let url = 'finance/payment_method/create'
            if (formData.id) {
                url = 'finance/payment_method/update'
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
    remove({ commit }, id) {
        return new Promise((resolve, reject) => {
            const rawData = {
                is_delete: 1,
            }
            const param = { rawData: rawData, url: 'finance/payment_method/delete', id: id }
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
    setData(state, paymentmethod) {
        state.items = paymentmethod.rows.rows
        state.totalRows = paymentmethod.rows.iTotal
        state.start = paymentmethod.rows.start
    },
    setParamPaymentMethod(state, data) {
        state.options.paymentmethod = []
        for (let i = 0; i < data.rows.rows.length; i++) {
            const option = []
            for (const key in data.rows.rows[i]) {
                if (key === 'id') { option['value'] = data.rows.rows[i][key] } else if (key === 'name') { option['title'] = data.rows.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows.rows[i][key] }
            }
            state.options.paymentmethod.push(Object.assign({}, option))
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
