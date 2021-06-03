<template>
  <div v-observe-visibility="visibilityChanged">

  <template v-if="chartOptions.chart.type !== 'scatter'">
    <div 
      v-show="tooltip" 
      ref="mobileTooltip"
      class="relative rounded-r-xl rounded-tl-xxs border-solid border-3 font-normal bg-white rounded-bl-xl mb-1 text-xs overflow-hidden">
      
      <div @click="tooltip = !tooltip" class="absolute top-0 right-0 mt-1 mr-2 text-gray-600 text-sm cursor-pointer"><i class="far fa-lg fa-times-circle"></i></div>
      <div v-show="tooltipX != ''" class="font-oswald bg-gray-200 border-b-1 border-gray-400 w-full pl-4 pr-8 py-2">{{ tooltipX }}</div>
      <div v-show="tooltipY != ''" class="font-lato pl-4 pr-8 py-2">{{ tooltipY }}</div>
    </div>
    
    <apex-chart 
      ref="apexChartInstance"
      :height="height" 
      :type="chartOptions.chart.type" 
      :options="chartOptions" 
      :series="series" />
  </template>

  <template v-if="options.chart.type === 'scatter'">
    <div 
      v-show="plotlyTooltip" 
      ref="plotlyMobileTooltip"
      class="relative rounded-r-xl rounded-tl-xxs border-solid border-3 font-normal bg-white rounded-bl-xl mb-1 text-xs overflow-hidden">
      
      <div @click="plotlyTooltip = !plotlyTooltip" class="absolute top-0 right-0 mt-1 mr-2 text-gray-600 text-sm cursor-pointer"><i class="far fa-lg fa-times-circle"></i></div>
      <div class="font-oswald bg-gray-200 border-b-1 border-gray-400 w-full pl-4 pr-8 py-2" v-html="tooltipX" />
      <div class="font-lato pl-4 pr-8 py-2" v-html="tooltipY" />
    </div>

    <div class="relative">
      <plotly-vue
        ref="plotly"
        @hover="plotlyHover"
        @unhover="plotlyUnhover"
        @click="plotlyClick"
        :data="series" 
        :layout="plotly.layout" 
        :static-plot="false"
        :display-mode-bar="false" />

        <div :id="plotylTTId" class="absolute opacity-0 text-sm px-4 py-2 pointer-events-none rounded-r-xl border-solid border-3 font-normal border-gray-400 bg-white rounded-bl-xl transition-all ease-linear duration-100">
          <div class="text-sm font-oswald">{{ plotlyTTName }}</div>
          <div class="text-xs font-lato" v-html="plotlyTTData" />
        </div>

        <div class="absolute top-0 right-0 flex justify-end items-center">
          <popper trigger="click" root-class="downloadPopper" :options="popperOptions">
            <div class="popper flex flex-col text-xs bg-white border-2 w-32 border-gray-500 text-right p-1">
              <div class="cursor-pointer" @click="plotlyImageSVG">Download SVG</div>
              <div class="cursor-pointer" @click="plotlyImagePNG">Download PNG</div>
              <div class="cursor-pointer" @click="plotlyCSV">Download XLSX</div>
            </div>
            <div 
              slot="reference"
              :data-export="title"
              class="uppercase font-oswald text-gray-800 text-right cursor-pointer">
              <i class="far fa-arrow-to-bottom mr-1"></i></i> Export Data
            </div>
          </popper>
        </div>
      </div>

      <div v-if="series.length > 1" class="max-w-2xl mx-auto pt-6 pb-10 px-4 relative md:px-0">
        <p class="font-oswald">Toggle Scores</p>

        <div class="flex w-full flex-wrap -mt-2">
          <div v-for="(item, index) in series" v-if="item.showlegend == true" :key="index"
            class="md:w-20 w-1/3 mt-6 flex items-start justify-end flex-col">
            <p class="font-oswald text-sm pb-2 pr-8 leading-tight">{{ item.name }}</p>
            <div class="border-3 border-teal-100 rounded-sm">
              <span 
                @click="scatterLegendClick(index)" 
                class="bg-teal-100 relative flex justify-start items-center flex-shrink-0 h-4 w-8 cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
                <span aria-hidden="true" 
                  :style="(series[index].visible === true) ? 'background-color: '+series[index].color : ''"
                  :class="(series[index].visible === true) ? 'translate-x-4' : 'translate-x-0 bg-gray-800'"
                  class="translate-x-0 inline-block h-4 w-4 rounded-xs align-top transform transition ease-in-out duration-200"></span>
              </span>
            </div>
          </div>

          <div class="md:w-32 w-1/3 mt-6 flex items-start justify-end flex-col">
            <p class="font-oswald text-sm pb-2 pr-8 leading-tight">Global Average</p>
            <div class="border-3 border-teal-100 rounded-sm">
              <span 
                @click="scatterGlobalClick(series[series.length - 1])" 
                class="bg-teal-100 relative flex justify-start items-center flex-shrink-0 h-4 w-8 cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
                <span aria-hidden="true" 
                  :class="(series[series.length - 1].visible === true) ? 'translate-x-4 bg-teal-500' : 'translate-x-0 bg-gray-800'"
                  class="translate-x-0 inline-block h-4 w-4 rounded-xs align-top transform transition ease-in-out duration-200"></span>
                </span>
            </div>
          </div>
        </div>
      </div>
  </template>
  
  </div>
