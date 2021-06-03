<template>
<div>

  <admin-header :title="'Graphs'">
    <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
      <label class="block text-gray-700">Trashed:</label>
      <select v-model="form.trashed" class="mt-1 w-full form-select">
        <option :value="null" />
        <option value="with">With Trashed</option>
        <option value="only">Only Trashed</option>
      </select>
    </search-filter>
  </admin-header>
  <admin-action 
    :title="'Add a new Graph'"
    :description="'Building a Graph will allow you to place it on any page you create.'">
    <inertia-link  
      :href="route('graphs.create')" 
      class="inline-flex btn-create items-center">
      Add New Graph
    </inertia-link>
  </admin-action>


  <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
    <div class="py-4">
      <div class="flex flex-col">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
          <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
            <table class="min-w-full table-fixed">
              
              
              <tbody>
              <tr v-for="(graph, index) in graphs.data" :key="graph.id"
                :class="{'bg-white': index % 2 === 0, 'bg-gray-50': index % 2 !== 0 }">
                <td class="px-4 py-4 sm:px-6">
                  
                  <div class="flex items-center">
                    <div class="text-center pr-4">
                      <i :class="chartIcon(graph.options.chart.type)" class="fad fa-fw  text-teal-700"></i><br>
                    </div>

                    <div class="w-full">
                      <div class="flex items-center justify-between">
                        <div class="text-sm leading-5 font-medium text-gray-900 truncate">
                          {{ graph.name }}<br>
                          <span class="text-xs leading-5 text-gray-700">{{ graph.description || 'no description' }}</span>
                        </div>

                        <div v-show="graph.deleted_at === null" class="flex items-center">
                          <inertia-link 
                            :href="route('graphs.edit', graph.id)" 
                            class="px-3 py-2 text-teal-700 text-xs hover:text-teal-500">
                            <i class="fad fa-edit"></i> edit
                          </inertia-link>

                          <button 
                            @click="destroy(graph.id)" 
                            class="px-3 py-2 text-red-700 text-xs hover:text-red-500">
                            <i class="fad fa-trash-alt"></i> delete
                          </button>

                          <div class="ml-2 flex-shrink-0 flex">
                            <div class="flex text-xs justify-end items-center">
                              publish
                              <span 
                                @click="publish(graph)" 
                                class="bg-teal-100 ml-3 relative inline-block flex-shrink-0 h-6 w-11 border-3 border-transparent rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
                                <span aria-hidden="true" 
                                  :class="graph.publish ? 'translate-x-5 bg-teal-500' : 'translate-x-0 bg-gray-400'"
                                  class="translate-x-0 inline-block h-5 w-5 rounded-sm shadow transform transition ease-in-out duration-200"></span>
                              </span>
                            </div>
                          </div>
                        </div>

                        <div v-show="graph.deleted_at !== null" class="flex items-center">
                          <button 
                            @click="restore(graph.id)" 
                            class="px-3 py-2 text-yellow-700 text-xs hover:text-yellow-500">
                            <i class="fad fa-trash-undo"></i> restore
                          </button>
                        </div>

                      </div>

                    </div>
                  </div>
                </td>
              </tr>

              <tr v-if="graphs.data.length === 0">
                <td class="px-6 py-4 whitespace-no-wrap text-center text-sm leading-5 text-gray-900">
                  No graphs found.
                </td>
              </tr>
              </tbody>
              <tfoot>
                <th colspan="4" class="px-6 pb-3 bg-gray-50 border-t border-gray-200">
                  <pagination :links="graphs.links" />
                </th>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>


</div>
</template>

<script>

import Admin from '@/Layouts/Admin'
import mixins from '@/Mixins/mixinAdmin.js'

import throttle from 'lodash/throttle'
import pickBy from 'lodash/pickBy'
import mapValues from 'lodash/mapValues'

export default {
  layout: Admin,
  mixins: [mixins],
  data() {
    return {
      form: {
        search: this.filters.search,
        trashed: this.filters.trash
      }
    }
  },
  props: {
    datasets: Array,
    filters: Object,
    graphs: Object
  },
  watch: {
    form: {
      handler: throttle(function() {
        let query = pickBy(this.form)
        this.$inertia.replace(this.route('graphs', Object.keys(query).length ? query : { remember: 'forget' }))
      }, 150),
      deep: true,
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
    destroy(id) {
      if (confirm('Are you sure you want to delete this graph?')) {
        this.$inertia.delete(this.route('graphs.destroy', id))
      }
    },
    restore(id) {
      this.$inertia.replace(this.route('graphs.restore', id))
    },
    // getDataset(rows) {
    //   let results =[]
    //   rows.forEach(row => {
    //     let result = this.datasets.filter(dataset => {
    //       // Need to make sure we loop through multiple datasets if they are in an array
    //       if (dataset.id === row.dataset) {
    //         results.push(dataset.dataset_name)
    //       }
    //     })
    //     //
    //   })
      
    //   return results.join(', ')
    // },
    publish(graph) {
      let publish = !graph.publish
      this.$inertia.put(this.route('graphs.update', graph), {publish: publish})
    },
    reset() {
      this.form = mapValues(this.form, () => null)
    }
  }
}
</script>