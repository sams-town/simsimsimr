// verticalMenuMixin.js
export default {
    computed: {
      isVerticalMenuCollapsed: {
        get() {
          return this.$store.state.verticalMenu.isVerticalMenuCollapsed
        },
        set(val) {
          this.$store.commit('verticalMenu/UPDATE_VERTICAL_MENU_COLLAPSED', val)
        }
      },
      collapseTogglerIcon() {
        if (this.isVerticalMenuActive) {
          return this.isVerticalMenuCollapsed ? 'unpinned' : 'pinned'
        }
        return 'close'
      },
      collapseTogglerIconFeather() {
        return this.collapseTogglerIcon === 'unpinned' ? 'CircleIcon' : this.collapseTogglerIcon === 'pinned' ? 'DiscIcon' : 'XIcon'
      }
    },
    data() {
      return {
        isMouseHovered: false,
        // isVerticalMenuActive: true,
      }
    },
    methods: {
      updateMouseHovered(val) {
        this.isMouseHovered = val
      },
      toggleCollapsed() {
        this.isVerticalMenuCollapsed = !this.isVerticalMenuCollapsed
      },
      // toggleVerticalMenuActive() {
      //   this.isVerticalMenuActive = !this.isVerticalMenuActive
      // }
    }
  }
  