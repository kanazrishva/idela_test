<template>
<div>
  <admin-header :title="'Edit Dataset'" :breadcrumbs="breadcrumbs" />

  <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
    <div class="py-4">

      <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
        <div class="md:grid md:grid-cols-3 md:gap-6">
          <div class="md:col-span-1">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Dataset</h3>
            <p class="mt-1 text-sm leading-5 text-gray-500">
              All fields are required except for a description. The dataset name is currently only used internally.
            </p>
          </div>
          <div class="mt-5 md:mt-0 md:col-span-2">
            <text-input v-model="form.dataset_name" :errors="error('dataset_name')" class="pr-6 pb-8" label="Name" />
            <text-area v-model="form.dataset_description" class="pr-6 pb-8" label="Description" />

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
        
            </form>
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
      <div class="md:grid grid-cols-3 md:gap-6">
        <div class="col-span-1 col-start-2">
          <button @click="submit" class="btn-create" type="submit">Update Dataset</button>
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
        dataset_name: this.dataset.dataset_name,
        dataset_description: this.dataset.dataset_description,
        dataset_year: this.dataset.dataset_year,
        country_id: this.dataset.country_id,
        region_id: this.dataset.region_id,
        organization: this.dataset.organizations,
        equity: this.dataset.equities,
        program: this.dataset.programs,
      }
    }
  },
  props: {
    dataset: Object,
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
      this.$inertia.put(this.route('datasets.update', this.dataset.id), this.form)
        .then(() => this.sending = false)
    }
  }
}
</script>