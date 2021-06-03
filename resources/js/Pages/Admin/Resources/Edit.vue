<template>
<div>

  <admin-header :title="'Edit Resource'" :breadcrumbs="breadcrumbs">
   <button class="inline-flex items-center px-3 py-3 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-teal-500 hover:bg-teal-500 focus:outline-none focus:border-teal-700 focus:shadow-outline-teal active:bg-teal-700 transition ease-in-out duration-150"
      @click="submit">
      Update Resource
    </button>
  </admin-header>

  <!-- Blocked Section -->
  <div class="max-w-7xl mt-6 mx-auto px-4">
    <div class="bg-white px-4 py-1 border-b border-gray-200">
      <div class="flex justify-between items-center flex-wrap">

          <div class="flex items-center">
            <div class="flex-shrink-0 ml-4">
              <i class="h-12 w-12 fa-fw fad fa-newspaper"></i>
            </div>
            <div class="ml-2">
              <h3 class="leading-tight text-sm font-medium text-gray-900">
                Resource
              </h3>
              <p class="text-xs leading-tight text-gray-500">
                Update all the fields you need below to add a resource that will be available for pages.
              </p>
            </div>
          </div>
      </div>
    </div>

    <!-- Settings Bar -->
    <div class="bg-white border-b border-gray-200 px-4 py-2 grid grid-cols-1">
      <div class="col-span-1 flex justify-center items-center">
        <span class="text-xs font-bold text-gray-600 mr-2">URL</span>
        <text-input class="w-1/2" v-model="resource.link" />

      </div>
    </div>
    <!-- End Settings Bar -->

    <div class="p-4 bg-white">
      <div class="bg-blue-100">
        <!-- Content Section -->
        <resource :block="block" :editable="true" />
        <!-- End Content Section -->
      </div>
    </div>

  </div>
  <!-- End Block -->

</div>
</template>

<script>

import Admin from '@/Layouts/Admin'
import Resource from '@/PageBlocks/Resource'

import mixins from '@/Mixins/mixinAdmin.js'

export default {
  layout: Admin,
  mixins: [mixins],
  data() {
    return {
      breadcrumbs: [
        {
          title: 'Resources',
          link: 'resources'
        },
      ],
      block: {
        resource: this.resource
      }
      // form: {
      //   category: 'Category',
      //   picture: null,
      //   country: 'Country',
      //   date: 'March 12th, 2020',
      //   title: 'Lorem ipsum dolor sit amet, consectetur.',
      //   description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore',
      //   url: '#',
      // }
    }
  },
  props: {
    resource: [Object, Array]
  },
  components: {
    Resource
  },
  methods: {
    submit() {
      this.$inertia.put(this.route('resources.update', this.resource), this.block.resource)
    }
  }
}
</script>