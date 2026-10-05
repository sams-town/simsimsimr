<template>
 <nav class="navbar p-l-5 p-r-5 py-3 shadow">
    <ul class="nav navbar-nav navbar-left w-100">
      <li class="d-flex flex-wrap justify-content-between align-items-center w-100" :class="{ 'navbar-expanded': !isVerticalMenuCollapsed, 'navbar-collapsed': isVerticalMenuCollapsed }">
        <!-- <div class="text-white fs-24 font-weight-bold">{{ head_title }}</div> -->
        <!-- Hamburger for small screens -->
        <ul class="nav navbar-nav d-xl-none mr-2">
          <li class="nav-item">
            <b-link class="nav-link d-flex align-items-center" @click="toggleVerticalMenuActive">
              <feather-icon icon="MenuIcon" size="16" />
            </b-link>
          </li>
        </ul>

        <!-- Breadcrumb navigation -->
        <nav aria-label="breadcrumb" class="flex-grow-1">
          <ul class="custom-breadcrumb m-0 d-flex align-items-center">
            <!-- Breadcrumb items -->
            <li
              v-for="(item, index) in breadcrumb_items"
              :key="index"
              class="custom-breadcrumb-item"
              :class="{ 'is-active': index === breadcrumb_items.length - 1, 'no-link': !item.link && index !== breadcrumb_items.length - 1 }"
            >
              <template v-if="item.link && index !== breadcrumb_items.length - 1">
                <a :href="item.link">{{ item.text }}</a>
              </template>
              <template v-else>
                <span>{{ item.text }}</span>
              </template>
            </li>
          </ul>
        </nav>

        <!-- Right-side icons -->
        <div class="d-flex align-items-center pr-3">
          <div class="mr-3">
            <vnotification
              :notifications="notifications" 
              :hideRead="true"
              @refreshnotif="getNotifications" 
            />
          </div>
          <a @click.prevent="logout">
            <feather-icon icon="PowerIcon" size="18" color="white" class="pointer"/>
          </a>
        </div>
      </li>
    </ul>
  </nav>
</template>


