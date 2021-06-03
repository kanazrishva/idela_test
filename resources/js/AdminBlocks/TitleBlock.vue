<template>
<div>
  <!-- Blocked Section -->
  <div class="max-w-7xl mx-auto px-4">

    <block-header 
      :block.sync="block" 
      :title="'Title Block'" 
      :description="'Large text block to be used as a seperator between sections.'"
      :icon="'fa-heading'"
      v-on:destroy="$emit('destroy', block.blockid)"
    />

    <div v-show="block.expanded">
      <!-- Settings Bar -->
      <div class="bg-white border-b border-gray-200 px-4 py-2 grid grid-cols-3">
        <div class="col-span-1 col-start-2 mx-1 h-6 bg-white flex justify-center">
          <div @click="block.alignment = 'text-left'"  
          :class="(block.alignment == 'text-left') ? 'text-teal-500 border-teal-600' : 'text-gray-400 border-gray-200'" 
          class="w-8 flex justify-center items-center border rounded-l-full cursor-pointer">
            <i class="fad fa-align-left"></i>
          </div>
          <div @click="block.alignment = 'text-center'" 
          :class="(block.alignment == 'text-center') ?  'text-teal-500 border-teal-600' : 'text-gray-400 border-gray-200'" 
          class="w-8 text-center flex justify-center items-center border cursor-pointer">
            <i class="fad fa-align-center"></i>
          </div>
          <div @click="block.alignment = 'text-right'" 
          :class="(block.alignment == 'text-right') ?  'text-teal-500 border-teal-600' : 'text-gray-400 border-gray-200'" 
          class="w-8 text-center flex justify-center items-center border rounded-r-full cursor-pointer">
            <i class="fad fa-align-right"></i>
          </div>
        </div>

        <div class="col-span-1 flex justify-end">
          <div 
            @click="block.background = 'bg-white'" 
            class="mx-1 w-6 h-6 bg-white border rounded-full cursor-pointer"
            :class="(block.background == 'bg-white') ? 'border-teal-600' : 'border-gray-300'"></div>
          <div 
            @click="block.background = 'bg-blue-100'"
            class="mx-1 w-6 h-6 bg-blue-100 border rounded-full cursor-pointer"
            :class="(block.background == 'bg-blue-100') ? 'border-teal-600' : 'border-gray-300'"></div>
        </div>
      </div>
      <!-- End Settings Bar -->

      <div class="p-4 bg-white">
        <!-- Content Section -->
        <title-block :block.sync="block" :editable="true" />
        <!-- End Content Section -->
      </div>
    </div>
    
  </div>
</div>
</template>

<script>
import TitleBlock from '@/PageBlocks/TitleBlock'
import BlockHeader from '@/AdminBlocks/BlockHeader'

export default {
  props: {
    block: Object,
  },
  components: {
    BlockHeader,
    TitleBlock
  }
}
</script>