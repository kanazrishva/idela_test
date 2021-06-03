<template>
<div class="bg-white px-4 py-1 border-b border-gray-200">
  <div class="flex justify-between items-center flex-wrap">
  
      <div class="flex items-center">
        <div class="flex-shrink-0 ml-4">
          <i class="h-12 w-12 fa-fw fad" :class="icon"></i>
        </div>
        <div class="ml-2">
          <h3 class="leading-tight text-sm font-medium text-gray-900">
            {{ title }}
          </h3>
          <p class="text-xs leading-tight text-gray-500">
            {{ description }}
          </p>
        </div>
      </div>
    
    <div class="flex-shrink-0 flex">
      <button v-if="!removeable" @click="$emit('destroy', block.blockid)" 
        class="px-3 py-2 text-red-700 text-xs hover:text-red-500">
        <i class="fad fa-trash-alt"></i> remove
      </button>
      <button @click="block.expanded = !block.expanded"
        :class="(block.expanded) ? 'rotate-90' : 'rotate-0'"
        class="w-8 h-8 border rounded-full border-gray-200 transform focus:outline-none duration-200 transition-all cursor-pointer">
        <i class="fad fa-chevron-right"></i>
      </button>
    </div>
  </div>
</div>
</template>

<script>
export default {
  props: {
    block: Object,
    title: null,
    description: null,
    icon: null,
    removeable: {
      default: false
    }
  }
}
</script>