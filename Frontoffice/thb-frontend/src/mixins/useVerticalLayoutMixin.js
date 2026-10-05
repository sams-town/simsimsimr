// import store from '@/store'

export default {
  data() {
    return {
      // State for vertical menu visibility and breakpoint
      isVerticalMenuActive: true,
      currentBreakpoint: 'xl',
    }
  },
  computed: {
    // Computed for checking if the vertical menu is collapsed
    isVerticalMenuCollapsed() {
      return this.$store.state.verticalMenu.isVerticalMenuCollapsed
    },

    // Layout classes based on the breakpoint and menu state
    layoutClasses() {
      const classes = []

      // console.log('this.currentBreakpoint', this.currentBreakpoint)
      if (this.currentBreakpoint === 'xl') {
        classes.push('vertical-menu-modern')
        // console.log('this.isVerticalMenuCollapsed', this.isVerticalMenuCollapsed)
        classes.push(this.isVerticalMenuCollapsed ? 'menu-collapsed' : 'menu-expanded')
      } else {
        classes.push('vertical-overlay-menu')
        // console.log('this.isVerticalMenuActive', this.isVerticalMenuActive)
        classes.push(this.isVerticalMenuActive ? 'menu-open' : 'menu-hide')
      }

      // console.log('layoutclasses', classes)

      // Add navbar type class
      classes.push(`navbar-${this.navbarType}`)

      // Add footer type class
      if (this.footerType === 'sticky') classes.push('footer-fixed')
      if (this.footerType === 'static') classes.push('footer-static')
      if (this.footerType === 'hidden') classes.push('footer-hidden')

      return classes
    },

    // Overlay classes for vertical menu on smaller breakpoints
    overlayClasses() {
      if (this.currentBreakpoint !== 'xl' && this.isVerticalMenuActive) return 'show'
      return null
    },

    // Navbar type class (sticky, static, or hidden)
    navbarTypeClass() {
      if (this.navbarType === 'sticky') return 'fixed-top'
      if (this.navbarType === 'static') return 'navbar-static-top'
      if (this.navbarType === 'hidden') return 'd-none'
      return 'floating-nav'
    },

    // Footer type class (static or hidden)
    footerTypeClass() {
      if (this.footerType === 'static') return 'footer-static'
      if (this.footerType === 'hidden') return 'd-none'
      return ''
    },
  },
  watch: {
    currentBreakpoint(val) {
      this.isVerticalMenuActive = val === 'xl'
    }
  },
  methods: {
    // Toggle the visibility of the vertical menu
    toggleVerticalMenuActive() {
      // console.log(this.isVerticalMenuActive, 'this.isVerticalMenuActive')
      this.isVerticalMenuActive = !this.isVerticalMenuActive
      // console.log(this.isVerticalMenuActive, 'this.isVerticalMenuActive')

    },

    // Resize handler to adjust the breakpoint
    resizeHandler() {
      if (window.innerWidth >= 1200) this.currentBreakpoint = 'xl'
      else if (window.innerWidth >= 992) this.currentBreakpoint = 'lg'
      else if (window.innerWidth >= 768) this.currentBreakpoint = 'md'
      else if (window.innerWidth >= 576) this.currentBreakpoint = 'sm'
      else this.currentBreakpoint = 'xs'
    },
  },
  //bagaimana cara menjalankan ini di file vue berbeda
  props: {
    // navbarType: {
    //   type: String,
    //   required: true,
    //   default:'floating',
    // },
    // footerType: {
    //   type: String,
    //   required: true,
    //   default:'static',
    // },
  },
}
