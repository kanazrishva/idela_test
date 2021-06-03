<template>
<div>

  <admin-header :title="'Users'" />

  <admin-action 
    :title="'Add a new User'"
    :description="'Adding a new user will provide them access to this system.'">
    <inertia-link  :href="route('users.create')" class="inline-flex items-center px-3 py-3 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-teal-500 hover:bg-teal-500 focus:outline-none focus:border-teal-700 focus:shadow-outline-teal active:bg-teal-700 transition ease-in-out duration-150">
      Add New User
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
                    User
                  </th>
                  <th class="w-1/4 px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                    Email Address
                  </th>
                  <th class="w-1/2 px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                    &nbsp;
                  </th>
                </tr>
              </thead>
              <tbody>
               
                <tr v-for="(user, index) in users.data" :key="user.id"
                  :class="{'bg-white': index % 2 === 0, 'bg-gray-50': index % 2 !== 0 }">
                  <td class="px-6 py-4 whitespace-no-wrap text-left text-sm leading-5 text-gray-500">
                    {{ user.name }}
                  </td>
                  <td class="px-6 py-4 whitespace-no-wrap text-left text-sm leading-5 text-gray-500">
                    {{ user.email }}
                  </td>
                  <td class="px-6 py-4 whitespace-no-wrap text-right text-sm leading-5 text-gray-500">
                    <inertia-link :href="route('users.edit', user.id)" class="px-3 py-2 text-teal-700 text-xs hover:text-teal-500">
                      <i class="fad fa-edit"></i> edit
                    </inertia-link>
                    <button class="px-3 py-2 text-red-700 hover:text-red-500" @click="destroy(user.id)"><i class="fad fa-trash-alt"></i></button>
                  </td>
                </tr>
              </tbody>
              <tfoot>
                <th colspan="4" class="px-6 pb-3 bg-gray-50 border-t border-gray-200">
                  <pagination :links="users.links" />
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

export default {
  layout: Admin,
  mixins: [mixins],
  props: {
    users: Object,
  },
  methods: {
    destroy(id) {
      if (confirm('Are you sure you want to remove this user?')) {
        this.$inertia.delete(this.route('users.destroy', id))
      }
    }
  }
}
</script>