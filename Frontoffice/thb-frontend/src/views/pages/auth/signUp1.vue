<template>
    <div class="v-width row mx-0">
        <div class="col-lg-6 d-none d-lg-flex justify-content-center align-items-center bg-primary-500">
            <img src="../../../@core/assets/images/login/login.png" width="80%">
        </div>
        <div class="col-lg d-flex justify-content-center align-items-center">
            <div class="col-lg-8 d-flex flex-column">
                <!-- LOGO -->
                <div class="d-flex justify-content-center">
                    <img src="../../../@core/assets/images/login/logo_zeemarr.png" width="70px" class="zeemar-logo">
                </div>
                <!-- WIZARD -->
                <wizard :step="active.step" />
                <!-- FORM -->
                <b-form>
                    <div v-if="active.step === 'first'" class="col main-content mt-5">
                        <div>
                            <p class="mb-0 text-bold-18">Select the Product You Want</p>
                            <div class="d-flex flex-row flex-wrap mt-3 mt-md-4 mt-lg-3 cards-gap">
                                <div class='col text-center card-check'>
                                    <input type="radio" v-model="form.product" id="img1" class="d-none imgbgchk" value="HIS">
                                    <label for="img1">
                                        <img src="../../../@core/assets/images/login/icon-his.png" alt="Image 1">
                                        <div class="tick_container">
                                            <div class="tick">
                                                <b-icon icon="check-circle-fill" scale="1.3" />
                                            </div>
                                        </div>
                                        <div class="mt-2 text-white">
                                            <h5>HIS</h5>
                                            <p class="mt-1 mt-lg-2">(Hospital Information System)</p>
                                        </div>
                                    </label>
                                </div>
                                <div class='col text-center card-check'>
                                    <input type="radio" v-model="form.product" id="img2" class="d-none imgbgchk" value="CIS">
                                    <label for="img2">
                                        <img src="../../../@core/assets/images/login/icon-clinic.png" alt="Image 2">
                                        <div class="tick_container">
                                            <div class="tick">
                                                <b-icon icon="check-circle-fill" scale="1.3" />
                                            </div>
                                        </div>
                                        <div class="mt-2 text-white">
                                            <h5>CIS</h5>
                                            <p class="mt-1 mt-lg-2">(Clinic Information System)</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 mt-md-5 mt-lg-4">
                            <p class="mb-0 text-bold-18">Select the Type of Product You Want</p>
                            <b-form-radio-group>
                                <b-form-radio value="HLD" v-model="form.type_product" disabled class="py-1 check">Holding</b-form-radio>
                                <b-form-radio value="STL" v-model="form.type_product" class="py-1 check">Stand Alone</b-form-radio>
                            </b-form-radio-group>
                        </div>
                        <div class="d-flex justify-content-lg-end px-0 mt-4">
                            <b-button @click="goToStep('second')" variant="orange-500" class="col-lg-5">
                                Step 2 Profile Info <b-icon icon="chevron-right" scale="0.9" />
                            </b-button>
                        </div>
                    </div>
                    <!-- /step 1 -->
                    <div v-if="active.step === 'second'">
                        <div class="mt-3 mt-lg-4 c-form">
                            <div>
                                <label>Code</label>
                                <b-form-input v-model="form.code" type="text"></b-form-input>
                            </div>
                            <div>
                                <label>Name of Hospital/Clinic</label>
                                <b-form-input v-model="form.name" type="text"></b-form-input>
                            </div>
                            <div class="mt-12">
                                <label>Phone Number</label>
                                <b-input-group prepend="62">
                                    <b-form-input v-model="form.phone" type="number"></b-form-input>
                                </b-input-group>
                            </div>
                            <div class="mt-12">
                                <label>Email</label>
                                <b-form-input v-model="form.email" type="email"></b-form-input>
                            </div>
                            <div class="mt-12">
                                <label>Password</label>
                                <b-form-input v-model="form.password" type="password"></b-form-input>
                            </div>
                            <div class="mt-12">
                                <label>Verify Password</label>
                                <b-form-input v-model="form.repassword" type="password"></b-form-input>
                            </div>
                        </div>
                        <div class="d-flex justify-content-lg-end mt-4">
                            <b-button type="submit" variant="orange-500" @click.prevent="validationForm" class="col-lg-4">
                                Sign up <b-icon icon="chevron-right" scale="0.9"></b-icon>
                            </b-button>
                        </div>
                    </div>
                    <!-- /step 2 -->
                </b-form>
                <!-- END: FORM -->
            </div>
        </div>
    </div>
