<template>
  <div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
      <!-- Replace with your content -->
      <div class="py-4">
        <p>Updates have been moved to GitHub in related issues.</p>
        <br>
        <br>
        <inertia-link :href="route('logout')" method="post">Logout</inertia-link>
      
      </div>
      <!-- /End replace -->
    </div>
    
  </div>
</template>

<script>

import Admin from '@/Layouts/Admin'

export default {
  layout: Admin,
  components: {
 
  }
}
</script>