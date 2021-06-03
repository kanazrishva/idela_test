<template>
<div>
  <admin-header :title="'Datasets'">
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
    :title="'Adding a new Dataset'"
    :description="'Adding a new dataset will create and upload the data you select. It will still need tobe processed and published in order to be used.'">
    <inertia-link
      class="inline-flex items-center px-3 py-3 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-teal-500 hover:bg-teal-500 focus:outline-none focus:border-teal-700 focus:shadow-outline-teal active:bg-teal-700 transition ease-in-out duration-150"
      :href="route('datasets.create')" >
      Add New Dataset
    </inertia-link>
  </admin-action>





  <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
      <!-- Replace with your content -->
    <div class="py-4">
      <div class="flex flex-col">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
          <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
            <table class="min-w-full table-fixed">
              
              
              <tbody>
              <tr v-for="(dataset, index) in datasets.data" :key="dataset.id"
                :class="{'bg-white': index % 2 === 0, 'bg-gray-50': index % 2 !== 0 }">
                <td class="px-4 py-4 sm:px-6">
                  <div class="flex items-center justify-between">
                    <div class="text-sm leading-5 font-medium text-gray-600 truncate">
                       {{ dataset.dataset_name }}
                    </div>
                    <div class="ml-2 flex-shrink-0 flex">
                      <span 
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                        :class="classStatus(dataset.status)">
                        {{ dataset.status }} 
                      </span>
                    </div>
                  </div>

                  <div class="mt-2 sm:flex sm:justify-between">
                    <div class="flex">
                      <div class="mr-6 flex items-center text-sm leading-5 text-gray-500">
                        {{ dataset.dataset_year }}
                      </div>
                      <div class="mr-6 mt-2 flex items-center text-sm leading-5 text-gray-500 sm:mt-0">
                        <i class="fad fa-map-marker-alt mr-1.5 h-5 w-5 text-gray-400"></i>
                        {{ dataset.country.country }}
                      </div>
                      <div v-if="dataset.status === 'imported'" class="mt-2 flex items-center text-sm leading-5 text-gray-500 sm:mt-0">
                        <i class="fad fa-table mr-1.5 h-5 w-5 text-gray-400"></i>
                        {{ dataset.total_rows }} rows
                      </div>
                    </div>
                    <div class="ml-2 flex-end flex">
                      <inertia-link :href="route('datasets.edit', dataset.id)" class="px-3 py-2 text-teal-700 text-xs hover:text-teal-500">
                        <i class="fad fa-edit"></i> edit
                      </inertia-link>
                      <inertia-link v-if="dataset.status === 'uploaded'"
                        class="px-3 py-2 text-teal-700 text-xs hover:text-teal-500" 
                        :href="route('datasets.process', dataset.id)">
                        <i class="fad fa-rabbit-fast"></i> process
                      </inertia-link>
                      <template  v-if="dataset.status === 'imported'">
                        <inertia-link class="px-3 py-2 text-teal-700 text-xs hover:text-teal-500" 
                          :href="route('datasets.show', dataset.id)">
                          <i class="fad fa-telescope"></i> view
                        </inertia-link>

                        <inertia-link class="px-3 py-2 text-teal-700 text-xs hover:text-teal-500" 
                          :href="route('datasets.codebook', dataset.id)">
                          <i class="fad fa-sitemap"></i> codebook
                        </inertia-link>
                      </template>

                      <button @click="destroy(dataset.id)" class="px-3 py-2 text-red-700 text-xs hover:text-red-500">
                        <i class="fad fa-trash-alt"></i> delete
                      </button>
                    </div>
                  </div>

                  
                </td>
              </tr>

              <tr v-if="datasets.data.length === 0">
                <td class="px-6 py-4 whitespace-no-wrap text-center text-sm leading-5 text-gray-900">
                  No datasets found.
                </td>
              </tr>
              </tbody>
              <tfoot>
                <th colspan="4" class="px-6 pb-3 border-gray-200 bg-gray-50 border-t border-gray-200">
                  <pagination :links="datasets.links" />
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

import Vue from 'vue'
import VueLodash from 'vue-lodash'
import throttle from 'lodash/throttle'
import pickBy from 'lodash/pickBy'
import mapValues from 'lodash/mapValues'
Vue.use(VueLodash, { lodash: {throttle, pickBy, mapValues} })

export default {
  layout: Admin,
  mixins: [mixins],
  data() {
    return {
      form: {
        search: this.filters.search,
        trashed: this.filters.trash
      },
      test: null
    }
  },
  props: {
    datasets: Object,
    filters: Object
  },
  watch: {
    form: {
      handler: Vue.lodash.throttle(function() {
        let query = Vue.lodash.pickBy(this.form)
        this.$inertia.replace(this.route('datasets', Object.keys(query).length ? query : { remember: 'forget' }))
      }, 150),
      deep: true,
    }
  },
  methods: {
    classStatus(status) {
      let colors = 'bg-green-100 text-green-800'
      if (status === 'uploaded') {
        colors = 'bg-yellow-100 text-yellow-800'
      }

      if (status === 'importing') {
        colors = 'bg-gray-200 text-gray-800'
      }

      if (status === 'failed') {
        colors = 'bg-red-700 text-white'
      }


      return colors
    },
    reset() {
      this.form = Vue.lodash.mapValues(this.form, () => null)
    },
    destroy(id) {
      if (confirm('Are you sure you want to delete this dataset?')) {
        this.$inertia.delete(this.route('datasets.destroy', id))
      }
    }
  },
  created() {
    // Listen to changes for the datasets
    Echo.channel('dataset')
    .listen('DatasetEvent', (e) => {
        this.$inertia.replace(this.route('datasets'));
        this.test = e;
    });
  }
}
</script>