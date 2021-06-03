<template>
<div>

  <admin-header :title="'Duplicate Page'" :breadcrumbs="breadcrumbs" />

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
              <multiselect
                v-model="form.dataset"
                :options="datasets"
                :hideSelected="false"
                :clear-on-select="false"
                :multiple="false"
                :limit="1"
                :allowEmpty="true"
                :showLabels="false"
                placeholder="Select at least one dataset"
                label="dataset_name"
                class="w-full mr-6"
                track-by="id">
                <!-- <template slot="selection" slot-scope="{ values, search, isOpen}">
                  <span v-if="(values.length > 1) && !isOpen">{{ values.length }} datasets selected</span>
                </template> -->
                <!-- <template slot="tag" slot-scope="{ option, remove }">
                  <span class="multiselect__single">{{ option.dataset_name }}</span>
                </template> -->
                <template slot="option" slot-scope="{option}">
                  <div class="text-black">{{ option.dataset_name }}</div>
                </template>


              </multiselect>
    
              <loading-button type="submit">Duplicate Page</loading-button>
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
        page: this.page,
        title: null,
        description: null,
        dataset: null,
      }
    }
  },
  props: {
    page: [Object, Array],
    datasets: [Object, Array]
  },
  methods: {
    submit() {
       this.$inertia.post(this.route('pages.duplicate'), this.form)
    }
  }
}
</script>