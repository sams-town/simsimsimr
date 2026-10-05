<template>
    <div class="v-width row mx-0">
        <!-- <img class="wave" src="/img/login/frame_login.png"> -->
            <div class="col-lg-7 d-none d-lg-flex justify-content-center align-items-center bg-primary-500">
      <img src="../../../@core/assets/images/login/grahakara_on black.png" width="40%" />
    </div>
            <div class="col-lg d-flex justify-content-center align-items-center">
                <div class="col d-flex flex-column align-items-center">
                    <div class="col d-flex flex-column justify-content-center align-items-center gap-4">
                        <img src="../../../@core/assets/images/logo-thb.jpg" width="200px" class="thb-logo" />
          <h4 class="font-weight-bold">RS Taman Harapan Baru</h4>
                    </div>
                    <div class="col-lg-10 px-0 justify-content-center align-items-center mt-3 mt-lg-4">
                        <b-form @submit.prevent="login">
                            <div class="mt-3 mt-lg-4">
                                <div>
                                    <label class="c-form-label">Username</label>
                                    <b-form-input v-model="username" placeholder="username"></b-form-input>
                                </div>
                                <div class="c-form mt-12">
                                    <label>Password</label>
                                    <b-input-group class="input-group-merge">
                                        <b-form-input v-model="password" :type="passwordFieldType" placeholder="password"></b-form-input>
                                        <b-input-group-append is-text>
                                            <feather-icon class="cursor-pointer" :icon="passwordToggleIcon" @click="togglePasswordVisibility" />
                                        </b-input-group-append>
                                    </b-input-group>
                                </div>
                                <b-form-checkbox style="display: none;" value="Remember" class="text-dark mt-3"> Remember me</b-form-checkbox>
                            </div>
                            <b-button variant="orange-500" class="col mt-4" type="submit" block>Sign in</b-button>
                        </b-form>
                        <!-- <div class="row mx-0 mt-32">
                            <p class="mb-0 text-regular-16">Don't have an account?</p>
                            <router-link :to="{ name: 'SignupView' }" class="mb-0 text-bold-16 text-orange-500 ml-2">Sign up</router-link>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
</template>

<script lang="js">
import useJwt from '@/auth/jwt/useJwt';
import { getHomeRouteForLoggedInUser } from '@/auth/utils';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue';
import { togglePasswordVisibility } from '@core/mixins/ui/forms';
export default {
    mixins: [togglePasswordVisibility],
    data() {
        return {
            username: '',
            password: '',
        }
    },
    computed: {
        passwordToggleIcon() {
            return this.passwordFieldType === 'password' ? 'EyeIcon' : 'EyeOffIcon'
        },
    },
    methods: {
        login() {
            const lastVisited = JSON.parse(localStorage.getItem("lastVisited"));
            localStorage.clear();
            useJwt.login({
                username: this.username,
                password: this.password
            })
                .then(response => {
                    useJwt.setToken(response.data.data.token)
                    const resLogin = response.data.data
                    useJwt.getProfile().then(response => {
                        const userData =
                        {
                            user_id: response.data.data.user.id,
                            username: response.data.data.user.username,
                            employee_id: response.data.data.employee.id,
                            sip: response.data.data.employee.sip,
                            photo_sm: response.data.data.employee.photo_sm,
                            employee_name: response.data.data.employee.fullname,
                            position_id: response.data.data.employee.position_id,
                            position: {
                                id: response.data.data.employee.position_id,
                                name: response.data.data.employee.position_name,
                            },
                            institute_branch: response.data.data.institute_branch,
                            module: response.data.data.module,
                            user_group: response.data.data.user_group,
                            role: 'Administrator',
                            ability: [
                                {
                                    action: 'manage',
                                    subject: 'all'
                                }
                            ],
                            // response: response.data.data,
                        }
                        localStorage.setItem('sesnotify', 'on')
                        localStorage.setItem('userData', JSON.stringify(userData))
                        this.getNotifications().then(hasNotif => {
                            let directURL = '/';
                            if (hasNotif) {
                                directURL = '/notifications';
                            } else if (lastVisited?.employee_id == response.data.data.employee.id) {
                                directURL = lastVisited.url;
                            }
                            // let directURL = lastVisited?.employee_id == response.data.data.employee.id ? lastVisited.url : '/'
                            this.$router.replace(getHomeRouteForLoggedInUser(userData.role, directURL ))
                                .then(() => {
                                    this.$toast({
                                        component: ToastificationContent,
                                        position: 'top-right',
                                        props: {
                                            title: `Welcome ${userData.employee_name || userData.employee_name}`,
                                            icon: 'CoffeeIcon',
                                            variant: 'success',
                                            text: `You have successfully logged in as ${userData.role}. Now you can start to explore!`,
                                        },
                                    })
                                    // location.reload()
                                    window.location.href = window.location.href
                                })
                                .catch(error => {
                                    this.$refs.loginForm.setErrors(error.response.data.error)
                                })
                        })
                    })
                })
                .catch(error => {
                    console.log(error)
                    if (error) {
                            this.$toast({
                                component: ToastificationContent,
                                position: 'top-right',
                                props: {
                                    title: `Login Failed`,
                                    icon: 'CoffeeIcon',
                                    variant: 'danger',
                                    text: error,
                                }
                            })
                    }else{
                        if (error.response.data) {
                            this.$toast({
                                component: ToastificationContent,
                                position: 'top-right',
                                props: {
                                    title: `Login Failed`,
                                    icon: 'CoffeeIcon',
                                    variant: 'danger',
                                    text: error.response.data.message.error,
                                }
                            })
                        }
                    }
                })
        },
        getNotifications() {
            return this.$store.dispatch('notification/getNotificationAll', { status: 'delivered'})
                .then(response => {
                    if (response && response.length > 0) {
                        return true;
                    } else {
                        return false;
                    }
                })
                .catch(({message, debug, response}) => {
                    console.error('response', response)
                    return false;
                })
        },
    },
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