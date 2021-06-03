<template>
  <div>
    <label v-if="label" class="block text-sm font-medium leading-5 mb-1 text-gray-700" :for="id">{{ label }}</label>
    <textarea 
      :id="id" 
      ref="input" 
      v-bind="$attrs" 
      class="form-textarea focus:shadow-outline-teal w-full" 
      :class="(errors.length) ? 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:shadow-outline-red' : ''" 
      :value="value" @input="$emit('input', $event.target.value)" />
    <div v-if="errors.length" class="form-error mt-1 text-xs text-red-600">{{ errors[0] }}</div>
  </div>
</template>

<script>
export default {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `textarea-input-${this._uid}`
      },
    },
    value: String,
    label: String,
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
  },
}
</script>
