<template>
<div>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div>
      <nav class="hidden sm:flex items-center text-sm leading-5 font-medium text-gray-500">
        <breadcrumb :title="'Dashboard'" :link="'dashboard'" />
        <breadcrumb v-for="(breadcrumb, index) in breadcrumbs" 
          :key="index"
          :title="breadcrumb.title" 
          :link="breadcrumb.link" />
        <div>{{ title }}</div>
      </nav>
    </div>

    <div class="mt-2 md:flex md:items-center md:justify-between">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:leading-9 sm:truncate">
          {{ title }}
        </h2>
      </div>

      <slot />
    </div>
  </div>
</div>
</template>

<script>

import Breadcrumb from '@/Shared/Breadcrumb'

export default {
  props: {
    title: String,
    breadcrumbs: {
      type: Array,
      default: () => { return [] }
    }
  },
  components: {
    Breadcrumb
  }
}
</script>