</template>

<script>
import mixins from '@/Mixins/mixinGraph.js'
import ApexChart from 'vue-apexcharts'
import { Plotly as PlotlyVue } from 'vue-plotly'
import Popper from 'vue-popperjs'

import axios from 'axios'

export default {
  mixins: [mixins],
  data() {
    return {
      isVisible: false,
      loaded: false,
      count: 0,
      tooltipX: '',
      tooltipY: '',
      tooltip: false,
      plotlyTooltip: false,
      popperOptions: {
        placement: 'bottom', 
        modifiers: { 
          offset: { 
            offset: '10,0,0,0' 
          }
        }
      },
    }
  },
  props: {
    graph: null,
    height: null,
    accordion: false,
    expand: true,
    title: '',
    filters: '',
    topContentOffset: 0,
    filterSelectionAge: null,
    filterSelectionGender: null,
    compareBy: false,
  },
  components: {
    ApexChart,
    PlotlyVue,
    Popper
  },
  watch: {
    graph(newVal) {
      this.createGraph(newVal)
    },
    expand(newVal) {
      if (this.isVisible === true && this.loaded === false && newVal === true) {
        this.createGraph(this.graph)
      }
    },
  },
  methods: {
    visibilityChanged (isVisible, entry) {
      this.isVisible = isVisible

      if (!this.accordion) {
        if (isVisible === true && this.loaded === false) {
          this.createGraph(this.graph)
        }
      } else {
        if (isVisible === true && this.loaded === false && this.expand === true) {
          this.createGraph(this.graph)
        }
      }
      
    },
    plotlyImagePNG() {
      let chartid = this.title.trim()
      if (this.filters != '') {
        chartid = chartid + ' ' + this.filters.trim()
      }

      chartid = this.slugify(chartid)

      let image = Plotly.toImage({data: this.series , layout: this.plotly.layout }, {format: 'png',height:400,width:900})
        .then((url) => {
          var a = document.createElement("a")
              a.href = url;
              a.download = chartid + '.png'
              a.click()
        })
    },
    plotlyImageSVG() {

      let chartid = this.title.trim()
      if (this.filters != '') {
        chartid = chartid + ' ' + this.filters.trim()
      }

      chartid = this.slugify(chartid)

      let image = Plotly.toImage({data: this.series , layout: this.plotly.layout }, {format: 'svg',height:400,width:900})
        .then((url) => {
          var a = document.createElement("a")
              a.href = url;
              a.download = chartid + '.svg'
              a.click()
        })
    },
    plotlyCSV() {

      let chartid = this.title.trim()
      if (this.filters != '') {
        chartid = chartid + ' ' + this.filters.trim()
      }

      chartid = this.slugify(chartid)
      
      axios.post(this.route('graphs.export').url(), {'plotly': this.evaluated, 'title': chartid })
        .then((response) => {
          var a = document.createElement("a")
              a.href = response.data
              a.download = response.data
              a.click()
        })
    },
    applyFilters(data) {
      axios.get(this.route('graphs.frontend', this.graph).url(), {params: data})
      .then((response) => {
        this.loaded = true;
        this.evaluated = response.data.series
        this.options = response.data.options
        this.updateSeries()
      })
    },
    createGraph(id) {
       axios.get(this.route('graphs.frontend', id).url())
      .then((response) => {
        this.loaded = true;
        this.evaluated = response.data.series
        this.options = response.data.options
        this.updateSeries()
        
      })
    },
  },
  created() {
    this.$parent.$on('applyFilter', this.applyFilters)
  },
}
</script>