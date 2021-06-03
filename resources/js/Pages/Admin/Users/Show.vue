<template>
<div>
  <admin-header :title="'Update Your Information'" :breadcrumbs="breadcrumbs" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
      <!-- Replace with your content -->
      <div class="py-4">
        <form @submit.prevent="submit">
          <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
              <div class="md:col-span-1">
                <h3 class="text-lg font-medium leading-6 text-gray-900">User</h3>
                <p class="mt-1 text-sm leading-5 text-gray-500">
                  All fields are required.
                </p>
              </div>
              <div class="mt-5 md:mt-0 md:col-span-2">
                
                  <text-input v-model="form.name" :errors="error('name')" class="pr-6 pb-8" label="Name" />
                  <text-input v-model="form.email" :errors="error('email')" class="pr-6 pb-8" label="Email Address" />
              </div>
            </div>
          </div>

          <div class="bg-white shadow px-4 mt-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
              <div class="md:col-span-1">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Passwords.</h3>
                <p class="mt-1 text-sm leading-5 text-gray-500">
                  Passwords must be at least 8 characters long.<br>
                  Use a passphrase for even better security!
                </p>
              </div>
              <div class="mt-5 md:mt-0 md:col-span-2">
                  <text-input v-model="form.password" :errors="error('password')" class="pr-6 pb-8" label="Password" />
                  <text-input v-model="form.passwordconfirm" :errors="error('passwordconfirm')" class="pr-6 pb-8" label="Confirm Password" />

                  <loading-button :loading="sending" type="submit">Update your Information</loading-button>
              </div>
            </div>
          </div>
        </form>
          
        </div>
      </div>
      <!-- /End replace -->
    </div>
</div>
</template>

<script>

import Admin from '@/Layouts/Admin'
import mixins from '@/Mixins/mixinAdmin.js'

export default {
  layout: Admin,
  mixins: [mixins],
  data() {
    return {
      breadcrumbs: [
        {
          title: 'Users',
          link: 'users'
        },
      ],
      sending: false,
      form: {
        email: this.user.email,
        name: this.user.name,
        password: null,
        passwordconfirm: null
      }
    }
  },
  props: {
    user: Object
  },
  methods: {
    submit() {

      let form = {
        email: this.form.email,
        name: this.form.name
      }

      if (this.form.password) {
        form.password = this.form.password
        form.passwordconfirm = this.form.passwordconfirm
      }

      this.$inertia.put(this.route('users.update_current', this.user.id), form)
        .then(() => {
          this.form.password = null
          this.form.passwordconfirm = null
        })
    }
  }
}
</script>