<template>
<div>

  <admin-header :title="'Pages'">
    <search-filter v-model="form.search" :showFilters="false" class="w-full max-w-md mr-4" @reset="reset" />
  </admin-header>

  <admin-action 
    :title="'Add a new Page'"
    :description="'Add a public facing page to the site.'">
    <inertia-link 
      :href="route('pages.create')" 
      class="inline-flex items-center px-3 py-3 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-teal-500 hover:bg-teal-500 focus:outline-none focus:border-teal-700 focus:shadow-outline-teal active:bg-teal-700 transition ease-in-out duration-150">
      Add New Page
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
              <tr v-for="(page, index) in pages.data" :key="pages.id"
                :class="{'bg-white': index % 2 === 0, 'bg-gray-50': index % 2 !== 0 }">
                <td class="px-4 py-4 sm:px-6">
                  <div class="flex items-center justify-between">
                    <div class="text-sm leading-5 font-medium text-gray-800">
                       <span class="truncate">{{ page.title }}</span><br>
                       <p class="text-xs text-gray-600 max-w-2xl leading-tight mt-1">{{ page.description }}</p>
                       <p class="text-xs mt-3 text-gray-500">/data/{{ page.slug }}</p>
                    </div>
                    <div class="ml-2 flex-shrink-0 flex">
                      <div class="flex my-4 text-xs font-bold justify-end items-center">
                        publish
                        <span 
                          @click="publish(page)" 
                          class="bg-teal-100 ml-3 relative inline-block flex-shrink-0 h-6 w-11 border-3 border-transparent rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
                          <!-- On: "translate-x-5", Off: "translate-x-0" -->
                          <span aria-hidden="true" 
                            :class="page.publish ? 'translate-x-5 bg-teal-500' : 'translate-x-0 bg-gray-400'"
                            class="translate-x-0 inline-block h-5 w-5 rounded-sm shadow transform transition ease-in-out duration-200"></span>
                        </span>
                      </div>
                    </div>
                  </div>

                  <div class="mt-2 sm:flex sm:justify-between">
                    <div class="flex">
                      
                    </div>
                    <div class="ml-2 flex-end flex">
                      <inertia-link :href="route('pages.edit', page)" class="px-3 py-2 text-teal-700 text-xs hover:text-teal-500">
                        <i class="fad fa-edit"></i> edit
                      </inertia-link>

                      <inertia-link :href="route('pages.copy', page)" class="px-3 py-2 text-teal-700 text-xs hover:text-teal-500">
                        <i class="fad fa-clone"></i> duplicate
                      </inertia-link>

                      <a class="px-3 py-2 text-teal-700 text-xs hover:text-teal-500" 
                        v-show="!page.publish"
                        :href="route('pages.show', page.id)"
                        target="_blank">
                        <i class="fad fa-telescope"></i> preview
                      </a>

                      <a class="px-3 py-2 text-teal-700 text-xs hover:text-teal-500" 
                        v-show="page.publish" 
                        :href="route('page', page.slug)" 
                        target="_blank">
                        <i class="fad fa-external-link"></i> view
                      </a>

                      <button @click="destroy(page.id)" class="px-3 py-2 text-red-700 text-xs hover:text-red-500">
                        <i class="fad fa-trash-alt"></i> delete
                      </button>
                    </div>
                  </div>

                  
                </td>
              </tr>

              <tr v-if="pages.data.length === 0">
                <td class="px-6 py-4 whitespace-no-wrap text-center text-sm leading-5 text-gray-900">
                  No pages found.
                </td>
              </tr>
              </tbody>
              <tfoot>
                <th colspan="4" class="px-6 pb-3 bg-gray-50 border-t border-gray-200">
                  <pagination :links="pages.links" />
                </th>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End of Table -->

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
  data() {
    return {
      form: {
        search: this.filters.search,
      },
    }
  },
  layout: Admin,
  mixins: [mixins],
  props: {
    pages: Object,
    filters: Object
  },
  watch: {
    form: {
      handler: Vue.lodash.throttle(function() {
        let query = Vue.lodash.pickBy(this.form)
        this.$inertia.replace(this.route('pages', Object.keys(query).length ? query : { remember: 'forget' }))
      }, 150),
      deep: true,
    }
  },
  methods: {
    reset() {
      this.form = Vue.lodash.mapValues(this.form, () => null)
    },
    duplicate(page) {
      this.$inertia.post(this.route('pages.duplicate'), {'page': page})
    },
    destroy(page) {
      if (confirm('Are you sure you want to delete this page?')) {
        this.$inertia.delete(this.route('pages.destroy', page))
      }
    },
    publish(page) {
      let publish = !page.publish
      this.$inertia.put(this.route('pages.publish', page), {publish: publish})
    },
  }
}
</script>