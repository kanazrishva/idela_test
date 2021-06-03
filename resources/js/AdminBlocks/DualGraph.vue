<template>
<div>
  <!-- Blocked Section -->
  <div class="max-w-7xl mx-auto px-4">

    <block-header 
      :block.sync="block" 
      :title="'Dual Graph Block'" 
      :description="'Side by side graphs with content.'"
      :icon="'fa-signal-2'"
      v-on:destroy="$emit('destroy', block.blockid)"
    />


    <div v-show="block.expanded">
      <!-- Settings Bar -->
      <div class="bg-white border-b border-gray-200 px-4 py-2 grid grid-cols-2">
        <div class="col-span-1 flex justify-center">


          <multiselect
            v-show="block.left.graph == selectedlgraph"
            v-model="lgraph"
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
              <div v-if="props.option.$isLabel">
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
            v-show="selectedlgraph !== null && (block.left.graph == selectedlgraph)"
            @click="duplicate(lgraph, 'left')" 
            class="mx-1 w-6 h-6 cursor-pointer flex items-center justify-center">
            <i class="fad fa-sm text-teal-500 fa-check"></i>
          </div>

          <div v-show="selectedlgraph == null && (block.left.graph !== selectedlgraph)"
            class="flex items-center justify-start">

            <div class="text-xs mr-4"><span class="font-black text-gray-800">Graph</span> |  {{ lgraph && lgraph.name }}</div>
            
            <div @click="editGraph(lgraph, 'left')" class="mx-1 w-6 h-6 cursor-pointer flex items-center justify-center">
              <i class="fad fa-sm text-teal-500 fa-edit"></i>
            </div>

            <div @click="deleteLeftGraph" class="mx-1 w-6 h-6 cursor-pointer flex items-center justify-center">
              <i class="fad fa-sm fa-trash-alt text-red-700 hover:text-red-500"></i>
            </div>
          </div>

        </div>
        <div class="col-span-1 flex justify-center">


          <multiselect
            v-show="block.right.graph == selectedrgraph"
            v-model="rgraph"
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
              <div v-if="props.option.$isLabel">
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
            v-show="selectedrgraph !== null && (block.right.graph == selectedrgraph)"
            @click="duplicate(rgraph, 'right')" 
            class="mx-1 w-6 h-6 cursor-pointer flex items-center justify-center">
            <i class="fad fa-sm text-teal-500 fa-check"></i>
          </div>

          <div v-show="selectedrgraph == null && (block.right.graph !== selectedrgraph)"
            class="flex items-center justify-start">

            <div class="text-xs mr-4"><span class="font-black text-gray-800">Graph</span> |  {{ rgraph && rgraph.name }}</div>
            
            <div @click="editGraph(rgraph, 'right')" class="mx-1 w-6 h-6 cursor-pointer flex items-center justify-center">
              <i class="fad fa-sm text-teal-500 fa-edit"></i>
            </div>

            <div @click="deleteRightGraph" class="mx-1 w-6 h-6 cursor-pointer flex items-center justify-center">
              <i class="fad fa-sm fa-trash-alt text-red-700 hover:text-red-500"></i>
            </div>
          </div>
        </div>
      </div>
      <!-- End Settings Bar -->

      <div class="p-4 bg-white">
        <div class="bg-blue-100">
          <!-- Content Section -->
          <dual-graph :block.sync="block" :editable="true" />
          <!-- End Content Section -->
        </div>
      </div>

    </div>

  </div>
</div>
</template>

<script>

import BlockHeader from '@/AdminBlocks/BlockHeader'
import DualGraph from '@/PageBlocks/DualGraph'
import SelectInput from '@/Shared/SelectInput'
import Multiselect from 'vue-multiselect'

import axios from 'axios'

export default {
  data() {
    return {
      selectedlgraph: null,
      selectedrgraph: null,
      lgraph: this.graphs.find(o => o.id === this.block.left.graph),
      rgraph: this.graphs.find(o => o.id === this.block.right.graph)
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
    DualGraph,
    Multiselect,
    SelectInput,
  },
  watch: {
    lgraph(newVal) {
      if (newVal !== null) {
        this.selectedlgraph = newVal.id
        this.block.left.graph = newVal.id
      } else {
        this.selectedlgraph = null
        this.block.left.graph = null
      }
    },
    rgraph(newVal) {
      if (newVal !== null) {
        this.selectedrgraph = newVal.id
        this.block.right.graph = newVal.id
      } else {
        this.selectedrgraph = null
        this.block.right.graph = null
      }
    }
  },
  methods: {
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
    duplicate(graph, side) {
      axios.post(this.route('graphs.duplicate').url(), {'graph': graph.id, 'page_id': this.page_id})
        .then((response) => {
          this.$emit('replaceGraphs', response.data.graphs)
          if (side === 'left') {
            this.block.left.graph = response.data.graph.id
            this.selectedlgraph = null
            //this.lgraph = response.data.graph
          }

          if (side === 'right') {
            this.block.right.graph = response.data.graph.id
            this.selectedrgraph = null
            //this.rgraph = response.data.graph
          }
        })
    },
    deleteLeftGraph() {
      if (confirm('Are you sure you want to delete this graph?')) {
        this.$inertia.delete(this.route('graphs.destroy', this.block.left.graph), {preserveState: true})
        .then((response) => {
          this.lgraph = null
        })
      }
    },
    deleteRightGraph() {
      if (confirm('Are you sure you want to delete this graph?')) {
        this.$inertia.delete(this.route('graphs.destroy', this.block.right.graph), {preserveState: true})
        .then((response) => {
          this.rgraph = null
        })
      }
    },
    editGraph(graph, side) {
      this.$emit('editGraphDual', {'block': this.block, 'side':side})
      // if (confirm('Did you save the page? If not, you might want to save before editing this graph.')) {
      //   this.$inertia.visit(this.route('graphs.edit', graph.id))
      // }
    }
  },
  created() {
    if (this.lgraph === undefined) {
      //console.log('undefined')
      this.lgraph = null
    }

    if (this.rgraph === undefined) {
      //console.log('undefined')
      this.rgraph = null
    }
  }
}
</script>