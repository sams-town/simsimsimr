<template>
    <div class="notification-panel" :class="{ 'scrollable-panel': !disablescroll, 'full-panel': disablescroll }">
        <div v-if="$route.path !== '/notifications'" class="card-header px-12 py-8">
            <div class=" d-flex justify-content-between align-items-center">
                <div class="fs-16 font-weight-bold">NOTIFIKASI</div>
                <router-link to="/notifications" class="fs-13">Lihat Semua</router-link>
            </div>
        </div>

        <div v-if="newNotifications.length">
            <div class="fs-14 px-12 py-8 text-white bg-ax-primary-500" style="font-weight: 400; font-style: italic;">
                {{ newNotifications.length }} belum dibaca
            </div>

            <!-- <template v-for="(item, index) in newNotifications"> -->
                <div v-for="(item, index) in newNotifications" :key="'new-' + index" @click="onItemClick(item)" class="notification-item p-12 w-100 pointer">
                    <div class="d-flex justify-content-between align-items-end">
                        <div class="font-weight-bold text-neutral-600">{{ item.title }}</div>
                        <div class="fs-13 text-neutral-400"><i>{{ timeFromNow(item.created_at) }}</i></div>
                    </div>
                    <div class="fs-12 text-neutral-600 mt-1">{{ item.message }}</div>
                </div>
            <!-- </template> -->
        </div>

        <div v-if="earlierNotifications.length && vHideRead == false">
            <div class="fs-14 px-12 py-8 text-white bg-ax-primary-500" style="font-weight: 400; font-style: italic;">Dibaca</div>

            <!-- <template v-for="(item, index) in earlierNotifications" :key="'old-' + index"> -->
                <div v-for="(item, index) in earlierNotifications" :key="'old-' + index" @click="onItemClick(item)" class="notification-item p-12 w-100 pointer">
                    <div class="d-flex justify-content-between align-items-end">
                        <div class="font-weight-bold text-neutral-600">{{ item.title }}</div>
                        <div class="fs-13 text-neutral-400"><i>{{ timeFromNow(item.created_at) }}</i></div>
                    </div>
                    <div class="fs-13 text-neutral-600 mt-1">{{ item.message }}</div>
                </div>
            <!-- </template> -->
        </div>

        <div v-if="!newNotifications.length && (!earlierNotifications.length || vHideRead == true)" class="text-center text-muted py-5">
            Tidak ada notifikasi
        </div>
    </div>
</template>

<script>
import dateMixin from "@/mixins/dateMixin";
// import notification from "@/router/routes/notification";
// import kardex from "@/store/pharmacy/kardex";

