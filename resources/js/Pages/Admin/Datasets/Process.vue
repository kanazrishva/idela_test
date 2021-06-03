<template>
<div>
  <admin-header :title="dataset.dataset_name + ' Processing'" :breadcrumbs="breadcrumbs" />

  <div class="max-w-7xl mx-auto px-4 py-5 lg:px-8">
    <div class="bg-white shadow sm:rounded-lg sm:p-6">
      <div class="flex items-center justify-between">
        <div class="max-w-3xl">
          <h3 class="text-lg font-medium leading-6 text-gray-900">Processing a Dataset</h3>
          <p class="mt-1 text-sm leading-5 text-gray-500">
            If a column of the excel file matches a codebook column, it will be automaticaly assigned to that column upon processing. You can also update each value after an import. Only columns that are mapped to a codebook item can be used in equations and subsequently in graphs.
          </p>
        </div>

        <div class="flex items-center justify-end">
          <button 
            @click="importexcel" 
            :disabled="disabled" 
            class="inline-flex items-center px-3 py-3 border border-transparent text-sm leading-4 font-medium rounded-md text-white transition ease-in-out duration-150"
            :class="(disabled) ? 'bg-gray-400 cursor-default' :'bg-teal-500 hover:bg-teal-500 focus:outline-none focus:border-teal-700 focus:shadow-outline-teal active:bg-teal-700'">
            Import Data
          </button>
        </div>
      </div>
    </div>
  </div>


  <div  v-if="this.$page.errors.hasOwnProperty('default')" class="max-w-7xl mx-auto px-4 py-2 lg:px-8 my-2">
    <div class="bg-red-500 text-white text-center text-xs shadow sm:rounded-lg sm:p-6">
      <p>There are errors with conflicting codebook items. Make sure to scroll down to isolate the items that are duplicated.</p>
    </div>
  </div>


  <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
    <!-- Replace with your content -->
    <div class="py-4">

      <div class="flex flex-col">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
          <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
            <table class="min-w-full table-fixed">
              <thead>
              <tr>
                <th class="w-1/3 px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Excel Header
                </th>
                <th class="w-2/3 px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Codebook Item
                </th>
              </tr>
              </thead>
              <tr v-for="(excel, index) in excel" :key="index"
                :class="{'bg-white': index % 2 === 0, 'bg-gray-50': index % 2 !== 0 }">
                <td class="px-6 py-4 whitespace-no-wrap text-left text-sm leading-5 text-gray-900">{{ excel.header }}</td>
                <td class="px-6 py-4 whitespace-no-wrap text-left text-sm leading-5 text-gray-700">

                    <multiselect 
                      v-model="form.excel[index].codebook"
                      :options="codebook"
                      :allow-empty="true"
                      label="matching_name"
                      class="flex items-start justify-start w-full"
                      @input="selectChange(index, $event)"
                      track-by="id">
                      <template slot="singleLabel" slot-scope="{ option }">
                        <strong>{{ option.matching_name }}</strong>
                      </template>
                    </multiselect>

                  <div v-if="error(`excel.${index}.codebook.id`).length" class="mt-1 text-xs text-red-600">
                    This codebook item has already been assigned elsewhere in the dataset.
                  </div>

                </td>
              </tr>
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
      disabled: false,
      form: {
        excel: this.excel
      }
    }
  },
  props: {
    excel: Array,
    codebook: Array,
    dataset: Object
  },
  methods: {
    selectChange(index, $event) {
      this.form.excel[index].codebook = $event
    },
    importexcel() {
      this.disabled = true
      this.$inertia.post(this.route('datasets.import', this.dataset), this.form)
        .then(() => {
          this.sending = false
          this.disabled = false
        })
    }
  }
}
</script>