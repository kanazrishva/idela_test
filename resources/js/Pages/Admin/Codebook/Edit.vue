<template>
  <div>
    <admin-header :title="'Edit Codebook'" :breadcrumbs="breadcrumbs" />

    <trashed-message v-if="codebook.deleted_at" @restore="restore" class="">
      This codebook has been deleted.
    </trashed-message>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
      <!-- Replace with your content -->
      <div class="py-4">

        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
          <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
              <h3 class="text-lg font-medium leading-6 text-gray-900">Codebook Editing</h3>
              <p class="mt-1 text-sm leading-5 text-gray-500">
                When editing a codebook item, please remember that this will propigate through every assigned dataset.
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

                <div v-if="!codebook.deleted_at">
                <loading-button :loading="sending" class="btn-indigo" type="submit">Update Codebook Item</loading-button>
                
                <button @click="destroy" class="px-3 py-2 text-red-700 text-xs hover:text-red-500">
                  <i class="fad fa-trash-alt"></i> delete codebook
                </button>
                </div>

              </form>
            </div>
          </div>
        </div>

        <div v-if="!codebook.deleted_at" class="bg-white shadow mt-5 sm:rounded-lg sm:p-6">
          <div class="md:grid md:grid-cols-2 md:gap-6">
            <div class="md:col-span-1">
              <h3 class="text-lg font-medium leading-6 text-gray-900">Add a Codebook Column</h3>
              <p class="mt-1 text-sm leading-5 text-gray-500">
                Columns are used here to match potential imported datasets. Consider adding variants if needed. e.g. ltr1, ltr01
              </p>
            </div>

            <div>

              <form @submit.prevent="valueColumnSubmit" class="flex items-start">
                <text-input v-model="columnform.column_name" :errors="error('column_name')" class="w-full mr-2" :placeholder="'Column Name'" />
                <loading-button class="ml-2" type="submit">Add</loading-button>
              </form>

            </div>
          </div>
        </div>

         <div class="flex flex-col mt-5">
          <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
              <table class="min-w-full table-fixed">
                <thead>
                  <tr>
                    <th class="w-1/3 px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Column Name
                    </th>
                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-right">
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="codebookcolumns.length === 0" class="bg-white">
                    <td colspan="4" class="px-6 py-4 whitespace-no-wrap text-center text-sm leading-5 text-gray-300">
                      No results available.
                    </td>
                  </tr>
                  <tr v-for="(codebookcolumn, index) in codebookcolumns" :key="codebookcolumn.id"
                    :class="{'bg-white': index % 2 === 0, 'bg-gray-50': index % 2 !== 0 }">

                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900">
                      <div v-show="codebookcolumnOffset !== codebookcolumn.id">
                      {{ codebookcolumn.column_name }}
                       <button v-if="!codebook.deleted_at" class="px-3 py-2 text-teal-700 hover:text-teal-500" @click="startColumnEditing(codebookcolumn.id)"><i class="fad fa-edit"></i></button>
                      </div>

                      <div v-show="codebookcolumnOffset === codebookcolumn.id" v-if="!codebook.deleted_at" class="flex items-start justify-start">
                      <form class="flex items-start" @submit.prevent="columnUpdate(codebookcolumn)">
                        <text-input v-model="codebookcolumn.column_name" :errors="error('column_name', 'codebookColumn.' + codebookcolumn.id)" class="pr-2" />
                        <loading-button type="submit"><i class="fad fa-save"></i></loading-button>
                      </form>
                      <button @click="cancelColumnEdit()" class="pt-3 pl-4 text-teal-500 hover:text-teal-700 text-xs"><i class="fad fa-undo"></i> cancel</button>
                      </div>
                    </td>
                    <td class='px-6 py-4 whitespace-no-wrap text-right text-sm leading-5 text-gray-700'>
                      <button @click="destroyColumnValue(codebookcolumn.id)" v-if="!codebook.deleted_at" class="px-3 py-2 text-red-700 hover:text-red-500">
                        <i class="fad fa-trash-alt"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div v-if="!codebook.deleted_at" class="bg-white shadow mt-5 sm:rounded-lg sm:p-6">
          <div class="md:grid md:grid-cols-2 md:gap-6">
            <div class="md:col-span-1">
              <h3 class="text-lg font-medium leading-6 text-gray-900">Add a Codebook Item Value</h3>
              <p class="mt-1 text-sm leading-5 text-gray-500">
                Values help identify specific variables from datasets that are assigned to Codebook Items.
              </p>
            </div>

            <div>

              <form @submit.prevent="valueSubmit" class="flex items-start">
                <text-input v-model="valueform.value" :errors="error('value')" class="w-full mr-2" :placeholder="'Value'" />
                <text-input v-model="valueform.label" :errors="error('label')" class="w-full" :placeholder="'Label'" />
                <loading-button :loading="sendingvalueform" class="ml-2" type="submit">Add</loading-button>
              </form>

            </div>
          </div>
        </div>

        <div class="flex flex-col mt-5">
          <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
              <table class="min-w-full table-fixed">
                <thead>
                  <tr>
                    <th class="w-1/3 px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Value to Match
                    </th>
                    <th class="w-1/3 px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Label for Value
                    </th>
                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-right">
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="codebook.codebookvalues.length === 0" class="bg-white">
                    <td colspan="4" class="px-6 py-4 whitespace-no-wrap text-center text-sm leading-5 text-gray-300">
                      No results available.
                    </td>
                  </tr>

                  <tr v-for="(codebookvalue, index) in codebook.codebookvalues" :key="codebookvalue.id"
                    :class="{'bg-white': index % 2 === 0, 'bg-gray-50': index % 2 !== 0 }">

                    
                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900">
                      <div v-show="codebookvalueOffset !== codebookvalue.id">
                      {{ codebookvalue.value }}
                      </div>

                      <div v-show="codebookvalueOffset === codebookvalue.id" class="flex items-start justify-start">
                      <text-input v-model="codebookvalue.value" :errors="error('value', 'codebookValue.' + codebookvalue.id)" placeholder="Value" />
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-no-wrap text-left text-sm leading-5 text-gray-900">
                      <div v-show="codebookvalueOffset !== codebookvalue.id">
                      {{ codebookvalue.label }}
                      </div>

                      <div v-show="codebookvalueOffset === codebookvalue.id" class="flex items-start justify-start">
                      <text-input v-model="codebookvalue.label" :errors="error('label', 'codebookValue.' + codebookvalue.id)" class="mr-2" placeholder="Label" />
                      <button @click="valueUpdate(codebookvalue)" class="inline-flex items-center px-3 py-3 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-teal-500 hover:bg-teal-500 focus:outline-none focus:border-teal-700 focus:shadow-outline-teal active:bg-teal-700 transition ease-in-out duration-150"><i class="fad fa-save"></i></button>
                      <button @click="cancelEdit()" class="pt-3 pl-4 text-teal-500 hover:text-teal-700 text-xs"><i class="fad fa-undo"></i> cancel</button>
                      </div>
                    </td>
                    



                    <td class='px-6 py-4 whitespace-no-wrap text-right text-sm leading-5 text-gray-700'>
                      <button @click="startEditing(codebookvalue.id)" class="px-3 py-2 text-teal-700 hover:text-teal-500">
                        <i class="fad fa-edit"></i>
                      </button>
                      <button @click="destroyCodebookValue(codebookvalue.id)" class="px-3 py-2 text-red-700 hover:text-red-500">
                        <i class="fad fa-trash-alt"></i>
                      </button>
                    </td>
                  </tr>
                  
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
      <!-- /End replace -->
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
      sendingvalueform: false,
      codebookvalueOffset: -1,
      codebookcolumnOffset: -1,
      form: {
        type: this.codebook.type,
        matching_name: this.codebook.matching_name,
        matching_description: this.codebook.matching_description
      },
      columnform: {
        column_name: null,
      },
      valueform: {
        value: null,
        label: null
      }
    }
  },
  props: {
    codebook: Object,
    codebookcolumns: Array,
  },
  remember: 'form',
  methods: {
    startEditing(id) {
      this.codebookvalueOffset = id
    },
    startColumnEditing(id) {
      this.codebookcolumnOffset = id
    },
    cancelEdit() {
      this.codebookvalueOffset = -1
    },
    cancelColumnEdit() {
      this.codebookcolumnOffset = -1
    },
    valueSubmit() {
      this.sendingvalueform = true
      this.$inertia.post(this.route('codebookvalue.store', this.codebook.id), this.valueform)
        .then(() => {
          this.sendingvalueform = false
          this.valueform.value = null
          this.valueform.label = null
          this.$inertia.replace(this.route('codebook.edit', this.codebook.id), {
            only: [this.codebook]
          })
        })
    },
    valueColumnSubmit() {
      this.$inertia.post(this.route('codebookcolumn.store', this.codebook.id), this.columnform)
    },
    valueUpdate(codebookvalue) {
      this.$inertia.put(this.route('codebookvalue.update', codebookvalue), codebookvalue)
        .then(() => {
          if (!this.$page.errors['codebookValue.'+codebookvalue.id]) {
            this.codebookvalueOffset = -1
          }
        })
    },
    columnUpdate(codebookcolumn) {
      this.$inertia.put(this.route('codebookcolumn.update', codebookcolumn), codebookcolumn)
        .then(() => {
          if (!this.$page.errors['codebookColumn.'+codebookcolumn.id]) {
            this.codebookcolumnOffset = -1
          }
        })
    },
    destroyCodebookValue(id) {
      if (confirm('Are you sure you want to delete this Codebook Value item')) {
        this.$inertia.delete(this.route('codebookvalue.destroy', id))
          .then(() =>  {
            this.$inertia.replace(this.route('codebook.edit', this.codebook.id), {
              only: [this.codebook]
            })
          })
      }
    },
    destroyColumnValue(id) {
      if (confirm('Are you sure you want to delete this Codebook Column')) {
        this.$inertia.delete(this.route('codebookcolumn.destroy', id))
      }
    },
    submit() {
      this.sending = true
      this.$inertia.put(this.route('codebook.update', this.codebook.id), this.form)
        .then(() => this.sending = false)
    },
    destroy() {
      if (confirm('Are you sure you want to delete this Codebook item')) {
        this.$inertia.delete(this.route('codebook.remove', this.codebook.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this Codebook?')) {
        this.$inertia.put(this.route('codebook.restore', this.codebook.id))
      }
    }
  }
}
</script>