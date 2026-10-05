<template>
    <div>
        <div class="" style="border-bottom: 2px solid black;">
            <div class="container text-center py-16">
                <img height="40px" src="./../../../../@core/assets/images/logo-thb.jpg" alt="logo-thb">
                <!-- <img height="40px" src="./../../../../@core/assets/images/lafki-logo.png" alt="lafki-logo" class="px-8"> -->
                <!-- <img height="40px" src="./../../../../@core/assets/images/apexa.png" alt="-logo"> -->
                <div class="font-weight-bold fs-14 mt-10">{{ regData.alias_name }}. {{ regData.name }} | {{localDate(regData.birth_date)}} ({{ parsedAge(regData.birth_date) }}) | {{ regData.norm }} | {{ regData.health_facility_name }} | {{ regData.health_facility_item_name }}</div>
            </div>
        </div>
        <div class="d-flex flex-column justify-content-center align-items-center mt-16">
            <div>
                <VueSignaturePad width="400px" height="200px" ref="signaturePad" style="border: 1px solid black;" />
                <div class="d-flex justify-content-center mt-8">
                    <b-button @click="clearSignature" :disabled="disabled_btn" size="sm" variant="outline-primary" class="px-12 mr-2">BERSIHKAN</b-button>
                    <b-button @click="saveSignature" :disabled="disabled_btn" size="sm" variant="primary-500" class="px-24">SIMPAN</b-button>
                </div>

                <!-- <img :src="imageVariable.src" :width="imageVariable.width" :height="imageVariable.height"> -->
            </div>
            <div class="mt-32 text-center">
                <div class="fs-14">Gambar Tersimpan : {{ imageVariable ? '' : '-' }}</div>
                <img v-if="imageVariable" :src="imageVariable" width="400px" height="200px" class="mt-1 p-1" style="border: 1px dashed black;">
            </div>
        </div>
    </div>
</template>
<script>
import dateMixin from "@/mixins/dateMixin";
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue';

export default {
    mixins: [dateMixin],

    data() {
        return {
            urlbe:process.env.VUE_APP_IMAGE,
            regData: {},
            reg_des_id: '',
            imageVariable: '',
            disabled_btn: false
        }
    },

    methods: {
        clearSignature() {
            this.$refs.signaturePad.clearSignature();
        },

        saveSignature() {
            const { isEmpty, data } = this.$refs.signaturePad.saveSignature();
            this.disabled_btn = true
            // console.log(isEmpty);
            // console.log(data);

            // this.base64ToImageObject(data)
            // .then(result => {
            //     this.imageVariable = result;
            // })
            // .catch(error => {
            //     console.error('Error:', error);
            //     this.disabled_btn = false
            // });

            this.validationSignature(data)
        },

        validationSignature(base64) {
            let obj = {
                signature: base64,
                extension: 'image/png',
                registration_destination_id: this.reg_des_id
            }

            try {
                this.$store.dispatch('registration/putSignature', obj)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.getData(this.reg_des_id)
                        this.clearSignature()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)
            } finally {
                this.disabled_btn = false
            }
        },

        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },

        getData(regDesId) {
            this.$store.dispatch('registration/getById', regDesId).then(data => {
                this.regData = data[0]
                // console.log('this.regData:', this.regData);
                const baseUrl = this.regData?.patient_signature ? `${this.urlbe}${this.regData.patient_signature}`: ''

                // nambahin parameter timestamp biar bisa bypass cache nya
                this.imageVariable = baseUrl + (baseUrl ? `?t=${Date.now()}` : '')
            })
        }

        // base64ToImageObject(base64Data) {
        //     return new Promise((resolve, reject) => {
        //         try {
        //             const base64WithoutHeader = base64Data.split(';base64,').pop();
        //             const img = new Image();
        //             img.src = `data:image/png;base64,${base64WithoutHeader}`;

        //             img.onload = () => {
        //                 resolve({
        //                     imageObject: img,
        //                     width: img.width,
        //                     height: img.height,
        //                     src: img.src
        //                 });
        //             };

        //             img.onerror = (error) => {
        //                 reject(new Error('Gagal memuat gambar: ' + error));
        //             };
        //         } catch (error) {
        //             reject(new Error('Error konversi Base64: ' + error));
        //         }
        //     });
        // },
    },

    created() {
        if (this.$route.params.reg_des_id) {
            this.reg_des_id = this.$route.params.reg_des_id
            this.getData(this.$route.params.reg_des_id)
        }
    },

};
</script>

<style>
@import './../../../../@core/css/main/main.min.css';

</style>
