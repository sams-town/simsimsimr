<template>
  <div class="main-menu menu-fixed menu-accordion menu-shadow" :class="[
      { 'expanded': !isVerticalMenuCollapsed || (isVerticalMenuCollapsed && isMouseHovered) },
      skin === 'semi-dark' ? 'menu-dark' : 'menu-light'
    ]" @mouseenter="updateMouseHovered(true)" @mouseleave="updateMouseHovered(false)">
    <div class="main-menu-content">
      <aside id="leftsidebar" class="sidebar"  :class="{ collapsed: (isVerticalMenuCollapsed && !isMouseHovered) }">
        <div class="menu">
          <ul class="list">
            <li>
              <div class="user-info">
                <!-- Header / Logo + Toggle -->
                <div class="navbar-header expanded mb-10">
                  <slot
                    name="header"
                    :toggleVerticalMenuActive="toggleVerticalMenuActive"
                    :toggleCollapsed="toggleCollapsed"
                    :collapseTogglerIcon="collapseTogglerIcon"
                  >
                    <ul class="nav navbar-nav flex-row">
                      <li class="nav-item mr-auto">
                        <div class="d-flex align-items-center justify-content-between">

                          <!-- Logo + Hospital Name -->
                          <div class="d-flex flex-column align-items-start">
                            <b-img
                              src="../../@core/assets/images/login/grahakara_color on dark bg.png"
                              width="180%"
                              class="img-fluid mb-1"
                            />
                          </div>

                          <!-- Collapse / Close Toggle -->
                          <div class="nav-toggle ml-auto">
                            <b-link class="nav-link modern-nav-toggle">
                              <feather-icon
                                icon="XIcon"
                                size="20"
                                class="d-block d-xl-none"
                                @click="toggleVerticalMenuActive"
                              />
                              <feather-icon
                                :icon="collapseTogglerIconFeather"
                                size="20"
                                class="d-none d-xl-block collapse-toggle-icon"
                                @click="toggleCollapsed"
                              />
                            </b-link>
                          </div>

                        </div>
                      </li>
                    </ul>
                  </slot>
                </div>

                <!-- User Avatar -->
                <div class="user-avatar">
                  <h6 class="mb-0">RS Taman Harapan Baru</h6>
                  <img
                    :src="avatarSrc"
                    alt="User Avatar"
                  />
                </div>

                <!-- User Details -->
                <div class="user-detail">
                  <h4>{{ userData.employee_name }}</h4>
                  <small>{{ userData.user_group.name }}</small>
                  <small class="app-version">v1.4.0</small>
                </div>

              </div>
            </li>
          </ul>
        </div>
        <div class="menu">
          <div class="scroll-container">
            <div class="list all_menus">
              <div class="menu-search px-2 mb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <feather-icon icon="SearchIcon" size="16" />
                    </span>
                  </div>

                  <input
                    v-model="searchMenu"
                    class="form-control"
                    placeholder="Cari modul..."
                  >
                </div>
              </div>
              <!-- <template  v-if="userData.user_group.name !== 'Doctor'"> -->
                 <li
                  v-for="rsidebar in filteredModules"
                  :key="rsidebar.name"
                  class="firstLevel rounded"
                  :class="{ 'bg-indigo': $route.name === rsidebar.url }"
                >
                  <router-link :to="{ name: rsidebar.url }">
                    <img :src="require(`@/@core/assets/icons/sidebar/${rsidebar.icon}`)" alt="">
                    <span
                      class="text-bold-14"
                      :class="$route.name === rsidebar.url ? 'text-neutral-100' : 'text-neutral-700'"
                    >
                      {{ rsidebar.name }}
                    </span>
                  </router-link>
                </li>
              <!-- <li class="firstLevel rounded bg-indigo">
                <router-link :to="{ name: 'MedicalServicesDoctorView' }">
                  <img :src="require(`@/@core/assets/icons/sidebar/dashboard.svg`)" alt="">
                  <span class="text-bold-14 text-neutral-100">Jasa Dokter</span>
                </router-link>
              </li> -->
              <!-- </template> -->
              <!-- <template v-else>
                <li class="firstLevel rounded" :class="{ 'bg-indigo' : $route.name === 'QueueNurseRJView' }">
                  <router-link :to="{ name: 'QueueNurseRJView' }">
                    <img :src="require('@/@core/assets/icons/sidebar/dashboard.svg')" alt="">
                    <span class="text-bold-14" :class="$route.name === 'QueueNurseRJView' ? 'text-neutral-100' : 'text-neutral-700'">Rawat Jalan</span>
                  </router-link>
                </li>
                <li class="firstLevel rounded" :class="{ 'bg-indigo' : $route.name === 'QueueNurseINView' }">
                  <router-link :to="{ name: 'QueueNurseINView' }">
                    <img :src="require('@/@core/assets/icons/sidebar/dashboard.svg')" alt="">
                    <span class="text-bold-14" :class="$route.name === 'QueueNurseINView' ? 'text-neutral-100' : 'text-neutral-700'">Rawat Inap</span>
                  </router-link>
                </li>
                <li class="firstLevel rounded" :class="{ 'bg-indigo' : $route.name === 'QueueNurseIGDView' }">
                  <router-link :to="{ name: 'QueueNurseIGDView' }">
                    <img :src="require(`@/@core/assets/icons/sidebar/dashboard.svg`)" alt="">
                    <span class="text-bold-14" :class="$route.name === 'QueueNurseIGDView' ? 'text-neutral-100' : 'text-neutral-700'">IGD</span>
                  </router-link>
                </li>
                <li class="firstLevel rounded" :class="{ 'bg-indigo' : $route.name === 'DoctorReferenceView' }">
                  <router-link :to="{ name: 'DoctorReferenceView' }">
                    <img :src="require(`@/@core/assets/icons/sidebar/dashboard.svg`)" alt="">
                    <span class="text-bold-14" :class="$route.name === 'DoctorReferenceView' ? 'text-neutral-100' : 'text-neutral-700'">Referensi</span>
                  </router-link>
                </li>
              </template> -->
              <!-- <template v-for="rsidebar in userData.module">
                <li class="firstLevel rounded" :class="{ 'bg-indigo' : $route.name === rsidebar.url }">
                  <router-link :to="{ name: rsidebar.url }">
                    <img :src="require(`@/@core/assets/icons/sidebar/${rsidebar.icon}`)" alt="">
                    <span class="text-bold-14" :class="$route.name === rsidebar.url ? 'text-neutral-100' : 'text-neutral-700'">{{rsidebar.name}}</span>
                  </router-link>
                </li>
              </template> -->
              <!-- /////////////////////////// -->
              <!-- <li class="firstLevel">
                  <router-link :to="{ name: 'CanvasView' }" class="d-flex">
                    <img src="@/@core/assets/icons/sidebar/registration.svg" alt="">
                    <span class="text-bold-14 text-neutral-700">Canvasing</span>
                    <b-icon icon="chevron-down" class="ml-auto my-auto"></b-icon>
                  </router-link>
                </li> -->
            </div>
          </div>
        </div>
      </aside>
    </div>
  </div>
