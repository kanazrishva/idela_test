<template>
<div>
<!-- Graph Editing? -->
<graph-edit 
  v-show="graphEdit" 
  class="absolute bg-white top-0 right-0 b-0 w-full"  
  style="z-index: 100" 
  v-on:closeGraphEdit="closeGraphEdit"
  :datasets="datasets" 
  :graph="graph" 
  :equations="equations" />
<!-- End Graph Editing -->
<admin>

  <admin-header :title="form.title" :breadcrumbs="breadcrumbs" />
  <div>
    <!-- Hero Section Goes Here -->
    <hero class="mt-5" :block.sync="form.hero" :countries="countries" />
    <!-- Hero Section Ends Here -->

    <!-- Show the switch for Child/Caregiver Here -->
    <div class="w-full max-w-5xl mt-5 px-5 mx-auto grid grid-cols-2">
      <div v-show="domain" class="col-span-1 font-oswald text-4xl uppercase">Child</div>
      <div v-show="!domain" class="col-span-1 font-oswald text-4xl uppercase">Caregiver</div>
      <div class="col-span-1 uppercase font-oswald flex justify-end items-center">
        Switch Environments 
        <span class="mx-5 text-gray-400">|</span> 
        Child 

        <span 
          @click="domain = !domain" 
          class="bg-teal-100 mx-3 relative inline-block flex-shrink-0 h-6 w-11 border-3 border-transparent rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
          <!-- On: "translate-x-5", Off: "translate-x-0" -->
          <span aria-hidden="true" 
            :class="domain ? 'translate-x-0' : 'translate-x-5'"
            class="translate-x-0 inline-block h-5 w-5 rounded-sm bg-teal-500 shadow transform transition ease-in-out duration-200"></span>
        </span>

        Caregiver
      </div>
    </div>

    <!-- Blocked Section -->
    <div v-show="domain">
      <div class="max-w-7xl mx-auto px-4 mt-6 grid grid-cols-2">
        <div class="col-span-1 col-start-2 flex justify-end items-center">
          <div class="text-xs mr-4">Add block</div>
          <select-input v-model="addBlocks" class="w-2/3">
            <option value="SingleGraph">Single Graph Block</option>
            <option value="DualGraph">Dual Graph Block</option>
            <option value="TitleBlock">Title Block</option>
            <option value="Resource">Resource</option>
          </select-input>
          <button @click="addBlock('domain')" class="p-2 ml-2 h-11 w-11 text-center border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-teal-500 hover:bg-teal-500 focus:outline-none focus:border-teal-700 focus:shadow-outline-teal active:bg-teal-700 transition ease-in-out duration-150">
            <i class="fad fa-fw fa-plus"></i>
          </button>
        </div>
      </div>

      <draggable v-model="form.blocks" group="blocks" @start="drag=true" @end="drag=false">
      <admin-blocks class="mt-6 font-lato page-builder"
        v-for="(block, index) in form.blocks" 
        :page_id="page.id"
        :block.sync="block" 
        :graphs.sync="allGraphs"
        :graphoptions="graphoptions"
        :key="block.blockid"
        v-on:editGraph="editGraph"
        v-on:editGraphDual="editGraphDual"
        v-on:replaceGraphs="replaceGraphs"
        v-on:destroy="removeBlock" />
      </draggable>
    </div>

    <div v-show="!domain">
      <div class="max-w-7xl mx-auto px-4 mt-6 flex justify-end items-center">
        <div class="text-xs mr-4">Add block</div>
        <select-input v-model="addCaregiver" class="w-1/4">
          <option value="SingleGraph">Single Graph Block</option>
          <option value="DualGraph">Dual Graph Block</option>
          <option value="TitleBlock">Title Block</option>
          <option value="Resource">Resource</option>
        </select-input>
        <button @click="addBlock('caregiver')" class="p-2 ml-2 h-11 w-11 text-center border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-teal-500 hover:bg-teal-500 focus:outline-none focus:border-teal-700 focus:shadow-outline-teal active:bg-teal-700 transition ease-in-out duration-150">
          <i class="fad fa-fw fa-plus"></i>
        </button>
      </div>

      <draggable v-model="form.caregiver" group="caregiver" @start="drag=true" @end="drag=false">
      <admin-blocks class="mt-6 font-lato page-builder"
        v-for="(block, index) in form.caregiver" 
        :page_id="page.id"
        :block.sync="block" 
        :graphs.sync="graphs"
        :graphoptions="graphoptions"
        :key="block.blockid"
        v-on:editGraph="editGraph"
        v-on:editGraphDual="editGraphDual"
        v-on:replaceGraphs="replaceGraphs"
        v-on:destroy="removeBlock" />
      </draggable>
    </div>
    <!-- End Blocked Section -->

    <!-- Footer Section -->
    <div class="max-w-7xl mx-auto px-4 py-4 mt-12 border-t-3 grid grid-cols-2">
      <div class="col-span-1 flex flex-col justify-center items-start">
        <h3 class="font-oswald text-xl">Global Elements</h3>
        <p class="text-xs text-gray-600">All blocks in this area appear below the Domain/Caregiver section</p>
      </div>
      <div class="col-span-1 flex justify-end items-center">
        <div class="text-xs mr-4">Add block</div>
        <select-input v-model="addFooter" class="w-2/3">
          <option value="SingleGraph">Single Graph Block</option>
          <option value="DualGraph">Dual Graph Block</option>
          <option value="TitleBlock">Title Block</option>
          <option value="Resource">Resource</option>
        </select-input>
        <button @click="addBlock('footer')" class="p-2 ml-2 h-11 w-11 text-center border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-teal-500 hover:bg-teal-500 focus:outline-none focus:border-teal-700 focus:shadow-outline-teal active:bg-teal-700 transition ease-in-out duration-150">
          <i class="fad fa-fw fa-plus"></i>
        </button>
      </div>
    </div>

    <draggable v-model="form.footer" group="footer" @start="drag=true" @end="drag=false">
    <admin-blocks class="mt-6 font-lato page-builder"
      v-for="(block, index) in form.footer" 
      :page_id="page.id"
      :block.sync="block" 
      :graphs.sync="allGraphs"
      :graphoptions="graphoptions"
      :resources="resources"
      :key="block.blockid"
      v-on:editGraph="editGraph"
      v-on:editGraphDual="editGraphDual"
      v-on:replaceGraphs="replaceGraphs"
      v-on:destroy="removeBlock" />
    </draggable>
    
    <!-- End Footer Section -->
  </div>

  <template v-slot:rightmenu>
    <div class="hidden md:flex md:flex-shrink-0">
      <div class="flex flex-col bg-gray-50" style="width: 24em">
        <div class="h-0 flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
          <div class="px-4">

            <div class="flex">
              <button @click="submit"
                :class="(page.publish) ? 'w-full' : 'w-2/3'"
                class="p-2 r-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-teal-500 hover:bg-teal-500 focus:outline-none focus:border-teal-700 focus:shadow-outline-teal active:bg-teal-700 transition ease-in-out duration-150">
                <i class="fad fa-save mr-2"></i> Save Page
              </button>

              <a
                v-show="!page.publish"
                :href="route('pages.show', page.id)"
                target="_blank"
                class="w-1/3 ml-2 p-2 border border-transparent text-sm leading-4 font-medium rounded-md text-black bg-teal-100 hover:bg-teal-200 focus:outline-none focus:border-teal-700 focus:shadow-outline-teal active:bg-teal-700 transition ease-in-out duration-150">
                <i class="fad fa-external-link mr-2"></i> Preview
              </a>
            </div>

            <div class="flex my-4 text-xs justify-end items-center">
              publish page
              <span 
                @click="publish" 
                class="bg-teal-100 ml-3 relative inline-block flex-shrink-0 h-6 w-11 border-3 border-transparent rounded-sm cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
                <!-- On: "translate-x-5", Off: "translate-x-0" -->
                <span aria-hidden="true" 
                  :class="page.publish ? 'translate-x-5 bg-teal-500' : 'translate-x-0 bg-gray-400'"
                  class="translate-x-0 inline-block h-5 w-5 rounded-sm shadow transform transition ease-in-out duration-200"></span>
              </span>
            </div>

            <div class="-mx-4 mt-8 border-t-3 border-gray-100">
              <div class="border-t-3 border-b-3 border-gray-100 p-2 px-4 flex items-center justify-between">
                <h4 class="text-sm font-bold text-gray-600">Page Information</h4>
                <button 
                  @click="sidebar.pageInfoAcc = !sidebar.pageInfoAcc"
                  :class="(sidebar.pageInfoAcc) ? 'rotate-45' : 'rotate-0'" class="transform focus:outline-none text-teal-500 duration-200 transition-all cursor-pointer">
                  <i  class="fad fa fa-plus"></i>
                </button>
              </div>
              <div class="bg-white p-4" v-show="sidebar.pageInfoAcc">
              <p class="text-xs text-gray-500 mb-4 mt-4 leading-tight">The information here is for internal use only and is not displayed on the 
                public facing site.
              </p>

              <text-input v-model="form.slug" :errors="error('slug')" class="pb-4" label="Page Slug" />
              <text-input v-model="form.title" :errors="error('title')" class="pb-4" label="Page Title" />
              <text-area v-model="form.description" class="pb-4" label="Page Description" />

              <label>Page Dataset(s)</label>
              <multiselect 
                v-model="form.datasets"
                :options="datasets"
                :hideSelected="false"
                :clear-on-select="false"
                :multiple="true"
                :allowEmpty="true"
                :showLabels="false"
                placeholder="Select at least one dataset"
                label="dataset_name"
                class="flex items-start justify-start w-full"
                :class="(error(`datasets`).length > 0) ? 'errors' : ''"
                track-by="id">
                <template slot="singleLabel" slot-scope="{ option }">
                  {{ option.dataset_name }}
                </template>
                <template slot="option" slot-scope="{option}">
                  <div class="text-black">{{ option.dataset_name }}</div>
                </template>
              </multiselect>
              <div class="mt-1 text-xs text-red-600" v-if="error(`datasets`).length">Please select a dataset.</div>

              </div>
            </div>

            <div class="-mx-4">
              <div class="border-t-3 border-b-3 border-gray-100 p-2 px-4 flex items-center justify-between">
                <h4 class="text-sm font-bold text-gray-600">Page Meta</h4>
                <button 
                  @click="sidebar.pageMetaAcc = !sidebar.pageMetaAcc"
                  :class="(sidebar.pageMetaAcc) ? 'rotate-45' : 'rotate-0'" class="transform focus:outline-none text-teal-500 duration-200 transition-all cursor-pointer">
                  <i  class="fad fa fa-plus"></i>
                </button>
              </div>
              <div class="bg-white p-4" v-show="sidebar.pageMetaAcc">
              <h4 class="text-sm font-bold mt-6">General Meta</h4>
              <p class="text-xs text-gray-500 mb-4 leading-tight">The title and description is used in search to display the page information.
              </p>

              <text-input v-model="form.meta.title" :errors="error('slug')" class="pb-4" label="Page Title" />
              <text-area v-model="form.meta.description" class="pb-4" label="Meta Description" />

              <h4 class="text-sm font-bold mt-6">Open Graph Meta</h4>
              <p class="text-xs text-gray-500 mb-4 leading-tight">Facebook & other social sites typically use open graph tags as a way to properly show
                information about your site. The ideal size for the social share image is 1200 x 600.
              </p>
              <text-input v-model="form.meta.ogtitle" :errors="error('slug')" class="pb-4" label="OG Title" />
              <text-area v-model="form.meta.ogdescription" class="pb-4" label="OG Description" />
              <image-input 
                v-model="form.meta.ogimage" 
                :directory="'meta/'"
                :imgix="'w=1200&h=630&fit=crop'"
                :errors="error('slug')" 
                class="pb-4" 
                label="OG Image" />


              <h4 class="text-sm font-bold mt-6">Twitter Specific Meta</h4>
              <p class="text-xs text-gray-500 mb-4 leading-tight">Twitter uses their own meta tags for their social share. By default we encourage the summary_card_large 
                setting and it will be used if an image is present. The ideal size for the social share image is 1200 x 630.</p>
              <text-input v-model="form.meta.twittertitle" :errors="error('slug')" class="pb-4" label="Twitter Title" />
              <text-area v-model="form.meta.twitterdescription" class="pb-4" label="Twitter Description" />
              <image-input 
                v-model="form.meta.twitterimage" 
                :directory="'meta/'"
                :imgix="'w=1200&h=628&fit=crop'"
                :errors="error('slug')" 
                class="pb-4" 
                label="Twitter Image" />

              </div>
            </div>


          </div>
        </div>
      </div>
    </div>
  </template>

