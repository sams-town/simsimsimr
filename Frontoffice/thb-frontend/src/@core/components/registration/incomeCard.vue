<template>
    <div class="card shadow-sm">
        <div class="card-body">
            <p class="text-semi-16">{{ title }}</p>
            <div class="mt-3">
                <div class="col px-0">
                    <div class="text-medium-14 text-neutral-500">Per hari ini</div>
                    <div class="d-flex align-items-end justify-content-between">
                        <div class="text-semi-24">{{ income | currency}}</div>
                        <div class="d-flex align-items-center justify-content-center py-1 px-12 rounded-pill" :class="getColor()">
                            <feather-icon :icon="getIcon()" class="mr-1" />
                            <div class="text-medium-12">{{ percent }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    components: {},
    props: {
        type: {
            type: String,
            default: 'success'
        },
        title: {
            type: String,
            default: 'Total Pendapatan Klinik'
        },
        income: {
            default: '13.900.000'
        },
        percent: {
            type: Number,
            default: 7.2
        }
    },
    mounted() {
        this.getColor()
        this.getIcon()
        // this.$store.dispatch('dashboard/getDashboardByDate');
    },
    filters:{
        currency(value) {
            return new Intl.NumberFormat("en-US", { style: "currency", currency: "IDR" }).format(value)
        }
    },
    methods: {
        getIcon() {
            if (this.type === 'success') {
                return 'ArrowUpIcon'
            } else {
                return 'ArrowDownIcon'
            }
        },
        getColor() {
            if (this.type === 'success') {
                return 'bg-success-100'
            } else {
                return 'bg-danger-100'
            }
        }
    },
}
</script>