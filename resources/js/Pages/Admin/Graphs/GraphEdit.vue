<template>
<admin>

  <admin-header :title="'Edit Graph'" :breadcrumbs="breadcrumbs" />  

  <div v-if="options != null" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <template v-if="options.chart.type !== 'scatter'">
      <apex-chart 
      v-if="this.options.chart.type === 'pie'"
      class="w-full border-2" 
      :height="'400px'" 
      type="pie" 
      :options="chartOptions" 
      :series="series" />

      <apex-chart 
      v-if="options.chart.type === 'donut'"
      class="w-full border-2" 
      :height="'400px'" 
      type="donut" 
      :options="chartOptions" 
      :series="series" />

      <apex-chart 
      v-if="options.chart.type !== 'pie' && options.chart.type !== 'donut'"
      class="w-full border-2" 
      :height="'400px'" 
      type="bar" 
      :options="chartOptions" 
      :series="series" />
    </template>

    <template v-if="options.chart.type === 'scatter'">
      <div class="relative">
        <plotly-vue
          ref="plotly"
          id="plotlyIdTest"
          @hover="plotlyHover"
          @unhover="plotlyUnhover"
          :data="series"
          :layout="plotly.layout" 
          :static-plot="false"
          :display-mode-bar="false" />
        <div :id="plotylTTId" class="absolute opacity-0 text-sm px-4 py-2 pointer-events-none rounded-r-xl border-solid border-3 font-normal border-gray-400 bg-white rounded-bl-xl transition-all ease-linear duration-100">
          <div class="text-sm font-oswald">{{ plotlyTTName }}</div>
          <div class="text-xs font-lato" v-html="plotlyTTData" />
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

  <div class="max-w-7xl mx-auto px-4 mt-6 grid grid-cols-3">
    <div class="col-span-1"></div>
    <div class="text-center col-span-1">
      <div @click="generateData" class="w-12 h-12 m-auto rounded-full flex justify-center items-center bg-teal-500">
        <i class="fad fa-sync text-white" />
      </div>
    </div>
    <div class="mr-4 text-right col-span-1 flex  justify-end items-center">
      <div>
      <div class="text-sm">Add Series</div>
      <div class="text-xs text-gray-400">Pie & Donut can only have a single series of data.</div>
      </div>
      <button @click="addSeries" class="ml-2 h-7 w-7 text-center border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-teal-500 hover:bg-teal-500 focus:outline-none focus:border-teal-700 focus:shadow-outline-teal active:bg-teal-700 transition ease-in-out duration-150">
        <i class="fad fa-fw fa-project-diagram"></i>
      </button>
    </div>
    
  </div>



  <draggable v-if="rows != null" v-model="rows" group="series" handle=".series-drag">
    <div v-for="(row, index) in rows" :key="row.id" class="mt-6 page-builder">
      <div class="max-w-7xl w-full mx-auto px-4">
        <!-- Heading Bar for Series -->
        <div class="bg-white px-4 py-1 border-b border-gray-200">
          <div class="flex justify-between items-center flex-wrap">
          
              <div class="flex items-center">
                <div class="flex-shrink-0 ml-4">
                  <i class="h-12 w-12 fa-fw fad fa-project-diagram series-drag"></i>
                </div>
                <div class="ml-2">
                  <h3 class="leading-tight text-sm font-medium text-gray-900"
                    contenteditable="true"
                    @blur="(e) => {row.name = e.target.innerText}" >
                    {{ row.name }}
                  </h3>
                </div>
              </div>
            
            <div class="flex-shrink-0 flex">
              <button @click="removeSeries(row.id)" 
                class="px-3 py-2 text-red-700 text-xs hover:text-red-500">
                <i class="fad fa-trash-alt"></i> remove
              </button>
              <button @click="row.expanded = !row.expanded"
                :class="(row.expanded) ? 'rotate-90' : 'rotate-0'"
                class="w-8 h-8 border rounded-full border-gray-200 transform focus:outline-none duration-200 transition-all cursor-pointer">
                <i class="fad fa-chevron-right"></i>
              </button>
            </div>
          </div>
        </div>
        <!-- End Heading Bar for Series -->

        <div v-show="row.expanded">
          <!-- Settings Bar -->
          <div class="bg-white border-b border-gray-200 px-4 py-2 grid grid-cols-4">
            <div class="col-span-3 mx-1 h-6 bg-white flex justify-start items-center">
              <div class="text-xs mr-4">Datasets</div>
              <multiselect
                v-model="row.dataset"
                :options="datasets"
                :hideSelected="false"
                :clear-on-select="false"
                :multiple="true"
                :limit="1"
                :allowEmpty="true"
                :showLabels="false"
                placeholder="Select at least one dataset"
                label="dataset_name"
                class="w-full mr-6"
                track-by="id">
                <!-- <template slot="selection" slot-scope="{ values, search, isOpen}">
                  <span v-if="(values.length > 1) && !isOpen">{{ values.length }} datasets selected</span>
                </template> -->
                <!-- <template slot="tag" slot-scope="{ option, remove }">
                  <span class="multiselect__single">{{ option.dataset_name }}</span>
                </template> -->
                <template slot="option" slot-scope="{option}">
                  <div class="text-black">{{ option.dataset_name }}</div>
                </template>


              </multiselect>
            </div>
            <div class="col-span-1 flex justify-end items-center">
                <div class="text-xs font-bold">Add Data Point(s)</div>
                <div @click="addSeriesData(row)" class="mx-1 w-6 h-6 cursor-pointer flex items-center justify-center">
                  <i class="fad fa-sm text-teal-500 fa-plus"></i>
                </div>
            </div>
          </div>
          <!-- End of Settings Bar -->

          <div class="p-4 bg-white">
              <!-- Content Section -->
              <draggable v-model="row.data" :group="'series-data' + index " handle=".dataitem">
                <div v-for="(dataitem, idx) in row.data" :key="idx" class="shadow-md border-3 border-gray-100 mb-4 p-2 flex items-center justify-evenly">
                  <i class="fad fa-bars dataitem mr-2"></i>

                  <multiselect
                    v-model="dataitem.item"
                    :options="equations"
                    :hideSelected="true"
                    :allowEmpty="false"
                    :showLabels="false"
                    label="name"
                    class="w-3/4 mr-6"
                    track-by="id">
                    <template slot="singleLabel" slot-scope="{ option }">
                      {{ option.name }}
                    </template>
                    <template slot="option" slot-scope="{option}">
                      <div class="text-black">{{ option.name }}</div>
                    </template>
                  </multiselect>

                  <text-input-small v-model="dataitem.label" class="w-1/4" />
                  
                  <button @click="removeDataItem(row, dataitem.id)" 
                    class="px-3 py-2 text-red-700 text-xs hover:text-red-500">
                    <i class="fad fa-trash-alt"></i> remove
                  </button>

                </div>
              </draggable>
              <!-- End Content Section -->
          </div>


        </div>
      </div>
    </div>
  </draggable>

  </div>

  <template v-if="options != null" v-slot:rightmenu>
    <div class="hidden md:flex md:flex-shrink-0">
    <div class="flex flex-col bg-gray-50" style="width: 24em">
      <div class="h-0 flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
        <div class="px-4">
          
          <div class="flex mb-6">
            <button @click="submit"
              class="w-3/4 p-2 r-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-teal-500 hover:bg-teal-500 focus:outline-none focus:border-teal-700 focus:shadow-outline-teal active:bg-teal-700 transition ease-in-out duration-150">
              <i class="fad fa-save mr-2"></i> Save Graph
            </button>

            <button class="w-1/4 ml-2 p-2 border border-transparent text-sm leading-4 font-medium rounded-md text-black bg-teal-100 hover:bg-teal-200 focus:outline-none focus:border-teal-700 focus:shadow-outline-teal active:bg-teal-700 transition ease-in-out duration-150"
              @click="$emit('closeGraphEdit')">
                <i class="fad fa-times mr-2"></i> Close
            </button>
          </div>

          <div class="-mx-4">
            <div class="border-t-3 border-b-3 border-gray-100 p-2 px-4 flex items-center justify-between">
              <h4 class="text-sm font-bold text-gray-600">Graph Information</h4>
              <button 
                @click="sidebar.infoAcc = !sidebar.infoAcc"
                :class="(sidebar.infoAcc) ? 'rotate-45' : 'rotate-0'" class="transform focus:outline-none text-teal-500 duration-200 transition-all cursor-pointer">
                <i  class="fad fa fa-plus"></i>
              </button>
            </div>
            <div class="bg-white p-4" v-show="sidebar.infoAcc">

              <select-input v-model="options.chart.type" @input="updateSeries()" label="Type of Graph" class="pb-6 w-full">
                <option value="bar">Bar Chart</option>
                <option value="line">Line Chart</option>
                <option value="area">Area Chart</option>
                <option value="pie">Pie Chart</option>
                <option value="donut">Donut Chart</option>
                <option value="scatter">Scatter Chart</option>
              </select-input>

              <text-input v-model="form.name" :errors="error('title')" class="pb-4" label="Graph Title" />
              <text-area v-model="form.description" class="pb-4" label="Graph Description" />
            </div>
          </div>


          <div class="-mx-4">
            <div class="border-t-3 border-b-3 border-gray-100 p-2 px-4 flex items-center justify-between">
              <h4 class="text-sm font-bold text-gray-600">General Options</h4>
              <button 
                @click="sidebar.generalAcc = !sidebar.generalAcc"
                :class="(sidebar.generalAcc) ? 'rotate-45' : 'rotate-0'" class="transform focus:outline-none text-teal-500 duration-200 transition-all cursor-pointer">
                <i  class="fad fa fa-plus"></i>
              </button>
            </div>
            <div class="bg-white p-4" v-show="sidebar.generalAcc">

              <select-input v-model="options.colors" @input="updateSeries()" class="pb-8 w-full" label="Graph Color Scheme">
                <option :value="colors.full">All</option>
                <option :value="colors.purple">Purple</option>
                <option :value="colors.gold">Gold</option>
                <option :value="colors.blue">Blue</option>
                <option :value="colors.red">Red</option>
                <option :value="colors.green">Green</option>
              </select-input>





              <toolbar :options.sync="options" />

            </div>
          </div>

          <div class="-mx-4">
            <div class="border-t-3 border-b-3 border-gray-100 p-2 px-4 flex items-center justify-between">
              <h4 class="text-sm font-bold text-gray-600">Y Axis Controls</h4>
              <button 
                @click="sidebar.yAxisAcc = !sidebar.yAxisAcc"
                :class="(sidebar.yAxisAcc) ? 'rotate-45' : 'rotate-0'" class="transform focus:outline-none text-teal-500 duration-200 transition-all cursor-pointer">
                <i  class="fad fa fa-plus"></i>
              </button>
            </div>
            <div class="bg-white p-4" v-show="sidebar.yAxisAcc">
              
              <div class="flex my-3 w-full text-xs font-bold justify-between items-center">
                Display Y-Axis
                <span 
                  @click="options.yaxis.show = !options.yaxis.show" 
                  class="bg-teal-100 ml-3 relative inline-block flex-shrink-0 h-6 w-11 border-3 border-transparent rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
                  <!-- On: "translate-x-5", Off: "translate-x-0" -->
                  <span aria-hidden="true" 
                    :class="options.yaxis.show ? 'translate-x-5 bg-teal-500' : 'translate-x-0 bg-gray-400'"
                    class="translate-x-0 inline-block h-5 w-5 rounded-sm shadow transform transition ease-in-out duration-200"></span>
                </span>
              </div>

              <div class="my-3">
                <div class="text-xs font-bold">Y-Axis Title</div>
                <text-input v-model="options.yaxis.title.text" class="w-full" />
              </div>
              
              <div class="my-3">
              <div class="text-xs font-bold">Y-Axis Minimum & Maximum Values</div>
              <div class="flex justify-evenly">
                <text-input v-model="options.yaxis.min" class="w-full pr-2" />
                
                <text-input v-model="options.yaxis.max" class="w-full pl-2" />
              </div>
              </div>

              <div class="my-3">
                <div class="text-xs font-bold">Y-Axis Tick Amount</div>
                <text-input v-model.number="options.yaxis.tickAmount" />
              </div>

              <div class="my-3">
                <div class="text-xs font-bold">Y-Axis Label Formatter</div>
                <text-input v-model="options.yaxisLabelFormatter" />
              </div>
            </div>
          </div>

          <div class="-mx-4">
            <div class="border-t-3 border-b-3 border-gray-100 p-2 px-4 flex items-center justify-between">
              <h4 class="text-sm font-bold text-gray-600">X Axis Controls</h4>
              <button 
                @click="sidebar.xAxisAcc = !sidebar.xAxisAcc"
                :class="(sidebar.xAxisAcc) ? 'rotate-45' : 'rotate-0'" class="transform focus:outline-none text-teal-500 duration-200 transition-all cursor-pointer">
                <i  class="fad fa fa-plus"></i>
              </button>
            </div>
            <div class="bg-white p-4" v-show="sidebar.xAxisAcc">

              <div class="my-3" v-if="options.chart.type == 'scatter'">
              <div class="text-xs font-bold">X-Axis Minimum & Maximum Values</div>
                <div class="flex justify-evenly">
                  <text-input v-model="options.xaxis.min" class="w-full pr-2" />
                  
                  <text-input v-model="options.xaxis.max" class="w-full pl-2" />
                </div>
              </div>

              <div class="my-3">
                <div class="text-xs font-bold">X-Axis Tick Amount</div>
                <text-input v-model.number="options.xaxis.tickAmount" />
              </div>
              
              <div class="my-3">
                <div class="text-xs font-bold">X-Axis Title</div>
                <text-input v-model="options.xaxis.title.text" class="w-full" />
              </div>

              <div class="my-3">
                <div class="text-xs font-bold pb-8 ">X-Axis Label Formatter</div>
                <text-input v-model="options.xaxisLabelFormatter" />
              </div>
            </div>
          </div>

          <div class="-mx-4">
            <div class="border-t-3 border-b-3 border-gray-100 p-2 px-4 flex items-center justify-between">
              <h4 class="text-sm font-bold text-gray-600">Data Labels</h4>
              <button 
                @click="sidebar.datalabelsAcc = !sidebar.datalabelsAcc"
                :class="(sidebar.datalabelsAcc) ? 'rotate-45' : 'rotate-0'" class="transform focus:outline-none text-teal-500 duration-200 transition-all cursor-pointer">
                <i  class="fad fa fa-plus"></i>
              </button>
            </div>
            <div class="bg-white p-4" v-show="sidebar.datalabelsAcc">
              
              <div class="flex my-3 w-full text-xs font-bold justify-between items-center">
                Display Data Labels
                <span 
                  @click="options.dataLabels.enabled = !options.dataLabels.enabled" 
                  class="bg-teal-100 ml-3 relative inline-block flex-shrink-0 h-6 w-11 border-3 border-transparent rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
                  <!-- On: "translate-x-5", Off: "translate-x-0" -->
                  <span aria-hidden="true" 
                    :class="options.dataLabels.enabled ? 'translate-x-5 bg-teal-500' : 'translate-x-0 bg-gray-400'"
                    class="translate-x-0 inline-block h-5 w-5 rounded-sm shadow transform transition ease-in-out duration-200"></span>
                </span>
              </div>

              <div class="my-3">
                <div class="text-xs font-bold">Format</div>
                <div class="text-xs text-gray-500 mb-2">You can use the following shortcodes to change the data in the data labels:<br><br>
                {val} - The value of the data</div>
                <text-input v-model.number="options.dataLabelsFormat" @change="updateSeries" class="w-full" />
              </div>
            </div>
          </div>

          <div class="-mx-4">
            <div class="border-t-3 border-b-3 border-gray-100 p-2 px-4 flex items-center justify-between">
              <h4 class="text-sm font-bold text-gray-600">Tooltips</h4>
              <button 
                @click="sidebar.tooltipsAcc = !sidebar.tooltipsAcc"
                :class="(sidebar.tooltipsAcc) ? 'rotate-45' : 'rotate-0'" class="transform focus:outline-none text-teal-500 duration-200 transition-all cursor-pointer">
                <i  class="fad fa fa-plus"></i>
              </button>
            </div>
            <div class="bg-white p-4" v-show="sidebar.tooltipsAcc">
              
              <div class="flex my-3 w-full text-xs font-bold justify-between items-center">
                Show Y-Axis based tooltips
                <span 
                  @click="options.tooltip.y.show = !options.tooltip.y.show" 
                  class="bg-teal-100 ml-3 relative inline-block flex-shrink-0 h-6 w-11 border-3 border-transparent rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
                  <!-- On: "translate-x-5", Off: "translate-x-0" -->
                  <span aria-hidden="true" 
                    :class="options.tooltip.y.show ? 'translate-x-5 bg-teal-500' : 'translate-x-0 bg-gray-400'"
                    class="translate-x-0 inline-block h-5 w-5 rounded-sm shadow transform transition ease-in-out duration-200"></span>
                </span>
              </div>

              <div class="my-3">
                <div class="text-xs font-bold">Y-Axis Data</div>
                <div class="text-xs text-gray-500 mb-2">
                  You can use the following shortcodes to change the data in the data labels depending on the type of graph selected:<br><br>
                  {val} - The value of the data<br> 
                  {qty} - The qty of items in this data<br>
                  {dataLabel} - X-Axis value, or the label within the series<br>
                  {seriesName} - The series title<br>
                </div>
                <text-input v-model.number="options.yaxisformat" class="pr-6 pb-8 w-full" />

                <div class="text-xs font-bold">Y-Axis Series</div>
                <div class="text-xs text-gray-500 mb-2">
                  You can use the following shortcodes to change the data in the data series depending on the type of graph selected:<br><br>
                  {seriesName} - The series title<br>
                </div>
                <text-input v-model.number="options.yaxisseries" class="pr-6 pb-8 w-full" />
              </div>
              
              <div class="flex my-3 w-full text-xs font-bold justify-between items-center">
                Show X-Axis based tooltips
                <span 
                  @click="options.tooltip.x.show = !options.tooltip.x.show" 
                  class="bg-teal-100 ml-3 relative inline-block flex-shrink-0 h-6 w-11 border-3 border-transparent rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
                  <!-- On: "translate-x-5", Off: "translate-x-0" -->
                  <span aria-hidden="true" 
                    :class="options.tooltip.x.show ? 'translate-x-5 bg-teal-500' : 'translate-x-0 bg-gray-400'"
                    class="translate-x-0 inline-block h-5 w-5 rounded-sm shadow transform transition ease-in-out duration-200"></span>
                </span>
              </div>

              <div class="my-3">
                <div class="text-xs font-bold">X-Axis Data</div>
                <div class="text-xs text-gray-500 mb-2">
                  You can use the following shortcodes to change the data in the data labels depending on the type of graph selected:<br><br>
                  {val} - The value of the data
                </div>
                <text-input v-model.number="options.xaxisformat" class="pr-6 pb-8 w-full" />
              </div>


              </div>
              
            </div>
          </div>

        </div>
      </div>
    </div>
    </div>
  </template>


