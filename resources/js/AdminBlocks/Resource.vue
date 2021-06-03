<template>
<div>
  <!-- Blocked Section -->
  <div class="max-w-7xl mx-auto px-4">

    <block-header 
      :block.sync="block" 
      :title="'Resource Block'" 
      :description="'Link a resource item onto a page'"
      :icon="'fa-newspaper'"
      v-on:destroy="$emit('destroy', block.blockid)"
    />

    <div v-show="block.expanded">
      <!-- Settings Bar -->
      <div class="bg-white border-b border-gray-200 px-4 py-2 grid grid-cols-1">
        <multiselect
            v-model="block.resource"
            :options="resources"
            :hideSelected="true"
            :allowEmpty="false"
            :showLabels="false"
            label="name"
            class="w-full"
            track-by="id">
            <template slot="singleLabel" slot-scope="{ option }">
              {{ option.title }}
            </template>
            <template slot="option" slot-scope="{option}">
              <div class="text-black">{{ option.title }}</div>
            </template>
          </multiselect>
      </div>
      <!-- End Settings Bar -->

      <div class="p-4 bg-white">
        <!-- Content Section -->
        <resource :block="block" :editable="false" />
        <!-- End Content Section -->
      </div>
    </div>
    
  </div>
</div>
</template>

<script>

import BlockHeader from '@/AdminBlocks/BlockHeader'
import Resource from '@/PageBlocks/Resource'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
import TextArea from '@/Shared/TextArea'
import Multiselect from 'vue-multiselect'

export default {
  props: {
    block: Object,
    resources: [Object, Array]
  },
  components: {
    BlockHeader,
    Resource,
    TextInput,
    SelectInput,
    TextArea,
    Multiselect
  }
}
</script>