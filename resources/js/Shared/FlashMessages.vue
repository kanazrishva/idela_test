<template>
  <div>
    <notifications group="flash" position="bottom right">
      <template slot="body" slot-scope="props">


        <div class="inset-0 flex items-end justify-center px-4 py-4 pointer-events-none sm:items-start sm:justify-end">
          <div class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto">
            <div class="rounded-lg shadow-xs overflow-hidden">
              <div class="p-4">
                <div class="flex items-start">
                  <div class="flex-shrink-0">
                    <i v-if="props.item.type == 'success'" class="fad fa-check-circle h-6 w-6 text-green-400"></i>
                    <i v-if="props.item.type == 'error'" class="fad fa-times-circle h-6 w-6 text-red-400"></i>
                  </div>
                  <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm leading-5 font-medium "
                      :class="(props.item.type == 'success') ? 'text-green-700' : 'text-red-700'"
                      v-html="props.item.title" />
                    <p class="mt-1 text-sm leading-5 text-gray-500" v-html="props.item.text" />
                  </div>
                  <div class="ml-4 flex-shrink-0 flex">
                    <button 
                      @click="props.close"
                      class="inline-flex text-gray-400 focus:outline-none focus:text-gray-500 transition ease-in-out duration-150">
                      <i class="fal fa-times"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </template>
    </notifications>

  </div>
</template>

<script>
export default {
  data() {
    return {
      show: true,
      flash: {}
    }
  },
  watch: {
    '$page.flash': {
      handler: 'displayNotification',
      deep: true,
    },
  },
  methods: {
    displayNotification() {

      if (this.$page.flash.success) {
        this.$notify({
          group: 'flash',
          title: 'Success!',
          type: 'success',
          text: this.$page.flash.success,
          duration: 5000,
          speed: 500
        })
      }


      if (this.$page.flash.error || Object.keys(this.$page.errors).length > 0) {

        let text = this.$page.flash.error

        if (Object.keys(this.$page.errors).length === 1) {
          text = "There are some errors."
        } else {
          text = `There are ${Object.keys(this.$page.errors.default).length } form errors.`
        }

        this.$notify({
          group: 'flash',
          title: 'Error',
          type: 'error',
          text: text,
          duration: 5000,
          speed: 500
        })
      }
    }
  }
}
</script>
