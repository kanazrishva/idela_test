<template>
<div>

  <admin-header :title="'Add New Page'" :breadcrumbs="breadcrumbs" />

  <!-- Blocked Section -->
  <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
    <div class="py-4">
      <form @submit.prevent="submit">
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
          <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
              <h3 class="text-lg font-medium leading-6 text-gray-900">Page</h3>
              <p class="mt-1 text-sm leading-5 text-gray-500">
                Lorem ipsum
              </p>
            </div>
            <div class="mt-5 md:mt-0 md:col-span-2">
            
              <text-input v-model="form.title" :errors="error('dataset_name')" class="pr-6 pb-8" label="Name" />
              <text-area v-model="form.description" class="pr-6 pb-8" label="Description" />

    
              <loading-button type="submit">Add Page</loading-button>
          </div>
          </div>
        </div>
      </form>
        
      </div>
    </div>
  </div>
  <!-- End Block -->

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
          title: 'Pages',
          link: 'pages'
        },
      ],
      form: {
        title: null,
        description: null,
        meta: {
          title: null,
          description: null,
          ogtitle: null,
          ogdescription: null,
          ogimage: null,
          twittertitle: null,
          twitterdescription: null,
          twitterimage: null
        },
        hero: {
          expanded: true,
          description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
          title: "Lorem ipsum dolor sit amet, consectetur adipiscing elit",
          country: null,
          items: []
        }
      }
    }
  },
  methods: {
    submit() {
       this.$inertia.post(this.route('pages.store'), this.form)
    }
  }
}
</script>