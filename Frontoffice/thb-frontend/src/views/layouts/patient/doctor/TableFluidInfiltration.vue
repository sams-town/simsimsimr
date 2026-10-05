<template>
    <div style="overflow-x: auto; width: 100%;">
      <table class="table table-sm table-bordered compact-ketuban-table">
        <tr>
          <th>Air Ketuban</th>
          <td v-for="label in labels" :key="'fluid-' + label"
          @click="isEditing && $emit('cell-clicked', { type: 'amniotic_fluid', label, value: getFluid(label) })"
          :style="{ cursor: isEditing ? 'pointer' : 'default' }"
          >
            {{ getFluid(label) }}
          </td>
        </tr>
        <tr>
          <th>Penyusupan</th>
          <td v-for="label in labels" :key="'infiltration-' + label"
          @click="isEditing && $emit('cell-clicked', { type: 'infiltration', label, value: getInfiltration(label) })"
          :style="{ cursor: isEditing ? 'pointer' : 'default' }"
          >
            {{ getInfiltration(label) }}
          </td>
        </tr>
        <tr>
          <th>Jam Observasi</th>
          <th v-for="label in labels" :key="label">{{ label }}</th>
        </tr>
      </table>
    </div>
</template>

<script>

export default {
  props: {
    partographId:{
      type: [Number, String],
      required: true
    },
    labels: Array, // observation hours
    isEditing: {
      type: Boolean,
      default: false
    },
  },
  data() {
    return {
      amnioticFluids: [],
      infiltration: []
    }
  },
  // computed: {
  //   fields() {
  //     return [
  //       { key: 'observation_hours', label: 'Jam Observasi' },
  //       { key: 'amniotic_fluid', label: 'Air Ketuban' },
  //       { key: 'infiltration', label: 'Penyusupan' },

  //     ]
  //   },
  // },
  methods: {
    async fetchPartographDetail() {
      if (!this.partographId) return;
      try {
        const data = await this.$store.dispatch('partograph/getPartographDetail', {
          partographId: this.partographId
        });
        const partograph = data.data && data.data[0] ? data.data[0] : {};
        this.amnioticFluids = partograph.amniotic_fluid || [];
        this.infiltration = partograph.infiltration || [];
      } catch (e) {
        this.amnioticFluids = [];
        this.infiltration = [];
      }
    },
    getFluid(label) {
      const amnioticFluidCodeMap = {
        1: 'U', // Utuh
        2: 'J', // Jernih
        3: 'M', // Mekonium
        4: 'D', // Darah
        5: 'K', // Kering
      };
      const item = this.amnioticFluids.find(i => i.observation_hours?.slice(0, 5) === label);
      const code = amnioticFluidCodeMap[Number(item?.amniotic_fluid)];
      return code || '';``
    },

    getInfiltration(label) {
      const item = this.infiltration.find(i => i.observation_hours?.slice(0, 5) === label);
      return item?.infiltration;
    }
  },
  mounted() {
    // console.log('amniotic_fluid received in child:', this.amniotic_fluid);
    // console.log('infiltration received in child:', this.infiltration);
  },
  watch: {
    partographId: {
      immediate: true,
      handler() {
        this.fetchPartographDetail();
      }
    }
  }
}
</script>

<style scoped>
    .compact-ketuban-table {
        font-size: 11px;
        table-layout: fixed;
    }
    .compact-ketuban-table th,
    .compact-ketuban-table td {
        padding: 2px 4px !important;
        text-align: center;
        vertical-align: middle;
    }
    .compact-ketuban-table th {
    font-weight: normal !important;
}
</style>