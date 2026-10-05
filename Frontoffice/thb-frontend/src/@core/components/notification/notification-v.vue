<template>
    <div class="position-relative">
        <!-- <b-button @click="stopNotifSound" size="sm">henti</b-button> -->
        <!-- <b-button @click="playNotifSound" size="sm">cobain</b-button> -->
        <div @click="toggleNotifications($event)" class="pointer">
            <feather-icon icon="BellIcon" size="18" color="white"/>
            <span v-if="unreadCount > 0" class="notification-dot position-absolute px-1">
                <div class="d-flex justify-content-center align-items-center h-100 w-100 text-white fs-9" style="line-height: 100%;">
                    {{ getUnreadText(unreadCount) }}
                </div>
            </span>
        </div>

        <div v-if="showNotifications" v-click-outside="closeNotifications" class="notification-dropdown">
            <NotificationList 
                :notifications="passNotifications" 
                :vHideRead="hideRead" 
                @toggleRead="updateNotificationStatus"
                @itemClick="handleItemClick" 
                @radResult="triggerRadResult"
                @labResult="triggerLabResult"
                @confirmKardex="openConfirmKardex"
            />
        </div>

        <!-- <template v-if="showVerificationModal">
            <verificationSBARView @closeModal="toggleVerificationModal" @toggleRead="updateNotificationStatus"  ref="childVerifSbarRef" />
        </template> -->
        <verificationSBARView @closeModal="toggleVerificationModal" @toggleRead="updateNotificationStatus"  ref="childVerifSbarRef" />
        <ConfirmKardexView ref="confirmKardexRef" @refresh="refreshNotification"/>
        <LabResultBundleModalView ref="labBundleModalResult"/>
        <RadResultModalView ref="radModalResult"/>
    </div>
</template>

<script>
const notif1 = new Audio(require("@/@core/assets/sound/notification/notification1.mp3"))

import clickOutside from '@/@core/directives/click-outside';
import NotificationList from '@/@core/components/notification/notificationList.vue';
import verificationSBARView from '@/views/pages/patient/sbar/verificationSBARView.vue';
import LabResultBundleModalView from '@/views/pages/patient/medical_support/LabResultBundleModalView.vue';
import RadResultModalView from '@/views/pages/patient/medical_support/RadResultModalView.vue';
import dateMixin from "@/mixins/dateMixin";
import optionMixin from "@/mixins/optionMixin";
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import ConfirmKardexView from '@/views/pages/patient/kardex/ConfirmKardexView.vue'

export default {
    directives: { clickOutside },
    components: { verificationSBARView, NotificationList, LabResultBundleModalView, RadResultModalView, ConfirmKardexView },
    mixins: [dateMixin, optionMixin],

    data() {
        return {
            showNotifications: false,
            showVerificationModal: false,
            lastToggleClickTarget: null,
        };
    },

    props: {
        notifications: Array,
        hideRead: {
            type: Boolean,
            default: false,
        }
    },

    watch: {
        "notifications"(newValue) {
            if(newValue) {
                this.checkToPlay()
            }
        },
    },

    computed: {
        unreadCount() {
            // return this.notifications.filter(n => !n.isRead).length;
            return this.notifications.filter(n => n.read_at == null || n.read_at == '' ).length;
        },
        passNotifications(){
            return this.notifications;
        }
    },
    methods: {
        openConfirmKardex(obj) {
            this.$refs.confirmKardexRef.parentTrigger(obj); 
        },
        
        // behavior notifikasi di DOM
        toggleNotifications(event) {
            this.lastToggleClickTarget = event?.target;
            this.showNotifications = !this.showNotifications;
        },

        toggleVerificationModal() {
            this.showVerificationModal = !this.showVerificationModal;
        },

        closeNotifications() {
            if (this.showVerificationModal) {
                return; // Don't close the notification dropdown if the modal is open
            }
            this.showNotifications = false;
        },

        // informasi di notifikasinya
        getUnreadText(count) {
            if (count > 99) return '99+'
            return count 
        },

        // markAllAsRead() {
        //     // Mark all notifications as read
        //     this.notifications.forEach(n => {
        //         n.isRead = true;
        //     });
        // },

        // buat ngebunyiin suara notifikasi
        checkToPlay() {
            // let countTransferDoesntRead = this.notifications.filter(n => (n.read_at == null || n.read_at == '') && n.type.toLowerCase() == 'transfer' ).length
            let countTransferDoesntRead = this.notifications.filter(n => n.read_at == null || n.read_at == '').length
            if (countTransferDoesntRead) {
                this.playNotifSound()
            } else {
                this.stopNotifSound()
            }
        },

        playNotifSound() {
            notif1.play()
            // notif1.loop = true
        },

        stopNotifSound() {
            notif1.loop = false
            notif1.pause()
            notif1.currentTime = 0
        },

        // Update notification's isRead status when a notification is clicked
        updateNotificationStatus(updatedItem) {
            if(updatedItem.read_at) return

            this.$store.dispatch('notification/readNotificationID', updatedItem.id)
            .then(response => {
                this.refreshNotification()
            })
            .catch(console.error)
        },

        refreshNotification() {
            this.$emit('refreshnotif', 'refresh notif')
        },

        // buat nge-read notifikasi
        // readNotification(notif_id){
        //     // console.log('rednotif', notif_id)
        //     this.$store
        //         .dispatch('notification/readNotificationID', notif_id)
        //         .then(response => {
        //             // console.log('readNotification', response)
        //             this.$emit('refreshnotif', 'refresh notif');
        //         })
        //         .catch(console.error);
        // },

        // buat nge-trigger modal SBAR
        handleItemClick(item) {
            this.showVerificationModal = true;
            this.$refs.childVerifSbarRef.parentTrigger(item); 
            // this.showNotifications = false;  // Close the notification dropdown when clicking an item
        },
        
        // trigger modal lihat hasil lab & radiologi
        triggerRadResult(message) {
            this.$refs.radModalResult.seeRadiologyResult(message);
        },

        triggerLabResult(message) {
            this.$refs.labBundleModalResult.seeLaboratoryResult(message);
        },

        toastification(a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
    },
};
</script>

<style scoped>
.notification-dot {
    min-width: 12px;
    height: 12px;
    background-color: red;
    border-radius: calc(12px / 2);
    top: 0px;
    right: 0px;
}

.notification-dropdown {
    position: absolute;
    top: 40px;
    right: 0;
    width: 340px;
    background-color: white;
    border-radius: 6px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
    z-index: 1000;  /* Ensure this is above other content */
    border: 1.5px solid #0D98BA;
}
</style>
