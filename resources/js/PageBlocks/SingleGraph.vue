<template>
<div class="w-full max-w-5xl mx-auto md:px-5">
  <div
    :class="accordionBlock"
    class="relative border-solid border-3 border-gray-500 border-l-0 border-r-0 bg-white my-4 py-5 px-4 md:px-16 transition duration-200 md:border-l-3 md:border-r-3">
    
    <popper trigger="clickToToggle" :visible-arrow="false" :root-class="'pagepopper'" :options="popperOptions">
      <div class="popper z-50 left-4 right-4 w-full focus:outline-none"
        :contenteditable="editable"
        @blur="(e) => {this.block.tooltip = e.target.innerHTML}" 
        v-html="this.block.tooltip" />
      <div slot="reference" class="cursor-pointer absolute top-6 left-6 text-gray-600">
      <i class="far fa-info-circle"></i>
      </div>
    </popper>

    <div v-if="block.accordion">
      <div 
        @click="accordionExpand = !accordionExpand"
        v-show="!accordionExpand"     
        :class="(accordionExpand) ? 'rotate-45' : 'rotate-0'"	        
        class="uppercase absolute top-5 right-4 font-oswald text-teal-500 text-right transform transition duration-200 cursor-pointer">	        
        <i class="fal fa-fw fa-2x fa-plus-circle"></i>
        </div>

        <div 
          @click="accordionExpand = !accordionExpand"
          v-show="accordionExpand"
          class="uppercase absolute top-5 right-4 font-oswald text-teal-500 text-right transform transition duration-200 cursor-pointer">
          <i class="fal fa-fw fa-2x fa-minus-circle"></i>
        </div>

        <div
          class="hidden relative top-6 justify-center uppercase text-black font-oswald font-xs items-center mb-8  
          md:flex md:absolute md:mb-0 md:right-16"
          v-if="!accordionExpand">
          Expand
        </div>

        <div
          class="hidden relative top-6 justify-center uppercase text-black font-oswald font-xs items-center mb-8  
          md:flex md:absolute md:mb-0 md:right-16"
          v-if="accordionExpand">
          Close
        </div>
      </div>


    <div ref="topContent" 
        :class="(block.accordion && !accordionExpand) ? 'md:w-3/4' : 'md:w-1/3'"
        class="w-8/12 ml-12 mb-8 md:ml-0">
      <h3 
        :class="(block.accordion && !accordionExpand) ? 'truncate ' : ''"
        class="font-oswald text-xl leading-6 mt-1 md:leading-8 focus:outline-none md:text-2xl md:mt-0" 
        :contenteditable="editable"
        @blur="(e) => {this.block.title = e.target.innerHTML}" 
        v-html="block.title" />

      <div class="mt-2 leading-tight focus:outline-none transition duration-0"
        :class="accordion"
        :contenteditable="editable"
        @blur="(e) => {this.block.description = e.target.innerHTML}"
        v-html="block.description" />
    </div>


    <div
      class="
      hidden relative top-6 justify-center uppercase text-black font-oswald font-xs items-center mb-8  
      md:flex md:absolute md:mb-0 md:right-16"
      v-if="!accordionExpand && block.accordion">
      Expand
    </div>


    <div v-show="filterDisplay != ''" class="w-full text-right text-xs">
      <span class="text-teal-500 uppercase tracking-wider font-oswald mr-2">Viewing:</span> {{ filterDisplay }}
      <p class="my-1">Note: filters are in place and exporting will include only filtered data</p>
    </div>

    <div 
      :class="accordion"
      class="transition duration-200">
      <graph class="w-full mt-2" 
        :height="'400px'" 
        v-if="block.graph !== null"
        :accordion="block.accordion" 
        :topContentOffset="topContentOffset"
        :filterSelectionAge="filterSelectionAge"
        :filterSelectionGender="filterSelectionGender"
        :compareBy="graphOverrides"
        :expand="accordionExpand"
        :title="block.title"
        :filters="filterDisplay"
        :graph="block.graph" />


      <div v-if="filtersShow" class="relative flex justify-end items-center mb-8">
        <div 
          @click="filter = !filter"
          class="uppercase font-oswald text-gray-800 text-right cursor-pointer">
          <i class="far fa-filter mr-1"></i></i> Filter Data
        </div>
      </div>
    </div>

    

    <div v-show="filter && filtersShow" class="border-t-4 mx-auto pt-6 relative">
      <div v-observe-visibility="visibilityChanged" class="flex w-full flex-col md:flex-row ">
        
        <div @click="filter = !filter" class="absolute top-0 right-0 mt-4 text-teal-500 cursor-pointer">
          <i class="far fa-2x fa-times-circle"></i>
        </div>

        <div 
          v-show="seriesCount < 2 && graphType == 'bar' && filtersShowCompareBy" 
          :class="((filtersShowCompareBy == true) && (filtersShowFilters == true)) ? 'border-b-2 md:border-r-2' : ''"
          class=" pb-2 md:border-b-0 md:pb-0">
          <div class="mr-8 pr-8">
            <p class="font-oswald text-teal-500 text-sm uppercase mb-2">Compare by</p>

            <div v-if="treatment != 0" class="flex justify-start items-center mb-1"> 
              <span 
                @click="compareByIntervention()" 
                class="bg-teal-100 mr-3 relative flex items-center px-1 flex-shrink-0 h-4 w-8 rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
                <span aria-hidden="true" 
                  :class="interventionStatus ? 'translate-x-3 bg-teal-500' : 'translate-x-0 bg-gray-800'"
                  class="translate-x-0 inline-block h-3 w-3 border01 border-transparent rounded-sm shadow transform transition ease-in-out duration-200"></span>
              </span>
              <p class="text-sm">Intervention Status</p>
            </div>

            <div class="flex justify-start items-center mb-1"> 
              <span 
                @click="compareByGender()" 
                class="bg-teal-100 mr-3 relative flex items-center px-1 flex-shrink-0 h-4 w-8 rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
                <span aria-hidden="true" 
                  :class="genderStatus ? 'translate-x-3 bg-teal-500' : 'translate-x-0 bg-gray-800'"
                  class="translate-x-0 inline-block h-3 w-3 border01 border-transparent rounded-sm shadow transform transition ease-in-out duration-200"></span>
              </span>
              <p class="text-sm">Gender</p>
            </div>
          </div>
        </div>

        <div 
          v-show="filtersShowFilters"
          class="flex flex-col mt-4 md:ml-8 md:mt-0">
          <p class="font-oswald text-teal-500 text-sm uppercase mb-2">Filter By</p>

          <div class="flex flex-row justify-between w-full md:justify-start">
            <div v-show="displayGender && filtersShowFiltersGender" class="mr-6 md:mr-12">
              <p class="text-sm font-oswald mb-2">Gender</p>
              <div v-for="(gender, key, index) in genders" :key="index" class="flex justify-start items-center mb-1">
                <span 
                  @click="filterGender(gender)" 
                  class="bg-teal-100 mr-3 relative flex items-center px-1 flex-shrink-0 h-4 w-8 rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
                  <span aria-hidden="true" 
                    :class="filterSelectionGender.includes(gender) ? 'translate-x-3 bg-teal-500' : 'translate-x-0 bg-gray-800'"
                    class="translate-x-0 inline-block h-3 w-3 border01 border-transparent rounded-sm shadow transform transition ease-in-out duration-200"></span>
                </span>
                <p class="text-sm">{{ key }}</p>
              </div>
            </div>

            <div class="mr-6 md:mr-12" v-if="(ages.length > 1) && filtersShowFiltersAge">
              <p class="text-sm font-oswald mb-2">Age</p>
              <div class="flex flex-row text-sm">
                <div v-for="(age, index) in ages" :key="index" class="flex items-center h-5 flex-col mr-2">
                  <input 
                    v-model="filterSelectionAge"
                    type="checkbox"
                    :value="age"
                    :class="(ages.length == (index + 1)) ? '' : 'checkbox-right'"
                    class="form-checkbox form-checkbox-front relative h-4 w-4 text-teal-500 transition duration-150 ease-in-out" />
                    {{ age }}
                </div>
              </div>
            </div>

            <div v-if="(locations.length > 1) && filtersShowFiltersLocation">
              <p class="text-sm font-oswald mb-2">Location</p>
              <div v-for="(location, index) in locations" :key="index" class="flex justify-start items-center mb-1">
                <span 
                  @click="filterLocation(location)" 
                  class="bg-teal-100 mr-3 relative flex items-center px-1 flex-shrink-0 h-4 w-8 rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
                  <span aria-hidden="true" 
                    :class="filterSelectionLocation.includes(location) ? 'translate-x-3 bg-teal-500' : 'translate-x-0 bg-gray-800'"
                    class="translate-x-0 inline-block h-3 w-3 border01 border-transparent rounded-sm shadow transform transition ease-in-out duration-200"></span>
                </span>
                <p class="text-sm">{{ location }}</p>
              </div>
            </div>
          </div>
        
        </div>

      </div>

      <div class="mt-10">
        <button @click="applyFilters" class="bg-teal-500 rounded-tr-lg rounded-b-lg px-4 py-3 uppercase text-white font-oswald leading-none tracking-wider focus:outline-none">Apply Filters</button>
        <button @click="clearFilters" class="text-gray-600 tracking-wider leading-none px-4 py-3 uppercase ml-6 font-oswald text-sm focus:outline-none">Clear Filters</button>
      </div>
    </div>

  </div>
