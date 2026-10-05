import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
    start: 20,
    currentPage: 1,
    totalRows: 1,
    fields: [
        { key: 'employee_name', label: 'Karyawan', sortable: true },
        { key: 'username', label: 'Username', sortable: true },
        { key: 'is_delete', label: 'Status', sortable: true },
        { key: 'action', label: 'Aksi' },
    ],
    items: [],
    pageOptions: [20, 50, 100],
    sortOptions: [],
    options: {
        user: []
    }
})

// getters
const getters = {}

// actions
const actions = {
    getParam({ commit }, params) {
        return new Promise((resolve, reject) => {
            let param = {
                status: `setting/user/list`
            }
            useIPA.get(param).then((response) => {
                const Param = {
                    rows: response.data.data
                }
                if (params === 'user') {
                    commit('setParamUser', { ...Param })
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

        if (params.loket) {
            queryParams.push(`loket=${params.loket}`);
        }

        if (params.url) {
            queryParams.push(`url=${params.url}`);
        }

        let paramsall = queryParams.join('&');

        let param = { status: `queue_loket/list?${paramsall}` };

        useIPA.get(param).then(response => {
            const user = {
                rows: response.data.data
            };
            commit('setData', { ...user });
        });
    },
    getById({ commit }, id) {
        return new Promise((resolve, reject) => {
            let param = { status: 'setting/user/detail/' + id }
            useIPA.get(param).then(response => {
                resolve(response.data.data)
            })
        })
    },
    getDisplay({ commit }) {
        return new Promise((resolve, reject) => {
            let param = { status: 'queue_loket/display' }
            useIPA.get(param).then(response => {
                resolve(response.data.data)
            })
        })
    },
    getAds({ commit }) {
        return new Promise((resolve, reject) => {
            let param = { status: 'queue_loket/ads' }
            useIPA.get(param).then(response => {
                resolve(response.data.data)
            })
        })
    },
    getDisplayLab({ commit }, date) {
        return new Promise((resolve, reject) => {
            let queryParams = [];

            if (date) {
                queryParams.push(`date=${date}`);
            }
        
            let paramsall = queryParams.join('&')
            let param = {status: `health_facility/laboratory_request/queue?${paramsall}`}

            // let param = { status: 'health_facility/laboratory_request/queue?date=2025-01-01' }
            useIPA.get(param).then(response => {
                resolve(response.data.data)
            })
        })
    },

    postPutQueueNextLab ({ commit }, formData) {
        return new Promise((resolve, reject) => {
            const rawData = formData

            const param = { rawData: rawData, url: `health_facility/laboratory_request/queue_next`, id: formData.queue }
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
            .catch(({error, response}) => {
                reject({error: response.data.message, response})
            })
		})
	},

    postPutQueueNextPrs ({ commit }, formData) {
        return new Promise((resolve, reject) => {
            const rawData = formData

            const param = { rawData: rawData, url: `pharmacy/prescription/queue_next`, id: formData.queue }
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
            .catch(({error, response}) => {
                reject({error: response.data.message, response})
            })
		})
	},


    getDisplayRad({ commit }, date) {
        return new Promise((resolve, reject) => {
            let queryParams = [];

            if (date) {
                queryParams.push(`date=${date}`);
            }
        
            let paramsall = queryParams.join('&')
            let param = {status: `health_facility/radiology_request/queue?${paramsall}`}

            useIPA.get(param).then(response => {
                resolve(response.data.data)
            })
        })
    },

    postPutQueueNextRad ({ commit }, formData) {
        return new Promise((resolve, reject) => {
            const rawData = formData

            const param = { rawData: rawData, url: `health_facility/radiology_request/queue_next`, id: formData.queue }
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
            .catch(({error, response}) => {
                reject({error: response.data.message, response})
            })
		})
	},


    getDisplayPresc({ commit }, date) {
        return new Promise((resolve, reject) => {
            let queryParams = [];

            if (date) {
                queryParams.push(`date=${date}`);
            }
        
            let paramsall = queryParams.join('&')
            let param = {status: `pharmacy/prescription/queue?${paramsall}`}

            useIPA.get(param).then(response => {
                resolve(response.data.data)
            })
        })
    },
    getDisplayById({ commit },id) {
        return new Promise((resolve, reject) => {
            let param = { status: 'queue_loket/display/'+id }
            useIPA.get(param).then(response => {
                resolve(response.data.data)
            })
        })
    },
    getDisplayPoli({ commit }, id) {
        return new Promise((resolve, reject) => {
            let param = { status: `queue_loket/display_poli?display_id=${id}` }
            useIPA.get(param).then(response => {
                resolve(response.data.data)
            })
        })
    },
    getDisplayPoliById({ commit },params) {
        return new Promise((resolve, reject) => {
            let param = { status: `queue_loket/display_poli?display_id=${params.display_id}&id=${params.id}` }
            useIPA.get(param).then(response => {
                resolve(response.data.data)
            })
        })
    },
    formPostCheckin({ commit }, formData) {
        return new Promise((resolve, reject) => {
            let url = 'queue_loket/checkin'
            const param = { rawData: formData, url: url, id: formData.id }
            useIPA.postputnoid(param)
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
    formPostPutAction({ commit }, formData) {
        return new Promise((resolve, reject) => {
            let url = 'queue_loket/action'
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
    callLab({ commit }, formData) {
        return new Promise((resolve, reject) => {
            let url = 'health_facility/laboratory_request/queue_next'
            const param = { rawData: formData, url: url, id: formData.queue }
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
    formPostPutActionPoli({ commit }, formData) {
        return new Promise((resolve, reject) => {
            let url = 'queue_loket/action_poli'
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
    formPostPut({ commit }, formData) {
        return new Promise((resolve, reject) => {
            const rawData = {
                group_id: formData.group_id,
                employee_id: formData.employee_id,
                password: formData.password,
                username: formData.username,
            }
            let url = 'setting/user/create'
            if (formData.id) {
                url = 'setting/user/update'
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
            const param = { rawData: rawData, url: 'setting/user/delete', id: id }
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
    setData(state, user) {
        console.log(user)
        state.items = user.rows.rows
        state.totalRows = user.rows.iTotal
        state.start = user.rows.start
    },
    setParamUser(state, data) {
        console.log(data)
        state.options.user = []
        for (let i = 0; i < data.rows.rows.length; i++) {
            const option = []
            for (const key in data.rows.rows[i]) {
                if (key === 'id') { option['value'] = data.rows.rows[i][key] } else if (key === 'name') { option['title'] = data.rows.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows.rows[i][key] }
            }
            state.options.user.push(Object.assign({}, option))
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
