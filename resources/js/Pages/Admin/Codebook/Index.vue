<template>
<div>

  <admin-header :title="'Codebook'">
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
    :title="'Add a new Codebook Item'"
    :description="'Adding a new codebook item will help with processing datasets and ensuring calculations are accurate.'">
    <inertia-link  :href="route('codebook.create')" class="inline-flex items-center px-3 py-3 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-teal-500 hover:bg-teal-500 focus:outline-none focus:border-teal-700 focus:shadow-outline-teal active:bg-teal-700 transition ease-in-out duration-150">
      Add New Item
    </inertia-link>
  </admin-action>

  
  <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
    <!-- Replace with your content -->
    <div class="py-4">


      <div class="flex flex-col">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
          <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
            <table class="min-w-full table-fixed">
              <thead>
                <tr>
                  <th class="w-1/4 px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                    Columns in Dataset
                  </th>
                  <th class="w-1/2 px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                    Label & Description
                  </th>
                  <th class="w-1/8 px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                    Datatype
                  </th>
                  <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-right">
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="codebook.data.length === 0">
                  <td colspan="4" class="px-6 py-4 whitespace-no-wrap text-center text-sm leading-5 text-gray-900">
                    No results available.
                  </td>
                </tr>
                <tr v-for="(codeitem, index) in codebook.data" :key="codeitem.id"
                  :class="{'bg-white': index % 2 === 0, 'bg-gray-50': index % 2 !== 0 }">
                  <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900">
                    <span v-for="column in codeitem.codebook_columns" class="inline-flex items-center px-2.5 py-0.5 mx-0.5 rounded-full text-xs font-medium leading-4 bg-teal-100 text-teal-800">
                    {{ column.column_name }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-no-wrap text-left text-sm leading-5 text-gray-900">
                    <span class="text-bold">{{ codeitem.matching_name }}</span><br>
                    <span class="text-xs text-gray-700">{{ codeitem.matching_description || 'no desciption' }}</span>
                  </td>
                  <td class="px-6 py-4 whitespace-no-wrap text-center text-sm leading-5 text-gray-700">
                    {{ codeitem.type }}
                  </td>
                  <td class='px-6 py-4 whitespace-no-wrap text-right text-sm leading-5 text-gray-700'>
                      <div v-show="codeitem.deleted_at == null">
                        <inertia-link :href="route('codebook.edit', codeitem.id)" class="px-3 py-2 text-xs text-teal-700 hover:text-teal-500">
                          <i class="fad fa-edit"></i> edit 
                        </inertia-link>
                        <!-- <button 
                          @click="remove(codeitem.id)" 
                          class="px-3 py-2 text-red-700 text-xs hover:text-red-500">
                          <i class="fad fa-trash-alt"></i> delete
                        </button> -->
                        
                      </div>


                      <div v-show="codeitem.deleted_at !== null" class="flex items-center">
                        <button 
                          @click="restore(codeitem.id)" 
                          class="px-3 py-2 text-yellow-700 text-xs hover:text-yellow-500">
                          <i class="fad fa-trash-undo"></i> restore
                        </button>

                        <!-- <button 
                          @click="destroy(codeitem.id)" 
                          class="px-3 py-2 text-red-700 text-xs hover:text-red-500">
                          <i class="fad fa-trash-alt"></i> delete
                        </button> -->
                      </div>
                  </td>
                </tr>
              </tbody>
              <tfoot>
                <th colspan="4" class="px-6 pb-3 bg-gray-50 border-t border-gray-200">
                  <pagination :links="codebook.links" />
                </th>
              </tfoot>
            </table>
          </div>
        </div>
      </div>


    </div>
    <!-- /End replace -->
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
    codebook: Object,
    filters: Object
  },
  watch: {
    form: {
      handler: throttle(function() {
        let query = pickBy(this.form)
        this.$inertia.replace(this.route('codebook', Object.keys(query).length ? query : { remember: 'forget' }))
      }, 150),
      deep: true,
    }
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
    remove(id) {
      if (confirm('Are you sure you want to remove this codebook? It will detach all values associated with it but leave existing datasets and equations unaffected.')) {
        this.$inertia.delete(this.route('codebook.remove', id))
      }
    },
    restore(id) {
      this.$inertia.put(this.route('codebook.restore', id))
    },
    // destroy(id) {
    //   if (confirm('Removing this codebook is a permanent action and cannot be reversed and may affect equations if not updated previously. Do you want to remove this item permanently?')) {
    //     this.$inertia.post(this.route('codebook.destroy', id))
    //   }
    // },
  }
}
</script>