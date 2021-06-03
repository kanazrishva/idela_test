<template>
  <div class="max-w-7xl mx-auto px-8 py-5">
    <div class="bg-white shadow rounded-lg p-6">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-lg font-medium leading-6 text-gray-900">{{ title }}</h3>
          <p class="mt-1 text-sm leading-5 text-gray-500">{{ description }}</p>
        </div>

        <div class="flex items-center justify-end">
          <slot />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    title: String,
    description: String
  }
}
</script>