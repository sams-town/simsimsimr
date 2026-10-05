export default {
    computed: {
      isVerticalMenuCollapsed: {
        get() {
          return this.$store.state.verticalMenu.isVerticalMenuCollapsed
        },
        set(val) {
          this.$store.commit('verticalMenu/UPDATE_VERTICAL_MENU_COLLAPSED', val)
        },
      },
  
      isRTL: {
        get() {
          return this.$store.state.appConfig.layout.isRTL
        },
        set(val) {
          this.$store.commit('appConfig/TOGGLE_RTL', val)
        },
      },
  
      skin: {
        get() {
          return this.$store.state.appConfig.layout.skin
        },
        set(val) {
          this.$store.commit('appConfig/UPDATE_SKIN', val)
        },
      },
  
      skinClasses() {
        if (this.skin === 'bordered') return 'bordered-layout'
        if (this.skin === 'semi-dark') return 'semi-dark-layout'
        return null
      },
  
      routerTransition: {
        get() {
          return this.$store.state.appConfig.layout.routerTransition
        },
        set(val) {
          this.$store.commit('appConfig/UPDATE_ROUTER_TRANSITION', val)
        },
      },
  
      layoutType: {
        get() {
          return this.$store.state.appConfig.layout.type
        },
        set(val) {
          this.$store.commit('appConfig/UPDATE_LAYOUT_TYPE', val)
        },
      },
  
      contentWidth: {
        get() {
          return this.$store.state.appConfig.layout.contentWidth
        },
        set(val) {
          this.$store.commit('appConfig/UPDATE_CONTENT_WIDTH', val)
        },
      },
  
      isNavMenuHidden: {
        get() {
          return this.$store.state.appConfig.layout.menu.hidden
        },
        set(val) {
          this.$store.commit('appConfig/UPDATE_NAV_MENU_HIDDEN', val)
        },
      },
  
      navbarBackgroundColor: {
        get() {
          return this.$store.state.appConfig.layout.navbar.backgroundColor
        },
        set(val) {
          this.$store.commit('appConfig/UPDATE_NAVBAR_CONFIG', { backgroundColor: val })
        },
      },
  
      navbarType: {
        get() {
          return this.$store.state.appConfig.layout.navbar.type
        },
        set(val) {
          this.$store.commit('appConfig/UPDATE_NAVBAR_CONFIG', { type: val })
        },
      },
  
      footerType: {
        get() {
          return this.$store.state.appConfig.layout.footer.type
        },
        set(val) {
          this.$store.commit('appConfig/UPDATE_FOOTER_CONFIG', { type: val })
        },
      },
    },
  }
  