export default {
    mixins: [dateMixin],
    props: {
        notifications: Array,
        disablescroll: {
            type: Boolean,
            default: false
        },
        vHideRead: {
            type: Boolean,
            default: false,
        }
    },
    computed: {
        // Filter new notifications (where isRead is false)
        newNotifications() {
            return this.notifications.filter(n => !n.read_at)
            // return this.notifications.filter(n => !n.isRead);  // Only unread notifications
        },
        // Filter earlier notifications (where isRead is true)
        earlierNotifications() {
            return this.notifications.filter(n => n.read_at)
            // return this.notifications.filter(n => n.isRead); // Only read notifications
        }
    },
    methods: {
        onItemClick(item) {
            // console.log(item);
            // sbar tu di read-nya ketika dokter yang dituju ngelakuin verifikasi
            // selain sbar, read-nya ketika di klik
            
            if (item.type.toLowerCase() == 'sbar') return this.$emit('itemClick', item)
            if (item.type.toLowerCase() == 'transfer') {
                this.$emit('toggleRead', item)
                return this.transferInformation(item?.data?.treatment_room_id)
            }
            if (item.type.toLowerCase() == 'regist_inpatient') {
                this.$emit('toggleRead', item)
                return this.blankToFloorPlan(item?.data?.health_facility_item_id)
            }
            if (item.type.toLowerCase() == 'rad_expertise') {
                this.$emit('toggleRead', item)
                return this.openRadResult(item?.data?.radiology_request_item_id)
            }
            if (item.type.toLowerCase() == 'lab_result') {
                this.$emit('toggleRead', item)
                return this.openLabResult(item?.data?.laboratory_request_id)
            }
            if (item.type.toLowerCase() == 'form_checkout_patient') {
                this.$emit('toggleRead', item)
                return this.blankToBillPatient(item?.data?.registration_id)
            }
            if (item.type.toLowerCase() == 'prescription_checkout_patient') {
                this.$emit('toggleRead', item)
                return this.blankToPrescription(item?.data?.prescription_id)
            }
            if (item.type.toLowerCase() == 'kardex') return this.openConfirmKardex(item)
        },

        async transferInformation(treatment_room_id) {
            try {                
                await this.$store.dispatch('registration/getTreatmentRoomById', treatment_room_id)
                .then(
                    response => {
                        const temp_tre = response?.length > 0 ? response[0] : []
                        this.blankToFloorPlan(temp_tre.to_unit)
                })
            } catch (error) {
                console.error('Error catch while GET registration/getTreatmentRoom at: ', error);
            }
        },

        blankToFloorPlan(health_facility_item_id){
            const url = this.$router.resolve({ path: '/inpatient/ward/'+ health_facility_item_id }).href;
            window.open(url, '_blank');
        },

        blankToBillPatient(registration_id){
            const url = this.$router.resolve({ path: '/payment/detail/'+ registration_id}).href;
            window.open(url, '_blank');
        },

        blankToPrescription(prescription_id){
            const url = this.$router.resolve({ path: '/pharmacy/recipe/outpatient/detail/'+ prescription_id}).href;
            window.open(url, '_blank');
        },

        openRadResult(radiology_request_item_id) {
            let message = {
                alias_name: '',
                name: '',
                url: '',
                costing_name: '',
            }

            try {
                this.$store.dispatch('radiology/getRadiologyItemById', radiology_request_item_id)
                .then(
                    async res => {
                        if (res?.length > 0) {
                            message.alias_name = res[0].alias_name
                            message.name = res[0].patient_name
                            message.url = res[0].url
                            message.costing_name = res[0].costing_name
                            this.$emit('radResult', message)
                        }
                    }
                )
                .catch(({message, debug, response}) => {
                    console.error(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                console.error('Error catch at: ', error)    
            }            
        },

        async openLabResult(laboratory_request_id) {
            let orderData = {}
            let result = []
            let message = {
                alias_name: '',
                name: '',
                norm: '',
                no_request: '',
                laboratory_request_id: '',
                laboratory_request_item: '',
                arryData: '', // isinya result
            }

            try {
                await this.$store.dispatch('laboratory/getByIdOrder', laboratory_request_id)
                    .then(response => {
                        // console.log('response 1: ', response);
                        if(response?.length > 0) {
                            orderData = response[0]
                        }
                    }
                )
                await this.$store.dispatch('laboratory/getResultById', laboratory_request_id)
                    .then(response => {
                        if (response?.length > 0) {
                            // console.log('response 2: ', response);
                            result = response[0].result_detail
                        }
                    }
                )    
                
                message.alias_name = orderData?.regData?.alias_name
                message.name = orderData?.regData?.name
                message.norm = orderData?.regData?.name
                message.no_request = orderData?.no_request
                message.laboratory_request_id = orderData?.id
                message.laboratory_request_item = orderData?.laboratory_request_item
                message.arryData = result || []

                // console.log('message', message);
                this.$emit('labResult', message)
            } catch (error) {
                console.error('Error catch at: ', error)    
            }            
        },

        async openConfirmKardex(obj) {
            const kardex_id = obj?.data?.kardex_id
            try {                
                await this.$store.dispatch('kardex/getKardexDetail', {id: kardex_id})
                .then(
                    response => {
                        // console.log('response: ', response);
                        if (response.length > 0) {
                            let message = {
                                kardex: response[0],
                                notification: obj
                            }
                            this.$emit('confirmKardex', message)                        
                        }
                })
            } catch (error) {
                console.error('Error catch while GET kardex/getKardexDetail at: ', error);
            }
        }
    }
};
</script>

<style scoped>
.notification-panel {
    background-color: #fff;
    border-radius: 10px;
}

.scrollable-panel {
    max-height: 600px;
    overflow-y: auto;
}

.full-panel {
    max-height: none;
    overflow-y: visible;
}

/* .avatar {
    width: 48px;
    height: 48px;
}

.avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 48px;
    height: 48px;
    background-color: #e0e0e0;
    font-weight: bold;
    color: #555;
    font-size: 16px;
    overflow: hidden;
} */

.notification-item {
    background-color: white;
    transition: background-color 0.2s;
    border-top: 1px solid #0d97ba49;
}

.notification-item:hover {
    background-color: #74e3ff37;
    /* background-color: #f0f2f5; */
}

/* .notification-item.unread {
    background-color: #f0f0f0;
} */
</style>
