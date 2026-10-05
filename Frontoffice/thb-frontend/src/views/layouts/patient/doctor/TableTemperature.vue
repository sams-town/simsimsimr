<template>
  <div style="overflow-x: auto; width: 100%;">
    <table class="table table-sm table-bordered compact-ketuban-table">
      <tr>
        <th>Suhu (°C)</th>
        <td
          v-for="label in labels"
          :key="label"
          @click="isEditing && emitCellClicked(label)"
          :style="{ cursor: isEditing ? 'pointer' : 'default' }"
        >
          {{ getTemperatureValue(label) }}
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
    temperature: Array, // array of { observation_hours, temperature }
    refreshKey: Number,
    isEditing: {
      type: Boolean,
      default: false
    }
  },
  methods: {
    // getTemperature(label) {
    //   const temperature = this.temperature || []
    //   const item = temperature.find(i => i.observation_hours?.slice(0, 5) === label);
    //   return item ? `${item.temperature} °C` : '';
    // },
    getTemperatureValue(label) {
      const arr = this.temperature || [];
      const item = arr.find(i => i.observation_hours?.slice(0, 5) === label);
      return item && item.temperature !== undefined && item.temperature !== null ? item.temperature : '';
    },
    emitCellClicked(label) {
      const arr = this.temperature || [];
      const item = arr.find(i => i.observation_hours?.slice(0, 5) === label);
      this.$emit('cell-clicked', {
        label,
        value: item ? item.temperature : null
      });
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