<template>
  <div class="toastification">
    <div class="d-flex align-items-start">
      <b-avatar
        :variant="variant"
        size="1.8rem"
        class="mr-75 flex-shrink-0"
      >
        <feather-icon
          :icon="icon"
          size="15"
        />
      </b-avatar>
      <div class="d-flex flex-grow-1">
        <div>
          <h5
            v-if="title"
            class="mb-0 font-weight-bolder toastification-title"
            :class="`text-${variant}`"
            v-text="title"
          />
          <b-row>
            <b-col>
              <small
                v-if="text"
                class="d-inline-block text-body"
                v-text="text"
              />
            </b-col>
            <b-col>
              <b-button @click="link(to)" variant="relief-primary">
                Open!
              </b-button>
            </b-col>
          </b-row>
        </div>
        <span
          class="cursor-pointer toastification-close-icon ml-auto "
          @click="$emit('close-toast')"
        >
          <feather-icon
            v-if="!hideClose"
            icon="XIcon"
            class="text-body"
          />
        </span>
      </div>
    </div>
  </div>
</template>

<script>
import { BAvatar, BCol, BRow, BButton } from 'bootstrap-vue'
import router from '@/router/index.js'

export default {
  components: {
    BAvatar, BCol, BRow, BButton
  },
  props: {
    variant: {
      type: String,
      default: 'primary',
    },
    icon: {
      type: String,
      default: null,
    },
    title: {
      type: String,
      default: null,
    },
    text: {
      type: String,
      default: null,
    },
    to: {
      type: String,
      default: null,
    },
    hideClose: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    link (to) {
      router.push({ name: to })
    }
  }
}
</script>

<style lang="scss" scoped>
.toastification-close-icon,
.toastification-title {
  line-height: 26px;
}

.toastification-title {
  color: inherit;
}
</style>
