<template>
  <div>
    <label v-if="label" class="block text-sm font-medium leading-5 mb-1 text-gray-700" :for="id">{{ label }}</label>
    <select 
      :id="id" 
      ref="input" 
      v-model="selected" 
      v-bind="$attrs" 
      class="block form-select w-full py-3 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:shadow-outline-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5" 
      :class=" (errors.length) ? 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:shadow-outline-red' : ''">
      <slot />
    </select>
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
        return `select-input-${this._uid}`
      },
    },
    value: [String, Number, Boolean, Object, Array],
    label: String,
    errors: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      selected: this.value,
    }
  },
  watch: {
    selected(selected) {
      this.$emit('input', selected)
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
