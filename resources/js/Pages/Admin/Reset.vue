<template>
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
  <div class="max-w-md w-full">
    <div>
      <h2 class="mt-6 mb-4 text-center text-3xl leading-9 font-extrabold text-gray-900">
        Reset your password
      </h2>
    </div>

    <div v-for="(error, index) in erroremail" :key="index" class="text-xs my-2 text-center text-red-500">{{ error }}</div>
    <div v-for="(error, index) in errorpassword" :key="index" class="text-xs my-2 text-center text-red-500">{{ error }}</div>


      <input type="hidden" name="remember" value="true" />
      <div class="rounded-md shadow-sm">
        <div>
          <label for="remember" class="ml-2 block text-xs leading-5 text-gray-900">
            Email address
          </label>
          <input aria-label="Password" 
            v-model="form.email" 
            name="email" 
            type="email" 
            required 
            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5" 
            placeholder="Email address" />
        </div>
      </div>

      <div class="rounded-md shadow-sm mt-2">
        <div>
          <label for="remember" class="ml-2 block text-xs leading-5 text-gray-900">
            Password
          </label>
          <input aria-label="Password" 
            v-model="form.password" 
            name="password" 
            type="password" 
            required 
            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5" 
            placeholder="Password" />
        </div>
      </div>

      <div class="rounded-md shadow-sm mt-2">
        <div>
          <label for="remember" class="ml-2 block text-xs leading-5 text-gray-900">
            Confirm Password
          </label>
          <input aria-label="Confirm Password" 
            v-model="form.passwordconfirm" 
            name="passwordconfirm" 
            type="password" 
            required 
            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5" 
            placeholder="Confirm Password" />
        </div>
      </div>

      <div class="mt-6">
        <button type="submit" @click="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-teal-600 hover:bg-teal-500 focus:outline-none focus:border-teal-700 focus:shadow-outline-indigo active:bg-teal-700 transition duration-150 ease-in-out">
          Reset Password
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
        email: this.email,
        token: this.token,
        password: '',
        passwordconfirm: ''
      },
      erroremail: [],
      errorpassword: []
    }
  },
  props: {
    email: '',
    token: ''
  },
  methods: {
    submit() {
      this.$inertia.post(this.route('password.update'), {
        email: this.form.email,
        token: this.form.token,
        password: this.form.password,
        'password_confirmation': this.form.passwordconfirm
      }).then(() => {
        this.sending = false
        this.erroremail = this.error('email')
        this.errorpassword = this.error('password')
      })
    }
  }
}
</script>