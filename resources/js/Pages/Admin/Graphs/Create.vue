<template>
<div>
  <admin-header :title="'Add New Graph'" :breadcrumbs="breadcrumbs" />  

  <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
    <div class="py-4">

      <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
        <div class="md:grid md:grid-cols-3 md:gap-6">
          <div class="md:col-span-1">

            <h3 class="text-lg font-medium leading-6 text-gray-900">New Template Graph</h3>
            <p class="mt-1 text-sm leading-5 text-gray-500">
              Creating a new graph here will make it available as a template to duplicate on any/all pages.
            </p>
          </div>
          <div class="mt-5 md:mt-0 md:col-span-2">
            
            <text-input v-model="form.name" :errors="error('name')" class="pb-4" label="Graph Name" />
            <text-area v-model="form.description" class="pb-4" label="Graph Description" />
            <select-input v-model="options.chart.type" label="Type of Graph" class="pb-6 w-full">
              <option value="bar">Bar Chart</option>
              <option value="line">Line Chart</option>
              <option value="area">Area Chart</option>
              <option value="pie">Pie Chart</option>
              <option value="donut">Donut Chart</option>
              <option value="scatter">Scatter Chart</option>
            </select-input>


              <button @click="submit" class="btn-create">
                <i class="fad fa-save mr-2"></i> Create Graph
              </button>
          </div>
        </div>
      </div>
    
    </div>
  </div>

</div>
</template>

<script>

import Admin from '@/Layouts/Admin'

import mixinsGraph from '@/Mixins/mixinGraph.js'
import mixinsAdmin from '@/Mixins/mixinAdmin.js'

export default {
  mixins: [mixinsGraph, mixinsAdmin],
  layout: Admin,
  data() {
    return {
      // Need some defaults here
      breadcrumbs: [
        {
          title: 'Graphs',
          link: 'graphs'
        }
      ],
      rows: [{
        data: [{
          item: null,
          label: '',
          id: this.generateId(),
        }],
        id: this.generateId(),
        expanded: true,
        dataset: null,
        name: 'Series 1'
      }],
      // Merges with the Mixin Options
      options: {
        chart: {
          type: "bar"
        }
      },
      form: {
        name: null,
        description: null,
      },
    }
  },
  methods: {
    generateId() {
      return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15)
    },
    submit() {

      let params = {
        'rows': (this.rows),
        'options': (this.options),
        'name': this.form.name,
        'description': this.form.description
      }

      this.$inertia.post(this.route('graphs.store'), params)
    }
  }
}


</script>

