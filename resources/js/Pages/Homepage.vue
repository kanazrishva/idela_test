<template>
<div>
  <div v-show="!listView && !mobileFirst" class="px-5 md:px-0">
    <!-- Pop up for Data Explorer -->
    <div v-show="introModal" class="relative mt-4 mb-4 z-20 top-0 w-full max-w-5xl pin-c-x md:fixed md:mt-20">
      <div class=" top-0 right-0 mt-12 border-3 border-teal-600 w-full max-w-sm bg-white p-4 flex flex-col justify-center items-center md:absolute">
        <div @click="introModal = !introModal" class="cursor-pointer">
          <i class="fal fa-2x fa-times-circle text-teal-500 mb-2"></i>
        </div>
        <h1 class="font-oswald uppercase text-xl">Welcome to the Data Explorer!</h1>
        <p class="text-center leading-tight">
        This tool allows you to visualize graphs of 100+ data sets, exposing key trends 
        with customizable visualizations. Drag and drop the map to explore by location, 
        or use filters to narrow choices in list view. Explore the unique findings in our 
        standard visualizations, then customize graphs for what you find most useful. 
        Finally, export! Take your visualizations with you for reports, presentations, 
        donors or advocacy.
        </p>
      </div>
    </div>
    <!-- End for Data Explorer -->


    <div class="w-full pb-5 md:px-5 md:pt-8 max-w-5xl mx-auto flex items-center justify-center flex-col md:items-start"> 
      <div class="font-oswald uppercase">Switch Data View</div>
      <div class="flex justify-center items-center font-oswald text-xs mt-2 leading-none uppercase">
        Map View
        <span 
        @click="listView = !listView" 
        class="bg-teal-100 mx-3 relative inline-block flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
          <span aria-hidden="true" 
            :class="listView ? 'translate-x-5' : 'translate-x-0'"
            class="translate-x-0 inline-block h-5 w-5 bg-teal-500 rounded-sm shadow transform transition ease-in-out duration-200"></span>
        </span>
        List View
      </div>
    </div>

    <div class="globe-container relative w-full max-w-xl h-72 mx-auto mb-20 mt-4 md:h-96 md:mt-0 ">
      <canvas @click="getPosition" ref="globe" id="globe" class="cursor-move"  />
      <div class="absolute -mb-8 bottom-0 right-0 w-20 text-teal-500 text-center text-xs font-oswald uppercase md:mb-0">
        <i class="fal fa-3x fa-arrows"></i>
        <p class="mt-2">Click and drag map to explore</p>
      </div>

      <div ref="hoverCountry"
        :class="((globe.hoverCountry != '') && (globe.selectedCountry == null)) ? 'block' : 'hidden'"
         class="absolute pointer-events-none transition-all ease-linear duration-75 top-0 left-0 z-30 w-full max-w-xs border-3 border-gray-500 bg-white rounded-r-xl rounded-bl-xl overflow-hidden">
        <div class="bg-gray-700 text-white py-2 px-4 font-oswald text-sm uppercase">
          <i class="fal fa-map-marker-alt"></i> <span class="ml-2">{{ globe.hoverCountry }} {{ globe.selectedCountry }}</span>
        </div>
      </div>
    </div>
    
    <!-- List of the Country Specific Pages -->
    <div ref="mappages" class="hidden md:absolute z-30 w-full md:max-w-xs border-3 border-gray-500 bg-white rounded-r-xl rounded-bl-xl overflow-hidden">
      <div class="bg-gray-700 text-white py-2 px-4 font-oswald text-sm uppercase">
        <i class="fal fa-map-marker-alt"></i> <span class="ml-2">{{ countrySelected }}</span>
        <div @click="closeMapPages" class="float-right text-white cursor-pointer">
          <i class="fal fa-times-circle"></i>
        </div>
      </div>

      <div v-show="mapViewLoaded == false" class="p-3 text-center text-sm text-gray-800">
        <i class="fad fa-spinner fa-spin"></i>
      </div> 

      <div  v-show="mapViewLoaded == true" v-for="(page, index) in mapView" class="w-full border-t-3 border-gray-500">
        <div class="py-3">
        <div class="w-full flex justify-between">
          <div class="text-xs text-teal-500 font-oswald px-4">
            {{ datasetYears(page.datasets) }}
          </div>
          <div class="text-xs text-teal-500 font-oswald px-4">
            Sample = {{ datasetSampleSize(page.datasets) }}
          </div>
        </div>
        <p class="font-oswald text-sm px-4 pt-1"><inertia-link :href="route('page', page.slug)">{{ page.title }}</inertia-link></p>
        </div>
      </div>

      <div v-show="mapViewLoaded == true" v-if="mapView.length == 0" class="p-3 text-center text-sm text-gray-800">
        <p>No datasets from {{ countrySelected }} are currently available. If you’re looking for information about our work in {{ countrySelected }} please 
          <a href="https://idela-network.org/connect/contact-us/" target="blank" class="text-teal-500">contact us</a>.</p>
      </div>

    </div>
    <!-- End List of Country Specicic Pages -->

      
    
  </div>
  

  <div v-show="listView || mobileFirst">

    <!-- Pop up for Data Explorer -->
    <div v-if="mobileFirst">
    <div v-show="introModal" class="relative mt-4 mb-4 z-20 top-0 mx-auto w-full max-w-5xl pin-c-x md:fixed md:mt-20">
      <div class="top-0 right-0 mx-auto mt-12 border-3 border-teal-600 w-full max-w-sm bg-white p-4 flex flex-col justify-center items-center md:absolute">
        <div @click="introModal = !introModal" class="cursor-pointer">
          <i class="fal fa-2x fa-times-circle text-teal-500 mb-2"></i>
        </div>
        <h1 class="font-oswald uppercase text-xl">Welcome to the Data Explorer!</h1>
        <p class="text-center leading-tight">
        This tool allows you to visualize graphs of 100+ data sets, exposing key trends 
        with customizable visualizations. Use filters to narrow your choices. 
        Explore the unique findings in our standard visualizations, then customize graphs 
        for what you find most useful. Finally, export! Take your visualizations with you 
        for reports, presentations, donors or advocacy.
        </p>
      </div>
    </div>
    </div>
    <!-- End for Data Explorer -->

    <div class="w-full flex-wrap md:pt-8 px-5 max-w-5xl mx-auto flex items-start justify-center">
        <div 
          :class="mobileFilter ? 'block' : 'hidden'"
          class="fixed w-full h-screen bg-black bg-opacity-75 z-50 top-0 md:hidden" />
        <div 
          :class="mobileFilter ? 'block' : 'hidden'"
          class="fixed h-screen z-50 top-0 left-0 -ml-1 w-4/5 border-3 bg-white rounded-r-xl rounded-bl-xl overflow-hidden md:relative md:block md:w-3/12 md:h-auto md:z-auto">
          <div class="flex w-full items-start justify-start flex-col p-4 relative"> 
            <div v-if="!mobileFirst" class="font-oswald uppercase">Switch Data View</div>
            <div v-if="mobileFirst" class="font-oswald uppercase">Filters</div>
            <div v-if="!mobileFirst" class="flex justify-start items-center font-oswald text-xs mt-2 leading-none uppercase">
              Map View
              <span 
              @click="listView = !listView" 
              class="bg-teal-100 mx-3 relative inline-block flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
                <span aria-hidden="true" 
                  :class="listView ? 'translate-x-5' : 'translate-x-0'"
                  class="translate-x-0 inline-block h-5 w-5 bg-teal-500 rounded-sm shadow transform transition ease-in-out duration-200"></span>
              </span>
              List View
            </div>
            <div @click="mobileFilter = !mobileFilter" class="absolute top-0 right-0 mt-4 mr-4 text-teal-500 cursor-pointer md:hidden">
              <i class="fal fa-2x fa-times-circle"></i>
            </div>
          </div>

          <div class="w-full relative z-20 px-4 py-2 bg-gray-300 text-teal-500 text-xs font-oswald uppercase flex justify-between items-center">
            Filter Data 
            <div v-show="showClearFilters" @click="clearFilters" class="text-xxs text-gray-700 leading-none cursor-pointer">clear filters</div>
          </div>
          
          <div 
            :class="mobileFirst ? 'mt-20' : 'mt-26'"
            class="overflow-scroll absolute z-10 pt-2 top-0 bottom-0 left-0 right-0 outline-none md:overflow-hidden md:relative md:mt-0 md:pt-0">
            
            
            
            <!-- Filter by Region -->
            <div>
              <div class="text-teal-500 uppercase text-sm font-oswald py-2 px-4 flex justify-between items-center">
                Region

                <div @click="filterRegion = !filterRegion" class="float-right cursor-pointer">
                  <div v-show="filterRegion"><i class="far fa-2x fa-minus-circle"></i></div>
                  <div v-show="!filterRegion"><i class="far fa-2x fa-plus-circle"></i></div>
                </div>
              </div>

              <div v-show="filterRegion" class="pb-4">
            
            
                <div v-for="(region, index) in regions" :key="index" class="flex justify-start items-center">
                  <span 
                  @click="filterRegions(region.name, region.id)" 
                  class="bg-teal-100 mx-3 relative flex items-center px-1 flex-shrink-0 h-4 w-8 rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
                    <span aria-hidden="true" 
                      :class="filterSelectionRegion.includes(region.name) ? 'translate-x-3 bg-teal-500' : 'translate-x-0 bg-gray-800'"
                      class="translate-x-0 inline-block h-3 w-3 border01 border-transparent rounded-sm shadow transform transition ease-in-out duration-200"></span>
                  </span>
                  <p class="text-sm">{{ region.name }}</p>
                </div>

              </div>
            </div>
            <!-- End Filter -->

            <!-- Filter by Country -->
            <div class="border-t-3">
              <div class="text-teal-500 uppercase text-sm font-oswald px-4 py-2 flex justify-between items-center">
                Country

                <div @click="filterCountry = !filterCountry" class="float-right cursor-pointer">
                  <div v-show="filterCountry"><i class="far fa-2x fa-minus-circle"></i></div>
                  <div v-show="!filterCountry"><i class="far fa-2x fa-plus-circle"></i></div>
                </div>
              </div>

              <div v-show="filterCountry" class="pb-4">
                
                <div v-for="(country, index) in countries" :key="index" class="flex justify-start items-center">
                  <span 
                  @click="filterCountries(country.country, country.id)" 
                  class="bg-teal-100 mx-3 relative flex items-center px-1 flex-shrink-0 h-4 w-8 rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
                    <span aria-hidden="true" 
                      :class="filterSelectionCountry.includes(country.country) ? 'translate-x-3 bg-teal-500' : 'translate-x-0 bg-gray-800'"
                      class="translate-x-0 inline-block h-3 w-3 border01 border-transparent rounded-sm shadow transform transition ease-in-out duration-200"></span>
                  </span>
                  <p class="text-sm">{{ country.country }}</p>
                </div>

              </div>
            </div>
            <!-- End Country -->

            <!-- Filter by Program -->
            <div class="border-t-3">
              <div class="text-teal-500 uppercase text-sm font-oswald px-4 py-2 flex justify-between items-center">
                Programs

                <div @click="filterProgram = !filterProgram" class="float-right cursor-pointer">
                  <div v-show="filterProgram"><i class="far fa-2x fa-minus-circle"></i></div>
                  <div v-show="!filterProgram"><i class="far fa-2x fa-plus-circle"></i></div>
                </div>
              </div>

              <div v-show="filterProgram" class="pb-4">
                
                <div v-for="(program, index) in programs" :key="index" class="flex justify-start items-center">
                  <span 
                  @click="filterPrograms(program.name, program.id)" 
                  class="bg-teal-100 mx-3 relative flex items-center px-1 flex-shrink-0 h-4 w-8 rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
                    <span aria-hidden="true" 
                      :class="filterSelectionPrograms.includes(program.name) ? 'translate-x-3 bg-teal-500' : 'translate-x-0 bg-gray-800'"
                      class="translate-x-0 inline-block h-3 w-3 border01 border-transparent rounded-sm shadow transform transition ease-in-out duration-200"></span>
                  </span>
                  <p class="text-sm">{{ program.name }}</p>
                </div>

              </div>
            </div>
            <!-- End Filter by Program

            <!-- Filter by Equities -->
            <div class="border-t-3">
              <div class="text-teal-500 uppercase text-sm font-oswald px-4 py-2 flex justify-between items-center">
                Equity

                <div @click="filterSectionEquity = !filterSectionEquity" class="float-right cursor-pointer">
                  <div v-show="filterSectionEquity"><i class="far fa-2x fa-minus-circle"></i></div>
                  <div v-show="!filterSectionEquity"><i class="far fa-2x fa-plus-circle"></i></div>
                </div>
              </div>

              <div v-show="filterSectionEquity" class="pb-4">
                
                <div v-for="(equity, index) in equities" :key="index" class="flex justify-start items-center">
                  <span 
                  @click="filterEquity(equity.name, equity.id)" 
                  class="bg-teal-100 mx-3 relative flex items-center px-1 flex-shrink-0 h-4 w-8 rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
                    <span aria-hidden="true" 
                      :class="filterSelectionEquity.includes(equity.name) ? 'translate-x-3 bg-teal-500' : 'translate-x-0 bg-gray-800'"
                      class="translate-x-0 inline-block h-3 w-3 border01 border-transparent rounded-sm shadow transform transition ease-in-out duration-200"></span>
                  </span>
                  <p class="text-sm">{{ equity.name }}</p>
                </div>

              </div>
            </div>
            <!-- End Equities -->

            <!-- Filter by Organizations -->
            <div class="border-t-3">
              <div class="text-teal-500 uppercase text-sm font-oswald px-4 py-2 flex justify-between items-center">
                Organizations

                <div @click="filterSectionOrganization = !filterSectionOrganization" class="float-right cursor-pointer">
                  <div v-show="filterSectionOrganization"><i class="far fa-2x fa-minus-circle"></i></div>
                  <div v-show="!filterSectionOrganization"><i class="far fa-2x fa-plus-circle"></i></div>
                </div>
              </div>

              <div v-show="filterSectionOrganization" class="pb-4">
                
                <div v-for="(org, index) in organizations" :key="index" class="flex justify-start items-center">
                  <span 
                  @click="filterOrganization(org.name, org.id)" 
                  class="bg-teal-100 mx-3 relative flex items-center px-1 flex-shrink-0 h-4 w-8 rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
                    <span aria-hidden="true" 
                      :class="filterSelectionOrg.includes(org.name) ? 'translate-x-3 bg-teal-500' : 'translate-x-0 bg-gray-800'"
                      class="translate-x-0 inline-block h-3 w-3 border01 border-transparent rounded-sm shadow transform transition ease-in-out duration-200"></span>
                  </span>
                  <p class="text-sm">{{ org.name }}</p>
                </div>

              </div>
            </div>
            <!-- End Organization -->

            <!-- Filter by Year -->
            <div class="border-t-3">
              <div class="text-teal-500 uppercase text-sm font-oswald px-4 py-2 flex justify-between items-center">
                Years

                <div @click="filterYear = !filterYear" class="float-right cursor-pointer">
                  <div v-show="filterYear"><i class="far fa-2x fa-minus-circle"></i></div>
                  <div v-show="!filterYear"><i class="far fa-2x fa-plus-circle"></i></div>
                </div>
              </div>

              <div v-show="filterYear" class="pb-4">
                
                <div v-for="(year, index) in years" :key="index" class="flex justify-start items-center">
                  <span 
                  @click="filterYears(year)" 
                  class="bg-teal-100 mx-3 relative flex items-center px-1 flex-shrink-0 h-4 w-8 rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
                    <span aria-hidden="true" 
                      :class="filterSelectionYear.includes(year) ? 'translate-x-3 bg-teal-500' : 'translate-x-0 bg-gray-800'"
                      class="translate-x-0 inline-block h-3 w-3 border01 border-transparent rounded-sm shadow transform transition ease-in-out duration-200"></span>
                  </span>
                  <p class="text-sm">{{ year }}</p>
                </div>

              </div>
            </div>
            <!-- End Year -->

            <!-- Filter by Sample Size -->
            <div class="border-t-3">
              <div class="text-teal-500 uppercase text-sm font-oswald px-4 py-2 flex justify-between items-center">
                Sample Size

                <div @click="filterSampleSize = !filterSampleSize" class="float-right cursor-pointer">
                  <div v-show="filterSampleSize"><i class="far fa-2x fa-minus-circle"></i></div>
                  <div v-show="!filterSampleSize"><i class="far fa-2x fa-plus-circle"></i></div>
                </div>
              </div>

              <div v-show="filterSampleSize" class="pb-4">
                <div class="px-8 pt-8">
                  <vue-slider ref="slider" 
                    :min="0" 
                    :max="size"
                    :height="4" 
                    :tooltip="'always'"
                    :dot-height="8"
                    :dot-width="8"
                    @change="sliderFilter"
                    v-model="searchSize">
                    <template v-slot:dot="{ value, focus }">
                      <div :class="['bg-teal-500 h-3 w-3 rounded-full', { focus }]"></div>
                    </template>
                    <template v-slot:tooltip="{ value, focus }">
                      <div :class="['bg-teal-500 text-xs text-white p-1 rounded-sm', { focus }]">{{ value }}</div>
                    </template>

                    <template v-slot:process="{ start, end, style, index }">
                      <div class="vue-slider-process bg-teal-500" :style="[style]">
                        <!-- Can add custom elements here -->
                      </div>
                    </template>
                    </vue-slider>
                </div>
              </div>
            </div>
            <!-- End Sample Size -->

            <!-- Filter by Caregiver -->
            <div class="border-t-3">
              <div class="text-teal-500 uppercase text-sm font-oswald px-4 py-2 flex justify-between items-center">
                Caregiver

                <div @click="filterSectionCaregiver = !filterSectionCaregiver" class="float-right cursor-pointer">
                  <div v-show="filterSectionCaregiver"><i class="far fa-2x fa-minus-circle"></i></div>
                  <div v-show="!filterSectionCaregiver"><i class="far fa-2x fa-plus-circle"></i></div>
                </div>
              </div>

              <div v-show="filterSectionCaregiver" class="pb-4">
                
                <div class="flex justify-start items-center">
                  <span 
                  @click="filterCaregiver()" 
                  class="bg-teal-100 mx-3 relative flex items-center px-1 flex-shrink-0 h-4 w-8 rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
                    <span aria-hidden="true" 
                      :class="searchCaregiver ? 'translate-x-3 bg-teal-500' : 'translate-x-0 bg-gray-800'"
                      class="translate-x-0 inline-block h-3 w-3 border01 border-transparent rounded-sm shadow transform transition ease-in-out duration-200"></span>
                  </span>
                  <p class="text-sm">Has Caregiver Data</p>
                </div>

              </div>
            </div>
            <!-- End Caregiver -->
          </div>
        </div>

        <div class="w-full md:w-9/12">
          <div class="block w-full flex justify-between items-center md:justify-end">
            <div @click="mobileFilter = !mobileFilter" class="font-oswald text-gray-600 text-xs uppercase md:hidden">
              <i class="fal fa-filter"></i>
              Filter
            </div>
            <!-- <div class="breadcrumbs font-oswald text-gray-400 uppercase text-right">
              Home / <span class="text-black">Data Sets</span>
            </div> -->
          </div>
          <div class="w-full mt-6 mb-4 flex flex-col justify-start items-start md:pl-5 md:flex-row">
            <div class="w-full justify-start items-start flex-wrap flex md:w-3/4">
              <div v-for="(item, index) in filterSelectionRegion"
                class="rounded-sm flex justify-center items-center mb-2 bg-teal-400 text-white text-xs leading-none py-1 px-2 mr-2">
                {{ item }} &nbsp;
                <div @click="filterRegions(item, searchRegions[index])">
                  <i class="fas fa-times cursor-pointer"></i>
                </div>
              </div>

              <div v-for="(item, index) in filterSelectionCountry"
                class="rounded-sm flex justify-center items-center mb-2 bg-teal-400 text-white text-xs leading-none py-1 px-2 mr-2">
                {{ item }} &nbsp;
                <div @click="filterCountries(item, searchCountries[index])">
                  <i class="fas fa-times cursor-pointer"></i>
                </div>
              </div>

              <div v-for="(item, index) in filterSelectionPrograms"
                class="rounded-sm flex justify-center items-center mb-2 bg-teal-400 text-white text-xs leading-none py-1 px-2 mr-2">
                {{ item }} &nbsp;
                <div @click="filterPrograms(item, searchPrograms[index])">
                  <i class="fas fa-times cursor-pointer"></i>
                </div>
              </div>

              <div v-if="searchCaregiver"
                class="rounded-sm flex justify-center items-center mb-2 bg-teal-400 text-white text-xs leading-none py-1 px-2 mr-2">
                Has Caregiver Data &nbsp;
                <div @click="filterCaregiver()">
                  <i class="fas fa-times cursor-pointer"></i>
                </div>
              </div>

              <div v-for="(item, index) in filterSelectionYear"
                class="rounded-sm flex justify-center items-center mb-2 bg-teal-400 text-white text-xs leading-none py-1 px-2 mr-2">
                {{ item }} &nbsp;
                <div @click="filterYears(item)">
                  <i class="fas fa-times cursor-pointer"></i>
                </div>
              </div>

              <div v-for="(item, index) in filterSelectionOrg"
                class="rounded-sm flex justify-center items-center mb-2 bg-teal-400 text-white text-xs leading-none py-1 px-2 mr-2">
                {{ item }} &nbsp;
                <div @click="filterOrganization(item, searchOrg[index])">
                  <i class="fas fa-times cursor-pointer"></i>
                </div>
              </div>

              <div v-for="(item, index) in filterSelectionEquity"
                class="rounded-sm flex justify-center items-center mb-2 bg-teal-400 text-white text-xs leading-none py-1 px-2 mr-2">
                {{ item }} &nbsp;
                <div @click="filterEquity(item, searchEquity[index])">
                  <i class="fas fa-times cursor-pointer"></i>
                </div>
              </div>

              <div v-show="(searchSize[0] >= 0 && searchSize[1]+1 < size)"
                class="rounded-sm flex justify-center items-center mb-2 bg-teal-400 text-white text-xs leading-none py-1 px-2 mr-2">
                size: {{ searchSize[0] }} - {{ searchSize[1] }} &nbsp;
                <div @click="resetSize()">
                  <i class="fas fa-times cursor-pointer"></i>
                </div>
              </div>
            </div>

            <div class="w-full md:w-1/4 relative">
              <input 
                type="text"
                v-model="searchText" 
                ref="search"
                @keyup="updateSearch"
                class="form-input bg-teal-100 text-black rounded-md border-none text-xs w-full h-8 px-4"  
                placeholder="Search" />

                <div v-show="searchText.length > 0" @click="clearSearch" class="absolute top-0 right-0 mt-1 mr-2 text-gray-700 cursor-pointer">
                  <i class="fal fa-times-circle"></i>
                </div>
            </div>
          </div>

          <div class="w-full flex flex-wrap">
            
            <div v-for="(page, index) in listPages" class="w-full md:w-1/2 md:pl-5 pb-5">
              <inertia-link :href="route('page', page.slug)">
              <div class="border-3 border-gray-500 w-full bg-white rounded-r-xl rounded-bl-xl overflow-hidden shadow-none transition-all ease-in duration-200 hover:border-yellow-600 hover:shadow-yellow-600">
                <div class="bg-gray-700 text-white py-2 px-4 font-oswald text-sm uppercase">
                  <i class="fal fa-map-marker-alt"></i> <span class="ml-2">{{ page.hero.country }}</span>
                  <div class="float-right">
                      {{ datasetYears(page.datasets) }}
                  </div>
                </div>
                <p class="text-xxs text-teal-500 font-oswald px-6 pt-2 text-right">Sample = {{ datasetSampleSize(page.datasets) }}</p>
                <p class="font-oswald text-sm px-6 pb-2">{{ page.title }}</p>
              </div>
              </inertia-link>
            </div>

            <div v-if="listPages.length == 0" class="w-full">
              <p class="text-center text-sm text-gray-700 p-4">No results found based on the current filters and/or search</p>
            </div>
        
            
          </div>
        </div>
    </div>
  </div>
