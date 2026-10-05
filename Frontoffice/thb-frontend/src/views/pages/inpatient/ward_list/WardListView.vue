<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Denah Bangsal' }
            ]"
        >
        <section class="">
            <div>
                <ModernLabel label="Bangsal Lantai 2" />
                <b-table bordered responsive small head-variant="light" :items="second_floor" :fields="roomFields" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                    <template #cell(no)="row">{{ row.index+1 }}</template>
                    <template #cell(action)="row">                        
                        <router-link :to="{ path: '/inpatient/ward/'+ row.item.health_facility_item_id}">
                            <b-button variant="w3green" size="sm" class="mr-1">
                                Detail
                                <feather-icon icon="ArrowRightCircleIcon" class="ml-1" /> 
                            </b-button>
                        </router-link>
                    </template>
                </b-table>
            </div>

            <div class="mt-32">
                <ModernLabel label="Bangsal Lantai 3" />
                <b-table bordered responsive small head-variant="light" :items="third_floor" :fields="roomFields" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                    <template #cell(no)="row">{{ row.index+1 }}</template>
                    <template #cell(action)="row">                        
                        <router-link :to="{ path: '/inpatient/ward/'+ row.item.health_facility_item_id}">
                            <b-button variant="w3green" size="sm" class="mr-1">
                                Detail
                                <feather-icon icon="ArrowRightCircleIcon" class="ml-1" /> 
                            </b-button>
                        </router-link>
                    </template>
                </b-table>
            </div>

            <div class="mt-32">
                <ModernLabel label="Bangsal Lantai 4" />
                <b-table bordered responsive small head-variant="light" :items="fourth_floor" :fields="roomFields" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                    <template #cell(no)="row">{{ row.index+1 }}</template>
                    <template #cell(action)="row">                        
                        <router-link :to="{ path: '/inpatient/ward/'+ row.item.health_facility_item_id}">
                            <b-button variant="w3green" size="sm" class="mr-1">
                                Detail
                                <feather-icon icon="ArrowRightCircleIcon" class="ml-1" /> 
                            </b-button>
                        </router-link>
                    </template>
                </b-table>
            </div>

            <div class="mt-32">
                <ModernLabel label="Bangsal Lantai 5" />
                <b-table bordered responsive small head-variant="light" :items="fifth_floor" :fields="roomFields" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                    <template #cell(no)="row">{{ row.index+1 }}</template>
                    <template #cell(action)="row">                        
                        <router-link :to="{ path: '/inpatient/ward/'+ row.item.health_facility_item_id}">
                            <b-button variant="w3green" size="sm" class="mr-1">
                                Detail
                                <feather-icon icon="ArrowRightCircleIcon" class="ml-1" /> 
                            </b-button>
                        </router-link>
                    </template>
                </b-table>
            </div>
        </section>
        </Layout>
    </div>
</template>

<script>
export default {
    data() {
        return {
            roomFields: [
                { key: 'no', label: 'No', thStyle: { width: '60px' } },
                { key: 'name', label: 'Bangsal' },
                { key: 'total', label: 'Jumlah Bed', thStyle: { width: '150px' }, },
                { key: 'available', label: 'Tersedia', thStyle: { width: '150px' }, },
                { key: 'filled', label: 'Terisi', thStyle: { width: '150px' }, },
                { key: 'action', label: 'Aksi', thStyle: { width: '150px' },},
            ],

            second_floor: [],
            third_floor: [],
            fourth_floor: [],
            fifth_floor: [],
        }
    },

    methods: {
        getWards() {
            try {
                this.$store.dispatch('room/getDashboardRoom').then(
                    async response => {
                        let temp_ward = response
                        const roomCategories = {
                            second_floor: [5, 6, 13, 14, 15, 16, 17, 18, 27, 29, 12, 32],
                            third_floor: [10, 30, 31],
                            fourth_floor: [2, 4, 7],
                            fifth_floor: [1]
                        };

                        Object.entries(roomCategories).forEach(([key, ids]) => {
                            const idSet = new Set(ids);
                            this[key] = temp_ward ? temp_ward.filter(item => idSet.has(Number(item.type_room_id))) : []
                        });

                        // console.log('this.second_floor', this.second_floor);
                        // console.log('this.third_floor', this.third_floor);
                        // console.log('this.fourth_floor', this.fourth_floor);
                        // console.log('this.fifth_floor', this.fifth_floor);
                    }
                )
            } catch (error) {
                console.error('Error while GET DATA at: ', error);                
            }
        },
    },

    created() {
        this.getWards()
    }
}
</script>