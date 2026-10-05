import useIPA from '@/auth/jwt/useJwt'
import router from '@/router/index.js'

// initial state: used
const state = () => ({
    items: [],
    options: {
        serviceactivity: []
    }
});

// getters: no
const getters = {};

// actions: used
// const actions = {
//     getByDate({ commit }, params) {
//         return new Promise((resolve, reject) => {
//             let param = {
//                 // status: `/third_party/company/list=${params}`
//                 status: `report/service_activity/detail?date=${params}`
//             }

//             // console.log(param)
//             useIPA.get(param).then((response) => {
//                 resolve(response.data.data)
//                 const Param = {
//                     rows: response.data.data
//                 }
//                 commit('setParamServiceActivity', { ...Param })
//             })
//         })
//     },
// };

// // mutations: used
// const mutations = {
//     setData(state, serviceactivity) {
//         state.items = serviceactivity.rows.rows
//         state.totalRows = serviceactivity.rows.iTotal
//         state.start = serviceactivity.rows.start
//     },
//     setParamServiceActivity(state, data) {
//         state.options.serviceactivity = []
//         for (let i = 0; i < data.rows.rows.length; i++) {
//             const option = []
//             for (const key in data.rows.rows[i]) {
//                 if (key === 'id') { option['value'] = data.rows.rows[i][key] } else if (key === 'name') { option['title'] = data.rows.rows[i][key] } else if (key === 'id') { option['oid'] = data.rows.rows[i][key] }
//             }
//             state.options.serviceactivity.push(Object.assign({}, option))
//         }
//     }
// };

const actions = {
    getByDate({ commit }, params) {
        return new Promise((resolve, reject) => {
            let param = {
                status: `report/service_activity/detail?date_start=2023-01-01&date_end=2024-01-01`
            };

            useIPA.get(param)
                .then((response) => {
                    if (response.data) {
                        commit('setData', response.data.data); // Menggunakan respons data secara langsung
                        resolve(response.data);
                    } else {
                        reject('Data tidak valid');
                    }
                })
                .catch((error) => {
                    reject(error);
                });
        });
    },
};

const mutations = {
    setData(state, serviceactivity) {
        state.items = serviceactivity; // Set items ke respons data yang diterima
    },
    setParamServiceActivity(state, data) {
        state.options.serviceactivity = [];
        for (let i = 0; i < data.length; i++) { // Mengakses data langsung, tanpa rows
            const option = {
                value: data[i].id, // Mengakses data langsung, sesuai struktur yang diterima
                title: data[i].name,
                oid: data[i].id,
            };
            state.options.serviceactivity.push(Object.assign({}, option));
        }
    },
};


export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
