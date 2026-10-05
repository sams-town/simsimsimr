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
        letter: []
    }
})

// getters
const getters = {}

// actions
const actions = {
    getParam({ commit }, params) {
        return new Promise((resolve, reject) => {
            let param = {
                status: `setting/letter/list`
            }
            useIPA.get(param).then((response) => {
                const Param = {
                    rows: response.data.data
                }
                if (params === 'letter') {
                    commit('setParamLetter', { ...Param })
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

        let paramsall = queryParams.join('&');

        let param = { status: `setting/letter/list?${paramsall}` };

        useIPA.get(param).then(response => {
            const letter = {
                rows: response.data.data
            };
            commit('setData', { ...letter });
        });
    },

    getById({ commit }, id) {
        return new Promise((resolve, reject) => {
            let param = { status: 'setting/letter/detail/' + id }
            useIPA.get(param).then(response => {
                resolve(response.data.data)
            })
        })
    },
    formPostPut({ commit }, formData) {
        return new Promise((resolve, reject) => {
            const rawData = {
                name: formData.name,
                content: formData.content,
                letter_type_id: formData.letter_type_id
            }
            let url = 'setting/letter/create'
            if (formData.id) {
                url = 'setting/letter/update'
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
            const param = { rawData: rawData, url: 'setting/letter/delete', id: id }
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
    setData(state, letter) {
        state.items = letter.rows.rows
        state.totalRows = letter.rows.iTotal
        state.start = letter.rows.start
    },
    // setParamLetter(state, data) {
    //     state.options.letter = []
    //     for (let i = 0; i < data.rows.rows.length; i++) {
    //         const option = []
    //         for (const key in data.rows.rows[i]) {
    //             if (key === 'id') { option['value'] = data.rows.rows[i][key] } else if (key === 'name') { option['title'] = data.rows.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows.rows[i][key] }
    //         }
    //         state.options.letter.push(Object.assign({}, option))
    //     }
    // }
    setParamLetter(state, data) {
        state.options.letter = []
        for (let i = 0; i < data.rows.rows.length; i++) {
            const row = data.rows.rows[i]
            const option = {
                value: row.id,
                title: row.name,
                is_informed_consent: row.is_informed_consent
            }
            // for (const key in data.rows.rows[i]) {
            //     if (key === 'id') { option['value'] = data.rows.rows[i][key] } else if (key === 'name') { option['title'] = data.rows.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows.rows[i][key] }
            // }
            state.options.letter.push(Object.assign(option))
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
