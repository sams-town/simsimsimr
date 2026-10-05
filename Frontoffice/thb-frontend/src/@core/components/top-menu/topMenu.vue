<template>
    <div>
        <div class="row no-gutters">
            <div class="col-4 col-lg-2 px-1 px-md-2 d-flex align-self-stretch">
                <router-link :to="{ name: 'PatientListView'}" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menu1 }">
                        <b-icon icon="bootstrap-fill"></b-icon>
                        <span>List Pendaftaran Pasien</span>
                    </b-button>
                </router-link>
            </div>
            <div class="col-4 col-lg-2 px-1 px-md-2 d-flex align-self-stretch">
                <router-link :to="{ name: 'ExternalListView' }" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menu2 }">
                        <b-icon icon="bootstrap-fill"></b-icon>
                        <span>List Pendaftaran Eksternal</span>
                    </b-button>
                </router-link>
            </div>
            <div class="col-4 col-lg-2 px-1 px-md-2 d-flex align-self-stretch">
                <router-link :to="{ name: 'CancelListView' }" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menu3 }">
                        <b-icon icon="bootstrap-fill"></b-icon>
                        <span>List Pendaftaran Batal</span>
                    </b-button>
                </router-link>
            </div>
            <div class="col-4 col-lg-2 px-1 px-md-2 d-flex align-self-stretch">
                <router-link :to="{ name: 'SensusListView' }" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menu4 }">
                        <b-icon icon="bootstrap-fill"></b-icon>
                        <span>List Sensus Rawat Inap</span>
                    </b-button>
                </router-link>
            </div>
            <div class="col-4 col-lg-2 px-1 px-md-2 d-flex align-self-stretch">
                <router-link :to="{ name: 'ScheduleDoctorView'}" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menu5 }">
                        <b-icon icon="bootstrap-fill"></b-icon>
                        <span>Jadwal Praktek Dokter</span>
                    </b-button>
                </router-link>
            </div>
            <div class="col-4 col-lg-2 px-1 px-md-2 d-flex align-self-stretch">
                <router-link :to="{ name: 'SchedulePatientView'}" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menu14 }">
                        <b-icon icon="bootstrap-fill"></b-icon>
                        <span>Jadwal Kontrol Pasien</span>
                    </b-button>
                </router-link>
            </div>
        </div>
        <!-- NEXT ROW -->
        <div class="row no-gutters mt-2">
            <div class="col-4 col-lg-2 px-1 px-md-2 d-flex align-self-stretch">
                <router-link :to="{ name: 'RegisMasterView' }" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menu7 }">
                        <b-icon icon="bootstrap-fill"></b-icon>
                        <span>Pendaftaran</span>
                    </b-button>
                </router-link>
            </div>
            <div class="col-4 col-lg-2 px-1 px-md-2 d-flex align-self-stretch">
                <router-link :to="{ name: 'OutpatientVisitReportView' }" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menu6 }">
                        <b-icon icon="bootstrap-fill"></b-icon>
                        <span>Laporan Kunjungan Pasien Rawat Jalan</span>
                    </b-button>
                </router-link>
            </div>
            <!-- <div class="col px-1 px-md-2 d-flex align-self-stretch">
                <router-link :to="{ name: 'ExternalRegView'}" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menu8 }">
                        <b-icon icon="bootstrap-fill"></b-icon>
                        <span>Pendaftaran Eksternal</span>
                    </b-button>
                </router-link>
            </div>
            <div class="col px-1 px-md-2 d-flex align-self-stretch">
                <router-link :to="{ name: 'ReportView' }" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menu9 }">
                        <b-icon icon="bootstrap-fill"></b-icon>
                        <span>Laporan</span>
                    </b-button>
                </router-link>
            </div>
            <div class="col px-1 px-md-2 d-flex align-self-stretch">
                <router-link to="#" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menu10 }">
                        <b-icon icon="bootstrap-fill"></b-icon>
                        <span>Dashboard</span>
                    </b-button>
                </router-link>
            </div> -->
            <div class="col-4 col-lg-2 px-1 px-md-2 d-flex align-self-stretch">
                <router-link :to="{ name: 'MedicalHistoryView' }" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menu11 }">
                        <b-icon icon="bootstrap-fill"></b-icon>
                        <span>Histori Rekam Medis</span>
                    </b-button>
                </router-link>
            </div>
            <template v-if="userData.employee_id == 'd77142c4-4777-47d8-bd0c-fd7dfd134af4'">
                <div class="col-4 col-lg-2 px-1 px-md-2 d-flex align-self-stretch">
                    <router-link :to="{ name: 'MedicalRecordView' }" class="col px-0 d-flex align-items-stretch prevent-hover">
                        <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menu12 }">
                            <b-icon icon="bootstrap-fill"></b-icon>
                            <span>Merge Rekam Medis</span>
                        </b-button>
                    </router-link>
                </div>
            </template>
            <div class="col-4 col-lg-2 px-1 px-md-2 d-flex align-self-stretch">
                <router-link :to="{ name: 'ServiceReportView' }" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menu13 }">
                        <b-icon icon="bootstrap-fill"></b-icon>
                        <span>Laporan Rekapitulasi Kegiatan Pelayanan</span>
                    </b-button>
                </router-link>
            </div>
        </div>
        <!-- <div class="row no-gutters mt-2">
            <div class="col-4 col-lg-2 px-1 px-md-2 d-flex align-self-stretch">
                <router-link :to="{ name: 'ServiceReportView' }" class="col px-0 d-flex align-items-stretch prevent-hover">
                    <b-button pill class="col menu-buttons text-dark gap-2" :class="{ selected: menu13 }">
                        <b-icon icon="bootstrap-fill"></b-icon>
                        <span>Laporan Rekapitulasi Kegiatan Pelayanan</span>
                    </b-button>
                </router-link>
            </div>
        </div> -->
    </div>
</template>

<script>
export default {
    props: {
        selectedMenu: {
            type: String,
            default: 'menu3'
        }
    },
    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),

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
        }
    },
    mounted() {
        // console.log(this.selectedMenu);
        switch (this.selectedMenu) {
            case 'menu1':
                this.menu1 = true;
                break;
            case 'menu2':
                this.menu2 = true;
                break;
            case 'menu3':
                this.menu3 = true;
                break;
            case 'menu4':
                this.menu4 = true;
                break;
            case 'menu5':
                this.menu5 = true;
                break;
            case 'menu6':
                this.menu6 = true;
                break;
            case 'menu7':
                this.menu7 = true;
                break;
            case 'menu8':
                this.menu8 = true;
                break;
            case 'menu9':
                this.menu9 = true;
                break;
            case 'menu10':
                this.menu10 = true;
                break;
            case 'menu11':
                this.menu11 = true;
                break;
            case 'menu12':
                this.menu12 = true;
                break;
            case 'menu13':
                this.menu13 = true;
                break;
            case 'menu14':
                this.menu14 = true;
                break;
            default:
                break;
        }
    },
}
</script>

<style scoped>
@import '../../css/utility.css';

.prevent-hover:hover{
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

/* .menu-buttons.selected>span {
    color: #8908B9;
} */

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