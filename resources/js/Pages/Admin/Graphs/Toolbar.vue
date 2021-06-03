<template>
<div>

  <toggle 
    v-if="type !== 'scatter'"
    :label="'Shared Tooltips'" 
    :toggle.sync="options.tooltip.shared"
    @toggleUpdate="(toggle) => { options.tooltip.shared = toggle }" /> 

  <toggle 
    v-if="type !== 'scatter'"
    :label="'Distributed Bar Graph'" 
    :toggle.sync="options.plotOptions.bar.distributed"
    @toggleUpdate="(toggle) => { options.plotOptions.bar.distributed = toggle }" /> 

  <toggle 
    v-if="type !== 'scatter'"
    :label="'Use Nice Scales'" 
    :toggle.sync="options.yaxis.forceNiceScale"
    @toggleUpdate="(toggle) => { options.yaxis.forceNiceScale = toggle }" /> 

  <toggle 
    :label="'Use Legend'" 
    :toggle.sync="options.legend.show"
    @toggleUpdate="(toggle) => { options.legend.show = toggle }" /> 


</div>
</template>

<script>

import Toggle from '@/Pages/Admin/Graphs/Toggle'

export default {
  data() {
    return {
      type: this.options.chart.type
    }
  },
  props: {
    options: [Object, Array]
  },
  components: {
    Toggle
  },
  watch: {
    options: {
      handler: function(newVal) {
        this.type = newVal.chart.type
      }, 
      deep: true
    }
  }
}
</script>