<template>
    <div class="max-w-7xl mx-auto px-4 pt-4 sm:px-6 lg:px-8">
    <div class="p-4 bg-orange-200 rounded flex items-center justify-between">
      <div class="flex items-center">
        <div class="text-sm font-medium text-yellow-800">
          <slot />
        </div>
      </div>
      <button class="text-sm text-yellow-800 hover:underline" tabindex="-1" type="button" @click="$emit('restore')">Restore</button>
    </div>
  </div>
</template>