</template>

<script>
import defaultAvatar from '@/@core/assets/images/profile_av.jpg';
import verticalMenuMixin from '@/mixins/verticalMenuMixin';

  export default {
    name: 'SideBar',
    mixins: [verticalMenuMixin],
    data() {
      return {
        searchMenu: '',
        userData: JSON.parse(localStorage.getItem('userData')),
        urlbe: process.env.VUE_APP_IMAGE,
        menus: {
          menu1: false,
          menu2: false,
          menu3: false,
          menu4: false,
          menu5: false,
          menu6: false,
          menu7: false,
          menu8: false,
          menu9: false,
          menu10: false,
          menu11: false,
          menu12: false,
          menu13: false,
          menu14: false,
          menu15: false,
          menu16: false,
          menu17: false,
          menu18: false,
          menu19: false,
          menu20: false,
          menu21: false,
          menu22: false,
        },
        submenus: {
          submenu1: false,
          submenu2: false,
          submenu3: false,
          submenu4: false,
          submenu5: false,
          submenu6: false,
          submenu7: false,
          submenu8: false,
          submenu9: false,
          submenu10: false,
          submenu11: false,
          submenu12: false,
          submenu13: false,
          submenu14: false,
          submenu15: false,
          submenu16: false,
          submenu17: false,
          submenu18: false,
          submenu221: false,
        },
        active: {
          firstLevel: null,
          secondLevelVisible: false
        },
        skin:'menu-light',
      }
    },
    props: {
      selected_sidebar: {
        type: String,
        default: 'menu1'
      },
      selected_sidebar_submenu: {
        type: String,
        default: 'submenu0'
      },
      isVerticalMenuActive: {
        type: Boolean,
        required: true,
      },
      toggleVerticalMenuActive: {
        type: Function,
        required: true,
      },
    },
    computed: {
    avatarSrc() {
      if (this.userData.photo_sm) {
        return this.urlbe + this.userData.photo_sm
      }
      return defaultAvatar
    },
    avatarSrc() {
      if (this.userData.photo_sm) {
        return this.urlbe + this.userData.photo_sm
      }
      return defaultAvatar
    },

    filteredModules() {
      if (!this.searchMenu) {
        return this.userData.module
      }

      return this.userData.module.filter(menu =>
        menu.name.toLowerCase().includes(this.searchMenu.toLowerCase())
      )
    }

  },
    mounted() {
      if (this.menus.hasOwnProperty(this.selected_sidebar)) {
        this.menus[this.selected_sidebar] = true;
      }
      if (this.submenus.hasOwnProperty(this.selected_sidebar_submenu)) {
        this.submenus[this.selected_sidebar_submenu] = true;
      }
    },
    methods: {
      toggleSecondLevel(menu) {
        if (this.active.firstLevel === menu && this.active.secondLevelVisible) {
          this.active.secondLevelVisible = false;
        } else {
          this.active.firstLevel = menu;
          this.active.secondLevelVisible = true;
        }
      }
    }
  }
</script>

<style scoped>
  li.firstLevel:hover,
  .firstLevel:hover {
    background-color: #e3fcfc;
  }

  .secondLevel:hover {
    background-color: #ffebd5;
  }

  .box:hover {
    cursor: pointer;
  }

  .main-menu.menu-light {
    color: black;
  }

  .user-info {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 15px;
}

.user-avatar img {
  width: 50%;
  border-radius: 50%;
  display: block;
  margin: 0 auto;
  margin-top: 15px;
}

.user-detail {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 2px;
}

.user-detail h4 {
  margin: 0;
}

.app-version {
  font-size: 12px;
  margin-top: 4px;
  opacity: 0.6;
}
.menu-search {
  padding: 0 12px;
}

.menu-search input {
  border-radius: 20px;
  font-size: 14px;
}

.menu-search input:focus {
  box-shadow: none;
  border-color: #16c5c5;
}
</style>

<style lang="scss">
@import "~@core/scss/base/core/menu/menu-types/vertical-menu.scss";
</style>
