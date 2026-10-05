<template>
    <div style="font-size: 32px;">
        <section class="text-white">
            <div class="position-relative text-center bg-primary p-16">
                <div class="font-weight-bold">Informasi Ketersediaan Tempat Tidur RS Taman Harapan Baru</div>
                <div id="clock" style="font-size: 0.8em;"></div>

                <div class="d-flex align-items-center justify-content-center position-absolute h-100 px-20 bg-white" style="left: 0; top: 0;">
                    <b-img src="../../../../@core/assets/images/apexa.png" width="120px" class="mr-24"/>
                </div>

                <div class="d-flex align-items-center justify-content-center position-absolute h-100 px-20" style="right: 0; top: 0;">
                    <b-button @click="fullScreenMode()" v-b-tooltip.hover.left :title="active.tooltip" variant="primary-500">
                        <feather-icon :icon="active.tooltip == 'Maximize' ? 'MaximizeIcon' : 'MinimizeIcon'"/>
                    </b-button>
                </div>
            </div>
            <div class="bg-w3green p-8 text-right overflow-hidden" style="font-size: 0.7em;">
                <div class="slide-left" style="margin-right: -390px;">Ketersediaan tempat tidur sampai pasien tiba di rumah sakit</div>
            </div>
        </section>

        <!-- <section class="row mx-0 mt-20">
            <template v-if="ward_list?.length > 0">
                <div v-for="(item, index) in ward_list" class="col-6 col-md-4 col-lg-2" :class="index <= 5 ? 'mt-20 mt-lg-0' : 'mt-16'">
                    <div class="card text-center" style="font-size: 0.45em;">
                        <div class="card-header bg-primary text-white" style="font-weight: 700;">{{ item.name }}</div>
                        <div class="card-body p-2">
                            <table class="text-left" style="font-weight: 600;">
                                <tr>
                                    <td style="width: 100px;">Kelas</td>
                                    <td style="width: 30px;">:</td>
                                    <td>{{ item.class }}</td>
                                </tr>
                                <b-tr>
                                    <td>Total</td>
                                    <td>:</td>
                                    <td>{{ item.total }}</td>
                                </b-tr>
                                <b-tr>
                                    <td>Tersedia</td>
                                    <td>:</td>
                                    <td>{{ item.available }}</td>
                                </b-tr>
                            </table>
                        </div>
                    </div>
                </div>
            </template>
        </section> -->
        <section class="row mx-0 mt-20">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header bg-primary"></div>
                    <b-table bordered fixed :fields="RoomFields" :items="ward_list" thead-class="text-bold-24" tbody-tr-class="text-semi-18" class="text-center">
                        <template #cell(no)="data">{{ data.index+1 }}</template>
                    </b-table>
                </div>
            </div>
            <div class="col-lg">
                <div class="row">
                    <template v-if="monitoring_list.length > 0">
                        <div v-for="(item, index) in monitoring_list" class="col-6 col-lg-4" :class="index <= 2 ? 'mt-20 mt-lg-0' : 'mt-20'">
                            <div class="card text-center font-weight-bold" style="font-size: 0.625em;">
                                <div class="card-header bg-primary text-white">{{ item.name }}</div>
                                <div class="card-body">{{ item.available }}</div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </section>
    </div>
</template>
<script>
import moment from 'moment'
import 'moment/locale/id';
moment.locale('id');

