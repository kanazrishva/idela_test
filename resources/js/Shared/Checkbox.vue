<template>
  <div class="relative flex items-start">

    <div class="absolute flex items-center h-5">
      <input 
        v-bind="$attrs" 
        :id="id" 
        type="checkbox"
        :value="value"
        :checked="shouldBeChecked"
        @change="updateInput"
        :class=" (errors.length) ? 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:shadow-outline-red' : ''"
        class="form-checkbox h-4 w-4 text-teal-500 transition duration-150 ease-in-out" />
    </div>
    <div class="pl-7 text-sm leading-5">
    <label v-if="label" :for="id" class="block text-sm font-medium leading-5 text-gray-700" :class="(errors.length) ? 'text-red-600' : ''">
      {{ label }}
    </label>
    <p 
      :class="(errors.length) ? 'text-red-600' : ''"
      class="text-gray-500 text-xs">{{ description }}</p>
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
        return `checkbox-${this._uid}`
      },
    },
    value: [String, Number],
    modelValue: {
      default: false
    },
    trueValue: {
      default: true
    },
    falseValue: {
      default: false
    },
    label: String,
    description: String,
    errors: {
      type: Array,
      default: () => [],
    },
  },
  computed: {
    shouldBeChecked() {
      if (this.modelValue instanceof Array) {
        return this.modelValue.includes(this.value)
      }

      return this.modelValue === this.trueValue
    }
  },
  methods: {
   updateInput(event) {
     let isChecked = event.target.checked

     if (this.modelValue instanceof Array) {
       let newValue = [...this.modelValue]

       if (isChecked) {
         newValue.push(this.value)
       } else {
         newValue.splice(newValue.indexOf(this.value), 1)
       }

       this.$emit('change', newValue)
     } else {
       this.$emit('change', isChecked ? this.trueValue : this.falseValue)
     }
   }
  },
}
</script>
