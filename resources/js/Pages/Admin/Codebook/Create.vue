<template>
<div>
  <admin-header :title="'Add New Codebook'" :breadcrumbs="breadcrumbs" />


  <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
    <div class="py-4">

      <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
        <div class="md:grid md:grid-cols-3 md:gap-6">
          <div class="md:col-span-1">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Codebook Item</h3>
            <p class="mt-1 text-sm leading-5 text-gray-500">
              The column name should be the processed column name that is output from the excel import.
            </p>
          </div>
          <div class="mt-5 md:mt-0 md:col-span-2">
            <form @submit.prevent="submit">
              <text-input v-model="form.matching_name" :errors="error('matching_name')" class="pr-6 pb-8 w-full" label="Codebook Name" />

              <select-input v-model="form.type" :errors="error('type')" class="pr-6 pb-8 w-full" label="Type of Column">
                <option value="String">String</option>
                <option value="Boolean">Boolean</option>
                <option value="Integer">Integer</option>
                <option value="Decimal">Decimal</option>
              </select-input>

              <text-input v-model="form.matching_description" :errors="error('matching_description')" class="pr-6 pb-8 w-full" label="Description" />

              <loading-button :loading="sending" class="btn-indigo" type="submit">Create Codebook Item</loading-button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
    
</div>
</template>

<script>

import Admin from '@/Layouts/Admin.vue'
import mixins from '@/Mixins/mixinAdmin.js'

export default {
  layout: Admin,
  mixins: [mixins],
  data() {
    return {
      breadcrumbs: [
        {
          title: 'Codebook',
          link: 'codebook'
        },
      ],
      sending: false,
      form: {
        type: null,
        matching_name: null,
        matching_description: null
      }
    }
  },
  remember: 'form',
  methods: {
    submit() {
      this.sending = true
      this.$inertia.post(this.route('codebook.store'), this.form)
        .then(() => this.sending = false)
    }
  }
}
</script>