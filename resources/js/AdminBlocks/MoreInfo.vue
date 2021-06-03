<template>
  <div>
    <!-- Blocked Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">

      <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
        <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-no-wrap">
          <div class="ml-4 mt-4">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <i class="h-12 w-12 fa-fw fa-3x fad fa-chart-bar"></i>
              </div>
              <div class="ml-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                  More Info
                </h3>
                <p class="text-sm leading-5 text-gray-500">
                  This graph will go 100% across the page.
                </p>
              </div>
            </div>
          </div>
          <div class="ml-4 mt-4 flex-shrink-0 flex">
            <button @click="$emit('destroy', block.blockid)" class="px-3 py-2 text-red-700 text-xs hover:text-red-500">
              <i class="fad fa-trash-alt"></i> remove
            </button>
          </div>
        </div>
      </div>

      <div class="py-4">
        <form @submit.prevent="submit">
          <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
              <div class="md:col-span-1">
                <h3 class="text-lg font-medium leading-6 text-gray-900">More Information</h3>
                <p class="mt-1 text-sm leading-5 text-gray-500">
                  Side by side graphs.
                </p>
              </div>
              <div class="mt-5 md:mt-0 md:col-span-2">
              
                <p>More Information Section</p>

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

import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
import TextArea from '@/Shared/TextArea'

export default {
  data() {
    return {
      form: {
        left: {
          icon: this.block.left.icon,
          title: this.block.left.title,
          description: this.block.left.description,
          button: {
            link: this.block.left.button.link,
            url: this.block.left.button.url,
            target: this.block.left.button.target,
          }
        },
        right: {
          icon: this.block.left.icon,
          title: this.block.left.title,
          description: this.block.left.description,
          button: {
            link: this.block.left.button.link,
            url: this.block.left.button.url,
            target: this.block.left.button.target,
          }
        },
      },
    }
  },
  props: {
    block: Object,
    graphs: [Object, Array]
  },
  components: {
    TextInput,
    SelectInput,
    TextArea
  }
}
</script>