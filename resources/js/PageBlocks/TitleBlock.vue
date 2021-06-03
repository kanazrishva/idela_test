<template>
<div class="py-8" :class="block.background">
  <div class="w-full max-w-4xl mx-auto" :class="block.alignment">
    <h3 class="font-oswald text-4xl pt-4 pb-5 uppercase focus:outline-none" 
      :contenteditable="editable" 
      @blur="handleInput" >{{ block.title }}</h3>
  </div>
</div>
</template>

<script>
export default {
  props: {
    block: Object,
    editable: false
  },
  methods: {
    handleInput(e) {
      this.block.title = e.target.innerHTML
    } 
  }
}
</script>