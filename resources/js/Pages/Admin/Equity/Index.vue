<template>
<div>

  <admin-header :title="'Equity'">
    <search-filter v-model="form.search" class="w-half max-w-md mr-4" @reset="reset" :showFilters="false" />
  </admin-header>

 <admin-action
  :title="'Add a new equity'"
  :description="'Equity are applied to Datasets.'">
  <form @submit.prevent="submit" class="flex items-start">
    <text-input 
      v-model="equityForm.name" 
      :errors="error('equity')" 
      :placeholder="'Name'"
      class="w-full" />

    <loading-button
      class="ml-2" 
      type="submit">
      Add
    </loading-button>
  </form>
 </admin-action>

  

  <div class="max-w-7xl mx-auto px-4 py-5 sm:px-6 md:px-8">
    <!-- Replace with your content -->
    <div class="py-4">

        <div class="flex flex-col">
          <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
              <table class="min-w-full table-fixed">
                <thead>
                  <tr>
                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Name
                    </th>
                    <th class="w-1/4 px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Datasets
                    </th>
                    <th class="w-1/8 px-6 py-3 border-b border-gray-200 bg-gray-50">

                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(equity, index) in equities.data" :key="equity.id" 
                    :class="{'bg-white': index % 2 === 0, 'bg-gray-50': index % 2 !== 0 }">

                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900">
                      <div v-show="equityOffset !== equity.id">
                        {{ equity.name }}
                        <button class="px-3 py-2 text-teal-700 hover:text-teal-500" @click="startEditing(equity.id)"><i class="fad fa-edit"></i></button> 
                      </div>
                      <div v-show="equityOffset === equity.id" class="flex items-start justify-start">
                        <form class="flex items-start" @submit.prevent="valueUpdate(equity)">
                          <text-input v-model="equity.name" :errors="error('name', 'equity.' + equity.id)" class="pr-2" />
                          <loading-button type="submit"><i class="fad fa-save"></i></loading-button>
                        </form>
                        <button @click="cancelEdit()" class="pt-3 pl-4 text-teal-500 hover:text-teal-700 text-xs"><i class="fad fa-undo"></i> cancel</button>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-no-wrap text-center text-sm leading-5 text-gray-500">
                      {{ equity.datasets.length }}
                    </td>
                    <td class="px-6 py-4 whitespace-no-wrap text-right text-sm leading-5 text-gray-500">
                      <button class="px-3 py-2 text-red-700 hover:text-red-500" v-show="equity.datasets.length === 0" @click="destroyregion(equity.id)"><i class="fad fa-trash-alt"></i></button>
                    </td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr>
                    <th colspan="3" class="px-6 pb-3 bg-gray-50 border-t border-gray-200">
                      <pagination :links="equities.links" />
                    </th>
                  </tr>
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
      equityOffset: -1,
      sending: false,
      form: {
        search: this.filters.search
      },
      equityForm: {
        name: null
      }
    }
  },
  props: {
    equities: Object,
    filters: Object
  },
  watch: {
    form: {
      handler: throttle(function() {
        let query = pickBy(this.form)
        this.$inertia.replace(this.route('equities', Object.keys(query).length ? query : { remember: 'forget' }))
      }, 150),
      deep: true,
    }
  },
  methods: {
    startEditing(id) {
      this.equityOffset = id
    },
    cancelEdit() {
      this.equityOffset = -1
      this.$page.errors = {}
    },
    valueUpdate(region) {
      this.$inertia.put(this.route('equities.update', region), region)
        .then(() => {
          if (!this.$page.errors['equity.'+region.id]) {
            this.equityOffset = -1
          }
        })
    },
    destroyregion(id) {
      if (confirm('Are you sure you want to delete this equity')) {
        this.$inertia.delete(this.route('equities.destroy', id))
      }
    },
    submit() {
      this.sending = true
      this.$inertia.post(this.route('equities.store'), this.equityForm)
        .then(() => {
          this.equityForm.name = null
          this.sending = false
        })
    },
    reset() {
      this.form = mapValues(this.form, () => null)
    }
  }
}
</script>