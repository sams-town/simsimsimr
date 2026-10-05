<template>
    <div>
        <div class="row no-gutters">
            <div class="col-6 col-lg-2 my-1 my-lg-0 px-1 px-md-2 d-flex align-self-stretch">
                <router-link :to="{ name: 'SatusehatView' }" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menus.menu1 }">
                        <i><img src="../../assets/icons/top_menu/satusehat/menu_1_.svg" alt=""></i>
                        <span>Dashboard</span>
                    </b-button>
                </router-link>
            </div>
            <div class="col-6 col-lg-2 my-1 my-lg-0 px-1 px-md-2 d-flex align-self-stretch">
                <router-link :to="{ name: 'SatusehatPractitionerView' }" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menus.menu2 }">
                        <i><img src="../../assets/icons/top_menu/satusehat/menu_1_.svg" alt=""></i>
                        <span>Referensi Praktisi</span>
                    </b-button>
                </router-link>
            </div>
            <div class="col-6 col-lg-2 my-1 my-lg-0 px-1 px-md-2 d-flex align-self-stretch">
                <router-link :to="{ name: 'SatusehatDepartmentView' }" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menus.menu3 }">
                        <i><img src="../../assets/icons/top_menu/satusehat/menu_1_.svg" alt=""></i>
                        <span>Mapping Organisasi</span>
                    </b-button>
                </router-link>
            </div>
            <div class="col-6 col-lg-2 my-1 my-lg-0 px-1 px-md-2 d-flex align-self-stretch">
                <router-link :to="{ name: 'BpjsClaimSubmissionSheetView' }" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menus.menu4 }">
                        <i><img src="../../assets/icons/top_menu/satusehat/menu_1_.svg" alt=""></i>
                        <span>Mapping Lokasi</span>
                    </b-button>
                </router-link>
            </div>
            <div class="col-6 col-lg-2 my-1 my-lg-0 px-1 px-md-2 d-flex align-self-stretch">
                <router-link :to="{ name: 'BpjsVisitMonitoringView' }" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menus.menu5 }">
                        <i><img src="../../assets/icons/top_menu/satusehat/menu_1_.svg" alt=""></i>
                        <span>Mapping Vaksin</span>
                    </b-button>
                </router-link>
            </div>
            <div class="col-6 col-lg-2 my-1 my-lg-0 px-1 px-md-2 d-flex align-self-stretch">
                <router-link :to="{ name: 'BpjsReferenceDataView' }" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menus.menu6 }">
                        <i><img src="../../assets/icons/top_menu/satusehat/menu_1_.svg" alt=""></i>
                        <span>Kirim Encounter</span>
                    </b-button>
                </router-link>
            </div>
            <div class="col-6 col-lg-2 my-1 my-lg-0 px-1 px-md-2 d-flex align-self-stretch mt-lg-2">
                <router-link to="#" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menus.menu7 }">
                        <i><img src="../../assets/icons/top_menu/satusehat/menu_1_.svg" alt=""></i>
                        <span>Kirim Condition</span>
                    </b-button>
                </router-link>
            </div>
            <div class="col-6 col-lg-2 my-1 my-lg-0 px-1 px-md-2 d-flex align-self-stretch mt-lg-2">
                <router-link to="#" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menus.menu7 }">
                        <i><img src="../../assets/icons/top_menu/satusehat/menu_1_.svg" alt=""></i>
                        <span>Kirim Observation-TTV</span>
                    </b-button>
                </router-link>
            </div>
            <div class="col-6 col-lg-2 my-1 my-lg-0 px-1 px-md-2 d-flex align-self-stretch mt-lg-2">
                <router-link to="#" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menus.menu7 }">
                        <i><img src="../../assets/icons/top_menu/satusehat/menu_1_.svg" alt=""></i>
                        <span>Kirim Procedure</span>
                    </b-button>
                </router-link>
            </div>
            <div class="col-6 col-lg-2 my-1 my-lg-0 px-1 px-md-2 d-flex align-self-stretch mt-lg-2">
                <router-link to="#" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menus.menu7 }">
                        <i><img src="../../assets/icons/top_menu/satusehat/menu_1_.svg" alt=""></i>
                        <span>Kirim Imunisasi</span>
                    </b-button>
                </router-link>
            </div>
            <div class="col-6 col-lg-2 my-1 my-lg-0 px-1 px-md-2 d-flex align-self-stretch mt-lg-2">
                <router-link to="#" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menus.menu7 }">
                        <i><img src="../../assets/icons/top_menu/satusehat/menu_1_.svg" alt=""></i>
                        <span>Kirim Diet</span>
                    </b-button>
                </router-link>
            </div>
            <div class="col-6 col-lg-2 my-1 my-lg-0 px-1 px-md-2 d-flex align-self-stretch mt-lg-2">
                <router-link to="#" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menus.menu7 }">
                        <i><img src="../../assets/icons/top_menu/satusehat/menu_1_.svg" alt=""></i>
                        <span>Kirim Clinical Impression</span>
                    </b-button>
                </router-link>
            </div>
            <div class="col-6 col-lg-2 my-1 my-lg-0 px-1 px-md-2 d-flex align-self-stretch mt-lg-2">
                <router-link to="#" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menus.menu7 }">
                        <i><img src="../../assets/icons/top_menu/satusehat/menu_1_.svg" alt=""></i>
                        <span>Mapping Obat/Alkes & BHP </span>
                    </b-button>
                </router-link>
            </div>
            <div class="col-6 col-lg-2 my-1 my-lg-0 px-1 px-md-2 d-flex align-self-stretch mt-lg-2">
                <router-link to="#" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menus.menu7 }">
                        <i><img src="../../assets/icons/top_menu/satusehat/menu_1_.svg" alt=""></i>
                        <span>Mapping Medication </span>
                    </b-button>
                </router-link>
            </div>
            <div class="col-6 col-lg-2 my-1 my-lg-0 px-1 px-md-2 d-flex align-self-stretch mt-lg-2">
                <router-link to="#" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menus.menu7 }">
                        <i><img src="../../assets/icons/top_menu/satusehat/menu_1_.svg" alt=""></i>
                        <span>Mapping Medication Request </span>
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
                menu5: false,
                menu6: false,
                menu7: false,
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