<template>
<div>
  <div class="max-w-7xl mx-auto px-4">
    <block-header   
      :block.sync="block" 
      :removeable="true"
      :title="'Page Hero'"
      :description="'The hero at the top of each page.'"
      :icon="'fa-square'"
    />

     <div v-show="block.expanded">
      <!-- Settings Bar -->
      <div class="bg-white border-b border-gray-200 px-4 py-2 grid grid-cols-2">
        <div class="col-span-1 mx-1 h-6 bg-white flex justify-center items-center">
          <div class="mr-4 text-xs font-bold">Country</div>
          <select-input v-model="block.country" class="w-1/3">
            <option v-for="(country, index) in countries" :key="index" :value="country.country">{{ country.country }}</option>
          </select-input>
        </div>
        <div class="col-span-1 mx-1 h-6 bg-white flex justify-center items-center">
          <div class="text-xs font-bold">Add Element</div>
          <div @click="addItem" class="mx-1 w-6 h-6 cursor-pointer flex items-center justify-center">
            <i class="fad fa-sm text-teal-500 fa-plus"></i>
          </div>
        </div>
      </div>
      <!-- End Settings Bar -->


       <div class="p-4 bg-white">
         <div class="py-5 bg-blue-100">
            <!-- Content Section -->
            <hero :block.sync="block" @removeElement="removeElement" :editable="true" />
            <!-- End Content Section -->
          </div>
      </div>
     </div>
  </div>
</div>
</template>

<script>
import Hero from '@/PageBlocks/Hero'
import BlockHeader from '@/AdminBlocks/BlockHeader'
import SelectInput from '@/Shared/SelectInputSmall'

export default {
  data() {
    return {
      selectedElement: null
    }
  },
  props: {
    block: Object,
    countries: [Object, Array]
  },
  components: {
    BlockHeader,
    Hero,
    SelectInput
  },
  methods: {
    removeElement(id) {
      this.block.items = this.block.items.filter(function( obj ) {
          return obj.id !== id;
      });
    },
    addItem() {
      this.block.items.push({
        id: Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15),
        icon: '',
        title: 'Lorem Ipsum',
        description: 'Lorem ipsum dolor'
      })
    }
  }
}
</script>