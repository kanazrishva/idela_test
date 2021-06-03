<template>
<div>
  <header class="fixed w-full top-0 z-40">
    <div class="hidden nav-secondary w-full justify-between  md:flex">
      <div class="logo flex justify-start text-xs items-center px-3">
        <a href="https://www.savethechildren.org/" target="_blank">
          <img class="h-5" :src="asset('images/logo-save-the-children.svg')" alt="Save The Children Logo">
        </a>
      </div>

      <div class="h-full flex">
        <a href="https://idela-network.org/start-using-idela/" target="_blank" class="btn-start h-full text-xs flex justify-center items-center px-3">
          <i class="icon-start fas fa-user-plus"></i> Start Using IDELA
        </a>
        <a href="https://idela-network.org/login/" target="_blank" class="btn-login text-white text-xs h-full flex justify-center items-center px-4">
          <i class="icon-login fas fa-fw fa-sign-in-alt"></i> Login
        </a>
      </div>
    </div>

    <div class="nav-main w-full flex justify-between">
      <div class="logo flex justify-center items-center px-3">
        <a href="/">
          <img class="h-8" :src="asset('images/logo-idela.svg')" alt="IDELA Network">
        </a>
      </div>
      <div class="hidden px-4 h-full justify-center items-center uppercase font-oswald md:flex">
        <a href="https://idela-network.org/about/" target="_blank" class="px-3">About IDELA</a>
        <a href="https://idela-network.org/" target="_blank" class="px-3">The IDELA Network</a>
      </div>

      <div @click="mobileNav = !mobileNav" class="flex px-4 h-full text-teal-500 justify-center items-center uppercase font-oswald md:hidden">
        <i class="fal fa-2x fa-bars"></i>
      </div>
    </div>
  </header>

  <nav v-show="mobileNav" class="fixed w-full h-screen top-0 z-50 bg-teal-500 bg-opacity-75">
    <div @click="mobileNav = !mobileNav" class="fixed top-0 right-0 mt-3 mr-4 text-teal-500">
      <i class="fal fa-2x fa-times"></i>
    </div>
    
    <div class="px-4 bg-teal-100 h-48 w-full flex flex-col justify-center items-center uppercase font-oswald">
      <a href="https://idela-network.org/about/" target="_blank" class="py-2">About IDELA</a>
      <a href="https://idela-network.org/" target="_blank" class="py-2">The IDELA Network</a>
    </div>
    <div class="bg-white grid grid-cols-2">
      <div class="col-span-1 h-20 flex justify-center items-center">
        <a href="https://www.savethechildren.org/" target="_blank">
          <img class="h-7" :src="asset('images/logo-save-the-children.svg')" alt="Save The Children Logo">
        </a>
      </div>
      <div class="col-span-1 flex justify-center items-center">
        <a href="https://idela-network.org/start-using-idela/" target="_blank" class="btn-start h-full text-xs text-gray-800 flex justify-center items-center px-3">
          <i class="icon-start fas fa-fw fa-user-plus"></i>&nbsp; Start Using IDELA
        </a>
      </div>
      <div class="col-span-2">
        <a href="https://idela-network.org/login/" target="_blank" class="btn-login text-white text-sm bg-red-500 py-4 h-full flex justify-center items-center px-4">
          <i class="icon-login fas fa-fw fa-sign-in-alt"></i>&nbsp; Login
        </a>
      </div>
    </div>
  </nav>
  

  
  <slot></slot>


  <footer class="w-full max-w-5xl px-5 mx-auto">

    <div class="flex-col footer-contact text-white rounded-r-xl rounded-bl-xl my-4 py-7 px-8 flex justify-between items-start md:items-center md:flex-row">
      <div class="font-oswald">Questions about IDELA or need further information?</div>
      <a href="https://idela-network.org/connect/contact-us/" target="_blank" class="font-oswald uppercase border-3 border-white rounded-r-lg rounded-bl-lg px-3 py-2 mt-4 md:mt-0">Contact Us</a>
    </div>

    <div class="flex-col footer-product bg-gray-700 text-white rounded-r-xl rounded-bl-xl my-4 py-7 px-8 flex justify-between  md:flex-row">
      <div class="logo flex justify-start items-center pb-5 md:pb-0">
        <div class="product text-xs leading-tight w-16 mr-2">IDELA is a product of:</div>
        <span class="save-the-children">
          <img class="h-8" :src="asset('images/logo-save-the-children-w.svg')" alt="Save The Children">
        </span>
      </div>

      <div class="flex-col uppercase font-oswald flex justify-end items-start md:items-center md:flex-row">
        <a href="https://savethechildren.org/" target="_blank" class="py-1 md:px-3">Save the Children</a>
        <a href="https://savethechildren.org/us/what-we-do/global-programs/education" target="_blank" class="py-1 md:px-3">Education</a>
        <a href="https://savethechildren.org/us/what-we-do/global-programs/education/early-childhood-development" target="_blank" class="py-1 md:pl-3">Early Childhood Development</a>
      </div>
    </div>


    <div class="flex-col footer border-solid border-3 border-teal-600 text-white bg-gray-900 rounded-r-xl rounded-bl-xl my-4 p-6 flex md:flex-row md:p-8 lg:px-32 lg:py-16">
      <div class="w-full md:w-3/4">
        <div class="logo mb-5">
          <img class="h-8" :src="asset('images/logo-idela.svg')" alt="IDELA Network">
        </div>
        <p class="w-full leading-tight max-w-xs">
          Save the Children is committed to evidence-based programming and growing the knowledge base about what is needed for young children to build strong early learning foundations.
        </p>
      </div>
      <div class="w-full pt-6 md:w-1/4 flex flex-col font-oswald justify-center">
        <a href="https://idela-network.org/about/" target="_blank" class="py-1">About The Data</a>
        <a href="https://idela-network.org/about/faq/" target="_blank" class="py-1">FAQ</a>
        <a href="https://idela-network.org/resources/" target="_blank" class="py-1">Resources</a>
        <a href="https://idela-network.org/" target="_blank" class="py-1">IDELA-Network.org</a>
      </div>
    </div>
  </footer>
</div>
</template>

<script>
export default {
  data() {
    return {
      mobileNav: false
    }
  }
}
</script>