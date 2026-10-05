<template>
    <div class="card-body py-32 px-8 position-relative rounded" :class="color">
        <div class="d-flex flex-wrap text-center text-lg-left">
            <img src="../../assets/icons/dashboard/circle.svg" class="position-absolute" style="top:0; right:0;"
                alt="decoration" />
            <div class="col-lg-3 px-0">
                <img :src="getIcon()" alt="" class="img__icon">
            </div>
            <div class="col-lg text-white">
                <div class="text-semi-18">{{ title }}</div>
                <div class="text-medium-16">{{ number }}</div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        title: {
            type: String,
            default: 'Pasien Rawat Jalan',
        },
        number: {
            type: Number,
            default: 300,
        },
        color: {
            type: String,
            default: 'purple',
        },
    },
    mounted() {
        this.getIcon();
        // console.log('umber: ', this.number)
    },
    methods: {
        getIcon() {
            switch (this.title) {
                case 'Pasien Rawat Jalan':
                    return require('../../assets/icons/dashboard/rawat-jalan.svg');
                case 'Pasien Rawat Inap':
                    return require('../../assets/icons/dashboard/rawat-inap.svg');
                case 'Pasien IGD':
                    return require('../../assets/icons/dashboard/igd.svg');
                case 'Radiologi':
                    return require('../../assets/icons/dashboard/radiology.svg');
                case 'Laboratorium':
                    return require('../../assets/icons/dashboard/laboratory.svg');
                case 'Fisioterapi':
                    return require('../../assets/icons/dashboard/fisioterapi.svg');
                default:
                    return require('../../assets/icons/dashboard/fisioterapi.svg');
            }
        }
    },
}
</script>
<style scoped>
.purple {
    background: linear-gradient(180deg, #A172F0 0%, #C485E6 100%);
}

.blue {
    background: linear-gradient(180deg, #7B9FEA 0%, #69C6EE 100%);
}

.orange {
    background: linear-gradient(180deg, #F3AC73 0%, #EABE9C 100%);
}

.green {
    background: linear-gradient(180deg, #6DBFBF 0%, #AAE8DD 100%);
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