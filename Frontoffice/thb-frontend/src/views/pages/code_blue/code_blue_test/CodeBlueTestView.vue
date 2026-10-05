<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Code Blue' },
            ]"
        />
        <section class="content pt-12">
            <div class="col mt-32">
                <section class="card">
                    <div class="card-header font-weight-bold bg-primary-200">CODE BLUE</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-center">
                            <div class="col-lg-4">
                                <!-- <div class="mt-12">
                                    <label class="c-form-label">Instalasi</label>
                                    <v-select v-model="params.health_facility_id" aria-readonly="true" :options="OptionsHealthFacility" :reduce="OptionsHealthFacility => OptionsHealthFacility.value" label="title" placeholder="-- Instalasi --"></v-select>
                                </div> -->
                                <div class="mt-12">
                                    <label class="c-form-label">Ruangan</label>
                                    <!-- {{ params.health_facility_item_id }} -->
                                    <v-select v-model="params.health_facility_item_id" aria-readonly="true" :options="OptionsHealthFacilityItem" :reduce="OptionsHealthFacilityItem => OptionsHealthFacilityItem.value" label="title" placeholder="-- Poliklinik --"></v-select>
                                </div>
                                <div class="mt-12" v-if="isHaveRoom">
                                    <label class="c-form-label">Kamar</label>
                                    <v-select v-model="params.health_facility_item_room_number" aria-readonly="true" :options="OptionsRoomHealthFacility" :reduce="OptionsRoomHealthFacility => OptionsRoomHealthFacility.number" label="name" placeholder="-- Kamar --"></v-select>
                                </div>
                                <div class="text-center mt-24">
                                    <b-button type="submit" variant="primary" class="mr-1" :disabled="isDisabledCodeBlue" @click.prevent="sendCodeBlue"> 
                                        <feather-icon icon="SearchIcon"/> CODE BLUE
                                    </b-button>
                                    <!-- <b-button variant="outline-primary" @click.prevent="filterReset">
                                        <feather-icon icon="XIcon"/> Clear Filter
                                    </b-button> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </section>
    </div>
</template>
<script>
import Layout from '@/@core/light/Layout.vue';
import { mapState } from 'vuex'
import vSelect from 'vue-select'
import dateMixin from "@/mixins/dateMixin";
import moment from 'moment';
import router from '@/router/index';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import io from 'socket.io-client';

export default {
    name: 'CodeBlue',
    components: {
        vSelect, Layout, ToastificationContent
    },
    mixins: [dateMixin],
    data() {
        return {
            socket: 
                io(process.env.VUE_APP_SOCKET_IO_NOTIF, {
                path: '/socket.io',
                transports: ['websocket', 'polling', 'flashsocket'],
                withCredentials: true}),
            params: {
                // health_facility_id: '',
                health_facility_item_id: '',
                health_facility_item_room_number:0,
            },
            isDisabledCodeBlue:false,
            OptionsRoomHealthFacility:[],
            isHaveRoom:false,
            ruanganTanpaKamar:{
                iccu:'7f2a9f80-c30d-4cbd-9f0d-364cda2ec84c',
                icu:'6c7d7e53-ff24-4246-85b2-07ea77ac801f',
                icuIsolasi:'ff1f252e-daab-4bdb-b308-e9f4cb11a47d',
                nicu:'5515af72-6dcb-415d-89e4-2433e7b0149d',
                perinatologi:'05079cd9-07c6-4cf9-beb0-271eb301b7c1',
                picu:'e66e6ed4-4ef4-4375-a6d0-0445e3ec3039',  
            }
        }
    },
    watch: {
        // 'params.health_facility_id' (value) {
        //     if(value){
        //         if(this.params.health_facility_item_id){
        //             this.params.health_facility_item_id = ''
        //         }
        //         return this.$store.dispatch('healthfacilityitem/getParam', value)
        //     }
        // },
        // 'params.health_facility_item_id' (value) {
        //     if(value){
        //         return this.$store.dispatch('employee/getParam', value)
        //     }
        // },
        'params.health_facility_item_id' (value) {
            if(value){
                this.params.health_facility_item_room_number = ''

                return this.$store.dispatch('room/getRoombyFacilityID', {'health_facility_item_id':value}).then(response => {
                    this.OptionsRoomHealthFacility = response
                    this.checkHaveRoom(value)
                })
            }
        },
    },
    computed: {
        ...mapState({
            // OptionsHealthFacility: state => state.healthfacility.options.healthfacility,
            OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,
            // OptionsHealthFacilityList: state => state.healthfacility.items,
        }),
        dateFormat() {
            return create_at => this.$options.filters.formatDate(create_at)
        },
    },

    methods: {
        checkHaveRoom(health_facility_item_id){
            if (Object.values(this.ruanganTanpaKamar).includes(health_facility_item_id)) {
                this.isHaveRoom = false
            } else if(this.OptionsRoomHealthFacility.length > 0){
                this.isHaveRoom = true
            } else {
                this.isHaveRoom = false
            }
        },  
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        async sendCodeBlue(){
            if (this.params.health_facility_item_id) {
                this.isDisabledCodeBlue = true
                try {
                    const formData = {
                        bed_id: this.params.health_facility_item_id
                    }
                    
                    const response = await this.$store.dispatch('codeblue/formPostPut', formData);
                    
                    this.socket.emit('SEND_ANNOUNCE', this.params)
                    
                    this.notification('Success','CheckIcon','success','Code Blue Berhasil')
    
                } catch (error) {
                    this.notification('Failed','CloseIcon','danger','Code Blue unsuccessful')
                    console.log('Error at: ', error);
                } finally {
                    this.isDisabledCodeBlue = false
                }
            } else {
                this.notification('Warning','CloseIcon','warning','Tambahkan Poli terlebih dahulu')
            }
        },
        getAllHeartFacilityitems(){
            let value = ''
            this.$store.dispatch('healthfacilityitem/getParam', value)
            // this.$store.dispatch('healthfacilityitem/getAll', this.params)
            //     .then(res => {
            //         // this.show = false
            //         // this.isDisabledFilter = false
            //     },
            //     err => {
            //         // this.show = false
            //         // this.isDisabledFilter = false
            //     })
        }
    },

    mounted() {
    },

    created() {
        this.getAllHeartFacilityitems()
    },
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>