<template>
<div>

  <admin-header :title="'Organizations'">
    <search-filter v-model="form.search" class="w-half max-w-md mr-4" @reset="reset" :showFilters="false" />
  </admin-header>

 <admin-action
  :title="'Add a new organization'"
  :description="'Organizations are applied to Datasets.'">
  <form @submit.prevent="submit" class="flex items-start">
    <text-input 
      v-model="organzationForm.name" 
      :errors="error('organization')" 
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
                  <tr v-for="(organization, index) in organizations.data" :key="organization.id" 
                    :class="{'bg-white': index % 2 === 0, 'bg-gray-50': index % 2 !== 0 }">

                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900">
                      <div v-show="organizationOffset !== organization.id">
                        {{ organization.name }}
                        <button class="px-3 py-2 text-teal-700 hover:text-teal-500" @click="startEditing(organization.id)"><i class="fad fa-edit"></i></button> 
                      </div>
                      <div v-show="organizationOffset === organization.id" class="flex items-start justify-start">
                        <form class="flex items-start" @submit.prevent="valueUpdate(organization)">
                          <text-input v-model="organization.name" :errors="error('name', 'organization.' + organization.id)" class="pr-2" />
                          <loading-button type="submit"><i class="fad fa-save"></i></loading-button>
                        </form>
                        <button @click="cancelEdit()" class="pt-3 pl-4 text-teal-500 hover:text-teal-700 text-xs"><i class="fad fa-undo"></i> cancel</button>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-no-wrap text-center text-sm leading-5 text-gray-500">
                      {{ organization.datasets.length }}
                    </td>
                    <td class="px-6 py-4 whitespace-no-wrap text-right text-sm leading-5 text-gray-500">
                      <button class="px-3 py-2 text-red-700 hover:text-red-500" v-show="organization.datasets.length === 0" @click="destroyregion(organization.id)"><i class="fad fa-trash-alt"></i></button>
                    </td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr>
                    <th colspan="3" class="px-6 pb-3 bg-gray-50 border-t border-gray-200">
                      <pagination :links="organizations.links" />
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
      organizationOffset: -1,
      sending: false,
      form: {
        search: this.filters.search
      },
      organzationForm: {
        name: null
      }
    }
  },
  props: {
    organizations: Object,
    filters: Object
  },
  watch: {
    form: {
      handler: throttle(function() {
        let query = pickBy(this.form)
        this.$inertia.replace(this.route('organizations', Object.keys(query).length ? query : { remember: 'forget' }))
      }, 150),
      deep: true,
    }
  },
  methods: {
    startEditing(id) {
      this.organizationOffset = id
    },
    cancelEdit() {
      this.organizationOffset = -1
      this.$page.errors = {}
    },
    valueUpdate(region) {
      this.$inertia.put(this.route('organizations.update', region), region)
        .then(() => {
          if (!this.$page.errors['organization.'+region.id]) {
            this.organizationOffset = -1
          }
        })
    },
    destroyregion(id) {
      if (confirm('Are you sure you want to delete this organization')) {
        this.$inertia.delete(this.route('organizations.destroy', id))
      }
    },
    submit() {
      this.sending = true
      this.$inertia.post(this.route('organizations.store'), this.organzationForm)
        .then(() => {
          this.organzationForm.name = null
          this.sending = false
        })
    },
    reset() {
      this.form = mapValues(this.form, () => null)
    }
  }
}
</script>