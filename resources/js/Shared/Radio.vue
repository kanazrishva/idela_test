<template>
  <div class="relative flex items-start">

    <div class="absolute flex items-center h-5">
      <input 
        v-bind="$attrs"
        v-model="selected"
        :id="id" 
        type="radio"
        :class=" (errors.length) ? 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:shadow-outline-red' : ''"
        class="form-radio h-4 w-4 text-teal-500 transition duration-150 ease-in-out" />
    </div>
    <div class="pl-7 text-sm leading-5">
    <label v-if="label" :for="id" class="block text-sm font-medium leading-5 text-gray-700" :class="(errors.length) ? 'text-red-600' : ''">
      {{ label }}
    </label>
    </div>

  </div>
</template>

<script>
export default {
  inheritAttrs: false,
  model: {
    prop: 'modelValue',
    event: 'change'
  },
  props: {
    id: {
      type: String,
      default() {
        return `radio-${this._uid}`
      },
    },
    value: [String, Number],
    label: String,
    errors: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      selected: this.value
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
      this.$refs.input.change()
    },
  },
}
</script>
