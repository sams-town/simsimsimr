import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

const state = () => ({});

const actions = {
    getDashboardEmergencyVisits({ commit }, params) {
        return new Promise((resolve, reject) => {
            console.log(params)
            let queryParams = [];

            if (params) {
                queryParams.push(`period=${params.period}`)

                if (params.date_start && params.date_end) {
                    queryParams.push(`date_start=${params.date_start}`)
                    queryParams.push(`date_end=${params.date_end}`)
                }
            }

            let paramsall = queryParams.join('&')
            let param = { status: `/dashboard/executive/emergency_visits?${paramsall}` }

            useIPA.get(param)
                .then((response) => {
                    if (response.data) {
                        resolve(response.data);
                    } else {
                        reject('Data tidak valid');
                    }
                })
                .catch((error) => {
                    reject(error)
                })
        });
    },
    getDashboardOutpatientVisits({ commit }, params) {
        return new Promise((resolve, reject) => {
            console.log(params)
            let queryParams = [];

            if (params) {
                queryParams.push(`period=${params.period}`)

                if (params.date_start && params.date_end) {
                    queryParams.push(`date_start=${params.date_start}`)
                    queryParams.push(`date_end=${params.date_end}`)
                }
            }

            let paramsall = queryParams.join('&')
            let param = { status: `/dashboard/executive/outpatient_visits?${paramsall}` }

            useIPA.get(param)
                .then((response) => {
                    if (response.data) {
                        resolve(response.data);
                    } else {
                        reject('Data tidak valid');
                    }
                })
                .catch((error) => {
                    reject(error)
                })
        });
    },
    getDashboardOutpatientIntoInpatient({ commit }, params) {
        return new Promise((resolve, reject) => {
            console.log(params)
            let queryParams = [];

            if (params) {
                queryParams.push(`period=${params.period}`)

                if (params.date_start && params.date_end) {
                    queryParams.push(`date_start=${params.date_start}`)
                    queryParams.push(`date_end=${params.date_end}`)
                }
            }

            let paramsall = queryParams.join('&')
            let param = { status: `/dashboard/executive/outpatient_into_inpatient?${paramsall}` }

            useIPA.get(param)
                .then((response) => {
                    if (response.data) {
                        resolve(response.data);
                    } else {
                        reject('Data tidak valid');
                    }
                })
                .catch((error) => {
                    reject(error)
                })
        });
    },
};

const mutations = {};

export default {
    namespaced: true,
    state,
    actions,
    mutations
}