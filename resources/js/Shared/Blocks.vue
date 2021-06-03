<template>
  <component :is="component" :block="block" />
</template>

<script>

export default {
  props: {
    block: null,
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
      return () => import(`@/PageBlocks/${this.block.component}`)
    }
  },
  mounted() {
    this.loader()
      .then(() => {
          this.component = () => this.loader()
      })
      .catch(() => {
          this.component = () => import('@/PageBlocks/Error')
      })
  }
}
</script>