<script>
import { initialAbility } from '@/libs/acl/config'
import useJwt from '@/auth/jwt/useJwt'
// import Locale from '@core/layouts/components/app-navbar/components/Locale.vue'
// import DarkToggler from '@core/layouts/components/app-navbar/components/DarkToggler.vue'
// import * as PusherPushNotifications from '@pusher/push-notifications-web'
import vnotification from '@core/components/notification/notification-v.vue'
import io from 'socket.io-client';
export default {
  components:{
    vnotification,
  },  
  props: {
    breadcrumb_items: {
      type: Array,
      // default: () => [
      //   { text: "Home", link: "/" },
      //   { text: 'Products', link: '/products' },
      //   { text: 'Laptops', link: '/products/laptops' },
      //   { text: "Default Page" }
      // ]
    },
    toggleVerticalMenuActive: {
      type: Function,
      default: () => {},
    },
  },
  computed: {
    isVerticalMenuCollapsed: {
      get() {
        return this.$store.state.verticalMenu.isVerticalMenuCollapsed
      },
      set(val) {
        this.$store.commit('verticalMenu/UPDATE_VERTICAL_MENU_COLLAPSED', val)
      }
    },
  },
  data(){
    return {
      socket: io(process.env.VUE_APP_SOCKET_IO_NOTIF, {
        path: '/socket.io',
        transports: ['websocket', 'polling', 'flashsocket'],
        withCredentials: true
      }),
      notifications:[],
      nutritionists_position: '8ca42f62-85bb-45f8-bf32-152a792e0f40',
    }
  },
  methods: {
    logout() {
      // const beamsClient = new PusherPushNotifications.Client({
      //   instanceId: process.env.VUE_APP_PUSHER,
      // })
      // // beamsClient.clearAllState()
      // .then(() => console.log('Beams state has been cleared'))
      // .catch(e => console.error('Could not clear Beams state', e))
      // Remove userData from localStorage
      // ? You just removed token from localStorage. If you like, you can also make API call to backend to blacklist used token
      localStorage.removeItem(useJwt.jwtConfig.storageTokenKeyName)
      localStorage.removeItem(useJwt.jwtConfig.storageRefreshTokenKeyName)

      let userData = JSON.parse(localStorage.getItem('userData'))

      // Remove userData from localStorage
      localStorage.removeItem('userData')
      localStorage.clear();
      
      const currentURL = this.$route.fullPath;
      let myObj = {
        url: currentURL, 
        employee_id: userData?.employee_id
      }

      if (currentURL !== "/login") {
        localStorage.setItem("lastVisited", JSON.stringify(myObj));
      }

      // Reset ability
      // this.$ability.update(initialAbility)

      // Redirect to login page
      this.$router.push({ name: 'login' })
    },
    async getNotifications(){
      try {                
          await this.$store.dispatch('notification/getNotificationAll', { status: 'all'})
          .then(
              response => {
                  if(response) this.notifications = response
                  // console.log('response', response);
              }
          )
          .catch(({message, debug, response}) => {
              console.error('response', response)                        
          })
      } catch (error) {
          console.error('Error catch while GET notification/getNotificationAll at: ', error);
      }
    },
  },
  mounted(){
    this.socket.on('SBAR', (obj) => {
      // console.log('socket notif', obj)
      let userData = JSON.parse(localStorage.getItem('userData'))

      if (userData.position.id == this.nutritionists_position) this.getNotifications()
      if (obj.type == 'VERIF_SBAR' && obj?.doctor_in_charge_id == userData.employee_id) this.getNotifications()
      if ((obj.type == 'REFRESH_DATA' || obj.type == 'CONSULTATION_KARDEX') && (obj?.doctor_in_charge_id == userData.employee_id || obj?.position_id == userData.position.id)) this.getNotifications()
      if ((obj.type == 'PATIENT_TRANSFER' || obj.type == 'LAB_RESULT' || obj.type == 'RAD_RESULT') && obj?.position_id == userData.position.id) this.getNotifications()
    })
  },
  created(){
    this.getNotifications()
  }
}
</script>

<style scoped>
.custom-breadcrumb {
  display: flex;
  list-style: none;
  padding: 0;
  border-radius: 4px;
  font-size: 14px;
  letter-spacing: 0.5px;
  color: #fff;
}

.custom-breadcrumb-item {
  margin-right: 8px;
  text-transform: uppercase;
}

.custom-breadcrumb-item::after {
  content: ">";
  margin-left: 8px;
  /* color: #fff; */
}

.custom-breadcrumb-item:last-child::after {
  content: "";
}

/* Jika item memiliki link */
.custom-breadcrumb-item a {
  text-decoration: none;
  color: #fff;
}

.custom-breadcrumb-item a:hover {
  text-decoration: underline;
}

/* Item terakhir (aktif) */
.custom-breadcrumb-item.is-active {
  font-weight: 600;
  /* color: #fff; */
}

/* Item yang bukan terakhir tetapi tidak memiliki link */
.custom-breadcrumb-item.no-link {
  font-weight: normal;
  /* color: #fff; */
}

/* Agar item yang tidak memiliki link tidak dapat di-hover */
.custom-breadcrumb-item.no-link:hover {
  cursor: default;
  text-decoration: none;
  /* color: #fff; */
}

/* BY V */
.navbar-expanded {
  padding-left: 270px;
  -webkit-transition: 300ms ease all;
  transition: 300ms ease all;
}

@media (max-width: 1199.98px) {
  .navbar-expanded {
    padding-left: 1rem !important;
  }
  .navbar-collapsed {
    padding-left: 1rem !important;
  }
}

.navbar-collapsed {
  padding-left: 100px;
  -webkit-transition: 300ms ease all;
  transition: 300ms ease all;
}

</style>