</template>

<script>
import wizard from '@/@core/components/auth/wizard.vue';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default {
    components: {
        wizard, ToastificationContent
    },
    data() {
        return {
            active: {
                step: 'first',
            },
            form: {
                id: '',
                product: '',
                type_product: '',
                code: '',
                name: '',
                phone: '',
                email: '',
                password: '',
                repassword: '',
            }
        };
    },
    methods: {
        goToStep(step) {
            this.active.step = step;
        },
        validationForm () {
            // this.$refs.simpleRules.validate()
            // .then(success => {
            //     if (success) {
                const formData = this.form 
                this.$store.dispatch('helper/formPostPut', formData)
                .then(res => {
                    this.modalShow = false
                    this.notification('Success','CheckIcon','success','You have successfully')
                }, 
                err => {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                })
                // } else {
                // this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                // }
            // })
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        }
    },
}
</script>
<style scoped>
@import '../../../@core/css/utility.css';
@import '../../../@core/css/main/main.min.css';
/* @import '../../../@core/css/_layouts/auth.css'; */

.v-width {
    width: 100vw;
    height: 100vh;
}

/* label {
    margin: 0;
} */

.tick_container {
    position: absolute;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    border-radius: 100%;
    cursor: pointer;
    border: 1.5px solid #fff;
    top: 10%;
    left: 15%;
    transform: translate(-50%, -30%);
    -ms-transform: translate(-50%, -30%);
    width: 20px;
    height: 20px;
}

.tick {
    display: flex;
    justify-content: center;
    align-items: center;
    opacity: 0;
    color: white;
    padding: 6px 12px;
    height: 30px;
    width: 30px;
    border-radius: 100%;
}

.imgbgchk:checked+label>.tick_container>.tick {
    opacity: 1;
}

.card-check {
    background: #D758A9;
    border-radius: 10px;
    cursor: pointer;
    padding: 8px;
}

label>img {
    width: 100px;
    height: 71px;
}

.card-check p {
    font-style: normal;
    font-weight: 500;
    font-size: 10px;
    line-height: 100%;
}

.card-check h5 {
    font-style: normal;
    font-weight: 700;
    font-size: 13px;
    line-height: 100%;
}

.cards-gap {
    gap: 15px;
}

.check {
    font-style: normal;
    font-weight: 500;
}

@media (min-width: 768px) {
    .cards-gap {
        gap: 30px;
    }

    .card-check {
        padding: 20px;
    }

    label>img {
        width: 130px;
        height: 93px;
    }

    .tick_container {
        border: 2px solid #fff;
        top: 15%;
        left: 15%;
        width: 25px;
        height: 25px;
    }

    .card-check p {
        font-size: 12px;
    }

    .card-check h5 {
        font-size: 16px;
    }
}

@media (min-width: 992px) {

    .cards-gap {
        gap: 15px;
    }

    .card-check {
        padding: 14px;
    }

    label>img {
        width: 100px;
        height: 71px;
    }
}

@media (min-width: 1400px) {
    .cards-gap {
        gap: 30px;
    }

    .check {
        font-weight: 600;
    }

    .card-check {
        padding: 20px;
    }

    .card-check p {
        font-size: 14px;
    }

    .card-check h5 {
        font-size: 18px;
    }

    label>img {
        width: 130px;
        height: 93px;
    }
}
</style>