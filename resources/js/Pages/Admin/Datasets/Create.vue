<template>
<div>
  <admin-header :title="'Add New Dataset'" :breadcrumbs="breadcrumbs" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
      <!-- Replace with your content -->
      <div class="py-4">
        <form @submit.prevent="submit">
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
          <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
              <h3 class="text-lg font-medium leading-6 text-gray-900">Dataset</h3>
              <p class="mt-1 text-sm leading-5 text-gray-500">
                All fields are required except for a description. The dataset name is currently only used internally. This information can also be edited later.
              </p>
            </div>
            <div class="mt-5 md:mt-0 md:col-span-2">
              
                <text-input v-model="form.dataset_name" :errors="error('dataset_name')" class="pr-6 pb-8" label="Name" />
                <text-area v-model="form.dataset_description" class="pr-6 pb-8" label="Dataset Description" />

                <span class="flex items-start justify-start">
                  <select-input v-model="form.dataset_year" :errors="error('dataset_year')" class="pr-6 pb-8 w-full" label="Year">
                    <option value="2020">2020</option>
                    <option value="2019">2019</option>
                    <option value="2018">2018</option>
                    <option value="2017">2017</option>
                    <option value="2016">2016</option>
                    <option value="2015">2015</option>
                    <option value="2014">2014</option>
                    <option value="2013">2013</option>
                    <option value="2012">2012</option>
                    <option value="2011">2011</option>
                    <option value="2010">2010</option>
                  </select-input>

                  <select-input v-model="form.region_id" :errors="error('region_id')" class="pr-6 pb-8 w-full" label="Region">
                    <option v-for="region in regions" :key="region.id" :value="region.id">{{ region.name }}</option>
                  </select-input>

                  <select-input v-model="form.country_id" :errors="error('country_id')" class="pr-6 pb-8 w-full" label="Country">
                    <option v-for="country in countries" :key="country.id" :value="country.id">{{ country.country }}</option>
                  </select-input>
                </span>

                <file-input v-model="form.dataset" :errors="error('dataset')" class="pr-6 pb-8 w-full" type="file" accept="xlsx" label="File" />
            </div>
          </div>
        </div>

      <div class="bg-white shadow px-4 py-5 mt-8 rounded-lg">
        <div class="md:grid md:grid-cols-3 md:gap-6">
          <div class="md:col-span-1">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Organizations</h3>
            <p class="mt-1 text-sm leading-5 text-gray-500">
              Assign the dataset to an/or organization(s). If one is not listed, you can add one and update the dataset after.
            </p>
          </div>
          <div class="mt-5 md:mt-0 md:col-span-2">

              <div v-for="(organization, index) in organizations" :key="index">
                
                <div class="relative flex items-start">
                  <div class="absolute flex items-center h-5">
                    <input 
                      v-model="form.organization"
                      type="checkbox"
                      :value="organization.id"
                      class="form-checkbox h-4 w-4 text-teal-500 transition duration-150 ease-in-out" />
                  </div>
                  <div class="pl-7 text-sm leading-5">
                    <label class="block text-sm font-medium leading-5 text-gray-700">
                      {{ organization.name }}
                    </label>
                  </div>
                </div>
              
              </div>
          </div>
        </div>
      </div>

      <div class="bg-white shadow px-4 py-5 mt-8 rounded-lg">
        <div class="md:grid md:grid-cols-3 md:gap-6">
          <div class="md:col-span-1">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Equities</h3>
            <p class="mt-1 text-sm leading-5 text-gray-500">
              Assign the dataset to an/or equity(ies). If one is not listed, you can add one and update the dataset after.
            </p>
          </div>
          <div class="mt-5 md:mt-0 md:col-span-2">
              <div v-for="(equity, index) in equities" :key="index">
                
                <div class="relative flex items-start">
                  <div class="absolute flex items-center h-5">
                    <input 
                      v-model="form.equity"
                      type="checkbox"
                      :value="equity.id"
                      class="form-checkbox h-4 w-4 text-teal-500 transition duration-150 ease-in-out" />
                  </div>
                  <div class="pl-7 text-sm leading-5">
                    <label class="block text-sm font-medium leading-5 text-gray-700">
                      {{ equity.name }}
                    </label>
                  </div>
                </div>
              
              </div>
          </div>
        </div>
      </div>

      <div class="bg-white shadow px-4 py-5 mt-8 rounded-lg">
        <div class="md:grid md:grid-cols-3 md:gap-6">
          <div class="md:col-span-1">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Programs</h3>
            <p class="mt-1 text-sm leading-5 text-gray-500">
              Assign the dataset to an/or program(s). If one is not listed, you can add one and update the dataset after.
            </p>
          </div>
          <div class="mt-5 md:mt-0 md:col-span-2">
              <div v-for="(program, index) in programs" :key="index">
                
                <div class="relative flex items-start">
                  <div class="absolute flex items-center h-5">
                    <input 
                      v-model="form.program"
                      type="checkbox"
                      :value="program.id"
                      class="form-checkbox h-4 w-4 text-teal-500 transition duration-150 ease-in-out" />
                  </div>
                  <div class="pl-7 text-sm leading-5">
                    <label class="block text-sm font-medium leading-5 text-gray-700">
                      {{ program.name }}
                    </label>
                  </div>
                </div>
              
              </div>
          </div>
        </div>
      </div>

        <div class="bg-white shadow px-4 py-5 mt-8 rounded-lg">
          <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
              <h3 class="text-lg font-medium leading-6 text-gray-900">Verify Your Excel</h3>
              <p class="mt-1 text-sm leading-5 text-gray-500">
                To ensure accuracy with uploading your excel dataset, please verify the following has been completed.
              </p>
            </div>
            <div class="mt-5 md:mt-0 md:col-span-2">

              <checkbox v-model="form.verify1" :errors="error('verify1')" class="pb-6" label="Headers should match codebook matched names" description="Headers should be lowercase and slugged such as num1, or age_months" />
              <checkbox v-model="form.verify2" :errors="error('verify2')" class="pb-6" label="Verify any text entries are under 150 characters" description="If any text is longer than 150 chaacters in a cell, it will be truncated" />
              <checkbox v-model="form.verify3" :errors="error('verify3')" class="pb-6" label="Check for random variables in columns" description="Make sure there are no random entries in columns. e.g. a 99 in a column that only should have a 0 or 1" />
              <checkbox v-model="form.verify4" :errors="error('verify4')" class="pb-6" label="Convert the equations" description="Equations will not convert at time of import, make sure they have been converted to their results" />
              <checkbox v-model="form.verify5" :errors="error('verify5')" class="pb-6" label="Clean the extra rows" description="Sometimes there are extra rows with blank information. A quick selection and delete can clean these up" />
              <checkbox v-model="form.verify6" :errors="error('verify6')" class="pb-6" label="Check data for granularity" description="Optional, but required if you want to make detailed graphs beyond total scores and helpful for data validation" />
              <checkbox v-model="form.verify7" :errors="error('verify7')" class="pb-6" label="Total observation verification" description="If there are variable categories with less than 20 observations (e.g., number of children with age 7), please delete these data points." />

              <loading-button :loading="sending" type="submit">Upload & Create Dataset</loading-button>
              
            </div>
          </div>
        </div>
        </form>
          
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
  data() {
    return {
      breadcrumbs: [
        {
          title: 'Datasets',
          link: 'datasets'
        },
      ],
      sending: false,
      form: {
        dataset_name: null,
        dataset_description: null,
        dataset_year: null,
        country_id: null,
        region_id: null,
        organization: [],
        equity: [],
        program: [],
        dataset: null,
        verify1: false,
        verify2: false,
        verify3: false,
        verify4: false,
        verify5: false,
        verify6: false,
        verify7: false
      }
    }
  },
  props: {
    countries: Array,
    regions: Array,
    organizations: [Object, Array],
    programs: [Object, Array],
    equities: [Object, Array]
  },
  remember: 'form',
  methods: {
    submit() {
      this.sending = true
      this.$inertia.post(this.route('datasets.store'), this.form)
        .then(() => this.sending = false)
    }
  }
}
</script>