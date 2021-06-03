<template>
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
  <div class="max-w-md w-full">
    <div>
      <h2 class="mt-6 mb-4 text-center text-3xl leading-9 font-extrabold text-gray-900">
        Recover your account
      </h2>
    </div>

    <div v-if="errors.length > 0" v-for="(error, index) in errors" :key="index" class="text-xs my-2 text-center text-red-500">{{ error }}</div>
    <div v-if="success" class="text-xs my-2 text-center text-green-500">{{ success }}</div>


      <input type="hidden" name="remember" value="true" />
      <div class="rounded-md shadow-sm">
        <div>
          <input aria-label="Email address" v-model="form.email" name="email" type="email" required class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5" placeholder="Email address" />
        </div>
      </div>

      <div class="mt-6">
        <button type="submit" @click="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-teal-600 hover:bg-teal-500 focus:outline-none focus:border-teal-700 focus:shadow-outline-indigo active:bg-teal-700 transition duration-150 ease-in-out">
          Send
        </button>
      </div>


    <div class="mt-6 flex items-center justify-between">
      <div class="text-sm leading-5">
        <inertia-link href="/admin" class="font-medium text-teal-600 hover:text-teal-500 focus:outline-none focus:underline transition ease-in-out duration-150">
          Back to login
        </inertia-link>
      </div>
    </div>
  </div>
</div>
</template>

<script>
export default {
  data() {
    return {
      form: {
        email: ''
      },
      errors: {
        type: Array,
        default: () => [],
      },
      success: null,
    }
  },
  methods: {
    submit() {
      this.$inertia.post(this.route('password.email'), {
        email: this.form.email,
      }).then(() => {
        console.log(this.$page)
        this.sending = false
        this.errors = this.error('email')
        this.success = this.$page.flash.success
      })
    }
  }
}
</script>