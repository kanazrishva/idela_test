<template>
<div>

  <admin-header :title="'Edit Equation'" :breadcrumbs="breadcrumbs" />

  <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
    <div class="py-4">

      <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
        <div class="md:grid md:grid-cols-3 md:gap-6">
          <div class="md:col-span-1">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Equation Details</h3>
            <p class="mt-1 text-sm leading-5 text-gray-500">
              Adjust the name and description of the equation if needed.
            </p>
          </div>
          <div class="mt-5 md:mt-0 md:col-span-2">
       
              <text-input v-model="form.name" :errors="error('name')" class="pr-6 pb-8 w-full" label="Name" />
              <text-input v-model="form.description" :errors="error('description')" class="pr-6 pb-8 w-full" label="Description" />
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>


  <div  v-if="equation.action == 'distribution'" >

    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
      <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">

        <div class="md:grid md:grid-cols-3 md:gap-6">
          <div class="md:col-span-1">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Distribution Percentage</h3>
            <p class="mt-1 text-sm leading-5 text-gray-500">
              Returns the percentage of a codebook item between two values. 
              The first value is calculated with >= and the second value is <.
            </p>
          </div>

          <div class="mt-5 md:mt-0 md:col-span-2">
              <label class="block text-sm font-medium leading-5 mb-1 text-gray-700">Codebook Item</label>
              <multiselect 
                v-model="form.values"
                :options="selection"
                label="matching_name"
                class="flex items-start justify-start w-full"
                track-by="id">
                <template slot="singleLabel" slot-scope="{ option }">
                  <strong>{{ option.matching_name }}</strong>
                </template>
              </multiselect>
            
          </div>
        </div>
        
        <div class="mt-6 md:grid md:grid-cols-3 md:gap-6">
          <div class="md:col-span-1">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Value to Match</h3>
            <p class="mt-1 text-sm leading-5 text-gray-500">
              If the codebook has values associated with it, select the proper value for the count. 
              If values are not set via the Codebook, then you can manually add it here.
            </p>
          </div>

          <div class="mt-5 md:mt-0 md:col-span-2">
            <div class="flex">
              <text-input v-model="lowerval" class="pr-6 pb-8 w-1/2" label="Lower value (>=)" />
             <text-input v-model="upperval" class="pr-6 pb-8 w-1/2" label="Upper value (<)" />
            </div>
            <button @click="submitDistribution" class="inline-flex items-center px-3 py-3 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-teal-500 hover:bg-teal-500 focus:outline-none focus:border-teal-700 focus:shadow-outline-teal active:bg-teal-700 transition ease-in-out duration-150" type="submit">Update Equation</button>
          </div>

           
        </div>

      </div>
    </div>

  </div>


  <div  v-if="equation.action == 'cnt'" >
    <form @submit.prevent="submit"> 
      <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">

          <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
              <h3 class="text-lg font-medium leading-6 text-gray-900">Count Based Equation</h3>
              <p class="mt-1 text-sm leading-5 text-gray-500">
                Select all the items that should be calculated for the result of the Equation.
              </p>
            </div>

            <div class="mt-5 md:mt-0 md:col-span-2">
                <label class="block text-sm font-medium leading-5 mb-1 text-gray-700">Codebook Item</label>
                <multiselect 
                  v-model="form.values"
                  :options="selection"
                  label="matching_name"
                  class="flex items-start justify-start w-full"
                  track-by="id">
                  <template slot="singleLabel" slot-scope="{ option }">
                    <strong>{{ option.matching_name }}</strong>
                  </template>
                </multiselect>
              
            </div>
          </div>
          
          <div class="mt-6 md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
              <h3 class="text-lg font-medium leading-6 text-gray-900">Value to Match</h3>
              <p class="mt-1 text-sm leading-5 text-gray-500">
                If the codebook has values associated with it, select the proper value for the count. 
                If values are not set via the Codebook, then you can manually add it here.
              </p>
            </div>

            <div class="mt-5 md:mt-0 md:col-span-2">
              <text-input v-model="form.value" class="pr-6 pb-8 w-full" label="Value to Count" />

              <loading-button type="submit">Update Equation</loading-button>

            </div>
          </div>

        </div>
      </div>
    </form>
  </div>


  <div  v-if="equation.action == 'pctgroupby'" >
    <form @submit.prevent="submitPctGroupBy"> 
      <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">

          <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
              <h3 class="text-lg font-medium leading-6 text-gray-900">Percent Grouped By Codebook & Value</h3>
              <p class="mt-1 text-sm leading-5 text-gray-500">
                Returns the sum of values from the selected codebook item, groups them by a selected codebook item & specific value, 
                then divides the total by the count of the grouped item. Use this when needing a specific value out of a specific column.
                Returns a single data point e.g. 51.34
              </p>
            </div>

            <div class="mt-5 md:mt-0 md:col-span-2">
              <label class="block text-sm font-medium leading-5 mb-1 text-gray-700">Codebook Item for Percentage</label>
              <multiselect 
                v-model="form.values"
                :options="selection"
                label="matching_name"
                class="flex items-start justify-start w-full"
                track-by="id">
                <template slot="singleLabel" slot-scope="{ option }">
                  <strong>{{ option.matching_name }}</strong>
                </template>
              </multiselect>


              <div class="mt-5 md:mt-0 md:col-span-2">
                
                <div class="flex mt-5">
                  <div class="w-1/2 mr-6">
                    <label class="block text-sm font-medium leading-5 mb-1 text-gray-700">Codebook Item to Group By</label>
                    <multiselect 
                      v-model="groupby"
                      :options="selection"
                      label="matching_name"
                      class="flex items-start justify-start w-full"
                      track-by="id">
                      <template slot="singleLabel" slot-scope="{ option }">
                        <strong>{{ option.matching_name }}</strong>
                      </template>
                    </multiselect>
                  </div>
                  <text-input v-model="valuetomatch" class="pb-8 w-1/2" label="Value to Match" />
                </div>

                <loading-button type="submit">Update Equation</loading-button>
              </div>
          </div>
          </div>
        </div>
      </div>
    </form>
  </div>

  <div  v-if="equation.action == 'avggroupbytot'" >
    <form @submit.prevent="submit"> 
      <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">

          <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
              <h3 class="text-lg font-medium leading-6 text-gray-900">Average Percent Grouped By Codebook in Total Rows</h3>
              <p class="mt-1 text-sm leading-5 text-gray-500">
                Retuns the percentage of the values for the selected codebook item by sum of the total items for a given datasets.
                It will them group by the second codebook item.
              </p>
            </div>

            <div class="mt-5 md:mt-0 md:col-span-2">
                <label class="block text-sm font-medium leading-5 mb-1 text-gray-700">Codebook Item</label>
                <multiselect 
                  v-model="form.values"
                  :options="selection"
                  label="matching_name"
                  class="flex items-start justify-start w-full"
                  track-by="id">
                  <template slot="singleLabel" slot-scope="{ option }">
                    <strong>{{ option.matching_name }}</strong>
                  </template>
                </multiselect>

                <label class="block text-sm mt-4 font-medium leading-5 mb-1 text-gray-700">Group By</label>
                  <multiselect 
                    v-model="form.value"
                    :options="selection"
                    :multiple="false"
                    label="matching_name"
                    class="flex items-start justify-start w-full"
                    track-by="id">
                    <template slot="singleLabel" slot-scope="{ option }">
                      <strong>{{ option.matching_name }}</strong>
                    </template>
                  </multiselect>

              <loading-button class="mt-6" type="submit">Update Equation</loading-button>
              
            </div>
          </div>
          
        

        </div>
      </div>
    </form>
  </div>


  <div  v-if="equation.action == 'cntgroupby'" >
    <form @submit.prevent="submit"> 
      <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">

          <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
              <h3 class="text-lg font-medium leading-6 text-gray-900">Count Based Equation</h3>
              <p class="mt-1 text-sm leading-5 text-gray-500">
                Select all the items that should be calculated for the result of the Equation.
              </p>
            </div>

            <div class="mt-5 md:mt-0 md:col-span-2">
                <label class="block text-sm font-medium leading-5 mb-1 text-gray-700">Codebook Item</label>
                <multiselect 
                  v-model="form.values"
                  :options="selection"
                  label="matching_name"
                  class="flex items-start justify-start w-full"
                  track-by="id">
                  <template slot="singleLabel" slot-scope="{ option }">
                    <strong>{{ option.matching_name }}</strong>
                  </template>
                </multiselect>
              
            </div>
          </div>
          
          <div class="mt-6 md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
              <h3 class="text-lg font-medium leading-6 text-gray-900">Value to Group</h3>
              <p class="mt-1 text-sm leading-5 text-gray-500">
                If the codebook has values associated with it, select the proper value for the count. 
                If values are not set via the Codebook, then you can manually add it here.
              </p>
            </div>

            <div class="mt-5 md:mt-0 md:col-span-2">
              <label class="block text-sm font-medium leading-5 mb-1 text-gray-700">Group By</label>
                  <multiselect 
                    v-model="form.value"
                    :options="selection"
                    :multiple="false"
                    label="matching_name"
                    class="flex items-start justify-start w-full"
                    track-by="id">
                    <template slot="singleLabel" slot-scope="{ option }">
                      <strong>{{ option.matching_name }}</strong>
                    </template>
                  </multiselect>

              <loading-button type="submit">Update Equation</loading-button>

            </div>
          </div>

        </div>
      </div>
    </form>
  </div>




  <div  v-if="equation.action == 'scatter'" >
    <form @submit.prevent="submit"> 
      <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">

          <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
              <h3 class="text-lg font-medium leading-6 text-gray-900">Scatter Plot</h3>
              <p class="mt-1 text-sm leading-5 text-gray-500">
                Returns all points of data as a sctter plot grouped by another codebook item
              </p>
            </div>

            <div class="mt-5 md:mt-0 md:col-span-2">
                <label class="block text-sm font-medium leading-5 mb-1 text-gray-700">Codebook Item</label>
                <multiselect 
                  v-model="form.values"
                  :options="selection"
                  label="matching_name"
                  :multiple="false"
                  class="flex items-start justify-start w-full"
                  track-by="id">
                  <template slot="singleLabel" slot-scope="{ option }">
                    <strong>{{ option.matching_name }}</strong>
                  </template>
                </multiselect>

                <label class="block mt-4 text-sm font-medium leading-5 mb-1 text-gray-700">Group By</label>
                  <multiselect 
                    v-model="form.value"
                    :options="selection"
                    :multiple="false"
                    label="matching_name"
                    class="flex items-start justify-start w-full"
                    track-by="id">
                    <template slot="singleLabel" slot-scope="{ option }">
                      <strong>{{ option.matching_name }}</strong>
                    </template>
                  </multiselect>

              <loading-button class="mt-6" type="submit">Update Equation</loading-button>


              
            </div>
          </div>

        </div>
      </div>
    </form>
  </div>


  <div  v-if="equation.action == 'avggroupby'" >
    <form @submit.prevent="submit"> 
      <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">

          <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
              <h3 class="text-lg font-medium leading-6 text-gray-900">Average Percent Grouped By Codebook</h3>
              <p class="mt-1 text-sm leading-5 text-gray-500">
                Returns the percentage of the values for the selected codebook item by sum of the total items for a selected codebook item, 
                and dividing it by the second codebook item. It will them group by the second codebook item.
              </p>
            </div>

            <div class="mt-5 md:mt-0 md:col-span-2">
              <label class="block text-sm font-medium leading-5 mb-1 text-gray-700">Codebook Item(s)</label>
              <multiselect 
                v-model="form.values"
                :options="selection"
                :multiple="true"
                label="matching_name"
                class="flex items-start justify-start w-full"
                track-by="id">
                <template slot="singleLabel" slot-scope="{ option }">
                  <strong>{{ option.matching_name }}</strong>
                </template>
              </multiselect>

              <label class="block text-sm mt-4 font-medium leading-5 mb-1 text-gray-700">Group By</label>
                <multiselect 
                  v-model="form.value"
                  :options="selection"
                  :multiple="false"
                  label="matching_name"
                  class="flex items-start justify-start w-full"
                  track-by="id">
                  <template slot="singleLabel" slot-scope="{ option }">
                    <strong>{{ option.matching_name }}</strong>
                  </template>
                </multiselect>

              <loading-button class="mt-6" type="submit">Update Equation</loading-button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>



  <div  v-if="equation.action == 'pct'" >
    <form @submit.prevent="submit"> 
      <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">

          <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
              <h3 class="text-lg font-medium leading-6 text-gray-900">Percent</h3>
              <p class="mt-1 text-sm leading-5 text-gray-500">
                Return the percentage of items in a given dataset. This will sum the total items in a selected codebook, 
                and divide by the total rows of the dataset(s) selected.
              </p>
            </div>

            <div class="mt-5 md:mt-0 md:col-span-2">
              <label class="block text-sm font-medium leading-5 mb-1 text-gray-700">Codebook Item(s)</label>
              <multiselect 
                v-model="form.values"
                :options="selection"
                :multiple="true"
                label="matching_name"
                class="flex items-start justify-start w-full"
                track-by="id">
                <template slot="singleLabel" slot-scope="{ option }">
                  <strong>{{ option.matching_name }}</strong>
                </template>
              </multiselect>
            </div>
          </div>
          
          <div class="mt-6 md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
              <h3 class="text-lg font-medium leading-6 text-gray-900">Value to Divide By</h3>
              <p class="mt-1 text-sm leading-5 text-gray-500">
                If needed, enter a numeric value here for the total to be divided by. 
                This is most likly to be used when there are multiple values selected.
              </p>
            </div>

            <div class="mt-5 md:mt-0 md:col-span-2">
              <text-input v-model="form.value" class="pr-6 pb-8 w-full" label="Value to Divide By (if any)" />

              <loading-button type="submit">Update Equation</loading-button>

            </div>
          </div>

        </div>
      </div>
    </form>
  </div>

  <div  v-if="equation.action == 'sum'" class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
    <div class="md:grid md:grid-cols-3 md:gap-6">
      <div class="md:col-span-1">
        <h3 class="text-lg font-medium leading-6 text-gray-900">Equation Elements</h3>
        <p class="mt-1 text-sm leading-5 text-gray-500">
          Select all the items that should be calculated for the result of the Equation.
        </p>
      </div>



      <div class="mt-5 md:mt-0 md:col-span-2">
        <form @submit.prevent="submit">
          <div v-for="(item, index) in selection" :key="index">
            <input  
              type="checkbox"
              :id="'checkbox-' + index"
              :value="item.id"

              v-model="form.values" />
            <label :for="'checkbox-' + index" class="ml-2 block text-sm font-medium leading-5 text-gray-700">
              {{ item.matching_name }}
            </label>
          </div>
        </form>
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
          title: 'Equations',
          link: 'equations'
        },
      ],
      lowerval: null,
      upperval: null,
      //
      groupby: null,
      valuetomatch: null,
      form: {
        name: this.equation.name,
        description: this.equation.description,
        values: [], //(this.selected.length === 1) ? this.selected[0] : this.selected,
        value: this.equation.value
      }
    }
  },
  props: {
    equation: Object,
    selection: Array,
    selected: [Array, Object]
  },
  watch: {

  },
  methods: {
    submit() {
      this.$inertia.put(this.route('equations.update', this.equation.id), this.form)
    },
    submitDistribution() {
      let form = this.form
          form.value = this.lowerval + ',' + this.upperval

      console.log(form)

      this.$inertia.put(this.route('equations.update', this.equation.id), form)
    },

    submitPctGroupBy() {
      let form = this.form
          form.value = this.groupby.id + ',' + this.valuetomatch

      console.log(form)

      this.$inertia.put(this.route('equations.update', this.equation.id), form)
    },

    selectChange($event) {
      this.values = [];
      //this.values[0].codebook.push($event)
    },
  },
  created() {
    if (this.equation.action == 'distribution') {
      let str = this.equation.value.split(",")

      //console.log(str)

      this.lowerval = str[0]
      this.upperval = str[1]
    }

    this.form.values = []
    if (this.selected != null) {
      this.form.values = (this.selected.length === 1) ? this.selected[0] : this.selected
    }

 

    if (this.equation.action == 'pctgroupby') {

      if (this.equation.value) {
        let str = this.equation.value.split(",")

        this.groupby = this.selection.find(o => o.id == str[0]) 
        this.valuetomatch = str[1]
      }
    }

     if (this.equation.action == 'scatter') {

      if (this.equation.value) {

        this.form.value = this.selection.find(o => o.id == this.equation.value) 
      }
    }
  }
}
</script>