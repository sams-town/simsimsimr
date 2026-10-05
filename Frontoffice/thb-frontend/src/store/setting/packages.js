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
        packages: []
    }
})

// getters
const getters = {}

// actions
const actions = {
    getParam({ commit }, params) {
        return new Promise((resolve, reject) => {
            let param = {
                status: `setting/package/list`
            }
            useIPA.get(param).then((response) => {
                const Param = {
                    rows: response.data.data
                }
                commit('setParamPackages', { ...Param })
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

        let param = { status: `setting/package/list?${paramsall}` };

        useIPA.get(param).then(response => {
            const packages = {
                rows: response.data.data
            };
            commit('setData', { ...packages });
        });
    },

    getById({ commit }, id) {
        return new Promise((resolve, reject) => {
            let param = { status: 'setting/package/detail/' + id }
            useIPA.get(param).then(response => {
                resolve(response.data.data)
            })
        })
    },
    formPostPut({ commit }, formData) {
        return new Promise((resolve, reject) => {
            const rawData = {
                url: formData.url,
                icon: formData.icon,
                name: formData.name,
            }
            let url = 'setting/package/create'
            if (formData.id) {
                url = 'setting/package/update'
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
            const param = { rawData: rawData, url: 'setting/package/delete', id: id }
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
    setData(state, packages) {
        state.items = packages.rows.rows
        state.totalRows = packages.rows.iTotal
        state.start = packages.rows.start
    },
    setParamPackages(state, data) {
        state.options.packages = []
        for (let i = 0; i < data.rows.rows.length; i++) {
            const option = []
            for (const key in data.rows.rows[i]) {
                if (key === 'id') { option['value'] = data.rows.rows[i][key] } else if (key === 'name') { option['title'] = data.rows.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows.rows[i][key] }
            }
            state.options.packages.push(Object.assign({}, option))
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
