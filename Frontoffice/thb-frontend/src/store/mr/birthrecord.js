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

function saveHelper(url, payload) {
    return new Promise((resolve, reject) => {
        const rawData = payload;
        const param = { rawData, url };
        useIPA.putonly(param)
            .then(response => {
                if (response.data.hasErrors) {
                    reject(response.data.message);
                } else {
                    resolve(response.data);
                }
                return response;
            })
            .catch(({ response }) => {
                reject({ message: response.data.message, debug: response.data.debug, response });
            });
    });
}

const actions = {
    createBirthRecord({ commit }, birthNotes) {
        return new Promise((resolve, reject) => {
            const rawData = birthNotes
            let url = 'mr/birth_record/save_note'
            const param = { rawData: rawData, url: url }
            console.log("createBirthRecord param: ", param)
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
                reject({message: response.data.message, debug: response.data.debug, response})
            })

        })
    },

     getBirthRecordDetail({ commit }, params) {
        return new Promise((resolve, reject) => {
            let url = 'mr/birth_record/detail';
            let finalString;

            if (params.birth_record_id) {
                finalString = { status: `${url}/${params.birth_record_id}` };
            } else if (params.partograph_id) {
                finalString = { status: `${url}?partograph_id=${params.partograph_id}` };
            } else {
                reject('No valid identifier provided');
                return;
            }
            console.log('finalString', finalString);
            useIPA.get(finalString)
            .then(response => {
                    if (response.data.hasErrors) {
                    reject(response.data.message)
                    } else {
                        resolve(response.data)
                    }
                    return response;
                })
            .catch(({response}) => {
                console.log('Caught error:', error);
                reject({message: response.data.message, debug: response.data.debug, response})
            })

        })
    },

    updateBirthRecord({ commit }, payload) {
        return new Promise((resolve, reject) => {
            const rawData = payload
            let url = `mr/birth_record/save_note`
            const param = { rawData: rawData, url: url }
            console.log('updateBirthRecord param', param)
            useIPA.postput(param)
            .then(response => {
                    if (response.data.hasErrors) {
                        reject(response.data.message)
                    } else {
                    resolve(response.data)
                    }
                    console.log('updateBirthRecord response', response)
                    return response;
                })
            .catch(({response}) => {
                reject({message: response.data.message, debug: response.data.debug, response})
            })
        })
    },  

    createBirthRecordKala({ commit }, birthRecordKala) {
        return new Promise((resolve, reject) => {
            const rawData = birthRecordKala
            let url = 'mr/birth_record_kala/create'
            const param = { rawData: rawData, url: url }
            console.log("createBirthRecordKala param: ", param)
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
                reject({message: response.data.message, debug: response.data.debug, response})
            })

        })
    },

    getBirthRecordKala({ commit }, params) {
        return new Promise((resolve, reject) => {
            let url = 'mr/birth_record_kala/detail';
            let finalString;

            if (params.birth_record_id) {
                // finalString = { status: `${url}/${params.birth_record_id}` };
                finalString = { status: `${url}?birth_record_id=${params.birth_record_id}` };
            } else {
                reject('No valid identifier provided');
                return;
            }
            // console.log('getBirthRecordKala finalString', finalString);
            useIPA.get(finalString)
            .then(response => {
                    if (response.data.hasErrors) {
                    reject(response.data.message)
                    } else {
                        resolve(response.data)
                    }
                    return response;
                })
            .catch(({response}) => {
                console.log('Caught error:', error);
                reject({message: response.data.message, debug: response.data.debug, response})
            })

        })
    },

    saveBirthRecordKalaOne({ commit }, payload) {
        return saveHelper('mr/birth_record_kala/save_kala_one', payload);
    },

    saveBirthRecordKalaTwo({ commit }, payload) {
        return saveHelper('mr/birth_record_kala/save_kala_two', payload);
    },

    saveBirthRecordKalaThree({ commit }, payload) {
        return saveHelper('mr/birth_record_kala/save_kala_three', payload);
    },

    saveBirthRecordKalaFour({ commit }, payload) {
        return saveHelper('mr/birth_record_kala/save_kala_four', payload);
    },

    saveNewbornBaby({ commit }, payload) {
        return saveHelper('mr/birth_record/save_newborn_baby', payload);
    },

    savePemantauanPersalinan({ commit }, payload) {
        return saveHelper('mr/birth_record_kala/save_monitoring_kala_four', payload);
    }


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