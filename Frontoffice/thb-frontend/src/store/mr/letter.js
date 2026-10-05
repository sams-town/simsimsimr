import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
    start: 20,
    currentPage: 1,
    totalRows: 1,
    fields: [
        { key: 'no', label: 'No', sortable: true },
        { key: 'letter_type_name', label: 'Surat', sortable: true },
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
                status: `/mr/letter/list=${params}`
            }
            useIPA.get(param).then((response) => {
                resolve(response.data.data)
                const Param = {
                    rows: response.data.data
                }
                commit('setParamLetter', { ...Param })
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

        if (params.no) {
            queryParams.push(`no=${params.no}`);
        }

        if (params.registration_destination_id) {
            queryParams.push(`registration_destination_id=${params.registration_destination_id}`);
        }

        if (params.norm) {
            queryParams.push(`norm=${params.norm}`);
        }

        if (params.letter_type_id) {
            queryParams.push(`letter_type_id=${params.letter_type_id}`);
        }

        let paramsall = queryParams.join('&');

        let param = { status: `mr/letter/list?${paramsall}` };

        useIPA.get(param).then(response => {
            const letter = {
                rows: response.data.data
            };
            commit('setData', { ...letter });
        });
    },

    
    getByIdDeath({ commit }, id) {
        return new Promise((resolve, reject) => {
            let param = { status: 'mr/letter/death/' + id }
            useIPA.get(param).then(response => {
                resolve(response.data.data)
            })
        })
    },
    geLetterBirth({ commit }, id) {
        return new Promise((resolve, reject) => {
            let param = { status: 'mr/letter/birth/' + id }
            useIPA.get(param).then(response => {
                resolve(response.data.data)
            })
        })
    },

    getAllNew({ commit }, params) {
        return new Promise((resolve, reject) => {
            let queryParams = [];
    
            if (params.offset) {
                queryParams.push(`offset=${params.offset}`);
            } else if (params.offset === 0) {
                queryParams.push(`offset=${params.offset}`);
            }
    
            if (params.limit) {
                queryParams.push(`limit=${params.limit}`);
            }
    
            if (params.no) {
                queryParams.push(`no=${params.no}`);
            }
    
            if (params.registration_destination_id) {
                queryParams.push(`registration_destination_id=${params.registration_destination_id}`);
            }
    
            if (params.norm) {
                queryParams.push(`norm=${params.norm}`);
            }
    
            if (params.letter_type_id) {
                queryParams.push(`letter_type_id=${params.letter_type_id}`);
            }
    
            let paramsall = queryParams.join('&');
    
            let param = { status: `mr/letter/list?${paramsall}` };
    
            useIPA.get(param).then(response => {
                resolve(response.data.data)
            });
        })
    },

    getById({ commit }, id) {
        return new Promise((resolve, reject) => {
            let param = { status: 'mr/letter/detail/' + id }
            useIPA.get(param).then(response => {
                resolve(response.data.data)
            })
        })
    },

    putSignature ({ commit }, formData) {
        return new Promise((resolve, reject) => {
        let url = `mr/letter/signature_update/${formData.id}`
        const param = { rawData: formData, url: url }
        // useIPA.postput(param)
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
    
    formPostPut({ commit }, formData) {
        return new Promise((resolve, reject) => {
            let url = '/mr/letter/create'
            let param = { rawData: formData, url: url, id: "" }
            if (formData.id) {
                url = '/mr/letter/update'
                param = { rawData: formData, url: url, id: formData.id }
            }
            // console.log('param', param)
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
            const param = { rawData: rawData, url: 'mr/letter/delete', id: id }
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
    setParamLetter(state, data) {
        state.options.letter = []
        for (let i = 0; i < data.rows.rows.length; i++) {
            const option = []
            for (const key in data.rows.rows[i]) {
                if (key === 'id') { option['value'] = data.rows.rows[i][key] } else if (key === 'name') { option['title'] = data.rows.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows.rows[i][key] }
            }
            state.options.letter.push(Object.assign({}, option))
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
