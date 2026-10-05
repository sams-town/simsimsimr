<template>
  <div style="overflow-x: auto; width: 100%;">
    <table class="table table-sm table-bordered compact-ketuban-table">
      <tr>
        <th>Protein</th>
        <td
          v-for="label in labels"
          :key="'protein-' + label"
          @click="isEditing && emitCellClicked(label)"
          :style="{ cursor: isEditing ? 'pointer' : 'default' }"
        >
          {{ getProtein(label) }}
        </td>
      </tr>
      <tr>
        <th>Aseton</th>
        <td
          v-for="label in labels"
          :key="'acetone-' + label"
          @click="isEditing && emitCellClicked(label)"
          :style="{ cursor: isEditing ? 'pointer' : 'default' }"
        >
          {{ getAcetone(label) }}
        </td>
      </tr>
      <tr>
        <th>Volume (cc)</th>
        <td
          v-for="label in labels"
          :key="'volume-' + label"
          @click="isEditing && emitCellClicked(label)"
          :style="{ cursor: isEditing ? 'pointer' : 'default' }"
        >
          {{ getVolume(label) }}
        </td>
      </tr>
      <tr>
        <th>Jam Observasi</th>
        <th v-for="label in labels" :key="'jam-' + label">{{ label }}</th>
      </tr>
    </table>
  </div>
</template>

<script>

export default {
  props: {
    labels: Array, // observation hours
    urine: Array, // array of { observation_hours, urine }
    refreshKey: Number,
    isEditing: {
      type: Boolean,
      default: false
    }
  },
  methods: {
    getProtein(label) {
        const urine = this.urine || []
        const item = urine.find(i => i.observation_hours?.slice(0, 5) === label)
        if (item && item.protein !== undefined && item.protein !== null) {
            switch (Number(item.protein)) {
                case 0: return '-'
                case 1: return '+'
                case 2: return '++'
                case 3: return '+++'
                default: return ''
            }
        }
        return ''
    },
    getAcetone(label) {
        const urine = this.urine || []
        const item = urine.find(i => i.observation_hours?.slice(0, 5) === label)
        if (item && item.acetone !== undefined && item.acetone !== null) {
            switch (Number(item.acetone)) {
                case 0: return '-'
                case 1: return '+'
                case 2: return '++'
                case 3: return '+++'
                default: return ''
            }
        }
        return ''
    },
    getVolume(label) {
        const urine = this.urine || []
        const item = urine.find(i => i.observation_hours?.slice(0, 5) === label)
        return item && item.volume ? `${item.volume}` : ''
    },
    
    emitCellClicked(label) {
      const urine = this.urine || []
      const item = urine.find(i => i.observation_hours?.slice(0, 5) === label)
      this.$emit('cell-clicked', {
        label,
        protein: item ? item.protein : null,
        acetone: item ? item.acetone : null,
        volume: item ? item.volume : null
      })
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