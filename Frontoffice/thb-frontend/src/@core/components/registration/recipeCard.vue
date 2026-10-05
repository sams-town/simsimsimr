<template>
    <div class="card-body py-32 px-8 rounded text-center" :class="color">
        <div>
            <img :src="imgUrl" alt="recipe" width="40px">
        </div>
        <div class="mt-20">
            <p class="text-bold-16">Obat</p>
            <p class="text-medium-16">{{ title }} Moving</p>
            <p class="text-regular-14">{{ number }}</p>
        </div>
        <b-button variant="success-500" class="mt-40">
            Lihat Detail <feather-icon icon="ChevronRightIcon" />
        </b-button>
    </div>
</template>
<script>

export default {
    props: {
        title: {
            type: String,
            default: 'Slow'
        },
        number: {
            type: Number,
            default: 320
        },
        color: {
            type: String,
            default: 'yellow'
        }
    },
    computed: {
        imgUrl() {
            if (this.color == 'yellow') {
                return require('../../assets/images/dashboard/pharmacy-yellow.png');
            } else {
                return require('../../assets/images/dashboard/pharmacy-red.png');
            }
        }
    }
}
</script>
<style scoped>

.yellow {
    background: linear-gradient(180deg, #F3AC73 0%, #EABE9C 100%);
}

.red {
    background: linear-gradient(180deg, #F97BB4 0%, #F493EB 100%);
}
</style>