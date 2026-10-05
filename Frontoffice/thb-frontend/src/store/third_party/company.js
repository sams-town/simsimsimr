import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
    start: 20,
    currentPage: 1,
    totalRows: 1,
    fields: [
        { key: 'insurance_type_id', label: 'Flag', sortable: true },
        { key: 'name', label: 'Nama', sortable: true },
        { key: 'address', label: 'Alamat', sortable: true },
        { key: 'account_alias', label: 'PIC', sortable: true },
        { key: 'phone', label: 'Telepon', sortable: true },
        { key: 'is_delete', label: 'Status', sortable: true },
        { key: 'action', label: 'Aksi' },
    ],
    items: [],
    pageOptions: [20, 50, 100],
    sortOptions: [],
    options: {
        company: [],
        supplier: [],
        factory: []
    }
})

// getters
const getters = {}

// actions
const actions = {
    getParam({ commit }, params) {
        return new Promise((resolve, reject) => {
            let param = {
                status: `/third_party/company/list=${params}`
            }
            useIPA.get(param).then((response) => {
                resolve(response.data.data)
                const Param = {
                    rows: response.data.data
                }
                commit('setParamCompany', { ...Param })
            })
        })
    },
    getParamcompany({ commit }, params) {
        return new Promise((resolve, reject) => {
            let param = {
                status: `/third_party/company/list?insurance_type_id=${params}`
            }
            useIPA.get(param).then((response) => {
                resolve(response.data.data)
                const Param = {
                    rows: response.data.data
                }
                commit('setParamCompany', { ...Param })
            })
        })
    },
    getParamFlag({ commit }, params) {
        return new Promise((resolve, reject) => {
            let param = {
                status: `/third_party/company/list?flag=${params}`
            }
            useIPA.get(param).then((response) => {
                resolve(response.data.data)
                const Param = {
                    rows: response.data.data
                }
                if (params === 1){
                    commit('setParamInsurance', { ...Param })
                } else if (params === 3){
                    commit('setParamSupplier', { ...Param })
                } else if (params === 4) {
                    commit('setParamFactory', { ...Param })
                }
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

        if (params.flag) {
            queryParams.push(`flag=${params.flag}`);
        }

        let paramsall = queryParams.join('&');

        let param = { status: `third_party/company/list?${paramsall}` };

        useIPA.get(param).then(response => {
            const company = {
                rows: response.data.data
            };
            commit('setData', { ...company });
        });
    },

    getById({ commit }, id) {
        return new Promise((resolve, reject) => {
            let param = { status: 'third_party/company/detail/' + id }
            useIPA.get(param).then(response => {
                resolve(response.data.data)
            })
        })
    },
    formPostPut({ commit }, formData) {
        return new Promise((resolve, reject) => {
            const rawData = {
                flag: formData.flag,
                phone: formData.phone,
                account_alias: formData.account_alias,
                insurance_type_id: formData.insurance_type_id,
                name: formData.name,
            }
            let url = 'third_party/company/create'
            if (formData.id) {
                url = 'third_party/company/update'
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
            const param = { rawData: rawData, url: 'third_party/company/delete', id: id }
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
    setData(state, company) {
        state.items = company.rows.rows
        state.totalRows = company.rows.iTotal
        state.start = company.rows.start
    },
    setParamInsurance(state, data) {
        state.options.company = []
        for (let i = 0; i < data.rows.rows.length; i++) {
            const option = []
            for (const key in data.rows.rows[i]) {
                if (key === 'id') { option['value'] = data.rows.rows[i][key] } else if (key === 'name') { option['title'] = data.rows.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows.rows[i][key] }
            }
            state.options.company.push(Object.assign({}, option))
        }
    },
    setParamCompany(state, data) {
        state.options.company = []
        for (let i = 0; i < data.rows.rows.length; i++) {
            const option = []
            for (const key in data.rows.rows[i]) {
                if (key === 'id') { option['value'] = data.rows.rows[i][key] } else if (key === 'name') { option['title'] = data.rows.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows.rows[i][key] }
            }
            state.options.company.push(Object.assign({}, option))
        }
    },
    setParamSupplier(state, data) {
        state.options.supplier = []
        for (let i = 0; i < data.rows.rows.length; i++) {
            const option = []
            for (const key in data.rows.rows[i]) {
                if (key === 'id') { option['value'] = data.rows.rows[i][key] } else if (key === 'name') { option['title'] = data.rows.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows.rows[i][key] }
            }
            state.options.supplier.push(Object.assign({}, option))
        }
    },
    setParamFactory(state, data) {
        state.options.factory = []
        for (let i = 0; i < data.rows.rows.length; i++) {
            const option = []
            for (const key in data.rows.rows[i]) {
                if (key === 'id') { option['value'] = data.rows.rows[i][key] } else if (key === 'name') { option['title'] = data.rows.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows.rows[i][key] }
            }
            state.options.factory.push(Object.assign({}, option))
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
