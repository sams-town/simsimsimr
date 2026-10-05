import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

const state = () => ({
    dashboardData: [],
});

const actions = {
    getDashboardByDate({ commit }, params) {
        return new Promise((resolve, reject) => {
            console.log(params)
            let queryParams = [];

            if (params) {
                if (params.date_start && params.date_end) {
                    queryParams.push(`date_start=${params.date_start}`)
                    queryParams.push(`date_end=${params.date_end}`)
                }
            }

            let paramsall = queryParams.join('&')
            let param = { status: `/dashboard/detail?${paramsall}` }

            useIPA.get(param)
                .then((response) => {
                    if (response.data) {
                        commit('setData', response.data.data);
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
    getDashboardCheckoutMonitoring({ commit }, params) {
        return new Promise((resolve, reject) => {
            console.log(params)
            let queryParams = [];

            if (params) {
                if (params.date_start) {
                    queryParams.push(`date_start=${params.date_start}`)
                }
                if (params.date) {
                    queryParams.push(`date=${params.date}`)
                }
                if (params.exit_id) {
                    queryParams.push(`exit_id=${params.exit_id}`)
                }
            }

            let paramsall = queryParams.join('&')
            let param = { status: `/dashboard/get_checkout_monitoring?${paramsall}` }

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
    getDashboardBalancing({ commit }, params) {
        return new Promise((resolve, reject) => {
            console.log(params)
            let queryParams = [];

            if (params) {
                if (params.date_start) {
                    queryParams.push(`date_start=${params.date_start}`)
                }
                if (params.date) {
                    queryParams.push(`date=${params.date}`)
                }
            }

            let paramsall = queryParams.join('&')
            let param = { status: `/dashboard/get_balancing?${paramsall}` }

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
    getMargin({ commit }, params) {
        return new Promise((resolve, reject) => {
            console.log(params)
            let queryParams = [];

            if (params) {
                if (params.date_start) {
                    queryParams.push(`date_start=${params.date_start}`)
                }
                if (params.date_end) {
                    queryParams.push(`date_end=${params.date_end}`)
                }
                if (params.health_facility_item_id) {
                    queryParams.push(`health_facility_item_id=${params.health_facility_item_id}`)
                }
            }

            let paramsall = queryParams.join('&')
            let param = { status: `/dashboard/check_margin?${paramsall}` }

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
    getMarginPrescription({ commit }, params) {
        return new Promise((resolve, reject) => {
            console.log(params)
            let queryParams = [];

            if (params) {
                queryParams.push(`registration_id=${params}`)
            }

            let paramsall = queryParams.join('&')
            let param = { status: `/dashboard/check_margin_prescription?${paramsall}` }

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
    getMarginLaboratory({ commit }, params) {
        return new Promise((resolve, reject) => {
            console.log(params)
            let queryParams = [];

            if (params) {
                queryParams.push(`registration_id=${params}`)
            }

            let paramsall = queryParams.join('&')
            let param = { status: `/dashboard/check_margin_laboratory?${paramsall}` }

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
    getMarginRadiology({ commit }, params) {
        return new Promise((resolve, reject) => {
            console.log(params)
            let queryParams = [];

            if (params) {
                queryParams.push(`registration_id=${params}`)
            }

            let paramsall = queryParams.join('&')
            let param = { status: `/dashboard/check_margin_radiology?${paramsall}` }

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
    getDiagnosa({ commit }, params) {
        return new Promise((resolve, reject) => {
            console.log(params)
            let queryParams = [];

            if (params) {
                queryParams.push(`registration_id=${params}`)
            }

            let paramsall = queryParams.join('&')
            let param = { status: `/dashboard/diagnosa?${paramsall}` }

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
    savediagnosa ({ commit }, formData) {
    return new Promise((resolve, reject) => {
    let url = 'dashboard/save_diagnosa'
    const param = { rawData: formData, url: url, id: 1 }
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
};

const mutations = {
    setData(state, dashboard) {
        state.dashboardData = dashboard;
    },
};

export default {
    namespaced: true,
    state,
    actions,
    mutations
}