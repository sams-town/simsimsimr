import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state
const state = () => ({
    start: 20,
    perPage: 20,
    pageOptions: [20, 50, 100],
    totalRows: 1,
    firstRow: 1,
    lastRow: 1,
    sortOptions: [],
    fields: [
        { key: 'doctor_name', label: 'Nama Dokter' },
        { key: 'health_facility_item_name', label: 'Poli' },
        { key: 'schedule_item', label: 'Jadwal Praktek' },
        { key: 'action', label: 'Aksi', thStyle: { width: '130px', textAlign: 'center' }, },
    ],
    items: [],
    quota_bo_fields: [
        { key: 'queue', label: 'Nomor Urut', thStyle: { width: '120px', textAlign: 'center' }, },
        { key: 'time', label: 'Jam' },
        { key: 'patient_name', label: 'Nama Pasien' },
        { key: 'reserve', label: 'Aksi', thStyle: { width: '140px', textAlign: 'center' } },
    ],
    quotaBOItems: [],
    options: {
        schedule_item: [],
        quota_bo_item: []
    }
})

// getters
const getters = {}

// actions
const actions = {
    getParam({ commit }) {
        return new Promise((resolve, reject) => {
            let param = {
                status: `medical_support/doctor_schedule_item/detail`
            }
            useIPA.get(param).then((response) => {
                const Param = {
                    rows: response.data.data
                }
                commit('setParamScheduleItem', { ...Param })
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

        if (params.doctor_id) {
            queryParams.push(`doctor_id=${params.doctor_id}`);
        }

        if (params.health_facility_item_id) {
            queryParams.push(`health_facility_item_id=${params.health_facility_item_id}`);
        }

        if (params.day) {
            queryParams.push(`day=${params.day}`);
        }

        let paramsall = queryParams.join('&');

        let param = { status: `medical_support/doctor_schedule_item/list?${paramsall}` };
        useIPA.get(param).then(response => {
            const schedule_item = {
                rows: response.data.data
            }
            commit('setListData', { ...schedule_item })
        })
    },
    clear({ commit }) {
        const schedule_item = {
            rows: ''
        }
        commit('setListData', { ...schedule_item })
    },
    getDetail({ commit }) {
        let param = { status: 'medical_support/doctor_schedule_item/detail' }
        useIPA.get(param).then(response => {
            const schedule_item = {
                rows: response.data.data
            }
            commit('setData', { ...schedule_item })
        })
    },
    getDetailQuotaBO({ commit }, data) {
        let param = { status: 'medical_support/doctor_schedule_item/quota_bo/' + data.doctor_schedule_item_id + '?date_appointment=' + data.booking_date }
        useIPA.get(param).then(response => {
            const quota_bo_item = {
                rows: response.data.data
            }
            // console.log('quota_bo_item:', quota_bo_item);
            commit('setBOData', { ...quota_bo_item })
        })
    },
    getById({ commit }, id) {
        return new Promise((resolve, reject) => {
            let param = { status: 'medical_support/doctor_schedule_item/detail/' + id }
            useIPA.get(param).then(response => {
                resolve(response.data.data)
            })
        })
    },
    getByDoctorId({ commit }, doctor_id) {
        return new Promise((resolve, reject) => {
            let param = { status: 'medical_support/doctor_schedule_item/detail/?doctor_id=' + doctor_id }
            useIPA.get(param).then(response => {
                resolve(response.data.data)
            })
        })
    },
    formPostPut({ commit }, formData) {
        return new Promise((resolve, reject) => {
            const rawData = {
                code: formData.code,
                day: formData.day,
                hour_start: formData.hour_start,
                hour_end: formData.hour_end,
                doctor_schedule_id: formData.doctor_schedule_id,
                quota: formData.quota,
                quota_bo: formData.quota_bo,
                quota_jkn: formData.quota_jkn,
            }

            let url = ''
            if (formData.id) {
                url = 'medical_support/doctor_schedule_item/update/' + formData.id
            }
            const param = { rawData: rawData, url: url, id: formData.id }
            useIPA.postput(param)
                .then(
                    response => {
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
    formPostPutHFIS({ commit }, formData) {
        return new Promise((resolve, reject) => {
            const rawData = {
                code: formData.code,
                day: formData.day,
                hour_start: formData.hour_start,
                hour_end: formData.hour_end,
                quota: formData.quota,
                quota_bo: formData.quota_bo,
                quota_jkn: formData.quota_jkn,
            }

            let url = ''
            if (formData.id) {
                url = 'medical_support/doctor_schedule_item/update_hfis/' + formData.id
            }
            const param = { rawData: rawData, url: url, id: formData.id }
            useIPA.postput(param)
                .then(
                    response => {
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
            const param = { rawData: rawData, url: 'medical_support/doctor_schedule_item/delete', id: id }
            useIPA.delete(param)
                .then(
                    response => {
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
                    }
                )
        })
    }
}

// mutations
const mutations = {
    setData(state, schedule_item) {
        state.items = schedule_item.rows
        state.totalRows = schedule_item.rows.length
    },
    setListData(state, schedule_item) {
        state.items = schedule_item.rows.rows
        state.totalRows = schedule_item.rows.iTotal
        state.start = schedule_item.rows.start
    },
    setBOData(state, schedule_item) {
        state.quotaBOItems = []
        state.quotaBOItems = schedule_item
        console.log('quota_bo_item:', schedule_item);

        // state.totalRows = schedule_item.rows.length
    },
    setParamScheduleItem(state, data) {
        state.options.schedule_item = []
        for (let i = 0; i < data.rows.length; i++) {
            const option = []
            for (const key in data.rows[i]) {
                if (key === 'id') {
                    option['value'] = data.rows[i][key]
                } else if (key === 'hour_start') {
                    option['hour_start'] = data.rows[i][key]
                } else if (key === 'hour_end') {
                    option['hour_end'] = data.rows[i][key]
                } else if (key === 'id') {
                    option['oid'] = data.rows[i][key]
                }
            }
            state.options.schedule_item.push(Object.assign({}, option))
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
