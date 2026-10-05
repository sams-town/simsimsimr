<template>
    <div>
        <!-- <div class="card rounded shadow-sm border-0">
            <div class="card-body"> -->
        <div class="d-flex flex-wrap">
            <li class="m-2">
                <router-link :to="{ name: 'PharmacyRecipeOutpatientView' }" class="px-16 py-8 rounded-pill"
                    :class="{ active: menus.menu1 }">
                    <span class="text-semi-14-on">List Resep Dokter Rawat Jalan</span>
                </router-link>
            </li>
            <li class="m-2">
                <router-link to="#" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu2 }">
                    <span class="text-semi-14-on">List Resep Dokter Rawat Inap</span>
                </router-link>
            </li>
            <li class="m-2">
                <router-link to="#" class="px-16 py-8 rounded-pill" :class="{ active: menus.menu3 }">
                    <span class="text-semi-14-on">List Resep Dokter IGD</span>
                </router-link>
            </li>
            <li class="m-2">
                <router-link :to="{ name: 'DoctorStudiesView' }" class="px-16 py-8 rounded-pill"
                    :class="{ active: menus.menu4 }">
                    <span class="text-semi-14-on">List Resep Apotik</span>
                </router-link>
            </li>
            <li class="m-2">
                <router-link :to="{ name: 'NurseStudiesView' }" class="px-16 py-8 rounded-pill"
                    :class="{ active: menus.menu5 }">
                    <span class="text-semi-14-on">Tambah Resep Apotik</span>
                </router-link>
            </li>
            <li class="m-2">
                <router-link :to="{ name: 'PatientProcedureView' }" class="px-16 py-8 rounded-pill"
                    :class="{ active: menus.menu6 }">
                    <span class="text-semi-14-on">Report Resep</span>
                </router-link>
            </li>
        </div>
        <!-- </div>
        </div> -->
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
            menus: {
                menu1: false,
                menu2: false,
                menu3: false,
                menu4: false,
                menu5: false,
                menu6: false,
            }
        }
    },
    mounted() {
        if (this.menus.hasOwnProperty(this.selectedMenu)) {
            this.menus[this.selectedMenu] = true;
        }
    }
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
}</style>