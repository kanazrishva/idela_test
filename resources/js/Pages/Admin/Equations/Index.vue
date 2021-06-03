<template>
<div>

  <admin-header :title="'Equations'">
    <search-filter v-model="form.search" class="w-half max-w-md mr-4" @reset="reset" :showFilters="false" />
  </admin-header>
  <admin-action 
    :title="'Add a new Equation'"
    :description="'Crafiting an equation will ensure you can add your data properly into a graph.'">
    <inertia-link
      class="inline-flex items-center px-3 py-3 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-teal-500 hover:bg-teal-500 focus:outline-none focus:border-teal-700 focus:shadow-outline-teal active:bg-teal-700 transition ease-in-out duration-150"
      :href="route('equations.create')" >
      Add New Equation
    </inertia-link>
  </admin-action>


  <div class="max-w-7xl mx-auto px-4 py-5 sm:px-6 md:px-8">
    <div class="py-4">

        <div class="flex flex-col">
          <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
              <table class="min-w-full table-fixed">
                <thead>
                  <tr>
                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Equation
                    </th>
                    <th class="w-1/8 px-6 py-3 border-b border-gray-200 bg-gray-50">

                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(equation, index) in equations.data" :key="index" 
                    :class="{'bg-white': index % 2 === 0, 'bg-gray-50': index % 2 !== 0 }">

                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900">
                        {{ equation.name }}<br>
                        <span class="text-xs font-normal">{{ equation.description }}</span>
                    </td>

                    <td class="px-6 py-4 whitespace-no-wrap text-right text-sm leading-5 text-gray-500">
                      <inertia-link :href="route('equations.edit', equation.id)" class="px-3 py-2 text-xs text-teal-700 hover:text-teal-500"><i class="fad fa-edit"></i> edit</inertia-link> 
                      <!-- <inertia-link :href="route('equations.show', equation.id)" class="px-3 py-2 text-xs text-teal-700 hover:text-teal-500"><i class="fad fa-vial"></i> sandbox</inertia-link>  -->
                      <button class="px-3 py-2 text-xs text-red-700 hover:text-red-500" @click="destroy(equation.id)"><i class="fad fa-trash-alt"></i> delete</button>
                    </td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr>
                    <th colspan="3" class="px-6 pb-3 border-gray-200 bg-gray-50 border-t">
                      <pagination :links="equations.links" />
                    </th>
                  </tr>
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
        search: this.filters.search
      }
    }
  },
  props: {
    equations: Object,
    filters: Object
  },
  watch: {
    form: {
      handler: throttle(function() {
        let query = pickBy(this.form)
        this.$inertia.replace(this.route('equations', Object.keys(query).length ? query : { remember: 'forget' }))
      }, 150),
      deep: true,
    }
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
    destroy(id) {
      if (confirm('Are you sure you want to delete this equation?')) {
        this.$inertia.delete(this.route('equations.destroy', id))
      }
    }
  }
}
</script>