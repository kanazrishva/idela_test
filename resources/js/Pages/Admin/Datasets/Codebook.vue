<template>
<div class="h-full">
  <admin-header :title="dataset.dataset_name + ' Codebook'" :breadcrumbs="breadcrumbs">
    <search-filter v-model="searchForm.search" :showFilters="false" class="w-full max-w-md mr-4" @reset="reset" />
  </admin-header>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
      <!-- Replace with your content -->
      <div class="py-4">

        <div class="flex flex-col">
          <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="align-middle inline-block min-w-full shadow sm:rounded-lg border-b border-gray-200">
              <table class="min-w-full table-fixed">
                <thead>
                <tr>
                  <th class="w-1/3 px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                    Dataset Column
                  </th>
                  <th class="w-2/3 px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                    Codebook Item
                  </th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(header, index) in headers.data" :key="index"
                  :class="{'bg-white': index % 2 === 0, 'bg-gray-50': index % 2 !== 0 }">
                  <td class="px-6 py-4 whitespace-no-wrap text-left text-sm leading-5 text-gray-900">{{ header.column_name }}</td>
                  <td class="px-6 py-4 whitespace-no-wrap text-left text-sm leading-5 text-gray-700">
                  
                  <div v-show="codebookOffset !== header.id">
                    <template v-if="header.codebook !== null">
                      {{ header.codebook.matching_name}}
                    </template>
                    <template v-else>
                      ---
                    </template>
                    <button class="px-3 py-2 text-teal-700 hover:text-teal-500" @click="startEditing(header)"><i class="fad fa-edit"></i></button>
                  </div>
                  <div v-show="codebookOffset === header.id"  class="flex items-start justify-start">
                  <form @submit.prevent="updateDatasetMeta(header.id, dataset)" class="flex items-start justify-start w-full">
                   
                    <div class="w-full">
                      <multiselect 
                        v-model="header.codebook"
                        :id="'select-input-'.id" 
                        :options="codebook"
                        :allow-empty="true"
                        label="matching_name"
                        class="flex items-start justify-start w-full"
                        @input="selectChange($event)"
                        track-by="id">
                        <template slot="singleLabel" slot-scope="{ option }">
                          <strong>{{ option.matching_name }}</strong>
                        </template>
                      </multiselect>
                      <div v-if="Object.keys($page.errors).length > 0" class="mt-1 text-xs text-red-600">
                        That codebook item was already assigned to a different column. It has been reset.
                      </div>
                    </div>

                    <loading-button type="submit" class="ml-2"><i class="fad fa-save"></i></loading-button>
                  </form>
                  <button @click="cancelEdit(header)" class="pt-3 pl-4 text-teal-500 hover:text-teal-700 text-xs"><i class="fad fa-undo"></i> cancel</button>
                  </div>
                  </td>
                </tr>
                </tbody>
                <tfoot>
                  <th colspan="4" class="px-6 pb-3 border-gray-200 bg-gray-50 border-t border-gray-200">
                    <pagination :links="headers.links" />
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
      breadcrumbs: [
        {
          title: 'Datasets',
          link: 'datasets'
        },
      ],
      codebookOffset: -1,
      searchForm: {
        search: this.filters.search,
      },
      form: {
        codebook_id: null
      },
      prevSelection: null
    }
  },
  props: {
    dataset: Object,
    codebook: Array,
    headers: Object,
    filters: Object,
  },
  watch: {
    searchForm: {
      handler: Vue.lodash.throttle(function() {
        let query = Vue.lodash.pickBy(this.searchForm)
        let data = Object.keys(query).length ? query : { remember: 'forget' }
            data.dataset = this.dataset.id

        this.$inertia.replace(this.route('datasets.codebook', data))
      }, 150),
      deep: true,
    }
  },
  methods: {
    reset() {
      this.searchForm = Vue.lodash.mapValues(this.searchForm, () => null)
    },
    startEditing(header) {
      this.prevSelection = header.codebook
      this.codebookOffset = header.id
    },
    cancelEdit(header) {
      header.codebook = this.prevSelection
      this.resetForm()
    },
    resetForm() {
      this.codebookOffset = -1
      this.$page.errors = {}
      this.form.codebook_id = null
      this.prevSelection = null
    },
    selectChange(codebook) {
      this.form.codebook_id = null

      if (codebook !== null) {
        this.form.codebook_id = codebook.id
      }
    },
    updateDatasetMeta(header, dataset) {
      this.$inertia.put(this.route('datasets.codebook.update', [header, dataset]), this.form)
        .then(() => {
          if (Object.keys(this.$page.errors).length === 0) {
            this.resetForm()
          }
        })
    }
  }
}
</script>