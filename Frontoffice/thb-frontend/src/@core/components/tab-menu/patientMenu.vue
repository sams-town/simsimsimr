<template>
    <div>
        <div class="card rounded shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex flex-wrap">
                    <li class="m-2">
                        <router-link :to="{ path: '/patient/detail/'+ id}" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu1 }">
                            <span class="text-semi-14-on">Detail Pendaftaran</span>
                        </router-link>
                    </li>
                    <!-- <li class="m-2">
                        <router-link :to="{ name: 'PatientInsuranceView' }" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu19 }">
                            <span class="text-semi-14-on">Penjamin</span>
                        </router-link>
                    </li> -->
                    <li class="m-2">
                        <router-link :to="{ name: 'PatientCPPTView' }" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu2 }">
                            <span class="text-semi-14-on">Catatan Terintegrasi /CPPT</span>
                        </router-link>
                    </li>
                    <li class="m-2">
                        <router-link :to="{ path: '/patient/resume/'+ id}" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu3 }">
                            <span class="text-semi-14-on">Resume</span>
                        </router-link>
                    </li>
                    <li class="m-2">
                        <router-link :to="{ path: '/patient/nurse/'+ id}" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu5 }">
                            <span class="text-semi-14-on">Perawat</span>
                        </router-link>
                    </li>
                    <li class="m-2">
                        <router-link :to="{ path: '/patient/triage/'+ id}" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu21 }">
                            <span class="text-semi-14-on">Triase</span>
                        </router-link>
                    </li>
                    <!-- <li v-if="isRanap == false" class="m-2"> -->
                    <li class="m-2">
                        <router-link :to="{ path: '/patient/doctor/'+ id}" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu4 }">
                            <span class="text-semi-14-on">Dokter</span>
                        </router-link>
                    </li>
                    <li class="m-2">
                        <router-link :to="{ path: '/patient/procedure/'+ id}" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu6 }">
                            <span class="text-semi-14-on">Procedure</span>
                        </router-link>
                    </li>
                    <!-- <li class="m-2">
                        <router-link to="#" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu7 }">
                            <span class="text-semi-14-on">Penunjang Medis</span>
                        </router-link>
                    </li> -->
                    <li class="m-2">
                        <router-link :to="{ path: '/patient/alkes/'+ id}" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu8 }">
                        <!-- <router-link :to="{ name: 'PatientAlkesView' }" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu8 }"> -->
                            <span class="text-semi-14-on">BHP</span>
                        </router-link>
                    </li>
                    <li class="m-2">
                        <router-link :to="{ path: '/patient/tool-use/'+ id}" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu9 }">
                        <!-- <router-link :to="{ name: 'PatientToolView' }" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu9 }"> -->
                            <span class="text-semi-14-on">Pemakaian Alat</span>
                        </router-link>
                    </li>
                    <li class="m-2">
                        <router-link :to="{ name: 'PatientBloodGourdView' }" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu10 }">
                            <span class="text-semi-14-on">Labu Darah</span>
                        </router-link>
                    </li>
                    <li class="m-2">
                        <router-link :to="{ name: 'PatientBHPView' }" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu11 }">
                            <span class="text-semi-14-on">Sewa Kamar</span>
                        </router-link>
                    </li>
                    <li class="m-2">
                        <router-link :to="{ name: 'PatientRoomView' }" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu12 }">
                            <span class="text-semi-14-on">Ruang/Kamar</span>
                        </router-link>
                    </li>
                    <li class="m-2">
                        <router-link :to="{ name: 'PatientNutritionView' }" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu13 }">
                            <span class="text-semi-14-on">Gizi</span>
                        </router-link>
                    </li>
                    <!-- <li class="m-2">
                        <router-link :to="{ name: 'PatientMCUView' }" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu14 }">
                            <span class="text-semi-14-on">MCU</span>
                        </router-link>
                    </li> -->
                    <li class="m-2">
                        <router-link :to="{ name: 'PatientOKView'} " class="px-16 py-8 rounded-pill" :class="{ active: menus.menu15 }">
                            <span class="text-semi-14-on">OK</span>
                        </router-link>
                    </li>
                    <!-- <li class="m-2">
                        <router-link to="#" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu16 }">
                            <span class="text-semi-14-on">Asuhan Kebidanan</span>
                        </router-link>
                    </li> -->
                    <li class="m-2">
                        <router-link :to="{ path: '/patient/laboratory/'+ id}" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu17 }">
                            <span class="text-semi-14-on">Laboratorium</span>
                        </router-link>
                    </li>
                    <li class="m-2">
                        <router-link :to="{ path: '/patient/radiology/'+ id}" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu18 }">
                            <span class="text-semi-14-on">Radiologi</span>
                        </router-link>
                    </li>
                    <li class="m-2">
                        <router-link :to="{ path: '/patient/prescription/'+ id}" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu25 }">
                        <!-- <router-link :to="{ name: 'PatientAlkesView' }" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu8 }"> -->
                            <span class="text-semi-14-on">Resep</span>
                        </router-link>
                    </li>
                    <li class="m-2">
                        <router-link :to="{ path: '/patient/physiotherapy/'+ id}" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu20 }">
                            <span class="text-semi-14-on">Fisioterapi</span>
                        </router-link>
                    </li>
                    <li class="m-2">
                        <router-link :to="{ path: '/patient/inpatient/'+ id}" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu22 }">
                            <span class="text-semi-14-on">Rawat Inap</span>
                        </router-link>
                    </li>
                    
                    <!-- fazri inacbg -->
                    <li class="m-2">
                        <router-link :to="{ path: '/patient/eklaim/'+ id}" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu26 }">
                            <span class="text-semi-14-on">E-Klaim INA-CBG</span>
                        </router-link>
                    </li>
                    <!-- end fazri inacbg -->
                     
                    <template v-if="this.userData.employee_id == 'd77142c4-4777-47d8-bd0c-fd7dfd134af4'">
                        <!-- <li class="m-2">
                            <router-link :to="{ path: '/patient/medical-rehabilitation/'+ id}" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu23 }">
                                <span class="text-semi-14-on">Rehabilitasi Medik</span>
                            </router-link>
                        </li>
                        <li class="m-2">
                            <router-link :to="{ path: '/patient/hemodialysis/'+ id}" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu24 }">
                                <span class="text-semi-14-on">Hemodialisa</span>
                            </router-link>
                        </li> -->
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        selectedMenu: {
            type: String,
            default: 'menu1'
        }
    },
    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),
            id:'',
            menus: {
                menu1: false,
                menu2: false,
                menu3: false,
                menu4: false,
                menu5: false,
                menu6: false,
                menu7: false,
                menu8: false,
                menu9: false,
                menu10: false,
                menu11: false,
                menu12: false,
                menu13: false,
                menu14: false,
                menu15: false,
                menu16: false,
                menu17: false,
                menu18: false,
                menu19: false,
                menu20: false,
                menu21: false,
                menu22: false,
                menu23: false,
                menu24: false,
                menu25: false,
                menu26: false
            },
            isRanap: '',
        }
    },
    mounted() {
        if (this.menus.hasOwnProperty(this.selectedMenu)) {
            this.menus[this.selectedMenu] = true;   
        }
    },
    created() {
        if (this.$route.params.id) {
            // this.show = true
            this.id = this.$route.params.id

			this.$store.dispatch('registration/getById', this.id).then(data => {
                // console.log(data[0].health_facility_id);

                if (data[0].health_facility_id =='40f06534-07ec-4221-ad9d-dea08196079f') {
                    this.isRanap = true
                } else {
                    this.isRanap = false
                }
            })
        }
    },
}

</script>
<style scoped>
li {
    list-style-type: none;
}

li a {
    color: #78838C;
}

li a:hover {
    text-decoration: none;
    color: #101725;
    background-color: rgb(245, 239, 254);
}

li a.active {
    background-color: rgba(132, 66, 243, 0.05);
    color: indigo;
    border: 1px solid indigo;
    color: #1B1B1B;
}

li a.active:hover {
    border: 1px solid indigo;
    background-color: rgb(245, 239, 254);
    color: indigo;
}
</style>