export default {
    data() {
        return {
            date_now: moment(new Date()).locale("id").format('dddd, Do MMMM YYYY'),
            monitoring_list: [],
            room_list: [],
            ward_list: [],
            RoomFields: [
                { key: 'no', label: 'No', sortable: true, thStyle: { width: '100px' } },
                { key: 'name', label: 'Ruangan', sortable: true },
                { key: 'class', label: 'Kelas', sortable: true },
                { key: 'total', label: 'Total Tempat Tidur', sortable: true },
                { key: 'available', label: 'Tersedia', sortable: true },
            ],
            // RoomFields: [
            //     { key: 'no', label: 'No', sortable: true },
            //     { key: 'service_class_name', label: 'Kelas Rawat', sortable: true },
            //     { key: 'name', label: 'Ruangan', sortable: true },
            //     { key: 'capacity', label: 'Kapasitas', sortable: true },
            //     { key: 'available', label: 'Tersedia', sortable: true },
            //     { key: 'last_updated', label: 'Tanggal Update', sortable: true },
            // ],

            params: {
                limit: 20,
                offset: 0,
            },
            totalRows: 0,
            active: {
                tooltip: 'Maximize',
            }
        }
    },
    methods: {
        // getRoomList() {
        //     if (this.totalRows === 0 || this.params.offset >= this.totalRows) {
        //         this.params.offset = 0
        //         this.params.limit = 20
        //     }

        //     try {
        //         this.$store.dispatch('room/getRoomListV2', this.params).then(response => {
        //             this.room_list = response.rows
        //             this.totalRows = response.iTotal

        //             if (this.params.offset + this.params.limit < this.totalRows) {
        //                 this.params.offset += this.params.limit
        //             } else {
        //                 this.params.offset = 0
        //             }
        //         });
        //     } catch (error) {
        //         console.log('Error while GET DATA at: ', error);
        //     }
        // },
        getMonitoringClass() {
            try {
                this.$store.dispatch('room/getMonitoringClass').then(response => {
                    this.monitoring_list = response
                })
            } catch (error) {
                console.error('Error while GET data from room/getMonitoringClass at: ', error);
            }
        },
        updateClock() {
            const clockElement = document.getElementById('clock');
            if (clockElement) {
                clockElement.innerHTML = moment().locale("id").format('dddd, Do MMMM YYYY H:mm:ss');
            }
        },
        fullScreenMode(){
            if (!document.fullscreenElement &&
                !document.mozFullScreenElement &&
                !document.webkitFullscreenElement &&
                !document.msFullscreenElement) {
                if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen();
                } else if (document.documentElement.mozRequestFullScreen) {
                    document.documentElement.mozRequestFullScreen();
                } else if (document.documentElement.webkitRequestFullscreen) {
                    document.documentElement.webkitRequestFullscreen();
                } else if (document.documentElement.msRequestFullscreen) {
                    document.documentElement.msRequestFullscreen();
                }
                this.active.tooltip = 'Minimize'
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
                this.active.tooltip = 'Maximize'
            }
        },
        getWards() {
            try {
                this.$store.dispatch('room/getAvailabilityRoom').then(data => {
                    this.ward_list = data
                    // console.log('this.type_room_data: ', data);
                })
            } catch (error) {
                console.error('Error while GET DATA at: ', error);
            }
        },

    },
    created() {
        this.getWards()

        // this.$store.dispatch('helper/getParam', 'type_room')
        this.getMonitoringClass()
        // this.getRoomList()
    },
    mounted() {
        this.updateClock();
        setInterval(this.updateClock, 1000)

        this.intervalId = setInterval(() => {
            this.getWards()
            // this.getRoomList()
            this.getMonitoringClass()
        }, 5000);
    },
    beforeDestroy() {
        clearInterval(this.intervalId);
    }
}

</script>

<style scoped>
@import './../../../../@core/css/main/main.min.css';

/* .head-class {
    font-size: 0.5em;
}

.row-class {
    font-size: 0.5em;
} */

.slide-left {
	-webkit-animation: slide-left 10s linear ;
            animation: slide-left 10s linear ;
            animation-iteration-count: infinite;
}

@-webkit-keyframes slide-left {
    0% {
    -webkit-transform: translateX(0);
            transform: translateX(0);
    }
    100% {
    -webkit-transform: translateX(-100%);
            transform: translateX(-100%);
    }
}
@keyframes slide-left {
    0% {
        -webkit-transform: translateX(0);
                transform: translateX(0);
    }
    100% {
        -webkit-transform: translateX(-100%);
                transform: translateX(-100%);
    }
}


</style>