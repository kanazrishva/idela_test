<template>
  <span class="inline-flex rounded-md shadow-sm">
    <button 
      :disabled="loading" 
      class="inline-flex items-center px-3 py-3 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-teal-500 hover:bg-teal-500 focus:outline-none focus:border-teal-700 focus:shadow-outline-teal active:bg-teal-700 transition ease-in-out duration-150">
      <slot />
    </button>
  </span>
</template>

<script>
export default {
  props: {
    loading: Boolean,
  },
}
</script>
