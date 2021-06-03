<template>
  <div class="flex items-center">
  <inertia-link 
    :href="route(link)" 
    class="hover:text-teal-500 focus:outline-none transition duration-150 ease-in-out">
    {{ title }}
  </inertia-link>
  <i class="fad fa-angle-right flex-shrink-0 mx-2 h-5 w-5 text-gray-400"></i>
  </div>
</template>

<script>
export default {
  props: {
    link: String,
    title: String
  }
}
</script>