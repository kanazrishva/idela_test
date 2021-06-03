<template>
<div class="bg-black bg-opacity-75 h-screen overflow-scroll py-2">

<portal-target name="dropdown" slim />

<div class="flex bg-gray-100 ml-12">
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