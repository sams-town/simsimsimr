<template>
  <div style="overflow-x: auto; width: 100%;">
    <table class="table table-sm table-bordered compact-ketuban-table">
      <tr>
        <th>Obat dan Cairan IV</th>
        <td v-for="label in labels" :key="label"
          @click="isEditing && $emit('cell-clicked', { label, value: getIVFluidsMedsValue(label) })"
          :style="{ cursor: isEditing ? 'pointer' : 'default' }"
        >
          {{ getIVFluidsMeds(label) }}
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
    labels: Array, // observation hours
    IVFluidsMeds: Array, // array of { observation_hours, iv_fluids_meds }
    refreshKey: Number,
    isEditing: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      IVFluidsMedsInitialMap: {
        1: 'OXY',
        2: 'RL',
        3: 'NS',
        4: 'MTG',
        5: 'AMP',
        6: 'PCM',
        7: 'SPM',
        8: 'DEX',
        9: 'MgSO₄'
      }
    }
  },
  methods: {
    // getTemperature(label) {
    //   const temperature = this.temperature || []
    //   const item = temperature.find(i => i.observation_hours?.slice(0, 5) === label);
    //   return item ? `${item.temperature} °C` : '';
    // },
    getIVFluidsMeds(label) {
        const IVFluidsMeds = this.IVFluidsMeds || [];
        const item = IVFluidsMeds.find(i => i.observation_hours?.slice(0, 5) === label);
        return item ? this.IVFluidsMedsInitialMap[item.iv_fluids_meds] || '' : '';
    },

    getIVFluidsMedsValue(label) {
      const arr = this.IVFluidsMeds || [];
      const item = arr.find(i => i.observation_hours?.slice(0, 5) === label);
      return item ? item.iv_fluids_meds : null;
    }
  },
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