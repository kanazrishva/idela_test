<template>
<div class="flex my-3 w-full text-xs font-bold justify-between items-center">
  {{ label }}
  <span 
    @click="$emit('toggleUpdate', item = !item)" 
    class="bg-teal-100 ml-3 relative inline-block flex-shrink-0 h-6 w-11 border-3 border-transparent rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
    <!-- On: "translate-x-5", Off: "translate-x-0" -->
    <span aria-hidden="true" 
      :class="item ? 'translate-x-5 bg-teal-500' : 'translate-x-0 bg-gray-400'"
      class="translate-x-0 inline-block h-5 w-5 rounded-sm shadow transform transition ease-in-out duration-200"></span>
  </span>
</div>
</template>

<script>
export default {
  data() {
    return {
      item: this.toggle
    }
  },
  props: {
    label: '',
    toggle: [Object, Array, Boolean],
  }
}
</script>