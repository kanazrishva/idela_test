<template>
  <div>
    <label v-if="label" class="block text-sm font-medium leading-5 mb-1 text-gray-700">{{ label }}</label>

    <div class="form-input p-0" :class="{ error: errors.length }">
      <input ref="file" type="file" :accept="accept" class="hidden" @change="change">
      <div v-if="!value" class="p-2">
        <button type="button" class="px-4 py-1 bg-gray-500 hover:bg-gray-700 rounded-sm text-xs font-medium text-white" @click="browse">
          Browse
        </button>
      </div>
      <div v-else class="flex items-center justify-between p-2">
        <div class="flex-1 pr-1">{{ value.name }}</div>
        <button type="button" class="px-4 py-1 bg-gray-500 hover:bg-gray-700 rounded-sm text-xs font-medium text-white" @click="remove">
          Remove
        </button>
      </div>
      <div v-if="uploadProgress < 100 && uploadProgress !=0" class="flex flex-row items-center justify-center">
        <i class="fad fa-spinner fa-spin text-gray-500"></i>
        <p class="text-xs ml-1 font-bold text-gray-300">{{ uploadProgress }}%</p>
      </div>
    </div>

    <div v-if="errors.length" class="form-error mt-1 text-xs text-red-600">{{ errors[0] }}</div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      dataFile: null,
      uploadProgress: 0
    }
  },
  props: {
    value: [File, Object],
    label: String,
    accept: String,
    errors: {
      type: Array,
      default: () => [],
    },
  },
  watch: {
    value(value) {
      if (!value) {
        this.$refs.file.value = ''
      }
    },
  },
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
        Vapor.store(files[0], {
          progress: progress => {
            this.uploadProgress = Math.round(progress * 100);
          }
        }).then(response => {

          //console.log(response)
          this.dataFile = {
            upload: true,
            file: {
                extension: response.extension,
                directory: 'datasets',
                uuid: response.uuid,
                key: response.key,
                bucket: response.bucket,
                name: this.$refs.file.files[0].name,
                content_type: this.$refs.file.files[0].type 
              }
            }

          // Add the file to uploads
          this.$emit('input', this.dataFile.file)
        });
      }
      //this.$emit('input', e.target.files[0])
    },
    remove() {
      if (this.dataFile != null) {
        this.$emit('remove', this.dataFile)
        this.dataFile = null
      } else {
        this.$emit('input', null)
      }
    },
  },
}
</script>
