<template>
    <div class="card-body py-32 px-8 rounded position-relative overflow-hidden" :class="color">
        <div class="d-flex flex-wrap align-items-center text-center text-lg-left">
            <img src="../../assets/icons/dashboard/circle2.svg" class="position-absolute" style="bottom:0; right:0;"
                alt="decoration" />
            <div class="col-lg-2 px-0">
                <img :src="getIcon()" alt="" class="icon__card">
            </div>
            <div class="col-lg px-0">
                <div class="d-flex flex-wrap justify-content-center">
                    <div class="col-lg px-0 px-lg-3 text-white">
                        <div class="text-semi-18">{{ title }}</div>
                        <div class="text-medium-14">{{ number }}</div>
                    </div>
                    <div class="mt-2 mt-lg-0">
                        <div
                            class="bg-white text-dark d-flex align-items-center justify-content-center py-1 px-12 rounded-pill">
                            <feather-icon :icon="getArrowIcon()" class="mr-1" />
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
    props: {
        type: {
            type: String,
            default: 'up'
        },
        title: {
            type: String,
            default: 'Pasien Baru',
        },
        number: {
            type: Number,
            default: 200,
        },
        color: {
            type: String,
            default: 'purple',
        },
        percent: {
            type: Number,
            default: 7.2
        }
    },
    mounted() {
        this.getIcon();
        this.getArrowIcon()
    },
    methods: {
        getIcon() {
            switch (this.title) {
                case 'Pasien Lama':
                    return require('../../assets/icons/dashboard/new-patient.svg');
                case 'Pasien Baru':
                    return require('../../assets/icons/dashboard/old-patient.svg');
                case 'Pasien Laki-laki':
                    return require('../../assets/icons/dashboard/men.svg');
                case 'Pasien Perempuan':
                    return require('../../assets/icons/dashboard/woman.svg');
                default:
                    return require('../../assets/icons/dashboard/woman.svg');
            }
        },
        getArrowIcon() {
            if (this.type === 'up') {
                return 'ArrowUpIcon'
            } else if (this.type === 'down') {
                return 'ArrowDownIcon'
            }
        },
    },
}
</script>
<style scoped>
.purple {
    background: linear-gradient(180deg, #AF9BFF 0%, #CFB4FD 100%);
}

.blue {
    background: linear-gradient(180deg, #588BF2 0%, #ACBEFD 100%);
}

.red {
    background: linear-gradient(180deg, #F97BB4 0%, #F493EB 100%);
}

.img__icon {
    width: 50%;
    object-fit: contain;
}

@media screen and (min-width: 992px) {
    .img__icon {
        width: 100%;
    }
}
</style>