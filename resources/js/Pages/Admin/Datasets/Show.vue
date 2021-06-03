<template>
<div>
  <admin-header :title="dataset.dataset_name + ' Raw Data'" :breadcrumbs="breadcrumbs" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
      <!-- Replace with your content -->
      <div class="py-4">
        <div class="">

            <table class="w-full whitespace-no-wrap table-auto">
              <thead>
              <tr>
                <th v-for="header in headers" :key="header.id" class="border px-4 py-2 text-sm">
                  {{ header.column_name }}
                </th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="(result, index) in results" :key="index">
                <td v-for="datarow in result" :key="datarow.id" class="border px-4 py-2 text-xs text-center">
                  {{ datarow.value }}
                  <!--<br><span class="text-xs">{{ datarow.column_name }}</span>-->
                </td>
              </tr>
              </tbody>
            </table>

             <pagination :links="dataset.links" />
        </div>
      </div>
      <!-- /End replace -->
    </div>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
  <div class="px-4 flex items-center justify-between sm:px-0">
    <div v-if="page === 0" 
      class="w-0 flex-1 flex">
      <div class="-mt-px border-t-3 border-transparent pt-4 pr-1 inline-flex items-center text-sm leading-5 font-medium text-gray-500">
        <i class="fad fa-arrow-left mr-3 h-5 w-5 text-gray-400"></i>
        Previous
      </div>
    </div>

    <inertia-link v-if="page > 0"
      class="w-0 flex-1 flex"
      :href="route('datasets.show', {'dataset':dataset.id, 'page': pagePrev})">
      <div class="-mt-px border-t-3 border-transparent pt-4 pl-1 inline-flex items-center text-sm leading-5 font-medium text-teal-500 hover:text-teal-700 focus:border-teal-500 focus:text-teal-500 transition ease-in-out duration-150">
        <i class="fad fa-arrow-left mr-3 h-5 w-5"></i>
        Previous
      </div>
    </inertia-link>
    

    <div class="hidden md:flex">
      <div class="-mt-px pt-4 px-4 items-center text-sm text-gray-500">
      Showing <b>{{ (page * 20) + 1 }}</b> to <b>{{ (page * 20) + 20 }}</b> of <b>{{ totalrows }}</b> results
      </div>
    </div>

    <div v-if="(page + 1) >= totalpages" 
      class="w-0 flex-1 flex justify-end">
      <div class="-mt-px border-t-3 border-transparent pt-4 pl-1 inline-flex items-center text-sm leading-5 font-medium text-gray-500">
        Next
        <i class="fad fa-arrow-right ml-3 h-5 w-5 text-gray-400"></i>
      </div>
    </div>

    <inertia-link v-if="page < totalpages" 
      class="w-0 flex-1 flex justify-end" 
      :href="route('datasets.show', {'dataset':dataset.id, 'page': pageNext})">
      <div class="-mt-px border-t-3 border-transparent pt-4 pl-1 inline-flex items-center text-sm leading-5 font-medium text-teal-500 hover:text-teal-700 focus:border-teal-500 focus:text-teal-500 transition ease-in-out duration-150">
        Next
        <i class="fad fa-arrow-right ml-3 h-5 w-5"></i>
      </div>
    </inertia-link>
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
      pageNext: this.page + 1,
      pagePrev: this.page - 1
    }
  },
  props: {
    dataset: Object,
    headers: Array,
    totalpages: Number,
    totalrows: Number,
    page: Number,
    results: Array
  }
}
</script>