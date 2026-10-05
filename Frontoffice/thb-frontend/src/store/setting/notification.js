import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
})

// getters
const getters = {}

// actions
const actions = {
    getNotificationAll({ commit }, params) {
        return new Promise((resolve, reject) => {
            let queryParams = [];

            if (params.offset != undefined) {
                queryParams.push(`offset=${params.offset}`);
            }
            
            if (params.status) {
                queryParams.push(`status=${params.status}`);
            }

            if (params.employee_id) {
                queryParams.push(`employee_id=${params.employee_id}`);
            }

            let paramsall = queryParams.join('&');

            let param = { status: `setting/notifications/get?${paramsall}` };
            useIPA.get(param)
            .then(response => {
                resolve(response.data.data)
                return response;
            })
            .catch(({response}) => {
                reject({message: response.data.message, debug: response.data.debug, response: response.data,})
            })
        })
    },
    readNotificationID({ commit }, notif_id){
        return new Promise((resolve, reject) => {
            const rawData = {
                always_copy_nurse: 1, 
                anamnesis:1
            }
            let url = 'setting/notifications/read'
            const param = { rawData: rawData, url: url, id: notif_id }
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
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