</div>
</template>

<script>

import Graph from '@/Shared/Graph'
import Popper from 'vue-popperjs'
import axios from 'axios'

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
      },

      filtersShow: this.displayFilters(),
      filtersShowCompareBy: this.block.compareby,
      filtersShowFilters: this.block.filterby,
      filtersShowFiltersAge: this.block.filter_age,
      filtersShowFiltersGender: this.block.filter_gender,
      filtersShowFiltersLocation: this.block.filter_location,

      filter: false,
      filterApplied: false,
      filterSelectionGender: [],
      filterSelectionAge: [],
      filterSelectionLocation: [],
      filterDisplay: '',
      accordionExpand: this.block.expand,
      isVisible: false,
      topContentOffset: 0,
      // Filter Items
      genders: [],
      ages: [],
      locations: [],
      treatment: 0,
      seriesCount: 0,
      interventionStatus: false,
      genderStatus: false,
      graphType: '',
      graphOverrides: false
    }
  },
  props: {
    block: Object,
    editable: false
  },
  components: {
    Graph,
    Popper
  },
  watch: {
    'block.expand'(newVal, oldVal) {
      this.accordionExpand = newVal
    },
    'block.filterby'(newVal, oldVal) {
      this.filtersShow = this.displayFilters()
      this.filtersShowFilters = newVal
    },
    'block.compareby'(newVal, oldVal) {
      this.filtersShow = this.displayFilters()
      this.filtersShowCompareBy = newVal
    },
    'block.filter_age'(newVal, oldVal) {
      this.filtersShowFiltersAge = newVal
    },
    'block.filter_gender'(newVal, oldVal) {
      this.filtersShowFiltersGender = newVal
    },
    'block.filter_location'(newVal, oldVal) {
      this.filtersShowFiltersLocation = newVal
    }
  },
  computed: {
    displayGender() {
      let status = true
      if (this.genderStatus) {
        status = false
      }

      if (this.genders.length == 0) {
        status = false
      }

      return status
    },
    accordion() {
      let classes = 'block opacity-100'
      if (this.block.accordion) {
        //console.log('this has an accordion property')
        if (this.accordionExpand) {
          //console.log('The block is expanded')
        } else {
          //console.log('The block is colapsed')
          classes = 'hidden'
        }
      }

      return classes
    },
    accordionBlock() {
      let classes = 'block h-auto'
      if (this.block.accordion) {
        //console.log('this has an accordion property')
        if (this.accordionExpand) {
          //console.log('The block is expanded')
        } else {
          //console.log('The block is colapsed')
          classes = 'block h-20'
        }

        if (!this.block.withabove) {
          classes += ' md:rounded-tr-xl'
        } else {
          classes += '  -mt-4 border-t-0'
        }
      } else {
        classes += ' md:rounded-tr-xl'
      }

      if (this.block.endcap) {
        classes += ' md:rounded-bl-xl md:rounded-br-xl'
      } else {
        classes += ' md:rounded-br-none'
      }

      return classes
    }
  },
  methods: {
    displayFilters() {
      console.log(this.block.compareby)
      console.log(this.block.filterby)
      if ((this.block.compareby == undefined) || (this.block.filterby == undefined)) {
        return true
      }

      if ((this.block.compareby == true) || (this.block.filterby == true)) {
        return true
      }

      return false
    },
    applyFilters() {
      this.emitFilter()
      this.filterApplied = true
      this.showAllFilters()
    },
    clearFilters() {
      this.filterApplied = false
      this.filterSelectionGender = []
      this.filterSelectionAge = []
      this.filterSelectionLocation = []
      this.interventionStatus = false
      this.genderStatus = false
      this.filterDisplay = ''
      this.graphOverrides = false
      this.emitFilter()
      
    },
    emitFilter() {
      this.$emit('applyFilter', {
        filterSelectionGender: this.filterSelectionGender,
        filterSelectionAge: this.filterSelectionAge,
        filterSelectionLocation: this.filterSelectionLocation,
        compareByIntervetion: this.interventionStatus,
        compareByGender: this.genderStatus
      })
    },
    showAllFilters() {
      let gender = []
      let filter = ''
      let compareby = ''

      Object.keys(this.genders).find(key => {
        return this.filterSelectionGender.forEach(item => {
          if (this.genders[key] == item) {
            gender.push(key)
          }
        })
      })

      let filters = gender.concat(this.filterSelectionAge)
          filters = filters.concat(this.filterSelectionLocation)
      filter = filters.join(', ')


      if (this.interventionStatus) {
        compareby = 'Compare by Intervention Status'
        this.graphOverrides = true
        if (filters.length > 0) {
          compareby = compareby + ', filtering by ' + filters.join(', ')
        }
        filter = compareby
      }

      if (this.genderStatus) {
        compareby = 'Compare by Gender'
        this.graphOverrides = true
        if (filters.length > 0) {
          compareby = compareby + ', filtering by ' + filters.join(', ')
        }
        filter = compareby
      }

      if (!this.genderStatus && !this.interventionStatus) {
        this.graphOverrides = false
      }

      this.filterDisplay = filter
    },
    filterGender(gender) {
      if (this.filterSelectionGender.includes(gender)) {
        let index = this.filterSelectionGender.indexOf(gender)
        this.filterSelectionGender.splice(index, 1)
      } else {
        this.filterSelectionGender.push(gender)
      }
    },
    filterLocation(location) {
      if (this.filterSelectionLocation.includes(location)) {
        let index = this.filterSelectionLocation.indexOf(location)
        this.filterSelectionLocation.splice(index, 1)
      } else {
        this.filterSelectionLocation.push(location)
      }
    },
    visibilityChanged(isVisible, entry) {
      this.isVisible = isVisible

      if (isVisible === true && this.filter === true) {
        this.getFilters()
      }
    },
    getFilters() {
      axios.get(this.route('graphs.filters', this.block.graph).url())
      .then((response) => {
        this.genders = response.data.genders
        this.ages = response.data.ages
        this.treatment = response.data.treatment
        this.locations = response.data.districts
        this.seriesCount = response.data.seriesCount
        this.graphType = response.data.graphType
      })
    },
    compareByIntervention() {
      this.interventionStatus = !this.interventionStatus;
      if (this.genderStatus != false) {
        this.genderStatus = !this.interventionStatus;
      }
    },
    compareByGender() {
      this.genderStatus = !this.genderStatus;
      if (this.interventionStatus != false) {
        this.interventionStatus = !this.genderStatus;
      }
    },

    
  },
  mounted() {
    this.topContentOffset = this.$refs.topContent.offsetHeight
  }
}
</script>