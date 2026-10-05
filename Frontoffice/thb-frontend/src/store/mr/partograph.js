import useIPA from '@/auth/jwt/useJwt'
import axios from 'axios'


const state = {
//   lineChartData: null,
//   loading: false,
//   error: null
}

const mutations = {
//   SET_LINE_CHART_DATA(state, data) {
//     state.lineChartData = data
//   },
//   SET_LOADING(state, status) {
//     state.loading = status
//   },
//   SET_ERROR(state, error) {
//     state.error = error
//   }
}

const actions = {
    createPartograph({ commit }, partograph) {
        return new Promise((resolve, reject) => {
            const rawData = partograph
            let url = 'mr/partograph/create'
            const param = { rawData: rawData, url: url }
            // console.log(param)
            useIPA.postputnoid(param)
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

    updatePartograph({ commit }, partograph) {
        return new Promise((resolve, reject) => {
            const rawData = partograph
            let url = `mr/partograph/update/${partograph.id}`
            const param = { rawData: rawData, url: url, id: rawData.partograph_id }
            useIPA.postput(param)
            .then(response => {
                    if (response.data.hasErrors) {
                        reject(response.data.message)
                    } else {
                    resolve(response.data)
                    }
                    // console.log('updatePartograph response', response)
                    return response;
                })
            .catch(({response}) => {
                reject({message: response.data.message, debug: response.data.debug, response})
            })
        })
    },  

    updatePartographItem({ commit }, item) {
        return new Promise((resolve, reject) => {
            const rawData = item
            let url = 'mr/partograph/item'
            const param = { rawData: rawData, url: url, id: rawData.partograph_id }
            console.log('updatePartographItem', param)
            useIPA.postput(param)
            .then(response => {
                    if (response.data.hasErrors) {
                    reject(response.data.message)
                    } else {
                    resolve(response.data)
                    }
                    // console.log('updatePartographItem response', response)
                    return response;
                })
            .catch(({response}) => {
                reject({message: response.data.message, debug: response.data.debug, response: response.data,})
            })
        })
    },

    deletePartographItem({ commit }, payload) {
        return new Promise((resolve, reject) => {
            const rawData = payload
            let url = 'mr/partograph/item_delete'
            const param = { rawData: rawData, url: url, id: rawData.partograph_id }
            console.log('deletePartographItem', param)
            useIPA.delete(param)
                .then(response => {
                    if (response.data.hasErrors) {
                        reject(response.data.message)
                    } else {
                        resolve(response.data)
                    }
                    return response
                })
                .catch(({ response }) => {
                    reject({ message: response?.data?.message, debug: response?.data?.debug, response })
                })
        })
    },



    hitThis(){
        useIPA.get(`/mr/partograph/get_hours/aa2addeb-7435-40d5-a1fe-a72f92d15de8?addition_time=30&max_time=2025-05-23 08:02:00`)   
    },

    fetchHours({ commit }, params) {
        return new Promise((resolve, reject) => {
            // console.log('im here')
            let queryParams = [];
            if (params.addition_time) {
                queryParams.push(`addition_time=${params.addition_time}`)
            }
            if (params.max_time) {
                queryParams.push(`max_time=${params.max_time}`)
            }
            let paramsTime = queryParams.join('&')
            let paramsAll = `${params.partographId}?${paramsTime}`
            let finalString = {status: `mr/partograph/get_hours/${paramsAll}`}

            // console.log('finalString', finalString)

            useIPA.get(finalString).then(response => {
                resolve(response.data)
            }).catch(error => reject(error))
        })
    },

    getPartographDetail({ commit }, params) {
        return new Promise((resolve, reject) => {
            let url = 'mr/partograph/detail';
            let finalString;

            if (params.partographId) {
                finalString = { status: `${url}/${params.partographId}` };
            } else if (params.registration_destination_id) {
                finalString = { status: `${url}?registration_destination_id=${params.registration_destination_id}` };
            } else {
                reject('No valid identifier provided');
                return;
            }

            // console.log('finalString', finalString);

            useIPA.get(finalString)
            .then(response => {
                    if (response.data.hasErrors) {
                    reject(response.data.message)
                    } else {
                        // console.log('getPartographDetail response', response)
                        resolve(response.data)
                    }
                    return response;
                })
            .catch(({response}) => {
                reject({message: response.data.message, debug: response.data.debug, response: response.data,})
            })

        })
    },

}

const getters = {
//   lineChartData: state => state.lineChartData,
//   loading: state => state.loading,
//   error: state => state.error
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}