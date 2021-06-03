<template>
<div>

<portal-target name="dropdown" slim />

<div class="h-screen flex bg-gray-100">
  <!-- Static sidebar for desktop -->
  <div class="flex flex-shrink-0">
    <div class="flex flex-col w-12 bg-gray-800">
      <div class="h-0 flex-1 flex flex-col pt-5 pb-4">
        <div class="flex items-center flex-shrink-0 px-4">
          <!-- <img class="h-8 w-auto" src="/img/logos/workflow-logo-on-dark.svg" alt="Workflow" /> -->
        </div>
        <!-- Sidebar component, swap this element with another sidebar if you like -->
        <nav class="mt-5 flex-1 px-2 bg-gray-800 relative">
          <popper trigger="hover" root-class="navpopper" :options="popperOptions">
            <div class="popper">
              Dashboard
            </div>
            <inertia-link :href="route('dashboard')" slot="reference" class="group relative flex items-center px-2 py-2 text-sm leading-5 font-medium text-gray-300 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition ease-in-out duration-150">
              <i class="fad fa-tachometer-alt-fast fa-fw mr-3 h-6 w-6 text-gray-400 group-hover:text-teal-300 group-focus:text-gray-300 transition ease-in-out duration-150"></i>
            </inertia-link>
          </popper>

          <popper trigger="hover" root-class="navpopper" :options="popperOptions">
            <div class="popper">
              Datasets
            </div>
            <inertia-link :href="route('datasets')" slot="reference" class="mt-1 group flex items-center px-2 py-2 text-sm leading-5 font-medium text-gray-300 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition ease-in-out duration-150">
              <i class="fad fa-database fa-fw mr-3 h-6 w-6 text-gray-400 group-hover:text-teal-300 group-focus:text-gray-300 transition ease-in-out duration-150"></i>
            </inertia-link>
          </popper>

          <popper trigger="hover" root-class="navpopper" :options="popperOptions">
            <div class="popper">
              Codebook
            </div>
            <inertia-link :href="route('codebook')" slot="reference" class="mt-1 group flex items-center px-2 py-2 text-sm leading-5 font-medium text-gray-300 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition ease-in-out duration-150">
              <i class="fad fa-sitemap fa-fw mr-3 h-6 w-6 text-gray-400 group-hover:text-teal-300 group-focus:text-gray-300 transition ease-in-out duration-150"></i>
            </inertia-link>
          </popper>

          <popper trigger="hover" root-class="navpopper" :options="popperOptions">
            <div class="popper">
              Equations
            </div>
            <inertia-link :href="route('equations')" slot="reference" class="mt-1 group flex items-center px-2 py-2 text-sm leading-5 font-medium text-gray-300 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition ease-in-out duration-150">
              <i class="fad fa-lambda fa-fw mr-3 h-6 w-6 text-gray-400 group-hover:text-teal-300 group-focus:text-gray-300 transition ease-in-out duration-150"></i>
            </inertia-link>
          </popper>

          <popper trigger="hover" root-class="navpopper" :options="popperOptions">
            <div class="popper">
              Graphs
            </div>
            <inertia-link :href="route('graphs')" slot="reference" class="mt-1 group flex items-center px-2 py-2 text-sm leading-5 font-medium text-gray-300 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition ease-in-out duration-150">
              <i class="fad fa-chart-bar fa-fw mr-3 h-6 w-6 text-gray-400 group-hover:text-teal-300 group-focus:text-gray-300 transition ease-in-out duration-150"></i>
            </inertia-link>
          </popper>

          <popper trigger="hover" root-class="navpopper" :options="popperOptions">
            <div class="popper">
              Pages
            </div>
            <inertia-link :href="route('pages')" slot="reference" class="mt-1 group flex items-center px-2 py-2 text-sm leading-5 font-medium text-gray-300 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition ease-in-out duration-150">
              <i class="fad fa-window fa-fw mr-3 h-6 w-6 text-gray-400 group-hover:text-teal-300 group-focus:text-gray-300 transition ease-in-out duration-150"></i>
            </inertia-link>
          </popper>

          <popper trigger="hover" root-class="navpopper" :options="popperOptions">
            <div class="popper">
              Resources
            </div>
            <inertia-link :href="route('resources')" slot="reference" class="mt-1 group flex items-center px-2 py-2 text-sm leading-5 font-medium text-gray-300 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition ease-in-out duration-150">
              <i class="fad fa-newspaper fa-fw mr-3 h-6 w-6 text-gray-400 group-hover:text-teal-300 group-focus:text-gray-300 transition ease-in-out duration-150"></i>
            </inertia-link>
          </popper>

          <popper trigger="hover" root-class="navpopper" :options="popperOptions">
            <div class="popper">
              Countries
            </div>
            <inertia-link :href="route('countries')" slot="reference" class="mt-1 group flex items-center px-2 py-2 text-sm leading-5 font-medium text-gray-300 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition ease-in-out duration-150">
              <i class="fad fa-flag fa-fw mr-3 h-6 w-6 text-gray-400 group-hover:text-teal-300 group-focus:text-gray-300 transition ease-in-out duration-150"></i>
            </inertia-link>
          </popper>

          <popper trigger="hover" root-class="navpopper" :options="popperOptions">
            <div class="popper">
              Regions
            </div>
            <inertia-link :href="route('regions')" slot="reference" class="mt-1 group flex items-center px-2 py-2 text-sm leading-5 font-medium text-gray-300 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition ease-in-out duration-150">
              <i class="fad fa-globe fa-fw mr-3 h-6 w-6 text-gray-400 group-hover:text-teal-300 group-focus:text-gray-300 transition ease-in-out duration-150"></i>
            </inertia-link>
          </popper>

          <popper trigger="hover" root-class="navpopper" :options="popperOptions">
            <div class="popper">
              Organizations
            </div>
            <inertia-link :href="route('organizations')" slot="reference" class="mt-1 group flex items-center px-2 py-2 text-sm leading-5 font-medium text-gray-300 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition ease-in-out duration-150">
              <i class="fad fa-list fa-fw mr-3 h-6 w-6 text-gray-400 group-hover:text-teal-300 group-focus:text-gray-300 transition ease-in-out duration-150"></i>
            </inertia-link>
          </popper>


          <popper trigger="hover" root-class="navpopper" :options="popperOptions">
            <div class="popper">
              Equities
            </div>
            <inertia-link :href="route('equities')" slot="reference" class="mt-1 group flex items-center px-2 py-2 text-sm leading-5 font-medium text-gray-300 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition ease-in-out duration-150">
              <i class="fad fa-list fa-fw mr-3 h-6 w-6 text-gray-400 group-hover:text-teal-300 group-focus:text-gray-300 transition ease-in-out duration-150"></i>
            </inertia-link>
          </popper>


          <popper trigger="hover" root-class="navpopper" :options="popperOptions">
            <div class="popper">
              Programs
            </div>
            <inertia-link :href="route('programs')" slot="reference" class="mt-1 group flex items-center px-2 py-2 text-sm leading-5 font-medium text-gray-300 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition ease-in-out duration-150">
              <i class="fad fa-list fa-fw mr-3 h-6 w-6 text-gray-400 group-hover:text-teal-300 group-focus:text-gray-300 transition ease-in-out duration-150"></i>
            </inertia-link>
          </popper>

          <popper trigger="hover" root-class="navpopper" :options="popperOptions">
            <div class="popper">
              Users
            </div>
            <inertia-link :href="route('users')" slot="reference" class="mt-1 group flex items-center px-2 py-2 text-sm leading-5 font-medium text-gray-300 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition ease-in-out duration-150">
              <i class="fad fa-users fa-fw mr-3 h-6 w-6 text-gray-400 group-hover:text-teal-300 group-focus:text-gray-300 transition ease-in-out duration-150"></i>
            </inertia-link>
          </popper>

        </nav>
      </div>

      <div class="flex-shrink-0 flex-col flex items-center justify-center bg-gray-700 p-4">
        <popper trigger="hover" root-class="navpopper" :options="popperOptions">
          <div class="popper">
            Your Account
          </div>

          <inertia-link :href="route('users.show', $page.auth.user.id)" slot="reference" class="flex-shrink-0 w-full group block">
            <div class="flex items-center">
              <i class="fad fa-user h-6 w-6 text-gray-400 group-hover:text-teal-300 group-focus:text-gray-300 transition ease-in-out duration-150"></i>
            </div>
          </inertia-link> 
        </popper>



        <popper trigger="hover" root-class="navpopper" :options="popperOptions">
          <div class="popper">
            Logout
          </div>
          <inertia-link :href="route('logout')" method="post" slot="reference" class="flex-shrink-0 mt-2 w-full group block">
            <div class="flex items-center">
              <i class="fad fa-power-off h-6 w-6 text-gray-400 group-hover:text-teal-300 group-focus:text-gray-300 transition ease-in-out duration-150"></i>
            </div>
          </inertia-link>
        </popper>

      </div>

    </div>
  </div>


  <div class="flex flex-col w-0 flex-1 overflow-hidden z-50">
    <main class="flex-1 relative z-0 overflow-y-auto pt-2 pb-6 focus:outline-none md:py-6" tabindex="0" x-data x-init="$el.focus()">
      <flash-messages />
      <slot></slot>
    </main>
  </div>

  <slot name="rightmenu"></slot>
</div>

</div>
</template>

<script>
import FlashMessages from '@/Shared/FlashMessages'
import Popper from 'vue-popperjs'

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
      }
    }
  },
  components: {
    FlashMessages,
    Popper
  }
}
</script>