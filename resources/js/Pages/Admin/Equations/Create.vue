<template>
<div>

  <admin-header :title="'Add New Equation'" :breadcrumbs="breadcrumbs" />

  

  <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
    <div class="py-4">

      <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
        <div class="md:grid md:grid-cols-3 md:gap-6">
          <div class="md:col-span-1">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Equation</h3>
            <p class="mt-1 text-sm leading-5 text-gray-500">
              A good name and description for the equation will go a long way.
            </p>
          </div>
          <div class="mt-5 md:mt-0 md:col-span-2">
              <text-input v-model="form.name" :errors="error('name')" class="pr-6 pb-8 w-full" label="Equation Name" />
              <text-input v-model="form.description" :errors="error('description')" class="pr-6 pb-8 w-full" label="Description" />
          </div>
        </div>
      </div>

      <div class="bg-white shadow px-4 py-5 mt-5 sm:rounded-lg sm:p-6">
        <div class="md:grid md:grid-cols-1 md:gap-6">
          <div class="md:col-span-1">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Type of Equation.</h3>
            <p class="mt-1 text-sm leading-5 text-gray-500">
              Once you declare a type, it cannot be changed. Here are the available choices for creating an equation.
            </p>

            <div class="flex mt-6">
              <div class="w-1/5 text-right px-4">
                <input 
                v-model="form.action"
                value="cnt"
                selected
                type="radio"
                class="form-radio h-4 w-4 text-teal-500 transition duration-150 ease-in-out" />
              </div>
              <div class="w-4/5">
                <h4 class="font-medium text-xl">Count</h4>
                <p class="text-sm mt-2">
                Return the count of all items that match the given codebook value.
                A single value is returned e.g. 230, or 507.
                </p>
                <p class="text-xs mt-2">
                e.g. count all ages that equal 3.<br>
                e.g. count all genders that equal 1.
                </p>
              </div>
            </div>


            <div class="flex mt-6">
              <div class="w-1/5 text-right px-4">
                <input 
                v-model="form.action"
                value="cntgroupby"
                selected
                type="radio"
                class="form-radio h-4 w-4 text-teal-500 transition duration-150 ease-in-out" />
              </div>
              <div class="w-4/5">
                <h4 class="font-medium text-xl">Count Grouped By</h4>
                <p class="text-sm mt-2">
                Return the count of all items that match the given codebook vaule and group them by an additional codebook value.
                </p>
                <p class="text-xs mt-2">
                e.g. count all IDELA Totals and group by Age.
                </p>
              </div>
            </div>


            <div class="flex mt-6">
              <div class="w-1/5 text-right px-4">
                <input 
                v-model="form.action"
                value="pct"
                selected
                type="radio"
                class="form-radio h-4 w-4 text-teal-500 transition duration-150 ease-in-out" />
              </div>
              <div class="w-4/5">
                <h4 class="font-medium text-xl">Percent</h4>
                <p class="text-sm mt-2">
                Return the percentage of items in a given dataset. This will sum the total items in a selected codebook, 
                and divide by the total rows of the dataset(s) selected.
                A single value is returned e.g. 43.21, 56.12
                </p>
                <p class="text-xs mt-2">
                e.g. Sum all Total IDELA Scores and divide by the total rows of the dataset(s).
                </p>
                <p class="text-sm mt-2">
                If an additional division is needed based on the codebook items, it will perform that before dividing by the total rows.
                e.g. Sum all motor skills, divide by 4, then divide by total rows of the dataset(s).
                </p>
              </div>
            </div>

            <div class="flex mt-6">
              <div class="w-1/5 text-right px-4">
                <input 
                v-model="form.action"
                value="avggroupbytot"
                selected
                type="radio"
                class="form-radio h-4 w-4 text-teal-500 transition duration-150 ease-in-out" />
              </div>
              <div class="w-4/5">
                <h4 class="font-medium text-xl">Average Percent Grouped By Codebook in Total Rows</h4>
                <p class="text-sm mt-2">
                Retuns the percentage of the values for the selected codebook item by sum of the total items for a given datasets.
                It will them group by the second codebook item.
                An array of data is returned e.g. [21.34, 43.21, 56.12]
                </p>
                <p class="text-xs mt-2">
                e.g. Group the Total IDELA by Childrens Age
                </p>
              </div>
            </div>

            <div class="flex mt-6">
              <div class="w-1/5 text-right px-4">
                <input 
                v-model="form.action"
                value="avggroupby"
                selected
                type="radio"
                class="form-radio h-4 w-4 text-teal-500 transition duration-150 ease-in-out" />
              </div>
              <div class="w-4/5">
                <h4 class="font-medium text-xl">Average Percent Grouped By Codebook</h4>
                <p class="text-sm mt-2">
                Retuns the percentage of the values for the selected codebook item by sum of the total items for a selected codebook item, 
                and dividing it by the second codebook item. It will them group by the second codebook item.
                An array value is returned e.g. [43.21, 56.12]
                </p>
                <p class="text-xs mt-2">
                e.g. Group the Total IDELA Score by Childrens Age
                </p>
              </div>
            </div>


            <div class="flex mt-6">
              <div class="w-1/5 text-right px-4">
                <input 
                v-model="form.action"
                value="pctgroupby"
                selected
                type="radio"
                class="form-radio h-4 w-4 text-teal-500 transition duration-150 ease-in-out" />
              </div>
              <div class="w-4/5">
                <h4 class="font-medium text-xl">Percent Grouped By Codebook & Value</h4>
                <p class="text-sm mt-2">
                Returns the sum of values from the selected codebook item, groups them by a selected codebook item & specific value, 
                then divides the total by the count of the grouped item. Use this when needing a specific value out of a specific column.
                Returns a single data point e.g. 51.34
                </p>
                <p class="text-xs mt-2">
                e.g. Return the average Total IDELA score grouped by Gender, Females
                </p>
              </div>
            </div>

            <div class="flex mt-6">
              <div class="w-1/5 text-right px-4">
                <input 
                v-model="form.action"
                value="distribution"
                selected
                type="radio"
                class="form-radio h-4 w-4 text-teal-500 transition duration-150 ease-in-out" />
              </div>
              <div class="w-4/5">
                <h4 class="font-medium text-xl">Distribution Percentage</h4>
                <p class="text-sm mt-2">
                Returns the percentage of a codebook item between two values. 
                The first value is calculated with >= and the second value is <.
                </p>
                <p class="text-xs mt-2">
                e.g. Return the percentage of Total IDELA scores >= .1 and < .2.
                </p>
              </div>
            </div>


            <div class="flex mt-6">
              <div class="w-1/5 text-right px-4">
                <input 
                v-model="form.action"
                value="scatter"
                selected
                type="radio"
                class="form-radio h-4 w-4 text-teal-500 transition duration-150 ease-in-out" />
              </div>
              <div class="w-4/5">
                <h4 class="font-medium text-xl">Scatter Plot</h4>
                <p class="text-sm mt-2">
                Returns all points of data as a sctter plot grouped by another codebook item
                </p>
                <p class="text-xs mt-2">
                e.g. Average score of IDELA Total with qty. of Home Learning Materials.
                </p>
              </div>
            </div>

          
            <!-- <select-input v-model="form.action" :errors="error('action')" class="pr-6 pb-8 w-full" label="Type of Result">
              <option value="cnt">Count</option>
              <option value="cntgroupby">Count Grouped By</option>
              <option value="pct">Percent</option>
              <option value="avggroupby">Average Percent Grouped By Codebook</option>
              <option value="pctgroupby">Percent Grouped By Codebook & Value</option>
              <option value="distribution">Distribution Percentage</option>
              <option value="avggroupbytot">Average Percent Grouped By Codebook in Total Rows</option>
              <option value="scatter">Scatter Plot, Equation</option>
              <!-- <option value="sum">Sum</option> -->
            <!-- </select-input> -->

           

            <button 
              @click="submit"
              class="inline-flex items-center mt-8 ml-1/5 px-3 py-3 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-teal-500 hover:bg-teal-500 focus:outline-none focus:border-teal-700 focus:shadow-outline-teal active:bg-teal-700 transition ease-in-out duration-150">
              Create Equation</button>
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

import Radio from '@/Shared/Radio'

export default {
  layout: Admin,
  mixins: [mixins],
  data() {
    return {
      breadcrumbs: [
        {
          title: 'Equations',
          link: 'equations'
        },
      ],
      form: {
        name: null,
        description: null,
        action: null,
        type: 'codebook'
      }
    }
  },
  components: {
    Radio
  },
  methods: {
    submit() {
      this.$inertia.post(this.route('equations.store'), this.form)
    }
  }
}
</script>