<template>
<div class="bg-white py-8">
  <div class="w-full max-w-4xl px-5 mx-auto flex">
    <h3 class="font-oswald text-4xl pb-8 pt-2 mb-5 text-center md:text-left">{{ block.title }}</h3>
  </div>
  <div class="w-full max-w-4xl px-5 mx-auto flex justify-between">
    <div class="more-info-box w-1/2 border-solid border-3 border-teal-600 p-8 rounded-r-xl rounded-bl-xl">
      <h3 class="title font-oswald text-4xl">{{ block.left.title }}</h3>
      <div class="description mb-8 leading-tight" v-html="block.left.description" />
      <a class="btn-more-info bg-gray-700 tracking-widest text-white font-oswald px-3 py-2 uppercase rounded-r-lg rounded-bl-lg" 
        :href="block.left.button.url" :target="block.left.button.target">{{ block.left.button.link }}</a>
    </div>
    <div class="more-info-box w-1/2 border-solid border-3 border-teal-600 ml-3 p-8 rounded-r-xl rounded-bl-xl">
      <h3 class="title font-oswald text-4xl">{{ block.right.title }}</h3>
      <div class="description mb-8 leading-tight" v-html="block.right.description" />
      <a class="btn-more-info bg-gray-700 tracking-widest text-white font-oswald px-3 py-2 uppercase rounded-r-lg rounded-bl-lg" 
        :href="block.right.button.url" :target="block.right.button.target">{{ block.right.button.link }}</a>
    </div>
  </div>
</div>
</template>

<script>
export default {
  props: {
    block: Object
  }
}
</script>