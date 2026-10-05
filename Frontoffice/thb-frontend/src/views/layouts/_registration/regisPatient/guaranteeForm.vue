<template>
    <div class="col px-0 mt-2">
        <b-form>
            <div class="col px-0 mt-1 mt-lg-10 c-form">
                <label>Pilih Jaminan *</label>
                <b-form-select v-model="selected" :options="options"></b-form-select>
            </div>
            <div class="col px-0 mt-10 mt-lg-20">
                <modalBPJS />
            </div>
        </b-form>
    </div>
</template>

<script>
import modalBPJS from '@/views/layouts/_registration/regisPatient/modalBPJS.vue';
export default {
    components: { modalBPJS },
    data() {
        return {
            selected: null,
            options: [
                { value: null, text: '-- Pilih --' },
                { value: 'a', text: 'This is option 1' },
                { value: 'b', text: 'This is option 2' },
                { value: 'c', text: 'This is option 3' },
                { value: 'd', text: 'This is option 4' }
            ],
        }
    },
}
</script>