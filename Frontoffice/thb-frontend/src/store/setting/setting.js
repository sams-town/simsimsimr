import useIPA from '@/auth/jwt/useJwt'

const state = () => ({
    start: 0,
    currentPage: 1,
    totalRows: 1,
    fields: [
        { key: 'provider', label: 'Provider', sortable: true },
        { key: 'type', label: 'Type', sortable: true },
        { key: 'base_url', label: 'Base URL', sortable: false },
        { key: 'is_active', label: 'Status', sortable: true },
        { key: 'action', label: 'Aksi' },
    ],
    items: [],
    pageOptions: [20, 50, 100],
})

const getters = {}

const actions = {
    getAll({ commit }, { params, cancelToken }) {
        return new Promise((resolve, reject) => {
        let queryParams = []

        if (params.offset === 0 || params.offset) {
            queryParams.push(`offset=${params.offset}`)
        }
        if (params.limit) {
            queryParams.push(`limit=${params.limit}`)
        }
        if (params.provider) {
            queryParams.push(`provider=${params.provider}`)
        }
        if (params.type) {
            queryParams.push(`type=${params.type}`)
        }


        let paramsall = queryParams.join('&');
        let param = { status: `setting/api_credentials/list?${paramsall}` }
    
        useIPA.get(param, { cancelToken })
            .then(response => {
            resolve(response.data.data);
            })
            .catch(error => {
            if (axios.isCancel(error)) {
                console.log("Request dibatalkan:", error.message);
            } else {
                reject(error);
            }
            });
        });
    },

    getById({ commit }, id) {
        return new Promise((resolve, reject) => {
            let param = { status: 'setting/api_credentials/detail/' + id }
            useIPA.get(param).then(response => {
                resolve(response.data.data)
            }, error => {
                reject(error)
            })
        })
    },

    postPutCredential({ commit }, formData) {
        return new Promise((resolve, reject) => {
            const rawData = {
                provider: formData.provider,
                type: formData.type,
                auth_url: formData.auth_url,
                base_url: formData.base_url,
                client_id: formData.client_id,
                client_secret: formData.client_secret,
                organization_id: formData.organization_id,
                location_id: formData.location_id,
                cons_id: formData.cons_id,
                ppkrs: formData.ppkrs,
                user_key: formData.user_key,
                is_active: formData.is_active ? 1 : 0,
            }

            let url = 'setting/api_credentials/create'
            if (formData.id) {
                url = 'setting/api_credentials/update'
            }

            const param = { rawData: rawData, url: url, id: formData.id }
            useIPA.postput(param).then(
                response => {
                    if (response.data.hasErrors) {
                        reject(response.data.message)
                    } else {
                        resolve(response.data)
                    }
                },
                error => {
                    if (error.response && error.response.data && error.response.data.hasOwnProperty('hasErrors')) {
                        reject(error.response.data.message)
                    } else {
                        reject(error.message)
                    }
                }
            )
        })
    },
    updateStatus({ commit }, credential) {
        return new Promise((resolve, reject) => {
            const rawData = {
                is_active: credential.is_active == 1 ? 0 : 1, // toggle: aktif jadi nonaktif, atau sebaliknya
            }
            const param = { rawData: rawData, url: 'setting/api_credentials/status', id: credential.id }
            useIPA.postput(param).then(
                response => {
                    if (response.data.hasErrors) {
                        reject(response.data.message)
                    } else {
                        resolve(response.data)
                    }
                },
                error => {
                    if (error.response && error.response.data && error.response.data.hasOwnProperty('hasErrors')) {
                        reject(error.response.data.message)
                    } else {
                        reject(error.message)
                    }
                }
            )
        })
    },
    toggleActive({ commit }, credential) {
        return new Promise((resolve, reject) => {
            const rawData = {
                is_active: credential.is_active ? 0 : 1,
            }
            const param = { rawData: rawData, url: 'setting/api_credentials/delete', id: credential.id }
            useIPA.delete(param).then(
                response => {
                    if (response.data.hasErrors) {
                        reject(response.data.message)
                    } else {
                        resolve(response.data)
                    }
                },
                error => {
                    if (error.response && error.response.data && error.response.data.hasOwnProperty('hasErrors')) {
                        reject(error.response.data.message)
                    } else {
                        reject(error.message)
                    }
                }
            )
        })
    },
}

const mutations = {
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
}