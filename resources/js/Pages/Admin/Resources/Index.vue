<template>
<div>

  <admin-header :title="'Resources'">
    <search-filter v-model="form.search" :showFilters="false" class="w-full max-w-md mr-4" @reset="reset" />
  </admin-header>

  <admin-action 
    :title="'Adding a new Resource'"
    :description="'Resources are linked into Pages'">
    <inertia-link
      class="inline-flex items-center px-3 py-3 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-teal-500 hover:bg-teal-500 focus:outline-none focus:border-teal-700 focus:shadow-outline-teal active:bg-teal-700 transition ease-in-out duration-150"
      :href="route('resources.create')" >
      Add New Resource
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
              <tr v-for="(resource, index) in resources.data" :key="resource.id"
                :class="{'bg-white': index % 2 === 0, 'bg-gray-50': index % 2 !== 0 }">
                <td class="px-4 py-4 sm:px-6">
                  <div class="flex items-center">
                    <div class="mr-4 flex-none w-32">
                      <img v-if="resource.image" :src="imgix_domain + resource.image+'?w=100&h=50&fit=crop'">
                      <img v-if="!resource.image" :src="imgix_domain + 'public/misc/resources-placeholder.jpg?w=100&h=50&fit=crop'">
                    </div>

                    <div class="flex flex-auto items-center justify-between">
                      <div class="text-sm leading-5 font-medium text-gray-900">
                        <div class="text-xs uppercase">{{ resource.category }}</div>
                        <div class="text-sm mb-1" v-html="resource.title" />
                        <div class="text-xs leading-tight text-gray-600" v-html="resource.excerpt" />
                        <span class="text-xxs text-teal-600"><a :href="resource.link" target="_blank">{{ resource.link }}</a></span>

                      </div>
                      <div class="ml-2 flex-shrink-0 flex">
                      
                      </div>
                    </div>
                    
                    <div class="mt-2 flex flex-none w-48 self-end justify-end">
                      <div class="ml-2 justify-end flex">
                        <inertia-link :href="route('resources.edit', resource)" class="px-3 py-2 text-teal-700 text-xs hover:text-teal-500">
                          <i class="fad fa-edit"></i> edit
                        </inertia-link>

                        <button @click="destroy(resource.id)" class="px-3 py-2 text-red-700 text-xs hover:text-red-500">
                          <i class="fad fa-trash-alt"></i> delete
                        </button>
                      </div>
                    </div>


                  </div>



                  
                </td>
              </tr>

              <tr v-if="resources.data.length === 0">
                <td class="px-6 py-4 whitespace-no-wrap text-center text-sm leading-5 text-gray-900">
                  No resources found.
                </td>
              </tr>
              </tbody>
              <tfoot>
                <th colspan="4" class="px-6 pb-3 bg-gray-50 border-t border-gray-200">
                  <pagination :links="resources.links" />
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
  layout: Admin,
  mixins: [mixins],
  data() {
    return {
      form: {
        search: this.filters.search
      }
    }
  },
  props: {
    resources: [Object, Array],
    filters: Object
  },
  watch: {
    form: {
      handler: Vue.lodash.throttle(function() {
        let query = Vue.lodash.pickBy(this.form)
        this.$inertia.replace(this.route('resources', Object.keys(query).length ? query : { remember: 'forget' }))
      }, 150),
      deep: true,
    }
  },
  methods: {
    reset() {
      this.form = Vue.lodash.mapValues(this.form, () => null)
    },
    destroy(resource) {
      if (confirm('Are you sure you want to delete this resource?')) {
        this.$inertia.delete(this.route('resources.destroy', resource))
      }
    }
  }
}
</script>