<template>
  <component 
    v-on="$listeners"
    :is="component" 
    :page_id="page_id"
    :block.sync="block" 
    :resources="resources"
    :countries="countries"
    :graphs.sync="graphs"
    :graphoptions="graphoptions" />
</template>

<script>

export default {
  props: {
    page_id: null,
    block: null,
    graphs: null,
    countries: null,
    resources: null,
    graphoptions: null
  },
  data() {
    return {
      component: null
    }
  },
  computed: {
    loader() {
      if (!this.block.component) {
        return null
      }
      return () => import(`@/AdminBlocks/${this.block.component}`)
    }
  },
  mounted() {
    this.loader()
      .then(() => {
          this.component = () => this.loader()
      })
      .catch(() => {
          this.component = () => import('@/AdminBlocks/Error')
      })
  }
}
</script>