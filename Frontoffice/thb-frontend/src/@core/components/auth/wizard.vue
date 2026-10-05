<template>
    <div class="col">
        <div class="d-flex flex-row text-center">
            <div class="wiz-group">
                <div class="numbeing-wiz bg-pink-500">1</div>
                <span>Pilih Produk</span>
            </div>
            <div class="col px-0 py-3">
                <hr class="wiz-line" :class="{ 'bg-pink-500': step === 'second' }">
            </div>
            <div class="wiz-group">
                <div class="numbeing-wiz" :class="step === 'second' ? 'bg-pink-500' : 'bg-neutral-300'">2</div>
                <span>Profile Info</span>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        step: {
            type: String,
            default: 'first'
        }
    }
}
</script>
<style scoped>
@import '../../../@core/css/main/main.min.css';

.numbeing-wiz {
    width: 34px;
    height: 34px;
    color: #fff;
    border-radius: 50%;
    font-weight: 600;
    font-size: 12px;
    line-height: 15px;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10;
}

.wiz-group {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    gap: 10px;
}

.wiz-group span {
    font-style: normal;
    font-weight: 500;
    font-size: 10px;
    line-height: 12px;
}

.wiz-line {
    height: 3px;
    margin: 0 -15px;
}
</style>