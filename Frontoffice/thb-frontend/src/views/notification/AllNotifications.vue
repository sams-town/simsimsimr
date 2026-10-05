<template>
    <b-container fluid class="py-4">
        <b-row class="justify-content-center">
            <b-col cols="12" md="10" lg="8">
                <div class="bg-white notification-wrapper p-4 rounded shadow-sm">
                    <b-button variant="primary" size="sm" class="mb-3" to="/">
                        <b-icon-house-fill class="mr-1" /> Back to Home
                    </b-button>

                    <h4 class="font-weight-bold mb-3">Notifikasi</h4>
                    <!-- Tabs -->
                    <b-tabs v-model="activeTab" card lazy>
                        <b-tab title="Semua" active>
                            <NotificationList 
                                :notifications="filteredNotifications.all" 
                                :disablescroll="disableScroll"
                                @itemClick="handleItemClick" 
                                @radResult="triggerRadResult" 
                                @labResult="triggerLabResult"
                                @confirmKardex="openConfirmKardex"
                            />
                        </b-tab>
                        <b-tab title="Belum Dibaca">
                            <NotificationList 
                                :notifications="filteredNotifications.unread"
                                :disablescroll="disableScroll" 
                                @itemClick="handleItemClick" 
                                @radResult="triggerRadResult" 
                                @labResult="triggerLabResult"
                                @confirmKardex="openConfirmKardex"
                            />
                        </b-tab>
                    </b-tabs>
                </div>
            </b-col>
        </b-row>
        <!-- Modal -->
        <!-- <verificationSBARView v-if="showVerificationModal" v-model:visible="showVerificationModal" @toggleRead="updateNotificationStatus" ref="childVerifSbarRef" /> -->
        <verificationSBARView @toggleRead="updateNotificationStatus" ref="childVerifSbarRef" />
        <ConfirmKardexView ref="confirmKardexRef" @refresh="getNotifications"/>
        <LabResultBundleModalView ref="labBundleModalResult"/>
        <RadResultModalView ref="radModalResult"/>
    </b-container>
</template>

<script>
import verificationSBARView from '@/views/pages/patient/sbar/verificationSBARView.vue';
import NotificationList from '@/@core/components/notification/notificationList.vue';
import io from 'socket.io-client';
import LabResultBundleModalView from '@/views/pages/patient/medical_support/LabResultBundleModalView.vue';
import RadResultModalView from '@/views/pages/patient/medical_support/RadResultModalView.vue';
import ConfirmKardexView from '@/views/pages/patient/kardex/ConfirmKardexView.vue'

export default {
    components: { verificationSBARView, NotificationList, LabResultBundleModalView, RadResultModalView, ConfirmKardexView },
    data() {
        return {
            socket:
                io(process.env.VUE_APP_SOCKET_IO_NOTIF, {
                    path: '/socket.io',
                    transports: ['websocket', 'polling', 'flashsocket'],
                    withCredentials: true
                }),
            // showVerificationModal: false,
            notifications: [],
            activeTab: 0,
            disableScroll: false,
            nutritionists_position: '8ca42f62-85bb-45f8-bf32-152a792e0f40',            
        };
    },
    computed: {
        filteredNotifications() {
            // const now = new Date();
            return {
                all: this.notifications,
                unread: this.notifications.filter(n => !n.read_at),
                // new: this.notifications.filter(n => n.unread),
                // earlier: this.notifications.filter(n => !n.unread)
            };
        }
    },
    methods: {
        openConfirmKardex(obj) {
            this.$refs.confirmKardexRef.parentTrigger(obj); 
        },

        handleItemClick(item) {
            // this.showVerificationModal = true;
            this.$refs.childVerifSbarRef.parentTrigger(item);
        },
        // markAllAsRead() {
        //     this.notifications.forEach(n => (n.unread = false));
        // },
        async getNotifications(){
            try {                
                await this.$store.dispatch('notification/getNotificationAll', { status: 'all'})
                .then(
                    response => {
                        if(response) this.notifications = response
                    }
                )
                .catch(({message, debug, response}) => {
                    console.error('response', response)                        
                })
            } catch (error) {
                console.error('Error catch while GET notification/getNotificationAll at: ', error);
            }
        },

        updateNotificationStatus(updatedItem) {
            if(updatedItem.read_at) return

            this.$store.dispatch('notification/readNotificationID', updatedItem.id)
            .then(response => {
                this.getNotifications()
            })
            .catch(console.error)
        },

        // refreshNotification() {
        //     this.$emit('refreshnotif', 'refresh notif')
        // },

        // readNotification(notif_id) {
        //     this.$store
        //         .dispatch('notification/readNotificationID', notif_id)
        //         .then(response => {
        //             console.log('readNotification', response)
        //             this.getNotifications()
        //         })
        //         .catch(console.error);
        // },

        // Update notification's isRead status when a notification is clicked
        // updateNotificationStatus(updatedItem) {
        //     if (updatedItem.read_at) return
        //     this.readNotification(updatedItem.id)
        // },

        triggerRadResult(message) {
            this.$refs.radModalResult.seeRadiologyResult(message);
        },

        triggerLabResult(message) {
            this.$refs.labBundleModalResult.seeLaboratoryResult(message);
        },
    },
    mounted() {
        this.disableScroll = true
        this.socket.on('SBAR', (obj) => {
            // console.log('socket notif', obj)
            let userData = JSON.parse(localStorage.getItem('userData'))

            if (userData.position.id == this.nutritionists_position) this.getNotifications()
            if (obj.type == 'VERIF_SBAR' && obj?.doctor_in_charge_id == userData.employee_id) this.getNotifications()
            if ((obj.type == 'REFRESH_DATA' || obj.type == 'CONSULTATION_KARDEX') && (obj?.doctor_in_charge_id == userData.employee_id || obj?.position_id == userData.position.id)) this.getNotifications()
            if ((obj.type == 'PATIENT_TRANSFER' || obj.type == 'LAB_RESULT' || obj.type == 'RAD_RESULT') && obj?.position_id == userData.position.id) this.getNotifications()
        })
    },
    created() {
        this.getNotifications();
    }
};
</script>

<style scoped>
.notification-wrapper {
    /* max-height: 70vh; */
    overflow-y: hidden;
    overflow-x: hidden
}
</style>