</div>
</template>

<script>
import Main from '@/Layouts/Main'
import Vue from 'vue'

import VueSlider from 'vue-slider-component'
import axios from 'axios'
import throttle from 'lodash/throttle'
import mapJson from '@/custom.geo.json'
import * as d3 from "d3"
import * as topojson from "topojson"

export default {
  layout: Main,
  data() {
    return {
      mobileFirst: false,
      mobileFilter: false,
      listView: false,
      mapView: this.mapPages,
      mapViewLoaded: false,
      introModal: true,
      filterSectionCaregiver: false,
      filterRegion: false,
      filterCountry: false,
      filterProgram: false,
      filterSectionOrganization: false,
      filterSectionEquity: false,
      filterYear: false,
      filterSampleSize: false,
      filterSelectionRegion: [],
      filterSelectionCountry: [],
      filterSelectionYear: [],
      filterSelectionOrg: [],
      filterSelectionEquity: [],
      filterSelectionPrograms: [],
      searchRegions: [],
      searchCountries: [],
      searchOrg: [],
      searchPrograms: [],
      searchEquity: [],
      searchCaregiver: false,
      searchSize: [0, this.size],
      searchText: '',
      countryList: null,
      countrySelected: null,
      showClearFilters: false,
      globe: {
        hoverCountry: '',
        countryList: null,
        selectedCountry: null,
        autorotate: null,
        rotationDelay: 3000,
        scaleFactor: 1,
        degPerSec: 6,
        angles: {
          x: -20,
          y: 40,
          z: 0
        },
        colorWater: 'transparent',
        colorLand: '#0A838C',
        colorLandBackface: '#0A555B',
        colorGraticule: '#CCCCCC',
        colorCountry: '#09A2AC',
        selectedColorCountry: '#FF0000',
        canvas: null,
        context: null,
        water: {
          type: 'Sphere'
        },
        projection: d3.geoOrthographic().precision(.25),
        graticule: d3.geoGraticule10(),
        path: null,
        v0: 0,
        r0: 0,
        q0: 0,
        lastTime: null,
        degPerMs: null,
        width: null,
        height: null,
        land: null,
        countries: null,
        now: null,
        diff: null,
        rotation: null,
        currentCountry: null,
      }
    }
  },
  components: {
    VueSlider
  },
  props: {
    pages: [Object, Array],
    mapPages: [Object, Array],
    listPages: [Object, Array],
    size: [String, Number],
    years: [Object, Array],
    regions: [Object, Array],
    countries: [Object, Array],
    organizations: [Object, Array],
    equities: [Object, Array],
    programs: [Object, Array]
  },
  methods: {
    datasetYears(datasets) {
      let years = datasets.map(d => d.dataset_year).sort()
      let strYears
      if (years.length == 1) {
        strYears = years[0]
      }

      if (years.length == 2) {
        strYears = years.join(' & ')
      }

      if (years.length > 2) {
        strYears = years.map((val, index) => {
          return val
        })
        
        strYears = strYears.join(', ')
        strYears = strYears.replace(/,(?=[^,]*$)/, " &")
      }

      return strYears
    },

    datasetSampleSize(datasets) {
      return datasets.map(d => d.total_rows).reduce((a, b) => a + b, 0)
    },

    canIClearFilters() {
      this.showClearFilters = true
      if (this.filterSelectionRegion.length == 0 && 
        this.filterSelectionCountry.length == 0 && 
        this.filterSelectionYear.length == 0 &&
        this.filterSelectionOrg.length == 0 &&
        this.filterSelectionEquity.length == 0 &&
        this.filterSelectionPrograms.length == 0 &&
        this.searchCaregiver == false &&
        this.searchSize[0] == 0 &&
        this.searchSize[1] == this.size) {
        this.showClearFilters = false
      }
    },

    clearFilters() {
      this.filterSelectionRegion = []
      this.searchRegions = []
      this.filterSelectionCountry = []
      this.searchCountries = []
      this.filterSelectionYear = []
      this.filterSelectionOrg = []
      this.filterSelectionPrograms = []
      this.searchOrg = []
      this.searchPrograms = []
      this.filterSelectionEquity = []
      this.searchEquity = []
      this.searchSize = [0, this.size]

      this.filterCall()
      this.canIClearFilters()
    },

    clearSearch() {
      this.searchText = ''
      this.filterCall()
    },

    filterRegions(item, id) {
      if (this.filterSelectionRegion.includes(item)) {
        let index = this.filterSelectionRegion.indexOf(item)
        this.filterSelectionRegion.splice(index, 1)
      } else {
        this.filterSelectionRegion.push(item)
      }

      if (this.searchRegions.includes(id)) {
        let index = this.searchRegions.indexOf(id)
        this.searchRegions.splice(index, 1)
      } else {
        this.searchRegions.push(id)
      }
      this.canIClearFilters()
      this.filterCall()
    },
    
    filterCountries(item, id) {
      if (this.filterSelectionCountry.includes(item)) {
        let index = this.filterSelectionCountry.indexOf(item)
        this.filterSelectionCountry.splice(index, 1)
      } else {
        this.filterSelectionCountry.push(item)
      }

      if (this.searchCountries.includes(id)) {
        let index = this.searchCountries.indexOf(id)
        this.searchCountries.splice(index, 1)
      } else {
        this.searchCountries.push(id)
      }
      this.canIClearFilters()
      this.filterCall()
    },

    filterYears(item) {
      if (this.filterSelectionYear.includes(item)) {
        let index = this.filterSelectionYear.indexOf(item)
        this.filterSelectionYear.splice(index, 1)
      } else {
        this.filterSelectionYear.push(item)
      }
      this.canIClearFilters()
      this.filterCall()
    },

    filterOrganization(item, id) {
      if (this.filterSelectionOrg.includes(item)) {
        let index = this.filterSelectionOrg.indexOf(item)
        this.filterSelectionOrg.splice(index, 1)
      } else {
        this.filterSelectionOrg.push(item)
      }

      if (this.searchOrg.includes(id)) {
        let index = this.searchOrg.indexOf(id)
        this.searchOrg.splice(index, 1)
      } else {
        this.searchOrg.push(id)
      }
      this.canIClearFilters()
      this.filterCall()
    },

    filterEquity(item, id) {
      if (this.filterSelectionEquity.includes(item)) {
        let index = this.filterSelectionEquity.indexOf(item)
        this.filterSelectionEquity.splice(index, 1)
      } else {
        this.filterSelectionEquity.push(item)
      }

      if (this.searchEquity.includes(id)) {
        let index = this.searchEquity.indexOf(id)
        this.searchEquity.splice(index, 1)
      } else {
        this.searchEquity.push(id)
      }
      this.canIClearFilters()
      this.filterCall()
    },

    filterPrograms(item, id) {
      if (this.filterSelectionPrograms.includes(item)) {
        let index = this.filterSelectionPrograms.indexOf(item)
        this.filterSelectionPrograms.splice(index, 1)
      } else {
        this.filterSelectionPrograms.push(item)
      }

      if (this.searchPrograms.includes(id)) {
        let index = this.searchPrograms.indexOf(id)
        this.searchPrograms.splice(index, 1)
      } else {
        this.searchPrograms.push(id)
      }
      this.canIClearFilters()
      this.filterCall()
    },

    filterCaregiver() {
      this.searchCaregiver = !this.searchCaregiver
      this.filterCall()
      this.canIClearFilters()
    },

    // Formatted to accomodate lodash
    sliderFilter: Vue.lodash.throttle(function() {
      this.canIClearFilters()
      this.filterCall()
    }, 150),

    updateSearch: Vue.lodash.throttle(function() {
      this.filterCall()
    }, 150),

    resetSize() {
      this.searchSize = [0, this.size]
      this.filterCall()
    },

    countryClicked(country) {
      if (country != undefined) {

        let selectable = false
        this.mapViewLoaded = false
        this.globe.hoverCountry = ''

        this.countries.forEach((availableCountry) => {
          this.globe.countryList.forEach((c) => {
            if (availableCountry.country == c.name) {
              if (Number(c.id) === Number(country.id)) {
                selectable = true
                this.globe.autorotate.stop()
                this.globe.selectedCountry  = country
                country = c
                this.render()
              }
            }
          })
        })
        
        if (selectable) {
          this.countrySelected = country.name
          this.$inertia.replace(this.route('homepage', {
            'map' : country.name
          }), {
            preserveScroll: true
          }).then(() => {
            this.mapView = this.mapPages
            this.mapViewLoaded = true
          })

          //this.$refs['mappages'].style.left = event.clientX + 'px'
          //this.$refs['mappages'].style.top = event.clientY + 'px'
          this.$refs['mappages'].style.display = 'block'
        } else {
          this.closeMapPages()
        }
      } else {
        this.closeMapPages()
        //this.$refs['mappages'].style.display = 'none'
      }
    }, 

    getPosition(event) {
      this.$refs['mappages'].style.left = event.clientX + 'px'
      this.$refs['mappages'].style.top = event.clientY + 'px'
    },

    closeMapPages() {
      this.fill(this.globe.selectedCountry, '#09A2AC')
      this.globe.selectedCountry = null
      this.mapViewLoaded = false
      this.$refs['mappages'].style.display = 'none'
      this.mapView = []
      this.startRotation(this.globe.rotationDelay)
    },

    filterCall() {
      let ss = this.searchSize
      if (this.searchSize[0]  == 0 && this.searchSize[1] == this.size) {
        ss = null
      }

      this.$inertia.replace(this.route('homepage'), {
        data: {
          'caregiver': this.searchCaregiver,
          'regions': this.searchRegions, 
          'countries': this.searchCountries, 
          'years': this.filterSelectionYear,
          'organizations': this.searchOrg,
          'programs': this.searchPrograms,
          'equities': this.searchEquity,
          'size': ss,
          'search': this.searchText,
        },
        preserveScroll: true
      })
    },

    isMobile() {
      if(/Android|webOS|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        this.mobileFirst = true
        this.listView = true
      } else {
        this.mobileFirst = false
        this.listView = false
      }
    },
    
    // All Globe Functions
    render() {
      let c = this.globe.context
      c.clearRect(0, 0, this.globe.width, this.globe.height)
      
      // 
      this.globe.projection.clipAngle(180);
      //c.strokeStyle = "rgba(0, 0, 0, 0.25)", c.lineWidth = 1, c.beginPath(), this.globe.path(this.globe.water), c.stroke()
      //c.strokeStyle = "rgba(0, 0, 0, 0)", c.lineWidth = .5, c.beginPath(), this.globe.path(this.globe.graticule), c.stroke();
      this.fill(this.globe.land, '#064B4F')

      this.globe.projection.clipAngle(90)
      //.strokeStyle = "rgba(27, 129, 138, 0.5)", c.lineWidth = 5, c.beginPath(), this.globe.path(this.globe.water), c.stroke()
      c.strokeStyle = "rgba(0, 0, 0, .15)", c.lineWidth = 2, c.beginPath(), this.globe.path(this.globe.water), c.stroke()
      c.strokeStyle = "rgba(0, 0, 0, .15)", c.lineWidth = 2, c.beginPath(), this.globe.path(this.globe.graticule), c.stroke();
      this.fill(this.globe.land, '#06838b')

      this.globe.countries.features.forEach((country) => {
        c.strokeStyle = "rgba(0, 0, 0, 0.25)", c.lineWidth = 0.5, c.beginPath(), this.globe.path(country), c.stroke();
      })
      
      this.countries.forEach((availableCountry) => {
        this.globe.countryList.forEach((country) => {
          if (availableCountry.country == country.name) {
            country = this.globe.countries.features.find(function(c) {
              return Number(c.id) === Number(country.id)
            })

            this.fill(country, '#09A2AC')
            if (
              this.globe.currentCountry && 
              (this.globe.selectedCountry != this.globe.currentCountry) &&
              (this.globe.currentCountry == country)  
            ) {
              this.fill(country, '#4DC2CA')
            }

            
          }
        })
      })


      if (this.globe.selectedCountry) {
        this.fill(this.globe.selectedCountry, this.globe.selectedColorCountry)
      }

    },
    startRotation(delay) {
      this.globe.autorotate.restart(this.rotate, delay || 0)
    },
    stopRotation() {
      this.globe.autorotate.stop()
    },
    rotate(elapsed) {
      this.globe.now = d3.now()
      this.globe.diff = this.globe.now - this.globe.lastTime
      if (this.globe.diff < elapsed) {
        this.globe.rotation = this.globe.projection.rotate()
        this.globe.rotation[0] += this.globe.diff * this.globe.degPerMs
        this.globe.projection.rotate(this.globe.rotation)
        this.render()
      }
      this.globe.lastTime = this.globe.now
    },
    setAngles() {
      let rotation = this.globe.projection.rotate()
        rotation[0] = this.globe.angles.y
        rotation[1] = this.globe.angles.x
        rotation[2] = this.globe.angles.z
        this.globe.projection.rotate(rotation)
    },
    dragstarted() {
        this.globe.v0 = versor.cartesian(this.globe.projection.invert(d3.mouse(this.$refs['globe'])))
        this.globe.r0 = this.globe.projection.rotate()
        this.globe.q0 = versor(this.globe.r0)
        //
    },
    dragged() {
        let v1 = versor.cartesian(this.globe.projection.rotate(this.globe.r0).invert(d3.mouse(this.$refs['globe'])))
        let q1 = versor.multiply(this.globe.q0, versor.delta(this.globe.v0, v1))
        let r1 = versor.rotation(q1)
        this.globe.projection.rotate(r1)
        this.render()
    },
    dragended() {
      this.startRotation(this.globe.rotationDelay)
    },
    mousemove(d3mouse) {
      let xy = d3.mouse(d3mouse)
      let c = this.getCountry(this.$refs['globe'])
        // If no country e.g. water
        if (!c) {
          if (this.globe.currentCountry) {
            //leave(currentCountry)
            this.globe.hoverCountry = ''
            this.globe.currentCountry = undefined
            this.render()
          }
          return
        }

        let hoverCountry = this.$refs['hoverCountry']

        // If the country we've hovered over matches the current country, do nothing
        if (c === this.globe.currentCountry) {
          hoverCountry.style.left = xy[0] + 'px'
          hoverCountry.style.top =  xy[1] + 'px'
          return
        }


        this.countries.forEach((availableCountry) => {
          this.globe.countryList.forEach((country) => {
            if (availableCountry.country == country.name) {
              if (Number(c.id) === Number(country.id)) {
                this.globe.currentCountry = c
                this.globe.hoverCountry = country.name
                hoverCountry.style.left = xy[0] + 'px'
                hoverCountry.style.top = xy[1] + 'px'
              }
            }
          })
        })

        // Else, render
        this.render()
    },
    click() {
      var c = this.getCountry(this.$refs['globe'])
      this.stopRotation();
      this.countryClicked(c);
      
      // select the country feature to ensure we set something here.
      this.render()
    },
    getCountry(event) {
      let $this = this
      let pos = this.globe.projection.invert(d3.mouse(event))

      if (this.globe.countries.features) {
        return this.globe.countries.features.find(function(f) {
          return f.geometry.coordinates.find(function(c1) {
            return $this.polygonContains(c1, pos) || c1.find(function(c2) {
              return $this.polygonContains(c2, pos)
            })
          })
        })
      }
    },
    polygonContains(polygon, point) {
      var n = polygon.length
      var p = polygon[n - 1]
      var x = point[0], y = point[1]
      var x0 = p[0], y0 = p[1]
      var x1, y1
      var inside = false
      for (var i = 0; i < n; ++i) {
        p = polygon[i], x1 = p[0], y1 = p[1]
        if (((y1 > y) !== (y0 > y)) && (x < (x0 - x1) * (y - y1) / (y0 - y1) + x1)) inside = !inside
        x0 = x1, y0 = y1
      }
      return inside
    },
    scale() {
      this.globe.width = document.querySelector('.globe-container').offsetWidth //document.documentElement.clientWidth
      this.globe.height = document.querySelector('.globe-container').offsetHeight //document.documentElement.clientHeight
      this.globe.canvas.attr('width', this.globe.width).attr('height', this.globe.height)
      this.globe.projection
        .scale((this.globe.scaleFactor * Math.min(this.globe.width, this.globe.height)) / 2)
        .translate([this.globe.width / 2, this.globe.height / 2])
      
      this.render()
    },
    fill(obj, color) {
      this.globe.context.beginPath()
      this.globe.path(obj)
      this.globe.context.fillStyle = color
      this.globe.context.fill()
    },
    stroke(obj, color) {
      this.globe.context.beginPath()
      this.globe.path(obj)
      this.globe.context.strokeStyle = color
      this.globe.context.stroke()
    },
    loadData(cb) {
      d3.json('https://idela-data-storage-staging.s3.amazonaws.com/public/misc/world110m.json').then(function(world) {
        d3.tsv('https://idela-data-storage-staging.s3.amazonaws.com/public/misc/country-names.tsv').then(function(countries) {
          cb(world, countries)
        })
      })
    }
  },
  mounted() {
    this.isMobile()

    if (!this.mobileFirst) {
      this.globe.canvas = d3.select('#globe')
      this.globe.context = this.globe.canvas.node().getContext('2d')
      this.globe.path = d3.geoPath(this.globe.projection).context(this.globe.context)
      this.globe.degPerMs = this.globe.degPerSec / 1000
      this.lastTime = d3.now()
      this.setAngles()

      this.globe.canvas
        .call(d3.drag()
          .on('start', this.dragstarted)
          .on('drag', this.dragged)
          .on('end', this.dragended)
        )

      let $this = this
      this.globe.canvas.on('mousemove', function() { $this.mousemove(this) })
      this.globe.canvas.on('click', this.click)

      this.loadData((world, cList) => {
        this.globe.land = topojson.feature(world, world.objects.land)
        this.globe.countries = topojson.feature(world, world.objects.countries)
        this.globe.countryList = cList
        
        window.addEventListener('resize', this.scale)
        this.scale()
        this.globe.autorotate = d3.timer(this.rotate)
      })
    }

    // Google Analytics via GTM
    dataLayer.push({
        'event': 'pageView',
        'virtualUrl': this.$inertia.page.url
    });
  },
}
</script>
