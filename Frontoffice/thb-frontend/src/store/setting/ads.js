import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'
import axios from 'axios';
// initial state
const state = () => ({
    start: 20,
    currentPage: 1,
    totalRows: 1,
    fields: [
        { key: 'name', label: 'Nama', sortable: true },
        { key: 'file', label: 'File', sortable: true },
        { key: 'is_delete', label: 'Status', sortable: true },
        { key: 'action', label: 'Aksi' },
    ],
    items: [],
    pageOptions: [20, 50, 100],
    sortOptions: [],
    options: {
        ads: []
    }
})

// getters
const getters = {}


// actions
const actions = {
    getParam({ commit }, params) {
        return new Promise((resolve, reject) => {
            let param = {
                status: `setting/ads/list`
            }
            useIPA.get(param).then((response) => {
                const Param = {
                    rows: response.data.data
                }
                if (params === 'ads') {
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

        let param = { status: `setting/ads/list?${paramsall}` };

        useIPA.get(param).then(response => {
            const ads = {
                rows: response.data.data
            };
            commit('setData', { ...ads });
        });
    },

    getById({ commit }, id) {
        return new Promise((resolve, reject) => {
            let param = { status: 'setting/ads/detail/' + id }
            useIPA.get(param).then(response => {
                resolve(response.data.data)
            })
        })
    },
    handleFileChange(event){
  // Capture the selected file(s)
  selectedFile.value = event.target.files ? event.target.files[0] : null;
  console.log(selectedFile.value.name);
        
    },
    formPostPut({ commit }, formData) {
        return new Promise((resolve, reject) => {
            // const datanew = new FormData();
            // datanew.append('file', formData.file);
            // datanew.append('name', formData.name);
            // datanew.append('ads_id', formData.ads_id);
            
            const rawData = {
                name: formData.name,
                file: formData.result,
                extension: formData.extension,
                ads_id: formData.ads_id
            }
            let url = 'setting/ads/create'
            if (formData.id) {
                url = 'setting/ads/update'
            }
            const param = { rawData, url: url, id: formData.id }
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
            const param = { rawData: rawData, url: 'setting/ads/delete', id: id }
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
    setData(state, ads) {
        state.items = ads.rows.rows
        state.totalRows = ads.rows.iTotal
        state.start = ads.rows.start
    },
    // setParamLetter(state, data) {
    //     state.options.ads = []
    //     for (let i = 0; i < data.rows.rows.length; i++) {
    //         const option = []
    //         for (const key in data.rows.rows[i]) {
    //             if (key === 'id') { option['value'] = data.rows.rows[i][key] } else if (key === 'name') { option['title'] = data.rows.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows.rows[i][key] }
    //         }
    //         state.options.ads.push(Object.assign({}, option))
    //     }
    // }
    setParamLetter(state, data) {
        state.options.ads = []
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
            state.options.ads.push(Object.assign(option))
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
