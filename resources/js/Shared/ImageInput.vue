<template>
  <div>
    <label v-if="label" class="block text-sm font-medium leading-5 mb-1 text-gray-700">{{ label }}</label>

    <div class="relative form-input w-full p-2" :class="{ error: errors.length }">
      
      <!-- Begin Only Value Set -->
      <div class="relative flex items-center justify-center">
        <img v-if="value && imageData == null"
          class="relative w-auto"
          :src="imgix_domain+this.value+'?'+this.imgix">
      </div>
      <!-- End Only Value Set -->

      <!-- Begin Image Preview Exists -->
      <div v-if="imageData" class="relative w-full top-0 bottom-0 flex items-center justify-center bg-white">
        <img :src="imageData">
        
      </div>
      <!-- End Image Preview Exists -->

      <!-- Begin Value & Image Data are Null -->
      <!-- <div class="m-2 flex items-start justify-between" v-if="value == null && imageData == null">
          <p class="text-xs ml-12 text-gray-400">Add a new image</p>
          <div v-if="uploadProgress < 100 && uploadProgress !=0" class="flex flex-row items-center justify-center">
            <i class="fad fa-spinner fa-spin text-gray-500"></i>
            <p class="text-xs ml-1 font-bold text-gray-300">{{ uploadProgress }}%</p>
          </div>
      </div> -->
      <!-- End Value & Image Data are Null -->

     

    </div>
    <div class="toolbar relative border border-gray-200 grid grid-cols-2 p-1 mt-1 rounded-md">
    <!-- Begin Core Actions and Inputs -->
      <input ref="file" type="file" :accept="accept" class="hidden" @change="change">
      
      <div 
        :class="(value == null && imageData == null) ? 'col-span-2 justify-between' : 'col-span-1 justify-start'"
        class="flex items-center">
        <button type="button" 
          class="h-8 w-8 text-teal-500 flex items-center justify-center text-xs rounded-full bg-white border border-gray-200 focus:outline-none" 
          @click="browse">
          <i class="fad fa-images"></i>
        </button>

        <button 
          type="button" 
          v-if="notSaved" 
          @click="save" 
          class="h-8 w-8 ml-2 text-teal-500 flex items-center justify-center text-xs rounded-full bg-white border border-gray-200 focus:outline-none">
          <i class="fad fa-check"></i>
        </button>

        <div class="m-2 flex items-start justify-between" v-if="value == null && imageData == null">
            <p class="text-xs ml-12 text-gray-400">Add a new image</p>
            <div v-if="uploadProgress < 100 && uploadProgress !=0" class="flex flex-row items-center justify-center">
              <i class="fad fa-spinner fa-spin text-gray-500"></i>
              <p class="text-xs ml-1 font-bold text-gray-300">{{ uploadProgress }}%</p>
            </div>
        </div>

      </div>

      <div class="col-span-1 flex items-center justify-end">
        <button type="button" 
          @click.prevent="remove" 
          v-if="value != null || imageData != null"
          class="flex items-center justify-center text-xs rounded-full h-8 w-8 bg-white border border-gray-200 text-red-700 focus:outline-none">
          <i class="fad fa-trash-alt"></i>
        </button>
      </div>
      <!-- End Core Actions and Inputs -->
    </div>
    <div v-if="errors.length" class="form-error mt-1 text-xs text-red-600">{{ errors[0] }}</div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      notSaved: false,
      imageData: null, // Preview Image
      imageFile: null,
      originalValue: null,
      uploadProgress: 0
    }
  },
  props: {
    value: null, // This should just hold the existing URL
    imgix: null,
    directory: {
      type: String,
      default: ''
    },
    label: String,
    accept: String,
    errors: {
      type: Array,
      default: () => [],
    },
  },
  // watch: {
  //   value(value) {
  //     //console.log(value)
  //     // if (!value) {
  //     //   this.$refs.file.value = ''
  //     // }
  //   },
  // },
  methods: {
    filesize(size) {
      var i = Math.floor(Math.log(size) / Math.log(1024))
      return (size / Math.pow(1024, i) ).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i]
    },
    browse() {
      this.$refs.file.click()
    },
    change(e) {

      const files = e.target.files
      if (files && files[0]) {
        
        // Render the Preview to an image
        // const reader = new FileReader
        // reader.onload = e => {
        //   this.imageData = e.target.result
        // }
        // reader.readAsDataURL(files[0])

        // Transfer the file to S3 via Vapor
        Vapor.store(files[0], {
          progress: progress => {
            this.uploadProgress = Math.round(progress * 100);
          }
        }).then(response => {
          this.imageData = this.imgix_domain+'tmp/'+response.uuid+'?'+this.imgix
          this.imageFile = {
            upload: true,
            file: {
                directory: this.directory,
                uuid: response.uuid,
                key: response.key,
                bucket: response.bucket,
                name: this.$refs.file.files[0].name,
                content_type: this.$refs.file.files[0].type 
              }
            }
          this.notSaved = true

          // Add the file to uploads
          this.$emit('upload', this.imageFile)
        });
      }
    },
    save() {
      this.notSaved = false

      //copy the file over
       this.$inertia.post(this.route('pages.copy.image'), this.imageFile.file)
        .then(() => {
          console.log('this is complete')
          let url = 'public/' + this.directory + this.imageFile.file.uuid
          this.$emit('input', url)
        })

      // let url = 'public/' + this.directory + this.imageFile.file.uuid
      // this.$emit('input', url)
    },
    remove() {
      this.notSaved = false
      if (this.imageFile != null) {
        this.$emit('remove', this.imageFile)
        this.imageFile = null
        this.imageData = null
      } else {
        this.$emit('input', null)
      }
    },
  }
}
</script>
