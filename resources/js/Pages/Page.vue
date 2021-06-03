<template>
  <div>
    <div class="w-full max-w-5xl px-5 mx-auto">
      <div class="breadcrumbs py-5 font-oswald text-gray-400 uppercase text-right">
        <a href="/">Home</a> /
        <span class="text-black">{{ page.title }}</span>
      </div>
    </div>

    <hero :block="hero" />

    <div v-if="caregiver.length > 0" class="w-full max-w-5xl mt-5 px-5 mx-auto grid grid-cols-2">
      <div v-show="domain" class="text-center col-span-2 row-start-2 mt-8 font-oswald text-4xl uppercase md:block md:text-left md:col-span-1 md:row-start-1 md:mt-0">Child</div>
      <div v-show="!domain" class="text-center col-span-2 row-start-2 mt-8 font-oswald text-4xl uppercase md:block md:text-left md:col-span-1 md:row-start-1 md:mt-0">Caregiver</div>
      <div class="col-span-2 text-sm uppercase font-oswald flex flex-col justify-end items-center md:col-span-1 md:flex-row md:text-base">
        Switch Environments 
        <span class="hidden mx-5 text-gray-400 md:inline-block">|</span> 
        
        <div class="flex justify-center items-center mt-4 md:mt-0">
          Child 
          
          <div class="border-3 border-teal-100 rounded-sm mx-3">
          <span 
            @click="domain = !domain" 
            class="bg-teal-100 relative flex justify-start items-center flex-shrink-0 h-4 w-8 cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
            <!-- On: "translate-x-5", Off: "translate-x-0" -->
            <span aria-hidden="true" 
              :class="domain ? 'translate-x-0' : 'translate-x-4'"
              class="translate-x-0 inline-block h-4 w-4 rounded-xs bg-teal-500 align-top transform transition ease-in-out duration-200"></span>
          </span>
          </div>

          Caregiver
        </div>
      </div>
    </div>

    
    <div v-show="domain">
      <blocks v-for="(block, index) in blocks" :block="block" :key="index" />
    </div>

    <div v-show="!domain">
      <blocks v-for="(block, index) in caregiver" :block="block" :key="index" />
    </div>

    <blocks v-for="(block, index) in footer" :block="block" :key="index" />

    <div class="bg-white py-8 pb-12">
      <div class="w-full max-w-4xl px-5 mx-auto flex justify-center items-center md:justify-start">
        <h3 class="font-oswald text-4xl pb-8 pt-2 mb-5">More Info</h3>
      </div>
      <div class="w-full max-w-4xl px-5 mx-auto flex flex-wrap justify-between md:flex-no-wrap">
        <div class="more-info-box w-full border-solid border-3 border-teal-600 p-8 rounded-r-xl rounded-bl-xl md:w-1/2">
          <img :src="asset('images/icon-questions.svg')" />
          <h3 class="title font-oswald text-4xl">Questions?</h3>
          <div class="description mb-8 leading-tight">
            The IDELA Community of Practice hosts a public forum where IDELA users and those interested in the tool can ask questions. Adaptation, translation, training and analysis are all common topics in the forum.
          </div>
          <a class="btn-more-info bg-gray-700 tracking-widest text-white font-oswald px-3 py-2 uppercase rounded-r-lg rounded-bl-lg" 
            href="https://idela-network.org/community-of-practice/" target="_blank">Community of practice</a>
        </div>
        <div class="more-info-box w-full border-solid border-3 border-teal-600 mt-6 p-8 rounded-r-xl rounded-bl-xl md:w-1/2 md:ml-3 md:mt-0">
        <img class="mb-1" :src="asset('images/icon-contact.svg')" />
          <h3 class="title font-oswald text-4xl">IDELA Data</h3>
          <div class="description mb-8 leading-tight">
            Interested to know more about IDELA as an assessment tool and how data is collected? Visit our About page to learn about implementation and data collection.
          </div>
          <a class="btn-more-info bg-gray-700 tracking-widest text-white font-oswald px-3 py-2 uppercase rounded-r-lg rounded-bl-lg" 
            href="https://idela-network.org/about/" target="_blank">About IDELA</a>
        </div>
      </div>
    </div>
    
  </div>
</template>

<script>
import Main from '@/Layouts/Main'
import Blocks from '@/Shared/Blocks'
import Hero from '@/PageBlocks/Hero'

export default {
  layout: Main,
  data() {
    return {
      domain: true,
      blocks: this.page.blocks,
      hero: this.page.hero,
      caregiver: this.page.caregiver,
      footer: this.page.footer
    }
  },
  components: {
    Blocks,
    Hero
  },
  props: {
    page: [Object, Array]
  },
  mounted() {
    // Google Analytics via GTM
    dataLayer.push({
        'event': 'pageView',
        'virtualUrl': this.$inertia.page.url
    });
  }
}
</script>