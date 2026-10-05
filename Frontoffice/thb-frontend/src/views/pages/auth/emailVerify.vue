<template>
    <div class="v-width row mx-0">
        <div class="col-lg-6 d-none d-lg-flex justify-content-center align-items-center bg-primary-500">
            <img src="../../../@core/assets/images/login/login.png" width="80%">
        </div>
        <div class="col-lg d-flex justify-content-center align-items-center">
            <div class="col position-relative">
                <div class="d-flex justify-content-center">
                    <img src="../../../@core/assets/images/login/logo_zeemarr.png" width="70px" class="zeemar-logo">
                </div>
                <div class="mt-3 mt-lg-4 d-flex flex-column align-items-center text-center">
                    <h3 class="mb-0 text-bold-36">Verify your email</h3>
                    <p class="mt-3 mt-lg-2 mb-0 text-medium-18">You will need to verify your email to complete registration
                    </p>
                    <img class="mt-2" width="60%" src="../../../@core/assets/images/login/verify_email.png">
                    <p class="mt-3 mt-lg-2 mb-0 text-medium-16">Verify code has sent to your email <b>xxx@email.com</b></p>
                    <p class="mt-1 mb-0 text-medium-16">Please verify this email address by clicking button below</p>
                    <div class="w-100 d-lg-flex justify-content-lg-center mt-20 mt-lg-32">
                        <b-button variant="orange-500" type="submit" class="col-lg-4">Verify your Email</b-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script >
export default {

}
</script>
<style scoped>
@import '../../../@core/css/utility.css';
@import '../../../@core/css/main/main.min.css';

.v-width {
    width: 100vw;
    height: 100vh;
}
</style>