<template>
  <div class="px-4 flex items-center justify-between sm:px-0">
    <template v-for="(link, key) in links">
      <div v-if="link.url === null && link.label === 'Previous'" 
        :key="key" 
        class="w-0 flex-1 flex">
        <div class="-mt-px border-t-3 border-transparent pt-4 pr-1 inline-flex items-center text-sm leading-5 font-medium text-gray-500">
          <i class="fad fa-arrow-left mr-3 h-5 w-5 text-gray-400"></i>
          Previous
        </div>
      </div>

      <inertia-link v-if="link.url !== null && link.label === 'Previous'"  :key="key" 
        class="w-0 flex-1 flex"
        :href="link.url">
        <div class="-mt-px border-t-3 border-transparent pt-4 pl-1 inline-flex items-center text-sm leading-5 font-medium text-teal-500 hover:text-teal-700 focus:border-teal-500 focus:text-teal-500 transition ease-in-out duration-150">
          <i class="fad fa-arrow-left mr-3 h-5 w-5"></i>
          Previous
        </div>
      </inertia-link>
    </template>

    <div class="hidden md:flex">
    <template v-for="(link, key) in links">
      <inertia-link 
        v-if="!link.active && link.label !== 'Previous' && link.label !== 'Next'"
        :href="link.url" 
        class="-mt-px border-t-3 border-transparent pt-4 px-4 inline-flex items-center text-sm leading-5 font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-400 transition ease-in-out duration-150">
        {{ link.label }}
      </inertia-link>

      <div
        v-if="link.active && link.label !== 'Previous' && link.label !== 'Next'"
        :href="link.url" 
        class="-mt-px border-t-3 border-teal-500 pt-4 px-4 inline-flex items-center text-sm leading-5 font-medium text-teal-500">
        {{ link.label }}
      </div>

    </template>
    </div>

    <template v-for="(link, key) in links">
      <div v-if="link.url === null && link.label === 'Next'" 
        :key="key" 
        class="w-0 flex-1 flex justify-end">
        <div class="-mt-px border-t-3 border-transparent pt-4 pl-1 inline-flex items-center text-sm leading-5 font-medium text-gray-500">
          Next
          <i class="fad fa-arrow-right ml-3 h-5 w-5 text-gray-400"></i>
        </div>
      </div>


      <inertia-link v-if="link.url !== null && link.label === 'Next'" 
        :key="key" 
        class="w-0 flex-1 flex justify-end" 
        :href="link.url">
        <div class="-mt-px border-t-3 border-transparent pt-4 pl-1 inline-flex items-center text-sm leading-5 font-medium text-teal-500 hover:text-teal-700 focus:border-teal-500 focus:text-teal-500 transition ease-in-out duration-150">
          Next
          <i class="fad fa-arrow-right ml-3 h-5 w-5"></i>
        </div>
      </inertia-link>
    </template>
  </div>
</template>

<script>
export default {
  props: {
    links: Array,
  },
}
</script>