<template>
    <div style="overflow-x: auto; width: 100%;">
      <table class="table table-sm table-bordered compact-ketuban-table">
        <tr>
          <th>Oksitosin U/L</th>
          <td v-for="label in labels" :key="label"
            @click="isEditing && $emit('cell-clicked', { label, type: 'oxytocin', value: getOxytocin(label), dripRate: getOxytocinDripRate(label) })"
            :style="{ cursor: isEditing ? 'pointer' : 'default' }"
          >
            {{ getOxytocin(label) }}
          </td>
        </tr>
        <tr>
          <th>Tetes / menit</th>
          <td v-for="label in labels" :key="label"
            @click="isEditing && $emit('cell-clicked', { label, type: 'oxytocin', value: getOxytocin(label), dripRate: getOxytocinDripRate(label) })"
            :style="{ cursor: isEditing ? 'pointer' : 'default' }"
          >
            {{ getOxytocinDripRate(label) }}
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
import { removeMatching } from '@/@core/assets/plugins/fullcalendar/fullcalendar';

export default {
  props: {
    labels: Array, // observation hours
    oxytocin: Array, 
    // infiltration: Array,
    oxytocinDripRate: Array,
    refreshKey: Number,
    isEditing: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    fields() {
      return [
        { key: 'observation_hours', label: 'Jam Observasi' },
        { key: 'oxytocin', label: 'Oksitosin U/L' },
        { key: 'oxytocinDripRate', label: 'Tetes / menit' },

      ]
    },
  },
  methods: {
    getOxytocin(label) {
        const oxytocinArr = this.oxytocin || [];
        const item = oxytocinArr.find(i => i.observation_hours?.slice(0, 5) === label);
        return item && item.oxytocin !== undefined && item.oxytocin !== null ? item.oxytocin : '';
    },
    getOxytocinDripRate(label) {
        const oxytocinArr = this.oxytocin || [];
        const item = oxytocinArr.find(i => i.observation_hours?.slice(0, 5) === label);
        return item && item.drops !== undefined && item.drops !== null ? item.drops : '';
    }
  },
  mounted() {
    // console.log('amniotic_fluid received in child:', this.amniotic_fluid);
    // console.log('infiltration received in child:', this.infiltration);
  },
  watch: {
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