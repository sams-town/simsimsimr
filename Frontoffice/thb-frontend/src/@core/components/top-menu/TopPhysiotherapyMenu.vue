<template>
    <div>
        <div class="row no-gutters">
            <div class="col-6 col-lg-2 my-1 my-lg-0 px-1 px-md-2 d-flex align-self-stretch">
                <router-link :to="{ name: 'PhysiotherapyExCheckupView' }" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menus.menu1 }">
                        <b-icon icon="bootstrap-fill"></b-icon>
                        <span>List Pendaftaran Pemerikasaan (Eksternal)</span>
                    </b-button>
                </router-link>
            </div>
            <div class="col-6 col-lg-2 my-1 my-lg-0 px-1 px-md-2 d-flex align-self-stretch">
                <router-link :to="{ name: 'PhysiotherapyCheckupRequestView' }" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menus.menu2 }">
                        <b-icon icon="bootstrap-fill"></b-icon>
                        <span>List Permintaan Pemeriksaan</span>
                    </b-button>
                </router-link>
            </div>
            <div class="col-6 col-lg-2 my-1 my-lg-0 px-1 px-md-2 d-flex align-self-stretch">
                <router-link :to="{ name: 'PhysiotherapyCheckupReportView' }" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menus.menu3 }">
                        <b-icon icon="bootstrap-fill"></b-icon>
                        <span>Laporan Pemeriksaan</span>
                    </b-button>
                </router-link>
            </div>
            <div class="col-6 col-lg-2 my-1 my-lg-0 px-1 px-md-2 d-flex align-self-stretch">
                <router-link to="#" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menus.menu4 }">
                        <b-icon icon="bootstrap-fill"></b-icon>
                        <span>Dashboard</span>
                    </b-button>
                </router-link>
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
            menus: {
                menu1: false,
                menu2: false,
                menu3: false,
                menu4: false,
            }
        }
    },
    mounted() {
        // console.log(this.selectedMenu);
        if (this.menus.hasOwnProperty(this.selectedMenu)) {
            this.menus[this.selectedMenu] = true;
        }
    },
}
</script>

<style scoped>
@import '../../css/utility.css';

.prevent-hover:hover {
    text-decoration: none;
}

.menu-buttons {
    background-color: #F2F2F2;
    box-shadow: 0px 2px 2px rgba(128, 109, 92, 0.4);
    text-align: left;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    border: 0;
}

.menu-buttons.selected {
    background-color: #F8C43E;
    border: 1px solid #E8A700;
}

.menu-buttons:hover {
    background-color: #fff2d1;
}

.menu-buttons>span {
    font-size: 10px;
    line-height: 12px;
    font-weight: 600;
}

@media (min-width: 1400px) {
    .menu-buttons>span {
        font-size: 12px;
        line-height: 15px;
    }

    .menu-buttons {
        padding: 15px 18px;
    }
}
</style>