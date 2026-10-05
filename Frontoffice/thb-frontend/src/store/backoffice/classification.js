import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
    perPage: 20,
    pageOptions: [20, 50, 100],
    totalRows: 1,
    firstRow: 1,
    lastRow: 1,
    totalRows: 1,
    sortOptions: [],
    fields: [
        { key: 'account', label: 'Kode', sortable: true },
        { key: 'name', label: 'Nama', sortable: true },
        { key: 'is_delete', label: 'Status', sortable: true },
        { key: 'action', label: 'Aksi' },
    ],
    items: [],
    options: {
        classification: []
    }
})

// getters
const getters = {}

// actions
const actions = {
    getParam({ commit }, params) {
        return new Promise((resolve, reject) => {
            let param = {
                status: `backoffice/classification/list?type_id=${params}`
            }
            useIPA.get(param).then((response) => {
                const Param = {
                    rows: response.data.data
                }
                commit('setParamClassification', { ...Param })
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

        let param = { status: `backoffice/classification/list?${paramsall}` };

        useIPA.get(param).then(response => {
            const backoffice = {
                rows: response.data.data
            };
            commit('setData', { ...backoffice });
        });
    },

    getById({ commit }, id) {
        return new Promise((resolve, reject) => {
            let param = { status: 'backoffice/classification/detail/' + id }
            useIPA.get(param).then(response => {
                resolve(response.data.data)
            })
        })
    },
    formPostPut({ commit }, formData) {
        return new Promise((resolve, reject) => {
            let url = 'backoffice/classification/create'
            if (formData.id) {
                url = 'backoffice/classification/update'
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
            const param = { rawData: rawData, url: 'backoffice/classification/delete', id: id }
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
    setData(state, classification) {
        state.items = classification.rows.rows
        state.totalRows = classification.rows.iTotal
        state.start = classification.rows.start
    },
    setParamClassification(state, data) {
        state.options.classification = []
        for (let i = 0; i < data.rows.rows.length; i++) {
            const option = []
            for (const key in data.rows.rows[i]) {
                if (key === 'id') { option['value'] = data.rows.rows[i][key] } else if (key === 'name') { option['title'] = data.rows.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows.rows[i][key] }
            }
            state.options.classification.push(Object.assign({}, option))
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
