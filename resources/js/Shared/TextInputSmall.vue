<template>
  <div>
    <label v-if="label" class="block text-xs font-medium leading-5 mb-1 text-gray-700" :for="id">{{ label }}</label>
    <input 
      :id="id" ref="input" 
      v-bind="$attrs" 
      :class=" (errors.length) ? 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:shadow-outline-red' : ''" 
      :type="type" 
      :value="value"
      :placeholder="placeholder"
      @input="$emit('input', $event.target.value)"
      class="block text-xs w-full py-1 px-3 border border-gray-300 bg-white rounded-lg focus:shadow-outline-teal" >
    <div v-if="errors.length" class="mt-1 text-xs text-red-600">{{ errors[0] }}</div>
  </div>
</template>

<script>
export default {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `text-input-${this._uid}`
      },
    },
    type: {
      type: String,
      default: 'text',
    },
    value: [String, Number],
    label: String,
    placeholder: String,
    errors: {
      type: Array,
      default: () => [],
    },
  },
  methods: {
    focus() {
      this.$refs.input.focus()
    },
    select() {
      this.$refs.input.select()
    },
    setSelectionRange(start, end) {
      this.$refs.input.setSelectionRange(start, end)
    },
  },
}
</script>
