<template>
<div>

  <admin-header :title="'Add New Resource'" :breadcrumbs="breadcrumbs">
   <button class="inline-flex items-center px-3 py-3 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-teal-500 hover:bg-teal-500 focus:outline-none focus:border-teal-700 focus:shadow-outline-teal active:bg-teal-700 transition ease-in-out duration-150"
      @click="submit">
      Create Resource
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
      <div class="my-2 col-span-1 flex justify-start items-start flex-col">
        <span class="text-xs font-bold text-gray-600 mr-2">Create From a Published Page</span>
        <multiselect 
          v-model="selectedPage"
          :options="pages"
          :allow-empty="true"
          label="title"
          class="flex items-start justify-start w-full"
          @input="selectChange($event)"
          track-by="id">
          <template slot="singleLabel" slot-scope="{ option }">
            <strong>{{ option.title }}</strong>
          </template>
        </multiselect>
      </div>

      <div class="col-span-1 my-2 flex justify-center items-start flex-col">
        <span class="text-xs font-bold text-gray-600 mr-2">URL</span>
        <text-input class="w-full" v-model="form.link" />
      </div>
    </div>
    <!-- End Settings Bar -->

    <div class="p-4 bg-white">
      <div class="bg-blue-100">
        <!-- Content Section -->
        <resource :block="{resource: form}" :editable="true" />
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
      selectedPage: {},
      form: {
        category: 'Category',
        image: null,
        country: 'Country',
        publish_date: 'March 12th, 2020',
        title: 'Lorem ipsum dolor sit amet, consectetur.',
        excerpt: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore',
        link: '#',
      }
    }
  },
  components: {
    Resource
  },
  props: {
    pages: Array
  },
  methods: {
    selectChange(evt) {
      console.log(evt)

      if (evt) { 
        this.form.category = 'Data Page'
        this.form.title = evt.title
        this.form.excerpt = evt.description
        this.form.country = evt.hero.country
        this.form.link  = 'https://data.idela-network.org/data/' + evt.slug
      } else {
        this.form = {
          category: 'Category',
          image: null,
          country: 'Country',
          publish_date: 'March 12th, 2020',
          title: 'Lorem ipsum dolor sit amet, consectetur.',
          excerpt: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore',
          link: '#',
        }
      }
    },
    submit() {
       this.$inertia.post(this.route('resources.store'), this.form)
    }
  }
}
</script>