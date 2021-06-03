<template>
<div class="bg-white py-4">
  <div class="w-full max-w-4xl px-5 mx-auto flex justify-between">
    <div class="more-info-box w-full md:h-64 border-solid border-3 border-teal-600 rounded-r-xl rounded-bl-xl flex flex-col md:flex-row">
      <div class="w-full md:w-1/2 md:h-full relative">
        <div class="uppercase text-white font-oswald absolute text-shdow-resources left-4 top-3 md:left-7 md:top-5 z-20"
          :contenteditable="editable"
           @blur="(e) => {resource.category = e.target.innerText}">{{ resource.category }}</div>

        <!-- Image Editor -->
        <template v-if="editable">
          <popper trigger="clickToToggle" :visible-arrow="false" :root-class="'imagepopper'" :options="popperOptions">
            <div class="popper w-64 max-w-3xl">
              <image-input 
                v-model="resource.image"
                v-on:upload="$emit('uploadImage', $event)" 
                v-on:remove="$emit('removeImage', $event)"
                :directory="'page/resources/'"
                :imgix="'w=425&h=250&fit=crop'"/>
            </div>
            <div slot="reference" class="absolute cursor-pointer top-0 bottom-0 left-0 right-0 rounded-tr-xl md:rounded-tl-none md:rounded-bl-xl  text-gray-600">
              <div 
                :class="(!resource.image) ? 'bg-gray-200' : ''"
                class="w-full h-full flex justify-center items-center rounded-tr-xl md:rounded-tl-none md:rounded-bl-xl">
                <span v-if="!resource.image"><i class="far fa-image"></i></span>
              </div>
            </div>
          </popper>
        </template>
        <!-- End Image Editor -->

        <div class="overflow-hidden rounded-tr-xl md:rounded-tl-none md:rounded-bl-xl md:rounded-tr-none">
          <img v-if="resource.image" :src="imgix_domain + resource.image+'?w=425&h=250&fit=crop'">
          <img v-if="!resource.image" :src="imgix_domain + 'public/misc/resources-placeholder.jpg?w=425&h=250&fit=crop'">
        </div>

      </div>
      <div class="w-full md:w-1/2 flex justify-end items-end relative border-l-solid border-t-3 md:border-l-3 md:border-t-0 border-teal-600">
        <div class="uppercase text-teal-500 font-oswald absolute left-5 top-5"><i class="fal mr-1 fa-map-marker-alt"></i>
          <span
            :contenteditable="editable"
           @blur="(e) => {resource.country = e.target.innerText}">{{ resource.country }}</span>
        </div>
        <div class="uppercase text-teal-500 font-oswald text-sm absolute left-5 top-12 md:right-7 md:top-5 md:left-auto md:text-base"
          :contenteditable="editable"
           @blur="(e) => {resource.publish_date = e.target.innerText}">{{ resource.publish_date }}</div>
        <div class="p-6 mt-16">
          <template v-if="!editable">
          <a :href="resource.link" target="_blank">
          <h3 class="title font-oswald text-xl font-bold leading-tight" v-html="resource.title" />
          </a>
          </template>

          <template v-if="editable">
          <h3 class="title font-oswald text-xl font-bold leading-tight"
            :contenteditable="editable"
            @blur="(e) => {resource.title = e.target.innerHTML}"
            v-html="resource.title" />
          </template>

          <div class="description pt-3 leading-tight" 
            :contenteditable="editable"
            @blur="(e) => {resource.excerpt = e.target.innerHTML}"
            v-html="resource.excerpt" />
        </div>
      </div>
      
    </div>
  </div>
</div>
</template>

<script>
import ImageInput from '@/Shared/ImageInput'
import Popper from 'vue-popperjs'
import axios from 'axios'

export default {
  data() {
    return {
      popperOptions: {
        placement: 'left', 
        modifiers: { 
          offset: { 
            offset: '0,10px,0,0' 
          }
        }
      },
      resource: this.block.resource
    }
  },
  props: {
    block: Object,
    id: 0,
    editable: false
  },
  watch: {
    'block.resource'(newVal, oldVal) {
      this.getResource()
    }
  },
  components: {
    ImageInput,
    Popper,
  },
  methods: {
    getResource() {
      if (!this.editable) {
        if (this.block.resource.id) {
          axios.get(this.route('resources.show', this.block.resource.id).url())
          .then((response) => {
            this.resource = response.data
          })
        }
      }
    }
  },
  mounted() {
    this.getResource()
  }
}
</script>