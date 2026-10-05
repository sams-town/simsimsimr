<template>
    <div>
        <TopInpatientMenu selectedMenu="menu1" />
        <div class="col mt-32">
            <div class="d-flex justify-content-between align-items-center">
                <p class="text-bold-24">Ruang Perawatan</p>
                <router-link :to="{ name: 'MasterRoomView' }">
                    <b-button variant="info-600" size="sm" class="text-white">
                        <feather-icon icon="PlusIcon" /> Tambah Ruang Perawatan
                    </b-button>
                </router-link>
            </div>
            <section v-if="active.section === 'typeRoom'">
                <div class="row">
                    <div v-for="item in type_room_data" class="col-lg-3 mt-16 mt-lg-20">
                        <div @click="selectRoomType(item.type_room_id)" class="card pointer bg-orange-100">
                            <div class="card-body">
                                <div class="w-100 d-flex flex-column align-items-center justify-content-center">
                                    <img src="@/@core/assets/icons/inpatient/type1.svg" alt="">
                                    <div class="text-bold-24 mt-12">{{ item.name }}</div>
                                </div>
                                <div class="row mt-24">
                                    <div class="col-lg">
                                        <div class="w-100 bg-primary-500 text-white d-flex align-items-center justify-content-center py-2 px-12 rounded-pill">
                                            <feather-icon icon="UsersIcon" class="mr-1" />
                                            <p class="mb-0 text-medium-12">{{ item.filled }} Pasien</p>
                                        </div>
                                    </div>
                                    <div class="col-lg">
                                        <div class="w-100 bg-primary-500 text-white d-flex align-items-center justify-content-center py-2 px-12 rounded-pill">
                                            <feather-icon icon="CheckCircleIcon" class="mr-1" />
                                            <p class="mb-0 text-medium-12">{{ item.available }} Tersedia</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section v-else-if="active.section === 'room'">
                <b-button @click="backToSection('typeRoom')" variant="outline-primary" class="mt-20">
                    <feather-icon icon="ArrowLeftIcon" /> Kembali
                </b-button>
                <div class="row">
                    <div v-for="item in room_data" class="col-lg-3 mt-16 mt-lg-20">
                        <div @click="selectRoom({ id:item.room_id, name:item.room_name })" class="card pointer bg-orange-100">
                            <div class="card-body">
                                <div class="w-100 d-flex flex-column align-items-center justify-content-center">
                                    <img src="@/@core/assets/icons/inpatient/type1.svg" alt="">
                                    <div class="text-bold-24 mt-12">{{ item.room_name }}</div>
                                </div>
                                <div class="row mt-24">
                                    <div class="col-lg">
                                        <div class="w-100 bg-primary-500 text-white d-flex align-items-center justify-content-center py-2 px-12 rounded-pill">
                                            <feather-icon icon="UsersIcon" class="mr-1" />
                                            <p class="mb-0 text-medium-12">{{ item.Filled }} Pasien</p>
                                        </div>
                                    </div>
                                    <div class="col-lg">
                                        <div class="w-100 bg-primary-500 text-white d-flex align-items-center justify-content-center py-2 px-12 rounded-pill">
                                            <feather-icon icon="CheckCircleIcon" class="mr-1" />
                                            <p class="mb-0 text-medium-12">{{ item.Available }} Tersedia</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </section>
            <section v-else-if="active.section === 'bed'">
                <b-button @click="backToSection('room')" variant="outline-primary" class="mt-20">
                    <feather-icon icon="ArrowLeftIcon" /> Kembali
                </b-button>
                <div class="card mt-20">
                    <div class="card-body d-flex align-items-center">
                        <div>
                            <img src="@/@core/assets/icons/inpatient/no_box_type1.svg" alt="img-bed">
                        </div>
                        <p class="text-bold-18 ml-16 ml-lg-40">Ruang Perawatan: {{ active.temp_room_name }}</p>
                    </div>
                </div>
                <div class="row">
                    <div v-for="item in bed_data" class="col-lg-6 mt-16">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <img src="@/@core/assets/icons/inpatient/bed1.svg" alt="bed-img">
                                    <b-table-simple small fixed>
                                        <b-tbody class="text-regular-14">
                                            <b-tr>
                                                <b-td>Bed</b-td>
                                                <b-td>: {{ item.name }}</b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>Status</b-td>
                                                <b-td>: {{ item.type_status_bed_name }}</b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>Pasien</b-td>
                                                <b-td>: {{ item?.alias_name }} {{ item.patient_name }}</b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>Diagnosa</b-td>
                                                <b-td>: {{ item.icd10_name }}</b-td>
                                            </b-tr>
                                        </b-tbody>
                                    </b-table-simple>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>
<script>
import TopInpatientMenu from '@/@core/components/top-menu/TopInpatientMenu.vue';
// import WardCard from '@/@core/components/cards/WardCard.vue';
import { mapState } from 'vuex'

export default {
    components: {
        TopInpatientMenu, //WardCard
    },
    data() {
        return {
            active: {
                section: 'typeRoom', // room, bed
                temp_type_room_id: null,
                temp_room_id: null,
                temp_room_name: '',
            },
            type_room_data: [],
            room_data: [],
            bed_data: [],
        }
    },
    computed: {
        ...mapState({
            type_room_items: state => state.room.items_type_room_monitoring,
            room_items: state => state.room.items_room_monitoring,
            bed_items: state => state.room.items_bed,
        }),
    },
    
    watch: {
        // type_room_items (obj) {
        //     if(obj){
        //         this.type_room_data = obj
        //         // console.log('type room data: ', obj);
        //     }
        // },
        room_items (obj) {
            if(obj){
                this.room_data = obj
                // console.log('room data: ', this.room_data);
            }
        },
        bed_items (obj) {
            if(obj){
                this.bed_data = obj
                // console.log('bed data: ', this.bed_data);
            }
        },
    },
    
    methods: {
        selectRoomType(type_room_id) {
            this.active.section = 'room'
            this.active.temp_type_room_id = type_room_id
            this.$store.dispatch('room/getRoomMonitoring', this.active.temp_type_room_id)
        },
        selectRoom(room) {
            this.active.section = 'bed'
            this.active.temp_room_id = room.id
            this.active.temp_room_name = room.name
            this.$store.dispatch('room/getBed', this.active.temp_room_id)
        },
        backToSection(section) {
            this.active.section = section
            if (this.active.section === 'bed') {
                this.$store.dispatch('room/getRoomMonitoring', this.active.temp_type_room_id)
            } else if (this.active.section === 'room') {
                this.getWards()
                // this.$store.dispatch('room/getTypeRoomMonitoring')
            }
        },
        getWards() {
            try {
                this.$store.dispatch('room/getDashboardRoom').then(data => {
                    this.type_room_data = data
                    // console.log('this.type_room_data: ', this.type_room_data);                
                })
            } catch (error) {
                console.error('Error while GET DATA at: ', error);                
            }
        },

    },
    
    created() {
        this.getWards()
        // this.$store.dispatch('room/getTypeRoomMonitoring')
    }
}

</script>