<template>
<div v-observe-visibility="visibilityChanged" class="w-full max-w-5xl px-5 mx-auto">
  <div class="rounded-r-xl flex flex-wrap border-solid border-3 border-yellow-600 bg-white rounded-bl-xl my-4 overflow-hidden">
    <div class="w-full h-64 md:h-auto md:w-1/4 relative pointer-events-none overflow-hidden">
        <l-map
            ref="map"
            :zoom="zoom"
            :maxZoom="3"
            :center="center"
            @ready="mapReady"
            class="absolute top-0 bottom-0 z-0 w-full bg-white rounded-tr-xlinner md:rounded-bl-xlinner md:rounded-tr-none cursor-default pointer-events-none"> 
          <l-geo-json 
            ref="countries"
            :geojson="geojson"
            :options-style="styleFunction"
            :options="{
              zoomControl: false,
              onEachFeature: this.onEachFeature
            }" />
        </l-map>
    </div>
    <div class="w-full md:w-3/4 flex flex-col justify-end items-end relative border-t-3 border-t-solid border-yellow-600 px-7 py-6 md:px-12 md:py-8 md:border-l-solid md:border-l-3 md:border-t-0">
      <div class="grid grid-cols-1 md:grid-cols-2 w-full">
        <h1 class="col-span-1 font-oswald text-xl leading-tight md:pr-12 focus:outline-none"
          :contenteditable="editable"
          @blur="(e) => {this.block.title = e.target.innerHTML}" 
          v-html="block && block.title || 'Lorem ipsum dolor sit amet'" />
        <div class="mt-3 col-span-1 leading-tight focus:outline-none md:mt-0"
          :contenteditable="editable"
          @blur="(e) => {this.block.description = e.target.innerHTML}" 
          v-html="block && block.description || 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt.'" />
      </div>

      <div class="w-full grid grid-cols-2 md:grid-cols-3">
        <div v-for="(item, index) in block.items" :key="index" class="col-span-1 mt-6 flex flex-col md:flex-row">
          <div class="w-10 h-10 relative flex flex-shrink-0 justify-end items-start mr-4">
            
            <!-- Image Editor -->
            <template v-if="editable">
              <popper trigger="clickToToggle" :visible-arrow="false" :root-class="'imagepopper'" :options="popperOptions">
                <div class="popper w-64 max-w-3xl">
                  <image-input 
                    v-model="item.icon"
                    v-on:upload="$emit('uploadImage', $event)" 
                    v-on:remove="$emit('removeImage', $event)"
                    :directory="'page/hero/'"
                    :imgix="'w=32&h=32&fit=crop'"/>
                </div>
                <div slot="reference" class="absolute cursor-pointer w-10 h-10 text-gray-600">
                  <div 
                    :class="(!item.icon) ? 'bg-gray-200' : ''"
                    class="w-full h-full flex justify-center items-center">
                    <span v-if="!item.icon"><i class="far fa-image"></i></span>
                  </div>
                </div>
              </popper>
            </template>
            <!-- End Image Editor -->

            <img v-if="item.icon" class="pt-1" :src="imgix_domain+item.icon+'?w=28&fit=crop'">
          </div>
          
        
          <div class="relative">
            <p class="font-oswald text-sm focus:outline-none"
              :contenteditable="editable"
              @blur="(e) => {item.title = e.target.innerText}">
              {{ item.title }}
            </p>
            <p class="text-sm leading-tight focus:outline-none"
              :contenteditable="editable"
              @blur="(e) => {item.description = e.target.innerText}">
              {{ item.description }}
            </p>

            <template v-if="editable">
              <div @click="$emit('removeElement', item.id)"
                class="absolute cursor-pointer top-0 right-0 -mr-4 -mt-2 text-red-200 text-xs hover:text-red-500">
                <i class="fad fa-trash-alt"></i>
              </div>
            </template>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
</template>

<script>

import ImageInput from '@/Shared/ImageInput'
import Popper from 'vue-popperjs'
import L from 'leaflet'
import { LMap, LGeoJson, LLayerGroup } from 'vue2-leaflet'
import mapJson from '@/custom.geo.json'

export default {
  data() {
    return {
      zoom: 2,
      center: [48, -1.219482],
      geojson: mapJson,
      geoCountries: [],
      fillColor: "#0A838C",
      selectedLayers: [],
      popperOptions: {
        placement: 'left', 
        modifiers: { 
          offset: { 
            offset: '0,10px,0,0' 
          }
        }
      }
    }
  },
  props: {
    block: Object,
    editable: false,
  },
  components: {
    ImageInput,
    Popper,
    LMap,
    LGeoJson,
    LLayerGroup
  },
  computed: {
    styleFunction() {
      const fillColor = this.fillColor; // important! need touch fillColor in computed for re-calculate when change fillColor
      return () => {
        return {
          weight: 1,
          color: "#ECEFF1",
          opacity: 1,
          fillColor: fillColor,
          fillOpacity: 1
        };
      };
    },
  },
  watch: {
    'block.country'(newVal) {
      this.setSelectionBounds()
    }
  },
  methods: {
    mapReady() {
      this.$refs.map.mapObject.zoomControl.disable()
      this.$refs.map.mapObject.touchZoom.disable()
      this.$refs.map.mapObject.scrollWheelZoom.disable()
      this.$refs.map.mapObject.doubleClickZoom.disable()
      this.$refs.map.mapObject.dragging.disable()
    },
    setSelectionBounds() {
      this.$refs.countries.mapObject.eachLayer((layer) => { 
        layer.setStyle({
          weight: 1,
          color: "#ECEFF1",
          opacity: 1,
          fillColor: "#0A838C",
          fillOpacity: 1
        })

        let bounds = layer._bounds

        if (layer.feature.properties.name_long == this.block.country) {
          layer.setStyle({
              weight: 1,
              color: "#FF0000",
              opacity: 1,
              fillColor: "#ff0000",
              fillOpacity: 1
            })

            if ( this.block.country == 'Fiji') {
              let ne = L.latLng(-16.02088225674123, 179)
              let sw = L.latLng(-18.28799, -179+360);
              bounds = L.latLngBounds(ne, sw)
            }
            this.$refs.map.fitBounds(bounds)

        }
      })
    },
    visibilityChanged (isVisible, entry) {
      if (isVisible === true) {
        this.$refs.map.mapObject.invalidateSize()
        this.setSelectionBounds()
      }
    },
    onEachFeature(feature, layer) {
      this.geoCountries.push(layer)
      
      if (feature.properties.name_long == this.block.country) {
        layer.setStyle({
            weight: 1,
            color: "#FF0000",
            opacity: 1,
            fillColor: "#ff0000",
            fillOpacity: 1
          })

        let bounds = layer._bounds

        if ( this.block.country == 'Fiji') {
          let ne = L.latLng(-16.02088225674123, 179)
          let sw = L.latLng(-18.28799, -179+360);
          bounds = L.latLngBounds(ne, sw)
        }
        this.$refs.map.fitBounds(bounds)

        
      }
    },
  }
}
</script>