<template>
    <div class="position-relative" :class="`w-${posWidth}`">
        <div class="d-flex flex-column mb-1" :class="fullWidth == true ? 'full-room-box' : 'room-box'">
            <div class="d-flex justify-content-between align-items-start fs-14 p-1" :class="getRoomGenderVariant(datasets.gender_availability)" style="border-bottom: 1px solid black;">
                <div>
                    <div class="font-weight-bold">
                        {{ datasets.code }}
                        <span v-if="datasets.room_number">- {{ datasets.room_number }}</span>
                    </div>
                    <div class="fs-10" style="line-height: 100%;">KELAS {{datasets.service_class_name}}</div>                         
                </div>
                <div>
                    <template v-if="datasets.room_pressure == '+'">
                        <feather-icon size="16" color="blue" icon="PlusCircleIcon"/> 
                    </template>
                    <template v-if="datasets.room_pressure == '-'" >
                        <feather-icon size="16" color="red" icon="MinusCircleIcon"/> 
                    </template>
                    <template v-if="datasets.is_isolation == 1" >
                        <feather-icon size="16" color="brown" icon="ShieldIcon"/> 
                    </template>
                    <template v-if="datasets.is_surgery == 1" >
                        <feather-icon size="16" color="orange" icon="ZapIcon"/> 
                    </template>
                    <template v-if="datasets.is_internal == 1" >
                        <feather-icon size="16" color="red" icon="HeartIcon"/> 
                    </template>
                </div>
            </div>

            <!-- <div class="fs-12 px-1" :class="getRoomGenderVariant(datasets.gender_availability)" style="border-bottom: 1px solid black;">Kelas {{datasets.service_class_name}}</div> -->
            
            <div class="w-100 h-100 px-16 pb-16 pt-8 position-relative" :class="getRoomGenderVariant(datasets.gender_availability)">
                <div class="row h-100" style="margin: 0 -4px;">
                    <div v-for="(item_b, index_b) in reversedItems" class="col px-1">
                        <!-- <div 
                            @click="preOpenCheck(item_b)" 
                            class="h-100 d-flex justify-content-center align-items-center pointer" 
                            :class="getBedStatusVariant(item_b.type_status_bed_id)" 
                            style="border: 1px solid grey;">
                            {{ item_b.name }}
                        </div> -->
                        <div 
                            @click="preOpenCheck(item_b)" 
                            class="h-100 d-flex justify-content-center align-items-center pointer" 
                            :class="getBedStatusVariant(item_b)" 
                            style="border: 1px solid grey;">
                            {{ item_b.name }}
                        </div>
                    </div>
                </div>
                
                <feather-icon @click="triggerParentSetting" v-if="userData.employee_id == admin_id || userData.user_group.id == ns_group_id || userData.user_group.id == administration_id" size="12" color="grey" icon="SettingsIcon" class="pointer" style="position: absolute; bottom: 2; left: 2;"/> 
            </div>
        </div >
    </div>
</template>

<script>
import optionMixin from '@/mixins/optionMixin';

export default {
    mixins: [optionMixin],
    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),
            admin_id: 'd77142c4-4777-47d8-bd0c-fd7dfd134af4',
            ns_group_id: '29644626-5b54-4d54-ab69-22146a631277',
            administration_id: '00a30ce3-2ca3-45f8-8837-4a0babce20e9',
        }
    },

    props: {
        datasets: {
            type: Object,
            default: () => {}
        },
        reverse: {
            type: Boolean,
            default: false
        },
        fullWidth: {
            type: Boolean,
            default: false
        },
        posWidth: {
            type: Number,
            default: 100
        },
    },

    computed: {
        reversedItems() {
            if (this.reverse) {
                return [...this.datasets.beds].reverse();
            }
            return this.datasets.beds
        },
    },

    
    methods: {
        getBedStatusVariant(bed) {
            // Cek apakah bed benar-benar ada pasien
            if (bed.patients && bed.patients.length > 0) {
                return 'bg-success'; // Hijau - terisi
            }
            
            // Cek status bed lainnya
            if (bed.type_status_bed_id == 1) {
                return 'bg-white'; // Abu-abu/Putih - kosong
            }
            
            if (bed.type_status_bed_id == 3) {
                return 'bg-blinking'; // Kuning berkedip - pindahan
            }
            
            return 'bg-white'; // Default
        },
        
        triggerParentSetting() {
            // console.log('hello: ', this.datasets);
            this.$emit("childSetting", this.datasets);
        },

        preOpenCheck(obj) {
            if (this.userData.employee_id == this.admin_id || this.userData.user_group.id == this.ns_group_id) {
                if (obj.type_status_bed_id != 3) return this.triggerParentSwap(obj)
                if (obj.type_status_bed_id == 3) return this.triggerConfirm(obj)
            } else {
                this.triggerInform(obj)
            }
        },

        triggerInform(obj) {
            let message = {
                room_name: this.datasets.name,
                bed_id: obj.id,
                bed_name: obj.name,
                type_status_bed_id: obj.type_status_bed_id,
                patients: obj.patients
            }
            this.$emit("childInform", message);
        },

        triggerParentSwap(obj) {
            let message = {
                room_name: this.datasets.name,
                bed_id: obj.id,
                bed_name: obj.name,
                type_status_bed_id: obj.type_status_bed_id,
                patients: obj.patients
            }
            this.$emit("childSwap", message);
        },

        triggerConfirm(obj) {
            const patients = obj?.patients || []
            let need_to_confirm = patients.filter(item => item.is_transfer == 0) || []
            // console.log('need_to_confirm', need_to_confirm);

            if (need_to_confirm?.length > 0) {
                let selected_patient = need_to_confirm[0]
                // console.log('selected_patient', selected_patient);

                this.$store.dispatch('registration/getById', selected_patient.registration_destination_id)
                .then(
                    response => {
                        let temp = response?.length > 0 ? response[0] : []
                        this.$emit("childConfirmTranfer", 
                            {
                                registration_destination_id: temp?.registration_destination_id, 
                                alias_name: temp?.alias_name, 
                                name: temp?.name, 
                                room_abbreviation: temp?.room_abbreviation,
                                bed_name: temp?.bed_name,
                                doctor_in_charge_id: temp?.doctor_in_charge_id,
                                birth_date: temp?.birth_date
                            }
                        );
                    }
                )
            }
        },

        getRoomGenderVariant(gender) {
            if (gender === '' || gender === null) return 'bg-white'
            if (gender === 0) return 'bg-pink-200'
            if (gender == 1) return 'bg-info-300'
            return ''
        },
    },
}
</script>
<style scoped>
.room-box {
    height: 137px; max-width: 200px; border: 2px solid black;
    /* height: 123px; max-width: 180px; border: 2px solid black; */
    /* aspect-ratio: 16 / 9; border: 2px solid black; */
}

.full-room-box {
    height: 137px; border: 2px solid black;
}

.bg-blinking {
    animation: blink 1.5s infinite ease-in-out;
}

.w-33 {
    width: 33%;
}

@keyframes blink {
    0% {
        background-color: yellowgreen;
    }
    50% {
        background-color: yellow;
    }
    100% {
        background-color: yellowgreen;
    }
}
</style>