</admin>
</template>

<script>

import Admin from '@/Layouts/TrimAdmin'
import ApexChart from 'vue-apexcharts'
import { Plotly as PlotlyVue } from 'vue-plotly'
import Draggable from 'vuedraggable'
import Multiselect from 'vue-multiselect'
import SelectInput from '@/Shared/SelectInput'
import Toolbar from '@/Pages/Admin/Graphs/Toolbar'

import mixinsGraph from '@/Mixins/mixinGraph.js'
import mixinsAdmin from '@/Mixins/mixinAdmin.js'
import axios from 'axios'

export default {
  mixins: [mixinsGraph, mixinsAdmin],
  data() {
    return {
      breadcrumbs: [
        {
          title: 'Graphs',
          link: 'graphs'
        }
      ],
      sidebar: {
        infoAcc: false,
        yAxisAcc: false,
        xAxisAcc: false,
        generalAcc: false,
        tooltipsAcc: false,
        datalabelsAcc: false
      },
      rows: this.graph && this.graph.rows || null,
      options: this.graph && this.graph.options || null,
      evaluated: [{
        labels: []
      }],
      form: {
        name: this.graph && this.graph.name || null,
        description: this.graph && this.graph.description || null,
      },
    }
  },
  props: {
    datasets: Array,
    codebook: Array,
    graph: Object,
    equations: Array,
  },
  components: {
    Admin,
    ApexChart,
    Draggable,
    SelectInput,
    PlotlyVue,
    Toolbar
  },
  /*
  *
  * Temp computed function placement 
  * 
  * 
  */
  watch: {
    graph: function(newVal) {
      this.rows = this.graph.rows
      this.options = this.graph.options
      this.form.name = this.graph.name
      this.form.description = this.graph.description
      
      console.log('graph changed')

      this.generateData()
    }
  },
  /*
  *
  * End temp computed function placement 
  * 
  * 
  */
  methods: {
    // Generate a random ID for the Rows ans Series Data
    generateId() {
      return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15)
    },
    removeSeries(id) {
      this.rows = this.rows.filter(function( obj ) {
          return obj.id !== id;
      });
    },
    removeDataItem(row, id) {
      row.data  = row.data.filter(function( obj ) {
          return obj.id !== id;
      });
    },
    // Adds a new Series of Data
    addSeries() {
      this.rows.push({
        id: this.generateId(),
        expanded: true,
        dataset: null,
        name: 'Series ' + (this.rows.length + 1),
        data: [{
          id: this.generateId(),
          label: '',
          item: ''
        }]
      })
    },
    // Add a data item to a series
    addSeriesData(row) {
      row.data.push({
        id: this.generateId(),
        label: '',
        item: ''
      })
    },
    // Update the Graph Information
    submit() {
      let params = {
        'rows': this.rows,
        'options': this.options,
        'name': this.form.name,
        'description': this.form.description
      }
      this.$inertia.put(this.route('graphs.update', this.graph.id), params)
    },
    // Method for getting the data convered into the graph format
    generateData() {
      axios.post(this.route('graphs.generate').url(), {'rows':this.rows, 'type':this.options.chart.type})
      .then((response) => {
        this.evaluated = response.data;
        this.updateSeries();
      })
    },
  },
  mounted() {
    //this.generateData()
  }
}
</script>