</admin>
</div>
</template>

<script>

import Admin from '@/Layouts/Admin'
import AdminBlocks from '@/Shared/AdminBlocks'
import GraphEdit from '@/Pages/Admin/Graphs/GraphEdit'
import Hero from '@/AdminBlocks/Hero'
import Draggable from 'vuedraggable'
import mixins from '@/Mixins/mixinAdmin.js'

export default {
  mixins: [mixins],
  data() {
    return {
      breadcrumbs: [
        {
          title: 'Pages',
          link: 'pages'
        },
      ],

      // Graph specific data editing
      allGraphs: this.graphs,
      graphEdit: false,
      graph: null, //Selected Graph for Editing
      graphBlock: null,

      addBlocks: null,
      addCaregiver: null,
      addFooter: null,
      dragging: false,
      domain: true,
      sidebar: {
        pageInfoAcc: true,
        pageMetaAcc: false
      },
      form: {
        title: this.page.title,
        slug: this.page.slug,
        description: this.page.description,
        datasets: this.selected_datasets,
        blocks: this.page.blocks,
        hero: this.page.hero,
        meta: this.page.meta,
        caregiver: this.page.caregiver,
        footer: this.page.footer
      },
    }
  },
  props: {
    page: Object,
    selected_datasets: [Object, Array],
    countries: [Object, Array],
    graphs: [Object, Array],
    graphoptions: [Object, Array],
    resources: [Object, Array],
    // Graphs
    datasets: Array,
    codebook: Array,
    equations: Array,
  },
  components: {
    Admin,
    Hero,
    Draggable,
    AdminBlocks,
    GraphEdit
  },
  methods: {
    addBlock(type) {

      let blockid = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15)
      let obj, blockType

      //
      if (type == 'domain') {
        blockType = this.addBlocks;
      } else if (type == 'caregiver') {
        blockType = this.addCaregiver;
      } else {
        blockType = this.addFooter;
      }

      // make sure things are setup for pushing to blocks
      if (this.form.blocks == null) {
        this.form.blocks = []
      }

      if (this.form.caregiver == null) {
        this.form.caregiver = []
      }

      if (this.form.footer == null) {
        this.form.footer = []
      }

      switch (blockType) {
        case "TitleBlock":
          obj = {
            blockid: blockid,
            component: blockType,
            expanded: true,
            title: 'Lorem Ipsum Dolor',
            alignment: 'text-center',
            background: 'bg-white'
          }
        break;
        case "DualGraph":
          obj= {
            blockid: blockid,
            component: blockType,
            expanded: true,
            filter:false,
            tooltip: true,
            left: {
              title: 'Lorem Ipsum Dolor Sit Amet',
              description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam tempus libero ac venenatis volutpat.',
              tooltip: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam tempus libero ac venenatis volutpat.',
              graph: null
            },
            right: {
              title: 'Lorem Ipsum Dolor Sit Amet',
              description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam tempus libero ac venenatis volutpat.',
              tooltip: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam tempus libero ac venenatis volutpat.',
              graph: null
            }
          }
        break;
        case "MoreInfo":
          obj = {
            blockid: blockid,
            component: blockType,
            expanded: true,
            title: 'More Info',
            left: {
              icon: 'book.svg',
              title: 'Have Questions?',
              description: 'Quid ex eo delectu rerum, quem ad minima veniam, quis nostrum exercitationem ullam. Et quidem exercitus quid ex eo ortum, tam inportuno tamque crudeli; sin, ut.',
              button: {
                link: 'Our FAQs',
                url:'#',
                target: '_blank',
              }
            },
            right: {
              icon: 'email.svg',
              title: 'Contact Us',
              description: 'Quid ex eo delectu rerum, quem ad minima veniam, quis nostrum exercitationem ullam. Et quidem exercitus quid ex eo ortum, tam inportuno tamque crudeli; sin, ut.',
              button: {
                link: 'Reach Out',
                url:'#',
                target: '_blank',
              }
            },
          }
        break;
        case "Resource":
          obj = {
            blockid: blockid,
            component: blockType,
            expanded: true,
            resource: {},
          }
        break;
        case "SingleGraph":
          obj = {
            blockid: blockid,
            component: blockType,
            expanded: true,
            filter:false,
            tooltip: true,
            accordion: false,
            expand: false,
            withabove: false,
            endcap: true,
            filter_compareby: true,
            compareby: true,
            filter: true,
            filter_gender: true,
            filter_age: true,
            filter_location: true,
            title: 'Lorem Ipsum Dolor Sit Amet',
            description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam tempus libero ac venenatis volutpat.',
            tooltip: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam tempus libero ac venenatis volutpat.',
            graph: null
          }
        break;
      }

      if (type == 'domain') {
        this.form.blocks.push(obj);
      } else if (type == 'caregiver') {
        this.form.caregiver.push(obj);
      } else {
        this.form.footer.push(obj);
      }
      
    },
    removeBlock(blockid) {
      this.removeBlocks(blockid)
      this.removeCaregiverBlocks(blockid)
      this.removeFooterBlocks(blockid)
    },
    removeBlocks(blockid) {
      this.form.blocks = this.form.blocks.filter(function( obj ) {
          return obj.blockid !== blockid;
      });
    },
    removeCaregiverBlocks(blockid) {
      this.form.caregiver = this.form.caregiver.filter(function( obj ) {
          return obj.blockid !== blockid;
      });
    },
    removeFooterBlocks(blockid) {
      this.form.footer = this.form.footer.filter(function( obj ) {
          return obj.blockid !== blockid;
      });
    },
    editGraph(block) {
      console.log('graph is now editing')
      let graph = block.graph
      this.graphBlock = block
      
      // Get the graph to launch the editor
      let g = this.allGraphs.filter(function( obj ) {
          return obj.id === graph
      });

      this.graph = g[0]
      this.graphEdit = true
    },

    editGraphDual(item) {
      console.log('graph is now editing')

      let graph

      if (item.side === 'left') {
        graph = item.block.left.graph
      } else {
        graph = item.block.right.graph
      }
      
      this.graphBlock = item.block
      
      // Get the graph to launch the editor
      let g = this.allGraphs.filter(function( obj ) {
          return obj.id === graph
      });

      this.graph = g[0]
      this.graphEdit = true
    },

    editGraphDualCaregiver(item) {
      console.log('graph is now editing')

      let graph

      if (item.side === 'left') {
        graph = item.block.left.graph
      } else {
        graph = item.block.right.graph
      }
      
      this.graphBlock = item.block
      
      // Get the graph to launch the editor
      let g = this.allGraphs.filter(function( obj ) {
          return obj.id === graph
      });

      this.graph = g[0]
      this.graphEdit = true
    },

    replaceGraphs(graphs) {
      this.allGraphs = graphs
    },
    closeGraphEdit() {
      this.graphEdit = false
      this.graph = null

      // Get the proper block and update the
      this.form.blocks.forEach(block => {
        if (block.id == this.graphBlock.id) {
          console.log('found the right block')
          block.blockid = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15)
        }
      })

      // Get the proper block and update the
      this.form.caregiver.forEach(block => {
        if (block.id == this.graphBlock.id) {
          console.log('found the right block')
          block.blockid = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15)
        }
      })
    },
    publish() {
      let publish = !this.page.publish
      this.$inertia.put(this.route('pages.publish', this.page), {publish: publish})
    },
    submit() {
      this.$inertia.put(this.route('pages.update', this.page), this.form)
        .then(() => {
          // Reset the uploads so they don't get processed again
          this.form.uploads = []
        })
    },
  }
}
</script>