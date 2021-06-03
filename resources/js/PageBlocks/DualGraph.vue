<template>
<div class="w-full grid grid-cols-2 gap-0 md:gap-7 max-w-5xl md:px-5 mx-auto">

  <div class="col-span-2 relative border-solid border-3 border-l-0 border-r-0 border-gray-400 bg-white md:rounded-bl-xl md:rounded-r-xl my-4 py-5 md:col-span-1 md:border-l-3 md:border-r-3">
    <popper trigger="clickToToggle" :visible-arrow="false" :root-class="'pagepopper z-20'" :options="popperOptions">
      <div class="popper  z-20 focus:outline-none"
        :contenteditable="editable"
        @blur="(e) => {this.block.left.tooltip = e.target.innerHTML}" 
        v-html="this.block.left.tooltip" />
      <div slot="reference" class="cursor-pointer absolute top-7 left-6 text-gray-600  z-20">
        <i class="far fa-info-circle"></i>
      </div>
    </popper>

    <!-- <div class="uppercase absolute right-10 top-6 font-oswald text-gray-600 text-right"><i class="far fa-arrow-to-bottom mr-1"></i> Export Data</div> -->
    <div class="w-2/3 pl-16">
      <h3 class="font-oswald text-xl leading-6 mt-1 md:leading-8 focus:outline-none md:text-2xl md:mt-0" 
        :contenteditable="editable"
        @blur="(e) => {this.block.left.title = e.target.innerHTML}" 
        v-html="block.left.title" />
      <div class="mt-2 leading-tight focus:outline-none"
        :contenteditable="editable"
        @blur="(e) => {this.block.left.description = e.target.innerHTML}"
        v-html="block.left.description" />
    </div>
 
    <graph class="px-8" :height="'260px'" :graph="block.left.graph" />
  </div>

  <div class="col-span-2 relative border-solid border-3 border-l-0 border-r-0 border-gray-400 bg-white md:rounded-bl-xl md:rounded-r-xl my-4 py-5 md:col-span-1 md:border-l-3 md:border-r-3">
    <popper trigger="clickToToggle" :visible-arrow="false" :root-class="'pagepopper z-20'" :options="popperOptions">
      <div class="popper  z-20 focus:outline-none"
        :contenteditable="editable"
        @blur="(e) => {this.block.right.tooltip = e.target.innerHTML}" 
        v-html="this.block.right.tooltip" />
      <div slot="reference" class="cursor-pointer absolute top-7 left-6 text-gray-600 z-20">
        <i class="far fa-info-circle"></i>
      </div>
    </popper>
    
    
    <!-- <div class="uppercase absolute right-10 top-6 font-oswald text-gray-600 text-right"><i class="far fa-arrow-to-bottom mr-1"></i> Export Data</div> -->
    <div class="w-2/3 pl-16">
      <h3 class="font-oswald text-xl leading-6 mt-1 md:leading-8 focus:outline-none md:text-2xl md:mt-0"
        :contenteditable="editable"
        @blur="(e) => {this.block.right.title = e.target.innerHTML}" 
        v-html="block.right.title" />
      <div class="mt-2 leading-tight focus:outline-none"
        :contenteditable="editable"
        @blur="(e) => {this.block.right.description = e.target.innerHTML}"
        v-html="block.right.description" />
    </div>
 
    <graph class="px-8" :height="'260px'" :graph="block.right.graph" />
  </div>

</div>
</template>

<script>
import Graph from '@/Shared/Graph'
import Popper from 'vue-popperjs'

export default {
  data() {
    return {
      popperOptions: {
        placement: 'right', 
        modifiers: { 
          offset: { 
            offset: '0,10px,0,0' 
          }
        }
      }
    }
  },
  props: {
    block: Object,
    editable: false
  },
  components: {
    Graph,
    Popper
  }
}
</script>