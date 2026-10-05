import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
    start: 20,
    currentPage: 1,
    totalRows: 1,
    fields: [
        { key: 'name', label: 'Nama', sortable: true },
        { key: 'is_delete', label: 'Status', sortable: true },
        { key: 'action', label: 'Aksi' },
    ],
    items: [],
    pageOptions: [20, 50, 100],
    sortOptions: [],
    options: {
        timestamp: []
    }
})

// getters
const getters = {}

// actions
const actions = {
    getParam({ commit }, params) {
        return new Promise((resolve, reject) => {
            let param = {
                status: `/setting/timestamp/list=${params}`
            }

            useIPA.get(param).then((response) => {
                resolve(response.data.data)
                const Param = {
                    rows: response.data.data
                }
                commit('setParamTimestamp', { ...Param })
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

        let paramsall = queryParams.join('&');

        let param = { status: `setting/timestamp/list?${paramsall}` };

        useIPA.get(param).then(response => {
            const timestamp = {
                rows: response.data.data
            };
            commit('setData', { ...timestamp });
        });
    },

    getById({ commit }, id) {
        return new Promise((resolve, reject) => {
            let param = { status: 'setting/timestamp/detail/' + id }
            useIPA.get(param).then(response => {
                resolve(response.data.data)
            })
        })
    },
    // ini kebawah belum
    formPostPut({ commit }, formData) {
        return new Promise((resolve, reject) => {
            const rawData = {
                module_id: formData.module_id,
                name: formData.name,
            }
            let url = 'setting/timestamp/create'
            if (formData.id) {
                url = 'setting/timestamp/update'
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
            const param = { rawData: rawData, url: 'setting/timestamp/delete', id: id }
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
    setData(state, timestamp) {
        state.items = timestamp.rows.rows
        state.totalRows = timestamp.rows.iTotal
        state.start = timestamp.rows.start
    },
    setParamTimestamp(state, data) {
        state.options.timestamp = []
        for (let i = 0; i < data.rows.rows.length; i++) {
            const option = []
            for (const key in data.rows.rows[i]) {
                if (key === 'id') { option['value'] = data.rows.rows[i][key] } else if (key === 'name') { option['title'] = data.rows.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows.rows[i][key] }
            }
            state.options.timestamp.push(Object.assign({}, option))
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
