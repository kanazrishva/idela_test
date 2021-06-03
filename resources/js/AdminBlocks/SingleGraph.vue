<template>
<div>

<!-- Blocked Section -->
  <div class="max-w-7xl mx-auto px-4">

    <block-header 
      :block.sync="block" 
      :title="'Single Graph Block'" 
      :description="'A single full width graph with content.'"
      :icon="'fa-chart-bar'"
      v-on:destroy="$emit('destroy', block.blockid)"
    />

    <div v-show="block.expanded">
      <!-- Settings Bar -->
      <div class="bg-white border-b border-gray-200 px-4 py-2 grid grid-cols-2">

        <div class="col-span-2 flex justify-start pb-4">

          <multiselect
            v-show="block.graph == selectedgraph"
            v-model="graph"
            :options="graphoptions"
            :group-select="false"
            :showLabels="false"
            :searchable="false"
            group-values="graphs"
            group-label="graphtype"
            label="name"
            class="w-full"
            track-by="id">
            <template slot="option" slot-scope="props">
              <div v-if="props.option.$isLabel === true">
                <i :class="chartIcon(props.option.$groupLabel)" class="fad fa-fw text-teal-700"></i> 
                <span class="text-black font-oswald text-sm pl-4">{{ props.option.$groupLabel.toUpperCase() }}</span>
              </div>
              <div v-else>
                <div class="text-black text-sm">{{ props.option.name }}</div>
                <div class="text-gray-700 text-xs truncate">{{ props.option.description || '---' }}</div>
              </div>
            </template>
          </multiselect>

          <div 
            v-show="selectedgraph !== null && (block.graph == selectedgraph)"
            @click="duplicate" 
            class="mx-1 w-6 h-6 cursor-pointer flex items-center justify-center">
            <i class="fad fa-sm text-teal-500 fa-check"></i>
          </div>

          <div v-show="selectedgraph == null && (block.graph !== selectedgraph)"
            class="flex items-center justify-start">

            <div class="text-xs mr-4"><span class="font-black text-gray-800">Graph</span> |  {{ graph && graph.name }}</div>
            
            <div @click="editGraph" class="mx-1 w-6 h-6 cursor-pointer flex items-center justify-center">
              <i class="fad fa-sm text-teal-500 fa-edit"></i>
            </div>

            <div @click="deleteGraph" class="mx-1 w-6 h-6 cursor-pointer flex items-center justify-center">
              <i class="fad fa-sm fa-trash-alt text-red-700 hover:text-red-500"></i>
            </div>
          </div>

        </div>

        <div class="col-span-2 flex justify-start">
          <div class="text-xs w-32 font-bold pr-4">Display of Graph</div>
          <div class="flex text-xs justify-end items-center">
            accordion
            <span 
              @click="block.accordion = !block.accordion" 
              class="bg-teal-100 ml-3 relative inline-block flex-shrink-0 h-6 w-11 border-3 border-transparent rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
              <!-- On: "translate-x-5", Off: "translate-x-0" -->
              <span aria-hidden="true" 
                :class="block.accordion ? 'translate-x-5 bg-teal-500' : 'translate-x-0 bg-gray-400'"
                class="translate-x-0 inline-block h-5 w-5 rounded-sm shadow transform transition ease-in-out duration-200"></span>
            </span>
          </div>

          <div class="flex text-xs justify-end items-center ml-4">
            expand
            <span 
              @click="block.expand = !block.expand" 
              class="bg-teal-100 ml-3 relative inline-block flex-shrink-0 h-6 w-11 border-3 border-transparent rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
              <!-- On: "translate-x-5", Off: "translate-x-0" -->
              <span aria-hidden="true" 
                :class="block.expand ? 'translate-x-5 bg-teal-500' : 'translate-x-0 bg-gray-400'"
                class="translate-x-0 inline-block h-5 w-5 rounded-sm shadow transform transition ease-in-out duration-200"></span>
            </span>
          </div>

           <div class="flex text-xs justify-end items-center ml-4">
            with above
            <span 
              @click="block.withabove = !block.withabove" 
              class="bg-teal-100 ml-3 relative inline-block flex-shrink-0 h-6 w-11 border-3 border-transparent rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
              <!-- On: "translate-x-5", Off: "translate-x-0" -->
              <span aria-hidden="true" 
                :class="block.withabove ? 'translate-x-5 bg-teal-500' : 'translate-x-0 bg-gray-400'"
                class="translate-x-0 inline-block h-5 w-5 rounded-sm shadow transform transition ease-in-out duration-200"></span>
            </span>
          </div>

           <div class="flex text-xs justify-end items-center ml-4">
            endcap
            <span 
              @click="block.endcap = !block.endcap" 
              class="bg-teal-100 ml-3 relative inline-block flex-shrink-0 h-6 w-11 border-3 border-transparent rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
              <!-- On: "translate-x-5", Off: "translate-x-0" -->
              <span aria-hidden="true" 
                :class="block.endcap ? 'translate-x-5 bg-teal-500' : 'translate-x-0 bg-gray-400'"
                class="translate-x-0 inline-block h-5 w-5 rounded-sm shadow transform transition ease-in-out duration-200"></span>
            </span>
          </div>
        </div>

        <div class="col-span-2 flex pt-2 justify-start items-center">
          <div class="text-xs w-32 font-bold pr-4">Filter Options</div>

          <div class="flex text-xs justify-end items-center">
            compare by
            <span 
              @click="block.compareby = !block.compareby" 
              class="bg-teal-100 ml-3 relative inline-block flex-shrink-0 h-6 w-11 border-3 border-transparent rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
              <!-- On: "translate-x-5", Off: "translate-x-0" -->
              <span aria-hidden="true" 
                :class="block.compareby ? 'translate-x-5 bg-teal-500' : 'translate-x-0 bg-gray-400'"
                class="translate-x-0 inline-block h-5 w-5 rounded-sm shadow transform transition ease-in-out duration-200"></span>
            </span>
          </div>

          <div class="flex text-xs justify-end items-center ml-4">
            filter
            <span 
              @click="block.filterby = !block.filterby" 
              class="bg-teal-100 ml-3 relative inline-block flex-shrink-0 h-6 w-11 border-3 border-transparent rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
              <!-- On: "translate-x-5", Off: "translate-x-0" -->
              <span aria-hidden="true" 
                :class="block.filterby ? 'translate-x-5 bg-teal-500' : 'translate-x-0 bg-gray-400'"
                class="translate-x-0 inline-block h-5 w-5 rounded-sm shadow transform transition ease-in-out duration-200"></span>
            </span>
          </div>

          <div v-show="block.filterby" class="flex text-xs justify-end items-center ml-4">
            gender
            <span 
              @click="block.filter_gender = !block.filter_gender" 
              class="bg-teal-100 ml-3 relative inline-block flex-shrink-0 h-6 w-11 border-3 border-transparent rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
              <!-- On: "translate-x-5", Off: "translate-x-0" -->
              <span aria-hidden="true" 
                :class="block.filter_gender ? 'translate-x-5 bg-teal-500' : 'translate-x-0 bg-gray-400'"
                class="translate-x-0 inline-block h-5 w-5 rounded-sm shadow transform transition ease-in-out duration-200"></span>
            </span>
          </div>

          <div v-show="block.filterby" class="flex text-xs justify-end items-center ml-4">
            age
            <span 
              @click="block.filter_age = !block.filter_age" 
              class="bg-teal-100 ml-3 relative inline-block flex-shrink-0 h-6 w-11 border-3 border-transparent rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
              <!-- On: "translate-x-5", Off: "translate-x-0" -->
              <span aria-hidden="true" 
                :class="block.filter_age ? 'translate-x-5 bg-teal-500' : 'translate-x-0 bg-gray-400'"
                class="translate-x-0 inline-block h-5 w-5 rounded-sm shadow transform transition ease-in-out duration-200"></span>
            </span>
          </div>

          <div v-show="block.filterby" class="flex text-xs justify-end items-center ml-4">
            location
            <span 
              @click="block.filter_location = !block.filter_location" 
              class="bg-teal-100 ml-3 relative inline-block flex-shrink-0 h-6 w-11 border-3 border-transparent rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
              <!-- On: "translate-x-5", Off: "translate-x-0" -->
              <span aria-hidden="true" 
                :class="block.filter_location ? 'translate-x-5 bg-teal-500' : 'translate-x-0 bg-gray-400'"
                class="translate-x-0 inline-block h-5 w-5 rounded-sm shadow transform transition ease-in-out duration-200"></span>
            </span>
          </div>
        </div>

      </div>
      <!-- End Settings Bar -->

      <div class="p-4 bg-white">
        <div class="bg-blue-100">
          <!-- Content Section -->
          <single-graph :block.sync="block" :editable="true" />
          <!-- End Content Section -->
        </div>
      </div>

    </div>


  </div>
</div>
</template>

<script>

import BlockHeader from '@/AdminBlocks/BlockHeader'
import SingleGraph from '@/PageBlocks/SingleGraph'
import Multiselect from 'vue-multiselect'

import axios from 'axios'

export default {
  data() {
    return {
      selectedgraph: null,
      graph: this.graphs.find(o => o.id === this.block.graph),
    }
  },
  props: {
    page_id: null,
    block: Object,
    graphs: [Object, Array],
    graphoptions: [Object, Array]
  },
  components: {
    BlockHeader,
    SingleGraph,
    Multiselect
  },
  watch: {
    graph(newVal) {
      console.log(newVal)
      if (newVal !== null) {
        this.selectedgraph = newVal.id
        this.block.graph = newVal.id
      } else {
        this.selectedgraph = null
        this.block.graph = null
      }
      
    },
    // block(newVal) {
    //   console.log('block updated')
    // }
  },
  filters: {
  capitalize: function (value) {
      if (!value) return ''
      value = value.toString()
      return value.charAt(0).toUpperCase() + value.slice(1)
    }
  },
  methods: {
    nameWithDesc({name, description}) {
      let str = ''
      if (name) {
        str =  `${name} — [${description}]`
      }
      return str
    },
    chartIcon(type) {
      let icon = ''
      switch(type) {
        case 'donut':
          icon = 'fa-circle-notch'
        break
        case 'pie':
          icon = 'fa-chart-pie'
        break
        case 'bar':
          icon = 'fa-chart-bar'
        break
        case 'area':
          icon = 'fa-chart-area'
        break
        case 'line':
          icon = 'fa-chart-line'
        break
        case 'scatter':
          icon = 'fa-chart-scatter'
        break
      }

      return icon
    },
    duplicate() {

      //console.log('populate this up to the parent')
      
      axios.post(this.route('graphs.duplicate').url(), {'graph': this.graph.id, 'page_id': this.page_id})
      .then((response) => {
        this.$emit('replaceGraphs', response.data.graphs)
        this.block.graph = response.data.graph.id
        this.selectedgraph = null
        //this.graph = response.data.graph

      })
    },
    deleteGraph() {
      if (confirm('Are you sure you want to delete this graph?')) {
        this.$inertia.delete(this.route('graphs.destroy', this.block.graph), {preserveState: true})
        .then((response) => {
          this.graph = null
        })
      }
    },
    editGraph() {
      this.$emit('editGraph', this.block)
    }
  },
  created() {
    if (this.graph === undefined) {
      this.graph = null
    }
  },
  mounted() {

    // Add filter defaults to blocks to support legacy cases
    if (this.block.compareby == undefined) {
      console.log('compareby set')
      this.$set(this.block, 'compareby', true)
    }
    if (this.block.filterby == undefined) {
      console.log('filterby set')

      this.$set(this.block, 'filterby', true)
    }
    if (this.block.filter_gender == undefined) {
      this.$set(this.block, 'filter_gender', true)
    }
    if (this.block.filter_age == undefined) {
      this.$set(this.block, 'filter_age', true)
    }
    if (this.block.filter_location == undefined) {
      this.$set(this.block, 'filter_location', true)
    }
  }
}
</script>